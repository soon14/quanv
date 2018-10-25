<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}
 
require rr_vv3_PLUGIN . 'app/core/page_mobile.php';
class Auth_RrvV3Page extends AppMobilePage
{
	public function token()
	{
		global $_GPC;
		$token = trim($_GPC['token']);

		if (!empty($token)) {
			$token = authcode(base64_decode($token), 'DECODE', '*736bg%21@');

			if (!empty($token)) {
				app_json(array('token' => $token));
			}
			else {
				app_error(AppError::$UserTokenFail);
			}
		}

		app_json();
	}
}

?>
