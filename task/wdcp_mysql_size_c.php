<?php
/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/
/*
SELECT sum( data_length + index_length ) /1024 /1024 AS total_mb FROM information_schema.tables where table_schema='wdlinux_cn';
total_mb  
117.38788033
*/


set_time_limit(0);
if (substr(php_sapi_name(),0,3) !== 'cli') exit;
require_once "/www/wdlinux/wdcp/inc/common.inc.php";
exec("ps ax | grep -v grep | grep mysql_size_c.php | wc -l",$str,$re);
if ($str[0]>1) exit;
//$q=$db->query("select id,dbname,dbsize from wd_mysql where state=0");
$q=$db->query("select id,dbname,dbsize,state from wd_mysql where dbsize!=0 and isuser=0");
$all_db_list=array();
$i=0;
while ($r=$db->fetch_array($q)) {
	if ($r['dbsize']==0) continue;
	$all_db_list[$i]['id']=$r['id'];
	$all_db_list[$i]['dbname']=$r['dbname'];
	$all_db_list[$i]['dbsize']=$r['dbsize'];
	$all_db_list[$i]['state']=$r['state'];
	//echo $r['state']."|||\n";
	$i++;
}
//print_r($all_db_list);exit;

if (!file_exists(WD_ROOT."/data/dbr.inc.php")) exit;
require_once WD_ROOT."/data/dbr.inc.php";

/*
$sqlrootpw='wdlinux.cn';
$sqlrootpw_en='0';
*/
/*
	$sqlroot=wdl_sqlroot_pw();
	if (!($link = @mysql_connect("localhost","root",$sqlroot))) go_back("mysql root密码错误");
	//echo "CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET ".$dbcharset." COLLATE ".$dbcharset."_chinese_ci;";
	if ($dbcharset=="gbk")
		$sql="CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET ".$dbcharset." COLLATE ".$dbcharset."_chinese_ci;\n";
	else
		$sql="CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET ".$dbcharset." COLLATE ".$dbcharset."_general_ci;\n";
	$re=crunquery($sql);
*/
if ($sqlrootpw_en==0)
	$sqlrp=$sqlrootpw;
else
	$sqlrp=wdl_sqlroot_pw();

$db_stop="";
$db_start="start db list:";
$db_noa="noa db list:";
if (!($link = @mysql_connect("localhost","root",$sqlrp))) exit;
$j=0;
$c=0;
for ($i=0;$i<sizeof($all_db_list);$i++) {
	if (empty($all_db_list[$i])) continue;
	//echo $all_db_list[$i]['dbname']."\n";continue;
	//echo "SELECT sum( data_length + index_length ) /1024 /1024 AS total_mb FROM information_schema.tables where table_schema='$all_db_list[$i][dbname]';\n";
	$dbnamet=$all_db_list[$i]['dbname'];
	$q=@mysql_query("SELECT sum( data_length + index_length ) /1024 /1024 AS total_mb FROM information_schema.tables where table_schema='$dbnamet';");
	$r=@mysql_fetch_array($q);
	//print_r($r);continue;
	//echo $r['total_mb']."|\n";
	$dbsizet=round($r['total_mb']);
	//echo $dbsizet."||\n";
	//echo $dbsizet."|".$all_db_list[$i]['dbsize']."|".$all_db_list[$i]['state']."\n";//continue;
	if ($dbsizet>=$all_db_list[$i]['dbsize'] and $all_db_list[$i]['state']==0) {
		//echo "update db set Insert_priv='N',Update_priv='N' where db='$dbnamet'";
		if ($mysql_quota_is==1) {
			@mysql_query("update mysql.db set Insert_priv='N',Update_priv='N' where db='$dbnamet'");
			$db->query("update wd_mysql set state=1 where dbname='$dbnamet' and isuser=0");
			$j++;
		}
		$db_stop.=$all_db_list[$i]['dbname']." ";
	}elseif ($dbsizet<$all_db_list[$i]['dbsize'] and $all_db_list[$i]['state']==1){
		//echo "update db set Insert_priv='Y',Update_priv='Y' where db='$dbnamet'";
		if ($mysql_quota_is==1) {
			@mysql_query("update mysql.db set Insert_priv='Y',Update_priv='Y' where db='$dbnamet'");
			$db->query("update wd_mysql set state=0 where dbname='$dbnamet' and isuser=0");
			//$j++;
		}
		$db_start.=$all_db_list[$i]['dbname'];
	}else;
		//$db_noa.=$all_db_list[$i]['dbname'];
	$c++;
}
//echo $i."|i\n";
if ($j>0)
	@mysql_query("flush privileges;");
mysql_close($link);

$msg="总共检查$c个数据库,$j个超额，数据库名:$db_stop";
$rtime=time();
$db->query("insert into wd_tasklog(name,note,rtime) values('数据库大小检查','$msg',$rtime)");
//echo $db_stop."\n";
//echo $db_start."\n";
//echo $db_noa."\n";
//echo "OK";
?>