
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 23-02-2023 a las 21:57:57
-- Versión del servidor: 8.0.32
-- Versión de PHP: 8.1.15

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
  `text` varchar(255) DEFAULT NULL,
  `artist` varchar(50) DEFAULT NULL,
  `expansion` varchar(50) DEFAULT NULL,
  `imageUri` varchar(300) DEFAULT NULL,
  `numCopies` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `card`
--

INSERT INTO `card` (`id`, `name`, `manacost`, `cmc`, `atributes`, `text`, `artist`, `expansion`, `imageUri`, `numCopies`) VALUES
('023b5e6f-10de-422d-8431-11f1fdeca246', 'Abu Jafar', '{W}', 1, '0/1', 'When Abu Jafar dies, destroy all creatures blocking or blocked by it. They cant be regenerated', 'Ken Meyer, Jr.', 'Chronicles', 'https://cards.scryfall.io/normal/front/d/2/d25ff6aa-a01d-49f2-926f-8f5457143b5c.jpg?1583542840', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deck`
--

CREATE TABLE `deck` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `numCards` int NOT NULL DEFAULT '0',
  `userDeck` varchar(300) DEFAULT NULL,
  `deckImage` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `deck`
--

INSERT INTO `deck` (`id`, `user_id`, `name`, `numCards`, `userDeck`, `deckImage`) VALUES
(7, 2, 'goblina', 0, NULL, ''),
(8, 2, 'goblin', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deckcard`
--

CREATE TABLE `deckcard` (
  `deck_id` int NOT NULL,
  `card_id` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `deckcard`
--

INSERT INTO `deckcard` (`deck_id`, `card_id`) VALUES
(8, '023b5e6f-10de-422d-8431-11f1fdeca246');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL COMMENT 'primary-key',
  `email` varchar(60) NOT NULL,
  `passwd` varchar(240) NOT NULL,
  `nick` varchar(40) NOT NULL,
  `imageUri` varchar(300) DEFAULT NULL,
  `token` varchar(240) DEFAULT NULL,
  `deckList` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='users-table';

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `email`, `passwd`, `nick`, `imageUri`, `token`, `deckList`) VALUES
(1, 'admin@admin.com', '8C6976E5B5410415BDE908BD4DEE15DFB167A9C873FC4BB8A81F6F2AB448A918', 'admin', NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2NzcxNzI5NjgsImRhdGEiOnsiaWQiOiIxIiwiZW1haWwiOm51bGx9fQ.urEK9Hq6u4pz9q2Tuulcm1gWIfE2pDpkQDB5WDEbkFc', ''),
(2, 'lmolinamoreno@hotmail.com', '5663827deac9d358dc673eff746de182f10255a43b2d553d94d2d60869c25ff3', 'dummyplug_01', NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2NzcxODgwNTAsImRhdGEiOnsiaWQiOiIyIiwiZW1haWwiOiJsbW9saW5hbW9yZW5vQGhvdG1haWwuY29tIn19.EKnPipYL2yOzK5SLbDiqrDD6TU-yHit8YyBqlcsSbfE', NULL);

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
-- Restricciones para tablas volcadas
--

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


-- SELECT c.*
-- FROM card c
-- JOIN deckcard dc ON c.id = dc.card_id
-- WHERE dc.deck_id = 7