<?php
/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/

require_once "../inc/common.inc.php";
//require_once "../inc/vhost.inc.php";
//require_once "../inc/page_class.php";
require_once "../login.php";
//require_once "../inc/admlogin.php";
//if ($wdcp_gid!=1) exit;


if (isset($_GET['act']) and $_GET['act']=="update") {
	if ($wdcp_gid!=1) go_back("无权操作");//

	$q=$db->query("select * from wd_site where state=0");
	$i=0;
	while ($r=$db->fetch_array($q)) {
		update_vhost($r['id']);
		$i++;
	}
	web_reload();
	optlog($wdcp_uid,"更新了 $i 个站点",0,0);//
	//exit;
	str_go_url("更新完成!",0);
}

if (isset($_POST['Submit_chu'])) {
	if ($wdcp_gid!=1) go_back("不是管理员不能操作此功能");
	$domain=chop($_POST['domain']);
	$uid=intval($_POST['uid']);
	$id=intval($_POST['id']);
	if ($uid==0) $uid=$wdcp_uid;
	$q=$db->query("select * from wd_site where domain='$domain' and id='$id'");
	if ($db->num_rows($q)==0) go_back("站点id错误!");
	$db->query("update wd_site set uid='$uid' where domain='$domain' and id='$id'");
	$db->query("update wd_ftp set mid='$uid' where sid='$id'");
	$db->query("update wd_mysql set uid='$uid' where sid='$id'");
	optlog($wdcp_uid,"修改站点$domain 所属用户",0,0);
	str_go_url("修改成功!",0);
}

if (isset($_GET['act']) and $_GET['act']=="chgu") {
	$domain=chop($_GET['domain']);
	$uid=intval($_GET['uid']);
	$id=intval($_GET['id']);
	$user_list=member_list($uid);
	require_once(G_T("vhost/vhost_chgu.htm"));
	exit;
}


if (isset($_GET['act']) and ($_GET['act']=="off")) {
	$id=intval($_GET['id']);
	$domain=chop($_POST['domain']);
	if ($wdcp_gid==1)
		$q=$db->query("select * from wd_site where id='$id'");
	else
		$q=$db->query("select * from wd_site where uid='$wdcp_uid' and id='$id'");
	if ($db->num_rows($q)==0) go_back("ID不存在！");
	update_vhost_del($id);
	$re=$db->query("update wd_site set state=1 where id='$id'");
	//$sql=$db->query("select * from wd_host where id='$id'");
	//$q=$db->fetch_array($sql);
	//$tempfn=$ws_vhost."/".$q['domain'].".conf";
	//exec("sudo wd_app rm '$tempfn' no",$str,$re);
	//$re=wdl_sudo_app_rm($tempfn);
	//exec("sudo wd_app restart $c_server_init",$str,$re);
	web_reload();
	optlog($wdcp_uid,"暂停站点 $domain",0,0);//
	//if ($re==0) 
	str_go_url("已关闭!",0);
	//else
		//go_back("关闭错误!");
	exit;
}


if (isset($_GET['act']) and ($_GET['act']=="on")) {
	$id=intval($_GET['id']);
	$domain=chop($_GET['domain']);
	if ($wdcp_gid==1)
		$q=$db->query("select * from wd_site where id='$id'");
	else
		$q=$db->query("select * from wd_site where uid='$wdcp_uid' and id='$id'");
	if ($db->num_rows($q)==0) go_back("ID不存在！");
	update_vhost($id);
	$re=$db->query("update wd_site set state=0 where id='$id'");
	web_reload();
	optlog($wdcp_uid,"开启站点 $domain",0,0);//
	str_go_url("已开启!",0);
	exit;
}



//
if (isset($_GET['act']) and ($_GET['act']=="del")) {
	$id=intval($_GET['id']);
	$domain=chop($_GET['domain']);
	
	if ($wdcp_gid==1)
		$q=$db->query("select * from wd_site where id='$id'");
	else
		$q=$db->query("select * from wd_site where uid='$wdcp_uid' and id='$id'");
	if ($db->num_rows($q)==0) go_back("ID不存在！");
	$r=$db->fetch_array($q);
	update_vhost_del($id);
	
	if ($site_dir_del_is==1 and !eregi("public_html",$r['vhost_dir']) and $r['vhost_dir']!="/" and substr($r['dir'],0,3)!="../") {//rmdir($r['dir']);
		//echo $r['vhost_dir'];//
		$rmdir_tmp=WD_ROOT."/data/tmp/rmdir.txt";
		@file_put_contents($rmdir_tmp,$r['vhost_dir']);
		//echo @file_get_contents($rmdir_tmp);
		//echo $r['dir']."<br>";
		exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);
		if (@file_exists($rmdir_tmp)) @unlink($rmdir_tmp);
	}
	web_reload();
	//exit;
	
	/*
	$sql=$db->query("select * from wd_host where id='$id'");
	$re=$db->fetch_array($sql);
	$vf=chop($re['domain']).".conf";
	$u=$re['ftpuser'];
	$dbn=$re['dbname'];
	$tempfn=$ws_vhost."/".$vf;
	//exec("sudo wd_app rm '$tempfn' no",$str,$rea);
	//echo "ftpuser:|".$re['ftpuser']."|";exit;
	wdl_sudo_app_rm($tempfn);
	if ($re['ftpuser']!="")
		wdl_sudo_app_user_del($u);
		//exec("sudo wd_app user del '$u' ok",$str,$rea);

	//$sql="DROP DATABASE $dbn;\n";
	//$sql.="DROP USER '".$re['dbuser']."'@'localhost';";
	//$sqlroot=wdl_sqlroot_pw();
	//$link=mysql_connect("localhost","root",$sqlroot) or go_back("mysql root密码错误");
	//crunquery($sql);
	*/
	//if ($re==0) go_to("删除成功!");	
	$db->query("delete from wd_site where id='$id'");
	optlog($wdcp_uid,"删除站点 $domain",0,0);//
	//echo $re['domain']." 删除成功!<br><br>";
	//echo " 删除成功!<br><br>";
	str_go_url("删除成功!",0);	
}


$pagenum=15;
if (!isset($_GET['page'])) $start=0;
else	$start=(intval($_GET['page'])-1)*$pagenum;
if ($start<0) $start=0;

if (isset($_POST['Submit'])) {
	$dt=intval($_POST['dt']);
	$domain=chop($_POST['domain']);
	if ($dt==1)
		$wh="domain like '%$domain%' and";
	else
		$wh="domains like '%$domain%' and";
}else
	$wh="";

//$query=$db->query("select * from wd_site where ldomain=0 order by id");
if ($wdcp_gid==1) {
	$query=$db->query("select * from wd_site where $wh sdomain=0 order by id");
	$sum=$db->num_rows($query);
	$query=$db->query("select * from wd_site where $wh sdomain=0 order by id desc limit $start,$pagenum");
}else{
	$query=$db->query("select * from wd_site where $wh uid='$wdcp_uid' and sdomain=0 order by id");
	$sum=$db->num_rows($query);
	$query=$db->query("select * from wd_site where $wh uid='$wdcp_uid' and sdomain=0 order by id desc limit $start,$pagenum");
}
$list=array();
$i=0;
while ($re=$db->fetch_array($query)) {
	if ($re['state']=="0") {
		$s11="正常";
		$s12='<a href="'.$PHP_SELF.'?act=off&id='.$re['id'].'&domain='.$re['domain'].'">关</a>';
	}else{
		$s11="关闭";
		$s12='<a href="'.$PHP_SELF.'?act=on&id='.$re['id'].'&domain='.$re['domain'].'">开</a>';	
	}
	$list[$i]['id']=$re['id'];
	$list[$i]['domain']=$re['domain'];
	$list[$i]['domains']=$re['domains'];
	$list[$i]['vhost_dir']=$re['vhost_dir'];
	$list[$i]['conn']=$re['conn'];
	$list[$i]['bw']=$re['bw'];
	$list[$i]['linkc']=$re['a_filetype'];
	if ($re['re_dir']==1)
		$list[$i]['301']="301";
	elseif ($re['re_dir']==2)
		$list[$i]['301']="302";
	else
		$list[$i]['301']="无";
	$list[$i]['time']=date("Y-m-d H:i",$re['rtime']);
	$list[$i]['user']=uid_name($re['uid']);
	$list[$i]['uid']=$re['uid'];
	if ($re['state']==0)
		$list[$i]['state']="正常";
	else
		$list[$i]['state']="暂停";
	$list[$i]['act']=$s12;
	$i++;
}
//print_r($list);
$page=new page(array('total'=>$sum,'perpage'=>$pagenum));
$pagelist=$page->show();
require_once(G_T("vhost/vhost_list.htm"));
//G_T_F("footer.htm");
footer_info();
?>