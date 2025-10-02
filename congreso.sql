-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-10-2025 a las 13:57:38
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `congreso`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_usuario`
--

CREATE TABLE `detalle_usuario` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `condiciones_medicas` varchar(255) DEFAULT NULL,
  `estado_pago` varchar(50) DEFAULT NULL,
  `localidad` varchar(100) DEFAULT NULL,
  `legajo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT 'agregar informacion',
  `imagen` varchar(255) DEFAULT 'agregar informacion',
  `lugar` varchar(255) DEFAULT 'agregar informacion',
  `fecha` varchar(255) DEFAULT 'agregar informacion'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id`, `nombre`, `descripcion`, `imagen`, `lugar`, `fecha`) VALUES
(4, 'moyano', 'agregar informacion', 'agregar informacion', 'agregar informacion', 'agregar informacion'),
(5, 'Asd', 'agregar informacion', 'agregar informacion', 'agregar informacion', 'agregar informacion');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones`
--

CREATE TABLE `inscripciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `evento_id` int(11) NOT NULL,
  `fecha_inscripcion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tabla_qr`
--

CREATE TABLE `tabla_qr` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(50) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `rol` varchar(7) NOT NULL DEFAULT 'usuario',
  `dni` int(8) NOT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `localidad` varchar(100) DEFAULT NULL,
  `facultad` varchar(100) DEFAULT NULL,
  `carrera` varchar(100) DEFAULT NULL,
  `condiciones_medicas` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `token_expira` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `password`, `email`, `telefono`, `rol`, `dni`, `provincia`, `departamento`, `localidad`, `facultad`, `carrera`, `condiciones_medicas`, `token`, `token_expira`) VALUES
(1, 'Maxi', '$2y$10$kbsTWuSxm6hPygNA08PYSeBKv0ZCh6oE7lcnrliiEH9Juza6LaJ/K', 'fantinosmaximo@gmail.com', '3564367773', 'admin', 47579853, 'Córdoba', 'San Justo', 'San Francisco', 'UTN', 'Tecnicatura en Programacion', 'Miope', NULL, NULL),
(32, 'Violeta ', '$2y$10$8NW4OUwdumRf9G2V8W9jbuyN/cztnFFIyT7VEMhB3zN9Q1FsjL2Fe', 'vbrunettojuncos@escuelasproa.edu.ar', '', 'usuario', 47666238, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 'Loan', '$2y$10$2UsZJW.h2Eo9SlNsGlHRQ.AuYCkcSYpdHPioOXUBjwVuVJH.VlntK', 'fantinosmaximo@gmail.com', '', 'usuario', 47666237, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 'Maxi2', '$2y$10$qlJuIBP.FjChzB3sg.dER.zIPt9rmkhkfafALnuqbUroI.b6EzZW.', 'fantinosmaximo@gmail.com', '', 'admin', 23252473, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `detalle_usuario`
--
ALTER TABLE `detalle_usuario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_event` (`usuario_id`,`evento_id`),
  ADD KEY `evento_id` (`evento_id`);

--
-- Indices de la tabla `tabla_qr`
--
ALTER TABLE `tabla_qr`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `detalle_usuario`
--
ALTER TABLE `detalle_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tabla_qr`
--
ALTER TABLE `tabla_qr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_usuario`
--
ALTER TABLE `detalle_usuario`
  ADD CONSTRAINT `detalle_usuario_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `tabla_qr`
--
ALTER TABLE `tabla_qr`
  ADD CONSTRAINT `tabla_qr_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
