<?php
/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/

require_once "../inc/common.inc.php";
require_once "../login.php";
require_once "../inc/admlogin.php";
//if ($wdcp_gid!=1) exit;
$adminid=$wdcp_gid;

if (empty($_SERVER['QUERY_STRING'])) $_SESSION['site_dir']="";//

if (isset($_GET['act']) and $_GET['act']=="us") {
	$sid=intval($_GET['sid']);
	//echo $sid;
	$q=$db->query("select * from wd_site where uid='$wdcp_uid' and id='$sid'");
	if ($db->num_rows($q)==0) go_back("站点错误");
	$r=$db->fetch_array($q);
	$vdir=$r['vhost_dir'];
	$_SESSION['site_dir']=$vdir;
}

$site_select='<a href="'.$PHP_SELF.'?act=chg&site_dir=chg">选择站点</a>';
$site_list="";
if ($wdcp_gid!=1 and (empty($_SESSION['site_dir']) or $_GET['site_dir']=="chg")) {
	$q=$db->query("select * from wd_site where uid='$wdcp_uid'");
	if ($db->num_rows($q)>0){
		$site_list="选择站点:<br><br>";
		while ($r=$db->fetch_array($q)) {
			$site_list.='域名'.$r['domain'].'&nbsp;&nbsp;&nbsp;目录'.$r['vhost_dir'].' <a href="'.$PHP_SELF.'?act=us&sid='.$r['id'].'">管理</a><br>';
		}
	}else
		$site_list="没有站点可以管理";
}
//print_r($_SESSION['site_dir']);

//if (!isset($_GET['p'])) $cu_dir=getcwd();
if ($wdcp_gid==1){ 
	if (!isset($_GET['p'])) 
		//$cu_dir="/www/web/default";
		$cu_dir="/www/web";//
	else
		$cu_dir=chop($_GET['p']);
	if (eregi("wdcp",$cu_dir)) $cu_dir="/www/web";//$cu_dir="/www/web/default";
	$site_select="";
	//$home_dir="/www/web/default";
	$home_dir="/www/web";
}else{
	if (!isset($_GET['p']))
		$cu_dir=$_SESSION['site_dir'];
	else{
		$cu_dir=chop($_GET['p']);
		$site_dir_len=strlen($_SESSION['site_dir']);
		if (strcmp(substr($cu_dir,0,$site_dir_len),$_SESSION['site_dir'])!=0) go_back("无权操作");
	}
	$home_dir=$_SESSION['site_dir'];
	if (eregi("wdcp",$cu_dir)) $cu_dir=$_SESSION['site_dir'];
}

$s1=explode("/",$cu_dir);
$cu_file=end($s1);
$s2=strlen($cu_file);
$pre_dir=substr($cu_dir,0,strlen($cu_dir)-($s2+1));
$is_trash_dir=0;
//echo substr($cu_dir,0,11);
if (substr($cu_dir,0,10)==="/www/trash") $is_trash_dir=1;
if (isset($_GET['act']) and $_GET['act']=="get_file") $get_file=1;
else $get_file=0;
if (isset($_GET['act']) and $_GET['act']=="create_dir") $create_dir=1;
else $create_dir=0;
if (isset($_GET['act']) and $_GET['act']=="create_file") $create_file=1;
else $create_file=0;
if (isset($_GET['act']) and $_GET['act']=="upload_file") $upload_file=1;
else $upload_file=0;

if (isset($_GET['act']) and $_GET['act']=="down") {
	$f=chop($_GET['f']);
	$p=chop($_GET['p']);
	$fp=$p."/".$f;
	if (!@file_exists($fp)) js_close("文件不存在!");//go_back("文件不存在!");
	if (!@is_readable($fp)) js_close("没有权限!");
	$file = fopen($fp,"r"); // 打开文件
	// 输入文件标签
	Header("Content-type: application/octet-stream");
	Header("Accept-Ranges: bytes");
	Header("Accept-Length: ".filesize($fp));
	Header("Content-Disposition: attachment; filename=".$f);
	// 输出文件内容
	echo @fread($file,@filesize($fp));
	@fclose($file);
	exit();
}


if (isset($_POST['Submit_gf'])) {
	wdl_demo_sys();
	$durl=chop($_POST['get_url']);
	if (empty($durl)) go_back("网址不能为空!");
	if (strcmp(substr($durl,0,7),"http://")!=0) go_back("网址格式错误");//
	$durl_tmp=WD_ROOT."/data/tmp/durl.txt";
	$msg=$cu_dir."|".$durl;
	//echo $msg;
	@file_put_contents($durl_tmp,$msg);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);
	if (@file_exists($durl_tmp)) @unlink($durl_tmp);
	optlog($wdcp_uid,"下载文件 $durl",0,0);//	
	if ($re==0)
		str_go_url("文件已在后台下载中!",1);
	else
		str_go_url("下载失败!",1);
}

if (isset($_POST['Submit_cdir'])) {
	wdl_demo_sys();
	$dirname=chop($_POST['dirname']);
	if (empty($dirname)) go_back("目录名不能为空");
	if (eregi("/",$dirname)) go_back("不支持多级目录");
	if (@is_dir($cu_dir."/".$dirname)) go_back("目录已存在");
	$cdir_tmp=WD_ROOT."/data/tmp/cdir.txt";
	$msg=$cu_dir."|".$dirname;
	@file_put_contents($cdir_tmp,$msg);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);
	if (@file_exists($cdir_tmp)) @unlink($cdir_tmp);
	optlog($wdcp_uid,"创建目录 $dirname",0,0);//	
	if ($re==0)
		str_go_url("目录创建成功!",1);
	else
		str_go_url("创建失败!",1);	
}

if (isset($_POST['Submit_cfile'])) {
	wdl_demo_sys();
	$filename=chop($_POST['filename']);
	if (empty($filename)) go_back("文件名不能为空");
	if (@is_file($cu_dir."/".$filename)) go_back("文件已存在");
	$cfile_tmp=WD_ROOT."/data/tmp/cfile.txt";
	$msg=$cu_dir."|".$filename;
	@file_put_contents($cfile_tmp,$msg);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);
	if (@file_exists($cfile_tmp)) @unlink($cfile_tmp);
	optlog($wdcp_uid,"创建文件 $filename",0,0);//	
	if ($re==0)
		str_go_url("文件创建成功!",1);
	else
		str_go_url("文件失败!",1);	
}

if (isset($_POST['Submit_upfile'])) {
	//print_r($_FILES['upfile']['name']);exit;
	wdl_demo_sys();
	$up_dir="/www/wdlinux/wdcp/data/tmp/";
	$s=0;
	for ($i=0;$i<sizeof($_FILES['upfile']['name']);$i++) {
		//echo $_FILES['upfile']['tmp_name'][$i]."|".$_FILES['upfile']['name'][$i]."<br>";
		if (empty($_FILES['upfile']['name'][$i])) continue;
		//if (eregi("\ ",$_FILES['upfile']['name'])) echo "aa";exit;
		@move_uploaded_file($_FILES['upfile']['tmp_name'][$i],str_replace(" ","_",$up_dir.$_FILES['upfile']['name'][$i]));
		$flist.=str_replace(" ","_",$_FILES['upfile']['name'][$i])." ";
		$s++;
	}
	//echo $flist;exit;
	$ufile_tmp=WD_ROOT."/data/tmp/ufile.txt";
	$msg=$cu_dir."|".$flist;
	@file_put_contents($ufile_tmp,$msg);
	//echo $msg;
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);
	if (@file_exists($ufile_tmp)) @unlink($ufile_tmp);
	optlog($wdcp_uid,"上传 $s 个文件",0,0);//	
	if ($re==0)
		str_go_url("成功上传 $s 个文件!",1);
	else
		str_go_url("上传失败!",1);//
}

if (isset($_POST['Submit_act'])) {
	//$num=@array_keys($_POST['num']);
	//if (sizeof($num)==0) go_back("请选择文件!");
	$num=$_POST['num'];//print_r($num);
	if (empty($num)) go_back("请选择文件或目录");//
	$act_more=isset($_POST['act_more'])?1:0;//echo $act_more;go_back("");
	$act=chop($_POST['act']);
	if ($act===0) go_back("请选择操作:打包,删除,修改等!");
	$act_name=chop($_POST['act_name']);
	$flist="";
	for ($i=0;$i<sizeof($num);$i++) {
		//echo $num[$i]."<br>";
		$flist.=$num[$i]." ";
	}
	//echo "|".$flist."|<br>";exit;
	if ($act=="tar") {
		wdl_demo_sys();
		//echo "tar";
		/*
		if (empty($act_name)) {
			exec("sudo wd_app tar '$cu_dir' no '$flist'",$str,$re);
			optlog($wdcp_uid,"打包文件 $flist",0,0);//
			check_re($re,1,"错误!/已打包");
		}else{
			exec("sudo wd_app tar '$cu_dir' '$act_name' '$flist'",$str,$re);
			optlog($wdcp_uid,"打包文件 $flist",0,0);//
			check_re($re,1,"错误!/已打包");
		}
		*/
		$tar_tmp=WD_ROOT."/data/tmp/tar.txt";
		if (empty($act_name))
			$msg="$cu_dir|no|".chop($flist);
		else
			$msg="$cu_dir|$act_name|".chop($flist);
		@file_put_contents($tar_tmp,$msg);
		exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);
		if (@file_exists($tar_tmp)) @unlink($tar_tmp);
		optlog($wdcp_uid,"打包文件 $flist",0,0);//
		check_re($re,1,"错误!/已打包");
	}elseif ($act=="del") {
		//demo
		wdl_demo_sys();
		
		//exec("sudo wd_app del '$cu_dir' '$flist'",$str,$re);//print_r($str);print_r($re);exit;
		$del_tmp=WD_ROOT."/data/tmp/del.txt";
		$msg="$cu_dir|".chop($flist);
		@file_put_contents($del_tmp,$msg);
		exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);	
		if (@file_exists($del_tmp)) @unlink($del_tmp);
		optlog($wdcp_uid,"删除文件 $flist",0,0);//
		check_re($re,1,"错误!/删除成功");
	}elseif ($act=="move") {
		wdl_demo_sys();
		if (empty($act_name)) go_back("请输入要移动到的目录!");
		//echo "move";
		$move_tmp=WD_ROOT."/data/tmp/move.txt";
		$msg="$cu_dir|$act_name|".chop($flist);
		//echo $msg;
		@file_put_contents($move_tmp,$msg);
		exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);	
		if (@file_exists($move_tmp)) @unlink($move_tmp);
		optlog($wdcp_uid,"移动文件 $flist ",0,0);//
		check_re($re,1,"错误!/移动成功");		
	}elseif ($act=="copy") {
		wdl_demo_sys();
		if (empty($act_name)) go_back("请输入要复制到的目录!");
		//echo "move";
		$copy_tmp=WD_ROOT."/data/tmp/copy.txt";
		$msg="$cu_dir|$act_name|".chop($flist);
		//echo $msg;
		@file_put_contents($copy_tmp,$msg);
		exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);	
		if (@file_exists($copy_tmp)) @unlink($copy_tmp);
		optlog($wdcp_uid,"复制文件 $flist ",0,0);//
		check_re($re,1,"错误!/复制成功");	
	}elseif ($act=="perm") {
		//echo "perm";
		//demo
		wdl_demo_sys();
		if (empty($act_name)) go_back("请输入相应的权限,如777,755");
		if (!is_numeric($act_name)) go_back("输入有错,请输入如777,755");
		//
		//for ($i=0;$i<strlen($act_name);$i++) {
		for ($i=0;$i<3;$i++) {
			$perm_num=array("4","2","1","5","6","7");
			if (!in_array($act_name[$i],$perm_num)) go_back("输入有错,请输入如777,755");
			//echo $act_name[$i];
			//exec("sudo wd_app perm '$cu_dir' '$act_more' '$act_name' '$flist'",$str,$re);
			//optlog($wdcp_uid,"修改文件权限 $flist",0,0);//
			//check_re($re,1,"错误!/设置权限成功!");
		}
		$perm_tmp=WD_ROOT."/data/tmp/perm.txt";
		$msg="$act_name|$act_more|$cu_dir|".chop($flist);
		@file_put_contents($perm_tmp,$msg);
		exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);
		if (@file_exists($perm_tmp)) @unlink($perm_tmp);
		optlog($wdcp_uid,"修改文件权限 $flist",0,0);//
		check_re($re,1,"错误!/设置权限成功!");
	}elseif ($act=="ower") {
		//demo
		wdl_demo_sys();
		//echo "user";
		if (empty($act_name)) go_back("请输入用户名或用户ID");
		//exec("sudo wd_app ower '$cu_dir' '$act_more' '$act_name' '$flist'",$str,$re);
		$ower_tmp=WD_ROOT."/data/tmp/ower.txt";
		$msg="$act_name|$act_more|$cu_dir|".chop($flist);
		@file_put_contents($ower_tmp,$msg);
		exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);
		if (@file_exists($ower_tmp)) @unlink($ower_tmp);
		optlog($wdcp_uid,"修改文件用户 $flist",0,0);//
		if ($re==12) go_back("该用户不存在!");
		check_re($re,1,"错误!/设置所有者成功!");
	}elseif ($act=="group") {
		//demo
		wdl_demo_sys();

		//echo "group";
		if (empty($act_name)) go_back("请输入组名或组ID");
		//exec("sudo wd_app owerg '$cu_dir' '$act_more' '$act_name' '$flist'",$str,$re);
		$group_tmp=WD_ROOT."/data/tmp/group.txt";
		$msg="$act_name|$act_more|$cu_dir|".chop($flist);
		@file_put_contents($group_tmp,$msg);
		exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);
		if (@file_exists($group_tmp)) @unlink($group_tmp);
		optlog($wdcp_uid,"修改文件属组 $flist",0,0);//
		if ($re==12) go_back("该用户组不存在!");
		check_re($re,1,"错误!/设置所有组成功!");
	}else
		go_back("没选择要做什么操作");
	//echo "OK";
}

if (isset($_POST['Submit_edit'])) {
	//demo
	wdl_demo_sys();
		
	$tn=time();
	$tmpdir=WD_ROOT."/data/".$tn;
	$fn=chop($_POST['fn']);
	
	//检查文件,限制编辑部分文件
	if ($wdcp_uid!=1 and ereg("rc\.d|init\.d",$fn)) go_back("该文件限制修改操作");
	
	$content=stripslashes(chop($_POST['contents']));
	@file_put_contents($tmpdir,$content);
	//echo "|".$tmpdir."|<br>";
	//echo "|".$fn."|<br>";
	//exec("sudo wd_app cp '$tmpdir' '$fn'",$str,$re);
	//exec("sudo wd_app test",$str,$re);
	//unlink($tmpdir);
	//print_r($str);print_r($re);exit;
	$cp_tmp=WD_ROOT."/data/tmp/cp.txt";
	$msg="$tmpdir|$fn";
	@file_put_contents($cp_tmp,$msg);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);
	if (@file_exists($cp_tmp)) @unlink($cp_tmp);
	optlog($wdcp_uid,"修改文件 $fn",0,0);//
	if ($re==0) 
		str_go_url("已修改更新!","filem.php?p=".$pre_dir);
	else
		go_back("修改有误!");
	exit;
	//echo $tmpdir."<br>";
	//echo $fn."<br>";
}

if (isset($_GET['act']) and $_GET['act']=="del") {
	//demo
	wdl_demo_sys();
		
	if ($is_trash_dir==1) go_back("回收站内容不能在此删除!");
	$t=chop($_GET['t']);
	$f=chop($_GET['p']);
	//echo $t."|".$f."<br>";exit;
	//exec("sudo wd_app del '$pre_dir' '$f'",$str,$re);//print_r($str);print_r($re);exit;

	$del_tmp=WD_ROOT."/data/tmp/del.txt";
	$msg="$pre_dir|$f";
	@file_put_contents($del_tmp,$msg);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);	
	if (@file_exists($del_tmp)) @unlink($del_tmp);
	optlog($wdcp_uid,"删除文件 $f",0,0);//
	check_re($re,1,"错误!/已删除");
}

if (isset($_GET['act']) and  chop($_GET['act'])=="tar") {
	if (!@file_exists($cu_dir)) go_back("文件不存在!");
	$t1=explode(".",$cu_file);
	$t2=end($t1);
	//echo "tara xvf '$pre_dir' '$cu_file'";
	//optlog($wdcp_uid,"解压文件 $cu_file",0,0);//
	/*
	if ($t2=="tar") {
		exec("sudo wd_app tara xvf '$pre_dir' '$cu_file'",$str,$re);//print_r($str);print_r($re);exit;
		check_re($re,1,"错误!/解压完成!");
	}elseif ($t2=="gz" or $t2=="tgz"){
		exec("sudo wd_app tara zxvf '$pre_dir' '$cu_file'",$str,$re);//print_r($str);print_r($re);exit;
		check_re($re,1,"错误!/解压完成");
	}elseif ($t2=="bz2"){
		exec("sudo wd_app tara jxvf '$pre_dir' '$cu_file'",$str,$re);//print_r($str);print_r($re);exit;
		check_re($re,1,"错误!/解压完成");
	}elseif ($t2=="zip") {
		exec("sudo wd_app zip '$pre_dir' '$cu_file'",$str,$re);
		check_re($re,1,"错误!/解压完成");
	}else{
		go_back("文件类型错误!");
		exit;
	}
	*/
	$untar_tmp=WD_ROOT."/data/tmp/untar.txt";
	$msg="$pre_dir|$cu_file";
	@file_put_contents($untar_tmp,$msg);
	exec("sudo /www/wdlinux/wdphp/bin/php /www/wdlinux/wdcp/task/wdcp_sr.php",$str,$re);//print_r($str);print_r($re);
	if (@file_exists($untar_tmp)) @unlink($untar_tmp);
	optlog($wdcp_uid,"解压文件 $cu_file",0,0);//
	check_re($re,1,"错误!/解压完成");
}



if (isset($_GET['act']) and ($_GET['act']=="edit") and $_GET['t']=="f") {
	//echo $cu_dir;
	if (!@file_exists($cu_dir)) go_back("文件不存在!");
	//if (!is_readable($cu_dir)) go_back("文件不可读!");
	//exec("sudo wd_app mab '$cu_dir'",$str,$re);
	//echo $cu_dir;print_r($str);print_r($re);exit;
	//if ($re==0) go_back("二进制文件不可修改!");
	
	if (@is_executable($cu_dir)) {//go_back("执行文件不可编辑");
	//判断二进制文件
		$allow_code=array("3533","8372","3510","3532");
		$fp = @fopen($cu_dir, "rb");
		$bin = @fread($fp,2);
		@fclose($fp);
		$str_info  = @unpack("C2chars", $bin);
		$type_code = intval($str_info['chars1'].$str_info['chars2']);
		//echo $type_code;//exit;
		if ($type_code==12769 or $type_code==0) go_back("二进制文件,不可以编辑");
	}
	
	
	$str=@file_get_contents($cu_dir);
	//preg_match("/charset=(.*)('|\") /isU",$str,$s1);
	preg_match("/charset=(.*)('|\"| )/isU",$str,$s1);
	//print_r($s1);
	if (empty($s1[1])) {
		$charset="gb2312";
		$title="文件修改/编辑";
		$cu_title="当前文件";
		$bu_save="保存";
		$bu_reset="重载";
		$bu_return="返回";
	}else{
		$charset=$s1[1];
		//echo $charset;
		$title=mb_convert_encoding("文件修改/编辑","$charset","GBK");
		$cu_title=mb_convert_encoding("当前文件","$charset","GBK");
		$bu_save=mb_convert_encoding("保存","$charset","GBK");
		$bu_reset=mb_convert_encoding("重载","$charset","GBK");
		$bu_return=mb_convert_encoding("返回","$charset","GBK");
	}
	//echo $title;
	require_once(G_T("sys/filem_edit.htm"));
	exit;
}

//if (!shell_is_dir($cu_dir)) $cu_dir=getcwd();
//if (!is_dir($cu_dir)) $cu_dir=getcwd();
if (!@is_dir($cu_dir)) $cu_dir="/www/web/default";
if (eregi("wdcp",$cu_dir)) $cu_dir="/www/web/default";
if (empty($pre_dir)) $pre_dir="/";


/*
echo get_cfg_var("open_basedir");
function open_dir($dir) {
	if (get_cfg_var("open_basedir")==="") {
		echo "11";
		return php_open_dir($dir);}
	else {
		echo "22";
		return shell_open_dir($dir);}
}
*/
//define(open_dir,php_open_dir);
//define(open_dir,shell_open_dir);
//$str=shell_open_dir($cu_dir);
//print_r($str);
//exit;
//文件名 类型 拥有者 权限 时间 大小
//echo gmdate("Y-m-d H:i",$ctime);echo "<br>";
//echo $cu_dir."<br>";
//echo $pre_dir."<br>";
//$predir=
//echo getcwd()."\\..";
//echo '<a href="'.$PHP_SELF.'?p='.$pre_dir.'">上一层目录</a><br>';


$str=php_open_dir($cu_dir);//print_r($str);
$list=array();
for ($i=0;$i<sizeof($str);$i++) {
	//echo $str[$i]."<br>";
	$s1=explode("|",$str[$i]);
	$s11=$cu_dir."/".$s1[0];
	if ($cu_dir=="/") $s11="/".$s1[0];
	//if (shell_is_dir($s11)) {
	if (is_dir($s11)) {
		$a1='<a href="'.$PHP_SELF.'?p='.$s11.'&act=list&t=d"><font color="#0000FF">'.$s1[0].'</font></a>';
		$mlink='<a href="'.$PHP_SELF.'?p='.$s11.'&act=list&t=d">进</a>';
		$dlink=$PHP_SELF.'?p='.$s11.'&act=del&t=d';
	}else{
		$a1='<a href="'.$PHP_SELF.'?p='.$s11.'&act=edit&t=f">'.$s1[0].'</a>';
		$mlink='<a href="'.$PHP_SELF.'?p='.$s11.'&act=edit&t=f">编</a>';
		$m11=explode(".",$s1[0]);
		$m12=".".end($m11);
		if (eregi(".gz|.gif|.bin|.jpg|.bmp|.zip",$m12)) {
			$a1=$s1[0];
			$mlink="";
		}
		$dlink=$PHP_SELF.'?p='.$s11.'&act=del&t=f';
	}
	if ($s1[1]=="file") {
		$a2="文件";
		$down='<a href="'.$PHP_SELF.'?act=down&f='.$s1[0].'&p='.$cu_dir.'" target=_blank>下载</a>';	
	}else{
		$a2="目录";
		$down="";
	}
	//tar1=
	if (substr($s1[0],-3)==".gz" or substr($s1[0],-4)==".tgz" or substr($s1[0],-4)==".tar" or substr($s1[0],-4)==".zip")
		$tar='<a href="'.$PHP_SELF.'?p='.$s11.'&act=tar&t=f">解压</a>';
	else
		$tar="";
	
	$list[$i]['id']=$i;
	$list[$i]['a1']=$a1;
	$list[$i]['a2']=$a2;
	$list[$i]['s10']=$s1[0];
	$list[$i]['s12']=$s1[2];
	$list[$i]['s13']=$s1[3];
	$list[$i]['s14']=$s1[4];
	$list[$i]['s15']=$s1[5];
	$list[$i]['s16']=$s1[6];
	$list[$i]['mlink']=$mlink;
	$list[$i]['dlink']=$dlink;
	$list[$i]['tar']=$tar;
	$list[$i]['down']=$down;
}
//array_multisort($isdir,SORT_DESC,$time,SORT_DESC,$fileArr);  
//array_multisort($list[]['a1'],SORT_DESC,$list);
require_once(G_T("sys/filem.htm"));

//G_T_F("footer.htm");
footer_info();
?>