<?php

/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/
if (!defined('WD_ROOT')) exit();

function wdl_server_name($str)
{
    global $os_rl;
    if ($os_rl == 2) {
        return ltrim(@file_get_contents("/etc/hostname"));
        exit;
    }
    if ($str == 0) {
        $str = wdl_file_get_contents("/etc/sysconfig/network");
    }
    preg_match("/HOSTNAME=(.*)\$/isU", $str, $s1);//print_r($s1);
    $s2 = explode(".", $s1[1]);
    if (eregi("GATEWAY", $s2[0]))
        $s2 = explode("GATEWAY", $s2[0]);
    return str_replace("\"", "", $s2[0]);
}

function wdl_server_load($str)
{
    if ($str == 0) {
        $str = wdl_file_get_contents("/proc/loadavg");
    }
    $s1 = explode(" ", $str);
    //return "1分钟:".$s1[0]."&nbsp;&nbsp;5分钟:".$s1[1]."&nbsp;&nbsp;15分钟:".$s1[2];
    return $s1[0] . "|" . $s1[1] . "|" . $s1[2];
}

function wdl_server_cpu($str)
{
    if ($str == 0) {
        $str = wdl_file_get_contents("/proc/cpuinfo");
    }
    preg_match_all("/physical id(.*)\n/isU", $str, $s1);
    $tt1 = 0;
    for ($i = 0; $i < sizeof($s1[1]); $i++) {
        $s2 = str_replace(":", "", trim($s1[1][$i]));
        if ($i == 0) {
            $t1 = $s2;
            $tt1++;
        }
        if ($t1 != $s2)
            $t11++;
    }
    if (sizeof($s1[1]) == 0)
        $tt1 = "1";
    preg_match_all("/model name(.*)\n/isU", $str, $s1);
    $s2 = sizeof($s1[1]);
    $s3 = str_replace(":", "", trim($s1[1][0]));
    if (eregi("address sizes", $str))
        $bit = "64";
    else
        $bit = "32";
    return $tt1 . "|" . $s2 . "|" . $s3 . "|" . $bit;
}

function wdl_server_version($str)
{
    if ($str == 0) {
        $str = wdl_file_get_contents("/proc/version");
    }
    $s1 = explode(" ", $str);
    if (eregi("x86_64", $str))
        $bit = "64位";
    else
        $bit = "32位";
    //return $s1[0]." ".$s1[2]." ".$bit;
    return $s1[0] . " " . $s1[2];
}

function wdl_server_run_time($str)
{
    if ($str == 0) {
        $str = wdl_file_get_contents("/proc/uptime");
    }
    $s1 = explode(".", $str);
    //天
    $d = intval($s1[0] / 86400);
    $s2 = $s1[0] - $d * 86400;
    //小时
    $s3 = intval($s2 / 3600);
    $s4 = intval($s2 - $s3 * 3600);
    //分
    $s5 = intval($s4 / 60);
    return $d . "天" . $s3 . "小时" . $s5 . "分";
}

//内容资源
//MemTotal|MemFree|Buffers|Cached|SwapTotal|SwapFree
function wdl_server_mem($str)
{
    if ($str == 0) {
        $cf = "/proc/meminfo";
        $str = wdl_file_get_contents($cf);
    }
    preg_match("/MemTotal:(.*) kB/isU", $str, $s1);
    preg_match("/MemFree:(.*) kB/isU", $str, $s2);
    preg_match("/Buffers:(.*) kB/isU", $str, $s3);
    preg_match("/Cached:(.*) kB/isU", $str, $s4);
    preg_match("/SwapTotal:(.*) kB/isU", $str, $s5);
    preg_match("/SwapFree:(.*) kB/isU", $str, $s6);

    //$total=number_format(trim($s1[1])/1024);
    $total = round(trim($s1[1]) / 1024);
    $free = round(trim($s2[1]) / 1024);
    $buffer = round(trim($s3[1]) / 1024);
    $cached = round(trim($s4[1]) / 1024);
    $use = round((trim($s1[1]) - trim($s2[1])) / 1024);
    $swapt = round(trim($s5[1]) / 1024);
    $swapf = round(trim($s6[1]) / 1024);
    $swapu = round((trim($s5[1]) - trim($s6[1])) / 1024);
    //1,2,3,4,5,6,7,8
    return $total . "|" . $use . "|" . $free . "|" . $buffer . "|" . $cached . "|" . $swapt . "|" . $swapu . "|" . $swapf;
}

/*
function wdl_server_mem($str) {
	if ($str==0) {
		$cf="/proc/meminfo";
		$str=wdl_file_get_contents($cf);
	}
	preg_match("/MemTotal:(.*) kB/isU",$str,$s1);
	preg_match("/MemFree:(.*) kB/isU",$str,$s2);
	preg_match("/Buffers:(.*) kB/isU",$str,$s3);
	preg_match("/Cached:(.*) kB/isU",$str,$s4);
	preg_match("/SwapTotal:(.*) kB/isU",$str,$s5);
	preg_match("/SwapFree:(.*) kB/isU",$str,$s6);

	//$total=number_format(trim($s1[1])/1024);
	$total=round(trim($s1[1])/1024);
	$free=round(trim($s2[1])/1024);
	$buffer=round(trim($s3[1])/1024);
	$cached=round(trim($s4[1])/1024);
	$use=round((trim($s1[1])-trim($s2[1]))/1024);
	$swapt=round(trim($s5[1])/1024);
	$swapf=round(trim($s6[1])/1024);
	$swapu=round((trim($s5[1])-trim($s6[1]))/1024);
	//1,2,3,4,5,6,7,8
	return $total."|".$use."|".$free."|".$buffer."|".$cached."|".$swapt."|".$swapu."|".$swapf;
}
*/
?>