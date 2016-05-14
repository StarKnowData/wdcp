<?php

/*
# WDlinux Control Panel,online management of Linux servers, virtual hosts
# Wdcdn system
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcdn
# Last Updated 2011.05
*/

require_once "../inc/common.inc.php";
require_once "../login.php";
require_once "../inc/admlogin.php";
if ($wdcp_gid!=1) exit;
//if ($wdcp_gid!=1 or empty($_SESSION['admin'])) exit;

$pagenum=20;
if (!isset($_GET['page'])) $start=0;
else	$start=(intval($_GET['page'])-1)*$pagenum;
if ($start<0) $start=0;
//$sum=sum_result("select * from wd_optlog");
$query=$db->query("select * from wd_tasklog");
$sum=$db->num_rows($query);
$query=$db->query("select * from wd_tasklog order by id desc limit $start,$pagenum");

$list=array();
$i=0;
while ($r=$db->fetch_array($query)) {
	$list[$i]['id']=$r['id'];
	$list[$i]['name']=$r['name'];
	$list[$i]['note']=$r['note'];
	$list[$i]['rtime']=date("Y-m-d H:i",$r['rtime']);
	$i++;
}
$page=new page(array('total'=>$sum,'perpage'=>$pagenum));
$pagelist=$page->show();
require_once(G_T("admin/tasklog.htm"));
//G_T_F("footer.htm");
footer_info();
?>
