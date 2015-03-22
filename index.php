<?php
require_once "inc/common.inc.php";
require_once "login.php";

if (isset($_SESSION['turl']))
    $turl = $_SESSION['turl'];
else
    $turl = "default.php";

require_once(G_T("index.htm"));
?>
