<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class CashierModel extends PluginModel 
{
	const APPLY = 'apply';
	const CHECKED = 'checked';
	const APPLY_CLEARING = 'apply_clearing';
	const PAY = 'pay';
	const PAY_CASHIER = 'pay_cashier';
	const PAY_CASHIER_USER = 'pay_cashier_user';
	static public $paytype = array(0 => '微信', 1 => '支付宝', 2 => '商城余额', 3 => '现金收款', 101 => '系统微信', 102 => '系统支付宝');
	public $setmeal = array('标准套餐', '豪华套餐');
	static public $UserSet = array();
	static public function perm() 
	{
		$perm = array('index' => '我要收款', 'goods' => '商品收款', 'order' => '收款订单', 'statistics' => '收银统计', 'sysset' => '设置', 'sale' => '营销', 'clearing' => '提现', 'goodsmanage' => '商品管理');
		if (empty($_W['cashieruser']['can_withdraw'])) 
		{
			unset($perm['clearing']);
		}
		return $perm;
	}
	public function getUserSet($name = '', $cashierid) 
	{
		global $_W;
		if (!(isset(static::$UserSet[$cashierid]))) 
		{
			$user = $this->userInfo($cashierid);
			$set = ((empty($user['set']) ? array() : json_decode($user['set'], true)));
			static::$UserSet[$cashierid] = $set;
		}
		if (empty($name)) 
		{
			return static::$UserSet[$cashierid];
		}
		return (isset(static::$UserSet[$cashierid][$name]) ? static::$UserSet[$cashierid][$name] : '');
	}
	public function updateUserSet($data = array(), $cashierid) 
	{
		global $_W;
		$user = $this->userInfo($cashierid);
		$set = ((empty($user['set']) ? array() : json_decode($user['set'], true)));
		$set = json_encode(array_merge($set, $data));
		return pdo_query('UPDATE ' . tablename('rr_v_cashier_user') . ' SET `set`=:set WHERE `uniacid` = :uniacid AND `id` = :id', array(':uniacid' => $_W['uniacid'], ':id' => $cashierid, ':set' => $set));
	}
	public function payResult($logid, $return_log = false) 
	{
		global $_W;
		$id = intval($logid);
		if ($id != 0) 
		{
			$log = pdo_fetch('SELECT * FROM ' . tablename('rr_v_cashier_pay_log') . ' WHERE `id`=:id and `uniacid`=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
		}
		else 
		{
			$log = pdo_fetch('SELECT * FROM ' . tablename('rr_v_cashier_pay_log') . ' WHERE `logno`=:logno and `uniacid`=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':logno' => $logid));
		}
		if (!(empty($log))) 
		{
			$_W['cashierid'] = $log['cashierid'];
			$res = $this->updateOrder($log);
			if ($res && ($log['status'] != 1)) 
			{
				$log['status'] = 1;
				$log['paytime'] = time();
			}
			return ($return_log ? $log : $res);
		}
		return false;
	}
	public function categoryAll($status = 1) 
	{
		global $_W;
		$status = intval($status);
		$condition = ' and uniacid=:uniacid  and status=' . intval($status);
		$params = array(':uniacid' => $_W['uniacid']);
		$item = pdo_fetchall('SELECT * FROM ' . tablename('rr_v_cashier_category') . ' WHERE 1 ' . $condition . '  ORDER BY displayorder desc, id DESC', $params);
		return $item;
	}
	public function categoryOne($id) 
	{
		global $_W;
		$item = pdo_fetch('select * from ' . tablename('rr_v_cashier_category') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		return $item;
	}
	public function savaUser(array $params, $diyform = array()) 
	{
		global $_W;
		$diyform_flag = 0;
		$diyform_plugin = p('diyform');
		$f_data = array();
		if ($diyform_plugin && !(empty($_W['shopset']['cashier']['apply_diyform']))) 
		{
			if (!(empty($item['diyformdata']))) 
			{
				$diyform_flag = 1;
				$fields = iunserializer($item['diyformfields']);
				$f_data = iunserializer($item['diyformdata']);
			}
			else 
			{
				$diyform_id = $_W['shopset']['cashier']['apply_diyformid'];
				if (!(empty($diyform_id))) 
				{
					$formInfo = $diyform_plugin->getDiyformInfo($diyform_id);
					if (!(empty($formInfo))) 
					{
						$diyform_flag = 1;
						$fields = $formInfo['fields'];
					}
				}
			}
		}
		$fdata = array();
		if ($diyform_flag) 
		{
			$fdata = p('diyform')->getPostDatas($fields);
			if (is_error($fdata)) 
			{
				show_json(0, $fdata['message']);
			}
			if ($diyform_flag) 
			{
				$params['diyformdata'] = iserializer($fdata);
				if (!(empty($diyform))) 
				{
					$insert_data = $diyform_plugin->getInsertData($fields, $diyform);
					$params['diyformdata'] = $insert_data['data'];
				}
				$params['diyformfields'] = iserializer($fields);
			}
		}
		if (empty($params['title'])) 
		{
			show_json(0, '请填写收银台名称!');
		}
		if (empty($params['manageopenid'])) 
		{
			show_json(0, '请填写管理微信号!');
		}
		if (empty($params['name'])) 
		{
			show_json(0, '请填写联系人!');
		}
		if (empty($params['mobile'])) 
		{
			show_json(0, '请填写联系电话!');
		}
		if (empty($params['username'])) 
		{
			show_json(0, '请填写后台登录用户名!');
		}
		if (!(empty($params['password']))) 
		{
			$params['salt'] = random(8);
			$params['password'] = md5(trim($params['password']) . $params['salt']);
		}
		else 
		{
			unset($params['password']);
		}
		$params['storeid'] = intval($params['storeid']);
		$params['merchid'] = intval($params['merchid']);
		$params['isopen_commission'] = intval($params['isopen_commission']);
		$params['title'] = trim($params['title']);
		$params['logo'] = trim($params['logo']);
		$params['openid'] = trim($params['openid']);
		$params['manageopenid'] = trim($params['manageopenid']);
		$params['name'] = trim($params['name']);
		$params['mobile'] = trim($params['mobile']);
		$params['username'] = trim($params['username']);
		$params['withdraw'] = floatval($params['withdraw']);
		$params['wechat_status'] = intval($params['wechat_status']);
		$params['alipay_status'] = intval($params['alipay_status']);
		if (isset($params['deleted'])) 
		{
			$params['deleted'] = intval($params['deleted']);
		}
		if (!(isset($params['id']))) 
		{
			$params['createtime'] = TIMESTAMP;
			$params['deleted'] = 0;
			pdo_insert('rr_v_cashier_user', $params);
			$params['id'] = pdo_insertid();
		}
		else 
		{
			pdo_update('rr_v_cashier_user', $params, array('id' => $params['id'], 'uniacid' => $params['uniacid']));
		}
		return $params;
	}
	public function userInfo($openid) 
	{
		global $_W;
		$id = intval($openid);
		$sql = 'SELECT * FROM ' . tablename('rr_v_cashier_user') . ' WHERE uniacid=:uniacid AND deleted=0';
		$params = array(':uniacid' => $_W['uniacid']);
		if ($id == 0) 
		{
			$sql .= ' AND openid=:openid';
			$params[':openid'] = $openid;
		}
		else 
		{
			$sql .= ' AND id=:id';
			$params[':id'] = $id;
		}
		$res = pdo_fetch($sql . ' LIMIT 1', $params);
		return $res;
	}
	public function sendMessage($params, $type, $openid = NULL) 
	{
		global $_W;
		if (isset($params['createtime'])) 
		{
			$params['createtime'] = date('Y-m-d H:i:s', $params['createtime']);
		}
		if (isset($params['paytime'])) 
		{
			$params['paytime'] = date('Y-m-d H:i:s', $params['paytime']);
		}
		$data = m('common')->getPluginset('cashier');
		$notice = $data['notice'];
		if (empty($notice[$type])) 
		{
			return false;
		}
		switch ($type) 
		{
			case self: $datas = array('[联系人]' => $params['name'], '[联系电话]' => $params['mobile'], '[申请时间]' => date('Y-m-d H:i:s', $params['createtime']));
			break;
			default: switch ($type) 
			{
				case self: $datas = array('[联系人]' => $params['name'], '[联系电话]' => $params['mobile'], '[审核状态]' => $params['status'], '[审核时间]' => $params['createtime'], '[驳回原因]' => $params['reason']);
				break;
				default: switch ($type) 
				{
					case self: $datas = array('[联系人]' => $params['name'], '[联系电话]' => $params['mobile'], '[申请时间]' => $params['createtime'], '[申请金额]' => $params['money']);
					break;
					default: switch ($type) 
					{
						case self: $datas = array('[联系人]' => $params['name'], '[联系电话]' => $params['mobile'], '[申请时间]' => $params['createtime'], '[打款时间]' => $params['paytime'], '[申请金额]' => $params['money'], '[打款金额]' => $params['realmoney']);
						break;
						default: switch ($type) 
						{
							case self: $datas = array('[订单编号]' => $params['logno'], '[付款金额]' => $params['money'], '[余额抵扣]' => $params['deduction'], '[付款时间]' => $params['paytime'], '[收银台名称]' => $params['cashier_title']);
							break;
							default: switch ($type) 
							{
								case self: $datas = array('[订单编号]' => $params['logno'], '[付款金额]' => $params['money'], '[余额抵扣]' => $params['deduction'], '[付款时间]' => $params['paytime'], '[收银台名称]' => $params['cashier_title']);
								break;
								break;
								$datas = ((isset($datas) ? $datas : array()));
								$notice['openid'] = ((is_null($openid) ? $notice['openid'] : $openid));
							}
						}
					}
				}
			}
		}
	}
	protected function sendNotice($notice, $tag, $datas) 
	{
		global $_W;
		if (!(empty($notice[$tag]))) 
		{
			$advanced_template = pdo_fetch('select * from ' . tablename('rr_v_member_message_template') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $notice[$tag], ':uniacid' => $_W['uniacid']));
			if (!(empty($advanced_template))) 
			{
				$url = ((!(empty($advanced_template['url'])) ? $this->replaceArray($datas, $advanced_template['url']) : ''));
				$advanced_message = array( 'first' => array('value' => $this->replaceArray($datas, $advanced_template['first']), 'color' => $advanced_template['firstcolor']), 'remark' => array('value' => $this->replaceArray($datas, $advanced_template['remark']), 'color' => $advanced_template['remarkcolor']) );
				$data = iunserializer($advanced_template['data']);
				foreach ($data as $d ) 
				{
					$advanced_message[$d['keywords']] = array('value' => $this->replaceArray($datas, $d['value']), 'color' => $d['color']);
				}
				if (!(empty($notice['openid']))) 
				{
					$notice['openid'] = ((is_array($notice['openid']) ? $notice['openid'] : explode(',', $notice['openid'])));
					foreach ($notice['openid'] as $openid ) 
					{
						if (!(empty($notice[$tag])) && !(empty($advanced_template['template_id']))) 
						{
							m('message')->sendTplNotice($openid, $advanced_template['template_id'], $advanced_message, $url);
						}
						else 
						{
							m('message')->sendCustomNotice($openid, $advanced_message, $url);
						}
					}
				}
			}
		}
		return false;
	}
	protected function replaceArray(array $array, $message) 
	{
		foreach ($array as $key => $value ) 
		{
			$message = str_replace($key, $value, $message);
		}
		return $message;
	}
	public function wechayPayInfo($user) 
	{
		$wechatpay = json_decode($user['wechatpay'], true);
		if (empty($wechatpay['appid']) || empty($wechatpay['mch_id'])) 
		{
			$wechat = array('appid' => $wechatpay['sub_appid'], 'mch_id' => $wechatpay['sub_mch_id'], 'apikey' => $wechatpay['apikey']);
		}
		else 
		{
			$wechat = $wechatpay;
		}
		return $wechat;
	}
	public function wechatpay($params) 
	{
		global $_W;
		$wechat = $this->wechayPayInfo($_W['cashieruser']);
		$params['old'] = true;
		return m('common')->wechat_micropay_build($params, $wechat, 13);
	}
	public function wechatpay_101($params) 
	{
		return m('common')->wechat_micropay_build($params, array(), 13);
	}
	public function alipay($params) 
	{
		global $_W;
		return m('common')->AliPayBarcode($params, json_decode($_W['cashieruser']['alipay'], true));
	}
	public function createOrder(array $array, $return = NULL, $can_sale = true) 
	{
		global $_W;
		if (empty($array)) 
		{
			return 0;
		}
		$array['operatorid'] = ((isset($array['operatorid']) ? $array['operatorid'] : 0));
		$array['deduction'] = ((isset($array['deduction']) ? $array['deduction'] : 0));
		$realmoney = $array['money'];
		$sale = array();
		if ((0 < $realmoney) && $can_sale) 
		{
			if (!(empty($array['usecoupon']))) 
			{
				$usecoupon = $this->caculatecoupon($array['usecoupon'], $realmoney);
				$realmoney = $usecoupon['new_price'];
			}
			$sale = $this->saleCalculate($realmoney);
			$realmoney = $sale['money'];
		}
		if ($realmoney < $array['deduction']) 
		{
			$array['deduction'] = $realmoney;
		}
		$realmoney = $realmoney - $array['deduction'];
		$title = $_W['cashieruser']['title'] . '消费';
		if (!(empty($array['title']))) 
		{
			$title = $array['title'];
		}
		$data = array('uniacid' => $_W['uniacid'], 'cashierid' => $_W['cashierid'], 'operatorid' => $array['operatorid'], 'paytype' => $array['paytype'], 'openid' => (isset($array['openid']) ? $array['openid'] : ''), 'logno' => 'CS' . date('YmdHis') . mt_rand(10000, 99999), 'title' => $title, 'createtime' => time(), 'money' => $realmoney, 'randommoney' => (isset($sale['randommoney']) ? $sale['randommoney'] : 0), 'enough' => (isset($sale['enough']['money']) ? $sale['enough']['money'] : 0), 'mobile' => (isset($array['mobile']) ? $array['mobile'] : 0), 'deduction' => (isset($array['deduction']) ? $array['deduction'] : 0), 'discountmoney' => (isset($sale['discount']['money']) ? $sale['discount']['money'] : 0), 'discount' => (isset($sale['discount']['discount']) ? $sale['discount']['discount'] : 0), 'couponpay' => (isset($array['couponpay']) ? $array['couponpay'] : 0), 'nosalemoney' => (isset($array['nosalemoney']) ? $array['nosalemoney'] : 0), 'coupon' => (isset($array['coupon']) ? $array['coupon'] : 0), 'usecoupon' => (isset($array['usecoupon']) ? $array['usecoupon'] : 0), 'usecouponprice' => (isset($usecoupon['money']) ? $usecoupon['money'] : 0), 'status' => 0);
		$res = pdo_insert('rr_v_cashier_pay_log', $data);
		if (!($res)) 
		{
			return error(-2, '数据插入异常,请重试!');
		}
		$data['id'] = pdo_insertid();
		if (!(empty($usecoupon))) 
		{
			pdo_update('rr_v_coupon_data', array('used' => 1, 'usetime' => $data['createtime'], 'ordersn' => $data['logno']), array('id' => $array['usecoupon']));
		}
		if ($return !== NULL) 
		{
			return $data;
		}
		return $this->pay($data, (isset($array['auth_code']) ? $array['auth_code'] : NULL));
	}
	public function pay($data, $auth_code = NULL) 
	{
		global $_W;
		if (($data['money'] <= 0) || ($data['paytype'] == 3)) 
		{
			$data['status'] = 1;
			$data['paytype'] = (($data['paytype'] == 3 ? $data['paytype'] : 2));
			$data['deduction'] -= $data['randommoney'];
			$data['paytime'] = time();
			pdo_update('rr_v_cashier_pay_log', $data, array('id' => $data['id']));
			m('member')->setCredit($data['openid'], 'credit2', -$data['deduction'], array(0, '收银台 ' . $_W['cashieruser']['title'], '收款'));
			$user = $this->userInfo($data['cashierid']);
			$this->paySuccess($data, $user);
			return array('res' => true, 'id' => $data['id']);
		}
		$params = array('title' => $data['title'], 'tid' => $data['logno'], 'fee' => $data['money']);
		$params['out_trade_no'] = $params['tid'];
		$params['total_amount'] = $params['fee'];
		$params['subject'] = $params['title'];
		$params['body'] = $_W['uniacid'] . ':' . 2;
		if ($auth_code !== NULL) 
		{
			$params['auth_code'] = $auth_code;
		}
		if ($data['paytype'] == 0) 
		{
			$res = $this->wechatpay($params);
		}
		else if ($data['paytype'] == 1) 
		{
			$res = $this->alipay($params);
		}
		else 
		{
			$res = $this->wechatpay_101($params);
		}
		return array('res' => $res, 'id' => $data['id']);
	}
	public function orderQuery($pay_log) 
	{
		$array = array();
		if (is_array2($pay_log)) 
		{
			foreach ($pay_log as $value ) 
			{
				if ($value['status'] == '0') 
				{
					$res = $this->updateOrder($value);
					if ($res) 
					{
						$array[] = $res;
					}
				}
			}
		}
		else if (is_array($pay_log)) 
		{
			$res = $this->updateOrder($pay_log);
			if ($res) 
			{
				$array[] = $res;
			}
		}
		else 
		{
			$res = $this->payResult($pay_log);
			if ($res) 
			{
				$array[] = $res;
			}
		}
		return $array;
	}
	public function refund($id) 
	{
		global $_W;
		$id = (int) $id;
		$pay_log = pdo_fetch('SELECT * FROM ' . tablename('rr_v_cashier_pay_log') . ' WHERE uniacid=:uniacid AND id=:id', array(':uniacid' => $_W['uniacid'], ':id' => $id));
		if ($pay_log['status'] != 1) 
		{
			return error(-1, '未支付或者已退款!');
		}
		$out_trade_no = 'CST' . date('YmdHis') . mt_rand(1000, 9999);
		$res = array();
		switch ($pay_log['paytype']) 
		{
			case '0': $res = $this->refundWechat($pay_log['openid'], $pay_log['logno'], $out_trade_no, $pay_log['money'] * 100, $pay_log['money'] * 100, false);
			break;
			case '1': $res = m('finance')->newAlipayRefund(array('out_trade_no' => $pay_log['logno'], 'refund_amount' => $pay_log['money'], 'refund_reason' => $_W['cashieruser']['title'] . ' 收银台退款! 退款订单号: ' . $out_trade_no), json_decode($_W['cashieruser']['alipay'], true));
			break;
			case '2': m('member')->setCredit($pay_log['openid'], 'credit2', $pay_log['money'] + $pay_log['deduction'], $_W['cashieruser']['title'] . ' 收银台退款! 退款订单号' . $out_trade_no);
			break;
			case '3': $res = true;
			break;
			case '101': $res = m('finance')->refund($pay_log['openid'], $pay_log['logno'], $out_trade_no, $pay_log['money'] * 100, $pay_log['money'] * 100, false);
			break;
			case '102': $res = m('finance')->refund($pay_log['openid'], $pay_log['logno'], $out_trade_no, $pay_log['money'] * 100, $pay_log['money'] * 100, false);
			break;
		}
		if (is_error($res)) 
		{
			return $res;
		}
		$refunduser = 0;
		if (isset($_W['cashieruser']['operator'])) 
		{
			$refunduser = $_W['cashieruser']['operator']['id'];
		}
		pdo_update('rr_v_cashier_pay_log', array('status' => -1, 'refundsn' => $out_trade_no, 'refunduser' => $refunduser), array('uniacid' => $_W['uniacid'], 'id' => $id));
		if (com('coupon') && !(empty($pay_log['usecoupon']))) 
		{
			com('coupon')->returnConsumeCoupon($pay_log['usecoupon']);
		}
		if (!(empty($pay_log['present_credit1']))) 
		{
			m('member')->setCredit($pay_log['openid'], 'credit1', -$pay_log['present_credit1'], $_W['cashieruser']['title'] . ' 收银台退款收回赠送的积分! 退款订单号' . $out_trade_no);
		}
		if (!(empty($pay_log['orderid']))) 
		{
			pdo_update('rr_v_order', array('status' => -1), array('uniacid' => $_W['uniacid'], 'id' => $pay_log['orderid']));
		}
		return $res;
	}
	public function updateOrder($log) 
	{
		global $_W;
		if (!(empty($log['status']))) 
		{
			return (int) $log['id'];
		}
		$realmoney = floatval($log['money']);
		$user = $this->userInfo($log['cashierid']);
		if (($log['paytype'] != '101') && ($log['paytype'] != '102')) 
		{
			if (empty($log['paytype'])) 
			{
				$wechat = $this->wechayPayInfo($user);
				$res = m('common')->wechat_order_query($log['logno'], 0, $wechat);
			}
			else if ($log['paytype'] == '1') 
			{
				$alipay = json_decode($user['alipay'], true);
				$res = m('common')->AliPayQuery($log['logno'], $alipay);
			}
		}
		else 
		{
			list($set, $payment) = m('common')->public_build();
			if ($payment['is_new'] == 1) 
			{
				if ($payment['type'] == 4) 
				{
					$res = m('pay')->query($log['logno'], $payment);
				}
				else 
				{
					if (($payment['type'] == 0) || ($payment['type'] == 2)) 
					{
						$payment['appid'] = $payment['sub_appid'];
						$payment['mch_id'] = $payment['sub_mch_id'];
						unset($payment['sub_mch_id']);
					}
					$res = m('common')->wechat_order_query($log['logno'], 0, $payment);
				}
			}
			else 
			{
				if (isset($set) && ($set['weixin'] == 1)) 
				{
					load()->model('payment');
					$setting = uni_setting($_W['uniacid'], array('payment'));
					$account = pdo_get('account_wechats', array('uniacid' => $_W['uniacid']), array('key', 'secret'));
					if (is_array($setting['payment'])) 
					{
						$options = $setting['payment']['wechat'];
						$options['appid'] = $account['key'];
						$options['secret'] = $account['secret'];
						if (IMS_VERSION <= 0.80000000000000004) 
						{
							$options['apikey'] = $options['signkey'];
						}
						$wechat = array('appid' => $options['appid'], 'mch_id' => $options['mchid'], 'apikey' => $options['apikey']);
						$res = m('common')->wechat_order_query($log['logno'], 0, $wechat);
					}
				}
				else if (isset($set) && ($set['weixin_sub'] == 1)) 
				{
					$sec = m('common')->getSec();
					$sec = iunserializer($sec['sec']);
					$wechat = array('appid' => $sec['appid_sub'], 'mch_id' => $sec['mchid_sub'], 'sub_appid' => (!(empty($sec['sub_appid_sub'])) ? $sec['sub_appid_sub'] : ''), 'sub_mch_id' => $sec['sub_mchid_sub'], 'apikey' => $sec['apikey_sub']);
					$res = m('common')->wechat_order_query($log['logno'], 0, $wechat);
				}
				if (empty($res)) 
				{
					$sec = m('common')->getSec();
					$sec = iunserializer($sec['sec']);
					if (isset($set) && ($set['weixin_jie'] == 1)) 
					{
						$wechat = array('appid' => $sec['appid'], 'mch_id' => $sec['mchid'], 'apikey' => $sec['apikey']);
						$res = m('common')->wechat_order_query($log['logno'], 0, $wechat);
					}
					else if (isset($set) && ($set['weixin_jie_sub'] == 1)) 
					{
						$wechat = array('appid' => $sec['appid_jie_sub'], 'mch_id' => $sec['mchid_jie_sub'], 'sub_appid' => (!(empty($sec['sub_appid_jie_sub'])) ? $sec['sub_appid_jie_sub'] : ''), 'sub_mch_id' => $sec['sub_mchid_jie_sub'], 'apikey' => $sec['apikey_jie_sub']);
						$res = m('common')->wechat_order_query($log['logno'], 0, $wechat);
					}
				}
			}
		}
		if (empty($res)) 
		{
			return false;
		}
		if (($res['trade_state'] == 'REFUND') || ($res['message'] == '该订单已经关闭或者已经退款')) 
		{
			pdo_update('rr_v_cashier_pay_log', array('status' => -1), array('uniacid' => $_W['uniacid'], 'id' => $log['id']));
			return false;
		}
		if (($res['total_fee'] == round($realmoney * 100, 2)) || ($res['total_amount'] == round($realmoney, 2))) 
		{
			$log = pdo_fetch('SELECT * FROM ' . tablename('rr_v_cashier_pay_log') . ' WHERE `id`=:id and `uniacid`=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $log['id']));
			if (empty($log['status'])) 
			{
				$res['openid'] = ((isset($res['openid']) ? $res['openid'] : ''));
				$log['openid'] = ((empty($log['openid']) ? $res['openid'] : $log['openid']));
				$this->paySuccess($log, $user, $res);
			}
			return (int) $log['id'];
		}
	}
	public function paySuccess($log, $user) 
	{
		global $_W;
		$coupon = 0;
		if (!(empty($log['openid']))) 
		{
			if ($user['isopen_commission']) 
			{
				$this->becomeDown($log['openid'], $user['manageopenid']);
			}
			$this->sendMessage(array('logno' => $log['logno'], 'money' => $log['money'], 'deduction' => $log['deduction'], 'paytime' => time(), 'cashier_title' => $user['title']), self::PAY_CASHIER_USER, $log['openid']);
			$coupon = $this->seedCoupon($log['money'] + $log['deduction'], $log['openid']);
		}
		pdo_update('rr_v_cashier_pay_log', array('openid' => $log['openid'], 'payopenid' => $log['openid'], 'money' => $log['money'], 'status' => 1, 'paytime' => (0 < $log['paytime'] ? $log['paytime'] : time()), 'coupon' => $coupon), array('id' => $log['id']));
		$log['deduction'] = (double) $log['deduction'];
		if (!(empty($log['deduction'])) && ($log['paytype'] != 2)) 
		{
			$userinfo = m('member')->getMobileMember($log['mobile']);
			m('member')->setCredit($userinfo['openid'], 'credit2', -$log['deduction'], array(0, '收银台 ' . $_W['cashieruser']['title'], '收款'));
		}
		if (!(empty($log['orderid']))) 
		{
			$paytype = 1;
			if (empty($log['paytype']) || ($log['paytype'] == '101')) 
			{
				$paytype = 21;
			}
			if ($log['paytype'] == '1') 
			{
				$paytype = 22;
			}
			pdo_update('rr_v_order', array('status' => 3, 'paytype' => $paytype, 'paytime' => time(), 'sendtime' => time(), 'finishtime' => time()), array('id' => $log['orderid'], 'uniacid' => $_W['uniacid']));
			$this->setStocks($log['orderid']);
			if (p('commission')) 
			{
				p('commission')->checkOrderPay($log['orderid']);
				p('commission')->checkOrderFinish($log['orderid']);
			}
		}
		if (!(empty($log['isgoods']))) 
		{
			$this->setSelfGoodsStocks($log['id']);
		}
		$operator = false;
		if (!(empty($log['operatorid']))) 
		{
			$operator = pdo_fetch('select * from ' . tablename('rr_v_cashier_operator') . ' WHERE id=:id AND cashierid=:cashierid limit 1', array(':id' => $log['operatorid'], ':cashierid' => $log['cashierid']));
		}
		if ($operator) 
		{
			$user['manageopenid'] = $operator['manageopenid'];
		}
		$this->sendMessage(array('logno' => $log['logno'], 'money' => $log['money'], 'deduction' => $log['deduction'], 'paytime' => time(), 'cashier_title' => $user['title']), self::PAY_CASHIER, $user['manageopenid']);
		$userset = json_decode($user['set'], true);
		if (!(empty($log['openid']))) 
		{
			$present_credit1 = $this->sendCredit1($log, $userset);
			if (0 < $present_credit1) 
			{
				pdo_update('rr_v_cashier_pay_log', array('present_credit1' => $present_credit1), array('id' => $log['id']));
			}
		}
		if ((!(empty($log['isgoods'])) || !(empty($log['orderid']))) && !(empty($userset['printer_status'])) && !(empty($userset['printer'])) && !(empty($userset['printer_template']))) 
		{
			com_run('printer::sendCashierMessage', $log, $userset['printer_template'], $userset['printer'], $operator);
		}
		else if (!(empty($userset['printer_status'])) && !(empty($userset['printer'])) && !(empty($userset['printer_template_default']))) 
		{
			com_run('printer::sendCashier', $log, $userset['printer_template_default'], $userset['printer'], $operator);
		}
	}
	public function paytype($paytype = 0, $auto_code = NULL) 
	{
		global $_W;
		if (is_null($paytype) && ($auto_code !== NULL) && (substr($auto_code, 0, 2) == '28')) 
		{
			return error(-101, '暂时不支持支付宝支付!');
		}
		if (($paytype == -1) && ($auto_code !== NULL)) 
		{
			$wechat = array(10, 11, 12, 13, 14, 15);
			$alipay = array(28);
			$type = substr($auto_code, 0, 2);
			if (in_array($type, $alipay)) 
			{
				list(, $payment) = m('common')->public_build();
				if (($payment['is_new'] == 1) && ($payment['type'] == 4)) 
				{
					$paytype = 102;
				}
				if (empty($_W['cashieruser']['alipay_status']) && ($paytype != 102)) 
				{
					return error(-101, '暂时不支持支付宝支付!');
				}
				$paytype = 1;
			}
			else if (in_array($type, $wechat)) 
			{
				$paytype = 0;
			}
		}
		if (empty($paytype) && !(empty($_W['cashieruser']['wechat_status']))) 
		{
			$paytype = 0;
		}
		else if ($paytype == '1') 
		{
			$paytype = 1;
		}
		else if ($paytype == '2') 
		{
			$paytype = 2;
		}
		else if ($paytype == '3') 
		{
			$paytype = 3;
		}
		else if ($paytype == '102') 
		{
			$paytype = 102;
		}
		else 
		{
			$paytype = 101;
		}
		return $paytype;
	}
	public function getrandommoney($money = 0) 
	{
		global $_W;
		$userset = $this->getUserSet('', $_W['cashierid']);
		if (isset($userset['randtime']) && !($this->sale_time($userset['randtime']))) 
		{
			return 0;
		}
		$probability = $userset['rand'];
		if (empty($probability)) 
		{
			return 0;
		}
		if (!(empty($probability['minmoney'])) && ($money < floatval($probability['minmoney']))) 
		{
			return 0;
		}
		$sum = 0;
		$i = 0;
		while ($i < count($probability['rand'])) 
		{
			$sum += $probability['rand'][$i];
			$rand_num = rand(1, 100);
			if ($rand_num <= $sum) 
			{
				$loop = $i;
				break;
			}
			++$i;
		}
		$min = (double) $probability['rand_left'][$loop] * 100;
		$max = (double) $probability['rand_right'][$loop] * 100;
		return rand($min, $max) / 100;
	}
	public function becomeDown($openid, $manageopenid) 
	{
		$member = m('member')->getMember($openid);
		if ($member) 
		{
			if (empty($member['isagent']) && empty($member['agentid'])) 
			{
				$store_member = m('member')->getMember($manageopenid);
				pdo_query('UPDATE ' . tablename('rr_v_member') . ' SET agentid=' . $store_member['id'] . ' WHERE `id` = :id', array(':id' => $member['id']));
			}
		}
	}
	public function getTodayOrder() 
	{
		$starttime = strtotime(date('Y-m-d'));
		$endtime = time();
		return $this->getOrderMoney($starttime, $endtime);
	}
	public function getWeekOrder() 
	{
		$starttime = strtotime(date('Y-m-d')) - ((date('w') - 1) * 3600 * 24);
		$endtime = time();
		return $this->getOrderMoney($starttime, $endtime);
	}
	public function getMonthOrder() 
	{
		$starttime = strtotime(date('Y-m') . '-1');
		$endtime = time();
		return $this->getOrderMoney($starttime, $endtime);
	}
	public function getOrderMoney($starttime = 0, $endtime = 0) 
	{
		global $_W;
		if (($starttime == 0) && ($endtime == 0)) 
		{
			$money = (double) pdo_fetchcolumn('SELECT SUM(money+deduction) FROM ' . tablename('rr_v_cashier_pay_log') . ' WHERE uniacid=:uniacid AND status=1 AND cashierid=:cashierid ', array(':uniacid' => $_W['uniacid'], ':cashierid' => $_W['cashierid']));
		}
		else 
		{
			$money = (double) pdo_fetchcolumn('SELECT SUM(money+deduction) FROM ' . tablename('rr_v_cashier_pay_log') . ' WHERE uniacid=:uniacid AND status=1 AND cashierid=:cashierid AND createtime BETWEEN :starttime AND :endtime', array(':uniacid' => $_W['uniacid'], ':cashierid' => $_W['cashierid'], ':starttime' => $starttime, ':endtime' => $endtime));
		}
		return $money;
	}
	public function getEnoughs($price = 0) 
	{
		global $_W;
		$set = $this->getUserSet('', $_W['cashierid']);
		if (isset($set['enoughtime']) && !($this->sale_time($set['enoughtime']))) 
		{
			return array('old_price' => $price, 'new_price' => $price, 'enough' => 0, 'money' => 0);
		}
		$allenoughs = array();
		$enoughs = $set['enoughs'];
		if ((0 < floatval($set['enoughmoney'])) && (0 < floatval($set['enoughdeduct']))) 
		{
			$allenoughs[] = array('enough' => floatval($set['enoughmoney']), 'money' => floatval($set['enoughdeduct']));
		}
		if (is_array($enoughs)) 
		{
			foreach ($enoughs as $e ) 
			{
				if ((0 < floatval($e['enough'])) && (0 < floatval($e['give']))) 
				{
					$allenoughs[] = array('enough' => floatval($e['enough']), 'money' => floatval($e['give']));
				}
			}
		}
		usort($allenoughs, 'sort_cashier');
		$new_price = $price;
		$enough = 0;
		$money = 0;
		foreach ($allenoughs as $key => $value ) 
		{
			if (($value['enough'] <= $price) && (0 < $value)) 
			{
				$new_price = $price - $value['money'];
				$enough = $value['enough'];
				$money = $value['money'];
				break;
			}
		}
		return array('old_price' => $price, 'new_price' => round($new_price, 2), 'enough' => $enough, 'money' => $money);
	}
	public function is_perm($text) 
	{
		global $_W;
		if (isset($_W['cashieruser']['operator'])) 
		{
			$perm = json_decode($_W['cashieruser']['operator']['perm'], true);
			$routes = explode('.', $text);
			if (!(in_array($routes[0], $perm))) 
			{
				return false;
			}
		}
		return true;
	}
	public function getDiscount($price = 0) 
	{
		global $_W;
		$set = $this->getUserSet('', $_W['cashierid']);
		$set['discount'] = floatval($set['discount']);
		$price = floatval($price);
		if (empty($price) || empty($set['discount']) || (isset($set['discounttime']) && !($this->sale_time($set['discounttime'])))) 
		{
			return array('old_price' => $price, 'new_price' => $price, 'discount' => 0, 'money' => 0);
		}
		$new_price = 0;
		if (!(empty($set['discount']))) 
		{
			$new_price = ($set['discount'] * $price) / 10;
		}
		return array('old_price' => $price, 'new_price' => round($new_price, 2), 'discount' => $set['discount'], 'money' => round($price - $new_price, 2));
	}
	public function sale_time($sale_time) 
	{
		if (!(empty($sale_time)) && ($sale_time['start'] <= time()) && (time() <= $sale_time['end'])) 
		{
			if (empty($sale_time['start1'])) 
			{
				return true;
			}
			$hour = idate('H');
			$minute = idate('i');
			$return = false;
			foreach ($sale_time['start1'] as $key => $value ) 
			{
				if (($sale_time['start1'][$key] == $hour) && ($sale_time['start2'][$key] <= $minute)) 
				{
					if ((($sale_time['end1'][$key] == $hour) && ($minute <= $sale_time['end2'][$key])) || ($hour < $sale_time['end1'][$key])) 
					{
						$return = true;
					}
				}
				if ($sale_time['start1'][$key] < $hour) 
				{
					if ((($sale_time['end1'][$key] == $hour) && ($minute <= $sale_time['end2'][$key])) || ($hour < $sale_time['end1'][$key])) 
					{
						$return = true;
					}
				}
			}
			return $return;
		}
		return false;
	}
	public function saleCalculate($money = 0, $random = true) 
	{
		$randommoney = 0;
		if ($random) 
		{
			$randommoney = $this->getrandommoney($money);
		}
		$realmoney = round($money - $randommoney, 2);
		$enoughs = $this->getEnoughs($realmoney);
		$discount = $this->getDiscount($enoughs['new_price']);
		return array('randommoney' => $randommoney, 'enough' => $enoughs, 'discount' => $discount, 'money' => $discount['new_price']);
	}
	public function createGoodsOrder($goods, $openid = '') 
	{
		global $_W;
		global $_GPC;
		$allgoods = array();
		$realmoney = 0;
		$goodsprice = 0;
		foreach ($goods as $g ) 
		{
			if (empty($g)) 
			{
				continue;
			}
			$goodsid = intval($g['goodsid']);
			$optionid = intval($g['optionid']);
			$goodstotal = intval($g['total']);
			if ($goodstotal < 1) 
			{
				$goodstotal = 1;
			}
			if (empty($goodsid)) 
			{
				show_json(0, '参数错误,未选择商品!');
			}
			$sql = 'SELECT id as goodsid,title,type, weight,total,issendfree,isnodiscount, thumb,marketprice,cash,isverify,verifytype,' . ' goodssn,productsn,sales,istime,timestart,timeend,hasoption,' . ' usermaxbuy,minbuy,maxbuy,unit,buylevels,buygroups,deleted,' . ' status,deduct,manydeduct,`virtual`,discounts,deduct2,ednum,edmoney,edareas,diyformtype,diyformid,diymode,' . ' dispatchtype,dispatchid,dispatchprice,merchid,merchsale,cates,' . ' isdiscount,isdiscount_time,isdiscount_discounts, virtualsend,' . ' buyagain,buyagain_islong,buyagain_condition, buyagain_sale' . ' FROM ' . tablename('rr_v_goods') . ' where id=:id and uniacid=:uniacid and cashier=1 limit 1';
			$data = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':id' => $goodsid));
			if (!(empty($data['hasoption']))) 
			{
				$opdata = m('goods')->getOption($data['goodsid'], $optionid);
				if (empty($opdata) || empty($optionid)) 
				{
					show_json(0, '商品' . $data['title'] . '的规格不存在,请到购物车删除该商品重新选择规格!');
				}
			}
			$merchid = $data['merchid'];
			$merch_array[$merchid]['goods'][] = $data['goodsid'];
			$data['stock'] = $data['total'];
			$data['total'] = $goodstotal;
			if (!(empty($optionid))) 
			{
				$option = pdo_fetch('select id,title,marketprice,goodssn,productsn,stock,`virtual`,weight from ' . tablename('rr_v_goods_option') . ' where id=:id and goodsid=:goodsid and uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':goodsid' => $goodsid, ':id' => $optionid));
				if (!(empty($option))) 
				{
					if ($option['stock'] != -1) 
					{
						if (empty($option['stock'])) 
						{
							show_json(-1, $data['title'] . '<br/>' . $option['title'] . ' 库存不足!');
						}
					}
					$data['optionid'] = $optionid;
					$data['optiontitle'] = $option['title'];
					$data['marketprice'] = $option['marketprice'];
					if (!(empty($option['goodssn']))) 
					{
						$data['goodssn'] = $option['goodssn'];
					}
					if (!(empty($option['productsn']))) 
					{
						$data['productsn'] = $option['productsn'];
					}
					if (!(empty($option['weight']))) 
					{
						$data['weight'] = $option['weight'];
					}
				}
			}
			$data['marketprice'] = ((isset($g['marketprice']) ? floatval($g['marketprice']) : $data['marketprice']));
			$gprice = $data['marketprice'] * $goodstotal;
			$diyprice = ((isset($g['price']) ? floatval($g['price']) : $data['marketprice']));
			$data['ggprice'] = $gprice;
			$data['realprice'] = $diyprice * $goodstotal;
			$realmoney += $data['realprice'];
			$goodsprice += $gprice;
			$allgoods[] = $data;
		}
		$ismerch = 0;
		if (0 < $_W['cashieruser']['merchid']) 
		{
			$ismerch = 1;
		}
		if (0 < $ismerch) 
		{
			$ordersn = m('common')->createNO('order', 'ordersn', 'ME');
		}
		else 
		{
			$ordersn = m('common')->createNO('order', 'ordersn', 'SH');
		}
		$order = array();
		$order['ismerch'] = $ismerch;
		$order['parentid'] = 0;
		$order['uniacid'] = $_W['uniacid'];
		$order['openid'] = $openid;
		$order['ordersn'] = $ordersn;
		$order['price'] = $realmoney;
		$order['oldprice'] = $goodsprice;
		$order['grprice'] = $goodsprice;
		$order['cash'] = 0;
		$order['status'] = 0;
		$order['remark'] = trim($_GPC['remark']);
		$order['addressid'] = 0;
		$order['goodsprice'] = $goodsprice;
		$order['storeid'] = 0;
		$order['createtime'] = time();
		$order['paytype'] = 0;
		$order['merchshow'] = 0;
		$order['merchid'] = intval($_W['cashieruser']['merchid']);
		$order['isparent'] = 0;
		$order['transid'] = '';
		pdo_insert('rr_v_order', $order);
		$orderid = pdo_insertid();
		foreach ($allgoods as $goods ) 
		{
			$order_goods = array();
			$order_goods['merchid'] = $goods['merchid'];
			$order_goods['uniacid'] = $_W['uniacid'];
			$order_goods['orderid'] = $orderid;
			$order_goods['goodsid'] = $goods['goodsid'];
			$order_goods['price'] = $goods['marketprice'] * $goods['total'];
			$order_goods['total'] = $goods['total'];
			$order_goods['optionid'] = $goods['optionid'];
			$order_goods['createtime'] = time();
			$order_goods['optionname'] = $goods['optiontitle'];
			$order_goods['goodssn'] = $goods['goodssn'];
			$order_goods['productsn'] = $goods['productsn'];
			$order_goods['realprice'] = $goods['realprice'];
			$order_goods['oldprice'] = $goods['ggprice'];
			$order_goods['openid'] = $openid;
			pdo_insert('rr_v_order_goods', $order_goods);
		}
		$pluginc = p('commission');
		if ($pluginc) 
		{
			$pluginc->checkOrderConfirm($orderid);
		}
		$order['id'] = $orderid;
		return $order;
	}
	public function goodsCalculate($selfgoods = array(), $shopgoods = array(), $params, $return = false) 
	{
		global $_W;
		$order = $this->createOrder(array('paytype' => $params['paytype'], 'openid' => (isset($params['openid']) ? $params['openid'] : ''), 'money' => (double) $params['money'], 'couponpay' => (double) $params['couponpay'], 'nosalemoney' => (double) $params['nosalemoney'], 'operatorid' => (int) $params['operatorid'], 'deduction' => (double) $params['deduction'], 'mobile' => (int) $params['mobile'], 'title' => $params['title']), 1, false);
		if (!(empty($selfgoods))) 
		{
			foreach ($selfgoods as $key => $val ) 
			{
				$order['goodsprice'] += $val['price'] * $val['total'];
				$order['isgoods'] = 1;
				$data = array('cashierid' => $order['cashierid'], 'logid' => $order['id'], 'goodsid' => $val['goodsid'], 'price' => $val['price'] * $val['total'], 'total' => $val['total']);
				$g = pdo_get('rr_v_cashier_goods', array('cashierid' => $data['cashierid'], 'id' => $data['goodsid']));
				if (($g['total'] < $data['total']) && ($g['total'] != -1)) 
				{
					pdo_delete('rr_v_cashier_pay_log_goods', array('cashierid' => $data['cashierid'], 'logid' => $data['logid']));
					return array('res' => error('-101', $g['title'] . ' 库存不足, 剩余库存 ' . $g['total']));
				}
				pdo_insert('rr_v_cashier_pay_log_goods', $data);
			}
		}
		if (!(empty($shopgoods))) 
		{
			$goods = array();
			foreach ($shopgoods as $val ) 
			{
				$goods[] = array('goodsid' => $val['goodsid'], 'optionid' => $val['optionid'], 'price' => $val['price'], 'total' => $val['total'], 'marketprice' => (isset($val['marketprice']) ? $val['marketprice'] : NULL));
			}
			$goodsOrder = $this->createGoodsOrder($shopgoods, $params['openid']);
			$order['orderid'] = $goodsOrder['id'];
			$order['orderprice'] = $goodsOrder['price'];
		}
		if ($params['money'] == $order['orderprice'] + $order['goodsprice']) 
		{
			pdo_update('rr_v_cashier_pay_log', $order, array('id' => $order['id']));
			if ($return) 
			{
				return $order;
			}
			return $this->pay($order, $params['auth_code']);
		}
		return array('res' => error('-101', '支付金额与商品金额有误差!'));
	}
	public function setStocks($orderid = 0) 
	{
		global $_W;
		if ($orderid == 0) 
		{
			return false;
		}
		$order = pdo_fetch('select id,ordersn,price,openid,dispatchtype,addressid,carrier,status,isparent from ' . tablename('rr_v_order') . ' where id=:id limit 1', array(':id' => $orderid));
		$param = array();
		$param[':uniacid'] = $_W['uniacid'];
		if ($order['isparent'] == 1) 
		{
			$condition = ' og.parentorderid=:parentorderid';
			$param[':parentorderid'] = $orderid;
		}
		else 
		{
			$condition = ' og.orderid=:orderid';
			$param[':orderid'] = $orderid;
		}
		$goods = pdo_fetchall('select og.goodsid,og.total,g.totalcnf,og.realprice,g.credit,og.optionid,g.total as goodstotal,og.optionid,g.sales,g.salesreal from ' . tablename('rr_v_order_goods') . ' og ' . ' left join ' . tablename('rr_v_goods') . ' g on g.id=og.goodsid ' . ' where ' . $condition . ' and og.uniacid=:uniacid ', $param);
		foreach ($goods as $g ) 
		{
			$stocktype = -1;
			if (!(empty($stocktype))) 
			{
				if (!(empty($g['optionid']))) 
				{
					$option = m('goods')->getOption($g['goodsid'], $g['optionid']);
					if (!(empty($option)) && ($option['stock'] != -1)) 
					{
						$stock = -1;
						if ($stocktype == 1) 
						{
							$stock = $option['stock'] + $g['total'];
						}
						else if ($stocktype == -1) 
						{
							$stock = $option['stock'] - $g['total'];
							($stock <= 0) && ($stock = 0);
						}
						if ($stock != -1) 
						{
							pdo_update('rr_v_goods_option', array('stock' => $stock), array('uniacid' => $_W['uniacid'], 'goodsid' => $g['goodsid'], 'id' => $g['optionid']));
						}
					}
				}
				if (!(empty($g['goodstotal'])) && ($g['goodstotal'] != -1)) 
				{
					$totalstock = -1;
					if ($stocktype == 1) 
					{
						$totalstock = $g['goodstotal'] + $g['total'];
					}
					else if ($stocktype == -1) 
					{
						$totalstock = $g['goodstotal'] - $g['total'];
						($totalstock <= 0) && ($totalstock = 0);
					}
					if ($totalstock != -1) 
					{
						pdo_update('rr_v_goods', array('total' => $totalstock), array('uniacid' => $_W['uniacid'], 'id' => $g['goodsid']));
					}
				}
			}
			if (1 <= $order['status']) 
			{
				if ($g['totalcnf'] != 1) 
				{
					pdo_update('rr_v_goods', array('sales' => $g['sales'] - $g['total']), array('uniacid' => $_W['uniacid'], 'id' => $g['goodsid']));
				}
				$salesreal = pdo_fetchcolumn('select ifnull(sum(total),0) from ' . tablename('rr_v_order_goods') . ' og ' . ' left join ' . tablename('rr_v_order') . ' o on o.id = og.orderid ' . ' where og.goodsid=:goodsid and o.status>=1 and o.uniacid=:uniacid limit 1', array(':goodsid' => $g['goodsid'], ':uniacid' => $_W['uniacid']));
				pdo_update('rr_v_goods', array('salesreal' => $salesreal), array('id' => $g['goodsid']));
			}
		}
	}
	public function setSelfGoodsStocks($logid = 0) 
	{
		global $_W;
		if ($logid == 0) 
		{
			return false;
		}
		$goods = pdo_fetchall('SELECT * FROM ' . tablename('rr_v_cashier_pay_log_goods') . ' WHERE cashierid=:cashierid AND logid=:logid', array(':cashierid' => $_W['cashierid'], ':logid' => $logid));
		foreach ($goods as $g ) 
		{
			pdo_query('UPDATE ' . tablename('rr_v_cashier_goods') . ' SET total=total-' . $g['total'] . ' WHERE cashierid=:cashierid AND id=:id AND total<>-1', array(':cashierid' => $_W['cashierid'], ':id' => $g['goodsid']));
		}
	}
	public function querycoupons($kwd = '') 
	{
		global $_W;
		global $_GPC;
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$params[':merchid'] = intval($_W['cashieruser']['merchid']);
		$condition = ' and uniacid=:uniacid and merchid=:merchid and (total=-1 OR total>0)';
		if (empty($_W['cashieruser']['couponid'])) 
		{
			return array();
		}
		$condition .= 'AND id IN (' . $_W['cashieruser']['couponid'] . ')';
		if (!(empty($kwd))) 
		{
			$condition .= ' AND `couponname` LIKE :keyword';
			$params[':keyword'] = '%' . $kwd . '%';
		}
		$ds = pdo_fetchall('SELECT id,couponname as title , thumb FROM ' . tablename('rr_v_coupon') . ' WHERE 1 ' . $condition . ' order by createtime desc', $params);
		$ds = set_medias($ds, 'thumb');
		return $ds;
	}
	public function seedCoupon($price = 0, $member) 
	{
		global $_W;
		if (empty($member)) 
		{
			return false;
		}
		if (is_string($member)) 
		{
			$member = m('member')->getMember($member);
		}
		$set = $this->getUserSet('', $_W['cashierid']);
		$price = floatval($price);
		if (empty($price) || empty($set['coupon']) || (isset($set['coupontime']) && !($this->sale_time($set['coupontime'])))) 
		{
			return false;
		}
		if (empty($set['coupon']['couponid'])) 
		{
			return false;
		}
		if (!(empty($set['coupon']['minmoney'])) && ($price < floatval($set['coupon']['minmoney']))) 
		{
			return false;
		}
		$coupon = com('coupon');
		if ($coupon) 
		{
			$data = $coupon->getCoupon($set['coupon']['couponid']);
			if (($data['total'] == '-1') || (0 < $data['total'])) 
			{
				$coupon->poster($member, $set['coupon']['couponid'], 1);
				return $set['coupon']['couponid'];
			}
		}
		return false;
	}
	public function caculatecoupon($couponid, $totalprice, $openid = NULL) 
	{
		global $_W;
		$openid = ((is_null($openid) ? $_W['openid'] : $openid));
		$uniacid = $_W['uniacid'];
		$sql = 'SELECT d.id,d.couponid,c.enough,c.backtype,c.deduct,c.discount,c.backmoney,c.backcredit,c.backredpack,c.merchid,c.limitgoodtype,c.limitgoodcatetype,c.limitgoodids,c.limitgoodcateids,c.limitdiscounttype  FROM ' . tablename('rr_v_coupon_data') . ' d';
		$sql .= ' left join ' . tablename('rr_v_coupon') . ' c on d.couponid = c.id';
		$sql .= ' where d.id=:id and d.uniacid=:uniacid and d.openid=:openid and d.used=0  limit 1';
		$data = pdo_fetch($sql, array(':uniacid' => $uniacid, ':id' => $couponid, ':openid' => $openid));
		if (empty($data)) 
		{
			return false;
		}
		$data['enough'] = floatval($data['enough']);
		if (!(empty($data['enough'])) && ($totalprice < $data['enough'])) 
		{
			return false;
		}
		$deduct = (double) $data['deduct'];
		$discount = (double) $data['discount'];
		$backtype = (double) $data['backtype'];
		$deductprice = 0;
		if ((0 < $deduct) && ($backtype == 0) && (0 < $totalprice)) 
		{
			if ($totalprice < $deduct) 
			{
				$deduct = $totalprice;
			}
			if ($deduct <= 0) 
			{
				$deduct = 0;
			}
			$deductprice = $deduct;
		}
		else if ((0 < $discount) && ($backtype == 1)) 
		{
			$deductprice = round($totalprice * (1 - ($discount / 10)), 2);
			if ($totalprice < $deductprice) 
			{
				$deductprice = $totalprice;
			}
			if ($deductprice <= 0) 
			{
				$deductprice = 0;
			}
		}
		$new_price = $totalprice - $deductprice;
		return array('old_price' => $totalprice, 'new_price' => round($new_price, 2), 'discount' => $discount, 'money' => $deductprice);
	}
	public function sendCredit1($log, $userset = NULL) 
	{
		$credit1_double = 1;
		$price = $log['money'] + $log['deduction'];
		if (empty($userset['credit1']) || empty($price)) 
		{
			return 0;
		}
		if (!(empty($userset['credit1_double']))) 
		{
			$credit1_double = $userset['credit1_double'];
		}
		$price = $price * $credit1_double;
		$credit1 = com_run('sale::getCredit1', $log['openid'], $price, 37, 1, 0, $log['title'] . '收银订单号 : ' . $log['logno'] . '  收银台消费送积分');
		return $credit1;
	}
	public function upload_cert($fileinput) 
	{
		global $_W;
		$filename = $_FILES[$fileinput]['name'];
		$tmp_name = $_FILES[$fileinput]['tmp_name'];
		if (!(empty($filename)) && !(empty($tmp_name))) 
		{
			$ext = strtolower(substr($filename, strrpos($filename, '.')));
			if ($ext != '.pem') 
			{
				$errinput = '';
				if ($fileinput == 'cert_file') 
				{
					$errinput = 'CERT文件格式错误';
				}
				else if ($fileinput == 'key_file') 
				{
					$errinput = 'KEY文件格式错误';
				}
				else if ($fileinput == 'root_file') 
				{
					$errinput = 'ROOT文件格式错误';
				}
				show_json(0, $errinput . ',请重新上传!');
			}
			return file_get_contents($tmp_name);
		}
		return '';
	}
	public function refundWechat($openid, $out_trade_no, $out_refund_no, $totalmoney, $refundmoney = 0, $app = false, $refund_account = false) 
	{
		global $_W;
		global $_GPC;
		if (empty($openid)) 
		{
			return error(-1, 'openid不能为空');
		}
		$wechatpay = json_decode($_W['cashieruser']['wechatpay'], true);
		if (!(is_array($wechatpay))) 
		{
			return error(1, '没有设定支付参数');
		}
		$certs = array('cert' => $wechatpay['cert'], 'key' => $wechatpay['key'], 'root' => $wechatpay['root']);
		if (empty($wechatpay['appid']) && empty($wechatpay['mch_id']) && !(empty($wechatpay['sub_appid']))) 
		{
			$wechatpay['appid'] = $wechatpay['sub_appid'];
			$wechatpay['mch_id'] = $wechatpay['sub_mch_id'];
			unset($wechatpay['sub_mch_id']);
			unset($wechatpay['sub_appid']);
		}
		$url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
		$pars = array();
		$pars['appid'] = $wechatpay['appid'];
		$pars['mch_id'] = $wechatpay['mch_id'];
		if (!(empty($wechatpay['sub_mch_id']))) 
		{
			$pars['sub_mch_id'] = $wechatpay['sub_mch_id'];
		}
		$pars['nonce_str'] = random(8);
		$pars['out_trade_no'] = $out_trade_no;
		$pars['out_refund_no'] = $out_refund_no;
		$pars['total_fee'] = $totalmoney;
		$pars['refund_fee'] = $refundmoney;
		$pars['op_user_id'] = $wechatpay['mch_id'];
		if ($refund_account) 
		{
			$pars['refund_account'] = $refund_account;
		}
		if (!(empty($wechatpay['sub_appid']))) 
		{
			$pars['sub_appid'] = $wechatpay['sub_appid'];
		}
		ksort($pars, SORT_STRING);
		$string1 = '';
		foreach ($pars as $k => $v ) 
		{
			$string1 .= $k . '=' . $v . '&';
		}
		$string1 .= 'key=' . $wechatpay['apikey'];
		$pars['sign'] = strtoupper(md5($string1));
		$xml = array2xml($pars);
		$extras = array();
		$errmsg = '未上传完整的微信支付证书，请到【系统设置】->【支付方式】中上传!';
		if (is_array($certs)) 
		{
			if (empty($certs['cert']) || empty($certs['key']) || empty($certs['root'])) 
			{
				if ($_W['ispost']) 
				{
					show_json(0, array('message' => $errmsg));
				}
				show_message($errmsg, '', 'error');
			}
			$certfile = IA_ROOT . '/addons/rr_vv3/cert/' . random(128);
			file_put_contents($certfile, $certs['cert']);
			$keyfile = IA_ROOT . '/addons/rr_vv3/cert/' . random(128);
			file_put_contents($keyfile, $certs['key']);
			$rootfile = IA_ROOT . '/addons/rr_vv3/cert/' . random(128);
			file_put_contents($rootfile, $certs['root']);
			$extras['CURLOPT_SSLCERT'] = $certfile;
			$extras['CURLOPT_SSLKEY'] = $keyfile;
			$extras['CURLOPT_CAINFO'] = $rootfile;
		}
		else 
		{
			if ($_W['ispost']) 
			{
				show_json(0, array('message' => $errmsg));
			}
			show_message($errmsg, '', 'error');
		}
		load()->func('communication');
		$resp = ihttp_request($url, $xml, $extras);
		@unlink($certfile);
		@unlink($keyfile);
		@unlink($rootfile);
		if (is_error($resp)) 
		{
			return error(-2, $resp['message']);
		}
		if (empty($resp['content'])) 
		{
			return error(-2, '网络错误');
		}
		$arr = json_decode(json_encode(simplexml_load_string($resp['content'], 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		if (($arr['return_code'] == 'SUCCESS') && ($arr['result_code'] == 'SUCCESS')) 
		{
			return true;
		}
		if (($arr['return_code'] == 'SUCCESS') && ($arr['result_code'] == 'FAIL') && ($arr['return_msg'] == 'OK') && !($refund_account)) 
		{
			if ($arr['err_code'] == 'NOTENOUGH') 
			{
				return $this->refundWechat($openid, $out_trade_no, $out_refund_no, $totalmoney, $refundmoney, $app, 'REFUND_SOURCE_RECHARGE_FUNDS');
			}
		}
		if ($arr['return_msg'] == $arr['err_code_des']) 
		{
			$error = $arr['return_msg'];
		}
		else 
		{
			$error = $arr['return_msg'] . ' | ' . $arr['err_code_des'];
		}
		return error(-2, $error);
	}
}
function sort_cashier($a, $b) 
{
	$enough1 = floatval($a['enough']);
	$enough2 = floatval($b['enough']);
	if ($enough1 == $enough2) 
	{
		return 0;
	}
	return ($enough1 < $enough2 ? 1 : -1);
}
?>