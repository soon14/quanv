<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}
 
require rr_vv3_PLUGIN . 'app/core/page_mobile.php';
class Test_RrvV3Page extends AppMobilePage
{
	public function notice()
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
		// $openid = 'sns_wa_oH-n90ITOAn8POfASLhTREgLlKmI';
		if(empty($openid)){
			app_error(AppError::ParamsError);
		}else{
			//查询订单信息
			$order = pdo_fetchall('SELECT a.orderid,a.goodsid,a.optionname,b.ordersn,b.price,b.paytime,a.goodstype 
				FROM `ims_rr_v_order_goods` a,`ims_rr_v_order` b WHERE a.uniacid = :uniacid AND a.orderid=b.id 
				AND a.openid = :openid AND b.status=3 AND paytype = 21 ORDER BY b.id DESC '.$limit.' ',array(':uniacid' => $uniacid,':openid' => $openid));
			if(!empty($order)){
				
				foreach ($order as &$value) {
					$value['phone'] = $_W['shopset']['contact']['phone'];
					$value['paytime'] = date('Y-m-d H:i:s',$value['paytime']);
					//$order['goodstype']=1图文咨询
					if($value['goodstype'] ==1){
						
						$value['customer'] = pdo_fetchall('SELECT a.orderid,a.doctorid,a.consult_type,b.default_consult,b.highgrade_consult,c.realname,c.nickname 
							FROM '.tablename('rr_v_member_patients_diag').' a, '.tablename('rr_v_member_doctors').' b, '.tablename('rr_v_member').' c 
							WHERE a.uniacid = :uniacid AND a.isdelete =0 AND a.orderid >0 AND a.doctorid >0 and a.id = :id
							AND a.doctorid=b.id AND b.openid=c.openid ',array(':uniacid' => $uniacid,':id' => $value['goodsid']));
						
					}elseif($value['goodstype'] ==2){
						//$order['goodstype']=2视频播放
						$value['videos'] = pdo_fetchall('SELECT a.videoname,b.realname,b.nickname FROM '.tablename('rr_v_member_videos').' a,
							'.tablename('rr_v_member').' b WHERE a.uniacid = :uniacid AND a.id = :id AND a.openid=b.openid',array(':uniacid' => $uniacid,':id' => $value['goodsid']));

					}elseif($value['goodstype'] ==3){
						$value['articles'] = pdo_fetchall('SELECT a.article_title,b.realname,b.nickname FROM '.tablename('rr_v_articles').' a,
							'.tablename('rr_v_member').' b WHERE a.uniacid = :uniacid AND a.id = id AND a.openid=b.openid',array(':uniacid' => $uniacid,':id' => $value['goodsid']));
					}elseif($value['goodstype'] ==4){
						$value['lectures'] = pdo_fetchall('SELECT a.lecture_title,a.start_time,a.lecture_address,b.ordernumber,c.realname,c.nickname 
							FROM '.tablename('rr_v_lectures').' a,'.tablename('rr_v_lectures_log').' b,'.tablename('rr_v_member').' c
							WHERE a.uniacid = :uniacid AND a.id = b.lid AND a.id = :id AND a.openid=c.openid AND b.ordernumber <>"" and
							b.openid = :openid ',array(':uniacid' => $uniacid,':id' => $value['goodsid'],':openid' => $openid));
					}elseif($value['goodstype'] ==5){
						$value['consult'] = pdo_fetchall('SELECT a.start_time,a.address,b.ordernumber,c.realname,c.nickname FROM '.tablename('rr_v_consult').' a,
							'.tablename('rr_v_consult_log').' b,'.tablename('rr_v_member').' c WHERE a.uniacid = :uniacid AND a.isdelete =0 
							AND a.id = b.consultid AND a.id = :id AND a.openid=c.openid AND b.openid = :openid ',array(':uniacid' => $uniacid,':id' => $value['goodsid'],':openid' => $openid));
					}
				}//foreach
				
			}//if
		}

		var_dump($order);
		
	}




}

