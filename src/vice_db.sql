SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Base de dades: `vice_db`
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
-- Estructura de la taula `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `delivery_address` varchar(255) DEFAULT NULL,
  `delivery_phone` varchar(20) DEFAULT NULL,
  `delivery_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Bolcament de dades per a la taula `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `status`, `address`, `phone`, `created_at`, `delivery_address`, `delivery_phone`, `delivery_notes`) VALUES
(1, 2, 22.50, 'completed', NULL, NULL, '2025-12-04 15:18:40', NULL, NULL, NULL),
(3, 3, 10.00, 'completed', NULL, NULL, '2025-12-05 16:26:27', NULL, NULL, NULL),
(5, 3, 28.00, 'completed', NULL, NULL, '2025-12-05 16:29:26', NULL, NULL, NULL),
(6, 3, 12.50, 'completed', NULL, NULL, '2025-12-05 17:41:26', 'Ocean Drive 101, Apt 5B', '+34 611223344', 'Llamar al llegar'),
(8, 5, 12.50, 'completed', NULL, NULL, '2025-12-09 17:46:29', 'Calle Test 2', '666777888', ''),
(9, 5, 22.00, 'completed', NULL, NULL, '2025-12-09 18:17:10', 'Order 2Order 2 EDITED', '222', ''),
(10, 3, 12.50, 'completed', NULL, NULL, '2025-12-10 16:32:45', 'Test LS', '999', '');

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
(1, 1, 1, 1, 12.50),
(2, 1, 2, 1, 10.00),
(5, 3, 2, 1, 10.00),
(7, 5, 2, 1, 10.00),
(8, 5, 3, 2, 9.00),
(9, 6, 1, 1, 12.50),
(11, 8, 1, 1, 12.50),
(12, 9, 1, 1, 12.50),
(13, 9, 4, 1, 9.50),
(14, 10, 1, 1, 12.50);

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
(1, 1, 'Nachos Vice', 'Nachos con queso cheddar, jalapeños y guacamole casero.', 12.50, 'img/producto1.jpg'),
(2, 1, 'Alitas Neon', 'Alitas de pollo con salsa barbacoa picante.', 10.00, 'img/producto2.jpg'),
(3, 2, 'Miami Mule', 'Vodka, lima, cerveza de jengibre y menta.', 9.00, 'img/producto3.jpg'),
(4, 2, 'Sunset Vice', 'Ron, piña, coco y granadina.', 9.50, 'img/producto4.jpg'),
(6, 3, 'Vice Burger', 'Hamburguesa premium con queso cheddar, bacon crujiente, cebolla caramelizada y salsa especial Vice.', 16.50, 'img/burger-vice.jpg'),
(7, 3, 'Miami Ribs', 'Costillas BBQ glaseadas con salsa de miel y especias, acompañadas de patatas rústicas.', 22.00, 'img/ribs-miami.jpg'),
(8, 3, 'Neon Tacos', 'Tres tacos de carne asada con guacamole, pico de gallo y queso fundido.', 14.50, 'img/tacos-neon.jpg'),
(9, 4, 'Patatas Vice', 'Patatas fritas con salsa especial Vice y queso fundido.', 6.00, 'img/patatas-vice.jpg'),
(10, 4, 'Onion Rings', 'Aros de cebolla crujientes con salsa de mostaza y miel.', 5.50, 'img/onion-rings.jpg'),
(11, 4, 'Miami Fries', 'Patatas deluxe con bacon, jalapeños y salsa ranch.', 7.50, 'img/miami-fries.jpg'),
(12, 3, 'Ejemplo1', '', 10.00, 'img/default-product.jpg'),
(13, 3, 'Ejemplo2', '', 10.00, 'img/default-product.jpg'),
(14, 3, 'Ejemplo3', '', 10.00, 'img/default-product.jpg'),
(15, 3, 'ejemplo4', '', 10.00, 'img/default-product.jpg');

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
(4, 'David Rojas', 'melapela@gmail.com', '$2y$10$JMUUtUF2XBzU1cllBej8C.jKAcJE6YVsiyT863M514yGQc7T7KdYu', 'user', 1, '2025-12-09 16:19:17'),
(5, 'Test User 2', 'test2@vice.com', '$2y$10$0fxg22HiAuRwkxpj2qUfr.GmzzV9LU1EcnlvAN/jqxq7RIr0zjbKC', 'user', 1, '2025-12-09 17:45:22');

--
-- Índexs per a les taules bolcades
--

--
-- Índexs per a la taula `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT per la taula `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT per la taula `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT per la taula `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
