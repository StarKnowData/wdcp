<?php
/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/

set_time_limit(0);
if (substr(php_sapi_name(),0,3) !== 'cli') exit;

require_once "/www/wdlinux/wdcp/inc/common.inc.php";

if (!isset($backup_home)) $backup_home="/www/backup";
if (!is_dir($backup_home)) exec("mkdir -p $backup_home",$str,$re);
$backup_ftp=$backup_home."/ftp";
if (!is_dir($backup_ftp)) @mkdir($backup_ftp, 0755);
$cdate=date("YmdHis");

$q=$db->query("select * from wd_ftp where sid=0");
//echo $db->num_rows($q)."\n";
$i=0;
while ($r=$db->fetch_array($q)) {
	//echo $r['user']."\n";
	$tf=$backup_ftp."/".$r['user']."_".$cdate.".tar.gz";
	$tdir=$r['dir'];
	exec("tar zcvf $tf $tdir  > /dev/null 2>&1",$str,$re);
	$i++;
}

//
exec("find $backup_ftp -name '*.tar.gz' -type f -mtime +7 -exec rm -f {} \;",$str,$re);

//
$msg="总共备份$i个帐号";
$rtime=time();
$db->query("insert into wd_tasklog(name,note,rtime) values('FTP备份','$msg',$rtime)");

?>