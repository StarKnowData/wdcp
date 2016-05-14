<?
/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/

set_time_limit(0);
if (substr(php_sapi_name(),0,3) !== 'cli') exit;
if (@file_exists("/tmp/wdcp21_up.txt")) exit;

require_once "/www/wdlinux/wdcp/inc/common.inc.php";

if (@file_exists("/tmp/wdcp21_up.txt")) exit;

//更新用户shell
$str=@file_get_contents("/etc/passwd");
if (eregi("wdcp:/bin/bash",$str)) {
	//echo "11";
	exec("usermod -s /sbin/nologin wdcpu",$str,$re);//print_r($str);print_r($re);
}
//exit;

//更新sudoers
$str=@file_get_contents("/etc/sudoers");
if (!eregi("/www/wdlinux/wdphp/bin/php",$str)) {
	//echo "22";
	$str=preg_replace("/wd_app/imU","wd_app,/www/wdlinux/wdphp/bin/php",$str);
	@file_put_contents("/etc/sudoers",$str);
}

$nginx_conf="/www/wdlinux/nginx/conf/nginx.conf";
if (@file_exists($nginx_conf)) {
	//echo "33\n";
	$str=@file_get_contents($nginx_conf);
	$i=0;
	if (!eregi("limit_zone",$str)) {
		//echo "44\n";
		$str=preg_replace("/sendfile/isU","limit_zone one \$binary_remote_addr 32k;\n    sendfile",$str);
		//@file_put_contents($nginx_conf,$str);
		$i++;
	}
	if (!eregi("#include default.conf",$str)) {
		$str=str_replace("include default.conf","#include default.conf",$str);
		@copy("/www/wdlinux/nginx/conf/default.conf","/www/wdlinux/nginx/conf/vhost/00000.default.conf");
		exec("chown wdcpu.wdcpg /www/wdlinux/nginx/conf/vhost/00000.default.conf");
		$i++;
	}
	if ($i>0)	@file_put_contents($nginx_conf,$str);
}

//default site
//apache
$httpd_conf="/www/wdlinux/apache/conf/httpd.conf";
if (@file_exists($httpd_conf)) {
	$str=@file_get_contents($httpd_conf);
	if (!eregi("#Include conf/default.conf",$str)) {
		$str=str_replace("Include conf/default.conf","#Include conf/default.conf",$str);
		@copy("/www/wdlinux/apache/conf/default.conf","/www/wdlinux/apache/conf/vhost/00000.default.conf");
		exec("chown wdcpu.wdcpg /www/wdlinux/apache/conf/vhost/00000.default.conf");
		@file_put_contents($httpd_conf,$str);
	}
}

//php.ini
$php_ini="/www/wdlinux/wdphp/lib/php.ini";
$str=@file_get_contents($php_ini);
if (!preg_match("/^session.save_path/imU",$str,$s1)) {
	$str=preg_replace("/^;session.save_path(.*)$/imU","session.save_path = \"/www/wdlinux/tmp\"",$str);
	@file_put_contents($php_ini,$str);
	if (!@is_dir("/www/wdlinux/tmp")) exec("mkdir -p /www/wdlinux/tmp;chmod 777 /www/wdlinux/tmp",$str,$re);
	exec("service wdapache restart",$str,$re);
}

//计划任务表
$tabsql="CREATE TABLE IF NOT EXISTS `wd_task` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) not null default '',
  `file` varchar(200) not null default '',
  `d1` varchar(200) not null default '0',
  `d2` varchar(200) not null default '0',
  `d3` varchar(200) not null default '0',
  `d4` varchar(200) not null default '0',
  `d5` varchar(200) not null default '0',
  `ut` tinyint(1) unsigned not null default '0',
  `state` tinyint(1) unsigned not null default '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=10 ;";
//if (!@file_exists("/tmp/wdcp21_up.txt")) crunquery($tabsql);

//默认计划任务
$sql="insert into wd_task(id,name,file,d1,d2,d3,d4,d5,ut,state) values
(1,'配置文件备份','/www/wdlinux/wdcp/task/wdcp_conf_backup.php','5','1','*','*','*','0','1'),
(2,'自动释放内存','/www/wdlinux/wdcp/task/wdcp_release_mem.php','5','5','*','*','*','1','1'),
(3,'mysql备份','/www/wdlinux/wdcp/task/wdcp_mysql_backup.php','35','1','*','*','*','0','1'),
(4,'mysql大小统计','/www/wdlinux/wdcp/task/wdcp_mysql_size_c.php','15','1','*','*','*','0','1'),
(5,'网站备份','/www/wdlinux/wdcp/task/wdcp_site_backup.php','5','2','*','*','*','0','1'),
(6,'FTP备份','/www/wdlinux/wdcp/task/wdcp_ftp_backup.php','5','3','*','*','*','0','1');";
//if (!@file_exists("/tmp/wdcp21_up.txt")) runquery($sql);

//计划任务表日志
$tabsql="CREATE TABLE IF NOT EXISTS `wd_tasklog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) not null default '',
  `note` varchar(255) not null default '',
  `rtime` int(11) unsigned not null default '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;";
//if (!@file_exists("/tmp/wdcp21_up.txt")) crunquery($tabsql);

$tabsql="CREATE TABLE IF NOT EXISTS `wd_mail_tp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mt` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` text DEFAULT '',
  `apt` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `rtime` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;";
//if (!@file_exists("/tmp/wdcp21_up.txt")) crunquery($tabsql);

$sql="ALTER TABLE `wd_site` ADD `a_filetype` VARCHAR( 255 ) NOT NULL AFTER `file_inc` ;
ALTER TABLE `wd_site` ADD `re_dir` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `err503` ;
ALTER TABLE `wd_site` ADD `re_url` VARCHAR( 100 ) NOT NULL DEFAULT '' AFTER `re_dir` ;
ALTER TABLE `wd_site` ADD `bw` TINYINT( 4 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `limit_dir` ;
ALTER TABLE `wd_site` ADD `conn` TINYINT( 4 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `limit_dir` ;
ALTER TABLE `wd_site` ADD `gzip` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `file_inc` ;
ALTER TABLE `wd_site` ADD `expires` TINYINT( 1 ) UNSIGNED NOT NULL AFTER `gzip` ;";
//if (!@file_exists("/tmp/wdcp21_up.txt")) runquery($sql);

$t="/www/wdlinux/wdcp/wdcp1to2.php";
if (@file_exists($t)) @unlink($t);
$t="/www/wdlinux/wdcp/wdcp21_up.php";
if (@file_exists($t)) @unlink($t);
@touch("/tmp/wdcp21_up.txt");

if (@is_dir("/www/wdlinux/nginx")) exec("service nginxd restart",$str,$re);
if (@is_dir("/www/wdlinux/apache")) exec("service httpd restart",$str,$re);
echo "\n";
echo "\n";
echo "wdcp update is OK\n";
echo "\n";
?>