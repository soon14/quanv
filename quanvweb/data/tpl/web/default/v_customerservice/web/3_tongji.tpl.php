<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>

<ul class="nav nav-tabs">
	<li <?php  if($operation == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('tongji')?>">今日统计</a></li>
	<li <?php  if($operation == 'month') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('tongji',array('op' =>'month'))?>">当月统计</a></li>
</ul>

<div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<form action="./index.php" method="get" class="form-horizontal" role="form" id="form1">
			<input type="hidden" name="c" value="site" />
			<input type="hidden" name="a" value="entry" />
			<input type="hidden" name="m" value="v_customerservice" />
			<input type="hidden" name="do" value="tongji" />
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">选择客服</label>
				<div class="col-xs-12 col-sm-8 col-lg-9">
					<select name="kefuopenid" class='form-control'>
						<option value="0">--请选择客服--</option>
						<?php  if(is_array($cservicelist)) { foreach($cservicelist as $grow) { ?>
						<option value="<?php  echo $grow['content'];?>" <?php  if($_GPC['kefuopenid'] == $grow['content']) { ?>selected<?php  } ?>><?php  echo $grow['name'];?></option>
						<?php  } } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-12 col-xs-12">
					<input type="hidden" name="op" value="<?php  echo $operation;?>" />
					<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
				</div>
			</div>
		</form>
	</div>
</div>


<?php  if($operation == 'display') { ?>
<div class="main panel panel-default">
	<div class="panel-heading">接待次数</div>
	<div class="panel-body" style="padding-left:0px;padding-right:0px;">
		<canvas id="Chart" height="100"></canvas>
	</div>
</div>
<?php  } ?>
<?php  if($operation == 'month') { ?>
<div class="main panel panel-default">
	<div class="panel-heading">接待次数</div>
	<div class="panel-body" style="padding-left:0px;padding-right:0px;">
		<canvas id="Chart" height="100"></canvas>
	</div>
</div>
<?php  } ?>
<script type="text/javascript" src="<?php echo MD_ROOT;?>static/newui/js/chart.min.js"></script>
<script type="text/javascript">
var chart = null;
var defaults = {
	responsive:true,
}
var data = {
	labels : <?php  echo $labels;?>,
	datasets : [
		{
			fillColor : "rgba(151,187,205,0.5)",
			strokeColor : "rgba(151,187,205,1)",
			pointColor : "rgba(151,187,205,1)",
			pointStrokeColor : "#428BCA",
			data : <?php  echo $datas;?>
		}
	]
}
var ctx = document.getElementById("Chart").getContext("2d");
chart = new Chart(ctx).Line(data,defaults);
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>