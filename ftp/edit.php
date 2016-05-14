<?php

/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/

require_once "../inc/common.inc.php";
require_once "../login.php";
//require_once "../inc/admlogin.php";
//if ($wdcp_gid!=1) exit;


if (isset($_POST['Submit_edit'])) {
	//print_r($_POST);
	$id=intval($_POST['id']);
	$user=chop($_POST['user']);
	$sid=intval($_POST['sid']);
	$password=chop($_POST['password']);
	//$vhost_dir=chop($_POST['dir']);
	$quotasize=intval($_POST['quotasize']);
	$quotafiles=intval($_POST['quotafiles']);
	$ulbandwidth=intval($_POST['ulbandwidth']);
	$dlbandwidth=intval($_POST['dlbandwidth']);
	if ($wdcp_gid!=1) go_back("无权修改，请联系管理员");
	//echo $_POST['ulbandwidth']."|".$_POST['dlbandwidth']."<br>";
	//echo $ulbandwidth."|".$dlbandwidth;
	//check_user($user);
	if (strlen($password)<32) {
		//check_string($password);
		check_passwd($password);
		$password=md5($password);
	}else
		$password="";
	//echo $password;

	//ftp_user_add($user,$password,$vhost_dir,$quotasize);
	ftp_user_chg($id,$password,$quotasize,$quotafiles,$ulbandwidth,$dlbandwidth);
	if ($sid>0)
		$db->query("update wd_ftp set sid='$sid' where id='$id'");
	optlog($wdcp_uid,"修改FTP帐号 $user",0,0);
	str_go_url("FTP帐号修改成功!","list.php");
}
$id=intval($_GET['id']);
if (!is_numeric($id)) go_back("ID错误");
if ($wdcp_gid==1)
	$q=$db->query("select * from wd_ftp where id='$id'");
else
	$q=$db->query("select * from wd_ftp where mid='$wdcp_uid' and id='$id'");
if ($db->num_rows($q)==0) go_back("FTP帐号不存在!");
$r=$db->fetch_array($q);
$user=$r['user'];
$password=$r['password'];
$dir=$r['dir'];
$quotasize=$r['quotasize'];
$quotafiles=$r['quotafiles'];
$ulbandwidth=$r['ulbandwidth'];
$dlbandwidth=$r['dlbandwidth'];
$id=$r['id'];
$site_list=site_list($r['sid']);
require_once(G_T("ftp/edit.htm"));
//G_T_F("footer.htm");
footer_info();
?>
