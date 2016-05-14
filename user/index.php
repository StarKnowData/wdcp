<?
exit;//
require_once "../data/db.inc.php";
require_once "../inc/fun.inc.php";
require_once "../inc/db_mysql.class.php";
$db = new dbstuff;
$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect, true, $dbcharset);
$mykey=wdl_encrypt_key();

if (isset($_POST['Submit_login'])) {
	$username=addslashes(chop($_POST['username']));
	$password=md5(chop($_POST['password']));
	$q=$db->query("select * from wd_host where ftpuser='$username' and ftppasswd='$password'");
	//echo "select * from wd_host where ftpuser='$username' and ftppasswd='$password'";
	//echo "11";
	//echo $db->num_rows($q);
	if ($db->num_rows($q)==1) {
		setcookie('username',"$username");
		//echo $_COOKIE['username']."|22";exit;
		/* echo '<script language="javascript">location.href="user.php"</script>';*/
		go_url(0);
		
	}else {
		//echo $_COOKIE['username']."|33";
		/* echo '<script language="javascript">alert("用户名或密码错误!");location.href="user.php"</script>'; */
		go_back("用户或密码错误");
	}
} 


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>wdcp系统用户管理面板</title>
<style type="text/css">
<!--
.STYLE1 {color: #CC0000}
-->
</style>
</head>

<body>
<?
if (!isset($_COOKIE['username'])) {
?>
<br><Br><Br>
<form id="form1" name="form1" method="post" action="">
  <table width="403" height="153" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#EFEFEF">
    <tr>
      <td height="42" colspan="2"><div align="center"><strong>wdcp系统用户登录</strong></div></td>
    </tr>
    <tr>
      <td width="151" height="31"><div align="center">FTP用户名:</div></td>
      <td width="252"><label>
        <input name="username" type="text" id="username" />
      </label></td>
    </tr>
    <tr>
      <td height="42"><div align="center">密码:</div></td>
      <td><label>
        <input name="password" type="password" id="password" />
      </label></td>
    </tr>
    <tr>
      <td height="36">&nbsp;</td>
      <td><label>
        <input name="Submit_login" type="submit" id="Submit_login" value="确定" />
      </label></td>
    </tr>
  </table>
</form>
<br><br><br><Br><Br>
<?
require_once "../footer.php";
exit;
}
$username=$_COOKIE['username'];
if (empty($username)) exit;
?>
wdcp系统用户管理面板:&nbsp;
<a href="<?=$PHP_SELF;?>?act=domain">域名邦定</a>
<a href="<?=$PHP_SELF;?>?act=ftp">FTP密码修改</a>
<a href="<?=$PHP_SELF;?>?act=mysql">mysql密码修改</a>
<a href="../phpmyadmin" target="_blank">phpmyadmin</a>
<a href="<?=$PHP_SELF;?>?act=logout">退出</a>
<br><br>
<?

if (isset($_GET['act']) and $_GET['act']=="logout") {
	js_close("关闭窗口");
	exit;
}

if (isset($_POST['Submit_ftp'])) {
	$opass=chop($_POST['opass']);
	$cpass=chop($_POST['npass']);
	$ncpass=chop($_POST['cnpass']);
	if ($cpass!==$ncpass) go_back("两次密码不同!");
	$q=$db->query("select * from wd_host where ftpuser='$username'");
	$re=$db->fetch_array($q);
	if (md5($opass)===$re['ftppasswd']) {
		$npass=md5($npass);
		$re1=wdl_sudo_app_user_chgpass($username,$cpass);
		print_r($re1);
		if ($re1==0) {
			$passwd=md5($passwd);
			$query = $db->query("update wd_host set ftppasswd='$npass' where ftpuser='$username'");
			str_go_url("密码修改成功!",0);
		}else
			go_back("密码修改失败!");
	}else
		go_back("原密码不对!");
}

if (isset($_POST['Submit_mysql'])) {
	$opass2=chop($_POST['opass2']);
	$cpass2=chop($_POST['npass2']);
	$ncpass2=chop($_POST['cnpass2']);
	if ($cpass2!==$ncpass2) go_back("两次密码不同!");
	$q=$db->query("select * from wd_host where ftpuser='$username'");
	$re=$db->fetch_array($q);
	//echo md5(opass2)."|".$re['dbpasswd'];exit;
	if (md5($opass2)===$re['dbpasswd']) {
		$dbuser=$re['dbuser'];
		require_once "../data/dbr.inc.php";
		$sql="use mysql;\n";
		$sql.="update user set password=password('$ncpass2') where user='$dbuser';\n";
		$sql.="flush privileges;";
		$sqlroot=wdl_sqlroot_pw();
		$link=@mysql_connect("localhost","root",$sqlroot) or go_back("mysql root密码错误");
		crunquery($sql);
		$nnpass=md5($cpass2);
		$db->query("update wd_host set dbpasswd='$nnpass' where ftpuser='$username'");
		str_go_url("密码修改成功!",0);
		exit;		
	}else
		go_back("原密码不对!");
}

if (isset($_POST['Submit_domain'])) {
	$domains=chop($_POST['domains']);
	if (!eregi("[a-z0-9]{1,50}\.[a-z]{2,3}",$domains)) go_back("域名有错!");
	$q=$db->query("update wd_host set domains='$domains',ups=1 where ftpuser='$username'");
	if (!$q) go_back("域名邦定失败!");
	str_go_url("域名邦定成功!,需管理员审核方生效!",0);
	exit;
}

if (isset($_GET['act']) and $_GET['act']=="ftp") {
?>
<form id="form2" name="form2" method="post" action="">
  <table width="403" height="177" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#EFEFEF">
    <tr>
      <td height="42" colspan="2"><div align="center"><strong>FTP用户密码修改</strong></div></td>
    </tr>
    <tr>
      <td width="151" height="31"><div align="center">原密码:</div></td>
      <td width="252"><label>
        <input name="opass" type="password" id="opass" />
      </label></td>
    </tr>
    <tr>
      <td height="34"><div align="center">新密码:</div></td>
      <td><label>
        <input name="npass" type="password" id="npass" />
      </label></td>
    </tr>
    <tr>
      <td height="32"><div align="center">确认新密码:</div></td>
      <td><label>
        <input name="cnpass" type="password" id="cnpass" />
      </label></td>
    </tr>
    <tr>
      <td height="36">&nbsp;</td>
      <td><label>
        <input name="Submit_ftp" type="submit" id="Submit_ftp" value="确定" />
      </label></td>
    </tr>
  </table>
</form>
<p>
  <?
}

if (isset($_GET['act']) and $_GET['act']=="mysql") {
?>
</p>
<form id="form3" name="form3" method="post" action="">
  <table width="403" height="177" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#EFEFEF">
    <tr>
      <td height="42" colspan="2"><div align="center"><strong>mysql数据库密码修改</strong></div></td>
    </tr>
    <tr>
      <td width="151" height="31"><div align="center">原密码:</div></td>
      <td width="252"><label>
        <input name="opass2" type="password" id="opass2" />
      </label></td>
    </tr>
    <tr>
      <td height="34"><div align="center">新密码:</div></td>
      <td><label>
        <input name="npass2" type="password" id="npass2" />
      </label></td>
    </tr>
    <tr>
      <td height="32"><div align="center">确认新密码:</div></td>
      <td><label>
        <input name="cnpass2" type="password" id="cnpass2" />
      </label></td>
    </tr>
    <tr>
      <td height="36">&nbsp;</td>
      <td><label>
        <input name="Submit_mysql" type="submit" id="Submit_mysql" value="确定" />
      </label></td>
    </tr>
  </table>
</form>
<p>
<? } 
if (isset($_GET['act']) and $_GET['act']=="domain") {
$q=$db->query("select id,domains from wd_host where ftpuser='$username'");
$re=$db->fetch_array($q);
?>
</p>
<form id="form4" name="form4" method="post" action="">
  <table width="680" height="169" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#EFEFEF">
    <tr>
      <td height="37" colspan="2"><div align="center"><strong>域名邦定</strong></div></td>
    </tr>
    <tr>
      <td width="95" height="30"><div align="center">域名</div></td>
      <td width="302"><label><input name="domains" type="text" id="domains" value="<?=$re['domains'];?>" size="80" />
      </label></td>
    </tr>
    
    <tr>
      <td height="36">&nbsp;</td>
      <td><label>
        <input name="Submit_domain" type="submit" id="Submit_domain" value="确定" />
      </label></td>
    </tr>
    <tr>
      <td height="63" colspan="2"><span class="STYLE1">备注:</span><br />
        1 不带http://开头,如bbs.wdlinux.cn<br />
        2 
      多个域名请用逗号(,)分隔,如www.wdlinux.cn,bbs.wdlinux.cn</td>
    </tr>
  </table>
</form>
<? } ?>
<p>
  <?
require_once "../footer.php";
?>
</p>
