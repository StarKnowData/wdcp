<?php

/*
# WDlinux Control Panel,online management of Linux servers, virtual hosts
# Wdcdn system
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcdn
# Last Updated 2011.05
*/

require_once "../inc/common.inc.php";
if (!isset($api_ip) or empty($api_ip)) exit;
$ip_list=explode(",",$api_ip);
$rip=$_SERVER['REMOTE_ADDR'];
//if ($api_ip!="null" and !in_array($rip,$ip_list)) exit;
if ($_GET['p']=="get") {
	if ($api_ip!="null" and !in_array($rip,$ip_list)) exit;
	$user=chop($_GET['user']);
	$api_key=chop($_GET['key']);
	$qq=chop($_GET['qq']);
	$tel=chop($_GET['tel']);
	$email=chop($_GET['email']);
	$xm=chop($_GET['xm']);
	$act=chop($_GET['act']);
}else {
	$user=chop($_POST['user']);
	$api_key=chop($_POST['key']);
	$qq=chop($_POST['qq']);
	$tel=chop($_POST['tel']);
	$email=chop($_POST['email']);
	$xm=chop($_POST['xm']);
	$act=chop($_POST['act']);
}
if ($api_key!==md5($user.$api_pass)) { echo "key err";exit;}//

//reg.php?act=user_ok&name=user1
if ($act=="user_ok") {
	//echo $user."\n";
	$q=$db->query("select * from wd_member where name='$user'");
	if ($db->num_rows($q)>0) echo 1;
	else echo 0;
	exit;
}elseif ($act=="create") {
	$pass=md5("wddns_2012");
	$rtime=time();//
	//$uid=check_exists_user($user,$pass);
	$q=$db->query("select * from wd_member where name='$user'");
	if ($db->num_rows($q)==0) {
		$db->query("insert into wd_member(name,passwd,qq,tel,xm,email,rtime) values('$user','$pass','$qq','$tel','$xm','$email','$rtime')");
		$iid=$db->insert_id();
		//return $iid;
		echo 1;
	}else{
		echo 0;
	}
	//if ($uid>0) echo 1;
	//else echo 0;
}else;


function check_exists_user($user,$pass) {
	global $db;
	$rtime=time();
	$q=$db->query("select * from wd_member where name='$user'");
	if ($db->num_rows($q)==0) {
		$db->query("insert into wd_member(name,passwd,rtime) values('$user','$pass','$rtime')");
		$iid=$db->insert_id();
		return $iid;
	}else{
		$r=$db->fetch_array($q);
		return $r['id'];
	}
}

function check_var($var,$msg) {
	if (!isset($var) or empty($var)) {echo $msg." err";exit;}
}

?>