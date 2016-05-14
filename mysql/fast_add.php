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


if (isset($_POST['Submit_add'])) {
	$user=chop($_POST['user']);
	$password=chop($_POST['password']);
	$dbname=chop($_POST['dbname']);
	if (empty($dbname)) $dbname=$user;//
	$dbcharset=chop($_POST['dbcharset']);
	$quotasize=chop($_POST['quotasize']);
	$sid=intval($_POST['sid']);
	$uid=intval($_POST['mid']);
	if ($uid==0) $uid=$wdcp_uid;

	if ($wdcp_gid!=1) {
		$q1=$db->query("select * from wd_group where id='$wdcp_gid'");
		$r1=$db->fetch_array($q1);
		$mysqlc=$r1['mysql'];
		$q2=$db->query("select * from wd_mysql where uid='$wdcp_uid' and isuser=0");
		if ($db->num_rows($q2)>=$mysqlc and $mysqlc>0) go_back("可创建mysql数据库数超出限制,请联系管理员");
	}

	check_user($user);
	check_passwd($password);
	//check_string($password);
	check_string($dbname);
	system_name_check($user,0);
	system_name_check($dbname,1);
	create_db($dbname,$dbcharset);
	create_db_user($user,$password);
	grant_db_user($user,$host,$dbname);
	mysql_add_db($uid,$sid,$dbname,$dbcharset,$quotasize,$rtime);
	mysql_add_user($user,$password,$host,$dbname,$rtime);
	//wd_mysql_add($uid,$sid,$user,$password,$host,$dbname,$dbcharset,$quotasize,$isuser,$rtime);
	optlog($wdcp_uid,"增加mysql数据库和用户 $dbname $user",0,0);
	str_go_url("增加成功!",0);
}
$member_list=member_list();
$site_list=site_list();
unset($user,$password);
require_once(G_T("mysql/fast_add.htm"));
//G_T_F("footer.htm");
footer_info();
?>
