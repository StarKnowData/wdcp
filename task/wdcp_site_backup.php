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
$backup_site=$backup_home."/site";
if (!is_dir($backup_site)) @mkdir($backup_site, 0755);
$cdate=date("Ymd");

$q=$db->query("select * from wd_site where sdomain=0");
$i=0;
while ($r=$db->fetch_array($q)) {
	//echo $r['domain']."\n";
	$tf=$backup_site."/".str_replace(".","_",$r['domain'])."_".$cdate.".tar.gz";
	$tdir=$r['vhost_dir'];
	exec("tar zcvf $tf $tdir > /dev/null 2>&1",$str,$re);
	$i++;
}

//
exec("find $backup_site -name '*.tar.gz' -type f -mtime +7 -exec rm -f {} \;",$str,$re);

$msg="总共备份$i个站点";
$rtime=time();
$db->query("insert into wd_tasklog(name,note,rtime) values('站点备份','$msg',$rtime)");
?>