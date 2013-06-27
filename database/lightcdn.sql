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
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `asset_headers` longtext NOT NULL,
  `first_served` datetime NOT NULL,
  `last_served` datetime NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `original_url` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `content_length` varchar(255) DEFAULT NULL,
  `vary` varchar(255) DEFAULT NULL,
  `last_modified` varchar(255) DEFAULT NULL,
  `etag` varchar(255) DEFAULT NULL,
  `content_language` varchar(255) DEFAULT NULL,
  `accept_encoding` varchar(255) DEFAULT NULL,
  `expires` varchar(255) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `where_clause` (`original_url`,`deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;