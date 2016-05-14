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

$resolv_tmp=WD_ROOT."/data/tmp/resolv.conf";
if (isset($_POST['Submit'])) {
	wdl_demo_sys();
	$ns1=chop($_POST['ns1']);
	$ns2=chop($_POST['ns2']);
	$sdomain=chop($_POST['sdomain']);
	//$str=$ns1."|".$ns2."|".$sdomain;
	$str="";
	if ($ns1!=="")	$str.="nameserver ".$ns1."\n";
	if ($ns2!=="") $str.="nameserver ".$ns2."\n";
	if ($sdomain!=="") $str.="search ".$sdomain;
	//echo $str."<br>";
	//exec("sudo wd_sys resolv set '$str'",$str,$re);
	//$re=wdl_sudo_sys_resolv_set("$str");
	//print_r($str);exit;
	@file_put_contents($resolv_tmp,$str);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);
	if (@file_exists($resolv_tmp)) @unlink($resolv_tmp);
	optlog($wdcp_uid,"修改服务器DNS的IP",0,0);//
	if ($re==0)
		str_go_url("保存成功!",0);
	else
		go_back("保存失败!");
	exit;
	
}

$resolv_conf="/etc/resolv.conf";
$str=@file_get_contents($resolv_conf);
//echo $str;
preg_match_all("/^nameserver(.*)$/imU",$str,$s1);
$ns1=@trim($s1[1][0]);
$ns2=@trim($s1[1][1]);
preg_match("/^search (.*)$/imU",$str,$s1);
//print_r($s1);
if (!empty($s1))
	$domain=$s1[1];	

/*
//exec("wd_sys resolv stat",$str,$re);
$str=wdl_sys_resolv_stat();
$ns=$sd=array();
for ($i=0;$i<sizeof($str);$i++) {
	$s1=explode(" ",$str[$i]);
	if ($s1[0]==="nameserver") {
		$ns[]=$s1[1];
		continue;
		}
	if ($s1[0]==="search") {
		$sd[]=$s1[1];
		continue;
		}
}
//print_r($str);
$ns1=$ns[0];
$ns2=$ns[1];
$domain=$sd[0];
*/

require_once(G_T("sys/resolv.htm"));

//G_T_F("footer.htm");
footer_info();
?>