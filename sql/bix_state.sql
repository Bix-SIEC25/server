-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 19, 2026 at 01:03 PM
-- Server version: 11.4.8-MariaDB
-- PHP Version: 8.4.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `insa`
--

-- --------------------------------------------------------

--
-- Table structure for table `bix_state`
--

CREATE TABLE `bix_state` (
  `scenario` text NOT NULL DEFAULT '[{"transition":"waiting for scenario"}]',
  `x` float DEFAULT 0,
  `y` float NOT NULL DEFAULT 0,
  `dir` float NOT NULL DEFAULT 0,
  `wait_car` int(11) NOT NULL DEFAULT 1,
  `qr` tinyint(1) NOT NULL DEFAULT 0,
  `face` tinyint(1) NOT NULL DEFAULT 0,
  `dialog` tinyint(1) NOT NULL DEFAULT 0,
  `fall_ia` tinyint(1) NOT NULL DEFAULT 0,
  `mov_car` tinyint(1) NOT NULL DEFAULT 0,
  `wait_image_verif` tinyint(1) NOT NULL DEFAULT 0,
  `last_step` tinytext NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bix_state`
--

INSERT INTO `bix_state` (`scenario`, `x`, `y`, `dir`, `wait_car`, `qr`, `face`, `dialog`, `fall_ia`, `mov_car`, `wait_image_verif`, `last_step`) VALUES
('[{\"transition\":\"waiting for scenario\"}]', 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, '');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
