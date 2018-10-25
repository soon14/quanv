<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}
 
class Reward_RrvV3Page extends WebPage
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

		$list = pdo_fetchall('SELECT * FROM ' . tablename('rr_v_reward') . ' WHERE 1 ' . $condition . '  ORDER BY displayorder DESC limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_reward') . ' WHERE 1 ' . $condition, $params);
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
			$data = array('uniacid' => $_W['uniacid'], 'title' => trim($_GPC['title']), 'price' => trim($_GPC['price']), 'status' => intval($_GPC['status']), 'displayorder' => intval($_GPC['displayorder']), 'icon' => save_media($_GPC['icon']), 'createtime' => time());

			if (!empty($id)) {
				pdo_update('rr_v_reward', $data, array('id' => $id));
				plog('app.shop.reward.edit', '修改打赏类别 ID: ' . $id);
			}
			else {
				pdo_insert('rr_v_reward', $data);
				$id = pdo_insertid();
				plog('app.shop.reward.add', '添加打赏类别 ID: ' . $id);
			}

			show_json(1, array('url' => webUrl('app/shop/reward')));
		}

		$item = pdo_fetch('select * from ' . tablename('rr_v_reward') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		include $this->template();
	}

	public function delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,title FROM ' . tablename('rr_v_reward') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_delete('rr_v_reward', array('id' => $item['id']));
			plog('app.shop.reward.delete', '删除打赏类别 ID: ' . $item['id'] . ' 打赏标题: ' . $item['title'] . ' ');
		}

		show_json(1, array('url' => referer()));
	}

	public function displayorder()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$displayorder = intval($_GPC['value']);
		$item = pdo_fetchall('SELECT id,title FROM ' . tablename('rr_v_reward') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		if (!empty($item)) {
			pdo_update('rr_v_reward', array('displayorder' => $displayorder), array('id' => $id));
			plog('app.shop.reward.edit', '修改打赏类别排序 ID: ' . $item['id'] . ' 打赏标题: ' . $item['title'] . ' 排序: ' . $displayorder . ' ');
		}

		show_json(1);
	}

	public function status()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,title FROM ' . tablename('rr_v_reward') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_update('rr_v_reward', array('status' => intval($_GPC['status'])), array('id' => $item['id']));
			plog('app.shop.reward.edit', ('修改打赏类别状态<br/>ID: ' . $item['id'] . '<br/>打赏标题: ' . $item['title'] . '<br/>状态: ' . $_GPC['status']) == 1 ? '显示' : '隐藏');
		}

		show_json(1, array('url' => referer()));
	}
}

?>
