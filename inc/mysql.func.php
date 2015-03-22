<?php

/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/
if (!defined('WD_ROOT')) exit();

function check_mysql_user($user, $t = 0)
{
    global $db;
    $q = $db->query("select * from wd_mysql where dbuser='$user' and isuser=1");
    if ($db->num_rows($q) > 0)
        if ($t == 1) {
            echo "user exists";
            exit;
        } else go_back("该数据库用户已存在!");
}

function check_mysql_db($dbname, $t = 0)
{
    global $db;
    $q = $db->query("select * from wd_mysql where dbname='$dbname' and isuser=0");
    if ($db->num_rows($q) > 0)
        if ($t == 1) {
            echo "dbname exists";
            exit;
        } else go_back("该数据库已存在!");
}


//修改数据库大小
function mysql_db_size_edit($id, $size, $t = 0)
{
    global $db, $wdcp_gid;
    //if ($wdcp_gid!=1) go_back("无权修改，请联系管理员");//
    if (!is_numeric($id)) {
        //echo "select * from wd_mysql where dbname='$id' and isuser=0";
        $q = $db->query("select * from wd_mysql where dbname='$id' and isuser=0");
    } else
        $q = $db->query("select * from wd_mysql where id='$id'");
    if ($db->num_rows($q) == 0)
        if ($t == 1) {
            echo "db not exists";
            exit;
        } else go_back("数据库不存在!");
    if (!is_numeric($id))
        $db->query("update wd_mysql set dbsize='$size' where dbname='$id' and isuser=0");
    else
        $db->query("update wd_mysql set dbsize='$size' where id='$id'");
    return true;
}


function system_name_check($name, $t = 0, $tt = 0)
{
    global $db;
    $user_list = array("root", "test", "wdcp", "wdcdn");
    $db_list = array("mysql", "test", "wdcpdb", "wdcdndb");
    if ($t == 0) {
        if (in_array($name, $user_list))
            if ($tt == 1) {
                echo "user err";
                exit;
            } else go_back("数据库用户名与系统重复!");//
        $q = $db->query("select * from wd_mysql where dbuser='$name' and isuser=1");
        if ($db->num_rows($q) > 0)
            if ($tt == 1) {
                echo "user exists";
                exit;
            } else go_back("该数据库用户已存在!");
    }
    if ($t == 1) {
        if (in_array($name, $db_list))
            if ($tt == 1) {
                echo "dbname err";
                exit;
            } else go_back("数据库名与系统重复");
        //echo "select * from wd_mysql where dbname='$name'";
        $q = $db->query("select * from wd_mysql where dbname='$name' and isuser=0");
        if ($db->num_rows($q) > 0)
            if ($tt == 1) {
                echo "dbname exists";
                exit;
            } else go_back("该数据库已存在!");
    }
}

function wdl_sqlroot_pw()
{
    global $sqlrootpw_en, $sqlrootpw, $mykey;
    if ($sqlrootpw_en == 0)
        return $sqlrootpw;
    else
        return wdl_decrypt($sqlrootpw, $mykey);
}

function sql_insert($tab, $var, $value)
{
    global $db;
    //for

    //$q=$db->query("insert into wd_member(gid,name,passwd,xm,xb,sfzh,addr,tel,qq,email,rtime,state) values('$gid','$name','$passwd','$xm','$xb','$sfzh','$addr','$tel','$qq','$email','$rtime','$state')")
    //$db->query("insert into ");

}

function chg_mysql_passwd($id, $pass)
{
    global $db;
    $q = $db->query("select * from wd_mysql where id='$id'");
    if ($db->num_rows($q) == 0) go_back("ID不存在！");

}


function wd_mysql_add($uid, $sid, $user, $password, $host, $dbname, $dbcharset, $quotasize = 0, $isuser = 0, $rtime = 0, $t = 0)
{
    global $db;
    if ($rtime == 0) $rtime = time();
    if (!isset($host) or empty($host)) $host = "localhost";
    $npasswd = md5($password);
    $q = $db->query("insert into wd_mysql(uid,sid,dbuser,dbpw,dbhost,dbname,dbchar,dbsize,isuser,rtime) values('$uid','$sid','$user','$npasswd','$host','$dbname','$dbcharset','$quotasize','$isuser','$rtime')");
    //exit;
    if (!$q)
        if ($t == 1) {
            echo "add err";
            exit;
        } else go_back("保存失败！");
}

function mysql_add_db($uid, $sid, $dbname, $dbcharset, $quotasize = 0, $rtime = 0, $t = 0)
{
    global $db;
    if ($rtime == 0) $rtime = time();
    //if (!isset($host) or empty($host)) $host="localhost";
    $q = $db->query("insert into wd_mysql(uid,sid,dbname,dbchar,dbsize,rtime) values('$uid','$sid','$dbname','$dbcharset','$quotasize','$rtime')");
    //exit;
    if (!$q)
        if ($t == 1) {
            echo "add err";
            exit;
        } else go_back("保存失败！");
}

function mysql_del_db($id, $dbname, $t = 0)
{
    global $db;
    //if (!isset($host) or empty($host)) $host="localhost";
    //$q=$db->query("insert into wd_mysql(uid,sid,dbname,dbchar,dbsize,rtime) values('$uid','$sid','$dbname','$dbcharset','$quotasize','$rtime')");
    $q = $db->query("select * from wd_mysql where id='$id'");
    if ($db->num_rows($q) == 0)
        if ($t == 1) {
            echo "no dbanme";
            exit;
        } else go_back("数据库不存在!");
    $q = $db->query("delete from wd_mysql where id='$id'");
    //exit;
    if (!$q)
        if ($t == 1) {
            echo "del err";
            exit;
        } else go_back("数据库删除失败！");
}

function mysql_add_user($user, $password, $host, $dbname, $rtime = 0, $t = 0)
{
    global $db, $wdcp_uid, $sid;
    if (empty($sid)) $sid = 0;
    if ($rtime == 0) $rtime = time();
    if (!isset($host) or empty($host)) $host = "localhost";
    $npasswd = md5($password);
    $q = $db->query("insert into wd_mysql(uid,sid,dbuser,dbpw,dbhost,dbname,isuser,rtime) values('$wdcp_uid','$sid','$user','$npasswd','$host','$dbname','1','$rtime')");
    //exit;
    if (!$q)
        if ($t == 1) {
            echo "add err";
            exit;
        } else go_back("保存失败！");
}

function mysql_del_user($id, $user, $t = 0)
{
    global $db;
    //if (!isset($host) or empty($host)) $host="localhost";
    //$q=$db->query("insert into wd_mysql(uid,sid,dbname,dbchar,dbsize,rtime) values('$uid','$sid','$dbname','$dbcharset','$quotasize','$rtime')");
    $q = $db->query("select * from wd_mysql where id='$id'");
    if ($db->num_rows($q) == 0)
        if ($t == 1) {
            echo "no db";
            exit;
        } else go_back("数据库不存在!");
    $q = $db->query("delete from wd_mysql where id='$id'");
    //exit;
    if (!$q)
        if ($t == 1) {
            echo "del err";
            exit;
        } else go_back("数据库用户删除失败！");
}

function mysql_user_edit($id, $user, $password, $host, $t = 0)
{
    global $db;
    if (!is_numeric($id))
        $q = $db->query("select * from wd_mysql where dbuser='$id' and isuser=1");
    else
        $q = $db->query("select * from wd_mysql where id='$id'");
    if ($db->num_rows($q) == 0)
        if ($t == 1) {
            echo "user exists";
            exit;
        } else go_back("用户ID不存在!");
    if (!is_numeric($id))
        $q = $db->query("update wd_mysql set dbpw='$password',dbhost='$host' where dbuser='$user' and isuser=1");
    else
        //$q=$db->query("update wd_mysql set dbuser='$user',dbpw='$password',dbhost='$host' where id='$id'");
        $q = $db->query("update wd_mysql set dbpw='$password',dbhost='$host' where id='$id'");
    if (!$q)
        if ($t == 1) {
            echo "edit err";
            exit;
        } else go_back("用户修改失败!");
}


function wd_mysql_del($id, $user, $dbname, $flag, $t = 0)
{
    global $db;
    if ($flag == 1)
        $wh = "where id='$id'";
    elseif ($flag == 2)
        $wh = "where user='$user'";
    elseif ($flag == 3)
        $wh = "where dbname";
    else
        return;
    $q = $db->query("delete from wd_mysql where id='$id");
    if (!$q)
        if ($t == 1) {
            echo "del err";
            exit;
        } else go_back("删除失败!");
}

function create_db($dbname, $dbcharset, $t = 0)
{
    //global $db;
    $sqlroot = wdl_sqlroot_pw();
    if (!($link = @mysql_connect("localhost", "root", $sqlroot)))
        if ($t == 1) {
            echo "root pass err";
            exit;
        } else go_back("mysql root密码错误");
    //echo "CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET ".$dbcharset." COLLATE ".$dbcharset."_chinese_ci;";
    if ($dbcharset == "gbk")
        $sql = "CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET " . $dbcharset . " COLLATE " . $dbcharset . "_chinese_ci;\n";
    else
        $sql = "CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET " . $dbcharset . " COLLATE " . $dbcharset . "_general_ci;\n";
    $re = crunquery($sql);
    //exit;
    if (!$re)
        if ($t == 1) {
            echo "create err";
            exit;
        } else go_back("数据库创建失败！");
}

function drop_db($dbname, $t = 0)
{
    $sqlroot = wdl_sqlroot_pw();
    if (!($link = @mysql_connect("localhost", "root", $sqlroot)))
        if ($t == 1) {
            echo "root passwd err";
            exit;
        } else go_back("mysql root密码错误");
    //if (!mysql_select_db($dbname)) go_back("数据库不存在！");
    //DROP DATABASE IF EXISTS `789mmcn` ;
    $sql = "DROP DATABASE IF EXISTS `$dbname`";
    $re = crunquery($sql);
    if (!$re)
        if ($t == 1) {
            echo "del err";
            exit;
        } else go_back("数据库删除失败！");
}

function create_db_user($user, $password, $host = '', $t = 0)
{
    $sqlroot = wdl_sqlroot_pw();
    if (!($link = @mysql_connect("localhost", "root", $sqlroot)))
        if ($t == 1) {
            echo "root passwd err";
            exit;
        } else go_back("mysql root密码错误");
    if (!isset($host) or empty($host)) $host = "localhost";
    $sql = "CREATE USER '$user'@'$host' IDENTIFIED BY '$password';\n";
    $sql .= "flush privileges;";
    $re = crunquery($sql);
    if (!$re)
        if ($t == 1) {
            echo "create err";
            exit;
        } else go_back("数据库用户创建失败！");
}

function del_db_user($user, $host, $t = 0)
{
    $sqlroot = wdl_sqlroot_pw();
    if (!($link = @mysql_connect("localhost", "root", $sqlroot)))
        if ($t == 1) {
            echo "root passwd err";
            exit;
        } else go_back("mysql root密码错误");
    if (!isset($host) or empty($host)) $host = "localhost";
    $sql = "DROP USER '$user'@'$host';";
    //echo "DROP USER '$user'@'$host';";exit;
    $re = crunquery($sql);
    //if (!$re) go_back("数据库用户删除失败！");
}

function grant_db_user($user, $host = '', $dbname, $t = 0)
{
    $sqlroot = wdl_sqlroot_pw();
    if (!($link = @mysql_connect("localhost", "root", $sqlroot)))
        if ($t == 1) {
            echo "root passwd err";
            exit;
        } else go_back("mysql root密码错误");
    if (!isset($host) or empty($host)) $host = "localhost";
    $sql .= "GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP,INDEX,ALTER,CREATE TEMPORARY TABLES,SHOW VIEW ON `$dbname` .* TO '$user'@'$host';\n";
    $sql .= "flush privileges;";
    $re = crunquery($sql);
    if (!$re)
        if ($t == 1) {
            echo "grant err";
            exit;
        } else go_back("数据库用户授权失败！");
}

function dbuser_chg_password($user, $password, $host = '', $t = 0)
{
    //global $db;
    $sqlroot = wdl_sqlroot_pw();
    //$sqlroot="123456";
    if (!($link = @mysql_connect("localhost", "root", $sqlroot)))
        if ($t == 1) {
            echo "root passwd err";
            exit;
        } else go_back("mysql root密码错误");
    if (!isset($host) or empty($host)) $host = "localhost";
    //$sql="use mysql;\n";
    //$sql.="update user set password=password('$password') where user='$user' and host='$host';\n";
    //$sql.="flush privileges;";
    $sql = "SET PASSWORD FOR $user@'$host'=password('$password');";
    //echo "SET PASSWORD FOR $user@'$host'=password('$password');";exit;
    //echo $sql;//
    $re = crunquery($sql);
    //echo $re;
    //exit;
    if (!$re)
        if ($t == 1) {
            echo "edit err";
            exit;
        } else  go_back("修改数据库用户密码失败！");
    //$npaswd=md5($password);
    //$db->query("update wd_mysql set dbpw='$npasswd' where dbuser='$user' and dbhost='$host'");
}

function db_list()
{
    global $db, $wdcp_gid, $wdcp_uid;
    if (empty($id)) $id = 0;
    if ($wdcp_gid == 1) {
        $q = $db->query("select * from wd_mysql where isuser=0");
    } else {
        $q = $db->query("select * from wd_mysql where uid='$wdcp_uid' and isuser=0");
    }
    $msg = '<option value="0">选择数据库</option>\n';
    if ($db->num_rows($q) == 0) return $msg;
    while ($r = $db->fetch_array($q)) {
        if ($id == $r['id'])
            $msg .= '<option value="' . $r['dbname'] . '" selected="selected">' . $r['domain'] . '</option>\n';
        else
            $msg .= '<option value="' . $r['dbname'] . '">' . $r['dbname'] . '</option>\n';
    }
    return $msg;
}

//运行sql语句
function runquery($sql)
{
    global $lang, $tablepre, $db;

    if (!isset($sql) || empty($sql)) return;

    $sql = str_replace("\r", "\n", $sql);
    $ret = array();
    $num = 0;
    foreach (explode(";\n", trim($sql)) as $query) {
        $ret[$num] = '';
        $queries = explode("\n", trim($query));
        foreach ($queries as $query) {
            $ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0] . $query[1] == '--') ? '' : $query;
        }
        $num++;
    }
    unset($sql);

    foreach ($ret as $query) {
        $query = trim($query);
        if ($query) {

            if (substr($query, 0, 12) == 'CREATE TABLE') {
                $db->query(createtable($query)) or go_back("数据表创建失败!");
            } else {
                $db->query($query) or go_back("SQL执行失败!");
            }

        }
    }

}


//sql表分析
function createtable($sql)
{
    $type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
    $type = in_array($type, array('MYISAM', 'HEAP')) ? $type : 'MYISAM';
    return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql) .
    (mysql_get_server_info() > '4.1' ? " ENGINE=$type DEFAULT CHARSET=" . GBK : " TYPE=$type");
}

/////////
//运行sql语句
function crunquery($sql)
{
    global $lang, $tablepre;

    if (!isset($sql) || empty($sql)) return;

    $sql = str_replace("\r", "\n", $sql);
    $ret = array();
    $num = 0;
    foreach (explode(";\n", trim($sql)) as $query) {
        $ret[$num] = '';
        $queries = explode("\n", trim($query));
        foreach ($queries as $query) {
            $ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0] . $query[1] == '--') ? '' : $query;
        }
        $num++;
    }
    unset($sql);

    foreach ($ret as $query) {
        $query = trim($query);
        if ($query) {

            if (substr($query, 0, 12) == 'CREATE TABLE') {
                return mysql_query(createtable($query));// or go_back("数据表创建失败!");
            } else {
                return mysql_query($query);// or go_back("SQL执行失败!");
            }

        }
    }
}

?>