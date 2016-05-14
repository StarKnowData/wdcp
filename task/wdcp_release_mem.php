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

//if (substr(getcwd(),0,17)!="/www/wdlinux/wdcp") exit;

exec("/bin/sync",$str,$re);
$i=0;
if ($re==0) {
	@file_put_contents("/proc/sys/vm/drop_caches",1);
	@file_put_contents("/proc/sys/vm/drop_caches",2);
	@file_put_contents("/proc/sys/vm/drop_caches",3);
	$i++;
}
$rtime=time();
if ($i>0)
	$msg="内存释放成功";
else
	$msg="内存释放失败";
$db->query("insert into wd_tasklog(name,note,rtime) values('内存释放','$msg',$rtime)");

?>