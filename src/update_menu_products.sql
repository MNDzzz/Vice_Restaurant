SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

UPDATE `products` SET 
    `name` = 'Cachimba Premium', 
    `description` = 'Shisha de lujo con una manguera, sabores premium a elección y base iluminada.', 
    `price` = 25.00, 
    `image` = 'shisha1.png' 
WHERE `id` = 9;


UPDATE `products` SET 
    `name` = 'Cachimba VIP Duo', 
    `description` = 'Shisha exclusiva de dos mangueras, cuerpo dorado y tabaco reserva especial.', 
    `price` = 40.00, 
    `image` = 'shisha2.png' 
WHERE `id` = 10;


UPDATE `products` SET 
    `name` = 'Champagne Vice Luminous', 
    `description` = 'Botella francesa premium con etiqueta luminosa, servida con bengalas y cubitera led.', 
    `price` = 120.00, 
    `image` = 'champagne.png' 
WHERE `id` = 11;

UPDATE `products` SET `image` = 'nachos.png' WHERE `id` = 1;
UPDATE `products` SET `image` = 'wings.png' WHERE `id` = 2;

UPDATE `products` SET `image` = 'mule.png' WHERE `id` = 3;
UPDATE `products` SET `image` = 'sunset.png' WHERE `id` = 4;

UPDATE `products` SET `image` = 'burger.png' WHERE `id` = 6;
UPDATE `products` SET `image` = 'ribs.png' WHERE `id` = 7;
UPDATE `products` SET `image` = 'tacos.png' WHERE `id` = 8;


UPDATE `categories` SET `image` = 'entrantes-neon.png' WHERE `id` = 1;
UPDATE `categories` SET `image` = 'cocktails-neon.png' WHERE `id` = 2;
UPDATE `categories` SET `image` = 'principales-neon.png' WHERE `id` = 3;
UPDATE `categories` SET `image` = 'extras-vip-neon.png' WHERE `id` = 4;

COMMIT;
