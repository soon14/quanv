<?php


if (!defined('IN_IA')) {
	exit('Access Denied');
}
require rr_vv3_PLUGIN . 'app/core/page_mobile.php';
class Member_RrvV3Page extends AppMobilePage
{

	public function get_memadv()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}			
		$member = m('member')->getMember($openid);
		$condition = ' uniacid= :uniacid and showtype=:showtype and enabled = 1 ORDER BY id DESC LIMIT 5';
		$params = array(':uniacid' => $uniacid, ':showtype' => $member['identity']);
		$list = pdo_fetchall('select * from ' . tablename('rr_v_adv') . ' where ' . $condition . ' ', $params);
		if (empty($list)) {
			app_error(AppError::$ParamsError);
		}
		foreach ($list as &$row) {
			$row['thumb'] = tomedia($row['thumb']);
		}
		unset($row);

		$result['list'] = $list;
		
		app_json($result);
	}

	public function memadv_detail()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$id = intval($_GPC['id']);

		if (empty($_GPC['id'])) {
			app_error(AppError::$ParamsError);
		}			

		$condition = ' uniacid=:uniacid and id=:id and enabled = 1 limit 1';
		$params = array(':uniacid' => $uniacid, ':id' => $id);
		$list = pdo_fetch('SELECT * FROM ' . tablename('rr_v_adv') . ' where ' . $condition, $params);
		if (!empty($list)) {

			$list['content'] = str_replace('section', 'div', $list['content']);
			$list['content'] = str_replace('"Microsoft YaHei"', '\'Microsoft YaHei\'', $list['content']);
			$list['thumb'] = tomedia($list['thumb']);
		}
		
		app_json(array('list' => $list));
	}

	public function notice_detail()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$id = intval($_GPC['id']);

		if (empty($_GPC['id'])) {
			app_error(AppError::$ParamsError);
		}			

		$condition = ' uniacid=:uniacid and id=:id and status=1 and iswxapp = 1 limit 1';
		$params = array(':uniacid' => $uniacid, ':id' => $id);
		$list = pdo_fetch('SELECT * FROM ' . tablename('rr_v_notice') . ' where ' . $condition, $params);
		if (!empty($list)) {

			$list['detail'] = str_replace('section', 'div', $list['detail']);
			$list['detail'] = str_replace('"Microsoft YaHei"', '\'Microsoft YaHei\'', $list['detail']);

			$list['thumb'] = tomedia($list['thumb']);
		}
		
		app_json(array('list' => $list));
	}

	//用户信息绑定
	public function user_binding()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];

		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}			

		//将用户关注公众号的openid和小程序openid进行绑定，绑定为同一个用户
		$condition = ' uniacid= :uniacid and openid=:openid ';
		$where = ' uniacid= :uniacid and openid<>:openid and avatar_byte>0';
		$params = array(':uniacid' => $uniacid, ':openid' => $openid);
		$wx_list = pdo_fetch('select * from ' . tablename('rr_v_member') . ' where ' . $condition . ' limit 1', $params);
		if(empty($wx_list['memberid'])){
			$params[':MD5nickname'] = $wx_list['MD5nickname'];
			$gz_list = pdo_fetchall('select * from ' . tablename('rr_v_member') . ' where ' . $where . ' and MD5nickname=:MD5nickname', $params);
			if(count($gz_list) < 2){
				foreach ($gz_list as &$row) {
					if($wx_list['avatar_byte'] == $row['avatar_byte']){
						pdo_update('rr_v_member', array('memberid' => $wx_list['id']), array('uniacid' => $uniacid, 'openid' => $row['openid']));
						pdo_update('rr_v_member', array('memberid' => $row['id']), array('uniacid' => $uniacid, 'openid' => $wx_list['openid']));
					}else{
						$num = $row['avatar_byte'] > $wx_list['avatar_byte'] ? ($row['avatar_byte'] - $wx_list['avatar_byte']) : ($wx_list['avatar_byte'] - $row['avatar_byte']);
						if($num < 10){
							pdo_update('rr_v_member', array('memberid' => $wx_list['id']), array('uniacid' => $uniacid, 'openid' => $row['openid']));
							pdo_update('rr_v_member', array('memberid' => $row['id']), array('uniacid' => $uniacid, 'openid' => $wx_list['openid']));
						}elseif($wx_list['MD5nickname'] == $row['MD5nickname']){
							pdo_update('rr_v_member', array('memberid' => $wx_list['id']), array('uniacid' => $uniacid, 'openid' => $row['openid']));
							pdo_update('rr_v_member', array('memberid' => $row['id']), array('uniacid' => $uniacid, 'openid' => $wx_list['openid']));
						}
					}
				}
				unset($row);
			}
			if(count($gz_list) > 2){
				
				foreach ($gz_list as &$val) {

					if($wx_list['avatar_byte'] == $row['avatar_byte']){
						pdo_update('rr_v_member', array('memberid' => $wx_list['id']), array('uniacid' => $uniacid, 'openid' => $row['openid']));
						pdo_update('rr_v_member', array('memberid' => $row['id']), array('uniacid' => $uniacid, 'openid' => $wx_list['openid']));
					}else{

						$num = $val['avatar_byte'] > $wx_list['avatar_byte'] ? ($val['avatar_byte'] - $wx_list['avatar_byte']) : ($wx_list['avatar_byte'] - $val['avatar_byte']);

						if($num < 10){
							pdo_update('rr_v_member', array('memberid' => $wx_list['id']), array('uniacid' => $uniacid, 'openid' => $val['openid']));
							pdo_update('rr_v_member', array('memberid' => $val['id']), array('uniacid' => $uniacid, 'openid' => $wx_list['openid']));
						}elseif($wx_list['MD5nickname'] == $row['MD5nickname']){
							pdo_update('rr_v_member', array('memberid' => $wx_list['id']), array('uniacid' => $uniacid, 'openid' => $row['openid']));
							pdo_update('rr_v_member', array('memberid' => $row['id']), array('uniacid' => $uniacid, 'openid' => $wx_list['openid']));
						}
					}
				}
				unset($val);
				

			}
		}

		$member = m('member')->getMember($openid);
		if(!empty($member['memberid'])){
			$user = m('member')->getMember($member['memberid']);
			$list['gzhopenid'] = $user['openid'];
		}else{
			$list['gzhopenid'] = '';
		}
		
		app_json(array('list' => $list));
	}

	
}