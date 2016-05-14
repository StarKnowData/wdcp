<?php
/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/
/*
SELECT sum( data_length + index_length ) /1024 /1024 AS total_mb FROM information_schema.tables where table_schema='wdlinux_cn';
total_mb  
117.38788033
*/


set_time_limit(0);
if (substr(php_sapi_name(),0,3) !== 'cli') exit;
require_once "/www/wdlinux/wdcp/inc/common.inc.php";

?>