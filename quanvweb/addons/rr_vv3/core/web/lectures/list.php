<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class List_RrvV3Page extends WebPage
{
	protected function lecturesData($status, $st)
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		if($st == 'isshow1'){
			$condition = ' f.isshow = 1';
		}else if($st == 'isshow0'){
			$condition = ' f.isshow = 0 and f.status != 3';
		}else{
			$condition = ' f.status = "'.$status.'" and f.isshow =0';
		}

		if (!(empty($_GPC['keyword']))) 
		{	
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and ( f.article_title like :keyword or f.article_content like :keyword or dm.nickname like :keyword or dm.realname like :keyword)';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';		
		}
		$condition .= ' and dm.uniacid=:uniacid';
		$params = array(':uniacid' => $_W['uniacid']);

		$join = '';
		$join .= ' join ' . tablename('rr_v_lectures') . ' f on f.openid=dm.openid';
		$sql = 'select dm.realname,dm.mobile,dm.nickname,dm.avatar,f.* from ' . tablename('rr_v_member') . ' dm ' . $join . ' where  ' . $condition . '  ORDER BY id DESC';
		$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		$list = pdo_fetchall($sql, $params);
		$total = pdo_fetchcolumn('select count(*) from' . tablename('rr_v_member') . ' dm ' . $join . ' where  ' . $condition . ' ', $params);
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->template('lectures/list');
	}

	public function status0()
	{
		global $_W;
		global $_GPC;
		$lecturesData = $this->lecturesData(0, 'status0');
	}

	public function status1()
	{
		global $_W;
		global $_GPC;
		$lecturesData = $this->lecturesData(1, 'status1');
	}

	public function status2()
	{
		global $_W;
		global $_GPC;
		$lecturesData = $this->lecturesData(2, 'status2');
	}

	public function isshow1()
	{
		global $_W;
		global $_GPC;
		$lecturesData = $this->lecturesData(1, 'isshow1');
	}

	public function isshow0()
	{
		global $_W;
		global $_GPC;
		$lecturesData = $this->lecturesData(0, 'isshow0');
	}

	public function status3()
	{
		global $_W;
		global $_GPC;
		$lecturesData = $this->lecturesData(3, 'status3');
	}

	public function ajaxGetTotals()
	{
		global $_W;
		global $_GPC;
		$params = array(':uniacid' => $_W['uniacid']);
		$totals['all'] = pdo_fetchcolumn('select count(*) from '.tablename('rr_v_lectures').' where uniacid = :uniacid and status!=3 ',$params);
		$totals['status0'] = pdo_fetchcolumn('select count(*) from '.tablename('rr_v_lectures').' where uniacid = :uniacid and status=0 and isshow=0 ',$params);
		$totals['status1'] = pdo_fetchcolumn('select count(*) from '.tablename('rr_v_lectures').' where uniacid = :uniacid and status=1 and isshow=0',$params);
		$totals['status2'] = pdo_fetchcolumn('select count(*) from '.tablename('rr_v_lectures').' where uniacid = :uniacid and status=2 and isshow=0',$params);
		$totals['status3'] = pdo_fetchcolumn('select count(*) from '.tablename('rr_v_lectures').' where uniacid = :uniacid and status=3 and isshow=0',$params);
		$totals['isshow0'] = pdo_fetchcolumn('select count(*) from '.tablename('rr_v_lectures').' where uniacid = :uniacid and isshow=0 and status !=3',$params);
		$totals['isshow1'] = pdo_fetchcolumn('select count(*) from '.tablename('rr_v_lectures').' where uniacid = :uniacid and isshow=1',$params);
		$result = (empty($totals) ? array() : $totals);
		show_json(1,$result);
	}
	

}

?>
