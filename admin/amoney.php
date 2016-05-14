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
//require_once "../inc/admlogin.php";
//echo $wdcdn_user."|".$wdcdn_uid."|".$wdcdn_gid;
//if ($wdcdn_gid!=1) exit;
if ($wdcp_gid>5) exit;
//if ($wdcdn_gid!=1 or empty($_SESSION['admin'])) exit;

if (isset($_POST['Submit'])) {
	$ot=intval($_POST['ot']);
	$id=chop($_POST['id']);
	$money=chop($_POST['money']);
	$note=chop($_POST['note']);
	//if (empty($id) or empty($note)) go_back("输入有误！");
	if (empty($id)) go_back("输入有误！");
	if (!is_numeric($money)) go_back("金额有错");
	//echo $ot."|".$id."|".$money;
	user_yb_money($id,$ot,$money);
	if ($ot==1) $actn="入款";//
	else	$actn="扣款";
	$title=uid_name($wdcp_uid)." $actn $money 元 ".$note;
	$title1=" $id $actn $money 元 ".$note;
	if (!is_numeric($id))
		$id=name_uid($id);
	$time=time();
	//buy_charge_log($id,$title,$money,0);
	//$db->query("insert into wd_dns_buylog(uid,pid,did,domain,money,state,rtime) values('$uid','$pid','$did','$domain','$price','$s','$rtime')");
	$db->query("insert into wd_dns_paylog(uid,money,note,rtime) values('$id','$money','$title',$time)");
	
	optlog($wdcp_uid,"用户ID $id $actn $money 元",0,0);
	str_go_url(" 用户ID $title1 成功!",0);
}
if (isset($_GET['uid'])) $uid=intval($_GET['uid']);
else $uid=$wdcp_uid;
require_once(G_T("admin/amoney.htm"));
?>