-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 02, 2025 at 05:58 PM
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
(1, 'DN-1751212277', 'ITBienvenu', 'Nyamirambo', 'KIGALI', '', '', 1, '2025-07-01', 'MENAMOTORS', 'Naone', '2025-06-29 16:18:50', 'No coments', 5, '2025-06-29 15:51:17');

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
(6, 'HP EliteBook G3 i5 6th Gen 8GB RAM 256GB SSD', 'Computer', '1234', 'HP EliteBook G3 i5 6th Gen 8GB RAM 256GB SSD,\r\nTouch screen', 280.00, 350.00, 10, 5, 'UMUCYO', '../uploads/product_6864fd04370fb.jpeg', 5, '2025-07-02 09:33:56'),
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
(1, 'SO-1751207345', 'KINAZI', '12345678', 'Nyamirambo', 'Kigali', '000', 'Rwanda', 900.00, 'pending', '2025-06-29', '2025-06-25', 5, '2025-06-29 14:29:05'),
(2, 'SO-1751272121', 'ITBienvenu', '12346', 'Nyamirambo', 'Kigali', '000', 'Rwanda', 1400.00, 'pending', '2025-06-30', '2025-07-02', 5, '2025-06-30 08:28:41');

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
(15, 1, 2, 1, 140.00, 140.00),
(16, 1, 3, 2, 380.00, 760.00),
(22, 2, 2, 10, 140.00, 1400.00);

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
  `otp` int(100) NOT NULL,
  `time` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `names`, `email`, `phone`, `password`, `role`, `otp`, `time`) VALUES
(5, 'MWIMULE Bienvenu', 'bienvenugashema@gmail.com', 781300739, '$2y$10$7CdsHvD6/SGQU2ZWzbkykeT16VshyhFi04IQYIKJkm0XliXHe31ba', 'executive', 0, '2025-06-28 22:10:33.017584'),
(7, 'GASHEMA', 'mwimulegashema@gmail.com', 736701735, '$2y$10$qSCteFqChnAR6PNJ5RSHSuPxADCnKWvQK4nXfXJs1vfWyUM1ZIkMy', 'admin', 0, '2025-07-01 22:34:37.495720'),
(8, 'Protais Hashimwumukiza', 'pascal123@gmail.com', 123, '$2y$10$t2IutC4SJvfN0Z7Kii3cruOtBSWU2vujDjRitmUqVt5mOZlAeG8Iy', 'salesperson', 0, '2025-07-02 08:44:58.915522'),
(9, 'Kalisa Munezero', 'munezero123@gmail.com', 123456789, '$2y$10$YpOAqA27qKoG2zlavnl2vOVX398H.axzk7BkpRGvtSG9oNQs8gKJi', 'daily', 0, '2025-07-02 15:33:12.469857');

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
(3, 5, 'Visited Dashboard', '', 'User entered executive dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 's4nt5ftntu9imcjgeekdvc1nh2', '/CoolAdmin/executive/form.php', '2025-07-02 15:55:29');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
