<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}
 
require rr_vv3_PLUGIN . 'app/core/page_mobile.php';
class Hotsearch_RrvV3Page extends AppMobilePage
{
	
	//返回给小程序端热门搜索词
	public function hotkeyword()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$type = $param['type'];//类型，0表示无，1表示视频，2表示文章,3是找医生热门搜索词
		$limit = ' limit 0,10';
		$params = array(':uniacid' => $uniacid);
		if(empty($type)){
			app_error(AppError::$ParamsError);
		}
		if($type == 1){
			$params[':type'] = 1;
		}elseif($type == 2){
			$params[':type'] = 2;
		}elseif($type == 3){
			$params[':type'] = 3;
		}else{
			app_error(AppError::$ParamsError);
		}
		$sql = 'select content from '.tablename('rr_v_hotsearch').' where uniacid = :uniacid and type = :type order by search_count desc '.$limit.' ';
		$hotkeyword = pdo_fetchall($sql,$params);

		app_json(array('hotkeyword' => $hotkeyword));

		
	}

	




}

?>
