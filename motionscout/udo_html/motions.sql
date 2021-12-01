-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Erstellungszeit: 01. Dez 2021 um 18:07
-- Server-Version: 10.3.31-MariaDB-0+deb10u1
-- PHP-Version: 7.1.20-1+b2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `motionscout`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `motions`
--

DROP TABLE IF EXISTS `motions`;
CREATE TABLE `motions` (
  `id` bigint(15) NOT NULL,
  `time` timestamp(6) NULL DEFAULT current_timestamp(6),
  `user` varchar(30) DEFAULT NULL,
  `wert1` varchar(15) DEFAULT NULL,
  `wert2` varchar(15) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `motions`
--

INSERT INTO `motions` (`id`, `time`, `user`, `wert1`, `wert2`) VALUES
(1, '2021-11-22 09:05:32.078407', 'udo', 'ESP32-1', 530),
(2, '2021-11-22 09:06:27.085492', 'udo', 'Raspi V1.3.1', NULL);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `motions`
--
ALTER TABLE `motions`
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `time` (`time`),
  ADD KEY `user` (`user`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `motions`
--
ALTER TABLE `motions`
  MODIFY `id` bigint(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23412;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
