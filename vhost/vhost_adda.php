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
	//$domain=check_values(str_replace("www.","",$_POST['domain']),"域名不能为空!");
	$domain=check_values($_POST['domain'],"域名不能为空!");
	//if ($domain!="default") check_domain($domain);
	if ($domain=="default") check_domain_def($domain);
	else check_domain($domain);
	
	$is_www=intval($_POST['is_www']);
	//go_back("test1");
	$domains=str_replace("http://","",chop($_POST['domains']));
	//if (empty($domains)) $domains="www.".$domain;
	//else
	//$domains="www.".$domain." ".$domains;
	if ($domain=="default");
	elseif ($is_www==1) {
		//$domains="www.".$domain." ".$domains;
		$dms=explode(",",$domains);
		$dws="www.".$domain;
		if (!in_array($dws,$dms))
			$domains="www.".$domain." ".$domains;
	}else;

	if ($wdcp_gid!=1) {
		$q1=$db->query("select * from wd_group where id='$wdcp_gid'");
		$r1=$db->fetch_array($q1);
		$sitec=$r1['site'];
		if ($sitec>0) {
			$q2=$db->query("select * from wd_site where uid='$wdcp_uid'");
			if ($db->num_rows($q2)>=$sitec) go_back("可创建站点数超出限制,请联系管理员");
		}
	}

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
	
	//ftp
	$ftp_flag=chop($_POST['ftp_flag']);
	if ($ftp_flag==1) {
		$ftpuser=chop($_POST['ftpuser']);
		$ftppassword=stripslashes(chop($_POST['ftppasswd']));
		$cftppasswd=stripslashes(chop($_POST['cftppasswd']));
		if (strcmp($ftppassword,$cftppasswd)!=0) go_back("FTP两次密码不对!");

		//20130613
		if ($wdcp_gid!=1) {
			$q1=$db->query("select * from wd_group where id='$wdcp_gid'");
			$r1=$db->fetch_array($q1);
			$ftpc=$r1['ftp'];
			$q2=$db->query("select * from wd_ftp where mid='$wdcp_uid'");
			if ($db->num_rows($q2)>$ftpc and $ftpc>0) go_back("可创建ftp数超出限制,请联系管理员");
			if (@is_dir($vhost_dir)) go_back("普通用户不能创建已存在目录的FTP用户");
		}

		check_user($ftpuser,0,"ftp用户名");
		check_string($ftppassword,"ftp密码",0);//
		check_user_ftp($ftpuser);
	}
	
	//mysql db
	$db_flag=chop($_POST['db_flag']);
	if ($db_flag==1) {
		$dbuser_n=chop($_POST['dbuser']);
		$dbpasswd=chop($_POST['dbpasswd']);
		$cdbpasswd=chop($_POST['cdbpasswd']);
		if (strcmp($dbpasswd,$cdbpasswd)!=0) go_back("两次密码不对!");
		$dbname_n=chop($_POST['dbname']);
	
		if ($wdcp_gid!=1) {
			$q1=$db->query("select * from wd_group where id='$wdcp_gid'");
			$r1=$db->fetch_array($q1);
			$mysqlc=$r1['mysql'];
			$q2=$db->query("select * from wd_mysql where uid='$wdcp_uid' and isuser=0");
			if ($db->num_rows($q2)>$mysqlc and $mysqlc>0) go_back("可创建mysql数据库数超出限制,请联系管理员");
		}
	
		if (empty($dbname_n)) $dbname_n=$dbuser_n;//
		$dbcharset=chop($_POST['dbcharset']);
		check_user($dbuser_n,0,"数据库");
		check_string($dbpasswd,"数据库密码");
		check_string($dbname_n,"数据库名");
		system_name_check($dbuser_n,0);
		system_name_check($dbname_n,1);
	}
	
	$rtime=time();
	$query = $db->query("insert into wd_site(id,uid,domain,domains,domainss,vhost_dir,limit_dir,conn,bw,dir_index,dir_list,gzip,expires,a_filetype,a_url,d_url,err400,err401,err403,err404,err405,err500,err503,re_dir,re_url,access_log,error_log,ruser,rewrite,port,uip,rtime,state) values
(NULL,'$uid','$domain','$domains','$domainss','$wvhost_dir','$limit_dir','$conn','$bw','$dindex','$dir_list','$gzip','$expires','$a_filetype','$a_url','$d_url','$err400','$err401','$err403','$err404','$err405','$err500','$err503','$re_dir','$re_url','$access_log','$error_log','$ruser','$rewrite','$port','$uip','$rtime','0');");
	//exit;
	$sid=$db->insert_id();
	update_vhost($sid);
	optlog($wdcp_uid,"增加站点 $domain",0,0);//
	//if ($query) str_go_url("添加成功!请继续",0);
	if (!$query) go_back("保存失败!");
	
	//ftp
	if ($ftp_flag==1) {
		$npassword=md5($ftppassword);
		$quotasize=0;
		ftp_user_add($sid,$uid,$ftpuser,$npassword,$wvhost_dir,$quotasize);//
		optlog($wdcp_uid,"增加FTP帐号 $ftpuser",0,0);//
	}

	//mysql db
	if ($db_flag==1) {
		create_db($dbname_n,$dbcharset);
		create_db_user($dbuser_n,$dbpasswd);
		grant_db_user($dbuser_n,$host,$dbname_n);
		$quotasize=0;
		mysql_add_db($uid,$sid,$dbname_n,$dbcharset,$quotasize,$rtime);
		mysql_add_user($dbuser_n,$dbpasswd,$host,$dbname_n,$rtime);
		//wd_mysql_add($uid,$sid,$user,$password,$host,$dbname,$dbcharset,$quotasize,$isuser,$rtime);
		optlog($wdcp_uid,"增加mysql数据库和用户 $dbname_n $dbuser_n",0,0);
	}
	web_reload();
	str_go_url("增加站点成功!",0);
	exit;
}
$port_list=return_web_port();
$ip_list=return_web_ip();
//$user_list=user_list();
$user_list=member_list();
require_once(G_T("vhost/vhost_adda.htm"));
//G_T_F("footer.htm");
footer_info();
?>
