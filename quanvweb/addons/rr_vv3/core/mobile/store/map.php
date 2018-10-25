<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Map_RrvV3Page extends MobilePage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$merchid = intval($_GPC['merchid']);

		if (0 < $merchid) {
			$store = pdo_fetch('select * from ' . tablename('rr_v_merch_store') . ' where id=:id and uniacid=:uniacid and merchid=:merchid', array(':id' => $id, ':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
		}
		else {
			$store = pdo_fetch('select * from ' . tablename('rr_v_store') . ' where id=:id and uniacid=:uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		}

		$store['logo'] = empty($store['logo']) ? $_W['shopset']['shop']['logo'] : $store['logo'];
		include $this->template();
	}
}

?>