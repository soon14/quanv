<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class News_RrvV3Page extends WebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;

		$condition = ' uniacid=:uniacid and isdelete=0 and start_time > "'.date('Y/m/d H:i').'" ';
		$params = array(':uniacid' => $uniacid);
		if (!(empty($_GPC['keyword']))) 
		{	
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and week like :keyword or price like :keyword or address like :keyword ';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';		
		}

		$sql = 'select * from ' . tablename('rr_v_consult') . ' where  ' . $condition . '  ORDER BY start_time';
		$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		$list = pdo_fetchall($sql, $params);
		foreach ($list as &$row) {
			$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
			$row['date'] = explode(' ', $row['start_time'])[0];
			$row['start_time'] = explode(' ', $row['start_time'])[1];
			$row['end_time'] = explode(' ', $row['end_time'])[1];
			$row['consult_nums'] = pdo_fetchcolumn('select count(*) from' . tablename('rr_v_consult_log') . ' where uniacid=:uniacid and status=1 and consultid=:consultid ', array(':uniacid' => $uniacid, ':consultid' => $row['id']));
			if(empty($row['consult_nums'])){
				$row['consult_nums'] = 0;
			}
		}
		unset($row);
		$total = pdo_fetchcolumn('select count(*) from' . tablename('rr_v_consult') . ' where  ' . $condition . ' ', $params);
		$pager = pagination2($total, $pindex, $psize);

	
		include $this->template();
	}


}

?>
