<?php

/*
# WDlinux Control Panel,online management of Linux servers, virtual hosts
# Wdcdn system
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcdn
# Last Updated 2011.05
*/
exit;
require_once "../inc/common.inc.php";
//require_once WD_ROOT."/inc/userinfo.php";
//require_once "../login.php";
//require_once "../inc/page_class.php";
if ($is_reg==0) go_back("未开放注册");

if (isset($_POST['Submit_reg'])) {
	$name=chop($_POST['username']);
	$passwd=stripslashes(chop($_POST['passwd']));
	$ft=chop($_POST['ft']);
	$charge=chop($_POST['charge']);
	//if (empty($charge) or !is_numeric($charge)) go_back("带宽或流量输入错误");
	$xm=chop($_POST['xm']);
	$xb=chop($_POST['xb']);
	$sfzh=chop($_POST['sfzh']);
	$addr=chop($_POST['addr']);
	$tel=chop($_POST['tel']);
	$qq=chop($_POST['qq']);
	$email=chop($_POST['email']);
	check_user($name);
	check_passwd($passwd);
	//if (!empty($charge
	if (!empty($xm)) check_string($xm);
	$sfzh1=substr($sfzh,0,strlen($sfzh)-1);
	if (!empty($sfzh) and !is_numeric($sfzh1)) go_back("身份证输入有错");
	if (!empty($addr)) check_string($addr,"地址");
	if (!empty($tel)) check_string($tel,"电话");
	if (!empty($qq) and !is_numeric($qq)) go_back("QQ号输入有错");
	if (!empty($email)) check_email($email);
	$rtime=time();
	$lip=$_SERVER['REMOTE_ADDR'];
	$passwd=md5($passwd);
	$q=$db->query("select * from wd_member where name='$name'");
	if ($db->num_rows($q)!=0) go_back("该用户名已存在");
	$q=$db->query("insert into wd_member(name,passwd,xm,xb,sfzh,addr,tel,qq,email,rtime,state) values('$name','$passwd','$xm','$xb','$sfzh','$addr','$tel','$qq','$email','$rtime',2)");
	if (!$q) go_bac("保存失败!");
	else
		str_go_url("用户注册成功!","../index.php");
}
require_once(G_T("member/register.htm"));
?>
