<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Auditing_RrvV3Page extends WebPage 
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
		$condition .= ' and f.isdelete=0 and f.isaudit IN(0,2)';
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
			$data = array(
				'job' => $_GPC['job'],
				'hospital' => $_GPC['hospital'],
				'status' => intval($_GPC['status']),
				'recommend_index' => intval($_GPC['recommend_index']),
				'specialty' => $specialty,
				'introduce' => trim($_GPC['introduce']),
				'id_card' => serialize($_GPC['id_card']),
				'practice_doctors_certificate' => serialize($_GPC['practice_doctors_certificate']),
				'doctor_certificate' => serialize($_GPC['doctor_certificate']),
			);
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
			
			pdo_update('rr_v_member_doctors', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
			plog('member.doctors.edit', '修改医生资料  ID: ' . $member['id'] . ' <br/> 医生信息:  ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);

			show_json(1);
		}
		
		include $this->template();
	}

	public function update() 
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$openid = $_GPC['openid'];
		$isaudit = intval($_GPC['isaudit']);

		$members = pdo_fetch('select * from ' . tablename('rr_v_member_doctors') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
		if(empty($members)){
			show_json(0,'无医生数据信息或已被删除！');
		}else{

			if($isaudit == 1){

				//将医生关注公众号的openid和小程序openid进行绑定，绑定为同一个医生
				$condition = ' uniacid= :uniacid and openid=:openid ';
				$where = ' uniacid= :uniacid and openid<>:openid and avatar_byte>0';
				$params = array(':uniacid' => $uniacid, ':openid' => $members['openid']);
				$wx_list = pdo_fetch('select * from ' . tablename('rr_v_member') . ' where ' . $condition . ' limit 1', $params);
				if(empty($wx_list['memberid'])){
					$params[':MD5nickname'] = $wx_list['MD5nickname'];
					$gz_list = pdo_fetchall('select * from ' . tablename('rr_v_member') . ' where ' . $where . ' and MD5nickname=:MD5nickname', $params);
					if(count($gz_list) < 2){
						foreach ($gz_list as &$row) {
							if($wx_list['avatar_byte'] == $row['avatar_byte']){
								pdo_update('rr_v_member', array('memberid' => $wx_list['id']), array('uniacid' => $uniacid, 'openid' => $row['openid']));
								pdo_update('rr_v_member', array('memberid' => $row['id']), array('uniacid' => $uniacid, 'openid' => $wx_list['openid']));
							}else{
								$num = $row['avatar_byte'] > $wx_list['avatar_byte'] ? ($row['avatar_byte'] - $wx_list['avatar_byte']) : ($wx_list['avatar_byte'] - $row['avatar_byte']);
								if($num < 10){
									pdo_update('rr_v_member', array('memberid' => $wx_list['id']), array('uniacid' => $uniacid, 'openid' => $row['openid']));
									pdo_update('rr_v_member', array('memberid' => $row['id']), array('uniacid' => $uniacid, 'openid' => $wx_list['openid']));
								}elseif($wx_list['MD5nickname'] == $row['MD5nickname']){
									pdo_update('rr_v_member', array('memberid' => $wx_list['id']), array('uniacid' => $uniacid, 'openid' => $row['openid']));
									pdo_update('rr_v_member', array('memberid' => $row['id']), array('uniacid' => $uniacid, 'openid' => $wx_list['openid']));
								}
							}
						}
						unset($row);
					}
					if(count($gz_list) > 2){
						
						foreach ($gz_list as &$val) {

							if($wx_list['avatar_byte'] == $row['avatar_byte']){
								pdo_update('rr_v_member', array('memberid' => $wx_list['id']), array('uniacid' => $uniacid, 'openid' => $row['openid']));
								pdo_update('rr_v_member', array('memberid' => $row['id']), array('uniacid' => $uniacid, 'openid' => $wx_list['openid']));
							}else{

								$num = $val['avatar_byte'] > $wx_list['avatar_byte'] ? ($val['avatar_byte'] - $wx_list['avatar_byte']) : ($wx_list['avatar_byte'] - $val['avatar_byte']);

								if($num < 10){
									pdo_update('rr_v_member', array('memberid' => $wx_list['id']), array('uniacid' => $uniacid, 'openid' => $val['openid']));
									pdo_update('rr_v_member', array('memberid' => $val['id']), array('uniacid' => $uniacid, 'openid' => $wx_list['openid']));
								}elseif($wx_list['MD5nickname'] == $row['MD5nickname']){
									pdo_update('rr_v_member', array('memberid' => $wx_list['id']), array('uniacid' => $uniacid, 'openid' => $row['openid']));
									pdo_update('rr_v_member', array('memberid' => $row['id']), array('uniacid' => $uniacid, 'openid' => $wx_list['openid']));
								}
							}
						}
						unset($val);
						

					}
					// foreach ($gz_list as &$row) {
					// 	if($wx_list['nickname'] == $row['nickname'] && $wx_list['avatar_byte'] == $row['avatar_byte']){
					// 		pdo_update('rr_v_member', array('memberid' => $wx_list['id']), array('uniacid' => $uniacid, 'openid' => $row['openid']));
					// 		pdo_update('rr_v_member', array('memberid' => $row['id']), array('uniacid' => $uniacid, 'openid' => $wx_list['openid']));
					// 	}
					// }
					// unset($row);
				}

				$member = m('member')->getMember($members['openid']);
				$kefu_openid = m('member')->getMember($member['memberid']);
				if(empty($member['memberid'])){
					show_json(0,'该医生还未关注公众号，请先关注公众号！');
				}
				
				// if(!empty($members['id_card']) && !empty($members['practice_doctors_certificate']) && !empty($members['doctor_certificate'])){
					pdo_update('rr_v_member_doctors', array('isaudit' => $isaudit, 'reject' => ''), array('id' => $members['id']));
					plog('member.doctors.edit', '医生信息审核:  ID: ' . $members['id'] . ' /  ' . $members['openid']);

					if(empty($members['avatars'])){
						pdo_update('rr_v_member', array('isaudit' => 2, 'identity' => 1), array('uniacid' => $uniacid, 'openid' => $members['openid']));
					}else{
						pdo_update('rr_v_member', array('isaudit' => 2, 'identity' => 1, 'avatar' => $members['avatars']), array('uniacid' => $uniacid, 'openid' => $members['openid']));
					}
					

					pdo_update('rr_v_member_patients', array('isdelete' => 1), array('openid' => $members['openid'], 'uniacid' => $uniacid));


					//添加医生到客服表
					if(!empty($kefu_openid)){

						$kefu = pdo_fetch('select id from ' . tablename('messikefu_cservice') . ' where content=:openid and weid=:uniaicd limit 1 ', array(':openid' => $kefu_openid['openid'], ':uniaicd' => $_W['uniacid']));

						if(empty($kefu)){

							$kefuData = array(
								'weid' => $_W['uniacid'],'ctype' => 1,'thumb' => $member['avatar'],'displayorder' => 0,
								'starthour' => 8,'endhour' => 23,'autoreply' => '','isonline' => 0,'groupid' => 1,'fansauto' => '',
								'kefuauto' => '','isautosub' => 0,'qrtext' => '','qrcolor' => '',
								'qrbg' => '','iskefuqrcode' => 0,'kefuqrcode' => '','ishow' => 0,
								'notonline' => '','lingjie' => 0,'typename' => '在线问诊','isgly' => 0,
								'iszx' => 0,'isrealzx' => 0,'username' => '','pwd' => '',

							);

							$kefuData['content'] = $kefu_openid['openid'];
							if(empty($member['realname'])){

								$kefuData['name'] = $member['nickname'];

							}else{

								$kefuData['name'] = $member['realname'];

							}
							pdo_insert('messikefu_cservice', $kefuData);
						}
					}
				// }else{
				// 	show_json(0,'医生资质未上传，不能审核！');
				// }
			}
		}
		show_json(1);
	}

	public function reject() 
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$openid = $_GPC['openid'];
		$isaudit = intval($_GPC['isaudit']);

		$members = pdo_fetch('select * from ' . tablename('rr_v_member_doctors') . ' where openid=:openid and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
		if(empty($members)){
			show_json(0,'无医生数据信息或已被删除！');
		}

		if ($_W['ispost']) 
		{

			pdo_update('rr_v_member_doctors', array('isaudit' => $isaudit, 'reject' => trim($_GPC['reject'])), array('id' => $members['id']));
			plog('member.doctors.edit', '医生信息审核:  ID: ' . $members['id'] . ' /  ' . $members['openid']);
			pdo_update('rr_v_member', array('isaudit' => 3, 'identity' => 0), array('uniacid' => $uniacid, 'openid' => $members['openid']));

			$userData['uniacid'] = $uniacid;
			$userData['openid'] = $members['openid'];
			$userData['createtime'] = time();
			
			$pat = pdo_fetch('select id from ' . tablename('rr_v_member_patients') . ' where openid=:openid and uniacid=:uniaicd limit 1 ', array(':openid' => $members['openid'], ':uniaicd' => $uniacid));

			if (!(empty($pat))) 
			{
				$userData['isdelete'] = 0;
				pdo_update('rr_v_member_patients', $userData, array('openid' => $members['openid'], 'uniacid' => $uniacid));
			}else{
				pdo_insert('rr_v_member_patients', $userData);
			}
				
			show_json(1);
		}
		include $this->template();
	}

	public function ajaxgettotals() 
	{
		global $_W;
		global $_GPC;
		$paras = array(':uniacid' => $_W['uniacid']);

		$totals['isaudit'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('rr_v_member_doctors') . '' . ' WHERE uniacid = :uniacid and isdelete=0 and isaudit IN(0,2)', $paras);
		$result = ((empty($totals) ? array() : $totals));
		show_json(1, $result);
	}
}
?>


