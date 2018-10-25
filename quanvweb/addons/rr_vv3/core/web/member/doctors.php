<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Doctors_RrvV3Page extends WebPage 
{
	public function main() 
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = ' and dm.uniacid=:uniacid';
		$params = array(':uniacid' => $_W['uniacid']);
		if (!(empty($_GPC['mid']))) 
		{
			$condition .= ' and dm.id=:mid';
			$params[':mid'] = intval($_GPC['mid']);
		}
		if (!(empty($_GPC['realname']))) 
		{
			$_GPC['realname'] = trim($_GPC['realname']);
			$condition .= ' and ( dm.realname like :realname or dm.nickname like :realname or dm.mobile like :realname or dm.id like :realname)';
			$params[':realname'] = '%' . $_GPC['realname'] . '%';
		}
		if (empty($starttime) || empty($endtime)) 
		{
			$starttime = strtotime('-1 month');
			$endtime = time();
		}
		if (!(empty($_GPC['time']['start'])) && !(empty($_GPC['time']['end']))) 
		{
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			$condition .= ' AND dm.createtime >= :starttime AND dm.createtime <= :endtime ';
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}
		$join = '';
		$join .= ' join ' . tablename('rr_v_member_doctors') . ' f on f.openid=dm.openid';

		if ($_GPC['status'] != '') 
		{
			$condition .= ' and dm.status=' . intval($_GPC['status']);
		}
		$condition .= ' and f.isdelete=0 and f.isaudit = 1';
		$sql = 'select dm.realname,dm.mobile,dm.nickname,dm.avatar,f.* from ' . tablename('rr_v_member') . ' dm ' . $join . ' where 1 ' . $condition . '  ORDER BY id DESC';
		if (empty($_GPC['export'])) 
		{
			$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		}
		else 
		{
			ini_set('memory_limit', '-1');
		}
		$list = pdo_fetchall($sql, $params);
		$list_parent = array();
		$list_department = array();
		foreach ($list as $val ) 
		{
			$list_parent[] = trim($val['parentid'], ',');
			$list_department[] = trim($val['departmentid'], ',');
		}

		isset($list_parent) && ($list_parent = array_values(array_filter($list_parent)));
		if (!(empty($list_parent))) 
		{
			$res_parent = pdo_fetchall('select id,name as parentname from ' . tablename('rr_v_member_department') . ' where id in (' . implode(',', $list_parent) . ')', array(), 'id');
		}
		isset($list_department) && ($list_department = array_values(array_filter($list_department)));
		if (!(empty($list_department))) 
		{
			$res_department = pdo_fetchall('select id,name as departname from ' . tablename('rr_v_member_department') . ' where id in (' . implode(',', $list_department) . ')', array(), 'id');
		}

		$shop = m('common')->getSysset('shop');
		foreach ($list as &$row ) 
		{
			$row['parentname'] = ((isset($res_parent[$row['parentid']]) ? $res_parent[$row['parentid']]['parentname'] : ''));
			$row['departname'] = ((isset($res_department[$row['departmentid']]) ? $res_department[$row['departmentid']]['departname'] : ''));
			
		}
		unset($row);
		// if ($_GPC['export'] == '1') 
		// {
		// 	plog('member.doctors', '导出会员数据');
		// 	foreach ($list as &$row ) 
		// 	{
		// 		$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
		// 		$row['realname'] = str_replace('=', '', $row['realname']);
		// 		$row['nickname'] = str_replace('=', '', $row['nickname']);
		// 	}
		// 	unset($row);
		// 	m('excel')->export($list, array( 'title' => '医生数据', 'columns' => array( array('title' => '昵称', 'field' => 'nickname', 'width' => 12), array('title' => '姓名', 'field' => 'realname', 'width' => 12), array('title' => '手机号', 'field' => 'mobile', 'width' => 12), array('title' => 'openid', 'field' => 'openid', 'width' => 24), array('title' => '会员等级', 'field' => 'levelname', 'width' => 12), array('title' => '会员分组', 'field' => 'groupname', 'width' => 12), array('title' => '注册时间', 'field' => 'createtime', 'width' => 12), array('title' => '积分', 'field' => 'credit1', 'width' => 12), array('title' => '余额', 'field' => 'credit2', 'width' => 12), array('title' => '成交订单数', 'field' => 'ordercount', 'width' => 12), array('title' => '成交总金额', 'field' => 'ordermoney', 'width' => 12) ) ));
		// }
		$total = pdo_fetchcolumn('select count(*) from' . tablename('rr_v_member_doctors') . ' dm ' . $join . ' where 1 ' . $condition . ' ', $params);
		$pager = pagination2($total, $pindex, $psize);
		include $this->template();
	}
	public function detail() 
	{
		global $_W;
		global $_GPC;
		$area_set = m('util')->get_area_config_set();
		$new_area = intval($area_set['new_area']);
		$shop = $_W['shopset']['shop'];

		$id = intval($_GPC['id']);
		$info = pdo_fetch('select * from ' . tablename('rr_v_member_doctors') . ' where id=:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
		$info['specialty'] = explode(',',$info['specialty']);
		$member = m('member')->getMember($info['openid']);
		
		if(!empty($info['id_card'])){
			$info['id_card'] = unserialize($info['id_card']);
			foreach ($info['id_card'] as &$v) {
				$v = tomedia($v);
			}

		}

		if(!empty($info['practice_doctors_certificate'])){
			$info['practice_doctors_certificate'] = unserialize($info['practice_doctors_certificate']);
			foreach ($info['practice_doctors_certificate'] as &$value) {
				$value = tomedia($value);
			}
		}

		if(!empty($info['doctor_certificate'])){
			$info['doctor_certificate'] = unserialize($info['doctor_certificate']);
			foreach ($info['doctor_certificate'] as &$value) {
				$value = tomedia($value);
			}
		}
		
		// $parents = pdo_fetchall('select a.id AS pid,a.name AS parentname,b.id AS did,b.name AS departname from ' . tablename('rr_v_member_department') . ' AS a,' . tablename('rr_v_member_department') . ' AS b where a.uniacid=:uniacid AND a.isshow = 1 AND a.id = b.parentid ORDER BY a.id', array(':uniacid' => $_W['uniacid']));
		$parents = pdo_fetchall('select id,name from ' . tablename('rr_v_member_department') . ' where uniacid=:uniacid AND isshow = 1 ORDER BY id', array(':uniacid' => $_W['uniacid']));
		$specialty = pdo_fetchall('select id,title from '.tablename('rr_v_search').' where uniacid = :uniacid and enabled = :enabled',array(':uniacid' => $_W['uniacid'],':enabled' => 1));

		if ($_W['ispost']) 
		{
			$specialty = implode(',',$_GPC['specialty']);
			if(!empty($_GPC['practice_doctors_certificate'])){
				$practice_doctors_certificate = tomedia($_GPC['practice_doctors_certificate']);
			}
			if(!empty($_GPC['doctor_certificate'])){
				$doctor_certificate = tomedia($_GPC['doctor_certificate']);
			}
			$data = array(
				'job' => $_GPC['job'],
				'hospital' => $_GPC['hospital'],
				'status' => intval($_GPC['status']),
				'recommend_index' => intval($_GPC['recommend_index']),
				'specialty' => $specialty,
				'practice_doctors_certificate' => serialize($practice_doctors_certificate),
				'doctor_certificate' => serialize($doctor_certificate),
				'resume' => trim($_GPC['resume']),
			);
			$id_card = array();
			if(!empty($_GPC['front'])){
				$id_card['front'] = tomedia($_GPC['front']);
			}
			if(!empty($_GPC['verso'])){
				$id_card['verso'] = tomedia($_GPC['verso']);
			}
			$data['id_card'] = serialize($id_card);
			if(!empty($_GPC['parent'])){
				$res_parent = pdo_fetch('select id,parentid from ' . tablename('rr_v_member_department') . ' where id=:id limit 1', array(':id' => intval($_GPC['parent'])));
				if($res_parent['parentid'] != 0){
					$data['parentid'] = $res_parent['parentid'];
					$data['departmentid'] = intval($_GPC['parent']);
				}else{
					$data['parentid'] = intval($_GPC['parent']);
					$data['departmentid'] = 0;
				}
			}

			if(!empty($_GPC['front']) && !empty($_GPC['verso'])){
				$data['id_card'] = serialize(array('front' => tomedia($_GPC['front']), 'verso' => tomedia($_GPC['verso'])));
			}

			if(!empty($_GPC['practice_doctors_certificate'])){
				$data['practice_doctors_certificate'] = serialize(tomedia($_GPC['practice_doctors_certificate']));
			}

			if(!empty($_GPC['doctor_certificate'])){
				$data['doctor_certificate'] = serialize(tomedia($_GPC['doctor_certificate']));
			}
			
			pdo_update('rr_v_member_doctors', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
			plog('member.doctors.edit', '修改医生资料  ID: ' . $member['id'] . ' <br/> 医生信息:  ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);

			show_json(1);
		}
		
		include $this->template();
	}

	public function setblack() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		if (empty($id)) 
		{
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}
		$members = pdo_fetchall('select id,openid,nickname,realname,mobile from ' . tablename('rr_v_member_doctors') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		$black = intval($_GPC['isblack']);
		foreach ($members as $member ) 
		{
			if (!(empty($black))) 
			{
				pdo_update('rr_v_member_doctors', array('isblack' => 1), array('id' => $member['id']));
				plog('member.doctors.edit', '设置黑名单 <br/>医生信息:  ID: ' . $member['id'] . ' /  ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
			}
			else 
			{
				pdo_update('rr_v_member_doctors', array('isblack' => 0), array('id' => $member['id']));
				plog('member.doctors.edit', '取消黑名单 <br/>医生信息:  ID: ' . $member['id'] . ' /  ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
			}
		}
		show_json(1);
	}

	public function query() 
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		$wechatid = intval($_GPC['wechatid']);
		if (empty($wechatid)) 
		{
			$wechatid = $_W['uniacid'];
		}
		$params = array();
		$params[':uniacid'] = $wechatid;
		$condition = ' and uniacid=:uniacid';
		if (!(empty($kwd))) 
		{
			$condition .= ' AND ( `nickname` LIKE :keyword or `realname` LIKE :keyword or `mobile` LIKE :keyword )';
			$params[':keyword'] = '%' . $kwd . '%';
		}
		$ds = pdo_fetchall('SELECT id,headimgurl,nickname,openid,realname,mobile FROM ' . tablename('rr_v_member_doctors') . ' WHERE 1 ' . $condition . ' order by createtime desc', $params);
		if ($_GPC['suggest']) 
		{
			exit(json_encode(array('value' => $ds)));
		}
		include $this->template();
	}

	/**
	 * 设置医生为名医推荐
	 * @param  string $id 医生主键id
	 * @return 
	 */
	public function setFamous()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$id = $_GPC['id'];
		if(empty($id)){
			show_json(0,'没有该医生记录,请刷新重试');
		}
		
		$result = pdo_update('rr_v_member_doctors',array('isfamous' => 1),array('id' => $id,'uniacid' => $uniacid));
		
		show_json(1);
		
	}

	/**
	 * 取消医生名医推荐
	 * @param  string $id 医生主键id
	 * @return 
	 */
	public function setNoFamous()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$id = $_GPC['id'];
		if(empty($id)){
			show_json(0,'没有该医生记录,请刷新重试');
		}
		
		$result = pdo_update('rr_v_member_doctors',array('isfamous' => 0),array('id' => $id,'uniacid' => $uniacid));
		
		show_json(1);
		
	}

	/**
	 * 医生排序
	 * @param  string $id 医生主键id
	 * @return 
	 */
	public function change()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			show_json(0, array('message' => '参数错误'));
		}

		$type = trim($_GPC['type']);
		$value = trim($_GPC['value']);

		if (!in_array($type, array('displayorder'))) {
			show_json(0, array('message' => '参数错误'));
		}

		$doctors = pdo_fetch('select id,displayorder from ' . tablename('rr_v_member_doctors') . ' where id=:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));

		if (empty($doctors)) {
			show_json(0, array('message' => '参数错误'));
		}


		pdo_update('rr_v_member_doctors', array($type => $value), array('id' => $id));

		show_json(1);
		
	}

	
}
?>


