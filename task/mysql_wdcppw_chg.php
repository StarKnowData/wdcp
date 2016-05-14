<?php
/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/

set_time_limit(0);
if (substr(php_sapi_name(),0,3) !== 'cli') exit;//
define('WD_ROOT', substr(dirname(__FILE__), 0, -4));

//require_once "/www/wdlinux/wdcp/inc/common.inc.php";
@require_once "/www/wdlinux/wdcp/inc/base.func.php";
@require_once "/www/wdlinux/wdcp/inc/fun.inc.php";
@require_once "/www/wdlinux/wdcp/inc/mysql.func.php";
@require_once "/www/wdlinux/wdcp/inc/ftp.func.php";
if (!@file_exists("/www/wdlinux/wdcp/data/dbr.inc.php")) exit;
else @require_once "/www/wdlinux/wdcp/data/dbr.inc.php";
exec("ps ax | grep -v grep | grep mysql_wdcppw_chg.php | wc -l",$str,$re);
if ($str[0]>1) exit;

//$nrpass="wdlinuxcntt";
$nrpass=randstr();
//echo "12\n";
echo "\n";
echo "Running..., Please Wait...\n";
//exit;
$mykey=wdl_encrypt_key();
$mpw=wdl_sqlroot_pw();
//echo $mpw;exit;
mysql_connect("localhost","root","$mpw");
mysql_query("use mysql;");
$q=mysql_query("update user set password=password('$nrpass') where user='wdcp';");
if (!$q) {echo "err";exit;}
//echo "update user set password=password('$nrpass') where user='wdcp';";
mysql_query("flush privileges;");

$dbmsg="<?\n\$dbhost = 'localhost';\n\$dbuser = 'wdcp';\n\$dbpw = '$nrpass';\n\$dbname = 'wdcpdb';\n\$pconnect = 0;\n\$dbcharset = 'gbk';\n?>";
//echo $dbmsg;

@file_put_contents("/www/wdlinux/wdcp/data/db.inc.php",$dbmsg);

$dbpw = "$nrpass";//20120616
pureftpd_mysql_repair();//20120613 
system("service pureftpd restart > /dev/null 2>&1");
//echo "\n";
echo "\n";
echo "Change wdcp password is OK\n";
echo "Current wdcp password is:$nrpass\n";