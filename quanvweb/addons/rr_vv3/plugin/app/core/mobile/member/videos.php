<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}
 
require rr_vv3_PLUGIN . 'app/core/page_mobile.php';
class Videos_RrvV3Page extends AppMobilePage
{
	//云端视频信息
	public function videoInfo()
	{
		global $_W;
		global $_GPC;
		$param = $_GPC['param'];

		load()->func('communication');

		if(empty($param['fileId'])){
			app_error(AppError::$ParamsError);
		}

		$fileId = trim($param['fileId']);
		$getParams = array('fileId' => $fileId, 'infoFilter.0' => 'basicInfo', 'infoFilter.1' => 'transcodeInfo', 'infoFilter.2' => 'snapshotByTimeOffsetInfo');
		$resultArray = videoApi('GetVideoInfo', $getParams);


		app_json(array('result' => $resultArray));

	}

	//云端视频指定时间点截图
	public function set_videoImage()
	{
		global $_W;
		global $_GPC;
		$param = $_GPC['param'];

		load()->func('communication');

		$fileId = trim($param['fileId']);
		$getParams = array('fileId' => $fileId, 'definition' => 10, 'timeOffset.0' => 9000);
		$resultArray = videoApi('CreateSnapshotByTimeOffset', $getParams);


		app_json(array('result' => $resultArray));

	}

	//获取医生的视频数据
	public function get_data()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];

		if(empty($param['openid'])){
			app_error(AppError::$ParamsError);
		}else{

			$page = intval($param['page']);
			$pageSize = intval($param['pageSize']);

			if($param['type'] == '1'){//全部已发布的

				$condition = ' openid=:openid and  uniacid = :uniacid and isshow = 1';
				
			}else{//全部审核的

				$condition = ' openid=:openid and  uniacid = :uniacid and status IN(0,1,2) and isshow = 0';

			}
			$params = array(':uniacid' => $uniacid, ':openid' => $openid);
			
			$sql = 'SELECT * FROM ' . tablename('rr_v_member_videos') . ' where ' . $condition . ' ORDER BY `createtime` DESC LIMIT ' . ($page - 1) * $pageSize . ',' . $pageSize;
			$list = pdo_fetchall($sql, $params);
			if(!empty($list)){

				foreach ($list as &$val) {
					$val['createtime'] = date("Y-m-d H:i:s", $val['createtime']);
					if($val['sendtime'] != 0){
						$val['sendtime'] = date("Y-m-d", $val['sendtime']);
					}
					load()->func('communication');

					$getParams = array('fileId' => $val['fileid'], 'infoFilter.0' => 'basicInfo', 'infoFilter.1' => 'snapshotByTimeOffsetInfo');
					$val['videoinfo'] = videoApi('GetVideoInfo', $getParams);
					
				}
				unset($val);

			}
			
			app_json(array('list' => $list, 'page' => $page, 'pageSize' => $pageSize));
		}

	}
	//医生端视频列表数量
	public function get_videos_total()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];

		if(empty($param['openid'])){
			app_error(AppError::$ParamsError);
		}else{
			$params = array(':uniacid' => $uniacid, ':openid' => $openid);
			
			$audit = pdo_fetchcolumn('select count(*) from ' .tablename('rr_v_member_videos').' where uniacid = :uniacid and 
			openid = :openid and status = 0 and isshow = 0',$params);
			if(!empty($audit)){
				$total['audit'] = $audit;
			}else{
				$total['audit'] = 0;
			}
			$release = pdo_fetchcolumn('select count(*) from ' .tablename('rr_v_member_videos').' where uniacid = :uniacid and 
				openid = :openid and isshow = 1',$params);
			if(!empty($release)){
				$total['release'] = $release;
			}else{
				$total['release'] = 0;
			}
							
		}
			app_json(array('total' => $total));

	}

	public function get_detail()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$id = intval($param['id']);

		if(empty($param['openid']) || empty($id)){
			app_error(AppError::$ParamsError);
		}else{

			$shop = m('common')->getSysset('shop');//平台抽成
			if(empty($shop['drawsprice'])){
				$drawsprice = 0;
			}else{
				$drawsprice = $shop['drawsprice']/100;
			}

			$condition = ' f.uniacid = :uniacid and f.id = :id ';
			$params = array(':uniacid' => $uniacid, ':id' => $id);

			$join = '';
			$join .= ' join ' . tablename('rr_v_member_videos') . ' f on f.openid=dm.openid ';
			$join .= ' join ' . tablename('rr_v_member_doctors') . ' b on f.openid=b.openid ';

			$sql = 'select b.id doctorid,dm.realname,dm.mobile,dm.nickname,dm.avatar,dm.openid_wa,dm.identity,dm.memberid,dm.level,b.hospital,b.job,b.specialty,
			b.parentid,b.departmentid,b.default_consult,b.highgrade_consult,f.* from ' . tablename('rr_v_member') . ' dm ' . $join . ' where ' . $condition . '  ';
			
			$list = pdo_fetch($sql, $params);
			if(!empty($list)){
				load()->func('communication');

				$list['money'] = round(($list['money']*1 + $list['money']*$drawsprice),1);
				$list['default_consult'] = round(($list['default_consult']*1 + $list['default_consult']*$drawsprice),1);
				$list['highgrade_consult'] = round(($list['highgrade_consult']*1 + $list['highgrade_consult']*$drawsprice),1);

				$getParams = array('fileId' => $list['fileid'], 'infoFilter.0' => 'basicInfo', 'infoFilter.1' => 'transcodeInfo', 'infoFilter.2' => 'snapshotByTimeOffsetInfo');
				$list['videoinfo'] = videoApi('GetVideoInfo', $getParams);
			}
			$list['createtime'] = date("Y-m-d H:i:s", $list['createtime']);
			if($list['sendtime'] != 0){
				$list['sendtime'] = date("Y-m-d H:i:s",$list['sendtime']);
				$list['sendtime'] = explode(' ', $list['sendtime'])[0];
			}
			
			if($list['departmentid'] != 0){
				$departmentid = $list['departmentid'];
			}else{
				$departmentid = $list['parentid'];
			}
			$res = pdo_fetch('select id,name from ' . tablename('rr_v_member_department') . ' where uniacid = :uniacid and id=:id  LIMIT 1', array(':uniacid' => $uniacid, ':id' => $departmentid));
			if(!empty($res)){
				$list['department'] = $res['name'];
			}else{
				$list['department'] = '';
			}
			
			if($list['level'] == 0){
				$list['level'] = 'V0';
			}else{
				$level = pdo_fetch('select id,levelname from ' . tablename('rr_v_member_level') . ' where uniacid = :uniacid and id=:id  LIMIT 1', array(':uniacid' => $uniacid, ':id' => $list['level']));
				$list['level'] = $level['levelname'];
			}
			//擅长
			// if(!empty($val['specialty'])){
			// 	$arr = explode(',', $val['specialty']);
			// 	$searchArr = array();
			// 	foreach ($arr as &$row) {
			// 		$search = pdo_fetch('select id,title from ' . tablename('rr_v_search') . ' where uniacid = :uniacid and id=:id  LIMIT 1', array(':uniacid' => $uniacid, ':id' => $row));
			// 		$searchArr[] = $search['title'];
			// 	}
			// 	$val['specialty'] = $searchArr;
			// }

			//点赞数
			$list['like_total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_comments_like') . ' where uniacid = :uniacid and pid = :pid AND type =1 ', array(':uniacid' => $uniacid, ':pid' => $id));

			//评论数
			$list['comments_total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_comments') . ' where uniacid = :uniacid and video_id = :video_id AND article_id = 0 and cid = 0 and rid =0', array(':uniacid' => $uniacid, ':video_id' => $id));

			//收藏数
			$list['collection_total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_member_collection') . ' where uniacid = :uniacid and pid = :pid AND type =1 ', array(':uniacid' => $uniacid, ':pid' => $id));	

			//自己是否点赞
			$list['islike'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_comments_like') . ' where uniacid = :uniacid and pid = :pid and type =1 and openid =:openid ', array(':uniacid' => $uniacid, ':pid' => $id, ':openid' => $openid));
		
			//自己是否评论
			$list['iscomments'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_comments') . ' where uniacid =:uniacid and video_id =:pid AND article_id = 0 and rid =0 and openid =:openid', array(':uniacid' => $uniacid, ':pid' => $id, ':openid' => $openid));

			//自己是否收藏
			$list['iscollection'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_member_collection') . ' where uniacid =:uniacid and pid = :pid AND type =1 and openid =:openid', array(':uniacid' => $uniacid, ':pid' => $id, ':openid' => $openid));
			
			
			//返回患者关注医生的状态
			$followed = pdo_fetch('select id,isfollow from ' . tablename('rr_v_member_follow') . ' where uniacid =:uniacid and doc_openid=:doc_openid and pat_openid=:pat_openid  LIMIT 1', array(':uniacid' => $uniacid, ':doc_openid' => $list['openid'], ':pat_openid' => $openid));
				if(!empty($followed)){
					$list['isfollow'] = $followed['isfollow'];
				}else{
					$list['isfollow'] = 0;
				}
			//返回该患者文章购买状态
			$order = pdo_fetch('select a.orderid,a.goodsid,b.ordersn,b.price,a.goodstype from '.tablename('rr_v_order_goods').' a,'.tablename('rr_v_order').' b where a.uniacid = :uniacid AND a.orderid=b.id AND b.status=3 AND b.paytype = 21 AND a.goodsid = :goodsid 
				AND a.openid = :openid ',array(':uniacid' => $uniacid,':goodsid' => $id, ':openid' => $openid));
			if(empty($order)){
				$list['paystatus'] = 0;
			}else{
				$list['paystatus'] = 1;
			}
			
			app_json(array('list' => $list));
		}

	}

	//视频点播数
	public function add_playcount()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$id = intval($_GPC['id']);

		if(empty($id)){
			app_error(AppError::$ParamsError);
		}

		$list = pdo_fetch('SELECT * FROM ' . tablename('rr_v_member_videos') . ' where uniacid=:uniacid and id=:id limit 1', array(':uniacid' => $uniacid, ':id' => $id));

		$status = pdo_update('rr_v_member_videos', array('playcount' => $list['playcount']+1), array('uniacid' => $uniacid, 'id' => $id));

		app_json(array('status' => $status));

	}

	public function add()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$status = 0;
		if (empty($param['openid']) || empty($param['videoname']) || empty($param['video_url'])) {
			app_error(AppError::$ParamsError);
		}else{
			$data = array(
				'uniacid' 		=> $uniacid,
				'openid'  		=> $openid,
				'fileid' 		=> $param['fileid'],
				'videoname' 	=> $param['videoname'],
				'video_url' 	=> $param['video_url'],
				'content' 		=> $param['content'],
				'money' 		=> $param['money'],
				'tag'			=> $param['tag'],
				'createtime' 	=> time(),
			);
			pdo_insert('rr_v_member_videos', $data);
			$status = 1;
			
		}
		
		$result['status'] = $status;
		
		app_json($result);

	}

	public function edit()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$id = intval($param['id']);
		$status = 0;

		if(empty($id) || empty($param['openid']) || empty($param['videoname']) || empty($param['video_url']) || empty($param['fileid'])){
			app_error(AppError::$ParamsError);
		}else{

			$data = array(
				'fileid' 		=> $param['fileid'],
				'videoname' 	=> $param['videoname'],
				'video_url' 	=> $param['video_url'],
				'content' 		=> $param['content'],
				'money' 		=> $param['money'],
				'tag' 			=> $param['tag'],
				'status' 		=> 0,
			);

			$video = pdo_fetch('SELECT id,fileid,videoname FROM ' . tablename('rr_v_member_videos') . ' where uniacid=:uniacid and id=:id and openid=:openid limit 1', array(':uniacid' => $uniacid, ':id' => $id, ':openid' => $openid));
			if(!empty($video)){
				if($param['fileid'] != $video['fileid']){

					load()->func('communication');

					//删除云端原来的视频
					$getParams = array('fileId' => $video['fileid'], 'isFlushCdn' => 1, 'priority' => 0);
					$res = videoApi('DeleteVodFile', $getParams);

				}
			}
			
			pdo_update('rr_v_member_videos', $data, array('uniacid' => $uniacid, 'id' => $id, 'openid' => $openid));
			$status = 1;
			
		}

		$result['status'] = $status;
		
		app_json($result);

	}

	public function enabled()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$status = 0;
		$id = intval($param['id']);

		if (empty($id) || empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}else{
			pdo_update('rr_v_member_videos', array('isshow' => 1, 'sendtime' => time()), array('uniacid' => $uniacid, 'id' => $id, 'openid' => $openid));
			$status = 1;
		}

		$result['status'] = $status;
		
		app_json($result);
	}

	//患者端首页获取视频更多
	public function search_video()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$pageSize = intval($param['pageSize']);
		$page = intval(($param['page'])-1) * $pageSize;
		$limit = ' limit '.$page.','.$pageSize.' ';
		$keyword = $param['keyword'];
		if(empty($param['page']) || empty($param['pageSize'])){
			app_error(AppError::$ParamsError);
		}else{

			$params = array(':uniacid' => $uniacid);
			$condition = ' uniacid = :uniacid and isshow=1 and status=1 ';
			
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
					$condition .= ' and (videoname LIKE :keyword OR content  LIKE :keyword)';

					//记录搜索关键词
					$hotsearch = pdo_fetchall('SELECT id,content,search_count FROM ' . tablename('rr_v_hotsearch') . ' WHERE uniacid=:uniacid AND content LIKE :keyword ', array(':uniacid' => $uniacid, ':keyword' => $params[':keyword']));
					if(empty($hotsearch)){
						pdo_insert('rr_v_hotsearch', array('uniacid'=>$uniacid, 'type'=>1, 'content'=>$keyword, 'creattime'=>time()));
					}else{
						foreach ($hotsearch as &$row) {
							pdo_update('rr_v_hotsearch', array('search_count' => $row['search_count']+1), array('uniacid' => $uniacid, 'type' => 1));
						}
					}
				}
			}
			if($param['type'] == '1'){
				$condition .= ' and id IN(SELECT f.video_id FROM (SELECT video_id,COUNT(*) mycount FROM '.tablename('rr_v_comments').' WHERE uniacid = :uniacid AND article_id = 0 AND cid = 0 AND rid = 0 GROUP BY video_id ORDER BY mycount DESC) f )';
			}elseif($param['type'] == '2'){
				$condition .= ' and money=0 ORDER BY `sendtime` DESC';
			}elseif($param['type'] == '3'){
				$condition .= ' ORDER BY `sendtime` DESC';
			}

			$shop = m('common')->getSysset('shop');//平台抽成
			if(empty($shop['drawsprice'])){
				$drawsprice = 0;
			}else{
				$drawsprice = $shop['drawsprice']/100;
			}

			$sql = 'SELECT id,fileid,videoname,img_url,content,money,playcount,sendtime FROM ' . tablename('rr_v_member_videos') . ' where ' . $condition . ' ';
			$sql .= $limit;
			$list = pdo_fetchall($sql, $params);
			if(!empty($list)){
				load()->func('communication');

				foreach ($list as &$value) {
					$value['sendtime'] = date('Y-m-d', $value['sendtime']);

					$value['money'] = round(($value['money']*1 + $value['money']*$drawsprice),1);

					$getParams = array('fileId' => $value['fileid'], 'infoFilter.0' => 'basicInfo', 'infoFilter.1' => 'transcodeInfo', 'infoFilter.2' => 'snapshotByTimeOffsetInfo');
					$value['videoinfo'] = videoApi('GetVideoInfo', $getParams);
				}
				unset($value);
			}else{
				$list = array();
			}
			
		}
		
		app_json(array('list' => $list));//返回视频列表
	}

	//视频24小时过后自动发布
	public function TimingSendVideos()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$status = 0;
		$message = '操作失败';

		$video = pdo_fetchall('SELECT * FROM '.tablename('rr_v_member_videos').' WHERE uniacid =:uniacid and status=1 and isshow=0 and audittime>0', array(':uniacid' => $uniacid));
		if(!empty($video)){
			foreach ($video as &$value) {
				$starttime = strtotime('+1 day',$value['audittime']);
				if($starttime > time()){//超过24小时
					pdo_update('rr_v_member_videos', array('isshow' => 1, 'sendtime' => time()), array('uniacid' => $uniacid, 'id' => $value['id'], 'openid' => $value['openid']));
				}
			}
			unset($value);

			$status = 1;
			$message = '操作成功';

		}

		$result['message'] = $message;
		$result['status'] = $status;
		
		app_json($result);
	}



}

?>
