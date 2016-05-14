<?
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


$acc_dir="/www/wdlinux/acc_dir";


$pagenum=15;
if (!isset($_GET['page'])) $start=0;
else	$start=(intval($_GET['page'])-1)*$pagenum;
if ($start<0) $start=0;

if (isset($_POST['Submit'])) {
	$dt=intval($_POST['dt']);
	$domain=chop($_POST['domain']);
	if ($dt==1)
		$wh="domain like '$domain' and";
	else
		$wh="domains like '$domain' and";
}else
	$wh="";

//$query=$db->query("select * from wd_site where ldomain=0 order by id");
if ($wdcp_gid==1) {
	$query=$db->query("select * from wd_site where $wh sdomain=0 order by id");
	$sum=$db->num_rows($query);
	$query=$db->query("select * from wd_site where $wh sdomain=0 order by id limit $start,$pagenum");
}else{
	$query=$db->query("select * from wd_site where $wh uid='$wdcp_uid' and sdomain=0 order by id");
	$sum=$db->num_rows($query);
	$query=$db->query("select * from wd_site where $wh uid='$wdcp_uid' and sdomain=0 order by id limit $start,$pagenum");
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
	$list[$i]['linkc']=$re['linkc'];
	if ($re['re_dir']==1)
		$list[$i]['301']="301";
	elseif ($re['re_dir']==2)
		$list[$i]['301']="302";
	else
		$list[$i]['301']="无";
	$list[$i]['time']=date("Y-m-d H:i",$re['rtime']);
	$list[$i]['user']=uid_name($re['uid']);
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
G_T_F("footer.htm");
?>