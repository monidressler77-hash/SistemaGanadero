<?php
// app/Controllers/DashboardController.php
declare(strict_types=1);

class DashboardController {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function index(): void {
        AuthController::checkAuth();
        $pageTitle = "Dashboard Ejecutivo — FMIS Colonias Unidas";

        // 1. KPIs principales (RF-09 / RN-02)
        $totalBovinos  = (int) $this->pdo->query("SELECT COUNT(*) FROM bovinos WHERE estado_general = 'activo'")->fetchColumn();
        $enCarencia    = (int) $this->pdo->query("SELECT COUNT(*) FROM bovinos WHERE estado_sanitario = 'en_carencia' AND estado_general = 'activo'")->fetchColumn();
        $alertasStock  = (int) $this->pdo->query("SELECT COUNT(*) FROM insumos WHERE stock_actual <= stock_minimo")->fetchColumn();
        $valorDeposito = (float) $this->pdo->query("SELECT COALESCE(SUM(stock_actual * costo_unitario), 0) FROM insumos")->fetchColumn();

        // 2. Distribución por categoría zootécnica
        $porCategoria = $this->pdo->query("
            SELECT categoria, COUNT(*) AS total 
            FROM bovinos 
            WHERE estado_general = 'activo' 
            GROUP BY categoria
        ")->fetchAll();

        // 3. Animales con bloqueo sanitario / carencia activa (RN-03)
        $animalesCarencia = $this->pdo->query("
            SELECT id, rp, nombre, categoria, fecha_liberacion_carencia 
            FROM bovinos 
            WHERE estado_general = 'activo' AND estado_sanitario = 'en_carencia'
            ORDER BY fecha_liberacion_carencia ASC
        ")->fetchAll();

        // 4. Insumos en stock crítico (RF-08 / RN-08)
        $insumosCriticos = $this->pdo->query("
            SELECT nombre_insumo, tipo_insumo, stock_actual, stock_minimo, unidad_medida 
            FROM insumos 
            WHERE stock_actual <= stock_minimo
            ORDER BY stock_actual ASC
        ")->fetchAll();

        // 5. Últimos pesajes y GMD calculada (RF-03)
        $ultimosPesajes = $this->pdo->query("
            SELECT p.fecha_pesaje, p.peso_kg, p.dias_transcurridos, p.gmd_calculada, b.id AS bovino_id, b.rp, b.nombre, b.sistema_alimentacion
            FROM pesajes p
            INNER JOIN bovinos b ON p.bovino_id = b.id
            ORDER BY p.fecha_pesaje DESC, p.id DESC
            LIMIT 5
        ")->fetchAll();

        require_once dirname(__DIR__, 2) . '/views/dashboard.php';
    }
}