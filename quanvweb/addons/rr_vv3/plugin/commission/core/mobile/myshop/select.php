<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

require rr_vv3_PLUGIN . 'commission/core/page_login_mobile.php';
class Select_RrvV3Page extends CommissionMobileLoginPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$member = m('member')->getMember($_W['openid']);

		if ($member['agentselectgoods'] == 1) {
			$err = '您无权自选商品，请和运营商联系!';

			if ($_W['ispost']) {
				show_json(-1, $err);
			}

			$this->message($err, '', 'error');
		}

		if (empty($this->set['select_goods'])) {
			if ($member['agentselectgoods'] != 2) {
				$err = '系统未开启自选商品!';

				if ($_W['ispost']) {
					show_json(-1, $err);
				}

				$this->message($err, '', 'error');
			}
		}

		$shop = pdo_fetch('select * from ' . tablename('rr_v_commission_shop') . ' where uniacid=:uniacid and mid=:mid limit 1', array(':uniacid' => $_W['uniacid'], ':mid' => $member['id']));

		if ($_W['ispost']) {
			$shopdata['selectgoods'] = intval($_GPC['selectgoods']);
			$shopdata['selectcategory'] = intval($_GPC['selectcategory']);
			$shopdata['uniacid'] = $_W['uniacid'];
			$shopdata['mid'] = $member['id'];

			if (is_array($_GPC['goodsids'])) {
				$shopdata['goodsids'] = implode(',', $_GPC['goodsids']);
			}

			if (!empty($shopdata['selectgoods']) && !is_array($_GPC['goodsids'])) {
				show_json(0, '请选择商品!');
			}

			if (empty($shop['id'])) {
				pdo_insert('rr_v_commission_shop', $shopdata);
			}
			else {
				pdo_update('rr_v_commission_shop', $shopdata, array('id' => $shop['id']));
			}

			show_json(1);
		}

		$goods = array();

		if (!empty($shop['selectgoods'])) {
			$goodsids = explode(',', $shop['goodsids']);

			if (!empty($goodsids)) {
				$goods = pdo_fetchall('select id,title,marketprice,thumb from ' . tablename('rr_v_goods') . ' where uniacid=:uniacid and id in ( ' . trim($shop['goodsids']) . ')', array(':uniacid' => $_W['uniacid']));
				$goods = set_medias($goods, 'thumb');
			}
		}

		$set = m('common')->getSysset('shop');

		if ($_W['shopset']['category']['level'] != -1) {
			$category = m('shop')->getCategory();
		}

		include $this->template();
	}
}

?>
