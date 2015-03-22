<?php

/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/
error_reporting(0);
//error_reporting(E_ALL);
//error_reporting(E_ERROR|E_WARNING|E_PARSE);
//session_cache_limiter('private,must-revalidate');
session_start();
//echo "11";
//ini_set('date.timezone','Asia/Shanghai');
//date_default_timezone_set('Asia/Shanghai');
@define('WD_ROOT', substr(dirname(__FILE__), 0, -4));
//echo "33";
require_once WD_ROOT . "/inc/fun.inc.php";
require_once WD_ROOT . "/inc/fun.sys.php";
//require_once WD_ROOT."/base/alogin.php";
//require_once WD_ROOT."/data/wd_sys.php";
if (@file_exists(WD_ROOT . "/data/db.inc.php"))
    require_once WD_ROOT . "/data/db.inc.php";
if (@file_exists(WD_ROOT . "/data/dbr.inc.php"))
    require_once WD_ROOT . "/data/dbr.inc.php";
if (@file_exists(WD_ROOT . "/data/sys_conf.php"))
    require_once WD_ROOT . "/data/sys_conf.php";
if (@file_exists(WD_ROOT . "/data/ver.php"))
    require_once WD_ROOT . "/data/ver.php";
require_once WD_ROOT . "/inc/db_mysql.class.php";
require_once WD_ROOT . "/inc/page_class.php";
if (@file_exists(WD_ROOT . "/inc/mysql.func.php"))
    require_once WD_ROOT . "/inc/mysql.func.php";
if (@file_exists(WD_ROOT . "/inc/base.func.php"))
    require_once WD_ROOT . "/inc/base.func.php";
if (@file_exists(WD_ROOT . "/inc/cdn.func.php"))
    require_once WD_ROOT . "/inc/cdn.func.php";
if (@file_exists(WD_ROOT . "/inc/dns.func.php"))
    require_once WD_ROOT . "/inc/dns.func.php";
if (@file_exists(WD_ROOT . "/inc/member.func.php"))
    require_once WD_ROOT . "/inc/member.func.php";
if (@file_exists(WD_ROOT . "/inc/vhost.func.php"))
    require_once WD_ROOT . "/inc/vhost.func.php";
if (@file_exists(WD_ROOT . "/inc/file.func.php"))
    require_once WD_ROOT . "/inc/file.func.php";
if (@file_exists(WD_ROOT . "/inc/ftp.func.php"))
    require_once WD_ROOT . "/inc/ftp.func.php";
if (@file_exists(WD_ROOT . "/inc/sendmail.inc.php"))
    require_once WD_ROOT . "/inc/sendmail.inc.php";
if (@file_exists(WD_ROOT . "/data/dns_license.php"))
    $dns_is = 1;
if (@file_exists(WD_ROOT . "/data/union_is"))
    $wddns_is = 1;


//echo "22";
//require_once WD_ROOT."/login.php";//

$db = new dbstuff;
$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect, true, $dbcharset);

$PHP_SELF = trim($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']);
$c_server_init = cu_server();
$ctime = time();//+28800;
$from_goto = @$_SERVER['HTTP_REFERER'];
$mykey = wdl_encrypt_key();
$demo_ip = array("183.60.137.101", "192.168.1.253");//
//$module_list=wdl_module_list();
if (!isset($module_list)) $module_list = "vhost,mysql,ftp";//$module_list=array("vhost","mysql","ftp");
//$demo_ip="192.168.1.252";
//echo "aa";

if (!isset($web_home))
    $web_home = "/www/web";
if (!isset($phpmyadmin_dir))
    $phpmyadmin_dir = "phpmyadmin";
if (!isset($web_port))
    $web_port = "80";
if (!isset($templates_dir))
    $templates_dir = "templates";
if (!isset($is_flow))
    $is_flow = 0;
if (!isset($is_debug))
    $is_debug = 0;
if (!isset($cookie_time))
    $cookie_time = 1800;//1800//
if (!isset($is_lc))
    $is_lc = 0;
if (!isset($apa_conf_dir))
    $apa_conf_dir = "/www/wdlinux/apache/conf/vhost/";
if (!isset($ngi_conf_dir))
    $ngi_conf_dir = "/www/wdlinux/nginx/conf/vhost/";
if (!isset($backup_home))
    $backup_home = "/www/backup";
if (!isset($trash_home))
    $trash_home = "/www/trash";
//if (!isset($logs_home)) $logs_home="/www/logs";
if (!isset($site_dir_del_is))
    $site_dir_del_is = 0;
if (!isset($ftp_dir_del_is))
    $ftp_dir_del_is = 0;
if (!isset($manager_ip))
    $manager_ip = "";
if (!isset($manager_url))
    $manager_url = "";
if (!isset($mysql_quota_is))
    $mysql_quota_is = 0;
if (!isset($my_cnf_l))
    $my_cnf_l = 0;
if (!isset($web_logs_is))
    $web_logs_is = 0;//
if (!isset($is_reg))
    $is_reg = 0;

if (!isset($web_logs_home))
    $web_logs_home = "/www/web_logs";
if (!isset($web_logs_logrotate))
    $web_logs_logrotate = 0;
if (!isset($web_logs_gz))
    $web_logs_gz = 0;
if (!isset($web_logs_day))
    $web_logs_day = 7;
if (!isset($site_logs_is))
    $site_logs_is = 0;

$wdcp_bk = "/www/wdlinux/wdcp_bk";
$wdcp_bk_cf = $wdcp_bk . "/conf";
$wdcp_bk_sys = $wdcp_bk . "/sys";
$wdcp_bk_def = $wdcp_bk . "/def";

$htpasswd_dir = "/www/wdlinux/etc/htpasswd";//

//os版本
if (!isset($os_rl) or empty($os_rl)) {
    $os_str = @file_get_contents("/etc/issue");
    if (eregi("ubuntu|debian", $os_str)) $os_rl = 2;//
    else $os_rl = 1;
    config_update("os_rl", $os_rl, "OS版本");
    config_updatef();
}
if ($os_rl == 2) {
    $service_cmd = "/usr/sbin/service";
} else {
    $service_cmd = "/sbin/service";
}
/*
if (!isset($web_eng)) {
	if ($_SERVER["SERVER_SOFTWARE"]=="nginx") $web_eng=2;
	elseif ($_SERVER["SERVER_SOFTWARE"]=="Apache" and $_SERVER["SERVER_ADDR"]=="127.0.0.1") $web_eng=3;
	else $web_eng=1;
}
*/
if (!isset($web_eng)) {
    if (@is_dir($apa_conf_dir) and @is_dir($ngi_conf_dir))
        $web_eng = 3;
    elseif (@is_dir($apa_conf_dir))
        $web_eng = 1;
    elseif (@is_dir($ngi_conf_dir))
        $web_eng = 2;
    else $web_eng = 3;
    config_update("web_eng", $web_eng, "web服务引擎");
    config_updatef();
}

//wddns conf
if (!isset($dns_domain))
    $dns_domain = "wddns.net";
if (!isset($dns_ns_list))
    $dns_ns_list = "ns1.wddns.net,ns2.wddns.net";
$dns_ns_lista = explode(",", $dns_ns_list);
$dns_ns_num = sizeof($dns_ns_lista);
$dns_ns1 = $dns_ns_lista[0];
$dns_ns2 = $dns_ns_lista[1];
if (empty($dns_ns1))
    $dns_ns1 = "ns1.wddns.net";
if (empty($dns_ns2))
    $dns_ns2 = "ns2.wddns.net";

//$dns_nst1=explode(",",$dns_ns_list);
//$dns_ns_num=sizeof($dns_nst1);
//$dns_ns_lista=array();
//foreach($dns_nst1 as $dns_nst2)

//echo $dns_ns_num;
if (!isset($dns_ttl))
    $dns_ttl = 3600;
if (!isset($dns_min_ttl))
    $dns_min_ttl = 600;
//if (!isset($dns_ns_num)) $dns_ns_num=0;
//if (!isset($dns_ns_name)) $dns_ns_name="ns";
//
if (!isset($dns_domain_count))
    $dns_domain_count = 0;
if (!isset($dns_records_count))
    $dns_records_count = 0;
if (!isset($dns_ns_ip_list))
    $dns_ns_ip_list = "null";
if (!isset($dns_ns_ip_port))
    $dns_ns_ip_port = 0;
if (!isset($dns_master_ip))
    $dns_master_ip = "127.0.0.1";
if (!isset($dns_master_port))
    $dns_master_port = 0;
//
if (!isset($dns_mon_is))
    $dns_mon_is = 0;
if (!isset($dns_mon_iss))
    $dns_mon_iss = 0;
if (!isset($dns_mon_time))
    $dns_mon_time = 5;
if (!isset($dns_mon_timeout))
    $dns_mon_timeout = 10;
if (!isset($dns_mon_num))
    $dns_mon_num = 3;
if (!isset($dns_mon_auto))
    $dns_mon_auto = 0;
if (!isset($dns_mon_autos))
    $dns_mon_autos = 0;
if (!isset($dns_mon_email))
    $dns_mon_email = "";
if (!isset($dns_mon_tel))
    $dns_mon_tel = "";
if (!isset($dns_domain_audit))
    $dns_domain_audit = 0;
if (!isset($dns_domain_email))
    $dns_domain_email = 0;

if (!isset($dns_query_count_is))
    $dns_query_count_is = 0;
if (!isset($dns_attack_check_is))
    $dns_attack_check_is = 0;
if (!isset($dns_attack_query_num_url))
    $dns_attack_query_num_url = 500;
if (!isset($dns_attack_query_num_ip))
    $dns_attack_query_num_ip = 500;
if (!isset($dns_attack_deny_is))
    $dns_attack_deny_is = 0;
if (!isset($dns_manager_is))
    $dns_manager_is = 0;

if (!isset($dns_ns_mon_is))
    $dns_ns_mon_is = 0;
if (!isset($dns_ns_fail_auto_is))
    $dns_ns_fail_auto_is = 0;
if (!isset($dns_url_is))
    $dns_url_is = 0;
if (!isset($dns_ptr_is))
    $dns_ptr_is = 0;//
if (!isset($dns_file_is))
    $dns_file_is = 0;
//echo "aa";
?>