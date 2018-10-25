<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_RrvV3Page extends WebPage
{
	public function main()
	{
		if (cv('consult.news.view')) {
			header('location: ' . webUrl('consult/news'));
		}
		else if (cv('consult.waits.view')) {
			header('location: ' . webUrl('consult/waits'));
		}
		else if (cv('consult.finishs.view')) {
			header('location: ' . webUrl('consult/finishs'));
		}
		else if (cv('consult.historys.view')) {
			header('location:' . webUrl('consult/historys'));
		}
		else if (cv('consult.all.view')) {
			header('location:' . webUrl('consult/all'));
		}
		else {
			header('location: ' . webUrl('consult/all'));
		}
	}

	public function detail()
	{
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];
		$id = $_GPC['id'];
		if(empty($id)){
			show_json(0,'无该条数据！');
		}else{
			$sql = 'select dm.realname,dm.nickname,dm.avatar,f.* from '.tablename('rr_v_member'). 'dm,' .tablename('rr_v_consult'). 'f where f.uniacid = :uniacid and dm.openid = f.openid and f.id = :id';
			$params = array(':uniacid' => $uniacid,':id' => $id);
			$info = pdo_fetch($sql,$params);
			$info['createtime'] = date('Y-m-d H:i:s', $info['createtime']);
			$info['consult_nums'] = pdo_fetchcolumn('select count(*) from' . tablename('rr_v_consult_log') . ' where uniacid=:uniacid and status=1 and consultid=:consultid ', array(':uniacid' => $uniacid, ':consultid' => $info['id']));
			if(empty($info['consult_nums'])){
				$info['consult_nums'] = 0;
			}
			$member = m('member')->getMember($info['openid']);
		}
		

		include $this->template();

	}

	public function ajaxgettotals()
	{
		global $_GPC;
		global $_W;
		$paras = array(':uniacid' => $_W['uniacid']);

		$totals['all'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('rr_v_consult') . '' . ' WHERE uniacid = :uniacid and isdelete=0', $paras);
		$totals['news'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('rr_v_consult') . '' . ' WHERE uniacid = :uniacid and isdelete=0 and start_time > "'.date('Y/m/d H:i').'" ', $paras);
		$totals['waits'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('rr_v_consult_log') . '' . ' WHERE uniacid = :uniacid and confirmtime=0 and ordernumber<>"" ', $paras);
		$totals['finishs'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('rr_v_consult_log') . '' . ' WHERE uniacid = :uniacid and confirmtime>0 ', $paras);
		$totals['historys'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('rr_v_consult') . '' . ' WHERE uniacid = :uniacid and isdelete=0 and start_time < "'.date('Y/m/d H:i').'" ', $paras);

		$result = (empty($totals) ? array() : $totals);
		show_json(1, $result);
	}
}

?>
