<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

require rr_vv3_PLUGIN . 'commission/core/page_login_mobile.php';
class Set_RrvV3Page extends CommissionMobileLoginPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$member = m('member')->getMember($_W['openid']);
		$shop = pdo_fetch('select * from ' . tablename('rr_v_commission_shop') . ' where uniacid=:uniacid and mid=:mid limit 1', array(':uniacid' => $_W['uniacid'], ':mid' => $member['id']));

		if ($_W['ispost']) {
			$shopdata = (is_array($_GPC['shopdata']) ? $_GPC['shopdata'] : array());
			$shopdata['uniacid'] = $_W['uniacid'];
			$shopdata['mid'] = $member['id'];

			if (empty($shop['id'])) {
				pdo_insert('rr_v_commission_shop', $shopdata);
			}
			else {
				pdo_update('rr_v_commission_shop', $shopdata, array('id' => $shop['id']));
			}

			show_json(1);
		}

		$shop = set_medias($shop, array('img', 'logo'));
		$openselect = false;

		if ($this->set['select_goods'] == '1') {
			if (empty($member['agentselectgoods']) || ($member['agentselectgoods'] == 2)) {
				$openselect = true;
			}
		}
		else {
			if ($member['agentselectgoods'] == 2) {
				$openselect = true;
			}
		}

		$shop['openselect'] = $openselect;
		include $this->template('commission/myshop/set');
	}
}

?>
