<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="main">
	<form action="<?php  echo $this->createWebUrl('setting',array('op'=>'post'));?>" method="post" class="form-horizontal form">
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#basic" role="tab" data-toggle="tab">基本设置</a></li>
			<li role="presentation"><a href="#share" role="tab" data-toggle="tab">分享设置</a></li>
			<li role="presentation"><a href="#tplmsg" role="tab" data-toggle="tab">消息提醒设置</a></li>
			<li role="presentation"><a href="#allshare" role="tab" data-toggle="tab">共享设置</a></li>
			<li role="presentation"><a href="#color" role="tab" data-toggle="tab">色调设置</a></li>
			<li role="presentation"><a href="#nofollow" role="tab" data-toggle="tab">未关注设置</a></li>
			<li role="presentation"><a href="#qunliao" role="tab" data-toggle="tab">群聊设置</a></li>
			<li role="presentation"><a href="#fujian" role="tab" data-toggle="tab">七牛云存储</a></li>
			<li role="presentation"><a href="#tuling" role="tab" data-toggle="tab">图灵机器人</a></li>
			<li role="presentation"><a href="#tongbu" role="tab" data-toggle="tab">同步数据</a></li>
			<li role="presentation"><a href="#youhua" role="tab" data-toggle="tab">优化加速</a></li>
		</ul>
		
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active panel panel-default" id="basic">
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">客服中心标题</label>
						<div class="col-sm-9 col-xs-12">
							<input name="title" class="form-control" value="<?php  echo $setting['title'];?>" type="text">
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">敏感词过滤</label>
						<div class="col-sm-7 col-xs-12">
							<textarea class="form-control" name="mingan"><?php  echo $setting['mingan'];?></textarea>
							<span class="help-block" style="color:#900;">多个敏感词可用|隔开</span>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">版权信息</label>
						<div class="col-sm-7 col-xs-12">
							<textarea class="form-control" name="copyright"><?php  echo $setting['copyright'];?></textarea>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">页面底部菜单文字一</label>
						<div class="col-sm-9 col-xs-12">
							<input name="footertext1" class="form-control" value="<?php  echo $setting['footertext1'];?>" type="text">
							<span class="help-block" style="color:#900;">（原选择客服）建议不要超过4个汉字</span>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">页面底部菜单文字二</label>
						<div class="col-sm-9 col-xs-12">
							<input name="footertext2" class="form-control" value="<?php  echo $setting['footertext2'];?>" type="text">
							<span class="help-block" style="color:#900;">（原群聊中心）建议不要超过4个汉字</span>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">页面底部菜单文字三</label>
						<div class="col-sm-9 col-xs-12">
							<input name="footertext4" class="form-control" value="<?php  echo $setting['footertext4'];?>" type="text">
							<span class="help-block" style="color:#900;">（原消息管理）建议不要超过4个汉字</span>
						</div>
					</div>
					
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">选择客服页面模板</label>
						<div class="col-sm-9 col-xs-12">
							<label for="chosekefutem0" class="radio-inline"><input name="chosekefutem" value="0" id="chosekefutem0" <?php  if($setting['chosekefutem'] == 0) { ?>checked="true"<?php  } ?> type="radio"> 传统样式</label>
							&nbsp;&nbsp;&nbsp;
							<label for="chosekefutem1" class="radio-inline"><input name="chosekefutem" value="1" id="chosekefutem1" <?php  if($setting['chosekefutem'] == 1) { ?>checked="true"<?php  } ?> type="radio"> 宫格样式</label>
							&nbsp;&nbsp;&nbsp;
							<label for="chosekefutem2" class="radio-inline"><input name="chosekefutem" value="2" id="chosekefutem2" <?php  if($setting['chosekefutem'] == 2) { ?>checked="true"<?php  } ?> type="radio"> 宫格样式（不弹客服头像）</label>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">客服随机展示</label>
						<div class="col-sm-9 col-xs-12">
							<label for="suiji1" class="radio-inline"><input name="suiji" value="1" id="suiji1" <?php  if($setting['suiji'] == 1) { ?>checked="true"<?php  } ?> type="radio"> 开启</label>
							&nbsp;&nbsp;&nbsp;
							<label for="suiji0" class="radio-inline"><input name="suiji" value="0" id="suiji0" <?php  if($setting['suiji'] == 0) { ?>checked="true"<?php  } ?> type="radio"> 关闭</label>
							<span class="help-block" style="color:#900;">客服随机展示指的是在客服列表页面，或者客服组下面的客服列表页面，每次点开页面客服都随机排列</span>
						</div>
					</div>
				</div>
			</div>
			
			<div role="tabpanel" class="tab-pane panel panel-default" id="share">
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">分享标题</label>
						<div class="col-sm-7 col-xs-12">
							<input name="sharetitle" class="form-control" value="<?php  echo $setting['sharetitle'];?>" type="text">
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">分享图片</label>
						<div class="col-sm-9 col-xs-12">
							<?php  echo tpl_form_field_image('sharethumb', $setting['sharethumb'], '', array('extras' => array('text' => 'readonly')))?>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">分享描述</label>
						<div class="col-sm-7 col-xs-12">
							<textarea class="form-control" name="sharedes"><?php  echo $setting['sharedes'];?></textarea>
						</div>
					</div>
				</div>
			</div>

			<div role="tabpanel" class="tab-pane panel panel-default" id="tplmsg">
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">开启消息提醒</label>
						<div class="col-sm-9 col-xs-12">
							<label for="istplon1" class="radio-inline"><input name="istplon" value="1" id="istplon1" <?php  if($setting['istplon'] == 1) { ?>checked="true"<?php  } ?> type="radio"> 是</label>
							&nbsp;&nbsp;&nbsp;
							<label for="istplon2" class="radio-inline"><input name="istplon" value="0" id="istplon2" <?php  if($setting['istplon'] == 0) { ?>checked="true"<?php  } ?> type="radio"> 否</label>
							<span class="help-block"></span>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">消息提醒时间间隔</label>
						<div class="col-sm-2 col-xs-12">
							<div class="input-group">
								<input class="form-control" name="kefutplminute" value="<?php  echo $setting['kefutplminute'];?>" type="text">
								<span class="input-group-addon">秒</span>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">开启群聊消息提醒</label>
						<div class="col-sm-9 col-xs-12">
							<label for="isgrouptplon1" class="radio-inline"><input name="isgrouptplon" value="1" id="isgrouptplon1" <?php  if($setting['isgrouptplon'] == 1) { ?>checked="true"<?php  } ?> type="radio"> 是</label>
							&nbsp;&nbsp;&nbsp;
							<label for="isgrouptplon2" class="radio-inline"><input name="isgrouptplon" value="0" id="isgrouptplon2" <?php  if($setting['isgrouptplon'] == 0) { ?>checked="true"<?php  } ?> type="radio"> 否</label>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">群聊消息提醒时间间隔</label>
						<div class="col-sm-2 col-xs-12">
							<div class="input-group">
								<input class="form-control" name="grouptplminute" value="<?php  echo $setting['grouptplminute'];?>" type="text">
								<span class="input-group-addon">秒</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div role="tabpanel" class="tab-pane panel panel-default" id="allshare">
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启客户聊天记录共享</label>
						<div class="col-sm-9 col-xs-12">
							<label for="issharemsg1" class="radio-inline"><input name="issharemsg" value="1" id="issharemsg1" <?php  if($setting['issharemsg'] == 1) { ?>checked="true"<?php  } ?> type="radio"> 是</label>
							&nbsp;&nbsp;&nbsp;
							<label for="issharemsg2" class="radio-inline"><input name="issharemsg" value="0" id="issharemsg2" <?php  if($setting['issharemsg'] == 0) { ?>checked="true"<?php  } ?> type="radio"> 否</label>
							<span class="help-block"></span>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">聊天记录共享类型</label>
						<div class="col-sm-9 col-xs-12">
							<label for="sharetype1" class="radio-inline"><input name="sharetype" value="1" id="sharetype1" <?php  if($setting['sharetype'] == 1) { ?>checked="true"<?php  } ?> type="radio"> 客服组内共享</label>
							&nbsp;&nbsp;&nbsp;
							<label for="sharetype2" class="radio-inline"><input name="sharetype" value="0" id="sharetype2" <?php  if($setting['sharetype'] == 0) { ?>checked="true"<?php  } ?> type="radio"> 全部客服共享</label>
							<span class="help-block"></span>
						</div>
					</div>
				</div>
			</div>
			
			<div role="tabpanel" class="tab-pane panel panel-default" id="color">
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">聊天界面背景颜色</label>
						<div class="col-sm-9 col-xs-12">
							<?php  echo tpl_form_field_color('bgcolor',$setting['bgcolor']);?>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">界面主色调</label>
						<div class="col-sm-9 col-xs-12">
							<?php  echo tpl_form_field_color('temcolor',$setting['temcolor']);?>
						</div>
					</div>
				</div>
			</div>
			
			<div role="tabpanel" class="tab-pane panel panel-default" id="nofollow">
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">未关注用户头像</label>
						<div class="col-sm-9 col-xs-12">
							<?php  echo tpl_form_field_image('defaultavatar', $setting['defaultavatar'], '', array('extras' => array('text' => 'readonly')))?>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启未关注提醒</label>
						<div class="col-sm-9 col-xs-12">
							<label for="isshowwgz1" class="radio-inline"><input name="isshowwgz" value="1" id="isshowwgz1" <?php  if($setting['isshowwgz'] == 1) { ?>checked="true"<?php  } ?> type="radio"> 是</label>
							&nbsp;&nbsp;&nbsp;
							<label for="isshowwgz2" class="radio-inline"><input name="isshowwgz" value="0" id="isshowwgz2" <?php  if($setting['isshowwgz'] == 0) { ?>checked="true"<?php  } ?> type="radio"> 否</label>
							<span class="help-block"></span>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">未关注用户说明</label>
						<div class="col-sm-7 col-xs-12">
							<textarea class="form-control" name="unfollowtext"><?php  echo $setting['unfollowtext'];?></textarea>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">公众号二维码</label>
						<div class="col-sm-9 col-xs-12">
							<?php  echo tpl_form_field_image('followqrcode', $setting['followqrcode'], '', array('extras' => array('text' => 'readonly')))?>
						</div>
					</div>
				</div>
			</div>
			
			<div role="tabpanel" class="tab-pane panel panel-default" id="qunliao">
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">前端是否展示群聊</label>
						<div class="col-sm-9 col-xs-12">
							<label for="isgroupon1" class="radio-inline"><input name="isgroupon" value="1" id="isgroupon1" <?php  if($setting['isgroupon'] == 1) { ?>checked="true"<?php  } ?> type="radio"> 是</label>
							&nbsp;&nbsp;&nbsp;
							<label for="isgroupon2" class="radio-inline"><input name="isgroupon" value="0" id="isgroupon2" <?php  if($setting['isgroupon'] == 0) { ?>checked="true"<?php  } ?> type="radio"> 否</label>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示群聊人数</label>
						<div class="col-sm-9 col-xs-12">
							<label for="ishowgroupnum1" class="radio-inline"><input name="ishowgroupnum" value="1" id="ishowgroupnum1" <?php  if($setting['ishowgroupnum'] == 1) { ?>checked="true"<?php  } ?> type="radio"> 是</label>
							&nbsp;&nbsp;&nbsp;
							<label for="ishowgroupnum2" class="radio-inline"><input name="ishowgroupnum" value="0" id="ishowgroupnum2" <?php  if($setting['ishowgroupnum'] == 0) { ?>checked="true"<?php  } ?> type="radio"> 否</label>
						</div>
					</div>
				</div>
			</div>
			
			<div role="tabpanel" class="tab-pane panel panel-default" id="fujian">
				<div class="panel-body">
					<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/set_fujian', TEMPLATE_INCLUDEPATH)) : (include template('web/set_fujian', TEMPLATE_INCLUDEPATH));?>
				</div>
			</div>
			
			<div role="tabpanel" class="tab-pane panel panel-default" id="tuling">
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">图灵机器人的KEY</label>
						<div class="col-sm-7 col-xs-12">
							<input name="tulingkey" class="form-control" value="<?php  echo $setting['tulingkey'];?>" type="text">
							<div class="help-block">如果没有申请KEY，请留空！！你也可以去<a href="http://www.tuling123.com/" target="_blank" style="text-decoration:underline;color:#FF0000">图灵</a>网站申请自己独立的KEY</div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启</label>
						<div class="col-sm-9 col-xs-12">
							<label for="istulingon1" class="radio-inline"><input name="istulingon" value="1" id="istulingon1" <?php  if($setting['istulingon'] == 1) { ?>checked="true"<?php  } ?> type="radio"> 是</label>
							&nbsp;&nbsp;&nbsp;
							<label for="istulingon2" class="radio-inline"><input name="istulingon" value="0" id="istulingon2" <?php  if($setting['istulingon'] == 0) { ?>checked="true"<?php  } ?> type="radio"> 否</label>
						</div>
					</div>
				</div>
			</div>
			
			<div role="tabpanel" class="tab-pane panel panel-default" id="tongbu">
				<div class="panel-body">
					<div class="alert alert-danger" role="alert">
						<h2>同步数据说明</h2>
						<p>1.同步用户由未关注变为已关注头像和昵称的数据同步。</p>
						<p>2.同步版本迭代数据结构。（加快程序运行效率）</p>
					</div>
					<div class="form-group">
						<div class="col-sm-12 col-xs-12">
							<a href="<?php  echo $this->createWebUrl('setting',array('op'=>'tongbu'))?>" class="btn btn-success">同步数据</a>
						</div>
					</div>
				</div>
			</div>
			
			<div role="tabpanel" class="tab-pane panel panel-default" id="youhua">
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-1 col-md-1 control-label"></label>
						<div class="col-sm-4 col-xs-12">
							<div class="input-group">
								<span class="input-group-addon">清除</span>
								<input class="form-control" name="days" id="days" value="14" type="text">
								<span class="input-group-addon">天前所有客服功能数据</span>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-1 col-md-1 control-label"></label>
						<div class="col-sm-4 col-xs-12">
							<div class="input-group">
								<span class="input-group-addon">清除</span>
								<input class="form-control" name="days2" id="days2" value="14" type="text">
								<span class="input-group-addon">天前所有群聊功能数据</span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-xs-12 col-sm-1 col-md-1 control-label"></label>
						<div class="col-sm-4 col-xs-12">
							<button type="button" class="btn btn-success doyouhua">确定优化</button>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group col-sm-12">
				<input type="hidden" name="id" value="<?php  echo $setting['id'];?>" />
				<input name="submit" type="submit" value="确定" class="btn btn-primary"> 
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
$(function(){
	$(".doyouhua").click(function(){
		var r=confirm("确定要优化吗？删除数据后不能恢复！")
		if (r==true){
			$.ajax({
				url:"<?php  echo $this->createWebUrl('setting')?>",
				data:{
					op:'youhua',
					days:$("#days").val(),
					days2:$("#days2").val(),
				},
				dataType:'json',
				type:'post',        
				success:function(data){
					alert(data.msg);
				},
			});
		}else{
		}

	});
})
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>