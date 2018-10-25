<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}
 
require rr_vv3_PLUGIN . 'app/core/page_mobile.php';
class Newpay_RrvV3Page extends AppMobilePage
{

	public function createpay()
	{
		global $_W;
		global $_GPC;
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$uniacid = $_W['uniacid'];
		$member = m('member')->getMember($openid);

		if ($member['isblack'] == 1) {
			app_error(AppError::$UserIsBlack);
		}
		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}

		$goodsid = $param['goodsid'];//视频、文章、图文咨询的医生表id，当面咨询id，讲座id，医生的打赏id
		$goodstype = intval($param['goodstype']);//1表示图文咨询，2表示视频播放，3表示文章查阅,4表示讲座报名,5表示当面咨询,6表示打赏
		$total = $param['total'];
		$price = $param['price'];
		$optionname = $param['optionname'];
		$ordersn = m('common')->createNO('order', 'ordersn', 'QV');
		if (empty($ordersn)) {
			app_error(AppError::$ParamsError);
		}

		//创建订单
		$order = array();
		$order['uniacid'] = $uniacid;
		$order['openid'] = $openid;
		$order['ordersn'] = $ordersn;
		$order['price'] = $price;
		$order['goodsprice'] = $price;
		$order['status'] = 0;
		$order['paytype'] = 0;
		$order['createtime'] = time();
		$order['carrier'] = iserializer(array());
		$order['oldprice'] = $price;
		$order['olddispatchprice'] = $price;
		$order['grprice'] = $price;
		$order['verifyinfo'] = iserializer(array());

		pdo_insert('rr_v_order', $order);
		$orderid = pdo_insertid();
		if (empty($orderid)) {
			app_error(AppError::$ParamsError);
		}

		$order_goods = array();
		$order_goods['orderid'] = $orderid;
		$order_goods['uniacid'] = $uniacid;
		$order_goods['goodsid'] = $goodsid;
		if($goodstype == 6){
			$order_goods['price'] = $price;
		}else{
			$shop = m('common')->getSysset('shop');//平台抽成
			if(empty($shop['drawsprice'])){
				$drawsprice = 0;
			}else{
				$drawsprice = $shop['drawsprice']/100;
			}
			
			$order_goods['price'] = round(($price*1 - $price*$drawsprice),1);
		}
		$order_goods['total'] = $total;
		$order_goods['createtime'] = time();
		$order_goods['optionname'] = $optionname;
		$order_goods['realprice'] = $price;
		$order_goods['oldprice'] = $price;
		$order_goods['diyformdata'] = iserializer(array());
		$order_goods['diyformfields'] = iserializer(array());
		$order_goods['openid'] = $openid;
		$order_goods['goodstype'] = $goodstype;

		pdo_insert('rr_v_order_goods', $order_goods);

		//获取支付参数，默认微信支付
		$log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(':uniacid' => $uniacid, ':module' => 'rr_vv3', ':tid' => $order['ordersn']));
		if (!empty($log) && ($log['status'] != '0')) {
			app_error(AppError::$OrderAlreadyPay);
		}
		if (!empty($log) && ($log['status'] == '0')) {
			pdo_delete('core_paylog', array('plid' => $log['plid']));
			$log = NULL;
		}

		if (empty($log)) {
			$log = array('uniacid' => $uniacid, 'openid' => $param['openid'], 'module' => 'rr_vv3', 'tid' => $order['ordersn'], 'fee' => $order['price'], 'status' => 0);
			pdo_insert('core_paylog', $log);
			$plid = pdo_insertid();
		}

		$set = m('common')->getSysset(array('shop', 'pay'));
		$wechat = array('success' => false);
		if (!empty($set['pay']['wxapp']) && (0 < $order['price']) && $this->iswxapp) {
			$tid = $order['ordersn'];

			$payinfo = array('openid' => $openid, 'title' => $optionname . '订单', 'tid' => $tid, 'fee' => $order['price']);
			$res = $this->model->wxpay($payinfo, 14);

			if (!is_error($res)) {
				$wechat = array('success' => true, 'payinfo' => $res);
				if (!empty($res['package']) && strexists($res['package'], 'prepay_id=')) {
					$prepay_id = str_replace('prepay_id=', '', $res['package']);
					pdo_update('rr_v_order', array('wxapp_prepay_id' => $prepay_id), array('id' => $orderid, 'uniacid' => $_W['uniacid']));
				}
			}
			else {
				$wechat['payinfo'] = $res;
			}
		}


		app_json(array(
				'order'  => array('orderid' => $orderid, 'ordersn' => $order['ordersn'], 'price' => $order['price'], 'title' => $set['shop']['name'] . '订单'),
				'wechat' => $wechat,
		));
	}


	public function complete()
	{
		global $_W;
		global $_GPC;
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$orderid = intval($param['orderid']);
		$uniacid = $_W['uniacid'];

		if (empty($orderid) || empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}

		$set = m('common')->getSysset(array('shop', 'pay'));
		// $member = m('member')->getMember($openid, true);
		$order = pdo_fetch('select * from ' . tablename('rr_v_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));

		if (empty($order)) {
			app_error(AppError::$OrderNotFound);
		}

		$log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(':uniacid' => $uniacid, ':module' => 'rr_vv3', ':tid' => $order['ordersn']));

		if (empty($log)) {
			app_error(AppError::$OrderPayFail);
		}

		if (empty($set['pay']['wxapp']) && $this->iswxapp) {
			app_error(AppError::$OrderPayFail, '未开启微信支付');
		}

		$ordersn = $order['ordersn'];

		if(!empty($log['tag']) && $log['status'] != 0){

			$record = array();
			$record['type'] = 'wechat';
			$record['uniontid'] = iunserializer($log['tag'])['transaction_id'];
			pdo_update('core_paylog', $record, array('plid' => $log['plid']));
			@session_start();
			$_SESSION[rr_vv3_PREFIX . '_order_pay_complete'] = 1;
			pdo_update('rr_v_order', array('apppay' => 2, 'status' => 3), array('id' => $order['id']));

			$goods = pdo_fetch('SELECT goodsid,goodstype FROM ' . tablename('rr_v_order_goods') . ' WHERE `uniacid`=:uniacid AND orderid=:orderid AND openid=:openid ', array(':uniacid' => $uniacid, ':orderid' => $order['id'], ':openid' => $openid));
			if($goods['goodstype'] == 1){
				pdo_update('rr_v_member_patients_diag', array('orderid' => $order['id']), array('id' => $goods['goodsid'], 'openid' => $openid));

				$res_doc = pdo_fetch('SELECT a.id,a.name,a.createtime,a.doctorid,b.openid FROM '.tablename('rr_v_member_patients_diag').' a,'.tablename('rr_v_member_doctors').' b where a.uniacid = :uniacid
					and a.id =:id and a.doctorid = b.id and a.openid =:openid limit 1', array(':uniacid' => $uniacid, ':id' => $goods['goodsid'], ':openid' => $openid));
				if(!empty($res_doc)){
					$doc_mem = m('member')->getMember($res_doc['openid']);
					if(!empty($doc_mem['memberid'])){
						$kefu_openid = m('member')->getMember($doc_mem['memberid']);
					}
					$pat_mem = m('member')->getMember($openid);
					if(!empty($pat_mem['memberid'])){
						$fans_openid = m('member')->getMember($pat_mem['memberid']);
					}

					// if(!empty($kefu_openid) && !empty($fans_openid)){

					// 	$fanskefu = pdo_fetch('select * from ' . tablename('messikefu_fanskefu') . ' where fansopenid=:fansopenid and kefuopenid=:kefuopenid and weid=:uniaicd limit 1 ', array(':fansopenid' => $fans_openid['openid'], ':kefuopenid' => $kefu_openid['openid'], ':uniaicd' => $_W['uniacid']));
					// 	if(empty($fanskefu)){

					// 		$kefuData = array(
					// 			'weid' => $_W['uniacid'], 'fansopenid' => $fans_openid['openid'],'kefuopenid' => $kefu_openid['openid'], 
					// 			'fansavatar' => $pat_mem['avatar'],'kefuavatar' => $doc_mem['avatar'],
					// 			'fansnickname' => $res_doc['name'],'kefunickname' => empty($doc_mem['realname'])?$doc_mem['nickname']:$doc_mem['realname'],
					// 			'lasttime' => $res_doc['createtime'],'notread' => 0,'lastcon' => '',
					// 			'kefulasttime' => 0,'kefulastcon' => '','kefunotread' => 0,'msgtype' => 0,
					// 			'kefumsgtype' => 0,'kefuseetime' => 0,'fansseetime' => 0,'guanlinum' => 0,
					// 			'ishei' => 0,'fansdel' => 0,'kefudel' => 0,'fszx' => 0,
					// 			'kfzx' => 0,

					// 		);

					// 		pdo_insert('messikefu_fanskefu', $kefuData);

					// 	}
					// }

				}
			}
			if($goods['goodstype'] == 4){
				pdo_update('rr_v_lectures_log', array('ordernumber' => 'V'.time()), array('id' => $goods['goodsid'], 'openid' => $openid));

				//讲座notread字段数量+1
				$notread = pdo_fetch('select a.id,a.notread from '.tablename('rr_v_lectures').' a,'.tablename('rr_v_lectures_log').' b where a.uniacid = :uniacid and 
					b.id = :id and a.id = b.lid limit 1',array(':uniacid' => $uniacid,':id' => $goods['goodsid']));
				pdo_update('rr_v_lectures',array('notread' =>$notread['notread']+1),array('uniacid' => $uniacid, 'id' => $notread['id']));
			}
			if($goods['goodstype'] == 5){
				pdo_update('rr_v_consult_log', array('ordernumber' => 'V'.time()), array('consultid' => $goods['goodsid'], 'openid' => $openid));

				//报名后往rr_v_consult表notread字段+1
				$notread = pdo_fetch('select notread as nums from '.tablename('rr_v_consult').' where uniacid = :uniacid and id = :id ',array(':uniacid' => $uniacid,':id' => $goods['goodsid']));
				
				pdo_update('rr_v_consult',array('notread' => $notread['nums']+1),array('uniacid' => $uniacid, 'id' => $goods['goodsid']));
			}
			if($goods['goodstype'] == 6){
				pdo_update('rr_v_member_doctors_reward', array('orderid' => $order['id'], 'paytime' => $order['paytime']), array('id' => $goods['goodsid'], 'pat_openid' => $openid));
			}
			
			$message = '您已经支付成功';

			app_json(array('status' => 1, 'message' => $message));
		}
		
		app_error(AppError::$OrderPayFail);
	}

}

?>
