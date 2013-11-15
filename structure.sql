-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: 144.76.202.174
-- Generation Time: Nov 15, 2013 at 01:45 AM
-- Server version: 5.5.34-0ubuntu0.12.04.1
-- PHP Version: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mcCache`
--
CREATE DATABASE IF NOT EXISTS `mcCache` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `mcCache`;

-- --------------------------------------------------------

--
-- Table structure for table `player_averages`
--

DROP TABLE IF EXISTS `player_averages`;
CREATE TABLE IF NOT EXISTS `player_averages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `server` text NOT NULL,
  `date` int(11) NOT NULL,
  `startdate` int(11) NOT NULL,
  `enddate` int(11) NOT NULL,
  `startid` int(11) NOT NULL,
  `endid` int(11) NOT NULL,
  `averageonline` int(11) NOT NULL,
  `mostonline` int(11) NOT NULL,
  `leastonline` int(11) NOT NULL,
  `players` text NOT NULL,
  `maxplayers` int(11) NOT NULL,
  `uniqueplayers` int(11) NOT NULL,
  `glitch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=296 ;

-- --------------------------------------------------------

--
-- Table structure for table `player_cache`
--

DROP TABLE IF EXISTS `player_cache`;
CREATE TABLE IF NOT EXISTS `player_cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `server` text NOT NULL,
  `date` int(11) NOT NULL,
  `players` text NOT NULL,
  `maxplayers` int(11) NOT NULL,
  `onlineplayers` int(11) NOT NULL,
  `offline` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=57804 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
