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


$service_tmp=WD_ROOT."/data/tmp/service.txt";
$port_tmp=WD_ROOT."/data/tmp/port.txt";
$stop_tmp=WD_ROOT."/data/tmp/stop.txt";
$start_tmp=WD_ROOT."/data/tmp/start.txt";
$stops_tmp=WD_ROOT."/data/tmp/stops.txt";
$starts_tmp=WD_ROOT."/data/tmp/starts.txt";


if (!isset($_GET['view'])) $view="run";
else	$view=chop($_GET['view']);

if (isset($_GET['act']) && $_GET['act']==="stop") {
	//demo
	wdl_demo_sys();
		
	$s=isset($_GET['s'])?y:n;
	$srv=chop($_GET['srv']);
	if ($s==="y") {
		//exec("sudo wd_sys service stop $srv off",$str,$re);
		//$re=wdl_sudo_sys_service_stop_off($srv);
		@file_put_contents($stops_tmp,$srv);
		exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);
		if (@file_exists($stops_tmp)) @unlink($stops_tmp);
		optlog($wdcp_uid,"关闭服务启动 $srv",0,0);//
		if ($re==0) str_go_url("设置成功!",0);
	}else{
		//exec("sudo wd_sys service stop $srv",$str,$re);
		//$re=wdl_sudo_sys_service_stop($srv);
		@file_put_contents($stop_tmp,$srv);
		exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);
		if (@file_exists($stop_tmp)) @unlink($stop_tmp);
		if ($re==0) str_go_url("服务已停止!",0);
		optlog($wdcp_uid,"关闭服务 $srv",0,0);//
	}
	exit;
}

if (isset($_GET['act']) && $_GET['act']==="start") {
	wdl_demo_sys();
	$s=isset($_GET['s'])?y:n;
	//echo $s;
	$srv=chop($_GET['srv']);
	//echo $s."|".$srv;exit;
	if ($s==="y") {
		//echo "Y";
		//exec("sudo wd_sys service start $srv on",$str,$re);//print_r($str);print_r($re);exit;
		//wdl_sudo_sys_service_start_on($srv);
		@file_put_contents($starts_tmp,$srv);
		exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);
		if (@file_exists($starts_tmp)) @unlink($starts_tmp);
		optlog($wdcp_uid,"设置服务启动 $srv",0,0);//
		if ($re==0) str_go_url("设置成功!",0);
	}else{
		//echo "NO";
		//exec("sudo wd_sys service start $srv",$str,$re);//print_r($str);print_r($re);exit;
		//$re=wdl_sudo_sys_service_start($srv);
		@file_put_contents($start_tmp,$srv);
		exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);
		if (@file_exists($start_tmp)) @unlink($start_tmp);
		optlog($wdcp_uid,"启动服务 $srv",0,0);//
		if ($re==0) str_go_url("服务已启动!",0);
	}	
	exit;
}

if (isset($_GET['act']) && $_GET['act']=="restart") {
	wdl_demo_sys();
	$srv=chop($_GET['srv']);
	if ($srv=="wdapache") {
		$reload_tmp=WD_ROOT."/data/tmp/reload.txt";
		@file_put_contents($reload_tmp,$srv);
		exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);exit;
		if (@file_exists($reload_tmp)) @unlink($reload_tmp);
	}else{
		$restart_tmp=WD_ROOT."/data/tmp/restart.txt";
		@file_put_contents($restart_tmp,$srv);
		exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);exit;
		if (@file_exists($restart_tmp)) @unlink($restart_tmp);		
	}
	optlog($wdcp_uid,"重启服务 $srv",0,0);//
	if ($re==0) str_go_url("服务已重启!",1);
}


/*
//exec("sudo wd_sys service stat",$str,$re);
$str=wdl_sudo_sys_service_stat();
//print_r($str);print_r($re);
//exec("sudo wd_sys port stat",$str1,$re1);
$str1=wdl_sudo_sys_port_stat();
//print_r($str1);print_r($re1);
*/

@touch($service_tmp);
exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);
if (@file_exists($service_tmp)) @unlink($service_tmp);
@touch($port_tmp);
exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str1,$re1);//print_r($str1);print_r($re1);
if (@file_exists($port_tmp)) @unlink($port_tmp);

$ss=array();
$as=array("httpd","nginx.conf","php-fpm","mysqld","vsftpd","pure-ftpd","proftpd","sshd","portmap","sendmail","named");
for ($i=0;$i<sizeof($str1);$i++) {
	$ss1=explode("/",$str1[$i]);
	$ss2=chop($ss1[1]);
	$ss[]=substr($ss2,0,strlen($ss2)-1);
}
//print_r($ss);
//print_r($str);

  $j=1;
  $list=array();
  for ($i=0;$i<sizeof($str);$i++) {
  	$s1=explode(" ",$str[$i]);
	$s2=explode(":",$s1[1]);
	if ($s2[1]=="on" or in_array($s1[0],$ss)) {
		$sd1="运行中...";
		$sd2="是";
		if ($s2[1]=="off") $sd2="否";
		$sd3="<a href=\"".$PHP_SELF."?act=stop&srv=".$s1[0]."\">关闭</a>";
		$sd4="<a href=\"".$PHP_SELF."?act=stop&s=y&srv=".$s1[0]."\">禁止自启动</a>";	
		if (!in_array("$s1[0]",$ss) and in_array("$s1[0]",$as)) {
			$sd1="关闭";
			$sd3="<a href=\"".$PHP_SELF."?act=start&srv=".$s1[0]."\">启动</a>";
		}
		$sd5="<a href=\"".$PHP_SELF."?act=restart&srv=".$s1[0]."\">重启</a>";
	}else {
		if ($view=="run") continue;
		$sd1="关闭";
		$sd2="否";
		$sd3="<a href=\"".$PHP_SELF."?act=start&srv=".$s1[0]."\">启动</a>";
		$sd4="<a href=\"".$PHP_SELF."?act=start&s=y&srv=".$s1[0]."\">自启动</a>";
		$sd5="";
	}
	/*
	if (in_array("$s1[0]",$ss)) {
		$sd1="运行中...";
		$sd3="<a href=\"".$PHP_SELF."?act=stop&srv=".$s1[0]."\">关闭</a>";
	}
	*/
	$list[$j]['id']=$j;
	$list[$j]['prog']=$s1[0];
	$list[$j]['sd1']=$sd1;
	$list[$j]['sd2']=$sd2;
	$list[$j]['sd3']=$sd3;
	$list[$j]['sd4']=$sd4;
	$list[$j]['sd5']=$sd5;
  	$j++;
  }
//print_r($list);

require_once(G_T("sys/service.htm"));

//G_T_F("footer.htm");
footer_info();
?>
