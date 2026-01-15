-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 15, 2026 at 10:33 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_pharmastock`
--

-- --------------------------------------------------------

--
-- Table structure for table `registration_requests`
--

CREATE TABLE `registration_requests` (
  `id` int NOT NULL,
  `staff_id` varchar(50) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `request_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `medicine_name` varchar(100) DEFAULT NULL,
  `qty` int DEFAULT NULL,
  `price_per_unit` double DEFAULT NULL,
  `total_price` double DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `user_id`, `medicine_name`, `qty`, `price_per_unit`, `total_price`, `status`, `created_at`) VALUES
(1, 1, 'Amoxicillin', 10, 5000, 50000, 'approved', '2025-12-13 17:44:12'),
(2, 1, 'Amoxicillin', 10, 50000, 500000, 'approved', '2025-12-13 18:46:16'),
(3, 1, 'Amoxicillin 500mg', 10, 5000, 50000, 'approved', '2025-12-13 19:02:50'),
(4, 1, 'OBH Combi', 10, 1000, 10000, 'approved', '2025-12-13 19:04:18'),
(5, 1, 'Amoxicillin 500mg', 10, 45000, 450000, 'approved', '2025-12-13 19:10:28'),
(6, 1, 'Amoxicillin 500mg', 12, 45000, 540000, 'approved', '2025-12-13 19:14:15'),
(7, 1, 'Amoxicillin 500mg', 2, 45000, 90000, 'approved', '2025-12-13 19:47:38'),
(8, 1, 'Amoxicillin 500mg', 1, 45000, 45000, 'approved', '2025-12-13 20:50:37'),
(9, 1, 'Amoxicillin 500mg', 2, 45000, 90000, 'approved', '2025-12-13 21:26:57'),
(10, 1, 'Amoxicillin 500mg', 1, 45000, 45000, 'approved', '2025-12-13 21:34:51'),
(11, 1, 'Amoxicillin 500mg', 2, 45000, 90000, 'approved', '2025-12-14 07:56:43'),
(12, 1, 'OBH Combi', 2, 15000, 30000, 'approved', '2025-12-14 08:19:01'),
(13, 1, 'Amoxicillin 500mg', 1, 45000, 45000, 'approved', '2025-12-14 08:19:01'),
(14, 1, 'Amoxicillin 500mg', 2, 45000, 90000, 'approved', '2025-12-15 11:38:36'),
(15, 1, 'Amoxicillin 500mg', 12, 45000, 540000, 'approved', '2025-12-16 10:23:49'),
(16, 12345, 'Amoxicillin 500mg', 1, 45000, 45000, 'approved', '2025-12-17 18:47:20'),
(17, 12345, 'OBH Combi', 1, 15000, 15000, 'approved', '2025-12-18 03:53:15'),
(18, 12345, 'OBH Combi', 1, 15000, 15000, 'rejected', '2025-12-18 04:26:15'),
(19, 12345, 'Amoxicillin 500mg', 1, 45000, 45000, 'approved', '2025-12-18 04:26:15'),
(20, 12345, 'Vitamin C 1000mg', 2, 35000, 70000, 'rejected', '2025-12-19 12:19:06'),
(21, 12345, 'Amoxicillin 500mg', 1, 45000, 45000, 'rejected', '2025-12-19 12:19:06'),
(22, 12345, 'Amoxicillin 500mg', 1, 45000, 45000, 'rejected', '2025-12-19 12:23:24'),
(23, 12345, 'OBH Combi', 2, 15000, 30000, 'rejected', '2025-12-19 12:23:24'),
(24, 12345, 'Amoxicillin 500mg', 2, 45000, 90000, 'approved', '2026-01-15 04:34:37'),
(25, 152021158, 'Amoxicillin 500mg', 2, 45000, 90000, 'approved', '2026-01-15 04:57:45'),
(26, 12345, 'Amoxicillin 500mg', 1, 45000, 45000, 'approved', '2026-01-15 10:25:10'),
(27, 12345, 'Betadine Solution', 1, 25000, 25000, 'rejected', '2026-01-15 10:30:20');

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

CREATE TABLE `stocks` (
  `id` int NOT NULL,
  `medicine_name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `stock_qty` int NOT NULL,
  `unit` varchar(20) NOT NULL,
  `price` int NOT NULL,
  `expiry_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `stocks`
--

INSERT INTO `stocks` (`id`, `medicine_name`, `category`, `stock_qty`, `unit`, `price`, `expiry_date`) VALUES
(1, 'Amoxicillin 500mg', 'Antibiotik', 98, 'Box', 45000, '2025-12-30'),
(2, 'Paracetamol 500mg', 'Analgesik', 500, 'Strip', 5000, '2026-05-20'),
(3, 'Vitamin C 1000mg', 'Vitamin', 200, 'Botol', 35000, '2024-11-15'),
(4, 'OBH Combi', 'Sirup Batuk', 62, 'Botol', 15000, '2025-01-10'),
(5, 'Betadine Solution', 'Antiseptik', 50, 'Botol', 25000, '2026-08-01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nip` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nip`, `name`, `password`, `role`, `status`) VALUES
(1, '12345', 'Budi Staff', '12345', 'staff', 'active'),
(2, '152021158', 'Raha nugraha', '12345', 'staff', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `registration_requests`
--
ALTER TABLE `registration_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_id` (`staff_id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nip` (`nip`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `registration_requests`
--
ALTER TABLE `registration_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
