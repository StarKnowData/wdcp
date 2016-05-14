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
if ($api_ip!="null" and !in_array($rip,$ip_list)) exit;
//echo "11";
$act=chop($_GET['act']);
$user=chop($_GET['user']);
$password=chop($_GET['passwd']);
$vhost_dir=chop($_GET['dir']);
if (!isset($_GET['quotasize'])) $quotasize=0;
else $quotasize=intval($_GET['quotasize']);
if (!isset($_GET['quotafiles'])) $quotafiles=0;
else $quotafiles=intval($_GET['quotafiles']);
if (!isset($_GET['ulbandwidth'])) $ulbandwidth=0;
else $ulbandwidth=intval($_GET['ulbandwidth']);
if (!isset($_GET['dlbandwidth'])) $dlbandwidth=0;
else $dlbandwidth=intval($_GET['dlbandwidth']);
if (!isset($_GET['sid'])) $sid=0;
else $sid=intval($_GET['sid']);
if (!isset($_GET['mid'])) $mid=0;
else $mid=intval($_GET['mid']);
$api_key=chop($_GET['key']);
if (empty($wdcp_uid)) $wdcp_uid=0;
if ($api_key!==md5($user.$api_pass)) { echo "key err";exit;}//
/*
add|edit|del|stop|start|passwd|list
username,password,dir,quotasize,quotafiles,ulbandwidth,dlbandwidth,sid,mid,
key=md5($user.$apk_pass);
*/

if ($act=="add") {
	//act=add&user=test1&passwd=12345&dir=/home/web/test1&quotasize=100&quotafiles500&ulbandwidth=30&dlbandwidth=50;
	check_user($user,0,'',1);
	check_string($password,'',1);
	check_user_ftp($user,1);
	if (empty($vhost_dir)) $vhost_dir=$user;
	if (substr($vhost_dir,0,1)=="/")
		$wvhost_dir=str_replace(".","_",$vhost_dir);
	else
		$wvhost_dir=$web_home."/".str_replace(".","_",$vhost_dir);
	//echo $wvhost_dir."|<br>";
	wdl_vhostdir_check($wvhost_dir,1);
	if ($sid==0)
		is_dir_check($wvhost_dir);
	else{
		$tdir=sid_to_dir($sid);
		//echo $tdir;
		if (!empty($tdir))
			$wvhost_dir=$tdir;
		//else
			//echo "oo";
	}
	//echo $wvhost_dir;exit;
	//is_dir_check($wvhost_dir);
	$npassword=md5($password);
	ftp_user_add($sid,$mid,$user,$npassword,$wvhost_dir,$quotasize,$quotafiles,$ulbandwidth,$dlbandwidth,1);//
	optlog($wdcp_uid,"Ôö¼ÓFTPÕÊºÅ $user",0,0);//
}elseif ($act=="edit") {
	//act=edit&user=test1&passwd=123456&dir=/home/web/test1
	$q=$db->query("select * from wd_ftp where user='$user'");
	if ($db->num_rows($q)==0) {echo "no user";exit;}
	if (strlen($password)<32) {
		check_string($password,1);
		$password=md5($password);
	}else
		$password="";
	ftp_user_chg($user,$password,$quotasize,$quotafiles,$ulbandwidth,$dlbandwidth,1);
	optlog($wdcp_uid,"ÐÞ¸ÄFTPÕÊºÅ $user",0,0);
}elseif ($act=="del") {
	$q=$db->query("select * from wd_ftp where user='$user'");
	if ($db->num_rows($q)==0) {echo "no user";exit;}
	$r=$db->fetch_array($q);
	$q=$db->query("delete from wd_ftp where user='$user'");
	if ($ftp_dir_del_is==1 and !eregi("public_html",$r['dir'])) rmdir($r['dir']);
	optlog($wdcp_uid,"É¾³ýFTPÕÊºÅ $user ",0,0);
}elseif ($act=="stop") {
	$q=$db->query("select * from wd_ftp where user='$user'");
	if ($db->num_rows($q)==0) {echo "no user";exit;}
	$q=$db->query("update wd_ftp set status=1 where user='$user'");
	optlog($wdcp_uid,"¹Ø±ÕFTPÕÊºÅ $user ",0,0);
}elseif ($act=="start") {
	$q=$db->query("select * from wd_ftp where user='$user'");
	if ($db->num_rows($q)==0) {echo "no user";exit;}
	$q=$db->query("update wd_ftp set status=0 where user='$user'");
	optlog($wdcp_uid,"¹Ø±ÕFTPÕÊºÅ $user ",0,0);
}elseif ($act=="passwd") {
	$q=$db->query("select * from wd_ftp where user='$user'");
	if ($db->num_rows($q)==0) {echo "no user";exit;}
	$npass=md5($password);
	$q=$db->query("update wd_ftp set password='$npass' where user='$user'");
	optlog($wdcp_uid,"ÐÞ¸ÄFTPÕÊºÅ $user ÃÜÂë ",0,0);
}elseif ($act=="list"){
	$query=$db->query("select * from wd_ftp");
	$msg="";
	while ($r=$db->fetch_array($query)) {
		$msg.=$r['id']."|".$r['user']."|".$r['password']."|".$r['dir']."|".$r['quotasize']."|".$r['quotafiles']."|".$r['ulbandwidth']."|".$r['dlbandwidth']."|".$r['rtime']."|".$r['mid']."|".$r['status']."|".$r['sid']."\n";
	}
	echo $msg;exit;
}else{ echo "act err";exit;}
echo 'success';
?>