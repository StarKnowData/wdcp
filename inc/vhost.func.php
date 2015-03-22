<?php

/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/
if (!defined('WD_ROOT')) exit();


function check_domain($domain, $t = 0)
{
    global $db;
    $domain = str_replace("http://", "", $domain);
    if (eregi("/", $domain))
        if ($t == 1) {
            echo "domain err";
            exit;
        } else go_back("域名格式有错!");
    if (!eregi("[a-z0-9]{1,50}\.[a-z]{2,3}", $domain))
        if ($t == 1) {
            echo "domain err";
            exit;
        } else go_back("域名格式有错!");
    $q = $db->query("select * from wd_site where domain='$domain'");
    if ($db->num_rows($q) > 0)
        if ($t == 1) {
            echo "domain exists";
            exit;
        } else go_back("域名已存在！");
}

function check_domain_def($domain, $t = 0)
{
    global $db;
    $domain = str_replace("http://", "", $domain);
    if (eregi("/", $domain))
        if ($t == 1) {
            echo "domain err";
            exit;
        } else go_back("域名格式有错!");
    $q = $db->query("select * from wd_site where domain='$domain'");
    if ($db->num_rows($q) > 0)
        if ($t == 1) {
            echo "domain exists";
            exit;
        } else go_back("域名已存在！");
}

function web_home_check($dir)
{
    global $web_home_is;
    if (($web_home_is == 1) and !@is_dir($dir)) {
        //exec("sudo wd_app mkdir '$dir'",$str,$re);
        is_dir_check($dir);
    }
}

function make_apache_port($e, $p, $ip = "")
{
    if ($e == 2 or $e == 3) return;
    if ($p == "80" and empty($ip)) return;
    $port_conf = "/www/wdlinux/apache/conf/vhost/port.conf";
    $p1 = explode(",", $p);
    $pm = "";
    //端口
    for ($i = 1; $i < sizeof($p1); $i++) {
        if (empty($p1[$i]) or !is_numeric($p1[$i])) continue;
        if ($p1[$i] == "88") continue;
        $pm .= "Listen " . $p1[$i] . "\n";
    }
    //ip
    $p2 = explode(",", $ip);
    for ($i = 0; $i < sizeof($p2); $i++) {
        if (empty($p2[$i])) continue;
        if (eregi(":", $p2[$i]))
            $pm .= "NameVirtualHost " . $p2[$i] . "\n";
        else
            $pm .= "NameVirtualHost " . $p2[$i] . ":80\n";
    }
    //echo $pm;exit;
    if (!@is_writable($port_conf)) file_is_write($port_conf);
    @file_put_contents($port_conf, $pm);
    return;
}


//更新rewrite规则文件
function update_rewrite($fn, $act = 0)
{
    global $web_eng, $apa_conf_dir, $ngi_conf_dir;
    $re_conf = WD_ROOT . "/data/rewrite/" . $fn;
    $re_apa_dir = str_replace("vhost/", "", $apa_conf_dir) . "rewrite/";
    $re_ngi_dir = str_replace("vhost/", "", $ngi_conf_dir) . "rewrite/";
    //echo $re_conf."|".$re_apa_dir."|".$re_ngi_dir."<br>";//exit;
    if ($web_eng == 1) {
        if (@file_exists($re_conf) and @is_dir($re_apa_dir))
            @copy($re_conf, $re_apa_dir . $fn);
        else
            go_back("文件或目录不存在！");
    } elseif ($web_eng == 2) {
        if (@file_exists($re_conf) and @is_dir($re_ngi_dir))
            @copy($re_conf, $re_ngi_dir . $fn);
        else
            go_back("文件或目录不存在！");
    } elseif ($web_eng == 3) {
        if (@file_exists($re_conf) and @is_dir($re_apa_dir))
            @copy($re_conf, $re_apa_dir . $fn);
        else
            go_back("文件或目录不存在！");
        //if (file_exists($re_conf) and is_dir($re_ngi_dir))
        //copy($re_conf,$re_ngi_dir);
    } else
        go_back("错误");
}

function update_rewrite_del($fn)
{
    global $web_eng, $apa_conf_dir, $ngi_conf_dir;
    //$re_conf=WD_ROOT."/data/rewrite/".$fn.".conf";
    $re_apa_dir = str_replace("vhost/", "", $apa_conf_dir) . "rewrite/";
    $re_ngi_dir = str_replace("vhost/", "", $ngi_conf_dir) . "rewrite/";
    $re_apa_conf = $re_apa_dir . $fn;
    $re_ngi_conf = $re_apa_dir . $fn;
    if ($web_eng == 1) {
        if (@file_exists($re_apa_conf))
            @unlink($re_apa_conf);
    } elseif ($web_eng == 2) {
        if (@file_exists($re_ngi_conf))
            @unlink($re_ngi_conf);
    } elseif ($web_eng == 3) {
        if (@file_exists($re_apa_conf))
            @unlink($re_apa_conf);
        //if (file_exists($re_ngi_conf))
        //unlink($re_ngi_conf);
    } else;
}


//web重起或重载
function web_reload()
{
    global $web_eng, $auto_reload;
    //require WD_ROOT."/data/sys_conf.php";
    $reload_tmp = WD_ROOT . "/data/tmp/reload.txt";
    if ($web_eng == 1) {
        //exec("sudo wd_app apache_reload",$str,$re);
        @file_put_contents($reload_tmp, "httpd");
        exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php > /dev/null 2>&1", $str, $re);
        if (@file_exists($reload_tmp)) @unlink($reload_tmp);
        return $re;
    } elseif ($web_eng == 2) {
        //exec("sudo wd_app nginx_reload",$str,$re);
        @file_put_contents($reload_tmp, "nginxd");
        exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php > /dev/null 2>&1", $str, $re);
        if (@file_exists($reload_tmp)) @unlink($reload_tmp);
        return $re;
    } elseif ($web_eng == 3) {
        //exec("sudo wd_app apache_reload",$str,$re);
        //exec("sudo wd_app nginx_reload",$str,$re);
        @file_put_contents($reload_tmp, "httpd,nginxd");
        exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php > /dev/null 2>&1", $str, $re);
        if (@file_exists($reload_tmp)) @unlink($reload_tmp);
        return $re;
    } else;
}

function web_restart()
{
    global $web_eng;
    $restart_tmp = WD_ROOT . "/data/tmp/restart.txt";
    //require WD_ROOT."/data/sys_conf.php";
    if ($web_eng == 1) {
        //exec("sudo wd_app apache_restart",$str,$re);
        @file_put_contents($restart_tmp, "httpd");
        exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php > /dev/null 2>&1", $str, $re);
        if (@file_exists($restart_tmp)) @unlink($restart_tmp);
        return $re;
    } elseif ($web_eng == 2) {
        //exec("sudo wd_app nginx_restart",$str,$re);
        @file_put_contents($restart_tmp, "nginxd");
        exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php > /dev/null 2>&1", $str, $re);
        if (@file_exists($restart_tmp)) @unlink($restart_tmp);
        return $re;
    } elseif ($web_eng == 3) {
        //exec("sudo wd_app apache_restart",$str,$re);
        //exec("sudo wd_app nginx_restart",$str,$re);
        @file_put_contents($restart_tmp, "httpd,nginxd");
        exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php > /dev/null 2>&1", $str, $re);
        if (@file_exists($restart_tmp)) @unlink($restart_tmp);
        return $re;
    } else;
}

function del_site($id, $t = 0)
{
    global $db, $apa_conf_dir, $ngi_conf_dir;
    $q = $db->query("select * from wd_site where id='$id'");
    if ($db->num_rows($q) == 0)
        if ($t == 1) {
            echo "id err";
            exit;
        } else go_back("ID错误");
    $r = $db->fetch_array($q);
    if ($r['domain'] == "default")
        $domain = "00000." . $r['domain'];
    else
        $domain = $r['domain'];
    if ($web_eng == 1) {
        $conf = $apa_conf_dir . $domain . ".conf";
        @unlink($conf);
    } elseif ($web_eng == 2) {
        $conf = $ngi_conf_dir . $domain . ".conf";
        @unlink($conf);
    } elseif ($web_eng == 3) {
        $conf = $apa_conf_dir . $domain . ".conf";
        @unlink($conf);
        $conf = $ngi_conf_dir . $domain . ".conf";
        @unlink($conf);
    } else;
}

//更新站点
function update_vhost($id)
{
    global $web_eng;
    //require WD_ROOT."/data/sys_conf.php";
    //echo $web_eng." 3\n";
    if ($web_eng == 1) {
        update_vhost_apache($id);// //
    } elseif ($web_eng == 2) {
        update_vhost_nginx($id);
    } elseif ($web_eng == 3) {
        update_vhost_apache($id);
        update_vhost_nginx($id);
    } else;
}

function update_vhost_del($id, $t = 0)
{
    global $db, $web_eng, $apa_conf_dir, $ngi_conf_dir;
    //require WD_ROOT."/data/sys_conf.php";
    $q = $db->query("select * from wd_site where id='$id'");
    if ($db->num_rows($q) == 0)
        if ($t == 1) {
            echo "id err";
            exit;
        } else go_back("ID错误");
    $re = $db->fetch_array($q);
    if ($re['domain'] == "default")
        $domain = "00000." . $re['domain'];
    elseif ($re['domainss'] == 1)
        $domain = "zzzzz." . $re['domain'];
    else
        $domain = $re['domain'];
    if ($web_eng == 1) {
        $conf = $apa_conf_dir . $domain . ".conf";
        if (@file_exists($conf)) {
            @unlink($conf);
            //web_reload();
        }
    } elseif ($web_eng == 2) {
        $conf = $ngi_conf_dir . $domain . ".conf";
        if (@file_exists($conf)) {
            @unlink($conf);
            //web_reload();
        }
    } elseif ($web_eng == 3) {
        $conf = $apa_conf_dir . $domain . ".conf";
        if (@file_exists($conf))
            @unlink($conf);
        $conf = $ngi_conf_dir . $domain . ".conf";
        if (@file_exists($conf)) {
            @unlink($conf);
            //web_reload();
        }
    } else;
}

function update_vhost_apache($id, $t = 0)
{
    global $db, $demo_ip, $apa_conf_dir, $web_eng, $web_logs_home, $htpasswd_dir, $web_ip;
    require WD_ROOT . "/data/sys_conf.php";
    //echo $web_eng." 4\n";
    //echo date("Y-m-d H:i:s")." 1<br>";
    $q = $db->query("select * from wd_site where id=$id");
    //echo date("Y-m-d H:i:s")." 2<br>";
    if ($db->num_rows($q) == 0)
        if ($t == 1) {
            echo "id err";
            exit;
        } else go_back("sid错误");
    $re = $db->fetch_array($q);
    //echo date("Y-m-d H:i:s")." 3<br>";
    //echo $web_eng."\n";
    if ($web_eng == 3)
        $msg = "<VirtualHost *:88>\n";
    elseif ($re['uip'] != 0)
        $msg = "<VirtualHost " . $re['uip'] . ":" . $re['port'] . ">\n";
    else
        $msg = "<VirtualHost *:" . $re['port'] . ">\n";
    //if ($re['sdomain']==0 and @is_dir($re['vhost_dir']."/public_html"))
    //else
    //$DocumentRoot=$re['vhost_dir'];
    //wdl_sudo_app_mkdirw($DocumentRoot);
    //$DocumentRoot=$re['vhost_dir'];
    //if ($re['sdomain']==0) {
    if ($re['sdomain'] == 0 or !eregi("public_html", $re['vhost_dir'])) {
        $DocumentRoot = $re['vhost_dir'] . "/public_html";
        $pDocumentRoot = $re['vhost_dir'];
    } else {
        $DocumentRoot = $re['vhost_dir'];
        $pd1 = explode("/public_html", $re['vhost_dir']);
        $pDocumentRoot = $pd1[0];
    }
    if (!@is_dir($DocumentRoot))
        is_web_dir($DocumentRoot);

    $msg .= "DocumentRoot " . $DocumentRoot . "\n";

    //echo date("Y-m-d H:i:s")." 4<br>";
    $wipl = "";
    if ($re['domain'] != "default") {
        $msg .= "ServerName " . $re['domain'] . "\n";
        if ($re['domains'] != "") {
            $msg .= "ServerAlias " . str_replace(",", " ", $re['domains']);
            if ($re['domainss'] == 1)
                $msg .= " *." . $re['domain'] . "\n";
            else
                $msg .= "\n";
        } elseif ($re['domainss'] == 1) {
            $msg .= "ServerAlias *." . $re['domain'] . "\n";//
        } else;
    } else {

        //echo $re['domain']." web_ip<br>";
        if (!empty($web_ip)) {
            $wip1 = explode(",", $web_ip);
            for ($i = 0; $i < sizeof($wip1); $i++) {
                //echo $i."<br>";
                if ($web_eng == 3)
                    $wipl .= "<VirtualHost $wip1[$i]:88>\n";
                else
                    $wipl .= "<VirtualHost $wip1[$i]:80>\n";
                $wipl .= "DocumentRoot $DocumentRoot\n";
                $wipl .= "</VirtualHost>\n";
            }


            //默认服务器IP
            if (empty($_SERVER["SERVER_ADDR"])) {
                exec("ifconfig | grep 'inet addr' | gawk -F: '{print $2}' | gawk '{print $1}'", $lip1, $lre);
                if (!in_array($lip1[0], $wip1))
                    $_SERVER["SERVER_ADDR"] = "$lip1[0]";
                if (!in_array($lip1[1], $wip1))
                    $_SERVER["SERVER_ADDR"] .= " $lip1[1]";
            }

        }

        $msg .= "ServerName localhost\n";
        $msg .= "ServerAlias " . $_SERVER["SERVER_ADDR"] . " " . str_replace(",", " ", $web_ip) . "\n";//
    }
    //echo date("Y-m-d H:i:s")." 5<br>";
    //
    $msgr = $msg;

    if ($re['dir_index'] !== "")
        $msg .= "DirectoryIndex " . str_replace(",", " ", $re['dir_index']) . "\n";
    if ($re['err400'] == 1)
        $msg .= "ErrorDocument 400 /errpage/400.html\n";
    if ($re['err401'] == 1)
        $msg .= "ErrorDocument 401 /errpage/401.html\n";
    if ($re['err403'] == 1)
        $msg .= "ErrorDocument 403 /errpage/403.html\n";
    if ($re['err404'] == 1)
        $msg .= "ErrorDocument 404 /errpage/404.html\n";
    if ($re['err405'] == 1)
        $msg .= "ErrorDocument 405 /errpage/405.html\n";
    if ($re['err500'] == 1)
        $msg .= "ErrorDocument 500 /errpage/500.html\n";
    if ($re['err503'] == 1)
        $msg .= "ErrorDocument 503 /errpage/503.html\n";
    if ($re['access_log'] == 1)
        //$msg.="CustomLog \"logs/".$re['domain']."_access_log\" common\n";
        $msg .= "CustomLog \"$web_logs_home/" . $re['domain'] . "_access_log\" common\n";
    if ($re['error_log'] == 1)
        //$msg.="ErrorLog \"logs/".$re['domain']."_error_log\"\n";
        $msg .= "ErrorLog \"$web_logs_home/" . $re['domain'] . "_error_log\"\n";
    if (($re['rewrite'] !== "") and (@file_exists("../data/rewrite/" . str_replace("_nginx", "_apache", chop($re['rewrite'])) . ".conf")))
        $msg .= "include conf/rewrite/" . str_replace("_nginx", "_apache", chop($re['rewrite'])) . ".conf\n";

    //if (@$_SERVER["SERVER_ADDR"]===$demo_ip)
    if (in_array(@$_SERVER["SERVER_ADDR"], $demo_ip))
        $msg .= "php_admin_value open_basedir " . $pDocumentRoot . ":/tmp\n";
    //elseif ($re['sdomain']>0)
    //$msg.="php_admin_value open_basedir ".$pDocumentRoot.":/tmp\n";
    elseif ($re['limit_dir'] == 1 or $re['limit_dir'] == "def")
        $msg .= "php_admin_value open_basedir " . $pDocumentRoot . ":/tmp\n";
    else;
    //if ($re['limit_dir']==0 and )
    //$msg.="php_admin_value open_basedir ".$re['vhost_dir'].":/tmp\n";


    //echo date("Y-m-d H:i:s")." 6<br>";
    //
    if ($re['conn'] != 0 and $web_eng == 1) {
        $msg .= "<IfModule mod_limitipconn.c>\n";
        $msg .= "<Location />\n";
        $msg .= "   MaxConnPerIP 1\n";
        $msg .= "</Location>\n";
        $msg .= "</IfModule>\n";
    }

    if ($re['bw'] != 0 and $web_eng == 1) {
        $bws = $re['bw'] * 1024;
        $msg .= "<IfModule mod_bw.c>\n";
        $msg .= "BandWidthModule On\n";
        $msg .= "ForceBandWidthModule On\n";
        $msg .= "BandWidth all $bws\n";
        $msg .= "</IfModule>\n";
    }

    if (!empty($re['a_filetype']) and $web_eng == 1) {
        $a_filetype = str_replace(",", "|", $re['a_filetype']);
        $a_url = $re['domain'] . "|" . str_replace(",", "|", $re['domains']) . "|" . str_replace(",", "|", $re['a_url']);
        $a1 = explode("|", $a_url);
        $msg .= "RewriteEngine on\n";
        $msg .= "RewriteCond %{HTTP_REFERER} !^$ [NC]\n";
        $a2 = "";
        for ($i = 0; $i < sizeof($a1); $i++) {
            if (empty($a1[$i])) continue;
            //$msg.="SetEnvIfNoCase Referer \"^http://$a1[$i]\" local_ref=1\n";
            $msg .= "RewriteCond %{HTTP_REFERER} !$a1[$i] [NC]\n";
        }
        if (empty($re['d_url']))
            $msg .= "RewriteRule .*.(gif|jpg)$ [NC,L]\n";
        else
            $msg .= "RewriteRule .*.(gif|jpg)$ http://$re[d_url] [R,NC,L]\n";
        //$msg.="SetEnvIf Referer \"^$\" local_ref=1\n";

    }
    //echo $re['gzip'];//exit;
    if ($re['gzip'] == 1) {
        $msg .= "<IfModule mod_deflate.c>\n";
        $msg .= "DeflateCompressionLevel 7\n";
        $msg .= "AddOutputFilterByType DEFLATE text/html text/plain text/xml application/x-httpd-php\n";
        $msg .= "AddOutputFilter DEFLATE css js html htm gif jpg png bmp php\n";
        $msg .= "</IfModule>\n";
        //echo $msg;exit;
    }
    if ($re['expires'] == 1) {
        $msg .= "<IfModule mod_expires.c>\n";
        $msg .= "ExpiresActive On\n";
        $msg .= "ExpiresByType image/gif A3600\n";
        $msg .= "ExpiresByType image/jpeg A3600\n";
        $msg .= "ExpiresByType image/png A36000\n";
        $msg .= "ExpiresByType text/css A3800\n";
        $msg .= "ExpiresByType application/x-shockwave-flash A3600\n";
        $msg .= "ExpiresByType application/x-javascript A3600\n";
        $msg .= "ExpiresByType video/x-flv A3600\n";
        //$msg.="ExpiresDefault A86400\n";
        $msg .= "</IfModule>\n";
    }

    if ($re['ruser'] == 1 and $re['ftpuser'] != "") {
        $msg .= "    <IfModule mpm_itk_module>\n";
        $msg .= "      AssignUserId " . $re['ftpuser'] . " " . $re['ftpuser'] . "\n";
        $msg .= "    </IfModule>\n";
        //demo
    }
    //
    if ($re['re_dir'] == 1 and !empty($re['re_url']) and $web_eng == 1) {
        $a_url = $re['domain'] . "|" . str_replace(",", "|", $re['domains']);
        $a1 = explode("|", $a_url);
        $msg = $msgr;
        $msg .= "RewriteEngine on\n";
        //for ($i=0;$i<=sizeof($a1);$i++) {
        //if (empty($a1[$i])) continue;
        //$msg.="RewriteCond %{HTTP_HOST} ^$a1[$i]$ [NC]\n";
        //}
        //$msg.="RewriteRule ^(.*)$ http://$re[re_url]/\$1 [R=301,L]\n";
        $msg .= "RewriteRule ^(.*)$ http://$re[re_url]\$1 [R=301,L]\n";//20120701
    }

    if ($re['re_dir'] == 2 and !empty($re['re_url']) and $web_eng == 1) {
        $a_url = $re['domain'] . "|" . str_replace(",", "|", $re['domains']);
        $a1 = explode("|", $a_url);
        $msg = $msgr;
        //$msg.="RewriteEngine on\n";
        //for ($i=0;$i<=sizeof($a1);$i++) {
        //if (empty($a1[$i])) continue;
        //$msg.="RewriteCond %{HTTP_HOST} ^$a1[$i]$ [NC]\n";
        //}
        $msg .= "RewriteRule ^(.*)$ http://$re[re_url]/\$1 [R=302,L]\n";
    }
    $msg .= "</VirtualHost>\n";
    //echo date("Y-m-d H:i:s")." 7<br>";

    if ($re['domain'] == "default")
        $msg .= $wipl;

    $msg .= "<Directory $pDocumentRoot>\n";
    if ($re['dir_list'] == 1)
        $msg .= "    Options Indexes FollowSymLinks\n";
    else
        $msg .= "    Options FollowSymLinks\n";

    $htf = $htpasswd_dir . "/" . $re['id'] . "_" . $re['domain'] . ".txt";
    //echo $htf."<br>";
    if (@file_exists($htf) and $web_eng = 1) {
        $msg .= "    AllowOverride AuthConfig\n";
        $msg .= "    AuthType Basic\n";
        $msg .= "    AuthName \"" . $re['domain'] . "\"\n";
        $msg .= "    AuthUserFile $htf\n";
        $msg .= "    Require valid-user\n";
    } else
        $msg .= "    AllowOverride All\n";
    $msg .= "    Order allow,deny\n";
    $msg .= "    Allow from all\n";
    $msg .= "</Directory>\n";
    //echo date("Y-m-d H:i:s")." 8<br>";

    if (@is_dir($apa_conf_dir)) {
        if ($re['domain'] == "default")
            $conf = $apa_conf_dir . "00000." . $re['domain'] . ".conf";
        elseif ($re['domainss'] == 1) {
            if (@file_exists($apa_conf_dir . $re['domain'] . ".conf")) @unlink($apa_conf_dir . $re['domain'] . ".conf");
            $conf = $apa_conf_dir . "zzzzz." . $re['domain'] . ".conf";
        } else {
            if (@file_exists($apa_conf_dir . "zzzzz." . $re['domain'] . ".conf")) @unlink($apa_conf_dir . "zzzzz." . $re['domain'] . ".conf");
            $conf = $apa_conf_dir . $re['domain'] . ".conf";
        }
        @file_put_contents($conf, $msg);
        //web_reload();
        //echo date("Y-m-d H:i:s")." ".$conf."<br>";
        return true;
    } else
        return false;
}

function update_vhost_nginx($id, $t = 0)
{
    global $db, $demo_ip, $ngi_conf_dir, $web_eng, $web_logs_home, $htpasswd_dir, $web_ip;
    require WD_ROOT . "/data/sys_conf.php";
    //echo $web_eng." 4\n";
    $q = $db->query("select * from wd_site where id=$id");
    if ($db->num_rows($q) == 0)
        if ($t == 1) {
            echo "id err";
            exit;
        } else go_back("sid错误");
    $re = $db->fetch_array($q);
    //echo $web_eng."\n";
    $msgng = "server {\n";
    if ($re['uip'] != 0)
        $msgng .= "        listen       " . $re['uip'] . ":" . $re['port'] . ";\n";
    else
        $msgng .= "        listen       " . $re['port'] . ";\n";
    //if ($re['sdomain']==0 and @is_dir($re['vhost_dir']."/public_html"))

    //else
    //$DocumentRoot=$re['vhost_dir'];
    //wdl_sudo_app_mkdirw($DocumentRoot);
    //$DocumentRoot=$re['vhost_dir'];
    if ($re['sdomain'] == 0 or !eregi("public_html", $re['vhost_dir']))
        $DocumentRoot = $re['vhost_dir'] . "/public_html";
    else
        $DocumentRoot = $re['vhost_dir'];
    if (!@is_dir($DocumentRoot))
        is_web_dir($DocumentRoot);

    if ($re['domain'] != "default") {
        if ($re['domains'] != "") {
            $msgng .= "        server_name " . $re['domain'] . " " . str_replace(",", " ", $re['domains']);
            if ($re['domainss'] == 1)
                $msgng .= " *." . $re['domain'] . ";\n";
            else
                $msgng .= ";\n";
        } else {
            $msgng .= "        server_name " . $re['domain'];
            if ($re['domainss'] == 1)
                $msgng .= " *." . $re['domain'] . ";\n";
            else
                $msgng .= ";\n";
        }
        //echo $msgng;
    } else {
        if (!empty($web_ip)) {
            $wip1 = explode(",", $web_ip);
            for ($i = 0; $i < sizeof($wip1); $i++)
                $msgng .= "        listen       " . $wip1[$i] . ":" . $re['port'] . " default;\n";
        }
        //默认服务器IP
        if (empty($_SERVER["SERVER_ADDR"])) {
            exec("ifconfig | grep 'inet addr' | gawk -F: '{print $2}' | gawk '{print $1}'", $lip1, $lre);
            if (!in_array($lip1[0], $wip1))
                $_SERVER["SERVER_ADDR"] = "$lip1[0]";
            if (!in_array($lip1[1], $wip1))
                $_SERVER["SERVER_ADDR"] .= " $lip1[1]";
        }
        $msgng .= "        server_name localhost " . $_SERVER["SERVER_ADDR"] . " " . str_replace(",", " ", $web_ip) . ";\n";
    }

    $msgngr = $msgng;
    $msgng .= "        root " . $DocumentRoot . ";\n";

    if ($re['dir_list'] == 1) {
        $msgng .= "        autoindex on;\n";
        //$msgng.="autoindex_exact_size off;\n";
        //$msgng.="autoindex_localtime on;\n";
    }

    if ($re['dir_index'] !== "")
        $msgng .= "        index " . str_replace(",", " ", $re['dir_index']) . ";\n";
    else
        $msgng .= "        index  index.html index.php index.htm;\n";
    if ($re['err400'] == 1)
        $msgng .= "        error_page  400 /errpage/400.html;\n";
    if ($re['err401'] == 1)
        $msgng .= "        error_page  401 /errpage/401.html;\n";
    if ($re['err403'] == 1)
        $msgng .= "        error_page  403 /errpage/403.html;\n";
    if ($re['err404'] == 1)
        $msgng .= "        error_page  404 /errpage/404.html;\n";
    if ($re['err405'] == 1)
        $msgng .= "        error_page  405 /errpage/405.html;\n";
    if ($re['err500'] == 1)
        $msgng .= "        error_page  401 /errpage/500.html;\n";
    if ($re['err503'] == 1)
        $msgng .= "        error_page  503 /errpage/503.html;\n";

    $htf = $htpasswd_dir . "/" . $re['id'] . "_" . $re['domain'] . ".txt";
    //echo $htf."<br>";
    if (@file_exists($htf)) {
        $msgng .= "        auth_basic \"" . $re['domain'] . "\";\n";
        $msgng .= "        auth_basic_user_file $htf;\n";
    }

    if ($re['conn'] != 0)
        $msgng .= "        limit_conn one $re[conn];\n";
    if ($re['bw'] != 0)
        $msgng .= "        limit_rate $re[bw]k;\n";

    if (!empty($re['a_filetype'])) {
        $a_filetype = str_replace(",", "|", $re['a_filetype']);
        $msgng .= "        location ~* \.(" . $a_filetype . ")$ {\n";
        $a_url = $re['domain'] . " " . str_replace(",", " ", $re['domains']) . " " . str_replace(",", " ", $re['a_url']);
        $msgng .= "                valid_referers none blocked " . $a_url . ";\n";
        if (empty($re['d_url']))
            $d_url = "	return 403;\n";
        else
            $d_url = "	rewrite ^/ http://" . str_replace("http://", "", $re['d_url']) . ";\n";
        $msgng .= "        if (\$invalid_referer) {\n";
        //$msgng.="        rewrite ^/ ".$d_url.";\n";
        $msgng .= "        " . $d_url;
        $msgng .= "        }\n";
        $msgng .= "        }\n";
    }


    $msgng .= "        location ~ \.php$ {\n";
    if ($web_eng == 3) {
        $msgng .= "                proxy_pass http://127.0.0.1:88;\n";
        $msgng .= "                include naproxy.conf;\n";//
    } else {
        $msgng .= "                fastcgi_pass   127.0.0.1:9000;\n";
        $msgng .= "                fastcgi_index  index.php;\n";
        $msgng .= "                include fcgi.conf;\n";
    }
    $msgng .= "        }\n";

    if ($re['expires'] == 1) {
        $msgng .= "        location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$ {\n";
        $msgng .= "                expires      1d;\n";
        $msgng .= "        }\n";
        //}
        if ($web_eng == 3)
            $msgng .= "        location ~ .*\.(js|css|htm)?$ {\n";
        else
            $msgng .= "        location ~ .*\.(js|css|html|htm)?$ {\n";
        $msgng .= "                expires      12h;\n";
        $msgng .= "        }\n";
    }

    if ($web_eng == 3) {
        $msgng .= "        location / {\n";
        $msgng .= "                try_files \$uri @apache;\n";
        $msgng .= "        }\n";

        $msgng .= "        location @apache {\n";
        $msgng .= "                 proxy_pass http://127.0.0.1:88;\n";
        $msgng .= "                 include naproxy.conf;\n";
        $msgng .= "        }\n";
    }

    if ($re['rewrite'] !== "" and (@file_exists("../data/rewrite/" . str_replace("_apache", "_nginx", chop($re['rewrite'])) . ".conf")) and $web_eng != 3)
        $msgng .= "        include rewrite/" . str_replace("_apache", "_nginx", chop($re['rewrite'])) . ".conf;\n";

    if ($re['re_dir'] == 1 and !empty($re['re_url'])) {
        $msgng = $msgngr;
        $msgng .= "        rewrite ^/(.*)$ http://" . str_replace("http://", "", $re['re_url']) . "/\$1 permanent;\n";
    }

    if ($re['re_dir'] == 2 and !empty($re['re_url'])) {
        $msgng = $msgngr;
        $msgng .= "        rewrite ^/(.*)$ http://" . str_replace("http://", "", $re['re_url']) . "/\$1 redirect;\n";
    }


    if ($re['access_log'] == 1)
        //$msgng.="        log_format  wwwlogs  '\$remote_addr - \$remote_user [\$time_local] \$request \$status \$body_bytes_sent \$http_referer \$http_user_agent \$http_x_forwarded_for';\n";
        //$msgng.="        access_log  logs/".$re['domain']."_access.log  wwwlogs;\n";
        $msgng .= "        access_log  $web_logs_home/" . $re['domain'] . "_access.log  wwwlogs;\n";

    if ($re['error_log'] == 1)
        //$msgng.="        error_log  logs/".$re['domain']."_error.log;\n";
        $msgng .= "        error_log  $web_logs_home/" . $re['domain'] . "_error.log;\n";

    $msgng .= "}\n";
    if (@is_dir($ngi_conf_dir)) {
        if ($re['domain'] == "default")
            $conf = $ngi_conf_dir . "00000." . $re['domain'] . ".conf";//
        elseif ($re['domainss'] == 1) {
            if (@file_exists($ngi_conf_dir . $re['domain'] . ".conf")) @unlink($ngi_conf_dir . $re['domain'] . ".conf");
            $conf = $ngi_conf_dir . "zzzzz." . $re['domain'] . ".conf";//
        } else {
            if (@file_exists($ngi_conf_dir . "zzzzz." . $re['domain'] . ".conf")) @unlink($ngi_conf_dir . "zzzzz." . $re['domain'] . ".conf");
            $conf = $ngi_conf_dir . $re['domain'] . ".conf";
        }
        @file_put_contents($conf, $msgng);
        //web_reload();
        return true;
    } else
        return false;

}

//端口列表
function return_web_port($port = 80)
{
    global $web_port;
    $s1 = explode(",", $web_port);
    $msg = '<option value="80">80</option>';
    for ($i = 1; $i <= sizeof($s1); $i++) {
        if (empty($s1[$i])) continue;
        if ($port == $s1[$i])
            $msg .= '<option value="' . $s1[$i] . '" selected="selected">' . $s1[$i] . '</option>';
        else
            $msg .= '<option value="' . $s1[$i] . '">' . $s1[$i] . '</option>';
    }
    return $msg;
}

//IP列表
function return_web_ip($ip = 0)
{
    global $web_ip;
    $s1 = explode(",", $web_ip);
    $msg = '<option value="0">默认</option>';
    for ($i = 0; $i <= sizeof($s1); $i++) {
        if (empty($s1[$i])) continue;
        $s12 = explode(":", $s1[$i]);
        if ($ip == $s12[0])
            $msg .= '<option value="' . $s12[0] . '" selected="selected">' . $s12[0] . '</option>';
        else
            $msg .= '<option value="' . $s12[0] . '">' . $s12[0] . '</option>';
    }
    return $msg;
}

function return_sid()
{
    global $db;
    $q = $db->query("select id,domain from wd_site");
    $msg = '<select name="sid" id="sid">\n<option value="0">无</option>';
    while ($re = $db->fetch_array($q)) {
        $msg .= '<option value="' . $re['id'] . '">' . $re['domain'] . '</option>';
    }
    $msg .= '</select>';
    return $msg;
}


//多选框
function return_checkbox($var)
{
    if (chop($var) == 1)
        return 'checked="checked" ';
    else
        return '';
}

function domain_list($str)
{
    $s1 = explode(",", $str);
    for ($i = 0; $i < sizeof($s1); $i++) {
        echo $s1[$i] . "<br>";
    }
}


//域名分隔
function domain_alias($str)
{
    $s1 = explode(",", $str);
    $msg = "";
    for ($i = 0; $i <= sizeof($s1); $i++) {
        if (!empty($s1[$i]))
            $msg .= $s1[$i] . " ";
    }
    return chop($msg);
}


function site_list($id = 0)
{
    global $db, $wdcp_gid, $wdcp_uid;
    if ($wdcp_gid == 1)
        $q = $db->query("select * from wd_site where sdomain=0");
    else
        $q = $db->query("select * from wd_site where uid='$wdcp_uid' and sdomain=0");
    $msg = '<option value="0">所属站点</option>\n';
    while ($r = $db->fetch_array($q)) {
        if ($id == $r['id'])
            $msg .= '<option value="' . $r['id'] . '" selected="selected">' . $r['domain'] . '</option>\n';
        else
            $msg .= '<option value="' . $r['id'] . '">' . $r['domain'] . '</option>\n';
    }
    return $msg;
}

function wdl_vhostdir_check($dir, $t = 0)
{
    //if (!eregi("/www/web|/home"
    //echo substr(trim($dir),0,8)."|". substr(trim($dir),0,5);
    $not = array("var", "bin", "dev", "mnt", "proc", "sbin", "srv", "tmp", "var", "boot", "etc", "lib", "media", "root", "selinux", "sys", "usr");
    //if (substr(trim($dir),0,8)!=="/www/web" and substr(trim($dir),0,5)!=="/home") go_back("目录错误,FTP目录限制在/www/web和/home");
    $s1 = explode("/", $dir);//echo sizeof($s1);exit;
    if (in_array($s1[0], $not))
        if ($t == 1) {
            echo "dir err";
            exit;
        } else go_back("FTP主目录错误");
    if (sizeof($s1) < 3)
        if ($t == 1) {
            echo "dir err";
            exit;
        } else go_back("FTP主目录错误");
    if (eregi("\.\.", $dir))
        if ($t == 1) {
            echo "dir err";
            exit;
        } else go_back("目录名错误");
    for ($i = 0; $i < sizeof($s1); $i++) {
        if (empty($s1[$i])) continue;
        if (!eregi("^[_a-zA-Z0-9.]*$", $s1[$i]))
            if ($t == 1) {
                echo "dir err";
                exit;
            } else go_back("目录名不合法，只能使用字母，数字，下划线组成");
    }
}

function is_web_dir($dir)
{
    $wdir_tmp = WD_ROOT . "/data/tmp/wdir.txt";
    if (@is_dir($dir)) return;
    @file_put_contents($wdir_tmp, $dir);
    exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php", $str, $re);
    if (@file_exists($wdir_tmp)) @unlink($wdir_tmp);
}

function update_site_all()
{
    global $db, $web_eng;
    //echo $web_eng."<->2\n";
    $q = $db->query("select * from wd_site where state=0");
    $i = 0;
    while ($r = $db->fetch_array($q)) {
        update_vhost($r['id']);
    }
    web_reload();
    return true;
}

function env_to($e)
{
    global $db, $service_cmd;
    if ($e == "apache" or $e == "lamp") {
        $i = 0;
        if (@file_exists("/www/wdlinux/apache/conf/httpd.conf")) {
            exec("sed -i 's/Listen 88/Listen 80/g' /www/wdlinux/apache/conf/httpd.conf", $str, $re);
            exec("sed -i 's/NameVirtualHost \*:88/NameVirtualHost \*:80/g' /www/wdlinux/apache/conf/httpd.conf", $str, $re);
            $i++;
        }
        if (@file_exists("/www/wdlinux/apache/conf/vhost/00000.default.conf") and $i == 1)
            exec("sed -i 's/VirtualHost \*:88/VirtualHost \*:80/g' /www/wdlinux/apache/conf/vhost/00000.default.conf", $str, $re);
        if (@is_dir("/www/wdlinux/apache_php")) {
            exec("rm -f /www/wdlinux/php", $str, $re);
            exec("ln -sf /www/wdlinux/apache_php /www/wdlinux/php", $str, $re);
            exec("ln -sf /www/wdlinux/apache_php/etc/php.ini /www/wdlinux/etc/php.ini", $str, $re);
            $i++;
        }
        if ($i == 2) {
            config_update("web_eng", 1, "web引擎");
            config_updatef();
            //unset($web_eng);
            //require WD_ROOT."/data/sys_conf.php";
            //echo $web_eng."<->1\n";
            exec("chown -R wdcpu.wdcpg /www/wdlinux/apache/conf/vhost", $str, $re);
            exec("$service_cmd nginxd stop;$service_cmd php-fpm stop;$service_cmd httpd stop", $str, $re);
            update_site_all();
            exec("/sbin/chkconfig --level 35 nginxd off;/sbin/chkconfig --level 35 httpd on", $str, $re);//exit;
            //echo "$service_cmd nginxd stop;$service_cmd php-fpm stop;$service_cmd httpd restart";
            exec("$service_cmd httpd start", $str, $re);
            return 1;
        } else
            return 0;

    } elseif ($e == "nginx" or $e == "lnmp") {
        $i = 0;
        if (@is_dir("/www/wdlinux/nginx_php")) {
            exec("rm -f /www/wdlinux/php", $str, $re);
            exec("ln -sf /www/wdlinux/nginx_php /www/wdlinux/php", $str, $re);
            exec("ln -sf /www/wdlinux/nginx_php/etc/php.ini /www/wdlinux/etc/php.ini", $str, $re);
            $i++;
        }
        if (!@file_exists("/etc/init.d/php-fpm"))////
            exec("ln -sf /www/wdlinux/init.d/php-fpm /etc/init.d/php-fpm;chmod 755 /etc/init.d/php-fpm", $str, $re);
        if (@file_exists("/www/wdlinux/wdcp_bk/conf/defaultn.conf") and $i == 1) {
            exec("cp -f /www/wdlinux/wdcp_bk/conf/defaultn.conf /www/wdlinux/nginx/conf/vhost/00000.default.conf", $str, $re);
            exec("chown -R wdcpu.wdcpg /www/wdlinux/nginx/conf/vhost", $str, $re);
            $i++;
        } elseif ($i == 1) {
            @file_put_contents("/www/wdlinux/nginx/conf/vhost/00000.default.conf", defaultn_contents());
            exec("chown -R wdcpu.wdcpg /www/wdlinux/nginx/conf/vhost", $str, $re);
            $i++;
        } else;

        if ($i == 2) {
            //$db->query("");
            config_update("web_eng", 2, "web引擎");
            config_updatef();
            //unset($web_eng);
            //require WD_ROOT."/data/sys_conf.php";
            //echo $web_eng."<->1\n";
            exec("$service_cmd nginxd stop;$service_cmd php-fpm stop;$service_cmd httpd stop", $str, $re);
            update_site_all();
            exec("sed -i 's/#*service/service/g' /www/wdlinux/init.d/nginxd", $str, $re);
            exec("/sbin/chkconfig --level 35 httpd off;/sbin/chkconfig --level 35 nginxd on", $str, $re);
            exec("$service_cmd nginxd start", $str, $re);
            return 1;
        } else
            return 0;

    } elseif ($e == "na" or $e == "lnamp") {
        $i = 0;
        if (@file_exists("/www/wdlinux/apache/conf/httpd.conf")) {
            exec("sed -i 's/Listen 80/Listen 88/g' /www/wdlinux/apache/conf/httpd.conf", $str, $re);
            exec("sed -i 's/NameVirtualHost \*:80/NameVirtualHost \*:88/g' /www/wdlinux/apache/conf/httpd.conf", $str, $re);
            $i++;
        }
        if (@file_exists("/www/wdlinux/apache/conf/vhost/00000.default.conf"))
            exec("sed -i 's/VirtualHost \*:80/VirtualHost \*:88/g' /www/wdlinux/apache/conf/vhost/00000.default.conf", $str, $re);
        if (@is_dir("/www/wdlinux/apache_php")) {
            exec("rm -f /www/wdlinux/php", $str, $re);//
            exec("ln -sf /www/wdlinux/apache_php /www/wdlinux/php", $str, $re);
            exec("ln -sf /www/wdlinux/apache_php/etc/php.ini /www/wdlinux/etc/php.ini", $str, $re);
            $i++;
        }
        if (@file_exists("/www/wdlinux/wdcp_bk/conf/defaultna.conf"))
            exec("cp -f /www/wdlinux/wdcp_bk/conf/defaultna.conf /www/wdlinux/nginx/conf/vhost/00000.default.conf", $str, $re);
        else {
            @file_put_contents("/www/wdlinux/nginx/conf/vhost/00000.default.conf", defaultna_contents());
            exec("chown -R wdcpu.wdcpg /www/wdlinux/nginx/conf/vhost", $str, $re);
        }

        if ($i == 2) {
            config_update("web_eng", 3, "web引擎");
            config_updatef();
            //unset($web_eng);
            //require WD_ROOT."/data/sys_conf.php";
            //echo $web_eng."<->1\n";
            exec("$service_cmd nginxd stop;$service_cmd php-fpm stop;$service_cmd httpd stop", $str, $re);
            update_site_all();
            exec("sed -i 's/service/#service/g' /www/wdlinux/init.d/nginxd", $str, $re);
            exec("/sbin/chkconfig --level 35 httpd on;/sbin/chkconfig --level 35 nginxd on", $str, $re);
            exec("$service_cmd httpd start;$service_cmd nginxd start", $str, $re);
            return 1;
        } else
            return 0;
    } else
        return 0;
}

function defaultna_contents()
{
    return '    server {
        listen       80;
        server_name  localhost;
        root /www/web/default;
        index index.php index.html index.htm;

        location ~ \.php$ {
                proxy_pass http://127.0.0.1:88;
                include naproxy.conf;
        }
        location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$ {
                expires      30d;
        }

        location ~ .*\.(js|css)?$ {
                expires      12h;
        }
    }';
}

function defaultn_contents()
{
    return '    server {
        listen       80;
        server_name  localhost;
        root /www/web/default;
        index index.php index.html index.htm;

        location ~ \.php$ {
            fastcgi_pass   127.0.0.1:9000;
            fastcgi_index  index.php;
            include fcgi.conf;
        }
        location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$ {
                expires      30d;
        }

        location ~ .*\.(js|css)?$ {
                expires      12h;
        }
    }';
}

?>