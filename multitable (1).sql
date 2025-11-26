-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 26, 2025 at 08:40 PM
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
-- Database: `multitable`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager','staff') NOT NULL DEFAULT 'staff',
  `approved` enum('pending','approved','suspended') DEFAULT 'approved',
  `deleted` tinyint(1) DEFAULT 0,
  `restaurant_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','suspended') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `role`, `approved`, `deleted`, `restaurant_id`, `created_at`, `status`) VALUES
(1, 'JohnManager', 'manager@sunsetbistro.com', '$2y$10$Ha0dXwmtyEGBEjfMV.w3LeY1tpZPJfRuhplCx52Opm6Uct8p2K3hi', 'manager', 'approved', 0, 1, '2025-11-07 07:18:52', 'approved'),
(2, 'JaneStaff', 'staff@sunsetbistro.com', '$2y$10$0xbLFRrjsq7Sb/lmJBMxBuy/rBEZoh0aDRzxfIssP..Xu4FQXjcr2', 'staff', 'approved', 0, 1, '2025-11-07 07:20:05', 'pending'),
(3, 'Big Boss', 'boss@gmail.com', '$2y$10$u7NS4GliPEgkUM9K/XUVjOeCpF6j5W5r7ME0ut/i8Qii72uJb6tnK', 'manager', 'approved', 0, 9, '2025-11-07 08:07:14', 'approved'),
(6, 'Super Admin', 'admin@multitable.com', '$2y$10$T0j/WHVp8cwB7V8T6vzKXukcbDgwa9h7uXkX.AGhqhxxtpslHlPRy', 'admin', 'approved', 0, NULL, '2025-11-07 08:19:17', 'approved'),
(7, 'Trial', 'trial@gmail.com', '$2y$10$O3cdlgUVT71B53XM1Uzn5u3MVekQfDKX0VI23B4EY7v7kzzlE1d7a', 'manager', 'approved', 0, 10, '2025-11-07 09:33:10', 'pending'),
(8, 'Sakshi', 'sakshi1@gmail.com', '$2y$10$8w1nymNGglm8Rn5V7XjC4OaHHGMIYefiohxezuhwbzik6qFa.b98u', 'staff', 'pending', 0, 9, '2025-11-07 11:35:24', 'pending'),
(9, 'Aarti', 'aarti1@gmail.com', '$2y$10$9xCtzC9U0xfYt5gM6Pd4iu8Seq04IEFaqUm/eHNhsDwgx3XMKxaFq', 'manager', 'approved', 0, 11, '2025-11-24 08:05:30', 'pending'),
(10, 'Dodla', 'dodla@gmail.com', '$2y$10$HRicNWBIeNO9QvRGksKfBemcEm/CIscJKtuWBtV7CukIJVFFYk32W', 'manager', 'approved', 0, 3, '2025-11-24 08:07:36', 'pending'),
(11, 'Jay R.', 'jayr@gmail.com', '$2y$10$lF5rUSYOqpnKJs9mgkDT3OYb7wOoQrfMJombTHsYjlFZhAaWJ.iAu', 'manager', 'approved', 0, 2, '2025-11-24 08:13:15', 'pending'),
(12, 'Sakshi', 'sakshim@gmail.com', '$2y$10$Wc2xdAAQgO1cQ129WsNKf.qh5JdEuccTb.tkSmt0HRYJ4hY1u2dBS', 'manager', 'approved', 1, NULL, '2025-11-25 10:12:05', 'pending'),
(13, 'sakshi123', 'sakshi123@gmail.com', '$2y$10$B2amxpSPwgqm6pF1RMJp5.XV8fyP4hxRKDfzrBzkVF9PaBIadfp5y', 'manager', 'approved', 0, 13, '2025-11-25 10:16:21', 'pending'),
(15, 'Sakshi M1', 'sakshi1234@gmail.com', '123456', 'manager', 'approved', 0, 12, '2025-11-25 10:22:08', 'approved'),
(16, 'Sakshi S Final', 'sakshisf@hotel.com', '$2y$10$dGE2lZp9H2LhGL4D7cJkxezeXguehS57F1z6TeED2jPZbD8ZHyVMO', 'manager', 'approved', 0, 12, '2025-11-25 10:26:17', 'approved'),
(17, 'Queen', 'queen@gmail.com', '$2y$10$l94iWi91g8TNrUx2fi3qn.NKmicoyuEC2xLt5F0YApeocwYNPrwQu', 'staff', 'approved', 0, 1, '2025-11-26 07:24:49', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `table_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `guests` int(11) DEFAULT 2,
  `special_request` text DEFAULT NULL,
  `payment_status` enum('pending','paid') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `restaurant_id`, `table_id`, `booking_date`, `booking_time`, `guests`, `special_request`, `payment_status`, `created_at`, `status`) VALUES
(1, 12, 4, 5, '2025-11-07', '16:04:00', 2, NULL, 'pending', '2025-11-06 08:33:54', 'confirmed'),
(2, 12, 1, 1, '2025-11-07', '16:04:00', 2, NULL, 'pending', '2025-11-06 08:34:44', 'confirmed'),
(3, 12, 4, 5, '2025-11-07', '14:06:00', 2, NULL, 'paid', '2025-11-06 08:36:29', 'confirmed'),
(4, 13, 1, 1, '2025-11-08', '17:23:00', 4, NULL, 'paid', '2025-11-06 08:51:39', 'confirmed'),
(5, 12, 7, 9, '2025-11-07', '16:38:00', 2, NULL, 'pending', '2025-11-06 09:07:55', 'confirmed'),
(6, 12, 1, 1, '2025-11-07', '16:38:00', 2, NULL, 'pending', '2025-11-06 09:11:55', 'confirmed'),
(7, 12, 1, 2, '2025-11-07', '16:38:00', 2, NULL, 'pending', '2025-11-06 09:12:09', 'pending'),
(8, 12, 1, 2, '2025-11-08', '15:54:00', 4, NULL, 'pending', '2025-11-06 09:23:58', 'pending'),
(9, 12, 7, 9, '2025-11-07', '17:11:00', 2, NULL, 'pending', '2025-11-06 09:39:50', 'pending'),
(10, 12, 7, 9, '2025-11-07', '17:11:00', 2, NULL, 'pending', '2025-11-06 09:40:52', 'pending'),
(11, 12, 7, 9, '2025-11-07', '17:11:00', 2, NULL, 'pending', '2025-11-06 09:41:56', 'pending'),
(12, 12, 6, 8, '2025-11-07', '17:11:00', 2, NULL, 'pending', '2025-11-06 09:58:14', 'pending'),
(13, 12, 6, 8, '2025-11-07', '17:11:00', 2, NULL, 'pending', '2025-11-06 10:00:33', 'pending'),
(14, 12, 6, 8, '2025-11-07', '16:43:00', 2, NULL, 'pending', '2025-11-06 10:12:55', 'pending'),
(15, 12, 6, 8, '2025-11-07', '16:43:00', 2, NULL, 'pending', '2025-11-06 10:14:44', 'pending'),
(16, 12, 6, 8, '2025-11-07', '16:43:00', 2, NULL, 'pending', '2025-11-06 10:29:26', 'pending'),
(17, 12, 6, 8, '2025-11-07', '16:43:00', 2, NULL, 'pending', '2025-11-06 10:33:27', 'confirmed'),
(18, 12, 2, 3, '2025-11-07', '18:05:00', 4, NULL, 'pending', '2025-11-06 11:34:20', 'confirmed'),
(19, 12, 4, 5, '2025-11-07', '00:46:00', 2, NULL, 'pending', '2025-11-07 06:15:54', 'confirmed'),
(20, 12, 8, 10, '2025-11-20', '01:58:00', 4, NULL, 'paid', '2025-11-07 06:27:02', 'confirmed'),
(21, 14, 6, 8, '2025-10-29', '12:06:00', 2, NULL, 'pending', '2025-11-07 06:32:17', 'pending'),
(22, 12, 1, 1, '2025-11-08', '13:12:00', 2, NULL, 'pending', '2025-11-07 06:41:55', 'confirmed'),
(23, 12, 6, 8, '2025-11-07', '17:01:00', 2, NULL, 'pending', '2025-11-07 08:30:40', 'pending'),
(24, 12, 6, 8, '2025-11-07', '17:01:00', 2, NULL, 'pending', '2025-11-07 08:33:17', 'confirmed'),
(25, 12, 4, 5, '2025-11-12', '18:27:00', 4, NULL, 'pending', '2025-11-07 08:54:35', 'confirmed'),
(26, 15, 1, 6, '2025-11-25', '14:30:00', 4, NULL, 'pending', '2025-11-24 07:08:22', 'confirmed'),
(27, 15, 4, 5, '2025-11-25', '14:30:00', 4, NULL, 'pending', '2025-11-24 07:35:11', 'confirmed'),
(28, 16, 1, 6, '2025-11-26', '17:14:00', 4, NULL, 'pending', '2025-11-24 08:42:27', 'confirmed'),
(29, 17, 1, 1, '2025-11-27', '01:48:00', 2, NULL, 'pending', '2025-11-25 06:16:28', 'confirmed'),
(30, 17, 3, 4, '2025-11-25', '14:10:00', 4, NULL, 'pending', '2025-11-25 06:38:11', 'pending'),
(31, 12, 4, 7, '2025-11-26', '17:34:00', 8, NULL, 'pending', '2025-11-25 10:03:39', 'pending'),
(32, 18, 1, 2, '2025-11-27', '01:35:00', 4, NULL, 'pending', '2025-11-26 06:03:31', 'confirmed'),
(33, 19, 4, 7, '2025-11-06', '01:57:00', 4, NULL, 'pending', '2025-11-26 06:25:18', 'pending'),
(34, 19, 1, 2, '2025-11-27', '01:00:00', 4, NULL, 'pending', '2025-11-26 06:28:12', 'confirmed'),
(35, 19, 1, 2, '2025-11-27', '14:21:00', 4, NULL, 'pending', '2025-11-26 06:50:03', 'confirmed'),
(36, 12, 4, 7, '2025-11-13', '15:32:00', 4, NULL, 'pending', '2025-11-26 08:00:33', 'pending'),
(37, 12, 1, 2, '2025-11-13', '15:32:00', 4, NULL, 'pending', '2025-11-26 08:00:48', 'confirmed'),
(38, 19, 4, 5, '2025-11-27', '15:18:00', 2, NULL, 'pending', '2025-11-26 09:46:53', 'confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `booking_services`
--

CREATE TABLE `booking_services` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `extra_service_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_services`
--

INSERT INTO `booking_services` (`id`, `booking_id`, `extra_service_id`, `created_at`) VALUES
(9, 13, 12, '2025-11-06 10:11:57'),
(10, 14, 12, '2025-11-06 10:12:58'),
(11, 15, 12, '2025-11-06 10:16:51'),
(12, 15, 13, '2025-11-06 10:16:51'),
(13, 16, 12, '2025-11-06 10:29:30'),
(19, 17, 12, '2025-11-06 11:33:15'),
(21, 22, 2, '2025-11-07 06:42:11'),
(23, 26, 3, '2025-11-24 07:08:36'),
(24, 28, 4, '2025-11-24 08:42:38'),
(25, 29, 2, '2025-11-25 06:16:33'),
(26, 32, 2, '2025-11-26 06:03:34'),
(27, 35, 2, '2025-11-26 06:50:09'),
(28, 35, 11, '2025-11-26 06:50:09'),
(29, 37, 1, '2025-11-26 08:01:02');

-- --------------------------------------------------------

--
-- Table structure for table `extra_services`
--

CREATE TABLE `extra_services` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `category` enum('decorations','entertainment','catering','special setup') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `extra_services`
--

INSERT INTO `extra_services` (`id`, `restaurant_id`, `category_id`, `name`, `description`, `price`, `category`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Candlelight Setup', 'Romantic candle setup for special evenings', 500.00, NULL, '2025-11-06 09:52:46', '2025-11-06 09:52:46'),
(2, 1, 1, 'Flower Decoration', 'Elegant floral table arrangements', 700.00, NULL, '2025-11-06 09:52:46', '2025-11-06 09:52:46'),
(3, 1, 1, 'Theme Lighting', 'Custom lighting for special events', 1200.00, NULL, '2025-11-06 09:52:46', '2025-11-06 09:52:46'),
(4, 1, 2, 'Live Music', 'Acoustic band for dinner ambiance', 1000.00, NULL, '2025-11-06 09:52:46', '2025-11-06 09:52:46'),
(5, 1, 2, 'DJ Night', 'Professional DJ setup for parties', 2000.00, NULL, '2025-11-06 09:52:46', '2025-11-06 09:52:46'),
(6, 1, 3, 'Buffet Setup', 'All-you-can-eat buffet service', 1500.00, NULL, '2025-11-06 09:52:46', '2025-11-06 09:52:46'),
(7, 1, 3, 'Custom Menu', 'Chef-special curated meal', 2000.00, NULL, '2025-11-06 09:52:46', '2025-11-06 09:52:46'),
(8, 1, 3, 'Premium Drinks', 'Cocktails and mocktails', 1200.00, NULL, '2025-11-06 09:52:46', '2025-11-06 09:52:46'),
(9, 1, 4, 'Private Cabin', 'Exclusive private area for guests', 2500.00, NULL, '2025-11-06 09:52:46', '2025-11-06 09:52:46'),
(10, 1, 4, 'Rooftop View', 'Scenic rooftop dining experience', 3000.00, NULL, '2025-11-06 09:52:46', '2025-11-06 09:52:46'),
(11, 1, 4, 'VIP Lounge', 'High-end VIP table experience', 3500.00, NULL, '2025-11-06 09:52:46', '2025-11-06 09:52:46'),
(12, 6, 0, 'Five Star Meal', 'This is my trial', 500.00, 'catering', '2025-11-06 09:57:53', '2025-11-06 09:57:53'),
(13, 6, 0, 'Birthday', 'Birthday Hotel', 100.00, 'catering', '2025-11-06 10:16:28', '2025-11-06 10:16:28'),
(14, 8, 0, 'Birthday', 'Party and Celebrations', 500.00, 'entertainment', '2025-11-07 06:26:07', '2025-11-07 06:26:07');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `upi_id` varchar(200) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('success','failed') DEFAULT 'success',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` enum('upi','cash') DEFAULT 'upi',
  `reference_no` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `upi_id`, `amount`, `payment_date`, `status`, `created_at`, `payment_method`, `reference_no`) VALUES
(1, 34, NULL, 200.00, '2025-11-26 12:31:32', 'success', '2025-11-26 07:01:32', 'cash', NULL),
(2, 34, 'qwe@upi', 200.00, '2025-11-26 12:32:45', '', '2025-11-26 07:02:45', 'upi', NULL),
(3, 34, NULL, 200.00, '2025-11-26 12:33:00', 'success', '2025-11-26 07:03:00', 'cash', NULL),
(4, 35, NULL, 4400.00, '2025-11-26 12:37:29', 'success', '2025-11-26 07:07:29', 'cash', NULL),
(5, 32, NULL, 900.00, '2025-11-26 12:47:33', 'success', '2025-11-26 07:17:33', 'cash', NULL),
(6, 37, NULL, 700.00, '2025-11-26 13:31:17', 'success', '2025-11-26 08:01:17', 'cash', NULL),
(7, 38, NULL, 30.00, '2025-11-26 15:16:59', 'success', '2025-11-26 09:46:59', 'cash', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `cuisine` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT 4.5,
  `price_range` varchar(10) DEFAULT '$$'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `cuisine`, `location`, `description`, `image`, `rating`, `price_range`) VALUES
(1, 'Ocean View Dine', 'Seafood', 'Goa', 'Fresh catches served with ocean breeze.', 'assets/images/hilton.jpeg', 4.6, '$$$'),
(2, 'Urban Grill', 'Barbecue', 'Mumbai', 'Smoky flavors and rooftop vibes.', 'https://images.unsplash.com/photo-1528605248644-14dd04022da1', 4.4, '$$'),
(3, 'Spice Route', 'Indian', 'Delhi', 'Aromatic curries and tandoor classics.', 'assets/images/resturant.jpeg', 4.8, '$$'),
(4, 'La De La Qruz', 'French – pastries, crepes, ratatouille', 'Pune, Shivajinagar', 'Fine Dining – gourmet meals, multi-course menus', 'assets/images/im1.jpeg', 4.0, '100 $$'),
(5, 'NEW RESTAURANT ', 'African', 'INDIA', 'Best food ever', 'assets/images/image2s.jpeg', 4.0, '120 $$'),
(6, 'Hilton Hotel', 'Indian', 'Khadki', 'Pilau, Biryani', 'assets/images/ocen.jpeg', 4.0, '100$$'),
(7, 'Switz Restaurant', 'Sri Lankan', 'Chennai', 'Sri Lankan Food', 'assets/images/images3.jpeg', 4.0, '100-200$$'),
(8, 'Modern', 'Local Food', 'Kenya', 'Fish n Chicken', 'assets/images/im3.jpeg', 4.0, '100'),
(9, 'Hilton 2', '', '', NULL, NULL, 4.5, '$$'),
(10, 'Big Pies', '', '', NULL, NULL, 4.5, '$$'),
(11, 'Big Pies', '', '', NULL, NULL, 4.5, '$$'),
(12, 'Aundh Modern Hotel', 'Indian', 'Aundh, Pune', NULL, '321.jpeg', 4.5, '$$'),
(13, 'Aundh Modern Hotel', '', '', NULL, NULL, 4.5, '$$');

-- --------------------------------------------------------

--
-- Table structure for table `service_categories`
--

CREATE TABLE `service_categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_categories`
--

INSERT INTO `service_categories` (`id`, `category_name`) VALUES
(1, 'Decorations'),
(2, 'Entertainment'),
(3, 'Catering'),
(4, 'Special Setup');

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `table_type` varchar(100) DEFAULT NULL,
  `capacity` int(11) DEFAULT 2,
  `premium_fee` decimal(8,2) DEFAULT 0.00,
  `available` tinyint(1) DEFAULT 1,
  `status` enum('available','booked') NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`id`, `restaurant_id`, `table_name`, `table_type`, `capacity`, `premium_fee`, `available`, `status`) VALUES
(1, 1, 'Window Table 1', 'Ocean View', 4, 300.00, 1, ''),
(2, 1, 'Patio Booth', 'Private Booth', 2, 200.00, 1, ''),
(3, 2, 'Rooftop 1', 'Open Air', 4, 250.00, 1, ''),
(4, 3, 'Royal Table', 'Luxury Booth', 6, 400.00, 1, ''),
(5, 4, 'Window Table', 'Balcony', 2, 30.00, 1, ''),
(6, 1, 'Oceanic View', 'Window View', 2, 130.00, 1, ''),
(7, 4, 'Window Table', 'Balcony', 2, 30.00, 1, ''),
(8, 6, 'Rooftop 360', 'Rooftop', 2, 90.00, 1, ''),
(9, 7, 'Basement Table', 'Basement', 2, 50.00, 1, ''),
(10, 8, 'Modern', 'Modern', 4, 55.00, 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin','manager','staff') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(2, 'qwe', 'qwe@gmail.com', 'admin123', 'customer', '2025-11-04 18:32:24'),
(4, 'Hotel Manager', 'manager@multitable.com', '$2y$10$w9Vq0kqDjHtTrI4ZB.1mFOMi3bAMUfXEjvQfLq8hiZSLdYg1OFBZe', 'manager', '2025-11-04 18:43:28'),
(5, 'Staff Member', 'staff@multitable.com', '$2y$10$w9Vq0kqDjHtTrI4ZB.1mFOMi3bAMUfXEjvQfLq8hiZSLdYg1OFBZe', 'staff', '2025-11-04 18:43:28'),
(9, 'Super Admin', 'superadmin@multitable.com', '$2y$10$fXuSUT3VMKrr9j2JaVSoeeWsrNkN6JycZQtHrXKWcN/cfVAWA1mUe', 'admin', '2025-11-04 19:10:11'),
(10, 'Taster', 'taste@gmail.com', '$2y$10$eZjEr.yphpAnZfPi0Zl7OeCqzSJkOtertz0ri9HlWDrB6oo0T3bSS', 'customer', '2025-11-04 19:22:17'),
(11, 'Michael', 'm@gmail.com', '$2y$10$net.ZgQHlcGyDuclNfDY5eYCJTeE9ckK5tftwax7wY5xpNNGrS5GK', 'customer', '2025-11-05 10:41:23'),
(12, 'sakshi', 'sakshi@gmail.com', '$2y$10$BKTZaV24/lpiTsvJ7FEKXOwfkdpJNJTUJchnFJkeitfTP/QrsLIOS', 'customer', '2025-11-06 07:20:07'),
(13, 'Aarti', 'aarti@gmail.com', '$2y$10$lze.17DSGZxHqAlDcXQizO..RyBWBseZemEsTaInNukViYohMnnpy', 'customer', '2025-11-06 08:49:42'),
(14, 'Jay', 'jay123@gmail.com', '$2y$10$cH38e1vNc7ullzot8xjHRezDV3u/K0M1FoWXdSf4JxyDWQTaHX0ZC', 'customer', '2025-11-07 06:31:52'),
(15, 'Jay11', 'jay11@gmail.com', '$2y$10$tFTy8pHv.X5AjutgDjRhluD.ZKQMX1mn0D2qAYhTPdOWBgD89aS6S', 'customer', '2025-11-24 07:07:31'),
(16, 'SAKSHI123', 'sakshi123@gmail.com', '$2y$10$dU2Ojoq8ymfo8OMIMQ157.gkAaBgqKF9jEtcfaLLuW3nGjAg9fCwe', 'customer', '2025-11-24 08:41:57'),
(17, 'Sakshi S', 'sakshis@gmail.com', '$2y$10$pgyg/VQhR3C/EDvDWzTks.dal0dRllP.WWqBVmx.6289GgJVAVbTW', 'customer', '2025-11-25 06:16:09'),
(18, 'Aarti D.', 'aartid@gmail.com', '$2y$10$JOTgkoVMUsoK7vOgXC1QBOI5S/FG9LdoYz7GfQvj30Y.ICh1zlBDe', 'customer', '2025-11-26 06:03:15'),
(19, 'AAA', 'aaa@gmail.com', '$2y$10$aZDOG60CVfT7ShAekDZ7p.6wrjuVnj0/vNCK2KK2.p9sT1D8GKYNi', 'customer', '2025-11-26 06:25:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `table_id` (`table_id`),
  ADD KEY `fk_bookings_restaurant` (`restaurant_id`);

--
-- Indexes for table `booking_services`
--
ALTER TABLE `booking_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `extra_service_id` (`extra_service_id`);

--
-- Indexes for table `extra_services`
--
ALTER TABLE `extra_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `fk_extras_restaurants` (`restaurant_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_categories`
--
ALTER TABLE `service_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tables_restaurants` (`restaurant_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `booking_services`
--
ALTER TABLE `booking_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `extra_services`
--
ALTER TABLE `extra_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `service_categories`
--
ALTER TABLE `service_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bookings_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_services`
--
ALTER TABLE `booking_services`
  ADD CONSTRAINT `booking_services_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_services_ibfk_2` FOREIGN KEY (`extra_service_id`) REFERENCES `extra_services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `extra_services`
--
ALTER TABLE `extra_services`
  ADD CONSTRAINT `fk_extras_restaurants` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tables`
--
ALTER TABLE `tables`
  ADD CONSTRAINT `fk_tables_restaurants` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
