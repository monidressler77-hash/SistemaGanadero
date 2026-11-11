<?php
// app/Controllers/BovinoController.php
declare(strict_types=1);

require_once dirname(__DIR__) . '/Models/Bovino.php';

class BovinoController {
    private PDO $pdo;
    private Bovino $model;

    public function __construct(PDO $pdo) {
        AuthController::checkAuth();
        $this->pdo   = $pdo;
        $this->model = new Bovino($pdo);
    }

    public function index(): void {
        $filters = [
            'q'                    => trim($_GET['q'] ?? ''),
            'categoria'            => $_GET['categoria'] ?? '',
            'proposito'            => $_GET['proposito'] ?? '',
            'sistema_alimentacion' => $_GET['sistema_alimentacion'] ?? '',
            'estado_general'       => $_GET['estado_general'] ?? ''
        ];
        $bovinos   = $this->model->getAll($filters);
        $pageTitle = "Catálogo del Hato Bovino — FMIS";
        require_once dirname(__DIR__, 2) . '/views/bovinos/index.php';
    }

    public function create(): void {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rp        = strtoupper(trim($_POST['rp'] ?? ''));
            $categoria = $_POST['categoria'] ?? '';
            $raza      = trim($_POST['raza'] ?? '');
            $sexo      = $_POST['sexo'] ?? '';
            $fechaNac  = $_POST['fecha_nacimiento'] ?? '';

            if ($rp === '' || $categoria === '' || $raza === '' || $sexo === '' || $fechaNac === '') {
                $error = "Complete todos los campos obligatorios marcados con (*).";
            } elseif ($fechaNac > date('Y-m-d')) {
                $error = "Regla de Negocio (RN-05): La fecha de nacimiento no puede ser futura.";
            } elseif ($this->model->findByRP($rp)) {
                $error = "Regla de Negocio (RN-01): El código de RP/Carimba '{$rp}' ya pertenece a otro animal registrado.";
            } else {
                try {
                    $nuevoId = $this->model->create($_POST);
                    header("Location: index.php?action=bovino_show&id={$nuevoId}&msg=creado");
                    exit;
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
            }
        }

        $pageTitle = "Registrar Alta de Bovino — FMIS";
        require_once dirname(__DIR__, 2) . '/views/bovinos/create.php';
    }

    public function show(): void {
        $id     = (int) ($_GET['id'] ?? 0);
        $bovino = $this->model->findById($id);
        if (!$bovino) {
            header('Location: index.php?action=bovinos');
            exit;
        }

        $error   = '';
        $success = '';

        if (isset($_GET['msg']) && $_GET['msg'] === 'creado') {
            $success = "Animal registrado correctamente con estado Activo (CU-02).";
        }

        // Procesar formularios de la ficha (Pesaje, Sanidad, Reproducción o Baja)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formType = $_POST['form_type'] ?? '';
            try {
                if ($formType === 'pesaje') {
                    $res = $this->model->registrarPesaje(
                        $id,
                        $_POST['fecha_pesaje'] ?? date('Y-m-d'),
                        (float) ($_POST['peso_kg'] ?? 0),
                        trim($_POST['observaciones'] ?? '')
                    );
                    $gmdTxt = $res['gmd'] !== null ? " | GMD Calculada: {$res['gmd']} kg/día ({$res['dias']} días)" : " (Peso inicial de referencia)";
                    $success = "Pesaje registrado exitosamente{$gmdTxt}.";
                } elseif ($formType === 'sanidad') {
                    $this->model->registrarSanidad(
                        $id,
                        (int) ($_POST['insumo_id'] ?? 0),
                        $_POST['tipo_evento'] ?? 'vacunacion',
                        $_POST['fecha_aplicacion'] ?? date('Y-m-d'),
                        (float) ($_POST['dosis_aplicada'] ?? 0),
                        trim($_POST['diagnostico_observacion'] ?? '')
                    );
                    $success = "Tratamiento sanitario registrado y stock descontado atómicamente (CU-04).";
                } elseif ($formType === 'reproduccion') {
                    $fep = $this->model->registrarReproduccion(
                        $id,
                        $_POST['tipo_evento'] ?? 'celo',
                        $_POST['fecha_evento'] ?? date('Y-m-d'),
                        trim($_POST['toro_o_semen'] ?? ''),
                        trim($_POST['resultado_observacion'] ?? '')
                    );
                    $fepTxt = $fep ? " | Fecha Estimada de Parto (FEP): " . date('d/m/Y', strtotime($fep)) : "";
                    $success = "Evento reproductivo asentado correctamente{$fepTxt}.";
                } elseif ($formType === 'baja') {
                    $this->model->registrarBaja(
                        $id,
                        $_POST['fecha_baja'] ?? date('Y-m-d'),
                        $_POST['tipo_baja'] ?? 'venta',
                        trim($_POST['detalle_motivo'] ?? 'Baja operativa'),
                        (float) ($_POST['precio_venta_gs'] ?? 0)
                    );
                    $success = "Baja del animal registrada. El estado general pasó a Inactivo (RN-05).";
                }
                // Recargar datos actualizados del animal
                $bovino = $this->model->findById($id);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        $pesajes      = $this->model->getPesajes($id);
        $sanidad      = $this->model->getSanidad($id);
        $reproduccion = $this->model->getReproduccion($id);
        $insumosSan   = $this->pdo->query("SELECT * FROM insumos WHERE tipo_insumo IN ('vacuna','medicamento','antiparasitario') ORDER BY nombre_insumo ASC")->fetchAll();

        $pageTitle = "Ficha Individual {$bovino['rp']} — FMIS";
        require_once dirname(__DIR__, 2) . '/views/bovinos/show.php';
    }
}