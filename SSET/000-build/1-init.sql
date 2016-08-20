### Simple database initialization file

DROP DATABASE social_engineering;
CREATE DATABASE social_engineering;
USE social_engineering;

DROP TABLE IF EXISTS `session`;
CREATE TABLE IF NOT EXISTS `session` (
 `id` char(128) NOT NULL,
 `access` int(10) unsigned DEFAULT NULL,
 `data` text,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE codes (codeid int AUTO_INCREMENT NOT NULL, code varchar(255), group_name text, redirect_url text, PRIMARY KEY(codeid));
CREATE TABLE clicks (codeid int, username text, ip_address text, extra MEDIUMTEXT , browser_name varchar(30) DEFAULT NULL, browser_version varchar(30) DEFAULT NULL, platform varchar(30) DEFAULT NULL, user_agent_full text, time datetime, FOREIGN KEY (codeid) REFERENCES codes(codeid) ON DELETE CASCADE ON UPDATE CASCADE);

CREATE TABLE `admins` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `last_login_datetime` datetime NOT NULL,
  `last_login_ip` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `login_attempts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL UNIQUE,
  `login_attempts` tinyint(3) DEFAULT 0,
  `first_failed_login_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
