-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 29, 2025 at 01:30 PM
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
-- Database: `job_bp`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `application_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `applicant_name` varchar(255) NOT NULL,
  `applicant_email` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `cover_letter` text DEFAULT NULL,
  `resume_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','reviewed','accepted','rejected') DEFAULT 'pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`application_id`, `job_id`, `applicant_name`, `applicant_email`, `user_id`, `cover_letter`, `resume_path`, `status`, `applied_at`) VALUES
(1, 1, 'Admin', 'admina@gmail.com', NULL, 'I loved reading books ', 'uploads/resumes/1756434279_JOB-BROAD-PLATFORM (1).docx', 'pending', '2025-08-29 02:24:39'),
(2, 1, 'Admin', 'admin@gmail.com', NULL, 'because i love books', 'uploads/resumes/1756435067_JOB-BROAD-PLATFORM (1).docx', 'pending', '2025-08-29 02:37:47'),
(3, 1, 'Admin', 'admin@gmail.com', NULL, 'i loved books', 'uploads/resumes/1756435437_JOB-BROAD-PLATFORM (1).docx', 'pending', '2025-08-29 02:43:57'),
(4, 2, 'Admin', 'admin@gmail.com', NULL, 'kase malabo mata ko', 'uploads/resumes/1756442811_1756435437_JOB-BROAD-PLATFORM (1).docx', 'pending', '2025-08-29 04:46:51');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `requirements` text DEFAULT NULL,
  `salary` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `employment_type` enum('Full-time','Part-time','Contract','Internship') DEFAULT 'Full-time',
  `contact_name` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(50) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `title`, `description`, `requirements`, `salary`, `location`, `employment_type`, `contact_name`, `contact_email`, `contact_phone`, `category`, `created_at`) VALUES
(1, 'ReadN\'Reflect', 'organizing book and encoding and decoding books', 'should be a bookworm', '1500', 'Bamban', 'Part-time', 'Tina', 'tinabi@gmail.com', '09292525633', NULL, '2025-08-29 02:23:50'),
(2, 'EyeWeart', 'should be good at glasses', 'should have a 4 years degree', '30000', 'Manila', 'Full-time', 'Marites', 'tinabi@gmail.com', '09292525633', NULL, '2025-08-29 03:13:44');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `receiver_email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `job_id`, `sender_id`, `receiver_id`, `receiver_email`, `message`, `sent_at`, `status`, `created_at`) VALUES
(1, 1, 1, 0, 'admin@gmail.com', 'i would like to have an interview with u', '2025-08-29 03:42:28', 'unread', '2025-08-29 11:57:34'),
(2, 1, 1, 0, 'admin@gmail.com', 'i would love to work with u, but for now, are u available for an interview about the job you applied with', '2025-08-29 03:57:58', 'unread', '2025-08-29 11:57:58'),
(3, 1, 1, 0, 'admin@gmail.com', 'i would like to have an interview with u', '2025-08-29 03:58:15', 'unread', '2025-08-29 11:58:15'),
(4, 1, 1, 0, 'admin@gmail.com', 'i would love to work with u', '2025-08-29 04:01:41', 'unread', '2025-08-29 12:01:41'),
(5, 1, 1, 0, 'admin@gmail.com', 'i would like to work with u', '2025-08-29 04:05:07', 'unread', '2025-08-29 12:05:07'),
(6, 1, 1, 0, 'admin@gmail.com', 'thank u so muchh i would like to have an interview with u soon', '2025-08-29 04:20:03', 'unread', '2025-08-29 12:20:03'),
(7, 1, 1, 0, 'admin@gmail.com', 'i would like to work with u', '2025-08-29 04:52:14', 'unread', '2025-08-29 12:52:14'),
(8, 1, 1, 0, 'admin@gmail.com', 'thank u so muchh', '2025-08-29 05:14:18', 'unread', '2025-08-29 13:14:18'),
(9, 2, 1, 0, 'admin@gmail.com', 'i would love to interview u', '2025-08-29 05:14:46', 'unread', '2025-08-29 13:14:46'),
(10, 1, 1, 1, '', 'thank u', '2025-08-29 10:15:25', 'unread', '2025-08-29 18:15:25'),
(11, 1, 1, 1, '', 'huh', '2025-08-29 10:26:27', 'unread', '2025-08-29 18:26:27'),
(12, 1, 1, 0, 'admin@gmail.com', 'houy', '2025-08-29 10:54:37', 'unread', '2025-08-29 18:54:37'),
(13, 1, 1, 0, 'tinabi@gmail.com', 'yes ?', '2025-08-29 10:57:57', 'unread', '2025-08-29 18:57:57'),
(14, 2, 1, 0, 'admin@gmail.com', 'hey grull', '2025-08-29 10:58:08', 'unread', '2025-08-29 18:58:08'),
(15, 2, 1, 0, 'tinabi@gmail.com', 'sup bebe gurll', '2025-08-29 10:58:25', 'unread', '2025-08-29 18:58:25'),
(16, 1, 1, 0, 'admin@gmail.com', 'yes', '2025-08-29 11:07:20', 'unread', '2025-08-29 19:07:20'),
(17, 1, 1, 0, 'tinabi@gmail.com', 'hey', '2025-08-29 11:09:03', 'unread', '2025-08-29 19:09:03'),
(18, 1, 1, 0, 'tinabi@gmail.com', 'aayoko na', '2025-08-29 11:11:39', 'unread', '2025-08-29 19:11:39'),
(19, 1, 1, 0, 'admin@gmail.com', 'pagod na pagod na ako kakabuhat sianyo AG BCHEIY YG', '2025-08-29 11:12:54', 'unread', '2025-08-29 19:12:54'),
(20, 2, 1, 0, 'admin@gmail.com', 'THE HOUSE OF US, feel na feel kita George', '2025-08-29 11:13:31', 'unread', '2025-08-29 19:13:31');

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `resume_path` varchar(255) DEFAULT NULL,
  `profile_pic_path` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','disabled') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password`, `status`, `created_at`, `phone`, `address`, `skills`, `resume`, `profile_pic`) VALUES
(1, 'tinabitch', 'tinabi@gmail.com', '1234', 'active', '2025-08-29 04:12:33', '09292525633', 'Bamban, Tarlac', 'funny', 'uploads/resume/1756460113_1.jpg', 'uploads/profile/1756460113_1swap.jpg'),
(3, 'Admin', 'admin@gmail.com', '1234', 'active', '2025-08-29 11:16:02', NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`);

--
-- Constraints for table `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
