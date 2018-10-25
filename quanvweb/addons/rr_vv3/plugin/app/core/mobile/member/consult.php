<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}
 
require rr_vv3_PLUGIN . 'app/core/page_mobile.php';
class Consult_RrvV3Page extends AppMobilePage
{
	//医生端获取预约信息
	public function get_docconsult()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];

		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}else{
			if($param['type'] == '0'){//获取历史记录
				$condition = ' uniacid=:uniacid and openid=:openid and isdelete=0 and end_time < "'.date('Y/m/d H:i').'" ORDER BY start_time DESC';
			}else{
				$condition = ' uniacid=:uniacid and openid=:openid and isdelete=0 and start_time > "'.date('Y/m/d H:i').'" ORDER BY start_time';
			}
			$params = array(':uniacid' => $uniacid, ':openid' => $openid);

			$list = pdo_fetchall('SELECT * FROM ' . tablename('rr_v_consult') . ' WHERE '.$condition.' ', $params);
			foreach ($list as &$row) {
				$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
				$row['date'] = explode(' ', $row['start_time'])[0];
				$row['start_time'] = explode(' ', $row['start_time'])[1];
				$row['end_time'] = explode(' ', $row['end_time'])[1];
				$row['consult_nums'] = pdo_fetchcolumn('select count(*) from' . tablename('rr_v_consult_log') . ' where uniacid=:uniacid and status=1 and ordernumber <>"" and consultid=:consultid ', array(':uniacid' => $uniacid, ':consultid' => $row['id']));
				if(empty($row['consult_nums'])){
					$row['consult_nums'] = 0;
				}
			}
			unset($row);
			
		}
		
		
		app_json(array('list' => $list));

	}

	//医生端获取预约信息数量
	public function get_docconsult_total()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$params = array(':uniacid' => $uniacid, ':openid' => $openid);
		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}else{

			$condition = ' uniacid=:uniacid and openid=:openid and isdelete=0 and start_time > "'.date('Y/m/d H:i').'" ORDER BY start_time';

			//获取列表新的未读报名人数
			$notread = pdo_fetch('SELECT IFNULL(SUM(notread),0) mysum FROM ' . tablename('rr_v_consult') . ' WHERE '.$condition.' ',$params);
			if(empty($notread)){
				$notread['mysum'] = 0;
			}
			$total['release'] = $notread['mysum'];
		}
		
		
		app_json(array('list' => $total));

	}

	//医生端当面预约详情列表
	public function doc_consultdetail()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$consultid = intval($_GPC['consultid']);

		if (empty($consultid)) {
			app_error(AppError::$ParamsError);
		}else{

			$condition = ' a.uniacid =:uniacid AND a.status = 1 AND a.consultid = c.id AND a.openid = b.openid AND a.ordernumber <>"" AND a.consultid =:consultid ';
			$params = array(':uniacid' => $uniacid, ':consultid' => $consultid);
			if($_GPC['type'] == '1'){

				$condition .= ' AND a.confirmtime = 0 ';

			}elseif($_GPC['type'] == '2'){

				$condition .= ' AND a.confirmtime > 0 ';
				

			}elseif($_GPC['type'] == '3'){
				if(!empty($_GPC['keyword'])){
					$condition .= ' AND a.name LIKE "%'.trim($_GPC['keyword']).'%" ';
				}
				$condition .= ' AND c.start_time < "'.date('Y/m/d H:i').'" ';
			}

			$list = pdo_fetchall('SELECT a.id consult_logid,a.consultid,a.name,a.openid,a.mobile,a.sex,a.age,a.title,a.ordernumber,a.remarks,
				a.confirmtime,a.createtime,b.avatar FROM ' . tablename('rr_v_consult_log') . ' a,' . tablename('rr_v_member') . ' b,' . tablename('rr_v_consult') . ' c WHERE '.$condition.' ORDER BY a.id DESC', $params);
			foreach ($list as &$row) {
				$row['createtime'] = date('m/d H:i', $row['createtime']);
				if($row['confirmtime'] != 0){
					$row['confirmDate'] = date('Y/m/d', $row['confirmtime']);
					$row['confirmTime'] = date('H:i:s', $row['confirmtime']);
				}
			}
			unset($row);
			//清空noread字段数量
			pdo_update('rr_v_consult',array('notread' => 0),array('id' => $consultid));
		}
		
		app_json(array('list' => $list));
	}

	//医生端当面预约详情列表数量
	public function doc_consultdetail_total()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$consultid = intval($_GPC['consultid']);
		$params = array(':uniacid' => $uniacid, ':consultid' => $consultid);
		if (empty($consultid)) {
			app_error(AppError::$ParamsError);
		}else{
			$unfinished = pdo_fetchcolumn('select count(*) from ' .tablename('rr_v_consult_log').' where uniacid = :uniacid and 
				consultid = :consultid and ordernumber<>"" and status = 1 and confirmtime = 0',$params);
			if(!empty($unfinished)){
				$total['unfinished'] = $unfinished;
			}else{
				$total['unfinished'] = 0;
			}
			$finished = pdo_fetchcolumn('select count(*) from ' .tablename('rr_v_consult_log').' where uniacid = :uniacid and 
				consultid = :consultid and ordernumber<>"" and status = 1 and confirmtime > 0',$params);
			if(!empty($finished)){
				$total['finished'] = $finished;
			}else{
				$total['finished'] = 0;
			}

		}
		
		app_json(array('list' => $total));
	}

	//医生端当面预约患者支付预约详情
	public function pat_payconsultdetail()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = $param['openid'];
		$consult_logid = intval($param['consult_logid']);

		if (empty($openid) || empty($consult_logid)) {
			app_error(AppError::$ParamsError);
		}else{

			$condition = ' a.uniacid =:uniacid AND a.status = 1 AND a.openid = b.openid AND a.ordernumber <>"" ';
 			$condition .= ' AND a.consultid = c.id AND c.id = g.goodsid AND g.orderid = o.id AND a.openid=o.openid ';
 			$condition .= ' AND a.id =:id AND a.openid =:openid AND o.status = 3 AND o.paytype = 21 ';
			$params = array(':uniacid' => $uniacid, ':id' => $consult_logid, ':openid' => $openid);

			$sql = 'SELECT a.id,a.consultid,a.name,a.openid,a.mobile,a.sex,a.age,a.title,a.ordernumber,a.remarks,
				a.confirmtime,a.createtime,b.avatar,c.start_time,c.end_time,c.week,c.address,c.remark,o.price,o.paytime FROM ';
			$sql .= tablename('rr_v_consult_log') . ' a,' . tablename('rr_v_member') . ' b,' . tablename('rr_v_consult') . ' c,';
			$sql .= tablename('rr_v_order_goods') . ' g,' . tablename('rr_v_order') . ' o ';
			$sql .= 'WHERE '.$condition.' ';

			$list = pdo_fetchall($sql, $params);
			foreach ($list as &$row) {
				$row['createtime'] = date('m/d H:i', $row['createtime']);
				$row['paytime'] = date('Y-m-d H:i:s', $row['paytime']);
				$row['date'] = explode(' ', $row['start_time'])[0];
				$row['start_time'] = explode(' ', $row['start_time'])[1];
				$row['end_time'] = explode(' ', $row['end_time'])[1];
				if($row['confirmtime'] != 0){
					$row['confirmtime'] = date('Y-m-d H:i:s', $row['confirmtime']);
				}
			}
			unset($row);
		}
		
		app_json(array('list' => $list));
	}

	//医生添加预约
	public function add()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$status = 0;
		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}else{
			$data = array(
				'uniacid' 		=> $uniacid,
				'openid'  		=> $openid,
				'start_time' 	=> $param['date'] . ' ' . $param['start_time'],
				'end_time' 		=> $param['date'] . ' ' . $param['end_time'],
				'week' 			=> $param['week'],
				'people_nums' 	=> intval($param['people_nums']),
				'price' 		=> $param['price'],
				'address' 		=> $param['address'],
				'remark'		=> $param['remark'],
				'createtime' 	=> time(),
			);
			pdo_insert('rr_v_consult', $data);
			$status = 1;
			
		}
		
		$result['status'] = $status;
		
		app_json($result);

	}

	//医生删除预约
	public function delete()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$id = intval($_GPC['id']);
		$status = 0;

		if (empty($id)) {
			app_error(AppError::$ParamsError);
		}

		$consult = pdo_fetch('SELECT * FROM ' . tablename('rr_v_consult') . ' WHERE id = ' . $id . ' AND uniacid=' . $uniacid . ' LIMIT 1');
		if(!empty($consult)){
			pdo_update('rr_v_consult', array('isdelete' => 1), array('uniacid' => $uniacid, 'id' => $id));
			// pdo_delete('rr_v_consult', array('id' => $id));
			$status = 1;
		}

		$result['status'] = $status;
		
		app_json($result);
	}

	//医生修改预约
	public function edit() 
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$id = intval($param['id']);
		$status = 0;

		if (empty($id) || empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}else{
			$consult = pdo_fetch('SELECT * FROM ' . tablename('rr_v_consult') . ' WHERE id = ' . $id . ' AND uniacid=' . $uniacid . ' LIMIT 1');
			$data = array(
				'start_time' 	=> $param['date'] . ' ' . $param['start_time'],
				'end_time' 		=> $param['date'] . ' ' . $param['end_time'],
				'week' 			=> $param['week'],
				'people_nums' 	=> intval($param['people_nums']),
				'price' 		=> $param['price'],
				'address' 		=> $param['address'],
				'remark' 		=> $param['remark'],
			);
			if(!empty($consult)){
				pdo_update('rr_v_consult', $data, array('uniacid' => $uniacid, 'id' => $id, 'openid' => $openid));
				$status = 1;
			}else{
				app_error(AppError::$ParamsError);
			}
			
		}
		
		$result['status'] = $status;
		
		app_json($result);
	}

	//患者端获取预约
	public function get_patconsult()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$doc_openid = $param['doc_openid'];
		$pat_openid = 'sns_wa_'.$param['pat_openid'];

		if (empty($param['pat_openid']) || empty($doc_openid)) {
			app_error(AppError::$ParamsError);
		}else{
			$condition = ' d.uniacid = :uniacid and d.openid = :openid and m.openid=d.openid';
			$params = array(':uniacid' => $uniacid, ':openid' => $doc_openid);

			$sql = 'select m.realname,m.nickname,m.avatar,m.level,d.recommend_index,d.hospital,d.job,d.specialty,d.parentid,d.departmentid from ' . tablename('rr_v_member') . ' m,' . tablename('rr_v_member_doctors') . ' d where ' . $condition . '  ';
			$res_doc = pdo_fetch($sql, $params);
			if($res_doc['level']==0){
				$res_doc['level'] = 'V0';
			}else{
				$level = pdo_fetch('select id,levelname from ' . tablename('rr_v_member_level') . ' where uniacid = :uniacid and id=:id  LIMIT 1', array(':uniacid' => $uniacid, ':id' => $res_doc['level']));
				$res_doc['level'] = $level['levelname'];
			}
			if($res_doc['departmentid'] != 0){
				$departmentid = $res_doc['departmentid'];
			}else{
				$departmentid = $res_doc['parentid'];
			}
			$res = pdo_fetch('select id,name from ' . tablename('rr_v_member_department') . ' where uniacid = :uniacid and id=:id  LIMIT 1', array(':uniacid' => $uniacid, ':id' => $departmentid));
			if(empty($res)){
				$res['name'] = '';
			}
			$res_doc['department'] = $res['name'];

			$where = ' uniacid=:uniacid and openid =:openid and isdelete=0 and start_time > "'.date('Y/m/d H:i').'"';

			$shop = m('common')->getSysset('shop');//平台抽成
			if(empty($shop['drawsprice'])){
				$drawsprice = 0;
			}else{
				$drawsprice = $shop['drawsprice']/100;
			}

			$list = pdo_fetchall('SELECT * FROM ' . tablename('rr_v_consult') . ' WHERE '.$where.' ORDER BY start_time', $params);
			foreach ($list as &$row) {
				$row['price'] = round(($row['price']*1 + $row['price']*$drawsprice),1);
				$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
				$row['date'] = explode(' ', $row['start_time'])[0];
				$row['start_time'] = explode(' ', $row['start_time'])[1];
				$row['end_time'] = explode(' ', $row['end_time'])[1];
				$consult['consult_nums'] = pdo_fetchcolumn('select count(*) from' . tablename('rr_v_consult_log') . ' where uniacid=:uniacid and status=1 and ordernumber<>"" and consultid=:consultid ', array(':uniacid' => $uniacid, ':consultid' => $row['id']));
				if(empty($consult['consult_nums'])){
					$row['consult_nums'] = 0;
				}else{
					$row['consult_nums'] = $consult['consult_nums'];
				}
				$pat_data = pdo_fetch('select * from ' . tablename('rr_v_consult_log') . ' where uniacid=:uniacid and status=1 and ordernumber<>"" and consultid=:consultid and openid=:openid', array(':uniacid' => $uniacid, ':consultid' => $row['id'], ':openid' => $pat_openid));
				if(empty($pat_data)){
					$row['isconsult'] = 0;
				}else{
					$row['isconsult'] = 1;
				}
			}
			unset($row);
			$res_doc['consult'] = $list;
		}
		
		app_json(array('result' => $res_doc));

	}

	//患者端确认预约
	public function pat_submit()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$consultid = intval($param['consultid']);
		$status = 1;
		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}else{

			$pat_data = pdo_fetchall('select * from' . tablename('rr_v_consult_log') . ' where uniacid=:uniacid and status=1 and consultid=:consultid and openid=:openid', array(':uniacid' => $uniacid, ':consultid' => $consultid, ':openid' => $openid));
			$data = array(
				'uniacid' 		=> $uniacid,
				'consultid' 	=> $consultid,
				'name' 			=> $param['name'],
				'openid'  		=> $openid,
				'mobile' 		=> $param['mobile'],
				'sex' 			=> intval($param['sex']),
				'age' 			=> $param['age'],
				'title'			=> $param['title'],
				'remarks' 		=> $param['remarks'],
				'createtime' 	=> time(),
			);
			if(empty($pat_data)){
				pdo_insert('rr_v_consult_log', $data);
			}else{
				pdo_delete('rr_v_consult_log', array('uniacid' => $uniacid, 'ordernumber' => '', 'consultid' => $consultid, 'openid' => $openid));
				pdo_insert('rr_v_consult_log', $data);
			}
			
			
		}
		
		$result['status'] = $status;
		
		app_json($result);

	}

	//患者端获取我的当面咨询记录
	public function pat_consults()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$pageSize = intval($param['pageSize']);
		$page = intval(($param['page'])-1) * $pageSize;
		$limit = ' limit '.$page.','.$pageSize.' ';
		
		if (empty($param['openid']) || empty($param['page']) || empty($param['pageSize'])) {
			app_error(AppError::$ParamsError);
		}else{

			$shop = m('common')->getSysset('shop');//平台抽成
			if(empty($shop['drawsprice'])){
				$drawsprice = 0;
			}else{
				$drawsprice = $shop['drawsprice']/100;
			}

			$condition = ' a.uniacid =:uniacid AND a.consultid = b.id AND a.ordernumber <>"" AND a.status = 1 AND a.openid =:openid ';
			$params = array(':uniacid' => $uniacid, ':openid' => $openid);

			$sql = 'SELECT a.id consult_logid,a.consultid,a.name,a.mobile,a.sex,a.age,a.title,a.ordernumber,a.confirmtime,b.openid,b.start_time,
					b.end_time,b.week,b.people_nums,b.price,b.address,b.remark,b.createtime FROM ' . tablename('rr_v_consult_log') . ' a,
					' . tablename('rr_v_consult') . ' b where '.$condition.' ORDER BY b.start_time DESC';
			$sql .= $limit;
			$list = pdo_fetchall($sql, $params);
			if(!empty($list)){
				foreach ($list as &$row) {
					//医生信息
					$where = ' d.uniacid = :uniacid and d.openid = :openid and m.openid=d.openid and m.identity=1';
					$doc_params = array(':uniacid' => $uniacid, ':openid' => $row['openid']);

					$doc = 'SELECT d.id doctorid,d.openid,m.realname,m.nickname,m.avatar,m.level,d.recommend_index,
					d.hospital,d.job,d.specialty,d.parentid,d.departmentid,m.memberid,d.default_consult,
					d.highgrade_consult FROM ' . tablename('rr_v_member') . ' m,' . tablename('rr_v_member_doctors') . ' d 
					where ' . $where . '  ';
					$res_doc = pdo_fetch($doc, $doc_params);
					if($res_doc['level']==0){
						$res_doc['level'] = 'V0';
					}else{
						$level = pdo_fetch('select id,levelname from ' . tablename('rr_v_member_level') . ' where uniacid = :uniacid and id=:id  LIMIT 1', array(':uniacid' => $uniacid, ':id' => $res_doc['level']));
						$res_doc['level'] = $level['levelname'];
					}
					if($res_doc['departmentid'] != 0){
						$departmentid = $res_doc['departmentid'];
					}else{
						$departmentid = $res_doc['parentid'];
					}
					$res = pdo_fetch('select id,name from ' . tablename('rr_v_member_department') . ' where uniacid = :uniacid and id=:id  LIMIT 1', array(':uniacid' => $uniacid, ':id' => $departmentid));
					$res_doc['department'] = $res['name'];

					$row['price'] = round(($row['price']*1 + $row['price']*$drawsprice),1);
					$res_doc['default_consult'] = round(($res_doc['default_consult']*1 + $res_doc['default_consult']*$drawsprice),1);
					$res_doc['highgrade_consult'] = round(($res_doc['highgrade_consult']*1 + $res_doc['highgrade_consult']*$drawsprice),1);

					$row['doctors'] = $res_doc;

				}
				unset($row);
			}
			app_json(array('list' => $list));
		}
	}

	//患者端当面预约患者支付预约详情
	public function pat_consultsdetail()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$consult_logid = intval($param['consult_logid']);

		if (empty($param['openid']) || empty($consult_logid)) {
			app_error(AppError::$ParamsError);
		}else{

			$condition = ' a.uniacid =:uniacid AND a.status = 1 AND a.openid = b.openid AND a.ordernumber <>"" ';
 			$condition .= ' AND a.consultid = c.id AND c.id = g.goodsid AND g.orderid = o.id AND a.openid=o.openid ';
 			$condition .= ' AND a.id =:id AND a.openid =:openid AND o.status = 3 AND o.paytype = 21 ';
			$params = array(':uniacid' => $uniacid, ':id' => $consult_logid, ':openid' => $openid);

			$sql = 'SELECT a.id,a.consultid,a.name,a.openid,a.mobile,a.sex,a.age,a.title,a.ordernumber,a.remarks,
				a.confirmtime,a.createtime,b.avatar,c.start_time,c.end_time,c.week,c.address,c.remark,o.price,o.paytime,c.openid doc_openid FROM ';
			$sql .= tablename('rr_v_consult_log') . ' a,' . tablename('rr_v_member') . ' b,' . tablename('rr_v_consult') . ' c,';
			$sql .= tablename('rr_v_order_goods') . ' g,' . tablename('rr_v_order') . ' o ';
			$sql .= 'WHERE '.$condition.' ';

			$list = pdo_fetch($sql, $params);
			$list['createtime'] = date('m/d H:i', $list['createtime']);
			$list['paytime'] = date('Y-m-d H:i:s', $list['paytime']);
			$list['date'] = explode(' ', $list['start_time'])[0];
			$list['start_time'] = explode(' ', $list['start_time'])[1];
			$list['end_time'] = explode(' ', $list['end_time'])[1];
			if($list['confirmtime'] != 0){
				$list['confirmtime'] = date('Y-m-d H:i:s', $list['confirmtime']);
			}

			//医生信息
			$res_doc = pdo_fetch('select m.realname,m.nickname,m.avatar,m.level,d.recommend_index,d.hospital,d.job,d.specialty,d.parentid,
				d.departmentid from ' . tablename('rr_v_member') . ' m,' . tablename('rr_v_member_doctors') . ' d where d.uniacid = :uniacid 
				and d.openid = :openid and m.openid=d.openid ', array(':uniacid' => $uniacid, ':openid' => $list['doc_openid']));
			if($res_doc['level']==0){
				$res_doc['level'] = 'V0';
			}else{
				$level = pdo_fetch('select id,levelname from ' . tablename('rr_v_member_level') . ' where uniacid = :uniacid and id=:id  LIMIT 1', array(':uniacid' => $uniacid, ':id' => $res_doc['level']));
				$res_doc['level'] = $level['levelname'];
			}
			if($res_doc['departmentid'] != 0){
				$departmentid = $res_doc['departmentid'];
			}else{
				$departmentid = $res_doc['parentid'];
			}
			$res = pdo_fetch('select id,name from ' . tablename('rr_v_member_department') . ' where uniacid = :uniacid and id=:id  LIMIT 1', array(':uniacid' => $uniacid, ':id' => $departmentid));
			$res_doc['department'] = $res['name'];
			$list['doctors'] = $res_doc;
		}
		
		app_json(array('list' => $list));
	}

}

?>
