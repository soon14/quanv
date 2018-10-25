<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Doctorsbill_RrvV3Page extends WebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];

		//查询时间段
		$firstDate = strtotime(date('Y-01-01'));
		// $lastDate = strtotime(date('Y-m-t',strtotime('-1 month')));
		$lastDate = strtotime(date('Y-m-d'));

		$list = array();

		$start_date = $firstDate;
		$end_date = $lastDate;


		//各类别消费情况
		$condition = ' where a.uniacid =:uniacid AND b.status = 3 AND b.paytype = 21 AND b.paytime >'.$start_date.' AND b.paytime <'.$end_date.' ';
		$group = ' ORDER BY b.paytime';
		$params = array(':uniacid' => $uniacid);

		$join = ' LEFT JOIN ' . tablename('rr_v_order') . ' b ON a.orderid = b.id AND a.openid = b.openid';

		$sql = 'SELECT m.openid,m.realname,m.nickname,m.avatar,a.optionname,b.price,a.goodstype,b.paytime FROM ' . tablename('rr_v_order_goods') . ' a';
		
		//1、图文咨询
		$join1 .= ' LEFT JOIN ' . tablename('rr_v_member_patients_diag') . ' c ON a.goodsid = c.id AND a.orderid = c.orderid';
		$join1 .= ' LEFT JOIN ' . tablename('rr_v_member_doctors') . ' d ON c.doctorid = d.id';
		$join1 .= ' LEFT JOIN ' . tablename('rr_v_member') . ' m ON d.openid = m.openid';

		$condition1 = ' AND a.goodstype = 1 AND c.orderid>0 AND c.doctorid >0';
		
		$imgtext_list = pdo_fetchall($sql . $join . $join1 . $condition . $condition1 . $group, $params);

		//2、视频播放
		$join2 .= ' LEFT JOIN ' . tablename('rr_v_member_videos') . ' c ON a.goodsid = c.id';
		$join2 .= ' LEFT JOIN ' . tablename('rr_v_member') . ' m ON c.openid = m.openid';

		$condition2 = ' AND a.goodstype = 2 AND c.status = 1 AND c.isshow = 1';

		$videos_list = pdo_fetchall($sql . $join . $join2 . $condition . $condition2 . $group, $params);

		//3、文章查阅
		$join3 .= ' LEFT JOIN ' . tablename('rr_v_articles') . ' c ON a.goodsid = c.id';
		$join3 .= ' LEFT JOIN ' . tablename('rr_v_member') . ' m ON c.openid = m.openid';
		
		$condition3 = ' AND a.goodstype = 3 AND c.status = 2 AND c.isshow = 1';

		$articles_list = pdo_fetchall($sql . $join . $join3 . $condition . $condition3 . $group, $params);

		//4、讲座报名
		$join4 .= ' LEFT JOIN ' . tablename('rr_v_lectures_log') . ' c ON a.goodsid = c.id AND a.openid = c.openid';
		$join4 .= ' LEFT JOIN ' . tablename('rr_v_lectures') . ' d ON c.lid = d.id';
		$join4 .= ' LEFT JOIN ' . tablename('rr_v_member') . ' m ON d.openid = m.openid';
		
		$condition4 = ' AND a.goodstype = 4 AND c.ordernumber<>"" ';

		$lectures_list = pdo_fetchall($sql . $join . $join4 . $condition . $condition4 . $group, $params);

		//5、当面咨询
		$join5 .= ' LEFT JOIN ' . tablename('rr_v_consult') . ' c ON a.goodsid = c.id';
		$join5 .= ' LEFT JOIN ' . tablename('rr_v_member') . ' m ON c.openid = m.openid';
		
		$condition5 = ' AND a.goodstype = 5 AND c.isdelete = 0 ';

		$consult_list = pdo_fetchall($sql . $join . $join5 . $condition . $condition5 . $group, $params);

		//6、送心意
		$join6 .= ' LEFT JOIN ' . tablename('rr_v_member_doctors_reward') . ' c ON a.goodsid = c.id AND a.orderid = c.orderid';
		$join6 .= ' LEFT JOIN ' . tablename('rr_v_member') . ' m ON c.doc_openid = m.openid';
		
		$condition6 = ' AND a.goodstype = 6 AND c.orderid > 0 ';
		

		$reward_list = pdo_fetchall($sql . $join . $join6 . $condition . $condition6 . $group, $params);

		//合并数组
		$list = array_merge($imgtext_list, $videos_list, $articles_list, $lectures_list, $consult_list, $reward_list);

		if(!empty($list)){
			// $arr = array(
			// 	array('optionname' =>'图文咨询', 'totalprice' =>0, 'goodstype' =>1),
			// 	array('optionname' =>'视频播放', 'totalprice' =>0, 'goodstype' =>2),
			// 	array('optionname' =>'文章查阅', 'totalprice' =>0, 'goodstype' =>3),
			// 	array('optionname' =>'讲座报名', 'totalprice' =>0, 'goodstype' =>4),
			// 	array('optionname' =>'当面咨询', 'totalprice' =>0, 'goodstype' =>5),
			// 	array('optionname' =>'送心意', 'totalprice' =>0, 'goodstype' =>6),
			// );

			//去除数组重复
			foreach ($list as &$value) {
				$openid = $value['openid'];
				$arr[$openid] = isset($arr[$openid])?
				($value['openid'] != $arr[$openid]['openid'])? $value : $arr[$openid] : $value;
			}
			unset($value);

			$arr=array_values($arr);
			foreach ($arr as &$value2) {
				if(!empty($value2['openid'])){
					$value2['billprice'] = 0; 
					$value2['date'] = '';
					$value2['datetime'] = 0;

					if($start_date <= $end_date){
						for($i = strtotime(date('Y-m',$start_date)); $i <= strtotime(date('Y-m',$end_date)); $i = strtotime(date('Y-m',strtotime('+1 month',$i)))){
							$value2['billprice'] = 0;
							foreach ($list as &$row) {

								if(strtotime(date('Y-m',$row['paytime'])) == $i){

									if($row['openid'] == $value2['openid']){
										$value2['billprice'] = $value2['billprice']*1 + $row['price']*1;
									}
									$value2['date'] = date('Y年m月',$i);
									$value2['datetime'] = $i;
								}
								
							}
							unset($row);

							if($value2['billprice'] != 0){
								$res_arr[] = $value2;
							}
							
						}
					}
				}
				
			}
			unset($value2);

			if(!empty($res_arr)){
				foreach ($res_arr as &$value3) {
					$bankcard = pdo_fetch('SELECT * FROM ' . tablename('rr_v_member_bankcard') . ' where uniacid=:uniacid 
						and openid=:openid limit 1', array(':uniacid' => $uniacid, ':openid' => $value3['openid']));
					if(!empty($bankcard)){
						$value3['bankname'] = $bankcard['bankname'];
						$value3['bankcard'] = $bankcard['bankcard'];
						$value3['cardname'] = $bankcard['username'];
						$value3['mobile'] = $bankcard['mobile'];
					}

					$bill = pdo_fetch('SELECT * FROM ' . tablename('rr_v_member_doctors_bill') . ' where uniacid=:uniacid 
						and openid=:openid and tradedate=:tradedate limit 1', array(':uniacid' => $uniacid, ':openid' => $value3['openid'], ':tradedate' => $value3['date']));
					if(!empty($bill)){
						$value3['isbill'] = $bill['status'];
					}else{
						$value3['isbill'] = 0;
					}
				}

			}
			$result = $res_arr;

		}
		
		include $this->template();
	}

	public function addBill()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$openid = trim($_GPC['openid']);
		$billprice = trim($_GPC['billprice']);
		$tradedate = trim($_GPC['tradedate']);
		$status = intval($_GPC['status']);

		$param = array(':uniacid' => $uniacid, ':openid' => $openid, ':tradedate' => $tradedate);
		$items = pdo_fetch('SELECT * FROM ' . tablename('rr_v_member_doctors_bill') . ' WHERE uniacid=:uniacid and openid=:openid and tradedate=:tradedate limit 1', $param);
		
		$data = array(
			'uniacid' 	=> $uniacid,
			'openid' 	=> $openid,
			'billprice' => $billprice,
			'tradedate' => $tradedate,
			'status' 	=> $status,
			'tradetime' => time(),
		);

		if(empty($items)){
			pdo_insert('rr_v_member_doctors_bill', $data);
		}
		

		show_json(1, array('url' => referer()));

	}


}

?>
