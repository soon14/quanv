<?php defined('IN_IA') or exit('Access Denied');?><div class="remote-qiniu">
	<div class="form-group">
		<label class="col-xs-12 col-sm-3 col-md-2 control-label">七牛云存储开关</label>
		<div class="col-sm-9 col-xs-12">
			<label for="isqiniu1" class="radio-inline"><input name="isqiniu" value="1" id="isqiniu1" <?php  if($setting['isqiniu'] == 1) { ?>checked="true"<?php  } ?> type="radio"> 开启</label>
			&nbsp;&nbsp;&nbsp;
			<label for="isqiniu2" class="radio-inline"><input name="isqiniu" value="0" id="isqiniu2" <?php  if($setting['isqiniu'] == 0) { ?>checked="true"<?php  } ?> type="radio"> 关闭</label>
			&nbsp;&nbsp;&nbsp;
			<label for="isqiniu3" class="radio-inline"><input name="isqiniu" value="3" id="isqiniu3" <?php  if($setting['isqiniu'] == 3) { ?>checked="true"<?php  } ?> type="radio"> 使用系统统一配置</label>
		</div>
	</div>
	<div class="form-group">
		<label class="col-xs-12 col-sm-3 col-md-2 control-label">Accesskey</label>
		<div class="col-sm-9 col-xs-12">
			<input name="qiniuaccesskey" class="form-control" value="<?php  echo $setting['qiniuaccesskey'];?>" placeholder="" type="text">
			<span class="help-block">用于签名的公钥</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-xs-12 col-sm-3 col-md-2 control-label">Secretkey</label>
		<div class="col-sm-9 col-xs-12">
			<input name="qiniusecretkey" class="form-control encrypt" value="<?php  echo $setting['qiniusecretkey'];?>" placeholder="" type="text">
			<span class="help-block">用于签名的私钥</span>
		</div>
	</div>
	<div class="form-group" id="qiniubucket">
		<label class="col-xs-12 col-sm-3 col-md-2 control-label">Bucket</label>
		<div class="col-sm-9 col-xs-12">
			<input name="qiniubucket" class="form-control" value="<?php  echo $setting['qiniubucket'];?>" placeholder="" type="text">
			<span class="help-block">请保证bucket为可公共读取的</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-xs-12 col-sm-3 col-md-2 control-label">Url</label>
		<div class="col-sm-9 col-xs-12">
			<input name="qiniuurl" class="form-control" value="<?php  echo $setting['qiniuurl'];?>" placeholder="" type="text">
			<span class="help-block" style="color:red;">七牛支持用户自定义访问域名。注：url开头加http://或https://结尾不加 ‘/’例：http://abc.com</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
		<div class="col-sm-9 col-xs-12">
			<button name="button" type="button" class="btn btn-info js-checkremoteqiniu" value="check">测试配置（无需保存）</button>
		</div>
	</div>
</div>
<script type="text/javascript">
$('.js-checkremoteqiniu').on('click', function(){
	var key = $.trim($(':text[name="qiniuaccesskey"]').val());
	if (key == '') {
		alert('请填写Accesskey');
		return false;
	}
	var secret = $.trim($(':text[name="qiniusecretkey"]').val());
	if (secret == '') {
		alert('请填写Secretkey');
		return false;
	}
	var param = {
		'accesskey' : $.trim($(':text[name="qiniuaccesskey"]').val()),
		'secretkey' : $.trim($(':text[name="qiniusecretkey"]').val()),
		'url'  : $.trim($(':text[name="qiniuurl"]').val()),
		'bucket' :  $.trim($(':text[name="qiniubucket"]').val()),
		'district' :  $.trim($('[name="qiniuurl"]').val())
	};
	$.post("./index.php?c=utility&a=checkattach&do=qiniu&",param, function(data) {
		var data = $.parseJSON(data);
		if(data.message.errno == 0) {
			alert('配置成功');
			return false;
		}
		if(data.message.errno < 0) {
			alert(data.message.message);
			return false;
		}
	});
});
</script>