<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-heading"> <h2>患者详情</h2> </div>



<form <?php  if('member.patients.edit') { ?>action="" method='post'<?php  } ?> class='form-horizontal form-validate'>

 <input type="hidden" name="referer" value="<?php  echo referer()?>" />

	<div class="tabs-container">



		<div class="tabs">

			<ul class="nav nav-tabs">

				<?php  if($hasupdiag) { ?>

					<li class="active"><a data-toggle="tab" href="#tab-basic" aria-expanded="true"> 基本信息</a></li>

					<li class=""><a data-toggle="tab" href="#tab-diag" aria-expanded="false"> 病情资料</a></li>

					<!-- <li class=""><a data-toggle="tab" href="#tab-plusdiag" aria-expanded="false"> 添加病情资料</a></li> -->

				<?php  } else { ?>

					<li class=""><a data-toggle="tab" href="#tab-updiag" aria-expanded="true"> 查看病情资料</a></li>

				<?php  } ?>

				
			</ul>

			<div class="tab-content ">

				<?php  if($hasupdiag) { ?>

					<div id="tab-basic" class="tab-pane active"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('member/patients/basic', TEMPLATE_INCLUDEPATH)) : (include template('member/patients/basic', TEMPLATE_INCLUDEPATH));?></div>
				
					<div id="tab-diag" class="tab-pane "><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('member/patients/diag', TEMPLATE_INCLUDEPATH)) : (include template('member/patients/diag', TEMPLATE_INCLUDEPATH));?></div>

					<div id="tab-plusdiag" class="tab-pane "><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('member/patients/plusdiag', TEMPLATE_INCLUDEPATH)) : (include template('member/patients/plusdiag', TEMPLATE_INCLUDEPATH));?></div>

				<?php  } else { ?>

					<div id="tab-updiag" class="tab-pane active"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('member/patients/updiag', TEMPLATE_INCLUDEPATH)) : (include template('member/patients/updiag', TEMPLATE_INCLUDEPATH));?></div>

				<?php  } ?>

				
			</div>

		</div>

	</div>

	



</form>



<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

