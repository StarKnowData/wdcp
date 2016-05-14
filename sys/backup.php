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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>数据备份</title>
</head>

<body>
<?
if (isset($_GET['act']) and $_GET['act']=="bk" and isset($_GET['t'])) {
	$t=chop($_GET['t']);
	$name=chop($_GET['name']);
	$fdir=chop($_GET['p']);
	if ($t=="mysql") 
		//$cu_dir=$mysql_data;
		$cu_dir="/www/wdlinux/mysql/var";
	else {
		$s1=explode("/",$fdir);
		$name=end($s1);
		$cu_dir=substr($fdir,0,strlen($fdir)-strlen($name)-1);
	}
	//echo $cu_dir;exit;
	//echo $cu_dir."|".$name."<br>";
	//exec("sudo wd_app bk $t '$cu_dir' '$name'",$str,$re);
	//wdl_sudo_app_bk($t,$cu_dir,$name);
	$backup_tmp=WD_ROOT."/data/tmp/backup.txt";
	@file_put_contents($backup_tmp,$cu_dir."|".$name."|".$t);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);exit;
	if (@file_exists($backup_tmp)) @unlink($backup_tmp);
	//print_r($str);print_r($re);
	optlog($wdcp_uid,"数据备份 $name",0,0);//
	if ($re==0) js_close("$name 备份成功!");
	else go_back("备份失败!");
	
}
?>
</body>
</html>
