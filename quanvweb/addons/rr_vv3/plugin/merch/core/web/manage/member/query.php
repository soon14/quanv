<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
require rr_vv3_PLUGIN . 'merch/core/inc/page_merch.php';
class Query_RrvV3Page extends MerchWebPage 
{
	public function main() 
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$condition = ' and uniacid=:uniacid';
		if (!(empty($kwd))) 
		{
			$condition .= ' AND (`realname` LIKE :keyword or `nickname` LIKE :keyword or `mobile` LIKE :keyword)';
			$params[':keyword'] = '%' . $kwd . '%';
		}
		$ds = pdo_fetchall('SELECT * FROM ' . tablename('rr_v_member') . ' WHERE 1 ' . $condition . ' order by id asc', $params);
		if ($_GPC['suggest']) 
		{
			exit(json_encode(array('value' => $ds)));
		}
		include $this->template();
		exit();
	}
}
?>