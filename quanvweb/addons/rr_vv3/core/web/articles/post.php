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
		pdo_update('rr_v_articles',array('status' => 2),array('id' => $id));
		show_json(1);
	}
	
	// public function noaudit()
	// {
	// 	global $_W,$_GPC;
	// 	$id = $_GPC['id'];
	// 	pdo_update('rr_v_articles',array('status' => 1),array('id' => $id));
	// 	show_json(1);
	// }
	
	
	public function delete()
	{
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];
		$id = $_GPC['id'];
		pdo_update('rr_v_articles',array('status' => 3),array('id' => $id,'uniacid' => $uniacid));
		show_json(1);
	
	}
	
	//修改审核不过的原因
	public function checked(){
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$checked = $_GPC['checked'];
		$article_title = $_GPC['article_title'];

		if ($_W['ispost']) {
			pdo_update('rr_v_articles', array('checked' => $checked,'status' => 1), array('id' => $id, 'uniacid' => $_W['uniacid']));
		
			show_json(1);
		}

		include $this->template();

	}

	//已发布文章退回前台重新修改
	public function goback(){
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$goback = $_GPC['goback'];
		$article_title = $_GPC['article_title'];

		if ($_W['ispost']) {
			pdo_update('rr_v_articles', array('checked' => $goback,'status' => 1,'isshow' => 0,'releasetime' => 0), array('id' => $id, 'uniacid' => $_W['uniacid']));
		
			show_json(1);
		}

		include $this->template();

	}

	

}

?>
