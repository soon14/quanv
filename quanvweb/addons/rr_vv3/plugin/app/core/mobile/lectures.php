<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}
 
require rr_vv3_PLUGIN . 'app/core/page_mobile.php';
class Lectures_RrvV3Page extends AppMobilePage
{
	//医生端上传讲座接口
	public function add_lecture()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$time = $param['time'];
		$timeEnd = $param['timeEnd'];
		$data = $param['data'];
		$openid = 'sns_wa_'.$param['openid'];
		if(empty($param['openid'])){
			app_error(AppError::$ParamsError);
		}else{	
			$data = array(
					'uniacid' => $uniacid,
					'openid' => $openid,
					'cover_url' => $param['cover_url'],
					'lecture_author' => $param['lecture_author'],
					'lecture_title' => $param['lecture_title'],
					'lecture_introduction' => $param['lecture_introduction'],
					'lecture_address' => $param['lecture_address'],
					'lecture_nums' => $param['lecture_nums'],
					'lecture_cost' => $param['lecture_cost'],
					'start_time' => $data. ' ' .$time,
					'end_time' => $data.' '.$timeEnd,
					'remark' => $param['remark'], 
					'status' => 0,
					'createtime' => time(),

				);
				if(empty($param['id'])){
					pdo_insert('rr_v_lectures',$data);
					$result = array('res' => 'insert_success');
				}else{
					pdo_update('rr_v_lectures',$data,array('id' => $param['id']));
					$result = array('res' => 'update_success');
					
				}
				app_json($result);
			
		}

	}	

	//医生端讲座列表
	public function get_lecture_list()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$type = $param['type']; //type:0查审核，1查已发布/待开讲，2查历史记录
		$openid = 'sns_wa_'.$param['openid'];
		if(empty($param['openid'])){
			app_error(AppError::$ParamsError);
		}
		$params = array(':uniacid' => $uniacid,':openid' => $openid);
		if ($type == 0){
			$where = ' and status IN(0,1,2) and isshow = 0 and releasetime=0 order by start_time';
		}elseif($type == 1){
			$where = ' and isshow = 1 and start_time > "'.date('Y-m-d H:i:s').'" and releasetime>0 order by start_time';
		}elseif($type ==2){
			$where = '  and end_time < "'.date('Y-m-d H:i:s').'" order by start_time desc';
		}
		$pageSize = intval($param['pageSize']);
		$page = intval(($param['page'])-1) * $pageSize;
		
		$sql = 'select * from ' .tablename('rr_v_lectures').' where uniacid = :uniacid and openid = :openid '.$where.' LIMIT '.$page.','.$pageSize.' ';
		$list = pdo_fetchall($sql,$params);
		if(!empty($list)){
			foreach ($list as &$value) {
				$member = m('member')->getMember($value['openid']);
				$value['realname'] = $member['realname'];
				$value['nickname'] = $member['nickname'];
				$value['avatar'] = $member['avatar'];
				$time = date("Y-m-d H:i:s",$value['createtime']);
				$value['createtime'] = explode(' ', $time)[0];
				$value['cretime'] = explode(' ', $time)[1];
				$value['time'] = explode(' ',$value['start_time'])[1];
				$value['timeEnd'] = explode(' ',$value['end_time'])[1];
				$value['data'] = explode(' ', $value['end_time'])[0];
				//查询讲座报名人数
				$peoplenum['peoplenum'] = pdo_fetchcolumn('select count(*) as peoplenum from '.tablename('rr_v_lectures_log').' where uniacid = :uniacid 
					and ordernumber <>"" and lid = :id ',array(':uniacid' => $uniacid,':id' => $value['id']));
				if(empty($peoplenum['peoplenum'])){
					$value['peoplenum'] = 0;
				}else{
					$value['peoplenum'] = $peoplenum['peoplenum'];
				}	
			}
		}
		
		$total['total'] = count($list);
		app_json(array('list' => $list,'total' => $total));
	}

	//医生端讲座列表数量
	public function get_lecture_total()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$params = array(':uniacid' => $uniacid,':openid' => $openid);
		if(empty($param['openid'])){
			app_error(AppError::$ParamsError);
		}else{
			//讲座审核状态条数提醒
			$nopass = pdo_fetchcolumn('select count(*) from ' .tablename('rr_v_lectures').' where uniacid = :uniacid and 
				openid = :openid and status IN(1,2) and isshow = 0 and releasetime=0 and nopass=1',$params);
			if(!empty($nopass)){
				$total['nopass'] = $nopass;
			}else{
				$total['nopass'] = 0;
			}
			//待开讲讲座报名人数条数提醒
			$notread = pdo_fetchcolumn('select sum(notread) as notread from ' .tablename('rr_v_lectures').' where uniacid = :uniacid and 
				openid = :openid and isshow = 1 and start_time > "'.date('Y-m-d H:i:s').'" and releasetime>0',$params);
			if(empty($notread)){
				$notread['notread'] = 0;
			}
			
			$total['notread'] = $notread['notread'];

		}
	
		app_json(array('total' => $total));
	}

	//医生端讲座详情
	public function lecture_detail()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$id = intval($param['id']);
		$openid = 'sns_wa_'.$param['openid'];
		if(empty($id) || empty($param['openid'])){
			app_error(AppError::$ParamsError);
		}else{
			$sql = 'select * from ' .tablename('rr_v_lectures').' where uniacid = :uniacid and id = :id and openid =:openid';
			$res = pdo_fetch($sql,array(':id' => $id,':uniacid' => $uniacid, ':openid' => $openid));
			$time = date("Y-m-d H:i:s",$res['createtime']);
			$res['createtime'] = explode(' ', $time)[0];
			$res['cretime'] = explode(' ', $time)[1];
			if($res['releasetime'] != 0){
				$res['releasetime'] = date('Y-m-d H:i:s',$res['releasetime']);
			}
			$res['time'] = explode(' ',$res['start_time'])[1];
			$res['timeEnd'] = explode(' ',$res['end_time'])[1];
			$res['data'] = explode(' ', $res['end_time'])[0];
			$doc = 'select m.avatar,m.realname,m.nickname,m.level,d.job,d.hospital,d.parentid,d.departmentid from '.tablename('rr_v_member').' m, '.tablename('rr_v_member_doctors').' d where m.openid = d.openid and d.uniacid = :uniacid and d.openid = :openid and d.isdelete = 0';
			$doctor = pdo_fetch($doc,array(':uniacid' => $uniacid,':openid' => $openid));
			if($doctor['level'] == 0){
				$doctor['level'] = 'V0';
			}else{
				$level = pdo_fetch('select id,levelname from ' . tablename('rr_v_member_level') . ' where uniacid = :uniacid and id=:id  LIMIT 1', array(':uniacid' => $uniacid, ':id' => $doctor['level']));
				$doctor['level'] = $level['levelname'];
			}
			if($doctor['parentid'] != 0){
				$parentname = pdo_fetch('select id,name from '.tablename('rr_v_member_department').' where uniacid = :uniacid and id = :parentid and isshow = 1 ',array(':uniacid' => $uniacid,':parentid' => $doctor['parentid']));
			}else{
				$parentname['name'] = '';
			}
			if($doctor['departmentid'] != 0){
				$departname = pdo_fetch('select id,name from '.tablename('rr_v_member_department').' where uniacid = :uniacid and id = :departmentid and isshow = 1 ',array(':uniacid' => $uniacid,':departmentid' => $doctor['departmentid']));
			}else{
				$departname['name'] = '';
			}
			$res['doctor'] = $doctor;
			$res['doctor']['parentname'] = $parentname['name'];
			$res['doctor']['departname'] = $departname['name'];
			//返回患者关注医生的状态
			$followed = pdo_fetch('select id,isfollow from ' . tablename('rr_v_member_follow') . ' where uniacid = :uniacid and doc_openid=:doc_openid and pat_openid=:pat_openid  LIMIT 1', array(':uniacid' => $uniacid, ':doc_openid' => $doctor['openid'], ':pat_openid' => $openid));
			if(!empty($followed)){
				$res['isfollow'] = $followed['isfollow'];
			}else{
				$res['isfollow'] = 0;
			}
			

		}
		app_json(array('res' => $res));
	
	}

	//清除讲座红点提醒
	public function clear_remind()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$id = intval($_GPC['id']);
		$type = $_GPC['type'];
		if(empty($id) || empty($_GPC['type'])){
			app_error(AppError::$ParamsError);
		}

		if($type == '1'){
			
			//清除报名人数状态
			$status = pdo_update('rr_v_lectures',array('notread' => 0),array('uniacid' => $uniacid, 'id' => $id));

		}else{

			//清除讲座审核状态条数提醒状态
			$status = pdo_update('rr_v_lectures',array('nopass' => 0),array('uniacid' => $uniacid, 'id' => $id));
		}

		app_json(array('status' => $status));
	}

	//医生端发布讲座接口
	public function release_lecture()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		if(!(empty($id))){
			$res = pdo_update('rr_v_lectures',array('isshow' => 1,'releasetime' => time()),array('id' => $id));
		}
		if(!empty($res)){
			$result = array('res' => 'success');
		}else{
			$result = array('res' => 'fail');
		}
		app_json($result);
	}


}

?>
