<?php

if ($wdcp_gid==1) {
?>
<dl>
    <dt><a href="###" onclick="showHide('dns1');" target="_self">智能DNS系统</a></dt>
    <dd id="dns1" style="display:block;">
			<ul>
				<li><a href="dns/sys_set.php" target="mainFrame">DNS服务器设置</a></li>
				<li><a href="dns/domain_add.php" target="mainFrame">域名增加</a></li>
				<li><a href="dns/domain_list.php" target="mainFrame">域名列表</a></li>
				<?php if ($dns_ptr_is==1) { ?>
				<li><a href="dns/ptr_list.php" target="mainFrame">PTR列表</a></li> 
				<?php } ?>
				<?php if ($dns_url_is==1) { ?>
				<li><a href="dns/url_list.php" target="mainFrame">URL列表</a></li>
				<?php } ?>
				<li><a href="dns/domain_check.php" target="mainFrame">域名检测</a></li>
				<li><a href="dns/change_ip.php" target="mainFrame">批量操作</a></li>
				<li><a href="dns/query_count_day.php" target="mainFrame">查询统计</a></li>
				<li><a href="dns/attack_log.php" target="mainFrame">攻击检测</a></li>
				<li><a href="dns/monitor.php" target="mainFrame">监控域名</a></li>
				<li><a href="dns/monitor_log.php" target="mainFrame">监控记录</a></li>
				<li><a href="dns/mail_tp.php" target="mainFrame">邮件模板</a></li>
				<li><a href="dns/dns_product.php" target="mainFrame">DNS产品管理</a></li>
				<?php if ($wddns_is==1 or $dns_key_num>=3) { ?>
				<li><a href="dns/dns_group.php" target="mainFrame">DNS分组</a></li>
				<?php } ?>
          </ul>
		</dd>
</dl>
<dl>
    <dt><a href="###" onclick="showHide('dns2');" target="_self">财务管理</a></dt>
    <dd id="dns2" style="display:block;">
			<ul>
				<li><a href="admin/amoney.php" target="mainFrame">入款扣款</a></li>
				<li><a href="admin/pay_log.php" target="mainFrame">支付记录</a></li>
				<li><a href="admin/buy_log.php" target="mainFrame">购买记录</a></li>
				<li><a href='admin/pay_set.php' target='mainFrame'>支付接口</a></li>
				<li><a href='memberd/account.php' target='mainFrame'>帐务信息</a></li>
				<li><a href='memberd/accounts.php' target='mainFrame'>帐务统计</a></li>
          </ul> 
		</dd>
</dl>
<?php
union_menu();
}elseif ($wdcp_gid==5) {
?>
<dl>
    <dt><a href="###" onclick="showHide('dns1');" target="_self">智能DNS管理</a></dt>
    <dd id="dns1" style="display:block;">
			<ul>
				<li><a href="dns/domain_add.php" target="mainFrame">域名增加</a></li>
				<li><a href="dns/domain_list.php" target="mainFrame">域名列表</a></li>
				<li><a href="dns/ptr_list.php" target="mainFrame">PTR列表</a></li>
				<li><a href="dns/domain_check.php" target="mainFrame">域名检测</a></li>
				<li><a href="dns/query_count_day_u.php" target="mainFrame">查询统计</a></li>
				<li><a href="dns/monitor.php" target="mainFrame">宕机监控</a></li>
				<li><a href="dns/monitor_log.php" target="mainFrame">监控记录</a></li>
          </ul>
		</dd>
</dl>
<?php
union_menu();
}else{
?>
<dl>
    <dt><a href="###" onclick="showHide('dns1');" target="_self">智能DNS管理</a></dt>
    <dd id="dns1" style="display:block;">
			<ul>
				<li><a href="dns/domain_add.php" target="mainFrame">域名增加</a></li>
				<li><a href="dns/domain_list.php" target="mainFrame">域名列表</a></li>
				<li><a href="dns/domain_check.php" target="mainFrame">域名检测</a></li>
				<li><a href="dns/change_ip.php" target="mainFrame">批量操作</a></li>
				<li><a href="dns/query_count_day_u.php" target="mainFrame">查询统计</a></li>
				<li><a href="dns/monitor.php" target="mainFrame">宕机监控</a></li>
				<li><a href="dns/monitor_log.php" target="mainFrame">监控记录</a></li>
          </ul>
		</dd>
</dl>
<?php
union_menu();
}
?>