<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Category_RrvV3Page extends ComWebPage
{
	public function main()
	{
		global $_W;
		$all = pdo_fetchall('SELECT *FROM ' . tablename('rr_v_newstore_category') . ' WHERE uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
		include $this->template();
	}

	public function detail()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$item = pdo_fetch('SELECT name FROM ' . tablename('rr_v_newstore_category') . ' WHERE id = :id AND uniacid = :uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));

		if ($_W['ispost']) {
			$name = trim($_GPC['name']);

			if (empty($name)) {
				show_json(0, '门店分类名称不能为空!');
			}

			if (empty($id)) {
				pdo_insert('rr_v_newstore_category', array('name' => $name, 'uniacid' => $_W['uniacid']));
			}
			else {
				pdo_update('rr_v_newstore_category', array('name' => $name), array('uniacid' => $_W['uniacid'], 'id' => $id));
			}

			show_json(1, array('url' => webUrl('store/category')));
		}

		include $this->template();
	}

	public function delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			exit();
		}

		pdo_delete('rr_v_newstore_category', array('id' => $id, 'uniacid' => $_W['uniacid']));
		show_json(1);
	}
}

?>
