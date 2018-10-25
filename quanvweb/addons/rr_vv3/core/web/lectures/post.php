<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Post_RrvV3Page extends WebPage
{

	public function audit()
	{
		global $_W,$_GPC;
		$id = $_GPC['id'];
		pdo_update('rr_v_lectures',array('status' => 2,'nopass' => 1),array('id' => $id));
		show_json(1);
	}
	
	
	public function delete()
	{
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];
		$id = $_GPC['id'];
		pdo_update('rr_v_lectures',array('status' => 3),array('id' => $id,'uniacid' => $uniacid));
		show_json(1);
	
	}

	//查看已报名会员列表
	public function apply_list()
	{
		global $_W,$_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$id = $_GPC['id'];
		$lecture_title = $_GPC['lecture_title'];
		$uniacid = $_W['uniacid'];
		$condition = ' b.lid = a.id and b.uniacid = :uniacid and a.id = :id ';
		$params = array(':uniacid' => $uniacid,':id' => $id);
		if (!(empty($_GPC['keyword']))) 
		{	
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and ( b.username like :keyword or b.mobile like :keyword)';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';		
		}
		
		$sql = 'select a.lecture_title,b.* from '.tablename('rr_v_lectures').' a,'.tablename('rr_v_lectures_apply_client').'b where  '.$condition.' order by b.id desc';
		$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;

		$list = pdo_fetchall($sql,$params);
		$total = pdo_fetchcolumn('select count(*) from' . tablename('rr_v_lectures') . ' a, ' . tablename('rr_v_lectures_apply_client') . ' b where  ' . $condition . ' ', $params);
		$pager = pagination2($total, $pindex, $psize);
		include $this->template();
	}
	public function apply_list_detail()
	{
		global $_W,$_GPC;
		$id = $_GPC['id'];
		$uniacid = $_W['uniacid'];
		$sql = 'select a.lecture_title,b.* from '.tablename('rr_v_lectures').' a,'.tablename('rr_v_lectures_apply_client').'b where b.lid = a.id and b.uniacid = :uniacid  and b.id = :id';
		$info = pdo_fetch($sql,array(':uniacid' => $uniacid,':id' => $id));
		include $this->template();
	}

	//修改审核不过的原因
	public function checked(){
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$checked = $_GPC['checked'];
		$lecture_title = $_GPC['lecture_title'];

		if ($_W['ispost']) {
			pdo_update('rr_v_lectures', array('checked' => $checked,'status' => 1,'nopass' => 1), array('id' => $id, 'uniacid' => $_W['uniacid']));
		
			show_json(1);
		}

		include $this->template();
	}

	//已发布讲座退回前台重新修改
	public function goback(){
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$goback = $_GPC['goback'];
		$article_title = $_GPC['article_title'];

		if ($_W['ispost']) {
			pdo_update('rr_v_lectures', array('checked' => $goback,'status' => 1,'isshow' => 0,'releasetime' => 0), array('id' => $id, 'uniacid' => $_W['uniacid']));
		
			show_json(1);
		}

		include $this->template();

	}
	

}

?>
