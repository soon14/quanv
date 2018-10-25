<?php defined('IN_IA') or exit('Access Denied');?><div class="form-group">
	<label class="col-sm-2 control-label must"> 微信会员卡领取</label>
	<div class="col-sm-9 col-xs-12" >
		<?php if( ce('member.card' ,$item) ) { ?>
			<label class="radio-inline"><input type="radio" name="centerget" value="0" <?php  if(empty($item['centerget'])) { ?>checked="true"<?php  } ?>  /> 关闭</label>
			<label class="radio-inline"><input type="radio" name="centerget" value="1" <?php  if($item['centerget'] == 1) { ?>checked="true"<?php  } ?>" /> 开启</label>
			<span class='help-block'>会员中心是否可以领取微信会员卡</span>
		<?php  } else { ?>
			<div class='form-control-static'>
				<?php  if($item['centerget']==0) { ?>
					不可以
				<?php  } else if($item['centerget']==1) { ?>
					可以
				<?php  } ?>
			</div>
		<?php  } ?>
	</div>
</div>

<div class="form-group">
	<label class="col-sm-2 control-label must"> 激活信息</label>
	<div class="col-sm-9 col-xs-12" >
		<?php if( ce('member.card' ,$item) ) { ?>
		<label class="radio-inline"><input type="radio" name="openactive" value="0" <?php  if(empty($item['openactive'])) { ?>checked="true"<?php  } ?>  /> 关闭</label>
		<label class="radio-inline"><input type="radio" name="openactive" value="1" <?php  if($item['openactive'] == 1) { ?>checked="true"<?php  } ?>" /> 开启</label>
		<span class='help-block'>微信会员卡激活是否需要填写激活信息</span>
		<?php  } else { ?>
		<div class='form-control-static'>
			<?php  if($item['openactive']==0) { ?>
				关闭
			<?php  } else if($item['openactive']==1) { ?>
				开启
			<?php  } ?>
		</div>
		<?php  } ?>
	</div>
</div>

<div class='form-group-title'>激活信息字段</div>
<div class="form-group">
	<label class="col-sm-2 control-label must">真实姓名</label>
	<div class="col-sm-9 col-xs-12">
		<?php if( ce('member.card' ,$item) ) { ?>
			<label class="radio-inline"><input type="radio" value="0" name="realname"  <?php  if(empty($item['realname'])) { ?>checked="true"<?php  } ?>  >不填写</label>
			<label class="radio-inline"><input type="radio" value="1" name="realname"  <?php  if($item['realname'] == 1) { ?>checked="true"<?php  } ?> >填写</label>
		<?php  } else { ?>
			<div class='form-control-static'>
				<?php  if($item['realname']==0) { ?>
				关闭
				<?php  } else if($item['realname']==1) { ?>
				开启
				<?php  } ?>
			</div>
		<?php  } ?>
	</div>
</div>


<div class="form-group">
	<label class="col-sm-2 control-label must">手机号</label>
	<div class="col-sm-9 col-xs-12">
		<?php if( ce('member.card' ,$item) ) { ?>
		<label class="radio-inline"><input type="radio" value="0" name="mobile"  <?php  if(empty($item['mobile'])) { ?>checked="true"<?php  } ?>  >不填写</label>
		<label class="radio-inline"><input type="radio" value="1" name="mobile"  <?php  if($item['mobile'] == 1) { ?>checked="true"<?php  } ?> >填写</label>
		<?php  } else { ?>
		<div class='form-control-static'>
			<?php  if($item['mobile']==0) { ?>
			关闭
			<?php  } else if($item['mobile']==1) { ?>
			开启
			<?php  } ?>
		</div>
		<?php  } ?>
	</div>
</div>
<?php  if(com("sms")) { ?>
	<div class="form-group">
		<label class="col-sm-2 control-label must">手机短信验证</label>
		<div class="col-sm-9 col-xs-12">
			<?php if( ce('member.card' ,$item) ) { ?>
				<label class="radio-inline"><input type="radio" value="0" name="sms_active"  <?php  if(empty($item['sms_active'])) { ?>checked="true"<?php  } ?>  >关闭</label>
				<label class="radio-inline"><input type="radio" value="1" name="sms_active"  <?php  if($item['sms_active'] == 1) { ?>checked="true"<?php  } ?> >开启</label>
			<?php  } else { ?>
			<div class='form-control-static'>
				<?php  if($item['sms_active']==0) { ?>
					关闭
				<?php  } else if($item['sms_active']==1) { ?>
					开启
				<?php  } ?>
			</div>
			<?php  } ?>

		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label must">短信模板</label>
		<div class="col-sm-9 col-xs-12">
			<select class="select2" style="display: block; width: 100%" name="sms_id">
			<option value=''>从短信消息库中选择</option>
			<?php  if(is_array($template_sms)) { foreach($template_sms as $template_val) { ?>
				<option value="<?php  echo $template_val['id'];?>" <?php  if($item['sms_id']==$template_val['id']) { ?>selected<?php  } ?>><?php  echo $template_val['name'];?></option>
			<?php  } } ?>
			</select>
		</div>
	</div>
<?php  } ?>

<!--
<div class="form-group">
	<label class="col-sm-2 control-label must">所属城市</label>
	<div class="col-sm-9 col-xs-12">
		<label class="radio-inline"><input type="radio" value="0" name="city"  <?php  if(empty($item['mobile'])) { ?>checked="true"<?php  } ?>  >关闭</label>
		<label class="radio-inline"><input type="radio" value="1" name="city"  <?php  if($item['mobile'] == 1) { ?>checked="true"<?php  } ?> >开启</label>
	</div>
</div>-->

<!--yifuyuanma-->