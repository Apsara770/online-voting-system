-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 23, 2025 at 11:32 AM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `online_voting_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

DROP TABLE IF EXISTS `candidates`;
CREATE TABLE IF NOT EXISTS `candidates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `election_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `bio` text,
  `photo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `election_id` (`election_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `election_id`, `name`, `bio`, `photo`) VALUES
(4, 1, 'Nimasha Perera', NULL, NULL),
(3, 1, 'John Silva', NULL, NULL),
(10, 4, 'sanjula perera', 'an employee', 'assets/images/candidates/68591b7a1470c_images (2).jpeg'),
(9, 4, 'sandamini weerasekara', 'an student', 'assets/images/candidates/685917cb32a34_826499-Women-Business-Laptop-Office-Computer-Keyboard.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `elections`
--

DROP TABLE IF EXISTS `elections`;
CREATE TABLE IF NOT EXISTS `elections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` text,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `status` enum('active','inactive','closed') NOT NULL DEFAULT 'inactive',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `elections`
--

INSERT INTO `elections` (`id`, `title`, `description`, `start_time`, `end_time`, `status`) VALUES
(2, 'Presidential Election', 'Vote for the next president.', '2025-06-23 15:36:00', '2025-06-26 16:37:00', 'active'),
(3, 'University Council', 'Select your student council rep.', '2025-06-30 16:37:00', '2025-07-30 16:37:00', 'inactive'),
(4, 'Community Leadership', 'Choose your local community leader.', '2025-06-23 14:05:00', '2025-06-24 14:05:00', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','voter') NOT NULL DEFAULT 'voter',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(8, 'apsara sewwandi', 'sewwandiapsara171@gmail.com', '$2y$10$dcf/Q1BnFx..obIMUx9noumYX9tVVlkBxj7YwK6OEVKWS/TxsyjAS', 'voter', '2025-06-23 15:32:35'),
(7, 'Admin User', 'admin@example.com', '$2y$10$Ejz2D/QcgHGNAloFcVM47.UClDF11ljPY2OmFXHOMJtViHtw6BPny', 'admin', '2025-06-23 11:53:39'),
(4, 'Nuwan Perera', 'nuwan@gmail.com', '$2y$10$M8o/F2XqxEqG37HjT3aE4Oywsc/ssSXCp3TZ.El8tkP2nd37M7LHy', 'voter', '2025-06-23 08:30:50');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

DROP TABLE IF EXISTS `votes`;
CREATE TABLE IF NOT EXISTS `votes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `voter_id` int NOT NULL,
  `election_id` int NOT NULL,
  `candidate_id` int NOT NULL,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_vote` (`voter_id`,`election_id`),
  KEY `election_id` (`election_id`),
  KEY `candidate_id` (`candidate_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`id`, `voter_id`, `election_id`, `candidate_id`, `timestamp`) VALUES
(1, 1, 1, 2, '2025-06-22 19:11:49'),
(2, 2, 1, 1, '2025-06-23 08:29:34'),
(3, 4, 2, 6, '2025-06-23 08:32:07'),
(4, 4, 4, 9, '2025-06-23 14:57:32'),
(5, 7, 4, 9, '2025-06-23 15:00:00'),
(6, 8, 4, 10, '2025-06-23 15:54:37');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
