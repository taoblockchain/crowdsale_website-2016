-- phpMyAdmin SQL Dump
-- version 4.6.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 31, 2016 at 07:43 AM
-- Server version: 5.6.31-77.0-log
-- PHP Version: 5.6.23-1+0~20160628130202.3+jessie~1.gbp530356

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crowdsale`
--
CREATE DATABASE IF NOT EXISTS `crowdsale` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `crowdsale`;

-- --------------------------------------------------------

--
-- Table structure for table `final`
--

DROP TABLE IF EXISTS `final`;
CREATE TABLE IF NOT EXISTS `final` (
  `AmtPurchased` double(25,8) DEFAULT NULL,
  `TaoAddress` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Sales`
--

DROP TABLE IF EXISTS `Sales`;
CREATE TABLE IF NOT EXISTS `Sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `CoinAddress` text NOT NULL,
  `BTCaddress` text NOT NULL,
  `ReferralId` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=219 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `temp2`
--

DROP TABLE IF EXISTS `temp2`;
CREATE TABLE IF NOT EXISTS `temp2` (
  `purchased` double DEFAULT NULL,
  `modifier` float NOT NULL,
  `BitcoinAddress` varchar(40) NOT NULL,
  `TaoAddress` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
  `transaction_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `txid` varchar(200) NOT NULL,
  `address` varchar(40) NOT NULL,
  `amount` float NOT NULL,
  `modifier` float NOT NULL,
  `timestamp` bigint(20) NOT NULL,
  PRIMARY KEY (`transaction_id`),
  KEY `amount` (`amount`)
) ENGINE=InnoDB AUTO_INCREMENT=183 DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
