<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}
 
require rr_vv3_PLUGIN . 'app/core/page_mobile.php';
class Patients_RrvV3Page extends AppMobilePage
{
	//获取患者病情资料库列表
	public function get_diag_list()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$pageSize = intval($param['pageSize']);
		$page = intval(($param['page'])-1) * $pageSize;
		$limit = ' limit '.$page.','.$pageSize.' ';
		$params = array(':uniacid' => $uniacid,':openid' => $openid,':isdelete' => 0);
		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}else{
			$sql = 'select * from '.tablename('rr_v_member_patients_diag').' where uniacid = :uniacid and openid = :openid and isdelete = :isdelete and orderid=0 and doctorid=0 order by createtime desc '.$limit.' ';
			$list = pdo_fetchall($sql,$params);
			foreach ($list as &$value) {
				if(!empty($value['diag_thumbs'])){
					$value['diag_thumbs'] = unserialize($value['diag_thumbs']);  
				}else{
					$value['diag_thumbs'] = array();
				}                                                                                                                                 
				$value['createtime'] = date('Y-m-d H:i:s',$value['createtime']);
			}

			
		}
		
		app_json(array('list' => $list));

	}

	//患者添加、编辑病情接口
	public function edit_diag()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$id = $param['id'];//病情id
		if(empty($param['openid'])){
			app_error(AppError::$ParamsError);
		}	
			$data = array(
				'uniacid' 		=> $uniacid,
				'openid' 		=> $openid,
				'name' 			=>  trim($param['name']),
				'mobile' 		=>  trim($param['mobile']),
				'sex' 			=>  intval($param['sex']),
				'age' 			=> trim($param['age']),
				'title'			=> trim($param['title']),
				'diag_doctor' 	=> intval($param['diag_doctor']),
				'diag_day' 		=> intval($param['diag_day']), 
				'content' 		=> trim($param['content']), 
				'createtime' 	=> time(),
			);
			if(is_array($param['diag_thumbs'])){
				$data['diag_thumbs'] = serialize($param['diag_thumbs']);
			}
			if(empty($id)){
				$res = pdo_insert('rr_v_member_patients_diag',$data);
				if($res){
					$result = 1;
				}else{
					$result = 0;
				}
			}else{
				$res = pdo_update('rr_v_member_patients_diag',$data,array('id' => $id));
				if($res){
					$result = 1;
				}else{
					$result = 0;
				}
			}
		
		app_json(array('res' => $result));

	}

	//患者删除病情接口
	public function delete_diag()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$id = $param['id'];//病情id
		if(empty($id)) {
			app_error(AppError::$ParamsError);
		}else{
			$res = pdo_update('rr_v_member_patients_diag',array('isdelete' => 1),array('id' => $id));
			if($res){
				$result = $res;
			}else{
				$result = 0;
			}
			
		}
		
		app_json(array('res' =>$result));

	}

	//保存资料到患者病情
	public function save_patdata(){
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$status = 0;

		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}else{
			//保存资料到患者病情
			$data = array(
				'uniacid' 		=>  $uniacid,
				'openid' 		=>  $openid,
				'name' 			=>  trim($param['name']),
				'mobile' 		=>  trim($param['mobile']),
				'title' 		=> 	trim($param['title']),
				'sex' 			=>  intval($param['sex']),
				'age' 			=>  trim($param['age']),
				'content' 		=>  trim($param['content']),  
				'createtime' 	=>  time(),
			);
			if(is_array($param['diag_thumbs'])){
				$data['diag_thumbs'] = serialize($param['diag_thumbs']);
			}
			pdo_insert('rr_v_member_patients_diag',$data);
			$diagid = pdo_insertid();
			if(!empty($param['doctorid'])){
				$result['diaglogid'] = $diagid;
				pdo_update('rr_v_member_patients_diag',array('doctorid' => intval($param['doctorid']), 'consult_type' => intval($param['consult_type'])),array('uniacid' => $uniacid, 'id' => $diagid, 'openid' => $openid));
			}
			$status = 1;
		}
		$result['status'] = $status;
		
		app_json($result);

	}


	//患者端我的关注
	public function my_focus()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$pageSize = intval($param['pageSize']);
		$page = intval(($param['page'])-1) * $pageSize;
		$limit = ' limit '.$page.','.$pageSize.' ';
		// $limit = 'limit 0 ,5';
		$openid = 'sns_wa_'.$param['openid'];
		if(empty($param['openid'])){
			app_error(AppError::$ParamsError);
		}
		$sql = 'select id,doc_openid from '.tablename('rr_v_member_follow').' where uniacid = :uniacid and pat_openid = :pat_openid ';
		$res = pdo_fetchall($sql.$limit,array(':uniacid' => $uniacid,':pat_openid' => $openid));

		$shop = m('common')->getSysset('shop');//平台抽成
		if(empty($shop['drawsprice'])){
			$drawsprice = 0;
		}else{
			$drawsprice = $shop['drawsprice']/100;
		}

		if(!empty($res)){
			//关注的医生列表不为空
			foreach ($res as &$value) {
				//拿$value['doc_openid']查医生
				$openid_str .= "'".$value['doc_openid']."',";//拼接openid字符串
			}
			$openid_str = rtrim($openid_str,",");
			$params = array(':uniacid' => $uniacid,':isdelete' => 0);
				$condition = ' and d.openid = m.openid and d.isdelete = :isdelete and d.openid in ('.$openid_str.') and d.isaudit = 1 order by d.createtime desc ';
				$doctors = pdo_fetchall('select m.nickname,m.realname,m.avatar,m.level,m.memberid,d.id as doctorid,d.openid,d.job,d.hospital,d.parentid,d.departmentid,d.specialty,d.default_consult,d.highgrade_consult,d.field from '.tablename('rr_v_member_doctors').' d,'.tablename('rr_v_member').' m where d.uniacid = :uniacid '.$condition.'  ',$params);
			if(!empty($doctors)){
				foreach ($doctors as &$value) {
					if($value['level']==0){
						$value['level'] = 'V0';
					}else{
						$level = pdo_fetch('select id,levelname from ' . tablename('rr_v_member_level') . ' where uniacid = :uniacid and id=:id  LIMIT 1', array(':uniacid' => $uniacid, ':id' => $value['level']));
						$value['level'] = $level['levelname'];
					}
					if($value['parentid'] != 0){
						$res = pdo_fetch('select name from '.tablename('rr_v_member_department').' where uniacid = :uniacid and id = :parentid ',array('uniacid' => $uniacid,':parentid' => $value['parentid']));
						$value['par_name'] = $res['name'];
					}
					if($value['departmentid'] != 0){
						$re = pdo_fetch('select name from '.tablename('rr_v_member_department').' where uniacid = :uniacid and id = :departmentid ',array('uniacid' => $uniacid,':departmentid' => $value['departmentid']));
						$value['depar_name'] = $re['name'];
					}

					$value['default_consult'] = round(($value['default_consult']*1 + $value['default_consult']*$drawsprice),1);
					$value['highgrade_consult'] = round(($value['highgrade_consult']*1 + $value['highgrade_consult']*$drawsprice),1);

					// if($value['specialty'] != null){
					// 	$arr = array();
					// 	$value['specialty'] = explode(',', $value['specialty']);
					// 	foreach ($value['specialty']  as $v) {
					// 		$specialty = pdo_fetch('select title from '.tablename('rr_v_search').' where uniacid = :uniacid and id = :id and enabled = 1 and iswxapp = 1 ',array(':uniacid' => $uniacid,':id' => $v));
					// 		$arr[] = $specialty['title'];
					// 		$value['specialty'] = $arr;
					// 	}

					// }
					unset($value['parentid']);
					unset($value['departmentid']);
					//获取医生公众号openid
					if(!empty($value['memberid'])){
						$gzh_openid = pdo_fetch('select id,openid from ' . tablename('rr_v_member') . ' where uniacid=:uniacid and id=:id ', array(':uniacid' => $uniacid, ':id' => intval($value['memberid'])));
						$value['gzh_openid'] = $gzh_openid['openid'];
					}else{
						$value['gzh_openid'] = '';
					}


					//判断患者咨询是否过期
					$diag_log = pdo_fetch('select id diaglogid,name,age,sex,mobile,diag_thumbs,content,consult_type,doctorid,orderid,createtime from ' . tablename('rr_v_member_patients_diag') . ' where uniacid =:uniacid and openid=:openid and isdelete=0 ORDER BY id DESC limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
					
					$pat_member = m('member')->getMember($openid);
					if(empty($pat_member['memberid']) || $value['gzh_openid'] == ''){
						$value['isoverdue'] = 2;
					}elseif(!empty($pat_member['memberid']) && $value['gzh_openid'] != ''){
						$fans = m('member')->getMember($pat_member['memberid']);

						$fkid = pdo_fetch("SELECT id,fansopenid FROM " . tablename('messikefu_fanskefu') . " WHERE weid = {$_W['uniacid']} AND kefuopenid = '{$value['gzh_openid']}' AND fansopenid = '{$fans['openid']}'");
						if($diag_log['doctorid'] > 0){
							if($diag_log['consult_type'] == 1){
								$consult_total['total'] = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('messikefu_chat') . " where weid={$_W['uniacid']} AND openid='{$value['gzh_openid']}' and fkid={$fkid['id']} and time > {$diag_log['createtime']} ");
								
								if($consult_total['total'] >= 6){
									$value['isoverdue'] = 0;
								}elseif($consult_total['total'] != 0){
									$value['isoverdue'] = 1;
									$value['diaglogid'] = $diag_log['diaglogid'];
								}else{
									if(!empty($fkid)){
										$value['isoverdue'] = 1;
										$value['diaglogid'] = $diag_log['diaglogid'];
									}else{
										$value['isoverdue'] = 0;
									}
								}

							}elseif($diag_log['consult_type'] == 2){

								$startDate = strtotime('+7 day',strtotime(date('Y-m-d H:i:s',$diag_log['createtime'])));

								$endDate = pdo_fetch("SELECT id,time FROM " . tablename('messikefu_chat') . " where weid={$_W['uniacid']} AND openid='{$value['gzh_openid']}' and fkid={$fkid['id']} and time > {$diag_log['createtime']}  ORDER BY time DESC");
								if($startDate < $endDate['time']){
									$value['isoverdue'] = 0;
								}elseif(!empty($endDate) && $startDate > $endDate['time']){
									$value['isoverdue'] = 1;
									$value['diaglogid'] = $diag_log['diaglogid'];
								}else{
									if(!empty($fkid)){
										$value['isoverdue'] = 1;
										$value['diaglogid'] = $diag_log['diaglogid'];
									}else{
										$value['isoverdue'] = 0;
									}
								}

							}
						}

					}else{
						$value['isoverdue'] = 0;
					}


				}
			}else{
				$doctors = array();
			
			}
		
		}else{
			$doctors = array();
		}
		app_json(array('list' => $doctors));
	}

	//患者端我的讲座
	public function my_lectures()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$pageSize = intval($param['pageSize']);
		$page = intval(($param['page'])-1) * $pageSize;
		$limit = ' limit '.$page.','.$pageSize.' ';
		$openid = 'sns_wa_'.$param['openid'];//患者openid
		$type = $param['type'];//1是本月，2是下月，3往期历史开讲记录
   		if(empty($type) || empty($param['openid'])){
   			app_error(AppError::$ParamsError);
   		}

   		$res = pdo_fetchall('SELECT lid,ordernumber FROM '.tablename('rr_v_lectures_log').' where uniacid=:uniacid AND ordernumber<>"" and openid=:openid ORDER BY id DESC',array(':uniacid' => $uniacid,':openid' => $openid));
   		if(!empty($res)){
   			$ids = '';
   			foreach ($res as &$row) {
   				$ids .= $row['lid'].',';
   			}

			$condition = ' b.uniacid = :uniacid and m.openid = b.openid and b.isshow = 1 and b.releasetime > 0 and b.id IN('.rtrim($ids,',').') ';
   			if($type == '1'){
   				//本月开讲
	   			$firstDay = date('Y-m-d H:i');
	   			$lastDay = date('Y-m-t H:i');
	   			$condition .= ' and "'.$firstDay.'" < b.start_time and b.end_time < "'.$lastDay.'" ORDER BY b.start_time ';
   			}elseif($type == '2'){
   				//下月开讲
	   			$firstDay = date('Y-m-01',strtotime('+1 month'));
	   			$lastDay = date('Y-m-t',strtotime('+1 month'));
	   			$condition .= ' and "'.$firstDay.'" < b.start_time and b.end_time < "'.$lastDay.'" ORDER BY b.start_time ';
   			}elseif($type == '3'){
   				//往期历史开讲记录
	   			$condition .= ' and b.end_time < "'.date('Y-m-d H:i').'" ORDER BY b.start_time DESC ';
   			}
   			$sql = 'SELECT m.avatar,m.realname,m.nickname,b.id,b.openid,b.cover_url,b.lecture_author,b.lecture_title,b.lecture_address,b.lecture_cost,
   					b.start_time,b.end_time FROM '.tablename('rr_v_member').' m,' .tablename('rr_v_lectures').' b where '.$condition.' ';
   			$sql .= $limit;
   			$params = array(':uniacid' => $uniacid);
   			$list = pdo_fetchall($sql,$params);
   			if(!empty($list)){
   				foreach ($list as &$value) {
					$value['time'] = explode(' ',$value['start_time'])[1];
					$value['timeEnd'] = explode(' ',$value['end_time'])[1];
					$value['data'] = explode(' ', $value['end_time'])[0];
					//查询报名人数
					$peoplenum['peoplenum'] = pdo_fetchcolumn('select count(*) as peoplenum from '.tablename('rr_v_lectures_log').' where uniacid = :uniacid 
						and ordernumber <>"" and lid = :id ',array(':uniacid' => $uniacid,':id' => $value['id']));
					if(empty($peoplenum['peoplenum'])){
						$value['peoplenum'] = 0;
					}else{
						$value['peoplenum'] = $peoplenum['peoplenum'];
					}
				
				}
				unset($value);
   			}
   			
   		}else{
   			$list = array();
   		}

		app_json(array('list' => $list));
	}

	//患者端我的购买
	public function my_buy()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$pageSize = intval($param['pageSize']);
		$page = intval(($param['page'])-1) * $pageSize;
		$type = intval($param['type']);
		$limit = ' limit '.$page.','.$pageSize.' ';
		$openid = 'sns_wa_'.$param['openid'];//患者openid
		$type = $param['type'];//商品类型，默认无，1表示图文咨询，2表示视频点播，3表示文章查阅,4表示讲座报名
		// $openid = 'sns_wa_oH-n90ITOAn8POfASLhTREgLlKmI';
		// $type = 3;
		// $limit = 'limit 0,10';
   		if(empty($type) || empty($param['openid'])){
   			app_error(AppError::$ParamsError);
   		}

   		$shop = m('common')->getSysset('shop');//平台抽成
		if(empty($shop['drawsprice'])){
			$drawsprice = 0;
		}else{
			$drawsprice = $shop['drawsprice']/100;
		}
   		
   		$params = array(':uniacid' => $uniacid,':openid' => $openid);
   		$condition = ' WHERE a.uniacid = :uniacid AND a.orderid=b.id AND a.openid = :openid AND b.status = 3 AND paytype = 21 and 
   		a.goodstype = '.$type.' ';
   		$sql = 'SELECT a.orderid,a.goodsid,b.ordersn,b.price,b.paytime,a.goodstype FROM '.tablename('rr_v_order_goods').' a,'.tablename('rr_v_order').' 
   		b '.$condition.' '.$limit.' ';
   		$res = pdo_fetchall($sql,$params);
   		if(!empty($res)){
   			$mylist = array();
   			if($type == 2){
   				$table = tablename('rr_v_member_videos');
   			}elseif($type == 3){
   				$table = tablename('rr_v_articles');
   			}
   			foreach ($res as $value) {
   				//拿$value['goodsid']查询
   				$list = pdo_fetch('select * from '.$table.' where uniacid = :uniacid and id = :id and isshow = 1 ',array(':uniacid' => $uniacid,':id' => $value['goodsid']));
   				// $list['releasetime'] = date('Y-m-d H:i:s',$list['releasetime']);
   				$list['paytime'] = explode(' ', date('Y-m-d H:i:s',$value['paytime']))[0];

   				$list['money'] = round(($list['money']*1 + $list['money']*$drawsprice),1);
   				if($type == 2){
   					load()->func('communication');

					$getParams = array('fileId' => $list['fileid'], 'infoFilter.0' => 'basicInfo', 'infoFilter.1' => 'transcodeInfo', 'infoFilter.2' => 'snapshotByTimeOffsetInfo');
					$list['videoinfo'] = videoApi('GetVideoInfo', $getParams);
   				}
   				$mylist[] = $list;
   			}
   			
   		}else{
   			app_json(array('list' => array()));
   		}

   		app_json(array('list' => $mylist));

	}

	//患者端当面咨询病情分类接口
	public function diagtype()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$params = array(':uniacid' => $uniacid);
		$diagtype = pdo_fetchall('select id,diagname from '.tablename('rr_v_diagtype').' where uniacid = :uniacid 
			and isshow = 1 order by createtime desc ',$params);
		
		app_json(array('diagtype' => $diagtype));


	}

	//患者端讲座报名接口
	public function notice()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$params = array(':uniacid' => $uniacid);
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];//患者openid
		$id = $param['id'];//讲座id
		$result = 0;
		if(empty($param['openid']) || empty($id)){
			app_error(AppError::ParamsError);
		}else{
			$re = pdo_fetchall('select * from '.tablename('rr_v_lectures_log').' where uniacid = :uniacid 
				and lid = :id and openid = :openid and ordernumber = "" ',array(':uniacid' => $uniacid,':openid' => $openid,':id' => $id));

			$data = array(
				'uniacid' => $uniacid,
				'openid' => $openid,
				'lid' => $id,
				'createtime' => time(),
			);
			if(empty($re)){
				$res = pdo_insert('rr_v_lectures_log',$data);
				if($res){
					$result = 1;
				}
			}else{
				pdo_delete('rr_v_lectures_log',array('uniacid' => $uniacid,'openid' => $openid,'lid' => $id,'ordernumber' => ''));
				$res = pdo_insert('rr_v_lectures_log',$data);
				if($res){
					$result = 1;
				}
			}
			$plid = pdo_insertid();
			
		}

		app_json(array('lec_logid' => $plid, 'res' => $result));
	}

	//患者端通知列表接口
	public function my_notice()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$params = array(':uniacid' => $uniacid);
		$param = $_GPC['param'];
		$pageSize = intval($param['pageSize']);
		$page = intval(($param['page'])-1) * $pageSize;
		$limit = ' limit '.$page.','.$pageSize.' ';
		$openid = 'sns_wa_'.$param['openid'];
		
		if(empty($param['openid'])){
			app_error(AppError::ParamsError);
		}
		else{
			//查询订单信息
			$order = pdo_fetchall('SELECT a.orderid,a.goodsid,a.optionname,b.ordersn,b.price,b.paytime,a.goodstype 
				FROM '.tablename('rr_v_order_goods').' a,'.tablename('rr_v_order').' b WHERE a.uniacid = :uniacid AND a.orderid=b.id 
				AND a.openid = :openid AND b.status=3 AND paytype = 21 ORDER BY b.id DESC '.$limit.' ',array(':uniacid' => $uniacid,':openid' => $openid));
			if(!empty($order)){
				
				foreach ($order as &$value) {
					$value['phone'] = $_W['shopset']['contact']['phone'];
					$value['paytime'] = date('Y-m-d H:i:s',$value['paytime']);

					$paylog = pdo_fetch('select uniontid,tid from '.tablename('core_paylog').' where uniacid = :uniacid and tid = :tid and openid = :openid and uniontid<>"" and status = 1 ',array(':uniacid' => $uniacid,':tid' => $value['ordersn'], ':openid' => $openid));
					if(!empty($paylog)){
						$value['uniontid'] = $paylog['uniontid'];
					}
					//$order['goodstype']=1图文咨询
					if($value['goodstype'] == 1){
						
						$value['customer'] = pdo_fetchall('SELECT a.orderid,a.doctorid,a.consult_type,b.default_consult,b.highgrade_consult,c.realname,c.nickname,c.avatar
							FROM '.tablename('rr_v_member_patients_diag').' a, '.tablename('rr_v_member_doctors').' b, '.tablename('rr_v_member').' c 
							WHERE a.uniacid = :uniacid AND a.isdelete =0 AND a.orderid >0 AND a.doctorid >0 and a.id = :id
							AND a.doctorid=b.id AND b.openid=c.openid ',array(':uniacid' => $uniacid,':id' => $value['goodsid']));
						
					}elseif($value['goodstype'] == 2){
						//$order['goodstype']=2视频播放
						$value['videos'] = pdo_fetchall('SELECT a.videoname,b.realname,b.nickname,b.avatar FROM '.tablename('rr_v_member_videos').' a,
							'.tablename('rr_v_member').' b WHERE a.uniacid = :uniacid AND a.id = :id AND a.openid=b.openid',array(':uniacid' => $uniacid,':id' => $value['goodsid']));

					}elseif($value['goodstype'] == 3){
						$value['articles'] = pdo_fetchall('SELECT a.article_title,b.realname,b.nickname,b.avatar FROM '.tablename('rr_v_articles').' a,
							'.tablename('rr_v_member').' b WHERE a.uniacid = :uniacid AND a.id = :id AND a.openid=b.openid',array(':uniacid' => $uniacid,':id' => $value['goodsid']));
					}elseif($value['goodstype'] == 4){
						$value['lectures'] = pdo_fetchall('SELECT a.lecture_title,a.start_time,a.lecture_address,b.ordernumber,c.realname,c.nickname,c.avatar 
							FROM '.tablename('rr_v_lectures').' a,'.tablename('rr_v_lectures_log').' b,'.tablename('rr_v_member').' c
							WHERE a.uniacid = :uniacid AND a.id = b.lid AND b.id = :id AND a.openid=c.openid AND b.ordernumber <>"" and
							b.openid = :openid ',array(':uniacid' => $uniacid,':id' => $value['goodsid'],':openid' => $openid));
					}elseif($value['goodstype'] == 5){
						$value['consult'] = pdo_fetchall('SELECT a.start_time,a.address,b.ordernumber,c.realname,c.nickname,c.avatar FROM '.tablename('rr_v_consult').' a,
							'.tablename('rr_v_consult_log').' b,'.tablename('rr_v_member').' c WHERE a.uniacid = :uniacid AND a.isdelete =0 
							AND a.id = b.consultid AND a.id = :id AND a.openid=c.openid AND b.openid = :openid ',array(':uniacid' => $uniacid,':id' => $value['goodsid'],':openid' => $openid));
					}elseif($value['goodstype'] == 6){
						//获取送心意
						$reward = pdo_fetchall('SELECT a.doc_openid,a.orderid,a.remark,a.paytime,b.title,b.icon,b.price,c.realname,c.nickname,c.avatar FROM '.tablename('rr_v_member_doctors_reward').' a,
							'.tablename('rr_v_reward').' b,'.tablename('rr_v_member').' c WHERE a.uniacid = :uniacid AND a.rewardid = b.id 
							AND a.orderid > 0 AND a.id = :id AND a.doc_openid = c.openid AND a.pat_openid = :openid ',array(':uniacid' => $uniacid,':id' => $value['goodsid'],':openid' => $openid));
						if(!empty($reward)){
							foreach ($reward as &$val) {
								$val['icon'] = tomedia($val['icon']);
							}
							$value['reward'] = $reward;
						}
					}
				}//foreach
				
			}//if
		}

		app_json(array('list' =>$order));
		
	}

	//患者端当面预约完成，确认当面预约
	public function confirm_consult()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$consultid = intval($param['consultid']);//预约id
		$consult_logid = intval($param['consult_logid']);//患者预约记录表id
		$status = 0;

		if (empty($param['openid']) || empty($consultid) || empty($consult_logid)) {
			app_error(AppError::$ParamsError);
		}else{

			if(!empty($param['consultid'])){
				pdo_update('rr_v_consult_log',array('confirmtime' => time()),array('uniacid' => $uniacid, 'id' => $consult_logid, 'consultid' => $consultid, 'openid' => $openid));
			}
			$status = 1;
		}
		$result['status'] = $status;
		
		app_json($result);

	}

	//患者打赏医生
	public function reward_doc()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$doc_openid = $param['doc_openid'];
		$pat_openid = 'sns_wa_'.$param['pat_openid'];
		$rewardid = intval($param['rewardid']);//打赏id

		$status = 0;

		if (empty($param['doc_openid']) || empty($param['pat_openid']) || empty($rewardid)) {
			app_error(AppError::$ParamsError);
		}else{

			$shop = m('common')->getSysset('shop');//平台抽成
			if(empty($shop['drawsprice'])){
				$drawsprice = 0;
			}else{
				$drawsprice = $shop['drawsprice'];
			}

			$data = array(
				'uniacid' 		=> $uniacid,
				'doc_openid'  	=> $doc_openid,
				'pat_openid' 	=> $pat_openid,
				'rewardid' 		=> $rewardid,
				'price' 		=> ''.round(($param['price']*((100 - $drawsprice)/100)),1),//打赏钱
				'remark'		=> trim($param['remark']),//对医生说的话
				'createtime' 	=> time(),
			);
			
			pdo_insert('rr_v_member_doctors_reward', $data);
			$rewarddocid = pdo_insertid();
			$status = 1;
			$result['rewarddocid'] = $rewarddocid;
		}

		$result['status'] = $status;
		
		app_json($result);
	}

}

?>
