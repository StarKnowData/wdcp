<?php

if (!defined('WD_ROOT')) exit();

function curl_get_state($url, $proxy, $timeout = 10)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_URL, $url . "/");//
    //curl_setopt($ch,CURLOPT_HEADER,1);
    //curl_setopt($ch,CURLOPT_RETURNTRANSFER,0);
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_exec($ch);
    $state = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $state;
}

function randstr($len = 8)
{
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_';
    // characters to build the password from
    mt_srand((double)microtime() * 1000000 * getmypid());
    // seed the random number generater (must be done)
    $password = '';
    while (strlen($password) < $len)
        $password .= substr($chars, (mt_rand() % strlen($chars)), 1);
    return $password;
}

function check_login_ip()
{
    global $manager_ip;
    if (empty($manager_ip)) return;
    //$l_ip=$_SERVER['REMOTE_ADDR'];
    $l_ip = get_client_ip();//
    //echo $l_ip."<br>";
    $s1 = explode(",", str_replace(".x", "", strtolower($manager_ip)));
    for ($i = 0; $i < sizeof($s1); $i++) {
        //echo $s1[$i]."<br>";
        if ($s1[$i] == "$l_ip" or eregi($s1[$i], $l_ip)) return;
    }
    exit;
}

function check_deny_ip()
{
    global $db, $ctime, $is_ll;
    if ($is_ll == 0) return;
    $lip = get_client_ip();
    //echo $lip."-ip";
    $q = $db->query("select * from wd_loginlog where lip='$lip' and state=1 order by id desc limit 2,1");
    $r = $db->fetch_array($q);
    //$ltime=$r['ltime'];
    if (($ctime - $r['ltime']) < 600) {
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");
        exit;
    }
}

function return_selected($v1, $v2)
{
    if ($v1 == $v2) return 'selected="selected"';
}

function return_radio($v1, $v2)
{
    if ($v1 == $v2) return 'checked';
}

?>