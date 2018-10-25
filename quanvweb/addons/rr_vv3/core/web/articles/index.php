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
			$condition .= ' and ( f.article_title like :keyword or f.article_content like :keyword or dm.nickname like :keyword or dm.realname like :keyword)';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';		
		}
		$join = '';
		$join .= ' join ' . tablename('rr_v_articles') . ' f on f.openid=dm.openid';
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
		$sql = 'select dm.realname,dm.nickname,dm.avatar,f.* from '.tablename('rr_v_member'). 'dm,' .tablename('rr_v_articles'). 'f where f.uniacid = :uniacid and dm.openid = f.openid and f.id = :id';
		$params = array(':uniacid' => $uniacid,':id' => $id);
		$info = pdo_fetch($sql,$params);
		$member = m('member')->getMember($info['openid']);
		// $info['article_content'] = unserialize($info['article_content']);

		// foreach ($info['article_content'] as $key => $value) 
		// {
		// 	if($value['state'] == 'text')
		// 	{
		// 		$article_content .= $value['text'].'<br/><br/>';
		// 	}else if($value['state'] == 'img')
		// 	{
		// 		$article_content .= '<img src="'.$value['img'].'"/><br/><br/>';
		// 	}
		// }
		// $info['new_content'] = $article_content; //拼接html标签后的文章内容
		if($_W['ispost']){
			if(!empty($id)){
				$data = array(
					'article_title' => trim($_GPC['article_title']),
					'cover_url' => tomedia($_GPC['cover_url']),
					'article_introduction' => trim($_GPC['article_introduction']),
					'money' => intval($_GPC['money']),
					'click_nums' => intval($_GPC['click_nums']),
					'turn_nums' => intval($_GPC['turn_nums']),
					'tag' => trim($_GPC['tag']),
				);
				
				$data['article_content'] = m('common')->html_images($_GPC['article_content']);
				
				pdo_update('rr_v_articles',$data,array('id' => $id));
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

		$articles = pdo_fetchall('SELECT * FROM ' . tablename('rr_v_articles') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $uniacid);

		foreach ($articles as &$article) {
			pdo_delete('rr_v_articles', array('uniacid' => $uniacid, 'id' => $article['id']));
			plog('articles.delete', '删除文章  ID: ' . $article['id'] . ' <br/>文章标题: ' . $article['article_title']);
		}
		show_json(1, array('url' => referer()));
	}

	public function add()
	{
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];
		
		$sql = 'select openid,realname,nickname,avatar from '.tablename('rr_v_member'). ' where uniacid = :uniacid and identity = 1 and isaudit =2 ORDER BY realname,nickname';
		$params = array(':uniacid' => $uniacid);
		$members = pdo_fetchall($sql,$params);

		if($_W['ispost']){
			
			$data = array(
				'uniacid' 		=> $uniacid,
				'openid' 		=> $_GPC['member'],
				'article_title' => trim($_GPC['title']),
				'article_introduction' => trim($_GPC['article_introduction']),
				'money' 		=> trim($_GPC['money']),
				'status' 		=> 2,
				'checked'		=> '',
				'createtime' 	=> time()
			);
			$data['article_content'] = m('common')->html_images($_GPC['content']);
			if(!empty($_GPC['cover_url'])){
				$data['cover_url'] = tomedia(trim($_GPC['cover_url']));
			}
			pdo_insert('rr_v_articles', $data);
			show_json(1);
			
		}
		include $this->template();

	}


}

?>
