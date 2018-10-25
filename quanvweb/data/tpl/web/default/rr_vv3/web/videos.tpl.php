<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style type='text/css'>
    .trhead td {  background:#efefef;text-align: center}
    .trbody td {  text-align: center; vertical-align:top;border-left:1px solid #f2f2f2;overflow: hidden; font-size:12px;}
    .trorder { background:#f8f8f8;border:1px solid #f2f2f2;text-align:left;}
    .ops { border-right:1px solid #f2f2f2; text-align: center;}
</style>

<div class="page-heading">

    <h2>视频管理</h2>

</div>
<form action="./index.php" method="get" class="form-horizontal table-search" role="form">

        <input type="hidden" name="c" value="site" />

        <input type="hidden" name="a" value="entry" />

        <input type="hidden" name="m" value="rr_vv3" />

        <input type="hidden" name="do" value="web" />

        <input type="hidden" name="r" value="videos" />

        <div class="page-toolbar row m-b-sm m-t-sm">

            <div class="col-sm-4">

                <div class="input-group-btn">

			        <button class="btn btn-default btn-sm"  type="button" data-toggle='refresh'><i class='fa fa-refresh'></i></button>

                    <!-- <?php if(cv('videos.delete')) { ?>

                        <?php  if($st!='status3') { ?>
						<button class="btn btn-default btn-sm" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('videos/list/delete')?>"><i class='fa fa-trash'></i> 批量删除</button>
						<?php  } ?>
                    <?php  } ?> -->

			    </div>

            </div>

            <div class="col-sm-6 pull-right">

                <div class="input-group">

                    <input type="text" class="form-control input-sm"  name="keyword" value="<?php  echo $_GPC['keyword'];?>" placeholder="可搜索视频名称/视频简介"/>

    				<span class="input-group-btn">

                        <button class="btn btn-sm btn-primary" type="submit"> 搜索</button>

    				</span>

                </div>

            </div>

        </div>

  </form>

<?php  if(count($list) > 0) { ?>

	<table class="table table-hover table-responsive">

            <thead class="navbar-inner">

                <tr>

                    <th style="width:25px;"><input type='checkbox' /></th>

                    <th style="width:50px;">排序</th>

                    <th style="width:120px;">提交人</th>

                    <th style="width:160px;">视频名称</th>

                    <th style="width:110px;">视频简介</th>

                    <th style="width:100px;">点播费用</th>

                    <th style="width:110px;">提交时间</th>

                    <th style="width:80px;">当前状态</th>
					
                    <th style="width:100px;">操作</th>

                </tr>

            </thead>

            <tbody>

                <?php  if(is_array($list)) { foreach($list as $row) { ?>

                <tr rel="pop" data-title="ID: <?php  echo $row['id'];?> ">

                   	<td style="position: relative; ">

					   <input type='checkbox'   value="<?php  echo $row['id'];?>"/>

                    </td>

                    <td><?php  echo $row['displayorder'];?></td>

                    <td>
						<div>

                        	<?php  if(!empty($row['avatar'])) { ?>

                            <img src='<?php  echo tomedia($row['avatar'])?>' style='width:30px;height:30px;padding1px;border:1px solid #ccc' />

                            <?php  } ?>

                            <?php  if(strexists($row['openid'],'sns_wa_')) { ?>

                                <i class="icon icon-wxapp" data-toggle="tooltip" data-placement="top" data-original-title="小程序注册" style="color: #7586db;"></i>

                            <?php  } ?>


                            <?php  if(empty($row['realname'])) { ?><?php  echo $row['nickname'];?><?php  } else { ?><?php  echo $row['realname'];?><?php  } ?>

                        </div>
					
					</td>

					<td><?php  echo $row['videoname'];?></td>

					<td><?php  echo $row['content'];?></td>

					<td>￥<?php  echo $row['money'];?></td>

                    <td><?php  echo $row['createtime'];?></td>

                    <td> 

						<label class='label <?php  if($row['status'] == 0) { ?><?php  } else if($row['status'] == 2) { ?>label-danger<?php  } else if($row['status'] == 1) { ?>label-success<?php  } ?>'>
							<?php  if($row['status'] == 0) { ?>待审核
							<?php  } else if($row['status'] == 2) { ?>未通过
							<?php  } else if($row['status'] == 1) { ?>已通过
							<?php  } ?>
						</label><br/>
						<label class='label <?php  if($row['isshow'] == 0) { ?>label-warning<?php  } else if($row['isshow'] == 1) { ?>label-primary<?php  } ?>'>
							<?php  if($row['isshow'] == 0) { ?>未发布
							<?php  } else if($row['isshow'] == 1) { ?>已发布
							<?php  } ?>
						</label>			

					</td>

                    <td  style="overflow:visible;">

                        <div class="btn-group btn-group-sm" >

                            <ul class="dropdown-menu-left" role="menu" style='z-index: 9999'>

                                <?php if(cv('videos.detail')) { ?>

                                    <li>

                                    	<a href="<?php  echo webUrl('videos/detail',array('id' => $row['id']));?>" title="查看详情"><i class='fa fa-edit'></i> 查看详情</a>

                                    </li>

                                <?php  } ?>

                                <?php if(cv('videos.edit')) { ?>
                                    
                                    <?php  if($row['status']==0) { ?>
                                        <li>
                                        	<a class="btn btn-primary btn-xs" data-toggle="ajaxPost" href="<?php  echo webUrl('videos/enabled', array('id' => $row['id'],'status' => 1))?>" data-confirm="确认此视频审核通过吗？">审核通过</a>
                                        </li>
                                        
                                        <li>
											<a  class="btn btn-danger btn-xs" data-toggle="ajaxModal" href="<?php  echo webUrl('videos/checked', array('id' => $row['id'],'videoname' => $row['videoname']))?>" >审核不过</a>
                                        </li>

                                    <?php  } ?>

                                <?php  } ?>

                            </ul>

                        </div>

                    </td>

                </tr>

                <?php  } } ?>

            </tbody>

        </table>

        <?php  echo $pager;?>

<?php  } else { ?>
<div class='panel panel-default'>
    <div class='panel-body' style='text-align: center;padding:30px;'>
        暂时没有任何视频!
    </div>
</div>
<?php  } ?>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

<!--yifuyuanma-->