-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 10, 2025 at 04:09 AM
-- Server version: 10.3.39-MariaDB-0ubuntu0.20.04.2
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bpa_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(122) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `adoption`
--

CREATE TABLE `adoption` (
  `adopt_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `phone` bigint(11) NOT NULL,
  `address` varchar(200) NOT NULL,
  `pet_name` varchar(200) NOT NULL,
  `pet_type` enum('dog','cat') NOT NULL,
  `reason` longtext NOT NULL,
  `experience` enum('yes','no') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adoption`
--

INSERT INTO `adoption` (`adopt_id`, `name`, `email`, `phone`, `address`, `pet_name`, `pet_type`, `reason`, `experience`, `created_at`) VALUES
(9, 'ace', 'acefelixerp.manganaan@gmail.com', 9064075290, 'Bahay', 'ash', 'dog', 'ace', 'yes', '2024-12-12 08:00:34');

-- --------------------------------------------------------

--
-- Table structure for table `Missing`
--

CREATE TABLE `Missing` (
  `m_id` int(11) NOT NULL,
  `m_name` varchar(50) NOT NULL,
  `m_mail` varchar(50) NOT NULL,
  `m_phone` bigint(20) NOT NULL,
  `m_breed` varchar(50) NOT NULL,
  `m_place` longtext NOT NULL,
  `m_descript` longtext NOT NULL,
  `m_photo` blob NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `Missing`
--

INSERT INTO `Missing` (`m_id`, `m_name`, `m_mail`, `m_phone`, `m_breed`, `m_place`, `m_descript`, `m_photo`, `created_at`) VALUES
(1, 'ace', 'acefelixerp.manganaan@yahoo.com', 9064075290, 'chuahua', 'saranay', 'safdsfbgbd', 0x2e2e2f73746f7265642f7065745f696d6167652f646f67312e6a7067, '2024-11-17 16:41:54');

-- --------------------------------------------------------

--
-- Table structure for table `register`
--

CREATE TABLE `register` (
  `id` int(11) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `email` varchar(500) NOT NULL,
  `pet` varchar(50) NOT NULL,
  `age` varchar(50) NOT NULL,
  `breed` varchar(50) NOT NULL,
  `address` longtext NOT NULL,
  `pet_image` blob NOT NULL,
  `pet_vaccine` blob NOT NULL,
  `additional_info` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `register`
--

INSERT INTO `register` (`id`, `owner`, `email`, `pet`, `age`, `breed`, `address`, `pet_image`, `pet_vaccine`, `additional_info`, `created_at`) VALUES
(78, 'burger', 'userace@a', 'wendhil', '1', 'Chuahua', 'Saranay', 0x2e2e2f73746f7265642f7065745f696d6167652f646f67316a7067, 0x2e2e2f73746f7265642f76616363696e655f7265636f72642f6361746a7067, 'sdvfgbnhjmk,hmgfdcvgbhnj', '2024-11-23 16:26:09'),
(79, 'burger', 'macefelixerp@gmail.com', 'dan', '1', 'chuahhua', 'Saranay', 0x2e2e2f73746f7265642f7065745f696d6167652f6361746a7067, 0x2e2e2f73746f7265642f76616363696e655f7265636f72642f646f67326a7067, 'lkjhgfchbjk', '2024-11-23 16:27:45'),
(80, 'ace', 'macefelixerp@gmail.com', 'wendhil', '1', 'chuahhua', 'Saranay', 0x2e2e2f73746f7265642f7065745f696d6167652f646f67316a7067, 0x2e2e2f73746f7265642f76616363696e655f7265636f72642f6361746a7067, 'kjhuytrdtfyguhi', '2024-11-23 16:30:11'),
(81, 'ace', 'macefelixerp@gmail.com', 'wendhil', '1', 'chuahhua', 'Saranay', 0x2e2e2f73746f7265642f7065745f696d6167652f646f67316a7067, 0x2e2e2f73746f7265642f76616363696e655f7265636f72642f6361746a7067, 'kjhuytrdtfyguhi', '2024-11-23 16:31:19'),
(82, 'burger', 'acefelixerp.manganaan@yahoo.com', 'dan', '1', 'dasdasdas', 'Saranay', 0x2e2e2f73746f7265642f7065745f696d6167652f6361746a7067, 0x2e2e2f73746f7265642f76616363696e655f7265636f72642f64617368626f61726473706e67, 'asdfbgterdf trbhvrbtyn', '2024-11-23 17:06:56'),
(83, 'wendhil', 'wen@gmail.com', 'wenwen', '1', 'chuahua', '221 gonzales st.  cal. city', 0x2e2e2f73746f7265642f7065745f696d6167652f646f676a7067, 0x2e2e2f73746f7265642f76616363696e655f7265636f72642f76616363696e656a706567, 'cute,addorable, and sweet', '2024-11-24 00:42:58'),
(84, 'burger', 'acefelixerp.manganaan@yahoo.com', 'merry', '1', 'dog', 'Saranay', 0x2e2e2f73746f7265642f7065745f696d6167652f616e6772795f636869687561687561706e67, 0x2e2e2f73746f7265642f76616363696e655f7265636f72642f6361746a7067, 'asxdcfvgxbchnvjmkn.lmk.j,hujyht', '2024-11-26 08:29:22'),
(85, 'burger', 'acefelixerp.manganaan@yahoo.com', 'merry', '1', 'dog', 'Saranay', 0x2e2e2f73746f7265642f7065745f696d6167652f616e6772795f636869687561687561706e67, 0x2e2e2f73746f7265642f76616363696e655f7265636f72642f6361746a7067, 'asxdcfvgxbchnvjmkn.lmk.j,hujyht', '2024-11-26 08:46:31');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` bigint(55) NOT NULL,
  `email` varchar(200) NOT NULL,
  `species` varchar(200) NOT NULL,
  `breed` varchar(200) NOT NULL,
  `numabuse` bigint(50) NOT NULL,
  `typeabuse` enum('physical abuse','emotional abuse','sexual abuse','abandonment') NOT NULL,
  `descript` longtext NOT NULL,
  `evidence` blob NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `pwd` varchar(200) NOT NULL,
  `role` enum('admin','user','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `pwd`, `role`) VALUES
(1, 'userace', 'userace@a', '$2y$10$SHLGDT88JPR0GNwK3qK6K.AJpFgessO89.kSLAxLcsUw/x1iPmhHG', 'user'),
(2, 'admin', 'admin@gmail.com', '$2y$10$wUeFunJOJ2o9HnrRRhBD9ur.QavLJP6C9JH2xL6bME1OoQf9uc.Qi', 'admin'),
(21, 'nayeonie', 'nayeonie@gmail.com', '$2y$10$m78n9VSTrOPiYi9haYEGhu8XoGFjAoHnKn85QEP3mhQ55JZym5..6', 'user'),
(22, 'roseann', 'roseannolarte@gmail.com', '$2y$10$Df.i2SSgnWy60B6KuSNhRuilFlebuOVpy3xoEQgaJYSBZAM/sM.3S', 'user'),
(23, 'juandelacroz', 'juandelacruz@email.com', '$2y$10$Ovdb3.vjldt.YRpB/6LqjOCoABfPrQ8UxBdXkssVYymHmTP0KRGKi', 'user'),
(24, 'ace123', 'ace.12@gmail.com', '$2y$10$MV3FY.7tyaVp0.8QUihu2OV2TgjUHfGRSe6JtMkQo4CjUN8jBK3B6', 'user'),
(25, 'smart2', 'smart.12@gmail.com', '$2y$10$TPhbtSB7Goal6n0oakxSXO87bsgFb.7BrgahZeDBBzr4ubBYxU1Ta', 'user'),
(26, 'admin@', 'admin@mail.com', '$2y$10$8FdCV/Yy3cLbHw4g//E.7uHbCQ2FILtR2wjtnOQpaq0k.H0UElwrC', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `adoption`
--
ALTER TABLE `adoption`
  ADD PRIMARY KEY (`adopt_id`);

--
-- Indexes for table `Missing`
--
ALTER TABLE `Missing`
  ADD PRIMARY KEY (`m_id`);

--
-- Indexes for table `register`
--
ALTER TABLE `register`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `adoption`
--
ALTER TABLE `adoption`
  MODIFY `adopt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `Missing`
--
ALTER TABLE `Missing`
  MODIFY `m_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `register`
--
ALTER TABLE `register`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
