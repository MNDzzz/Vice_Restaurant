-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: sql310.byetcluster.com
-- Temps de generació: 07-01-2026 a les 05:29:59
-- Versió del servidor: 11.4.9-MariaDB
-- Versió de PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de dades: `if0_40839903_vice_db`
--

-- --------------------------------------------------------

--
-- Estructura de la taula `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Bolcament de dades per a la taula `categories`
--

INSERT INTO `categories` (`id`, `name`, `image`) VALUES
(1, 'Entrantes', 'img/catEntrantes.jpg'),
(2, 'Cocktails', 'img/catCocktails.jpg'),
(3, 'Principales', 'views/images/banners/principales-banner.jpg'),
(4, 'Extras', 'views/images/banners/extras-banner.jpg');

-- --------------------------------------------------------

--
-- Estructura de la taula `config`
--

CREATE TABLE `config` (
  `key` varchar(50) NOT NULL,
  `value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Bolcament de dades per a la taula `config`
--

INSERT INTO `config` (`key`, `value`) VALUES
('currency_code', 'USD'),
('currency_rate', '1.1707'),
('currency_symbol', '€');

-- --------------------------------------------------------

--
-- Estructura de la taula `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Bolcament de dades per a la taula `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `action`, `details`, `created_at`) VALUES
(1, NULL, 'TEST_DEBUG', 'Prueba de inserción directa desde API', '2026-01-06 18:45:22'),
(2, NULL, 'TEST_DEBUG', 'Prueba de inserción directa desde API', '2026-01-06 18:47:25'),
(3, NULL, 'TEST_DEBUG', 'Prueba de inserción directa desde API', '2026-01-06 18:47:57'),
(4, NULL, 'TEST_DEBUG', 'Prueba de inserción directa desde API', '2026-01-06 18:48:01'),
(5, NULL, 'TEST_DEBUG', 'Prueba de inserción directa desde API', '2026-01-06 18:50:48'),
(6, 3, 'update_product', 'Producto actualizado ID: 13', '2026-01-06 19:04:56'),
(7, 3, 'update_product', 'Producto actualizado ID: 13', '2026-01-06 19:16:37'),
(8, 3, 'update_config', 'Configuración de moneda actualizada', '2026-01-06 21:32:30'),
(9, 3, 'update_config', 'Configuración de moneda actualizada', '2026-01-06 21:33:11'),
(10, 3, 'update_config', 'Configuración de moneda actualizada', '2026-01-06 21:48:15');

-- --------------------------------------------------------

--
-- Estructura de la taula `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `delivery_name` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `delivery_address` varchar(255) DEFAULT NULL,
  `delivery_city` varchar(100) DEFAULT NULL,
  `delivery_postal_code` varchar(20) DEFAULT NULL,
  `delivery_phone` varchar(20) DEFAULT NULL,
  `delivery_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Bolcament de dades per a la taula `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `status`, `delivery_name`, `address`, `phone`, `created_at`, `delivery_address`, `delivery_city`, `delivery_postal_code`, `delivery_phone`, `delivery_notes`) VALUES
(1, 2, '22.50', 'completed', NULL, NULL, NULL, '2025-12-04 15:18:40', NULL, NULL, NULL, NULL, NULL),
(3, 3, '10.00', 'completed', NULL, NULL, NULL, '2025-12-05 16:26:27', NULL, NULL, NULL, NULL, NULL),
(5, 3, '28.00', 'completed', NULL, NULL, NULL, '2025-12-05 16:29:26', NULL, NULL, NULL, NULL, NULL),
(6, 3, '12.50', 'completed', NULL, NULL, NULL, '2025-12-05 17:41:26', 'Ocean Drive 101, Apt 5B', NULL, NULL, '+34 611223344', 'Llamar al llegar'),
(8, 5, '12.50', 'completed', NULL, NULL, NULL, '2025-12-09 17:46:29', 'Calle Test 2', NULL, NULL, '666777888', ''),
(11, 3, '37.50', 'completed', '', NULL, NULL, '2026-01-06 14:39:12', 'a', '', '', 'a', ''),
(14, 3, '31.75', 'pending', '', NULL, NULL, '2026-01-06 20:20:32', 'a', '', '', 'a', 'a'),
(15, 3, '21.25', 'pending', '', NULL, NULL, '2026-01-06 20:36:02', 'a', '', '', 'a', 'a'),
(16, 3, '18.50', 'pending', '', NULL, NULL, '2026-01-06 20:52:33', 'a', '', '', 'a', 'a'),
(17, 3, '19.50', 'pending', '', NULL, NULL, '2026-01-06 20:53:04', 'a', '', '', 'a', 'a');

-- --------------------------------------------------------

--
-- Estructura de la taula `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Bolcament de dades per a la taula `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, '12.50'),
(2, 1, 2, 1, '10.00'),
(5, 3, 2, 1, '10.00'),
(7, 5, 2, 1, '10.00'),
(8, 5, 3, 2, '9.00'),
(9, 6, 1, 1, '12.50'),
(11, 8, 1, 1, '12.50'),
(15, 11, 1, 1, '12.50'),
(16, 11, 2, 1, '10.00'),
(17, 11, 4, 1, '9.50'),
(18, 11, 10, 1, '5.50'),
(25, 14, 1, 1, '12.50'),
(26, 14, 3, 1, '9.00'),
(27, 14, 2, 1, '10.00'),
(28, 14, 4, 1, '9.50'),
(29, 15, 6, 1, '16.50'),
(30, 15, 4, 1, '9.50'),
(31, 16, 3, 1, '9.00'),
(32, 16, 4, 1, '9.50'),
(33, 17, 6, 1, '16.50'),
(34, 17, 4, 1, '9.50');

-- --------------------------------------------------------

--
-- Estructura de la taula `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Bolcament de dades per a la taula `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `image`) VALUES
(1, 1, 'Nachos Vice', 'Nachos con queso cheddar, jalapeños y guacamole casero.', '12.50', 'img/producto1.jpg'),
(2, 1, 'Alitas Neon', 'Alitas de pollo con salsa barbacoa picante.', '10.00', 'img/producto2.jpg'),
(3, 2, 'Miami Mule', 'Vodka, lima, cerveza de jengibre y menta.', '9.00', 'img/producto3.jpg'),
(4, 2, 'Sunset Vice', 'Ron, piña, coco y granadina.', '9.50', 'img/producto4.jpg'),
(6, 3, 'Vice Burger', 'Hamburguesa premium con queso cheddar, bacon crujiente, cebolla caramelizada y salsa especial Vice.', '16.50', 'img/burger-vice.jpg'),
(7, 3, 'Miami Ribs', 'Costillas BBQ glaseadas con salsa de miel y especias, acompañadas de patatas rústicas.', '22.00', 'img/ribs-miami.jpg'),
(8, 3, 'Neon Tacos', 'Tres tacos de carne asada con guacamole, pico de gallo y queso fundido.', '14.50', 'img/tacos-neon.jpg'),
(9, 4, 'Patatas Vice', 'Patatas fritas con salsa especial Vice y queso fundido.', '6.00', 'img/patatas-vice.jpg'),
(10, 4, 'Onion Rings', 'Aros de cebolla crujientes con salsa de mostaza y miel.', '5.50', 'img/onion-rings.jpg'),
(11, 4, 'Miami Fries', 'Patatas deluxe con bacon, jalapeños y salsa ranch.', '7.50', 'img/miami-fries.jpg'),
(12, 3, 'Ejemplo1', '', '10.00', 'img/default-product.jpg'),
(13, 3, 'logggg 2', 'log funciona pls', '10.00', 'img/default-product.jpg');

-- --------------------------------------------------------

--
-- Estructura de la taula `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','admin','user') DEFAULT 'user',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Bolcament de dades per a la taula `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES
(1, 'Admin Vice', 'admin@vice.com', '$2y$10$82t2Xtq0NwUVdZJPriILpeNPY9yYW.2Ir1oyiC/7894nlor50IjEy', 'superadmin', 1, '2025-12-04 15:07:33'),
(2, 'Álvaro', 'alvaromendezfp24@ibf.cat', '$2y$10$8z5z3ZHA2jfpUb2yJzOSueXiWB.KcAg9byWp6FlBykjO3tbJCT.5e', 'admin', 1, '2025-12-04 15:18:17'),
(3, 'Super Admin', 'superadmin@vice.com', '$2y$10$WYZ4wA0/qYgWVkvuySRu8.OHVt9nRNJ6J7eka37nVlNcf7QM3CpVS', 'superadmin', 1, '2025-12-04 16:48:48'),
(5, 'Testing User', 'test2@vice.com', '$2y$10$0fxg22HiAuRwkxpj2qUfr.GmzzV9LU1EcnlvAN/jqxq7RIr0zjbKC', 'user', 1, '2025-12-09 17:45:22');

--
-- Índexs per a les taules bolcades
--

--
-- Índexs per a la taula `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Índexs per a la taula `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`key`);

--
-- Índexs per a la taula `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índexs per a la taula `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índexs per a la taula `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Índexs per a la taula `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Índexs per a la taula `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT per les taules bolcades
--

--
-- AUTO_INCREMENT per la taula `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la taula `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT per la taula `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT per la taula `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT per la taula `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT per la taula `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restriccions per a les taules bolcades
--

--
-- Restriccions per a la taula `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Restriccions per a la taula `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Restriccions per a la taula `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
