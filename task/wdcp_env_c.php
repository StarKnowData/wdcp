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
exec("ps ax | grep -v grep | grep wdcp_env_c.php | wc -l",$str,$re);
if ($str[0]>1) exit;

$to=$_SERVER['argv'][1];
if (empty($to)) exit;

if (env_to($to))  {
	echo "\n";
	echo "Success\n";
	echo "Current env is $to\n\n";
}//

/*
else{
	echo "\n";
	echo "Error\n\n";	
}
*/