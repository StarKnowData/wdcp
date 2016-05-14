<?php

if ($wdcp_gid==1) {
?>
<dl>
    <dt><a href="###" onclick="showHide('items2');" target="_self">网站管理</a></dt>
    <dd id="items2" style="display:block;">
			<ul>
<li><a href='vhost/vhost_adda.php' target='mainFrame'>创建整站</a></li>
<li><a href='vhost/vhost_add.php' target='mainFrame'>新建站点</a></li>
<li><a href='vhost/vhost_list.php' target='mainFrame'>站点列表</a></li>
<li><a href='vhost/subdomain.php' target='mainFrame'>二级域名</a></li>
<li><a href='vhost/htpasswd.php' target='mainFrame'>验证访问</a></li>
<li><a href='vhost/rewrite.php' target='mainFrame'>rewrite规则管理</a></li>
<li><a href='vhost/php.php' target='mainFrame'>php设置</a></li>
          </ul>
		</dd>
</dl>
<?php

}else{
?>
<dl>
    <dt><a href="###" onclick="showHide('items2');" target="_self">站点管理</a></dt>
    <dd id="items2" style="display:block;">
			<ul>
<li><a href='vhost/vhost_adda.php' target='mainFrame'>创建整站</a></li>
<li><a href='vhost/vhost_add.php' target='mainFrame'>新建站点</a></li>
<li><a href='vhost/vhost_list.php' target='mainFrame'>站点列表</a></li>
<li><a href='vhost/subdomain.php' target='mainFrame'>二级域名</a></li>
<li><a href='vhost/htpasswd.php' target='mainFrame'>验证访问</a></li>
<li><a href='sys/filem.php' target='mainFrame'>文件管理器</a></li>
          </ul>
		</dd>
</dl>
<?php

}
?>