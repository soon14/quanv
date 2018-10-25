<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
require rr_vv3_PLUGIN . 'merch/core/inc/page_merch.php';
class Printset_RrvV3Page extends MerchWebPage 
{
	public function main() 
	{
		global $_W;
		global $_GPC;
		$merchid = $_W['merchid'];
		$sys = pdo_fetch('select * from ' . tablename('rr_v_exhelper_sys') . ' where uniacid=:uniacid and merchid=:merchid limit 1 ', array(':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
		if ($_W['ispost']) 
		{
			$port = intval($_GPC['port']);
			$ip = 'localhost';
			if (!(empty($port))) 
			{
				if (empty($sys)) 
				{
					pdo_insert('rr_v_exhelper_sys', array('port' => $port, 'ip' => $ip, 'uniacid' => $_W['uniacid'], 'merchid' => $merchid));
				}
				else 
				{
					pdo_update('rr_v_exhelper_sys', array('port' => $port, 'ip' => $ip), array('uniacid' => $_W['uniacid'], 'merchid' => $merchid));
				}
				plog('merch.exhelper.printset.edit', '修改打印机端口 原端口: ' . $sys['port'] . ' 新端口: ' . $port);
				show_json(1);
			}
		}
		include $this->template();
	}
}
?>