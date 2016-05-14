<?php

/*
# WDlinux Control Panel,online management of Linux servers, virtual hosts
# Wdcdn system
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcdn
# Last Updated 2011.05
*/

require_once "../inc/common.inc.php";
require_once "../inc/page_class.php";
require_once "../login.php";

if (isset($_POST['Submit_chg'])) {
	wdl_demo_sys();
	$opasswd=stripslashes(chop($_POST['opasswd']));
	$npasswd=stripslashes(chop($_POST['npasswd']));
	$cnpasswd=chop($_POST['cnpasswd']);
	if ($npasswd!==$cnpasswd) go_back("两次密码不一样");
	check_passwd($npasswd);
	//check_passwd($cnpasswd);
	$opasswd=md5($opasswd);
	$npasswd=md5($npasswd);
	//$q=$db->query("select * from wd_member where name='$wdcp_user' and passwd='$opasswd'");
	//echo $wdcp_uid."|".$opasswd."<br>";
	//echo "select * from wd_member where id='$wdcp_uid' and passwd='$opasswd'";exit;
	$q=$db->query("select * from wd_member where id='$wdcp_uid' and passwd='$opasswd'");
	if ($db->num_rows($q)==0) go_back("用户名或密码错误!");
	$r=$db->fetch_array($q);
	$user=$r['name'];
	$q=$db->query("update wd_member set passwd='$npasswd' where id='$wdcp_uid'");
	optlog($wdcp_uid,"修改用户 $user 密码",0,0);//
	if (!$q) go_bac("保存失败!");
	else
		str_go_url("密码修改成功!",0);	
}
//require_once WD_ROOT."/templates/member/chgpasswd.htm";
require_once(G_T("member/chgpasswd.htm"));
G_T_F("footer.htm");
?>
