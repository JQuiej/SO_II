-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-04-2025 a las 06:13:08
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
-- Base de datos: `login_registro_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial`
--

CREATE TABLE `historial` (
  `id` int(11) NOT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `accion` text DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `historial`
--

INSERT INTO `historial` (`id`, `usuario`, `accion`, `fecha`) VALUES
(175, 'sam123', 'Inició sesión', '2025-04-05 09:01:39'),
(176, 'lorozco', 'Inició sesión', '2025-04-05 09:02:53'),
(177, 'sam123', 'Inició sesión', '2025-04-05 09:05:17'),
(178, 'prueba2', 'Inició sesión', '2025-04-05 09:06:57'),
(179, 'prueba2', 'Actualizó su perfil', '2025-04-05 09:07:12'),
(180, 'sam123', 'Inició sesión', '2025-04-05 09:07:35'),
(181, 'sam123', 'Cambió rol de lorozco a admin', '2025-04-05 09:07:53'),
(182, 'sam123', 'Desactivó cuenta del usuario ID 20', '2025-04-05 09:08:15'),
(183, 'sam123', 'Activó cuenta del usuario ID 20', '2025-04-05 09:08:22'),
(184, 'sam123', 'Inició sesión', '2025-04-14 12:28:27'),
(185, 'sam123', 'Inició sesión', '2025-04-14 12:53:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre_completo` varchar(255) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `rol` varchar(20) NOT NULL DEFAULT 'usuario',
  `token_recuperacion` varchar(64) DEFAULT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre_completo`, `correo`, `usuario`, `contrasena`, `telefono`, `avatar_url`, `rol`, `token_recuperacion`, `estado`) VALUES
(8, 'Luis Armando Orozco', 'oro440@gmail.com', 'lorozco', '$2y$10$z5UKIm2aWZxdkl0n3G.LFe4fvKvsFCX8J3A/GBldAqCUu7AiaBRt6', '40154711', 'uploads/67e6354f40e93.jpeg', 'admin', '37b74e8a03e5b8638e5363ae50a30c42', 'activo'),
(18, 'Samantha Orozco', 'lorzco@celtech.com', 'sam123', '$2y$10$e243CoTWM28n0Po.1oTR1uRuTjDY209porwA3CFA60g9BUhR4XqXC', '55555555', 'uploads/67e634be2d98a.jpeg', 'admin', NULL, 'activo'),
(19, 'Andrick Martinez', 'amartinez@gmail.com', 'amartinez', '$2y$10$muzLrEoV3SzvFJTy/gr/XONKgmr421a/NYYD.OonAHNe76eAT0RXG', '32255845', 'uploads/67e9873743225.png', 'usuario', NULL, 'activo'),
(20, 'jose', 'alkfjklj@gmail.com', 'Jquiej', '$2y$10$mWOJELQkIbxwYU0V9E6NQO2qMdIII0L0FUsNjq/ppm9XSYUgpOgsO', '3313213', NULL, 'usuario', NULL, 'activo'),
(21, 'prueba', 'prueba@gmail.com', 'prueba', '$2y$10$ybuVE9SIyDqolQp3a/Rvi.J/SeCHoWKTWKjjS2Hfykrk0SQmo0BZe', '55555555', 'uploads/67f143cf7eb5a.png', 'usuario', NULL, 'activo'),
(22, 'prueba2', 'prueba2@gmail.com', 'prueba2', '$2y$10$jE.799mRTiVYhcqLwCYlQOox7RMMRk0sWHqUku.4X5O6YFq0.w9xK', '666666', 'uploads/67f147052aef0.jpeg', 'usuario', NULL, 'activo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `historial`
--
ALTER TABLE `historial`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `historial`
--
ALTER TABLE `historial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=186;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
