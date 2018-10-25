<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Department_RrvV3Page extends WebPage 
{
	public function main() 
	{
		global $_W;
		global $_GPC;

		$children = array();
		$department = pdo_fetchall('SELECT * FROM ' . tablename('rr_v_member_department') . ' WHERE uniacid = \'' . $_W['uniacid'] . '\' ORDER BY parentid ASC, displayorder DESC');
		foreach ($department as $index => $row ) 
		{
			if (!(empty($row['parentid']))) 
			{
				$children[$row['parentid']][] = $row;
				unset($department[$index]);
			}
		}

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
		$parentid = intval($_GPC['parentid']);
		$id = intval($_GPC['id']);
		if (!(empty($id))) 
		{
			$item = pdo_fetch('SELECT * FROM ' . tablename('rr_v_member_department') . ' WHERE id = \'' . $id . '\' limit 1');
			$parentid = $item['parentid'];
		}
		else 
		{
			$item = array('displayorder' => 0,'isshow' => 1);
		}
		if (!(empty($parentid))) 
		{
			$parent = pdo_fetch('SELECT id, parentid, name FROM ' . tablename('rr_v_member_department') . ' WHERE id = \'' . $parentid . '\' limit 1');
			if (empty($parent)) 
			{
				$this->message('抱歉，上级分类不存在或是已经被删除！', webUrl('department/add'), 'error');
			}
			if (!(empty($parent['parentid']))) 
			{
				$parent1 = pdo_fetch('SELECT id, name FROM ' . tablename('rr_v_member_department') . ' WHERE id = \'' . $parent['parentid'] . '\' limit 1');
			}
		}
		if (empty($parent)) 
		{
			$level = 1;
		}
		else if (empty($parent['parentid'])) 
		{
			$level = 2;
		}
		else 
		{
			$level = 3;
		}
		if ($_W['ispost']) 
		{
			$data = array('uniacid' => $_W['uniacid'], 'name' => trim($_GPC['catename']), 'isshow' => intval($_GPC['isshow']), 'displayorder' => intval($_GPC['displayorder']), 'parentid' => intval($parentid), 'createtime' => strtotime(date('Y-m-d H:i:s')), 'level' => $level);
			if (!(empty($id))) 
			{
				unset($data['parentid']);
				pdo_update('rr_v_member_department', $data, array('id' => $id));
				
				plog('member.department.edit', '修改分类 ID: ' . $id);
			}
			else 
			{
				pdo_insert('rr_v_member_department', $data);
				$id = pdo_insertid();
				plog('member.department.add', '添加分类 ID: ' . $id);
			}
			m('shop')->getCategory(true);
			m('shop')->getAllCategory(true);
			show_json(1, array('url' => webUrl('member/department')));
		}
		include $this->template();
	}
	public function delete() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$item = pdo_fetch('SELECT id, name, parentid FROM ' . tablename('rr_v_member_department') . ' WHERE id = \'' . $id . '\'');
		if (empty($item)) 
		{
			$this->message('抱歉，分类不存在或是已经被删除！', webUrl('member/department', array('op' => 'display')), 'error');
		}
		pdo_delete('rr_v_member_department', array('id' => $id, 'parentid' => $id), 'OR');
		plog('shop.category.delete', '删除分类 ID: ' . $id . ' 分类名称: ' . $item['name']);
		m('shop')->getCategory(true);
		show_json(1, array('url' => referer()));
	}
	public function enabled() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		if (empty($id)) 
		{
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}
		$items = pdo_fetchall('SELECT id,name FROM ' . tablename('rr_v_member_department') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		foreach ($items as $item ) 
		{
			pdo_update('rr_v_member_department', array('enabled' => intval($_GPC['enabled'])), array('id' => $item['id']));
			plog('shop.dispatch.edit', (('修改分类状态<br/>ID: ' . $item['id'] . '<br/>分类名称: ' . $item['name'] . '<br/>状态: ' . $_GPC['enabled']) == 1 ? '显示' : '隐藏'));
		}
		m('shop')->getCategory(true);
		show_json(1, array('url' => referer()));
	}
	public function query() 
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$condition = ' and enabled=1 and uniacid=:uniacid';
		if (!(empty($kwd))) 
		{
			$condition .= ' AND `name` LIKE :keyword';
			$params[':keyword'] = '%' . $kwd . '%';
		}
		$ds = pdo_fetchall('SELECT * FROM ' . tablename('rr_v_member_department') . ' WHERE 1 ' . $condition . ' order by displayorder desc,id desc', $params);
		$ds = set_medias($ds, array('thumb', 'advimg'));
		if ($_GPC['suggest']) 
		{
			exit(json_encode(array('value' => $ds)));
		}
		include $this->template();
	}
}
?>