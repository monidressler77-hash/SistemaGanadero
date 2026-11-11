<?php
// C:\servidor-docker\www\app\Controllers\ReporteController.php
declare(strict_types=1);

require_once dirname(__DIR__) . '/Models/Bovino.php';

class ReporteController {
    private PDO $pdo;
    private Bovino $bovinoModel;

    public function __construct(PDO $pdo) {
        AuthController::checkAuth();
        $this->pdo         = $pdo;
        $this->bovinoModel = new Bovino($pdo);
    }

    public function index(): void {
        $bovinoId = (int) ($_GET['bovino_id'] ?? 0);
        $bovinos  = $this->bovinoModel->getAll();
        $insumos  = $this->pdo->query("SELECT * FROM insumos ORDER BY nombre_insumo ASC")->fetchAll();

        $fichaIndividual = null;
        $pesajesFicha    = [];
        $sanidadFicha    = [];

        if ($bovinoId > 0) {
            $fichaIndividual = $this->bovinoModel->findById($bovinoId);
            if ($fichaIndividual) {
                $pesajesFicha = $this->bovinoModel->getPesajes($bovinoId);
                $sanidadFicha = $this->bovinoModel->getSanidad($bovinoId);
            }
        }

        $pageTitle = "Reportes Zootécnicos e Informes PDF — FMIS";
        require_once dirname(__DIR__, 2) . '/views/reportes/index.php';
    }
}