<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Category_RrvV3Page extends PluginWebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;

		if (!empty($_GPC['catname'])) {
			ca('universalform.category.edit');

			foreach ($_GPC['catname'] as $id => $catname ) {
				$catname = trim($catname);

				if (empty($catname)) {
					continue;
				}


				if ($id == 'new') {
					pdo_insert('rr_v_universalform_category', array('name' => $catname, 'uniacid' => $_W['uniacid']));
					$insert_id = pdo_insertid();
					plog('universalform.category.add', '添加万能表单分类 ID: ' . $insert_id);
				}
				 else {
					pdo_update('rr_v_universalform_category', array('name' => $catname), array('id' => $id));
					plog('universalform.category.edit', '修改万能表单分类 ID: ' . $id);
				}
			}

			plog('universalform.category.edit', '批量修改分类');
			show_json(1, array('url' => webUrl('universalform/category')));
		}


		$list = pdo_fetchall('SELECT * FROM ' . tablename('rr_v_universalform_category') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' ORDER BY id DESC');
		include $this->template();
	}

	public function delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$item = pdo_fetch('SELECT id,name FROM ' . tablename('rr_v_universalform_category') . ' WHERE id = \'' . $id . '\' AND uniacid=' . $_W['uniacid'] . '');

		if (empty($item)) {
			$this->message('抱歉，分类不存在或是已经被删除！', webUrl('universalform/category', array('op' => 'display')), 'error');
		}


		pdo_delete('rr_v_universalform_category', array('id' => $id));
		plog('universalform.category.delete', '删除分类 ID: ' . $id . ' 标题: ' . $item['name'] . ' ');
		show_json(1, array('url' => webUrl('universalform/category', array('op' => 'display'))));
	}
}


?>