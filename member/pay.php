<?php

/*
# WDlinux Control Panel,online management of Linux servers, virtual hosts
# Wdcdn system
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcdn
# Last Updated 2011.05
*/

require_once "../inc/common.inc.php";
require_once "../login.php";
if ($pay_type==2)
	require_once(G_T("memberd/pays.htm"));
else
	require_once(G_T("memberd/pay.htm"));
?>
