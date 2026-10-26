-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 06-10-2026 a las 05:35:41
-- Versión del servidor: 12.1.2-MariaDB-log
-- Versión de PHP: 8.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `db_proyecto_renta`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anios`
--

CREATE TABLE `anios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `anio` year(4) NOT NULL,
  `archivo_excel` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:0:{}s:11:\"permissions\";a:0:{}s:5:\"roles\";a:0:{}}', 1791350761);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `numero_factura` varchar(50) NOT NULL,
  `monto_total` decimal(12,2) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id`, `fecha`, `numero_factura`, `monto_total`, `observaciones`, `user_id`, `created_at`, `updated_at`) VALUES
(1, '2025-10-31', 'SALDO-INICIAL', 171383.99, 'Existencias de los distritos al 31/10/2025 según sus libros de especies (carga inicial)', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(2, '2025-12-12', '06383', 1050.00, 'Compra al M.H. registrada en los libros de especies (carga inicial)', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(3, '2026-03-10', '06585', 94519.50, 'Compra al M.H. registrada en los libros de especies (carga inicial)', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(4, '2026-03-10', '06589', 930.00, 'Compra al M.H. registrada en los libros de especies (carga inicial)', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(5, '2026-03-10', '06586', 1291.50, 'Compra al M.H. registrada en los libros de especies (carga inicial)', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(6, '2026-03-10', '06588', 1333.00, 'Compra al M.H. registrada en los libros de especies (carga inicial)', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(7, '2026-03-10', '06587', 31179.00, 'Compra al M.H. registrada en los libros de especies (carga inicial)', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(8, '2026-03-23', '06619', 1000.00, 'Compra al M.H. registrada en los libros de especies (carga inicial)', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(9, '2026-03-23', '06621', 250.00, 'Compra al M.H. registrada en los libros de especies (carga inicial)', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consultas`
--

CREATE TABLE `consultas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nitdui` varchar(255) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `buscado_en` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `denominaciones`
--

CREATE TABLE `denominaciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tipo_especie_id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `precio_costo` decimal(10,4) DEFAULT NULL,
  `precio_venta` decimal(10,4) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `denominaciones`
--

INSERT INTO `denominaciones` (`id`, `tipo_especie_id`, `descripcion`, `valor`, `precio_costo`, `precio_venta`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 0.57, 0.1000, 0.5700, 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(2, 1, NULL, 0.69, NULL, 0.6900, 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(3, 1, NULL, 1.14, 0.1500, 1.1400, 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(4, 1, NULL, 1.71, NULL, 1.7100, 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(5, 1, NULL, 2.29, NULL, 2.2900, 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(6, 1, NULL, 2.86, NULL, 2.8600, 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(7, 1, NULL, 3.43, 0.1000, 3.4300, 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(9, 2, NULL, 0.12, 0.0105, 0.1200, 1, '2026-07-22 04:29:39', '2026-09-30 06:43:52'),
(10, 2, NULL, 0.15, 0.0150, 0.1500, 1, '2026-07-22 04:29:39', '2026-09-30 06:43:52'),
(11, 2, NULL, 0.25, 0.0150, 0.2500, 1, '2026-07-22 04:29:39', '2026-09-30 06:43:52'),
(12, 2, NULL, 0.50, 0.0150, 0.5000, 1, '2026-07-22 04:29:39', '2026-09-30 06:43:52'),
(13, 2, NULL, 1.00, 0.0150, 1.0000, 1, '2026-07-22 04:29:39', '2026-09-30 06:43:52'),
(14, 3, NULL, 0.50, 0.0150, 0.5000, 1, '2026-07-22 04:29:39', '2026-09-30 06:43:52'),
(15, 4, NULL, 0.15, 0.1500, NULL, 1, '2026-07-22 04:29:39', '2026-09-30 06:35:45'),
(16, 5, NULL, 0.06, 0.0600, NULL, 1, '2026-07-22 04:29:39', '2026-09-30 06:43:52'),
(17, 5, NULL, 0.09, 0.0900, NULL, 1, '2026-07-22 04:29:39', '2026-09-30 06:43:52'),
(18, 6, NULL, 0.30, 0.3000, NULL, 1, '2026-07-22 04:29:39', '2026-09-30 06:35:45'),
(19, 7, NULL, 0.06, 0.0600, NULL, 1, '2026-07-22 04:29:39', '2026-09-30 06:35:45'),
(20, 8, NULL, 0.10, 0.1000, NULL, 1, '2026-07-22 04:29:39', '2026-09-30 06:35:45'),
(21, 9, NULL, 0.53, 0.5300, NULL, 1, '2026-07-22 04:29:39', '2026-09-30 06:35:45'),
(22, 10, NULL, 0.20, 0.2000, NULL, 1, '2026-07-22 04:29:39', '2026-09-30 06:35:45'),
(23, 11, NULL, 0.06, 0.0600, NULL, 1, '2026-07-22 04:29:39', '2026-09-30 06:35:45'),
(24, 12, NULL, 0.26, 0.2600, NULL, 1, '2026-07-22 04:29:39', '2026-09-30 06:35:45'),
(25, 13, NULL, 0.26, 0.2600, NULL, 1, '2026-07-22 04:29:39', '2026-09-30 06:35:45'),
(26, 14, NULL, 0.10, 0.1000, NULL, 1, '2026-07-22 04:29:39', '2026-09-30 06:35:45'),
(27, 15, NULL, 0.30, 0.3000, NULL, 1, '2026-07-22 04:29:39', '2026-09-30 06:35:45'),
(28, 17, NULL, 0.15, 0.1500, NULL, 1, '2026-09-30 06:35:45', '2026-09-30 06:35:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `distritos`
--

CREATE TABLE `distritos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `nombre_oficial` varchar(150) DEFAULT NULL,
  `lugar_firma` varchar(150) DEFAULT NULL,
  `saldo_por_valor` tinyint(1) NOT NULL DEFAULT 1,
  `codigo` varchar(10) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `distritos`
--

INSERT INTO `distritos` (`id`, `nombre`, `nombre_oficial`, `lugar_firma`, `saldo_por_valor`, `codigo`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Distrito Metapán', 'DISTRITO DE METAPÁN', 'UNIDAD DE TESORERIA DISTRITO DE METAPAN', 1, 'D01', 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(2, 'Distrito Masahuat', 'DISTRITO DE MASAHUAT', NULL, 1, 'D02', 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(3, 'Distrito Santa Rosa Guachipilín', 'DISTRITO DE SANTA ROSA GUACHIPILIN', NULL, 1, 'D03', 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(4, 'Distrito Texistepeque', 'DISTRITO DE TEXISTEPEQUE', NULL, 0, 'D04', 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `distrito_denominacion`
--

CREATE TABLE `distrito_denominacion` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `distrito_id` bigint(20) UNSIGNED NOT NULL,
  `denominacion_id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `precio_costo` decimal(10,4) DEFAULT NULL,
  `precio_venta` decimal(10,4) DEFAULT NULL,
  `orden` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `descripcion_nulas` varchar(150) DEFAULT NULL,
  `orden_nulas` smallint(5) UNSIGNED DEFAULT NULL,
  `nulas_con_venta` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `distrito_denominacion`
--

INSERT INTO `distrito_denominacion` (`id`, `distrito_id`, `denominacion_id`, `descripcion`, `precio_costo`, `precio_venta`, `orden`, `descripcion_nulas`, `orden_nulas`, `nulas_con_venta`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'DE $0.57', 0.0750, 0.5700, 1, 'DE $0.57 NULAS', 4, 1, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(2, 1, 3, 'DE $1.14', 0.1500, 1.1400, 2, 'DE $1.14 NULAS', 5, 1, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(3, 1, 7, 'DE $ 3.43', 0.1000, 3.4300, 3, 'DE $3.43 NULAS', 6, 1, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(4, 1, 9, 'DE $0.12', 0.0105, 0.1200, 1, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(5, 1, 10, 'DE $0.15', 0.0150, 0.1500, 2, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(6, 1, 12, 'DE $0.50', 0.0150, 0.5000, 3, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(7, 1, 13, 'DE $1.00', 0.0150, 1.0000, 4, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(8, 1, 14, 'DE $0.50', 0.0150, 0.5000, 1, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(9, 1, 15, 'CARTAS DE VENTA', 0.1500, NULL, 1, 'NULAS', 2, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(10, 1, 18, 'DE $3.15', 0.3000, 3.1500, 1, 'NULAS', 2, 1, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(11, 1, 20, 'FÓRMULAS CONTINUAS', 0.1000, NULL, 1, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(12, 1, 21, 'TITULOS', 0.5300, NULL, 1, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(13, 1, 22, 'GUIAS DE CONDUCCION DE GANADO', 0.2000, NULL, 1, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(14, 2, 1, 'DE $0.57', 0.1000, 0.5700, 1, 'DE $0.57 NULAS', 4, 1, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(15, 2, 7, 'DE $ 3.43', 0.1000, 3.4300, 2, 'DE $3.43 NULAS', 3, 1, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(16, 2, 28, 'CARTAS DE VENTA', 0.1500, NULL, 1, 'NULAS', 2, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(17, 2, 15, 'CARTAS DE VENTA', 0.1500, NULL, 1, 'NULAS', 2, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(18, 2, 18, 'TARJETAS PARA IDENTIFICACION', 0.3000, NULL, 1, 'NULAS', 2, 1, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(19, 2, 19, 'FÓRMULAS 1-ISAM FORMA PLANA', 0.0600, NULL, 1, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(20, 2, 20, 'FÓRMULAS 1-ISAM CONTINUA', 0.1000, NULL, 1, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(21, 2, 21, 'TITULOS', 0.5300, NULL, 1, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(22, 2, 23, 'MATERIAL PETREO', 0.0600, NULL, 1, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(23, 2, 24, 'TARJETAS DE INMUEBLES', 0.2600, NULL, 1, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(24, 2, 25, 'TARJETAS DE EMPRESA', 0.2600, NULL, 1, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(25, 2, 22, 'GUIAS DE CONDUCCION DE GANADO', 0.2000, NULL, 1, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(26, 3, 7, 'DE $ 3.43', 0.1000, 3.4300, 1, 'DE $3.43 NULAS', 2, 1, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(27, 3, 10, 'DE $0.15', 0.0150, NULL, 1, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(28, 3, 13, 'DE $1.00', 0.0150, NULL, 2, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(29, 3, 15, 'CARTAS DE VENTA', 0.1500, NULL, 1, 'NULAS', 2, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(30, 3, 20, 'FÓRMULAS 1-ISAM CONTINUA', 0.1000, NULL, 1, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(31, 3, 21, 'TITULOS', 0.5300, NULL, 1, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(32, 3, 22, 'GUIAS DE CONDUCCION DE GANADO', 0.2000, NULL, 1, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(33, 4, 7, 'DE $ 3.43', 0.1000, 3.4300, 1, 'DE $3.43 NULAS', 2, 1, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(34, 4, 11, 'DE $0.25', 0.0150, 0.2500, 1, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(35, 4, 13, 'DE $1.00', 0.0150, 1.0000, 2, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(36, 4, 27, 'TARJETAS DE CONTROL DE MERCADO', 0.3000, NULL, 1, 'NULAS', 2, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(37, 4, 15, 'CARTAS DE VENTA', 0.1500, NULL, 1, 'NULAS', 2, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(38, 4, 17, 'JUEGO DE CARTULINA', 0.0900, 2.0000, 1, 'NULAS', 2, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(39, 4, 26, 'AVISOS DE COBRO COMUN', 0.1000, NULL, 1, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(40, 4, 20, 'FÓRMULAS 1-ISAM CONTINUA', 0.1000, NULL, 1, NULL, NULL, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(41, 4, 21, 'TITULOS', 0.5300, NULL, 1, NULL, NULL, 0, '2026-09-30 06:43:53', '2026-09-30 06:43:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `distrito_tipo_especie`
--

CREATE TABLE `distrito_tipo_especie` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `distrito_id` bigint(20) UNSIGNED NOT NULL,
  `tipo_especie_id` bigint(20) UNSIGNED NOT NULL,
  `titulo_libro` varchar(150) DEFAULT NULL,
  `titulo_reporte` varchar(150) DEFAULT NULL,
  `orden` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `nulas_en_realizaciones` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `distrito_tipo_especie`
--

INSERT INTO `distrito_tipo_especie` (`id`, `distrito_id`, `tipo_especie_id`, `titulo_libro`, `titulo_reporte`, `orden`, `nulas_en_realizaciones`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'FONDO DE VIALIDAD', 'FONDO DE VIALIDAD', 1, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(2, 1, 2, 'TIQUETES DE MERCADO', 'TIQUETES DE MERCADO', 2, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(3, 1, 3, 'TIQUETES SERVICIO DE BASCULA', 'TIQUETES SERVICIO DE BASCULA', 3, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(4, 1, 4, 'CARTAS DE VENTA', 'CARTAS DE VENTA', 4, 1, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(5, 1, 6, 'TARJETAS PARA CARNET DE IDENTIFICACIÓN', 'TARJETAS PARA CARNET DE IDENTIFICACIÓN', 5, 1, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(6, 1, 8, 'FORMULAS 1-ISAM', 'FORMULAS 1-I SAM', 6, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(7, 1, 9, 'TITULOS A PERPETUIDAD', 'TITULOS A PERPETUIDAD EN CEMENTERIOS', 7, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(8, 1, 10, 'GUIAS DE CONDUCCION DE GANADO MAYOR Y MENOR', 'GUIAS DE CONDUCCION DE GANADO MAYOR Y MENOR', 8, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(9, 2, 1, 'FONDO DE VIALIDAD', 'FONDO DE VIALIDAD', 1, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(10, 2, 17, 'CARTAS DE VENTA', 'CARTAS DE VENTA', 2, 1, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(11, 2, 4, 'CARTA DE VENTA CONTINUA', 'CARTAS DE VENTA CONTINUAS', 3, 1, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(12, 2, 6, 'TARJETAS PARA CARNET DE IDENTIFICACIÓN', 'TARJETAS PARA CARNET DE IDENTIFICACIÓN', 4, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(13, 2, 7, 'FORMULAS 1-ISAM FORMA PLANA', 'FORMULAS 1-I SAM FORMA PLANA', 5, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(14, 2, 8, 'FORMULAS 1-ISAM CONTINUA', 'FORMULAS 1-I SAM FORMA CONTINUA', 6, 1, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(15, 2, 9, 'TITULOS A PERPETUIDAD', 'TITULOS A PERPETUIDAD EN CEMENTERIOS', 7, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(16, 2, 11, 'BOLETOS DE COBRO DE MATERIAL PETREO', 'BOLETOS DE COBRO DE MATERIAL PETREO', 8, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(17, 2, 12, 'TARJETAS DE INMUEBLES', 'TARJETAS DE INMUEBLES', 9, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(18, 2, 13, 'TARJETAS DE EMPRESA', 'TARJETAS DE EMPRESA', 10, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(19, 2, 10, 'GUIAS DE CONDUCCION DE GANADO MAYOR Y MENOR', 'GUIAS DE CONDUCCION DE GANADO MAYOR Y MENOR', 11, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(20, 3, 1, 'FONDO DE VIALIDAD', 'FONDO DE VIALIDAD', 1, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(21, 3, 2, 'TIQUETES DE MERCADO', 'TIQUETES DE MERCADO', 2, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(22, 3, 4, 'CARTAS DE VENTA', 'CARTAS DE VENTA', 3, 1, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(23, 3, 8, 'FORMULAS 1-ISAM', 'FORMULAS 1-I SAM FORMA CONTINUA', 4, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(24, 3, 9, 'TITULOS A PERPETUIDAD', 'TITULOS A PERPETUIDAD EN CEMENTERIOS', 5, 1, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(25, 3, 10, 'GUIAS DE CONDUCCION DE GANADO MAYOR Y MENOR', 'GUIAS DE CONDUCCION DE GANADO MAYOR Y MENOR', 6, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(26, 4, 1, 'FONDO DE VIALIDAD', 'FONDO DE VIALIDAD', 1, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(27, 4, 2, 'TIQUETES DE MERCADO', 'TIQUETES DE MERCADO', 2, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(28, 4, 15, 'TARJETAS DE CONTROL DE MERCADO', 'TARJETAS DE CONTROL DE MERCADO', 3, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(29, 4, 4, 'CARTAS DE VENTA FORMA CONTINUA', 'CARTA DE VENTA CONTINUA', 4, 1, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(30, 4, 5, 'CARNET DE MENORES', 'CARNET DE IDENTIDAD DE MENORES', 5, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(31, 4, 14, 'AVISOS DE COBRO COMUN', 'AVISOS DE COBRO COMUN', 6, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(32, 4, 8, 'FORMULAS 1-ISAM', 'FORMULAS 1-I SAM FORMA CONTINUA', 7, 0, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(33, 4, 9, 'TITULOS A PERPETUIDAD', 'TITULOS A PERPETUIDAD EN CEMENTERIOS', 8, 0, '2026-09-30 06:43:53', '2026-09-30 06:43:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lotes`
--

CREATE TABLE `lotes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `compra_id` bigint(20) UNSIGNED NOT NULL,
  `tipo_especie_id` bigint(20) UNSIGNED NOT NULL,
  `denominacion_id` bigint(20) UNSIGNED NOT NULL,
  `serie` varchar(10) DEFAULT NULL,
  `cantidad_total` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `lotes`
--

INSERT INTO `lotes` (`id`, `compra_id`, `tipo_especie_id`, `denominacion_id`, `serie`, `cantidad_total`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'C', 305, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(2, 1, 1, 3, 'C', 46, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(3, 1, 1, 7, 'C', 1644, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(4, 1, 2, 9, 'Z', 34752, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(5, 1, 2, 9, 'X', 140000, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(6, 1, 2, 10, 'J', 72441, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(7, 1, 2, 10, 'J', 200000, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(8, 1, 2, 12, 'D', 44306, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(9, 1, 2, 12, 'D', 20000, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(10, 1, 2, 13, 'B', 6321, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(11, 1, 3, 14, 'A', 5500, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(12, 1, 4, 15, 'A', 2127, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(13, 1, 4, 15, 'A', 10000, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(14, 1, 6, 18, 'A', 301, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(15, 1, 6, 18, NULL, 214, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(16, 1, 8, 20, 'B', 6823, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(17, 1, 8, 20, 'C', 12500, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(18, 1, 8, 20, 'B', 8120, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(19, 1, 8, 20, 'C', 10000, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(20, 1, 8, 20, 'C', 13173, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(21, 1, 8, 20, 'U', 3, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(22, 1, 8, 20, 'U', 3, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(23, 1, 8, 20, 'U', 4, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(24, 1, 8, 20, 'U', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(25, 1, 8, 20, 'U', 11, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(26, 1, 8, 20, 'U', 2078, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(27, 1, 8, 20, 'B', 458, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(28, 1, 9, 21, 'A', 127, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(29, 1, 9, 21, 'A', 100, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(30, 1, 1, 1, 'C', 200, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(31, 1, 1, 7, 'C', 15, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(32, 1, 1, 7, 'C', 93, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(33, 1, 1, 7, 'C', 100, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(34, 1, 1, 7, 'C', 100, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(35, 1, 17, 28, 'L', 99, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(36, 1, 4, 15, 'A', 1500, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(37, 1, 6, 18, 'A', 39, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(38, 1, 6, 18, 'A', 100, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(39, 1, 6, 18, 'A', 100, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(40, 1, 7, 19, 'L', 162, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(41, 1, 8, 20, 'C', 2062, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(42, 1, 8, 20, 'D', 5000, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(43, 1, 9, 21, 'A', 100, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(44, 1, 9, 21, 'A', 50, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(45, 1, 11, 23, 'A', 150, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(46, 1, 12, 24, 'A', 18, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(47, 1, 12, 24, 'A', 100, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(48, 1, 12, 24, 'A', 100, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(49, 1, 13, 25, 'A', 100, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(50, 1, 2, 10, 'G', 1000, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(51, 1, 2, 13, 'B', 800, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(52, 1, 2, 13, 'B', 2982, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(53, 1, 2, 13, 'B', 5860, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(54, 1, 4, 15, 'A', 4118, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(55, 1, 4, 15, 'A', 3750, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(56, 1, 8, 20, 'B', 1548, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(57, 1, 8, 20, 'C', 7500, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(58, 1, 9, 21, NULL, 31, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(59, 1, 9, 21, NULL, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(60, 1, 9, 21, 'A', 100, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(61, 1, 9, 21, 'A', 50, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(62, 1, 1, 7, 'C', 62, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(63, 1, 1, 7, 'C', 21, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(64, 1, 1, 7, 'C', 100, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(65, 1, 1, 7, 'C', 13, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(66, 1, 1, 7, 'C', 200, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(67, 1, 1, 7, 'C', 500, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(68, 1, 2, 11, 'E', 57168, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(69, 1, 2, 13, 'I', 9861, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(70, 1, 2, 13, 'I', 10000, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(71, 1, 15, 27, 'B', 500, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(72, 1, 4, 15, 'A', 5000, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(73, 1, 5, 17, NULL, 27, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(74, 1, 5, 17, NULL, 100, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(75, 1, 5, 17, NULL, 100, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(76, 1, 5, 17, NULL, 102, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(77, 1, 5, 16, NULL, 20, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(78, 1, 14, 26, 'P', 4202, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(79, 1, 8, 20, 'C', 3554, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(80, 1, 8, 20, 'D', 15000, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(81, 1, 9, 21, NULL, 113, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(82, 1, 9, 21, NULL, 100, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(83, 1, 9, 21, 'A', 300, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(84, 2, 6, 18, 'A', 1000, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(85, 2, 8, 20, 'D', 7500, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(86, 3, 1, 7, 'C', 3000, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(87, 3, 2, 10, 'C', 260000, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(88, 3, 2, 10, 'B', 20000, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(89, 3, 2, 12, 'D', 70000, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(90, 3, 4, 15, 'A', 9990, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(91, 3, 4, 15, 'A', 10, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(92, 3, 6, 18, 'A', 500, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(93, 3, 8, 20, 'E', 5000, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(94, 3, 9, 21, 'A', 150, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(95, 3, 8, 20, 'E', 50000, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(96, 4, 10, 22, 'A', 4650, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(97, 5, 4, 15, 'A', 4500, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(98, 5, 6, 18, 'A', 100, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(99, 5, 8, 20, 'E', 5000, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(100, 5, 9, 21, 'A', 50, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(101, 5, 10, 22, 'A', 300, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(102, 6, 4, 15, 'A', 3000, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(103, 6, 8, 20, 'E', 5000, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(104, 6, 10, 22, 'A', 200, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(105, 6, 1, 7, 'C', 100, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(106, 7, 1, 7, 'C', 300, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(107, 7, 2, 11, 'M', 80000, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(108, 7, 2, 13, 'A', 10000, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(109, 7, 15, 27, 'A', 500, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(110, 8, 8, 20, 'E', 10000, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(111, 9, 8, 20, 'E', 2500, '2026-09-30 06:43:53', '2026-09-30 06:43:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lote_rangos`
--

CREATE TABLE `lote_rangos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lote_id` bigint(20) UNSIGNED NOT NULL,
  `numero_inicio` int(10) UNSIGNED NOT NULL,
  `numero_fin` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `lote_rangos`
--

INSERT INTO `lote_rangos` (`id`, `lote_id`, `numero_inicio`, `numero_fin`) VALUES
(1, 1, 33696, 34000),
(2, 2, 38505, 38550),
(3, 3, 131657, 133300),
(4, 4, 765249, 800000),
(5, 5, 840001, 980000),
(6, 6, 37560, 110000),
(7, 7, 110001, 310000),
(8, 8, 95695, 140000),
(9, 9, 320001, 340000),
(10, 10, 833680, 840000),
(11, 11, 14501, 20000),
(12, 12, 28174, 30300),
(13, 13, 130151, 140150),
(14, 14, 13600, 13900),
(15, 15, 48387, 48600),
(16, 16, 950678, 957500),
(17, 17, 892501, 905000),
(18, 18, 964381, 972500),
(19, 19, 905001, 915000),
(20, 20, 916828, 930000),
(21, 21, 900236, 900238),
(22, 22, 900269, 900271),
(23, 23, 900273, 900276),
(24, 24, 900279, 900279),
(25, 25, 900310, 900320),
(26, 26, 900323, 902400),
(27, 27, 977043, 977500),
(28, 28, 233524, 233650),
(29, 29, 242101, 242200),
(30, 30, 38101, 38300),
(31, 31, 860886, 860900),
(32, 32, 999208, 999300),
(33, 33, 133801, 133900),
(34, 34, 227601, 227700),
(35, 35, 801102, 801200),
(36, 36, 162651, 164150),
(37, 37, 367862, 367900),
(38, 38, 16401, 16500),
(39, 39, 31401, 31500),
(40, 40, 139339, 139500),
(41, 41, 52939, 55000),
(42, 42, 25001, 30000),
(43, 43, 234301, 234400),
(44, 44, 242601, 242650),
(45, 45, 1851, 2000),
(46, 46, 98887, 98904),
(47, 47, 21601, 21700),
(48, 48, 29451, 29550),
(49, 49, 24501, 24600),
(50, 50, 719001, 720000),
(51, 51, 840201, 841000),
(52, 52, 841019, 844000),
(53, 53, 844141, 850000),
(54, 54, 17233, 21350),
(55, 55, 140151, 143900),
(56, 56, 973453, 975000),
(57, 57, 930001, 937500),
(58, 58, 195320, 195350),
(59, 59, 195400, 195400),
(60, 60, 233901, 234000),
(61, 61, 242201, 242250),
(62, 62, 555439, 555500),
(63, 63, 763880, 763900),
(64, 64, 227301, 227400),
(65, 65, 10988, 11000),
(66, 66, 85601, 85800),
(67, 67, 133301, 133800),
(68, 68, 232833, 290000),
(69, 69, 590140, 600000),
(70, 70, 910001, 920000),
(71, 71, 99463, 99962),
(72, 72, 157651, 162650),
(73, 73, 740473, 740499),
(74, 74, 782001, 782100),
(75, 75, 798701, 798800),
(76, 76, 495499, 495600),
(77, 77, 529981, 530000),
(78, 78, 475799, 480000),
(79, 79, 48947, 52500),
(80, 80, 10001, 25000),
(81, 81, 167688, 167800),
(82, 82, 219601, 219700),
(83, 83, 234001, 234300),
(84, 84, 40301, 41300),
(85, 85, 430001, 437500),
(86, 86, 257801, 260800),
(87, 87, 1, 260000),
(88, 88, 980001, 1000000),
(89, 89, 580001, 650000),
(90, 90, 299511, 309500),
(91, 91, 265651, 265660),
(92, 92, 66201, 66700),
(93, 93, 145001, 150000),
(94, 94, 249651, 249800),
(95, 95, 237501, 287500),
(96, 96, 45001, 49650),
(97, 97, 309501, 314000),
(98, 98, 66701, 66800),
(99, 99, 150001, 155000),
(100, 100, 249801, 249850),
(101, 101, 44501, 44800),
(102, 102, 317751, 320750),
(103, 103, 160001, 165000),
(104, 104, 44801, 45000),
(105, 105, 261101, 261200),
(106, 106, 260801, 261100),
(107, 107, 700001, 780000),
(108, 108, 240001, 250000),
(109, 109, 68701, 69200),
(110, 110, 217501, 227500),
(111, 111, 235001, 237500);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_05_10_014755_create_permission_tables', 1),
(5, '2026_05_10_020000_create_anios_table', 1),
(6, '2026_05_20_032705_add_archivo_excel_to_anios_table', 2),
(7, '2026_06_02_000000_create_consultas_table', 3),
(8, '2026_07_07_000001_create_distritos_table', 4),
(9, '2026_07_07_000002_create_tipo_especies_table', 4),
(10, '2026_07_07_000003_create_denominaciones_table', 4),
(11, '2026_07_07_000004_create_compras_table', 4),
(12, '2026_07_07_000005_create_lotes_table', 4),
(13, '2026_07_07_000006_create_lote_rangos_table', 4),
(14, '2026_07_07_000007_create_nulas_table', 4),
(15, '2026_07_07_000008_create_traslados_table', 4),
(16, '2026_07_07_000009_create_traslado_detalles_table', 4),
(17, '2026_07_07_000010_create_realizaciones_table', 4),
(18, '2026_07_09_000001_rebuild_especies_municipales', 5),
(19, '2026_07_21_221627_add_user_id_to_compras_table', 6),
(20, '2026_08_06_153502_rebuild_realizaciones_as_ranges', 7),
(21, '2026_09_06_000001_add_tipo_to_traslados_table', 8),
(22, '2026_09_24_000001_add_descripcion_precio_costo_to_denominaciones_table', 9),
(23, '2026_09_29_000001_add_serie_to_realizaciones_table', 10),
(26, '2026_09_30_000001_add_configuracion_reportes_a_especies', 11);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(1, 'App\\Models\\User', 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nulas`
--

CREATE TABLE `nulas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `traslado_detalle_id` bigint(20) UNSIGNED NOT NULL,
  `distrito_id` bigint(20) UNSIGNED NOT NULL,
  `numero_inicio` int(10) UNSIGNED NOT NULL,
  `numero_fin` int(10) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `nulas`
--

INSERT INTO `nulas` (`id`, `traslado_detalle_id`, `distrito_id`, `numero_inicio`, `numero_fin`, `fecha`, `motivo`, `usuario_id`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 131660, 131661, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(2, 12, 1, 28217, 28217, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(3, 12, 1, 28227, 28227, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(4, 12, 1, 28250, 28250, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(5, 12, 1, 28259, 28259, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(6, 12, 1, 28273, 28273, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(7, 12, 1, 28308, 28309, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(8, 12, 1, 28325, 28325, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(9, 12, 1, 28328, 28328, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(10, 12, 1, 28332, 28332, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(11, 12, 1, 28347, 28347, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(12, 12, 1, 28393, 28393, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(13, 12, 1, 28439, 28439, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(14, 12, 1, 28448, 28448, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(15, 12, 1, 28489, 28489, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(16, 12, 1, 28516, 28516, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(17, 12, 1, 28600, 28600, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(18, 12, 1, 28621, 28621, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(19, 12, 1, 28645, 28645, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(20, 12, 1, 28672, 28672, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(21, 12, 1, 28700, 28700, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(22, 12, 1, 28711, 28711, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(23, 12, 1, 28723, 28723, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(24, 12, 1, 28725, 28725, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(25, 12, 1, 28742, 28742, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(26, 12, 1, 28829, 28829, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(27, 12, 1, 28856, 28856, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(28, 12, 1, 28905, 28905, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(29, 12, 1, 28908, 28908, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(30, 12, 1, 28911, 28911, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(31, 12, 1, 28984, 28984, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(32, 12, 1, 29065, 29065, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(33, 12, 1, 29069, 29069, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(34, 12, 1, 29102, 29108, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(35, 12, 1, 29165, 29165, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(36, 12, 1, 29254, 29254, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(37, 12, 1, 29265, 29265, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(38, 12, 1, 29267, 29267, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(39, 12, 1, 29272, 29272, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(40, 12, 1, 29283, 29283, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(41, 12, 1, 29309, 29309, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(42, 14, 1, 13747, 13747, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(43, 35, 2, 801104, 801105, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(44, 35, 2, 801108, 801108, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(45, 35, 2, 801183, 801183, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(46, 35, 2, 801188, 801189, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(47, 35, 2, 801200, 801200, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(48, 36, 2, 162651, 162652, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(49, 54, 3, 17239, 17239, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(50, 54, 3, 17267, 17267, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(51, 54, 3, 17283, 17283, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(52, 54, 3, 17286, 17286, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(53, 54, 3, 17292, 17292, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(54, 54, 3, 17315, 17315, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(55, 54, 3, 17350, 17350, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(56, 54, 3, 17354, 17354, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(57, 54, 3, 17369, 17369, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(58, 54, 3, 17391, 17393, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(59, 54, 3, 17406, 17406, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(60, 54, 3, 17425, 17425, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(61, 54, 3, 17455, 17455, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(62, 54, 3, 17478, 17478, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(63, 54, 3, 17480, 17480, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(64, 54, 3, 17483, 17483, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(65, 54, 3, 17489, 17489, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(66, 54, 3, 17529, 17529, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(67, 54, 3, 17532, 17532, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(68, 54, 3, 17568, 17568, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(69, 54, 3, 17577, 17579, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(70, 58, 3, 195320, 195321, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(71, 72, 4, 157653, 157654, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(72, 72, 4, 157689, 157689, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(73, 72, 4, 157702, 157702, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(74, 72, 4, 157710, 157710, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(75, 72, 4, 157714, 157714, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(76, 72, 4, 157737, 157737, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(77, 72, 4, 157770, 157770, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(78, 72, 4, 157881, 157881, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(79, 72, 4, 157960, 157960, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(80, 72, 4, 158002, 158002, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(81, 72, 4, 158056, 158056, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(82, 72, 4, 158081, 158081, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(83, 72, 4, 158087, 158087, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(84, 72, 4, 158131, 158131, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(85, 73, 4, 740484, 740484, '2025-11-30', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(86, 3, 1, 131675, 131675, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(87, 12, 1, 29326, 29326, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(88, 12, 1, 29332, 29332, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(89, 12, 1, 29344, 29344, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(90, 12, 1, 29347, 29347, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(91, 12, 1, 29352, 29353, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(92, 12, 1, 29390, 29390, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(93, 12, 1, 29395, 29396, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(94, 12, 1, 29448, 29448, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(95, 12, 1, 29487, 29487, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(96, 12, 1, 29493, 29493, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(97, 12, 1, 29500, 29500, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(98, 12, 1, 29518, 29518, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(99, 12, 1, 29538, 29538, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(100, 12, 1, 29548, 29549, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(101, 12, 1, 29552, 29552, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(102, 12, 1, 29580, 29581, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(103, 12, 1, 29603, 29603, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(104, 12, 1, 29608, 29608, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(105, 12, 1, 29628, 29628, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(106, 12, 1, 29633, 29633, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(107, 12, 1, 29650, 29650, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(108, 12, 1, 29653, 29654, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(109, 12, 1, 29714, 29714, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(110, 12, 1, 29728, 29729, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(111, 12, 1, 29731, 29731, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(112, 12, 1, 29785, 29785, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(113, 12, 1, 29840, 29840, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(114, 12, 1, 29844, 29844, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(115, 12, 1, 29887, 29887, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(116, 12, 1, 29930, 29931, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(117, 12, 1, 29933, 29933, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(118, 12, 1, 29961, 29961, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(119, 12, 1, 30008, 30008, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(120, 12, 1, 30031, 30031, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(121, 12, 1, 30061, 30061, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(122, 12, 1, 30078, 30078, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(123, 12, 1, 30104, 30104, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(124, 12, 1, 30132, 30132, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(125, 12, 1, 30135, 30135, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(126, 12, 1, 30165, 30165, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(127, 12, 1, 30173, 30173, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(128, 12, 1, 30224, 30224, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(129, 12, 1, 30274, 30274, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(130, 12, 1, 30291, 30291, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(131, 12, 1, 30297, 30297, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(132, 13, 1, 130165, 130165, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(133, 13, 1, 130167, 130167, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(134, 13, 1, 130191, 130191, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(135, 14, 1, 13793, 13793, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(136, 14, 1, 13892, 13892, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(137, 36, 2, 162721, 162721, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(138, 36, 2, 162732, 162732, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(139, 36, 2, 162749, 162749, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(140, 54, 3, 17614, 17614, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(141, 54, 3, 17685, 17685, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(142, 54, 3, 17710, 17712, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(143, 54, 3, 17720, 17720, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(144, 54, 3, 17726, 17726, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(145, 54, 3, 17781, 17781, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(146, 54, 3, 17785, 17785, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(147, 54, 3, 17812, 17812, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(148, 72, 4, 158164, 158164, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(149, 72, 4, 158236, 158236, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(150, 72, 4, 158242, 158242, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(151, 72, 4, 158293, 158293, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(152, 72, 4, 158297, 158297, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(153, 72, 4, 158307, 158307, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(154, 72, 4, 158309, 158309, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(155, 72, 4, 158332, 158332, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(156, 72, 4, 158338, 158338, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(157, 72, 4, 158423, 158423, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(158, 72, 4, 158446, 158446, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(159, 72, 4, 158474, 158474, '2025-12-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(160, 3, 1, 131894, 131894, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(161, 3, 1, 131899, 131899, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(162, 3, 1, 131906, 131906, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(163, 13, 1, 130245, 130246, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(164, 13, 1, 130366, 130366, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(165, 13, 1, 130414, 130414, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(166, 13, 1, 130430, 130430, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(167, 13, 1, 130461, 130462, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(168, 13, 1, 130467, 130467, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(169, 13, 1, 130474, 130474, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(170, 13, 1, 130492, 130492, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(171, 13, 1, 130561, 130561, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(172, 13, 1, 130630, 130630, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(173, 13, 1, 130638, 130638, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(174, 13, 1, 130644, 130645, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(175, 13, 1, 130743, 130743, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(176, 13, 1, 130758, 130758, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(177, 13, 1, 130761, 130761, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(178, 13, 1, 130774, 130774, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(179, 13, 1, 130788, 130788, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(180, 13, 1, 130802, 130802, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(181, 13, 1, 130823, 130823, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(182, 13, 1, 130827, 130827, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(183, 13, 1, 130831, 130831, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(184, 13, 1, 130843, 130843, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(185, 13, 1, 130856, 130856, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(186, 13, 1, 130929, 130929, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(187, 13, 1, 130950, 130950, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(188, 13, 1, 130964, 130964, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(189, 13, 1, 131039, 131039, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(190, 13, 1, 131068, 131068, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(191, 13, 1, 131081, 131081, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(192, 13, 1, 131099, 131099, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(193, 13, 1, 131164, 131164, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(194, 13, 1, 131175, 131175, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(195, 13, 1, 131201, 131201, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(196, 13, 1, 131269, 131269, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(197, 13, 1, 131271, 131272, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(198, 13, 1, 131280, 131280, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(199, 13, 1, 131282, 131282, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(200, 13, 1, 131296, 131296, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(201, 13, 1, 131299, 131300, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(202, 13, 1, 131370, 131370, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(203, 13, 1, 131377, 131377, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(204, 13, 1, 131392, 131392, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(205, 13, 1, 131427, 131427, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(206, 13, 1, 131481, 131481, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(207, 13, 1, 131540, 131540, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(208, 13, 1, 131548, 131548, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(209, 13, 1, 131552, 131552, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(210, 13, 1, 131555, 131555, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(211, 13, 1, 131641, 131641, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(212, 13, 1, 131654, 131654, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(213, 13, 1, 131683, 131683, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(214, 13, 1, 131685, 131685, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(215, 13, 1, 131718, 131718, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(216, 84, 1, 40354, 40354, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(217, 84, 1, 40382, 40382, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(218, 84, 1, 40387, 40387, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(219, 84, 1, 40394, 40394, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(220, 36, 2, 162874, 162874, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(221, 36, 2, 162971, 162971, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(222, 36, 2, 162979, 162979, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(223, 41, 2, 53829, 53829, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(224, 41, 2, 53831, 53831, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(225, 41, 2, 54136, 54136, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(226, 54, 3, 17899, 17900, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(227, 54, 3, 17949, 17949, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(228, 54, 3, 17973, 17974, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(229, 54, 3, 17995, 17995, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(230, 54, 3, 18003, 18003, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(231, 54, 3, 18006, 18009, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(232, 54, 3, 18048, 18048, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(233, 54, 3, 18063, 18063, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(234, 54, 3, 18092, 18092, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(235, 54, 3, 18096, 18097, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(236, 54, 3, 18144, 18146, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(237, 54, 3, 18148, 18149, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(238, 54, 3, 18161, 18161, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(239, 54, 3, 18196, 18196, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(240, 54, 3, 18215, 18215, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(241, 54, 3, 18218, 18218, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(242, 54, 3, 18235, 18235, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(243, 54, 3, 18258, 18258, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(244, 72, 4, 158551, 158551, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(245, 72, 4, 158555, 158555, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(246, 72, 4, 158618, 158618, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(247, 72, 4, 158651, 158651, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(248, 72, 4, 158690, 158690, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(249, 72, 4, 158695, 158695, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(250, 72, 4, 158726, 158726, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(251, 72, 4, 158749, 158749, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(252, 72, 4, 158796, 158796, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(253, 72, 4, 158822, 158822, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(254, 72, 4, 158848, 158848, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(255, 72, 4, 158879, 158879, '2026-01-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(256, 3, 1, 132127, 132127, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(257, 3, 1, 132129, 132129, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(258, 3, 1, 132142, 132142, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(259, 3, 1, 132150, 132150, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(260, 3, 1, 132183, 132183, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(261, 3, 1, 132192, 132192, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(262, 3, 1, 132213, 132213, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(263, 3, 1, 132226, 132226, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(264, 3, 1, 132232, 132232, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(265, 3, 1, 132253, 132254, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(266, 3, 1, 132268, 132268, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(267, 3, 1, 132272, 132272, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(268, 3, 1, 132281, 132281, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(269, 3, 1, 132287, 132287, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(270, 3, 1, 132290, 132290, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(271, 3, 1, 132358, 132358, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(272, 3, 1, 132376, 132376, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(273, 3, 1, 132385, 132385, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(274, 3, 1, 132394, 132394, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(275, 3, 1, 132401, 132401, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(276, 3, 1, 132407, 132407, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(277, 3, 1, 132435, 132435, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(278, 13, 1, 131750, 131750, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(279, 13, 1, 131832, 131832, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(280, 13, 1, 131930, 131931, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(281, 13, 1, 131955, 131955, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(282, 13, 1, 131962, 131963, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(283, 13, 1, 131966, 131966, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(284, 13, 1, 131982, 131982, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(285, 13, 1, 131990, 131990, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(286, 13, 1, 131992, 131992, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(287, 13, 1, 132043, 132043, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(288, 13, 1, 132058, 132058, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(289, 13, 1, 132072, 132072, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(290, 13, 1, 132138, 132138, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(291, 13, 1, 132154, 132154, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(292, 13, 1, 132163, 132163, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(293, 13, 1, 132172, 132172, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(294, 13, 1, 132179, 132179, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(295, 13, 1, 132186, 132186, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(296, 13, 1, 132188, 132188, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(297, 13, 1, 132203, 132205, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(298, 13, 1, 132255, 132255, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(299, 13, 1, 132281, 132282, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(300, 13, 1, 132305, 132305, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(301, 13, 1, 132331, 132331, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(302, 13, 1, 132372, 132372, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(303, 13, 1, 132395, 132395, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(304, 13, 1, 132405, 132405, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(305, 13, 1, 132502, 132502, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(306, 13, 1, 132555, 132555, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(307, 13, 1, 132574, 132574, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(308, 13, 1, 132580, 132580, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(309, 13, 1, 132623, 132623, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(310, 13, 1, 132669, 132669, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(311, 13, 1, 132690, 132691, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(312, 13, 1, 132739, 132739, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(313, 13, 1, 132764, 132764, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(314, 13, 1, 132771, 132771, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(315, 13, 1, 132775, 132775, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(316, 13, 1, 132796, 132796, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(317, 13, 1, 132871, 132871, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(318, 13, 1, 132878, 132880, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(319, 13, 1, 132945, 132945, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(320, 13, 1, 132964, 132964, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(321, 13, 1, 132990, 132990, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(322, 13, 1, 133014, 133014, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(323, 13, 1, 133066, 133066, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(324, 13, 1, 133142, 133142, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(325, 13, 1, 133165, 133165, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(326, 13, 1, 133219, 133220, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(327, 13, 1, 133223, 133223, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(328, 13, 1, 133264, 133264, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(329, 84, 1, 40670, 40670, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(330, 84, 1, 40678, 40678, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(331, 84, 1, 40695, 40695, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(332, 31, 2, 860895, 860895, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(333, 31, 2, 860898, 860898, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(334, 36, 2, 163141, 163141, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(335, 36, 2, 163172, 163172, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(336, 36, 2, 163196, 163196, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(337, 36, 2, 163208, 163208, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(338, 36, 2, 163210, 163210, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(339, 41, 2, 54292, 54294, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(340, 41, 2, 54664, 54664, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(341, 41, 2, 54708, 54708, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(342, 41, 2, 54715, 54717, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(343, 54, 3, 18291, 18291, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(344, 54, 3, 18293, 18293, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(345, 54, 3, 18314, 18320, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(346, 54, 3, 18381, 18381, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(347, 54, 3, 18390, 18390, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(348, 54, 3, 18402, 18402, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(349, 54, 3, 18418, 18419, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(350, 54, 3, 18424, 18425, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(351, 54, 3, 18436, 18436, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(352, 54, 3, 18461, 18461, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(353, 54, 3, 18483, 18483, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(354, 66, 4, 85703, 85703, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(355, 72, 4, 158890, 158890, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(356, 72, 4, 158893, 158893, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(357, 72, 4, 158907, 158907, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(358, 72, 4, 159151, 159151, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(359, 72, 4, 159279, 159279, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(360, 72, 4, 159313, 159313, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(361, 72, 4, 159319, 159319, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(362, 72, 4, 159352, 159353, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(363, 72, 4, 159378, 159378, '2026-02-28', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(364, 3, 1, 132458, 132458, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(365, 3, 1, 132466, 132466, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(366, 3, 1, 132472, 132472, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(367, 3, 1, 132482, 132482, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(368, 3, 1, 132486, 132486, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(369, 3, 1, 132503, 132503, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(370, 3, 1, 132556, 132556, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(371, 3, 1, 132558, 132558, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(372, 3, 1, 132587, 132587, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(373, 13, 1, 133317, 133317, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(374, 13, 1, 133360, 133360, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(375, 13, 1, 133453, 133453, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(376, 13, 1, 133477, 133478, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(377, 13, 1, 133484, 133485, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(378, 13, 1, 133490, 133492, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(379, 13, 1, 133498, 133498, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(380, 13, 1, 133545, 133545, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(381, 13, 1, 133551, 133551, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(382, 13, 1, 133555, 133557, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(383, 13, 1, 133577, 133578, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(384, 13, 1, 133621, 133621, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(385, 13, 1, 133623, 133623, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(386, 13, 1, 133644, 133644, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(387, 13, 1, 133648, 133648, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(388, 13, 1, 133664, 133664, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(389, 13, 1, 133670, 133670, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(390, 13, 1, 133699, 133699, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(391, 13, 1, 133751, 133751, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(392, 13, 1, 133818, 133819, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(393, 13, 1, 133822, 133822, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(394, 13, 1, 133848, 133848, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(395, 13, 1, 133861, 133861, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(396, 13, 1, 133876, 133876, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(397, 13, 1, 133881, 133881, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(398, 13, 1, 133906, 133906, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(399, 13, 1, 133913, 133913, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(400, 13, 1, 133919, 133919, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(401, 13, 1, 133934, 133934, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(402, 13, 1, 133951, 133951, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(403, 13, 1, 133953, 133953, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(404, 13, 1, 133971, 133976, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(405, 13, 1, 133999, 133999, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(406, 13, 1, 134011, 134012, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(407, 13, 1, 134046, 134046, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(408, 13, 1, 134087, 134087, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(409, 13, 1, 134092, 134092, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(410, 13, 1, 134106, 134106, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(411, 13, 1, 134115, 134116, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(412, 13, 1, 134119, 134119, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(413, 13, 1, 134149, 134149, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(414, 13, 1, 134193, 134193, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(415, 13, 1, 134198, 134198, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(416, 13, 1, 134221, 134221, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(417, 13, 1, 134224, 134224, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(418, 13, 1, 134291, 134291, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(419, 13, 1, 134313, 134313, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(420, 13, 1, 134339, 134339, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(421, 13, 1, 134368, 134368, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(422, 13, 1, 134393, 134393, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(423, 84, 1, 40751, 40751, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(424, 36, 2, 163261, 163261, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(425, 54, 3, 18532, 18532, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(426, 54, 3, 18543, 18543, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(427, 54, 3, 18570, 18570, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(428, 54, 3, 18592, 18593, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(429, 54, 3, 18626, 18626, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(430, 54, 3, 18650, 18650, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(431, 54, 3, 18657, 18657, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(432, 66, 4, 85710, 85710, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(433, 66, 4, 85727, 85727, '2026-03-31', NULL, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `realizaciones`
--

CREATE TABLE `realizaciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tipo_especie_id` bigint(20) UNSIGNED NOT NULL,
  `denominacion_id` bigint(20) UNSIGNED NOT NULL,
  `serie` varchar(10) DEFAULT NULL,
  `distrito_id` bigint(20) UNSIGNED NOT NULL,
  `numero_inicio` int(10) UNSIGNED NOT NULL,
  `numero_fin` int(10) UNSIGNED NOT NULL,
  `cantidad` int(10) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `nombre_contribuyente` varchar(200) DEFAULT NULL,
  `monto_cobrado` decimal(12,2) NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `realizaciones`
--

INSERT INTO `realizaciones` (`id`, `tipo_especie_id`, `denominacion_id`, `serie`, `distrito_id`, `numero_inicio`, `numero_fin`, `cantidad`, `fecha`, `nombre_contribuyente`, `monto_cobrado`, `usuario_id`, `created_at`, `updated_at`) VALUES
(1, 1, 7, 'C', 1, 131657, 131659, 3, '2025-11-30', NULL, 10.29, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(2, 1, 7, 'C', 1, 131662, 131667, 6, '2025-11-30', NULL, 20.58, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(3, 2, 10, 'J', 1, 37560, 76845, 39286, '2025-11-30', NULL, 5892.90, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(4, 2, 12, 'D', 1, 95695, 103367, 7673, '2025-11-30', NULL, 3836.50, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(5, 2, 13, 'B', 1, 833680, 834049, 370, '2025-11-30', NULL, 370.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(6, 3, 14, 'A', 1, 14501, 14768, 268, '2025-11-30', NULL, 134.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(7, 4, 15, 'A', 1, 28174, 28216, 43, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(8, 4, 15, 'A', 1, 28218, 28226, 9, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(9, 4, 15, 'A', 1, 28228, 28249, 22, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(10, 4, 15, 'A', 1, 28251, 28258, 8, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(11, 4, 15, 'A', 1, 28260, 28272, 13, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(12, 4, 15, 'A', 1, 28274, 28307, 34, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(13, 4, 15, 'A', 1, 28310, 28324, 15, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(14, 4, 15, 'A', 1, 28326, 28327, 2, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(15, 4, 15, 'A', 1, 28329, 28331, 3, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(16, 4, 15, 'A', 1, 28333, 28346, 14, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(17, 4, 15, 'A', 1, 28348, 28392, 45, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(18, 4, 15, 'A', 1, 28394, 28438, 45, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(19, 4, 15, 'A', 1, 28440, 28447, 8, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(20, 4, 15, 'A', 1, 28449, 28488, 40, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(21, 4, 15, 'A', 1, 28490, 28515, 26, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(22, 4, 15, 'A', 1, 28517, 28599, 83, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(23, 4, 15, 'A', 1, 28601, 28620, 20, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(24, 4, 15, 'A', 1, 28622, 28644, 23, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(25, 4, 15, 'A', 1, 28646, 28671, 26, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(26, 4, 15, 'A', 1, 28673, 28699, 27, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(27, 4, 15, 'A', 1, 28701, 28710, 10, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(28, 4, 15, 'A', 1, 28712, 28722, 11, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(29, 4, 15, 'A', 1, 28724, 28724, 1, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(30, 4, 15, 'A', 1, 28726, 28741, 16, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(31, 4, 15, 'A', 1, 28743, 28828, 86, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(32, 4, 15, 'A', 1, 28830, 28855, 26, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(33, 4, 15, 'A', 1, 28857, 28904, 48, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(34, 4, 15, 'A', 1, 28906, 28907, 2, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(35, 4, 15, 'A', 1, 28909, 28910, 2, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(36, 4, 15, 'A', 1, 28912, 28983, 72, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(37, 4, 15, 'A', 1, 28985, 29064, 80, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(38, 4, 15, 'A', 1, 29066, 29068, 3, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(39, 4, 15, 'A', 1, 29070, 29101, 32, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(40, 4, 15, 'A', 1, 29109, 29164, 56, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(41, 4, 15, 'A', 1, 29166, 29253, 88, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(42, 4, 15, 'A', 1, 29255, 29264, 10, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(43, 4, 15, 'A', 1, 29266, 29266, 1, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(44, 4, 15, 'A', 1, 29268, 29271, 4, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(45, 4, 15, 'A', 1, 29273, 29282, 10, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(46, 4, 15, 'A', 1, 29284, 29308, 25, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(47, 4, 15, 'A', 1, 29310, 29325, 16, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(48, 6, 18, 'A', 1, 13600, 13746, 147, '2025-11-30', NULL, 463.05, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(49, 6, 18, 'A', 1, 13748, 13790, 43, '2025-11-30', NULL, 135.45, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(50, 8, 20, 'B', 1, 950678, 953240, 2563, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(51, 8, 20, 'B', 1, 964381, 966152, 1772, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(52, 8, 20, 'C', 1, 916828, 918229, 1402, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(53, 8, 20, 'B', 1, 977043, 977500, 458, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(54, 8, 20, 'C', 1, 927501, 927557, 57, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(55, 9, 21, 'A', 1, 233524, 233541, 18, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(56, 1, 7, 'C', 2, 860886, 860886, 1, '2025-11-30', NULL, 3.43, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(57, 17, 28, 'L', 2, 801102, 801103, 2, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(58, 17, 28, 'L', 2, 801106, 801107, 2, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(59, 17, 28, 'L', 2, 801109, 801182, 74, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(60, 17, 28, 'L', 2, 801184, 801187, 4, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(61, 17, 28, 'L', 2, 801190, 801199, 10, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(62, 4, 15, 'A', 2, 162653, 162682, 30, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(63, 6, 18, 'A', 2, 367862, 367867, 6, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(64, 8, 20, 'C', 2, 52939, 53302, 364, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(65, 9, 21, 'A', 2, 234301, 234305, 5, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(66, 12, 24, 'A', 2, 98887, 98887, 1, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(67, 4, 15, 'A', 3, 17233, 17238, 6, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(68, 4, 15, 'A', 3, 17240, 17266, 27, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(69, 4, 15, 'A', 3, 17268, 17282, 15, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(70, 4, 15, 'A', 3, 17284, 17285, 2, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(71, 4, 15, 'A', 3, 17287, 17291, 5, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(72, 4, 15, 'A', 3, 17293, 17314, 22, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(73, 4, 15, 'A', 3, 17316, 17349, 34, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(74, 4, 15, 'A', 3, 17351, 17353, 3, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(75, 4, 15, 'A', 3, 17355, 17368, 14, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(76, 4, 15, 'A', 3, 17370, 17390, 21, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(77, 4, 15, 'A', 3, 17394, 17405, 12, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(78, 4, 15, 'A', 3, 17407, 17424, 18, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(79, 4, 15, 'A', 3, 17426, 17454, 29, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(80, 4, 15, 'A', 3, 17456, 17477, 22, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(81, 4, 15, 'A', 3, 17479, 17479, 1, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(82, 4, 15, 'A', 3, 17481, 17482, 2, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(83, 4, 15, 'A', 3, 17484, 17488, 5, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(84, 4, 15, 'A', 3, 17490, 17528, 39, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(85, 4, 15, 'A', 3, 17530, 17531, 2, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(86, 4, 15, 'A', 3, 17533, 17567, 35, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(87, 4, 15, 'A', 3, 17569, 17576, 8, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(88, 4, 15, 'A', 3, 17580, 17599, 20, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(89, 8, 20, 'B', 3, 973453, 974157, 705, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(90, 9, 21, NULL, 3, 195400, 195400, 1, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(91, 9, 21, NULL, 3, 195322, 195330, 9, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(92, 2, 11, 'E', 4, 232833, 239040, 6208, '2025-11-30', NULL, 1552.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(93, 2, 13, 'I', 4, 590140, 591400, 1261, '2025-11-30', NULL, 1261.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(94, 15, 27, 'B', 4, 99463, 99584, 122, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(95, 4, 15, 'A', 4, 157651, 157652, 2, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(96, 4, 15, 'A', 4, 157655, 157688, 34, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(97, 4, 15, 'A', 4, 157690, 157701, 12, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(98, 4, 15, 'A', 4, 157703, 157709, 7, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(99, 4, 15, 'A', 4, 157711, 157713, 3, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(100, 4, 15, 'A', 4, 157715, 157736, 22, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(101, 4, 15, 'A', 4, 157738, 157769, 32, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(102, 4, 15, 'A', 4, 157771, 157880, 110, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(103, 4, 15, 'A', 4, 157882, 157959, 78, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(104, 4, 15, 'A', 4, 157961, 158001, 41, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(105, 4, 15, 'A', 4, 158003, 158055, 53, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(106, 4, 15, 'A', 4, 158057, 158080, 24, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(107, 4, 15, 'A', 4, 158082, 158086, 5, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(108, 4, 15, 'A', 4, 158088, 158130, 43, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(109, 4, 15, 'A', 4, 158132, 158143, 12, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(110, 5, 17, NULL, 4, 740473, 740483, 11, '2025-11-30', NULL, 22.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(111, 5, 17, NULL, 4, 740485, 740486, 2, '2025-11-30', NULL, 4.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(112, 8, 20, 'C', 4, 48947, 50780, 1834, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(113, 9, 21, NULL, 4, 167688, 167703, 16, '2025-11-30', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(114, 1, 7, 'C', 1, 131668, 131674, 7, '2025-12-31', NULL, 24.01, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(115, 1, 7, 'C', 1, 131676, 131880, 205, '2025-12-31', NULL, 703.15, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(116, 2, 10, 'J', 1, 76846, 123428, 46583, '2025-12-31', NULL, 6987.45, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(117, 2, 12, 'D', 1, 103368, 111357, 7990, '2025-12-31', NULL, 3995.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(118, 2, 13, 'B', 1, 834050, 834437, 388, '2025-12-31', NULL, 388.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(119, 3, 14, 'A', 1, 14770, 15023, 254, '2025-12-31', NULL, 127.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(120, 4, 15, 'A', 1, 29327, 29331, 5, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(121, 4, 15, 'A', 1, 29333, 29343, 11, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(122, 4, 15, 'A', 1, 29345, 29346, 2, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(123, 4, 15, 'A', 1, 29348, 29351, 4, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(124, 4, 15, 'A', 1, 29354, 29389, 36, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(125, 4, 15, 'A', 1, 29391, 29394, 4, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(126, 4, 15, 'A', 1, 29397, 29447, 51, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(127, 4, 15, 'A', 1, 29449, 29486, 38, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(128, 4, 15, 'A', 1, 29488, 29492, 5, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(129, 4, 15, 'A', 1, 29494, 29499, 6, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(130, 4, 15, 'A', 1, 29501, 29517, 17, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(131, 4, 15, 'A', 1, 29519, 29537, 19, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(132, 4, 15, 'A', 1, 29539, 29547, 9, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(133, 4, 15, 'A', 1, 29550, 29551, 2, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(134, 4, 15, 'A', 1, 29553, 29579, 27, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(135, 4, 15, 'A', 1, 29582, 29602, 21, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(136, 4, 15, 'A', 1, 29604, 29607, 4, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(137, 4, 15, 'A', 1, 29609, 29627, 19, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(138, 4, 15, 'A', 1, 29629, 29632, 4, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(139, 4, 15, 'A', 1, 29634, 29649, 16, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(140, 4, 15, 'A', 1, 29651, 29652, 2, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(141, 4, 15, 'A', 1, 29655, 29713, 59, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(142, 4, 15, 'A', 1, 29715, 29727, 13, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(143, 4, 15, 'A', 1, 29730, 29730, 1, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(144, 4, 15, 'A', 1, 29732, 29784, 53, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(145, 4, 15, 'A', 1, 29786, 29839, 54, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(146, 4, 15, 'A', 1, 29841, 29843, 3, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(147, 4, 15, 'A', 1, 29845, 29886, 42, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(148, 4, 15, 'A', 1, 29888, 29929, 42, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(149, 4, 15, 'A', 1, 29932, 29932, 1, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(150, 4, 15, 'A', 1, 29934, 29960, 27, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(151, 4, 15, 'A', 1, 29962, 30007, 46, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(152, 4, 15, 'A', 1, 30009, 30030, 22, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(153, 4, 15, 'A', 1, 30032, 30060, 29, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(154, 4, 15, 'A', 1, 30062, 30077, 16, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(155, 4, 15, 'A', 1, 30079, 30103, 25, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(156, 4, 15, 'A', 1, 30105, 30131, 27, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(157, 4, 15, 'A', 1, 30133, 30134, 2, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(158, 4, 15, 'A', 1, 30136, 30164, 29, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(159, 4, 15, 'A', 1, 30166, 30172, 7, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(160, 4, 15, 'A', 1, 30174, 30223, 50, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(161, 4, 15, 'A', 1, 30225, 30273, 49, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(162, 4, 15, 'A', 1, 30275, 30290, 16, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(163, 4, 15, 'A', 1, 30292, 30296, 5, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(164, 4, 15, 'A', 1, 30298, 30300, 3, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(165, 4, 15, 'A', 1, 130151, 130164, 14, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(166, 4, 15, 'A', 1, 130166, 130166, 1, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(167, 4, 15, 'A', 1, 130168, 130190, 23, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(168, 4, 15, 'A', 1, 130192, 130212, 21, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(169, 6, 18, 'A', 1, 13791, 13792, 2, '2025-12-31', NULL, 6.30, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(170, 6, 18, 'A', 1, 13794, 13891, 98, '2025-12-31', NULL, 308.70, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(171, 6, 18, 'A', 1, 13893, 13900, 8, '2025-12-31', NULL, 25.20, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(172, 6, 18, 'A', 1, 40301, 40302, 2, '2025-12-31', NULL, 6.30, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(173, 8, 20, 'B', 1, 953241, 954744, 1504, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(174, 8, 20, 'B', 1, 966153, 967489, 1337, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(175, 8, 20, 'C', 1, 918230, 919496, 1267, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(176, 8, 20, 'C', 1, 927558, 928060, 503, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(177, 8, 20, 'U', 1, 900323, 900326, 4, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(178, 9, 21, 'A', 1, 233542, 233564, 23, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(179, 4, 15, 'A', 2, 162683, 162720, 38, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(180, 4, 15, 'A', 2, 162722, 162731, 10, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(181, 4, 15, 'A', 2, 162733, 162748, 16, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(182, 4, 15, 'A', 2, 162750, 162860, 111, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(183, 6, 18, 'A', 2, 367868, 367871, 4, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(184, 8, 20, 'C', 2, 53303, 53634, 332, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(185, 9, 21, 'A', 2, 234306, 234308, 3, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(186, 4, 15, 'A', 3, 17600, 17613, 14, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(187, 4, 15, 'A', 3, 17615, 17684, 70, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(188, 4, 15, 'A', 3, 17686, 17709, 24, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(189, 4, 15, 'A', 3, 17713, 17719, 7, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(190, 4, 15, 'A', 3, 17721, 17725, 5, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(191, 4, 15, 'A', 3, 17727, 17780, 54, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(192, 4, 15, 'A', 3, 17782, 17784, 3, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(193, 4, 15, 'A', 3, 17786, 17811, 26, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(194, 4, 15, 'A', 3, 17813, 17888, 76, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(195, 8, 20, 'B', 3, 974158, 974618, 461, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(196, 9, 21, NULL, 3, 195331, 195333, 3, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(197, 2, 11, 'E', 4, 239041, 245535, 6495, '2025-12-31', NULL, 1623.75, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(198, 2, 13, 'I', 4, 591401, 592761, 1361, '2025-12-31', NULL, 1361.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(199, 15, 27, 'B', 4, 99585, 99665, 81, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(200, 4, 15, 'A', 4, 158144, 158163, 20, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(201, 4, 15, 'A', 4, 158165, 158235, 71, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(202, 4, 15, 'A', 4, 158237, 158241, 5, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(203, 4, 15, 'A', 4, 158243, 158292, 50, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(204, 4, 15, 'A', 4, 158294, 158296, 3, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(205, 4, 15, 'A', 4, 158298, 158306, 9, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(206, 4, 15, 'A', 4, 158308, 158308, 1, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(207, 4, 15, 'A', 4, 158310, 158331, 22, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(208, 4, 15, 'A', 4, 158333, 158337, 5, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(209, 4, 15, 'A', 4, 158339, 158422, 84, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(210, 4, 15, 'A', 4, 158424, 158445, 22, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(211, 4, 15, 'A', 4, 158447, 158473, 27, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(212, 4, 15, 'A', 4, 158475, 158494, 20, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(213, 5, 17, NULL, 4, 740487, 740492, 6, '2025-12-31', NULL, 12.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(214, 8, 20, 'C', 4, 50781, 51912, 1132, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(215, 9, 21, NULL, 4, 167704, 167707, 4, '2025-12-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(216, 1, 7, 'C', 1, 131881, 131893, 13, '2026-01-31', NULL, 44.59, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(217, 1, 7, 'C', 1, 131895, 131898, 4, '2026-01-31', NULL, 13.72, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(218, 1, 7, 'C', 1, 131900, 131905, 6, '2026-01-31', NULL, 20.58, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(219, 1, 7, 'C', 1, 131907, 131965, 59, '2026-01-31', NULL, 202.37, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(220, 2, 10, 'J', 1, 123429, 169144, 45716, '2026-01-31', NULL, 6857.40, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(221, 2, 12, 'D', 1, 111358, 119285, 7928, '2026-01-31', NULL, 3964.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(222, 2, 13, 'B', 1, 834438, 834522, 85, '2026-01-31', NULL, 85.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(223, 3, 14, 'A', 1, 15024, 15363, 340, '2026-01-31', NULL, 170.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(224, 4, 15, 'A', 1, 130213, 130244, 32, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(225, 4, 15, 'A', 1, 130247, 130365, 119, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(226, 4, 15, 'A', 1, 130367, 130413, 47, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(227, 4, 15, 'A', 1, 130415, 130429, 15, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(228, 4, 15, 'A', 1, 130431, 130460, 30, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(229, 4, 15, 'A', 1, 130463, 130466, 4, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(230, 4, 15, 'A', 1, 130468, 130473, 6, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(231, 4, 15, 'A', 1, 130475, 130491, 17, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(232, 4, 15, 'A', 1, 130493, 130560, 68, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(233, 4, 15, 'A', 1, 130562, 130629, 68, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(234, 4, 15, 'A', 1, 130631, 130637, 7, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(235, 4, 15, 'A', 1, 130639, 130643, 5, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(236, 4, 15, 'A', 1, 130646, 130742, 97, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(237, 4, 15, 'A', 1, 130744, 130757, 14, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(238, 4, 15, 'A', 1, 130759, 130760, 2, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(239, 4, 15, 'A', 1, 130762, 130773, 12, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(240, 4, 15, 'A', 1, 130775, 130787, 13, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(241, 4, 15, 'A', 1, 130789, 130801, 13, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(242, 4, 15, 'A', 1, 130803, 130822, 20, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(243, 4, 15, 'A', 1, 130824, 130826, 3, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(244, 4, 15, 'A', 1, 130828, 130830, 3, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(245, 4, 15, 'A', 1, 130832, 130842, 11, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(246, 4, 15, 'A', 1, 130844, 130855, 12, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(247, 4, 15, 'A', 1, 130857, 130928, 72, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(248, 4, 15, 'A', 1, 130930, 130949, 20, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(249, 4, 15, 'A', 1, 130951, 130963, 13, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(250, 4, 15, 'A', 1, 130965, 131038, 74, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(251, 4, 15, 'A', 1, 131040, 131067, 28, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(252, 4, 15, 'A', 1, 131069, 131080, 12, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(253, 4, 15, 'A', 1, 131082, 131098, 17, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(254, 4, 15, 'A', 1, 131100, 131163, 64, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(255, 4, 15, 'A', 1, 131165, 131174, 10, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(256, 4, 15, 'A', 1, 131176, 131200, 25, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(257, 4, 15, 'A', 1, 131202, 131268, 67, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(258, 4, 15, 'A', 1, 131270, 131270, 1, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(259, 4, 15, 'A', 1, 131273, 131279, 7, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(260, 4, 15, 'A', 1, 131281, 131281, 1, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(261, 4, 15, 'A', 1, 131283, 131295, 13, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(262, 4, 15, 'A', 1, 131297, 131298, 2, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(263, 4, 15, 'A', 1, 131301, 131369, 69, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(264, 4, 15, 'A', 1, 131371, 131376, 6, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(265, 4, 15, 'A', 1, 131378, 131391, 14, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(266, 4, 15, 'A', 1, 131393, 131426, 34, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(267, 4, 15, 'A', 1, 131428, 131480, 53, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(268, 4, 15, 'A', 1, 131482, 131539, 58, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(269, 4, 15, 'A', 1, 131541, 131547, 7, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(270, 4, 15, 'A', 1, 131549, 131551, 3, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(271, 4, 15, 'A', 1, 131553, 131554, 2, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(272, 4, 15, 'A', 1, 131556, 131640, 85, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(273, 4, 15, 'A', 1, 131642, 131653, 12, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(274, 4, 15, 'A', 1, 131655, 131682, 28, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(275, 4, 15, 'A', 1, 131684, 131684, 1, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(276, 4, 15, 'A', 1, 131686, 131717, 32, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(277, 4, 15, 'A', 1, 131719, 131749, 31, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(278, 6, 18, 'A', 1, 40303, 40353, 51, '2026-01-31', NULL, 160.65, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(279, 6, 18, 'A', 1, 40355, 40381, 27, '2026-01-31', NULL, 85.05, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(280, 6, 18, 'A', 1, 40383, 40386, 4, '2026-01-31', NULL, 12.60, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(281, 6, 18, 'A', 1, 40388, 40393, 6, '2026-01-31', NULL, 18.90, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(282, 6, 18, 'A', 1, 40395, 40585, 191, '2026-01-31', NULL, 601.65, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(283, 8, 20, 'B', 1, 954759, 956528, 1770, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(284, 8, 20, 'B', 1, 956530, 957500, 971, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(285, 8, 20, 'C', 1, 892501, 892513, 13, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(286, 8, 20, 'B', 1, 967490, 968273, 784, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(287, 8, 20, 'B', 1, 968275, 969667, 1393, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(288, 8, 20, 'B', 1, 969669, 970094, 426, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(289, 8, 20, 'C', 1, 919497, 921718, 2222, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(290, 8, 20, 'C', 1, 928061, 928290, 230, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(291, 9, 21, 'A', 1, 233565, 233597, 33, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(292, 1, 7, 'C', 2, 860887, 860887, 1, '2026-01-31', NULL, 3.43, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(293, 4, 15, 'A', 2, 162861, 162873, 13, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(294, 4, 15, 'A', 2, 162875, 162970, 96, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(295, 4, 15, 'A', 2, 162972, 162978, 7, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(296, 4, 15, 'A', 2, 162980, 163040, 61, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(297, 6, 18, 'A', 2, 367872, 367874, 3, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(298, 8, 20, 'C', 2, 53635, 53828, 194, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(299, 8, 20, 'C', 2, 53830, 53830, 1, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(300, 8, 20, 'C', 2, 53832, 54135, 304, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(301, 8, 20, 'C', 2, 54137, 54222, 86, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(302, 9, 21, 'A', 2, 234309, 234311, 3, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(303, 12, 24, 'A', 2, 98888, 98894, 7, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(304, 4, 15, 'A', 3, 17889, 17898, 10, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(305, 4, 15, 'A', 3, 17901, 17948, 48, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(306, 4, 15, 'A', 3, 17950, 17972, 23, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(307, 4, 15, 'A', 3, 17975, 17994, 20, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(308, 4, 15, 'A', 3, 17996, 18002, 7, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(309, 4, 15, 'A', 3, 18004, 18005, 2, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(310, 4, 15, 'A', 3, 18010, 18047, 38, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(311, 4, 15, 'A', 3, 18049, 18062, 14, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(312, 4, 15, 'A', 3, 18064, 18091, 28, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(313, 4, 15, 'A', 3, 18093, 18095, 3, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(314, 4, 15, 'A', 3, 18098, 18143, 46, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(315, 4, 15, 'A', 3, 18147, 18147, 1, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(316, 4, 15, 'A', 3, 18150, 18160, 11, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(317, 4, 15, 'A', 3, 18162, 18195, 34, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(318, 4, 15, 'A', 3, 18197, 18214, 18, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(319, 4, 15, 'A', 3, 18216, 18217, 2, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(320, 4, 15, 'A', 3, 18219, 18234, 16, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(321, 4, 15, 'A', 3, 18236, 18257, 22, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(322, 4, 15, 'A', 3, 18259, 18271, 13, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(323, 8, 20, 'B', 3, 974619, 975000, 382, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(324, 8, 20, 'C', 3, 930001, 930707, 707, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(325, 1, 7, 'C', 3, 555439, 555439, 1, '2026-01-31', NULL, 3.43, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(326, 1, 7, 'C', 4, 10988, 10993, 6, '2026-01-31', NULL, 20.58, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(327, 2, 11, 'E', 4, 245536, 251660, 6125, '2026-01-31', NULL, 1531.25, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(328, 2, 13, 'I', 4, 592762, 593875, 1114, '2026-01-31', NULL, 1114.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(329, 15, 27, 'B', 4, 99666, 99746, 81, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(330, 4, 15, 'A', 4, 158495, 158550, 56, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(331, 4, 15, 'A', 4, 158552, 158554, 3, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(332, 4, 15, 'A', 4, 158556, 158617, 62, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(333, 4, 15, 'A', 4, 158619, 158650, 32, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(334, 4, 15, 'A', 4, 158652, 158689, 38, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(335, 4, 15, 'A', 4, 158691, 158694, 4, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(336, 4, 15, 'A', 4, 158696, 158725, 30, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(337, 4, 15, 'A', 4, 158727, 158748, 22, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(338, 4, 15, 'A', 4, 158750, 158795, 46, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(339, 4, 15, 'A', 4, 158797, 158821, 25, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(340, 4, 15, 'A', 4, 158823, 158847, 25, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(341, 4, 15, 'A', 4, 158849, 158878, 30, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(342, 4, 15, 'A', 4, 158880, 158888, 9, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(343, 5, 17, NULL, 4, 740493, 740499, 7, '2026-01-31', NULL, 14.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(344, 8, 20, 'C', 4, 51913, 52500, 588, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(345, 8, 20, 'D', 4, 10001, 11655, 1655, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(346, 9, 21, NULL, 4, 167708, 167719, 12, '2026-01-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(347, 1, 3, 'C', 1, 38505, 38505, 1, '2026-02-28', NULL, 1.14, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(348, 1, 7, 'C', 1, 131966, 132126, 161, '2026-02-28', NULL, 552.23, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(349, 1, 7, 'C', 1, 132128, 132128, 1, '2026-02-28', NULL, 3.43, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(350, 1, 7, 'C', 1, 132130, 132141, 12, '2026-02-28', NULL, 41.16, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(351, 1, 7, 'C', 1, 132143, 132149, 7, '2026-02-28', NULL, 24.01, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(352, 1, 7, 'C', 1, 132151, 132182, 32, '2026-02-28', NULL, 109.76, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(353, 1, 7, 'C', 1, 132184, 132191, 8, '2026-02-28', NULL, 27.44, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(354, 1, 7, 'C', 1, 132193, 132212, 20, '2026-02-28', NULL, 68.60, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(355, 1, 7, 'C', 1, 132214, 132225, 12, '2026-02-28', NULL, 41.16, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(356, 1, 7, 'C', 1, 132227, 132231, 5, '2026-02-28', NULL, 17.15, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(357, 1, 7, 'C', 1, 132233, 132252, 20, '2026-02-28', NULL, 68.60, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(358, 1, 7, 'C', 1, 132255, 132267, 13, '2026-02-28', NULL, 44.59, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(359, 1, 7, 'C', 1, 132269, 132271, 3, '2026-02-28', NULL, 10.29, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(360, 1, 7, 'C', 1, 132273, 132280, 8, '2026-02-28', NULL, 27.44, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(361, 1, 7, 'C', 1, 132282, 132286, 5, '2026-02-28', NULL, 17.15, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(362, 1, 7, 'C', 1, 132288, 132289, 2, '2026-02-28', NULL, 6.86, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(363, 1, 7, 'C', 1, 132291, 132357, 67, '2026-02-28', NULL, 229.81, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(364, 1, 7, 'C', 1, 132359, 132375, 17, '2026-02-28', NULL, 58.31, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(365, 1, 7, 'C', 1, 132377, 132384, 8, '2026-02-28', NULL, 27.44, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(366, 1, 7, 'C', 1, 132386, 132393, 8, '2026-02-28', NULL, 27.44, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(367, 1, 7, 'C', 1, 132395, 132400, 6, '2026-02-28', NULL, 20.58, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(368, 1, 7, 'C', 1, 132402, 132406, 5, '2026-02-28', NULL, 17.15, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(369, 1, 7, 'C', 1, 132408, 132434, 27, '2026-02-28', NULL, 92.61, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(370, 1, 7, 'C', 1, 132436, 132446, 11, '2026-02-28', NULL, 37.73, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(371, 2, 10, 'J', 1, 169145, 209775, 40631, '2026-02-28', NULL, 6094.65, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(372, 2, 12, 'D', 1, 119286, 126495, 7210, '2026-02-28', NULL, 3605.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(373, 2, 13, 'B', 1, 834523, 834884, 362, '2026-02-28', NULL, 362.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(374, 3, 14, 'A', 1, 15364, 15644, 281, '2026-02-28', NULL, 140.50, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(375, 4, 15, 'A', 1, 131751, 131831, 81, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(376, 4, 15, 'A', 1, 131833, 131929, 97, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(377, 4, 15, 'A', 1, 131932, 131954, 23, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(378, 4, 15, 'A', 1, 131956, 131961, 6, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(379, 4, 15, 'A', 1, 131964, 131965, 2, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(380, 4, 15, 'A', 1, 131967, 131981, 15, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(381, 4, 15, 'A', 1, 131983, 131989, 7, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(382, 4, 15, 'A', 1, 131991, 131991, 1, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(383, 4, 15, 'A', 1, 131993, 132042, 50, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(384, 4, 15, 'A', 1, 132044, 132057, 14, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(385, 4, 15, 'A', 1, 132059, 132071, 13, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(386, 4, 15, 'A', 1, 132073, 132137, 65, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(387, 4, 15, 'A', 1, 132139, 132153, 15, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(388, 4, 15, 'A', 1, 132155, 132162, 8, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(389, 4, 15, 'A', 1, 132164, 132171, 8, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(390, 4, 15, 'A', 1, 132173, 132178, 6, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(391, 4, 15, 'A', 1, 132180, 132185, 6, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(392, 4, 15, 'A', 1, 132187, 132187, 1, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(393, 4, 15, 'A', 1, 132189, 132202, 14, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(394, 4, 15, 'A', 1, 132206, 132254, 49, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(395, 4, 15, 'A', 1, 132256, 132280, 25, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(396, 4, 15, 'A', 1, 132283, 132304, 22, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(397, 4, 15, 'A', 1, 132306, 132330, 25, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(398, 4, 15, 'A', 1, 132332, 132371, 40, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(399, 4, 15, 'A', 1, 132373, 132394, 22, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(400, 4, 15, 'A', 1, 132396, 132404, 9, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(401, 4, 15, 'A', 1, 132406, 132501, 96, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(402, 4, 15, 'A', 1, 132503, 132554, 52, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(403, 4, 15, 'A', 1, 132556, 132573, 18, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(404, 4, 15, 'A', 1, 132575, 132579, 5, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(405, 4, 15, 'A', 1, 132581, 132622, 42, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(406, 4, 15, 'A', 1, 132624, 132668, 45, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(407, 4, 15, 'A', 1, 132670, 132689, 20, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(408, 4, 15, 'A', 1, 132692, 132738, 47, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(409, 4, 15, 'A', 1, 132740, 132763, 24, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(410, 4, 15, 'A', 1, 132765, 132770, 6, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(411, 4, 15, 'A', 1, 132772, 132774, 3, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(412, 4, 15, 'A', 1, 132776, 132795, 20, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(413, 4, 15, 'A', 1, 132797, 132870, 74, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(414, 4, 15, 'A', 1, 132872, 132877, 6, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(415, 4, 15, 'A', 1, 132881, 132944, 64, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(416, 4, 15, 'A', 1, 132946, 132963, 18, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(417, 4, 15, 'A', 1, 132965, 132989, 25, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(418, 4, 15, 'A', 1, 132991, 133013, 23, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(419, 4, 15, 'A', 1, 133015, 133065, 51, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(420, 4, 15, 'A', 1, 133067, 133141, 75, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(421, 4, 15, 'A', 1, 133143, 133164, 22, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(422, 4, 15, 'A', 1, 133166, 133218, 53, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(423, 4, 15, 'A', 1, 133221, 133222, 2, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(424, 4, 15, 'A', 1, 133224, 133263, 40, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(425, 4, 15, 'A', 1, 133265, 133275, 11, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(426, 6, 18, 'A', 1, 40586, 40669, 84, '2026-02-28', NULL, 264.60, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(427, 6, 18, 'A', 1, 40671, 40677, 7, '2026-02-28', NULL, 22.05, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(428, 6, 18, 'A', 1, 40679, 40694, 16, '2026-02-28', NULL, 50.40, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(429, 6, 18, 'A', 1, 40696, 40730, 35, '2026-02-28', NULL, 110.25, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(430, 8, 20, 'C', 1, 892514, 894570, 2057, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(431, 8, 20, 'B', 1, 970095, 971755, 1661, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(432, 8, 20, 'C', 1, 921719, 923778, 2060, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(433, 8, 20, 'C', 1, 928291, 928522, 232, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(434, 9, 21, 'A', 1, 233598, 233611, 14, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(435, 1, 7, 'C', 2, 860888, 860894, 7, '2026-02-28', NULL, 24.01, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(436, 1, 7, 'C', 2, 860896, 860897, 2, '2026-02-28', NULL, 6.86, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53');
INSERT INTO `realizaciones` (`id`, `tipo_especie_id`, `denominacion_id`, `serie`, `distrito_id`, `numero_inicio`, `numero_fin`, `cantidad`, `fecha`, `nombre_contribuyente`, `monto_cobrado`, `usuario_id`, `created_at`, `updated_at`) VALUES
(437, 1, 7, 'C', 2, 860899, 860899, 1, '2026-02-28', NULL, 3.43, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(438, 4, 15, 'A', 2, 163041, 163140, 100, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(439, 4, 15, 'A', 2, 163142, 163171, 30, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(440, 4, 15, 'A', 2, 163173, 163195, 23, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(441, 4, 15, 'A', 2, 163197, 163207, 11, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(442, 4, 15, 'A', 2, 163209, 163209, 1, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(443, 4, 15, 'A', 2, 163211, 163241, 31, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(444, 6, 18, 'A', 2, 367875, 367877, 3, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(445, 8, 20, 'C', 2, 54223, 54291, 69, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(446, 8, 20, 'C', 2, 54295, 54663, 369, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(447, 8, 20, 'C', 2, 54665, 54707, 43, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(448, 8, 20, 'C', 2, 54709, 54714, 6, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(449, 8, 20, 'C', 2, 54718, 54718, 1, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(450, 9, 21, 'A', 2, 234312, 234315, 4, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(451, 12, 24, 'A', 2, 98895, 98898, 4, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(452, 4, 15, 'A', 3, 18272, 18290, 19, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(453, 4, 15, 'A', 3, 18292, 18292, 1, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(454, 4, 15, 'A', 3, 18294, 18313, 20, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(455, 4, 15, 'A', 3, 18321, 18380, 60, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(456, 4, 15, 'A', 3, 18382, 18389, 8, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(457, 4, 15, 'A', 3, 18391, 18401, 11, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(458, 4, 15, 'A', 3, 18403, 18417, 15, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(459, 4, 15, 'A', 3, 18420, 18423, 4, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(460, 4, 15, 'A', 3, 18426, 18435, 10, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(461, 4, 15, 'A', 3, 18437, 18460, 24, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(462, 4, 15, 'A', 3, 18462, 18482, 21, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(463, 4, 15, 'A', 3, 18484, 18492, 9, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(464, 8, 20, 'C', 3, 930708, 931337, 630, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(465, 9, 21, NULL, 3, 195334, 195334, 1, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(466, 1, 7, 'C', 3, 555440, 555440, 1, '2026-02-28', NULL, 3.43, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(467, 1, 7, 'C', 4, 10994, 11000, 7, '2026-02-28', NULL, 24.01, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(468, 1, 7, 'C', 4, 85701, 85702, 2, '2026-02-28', NULL, 6.86, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(469, 1, 7, 'C', 4, 85704, 85706, 3, '2026-02-28', NULL, 10.29, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(470, 2, 11, 'E', 4, 251661, 257300, 5640, '2026-02-28', NULL, 1410.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(471, 2, 13, 'I', 4, 593876, 595043, 1168, '2026-02-28', NULL, 1168.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(472, 15, 27, 'B', 4, 99747, 99827, 81, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(473, 4, 15, 'A', 4, 158889, 158889, 1, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(474, 4, 15, 'A', 4, 158891, 158892, 2, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(475, 4, 15, 'A', 4, 158894, 158906, 13, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(476, 4, 15, 'A', 4, 158908, 159150, 243, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(477, 4, 15, 'A', 4, 159152, 159278, 127, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(478, 4, 15, 'A', 4, 159280, 159312, 33, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(479, 4, 15, 'A', 4, 159314, 159318, 5, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(480, 4, 15, 'A', 4, 159320, 159351, 32, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(481, 4, 15, 'A', 4, 159354, 159377, 24, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(482, 4, 15, 'A', 4, 159379, 159403, 25, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(483, 8, 20, 'D', 4, 11656, 13591, 1936, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(484, 9, 21, NULL, 4, 167720, 167728, 9, '2026-02-28', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(485, 1, 7, 'C', 1, 132447, 132457, 11, '2026-03-31', NULL, 37.73, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(486, 1, 7, 'C', 1, 132459, 132465, 7, '2026-03-31', NULL, 24.01, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(487, 1, 7, 'C', 1, 132467, 132471, 5, '2026-03-31', NULL, 17.15, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(488, 1, 7, 'C', 1, 132473, 132481, 9, '2026-03-31', NULL, 30.87, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(489, 1, 7, 'C', 1, 132483, 132485, 3, '2026-03-31', NULL, 10.29, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(490, 1, 7, 'C', 1, 132487, 132502, 16, '2026-03-31', NULL, 54.88, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(491, 1, 7, 'C', 1, 132504, 132555, 52, '2026-03-31', NULL, 178.36, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(492, 1, 7, 'C', 1, 132557, 132557, 1, '2026-03-31', NULL, 3.43, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(493, 1, 7, 'C', 1, 132559, 132586, 28, '2026-03-31', NULL, 96.04, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(494, 1, 7, 'C', 1, 132588, 132631, 44, '2026-03-31', NULL, 150.92, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(495, 2, 10, 'J', 1, 209776, 249813, 40038, '2026-03-31', NULL, 6005.70, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(496, 2, 12, 'D', 1, 126496, 134170, 7675, '2026-03-31', NULL, 3837.50, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(497, 2, 13, 'B', 1, 834885, 835234, 350, '2026-03-31', NULL, 350.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(498, 3, 14, 'A', 1, 14769, 14769, 1, '2026-03-31', NULL, 0.50, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(499, 3, 14, 'A', 1, 15645, 15890, 246, '2026-03-31', NULL, 123.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(500, 4, 15, 'A', 1, 133276, 133316, 41, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(501, 4, 15, 'A', 1, 133318, 133359, 42, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(502, 4, 15, 'A', 1, 133361, 133452, 92, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(503, 4, 15, 'A', 1, 133454, 133476, 23, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(504, 4, 15, 'A', 1, 133479, 133483, 5, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(505, 4, 15, 'A', 1, 133486, 133489, 4, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(506, 4, 15, 'A', 1, 133493, 133497, 5, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(507, 4, 15, 'A', 1, 133499, 133544, 46, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(508, 4, 15, 'A', 1, 133546, 133550, 5, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(509, 4, 15, 'A', 1, 133552, 133554, 3, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(510, 4, 15, 'A', 1, 133558, 133576, 19, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(511, 4, 15, 'A', 1, 133579, 133620, 42, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(512, 4, 15, 'A', 1, 133622, 133622, 1, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(513, 4, 15, 'A', 1, 133624, 133643, 20, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(514, 4, 15, 'A', 1, 133645, 133647, 3, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(515, 4, 15, 'A', 1, 133649, 133663, 15, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(516, 4, 15, 'A', 1, 133665, 133669, 5, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(517, 4, 15, 'A', 1, 133671, 133698, 28, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(518, 4, 15, 'A', 1, 133700, 133750, 51, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(519, 4, 15, 'A', 1, 133752, 133817, 66, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(520, 4, 15, 'A', 1, 133820, 133821, 2, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(521, 4, 15, 'A', 1, 133823, 133847, 25, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(522, 4, 15, 'A', 1, 133849, 133860, 12, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(523, 4, 15, 'A', 1, 133862, 133875, 14, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(524, 4, 15, 'A', 1, 133877, 133880, 4, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(525, 4, 15, 'A', 1, 133882, 133905, 24, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(526, 4, 15, 'A', 1, 133907, 133912, 6, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(527, 4, 15, 'A', 1, 133914, 133918, 5, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(528, 4, 15, 'A', 1, 133920, 133933, 14, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(529, 4, 15, 'A', 1, 133935, 133950, 16, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(530, 4, 15, 'A', 1, 133952, 133952, 1, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(531, 4, 15, 'A', 1, 133954, 133970, 17, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(532, 4, 15, 'A', 1, 133977, 133998, 22, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(533, 4, 15, 'A', 1, 134000, 134010, 11, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(534, 4, 15, 'A', 1, 134013, 134045, 33, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(535, 4, 15, 'A', 1, 134047, 134086, 40, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(536, 4, 15, 'A', 1, 134088, 134091, 4, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(537, 4, 15, 'A', 1, 134093, 134105, 13, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(538, 4, 15, 'A', 1, 134107, 134114, 8, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(539, 4, 15, 'A', 1, 134117, 134118, 2, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(540, 4, 15, 'A', 1, 134120, 134148, 29, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(541, 4, 15, 'A', 1, 134150, 134192, 43, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(542, 4, 15, 'A', 1, 134194, 134197, 4, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(543, 4, 15, 'A', 1, 134199, 134220, 22, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(544, 4, 15, 'A', 1, 134222, 134223, 2, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(545, 4, 15, 'A', 1, 134225, 134290, 66, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(546, 4, 15, 'A', 1, 134292, 134312, 21, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(547, 4, 15, 'A', 1, 134314, 134338, 25, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(548, 4, 15, 'A', 1, 134340, 134367, 28, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(549, 4, 15, 'A', 1, 134369, 134392, 24, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(550, 4, 15, 'A', 1, 134394, 134428, 35, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(551, 6, 18, 'A', 1, 40731, 40750, 20, '2026-03-31', NULL, 63.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(552, 6, 18, 'A', 1, 40752, 40825, 74, '2026-03-31', NULL, 233.10, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(553, 8, 20, 'B', 1, 954745, 954745, 1, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(554, 8, 20, 'C', 1, 894571, 896578, 2008, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(555, 8, 20, 'B', 1, 971756, 972500, 745, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(556, 8, 20, 'C', 1, 905001, 905717, 717, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(557, 8, 20, 'C', 1, 923779, 925373, 1595, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(558, 8, 20, 'C', 1, 928523, 928905, 383, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(559, 8, 20, 'U', 1, 900327, 900327, 1, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(560, 9, 21, 'A', 1, 233612, 233632, 21, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(561, 4, 15, 'A', 2, 163242, 163260, 19, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(562, 4, 15, 'A', 2, 163262, 163408, 147, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(563, 6, 18, 'A', 2, 367878, 367881, 4, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(564, 9, 21, 'A', 2, 234316, 234320, 5, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(565, 12, 24, 'A', 2, 98899, 98902, 4, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(566, 4, 15, 'A', 3, 18493, 18531, 39, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(567, 4, 15, 'A', 3, 18533, 18542, 10, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(568, 4, 15, 'A', 3, 18544, 18569, 26, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(569, 4, 15, 'A', 3, 18571, 18591, 21, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(570, 4, 15, 'A', 3, 18594, 18625, 32, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(571, 4, 15, 'A', 3, 18627, 18649, 23, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(572, 4, 15, 'A', 3, 18651, 18656, 6, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(573, 4, 15, 'A', 3, 18658, 18722, 65, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(574, 8, 20, 'C', 3, 931338, 931856, 519, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(575, 1, 7, 'C', 3, 555441, 555442, 2, '2026-03-31', NULL, 6.86, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(576, 1, 7, 'C', 4, 85707, 85709, 3, '2026-03-31', NULL, 10.29, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(577, 1, 7, 'C', 4, 85711, 85726, 16, '2026-03-31', NULL, 54.88, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(578, 1, 7, 'C', 4, 85728, 85730, 3, '2026-03-31', NULL, 10.29, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(579, 2, 11, 'E', 4, 257301, 263210, 5910, '2026-03-31', NULL, 1477.50, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(580, 2, 13, 'I', 4, 595044, 596386, 1343, '2026-03-31', NULL, 1343.00, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54'),
(581, 15, 27, 'B', 4, 99828, 99908, 81, '2026-03-31', NULL, 0.00, 1, '2026-09-30 06:43:54', '2026-09-30 06:43:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2026-05-12 10:05:57', '2026-05-12 10:05:57'),
(2, 'empleado', 'web', '2026-05-12 10:05:57', '2026-05-12 10:05:57'),
(3, 'visitante', 'web', '2026-07-14 04:48:08', '2026-07-14 04:48:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('79DYSBiOtFMaIlU4eiBDZkl9VuVNgQC6SUL6VldG', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'eyJfdG9rZW4iOiJ3OGo1VFFLOEZpWkhnZXNVMFpzZjhNYzd2dzNVN1JGUEJNTUtXUGt4IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL3Byb3llY3Rvc2VydmljaW9zb2NpYWwudGVzdFwvbG9naW4iLCJyb3V0ZSI6ImxvZ2luIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1790751864),
('Z3kmAHiLhrxqFAfm0DvfvgLtmXmVvIJ1kf4Phbe5', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'eyJfdG9rZW4iOiJNcWdkWWRPV3B2WDR0OGk4RTVpRnRzTXNkTTlSU0xmOVB6dzdxU1BuIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL3Byb3llY3Rvc2VydmljaW9zb2NpYWwudGVzdFwvYWRtaW5cL2VzcGVjaWVzXC9hamF4XC9yZWFsaXphY2lvbi1pbmZvP2Rpc3RyaXRvX2lkPTEmdGlwb19lc3BlY2llX2lkPTQiLCJyb3V0ZSI6ImFkbWluLmVzcGVjaWVzLmFqYXgucmVhbGl6YWNpb24taW5mbyJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxfQ==', 1791264398);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_especies`
--

CREATE TABLE `tipo_especies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `unidades` tinyint(1) NOT NULL DEFAULT 0,
  `orden` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_especies`
--

INSERT INTO `tipo_especies` (`id`, `nombre`, `descripcion`, `unidades`, `orden`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Fondo Vialidad', NULL, 0, 1, 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(2, 'Tiquetes de Mercado', NULL, 0, 2, 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(3, 'Tiquetes Servicio de Báscula', NULL, 0, 3, 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(4, 'Cartas de Venta Continua', NULL, 1, 5, 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(5, 'Carnet de Menores', 'Ya no se realizan en Metapán', 0, 7, 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(6, 'Tarjetas para Carnet de Identificación', NULL, 0, 6, 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(7, 'Fórmulas 1-ISAM Plana', NULL, 1, 8, 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(8, 'Fórmulas 1-ISAM Continua', NULL, 1, 9, 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(9, 'Títulos a Perpetuidad', NULL, 0, 10, 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(10, 'Guías de Conducción de Ganado', NULL, 1, 11, 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(11, 'Boletos de Cobro de Material Pétreo', NULL, 0, 12, 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(12, 'Tarjetas de Inmuebles', NULL, 0, 13, 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(13, 'Tarjetas de Empresa', NULL, 0, 14, 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(14, 'Avisos de Cobro Común', NULL, 0, 15, 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(15, 'Tarjetas de Control de Mercado', NULL, 1, 16, 1, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(16, 'Caja Auxiliar', NULL, 0, 17, 0, '2026-07-10 04:30:24', '2026-09-30 06:43:52'),
(17, 'Cartas de Venta', 'Cartas de venta manuales, ya no se realizan', 1, 4, 0, '2026-09-30 06:35:45', '2026-09-30 06:43:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `traslados`
--

CREATE TABLE `traslados` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tipo` varchar(20) NOT NULL DEFAULT 'bodega_distrito',
  `distrito_id` bigint(20) UNSIGNED DEFAULT NULL,
  `origen_distrito_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fecha` date NOT NULL,
  `observaciones` text DEFAULT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `traslados`
--

INSERT INTO `traslados` (`id`, `tipo`, `distrito_id`, `origen_distrito_id`, `fecha`, `observaciones`, `usuario_id`, `created_at`, `updated_at`) VALUES
(1, 'bodega_distrito', 1, NULL, '2025-10-31', 'Entrega de la factura N° SALDO-INICIAL', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(2, 'bodega_distrito', 2, NULL, '2025-10-31', 'Entrega de la factura N° SALDO-INICIAL', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(3, 'bodega_distrito', 3, NULL, '2025-10-31', 'Entrega de la factura N° SALDO-INICIAL', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(4, 'bodega_distrito', 4, NULL, '2025-10-31', 'Entrega de la factura N° SALDO-INICIAL', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(5, 'bodega_distrito', 1, NULL, '2025-12-12', 'Entrega de la factura N° 06383', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(6, 'bodega_distrito', 1, NULL, '2026-03-10', 'Entrega de la factura N° 06585', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(7, 'bodega_distrito', 1, NULL, '2026-03-23', 'Entrega de la factura N° 06585', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(8, 'bodega_distrito', 1, NULL, '2026-03-10', 'Entrega de la factura N° 06589', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(9, 'bodega_distrito', 2, NULL, '2026-03-10', 'Entrega de la factura N° 06586', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(10, 'bodega_distrito', 3, NULL, '2026-03-10', 'Entrega de la factura N° 06588', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(11, 'bodega_distrito', 4, NULL, '2026-03-10', 'Entrega de la factura N° 06587', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(12, 'bodega_distrito', 2, NULL, '2026-03-23', 'Entrega de la factura N° 06619', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(13, 'bodega_distrito', 3, NULL, '2026-03-23', 'Entrega de la factura N° 06621', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(14, 'distrito_distrito', 2, 1, '2026-03-01', 'Traslado del 27/02/2026, registrado en el libro de marzo de 2026', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `traslado_detalles`
--

CREATE TABLE `traslado_detalles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `traslado_id` bigint(20) UNSIGNED NOT NULL,
  `lote_id` bigint(20) UNSIGNED NOT NULL,
  `numero_inicio` int(10) UNSIGNED NOT NULL,
  `numero_fin` int(10) UNSIGNED NOT NULL,
  `cantidad` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `traslado_detalles`
--

INSERT INTO `traslado_detalles` (`id`, `traslado_id`, `lote_id`, `numero_inicio`, `numero_fin`, `cantidad`) VALUES
(1, 1, 1, 33696, 34000, 305),
(2, 1, 2, 38505, 38550, 46),
(3, 1, 3, 131657, 133300, 1644),
(4, 1, 4, 765249, 800000, 34752),
(5, 1, 5, 840001, 980000, 140000),
(6, 1, 6, 37560, 110000, 72441),
(7, 1, 7, 110001, 310000, 200000),
(8, 1, 8, 95695, 140000, 44306),
(9, 1, 9, 320001, 340000, 20000),
(10, 1, 10, 833680, 840000, 6321),
(11, 1, 11, 14501, 20000, 5500),
(12, 1, 12, 28174, 30300, 2127),
(13, 1, 13, 130151, 140150, 10000),
(14, 1, 14, 13600, 13900, 301),
(15, 1, 15, 48387, 48600, 214),
(16, 1, 16, 950678, 957500, 6823),
(17, 1, 17, 892501, 905000, 12500),
(18, 1, 18, 964381, 972500, 8120),
(19, 1, 19, 905001, 915000, 10000),
(20, 1, 20, 916828, 930000, 13173),
(21, 1, 21, 900236, 900238, 3),
(22, 1, 22, 900269, 900271, 3),
(23, 1, 23, 900273, 900276, 4),
(24, 1, 24, 900279, 900279, 1),
(25, 1, 25, 900310, 900320, 11),
(26, 1, 26, 900323, 902400, 2078),
(27, 1, 27, 977043, 977500, 458),
(28, 1, 28, 233524, 233650, 127),
(29, 1, 29, 242101, 242200, 100),
(30, 2, 30, 38101, 38300, 200),
(31, 2, 31, 860886, 860900, 15),
(32, 2, 32, 999208, 999300, 93),
(33, 2, 33, 133801, 133900, 100),
(34, 2, 34, 227601, 227700, 100),
(35, 2, 35, 801102, 801200, 99),
(36, 2, 36, 162651, 164150, 1500),
(37, 2, 37, 367862, 367900, 39),
(38, 2, 38, 16401, 16500, 100),
(39, 2, 39, 31401, 31500, 100),
(40, 2, 40, 139339, 139500, 162),
(41, 2, 41, 52939, 55000, 2062),
(42, 2, 42, 25001, 30000, 5000),
(43, 2, 43, 234301, 234400, 100),
(44, 2, 44, 242601, 242650, 50),
(45, 2, 45, 1851, 2000, 150),
(46, 2, 46, 98887, 98904, 18),
(47, 2, 47, 21601, 21700, 100),
(48, 2, 48, 29451, 29550, 100),
(49, 2, 49, 24501, 24600, 100),
(50, 3, 50, 719001, 720000, 1000),
(51, 3, 51, 840201, 841000, 800),
(52, 3, 52, 841019, 844000, 2982),
(53, 3, 53, 844141, 850000, 5860),
(54, 3, 54, 17233, 21350, 4118),
(55, 3, 55, 140151, 143900, 3750),
(56, 3, 56, 973453, 975000, 1548),
(57, 3, 57, 930001, 937500, 7500),
(58, 3, 58, 195320, 195350, 31),
(59, 3, 59, 195400, 195400, 1),
(60, 3, 60, 233901, 234000, 100),
(61, 3, 61, 242201, 242250, 50),
(62, 3, 62, 555439, 555500, 62),
(63, 3, 63, 763880, 763900, 21),
(64, 3, 64, 227301, 227400, 100),
(65, 4, 65, 10988, 11000, 13),
(66, 4, 66, 85601, 85800, 200),
(67, 4, 67, 133301, 133800, 500),
(68, 4, 68, 232833, 290000, 57168),
(69, 4, 69, 590140, 600000, 9861),
(70, 4, 70, 910001, 920000, 10000),
(71, 4, 71, 99463, 99962, 500),
(72, 4, 72, 157651, 162650, 5000),
(73, 4, 73, 740473, 740499, 27),
(74, 4, 74, 782001, 782100, 100),
(75, 4, 75, 798701, 798800, 100),
(76, 4, 76, 495499, 495600, 102),
(77, 4, 77, 529981, 530000, 20),
(78, 4, 78, 475799, 480000, 4202),
(79, 4, 79, 48947, 52500, 3554),
(80, 4, 80, 10001, 25000, 15000),
(81, 4, 81, 167688, 167800, 113),
(82, 4, 82, 219601, 219700, 100),
(83, 4, 83, 234001, 234300, 300),
(84, 5, 84, 40301, 41300, 1000),
(85, 5, 85, 430001, 437500, 7500),
(86, 6, 86, 257801, 260800, 3000),
(87, 6, 87, 1, 260000, 260000),
(88, 6, 88, 980001, 1000000, 20000),
(89, 6, 89, 580001, 650000, 70000),
(90, 6, 90, 299511, 309500, 9990),
(91, 6, 91, 265651, 265660, 10),
(92, 6, 92, 66201, 66700, 500),
(93, 6, 93, 145001, 150000, 5000),
(94, 6, 94, 249651, 249800, 150),
(95, 7, 95, 237501, 287500, 50000),
(96, 8, 96, 45001, 49650, 4650),
(97, 9, 97, 309501, 314000, 4500),
(98, 9, 98, 66701, 66800, 100),
(99, 9, 99, 150001, 155000, 5000),
(100, 9, 100, 249801, 249850, 50),
(101, 9, 101, 44501, 44800, 300),
(102, 10, 102, 317751, 320750, 3000),
(103, 10, 103, 160001, 165000, 5000),
(104, 10, 104, 44801, 45000, 200),
(105, 10, 105, 261101, 261200, 100),
(106, 11, 106, 260801, 261100, 300),
(107, 11, 107, 700001, 780000, 80000),
(108, 11, 108, 240001, 250000, 10000),
(109, 11, 109, 68701, 69200, 500),
(110, 12, 110, 217501, 227500, 10000),
(111, 13, 111, 235001, 237500, 2500),
(112, 14, 3, 133201, 133300, 100);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicaciones`
--

CREATE TABLE `ubicaciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `distrito_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `orden` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ubicaciones`
--

INSERT INTO `ubicaciones` (`id`, `distrito_id`, `nombre`, `orden`, `created_at`, `updated_at`) VALUES
(1, 1, 'CAJA 1', 1, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(2, 1, 'CAJA 2', 2, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(3, 1, 'RASTRO', 3, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(4, 1, 'AUXILIAR', 4, '2026-09-30 06:43:52', '2026-09-30 06:43:52'),
(5, 1, 'C. DOMICILIO', 5, '2026-09-30 06:43:52', '2026-09-30 06:43:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicacion_rangos`
--

CREATE TABLE `ubicacion_rangos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ubicacion_id` bigint(20) UNSIGNED NOT NULL,
  `tipo_especie_id` bigint(20) UNSIGNED NOT NULL,
  `serie` varchar(10) DEFAULT NULL,
  `numero_inicio` int(10) UNSIGNED NOT NULL,
  `numero_fin` int(10) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ubicacion_rangos`
--

INSERT INTO `ubicacion_rangos` (`id`, `ubicacion_id`, `tipo_especie_id`, `serie`, `numero_inicio`, `numero_fin`, `fecha`, `usuario_id`, `created_at`, `updated_at`) VALUES
(1, 1, 8, 'B', 950678, 957500, '2025-10-31', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(2, 1, 8, 'C', 892501, 905000, '2025-10-31', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(3, 2, 8, 'B', 964381, 972500, '2025-10-31', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(4, 2, 8, 'C', 905001, 915000, '2025-10-31', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(5, 3, 8, 'C', 916828, 930000, '2025-10-31', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(6, 4, 8, 'U', 900236, 900238, '2025-10-31', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(7, 4, 8, 'U', 900269, 900271, '2025-10-31', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(8, 4, 8, 'U', 900273, 900276, '2025-10-31', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(9, 4, 8, 'U', 900279, 900279, '2025-10-31', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(10, 4, 8, 'U', 900310, 900320, '2025-10-31', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(11, 4, 8, 'U', 900323, 902400, '2025-10-31', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(12, 5, 8, 'B', 977043, 977500, '2025-10-31', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(13, 5, 8, 'C', 927501, 927557, '2025-11-30', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(14, 5, 8, 'C', 927558, 930000, '2025-11-30', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(15, 5, 8, 'D', 430001, 437500, '2025-12-31', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(16, 1, 8, 'E', 237501, 255000, '2026-03-31', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(17, 2, 8, 'E', 255001, 272500, '2026-03-31', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(18, 3, 8, 'E', 272501, 287500, '2026-03-31', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53'),
(19, 5, 8, 'E', 145001, 150000, '2026-03-31', 1, '2026-09-30 06:43:53', '2026-09-30 06:43:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `usuario`, `password`, `rol`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$12$0Q8n4o4bwie9wiRX7eHSJOZW1tuzd49yjsO/7pPIXZrPWwSCKNIgi', 'admin', NULL, '2026-05-12 10:05:57', '2026-05-12 10:05:57'),
(2, 'empleado', '$2y$12$WWpjdGax.74Zu5aep2dpEefA.1sXZMVde/e32nEW2a9XuiYAfwfXi', 'empleado', NULL, '2026-05-12 10:05:58', '2026-07-14 04:59:59'),
(9, 'erick', '$2y$12$/sk1iHR/vhqkaI/UQXyYteFmNNTJd6Q5EqGtEYdOgZQsnHoM3RXBa', 'admin', NULL, '2026-05-27 01:59:40', '2026-07-14 05:07:14');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `anios`
--
ALTER TABLE `anios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `anios_anio_unique` (`anio`);

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `compras_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `consultas`
--
ALTER TABLE `consultas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `denominaciones`
--
ALTER TABLE `denominaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `denominaciones_tipo_especie_id_foreign` (`tipo_especie_id`);

--
-- Indices de la tabla `distritos`
--
ALTER TABLE `distritos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `distritos_codigo_unique` (`codigo`);

--
-- Indices de la tabla `distrito_denominacion`
--
ALTER TABLE `distrito_denominacion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `distrito_denominacion_distrito_id_denominacion_id_unique` (`distrito_id`,`denominacion_id`),
  ADD KEY `distrito_denominacion_denominacion_id_foreign` (`denominacion_id`);

--
-- Indices de la tabla `distrito_tipo_especie`
--
ALTER TABLE `distrito_tipo_especie`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `distrito_tipo_especie_distrito_id_tipo_especie_id_unique` (`distrito_id`,`tipo_especie_id`),
  ADD KEY `distrito_tipo_especie_tipo_especie_id_foreign` (`tipo_especie_id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `lotes`
--
ALTER TABLE `lotes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lotes_compra_id_foreign` (`compra_id`),
  ADD KEY `lotes_tipo_especie_id_foreign` (`tipo_especie_id`),
  ADD KEY `lotes_denominacion_id_foreign` (`denominacion_id`);

--
-- Indices de la tabla `lote_rangos`
--
ALTER TABLE `lote_rangos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lote_rangos_lote_id_foreign` (`lote_id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `nulas`
--
ALTER TABLE `nulas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nulas_traslado_detalle_id_foreign` (`traslado_detalle_id`),
  ADD KEY `nulas_distrito_id_foreign` (`distrito_id`),
  ADD KEY `nulas_usuario_id_foreign` (`usuario_id`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `realizaciones`
--
ALTER TABLE `realizaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `realizaciones_denominacion_id_foreign` (`denominacion_id`),
  ADD KEY `realizaciones_distrito_id_foreign` (`distrito_id`),
  ADD KEY `realizaciones_usuario_id_foreign` (`usuario_id`),
  ADD KEY `realizaciones_tipo_especie_id_serie_numero_inicio_index` (`tipo_especie_id`,`serie`,`numero_inicio`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `tipo_especies`
--
ALTER TABLE `tipo_especies`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `traslados`
--
ALTER TABLE `traslados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `traslados_distrito_id_foreign` (`distrito_id`),
  ADD KEY `traslados_usuario_id_foreign` (`usuario_id`),
  ADD KEY `traslados_origen_distrito_id_foreign` (`origen_distrito_id`);

--
-- Indices de la tabla `traslado_detalles`
--
ALTER TABLE `traslado_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `traslado_detalles_traslado_id_foreign` (`traslado_id`),
  ADD KEY `traslado_detalles_lote_id_foreign` (`lote_id`);

--
-- Indices de la tabla `ubicaciones`
--
ALTER TABLE `ubicaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ubicaciones_distrito_id_nombre_unique` (`distrito_id`,`nombre`);

--
-- Indices de la tabla `ubicacion_rangos`
--
ALTER TABLE `ubicacion_rangos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ubicacion_rangos_ubicacion_id_foreign` (`ubicacion_id`),
  ADD KEY `ubicacion_rangos_usuario_id_foreign` (`usuario_id`),
  ADD KEY `ubicacion_rangos_tipo_especie_id_serie_numero_inicio_index` (`tipo_especie_id`,`serie`,`numero_inicio`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_usuario_unique` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `anios`
--
ALTER TABLE `anios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT de la tabla `consultas`
--
ALTER TABLE `consultas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `denominaciones`
--
ALTER TABLE `denominaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `distritos`
--
ALTER TABLE `distritos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `distrito_denominacion`
--
ALTER TABLE `distrito_denominacion`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `distrito_tipo_especie`
--
ALTER TABLE `distrito_tipo_especie`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lotes`
--
ALTER TABLE `lotes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT de la tabla `lote_rangos`
--
ALTER TABLE `lote_rangos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `nulas`
--
ALTER TABLE `nulas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=492;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `realizaciones`
--
ALTER TABLE `realizaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=661;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tipo_especies`
--
ALTER TABLE `tipo_especies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `traslados`
--
ALTER TABLE `traslados`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT de la tabla `traslado_detalles`
--
ALTER TABLE `traslado_detalles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT de la tabla `ubicaciones`
--
ALTER TABLE `ubicaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ubicacion_rangos`
--
ALTER TABLE `ubicacion_rangos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `denominaciones`
--
ALTER TABLE `denominaciones`
  ADD CONSTRAINT `denominaciones_tipo_especie_id_foreign` FOREIGN KEY (`tipo_especie_id`) REFERENCES `tipo_especies` (`id`);

--
-- Filtros para la tabla `distrito_denominacion`
--
ALTER TABLE `distrito_denominacion`
  ADD CONSTRAINT `distrito_denominacion_denominacion_id_foreign` FOREIGN KEY (`denominacion_id`) REFERENCES `denominaciones` (`id`),
  ADD CONSTRAINT `distrito_denominacion_distrito_id_foreign` FOREIGN KEY (`distrito_id`) REFERENCES `distritos` (`id`);

--
-- Filtros para la tabla `distrito_tipo_especie`
--
ALTER TABLE `distrito_tipo_especie`
  ADD CONSTRAINT `distrito_tipo_especie_distrito_id_foreign` FOREIGN KEY (`distrito_id`) REFERENCES `distritos` (`id`),
  ADD CONSTRAINT `distrito_tipo_especie_tipo_especie_id_foreign` FOREIGN KEY (`tipo_especie_id`) REFERENCES `tipo_especies` (`id`);

--
-- Filtros para la tabla `lotes`
--
ALTER TABLE `lotes`
  ADD CONSTRAINT `lotes_compra_id_foreign` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`),
  ADD CONSTRAINT `lotes_denominacion_id_foreign` FOREIGN KEY (`denominacion_id`) REFERENCES `denominaciones` (`id`),
  ADD CONSTRAINT `lotes_tipo_especie_id_foreign` FOREIGN KEY (`tipo_especie_id`) REFERENCES `tipo_especies` (`id`);

--
-- Filtros para la tabla `lote_rangos`
--
ALTER TABLE `lote_rangos`
  ADD CONSTRAINT `lote_rangos_lote_id_foreign` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`);

--
-- Filtros para la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `nulas`
--
ALTER TABLE `nulas`
  ADD CONSTRAINT `nulas_distrito_id_foreign` FOREIGN KEY (`distrito_id`) REFERENCES `distritos` (`id`),
  ADD CONSTRAINT `nulas_traslado_detalle_id_foreign` FOREIGN KEY (`traslado_detalle_id`) REFERENCES `traslado_detalles` (`id`),
  ADD CONSTRAINT `nulas_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `realizaciones`
--
ALTER TABLE `realizaciones`
  ADD CONSTRAINT `realizaciones_denominacion_id_foreign` FOREIGN KEY (`denominacion_id`) REFERENCES `denominaciones` (`id`),
  ADD CONSTRAINT `realizaciones_distrito_id_foreign` FOREIGN KEY (`distrito_id`) REFERENCES `distritos` (`id`),
  ADD CONSTRAINT `realizaciones_tipo_especie_id_foreign` FOREIGN KEY (`tipo_especie_id`) REFERENCES `tipo_especies` (`id`),
  ADD CONSTRAINT `realizaciones_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `traslados`
--
ALTER TABLE `traslados`
  ADD CONSTRAINT `traslados_distrito_id_foreign` FOREIGN KEY (`distrito_id`) REFERENCES `distritos` (`id`),
  ADD CONSTRAINT `traslados_origen_distrito_id_foreign` FOREIGN KEY (`origen_distrito_id`) REFERENCES `distritos` (`id`),
  ADD CONSTRAINT `traslados_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `traslado_detalles`
--
ALTER TABLE `traslado_detalles`
  ADD CONSTRAINT `traslado_detalles_lote_id_foreign` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`),
  ADD CONSTRAINT `traslado_detalles_traslado_id_foreign` FOREIGN KEY (`traslado_id`) REFERENCES `traslados` (`id`);

--
-- Filtros para la tabla `ubicaciones`
--
ALTER TABLE `ubicaciones`
  ADD CONSTRAINT `ubicaciones_distrito_id_foreign` FOREIGN KEY (`distrito_id`) REFERENCES `distritos` (`id`);

--
-- Filtros para la tabla `ubicacion_rangos`
--
ALTER TABLE `ubicacion_rangos`
  ADD CONSTRAINT `ubicacion_rangos_tipo_especie_id_foreign` FOREIGN KEY (`tipo_especie_id`) REFERENCES `tipo_especies` (`id`),
  ADD CONSTRAINT `ubicacion_rangos_ubicacion_id_foreign` FOREIGN KEY (`ubicacion_id`) REFERENCES `ubicaciones` (`id`),
  ADD CONSTRAINT `ubicacion_rangos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
