-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 08, 2018 at 12:09 PM
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
-- Table structure for table `tbl_profiles`
--

DROP TABLE IF EXISTS `tbl_profiles`;
CREATE TABLE IF NOT EXISTS `tbl_profiles` (
  `id` varchar(255) NOT NULL,
  `name` varchar(32) DEFAULT NULL,
  `description` varchar(32) DEFAULT NULL,
  `logo_url` varchar(512) DEFAULT NULL,
  `profile_photo` varchar(512) DEFAULT NULL,
  `cover_photo` int(11) NOT NULL DEFAULT '0',
  `available_space` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `createtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `users_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_name_status` (`id`,`name`,`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_profiles`
--

INSERT INTO `tbl_profiles` (`id`, `name`, `title`, `logo_url`, `profile_photo`, `cover_photo`, `available_space`, `status`, `createtime`, `users_id`) VALUES
('tbl_profiles686F73743331353237343839313133393730', 'Watsons_TW', 'Watsons', NULL, NULL, 0, NULL, 1, '2018-05-28 14:31:53', '1234');

--
-- Triggers `tbl_profiles`
--
DROP TRIGGER IF EXISTS `trg_tbl_profiles`;
DELIMITER $$
CREATE TRIGGER `trg_tbl_profiles` BEFORE INSERT ON `tbl_profiles` FOR EACH ROW SET @Hex = HEX(CONCAT(@@hostname, UNIX_TIMESTAMP(), CAST(RAND()*1000 AS UNSIGNED))),NEW.id = CONCAT('tbl_profiles', @Hex)
$$
DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
