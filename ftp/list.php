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


if (isset($_POST['Submit_chu'])) {
	if ($wdcp_gid!=1) go_back("不是管理员不能操作此功能");
	$user=chop($_POST['user']);
	$mid=intval($_POST['mid']);
	$id=intval($_POST['id']);
	if ($uid==0) $uid=$wdcp_uid;
	$q=$db->query("select * from wd_ftp where user='$user' and id='$id'");
	if ($db->num_rows($q)==0) go_back("站点id错误!");
	$db->query("update wd_ftp set mid='$mid' where user='$user' and id='$id'");
	optlog($wdcp_uid,"修改FTP帐号$user 所属用户",0,0);
	str_go_url("修改成功!",0);
}

if (isset($_GET['act']) and $_GET['act']=="chgu") {
	$user=chop($_GET['user']);
	$mid=intval($_GET['mid']);
	$id=intval($_GET['id']);
	$user_list=member_list($mid);
	require_once(G_T("ftp/chgu.htm"));
	exit;
}

if (isset($_GET['act']) and $_GET['act']=="off") {
	$id=intval($_GET['id']);
	$user=chop($_GET['user']);
	if (!is_numeric($id)) go_back("ID错误");
	if ($wdcp_gid==1)
		$q=$db->query("update wd_ftp set status=1 where id='$id'");
	else
		$q=$db->query("update wd_ftp set status=1 where mid='$wdcp_uid' and id='$id'");
	optlog($wdcp_uid,"关闭FTP帐号 $user ",0,0);
	if (!$q) go_back("保存失败！");
	str_go_url("FTP帐号已关闭!",0);	
}

if (isset($_GET['act']) and $_GET['act']=="on") {
	$id=intval($_GET['id']);
	$user=chop($_GET['user']);
	if (!is_numeric($id)) go_back("ID错误");
	if ($wdcp_gid==1)
		$q=$db->query("update wd_ftp set status=0 where id='$id'");
	else
		$q=$db->query("update wd_ftp set status=0 where mid='$wdcp_uid' and id='$id'");
	optlog($wdcp_uid,"开启FTP帐号 $user ",0,0);
	if (!$q) go_back("保存失败！");
	str_go_url("FTP帐号已开启!",0);	
}


if (isset($_GET['act']) and $_GET['act']=="del") {
	$id=intval($_GET['id']);
	$user=chop($_GET['user']);
	if ($wdcp_gid!=1) go_back("无权操作!");//
	if (!is_numeric($id)) go_back("ID错误");
	$q=$db->query("select * from wd_ftp where id='$id'");
	if ($db->num_rows($q)==0) go_back("ID错误");
	$r=$db->fetch_array($q);
	if ($wdcp_gid==1)
		$q=$db->query("delete from wd_ftp where id='$id'");
	else
		$q=$db->query("delete from wd_ftp where mid='$wdcp_uid' and id='$id'");
	if ($ftp_dir_del_is==1 and !eregi("public_html",$r['dir']) and $r['dir']!="/" and substr($r['dir'],0,3)!="../") { //rmdir($r['dir']);
		$rmdir_tmp=WD_ROOT."/data/tmp/rmdir.txt";
		@file_put_contents($rmdir_tmp,$r['dir']);
		exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);
		if (@file_exists($rmdir_tmp)) @unlink($rmdir_tmp);
	}
		
	optlog($wdcp_uid,"删除FTP帐号 $user ",0,0);
	if (!$q) go_back("保存失败！");
	str_go_url("FTP帐号已删除!",0);
}

if (isset($_POST['Submit_chgpw'])) {
	//print_r($_POST);
	$id=intval($_GET['id']);
	$user=chop($_POST['user']);
	if (!is_numeric($id)) go_back("ID错误");
	$password=stripslashes(chop($_POST['password']));
	$cpassword=stripslashes(chop($_POST['cpassword']));
	if (strcmp($password,$cpassword)!=0) go_back("两次密码不对，请重新输入！");
	check_passwd($password);
	$npass=md5($password);
	//echo stripslashes($password);
	if ($wdcp_gid==1)
		$q=$db->query("update wd_ftp set password='$npass' where id='$id'");
	else
		$q=$db->query("update wd_ftp set password='$npass' where mid='$wdcp_uid' and id='$id'");
	optlog($wdcp_uid,"修改FTP帐号 $user 密码 ",0,0);
	if (!$q) go_back("修改失败！");
	str_go_url("FTP帐号密码修改成功!",0);	
}

if (isset($_GET['act']) and $_GET['act']=="chgpw") {
	$id=intval($_GET['id']);
	if (!is_numeric($id)) go_back("ID错误");
	if ($wdcp_gid==1)
		$q=$db->query("select * from wd_ftp where id='$id'");
	else
		$q=$db->query("select * from wd_ftp where mid='$wdcp_uid' and id='$id'");
	if ($db->num_rows($q)==0) go_back("FTP帐号不存在!");
	$r=$db->fetch_array($q);
	$user=$r['user'];
	$id=$r['id'];
	require_once(G_T("ftp/chgpw.htm"));
	G_T_F("footer.htm");
	exit;
}


$pagenum=20;
if (!isset($_GET['page'])) $start=0;
else	$start=(intval($_GET['page'])-1)*$pagenum;
if ($start<0) $start=0;

if (isset($_POST['Submit'])) {
	$user=chop($_POST['user']);
	$wh="user like '%$user%' and";
	$whg="where user like '%$user%'";
}elseif (isset($_GET['sid'])) {
	$sid=intval($_GET['sid']);
	$wh="sid='$sid' and";
	$whg="where sid='$sid'";
}else{
	$wh="";
	$whg="";	
}

if ($wdcp_gid==1) {
	$query=$db->query("select * from wd_ftp $whg");
	$sum=$db->num_rows($query);
	$query=$db->query("select * from wd_ftp $whg order by id desc limit $start,$pagenum");
}else{
	$query=$db->query("select * from wd_ftp where $wh mid='$wdcp_uid'");
	$sum=$db->num_rows($query);
	$query=$db->query("select * from wd_ftp where $wh mid='$wdcp_uid' order by id desc limit $start,$pagenum");
}
$list=array();
$i=0;
while ($r=$db->fetch_array($query)) {
	//echo "11";
	$list[$i]['id']=$r['id'];
	$list[$i]['user']=$r['user'];
	$list[$i]['password']=$r['password'];
	$list[$i]['dir']=$r['dir'];
	$list[$i]['quotasize']=$r['quotasize'];
	$list[$i]['quotafiles']=$r['quotafiles'];
	$list[$i]['ulbandwidth']=$r['ulbandwidth'];
	$list[$i]['dlbandwidth']=$r['dlbandwidth'];
	$list[$i]['rtime']=date("Y-m-d H:i",$r['rtime']);
	$list[$i]['users']=uid_name($r['mid']);
	$list[$i]['mid']=$r['mid'];
	if ($r['status']==0) {
		$list[$i]['status']="正常";
		$list[$i]['statusa']='<a href="'.$PHP_SELF.'?act=off&id='.$r['id'].'&user='.$r['user'].'">关闭</a>';
	}else{
		$list[$i]['status']="停用";
		$list[$i]['statusa']='<a href="'.$PHP_SELF.'?act=on&id='.$r['id'].'&user='.$r['user'].'">开启</a>';
	}
	$list[$i]['sid']=sid_to_domain($r['sid']);
	$i++;
}
$page=new page(array('total'=>$sum,'perpage'=>$pagenum));
$pagelist=$page->show();
require_once(G_T("ftp/list.htm"));
//G_T_F("footer.htm");
footer_info();
?>
