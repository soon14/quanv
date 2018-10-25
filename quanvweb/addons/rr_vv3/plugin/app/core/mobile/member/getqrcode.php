<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}


require rr_vv3_PLUGIN . 'app/core/page_mobile.php';
class Getqrcode_RrvV3Page extends AppMobilePage
{
	//获取医生二维码
	public function get_data()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];

		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}

		load()->func('communication');


		$list = pdo_fetch('select openid,status from ' . tablename('rr_v_member_qrcode') . ' where uniacid=:uniacid and openid=:openid limit 1', array(':uniacid' => $uniacid, ':openid' => $openid));
		if(!empty($list)){

			if($list['status'] == 1){
				$img_parth = 'images/wxapperweima/'.$openid.'.jpg';
			}else{
				show_json(0, '该用户二维码已设置隐藏，请联系管理员处理！');
			}

		}else{

			$appdata = m('common')->getSysset('app');
			$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appdata['appid'].'&secret='.$appdata['secret'];
			$content = ihttp_get($url);
			$token = @json_decode($content['content'], true);

			// $url2 = "https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=".$token['access_token'];//二维码
			$url2 = "https://api.weixin.qq.com/wxa/getwxacode?access_token=".$token['access_token'];//小程序码
			$path='pages/patient/doctor/doctor?openid='.$openid;
	        $width=64;
			$data ='{"path":"'.$path.'","width":'.$width.'}';
			// $return = ihttp_request($url, urldecode(json_encode($data)));
			$return = ihttp_request($url2,$data);	//远程http获取接口返回
			if(is_error($return)) {
			    return error(-1, "访问公众平台接口失败, 错误: {$return['message']}");
			}
			
			// ATTACHMENT_ROOT  文件存放目录
			$erweima = ATTACHMENT_ROOT.'images/wxapperweima/'.$openid.'.jpg';//设置路径和文件名，真实环境下文件名需要用一个医生ID为规律的命名
			$img_parth = 'images/wxapperweima/'.$openid.'.jpg';
	        file_put_contents($erweima,$return['content']);	// $return['content']为返回的二进制数据流 保存到制定路径

	        $res_data = array('uniacid' => $uniacid, 'openid' => $openid, 'status' => 1, 'createtime' => time());
			pdo_insert('rr_v_member_qrcode', $res_data);

		}
		
		$result['file_url'] = tomedia($img_parth);

		app_json($result);
		
	}

}