<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}
 
require rr_vv3_PLUGIN . 'app/core/page_mobile.php';
class Follow_RrvV3Page extends AppMobilePage
{	
	//小程序端患者关注医生接口
	public function set_follow()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$doc_openid = $param['doc_openid'];
		$pat_openid = 'sns_wa_'.$param['pat_openid'];

		$status = 0;

		if (empty($param['doc_openid']) || empty($param['pat_openid'])) {
			app_error(AppError::$ParamsError);
		}
		$res = pdo_fetch('select id,isfollow from '.tablename('rr_v_member_follow').' where uniacid =:uniacid 
		and doc_openid =:doc_openid and pat_openid =:pat_openid limit 1',array(':uniacid' => $uniacid,':doc_openid' => $doc_openid,':pat_openid' => $pat_openid));
		
		$data = array(
			'uniacid' => $uniacid,
			'doc_openid' => $doc_openid,
			'pat_openid' => $pat_openid,
			'createtime' => time(),

		);

		if(!empty($res)){
			pdo_delete('rr_v_member_follow', array('uniacid' => $uniacid, 'doc_openid' => $doc_openid, 'pat_openid' => $pat_openid));
			$result['isfollow'] = 2;
			$status = 1;
		}else{
			pdo_insert('rr_v_member_follow',$data);
			$result['isfollow'] = 1;
			$status = 1;
		}


		$result['status'] = $status;
		
		app_json($result);

	}
}

?>
