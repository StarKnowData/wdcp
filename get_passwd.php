<?php
/*
# WDlinux Control Panel,online management of Linux servers, virtual hosts
# Wdcdn system
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcdn
# Last Updated 2011.05
*/

require_once "inc/common.inc.php";
//require_once WD_ROOT."/inc/userinfo.php";
//require_once "../login.php";
//require_once "../inc/page_class.php";
if ($is_reg==0) {echo "未开放注册";exit;}//go_back("未开放注册");

if (isset($_POST['Submit_chg'])) {
	$t=intval($_POST['t']);
	$k=chop($_POST['k']);
	if (md5($t)!==$k) {echo "e1";exit;}
	$npasswd=chop($_POST['npasswd']);
	$cnpasswd=chop($_POST['cnpasswd']);
	if ($npasswd!==$cnpasswd) go_back("两次密码不一样");
	check_passwd($npasswd);
	$npasswd=md5($npasswd);
	$q=$db->query("select * from wd_member_gpw where state=0 and str='$k'");
	if ($db->num_rows($q)!=1) {str_go_url("非法操作","/");exit;}//go_back("非法操作");
	$r=$db->fetch_array($q);
	$uid=$r['uid'];
	$user=$r['name'];
	$db->query("update wd_member_gpw set state=1 where str='$k'");
	$q=$db->query("update wd_member set passwd='$npasswd' where id='$uid'");
	optlog($uid,"用户 $user 修改密码",0,0);//
	//echo "OK";exit;
	if (!$q) go_bac("保存失败!");
	else
		str_go_url("密码修改成功!","/");	
}

if (isset($_GET['k']) and isset($_GET['t'])) {
	$k=chop($_GET['k']);
	$t=intval($_GET['t']);
	if (md5($t)!==$k) {echo "e1";exit;}
	//if (strlen($k)!=32) {echo "e2";exit;}
	//echo "OK";
	require_once(G_T("member/chgpasswd_n.htm"));
	G_T_F("footer.htm");
	exit;
}


if (isset($_POST['Submit_gpw'])) {
	$username=chop($_POST['username']);
	$ckcode=intval($_POST['ckcode']);
	$is_ck=1;
	check_ckcode($ckcode);

	if (eregi("@",$username)){
		check_email($username);
		$q=$db->query("select * from wd_member where email='$username'");
	}else{
		check_user($username);
		$q=$db->query("select * from wd_member where name='$username'");
	}	
	if ($db->num_rows($q)!=1) {
		loginfailed($username,$passwd,0,0);
		str_go_url("用户或邮箱不存在！",0);
		exit;
	}
	$r=$db->fetch_array($q);
	if (empty($r['email'])) go_back("用户邮箱不存在,请联系管理员");
	//$email=$r['email'];
	$uid=$r['id'];
	$ct=time();
	$str=md5($ct);
	$db->query("insert into wd_member_gpw(uid,str,rtime) values('$uid','$str','$ct')");
	$title="取回密码";
	if ($_SERVER["SERVER_PORT"]==80)
		$url="http://".$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"]."?t=".$ct."&k=".$str;
	else
		$url="http://".$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["SCRIPT_NAME"]."?t=".$ct."&k=".$str;
	$contents="点击如下连接,修改密码\n $url";
	//echo $r['email']."|".$contents;exit;
	mail_send($r['email'],'',$title,$contents);//

	str_go_url("密码取回成功,请检查邮箱以及时修改密码","../index.php");
}
//$rid=isset($_GET['rid'])?intval($_GET['rid']):'';
$lc=@login_validation();
require_once(G_T("member/get_passwd.htm"));
?>
