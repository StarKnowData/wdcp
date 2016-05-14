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
require_once "../inc/admlogin.php";
if ($wdcp_gid!=1) exit;

//if ($wdcp_gid!=1 or empty($_SESSION['admin'])) exit;

if (isset($_POST['Submit'])) {
	//echo "aaaa<br>";
	$to=intval($_POST['to']);
	$users=$_POST['users'];
	$group=intval($_POST['group']);
	$subject=chop($_POST['subject']);
	$contents=chop($_POST['contents']);
	$mail_tp_select=intval($_POST['mail_tp_select']);
	//if (empty($to)) go_back("请选择收件人");
	if ($to==1 and empty($users)) go_back("请输入收件人用户名");
	//if (empty($users)) go_back("请输入收件人用户名");//
	//if (empty($group)) go_back("组ID有错");
	//if (empty($subject)) go_back("请输入主题");
	//if (empty($contents)) go_back("请输入内容");
	if ($mail_tp_select==0 and empty($subject)) go_back("请输入主题");
	if ($mail_tp_select==0 and empty($contents)) go_back("请输入内容");
	if ($mail_tp_select!=0) {
		$q=$db->query("select * from wd_mail_tp where mt='$mail_tp_select'");
		if ($db->num_rows($q)==0) go_back("邮件模板错误");
		$r=$db->fetch_array($q);
		$subject=$r['title'];
		$contents=$r['content'];
	}
	//echo "to:".$to."|users:".$users."|group:".$group."|subject:".$subject."|contents:".$contents."<br><br>";
	$tos=array();
	if ($to==1) {
		//echo "tos u||||||||||||<br>";
		//print_r(user_email($users));
		$tos=user_email($users);
	}elseif ($to==2) {
		//echo "tos g||||||||||||<br>";
		//print_r($tos=group_email($id));
		$tos=group_email($group);
	}else 
		go_back("请选择收件人！");
	//echo "tos||||||||||||<br>";
	//print_r($tos);
	//mail_submit($tos,$subject,$content);
	if (sizeof($tos)==0) go_back("用户或组错误，没有可发送地址");
	for ($i=0;$i<sizeof($tos);$i++) {
		//mail($tos[$i],$subject,$contents);
		if ($i!=0 and $i%3==0) sleep(3);
		if ($i!=0 and $i%10==0) sleep(5);
		if ($i!=0 and $i%30==0) sleep(15);
		$ncontents=str_replace("{username}",$tos[$i][0],$contents);
		//print_r($tos);
		//echo "<br>";
		//echo $tos[$i]."|<".$mail_from.">|".$subject."|".$ncontents;//continue;
		//mail_send($tos[$i][1],$mail_from,$subject,$ncontents);
		mail_send($tos[$i],'',$subject,$ncontents);
		//mail_send($tos[$i],$mail_from);
		//echo $tos[$i]."||44<br>";
	}
	//exit;
	optlog($wdcp_uid,"邮件发送 $tos",0,0);//exit;
	str_go_url("邮件发送成功!",0);
}
if (isset($_GET['user'])) {
	$isu='checked';
	$user=chop($_GET['user']);
}
$group_list=group_list();
$mail_tp_list=mail_tp_list();
require_once(G_T("member/email.htm"));
G_T_F("footer.htm");
?>
