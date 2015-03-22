<?php

/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/
if (!defined('WD_ROOT')) exit();

//dns
/*
function dns_line($str) {
	global $dns_config;
	if ($dns_config['line']=="two") {
		if ($str=="TEL"){
			$msg='  <label>
  <select name="line">
    <option value="TEL" selected="selected">电信</option>
    <option value="ANY">网通</option>
  </select>
  </label>';
			return $msg;
		}else{
			$msg='  <label>
  <select name="line">
    <option value="TEL">电信</option>
    <option value="ANY" selected="selected">网通</option>
  </select>
  </label>';
			return $msg;
		}
	}else{
		return "单线路";
	}
}
*///////


$gl_line_list = array(
    array("any", "默认"),
    array("zall_bot", "搜索引擎"),
    array("zYZZ", "亚洲"),
    array("zOZZ", "欧洲"),
    array("zFZZ", "非洲"),
    array("zDYZ", "大洋洲"),
    array("zBMZ", "北美洲"),
    array("zNMZ", "南美洲"),
    array("zZMZ", "中美洲"),
    array("zAE", "阿拉伯"),
    array("zAR", "阿根廷"),
    array("zAT", "奥地利"),
    array("zAU", "澳大利亚"),
    array("zBE", "比利时"),
    array("zBR", "巴西"),
    array("zCA", "加拿大"),
    array("zCF", "中非共和国"),
    array("zCH", "瑞士"),
    array("zCL", "智利"),
    array("zCN", "中国"),
    array("zCO", "哥伦比亚"),
    array("zCR", "哥斯达黎加"),
    array("zCU", "古巴"),
    array("zCZ", "捷克"),
    array("zDE", "德国"),
    array("zDK", "丹麦"),
    array("zEG", "埃及"),
    array("zES", "西班牙"),
    array("zFI", "芬兰"),
    array("zFR", "法国"),
    array("zGB", "英国"),
    array("zGR", "希腊"),
    array("zHK", "香港"),
    array("zIE", "爱尔兰"),
    array("zIL", "以色列"),
    array("zIN", "印度"),
    array("zIQ", "伊拉克"),
    array("zIR", "伊朗"),
    array("zIT", "意大利"),
    array("zJP", "日本"),
    array("zKP", "朝鲜"),
    array("zKR", "韩国"),
    array("zMO", "澳门"),
    array("zMX", "墨西哥"),
    array("zMY", "马来西亚"),
    array("zNL", "荷兰"),
    array("zNO", "挪威"),
    array("zNZ", "新西兰"),
    array("zPL", "波兰"),
    array("zPT", "葡萄牙"),
    array("zRU", "俄罗斯"),
    array("zSE", "瑞典"),
    array("zSG", "新加坡"),
    array("zSY", "叙利亚"),
    array("zTW", "台湾"),
    array("zUA", "乌克兰"),
    array("zUG", "乌干达"),
    array("zUS", "美国"),
    array("zUZ", "乌兹别克斯坦"),
    array("zVE", "委内瑞拉"),
    array("zVN", "越南"),
    array("zZA", "南非"));


if (@file_exists(WD_ROOT . "/data/dns.inc.php")) require_once WD_ROOT . "/data/dns.inc.php";
if (@file_exists(WD_ROOT . "/data/dns_license.php")) require_once WD_ROOT . "/data/dns_license.php";
if (@file_exists(WD_ROOT . "/data/wddns_ver.php")) require_once WD_ROOT . "/data/wddns_ver.php";
if (@file_exists(WD_ROOT . "/data/dns_nsg.php")) require_once WD_ROOT . "/data/dns_nsg.php";//20120810
if (@file_exists(WD_ROOT . "/data/dns_nsu.php")) require_once WD_ROOT . "/data/dns_nsu.php";
if (@file_exists(WD_ROOT . "/data/dns_pro.php")) require_once WD_ROOT . "/data/dns_pro.php";
if (@file_exists(WD_ROOT . "/inc/union.inc.php")) require_once WD_ROOT . "/inc/union.inc.php";
if ($dns_url_is == 1 and !@file_exists(WD_ROOT . "/data/dns_license.php")) exit;

$def_line_list = array(array("any", "默认"), array("dx", "电信"), array("lt", "联通"), array("edu", "教育网"), array("yd", "移动"), array("td", "铁通"));
if (@file_exists(WD_ROOT . "/data/dns_area.php") and @file_exists(WD_ROOT . "/data/dns_license.php")) {
    //echo check_dns_license(0);exit;
    if (check_dns_license(0) == 0) require_once WD_ROOT . "/data/dns_area.php";
    //if ($def_line_is==1)
    //$def_line_list=$line_list;//
    //else
    $def_line_list = array(array("any", "默认"), array("dx", "电信"), array("lt", "联通"), array("edu", "教育网"), array("yd", "移动"), array("td", "铁通"), array("gd", "广电"), array("gt", "港台"), array("zall_bot", "搜索引擎"), array("for", "海外"));
//}else $line_list=$def_line_list;
}
if (empty($line_list))
    $line_list = $def_line_list;

if (@file_exists(WD_ROOT . "/data/dns_line.php") and @file_exists(WD_ROOT . "/data/dns_license.php")) {
    if ($dns_key_num >= 2) {
        require_once WD_ROOT . "/data/dns_line.php";
        $def_line_list = $line_list;
    }
}
//if (!isset($line_list)) $line_list=array(0=>array("tel","电信"),1=>array("cnc","网通"),2=>array("edu","教育网"),3=>array("any","默认"));
//print_r($line_list);
//echo check_dns_license(0);

/*
function ngid_to_name($ngid) {
	if (!@file_exists(WD_ROOT."/data/dns_nsg.php")) return '未分组';
	global $nsga_list;
	$s1=explode(",",$ngid);
	$msg="";
	for ($i=0;$i<sizeof($s1);$i++) {
		if (!empty($nsga_list[$s1[$i]]))
			$msg.=$nsga_list[$s1[$i]][0]." ";
	}
	if (empty($msg)) $msg="未分组";
	return $msg;
}
*/

function ngid_to_name($ngid)
{
    if (!@file_exists(WD_ROOT . "/data/dns_nsg.php")) return '默认组';
    global $nsga_list;
    return $nsga_list[$ngid]['n'];
}

function ngid_to_select($ngid = '-1')
{
    //if (!@file_exists(WD_ROOT."/data/dns_nsg.php")) return;
    global $nsga_list;
    if (empty($nsga_list))
        return '<option value="0">默认组</option>';

    $msg = '';
    for ($i = 1; $i <= sizeof($nsga_list); $i++) {
        //<input name="ngid[]" type="checkbox" id="ngid[]" value="1" checked>
        if ($ngid == $i)
            $msg .= '<option value="' . $i . '" selected>' . $nsga_list[$i]['n'] . '</option>';
        else
            $msg .= '<option value="' . $i . '">' . $nsga_list[$i]['n'] . '</option>';
    }
    return $msg;
}

function select_pro($pid = 0)
{
    //if (!@file_exists(WD_ROOT."/data/dns_pro.php")) return;
    global $dns_pro;
    if (empty($dns_pro))
        return '<option value="0">默认套餐</option>';
    $msg = '';
    for ($i = 1; $i <= sizeof($dns_pro); $i++) {
        if ($dns_pro[$i]['dtt'] == 1)
            $dtt = "天";
        elseif ($dns_pro[$i]['dtt'] == 2)
            $dtt = "月";
        elseif ($dns_pro[$i]['dtt'] == 3)
            $dtt = "年";
        else;
        if ($pid == $i)
            $msg .= '<option value="' . $i . '" selected>' . $dns_pro[$i]['name'] . ' (' . $dns_pro[$i]['m1'] . '元/' . $dns_pro[$i]['dtl'] . $dtt . ')</option>';
        else
            $msg .= '<option value="' . $i . '">' . $dns_pro[$i]['name'] . ' (' . $dns_pro[$i]['m1'] . '元/' . $dns_pro[$i]['dtl'] . $dtt . ')</option>';
    }
    return $msg;
}

function pid_to_name($pid)
{
    if (!@file_exists(WD_ROOT . "/data/dns_pro.php")) return;
    global $dns_pro;
    if (empty($dns_pro)) return 0;
    return $dns_pro[$pid]['name'];
}

/*
function ngid_to_select($ngid='-1') {
	if (!@file_exists(WD_ROOT."/data/dns_nsg.php")) return;
	global $nsga_list;
	$s1=explode(",",$ngid);
	$na=array();
	for ($i=0;$i<sizeof($s1);$i++) {
		$na[]=$s1[$i];
	}
	$msg="";
	for ($i=0;$i<sizeof($nsga_list);$i++) {
		//<input name="ngid[]" type="checkbox" id="ngid[]" value="1" checked>
		if (in_array($i,$na))
			$msg.='<input name="ngid[]" type="checkbox" id="ngid[]" value="'.$i.'" checked>'.$nsga_list[$i][0].'&nbsp;';
		else
			$msg.='<input name="ngid[]" type="checkbox" id="ngid[]" value="'.$i.'">'.$nsga_list[$i][0].'&nbsp;';
	}
	return $msg;
}
*/

function select_nsg($gid = "-1")
{
    global $nsga_list;
    if (empty($nsga_list)) return;
    $msg = "";
    for ($i = 1; $i <= sizeof($nsga_list); $i++) {
        if ($i == $gid)
            $msg .= '<option value="' . $i . '" selected="selected">' . $nsga_list[$i][0] . '</option>' . "\n";
        else
            $msg .= '<option value="' . $i . '">' . $nsga_list[$i][0] . '</option>' . "\n";
    }
    return $msg;
}

function dns_line($line = '')
{
    global $line_list, $gl_line_list, $def_line_list, $ngid, $nsga_list, $pid, $dns_pro, $dns_key_num;
    //echo $ngid;
    /*
    if ($ngid==0) $line_list=$def_line_list;
    elseif ($ngid==1 or $ngid==3);
    elseif ($ngid==2) $line_list=$gl_line_list;
    else $line_list=$def_line_list;;
    */
    if (eregi("运营|免费", $nsga_list[$ngid]['n'])) $line_list = $def_line_list;
    elseif (eregi("分省|高防", $nsga_list[$ngid]['n'])) ;
    elseif (eregi("海外|全球", $nsga_list[$ngid]['n'])) $line_list = $gl_line_list;
    elseif (eregi("分省|省份|高防|vip", $dns_pro[$pid]['name'])) ;
    elseif ($dns_key_num > 0 and empty($dns_pro)) ;
    else $line_list = $def_line_list;

    $msg = '<select name="line">';
    for ($i = 0; $i < sizeof($line_list); $i++) {
        //echo "|".$line."|".$line_list[$i][0]."|<br>";
        //if ($line===0 or $line===$line_list[$i][0])
        if (empty($line) and $line_list[$i][0] == "any")
            $msg .= '<option value="' . $line_list[$i][0] . '" selected="selected">' . $line_list[$i][1] . '</option>';
        elseif ($line === $line_list[$i][0])
            $msg .= '<option value="' . $line_list[$i][0] . '" selected="selected">' . $line_list[$i][1] . '</option>';
        else
            $msg .= '<option value="' . $line_list[$i][0] . '">' . $line_list[$i][1] . '</option>';
    }
    $msg .= '</select>';
    return $msg;
}


function record_type($str)
{
    switch ($str) {
        case "SOA":
            return $s = 'SOA';
            break;
        case "NS":
            return $s = 'NS';
            break;
        case "CNAME":
            return $s = '        <select name="type1" id="type1">
          <option value="A">A</option>
          <option value="CNAME" selected="selected">CNAME</option>
          <option value="NS">NS</option>
          <option value="MX">MX</option>
		  <option value="TXT">TXT</option>
		  <option value="PTR">PTR</option>
		  <option value="URL1">显性URL</option>
		  <option value="URL2">隐性URL</option>
		  <option value="SRV">SRV</option>
        </select>';
            break;
        case "MX":
            return $s = '        <select name="type1" id="type1">
          <option value="A">A</option>
          <option value="CNAME">CNAME</option>
          <option value="NS">NS</option>
          <option value="MX" selected="selected">MX</option>
		  <option value="TXT">TXT</option>
		  <option value="PTR">PTR</option>
		  <option value="URL1">显性URL</option>
		  <option value="URL2">隐性URL</option>
		  <option value="SRV">SRV</option>
        </select>';
            break;
        case "TXT":
            return $s = '        <select name="type1" id="type1">
          <option value="A">A</option>
          <option value="CNAME">CNAME</option>
          <option value="NS">NS</option>
          <option value="MX">MX</option>
		  <option value="TXT" selected="selected">TXT</option>
		  <option value="PTR">PTR</option>
		  <option value="URL1">显性URL</option>
		  <option value="URL2">隐性URL</option>
		  <option value="SRV">SRV</option>
        </select>';
            break;
        case "PTR":
            return $s = '        <select name="type1" id="type1">
          <option value="A">A</option>
          <option value="CNAME">CNAME</option>
          <option value="NS">NS</option>
          <option value="MX">MX</option>
		  <option value="TXT">TXT</option>
		  <option value="PTR" selected="selected">PTR</option>
		  <option value="URL1">显性URL</option>
		  <option value="URL2">隐性URL</option>
		  <option value="SRV">SRV</option>
        </select>';
            break;
        case "URL1":
            return $s = '        <select name="type1" id="type1">
          <option value="A">A</option>
          <option value="CNAME">CNAME</option>
          <option value="NS">NS</option>
          <option value="MX">MX</option>
		  <option value="TXT">TXT</option>
		  <option value="PTR">PTR</option>
		  <option value="URL1" selected="selected">显性URL</option>
		  <option value="URL2">隐性URL</option>
		  <option value="SRV">SRV</option>
        </select>';
            break;
        case "URL2":
            return $s = '        <select name="type1" id="type1">
          <option value="A">A</option>
          <option value="CNAME">CNAME</option>
          <option value="NS">NS</option>
          <option value="MX">MX</option>
		  <option value="TXT">TXT</option>
		  <option value="PTR">PTR</option>
		  <option value="URL1">显性URL</option>
		  <option value="URL2" selected="selected">隐性URL</option>
		  <option value="SRV">SRV</option>
        </select>';
            break;//
        case "SRV":
            return $s = '        <select name="type1" id="type1">
          <option value="A">A</option>
          <option value="CNAME">CNAME</option>
          <option value="NS">NS</option>
          <option value="MX">MX</option>
		  <option value="TXT">TXT</option>
		  <option value="PTR">PTR</option>
		  <option value="URL1">显性URL</option>
		  <option value="URL2">隐性URL</option>
		  <option value="SRV" selected="selected">SRV</option>
        </select>';
            break;
        default;
            return $s = '        <select name="type1" id="type1">
          <option value="A" selected="selected">A</option>
          <option value="CNAME">CNAME</option>
          <option value="NS">NS</option>
          <option value="MX">MX</option>
		  <option value="TXT">TXT</option>
		  <option value="PTR">PTR</option>
		  <option value="URL1">显性URL</option>
		  <option value="URL2">隐性URL</option>
		  <option value="SRV">SRV</option>
        </select>';
    }

}

function dns_set_sync()
{
    global $db, $dns_ns_ip_list, $dns_ns_ip_port, $dns_query_count_is, $dns_attack_check_is, $dns_attack_query_num_url, $dns_attack_query_num_ip, $dns_attack_deny_is, $dns_key_num, $nsga_list;
    if ($dns_ns_ip_list == "null") return;
    if ($dns_ns_ip_port == 0) $dns_ns_ip_port = 8080;

    if (!empty($nsga_list)) {
        foreach ($nsga_list as $v)
            if (!empty($v['ip']))
                $dns_ns_ip_list1 .= $v['ip'] . ",";
        //for ($i=1;$i<=sizeof($nsga_list);$i++)
        //if (!empty($nsga_list[$i]['ip']))
        //$dns_ns_ip_list1.=$nsga_list[$i]['ip'].",";
        $dns_ns_ip_list = substr($dns_ns_ip_list1, 0, strlen($dns_ns_ip_list1) - 1);
        $ns_ip = explode(",", $dns_ns_ip_list);
        $ns_ip = array_unique($ns_ip);
        $ns_ip = array_values($ns_ip);
        //print_r($ns_ip);
    } else
        $ns_ip = explode(",", $dns_ns_ip_list);
    //echo $dns_ns_ip_list;exit;
    if (sizeof($ns_ip) > 1 and !file_exists(WD_ROOT . "/data/dns_license.php")) go_back("只支持一个IP");
    if (sizeof($ns_ip) > 1 and file_exists(WD_ROOT . "/data/dns_license.php")) check_dns_license();
    //$str="act=set&str=".base64_encode($dns_query_count_is."|".$dns_attack_check_is."|".$dns_attack_query_num_url."|".$dns_attack_query_num_ip."|".$dns_attack_deny_is);
    if ($dns_key_num >= 2)
        $data = base64_encode($dns_key_num . "|" . $dns_query_count_is . "|" . $dns_attack_check_is . "|" . $dns_attack_query_num_url . "|" . $dns_attack_query_num_ip . "|" . $dns_attack_deny_is);
    else
        $data = base64_encode("1");
    for ($i = 0; $i < sizeof($ns_ip); $i++) {
        if (empty($ns_ip[$i])) continue;
        //if ($ns_ip[$i]=="127.0.0.1") continue;
        $url = "http://" . $ns_ip[$i] . ":" . $dns_ns_ip_port . "/api/dns_set.php?act=set&str=" . $data;
        $tn = 0;
        do {
            $re = url_file_get_contents($url, 30);
            $tn++;
        } while ($re != "success" and $tn < 3);
    }
}

function dns_data_update()
{
    global $db, $dns_ns_ip_list, $dns_ns_ip_port, $nsga_list, $dns_url_is, $dns_url_server;
    if ($dns_ns_ip_list == "null") return;
    if ($dns_ns_ip_port == 0) $dns_ns_ip_port = 8080;
    $ns_ip = explode(",", $dns_ns_ip_list);
    $err_list = array();
    //if (sizeof($ns_ip)>1 and !@file_exists(WD_ROOT."/data/dns_license.php")) go_back("只支持一个IP");
    if (sizeof($ns_ip) > 1 and @file_exists(WD_ROOT . "/data/dns_license.php")) check_dns_license();

    //$q=$db->query("select * from wd_dns_update where state=0 and ut=1");
    $q = $db->query("select * from wd_dns_update where state=0");
    while ($r = $db->fetch_array($q)) {
        $okf = 0;
        $oid = $r['id'];
        //pid,ut,act,content,state
        $pid = $r['pid'];
        $ngid = $r['ngid'];
        //add,del,edit,update,start,stop,
        //$content=$r['content'];
        //顶级域名
        if (!empty($nsga_list[$ngid]['ip']))
            $ns_ip = explode(",", $nsga_list[$ngid]['ip']);
        else
            $ns_ip = explode(",", $dns_ns_ip_list);
        if ($r['ut'] == 1) {
            //if ($r['act']=="del") {
            $str = $r['act'] . "|" . $r['content'];
            for ($i = 0; $i < sizeof($ns_ip); $i++) {
                if (empty($ns_ip[$i])) continue;
                if ($ns_ip[$i] == "127.0.0.1") continue;
                //del|domain
                $nurl = "http://" . $ns_ip[$i] . ":" . $dns_ns_ip_port . "/api/dns_domain.php?str=" . base64_encode($str);
                $tn = 0;
                do {
                    $re = url_file_get_contents($nurl, 30);
                    $tn++;
                } while ($re !== "sucess" and $tn < 3);
                if ($re !== "sucess") $err_list[] = $nurl;
            }
            /*
            }else{
                $q1=$db->query("select * from wd_dns_domain where id='$pid'");
                $r1=$db->fetch_array($q1);
                //act|domain|ups|uid
                $str=$r['act']."|".$r1['domain']."|".$r1['ups']."|".$r1['uid'];
                for ($i=0;$i<sizeof($ns_ip);$i++) {
                    if (empty($ns_ip[$i])) continue;
                    $nurl="http://".$ns_ip[$i].":8080/api/dns_domain.php?str=".base64_encode($str);
                    $re=url_file_get_contents($nurl,30);
                }
            }
            */
            //别名
        } elseif ($r['ut'] == 3) {
            if ($dns_url_is == 1 and !empty($dns_url_server)) {
                $q1 = $db->query("select * from wd_dns_url where id='$pid'");
                $r1 = $db->fetch_array($q1);
                ////act|pid|uid|domain|url|state|
                $str = $r['act'] . "|" . $pid . "|" . $r1['uid'] . "|" . $r1['domain'] . "|" . $r1['url'] . "|" . $r1['ut'] . "|" . $r1['uc'] . "|" . $r1['state'];
                //for ($i=0;$i<sizeof($ns_ip);$i++) {
                //if (empty($ns_ip[$i])) continue;
                //if ($ns_ip[$i]=="127.0.0.1") continue;
                //$nurl="http://".$ns_ip[$i].":".$dns_ns_ip_port."/api/dns_url.php?str=".base64_encode($str);
                $nurl = "http://" . $dns_url_server . ":" . $dns_ns_ip_port . "/api/dns_url.php?str=" . base64_encode($str);
                //$tn=0;
                //do {
                $re = url_file_get_contents($nurl, 10);
                //$tn++;
                //}while ($re!=="sucess" and $tn<3);
                //if ($re!=="sucess") $err_list[]=$nurl;
                //}
            }
            //子域名
        } else {
            if ($r['act'] == "del") {
                $str = "del|" . $r['pid'];
                for ($i = 0; $i < sizeof($ns_ip); $i++) {
                    if (empty($ns_ip[$i])) continue;
                    if ($ns_ip[$i] == "127.0.0.1") continue;
                    //del|pid
                    $nurl = "http://" . $ns_ip[$i] . ":" . $dns_ns_ip_port . "/api/dns_records.php?str=" . base64_encode($str);
                    //$re=url_file_get_contents($nurl,30);
                    $tn = 0;
                    do {
                        $re = url_file_get_contents($nurl, 30);
                        $tn++;
                    } while ($re !== "sucess" and $tn < 3);
                    if ($re !== "sucess") $err_list[] = $nurl;
                }
            } elseif ($r['act'] == "upsa") {
                $str = "upsa|" . $r['content'];
                for ($i = 0; $i < sizeof($ns_ip); $i++) {
                    if (empty($ns_ip[$i])) continue;
                    if ($ns_ip[$i] == "127.0.0.1") continue;
                    //del|pid
                    $nurl = "http://" . $ns_ip[$i] . ":" . $dns_ns_ip_port . "/api/dns_records.php?str=" . base64_encode($str);
                    //$re=url_file_get_contents($nurl,30);
                    $tn = 0;
                    do {
                        $re = url_file_get_contents($nurl, 30);
                        $tn++;
                    } while ($re !== "sucess" and $tn < 3);
                    if ($re !== "sucess") $err_list[] = $nurl;
                }
            } else {
                $q1 = $db->query("select * from wd_dns_records where id='$pid'");
                $r1 = $db->fetch_array($q1);
                //act|pid|ups|uid
                //act|pid|uid|zone,host,type,data,ttl,view,mx_priority,primary_ns,second_ns,ups,avil
                $str = $r['act'] . "|" . $pid . "|" . $r1['uid'] . "|" . $r1['zone'] . "|" . $r1['host'] . "|" . $r1['type'] . "|" . $r1['data'] . "|" . $r1['ttl'] . "|" . $r1['view'] . "|" . $r1['mx_priority'] . "|" . $r1['primary_ns'] . "|" . $r1['second_ns'] . "|" . $r1['ups'] . "|" . $r1['avil'] . "|" . $r1['resp_person'];
                for ($i = 0; $i < sizeof($ns_ip); $i++) {
                    if (empty($ns_ip[$i])) continue;
                    if ($ns_ip[$i] == "127.0.0.1") continue;
                    $nurl = "http://" . $ns_ip[$i] . ":" . $dns_ns_ip_port . "/api/dns_records.php?str=" . base64_encode($str);
                    //$re=url_file_get_contents($nurl,30);
                    $tn = 0;
                    do {
                        $re = url_file_get_contents($nurl, 30);
                        $tn++;
                    } while ($re !== "sucess" and $tn < 3);
                    if ($re !== "sucess") $err_list[] = $nurl;
                }
            }
        }
        //if ($okf==0)
        $db->query("update wd_dns_update set state=1 where id='$oid'");
    }
    //
    for ($i = 0; $i < sizeof($err_list); $i++)
        url_file_get_contents($err_list[$i], 30);
    return true;
}

function dns_data_sync($ip)
{
    global $db, $dns_ns_ip_port, $dns_master_port, $nsga_list, $dns_ns_ip_list, $dns_url_is;
    check_dns_license();
    //
    $ngid = 0;
    $wh = "";
    $wh1 = "where ";
    for ($i = 1; $i <= sizeof($nsga_list); $i++) {
        if (!empty($nsga_list[$i]['ip'])) {
            $n1 = explode(",", $nsga_list[$i]['ip']);
            //echo $ip;
            //print_r($n1);
            if (in_array($ip, $n1)) {
                $wh1 .= "ngid=$i or ";
                $ngid = 1;
                //echo "aa";
            }
        }
    }
    if ($ngid == 1) $wh = substr($wh1, 0, strlen($wh1) - 4);
    $nsd = explode(",", $dns_ns_ip_list);
    if (empty($wh) and !in_array($ip, $nsd)) $wh = "where ngid='1000'";//$ngid=1000;
    //echo $wh;exit;
    //echo $ip."|".$ngid;

    $key = md5(time());
    @touch(WD_ROOT . "/data/" . $key . ".txt");
    if ($dns_ns_ip_port == 0) $dns_ns_ip_port = 8080;
    $nurl = "http://" . $ip . ":" . $dns_ns_ip_port . "/api/dns_all.php?key=" . $key . "&mport=" . $dns_master_port;
    //TRUNCATE TABLE `wd_dns_update_log`
    //域名
    $q = $db->query("select * from wd_dns_domain $wh");
    //echo "select * from wd_dns_domain $wh";
    $msg = "";
    //id,uid,domain,stime,ups
    while ($r = $db->fetch_array($q)) {
        $msg .= $r['id'] . "|" . $r['uid'] . "|" . $r['domain'] . "|" . $r['stime'] . "|" . $r['ups'] . "\n";
    }
    @file_put_contents(WD_ROOT . "/data/wd_dns_domain.txt", $msg);
    //子域名
    $msg = "";
    //id,uid,zone,host,type,data,ttl,view,mx_priority,primary_ns,second_ns,ups,avil
    $q = $db->query("select * from wd_dns_records $wh");
    //echo "select * from wd_dns_records $wh";
    while ($r = $db->fetch_array($q)) {
        $msg .= $r['id'] . "|" . $r['uid'] . "|" . $r['zone'] . "|" . $r['host'] . "|" . $r['type'] . "|" . $r['data'] . "|" . $r['ttl'] . "|" . $r['view'] . "|" . $r['mx_priority'] . "|" . $r['primary_ns'] . "|" . $r['second_ns'] . "|" . $r['ups'] . "|" . $r['avil'] . "|" . $r['resp_person'] . "\n";
    }
    @file_put_contents(WD_ROOT . "/data/wd_dns_records.txt", $msg);

    //别名
    if ($dns_url_is == 1) {
        $msg = "";
        //id,pid,uid,domain,url,state
        $q = $db->query("select * from wd_dns_url");
        while ($r = $db->fetch_array($q)) {
            $msg .= $r['id'] . "|" . $r['pid'] . "|" . $r['uid'] . "|" . $r['domain'] . "|" . $r['url'] . "|" . $r['ut'] . "|" . $r['state'] . "\n";
        }
        @file_put_contents(WD_ROOT . "/data/wd_dns_url.txt", $msg);
    }

    url_file_get_contents($nurl, 30);
}

function dns_data_sync_all()
{
    global $dns_ns_list, $nsga_list;
    //
    if (!empty($nsga_list)) {
        foreach ($nsga_list as $v)
            if (!empty($v['ip']))
                $dns_ns_ip_list1 .= $v['ip'] . ",";
        //for ($i=1;$i<=sizeof($nsga_list);$i++)
        //if (!empty($nsga_list[$i]['ip']))
        //$dns_ns_ip_list1.=$nsga_list[$i]['ip'].",";
        $dns_ns_ip_list = substr($dns_ns_ip_list1, 0, strlen($dns_ns_ip_list1) - 1);
        $ns_ip = explode(",", $dns_ns_ip_list);
        $ns_ip = array_unique($ns_ip);
        $s1 = array_values($ns_ip);
        //print_r($ns_ip);
    } else
        $s1 = explode(",", $dns_ns_ip_list);
    //$s1=explode(",",$dns_ns_list);
    for ($i = 0; $i < sizeof($s1); $i++) {
        if (empty($s1[$i])) continue;
        if ($s1[$i] == "127.0.0.1") continue;
        dns_data_sync($s1[$i]);
    }
}

/*
<?
$dns_master_ip="192.168.1.232";
$dns_num=1;
$dns_start_time=1322698088;
$dns_end_time=1325376488;
$dns_key="ca75a955ed9ed102a008ec09a29486bf";
?>
*/

//echo $dns_master_ip."|dns_master_ip\n";
/*
授权Key 1,2,3
*/

//function check_license($ip,$num,$start_time,$end_time=0,$key) {
function check_dns_license($t = 0)
{
    global $dns_key_ip, $dns_key_num, $dns_key_stime, $dns_key_etime, $dns_keys, $dns_master_ip;
    $k = "Wa_Cdms";
    $ct = time();
    $local_ip = @$_SERVER['SERVER_ADDR'];
    $domain = @$_SERVER["SERVER_NAME"];
    //echo $local_ip."|local_ip|||||||||||||||||||||1<br>\n";
    //echo $dns_master_ip."|dns_master_ip||||||||||||||||||||||2<br>\n";
    if ($local_ip == "127.0.0.1" and $dns_master_ip == "127.0.0.1") $local_ip = $dns_key_ip;
    //echo $local_ip;exit;
    //if (empty($local_ip)) $local_ip=$l_
    //echo $key."<br>";
    //echo $ip."|".$num."|".$start_time."|".$end_time."<br>";
    //echo $k."|".$dns_key_ip."|".$dns_key_num."|".$dns_key_stime."|".$dns_key_etime."<br>";
    $k1 = md5($k . $dns_key_ip . $dns_key_num . $dns_key_stime . $dns_key_etime);
    //echo "<br>";
    //echo "|".$k1."|<br>";
    //echo "|".$key."|<br>"; 未授权版本，功能有所限制,可联系管理员
    if (!@file_exists(WD_ROOT . "/data/dns_license.php")) {
        echo "&nbsp;&nbsp;&nbsp;&nbsp;商业授权版本可用,如需授权,请联系管理员";
        exit;
    }
    if ($k1 != $dns_keys) {
        echo "授权key错误";
        exit;
    }//go_back("授权key错误！");//exit;
    if ($ct < $dns_key_stime) {
        echo "时间错误1";
        exit;
    }//go_back("时间错误1");//exit;
    if ($ct > $dns_key_etime and $dns_key_etime != 0) {
        echo "系统已到期,请续费或联系QQ：12571192";
        exit;
    }//go_back("时间错误2");//exit;/
    //echo $local_ip."|".$dns_key_ip."|";
    if (!empty($local_ip) and $local_ip != $dns_key_ip) {
        echo "授权ip错误,请联系QQ：12571192";
        exit;
    }//go_back("授权IP错误！");//exit;
    if ($t == 1) return $dns_num;
    elseif ($t == 2) return $dns_end_time;
    else return 0;
}

//域名检测，检查域名是否为自己或已注册域名，只限于普通用户，管理用户不限制
function check_domain_dns($domain)
{
    global $db, $wdcp_gid, $wdcp_uid;
    if ($wdcp_gid == 1) return true;
    $s1 = str_replace("www.", "", $domain);
    $s2 = explode(".", $domain);
    $l = sizeof($s2);
    if ($l > 3 and eregi("com.cn|net.cn|gov.cn|org.cn", $s1)) {
        $s3 = $s2[$l - 2] . "." . $s2[$l - 1] . "." . $s2[$l];
        $wh = "(domain='$s1' or domain='$s3')";
    } else {
        $wh = "domain='$s1'";
    }
    $q = $db->query("select * from wd_dns_domain where uid='$wdcp_uid' and $wh");
    if ($db->num_rows($q) == 0) go_back("域名不存在");
    return true;
}

function check_monitor_id($id)
{
    global $db;
    $q = $db->query("select * from wd_dns_monitor where oid='$id'");
    if ($db->num_rows($q) == 0) return 0;
    $r = $db->fetch_array($q);
    return $r['id'];
}

$dns_ip_list_cn = array("电信", "网通", "移动", "教育网", "港台", "海外");
$dns_ip_list_c = array(array(
    array("湖南电信", "59.51.78.180"),
    array("内蒙古电信", "222.74.1.201"),
    array("北京电信", "211.156.177.71"),
    array("西藏电信", "202.98.224.73"),
    array("四川电信", "61.139.2.69"),
    array("黑龙江电信", "219.147.198.242")),
    array(
        array("海南网通", "221.11.141.9"),
        array("西藏网通", "221.13.65.38"),
        array("甘肃网通", "221.7.34.238"),
        array("河南网通", "202.102.227.82"),
        array("北京网通", "202.106.0.20"),
        array("贵州网通", "221.13.30.242"),
        array("上海网通", "203.95.7.161")),
    array(
        array("西藏移动", "211.139.73.34"),
        array("湖南移动", "211.142.210.98"),
        array("黑龙江移动", "211.137.241.34"),
        array("甘肃移动", "218.203.160.194"),
        array("湖北移动", "211.137.76.67"),
        array("江苏移动", "221.130.56.242")),
    array(
        array("河南教育网", "202.196.32.1"),
        array("江西教育网", "210.35.24.2"),
        array("湖北教育网", "211.69.143.1"),
        array("湖南教育网", "202.197.120.1"),
        array("安徽教育网", "211.86.241.82"),
        array("西藏教育网", "210.41.4.2")),
    array(
        array("香港和记环球电讯", "202.45.84.58"),
        array("香港宽频", "203.80.96.10")),
    array(
        array("加拿大", "209.166.160.36"),
        array("美国谷哥", "8.8.8.8")));

/*
$dns_ip_list_c=array(array(
array("河南电信","222.85.85.13"),
array("广东电信","202.96.128.143"),
array("青海电信","202.100.138.68"),
array("湖南电信","59.51.78.180"),
array("内蒙古电信","222.74.1.201"),
array("江苏电信","218.2.135.15"),
array("北京电信","211.156.177.71"),
array("西藏电信","202.98.224.73"),
array("四川电信","61.139.2.69"),
array("黑龙江电信","219.147.198.242"),
array("辽宁电信","219.149.52.141")),
array(
array("广东网通","210.21.196.6"),
array("海南网通","221.11.141.9"),
array("新疆网通","221.7.1.196"),
array("西藏网通","221.13.65.38"),
array("四川网通","124.161.87.155"),
array("甘肃网通","221.7.34.238"),
array("河南网通","202.102.227.82"),
array("黑龙江网通","202.97.224.69"),
array("北京网通","202.106.0.20"),
array("贵州网通","221.13.30.242"),
array("上海网通","203.95.7.161")),
array(
array("海南移动","211.138.164.6"),
array("西藏移动","211.139.73.34"),
array("山西移动","211.138.106.19"),
array("江西移动","211.141.90.68"),
array("青海移动","211.138.75.123"),
array("天津移动","211.137.160.5"),
array("安徽移动","211.138.180.2"),
array("湖南移动","211.142.210.98"),
array("黑龙江移动","211.137.241.34"),
array("甘肃移动","218.203.160.194"),
array("重庆移动","221.130.252.200"),
array("湖北移动","211.137.76.67"),
array("江苏移动","221.130.56.242")),
array(
array("贵州教育网","210.40.0.36"),
array("河南教育网","202.196.32.1"),
array("黑龙江教育网","202.118.176.24"),
array("海南教育网","210.37.40.4"),
array("江西教育网","210.35.24.2"),
array("湖北教育网","211.69.143.1"),
array("湖南教育网","202.197.120.1"),
array("安徽教育网","211.86.241.82"),
array("天津教育网","211.68.112.206"),
array("广西教育网","210.36.80.27"),
array("新疆教育网","219.247.64.101"),
array("西藏教育网","210.41.4.2"),
array("宁夏教育网","202.201.112.9"),
array("四川教育网","202.115.176.33")),
array(
array("香港和记环球电讯","202.45.84.58"),
array("OpenDNS","208.67.220.220"),
array("香港宽频","203.80.96.10"),
array("香港有线宽频i-Cable","61.10.0.130")),
array(
array("加拿大","209.166.160.36"),
array("印度","202.138.96.2"),
array("美国","165.87.13.129"),
array("新西兰","202.27.184.3")));
*/

?>