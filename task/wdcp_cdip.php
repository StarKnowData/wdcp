<?php
/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2012.8.9
*/

set_time_limit(0);
if (substr(php_sapi_name(),0,3) !== 'cli') exit;
require_once "/www/wdlinux/wdcp/inc/common.inc.php";
exec("ps ax | grep -v grep | grep wdcp_cdip.php | wc -l",$str,$re);
if ($str[0]>1) exit;
$db->query("delete from wd_loginlog order by id desc limit 3;");
echo "\n";
echo "Clean Deny Ip is OK\n\n";

?>