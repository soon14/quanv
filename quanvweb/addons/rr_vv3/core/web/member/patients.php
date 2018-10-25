<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Patients_RrvV3Page extends WebPage 
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
		$join .= ' join ' . tablename('rr_v_member_patients') . ' f on f.openid=dm.openid';

		if ($_GPC['status'] != '') 
		{
			$condition .= ' and dm.status=' . intval($_GPC['status']);
		}
		$condition .= ' and f.isdelete=0 ';
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
		unset($row);
		if ($_GPC['export'] == '1') 
		{
			plog('member.doctors', '导出会员数据');
			foreach ($list as &$row ) 
			{
				$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
				$row['realname'] = str_replace('=', '', $row['realname']);
				$row['nickname'] = str_replace('=', '', $row['nickname']);
			}
			unset($row);
			m('excel')->export($list, array( 'title' => '患者数据', 'columns' => array( array('title' => '昵称', 'field' => 'nickname', 'width' => 12), array('title' => '姓名', 'field' => 'realname', 'width' => 12), array('title' => '手机号', 'field' => 'mobile', 'width' => 12), array('title' => 'openid', 'field' => 'openid', 'width' => 24), array('title' => '会员等级', 'field' => 'levelname', 'width' => 12), array('title' => '会员分组', 'field' => 'groupname', 'width' => 12), array('title' => '注册时间', 'field' => 'createtime', 'width' => 12), array('title' => '积分', 'field' => 'credit1', 'width' => 12), array('title' => '余额', 'field' => 'credit2', 'width' => 12), array('title' => '成交订单数', 'field' => 'ordercount', 'width' => 12), array('title' => '成交总金额', 'field' => 'ordermoney', 'width' => 12) ) ));
		}
		$total = pdo_fetchcolumn('select count(*) from' . tablename('rr_v_member_patients') . ' dm ' . $join . ' where 1 ' . $condition . ' ', $params);
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
		$hasupdiag = true;
		$uniacid = $_W['uniacid'];
		$patientsid = intval($_GPC['patientsid']);
		$diagid = intval($_GPC['diagid']);
		$info = pdo_fetch('select * from ' . tablename('rr_v_member_patients') . ' where id=:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $patientsid));
		$member = m('member')->getMember($info['openid']);
		//查患者关注的医生
		$sql = 'select doc_openid from '.tablename('rr_v_member_follow').' where uniacid = :uniacid and pat_openid = :pat_openid ';
		$res = pdo_fetchall($sql,array(':uniacid' => $uniacid,':pat_openid' => $info['openid']));
		// var_dump($res);
		if(!empty($res)){
			//关注的医生列表不为空
			foreach ($res as $value) {
				//拿$value['doc_openid']查医生
				$doctors[] = m('member')->getMember($value['doc_openid']);
			}	
		}

		$list = pdo_fetchall('select * from ' . tablename('rr_v_member_patients_diag') . ' where openid=:openid and uniacid=:uniacid and isdelete = :isdelete', array(':uniacid' => $_W['uniacid'], ':openid' => $info['openid'],':isdelete' => 0));
		
		$type = $_GPC['type'];
		if($type == '1'){
			$hasupdiag = false;
			$list_updiag = pdo_fetch('select * from ' . tablename('rr_v_member_patients_diag') . ' where id=:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $diagid));
			if(!empty($list_updiag)){
				$list_updiag['diag_thumbs'] = unserialize($list_updiag['diag_thumbs']);
				$list_updiag['createtime'] = date('Y-m-d H:i:s',$list_updiag['createtime']);
				
				
			}
		}


		if ($_W['ispost']) 
		{
			$data = array(
				'sex' => intval($_GPC['sex']),
				'marital_status' => intval($_GPC['marital_status']),
				'height' => trim($_GPC['height']),
				'weight' => trim($_GPC['weight']),
				'address' => trim($_GPC['address']),
				'allergy_goods' => trim($_GPC['allergy_goods']),
				'medical_history' => trim($_GPC['medical_history']),
				'home_medicalhistory' => trim($_GPC['home_medicalhistory']),
				'hereditary_medical' => trim($_GPC['hereditary_medical']),
				'issmoke' => intval($_GPC['issmoke']),
				'isliquor' => intval($_GPC['isliquor']),
				'status' => intval($_GPC['status']),
			);

			if (empty($diagid)){
				pdo_update('rr_v_member_patients', $data, array('id' => $patientsid, 'uniacid' => $_W['uniacid']));
				$member = array_merge($member, $data);
				plog('member.patients.edit', '修改患者资料  ID: ' . $member['id'] . ' <br/> 患者信息:  ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
				com_run('wxcard::updateMemberCardByOpenid', $member['openid']);
			}
			/* 修改病情资料
			$updiag_data = array(
				'uniacid' => $_W['uniacid'], 
				'openid' => $info['openid'], 
				'name' => trim($_GPC['name']),
				'age' => intval($_GPC['age']),
				'diag_department' => trim($_GPC['diag_department']), 
				'tel' => $_GPC['tel'],
				'diag_doctor' => intval($_GPC['diag_doctor']),
				'diag_day' => intval($_GPC['diag_day']), 
				'content' => trim($_GPC['content']),  
				'createtime' => time(),
			);
			if (is_array($_GPC['diag_thumbs'])) {
				$thumbs = $_GPC['diag_thumbs'];
				$thumb_url = array();

				foreach ($thumbs as $th) {
					$thumb_url[] = trim($th);
				}
				unset($th);
				$updiag_data['diag_thumbs'] = serialize($thumb_url);
			}
			
			if (!empty($diagid)){
				pdo_update('rr_v_member_patients_diag', $updiag_data, array('id' => $diagid, 'uniacid' => $_W['uniacid']));
				plog('member.patients.edit', '编辑患者病情信息 ID: ' . $diagid);
			}
			if(empty($diagid)){
				pdo_insert('rr_v_member_patients_diag', $updiag_data);
				plog('member.patients.edit', '添加患者病情信息 ');
			}
			*/
			show_json(1);
		}
		
		include $this->template();
	}

	public function delete() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		if (empty($id)) 
		{
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}else{
			pdo_update('rr_v_member_patients_diag', array('isdelete' => 1),array('id' => $id, 'uniacid' => $_W['uniacid']));
			plog('member.patients.delete', '删除就诊信息  ID: ' . $id );
		}
		show_json(1, array('url' => referer()));
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
		$members = pdo_fetchall('select id,openid,nickname,realname,mobile from ' . tablename('rr_v_member_patients') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		$black = intval($_GPC['isblack']);
		foreach ($members as $member ) 
		{
			if (!(empty($black))) 
			{
				pdo_update('rr_v_member_patients', array('isblack' => 1), array('id' => $member['id']));
				plog('member.doctors.edit', '设置黑名单 <br/>医生信息:  ID: ' . $member['id'] . ' /  ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
			}
			else 
			{
				pdo_update('rr_v_member_patients', array('isblack' => 0), array('id' => $member['id']));
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
		$ds = pdo_fetchall('SELECT id,headimgurl,nickname,openid,realname,mobile FROM ' . tablename('rr_v_member_patients') . ' WHERE 1 ' . $condition . ' order by createtime desc', $params);
		if ($_GPC['suggest']) 
		{
			exit(json_encode(array('value' => $ds)));
		}
		include $this->template();
	}
}
?>