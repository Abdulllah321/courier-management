-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 29, 2024 at 02:13 AM
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
-- Database: `courier_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `agents`
--

CREATE TABLE `agents` (
  `id` varchar(30) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `branch` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agents`
--

INSERT INTO `agents` (`id`, `username`, `password`, `branch`) VALUES
('agent_669e577cab37f8.59539302', 'Abdullah', '$2y$10$Mqixs/1C.NDPRCdCBjeXBegsZlF/oTLVX8M2TgvgI5ok7KHW1ixoS', ''),
('agent_669e5795acf918.31025742', 'Yahya', '$2y$10$7LsrCiN7Sy6hIo6tGyG2heCsRqfKEY.LmIkSFf.NZxwASZFYrdOVi', '');

-- --------------------------------------------------------

--
-- Table structure for table `agent_branches`
--

CREATE TABLE `agent_branches` (
  `agent_id` varchar(255) NOT NULL,
  `branch_id` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agent_branches`
--

INSERT INTO `agent_branches` (`agent_id`, `branch_id`) VALUES
('agent_669e577cab37f8.59539302', 2),
('agent_669e577cab37f8.59539302', 3),
('agent_669e577cab37f8.59539302', 4),
('agent_669e5795acf918.31025742', 8),
('agent_669e5795acf918.31025742', 9);

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `branch_name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state_province` varchar(100) NOT NULL,
  `zip_postal_code` varchar(20) NOT NULL,
  `country` varchar(100) NOT NULL,
  `contact_person` varchar(100) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `branch_type` enum('main','sub','franchise') NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  `gps_coordinates` point NOT NULL,
  `branch_manager` varchar(100) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `branch_name`, `address`, `city`, `state_province`, `zip_postal_code`, `country`, `contact_person`, `phone_number`, `email_address`, `branch_type`, `status`, `gps_coordinates`, `branch_manager`, `deleted`) VALUES
(2, 'Main Branch', '123 Main St', 'Sargodha', 'Punjab', '40100', 'Pakistan', 'John Doe', '03001234567', 'john.doe@example.com', 'main', 'inactive', 0x, 'Jane Smith', 0),
(3, 'Sub Branch 1', '456 Secondary St', 'Lahore', 'Punjab', '54000', 'Pakistan', 'Alice Johnson', '03111234567', 'alice.johnson@example.com', 'sub', 'inactive', 0x, 'Bob Brown', 0),
(4, 'Sub Branch 2', '789 Another St', 'Karachi', 'Sindh', '74000', 'Pakistan', 'Charlie Williams', '03221234567', 'charlie.williams@example.com', 'sub', 'active', 0x, 'David Clark', 0),
(5, 'Franchise Branch 1', '101 First St', 'Islamabad', 'Islamabad Capital Territory', '44000', 'Pakistan', 'Eve Davis', '03331234567', 'eve.davis@example.com', 'franchise', 'active', 0x, 'Frank Martin', 0),
(6, 'Franchise Branch 2', '202 Second St', 'Rawalpindi', 'Punjab', '46000', 'Pakistan', 'Grace Lee', '03441234567', 'grace.lee@example.com', 'franchise', 'inactive', 0x, 'Henry Lewis', 0),
(8, 'Aphrodite Case', 'Ullam dolore volupta', 'Est ab proident ad ', 'Natus lorem quia lib', '64433', 'Voluptatibus aut pra', 'Est facilis earum i', '+1 (816) 967-8171', 'zibejad@mailinator.com', 'franchise', 'inactive', 0x, 'Velit voluptas eos ', 1),
(9, 'Aphrodite Case', 'Ullam dolore volupta', 'Est ab proident ad ', 'Natus lorem quia lib', '64433', 'Voluptatibus aut pra', 'Est facilis earum i', '+1 (816) 967-8171', 'zibejad@mailinator.com', 'franchise', 'inactive', 0x, 'Velit voluptas eos ', 1),
(10, 'Aphrodite Case', 'Ullam dolore volupta', 'Est ab proident ad ', 'Natus lorem quia lib', '64433', 'Voluptatibus aut pra', 'Est facilis earum i', '+1 (816) 967-8171', 'zibejad@mailinator.com', 'franchise', 'inactive', 0x, 'Velit voluptas eos ', 0),
(11, 'Aphrodite Case', 'Ullam dolore volupta', 'Est ab proident ad ', 'Natus lorem quia lib', '64433', 'Voluptatibus aut pra', 'Est facilis earum i', '+1 (816) 967-8171', 'zibejad@mailinator.com', 'franchise', 'inactive', 0x, 'Velit voluptas eos ', 1),
(12, 'Aphrodite Case', 'Ullam dolore volupta', 'Est ab proident ad ', 'Natus lorem quia lib', '64433', 'Voluptatibus aut pra', 'Est facilis earum i', '+1 (816) 967-8171', 'zibejad@mailinator.com', 'franchise', 'inactive', 0x, 'Velit voluptas eos ', 0),
(13, 'Shea Phillips', 'Saepe minus sint ali', 'Obcaecati eos dolore', 'Architecto repudiand', '62916', 'Distinctio Dolor il', 'Temporibus blanditii', '+1 (643) 803-8713', 'wigydeha@mailinator.com', 'sub', 'inactive', 0x, 'Earum quia in aliqua', 0),
(14, 'Shea Phillips', 'Saepe minus sint ali', 'Obcaecati eos dolore', 'Architecto repudiand', '62916', 'Distinctio Dolor il', 'Temporibus blanditii', '+1 (643) 803-8713', 'wigydeha@mailinator.com', 'sub', 'inactive', 0x, 'Earum quia in aliqua', 0),
(15, 'Sierra Stevenson', 'Molestiae anim minus', 'Soluta tempor offici', 'Sed veniam tempor n', '60934', 'Voluptatibus dolor ', 'Ut odit sunt dolor n', '+1 (677) 922-7568', 'zuwap@mailinator.com', 'sub', 'active', 0x, 'Nihil laboriosam in', 0),
(16, 'Sierra Stevenson', 'Molestiae anim minus', 'Soluta tempor offici', 'Sed veniam tempor n', '60934', 'Voluptatibus dolor c', 'Ut odit sunt dolor n', '+1 (677) 922-7568', 'zuwap@mailinator.com', 'sub', 'active', 0x, 'Nihil laboriosam in', 0),
(17, 'Sierra Stevenson', 'Molestiae anim minus', 'Soluta tempor offici', 'Sed veniam tempor n', '60934', 'Voluptatibus dolor c', 'Ut odit sunt dolor n', '+1 (677) 922-7568', 'zuwap@mailinator.com', 'sub', 'active', 0x, 'Nihil laboriosam in', 0),
(18, 'Sierra Stevenson', 'Molestiae anim minus', 'Soluta tempor offici', 'Sed veniam tempor n', '60934', 'Voluptatibus dolor c', 'Ut odit sunt dolor n', '+1 (677) 922-7568', 'zuwap@mailinator.com', 'sub', 'active', 0x, 'Nihil laboriosam in', 0),
(19, 'Teagan Dennis', 'Sunt non est mollit ', 'Deleniti expedita si', 'Pariatur Consequatu', '20206', 'Cupiditate dolorem s', 'Id numquam esse ame', '+1 (828) 632-5168', 'dysotixoqu@mailinator.com', 'franchise', 'inactive', 0x, 'Irure illo quisquam ', 0),
(20, 'Teagan Dennis', 'Sunt non est mollit ', 'Deleniti expedita si', 'Pariatur Consequatu', '20206', 'Cupiditate dolorem s', 'Id numquam esse ame', '+1 (828) 632-5168', 'dysotixoqu@mailinator.com', 'franchise', 'inactive', 0x, 'Irure illo quisquam ', 0),
(21, 'Teagan Dennis', 'Sunt non est mollit ', 'Deleniti expedita si', 'Pariatur Consequatu', '20206', 'Cupiditate dolorem s', 'Id numquam esse ame', '+1 (828) 632-5168', 'dysotixoqu@mailinator.com', 'franchise', 'inactive', 0x, 'Irure illo quisquam ', 0),
(22, 'Teagan Dennis', 'Sunt non est mollit ', 'Deleniti expedita si', 'Pariatur Consequatu', '20206', 'Cupiditate dolorem s', 'Id numquam esse ame', '+1 (828) 632-5168', 'dysotixoqu@mailinator.com', 'franchise', 'inactive', 0x, 'Irure illo quisquam ', 0),
(23, 'Teagan Dennis', 'Sunt non est mollit ', 'Deleniti expedita si', 'Pariatur Consequatu', '20206', 'Cupiditate dolorem s', 'Id numquam esse ame', '+1 (828) 632-5168', 'dysotixoqu@mailinator.com', 'franchise', 'inactive', 0x, 'Irure illo quisquam ', 0),
(24, 'Teagan Dennis', 'Sunt non est mollit ', 'Deleniti expedita si', 'Pariatur Consequatu', '20206', 'Cupiditate dolorem s', 'Id numquam esse ame', '+1 (828) 632-5168', 'dysotixoqu@mailinator.com', 'franchise', 'inactive', 0x, 'Irure illo quisquam ', 0),
(25, 'Teagan Dennis', 'Sunt non est mollit ', 'Deleniti expedita si', 'Pariatur Consequatu', '20206', 'Cupiditate dolorem s', 'Id numquam esse ame', '+1 (828) 632-5168', 'dysotixoqu@mailinator.com', 'franchise', 'inactive', 0x, 'Irure illo quisquam ', 0),
(26, 'Teagan Dennis', 'Sunt non est mollit ', 'Deleniti expedita si', 'Pariatur Consequatu', '20206', 'Cupiditate dolorem s', 'Id numquam esse ame', '+1 (828) 632-5168', 'dysotixoqu@mailinator.com', 'franchise', 'inactive', 0x, 'Irure illo quisquam ', 0),
(27, 'Teagan Dennis', 'Sunt non est mollit ', 'Deleniti expedita si', 'Pariatur Consequatu', '20206', 'Cupiditate dolorem s', 'Id numquam esse ame', '+1 (828) 632-5168', 'dysotixoqu@mailinator.com', 'franchise', 'inactive', 0x, 'Irure illo quisquam ', 0),
(28, 'Teagan Dennis', 'Sunt non est mollit ', 'Deleniti expedita si', 'Pariatur Consequatu', '20206', 'Cupiditate dolorem s', 'Id numquam esse ame', '+1 (828) 632-5168', 'dysotixoqu@mailinator.com', 'franchise', 'inactive', 0x, 'Irure illo quisquam ', 0);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` char(36) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text NOT NULL,
  `deleted` tinyint(1) DEFAULT 0,
  `created_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `first_name`, `last_name`, `email`, `phone`, `address`, `deleted`, `created_at`) VALUES
('customer_618289c0-487b-11ef-af40-0a0', 'Jon', 'Doe', 'test@example.us', '6019521325', '1600 Amphitheatre Parkway\r\nApartment 1', 0, NULL),
('customer_61828ed6-487b-11ef-af40-0a0', 'João', 'Souza Silva', 'teste@exemplo.us', '3121286800', 'Av. dos Andradas, 3000\r\nAndar 2, Apartamento 1', 0, NULL),
('customer_7b944fbc3db61be4bf8c9dc4335', 'Abdullah', 'Sufyan', 'abdullahsufyan2007@gmail.com', '03233297166', 'B-56, Petal residency, Glistan-e-johar, Block 9', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `status`, `created_at`, `url`) VALUES
(1, 1, 'A new branch \'Shea Phillips\' has been added.', 'read', '2024-07-21 18:57:46', 'view_branch.php?id=8'),
(2, 1, 'A new branch \'Sierra Stevenson\' has been added.', 'read', '2024-07-21 18:57:56', 'view_branch.php?id=8'),
(3, 1, 'A new branch \'Sierra Stevenson\' has been added.', 'read', '2024-07-21 19:38:07', 'view_branch.php?id=8'),
(4, 1, 'A new branch \'Sierra Stevenson\' has been added.', 'read', '2024-07-21 19:40:23', 'view_branch.php?id=8'),
(5, 1, 'A new branch \'Sierra Stevenson\' has been added.', 'read', '2024-07-21 19:40:35', 'view_branch.php?id=8'),
(6, 1, 'Branch \'Main Branch\' has been updated.', 'read', '2024-07-21 20:11:12', 'view_branch.php?id=8'),
(7, 1, 'Branch \'Main Branch\' has been updated.', 'read', '2024-07-21 20:24:41', 'view_branch.php?id=8'),
(8, 1, 'A new branch \'Teagan Dennis\' has been added.', 'read', '2024-07-21 22:34:01', 'view_branch.php?id=28'),
(9, 1, 'Branch \'Sub Branch 1\' has been updated.', 'read', '2024-07-22 01:43:57', 'view_branch.php?id=3'),
(10, 1, 'Branch \'Sierra Stevenson\' has been updated.', 'read', '2024-07-22 02:41:01', 'view_branch.php?id=15'),
(11, 1, 'A new branch \'\' has been added.', 'read', '2024-07-22 06:14:08', 'view_branch.php?id='),
(12, 1, 'A new agent \'and\' has been added.', 'read', '2024-07-22 06:50:26', 'manage_agents.php?page=\'\''),
(13, 1, 'A new agent \'admina1\' has been added.', 'read', '2024-07-22 06:54:25', 'manage_agents.php?page=\'\''),
(14, 1, 'A new agent \'Abdullah12\' has been added.', 'read', '2024-07-22 08:49:51', 'manage_agents.php?page=6'),
(15, 1, 'A new agent \'Abdullah\' has been added.', 'read', '2024-07-22 08:52:21', 'manage_agents.php?page=1'),
(16, 1, 'A new agent \'Abdullah\' has been added.', 'read', '2024-07-22 09:26:39', 'manage_agents.php?page=1'),
(17, 1, 'A new agent \'Abdullah\' has been added.', 'read', '2024-07-22 12:44:51', 'manage_agents.php?page=1'),
(18, 1, 'Agent \'Abdullah\' has been updated.', 'read', '2024-07-22 12:48:39', 'manage_agents.php'),
(19, 1, 'A new agent \'Abdullah\' has been added.', 'read', '2024-07-22 12:58:36', 'manage_agents.php?page=1'),
(20, 1, 'A new agent \'Yahya\' has been added.', 'read', '2024-07-22 12:59:01', 'manage_agents.php?page=1'),
(21, 1, 'Agent \'Yahya\' has been updated.', 'read', '2024-07-28 17:47:13', 'manage_agents.php'),
(22, 1, 'Agent \'Yahya\' has been updated.', 'read', '2024-07-28 17:47:34', 'manage_agents.php'),
(26, 1, 'Your parcel with tracking ID courier_cf36d7bccb02 has been created.', 'read', '2024-07-28 23:43:02', 'report.php?parcel_id=courier_cf36d7bccb02'),
(27, 1, 'You have a parcel from Jon Doe with tracking ID courier_cf36d7bccb02.', 'read', '2024-07-28 23:43:02', 'report.php?parcel_id=courier_cf36d7bccb02'),
(28, 1, 'Your parcel with tracking ID courier_a7f6f039863b has been created.', 'unread', '2024-07-29 00:09:52', 'report.php?parcel_id=courier_a7f6f039863b');

-- --------------------------------------------------------

--
-- Table structure for table `parcels`
--

CREATE TABLE `parcels` (
  `parcel_id` char(36) NOT NULL,
  `sender_id` char(36) NOT NULL,
  `receiver_id` char(36) NOT NULL,
  `weight` decimal(10,2) NOT NULL,
  `dimensions` varchar(50) NOT NULL,
  `status` enum('Pending','Shipped','Delivered') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted` tinyint(1) DEFAULT 0,
  `delivery_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parcels`
--

INSERT INTO `parcels` (`parcel_id`, `sender_id`, `receiver_id`, `weight`, `dimensions`, `status`, `created_at`, `deleted`, `delivery_date`) VALUES
('courier_00c750b38da325e91ff119749576', 'customer_618289c0-487b-11ef-af40-0a0', 'customer_7b944fbc3db61be4bf8c9dc4335', 12.00, '1500+300', 'Pending', '2024-07-28 23:37:07', 0, '2024-07-31'),
('courier_87c91454b77d', 'customer_618289c0-487b-11ef-af40-0a0', 'customer_7b944fbc3db61be4bf8c9dc4335', 12.00, '1500+300', 'Pending', '2024-07-28 23:38:08', 0, '2024-07-31'),
('courier_a7f6f039863b', 'customer_61828ed6-487b-11ef-af40-0a0', 'customer_7b944fbc3db61be4bf8c9dc4335', 2.00, '23', 'Pending', '2024-07-29 00:09:46', 0, '2024-08-02'),
('courier_cf36d7bccb02', 'customer_618289c0-487b-11ef-af40-0a0', 'customer_7b944fbc3db61be4bf8c9dc4335', 12.00, '1500+300', 'Pending', '2024-07-28 23:43:01', 0, '2024-07-31'),
('parcel_6182af4c-487b-11ef-af40-0a002', 'customer_618289c0-487b-11ef-af40-0a0', 'customer_61828ed6-487b-11ef-af40-0a0', 12.00, '1500 350', 'Pending', '2024-07-22 22:40:22', 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `agent_branches`
--
ALTER TABLE `agent_branches`
  ADD PRIMARY KEY (`agent_id`,`branch_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `parcels`
--
ALTER TABLE `parcels`
  ADD PRIMARY KEY (`parcel_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `agent_branches`
--
ALTER TABLE `agent_branches`
  ADD CONSTRAINT `agent_branches_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `admins` (`id`);

--
-- Constraints for table `parcels`
--
ALTER TABLE `parcels`
  ADD CONSTRAINT `parcels_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `parcels_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `customers` (`customer_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
