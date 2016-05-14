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

if (isset($_GET['act'])) {
	$act=chop($_GET['act']);
	wdl_demo_sys();
	$selinux_tmp=WD_ROOT."/data/tmp/selinux.txt";
	if ($act=="on"){
		//$re=wdl_sudo_sys_selinux_set("enforcing");
		@file_put_contents($selinux_tmp,0);
		exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_ss.php",$str,$re);
		if (@file_exists($selinux_tmp)) @unlink($selinux_tmp);
	}elseif ($act=="off"){
		//$re=wdl_sudo_sys_selinux_set("disabled");
		@file_put_contents($selinux_tmp,2);
		exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_ss.php",$str,$re);
		if (@file_exists($selinux_tmp)) @unlink($selinux_tmp);
	}elseif ($act=="warn"){
		//$re=wdl_sudo_sys_selinux_set("permissive");
		@file_put_contents($selinux_tmp,1);
		exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_ss.php",$str,$re);
		if (@file_exists($selinux_tmp)) @unlink($selinux_tmp);
	}else
		go_back("错误!");
	optlog($wdcp_uid,"设置selinux功能为 $act",0,0);//
	if ($re==0)
		str_go_url("设置成功!",0);
	else
		go_back("设置错误!");
	exit;
}

//$re=wdl_sys_selinux_stat();
$str=@file_get_contents("/etc/selinux/config");
preg_match("/^SELINUX=(.*)$/imU",$str,$s1);
//print_r($s1);
if (strtolower($s1[1])==="enforcing") $result="开启";
elseif (strtolower($s1[1])==="permissive") $result="警告";
else $result="关闭";

if ($os_rl==2) {$act_link='';$result="当前系统,不支持该功能";}
else
	$act_link='<a href="'.$PHP_SELF.'?act=off">关闭</a> / <a href="'.$PHP_SELF.'?act=warn">警告</a> / <a href="'.$PHP_SELF.'?act=on">开启</a>';

require_once(G_T("safe/selinux.htm"));

//G_T_F("footer.htm");
footer_info();
?>
