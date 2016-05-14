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

if (isset($_GET['act']) and $_GET['act']=="ntp") {
	$ntp_tmp=WD_ROOT."/data/tmp/ntp.txt";
	@touch($ntp_tmp);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);
	if (@file_exists($ntp_tmp)) @unlink($ntp_tmp);
	optlog($wdcp_uid,"同步时间",0,0);//
	str_go_url("同步成功!",0);	
}

$c_time=date("Y-m-d H:i:s T");

require_once(G_T("sys/time.htm"));

//G_T_F("footer.htm");
footer_info();
?>