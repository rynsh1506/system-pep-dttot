-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2026 at 04:47 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cadeb_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `approval_requests`
--

CREATE TABLE `approval_requests` (
  `id` int(11) NOT NULL,
  `candidate_id` int(11) DEFAULT NULL,
  `type` enum('EDIT','DELETE') NOT NULL,
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_data`)),
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_data`)),
  `requester_id` int(11) DEFAULT NULL,
  `l2_status` enum('PENDING','APPROVED','REJECTED') DEFAULT 'PENDING',
  `l2_approver_id` int(11) DEFAULT NULL,
  `l2_notes` text DEFAULT NULL,
  `l3_status` enum('PENDING','APPROVED','REJECTED') DEFAULT 'PENDING',
  `l3_approver_id` int(11) DEFAULT NULL,
  `l3_notes` text DEFAULT NULL,
  `final_status` enum('PENDING','COMPLETED','REJECTED') DEFAULT 'PENDING',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `id` int(11) NOT NULL,
  `nama_cadeb` varchar(255) NOT NULL,
  `no_identitas` varchar(50) NOT NULL,
  `nama_pasangan` varchar(255) DEFAULT NULL,
  `no_identitas_pasangan` varchar(50) DEFAULT NULL,
  `keterangan_pep` varchar(100) NOT NULL,
  `go_live` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `kategori` varchar(50) NOT NULL DEFAULT 'Cadeb'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `level` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `level`, `created_at`) VALUES
(1, 'staff', '$2y$10$5OO9kYOBdRKiB9yKQfKV4.RXycjJ1spxBIssCV/XMmKoL1fE47dAG', 'Staff Input (L1)', 1, '2026-01-20 02:48:51'),
(2, 'supervisor', '$2y$10$5OO9kYOBdRKiB9yKQfKV4.RXycjJ1spxBIssCV/XMmKoL1fE47dAG', 'Supervisor (L2)', 2, '2026-01-20 02:48:51'),
(3, 'manager', '$2y$10$5OO9kYOBdRKiB9yKQfKV4.RXycjJ1spxBIssCV/XMmKoL1fE47dAG', 'Manager (L3)', 3, '2026-01-20 02:48:51'),
(4, 'admin', '$2y$10$5OO9kYOBdRKiB9yKQfKV4.RXycjJ1spxBIssCV/XMmKoL1fE47dAG', 'Super Admin (L4)', 4, '2026-01-20 02:48:51'),
(5, '15080159', '$2y$10$5OO9kYOBdRKiB9yKQfKV4.RXycjJ1spxBIssCV/XMmKoL1fE47dAG', 'Nur Azizah', 1, '2026-03-17 06:54:34'),
(6, '12060050', '$2y$10$5OO9kYOBdRKiB9yKQfKV4.RXycjJ1spxBIssCV/XMmKoL1fE47dAG', 'Megawati', 1, '2026-04-07 03:46:43'),
(7, '18050058', '$2y$10$5OO9kYOBdRKiB9yKQfKV4.RXycjJ1spxBIssCV/XMmKoL1fE47dAG', 'Ghessa Utomo Setiawan', 2, '2026-04-12 15:24:32'),
(8, '15050097', '$2y$10$5OO9kYOBdRKiB9yKQfKV4.RXycjJ1spxBIssCV/XMmKoL1fE47dAG', 'Robert Syahratoe', 3, '2026-04-12 15:24:32'),
(9, '11060024', '$2y$10$5OO9kYOBdRKiB9yKQfKV4.RXycjJ1spxBIssCV/XMmKoL1fE47dAG', 'Siti Annisa', 1, '2026-04-12 15:24:56'),
(10, '15080164', '$2y$10$5OO9kYOBdRKiB9yKQfKV4.RXycjJ1spxBIssCV/XMmKoL1fE47dAG', 'Siti Maisarah', 1, '2026-04-13 09:47:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `approval_requests`
--
ALTER TABLE `approval_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidate_id` (`candidate_id`),
  ADD KEY `requester_id` (`requester_id`);

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `approval_requests`
--
ALTER TABLE `approval_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approval_requests`
--
ALTER TABLE `approval_requests`
  ADD CONSTRAINT `approval_requests_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `approval_requests_ibfk_2` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
