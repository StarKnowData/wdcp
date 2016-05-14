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


$process_tmp=WD_ROOT."/data/tmp/process.txt";
if (isset($_GET['act']) && $_GET['act']=="kill") {
	//demo
	wdl_demo_sys();
	
	$pid=intval($_GET['pid']);
	if (!is_numeric($pid)) go_back("pid错误!");
	//exec("sudo wd_sys process kill $pid",$str,$re);
	//wdl_sudo_sys_process_kill($pid);
	@file_put_contents($process_tmp,$pid);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);
	if (@file_exists($process_tmp)) @unlink($process_tmp);
	optlog($wdcp_uid,"强制终止进程 $pid",0,0);//
	//echo $re;exit;
	if ($re==0)
		str_go_url("成功终止进程$pid!",0);
	else
		str_go_url("该进程已结束!",0);
	exit;
}

//exec("wd_sys process stat",$str,$re);
//$str=wdl_sys_process_stat();
//print_r($str);
@touch($process_tmp);
exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);
if (@file_exists($process_tmp)) @unlink($process_tmp);
$list=array();
$j=0;
  for ($i=1;$i<sizeof($str);$i++) {
  	//echo $str[$i]."<br>";
	$s1=explode("|",$str[$i]);
	//print_r($s1);exit;
	if (empty($s1[0])) {
		$pid=$s1[1];
		$user=$s1[2];
		//if ($s1[3]==="wd_sys") continue;
		if ($s1[3]==="sh" or $s1[3]==="wd_sys" or $s1[3]==="tr" or $s1[3]==="ps" or $s1[3]==="sudo") continue;
		if (@eregi("wdcp_",$s1[4])) continue;
		//if ($s1[3]==="/bin/sh")
			//$prog=$s1[4];
		//else
			//$prog=$s1[3];
		@$prog=$s1[3]." ".$s1[4]." ".$s1[5]." ".$s1[6];
	}else{
		$pid=$s1[0];
		$user=$s1[1];
		//if (eregi("wd_sys|tr|ps",$s1[2])) continue;
		if ($s1[2]==="sh" or $s1[2]==="wd_sys" or $s1[2]==="tr" or $s1[2]==="ps" or $s1[2]==="sudo") continue;
		if (@eregi("wdcp_",$s1[3])) continue;
		//if ($s1[2]==="/bin/sh")
			//$prog=$s1[3];
		//else
			//$prog=$s1[2];	
		@$prog=$s1[2]." ".$s1[3]." ".$s1[4]." ".$s1[5];
	}
	$list[$j]['pid']=$pid;
	$list[$j]['user']=$user;
	$list[$j]['prog']=$prog;
	$j++;
  }

require_once(G_T("sys/process.htm"));

//G_T_F("footer.htm");
footer_info();
?>
