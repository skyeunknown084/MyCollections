-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 08, 2018 at 10:22 AM
-- Server version: 5.7.21
-- PHP Version: 7.0.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `himirror_b2b_qa`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_members`
--

DROP TABLE IF EXISTS `tbl_members`;
CREATE TABLE IF NOT EXISTS `tbl_members` (
  `id` varchar(255) NOT NULL,
  `account` varchar(32) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `id_card` varchar(32) DEFAULT NULL,
  `phone_number` varchar(16) DEFAULT NULL,
  `birthday` datetime DEFAULT NULL,
  `gender` tinyint(4) DEFAULT NULL,
  `ip_address` varchar(16) DEFAULT NULL,
  `profiles_id` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `createtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatetime` datetime NOT NULL,  
  `firstname` varchar(32) DEFAULT NULL,
  `lastname` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_members`
--

INSERT INTO `tbl_members` (`id`, `account`, `password`, `id_card`, `phone_number`, `birthday`, `gender`, `ip_address`, `profiles_id`, `status`, `createtime`, `updatetime`,`firstname`,`lastname`) VALUES
('tbl_members123', NULL, NULL, '20180530', NULL, '2018-05-30 00:00:00', 1, NULL, 'SYSChannelProfiles686F73743331353237343839313133393730', 1, '2018-05-30 10:13:03', '0000-00-00 00:00:00','Jizel','DM');

--
-- Triggers `tbl_members`
--
DROP TRIGGER IF EXISTS `trg_tbl_members`;
DELIMITER $$
CREATE TRIGGER `trg_tbl_members` BEFORE INSERT ON `tbl_members` FOR EACH ROW SET @Hex = HEX(CONCAT(@@hostname, UNIX_TIMESTAMP(), CAST(RAND()*1000 AS UNSIGNED))),NEW.id = CONCAT('tbl_members', @Hex)
$$
DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
