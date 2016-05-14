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


if (isset($_POST['Submit_add'])) {
	$sid=intval($_POST['sid']);
	$domain=chop($_POST['domain']);
	$pass1=chop($_POST['pass1']);
	$pass2=chop($_POST['pass2']);
	$username=chop($_POST['username']);
	check_user($username);
	check_passwd($pass1);
	if (strcmp($pass1,$pass2)!=0) go_back("两次密码不对");
	if ($wdcp_gid==1)
		$query=$db->query("select * from wd_site where id='$sid'");
	else
		$query=$db->query("select * from wd_site where uid='$wdcp_uid' and id='$sid' and domain='$domain'");
	if ($db->num_rows($query)==0) go_back("网站ID错误");	

	$htf=$htpasswd_dir."/".$sid."_".$domain.".txt";
	$htfm=0;
	if (@file_exists($htf)) $htfm=1;

	$str=$sid."|".$domain."|".$username."|".$pass1."|add";
	$htp_tmp=WD_ROOT."/data/tmp/htp.txt";
	@file_put_contents($htp_tmp,$str);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);
	if (@file_exists($htp_tmp)) @unlink($htp_tmp);
	if (@file_exists($htf) and $htfm==0) update_vhost($sid);
	optlog($wdcp_uid,"域名 $domain 增加用户验证",0,0);//
	if ($re==0)
		str_go_url("增加成功!",0);
	else
		go_back("增加错误!");
	exit;	
}

if (isset($_GET['act']) and $_GET['act']=="del_user") {
	$sid=intval($_GET['sid']);
	$domain=chop($_GET['domain']);
	$user=chop($_GET['user']);
	if ($wdcp_gid==1)
		$sql="select * from wd_site where id='$sid'";
	else
		$sql="select * from wd_site where uid='$wdcp_uid' and id='$sid' and domain='$domain'";
	$query=$db->query($sql);
	//if ($db->num_rows($query)==0) go_back("网站ID错误");
	if (!@file_exists($htpasswd_dir."/".$sid."_".$domain.".txt")) go_back("用户文件不存在");
	
	$str=$sid."|".$domain."|".$user."|".$pass1."|del";
	$htp_tmp=WD_ROOT."/data/tmp/htp.txt";
	@file_put_contents($htp_tmp,$str);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);
	if (@file_exists($htp_tmp)) @unlink($htp_tmp);
	optlog($wdcp_uid,"域名 $domain 删除用户验证",0,0);//
	if ($re==0)
		str_go_url("删除成功!",0);
	else
		go_back("删除错误!");
	exit;	
}

if (isset($_GET['act']) and $_GET['act']=="del") {
	$sid=intval($_GET['sid']);
	$domain=chop($_GET['domain']);
	if ($wdcp_gid==1)
		$sql="select * from wd_site where id='$sid'";
	else
		$sql="select * from wd_site where uid='$wdcp_uid' and id='$sid' and domain='$domain'";
	$query=$db->query($sql);
	//if ($db->num_rows($query)==0) go_back("网站ID错误");
	if (!@file_exists($htpasswd_dir."/".$sid."_".$domain.".txt")) go_back("用户文件不存在");
	
	$str=$sid."|".$domain."|".$user."|".$pass1."|df";
	$htp_tmp=WD_ROOT."/data/tmp/htp.txt";
	@file_put_contents($htp_tmp,$str);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);
	if (@file_exists($htp_tmp)) @unlink($htp_tmp);
	
	$htf=$htpasswd_dir."/".$sid."_".$domain.".txt";
	if (!@file_exists($htf)) update_vhost($sid);
	
	optlog($wdcp_uid,"域名 $domain 删除身份验证",0,0);//
	if ($re==0)
		str_go_url("删除成功!",0);
	else
		go_back("删除错误!");
	exit;	

}

if (isset($_POST['Submit_chp'])) {
	$sid=intval($_POST['sid']);
	$domain=chop($_POST['domain']);
	$user=chop($_POST['user']);
	$pass1=chop($_POST['pass1']);
	$pass2=chop($_POST['pass2']);
	check_passwd($pass1);
	if (strcmp($pass1,$pass2)!=0) go_back("两次密码不对");
	if ($wdcp_gid==1)
		$query=$db->query("select * from wd_site where id='$sid'");
	else
		$query=$db->query("select * from wd_site where uid='$wdcp_uid' and id='$sid' and domain='$domain'");
	if ($db->num_rows($query)==0) go_back("网站ID错误");

	$str=$sid."|".$domain."|".$user."|".$pass1."|edit";
	$htp_tmp=WD_ROOT."/data/tmp/htp.txt";
	@file_put_contents($htp_tmp,$str);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);
	if (@file_exists($htp_tmp)) @unlink($htp_tmp);
	optlog($wdcp_uid,"域名 $domain 修改用户验证密码",0,0);//
	if ($re==0)
		str_go_url("修改成功!",0);
	else
		go_back("修改错误!");
	exit;
}

if (isset($_GET['act']) and $_GET['act']=="chp") {
	$sid=intval($_GET['sid']);
	$domain=chop($_GET['domain']);
	$user=chop($_GET['user']);
	if ($wdcp_gid==1)
		$query=$db->query("select * from wd_site where id='$sid'");
	else
		$query=$db->query("select * from wd_site where uid='$wdcp_uid' and id='$sid'");
	if ($db->num_rows($query)==0) go_back("网站ID错误");
	$htf=$htpasswd_dir."/".$sid."_".$domain.".txt";
	if (!@file_exists($htf)) go_back("文件错误");
	require_once(G_T("vhost/htpasswd_user_chp.htm"));
	//G_T_F("footer.htm");	
	footer_info();
	exit;
}

if (isset($_GET['act']) and $_GET['act']=="view") {
	$sid=intval($_GET['sid']);
	$domain=chop($_GET['domain']);
	if ($wdcp_gid==1)
		$query=$db->query("select * from wd_site where id='$sid'");
	else
		$query=$db->query("select * from wd_site where uid='$wdcp_uid' and id='$sid'");
	if ($db->num_rows($query)==0) go_back("网站ID错误");
	
	$htf=$htpasswd_dir."/".$sid."_".$domain.".txt";
	if (!@file_exists($htf)) go_back("文件错误");
	$str=@file($htf);
	$list=array();
	for ($i=0;$i<sizeof($str);$i++) {
		$s1=explode(":",$str[$i]);
		$list[]=$s1[0];
	}
	require_once(G_T("vhost/htpasswd_user.htm"));
	//G_T_F("footer.htm");	
	footer_info();
	exit;	
}


if (isset($_GET['sid']) and $_GET['act']=="add") {
	$sid=intval($_GET['sid']);
	if (empty($sid)) go_back("输入ID有错");
	//$q=$db->query("select * from wd_site where id='$sid'");
	//echo $wdcp_gid;
	if ($wdcp_gid==1)
		$query=$db->query("select * from wd_site where id='$sid'");
	else
		$query=$db->query("select * from wd_site where uid='$wdcp_uid' and id='$sid'");
	if ($db->num_rows($query)==0) go_back("网站ID错误");
	$r=$db->fetch_array($query);
	$domain=$r['domain'];
	require_once(G_T("vhost/htpasswd_user_add.htm"));
	//G_T_F("footer.htm");	
	footer_info();
	exit;
}

$fd=opendir($htpasswd_dir);
$list=array();
$i=0;
while ($buffer=readdir($fd)) {
	if ($buffer=="." or $buffer=="..") continue;
	$d1=explode("_",str_replace(".txt","",$buffer));
	if (empty($d1[0]) or empty($d1[1])) continue;
	if ($wdcp_gid>5) {
		$domain=$d1[1];
		$q=$db->query("select * from wd_site where uid='$wdcp_uid' and domain='$domain'");
		if ($db->num_rows($q)==0) continue;
	}
	$list[$i]['sid']=$d1[0];
	$list[$i]['domain']=$d1[1];
	$i++;
}
//print_r($list);

require_once(G_T("vhost/htpasswd.htm"));
//G_T_F("footer.htm");
footer_info();
?>