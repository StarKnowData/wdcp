<?php
/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/

set_time_limit(0);
if (substr(php_sapi_name(),0,3) !== 'cli') exit;

require_once "/www/wdlinux/wdcp/inc/common.inc.php";

if (substr(getcwd(),0,17)!="/www/wdlinux/wdcp") exit;

//磁盘大小
$disk_tmp="/www/wdlinux/wdcp/data/tmp/disk.txt";
if (@file_exists($disk_tmp)) {
	exec("df -h",$str,$re);
	$msg="";
	$t="";
	for ($i=1;$i<sizeof($str);$i++) {
		if (empty($str[$i])) continue;
		$s1=explode(" ",preg_replace("/\s+/"," ",$str[$i]));
		//print_r($s1);
		if (empty($s1[1])) $t=$s1[0];
		elseif (empty($s1[0]))
			$msg.=$t."|".$s1[1]."|".$s1[2]."|".$s1[3]."|".$s1[4]."|".$s1[5]."\n";
		else
			$msg.=$s1[0]."|".$s1[1]."|".$s1[2]."|".$s1[3]."|".$s1[4]."|".$s1[5]."\n";
	}
	@unlink($disk_tmp);
	echo $msg;
	exit;
}


//syslogv
$syslogv_tmp="/www/wdlinux/wdcp/data/tmp/syslogv.txt";
if (@file_exists($syslogv_tmp)) {
	$str=@file_get_contents($syslogv_tmp);
	@unlink($syslogv_tmp);
	if (@file_exists($str)) {
		echo @file_get_contents($str);	
	}else
		echo "文件不存在!";
	exit;
}

//syslog
$syslog_tmp="/www/wdlinux/wdcp/data/tmp/syslog.txt";
if (@file_exists($syslog_tmp)) {
	$fd=@opendir("/var/log");
	$msg="";
	while ($buffer=@readdir($fd)) {
		if ($buffer=="." or $buffer=="..") continue;
		//$msg.=$buffer."\n";
		$t="/var/log/".$buffer;
		if (@is_dir($t)) continue;
		if (!@file_exists($t)) continue;
		$msg.=$t."|".@filesize($t)."|".@filemtime($t)."\n";
	}
	@unlink($syslog_tmp);
	echo $msg;
	exit;
}

//sshd_config
$ssh_conf="/etc/ssh/sshd_config";
$ssh_tmp="/www/wdlinux/wdcp/data/tmp/ssh.txt";
//$ssh_tmp="/dev/shm/ssh.txt";
if (@file_exists($ssh_tmp)) {
	$str=@file_get_contents($ssh_conf);
	$msg="";
	if (!preg_match("/^Port (.*)$/imU",$str,$s1))
		$msg="22|";
	else
		$msg=chop($s1[1])."|";
	if (!preg_match("/^PermitRootLogin (.*)$/imU",$str,$s2))
		$msg.="yes|";
	else
		$msg.=trim($s2[1])."|";
	if (!preg_match("/^UseDNS (.*)$/imU",$str,$s3))
		$msg.="yes|";
	else
		$msg.=trim($s3[1])."|";
	if (!preg_match("/^RSAAuthentication (.*)$/imU",$str,$s4))
		$msg.="yes|";
	//elseif (!preg_match("/^#RSAAuthentication (.*)$/imU",$str,$s4))
		//$msg.="yes|";
	else
		$msg.=trim($s4[1])."|";
	if (!preg_match("/^PasswordAuthentication (.*)$/imU",$str,$s5))
		$msg.="yes|";
		//$msg.=trim($s5[1])."|";
	//elseif (!preg_match("/^#PasswordAuthentication (.*)$/imU",$str,$s6))
		//$msg.="yes|";
	else
		$msg.=trim($s5[1])."|";
	//print_r($s5);exit;
		
	@unlink($ssh_tmp);
	echo $msg;
	exit;
}


?>