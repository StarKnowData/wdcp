<?php
/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/

set_time_limit(0);
if (substr(php_sapi_name(),0,3) !== 'cli') exit;
sleep(30);
require_once "/www/wdlinux/wdcp/inc/common.inc.php";

if ($web_logs_logrotate!=1) exit;
if ($site_logs_is!=1) exit;

$yd=date("Ymd",time()-6400);
$q=$db->query("select * from wd_site where access_log=1 or error_log=1");
while ($r=$db->fetch_array($q)) {
	//drupal.com_access_log-20120525.gz
	$tdir=$r['vhost_dir']."/logs";
	if (!@is_dir($tdir)) exec("mkdir -p $tdir;chown -R www.www $tdir");
	//echo $tdir."\n";
	//echo $web_eng;exit;
	$tflist="";
	if ($web_eng==3) {
		$tf=$web_logs_home."/".$r['domain']."_access_log-".$yd.".gz";
		$tf1=$web_logs_home."/".$r['domain']."_access_log-".$yd;
		if (@file_exists($tf)) $tflist.=$tf." ";
		//elseif (@file_exists($tf1)) $tflist.=$tf1." ";
		//else;
		$tf=$web_logs_home."/".$r['domain']."_access.log-".$yd.".gz";
		$tf1=$web_logs_home."/".$r['domain']."_access.log-".$yd;
		if (@file_exists($tf)) $tflist.=$tf." ";
		//elseif (@file_exists($tf1)) $tflist.=$tf1." ";
		//else;
		//
		$tf=$web_logs_home."/".$r['domain']."_error_log-".$yd.".gz";
		$tf1=$web_logs_home."/".$r['domain']."_error_log-".$yd;
		if (@file_exists($tf)) $tflist.=$tf." ";
		//elseif (@file_exists($tf1)) $tflist.=$tf1." ";
		//else;
		$tf=$web_logs_home."/".$r['domain']."_error.log-".$yd.".gz";
		$tf1=$web_logs_home."/".$r['domain']."_error.log-".$yd;
		if (@file_exists($tf)) $tflist.=$tf." ";
		//elseif (@file_exists($tf1)) $tflist.=$tf1." ";
		//else;
		//echo $tflist;
		if (!empty($tflist))
			exec("mv $tflist $tdir;chown -R www.www $tdir");	
	}elseif($web_eng==1) {
		$tf=$web_logs_home."/".$r['domain']."_access_log-".$yd.".gz";
		$tf1=$web_logs_home."/".$r['domain']."_access_log-".$yd;
		if (@file_exists($tf)) $tflist.=$tf." ";
		//elseif (@file_exists($tf1)) $tflist.=$tf1." ";
		//else;
		//
		$tf=$web_logs_home."/".$r['domain']."_error_log-".$yd.".gz";
		$tf1=$web_logs_home."/".$r['domain']."_error_log-".$yd;
		if (@file_exists($tf)) $tflist.=$tf." ";
		//elseif (@file_exists($tf1)) $tflist.=$tf1." ";
		//else;
		//echo $tflist;
		if (!empty($tflist))
			exec("mv $tflist $tdir;chown -R www.www $tdir");		
	}else{
		$tf=$web_logs_home."/".$r['domain']."_access.log-".$yd.".gz";
		$tf1=$web_logs_home."/".$r['domain']."_access.log-".$yd;
		if (@file_exists($tf)) $tflist.=$tf." ";
		//elseif (@file_exists($tf1)) $tflist.=$tf1." ";
		//else;
		//
		$tf=$web_logs_home."/".$r['domain']."_error.log-".$yd.".gz";
		$tf1=$web_logs_home."/".$r['domain']."_error.log-".$yd;
		if (@file_exists($tf)) $tflist.=$tf." ";
		//elseif (@file_exists($tf1)) $tflist.=$tf1." ";
		//else;
		//echo $tflist;
		if (!empty($tflist))
			exec("mv $tflist $tdir;chown -R www.www $tdir");
	}
}
?>