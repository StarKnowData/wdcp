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
if (!@is_dir($backup_home)) exec("mkdir -p $backup_home",$str,$re);
$backup_conf=$backup_home."/conf";
if (!@is_dir($backup_conf)) @mkdir($backup_conf, 0755);
$cdate=date("YmdHis");
//$apache_cf=$cf_dir."/apache_conf";
$tf=$backup_conf."/conf_".$cdate.".tar.gz";
$tdir="";
if (@is_dir("/www/wdlinux/apache/conf"))
	$tdir.=" /www/wdlinux/apache/conf";
if (@is_dir("/www/wdlinux/nginx/conf"))
	$tdir.=" /www/wdlinux/nginx/conf";
$tdir.=" /etc/sysconfig/iptables /etc/ssh/sshd_config";
exec("tar zcvf $tf $tdir  > /dev/null 2>&1",$str,$re);
//
exec("find $backup_conf -name '*.tar.gz' -type f -mtime +7 -exec rm -f {} \;",$str,$re);

$msg="总共备份$i个站点";
$rtime=time();
$db->query("insert into wd_tasklog(name,note,rtime) values('配置文件备份','配置文件备份成功',$rtime)");

?>
