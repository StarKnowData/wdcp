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

$iptables_tmp=WD_ROOT."/data/tmp/iptables.txt";
$iptablesa_tmp=WD_ROOT."/data/tmp/iptablesa.txt";
$iptablesd_tmp=WD_ROOT."/data/tmp/iptablesd.txt";
$iptabless_tmp=WD_ROOT."/data/tmp/iptabless.txt";

if (isset($_GET['act']) and $_GET['act']=="on") {
	$re=wdl_sudo_sys_iptables("on");
	//print_r($str);echo $re;exit;
	optlog($wdcp_uid,"开启iptables防火墙",0,0);//
	if ($re==0)
		str_go_url("开启成功并启动!",0);
	else
		go_back("开启错误!");
	exit;
}

if (isset($_GET['act']) and $_GET['act']=="del") {
	//demo
	//go_back("演示系统对部分功能已做限制!");
	wdl_demo_sys();
	$str=base64_decode(chop($_GET['str']));
	//echo $str;echo "<br>";//exit;
	//$re=wdl_sudo_sys_iptables_del("$str");
	//print_r($str);echo $re;exit;
	//echo $str;
	@file_put_contents($iptablesd_tmp,$str);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);exit;
	if (@file_exists($iptablesd_tmp)) @unlink($iptablesd_tmp);
	optlog($wdcp_uid,"删除iptables规则",0,0);//
	if ($re==0)
		str_go_url("删除成功!",0);
	else
		go_back("删除失败!");
	exit;
}
if (isset($_GET['act']) and $_GET['act']=="save") {
	//demo
	//go_back("演示系统对部分功能已做限制!");
	wdl_demo_sys();
	//$re=wdl_sudo_sys_iptables("save");
	//optlog($wdcp_uid,"保存iptables设置",0,0);//
	@touch($iptabless_tmp);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);
	if (@file_exists($iptabless_tmp)) @unlink($iptabless_tmp);
	if ($re==0)
		str_go_url("保存成功!",0);
	else
		go_back("备份失败!");
	exit;
}
if (isset($_GET['act']) and $_GET['act']=="backup") {
	//demo
	//go_back("演示系统对部分功能已做限制!");
	wdl_demo_sys();
	$re=wdl_sudo_sys_iptables("backup");
	optlog($wdcp_uid,"备份iptables设置",0,0);//
	if ($re==0)
		str_go_url("备份成功!",0);
	else
		go_back("备份失败!");
	exit;
}

if (isset($_POST['Submit_add'])) {
	$msg="-I INPUT";
	wdl_demo_sys();
	//iptables -I INPUT -p tcp -s 192.168.1.1 -j ACCEPT
	$msg.=" -p ".chop($_POST['pro']);
	if (!empty($_POST['sip']))
		$msg.=" -s ".chop($_POST['sip']);
	if (!empty($_POST['sport']))
		$msg.=" --sport ".chop($_POST['sport']);
	if (!empty($_POST['dip']))
		$msg.=" -d ".chop($_POST['dip']);
	if (!empty($_POST['dport']))
		$msg.=" --dport ".chop($_POST['dport']);
	$msg.=" -j ".chop($_POST['act']);
	//echo $msg;
	//$re=wdl_sudo_sys_iptables_set("$msg");
	@file_put_contents($iptablesa_tmp,$msg);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);
	if (@file_exists($iptablesa_tmp)) @unlink($iptablesa_tmp);
	optlog($wdcp_uid,"增加iptables规则",0,0);
	if ($re==0)
		str_go_url("增加成功!",0);
	else
		go_back("增加失败!");
	exit;
		
}

if (isset($_GET['act']) and $_GET['act']=="restore") {

}

//$str=wdl_sudo_sys_iptables_stat();
//print_r($str);echo $re;
@touch($iptables_tmp);
exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);

if (@file_exists($iptables_tmp)) @unlink($iptables_tmp);
$re=end($str);
$str=preg_replace("/-A RH-Firewall-1-INPUT|-A INPUT|-m state|-m tcp|--state NEW/isU","",$str);
//print_r($str);//
//echo sizeof($str);exit;

	//echo $pro[0]."|".$sip[0]."|".$sport[0]."|".$dip[0]."|".$dport[0]."|".$accept;
	//echo "<br>\n";
	//exit;
//}
//exit;

$list=array();
  for ($i=0;$i<sizeof($str);$i++) {
	//echo $str[$i]."<br>\n";
	//-A RH-Firewall-1-INPUT -m state --state NEW -m tcp -p tcp --dport 21 -j ACCEPT
	$link="/";
	//preg_match("/--sport\s\d+\s/isU",$str[$i],$sport);
	preg_match("/--sport\s(\d+|\d+:\d+)\s/isU",$str[$i],$sport);
	if (empty($sport[0])) $sport="不限";
	else{
		$link.=chop($sport[0])."/&&/";	
		$sport=str_replace("--sport ","",$sport[0]);
	}
	//preg_match("/--dport\s\d+\s/isU",$str[$i],$dport);
	preg_match("/--dport\s(\d+|\d+:\d+)\s/isU",$str[$i],$dport);
	if (empty($dport[0])) $dport="不限";
	else{
		$link.=chop($dport[0])."/&&/";
		$dport=str_replace("--dport ","",$dport[0]);
	}
	preg_match("/-p\s\w+\s/isU",$str[$i],$pro);
	$link.=chop($pro[0])."/&&/";
	$pro=str_replace("-p ","",$pro[0]);
	//preg_match("/-s\s\d+\.\d+\.\d+\.\d+\s/isU",$str[$i],$sip);print_r($sip);exit;
	//preg_match("/-s\s\d+\.\d+\.\d+\.\d+[\s|\/\d+\.\d+\.\d+\.\d+|\/\d+]/isU",$str[$i],$sip);print_r($sip);exit;
	preg_match("/-s\s(.*) /isU",$str[$i],$sip);//print_r($sip);exit;
	if (empty($sip[0])) $sip="不限";
	else{
		//$link.=chop($sip[0])."/&&/";
		$link.=chop(preg_replace("/\/(.*)/im","",$sip[0]))."/&&/";
		$sip=str_replace("-s ","",$sip[0]);
	}
	//preg_match("/-d\s\d+\.\d+\.\d+\.\d+\s/isU",$str[$i],$dip);
	preg_match("/-d\s(.*) /isU",$str[$i],$dip);
	if (empty($dip[0])) $dip="不限";
	else{
		//$link.=chop($dip[0])."/&&/";
		$link.=chop(preg_replace("/\/(.*)/im","",$dip[0]))."/&&/";
		$dip=str_replace("-d ","",$dip[0]);
	}
	if (eregi("-j ACCEPT",$str[$i])) $accept="允许";
	if (eregi("-j DROP",$str[$i])) $accept="拒绝";
	//echo $link;
	
	$opt=$PHP_SELF."?act=del&str=".base64_encode(substr($link,0,strlen($link)-3));
	
	$list[$i]['id']=$i;
	$list[$i]['pro']=$pro;
	$list[$i]['sip']=$sip;
	$list[$i]['sport']=$sport;
	$list[$i]['dip']=$dip;
	$list[$i]['dport']=$dport;
	$list[$i]['accept']=$accept;
	$list[$i]['href']=$opt;

}
if ($re==12) $no_use='<tr>
    <td height="22" colspan="8">&nbsp;iptables没有开启!&nbsp;&nbsp;&nbsp;<a href="'.$PHP_SELF.'?act=on">开启</a></td>
  </tr>';

if (isset($_GET['act']) && $_GET['act']==="add") {
	require_once(G_T("safe/iptables_add.htm"));
}

require_once(G_T("safe/iptables.htm"));

//G_T_F("footer.htm");
footer_info();

?>
