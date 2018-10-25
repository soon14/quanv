<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li <?php  if($operation == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('kehu', array('op' => 'display'))?>">客户管理</a></li>
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
				<input type="hidden" name="do" value="kehu" />
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">搜索客户</label>
					<div class="col-xs-12 col-sm-8 col-lg-9">
						<input type="text" name="keyword" class='form-control' placeholder='可根据客户昵称、标签、姓名、电话进行模糊搜索' value="<?php  echo $keyword;?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label"></label>
					<div class="col-sm-7 col-xs-12">
						<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
						<button type="button" class="btn btn-default">总记录数：<?php  echo $total;?></button>
					</div>
				</div>
			</form>
		</div>
	</div>
	
	<div class="panel panel-default">
		<div class="panel-heading">客户列表</div>
		<div class="panel-body table-responsive">
			<table class="table table-hover table-striped table-condensed">
				<thead>
					<tr>
						<th style="width:18%;">客户</th>
						<th style="width:18%;">客服</th>
						<th style="width:15%;">标签<br />姓名<br />电话</th>
						<th style="width:22%;">Openid<br />黑名单</th>
						<th style="text-align:right;">操作</th>
					</tr>
				</thead>
				<tbody>
				<?php  if(is_array($list)) { foreach($list as $row) { ?>
				<tr>
					<td><img src="<?php  echo $row['fansavatar'];?>" width="50" height="50" style="border-radius:50px;" /> <?php  echo $row['fansnickname'];?></td>
					<td><img src="<?php  echo $row['kefuavatar'];?>" width="50" height="50" style="border-radius:50px;" /> <?php  echo $row['kefunickname'];?></td>
					<td>
						<span class="label label-info showbiaoqian<?php  echo $row['id'];?>">标签:<?php  echo $row['name'];?></span><br />
						<span class="label label-info showrealname<?php  echo $row['id'];?>">姓名:<?php  echo $row['realname'];?></span><br />
						<span class="label label-info showtelphone<?php  echo $row['id'];?>">电话:<?php  echo $row['telphone'];?></span>
					</td>
					<td>
						<?php  echo $row['fansopenid'];?>
						<?php  if($row['ishei'] == 1) { ?>
						<br />
						<span class="label label-danger">黑名单</span>
						<?php  } ?>
					</td>
					<td style="text-align:right;">
						<?php  if($row['ishei'] == 0) { ?>
						<a href="<?php  echo $this->createWebUrl('kehu',array('op'=>'lahei','id'=>$row['id']))?>" class="btn btn-danger btn-sm">加入黑名单</a>&nbsp;&nbsp;
						<?php  } else { ?>
						<a href="<?php  echo $this->createWebUrl('kehu',array('op'=>'yichu','id'=>$row['id']))?>" class="btn btn-success btn-sm">移除黑名单</a>&nbsp;&nbsp;
						<?php  } ?>
						<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#biaoqian<?php  echo $row['id'];?>">修改标签</button>&nbsp;&nbsp;
						<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#formModal<?php  echo $row['id'];?>">聊天记录</button>&nbsp;&nbsp;
					</td>
				</tr>
				<?php  } } ?>
				</tbody>
			</table>
			
			
			<?php  if(is_array($list)) { foreach($list as $row) { ?>
				<div class="modal fade" id="biaoqian<?php  echo $row['id'];?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<h4 class="modal-title">修改标签</h4>
							</div>
							
							<div class="modal-body table-responsive">
								<div class="form-horizontal">
								<div class="form-group">
									<label class="col-xs-12 col-sm-3 col-md-3 col-lg-2 control-label">标签</label>
									<div class="col-xs-12 col-sm-9 col-lg-9">
										<input name="biaoqian" class="form-control biaoqian<?php  echo $row['id'];?>" value="<?php  echo $row['name'];?>" type="text">
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-xs-12 col-sm-3 col-md-3 col-lg-2 control-label">姓名</label>
									<div class="col-xs-12 col-sm-9 col-lg-9">
										<input name="realname" class="form-control realname<?php  echo $row['id'];?>" value="<?php  echo $row['realname'];?>" type="text">
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-xs-12 col-sm-3 col-md-3 col-lg-2 control-label">手机</label>
									<div class="col-xs-12 col-sm-9 col-lg-9">
										<input name="telphone" class="form-control telphone<?php  echo $row['id'];?>" value="<?php  echo $row['telphone'];?>" type="text">
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>
									<div class="col-xs-12 col-sm-8 col-lg-9">
										<button class="btn btn-success biaoqianbtn" data-id="<?php  echo $row['id'];?>" type="button ">确定</button>
									</div>
								</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal fade" id="formModal<?php  echo $row['id'];?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<h4 class="modal-title">客户记录</h4>
							</div>
							<div class="modal-body table-responsive">					
								<table class="table table-hover table-striped table-condensed">
									<thead>
										<tr>
											<th style="width:15%;">昵称</th>
											<th>聊天内容内容</th>
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
		if(confirm("确定要删除这条客户记录吗？")){
			var chatid = $(this).attr('data-id');
			$.ajax({
				url:"<?php  echo $this->createWebUrl('kehu')?>",
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
	
	$('.biaoqianbtn').click(function(){
		var id = $(this).attr('data-id');
		$.ajax({
			url:"<?php  echo $this->createWebUrl('kehu')?>",
			data:{
				id:id,
				op:'changebiaoqian',
				name:$('.biaoqian'+id).val(),
				realname:$('.realname'+id).val(),
				telphone:$('.telphone'+id).val(),
			},
			dataType:'json',
			type:'post',        
			success:function(data){
				alert(data.msg);
				$('.showbiaoqian'+id).text("标签:"+$('.biaoqian'+id).val());
				$('.showrealname'+id).text("姓名:"+$('.realname'+id).val());
				$('.showtelphone'+id).text("电话:"+$('.telphone'+id).val());
			},
		});
	});
})
function showimage(source)
 {
	 $("#ShowImage_Form").find("#img_show").html("<image src='"+source+"' class='carousel-inner img-responsive img-rounded' />");
	 $("#ShowImage_Form").modal();
 }
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>