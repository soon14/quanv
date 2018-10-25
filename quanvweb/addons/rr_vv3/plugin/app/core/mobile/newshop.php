<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}
 
require rr_vv3_PLUGIN . 'app/core/page_mobile.php';
class Newshop_RrvV3Page extends AppMobilePage
{
	public function get_docindex()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		// $defaults = array(
		// 	'adv'       	=> array('text' => '幻灯片', 'visible' => 1),
		// 	'search'    	=> array('text' => '搜索栏', 'visible' => 1),
		// 	'nav'       	=> array('text' => '导航栏', 'visible' => 1),
		// 	'notice'    	=> array('text' => '公告栏', 'visible' => 1),
		// 	'banner'    	=> array('text' => '广告栏', 'visible' => 1),
		// 	'lectures'    	=> array('text' => '讲座栏', 'visible' => 1)
		// 	);
		$defaults = array(
			'banner'    	=> array('text' => '广告栏', 'visible' => 1),
			'notice'    	=> array('text' => '公告栏', 'visible' => 1),
			'lectures'    	=> array('text' => '讲座栏', 'visible' => 1)
			);
		$appsql = '';

		if ($this->iswxapp) {
			$appsql = ' and iswxapp = 1';
		}

		$sorts = ($this->iswxapp ? $_W['shopset']['shop']['indexsort_wxapp'] : $_W['shopset']['shop']['indexsort']);
		$sorts = (isset($sorts) ? $sorts : $defaults);
		// $advs = pdo_fetchall('select id,advname,link,thumb from ' . tablename('rr_v_adv') . ' where uniacid=:uniacid' . $appsql . ' and enabled=1 order by displayorder desc', array(':uniacid' => $uniacid));
		// $advs = set_medias($advs, 'thumb');
		
		// $navs = pdo_fetchall('select id,navname,url,icon from ' . tablename('rr_v_nav') . ' where uniacid=:uniacid' . $appsql . ' and status=1 order by displayorder desc', array(':uniacid' => $uniacid));
		// $navs = set_medias($navs, 'icon');
		$banners = pdo_fetchall('select id,bannername,link,thumb,show_mode from ' . tablename('rr_v_banner') . ' where uniacid=:uniacid' . $appsql . ' and enabled=1 order by displayorder desc', array(':uniacid' => $uniacid));
		$banners = set_medias($banners, 'thumb');
		$bannerswipe = ($this->iswxapp ? intval($_W['shopset']['shop']['bannerswipe_wxapp']) : intval($_W['shopset']['shop']['bannerswipe']));


		$notices = pdo_fetchall('select id, title, link, thumb from ' . tablename('rr_v_notice') . ' where uniacid=:uniacid' . $appsql . ' and status=1 order by id desc limit 5', array(':uniacid' => $uniacid));
		$notices = set_medias($notices, 'thumb');

		$lectures = pdo_fetchall('select a.id,m.realname,m.nickname,d.hospital,d.parentid,d.departmentid,a.cover_url,a.lecture_author,'.'
			a.lecture_title,a.lecture_introduction from ' . tablename('rr_v_lectures') . ' a,' . tablename('rr_v_member') . ' m,' . tablename('rr_v_member_doctors') . '
			 d where a.uniacid=:uniacid and a.isshow=1 and a.status = 2 and a.openid=m.openid and m.openid=d.openid ORDER BY a.id DESC LIMIT 3', array(':uniacid' => $uniacid));
		foreach ($lectures as &$row) {
			if($row['departmentid'] != 0){
				$departmentid = $row['departmentid'];
			}else{
				$departmentid = $row['parentid'];
			}
			$res = pdo_fetch('select id,name from ' . tablename('rr_v_member_department') . ' where uniacid = :uniacid and id=:id  LIMIT 1', array(':uniacid' => $uniacid, ':id' => $departmentid));
			$row['department'] = $res['name'];
		}

		$newsorts = array();

		foreach ($sorts as $key => $old) {
			$old['type'] = $key;

			// if ($key == 'adv') {
			// 	$old['data'] = !empty($advs) ? $advs : array();
			// }
			// else if ($key == 'nav') {
			// 	$old['data'] = !empty($navs) ? $navs : array();
			// }
			// else 
			if ($key == 'banner') {
				$old['data'] = !empty($banners) ? $banners : array();
				$old['bannerswipe'] = !empty($bannerswipe) ? $bannerswipe : array();
			}
			else if ($key == 'notice') {
				$old['data'] = !empty($notices) ? $notices : array();
			}
			else if ($key == 'lectures') {
				$old['data'] = !empty($lectures) ? $lectures : array();
			}

			$newsorts[] = $old;
		}

		app_json(array('sorts' => $newsorts));
	}

	/**
     * 获取搜索栏
     */
	public function get_search()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		// $searchs = pdo_fetchall('select id,title from ' . tablename('rr_v_search') . ' where uniacid=:uniacid and iswxapp = 1 and enabled=1 
		// 	order by displayorder desc', array(':uniacid' => $uniacid));
		$searchs = pdo_fetchall('select id,content from ' . tablename('rr_v_hotsearch') . ' where uniacid=:uniacid and type = 3 
			order by search_count desc LIMIT 10', array(':uniacid' => $uniacid));

		app_json(array('searchs' => $searchs));
	}

	/**
     * 获取科室分类
     */
	public function get_category()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		
		$parents = array();
		$children = array();
		$data = pdo_fetchall('select * from ' . tablename('rr_v_member_department') . ' where uniacid=:uniacid and isshow = 1 order by displayorder desc', array(':uniacid' => $uniacid));
		foreach ($data as $index => $row ) 
		{
			if (!(empty($row['parentid']))) 
			{
				if ($row[$row['parentid']]['parentid'] == 0) 
				{
					$row[$row['parentid']]['level'] = 2;
				}
				else 
				{
					$row[$row['parentid']]['level'] = 3;
				}
				$children[$row['parentid']][] = $row;
				unset($data[$index]);
			}
			else 
			{
				$row['level'] = 1;
				$parents[] = $row;
			}
		}
		$category = array('parent' => $parents, 'children' => $children);

		$allcategory = array();
		foreach ($category['parent'] as $p) {
			//一级
			$parent = array('id' => $p['id'], 'name' => $p['name'], 'child' => array());
			if (is_array($category['children'][$p['id']])) {
				foreach ($category['children'][$p['id']] as $c) {
					if (!empty($c['id'])) {
						//二级
						$child = array('id' => $c['id'], 'name' => $c['name'], 'child' => array(), 'level' => $c['level']);
					}
					if (is_array($category['children'][$c['id']])) {
						foreach ($category['children'][$c['id']] as $t) {
							if (!empty($t['id'])) {
								//三级
								$child['child'][] = array('id' => $t['id'], 'name' => $t['name']);
							}
						}
					}
					$parent['child'][] = $child;
				}
			}
			$allcategory[] = $parent;
		}

		app_json(array('category' => $allcategory));
	}

	/**
     * 返回是否开启商品展示
     */
	public function get_goodsswitch()
	{
		global $_W;
		global $_GPC;
		$data = m('common')->getSysset('app');
		if(empty($data['goodsswitch'])){
			$goodsswitch = 0;
		}else{
			$goodsswitch = $data['goodsswitch'];
		}

		if(empty($data['version'])){
			$data['version'] = '';
		}

		app_json(array('goodsswitch' => $goodsswitch, 'version' => $data['version']));
	}

	/**
     * 获取商品列表
     */
	public function get_goods_list()
	{
		global $_W;
		global $_GPC;
		$args = array('pagesize' => 20, 'page' => 1);

		$goods = m('goods')->getList($args);

		app_json(array('goods' => $goods['list']));
	}

}

?>
