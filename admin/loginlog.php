<?php

/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/

require_once "../inc/common.inc.php";
require_once "../login.php";
require_once "../inc/admlogin.php";
if ($wdcp_gid!=1) exit;

$q=$db->query("select * from wd_loginlog order by id desc limit 30");
$i=0;
$list=array();
while ($r=$db->fetch_array($q)) {
	$list[$i]['id']=$r['id'];
	$list[$i]['name']=$r['name'];
	if (empty($r['passwd']))
		$list[$i]['passwd']="******";
	else
		$list[$i]['passwd']=$r['passwd'];
	$list[$i]['lip']=$r['lip'];
	$list[$i]['time']=date("Y-m-d H:i",$r['ltime']);
	if ($r['state']==0)
		$list[$i]['state']="³É¹¦";
	else
		$list[$i]['state']="Ê§°Ü";
	$i++;
}
require_once(G_T("admin/loginlog.htm"));
//G_T_F("footer.htm");
footer_info();
?>