<?php
/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/

set_time_limit(0);
if (substr(php_sapi_name(),0,3) !== 'cli') exit;

require_once "/www/wdlinux/wdcp/inc/common.inc.php";

if (substr(getcwd(),0,17)!="/www/wdlinux/wdcp") exit;

//web dir
$wdir_tmp="/www/wdlinux/wdcp/data/tmp/wdir.txt";
if (@file_exists($wdir_tmp)) {
	$dir=@file_get_contents($wdir_tmp);
	if (!@is_dir($dir)) {
		exec("mkdir -p $dir",$str,$re);
		if (@is_dir("/etc/skel/public_html")) 
			if (eregi("public_html",$dir))
				exec("cp -pR /etc/skel/public_html/* $dir",$str,$re);
			else
				exec("cp -pR /etc/skel/public_html $dir",$str,$re);
		else
			if (!eregi("public_html",$dir))
				exec("mkdir -p $dir/public_html",$str,$re);
		$dir=str_replace("/public_html","",$dir);//20120614
		exec("chown -R www.www $dir",$str,$re);
	}
	@unlink($wdir_tmp);
	exit;
}

//ftpdir
$ftp_tmp="/www/wdlinux/wdcp/data/tmp/ftp.txt";
if (@file_exists($ftp_tmp)) {
	$str=@file_get_contents($ftp_tmp);
	@unlink($ftp_tmp);
	if (!@is_dir($str)) {
		exec("mkdir -p $str;chown -R www.www $str",$str,$re);
	}
	exit;
}


//check ower wdcpu
$ower_wdcp_tmp="/www/wdlinux/wdcp/data/tmp/ower_wdcp.txt";
if (@file_exists($ower_wdcp_tmp)) {
	$f=chop(@file_get_contents($ower_wdcp_tmp));
	$f1=explode("/",$f);
	if ( @file_exists($f) and !@file_exists($wdcp_bk_cf."/".end($f1))) exec("cp $f $wdcp_bk_cf",$str,$re);
	if (@is_dir($f)) exec("chown -R wdcpu.wdcpg $f",$str,$re);
	elseif (@is_file($f)) exec("chown wdcpu.wdcpg $f",$str,$re);
	else { @touch($f);exec("chown wdcpu.wdcpg $f",$str,$re);}
	@unlink($ower_wdcp_tmp);
	echo $re;
	exit;
}

//check perm
$perm_tmp="/www/wdlinux/wdcp/data/tmp/perm.txt";
if (@file_exists($perm_tmp)) {
	$str=@file_get_contents($perm_tmp);
	$s1=explode("|",$str);
	if ($s1[1]==1)
		exec("cd $s1[2];chmod -R $s1[0] $s1[3]",$str1,$re);
	else
		exec("cd $s1[2];chmod $s1[0] $s1[3]",$str1,$re);
	@unlink($perm_tmp);
	echo $re;
	exit;
}

//check ower
$ower_tmp="/www/wdlinux/wdcp/data/tmp/ower.txt";
if (@file_exists($ower_tmp)) {
	$str=@file_get_contents($ower_tmp);
	$s1=explode("|",$str);
	if ($s1[1]==1)
		exec("cd $s1[2];chown -R $s1[0] $s1[3]",$str1,$re);
	else
		exec("cd $s1[2];chown $s1[0] $s1[3]",$str1,$re);
	@unlink($ower_tmp);
	//echo implode("\n",$str1);
	echo $re;
	exit;
}

//check group
$group_tmp="/www/wdlinux/wdcp/data/tmp/group.txt";
if (@file_exists($group_tmp)) {
	$str=@file_get_contents($group_tmp);
	$s1=explode("|",$str);
	if ($s1[1]==1)
		exec("cd $s1[2];chgrp -R $s1[0] $s1[3]",$str,$re);
	else
		exec("cd $s1[2];chgrp $s1[0] $s1[3]",$str,$re);
	@unlink($group_tmp);
	echo $re;
	exit;
}

//tar file
$tar_tmp="/www/wdlinux/wdcp/data/tmp/tar.txt";
if (@file_exists($tar_tmp)) {
	$str=@file_get_contents($tar_tmp);
	$s1=explode("|",$str);
	if ($s1[1]=="no") 
		$f=date("YmdHi").".tar.gz";	
	else
		$f=$s1[1].".tar.gz";
	exec("cd $s1[0];tar zcvf $f $s1[2]",$str,$re);
	@unlink($tar_tmp);
	exit;
}

//tar file
$untar_tmp="/www/wdlinux/wdcp/data/tmp/untar.txt";
if (@file_exists($untar_tmp)) {
	$str=@file_get_contents($untar_tmp);
	//echo $str;exit;
	$s1=explode("|",$str);
	@unlink($untar_tmp);
	//if (!is_dir($s1[0]) or !is_file($s1[1])) exit;
	if (!@is_dir($s1[0]) or !@is_file($s1[0]."/".$s1[1])) exit;
	$t1=explode(".",$s1[1]);
	$t2=end($t1);
	if ($t2=="tar")
		exec("cd $s1[0];tar xvf $s1[1]",$str1,$re);//print_r($str);print_r($re);exit;
	//elseif ($t2=="gz" or $t2=="tgz")
	elseif ($t2=="tar.gz" or $t2=="tgz")
		exec("cd $s1[0];tar zxvf $s1[1]",$str1,$re);//print_r($str);print_r($re);exit;
	elseif ($t2=="gz")
		if (substr($s1[1],-7)==".tar.gz")
			exec("cd $s1[0];tar xvf $s1[1]",$str1,$re);
		else
			exec("cd $s1[0];gzip -d $s1[1]",$str1,$re);//
	elseif ($t2=="bz2")
		exec("cd $s1[0];tar jxvf $s1[1]",$str1,$re);//print_r($str);print_r($re);exit;
	elseif ($t2=="zip")
		exec("cd $s1[0];unzip -xo $s1[1]",$str1,$re);//
	else;
	if (eregi("public_html",$s1[0]))//
		exec("cd $s1[0];chown -R www.www *",$str1,$re);
	//exec("cd $s1[0];tar zcvf $f $s1[2]",$str,$re);
	exit;
}

//del file
$del_tmp="/www/wdlinux/wdcp/data/tmp/del.txt";
if (@file_exists($del_tmp)) {
	$str=@file_get_contents($del_tmp);
	@unlink($del_tmp);
	$s1=explode("|",$str);
	if (!@isset($trash_home) or empty($trash_home)) $trash_home="/www/trash";//
	if (!@is_dir($trash_home)) exec("mkdir -p $trash_home",$str,$re);//@mkdir($trash_home,"755");
	//$ndir=$trash_home."/".date("Y/m/d");
	$ndir=$trash_home."/".date("Y/m/d/Hi");//
	if (!@is_dir($ndir)) exec("mkdir -p $ndir",$str,$re);
	exec("cd $s1[0];mv $s1[1] $ndir",$str,$re);
	exit;
}
//move file
$move_tmp="/www/wdlinux/wdcp/data/tmp/move.txt";
if (@file_exists($move_tmp)) {
	$str=chop(@file_get_contents($move_tmp));
	//echo $str;//exit;
	@unlink($move_tmp);
	$s1=explode("|",$str);
	$sdir=$s1[0];
	$ddir=$s1[1];
	if (substr($ddir,0,1)=="/")
		$ddir1=$ddir;
	else
		$ddir1=$sdir."/".$ddir;
	$flist=$s1[2];
	if (!@is_dir($sdir)) {echo 1;exit;}
	//if (!@is_dir($ddir1)) {echo 1;exit;}//
	//echo $flist."|".$ddir;
	exec("cd $sdir;mv $flist $ddir",$str,$re);
	echo $re;
	exit;
}

//copy file
$copy_tmp="/www/wdlinux/wdcp/data/tmp/copy.txt";
if (@file_exists($copy_tmp)) {
	$str=chop(@file_get_contents($copy_tmp));
	//echo $str;//exit;
	@unlink($copy_tmp);
	$s1=explode("|",$str);
	$sdir=$s1[0];
	$ddir=$s1[1];
	if (substr($ddir,0,1)=="/")
		$ddir1=$ddir;
	else
		$ddir1=$sdir."/".$ddir;
	$flist=$s1[2];
	if (!@is_dir($sdir)) {echo 1;exit;}
	if (!@is_dir($ddir1)) {echo 1;exit;}
	//echo $flist."|".$ddir;
	exec("cd $sdir;cp -pR $flist $ddir",$str,$re);
	echo $re;
	exit;
}

//cp file
$cp_tmp="/www/wdlinux/wdcp/data/tmp/cp.txt";
if (@file_exists($cp_tmp)) {
	$str=@file_get_contents($cp_tmp);
	$s1=explode("|",$str);
	if (!eregi($web_home,$s1[1]) and @is_dir($wdcp_bk_def)) {
		@copy($s1[1],$wdcp_bk_def);
	}
	@copy($s1[0],$s1[1]);
	@unlink($cp_tmp);
	exit;
}

//rmdir
$rmdir_tmp="/www/wdlinux/wdcp/data/tmp/rmdir.txt";
if (@file_exists($rmdir_tmp)) {
	//echo @file_get_contents($rmdir_tmp);
	$str=chop(@file_get_contents($rmdir_tmp));
	//echo $str;
	@unlink($rmdir_tmp);
	//echo $str;
	//echo "11";
	if (!@is_dir($str)) exit;
	$ddir=array("/","/usr","/etc","/var","/boot","/sbin","/bin","/home","/tmp","/proc","/lib","/sys","/root","/selinux","/srv","/sys");
	//echo "12";
	if (in_array($str,$ddir)) exit;
	//echo "13";
	if (eregi("\.\.",$str)) exit;
	//echo "14";
	if (!eregi("$web_home",$str)) exit;
	//echo "22";
	//echo $str;
	exec("rm -fr $str",$str,$re);
	exit;
}

//下载文件
$durl_tmp="/www/wdlinux/wdcp/data/tmp/durl.txt";
if (@file_exists($durl_tmp)) {
	$str=chop(@file_get_contents($durl_tmp));
	//echo $str;
	@unlink($durl_tmp);
	$s1=explode("|",$str);
	if (!@is_dir($s1[0])) {
		echo 0;
		exit;
	}
	exec("cd $s1[0];wget -cbq $s1[1] -o /dev/null",$str,$re);
	echo $re;
	exit;
}

//创建目录
$cdir_tmp="/www/wdlinux/wdcp/data/tmp/cdir.txt";
if (@file_exists($cdir_tmp)) {
	$str=chop(@file_get_contents($cdir_tmp));
	//echo $str;
	@unlink($cdir_tmp);
	$s1=explode("|",$str);
	if (!@is_dir($s1[0])) {
		echo 0;
		exit;
	}
	if (@is_dir($s1[0]."/".$s1[1])) {echo 1;exit;}
	exec("cd $s1[0];mkdir -p $s1[1];chown -R www.www $s1[1]",$str,$re);
	echo $re;
	exit;
}

//创建文件
$cfile_tmp="/www/wdlinux/wdcp/data/tmp/cfile.txt";
if (@file_exists($cfile_tmp)) {
	$str=chop(@file_get_contents($cfile_tmp));
	//echo $str;
	@unlink($cfile_tmp);
	$s1=explode("|",$str);
	if (!@is_dir($s1[0])) {
		echo 0;
		exit;
	}
	if (@is_file($s1[0]."/".$s1[1])) {echo 1;exit;}
	exec("cd $s1[0];touch $s1[1];chown -R www.www $s1[1]",$str,$re);
	echo $re;
	exit;
}

//上传文件
$ufile_tmp="/www/wdlinux/wdcp/data/tmp/ufile.txt";
if (@file_exists($ufile_tmp)) {
	$str=chop(@file_get_contents($ufile_tmp));
	//echo $str;
	@unlink($ufile_tmp);
	$s1=explode("|",$str);
	if (!@is_dir($s1[0])) {
		echo 0;
		exit;
	}
	//if (@is_file($s1[0]."/".$s1[1])) {echo 1;exit;}
	//echo $s1[1];
	exec("cd /www/wdlinux/wdcp/data/tmp; mv $s1[1] $s1[0];cd $s1[0];chown -R www.www $s1[1]",$str,$re);
	echo $re;
	exit;
}

//update
$update_tmp="/www/wdlinux/wdcp/data/tmp/update.txt";
if (@file_exists($update_tmp)) {
	$n=chop(@file_get_contents($update_tmp));
	@unlink($update_tmp);
	if (!eregi("http",$n)) {
		$f=chop($n)."_last.tar.gz";
		$af="/tmp/$f";
		$d_url="http://up.wdlinux.cn/down/$f";
	}else{
		$f1=explode("/",$n);
		$f=end($f1);
		$af="/tmp/$f";
		$d_url=$n;
	}
	exec("cd /tmp;wget -cq $d_url",$str,$re);//
	if (file_exists($af)) {
		//exec("tar zxvf $af -C /",$str,$re);print_r($str);print_r($re);
		exec("tar zxvf $af -C / >/dev/null",$str,$re);
		//echo "tar";
	}
	if (@file_exists($af))
		@unlink($af);
	//print_r($str);
	exit;
}

//连接数
$netstat_tmp="/www/wdlinux/wdcp/data/tmp/netstat.txt";
if (@file_exists($netstat_tmp)) {
	$str=@file_get_contents($netstat_tmp);
	if ($str=="state"){
		exec("netstat -n|awk '/^tcp/{++S[\$NF]} END {for(a in S) print a,S[a]}'",$str,$re);
	}elseif($str=="ip") {
		exec("netstat -n | awk '/^tcp/{print \$5}' | sed 's/::ffff://' | cut -d: -f1 | sort | uniq -c | sort -nr | head -60",$str,$re);
	}elseif($str=="web") {
		exec("netstat -n|grep ':80' | awk '/^tcp/{++S[\$NF]} END {for(a in S) print a,S[a]}'",$str,$re);
	}elseif($str=="mysql") {
		exec("netstat -n|grep ':3306' | awk '/^tcp/{++S[\$NF]} END {for(a in S) print a,S[a]}'",$str,$re);
	}else
		exec("netstat -n | awk '/^tcp/{print \$5}' | wc -l",$str,$re);
	@unlink($netstat_tmp);
	echo implode("\n",$str);
	exit;		
}

//系统启动服务
$service_tmp="/www/wdlinux/wdcp/data/tmp/service.txt";
if (@file_exists($service_tmp)) {
	//if (@filesize($service_tmp)==0) {
	@unlink($service_tmp);
	/*
	if ($os_rl==2) {
		exec("service --status-all",$str,$re);
		$result="";
		for ($i=0;$i<sizeof($str);$i++) {
			if (eregi("+",$str[$i])) {
				
		}
	}else{
	*/
		exec("/sbin/chkconfig --list | awk -F\" \" '{print \$1\" \"\$5}'",$str,$re);
		//exec("$service_cmd --status-all | grep 'running...'",$str,$re);
		echo implode("\n",$str);
	//}	
	exit;
}

//进程列表
$process_tmp="/www/wdlinux/wdcp/data/tmp/process.txt";
if (@file_exists($process_tmp)) {
	$id=@file_get_contents($process_tmp);
	if (@is_numeric($id)) {
		exec("kill -9 $id",$str,$re);
		echo $re;
		@unlink($process_tmp);
	}else{
		exec('ps -eo pid,user,command | tr -s " " "|"',$str,$re);
		echo implode("\n",$str);
		@unlink($process_tmp);
	}
	exit;
}

//端口列表
$port_tmp="/www/wdlinux/wdcp/data/tmp/port.txt";
if (@file_exists($port_tmp)) {
	//echo "11";
	exec('netstat -lnp | grep -E "tcp|udp" | tr -s " " "|"',$str,$re);
	echo implode("\n",$str);
	@unlink($port_tmp);
	exit;
}

//内在释放
$mem_tmp="/www/wdlinux/wdcp/data/tmp/mem.txt";
if (@file_exists($mem_tmp)) {
	exec("/bin/sync",$str,$re);
	if ($re==0) {
		@file_put_contents("/proc/sys/vm/drop_caches",1);
		@file_put_contents("/proc/sys/vm/drop_caches",2);
		@file_put_contents("/proc/sys/vm/drop_caches",3);
	}
	@unlink($mem_tmp);
	echo $re;
	exit;
}

//DNS设置
$resolv_tmp="/www/wdlinux/wdcp/data/tmp/resolv.conf";
$resolv_conf="/etc/resolv.conf";
if (@file_exists($resolv_tmp)) {
	@copy($resolv_tmp,$resolv_conf);
	@unlink($resolv_tmp);
	exit;
}

//命令运行器
$cmd_tmp="/www/wdlinux/wdcp/data/tmp/cmd.txt";
if (@file_exists($cmd_tmp)) {
	$str=@file_get_contents($cmd_tmp);
	$s1=explode(" ",trim($str));
	@unlink($cmd_tmp);
	$s2="";
	for ($i=1;$i<sizeof($s1);$i++)
		$s2.=$s1[$i]." ";
	$arg=substr($s2,0,strlen($s2)-1);
	//echo "$s1[0] $arg\n";
	exec("$s1[0] $arg",$str,$re);
	//echo implode("\n",$str);
	$msg="";
	foreach($str as $v) {
		if (eregi("wdcp_",$v)) continue;
		$msg.=$v."\n";
	}
	echo $msg;
	exit;
}

//启动服务
$start_tmp="/www/wdlinux/wdcp/data/tmp/start.txt";
if (@file_exists($start_tmp)) {
	$str=chop(@file_get_contents($start_tmp));
	$srvp="/etc/init.d/$str";
	@unlink($start_tmp);
	if (@file_exists($srvp))
		exec("$service_cmd $str start",$str,$re);
	echo $re;
	exit;
}
//启动服务,随机启动
$starts_tmp="/www/wdlinux/wdcp/data/tmp/starts.txt";
if (@file_exists($starts_tmp)) {
	$str=chop(@file_get_contents($starts_tmp));
	$srvp="/etc/init.d/$str";
	@unlink($starts_tmp);
	if (@file_exists($srvp))
		exec("/sbin/chkconfig --level 35 $str on",$str,$re);
	echo $re;
	exit;
}

//停止服务
$stop_tmp="/www/wdlinux/wdcp/data/tmp/stop.txt";
if (@file_exists($stop_tmp)) {
	$str=chop(@file_get_contents($stop_tmp));
	@unlink($stop_tmp);
	$srvp="/etc/init.d/$str";
	if (@file_exists($srvp))
		exec("$service_cmd $str stop",$str,$re);
	echo $re;
	exit;
}
//停止服务，关闭随机启动
$stops_tmp="/www/wdlinux/wdcp/data/tmp/stops.txt";
if (@file_exists($stops_tmp)) {
	$str=chop(@file_get_contents($stops_tmp));
	@unlink($stops_tmp);
	$srvp="/etc/init.d/$str";
	if (@file_exists($srvp))
		exec("/sbin/chkconfig --level 35 $str off",$str,$re);
	echo $re;
	exit;
}

//重起服务
$restart_tmp="/www/wdlinux/wdcp/data/tmp/restart.txt";
if (@file_exists($restart_tmp)) {
	$str=chop(@file_get_contents($restart_tmp));
	//$srvn=chop($str);
	@unlink($restart_tmp);
	$s1=explode(",",$str);
	for ($i=0;$i<sizeof($s1);$i++) {
		if (empty($s1[$i])) continue;
		//$srvp="/etc/rc.d/init.d/".$s1[$i];
		if ($os_rl==2 and $s1[$i]=="sshd") $s1[$i]="ssh";
		$srvp="/etc/init.d/".$s1[$i];
		//echo $srvp;
		if (@file_exists($srvp))
			exec("$service_cmd $s1[$i] restart",$str,$re);
	}
	echo $re;
	exit;
}

//重载服务
$reload_tmp="/www/wdlinux/wdcp/data/tmp/reload.txt";
if (@file_exists($reload_tmp)) {
	$str=chop(@file_get_contents($reload_tmp));
	@unlink($reload_tmp);
	//$srvn=chop($str);
	$s1=explode(",",$str);
	for ($i=0;$i<sizeof($s1);$i++) {
		if (empty($s1[$i])) continue;
		//$srvp="/etc/rc.d/init.d/".$s1[$i];
		$srvp="/etc/init.d/".$s1[$i];
		//echo $srvp;
		if (@file_exists($srvp))
			exec("$service_cmd $s1[$i] reload",$str,$re);
	}
	echo $re;
	exit;
}

//重起机器
$reboot_tmp="/www/wdlinux/wdcp/data/tmp/reboot.txt";
if (@file_exists($reboot_tmp)) {
	@unlink($reboot_tmp);
	exec("reboot",$str,$re);
	echo $re;
	exit;
}

//关机
$halt_tmp="/www/wdlinux/wdcp/data/tmp/halt.txt";
if (@file_exists($halt_tmp)) {
	@unlink($halt_tmp);
	exec("halt -p",$str,$re);
	echo $re;
	exit;
}

//清除日志
$syslogd_tmp="/www/wdlinux/wdcp/data/tmp/syslogd.txt";
if (@file_exists($syslogd_tmp)) {
	$str=@file_get_contents($syslogd_tmp);
	//echo $str;//exit;
	@unlink($syslogd_tmp);
	//echo "|".$str."|";
	if ($str=="0") {
		//echo "aa";
		$fd=opendir("/var/log");
		$msg="";
		while($buffer=@readdir($fd)) {
			//echo $buffer."\n";
			if ($buffer=="." or $buffer=="..") continue;
			$t="/var/log/".chop($buffer);
			if (@is_dir($t)) continue;
			//echo $t."\n";
			if (@filesize($t)==0) {
				//$msg.=$t."\n";
				@unlink($t);
			}
		}
		//echo $msg;
	}elseif (@file_exists($str)){
		//echo "|aaas".$str."|";
		@unlink($str);
	}else
		echo "err";
	echo 0;
	exit;
}

//iptables
$iptables_tmp="/www/wdlinux/wdcp/data/tmp/iptables.txt";
if (@file_exists($iptables_tmp)) {
	exec('/sbin/iptables-save | grep -E "tcp|udp"',$str,$re);
	@unlink($iptables_tmp);
	echo implode("\n",$str);
	exit;
}

//iptables add
$iptablesa_tmp="/www/wdlinux/wdcp/data/tmp/iptablesa.txt";
if (@file_exists($iptablesa_tmp)) {
	$str=@file_get_contents($iptablesa_tmp);
	exec("/sbin/iptables $str",$str,$re);
	@unlink($iptablesa_tmp);
	exit;
}

//iptables del
$iptablesd_tmp="/www/wdlinux/wdcp/data/tmp/iptablesd.txt";
if (@file_exists($iptablesd_tmp)) {
	$str=@file_get_contents($iptablesd_tmp);
	//echo $str."||||||||||";//exit;
	//exec("v1=`/sbin/iptables-save | awk '$str' | sed 's/-A /-D /'`;iptables \$v1",$str,$re);//print_r($str);print_r($re);
	exec("v1=`/sbin/iptables-save | awk '$str' | sed 's/-A /-D /' | head -1`;iptables \$v1",$str,$re);//print_r($str);print_r($re);
	//exec("v1=`/sbin/iptables-save | awk '$str' | sed 's/-A /-D /'`;for i in \$v1;do iptables \$i;done",$str,$re);//print_r($str);print_r($re);
	//exec("v1=`/sbin/iptables-save | awk '$str' | sed 's/-A /-D /'`;echo \$v1",$str,$re);print_r($str);print_r($re);
	//echo "iptables-save | awk '$str' | sed 's/-A /-D /'";
	//exec("iptables-save | awk '$str' | sed 's/-A /-D /'",$str,$re);//print_r($str);print_r($re);
	//echo implode("\n",$str);
	@unlink($iptablesd_tmp);
	exit;
}

//iptables save
$iptabless_tmp="/www/wdlinux/wdcp/data/tmp/iptabless.txt";
if (@file_exists($iptabless_tmp)) {
	if ($os_rl==2 and !@is_dir("/etc/sysconfig")) exec("mkdir -p /etc/sysconfig");
	$f="/etc/network/interfaces";
	if ($os_rl==2 and @file_exists($f)) {
		$s=@file_get_contents($f);
		if (!eregi("iptables",$s)) exec("echo 'pre-up iptables-restore /etc/sysconfig/iptables' >> /etc/network/interfaces");
	}
	exec("/sbin/iptables-save > /etc/sysconfig/iptables",$str,$re);
	@unlink($iptabless_tmp);
	exit;
}

//IP地址
$ifconfig_tmp="/www/wdlinux/wdcp/data/tmp/ifconfig.txt";
if (@file_exists($ifconfig_tmp)) {
	//echo "999";//exit;
	//echo @file_get_contents($ifconfig_tmp);
	//if (@filesize($ifconfig_tmp)==0){
	//echo "000";exit;
	exec("/sbin/ifconfig | grep -A1 eth",$str,$re);
	if ($re!=0)
		exec("/sbin/ifconfig | grep -A1 venet",$str,$re);
	@unlink($ifconfig_tmp);
	echo implode("\n",$str);
}

$ifconfiga_tmp="/www/wdlinux/wdcp/data/tmp/ifconfiga.txt";
if (@file_exists($ifconfiga_tmp)) {
	//$str="add|".$eth."|".$ip."|".$netmask."|".$save;
	//echo "aa";exit;
	//echo "act";
	$str=@file_get_contents($ifconfiga_tmp);
	//echo $str;//exit;
	$s1=explode("|",$str);
	//print_r($s1);
	if ($s1[0]=="add"){
		if (empty($s1[1]) or empty($s1[2]) or empty($s1[3])) exit;
		//echo "1111111111111111";
		exec("/sbin/ifconfig $s1[1] $s1[2] netmask $s1[3]",$str,$re);//print_r($str);print_r($re);
		if ($re==0 and ($s1[4]==1)) {
			save_ifconfig($s1[1],$s1[2],$s1[3]);
		}
	}elseif ($s1[0]=="stop") {
		//echo "ifconfig $s1[1] down";
		exec("/sbin/ifconfig $s1[1] down",$str,$re);
	}elseif ($s1[0]=="del") {
		exec("/sbin/ifconfig $s1[1] down",$str,$re);
		if ($os_rl==2) {
			 ub_del_ifconfig(chop($s1[1]));
		}else{
			$f="/etc/sysconfig/network-scripts/ifcfg-".chop($s1[1]);
			if (@file_exists($f)) @unlink($f);
		}
	}else;
	@unlink($ifconfiga_tmp);
	exit;
}

function ub_del_ifconfig($eth) {
	$f="/etc/network/interfaces";
	$s=@file_get_contents($f);
	$s1=preg_replace("/###s_$eth(.*)e_$eth/isU","",$s);
	@file_put_contents($f,$s1);
	return;
}

function save_ifconfig($eth,$ip,$netmask) {
	global $os_rl;
	if ($os_rl==2) {
		$f="/etc/network/interfaces";
		$s1=@file_get_contents($f);
		$s1.="\n###s_$eth\n";
		$s1.="auto $eth\n";
		$s1.="iface $eth inet static\n";
		$s1.="address $ip\n";
		$s1.="netmask $netmask\n";
		$s1.="###e_$eth\n";
		@file_put_contents($f,$s1);
	}else {
	$f="/etc/sysconfig/network-scripts/ifcfg-$eth";
	if (@file_exists($f)) exit;
		$msg='###wdcp save config
DEVICE='.$eth.'
BOOTPROTO=static
IPADDR='.$ip.'
NETMASK='.$netmask.'
ONBOOT=yes';
	@file_put_contents($f,$msg);
	}
}

$gateway_tmp="/www/wdlinux/wdcp/data/tmp/gateway.txt";
if (@file_exists($gateway_tmp)) {
	exec("netstat -nr | grep UG | awk -F' ' '{print $2}'",$str,$re);
	echo implode("\n",$str);
	@unlink($gateway_tmp);
	exit;
}

$task_tmp="/www/wdlinux/wdcp/data/tmp/task.txt";
if (@file_exists($task_tmp)) {
	//echo "11";
	$q=$db->query("select * from wd_task where state=0");
	$msg="";
	@unlink($task_tmp);
	$i=0;
	while ($r=$db->fetch_array($q)) {
		//$f="/www/wdlinux/wdcp/task/wdcp_".$r['file'].".php";
		$f=$r['file'];
		$id=$r['id'];
		//echo $f."\n";
		if (@file_exists($f)) {
			if (substr($f,-4)==".php")
				$msg.="$r[d1] $r[d2] $r[d3] $r[d4] $r[d5] /www/wdlinux/wdphp/bin/php $f\n";
			else
				$msg.="$r[d1] $r[d2] $r[d3] $r[d4] $r[d5] /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_task.php $id\n";
			/*
			if ($r['d1']=="0") $d1="*";
			else $d1="$r[d1]";
			//$d1="$r[d1]";
			//echo $r['d1']."|".$d1."\n";
			if ($r['d2']=="0") $d2="*";
			else $d2=$r['d2'];
			if ($r['d3']=="0") $d3="*";
			else $d3=$r['d3'];
			if ($r['d4']=="0") $d4="*";
			else $d4=$r['d4'];
			if ($r['d5']=="0") $d5="*";
			else $d1=$r['d5'];
			$msg.="$d1 $d2 $d3 $d4 $d5 /www/wdlinux/wdphp/bin/php $f\n";
			//$msg.="$r[d1] $r[d2] $r[d3] $r[d5] $r[d5] /www/wdlinux/wdphp/bin/php $f\n";
			*/
			$i++;
		}

	}
	//echo $msg;exit;
	//if ($i>0) {
	@file_put_contents("/var/spool/cron/root",$msg);
	exec("chmod 600 /var/spool/cron/root",$str,$re);
	//}
	exit;
}

//web切换
$web_eng_tmp="/www/wdlinux/wdcp/data/tmp/web_etmp.txt";
if (@file_exists($web_eng_tmp)) {
	$str=chop(@file_get_contents($web_eng_tmp));
	@unlink($web_eng_tmp);
	$re=env_to($str);
	echo $re;
	exit;
}

//目录身份验证
$htp_tmp="/www/wdlinux/wdcp/data/tmp/htp.txt";
if (@file_exists($htp_tmp)) {
	$str=chop(@file_get_contents($htp_tmp));
	@unlink($htp_tmp);
	if (!@is_dir($htpasswd_dir)) exec("mkdir -p $htpasswd_dir",$str11,$re);
	$s1=explode("|",$str);
	$htf=$htpasswd_dir."/".$s1[0]."_".$s1[1].".txt";
	if ($s1[4]=="add" or !@file_exists($htf)) {
		//$htf=$htpasswd_dir."/".$s1[0].".txt";
		if (!@file_exists($htf)) {
			exec("/www/wdlinux/wdapache/bin/htpasswd -cb $htf $s1[2] $s1[3]",$str12,$re);
		}else{
			exec("/www/wdlinux/wdapache/bin/htpasswd -b $htf $s1[2] $s1[3]",$str12,$re);
		}
	}elseif ($s1[4]=="edit" and @file_exists($htf)) {
		exec("/www/wdlinux/wdapache/bin/htpasswd -b $htf $s1[2] $s1[3]",$str12,$re);
	}elseif ($s1[4]=="del" and @file_exists($htf)) {
		exec("/www/wdlinux/wdapache/bin/htpasswd -D $htf $s1[2]",$str12,$re);
	}elseif ($s1[4]=="df" and @file_exists($htf)) {
		exec("rm -f $htf",$str12,$re);
	}else;
	echo $re;
	exit;
}


//web_logs
$web_logs_tmp="/www/wdlinux/wdcp/data/tmp/web_logs.txt";
if (@file_exists($web_logs_tmp)) {
	$tf="/etc/logrotate.d/web_logs";
	$msg="$web_logs_home/*log {\n";
    $msg.="	rotate $web_logs_day\n";
	@unlink($web_logs_tmp);
	if (!@is_dir($web_logs_home)) exec("mkdir -p $web_logs_home");
	if ($web_logs_logrotate==0 and @file_exists($tf)) @unlink($tf);
	if ($web_logs_logrotate!=1) exit;
	if ($web_logs_gz==1)
		$msg.="	compress\n";
	$msg.="	daily\n";
	$msg.="	missingok\n";
	$msg.="	notifempty\n";
	$msg.="	dateext\n";
	$msg.="	postrotate\n";
	if ($web_eng==1){
		$msg.="	$service_cmd httpd reload > /dev/null 2>&1 || true\n";
		//$msg.="	/www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_weblog.php > /dev/null 2>&1 || true\n";
	}elseif ($web_eng==2){
		$msg.="	$service_cmd nginxd reload > /dev/null 2>&1 || true\n";
		//$msg.="	/www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_weblog.php > /dev/null 2>&1 || true\n";
	}elseif ($web_eng==3) {
		$msg.="	$service_cmd httpd reload > /dev/null 2>&1 || true\n";
		$msg.="	$service_cmd nginxd reload > /dev/null 2>&1 || true\n";
		//$msg.="	/www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_weblog.php > /dev/null 2>&1 || true\n";
	}else 
		exit;
	if ($site_logs_is==1)
		$msg.="	/www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_weblog.php > /dev/null 2>&1 || true\n";
	$msg.="	endscript\n";
	$msg.="}\n";
	@file_put_contents($tf,$msg);
	exit;
}

//同步时间
$ntp_tmp="/www/wdlinux/wdcp/data/tmp/ntp.txt";
if (@file_exists($ntp_tmp)) {
	if (@file_exists("/www/wdlinux/tools/wdcp_ntp.sh")){
		//echo "11";
		exec("sh /www/wdlinux/tools/wdcp_ntp.sh",$str,$re);
		@unlink($ntp_tmp);
	}else{
		//echo "22";
		if (@file_exists("/sbin/ntpdate") or @file_exists("/usr/sbin/ntpdate"))
			exec("yum install -y ntp &");
		exec("ln -sf /usr/share/zoneinfo/Asia/Shanghai /etc/localtime;ntpdate tiger.sina.com.cn;hwclock -w");
	}
	exit;
}

//
$wls_tmp="/www/wdlinux/wdcp/data/tmp/wls.txt";
if (@file_exists($wls_tmp)) {
	$str=chop(@file_get_contents($wls_tmp));
	@unlink($wls_tmp);
	if ($str=="on")
		exec("/www/wdlinux/tools/web_logs.sh on",$str,$re);
	else
		exec("/www/wdlinux/tools/web_logs.sh off",$str,$re);
	exit;
}


//backup
$backup_tmp="/www/wdlinux/wdcp/data/tmp/backup.txt";
if (@file_exists($backup_tmp)) {
	$str=@file_get_contents($backup_tmp);
	@unlink($backup_tmp);
	$s1=explode("|",$str);
	$tdir=$backup_home."/".chop($s1[2]);
	if (!@is_dir($tdir)) @system("mkdir -p $tdir");
	if (@is_dir($s1[0]) and @file_exists($s1[0]."/".$s1[1])) {
		$f=$tdir."/".$s1[1]."_".date("YmdHi").".tar.gz";
		exec("cd $s1[0];tar zcvf $f $s1[1] &",$str,$re);
	}
	exit;
}

//wdapache port
$a_port_tmp="/www/wdlinux/wdcp/data/tmp/a_port.txt";
$wdapache_conf="/www/wdlinux/wdapache/conf/httpd.conf";
if (@file_exists($a_port_tmp)) {
	$str=@file_get_contents($a_port_tmp);
	//echo $str;
	$s1=explode("|",$str);
	//echo $s1[0]."|".$s1[1];
	$msg=@file_get_contents($wdapache_conf);
	$msg=preg_replace("/^Listen $s1[0]$/imU","Listen $s1[1]",$msg,1);
	$msg=preg_replace("/^\<VirtualHost \*:$s1[0]\>/imU","<VirtualHost *:$s1[1]>",$msg,1);//echo $msg;exit;
	@file_put_contents($wdapache_conf,$msg);
	@unlink($a_port_tmp);
	//if ($os_rl==2)
		//$s2=@file_get_contents("/etc/iptables.rules");
	//else
		$s2=@file_get_contents("/etc/sysconfig/iptables");
	//if ($s1[1]!=8080 and !eregi("--dport $s1[1]",$s2)) {
	if (!eregi("--dport $s1[1]",$s2)) {
		//echo "iptables";
		exec("/sbin/iptables -I INPUT -p tcp --dport $s1[1] -j ACCEPT",$str,$re);
		//exec("/etc/init.d/iptables save",$str,$re);
		//if ($os_rl==2) {
			if (!@is_dir("/etc/sysconfig")) exec("mkdir -p /etc/sysconfig");
			exec("/sbin/iptables-save > /etc/sysconfig/iptables");
		//}else
			//exec("/etc/init.d/iptables save",$str,$re);
	}
	exec("$service_cmd wdapache reload",$str,$re);
	exit;
}

//ftp port
$f_port_tmp="/www/wdlinux/wdcp/data/tmp/f_port.txt";
$pureftp_conf="/www/wdlinux/etc/pure-ftpd.conf";
if (@file_exists($f_port_tmp)) {
	$str=@file_get_contents($f_port_tmp);
	//echo $str;
	$s1=explode("|",$str);//echo $str;
	//echo $s1[0]."|".$s1[1];
	$msg=@file_get_contents($pureftp_conf);
	if (!eregi("Bind",$msg)) {
		$msg.="Bind                    $s1[1]";
	}else{
		$msg=preg_replace("/^Bind(.*)$s1[0]$/imU","Bind                    $s1[1]",$msg,1);
		//$msg=preg_replace("/^\<VirtualHost \*:$s1[0]\>/imU","<VirtualHost *:$s1[1]>",$msg,1);//echo $msg;exit;
	}
	@file_put_contents($pureftp_conf,$msg);
	@unlink($f_port_tmp);
	//if ($os_rl==2)
		//$s2=@file_get_contents("/etc/iptables.rules");
	//else
		$s2=@file_get_contents("/etc/sysconfig/iptables");
	//if ($s1[1]!=8080 and !eregi("--dport $s1[1]",$s2)) {
	if (!eregi("--dport $s1[1]",$s2)) {
		//echo "iptables";
		exec("/sbin/iptables -I INPUT -p tcp --dport $s1[1] -j ACCEPT",$str,$re);
		//exec("/etc/rc.d/init.d/iptables save",$str,$re);
		//if ($os_rl==2){
			if (!@is_dir("/etc/sysconfig")) exec("mkdir -p /etc/sysconfig");
			exec("/sbin/iptables-save > /etc/sysconfig/iptables");
		//}else
			//exec("/etc/init.d/iptables save",$str,$re);
	}
	exec("$service_cmd pureftpd restart",$str,$re);
	exit;
}

//ysin
$ysin_tmp="/www/wdlinux/wdcp/data/tmp/ysin.txt";
if (@file_exists($ysin_tmp)) {
	$str=@file_get_contents($ysin_tmp);
	if ($str=="ys_in") {
		$str=@file_get_contents("http://www.wdlinux.cn/conf/ysin.txt");
		@file_put_contents("/tmp/ysin.sh",$str);
		exec("cd /tmp;chmod 755 ysin.sh;./ysin.sh &",$s,$re);
	}
	@unlink($ysin_tmp);
exit;
}

?>