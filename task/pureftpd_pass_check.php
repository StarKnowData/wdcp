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
//$def_conf="/www/wdlinux/wdcp_cf/pureftpd-mysql.conf";
//if (!file_exists($def_conf)) exit;
//$str=@file_get_contents($def_conf);
//$s1=preg_replace("/{passwd}/isU","$dbpw",$str);
//@file_put_contents("/www/wdlinux/etc/pureftpd-mysql.conf",$s1);
pureftpd_mysql_repair();
system("service pureftpd restart");
echo "\n";
echo "pureftpd conf check is OK\n";
?>