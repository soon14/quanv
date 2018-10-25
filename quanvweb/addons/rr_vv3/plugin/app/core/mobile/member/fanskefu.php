<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}
 
require rr_vv3_PLUGIN . 'app/core/page_mobile.php';
class Fanskefu_RrvV3Page extends AppMobilePage
{
	//医生端获取患者聊天记录列表
	public function messikefu_list()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$keyword = trim($param['keyword']);
		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}else{
			//获取医生公众号openid
			$memberid = pdo_fetch('select id,openid,memberid from ' . tablename('rr_v_member') . ' where uniacid=:uniacid and openid=:openid limit 1', array(':uniacid' => $uniacid, ':openid' => $openid));
			if(empty($memberid) && empty($memberid['memberid'])){
				app_json(array('message' => '您还未关注公众号，请先关注公众号！', 'status' => 0));
			}
			$gzh_openid = pdo_fetch('select id,openid,memberid from ' . tablename('rr_v_member') . ' where uniacid=:uniacid and id=:id ', array(':uniacid' => $uniacid, ':id' => $memberid['memberid']));
			$condition = ' weid=:weid and kefuopenid=:kefuopenid ';
			
			$condition2 = ' a.uniacid =:uniacid AND a.optionname = "图文咨询" AND a.goodstype = 1 AND b.status = 3 AND b.paytype = 21 AND c.openid = a.openid AND c.orderid > 0 AND c.doctorid > 0 AND d.openid =:openid';

			$join = '';
			$join .= ' LEFT JOIN ' . tablename('rr_v_order') . ' b ON a.orderid=b.id AND a.openid=b.openid';
			$join .= ' LEFT JOIN ' . tablename('rr_v_member_patients_diag') . ' c ON a.goodsid = c.id AND a.orderid = c.orderid';
			$join .= ' LEFT JOIN ' . tablename('rr_v_member_doctors') . ' d ON c.doctorid = d.id';

			$pat_openids = '';
			$pat_gzhopenids = '';
			$order = pdo_fetchall('SELECT a.openid pat_openid,a.goodsid,a.orderid,c.doctorid,d.openid doc_openid,c.doctorid FROM ' . tablename('rr_v_order_goods') . ' a '.$join.' where '.$condition2.' ', array(':uniacid' => $uniacid, ':openid' => $openid));
			if(!empty($order)){
				foreach ($order as &$value) {
					$diagcount = pdo_fetch('SELECT COUNT(*) num FROM ' . tablename('rr_v_member_patients_diag') . ' where uniacid =:uniacid and orderid > 0 
						and doctorid =:doctorid AND openid =:openid ', array(':uniacid' => $uniacid, ':doctorid' => $value['doctorid'], ':openid' => $value['pat_openid']));
					if(!empty($diagcount) && $diagcount['num'] <= 2){
						$pat_openids .= $value['pat_openid'].',';
					}
					
				}
				unset($value);

			}
			if(!empty($pat_openids)){
				$arr = explode(',', $pat_openids);
				
				foreach ($arr as &$value2) {
					if(!empty($value2)){
						$pat = m('member')->getMember($value2);
						if(!empty($pat['memberid'])){
							$pat_fans = m('member')->getMember($pat['memberid']);
							$pat_gzhopenids .= "'".$pat_fans['openid']."',";
						}
					}
					
				}
				unset($value2);
			}

			if($param['type'] == '1'){//新的咨询

				if(!empty($pat_gzhopenids)){
					// $condition .= ' and fansopenid IN('.rtrim($pat_gzhopenids,",").') and lasttime > kefulasttime';
					$condition .= ' and fansopenid IN('.rtrim($pat_gzhopenids,",").') and kefulasttime = 0';
				}else{
					$condition .= ' and kefulasttime < 0 ';
				}

			}elseif($param['type'] == '2'){//医生未回复的咨询

				// if(!empty($pat_gzhopenids)){
				// 	$condition .= ' and lasttime > kefulasttime and fansopenid NOT IN('.rtrim($pat_gzhopenids,",").')';
				// }else{
				// 	$condition .= ' and lasttime > kefulasttime ';
				// }
				$condition .= ' and lasttime > kefulasttime and kefulasttime != 0';

			}elseif($param['type'] == '3'){//历史记录
				// if(!empty($pat_gzhopenids)){
				// 	$condition .= ' and kefulasttime < "'.time().'" and kefulasttime > lasttime';
				// }else{
				// 	$condition .= ' and kefulasttime < "'.time().'" and notread = 0 and kefulasttime > lasttime';
				// }
				$condition .= ' and kefulasttime < "'.time().'" and kefulasttime > lasttime and lasttime != 0';
				if(!empty($keyword)){
					$condition .= ' and fansnickname LIKE "%'.$keyword.'%" ';
				}
			}

			$doc_member = pdo_fetch('select id,openid from ' . tablename('rr_v_member_doctors') . ' where uniacid=:uniacid and openid=:openid ', array(':uniacid' => $uniacid, ':openid' => $openid));

			$pat_list = pdo_fetchall('select fansopenid,fansnickname,fansavatar,lastcon,kefulastcon,lasttime,kefulasttime,notread from ' . tablename('messikefu_fanskefu') . ' where '.$condition.' ORDER BY kefulasttime DESC', array(':weid' => $uniacid, ':kefuopenid' => $gzh_openid['openid']));
			foreach ($pat_list as &$row) {
				$row['sort_time'] = $row['lasttime'];
				$row['lasttimes'] = $row['lasttime'];
				$member = m('member')->getMember($row['fansopenid']);
				$fans = m('member')->getMember($member['memberid']);
				$diag_log = pdo_fetch('select id diaglogid,name,age,sex,orderid from ' . tablename('rr_v_member_patients_diag') . ' where uniacid =:uniacid and openid=:openid and isdelete=0 and doctorid =:doctorid ORDER BY createtime DESC', array(':uniacid' => $uniacid, ':openid' => $fans['openid'], ':doctorid' => $doc_member['id']));
				if(empty($diag_log)){
					$row['sex'] = 0;
					$row['age'] = '';
					$row['name'] = '';
					$row['diaglogid'] = '';
				}else{
					$row['sex'] = $diag_log['sex'];
					$row['age'] = $diag_log['age'];
					$row['name'] = $diag_log['name'];
					$row['diaglogid'] = $diag_log['diaglogid'];
				}
				//返回时间特性
				if($row['kefulasttime'] == 0 && $row['lasttime'] == 0 && $diag_log['orderid'] != 0){
					$order = pdo_fetch('select id,openid,paytime from ' . tablename('rr_v_order') . ' where uniacid =:uniacid and openid=:openid and id =:id and paytime > 0 ', array(':uniacid' => $uniacid, ':id' => $diag_log['orderid'], ':openid' => $fans['openid']));
					$begin_time = $order['paytime'];
				}elseif($row['kefulasttime'] != 0 && $row['lasttime'] == 0){
					$begin_time = $row['kefulasttime'];
				}else{
					$begin_time = $row['lasttime'];
				}
				$end_time = time();
				$row['begin_time'] = $begin_time;
				$row['end_time'] = $end_time;
				if($begin_time < $end_time){
			        $starttime = $begin_time;
			        $endtime = $end_time;
			    }else{
			        $starttime = $end_time;
			        $endtime = $begin_time;
			    }
			    
			    $timediff = $endtime-$starttime;
			    //计算分钟数
			    if($timediff >= 0 && $timediff <= 3600){
			    	$mins = round(intval($timediff/60));
			    	if($mins < 3){
			    		$row['lasttime'] = '刚刚';
			    	}else{
			    		$row['lasttime'] = $mins.'分钟';
			    	}
			    }
			    //计算小时数
			    if($timediff > 3600 && $timediff < 86400){
			    	$hours = round(intval($timediff/3600));
			    	if($hours < 8){
			    		$row['lasttime'] = $hours.'小时前';
			    	}else{
			    		$row['lasttime'] = $row['lasttime'] > 0 ? date('m/d H:i', $row['lasttime']) : date('m/d H:i', $row['kefulasttime']);
			    	}
			    }
			    //计算天数
			    if($timediff >= 86400){
			    	$days = round(intval($timediff/86400));
			    	if($days <= 1){
			    		$row['lasttime'] = '昨天';
			    	}elseif($days > 1 && $days < 7){
			    		$row['lasttime'] = $days.'天前';
			    	}else{
			    		$row['lasttime'] = $row['lasttime'] > 0 ? date('Y-m-d H:i', $row['lasttime']) : date('Y-m-d H:i', $row['kefulasttime']);
			    	}
			    }	
			}
			foreach($pat_list as $key=>$val){  
			    $list[$key] = $val['sort_time'];  
			}  
			// array_multisort($list,SORT_DESC,$pat_list);
			array_multisort(array_column($pat_list,'sort_time'),SORT_DESC,$pat_list);
		}
		
		app_json(array('messifans_list' => $pat_list));

	}

	//医生端获取患者聊天记录列表数量
	public function messikefu_list_total()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}else{
			//获取医生公众号openid
			$memberid = pdo_fetch('select id,openid,memberid from ' . tablename('rr_v_member') . ' where uniacid=:uniacid and openid=:openid ', array(':uniacid' => $uniacid, ':openid' => $openid));
			if(empty($memberid['memberid'])){
				app_json(array('message' => '您还未关注公众号，请先关注公众号！', 'status' => 0));
			}
			$gzh_openid = pdo_fetch('select id,openid,memberid from ' . tablename('rr_v_member') . ' where uniacid=:uniacid and id=:id ', array(':uniacid' => $uniacid, ':id' => intval($memberid['memberid'])));
			//新咨询条数
			$condition2 = ' a.uniacid =:uniacid AND a.optionname = "图文咨询" AND a.goodstype = 1 AND b.status = 3 AND b.paytype = 21 AND c.openid = a.openid AND c.orderid > 0 AND c.doctorid > 0 AND d.openid =:openid';

			$join = '';
			$join .= ' LEFT JOIN ' . tablename('rr_v_order') . ' b ON a.orderid=b.id AND a.openid=b.openid';
			$join .= ' LEFT JOIN ' . tablename('rr_v_member_patients_diag') . ' c ON a.goodsid = c.id AND a.orderid = c.orderid';
			$join .= ' LEFT JOIN ' . tablename('rr_v_member_doctors') . ' d ON c.doctorid = d.id';

			$pat_openids = '';
			$pat_gzhopenids = '';
			$order = pdo_fetchall('SELECT a.openid pat_openid,a.goodsid,a.orderid,c.doctorid,d.openid doc_openid,c.doctorid FROM ' . tablename('rr_v_order_goods') . ' a '.$join.' where '.$condition2.' ', array(':uniacid' => $uniacid, ':openid' => $openid));
			if(!empty($order)){
				foreach ($order as &$value) {
					$diagcount = pdo_fetch('SELECT COUNT(*) num FROM ' . tablename('rr_v_member_patients_diag') . ' where uniacid =:uniacid and orderid > 0 
						and doctorid =:doctorid AND openid =:openid ', array(':uniacid' => $uniacid, ':doctorid' => $value['doctorid'], ':openid' => $value['pat_openid']));
					if(!empty($diagcount) && $diagcount['num'] <= 2){
						$pat_openids .= $value['pat_openid'].',';
					}
				}
				unset($value);

			}
			if(!empty($pat_openids)){
				$arr = explode(',', $pat_openids);
				
				foreach ($arr as &$value2) {
					if(!empty($value2)){
						$pat = m('member')->getMember($value2);
						if(!empty($pat['memberid'])){
							$pat_fans = m('member')->getMember($pat['memberid']);
							$pat_gzhopenids .= "'".$pat_fans['openid']."',";
						}
					}
					
				}
				unset($value2);
			}

			if(!empty($pat_gzhopenids)){
				$condition = ' and fansopenid IN('.rtrim($pat_gzhopenids,",").') and kefulasttime = 0';
				// $condition = ' and fansopenid IN('.rtrim($pat_gzhopenids,",").') and lasttime > kefulasttime';
				// $condition3 = ' and lasttime > kefulasttime and fansopenid NOT IN('.rtrim($pat_gzhopenids,",").')';
				// $condition4 = ' and kefulasttime < "'.time().'" and kefulasttime > lasttime ';
			}else{
				$condition = ' and kefulasttime < 0 ';
				// $condition3 = ' and lasttime > kefulasttime';
				// $condition4 = ' and kefulasttime < "'.time().'" and kefulasttime > lasttime';
			}
			$condition3 = ' and lasttime > kefulasttime and kefulasttime != 0';
			$condition4 = ' and kefulasttime < "'.time().'" and kefulasttime > lasttime and lasttime != 0';

			$new = pdo_fetchcolumn('select count(*) from ' .tablename('messikefu_fanskefu').' where weid=:weid and kefuopenid=:kefuopenid 
				'.$condition.' ',array(':weid' => $uniacid, ':kefuopenid' => $gzh_openid['openid']));
			if(!empty($new)){
				$total['new'] = $new;
			}else{
				$total['new'] = 0;
			}

			//未回复条数
			$unrecovered = pdo_fetchcolumn('select count(*) as notread from ' .tablename('messikefu_fanskefu').' where weid=:weid and 
				kefuopenid=:kefuopenid '.$condition3.' ',array(':weid' => $uniacid, ':kefuopenid' => $gzh_openid['openid']));
			if(!empty($unrecovered)){
				$total['unrecovered'] = $unrecovered;
			}else{
				$total['unrecovered'] = 0;
			}
			$history = pdo_fetchcolumn('select count(*) from ' .tablename('messikefu_fanskefu').' where weid=:weid and kefuopenid=:kefuopenid 
				'.$condition4.' ',array(':weid' => $uniacid, ':kefuopenid' => $gzh_openid['openid']));
			if(!empty($history)){
				$total['history'] = $history;
			}else{
				$total['history'] = 0;
			}
	
		}
		
		app_json(array('total' => $total));

	}


	//患者端获取医生聊天记录列表
	public function messifans_list()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$pageSize = intval($param['pageSize']);
		$page = intval(($param['page'])-1) * $pageSize;
		$limit = 'limit '.$page.','.$pageSize.' ';

		if (empty($param['openid']) || empty($param['page']) || empty($param['pageSize'])) {
			app_error(AppError::$ParamsError);
		}else{
			//获取医生公众号openid
			$memberid = pdo_fetch('select id,openid,memberid from ' . tablename('rr_v_member') . ' where uniacid=:uniacid and openid=:openid ', array(':uniacid' => $uniacid, ':openid' => $openid));
			if(empty($memberid['memberid'])){
				app_json(array('message' => '您还未关注公众号，请先关注公众号！', 'status' => 0));
			}
			$gzh_openid = pdo_fetch('select id,openid,memberid from ' . tablename('rr_v_member') . ' where uniacid=:uniacid and id=:id ', array(':uniacid' => $uniacid, ':id' => intval($memberid['memberid'])));
			$condition = ' weid=:weid and fansopenid=:fansopenid ';

			$doc_list = pdo_fetchall('select kefuopenid,kefunickname,kefuavatar,lastcon,kefulastcon,lasttime,kefulasttime,kefunotread from ' . tablename('messikefu_fanskefu') . ' where '.$condition.' ORDER BY lasttime DESC '.$limit, array(':weid' => $uniacid, ':fansopenid' => $gzh_openid['openid']));
			foreach ($doc_list as &$row) {
				$row['sort_time'] = $row['kefulasttime'];
				$row['kefulasttimes'] = $row['kefulasttime'];
				$member = m('member')->getMember($row['kefuopenid']);

				$docmember = m('member')->getMember($member['memberid']);
				$doc_member = pdo_fetch('select id,openid from ' . tablename('rr_v_member_doctors') . ' where uniacid=:uniacid and openid=:openid ', array(':uniacid' => $uniacid, ':openid' => $docmember['openid']));
				$diag_log = pdo_fetch('select id diaglogid,name,age,sex,orderid from ' . tablename('rr_v_member_patients_diag') . ' where uniacid =:uniacid and openid=:openid and isdelete=0 and doctorid=:doctorid ORDER BY createtime DESC', array(':uniacid' => $uniacid, ':openid' => $openid, ':doctorid' => $doc_member['id']));
				if(empty($diag_log)){
					$row['diaglogid'] = '';
				}else{
					$row['diaglogid'] = $diag_log['diaglogid'];
				}
				//返回时间特性
				if($row['kefulasttime'] == 0 && $row['lasttime'] == 0 && $diag_log['orderid'] != 0){
					$order = pdo_fetch('select id,openid,paytime from ' . tablename('rr_v_order') . ' where uniacid =:uniacid and openid=:openid and id =:id and paytime > 0 ', array(':uniacid' => $uniacid, ':id' => $diag_log['orderid'], ':openid' => $openid));
					$begin_time = $order['paytime'];
				}elseif($row['lasttime'] != 0){
					$begin_time = $row['lasttime'];
				}else{
					$begin_time = $row['kefulasttime'];
				}

				$end_time = time();
				$row['begin_time'] = $begin_time;
				$row['end_time'] = $end_time;
				if($begin_time < $end_time){
			        $starttime = $begin_time;
			        $endtime = $end_time;
			    }else{
			        $starttime = $end_time;
			        $endtime = $begin_time;
			    }
			    
			    $timediff = $endtime-$starttime;
			    //计算分钟数
			    if($timediff >= 0 && $timediff <= 3600){
			    	$mins = round(intval($timediff/60));
			    	if($mins < 3){
			    		$row['kefulasttime'] = '刚刚';
			    	}else{
			    		$row['kefulasttime'] = $mins.'分钟';
			    	}
			    }
			    //计算小时数
			    if($timediff > 3600 && $timediff < 86400){
			    	$hours = round(intval($timediff/3600));
			    	if($hours < 8){
			    		$row['kefulasttime'] = $hours.'小时前';
			    	}else{
			    		$row['kefulasttime'] = $row['kefulasttime'] > 0 ? date('m/d H:i', $row['kefulasttime']) : date('m/d H:i', $row['lasttime']);
			    	}
			    }
			    //计算天数
			    if($timediff >= 86400){
			    	$days = round(intval($timediff/86400));
			    	if($days <= 1){
			    		$row['kefulasttime'] = '昨天';
			    	}elseif($days > 1 && $days < 7){
			    		$row['kefulasttime'] = $days.'天前';
			    	}else{
			    		$row['kefulasttime'] = $row['kefulasttime'] > 0 ? date('Y-m-d H:i', $row['kefulasttime']) : date('Y-m-d H:i', $row['lasttime']);
			    	}
			    }
			}
			foreach($doc_list as $key=>$val){  
			    $list[$key] = $val['sort_time'];  
			}  
			// array_multisort($list,SORT_DESC,$doc_list); 
			array_multisort(array_column($doc_list,'sort_time'),SORT_DESC,$doc_list); 
		}
		
		app_json(array('messikefu_list' => $doc_list));

	}

	//患者端获取医生聊天记录数量
	public function messifans_list_total()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}else{
			//获取医生公众号openid
			$memberid = pdo_fetch('select id,openid,memberid from ' . tablename('rr_v_member') . ' where uniacid=:uniacid and openid=:openid ', array(':uniacid' => $uniacid, ':openid' => $openid));
			if(!empty($memberid['memberid'])){
				$gzh_openid = pdo_fetch('select id,openid,memberid from ' . tablename('rr_v_member') . ' where uniacid=:uniacid and id=:id ', array(':uniacid' => $uniacid, ':id' => intval($memberid['memberid'])));
				$condition = ' weid=:weid and fansopenid=:fansopenid and kefunotread > 0';

				$total = pdo_fetch('select count(*) number from ' . tablename('messikefu_fanskefu') . ' where '.$condition.' ', 
					array(':weid' => $uniacid, ':fansopenid' => $gzh_openid['openid']));
				if(empty($total)){
					$total['number'] = 0;
				}
			}else{
				$total['number'] = 0;
			}
			
	
		}
		
		app_json(array('total' => $total));

	}


}

?>
