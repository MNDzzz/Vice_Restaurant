

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

DELETE FROM `order_items` 
WHERE `product_id` IN (SELECT `id` FROM `products` WHERE `name` LIKE 'Ejemplo%' OR `name` LIKE 'ejemplo%');

DELETE FROM `products` WHERE `name` LIKE 'Ejemplo%' OR `name` LIKE 'ejemplo%';

COMMIT;
