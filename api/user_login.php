<?php

/*
# WDlinux Control Panel,online management of Linux servers, virtual hosts
# Wdcdn system
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.05
*/

require_once "../inc/common.inc.php";
if (!isset($api_ip) or empty($api_ip)) exit;
$ip_list=explode(",",$api_ip);
$rip=$_SERVER['REMOTE_ADDR'];
//echo $rip;
//print_r($ip_list);
if ($_GET['p']=="get") {
	if ($api_ip!="null" and !in_array($rip,$ip_list)) exit;
	$user=chop($_GET['user']);
	$api_key=chop($_GET['key']);
	$type=chop($_GET['type']);
	$domain=chop($_GET['domain']);
	$tact=chop($_GET['tact']);
}else{
	$user=chop($_POST['user']);
	$api_key=chop($_POST['key']);	
	$type=chop($_POST['type']);
	$domain=chop($_POST['domain']);
	$tact=chop($_POST['tact']);
}
if (empty($user)) { echo "user empty";exit;}
if (empty($api_key)) {echo "key empty";exit;}
if (empty($api_pass)) { echo "no key";exit;}
if ($api_key!==md5($user.$api_pass)) { echo "key err";exit;}//

//
$q=$db->query("select * from wd_member where name='$user'");
if ($db->num_rows($q)!=1) {echo "user err";exit;}
$r=$db->fetch_array($q);
loginlog($user,0,0);

$q1=$db->query("select * from wd_group where id='$r[gid]'");
if ($db->num_rows($q1)==0) 
	if ($r['gid']==1) $r1['level']=1;
	else $r['level']=10;
else
	$r1=$db->fetch_array($q1);
if (empty($r1['level'])) $r1['level']=$r['gid'];

setcookie('wdcp_user',$user,time() + $cookie_time,'/');
setcookie('wdcp_uid',$r['id'],time() + $cookie_time,'/');
//setcookie('wdcp_gid',$r['gid'],time() + $cookie_time,'/');
setcookie('wdcp_gid',$r1['level'],time() + $cookie_time,'/');
setcookie('wdcp_ggid',$r['gid'],time() + $cookie_time,'/');
setcookie('wdcp_us',$r['state'],time() + $cookie_time,'/');

$wdcp_user=$r['name'];
$wdcp_uid=$r['id'];
//$wdcp_gid=$r['gid'];
$wdcp_gid=$r1['level'];
$wdcp_ggid=$r['gid'];
$wdcp_us=$r['state'];
$wdcp_lt=user_l_check(0);
//setcookie('wdcp_lt',$wdcp_lt,time() + $cookie_time,'/');
session_start();
unset($_SESSION['is_l']);
$_SESSION['is_l']=$wdcp_lt;
//last_login($user);

//$type=chop($_GET['type']);
//$domain=chop($_GET['domain']);
//$tact=chop($_GET['tact']);


if ($type=="dns" and !empty($domain)) {
	if ($tact=="add")
		$turl="/dns/domain_list.php?act=view&domain=$domain&acts=add";
	else
		$turl="/dns/domain_list.php?act=view&domain=$domain";
	$_SESSION['turl']=$turl;
}

//go_url("../index.php");
//go_url($turl);
go_url("../index.php");
//
?>