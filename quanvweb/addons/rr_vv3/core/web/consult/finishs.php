<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Finishs_RrvV3Page extends WebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;

		$condition = ' f.uniacid=:uniacid and f.confirmtime>0 and f.openid=dm.openid';
		$params = array(':uniacid' => $uniacid);
		if (!(empty($_GPC['keyword']))) 
		{	
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and f.name like :keyword or dm.nickname like :keyword or f.number like :keyword or f.remarks like :keyword ';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';		
		}

		$sql = 'select dm.nickname,dm.avatar,f.* from ' . tablename('rr_v_member') . ' dm,' . tablename('rr_v_consult_log') . ' f where  ' . $condition . '  ORDER BY f.id DESC';
		$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		$list = pdo_fetchall($sql, $params);
		foreach ($list as &$row) {
			$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
			if($row['departmentid'] !=0 ){
				$res = pdo_fetch('select id,name from' . tablename('rr_v_member_department') . ' where uniacid=:uniacid and id=:id ', array(':uniacid' => $uniacid, ':id' => $row['departmentid']));
				$row['department'] = $res['name'];
			}else{
				$row['department'] = '';
			}
		}
		unset($row);
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('rr_v_member') . ' dm,' . tablename('rr_v_consult_log') . ' f where  ' . $condition . ' ', $params);
		$pager = pagination2($total, $pindex, $psize);

		
		include $this->template();
	}

	public function finishdetail()
	{
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];
		$id = $_GPC['id'];
		if(empty($id)){
			show_json(0,'无该条数据！');
		}else{
			$sql = 'select m.nickname,m.avatar,a.* from '.tablename('rr_v_member'). 'm,' .tablename('rr_v_consult_log'). 'a where a.uniacid = :uniacid and m.openid = a.openid and a.id = :id and confirmtime > 0';
			$params = array(':uniacid' => $uniacid,':id' => $id);
			$info = pdo_fetch($sql,$params);
			$info['createtime'] = date('Y-m-d H:i:s', $info['createtime']);
			$info['confirmtime'] = date('Y-m-d H:i:s', $info['confirmtime']);

			$list = pdo_fetch('select start_time,end_time,week,address from' . tablename('rr_v_consult') . ' where uniacid=:uniacid and id=:id ', array(':uniacid' => $uniacid, ':id' => $info['consultid']));
			$info['date'] = explode(' ', $list['start_time'])[0];
			$info['start_time'] = explode(' ', $list['start_time'])[1];
			$info['end_time'] = explode(' ', $list['end_time'])[1];
			$info['week'] = $list['week'];
			$info['address'] = $list['address'];

			//获取支付
			$order = pdo_fetch('select a.orderid,a.goodsid,b.ordersn,b.price,a.goodstype,b.paytime from ' . tablename('rr_v_order_goods') . ' a,' . tablename('rr_v_order') . ' b where a.uniacid =:uniacid AND a.orderid=b.id AND a.goodsid =:goodsid
								AND a.openid =:openid AND b.status=3 AND paytype = 21 ', array(':uniacid' => $uniacid, ':openid' => $info['openid'], ':goodsid' => $info['consultid']));
			$info['price'] = $order['price'];
			$info['paytime'] = date('Y-m-d H:i:s', $order['paytime']);
		}
		

		include $this->template('consult/finishdetail');

	}
}

?>
