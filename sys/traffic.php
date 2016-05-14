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

if (@is_dir("/dev/shm"))
	$tdir="/dev/shm/";
else
	$tdir="/www/wdlinux/wdcp/data/tmp/";
	
$f="/proc/net/dev";
$str=@file($f);//
$list=array();
$j=0;
for ($i=2;$i<sizeof($str);$i++) {
//for ($i=2;$i<6;$i++) {
	//echo $str[$i]."<br>";
	//$ft="/dev/shm/".md5(chop($s1[0]))."_t.txt";
	//$fr="/dev/shm/".md5(chop($s1[0]))."_r.txt";
	$ft=$tdir.md5(chop($s1[0]))."_t.txt";
	$fr=$tdir.md5(chop($s1[0]))."_r.txt";


	$s1=explode(":",$str[$i]);
	$s2=explode("|",preg_replace("/\s+/i","|",trim($s1[1])));//print_r($s2);
	$list[$j][]=chop($s1[0]);
	//if ($s2[0]>1099511627776)
	if ($s2[0]>1073741824)
		$list[$j][]=round($s2[0]/(1024*1024*1024),2)."G";
	elseif ($s2[0]>1048576)
		$list[$j][]=round($s2[0]/(1024*1024),2)."M";
	else
		$list[$j][]=round($s2[0]/1024,2)."K";
	$pr=@file_get_contents($fr);
	//echo $pr."-pr<br>";
	if ($pr>0)
		$list[$j][]=round(($s2[0]-$pr)/1024,2)."K/s";
	else
		$list[$j][]=0;
	$list[$j][]=$s2[1];
	$list[$j][]=$s2[2];
	//$list[$j][]=round(($s2[8]/1024/1024)*8,2)."K";
	//if ($s2[8]>1099511627776)
	if ($s2[8]>1073741824)
		$list[$j][]=round($s2[8]/(1024*1024*1024),2)."G";
	elseif ($s2[8]>1048576)
		$list[$j][]=round($s2[8]/(1024*1024),2)."M";
	else
		$list[$j][]=round($s2[8]/1024,2)."K";
	$pt=@file_get_contents($ft);
	//echo $pt."-pt<br>";
	if ($pt>0)
		$list[$j][]=round(($s2[8]-$pt)/1024,2)."K/s";
	else
		$list[$j][]=0;

	$list[$j][]=$s2[9];
	$list[$j][]=$s2[10];
	$j++;
	//echo $pr."|".$pt;
	@file_put_contents($fr,$s2[0]);
	@file_put_contents($ft,$s2[8]);
}
//print_r($list);
//echo $list[0][1];

if ($_GET['act'] == "rt") {
	//print_r($list);
	//$arr=array('ts0'=>"$list[0][1]",'trs0'=>"",'tp0'=>"$list[0][2]",'ter0'=>"$list[0][3]",'rs0'=>"$list[0][4]",'rrs0'=>"",'rp0'=>"$list[0][5]",'rer0'=>"$list[0][6]",'ts1'=>"$list[1][1]",'trs1'=>"",'tp1'=>"$list[1][2]",'ter1'=>"$list[1][3]",'rs1'=>"$list[1][4]",'rrs1'=>"",'rp1'=>"$list[1][5]",'rer1'=>"$list[1][6]",'ts2'=>"$list[2][1]",'trs2'=>"",'tp2'=>"$list[2][2]",'ter2'=>"$list[2][3]",'rs2'=>"$list[2][4]",'rrs2'=>"",'rp2'=>"$list[2][5]",'rer2'=>"$list[2][6]",'ts3'=>"$list[3][1]",'trs3'=>"",'tp3'=>"$list[3][2]",'ter3'=>"$list[3][3]",'rs3'=>"$list[3][4]",'rrs3'=>"",'rp3'=>"$list[3][5]",'rer3'=>"$list[3][6]",'ts4'=>"$list[4][1]",'trs4'=>"",'tp4'=>"$list[4][2]",'ter4'=>"$list[4][3]",'rs4'=>"$list[4][4]",'rrs4'=>"",'rp4'=>"$list[4][5]",'rer4'=>"$list[4][6]",'ts5'=>"$list[5][1]",'trs5'=>"",'tp5'=>"$list[5][2]",'ter5'=>"$list[5][3]",'rs5'=>"$list[5][4]",'rrs5'=>"",'rp5'=>"$list[5][5]",'rer5'=>"$list[5][6]");
	$arr=array();
	//print_r($list);
	for ($i=0;$i<sizeof($list);$i++) {
		$arr['ts'.$i]=$list[$i][5];
		$arr['trs'.$i]=$list[$i][6];
		$arr['tp'.$i]=$list[$i][7];
		$arr['ter'.$i]=$list[$i][8];
		$arr['rs'.$i]=$list[$i][1];
		$arr['rrs'.$i]=$list[$i][2];
		$arr['rp'.$i]=$list[$i][3];
		$arr['rer'.$i]=$list[$i][4];
	}
	//print_r($arr);
	$jarr=json_encode($arr);
	//echo $arr;
	echo $_GET['callback'],'(',$jarr,')';
	exit;
}

require_once(G_T("sys/traffic.htm"));

//G_T_F("footer.htm");
footer_info();
?>