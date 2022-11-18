-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 18-11-2022 a las 10:31:40
-- Versión del servidor: 5.7.23-23
-- Versión de PHP: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `saf`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consultas`
--

CREATE TABLE `consultas` (
  `id_consulta` int(11) NOT NULL,
  `id_hospital` int(11) NOT NULL,
  `id_paciente_consulta` int(11) DEFAULT NULL,
  `nombre_especialista` varchar(200) COLLATE utf8mb4_spanish_ci NOT NULL,
  `cantidad_pacientes` int(11) NOT NULL,
  `tipo_consulta` enum('pediatria','urgencia','cgi') COLLATE utf8mb4_spanish_ci NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_spanish_ci NOT NULL COMMENT 'desocupado, ocupado, espera'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `consultas`
--

INSERT INTO `consultas` (`id_consulta`, `id_hospital`, `id_paciente_consulta`, `nombre_especialista`, `cantidad_pacientes`, `tipo_consulta`, `estado`) VALUES
(1, 1, NULL, 'Tatiana Tironi Roni ', 0, 'pediatria', 'desocupado'),
(2, 1, NULL, 'Edelmiro Chelle Chetrit', 0, 'urgencia', 'desocupado'),
(3, 1, NULL, 'Marianela Andrea Fuenzalida Riffo', 0, 'cgi', 'desocupado'),
(4, 2, NULL, 'Doctor 1', 0, 'cgi', 'desocupado'),
(5, 2, NULL, 'Doctor 2', 0, 'urgencia', 'desocupado'),
(6, 2, NULL, 'doctor 3', 0, 'pediatria', 'desocupado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hospitales`
--

CREATE TABLE `hospitales` (
  `id_hospital` int(11) NOT NULL,
  `nombre` varchar(200) COLLATE utf8mb4_spanish_ci NOT NULL,
  `direccion` varchar(400) COLLATE utf8mb4_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `hospitales`
--

INSERT INTO `hospitales` (`id_hospital`, `nombre`, `direccion`) VALUES
(1, 'Centro Clínico Fonasa 1', 'Merced 783'),
(2, 'Fonasa Chile-España', 'Chile España 47-95');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes`
--

CREATE TABLE `pacientes` (
  `id_paciente` int(11) NOT NULL,
  `id_hospital` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_spanish_ci NOT NULL,
  `edad` int(11) NOT NULL,
  `nro_historia_clinica` int(11) NOT NULL,
  `prioridad` float DEFAULT NULL,
  `riesgo` float NOT NULL,
  `estado` int(11) NOT NULL DEFAULT '0',
  `atendido` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `pacientes`
--

INSERT INTO `pacientes` (`id_paciente`, `id_hospital`, `nombre`, `edad`, `nro_historia_clinica`, `prioridad`, `riesgo`, `estado`, `atendido`) VALUES
(1, 1, 'Timmy P. Turner', 8, 1, 5, 0.4, 0, 0),
(2, 1, 'Herman Van Denker', 28, 2, 2, 0.56, 0, 0),
(3, 1, 'Auda Castro', 63, 3, 5.1, 8.513, 0, 0),
(5, 1, 'Diva Alvarez', 28, 4, 3.25, 0.91, 0, 0),
(6, 1, 'Edelmira Cantú', 3, 5, 7, 0.21, 0, 0),
(7, 1, 'Maurizio Tafoya', 12, 6, 6, 0.72, 0, 0),
(8, 1, 'Jordi Garibay', 15, 7, 2, 0.3, 0, 0),
(9, 1, 'Melina Cadena', 23, 8, 2, 0.46, 0, 0),
(10, 1, 'Vannina Barela', 37, 9, 2.75, 1.0175, 0, 0),
(11, 1, 'Ezio Caraballo', 51, 10, 4.7, 7.697, 0, 0),
(13, 1, 'Ivan Jimenez', 33, 11, 2, 0.66, 0, 0),
(14, 1, 'Anabel Segura', 28, 12, 2.75, 0.77, 0, 0),
(15, 1, 'Daniel', 45, 13, 4.5, 7.325, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes_ancianos`
--

CREATE TABLE `pacientes_ancianos` (
  `id_paciente_anciano` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `tiene_dieta` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `pacientes_ancianos`
--

INSERT INTO `pacientes_ancianos` (`id_paciente_anciano`, `id_paciente`, `tiene_dieta`) VALUES
(2, 11, 1),
(3, 3, 0),
(4, 15, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes_jovenes`
--

CREATE TABLE `pacientes_jovenes` (
  `id_paciente_joven` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `es_fumador` tinyint(4) NOT NULL,
  `anios_fumando` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `pacientes_jovenes`
--

INSERT INTO `pacientes_jovenes` (`id_paciente_joven`, `id_paciente`, `es_fumador`, `anios_fumando`) VALUES
(1, 2, 0, 0),
(3, 5, 1, 5),
(4, 9, 0, 0),
(5, 10, 1, 3),
(6, 13, 0, 0),
(7, 14, 1, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes_ninios`
--

CREATE TABLE `pacientes_ninios` (
  `id_paciente_ninio` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `rel_peso_estatura` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `pacientes_ninios`
--

INSERT INTO `pacientes_ninios` (`id_paciente_ninio`, `id_paciente`, `rel_peso_estatura`) VALUES
(1, 1, 3),
(2, 6, 4),
(3, 7, 4),
(4, 8, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `consultas`
--
ALTER TABLE `consultas`
  ADD PRIMARY KEY (`id_consulta`),
  ADD KEY `id_hospital` (`id_hospital`);

--
-- Indices de la tabla `hospitales`
--
ALTER TABLE `hospitales`
  ADD PRIMARY KEY (`id_hospital`);

--
-- Indices de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`id_paciente`),
  ADD KEY `id_hostipial` (`id_hospital`);

--
-- Indices de la tabla `pacientes_ancianos`
--
ALTER TABLE `pacientes_ancianos`
  ADD PRIMARY KEY (`id_paciente_anciano`),
  ADD KEY `id_paciente` (`id_paciente`);

--
-- Indices de la tabla `pacientes_jovenes`
--
ALTER TABLE `pacientes_jovenes`
  ADD PRIMARY KEY (`id_paciente_joven`),
  ADD KEY `id_paciente` (`id_paciente`);

--
-- Indices de la tabla `pacientes_ninios`
--
ALTER TABLE `pacientes_ninios`
  ADD PRIMARY KEY (`id_paciente_ninio`),
  ADD KEY `id_paciente` (`id_paciente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `consultas`
--
ALTER TABLE `consultas`
  MODIFY `id_consulta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `hospitales`
--
ALTER TABLE `hospitales`
  MODIFY `id_hospital` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `id_paciente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `pacientes_ancianos`
--
ALTER TABLE `pacientes_ancianos`
  MODIFY `id_paciente_anciano` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `pacientes_jovenes`
--
ALTER TABLE `pacientes_jovenes`
  MODIFY `id_paciente_joven` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `pacientes_ninios`
--
ALTER TABLE `pacientes_ninios`
  MODIFY `id_paciente_ninio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `consultas`
--
ALTER TABLE `consultas`
  ADD CONSTRAINT `consultas_ibfk_1` FOREIGN KEY (`id_hospital`) REFERENCES `hospitales` (`id_hospital`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pacientes`
--
ALTER TABLE `pacientes`
  ADD CONSTRAINT `pacientes_ibfk_1` FOREIGN KEY (`id_hospital`) REFERENCES `hospitales` (`id_hospital`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pacientes_ancianos`
--
ALTER TABLE `pacientes_ancianos`
  ADD CONSTRAINT `pacientes_ancianos_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pacientes_jovenes`
--
ALTER TABLE `pacientes_jovenes`
  ADD CONSTRAINT `pacientes_jovenes_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pacientes_ninios`
--
ALTER TABLE `pacientes_ninios`
  ADD CONSTRAINT `pacientes_ninios_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
