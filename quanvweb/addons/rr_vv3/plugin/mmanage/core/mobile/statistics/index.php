<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

require rr_vv3_PLUGIN . 'mmanage/core/inc/page_mmanage.php';
class Index_RrvV3Page extends MmanageMobilePage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		include $this->template();
	}
}

?>
