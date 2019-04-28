-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 28, 2019 at 11:19 AM
-- Server version: 5.7.25-0ubuntu0.18.04.2
-- PHP Version: 7.2.15-0ubuntu0.18.04.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `theptarin`
--

-- --------------------------------------------------------

--
-- Table structure for table `lis_result`
--

CREATE TABLE `lis_result` (
  `lis_number` varchar(20) NOT NULL,
  `lis_code` varchar(10) NOT NULL COMMENT 'รหัสการตรวจของ LIS',
  `test` varchar(200) NOT NULL COMMENT 'ชื่อผลตรวจ',
  `lab_code` varchar(10) NOT NULL COMMENT 'รหัสการตรวจของ รพ.',
  `result_code` varchar(20) NOT NULL COMMENT 'รหัสผลการตรวจของ LIS',
  `result` varchar(50) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `normal_range` varchar(100) NOT NULL,
  `technical_time` datetime NOT NULL COMMENT 'Date/time validation (Technical)',
  `medical_time` datetime NOT NULL COMMENT 'Date/time validation (Medical)',
  `result_date` date NOT NULL,
  `user_id` varchar(50) NOT NULL COMMENT 'User ID validation (Technical^Medical)',
  `remark` text,
  `sec_user` varchar(20) NOT NULL DEFAULT '',
  `sec_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sec_ip` varchar(20) NOT NULL DEFAULT '',
  `sec_script` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lis_result`
--
ALTER TABLE `lis_result`
  ADD PRIMARY KEY (`lis_number`,`lis_code`,`result_code`) USING BTREE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
