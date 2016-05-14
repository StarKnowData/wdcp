-- phpMyAdmin SQL Dump
-- version 3.4.3.1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2011 年 10 月 30 日 13:35
-- 服务器版本: 5.1.58
-- PHP 版本: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `wdcpdb`
--

-- --------------------------------------------------------

--
-- 表的结构 `wd_conf`
--

CREATE TABLE IF NOT EXISTS `wd_conf` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `val` varchar(255) NOT NULL DEFAULT '',
  `sort` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `note` tinytext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `wd_conf`
--

INSERT INTO `wd_conf` (`id`, `name`, `val`, `sort`, `note`) VALUES
(1, 'templates_dir', 'templates', 0, '模板目录'),
(2, 'cookie_time', '1800', 0, 'cookie缓存时间');

-- --------------------------------------------------------

--
-- 表的结构 `wd_ftp`
--

CREATE TABLE IF NOT EXISTS `wd_ftp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `nid` int(4) unsigned NOT NULL DEFAULT '0',
  `user` varchar(20) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `uid` int(11) NOT NULL DEFAULT '1000',
  `gid` int(11) NOT NULL DEFAULT '1000',
  `dir` varchar(128) NOT NULL DEFAULT '',
  `quotafiles` int(10) NOT NULL DEFAULT '0',
  `quotasize` int(10) NOT NULL DEFAULT '0',
  `ulbandwidth` int(10) NOT NULL DEFAULT '0',
  `dlbandwidth` int(10) NOT NULL DEFAULT '0',
  `ipaddress` varchar(15) NOT NULL DEFAULT '*',
  `comment` tinytext,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `ulratio` smallint(5) NOT NULL DEFAULT '1',
  `dlratio` smallint(5) NOT NULL DEFAULT '1',
  `rtime` int(11) unsigned NOT NULL DEFAULT '0',
  `utime` int(11) unsigned NOT NULL DEFAULT '0',
  `vtime` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `wd_group`
--

CREATE TABLE IF NOT EXISTS `wd_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL DEFAULT '',
  `site` TINYINT( 4 ) UNSIGNED NOT NULL DEFAULT  '0',
  `ftp` TINYINT( 4 ) UNSIGNED NOT NULL DEFAULT  '0',
  `mysql` TINYINT( 4 ) UNSIGNED NOT NULL DEFAULT  '0',
  `count` int(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=11 ;

--
-- 转存表中的数据 `wd_group`
--

INSERT INTO `wd_group` (`id`, `name`, `count`) VALUES
(1, '管理员', 0),
(10, '普通用户', 0);

-- --------------------------------------------------------

--
-- 表的结构 `wd_loginlog`
--

CREATE TABLE IF NOT EXISTS `wd_loginlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL DEFAULT '',
  `passwd` varchar(32) NOT NULL,
  `lip` varchar(20) NOT NULL DEFAULT '',
  `ltime` int(11) unsigned NOT NULL DEFAULT '0',
  `state` int(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `wd_member`
--

CREATE TABLE IF NOT EXISTS `wd_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gid` tinyint(4) unsigned NOT NULL DEFAULT '10',
  `pid` int(8) unsigned NOT NULL DEFAULT '0',
  `name` varchar(30) NOT NULL DEFAULT '',
  `passwd` char(32) NOT NULL DEFAULT '',
  `xm` varchar(15) NOT NULL DEFAULT '',
  `xb` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sfzh` varchar(20) NOT NULL DEFAULT '',
  `addr` varchar(255) NOT NULL DEFAULT '',
  `tel` varchar(100) NOT NULL DEFAULT '',
  `qq` varchar(20) NOT NULL DEFAULT '',
  `email` varchar(30) NOT NULL DEFAULT '',
  `sitec` int(8) unsigned NOT NULL DEFAULT '0',
  `cdnc` int(8) unsigned NOT NULL DEFAULT '0',
  `vpsc` int(8) unsigned NOT NULL DEFAULT '0',
  `dnsc` int(8) unsigned NOT NULL DEFAULT '0',
  `question` varchar(255) NOT NULL DEFAULT '',
  `answer` varchar(255) NOT NULL DEFAULT '',
  `money` float(10,2) unsigned NOT NULL DEFAULT '0.00',
  `umoney` float(10,2) unsigned NOT NULL DEFAULT '0.00',
  `cmoney` float(10,2) unsigned NOT NULL DEFAULT '0.00',
  `m1` varchar(50) NOT NULL DEFAULT '',
  `m2` varchar(50) NOT NULL DEFAULT '',
  `m3` varchar(50) NOT NULL DEFAULT '',
  `m4` int(8) unsigned NOT NULL DEFAULT '0',
  `m5` int(8) unsigned NOT NULL DEFAULT '0',
  `rtime` int(11) unsigned NOT NULL DEFAULT '0',
  `utime` int(10) unsigned NOT NULL DEFAULT '0',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `wd_member`
--

INSERT INTO `wd_member` (`id`, `gid`, `name`, `passwd`, `xm`, `xb`, `sfzh`, `addr`, `tel`, `qq`, `email`, `sitec`, `cdnc`, `vpsc`, `dnsc`, `question`, `answer`, `money`, `umoney`, `cmoney`, `m1`, `m2`, `m3`, `m4`, `m5`, `rtime`, `utime`, `state`) VALUES
(1, 1, 'admin', '7e537d80319ad455cf42057d10157a73', '', 0, '', '', '', '', '', 0, 0, 0, 0, '', '', 0.00, 0.00, 0.00, '', '', '', 0, 0, 1307807638, 1307807638, 0);

-- --------------------------------------------------------

--
-- 表的结构 `wd_mysql`
--

CREATE TABLE IF NOT EXISTS `wd_mysql` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL DEFAULT '0',
  `nid` int(4) unsigned NOT NULL DEFAULT '0',
  `dbuser` varchar(20) NOT NULL DEFAULT '',
  `dbpw` varchar(32) NOT NULL DEFAULT '',
  `dbhost` varchar(32) NOT NULL DEFAULT 'localhost',
  `dbname` varchar(20) NOT NULL DEFAULT '',
  `dbchar` varchar(20) NOT NULL DEFAULT '',
  `dbsize` int(8) unsigned NOT NULL DEFAULT '0',
  `isuser` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `comment` tinytext,
  `rtime` int(11) unsigned NOT NULL DEFAULT '0',
  `utime` int(11) unsigned NOT NULL DEFAULT '0',
  `vtime` int(11) unsigned NOT NULL DEFAULT '0',
  `state` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `wd_optlog`
--

CREATE TABLE IF NOT EXISTS `wd_optlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(15) NOT NULL DEFAULT '',
  `ip` varchar(20) NOT NULL DEFAULT '',
  `otime` int(11) unsigned NOT NULL DEFAULT '0',
  `opt` varchar(255) NOT NULL DEFAULT '',
  `act` tinyint(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `wd_site`
--

CREATE TABLE IF NOT EXISTS `wd_site` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(8) unsigned NOT NULL DEFAULT '0',
  `fid` int(8) unsigned NOT NULL DEFAULT '0',
  `nid` int(4) unsigned NOT NULL DEFAULT '0',
  `domain` varchar(30) NOT NULL DEFAULT '',
  `domains` varchar(255) DEFAULT '',
  `domainss` tinyint(1) DEFAULT '0',
  `sdomain` tinyint(1) DEFAULT '0',
  `vhost_dir` varchar(50) NOT NULL DEFAULT '',
  `limit_dir` varchar(50) NOT NULL DEFAULT 'def',
  `conn` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `bw` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `uip` varchar(20) NOT NULL DEFAULT '',
  `port` int(8) unsigned NOT NULL DEFAULT '80',
  `ruser` tinyint(1) NOT NULL DEFAULT '0',
  `rewrite` varchar(255) NOT NULL DEFAULT '',
  `ssl` tinyint(1) NOT NULL DEFAULT '0',
  `dir_index` varchar(50) DEFAULT '',
  `dir_list` tinyint(1) NOT NULL DEFAULT '0',
  `file_inc` tinyint(1) NOT NULL DEFAULT '0',
  `gzip` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `expires` tinyint(1) unsigned NOT NULL,
  `a_filetype` varchar(255) NOT NULL,
  `a_url` varchar(255) NOT NULL DEFAULT '',
  `d_url` varchar(255) NOT NULL DEFAULT '',
  `safe_mode` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `err400` tinyint(1) NOT NULL DEFAULT '0',
  `err401` tinyint(1) NOT NULL DEFAULT '0',
  `err403` tinyint(1) NOT NULL DEFAULT '0',
  `err404` tinyint(1) NOT NULL DEFAULT '0',
  `err405` tinyint(1) NOT NULL DEFAULT '0',
  `err500` tinyint(1) NOT NULL DEFAULT '0',
  `err503` tinyint(1) NOT NULL DEFAULT '0',
  `re_dir` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `re_url` varchar(100) NOT NULL DEFAULT '',
  `err301` varchar(80) NOT NULL DEFAULT '',
  `err302` varchar(80) NOT NULL DEFAULT '',
  `access_log` tinyint(1) NOT NULL DEFAULT '0',
  `error_log` tinyint(1) NOT NULL DEFAULT '0',
  `rtime` int(10) unsigned NOT NULL DEFAULT '0',
  `utime` int(10) unsigned NOT NULL DEFAULT '0',
  `vtime` int(11) unsigned NOT NULL DEFAULT '0',
  `comment` tinytext,
  `state` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


CREATE TABLE IF NOT EXISTS `wd_task` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `file` varchar(200) NOT NULL DEFAULT '',
  `d1` varchar(200) NOT NULL DEFAULT '',
  `d2` varchar(200) NOT NULL DEFAULT '',
  `d3` varchar(200) NOT NULL DEFAULT '',
  `d4` varchar(200) NOT NULL DEFAULT '',
  `d5` varchar(200) NOT NULL DEFAULT '',
  `ut` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=10 ;

--
-- 转存表中的数据 `wd_task`
--


CREATE TABLE IF NOT EXISTS `wd_tasklog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) not null default '',
  `note` varchar(255) not null default '',
  `rtime` int(11) unsigned not null default '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `wd_mail_tp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mt` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` text DEFAULT '',
  `apt` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `rtime` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `wd_member_gpw` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `str` varchar(32) not null default '',
  `state` tinyint(1) unsigned not null default '0',
  `rtime` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;
