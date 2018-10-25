<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li <?php  if($operation == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('group', array('op' => 'display'))?>">群聊中心</a></li>
	<li <?php  if($operation == 'post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('group', array('op' => 'post'))?>"><?php  if(!$id) { ?>添加群聊<?php  } else { ?>编辑群聊<?php  } ?></a></li>
</ul>
<?php  if($operation == 'post') { ?>
<div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="form1">
		<div class="panel panel-default">
			<div class="panel-heading">
				<?php  if(!$id) { ?>添加群聊<?php  } else { ?>编辑群聊<?php  } ?>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>群聊名称</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="groupname" class="form-control" value="<?php  echo $group['groupname'];?>" />
					</div>
				</div>

				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>群聊头像</label>
					<div class="col-sm-9 col-xs-12">
						<?php  echo tpl_form_field_image('thumb', $group['thumb'])?>
						<span class="help-block" style="color:#900;">为了保证美观，请上传180*180尺寸的图片</span>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">只允许管理员发言</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline">
							<input name="jinyan" value="1" <?php  if($group['jinyan'] == 1) { ?>checked="checked"<?php  } ?> type="radio"> 是
						</label>
						<label class="radio-inline">
							<input name="jinyan" value="0" <?php  if($group['jinyan'] == 0) { ?>checked="checked"<?php  } ?> type="radio"> 否
						</label>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">关注才能申请进群</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline">
							<input name="isguanzhu" value="1" <?php  if($group['isguanzhu'] == 1) { ?>checked="checked"<?php  } ?> type="radio"> 开启
						</label>
						<label class="radio-inline">
							<input name="isguanzhu" value="0" <?php  if($group['isguanzhu'] == 0) { ?>checked="checked"<?php  } ?> type="radio"> 关闭
						</label>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否审核才能进群</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline">
							<input name="isshenhe" value="1" <?php  if($group['isshenhe'] == 1) { ?>checked="checked"<?php  } ?> type="radio"> 否
						</label>
						<label class="radio-inline">
							<input name="isshenhe" value="0" <?php  if($group['isshenhe'] == 0) { ?>checked="checked"<?php  } ?> type="radio"> 是
						</label>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">群通知默认类型</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline">
							<input name="autotx" value="1" <?php  if($group['autotx'] == 1) { ?>checked="checked"<?php  } ?> type="radio"> 开启
						</label>
						<label class="radio-inline">
							<input name="autotx" value="0" <?php  if($group['autotx'] == 0) { ?>checked="checked"<?php  } ?> type="radio"> 关闭
						</label>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">群人员最多人数</label>
					<div class="col-sm-2 col-xs-12">		
						<div class="input-group">
							<input class="form-control" name="maxnum" value="<?php  echo $group['maxnum'];?>" type="text">
							<span class="input-group-addon">人</span>
						</div>
						<span class="help-block" style="color:#900;">填0表示不限制</span>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">群聊欢迎语</label>
					<div class="col-sm-7 col-xs-12">
						<textarea class="form-control" name="autoreply"><?php  echo $group['autoreply'];?></textarea>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">快捷消息</label>
					<div class="col-sm-7 col-xs-12">
						<textarea class="form-control" name="quickcon"><?php  echo $group['quickcon'];?></textarea>
						<span class="help-block" style="color:#900;">多个可用|隔开</span>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">选择管理员</label>
					<div class="col-sm-9 col-xs-12">
						<select name="admin" class="form-control">
							<option value="0">--请选择管理员--</option>
							<?php  if(is_array($cservicelist)) { foreach($cservicelist as $crow) { ?>
							<option value="<?php  echo $crow['content'];?>" <?php  if($group['admin'] == $crow['content']) { ?>selected="selected"<?php  } ?>><?php  echo $crow['name'];?></option>
							<?php  } } ?>
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</form>
</div>
<?php  } else if($operation == 'display') { ?>
<div class="main">
	<div class="panel panel-default">
		<div class="panel-body table-responsive">
			<form action="<?php  echo $this->createWebUrl('group')?>" method="post">
			<table class="table table-hover">
				<thead>
					<tr>
						<th style="width:5%;">选择</th>
						<th style="width:45%;">群聊链接</th>
						<th style="width:10%;">群聊名称</th>
						<th style="width:10%;">群聊头像</th>
						<th style="text-align:right;">操作</th>
					</tr>
				</thead>
				<tbody>
				<?php  if(is_array($grouplist)) { foreach($grouplist as $row) { ?>
				<tr>
					<td><input class="form-control" name="groupid[<?php  echo $row['id'];?>]" value="<?php  echo $row['id'];?>" type="checkbox"></td>
					<td><input class="form-control" name="serviceurl" value="<?php  echo $row['url'];?>" type="text"></td>
					<td><?php  echo $row['groupname'];?></td>
					<td><img src="<?php  echo tomedia($row['thumb']);?>" width="50" height="50" /></td>
					<td style="text-align:right;">
						<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#renModal<?php  echo $row['id'];?>">群成员</button>&nbsp;&nbsp;
						<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#formModal<?php  echo $row['id'];?>">群聊记录</button>&nbsp;&nbsp;
						<a href="<?php  echo $this->createWebUrl('group', array('op' => 'post', 'id' => $row['id']))?>" class="btn btn-default btn-sm" title="编辑"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;
						<a href="<?php  echo $this->createWebUrl('group', array('op' => 'delete', 'id' => $row['id']))?>" onclick="return confirm('删除群聊同时将删除该群聊的全部聊天记录，确认吗？');return false;" class="btn btn-default btn-sm" title="删除"><i class="fa fa-times"></i></a>
					</td>
				</tr>
				<?php  } } ?>
				<tr>
					<td colspan="5">
						<input type="hidden" name="op" value="deleteall" />
						<input name="submit" class="btn btn-danger" value="批量删除聊天记录" type="submit">
						<input name="token" value="<?php  echo $_W['token'];?>" type="hidden">
					</td>
				</tr>
				</tbody>
			</table>
			</form>
		</div>
	</div>
</div>
	<?php  if(is_array($grouplist)) { foreach($grouplist as $row) { ?>
	<div class="modal fade" id="renModal<?php  echo $row['id'];?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title"><?php  echo $row['groupname'];?>群成员</h4>
				</div>
				<div class="modal-body table-responsive">					
					<table class="table table-hover table-striped table-condensed">
						<thead>
							<tr>
								<th style="width:10%;">头像</th>
								<th style="width:15%;">昵称</th>
								<th style="width:25%;">openid<br />是否退出</th>
								<th style="width:15%;">加入时间</th>
								<th style="width:10%;">消息提醒</th>
								<th style="text-align:right;">操作</th>
							</tr>
						</thead>
						<tbody>						
							<?php  if(is_array($row['member'])) { foreach($row['member'] as $rowrow) { ?>
								<tr class="trre<?php  echo $rowrow['id'];?>">
									<td><img src="<?php  echo $rowrow['avatar'];?>" width="50" height="50" /></td>
									<td>
										<input type="text" data-id="<?php  echo $rowrow['id'];?>" class="form-control changenick" value="<?php  echo $rowrow['nickname'];?>" />
									</td>
									<td>
										<span class="label label-success"><?php  echo $rowrow['openid'];?></span>
										<?php  if($rowrow['isdel'] == 1) { ?><br /><span class="label label-danger">已退出</span><?php  } ?>
									</td>
									<td>
										<span class="label label-info"><?php  echo date("Y-m-d H:i:s",$rowrow['intime'])?></span>
									</td>
									<td>
										<input type="checkbox" class="kaiguan" data-id="<?php  echo $rowrow['id'];?>" <?php  if($rowrow['txkaiguan'] == 1) { ?>checked="checked"<?php  } ?> name="txkaiguan" value="<?php  echo $rowrow['txkaiguan'];?>"> 开启
									</td>
									<td style="text-align:right;">
										<?php  if($row['admin'] == $rowrow['openid']) { ?>
										<button class="btn btn-danger btn-sm" type="button">管理员不能删除</button>
										<?php  } else { ?>
										<a data-id="<?php  echo $rowrow['id'];?>" class="btn btn-default btn-sm delmember" title="删除"><i class="fa fa-times"></i></a>
										<?php  } ?>
									</td>
								</tr>
							<?php  } } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="formModal<?php  echo $row['id'];?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title"><?php  echo $row['groupname'];?>群聊记录</h4>
				</div>
				<div class="modal-body table-responsive" style="height:400px;overflow:scroll;">					
					<table class="table table-hover table-striped table-condensed">
						<thead>
							<tr>
								<th style="width:15%;">昵称</th>
								<th>聊天内容</th>
								<th style="width:15%;">时间</th>
								<th style="width:10%;text-align:right;">操作</th>
							</tr>
						</thead>
						<tbody>
							<?php  if(is_array($row['chatlist'])) { foreach($row['chatlist'] as $rowchat) { ?>
								<tr class="trre<?php  echo $rowchat['id'];?>">
									<td>
										<span class="label label-success"><?php  echo $rowchat['nickname'];?></span>
									</td>
									<td style="overflow:auto;text-overflow:initial;white-space:inherit;">
										<?php  if($rowchat['type'] == 3) { ?>
										<img src="<?php  echo $rowchat['content'];?>" onclick="showimage('<?php  echo $rowchat['content'];?>');" style="max-width:100px;cursor:pointer;" />
										<?php  } else if($rowchat['type'] == 5) { ?>
										<span class="label label-success">[语音消息]</span>
										<?php  } else { ?>
											<?php  echo $rowchat['content'];?>
										<?php  } ?>
									</td>
									<td>
										<span class="label label-info"><?php  echo date("Y-m-d H:i:s",$rowchat['time'])?></span>
									</td>
									<td style="text-align:right;">
										<a data-id="<?php  echo $rowchat['id'];?>" class="btn btn-default btn-sm deldu" title="删除"><i class="fa fa-times"></i></a>
									</td>
								</tr>
							<?php  } } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php  } } ?>
<?php  } ?>
<div id="ShowImage_Form" tabindex="-2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">     <div class="modal-dialog">
	<div class="modal-content">    
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			<h4 class="modal-title">图片信息</h4>
		</div>
		<div class="modal-body">
			<div id="img_show" style="text-align:center;"></div>
		</div>
	</div>
	</div>
</div>
<script type="text/javascript">
$(function(){
	$(".kaiguan").change(function(){
		var memberid = $(this).attr('data-id');
		$.ajax({
			url:"<?php  echo $this->createWebUrl('group')?>",
			data:{
				id:memberid,
				op:'changekaiguan',
			},
			dataType:'json',
			type:'post',        
			success:function(data){
				alert(data.msg);
			},
		});
	});
	$('.changenick').change(function(){
		if(confirm("确定要修改昵称吗？")){
			var groupmemberid = $(this).attr('data-id');
			$.ajax({
				url:"<?php  echo $this->createWebUrl('group')?>",
				data:{
					id:groupmemberid,
					op:'changenickname',
					nickname:$(this).val(),
				},
				dataType:'json',
				type:'post',        
				success:function(data){
					alert(data.msg);
				},
			});
		}
	});
	$('.deldu').click(function(){
		if(confirm("确定要删除这条聊天记录吗？")){
			var chatid = $(this).attr('data-id');
			$.ajax({
				url:"<?php  echo $this->createWebUrl('group')?>",
				data:{
					id:chatid,
					op:'deletedu',
				},
				dataType:'json',
				type:'post',        
				success:function(data){
					if(data.error == 1){
						alert(data.msg);
					}else{
						$('.trre'+chatid).remove();
					}
				},
			});
		}
	});
	
	$('.delmember').click(function(){
		if(confirm("删除群成员，确认吗？")){
			var chatid = $(this).attr('data-id');
			$.ajax({
				url:"<?php  echo $this->createWebUrl('group')?>",
				data:{
					id:chatid,
					op:'delmember',
				},
				dataType:'json',
				type:'post',        
				success:function(data){
					if(data.error == 1){
						alert(data.msg);
					}else{
						$('.trre'+chatid).remove();
					}
				},
			});
		}
	});
})
function showimage(source)
 {
	 $("#ShowImage_Form").find("#img_show").html("<image src='"+source+"' class='carousel-inner img-responsive img-rounded' style='max-width:50%;margin:0 auto;' />");
	 $("#ShowImage_Form").modal();
 }
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>