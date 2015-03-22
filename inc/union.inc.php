<?php

if (@file_exists(WD_ROOT . "/data/union_is")) $union_is = 1;
else    $union_is = 0;
function union_menu()
{
    global $union_is, $wdcp_gid, $union_a_menu, $union_u_menu;
    if ($wdcp_gid == 1 and $union_is == 1) {
        echo $union_a_menu;
    } elseif ($union_is == 1) {
        echo $union_u_menu;
    } else;
}

function sum_result($sql)
{
    global $db;
    $query = $db->query($sql);
    $sum = $db->num_rows($query);
    return $sum;
}

function random($length, $numeric = 0)
{
    PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
    if ($numeric) {
        $hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
    } else {
        $hash = '';
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
    }
    return $hash;
}

$union_a_menu = '
<dl>
    <dt><a href="###" onclick="showHide(\'items88\');" target="_self">邀请注册</a></dt>
    <dd id="items88" style="display:none;">
			<ul>
					<li><a href="unionu/invite.php" target="mainFrame">邀请注册</a></li>
					<li><a href="uniona/invite_m.php" target="mainFrame">邀请注册列表</a></li>
					<li><a href="uniona/invite_mr.php" target="mainFrame">直接注册列表</a></li>
          </ul>
		</dd>
</dl>';

$union_u_menu = '
<dl>
    <dt><a href="###" onclick="showHide(\'items89\');" target="_self">邀请注册</a></dt>
    <dd id="items89" style="display:none;">
			<ul>
					<li><a href="unionu/invite.php" target="mainFrame">邀请注册</a></li>
          </ul>
		</dd>
</dl>';

?>