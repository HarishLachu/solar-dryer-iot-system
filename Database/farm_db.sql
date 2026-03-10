-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 05, 2026 at 05:52 AM
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
-- Database: `farm_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `alerts_log`
--

CREATE TABLE `alerts_log` (
  `id` int(11) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `alert_type` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `temperature` decimal(5,2) DEFAULT NULL,
  `humidity` decimal(5,2) DEFAULT NULL,
  `triggered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alerts_log`
--

INSERT INTO `alerts_log` (`id`, `session_id`, `alert_type`, `message`, `temperature`, `humidity`, `triggered_at`, `resolved`) VALUES
(1, 1, 'DANGER', 'Emergency stop triggered by farmer from dashboard.', NULL, NULL, '2026-03-03 13:12:51', 1),
(2, 2, 'TOO_COLD', 'WARNING: Temperature 28°C too low (<35°C). Drying ineffective.', 28.00, 65.00, '2026-03-03 13:40:58', 1),
(3, NULL, 'OVERHEAT', 'WARNING: Temperature 62.8°C too high (>60°C). Risk of crop damage.', 62.80, 65.00, '2026-03-03 15:25:49', 1),
(4, 12, 'TOO_COLD', 'WARNING: Temperature 33.1°C too low (<35°C). Drying ineffective.', 33.10, 65.00, '2026-03-03 15:31:53', 1),
(5, 12, 'OVERHEAT', 'WARNING: Temperature 63.9°C too high (>60°C). Risk of crop damage.', 63.90, 65.00, '2026-03-03 15:32:01', 1),
(6, 13, 'OVERHEAT', 'WARNING: Temperature 64.9°C too high (>60°C). Risk of crop damage.', 64.90, 65.00, '2026-03-03 15:33:45', 1),
(7, 13, 'TOO_COLD', 'WARNING: Temperature 34.1°C too low (<35°C). Drying ineffective.', 34.10, 65.00, '2026-03-03 15:34:00', 1),
(8, NULL, 'TOO_COLD', 'WARNING: Temperature 34.1°C too low (<35°C). Drying ineffective.', 34.10, 65.00, '2026-03-03 16:34:38', 1),
(9, NULL, 'DANGER', 'DANGER: Temperature 71.3°C exceeds danger limit 65°C!', 71.30, 65.00, '2026-03-03 16:35:36', 1);

-- --------------------------------------------------------

--
-- Table structure for table `control_commands`
--

CREATE TABLE `control_commands` (
  `id` int(11) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `command_type` varchar(50) NOT NULL,
  `command_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`command_value`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `received` tinyint(4) NOT NULL DEFAULT 0,
  `received_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `control_commands`
--

INSERT INTO `control_commands` (`id`, `session_id`, `command_type`, `command_value`, `created_at`, `received`, `received_at`) VALUES
(1, NULL, 'STOP_SESSION', '{\"fan\":false}', '2026-03-03 13:11:43', 0, NULL),
(2, 1, 'START_SESSION', '{\"session_id\":1,\"crop\":\"Paddy\",\"target_temp\":50,\"duration\":360,\"fan\":true}', '2026-03-03 13:11:58', 0, NULL),
(3, 1, 'STOP_FAN', '{\"fan\":false,\"reason\":\"overheat\",\"temp\":null}', '2026-03-03 13:12:51', 0, NULL),
(4, 1, 'STOP_SESSION', '{\"fan\":false,\"reason\":\"user_stop\"}', '2026-03-03 13:12:51', 0, NULL),
(5, 2, 'START_SESSION', '{\"session_id\":2,\"crop\":\"Paddy\",\"target_temp\":50,\"duration\":360,\"fan\":true}', '2026-03-03 13:12:58', 0, NULL),
(6, 3, 'START_SESSION', '{\"session_id\":3,\"crop\":\"Paddy\",\"target_temp\":50,\"duration\":240,\"fan\":true}', '2026-03-03 13:57:08', 0, NULL),
(7, 3, 'STOP_SESSION', '{\"fan\":false,\"reason\":\"user_stop\"}', '2026-03-03 13:57:32', 0, NULL),
(8, 4, 'START_SESSION', '{\"session_id\":4,\"crop\":\"Paddy\",\"target_temp\":50,\"duration\":360,\"fan\":true}', '2026-03-03 14:27:42', 0, NULL),
(9, 4, 'STOP_SESSION', '{\"fan\":false,\"reason\":\"user_stop\"}', '2026-03-03 14:29:45', 0, NULL),
(10, 5, 'START_SESSION', '{\"session_id\":5,\"crop\":\"Paddy\",\"target_temp\":50,\"duration\":60,\"fan\":true}', '2026-03-03 14:32:12', 0, NULL),
(11, 5, 'STOP_SESSION', '{\"fan\":false,\"reason\":\"user_stop\"}', '2026-03-03 14:32:57', 0, NULL),
(12, 6, 'START_SESSION', '{\"session_id\":6,\"crop\":\"Paddy\",\"target_temp\":50,\"duration\":60,\"fan\":true}', '2026-03-03 14:53:12', 0, NULL),
(13, 6, 'STOP_SESSION', '{\"fan\":false,\"reason\":\"user_stop\"}', '2026-03-03 14:53:14', 0, NULL),
(14, 7, 'START_SESSION', '{\"session_id\":7,\"crop\":\"Paddy\",\"target_temp\":50,\"duration\":360,\"fan\":true}', '2026-03-03 14:54:29', 0, NULL),
(15, 7, 'STOP_SESSION', '{\"fan\":false,\"reason\":\"user_stop\"}', '2026-03-03 14:54:33', 0, NULL),
(16, 8, 'START_SESSION', '{\"session_id\":8,\"crop\":\"Paddy\",\"target_temp\":50,\"duration\":360,\"fan\":true}', '2026-03-03 14:57:29', 0, NULL),
(17, 8, 'STOP_SESSION', '{\"fan\":false,\"reason\":\"user_stop\"}', '2026-03-03 15:15:55', 0, NULL),
(18, 9, 'START_SESSION', '{\"session_id\":9,\"crop\":\"Paddy\",\"target_temp\":50,\"duration\":360,\"fan\":true}', '2026-03-03 15:15:58', 0, NULL),
(19, 9, 'STOP_SESSION', '{\"fan\":false,\"reason\":\"user_stop\"}', '2026-03-03 15:21:12', 0, NULL),
(20, 10, 'START_SESSION', '{\"session_id\":10,\"crop\":\"Paddy\",\"target_temp\":50,\"duration\":360,\"fan\":true}', '2026-03-03 15:21:30', 0, NULL),
(21, 10, 'STOP_SESSION', '{\"fan\":false,\"reason\":\"user_stop\"}', '2026-03-03 15:23:27', 0, NULL),
(22, 11, 'START_SESSION', '{\"session_id\":11,\"crop\":\"Paddy\",\"target_temp\":50,\"duration\":360,\"fan\":true}', '2026-03-03 15:30:02', 0, NULL),
(23, 11, 'STOP_SESSION', '{\"fan\":false,\"reason\":\"user_stop\"}', '2026-03-03 15:30:40', 0, NULL),
(24, 12, 'START_SESSION', '{\"session_id\":12,\"crop\":\"Paddy\",\"target_temp\":50,\"duration\":360,\"fan\":true}', '2026-03-03 15:31:37', 0, NULL),
(25, 12, 'STOP_SESSION', '{\"fan\":false,\"reason\":\"user_stop\"}', '2026-03-03 15:32:45', 0, NULL),
(26, 13, 'START_SESSION', '{\"session_id\":13,\"crop\":\"Paddy\",\"target_temp\":50,\"duration\":360,\"fan\":true}', '2026-03-03 15:32:49', 0, NULL),
(27, 13, 'STOP_SESSION', '{\"fan\":false,\"reason\":\"user_stop\"}', '2026-03-03 15:34:21', 0, NULL),
(28, NULL, 'STOP_SESSION', '{\"fan\":false}', '2026-03-03 16:05:32', 0, NULL),
(29, NULL, 'STOP_SESSION', '{\"fan\":false}', '2026-03-03 16:05:34', 0, NULL),
(30, NULL, 'STOP_SESSION', '{\"fan\":false}', '2026-03-03 16:05:35', 0, NULL),
(31, NULL, 'STOP_SESSION', '{\"fan\":false}', '2026-03-03 16:05:35', 0, NULL),
(32, NULL, 'STOP_SESSION', '{\"fan\":false}', '2026-03-03 16:05:35', 0, NULL),
(33, NULL, 'STOP_SESSION', '{\"fan\":false}', '2026-03-03 16:05:35', 0, NULL),
(34, NULL, 'STOP_SESSION', '{\"fan\":false}', '2026-03-03 16:05:35', 0, NULL),
(35, 14, 'START_SESSION', '{\"session_id\":14,\"crop\":\"Paddy\",\"target_temp\":50,\"duration\":360,\"fan\":true}', '2026-03-03 16:05:40', 0, NULL),
(36, 14, 'STOP_SESSION', '{\"fan\":false,\"reason\":\"user_stop\"}', '2026-03-03 16:05:44', 0, NULL),
(37, NULL, 'STOP_SESSION', '{\"fan\":false}', '2026-03-03 16:05:46', 0, NULL),
(38, 15, 'START_SESSION', '{\"session_id\":15,\"crop\":\"Paddy\",\"target_temp\":50,\"duration\":360,\"fan\":true}', '2026-03-03 16:05:54', 0, NULL),
(39, 15, 'STOP_SESSION', '{\"fan\":false,\"reason\":\"user_stop\"}', '2026-03-03 16:06:30', 0, NULL),
(40, NULL, 'STOP_SESSION', '{\"fan\":false}', '2026-03-03 16:06:30', 0, NULL),
(41, NULL, 'STOP_SESSION', '{\"fan\":false}', '2026-03-03 16:06:31', 0, NULL),
(42, NULL, 'STOP_SESSION', '{\"fan\":false}', '2026-03-03 16:06:31', 0, NULL),
(43, NULL, 'STOP_SESSION', '{\"fan\":false}', '2026-03-03 16:06:33', 0, NULL),
(44, 16, 'START_SESSION', '{\"session_id\":16,\"crop\":\"Paddy\",\"target_temp\":50,\"duration\":360,\"fan\":true}', '2026-03-03 16:06:50', 0, NULL),
(45, 16, 'STOP_SESSION', '{\"fan\":false,\"reason\":\"user_stop\"}', '2026-03-03 16:06:52', 0, NULL),
(46, 17, 'START_SESSION', '{\"session_id\":17,\"crop\":\"Paddy\",\"target_temp\":50,\"duration\":360,\"fan\":true}', '2026-03-03 16:23:04', 0, NULL),
(47, 17, 'STOP_SESSION', '{\"fan\":false,\"reason\":\"user_stop\"}', '2026-03-03 16:23:12', 0, NULL),
(48, 18, 'START_SESSION', '{\"session_id\":18,\"crop\":\"Paddy\",\"target_temp\":50,\"duration\":360,\"fan\":true}', '2026-03-03 16:26:41', 0, NULL),
(49, 18, 'STOP_SESSION', '{\"fan\":false,\"reason\":\"user_stop\"}', '2026-03-03 16:26:43', 0, NULL),
(50, 19, 'START_SESSION', '{\"session_id\":19,\"crop\":\"Paddy\",\"target_temp\":50,\"duration\":360,\"fan\":true}', '2026-03-03 16:33:33', 0, NULL),
(51, 19, 'STOP_SESSION', '{\"fan\":false,\"reason\":\"user_stop\"}', '2026-03-03 16:33:37', 0, NULL),
(52, 20, 'START_SESSION', '{\"session_id\":20,\"crop\":\"Paddy\",\"target_temp\":50,\"duration\":360,\"fan\":true}', '2026-03-03 16:33:42', 0, NULL),
(53, 20, 'STOP_SESSION', '{\"fan\":false,\"reason\":\"user_stop\"}', '2026-03-03 16:33:45', 0, NULL),
(54, 21, 'START_SESSION', '{\"session_id\":21,\"crop\":\"Paddy\",\"target_temp\":50,\"duration\":360,\"fan\":true}', '2026-03-03 16:35:22', 0, NULL),
(55, 21, 'STOP_SESSION', '{\"fan\":false,\"reason\":\"user_stop\"}', '2026-03-03 16:35:28', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `crop_profiles`
--

CREATE TABLE `crop_profiles` (
  `id` int(11) NOT NULL,
  `crop_name` varchar(50) NOT NULL,
  `min_temp` decimal(5,2) NOT NULL,
  `max_temp` decimal(5,2) NOT NULL,
  `target_temp` decimal(5,2) NOT NULL,
  `target_humidity` decimal(5,2) NOT NULL,
  `duration_hours` int(11) NOT NULL,
  `danger_temp` decimal(5,2) NOT NULL DEFAULT 65.00,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crop_profiles`
--

INSERT INTO `crop_profiles` (`id`, `crop_name`, `min_temp`, `max_temp`, `target_temp`, `target_humidity`, `duration_hours`, `danger_temp`, `description`) VALUES
(1, 'Paddy', 40.00, 55.00, 50.00, 14.00, 6, 65.00, 'Rice paddy: remove moisture to safe storage level of 14%'),
(2, 'Corn', 45.00, 58.00, 53.00, 13.00, 8, 68.00, 'Maize: dry to 13% moisture for safe storage'),
(3, 'Chili', 50.00, 62.00, 58.00, 10.00, 10, 70.00, 'Red/green chili: dry to 10% to prevent mold'),
(4, 'Cocoa', 40.00, 48.00, 45.00, 7.00, 14, 60.00, 'Cocoa beans: slow gentle drying to preserve flavour'),
(5, 'Cassava', 50.00, 60.00, 55.00, 12.00, 7, 68.00, 'Cassava chips: rapid drying to 12% moisture');

-- --------------------------------------------------------

--
-- Table structure for table `drying_sessions`
--

CREATE TABLE `drying_sessions` (
  `id` int(11) NOT NULL,
  `farmer_id` int(11) NOT NULL DEFAULT 1,
  `crop_name` varchar(50) NOT NULL,
  `target_temp` decimal(5,2) NOT NULL,
  `duration_minutes` int(11) NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_time` timestamp NULL DEFAULT NULL,
  `status` enum('running','completed','stopped') DEFAULT 'running',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drying_sessions`
--

INSERT INTO `drying_sessions` (`id`, `farmer_id`, `crop_name`, `target_temp`, `duration_minutes`, `start_time`, `end_time`, `status`, `notes`) VALUES
(1, 5, 'Paddy', 50.00, 360, '2026-03-03 13:11:58', '2026-03-03 13:12:51', 'stopped', NULL),
(2, 5, 'Paddy', 50.00, 360, '2026-03-03 13:12:58', '2026-03-03 13:57:08', 'stopped', NULL),
(3, 5, 'Paddy', 50.00, 240, '2026-03-03 13:57:08', '2026-03-03 13:57:32', 'stopped', NULL),
(4, 5, 'Paddy', 50.00, 360, '2026-03-03 14:27:42', '2026-03-03 14:29:45', 'stopped', NULL),
(5, 5, 'Paddy', 50.00, 60, '2026-03-03 14:32:12', '2026-03-03 14:32:57', 'stopped', NULL),
(6, 5, 'Paddy', 50.00, 60, '2026-03-03 14:53:12', '2026-03-03 14:53:14', 'stopped', NULL),
(7, 5, 'Paddy', 50.00, 360, '2026-03-03 14:54:29', '2026-03-03 14:54:33', 'stopped', NULL),
(8, 5, 'Paddy', 50.00, 360, '2026-03-03 14:57:29', '2026-03-03 15:15:55', 'stopped', NULL),
(9, 5, 'Paddy', 50.00, 360, '2026-03-03 15:15:58', '2026-03-03 15:21:12', 'stopped', NULL),
(10, 5, 'Paddy', 50.00, 360, '2026-03-03 15:21:30', '2026-03-03 15:23:27', 'stopped', NULL),
(11, 5, 'Paddy', 50.00, 360, '2026-03-03 15:30:02', '2026-03-03 15:30:40', 'stopped', NULL),
(12, 5, 'Paddy', 50.00, 360, '2026-03-03 15:31:37', '2026-03-03 15:32:45', 'stopped', NULL),
(13, 5, 'Paddy', 50.00, 360, '2026-03-03 15:32:49', '2026-03-03 15:34:21', 'stopped', NULL),
(14, 5, 'Paddy', 50.00, 360, '2026-03-03 16:05:40', '2026-03-03 16:05:44', 'stopped', NULL),
(15, 5, 'Paddy', 50.00, 360, '2026-03-03 16:05:54', '2026-03-03 16:06:30', 'stopped', NULL),
(16, 5, 'Paddy', 50.00, 360, '2026-03-03 16:06:50', '2026-03-03 16:06:52', 'stopped', NULL),
(17, 5, 'Paddy', 50.00, 360, '2026-03-03 16:23:04', '2026-03-03 16:23:12', 'stopped', NULL),
(18, 5, 'Paddy', 50.00, 360, '2026-03-03 16:26:41', '2026-03-03 16:26:43', 'stopped', NULL),
(19, 5, 'Paddy', 50.00, 360, '2026-03-03 16:33:33', '2026-03-03 16:33:37', 'stopped', NULL),
(20, 5, 'Paddy', 50.00, 360, '2026-03-03 16:33:42', '2026-03-03 16:33:45', 'stopped', NULL),
(21, 5, 'Paddy', 50.00, 360, '2026-03-03 16:35:22', '2026-03-03 16:35:28', 'stopped', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sensor_logs`
--

CREATE TABLE `sensor_logs` (
  `id` int(11) NOT NULL,
  `farm_id` int(11) NOT NULL DEFAULT 1,
  `temperature` decimal(5,2) NOT NULL,
  `humidity` decimal(5,2) NOT NULL,
  `soil_moisture` tinyint(4) NOT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `fan_status` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sensor_logs`
--

INSERT INTO `sensor_logs` (`id`, `farm_id`, `temperature`, `humidity`, `soil_moisture`, `recorded_at`, `fan_status`) VALUES
(1, 1, 28.00, 65.00, 0, '2026-02-26 08:51:38', 0),
(2, 1, 28.00, 65.00, 0, '2026-02-26 08:51:46', 0),
(3, 1, 28.00, 65.00, 0, '2026-02-26 08:51:55', 0),
(4, 1, 28.00, 65.00, 0, '2026-02-26 08:52:04', 0),
(5, 1, 28.00, 65.00, 0, '2026-02-26 08:52:12', 0),
(6, 1, 28.00, 65.00, 0, '2026-02-26 08:52:22', 0),
(7, 1, 28.00, 65.00, 0, '2026-02-26 08:52:34', 0),
(8, 1, 28.00, 65.00, 0, '2026-02-26 08:52:44', 0),
(9, 1, 28.00, 65.00, 0, '2026-02-26 08:52:55', 0),
(10, 1, 28.00, 65.00, 0, '2026-02-26 08:53:06', 0),
(11, 1, 28.00, 65.00, 0, '2026-02-26 08:53:17', 0),
(12, 1, 28.00, 65.00, 0, '2026-02-26 08:53:26', 0),
(13, 1, 28.00, 65.00, 0, '2026-02-26 08:53:37', 0),
(14, 1, 28.00, 65.00, 0, '2026-02-26 08:53:47', 0),
(15, 1, 28.00, 65.00, 0, '2026-02-26 08:53:58', 0),
(16, 1, 28.00, 65.00, 0, '2026-02-26 08:54:12', 0),
(17, 1, 28.00, 65.00, 0, '2026-02-26 08:54:18', 0),
(18, 1, 28.00, 65.00, 0, '2026-02-26 08:54:30', 0),
(19, 1, 28.00, 65.00, 0, '2026-02-26 08:54:41', 0),
(20, 1, 28.00, 65.00, 0, '2026-02-26 08:54:51', 0),
(21, 1, 28.00, 65.00, 0, '2026-02-26 08:55:02', 0),
(22, 1, 28.00, 65.00, 0, '2026-02-26 08:55:10', 0),
(23, 1, 28.00, 65.00, 0, '2026-02-26 08:55:21', 0),
(24, 1, 28.00, 65.00, 0, '2026-02-26 08:55:32', 0),
(25, 1, 28.00, 65.00, 0, '2026-02-26 08:55:40', 0),
(26, 1, 28.00, 65.00, 0, '2026-02-26 08:55:48', 0),
(27, 1, 28.00, 65.00, 0, '2026-02-26 08:55:56', 0),
(28, 1, 28.00, 65.00, 0, '2026-02-26 08:56:06', 0),
(29, 1, 28.00, 65.00, 0, '2026-02-26 08:56:15', 0),
(30, 1, 28.00, 65.00, 0, '2026-02-26 08:56:27', 0),
(31, 1, 28.00, 65.00, 0, '2026-02-26 08:56:39', 0),
(32, 1, 28.00, 65.00, 0, '2026-02-26 08:56:50', 0),
(33, 1, 28.00, 65.00, 0, '2026-02-26 08:57:02', 0),
(34, 1, 28.00, 65.00, 0, '2026-02-26 08:57:12', 0),
(35, 1, 28.00, 65.00, 0, '2026-02-26 08:57:23', 0),
(36, 1, 28.00, 65.00, 0, '2026-02-26 08:57:34', 0),
(37, 1, 28.00, 65.00, 0, '2026-02-26 08:57:45', 0),
(38, 1, 28.00, 65.00, 0, '2026-02-26 08:57:56', 0),
(39, 1, 28.00, 65.00, 0, '2026-02-26 08:58:07', 0),
(40, 1, 28.00, 65.00, 0, '2026-02-26 09:34:23', 0),
(41, 1, 28.00, 65.00, 0, '2026-02-26 09:34:48', 0),
(42, 1, 28.00, 65.00, 0, '2026-02-26 09:34:55', 0),
(43, 1, 28.00, 65.00, 0, '2026-02-26 09:35:03', 0),
(44, 1, 28.00, 65.00, 0, '2026-02-26 09:35:12', 0),
(45, 1, 28.00, 65.00, 0, '2026-02-26 09:35:24', 0),
(46, 1, 28.00, 65.00, 0, '2026-02-26 09:35:36', 0),
(47, 1, 28.00, 65.00, 0, '2026-02-26 09:35:51', 0),
(48, 1, 28.00, 65.00, 0, '2026-02-26 09:35:58', 0),
(49, 1, 28.00, 65.00, 0, '2026-02-26 09:36:05', 0),
(50, 1, 28.00, 65.00, 0, '2026-02-26 09:36:10', 0),
(51, 1, 28.00, 65.00, 0, '2026-02-26 09:36:16', 0),
(52, 1, 28.00, 65.00, 0, '2026-02-26 09:36:22', 0),
(53, 1, 28.00, 65.00, 0, '2026-02-26 09:36:29', 0),
(54, 1, 28.00, 65.00, 0, '2026-02-26 09:36:37', 0),
(55, 1, 28.00, 65.00, 0, '2026-02-26 09:36:45', 0),
(56, 1, 28.00, 65.00, 0, '2026-02-26 09:36:53', 0),
(57, 1, 28.00, 65.00, 0, '2026-02-26 09:37:00', 0),
(58, 1, 28.00, 65.00, 0, '2026-02-26 09:37:02', 0),
(59, 1, 28.00, 65.00, 0, '2026-02-26 09:37:13', 0),
(60, 1, 28.00, 65.00, 0, '2026-02-26 09:46:38', 0),
(61, 1, 28.00, 65.00, 0, '2026-02-26 09:46:44', 0),
(62, 1, 28.00, 65.00, 0, '2026-02-26 09:46:51', 0),
(63, 1, 28.00, 65.00, 0, '2026-02-26 09:47:01', 0),
(64, 1, 28.00, 65.00, 0, '2026-02-26 09:47:07', 0),
(65, 1, 28.00, 65.00, 0, '2026-02-27 08:34:01', 0),
(66, 1, 28.00, 65.00, 0, '2026-02-27 08:34:47', 0),
(67, 1, 28.00, 65.00, 0, '2026-03-03 12:31:16', 0),
(68, 1, 28.00, 65.00, 0, '2026-03-03 13:40:58', 0),
(69, 1, 28.00, 65.00, 0, '2026-03-03 13:41:07', 0),
(70, 1, 28.00, 65.00, 0, '2026-03-03 13:41:42', 0),
(71, 1, 28.00, 65.00, 0, '2026-03-03 13:42:51', 0),
(72, 1, 45.20, 62.50, 38, '2026-03-03 13:48:02', 0),
(73, 1, 28.00, 65.00, 0, '2026-03-03 13:50:54', 0),
(74, 1, 62.80, 65.00, 0, '2026-03-03 15:25:49', 0),
(75, 1, 62.80, 65.00, 0, '2026-03-03 15:26:27', 0),
(76, 1, 41.60, 65.00, 0, '2026-03-03 15:27:21', 0),
(77, 1, 45.80, 65.00, 0, '2026-03-03 15:27:26', 0),
(78, 1, 45.80, 65.00, 0, '2026-03-03 15:30:46', 0),
(79, 1, 33.10, 65.00, 0, '2026-03-03 15:31:53', 0),
(80, 1, 63.90, 65.00, 0, '2026-03-03 15:32:01', 0),
(81, 1, 29.90, 65.00, 0, '2026-03-03 15:32:22', 0),
(82, 1, 54.30, 65.00, 0, '2026-03-03 15:33:16', 0),
(83, 1, 64.90, 65.00, 0, '2026-03-03 15:33:45', 0),
(84, 1, 34.10, 65.00, 0, '2026-03-03 15:34:00', 0),
(85, 1, 34.10, 65.00, 0, '2026-03-03 16:34:38', 0),
(86, 1, 34.10, 65.00, 0, '2026-03-03 16:34:49', 0),
(87, 1, 50.10, 65.00, 0, '2026-03-03 16:35:04', 0),
(88, 1, 71.30, 65.00, 0, '2026-03-03 16:35:36', 0),
(89, 1, 71.30, 65.00, 0, '2026-03-03 16:35:45', 0),
(90, 1, 71.30, 65.00, 0, '2026-03-03 16:35:54', 0),
(91, 1, 71.30, 65.00, 0, '2026-03-03 16:36:03', 0),
(92, 1, 39.40, 65.00, 0, '2026-03-03 16:36:10', 0),
(93, 1, 39.40, 65.00, 0, '2026-03-03 16:36:19', 0),
(94, 1, 39.40, 65.00, 0, '2026-03-03 16:36:29', 0);

-- --------------------------------------------------------

--
-- Table structure for table `solar_dryer_logs`
--

CREATE TABLE `solar_dryer_logs` (
  `id` int(11) NOT NULL,
  `device_id` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `temperature` decimal(5,2) NOT NULL,
  `humidity` decimal(5,2) NOT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alerts_log`
--
ALTER TABLE `alerts_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `control_commands`
--
ALTER TABLE `control_commands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crop_profiles`
--
ALTER TABLE `crop_profiles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `drying_sessions`
--
ALTER TABLE `drying_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sensor_logs`
--
ALTER TABLE `sensor_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `solar_dryer_logs`
--
ALTER TABLE `solar_dryer_logs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alerts_log`
--
ALTER TABLE `alerts_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `control_commands`
--
ALTER TABLE `control_commands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `crop_profiles`
--
ALTER TABLE `crop_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `drying_sessions`
--
ALTER TABLE `drying_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `sensor_logs`
--
ALTER TABLE `sensor_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `solar_dryer_logs`
--
ALTER TABLE `solar_dryer_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
