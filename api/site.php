<?php

/*
# WDlinux Control Panel,online management of Linux servers, virtual hosts
# Wdcdn system
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcdn
# Last Updated 2011.05
*/

require_once "../inc/common.inc.php";
if (!isset($api_ip) or empty($api_ip)) exit;
$ip_list=explode(",",$api_ip);
$rip=$_SERVER['REMOTE_ADDR'];
//if ($api_ip!="null" and !in_array($rip,$ip_list)) exit;
if ($api_ip!="null" and !in_array($rip,$ip_list)) exit;

/*
act add|edit|del|stop|start|list
domain,domains,domainss,vhost_dir,dindex,err400,err401,err403,err404,err405,err50,access_log,err_log,limit_dir,rewrite,port,uid,conn,bw,a_filetype,a_url,d_url,re_dir,re_url,gzip,expires
*/

$act=chop($_GET['act']);
//echo $act;exit;
$domain=str_replace("www.","",chop($_GET['domain']));
if ($act!="list" and !eregi("[a-z0-9]{1,50}\.[a-z]{2,3}",$domain)) { echo "domain format err";exit;}
//check_domain($domain,1);

$domains=str_replace("http://","",chop($_GET['domains']));
if (empty($domains)) $domains="www.".$domain;
else
	$domains="www.".$domain." ".$domains;
$domainss=intval($_GET['domainss']);
$vhost_dir=chop($_GET['vhost_dir']);
$dindex=chop($_GET['dindex']);
$err400=intval($_GET['err400']);
$err401=intval($_GET['err401']);
$err403=intval($_GET['err403']);
$err404=intval($_GET['err404']);
$err405=intval($_GET['err405']);
$err500=intval($_GET['err500']);
$access_log=intval($_GET['access_log']);
if (empty($access_log)) $access_log=0;
$error_log=intval($_GET['error_log']);
if (empty($error_log)) $error_log=0;
$limit_dir=chop($_GET['limit_dir']);
if (empty($limit_dir)) $limit_dir=1;
$ruser=chop($_GET['ruser']);
$rewrite=chop($_GET['rewrite']);
$port=chop($_GET['port']);
if (empty($port)) $port=80;
$uid=intval($_GET['uid']);
if (empty($uid)) $uid=0;
$conn=intval($_GET['conn']);
if (empty($conn)) $conn=0;
$bw=intval($_GET['bw']);
if (empty($bw)) $bw=0;
$a_filetype=chop($_GET['a_filetype']);
if (empty($a_filetype)) $a_filetype="";
$a_url=str_replace("http://","",chop($_GET['a_url']));
if (empty($a_url)) $a_url="";
$d_url=str_replace("http://","",chop($_GET['d_url']));
if (empty($d_url)) $d_url="";
$re_dir=intval($_GET['re_dir']);
if (empty($re_dir)) $re_dir=0;
$re_url=str_replace("http://","",chop($_GET['re_url']));
if (empty($re_url)) $re_url="";
$gzip=intval($_GET['gzip']);
if (empty($gzip)) $gzip=1;
$expires=intval($_GET['expires']);
if (empty($expires)) $expires=1;
//if ($api_key!==md5($domain.$api_pass)) { echo "key err";exit;}//

if ($act=="add") {
	if (empty($vhost_dir)) $vhost_dir=$domain;
	if (substr($vhost_dir,0,1)=="/")
		$wvhost_dir=str_replace(".","_",$vhost_dir);
	else
		$wvhost_dir=$web_home."/".str_replace(".","_",$vhost_dir);
	wdl_vhostdir_check($wvhost_dir,1);
		$rtime=time();
	$query = $db->query("insert into wd_site(id,uid,domain,domains,domainss,vhost_dir,limit_dir,conn,bw,dir_index,gzip,expires,a_filetype,a_url,d_url,err400,err401,err403,err404,err405,err500,err503,re_dir,re_url,access_log,error_log,ruser,rewrite,port,rtime,state) values
(NULL,'$uid','$domain','$domains','$domainss','$wvhost_dir','$limit_dir','$conn','$bw','$dindex','$gzip','$expires','$a_filetype','$a_url','$d_url','$err400','$err401','$err403','$err404','$err405','$err500','$err503','$re_dir','$re_url','$access_log','$error_log','$ruser','$rewrite','$port','$rtime','0');");
	//exit;
	$sid=$db->insert_id();
	update_vhost($sid);
	optlog($wdcp_uid,"增加站点 $domain",0,0);//
	echo "success";
}elseif ($act=="edit") {
	$q=$db->query("select * from wd_site where domain='$domain'");
	if ($db->num_rows($q)==0) {echo "domain not exists";exit;}
	$r=$db->fetch_array($q);
	$id=$r['id'];
	$sql="update wd_site set domains='$domains',domainss='$domainss',dir_index='$dindex',err400='$err400',err401='$err401',err403='$err403',err404='$err404',err405='$err405',err500='$err500',err503='$err503',access_log='$access_log',error_log='$error_log',limit_dir='$limit_dir',ruser='$ruser',rewrite='$rewrite',conn='$conn',bw='$bw',a_filetype='$a_filetype',a_url='$a_url',d_url='$d_url',re_dir='$re_dir',re_url='$re_url',gzip='$gzip',expires='$expires' where domain='$domain'";
	$db->query($sql);
	update_vhost($id);
	optlog($wdcp_uid,"修改站点 $domain",0,0);//
	echo "success";
}elseif ($act=="del") {
	$q=$db->query("select * from wd_site where domain='$domain'");
	if ($db->num_rows($q)==0) {echo "domain not exists";exit;}
	$r=$db->fetch_array($q);
	$id=$r['id'];
	update_vhost_del($id);
	if ($site_dir_del_is==1 and !eregi("public_html",$r['dir'])) @rmdir($r['dir']);
	$db->query("delete from wd_site where id='$id'");
	optlog($wdcp_uid,"删除站点 $domain",0,0);//
	echo "success";
}elseif ($act=="stop") {
	//echo "select * from wd_site where domain='$domain'";
	$q=$db->query("select * from wd_site where domain='$domain'");
	if ($db->num_rows($q)==0) {echo "domain not exists";exit;}
	$r=$db->fetch_array($q);
	$id=$r['id'];
	update_vhost_del($id);
	$re=$db->query("update wd_site set state=1 where domain='$domain'");
	web_reload();
	optlog($wdcp_uid,"暂停站点 $domain",0,0);//
	echo "success";
}elseif ($act=="start") {
	$q=$db->query("select * from wd_site where domain='$domain'");
	if ($db->num_rows($q)==0) {echo "domain not exists";exit;}
	$r=$db->fetch_array($q);
	$id=$r['id'];
	update_vhost($id);
	$re=$db->query("update wd_site set state=0 where domain='$domain'");
	web_reload();
	optlog($wdcp_uid,"开启站点 $domain",0,0);//
	echo "success";
}elseif ($act=="list") {
	//$query=$db->query("select * from wd_site where sdomain=0 order by id");
	$query=$db->query("select * from wd_site order by id");
	$msg="";
	while ($r=$db->fetch_array($query)) {
		$msg.=$r['id']."|".$r['domain']."|".$r['domains']."|".$r['domainss']."|".$r['sdomain']."|".$r['vhost_dir']."|".$r['limit_dir']."|".$r['conn']."|".$r['bw']."|".$r['port']."|".$r['dir_index']."|".$r['gzip']."|".$r['expires']."|".$r['a_filetype']."|".$r['a_url']."|".$r['d_url']."|".$r['err400']."|".$r['err401']."|".$r['err403']."|".$r['err404']."|".$r['err405']."|".$r['err500']."|".$r['re_dir']."|".$r['re_url']."|".$r['access_log']."|".$r['error_log']."|".$r['state']."|".$r['uid']."|".$r['rtime']."\n";
	}
	echo $msg;exit;
}else echo "act err";exit;

?>