-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2025 at 03:49 PM
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
-- Database: `ticketingsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `chatbot_responses`
--

CREATE TABLE `chatbot_responses` (
  `id` int(11) NOT NULL,
  `trigger_keyword` varchar(100) NOT NULL,
  `response_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chatbot_responses`
--

INSERT INTO `chatbot_responses` (`id`, `trigger_keyword`, `response_text`, `created_at`) VALUES
(1, 'leave', 'To file a leave request, please go to the Leave Request page or contact HR directly.', '2025-10-14 09:27:19'),
(2, 'payroll', 'Payroll is processed every 15th and 30th of the month. For details, contact HR.', '2025-10-14 09:27:19'),
(3, 'benefits', 'You can view your benefits in the HR Portal under \"Employee Benefits\".', '2025-10-14 09:27:19'),
(4, 'equipment', 'If you have issues with your equipment, please create a technical support ticket.', '2025-10-14 09:27:19'),
(5, 'help', 'I can help you with leave, payroll, benefits, and ticket issues.', '2025-10-14 09:27:19'),
(6, 'ticket', 'To create a ticket, click \"New Ticket\" on your dashboard.', '2025-10-14 09:27:19');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `is_bot` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `employee_id`, `message`, `is_bot`, `created_at`) VALUES
(1, 'employee003', 'leave', 0, '2025-10-14 11:10:09'),
(2, 'employee003', 'To file a leave request, please go to the Leave Request page or contact HR directly.', 1, '2025-10-14 11:10:09'),
(3, 'employee003', 'sada', 0, '2025-10-14 11:10:11'),
(4, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-14 11:10:11'),
(5, 'employee003', 'leave', 0, '2025-10-14 11:14:30'),
(6, 'employee003', 'To file a leave request, please go to the Leave Request page or contact HR directly.', 1, '2025-10-14 11:14:30'),
(7, 'employee003', 'leave', 0, '2025-10-14 15:06:05'),
(8, 'employee003', 'To file a leave request, please go to the Leave Request page or contact HR directly.', 1, '2025-10-14 15:06:05'),
(9, 'employee003', 'payroll', 0, '2025-10-16 08:20:57'),
(10, 'employee003', 'Payroll is processed every 15th and 30th of the month. For details, contact HR.', 1, '2025-10-16 08:20:57'),
(11, 'employee003', 'tanginamo', 0, '2025-10-17 07:02:59'),
(12, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-17 07:02:59'),
(13, 'employee003', 'payroll', 0, '2025-10-17 07:03:16'),
(14, 'employee003', 'Payroll is processed every 15th and 30th of the month. For details, contact HR.', 1, '2025-10-17 07:03:16'),
(15, 'employee003', 'sup', 0, '2025-10-17 07:04:18'),
(16, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-17 07:04:18'),
(17, 'employee003', 'leave', 0, '2025-10-19 10:04:28'),
(18, 'employee003', 'To file a leave request, please go to the Leave Request page or contact HR directly.', 1, '2025-10-19 10:04:28'),
(19, 'employee003', 'payroll', 0, '2025-10-19 10:06:01'),
(20, 'employee003', 'Payroll is processed every 15th and 30th of the month. For details, contact HR.', 1, '2025-10-19 10:06:01'),
(21, 'employee003', 'payroll', 0, '2025-10-19 10:06:15'),
(22, 'employee003', 'Payroll is processed every 15th and 30th of the month. For details, contact HR.', 1, '2025-10-19 10:06:15'),
(23, 'employee003', 'asdasd', 0, '2025-10-22 14:14:27'),
(24, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-22 14:14:27'),
(25, 'employee003', 'adasd', 0, '2025-10-22 14:14:41'),
(26, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-22 14:14:41'),
(27, 'employee003', 'asdasd', 0, '2025-10-22 14:24:56'),
(28, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-22 14:24:56'),
(29, 'employee003', 'asdas', 0, '2025-10-22 14:28:37'),
(30, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-22 14:28:37'),
(31, 'employee003', 'asdas', 0, '2025-10-22 14:28:47'),
(32, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-22 14:28:47'),
(33, 'employee003', 'undefined', 0, '2025-10-22 14:28:50'),
(34, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-22 14:28:50'),
(35, 'employee003', 'undefined', 0, '2025-10-22 14:28:52'),
(36, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-22 14:28:52'),
(37, 'employee003', 'dadad', 0, '2025-10-22 14:28:56'),
(38, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-22 14:28:56'),
(39, 'employee003', 'undefined', 0, '2025-10-22 14:29:43'),
(40, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-22 14:29:43'),
(41, 'employee003', 'adad', 0, '2025-10-22 14:29:47'),
(42, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-22 14:29:47'),
(43, 'employee003', 'undefined', 0, '2025-10-22 14:29:53'),
(44, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-22 14:29:53'),
(45, 'employee003', 'undefined', 0, '2025-10-22 14:29:55'),
(46, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-22 14:29:55'),
(47, 'employee003', 'asdasd', 0, '2025-10-22 14:32:23'),
(48, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-22 14:32:23'),
(49, 'employee003', 'dsd', 0, '2025-10-22 14:32:58'),
(50, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-22 14:32:58'),
(51, 'employee003', 'dada', 0, '2025-10-22 14:33:43'),
(52, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-22 14:33:43'),
(53, 'employee003', 'undefined', 0, '2025-10-22 14:33:45'),
(54, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-22 14:33:45'),
(55, 'employee003', 'adasd', 0, '2025-10-22 14:35:39'),
(56, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-22 14:35:39'),
(57, 'employee003', 'asdasd', 0, '2025-10-22 14:38:36'),
(58, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-22 14:38:36'),
(59, 'employee003', 'undefined', 0, '2025-10-22 14:38:40'),
(60, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-22 14:38:40'),
(61, 'employee003', 'undefined', 0, '2025-10-22 14:38:42'),
(62, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-22 14:38:42'),
(63, 'employee003', 'undefined', 0, '2025-10-22 14:39:45'),
(64, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-22 14:39:45'),
(65, 'employee003', 'asdas', 0, '2025-10-22 14:47:32'),
(66, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-22 14:47:32'),
(67, 'employee003', 'undefined', 0, '2025-10-22 14:48:26'),
(68, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-22 14:48:26'),
(69, 'employee003', 'leave request', 0, '2025-10-22 14:52:17'),
(70, 'employee003', 'To file a leave request, please go to the Leave Request page or contact HR directly.', 1, '2025-10-22 14:52:17'),
(71, 'employee003', 'leave request', 0, '2025-10-23 00:51:46'),
(72, 'employee003', 'To file a leave request, please go to the Leave Request page or contact HR directly.', 1, '2025-10-23 00:51:46'),
(73, 'employee003', 'hghgg', 0, '2025-10-23 00:51:50'),
(74, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-23 00:51:50'),
(75, 'employee003', 'adasd', 0, '2025-10-23 13:58:11'),
(76, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-23 13:58:11'),
(77, 'employee003', 'leave request', 0, '2025-10-24 06:49:52'),
(78, 'employee003', 'To file a leave request, please go to the Leave Request page or contact HR directly.', 1, '2025-10-24 06:49:52'),
(79, 'employee003', 'leave request', 0, '2025-10-27 05:05:29'),
(80, 'employee003', 'To file a leave request, please go to the Leave Request page or contact HR directly.', 1, '2025-10-27 05:05:29'),
(81, 'employee003', 'adadasd', 0, '2025-10-28 09:49:31'),
(82, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-28 09:49:31'),
(83, 'employee003', 'adada', 0, '2025-10-28 14:44:56'),
(84, 'employee003', 'I couldn\'t find a specific answer to your question. Please create a ticket or contact HR directly at hr@company.com for personalized assistance.', 1, '2025-10-28 14:44:56');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'employee',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone_number` varchar(20) DEFAULT NULL,
  `two_step_enabled` tinyint(1) DEFAULT 0,
  `two_step_code` varchar(6) DEFAULT NULL,
  `two_step_code_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_id`, `full_name`, `email`, `department`, `password`, `photo`, `role`, `created_at`, `phone_number`, `two_step_enabled`, `two_step_code`, `two_step_code_expires`) VALUES
(1, 'HR001', 'HR Administrator', 'hr@company.com', 'Human Resources', 'admin123', '', 'hr_admin', '2025-10-05 15:44:29', NULL, 0, NULL, NULL),
(2, '422002152', 'nica', 'Nica@gmail.com', '422002152', '$2y$10$Cn.9ufz6yMu7bXd7fEGnw./zZETPwe1jr.TwM9eaddGB/ZOQAdWIS', 'assets/images/hr_68f97c0e8ade4_2022_0915_03445300.jpg', 'hr_admin', '2025-10-05 15:46:33', NULL, 0, NULL, NULL),
(3, 'employee003', 'meow', 'ihatespiders0405@gmail.com', 'Employee', '$2y$10$4d./b24ub.hvyQFUzCF7rO.qq16en2PBzOxhwiOguSOKedkZGBMA6', 'assets/images/hr_68ff5aa16d79c_up.jpg', 'employee', '2025-10-06 02:35:31', '', 1, NULL, NULL),
(4, 'employee03', 'meoww', 'meoww@gmail.com', 'Employee', '$2y$10$Me2X9TQqlc8/BV2NPDRRx.iLpHNLnSZmkYdhBTIq4I0UtocnrmgGC', 'assets/images/hr_6901f73dce21f_8323422c-c799-4cfc-bd5a-7b30b0964fce.jpg', 'employee', '2025-10-06 04:57:01', NULL, 0, NULL, NULL),
(5, 'troyjames', 'basangan', 'troyjamesrobrigado@gmail.com', 'Human Resources', '$2y$10$CHxxaofuD.pRHKy2LkJiou1bWY34TGsq6QfFJFGxBKkrWoAAUy0u6', '', 'employee', '2025-10-07 08:01:22', NULL, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `notification_type` varchar(50) NOT NULL DEFAULT 'general',
  `message` text NOT NULL,
  `sent_via` varchar(100) DEFAULT 'system',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `employee_id`, `notification_type`, `message`, `sent_via`, `created_at`) VALUES
(1, 5, 'announcement', 'asdasd', 'email', '2025-10-14 09:10:40');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`id`, `employee_id`, `email`, `token`, `created_at`, `expires_at`, `used`) VALUES
(3, 'employee003', 'ihatespiders0405@gmail.com', '846249', '2025-10-29 06:22:43', '2025-10-29 07:37:43', 1);

-- --------------------------------------------------------

--
-- Table structure for table `status_history`
--

CREATE TABLE `status_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status_history`
--

INSERT INTO `status_history` (`id`, `ticket_id`, `old_status`, `new_status`, `updated_by`, `notes`, `created_at`) VALUES
(1, 1, NULL, 'pending', 4, NULL, '2025-10-06 09:47:41'),
(2, 2, NULL, 'pending', 4, NULL, '2025-10-06 09:48:44'),
(3, 2, 'pending', 'in_progress', 2, 'pukinanagi n', '2025-10-06 10:00:01'),
(4, 1, 'pending', 'resolved', 2, 'burat', '2025-10-06 10:50:32'),
(5, 2, 'in_progress', 'closed', 2, '.', '2025-10-06 10:50:37'),
(6, 3, NULL, 'pending', 5, NULL, '2025-10-07 08:01:46');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_number` varchar(50) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `priority` varchar(20) DEFAULT 'medium',
  `status` varchar(50) DEFAULT 'pending',
  `assigned_to` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `ticket_number`, `employee_id`, `category_id`, `title`, `description`, `priority`, `status`, `assigned_to`, `created_at`, `updated_at`) VALUES
(1, 'TKT-20251006-dc80f3', 4, 1, 'Sick Leave', 'Brain Tumour malala.', 'high', 'resolved', 1, '2025-10-06 09:47:41', '2025-10-06 10:50:32'),
(2, 'TKT-20251006-c12a48', 4, 7, 'Id Lost', 'may nag nakaw ng Id ko', 'high', 'closed', 1, '2025-10-06 09:48:44', '2025-10-06 10:50:40'),
(3, 'TKT-20251007-ae68e6', 5, 3, 'aaaaa', 'aaaaaaaaaaaaaaaa', 'high', 'pending', NULL, '2025-10-07 08:01:46', '2025-10-07 08:01:46'),
(4, 'TKT-20251015-9D8184', 0, 0, 'asdasdasdasdasdas', 'dasdasdad', 'medium', 'pending', NULL, '2025-10-14 23:26:28', '2025-10-14 23:26:28'),
(5, 'TKT-20251015-7DCFBF', 0, 1, 'asdas', 'adasda', 'medium', 'pending', NULL, '2025-10-14 23:26:42', '2025-10-14 23:26:42'),
(6, 'TKT-20251015-0D35AD', 0, 1, 'asdas', 'adasda', 'medium', 'pending', NULL, '2025-10-15 12:18:27', '2025-10-15 12:18:27'),
(7, 'TKT-20251015-4431FA', 0, 0, 'sdfhsfhsdshfss', 'dsfhsfdvhsfdhadf', 'medium', 'pending', NULL, '2025-10-15 13:02:37', '2025-10-15 13:02:37'),
(8, 'TKT-20251016-1D455D', 0, 0, 'sdfhsfhsdshfss', 'dsfhsfdvhsfdhadf', 'medium', 'pending', NULL, '2025-10-16 04:49:20', '2025-10-16 04:49:20');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_categories`
--

CREATE TABLE `ticket_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ticket_categories`
--

INSERT INTO `ticket_categories` (`id`, `category_name`, `description`, `created_at`) VALUES
(1, 'Leave Request', 'Vacation, sick leave, personal leave requests', '2025-10-05 15:44:29'),
(2, 'Payroll Inquiry', 'Salary, benefits, deductions related questions', '2025-10-05 15:44:29'),
(3, 'IT Support', 'Computer, software, hardware, network issues', '2025-10-05 15:44:29'),
(4, 'Training Request', 'Professional development and training requests', '2025-10-05 15:44:29'),
(5, 'Workplace Concern', 'Workplace safety, harassment, conflicts', '2025-10-05 15:44:29'),
(6, 'Benefits Inquiry', 'Health insurance, retirement, other benefits', '2025-10-05 15:44:29'),
(7, 'Equipment Request', 'Office supplies, equipment needs', '2025-10-05 15:44:29'),
(8, 'Policy Question', 'Company policies and procedures', '2025-10-05 15:44:29'),
(9, 'Other', 'General concerns and requests', '2025-10-05 15:44:29');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_responses`
--

CREATE TABLE `ticket_responses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `response_text` text NOT NULL,
  `is_internal` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ticket_responses`
--

INSERT INTO `ticket_responses` (`id`, `ticket_id`, `employee_id`, `response_text`, `is_internal`, `created_at`) VALUES
(1, 1, 4, 'dsada', 0, '2025-10-06 10:51:30'),
(2, 1, 2, 'sadasd', 0, '2025-10-06 10:52:45'),
(3, 8, 422002152, 'adasda', 0, '2025-10-21 15:20:38'),
(4, 8, 422002152, 'adasda', 0, '2025-10-21 15:20:49'),
(5, 8, 422002152, 'adasda', 0, '2025-10-21 16:56:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chatbot_responses`
--
ALTER TABLE `chatbot_responses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `trigger_keyword` (`trigger_keyword`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notifications_employee` (`employee_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_employee_id` (`employee_id`);

--
-- Indexes for table `status_history`
--
ALTER TABLE `status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status_history_ticket` (`ticket_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_number` (`ticket_number`),
  ADD KEY `idx_tickets_employee` (`employee_id`),
  ADD KEY `idx_tickets_status` (`status`),
  ADD KEY `idx_tickets_category` (`category_id`);

--
-- Indexes for table `ticket_categories`
--
ALTER TABLE `ticket_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ticket_responses`
--
ALTER TABLE `ticket_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ticket_responses_ticket` (`ticket_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chatbot_responses`
--
ALTER TABLE `chatbot_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `status_history`
--
ALTER TABLE `status_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `ticket_categories`
--
ALTER TABLE `ticket_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ticket_responses`
--
ALTER TABLE `ticket_responses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
