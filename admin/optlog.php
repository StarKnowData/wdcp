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

if (isset($_GET['Submit']) and !empty($_GET['domain'])) {
	$domain=chop($_GET['domain']);
	$t=intval($_GET['t']);
	if ($wdcp_gid!=1) go_back("err");
	if ($t==1) {
		$q=$db->query("select * from wd_member where name='$domain'");
		if ($db->num_rows($q)==0) go_back("用户名不存在");
		$r=$db->fetch_array($q);
		$wh="where uid='".$r['id']."'";	//
		//$wh="where uid like '%$domain%'";
	}elseif ($t==2) {
		$wh="where opt like '%$domain%'";		
	}elseif ($t==3) {
		$wh="where ip='$domain'";
	}else
		go_back("err");
}else
	$wh="";

$query=$db->query("select * from wd_optlog $wh");
$sum=$db->num_rows($query);
$query=$db->query("select * from wd_optlog $wh order by id desc limit $start,$pagenum");

$list=array();
$i=0;
while ($r=$db->fetch_array($query)) {
	$list[$i]['id']=$r['id'];
	if ($r['uid']==0)
		$list[$i]['uid']="系统用户";
	else
		$list[$i]['uid']=uid_name($r['uid']);
	$list[$i]['opt']=$r['opt'];
	$list[$i]['ip']=$r['ip'];
	if (empty($r['ip'])) $list[$i]['ip']="localhost";
	$list[$i]['otime']=date("Y-m-d H:i",$r['otime']);
	$i++;
}
$page=new page(array('total'=>$sum,'perpage'=>$pagenum));
$pagelist=$page->show();

if ($wdcp_gid==1)
	$search_d='<form id="form1" name="form1" method="get" action="">查找:
  <select name="t" id="t">
    <option value="1" selected="selected">用户名</option>
    <option value="2">按域名</option>
	<option value="3">IP</option>
  </select>
  <input name="domain" type="text" id="domain" size="20" /></label>
  <input type="submit" name="Submit" value="查询" />
  </form>';//
else
	$search_d='';

require_once(G_T("admin/optlog.htm"));
//G_T_F("footer.htm");
footer_info();
?>
