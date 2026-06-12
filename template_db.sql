-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2026 at 06:49 PM
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
-- Database: `template_db`
--
CREATE DATABASE IF NOT EXISTS `template_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `template_db`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `email`, `password`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@websiteniz.com', '$2y$10$Ye90WfAhtldrVHYYfCXMMOmBT8dmLc74FCv1ferHg3atjrBXaqjqa', '2026-06-10 15:06:17', '2026-06-09 17:08:41', '2026-06-09 19:45:41');

-- --------------------------------------------------------

--
-- Table structure for table `contact_info`
--

CREATE TABLE `contact_info` (
  `id` int(11) NOT NULL,
  `address` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `twitter_url` varchar(255) DEFAULT '#',
  `facebook_url` varchar(255) DEFAULT '#',
  `github_url` varchar(255) DEFAULT '#',
  `instagram_url` varchar(255) DEFAULT '#',
  `linkedin_url` varchar(255) DEFAULT '#',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_info`
--

INSERT INTO `contact_info` (`id`, `address`, `email`, `phone`, `twitter_url`, `facebook_url`, `github_url`, `instagram_url`, `linkedin_url`, `created_at`, `updated_at`) VALUES
(1, 'İstanbul / Çekmeköy', 'bikmazoguz2000@gmail.com', '+905546797989', '#', '#', '#', '#', '#', '2025-12-06 02:24:36', '2026-01-12 19:49:20');

-- --------------------------------------------------------

--
-- Table structure for table `descriptions`
--

CREATE TABLE `descriptions` (
  `id` int(11) NOT NULL,
  `section_key` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `descriptions`
--

INSERT INTO `descriptions` (`id`, `section_key`, `content`, `created_at`, `updated_at`) VALUES
(1, 'intro', 'Hello World, I designed this website to learn web development', '2026-01-13 18:07:23', '2026-01-13 19:10:09'),
(2, 'what_we_do_intro', 'My work are following;', '2026-01-13 18:08:54', '2026-01-13 21:18:25'),
(3, 'contact_intro', 'You can get in touch!', '2026-01-13 18:09:46', '2026-01-13 21:18:25');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `brief` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `image_url`, `brief`, `description`, `created_at`) VALUES
(4, 'Fütüristik Landing', 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=1200', 'Dış kaynak görselli modern tema.', 'Unsplash üzerinden alınan yüksek çözünürlüklü görseller ile daha gerçekçi bir demo oluşturur. Tamamen responsive ve hızlıdır.', '2025-11-24 15:31:30'),
(5, 'Gece*', 'https://dilemmadergi.wordpress.com/wp-content/uploads/2013/02/gece-resimleri-11-1.gif?w=640', 'Parlak neon detaylı tek sayfa layout.', 'Karanlık temalı bu arayüz; portfolyo veya ajans siteleri için tasarlandı. Hyperspace spotlights bölümünü kullanarak güçlü bir ilk izlenim veriyor.\r\n\r\nMerhaba bu site yapay zeka kullanarak tema çekmeyi öğrenmek amacıyla yapılmıştır.', '2025-11-24 16:09:02'),
(6, 'Minimal Portfolyo', 'images/gallery_1768424965_6968060585a95.webp', 'Temiz tipografiyle sade görünüm.', 'Minimal yaklaşım, tasarım odaklı freelancerlar için uygun. İçerik blokları, projelerinizi öne çıkarmak için optimize edildi.', '2025-11-24 16:09:02'),
(7, 'Startup Tanıtımı', 'images/gallery_1768779618_696d6f626e77c.png', 'CTA odaklı startup açılış sayfası.', 'Kayan bölümler ve canlı renklerle ürününüzü tanıtın. Hazır butonlar ve ikon listesi, özellikleri anlatmayı kolaylaştırır.', '2025-11-24 16:09:02');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT 'No Title',
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `what_we_do`
--

CREATE TABLE `what_we_do` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `icon_class` varchar(255) DEFAULT 'fa-gem',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `what_we_do`
--

INSERT INTO `what_we_do` (`id`, `title`, `description`, `icon_class`, `created_at`, `updated_at`) VALUES
(1, 'Web Tasarımı', 'Modern ve kullanıcı dostu web siteleri tasarlıyoruz.', 'fa-laptop-code', '2025-11-30 23:29:37', '2025-11-30 23:29:37'),
(2, 'Mobil Uygulama Geliştirme', 'iOS ve Android için yenilikçi mobil uygulamalar geliştiriyoruz.', 'fa-mobile-alt', '2025-11-30 23:29:37', '2025-11-30 23:29:37'),
(3, 'E-ticaret Çözümleri', 'Online satışlarınızı artıracak güçlü e-ticaret platformları kuruyoruz.', 'fa-shopping-cart', '2025-11-30 23:29:37', '2025-11-30 23:29:37'),
(4, 'Dijital Pazarlama', 'Markanızın dijital dünyada öne çıkmasını sağlıyoruz.', 'fa-bullhorn', '2025-11-30 23:29:37', '2025-11-30 23:29:37'),
(5, 'Veritabanı Yönetimi', 'Güvenli ve performanslı veritabanı çözümleri sunuyoruz.', 'fa-database', '2025-11-30 23:29:37', '2025-11-30 23:29:37'),
(6, 'SEO Optimizasyonu', 'Arama motorlarında üst sıralara çıkarak daha fazla müşteriye ulaşın.', 'fa-search-dollar', '2025-11-30 23:29:37', '2025-11-30 23:29:37'),
(7, 'Kurumsal Kimlik Tasarımı', 'Markanız için akılda kalıcı ve profesyonel bir kimlik oluşturuyoruz.', 'fa-id-card', '2025-11-30 23:29:37', '2025-11-30 23:29:37'),
(8, 'Sosyal Medya Yönetimi', 'Sosyal medyada etkileşiminizi artırarak marka bilinirliğinizi güçlendiriyoruz.', 'fa-share-alt', '2025-11-30 23:29:37', '2025-11-30 23:29:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `contact_info`
--
ALTER TABLE `contact_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `descriptions`
--
ALTER TABLE `descriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `section_key` (`section_key`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `what_we_do`
--
ALTER TABLE `what_we_do`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_info`
--
ALTER TABLE `contact_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `descriptions`
--
ALTER TABLE `descriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `what_we_do`
--
ALTER TABLE `what_we_do`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
