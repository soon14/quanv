<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_RrvV3Page extends WebPage
{

	public function main()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		
		$condition = ' f.uniacid=:uniacid and f.status <> 3';
		if (!(empty($_GPC['keyword']))) 
		{	
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and ( f.videoname like :keyword or f.content like :keyword)';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';		
		}
		$params = array(':uniacid' => $_W['uniacid']);

		$join = '';
		$join .= ' join ' . tablename('rr_v_member_videos') . ' f on f.openid=dm.openid';
		$sql = 'select dm.realname,dm.mobile,dm.nickname,dm.avatar,f.* from ' . tablename('rr_v_member') . ' dm ' . $join . ' where  ' . $condition . '  ORDER BY videoname,id DESC';
		$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		$list = pdo_fetchall($sql, $params);
		
		foreach ($list as &$row) {
			$row['createtime'] = date('Y-m-d H:i:s', $row['createtime']);
		}
		unset($row);
		$total = pdo_fetchcolumn('select count(*) from' . tablename('rr_v_member') . ' dm ' . $join . ' where  ' . $condition . ' ', $params);
		$pager = pagination2($total, $pindex, $psize);

		include $this->template();
	}

	public function detail()
	{
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];
		$id = $_GPC['id'];
		if(empty($id)){
			show_json(0,'无该条数据！');
		}
		$sql = 'select dm.realname,dm.nickname,dm.avatar,f.* from '.tablename('rr_v_member'). 'dm,' .tablename('rr_v_member_videos'). 'f where f.uniacid = :uniacid and dm.openid = f.openid and f.id = :id';
		$params = array(':uniacid' => $uniacid,':id' => $id);
		$info = pdo_fetch($sql,$params);
		$info['createtime'] = date('Y-m-d H:i:s', $info['createtime']);
		$member = m('member')->getMember($info['openid']);
		if($_W['ispost']){
			$data = array(
				'videoname' => trim($_GPC['videoname']),
				'img_url' => tomedia($_GPC['thumb']),
				'content' => trim($_GPC['content']),
				'money' => intval($_GPC['money']),
				'playcount' => intval($_GPC['playcount']),
				'turn_nums' => intval($_GPC['turn_nums']),
				'tag' => trim($_GPC['tag']),

				);
			pdo_update('rr_v_member_videos',$data,array('id' => $id));
			show_json(1);
		}

		include $this->template();

	}


	public function enabled()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		$items = pdo_fetchall('SELECT * FROM ' . tablename('rr_v_member_videos') . ' WHERE id = ' . $id . ' AND uniacid=' . $_W['uniacid']);
		if(empty($items)){
			show_json(0,'无该条数据或已删除！');
		}
		else{
			pdo_update('rr_v_member_videos', array('status' => intval($_GPC['status']), 'audittime' => time()), array('id' => $id));
			plog('videos.list.enabled', '修改视频审核状态<br/>ID: ' . $items['id'] . '<br/>视频名称: ' . $items['videoname']);

			show_json(1, array('url' => referer()));
		}

	}

	//修改审核不过的原因
	public function checked(){
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$checked = $_GPC['checked'];
		$videoname = $_GPC['videoname'];

		if ($_W['ispost']) {
			pdo_update('rr_v_member_videos', array('checked' => $checked,'status' => 2), array('id' => $id, 'uniacid' => $_W['uniacid']));
		
			show_json(1);
		}

		include $this->template();
	}

	//已发布视频退回前台重新修改
	public function goback(){
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$goback = $_GPC['goback'];
		$videoname = $_GPC['videoname'];

		if ($_W['ispost']) {
			pdo_update('rr_v_member_videos', array('checked' => $goback,'status' => 2,'isshow' => 0,'sendtime' => 0), array('id' => $id, 'uniacid' => $_W['uniacid']));
			show_json(1);
		}

		include $this->template();
	}

}

?>
