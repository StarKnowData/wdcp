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
	$sid=intval($_POST['sid']);
	$password=chop($_POST['password']);
	$dbname=chop($_POST['dbname']);
	$host=chop($_POST['host']);
	if ($dbname=="0") go_back("选择的数据库有错!");
	check_user($user);
	//system_name_check($user,0);
	//check_string($password);
	check_passwd($password);
	check_string($dbname);
	system_name_check($user,0);
	//check_exists_dbname($dbname);
	//system_name_check($dbname,1);
	create_db_user($user,$password,$host);
	grant_db_user($user,$host,$dbname);
	mysql_add_user($user,$password,$host,$dbname,$rtime);
	optlog($wdcp_uid,"增加mysql数据库用户 $user",0,0);
	str_go_url("数据库用户增加成功!",0);
}
$member_list=member_list();
$site_list=site_list();
$db_list=db_list();
//print_r($db_list);
require_once(G_T("mysql/add_user.htm"));
//G_T_F("footer.htm");
footer_info();
?>
