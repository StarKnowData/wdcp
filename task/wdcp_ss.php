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

//ping
$ping_tmp="/www/wdlinux/wdcp/data/tmp/ping.txt";
$ping_conf="/proc/sys/net/ipv4/icmp_echo_ignore_all";
$sysctl_conf="/etc/sysctl.conf";
if (@file_exists($ping_tmp)) {
	$re=@file_get_contents($ping_tmp);
	@file_put_contents($ping_conf,$re);
	$str=@file_get_contents($sysctl_conf);
	//$a=0;
	if (!eregi("net.ipv4.icmp_echo_ignore_all",$str)) {
		$str.="net.ipv4.icmp_echo_ignore_all = $re";
		//$a++;
	}else{
		//preg_match_all("/^net.ipv4.icmp_echo_ignore_all(.*)$/imU",$str,$s1);
		$str=preg_replace("/^net.ipv4.icmp_echo_ignore_all(.*)$/imU","net.ipv4.icmp_echo_ignore_all = $re",$str);
		//$a++;
	}
	@file_put_contents($sysctl_conf,$str);
	@unlink($ping_tmp);
	exit;
}

//selinux
$selinux_tmp="/www/wdlinux/wdcp/data/tmp/selinux.txt";
$selinux_conf="/etc/selinux/config";
if (@file_exists($selinux_tmp)) {
	$re=@file_get_contents($selinux_tmp);
	if ($re==0) $res="enforcing";
	elseif ($re==1) $res="permissive";
	else $res="disabled";
	$str=@file_get_contents($selinux_conf);
	$str=preg_replace("/SELINUX=(.*)$/imU","SELINUX=$res",$str);
	@file_put_contents($selinux_conf,$str);
	@unlink($selinux_tmp);
	exit;
}

//ssh
$ssh_tmp="/www/wdlinux/wdcp/data/tmp/ssh.txt";
$ssh_conf="/etc/ssh/sshd_config";
if (@file_exists($ssh_tmp)) {
	$str=@file_get_contents($ssh_conf);
	$s1=@file($ssh_tmp);
	for ($i=0;$i<sizeof($s1);$i++) {
		$s2=explode("=",$s1[$i]);
		if (preg_match("/^$s2[0] (.*)$/imU",$str,$s1t)){
			//echo "$s2[0] $s2[1]";
			$str=preg_replace("/^$s2[0] (.*)$/imU","$s2[0] $s2[1]",$str);
		}else{
			//echo "$s2[0] $s2[1]";
			$str=preg_replace("/^#$s2[0] (.*)$/imU","$s2[0] $s2[1]",$str);
		}
	}
	@file_put_contents($ssh_conf,$str);
	@unlink($ssh_tmp);
	exit;
}

//ssh key down
$ssh_kt="/www/wdlinux/wdcp/data/tmp/ssh_kt.txt";
if (@file_exists($ssh_kt)) {
	$str=@file_get_contents($ssh_kt);
	$s1=explode("|",$str);//echo "|".end($s1)."|".r_k_c()."|";
	//echo strcmp(end($s1),r_k_c());exit;
	if (@strcmp(end($s1),r_k_c())!=0) {@unlink($ssh_kt);exit;}
	//echo $str;
	if (!@file_exists("/root/.ssh/wdcp_sshkey")) {echo "no";exit;}
	exec("cp -f /root/.ssh/wdcp_sshkey /tmp/sksehy.txt;chown wdcpu.wdcpg /tmp/sksehy.txt",$str,$re);
	@unlink($ssh_kt);
	exit;
}

//ssh key make
$ssh_mk="/www/wdlinux/wdcp/data/tmp/ssh_mk.txt";
if (@file_exists($ssh_mk)) {
	$str=@file_get_contents($ssh_mk);
	$s1=explode("|",$str);//echo "|".end($s1)."|".r_k_c()."|";
	//echo strcmp(end($s1),r_k_c());exit;
	if (@strcmp(end($s1),r_k_c())!=0) {@unlink($ssh_mk);exit;}
	if (!@is_dir("/root/.ssh")) exec("mkdir /root/.ssh");
	exec('ssh-keygen -t rsa -b 1024 -f /root/.ssh/wdcp_sshkey -N "";mv -f /root/.ssh/wdcp_sshkey.pub /root/.ssh/authorized_keys;chmod 400 /root/.ssh/authorized_keys',$str,$re);
	@unlink($ssh_mk);
	exit;
}

//ssh key chp
$ssh_kchp="/www/wdlinux/wdcp/data/tmp/ssh_kchp.txt";
if (@file_exists($ssh_kchp)) {
	$str=@file_get_contents($ssh_kchp);
	$s1=explode("|",$str);//echo "|".end($s1)."|".r_k_c()."|";
	//echo strcmp(end($s1),r_k_c());exit;
	if (@strcmp(end($s1),r_k_c())!=0) {@unlink($ssh_kchp);exit;}
	if (!@file_exists("/root/.ssh/wdcp_sshkey")) {@unlink($ssh_kchp);echo "nk";exit;}
	//echo "ssh-keygen -t rsa -b 1024 -f /root/.ssh/wdcp_sshkey -p -P '$s1[0]' -N '$s1[1]'";
	exec("ssh-keygen -t rsa -b 1024 -f /root/.ssh/wdcp_sshkey -p -P '$s1[0]' -N '$s1[1]';mv -f /root/.ssh/wdcp_sshkey.pub /root/.ssh/authorized_keys;chmod 400 /root/.ssh/authorized_keys",$str,$re);
	@unlink($ssh_kchp);
	//print_r($str);print_r($re);
	if (@in_array("Bad passphrase.",$str)) echo "bad";
	exit;
}

//ssh root passwd
$rcp_tmp="/www/wdlinux/wdcp/data/tmp/rcp.txt";
//$ssh_conf="/etc/ssh/sshd_config";
if (@file_exists($rcp_tmp)) {
	$str=chop(@file_get_contents($rcp_tmp));
	@unlink($rcp_tmp);
	if (empty($str)) exit;
	//exec("
	exit;
}

?>