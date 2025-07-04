-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 04, 2025 at 07:40 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `delivery_notes`
--

CREATE TABLE `delivery_notes` (
  `id` int(11) NOT NULL,
  `delivery_note_number` varchar(50) NOT NULL,
  `customer_company` varchar(100) NOT NULL,
  `customer_street` varchar(100) DEFAULT NULL,
  `customer_city` varchar(50) DEFAULT NULL,
  `customer_postal_code` varchar(20) DEFAULT NULL,
  `customer_country` varchar(50) DEFAULT NULL,
  `sales_order_id` int(11) DEFAULT NULL,
  `shipping_date` date DEFAULT curdate(),
  `delivered_by` varchar(100) DEFAULT NULL,
  `recipient_name` varchar(100) DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_notes`
--

INSERT INTO `delivery_notes` (`id`, `delivery_note_number`, `customer_company`, `customer_street`, `customer_city`, `customer_postal_code`, `customer_country`, `sales_order_id`, `shipping_date`, `delivered_by`, `recipient_name`, `received_at`, `notes`, `created_by`, `created_at`) VALUES
(1, 'DN-1751212277', 'ITBienvenu', 'Nyamirambo', 'KIGALI', '', '', NULL, '2025-07-01', 'MENAMOTORS', 'Naone', '2025-06-29 16:18:50', 'No coments', 5, '2025-06-29 15:51:17');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_note_items`
--

CREATE TABLE `delivery_note_items` (
  `id` int(11) NOT NULL,
  `delivery_note_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `notes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_note_items`
--

INSERT INTO `delivery_note_items` (`id`, `delivery_note_id`, `product_id`, `quantity`, `notes`) VALUES
(3, 1, 2, 12, 'Nothing');

-- --------------------------------------------------------

--
-- Table structure for table `document_downloads`
--

CREATE TABLE `document_downloads` (
  `id` int(11) NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `document_id` int(11) NOT NULL,
  `document_number` varchar(50) NOT NULL,
  `downloaded_by` int(11) NOT NULL,
  `downloaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_downloads`
--

INSERT INTO `document_downloads` (`id`, `document_type`, `document_id`, `document_number`, `downloaded_by`, `downloaded_at`) VALUES
(1, 'delivery_note', 1, 'DN-1751212277', 5, '2025-06-29 15:52:28'),
(2, 'delivery_note', 1, 'DN-1751212277', 5, '2025-06-29 16:10:21'),
(3, 'goods_received_note', 2, 'GRN-1751320653', 5, '2025-07-01 06:01:40'),
(4, 'proforma_invoice', 3, 'PROF-1751463517', 7, '2025-07-02 13:38:45');

-- --------------------------------------------------------

--
-- Table structure for table `goods_received_notes`
--

CREATE TABLE `goods_received_notes` (
  `id` int(11) NOT NULL,
  `grn_number` varchar(50) NOT NULL,
  `po_id` int(11) DEFAULT NULL,
  `supplier_name` varchar(100) DEFAULT NULL,
  `receipt_date` date DEFAULT curdate(),
  `received_by` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `goods_received_notes`
--

INSERT INTO `goods_received_notes` (`id`, `grn_number`, `po_id`, `supplier_name`, `receipt_date`, `received_by`, `notes`, `created_at`) VALUES
(1, 'GRN-1751319244', 1, 'ITBienvenu', '2025-06-30', 5, 'None', '2025-06-30 21:34:04'),
(2, 'GRN-1751320653', 1, 'ITBienvenu', '2025-06-30', 5, 'nn', '2025-06-30 21:57:33');

-- --------------------------------------------------------

--
-- Table structure for table `goods_received_note_items`
--

CREATE TABLE `goods_received_note_items` (
  `id` int(11) NOT NULL,
  `grn_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity_received` int(11) NOT NULL,
  `condition_notes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `goods_received_note_items`
--

INSERT INTO `goods_received_note_items` (`id`, `grn_id`, `product_id`, `quantity_received`, `condition_notes`) VALUES
(1, 1, 2, 12, 'None'),
(2, 2, 3, 8, 'None');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `company` varchar(100) NOT NULL,
  `vat` varchar(50) DEFAULT NULL,
  `street` varchar(100) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `status` varchar(20) DEFAULT 'unpaid',
  `due_date` date DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message_content` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  `parent_message_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `subject`, `message_content`, `timestamp`, `is_read`, `parent_message_id`) VALUES
(20, 5, 9, 'Hello munezero', 'This my secret message', '2025-07-04 13:15:51', 1, NULL),
(23, 9, 5, 'To ask what is the task', 'Hello then what is the task', '2025-07-04 13:37:25', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `otp_codes`
--

CREATE TABLE `otp_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `otp_code` varchar(10) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `is_used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp_codes`
--

INSERT INTO `otp_codes` (`id`, `user_id`, `otp_code`, `created_at`, `expires_at`, `is_used`) VALUES
(1, 5, '669433', '2025-07-03 16:44:13', '2025-07-03 16:54:13', 1),
(2, 5, '534297', '2025-07-04 14:02:21', '2025-07-04 14:12:21', 1),
(3, 9, '129263', '2025-07-04 14:20:21', '2025-07-04 14:30:21', 1),
(4, 7, '336885', '2025-07-04 14:47:35', '2025-07-04 14:57:35', 1),
(5, 5, '898230', '2025-07-04 15:08:37', '2025-07-04 15:18:37', 1),
(6, 5, '263651', '2025-07-04 16:53:29', '2025-07-04 16:54:29', 0),
(7, 5, '431806', '2025-07-04 16:55:40', '2025-07-04 17:05:40', 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `cost_price` decimal(10,2) DEFAULT 0.00,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `low_stock` int(11) DEFAULT 5,
  `supplier` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `sku`, `description`, `cost_price`, `price`, `quantity`, `low_stock`, `supplier`, `image`, `created_by`, `created_at`) VALUES
(2, 'Computer', 'Computer', '1234567', 'This oc is cool', 120.00, 140.00, 36, 10, 'HP', '../uploads/1751156822_481823684_122195135336088817_9130727470038034147_n.jpg', 5, '2025-06-28 23:06:00'),
(3, 'Phonee', 'Accesory', '88', 'cool', 300.00, 380.00, 9, 20, 'Amazon', '../uploads/1751157023_carbon (1).png', 5, '2025-06-29 00:30:23'),
(5, 'HP ENVY 1080', 'COMPUTER', '78678', 'This pc was received on debit', 600.00, 750.00, 4, 3, 'HP', '../uploads/product_6864f658e0f92.jpeg', 5, '2025-07-02 09:05:28'),
(6, 'HP EliteBook G3 i5 6th Gen 8GB RAM 256GB SSD', 'Computer', '1234', 'HP EliteBook G3 i5 6th Gen 8GB RAM 256GB SSD,\r\nTouch screen', 280.00, 380.00, 10, 5, 'UMUCYO', '../uploads/product_6864fd04370fb.jpeg', 5, '2025-07-02 09:33:56'),
(7, 'Iphone 12', 'Phone', '8987', 'Iphone 12 with 256GB STORAGE 8GB RAM,', 320.00, 450.00, 12, 5, 'Apple', '../uploads/product_68652da3c4872.jpeg', 7, '2025-07-02 13:01:23');

-- --------------------------------------------------------

--
-- Table structure for table `proforma_invoices`
--

CREATE TABLE `proforma_invoices` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `company` varchar(100) NOT NULL,
  `vat` varchar(50) DEFAULT NULL,
  `street` varchar(100) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `proforma_invoices`
--

INSERT INTO `proforma_invoices` (`id`, `invoice_number`, `company`, `vat`, `street`, `city`, `postal_code`, `country`, `status`, `created_by`, `created_at`) VALUES
(1, 'PROF-1751153035', 'ITBienvenu1', '123456789', 'Nyamirambo', 'Kigali', '000', 'Rwanda', 'pending', 5, '2025-06-28 23:23:55'),
(2, 'PROF-1751157068', 'Muneza', '12346', 'Nyamirambo', 'Kigali', '000', 'Rwanda', 'pending', 5, '2025-06-29 00:31:08'),
(3, 'PROF-1751463517', 'AGASUSURUKO', '102973892', 'RUHANGO,KR20', 'SOUTH', '000', 'RWANDA', 'pending', 7, '2025-07-02 13:38:37'),
(4, 'PROF-1751464560', 'KINAZI', '12345678', 'Nyamirambo', 'Kigali', '000', 'RWANDA', 'pending', 7, '2025-07-02 13:56:00');

-- --------------------------------------------------------

--
-- Table structure for table `proforma_items`
--

CREATE TABLE `proforma_items` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `proforma_items`
--

INSERT INTO `proforma_items` (`id`, `invoice_id`, `product_id`, `quantity`, `unit_price`, `total_price`) VALUES
(4, 1, 2, 2, 140.00, 280.00),
(11, 2, 2, 10, 140.00, 1400.00),
(12, 2, 3, 10, 380.00, 3800.00),
(13, 2, 2, 2, 140.00, 280.00),
(15, 3, 3, 2, 380.00, 760.00),
(16, 4, 5, 2, 750.00, 1500.00);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` int(11) NOT NULL,
  `po_number` varchar(50) NOT NULL,
  `supplier_name` varchar(100) NOT NULL,
  `supplier_contact_person` varchar(100) DEFAULT NULL,
  `supplier_email` varchar(100) DEFAULT NULL,
  `supplier_phone` varchar(50) DEFAULT NULL,
  `supplier_address` varchar(255) DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `order_date` date DEFAULT curdate(),
  `expected_delivery_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `po_number`, `supplier_name`, `supplier_contact_person`, `supplier_email`, `supplier_phone`, `supplier_address`, `total_amount`, `order_date`, `expected_delivery_date`, `status`, `notes`, `created_by`, `created_at`) VALUES
(1, 'PO-1751215405', 'ITBienvenu', 'BAGALE Gloire', 'bienvenugashema@gmail.com', '90456789', 'Nyamirambo', 1440.00, '2025-06-29', '2025-06-25', 'received', 'Nothing else', 5, '2025-06-29 16:43:25');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_cost` decimal(10,2) NOT NULL,
  `total_cost` decimal(12,2) NOT NULL,
  `notes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`id`, `po_id`, `product_id`, `quantity`, `unit_cost`, `total_cost`, `notes`) VALUES
(2, 1, 2, 12, 120.00, 1440.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sales_orders`
--

CREATE TABLE `sales_orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `company` varchar(100) NOT NULL,
  `vat` varchar(50) DEFAULT NULL,
  `street` varchar(100) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `order_date` date DEFAULT curdate(),
  `delivery_date` date DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_orders`
--

INSERT INTO `sales_orders` (`id`, `order_number`, `company`, `vat`, `street`, `city`, `postal_code`, `country`, `total_amount`, `status`, `order_date`, `delivery_date`, `created_by`, `created_at`) VALUES
(2, 'SO-1751272121', 'ITBienvenu', '12346', 'Nyamirambo', 'Kigali', '000', 'Rwanda', 1400.00, 'shipped', '2025-06-30', '2025-07-02', 5, '2025-06-30 08:28:41');

-- --------------------------------------------------------

--
-- Table structure for table `sales_order_items`
--

CREATE TABLE `sales_order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_order_items`
--

INSERT INTO `sales_order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`, `total_price`) VALUES
(37, 2, 2, 10, 140.00, 1400.00);

-- --------------------------------------------------------

--
-- Table structure for table `stock_logs`
--

CREATE TABLE `stock_logs` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `action` enum('stock_in','stock_out') NOT NULL,
  `quantity_changed` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `movement_type` varchar(50) NOT NULL,
  `quantity_change` int(11) NOT NULL,
  `current_stock_after` int(11) NOT NULL,
  `reference_document_type` varchar(50) DEFAULT NULL,
  `reference_document_id` int(11) DEFAULT NULL,
  `reference_document_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `moved_by` int(11) NOT NULL,
  `movement_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `product_id`, `movement_type`, `quantity_change`, `current_stock_after`, `reference_document_type`, `reference_document_id`, `reference_document_number`, `notes`, `moved_by`, `movement_timestamp`) VALUES
(2, 2, 'adjustment_add', 12, 24, 'Manual Adjustment', NULL, '1234567', 'Something got inserted', 5, '2025-07-01 07:20:15'),
(3, 5, 'inbound_creation', 4, 4, 'Product Creation', 5, '78678', 'Initial stock upon product creation', 5, '2025-07-02 09:05:28'),
(4, 6, 'inbound_creation', 10, 10, 'Product Creation', 6, '1234', 'Initial stock upon product creation', 5, '2025-07-02 09:33:56'),
(5, 2, 'adjustment_add', 12, 36, 'Manual Adjustment', NULL, '1234567', '1', 7, '2025-07-02 12:24:06'),
(6, 7, 'inbound_creation', 12, 12, 'Product Creation', 7, '8987', 'Initial stock upon product creation', 7, '2025-07-02 13:01:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(12) NOT NULL,
  `names` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone` int(14) NOT NULL,
  `password` varchar(300) NOT NULL,
  `role` varchar(12) NOT NULL,
  `time` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `names`, `email`, `phone`, `password`, `role`, `time`) VALUES
(5, 'MWIMULE Bienvenu', 'bienvenugashema@gmail.com', 781300739, '$2y$10$7CdsHvD6/SGQU2ZWzbkykeT16VshyhFi04IQYIKJkm0XliXHe31ba', 'executive', '2025-06-28 22:10:33.017584'),
(7, 'GASHEMA', 'mwimulegashema@gmail.com', 736701735, '$2y$10$qSCteFqChnAR6PNJ5RSHSuPxADCnKWvQK4nXfXJs1vfWyUM1ZIkMy', 'admin', '2025-07-01 22:34:37.495720'),
(9, 'Kalisa Munezero', 'munezero123@gmail.com', 123456789, '$2y$10$YpOAqA27qKoG2zlavnl2vOVX398H.axzk7BkpRGvtSG9oNQs8gKJi', 'daily', '2025-07-02 15:33:12.469857');

-- --------------------------------------------------------

--
-- Table structure for table `user_activity_logs`
--

CREATE TABLE `user_activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) DEFAULT NULL,
  `module` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(100) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `page` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_activity_logs`
--

INSERT INTO `user_activity_logs` (`id`, `user_id`, `action`, `module`, `details`, `ip_address`, `user_agent`, `session_id`, `page`, `created_at`) VALUES
(1, 5, 'Logged in', 'Authentication', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, NULL, '2025-07-02 15:54:31'),
(2, 5, 'Logged in', 'Authentication', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, NULL, '2025-07-02 15:55:18'),
(3, 5, 'Visited Dashboard', '', 'User entered executive dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/executive/form.php', '2025-07-02 15:55:29'),
(4, 9, 'Logged in', 'Authentication', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, NULL, '2025-07-02 16:42:42'),
(5, 9, 'Visited Daily Seller Dashboard', '', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/index.php', '2025-07-02 16:49:49'),
(6, 9, 'Visited Dashboard', '', 'User entered seller dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/index.php', '2025-07-02 16:56:57'),
(7, 9, 'Visited Dashboard', '', 'User entered seller dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/index.php', '2025-07-02 16:57:11'),
(8, 9, 'Visited Dashboard', '', 'User entered seller dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/index.php', '2025-07-02 17:00:35'),
(9, 9, 'Visited Dashboard', '', 'User entered seller dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/index.php', '2025-07-02 17:02:52'),
(10, 9, 'Visited Create Sales Order Page', '', 'Saller navigated to create sales order form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/create_sales_order.php', '2025-07-02 17:10:33'),
(11, 9, 'Visited Create Sales Order Page', '', 'Saller navigated to create sales order form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/create_sales_order.php', '2025-07-02 17:13:18'),
(12, 9, 'Visited My Orders Page', '', 'User viewed their sales orders', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/my_orders.php', '2025-07-02 17:13:20'),
(13, 9, 'Visited My Orders Page', '', 'User viewed their sales orders', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/my_orders.php', '2025-07-02 17:13:41'),
(14, 9, 'Visited My Profile Page', '', 'User viewed their profile', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/my_profile.php', '2025-07-02 17:13:44'),
(15, 9, 'Visited My Profile Page', '', 'User viewed their profile', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/my_profile.php', '2025-07-02 17:14:28'),
(16, 9, 'Visited My Profile Page', '', 'User viewed their profile', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/my_profile.php', '2025-07-02 17:14:33'),
(17, 9, 'Visited Create Sales Order Page', '', 'Saller navigated to create sales order form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/create_sales_order.php', '2025-07-02 17:14:55'),
(18, 9, 'Visited Create Sales Order Page', '', 'User navigated to create sales order form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/create_sales_order.php', '2025-07-02 17:19:34'),
(19, 9, 'Visited Create Sales Order Page', '', 'User navigated to create sales order form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/create_sales_order.php', '2025-07-02 17:19:51'),
(20, 9, 'Visited Create Sales Order Page', '', 'User navigated to create sales order form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/create_sales_order.php', '2025-07-02 17:25:20'),
(21, 9, 'Visited My Orders Page', '', 'User viewed their sales orders', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/my_orders.php', '2025-07-02 17:25:24'),
(22, 9, 'Visited My Orders Page', '', 'User viewed their sales orders', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/my_orders.php', '2025-07-02 17:27:35'),
(23, 9, 'Visited Dashboard', '', 'User entered seller dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/index.php', '2025-07-02 17:27:36'),
(24, 9, 'Visited Create Sales Order Page', '', 'User navigated to create sales order form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/create_sales_order.php', '2025-07-02 17:27:41'),
(25, 9, 'Visited My Orders Page', '', 'User viewed their sales orders', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/my_orders.php', '2025-07-02 17:27:43'),
(26, 9, 'Visited My Profile Page', '', 'User viewed their profile', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/my_profile.php', '2025-07-02 17:27:44'),
(27, 9, 'Visited My Orders Page', '', 'User viewed their sales orders', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/my_orders.php', '2025-07-02 17:27:47'),
(28, 9, 'Visited Create Sales Order Page', '', 'User navigated to create sales order form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/create_sales_order.php', '2025-07-02 17:27:48'),
(29, 9, 'Visited Dashboard', '', 'User entered seller dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/index.php', '2025-07-02 17:27:50'),
(30, 9, 'Visited My Profile Page', '', 'User viewed their profile', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/my_profile.php', '2025-07-02 17:28:05'),
(31, 9, 'Visited My Profile Page', '', 'User viewed their profile', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/my_profile.php', '2025-07-02 17:28:29'),
(32, 9, 'Visited My Profile Page', '', 'User viewed their profile', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/my_profile.php', '2025-07-02 17:29:45'),
(33, 9, 'Visited Dashboard', '', 'User entered seller dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/index.php', '2025-07-02 17:34:47'),
(34, 9, 'Visited Create Sales Order Page', '', 'User navigated to create sales order form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/create_sales_order.php', '2025-07-02 17:34:56'),
(35, 9, 'Visited My Orders Page', '', 'User viewed their sales orders', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/my_orders.php', '2025-07-02 17:34:57'),
(36, 9, 'Visited Create Sales Order Page', '', 'User navigated to create sales order form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/create_sales_order.php', '2025-07-02 17:34:58'),
(37, 9, 'Visited Dashboard', '', 'User entered seller dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/index.php', '2025-07-02 17:35:00'),
(38, 9, 'Visited Dashboard', '', 'User entered seller dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/index.php', '2025-07-02 17:41:43'),
(39, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 17:41:44'),
(40, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 17:42:41'),
(41, 9, 'Message Sent', '', 'User 9 sent message to 7 with subject: \'Account Recovery Request\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 17:42:41'),
(42, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 17:42:41'),
(43, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 17:48:49'),
(44, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 17:49:09'),
(45, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 17:50:07'),
(46, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 17:50:48'),
(47, 9, 'Attempting to Send Message', '', 'User 9 attempting to send message to 5 with subject: \'Account Recovery Request\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 17:50:48'),
(48, 9, 'Message Sent Successfully', '', 'User 9 sent message to 5 with subject: \'Account Recovery Request\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 17:50:48'),
(49, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 17:50:48'),
(50, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 17:59:07'),
(51, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:00:50'),
(52, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:00:53'),
(53, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:00:53'),
(54, 9, 'Visited My Profile Page', '', 'User viewed their profile', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/my_profile.php', '2025-07-02 18:01:12'),
(55, 9, 'Visited Create Sales Order Page', '', 'User navigated to create sales order form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/create_sales_order.php', '2025-07-02 18:01:14'),
(56, 9, 'Visited Dashboard', '', 'User entered seller dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/index.php', '2025-07-02 18:01:16'),
(57, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:01:20'),
(58, 5, 'Logged in', 'Authentication', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, NULL, '2025-07-02 18:03:07'),
(59, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/executive/index.php', '2025-07-02 18:12:46'),
(60, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/executive/index.php', '2025-07-02 18:13:11'),
(61, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/executive/index.php', '2025-07-02 18:14:05'),
(62, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/executive/index.php', '2025-07-02 18:15:17'),
(63, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/executive/index.php', '2025-07-02 18:16:44'),
(64, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/executive/index.php', '2025-07-02 18:18:36'),
(65, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/executive/index.php', '2025-07-02 18:18:50'),
(66, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/executive/index.php', '2025-07-02 18:19:49'),
(67, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/executive/index.php', '2025-07-02 18:19:55'),
(68, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:20:15'),
(69, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:21:49'),
(70, 5, 'Attempting to Send Message', '', 'User 5 attempting to send message to 5 with subject: \'h\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:21:49'),
(71, 5, 'Message Sent Successfully', '', 'User 5 sent message to 5 with subject: \'h\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:21:49'),
(72, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:21:49'),
(73, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:22:03'),
(74, 5, 'Visited My Profile Page', '', 'User viewed their profile', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/my_profile.php', '2025-07-02 18:22:05'),
(75, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/executive/index.php', '2025-07-02 18:22:12'),
(76, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:22:20'),
(77, 9, 'Logged in', 'Authentication', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, NULL, '2025-07-02 18:22:36'),
(78, 9, 'Visited Dashboard', '', 'User entered seller dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/index.php', '2025-07-02 18:22:36'),
(79, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:22:41'),
(80, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:23:19'),
(81, 9, 'Attempting to Send Message', '', 'User 9 attempting to send message to 7 with subject: \'hhh\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:23:19'),
(82, 9, 'Message Sent Successfully', '', 'User 9 sent message to 7 with subject: \'hhh\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:23:19'),
(83, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:23:19'),
(84, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:23:40'),
(85, 9, 'Visited Dashboard', '', 'User entered seller dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/daily/index.php', '2025-07-02 18:23:40'),
(86, 5, 'Logged in', 'Authentication', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, NULL, '2025-07-02 18:24:01'),
(87, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/executive/index.php', '2025-07-02 18:24:01'),
(88, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/executive/index.php', '2025-07-02 18:24:04'),
(89, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/executive/index.php', '2025-07-02 18:26:02'),
(90, 9, 'Logged in', 'Authentication', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', NULL, NULL, '2025-07-02 18:26:51'),
(91, 9, 'Visited Dashboard', '', 'User entered seller dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '9br9ufug6aos4jgivoi3u6oj63', '/CoolAdmin/daily/index.php', '2025-07-02 18:26:51'),
(92, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '9br9ufug6aos4jgivoi3u6oj63', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:26:57'),
(93, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '9br9ufug6aos4jgivoi3u6oj63', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:27:10'),
(94, 9, 'Attempting to Send Message', '', 'User 9 attempting to send message to 7 with subject: \'22\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '9br9ufug6aos4jgivoi3u6oj63', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:27:10'),
(95, 9, 'Message Sent Successfully', '', 'User 9 sent message to 7 with subject: \'22\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '9br9ufug6aos4jgivoi3u6oj63', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:27:10'),
(96, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '9br9ufug6aos4jgivoi3u6oj63', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:27:10'),
(97, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/executive/index.php', '2025-07-02 18:27:18'),
(98, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/executive/index.php', '2025-07-02 18:27:19'),
(99, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/executive/index.php', '2025-07-02 18:27:20'),
(100, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '9br9ufug6aos4jgivoi3u6oj63', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:28:12'),
(101, 9, 'Attempting to Send Message', '', 'User 9 attempting to send message to 5 with subject: \'s\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '9br9ufug6aos4jgivoi3u6oj63', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:28:12'),
(102, 9, 'Message Sent Successfully', '', 'User 9 sent message to 5 with subject: \'s\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '9br9ufug6aos4jgivoi3u6oj63', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:28:12'),
(103, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '9br9ufug6aos4jgivoi3u6oj63', '/CoolAdmin/daily/send_message.php', '2025-07-02 18:28:12'),
(104, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/executive/index.php', '2025-07-02 18:28:15'),
(105, 7, 'Logged in', 'Authentication', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, NULL, '2025-07-02 18:28:41'),
(106, 5, 'Logged in', 'Authentication', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', NULL, NULL, '2025-07-02 18:36:33'),
(107, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '9br9ufug6aos4jgivoi3u6oj63', '/CoolAdmin/executive/index.php', '2025-07-02 18:36:34'),
(108, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '9br9ufug6aos4jgivoi3u6oj63', '/CoolAdmin/executive/index.php', '2025-07-02 18:38:39'),
(109, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '9br9ufug6aos4jgivoi3u6oj63', '/CoolAdmin/executive/index.php', '2025-07-02 18:40:03'),
(110, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '9br9ufug6aos4jgivoi3u6oj63', '/CoolAdmin/executive/index.php', '2025-07-02 18:42:39'),
(111, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '9br9ufug6aos4jgivoi3u6oj63', '/CoolAdmin/executive/index.php', '2025-07-02 18:42:59'),
(112, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '9br9ufug6aos4jgivoi3u6oj63', '/CoolAdmin/executive/index.php', '2025-07-02 18:56:07'),
(113, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '9br9ufug6aos4jgivoi3u6oj63', '/CoolAdmin/executive/index.php', '2025-07-02 18:58:23'),
(114, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '9br9ufug6aos4jgivoi3u6oj63', '/CoolAdmin/executive/index.php', '2025-07-02 18:59:54'),
(115, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '9br9ufug6aos4jgivoi3u6oj63', '/CoolAdmin/executive/index.php', '2025-07-02 19:00:15'),
(116, 5, 'Attempting to Send Message', '', 'User 5 attempting to send message to 5 with subject: \'Re: thabks\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '9br9ufug6aos4jgivoi3u6oj63', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-02 19:00:44'),
(117, 5, 'Message Sent Successfully', '', 'User 5 sent message to 5 with subject: \'Re: thabks\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '9br9ufug6aos4jgivoi3u6oj63', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-02 19:00:44'),
(118, 5, 'Message Reply Sent', '', 'Executive 5 replied to message 3, sending to 5.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '9br9ufug6aos4jgivoi3u6oj63', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-02 19:00:44'),
(119, 9, 'Logged in', 'Authentication', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', NULL, NULL, '2025-07-02 19:02:36'),
(120, 9, 'Visited Dashboard', '', 'User entered seller dashboard', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', 'ofouaal6vgt2001la04mjbb8a0', '/CoolAdmin/daily/index.php', '2025-07-02 19:02:36'),
(121, 9, 'Visited Dashboard', '', 'User entered seller dashboard', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', 'ofouaal6vgt2001la04mjbb8a0', '/CoolAdmin/daily/index.php', '2025-07-02 19:03:30'),
(122, 9, 'Logged in', 'Authentication', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', NULL, NULL, '2025-07-02 22:52:03'),
(123, 9, 'Visited Dashboard', '', 'User entered seller dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'b20st883ft0acbqr06oboqmfsr', '/CoolAdmin/daily/index.php', '2025-07-02 22:52:03'),
(124, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'b20st883ft0acbqr06oboqmfsr', '/CoolAdmin/daily/send_message.php', '2025-07-02 22:52:11'),
(125, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'b20st883ft0acbqr06oboqmfsr', '/CoolAdmin/daily/send_message.php', '2025-07-02 22:52:30'),
(126, 9, 'Visited Create Sales Order Page', '', 'User navigated to create sales order form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'b20st883ft0acbqr06oboqmfsr', '/CoolAdmin/daily/create_sales_order.php', '2025-07-02 22:52:32'),
(127, 9, 'Visited My Orders Page', '', 'User viewed their sales orders', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'b20st883ft0acbqr06oboqmfsr', '/CoolAdmin/daily/my_orders.php', '2025-07-02 22:52:35'),
(128, 5, 'Logged in', 'Authentication', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, NULL, '2025-07-02 22:52:48'),
(129, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/executive/index.php', '2025-07-02 22:52:48'),
(130, 9, 'Visited My Orders Page', '', 'User viewed their sales orders', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'b20st883ft0acbqr06oboqmfsr', '/CoolAdmin/daily/my_orders.php', '2025-07-02 22:53:36'),
(131, 9, 'Visited Dashboard', '', 'User entered seller dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'b20st883ft0acbqr06oboqmfsr', '/CoolAdmin/daily/index.php', '2025-07-02 22:53:48'),
(132, 9, 'Visited Dashboard', '', 'User entered seller dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'b20st883ft0acbqr06oboqmfsr', '/CoolAdmin/daily/index.php', '2025-07-02 22:54:45'),
(133, 9, 'Visited My Orders Page', '', 'User viewed their sales orders', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'b20st883ft0acbqr06oboqmfsr', '/CoolAdmin/daily/my_orders.php', '2025-07-02 22:54:48'),
(134, 9, 'Visited Dashboard', '', 'User entered seller dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'b20st883ft0acbqr06oboqmfsr', '/CoolAdmin/daily/index.php', '2025-07-02 22:55:14'),
(135, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/executive/', '2025-07-02 22:56:02'),
(136, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/executive/', '2025-07-02 22:56:10'),
(137, 5, 'Attempting to Send Message', '', 'User 5 attempting to send message to 5 with subject: \'Re: Re: thabks\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-02 22:56:25'),
(138, 5, 'Message Sent Successfully', '', 'User 5 sent message to 5 with subject: \'Re: Re: thabks\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-02 22:56:25'),
(139, 5, 'Message Reply Sent', '', 'Executive 5 replied to message 7, sending to 5.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-02 22:56:25'),
(140, 5, 'Attempting to Send Message', '', 'User 5 attempting to send message to 5 with subject: \'Re: Re: thabks\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-02 22:56:28'),
(141, 5, 'Message Sent Successfully', '', 'User 5 sent message to 5 with subject: \'Re: Re: thabks\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-02 22:56:28'),
(142, 5, 'Message Reply Sent', '', 'Executive 5 replied to message 7, sending to 5.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-02 22:56:28'),
(143, 5, 'Attempting to Send Message', '', 'User 5 attempting to send message to 5 with subject: \'Thanks minee\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-02 22:56:39'),
(144, 5, 'Message Sent Successfully', '', 'User 5 sent message to 5 with subject: \'Thanks minee\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-02 22:56:39'),
(145, 5, 'Message Reply Sent', '', 'Executive 5 replied to message 7, sending to 5.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-02 22:56:39'),
(146, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'b20st883ft0acbqr06oboqmfsr', '/CoolAdmin/daily/send_message.php', '2025-07-02 22:56:44'),
(147, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/executive/', '2025-07-02 22:59:12'),
(148, 7, 'Logged in', 'Authentication', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, NULL, '2025-07-02 22:59:56'),
(149, 7, 'Visited Admin Dashboard', '', 'Executive user  viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/admin/index.php', '2025-07-02 22:59:56'),
(150, 7, 'Visited Admin Dashboard', '', 'Executive user  viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/admin/index.php', '2025-07-02 23:06:06'),
(151, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'b20st883ft0acbqr06oboqmfsr', '/CoolAdmin/daily/send_message.php', '2025-07-02 23:07:13'),
(152, 9, 'Attempting to Send Message', '', 'User 9 attempting to send message to 7 with subject: \'Hello\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'b20st883ft0acbqr06oboqmfsr', '/CoolAdmin/daily/send_message.php', '2025-07-02 23:07:13'),
(153, 9, 'Message Sent Successfully', '', 'User 9 sent message to 7 with subject: \'Hello\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'b20st883ft0acbqr06oboqmfsr', '/CoolAdmin/daily/send_message.php', '2025-07-02 23:07:13'),
(154, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'b20st883ft0acbqr06oboqmfsr', '/CoolAdmin/daily/send_message.php', '2025-07-02 23:07:13'),
(155, 7, 'Visited Admin Dashboard', '', 'Executive user  viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/admin/index.php', '2025-07-02 23:07:19'),
(156, 7, 'Visited Admin Dashboard', '', 'admin user GASHEMA viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/admin/index.php', '2025-07-02 23:07:41'),
(157, 7, 'Visited Admin Dashboard', '', 'admin user GASHEMA viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/admin/index.php', '2025-07-02 23:07:45'),
(158, 7, 'Visited Admin Dashboard', '', 'admin user GASHEMA viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/admin/index.php', '2025-07-02 23:07:57'),
(159, 7, 'Visited Admin Dashboard', '', 'admin user GASHEMA viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/admin/index.php', '2025-07-02 23:08:35'),
(160, 7, 'Visited Admin Dashboard', '', 'admin user GASHEMA viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/admin/index.php', '2025-07-02 23:08:54'),
(161, 7, 'Visited Admin Dashboard', '', 'admin user GASHEMA viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/admin/index.php', '2025-07-02 23:09:38'),
(162, 7, 'Visited Admin Dashboard', '', 'admin user GASHEMA viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/admin/index.php', '2025-07-02 23:14:36'),
(163, 7, 'Message Read', '', 'User 7 marked message 11 as read.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '72pkt0lvjb9v2smk78b2fph01l', '/CoolAdmin/seller/mark_message_read_ajax.php', '2025-07-02 23:14:38'),
(164, 5, 'Logged in', 'Authentication', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, NULL, '2025-07-03 09:17:02'),
(165, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-03 09:17:03'),
(166, 9, 'Logged in', 'Authentication', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', NULL, NULL, '2025-07-03 09:18:51'),
(167, 9, 'Visited Dashboard', '', 'User entered seller dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'qh01loemfi7kh33quipo4ripmq', '/CoolAdmin/daily/index.php', '2025-07-03 09:18:51'),
(168, 9, 'Visited Create Sales Order Page', '', 'User navigated to create sales order form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'qh01loemfi7kh33quipo4ripmq', '/CoolAdmin/daily/create_sales_order.php', '2025-07-03 09:19:15'),
(169, 9, 'Visited Create Sales Order Page', '', 'User navigated to create sales order form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'qh01loemfi7kh33quipo4ripmq', '/CoolAdmin/daily/create_sales_order.php', '2025-07-03 09:21:15'),
(170, 9, 'Visited Dashboard', '', 'User entered seller dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'qh01loemfi7kh33quipo4ripmq', '/CoolAdmin/daily/index.php', '2025-07-03 09:21:16'),
(171, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'qh01loemfi7kh33quipo4ripmq', '/CoolAdmin/daily/send_message.php', '2025-07-03 09:21:27'),
(172, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'qh01loemfi7kh33quipo4ripmq', '/CoolAdmin/daily/send_message.php', '2025-07-03 09:21:52');
INSERT INTO `user_activity_logs` (`id`, `user_id`, `action`, `module`, `details`, `ip_address`, `user_agent`, `session_id`, `page`, `created_at`) VALUES
(173, 9, 'Attempting to Send Message', '', 'User 9 attempting to send message to 5 with subject: \'Confirm Order\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'qh01loemfi7kh33quipo4ripmq', '/CoolAdmin/daily/send_message.php', '2025-07-03 09:21:52'),
(174, 9, 'Message Sent Successfully', '', 'User 9 sent message to 5 with subject: \'Confirm Order\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'qh01loemfi7kh33quipo4ripmq', '/CoolAdmin/daily/send_message.php', '2025-07-03 09:21:52'),
(175, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'qh01loemfi7kh33quipo4ripmq', '/CoolAdmin/daily/send_message.php', '2025-07-03 09:21:52'),
(176, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-03 09:21:59'),
(177, 5, 'Attempting to Send Message', '', 'User 5 attempting to send message to 9 with subject: \'Your order is confrmedr\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-03 09:22:31'),
(178, 5, 'Message Sent Successfully', '', 'User 5 sent message to 9 with subject: \'Your order is confrmedr\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-03 09:22:31'),
(179, 5, 'Message Reply Sent', '', 'Executive 5 replied to message 12, sending to 9.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-03 09:22:31'),
(180, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'qh01loemfi7kh33quipo4ripmq', '/CoolAdmin/daily/send_message.php', '2025-07-03 09:23:01'),
(181, 9, 'Visited Dashboard', '', 'User entered seller dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'qh01loemfi7kh33quipo4ripmq', '/CoolAdmin/daily//', '2025-07-03 09:23:07'),
(182, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-03 09:23:50'),
(183, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-03 09:24:47'),
(184, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-03 09:26:56'),
(185, 9, 'Visited Create Sales Order Page', '', 'User navigated to create sales order form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'qh01loemfi7kh33quipo4ripmq', '/CoolAdmin/daily//create_sales_order.php', '2025-07-03 09:27:10'),
(186, 5, 'Logged in', 'Authentication', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, NULL, '2025-07-03 12:26:57'),
(187, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-03 12:26:57'),
(188, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-03 12:27:10'),
(189, 5, 'Attempting to Send Message', '', 'User 5 attempting to send message to 9 with subject: \'Re: Confirm Order\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-03 12:27:33'),
(190, 5, 'Message Sent Successfully', '', 'User 5 sent message to 9 with subject: \'Re: Confirm Order\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-03 12:27:33'),
(191, 5, 'Message Reply Sent', '', 'Executive 5 replied to message 12, sending to 9.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-03 12:27:33'),
(192, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-03 12:28:30'),
(193, 5, 'Message Read', '', 'User 5 marked message 12 as read.', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/seller/mark_message_read_ajax.php', '2025-07-03 12:28:40'),
(194, 5, 'Attempting to Send Message', '', 'User 5 attempting to send message to 9 with subject: \'Re: Confirm Order\'', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-03 12:30:53'),
(195, 5, 'Message Sent Successfully', '', 'User 5 sent message to 9 with subject: \'Re: Confirm Order\'', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-03 12:30:53'),
(196, 5, 'Message Reply Sent', '', 'Executive 5 replied to message 12, sending to 9.', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-03 12:30:53'),
(197, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-03 12:38:54'),
(198, 5, 'Attempting to Send Message', '', 'User 5 attempting to send message to 9 with subject: \'Re: Confirm Order\'', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-03 12:39:15'),
(199, 5, 'Message Sent Successfully', '', 'User 5 sent message to 9 with subject: \'Re: Confirm Order\'', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-03 12:39:15'),
(200, 5, 'Message Reply Sent', '', 'Executive 5 replied to message 12, sending to 9.', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/seller/send_reply_ajax.php', '2025-07-03 12:39:15'),
(201, 9, 'Visited Create Sales Order Page', '', 'User navigated to create sales order form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'qh01loemfi7kh33quipo4ripmq', '/CoolAdmin/daily//create_sales_order.php', '2025-07-03 12:39:30'),
(202, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'qh01loemfi7kh33quipo4ripmq', '/CoolAdmin/daily//send_message.php', '2025-07-03 12:39:32'),
(203, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'qh01loemfi7kh33quipo4ripmq', '/CoolAdmin/daily//send_message.php', '2025-07-03 12:40:03'),
(204, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-03 14:06:30'),
(205, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-03 14:07:19'),
(206, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-03 14:08:22'),
(207, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-03 14:08:24'),
(208, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-03 14:08:30'),
(209, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-03 14:08:56'),
(210, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-03 14:09:20'),
(211, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-03 14:09:22'),
(212, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-03 14:09:27'),
(213, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-03 14:10:01'),
(214, 5, 'Attempting to Send Message', '', 'User 5 attempting to send message to 9 with subject: \'yaaaaaaaah\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-03 14:10:01'),
(215, 5, 'Message Sent Successfully', '', 'User 5 sent message to 9 with subject: \'yaaaaaaaah\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-03 14:10:01'),
(216, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-03 14:10:01'),
(217, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-03 14:10:09'),
(218, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'qh01loemfi7kh33quipo4ripmq', '/CoolAdmin/daily//send_message.php', '2025-07-03 14:10:22'),
(219, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-03 14:12:01'),
(220, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'qh01loemfi7kh33quipo4ripmq', '/CoolAdmin/daily//send_message.php', '2025-07-03 14:12:08'),
(221, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'qh01loemfi7kh33quipo4ripmq', '/CoolAdmin/daily//send_message.php', '2025-07-03 14:14:05'),
(222, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'qh01loemfi7kh33quipo4ripmq', '/CoolAdmin/daily/send_message.php', '2025-07-03 14:14:10'),
(223, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'qh01loemfi7kh33quipo4ripmq', '/CoolAdmin/daily/send_message.php', '2025-07-03 14:15:07'),
(224, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-03 14:17:37'),
(225, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-03 14:17:38'),
(226, 5, '5', '', 'OTP verified', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/verify_otp.php', '2025-07-04 12:03:03'),
(227, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 12:03:04'),
(228, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 12:04:26'),
(229, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 12:05:15'),
(230, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 12:07:48'),
(231, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 12:12:35'),
(232, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 12:14:11'),
(233, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 12:17:35'),
(234, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 12:18:18'),
(235, 9, '9', '', 'OTP verified', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/verify_otp.php', '2025-07-04 12:20:53'),
(236, 9, 'Visited Dashboard', '', 'User entered seller dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/index.php', '2025-07-04 12:20:53'),
(237, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 12:21:11'),
(238, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 12:22:01'),
(239, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 12:22:29'),
(240, 9, 'Attempting to Send Message', '', 'User 9 attempting to send message to 5 with subject: \'To day\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 12:22:29'),
(241, 9, 'Message Sent Successfully', '', 'User 9 sent message to 5 with subject: \'To day\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 12:22:29'),
(242, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 12:22:29'),
(243, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 12:22:47'),
(244, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 12:23:23'),
(245, 5, 'Visited Dashboard', '', 'User entered executive dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/form.php', '2025-07-04 12:29:45'),
(246, 5, 'Visited Dashboard', '', 'User entered executive dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/form.php', '2025-07-04 12:32:41'),
(247, 7, '7', '', 'OTP verified', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/verify_otp.php', '2025-07-04 12:47:54'),
(248, 7, 'Visited Admin Dashboard', '', 'admin user GASHEMA viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/admin/index.php', '2025-07-04 12:47:54'),
(249, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 12:48:28'),
(250, 9, 'Attempting to Send Message', '', 'User 9 attempting to send message to 7 with subject: \'Hello mine\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 12:48:28'),
(251, 9, 'Message Sent Successfully', '', 'User 9 sent message to 7 with subject: \'Hello mine\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 12:48:28'),
(252, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 12:48:28'),
(253, 7, 'Visited Admin Dashboard', '', 'admin user GASHEMA viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/admin/index.php', '2025-07-04 12:48:41'),
(254, 7, 'Visited Admin Dashboard', '', 'admin user GASHEMA viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/admin/index.php', '2025-07-04 12:51:15'),
(255, 7, 'Visited Admin Dashboard', '', 'admin user GASHEMA viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/admin/index.php', '2025-07-04 12:54:08'),
(256, 7, 'Visited Dashboard', '', 'User entered executive dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/form.php', '2025-07-04 13:00:04'),
(257, 7, 'Visited Admin Dashboard', '', 'admin user GASHEMA viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/admin/index.php', '2025-07-04 13:06:08'),
(258, 7, 'Visited Admin Dashboard', '', 'admin user GASHEMA viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/admin/index.php', '2025-07-04 13:07:02'),
(259, 7, 'Visited Admin Dashboard', '', 'admin user GASHEMA viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/admin/index.php', '2025-07-04 13:07:13'),
(260, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:07:56'),
(261, 7, 'Visited Admin Dashboard', '', 'admin user GASHEMA viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/admin/index.php', '2025-07-04 13:08:26'),
(262, 5, '5', '', 'OTP verified', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/verify_otp.php', '2025-07-04 13:08:58'),
(263, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 13:08:58'),
(264, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:09:31'),
(265, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:15:22'),
(266, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:15:51'),
(267, 5, 'Attempting to Send Message', '', 'User 5 attempting to send message to 9 with subject: \'Hello munezero\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:15:51'),
(268, 5, 'Message Sent Successfully', '', 'User 5 sent message to 9 with subject: \'Hello munezero\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:15:51'),
(269, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:15:51'),
(270, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:15:59'),
(271, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:28:29'),
(272, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:34:26'),
(273, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:35:21'),
(274, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:35:55'),
(275, 9, 'Attempting to Send Message', '', 'User 9 attempting to send message to 5 with subject: \'Re: Hello munezero\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:35:55'),
(276, 9, 'Message Sent Successfully', '', 'User 9 sent message to 5 with subject: \'Re: Hello munezero\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:35:55'),
(277, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:35:55'),
(278, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:36:33'),
(279, 5, 'Attempting to Send Message', '', 'User 5 attempting to send message to 9 with subject: \'Re: Re: Hello munezero\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:36:33'),
(280, 5, 'Message Sent Successfully', '', 'User 5 sent message to 9 with subject: \'Re: Re: Hello munezero\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:36:33'),
(281, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:36:33'),
(282, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:37:25'),
(283, 9, 'Attempting to Send Message', '', 'User 9 attempting to send message to 5 with subject: \'To ask what is the task\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:37:25'),
(284, 9, 'Message Sent Successfully', '', 'User 9 sent message to 5 with subject: \'To ask what is the task\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:37:25'),
(285, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:37:25'),
(286, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:41:13'),
(287, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:41:14'),
(288, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:41:15'),
(289, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:41:15'),
(290, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:41:26'),
(291, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:42:19'),
(292, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:42:50'),
(293, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:43:43'),
(294, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:45:45'),
(295, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:46:16'),
(296, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:48:47'),
(297, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:49:14'),
(298, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:51:51'),
(299, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:51:58'),
(300, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 13:52:29'),
(301, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/', '2025-07-04 13:59:56'),
(302, 5, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/daily/send_message.php', '2025-07-04 14:00:15'),
(303, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/', '2025-07-04 14:00:19'),
(304, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/', '2025-07-04 14:01:15'),
(305, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/', '2025-07-04 14:01:25'),
(306, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/', '2025-07-04 14:01:56'),
(307, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 14:12:24'),
(308, 9, 'Visited Send Message Page', '', 'User navigated to the send message form', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'nab31pff2tlallpo1b1hcum9ng', '/CoolAdmin/daily/send_message.php', '2025-07-04 14:47:56'),
(309, 5, '5', '', 'OTP verified', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/verify_otp.php', '2025-07-04 14:57:56'),
(310, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 14:57:56'),
(311, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 16:00:25'),
(312, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 16:08:48'),
(313, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 16:10:08'),
(314, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 16:15:45'),
(315, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 16:16:53'),
(316, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 16:26:23'),
(317, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 16:30:40'),
(318, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 16:33:07'),
(319, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 16:33:35'),
(320, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 16:36:53'),
(321, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 16:36:57'),
(322, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 16:37:18'),
(323, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 16:37:20'),
(324, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 16:37:34'),
(325, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 16:39:50'),
(326, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 16:57:53'),
(327, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 16:57:55'),
(328, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 16:59:23'),
(329, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 17:01:26'),
(330, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 17:01:28'),
(331, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 17:31:14'),
(332, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 17:36:15'),
(333, 5, 'Visited Admin Dashboard', '', 'Executive user MWIMULE Bienvenu viewed dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '1qnmb6sevteokl3qtrbmn0g3bn', '/CoolAdmin/executive/index.php', '2025-07-04 17:39:23');

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_time` datetime DEFAULT current_timestamp(),
  `logout_time` datetime DEFAULT NULL,
  `ip_address` varchar(100) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `delivery_notes`
--
ALTER TABLE `delivery_notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `delivery_note_number` (`delivery_note_number`),
  ADD KEY `sales_order_id` (`sales_order_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `delivery_note_items`
--
ALTER TABLE `delivery_note_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `delivery_note_id` (`delivery_note_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `document_downloads`
--
ALTER TABLE `document_downloads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `downloaded_by` (`downloaded_by`);

--
-- Indexes for table `goods_received_notes`
--
ALTER TABLE `goods_received_notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `grn_number` (`grn_number`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `received_by` (`received_by`);

--
-- Indexes for table `goods_received_note_items`
--
ALTER TABLE `goods_received_note_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grn_id` (`grn_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `parent_message_id` (`parent_message_id`);

--
-- Indexes for table `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`);

--
-- Indexes for table `proforma_invoices`
--
ALTER TABLE `proforma_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `proforma_items`
--
ALTER TABLE `proforma_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `po_number` (`po_number`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `stock_logs`
--
ALTER TABLE `stock_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `moved_by` (`moved_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `delivery_notes`
--
ALTER TABLE `delivery_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `delivery_note_items`
--
ALTER TABLE `delivery_note_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `document_downloads`
--
ALTER TABLE `document_downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `goods_received_notes`
--
ALTER TABLE `goods_received_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `goods_received_note_items`
--
ALTER TABLE `goods_received_note_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `otp_codes`
--
ALTER TABLE `otp_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `proforma_invoices`
--
ALTER TABLE `proforma_invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `proforma_items`
--
ALTER TABLE `proforma_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sales_orders`
--
ALTER TABLE `sales_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `stock_logs`
--
ALTER TABLE `stock_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=334;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `delivery_notes`
--
ALTER TABLE `delivery_notes`
  ADD CONSTRAINT `delivery_notes_ibfk_1` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `delivery_notes_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `delivery_note_items`
--
ALTER TABLE `delivery_note_items`
  ADD CONSTRAINT `delivery_note_items_ibfk_1` FOREIGN KEY (`delivery_note_id`) REFERENCES `delivery_notes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `delivery_note_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `document_downloads`
--
ALTER TABLE `document_downloads`
  ADD CONSTRAINT `document_downloads_ibfk_1` FOREIGN KEY (`downloaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `goods_received_notes`
--
ALTER TABLE `goods_received_notes`
  ADD CONSTRAINT `goods_received_notes_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `goods_received_notes_ibfk_2` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `goods_received_note_items`
--
ALTER TABLE `goods_received_note_items`
  ADD CONSTRAINT `goods_received_note_items_ibfk_1` FOREIGN KEY (`grn_id`) REFERENCES `goods_received_notes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `goods_received_note_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `invoice_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoice_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`parent_message_id`) REFERENCES `messages` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD CONSTRAINT `otp_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `proforma_invoices`
--
ALTER TABLE `proforma_invoices`
  ADD CONSTRAINT `proforma_invoices_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD CONSTRAINT `sales_orders_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD CONSTRAINT `sales_order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_movements_ibfk_2` FOREIGN KEY (`moved_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  ADD CONSTRAINT `user_activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
