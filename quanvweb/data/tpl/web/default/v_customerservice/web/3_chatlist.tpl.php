<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li <?php  if($operation == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('chatlist', array('op' => 'display'))?>">聊天管理</a></li>
</ul>
<?php  if($operation == 'display') { ?>
<div class="main">
	<div class="panel panel-info">
		<div class="panel-heading">筛选</div>
		<div class="panel-body">
			<form action="./index.php" method="get" class="form-horizontal" role="form" id="form1">
				<input type="hidden" name="c" value="site" />
				<input type="hidden" name="a" value="entry" />
				<input type="hidden" name="m" value="v_customerservice" />
				<input type="hidden" name="do" value="chatlist" />
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">选择客服</label>
					<div class="col-xs-12 col-sm-8 col-lg-9">
						<select name="kefuopenid" class='form-control'>
							<option value="0">--请选择客服--</option>
							<?php  if(is_array($cservicelist)) { foreach($cservicelist as $grow) { ?>
							<option value="<?php  echo $grow['content'];?>" <?php  if($_GPC['kefuopenid'] == $grow['content']) { ?>selected<?php  } ?>>
								<?php  echo $grow['name'];?> -- (未读<?php  echo $grow['weidu'];?>条)
							</option>
							<?php  } } ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">时间范围</label>
					<div class="col-sm-3 col-xs-12">
						<?php  echo tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d', $starttime),'endtime'=>date('Y-m-d', $endtime)));?>
					</div>
					<div class="col-sm-7 col-xs-12">
						<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
						<button name="export" value="export" class="btn btn-primary"><i class="fa fa-download"></i> 导出聊天记录</button>
						<button type="button" class="btn btn-default">总记录数：<?php  echo $total;?></button>
					</div>
				</div>
			</form>
		</div>
	</div>
	
	<div class="panel panel-default">
		<div class="panel-heading">聊天记录</div>
		<div class="panel-body table-responsive">
			<table class="table table-hover table-striped table-condensed">
				<thead>
					<tr>
						<th style="width:15%;">客户</th>
						<th style="width:55%;">最新消息</th>
						<th style="width:15%;">删除情况</th>
						<th style="text-align:right;">操作</th>
					</tr>
				</thead>
				<tbody>
				<?php  if(is_array($fanslist)) { foreach($fanslist as $row) { ?>
				<tr>
					<td><img src="<?php  echo $row['fansavatar'];?>" width="50" height="50" style="border-radius:50px;" /> <?php  echo $row['fansnickname'];?></td>
					<td>
						<span class="label label-info"><?php  echo date("Y-m-d H:i:s",$row['lasttime'])?></span>
						<span class="label label-success">
							<?php  if($row['msgtype'] == 4) { ?>
								[图片消息]
							<?php  } else if($row['msgtype'] == 5) { ?>
								[语音消息]
							<?php  } else { ?>
								<?php  echo preg_replace('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', '[无法识别字符]', $row['lastcon'])?>
							<?php  } ?>
						</span>
					</td>
					<td>
						<?php  if($row['fansdel'] == 1) { ?><span class="label label-danger">用户删除</span><?php  } ?>
						<?php  if($row['kefudel'] == 1) { ?><span class="label label-danger">客服删除</span><?php  } ?>
					</td>
					<td style="text-align:right;">
						<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#formModal<?php  echo $row['id'];?>">聊天记录</button>&nbsp;&nbsp;
						<a href="<?php  echo $this->createWebUrl('chatlist', array('op' => 'delete', 'id' => $row['id'],'kefuopenid'=>$kefuopenid))?>" onclick="return confirm('确认要删除聊天记录吗？');return false;" class="btn btn-default btn-sm" title="删除"><i class="fa fa-times"></i></a>
					</td>
				</tr>
				<?php  } } ?>
				</tbody>
			</table>
			
			<?php  if(is_array($fanslist)) { foreach($fanslist as $row) { ?>
				<div class="modal fade" id="formModal<?php  echo $row['id'];?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<h4 class="modal-title">聊天记录</h4>
							</div>
							<div class="modal-body table-responsive">					
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
										<?php  if(is_array($row['chat'])) { foreach($row['chat'] as $rowchat) { ?>
											<tr class="trre<?php  echo $rowchat['id'];?>">
												<td>
													<?php  if($rowchat['openid'] == $row['fansopenid']) { ?>
														<span class="label label-success"><?php  echo $row['fansnickname'];?></span>
													<?php  } else { ?>
														<span class="label label-info"><?php  echo $row['kefunickname'];?></span>
													<?php  } ?>
												</td>
												<td style="overflow:auto;text-overflow:initial;white-space:inherit;">
													<?php  if($rowchat['openid'] == $rowrow['fansopenid']) { ?>
														<?php  if($rowchat['type'] == 3 || $rowchat['type'] == 4) { ?>
															<img src="<?php  echo $rowchat['content'];?>" onclick="showimage('<?php  echo $rowchat['content'];?>');" style="max-width:100px;cursor:pointer;" />
														<?php  } else if($rowchat['type'] == 5 || $rowchat['type'] == 6) { ?>
														<span class="label label-success">[语音消息]</span>
														<?php  } else { ?>
															<?php  echo $rowchat['content'];?>
														<?php  } ?>
													<?php  } else { ?>
														<?php  if($rowchat['type'] == 3 || $rowchat['type'] == 4) { ?>
														<img src="<?php  echo $rowchat['content'];?>" onclick="showimage('<?php  echo $rowchat['content'];?>');" style="max-width:100px;cursor:pointer;" />
														<?php  } else if($rowchat['type'] == 5 || $rowchat['type'] == 6) { ?>
														<span class="label label-success">[语音消息]</span>
														<?php  } else { ?>
															<?php  echo $rowchat['content'];?>
														<?php  } ?>
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
		</div>
	</div>
	<?php  echo $pager;?>
</div>
<?php  } ?>
<div id="ShowImage_Form" tabindex="-2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">     <div class="modal-dialog modal-lg">
	<div class="modal-content">    
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			<h4 class="modal-title">图片信息</h4>
		</div>
		<div class="modal-body">
			<div id="img_show"></div>
		</div>
	</div>
	</div>
</div>
<script type="text/javascript">
$(function(){
	$('.deldu').click(function(){
		if(confirm("确定要删除这条聊天记录吗？")){
			var chatid = $(this).attr('data-id');
			$.ajax({
				url:"<?php  echo $this->createWebUrl('chatlist')?>",
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
})
function showimage(source)
 {
	 $("#ShowImage_Form").find("#img_show").html("<image src='"+source+"' class='carousel-inner img-responsive img-rounded' />");
	 $("#ShowImage_Form").modal();
 }
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>