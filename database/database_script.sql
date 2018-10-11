-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-10-2018 a las 21:38:11
-- Versión del servidor: 5.6.26
-- Versión de PHP: 5.5.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `baisbol`
--
CREATE DATABASE IF NOT EXISTS `baisbol` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `baisbol`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `division`
--

CREATE TABLE IF NOT EXISTS `division` (
  `id_D` varchar(10) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `id_T` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipment`
--

CREATE TABLE IF NOT EXISTS `equipment` (
  `id_E` varchar(10) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `id_D` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `player`
--

CREATE TABLE IF NOT EXISTS `player` (
  `id_p` varchar(10) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `Height` float NOT NULL,
  `Weight_p` float NOT NULL,
  `id_E` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `season`
--

CREATE TABLE IF NOT EXISTS `season` (
  `id_T` varchar(10) NOT NULL,
  `name` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `division`
--
ALTER TABLE `division`
  ADD PRIMARY KEY (`id_D`),
  ADD KEY `id_T` (`id_T`);

--
-- Indices de la tabla `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id_E`),
  ADD KEY `id_D` (`id_D`);

--
-- Indices de la tabla `player`
--
ALTER TABLE `player`
  ADD PRIMARY KEY (`id_p`),
  ADD KEY `id_E` (`id_E`);

--
-- Indices de la tabla `season`
--
ALTER TABLE `season`
  ADD PRIMARY KEY (`id_T`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `division`
--
ALTER TABLE `division`
  ADD CONSTRAINT `division_ibfk_1` FOREIGN KEY (`id_T`) REFERENCES `season` (`id_T`);

--
-- Filtros para la tabla `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `equipment_ibfk_1` FOREIGN KEY (`id_D`) REFERENCES `division` (`id_D`);

--
-- Filtros para la tabla `player`
--
ALTER TABLE `player`
  ADD CONSTRAINT `player_ibfk_1` FOREIGN KEY (`id_E`) REFERENCES `equipment` (`id_E`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
