DROP DATABASE IF EXISTS `COUNTRIES`;
CREATE DATABASE IF NOT EXISTS `COUNTRIES`;
USE `COUNTRIES`;

DROP TABLE IF EXISTS `countries`;
CREATE TABLE IF NOT EXISTS `countries` (
	`country_id` bigint UNSIGNED PRIMARY KEY NOT NULL
)	ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `countries`
(`country_id`)
	VALUES
(1);

DROP TABLE IF EXISTS `companiesAndServices`;
CREATE TABLE IF NOT EXISTS `companiesAndServices` (
	`companiesandservices_id` bigint UNSIGNED PRIMARY KEY NOT NULL,
	`country_id` bigint UNSIGNED NOT NULL,
	`name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
	`description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
	`logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
	`notes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
	`inBSD` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
	CONSTRAINT fk_country_companiesandservices FOREIGN KEY (`country_id`) REFERENCES countries(`country_id`)
)	ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `companiesAndServices`
(`companiesandservices_id`, `country_id`, `name`, `description`, `logo`, `notes`, `inBSD`)
	VALUES
(1, 1, 'Company1', '', '', '', 'True');

DROP TABLE IF EXISTS `alternatives`;
CREATE TABLE IF NOT EXISTS `alternatives` (
	`alternatives_id` bigint UNSIGNED PRIMARY KEY NOT NULL,
	`companiesandservices_id` bigint UNSIGNED NOT NULL,
	`name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
	`description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
	`link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
	`logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
	`notes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
	CONSTRAINT fk_companiesandservices_alternatives FOREIGN KEY (`companiesandservices_id`) REFERENCES companiesAndServices(`companiesandservices_id`)
)	ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `alternatives`
(`alternatives_id`, `companiesandservices_id`, `name`, `description`, `link`, `logo`, `notes`)
	VALUES
(1, 1, '', '', '', '', '');

DROP TABLE IF EXISTS `resources`;
CREATE TABLE IF NOT EXISTS `resources` (
	`resources_id` bigint UNSIGNED PRIMARY KEY NOT NULL,
	`companiesandservices_id` bigint UNSIGNED NOT NULL,
	`name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
	`link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
	CONSTRAINT fk_companiesandservices_resources FOREIGN KEY (`companiesandservices_id`) REFERENCES companiesAndServices(`companiesandservices_id`)
)	ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `resources`
(`resources_id`, `companiesandservices_id`, `name`, `link`)
	VALUES
(1, 1, '', '');

