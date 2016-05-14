<?php
require_once "inc/common.inc.php";
require_once "login.php";

if ($wdcp_gid==1) {
	require_once(G_T("admin/left.htm"));
}else { 
	require_once(G_T("user/left.htm"));
}