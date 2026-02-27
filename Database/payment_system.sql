-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 16, 2025 at 08:24 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `payment_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `payment_fees`
--

CREATE TABLE `payment_fees` (
  `id` int(11) NOT NULL,
  `payment_form_id` int(11) NOT NULL,
  `fee_name` varchar(255) NOT NULL,
  `fee_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_fees`
--

INSERT INTO `payment_fees` (`id`, `payment_form_id`, `fee_name`, `fee_amount`, `created_at`) VALUES
(1, 1, 'P100,000 and below', 0.00, '2025-11-13 18:34:10'),
(2, 1, 'Over P100,000 to P200,000', 0.00, '2025-11-13 18:34:10'),
(3, 1, 'Over P200,000', 970.00, '2025-11-13 18:34:10');

-- --------------------------------------------------------

--
-- Table structure for table `payment_forms`
--

CREATE TABLE `payment_forms` (
  `id` int(11) NOT NULL,
  `official_receipt_no` varchar(50) DEFAULT NULL,
  `owner_applicant_name` varchar(255) NOT NULL,
  `project_title` varchar(255) NOT NULL,
  `location` text NOT NULL,
  `date` date NOT NULL,
  `division` varchar(100) DEFAULT NULL,
  `project_type` varchar(10) DEFAULT NULL,
  `project_cost` decimal(15,2) DEFAULT 0.00,
  `multiplier` decimal(10,2) DEFAULT 0.00,
  `calculated_cost` decimal(15,2) DEFAULT 0.00,
  `floor_area` decimal(10,2) DEFAULT 0.00,
  `additional_lot_area` decimal(10,2) DEFAULT 0.00,
  `total_area` decimal(10,2) DEFAULT 0.00,
  `total_fees` decimal(10,2) DEFAULT 0.00,
  `surcharge_percentage` int(11) DEFAULT 0,
  `surcharge_amount` decimal(10,2) DEFAULT 0.00,
  `grand_total` decimal(10,2) DEFAULT 0.00,
  `prepared_by` varchar(100) DEFAULT NULL,
  `assessed_by` varchar(100) DEFAULT NULL,
  `status` enum('draft','approved','paid') DEFAULT 'draft',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_forms`
--

INSERT INTO `payment_forms` (`id`, `official_receipt_no`, `owner_applicant_name`, `project_title`, `location`, `date`, `division`, `project_type`, `project_cost`, `multiplier`, `calculated_cost`, `floor_area`, `additional_lot_area`, `total_area`, `total_fees`, `surcharge_percentage`, `surcharge_amount`, `grand_total`, `prepared_by`, `assessed_by`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'OP-2025-00001', 'Mark Spinus Jonson', 'Propose 1 Story House', 'Barangay. Malaruhatan, Lian, Batangas', '2025-11-14', '', 'A', 1170000.00, 30000.00, 1170000.00, 39.00, 200.00, 239.00, 970.00, 0, 0.00, 970.00, 'LOVELY LAXA', 'ENP. Mark Andrei L. Gubac', 'paid', 3, '2025-11-13 18:34:10', '2025-11-16 18:25:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','mpdc_staff') DEFAULT 'mpdc_staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `created_at`, `updated_at`) VALUES
(3, 'admin', '$2y$10$3UGl6MNB7TVTWTchXNimbOt231xY/oFvHjBLBV9pcJsO362JATU2i', 'Administrator', 'admin', '2025-11-13 04:33:04', '2025-11-13 04:33:04'),
(4, 'mpdc', '$2y$10$tlxYlJDT9ufcrEMQD3mnceFCmGeavxQ7c0KOFQyvrRpRlp0dwzNlm', 'MPDC Staff', 'mpdc_staff', '2025-11-13 04:33:04', '2025-11-13 04:33:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `payment_fees`
--
ALTER TABLE `payment_fees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_form_id` (`payment_form_id`);

--
-- Indexes for table `payment_forms`
--
ALTER TABLE `payment_forms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `official_receipt_no` (`official_receipt_no`),
  ADD KEY `created_by` (`created_by`);

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
-- AUTO_INCREMENT for table `payment_fees`
--
ALTER TABLE `payment_fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment_forms`
--
ALTER TABLE `payment_forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payment_fees`
--
ALTER TABLE `payment_fees`
  ADD CONSTRAINT `payment_fees_ibfk_1` FOREIGN KEY (`payment_form_id`) REFERENCES `payment_forms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_forms`
--
ALTER TABLE `payment_forms`
  ADD CONSTRAINT `payment_forms_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
