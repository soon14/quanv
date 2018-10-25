<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}
 
require rr_vv3_PLUGIN . 'app/core/page_mobile.php';
class Vindex_RrvV3Page extends AppMobilePage
{

	//全V患者首页数据接口
	public function get_pat_index()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$appsql = '';
		if ($this->iswxapp) {
			$appsql = ' and show_mode = 1 and iswxapp = 1';
		}
		$data = array();
		//首页背景图
		$banners = pdo_fetchall('select id,bannername,link,thumb,show_mode from ' . tablename('rr_v_banner') . ' where uniacid=:uniacid' . $appsql . ' and enabled = 1 order by displayorder desc', array(':uniacid' => $uniacid));
		$banners = set_medias($banners, 'thumb');
		$data['banners'] = array('type'=>'首页背景图','banners' => $banners);

		//首页名医推荐
		$doctors = pdo_fetchall('select m.nickname,m.realname,m.avatar,m.level,d.id,d.openid,d.job,d.hospital,d.parentid,d.departmentid from 
			'.tablename('rr_v_member_doctors').' d,'.tablename('rr_v_member').' m where d.uniacid = :uniacid and d.openid = m.openid 
			and d.isdelete = 0 and d.isaudit = 1 and d.isfamous = 1 order by d.displayorder desc limit 0,9 ',array(':uniacid' => $uniacid)); 
		foreach ($doctors as &$value) {
			if($value['level'] == 0){
					$value['level'] = 'V0';
				}else{
					$level = pdo_fetch('select id,levelname from ' . tablename('rr_v_member_level') . ' where uniacid = :uniacid and id=:id  LIMIT 1', array(':uniacid' => $uniacid, ':id' => $value['level']));
					$value['level'] = $level['levelname'];
				}
			if($value['parentid'] != 0){
				$res = pdo_fetch('select name from '.tablename('rr_v_member_department').' where uniacid = :uniacid and id = :parentid ',array('uniacid' => $uniacid,':parentid' => $value['parentid']));
				if(empty($res)){
					$res['name'] = '';
				}
				$value['par_name'] = $res['name'];
			}
			if($value['departmentid'] != 0){
				$re = pdo_fetch('select name from '.tablename('rr_v_member_department').' where uniacid = :uniacid and id = :departmentid ',array('uniacid' => $uniacid,':departmentid' => $value['departmentid']));
				if(empty($re)){
					$re['name'] = '';
				}
				$value['depar_name'] = $re['name'];
			}
			unset($value['parentid']);
			unset($value['departmentid']);
		}
		$data['doctors'] = array('type' => '名医推荐','doctors' => $doctors);

		//首页讲座
		$lectures = pdo_fetchall('select a.id,a.cover_url,a.lecture_author,a.lecture_title,a.lecture_introduction,a.lecture_address,a.lecture_cost,
			b.realname,b.nickname,b.avatar from '.tablename('rr_v_lectures').' a,'.tablename('rr_v_member').' b where a.openid = b.openid 
			and a.uniacid = :uniacid and a.isshow = :isshow and a.end_time > "'.date('Y-m-d H:i').'" ORDER BY a.start_time limit 3',array(':uniacid' => $uniacid,':isshow' => 1));
		$data['lectures'] = array('type' => '全V健康讲座','lectures' => $lectures);

		//首页视频列表
		$videos = pdo_fetchall('select a.id,a.fileid,a.img_url,a.videoname,a.money,b.realname,b.nickname,b.avatar from '.tablename('rr_v_member_videos').'
		 	a, '.tablename('rr_v_member').' b where a.openid = b.openid and a.uniacid = :uniacid 
			and a.isshow = :isshow and a.id IN(SELECT f.video_id FROM (SELECT video_id,COUNT(*) mycount FROM '.tablename('rr_v_comments').' WHERE uniacid = :uniacid AND article_id = 0 AND cid = 0 AND rid = 0 GROUP BY video_id ORDER BY mycount DESC) f ) 
			limit 5',array(':uniacid' => $uniacid,':isshow' => 1));
		if(empty($videos)){
			$videos = pdo_fetchall('select a.id,a.fileid,a.img_url,a.videoname,a.money,b.realname,b.nickname,b.avatar from '.tablename('rr_v_member_videos').'
		 	a, '.tablename('rr_v_member').' b where a.openid = b.openid and a.uniacid = :uniacid and a.isshow = :isshow ORDER BY a.playcount DESC limit 5',array(':uniacid' => $uniacid,':isshow' => 1));
		}

		$shop = m('common')->getSysset('shop');//平台抽成
		if(empty($shop['drawsprice'])){
			$drawsprice = 0;
		}else{
			$drawsprice = $shop['drawsprice']/100;
		}

		load()->func('communication');
		if(!empty($videos)){
			foreach ($videos as &$value2) {

				$value2['money'] = round(($value2['money']*1 + $value2['money']*$drawsprice),1);

				$getParams = array('fileId' => $value2['fileid'], 'infoFilter.0' => 'basicInfo', 'infoFilter.1' => 'snapshotByTimeOffsetInfo');
				$value2['videoinfo'] = videoApi('GetVideoInfo', $getParams);
			}
			unset($value2);
		}
		$data['videos'] = array('type' => '精品视频','videos' => $videos);

		//首页文章列表
		$articles = pdo_fetchall('select a.id,a.cover_url,a.article_title,a.money,a.click_nums,b.realname,b.nickname,b.avatar from '.tablename('rr_v_articles').' a,
		 '.tablename('rr_v_member').' b where a.openid = b.openid and a.uniacid = :uniacid 
			and a.isshow = :isshow and a.id IN(SELECT f.article_id FROM (SELECT article_id,COUNT(*) mycount FROM '.tablename('rr_v_comments').' WHERE uniacid = :uniacid AND video_id = 0 AND cid = 0 AND rid = 0 GROUP BY article_id ORDER BY mycount DESC) f ) 
			limit 3',array(':uniacid' => $uniacid,':isshow' => 1));
		if(empty($articles)){
			$articles = pdo_fetchall('select a.id,a.cover_url,a.article_title,a.money,a.click_nums,b.realname,b.nickname,b.avatar from '.tablename('rr_v_articles').' a,
		 '.tablename('rr_v_member').' b where a.openid = b.openid and a.uniacid = :uniacid and a.isshow = :isshow ORDER BY a.click_nums DESC limit 3',array(':uniacid' => $uniacid,':isshow' => 1));
		}
		if(!empty($articles)){
			foreach ($articles as &$value3) {
				$value3['money'] = round(($value3['money']*1 + $value3['money']*$drawsprice),1);
			}
			unset($value3);
		}
		$data['articles'] = array('type' => '精品文章','articles' => $articles);

		app_json(array('uniacid' => $uniacid,'patients' => $data));

	}

	//首页点击更多医生列表
	public function more_doctors()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$pageSize = intval($param['pageSize']);
		$page = intval(($param['page'])-1) * $pageSize;
		$limit = ' limit '.$page.','.$pageSize.' ';
		$id = $param['id'];
		if($id == 'a'){
		$doctors = pdo_fetchall('select m.nickname,m.realname,m.avatar,m.level,d.id,d.openid,d.job,d.hospital,d.parentid,d.departmentid,d.specialty from 
			'.tablename('rr_v_member_doctors').' d,'.tablename('rr_v_member').' m where d.uniacid = :uniacid  and d.openid = m.openid 
			and d.isdelete = 0 and d.isaudit = 1 order by m.createtime desc '.$limit.' ',array(':uniacid' => $uniacid));	
		}else{
			$doctors = pdo_fetchall('select m.nickname,m.realname,m.avatar,m.level,d.id,d.openid,d.job,d.hospital,d.parentid,d.departmentid,d.specialty from '.tablename('rr_v_member_doctors').' d,'.tablename('rr_v_member').' m where d.uniacid = :uniacid  and d.openid = m.openid and d.isdelete = :isdelete and (d.parentid = :id or d.departmentid = :id) order by m.createtime desc '.$limit.' ',array(':uniacid' => $uniacid,':isdelete' => 0,':id' => $id));
		}
		foreach ($doctors as &$value) {
			if($value['level'] == 0){
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
			unset($value['parentid']);
			unset($value['departmentid']);
			$followed = pdo_fetch('select id,isfollow from ' . tablename('rr_v_member_follow') . ' where uniacid = :uniacid and doc_openid=:doc_openid and pat_openid=:pat_openid  LIMIT 1', array(':uniacid' => $uniacid, ':doc_openid' => $doc_openid, ':pat_openid' => $pat_openid));
				if(!empty($followed)){
					$res_doc['isfollow'] = $followed['isfollow'];
				}else{
					$res_doc['isfollow'] = 0;
				}
			if($value['specialty'] != null){
				$arr = array();
				$value['specialty'] = explode(',', $value['specialty']);
				foreach ($value['specialty']  as $v) {
					$specialty = pdo_fetch('select title from '.tablename('rr_v_search').' where uniacid = :uniacid and id = :id and enabled = 1 and iswxapp = 1 ',array(':uniacid' => $uniacid,':id' => $v));
					$arr[] = $specialty['title'];
					$value['specialty'] = $arr;
				}

			}

		}
		app_json(array('uniacid' => $uniacid,'doctors' => $doctors));
	}

	//患者端点击更多讲座接口
	public function more_lectures()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$pageSize = intval($param['pageSize']);
		$page = intval(($param['page'])-1) * $pageSize;
		$limit = ' limit '.$page.','.$pageSize.' ';
		$openid = 'sns_wa_'.$param['openid'];
		$type = $param['type'];//1是本月，2是下月，3往期历史开讲记录
		if(empty($type) || empty($param['openid'])){
   			app_error(AppError::$ParamsError);
   		}else{
   			$condition = ' b.uniacid = :uniacid and m.openid = b.openid and b.isshow = 1 and b.releasetime > 0 ';
   			if($type == '1'){
   				//本月开讲
	   			$firstDay = date('Y-m-d H:i');
	   			$lastDay = date('Y-m-t H:i');
	   			$condition .= ' and "'.$firstDay.'" < b.start_time and b.end_time < "'.$lastDay.'" ORDER BY b.start_time ';
   			}elseif($type == '2'){
   				//下月开讲
	   			$firstDay = date('Y-m-01',strtotime('+1 month'));
	   			$lastDay = date('Y-m-t',strtotime('+1 month'));
	   			$condition .= ' and "'.$firstDay.'" < b.start_time and b.end_time < "'.$lastDay.'" ORDER BY b.start_time';
   			}elseif($type == '3'){
   				//往期历史开讲记录
	   			$condition .= ' and b.start_time < "'.date('Y-m-d H:i').'" ORDER BY b.start_time DESC ';
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
					//查询患者是否报名
					$log = pdo_fetch('select * from '.tablename('rr_v_lectures_log').' where uniacid = :uniacid and openid = :openid
						and ordernumber <>"" and lid = :id ',array(':uniacid' => $uniacid,':openid' => $openid,':id' => $value['id']));
					if(empty($log)){
						$value['paystatus'] = 0;
					}else{
						$value['paystatus'] = 1;
					}
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
   			}else{
   				$list = array();
   			}
   			$total['total'] = count($list);//记录总数
			app_json(array('list' => $list,'total' => $total));
   		}

	}

	//患者端点击讲座详情接口
	public function more_lectures_detail()
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

			$sql = 'SELECT * FROM ' .tablename('rr_v_lectures').' where uniacid = :uniacid and id = :id ';
			$res = pdo_fetch($sql,array(':id' => $id,':uniacid' => $uniacid));
			$time = date("Y-m-d H:i:s",$res['createtime']);
			$res['createtime'] = explode(' ', $time)[0];
			$res['cretime'] = explode(' ', $time)[1];
			if($res['releasetime'] != 0){
				$res['releasetime'] = date('Y-m-d H:i:s',$res['releasetime']);
			}
			$res['time'] = explode(' ',$res['start_time'])[1];
			$res['timeEnd'] = explode(' ',$res['end_time'])[1];
			$res['data'] = explode(' ', $res['end_time'])[0];

			if(!empty($res['end_time'])){
				if(strtotime($res['end_time']) > time()){
					$res['isend'] = 1;
				}else{
					$res['isend'] = 0;
				}
			}

			$shop = m('common')->getSysset('shop');//平台抽成
			if(empty($shop['drawsprice'])){
				$drawsprice = 0;
			}else{
				$drawsprice = $shop['drawsprice']/100;
			}

			$res['lecture_cost'] = round(($res['lecture_cost']*1 + $res['lecture_cost']*$drawsprice),1);

			$doc = 'SELECT m.avatar,m.realname,m.nickname,m.level,d.openid,d.job,d.hospital,d.parentid,d.departmentid FROM '.tablename('rr_v_member').' m,
			'.tablename('rr_v_member_doctors').' d where m.openid = d.openid and d.uniacid = :uniacid and d.openid = :openid and d.isdelete = 0';
			$doctor = pdo_fetch($doc,array(':uniacid' => $uniacid,':openid' => $res['openid']));
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
			//查询讲座是否报名
			$log = pdo_fetch('select * from '.tablename('rr_v_lectures_log').' where uniacid = :uniacid and openid = :openid
			and  ordernumber <>"" and lid = :id ',array(':uniacid' => $uniacid,':openid' => $openid,':id' => $id));
			if(empty($log)){
				$res['paystatus'] = 0;
			}else{
				$res['paystatus'] = 1;
			}
			//查询报名人数
			$peoplenum['peoplenum'] = pdo_fetchcolumn('select count(*) as peoplenum from '.tablename('rr_v_lectures_log').' where uniacid = :uniacid 
				and ordernumber <>"" and lid = :id ',array(':uniacid' => $uniacid,':id' => $id));
			if(empty($peoplenum['peoplenum'])){
				$res['peoplenum'] = 0;
			}else{
				$res['peoplenum'] = $peoplenum['peoplenum'];
			}

		}
	
		app_json(array('res' => $res));

	}

	//患者端首页点击更多视频接口
	public function more_video()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$pageSize = intval($param['pageSize']);
		$page = intval(($param['page'])-1) * $pageSize;
		$limit = ' limit '.$page.','.$pageSize.' ';
		$type = $param['type'];//1查全部，2查免费，3查热门
		if(empty($type)){
			app_error(AppError::$ParamsError);
		}
		if($type == 1){
			$condition = 'and isshow = 1 order by sendtime desc';
			// $videos = pdo_fetchall('select id,videoname,img_url,money,playcount,sendtime from '.tablename('rr_v_member_videos').' where uniacid = :uniacid and isshow = 1 order by sendtime desc '.$limit.' ',array(':uniacid' => $uniacid));
		}elseif($type == 2){
			$condition = 'and isshow = 1 and money = 0 order by sendtime desc';
			// $videos = pdo_fetchall('select id,videoname,img_url,money,playcount,sendtime from '.tablename('rr_v_member_videos').' where uniacid = :uniacid and isshow = 1 and money = :money order by sendtime desc '.$limit.' ',array(':uniacid' => $uniacid,':money' => 0));
		}elseif($type == 3){
			$condition = 'and isshow = 1 order by playcount desc';
			// $videos = pdo_fetchall('select id,videoname,img_url,money,playcount,sendtime from '.tablename('rr_v_member_videos').' where uniacid = :uniacid and isshow = 1 order by playcount desc '.$limit.' ',array(':uniacid' => $uniacid));
		}else{
			app_error(AppError::$ParamsError);
		}
		load()->func('communication');
		
		$videos = pdo_fetchall('select id,fileid,videoname,img_url,money,playcount,sendtime from '.tablename('rr_v_member_videos').' 
			where uniacid = :uniacid '.$condition.' '.$limit.' ',array(':uniacid' => $uniacid));
		if(!empty($videos)){
			foreach ($videos as &$value) {
				$value['sendtime'] = date('Y-m-d',$value['sendtime']);

				$getParams = array('fileId' => $value['fileid'], 'infoFilter.0' => 'basicInfo', 'infoFilter.1' => 'transcodeInfo', 'infoFilter.2' => 'snapshotByTimeOffsetInfo');
				$value['videoinfo'] = videoApi('GetVideoInfo', $getParams);
			}
		}else{
			$videos = array();
		}
		$total['total'] = count($videos);

		app_json(array('videos' => $videos,'total' => $total));

	}

	//患者端点击更多文章接口
	public function more_articles()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$pageSize = intval($param['pageSize']);
		$page = intval(($param['page'])-1) * $pageSize;
		$limit = ' limit '.$page.','.$pageSize.' ';
		$type = $param['type'];//1查全部，2查免费，3查热门
		if(empty($type)){
			app_error(AppError::$ParamsError);
		}
		
		if($type == 1){
			$condition = 'and isshow = 1 order by releasetime desc';
			// $articles = pdo_fetchall('select id,cover_url,article_title,money,click_nums,releasetime from '.tablename('rr_v_articles').'  where uniacid = :uniacid and isshow = 1 order by releasetime desc '.$limit.' ',array(':uniacid' => $uniacid));
		}elseif($type == 2){
			$condition = 'and isshow = 1 and money = 0 order by releasetime desc';
			// $articles = pdo_fetchall('select id,cover_url,article_title,money,click_nums,releasetime from '.tablename('rr_v_articles').'  where uniacid = :uniacid and isshow = 1 and money = :money order by releasetime desc '.$limit.' ',array(':uniacid' => $uniacid,':money' => 0));
		}elseif($type == 3){
			$condition = 'and isshow = 1 order by click_nums desc';
			// $articles = pdo_fetchall('select id,cover_url,article_title,money,click_nums,releasetime from '.tablename('rr_v_articles').'  where uniacid = :uniacid and isshow = 1 order by releasetime desc '.$limit.' ',array(':uniacid' => $uniacid));
		}else{
			app_error(AppError::$ParamsError);
		}
		$articles = pdo_fetchall('select id,cover_url,article_title,money,click_nums,releasetime from '.tablename('rr_v_articles').' 
		 where uniacid = :uniacid '.$condition.' '.$limit.' ',array(':uniacid' => $uniacid));

		if(!empty($articles)){
			foreach ($articles as &$value) {
			$value['releasetime'] = date('Y-m-d',$value['releasetime']);
			}
		}else{
			$articles = array();
		}
		$total['total'] = count($articles);

		app_json(array('articles' => $articles,'total' => $total));


	}

	//患者端首页点击搜索接口
	public function search()
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
			$condition = ' d.uniacid = :uniacid and m.openid=d.openid and m.identity=1 and d.isdelete=0 and d.isaudit=1';
			$params = array(':uniacid' => $uniacid);
			if(!empty($keyword)){
				//记录搜索关键词
				if(!is_numeric($keyword)){
					$res = pdo_fetch('select id,content,search_count from '.tablename('rr_v_hotsearch').' where uniacid = :uniacid and type = 3 and 
					content = :content',array(':uniacid' => $uniacid,':content' => $param['keyword']));
					if(!empty($res)){
						pdo_update('rr_v_hotsearch',array('search_count' => $res['search_count']+1),array('id' => $res['id']));
					}else{
						$data = array(
							'uniacid' => $uniacid,
							'content' => $param['keyword'],
							'type' => 3,
							'createtime' => time(),
							);
						pdo_insert('rr_v_hotsearch',$data);
					}

				}
				

		
				if($param['type'] == '1'){
					//以医生姓名和昵称搜索医生
					$list = pdo_fetchall('select * from ' . tablename('rr_v_member') . ' where uniacid = :uniacid and (nickname LIKE :keyword
					 or realname LIKE :keyword) and identity=1', array(':uniacid' => $uniacid, ':keyword' => '%'.trim($keyword).'%'));
					if(!empty($list)){
						$params[':keyword'] = '%'.trim($keyword).'%';
						$condition .= ' and (m.nickname LIKE :keyword or m.realname LIKE :keyword) ';
					}else{
						//以科室名称搜索医生
						$parent = pdo_fetchall('select id,name from ' . tablename('rr_v_member_department') . ' where uniacid = :uniacid and parentid = 0 and name LIKE :keyword ', array(':uniacid' => $uniacid, ':keyword' => '%'.trim($keyword).'%'));
						if(!empty($parent)){
							foreach ($parent as &$val2) {
								$parentid_str .= $val2['id'].',';
							}
							unset($val2);
							$params[':departmentid'] = rtrim($parentid_str,',');
							$condition .= ' and d.parentid IN(:departmentid)';
						}else{
							//以科室名称搜索医生
							$department = pdo_fetchall('select id,name from ' . tablename('rr_v_member_department') . ' where uniacid = :uniacid and parentid > 0 and name LIKE :keyword ', array(':uniacid' => $uniacid, ':keyword' => '%'.trim($keyword).'%'));
							if(!empty($department)){
								foreach ($department as &$val) {
									$departmentid_str .= $val['id'].',';
								}
								unset($val);
								$params[':departmentid'] = rtrim($departmentid_str,',');
								$condition .= ' and d.departmentid IN(:departmentid)';
							}else{
								//以疾病名称/医生擅长搜索医生
								$params[':keyword'] = '%'.trim($keyword).'%';
								$condition .= ' and d.field LIKE :keyword ';
							}
						}
					}
					
					
				}elseif($param['type'] == '3'){
					$params[':departmentid'] = intval($keyword);
					$condition .= ' and (d.departmentid =:departmentid or d.parentid =:departmentid) ';
				}
			}else{

				if($param['type'] == '2'){
					$arr = array();
					$sql2 = 'SELECT COUNT(*) num,doc_openid FROM '.tablename('rr_v_member_follow').' WHERE uniacid = :uniacid AND isfollow = 1 
					AND isdelete = 0 GROUP BY doc_openid ORDER BY num DESC';

					$arr1 = pdo_fetchall($sql2,array(':uniacid' => $uniacid));
					
					$sql3 = 'SELECT COUNT(*),openid FROM '.tablename('rr_v_member_doctors').' WHERE uniacid = :uniacid 
					AND isaudit = 1 AND isdelete = 0 AND openid NOT IN(SELECT doc_openid FROM '.tablename('rr_v_member_follow').' ) GROUP BY openid';
					
					$arr2 = pdo_fetchall($sql3,array(':uniacid' => $uniacid));
					array_merge($arr,$arr1,$arr2);
					if(!empty($arr)){
						foreach ($arr as $value) {
							$openid_str .= $value['doc_openid'].',';
						}
						$openid_str = rtrim($openid_str,',');
						
						$condition .= ' and d.openid IN('.$openid_str.') ';
					}

					$condition .= '  ORDER BY d.isfamous desc,d.displayorder desc';
				
				}

			}

			$sql = 'select d.openid,m.realname,m.nickname,m.avatar,m.level,m.memberid,d.recommend_index,d.hospital,d.job,d.specialty,d.field,d.parentid,d.departmentid,
			d.default_consult,d.highgrade_consult,d.field from ' . tablename('rr_v_member') . ' m,' . tablename('rr_v_member_doctors') . ' d where ' . $condition . ' '.$limit.' ';
			$res_doc = pdo_fetchall($sql, $params);
			foreach ($res_doc as &$row) {
				if($row['level']==0){
					$row['level'] = 'V0';
				}else{
					$level = pdo_fetch('select id,levelname from ' . tablename('rr_v_member_level') . ' where uniacid = :uniacid and id=:id  LIMIT 1', array(':uniacid' => $uniacid, ':id' => $row['level']));
					$row['level'] = $level['levelname'];
				}
				if($row['departmentid'] != 0){
					$departmentid = $row['departmentid'];
				}else{
					$departmentid = $row['parentid'];
				}
				$res = pdo_fetch('select id,name from ' . tablename('rr_v_member_department') . ' where uniacid = :uniacid and id=:id  LIMIT 1', array(':uniacid' => $uniacid, ':id' => $departmentid));
				if(empty($res)){
					$row['department'] = '';
				}else{
					$row['department'] = $res['name'];
				}
				
				// if(!empty($row['specialty'])){
				// 	$arr = explode(',', $row['specialty']);
				// 	foreach ($arr as &$value) {
				// 		$sp = pdo_fetch('select id,title from ' . tablename('rr_v_search') . ' where uniacid = :uniacid and id=:id  LIMIT 1', array(':uniacid' => $uniacid, ':id' => $value));
				// 		$specialty[] = $sp;
				// 	}
				// 	$row['specialty'] = $specialty;
				// }
			}
			unset($row);

			app_json(array('list' => $res_doc,'sql' => $sql));


		}

	}

	//科普文章、视频幻灯片接口
	/*
	public function advinfo()
	{	
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$type = $param['type'];
		if(empty($type)){
			app_error(AppError::$ParamsError);
		}else{
			switch ($type){
			case '1':
			$condition = 'and enabled = 1 and iswxapp = 1 and type = :type ';
			break;
			case '2':
			$condition = 'and enabled = 1 and iswxapp = 1 and type = :type ';
			break;
			}

			$list = pdo_fetchall('select * from '.tablename('rr_v_adv').' where uniacid = :uniacid 
				'.$condition.' order by displayorder desc ',array(':uniacid' => $uniacid,':type' => $type));
		}
		if(empty($list)){
			$list = array();
		}else{
			foreach ($list as &$value) {
				$value['thumb'] = tomedia($value['thumb']);
			}
		}
		app_json(array('list' => $list));
		

	}
	*/

	//患者端点击更多文章接口
	public function more_material()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = $param['openid'];
		$keyword = trim($param['keyword']);
		$pageSize = intval($param['pageSize']);
		$page = intval(($param['page'])-1) * $pageSize;
		$limit = ' limit '.$page.','.$pageSize.' ';
		$type = $param['type'];//1查全部，2查免费，3查热门
		$species = $param['species'];//1查文章 2查视频
		if(empty($type) || empty($openid)){
			app_error(AppError::$ParamsError);
		}

		$shop = m('common')->getSysset('shop');//平台抽成
		if(empty($shop['drawsprice'])){
			$drawsprice = 0;
		}else{
			$drawsprice = $shop['drawsprice']/100;
		}

		$params = array(':uniacid' => $uniacid,':openid' => $openid);
		if($species == 1 ){
			// if(!empty($keyword)){
			// 	$condition = 'and (a.article_title like :keyword or a.article_introduction like :keyword or a.article_content like :keyword or a.tag like :keyword)';
			// 	$params[':keyword'] = '%'.$keyword.'%';
			// }
			// if($type == 1){
			// 	$condition .= 'GROUP BY a.id ORDER BY group_count DESC';
			// }elseif($type == 2){
			// 	$condition .= 'and a.money = 0 GROUP BY a.id order by a.releasetime desc';
			// }elseif($type == 3){
			// 	$condition .= 'GROUP BY a.id order by a.releasetime desc';
				
			// }
			// $list = pdo_fetchall('SELECT COUNT(*) AS group_count,a.* FROM '.tablename('rr_v_articles').' AS a LEFT JOIN '.tablename('rr_v_comments').' AS b ON a.id = b.`article_id` WHERE a.uniacid=:uniacid AND a.openid=:openid and a.isshow=1   '.$condition.'  '.$limit.' ',$params);

			if(!empty($keyword)){
				$condition = 'and (article_title like :keyword or article_introduction like :keyword or article_content like :keyword or tag like :keyword)';
				$params[':keyword'] = '%'.$keyword.'%';
			}
			if($type == 1){
				$condition .= 'order by click_nums desc';
			}elseif($type == 2){
				$condition .= 'and money = 0 order by releasetime desc';
			}elseif($type == 3){
				$condition .= 'order by releasetime desc';
				
			}
			$list = pdo_fetchall('SELECT * FROM '.tablename('rr_v_articles').' WHERE uniacid=:uniacid AND openid=:openid and isshow=1   '.$condition.'  '.$limit.' ',$params);

			if(!empty($list)){
				foreach ($list as &$value) {

					$value['money'] = round(($value['money']*1 + $value['money']*$drawsprice),1);

					$value['releasetime'] = date('Y-m-d',$value['releasetime']);
				}
			}else{
				$list = array();
			}
			$total['total'] = count($list);
		}elseif($species == 2 ){
			// if(!empty($keyword)){
			// 	$condition = 'and (a.videoname like :keyword or a.content like :keyword or a.tag like :keyword)';
			// 	$params[':keyword'] = '%'.$keyword.'%';
			// }
			// if($type == 1){
			// 	$condition .= 'GROUP BY a.id order by a.sendtime desc';
			// }elseif($type == 2){
			// 	$condition .= 'and a.money = 0 GROUP BY a.id order by a.sendtime desc';
			// }elseif($type == 3){
			// 	$condition .= 'GROUP BY a.id ORDER BY group_count DESC,a.id DESC';
			// }
			// $list = pdo_fetchall('SELECT COUNT(*) AS group_count,a.* FROM '.tablename('rr_v_member_videos').' AS a LEFT JOIN '.tablename('rr_v_comments').' AS b ON a.id = b.`video_id` WHERE a.uniacid=:uniacid AND a.openid=:openid and a.isshow=1   '.$condition.'  '.$limit.' ',$params);

			if(!empty($keyword)){
				$condition = 'and (videoname like :keyword or content like :keyword or tag like :keyword)';
				$params[':keyword'] = '%'.$keyword.'%';
			}
			if($type == 1){
				$condition .= 'order by playcount desc';
			}elseif($type == 2){
				$condition .= 'and money = 0 order by sendtime desc';
			}elseif($type == 3){
				$condition .= 'order by sendtime desc';
			}
			$list = pdo_fetchall('SELECT * FROM '.tablename('rr_v_member_videos').' WHERE uniacid=:uniacid AND openid=:openid and isshow=1   '.$condition.'  '.$limit.' ',$params);

			if(!empty($list)){
				load()->func('communication');

				foreach ($list as &$value) {

					$value['money'] = round(($value['money']*1 + $value['money']*$drawsprice),1);
					
					$value['sendtime'] = date('Y-m-d', $value['sendtime']);

					$getParams = array('fileId' => $value['fileid'], 'infoFilter.0' => 'basicInfo', 'infoFilter.1' => 'transcodeInfo', 'infoFilter.2' => 'snapshotByTimeOffsetInfo');
					$value['videoinfo'] = videoApi('GetVideoInfo', $getParams);
				}
				unset($value);
			}else{
				$list = array();
			}
			$total['total'] = count($list);

		}
		

		app_json(array('list' => $list,'total' => $total));


	}
	
}

?>
