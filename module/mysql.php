<?php

if ($wdcp_gid==1) {
?>
<dl>
    <dt><a href="###" onclick="showHide('items3');" target="_self">MYSQL管理</a></dt>
    <dd id="items3" style="display:none;">
			<ul>
<li><a href='mysql/fast_add.php' target='mainFrame'>快速创建</a></li>
<li><a href='mysql/db_add.php' target='mainFrame'>创建数据库</a></li>
<li><a href='mysql/db_list.php' target='mainFrame'>数据库列表</a></li>
<li><a href='mysql/user_add.php' target='mainFrame'>新建数据库用户</a></li>
<li><a href='mysql/user_list.php' target='mainFrame'>数据库用户列表</a></li>
<li><a href='<?=$phpmyadmin_dir;?>' target='mainFrame'>phpmyadmin</a></li>
<li><a href='mysql/chg_rootp.php' target='mainFrame'>修改root用户密码</a></li>
<li><a href='mysql/mysql.php' target='mainFrame'>mysql设置</a></li>
          </ul>
		</dd>
</dl>
<?php

}else{
?>
<dl>
    <dt><a href="###" onclick="showHide('items3');" target="_self">MYSQL管理</a></dt>
    <dd id="items3" style="display:none;">
			<ul>
<li><a href='mysql/fast_add.php' target='mainFrame'>快速创建</a></li>
<li><a href='mysql/db_add.php' target='mainFrame'>创建数据库</a></li>
<li><a href='mysql/db_list.php' target='mainFrame'>数据库列表</a></li>
<li><a href='mysql/user_add.php' target='mainFrame'>新建数据库用户</a></li>
<li><a href='mysql/user_list.php' target='mainFrame'>数据库用户列表</a></li>
<li><a href='<?=$phpmyadmin_dir;?>' target='mainFrame'>phpmyadmin</a></li>
    </ul>
		</dd>
</dl>
<?php

}
?>