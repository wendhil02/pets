-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 29, 2025 at 04:28 AM
-- Server version: 10.4.32-MariaDB
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
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `pet_name` varchar(50) NOT NULL,
  `pet_type` varchar(50) NOT NULL,
  `pet_breed` varchar(50) NOT NULL,
  `info` text DEFAULT NULL,
  `reason` text NOT NULL,
  `experience` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adoption`
--

INSERT INTO `adoption` (`id`, `name`, `phone`, `email`, `address`, `pet_name`, `pet_type`, `pet_breed`, `info`, `reason`, `experience`, `created_at`) VALUES
(1, 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 'jhvgjh', 'dsadas', 'sdfcdfsv', 'sadczfvxcfdsvbcgfvd', 'dcfdsvscewscfvscsfv', '2025-01-23 01:48:16'),
(2, 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 'jhvgjh', 'dsadas', 'asdas', 'sadasdasdasd', 'dsadasdas', '2025-01-23 01:50:05'),
(3, 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 'jhvgjh', 'dsadas', 'asdas', 'dsvgdhfgmhj,hmgnfbgdvfsc', 'sdfvgbhnjmk,mhgnfds', '2025-01-23 01:50:39'),
(4, 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 'jhvgjh', 'dsadas', 'asdas', ';LMKJHGFDCHJK', 'LKMJHGFDXXFGDCHJ', '2025-01-23 06:52:31');

-- --------------------------------------------------------

--
-- Table structure for table `missing`
--

CREATE TABLE `missing` (
  `id` int(11) NOT NULL,
  `reportParty` varchar(255) NOT NULL,
  `phone_number` varchar(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `pet_name` varchar(255) NOT NULL,
  `petType` varchar(255) NOT NULL,
  `pet_breed` varchar(255) NOT NULL,
  `additional_info` text DEFAULT NULL,
  `pet_image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `register`
--

CREATE TABLE `register` (
  `id` int(11) NOT NULL,
  `registrationID` varchar(32) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `pet` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `breed` varchar(255) NOT NULL,
  `info` text DEFAULT NULL,
  `pet_image` varchar(255) NOT NULL,
  `pet_vaccine` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `register`
--

INSERT INTO `register` (`id`, `registrationID`, `owner`, `phone`, `email`, `address`, `pet`, `age`, `breed`, `info`, `pet_image`, `pet_vaccine`, `created_at`) VALUES
(24, '4fcce67deb1b4cf539e8297c18052f18', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sadaefrvtbhfngfbtgrehg', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678f67678714a.png', 'uploads/vaccine_images/vaccine_678f676787154.png', '2025-01-21 15:18:01'),
(25, '982f91a10eec3134eb438c3d2ec63435', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sadaefrvtbhfngfbtgrehg', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678f6909af39b.png', 'uploads/vaccine_images/vaccine_678f6909af3a3.png', '2025-01-21 15:18:01'),
(26, '799900878656dd61e6f5e750ec15fb45', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678f6bd6e5dc1.jpg', 'uploads/vaccine_images/vaccine_678f6bd6e5dca.jpg', '2025-01-21 15:18:01'),
(27, 'edbb2e2e1aa4c518aba3043e3c1e0cb7', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678f71dd8fdb7.png', 'uploads/vaccine_images/vaccine_678f71dd8fdc2.png', '2025-01-21 15:18:01'),
(28, 'b2a42f6008a8c19e49ecc03b7e704c23', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678f72047255d.png', 'uploads/vaccine_images/vaccine_678f720472567.png', '2025-01-21 15:18:01'),
(29, 'b83aaa70a3d79df7f3ac8abb5dada923', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'cdsvd cdadvgsf', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678f7234a34ab.png', 'uploads/vaccine_images/vaccine_678f7234a34b2.png', '2025-01-21 15:18:01'),
(30, 'dcd8c3b9ce724aaac6786352da6a26aa', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678f73cc6a5f0.png', 'uploads/vaccine_images/vaccine_678f73cc6a5fb.png', '2025-01-21 15:18:01'),
(31, 'a3de0b10fedae6ddbe4aed9297d50f2c', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678f7598f2f5c.jpg', 'uploads/vaccine_images/vaccine_678f7598f2f81.jpg', '2025-01-21 15:18:01'),
(32, '19bfe20c2c219bd1e77ff5cce4fd7eaf', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678f80485dbae.jpg', 'uploads/vaccine_images/vaccine_678f80485dbc9.jpg', '2025-01-21 15:18:01'),
(33, 'a9f1d9916c303ec771d7b06751b33b3b', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678fbd18cb485.jpg', 'uploads/vaccine_images/vaccine_678fbd18cb49b.png', '2025-01-21 15:28:24'),
(34, '1ca4920af3ca667cf75961935feec751', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678fbfa06711d.jpg', 'uploads/vaccine_images/vaccine_678fbfa06713f.jpg', '2025-01-21 15:39:12'),
(35, '823ce0944bc4d07ac88cbd9c4d324f05', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678fbff7b75f7.png', 'uploads/vaccine_images/vaccine_678fbff7b7601.png', '2025-01-21 15:40:39'),
(36, 'e5914239db1c036359600f2b05693f8b', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678fc04109957.jpg', 'uploads/vaccine_images/vaccine_678fc04109962.jpg', '2025-01-21 15:41:53'),
(37, 'bb6d08711fd8c179bf7a9ee819fac2a2', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678fc0a2c6ee1.jpg', 'uploads/vaccine_images/vaccine_678fc0a2c6eef.jpg', '2025-01-21 15:43:30'),
(38, '8575e53e4a397801647e5149393cda6b', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678fc23804f24.jpg', 'uploads/vaccine_images/vaccine_678fc23804f34.jpg', '2025-01-21 15:50:16'),
(39, '8dfb661f9fa4ce95d9d1842f8a75a249', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678fc31e4f0ff.jpg', 'uploads/vaccine_images/vaccine_678fc31e4f113.jpg', '2025-01-21 15:54:06'),
(40, '5acda1a6ba7fd881597cc83771b58a8f', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678fc4eb78070.jpg', 'uploads/vaccine_images/vaccine_678fc4eb7807f.jpg', '2025-01-21 16:01:47'),
(41, '2acaf9bab0090e7b706ccfc0e696749f', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678fc663011cb.jpg', 'uploads/vaccine_images/vaccine_678fc663011e1.jpg', '2025-01-21 16:08:03'),
(42, '08d85a0e7c3f74397f2e8239873b267b', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678fc8b1b7329.jpg', 'uploads/vaccine_images/vaccine_678fc8b1b733c.jpg', '2025-01-21 16:17:53'),
(43, '716bb7498f3dbd404556ac11d2ae9675', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678fc90c63ae7.jpg', 'uploads/vaccine_images/vaccine_678fc90c63af6.jpg', '2025-01-21 16:19:24'),
(44, 'e7cc51aba5191f3b41cb3531a4d30407', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'cdsfgbnhm hgyf', 'asdas', 2, 'chuahua', 'gdfbgbfbb', 'uploads/pet_images/pet_678fc9bfe26c0.jpg', 'uploads/vaccine_images/vaccine_678fc9bfe26dd.jpg', '2025-01-21 16:22:23'),
(45, '5599142251789162ec9a9b7f89240310', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678fca0762fb0.jpg', 'uploads/vaccine_images/vaccine_678fca0762fc0.jpg', '2025-01-21 16:23:35'),
(46, '0f33242ce1dd7cbbc86207993c4bfdf1', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678fce2bdce40.jpg', 'uploads/vaccine_images/vaccine_678fce2bdce54.jpg', '2025-01-21 16:41:15'),
(47, 'a2554db7dbf7faa4a5c1667bbc4f3459', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678fce5867855.jpg', 'uploads/vaccine_images/vaccine_678fce5867865.jpg', '2025-01-21 16:42:00'),
(48, 'c2b52fea18f3123edac9629dd4610391', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678fce71c16af.jpg', 'uploads/vaccine_images/vaccine_678fce71c16bd.jpg', '2025-01-21 16:42:25'),
(49, '8df3330a2662899684b07213a1d81f2e', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678fce8aa85ff.jpg', 'uploads/vaccine_images/vaccine_678fce8aa860e.jpg', '2025-01-21 16:42:50'),
(50, 'dbd3fae15bdf65f89ce2302fa6faa5c2', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'cdsvd cdadvgsf', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678fcf5228499.jpg', 'uploads/vaccine_images/vaccine_678fcf52284a9.jpg', '2025-01-21 16:46:10'),
(51, '29dbc77e91fdaa166cfcfff97332313d', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_678fcf7c106bd.jpg', 'uploads/vaccine_images/vaccine_678fcf7c106cd.jpg', '2025-01-21 16:46:52'),
(52, '7d42af934d708bae073513406a6be035', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_679091c741bd3.jpg', 'uploads/vaccine_images/vaccine_679091c741be1.jpg', '2025-01-22 06:35:51'),
(53, '9d80901d1da67e6caa7216d9922943e0', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_6790934fc3733.jpg', 'uploads/vaccine_images/vaccine_6790934fc3747.jpg', '2025-01-22 06:42:23'),
(54, '2dc0aa603c9d91905beb7aa8e3158269', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_679093797f793.jpg', 'uploads/vaccine_images/vaccine_679093797f7a3.jpg', '2025-01-22 06:43:05'),
(55, '717b3543e4b25499b1b2f2fd3e22866a', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_6790957f8e8ec.png', 'uploads/vaccine_images/vaccine_6790957f8e8f8.png', '2025-01-22 06:51:43'),
(56, 'a54a0fcfc041dfe23e4321c4c448962f', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_6790a46c2d4c7.jpg', 'uploads/vaccine_images/vaccine_6790a46c2d4de.jpg', '2025-01-22 07:55:24'),
(57, '150b19ec9d4f0c8ec8c170f46adb7d7f', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_679192506206a.jpg', 'uploads/vaccine_images/vaccine_679192506207c.jpg', '2025-01-23 00:50:25'),
(58, '99c528fd418fe3b5dad06cba8e205c4b', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_6791925062142.jpg', 'uploads/vaccine_images/vaccine_679192506214e.jpg', '2025-01-23 00:50:25'),
(59, 'f063a9f4216da1986e8d12d23dfd7b7b', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_67919270d9931.jpg', 'uploads/vaccine_images/vaccine_67919270d993e.jpg', '2025-01-23 00:50:56'),
(60, 'e42ba8f5e890249944a0de7509c2f51e', 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sadaefrvtbhfngfbtgrehg', 'asdas', 2, 'chuahua', 'asdas', 'uploads/pet_images/pet_6791f14e93d8b.jpg', 'uploads/vaccine_images/vaccine_6791f14e93dab.jpg', '2025-01-23 07:35:42');

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `id` int(11) NOT NULL,
  `reportParty` varchar(255) NOT NULL,
  `phone_number` varchar(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `pet_type` varchar(255) NOT NULL,
  `pet_breed` varchar(255) NOT NULL,
  `additional_info` text DEFAULT NULL,
  `pet_image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report`
--

INSERT INTO `report` (`id`, `reportParty`, `phone_number`, `email`, `address`, `pet_type`, `pet_breed`, `additional_info`, `pet_image`, `created_at`) VALUES
(1, 'azhi mangbobote', '09064075290', 'user@gmail.com', 'sdasdas', '0', 'chuahua', 'asdas', 'uploads/pet_cruelty/pet_6792020b297ee.jpg', '2025-01-23 08:47:07');

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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `missing`
--
ALTER TABLE `missing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `register`
--
ALTER TABLE `register`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `missing`
--
ALTER TABLE `missing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `register`
--
ALTER TABLE `register`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
