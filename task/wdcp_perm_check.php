<?php
/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/

set_time_limit(0);
if (substr(php_sapi_name(),0,3) !== 'cli') exit;
require_once "/www/wdlinux/wdcp/inc/common.inc.php";

exec("ps ax | grep -v grep | grep wdcp_perm_check.php | wc -l",$str,$re);
if ($str[0]>1) exit;

//用户
$wdcpu="wdcpu";
$wdcpg="wdcpg";

//web目录权限环境检测
//nginx
if (@is_dir("/www/wdlinux/nginx/conf/vhost")) {
	exec("chown -R wdcpu.wdcpg /www/wdlinux/nginx/conf/vhost");
	exec("chown -R wdcpu.wdcpg /www/wdlinux/nginx/conf/rewrite");
	//chown("/www/wdlinux/nginx/conf/vhost",$wdcpu);
	//chown("/www/wdlinux/nginx/conf/rewrite",$wdcpu);
	//chgrp("/www/wdlinux/nginx/conf/vhost",$wdcpg);
	//chgrp("/www/wdlinux/nginx/conf/rewrite",$wdcpg);
}
//nginx
if (@is_dir("/www/wdlinux/apache/conf/vhost")) {
	exec("chown -R wdcpu.wdcpg /www/wdlinux/apache/conf/vhost");
	exec("chown -R wdcpu.wdcpg /www/wdlinux/apache/conf/rewrite");
	//chown("/www/wdlinux/apache/conf/vhost",$wdcpu);
	//chown("/www/wdlinux/apache/conf/rewrite",$wdcpu);
	//chgrp("/www/wdlinux/apache/conf/vhost",$wdcpg);
	//chgrp("/www/wdlinux/apache/conf/rewrite",$wdcpg);
}
//sleep(300);
//check sudoer
$str=@file_get_contents("/etc/sudoers");
$i=0;
if (!eregi("wdcpu",$str)) {
	$str.="wdcpu  ALL=(ALL) NOPASSWD:/bin/wd_sys,/bin/wd_app,/www/wdlinux/wdphp/bin/php\n";
	$i++;
}

$msg="#Defaults    requiretty\n";
$msg.="Defaults logfile=/dev/null\n";
$msg.="Defaults loglinelen=0\n";
$msg.="Defaults syslog\n";
if (!eregi("#Defaults    requiretty",$str)) {
	$str=preg_replace("/Defaults    requiretty/isU",$msg,$str);
	$i++;
}

if ($i>0) @file_put_contents("/etc/sudoers",$str);
$i=0;

//public_html
if (!@is_dir("/etc/skel/public_html") and @file_exists("/www/wdlinux/wdcp_bk/public_html.tar.gz"))
	exec("tar zxvf /www/wdlinux/wdcp_bk/public_html.tar.gz -C /etc/skel >/dev/null 2>&1");

if (@is_dir("/www/wdlinux/apache_php"))//20120616
	exec("chown -R wdcpu.wdcpg /www/wdlinux/apache_php/etc/");
if (@is_dir("/www/wdlinux/nginx_php"))
	exec("chown -R wdcpu.wdcpg /www/wdlinux/nginx_php/etc/");//

//
//chown("/www/wdlinux/wdcp/data",$wdcpu);
//chgrp("/www/wdlinux/wdcp/data",$wdcpg);
exec("chown -R wdcpu.wdcpg /www/wdlinux/wdcp/data");
exec("chmod -R 755 /www/wdlinux/wdcp/data");
exec("chmod 600 /www/wdlinux/wdcp/data/db.inc.php");
exec("chmod 600 /www/wdlinux/wdcp/data/dbr.inc.php");
exec("chmod 600 /www/wdlinux/wdcp/data/sys_conf.php");
exec("chmod 600 /www/wdlinux/etc/pureftpd-mysql.conf");
//exec("chmod 755 /www/wdlinux/wdcp/data/tmp");
//exec("chown wdcpu.wdcpg /www/wdlinux/init.d");


//wdphp.ini
$php_ini="/www/wdlinux/wdphp/lib/php.ini";
$str=@file_get_contents($php_ini);
if (!preg_match("/^session.save_path/imU",$str,$s1)) {
	$str=preg_replace("/^;session.save_path(.*)$/imU","session.save_path = \"/www/wdlinux/tmp\"",$str);
	$str=str_replace("post_max_size = 8M","post_max_size = 30M",$str);
	$str=str_replace("upload_max_filesize = 2M","upload_max_filesize = 30M",$str);
	$str=str_replace("display_errors = On","display_errors = Off",$str);
	@file_put_contents($php_ini,$str);
	if (!@is_dir("/www/wdlinux/tmp")) exec("mkdir -p /www/wdlinux/tmp;chmod 777 /www/wdlinux/tmp",$str,$re);
	exec("service wdapache restart",$str,$re);
}

//php modules
if (@is_dir("/www/wdlinux/php/lib/php/extensions/no-debug-zts-20060613") and !@is_dir("/www/wdlinux/php/lib/php/extensions/no-debug-non-zts-20060613")) 
	exec("ln -s /www/wdlinux/php/lib/php/extensions/no-debug-zts-20060613 /www/wdlinux/php/lib/php/extensions/no-debug-non-zts-20060613",$str,$re);
	
if (@is_dir("/www/wdlinux/php/lib/php/extensions/no-debug-non-zts-20060613") and !@is_dir("/www/wdlinux/php/lib/php/extensions/no-debug-zts-20060613"))
	exec("ln -s /www/wdlinux/php/lib/php/extensions/no-debug-non-zts-20060613 /www/wdlinux/php/lib/php/extensions/no-debug-zts-20060613",$str,$re);

//backup file
if (!@is_dir("/www/wdlinux/wdcp_bk")) {
	exec("mkdir -p /www/wdlinux/wdcp_bk/{conf,sys,def}",$str,$re);
}
if (!@file_exists("/www/wdlinux/wdcp_bk/sys/sshd_config"))
	@copy("/etc/ssh/sshd_config","/www/wdlinux/wdcp_bk/sys/sshd_config");
if (!@file_exists("/www/wdlinux/wdcp_bk/sys/iptables"))
	@copy("/etc/sysconfig/iptables","/www/wdlinux/wdcp_bk/sys/iptables");

//检查web_log目录
if (!@is_dir($web_logs_home)) {
	exec("mkdir -p $web_logs_home");
	exec("chown www.www $web_logs_home");
}

echo "\n";
echo "all is OK\n";

?>