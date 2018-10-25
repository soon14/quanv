<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php  load()->func('tpl')?>
<ul class="nav nav-tabs">
	<li class="active"><a href="<?php  echo $this->createWebUrl('tpllist')?>">模板列表</a></li>
	<li><a href="<?php  echo $this->createWebUrl('sendone')?>">发送消息</a></li>
	<li><a href="<?php  echo $this->createWebUrl('sendonelog')?>">发送记录</a></li>
</ul>
<div class="main">
	<div class="alert alert-danger" role="alert">请先保证模板消息行业选择的是（IT科技 互联网|电子商务），无需<a href="https://mp.weixin.qq.com/" target="_blank" style="color:green;">微信公众号后台</a>手动添加模板消息，点击下方按钮直接创建即可。<br />添加完后请务必点击同步模板列表按钮。</div>
	<div class="panel panel-default">
		<div class="panel-heading">模板列表</div>
		<div class="panel-body">			
			<a href="<?php  echo $this->createWebUrl('createtpl',array('tplbh'=>'OPENTM202119578'))?>" class="btn btn-success"><i class="glyphicon glyphicon-plus"> </i> 创建客服提醒消息模板</a>
			<a href="<?php  echo $this->createWebUrl('createtpl',array('tplbh'=>'OPENTM202109783'))?>" class="btn btn-success"><i class="glyphicon glyphicon-plus"> </i> 创建粉丝提醒消息模板</a>
			<button type="button" class="btn btn-info"><i class="glyphicon glyphicon-refresh"> </i> 同步模板列表</button>
			<br /><br />
			<table class="table table-hover">
				<thead>
					<th width="50">序号</th>
					<th width="200">模板编号</th>
					<th width="200">标题</th>
					<th width="400">模板id</th>
					<th style="text-align:right;">操作</th>
				</thead>
				<tbody>
				<?php  if(is_array($list)) { foreach($list as $row) { ?>
					<tr>
						<td><?php  echo $row['id'];?></td>
						<td><?php  echo $row['tplbh'];?></td>
						<td><?php  echo $row['tpl_title'];?></td>
						<td><?php  echo $row['tpl_id'];?></td>
						<td style="text-align:right;"><a href="<?php  echo $this->createWebUrl('deltpl',array('tplid'=>$row['tpl_id']))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;" class="btn btn-default btn-sm" title="删除"><i class="fa fa-times"></i></a></td>
						<td style="text-align:right;"><a href="<?php  echo $this->createWebUrl('deltpl2',array('tplid'=>$row['tpl_id']))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;" class="btn btn-default btn-sm" title="删除"><i class="fa fa-times"></i>只删数据</a></td>
					</tr>
				<?php  } } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function(){
		$(".btn-info").click(function(){
			window.location.href="<?php  echo $this->createWebUrl('UpdateTpl')?>";
		});
	});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>