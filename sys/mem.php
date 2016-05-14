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



if (isset($_GET['act']) && $_GET['act']==="release") {
	//echo "OK";
	//exec("sudo wd_sys mem release",$str,$re);
	//$re=wdl_sudo_sys_mem_release();
	$mem_tmp=WD_ROOT."/data/tmp/mem.txt";
	@touch($mem_tmp);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);
	if (@file_exists($mem_tmp)) @unlink($mem_tmp);
	optlog($wdcp_uid,"强制释放内存",0,0);//
	if ($re==0)
		str_go_url("内存释放成功!",0);
	else
		str_go_url("错误,系统不允许此操作!",0);
	exit;
}

/*
exec("sudo wd_sys mem stat",$str,$re);
$total=round($str[0]/1024);
$free=round($str[1]/1024);
$buffer=round($str[2]/1024);
$cached=round($str[3]/1024);
$use=round(($str[0]-$str[1])/1024);
$atotal=round($str[4]/1024);
$afree=round($str[5]/1024);
$ause=round(($str[4]-$str[5])/1024);
$stotal=round(($str[0]+$str[4])/1024);
$sfree=round(($str[1]+$str[5])/1024);
$suse=round($stotal-$sfree);
*/
$mem=wdl_server_mem(0);
$m1=explode("|",$mem);
//return $total."|".$use."|".$free."|".$buffer."|".$cached."|".$swapt."|".$swapu."|".$swapf;
$total=$m1[0];
$use=$m1[1];
$free=$m1[2];
$buffer=$m1[3];
$cached=$m1[4];
$availfree=$free+$buffer+$cached;
$availuse=$total-$availfree;
$atotal=$m1[5];
$ause=$m1[6];
$afree=$m1[7];
$stotal=$total+$atotal;
$suse=$use+$ause;
$sfree=$free+$afree;


require_once(G_T("sys/mem.htm"));

//G_T_F("footer.htm");
footer_info();
?>