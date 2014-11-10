-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.6.17 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             8.3.0.4694
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table sb_db.a_auto_numbering
DROP TABLE IF EXISTS `a_auto_numbering`;
CREATE TABLE IF NOT EXISTS `a_auto_numbering` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `guid` char(36) COLLATE utf8_unicode_ci NOT NULL COMMENT 'All record requires external reference should have GUID',
  `entity_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name of the entity',
  `field_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name of the field',
  `is_table` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'Indicates whether entity is a table',
  `prefix` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1' COMMENT 'Prefix to be used',
  `suffix` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1' COMMENT 'Suffix to be used',
  `leading_zero` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Leading zero',
  `next_number` int(10) unsigned NOT NULL DEFAULT '1' COMMENT 'Next number',
  `sys_is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sys_status_code` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sys_note` text COLLATE utf8_unicode_ci,
  `sys_created_by` bigint(20) NOT NULL DEFAULT '0',
  `sys_created_on` datetime DEFAULT NULL,
  `sys_created_ip` bigint(20) NOT NULL DEFAULT '0',
  `sys_modified_by` bigint(20) DEFAULT NULL,
  `sys_modified_on` datetime DEFAULT NULL,
  `sys_modified_ip` bigint(20) DEFAULT NULL,
  `sys_closed_by` bigint(20) DEFAULT NULL,
  `sys_closed_on` datetime DEFAULT NULL,
  `sys_closed_ip` bigint(20) DEFAULT NULL,
  `sys_void_by` bigint(20) DEFAULT NULL,
  `sys_void_on` datetime DEFAULT NULL,
  `sys_void_ip` bigint(20) DEFAULT NULL,
  `sys_last_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `guid` (`guid`),
  UNIQUE KEY `entity_name` (`entity_name`),
  KEY `sys_status_code` (`sys_status_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT COMMENT='Template';

-- Data exporting was unselected.


-- Dumping structure for table sb_db.sys_api_access
DROP TABLE IF EXISTS `sys_api_access`;
CREATE TABLE IF NOT EXISTS `sys_api_access` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `api_key` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'API key for identification',
  `api_secret` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'API secret for integrity checking',
  `description` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Description of the application',
  `sys_is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sys_status_code` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `sys_note` text COLLATE utf8_unicode_ci,
  `sys_created_by` bigint(20) NOT NULL DEFAULT '0',
  `sys_created_on` datetime DEFAULT NULL,
  `sys_created_ip` bigint(20) NOT NULL DEFAULT '0',
  `sys_modified_by` bigint(20) DEFAULT NULL,
  `sys_modified_on` datetime DEFAULT NULL,
  `sys_modified_ip` bigint(20) DEFAULT NULL,
  `sys_closed_by` bigint(20) DEFAULT NULL,
  `sys_closed_on` datetime DEFAULT NULL,
  `sys_closed_ip` bigint(20) DEFAULT NULL,
  `sys_void_by` bigint(20) DEFAULT NULL,
  `sys_void_on` datetime DEFAULT NULL,
  `sys_void_ip` bigint(20) DEFAULT NULL,
  `sys_last_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `guid` (`api_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT COMMENT='API access';

-- Data exporting was unselected.


-- Dumping structure for table sb_db.sys_template
DROP TABLE IF EXISTS `sys_template`;
CREATE TABLE IF NOT EXISTS `sys_template` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `guid` char(36) COLLATE utf8_unicode_ci NOT NULL COMMENT 'All record requires external reference should have GUID',
  `sys_is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sys_status_code` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sys_note` text COLLATE utf8_unicode_ci,
  `sys_created_by` bigint(20) NOT NULL DEFAULT '0',
  `sys_created_on` datetime DEFAULT NULL,
  `sys_created_ip` bigint(20) NOT NULL DEFAULT '0',
  `sys_modified_by` bigint(20) DEFAULT NULL,
  `sys_modified_on` datetime DEFAULT NULL,
  `sys_modified_ip` bigint(20) DEFAULT NULL,
  `sys_closed_by` bigint(20) DEFAULT NULL,
  `sys_closed_on` datetime DEFAULT NULL,
  `sys_closed_ip` bigint(20) DEFAULT NULL,
  `sys_void_by` bigint(20) DEFAULT NULL,
  `sys_void_on` datetime DEFAULT NULL,
  `sys_void_ip` bigint(20) DEFAULT NULL,
  `sys_last_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `guid` (`guid`),
  KEY `sys_status_code` (`sys_status_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Template';

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
