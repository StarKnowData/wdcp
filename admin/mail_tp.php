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


if (isset($_POST['Submit_add'])) {
	$id=intval($_POST['id']);
	$mt=intval($_POST['mt']);
	$title=chop($_POST['mail_title']);
	$content=chop($_POST['mail_content']);
	$rtime=time();
	if (empty($id)) {
		$q=$db->query("insert into wd_mail_tp(mt,title,content,rtime) values('$mt','$title','$content','$rtime')");
		optlog($wdcp_uid,"增加邮件模板",0,0);
	}else{
		$q=$db->query("update wd_mail_tp set mt='$mt',title='$title',content='$content' where id='$id'");
		optlog($wdcp_uid,"修改邮件模板",0,0);
	}
	str_go_url("保存成功！",0);	
}


if (isset($_GET['act']) and $_GET['act']=="add") {
	require_once(G_T("admin/mail_tp_content.htm"));
	exit;
}

if (isset($_GET['act']) and $_GET['act']=="edit") {
	$id=intval($_GET['id']);
	$q=$db->query("select * from wd_mail_tp where id='$id'");
	if ($db->num_rows($q)==0) go_back("ID错误");
	$r=$db->fetch_array($q);
	$mail_title=$r['title'];
	$mail_content=$r['content'];
	$mt=$r['mt'];
	require_once(G_T("admin/mail_tp_content.htm"));
	exit;	
}

if (isset($_GET['act']) and $_GET['act']=="edit") {
	$id=intval($_GET['id']);
	$q=$db->query("select * from wd_mail_tp where id='$id'");
	if ($db->num_rows($q)==0) go_back("ID错误");
	$db->query("delete from wd_mail_tp where id='$id'");
	optlog($wdcp_uid,"修改邮件模板",0,0);
	str_go_url("删除成功！",0);	
}


$pagenum=20;
if (!isset($_GET['page'])) $start=0;
else	$start=(intval($_GET['page'])-1)*$pagenum;
if ($start<0) $start=0;
$sum=sum_result("select * from wd_mail_tp");
//$sum=$db->num_rows($query);
$query=$db->query("select * from wd_mail_tp order by id desc limit $start,$pagenum");

$list=array();
$i=0;
while ($r=$db->fetch_array($query)) {
	$list[$i]['id']=$r['id'];
	$list[$i]['title']=$r['title'];
	$list[$i]['content']=str_replace("\n","<br>",$r['content']);
	$list[$i]['rtime']=date("Y-m-d H:i",$r['rtime']);
	$i++;
}
$page=new page(array('total'=>$sum,'perpage'=>$pagenum));
$pagelist=$page->show();
require_once(G_T("admin/mail_tp_list.htm"));
?>
