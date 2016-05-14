<?php
/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/

set_time_limit(0);
if (substr(php_sapi_name(),0,3) !== 'cli') exit;
define('WD_ROOT', substr(dirname(__FILE__), 0, -4));

require_once "/www/wdlinux/wdcp/inc/common.inc.php";
exec("ps ax | grep -v grep | grep wdcp_login_chg.php | wc -l",$str,$re);
if ($str[0]>1) exit;

//$nrpass="wdlinuxcntt";
$nrpass=randstr();
$npasswd=md5($nrpass);
//echo "12\n";
$db->query("update wd_member set passwd='$npasswd' where id='1'");
//echo "\n";
echo "\n";
echo "Change Wdcp Login Password Is OK\n";
echo "Current Wdcp Login Password Is:$nrpass\n";