-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 05, 2024 at 05:43 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ITProjektas`
--

-- --------------------------------------------------------

--
-- Table structure for table `ClaimParts`
--

CREATE TABLE `ClaimParts` (
  `part_id` int(11) NOT NULL,
  `claim_id` int(11) NOT NULL,
  `part_name` varchar(50) DEFAULT NULL,
  `repair_cost` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ClaimParts`
--

INSERT INTO `ClaimParts` (`part_id`, `claim_id`, `part_name`, `repair_cost`) VALUES
(25, 14, 'Windows', 1.00),
(26, 14, 'Lights', 10.00),
(27, 14, 'Fenders', 10.00),
(28, 14, 'Doors', 10.00),
(29, 14, 'Bumpers', 10.00),
(30, 14, 'Mirrors', 10.00),
(31, 15, 'Windows', 0.00),
(32, 15, 'Lights', 0.00),
(33, 15, 'Fenders', 0.00),
(34, 15, 'Doors', 0.00),
(35, 15, 'Bumpers', 0.00),
(36, 15, 'Mirrors', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `ClaimPhotos`
--

CREATE TABLE `ClaimPhotos` (
  `photo_id` int(11) NOT NULL,
  `claim_id` int(11) NOT NULL,
  `photo_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ClaimPhotos`
--

INSERT INTO `ClaimPhotos` (`photo_id`, `claim_id`, `photo_path`) VALUES
(5, 14, 'uploads/photo_672a239abdb565.13290181_gettyimages-667707884-612x612.jpg'),
(6, 15, 'uploads/photo_672a249e2739d0.88638733_images.jpeg'),
(7, 16, 'uploads/photo_672a3099d363f2.69132170_new-spider-man-across-the-spider-verse-16-9-wallpapers-v0-xo8o2vb9tnhb1.png');

-- --------------------------------------------------------

--
-- Table structure for table `Claims`
--

CREATE TABLE `Claims` (
  `claim_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Pending','Approved','Denied') DEFAULT 'Pending',
  `repair_cost` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Claims`
--

INSERT INTO `Claims` (`claim_id`, `user_id`, `description`, `status`, `repair_cost`, `created_at`) VALUES
(14, 6, 'my car go oww', 'Approved', 51.00, '2024-11-05 13:54:34'),
(15, 7, 'oh no', 'Denied', 0.00, '2024-11-05 13:58:54'),
(16, 6, 'thats not a car', 'Pending', 0.00, '2024-11-05 14:50:01');

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `years_no_claims` int(11) DEFAULT 0,
  `num_services` int(11) DEFAULT 1,
  `referrals` int(11) DEFAULT 0,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `profit_loss_score` decimal(10,2) DEFAULT 0.00,
  `referrer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`user_id`, `name`, `years_no_claims`, `num_services`, `referrals`, `email`, `password`, `is_admin`, `profit_loss_score`, `referrer_id`) VALUES
(6, 'audxius', 50, 155, 0, 'audxius@gmail.com', '$2y$10$HMoSlA4sWM7ngLrb2M58.O9nKIzDxyhxbZXgEwSasSQADz8/gxEOq', 1, 0.00, NULL),
(7, 'nedaxas', 0, 1, 0, 'nedaxas@gmail.com', '$2y$10$mqgsrXeEmzpDXENXaUtFRuCUddg5/3Wg9EpQqnxD9fwFS3TXo.DWO', 0, 0.00, 6),
(8, 'asta', 0, 1, 0, 'asta@gmail.com', '$2y$10$.sdtWGgPeRB6gZok3v1Q2uOgzPVPH2wRm3yd/.HZ8Vw21.JfCBbuy', 0, 0.00, 6),
(10, 'gmail', 0, 1, 0, 'gmail@gmail.com', '$2y$10$073mPAvp2P0gsv6v3Sgk6uNXnlDUeT4ni6PYWzR6lsSb5okjnZ2l6', 0, 0.00, 8);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ClaimParts`
--
ALTER TABLE `ClaimParts`
  ADD PRIMARY KEY (`part_id`),
  ADD KEY `claim_id` (`claim_id`);

--
-- Indexes for table `ClaimPhotos`
--
ALTER TABLE `ClaimPhotos`
  ADD PRIMARY KEY (`photo_id`),
  ADD KEY `claim_id` (`claim_id`);

--
-- Indexes for table `Claims`
--
ALTER TABLE `Claims`
  ADD PRIMARY KEY (`claim_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ClaimParts`
--
ALTER TABLE `ClaimParts`
  MODIFY `part_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `ClaimPhotos`
--
ALTER TABLE `ClaimPhotos`
  MODIFY `photo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `Claims`
--
ALTER TABLE `Claims`
  MODIFY `claim_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ClaimParts`
--
ALTER TABLE `ClaimParts`
  ADD CONSTRAINT `ClaimParts_ibfk_1` FOREIGN KEY (`claim_id`) REFERENCES `Claims` (`claim_id`) ON DELETE CASCADE;

--
-- Constraints for table `ClaimPhotos`
--
ALTER TABLE `ClaimPhotos`
  ADD CONSTRAINT `ClaimPhotos_ibfk_1` FOREIGN KEY (`claim_id`) REFERENCES `Claims` (`claim_id`) ON DELETE CASCADE;

--
-- Constraints for table `Claims`
--
ALTER TABLE `Claims`
  ADD CONSTRAINT `Claims_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
