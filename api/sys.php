<?php

/*
# WDlinux Control Panel,online management of Linux servers, virtual hosts
# Wdcdn system
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcdn
# Last Updated 2011.05
*/

require_once "../inc/common.inc.php";
if (!isset($api_ip) or empty($api_ip)) exit;
$ip_list=explode(",",$api_ip);
$rip=$_SERVER['REMOTE_ADDR'];
//if ($api_ip!="null" and !in_array($rip,$ip_list)) exit;
if ($api_ip!="null" and !in_array($rip,$ip_list)) exit;
$act=chop($_GET['act']);
if (!isset($_GET['type'])) exit;
else $type=intval($_GET['type']);
$srv=chop($_GET['srv']);
$api_key=chop($_GET['key']);
if (empty($wdcp_uid)) $wdcp_uid=0;
if ($api_key!==md5($type.$api_pass)) { echo "key err";exit;}//

/*
act{reboot,halt,restart}
act=>restart,srv{web,mysqld,pureftpd,sshd}
type{mem,load,runtime}
*/

if ($type=="mem") {
	echo wdl_server_mem(0);
}elseif ($type=="load") {
	echo wdl_server_load(0);
}elseif ($type=="runtime") {
	echo wdl_server_run_time(0);
}elseif ($type=="server") {
	if ($act=="reboot") {
		$re=wdl_sudo_sys_reboot();
		optlog($wdcp_uid,"重起机器",0,0);//
	}elseif ($act=="halt") {
		$re=wdl_sudo_sys_halt();
		optlog($wdcp_uid,"关机",0,0);
	}elseif ($act=="restart") {
		if ($srv=="web")
			web_restart();
		else
			wdl_sudo_app_restart($srv);
		optlog($wdcp_uid,"重起服务 $srv",0,0);//
	}
}else;

?>