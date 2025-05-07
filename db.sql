-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.32-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.10.0.7023
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for qcunnected
CREATE DATABASE IF NOT EXISTS `qcunnected` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `qcunnected`;

-- Dumping structure for table qcunnected.events
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `start` varchar(255) DEFAULT NULL,
  `end` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table qcunnected.events: ~7 rows (approximately)
INSERT INTO `events` (`id`, `group_id`, `title`, `created_at`, `updated_at`, `start`, `end`) VALUES
	(10, 5, 'qqq', '2025-04-30 19:55:57', '2025-04-30 19:56:01', '2025-05-14', '2025-05-14'),
	(11, 5, 'asdasd', '2025-04-30 19:56:08', '2025-04-30 19:56:08', '2025-05-29', '2025-05-29'),
	(13, 6, 'q', '2025-04-30 20:01:33', '2025-04-30 20:01:33', '2025-05-13', '2025-05-13'),
	(14, 4, 'eventzzzzzzzzzzz', '2025-04-30 21:05:10', '2025-04-30 21:05:18', '2025-05-14', '2025-05-14'),
	(15, 4, 'aaa', '2025-04-30 21:05:26', '2025-04-30 21:05:26', '2025-05-22', '2025-05-22'),
	(17, 7, 'rap battle motus', '2025-05-01 16:02:52', '2025-05-01 16:02:52', '2025-05-20', '2025-05-20'),
	(18, 6, 'qqqqqqqqqqqqq', '2025-05-01 16:03:18', '2025-05-01 16:03:18', '2025-05-17', '2025-05-17');

-- Dumping structure for table qcunnected.groups
CREATE TABLE IF NOT EXISTS `groups` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `group_number` int(11) NOT NULL,
  `status` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table qcunnected.groups: ~6 rows (approximately)
INSERT INTO `groups` (`id`, `name`, `group_number`, `status`) VALUES
	(4, 'tester', 20, 0),
	(5, 'zzzz', 10, 0),
	(6, 'asdasd', 10, 1),
	(7, 'test', 15, 1),
	(8, 'testerpo', 10, 0),
	(9, 'cf', 10, 1);

-- Dumping structure for table qcunnected.group_files
CREATE TABLE IF NOT EXISTS `group_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 1,
  `message_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table qcunnected.group_files: ~11 rows (approximately)
INSERT INTO `group_files` (`id`, `group_id`, `user_id`, `file_name`, `file_path`, `file_type`, `uploaded_at`, `status`, `message_id`) VALUES
	(1, 6, 3, 'bg.jpg', '../uploads/group_files/68125a1bb2b6f_bg.jpg', 'image/jpeg', '2025-04-30 17:12:59', 1, 1),
	(2, 7, 3, 'bg.jpg', '../uploads/group_files/681293bf84a79_bg.jpg', 'image/jpeg', '2025-04-30 21:18:55', 1, 28),
	(3, 7, 3, 'technician-installing-cctv-camera-on-260nw-2492723943.webp', '../uploads/group_files/681293d27ef64_technician-installing-cctv-camera-on-260nw-2492723943.webp', 'image/webp', '2025-04-30 21:19:14', 1, 2),
	(4, 7, 3, '4ZLmkNvP2UBKwYxznZP29PTuV5moT5wmpITBCBjQ.png', '../uploads/group_files/681295cfa1eb1_4ZLmkNvP2UBKwYxznZP29PTuV5moT5wmpITBCBjQ.png', 'image/png', '2025-04-30 21:27:43', 1, 6),
	(5, 4, 3, 'checklist.png', '../uploads/group_files/68139aa58bf1d_checklist.png', 'image/png', '2025-05-01 16:00:37', 1, 4),
	(6, 7, 3, 'list.png', '../uploads/group_files/68139bf669fc2_list.png', 'image/png', '2025-05-01 16:06:14', 1, 23),
	(7, 7, 12, 'group.webp', '../uploads/group_files/681467b533d9b_group.webp', 'image/webp', '2025-05-02 06:35:33', 1, 7),
	(8, 7, 3, 'asdasdasdasdasd.png', '../uploads/group_files/681a09d5cc8b5_asdasdasdasdasd.png', 'image/png', '2025-05-06 13:08:37', 1, 31),
	(9, 7, 3, 'asdasdasdasdasd.png', '../uploads/group_files/681a09e3e654a_asdasdasdasdasd.png', 'image/png', '2025-05-06 13:08:51', 1, 32),
	(10, 7, 3, 'th.png', '../uploads/group_files/681a0a4adf83a_th.png', 'image/png', '2025-05-06 13:10:34', 1, 33),
	(11, 7, 3, 'bg.jpg', '../uploads/group_files/681a0ae99fe87_bg.jpg', 'image/jpeg', '2025-05-06 13:13:13', 1, 5),
	(12, 7, 3, 'download.png', '../uploads/group_files/681a0b7c265ca_download.png', 'image/png', '2025-05-06 13:15:40', 1, 34),
	(13, 7, 11, 'asdczxczxkdkqjwkejqwelkqmweblqwmebqlwemqwne.jpg', '../uploads/group_files/681a0bcd04fd9_asdczxczxkdkqjwkejqwelkqmweblqwmebqlwemqwne.jpg', 'image/jpeg', '2025-05-06 13:17:01', 1, 36);

-- Dumping structure for table qcunnected.group_lessons
CREATE TABLE IF NOT EXISTS `group_lessons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table qcunnected.group_lessons: ~4 rows (approximately)
INSERT INTO `group_lessons` (`id`, `group_id`, `file_path`, `uploaded_at`, `status`) VALUES
	(1, 6, '../uploads/6812722594c4b_bg.jpg', '2025-04-30 18:55:33', 0),
	(2, 6, '../uploads/681272fa11934_list.png', '2025-04-30 18:59:06', 1),
	(3, 7, '../uploads/681290ac7e1f9_xray.png', '2025-04-30 21:05:48', 1),
	(4, 4, '../uploads/681290c7b1527_3.png', '2025-04-30 21:06:15', 1);

-- Dumping structure for table qcunnected.group_messages
CREATE TABLE IF NOT EXISTS `group_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `group_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table qcunnected.group_messages: ~33 rows (approximately)
INSERT INTO `group_messages` (`id`, `user_id`, `message`, `group_id`, `created_at`, `status`) VALUES
	(1, 3, 'aaa', 6, '2025-05-01 00:38:47', 1),
	(2, 3, 'asdasd', 6, '2025-05-01 00:39:00', 1),
	(3, 2, 'qqq', 6, '2025-05-01 00:48:19', 1),
	(4, 3, 'qqq', 6, '2025-05-01 00:50:04', 1),
	(5, 3, 'zxczxc', 5, '2025-05-01 00:50:14', 1),
	(6, 3, 'qqq', 6, '2025-05-01 01:00:39', 1),
	(7, 3, 'aaa', 6, '2025-05-01 01:08:07', 1),
	(8, 3, 'zzzzzzzzz', 6, '2025-05-01 01:12:59', 1),
	(9, 11, 'aaa', 6, '2025-05-01 02:26:58', 1),
	(10, 3, 'q', 4, '2025-05-01 04:30:34', 1),
	(11, 11, 'hoy', 7, '2025-05-01 05:04:29', 1),
	(12, 3, 'l', 7, '2025-05-01 05:10:06', 1),
	(13, 3, 'asdasd', 7, '2025-05-01 05:16:35', 1),
	(14, 3, 'qqq', 7, '2025-05-01 05:18:45', 1),
	(15, 3, 'aaaaaaaaaaaa', 7, '2025-05-01 05:27:43', 1),
	(16, 3, 'tttttttttttttttttt', 4, '2025-05-02 00:00:37', 1),
	(17, 3, 'rar', 7, '2025-05-02 00:06:14', 1),
	(18, 12, 'aa', 7, '2025-05-02 14:22:40', 1),
	(19, 12, 'testing', 7, '2025-05-02 14:35:38', 1),
	(20, 3, 'hi', 7, '2025-05-02 14:37:16', 1),
	(21, 11, 'aaa', 7, '2025-05-02 14:41:02', 1),
	(22, 12, 'hi', 7, '2025-05-02 14:41:09', 1),
	(23, 3, 'aqqq', 7, '2025-05-02 14:41:14', 1),
	(24, 11, 'asdasd', 7, '2025-05-02 14:42:03', 1),
	(25, 11, 'qq', 7, '2025-05-02 14:42:21', 1),
	(26, 11, 'hello this is from admin', 7, '2025-05-02 14:43:51', 1),
	(27, 3, 'hi admin', 7, '2025-05-02 14:43:58', 1),
	(28, 12, 'hello admin bro', 7, '2025-05-02 14:44:04', 1),
	(29, 3, 'asdasd', 7, '2025-05-06 21:03:51', 1),
	(30, 3, 'r', 7, '2025-05-06 21:08:41', 1),
	(31, 3, 'hi', 7, '2025-05-06 21:08:51', 1),
	(32, 3, 'd', 7, '2025-05-06 21:10:34', 1),
	(33, 3, 'asdasd', 7, '2025-05-06 21:11:29', 1),
	(34, 3, '', 7, '2025-05-06 21:15:40', 1),
	(35, 3, 'test', 7, '2025-05-06 21:15:43', 1),
	(36, 11, '', 7, '2025-05-06 21:17:01', 1),
	(37, 11, 'zzz', 7, '2025-05-06 21:17:31', 1);

-- Dumping structure for table qcunnected.student_groups
CREATE TABLE IF NOT EXISTS `student_groups` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `group_id` bigint(20) NOT NULL,
  `status` tinyint(4) DEFAULT 1,
  `is_removed` varchar(255) DEFAULT NULL,
  `date_joined` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table qcunnected.student_groups: ~9 rows (approximately)
INSERT INTO `student_groups` (`id`, `user_id`, `group_id`, `status`, `is_removed`, `date_joined`) VALUES
	(1, 3, 6, 0, NULL, '2025-04-30 16:27:44'),
	(2, 3, 5, 1, NULL, '2025-04-30 16:50:10'),
	(3, 3, 6, 0, NULL, '2025-04-30 18:05:13'),
	(4, 3, 6, 0, NULL, '2025-04-30 18:05:43'),
	(5, 3, 6, 0, NULL, '2025-04-30 18:05:52'),
	(6, 3, 6, 0, NULL, '2025-04-30 18:06:34'),
	(7, 3, 4, 1, NULL, '2025-04-30 20:10:56'),
	(8, 3, 7, 0, NULL, '2025-04-30 21:07:19'),
	(9, 3, 7, 1, NULL, '2025-04-30 21:10:01'),
	(10, 12, 7, 1, NULL, '2025-05-02 06:16:05');

-- Dumping structure for table qcunnected.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `token` varchar(255) DEFAULT NULL,
  `datentime` datetime DEFAULT current_timestamp(),
  `position` varchar(50) DEFAULT 'user',
  `otp` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table qcunnected.users: ~5 rows (approximately)
INSERT INTO `users` (`id`, `name`, `email`, `profile`, `password`, `is_verified`, `status`, `token`, `datentime`, `position`, `otp`) VALUES
	(1, 'tester', 'tester@gmail.com', NULL, '$2y$10$E460AxiZ4lvrBt7cNMzh2OnN/rQwn1bkaWBCPjcw9WsWxHyvvtKPq', 0, 1, '1c607dbf3720133f7cf957560c9855e9', '2025-04-29 11:55:07', 'user', NULL),
	(2, 'tester', 'teste2r@gmail.com', NULL, '$2y$10$dMmbaseHeTyC56SO3qDs8uRvlJfJhhrAN7Du1wUiQi6u0Pto8/ew6', 0, 1, '7b3ccb14f2f5aaf7b87ee92c52c8fb32', '2025-04-29 12:55:16', 'user', NULL),
	(3, 'bzzzzzzzzzzz', 'Tester123!@gmail.com', '4.jfif', '$2y$10$uOqDYMK27yvxTSFokt2CY.JMAFsdLhHOZE3LPLCjB9JjJYmo5hqOS', 0, 1, 'fb9a0e30f9e9769ae638919a4cf25e2e', '2025-04-29 12:55:34', 'user', NULL),
	(8, 'Ester123!', 'mr.ephraiel@gmail.com', '6.jfif', '$2y$10$EwYQXp4KuBa5cUPF96dbWuZrxyZNiFICQwYGIFF0cR0DJx9eZWhk6', 0, 1, 'e708f2c8bd765d22b188d69fcaa3f6f5', '2025-04-29 13:20:35', 'user', '446187'),
	(11, 'brother long', 'Tester123!!@gmail.com', '6.jfif', '$2y$10$uOqDYMK27yvxTSFokt2CY.JMAFsdLhHOZE3LPLCjB9JjJYmo5hqOS', 0, 1, 'fb9a0e30f9e9769ae638919a4cf25e2e', '2025-04-29 12:55:34', 'admin', NULL),
	(12, 'Pass123!', 'brotherlong@gmail.com', '1.jfif', '$2y$10$BCzuz021QL8abj7oACYcJO9Jw/NbZh5DedL49XUnJ792QPeGV/hDK', 0, 1, '1c868d2c0a20fc43e71025fad34119d7', '2025-05-02 14:04:04', 'user', NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
