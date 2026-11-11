<?php
// C:\servidor-docker\www\app\Controllers\InsumoController.php
declare(strict_types=1);

class InsumoController {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        AuthController::checkAuth();
        $this->pdo = $pdo;
    }

    public function index(): void {
        $error   = '';
        $success = '';
        $isAdmin = (($_SESSION['user_role'] ?? '') === 'admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $op = $_POST['operacion'] ?? '';

            try {
                if ($op === 'nuevo_insumo') {
                    if (!$isAdmin) {
                        throw new Exception("Seguridad RBAC (RN-06): Solo el Administrador puede registrar nuevos insumos y costos.");
                    }
                    $nombre = trim($_POST['nombre_insumo'] ?? '');
                    $tipo   = $_POST['tipo_insumo'] ?? 'otro';
                    $stock  = (float) ($_POST['stock_actual'] ?? 0);
                    $minimo = (float) ($_POST['stock_minimo'] ?? 5);
                    $unidad = trim($_POST['unidad_medida'] ?? 'Unidad');
                    $retiro = (int) ($_POST['dias_retiro'] ?? 0);
                    $costo  = (float) ($_POST['costo_unitario'] ?? 0);

                    if ($nombre === '' || $stock < 0 || $costo < 0) {
                        throw new Exception("Verifique los datos ingresados para el insumo.");
                    }

                    $stmt = $this->pdo->prepare("
                        INSERT INTO insumos (nombre_insumo, tipo_insumo, stock_actual, stock_minimo, unidad_medida, dias_retiro, costo_unitario)
                        VALUES (:nombre, :tipo, :stock, :minimo, :unidad, :retiro, :costo)
                    ");
                    $stmt->execute([
                        'nombre' => $nombre,
                        'tipo'   => $tipo,
                        'stock'  => $stock,
                        'minimo' => $minimo,
                        'unidad' => $unidad,
                        'retiro' => $retiro,
                        'costo'  => $costo
                    ]);
                    $success = "Nuevo insumo '{$nombre}' incorporado al catálogo del almacén (RF-06).";

                } elseif ($op === 'movimiento_stock') {
                    $insumoId = (int) ($_POST['insumo_id'] ?? 0);
                    $tipoMov  = $_POST['tipo_movimiento'] ?? 'entrada'; // 'entrada' o 'salida'
                    $cantidad = (float) ($_POST['cantidad'] ?? 0);

                    if ($cantidad <= 0) {
                        throw new Exception("La cantidad del movimiento debe ser mayor a cero.");
                    }

                    $this->pdo->beginTransaction();
                    $stmtCheck = $this->pdo->prepare("SELECT * FROM insumos WHERE id = :id FOR UPDATE");
                    $stmtCheck->execute(['id' => $insumoId]);
                    $insumo = $stmtCheck->fetch();

                    if (!$insumo) {
                        throw new Exception("Insumo no encontrado.");
                    }

                    if ($tipoMov === 'salida') {
                        if ((float)$insumo['stock_actual'] < $cantidad) {
                            throw new Exception("Regla de Negocio (RN-03): Stock insuficiente de '{$insumo['nombre_insumo']}'. Disponible: {$insumo['stock_actual']} {$insumo['unidad_medida']}.");
                        }
                        $stmtUpd = $this->pdo->prepare("UPDATE insumos SET stock_actual = stock_actual - :cant WHERE id = :id");
                        $stmtUpd->execute(['cant' => $cantidad, 'id' => $insumoId]);
                        $success = "Consumo/Salida de {$cantidad} {$insumo['unidad_medida']} de '{$insumo['nombre_insumo']}' descontado atómicamente.";
                    } else {
                        $stmtUpd = $this->pdo->prepare("UPDATE insumos SET stock_actual = stock_actual + :cant WHERE id = :id");
                        $stmtUpd->execute(['cant' => $cantidad, 'id' => $insumoId]);
                        $success = "Ingreso/Compra de +{$cantidad} {$insumo['unidad_medida']} sumado al stock de '{$insumo['nombre_insumo']}'.";
                    }
                    $this->pdo->commit();
                }
            } catch (Exception $e) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                $error = $e->getMessage();
            }
        }

        $insumos = $this->pdo->query("SELECT * FROM insumos ORDER BY (stock_actual <= stock_minimo) DESC, tipo_insumo ASC, nombre_insumo ASC")->fetchAll();
        $valorTotalInventario = (float) $this->pdo->query("SELECT COALESCE(SUM(stock_actual * costo_unitario), 0) FROM insumos")->fetchColumn();
        $gastoSanitarioAcum   = (float) $this->pdo->query("SELECT COALESCE(SUM(costo_total), 0) FROM sanidad")->fetchColumn();

        $pageTitle = "Almacén de Insumos y Costos — FMIS";
        require_once dirname(__DIR__, 2) . '/views/insumos/index.php';
    }
}