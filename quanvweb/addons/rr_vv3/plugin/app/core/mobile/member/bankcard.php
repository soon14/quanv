<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}
 
require rr_vv3_PLUGIN . 'app/core/page_mobile.php';
class Bankcard_RrvV3Page extends AppMobilePage
{
	//获取我的银行卡
	public function my_bankcard()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];

		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}

		$list = pdo_fetchall('select * from '.tablename('rr_v_member_bankcard').' where uniacid =:uniacid and openid =:openid ORDER BY id,username ',array(':uniacid' => $uniacid,':openid' => $openid));


		app_json(array('list' => $list));
	}

	//绑定银行卡
	public function add_bankcard()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$id = intval($param['id']);

		$status = 0;

		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}

		$data = array(
			'uniacid' => $uniacid,
			'openid' => $openid,
			'username' => trim($param['username']),
			'bankname' => trim($param['bankname']),
			'bankcard' => trim($param['bankcard']),
			'createtime' => time(),
		);

		if(!empty($id)){
			pdo_update('rr_v_member_bankcard', $data, array('uniacid' => $uniacid, 'openid' => $openid, 'id' => $id));
			$status = 1;
		}else{
			pdo_insert('rr_v_member_bankcard', $data);
			$status = 1;
		}

		$result['status'] = $status;
		
		app_json($result);

	}

	//删除银行卡
	public function del_bankcard()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$id = intval($param['id']);

		$status = 0;

		if (empty($param['openid']) || empty($id)) {
			app_error(AppError::$ParamsError);
		}else{

			pdo_delete('rr_v_member_bankcard', array('uniacid' => $uniacid,'openid' => $openid,'id' => $id));
			$status = 1;
		}

		$result['status'] = $status;
		
		app_json($result);
	}
}

?>
