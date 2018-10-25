<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}
 
require rr_vv3_PLUGIN . 'app/core/page_mobile.php';
class Moneybag_RrvV3Page extends AppMobilePage
{	
	//医生端我的钱包
	public function doc_moneybag()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];

		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}
		$list = array();

		//当月计算
		$firstDay = date('Y-m-01 H:i:s');
		$lastDay = date('Y-m-t H:i:s');

		$start_date = strtotime($firstDay);
		$end_date = strtotime($lastDay);
		//各类别消费情况
		$condition = ' a.uniacid =:uniacid AND a.orderid = b.id AND a.openid = b.openid AND b.status = 3 AND b.paytype = 21 AND b.paytime >'.$start_date.' AND b.paytime <'.$end_date.' ';
		$group = 'GROUP BY a.optionname,a.goodstype ORDER BY a.optionname';
		$params = array(':uniacid' => $uniacid, ':openid' => $openid);

		$sql = 'SELECT a.optionname,SUM(a.price) totalprice,a.goodstype FROM ' . tablename('rr_v_order_goods') . ' a,' . tablename('rr_v_order') . ' b WHERE ';
		
		//1、图文咨询
		$goodstype1 = ' AND a.goodstype = 1 AND a.goodsid IN(SELECT id FROM ' . tablename('rr_v_member_patients_diag') . ' WHERE uniacid =:uniacid AND orderid>0 AND doctorid >0
						AND doctorid IN(SELECT id FROM ' . tablename('rr_v_member_doctors') . ' WHERE uniacid =:uniacid AND openid =:openid)) ';
		$imgtext_list = pdo_fetch($sql . $condition . $goodstype1 . $group, $params);
		if(empty($imgtext_list)){
			$imgtext_list = array('optionname' =>'图文咨询', 'totalprice' =>'0.00', 'goodstype' =>'1');
		}

		//2、视频播放
		$goodstype2 = ' AND a.goodstype = 2 AND a.goodsid IN(SELECT id FROM ' . tablename('rr_v_member_videos') . ' WHERE uniacid =:uniacid AND openid =:openid AND status = 1 AND isshow = 1) ';
		$videos_list = pdo_fetch($sql . $condition . $goodstype2 . $group, $params);
		if(empty($videos_list)){
			$videos_list = array('optionname' =>'视频播放', 'totalprice' =>'0.00', 'goodstype' =>'2');
		}

		//3、文章查阅
		$goodstype3 = ' AND a.goodstype = 3 AND a.goodsid IN(SELECT id FROM ' . tablename('rr_v_articles') . ' WHERE uniacid =:uniacid AND openid =:openid AND status = 2 AND isshow = 1) ';
		$articles_list = pdo_fetch($sql . $condition . $goodstype3 . $group, $params);
		if(empty($articles_list)){
			$articles_list = array('optionname' =>'文章查阅', 'totalprice' =>'0.00', 'goodstype' =>'3');
		}

		//4、讲座报名
		$goodstype4 = ' AND a.goodstype = 4 AND a.goodsid IN(SELECT b.id FROM ' . tablename('rr_v_lectures') . ' a,' . tablename('rr_v_lectures_log') . ' b WHERE a.uniacid =:uniacid AND a.id=b.lid AND b.ordernumber<>"" AND a.openid =:openid) ';
		$lectures_list = pdo_fetch($sql . $condition . $goodstype4 . $group, $params);
		if(empty($lectures_list)){
			$lectures_list = array('optionname' =>'讲座报名', 'totalprice' =>'0.00', 'goodstype' =>'4');
		}

		//5、当面咨询
		$goodstype5 = ' AND a.goodstype = 5 AND a.goodsid IN(SELECT id FROM ' . tablename('rr_v_consult') . ' WHERE uniacid =:uniacid AND openid =:openid AND isdelete = 0) ';
		$consult_list = pdo_fetch($sql . $condition . $goodstype5 . $group, $params);
		if(empty($consult_list)){
			$consult_list = array('optionname' =>'当面咨询', 'totalprice' =>'0.00', 'goodstype' =>'5');
		}

		$shop = m('common')->getSysset('shop');//平台抽成
		if(empty($shop['drawsprice'])){
			$drawsprice = 0;
		}else{
			$drawsprice = $shop['drawsprice'];
		}

		//6、送心意
		$goodstype6 = ' AND a.goodstype = 6 AND a.goodsid IN(SELECT id FROM ' . tablename('rr_v_member_doctors_reward') . ' WHERE uniacid =:uniacid AND doc_openid =:openid AND orderid > 0) ';
		$reward_list = pdo_fetch($sql . $condition . $goodstype6 . $group, $params);
		if(empty($reward_list)){
			$reward_list = array('optionname' =>'送心意', 'totalprice' =>'0.00', 'goodstype' =>'6');
		}else{
			$reward_list['totalprice'] = $reward_list['totalprice']*((100 - $drawsprice)/100);
		}

		//合并数组
		array_push($list,$imgtext_list, $videos_list, $articles_list, $lectures_list, $consult_list, $reward_list);

		//总金额
		$totalPrice['totalmoney'] = $imgtext_list['totalprice']*1 + $videos_list['totalprice']*1 + $articles_list['totalprice']*1 + $lectures_list['totalprice']*1 + $consult_list['totalprice']*1 + $reward_list['totalprice']*1;
		
		//记录总金额，更新我的余额
		$trade_log = pdo_fetch('SELECT * FROM ' . tablename('rr_v_member_trade') . ' WHERE uniacid =:uniacid AND openid =:openid ', $params);
		$member_tradelog = pdo_fetch('SELECT id,openid,credit2 FROM ' . tablename('rr_v_member') . ' WHERE uniacid =:uniacid AND openid =:openid ', $params);
		if(empty($trade_log)){
			pdo_insert('rr_v_member_trade', array('uniacid' => $uniacid, 'openid' => $openid, 'self_ordermoney' => $totalPrice['totalmoney'], 'updatetime' => time()));
			if($member_tradelog['credit2'] = '0.00'){
				pdo_update('rr_v_member', array('credit2' => $totalPrice['totalmoney']), array('uniacid' => $uniacid, 'openid' => $openid));
			}
		}else{
			if($trade_log['self_ordermoney']*1 < $totalPrice['totalmoney']*1 && $trade_log['updatetime'] < time()){
				pdo_update('rr_v_member', array('credit2' => $member_tradelog['credit2']*1 + $totalPrice['totalmoney']*1 - $trade_log['self_ordermoney']*1), array('uniacid' => $uniacid, 'openid' => $openid));
				pdo_update('rr_v_member_trade', array('self_ordermoney' => $totalPrice['totalmoney'], 'updatetime' => time()), array('uniacid' => $uniacid, 'openid' => $openid));
			}
		}

		$member = m('member')->getMember($openid);

		app_json(array('list' => $list, 'totalmoney' => $totalPrice, 'credit2' => $member['credit2'], 'date' => date('Y年m月')));

	}

	//医生端我的钱包历史明细
	public function doc_moneybag_list()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];

		//查询时间段
		$firstDate = trim($param['firstDate']);
		$lastDate = trim($param['lastDate']);

		if (empty($param['openid']) || empty($firstDate) || empty($lastDate)) {
			app_error(AppError::$ParamsError);
		}
		$list = array();

		$start_date = strtotime($firstDate.' 00:00:00');
		$end_date = strtotime($lastDate.' 23:59:59');
		//各类别消费情况
		$condition = ' a.uniacid =:uniacid AND a.orderid = b.id AND a.openid = b.openid AND b.status = 3 AND b.paytype = 21 AND b.paytime >'.$start_date.' AND b.paytime <'.$end_date.' ';
		$group = ' ORDER BY b.paytime';
		$params = array(':uniacid' => $uniacid, ':openid' => $openid);

		$sql = 'SELECT a.optionname,a.price,a.goodstype,b.paytime FROM ' . tablename('rr_v_order_goods') . ' a,' . tablename('rr_v_order') . ' b WHERE ';
		
		//1、图文咨询
		$goodstype1 = ' AND a.goodstype = 1 AND a.goodsid IN(SELECT id FROM ' . tablename('rr_v_member_patients_diag') . ' WHERE uniacid =:uniacid AND orderid>0 AND doctorid >0
						AND doctorid IN(SELECT id FROM ' . tablename('rr_v_member_doctors') . ' WHERE uniacid =:uniacid AND openid =:openid)) ';
		$imgtext_list = pdo_fetchall($sql . $condition . $goodstype1 . $group, $params);
		if(empty($imgtext_list)){
			$imgtext_list[] = array('optionname' =>'图文咨询', 'totalprice' =>0, 'goodstype' =>1, 'paytime' =>0);
		}

		//2、视频播放
		$goodstype2 = ' AND a.goodstype = 2 AND a.goodsid IN(SELECT id FROM ' . tablename('rr_v_member_videos') . ' WHERE uniacid =:uniacid AND openid =:openid AND status = 1 AND isshow = 1) ';
		$videos_list = pdo_fetchall($sql . $condition . $goodstype2 . $group, $params);
		if(empty($videos_list)){
			$videos_list[] = array('optionname' =>'视频播放', 'totalprice' =>0, 'goodstype' =>2, 'paytime' =>0);
		}

		//3、文章查阅
		$goodstype3 = ' AND a.goodstype = 3 AND a.goodsid IN(SELECT id FROM ' . tablename('rr_v_articles') . ' WHERE uniacid =:uniacid AND openid =:openid AND status = 2 AND isshow = 1) ';
		$articles_list = pdo_fetchall($sql . $condition . $goodstype3 . $group, $params);
		if(empty($articles_list)){
			$articles_list[] = array('optionname' =>'文章查阅', 'totalprice' =>0, 'goodstype' =>3, 'paytime' =>0);
		}

		//4、讲座报名
		$goodstype4 = ' AND a.goodstype = 4 AND a.goodsid IN(SELECT b.id FROM ' . tablename('rr_v_lectures') . ' a,' . tablename('rr_v_lectures_log') . ' b WHERE a.uniacid =:uniacid AND a.id=b.lid AND b.ordernumber<>"" AND a.openid =:openid) ';
		$lectures_list = pdo_fetchall($sql . $condition . $goodstype4 . $group, $params);
		if(empty($lectures_list)){
			$lectures_list[] = array('optionname' =>'讲座报名', 'totalprice' =>0, 'goodstype' =>4, 'paytime' =>0);
		}

		//5、当面咨询
		$goodstype5 = ' AND a.goodstype = 5 AND a.goodsid IN(SELECT id FROM ' . tablename('rr_v_consult') . ' WHERE uniacid =:uniacid AND openid =:openid AND isdelete = 0) ';
		$consult_list = pdo_fetchall($sql . $condition . $goodstype5 . $group, $params);
		if(empty($consult_list)){
			$consult_list[] = array('optionname' =>'当面咨询', 'totalprice' =>0, 'goodstype' =>5, 'paytime' =>0);
		}

		$shop = m('common')->getSysset('shop');//平台抽成
		if(empty($shop['drawsprice'])){
			$drawsprice = 0;
		}else{
			$drawsprice = $shop['drawsprice'];
		}

		//6、送心意
		$goodstype6 = ' AND a.goodstype = 6 AND a.goodsid IN(SELECT id FROM ' . tablename('rr_v_member_doctors_reward') . ' WHERE uniacid =:uniacid AND doc_openid =:openid AND orderid > 0) ';
		$reward_list = pdo_fetchall($sql . $condition . $goodstype6 . $group, $params);
		if(empty($reward_list)){
			$reward_list = array('optionname' =>'送心意', 'totalprice' =>0, 'goodstype' =>6, 'paytime' =>0);
		}else{
			foreach ($reward_list as &$values) {
				$values['totalprice'] = $values['price']*((100 - $drawsprice)/100);
			}
			unset($values);
		}

		//合并数组
		$list = array_merge($imgtext_list, $videos_list, $articles_list, $lectures_list, $consult_list, $reward_list);

		$res_arr = array();
		if(!empty($list)){

			$arr = array(
				array('optionname' =>'图文咨询', 'totalprice' =>0, 'goodstype' =>1),
				array('optionname' =>'视频播放', 'totalprice' =>0, 'goodstype' =>2),
				array('optionname' =>'文章查阅', 'totalprice' =>0, 'goodstype' =>3),
				array('optionname' =>'讲座报名', 'totalprice' =>0, 'goodstype' =>4),
				array('optionname' =>'当面咨询', 'totalprice' =>0, 'goodstype' =>5),
				array('optionname' =>'送心意', 'totalprice' =>0, 'goodstype' =>6),
			);

			if($start_date <= $end_date){
				for($i = strtotime(date('Y-m',$start_date)); $i <= strtotime(date('Y-m',$end_date)); $i = strtotime(date('Y-m',strtotime('+1 month',$i)))){
					foreach ($list as &$row) {
						foreach ($arr as &$value) {
							
							if(strtotime(date('Y-m',$row['paytime'])) == $i && $row['goodstype'] == $value['goodstype']){

								$value['totalprice'] = $value['totalprice'] + $row['price'];
							}
							
						}
						unset($value);
						$res_arr['list'] = $arr;
						$res_arr['date'] = date('Y年m月',$i);
					}
					foreach ($arr as &$val) {
						if($val['totalprice'] != 0){
							$totalmoney = $totalmoney + $val['totalprice'];
							$res_arr['totalmoney'] = $totalmoney;
						}
					}
					unset($val);
					$result[] = $res_arr;
				}
				
			}
		}

		app_json(array('res_arr' => $result));

	}

	//医生端我的钱包订单明细
	public function doc_moneybag_order()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$pageSize = intval($param['pageSize']);
		$page = intval(($param['page'])-1) * $pageSize;
		$limit = ' limit '.$page.','.$pageSize.' ';

		$goodstype = intval($param['goodstype']);

		$date = trim($param['date']);

		if (empty($param['openid']) || empty($param['pageSize']) || empty($param['page']) || empty($goodstype) || empty($date)) {
			app_error(AppError::$ParamsError);
		}

		//按月计算
		$arr = date_parse_from_format('Y年m月',$date);

		if($arr['month'] < 9){
			$arr['month'] = '0'.$arr['month'];
		}
		$firstDay = date($arr['year'].'-'.$arr['month'].'-01 00:00:00');
		$lastDay = date($arr['year'].'-'.$arr['month'].'-t 23:59:59');

		$start_date = strtotime($firstDay);
		$end_date = strtotime($lastDay);

		//各类别消费情况
		$condition = ' a.uniacid =:uniacid AND a.orderid = b.id AND a.openid = b.openid AND b.status = 3 AND b.paytype = 21 AND a.goodstype =:goodstype AND b.paytime >'.$start_date.' AND b.paytime <'.$end_date.' ';
		$group = ' ORDER BY b.paytime '.$limit;
		$params = array(':uniacid' => $uniacid, ':openid' => $openid, ':goodstype' => $goodstype);

		$sql = 'SELECT a.orderid,a.goodsid,a.optionname,b.ordersn,a.price,a.goodstype,b.paytime,b.openid FROM ' . tablename('rr_v_order_goods') . ' a,' . tablename('rr_v_order') . ' b WHERE ';
		
		//1、图文咨询
		if($goodstype == 1){

			$condition .= ' AND a.goodsid IN(SELECT id FROM ' . tablename('rr_v_member_patients_diag') . ' WHERE uniacid =:uniacid AND orderid>0 AND doctorid >0
						AND doctorid IN(SELECT id FROM ' . tablename('rr_v_member_doctors') . ' WHERE uniacid =:uniacid AND openid =:openid)) ';
		
		//2、视频播放	
		}elseif($goodstype == 2){

			$condition .= ' AND a.goodsid IN(SELECT id FROM ' . tablename('rr_v_member_videos') . ' WHERE uniacid =:uniacid AND openid =:openid AND status = 1 AND isshow = 1) ';

		//3、文章查阅
		}elseif($goodstype == 3){

			$condition .= ' AND a.goodsid IN(SELECT id FROM ' . tablename('rr_v_articles') . ' WHERE uniacid =:uniacid AND openid =:openid AND status = 2 AND isshow = 1) ';

		//4、讲座报名
		}elseif($goodstype == 4){

			$condition .= ' AND a.goodsid IN(SELECT b.id FROM ' . tablename('rr_v_lectures') . ' a,' . tablename('rr_v_lectures_log') . ' b WHERE a.uniacid =:uniacid AND a.id=b.lid AND b.ordernumber<>"" AND a.openid =:openid) ';

		//5、当面咨询
		}elseif($goodstype == 5){

			$condition .= ' AND a.goodsid IN(SELECT id FROM ' . tablename('rr_v_consult') . ' WHERE uniacid =:uniacid AND openid =:openid AND isdelete = 0) ';

		//6、送心意
		}elseif($goodstype == 6){

			$condition .= ' AND a.goodsid IN(SELECT id FROM ' . tablename('rr_v_member_doctors_reward') . ' WHERE uniacid =:uniacid AND doc_openid =:openid AND orderid > 0) ';

		}else{
			$condition .= ' AND a.goodstype = 0';
		}
		$list = pdo_fetchall($sql . $condition . $group, $params);

		$member = m('member')->getMember($openid);

		$shop = m('common')->getSysset('shop');//平台抽成
		if(empty($shop['drawsprice'])){
			$drawsprice = 0;
		}else{
			$drawsprice = $shop['drawsprice'];
		}

		if(!empty($list)){
			foreach ($list as &$row) {
				$row['paytime'] = date('m月d日', $row['paytime']);
				$paymember = m('member')->getMember($row['openid']);
				$row['pay_name'] = $paymember['realname'];
				$row['pay_nickname'] = $paymember['nickname'];
				$row['pay_avatar'] = $paymember['avatar'];
				$row['trade_name'] = $member['realname'];
				$row['trade_nickname'] = $member['nickname'];
				$row['trade_avatar'] = $member['avatar'];
				if($row['goodstype'] == 6){
					$row['price'] = $row['price']*((100 - $drawsprice)/100);
				}
			}
			unset($row);
		}

		app_json(array('list' => $list));

	}

	//医生端我的钱包订单明细详情
	public function doc_moneybag_orderdetail()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];

		$orderid = intval($param['orderid']);

		if (empty($param['openid']) || empty($orderid)) {
			app_error(AppError::$ParamsError);
		}

		$condition = ' a.uniacid =:uniacid AND a.orderid = b.id AND a.openid = b.openid AND b.status = 3 AND b.paytype = 21 AND a.orderid =:orderid ';
		$params = array(':uniacid' => $uniacid, ':orderid' => $orderid);

		$sql = 'SELECT a.orderid,a.goodsid,a.optionname,b.ordersn,a.price,a.goodstype,b.paytime,b.openid FROM ' . tablename('rr_v_order_goods') . ' a,' . tablename('rr_v_order') . ' b WHERE '.$condition.' ';
		
		$list = pdo_fetch($sql, $params);

		$member = m('member')->getMember($openid);

		$shop = m('common')->getSysset('shop');//平台抽成
		if(empty($shop['drawsprice'])){
			$drawsprice = 0;
		}else{
			$drawsprice = $shop['drawsprice'];
		}

		if(!empty($list)){
			$list['paytime'] = date('Y-m-d H:i', $list['paytime']);
			$paymember = m('member')->getMember($list['openid']);
			$list['pay_name'] = $paymember['realname'];
			$list['pay_nickname'] = $paymember['nickname'];
			$list['pay_avatar'] = $paymember['avatar'];
			$list['trade_name'] = $member['realname'];
			$list['trade_nickname'] = $member['nickname'];
			$list['trade_avatar'] = $member['avatar'];
			if($list['goodstype'] == 6){
				$list['price'] = $list['price']*((100 - $drawsprice)/100);
			}
		}
		
		app_json(array('list' => $list));

	}

	//患者端我的钱包
	public function pat_moneybag()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];

		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}
		//各类别消费情况
		$condition = ' a.uniacid =:uniacid AND a.orderid = b.id AND a.openid = b.openid AND b.status = 3 ';
		$condition .= '  AND b.paytype = 21 AND a.goodstype IN(1,2,3,4,5) AND a.openid =:openid  ';
		$params = array(':uniacid' => $uniacid, ':openid' => $openid);

		$sql = 'SELECT a.optionname,SUM(b.price) totalprice,a.goodstype FROM ' . tablename('rr_v_order_goods') . ' a,' . tablename('rr_v_order') . ' b WHERE '.$condition.' GROUP BY a.optionname,a.goodstype ORDER BY a.optionname';
		$list = pdo_fetchall($sql, $params);

		//总金额
		$totalPrice = pdo_fetch('SELECT (CASE WHEN SUM(price) IS NULL THEN "0.00" ELSE SUM(price) END) totalmoney FROM ' . tablename('rr_v_order') . ' WHERE uniacid =:uniacid AND STATUS = 3 AND paytype = 21 AND openid =:openid ', $params);

		app_json(array('list' => $list, 'totalmoney' => $totalPrice));

	}

	//患者端我的钱包明细
	public function pat_moneybag_detail()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$pageSize = intval($param['pageSize']);
		$page = intval(($param['page'])-1) * $pageSize;
		$limit = ' limit '.$page.','.$pageSize.' ';

		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}
		//各类别消费情况明细
		$condition = ' a.uniacid =:uniacid AND a.orderid = b.id AND a.openid = b.openid AND b.status = 3 ';
		$condition .= '  AND b.paytype = 21 AND a.goodstype IN(1,2,3,4,5) AND a.openid =:openid  ';
		$params = array(':uniacid' => $uniacid, ':openid' => $openid);

		$sql = 'SELECT a.orderid,a.optionname,b.price,a.goodstype,b.paytime FROM ' . tablename('rr_v_order_goods') . ' a,' . tablename('rr_v_order') . ' b WHERE '.$condition.' ORDER BY b.paytime DESC';
		$sql .= $limit;
		$list = pdo_fetchall($sql, $params);
		foreach ($list as &$row) {
			$row['paytime'] = date('Y-m-d H:i:s', $row['paytime']);
		}
		unset($row);

		app_json(array('list' => $list));

	}

	//患者端我的钱包订单详情
	public function pat_moneybag_orderdetail()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$orderid = intval($param['orderid']);

		if (empty($param['openid']) || empty($orderid)) {
			app_error(AppError::$ParamsError);
		}

		$condition = ' a.uniacid =:uniacid AND a.orderid = b.id AND a.openid = b.openid AND b.status = 3 ';
		$condition .= '  AND b.paytype = 21 AND a.orderid =:orderid AND a.openid =:openid  ';
		$params = array(':uniacid' => $uniacid, ':openid' => $openid, ':orderid' => $orderid);

		$sql = 'SELECT a.optionname,a.goodsid,b.price,a.goodstype,b.ordersn,b.paytime FROM ' . tablename('rr_v_order_goods') . ' a,' . tablename('rr_v_order') . ' b WHERE '.$condition.' ORDER BY b.paytime DESC';

		$list = pdo_fetch($sql, $params);
		if(!empty($list)){
			$list['paytime'] = date('Y-m-d H:i', $list['paytime']);
			if($list['goodstype'] == 1){

				$res_doc = pdo_fetch('SELECT a.orderid,a.doctorid,a.consult_type,b.default_consult,b.highgrade_consult,c.realname,c.nickname,c.avatar FROM ' . tablename('rr_v_member_patients_diag') . ' a,' . tablename('rr_v_member_doctors') . ' b,' . tablename('rr_v_member') . ' c 
									WHERE a.uniacid =:uniacid AND a.isdelete =0 AND a.orderid >0 AND a.doctorid >0 AND a.id =:id AND a.doctorid=b.id AND b.openid=c.openid', array(':uniacid' => $uniacid, ':id' => $list['goodsid']));
				
			}elseif($list['goodstype'] == 2){

				$res_doc = pdo_fetch('SELECT a.videoname,a.money,b.realname,b.nickname,b.avatar FROM ' . tablename('rr_v_member_videos') . ' a,' . tablename('rr_v_member') . ' b 
									WHERE a.uniacid =:uniacid AND a.id =:id AND a.openid=b.openid', array(':uniacid' => $uniacid, ':id' => $list['goodsid']));

			}elseif($list['goodstype'] == 3){

				$res_doc = pdo_fetch('SELECT a.article_title,a.money,b.realname,b.nickname,b.avatar FROM ' . tablename('rr_v_articles') . ' a,' . tablename('rr_v_member') . ' b 
									WHERE a.uniacid =:uniacid AND a.id =:id AND a.openid=b.openid', array(':uniacid' => $uniacid, ':id' => $list['goodsid']));

			}elseif($list['goodstype'] == 4){

				$res_doc = pdo_fetch('SELECT a.lecture_title,a.lecture_cost,a.start_time,a.end_time,a.lecture_address,a.remark,b.ordernumber,c.realname,c.nickname,c.avatar FROM ' . tablename('rr_v_lectures') . ' a,' . tablename('rr_v_lectures_log') . ' b,' . tablename('rr_v_member') . ' c 
									WHERE a.uniacid =:uniacid AND a.id = b.lid AND b.id =:id AND a.openid=c.openid', array(':uniacid' => $uniacid, ':id' => $list['goodsid']));

			}elseif($list['goodstype'] == 5){

				$res_doc = pdo_fetch('SELECT a.start_time,a.end_time,a.week,a.price,a.address,b.ordernumber,c.realname,c.nickname,c.avatar FROM ' . tablename('rr_v_consult') . ' a,' . tablename('rr_v_consult_log') . ' b,' . tablename('rr_v_member') . ' c 
									WHERE a.uniacid =:uniacid AND a.isdelete =0 AND a.id = b.consultid AND b.id =:id AND a.openid=c.openid', array(':uniacid' => $uniacid, ':id' => $list['goodsid']));

			}elseif($list['goodstype'] == 6){

				$res_doc = pdo_fetch('SELECT a.doc_openid,a.orderid,a.remark,a.paytime,b.title,b.icon,b.price,c.realname,c.nickname,c.avatar FROM ' . tablename('rr_v_member_doctors_reward') . ' a,' . tablename('rr_v_reward') . ' b,' . tablename('rr_v_member') . ' c 
									WHERE a.uniacid =:uniacid AND a.orderid > 0 AND a.rewardid = b.id AND a.id =:id AND a.doc_openid = c.openid', array(':uniacid' => $uniacid, ':id' => $list['goodsid']));
				$res_doc['icon'] = tomedia($res_doc['icon']);
			
			}
			$list['res_doc'] = $res_doc;
		}

		app_json(array('list' => $list));

	}
}

?>
