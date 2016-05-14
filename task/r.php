<?php
$key=chop($_GET['key']);
$act=chop($_GET['act']);
$f=chop($_GET['f']);
//echo $key."|".$act."|".$f."<br>";
$kf="../data/".$key.".txt";
$rf="../data/".$f.".txt";
if (!@file_exists($kf)) exit;
if ($act=="del" and @file_exists($kf)) @unlink($kf);
if ($act=="r" and @file_exists($rf)) {
	echo @file_get_contents($rf);
}
?>