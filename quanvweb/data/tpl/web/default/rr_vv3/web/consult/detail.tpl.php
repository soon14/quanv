<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-heading"> <h2>预约详情</h2> </div>



<form <?php  if('consult.edit') { ?>action="" method='post'<?php  } ?> class='form-horizontal form-validate'>

 <input type="hidden" name="referer" value="<?php  echo referer()?>" />

	<div class="tabs-container">

		<div class="tabs">

			<ul class="nav nav-tabs">

				<li class="active"><a data-toggle="tab" href="#tab-basic" aria-expanded="true"> 预约信息</a></li>

			</ul>

			<div class="tab-content ">

				<div id="tab-basic" class="tab-pane active">
					
					<div class="form-group">

					    <label class="col-sm-2 control-label">提交者</label>

					    <div class="col-sm-9 col-xs-12">

					        <img src='<?php  echo $member['avatar'];?>' style='width:50px;height:50px;padding:1px;border:1px solid #ccc' />

					        <?php  if(strexists($member['openid'],'sns_wa_')) { ?>

					            <i class="icon icon-wxapp" data-toggle="tooltip" data-placement="top" data-original-title="小程序注册" style="color: #7586db;"></i>

					        <?php  } ?>

					        <?php  if(empty($member['realname'])) { ?><?php  echo $member['nickname'];?><?php  } else { ?><?php  echo $member['realname'];?><?php  } ?>

					    </div>

					</div>

					<div class="form-group">

					    <label class="col-sm-2 control-label">开始时间</label>

					    <div class="col-sm-9 col-xs-12">

					        <div class='form-control-static'><?php  echo $info['start_time'];?></div>

					    </div>

					</div>


					<div class="form-group">

					    <label class="col-sm-2 control-label">结束时间</label>

					    <div class="col-sm-9 col-xs-12">

					        <div class='form-control-static'><?php  echo $info['end_time'];?></div>

					    </div>

					</div>

					<div class="form-group">

					    <label class="col-sm-2 control-label">星期</label>

					    <div class="col-sm-9 col-xs-12">

					        <div class='form-control-static'><?php  echo $info['week'];?></div>

					    </div>

					</div>

					<div class="form-group">

					    <label class="col-sm-2 control-label">预约人数</label>

					    <div class="col-sm-9 col-xs-12">

					        <div class='form-control-static'><?php  echo $info['consult_nums'];?>/<?php  echo $info['people_nums'];?></div>

					    </div>

					</div>

					<div class="form-group">

					    <label class="col-sm-2 control-label">预约费用</label>

					    <div class="col-sm-9 col-xs-12">

					        <div class='form-control-static'>￥<?php  echo $info['price'];?></div>

					    </div>

					</div>


					<div class="form-group">

					    <label class="col-sm-2 control-label">预约地址</label>

					    <div class="col-sm-9 col-xs-12">

					        <div class='form-control-static'><?php  echo $info['address'];?></div>

					    </div>

					</div>
					
					<div class="form-group">

					    <label class="col-sm-2 control-label">病情</label>

					    <div class="col-sm-9 col-xs-12">

					        <div class='form-control-static'><?php  echo $info['title'];?></div>

					    </div>

					</div>
					
					<div class="form-group">

					    <label class="col-sm-2 control-label">病情描述</label>

					    <div class="col-sm-9 col-xs-12">

					        <div class='form-control-static'><?php  echo $info['remarks'];?></div>

					    </div>

					</div>


					<div class="form-group">

					    <label class="col-sm-2 control-label">提交时间</label>

					    <div class="col-sm-9 col-xs-12">

					        <div class='form-control-static'><?php  echo $info['createtime'];?></div>

					    </div>

					</div>

				</div>

			</div>

		</div>

	</div>

	<div class="form-group"></div>	

          <div class="form-group">

		<label class="col-sm-2 control-label"></label>

		<div class="col-sm-9 col-xs-12">

			<?php if(cv('consult.edit')) { ?>

			<!-- <input type="submit"  value="提交" class="btn btn-primary" /> -->

			<?php  } ?>

			<input type="button" class="btn btn-primary" name="submit" onclick="history.go(-1)" value="返回列表" <?php if(cv('consult.edit')) { ?>style='margin-left:10px;'<?php  } ?> />

		</div>

	</div>



</form>



<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

