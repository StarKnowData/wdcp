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
require_once "/www/wdlinux/wdcp/inc/base.func.php";
require_once "/www/wdlinux/wdcp/inc/fun.inc.php";
require_once "/www/wdlinux/wdcp/inc/mysql.func.php";
exec("ps ax | grep -v grep | grep mysql_rootpw_chg.php | wc -l",$str,$re);
if ($str[0]>1) exit;

//$nrpass="wdlinuxcntt";
$nrpass=randstr();
//echo "12\n";
echo "\n";
echo "Running..., Please Wait...\n";
exec("service mysqld stop >/dev/null 2>&1",$str,$re);
//echo $re;
//if ($re!=0) sleep(3);
//echo "22\n";//exit;
exec("/www/wdlinux/mysql/bin/mysqld_safe --skip-grant-tables --socket=/tmp/mysql.sock >/dev/null 2>&1 &",$str,$re);
sleep(3);
//echo "33\n";
//exec("/www/wdlinux/mysql/bin/mysql -uroot -p' ' -S /tmp/mysql.sock -e 'use mysql;update user set password=password(\"$nrpass\") where user=\"root\";flush privileges;'");
exec("/www/wdlinux/mysql/bin/mysql -uroot -p' ' -e 'use mysql;update user set password=password(\"$nrpass\") where user=\"root\";flush privileges;'");
//echo "44\n";exit;
exec("killall -9 mysyqld >/dev/null 2>&1",$str,$re);
//echo "55\n";
//if ($re!=0) sleep(3);
exec("service mysqld start >/dev/null 2>&1");
//echo "66\n";

/*
<?
$sqlrootpw='wdlinux.cn';
$sqlrootpw_en='0';
?>
*/
//echo $nrpass."|".$mykey."\n";

$mykey=wdl_encrypt_key();
$nrpasss=wdl_encrypt($nrpass,$mykey);
//echo $nrpasss;//exit;

$dbrmsg="<?\n\$sqlrootpw='$nrpasss';\n\$sqlrootpw_en='1';\n?>";
//echo "77";
@file_put_contents("/www/wdlinux/wdcp/data/dbr.inc.php",$dbrmsg);
//echo "\n";
echo "\n";
echo "Change root password is OK\n";
echo "Current root password is:$nrpass\n";