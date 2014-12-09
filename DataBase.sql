-- phpMyAdmin SQL Dump
-- version 2.11.8.1deb5+lenny3
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 27-10-2009 a las 11:52:56
-- Versión del servidor: 5.0.51
-- Versión de PHP: 5.2.6-1+lenny3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de datos: `sluger`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direcciones`
--

CREATE TABLE IF NOT EXISTS `direcciones` (
  `id` int(11) NOT NULL auto_increment,
  `url` varchar(256) collate utf8_spanish_ci NOT NULL,
  `creada` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `creada` (`creada`),
  KEY `url` (`url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `elegidas`
--

CREATE TABLE IF NOT EXISTS `elegidas` (
  `id` varchar(50) collate utf8_spanish_ci NOT NULL,
  `url` varchar(256) collate utf8_spanish_ci NOT NULL,
  `creada` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `url` (`url`),
  KEY `creada` (`creada`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL auto_increment,
  `idpag` varchar(50) character set utf8 collate utf8_bin NOT NULL,
  `fecha` datetime NOT NULL,
  `refer` tinytext collate utf8_spanish_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fecha` (`fecha`),
  KEY `idpag` (`idpag`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

