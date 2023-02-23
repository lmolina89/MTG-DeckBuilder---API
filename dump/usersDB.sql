
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'primary-key', 
  `email` varchar(60) NOT NULL,
  `passwd` varchar(240) NOT NULL,
  `nick` varchar(40) NOT NULL,
  `imageUri` varchar(300) DEFAULT NULL,
  `token` varchar(240) DEFAULT NULL,
  `deckList` LONGTEXT DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='users-table';

CREATE TABLE `deck` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  `numCards` INT NOT NULL DEFAULT '0',
  `userDeck` VARCHAR(300) NULL DEFAULT NULL,
  `deckImage` VARCHAR(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id),
  PRIMARY KEY (id)
);

CREATE TABLE card (
  `id` INT NOT NULL ,
  `name` VARCHAR(255) NOT NULL,
  `manacost` varchar(40),
  `cmc` int,
  `atributes` VARCHAR(10),
  `text` VARCHAR(255),
  `artist` VARCHAR(50),
  `expansion` VARCHAR(50),
  `imageUri` VARCHAR(300),
  `numCopies` INT,
  PRIMARY KEY (`id`)
);

CREATE TABLE userdeck (
  `user_id` INT NOT NULL,
  `deck_id` INT NOT NULL,
  PRIMARY KEY (`user_id`, `deck_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
  FOREIGN KEY (`deck_id`) REFERENCES `deck`(`id`)
);

CREATE TABLE deckcard (
  `deck_id` INT NOT NULL,
  `card_id` INT NOT NULL,
  PRIMARY KEY (`deck_id`, `card_id`),
  FOREIGN KEY (`deck_id`) REFERENCES `deck`(`id`),
  FOREIGN KEY (`card_id`) REFERENCES `card`(`id`)
);


INSERT INTO users(id,email,passwd,nick,imageUri,token,deckList) 
VALUES ('1','admin@admin.com','8C6976E5B5410415BDE908BD4DEE15DFB167A9C873FC4BB8A81F6F2AB448A918','admin',null,null,"")
-- ALTER TABLE `deck` CHANGE `numCards` `numCards` INT NOT NULL DEFAULT '0';
-- ALTER TABLE `deck` CHANGE `deckImage` `deckImage` VARCHAR(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL;
