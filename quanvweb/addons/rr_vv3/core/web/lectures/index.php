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
		$condition = ' and dm.uniacid=:uniacid and f.status != :status';
		$params = array(':uniacid' => $_W['uniacid'],':status' => 3);

		if (!(empty($_GPC['keyword']))) 
		{	
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and ( f.lecture_title like :keyword or f.lecture_introduction like :keyword or f.lecture_author like :keyword )';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';		
		}
		$join = '';
		$join .= ' join ' . tablename('rr_v_lectures') . ' f on f.openid=dm.openid';
		$sql = 'select dm.realname,dm.mobile,dm.nickname,dm.avatar,f.* from ' . tablename('rr_v_member') . ' dm ' . $join . ' where 1 ' . $condition . '  ORDER BY id DESC';
		$sql .= ' limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		$list = pdo_fetchall($sql, $params);
		$total = pdo_fetchcolumn('select count(*) from' . tablename('rr_v_member') . ' dm ' . $join . ' where 1 ' . $condition . ' ', $params);
		$pager = pagination2($total, $pindex, $psize);

		include $this->template();
	}

	public function detail()
	{
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];
		$id = $_GPC['id'];
		if(!empty($id)){
		$sql = 'select dm.realname,dm.nickname,dm.avatar,f.* from '.tablename('rr_v_member'). 'dm,' .tablename('rr_v_lectures'). 'f where f.uniacid = :uniacid and dm.openid = f.openid and f.id = :id';
		$params = array(':uniacid' => $uniacid,':id' => $id);
		$info = pdo_fetch($sql,$params);
		$member = m('member')->getMember($info['openid']);
		$info['article_content'] = unserialize($info['article_content']);
		//查询讲座报名人数
			$order = pdo_fetchcolumn('select count(*) as total from '.tablename('rr_v_order_goods').' a, '.tablename('rr_v_order').' b where uniacid = :uniacid and b.id = a.orderid and b.status = 3 and b.paytype = 21 and a.goodstype = 4 and a.goodid = '.$id.' ');
			if(empty($order)){
				$info['apply_nums'] = 0;
			}else{
				$info['apply_nums'] = $order['total'];
			}
		foreach ($info['article_content'] as $key => $value) 
		{
			if($value['state'] == 'text')
			{
				$article_content .= $value['text'].'<br/><br/>';
			}else if($value['state'] == 'img')
			{
				$article_content .= '<img src="'.$value['img'].'"/><br/><br/>';
			}
		}
		$info['new_content'] = $article_content; //拼接html标签后的文章内容
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($_W['ispost']){
			$data = array(
				'cover_url' => tomedia($_GPC['cover_url']),
				'lecture_title' => trim($_GPC['lecture_title']),
				'lecture_introduction' => trim($_GPC['lecture_introduction']),
				'lecture_address' => trim($_GPC['lecture_address']),
				'lecture_nums' => intval($_GPC['lecture_nums']),
				'lecture_cost' => intval($_GPC['lecture_cost']),
				'start_time' => $_GPC['start_time'],
				'end_time' => $_GPC['end_time'],
				'remark' => trim($_GPC['remark']),

			);
			pdo_update('rr_v_lectures',$data,array('id' => $id));
			show_json(1);
		}

		}
		include $this->template();

	}

	//批量删除
	public function delete()
	{
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];
		$id = $_GPC['id'];
		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$articles = pdo_fetchall('SELECT * FROM ' . tablename('rr_v_lectures') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($articles as $article) {
			pdo_update('rr_v_lectures', array('status' => 3,'isshow' => 0),array('id' => $article['id']));
			plog('articles.delete', '删除文章  ID: ' . $article['id'] . ' <br/>文章标题: ' . $article['article_title']);
		}
		show_json(1, array('url' => referer()));
	}

	


}

?>
