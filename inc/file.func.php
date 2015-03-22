<?php

/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/
if (!defined('WD_ROOT')) exit();

//检查文件可写
function file_is_write($file)
{
    if (is_writable($file)) return;
    $ower_wdcp_tmp = WD_ROOT . "/data/tmp/ower_wdcp.txt";
    @file_put_contents($ower_wdcp_tmp, $file);
    exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php", $str, $re);
    if (@file_exists($ower_wdcp_tmp)) @unlink($ower_wdcp_tmp);
    return $re;
}

//fun 建立目录
function cdir($directoryName)
{
    $directoryName = str_replace("\\", "/", $directoryName);
    $dirNames = explode('/', $directoryName);
    $total = count($dirNames);
    $temp = '';
    if ($total == 0) return false;
    for ($i = 0; $i < $total; $i++) {
        $temp .= $dirNames[$i] . '/';
        if (!is_dir($temp)) {
            $oldmask = umask(0);
            //if (!@mkdir($temp, 0777)) exit("不能建立目录 $temp");
            @mkdir($temp, 0777);
            umask($oldmask);
        }
    }
    return true;
}


function shell_is_dir($dir)
{
    exec("sudo wd_app is_dir '$dir'", $str, $re);
    if ($re == 0)
        return true;
    else
        return false;
}

//php opendir
function php_open_dir($dir)
{
    $fd = @opendir($dir);
    $msg = array();
    while ($buffer = @readdir($fd)) {
        if ($buffer == "." or $buffer == "..") continue;
        //echo $buffer."<br>";
        //$msg[]=$buffer;
        $abuffer = $dir . "/" . $buffer;
        $ftype = @filetype($abuffer);
        //$ower=posix_getpwuid(fileowner($abuffer));
        $ower = posix_getpwuid(@fileowner($abuffer));
        $owerg = posix_getgrgid(@filegroup($abuffer));
        $permission = file_perm($abuffer);
        $mtime = gmdate("Y-m-d H:i", @filemtime($abuffer) + 28800);
        $size = file_size($abuffer);
        //if ($size==0) $size=1;
        //$msg[]=$buffer."|".$ftype."|".$ower['name']."|".$owerg['name']."|".$permission."|".$mtime."|".$size;
        $msg[$buffer] = $buffer . "|" . $ftype . "|" . $ower['name'] . "|" . $owerg['name'] . "|" . $permission . "|" . $mtime . "|" . $size;
    }
    @closedir($fd);
    //print_r($msg);
    @sort($msg);
    $nmsg = array();
    foreach ($msg as $k => $v)
        $nmsg[] = $v;
    return $nmsg;
}

//shell opendir
function shell_open_dir($dir)
{
    exec("sudo wd_app ls '$dir'", $str, $re);
    //print_r($str);
    $msg = array();
    if ($re != 0) go_back("打开目录错误");
    for ($i = 1; $i < sizeof($str); $i++) {
        //echo $str[$i]."<br>";
        $s1 = explode("|", $str[$i]);
        //-rw-r--r--|1|root|root|357|2011-01-16|16:18:53.000000000|+0800|backup.php
        $abuffer = $dir . "/" . $s1[8];
        $ftype = substr($s1[0], 0, 1) === "d" ? "目录" : "文件";//filetype($abuffer);
        $ower = $s1[2];//posix_getpwuid(fileowner($abuffer));
        $owerg = $s1[3];//posix_getgrgid(filegroup($abuffer));
        $permission = $s1[0];//file_perm($abuffer);
        //$mtime=gmdate("Y-m-d H:i",filemtime($abuffer)+28800);
        $mtime = $s1[5] . " " . substr($s1[6], 0, 5);
        $size = afile_size($s1[4]);
        //if ($size==0) $size=1;
        $msg[] = $s1[8] . "|" . $ftype . "|" . $ower . "|" . $owerg . "|" . $permission . "|" . $mtime . "|" . $size;
    }
    return $msg;
}

function file_perm($file)
{
    $perms = @fileperms($file);
    if (($perms & 0xC000) == 0xC000) {
        // Socket
        $info = 's';
    } elseif (($perms & 0xA000) == 0xA000) {
        // Symbolic Link
        $info = 'l';
    } elseif (($perms & 0x8000) == 0x8000) {
        // Regular
        $info = '-';
    } elseif (($perms & 0x6000) == 0x6000) {
        // Block special
        $info = 'b';
    } elseif (($perms & 0x4000) == 0x4000) {
        // Directory
        $info = 'd';
    } elseif (($perms & 0x2000) == 0x2000) {
        // Character special
        $info = 'c';
    } elseif (($perms & 0x1000) == 0x1000) {
        // FIFO pipe
        $info = 'p';
    } else {
        // Unknown
        $info = 'u';
    }

    // Owner
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x') : (($perms & 0x0800) ? 'S' : '-'));

    // Group
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x') : (($perms & 0x0400) ? 'S' : '-'));

    // World
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x') : (($perms & 0x0200) ? 'T' : '-'));
    return $info;
}

function file_size($file)
{
    $file_size = @filesize($file);
    if ($file_size >= 1073741824) {
        $file_size = round($file_size / 1073741824 * 100) / 100 . "g";
    } elseif ($file_size >= 1048576) {
        $file_size = round($file_size / 1048576) . "m";
    } elseif ($file_size >= 1024) {
        $file_size = round($file_size / 1024) . "k";
    } else {
        $file_size = $file_size . "b";
    }
    return $file_size;
}

function afile_size($file_size)
{
    //$file_size = filesize($file);
    if ($file_size >= 1073741824) {
        $file_size = round($file_size / 1073741824 * 100) / 100 . "g";
    } elseif ($file_size >= 1048576) {
        $file_size = round($file_size / 1048576) . "m";
    } elseif ($file_size >= 1024) {
        $file_size = round($file_size / 1024) . "k";
    } else {
        $file_size = $file_size . "b";
    }
    return $file_size;
}

function bfile_size($file)
{
    $file_size = filesize($file);
    if ($file_size >= 1073741824) {
        $file_size = round($file_size / 1073741824 * 100) / 100 . "g";
    } elseif ($file_size >= 1048576) {
        $file_size = round($file_size / 1048576 * 100) / 100 . "m";
    } elseif ($file_size >= 1024) {
        $file_size = round($file_size / 1024 * 100) / 100 . "k";
    } else {
        $file_size = $file_size . "b";
    }
    return $file_size;
}

//url_file_get_contents
function url_file_get_contents($url)
{
    $opts = array('http' => array('method' => "GET", 'timeout' => 30));
    $context = stream_context_create($opts);
    $str = @file_get_contents($url, false, $context);
    return $str;
}

?>