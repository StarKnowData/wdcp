<?php

/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/
if (!defined('WD_ROOT')) exit();

//user func

function user_mkey($username)
{
    $k = "WdU_2012.1107";
}

function loginfailed($user, $passwd, $lip = 0, $ltime = 0)
{
    global $db;
    if ($lip == 0) $lip = get_client_ip();
    if ($ltime == 0) $ltime = time();
    //echo $passwd;
    //$passwd=mysql_escape_string(strip_tags($passwd));
    //echo $passwd;
    //$npasswd=substr($passwd,0,2)."******".substr($passwd,-1);
    $npasswd = "********";
    $q = $db->query("insert into wd_loginlog(name,passwd,lip,ltime,state) values('$user','$npasswd','$lip','$ltime',1)");
    if ($q) return true;
    else    return false;
}

function loginlog($user, $lip = 0, $ltime = 0)
{
    global $db;
    if ($lip == 0) $lip = get_client_ip();
    if ($ltime == 0) $ltime = time();
    $user = strip_tags($user);
    $q = $db->query("insert into wd_loginlog(name,lip,ltime,state) values('$user','$lip','$ltime',0)");
    if ($q) return true;
    else    return false;
}

function last_login($user)
{
    global $db;
    //$ip=$_SERVER["REMOTE_ADDR"];
    $ip = get_client_ip();
    $ct = time();
    $user = strip_tags($user);
    $q = $db->query("update wd_member set ltime='$ct',lip='$ip' where name='$user'");
    //if (!$q) go_back("记录错误");
    return $q;
}


function return_uid()
{
    global $db;
    $q = $db->query("select id,name from wd_member");
    $msg = '<select name="uid" id="uid">\n<option value="0">无</option>\n';
    while ($re = $db->fetch_array($q)) {
        $msg .= '<option value="' . $re['id'] . '">' . $re['name'] . '</option>\n';
    }
    $msg .= '</select>';
    return $msg;

}


function gid_name($id)
{
    global $db;
    $id = intval($id);
    $q = $db->query("select name from wd_group where id='$id'");
    $r = $db->fetch_array($q);
    return $r['name'];
}

function uid_name($id)
{
    global $db;
    $id = intval($id);
    if ($id == 0) return "system";
    $q = $db->query("select name from wd_member where id='$id'");
    $r = $db->fetch_array($q);
    return $r['name'];
}

function name_uid($name)
{
    global $db;
    $id = intval($id);
    $name = strip_tags($name);
    $q = $db->query("select id from wd_member where name='$name'");
    $r = $db->fetch_array($q);
    return $r['id'];
}

function group_list($id = 0)
{
    global $db, $wdcp_gid;
    if ($wdcp_gid == 5) return '<option value="10">普通组</option>';
    $q1 = $db->query("select * from wd_group");
    $msg = "";
    while ($r1 = $db->fetch_array($q1)) {
        if ($wdcp_gid > 1 and !empty($r1['level']) and $r1['level'] <= 5) continue;
        if ($id == $r1['id'])
            $msg .= '<option value="' . $r1['id'] . '" selected="selected">' . $r1['name'] . '</option>';
        else
            $msg .= '<option value="' . $r1['id'] . '">' . $r1['name'] . '</option>';
    }
    return $msg;
}

function user_list($id = 0)
{
    global $db;
    $q1 = $db->query("select * from wd_member");
    $msg = "";
    while ($r1 = $db->fetch_array($q1)) {
        if ($id == $r1['id'])
            $msg .= '<option value="' . $r1['id'] . '" selected="selected">' . $r1['name'] . '</option>';
        else
            $msg .= '<option value="' . $r1['id'] . '">' . $r1['name'] . '</option>';
    }
    return $msg;
}

function member_list($id = 0)
{
    global $db, $wdcp_gid, $wdcp_uid;
    if ($wdcp_gid == 1) {
        $q = $db->query("select * from wd_member");
        $msg = '<option value="0">所属用户</option>\n';
    } else {
        $q = $db->query("select * from wd_member where id='$wdcp_uid'");
        $msg = '';
    }
    while ($r = $db->fetch_array($q)) {
        if ($id == $r['id'])
            $msg .= '<option value="' . $r['id'] . '" selected="selected">' . $r['name'] . '</option>\n';
        else
            $msg .= '<option value="' . $r['id'] . '">' . $r['name'] . '</option>\n';
    }
    return $msg;
}


function member_db_field($field, $id)
{
    global $db;
    $q = $db->query("select $field from wd_member where id='$id'");
    $r = $db->fetch_array($q);
    return $r[$field];
}


function user_email($users)
{
    global $db;
    //echo $users."||11<br>";
    $s1 = explode(",", $users);
    //print_r($s1);
    //echo "s1|||||||||||||<br>";
    $userlist = array();
    for ($i = 0; $i < sizeof($s1); $i++) {
        //echo $s1[$i]."||33<br>";
        $q = $db->query("select email from wd_member where name='$s1[$i]'");
        //echo $db->num_rows($q)."||55<br>";
        if ($db->num_rows($q) == 0) continue;
        $r = $db->fetch_array($q);
        //echo $r['email']."||22<br>";
        $userlist[] = $r['email'];
    }
    //print_r($userlist);
    //echo "userlist||||||||||||||||<br>";
    return $userlist;
}

function group_email($id)
{
    global $db, $wdcp_gid, $wdcp_uid;
    $q = $db->query("select * from wd_group where id='$id'");
    if ($db->num_rows($q) == 0) go_back("组ID有错");
    $userlist = array();
    if ($wdcp_gid == 5)
        $q = $db->query("select email from wd_member where pid='$wdcp_uid'");
    else
        $q = $db->query("select email from wd_member where gid='$id'");
    if ($db->num_rows($q) == 0) go_back("没有用户");
    while ($r = $db->fetch_array($q)) {
        //echo $r['email']."<br>";
        if (empty($r['email'])) continue;
        $userlist[] = $r['email'];
    }
    return $userlist;
}

function login_validation()
{
    global $db;
    $ip = get_client_ip();
    $q = $db->query("select ltime from wd_loginlog where lip='$ip' and state=1 order by id desc limit 10,1");
    $r = $db->fetch_array($q);
    $ctime = time();
    if ($ctime - $r['ltime'] < 1800) return 1;
    $q = $db->query("select ltime from wd_loginlog where lip='$ip' and state=1 order by id desc limit 2,1");
    $r = $db->fetch_array($q);
    $ctime = time();
    if ($ctime - $r['ltime'] < 120) return 1;
    else return 0;
}

function check_ckcode($ckcode)
{
    global $is_ck;
    if ($is_ck == 1) {
        session_start();
        if (@strtolower($ckcode) != strtolower($_SESSION['ckcode'])) {
            unset($_SESSION['ckcode']);
            go_back("验证码错误");
        }
    }
}

//cookie
function del_cookie()
{
    setcookie('wdcp_user', '');
    setcookie('wdcp_uid', '');
    setcookie('wdcp_gid', '');
    setcookie('wdcp_us', '');
    //setcookie('wdcp_lt','');
    //setcookie('PHPSESSID','');
    unset($_SESSION['is_l']);
    @session_destroy();
}


function user_l_check($ul_str = 0)
{
    global $wdcp_user, $wdcp_uid, $wdcp_gid, $wdcp_us;
    //$str='wdl_a';
    //echo $wdcp_user."|".$wdcp_uid."|".$wdcp_gid."|".$wdcp_us;
    $str = substr(md5($wdcp_user . $wdcp_uid), 8, 6);
    //echo $str."<br>";
    if ($ul_str == 0) {
        //echo $str;
        //echo $str."<br>";
        //$msg=$str."|".$wdcp_user."|".$wdcp_uid."|".$wdcp_gid."|".$wdcp_us;
        //file_put_contents(WD_ROOT."/data/1.txt",$msg);
        return md5($str . $wdcp_user . $wdcp_uid . $wdcp_gid . $wdcp_us);
    } else {
        //echo ;
        //echo $str."<br>";
        //echo $str."|".$wdcp_user."|".$wdcp_uid."|".$wdcp_gid."|".$wdcp_us."   2<br>";
        //$msg=$str."|".$wdcp_user."|".$wdcp_uid."|".$wdcp_gid."|".$wdcp_us;
        //file_put_contents(WD_ROOT."/data/2.txt",$msg);
        $s1 = md5($str . $wdcp_user . $wdcp_uid . $wdcp_gid . $wdcp_us);
        //echo "1:".$ul_str."|".$s1."<br>";//exit;
        //file_put_contents(WD_ROOT."/data/3.txt",$ul_str);
        //file_put_contents(WD_ROOT."/data/4.txt",$s1);
        if (strcmp($ul_str, $s1) != 0) {
            del_cookie();
            //echo "login err";
            //str_go_url("登录超时!",1);
            //exit;
            echo '<script language="javascript">alert("登录超时!");parent.location="/"</script>';
            exit;
            //go_back("登录信息错误!");
        }
    }
    return true;
}

function gid_to_name($gid)
{
    global $db;
    $q = $db->query("select * from wd_group where id='$gid'");
    if ($db->num_rows($q) == 0) return '组ID不存在';
    $r = $db->fetch_array($q);
    return $r['name'];
}

function gid_to_select($gid = 0)
{
    global $db;
    $q = $db->query("select * from wd_group");
    $msg = "";
    while ($r = $db->fetch_array($q)) {
        if ($gid == $r['id'])
            $msg .= '<option value="' . $r['id'] . '" selected="selected">' . $r['name'] . '</option>' . "\n";
        else
            $msg .= '<option value="' . $r['id'] . '">' . $r['name'] . '</option>' . "\n";
    }
    return $msg;
}

$user_group_list = array("0" => "请选择", "1" => "管理组", "5" => "客服组", "10" => "普通组", "100" => "域名组");

function user_group_level($id)
{
    global $db, $user_group_list;
    foreach ($user_group_list as $k => $v)
        if ($k == $id)
            $msg .= '<option value="' . $k . '"  selected>' . $v . '</option>';
        else
            $msg .= '<option value="' . $k . '">' . $v . '</option>';
    return $msg;
}

function kf_list()
{
    global $db;
    $q = $db->query("select * from wd_group where level=5");
    $msg = '';
    while ($r = $db->fetch_array($q)) {
        $gid = $r['id'];
        //echo $gid."<br>";
        $q1 = $db->query("select * from wd_member where gid='$gid'");
        while ($r1 = $db->fetch_array($q1)) {
            //echo $r1['id']."<br>";
            $msg .= '<option value="' . $r1['id'] . '">' . $r1['name'] . '</option>';
        }
    }
    return $msg;
}

function member_menu()
{
    global $dns_is;
    if ($dns_is == 1)
        return member_menu2();
    else
        return member_menu1();
}

function member_menu1()
{
    global $wdcp_gid;
    if ($wdcp_gid == 1) {
        return "<dl>
    <dt><a href='###' onclick=\"showHide('items5');\" target='_self'>用户管理</a></dt>
    <dd id='items5' style='display:none;'>
			<ul>
<li><a href='member/member.php' target='mainFrame'>用户管理</a></li>
<li><a href='member/chgpasswd.php' target='mainFrame'>修改密码</a></li>
<li><a href='member/group.php' target='mainFrame'>用户组管理</a></li>
          </ul>
		</dd>
</dl>";
    } else {
        return "<dl>
    <dt><a href='###' onclick=\"showHide('items5');\" target='_self'>帐号管理</a></dt>
    <dd id='items5' style='display:none;'>
			<ul>
<li><a href='member/chgpasswd.php' target='mainFrame'>修改密码</a></li>
          </ul>
		</dd>
</dl>";
    }
}

function member_menu2()
{
    global $wdcp_gid;
    if ($wdcp_gid == 1) {
        return "<dl>
    <dt><a href='###' onclick=\"showHide('items5');\" target='_self'>用户管理</a></dt>
    <dd id='items5' style='display:block;'>
			<ul>
<li><a href='memberd/member.php' target='mainFrame'>用户管理</a></li>
<li><a href='member/chgpasswd.php' target='mainFrame'>修改密码</a></li>
<li><a href='memberd/group.php' target='mainFrame'>用户组管理</a></li>
          </ul>
		</dd>
</dl>";
    } elseif ($wdcp_gid >= 5 and $wdcp_gid < 10) {
        return "<dl>
    <dt><a href='###' onclick=\"showHide('items5');\" target='_self'>用户管理</a></dt>
    <dd id='items5' style='display:block;'>
			<ul>
<li><a href='memberd/member.php' target='mainFrame'>用户管理</a></li>
<li><a href='member/chgpasswd.php' target='mainFrame'>修改密码</a></li>
          </ul>
		</dd>
</dl>";
    } else {
        return "<dl>
    <dt><a href='###' onclick=\"showHide('items5');\" target='_self'>帐号管理</a></dt>
    <dd id='items5' style='display:none;'>
			<ul>
<li><a href='member/chgpasswd.php' target='mainFrame'>修改密码</a></li>
<li><a href='member/pay.php' target='mainFrame'>在线支付</a></li>
<li><a href='admin/pay_log.php' target='mainFrame'>支付记录</a></li>
<li><a href='admin/buy_log.php' target='mainFrame'>购买记录</a></li>
<li><a href='memberd/account.php' target='mainFrame'>帐务信息</a></li>
          </ul>
		</dd>
</dl>";
    }
}


function user_yb_money($uid = 0, $t = 0, $price)
{
    global $db, $wdcp_uid;
    //echo $uid."<br>";
    if ($uid === 0) $uid = $wdcp_uid;
    //echo $uid;exit;
    if ($t == 0) go_back("错误");
    //echo "select money,umoney from wd_member where id='$uid' or name='$uid'";//exit;
    $q = $db->query("select money,umoney from wd_member where id='$uid' or name='$uid'");
    if ($db->num_rows($q) == 0) go_back("ID错误");
    $r = $db->fetch_array($q);
    $money = $r['money'];
    //if ($money<$price) go_back("帐户金额不够，请先充值!");
    $umoney = $r['umoney'];
    //echo $money."|".$umoney." ||||||||||||||||||<br>";
    $nmoney = $money - $price;
    $numoney = $umoney + $price;
    $n1money = $money + $price;
    //echo $nmoney."|".$numoney;
    if ($t == 2) {
        $db->query("update wd_member set money='$nmoney',umoney='$numoney' where id='$uid' or name='$uid'");
        //echo "update wd_member set money='$nmoney',umoney='$numoney' where id='$uid' or name='$uid'";
    } else {
        $db->query("update wd_member set money='$n1money' where id='$uid' or name='$uid'");
        //echo "update wd_member set money='$n1money' where id='$uid' or name='$uid'";
    }
}

function buy_charge_log($uid = 0, $title, $price = 0, $charge = 0)
{
    global $db, $wdcp_uid;
    //if ($uid===0) $uid=$wdcdn_uid;
    if (!is_numeric($uid)) $uid = name_uid($uid);
    $rtime = time();
    //$db->query("insert into wd_dns_buylog(uid,pid,did,domain,money,state,rtime) values('$uid','$pid','$did','$domain','$price','$s','$rtime')");
    $db->query("insert into wd_cdn_buylog(uid,bt,title,price,charge,rtime) values('$uid','1','$title','$price','$charge','$rtime')");
}


function pay_save()
{
    global $db, $wdcp_uid, $total_fee, $trade_no, $out_trade_no, $subject, $buyer_email;
    $rtime = time();
    $body = uid_name($wdcp_uid) . " 在线支付 $total_fee 元";
    $db->query("insert into wd_dns_paylog(uid,trade_no,out_trade_no,money,title,note,acc,rtime,state) values('$wdcp_uid','$trade_no','$out_trade_no','$total_fee','$subject','$body','$buyer_email','$rtime','1');");
}

function pay_check()
{
    global $db, $wdcp_uid, $out_trade_no, $trade_no, $total_fee;
    $q = $db->query("select * from wd_dns_paylog where out_trade_no='$out_trade_no' and state=1");
    if ($db->num_rows($q) == 1) {
        $db->query("update wd_dns_paylog set state=0,trade_no='$trade_no' where out_trade_no='$out_trade_no'");

        $r = $db->fetch_array($q);
        $wdcp_uid = $r['uid'];//

        $q = $db->query("select * from wd_member where id='$wdcp_uid'");
        $r = $db->fetch_array($q);
        $ua = $r['money'] + $total_fee;//
        $db->query("update wd_member set money='$ua' where id='$wdcp_uid'");
    }
}

?>