<?php

/*
# WDlinux Control Panel,online management of Linux servers, virtual hosts
# Wdcdn system
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcdn
# Last Updated 2011.05
*/

require_once "../inc/common.inc.php";
require_once "../login.php";
require_once "../inc/admlogin.php";
if ($wdcp_gid!=1) exit;

if (!isset($memcached_mem)) $memcached_mem=512;
if (!isset($memcached_port)) $memcached_port=11211;
if (!isset($memcached_conn)) $memcached_conn=5120;

if (!@is_dir("/www/wdlinux/memcached")) $msg='<br><font color=red>还未安装memcached服务</font>,具体安装请看 <a href="http://www.wdlinux.cn/bbs/thread-1373-1-1.html">http://www.wdlinux.cn/bbs/thread-1373-1-1.html</a>';

$cf="/www/wdlinux/etc/memcached.conf";

if (!@is_writable($cf) and @is_dir("/www/wdlinux/memcached")) {//exec("sudo wd_app check_perm $cf",$str,$re);
	$ower_wdcp_tmp=WD_ROOT."/data/tmp/ower_wdcp.txt";
	@file_put_contents($ower_wdcp_tmp,$cf);
	//echo file_get_contents($ower_wdcp_tmp);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);
	if (@file_exists($ower_wdcp_tmp)) @unlink($ower_wdcp_tmp);
}

if (@is_file($cf)) 	$restart='<a href="/sys/service.php?act=restart&srv=memcached">点击重起</a>. ';
else $restart="";

if (isset($_POST['Submit'])) {
	wdl_demo_sys();

	$memcached_mem=intval($_POST['memcached_mem']);
	$memcached_port=intval($_POST['memcached_port']);
	$memcached_conn=intval($_POST['memcached_conn']);
	
	config_update("memcached_mem",$memcached_mem,"memcache使用内存");
	config_update("memcached_port",$memcached_port,"memcache使用端口");
	config_update("memcached_conn",$memcached_conn,"memcache连接数");
	config_updatef();
	$str="# default memcached options\nmem=$memcached_mem\nport=$memcached_port\nconn=$memcached_conn\n";
	@file_put_contents($cf,$str);
	optlog($wdcp_uid,"修改memcached设置",0,0);//
	str_go_url("修改成功!",0);
}

require_once(G_T("admin/memcached.htm"));
//G_T_F("footer.htm");
footer_info();

?>