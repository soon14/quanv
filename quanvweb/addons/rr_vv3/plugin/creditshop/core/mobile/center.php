<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Center_RrvV3Page extends PluginMobilePage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$member = m('member')->getMember($_W['openid']);
		include $this->template();
	}
}

?>
