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

if (isset($_POST['Submit_add'])) {
	if ($wdcp_gid>5) go_back("无权操作");
	$name=strtolower(chop($_POST['username']));
	$passwd=chop($_POST['passwd']);
	$gid=intval($_POST['group']);
	$ft=chop($_POST['ft']);
	$charge=chop($_POST['charge']);
	$price=chop($_POST['price']);
	$xm=chop($_POST['xm']);
	$xb=chop($_POST['xb']);
	$sfzh=chop($_POST['sfzh']);
	$addr=chop($_POST['addr']);
	$tel=chop($_POST['tel']);
	$qq=chop($_POST['qq']);
	$email=chop($_POST['email']);
	check_user($name);
	check_passwd($passwd);
	$rtime=time();
	$passwd=md5($passwd);
	$q=$db->query("select * from wd_member where name='$name'");
	if ($db->num_rows($q)!=0) go_back("该用户名已存在");
	//if ($gid==1) $state=0;
	if ($gid==1) go_back("不能增加管理员");//
	else $state=0;
	$q=$db->query("insert into wd_member(gid,name,passwd,xm,xb,sfzh,addr,tel,qq,email,rtime,state) values('$gid','$name','$passwd','$xm','$xb','$sfzh','$addr','$tel','$qq','$email','$rtime','$state')");
	if (!$q) go_back("保存失败!");
	else {
		optlog($wdcp_uid,"增加用户$name",0,0);
		str_go_url("用户增加成功!",0);
		}
}

if (isset($_POST['Submit_edit'])) {
	wdl_demo_sys();
	if ($wdcp_gid>5 and $id!=$wdcp_uid) go_back("无权操作");
	$id=intval($_POST['id']);
	$name=strtolower(chop($_POST['username']));
	$passwd=chop($_POST['passwd']);
	$gid=intval($_POST['group']);
	$ft=chop($_POST['ft']);
	$charge=chop($_POST['charge']);
	$price=chop($_POST['price']);
	$xm=chop($_POST['xm']);
	$xb=chop($_POST['xb']);
	$sfzh=chop($_POST['sfzh']);
	$addr=chop($_POST['addr']);
	$tel=chop($_POST['tel']);
	$qq=chop($_POST['qq']);
	$email=chop($_POST['email']);
	check_user($name);
	if (empty($passwd)) go_back("密码错误");
	$sql="update wd_member set ";
	if (!empty($name)) $sql.="name='$name'";
	//if (!empty($passwd)) $sql.=",passwd='$passwd'";
	if (!empty($gid)) $sql.=",gid='$gid'";
	if (!empty($xm)) $sql.=",xm='$xm'";
	if (!empty($xb)) $sql.=",xb='$xb'";
	if (!empty($sfzh)) $sql.=",sfzh='$sfzh'";
	if (!empty($addr)) $sql.=",addr='$addr'";
	if (!empty($tel)) $sql.=",tel='$tel'";
	if (!empty($qq)) $sql.=",qq='$qq'";
	if (!empty($email)) $sql.=",email='$email'";
	//if (!empty($ft)) $sql.=",ft='$ft'";
	//if (!empty($charge)) $sql.=",charge='$charge'";
	//if (!empty($price)) $sql.=",price='$price'";
	$sql.=" where id='$id'";
	//echo $sql;exit;
	$q=$db->query($sql);
	if (!$q) go_bac("保存失败!");
	else {
		optlog($wdcp_uid,"修改用户$name",0,0);
		str_go_url("用户修改成功!",0);
		}
}

if (isset($_GET['act']) and $_GET['act']=="del") {
	wdl_demo_sys();
	$id=intval($_GET['id']);
	if ($wdcp_gid>5) go_back("无权操作");
	if ($id==1) go_back("该管理用户不能删除!");
	if ($id==$wdcp_uid) go_back("不能删除自己!");
	$q=$db->query("select * from wd_member where id='$id'");
	if ($db->num_rows($q)==0) go_back("id错误");
	$q=$db->query("delete from wd_member where id='$id'");
	if (!$q) go_bac("保存失败!");
	else {
		optlog($wdcp_uid,"删除用户$id",0,0);
		str_go_url("用户删除成功!",0);
		}
}

if (isset($_POST['Submit_chg'])) {
	wdl_demo_sys();
	if ($wdcp_gid>5 and $id!=$wdcp_uid) go_back("无权操作");
	$id=intval($_POST['id']);
	$npass=chop($_POST['npass']);
	$cnpass=chop($_POST['cnpass']);
	if ($npass!==$cnpass) go_back("两次密码不同!");
	check_passwd($npass);//
	$npass=md5($npass);
	$q=$db->query("update wd_member set passwd='$npass' where id='$id'");
	if (!$q) go_bac("保存失败!");
	else {
		optlog($wdcp_uid,"修改用户$name密码",0,0);
		str_go_url("密码修改成功!",0);
		}
}

if (isset($_GET['act']) and $_GET['act']=="stop") {
	$id=intval($_GET['id']);
	if ($wdcp_gid>5) go_back("无权操作");
	$db->query("update wd_member set state=1 where id='$id'");
	optlog($wdcp_uid,"暂停用户$id",0,0);
	str_go_url("用户暂停成功!",0);
}

if (isset($_GET['act']) and $_GET['act']=="start") {
	$id=intval($_GET['id']);
	if ($wdcp_gid>5) go_back("无权操作");
	$db->query("update wd_member set state=0 where id='$id'");
	optlog($wdcp_uid,"开启用户$id",0,0);
	str_go_url("用户开启成功!",0);
}
if (isset($_GET['act']) and $_GET['act']=="active") {
	$id=intval($_GET['id']);
	if ($wdcp_gid>5) go_back("无权操作");
	$db->query("update wd_member set state=0 where id='$id'");
	optlog($wdcp_uid,"审核用户$id",0,0);
	str_go_url("用户审核成功!",0);
}

if (isset($_GET['act']) and $_GET['act']=="add") {
	$group_list=group_list();
	require_once(G_T("member/member_add.htm"));
}

if (isset($_GET['act']) and $_GET['act']=="edit") {
	$id=intval($_GET['id']);
	if ($wdcp_gid>1 and $id!=$wdcp_uid)
		$q=$db->query("select * from wd_member where id='$id' and pid='$wdcp_uid'");
	else
		$q=$db->query("select * from wd_member where id='$id'");	
	if ($db->num_rows($q)==0) go_back("数据错误!");
	$r=$db->fetch_array($q);
	$group_list=group_list($r['gid']);
	require_once(G_T("member/member_edit.htm"));
}

if (isset($_GET['act']) and $_GET['act']=="chg") {
	$id=intval($_GET['id']);
	if ($wdcp_gid>5 and $id!=$wdcp_uid)
		$q=$db->query("select * from wd_member where id='$id' and pid='$wdcp_uid'");
	else
		$q=$db->query("select * from wd_member where id='$id'");
	if ($db->num_rows($q)==0) go_back("ID错误");
	$r=$db->fetch_array($q);
	require_once(G_T("member/member_chg.htm"));
}

if (isset($_GET['act']) and $_GET['act']=="search") {
	require_once(G_T("member/member_search.htm"));
}


if (!isset($_GET['act']) or $_GET['act']=="list" or isset($_POST['Submit_search'])) {
//print_r($_POST);
$pagenum=20;
if (!isset($_GET['page'])) $start=0;
else	$start=(intval($_GET['page'])-1)*$pagenum;
if ($start<0) $start=0;
$username=isset($_POST['username'])?chop($_POST['username']):'';
if (!empty($username)) {
	check_user($username);
	//echo "select * from wd_member where name='$username'";
	if ($wdcp_gid>5)
		$query=$db->query("select * from wd_member where name='$username' and pid='$wdcp_uid'");
	else
		$query=$db->query("select * from wd_member where name='$username'");
}else {
	$query=$db->query("select * from wd_member");
	$sum=$db->num_rows($query);
	$query=$db->query("select * from wd_member order by id desc limit $start,$pagenum");
}

$list=array();
$i=0;
while ($r=$db->fetch_array($query)) {
  	if ($r['state']==0) {$s1="正常";$s2='<a href="'.$PHP_SELF.'?act=stop&id='.$r['id'].'">暂停</a>';}
	elseif ($r['state']==2) {$s1="未充值";$s2='<a href="/admin/amoney.php?act=pay&uid='.$r['id'].'">审核</a>';}
	else { $s1="暂停";$s2='<a href="'.$PHP_SELF.'?act=start&id='.$r['id'].'">正常</a>';}

	$list[$i]['id']=$r['id'];
	$list[$i]['name']=$r['name'];
	$list[$i]['gid']=gid_name($r['gid']);
	$list[$i]['sitec']=$r['sitec'];
	//$list[$i]['fid']=charge_fid($r['fid']);
	$list[$i]['money']=$r['money'];
	$list[$i]['umoney']=$r['umoney'];
	$list[$i]['xm']=$r['xm'];
	$list[$i]['tel']=$r['tel'];
	$list[$i]['qq']=$r['qq'];
	$list[$i]['email']=$r['email'];
	$list[$i]['rtime']=date("Y-m-d",$r['rtime']);//
	$i++;
}
$page=new page(array('total'=>$sum,'perpage'=>$pagenum));
$pagelist=$page->show();
require_once(G_T("member/member_list.htm"));
}
G_T_F("footer.htm");
?>
