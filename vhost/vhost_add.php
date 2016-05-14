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


if (isset($_POST['Submit_add'])) {
	$domain=check_values($_POST['domain'],"域名不能为空!");//////
	//check_domain($domain);
	if ($domain=="default") check_domain_def($domain);
	else check_domain($domain);

	//go_back("test1");
	$is_www=intval($_POST['is_www']);
	$domains=str_replace("http://","",chop($_POST['domains']));
	//if (empty($domains)) $domains="www.".$domain;
	//if ($domain=="default");
	//if ($domain=="default");
	//elseif (empty($domains)) $domains="www.".$domain;
	//elseif (!eregi("www.$domain",$domains))
		//$domains="www.".$domain." ".$domains;
	//else;
	//echo $domains."|";
	if ($domain=="default");
	elseif ($is_www==1) {
		//$domains="www.".$domain." ".$domains;
		$dms=explode(",",$domains);
		$dws="www.".$domain;
		if (!in_array($dws,$dms))
			$domains="www.".$domain." ".$domains;
	}else;
	//echo $domains."|";exit;

	if ($wdcp_gid!=1) {
		$q1=$db->query("select * from wd_group where id='$wdcp_gid'");
		$r1=$db->fetch_array($q1);
		$sitec=$r1['site'];
		if ($sitec>0) {
			$q2=$db->query("select * from wd_site where uid='$wdcp_uid'");
			if ($db->num_rows($q2)>=$sitec) go_back("可创建站点数超出限制,请联系管理员");
		}//
	}
		
		//$domains="www.".$domain." ".$domains;
	//if (!eregi("[a-z0-9]{1,50}\.[a-z]{2,3}",$domain)) go_back("域名有错!");
	//go_back("test2");
	//if (!eregi("www.",$domains))
		//$domains=",www.".$domain;
	$domainss=intval($_POST['domainss']);
	//$vhost_dir=check_values($_POST['vhost_dir'],"目录不能为空");
	$vhost_dir=chop($_POST['vhost_dir']);
	//if (eregi("/",$vhost_dir)) go_back("目录名称错误");
	if (empty($vhost_dir)) $vhost_dir=$domain;
	if (substr($vhost_dir,0,1)=="/")
		$wvhost_dir=str_replace(".","_",$vhost_dir);
	else
		$wvhost_dir=$web_home."/".str_replace(".","_",$vhost_dir);
	wdl_vhostdir_check($wvhost_dir);
	if ($wdcp_gid!=1 and @is_dir($wvhost_dir)) go_back("目录存在");//
	//echo $wvhost_dir;exit;
	$dindex=chop($_POST['dindex']);
	$err400=chop($_POST['err400']);
	$err401=chop($_POST['err401']);
	$err403=chop($_POST['err403']);
	$err404=chop($_POST['err404']);
	$err405=chop($_POST['err405']);
	$err500=chop($_POST['err500']);
	$access_log=chop($_POST['access_log']);
	$error_log=chop($_POST['error_log']);
	$limit_dir=intval($_POST['limit_dir']);
	if ($wdcp_gid!=1) $limit_dir=1;//
	$dir_list=intval($_POST['dir_list']);
	$ruser=chop($_POST['ruser']);
	$rewrite=chop($_POST['rewrite']);
	$port=chop($_POST['port']);
	$uip=chop($_POST['uip']);
	$uid=intval($_POST['uid']);
	if ($uid==0) $uid=$wdcp_uid;
	
	//if (empty($err400)) $err400=1;
	//if (empty($err403)) $err403=1;
	//if (empty($err404)) $err404=1;
	//if (empty($err405)) $err405=1;
	//
	$conn=intval($_POST['conn']);
	$bw=intval($_POST['bw']);
	$a_filetype=chop($_POST['a_filetype']);
	$a_url=str_replace("http://","",chop($_POST['a_url']));
	$d_url=str_replace("http://","",chop($_POST['d_url']));
	$re_dir=intval($_POST['re_dir']);
	if ($re_dir>0) {$domains="";$domainss=0;}
	$re_url=str_replace("http://","",chop($_POST['re_url']));
	$gzip=intval($_POST['gzip']);
	$expires=intval($_POST['expires']);
	
	
	$rtime=time();
	$query = $db->query("insert into wd_site(id,uid,domain,domains,domainss,vhost_dir,limit_dir,conn,bw,dir_index,dir_list,gzip,expires,a_filetype,a_url,d_url,err400,err401,err403,err404,err405,err500,err503,re_dir,re_url,access_log,error_log,ruser,rewrite,port,uip,rtime,state) values
(NULL,'$uid','$domain','$domains','$domainss','$wvhost_dir','$limit_dir','$conn','$bw','$dindex','$dir_list','$gzip','$expires','$a_filetype','$a_url','$d_url','$err400','$err401','$err403','$err404','$err405','$err500','$err503','$re_dir','$re_url','$access_log','$error_log','$ruser','$rewrite','$port','$uip','$rtime','0');");
	//exit;
	$sid=$db->insert_id();
	update_vhost($sid);
	web_reload();
	optlog($wdcp_uid,"增加站点 $domain",0,0);//
	//if ($query) str_go_url("添加成功!请继续",0);
	if (!$query) go_back("保存失败!");
	str_go_url("增加站点成功!",0);
	exit;
}
$port_list=return_web_port();
$ip_list=return_web_ip();
//$user_list=user_list();
$user_list=member_list();
require_once(G_T("vhost/vhost_add.htm"));
//G_T_F("footer.htm");
footer_info();
?>
