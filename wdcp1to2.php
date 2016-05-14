<?
if (substr(php_sapi_name(),0,3) !== 'cli') exit;
if (@file_exists("/www/wdlinux/wdcp/data/1to2_up.txt")) { echo "is updated";exit;}

echo "start update ....";
if (!@file_exists("/www/web/wdcp/data/db.inc.php")) { echo "wdcp not exists";exit;}
if (!@is_dir("/www/wdlinux/wdcp")) { echo "new wdcp is not install";exit;}


$ct=date("Ymd");
//system("tar zcvf /www/backup/mysql_$ct.tar.gz /www/wdlinux/mysql/var");


//system("service vsftpd stop");
if (@is_dir("/www/wdlinux/nginx/conf")) {
	system("sed -i '/wdcp.conf/d' /www/wdlinux/nginx/conf/nginx.conf");
	system("chown -R wdcpu.wdcpg /www/wdlinux/nginx/conf/vhost");
	system("chown -R wdcpu.wdcpg /www/wdlinux/nginx/conf/rewrite");
	system("service httpd restart");
}
if (@is_dir("/www/wdlinux/apache/conf")) {
	system("sed -i '/wdcp.conf/d' /www/wdlinux/apache/conf/httpd.conf");
	system("chown -R wdcpu.wdcpg /www/wdlinux/apache/conf/vhost");
	system("chown -R wdcpu.wdcpg /www/wdlinux/apache/conf/rewrite");
	system("service nginxd restart");
}

system("sed -i 's/www /wdcpu /g' /etc/sudoers");
//system("groupadd -g 1000 www");
//system("useradd -g 1000 -u 1000 -d /dev/null -s /sbin/nologin www");
system("chown -R wdcpu.wdcpg /www/wdlinux/etc");
//system("chown -R www.www /www/web");


//sql
//copy("/www/web/wdcp/data/db.inc.php","/www/wdlinux/wdcp/data/");
require_once "/www/wdlinux/wdcp/inc/common.inc.php";
//Õ¾µã
$q=$db->query("select * from wd_host where domain!='local'");
while ($r=$db->fetch_array($q)) {
	//$db->query("insert into ");
	$domain=$r['domain'];
	//echo $domain."<br>\n";
	if (empty($domain)) continue;
	$domains=$r['domains'];
	$domainss=$r['domainss'];
	if (eregi("\.",$r['vhost_dir'])) {
		$o_dir=$r['vhost_dir'];//
		$wvhost_dir=str_replace(".","_",$r['vhost_dir']);
		system("mv $o_dir $wvhost_dir");
	}else
		$wvhost_dir=$r['vhost_dir'];
	$dindex=$r['dindex'];
	$err400=$r['err400'];
	$err401=$r['err401'];
	$err403=$r['err403'];
	$err404=$r['err404'];
	$err405=$r['err405'];
	$err500=$r['err500'];
	$err503=$r['err503'];
	$access_log=$r['access_log'];
	$error_log=$r['error_log'];
	$limit_dir=$r['limit_dir'];
	$rtime=$r['rtime'];
	$state=$r['state'];
	$rewrite=$r['rewrite'];
	//echo $domain."<br>\n";
	$db->query("insert into wd_site(id,uid,domain,domains,domainss,vhost_dir,limit_dir,dir_index,err400,err401,err403,err404,err405,err500,err503,access_log,error_log,rewrite,rtime,state) values (NULL,'1','$domain','$domains','$domainss','$wvhost_dir','$limit_dir','$dindex','$err400','$err401','$err403','$err404','$err405','$err500','$err503','$access_log','$error_log','$rewrite','$rtime','$state');");
}


//ftp
$q=$db->query("select * from wd_host where ftpuser!=''");
while ($r=$db->fetch_array($q)) {
	$ftpuser=$r['ftpuser'];
	if (empty($ftpuser)) continue;
	system("userdel $ftpuser");
	$ftppasswd=$r['ftppasswd'];
	$rtime=$r['rtime'];
	$wvhost_dir=str_replace(".","_",$r['vhost_dir']);
	$q1=$db->query("select * from wd_site where vhost_dir='$wvhost_dir'");
	$r1=$db->fetch_array($q1);
	$sid=$r1['id'];
	$db->query("insert into wd_ftp(sid,mid,user,password,dir,quotasize,rtime) values ('$sid','1','$ftpuser','$ftppasswd','$wvhost_dir','0','$rtime')");
}


//Êý¾Ý¿â
$q=$db->query("select * from wd_host where dbname!=''");
while ($r=$db->fetch_array($q)) {
	$dbuser=$r['dbuser'];
	if (empty($dbuser)) continue;
	$dbname=$r['dbname'];
	$dbpasswd=$r['dbpasswd'];
	$dbcharset=$r['dbcharset'];
	$rtime=$r['rtime'];
	$wvhost_dir=str_replace(".","_",$r['vhost_dir']);
	$q1=$db->query("select * from wd_site where vhost_dir='$wvhost_dir'");
	$r1=$db->fetch_array($q1);
	$sid=$r1['id'];
	$db->query("insert into wd_mysql(uid,sid,dbname,dbchar,dbsize,rtime) values('1','$sid','$dbname','$dbcharset','0','$rtime')");
	$db->query("insert into wd_mysql(uid,dbuser,dbpw,dbhost,dbname,isuser,rtime) values('1','$dbuser','$dbpasswd','localhost','$dbname','1','$rtime')");
}

$q=$db->query("select * from wd_site");
while ($r=$db->fetch_array($q)) {
	$sid=$r['id'];
	update_vhost($sid);
}
if (@is_dir("/www/wdlinux/nginx")) system("service nginxd restart");
if (@is_dir("/www/wdlinux/apache")) system("service httpd restart");
system("mkdir -p /www/web_logs");

@touch("/www/wdlinux/wdcp/data/1to2_up.txt");
echo "1.X update to 2 success";
echo "";

?>