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
	$id=intval($_POST['id']);
	$dbname=chop($_POST['dbname']);
	$dbsize=intval($_POST['dbsize']);
	if ($wdcp_gid!=1) go_back("无权修改，请联系管理员!");
	//check_user($user);
	//check_string($password);
	//check_string($dbname);
	//create_db($dbname,$dbcharset);
	//create_db_user($user,$password);
	//grant_db_user($user,$host,$dbname);
	//wd_mysql_add($uid,$sid,$user,$password,$host,$dbname,$dbcharset,$quotasize,$isuser,$rtime)
	mysql_db_size_edit($id,$dbsize);
	optlog($wdcp_uid,"修改mysql数据库 $dbname ",0,0);
	str_go_url("修改成功!",'db_list.php');
}
$id=intval($_GET['id']);
if (!is_numeric($id)) go_back("ID错误");
if ($wdcp_gid==1)
	$q=$db->query("select * from wd_mysql where id='$id'");
else
	$q=$db->query("select * from wd_mysql where uid='$wdcp_uid' and id='$id'");
if ($db->num_rows($q)==0) go_back("数据库不存在!");
$r=$db->fetch_array($q);
$id=$r['id'];
$dbname=$r['dbname'];
$dbsize=$r['dbsize'];
//$dbchar=$r['dbchar'];
require_once(G_T("mysql/db_edit.htm"));
//G_T_F("footer.htm");
footer_info();
?>
