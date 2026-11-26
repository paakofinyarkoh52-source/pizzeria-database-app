-- Pizzeria database schema
-- Structure for all tables, plus the menu (products).
-- No user, order or order item data is included.
--
-- Import: phpMyAdmin > Import, or `mysql -u <user> -p < database.sql`

CREATE DATABASE IF NOT EXISTS `pizzeria` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `pizzeria`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `status` enum('ready','at_table','paid') NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `table_number` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `item_total` decimal(10,2) NOT NULL,
  `topping_for_pizza` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `size` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `type` enum('pizza','topping') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `size`, `price`, `type`) VALUES
(1, 'cheese pizza', 'Medium', 50.50, 'pizza'),
(2, 'veggie pizza', 'Medium', 50.50, 'pizza'),
(3, 'Pepperoni', 'Large', 100.00, 'pizza'),
(5, 'Hawaiian Pizza', 'Large', 100.00, 'pizza'),
(6, 'Neapolitan pizza', 'Large', 100.00, 'pizza'),
(7, 'Sicilian pizza', 'Medium', 70.50, 'pizza'),
(8, 'Roman pizza', 'Medium', 65.50, 'pizza'),
(9, 'New York–style pizza', 'Large', 80.00, 'pizza'),
(10, 'Chicago deep-dish pizza', 'Large', 100.00, 'pizza'),
(11, 'Detroit-style pizza', 'Large', 70.50, 'pizza'),
(12, 'California-style pizza', 'Large', 100.00, 'pizza'),
(13, 'St. Louis-style pizza', 'Large', 85.00, 'pizza'),
(14, 'BBQ chicken pizza', 'Large', 100.00, 'pizza'),
(15, 'Four cheese pizza', 'Large', 100.00, 'pizza'),
(16, 'White pizza', 'Large', 100.00, 'pizza'),
(18, 'Pizza al taglio', 'Large', 100.00, 'pizza'),
(19, 'Turkish pide', 'Medium', 80.00, 'pizza'),
(20, 'Lahmacun', 'Medium', 80.00, 'pizza'),
(21, 'Extra Cheese', 'Medium', 10.00, 'topping'),
(22, 'Mushrooms', NULL, 8.00, 'topping'),
(23, 'Olives', NULL, 7.00, 'topping');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(256) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(256) NOT NULL,
  `last_login` datetime(6) NOT NULL,
  `logged_in` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `topping_for_pizza` (`topping_for_pizza`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `last_login` (`last_login`),
  ADD KEY `logged_in` (`logged_in`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`topping_for_pizza`) REFERENCES `order_items` (`order_item_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
