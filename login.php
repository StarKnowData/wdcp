<?php
/*
# WDlinux Control Panel,online management of Linux servers, virtual hosts
# Wdcdn system
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcdn
# Last Updated 2011.05
*/
//session_start();
require_once "inc/common.inc.php";
//require_once WD_ROOT."/inc/userinfo.php";
check_deny_ip();
check_login_ip();
if (!empty($manager_url)) {
    $u_f = $_SERVER["SERVER_NAME"];
    $m_u1 = explode(",", $manager_url);
    if (!in_array($u_f, $m_u1)) {
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");
        exit;
    }
}

//print_r($_POST);
//echo "11";
//if (isset($_POST['Submit_login'])) {
if ((isset($_POST['Submit_login_x']) and isset($_POST['Submit_login_y'])) or isset($_POST['Submit_login'])) {
//print_r($_POST);
//if (isset($_POST['username']) and isset($_POST['passwd'])) {
//if (isset($_POST['username']) and isset($_POST['passwd'])) {
    $username = chop(strip_tags($_POST['username']));//echo $username;exit;
    $passwd = chop($_POST['passwd']);
    if (empty($username)) go_back("用户名不能为空!");
    if (empty($passwd)) go_back("密码不能为空!");
    $is_ck = isset($_POST['is_ck']) ? intval($_POST['is_ck']) : 0;
    $ckcode = isset($_POST['ckcode']) ? intval($_POST['ckcode']) : 0;
    if (!isset($cookie_time)) $cookie_time = 1800;// $cookie_time=1800;//
    if ($is_lc == 1) check_ckcode($ckcode);
    if (eregi("@", $username))
        check_email($username);
    else
        check_user($username);

    //登录三次密码错误将锁定半小时

    check_passwd($passwd);
    //unset($mpasswd);
    //echo $passwd;////
    $mpasswd = md5($passwd);
    //echo "select * from wd_member where name='$username' and passwd='$mpasswd'";
    if (eregi("@", $username))
        $q = $db->query("select * from wd_member where email='$username' and passwd='$mpasswd'");
    else
        $q = $db->query("select * from wd_member where name='$username' and passwd='$mpasswd'");
    //echo "select * from wd_member where passwd='$mpasswd'";exit;
    //$q=$db->query("select * from wd_member where passwd='$mpasswd'");
    if ($db->num_rows($q) != 1) {
        loginfailed($username, $passwd, 0, 0);
        str_go_url("用户或密码错误1！", 0);
        exit;
    }
    $r = $db->fetch_array($q);
    if (eregi("@", $username))
        $cname = $r['email'];
    else
        $cname = $r['name'];
    if (strcmp(md5($username), md5($cname)) != 0) {
        loginfailed($username, $passwd, 0, 0);
        str_go_url("用户或密码错误2！", 0);
        exit;
    }
    //echo $cname;
    //print_r($r);
    $q1 = $db->query("select * from wd_group where id='$r[gid]'");
    if ($db->num_rows($q1) == 0)
        //if ($r['gid']==1) $r1['level']=1;
        //else $r['level']=10;
        $r1['level'] = $r['gid'];
    else
        $r1 = $db->fetch_array($q1);
    if (empty($r1['level'])) $r1['level'] = $r['gid'];

    //echo $r['name'];
    setcookie('wdcp_user', $r['name'], time() + $cookie_time, '/');
    setcookie('wdcp_uid', $r['id'], time() + $cookie_time, '/');
    setcookie('wdcp_gid', $r1['level'], time() + $cookie_time, '/');
    setcookie('wdcp_ggid', $r['gid'], time() + $cookie_time, '/');
    setcookie('wdcp_us', $r['state'], time() + $cookie_time, '/');
    //print_r($_COOKIE);
    //echo "<br>";
    $wdcp_user = $r['name'];
    $wdcp_uid = $r['id'];
    $wdcp_gid = $r1['level'];
    $wdcp_ggid = $r['gid'];
    $wdcp_us = $r['state'];
    $wdcp_lt = user_l_check(0);
    //setcookie('wdcp_lt',$wdcp_lt,time() + $cookie_time,'/');
    //if ($r['gid']==1) {
    //session_start();
    unset($_SESSION['is_l']);
    $_SESSION['is_l'] = $wdcp_lt;
    //$sessionId = session_id();      //尝试获取由PHP自身获得的SessionID(来源途径有URL或Cookie)
    //setcookie('PHPSESSID', $sessionId, time() + 1800,'/');
    //setcookie('PHPSESSID', $wdcp_lt, time() + $cookie_time,'/');
    //}
    //last_login($username);//echo $q;exit;
    //print_r($_SESSION);
    loginlog($username, 0, 0);
    /*
    if (empty($_COOKIE["wdcp_gid"])) {
        $_SESSION['wdcp_user']=$wdcp_user;
        $_SESSION['wdcp_uid']=$wdcp_uid;
        $_SESSION['wdcp_gid']=$wdcp_gid;
        $_SESSION['wdcp_ggid']=$wdcp_ggid;
        $_SESSION['wdcp_us']=$wdcp_us;
    }
    */
    //echo substr($from_goto,-1);exit;
    if (empty($from_goto) or eregi("\.html", $from_goto) or substr($from_goto, -1) == "/")//
        go_url("index.php");
    //header("Location:index.php");
    else
        //header("Location:$from_goto");
        go_url($from_goto);
}

if (isset($_GET['act']) and $_GET['act'] == "logout") {
    del_cookie();
    //exit;
    go_url(0);
}
//echo $_COOKIE['wdcp_user']."|".$_COOKIE['wdcp_uid']."|".$_COOKIE['wdcp_gid']."|".$_COOKIE['wdcp_us']."<br>";
//if (isset($_COOKIE['wdcp_user']) and ($_COOKIE['wdcp_user']!="deleted")) {
if (isset($_SESSION['is_l'])) {
    $wdcp_user = $_COOKIE['wdcp_user'];
    $wdcp_uid = $_COOKIE['wdcp_uid'];
    $wdcp_gid = $_COOKIE['wdcp_gid'];
    $wdcp_ggid = $_COOKIE['wdcp_ggid'];
    $wdcp_us = $_COOKIE['wdcp_us'];
    //$wdcp_lt=$_COOKIE['wdcp_lt'];
    //session_start();
    //print_r($_SESSION);
    $wdcp_lt = $_SESSION['is_l'];
    /*
    if (empty($_COOKIE["wdcp_gid"])) { //20130513
        $wdcp_user=$_SESSION['wdcp_user'];
        $wdcp_uid=$_SESSION['wdcp_uid'];
        $wdcp_gid=$_SESSION['wdcp_gid'];
        $wdcp_ggid=$_SESSION['wdcp_ggid'];
        $wdcp_us=$_SESSION['wdcp_us'];
    }
    */

    //echo "wdcp_lt:".$wdcp_lt;echo "<br>";
    user_l_check($wdcp_lt);
    //echo "aa";
    //if ($wdcp_gid==1) {
    //session_start();
    //$sessionId = session_id();
    //setcookie('PHPSESSID', $sessionId, time() + 180,'/');
    //}
    //echo $_COOKIE['wdcp_user']."|".$_COOKIE['wdcp_uid']."|".$_COOKIE['wdcp_gid']."|".$_COOKIE['wdcp_us']."<br>";
    //echo $wdcp_user."|".$wdcp_uid."|".$wdcp_gid."|".$wdcp_us;//exit;

} else {
    if (!@file_exists(WD_ROOT . "/data/sys_conf.php")) {
        first_install();
        config_updatef();
        @chmod(WD_ROOT . "/data/sys_conf.php", 0600);
    }
    $lc = @login_validation();
    if ($is_reg == 1)
        $reg_url = '<a href="register.php"><font color="#000000">注册</font></a>';
    else
        $reg_url = "";
    //echo "33";
    //print_r($_COOKIE);
    require_once(G_T("login.htm"));
    exit;
}
?>