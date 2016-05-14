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

if (isset($_GET['act']) and ($_GET['act']=="restart")) {
	$srv=chop($_GET['srv']);
	//exec("sudo wd_app restart $srv",$str,$re);
	if ($srv=="web") 
		if ($web_eng==1){
			$str="httpd";
		}elseif ($web_eng==2){
			$str="nginxd";
		}elseif ($web_eng==3) {
			$str="nginxd,httpd";
		}else;
	else
		$str=$srv;
	if (empty($str)) go_back("服务错误");
	$restart_tmp=WD_ROOT."/data/tmp/restart.txt";
	@file_put_contents($restart_tmp,$str);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);exit;
	if (@file_exists($restart_tmp)) @unlink($restart_tmp);
	//$re=wdl_sudo_app_restart($srv);
	//passthru("sudo wd_app restart $srv");
	//print_r($str);print_r($re);exit;
	optlog($wdcp_uid,"重起服务 $srv",0,0);//
	if ($re==0) 
		str_go_url("服务重起完成!",1);
	exit;

}

if (isset($_GET['act']) and ($_GET['act']=="reboot")) {
	//demo
	wdl_demo_sys();
	//exec("sudo wd_sys sys reboot",$str,$re);
	//$re=wdl_sudo_sys_reboot();
	$reboot_tmp=WD_ROOT."/data/tmp/reboot.txt";
	@touch($reboot_tmp);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);exit;
	if (@file_exists($reboot_tmp)) @unlink($reboot_tmp);
	//print_r($str);print_r($re);exit;
	optlog($wdcp_uid,"重起机器",0,0);//
	if ($re==0) 
		str_go_url("服务器重起中!",0);
	exit;

}

if (isset($_GET['act']) and ($_GET['act']=="halt")) {
	//demo
	wdl_demo_sys();
	//exec("sudo wd_sys sys halt",$str,$re);
	//$re=wdl_sudo_sys_halt();
	$halt_tmp=WD_ROOT."/data/tmp/halt.txt";
	@touch($halt_tmp);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);exit;
	if (@file_exists($halt_tmp)) @unlink(halt_tmp);
	optlog($wdcp_uid,"关机",0,0);//
	if ($re==0) 
		str_go_url("服务器关机中!",0);
	exit;
}

require_once(G_T("sys/server.htm"));

//G_T_F("footer.htm");
footer_info();
?>
