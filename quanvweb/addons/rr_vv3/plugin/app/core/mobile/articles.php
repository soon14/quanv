<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}
 
require rr_vv3_PLUGIN . 'app/core/page_mobile.php';
class Articles_RrvV3Page extends AppMobilePage
{
	//医生端上传文章接口
	public function add_article()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		if(empty($param['openid'])){
			app_error(AppError::$ParamsError);
		}else{
				$data = array(
					'uniacid' => $uniacid,
					'openid' => $openid,
					'cover_url' => trim($param['cover_url']),
					'article_title' => trim($param['article_title']),
					'article_introduction' => trim($param['article_introduction']),
					// 'article_content' => serialize($param['article_content']),
					'click_nums' => trim($param['click_nums']),
					'money' => trim($param['money']),
					'status' => 0,
					'tag' => trim($param['tag']),
					'createtime' => time(),

				);
				if(empty($param['id'])){
					pdo_insert('rr_v_articles',$data);
					$result = array('res' => 'insert_success');
				}else{
					pdo_update('rr_v_articles',$data,array('uniacid' => $uniacid, 'id' => intval($param['id'])));
					$result = array('res' => 'update_success');
				}	
				
				app_json($result);	

		}		
	}

	//医生端端文章列表
	public function get_article_list()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$type = $param['type']; //type:0查审核，1查已发布
		$openid = 'sns_wa_'.$param['openid'];
		if(empty($param['openid'])){
			app_error(AppError::$ParamsError);
		}
		$params = array(':uniacid' => $uniacid,':openid' => $openid);
		$pageSize = intval($param['pageSize']);
		$page = intval(($param['page'])-1) * $pageSize;
		$limit = ' limit '.$page.','.$pageSize.' ';
		if ($type == 0){
			$where = ' where uniacid = :uniacid and openid = :openid and status IN(0,1,2) and isshow = 0 order by createtime desc';
		}else{
			$where = ' where uniacid = :uniacid and openid = :openid and isshow = 1 order by releasetime desc';
		}
		
		$sql = 'select id,cover_url,article_title,article_introduction,article_content,click_nums,money,status,isshow,createtime 
		from ' .tablename('rr_v_articles').' '.$where.'  '.$limit.' ';
		$list = pdo_fetchall($sql,$params);
		if(!empty($list)){
			foreach ($list as &$value) {
				$time = date("Y-m-d H:i:s",$value['createtime']);
				$value['createtime'] = explode(' ', $time)[0];
				$value['time'] = explode(' ', $time)[1];
				// $value['article_content'] = unserialize($value['article_content']);
				$value['article_content'] = str_replace('section', 'div', $value['article_content']);
			}
		}else{
			$list = array();
		}
		$total['total'] = count($list);
		app_json(array('list' => $list,'total' => $total,'page' => $page,'pageSize' => $pageSize));
	}

	//医生端端文章列表数量
	public function get_article_list_total()
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
			$audit = pdo_fetchcolumn('select count(*) from ' .tablename('rr_v_articles').' where uniacid = :uniacid and 
				openid = :openid and status IN(0,1,2) and isshow = 0',$params);
			if(!empty($audit)){
				$total['audit'] = $audit;
			}else{
				$total['audit'] = 0;
			}
			$release = pdo_fetchcolumn('select count(*) from ' .tablename('rr_v_articles').' where uniacid = :uniacid and 
				openid = :openid and isshow = 1',$params);
			if(!empty($release)){
				$total['release'] = $release;
			}else{
				$total['release'] = 0;
			}
		}
		app_json(array('total' => $total));
	}

	//医生端文章详情
	public function article_detail()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$id = intval($param['id']);
		if(empty($param['openid']) || empty($id)){
			app_error(AppError::$ParamsError);
		}
		$sql = 'select id,openid,cover_url,article_title,article_introduction,article_content,money,click_nums,turn_nums,status,isshow,tag,checked,
		createtime,releasetime from ' .tablename('rr_v_articles').' where uniacid = :uniacid and id = :id ';
		$res = pdo_fetch($sql,array(':id' => $id,':uniacid' => $uniacid));
		if(!empty($res)){

			$shop = m('common')->getSysset('shop');//平台抽成
			if(empty($shop['drawsprice'])){
				$drawsprice = 0;
			}else{
				$drawsprice = $shop['drawsprice']/100;
			}

			$res['article_money'] = $res['money'];
			$res['money'] = round(($res['money']*1 + $res['money']*$drawsprice),1);

			$time = date("Y-m-d H:i:s",$res['createtime']);
			$res['createtime'] = explode(' ', $time)[0];
			$res['time'] = explode(' ', $time)[1];
			// $res['article_content'] = unserialize($res['article_content']);
			$res['article_content'] = str_replace('section', 'div', $res['article_content']);
			$res['article_content'] = str_replace('"Microsoft YaHei"', '\'Microsoft YaHei\'', $res['article_content']);
			if(!empty($res['releasetime'])){
				$res['releasetime'] = date('Y-m-d H:i:s',$res['releasetime']);
			}

			//点赞数
			$res['like_total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_comments_like') . ' where uniacid = :uniacid and pid = :pid AND type =3 ', array(':uniacid' => $uniacid, ':pid' => $id));

			//评论数
			$res['comments_total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_comments') . ' where uniacid = :uniacid and article_id = :article_id AND video_id = 0 and cid = 0 and rid =0', array(':uniacid' => $uniacid, ':article_id' => $id));

			//收藏数
			$res['collection_total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_member_collection') . ' where uniacid = :uniacid and pid = :pid AND type =2', array(':uniacid' => $uniacid, ':pid' => $id));	

			//自己是否点赞
			$res['islike'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_comments_like') . ' where uniacid = :uniacid and pid = :pid and type =3 and openid =:openid ', array(':uniacid' => $uniacid, ':pid' => $id, ':openid' => $openid));
		
			//自己是否评论
			$res['iscomments'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_comments') . ' where uniacid = :uniacid and article_id = :pid AND video_id = 0 and openid =:openid', array(':uniacid' => $uniacid, ':pid' => $id, ':openid' => $openid));

			//自己是否收藏
			$res['iscollection'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_member_collection') . ' where uniacid = :uniacid and pid = :pid AND type =2 and openid =:openid', array(':uniacid' => $uniacid, ':pid' => $id, ':openid' => $openid));

			//购买数
			$res['pay_total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_order_goods') . ' a,' . tablename('rr_v_order') . ' b where a.uniacid = :uniacid AND a.orderid=b.id AND b.status=3 AND b.paytype = 21 AND a.goodstype = 3 AND a.goodsid =:pid ', array(':uniacid' => $uniacid, ':pid' => $id));

			$doc = 'select m.realname,m.nickname,m.avatar,m.level,d.id doctorid,d.openid,d.job,d.hospital,d.parentid,d.departmentid,d.specialty,
			d.default_consult,d.highgrade_consult from '.tablename('rr_v_member').' m, '.tablename('rr_v_member_doctors').' d 
			where m.openid = d.openid and d.uniacid = :uniacid and d.openid = :openid ';
			$doctor = pdo_fetch($doc,array(':uniacid' => $uniacid,':openid' => $res['openid']));

			if(!empty($doctor)){

				$doctor['default_consult'] = round(($doctor['default_consult']*1 + $doctor['default_consult']*$drawsprice),1);
				$doctor['highgrade_consult'] = round(($doctor['highgrade_consult']*1 + $doctor['highgrade_consult']*$drawsprice),1);

			}

			if($doctor['level'] == 0){
					$doctor['level'] = 'V0';
				}else{
					$level = pdo_fetch('select id,levelname from ' . tablename('rr_v_member_level') . ' where uniacid = :uniacid and id=:id  LIMIT 1', array(':uniacid' => $uniacid, ':id' => $doctor['level']));
					$doctor['level'] = $level['levelname'];
				}
			if($doctor['parentid'] != 0){
				$re = pdo_fetch('select name from '.tablename('rr_v_member_department').' where uniacid = :uniacid and id = :parentid ',array('uniacid' => $uniacid,':parentid' => $doctor['parentid']));
				$doctor['par_name'] = $re['name'];
			}
			if($doctor['departmentid'] != 0){
				$re = pdo_fetch('select name from '.tablename('rr_v_member_department').' where uniacid = :uniacid and id = :departmentid ',array('uniacid' => $uniacid,':departmentid' => $doctor['departmentid']));
				$doctor['depar_name'] = $re['name'];
			}
			unset($doctor['parentid']);
			unset($doctor['departmentid']);
			if(!empty($doctor['specialty'])){
				$arr = array();
				$doctor['specialty'] = explode(',', $doctor['specialty']);
				foreach ($doctor['specialty']  as $v) {
					$specialty = pdo_fetch('select title from '.tablename('rr_v_search').' where uniacid = :uniacid and id = :id and enabled = 1 and iswxapp = 1 ',array(':uniacid' => $uniacid,':id' => $v));
					$arr[] = $specialty['title'];
					$doctor['specialty'] = $arr;
				}

			}
			
			//返回患者关注医生的状态
			$followed = pdo_fetch('select id,isfollow from ' . tablename('rr_v_member_follow') . ' where uniacid = :uniacid and doc_openid=:doc_openid and pat_openid=:pat_openid  LIMIT 1', array(':uniacid' => $uniacid, ':doc_openid' => $doctor['openid'], ':pat_openid' => $openid));
				if(!empty($followed)){
					$res['isfollow'] = $followed['isfollow'];
				}else{
					$res['isfollow'] = 0;
				}
			//返回该患者文章购买状态
			$order = pdo_fetch('select a.orderid,a.goodsid,b.ordersn,b.price,a.goodstype from '.tablename('rr_v_order_goods').' a,'.tablename('rr_v_order').' b where a.uniacid = :uniacid AND a.orderid=b.id AND b.status=3 AND b.paytype = 21 AND a.goodsid = :goodsid AND a.openid = :openid ',array(':uniacid' => $uniacid,':goodsid' => $id, ':openid' => $openid));
			if(empty($order)){
				$res['paystatus'] = 0;
			}else{
				$res['paystatus'] = 1;
			}
			$res['doctor'] = $doctor;
		}else{
			$res = array();
		}
		
		
		
		app_json(array('res' => $res));
		
	}

	//医生端发布文章接口
	public function release_article()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		if(!(empty($id))){
			$res = pdo_update('rr_v_articles',array('isshow' => 1,'releasetime' => time()),array('id' => $id));
		}
		if($res){
			$result = array('res' => 'success');
		}else{
			$result = array('res' => 'fail');
		}
		app_json($result);
	}

	//文章浏览数点击+1
	public function add_click_nums()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		if(!(empty($id))){
			$res = pdo_query("UPDATE ".tablename('rr_v_articles')." SET click_nums = click_nums+1 WHERE id = :id", array(':id' => $id));
		}
		if(!empty($res)){
			$result = array('status' => 1);
		}else{
			$result = array('status' => 0);
		}
		app_json($result);
	}

	//患者端首页获取文章更多
	public function search_articles()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$pageSize = intval($param['pageSize']);
		$page = intval(($param['page'])-1) * $pageSize;
		$limit = ' limit '.$page.','.$pageSize;
		$keyword = trim($param['keyword']);
		if(empty($param['page']) || empty($param['pageSize'])){
			app_error(AppError::$ParamsError);
		}else{

			$params = array(':uniacid' => $uniacid);
			$condition = ' uniacid = :uniacid and isshow=1 and status=2 ';
			if(!empty($keyword)){
				if(is_numeric($keyword)){
					$doc_list = pdo_fetchall('SELECT id,openid FROM ' . tablename('rr_v_member_doctors') . ' WHERE uniacid=:uniacid AND parentid =:id OR departmentid=:id ', array(':uniacid' => $uniacid, ':id' => intval($keyword)));
					foreach ($doc_list as &$val) {
						if(!empty($val)){
							$openid_str .= '"'.$val['openid'].'",';
						}
					}
					$condition .= ' and openid IN('.rtrim($openid_str,',').') ';
				}else{
					$params[':keyword'] = '%'.$keyword.'%';
					$condition .= ' and (article_title LIKE :keyword OR article_introduction LIKE :keyword OR tag LIKE :keyword) ';

					//记录搜索关键词
					$hotsearch = pdo_fetchall('SELECT id,content,search_count FROM ' . tablename('rr_v_hotsearch') . ' WHERE uniacid=:uniacid AND content LIKE :keyword ', array(':uniacid' => $uniacid, ':keyword' => $params[':keyword']));
					if(empty($hotsearch)){
						pdo_insert('rr_v_hotsearch', array('uniacid'=>$uniacid, 'type'=>2, 'content'=>$keyword, 'creattime'=>time()));
					}else{
						foreach ($hotsearch as &$row) {
							pdo_update('rr_v_hotsearch', array('search_count' => $row['search_count']+1), array('uniacid' => $uniacid, 'type' => 2));
						}
					}
				}
			}
			if($param['type'] == '1'){
				$condition .= ' and id IN(SELECT f.article_id FROM (SELECT article_id,COUNT(*) mycount FROM '.tablename('rr_v_comments').' WHERE uniacid = :uniacid AND video_id = 0 AND cid = 0 AND rid = 0 GROUP BY article_id ORDER BY mycount DESC) f )';
			}elseif($param['type'] == '2'){
				$condition .= ' and money = 0 ORDER BY `releasetime` DESC';
			}elseif($param['type'] == '3'){
				$condition .= ' ORDER BY `releasetime` DESC';
			}

			$shop = m('common')->getSysset('shop');//平台抽成
			if(empty($shop['drawsprice'])){
				$drawsprice = 0;
			}else{
				$drawsprice = $shop['drawsprice']/100;
			}


			$sql = 'SELECT id,article_title,cover_url,article_introduction,money,click_nums,tag,releasetime FROM ' . tablename('rr_v_articles') . ' where ' . $condition . ' ';
			$sql .= $limit;
			$list = pdo_fetchall($sql, $params);
			if(!empty($list)){
				foreach ($list as &$value) {
					$value['releasetime'] = date('Y-m-d', $value['releasetime']);

					$value['money'] = round(($value['money']*1 + $value['money']*$drawsprice),1);
				}
				unset($value);
			}else{
				$list = array();
			}
			
		}
		$total['total'] = count($list);
		app_json(array('list' => $list,'total' => $total));
	}


	

}

?>
