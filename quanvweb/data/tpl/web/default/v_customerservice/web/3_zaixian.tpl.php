<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li <?php  if($operation == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('zaixian', array('op' => 'display'))?>">客户在线管理</a></li>
	<li <?php  if($operation == 'kefu') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('zaixian', array('op' => 'kefu'))?>">客服在线管理</a></li>
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
				<input type="hidden" name="do" value="zaixian" />
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
						<a href="<?php  echo $this->createWebUrl('zaixian',array('op'=>'kehuxiaxianpl'))?>" class="btn btn-danger btn-sm">批量下线</a>
					</div>
				</div>
			</form>
		</div>
	</div>
	
	<div class="panel panel-default">
		<div class="panel-heading">列表</div>
		<div class="panel-body table-responsive">
			<table class="table table-hover table-striped table-condensed">
				<thead>
					<tr>
						<th style="width:18%;">客户</th>
						<th style="width:18%;">客服</th>
						<th style="width:22%;">Openid<br />在线状态</th>
						<th style="width:20%;">最后对话</th>
						<th style="text-align:right;">操作</th>
					</tr>
				</thead>
				<tbody>
				<?php  if(is_array($list)) { foreach($list as $row) { ?>
				<tr>
					<td><img src="<?php  echo $row['fansavatar'];?>" width="50" height="50" style="border-radius:50px;" /> <?php  echo $row['fansnickname'];?></td>
					<td><img src="<?php  echo $row['kefuavatar'];?>" width="50" height="50" style="border-radius:50px;" /> <?php  echo $row['kefunickname'];?></td>
					<td>
						<?php  echo $row['fansopenid'];?>
						<?php  if($row['fszx'] == 1) { ?>
						<br />
						<span class="label label-success">客户在线</span>
						<?php  } ?>
					</td>
					<td>
						<?php  if($row['lasttime'] > 0) { ?><?php  echo date("Y-m-d H:i:s",$row['lasttime'])?><?php  } else { ?>无<?php  } ?>
					</td>
					<td style="text-align:right;">
						<a href="<?php  echo $this->createWebUrl('zaixian',array('op'=>'kehuxiaxian','id'=>$row['id']))?>" class="btn btn-danger btn-sm">下线</a>
					</td>
				</tr>
				<?php  } } ?>
				</tbody>
			</table>
		</div>
	</div>
	<?php  echo $pager;?>
</div>
<?php  } ?>
<?php  if($operation == 'kefu') { ?>
<div class="main">
	<div class="panel panel-info">
		<div class="panel-heading">筛选</div>
		<div class="panel-body">
			<form action="./index.php" method="get" class="form-horizontal" role="form" id="form1">
				<input type="hidden" name="c" value="site" />
				<input type="hidden" name="a" value="entry" />
				<input type="hidden" name="m" value="v_customerservice" />
				<input type="hidden" name="do" value="zaixian" />
				<input type="hidden" name="op" value="kefu" />
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">搜索客户</label>
					<div class="col-xs-12 col-sm-8 col-lg-9">
						<input type="text" name="keyword" class='form-control' placeholder='可根据客户昵称进行模糊搜索' value="<?php  echo $keyword;?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label"></label>
					<div class="col-sm-7 col-xs-12">
						<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
						<button type="button" class="btn btn-default">总记录数：<?php  echo $total;?></button>
						<a href="<?php  echo $this->createWebUrl('zaixian',array('op'=>'kefuxiaxianpl'))?>" class="btn btn-danger btn-sm">批量下线</a>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">列表</div>
		<div class="panel-body table-responsive">
			<table class="table table-hover table-striped table-condensed">
				<thead>
					<tr>
						<th style="width:18%;">客服</th>
						<th style="width:18%;">客户</th>
						<th style="width:22%;">客户openid<br />在线状态</th>
						<th style="width:20%;">最后对话</th>
						<th style="text-align:right;">操作</th>
					</tr>
				</thead>
				<tbody>
				<?php  if(is_array($list)) { foreach($list as $row) { ?>
				<tr>
					<td><img src="<?php  echo $row['kefuavatar'];?>" width="50" height="50" style="border-radius:50px;" /> <?php  echo $row['kefunickname'];?></td>
					<td><img src="<?php  echo $row['fansavatar'];?>" width="50" height="50" style="border-radius:50px;" /> <?php  echo $row['fansnickname'];?></td>
					<td>
						<?php  echo $row['fansopenid'];?>
						<?php  if($row['kfzx'] == 1) { ?>
						<br />
						<span class="label label-success">客服在线</span>
						<?php  } ?>
					</td>
					<td>
						<?php  if($row['kefulasttime'] > 0) { ?><?php  echo date("Y-m-d H:i:s",$row['kefulasttime'])?><?php  } else { ?>无<?php  } ?>
					</td>
					<td style="text-align:right;">
						<a href="<?php  echo $this->createWebUrl('zaixian',array('op'=>'kefuxiaxian','id'=>$row['id']))?>" class="btn btn-danger btn-sm">下线</a>
					</td>
				</tr>
				<?php  } } ?>
				</tbody>
			</table>
		</div>
	</div>
	<?php  echo $pager;?>
</div>
<?php  } ?>
<script type="text/javascript">
$(function(){

})
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>