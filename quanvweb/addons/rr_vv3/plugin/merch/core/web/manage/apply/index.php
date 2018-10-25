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
		include $this->template();
	}
	public function ajaxgettotalprice() 
	{
		global $_W;
		$merchid = $_W['merchid'];
		$totals = $this->model->getMerchOrderTotalPrice($merchid);
		show_json(1, $totals);
	}
}
?>