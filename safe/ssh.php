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

$ssh_tmp=WD_ROOT."/data/tmp/ssh.txt";
$rcp_tmp=WD_ROOT."/data/tmp/rcp.txt";
//$ssh_tmp="/dev/shm/ssh.txt";

if (isset($_POST['Submit_chg'])) {
	$opasswd=chop($_POST['opasswd']);
	$npasswd=chop($_POST['npasswd']);
	$cnpasswd=chop($_POST['cnpasswd']);
	if (strcmp($npasswd,$cnpasswd)!=0) go_back("两次密码不对");
	$ssh_kchp=WD_ROOT."/data/tmp/ssh_kchp.txt";
	@file_put_contents($ssh_kchp,$opasswd."|".$npasswd."|".r_k_c());
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_ss.php",$str,$re);//print_r($str);print_r($re);
	if (@file_exists($ssh_kchp)) @unlink($ssh_kchp);
	if (@in_array("nk",$str)) str_go_url("还没生成密钥!","/safe/ssh.php");//go_back("还没生成密钥");
	elseif (@in_array("bad",$str)) go_back("原密码不对");
	else;
	optlog($wdcp_uid,"修改SSH私钥密码",0,0);//
	str_go_url("修改成功!",0);
}

if (isset($_GET['act']) and $_GET['act']=="chp") {
	require_once(G_T("safe/ssh_chp_k.htm"));
	//G_T_F("footer.htm");	
	footer_info();
	exit;
}

if (isset($_GET['act']) && $_GET['act']=="d") {
	$ssh_kt=WD_ROOT."/data/tmp/ssh_kt.txt";
	@file_put_contents($ssh_kt,r_k_c());
	//echo file_get_contents($ssh_kt);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_ss.php",$str,$re);//print_r($str);print_r($re);
	if (@file_exists($ssh_kt)) @unlink($ssh_kt);
	optlog($wdcp_uid,"下载SSH密钥文件",0,0);//
	$fp="/tmp/sksehy.txt";
	if (@file_exists($fp)) {
		$file = fopen($fp,"r"); // 打开文件
		// 输入文件标签
		Header("Content-type: application/octet-stream");
		Header("Accept-Ranges: bytes");
		Header("Accept-Length: ".filesize($fp));
		Header("Content-Disposition: attachment; filename=sshkey_wdcp");
		// 输出文件内容
		echo @fread($file,@filesize($fp));
		@fclose($file);
		@unlink($fp);
		exit();
	}else
		go_back("密钥文件不存在!确认是否已生成");
}

if (isset($_GET['act']) and $_GET['act']=="mk") {
	//echo "OK";exit;
	$ssh_mk=WD_ROOT."/data/tmp/ssh_mk.txt";
	@file_put_contents($ssh_mk,r_k_c());
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_ss.php",$str,$re);//print_r($str);print_r($re);
	if (@file_exists($ssh_mk)) @unlink($ssh_mk);
	optlog($wdcp_uid,"生成SSH密钥文件",0,0);//
	str_go_url("已生成!",0);
}

if (isset($_GET['act']) && $_GET['act']==="set") {
	//demo
	//go_back("演示系统对部分功能已做限制!");
	wdl_demo_sys();
	$v=chop($_GET['v']);
	$vv=chop($_GET['vv']);
	//$re=wdl_sudo_sys_ssh_set($v,$vv);
	$str="$v=$vv";//echo $str;
	@file_put_contents($ssh_tmp,$str);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_ss.php",$str,$re);//print_r($str);
	if (@file_exists($ssh_tmp)) @unlink($ssh_tmp);
	optlog($wdcp_uid,"设置ssh参数 $v",0,0);//
	if ($re==0)
		str_go_url("设置成功!",0);
	else
		go_back("设置错误!");
	exit;
}

if (isset($_POST['Submit'])) {
	//demo
	//go_back("演示系统对部分功能已做限制!");
	wdl_demo_sys();
	$port=chop($_POST['port']);
	if (!is_numeric($port)) {
		go_back("端口有误!");
		exit;
		}
	//$re=wdl_sudo_sys_ssh_set_port($port);
	$str="Port=$port";
	@file_put_contents($ssh_tmp,$str);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_ss.php",$str,$re);//print_r($str);
	if (@file_exists($ssh_tmp)) @unlink($ssh_tmp);
	optlog($wdcp_uid,"修改ssh服务端口 $port",0,0);//
	if ($re==0)
		str_go_url("修改成功!",0);
	else
		go_back("修改失败!");
	exit;
}

if (isset($_POST['Submit2'])) {
	exit;
	wdl_demo_sys();
	$p1=chop($_POST['pass1']);
	$p2=chop($_POST['pass2']);
	if (strcmp($p1,$p2)!=0) go_back("两次输入密码不同,请重新输入");
	check_passwd($p1);
	@file_put_contents($rcp_tmp,$p1);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_ss.php",$str,$re);//print_r($str);
	//echo file_get_contents($rcp_tmp);
	if (@file_exists($rcp_tmp)) @unlink($rcp_tmp);
	optlog($wdcp_uid,"修改root管理员密码",0,0);//
	if ($re==0)
		str_go_url("修改成功!",0);
	else
		go_back("修改失败!");
	exit;
	
}

//
/*
$str=wdl_sudo_sys_ssh_stat();
$msg=array();
//print_r($str);
for ($i=0;$i<sizeof($str);$i++) {
	$s1=explode(" ",str_replace("#","",$str[$i]));
	$msg[$s1[0]]=$s1[1];
}
*/

//@file_put_contents(WD_ROOT."/data/tmp/ssh.txt",1);
@touch($ssh_tmp);
exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sv.php",$str,$re);
if (@file_exists($ssh_tmp)) @unlink($ssh_tmp);
//print_r($str);print_r($re);//exit;
//str=>{port|PermitRootLogin|UseDNS}
//print_r($str);
$s1=explode("|",$str[0]);
//print_r($s1);
$port=chop($s1[0]);
//echo $s1[2];

//print_r($msg);
if (chop($s1[1])=="no")
	$rootl='否 <a href="'.$PHP_SELF.'?act=set&v=PermitRootLogin&vv=yes">允许</a>';
else
	$rootl='是 <a href="'.$PHP_SELF.'?act=set&v=PermitRootLogin&vv=no">禁止</a>';
if (chop($s1[2])=="no")
	$udns='禁用 <a href="'.$PHP_SELF.'?act=set&v=UseDNS&vv=yes">启用</a>';
else
	$udns='启用 <a href="'.$PHP_SELF.'?act=set&v=UseDNS&vv=no">禁用</a>';
if ($os_rl==2) $udns='';
if (chop($s1[3])=="no")
	$pl='否';
else
	$pl='是';
if (chop($s1[4])=="no")
	$pal='否 <a href="'.$PHP_SELF.'?act=set&v=PasswordAuthentication&vv=yes">启用</a>';
else
	$pal='是 <a href="'.$PHP_SELF.'?act=set&v=PasswordAuthentication&vv=no">关闭</a>';
require_once(G_T("safe/ssh.htm"));

//G_T_F("footer.htm");
footer_info();
?>
