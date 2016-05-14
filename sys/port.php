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


$port_tmp=WD_ROOT."/data/tmp/port.txt";
$stop_tmp="/www/wdlinux/wdcp/data/tmp/stop.txt";
if (isset($_GET['act']) && $_GET['act']=="stop") {
	//demo
	wdl_demo_sys();
	
	$srv=chop($_GET['srv']);
	if (empty($srv)) go_back("´íÎó!");
	if ($srv=="nginx.conf") $srv="nginxd";
	elseif ($srv=="pure-ftpd") $srv="pureftpd";
	//exec("sudo wd_sys service stop $srv",$str,$re);
	//wdl_sudo_sys_service_stop($srv);
	@file_put_contents($stop_tmp,$srv);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);
	if (@file_exists($stop_tmp)) @unlink($stop_tmp);
	optlog($wdcp_uid,"Í£Ö¹·þÎñ $srv",0,0);//
	if ($re==0)
		str_go_url("ÒÑÍ£Ö¹!",0);
	else
		go_back("Í£Ö¹Ê§°Ü!");
	exit;
}

//exec("sudo wd_sys port stat",$str,$re);
//$str=wdl_sudo_sys_port_stat();
//print_r($str);//
@touch($port_tmp);
exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);
if (@file_exists($port_tmp)) @unlink($port_tmp);
$list=array();
  for ($i=0;$i<sizeof($str);$i++) {
  	//tcp 0.0.0.0:3306 1242/mysqld 
	$s1=explode("|",$str[$i]);
	$pro=$s1[0];
	if (substr($s1[3],0,3)===":::") {
		$sip=":::";
		$sport=str_replace($sip,"",$s1[3]);
	}else{
		$s2=explode(":",$s1[3]);
		$sip=$s2[0];
		$sport=$s2[1];
	}
	if ($pro=="udp")
		$s3=explode("/",$s1[5]);
	else
		$s3=explode("/",$s1[6]);
	$pid=$s3[0];
	$prog=$s3[1];
	
	$list[$i]['pid']=$pid;
	$list[$i]['pro']=$pro;
	$list[$i]['sip']=$sip;
	$list[$i]['sport']=$sport;
	$list[$i]['prog']=$prog;
  }
require_once(G_T("sys/port.htm"));

//G_T_F("footer.htm");
footer_info();
?>