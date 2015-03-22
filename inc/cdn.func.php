<?php

/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/
if (!defined('WD_ROOT')) exit();

//cdn fun
function wd_cdn_update($url)
{
    exec("sudo wd_cdn update '$url'", $str, $re);
    return $re;
}

function wd_cdn_status()
{
    $msg = array();
    exec("sudo wd_cdn status", $str, $re);
    $msg[] = str_replace("Number of clients accessing cache", "缓存客户端", trim($str[0]));
    $msg[] = str_replace("Number of HTTP requests received", "接收的请求数", trim($str[1]));
    $msg[] = str_replace("Average HTTP requests per minute since start", "每分钟处理的请求数", trim($str[2]));
    $msg[] = str_replace("Hits as % of all requests", "缓存命中率(request)", trim($str[3]));
    $msg[] = str_replace("Hits as % of bytes sent", "缓存命中率(bytes sent)", trim($str[4]));
    $s1 = str_replace("StoreEntries", "缓存对像数", trim($str[5]));
    $s2 = explode(" ", $s1);
    $msg[] = $s2[1] . ":	" . $s2[0];
    $s1 = str_replace("StoreEntries with MemObjects", "内存缓存对像数", trim($str[6]));
    $s2 = explode(" ", $s1);
    $msg[] = $s2[1] . ":	" . $s2[0];
    $s1 = str_replace("Hot Object Cache Items", "热点缓存对像", trim($str[7]));
    $s2 = explode(" ", $s1);
    $msg[] = $s2[1] . ":	" . $s2[0];
    return $msg;
}

function wd_cdn_add($url)
{
    exec("sudo wd_cdn add '$url'", $str, $re);
    return $re;
}

function wd_cdn_del($url)
{
    exec("sudo wd_cdn del '$url'", $str, $re);
    return $re;
}


?>