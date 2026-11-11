<?php
// C:\servidor-docker\www\app\Controllers\ExtensibleController.php
declare(strict_types=1);

class ExtensibleController {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        AuthController::checkAuth();
        $this->pdo = $pdo;
    }

    public function porcinos(): void {
        $msg = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $stmt = $this->pdo->prepare("
                INSERT INTO lotes_porcinos (codigo_piara, categoria_porcina, cantidad_cabezas, galpon_corral, sistema_alimentacion)
                VALUES (:codigo, :cat, :cant, :galpon, :alim)
            ");
            $stmt->execute([
                'codigo' => strtoupper(trim($_POST['codigo_piara'] ?? '')),
                'cat'    => $_POST['categoria_porcina'] ?? 'engorde',
                'cant'   => (int) ($_POST['cantidad_cabezas'] ?? 1),
                'galpon' => trim($_POST['galpon_corral'] ?? 'Corral 1'),
                'alim'   => trim($_POST['sistema_alimentacion'] ?? 'Balanceado')
            ]);
            $msg = "Piara / Lote porcino registrado en la estructura extensible (RF-11).";
        }

        $lotes = $this->pdo->query("SELECT * FROM lotes_porcinos ORDER BY id DESC")->fetchAll();
        $pageTitle = "Módulo Porcino (Base Conceptual Extensible) — FMIS";
        require_once dirname(__DIR__, 2) . '/views/extensibles/porcinos.php';
    }

    public function avicolas(): void {
        $msg = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $stmt = $this->pdo->prepare("
                INSERT INTO lotes_avicolas (codigo_lote, linea_proposito, cantidad_aves, galpon, produccion_diaria_huevos)
                VALUES (:codigo, :linea, :cant, :galpon, :huevos)
            ");
            $stmt->execute([
                'codigo' => strtoupper(trim($_POST['codigo_lote'] ?? '')),
                'linea'  => $_POST['linea_proposito'] ?? 'postura_huevos',
                'cant'   => (int) ($_POST['cantidad_aves'] ?? 10),
                'galpon' => trim($_POST['galpon'] ?? 'Galpón 1'),
                'huevos' => (int) ($_POST['produccion_diaria_huevos'] ?? 0)
            ]);
            $msg = "Lote avícola registrado en la estructura extensible (RF-12).";
        }

        $lotes = $this->pdo->query("SELECT * FROM lotes_avicolas ORDER BY id DESC")->fetchAll();
        $pageTitle = "Módulo Avícola (Base Conceptual Extensible) — FMIS";
        require_once dirname(__DIR__, 2) . '/views/extensibles/avicolas.php';
    }
}