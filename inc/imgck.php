<?PHP
/*
# WDlinux Control Panel, online management of Linux servers, virtual hosts
# Author:wdlinux QQ:12571192
# Url:http://www.wdlinux.cn/wdcp
# Last Updated 2011.03
*/
//if(!defined('WD_ROOT')) exit("wdcp err!");
//

//error_reporting(0);
session_start();
session_register('ckcode');
$_SESSION['ckcode'] = '';
$width = '60';//ͼƬ࠭
$height = '20';//ͼƬٟ

$textall = array_merge_recursive(range('0', '9'));
for ($i = 0; $i < 4; $i++) {
    $tmptext = rand(0, 9);
    $randtext = $textall[$tmptext];
    $ckcode .= $randtext;
}

$_SESSION['ckcode'] = $ckcode;

@header("Expires: -1");
@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
@header("Pragma: no-cache");


$im = imagecreate($width, $height);
$backgroundcolor = imagecolorallocate($im, 255, 255, 255);

$numorder = array(1, 2, 3, 4);
shuffle($numorder);
$numorder = array_flip($numorder);

for ($i = 1; $i <= 4; $i++) {
    $x = $numorder[$i] * 13 + mt_rand(0, 4) - 2;
    $y = mt_rand(0, 3);
    $text_color = imagecolorallocate($im, mt_rand(50, 255), mt_rand(50, 128), mt_rand(50, 255));
    imagechar($im, 5, $x + 5, $y + 3, $ckcode[$numorder[$i]], $text_color);
}
$linenums = mt_rand(10, 32);
for ($i = 0; $i <= $linenums; $i++) {
    $linecolor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
    $linex = mt_rand(0, $width);
    $liney = mt_rand(0, $height);
    imageline($im, $linex, $liney, $linex + mt_rand(0, 4) - 2, $liney + mt_rand(0, 4) - 2, $linecolor);
}

for ($i = 0; $i <= 40; $i++) {
    $pointcolor = imagecolorallocate($im, mt_rand(50, 255), mt_rand(50, 255), mt_rand(50, 255));
    imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $pointcolor);
}

$bordercolor = imagecolorallocate($im, 150, 150, 150);
imagerectangle($im, 0, 0, $width - 1, $height - 1, $bordercolor);

header('Content-type: image/png');
imagepng($im);
imagedestroy($im);
?>