<?php

/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/

require_once "../inc/common.inc.php";
if (!isset($api_ip) or empty($api_ip)) exit;
$ip_list=explode(",",$api_ip);
$rip=$_SERVER['REMOTE_ADDR'];
//if ($api_ip!="null" and !in_array($rip,$ip_list)) exit;
if ($api_ip!="null" and !in_array($rip,$ip_list)) exit;
$act=chop($_GET['act']);
$user=chop($_GET['user']);
$password=chop($_GET['passwd']);
$dbname=chop($_GET['dbname']);
$dbhost=chop($_GET['dbhost']);
if (empty($dbhost)) $dbhost="localhost";
$dbcharset=chop($_GET['dbcharset']);
if (empty($dbcharset)) $dbcharset="gbk";
if (!isset($_GET['dbsize'])) $dbsize=0;
else $dbsize=intval($_GET['dbsize']);
$quotasize=$dbsize;
if (!isset($_GET['sid'])) $sid=0;
else $sid=intval($_GET['sid']);
if (!isset($_GET['mid'])) $mid=0;
else $mid=intval($_GET['mid']);
if (!isset($_GET['type'])) $type=0;
else $type=intval($_GET['type']);
$api_key=chop($_GET['key']);
if (empty($wdcp_uid)) $wdcp_uid=0;
//if ($api_key!==md5($user.$api_pass)) { echo "key err";exit;}//
$rtime=time();
/*
add|edit|del|stop|start|passwd|list
username,password,dbname,quotasize,sid,mid,type
key=md5($user.$apk_pass);
type=0,user
type=1,db
*/

if ($act=="add") {
	//act=add&type=0&user=test1&passwd=123456&dbname=test1
	if ($type==0) {
		check_user($user,0,'',1);
		system_name_check($user,0,1);
		check_string($password,'',1);
		check_string($dbname,1);
		//echo "select * from wd_mysql where dbname='$dbname'";
		$q=$db->query("select * from wd_mysql where dbname='$dbname' and isuser=0");
		if ($db->num_rows($q)==0) dis_err("dbname is not exists");
		system_name_check($user,0,1);
		create_db_user($user,$password,$host,1);
		grant_db_user($user,$dbhost,$dbname,1);
		mysql_add_user($user,$password,$host,$dbname,$rtime,1);
		optlog($wdcp_uid,"增加mysql数据库用户 $user",0,0);
		echo "success";
	}elseif ($type==1){
		//echo "00";
		check_string($dbname,'',1);
		//echo "11";
		system_name_check($dbname,1,1);
		//echo "22";
		create_db($dbname,$dbcharset,1);
		mysql_add_db($uid,$sid,$dbname,$dbcharset,$quotasize,$rtime,1);
		optlog($wdcp_uid,"增加mysql数据库 $dbname ",0,0);
		echo "success";
	}elseif ($type==3){
		check_user($user,0,'',1);
		check_string($password,'',1);
		check_string($dbname,'',1);
		system_name_check($user,0,1);
		system_name_check($dbname,1,1);
		create_db($dbname,$dbcharset,1);
		create_db_user($user,$password,1);
		grant_db_user($user,$host,$dbname,1);
		mysql_add_db($uid,$sid,$dbname,$dbcharset,$quotasize,$rtime,1);
		mysql_add_user($user,$password,$host,$dbname,$rtime,1);
		//wd_mysql_add($uid,$sid,$user,$password,$host,$dbname,$dbcharset,$quotasize,$isuser,$rtime);
		optlog($wdcp_uid,"增加mysql数据库和用户 $dbname $user",0,0);	
		echo "success";
	}else echo "err";
}elseif ($act=="edit") {
	if ($type==0) {
		$q=$db->query("select * from wd_mysql where dbuser='$user' and isuser=1");
		if ($db->num_rows($q)==0) {echo "no user";exit;}
		mysql_user_edit($user,$user,$password,$dbhost,1);
		dbuser_chg_password($user,$password,$dbhost,1);
		optlog($wdcp_uid,"修改mysql数据库用户 $dbuser ",0,0);
		echo "success";
	}elseif ($type==1){
		//$dbname=chop($_POST['dbname']);
		//$dbsize=intval($_POST['dbsize']);
		mysql_db_size_edit($dbname,$quotasize,1);
		optlog($wdcp_uid,"修改mysql数据库 $dbname ",0,0);
		echo "success";
	}else echo "err";
}elseif ($act=="del") {
	if ($type==0) {
		$q=$db->query("select * from wd_mysql where dbuser='$user' and isuser=1");
		if ($db->num_rows($q)==0) {echo "no user";exit;}
		$r=$db->fetch_array($q);
		$dbuser=$r['dbuser'];
		$dbhost=$r['dbhost'];
		del_db_user($dbuser,$host,1);
		$db->query("delete from wd_mysql where dbuser='$user' and isuser=1");
		optlog($wdcp_uid,"删除数据库用户$user ",0,0);
		echo "success";
	}elseif ($type==1){
		$q=$db->query("select * from wd_mysql where dbname='$dbname' and isuser=0");
		if ($db->num_rows($q)==0) {echo "no dbname";exit;}
		drop_db($dbname,1);
		$db->query("delete from wd_mysql where dbname='$dbname' and isuser=0");
		optlog($wdcp_uid,"删除数据库$dbname ",0,0);	
		echo "success";
	}else echo "err";
}elseif ($act=="stop"){
	if ($type==0) {
	
	}elseif ($type==1){
	
	}else echo "err";
}elseif ($act=="start") {
	if ($type==0) {
	
	}elseif ($type==1){
	
	}else echo "err";
}elseif ($act=="passwd") {
	if ($type==0) {
		$query=$db->query("select * from wd_mysql where isuser!=0");
	}elseif ($type==1){
	
	}else echo "err";
}elseif ($act=="list") {
	if ($type==0) {
		$query=$db->query("select * from wd_mysql where isuser!=0");
		$msg="";
		while ($r=$db->fetch_array($query)) {
			$msg.=$r['id']."|".$r['dbuser']."|".$r['dbname']."|".$r['rtime']."|".$r['state']."|".$r['sid']."\n";
		}
		echo $msg;
	}elseif ($type==1){
		$query=$db->query("select * from wd_mysql where isuser=0");
		$msg="";
		while ($r=$db->fetch_array($query)) {
			$msg.=$r['id']."|".$r['dbname']."|".$r['dbsize']."|".$r['rtime']."|".$r['state']."|".$r['sid']."\n";
		}
		echo $msg;
	}else echo "err";
}else { echo "act err";exit;}
//echo "success";

?>