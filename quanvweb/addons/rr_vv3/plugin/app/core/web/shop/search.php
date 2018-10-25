<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}
 
class Search_RrvV3Page extends WebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = ' and uniacid=:uniacid and iswxapp=1 ';
		$params = array(':uniacid' => $_W['uniacid']);

		if ($_GPC['enabled'] != '') {
			$condition .= ' and enabled=' . intval($_GPC['enabled']);
		}

		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and title  like :keyword';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}

		$list = pdo_fetchall('SELECT * FROM ' . tablename('rr_v_search') . ' WHERE 1 ' . $condition . '  ORDER BY displayorder DESC limit ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_search') . ' WHERE 1 ' . $condition, $params);
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
			$data = array('uniacid' => $_W['uniacid'], 'title' => trim($_GPC['title']), 'enabled' => intval($_GPC['enabled']), 'displayorder' => intval($_GPC['displayorder']), 'iswxapp' => 1);

			if (!empty($id)) {
				pdo_update('rr_v_search', $data, array('id' => $id));
				plog('app.shop.search.edit', '修改搜索栏 ID: ' . $id);
			}
			else {
				pdo_insert('rr_v_search', $data);
				$id = pdo_insertid();
				plog('app.shop.search.add', '添加搜索栏 ID: ' . $id);
			}

			show_json(1, array('url' => webUrl('app/shop/search')));
		}

		$item = pdo_fetch('select * from ' . tablename('rr_v_search') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
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

		$items = pdo_fetchall('SELECT id,title FROM ' . tablename('rr_v_search') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_delete('rr_v_search', array('id' => $item['id']));
			plog('app.shop.search.delete', '删除搜索栏 ID: ' . $item['id'] . ' 标题: ' . $item['title'] . ' ');
		}

		show_json(1, array('url' => referer()));
	}

	public function displayorder()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$displayorder = intval($_GPC['value']);
		$item = pdo_fetchall('SELECT id,title FROM ' . tablename('rr_v_search') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		if (!empty($item)) {
			pdo_update('rr_v_search', array('displayorder' => $displayorder), array('id' => $id));
			plog('app.shop.search.edit', '修改搜索栏排序 ID: ' . $item['id'] . ' 标题: ' . $item['title'] . ' 排序: ' . $displayorder . ' ');
		}

		show_json(1);
	}

	public function enabled()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,title FROM ' . tablename('rr_v_search') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_update('rr_v_search', array('enabled' => intval($_GPC['enabled'])), array('id' => $item['id']));
			plog('app.shop.search.edit', ('修改搜索栏状态<br/>ID: ' . $item['id'] . '<br/>标题: ' . $item['title'] . '<br/>状态: ' . $_GPC['enabled']) == 1 ? '显示' : '隐藏');
		}

		show_json(1, array('url' => referer()));
	}
}

?>
