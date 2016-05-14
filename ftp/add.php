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
//if ($wdcp_gid!=1) exit;//


if (isset($_POST['Submit_add'])) {
	$user=chop($_POST['username']);
	$password=stripslashes(chop($_POST['passwd']));
	$password2=stripslashes(chop($_POST['passwd2']));
	if (strcmp($password,$password2)!=0) go_back("两次密码不同,请确认输入");
	$vhost_dir=chop($_POST['dir']);
	$quotasize=intval($_POST['quotasize']);
	$quotafiles=intval($_POST['quotafiles']);
	$ulbandwidth=intval($_POST['ulbandwidth']);
	$dlbandwidth=intval($_POST['dlbandwidth']);
	$sid=intval($_POST['sid']);
	$mid=intval($_POST['mid']);
	if ($mid==0) $mid=$wdcp_uid;

	//
	if ($wdcp_gid!=1) {
		$q1=$db->query("select * from wd_group where id='$wdcp_gid'");
		$r1=$db->fetch_array($q1);
		$ftpc=$r1['ftp'];
		$q2=$db->query("select * from wd_ftp where mid='$wdcp_uid'");
		if ($db->num_rows($q2)>=$ftpc and $ftpc>0) go_back("可创建ftp数超出限制,请联系管理员");
		if (@is_dir($vhost_dir)) go_back("普通用户不能创建已存在目录的FTP用户");
	}

	check_user($user);
	check_passwd($password);
	//check_string($password);
	check_user_ftp($user);
	//echo "11";
	if (empty($vhost_dir)) $vhost_dir=$user;
	//echo $vhost_dir;
	if (substr($vhost_dir,0,1)=="/")
		$wvhost_dir=str_replace(".","_",$vhost_dir);
	else
		$wvhost_dir=$web_home."/".str_replace(".","_",$vhost_dir);
	//echo $wvhost_dir."|<br>";
	wdl_vhostdir_check($wvhost_dir);
	//echo "11";
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
	//echo "11";
	//echo $wvhost_dir;//exit;
	//is_dir_check($wvhost_dir);
	$npassword=md5($password);
	ftp_user_add($sid,$mid,$user,$npassword,$wvhost_dir,$quotasize,$quotafiles,$ulbandwidth,$dlbandwidth);//
	optlog($wdcp_uid,"增加FTP帐号 $user",0,0);//
	str_go_url("FTP帐号增加成功!",0);
}
$member_list=member_list();
$site_list=site_list();
require_once(G_T("ftp/add.htm"));
//G_T_F("footer.htm");
footer_info();
?>
