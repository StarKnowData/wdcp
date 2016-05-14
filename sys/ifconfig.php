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

$ifconfig_tmp=WD_ROOT."/data/tmp/ifconfig.txt";
$ifconfiga_tmp=WD_ROOT."/data/tmp/ifconfiga.txt";
$gateway_tmp=WD_ROOT."/data/tmp/gateway.txt";

if (isset($_POST['Submit'])) {
	//demo
	wdl_demo_sys();
	$eth=chop($_POST['eth']);
	$ip=chop($_POST['ipaddr']);
	$netmask=chop($_POST['netmask']);
	$save=isset($_POST['save'])?1:0;
	//$str=$eth." ".$ip." netmask ".$netmask;
	$str="add|".$eth."|".$ip."|".$netmask."|".$save;
	//echo $str;
	//exec("sudo wd_sys ifconfig set '$str'",$str1,$re);//print_r($str);print_r($re);exit;
	
	//$re=wdl_sudo_sys_ifconfig_set("$str");
	@file_put_contents($ifconfiga_tmp,$str);
	//echo file_get_contents($ifconfig_tmp);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);//exit;
	if (@file_exists($ifconfiga_tmp)) @unlink($ifconfiga_tmp);
	/*
	if ($re!=0) go_back("´íÎó!");
	if ($save==1) {
		$msg='###wdcp save config
DEVICE='.$eth.'
BOOTPROTO=static
IPADDR='.$ip.'
NETMASK='.$netmask.'
ONBOOT=yes';
		$fn="../data/tmp/ifcfg-".$eth;
		file_put_contents($fn,$msg);
		//exec("sudo wd_sys copy '$fn' '/etc/sysconfig/network-scripts'",$str2,$re2);
		$re2=wdl_sudo_sys_copy($fn,"/etc/sysconfig/network-scripts/");
		optlog($wdcp_uid,"Ôö¼ÓIPµØÖ· $ip",0,0);//
		if ($re2!=0) go_back("±£´æ´íÎó!");
		unlink($fn);
		
	}
	*/
	
	if ($re==0) 
		str_go_url("IPµØÖ·Ôö¼Ó³É¹¦!",0);
	else
		go_back("Ôö¼ÓÊ§°Ü!");
	exit;
}
if (isset($_GET['act']) and $_GET['act']=="stop") {
	$eth=chop($_GET['e']);
	$ip=chop($_GET['ip']);
	$str="stop|".$eth."|".$ip."_0";
	//echo $str;
	//exec("sudo wd_sys ifconfig stop '$eth'",$str,$re);//print_r($str);print_r($re);exit;
	//$re=wdl_sudo_sys_ifconfig_stop($eth);
	@file_put_contents($ifconfiga_tmp,$str);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);//exit;
	if (@file_exists($ifconfiga_tmp)) @unlink($ifconfiga_tmp);
	optlog($wdcp_uid,"Í£ÓÃIPµØÖ· $ip",0,0);//
	if ($re==0) 
		str_go_url("¸ÃIPÒÑÍ£ÓÃ!",0);
	else
		go_back("²Ù×÷Ê§°Ü!");
	exit;	
}

if (isset($_GET['act']) and $_GET['act']=="del") {
	//echo "OOO";
	$eth=chop($_GET['e']);
	//$ip=chop($_GET['ip']);
	//$str=$eth."_".$ip."_1";
	//echo $str;
	//$fn="/etc/sysconfig/network-scripts/ifcfg-".$eth;
	//exec("sudo wd_sys ifconfig stop '$eth'",$str,$re);
	//$re=wdl_sudo_sys_ifconfig_stop($eth);
	$str="del|".$eth;
	@file_put_contents($ifconfiga_tmp,$str);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);//exit;
	if (@file_exists($ifconfiga_tmp)) @unlink($ifconfiga_tmp);
	//if ($re!=0) go_back("É¾³ýÊ§°Ü!");
	//exec("sudo wd_sys rm '$fn' no",$str,$re);//print_r($str);print_r($re);
	//$re=wdl_sudo_sys_rm($fn);
	optlog($wdcp_uid,"É¾³ýIPµØÖ· $ip",0,0);//
	if ($re==0) 
		str_go_url("¸ÃIPÒÑÉ¾³ý!",0);
	else
		go_back("É¾³ýÊ§°Ü!");
	exit;	
}


if (isset($_GET['act']) and $_GET['act']=="add") {
	require_once(G_T("sys/ifconfig_add.htm"));
}
/*
eth0      Link encap:Ethernet  HWaddr 00:16:36:2D:0F:AC
          inet addr:192.168.1.252  Bcast:192.168.1.255  Mask:255.255.255.0

venet0    Link encap:UNSPEC  HWaddr 00-00-00-00-00-00-00-00-00-00-00-00-00-00-00-00
          inet addr:127.0.0.1  P-t-P:127.0.0.1  Bcast:0.0.0.0  Mask:255.255.255.255
--
venet0:0  Link encap:UNSPEC  HWaddr 00-00-00-00-00-00-00-00-00-00-00-00-00-00-00-00
          inet addr:183.60.137.63  P-t-P:183.60.137.63  Bcast:183.60.137.63  Mask:255.255.255.255
*/
//exec("wd_sys ifconfig stat",$str,$re);

//$str=wdl_sys_ifconfig_stat();
@touch($ifconfig_tmp);
exec("/www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);
if (@file_exists($ifconfig_tmp)) @unlink($ifconfig_tmp);
$t1="";
$t2=array();
for ($i=0;$i<sizeof($str);$i++) {
	//echo $str[$i]."<br>";
	$s1=explode(" ",$str[$i]);
	//print_r($s1);
	if ($s1[6]=="Link" or $s1[4]=="Link" or $s1[2]=="Link")
		$t1=$s1[0]."_";
	if ($s1[10]=="inet") {
		$s2=explode(":",$s1[11]);
		if ($s2[1]=="127.0.0.1") continue;
		$s3=explode(":",$s1[15]);
		$t1.=$s2[1]."_".$s3[1];
		$t2[]=$t1;
		$t1="";
	}
	//echo "|||||||||||<br>";
	//print_r($t2);
	//echo "|||||||||||<br>";
	//print_r($s1);
}
//print_r($t2);
//print_r($str);

$list=array();
for ($i=0;$i<sizeof($t2);$i++) {
	$s1=explode("_",$t2[$i]);
	//if (eregi(":",$s1[0]))
	$list[$i]['0']=$s1[0];
	$list[$i]['1']=$s1[1];
	$list[$i]['2']=$s1[2];	
	if ($i>0)
		$list[$i]['3']='<a href="'.$PHP_SELF.'?act=stop&e='.$s1[0].'&ip='.$s1[1].'">Í£</a> <a href="'.$PHP_SELF.'?act=del&e='.$s1[0].'&ip='.$s1[1].'">É¾</a>';
	else
		$list[$i]['3']='';//<a href="'.$PHP_SELF.'?act=stop&e='.$s1[0].'&ip='.$s1[1].'">Í£</a>';

}

//$gateway_ip=wdl_sys_ifconfig_gw();
@touch($gateway_tmp);
exec("/www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str1,$re);//print_r($str1);print_r($re);
if (@file_exists($gateway_tmp)) @unlink($gateway_tmp);
$gateway_ip=$str1[0];

require_once(G_T("sys/ifconfig.htm"));

//G_T_F("footer.htm");
footer_info();
?>
