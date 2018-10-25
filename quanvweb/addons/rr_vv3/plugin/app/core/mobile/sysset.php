<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}
 
require rr_vv3_PLUGIN . 'app/core/page_mobile.php';
class Sysset_RrvV3Page extends AppMobilePage
{
	//我的页获取系统设置信息
	public function get_sysset()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$openid = 'sns_wa_'.$param['openid'];
		if(empty($openid)){
			app_error(AppError::$ParamsError);
		}else{	
			
			$phone = $_W['shopset']['contact']['phone'];
			$complaintcall = $_W['shopset']['contact']['complaintcall'];

			$member = m('member')->getMember($openid);
			if($member['level']==0){
				$member['level'] = 'V0';
			}else{
				$level = pdo_fetch('select id,levelname from ' . tablename('rr_v_member_level') . ' where uniacid = :uniacid and id=:id  LIMIT 1', array(':uniacid' => $uniacid, ':id' => $res_doc['level']));
				$member['level'] = $level['levelname'];
			}
			$list['phone'] = $phone;
			$list['complaintcall'] = $complaintcall;
			$list['member'] = $member;
			
		}
		app_json(array('list' => $list));

	}	
	

}

?>
