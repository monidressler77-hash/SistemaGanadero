-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 11-11-2026 a las 22:41:15
-- Versión del servidor: 8.0.46
-- Versión de PHP: 8.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `mi_clase_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bajas_bovinos`
--

CREATE TABLE `bajas_bovinos` (
  `id` int UNSIGNED NOT NULL,
  `bovino_id` int UNSIGNED NOT NULL,
  `fecha_baja` date NOT NULL,
  `tipo_baja` enum('venta','muerte','consumo') COLLATE utf8mb4_unicode_ci NOT NULL,
  `detalle_motivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio_venta_gs` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bovinos`
--

CREATE TABLE `bovinos` (
  `id` int UNSIGNED NOT NULL,
  `rp` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categoria` enum('ternero','desmamante','vaquilla','novillo','vaca','toro') COLLATE utf8mb4_unicode_ci NOT NULL,
  `raza` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sexo` enum('M','H') COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `peso_actual` decimal(6,2) DEFAULT NULL,
  `proposito` enum('carne','leche','doble_proposito') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'carne',
  `sistema_alimentacion` enum('pastura_libre','semiconfinamiento','confinamiento_encierro') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pastura_libre',
  `estado_sanitario` enum('sano','en_tratamiento','en_carencia') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sano',
  `fecha_liberacion_carencia` date DEFAULT NULL,
  `estado_reproductivo` enum('no_aplica','vacia','servida','prenada','lactante') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no_aplica',
  `estado_general` enum('activo','vendido','muerto','consumo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `bovinos`
--

INSERT INTO `bovinos` (`id`, `rp`, `nombre`, `categoria`, `raza`, `sexo`, `fecha_nacimiento`, `peso_actual`, `proposito`, `sistema_alimentacion`, `estado_sanitario`, `fecha_liberacion_carencia`, `estado_reproductivo`, `estado_general`, `observaciones`, `created_at`) VALUES
(1, 'BOV-0101', 'Estrella', 'vaquilla', 'Brangus', 'H', '2024-03-15', 315.00, 'carne', 'pastura_libre', 'sano', NULL, 'prenada', 'activo', 'Potrero Norte - Excelente conformación', '2026-10-14 22:24:30'),
(2, 'BOV-0102', 'Campeón', 'novillo', 'Nelore', 'M', '2024-01-20', 376.00, 'carne', 'confinamiento_encierro', 'en_carencia', '2026-11-15', 'no_aplica', 'activo', 'Lote de terminación intensiva', '2026-10-14 22:24:30'),
(3, 'BOV-0103', 'Margarita', 'vaca', 'Holando', 'H', '2022-08-10', 460.00, 'leche', 'semiconfinamiento', 'sano', NULL, 'lactante', 'activo', 'Vaca lechera de ordeñe familiar', '2026-10-14 22:24:30'),
(4, 'BOV-0104', 'Sultán', 'toro', 'Braford', 'M', '2021-05-12', 750.00, 'carne', 'pastura_libre', 'sano', NULL, 'no_aplica', 'activo', 'Reproductor principal del hato', '2026-10-14 22:24:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumos`
--

CREATE TABLE `insumos` (
  `id` int UNSIGNED NOT NULL,
  `nombre_insumo` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_insumo` enum('vacuna','medicamento','antiparasitario','balanceado','sal_mineral','otro') COLLATE utf8mb4_unicode_ci NOT NULL,
  `stock_actual` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock_minimo` decimal(10,2) NOT NULL DEFAULT '5.00',
  `unidad_medida` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dias_retiro` int UNSIGNED NOT NULL DEFAULT '0',
  `costo_unitario` decimal(12,2) NOT NULL DEFAULT '0.00',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `insumos`
--

INSERT INTO `insumos` (`id`, `nombre_insumo`, `tipo_insumo`, `stock_actual`, `stock_minimo`, `unidad_medida`, `dias_retiro`, `costo_unitario`, `updated_at`) VALUES
(1, 'Vacuna Antiaftosa Bivalente', 'vacuna', 44.00, 10.00, 'Dosis', 0, 8500.00, '2026-10-14 22:43:06'),
(2, 'Ivermectina 1% Endectocida', 'antiparasitario', 18.00, 5.00, 'Dosis', 35, 6500.00, '2026-10-14 22:24:30'),
(3, 'Oxitetraciclina LA Antibiótico', 'medicamento', 3.00, 5.00, 'Dosis', 28, 12000.00, '2026-10-14 22:24:30'),
(4, 'Balanceado Engorde Colonias 18%', 'balanceado', 850.00, 200.00, 'Kg', 0, 2400.00, '2026-10-14 22:24:30'),
(5, 'Sal Mineralizada Fosfatada', 'sal_mineral', 40.00, 50.00, 'Kg', 0, 5500.00, '2026-10-14 22:24:30'),
(6, 'Expeller de Soja', 'balanceado', 900.00, 100.00, 'kg', 0, 1000.00, '2026-10-14 23:14:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lotes_avicolas`
--

CREATE TABLE `lotes_avicolas` (
  `id` int UNSIGNED NOT NULL,
  `codigo_lote` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `linea_proposito` enum('postura_huevos','parrilleros_carne','caseros_doble') COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad_aves` int UNSIGNED NOT NULL DEFAULT '0',
  `galpon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mortandad_acumulada` int UNSIGNED NOT NULL DEFAULT '0',
  `produccion_diaria_huevos` int UNSIGNED NOT NULL DEFAULT '0',
  `estado` enum('activo','cerrado') COLLATE utf8mb4_unicode_ci DEFAULT 'activo',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `lotes_avicolas`
--

INSERT INTO `lotes_avicolas` (`id`, `codigo_lote`, `linea_proposito`, `cantidad_aves`, `galpon`, `mortandad_acumulada`, `produccion_diaria_huevos`, `estado`, `created_at`) VALUES
(1, 'AVI-2026-01', 'postura_huevos', 85, 'Galpón Postura Este', 2, 68, 'activo', '2026-10-14 22:24:30'),
(2, 'AVI-2026-02', 'parrilleros_carne', 50, 'Galpón Pollos Engorde', 1, 0, 'activo', '2026-10-14 22:24:30'),
(3, 'AVI-2026-03', 'postura_huevos', 50, 'Galpón Ponedoras Postura', 0, 35, 'activo', '2026-10-14 23:17:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lotes_porcinos`
--

CREATE TABLE `lotes_porcinos` (
  `id` int UNSIGNED NOT NULL,
  `codigo_piara` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria_porcina` enum('lechones','engorde','cerdas_madres','verracos') COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad_cabezas` int UNSIGNED NOT NULL DEFAULT '0',
  `galpon_corral` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sistema_alimentacion` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT 'Balanceado intensivo',
  `estado` enum('activo','cerrado') COLLATE utf8mb4_unicode_ci DEFAULT 'activo',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `lotes_porcinos`
--

INSERT INTO `lotes_porcinos` (`id`, `codigo_piara`, `categoria_porcina`, `cantidad_cabezas`, `galpon_corral`, `sistema_alimentacion`, `estado`, `created_at`) VALUES
(1, 'POR-2026-01', 'engorde', 14, 'Chiquero N° 1', 'Suero lácteo + Balanceado Maíz/Soja', 'activo', '2026-10-14 22:24:30'),
(2, 'POR-2026-02', 'cerdas_madres', 3, 'Paridera Familiar', 'Ración Gestación/Lactancia', 'activo', '2026-10-14 22:24:30'),
(3, 'POR-202603', 'lechones', 20, 'Chiquero N° 2', 'Balanceado Maíz/Soja', 'activo', '2026-10-14 23:15:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pesajes`
--

CREATE TABLE `pesajes` (
  `id` int UNSIGNED NOT NULL,
  `bovino_id` int UNSIGNED NOT NULL,
  `fecha_pesaje` date NOT NULL,
  `peso_kg` decimal(6,2) NOT NULL,
  `dias_transcurridos` int UNSIGNED NOT NULL DEFAULT '0',
  `gmd_calculada` decimal(6,3) DEFAULT NULL,
  `observaciones` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pesajes`
--

INSERT INTO `pesajes` (`id`, `bovino_id`, `fecha_pesaje`, `peso_kg`, `dias_transcurridos`, `gmd_calculada`, `observaciones`, `created_at`) VALUES
(1, 1, '2026-06-01', 270.00, 0, NULL, 'Pesaje base de invierno', '2026-10-14 22:24:30'),
(2, 1, '2026-08-30', 315.00, 90, 0.500, 'Evolución normal en pastura', '2026-10-14 22:24:30'),
(3, 2, '2026-07-01', 310.00, 0, NULL, 'Ingreso a corral de engorde', '2026-10-14 22:24:30'),
(4, 2, '2026-08-30', 376.00, 60, 1.100, 'Excelente conversión en confinamiento', '2026-10-14 22:24:30'),
(5, 4, '2026-10-14', 700.00, 0, NULL, NULL, '2026-10-14 22:42:34'),
(6, 4, '2026-10-31', 750.00, 17, 2.941, NULL, '2026-10-14 22:43:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reproduccion`
--

CREATE TABLE `reproduccion` (
  `id` int UNSIGNED NOT NULL,
  `bovino_id` int UNSIGNED NOT NULL,
  `tipo_evento` enum('celo','monta_natural','inseminacion','confirmacion_prenez','parto','aborto') COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_evento` date NOT NULL,
  `toro_o_semen` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_estimada_parto` date DEFAULT NULL,
  `resultado_observacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reproduccion`
--

INSERT INTO `reproduccion` (`id`, `bovino_id`, `tipo_evento`, `fecha_evento`, `toro_o_semen`, `fecha_estimada_parto`, `resultado_observacion`, `created_at`) VALUES
(1, 1, 'confirmacion_prenez', '2026-06-15', 'Inseminación Pajuela Brangus Elite', '2027-03-25', 'Preñez confirmada por palpación', '2026-10-14 22:24:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sanidad`
--

CREATE TABLE `sanidad` (
  `id` int UNSIGNED NOT NULL,
  `bovino_id` int UNSIGNED NOT NULL,
  `insumo_id` int UNSIGNED NOT NULL,
  `tipo_evento` enum('vacunacion','desparasitacion','tratamiento_clinico') COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_aplicacion` date NOT NULL,
  `dosis_aplicada` decimal(8,2) NOT NULL,
  `dias_carencia` int UNSIGNED NOT NULL DEFAULT '0',
  `fecha_fin_retiro` date DEFAULT NULL,
  `costo_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `diagnostico_observacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sanidad`
--

INSERT INTO `sanidad` (`id`, `bovino_id`, `insumo_id`, `tipo_evento`, `fecha_aplicacion`, `dosis_aplicada`, `dias_carencia`, `fecha_fin_retiro`, `costo_total`, `diagnostico_observacion`, `created_at`) VALUES
(1, 1, 1, 'vacunacion', '2026-08-15', 1.00, 0, '2026-08-15', 8500.00, 'Campaña sanitaria oficial sin retiro', '2026-10-14 22:24:30'),
(2, 2, 2, 'desparasitacion', '2026-10-11', 2.00, 35, '2026-11-15', 13000.00, 'Desparasitación con bloqueo de carencia activo', '2026-10-14 22:24:30'),
(3, 4, 1, 'vacunacion', '2026-10-14', 1.00, 0, '2026-10-14', 8500.00, NULL, '2026-10-14 22:43:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('admin','operador') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'operador',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `nombre`, `email`, `password`, `rol`, `created_at`) VALUES
(1, 'Mónica Dressler (Propietaria / Admin)', 'admin@ganado.py', '$2y$10$eMne/9fe7dvVTwsBcG1ETubJtCaq/HMuSdgilx1SLWMyjQcSSINDW', 'admin', '2026-10-14 22:24:30'),
(2, 'Juan Benítez (Operador de Campo)', 'operador@ganado.py', '$2y$10$eMne/9fe7dvVTwsBcG1ETubJtCaq/HMuSdgilx1SLWMyjQcSSINDW', 'operador', '2026-10-14 22:24:30');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bajas_bovinos`
--
ALTER TABLE `bajas_bovinos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bovino_id` (`bovino_id`);

--
-- Indices de la tabla `bovinos`
--
ALTER TABLE `bovinos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rp` (`rp`);

--
-- Indices de la tabla `insumos`
--
ALTER TABLE `insumos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `lotes_avicolas`
--
ALTER TABLE `lotes_avicolas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_lote` (`codigo_lote`);

--
-- Indices de la tabla `lotes_porcinos`
--
ALTER TABLE `lotes_porcinos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_piara` (`codigo_piara`);

--
-- Indices de la tabla `pesajes`
--
ALTER TABLE `pesajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pesajes_bovino` (`bovino_id`);

--
-- Indices de la tabla `reproduccion`
--
ALTER TABLE `reproduccion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_repro_bovino` (`bovino_id`);

--
-- Indices de la tabla `sanidad`
--
ALTER TABLE `sanidad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sanidad_bovino` (`bovino_id`),
  ADD KEY `fk_sanidad_insumo` (`insumo_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bajas_bovinos`
--
ALTER TABLE `bajas_bovinos`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `bovinos`
--
ALTER TABLE `bovinos`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `insumos`
--
ALTER TABLE `insumos`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `lotes_avicolas`
--
ALTER TABLE `lotes_avicolas`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `lotes_porcinos`
--
ALTER TABLE `lotes_porcinos`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `pesajes`
--
ALTER TABLE `pesajes`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `reproduccion`
--
ALTER TABLE `reproduccion`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `sanidad`
--
ALTER TABLE `sanidad`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bajas_bovinos`
--
ALTER TABLE `bajas_bovinos`
  ADD CONSTRAINT `fk_bajas_bovino` FOREIGN KEY (`bovino_id`) REFERENCES `bovinos` (`id`) ON DELETE RESTRICT;

--
-- Filtros para la tabla `pesajes`
--
ALTER TABLE `pesajes`
  ADD CONSTRAINT `fk_pesajes_bovino` FOREIGN KEY (`bovino_id`) REFERENCES `bovinos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reproduccion`
--
ALTER TABLE `reproduccion`
  ADD CONSTRAINT `fk_repro_bovino` FOREIGN KEY (`bovino_id`) REFERENCES `bovinos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sanidad`
--
ALTER TABLE `sanidad`
  ADD CONSTRAINT `fk_sanidad_bovino` FOREIGN KEY (`bovino_id`) REFERENCES `bovinos` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_sanidad_insumo` FOREIGN KEY (`insumo_id`) REFERENCES `insumos` (`id`) ON DELETE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
