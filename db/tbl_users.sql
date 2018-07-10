-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 08, 2018 at 09:32 AM
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
-- Table structure for table `tbl_users`
--

DROP TABLE IF EXISTS `tbl_users`;
CREATE TABLE IF NOT EXISTS `tbl_users` (
  `id` varchar(255) NOT NULL,
  `account` varchar(32) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `profiles_id` varchar(255) DEFAULT NULL,
  `is_admin` tinyint(4) NOT NULL DEFAULT '0',
  `is_manager` tinyint(4) NOT NULL DEFAULT '1',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `createtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_manager_status` (`is_manager`,`account`,`password`,`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`id`, `account`, `password`, `profiles_id`, `is_admin`, `is_manager`, `status`, `createtime`) VALUES
('tbl_users686F73743331353237343838373036343431', 'b2b@himirror.com', 'himirror', NULL, 1, 0, 1, '2018-05-28 14:25:06'),
('tbl_users686F73743331353237343839313938323734', 'b2b@watsons.com', 'watsons', 'SYSChannelProfiles686F73743331353237343839313133393730', 0, 1, 1, '2018-05-28 14:33:18');

--
-- Triggers `tbl_users`
--
DROP TRIGGER IF EXISTS `trg_tbl_users`;
DELIMITER $$
CREATE TRIGGER `trg_tbl_users` BEFORE INSERT ON `tbl_users` FOR EACH ROW SET @Hex = HEX(CONCAT(@@hostname, UNIX_TIMESTAMP(), CAST(RAND()*1000 AS UNSIGNED))),NEW.id = CONCAT('tbl_users', @Hex)
$$
DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
