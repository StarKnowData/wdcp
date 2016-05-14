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


$netstat_tmp=WD_ROOT."/data/tmp/netstat.txt";

if (isset($_GET['act']) and $_GET['act']=="drop") {
	//demo
	wdl_demo_sys();
	$ip=chop($_GET['ip']);
	$act=chop($_GET['act']);
	$msg="-I INPUT -s $ip -j DROP";
	//exec("sudo wd_sys iptables set '$msg'",$str,$re);
	//$re=wdl_sudo_sys_iptables_set("$msg");
	$iptablesa_tmp=WD_ROOT."/data/tmp/iptablesa.txt";
	@file_put_contents($iptablesa_tmp,$msg);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);
	if (@file_exists($iptablesa_tmp)) @unlink($iptablesa_tmp);
	optlog($wdcp_uid,"拒绝 $ip 连接",0,0);//
	if ($re==0)
		str_go_url("已拒绝该IP的连接!",0);
	else
		go_back("操作失败!");
}

if (!isset($_GET['act']) or $_GET['act']=="all_c") {
	//exec("wd_sys netstat all_c",$str,$re);
	@file_put_contents($netstat_tmp,"all");
	exec("/www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);
	if (@file_exists($netstat_tmp)) @unlink($netstat_tmp);
	$all=$str[0];
	require_once(G_T("sys/netstat_all.htm"));
}

if (isset($_GET['act']) and $_GET['act']=="ip_c") {
	//exec("wd_sys netstat ip_c",$str,$re);
	@file_put_contents($netstat_tmp,"ip");
	exec("/www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);
	if (@file_exists($netstat_tmp)) @unlink($netstat_tmp);
	//print_r($str);
	$list=array();
	for ($i=0;$i<sizeof($str);$i++) {
		$s1=explode(" ",trim($str[$i]));
		//if (empty($s1[1])) continue;
		$list[$i][0]=$s1[0];
		$list[$i][1]=$s1[1];
	}
	require_once(G_T("sys/netstat_ip.htm"));
}

//
if (isset($_GET['act']) and $_GET['act']=="state_c") {
	//exec("wd_sys netstat state_c",$str,$re);
	@file_put_contents($netstat_tmp,"state");
	exec("/www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);
	if (@file_exists($netstat_tmp)) @unlink($netstat_tmp);
	$list=array();
	for ($i=0;$i<sizeof($str);$i++) {
		$s1=explode(" ",$str[$i]);
		$list[$i][0]=$s1[0];
		$list[$i][1]=$s1[1];
	}
	require_once(G_T("sys/netstat_state.htm"));
}

//web
if (isset($_GET['act']) and $_GET['act']=="web_c") {
	//exec("wd_sys netstat web_c",$str,$re);
	@file_put_contents($netstat_tmp,"web");
	exec("/www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);
	if (@file_exists($netstat_tmp)) @unlink($netstat_tmp);
	$list=array();
	for ($i=0;$i<sizeof($str);$i++) {
		$s1=explode(" ",$str[$i]);
		$list[$i][0]=$s1[0];
		$list[$i][1]=$s1[1];
	}
	require_once(G_T("sys/netstat_web.htm"));
}

//mysql
if (isset($_GET['act']) and $_GET['act']=="mysql_c") {
	//exec("wd_sys netstat mysql_c",$str,$re);
	@file_put_contents($netstat_tmp,"mysql");
	exec("/www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);
	if (@file_exists($netstat_tmp)) @unlink($netstat_tmp);
	$list=array();
	for ($i=0;$i<sizeof($str);$i++) {
		//echo $str[$i];
		if (empty($str[$i])) $str[$i]="无连接";
		$s1=explode(" ",$str[$i]);
		$list[$i][0]=$s1[0];
		$list[$i][1]=$s1[1];
	}
	require_once(G_T("sys/netstat_mysql.htm"));
}
if (!isset($_GET['act'])) 
	require_once(G_T("sys/netstat_all.htm"));

//G_T_F("footer.htm");
footer_info();
?>

