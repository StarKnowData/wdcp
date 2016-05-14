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

//echo "00\n";
$whois_tmp="/www/wdlinux/wdcp/data/tmp/whois.txt";
if (@file_exists($whois_tmp)) {
	//echo "11\n";
	$str=@file_get_contents($whois_tmp);
	@unlink($whois_tmp);
	if (empty($str)) exit;
	$s2=return_whois(str_replace("www.","",$str));
	if (is_array($s2))
		echo implode("\n",$s2);
	else
		echo $s2;
	exit;
}

//
$dnst_tmp="/www/wdlinux/wdcp/data/tmp/dns_t.txt";
if (@file_exists($dnst_tmp)) {
	$str=@file_get_contents($dnst_tmp);
	@unlink($dnst_tmp);
	if (empty($str)) exit;
	$s2=return_ns_ip($str);
	if (is_array($s2))
		echo implode("\n",$s2);
	else
		echo $s2;
	exit;
}	


//$domain="wdcdn.com";
//$str=return_ns_ip($domain);
//$str=return_whois($domain);
//print_r($str);

function return_ns_ip($domain) {
	global $dns_ip_list_c;
	if (!@file_exists("/usr/bin/dig")) return;
	$msg=array();
	//echo $domain."\n";
	for ($i=0;$i<sizeof($dns_ip_list_c);$i++) {
		//echo sizeof($dns_ip_list_c[$i])."\n";
		for ($j=0;$j<sizeof($dns_ip_list_c[$i]);$j++) {
			$nip=$dns_ip_list_c[$i][$j][1];
			//echo $nip."\n";
			exec("dig @$nip +short +time=1 +tries=1 +retry=1 $domain",$str,$re);
			//$msg[$nip]=$str[0];
			$msg[]=$str[0];
			$str="";
			//print_r($str);
			//$msg[]=$
			//echo $dns_ip_list_c[$i][$j][1]."\n";
		}
	}
	//print_r($msg);
	return $msg;
}

function return_whois($domain) {
	if (!@file_exists("/usr/bin/whois")) return;
	exec("whois $domain",$str,$re);//print_r($str);
	$ss=implode("\n",$str);
	preg_match("/Creation Date    : (.*) /isU",$ss,$s1);
	preg_match("/Updated Date     : (.*) /isU",$ss,$s2);
	preg_match("/Expiration Date  : (.*) /isU",$ss,$s3);
	$msg=array();
	$msg[]="注册时间:".$s1[1];
	$msg[]="更新时间:".$s2[1];
	$msg[]="到期时间:".$s3[1];
	return $msg;
}

//print_r($dns_ip_list);
?>