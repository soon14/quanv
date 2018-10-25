<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}
 
require rr_vv3_PLUGIN . 'app/core/page_mobile.php';
class Identity_RrvV3Page extends AppMobilePage
{
	public function get_data()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];

		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}else{

			$list = pdo_fetch('SELECT identity,isaudit FROM ' . tablename('rr_v_member') . ' WHERE openid = "' . $openid . '" AND uniacid=' . $uniacid . ' ');
			
		}
		
		app_json(array('list' => $list));

	}

	//申请成为医生
	public function doc_apply()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];

		$status = 0;

		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}else{

			$data = array(
				'uniacid' => $uniacid,
				'openid' => $openid,
				'job' => trim($param['job']),
				'hospital' => trim($param['hospital']),
				'reject' => '',
				'createtime' => time()
			);

			if(!empty($param['departid'])){
				$res_depart = pdo_fetch('select id,parentid from ' . tablename('rr_v_member_department') . ' where id=:id and uniacid=:uniacid', array(':id' => intval($param['departid']), ':uniacid' => $uniacid));
				if($res_depart['parentid'] != 0){
					$data['parentid'] = $res_depart['parentid'];
					$data['departmentid'] = intval($param['departid']);
				}else{
					$data['parentid'] = intval($param['departid']);
					$data['departmentid'] = 0;
				}
			}

			if(!empty($param['practice_doctors_certificate']) && is_array($param['practice_doctors_certificate'])){
				$data['practice_doctors_certificate'] = serialize($param['practice_doctors_certificate']);
			}
			if(!empty($param['doctor_certificate']) && is_array($param['doctor_certificate'])){
				$data['doctor_certificate'] = serialize($param['doctor_certificate']);
			}

			if(!empty($param['front']) && !empty($param['verso'])){
				$data['id_card'] = serialize(array('front' => $param['front'], 'verso' => $param['verso']));
			}
			if(!empty($param['avatar'])){
				$data['avatars'] = trim($param['avatar']);
			}

			$doc = pdo_fetch('select id,isaudit from ' . tablename('rr_v_member_doctors') . ' where openid=:openid and uniacid=:uniaicd ', array(':openid' => $openid, ':uniaicd' => $uniacid));

			if($param['identity'] == '1'){

				if (!empty($doc)){
					$data['isdelete'] = 0;
					$data['isaudit'] = 0;
					pdo_update('rr_v_member_doctors', $data, array('openid' => $openid, 'uniacid' => $uniacid));
				}else{
					pdo_insert('rr_v_member_doctors', $data);
				}
				$doc_data = array(
					'isaudit' => 1,
					'realname' => trim($param['realname']),
					'mobile' => trim($param['mobile'])
				);
				pdo_update('rr_v_member', $doc_data, array('openid' => $openid, 'uniacid' => $uniacid));
				$status = 1;

			}
			
		}
		$result['status'] = $status;
		$result['isaudit'] = 0;
		app_json($result);

	}

	//查询申请成为医生提交数据
	public function get_doc_apply()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];

		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}else{

			$list = pdo_fetch('SELECT a.id,a.hospital,a.job,a.parentid,a.departmentid,a.isaudit,a.practice_doctors_certificate,a.doctor_certificate,a.id_card,a.reject,b.realname,b.mobile,a.avatars avatar FROM ' . tablename('rr_v_member_doctors') . ' a,' . tablename('rr_v_member') . ' b where a.openid=:openid and a.uniacid=:uniaicd and a.openid=b.openid ', array(':openid' => $openid, ':uniaicd' => $uniacid));
			if(!empty($list['practice_doctors_certificate'])){
				$list['practice_doctors_certificate'] = unserialize($list['practice_doctors_certificate']);
				foreach ($list['practice_doctors_certificate'] as &$val) {
					$val = tomedia($val);
				}
			}else{
				$list['practice_doctors_certificate'] = array();
			}
			if(!empty($list['doctor_certificate'])){
				$list['doctor_certificate'] = unserialize($list['doctor_certificate']);
				foreach ($list['doctor_certificate'] as &$row) {
					$row = tomedia($row);
				}
			}else{
				$list['doctor_certificate'] = array();
			}
			if(!empty($list['id_card'])){
				$list['id_card'] = unserialize($list['id_card']);
				foreach ($list['id_card'] as &$value) {
					$value = tomedia($value);
				}
			}else{
				$list['id_card'] = array();
			}
			if(!$list){
				$list = array();
			}
			
		}
		
		app_json(array('list' => $list));

	}

}

?>
