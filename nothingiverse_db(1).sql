-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Creato il: Giu 10, 2025 alle 23:57
-- Versione del server: 10.4.28-MariaDB
-- Versione PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nothingiverse_db`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `id_utente` int(11) NOT NULL,
  `id_modello` int(11) NOT NULL,
  `data_like` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `likes`
--

INSERT INTO `likes` (`id`, `id_utente`, `id_modello`, `data_like`) VALUES
(18, 5, 4, '2025-06-10 21:24:56'),
(21, 5, 2, '2025-06-10 21:27:47'),
(25, 5, 5, '2025-06-10 21:43:06');

-- --------------------------------------------------------

--
-- Struttura della tabella `modelli`
--

CREATE TABLE `modelli` (
  `id_modello` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nome_modello` varchar(100) NOT NULL,
  `data_pubblicazione` timestamp NOT NULL DEFAULT current_timestamp(),
  `quantita_like` int(11) DEFAULT 0,
  `immagine` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `modelli`
--

INSERT INTO `modelli` (`id_modello`, `id_user`, `nome_modello`, `data_pubblicazione`, `quantita_like`, `immagine`) VALUES
(1, 1, 'Supporto Smartphone Regolabile', '2025-06-10 19:10:51', 15, ''),
(2, 2, 'Miniatura Castello Medievale', '2025-06-10 19:10:51', 33, ''),
(3, 1, 'Organizer da Scrivania', '2025-06-10 19:10:51', 28, ''),
(4, 3, 'Action Figure', '2025-06-10 19:10:51', 20, ''),
(5, 2, 'Vaso Geometrico', '2025-06-10 19:10:51', 42, ''),
(6, 3, 'Portachiavi LED', '2025-06-10 19:10:51', 67, '');

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `data_registrazione` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`id`, `username`, `email`, `password`, `data_registrazione`) VALUES
(1, 'designer3d', 'designer@example.com', 'password123', '2025-06-10 19:10:51'),
(2, 'maker_pro', 'maker@example.com', 'password123', '2025-06-10 19:10:51'),
(3, 'creative_mind', 'creative@example.com', 'password123', '2025-06-10 19:10:51'),
(4, 'saretto', 'saretto@gmail.com', '$2y$10$oiZ8kQ6S3eUbIGvUFZpwL.5fJDPq./vEKKfh3mMTWQBNALEL/pGwi', '2025-06-10 20:16:17'),
(5, 'samu', 'samu@piazzalanza.com', '$2y$10$VBdNONq7B9p66XqT.aIfce6R/FJr20llb6wF/mbB7pIpgSTwXvWX2', '2025-06-10 20:29:43');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`id_utente`,`id_modello`),
  ADD KEY `id_modello` (`id_modello`);

--
-- Indici per le tabelle `modelli`
--
ALTER TABLE `modelli`
  ADD PRIMARY KEY (`id_modello`),
  ADD KEY `id_user` (`id_user`);

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT per la tabella `modelli`
--
ALTER TABLE `modelli`
  MODIFY `id_modello` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT per la tabella `utenti`
--
ALTER TABLE `utenti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`id_modello`) REFERENCES `modelli` (`id_modello`) ON DELETE CASCADE;

--
-- Limiti per la tabella `modelli`
--
ALTER TABLE `modelli`
  ADD CONSTRAINT `modelli_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `utenti` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
