CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(64) NOT NULL,
  `surname` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `password` varchar(255) NOT NULL,
  `state` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `roles` varchar(255) NOT NULL DEFAULT 'authenticated',
  `registerDate` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP
) COLLATE 'utf8mb3_general_ci';

ALTER TABLE `users`
ADD UNIQUE `email` (`email`);

CREATE TABLE `brands` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL,
  `description` text NULL
) COLLATE 'utf8mb3_general_ci';

ALTER TABLE `brands`
ADD `state` tinyint unsigned NOT NULL DEFAULT '1';

CREATE TABLE `eshops` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(64) NOT NULL,
  `domain` varchar(64) NOT NULL
) COLLATE 'utf8mb3_general_ci';

ALTER TABLE `users`
ADD `eshop_id` int unsigned NOT NULL AFTER `id`;

ALTER TABLE `users`
ADD FOREIGN KEY (`eshop_id`) REFERENCES `eshops` (`id`);

ALTER TABLE `brands`
ADD `eshop_id` int unsigned NOT NULL AFTER `id`;

ALTER TABLE `brands`
ADD FOREIGN KEY (`eshop_id`) REFERENCES `eshops` (`id`);

CREATE TABLE `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL,
  `description` text NULL,
  `createdAt` datetime NOT NULL
) COLLATE 'utf8mb3_general_ci';

ALTER TABLE `products`
ADD `brand_id` int unsigned NOT NULL AFTER `id`;

ALTER TABLE `products`
ADD FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`);