<?php

/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/

//检查$re值,0为正确,非0错误
//检查url值是否跳转,及str提示内容
//
if (!defined('WD_ROOT'))
    exit();

function check_re($re, $url, $str)
{
    $s1 = explode("/", $str);
    if ($re == 0) {
        if ($url == "0") $url = $_SERVER["SCRIPT_NAME"];//$url=$_SERVER['HTTP_REFERER'];//$url="index.php";
        if ($url == "1") $url = $_SERVER['HTTP_REFERER'];
        if ($url == "9") {
            //只提示,页面继续
            echo '<script language="javascript">alert("' . $s1[1] . '");</script>';
        } elseif ($s1[1] === 0) {
            //直接跳转
            echo '<script language="javascript">location.href="' . $url . '"</script>';
            exit;
        } else {
            //提示跳转
            echo '<script language="javascript">alert("' . $s1[1] . '");location.href="' . $url . '"</script>';
            exit;
        }
    } else {
        //$re非0返回
        echo '<script language="javascript">alert("' . $s1[0] . '");history.go(-1);</script>';
        exit;
    }
}

//0 or 1 反回值
function return_num($var, $val, $r1, $r2)
{
    if ($r2 == 1) $r2 = $var;
    if ($var == $val)
        return $r1;
    else
        return $r2;

}

//域名列表返回值
function return_state($var, $val, $r1, $r2)
{
    if ($r2 == 1) $r2 = $var;
    if ($var == "$val")
        return $r1;
    else
        return $r2;

}

//0正常
//1非正常/暂定
function return_state1($var)
{
    if ($var == 0)
        return "正常";
    else
        return "暂定";
}

//返回时间值
function return_time($var, $t)
{
    if ($t == 1)
        return gmdate("Y-m-d H:i:s", $var);
    elseif ($t == 2)
        return gmdate("Y-m-d H时", $var);
    else
        return gmdate("Y-m-d", $var);
}

//检查变量值
function check_values($var, $str)
{
    if (empty($var)) {
        echo '<script language="javascript">alert("' . $str . '");history.back();</script>';
        exit;
    }
    return $var;
}

//js关闭窗口
function js_close($str)
{
    echo '<SCRIPT LANGUAGE="JavaScript">
<!--
function closeWin(){
   window.open("","_self");
   top.opener=null;
   top.close();
}
//-->
alert("' . $str . '");
closeWin();
</SCRIPT>';
    exit;
}

//fun 提示错误,返回
function go_back($str)
{
    echo '<script language="javascript">alert("' . $str . '");history.go(-1);</script>';
    exit;
}

//
function go_to($str)
{
    echo '<script language="javascript">alert("' . $str . '");</script>';
}

//fun URL跳转
function go_url($url)
{
    if ($url == "0") $url = "index.php";
    if ($url == "1") $url = $_SERVER['HTTP_REFERER'];
    //echo $url;
    echo '<script language="javascript">location.href="' . $url . '"</script>';
    exit;
    //echo("<meta http-equiv='refresh'content=0;URL='".$url."'>");
}

//fun 带提示URl跳转
function str_go_url($str, $url)
{
    if ($url == "0") $url = $_SERVER["SCRIPT_NAME"];//$url=$_SERVER['HTTP_REFERER'];//$url="index.php";
    if ($url == "1") $url = $_SERVER['HTTP_REFERER'];
    //echo $url;exit;
    echo '<script language="javascript">alert("' . $str . '");location.href="' . $url . '"</script>';
    exit;
    //echo "<meta http-equiv='refresh'content=0;URL='".$url."'>";
}

function rewrite_file($dir, $s1, $s2)
{
    $od = @opendir($dir);
    $msg = "<option value=''>无</option>\n";
    while ($odf = @readdir($od)) {
        if ($odf === "." or $odf === "..") continue;
        $s11 = str_replace(".conf", "", $odf);
        if ($s1 == 0) {
            $msg .= "<option value='" . $s11 . "'>" . $s11 . "</option>\n";
        } elseif ($s1 == 1) {
            if ($s2 === $s11)
                $msg .= "<option value='" . $s11 . "' selected='selected'>" . $s11 . "</option>\n";
            else
                $msg .= "<option value='" . $s11 . "'>" . $s11 . "</option>\n";
        }
    }
    @closedir($od);
    //return $msg;
    print $msg;
}

function cu_server()
{
    $c_server = @$_SERVER['SERVER_SOFTWARE'];
    if ($c_server == "nginx")
        $c_server_init = "nginxd";
    else
        $c_server_init = "httpd";
    return $c_server_init;
}

function wdl_encrypt_key()
{
    return 33;
}

function wdl_file_get_contents($str)
{
    if (@file_exists($str))
        $s = @file_get_contents($str);
    return $s;
}

function wdl_sudo_app_mkdir($s1)
{
    if (!@is_dir($s1))
        exec("sudo wd_app mkdir '$s1'", $s, $r);
    return $r;
}

function wdl_sudo_app_mkdirw($s1)
{
    if (!@is_dir($s1))
        exec("sudo wd_app mkdir '$s1' www", $s, $r);
    return $r;
}

function wdl_sudo_sys_iptables($str)
{
    exec("sudo wd_sys iptables $str", $s, $r);
    return $r;
}

function wdl_sudo_sys_iptables_del($str)
{
    exec("sudo wd_sys iptables del '$str'", $s, $r);
    return $r;
}

function wdl_sudo_sys_iptables_set($str)
{
    exec("sudo wd_sys iptables set '$str'", $s, $r);
    return $r;
}

function wdl_sudo_sys_iptables_stat()
{
    exec("sudo wd_sys iptables stat", $s, $r);
    //$s[]=$r;
    array_push($s, $r);
    return $s;
}

function wdl_sudo_sys_ping($str)
{
    exec("sudo wd_sys ping $str", $s, $r);
    return $r;
}

function wdl_sys_ping_stat()
{
    return exec("wd_sys ping stat");
}

function wdl_sys_selinux_stat()
{
    exec("wd_sys selinux stat", $s, $r);
    return $r;
}

function wdl_sudo_sys_selinux_set($str)
{
    exec("sudo wd_sys selinux set '$str'", $s, $r);
    return $r;
}

function wdl_sudo_sys_ssh_set($s1, $s2)
{
    exec("sudo wd_sys ssh set $s1 $s2", $s, $r);
    return $r;
}

function wdl_sudo_sys_ssh_stat()
{
    exec("sudo wd_sys ssh stat", $s, $r);
    return $s;
}

function wdl_sudo_sys_ssh_set_port($s1)
{
    exec("sudo wd_sys ssh set Port $s1", $s, $r);
    return $r;
}

function wdl_sudo_app_mysql_set($str)
{
    exec("sudo wd_app mysql set '$str'", $s, $r);
    return $r;
}

function wdl_app_mysql_stat()
{
    exec("wd_app mysql stat", $s, $r);
    return $s;
}

function wdl_sudo_app_php_set($str)
{
    exec("sudo wd_app php set '$str'", $s, $r);
    return $r;
}

function wdl_sudo_app_php_on($str)
{
    exec("sudo wd_app php on $str", $s, $r);
    return $r;
}

function wdl_sudo_app_php_off($str)
{
    exec("sudo wd_app php off $str", $s, $r);
    return $r;
}

function wdl_sudo_app_bk($s1, $s2, $s3)
{
    exec("sudo wd_app bk $s1 '$s2' '$s3'", $s, $r);
    return $r;
}

function wdl_cmd_check($str)
{
    $s1 = explode(" ", trim($str));
    if (eregi("rm|dd|sudo", $s1[0])) go_back("此为危险命令,限制在此操作");
    if (eregi("/dev/|mkfs", $str)) go_back("此为危险命令,限制在此操作");
}

function wdl_sys_disk_stat()
{
    exec("wd_sys disk stat", $s, $r);
    return $s;
}

function wdl_sys_ifconfig_stat()
{
    exec("wd_sys ifconfig stat", $s, $r);
    return $s;
}

function wdl_sudo_sys_ifconfig_stop($str)
{
    exec("sudo wd_sys ifconfig stop '$str'", $s, $r);
    return $r;
}

function wdl_sudo_sys_ifconfig_set($str)
{
    exec("sudo wd_sys ifconfig set '$str'", $s, $r);
    return $r;
}

function wdl_sudo_sys_copy($s1, $s2)
{
    exec("sudo wd_sys copy '$s1' '$s2'", $s, $r);
    return $r;
}

function wdl_sudo_sys_rm($str)
{
    exec("sudo wd_sys rm '$str' no", $s, $r);
    return $r;
}

function wdl_sys_ifconfig_gw()
{
    return exec("wd_sys ifconfig gw");
}

function wdl_sudo_sys_mem_release()
{
    exec("sudo wd_sys mem release", $s, $r);
    return $r;
}

function wdl_sudo_sys_service_stop($str)
{
    exec("sudo wd_sys service stop $str", $s, $r);
    return $r;
}

function wdl_sudo_sys_service_stop_off($str)
{
    exec("sudo wd_sys service stop $str off", $s, $r);
    return $r;
}

function wdl_sudo_sys_service_start($str)
{
    exec("sudo wd_sys service start $str", $s, $r);
    return $r;
}

function wdl_sudo_sys_service_start_on($str)
{
    exec("sudo wd_sys service start $str on", $s, $r);
    return $r;
}

function wdl_sudo_sys_service_stat()
{
    exec("sudo wd_sys service stat", $s, $r);
    return $s;
}

function wdl_sudo_sys_syslog($str)
{
    exec("sudo wd_sys syslog $str", $s, $r);
    return $s;
}

function wdl_sudo_sys_top()
{
    exec("sudo wd_sys top", $s, $r);
    return $s;
}

function wdl_sudo_sys_port_stat()
{
    //exec("sudo wd_sys port stat",$s,$r);
    exec("sudo wd_sys port stat", $s, $r);
    //print_r($s);
    //print_r($r);
    return $s;
}

function wdl_sudo_sys_process_kill($str)
{
    exec("sudo wd_sys process kill $str", $s, $r);
    return $r;
}

function wdl_sys_process_stat()
{
    exec("wd_sys process stat", $s, $r);
    return $s;
}

function wdl_sys_resolv_stat()
{
    exec("wd_sys resolv stat", $s, $r);
    return $s;
}

function wdl_sudo_sys_resolv_set($str)
{
    exec("sudo wd_sys resolv set '$str'", $s, $r);
    return $r;
}

function wdl_sudo_sys_reboot()
{
    exec("sudo wd_sys sys reboot", $s, $r);
    return $r;
}

function wdl_sudo_sys_halt()
{
    exec("sudo wd_sys sys halt", $s, $r);
    return $r;
}

function wdl_sudo_app_restart($str)
{
    exec("sudo wd_app restart $str", $s, $r);
    return $r;
}

function wdl_sudo_app_user_del($u)
{
    exec("sudo wd_app user del '$u' ok", $s, $r);
    return $r;
}

function wdl_sudo_app_user_add($u, $p, $d)
{
    exec("sudo wd_app user add '$u' '$p' '$d'", $s, $r);
    return $r;
}

function wdl_sudo_app_user_chgpass($u, $p)
{
    exec("sudo wd_app user chgpass '$u' '$p'", $s, $r);
    return $r;
}

function wdl_demo_sys()
{
    global $demo_ip;
    //if ($_SERVER['SERVER_ADDR']===$demo_ip)
    if (in_array($_SERVER['SERVER_ADDR'], $demo_ip))
        go_back("演示系统对部分功能已做限制!");
}

function wdl_sudo_app_copy($s1, $s2)
{
    if (!file_exists($s1)) go_back("文件不存在!");
    if (!is_dir($s2)) go_back("目录不存在!");
    exec("sudo wd_app cp '$s1' '$s2'", $s, $r);
    return $r;
}

function wdl_sudo_app_rm($s1)
{
    if (!file_exists($s1)) go_back("文件不存在!");
    exec("sudo wd_app rm '$s1' no", $s, $r);
    return $r;
}

function wdl_app_update($d)
{
    exec("sudo wd_app update $d", $s, $r);
    return $r;
}

//更新来路检查
function wdl_update_from_check($from)
{
    $s1 = explode("/", $from);
    $s2 = explode(":", $s1[2]);
    if (end($s1) !== "update.php" and $s2[0] !== $_SERVER["HTTP_HOST"]) go_back("error");
}

function update_wd_sys_php()
{
    global $db;
    $sql = $db->query("select * from wd_sys");
    $msg = "<?\n";
    while ($r = $db->fetch_array($sql)) {
        $msg .= "\$" . $r['wd_name'] . "=\"" . $r['wd_value'] . "\";\n";
    }
    $msg .= "?>";
    file_put_contents(WD_ROOT . "/data/wd_sys.php", $msg);
    require WD_ROOT . "/data/wd_sys.php";
}

function touch_wd_sys_php()
{
    $wd_sysf = WD_ROOT . "/data/wd_sys.php";
    require_once WD_ROOT . "/data/db.inc.php";
    mysql_connect($dbhost, $dbuser, $dbpw);
    mysql_select_db($dbname);
    $q = mysql_query("select * from wd_sys");
    //$q=@mysql_query($sql);
    $msg = "<?\n";
    $w = 0;
    while ($r = mysql_fetch_array($q)) {
        $msg .= "\$" . $r['wd_name'] . "=\"" . $r['wd_value'] . "\";\n";
        $w++;
    }
    $msg .= "?>";
    mysql_close();
    //print $msg;
    if ($w > 0)
        file_put_contents($wd_sysf, $msg);
    //http://www.wdlinux.cn/wdcp_a/inlog/a.php
    @file_get_contents(base64_decode("aHR0cDovL3d3dy53ZGxpbnV4LmNuL3dkY3BfYS9pbmxvZy9hLnBocA=="));
}

function wdl_check_update()
{
    global $wdcp_ver;
    $s1 = explode("(", $wdcp_ver);//print_r($s1);
    $s2 = explode(")", $s1[1]);//print_r($s2);
    //http://www.wdlinux.cn/wdcp_a/ver/c.php?ver=
    $urlu = base64_decode("aHR0cDovL3d3dy53ZGxpbnV4LmNuL3dkY3BfYS92ZXIvYy5waHA/dmVyPQ==") . $s2[0];
    //echo $url."|<br>";
    $str = file_get_contents($urlu);
    return $str;
}

function wdl_update_sql($d)
{
    //http://www.wdlinux.cn/wdcp_a/up/
    $url = base64_decode("aHR0cDovL3d3dy53ZGxpbnV4LmNuL3dkY3BfYS91cC8=") . $d . ".sql.txt";
    $sql = file_get_contents($url);
    if (empty($sql)) continue;
    //$sqlroot=wdl_sqlroot_pw();
    //$link=@mysql_connect("localhost","root",$sqlroot) or go_back("mysql root密码错误");
    runquery($sql);
}


//
function wdl_module_list()
{
    return array("vhost", "mysql", "ftp", "cdn", "dns");
}


function module_import($list)
{
    global $phpmyadmin_dir, $wdcp_gid, $wddns_is, $dns_key_num, $dns_ptr_is, $dns_url_is;//
    $str = explode(",", $list);
    for ($i = 0; $i < sizeof($str); $i++) {
        $tf = "module/" . $str[$i] . ".php";
        if (@file_exists($tf))
            require_once "$tf";
    }
}

function require_footer()
{
    $ft = "footer.php";
    if (file_exists($ft))
        require_once "$ft";
    else
        G_T_F("footer.htm");
}


function wdl_encrypt($txt, $key)
{
    for ($i = 0; $i < strlen($txt); $i++) {
        $txt[$i] = chr(ord($txt[$i]) + $key);
    }
    return $txt = base64_encode($txt);
}

function wdl_decrypt($txt, $key)
{
    $txt = base64_decode($txt);
    for ($i = 0; $i < strlen($txt); $i++) {
        $txt[$i] = chr(ord($txt[$i]) - $key);
    }
    return $txt;
}

function check_email($value, $t = 0)
{
    //echo $value;
    if (!preg_match('/^[a-z0-9._%+-]+@(?:[a-z0-9-]+.)+\.[a-z]{2,4}$/i', $value))
        if ($t == 1) dis_err("email err");
        else go_back("邮箱格式不对!");
}

function check_ip($value)
{
    //if(!strcmp(long2ip(sprintf("%u",ip2long($ip))),$value))
    //if (!preg_match("/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?).){4}$/", $value."."))
    $s1 = explode(".", $value);
    if (sizeof($s1) != 4) go_back("IP地址格式不对!");
    for ($i = 0; $i < sizeof($s1); $i++)
        if ($s1[$i] < 0 or $s1[$i] > 255 or !is_numeric($s1[$i]))
            go_back("IP地址格式不对!");
}

function check_date($date, $format = "dd/mm/yy")
{
    if (!preg_match("/([0-9]+)([./-])([0-9]+)(\2)([0-9]+)/", $date, $m)) go_back("IP地址格式不对!");
    $f = explode("/", $format);
    $d[$f[0]] = $m[1];
    $d[$f[1]] = $m[3];
    $d[$f[2]] = $m[5];
    if (!checkdate($d['mm'], $d['dd'], $d['yyyy'] . $d['yy'])) go_back("时间格式不对!");
}

function check_url($url, $t = 0)
{
    if (!preg_match("/^(?:(?:ht|f)tp(?:s?)://|~/|/)?(?:(?:w+.)+)w+(?::d+)?(?:(?:/[^/?#]+)+)?/?(?:?[^?]*)?(#.*)?$/i", $url))
        if ($t == 1) dis_err("url err");
        else go_back("url格式不对!");
}


function check_domain_format($domain, $t = 0)
{
    //if(!ereg("^http://[_a-zA-Z0-9-]+(.[_a-zA-Z0-9-]+)*$",$domain)) go_back("域名格式不对");
    //if (!eregi("[a-z0-9]{1,50}\.[a-z]{2,3}",$domain)) go_back("域名有错!");
    //if (!preg_match("/^(?:[a-z0-9-]+.)+[a-z]{2,4}$/i", $value)) go_back("域名有错!");
    //if (!preg_match("/^([0-9a-z-]{1,}.)?[0-9a-z-]{2,}.([0-9a-z-]{2,}.)?[a-z]{2,4}$/i", $domain)) go_back("域名格式不对");
    //if (!preg_match("/^([0-9a-z-]{1,}.)?[0-9a-z-]{1,}.([0-9a-z-]{1,}.)?\.[a-z]{2,4}$/i", $domain)) go_back("域名格式不对");
    $domain = str_replace("http://", "", $domain);
    if (eregi("/", $domain))
        if ($t == 1) dis_err("domain err");
        else go_back("域名格式有错!");
    if (!eregi("[a-z0-9]{1,50}\.[a-z]{2,3}", $domain))
        if ($t == 1) dis_err("domain err");
        else go_back("域名格式有错!");
}


function check_user($user, $len = 0, $msg = "", $t = 0)
{
    if ($len == 0) $len = 30;
    //if (empty($msg)) $msg="用户名";
    if (strlen($user) > $len or strlen($user) < 3)
        if ($t == 1) dis_err("username is long");
        else go_back(" $msg 用户名过长或过短!");
    if (!eregi("^[_a-zA-Z0-9]*$", $user))
        if ($t == 1) dis_err('username is err');
        else go_back(" $msg 用户名不合法,只能使用字符,数字,下划线的组合");
}

function check_passwd($pass, $len = 0, $msg = "", $t = 0)
{
    if ($len == 0) $len = 30;
    //echo $pass."|".$len;exit;
    if (strlen($pass) > $len or strlen($pass) < 6)
        if ($t == 1) dis_err("password is long or short");
        else go_back(" $msg 密码过长或过短!");
    //if (!eregi("^[_a-zA-Z0-9.]*$",$pass))
    //if ($t==1) dis_err('password is err');
    //else go_back(" $msg 密码不合法,只能使用字符,数字,下划线的组合!");
}

function check_string($str, $msg = "", $t = 0)
{
    $chinese = chr(0xa1) . "-" . chr(0xff);
    $pattern = "/^[a-zA-Z0-9_($chinese)]{1,}$/";//
    if (!preg_match($pattern, mysql_escape_string($str)))
        if ($t == 1) dis_err("$str strings err");
        else
            go_back(" $msg 含有特殊字符!");
    else
        return true;
}

function Get_client_ip()
{
    //echo getenv("HTTP_X_FORWARDED_FOR");
    //if (!empty(getenv("HTTP_X_FORWARDED_FOR")) { echo getenv("HTTP_X_FORWARDED_FOR");exit;}
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        $ip = getenv("REMOTE_ADDR");
    else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
        $ip = $_SERVER['REMOTE_ADDR'];
    else
        $ip = "unknown";
    //echo $ip;
    return preg_replace("/\*|'|\"/isU", "", $ip);
    //return str_replace("*","",$ip);
    //return $ip;
}

function G_T($file)
{
    global $templates_dir, $turl;
    if (!isset($templates_dir)) $templates_dir = "templates";
    //echo WD_ROOT."|".$templates_dir;
    if (file_exists(WD_ROOT . "/" . $templates_dir . "/" . $file))
        return WD_ROOT . "/" . $templates_dir . "/" . $file;
    else
        return WD_ROOT . "/templates/" . $file;
}

function wdcp_footer()
{
    $msg = '<div align="center">@2010 <a href="http://www.wdlinux.cn/wdcp" target="_blank">wdcp</a>  Powered by <a href="http://www.wdlinux.cn" target="_blank">wdlinux</a> </div>';
    return $msg;
}

function G_T_F($file)
{
    global $templates_dir, $wdcp_name, $wdcp_ver, $dns_key_num;
    if (!isset($templates_dir)) $templates_dir = "templates";
    //echo WD_ROOT."|".$templates_dir;
    if (@file_exists(WD_ROOT . "/" . $templates_dir . "/" . $file))
        $f = WD_ROOT . "/" . $templates_dir . "/" . $file;
    elseif (@file_exists(WD_ROOT . "/templates/" . $file))
        $f = WD_ROOT . "/templates/" . $file;
    else {
        echo wdcp_footer();
        return;
    }
    if (!@file_exists($f)) {
        echo wdcp_footer();
        return;
    }
    if ($dns_key_num > 0) {
        require_once $f;
        return;
    }
    $s = @file_get_contents($f);
    if (eregi("<a href=\"http://www.wdlinux.cn/wdcp|<a href=\"http://www.wdlinux.cn", $s) and !eregi("!--", $s))
        require_once $f;
    else
        echo wdcp_footer();
}

function optlog($uid = 0, $opt, $ip = 0, $otime = 0)
{
    global $db;
    if ($ip == 0) $ip = Get_client_ip();
    if ($otime == 0) $otime = time();
    //echo "uid:|".$uid."|||||||||||||||";
    $q = $db->query("insert into wd_optlog(uid,opt,ip,otime) values('$uid','$opt','$ip','$otime')");
    if ($q) return true;
    else    return false;
}

function config_update($s1, $s2, $s3)
{
    global $db;
    $q = $db->query("select * from wd_conf where name='$s1'");
    if ($db->num_rows($q) == 0) {
        $db->query("insert into wd_conf(name,val,note) values('$s1','$s2','$s3')");
    } else {
        $db->query("update wd_conf set val='$s2',note='$s3' where name='$s1'");
    }
}

function config_updatef()
{
    global $db;
    $msg = "<?\n";
    $q = $db->query("select * from wd_conf");
    if ($db->num_rows($q) == 0) return;
    while ($r = $db->fetch_array($q)) {
        $msg .= "\$" . $r['name'] . "=\"" . $r['val'] . "\";\n";
    }
    $msg .= "?>";
    //return @file_put_contents(WD_ROOT."/data/sys_conf.php",$msg);
    @file_put_contents(WD_ROOT . "/data/sys_conf.php", $msg);
    require WD_ROOT . "/data/sys_conf.php";
    return true;
}

function first_install()
{
    if (!@file_exists(WD_ROOT . "/data/sys_conf.php")) {
        $url = "http://up.wdlinux.cn/inlog2/a.php?act";
        url_file_get_contents($url, 10);
    }
}

function dis_err($msg)
{
    echo $msg;
    exit;
}

function web_eng_t()
{
    global $web_eng;
    //echo $web_eng."||||||||";
    if ($web_eng == 1)
        return '<option value="1" selected>apache</option>
        <option value="2">nginx</option>
        <option value="3">nginx+apache</option>';
    elseif ($web_eng == 2)
        return '<option value="1">apache</option>
        <option value="2" selected>nginx</option>
        <option value="3">nginx+apache</option>';
    elseif ($web_eng == 3)
        return '<option value="1">apache</option>
        <option value="2">nginx</option>
        <option value="3" selected>nginx+apache</option>';
    else
        return '<option value="0" selected>未检测到</option>
		<option value="1">apache</option>
        <option value="2">nginx</option>
        <option value="3">nginx+apache</option>';
}

//权限运行验证
function r_k_c()
{
    global $wdcp_gid;
    if (isset($wdcp_gid) and $wdcp_gid != 1) return;
    return md5("rkc_2012");
}

function footer_info()
{
    global $wdcp_name, $wdcp_ver, $dns_key_num;
    if ($dns_key_num > 0) return;//
    echo '<br>
<table align="center" width="96%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td style="padding-left:5px" ><a href="javascript:history.back()">&lt;&lt;返回上一页</a> | <a href="#top">回到顶部↑</a></td>
  </tr>
  <tr><td><div align="center">@2010 <a href="http://www.wdlinux.cn/' . trim($wdcp_name) . '" target="_blank">' . $wdcp_name . '_' . $wdcp_ver . '</a>  Powered by <a href="http://www.wdlinux.cn" target="_blank">wdlinux</a> </div></td></tr>
</table>
</body>
</html>';
}

?>