<?php
require_once "inc/common.inc.php";
require_once "login.php";
$utt="";
if ($dns_is==1){
	if ($wdcp_gid==5)
		$utt="客服管理员";
	elseif ($wdcp_gid==6)
		$utt="合作伙伴(公司)";
	elseif ($wdcp_gid==7)
		$utt="合作伙伴(个人)";
	else;
	$wdcp_user=$utt."".$wdcp_user;
}
require_once(G_T("top.htm"));
?>

