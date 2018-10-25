<?php
class Express_RrvV3Model
{
	/**
     * 获取快递列表
     */
	public function getExpressList()
	{
		global $_W;
		$sql = 'select * from ' . tablename('rr_v_express') . ' where status=1 order by displayorder desc,id asc';
		$data = pdo_fetchall($sql);
		return $data;
	}
}

if (!defined('IN_IA')) {
	exit('Access Denied');
}

?>
