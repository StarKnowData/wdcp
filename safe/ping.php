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

$ping_tmp=WD_ROOT."/data/tmp/ping.txt";
if (isset($_GET['act']) and $_GET['act']=="on") {
	//$re=wdl_sudo_sys_ping("on");
	@file_put_contents($ping_tmp,0);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_ss.php",$str,$re);//print_r($str);print_r($re);exit;
	if (@file_exists($ping_tmp)) @unlink($ping_tmp);
	optlog($wdcp_uid,"开启服务器ping功能",0,0);//
	if ($re==0)
		str_go_url("设置成功!",0);
	else
		go_back("设置失败!");
	exit;
}
if (isset($_GET['act']) and $_GET['act']=="off") {
	//$re=wdl_sudo_sys_ping("off");
	@file_put_contents($ping_tmp,1);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_ss.php",$str,$re);
	if (@file_exists($ping_tmp)) @unlink($ping_tmp);
	optlog($wdcp_uid,"关闭服务器ping功能",0,0);////
	if ($re==0)
		str_go_url("设置成功!",0);
	else
		go_back("设置失败!");//
	exit;
}
//$re=wdl_sys_ping_stat();
$re=@file_get_contents("/proc/sys/net/ipv4/icmp_echo_ignore_all");
$result=return_num($re,0,"可ping","禁ping");

require_once(G_T("safe/ping.htm"));

//G_T_F("footer.htm");
footer_info();
?>