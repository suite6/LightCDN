# SQL Manager 2005 for MySQL 3.6.5.1
# ---------------------------------------
# Host     : localhost
# Port     : 3306
# Database : lightcdn


#
# Structure for the `assets_info` table : 
#

DROP TABLE IF EXISTS `assets_info`;

CREATE TABLE IF NOT EXISTS `assets_info` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `header` LONGTEXT NOT NULL,
  `first_served` DATETIME NOT NULL,
  `last_served` DATETIME NOT NULL,
  `file_size` BIGINT(20) NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `original_url` VARCHAR(255) NOT NULL,
  `mime_type` TEXT NOT NULL,
  `deleted` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `where_clause` (`original_url`,`deleted`)
) DEFAULT CHARSET=utf8 ;


