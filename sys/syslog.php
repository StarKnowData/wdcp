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


$syslog_tmp="/www/wdlinux/wdcp/data/tmp/syslog.txt";
$syslogv_tmp="/www/wdlinux/wdcp/data/tmp/syslogv.txt";
$syslogd_tmp="/www/wdlinux/wdcp/data/tmp/syslogd.txt";
if (!isset($_GET['act']) and !isset($_GET['f'])) {
	@touch($syslog_tmp);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sv.php",$str,$re);
	if (@file_exists($syslog_tmp)) @unlink($syslog_tmp);
	//print_r($str);
	$list=array();
	for ($i=0;$i<=sizeof($str);$i++) {
		if (empty($str[$i])) continue;
		$s1=explode("|",$str[$i]);
		$list[$i]["name"]=$s1[0];
		$list[$i]['size']=afile_size($s1[1]);
		$list[$i]['time']=date("Y-m-d H:i",$s1[2]);
	}
	require_once(G_T("sys/syslog.htm"));
}else{
	wdl_demo_sys();
	$act=chop($_GET['act']);
	$f=chop($_GET['f']);
	if ($act=="del") {
		@file_put_contents($syslogd_tmp,$f);
		exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);exit;
		if (@file_exists($syslogd_tmp)) @unlink($syslogd_tmp);
		if ($re==0) str_go_url("文件已删除","syslog.php");
		$msg="删除文件列表:\n";
		$msg.=implode("\n",$str);
	}else{
		if (@is_readable($f)) 
			$msg=@file_get_contents($f);
		else{
			@file_put_contents($syslogv_tmp,$f);
			exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sv.php",$str,$re);//print_r($str);print_r($re);
			if (@file_exists($syslogv_tmp)) @unlink($syslogv_tmp);
			//if ($re==0) str_go_url("文件已删除","syslog.php");
			$msg=implode("\n",$str);
			//foreach($str as $c)
				//$msg.=$c;
		}
	}
	require_once(G_T("sys/syslog_view.htm"));
}

/*
$f=chop($_GET['t']);
if (empty($f)) $f="messages";
//$msg=file_get_contents($f);
//exec("sudo wd_sys syslog $f",$str,$re);//print_r($str);print_r($re);
$str=wdl_sudo_sys_syslog($f);
optlog($wdcp_uid,"查看日志 $f",0,0);//
$msg="";
for ($i=0;$i<sizeof($str);$i++) {
	if (eregi("wd_sys",$str[$i])) continue;
	$msg.=$str[$i]."\n";
}
*/

//G_T_F("footer.htm");
footer_info();
?>
