<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="we7-page-title">微官网</div>
<ul class="we7-page-tab">
	<li><a href="<?php  echo url('site/multi')?>">微官网列表</a></li>
	<li><a href="<?php  echo url('site/style')?>">微官网模板</a></li>
	<li><a href="<?php  echo url('site/article')?>">文章管理</a></li>
	<li class="active"><a href="<?php  echo url('site/category')?>">文章分类管理</a></li>
</ul>
<div class="we7-page-search we7-padding-bottom clearfix">
	<div class="pull-right">
		<?php  if(empty($id)) { ?><a href="<?php  echo url('site/category/post')?>" class="btn btn-primary we7-padding-horizontal">+新建文章分类</a><?php  } ?>
		<?php  if(!empty($id)) { ?><a href="<?php  echo url('site/category/post', array('id' => $id))?>" class="btn btn-primary we7-padding-horizontal">编辑文章分类</a><?php  } ?>
	</div>
</div>
<form action="./index.php?c=site&a=category&do=delete" class="we7-form" method="post">
<table class="table we7-table table-hover article-list vertical-middle" id="js-wesite-category-display">
	<col width="80px">
	<col width="130px"/>
	<col width="235px">
	<col width="90px"/>
	<col width=""/>
	<tr>
		<th></th>
		<th class="text-left">排序</th>
		<th class="text-left">分类名称</th>
		<th>设为栏目</th>
		<th class="text-right">操作</th>
	</tr>
	<?php  if(is_array($category)) { foreach($category as $row) { ?>
		<tr>
			<td>
				<input type="checkbox" we7-check-all="1" name="rid[]" id="rid-<?php  echo $row['id'];?>" value="<?php  echo $row['id'];?>">
				<label for="rid-<?php  echo $row['id'];?>">&nbsp;</label>
			</td>
			<td class="text-left"><?php  echo $row['displayorder'];?></td>
			<td class="text-left">
				<span><?php  echo $row['name'];?></span>
			</td>
			<td>
				<label>
					<div class="switch switch<?php echo $row['enabled'] ? 'On' : 'Off'?>" onclick="changeStatus(<?php  echo $row['id'];?>)"></div>
				</label>
			</td>
			<td style="position: relative;">
				<div class="link-group">
					<?php  if(empty($row['parentid'])) { ?>
					<a href="<?php  echo url('site/category/post', array('parentid' => $row['id']))?>" >&nbsp;+添加子分类</a>
					<?php  } ?>
					<a href="javascript:;" data-url="<?php  echo murl('site/site', array('cid' => $row['id']), true, true)?>"  class="js-clip">复制链接</a>
					<a href="<?php  echo url('site/article/post', array('pcate' => $row['id']));?>" >添加文章</a>
					<a href="<?php  echo url('site/category/post', array('id' => $row['id']));?>" >编辑</a>
					<a href="<?php  echo url('site/category/delete', array('id' => $row['id']));?>" class="del" onclick="return confirm('确认删除此分类吗？');return false;">删除</a>
				</div>
			</td>
		</tr>
		<?php  if(is_array($children[$row['id']])) { foreach($children[$row['id']] as $row) { ?>
		<tr class="bg-light-gray">
			<td>
				<input type="checkbox" name="rid[]" id="rid-<?php  echo $row['id'];?>" value="<?php  echo $row['id'];?>">
				<label for="rid-<?php  echo $row['id'];?>">&nbsp;</label>
			</td>
			<td class="text-left">
				<div class="pad-left">
					<?php  echo $row['displayorder'];?>
				</div>
			</td>
			<td class="text-left"><span class="color-gray" style="margin-left: 50px;"><?php  echo $row['name'];?></span></td>
			<td>
				<label>
					<div class="switch switch<?php echo $row['enabled'] ? 'On' : 'Off'?>" onclick="changeStatus(<?php  echo $row['id'];?>)"></div>
				</label>
			</td>
			<td style="position: relative;" class="text-right">
				<div class="link-group">
					<a href="javascript:;" data-url="<?php  echo murl('site/site', array('cid' => $row['id']), true, true)?>" class="we7-margin-right-sm js-clip">复制链接</a>
					<a href="<?php  echo url('site/article/post', array('pcate' => $row['parentid'], 'ccate' => $row['id']));?>" class="we7-margin-right-sm">添加文章</a>
					<a href="<?php  echo url('site/category/post', array('id' => $row['id'], 'parentid' => $row['parentid']));?>" class="we7-margin-right-sm">编辑</a>
					<a href="<?php  echo url('site/category/delete', array('id' => $row['id']));?>" class="del" onclick="return confirm('确认删除此分类吗？');return false;">删除</a>
				</div>
			</td>
		</tr>
		<?php  } } ?>
	<?php  } } ?>
</table>
<div class="clearfix"></div>
<div class="we7-margin-left">
	<input type="checkbox" we7-check-all="1" name="rid[]" id="select_all" value="1">
	<label for="select_all">&nbsp;</label>
	<input type="submit" class="btn btn-danger" name="submit" value="删除" onclick="if(!confirm('确定删除选中的规则吗？')) return false;"/>
	<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />	
</div>
</form>
<script>
$('#select_all').click(function(){
	$('.article-list :checkbox').prop('checked', $(this).prop('checked'));
});
$('.js-clip').each(function(){
	util.clip(this, $(this).attr('data-url'));
});
var changeStatus = function(id) {
	var id = parseInt(id);
	$.post('./index.php?c=site&a=category&do=change_status', {id: id},function(data) {
		if (data.message.errno == 0){
			util.message(data.message.message, data.redirect, 'success');
		} else {
			if (data.message.errno == 1) util.toast('更改失败！', 'error');
			if (data.message.errno == -1) util.toast('分类不存在，请刷新重试！');
		}
	}, 'json');
}
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>