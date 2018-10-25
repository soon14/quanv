<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class List_RrvV3Page extends WebPage
{
	protected function videosData($status, $st)
	{
		global $_W;
		global $_GPC;

		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		if($st == 'isshow1'){
			$condition = ' f.isshow = 1';
		}else if($st == 'isshow0'){
			$condition = ' f.isshow = 0 ';
		}else{
			$condition = ' f.status = "'.$status.'" ';
		}

		if (!(empty($_GPC['keyword']))) 
		{	
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and ( f.videoname like :keyword or f.content like :keyword)';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';		
		}
		$condition .= ' and f.uniacid=:uniacid';
		$params = array(':uniacid' => $_W['uniacid']);

		$join = '';
		$join .= ' join ' . tablename('rr_v_member_videos') . ' f on f.openid=dm.openid';
		$sql = 'select dm.realname,dm.mobile,dm.nickname,dm.avatar,f.* from ' . tablename('rr_v_member') . ' dm ' . $join . ' where  ' . $condition . '  ORDER BY videoname,id DESC';
		$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		$list = pdo_fetchall($sql, $params);
		foreach ($list as &$row) {
			$row['createtime'] = date('Y-m-d H:i:s', $row['createtime']);
		}
		unset($row);
		$total = pdo_fetchcolumn('select count(*) from' . tablename('rr_v_member') . ' dm ' . $join . ' where  ' . $condition . ' ', $params);
		$pager = pagination2($total, $pindex, $psize);
		
		include $this->template('videos/list');
	}

	public function status0()
	{
		global $_W;
		global $_GPC;
		$videosData = $this->videosData(0, 'status0');
	}

	public function status1()
	{
		global $_W;
		global $_GPC;
		$videosData = $this->videosData(1, 'status1');
	}

	public function status2()
	{
		global $_W;
		global $_GPC;
		$videosData = $this->videosData(2, 'status2');
	}

	public function status3()
	{
		global $_W;
		global $_GPC;
		$videosData = $this->videosData(3, 'status3');
	}

	public function isshow0()
	{
		global $_W;
		global $_GPC;
		$videosData = $this->videosData(0, 'isshow0');
	}

	public function isshow1()
	{
		global $_W;
		global $_GPC;
		$videosData = $this->videosData(1, 'isshow1');
	}

	public function enabled()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		$items = pdo_fetchall('SELECT * FROM ' . tablename('rr_v_member_videos') . ' WHERE id = ' . $id . ' AND uniacid=' . $_W['uniacid']);
		if(empty($items)){
			show_json(0,'无该条数据或已删除！');
		}
		else{
			pdo_update('rr_v_member_videos', array('status' => intval($_GPC['status']), 'audittime' => time()), array('id' => $id));
			plog('videos.list.enabled', '修改视频审核状态<br/>ID: ' . $items['id'] . '<br/>视频名称: ' . $items['videoname']);

			show_json(1, array('url' => referer()));
		}

	}

	public function ajaxgettotals()
	{
		global $_GPC;
		global $_W;
		$paras = array(':uniacid' => $_W['uniacid']);

		$totals['all'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('rr_v_member_videos') . '' . ' WHERE uniacid = :uniacid and status <>3', $paras);
		$totals['status0'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('rr_v_member_videos') . '' . ' WHERE uniacid = :uniacid and status=0 ', $paras);
		$totals['status1'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('rr_v_member_videos') . '' . ' WHERE uniacid = :uniacid and status=1 ', $paras);
		$totals['status2'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('rr_v_member_videos') . '' . ' WHERE uniacid = :uniacid and status=2 ', $paras);
		$totals['status3'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('rr_v_member_videos') . '' . ' WHERE uniacid = :uniacid and status=3 ', $paras);
		$totals['isshow0'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('rr_v_member_videos') . '' . ' WHERE uniacid = :uniacid and isshow=0 ', $paras);
		$totals['isshow1'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('rr_v_member_videos') . '' . ' WHERE uniacid = :uniacid and isshow=1 ', $paras);

		$result = (empty($totals) ? array() : $totals);
		show_json(1, $result);
	}

	//修改审核不过的原因
	public function checked(){
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$checked = $_GPC['checked'];
		$videoname = $_GPC['videoname'];

		if ($_W['ispost']) {
			pdo_update('rr_v_member_videos', array('checked' => $checked,'status' => 2), array('id' => $id, 'uniacid' => $_W['uniacid']));
		
			show_json(1);
		}

		include $this->template();
	}

}

?>
