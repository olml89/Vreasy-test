
CREATE DATABASE IF NOT EXISTS `vreasy_test`
DEFAULT CHARACTER SET utf8
COLLATE utf8_spanish_ci;

USE `vreasy_test`;

--7 decimal digits are used by the sunrise-sunset API
--latitude ranges from -90º to +90º, so we need 2 + 7 = 9 digits
--longitude ranges from -180º to +180º, so we need 3 + 7 = 10 digits

CREATE TABLE IF NOT EXISTS `cities` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(32) NOT NULL,
	`latitude` FLOAT(9, 7) NOT NULL, 
	`longitude` FLOAT(10, 7) NOT NULL, 
	PRIMARY KEY (`id`),
	UNIQUE KEY unique_name(`name`),
	UNIQUE KEY unique_coordinates(`latitude`, `longitude`)
) ENGINE = InnoDB
DEFAULT CHARSET = utf8
COLLATE utf8_spanish_ci;

--Some default values to dire up the database
INSERT IGNORE INTO cities(`name`, `latitude`, `longitude`) VALUES('Austin', 30.267153, -97.743057);
INSERT IGNORE INTO cities(`name`, `latitude`, `longitude`) VALUES('Boston', 42.358433, -71.059776);
INSERT IGNORE INTO cities(`name`, `latitude`, `longitude`) VALUES('Anchorage', 61.218056, -149.900284);
INSERT IGNORE INTO cities(`name`, `latitude`, `longitude`) VALUES('Pawtucket', 32.899776, -71.382553);
INSERT IGNORE INTO cities(`name`, `latitude`, `longitude`) VALUES('Providence', 41.823990, -71.412834);
INSERT IGNORE INTO cities(`name`, `latitude`, `longitude`) VALUES('Miami', 25.789097, -80.204041);
INSERT IGNORE INTO cities(`name`, `latitude`, `longitude`) VALUES('Las Vegas', 36.169941, -115.139832);
INSERT IGNORE INTO cities(`name`, `latitude`, `longitude`) VALUES('Honolulu', 21.304850, -157.857758);
INSERT IGNORE INTO cities(`name`, `latitude`, `longitude`) VALUES('San Francisco', 37.774929, -122.419418);
INSERT IGNORE INTO cities(`name`, `latitude`, `longitude`) VALUES('El Paso', 31.7754152, -106.4646348);
INSERT IGNORE INTO cities(`name`, `latitude`, `longitude`) VALUES('Albuquerque', 35.0841034, -106.6509851);
INSERT IGNORE INTO cities(`name`, `latitude`, `longitude`) VALUES('Chicago', 41.8755616, -87.6244212);
INSERT IGNORE INTO cities(`name`, `latitude`, `longitude`) VALUES('Columbia Falls', 48.379963, -114.180405);
INSERT IGNORE INTO cities(`name`, `latitude`, `longitude`) VALUES('Phoenix', 33.448376, -112.074036);
