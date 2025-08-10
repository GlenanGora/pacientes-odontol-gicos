-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 10-08-2025 a las 04:16:32
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `clinica_odontologica`
--
CREATE DATABASE IF NOT EXISTS `clinica_odontologica` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `clinica_odontologica`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_citas`
--

DROP TABLE IF EXISTS `tbl_citas`;
CREATE TABLE IF NOT EXISTS `tbl_citas` (
  `id_cita` int NOT NULL AUTO_INCREMENT,
  `id_paciente` int NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `duracion` int NOT NULL COMMENT 'Duración en minutos',
  `tipo_cita` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('agendada','confirmada','completada','cancelada') COLLATE utf8mb4_unicode_ci DEFAULT 'agendada',
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_cita`),
  KEY `id_paciente` (`id_paciente`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_contactos_emergencia`
--

DROP TABLE IF EXISTS `tbl_contactos_emergencia`;
CREATE TABLE IF NOT EXISTS `tbl_contactos_emergencia` (
  `id_contacto` int NOT NULL AUTO_INCREMENT,
  `id_paciente` int NOT NULL,
  `nombre_contacto` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono_contacto` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_contacto`),
  KEY `id_paciente` (`id_paciente`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tbl_contactos_emergencia`
--

INSERT INTO `tbl_contactos_emergencia` (`id_contacto`, `id_paciente`, `nombre_contacto`, `telefono_contacto`) VALUES
(1, 1, 'Maria García', '912345678');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_departamentos`
--

DROP TABLE IF EXISTS `tbl_departamentos`;
CREATE TABLE IF NOT EXISTS `tbl_departamentos` (
  `id_departamento` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_departamento`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tbl_departamentos`
--

INSERT INTO `tbl_departamentos` (`id_departamento`, `nombre`) VALUES
(1, 'Lima'),
(2, 'Arequipa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_diagnosticos`
--

DROP TABLE IF EXISTS `tbl_diagnosticos`;
CREATE TABLE IF NOT EXISTS `tbl_diagnosticos` (
  `id_diagnostico` int NOT NULL AUTO_INCREMENT,
  `nombre_diagnostico` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_diagnostico`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tbl_diagnosticos`
--

INSERT INTO `tbl_diagnosticos` (`id_diagnostico`, `nombre_diagnostico`, `descripcion`) VALUES
(1, 'Caries Dental', 'Destrucción del esmalte y la dentina.'),
(2, 'Gingivitis', 'Inflamación de las encías.'),
(3, 'Periodontitis', 'Infección severa de las encías que daña los tejidos blandos.'),
(4, 'Bruxismo', 'Hábito involuntario de apretar o rechinar los dientes.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_distritos`
--

DROP TABLE IF EXISTS `tbl_distritos`;
CREATE TABLE IF NOT EXISTS `tbl_distritos` (
  `id_distrito` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_provincia` int DEFAULT NULL,
  PRIMARY KEY (`id_distrito`),
  KEY `id_provincia` (`id_provincia`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tbl_distritos`
--

INSERT INTO `tbl_distritos` (`id_distrito`, `nombre`, `id_provincia`) VALUES
(1, 'La Molina', 1),
(2, 'Miraflores', 1),
(3, 'Carabayllo', 1),
(4, 'Santa Rosa de Quives', 2),
(5, 'Canta', 2),
(6, 'Yanahuara', 3),
(7, 'Cayma', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_documento_tipos`
--

DROP TABLE IF EXISTS `tbl_documento_tipos`;
CREATE TABLE IF NOT EXISTS `tbl_documento_tipos` (
  `id_documento_tipo` int NOT NULL AUTO_INCREMENT,
  `nombre_tipo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_documento_tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tbl_documento_tipos`
--

INSERT INTO `tbl_documento_tipos` (`id_documento_tipo`, `nombre_tipo`) VALUES
(1, 'DNI'),
(2, 'Carné de Extranjería'),
(3, 'Pasaporte');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_estados_paciente`
--

DROP TABLE IF EXISTS `tbl_estados_paciente`;
CREATE TABLE IF NOT EXISTS `tbl_estados_paciente` (
  `id_estado` int NOT NULL AUTO_INCREMENT,
  `nombre_estado` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tbl_estados_paciente`
--

INSERT INTO `tbl_estados_paciente` (`id_estado`, `nombre_estado`) VALUES
(1, 'Tratamiento'),
(2, 'Tratado'),
(3, 'Fallecido'),
(4, 'Archivado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_historial_medico`
--

DROP TABLE IF EXISTS `tbl_historial_medico`;
CREATE TABLE IF NOT EXISTS `tbl_historial_medico` (
  `id_historial` int NOT NULL AUTO_INCREMENT,
  `id_paciente` int NOT NULL,
  `enfermedades_preexistentes` text COLLATE utf8mb4_unicode_ci,
  `alergias` text COLLATE utf8mb4_unicode_ci,
  `medicacion_actual` text COLLATE utf8mb4_unicode_ci,
  `habitos` text COLLATE utf8mb4_unicode_ci,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_historial`),
  KEY `id_paciente` (`id_paciente`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_odontograma`
--

DROP TABLE IF EXISTS `tbl_odontograma`;
CREATE TABLE IF NOT EXISTS `tbl_odontograma` (
  `id_odontograma` int NOT NULL AUTO_INCREMENT,
  `id_paciente` int NOT NULL,
  `tipo_denticion` enum('adulto','nino') COLLATE utf8mb4_unicode_ci DEFAULT 'adulto',
  `odontograma_data` json NOT NULL,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_odontograma`),
  KEY `id_paciente` (`id_paciente`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_odontograma_historial`
--

DROP TABLE IF EXISTS `tbl_odontograma_historial`;
CREATE TABLE IF NOT EXISTS `tbl_odontograma_historial` (
  `id_odontograma_historial` int NOT NULL AUTO_INCREMENT,
  `id_paciente` int NOT NULL,
  `id_procedimiento_realizado` int NOT NULL,
  `odontograma_data` json NOT NULL,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_odontograma_historial`),
  KEY `id_paciente` (`id_paciente`),
  KEY `id_procedimiento_realizado` (`id_procedimiento_realizado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_pacientes`
--

DROP TABLE IF EXISTS `tbl_pacientes`;
CREATE TABLE IF NOT EXISTS `tbl_pacientes` (
  `id_paciente` int NOT NULL AUTO_INCREMENT,
  `nombres` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `id_documento_tipo` int NOT NULL,
  `numero_documento` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correo_electronico` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_sexo` int NOT NULL,
  `id_departamento` int DEFAULT NULL,
  `id_provincia` int DEFAULT NULL,
  `id_distrito` int DEFAULT NULL,
  `observaciones_generales` text COLLATE utf8mb4_unicode_ci,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_estado` int NOT NULL,
  PRIMARY KEY (`id_paciente`),
  UNIQUE KEY `numero_documento` (`numero_documento`),
  KEY `id_documento_tipo` (`id_documento_tipo`),
  KEY `id_sexo` (`id_sexo`),
  KEY `id_estado` (`id_estado`),
  KEY `id_departamento` (`id_departamento`),
  KEY `id_provincia` (`id_provincia`),
  KEY `id_distrito` (`id_distrito`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_pagos`
--

DROP TABLE IF EXISTS `tbl_pagos`;
CREATE TABLE IF NOT EXISTS `tbl_pagos` (
  `id_pago` int NOT NULL AUTO_INCREMENT,
  `id_paciente` int NOT NULL,
  `id_plan_tratamiento` int DEFAULT NULL,
  `id_procedimiento_realizado` int DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_pago` enum('adelanto','pago final') COLLATE utf8mb4_unicode_ci DEFAULT 'adelanto',
  `fecha_pago` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pago`),
  KEY `id_paciente` (`id_paciente`),
  KEY `id_plan_tratamiento` (`id_plan_tratamiento`),
  KEY `id_procedimiento_realizado` (`id_procedimiento_realizado`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_planes_tratamiento`
--

DROP TABLE IF EXISTS `tbl_planes_tratamiento`;
CREATE TABLE IF NOT EXISTS `tbl_planes_tratamiento` (
  `id_plan_tratamiento` int NOT NULL AUTO_INCREMENT,
  `id_paciente` int NOT NULL,
  `id_diagnostico` int DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `estado_plan` enum('activo','completado','cancelado') COLLATE utf8mb4_unicode_ci DEFAULT 'activo',
  PRIMARY KEY (`id_plan_tratamiento`),
  KEY `id_paciente` (`id_paciente`),
  KEY `id_diagnostico` (`id_diagnostico`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_procedimientos_realizados`
--

DROP TABLE IF EXISTS `tbl_procedimientos_realizados`;
CREATE TABLE IF NOT EXISTS `tbl_procedimientos_realizados` (
  `id_procedimiento_realizado` int NOT NULL AUTO_INCREMENT,
  `id_plan_tratamiento` int NOT NULL,
  `id_tratamiento` int NOT NULL,
  `costo_personalizado` decimal(10,2) NOT NULL,
  `notas_evolucion` text COLLATE utf8mb4_unicode_ci,
  `fecha_realizacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_procedimiento_realizado`),
  KEY `id_plan_tratamiento` (`id_plan_tratamiento`),
  KEY `id_tratamiento` (`id_tratamiento`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_provincias`
--

DROP TABLE IF EXISTS `tbl_provincias`;
CREATE TABLE IF NOT EXISTS `tbl_provincias` (
  `id_provincia` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_departamento` int DEFAULT NULL,
  PRIMARY KEY (`id_provincia`),
  KEY `id_departamento` (`id_departamento`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tbl_provincias`
--

INSERT INTO `tbl_provincias` (`id_provincia`, `nombre`, `id_departamento`) VALUES
(1, 'Lima', 1),
(2, 'Canta', 1),
(3, 'Arequipa', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_recetas`
--

DROP TABLE IF EXISTS `tbl_recetas`;
CREATE TABLE IF NOT EXISTS `tbl_recetas` (
  `id_receta` int NOT NULL AUTO_INCREMENT,
  `id_paciente` int NOT NULL,
  `id_procedimiento_realizado` int NOT NULL,
  `indicaciones_generales` text COLLATE utf8mb4_unicode_ci,
  `fecha_emision` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_receta`),
  KEY `id_paciente` (`id_paciente`),
  KEY `id_procedimiento_realizado` (`id_procedimiento_realizado`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_recetas_detalle`
--

DROP TABLE IF EXISTS `tbl_recetas_detalle`;
CREATE TABLE IF NOT EXISTS `tbl_recetas_detalle` (
  `id_detalle` int NOT NULL AUTO_INCREMENT,
  `id_receta` int NOT NULL,
  `nombre_medicamento` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dosis` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `frecuencia` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duracion` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `id_receta` (`id_receta`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_sexos`
--

DROP TABLE IF EXISTS `tbl_sexos`;
CREATE TABLE IF NOT EXISTS `tbl_sexos` (
  `id_sexo` int NOT NULL AUTO_INCREMENT,
  `nombre_sexo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_sexo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tbl_sexos`
--

INSERT INTO `tbl_sexos` (`id_sexo`, `nombre_sexo`) VALUES
(1, 'Masculino'),
(2, 'Femenino'),
(3, 'No Especificado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_tratamientos`
--

DROP TABLE IF EXISTS `tbl_tratamientos`;
CREATE TABLE IF NOT EXISTS `tbl_tratamientos` (
  `id_tratamiento` int NOT NULL AUTO_INCREMENT,
  `nombre_tratamiento` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `costo_base` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_tratamiento`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tbl_tratamientos`
--

INSERT INTO `tbl_tratamientos` (`id_tratamiento`, `nombre_tratamiento`, `descripcion`, `costo_base`) VALUES
(1, 'Limpieza Dental', 'Eliminación de placa y sarro.', 80.00),
(2, 'Obturación con Resina', 'Relleno de una cavidad dental con resina compuesta.', 120.00),
(3, 'Extracción Simple', 'Remoción de una pieza dental.', 150.00),
(4, 'Endodoncia', 'Tratamiento de conducto radicular.', 450.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_usuarios`
--

DROP TABLE IF EXISTS `tbl_usuarios`;
CREATE TABLE IF NOT EXISTS `tbl_usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_completo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tbl_usuarios`
--

INSERT INTO `tbl_usuarios` (`id_usuario`, `usuario`, `password`, `nombre_completo`) VALUES
(1, 'admin', '5d5e3556b020d364f88a3a8190a6f392989c00fd8482e6d12468549633acd23b149c3a19d225c312d2fcbaad7d0bd7725404152e6af6424159e30c7f073a985d', 'Doctora Ejemplo');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tbl_citas`
--
ALTER TABLE `tbl_citas`
  ADD CONSTRAINT `tbl_citas_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `tbl_pacientes` (`id_paciente`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tbl_contactos_emergencia`
--
ALTER TABLE `tbl_contactos_emergencia`
  ADD CONSTRAINT `tbl_contactos_emergencia_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `tbl_pacientes` (`id_paciente`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tbl_distritos`
--
ALTER TABLE `tbl_distritos`
  ADD CONSTRAINT `tbl_distritos_ibfk_1` FOREIGN KEY (`id_provincia`) REFERENCES `tbl_provincias` (`id_provincia`);

--
-- Filtros para la tabla `tbl_historial_medico`
--
ALTER TABLE `tbl_historial_medico`
  ADD CONSTRAINT `tbl_historial_medico_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `tbl_pacientes` (`id_paciente`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tbl_odontograma`
--
ALTER TABLE `tbl_odontograma`
  ADD CONSTRAINT `tbl_odontograma_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `tbl_pacientes` (`id_paciente`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tbl_odontograma_historial`
--
ALTER TABLE `tbl_odontograma_historial`
  ADD CONSTRAINT `tbl_odontograma_historial_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `tbl_pacientes` (`id_paciente`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_odontograma_historial_ibfk_2` FOREIGN KEY (`id_procedimiento_realizado`) REFERENCES `tbl_procedimientos_realizados` (`id_procedimiento_realizado`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tbl_pacientes`
--
ALTER TABLE `tbl_pacientes`
  ADD CONSTRAINT `tbl_pacientes_ibfk_1` FOREIGN KEY (`id_documento_tipo`) REFERENCES `tbl_documento_tipos` (`id_documento_tipo`),
  ADD CONSTRAINT `tbl_pacientes_ibfk_2` FOREIGN KEY (`id_sexo`) REFERENCES `tbl_sexos` (`id_sexo`),
  ADD CONSTRAINT `tbl_pacientes_ibfk_3` FOREIGN KEY (`id_estado`) REFERENCES `tbl_estados_paciente` (`id_estado`),
  ADD CONSTRAINT `tbl_pacientes_ibfk_4` FOREIGN KEY (`id_departamento`) REFERENCES `tbl_departamentos` (`id_departamento`),
  ADD CONSTRAINT `tbl_pacientes_ibfk_5` FOREIGN KEY (`id_provincia`) REFERENCES `tbl_provincias` (`id_provincia`),
  ADD CONSTRAINT `tbl_pacientes_ibfk_6` FOREIGN KEY (`id_distrito`) REFERENCES `tbl_distritos` (`id_distrito`);

--
-- Filtros para la tabla `tbl_pagos`
--
ALTER TABLE `tbl_pagos`
  ADD CONSTRAINT `tbl_pagos_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `tbl_pacientes` (`id_paciente`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_pagos_ibfk_2` FOREIGN KEY (`id_plan_tratamiento`) REFERENCES `tbl_planes_tratamiento` (`id_plan_tratamiento`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_pagos_ibfk_3` FOREIGN KEY (`id_procedimiento_realizado`) REFERENCES `tbl_procedimientos_realizados` (`id_procedimiento_realizado`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tbl_planes_tratamiento`
--
ALTER TABLE `tbl_planes_tratamiento`
  ADD CONSTRAINT `tbl_planes_tratamiento_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `tbl_pacientes` (`id_paciente`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_planes_tratamiento_ibfk_2` FOREIGN KEY (`id_diagnostico`) REFERENCES `tbl_diagnosticos` (`id_diagnostico`) ON DELETE SET NULL;

--
-- Filtros para la tabla `tbl_procedimientos_realizados`
--
ALTER TABLE `tbl_procedimientos_realizados`
  ADD CONSTRAINT `tbl_procedimientos_realizados_ibfk_1` FOREIGN KEY (`id_plan_tratamiento`) REFERENCES `tbl_planes_tratamiento` (`id_plan_tratamiento`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_procedimientos_realizados_ibfk_2` FOREIGN KEY (`id_tratamiento`) REFERENCES `tbl_tratamientos` (`id_tratamiento`);

--
-- Filtros para la tabla `tbl_provincias`
--
ALTER TABLE `tbl_provincias`
  ADD CONSTRAINT `tbl_provincias_ibfk_1` FOREIGN KEY (`id_departamento`) REFERENCES `tbl_departamentos` (`id_departamento`);

--
-- Filtros para la tabla `tbl_recetas`
--
ALTER TABLE `tbl_recetas`
  ADD CONSTRAINT `tbl_recetas_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `tbl_pacientes` (`id_paciente`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_recetas_ibfk_2` FOREIGN KEY (`id_procedimiento_realizado`) REFERENCES `tbl_procedimientos_realizados` (`id_procedimiento_realizado`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tbl_recetas_detalle`
--
ALTER TABLE `tbl_recetas_detalle`
  ADD CONSTRAINT `tbl_recetas_detalle_ibfk_1` FOREIGN KEY (`id_receta`) REFERENCES `tbl_recetas` (`id_receta`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
