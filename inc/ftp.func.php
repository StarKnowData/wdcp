<?php

/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/
if (!defined('WD_ROOT')) exit();

//增加FTP用户
function ftp_user_add($sid, $mid, $user, $password, $vhost_dir, $quotasize = 0, $quotafiles = 0, $ulbandwidth = 0, $dlbandwidth = 0, $t = 0)
{
    global $db;
    $rtime = time();
    $q = $db->query("insert into wd_ftp(sid,mid,user,password,dir,quotasize,quotafiles,ulbandwidth,dlbandwidth,rtime) values ('$sid','$mid','$user','$password','$vhost_dir','$quotasize','$quotafiles','$ulbandwidth','$dlbandwidth','$rtime')");
    if (!$q)
        if ($t == 1) dis_err("add err");
        else go_back("保存失败！");
}

function ftp_user_chg($fid, $password, $quotasize = 0, $quotafiles = 0, $ulbandwidth = 0, $dlbandwidth = 0, $t = 0)
{
    global $db;
    if (is_numeric($fid)) {
        if (empty($password)) {
            //echo "11";
            $q = $db->query("update wd_ftp set quotasize='$quotasize',quotafiles='$quotafiles',ulbandwidth='$ulbandwidth',dlbandwidth='$dlbandwidth' where id='$fid'");
        } else {
            //echo "22";
            $q = $db->query("update wd_ftp set password='$password',quotasize='$quotasize',quotafiles='$quotafiles',ulbandwidth='$ulbandwidth',dlbandwidth='$dlbandwidth' where id='$fid'");
        }
    } else {
        if (empty($password)) {
            //echo "11";
            $q = $db->query("update wd_ftp set quotasize='$quotasize',quotafiles='$quotafiles',ulbandwidth='$ulbandwidth',dlbandwidth='$dlbandwidth' where user='$fid'");
        } else {
            //echo "22";
            $q = $db->query("update wd_ftp set password='$password',quotasize='$quotasize',quotafiles='$quotafiles',ulbandwidth='$ulbandwidth',dlbandwidth='$dlbandwidth' where user='$fid'");
        }
    }
    if (!$q)
        if ($t == 1) dis_err("chg err");
        else go_back("保存失败！");
}

function is_dir_check($dir)
{
    cdir($dir);
    if (!@is_dir($dir)) {
        //exec("sudo wd_app mkdir '$dir'",$str,$re);
        //echo "sys mkdir";
        $ftp_tmp = WD_ROOT . "/data/tmp/ftp.txt";
        @file_put_contents($ftp_tmp, $dir);
        exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php", $str, $re);
        if (@file_exists($ftp_tmp)) @unlink($ftp_tmp);
    }
}

/*
function is_dir_check($dir) {
	//if (!cdir($dir)) {
	if (!is_dir($dir)) {
		exec("sudo wd_app mkdir '$dir'",$str,$re);
	}
}
*/

function sid_to_dir($id)
{
    global $db;
    $q = $db->query("select vhost_dir from wd_site where id='$id'");
    if ($db->num_rows($q) == 0) return;
    $re = $db->fetch_array($q);
    return $re['vhost_dir'];
}

function sid_to_domain($id)
{
    global $db;
    $q = $db->query("select domain from wd_site where id='$id'");
    if ($db->num_rows($q) == 0) return "无站点";
    $re = $db->fetch_array($q);
    return $re['domain'];
}

function check_user_ftp($user, $t = 0)
{
    global $db;
    $q = $db->query("select * from wd_ftp where user='$user'");
    if ($db->num_rows($q) > 0) {
        if ($t == 1) dis_err("user exists");
        else
            go_back("该用户名已存在！");
    }
}

function pureftpd_mysql_repair()
{
    global $dbpw;
    $cf = "/www/wdlinux/etc/pureftpd-mysql.conf";
    $msg = "#
# pureftpd-mysql.conf
# http://www.wdlinux.cn
###
MYSQLSocket     /tmp/mysql.sock
MYSQLServer     localhost
MYSQLPort       3306
MYSQLUser       wdcp
MYSQLPassword   {password}
MYSQLDatabase   wdcpdb
MYSQLCrypt      md5
MYSQLGetPW      SELECT password FROM wd_ftp WHERE user='\L' AND status='0'
MYSQLGetUID     SELECT uid FROM wd_ftp WHERE user='\L' AND status='0'
MYSQLGetGID     SELECT gid FROM wd_ftp WHERE user='\L' AND status='0'
MYSQLGetDir     SELECT dir FROM wd_ftp WHERE user='\L' AND status='0'
MySQLGetQTAFS   SELECT quotafiles FROM wd_ftp WHERE user='\L' AND status='0'
MySQLGetQTASZ   SELECT quotasize FROM wd_ftp WHERE user='\L' AND status='0'
MySQLGetBandwidthUL SELECT ulbandwidth FROM wd_ftp WHERE user='\L' AND status='0'
MySQLGetBandwidthDL SELECT dlbandwidth FROM wd_ftp WHERE user='\L' AND status='0'";
    $str = str_replace("{password}", $dbpw, $msg);
    @file_put_contents($cf, $str);
    return true;
}

?>