-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 30, 2019 at 05:45 PM
-- Server version: 5.7.25-0ubuntu0.18.04.2
-- PHP Version: 7.2.10-0ubuntu0.18.04.1

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
-- Table structure for table `lis_order`
--

CREATE TABLE `lis_order` (
  `id` int(11) NOT NULL,
  `message_date` datetime NOT NULL COMMENT 'Date/Time of Message',
  `patient_id` bigint(20) NOT NULL,
  `patient_name` varchar(200) NOT NULL COMMENT 'ชื่อ-สกุล',
  `gender` char(1) NOT NULL COMMENT 'เพศ',
  `birth_date` date NOT NULL COMMENT 'วันเกิด',
  `lis_number` varchar(20) NOT NULL,
  `reference_number` varchar(20) NOT NULL,
  `accept_time` datetime NOT NULL,
  `request_div` varchar(10) NOT NULL COMMENT 'รหัสหน่วยงานที่ส่งตรวจ',
  `sec_user` varchar(20) NOT NULL DEFAULT '',
  `sec_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sec_ip` varchar(20) NOT NULL DEFAULT '',
  `sec_script` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lis_order`
--
ALTER TABLE `lis_order`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lis_order`
--
ALTER TABLE `lis_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
