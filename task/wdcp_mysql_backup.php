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
//if (!@is_dir($backup_home)) @mkdir($backup_home,0755);//
if (!@is_dir($backup_home)) exec("mkdir -p $backup_home",$str,$re);
$backup_mysql=$backup_home."/mysql";
if (!@is_dir($backup_mysql)) @mkdir($backup_mysql, 0755);
$cdate=date("YmdHis");//

$q=$db->query("select * from wd_mysql where isuser=0");
$i=0;
while ($r=$db->fetch_array($q)) {
	$tf=$backup_mysql."/".$r['dbname']."_".$cdate.".tar.gz";
	$tdir="/www/wdlinux/mysql/var/".$r['dbname'];
	exec("tar zcvf $tf $tdir > /dev/null 2>&1",$str,$re);
	$i++;
}
//
$tf=$backup_mysql."/mysql_".$cdate.".tar.gz";
$tdir="/www/wdlinux/mysql/var/mysql";
exec("tar zcvf $tf $tdir > /dev/null 2>&1",$str,$re);
$i++;
//
$tf=$backup_mysql."/wdcpdb_".$cdate.".tar.gz";
$tdir="/www/wdlinux/mysql/var/wdcpdb";
exec("tar zcvf $tf $tdir > /dev/null 2>&1",$str,$re);
$i++;
//del file
exec("find $backup_mysql -name '*.tar.gz' -type f -mtime +7 -exec rm -f {} \;",$str,$re);

$msg="总共备份$i个数据库";
$rtime=time();
$db->query("insert into wd_tasklog(name,note,rtime) values('数据库备份','$msg',$rtime)");


?>