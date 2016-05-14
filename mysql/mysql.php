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

if (!isset($mysql_conf)) $mysql_conf="/www/wdlinux/etc/my.cnf";
//if (!is_writable($mysql_conf)) exec("sudo wd_app check_perm $mysql_conf",$str,$re);
file_is_write($mysql_conf);


if (isset($_POST['Submit2'])) {
	wdl_demo_sys();
	$level=intval($_POST['level']);
	if ($level==0) go_back("未选择!");
	if ($level==1) $cf="/www/wdlinux/wdcp_bk/conf/vps_my.cnf";
	elseif ($level==2) $cf="/www/wdlinux/wdcp_bk/conf/com_my.cnf";
	elseif ($level==3) $cf="/www/wdlinux/wdcp_bk/conf/innodb_my.cnf";
	else go_back("选择有错!");
	if (@file_exists($cf)) {
		@copy($cf,$mysql_conf);
		optlog($wdcp_uid,"设置mysql优化方案",0,0);//
		config_update("my_cnf_l",$level,"my_cnf_l");
		config_updatef();
		str_go_url("设置成功!",'mysql.php');
	}
}


$str=@file_get_contents($mysql_conf);

preg_match("/max_connections(.*)\n/isU",$str,$s1);//print_r($s1);//
$s2=explode("=",$s1[1]);
$max_connections=chop(trim($s2[1]));

preg_match("/wait_timeout(.*)\n/isU",$str,$s3);//print_r($s3);//
$s4=explode("=",$s3[1]);
$wait_timeout=chop(trim($s4[1]));

preg_match("/key_buffer_size(.*)\n/isU",$str,$s5);//print_r($s5);//
$s6=explode("=",$s5[1]);
$key_buffer_size=chop(trim($s6[1]));


preg_match("/query_cache_size(.*)\n/isU",$str,$s7);//print_r($s7);//
$s8=explode("=",$s7[1]);
$query_cache_size=chop(trim($s8[1]));

preg_match("/table_open_cache(.*)\n/isU",$str,$s9);//print_r($s9);//
$s10=explode("=",$s9[1]);
$table_open_cache=chop(trim($s10[1]));

preg_match("/tmp_table_size(.*)\n/isU",$str,$s11);//print_r($s11);//
$s12=explode("=",$s11[1]);
$tmp_table_size=chop(trim($s12[1]));


if (isset($_POST['Submit'])) {
	wdl_demo_sys();
	$max_connections_new=chop($_POST['max_connections']);
	$wait_timeout_new=chop($_POST['wait_timeout']);
	$key_buffer_size_new=chop($_POST['key_buffer_size']);
	$query_cache_size_new=chop($_POST['query_cache_size']);
	$table_open_cache_new=chop($_POST['table_open_cache']);
	$tmp_table_size_new=chop($_POST['tmp_table_size']);
	$str_new=$str;
	$s=0;
	if (strcmp($max_connections,$max_connections_new)!=0) {
		$str_new=preg_replace("/max_connections = $max_connections/isU","max_connections = $max_connections_new",$str_new);
		$max_connections=$max_connections_new;
		$s++;
	}

	if (strcmp($wait_timeout,$wait_timeout_new)!=0) {
		$str_new=preg_replace("/wait_timeout = $wait_timeout/isU","wait_timeout = $wait_timeout_new",$str_new);
		$wait_timeout=$wait_timeout_new;
		$s++;
	}

	if (strcmp($key_buffer_size,$key_buffer_size_new)!=0) {
		$str_new=preg_replace("/key_buffer_size = $key_buffer_size/isU","key_buffer_size = $key_buffer_size_new",$str_new);
		$key_buffer_size=$key_buffer_size_new;
		$s++;
	}

	if (strcmp($query_cache_size,$query_cache_size_new)!=0) {
		$str_new=preg_replace("/query_cache_size = $query_cache_size/isU","query_cache_size = $query_cache_size_new",$str_new);
		$query_cache_size=$query_cache_size_new;
		$s++;
	}

	if (strcmp($table_open_cache,$table_open_cache_new)!=0) {
		$str_new=preg_replace("/table_open_cache = $table_open_cache/isU","table_open_cache = $table_open_cache_new",$str_new);
		$table_open_cache=$table_open_cache_new;
		$s++;
	}

	if (strcmp($tmp_table_size,$tmp_table_size_new)!=0) {
		$str_new=preg_replace("/tmp_table_size = $tmp_table_size/isU","tmp_table_size = $tmp_table_size_new",$str_new);
		$tmp_table_size=$tmp_table_size_new;
		$s++;
	}

	if ($s>0) {
		//echo "aa";
		@file_put_contents($mysql_conf,$str_new);
		optlog($wdcp_uid,"设置mysql优化参数",0,0);//
		str_go_url("设置成功!",'mysql.php');
	}
	
}

function f_my_cnf_l($s) {
	global $my_cnf_l;
	if ($s==$my_cnf_l) return 'selected="selected"';
	else return;
}


require_once(G_T("mysql/mysql.htm"));
//G_T_F("footer.htm");
footer_info();
?>