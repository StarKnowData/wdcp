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

if (!isset($php_conf)) $php_conf="/www/wdlinux/etc/php.ini";

//if (!is_writable($php_conf)) exec("sudo wd_app check_perm $php_conf",$str,$re);
file_is_write($php_conf);


if (isset($_GET['act']) and ($_GET['act']=="restart")) {
	wdl_demo_sys();
	web_restart();
	str_go_url("重起成功!",'php.php');
}

if (isset($_GET['act']) and $_GET['act']=="on") {
	wdl_demo_sys();
	$v=chop($_GET['v']);
	if (!empty($v)) {
		$str=@file_get_contents($php_conf);
		$s1="$v = On";
		//echo $s1;
		$s2=preg_replace("/$v = Off/isU",$s1,$str);
		@file_put_contents($php_conf,$s2);
		optlog($wdcp_uid,"设置php.ini $v",0,0);//
		str_go_url("设置成功!",'php.php');
	}
}elseif (isset($_GET['act']) and $_GET['act']=="off") {
	wdl_demo_sys();
	$v=chop($_GET['v']);
	if (!empty($v)) {
		$str=@file_get_contents($php_conf);
		$s1="$v = Off";
		$s2=preg_replace("/$v = On/isU",$s1,$str);
		@file_put_contents($php_conf,$s2);
		optlog($wdcp_uid,"设置php.ini $v",0,0);//
		str_go_url("设置成功!",'php.php');
	}
}else;


$str=@file_get_contents($php_conf);

preg_match("/memory_limit(.*);/isU",$str,$s1);//print_r($s1);//
$s2=explode("=",$s1[1]);
$memory_limit=chop(trim($s2[1]));
//echo $memory_limit;

preg_match("/post_max_size(.*);/isU",$str,$s3);//print_r($s3);
$s4=explode("=",$s3[1]);
$post_max_size=chop(trim($s4[1]));

preg_match("/upload_max_filesize(.*);/isU",$str,$s5);//print_r($s5);
$s6=explode("=",$s5[1]);
$upload_max_filesize=chop(trim($s6[1]));

preg_match("/max_execution_time(.*);/isU",$str,$s7);//print_r($s7);
$s8=explode("=",$s7[1]);
$max_execution_time=chop(trim($s8[1]));

preg_match("/disable_functions(.*);/isU",$str,$s9);//print_r($s9);echo "aa";
$s10=explode("=",$s9[1]);
$disable_functions=chop(trim($s10[1]));


if (isset($_POST['Submit_u'])) {
	wdl_demo_sys();
	$memory_limit_new=chop($_POST['memory_limit']);
	$post_max_size_new=chop($_POST['post_max_size']);
	$upload_max_filesize_new=chop($_POST['upload_max_filesize']);
	$max_execution_time_new=chop($_POST['max_execution_time']);
	$disable_functions_new=chop($_POST['disable_functions']);
	//echo $memory_limit."|".$memory_limit_new."|";
	$s=0;
	$str_new=$str;
	if (strcmp($memory_limit,$memory_limit_new)!=0) {
		//echo "11";
		$str_new=preg_replace("/memory_limit = $memory_limit/isU","memory_limit = $memory_limit_new",$str_new);
		$memory_limit=$memory_limit_new;
		$s++;
	}
	if (strcmp($post_max_size,$post_max_size_new)!=0) {
		$str_new=preg_replace("/post_max_size = $post_max_size/isU","post_max_size = $post_max_size_new",$str_new);
		$post_max_size=$post_max_size_new;
		$s++;
	}
	if (strcmp($upload_max_filesize,$upload_max_filesize_new)!=0) {
		$str_new=preg_replace("/upload_max_filesize = $upload_max_filesize/isU","upload_max_filesize = $upload_max_filesize_new",$str_new);
		$upload_max_filesize=$upload_max_filesize_new;
		$s++;
	}
	if (strcmp($max_execution_time,$max_execution_time_new)!=0) {
		$str_new=preg_replace("/max_execution_time = $max_execution_time/isU","max_execution_time = $max_execution_time_new",$str_new);
		$max_execution_time=$$max_execution_time_new;
		$s++;
	}
	if (strcmp($disable_functions,$disable_functions_new)!=0) {
		//echo "|disable_functions = $disable_functions|disable_functions = $disable_functions_new|<br>";
		//preg_match("/disable_functions =/isU",$str_new,$s11);print_r($s11);
		$str_new=preg_replace("/disable_functions =$disable_functions/isU","disable_functions =$disable_functions_new",$str_new);
		$disable_functions=$disable_functions_new;
		//$str_new=preg_replace("/disable_functions =/isU","disable_functions = exec",$str_new);
		//echo $str_new;
		$s++;
	}
	
	if ($s>0) {
		@file_put_contents($php_conf,$str_new);
		optlog($wdcp_uid,"设置php.ini",0,0);//
		str_go_url("设置成功!",'php.php');
	}
}



preg_match("/safe_mode(.*);/isU",$str,$s11);//print_r($s11);
$s12=explode("=",$s11[1]);
$safe_mode=chop(trim($s12[1]));
//echo $safe_mode;

preg_match("/allow_url_fopen(.*);/isU",$str,$s13);//print_r($s13);
$s14=explode("=",$s13[1]);
$allow_url_fopen=chop(trim($s14[1]));

preg_match_all("/display_errors =(.*);/isU",$str,$s15);//print_r($s15);
//$s16=explode("=",
$display_errors=chop(trim($s15[1][1]));
//echo $display_errors;

preg_match_all("/register_globals =(.*);/isU",$str,$s17);//print_r($s17);//
$register_globals=chop(trim($s17[1][0]));
//echo "|".$register_globals."|";

if ($safe_mode=="On")
	$safe_mode_v='开 <a href="'.$PHP_SELF.'?act=off&v=safe_mode">关</a>';
else
	$safe_mode_v='关 <a href="'.$PHP_SELF.'?act=on&v=safe_mode">开</a>';
	
if ($allow_url_fopen=="On")
	$allow_url_fopen_v='开 <a href="'.$PHP_SELF.'?act=off&v=allow_url_fopen">关</a>';
else
	$allow_url_fopen_v='关 <a href="'.$PHP_SELF.'?act=on&v=allow_url_fopen">开</a>';

if ($register_globals=="On")
	$register_globals_v='开 <a href="'.$PHP_SELF.'?act=off&v=register_globals">关</a>';
else
	$register_globals_v='关 <a href="'.$PHP_SELF.'?act=on&v=register_globals">开</a>';

if ($display_errors=="On")
	$display_errors_v='开 <a href="'.$PHP_SELF.'?act=off&v=display_errors">关</a>';
else
	$display_errors_v='关 <a href="'.$PHP_SELF.'?act=on&v=display_errors">开</a>';

require_once(G_T("vhost/php.htm"));
//G_T_F("footer.htm");
footer_info();

?>