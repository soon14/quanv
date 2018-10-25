<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-heading"> <h2>文章详情</h2> </div>



<form <?php  if('articles.edit') { ?>action="" method='post'<?php  } ?> class='form-horizontal form-validate'>

 <input type="hidden" name="referer" value="<?php  echo referer()?>" />
 <input type="hidden" name="id" value="<?php  echo $info['id'];?>" />

	<div class="tabs-container">



		<div class="tabs">

			<ul class="nav nav-tabs">

				<li class="active"><a data-toggle="tab" href="#tab-basic" aria-expanded="true"> 文章信息</a></li>

			</ul>

			<div class="tab-content ">

				<div id="tab-basic" class="tab-pane active"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('articles/basic', TEMPLATE_INCLUDEPATH)) : (include template('articles/basic', TEMPLATE_INCLUDEPATH));?></div>

			</div>

		</div>

	</div>

	<div class="form-group"></div>	

          <div class="form-group">

		<label class="col-sm-2 control-label"></label>

		<div class="col-sm-9 col-xs-12">

			<?php if(cv('articles.edit')) { ?>

			<input type="submit"  value="提交" class="btn btn-primary" />

			<?php  } ?>

			<input type="button" class="btn btn-primary" name="submit" onclick="history.go(-1)" value="返回列表" <?php if(cv('articles.edit')) { ?>style='margin-left:10px;'<?php  } ?> />

		</div>

	</div>



</form>



<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

