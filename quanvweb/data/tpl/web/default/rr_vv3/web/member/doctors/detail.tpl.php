<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-heading"> <h2>医生详情</h2> </div>



<form <?php  if('member.doctors.edit') { ?>action="" method='post'<?php  } ?> class='form-horizontal form-validate'>

 <input type="hidden" name="referer" value="<?php  echo referer()?>" />

	<div class="tabs-container">



		<div class="tabs">

			<ul class="nav nav-tabs">

				<li class="active"><a data-toggle="tab" href="#tab-basic" aria-expanded="true"> 基本信息</a></li>

			</ul>

			<div class="tab-content ">

				<div id="tab-basic" class="tab-pane active"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('member/doctors/basic', TEMPLATE_INCLUDEPATH)) : (include template('member/doctors/basic', TEMPLATE_INCLUDEPATH));?></div>

			</div>

		</div>

	</div>

	<div class="form-group"></div>	

          <div class="form-group">

		<label class="col-sm-2 control-label"></label>

		<div class="col-sm-9 col-xs-12">

			<?php if(cv('member.doctors.edit')) { ?>

			<input type="submit"  value="提交" class="btn btn-primary" />

			<?php  } ?>

			<input type="button" class="btn btn-default" name="submit" onclick="history.go(-1)" value="返回列表" <?php if(cv('member.doctors.edit')) { ?>style='margin-left:10px;'<?php  } ?> />

		</div>

	</div>



</form>



<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

