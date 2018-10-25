<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}
 
require rr_vv3_PLUGIN . 'app/core/page_mobile.php';
class Doctors_RrvV3Page extends AppMobilePage
{
	//获取医生设置信息
	public function get_data()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];

		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}else{

			$res = pdo_fetch('SELECT id_card,practice_doctors_certificate,doctor_certificate FROM ' . tablename('rr_v_member_doctors') . ' WHERE openid = "' . $openid . '" AND uniacid=' . $uniacid . ' ');
			if(!empty($res['id_card'])){
				$list['id_card'] = unserialize($res['id_card']);
			}else{
				$list['id_card'] = array();
			}
			if(!empty($res['practice_doctors_certificate'])){
				$list['practice'] = unserialize($res['practice_doctors_certificate']);
			}else{
				$list['practice'] = array();
			}
			if(!empty($res['doctor_certificate'])){
				$list['doctor_certificate'] = unserialize($res['doctor_certificate']);
			}else{
				$list['doctor_certificate'] = array();
			}
			
		}
		
		app_json(array('list' => $list));

	}

	//医生资质修改
	public function update()
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
			$data = array();
			switch ($param['type']) {
				case 1:
						if (empty($param['front']) || empty($param['verso'])) {
							app_error(AppError::$ParamsError);
						}else{
							$data['id_card'] = serialize(array('front' => $param['front'], 'verso' => $param['verso']));
						}
					break;
				case 2:
					if (empty($param['practice'])) {
						app_error(AppError::$ParamsError);
					}else{
						$data['practice_doctors_certificate'] = serialize(array($param['practice']));
					}
					break;
				case 3:
					if (empty($param['doctor_certificate'])) {
						app_error(AppError::$ParamsError);
					}else{
						$data['doctor_certificate'] = serialize(array($param['doctor_certificate']));
					}
					break;
				default:
					# code...
					break;
			}
			if(!empty($data)){
				pdo_update('rr_v_member_doctors', $data, array('uniacid' => $uniacid, 'openid' => $openid));
				$status = 1;
			}
			
		}
		
		$result['status'] = $status;
		
		app_json($result);

	}

	//患者端医生个人主页
	public function doc_index()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		
		if($param['type'] == '1'){
			$doc_openid = 'sns_wa_'.$param['doc_openid'];
		}else{
			$doc_openid = $param['doc_openid'];
			$pat_openid = 'sns_wa_'.$param['pat_openid'];
		}

		$result = array();

		if (empty($param['doc_openid'])) {
			app_error(AppError::$ParamsError);
		}else{

			$condition = ' d.uniacid = :uniacid and d.openid = :openid and m.openid=d.openid and m.identity=1';
			$params = array(':uniacid' => $uniacid, ':openid' => $doc_openid);

			$sql = 'select d.id doctorid,d.openid,m.realname,m.nickname,m.avatar,m.level,d.recommend_index,d.hospital,d.job,d.field,d.parentid,d.departmentid,m.memberid,d.default_consult,d.highgrade_consult from ' . tablename('rr_v_member') . ' m,' . tablename('rr_v_member_doctors') . ' d where ' . $condition . '  ';
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
				$res_doc['department'] = '';
			}else{
				$res_doc['department'] = $res['name'];
			}
			
		

			//科室排名新算法
			if(!empty($departmentid)){
				$condition = ' AND b.departmentid=:departmentid ';
				$sqlparams = array(':uniacid' => $uniacid,':departmentid' => $departmentid);
			}else{
				$condition = ' AND b.parentid=:parentid ';
				$sqlparams = array(':uniacid' => $uniacid,':parentid' => $parentid);
				
			}
			$docs_rangings = pdo_fetchall('SELECT b.id,b.openid,COUNT(*) AS group_count FROM ims_rr_v_member_follow AS a LEFT JOIN ims_rr_v_member_doctors AS b ON a.`doc_openid` = b.openid
					WHERE a.`isfollow` = 1 AND a.`isdelete` = 0 AND a.uniacid=:uniacid '.$condition.'
					GROUP BY b.id ORDER BY group_count DESC',$sqlparams);

			if(!empty($docs_rangings)){
					foreach ($docs_rangings as $key => $value) {
						if($value['openid'] == $doc_openid){
							$res_doc['ranking'] = $key+1;
						}else{
							// $res_doc['ranking'] = 0;
						}
					}
				}else{
					$res_doc['ranking'] = 0;
				}

			$follow['followNum'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('rr_v_member_follow') . '' . ' WHERE uniacid = :uniacid and doc_openid=:openid and isfollow=1 and isdelete=0', $params);
			if(!empty($follow['followNum'])){
				$res_doc['followNum'] = $follow['followNum'];
			}else{
				$res_doc['followNum'] = 0;
			}
			if($doc_openid =='sns_wa_oa2sn47jmTQayZ1ICj2lGwalRXak'){ //杜正贵
				$res_doc['followNum'] += '2678';
				$res_doc['ranking'] = 1;
			}
			if($doc_openid =='sns_wa_oa2sn4_oEaC-GZp9D8TQak5H0zB0'){ //苏安平
				$res_doc['followNum'] += '2356';
				$res_doc['ranking'] = 1;
			}
			if($doc_openid =='sns_wa_oa2sn4-KrDKhitpTzhvXsd2ejZes'){
				$res_doc['followNum'] += '725';
			}
			if($doc_openid =='sns_wa_oa2sn4_YPdZhm3eD94WaImoms0gA'){
				$res_doc['followNum'] += '820';
			}
			if($doc_openid =='sns_wa_oa2sn4yVB5i4amdx_aDyK0RxsaKU'){
				$res_doc['followNum'] += '924';
			}
			if($doc_openid =='sns_wa_oa2sn42NXr04WhGlXb9mHZyNjQtU'){
				$res_doc['followNum'] += '815';
			}
			if($doc_openid =='sns_wa_oa2sn46Z6FJqG4MO-B_1H07LlbbM'){
				$res_doc['followNum'] += '205';
			}
			if($doc_openid =='sns_wa_oa2sn4_ym-77Ti4h5BWu5GU3U-Ys'){
				$res_doc['followNum'] += '362';
			}
			if($doc_openid =='sns_wa_oa2sn41f3Gf489zLu1rA8FGVeBk0'){
				$res_doc['followNum'] += '402';
			}
			if($doc_openid =='sns_wa_oa2sn45GYnu0327fcaqqITWDw3s4'){
				$res_doc['followNum'] += '368';
			}
			if($doc_openid =='sns_wa_oa2sn4-YLLSLYmiRiHDWU7A6u29Y'){
				$res_doc['followNum'] += '501';
			}
			if($doc_openid =='sns_wa_oa2sn45RR2YK5zrgQs7d6NJ4xDYo'){
				$res_doc['followNum'] += '435';
			}
			if($doc_openid =='sns_wa_oa2sn4yVB5i4amdx_aDyK0RxsaKU'){
				$res_doc['followNum'] += '398';
			}
			if($doc_openid =='sns_wa_oa2sn41m5hV-OI3kkID7jXG2DKug'){
				$res_doc['followNum'] += '450';
			}

			//医生端获取
			if($param['type'] == '1'){

				$banner = pdo_fetch('select id,bannername,thumb from ' . tablename('rr_v_banner') . ' where uniacid = :uniacid and iswxapp=1 and show_mode=0 and enabled=1 ORDER BY id DESC LIMIT 1', array(':uniacid' => $uniacid));
				$banner['thumb'] = tomedia($banner['thumb']);
				$res_doc['banner'] = $banner;

				// $lectures = pdo_fetchall('select id,cover_url,lecture_author,lecture_title,lecture_introduction from ' . tablename('rr_v_lectures') . ' where uniacid=:uniacid and isshow=1 and status = 2 and openid = :openid and end_time > "'.date('Y-m-d H:i').'" ORDER BY start_time LIMIT 3', $params);
				// //推荐别的医生的讲座
				// // if(empty($lectures)){
				// // 	$lectures = pdo_fetchall('select id,cover_url,lecture_author,lecture_title,lecture_introduction from ' . tablename('rr_v_lectures') . ' where uniacid=:uniacid and isshow=1 and status = 2 and end_time > "'.date('Y-m-d H:i').'" ORDER BY id DESC LIMIT 3', array(':uniacid' => $uniacid));
				// // }
				// if(!empty($lectures)){
				// 	foreach ($lectures as &$row) {
				// 		$pnum['peoplenum'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('rr_v_lectures_log') . '' . ' WHERE uniacid = :uniacid and ordernumber<>"" and lid=:lid ', array(':uniacid' => $uniacid, ':lid'=>$row['id']));
				// 		$row['peoplenum'] = $pnum['peoplenum'];
				// 	}

				// }
				
				// $res_doc['lectures'] = $lectures;

				// $videos = pdo_fetchall('select id,videoname,img_url,content,playcount,money from ' . tablename('rr_v_member_videos') . ' where uniacid=:uniacid and isshow=1 and status = 1 and openid = :openid 
				// 	and id IN(SELECT f.video_id FROM (SELECT video_id,COUNT(*) mycount FROM '.tablename('rr_v_comments').' WHERE uniacid = :uniacid AND article_id = 0 AND cid = 0 AND rid = 0 GROUP BY video_id ORDER BY mycount DESC) f ) LIMIT 3', $params);
				// $res_doc['videos'] = $videos;
				// $articles = pdo_fetchall('select id,cover_url,article_title,article_introduction,click_nums,money from ' . tablename('rr_v_articles') . ' where uniacid=:uniacid and isshow=1 and status = 2 and openid = :openid 
				// 	and id IN(SELECT f.article_id FROM (SELECT article_id,COUNT(*) mycount FROM '.tablename('rr_v_comments').' WHERE uniacid = :uniacid AND video_id = 0 AND cid = 0 AND rid = 0 GROUP BY article_id ORDER BY mycount DESC) f ) LIMIT 3', $params);
				// $res_doc['articles'] = $articles;
				
			}

			$shop = m('common')->getSysset('shop');//平台抽成
			if(empty($shop['drawsprice'])){
				$drawsprice = 0;
			}else{
				$drawsprice = $shop['drawsprice']/100;
			}

			//患者端获取
			if($param['type'] == '2'){

				$res_doc['default_consult'] = round(($res_doc['default_consult']*1 + $res_doc['default_consult']*$drawsprice),1);
				$res_doc['highgrade_consult'] = round(($res_doc['highgrade_consult']*1 + $res_doc['highgrade_consult']*$drawsprice),1);

				if (empty($pat_openid)) {
					app_error(AppError::$ParamsError);
				}
				
				$followed = pdo_fetch('select id,isfollow from ' . tablename('rr_v_member_follow') . ' where uniacid = :uniacid and doc_openid=:doc_openid and pat_openid=:pat_openid  LIMIT 1', array(':uniacid' => $uniacid, ':doc_openid' => $doc_openid, ':pat_openid' => $pat_openid));
				if(!empty($followed)){
					$res_doc['isfollow'] = $followed['isfollow'];
				}else{
					$res_doc['isfollow'] = 0;
				}

				//将患者关注公众号的openid和小程序openid进行绑定，绑定为同一个患者
				$condition2 = ' uniacid= :uniacid and openid=:openid ';
				$where = ' uniacid= :uniacid and openid<>:openid and avatar_byte>0';
				$params2 = array(':uniacid' => $uniacid, ':openid' => $pat_openid);
				$wx_list = pdo_fetch('select * from ' . tablename('rr_v_member') . ' where ' . $condition2 . ' limit 1', $params2);
				if(empty($wx_list['memberid'])){
					$params2[':MD5nickname'] = $wx_list['MD5nickname'];
					$gz_list = pdo_fetchall('select * from ' . tablename('rr_v_member') . ' where ' . $where . ' and MD5nickname=:MD5nickname', $params2);
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

				$pat = m('member')->getMember($pat_openid);
				if(!empty($pat['memberid'])){
					$pat_fans = m('member')->getMember($pat['memberid']);
					$res_doc['pat_gzhopenid'] = $pat_fans['openid'];
				}else{
					$res_doc['pat_gzhopenid'] = '';
				}
				// $lectures = pdo_fetchall('select id,cover_url,lecture_author,lecture_title,lecture_introduction from ' . tablename('rr_v_lectures') . ' where uniacid=:uniacid and isshow=1 and status = 2 and openid =:openid and end_time > "'.date('Y-m-d H:i').'" ORDER BY id DESC LIMIT 3', $params);
				//讲座推荐
				// if(empty($lectures)){
				// 	$lectures = pdo_fetchall('select id,cover_url,lecture_author,lecture_title,lecture_introduction from ' . tablename('rr_v_lectures') . ' where uniacid=:uniacid and isshow=1 and status = 2 and end_time > "'.date('Y-m-d H:i').'" ORDER BY id DESC LIMIT 3', array(':uniacid' => $uniacid));
				// }
				// $res_doc['lectures'] = $lectures;
				// $videos = pdo_fetchall('select id,videoname,img_url,content,playcount,money from ' . tablename('rr_v_member_videos') . ' where uniacid=:uniacid and isshow=1 and status = 1 and openid =:openid ORDER BY id DESC LIMIT 3', $params);
				//视频推荐
				// if(empty($videos)){
				// 	$videos = pdo_fetchall('select id,videoname,img_url,content,playcount,money from ' . tablename('rr_v_member_videos') . ' where uniacid=:uniacid and isshow=1 and status = 1 ORDER BY id DESC LIMIT 3', array(':uniacid' => $uniacid));
				// }
				// $res_doc['videos'] = $videos;
				// $articles = pdo_fetchall('select id,cover_url,article_title,article_introduction,click_nums,money from ' . tablename('rr_v_articles') . ' where uniacid=:uniacid and isshow=1 and status = 2 and openid =:openid ORDER BY id DESC LIMIT 3', $params);
				//文章推荐
				// if(empty($articles)){
				// 	$articles = pdo_fetchall('select id,cover_url,article_title,article_introduction,click_nums,money from ' . tablename('rr_v_articles') . ' where uniacid=:uniacid and isshow=1 and status = 2 ORDER BY id DESC LIMIT 3', array(':uniacid' => $uniacid));
				// }
				// $res_doc['articles'] = $articles;
				
			}

			$lectures = pdo_fetchall('select id,cover_url,lecture_author,lecture_title,lecture_introduction from ' . tablename('rr_v_lectures') . ' where uniacid=:uniacid and isshow=1 and status = 2 and openid = :openid and end_time > "'.date('Y-m-d H:i').'" ORDER BY start_time LIMIT 3', $params);
			if(!empty($lectures)){
				foreach ($lectures as &$row) {
					$pnum['peoplenum'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('rr_v_lectures_log') . '' . ' WHERE uniacid = :uniacid and ordernumber<>"" and lid=:lid ', array(':uniacid' => $uniacid, ':lid'=>$row['id']));
					$row['peoplenum'] = $pnum['peoplenum'];
				}

			}else{
				$lectures = array();
			}
			
			$res_doc['lectures'] = $lectures;

			$videos = pdo_fetchall('select id,fileid,videoname,img_url,content,playcount,money from ' . tablename('rr_v_member_videos') . ' where uniacid=:uniacid and isshow=1 and status = 1 and openid = :openid 
				and id IN(SELECT f.video_id FROM (SELECT video_id,COUNT(*) mycount FROM '.tablename('rr_v_comments').' WHERE uniacid = :uniacid AND article_id = 0 AND cid = 0 AND rid = 0 GROUP BY video_id ORDER BY mycount DESC) f ) LIMIT 3', $params);
			if(empty($videos)){
				$videos = pdo_fetchall('select id,fileid,videoname,img_url,content,playcount,money from ' . tablename('rr_v_member_videos') . ' where uniacid=:uniacid and isshow=1 and status = 1 and openid = :openid ORDER BY playcount DESC LIMIT 3', $params);
			}
			if(!empty($videos)){
				load()->func('communication');

				foreach ($videos as &$value2) {

					if($param['type'] == '2'){
						$value2['money'] = round(($value2['money']*1 + $value2['money']*$drawsprice),1);
					}

					$getParams = array('fileId' => $value2['fileid'], 'infoFilter.0' => 'basicInfo', 'infoFilter.1' => 'transcodeInfo', 'infoFilter.2' => 'snapshotByTimeOffsetInfo');
					$value2['videoinfo'] = videoApi('GetVideoInfo', $getParams);
				}
				unset($value2);
						
			}else{
				$videos = array();
			}
			$res_doc['videos'] = $videos;
			$articles = pdo_fetchall('select id,cover_url,article_title,article_introduction,click_nums,money from ' . tablename('rr_v_articles') . ' where uniacid=:uniacid and isshow=1 and status = 2 and openid = :openid 
				and id IN(SELECT f.article_id FROM (SELECT article_id,COUNT(*) mycount FROM '.tablename('rr_v_comments').' WHERE uniacid = :uniacid AND video_id = 0 AND cid = 0 AND rid = 0 GROUP BY article_id ORDER BY mycount DESC) f ) LIMIT 3', $params);
			if(empty($articles)){
				$articles = pdo_fetchall('select id,cover_url,article_title,article_introduction,click_nums,money from ' . tablename('rr_v_articles') . ' where uniacid=:uniacid and isshow=1 and status = 2 and openid = :openid ORDER BY click_nums DESC LIMIT 3', $params);
			}

			if(!empty($articles)){
				foreach ($articles as &$value3) {
					if($param['type'] == '2'){
						$value3['money'] = round(($value3['money']*1 + $value3['money']*$drawsprice),1);
					}
				}
				unset($value3);

			}else{
				$articles = array();
			}
			$res_doc['articles'] = $articles;


			//获取医生公众号openid
			if(!empty($res_doc['memberid'])){
				$gzh_openid = pdo_fetch('select id,openid from ' . tablename('rr_v_member') . ' where uniacid=:uniacid and id=:id ', array(':uniacid' => $uniacid, ':id' => $res_doc['memberid']));
				
				$res_doc['gzh_openid'] = $gzh_openid['openid'];

				//医生最近一月回复率
				// $start_time = strtotime(date('Y-m-01 H:i:s'));
				// $end_time = time();
				// if(date('d') < 9){
				// 	$start_time = strtotime(date('Y-m-d H:i:s',strtotime('-1 month')));
				// }

				//医生前一天回复率
				$start_time = strtotime(date('Y-m-d 00:00:00',strtotime('-1 day')));
				$end_time = strtotime(date('Y-m-d 23:59:59',strtotime('-1 day')));

				//咨询的患者总数
				$pat_total = pdo_fetchcolumn('SELECT COUNT(*) totals FROM ' .tablename('messikefu_fanskefu').' where weid = :uniacid and 
				kefuopenid = :kefuopenid and lasttime > '.$start_time.' and lasttime < '.$end_time.' ', array(':uniacid' => $uniacid, ':kefuopenid' => $gzh_openid['openid']));

				//医生回复总数
				$reply_total = pdo_fetchcolumn('SELECT COUNT(*) total FROM ' .tablename('messikefu_fanskefu').' where weid = :uniacid and 
				kefuopenid = :kefuopenid and lasttime > '.$start_time.' and lasttime < '.$end_time.' and kefulasttime > lasttime', array(':uniacid' => $uniacid, ':kefuopenid' => $gzh_openid['openid']));

				if(empty($pat_total) && empty($reply_total)){
					$res_doc['reply_rate'] = 100;//回复率
				}else{
					$res_doc['reply_rate'] = round(($reply_total['total']/$pat_total['totals'])*100);//回复率
				}
				

			}else{
				$res_doc['gzh_openid'] = '';
			}

			if($param['type'] == '2'){
				//判断患者咨询是否过期
				$diag_log = pdo_fetch('select id diaglogid,name,age,sex,mobile,diag_thumbs,content,consult_type,doctorid,orderid,createtime from ' . tablename('rr_v_member_patients_diag') . ' where uniacid =:uniacid and openid=:openid and doctorid =:doctorid and isdelete=0 ORDER BY id DESC limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $pat_openid, ':doctorid' => $res_doc['doctorid']));
				
				$pat_member = m('member')->getMember($pat_openid);
				if(empty($pat_member['memberid']) || $res_doc['gzh_openid'] == ''){
					$res_doc['isoverdue'] = 2;
				}elseif(!empty($pat_member['memberid']) && $res_doc['gzh_openid'] != ''){
					$fans = m('member')->getMember($pat_member['memberid']);

					$fkid = pdo_fetch("SELECT id,fansopenid FROM " . tablename('messikefu_fanskefu') . " WHERE weid = {$_W['uniacid']} AND kefuopenid = '{$res_doc['gzh_openid']}' AND fansopenid = '{$fans['openid']}'");
					if($diag_log['orderid'] > 0){
						if($diag_log['consult_type'] == 1){
							if(!empty($fkid)){
								$consult_total = pdo_fetchcolumn("SELECT count(*) total FROM " . tablename('messikefu_chat') . " where weid={$_W['uniacid']} AND openid='{$res_doc['gzh_openid']}' and fkid={$fkid['id']} and time > {$diag_log['createtime']} ");
								if(!empty($consult_total)){
									if($consult_total['total'] >= 6){
										$res_doc['isoverdue'] = 0;
									}else{
										$res_doc['isoverdue'] = 1;
										$res_doc['diaglogid'] = $diag_log['diaglogid'];
									}
								}else{
									$res_doc['isoverdue'] = 1;
									$res_doc['diaglogid'] = $diag_log['diaglogid'];
								}
								
							}else{
								$res_doc['isoverdue'] = 1;
								$res_doc['diaglogid'] = $diag_log['diaglogid'];
							}
							

						}elseif($diag_log['consult_type'] == 2){

							$startDate = strtotime('+7 day',strtotime(date('Y-m-d H:i:s',$diag_log['createtime'])));
							if(!empty($fkid)){
								$endDate = pdo_fetch("SELECT id,time FROM " . tablename('messikefu_chat') . " where weid={$_W['uniacid']} AND openid='{$res_doc['gzh_openid']}' and fkid={$fkid['id']} and time > {$diag_log['createtime']}  ORDER BY time DESC");
								if(!empty($endDate)){
									if($startDate < $endDate['time']){
										$res_doc['isoverdue'] = 0;
									}elseif($startDate > $endDate['time']){
										$res_doc['isoverdue'] = 1;
										$res_doc['diaglogid'] = $diag_log['diaglogid'];
									}else{
										$res_doc['isoverdue'] = 0;
									}
								}else{
									$res_doc['isoverdue'] = 1;
									$res_doc['diaglogid'] = $diag_log['diaglogid'];
								}
								
							}else{
								$res_doc['isoverdue'] = 1;
								$res_doc['diaglogid'] = $diag_log['diaglogid'];
							}
						}
					}else{
						$res_doc['isoverdue'] = 0;
					}

				}else{
					$res_doc['isoverdue'] = 0;
				}
			}

				
			$result['result'] = $res_doc;
			
			
		}
		app_json($result);

	}

	//医生端获取更多视频
	public function video_list()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];

		if (empty($param['page']) || empty($param['pageSize'])) {
			app_error(AppError::$ParamsError);
		}else{
			load()->func('communication');

			$page = intval($param['page']);
			$pageSize = intval($param['pageSize']);

			$condition = ' uniacid = :uniacid and isshow=1 and status = 1 ';
			$params = array(':uniacid' => $uniacid);

			if (!(empty($param['keyword']))){	

				$param['keyword'] = trim($param['keyword']);
				$condition .= ' and videoname like :keyword or content like :keyword ';
				$params[':keyword'] = '%' . $param['keyword'] . '%';
			}
			$videos = pdo_fetchall('select id,fileid,videoname,img_url,content,playcount from ' . tablename('rr_v_member_videos') . ' where '.$condition.'  ORDER BY id DESC', $params);
			if(!empty($videos)){
				foreach ($videos as &$value) {
					
					$getParams = array('fileId' => $value['fileid'], 'infoFilter.0' => 'basicInfo', 'infoFilter.1' => 'transcodeInfo', 'infoFilter.2' => 'snapshotByTimeOffsetInfo');
					$value['videoinfo'] = videoApi('GetVideoInfo', $getParams);
				}
				unset($value);
			}
		}
		
		app_json(array('videos' => $videos));

	}

	//医生端获取更多文章
	public function articles_list()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];

		if (empty($param['page']) || empty($param['pageSize'])) {
			app_error(AppError::$ParamsError);
		}else{

			$page = intval($param['page']);
			$pageSize = intval($param['pageSize']);

			$condition = ' uniacid = :uniacid and isshow=1 and status = 2 ';
			$params = array(':uniacid' => $uniacid);

			if (!(empty($param['keyword']))){	

				$param['keyword'] = trim($param['keyword']);
				$condition .= ' and article_title like :keyword or article_introduction like :keyword ';
				$params[':keyword'] = '%' . $param['keyword'] . '%';
			}
			$articles = pdo_fetchall('select id,cover_url,article_title,article_introduction,click_nums,releasetime from ' . tablename('rr_v_articles') . ' where '.$condition.'  ORDER BY id DESC', $params);
			
		}
		
		app_json(array('articles' => $articles));

	}


	//医生端图文咨询价格设置
	public function consult_money_set()
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
			$data = array();
			switch ($param['type']) {
				case 1:
						$list = pdo_fetch('select default_consult,highgrade_consult from ' . tablename('rr_v_member_doctors') . ' where uniacid=:uniacid and openid=:openid ', array(':uniacid' => $uniacid, ':openid' => $openid));
						$result['consult_money'] = $list;
						$status = 1;
					break;
				case 2:
					$data['default_consult'] = $param['default_consult'];
					$data['highgrade_consult'] = $param['highgrade_consult'];
					pdo_update('rr_v_member_doctors', $data, array('uniacid' => $uniacid, 'openid' => $openid));
					$status = 1;
					break;
				default:
					# code...
					break;
			}
			
		}
		
		$result['status'] = $status;
		
		app_json($result);

	}

	//医生端个人主页患者评价
	public function appraise()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = $param['openid'];//医生openid
		$type = $param['type'];//0,默认全部，1好评，2中评，3差评
		$pageSize = intval($param['pageSize']);
		$page = intval(($param['page'])-1) * $pageSize;
		$limit = ' limit '.$page.','.$pageSize.' ';
		if(empty($openid)){
			app_error(AppError::$ParamsError);
		}else{
			switch ($type) {
				case '1':
					$condition = 'and a.pingtype = 1';
					break;
				case '2':
					$condition = 'and a.pingtype = 2';
					break;
				case '3':
					$condition = 'and a.pingtype = 3';
					break;
				default:
					# code...
					break;
			}
		}
		$list = pdo_fetchall('SELECT a.*,b.nickname,b.avatar FROM '.tablename('messikefu_pingjia').' a, '.tablename('rr_v_member').' b WHERE 
			a.kefuopenid = :kefuopenid AND a.fensiopenid = b.openid '.$condition.' order by a.time desc '.$limit.' ',array(':kefuopenid' => $openid));
		if(!empty($list)){
			foreach ($list as &$value) {
			$value['time'] = date('Y-m-d H:i:s',$value['time']);
			}
			
		}
			//好评总数
			$good_praise = pdo_fetchcolumn('select count(*) as good_praise from '.tablename('messikefu_pingjia').' where pingtype = :pingtype and kefuopenid = :kefuopenid ',array(':pingtype' =>1,':kefuopenid' => $openid));
			//中评总数
			$medium_praise = pdo_fetchcolumn('select count(*) as medium_praise from '.tablename('messikefu_pingjia').' where pingtype = :pingtype and kefuopenid = :kefuopenid ',array(':pingtype' =>2,':kefuopenid' => $openid));
			//差评总数
			$bad_praise = pdo_fetchcolumn('select count(*) as bad_praise from '.tablename('messikefu_pingjia').' where pingtype = :pingtype and kefuopenid = :kefuopenid ',array(':pingtype' =>3,':kefuopenid' => $openid));
			//评论总数
			$total_praise = pdo_fetchcolumn('select count(*) as nums from '.tablename('messikefu_pingjia').' where kefuopenid = :kefuopenid',array(':kefuopenid' => $openid));

			$total['good_praise'] = $good_praise['good_praise'];
			$total['medium_praise'] = $medium_praise['medium_praise'];
			$total['bad_praise'] = $bad_praise['bad_praise'];
			$total['total_nums'] = $total_praise['nums'];	
			app_json(array('list' => $list,'total' => $total));
		

	}

	//医生端个人中心上传医生简介信息
	public function doc_set()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];

		$status = 0;

		if(empty($param['openid'])){
			app_error(AppError::$ParamsError);
		}else{

			if(!empty($param['photo']) && !empty($param['name']) && !empty($param['mobile'])){
				pdo_update('rr_v_member', array('avatar' => trim($param['photo']), 'avatar_status' => 1, 'realname' => trim($param['name']), 'mobile' => trim($param['mobile'])), array('uniacid' => $uniacid, 'openid' => $openid));
				
				$member = m('member')->getMember($openid);
				if(!empty($member['memberid'])){
					$kefu = m('member')->getMember($member['memberid']);
					pdo_update('messikefu_cservice', array('name' => trim($param['name']), 'thumb' => trim($param['photo'])), array('weid' => $uniacid, 'content' => $kefu['openid']));
					pdo_update('messikefu_fanskefu', array('kefunickname' => trim($param['name']), 'kefuavatar' => trim($param['photo'])), array('weid' => $uniacid, 'kefuopenid' => $kefu['openid']));
				}
				

				$status = 1;
			}
			
			$data = array(
				'sex' => intval($param['sex']),
				'age' => trim($param['age']),
				'email' => trim($param['email']),
				'resume' => trim($param['resume']),

			);
			if(!empty($param['introduce']) && is_array($param['introduce'])){
				$data['introduce'] = serialize($param['introduce']);
			}
			if(!empty($param['age']) && !empty($param['resume'])){
				$res = pdo_update('rr_v_member_doctors',$data,array('uniacid' => $uniacid, 'openid' => $openid));
				$status = 1;
			}else{
				$status = 2;
			}

			if(!empty($param['hospital']) && is_array($param['hospital'])){
				$hospital = $param['hospital'];
				pdo_delete('rr_v_member_doctors_hospital', array('uniacid' => $uniacid, 'openid' => $openid));
				foreach ($hospital as &$value) {
					pdo_insert('rr_v_member_doctors_hospital', array('uniacid' => $uniacid, 'openid' => $openid, 'hospital' => $value['hospital'], 'job' => $value['job'], 'departmentid' => intval($value['departmentid']), 'createtime' => time()));
				}
				$status = 1;
			}else{
				$status = 2;
			}

		}

		$result['status'] = $status;
		
		app_json($result);
	}

	//医生端个人中心医生简介详情
	public function introduction_detail()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];

		if(empty($param['openid'])){
			app_error(AppError::$ParamsError);
		}
		
		
		$res = pdo_fetch('SELECT a.openid,a.sex,a.age,a.introduce,a.email,a.resume,b.realname,b.mobile,b.avatar FROM '.tablename('rr_v_member_doctors').' a,'.tablename('rr_v_member').' b where 
			a.uniacid = :uniacid and a.openid = :openid and a.openid = b.openid',array(':uniacid' => $uniacid,':openid' => $openid));

		$hospital = pdo_fetchall('SELECT a.hospital,a.job,a.departmentid,a.createtime,b.name FROM '.tablename('rr_v_member_doctors_hospital').' a,'.tablename('rr_v_member_department').' b where 
			a.uniacid = :uniacid and a.openid = :openid and a.departmentid = b.id and b.isshow = 1 ORDER by a.id DESC',array(':uniacid' => $uniacid,':openid' => $openid));
		if(!empty($res['introduce'])){
			$res['introduce'] = unserialize($res['introduce']);
		}
		$res['hospital'] = $hospital;
		
		app_json(array('res' => $res));
	}

	//医生简介详情页
	public function doc_detail()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$doc_openid = $param['doc_openid'];
		$pat_openid = 'sns_wa_'.$param['pat_openid'];
		if (empty($param['doc_openid'])) {
			app_error(AppError::$ParamsError);
		}else{

			$condition = ' d.uniacid = :uniacid and d.openid = :openid and m.openid=d.openid and m.identity=1';
			$params = array(':uniacid' => $uniacid, ':openid' => $doc_openid);

			$sql = 'select d.id doctorid,d.openid,m.realname,m.nickname,m.avatar,m.memberid,m.level,d.recommend_index,d.hospital,d.job,d.specialty,d.sex,
			d.age,d.education,d.field,d.introduce,d.parentid,d.departmentid,d.default_consult,d.highgrade_consult,d.email,d.resume 
			from ' . tablename('rr_v_member') . ' m,' . tablename('rr_v_member_doctors') . ' d where ' . $condition . '  ';
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
			if(!empty($res_doc['introduce'])){
				$res_doc['introduce'] = unserialize($res_doc['introduce']);
			}else{
				$res_doc['introduce'] = array();
			}
			//返回关注状态
			$followed = pdo_fetch('select id,isfollow from ' . tablename('rr_v_member_follow') . ' where uniacid = :uniacid and 
				doc_openid=:doc_openid and pat_openid=:pat_openid  LIMIT 1', array(':uniacid' => $uniacid, ':doc_openid' => $doc_openid, ':pat_openid' => $pat_openid));
				if(!empty($followed)){
					$res_doc['isfollow'] = $followed['isfollow'];
				}else{
					$res_doc['isfollow'] = 0;
				}

			//医生任职医院
			$hospital = pdo_fetchall('SELECT a.hospital,a.job,a.departmentid,a.createtime,b.name FROM '.tablename('rr_v_member_doctors_hospital').' a,'.tablename('rr_v_member_department').' b where 
			a.uniacid = :uniacid and a.openid = :openid and a.departmentid = b.id and b.isshow = 1 ORDER by a.id DESC',array(':uniacid' => $uniacid,':openid' => $doc_openid));
			if(empty($hospital)){
				$hospital = array(
					array('hospital' => $res_doc['hospital'], 'job' => $res_doc['job'], 'name' => $res_doc['department']),
				);
			}
			$res_doc['hospital'] = $hospital;

		}
		app_json(array('res' => $res_doc));
	}


	//医生端热门科普文章、视频
	public function hot_data()
	{	
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		// $openid = 'sns_wa_'.$param['openid'];
		$type = $param['type'];//1文章，2是视频
		if(empty($type)){
			app_error(AppError::$ParamsError);
		}
		// if(!empty($openid)){
			if($type == 1){
				//文章
				$params = array(':uniacid' => $uniacid);
				$sql = 'SELECT * FROM ' . tablename('rr_v_articles') . ' where uniacid = :uniacid 
				and isshow = 1 and id IN(SELECT f.article_id FROM (SELECT article_id,COUNT(*) mycount FROM '.tablename('rr_v_comments').' WHERE uniacid = :uniacid AND video_id = 0 AND cid = 0 AND rid = 0 GROUP BY article_id ORDER BY mycount DESC) f ) LIMIT 3';
				$list = pdo_fetchall($sql, $params);
				// switch (count($list)) {
				// 	case '0':
				// 		$sql = 'SELECT * FROM ' . tablename('rr_v_articles') . ' where uniacid = :uniacid and openid <> :openid 
				// 		and isshow = 1 ORDER BY click_nums DESC LIMIT 3';
				// 		$list = pdo_fetchall($sql,$params);
				// 		break;
				// 	case '1':
				// 	//查2条其他医生文章追加到$list
				// 		$res = pdo_fetchall('SELECT * FROM ' . tablename('rr_v_articles') . ' where uniacid = :uniacid and openid <> :openid 
				// 			and isshow = 1 ORDER BY click_nums DESC LIMIT 2',$params);
				// 		foreach ($res as $value) {
				// 			$list[] = $value;
				// 		}
				// 		break;
				// 	case '2':
				// 	//查1条其他医生文章追加到$list
				// 		$list[]  = pdo_fetch('SELECT * FROM ' . tablename('rr_v_articles') . ' where uniacid = :uniacid and openid <> :openid 
				// 			and isshow = 1 ORDER BY click_nums DESC LIMIT 1',$params);
				// 		break;
					
				// }
				if(!empty($list)){
					foreach ($list as &$val) {
						$val['createtime'] = date("Y-m-d H:i:s", $val['createtime']);
						if($val['releasetime'] != 0){
							$val['releasetime'] = date("Y-m-d", $val['releasetime']);
						}
					
					}
				}
				
			}elseif ($type ==2) {
				//热门视频
				$params = array(':uniacid' => $uniacid);
				$sql = 'SELECT * FROM ' . tablename('rr_v_member_videos') . ' where uniacid = :uniacid 
				and isshow = 1 and id IN(SELECT f.video_id FROM (SELECT video_id,COUNT(*) mycount FROM '.tablename('rr_v_comments').' WHERE uniacid = :uniacid AND article_id = 0 AND cid = 0 AND rid = 0 GROUP BY video_id ORDER BY mycount DESC) f ) LIMIT 5';
				$list = pdo_fetchall($sql, $params);
				// switch (count($list)) {
				// 	case '0':
				// 		$sql = 'SELECT * FROM ' . tablename('rr_v_member_videos') . ' where uniacid = :uniacid and openid <> :openid 
				// 		and isshow = 1 ORDER BY playcount DESC LIMIT 3';
				// 		$list = pdo_fetchall($sql,$params);
				// 		break;
				// 	case '1':
				// 	//查2条其他医生视频追加到$list
				// 		$res = pdo_fetchall('SELECT * FROM ' . tablename('rr_v_member_videos') . ' where uniacid = :uniacid and openid <> :openid 
				// 			and isshow = 1 ORDER BY playcount DESC LIMIT 2',$params);
				// 		foreach ($res as $value) {
				// 			$list[] = $value;
				// 		}
				// 		break;
				// 	case '2':
				// 	//查1条其他医生视频追加到$list
				// 		$list[]  = pdo_fetch('SELECT * FROM ' . tablename('rr_v_member_videos') . ' where uniacid = :uniacid and openid <> :openid 
				// 			and isshow = 1 ORDER BY playcount DESC LIMIT 1',$params);
				// 		break;
					
				// }
				load()->func('communication');
				if(!empty($list)){
					foreach ($list as &$val) {
						$val['createtime'] = date("Y-m-d H:i:s", $val['createtime']);
						if($val['sendtime'] != 0){
							$val['sendtime'] = date("Y-m-d", $val['sendtime']);
						}

						$getParams = array('fileId' => $val['fileid'], 'infoFilter.0' => 'basicInfo', 'infoFilter.1' => 'transcodeInfo', 'infoFilter.2' => 'snapshotByTimeOffsetInfo');
						$val['videoinfo'] = videoApi('GetVideoInfo', $getParams);
					
					}
				}
				
			}
		// }else{
		// 	//医生openid为空，查3条记录
		// 	if($type ==1){
		// 		//文章
		// 		$sql = 'SELECT * FROM ' . tablename('rr_v_articles') . ' where uniacid = :uniacid and isshow = 1 ORDER BY click_nums DESC LIMIT 3';
		// 		$list = pdo_fetchall($sql,array(':uniacid' => $uniacid));
		// 	}elseif($type ==2){
		// 		//视频
		// 		$sql = 'SELECT * FROM ' . tablename('rr_v_member_videos') . ' where uniacid = :uniacid and isshow = 1 ORDER BY playcount DESC LIMIT 3';
		// 		$list = pdo_fetchall($sql,array(':uniacid' => $uniacid));
		// 	}
		// }

		
		app_json(array('list' => $list));
		

	}

	//医生端获取打赏类别
	public function get_reward()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = $param['openid'];

		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}

		//医生基本信息
		$condition = ' d.uniacid = :uniacid and d.openid = :openid and m.openid=d.openid and m.identity=1';
		$params = array(':uniacid' => $uniacid, ':openid' => $openid);

		$sql = 'select d.id doctorid,d.openid,m.realname,m.nickname,m.avatar,m.level,d.recommend_index,d.hospital,d.job,d.field,d.parentid,d.departmentid,m.memberid,d.default_consult,d.highgrade_consult from ' . tablename('rr_v_member') . ' m,' . tablename('rr_v_member_doctors') . ' d where ' . $condition . '  ';
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
		$res_doc['department'] = $res['name'];
		
		//打赏类别
		$list = pdo_fetchall('SELECT * FROM ' . tablename('rr_v_reward') . ' where uniacid = :uniacid and status = 1 ORDER BY displayorder', array(':uniacid' => $uniacid));
		if(!empty($list)){
			foreach ($list as &$value) {
				$value['icon'] = tomedia($value['icon']);
			}
		}	

		app_json(array('list' => $list, 'res_doc' => $res_doc));
	}

	//医生端获取我的打赏列表
	public function my_reward()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = $param['openid'];

		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}

		$condition = ' a.uniacid = :uniacid AND a.rewardid = b.id AND a.pat_openid = c.openid AND a.orderid > 0 AND a.doc_openid = :openid';
		$params = array(':uniacid' => $uniacid, ':openid' => $openid);

		$sql = 'SELECT a.pat_openid,a.orderid,a.remark,a.paytime,b.title,b.icon,b.price,c.nickname,c.avatar FROM ' . tablename('rr_v_member_doctors_reward') . ' a,' . tablename('rr_v_reward') . ' b,' . tablename('rr_v_member') . ' c where ' . $condition . ' ORDER BY a.orderid DESC';
		
		$list = pdo_fetchall($sql, $params);
		if(!empty($list)){
			foreach ($list as &$value) {
				$value['icon'] = tomedia($value['icon']);
			}
		}	

		app_json(array('list' => $list));
	}

	//医生端获取我的结算账单
	public function doctors_bill()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];

		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}

		$condition = ' uniacid = :uniacid AND status = 1 AND openid = :openid';
		$params = array(':uniacid' => $uniacid, ':openid' => $openid);

		$totalprice = pdo_fetchall('SELECT IFNULL(SUM(billprice),0.00) totalprice FROM ' . tablename('rr_v_member_doctors_bill') . ' where '.$condition.' ', $params);

		$condition .= ' GROUP BY tradedate ORDER BY tradedate';
		
		$list = pdo_fetchall('SELECT * FROM ' . tablename('rr_v_member_doctors_bill') . ' where '.$condition.' ', $params);
		if(!empty($list)){
			foreach ($list as &$value) {
				$value['tradetime'] = date('Y-m-d H:i', $value['tradetime']);
			}
		}

		app_json(array('list' => $list, 'totalprice' => $totalprice));
	}

	//获取医生端在线服务条款内容
	public function get_liability()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		
		$result = pdo_fetch('select * from '.tablename('rr_v_liability').' where uniacid=:uniacid and status=1 ',array(':uniacid' => $uniacid));
		$result['detail'] = str_replace('section', 'div', $result['detail']);
		$result['detail'] = str_replace('"Microsoft YaHei"', '\'Microsoft YaHei\'', $result['detail']);

		app_json(array('result' => $result));
	}

	//获取医生常用地址
	public function get_address()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];

		if (empty($param['openid'])) {
			app_error(AppError::$ParamsError);
		}else{

			$list = pdo_fetchall('SELECT * FROM ' . tablename('rr_v_member_doctors_address') . ' WHERE openid = "' . $openid . '" AND uniacid=' . $uniacid . ' ORDER BY id DESC');
			
		}
		
		app_json(array('list' => $list));

	}

	//添加医生常用地址
	public function save_address()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$param = $_GPC['param'];
		$openid = 'sns_wa_'.$param['openid'];
		$id = intval($param['id']);

		$status = 0;

		if (empty($param['openid']) || empty($param['address'])) {
			app_error(AppError::$ParamsError);
		}else{

			$data = array(
				'uniacid' 		=>	$uniacid,
				'openid' 		=>	$openid,
				'address' 		=>	trim($param['address']),
				'createtime' 	=>	time(),
			);

			if(!empty($id)){
				pdo_update('rr_v_member_doctors_address', $data, array('uniacid' =>	$uniacid, 'openid' => $openid, 'id' => $id));
			}else{
				pdo_insert('rr_v_member_doctors_address', $data);
			}

			$status = 1;
		}

		$result['status'] = $status;
		
		app_json($result);

	}

	//删除医生常用地址
	public function delete_address()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$id = intval($_GPC['id']);

		$status = 0;

		if (empty($_GPC['id'])) {
			app_error(AppError::$ParamsError);
		}else{

			pdo_delete('rr_v_member_doctors_address', array('uniacid' => $uniacid, 'id' => $id));

			$status = 1;
		}

		$result['status'] = $status;
		
		app_json($result);

	}

}

?>
