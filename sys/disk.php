<?php
/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/

require_once "../inc/common.inc.php";
require_once "../login.php";
require_once "../inc/admlogin.php";
if ($wdcp_gid!=1) exit;


//exec("wd_sys disk stat",$str,$re);
//$str=wdl_sys_disk_stat();
//print_r($str);

$disk_tmp=WD_ROOT."/data/tmp/disk.txt";
@touch($disk_tmp);
exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sv.php",$msg,$re);//print_r($str);print_r($re);
if (@file_exists($disk_tmp)) @unlink($disk_tmp);

/*
$msg=array();
for ($i=1;$i<sizeof($str);$i++) {
	//echo sizeof($str[$i])."<br>";
	$a1=explode("|",$str[$i]);
	//print_r($a1);
	//echo sizeof($a1)."<br>";
	if (sizeof($a1)==1)
		$a2=$str[$i];
	elseif (empty($a1[0])) {
		$a2.=$str[$i];
		$msg[]=$a2;
	}else{
		$msg[]=$str[$i];
	}
}
//print_r($msg);
*/

$list=array();
for ($i=0;$i<sizeof($msg);$i++) {
	$s1=explode("|",$msg[$i]);
	$list[$i][0]=$s1[0];
	$list[$i][1]=$s1[1];
	$list[$i][2]=$s1[2];
	$list[$i][3]=$s1[3];
	$list[$i][4]=$s1[4];
	$list[$i][5]=$s1[5];
}

require_once(G_T("sys/disk.htm"));

//G_T_F("footer.htm");
footer_info();
?>