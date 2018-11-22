-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 22, 2018 at 02:43 PM
-- Server version: 5.7.23
-- PHP Version: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gac`
--
CREATE DATABASE IF NOT EXISTS `gac` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `gac`;

-- --------------------------------------------------------

--
-- Table structure for table `calls_details`
--

DROP TABLE IF EXISTS `calls_details`;
CREATE TABLE IF NOT EXISTS `calls_details` (
  `compte_facture` int(11) NOT NULL,
  `id_facture` int(11) NOT NULL,
  `id_abonne` int(11) NOT NULL,
  `cdate` date NOT NULL,
  `ctime` time NOT NULL,
  `volume_reel` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `volume_facture` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
