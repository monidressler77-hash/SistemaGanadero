<?php
// C:\servidor-docker\www\instalar_bd.php
// Instalador automático del esquema relacional (3FN) y generador de hashes Bcrypt en vivo
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';

try {
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $tablas = ['lotes_avicolas', 'lotes_porcinos', 'bajas_bovinos', 'reproduccion', 'sanidad', 'pesajes', 'bovinos', 'insumos', 'users'];
    foreach ($tablas as $t) {
        $pdo->exec("DROP TABLE IF EXISTS {$t}");
    }
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // 1. Tabla users (RF-01 / RN-06)
    $pdo->exec("CREATE TABLE users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        email VARCHAR(120) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        rol ENUM('admin', 'operador') NOT NULL DEFAULT 'operador',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 2. Tabla insumos (RF-06 / RF-08)
    $pdo->exec("CREATE TABLE insumos (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nombre_insumo VARCHAR(120) NOT NULL,
        tipo_insumo ENUM('vacuna', 'medicamento', 'antiparasitario', 'balanceado', 'sal_mineral', 'otro') NOT NULL,
        stock_actual DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        stock_minimo DECIMAL(10,2) NOT NULL DEFAULT 5.00,
        unidad_medida VARCHAR(20) NOT NULL,
        dias_retiro INT UNSIGNED NOT NULL DEFAULT 0,
        costo_unitario DECIMAL(12,2) NOT NULL DEFAULT 0.00,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 3. Tabla bovinos (RF-02 / RN-01)
    $pdo->exec("CREATE TABLE bovinos (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        rp VARCHAR(20) NOT NULL UNIQUE,
        nombre VARCHAR(80) NULL,
        categoria ENUM('ternero', 'desmamante', 'vaquilla', 'novillo', 'vaca', 'toro') NOT NULL,
        raza VARCHAR(60) NOT NULL,
        sexo ENUM('M', 'H') NOT NULL,
        fecha_nacimiento DATE NOT NULL,
        peso_actual DECIMAL(6,2) NULL DEFAULT NULL,
        proposito ENUM('carne', 'leche', 'doble_proposito') NOT NULL DEFAULT 'carne',
        sistema_alimentacion ENUM('pastura_libre', 'semiconfinamiento', 'confinamiento_encierro') NOT NULL DEFAULT 'pastura_libre',
        estado_sanitario ENUM('sano', 'en_tratamiento', 'en_carencia') NOT NULL DEFAULT 'sano',
        fecha_liberacion_carencia DATE NULL DEFAULT NULL,
        estado_reproductivo ENUM('no_aplica', 'vacia', 'servida', 'prenada', 'lactante') NOT NULL DEFAULT 'no_aplica',
        estado_general ENUM('activo', 'vendido', 'muerto', 'consumo') NOT NULL DEFAULT 'activo',
        observaciones TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 4. Tabla pesajes (RF-03 / RN-04)
    $pdo->exec("CREATE TABLE pesajes (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        bovino_id INT UNSIGNED NOT NULL,
        fecha_pesaje DATE NOT NULL,
        peso_kg DECIMAL(6,2) NOT NULL,
        dias_transcurridos INT UNSIGNED NOT NULL DEFAULT 0,
        gmd_calculada DECIMAL(6,3) NULL DEFAULT NULL,
        observaciones VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_pesajes_bovino FOREIGN KEY (bovino_id) REFERENCES bovinos(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 5. Tabla sanidad (RF-04 / RN-03)
    $pdo->exec("CREATE TABLE sanidad (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        bovino_id INT UNSIGNED NOT NULL,
        insumo_id INT UNSIGNED NOT NULL,
        tipo_evento ENUM('vacunacion', 'desparasitacion', 'tratamiento_clinico') NOT NULL,
        fecha_aplicacion DATE NOT NULL,
        dosis_aplicada DECIMAL(8,2) NOT NULL,
        dias_carencia INT UNSIGNED NOT NULL DEFAULT 0,
        fecha_fin_retiro DATE NULL DEFAULT NULL,
        costo_total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
        diagnostico_observacion VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_sanidad_bovino FOREIGN KEY (bovino_id) REFERENCES bovinos(id) ON DELETE RESTRICT,
        CONSTRAINT fk_sanidad_insumo FOREIGN KEY (insumo_id) REFERENCES insumos(id) ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 6. Tabla reproduccion (RF-07)
    $pdo->exec("CREATE TABLE reproduccion (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        bovino_id INT UNSIGNED NOT NULL,
        tipo_evento ENUM('celo', 'monta_natural', 'inseminacion', 'confirmacion_prenez', 'parto', 'aborto') NOT NULL,
        fecha_evento DATE NOT NULL,
        toro_o_semen VARCHAR(80) NULL,
        fecha_estimada_parto DATE NULL DEFAULT NULL,
        resultado_observacion VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_repro_bovino FOREIGN KEY (bovino_id) REFERENCES bovinos(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 7. Tabla bajas_bovinos (RF-05)
    $pdo->exec("CREATE TABLE bajas_bovinos (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        bovino_id INT UNSIGNED NOT NULL UNIQUE,
        fecha_baja DATE NOT NULL,
        tipo_baja ENUM('venta', 'muerte', 'consumo') NOT NULL,
        detalle_motivo VARCHAR(255) NOT NULL,
        precio_venta_gs DECIMAL(12,2) NOT NULL DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_bajas_bovino FOREIGN KEY (bovino_id) REFERENCES bovinos(id) ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 8. Tablas Conceptuales Porcino y Avícola (RF-11 / RF-12)
    $pdo->exec("CREATE TABLE lotes_porcinos (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        codigo_piara VARCHAR(30) NOT NULL UNIQUE,
        categoria_porcina ENUM('lechones', 'engorde', 'cerdas_madres', 'verracos') NOT NULL,
        cantidad_cabezas INT UNSIGNED NOT NULL DEFAULT 0,
        galpon_corral VARCHAR(50) NOT NULL,
        sistema_alimentacion VARCHAR(60) DEFAULT 'Balanceado intensivo',
        estado ENUM('activo', 'cerrado') DEFAULT 'activo',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE lotes_avicolas (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        codigo_lote VARCHAR(30) NOT NULL UNIQUE,
        linea_proposito ENUM('postura_huevos', 'parrilleros_carne', 'caseros_doble') NOT NULL,
        cantidad_aves INT UNSIGNED NOT NULL DEFAULT 0,
        galpon VARCHAR(50) NOT NULL,
        mortandad_acumulada INT UNSIGNED NOT NULL DEFAULT 0,
        produccion_diaria_huevos INT UNSIGNED NOT NULL DEFAULT 0,
        estado ENUM('activo', 'cerrado') DEFAULT 'activo',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Generar hash Bcrypt real con el motor PHP 8.2 del contenedor
    $hashReal = password_hash('password123', PASSWORD_BCRYPT);

    $stmtUser = $pdo->prepare("INSERT INTO users (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
    $stmtUser->execute(['Mónica Dressler (Propietaria / Admin)', 'admin@ganado.py', $hashReal, 'admin']);
    $stmtUser->execute(['Juan Benítez (Operador de Campo)', 'operador@ganado.py', $hashReal, 'operador']);

    // Insertar Insumos Semilla
    $pdo->exec("INSERT INTO insumos (nombre_insumo, tipo_insumo, stock_actual, stock_minimo, unidad_medida, dias_retiro, costo_unitario) VALUES
        ('Vacuna Antiaftosa Bivalente', 'vacuna', 45.00, 10.00, 'Dosis', 0, 8500.00),
        ('Ivermectina 1% Endectocida', 'antiparasitario', 18.00, 5.00, 'Dosis', 35, 6500.00),
        ('Oxitetraciclina LA Antibiótico', 'medicamento', 3.00, 5.00, 'Dosis', 28, 12000.00),
        ('Balanceado Engorde Colonias 18%', 'balanceado', 850.00, 200.00, 'Kg', 0, 2400.00),
        ('Sal Mineralizada Fosfatada', 'sal_mineral', 40.00, 50.00, 'Kg', 0, 5500.00)");

    // Insertar Bovinos Semilla
    $pdo->exec("INSERT INTO bovinos (rp, nombre, categoria, raza, sexo, fecha_nacimiento, peso_actual, proposito, sistema_alimentacion, estado_sanitario, fecha_liberacion_carencia, estado_reproductivo, estado_general, observaciones) VALUES
        ('BOV-0101', 'Estrella', 'vaquilla', 'Brangus', 'H', '2024-03-15', 315.00, 'carne', 'pastura_libre', 'sano', NULL, 'prenada', 'activo', 'Potrero Norte - Excelente conformación'),
        ('BOV-0102', 'Campeón', 'novillo', 'Nelore', 'M', '2024-01-20', 376.00, 'carne', 'confinamiento_encierro', 'en_carencia', '2026-11-15', 'no_aplica', 'activo', 'Lote de terminación intensiva'),
        ('BOV-0103', 'Margarita', 'vaca', 'Holando', 'H', '2022-08-10', 460.00, 'leche', 'semiconfinamiento', 'sano', NULL, 'lactante', 'activo', 'Vaca lechera de ordeñe familiar'),
        ('BOV-0104', 'Sultán', 'toro', 'Braford', 'M', '2021-05-12', 640.00, 'carne', 'pastura_libre', 'sano', NULL, 'no_aplica', 'activo', 'Reproductor principal del hato')");

    // Insertar Pesajes, Sanidad, Reproducción y Lotes Extensibles
    $pdo->exec("INSERT INTO pesajes (bovino_id, fecha_pesaje, peso_kg, dias_transcurridos, gmd_calculada, observaciones) VALUES
        (1, '2026-06-01', 270.00, 0, NULL, 'Pesaje base de invierno'),
        (1, '2026-08-30', 315.00, 90, 0.500, 'Evolución normal en pastura'),
        (2, '2026-07-01', 310.00, 0, NULL, 'Ingreso a corral de engorde'),
        (2, '2026-08-30', 376.00, 60, 1.100, 'Excelente conversión en confinamiento')");

    $pdo->exec("INSERT INTO sanidad (bovino_id, insumo_id, tipo_evento, fecha_aplicacion, dosis_aplicada, dias_carencia, fecha_fin_retiro, costo_total, diagnostico_observacion) VALUES
        (1, 1, 'vacunacion', '2026-08-15', 1.00, 0, '2026-08-15', 8500.00, 'Campaña sanitaria oficial sin retiro'),
        (2, 2, 'desparasitacion', '2026-10-11', 2.00, 35, '2026-11-15', 13000.00, 'Desparasitación con bloqueo de carencia activo')");

    $pdo->exec("INSERT INTO reproduccion (bovino_id, tipo_evento, fecha_evento, toro_o_semen, fecha_estimada_parto, resultado_observacion) VALUES
        (1, 'confirmacion_prenez', '2026-06-15', 'Inseminación Pajuela Brangus Elite', '2027-03-25', 'Preñez confirmada por palpación')");

    $pdo->exec("INSERT INTO lotes_porcinos (codigo_piara, categoria_porcina, cantidad_cabezas, galpon_corral, sistema_alimentacion) VALUES
        ('POR-2026-01', 'engorde', 14, 'Chiquero N° 1', 'Suero lácteo + Balanceado Maíz/Soja'),
        ('POR-2026-02', 'cerdas_madres', 3, 'Paridera Familiar', 'Ración Gestación/Lactancia')");

    $pdo->exec("INSERT INTO lotes_avicolas (codigo_lote, linea_proposito, cantidad_aves, galpon, mortandad_acumulada, produccion_diaria_huevos) VALUES
        ('AVI-2026-01', 'postura_huevos', 85, 'Galpón Postura Este', 2, 68),
        ('AVI-2026-02', 'parrilleros_carne', 50, 'Galpón Pollos Engorde', 1, 0)");

    echo "<div style='font-family:sans-serif;max-width:600px;margin:40px auto;padding:25px;border-radius:10px;background:#d1e7dd;color:#0f5132;border:1px solid #badbcc;'>
            <h2>✅ ¡Base de Datos y Credenciales Instaladas!</h2>
            <p>Se crearon las 8 tablas en <strong>mi_clase_db</strong> y se encriptaron las claves con Bcrypt.</p>
            <ul>
                <li><strong>Administrador:</strong> <code>admin@ganado.py</code> | Clave: <code>password123</code></li>
                <li><strong>Operador:</strong> <code>operador@ganado.py</code> | Clave: <code>password123</code></li>
            </ul>
            <p><a href='index.php?action=logout' style='display:inline-block;padding:10px 18px;background:#198754;color:#fff;text-decoration:none;border-radius:6px;font-weight:bold;'>Ir a Iniciar Sesión ahora →</a></p>
          </div>";

} catch (Exception $e) {
    echo "<div style='font-family:sans-serif;padding:20px;background:#f8d7da;color:#842029;'>
            <h3>Error al instalar:</h3>
            <p>" . htmlspecialchars($e->getMessage()) . "</p>
          </div>";
}