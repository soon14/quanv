<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
require rr_vv3_PLUGIN . 'merch/core/inc/page_merch.php';
class Index_RrvV3Page extends MerchWebPage 
{
	public function main() 
	{
		global $_W;
		global $_GPC;
		$this->model->CheckPlugin('exhelper');
		include $this->template();
	}
}
?>