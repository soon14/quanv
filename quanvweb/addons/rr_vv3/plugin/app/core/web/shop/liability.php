<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}
  
class Liability_RrvV3Page extends WebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = ' and uniacid=:uniacid ';
		$params = array(':uniacid' => $_W['uniacid']);

		if ($_GPC['status'] != '') {
			$condition .= ' and status=' . intval($_GPC['status']);
		}

		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and title  like :keyword';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}

		$list = pdo_fetchall('SELECT * FROM ' . tablename('rr_v_liability') . ' WHERE 1 ' . $condition . '  ORDER BY displayorder DESC limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_liability') . ' WHERE 1 ' . $condition, $params);
		$pager = pagination($total, $pindex, $psize);
		include $this->template();
	}

	public function add()
	{
		$this->post();
	}

	public function edit()
	{
		$this->post();
	}

	protected function post()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		if ($_W['ispost']) {
			$data = array('uniacid' => $_W['uniacid'], 'displayorder' => intval($_GPC['displayorder']), 'title' => trim($_GPC['title']), 'thumb' => save_media($_GPC['thumb']), 'detail' => m('common')->html_images($_GPC['detail']), 'status' => intval($_GPC['status']), 'createtime' => time());

			if (!empty($id)) {
				pdo_update('rr_v_liability', $data, array('id' => $id));
				plog('app.shop.liability.edit', '修改免责声明 ID: ' . $id);
			}
			else {
				pdo_insert('rr_v_liability', $data);
				$id = pdo_insertid();
				plog('app.shop.liability.add', '修改免责声明 ID: ' . $id);
			}

			show_json(1, array('url' => webUrl('app/shop/liability')));
		}

		$liability = pdo_fetch('SELECT * FROM ' . tablename('rr_v_liability') . ' WHERE id =:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		
		include $this->template();
	}

	public function displayorder()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$displayorder = intval($_GPC['value']);
		$item = pdo_fetchall('SELECT id,title FROM ' . tablename('rr_v_liability') . ' WHERE id in( ' . $id . ' )  AND uniacid=' . $_W['uniacid']);

		if (!empty($item)) {
			pdo_update('rr_v_liability', array('displayorder' => $displayorder), array('id' => $id));
			plog('app.app.shop.liability.edit', '修改免责声明排序 ID: ' . $item['id'] . ' 标题: ' . $item['advname'] . ' 排序: ' . $displayorder . ' ');
		}

		show_json(1);
	}

	public function delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,title FROM ' . tablename('rr_v_liability') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_delete('rr_v_liability', array('id' => $item['id']));
			plog('app.app.shop.liability.delete', '删除免责声明 ID: ' . $item['id'] . ' 标题: ' . $item['title'] . ' ');
		}

		show_json(1, array('url' => referer()));
	}

	public function status()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,title FROM ' . tablename('rr_v_liability') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_update('rr_v_liability', array('status' => intval($_GPC['status'])), array('id' => $item['id']));
			plog('app.app.shop.liability.edit', ('修改免责声明状态<br/>ID: ' . $item['id'] . '<br/>标题: ' . $item['title'] . '<br/>状态: ' . $_GPC['status']) == 1 ? '显示' : '隐藏');
		}

		show_json(1, array('url' => referer()));
	}
}

?>
