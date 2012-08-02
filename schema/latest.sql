SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE DATABASE `commando` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `commando`;

CREATE TABLE IF NOT EXISTS `groups` (
  `id` char(25) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `added` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `added` (`added`),
  KEY `modified` (`modified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `recipe_heads` (
  `recipe` char(25) COLLATE utf8_unicode_ci NOT NULL,
  `recipe_version` char(25) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `recipe` (`recipe`),
  UNIQUE KEY `recipe_version` (`recipe_version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `recipe_versions` (
  `id` char(25) COLLATE utf8_unicode_ci NOT NULL,
  `recipe` char(25) COLLATE utf8_unicode_ci NOT NULL,
  `version` char(40) COLLATE utf8_unicode_ci NOT NULL,
  `interpreter` enum('shell','bash','perl','python','node.js') COLLATE utf8_unicode_ci NOT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `added` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `version` (`version`),
  KEY `added` (`added`),
  KEY `recipe` (`recipe`),
  KEY `interpreter` (`interpreter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `recipes` (
  `id` char(25) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `added` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `added` (`added`),
  KEY `modified` (`modified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `servers` (
  `id` char(25) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `group` char(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tags` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` blob NOT NULL,
  `ssh_username` blob NOT NULL,
  `ssh_port` blob NOT NULL,
  `added` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `label` (`label`),
  KEY `added` (`added`),
  KEY `modified` (`modified`),
  KEY `group` (`group`),
  KEY `tags` (`tags`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` tinyint(1) NOT NULL,
  `data` blob NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `modified` (`modified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


ALTER TABLE `recipe_heads`
  ADD CONSTRAINT `recipe_heads_ibfk_1` FOREIGN KEY (`recipe`) REFERENCES `recipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `recipe_heads_ibfk_2` FOREIGN KEY (`recipe_version`) REFERENCES `recipe_versions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `recipe_versions`
  ADD CONSTRAINT `recipe_versions_ibfk_1` FOREIGN KEY (`recipe`) REFERENCES `recipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `servers`
  ADD CONSTRAINT `servers_ibfk_1` FOREIGN KEY (`group`) REFERENCES `groups` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
