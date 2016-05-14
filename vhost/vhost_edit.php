<?php
/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/

require_once "../inc/common.inc.php";
//require_once "../inc/vhost.inc.php";
require_once "../login.php";
//require_once "../inc/admlogin.php";
//if ($wdcp_gid!=1) exit;


$vid=intval($_GET['id']);
if (empty($vid)) go_back("输入有错!");
if ($wdcp_gid==1)
	$query=$db->query("select * from wd_site where id='$vid'");
else
	$query=$db->query("select * from wd_site where uid='$wdcp_uid' and id='$vid'");
if ($db->num_rows($query)==0) go_back("ID不存在！");
$re=$db->fetch_array($query);
//print_r($re);
$id=$re['id'];

if (isset($_POST['Submit_edit'])) {
	//echo "POST";
	$sql="";
	$id=chop($_POST['id']);
	$domain=chop($_POST['domain']);
	//$domain=str_replace("http://","",check_values($_POST['domain'],"域名不能为空!"));
	$domains=chop($_POST['domains'])?str_replace("http://","",chop($_POST['domains'])):"";
	$dms1=array_unique(explode(",",$domains));//print_r($dms1);
	$domains=implode(",",$dms1);
	//echo $domains;exit;
	$domainss=chop($_POST['domainss'])?chop($_POST['domainss']):"0";//echo $domainss;
	$dindex=chop($_POST['dindex'])?chop($_POST['dindex']):"";
	$err400=chop($_POST['err400'])?chop($_POST['err400']):"0";
	$err401=chop($_POST['err401'])?chop($_POST['err401']):"0";
	$err403=chop($_POST['err403'])?chop($_POST['err403']):"0";
	$err404=chop($_POST['err404'])?chop($_POST['err404']):"0";
	$err405=chop($_POST['err405'])?chop($_POST['err405']):"0";
	$err500=chop($_POST['err500'])?chop($_POST['err500']):"0";
	$err503=chop($_POST['err503'])?chop($_POST['err503']):"0";
	$access_log=chop($_POST['access_log'])?chop($_POST['access_log']):"0";
	$error_log=chop($_POST['error_log'])?chop($_POST['error_log']):"0";
	$limit_dir=chop($_POST['limit_dir'])?intval($_POST['limit_dir']):"0";
	if ($wdcp_gid!=1) $limit_dir=1;//
	$dir_list=chop($_POST['dir_list'])?chop($_POST['dir_list']):"0";
	$ruser=chop($_POST['ruser'])?chop($_POST['ruser']):"0";
	$rewrite=chop($_POST['rewrite'])?chop($_POST['rewrite']):"";
	$port=chop($_POST['port']);
	$uip=chop($_POST['uip']);
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
	//echo $expires."|".$re['expires']."<br>";

	//if ($domain!==$re['domain'])
		//$sql="domain='$domain',";
	if ($domains!==$re['domains'])
		$sql.="domains='$domains',";
	if ($domainss!=$re['domainss'])
		$sql.="domainss='$domainss',";
	if ($dindex!==$re['dindex'])
		$sql.="dir_index='$dindex',";
	if ($err400!=$re['err400'])
		$sql.="err400='$err400',";
	if ($err401!=$re['err401'])
		$sql.="err401='$err401',";
	if ($err403!=$re['err403'])
		$sql.="err403='$err403',";
	if ($err404!=$re['err404'])
		$sql.="err404='$err404',";
	if ($err405!=$re['err405'])
		$sql.="err405='$err405',";
	if ($err500!=$re['err500'])
		$sql.="err500='$err500',";
	if ($err503!=$re['err503'])
		$sql.="err503='$err503',";
	if ($access_log!=$re['access_log'])
		$sql.="access_log='$access_log',";
	if ($error_log!=$re['error_log'])
		$sql.="error_log='$error_log',";
	if ($limit_dir!=$re['limit_dir'])
		$sql.="limit_dir='$limit_dir',";
	if ($dir_list!=$re['dir_list'])
		$sql.="dir_list='$dir_list',";
	if ($ruser!=$re['ruser'])
		$sql.="ruser='$ruser',";
	if ($rewrite!==$re['rewrite'])
		$sql.="rewrite='$rewrite',";
	if ($port!=$re['port'])
		$sql.="port='$port',";
	if ($uip!==$re['uip'])
		$sql.="uip='$uip',";
	//
	if ($conn!=$re['conn'])
		$sql.="conn='$conn',";
	if ($bw!=$re['bw'])
		$sql.="bw='$bw',";
	if ($a_filetype!==$re['a_filetype'])
		$sql.="a_filetype='$a_filetype',";
	if ($a_url!==$re['a_url'])
		$sql.="a_url='$a_url',";
	if ($d_url!==$re['d_url'])
		$sql.="d_url='$d_url',";
	if ($re_dir!=$re['re_dir'])
		$sql.="re_dir='$re_dir',";
	if ($re_url!==$re['re_url'])
		$sql.="re_url='$re_url',";
	if ($gzip!=$re['gzip'])
		$sql.="gzip='$gzip',";
	if ($expires!=$re['expires'])
		$sql.="expires='$expires',";
	if ($sql=="") go_back("没有作修改!");
	$sql=substr($sql,0,strlen($sql)-1);
	//update_vhost($id);
	//echo $sql;//exit;
	$sql="update wd_site set $sql,state=0 where id='$id'";
	$db->query($sql);
	update_vhost($id);
	web_reload();
	optlog($wdcp_uid,"修改站点 $domain",0,0);//
	//echo $sql."|<br>";exit;
	str_go_url("修改成功!","vhost_list.php");
	exit;

}
//$vid=intval($_GET['id']);
//if (empty($vid)) go_back("输入有错!");
$query=$db->query("select * from wd_site where id='$vid'");
$re=$db->fetch_array($query);
//print_r($re);//
$port_list=return_web_port($re['port']);
$ip_list=return_web_ip($re['uip']);
require_once(G_T("vhost/vhost_edit.htm"));
//G_T_F("footer.htm");
footer_info();
?>