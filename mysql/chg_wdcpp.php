<?php
/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/

require_once "../inc/common.inc.php";
require_once "../login.php";
require_once "../inc/admlogin.php";
if ($wdcp_gid!=1) exit;

//wdcp
if (isset($_POST['Submit_chg_mysql_wdcp'])) {
	if (in_array($_SERVER["SERVER_ADDR"],$demo_ip)) go_back("演示系统对部分功能已做限制!");

	$mpw=wdl_sqlroot_pw();
	$opass=chop($_POST['opass']);
	//echo $opass."|".$mpw;//exit;
	if (md5($opass)!==md5($dbpw)) go_back("原密码不对!");
	$npass=chop($_POST['npass']);
	$cpass=chop($_POST['cpass']);
	if (md5($npass)!==md5($cpass)) go_back("两次密码不对!");
	if (strlen($npass)<6 or strlen($npass)>20) go_back("密码过短或过长");
	@mysql_connect("localhost","root","$mpw");
	@mysql_query("use mysql;");
	$q=mysql_query("update user set password=password('$npass') where user='wdcp';");
	if (!$q) go_back("密码修改失败!");
	@mysql_query("flush privileges;");
	//$nmpw=encrypt($npass,$mykey);
	//echo $npass."|".$nmpw;exit;
	$dbf=WD_ROOT."/data/db.inc.php";
	$str=<<<EOF
<?
\$dbhost = 'localhost';
\$dbuser = 'wdcp';
\$dbpw = '$npass';
\$dbname = 'wdcpdb';
\$pconnect = 0;
\$dbcharset = 'gbk';
?>
EOF;
	file_put_contents($dbf,$str);
	str_go_url("密码修改成功!",1);
	exit;
}
require_once(G_T("mysql/wdcp_chg.htm"));
G_T_F("footer.htm");

?>