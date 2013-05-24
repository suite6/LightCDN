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
  `header` blob NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `last_served` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `file_size` bigint(20) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `original_url` varchar(255) NOT NULL,
  `mime_type` text,
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1


