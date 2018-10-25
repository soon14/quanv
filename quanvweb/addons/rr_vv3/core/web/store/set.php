<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Set_RrvV3Page extends ComWebPage
{
	public function __construct($_com = 'verify')
	{
		parent::__construct($_com);
	}

	public function main()
	{
		global $_W;
		global $_GPC;
		$verify = m('common')->getSysset('verify');
		$keyword = $verify['keyword'];
		$type = $verify['type'];

		if ($_W['ispost']) {
			ca('shop.verify.set.edit');
			$keyword = trim($_GPC['keyword']);
			$type = trim($_GPC['type']);

			if (empty($keyword)) {
				show_json(0, '请填写关键词!');
			}

			$keyword1 = m('common')->keyExist($keyword);

			if (!empty($keyword1)) {
				if ($keyword1['name'] != 'rr_vv3:com:verify') {
					show_json(0, '关键字已存在!');
				}
			}

			m('common')->updateSysset(array(
	'verify' => array('keyword' => $keyword, 'type' => $type)
	));
			m('common')->updatePluginset(array(
	'verify' => array('keyword' => $keyword, 'type' => $type)
	));
			$rule = pdo_fetch('select * from ' . tablename('rule') . ' where uniacid=:uniacid and module=:module and name=:name  limit 1', array(':uniacid' => $_W['uniacid'], ':module' => 'rr_vv3', ':name' => 'rr_vv3:com:verify'));

			if (empty($rule)) {
				$rule_data = array('uniacid' => $_W['uniacid'], 'name' => 'rr_vv3:com:verify', 'module' => 'rr_vv3', 'displayorder' => 0, 'status' => 1);
				pdo_insert('rule', $rule_data);
				$rid = pdo_insertid();
				$keyword_data = array('uniacid' => $_W['uniacid'], 'rid' => $rid, 'module' => 'rr_vv3', 'content' => trim($keyword), 'type' => 1, 'displayorder' => 0, 'status' => 1);
				pdo_insert('rule_keyword', $keyword_data);
			}
			else {
				pdo_update('rule_keyword', array('content' => trim($keyword)), array('rid' => $rule['id']));
			}

			plog('shop.verify.set', '设置核销关键词');
			show_json(1);
		}

		$url = mobileUrl('verify/page', NULL, true);
		$qrcode = m('qrcode')->createQrcode($url);
		include $this->template();
	}
}

?>
