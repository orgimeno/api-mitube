-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-01-2019 a las 23:26:46
-- Versión del servidor: 10.1.36-MariaDB
-- Versión de PHP: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `videos_application`
--

CREATE SCHEMA `videos_application` DEFAULT CHARACTER SET utf8 ;

-- --------------------------------------------------------

USE `videos_application`;

--
-- Estructura de tabla para la tabla `comments`
--

CREATE TABLE `comments` (
  `id` int(255) NOT NULL,
  `video_id` int(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `body` text CHARACTER SET latin1,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(255) NOT NULL,
  `role` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `name` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `surname` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `email` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `password` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `image` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `role`, `name`, `surname`, `email`, `password`, `image`, `created_at`) VALUES
(1, 'user', 'admin', 'istrador', 'admin@peterparker.com', '718e3978516d387924d91980a7e21af2f434de445731951a6585bda2eacef046', NULL, '2018-10-04 20:30:00'),
(2, 'user', 'Juan', 'Perez', 'juan@juan.com', 'pruebas', NULL, '2019-01-01 21:22:41'),
(3, 'user', 'admin', 'admin', 'juanjo@juan.com', '718e3978516d387924d91980a7e21af2f434de445731951a6585bda2eacef046', '1547944822.jpeg', '2019-01-01 21:29:58'),
(4, 'user', 'Alberto', 'Garcia', 'user@user.com', '04f8996da763b7a969b1028ee3007569eaf3a635486ddab211d512c85b9df8fb', NULL, '2019-01-16 20:17:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `videos`
--

CREATE TABLE `videos` (
  `id` int(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `title` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `description` text CHARACTER SET latin1,
  `status` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `image` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `video_path` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `videos`
--

INSERT INTO `videos` (`id`, `user_id`, `title`, `description`, `status`, `image`, `video_path`, `created_at`, `updated_at`) VALUES
(1, 3, 'titulo del video 1', NULL, NULL, '1547993470.jpeg', '1547993872.webm', '2019-01-20 02:11:00', '2019-01-20 02:11:00'),
(2, 3, 'titulo del video 2', NULL, NULL, NULL, NULL, '2019-01-20 13:51:09', '2019-01-20 13:51:09'),
(3, 3, 'Titulo 3 edit', NULL, NULL, NULL, NULL, '2019-01-20 14:03:32', '2019-01-20 14:19:53');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comment_video` (`video_id`),
  ADD KEY `fk_comment_user` (`user_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_videos_users` (`user_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_comment_video` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`);

--
-- Filtros para la tabla `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `fk_videos_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
