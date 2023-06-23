-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 04-06-2023 a las 17:49:10
-- Versión del servidor: 8.0.33
-- Versión de PHP: 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `usersDB`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `card`
--

CREATE TABLE `card` (
  `id` varchar(40) NOT NULL,
  `name` varchar(255) NOT NULL,
  `manacost` varchar(40) DEFAULT NULL,
  `cmc` int DEFAULT NULL,
  `atributes` varchar(10) DEFAULT NULL,
  `text` varchar(500) DEFAULT NULL,
  `artist` varchar(50) DEFAULT NULL,
  `expansion` varchar(50) DEFAULT NULL,
  `imageUri` varchar(300) DEFAULT NULL,
  `numCopies` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Estructura de tabla para la tabla `deck`
--

CREATE TABLE `deck` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `numCards` int NOT NULL DEFAULT '0',
  `deckImage` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Estructura de tabla para la tabla `deckcard`
--

CREATE TABLE `deckcard` (
  `deck_id` int NOT NULL,
  `card_id` varchar(40) NOT NULL,
  `numCards` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL COMMENT 'primary-key',
  `email` varchar(60) NOT NULL,
  `passwd` varchar(240) NOT NULL,
  `nick` varchar(40) NOT NULL,
  `token` varchar(240) DEFAULT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='users-table';

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `email`, `passwd`, `nick`, `token`, `admin`, `active`) VALUES
(1, 'admin@admin.com', '8C6976E5B5410415BDE908BD4DEE15DFB167A9C873FC4BB8A81F6F2AB448A918', 'admin', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2NzcxNzI5NjgsImRhdGEiOnsiaWQiOiIxIiwiZW1haWwiOm51bGx9fQ.urEK9Hq6u4pz9q2Tuulcm1gWIfE2pDpkQDB5WDEbkFc', 1, 1),
(2, 'lmolinamoreno@hotmail.com', '5663827DEAC9D358DC673EFF746DE182F10255A43B2D553D94D2D60869C25FF3', 'dummyplug_01', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2ODU4OTk3NzQsImRhdGEiOnsiaWQiOiIyIiwiZW1haWwiOiJsbW9saW5hbW9yZW5vQGhvdG1haWwuY29tIn19.9lPoMD5sURXpe7CnEm12ZMJbQxoiG-FrOzRFWBrwvJs', 0, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `card`
--
ALTER TABLE `card`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `deck`
--
ALTER TABLE `deck`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `deckcard`
--
ALTER TABLE `deckcard`
  ADD PRIMARY KEY (`deck_id`,`card_id`),
  ADD KEY `card_id` (`card_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `deck`
--
ALTER TABLE `deck`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'primary-key', AUTO_INCREMENT=3;


--
-- Filtros para la tabla `deck`
--
ALTER TABLE `deck`
  ADD CONSTRAINT `deck_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `deckcard`
--
ALTER TABLE `deckcard`
  ADD CONSTRAINT `deckcard_ibfk_1` FOREIGN KEY (`deck_id`) REFERENCES `deck` (`id`),
  ADD CONSTRAINT `deckcard_ibfk_2` FOREIGN KEY (`card_id`) REFERENCES `card` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
