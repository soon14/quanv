<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
require rr_vv3_PLUGIN . 'merch/core/inc/page_merch.php';
class Temp_RrvV3Page extends MerchWebPage 
{
	public function __construct($_com = 'virtual') 
	{
		parent::__construct($_com);
	}
	public function main() 
	{
		global $_W;
		global $_GPC;
		$page = ((empty($_GPC['page']) ? '' : $_GPC['page']));
		$pindex = max(1, intval($page));
		$psize = 12;
		$kw = ((empty($_GPC['keyword']) ? '' : $_GPC['keyword']));
		$items = pdo_fetchall('SELECT * FROM ' . tablename('rr_v_virtual_type') . ' WHERE uniacid=:uniacid and merchid=:merchid and title like :name order by id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, array(':name' => '%' . $kw . '%', ':uniacid' => $_W['uniacid'], ':merchid' => $_W['merchid']));
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('rr_v_virtual_type') . ' WHERE uniacid=:uniacid and merchid=:merchid and title like :name order by id desc ', array(':uniacid' => $_W['uniacid'], ':name' => '%' . $kw . '%', ':merchid' => $_W['merchid']));
		$pager = pagination($total, $pindex, $psize);
		$category = pdo_fetchall('select * from ' . tablename('rr_v_virtual_category') . ' where uniacid=:uniacid and merchid=:merchid order by id desc', array(':uniacid' => $_W['uniacid'], ':merchid' => $_W['merchid']), 'id');
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
		$datacount = 0;
		if (!(empty($id))) 
		{
			$item = pdo_fetch('SELECT * FROM ' . tablename('rr_v_virtual_type') . ' WHERE id=:id and uniacid=:uniacid and merchid=:merchid ', array(':id' => $id, ':uniacid' => $_W['uniacid'], ':merchid' => $_W['merchid']));
			$item['fields'] = iunserializer($item['fields']);
			$datacount = pdo_fetchcolumn('select count(*) from ' . tablename('rr_v_virtual_data') . ' where typeid=:typeid and uniacid=:uniacid and merchid=:merchid and openid=\'\' limit 1', array(':typeid' => $id, ':uniacid' => $_W['uniacid'], ':merchid' => $_W['merchid']));
		}
		if ($_W['ispost']) 
		{
			$keywords = $_GPC['tp_kw'];
			$names = $_GPC['tp_name'];
			if (!(empty($keywords))) 
			{
				$data = array();
				foreach ($keywords as $key => $val ) 
				{
					$data[$keywords[$key]] = $names[$key];
				}
			}
			$insert = array('uniacid' => $_W['uniacid'], 'cate' => intval($_GPC['cate']), 'title' => trim($_GPC['tp_title']), 'fields' => iserializer($data), 'merchid' => $_W['merchid']);
			if (empty($id)) 
			{
				pdo_insert('rr_v_virtual_type', $insert);
				$id = pdo_insertid();
				mplog('virtual.temp.edit', '添加模板 ID: ' . $id);
			}
			else 
			{
				pdo_update('rr_v_virtual_type', $insert, array('id' => $id));
				mplog('virtual.temp.edit', '编辑模板 ID: ' . $id);
			}
			show_json(1, array('url' => merchUrl('goods/virtual/temp')));
		}
		$category = pdo_fetchall('select * from ' . tablename('rr_v_virtual_category') . ' where uniacid=:uniacid and merchid=:merchid order by id desc', array(':uniacid' => $_W['uniacid'], ':merchid' => $_W['merchid']), 'id');
		include $this->template();
	}
	public function tpl() 
	{
		global $_W;
		global $_GPC;
		$kw = $_GPC['kw'];
		include $this->template('goods/virtual/temp/tpl');
		exit();
	}
	public function delete() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		if (empty($id)) 
		{
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}
		$types = pdo_fetchall('SELECT * FROM ' . tablename('rr_v_virtual_type') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid'] . ' and merchid=' . $_W['merchid']);
		foreach ($types as $type ) 
		{
			pdo_delete('rr_v_virtual_type', array('id' => $type['id']));
			pdo_delete('rr_v_virtual_data', array('typeid' => $type['id']));
			mplog('virtual.temp.delete', '删除模板 ID: ' . $type['id']);
		}
		show_json(1, array('url' => merchUrl('goods/virtual')));
	}
}
?>