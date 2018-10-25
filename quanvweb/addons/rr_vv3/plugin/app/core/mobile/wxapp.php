<?php
 
function app_error($errcode = 0, $message = '')
{
	exit(json_encode(array('error' => $errcode, 'message' => empty($message) ? AppError::getError($errcode) : $message)));
}

function app_json($result = NULL, $openid)
{
	global $_GPC;
	global $_W;
	$ret = array();

	if (!is_array($result)) {
		$result = array();
	}

	$ret['error'] = 0;
	$key = time() . '@' . $openid;
	$auth = array('authkey' => base64_encode(authcode($key, 'ENCODE', 'rr_vv3_wxapp')));
	m('cache')->set($auth['authkey'], 1);
	exit(json_encode(array_merge($ret, $auth, $result)));
}

if (!defined('IN_IA')) {
	exit('Access Denied');
}

require rr_vv3_PLUGIN . 'app/core/error_code.php';
require rr_vv3_PLUGIN . 'app/core/wxapp/wxBizDataCrypt.php';
class Wxapp_RrvV3Page extends Page
{
	protected $appid;
	protected $appsecret;

	public function __construct()
	{
		$data = m('common')->getSysset('app');
		$this->appid = $data['appid'];
		$this->appsecret = $data['secret'];
	}

	public function login()
	{
		global $_GPC;
		global $_W;
		$code = trim($_GPC['code']);

		if (empty($code)) {
			app_error(AppError::$ParamsError);
		}

		$url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $this->appid . '&secret=' . $this->appsecret . '&js_code=' . $code . '&grant_type=authorization_code';
		load()->func('communication');
		$resp = ihttp_request($url);

		if (is_error($resp)) {
			app_error(AppError::$SystemError, $resp['message']);
		}

		$arr = @json_decode($resp['content'], true);
		$arr['isclose'] = $_W['shopset']['app']['isclose'];

		if (!empty($_W['shopset']['app']['isclose'])) {
			$arr['closetext'] = $_W['shopset']['app']['closetext'];
		}

		if (!is_array($arr) || !isset($arr['openid'])) {
			app_error(AppError::$WxAppLoginError);
		}

		app_json($arr, $arr['openid']);
	}

	/**
     * 微信小程序登录
     */
	public function auth()
	{
		global $_GPC;
		global $_W;
		$encryptedData = trim($_GPC['data']);
		$iv = trim($_GPC['iv']);
		$sessionKey = trim($_GPC['sessionKey']);
		if (empty($encryptedData) || empty($iv)) {
			app_error(AppError::$ParamsError);
		}

		$pc = new WXBizDataCrypt($this->appid, $sessionKey);
		$errCode = $pc->decryptData($encryptedData, $iv, $data);

		if ($errCode == 0) {
			$data = json_decode($data, true);
			$member = m('member')->getMember('sns_wa_' . $data['openId']);

			if (empty($member)) {
				//增加member会员头像大小byte数据获取
				$data['avatarUrl'] = str_replace('/0', '/132', $data['avatarUrl']);		//小程序默认头像是/0最大，统一换成和公众号一样的大小/132
				$avatarurl = $data['avatarUrl'];		
				$avatar_byte = strlen($this->curl_file_get_contents($avatarurl));
				//curl_file_get_contents 109行增加此函数：curl方法获取远程文件信息
				//
				
				$member = array(
					'uniacid' => $_W['uniacid'], 'uid' => 0, 'openid' => 'sns_wa_' . $data['openId'], 
					'nickname' => !empty($data['nickName']) ? $data['nickName'] : '', 
					'MD5nickname' => MD5($data['nickName']),
					'wxnickname' => base64_encode($data['nickName']),
					'avatar' => !empty($data['avatarUrl']) ? $data['avatarUrl'] : '', 
					'gender' => !empty($data['gender']) ? $data['gender'] : '-1', 
					'openid_wa' => $data['openId'], 'comefrom' => 'sns_wa', 
					'avatar_byte' => (!(empty($avatar_byte)) ? $avatar_byte : 0), 
					'createtime' => time(), 'status' => 0
				);
				// $member = array('uniacid' => $_W['uniacid'], 'uid' => 0, 
					//'openid' => 'sns_wa_' . $data['openId'], 'nickname' => !empty($data['nickName']) ? $data['nickName'] : '', 
					//'avatar' => !empty($data['avatarUrl']) ? $data['avatarUrl'] : '', 
					//'gender' => !empty($data['gender']) ? $data['gender'] : '-1', 
					//'openid_wa' => $data['openId'], 'comefrom' => 'sns_wa', 'createtime' => time(), 'status' => 0
				//);
				pdo_insert('rr_v_member', $member);

				//添加会员到患者用户表
				$userData['uniacid'] = $_W['uniacid'];
				$userData['openid'] = 'sns_wa_' . $data['openId'];
				$userData['createtime'] = time();
				$pat = pdo_fetch('select id from ' . tablename('rr_v_member_patients') . ' where openid=:openid and uniacid=:uniaicd limit 1 ', array(':openid' => 'sns_wa_' . $data['openId'], ':uniaicd' => $_W['uniacid']));
				if (empty($pat))
				{
					pdo_insert('rr_v_member_patients', $userData);
				}else{
					pdo_update('rr_v_member_patients', array('isdelete' => 0), array('openid' => 'sns_wa_' . $data['openId'], 'uniacid' => $_W['uniacid']));
				}

				$id = pdo_insertid();
				$data['id'] = $id;
				$data['uniacid'] = $_W['uniacid'];
			}
			else {
				$updateData = array('nickname' => !empty($data['nickName']) ? $data['nickName'] : '', 'gender' => !empty($data['gender']) ? $data['gender'] : '-1');
				$updateData['MD5nickname'] = MD5($data['nickName']);
				$updateData['wxnickname'] = base64_encode($data['nickName']);
				if(empty($member['avatar'])){
					$updateData['avatar'] = !empty($data['avatarUrl']) ? $data['avatarUrl'] : '';
				}
				pdo_update('rr_v_member', $updateData, array('id' => $member['id'], 'uniacid' => $member['uniacid']));
				$data['id'] = $member['id'];
				$data['uniacid'] = $member['uniacid'];
			}

			app_json($data, $data['openId']);
		}

		app_error(AppError::$WxAppError, '登录错误, 错误代码: ' . $errCode);
	}

	//curl方法获取远程文件信息
	public function curl_file_get_contents($durl){
	   $ch = curl_init();
	   curl_setopt($ch, CURLOPT_URL, $durl);
	   curl_setopt($ch, CURLOPT_TIMEOUT, 5);				//超时5秒
	   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 	// 跳过证书检查  
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); 	 // 从证书中检查SSL加密算法是否存在  
	   curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);	//在HTTP请求中包含一个"User-Agent: "头的字符串。
	   curl_setopt($ch, CURLOPT_REFERER,_REFERER_);			//在HTTP请求头中"Referer: "的来路内容。
	   curl_setopt($ch, CURLOPT_FRESH_CONNECT,1);			//强制获取一个新的连接，替代缓存中的连接。
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);			//将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
	   $r = curl_exec($ch);
	   curl_close($ch);
	   return $r;
	}

	public function check()
	{
		global $_GPC;
		global $_W;
		$openid = trim($_GPC['openid']);

		if (empty($openid)) {
			app_error(AppError::$ParamsError);
		}

		$wxopenid = 'sns_wa_' . $openid;
		$member = m('member')->getMember($wxopenid);

		if (empty($member)) {
			$member = array('uniacid' => $_W['uniacid'], 'uid' => 0, 'openid' => $wxopenid, 'openid_wa' => $openid, 'comefrom' => 'sns_wa', 'createtime' => time(), 'status' => 0);
			pdo_insert('rr_v_member', $member);
			$member['id'] = pdo_insertid();
		}

		app_json(array('uniacid' => $member['uniacid'], 'openid' => $member['openid'], 'id' => $member['id'], 'nickname' => $member['nickname'], 'avatarUrl' => tomedia($member['avatar'])), $member['openid']);
	}

}

?>
