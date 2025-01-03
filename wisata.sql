-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 25, 2024 at 07:36 PM
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
-- Database: `wisata`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `added_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `package_id`, `quantity`, `added_date`) VALUES
(3, 59, 3, 4, '2024-12-25 16:53:07'),
(5, 59, 1, 3, '2024-12-25 17:02:02'),
(6, 59, 4, 1, '2024-12-25 17:11:21'),
(12, 60, 1, 3, '2024-12-25 17:57:27'),
(14, 60, 6, 2, '2024-12-25 17:57:46');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `image`, `title`, `description`, `price`) VALUES
(1, '/Responsipwd/booking/asset/anambasmain.jpg', 'Trip to Anambas', 'Rasakan liburan dengan aktivitas seru dengan dikelilingi ribuan pulau nan indah, surving dengan ribuan ikan cantik di laut anambas.', 18000000.00),
(2, '/Responsipwd/booking/asset/batammain.jpg', 'Kota Batam', 'Rasakan liburan dengan aktivitas seru dengan dikelilingi ribuan pulau nan indah, surving dengan ribuan ikan cantik di laut Batam.', 12000000.00),
(3, '/Responsipwd/booking/asset/bintanmain.jpg', 'Pulau Bintan', 'Rasakan liburan dengan aktivitas seru dengan dikelilingi ribuan pulau nan indah, surving dengan ribuan ikan cantik di pulau Bintan.', 15000000.00),
(4, '/Responsipwd/booking/asset/natunamain.jpg', 'Kepulauan Natuna', 'Rasakan liburan dengan aktivitas seru dengan dikelilingi ribuan pulau nan indah, surving dengan ribuan ikan cantik di laut Natuna.', 21000000.00),
(5, '/Responsipwd/booking/asset/special2main.jpg', 'Special Packages 2', 'Rasakan liburan dengan aktivitas seru dengan dikelilingi ribuan pulau nan indah, surving dengan ribuan ikan cantik di Kepulauan Riau.', 15000000.00),
(6, '/Responsipwd/booking/asset/bgmain.jpg', 'Special packages 4', 'Rasakan liburan dengan aktivitas seru dengan dikelilingi ribuan pulau nan indah, surving dengan ribuan ikan cantik di laut anambas.', 17000000.00),
(7, '/Responsipwd/booking/asset/karimunmain.jpg', 'Tb Karimun', 'Rasakan liburan dengan aktivitas seru dengan dikelilingi ribuan pulau nan indah, surving dengan ribuan ikan cantik di laut karimun.', 12000000.00),
(8, '/Responsipwd/booking/asset/tanjungpinangmain.jpg', 'Tanjungpinang', 'Rasakan liburan dengan aktivitas seru dengan dikelilingi ribuan pulau nan indah, surving dengan ribuan ikan cantik di ibukota melayu.', 17000000.00),
(9, '/Responsipwd/booking/asset/special3main.jpeg', 'Special Packages 3', 'Rasakan liburan dengan aktivitas seru dengan dikelilingi ribuan pulau nan indah, surving dengan ribuan ikan cantik di laut anambas.', 20000000.00),
(10, '/Responsipwd/booking/asset/bgmain1.jpg', 'Special Packages 5', 'Rasakan liburan dengan aktivitas seru dengan dikelilingi ribuan pulau nan indah, surving dengan ribuan ikan cantik di laut anambas.', 25000000.00),
(11, '/Responsipwd/booking/asset/linggamain.jpg', 'Lingga Island', 'Rasakan liburan dengan aktivitas seru dengan dikelilingi ribuan pulau nan indah, surving dengan ribuan ikan cantik di laut Lingga.', 10000000.00),
(12, '/Responsipwd/booking/asset/special1main.jpg', 'Special Packages 1', 'Rasakan liburan dengan aktivitas seru dengan dikelilingi ribuan pulau nan indah, surving dengan ribuan ikan cantik di laut anambas.', 12000000.00);

-- --------------------------------------------------------

--
--

CREATE TABLE `user` (
  `ID` int(11) NOT NULL,
  `Nama` text NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `RegistrationDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`ID`, `Nama`, `Email`, `Password`, `RegistrationDate`, `role`) VALUES
(52, 'Anggun Cantik', 'Sulis@gmail.com', '$2y$10$1Y8bazkLnBgYtu.43NIZKugaOmllDuuO3DLd5hfAuFNX.bbtDZ2p6', '2024-12-24 17:12:50', 'user'),
(53, 'saya', 'saya@gmail.com', '$2y$10$UKxSZrlWfoqWNdOYiuUcq.wkW/Xl9QqPqW90gVeITp5rjgs/6h0gm', '2024-12-25 08:23:06', 'user'),
(54, 'Noval Lias Ramadani', 'valleramadan46@gmail.com', '$2y$10$9w39vKumshPnVQ2XwcIT6uDT47NyGAtjw/rbbA6t3osfetizGWGdy', '2024-12-25 08:34:13', 'user'),
(55, 'Ramadan', 'rams@gmail.com', '$2y$10$h3CnFWlFGaBs0.lLFd.mve/IJo0EdV7xquKnUng/uyLSL2VBZNqa6', '2024-12-25 13:19:12', 'user'),
(56, 'namaku', 'namaku@gmail.com', '$2y$10$soaxTdcf.DiD.PAoZ6udC.ZP4fIoZGUw6H9Kp6SAui20Uz/uFktZe', '2024-12-25 13:43:08', 'user'),
(57, 'bapakkau', 'bapakkau@gmail.com', '$2y$10$CaKb0J95UAPxvpXyof9pGu7Hrm0IHknA99tfLL5wkIeWmW12jEDAK', '2024-12-25 14:10:28', 'user'),
(58, 'novall', 'novall@gmail.com', '$2y$10$I4riGHPJrox8PYkJzb6eJ.ttMzr8KH0FSF1CyQsmF2ytqPcFWg9rW', '2024-12-25 14:19:19', 'user'),
(59, 'val', 'val@gmail.com', '$2y$10$xUlLyOqa8HJ.vHZfKa7pReYgZ.MSjyd/a9DsQKOWxZKzZj/jmcXZC', '2024-12-25 14:28:42', 'user'),
(60, 'last', 'last@gmail.com', '$2y$10$EMViRvX3qbNLFqlAltEBMey/aR8MuoMVyTfd8V0wnx8DZOQWiLGLe', '2024-12-25 16:10:49', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tiket_wisata`
--
-- ALTER TABLE `tiket_wisata`
--   ADD PRIMARY KEY (`NIK`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
