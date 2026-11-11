<?php
// config/database.php
// Capa de Persistencia PDO con soporte ACID para MySQL 8.0 (InnoDB)
declare(strict_types=1);

$host = 'db';              // Contenedor MySQL en Docker
$db   = 'mi_clase_db';     // Base de datos del proyecto
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("<div style='font-family:sans-serif;padding:20px;background:#f8d7da;color:#842029;border-radius:8px;'>
            <h4>Error Crítico de Conexión a Base de Datos</h4>
            <p>Verifique que el contenedor MySQL (db) esté activo y que la base <strong>mi_clase_db</strong> exista en phpMyAdmin.</p>
            <small>Detalle técnico: " . htmlspecialchars($e->getMessage()) . "</small>
         </div>");
}