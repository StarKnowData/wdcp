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
//if ($wdcdn_gid!=1 or empty($_SESSION['admin'])) exit;


if (empty($mail_port)) $mail_port=25;


if (isset($_POST['Submit'])) {
	wdl_demo_sys();
	$mailsend=intval($_POST['mailsend']);
	$mail_server=chop($_POST['mail_server']);
	$mail_port=chop($_POST['mail_port']);
	$mail_auth=chop($_POST['mail_auth']);
	$mail_from=chop($_POST['mail_from']);
	$mail_auth_name=chop($_POST['mail_auth_name']);
	$mail_auth_passwd=chop($_POST['mail_auth_passwd']);
	
	config_update("mailsend",$mailsend,"邮件发送方式");
	config_update("mail_server",$mail_server,"smtp服务器");
	config_update("mail_port",$mail_port,"smtp端口");
	config_update("mail_auth",$mail_auth,"smpt服务器要求身份验证");
	config_update("mail_from",$mail_from,"发件人地址");
	config_update("mail_auth_name",$mail_auth_name,"smtp服务器验证用户名");
	config_update("mail_auth_passwd",$mail_auth_passwd,"smtp服务器验证密码");
	
	config_updatef();
	optlog($wdcp_uid,"修改了邮件设置",0,0);
	str_go_url("保存成功！",0);
}

if (isset($_POST['Submit_test'])) {
	$mail_from_t=chop($_POST['mail_from_t']);
	$mail_to_t=chop($_POST['mail_to_t']);
	mail_send($mail_to_t,$mail_from_t);
}
if (!@isset($mailsend)) {
	$mailsend=1;
	config_update("mailsend",$mailsend,"邮件发送方式");
	config_updatef();
}
if (empty($mail_server)) $mail_server="localhost";
if (empty($mail_auth)) $mail_auth=0;
if (empty($mail_from)) $mail_from="";
if (empty($mail_auth_name)) $mail_auth_name="";
if (empty($mail_auth_passwd)) $mail_auth_passwd="";
	
require_once(G_T("admin/mail.htm"));