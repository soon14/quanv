<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li <?php  if($operation == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('cservicegroup', array('op' => 'display'))?>">客服组</a></li>
	<li <?php  if($operation == 'post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('cservicegroup', array('op' => 'post'))?>"><?php  if(!$id) { ?>添加客服组<?php  } else { ?>编辑客服组<?php  } ?></a></li>
</ul>
<?php  if($operation == 'post') { ?>
<div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="form1">
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#basic" role="tab" data-toggle="tab">基本设置</a></li>
			<li role="presentation"><a href="#qianru" role="tab" data-toggle="tab">嵌入设置</a></li>
			<li role="presentation"><a href="#dsf" role="tab" data-toggle="tab">接入第三方</a></li>
		</ul>
		
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active panel panel-default" id="basic">
				<div class="panel-heading">
					<?php  if(!$id) { ?>添加客服组<?php  } else { ?>编辑客服组<?php  } ?>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>客服组名称</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="name" class="form-control" value="<?php  echo $cservicegroup['name'];?>" />
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">类别名称</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="typename" class="form-control" value="<?php  echo $cservicegroup['typename'];?>" />
							<span class="help-block" style="color:#900;">原（客服组）文字</span>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>客服组头像</label>
						<div class="col-sm-9 col-xs-12">
							<?php  echo tpl_form_field_image('thumb', $cservicegroup['thumb'])?>
							<span class="help-block" style="color:#900;">为了保证美观，请上传180*180尺寸的图片</span>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否前端显示</label>
						<div class="col-sm-9 col-xs-12">
							<label for="ishow1" class="radio-inline"><input name="ishow" value="1" id="ishow1" <?php  if($cservicegroup['ishow'] == 1) { ?>checked="checked"<?php  } ?> type="radio"> 是</label>
							&nbsp;&nbsp;&nbsp;
							<label for="ishow2" class="radio-inline"><input name="ishow" value="0" id="ishow2" <?php  if($cservicegroup['ishow'] == 0) { ?>checked="checked"<?php  } ?> type="radio"> 否</label>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="displayorder" class="form-control" value="<?php  echo $cservicegroup['displayorder'];?>" />
						</div>
					</div>
				</div>
			</div>
			
			<div role="tabpanel" class="tab-pane panel panel-default" id="qianru">
				<div class="panel-heading">
					<?php  if(!$id) { ?>添加客服组<?php  } else { ?>编辑客服组<?php  } ?>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">嵌入代码文字</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="qrtext" class="form-control" value="<?php  echo $cservicegroup['qrtext'];?>" />
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">嵌入代码文字颜色</label>
						<div class="col-sm-9 col-xs-12">
							<?php  echo tpl_form_field_color('qrcolor',$cservicegroup['qrcolor']);?>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">嵌入代码背景颜色</label>
						<div class="col-sm-9 col-xs-12">
							<?php  echo tpl_form_field_color('qrbg',$cservicegroup['qrbg']);?>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">距离页面右边</label>
						<div class="col-sm-9 col-xs-12">
							<div class="input-group">
								<input class="form-control" name="qrright" value="<?php  echo $cservicegroup['qrright'];?>" type="text">
								<span class="input-group-addon">%</span>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">距离页面下边</label>
						<div class="col-sm-9 col-xs-12">
							<div class="input-group">
								<input class="form-control" name="qrbottom" value="<?php  echo $cservicegroup['qrbottom'];?>" type="text">
								<span class="input-group-addon">%</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div role="tabpanel" class="tab-pane panel panel-default" id="dsf">
				<div class="panel-heading">
					<?php  if(!$id) { ?>添加客服组<?php  } else { ?>编辑客服组<?php  } ?>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">第三方标识</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="sanbs" class="form-control" value="<?php  echo $cservicegroup['sanbs'];?>" />
							<span class="help-block" style="color:#900;">除固定格式（人人：renren 啦啦外卖：lala）外，均可自定义英文字母（建议3至20个字符之间）</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">接入说明</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="sanremark" class="form-control" value="<?php  echo $cservicegroup['sanremark'];?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">聚合id</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="bsid" class="form-control" value="<?php  echo $cservicegroup['bsid'];?>" />
							<span class="help-block" style="color:#900;">例如某商品属于某个商家，则聚合id填商家id，否则可填0</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="form-group col-sm-12">
			<input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
			<input type="hidden" name="id" value="<?php  echo $cservicegroup['id'];?>" />
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</form>
</div>

<?php  } else if($operation == 'display') { ?>
<div class="main">
	<form action="" method="post">
	<div class="panel panel-default">
		<div class="panel-body table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th style="width:5%;">排序</th>
						<th style="width:45%;">客服组链接</th>
						<th style="width:20%;">客服组名称<br />接入第三方</th>
						<th style="width:10%;">客服组头像</th>
						<th style="text-align:right;">操作</th>
					</tr>
				</thead>
				<tbody>
				<?php  if(is_array($cservicegrouplist)) { foreach($cservicegrouplist as $row) { ?>
				<tr>
					<td><input class="form-control" name="displayorder[<?php  echo $row['id'];?>]" value="<?php  echo $row['displayorder'];?>" type="text" /></td>
					<td><input class="form-control" name="servicegroupurl" value="<?php  echo $row['servicegroupurl'];?>" type="text" /></td>
					<td><?php  echo $row['name'];?> <?php  if($row['ishow'] == 1) { ?><span style="color:red;">(前端显示)</span><?php  } ?><br /><?php  if($row['sanremark'] != '') { ?><?php  echo $row['sanremark'];?><?php  } else { ?>无<?php  } ?></td>
					<td><img src="<?php  echo tomedia($row['thumb']);?>" width="50" height="50" /></td>
					<td style="text-align:right;">
						<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#formModal<?php  echo $row['id'];?>">嵌入代码</button>&nbsp;&nbsp;
						<a href="<?php  echo $this->createWebUrl('cservicegroup', array('op' => 'post', 'id' => $row['id']))?>" class="btn btn-default btn-sm" title="编辑"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;
						<a href="<?php  echo $this->createWebUrl('cservicegroup', array('op' => 'delete', 'id' => $row['id']))?>" onclick="return confirm('确认要删除吗？');return false;" class="btn btn-default btn-sm" title="删除"><i class="fa fa-times"></i></a>
					</td>
				</tr>
				<?php  } } ?>
				<tr>
					<td></td>
					<td colspan="4">
						<input name="submit" class="btn btn-primary" value="提交" type="submit">
						<input name="token" value="<?php  echo $_W['token'];?>" type="hidden">
					</td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
	</form>
</div>

<?php  if(is_array($cservicegrouplist)) { foreach($cservicegrouplist as $row) { ?>
<div class="modal fade" id="formModal<?php  echo $row['id'];?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">拷贝下面代码复制到对应的模板文件即可</h4>
			</div>
			<div class="modal-body table-responsive">					
				<div class="modal-body table-responsive">
					<div class="panel panel-default">
						<div class="panel-heading">js嵌入模式</div>
						<div class="panel-body">
							<div class="alert alert-default" role="alert">
								<?php  echo $row['scripthtml'];?>
							</div>
						</div>
					</div>
					
					<div class="panel panel-default">
						<div class="panel-heading">链接模式</div>
						<div class="panel-body">
							<div class="alert alert-default" role="alert">
								<?php  echo $row['texturl'];?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php  } } ?>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>