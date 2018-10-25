<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}
 
require rr_vv3_PLUGIN . 'app/core/page_mobile.php';
class Comments_RrvV3Page extends AppMobilePage
{
	//视频、文章评论接口
	public function add_comments()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$type = $param['type'];//1是视频评论，2是嵌套视频评论,3是文章评论，4是嵌套文章评论
		$openid = 'sns_wa_'.$param['openid'];
		$pid = intval($param['pid']);
		$rid = intval($param['rid']);
		$cid = intval($param['cid']);
		$content = trim($param['content']);

		$status = 0;

		if(empty($pid) || empty($param['openid']) || empty($type) || empty($content)){
			app_error(AppError::ParamsError);
		}

		$data = array(
			'uniacid'		=>	$uniacid,
			'openid'		=>	$openid,
			'content'		=>	$content,
			'createtime'	=>	time(),
		);

		$condition = ' a.openid = b.openid AND a.uniacid = :uniacid and a.id=:id';
		$params = array(':uniacid' => $uniacid);
		if($type == 1 || $type == 2){
			$data['video_id'] = $pid;
			$condition .= ' AND a.video_id = :pid AND a.article_id = 0';

			$type_where = ' AND a.video_id =:pid AND a.article_id = 0';
			$where = 'and video_id = :pid AND article_id = 0';

		}elseif($type == 3 || $type == 4){
			$data['article_id'] = $pid;
			$condition .= ' AND a.article_id = :pid AND a.video_id = 0';

			$type_where = 'AND a.article_id =:pid AND a.video_id = 0';
			$where = 'and article_id = :pid AND video_id = 0';
		}
		if(!empty($rid)){
			$data['rid'] = $rid;
		}
		if(!empty($cid)){
			$data['cid'] = $cid;
		}
		if(!empty(trim($param['reply_name']))){
			$data['reply_name'] = trim($param['reply_name']);
		}
		if(!empty($data['video_id']) || !empty($data['article_id'])){
			pdo_insert('rr_v_comments',$data);
			$commentsid = pdo_insertid();
			$status = 1;
		}

		$params[':pid'] = $pid;

		if(!empty($commentsid) && empty($cid)){
			$params[':id'] = $commentsid;
		}else{
			$params[':id'] = $cid;
		}
		
		$result['status'] = $status;

		//返回评论数据
		$sql = 'SELECT a.*,b.realname,b.nickname,b.avatar FROM '.tablename('rr_v_comments').' a,'.tablename('rr_v_member').' b WHERE '.$condition.' ';
		$list = pdo_fetch($sql, $params);

		$list['createtime'] = date('Y-m-d H:i', $list['createtime']);
		$list['parent'] = array();
		if(!empty($cid)){
			$list['parent'] = pdo_fetchall('SELECT a.id,a.cid,a.rid,a.content,b.realname,b.nickname,b.avatar FROM '.tablename('rr_v_comments').' a,'.tablename('rr_v_member').' b WHERE a.openid = b.openid 
				AND a.uniacid = :uniacid AND a.cid = :cid AND a.rid = 0 '.$type_where.' ORDER BY a.id ASC', array(':uniacid' => $uniacid, ':pid' => $pid, ':cid' => $cid));
			if(!empty($list['parent'])){
				foreach ($list['parent'] as &$val) {
					$val['sons'] = pdo_fetchall('SELECT a.id,a.cid,a.rid,a.reply_name,a.content,b.realname,b.nickname,b.avatar FROM '.tablename('rr_v_comments').' a,'.tablename('rr_v_member').' b WHERE a.openid = b.openid 
								AND a.uniacid =:uniacid AND a.cid = :cid AND a.rid =:rid '.$type_where.' ORDER BY a.createtime', array(':uniacid' => $uniacid, ':pid' => $pid, ':cid' => $val['cid'], ':rid' => $val['id']));
					if(!empty($val['sons'])){
						foreach ($val['sons'] as &$val2) {
							if($val2['rid'] == $val['id']){
								$val2['replyname'] = $val['realname'];
								$val2['replynickname'] = $val['nickname'];
							}else{
								$val2['replyname'] = '';
								$val2['replynickname'] = '';
							}
							
						}
					}
					
				}

			}

			//自己是否评论
			$list['iscomments'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_comments') . ' where uniacid = :uniacid '.$where.' and cid = :cid and openid =:openid', array(':uniacid' => $uniacid, ':pid' => $pid, ':cid' => $cid, ':openid' => $openid));
		
		}else{
			$list['iscomments'] = 0;
		}
		
		//自己是否为评论点赞
		$list['islike'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_comments_like') . ' where uniacid = :uniacid and pid = :pid AND type =:type and openid =:openid', array(':uniacid' => $uniacid, ':pid' => $list['id'], ':openid' => $openid, ':type' => $type));

		//评论点赞数
		$list['commentslike_total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_comments_like') . ' where uniacid = :uniacid and pid = :pid AND type =:type', array(':uniacid' => $uniacid, ':pid' => $list['id'], ':type' => $type));

		
		//评论总数
		$list['comments_total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_comments') . ' where uniacid = :uniacid '.$where.' and cid = :cid and rid = 0 ', array(':uniacid' => $uniacid, ':pid' => $pid, ':cid' => $list['id']));	

		//总评论数
		$result['comments_total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_comments') . ' where uniacid = :uniacid '.$where.' and cid = 0 and rid = 0 ', array(':uniacid' => $uniacid, ':pid' => $pid));	

		$result['list'] = $list;

		app_json($result);
	}

	//获取视频、文章点赞数、评论数、评论结果
	public function comments_list()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$type = $param['type'];//1是视频评论，3是文章评论
		$openid = 'sns_wa_'.$param['openid'];
		$pid = intval($param['pid']);

		$page = intval($param['page']);
		$pageSize = intval($param['pageSize']);
		$limit = ' LIMIT ' . (($page - 1) * $pageSize) . ',' . $pageSize;

		if(empty($pid) || empty($param['openid']) || empty($type) || empty($page) || empty($pageSize)){
			app_error(AppError::ParamsError);
		}
		
		$condition = ' a.openid = b.openid AND a.uniacid = :uniacid AND a.rid = 0 AND a.cid = 0';
		$params = array(':uniacid' => $uniacid);
		//获取视频的信息
		if($type == 1){
			$condition .= ' AND a.video_id =:pid AND a.article_id = 0';
			$params[':pid'] = $pid;

			$type_where = ' AND a.video_id =:pid AND a.article_id = 0';
			$where = 'and video_id =:pid AND article_id = 0';

		}else{
		//获取文章的信息
			$condition .= ' AND a.article_id = :pid AND a.video_id = 0';
			$params[':pid'] = $pid;

			$type_where = 'AND a.article_id =:pid AND a.video_id = 0';
			$where = 'and article_id = :pid AND video_id = 0';
		}
		$sql = 'SELECT a.*,b.realname,b.nickname,b.avatar FROM '.tablename('rr_v_comments').' a,'.tablename('rr_v_member').' b WHERE '.$condition.' ORDER BY a.createtime DESC';
		$sql .= $limit;
		$list = pdo_fetchall($sql, $params);
		foreach ($list as &$value) {
			$value['createtime'] = date('Y-m-d H:i', $value['createtime']);
			$value['parent'] = pdo_fetchall('SELECT a.id,a.cid,a.rid,a.content,b.realname,b.nickname,b.avatar FROM '.tablename('rr_v_comments').' a,'.tablename('rr_v_member').' b WHERE a.openid = b.openid 
								AND a.uniacid =:uniacid AND a.cid =:cid AND a.rid = 0 '.$type_where.' ', array(':uniacid' => $uniacid, ':pid' => $pid, ':cid' => $value['id']));
			if(!empty($value['parent'])){
				foreach ($value['parent'] as &$val) {
					$val['sons'] = pdo_fetchall('SELECT a.id,a.cid,a.rid,a.reply_name,a.content,b.realname,b.nickname,b.avatar FROM '.tablename('rr_v_comments').' a,'.tablename('rr_v_member').' b WHERE a.openid = b.openid 
								AND a.uniacid =:uniacid AND a.cid =:cid AND a.rid =:rid '.$type_where.' ORDER BY a.createtime', array(':uniacid' => $uniacid, ':pid' => $pid, ':cid' => $val['cid'], ':rid' => $val['id']));
					if(!empty($val['sons'])){
						foreach ($val['sons'] as &$val2) {
							if($val2['rid'] == $val['id']){
								$val2['replyname'] = $val['realname'];
								$val2['replynickname'] = $val['nickname'];
							}else{
								$val2['replyname'] = '';
								$val2['replynickname'] = '';
							}
							
						}
					}
					
				}

			}
			
			//自己是否为评论点赞
			if($type == 1){
				$value['islike'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_comments_like') . ' where uniacid = :uniacid and pid = :pid AND type =2 and openid =:openid', array(':uniacid' => $uniacid, ':pid' => $value['id'], ':openid' => $openid));

				//评论点赞数
				$value['commentslike_total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_comments_like') . ' where uniacid = :uniacid and pid = :pid AND type =2', array(':uniacid' => $uniacid, ':pid' => $value['id']));
			}else{
				$value['islike'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_comments_like') . ' where uniacid = :uniacid and pid = :pid AND type =4 and openid =:openid', array(':uniacid' => $uniacid, ':pid' => $value['id'], ':openid' => $openid));

				//评论点赞数
				$value['commentslike_total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_comments_like') . ' where uniacid = :uniacid and pid = :pid AND type =4', array(':uniacid' => $uniacid, ':pid' => $value['id']));
			}
			
			//评论总数
			$value['comments_total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_comments') . ' where uniacid = :uniacid '.$where.' and cid = :cid and rid = 0 ', array(':uniacid' => $uniacid, ':pid' => $pid, ':cid' => $value['id']));
			
			//自己是否评论
			$value['iscomments'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_comments') . ' where uniacid = :uniacid '.$where.' and cid = :cid and openid =:openid', array(':uniacid' => $uniacid, ':pid' => $pid, ':cid' => $value['id'], ':openid' => $openid));
			
		}

		$comments_total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_comments') . ' where uniacid = :uniacid '.$where.' and rid = 0 and cid = 0 ', array(':uniacid' => $uniacid, ':pid' => $pid));

		app_json(array('list' => $list, 'comments_total' => $comments_total));

	}

	
	//小程序端文章、视频、评论点赞接口
	public function click_like()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$type = $param['type'];//1是视频点赞，2是视频评论点赞,3是文章点赞，4是文章评论点赞
		$openid = 'sns_wa_'.$param['openid'];
		$pid = intval($param['pid']);

		$status = 0;
		
		if(empty($pid) || empty($param['openid']) || empty($type)){
			app_error(AppError::ParamsError);
		}else{
			$condition = ' uniacid = :uniacid and openid=:openid and type=:type and pid=:pid';
			$params = array(':uniacid' => $uniacid,':openid' => $openid,':pid' => $pid,':type' => $type);
			$list = pdo_fetch('select * from '.tablename('rr_v_comments_like').' where '.$condition.' ',$params);

			if(empty($list)){
				pdo_insert('rr_v_comments_like',array('uniacid' => $uniacid, 'openid' => $openid, 'pid' => $pid, 'type' =>$type));
				$status = 1;
			}else{
				pdo_delete('rr_v_comments_like',array('uniacid' => $uniacid, 'openid' => $openid, 'pid' => $pid, 'type' =>$type));
				$status = 2;
			}
		}

		//点赞数
		$result['like_total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_comments_like') . ' where uniacid = :uniacid and pid = :pid AND type =:type ', array(':uniacid' => $uniacid, ':pid' => $pid, ':type' => $type));

		$result['status'] = $status;

		app_json($result);

	}

	//小程序端文章、视频转发接口
	public function turn()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$type = $param['type'];//1是文章，2是视频
		if(empty($type)){
			app_error(AppError::ParamsError);
		}
		if($type ==1){
			//转发文章
			$article_id = intval($param['article_id']);
			$res = pdo_fetch('select turn_nums from '.tablename('rr_v_articles').' where uniacid = :uniacid and id = :id ',array(':uniacid' => $uniacid,':id' => $article_id));
			$re = pdo_update('rr_v_articles',array('turn_nums' => $res['turn_nums']*1+1),array('id' => $article_id));
		}elseif($type ==2){
			//转发视频
			$video_id = $param['video_id'];
			$res = pdo_fetch('select turn_nums from '.tablename('rr_v_member_videos').' where uniacid = :uniacid and id = :id ',array(':uniacid' => $uniacid,':id' => $video_id));
			$turn_nums = $res['turn_nums']*1+1;	
			$re = pdo_update('rr_v_member_videos',array('turn_nums' => $turn_nums),array('id' => $video_id));
		}else{
			app_error(AppError::ParamsError);
		}
		if($re){
			$result = array('status' => 1);
		}else{
			$result = array('status' => 0);
		}

		$result['turn_nums'] = $res['turn_nums']*1+1;

		app_json($result);
	}

	//小程序端文章、视频收藏接口
	public function add_collection()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$type = intval($param['type']);//1是视频，2是文章
		$openid = 'sns_wa_'.$param['openid'];
		$pid = intval($param['pid']);

		$status = 0;
		
		if(empty($pid) || empty($param['openid']) || empty($type)){
			app_error(AppError::ParamsError);
		}else{
			$condition = ' uniacid = :uniacid and openid=:openid and type=:type and pid=:pid';
			$params = array(':uniacid' => $uniacid,':openid' => $openid,':pid' => $pid,':type' => $type);
			$list = pdo_fetch('select * from '.tablename('rr_v_member_collection').' where '.$condition.' ',$params);

			if(empty($list)){
				pdo_insert('rr_v_member_collection',array('uniacid' => $uniacid, 'openid' => $openid, 'pid' => $pid, 'type' =>$type, 'createtime' => time()));
				$status = 1;
			}else{
				pdo_delete('rr_v_member_collection',array('uniacid' => $uniacid, 'openid' => $openid, 'pid' => $pid, 'type' =>$type));
				$status = 2;
			}
		}

		//收藏数
		$result['collection_total'] = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('rr_v_member_collection') . ' where uniacid = :uniacid and pid = :pid AND type =:type ', array(':uniacid' => $uniacid, ':pid' => $pid, ':type' => $type));

		$result['status'] = $status;

		app_json($result);

	}

	//我的收藏
	public function my_collection()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$type = intval($param['type']);//1是视频，2是文章
		$openid = 'sns_wa_'.$param['openid'];
		
		$page = intval($param['page']);
		$pageSize = intval($param['pageSize']);
		$limit = ' LIMIT ' . (($page - 1) * $pageSize) . ',' . $pageSize;

		if(empty($param['openid']) || empty($type) || empty($page) || empty($pageSize)){
			app_error(AppError::ParamsError);
		}

		$condition = ' a.uniacid = :uniacid and a.openid=:openid and a.type=:type and a.pid=b.id';
		$params = array(':uniacid' => $uniacid, ':openid' => $openid, ':type' => $type);

		if($type == 1){
			$sql = 'SELECT b.id,a.pid,a.openid,a.createtime,b.fileid,b.videoname,b.img_url,b.money,b.content,b.playcount FROM '.tablename('rr_v_member_collection').' a,'.tablename('rr_v_member_videos').' b WHERE '.$condition.' ORDER BY a.createtime DESC';
		}else{
			$sql = 'SELECT b.id,a.pid,a.openid,a.createtime,b.article_title,b.article_introduction,b.cover_url,b.money,b.click_nums FROM '.tablename('rr_v_member_collection').' a,'.tablename('rr_v_articles').' b WHERE '.$condition.' ORDER BY a.createtime DESC';
		}
		load()->func('communication');

		$sql .= $limit;
		$list = pdo_fetchall($sql,$params);
		if(!empty($list)){
			foreach ($list as &$value) {
				$value['createtime'] = date('Y-m-d H:i', $value['createtime']);
				if($type == 1){

					$getParams = array('fileId' => $value['fileid'], 'infoFilter.0' => 'basicInfo', 'infoFilter.1' => 'transcodeInfo');
					$value['videoinfo'] = videoApi('GetVideoInfo', $getParams);
				}
			}
		}

		app_json(array('list' => $list));

	}


}

?>
