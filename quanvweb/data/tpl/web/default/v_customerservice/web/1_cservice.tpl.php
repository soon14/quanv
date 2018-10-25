<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li <?php  if($operation == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('cservice', array('op' => 'display'))?>">客服中心</a></li>
	<li <?php  if($operation == 'post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('cservice', array('op' => 'post'))?>"><?php  if(!$id) { ?>添加客服<?php  } else { ?>编辑客服<?php  } ?></a></li>
	<li><a href="<?php  echo $this->createWebUrl('cservicegroup', array('op' => 'display'))?>">客服组</a></li>
</ul>
<?php  if($operation == 'post') { ?>
<div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="form1">
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#basic" role="tab" data-toggle="tab">基本设置</a></li>
			<li role="presentation"><a href="#qrcode" role="tab" data-toggle="tab">客服二维码设置</a></li>
			<li role="presentation"><a href="#xiaoxi" role="tab" data-toggle="tab">消息设置</a></li>
			<li role="presentation"><a href="#zaixian" role="tab" data-toggle="tab">在线时间设置</a></li>
		</ul>
		
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active panel panel-default" id="basic">
				<div class="panel-heading">
					<?php  if(!$id) { ?>添加客服<?php  } else { ?>编辑客服<?php  } ?>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>客服名称</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="name" class="form-control" value="<?php  echo $cservice['name'];?>" />
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">类别名称</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="typename" class="form-control" value="<?php  echo $cservice['typename'];?>" />
							<span class="help-block" style="color:#900;">原（微信咨询、QQ咨询、手机咨询、座机咨询）文字</span>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>客服头像</label>
						<div class="col-sm-9 col-xs-12">
							<?php  echo tpl_form_field_image('thumb', $cservice['thumb'])?>
							<span class="help-block" style="color:#900;">为了保证美观，请上传180*180尺寸的图片</span>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">所属客服组</label>
						<div class="col-sm-9 col-xs-12">
							<?php  if(is_array($cservicegrouplist)) { foreach($cservicegrouplist as $crow) { ?>
								<label class='checkbox-inline'>
									<input type='checkbox' name='groupid[]' value='<?php  echo $crow['id'];?>' <?php  if(in_array($crow['id'],$groupids)) { ?>checked<?php  } ?> /> <?php  echo $crow['name'];?>
								</label>
							<?php  } } ?>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">客服类型</label>
						<div class="col-sm-9 col-xs-12">
							<label class='radio-inline'>
								<input type='radio' name='ctype' value='1' <?php  if($cservice['ctype']==1) { ?>checked<?php  } ?> onclick="$('#other').show();" /> 微信客服
							</label>
							<label class='radio-inline'>
								<input type='radio' name='ctype' value='2' <?php  if($cservice['ctype']==2) { ?>checked<?php  } ?> onclick="$('#other').hide();" /> QQ客服
							</label>
							<label class='radio-inline'>
								<input type='radio' name='ctype' value='3' <?php  if($cservice['ctype']==3) { ?>checked<?php  } ?> onclick="$('#other').hide();" /> 移动客服
							</label>
							<label class='radio-inline'>
								<input type='radio' name='ctype' value='4' <?php  if($cservice['ctype']==4) { ?>checked<?php  } ?> onclick="$('#other').hide();" /> 座机客服
							</label>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>客服内容</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="content" class="form-control" value="<?php  echo $cservice['content'];?>" /><br />
							<div class="alert alert-danger" role="alert">微信客服内容填openid，<a href="index.php?c=mc&a=fans" target="_blank" style="color:green;">快速获取openid</a>，QQ客服填qq号码，移动客服填手机号，坐班客服填座机号</div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否前端管理员</label>
						<div class="col-sm-9 col-xs-12">
							<label for="isgly1" class="radio-inline"><input name="isgly" value="1" id="ishow1" <?php  if($cservice['isgly'] == 1) { ?>checked="true"<?php  } ?> type="radio"> 是</label>
							&nbsp;&nbsp;&nbsp;
							<label for="isgly2" class="radio-inline"><input name="isgly" value="0" id="ishow2" <?php  if($cservice['isgly'] == 0) { ?>checked="true"<?php  } ?> type="radio"> 否</label>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="displayorder" class="form-control" value="<?php  echo $cservice['displayorder'];?>" />
						</div>
					</div>
				</div>
			</div>
			
			<div role="tabpanel" class="tab-pane panel panel-default" id="qrcode">
				<div class="panel-heading">
					<?php  if(!$id) { ?>添加客服<?php  } else { ?>编辑客服<?php  } ?>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">展示客服微信二维码</label>
						<div class="col-sm-9 col-xs-12">
							<label class='radio-inline'>
								<input type='radio' name='iskefuqrcode' value='0' <?php  if($cservice['iskefuqrcode']==0) { ?>checked<?php  } ?> onclick="$('#kefuqrcode').hide();" /> 不展示
							</label>
							<label class='radio-inline'>
								<input type='radio' name='iskefuqrcode' value='1' <?php  if($cservice['iskefuqrcode']==1) { ?>checked<?php  } ?> onclick="$('#kefuqrcode').show();" /> 展示
							</label>
						</div>
					</div>
					
					<div class="form-group" id="kefuqrcode" <?php  if($cservice['iskefuqrcode']!=1) { ?>style="display:none;"<?php  } ?>>
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">客服微信二维码</label>
						<div class="col-sm-9 col-xs-12">
							<?php  echo tpl_form_field_image('kefuqrcode', $cservice['kefuqrcode'])?>
						</div>
					</div>
				</div>
			</div>
			
			<div role="tabpanel" class="tab-pane panel panel-default" id="xiaoxi">
				<div class="panel-heading">
					<?php  if(!$id) { ?>添加客服<?php  } else { ?>编辑客服<?php  } ?>
				</div>
				<div class="panel-body">
					<div id="other" <?php  if($cservice['ctype']!=1) { ?>style="display:none;"checked<?php  } ?>>						
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">客服欢迎语</label>
							<div class="col-sm-7 col-xs-12">
								<textarea class="form-control" style="height:100px;" name="autoreply"><?php  echo $cservice['autoreply'];?></textarea>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">粉丝快捷消息</label>
							<div class="col-sm-7 col-xs-12">
								<textarea class="form-control" name="fansauto"><?php  echo $cservice['fansauto'];?></textarea>
								<span class="help-block" style="color:#900;">多个可用|隔开</span>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">客服快捷消息</label>
							<div class="col-sm-7 col-xs-12">
								<textarea class="form-control" name="kefuauto"><?php  echo $cservice['kefuauto'];?></textarea>
								<span class="help-block" style="color:#900;">多个可用|隔开</span>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">快捷消息提交设置</label>
							<div class="col-sm-9 col-xs-12">
								<label for="isautosub1" class="radio-inline"><input name="isautosub" value="1" id="isautosub1" <?php  if($cservice['isautosub'] == 1) { ?>checked="true"<?php  } ?> type="radio"> 进入输入框</label>
								&nbsp;&nbsp;&nbsp;
								<label for="isautosub2" class="radio-inline"><input name="isautosub" value="0" id="isautosub2" <?php  if($cservice['isautosub'] == 0) { ?>checked="true"<?php  } ?> type="radio"> 自动发送</label>
								<span class="help-block"></span>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div role="tabpanel" class="tab-pane panel panel-default" id="zaixian">
				<div class="panel-heading">
					<?php  if(!$id) { ?>添加客服<?php  } else { ?>编辑客服<?php  } ?>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">客服在线时间是否存在零节点</label>
						<div class="col-sm-9 col-xs-12">
							<label class='radio-inline'>
								<input type='radio' name='lingjie' value='1' <?php  if($cservice['lingjie']==1) { ?>checked<?php  } ?> /> 是
							</label>
							<label class='radio-inline'>
								<input type='radio' name='lingjie' value='0' <?php  if($cservice['lingjie']==0) { ?>checked<?php  } ?> /> 否
							</label>
							<span class="help-block" style="color:red;">零节点是指跨过0点的时间，例如13到3点</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">客服在线时间</label>
						<div class="col-sm-2 col-xs-12">
							<div class="input-group">
								<span class="input-group-addon">从</span>
								<input class="form-control" name="starthour" value="<?php  echo $cservice['starthour'];?>" type="text">
								<span class="input-group-addon">点</span>
							</div>
						</div>
						<div class="col-sm-2 col-xs-12">
							<div class="input-group">
								<span class="input-group-addon">到</span>
								<input class="form-control" name="endhour" value="<?php  echo $cservice['endhour'];?>" type="text">
								<span class="input-group-addon">点</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">不在线提示语</label>
						<div class="col-sm-7 col-xs-12">
							<textarea class="form-control" name="notonline"><?php  echo $cservice['notonline'];?></textarea>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">启用手动在线下线</label>
						<div class="col-sm-9 col-xs-12">
							<label for="iszx1" class="radio-inline"><input name="iszx" value="1" id="iszx1" <?php  if($cservice['iszx'] == 1) { ?>checked="true"<?php  } ?> type="radio"> 启用</label>
							&nbsp;&nbsp;&nbsp;
							<label for="iszx2" class="radio-inline"><input name="iszx" value="0" id="iszx2" <?php  if($cservice['iszx'] == 0) { ?>checked="true"<?php  } ?> type="radio"> 关闭</label>
							<span class="help-block" style="color:red;">启用手动在线下线后，设置的客服的在线时间将会失效，请谨慎操作！</span>
						</div>
					</div>
					
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">前端页面显示</label>
						<div class="col-sm-9 col-xs-12">
							<label for="ishow1" class="radio-inline"><input name="ishow" value="1" id="ishow1" <?php  if($cservice['ishow'] == 1) { ?>checked="true"<?php  } ?> type="radio"> 不显示</label>
							&nbsp;&nbsp;&nbsp;
							<label for="ishow2" class="radio-inline"><input name="ishow" value="0" id="ishow2" <?php  if($cservice['ishow'] == 0) { ?>checked="true"<?php  } ?> type="radio"> 显示</label>
							&nbsp;&nbsp;&nbsp;
							<label for="ishow3" class="radio-inline"><input name="ishow" value="2" id="ishow3" <?php  if($cservice['ishow'] == 2) { ?>checked="true"<?php  } ?> type="radio"> 手动在线才显示</label>
							<span class="help-block" style="color:red;">不显示与显示只有在用时间段控制客服在线时间时生效！手动在线才显示只有当客服手动上线时才生效！</span>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group col-sm-12">
				<input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	</form>
</div>

<?php  } else if($operation == 'display') { ?>
<div class="main">
	<div class="panel panel-info">
		<div class="panel-heading">筛选</div>
		<div class="panel-body">
			<form action="./index.php" method="get" class="form-horizontal" role="form" id="form1">
				<input name="c" value="site" type="hidden">
				<input name="a" value="entry" type="hidden">
				<input name="m" value="v_customerservice" type="hidden">
				<input name="do" value="cservice" type="hidden">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-3 col-lg-2 control-label">选择客服组</label>
					<div class="col-xs-12 col-sm-8 col-lg-9">
						<select name="groupid" class="form-control">
							<option value="0">--请选择客服组--</option>
							<?php  if(is_array($grouplist)) { foreach($grouplist as $row) { ?>
							<option value="<?php  echo $row['id'];?>" <?php  if($_GPC['groupid'] == $row['id']) { ?>selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
							<?php  } } ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-3 col-lg-2 control-label"></label>
					<div class="col-xs-12 col-sm-8 col-lg-9">
						<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
						<button type="button" class="btn btn-default">总记录数：0</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<form action="" method="post">
	<div class="panel panel-default">
		<div class="panel-body table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th style="width:5%;">排序</th>
						<th style="width:40%;">客服链接</th>
						<th style="width:20%;">客服名称</th>
						<th style="width:10%;">客服头像</th>
						<th style="text-align:right;">操作</th>
					</tr>
				</thead>
				<tbody>
				<?php  if(is_array($cservicelist)) { foreach($cservicelist as $row) { ?>
				<tr>
					<td><input class="form-control" name="displayorder[<?php  echo $row['id'];?>]" value="<?php  echo $row['displayorder'];?>" type="text" /></td>
					<td><input class="form-control" name="serviceurl" value="<?php  echo $row['serviceurl'];?>" type="text" /></td>
					<td><?php  echo $row['name'];?> <?php  if($row['ishow'] == 0) { ?><span style="color:red;">(前端显示)</span><?php  } ?><?php  if($row['ishow'] == 2 && $row['iszx'] == 1) { ?><span style="color:red;">(手动在线显示)</span><?php  } ?></td>
					<td><img src="<?php  echo tomedia($row['thumb']);?>" width="50" height="50" /></td>
					<td style="text-align:right;">
						<?php  if($row['ctype'] == 1) { ?>
						<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#pingjia<?php  echo $row['id'];?>">评价信息</button>&nbsp;&nbsp;
						<?php  } ?>
						<a href="<?php  echo $this->createWebUrl('cservice', array('op' => 'post', 'id' => $row['id']))?>" class="btn btn-default btn-sm" title="编辑"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;
						<a href="<?php  echo $this->createWebUrl('cservice', array('op' => 'delete', 'id' => $row['id']))?>" onclick="return confirm('删除客服同时将删除微信客服的全部聊天记录，确认吗？');return false;" class="btn btn-default btn-sm" title="删除"><i class="fa fa-times"></i></a>
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

	<?php  if(is_array($cservicelist)) { foreach($cservicelist as $row) { ?>
	<?php  if($row['ctype'] == 1) { ?>
	<div class="modal fade" id="pingjia<?php  echo $row['id'];?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title"><?php  echo $row['name'];?>的评价信息</h4>
				</div>
				<div class="modal-body table-responsive">		
				<table class="table table-hover">
					<thead>
						<tr>
							<th style="width:10%;">类型</th>
							<th style="width:45%;">评价内容</th>
							<th style="width:45%;text-align:right;">评价时间</th>
						</tr>
					</thead>
					<tbody>
						<?php  if(is_array($row['pingjia'])) { foreach($row['pingjia'] as $rowrow) { ?>
						<tr>
							<td>
								<?php  if($rowrow['pingtype'] == 1) { ?><span class="label label-success">好评</span><?php  } ?>
								<?php  if($rowrow['pingtype'] == 2) { ?><span class="label label-info">中评</span><?php  } ?>
								<?php  if($rowrow['pingtype'] == 3) { ?><span class="label label-danger">差评</span><?php  } ?>
							</td>
							<td><?php  echo $rowrow['content'];?></td>
							<td style="text-align:right;"><?php  echo date("Y-m-d H:i:s",$rowrow['time'])?></td>
						</tr>
						<?php  } } ?>
					</tbody>
				</table>
				</div>
			</div>
		</div>
	</div>
	<?php  } ?>
	<?php  } } ?>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>