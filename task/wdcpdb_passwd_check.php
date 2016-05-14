<?php
/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/

set_time_limit(0);
if (substr(php_sapi_name(),0,3) !== 'cli') exit;
define('WD_ROOT', substr(dirname(__FILE__), 0, -4));
//require_once "/www/wdlinux/wdcp/inc/common.inc.php";
require_once "/www/wdlinux/wdcp/data/db.inc.php";
require_once "/www/wdlinux/wdcp/inc/base.func.php";
require_once "/www/wdlinux/wdcp/inc/fun.inc.php";
require_once "/www/wdlinux/wdcp/inc/mysql.func.php";

exec("ps ax | grep -v grep | grep wdcpdb_passwd_check.php | wc -l",$str,$re);
if ($str[0]>1) exit;
exit;
/*
$dbhost = 'localhost';
$dbuser = 'wdcp';
$dbpw = '641cbb11';
$dbname = 'wdcpdb';
$pconnect = 0;
$dbcharset = 'gbk';
*/

//echo "00";

if (!($c=@mysql_connect($dbhost,$dbuser,$dbpw))) {
	//echo "11";
	if (!file_exists("/www/wdlinux/wdcp/data/dbr.inc.php")) exit;
	require_once "/www/wdlinux/wdcp/data/dbr.inc.php";
	$mykey=wdl_encrypt_key();
	if ($sqlrootpw_en==0)
		$sqlrp=$sqlrootpw;
	else
		$sqlrp=wdl_sqlroot_pw();
	//echo "|->".$sqlrp."\n";
	$nrpass=randstr();
	//echo $nrpass."\n";
	if (!($link = @mysql_connect("localhost","root",$sqlrp))) exit;
	//echo "33\n";
	//echo "use mysql;update user set password=password('$nrpass') where user='wdcp'\n";
	@mysql_select_db("mysql");
	@mysql_query("update user set password=password('$nrpass') where user='wdcp';");
	@mysql_query("flush privileges;");
	@mysql_close($link);
	$str=@file_get_contents("/www/wdlinux/wdcp/data/db.inc.php");
	//echo $dbpw."|".$nrpass."\n";
	$nstr=str_replace("$dbpw","$nrpass",$str);
	@file_put_contents("/www/wdlinux/wdcp/data/db.inc.php",$nstr);
	echo "\n";
	echo "wdcpdb passwd is repair OK\n";
	echo"\n";
}else{
	//echo 22;
	echo "\n";
	echo "wdcpdb passwd is OK\n";
	echo "\n";
}


?>