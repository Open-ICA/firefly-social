-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1:3306
-- 生成日期： 2024-07-22 02:33:36
-- 服务器版本： 8.3.0
-- PHP 版本： 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `firefly`
--

-- --------------------------------------------------------

--
-- 表的结构 `local_notes`
--

DROP TABLE IF EXISTS `local_notes`;
CREATE TABLE IF NOT EXISTS `local_notes` (
  `tid` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender` bigint UNSIGNED NOT NULL,
  `sendtimestamp` int NOT NULL,
  `title` text NOT NULL,
  `content` mediumtext NOT NULL,
  `replyto` varchar(256) NOT NULL,
  PRIMARY KEY (`tid`),
  KEY `replyindex` (`replyto`(250)),
  KEY `senderindex` (`sender`),
  KEY `sendtimeindex` (`sendtimestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- 表的结构 `local_user_auth`
--

DROP TABLE IF EXISTS `local_user_auth`;
CREATE TABLE IF NOT EXISTS `local_user_auth` (
  `uid` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nickname` varchar(256) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `headerurl` text NOT NULL,
  `displayname` text NOT NULL,
  `passwordcci` text NOT NULL,
  `mail` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `appubkey` text NOT NULL,
  `apprivkey` text NOT NULL,
  `summary` text NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `unique-nickname` (`nickname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- 表的结构 `timeline_cache`
--

DROP TABLE IF EXISTS `timeline_cache`;
CREATE TABLE IF NOT EXISTS `timeline_cache` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `url` varchar(500) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `sendtimestamp` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url-index` (`url`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
