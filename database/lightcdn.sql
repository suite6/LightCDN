# SQL Manager 2005 for MySQL 3.6.5.1
# ---------------------------------------
# Host     : localhost
# Port     : 3306
# Database : lightcdn


#
# Structure for the `assets_info` table : 
#

DROP TABLE IF EXISTS `assets_info`;

CREATE TABLE `assets_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `asset_headers` longtext NOT NULL,
  `header` longtext NOT NULL,
  `first_served` datetime NOT NULL,
  `last_served` datetime NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `original_url` varchar(255) NOT NULL,
  `mime_type` text NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `where_clause` (`original_url`,`deleted`)
) DEFAULT CHARSET=utf8;
