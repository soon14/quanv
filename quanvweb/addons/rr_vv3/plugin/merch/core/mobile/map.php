<?php
class Map_RrvV3Page extends PluginMobilePage 
{
	public function main() 
	{
		global $_W;
		global $_GPC;
		$merchid = intval($_GPC['merchid']);
		$store = pdo_fetch('select * from ' . tablename('rr_v_merch_user') . ' where id=:merchid and uniacid=:uniacid Limit 1', array(':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
		include $this->template();
	}
}
?>