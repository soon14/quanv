<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Selecticon_RrvV3Page extends WebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		include $this->template();
	}
}

?>
