<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>


<div class="page-heading"> <h2>讲座管理</h2> </div>

    <form action="./index.php" method="get" class="form-horizontal table-search" role="form">

        <input type="hidden" name="c" value="site" />

        <input type="hidden" name="a" value="entry" />

        <input type="hidden" name="m" value="rr_vv3" />

        <input type="hidden" name="do" value="web" />

        <input type="hidden" name="r" value="lectures" />

        <div class="page-toolbar row m-b-sm m-t-sm">

            <div class="col-sm-4">

                <div class="input-group-btn">

			        <button class="btn btn-default btn-sm"  type="button" data-toggle='refresh'><i class='fa fa-refresh'></i></button>


                    <?php if(cv('lectures.delete')) { ?>

                        <button class="btn btn-default btn-sm" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('lectures/delete')?>"><i class='fa fa-trash'></i> 批量删除</button>
						
                    <?php  } ?>

			    </div>

            </div>

            <div class="col-sm-6 pull-right">


                <div class="input-group">

                    <input type="text" class="form-control input-sm"  name="keyword" value="<?php  echo $_GPC['keyword'];?>" placeholder="可搜索讲座标题/简介/主讲人"/>
					
    				<span class="input-group-btn">


                        <button class="btn btn-sm btn-primary" type="submit"> 搜索</button>

                        <button type="submit" name="export" value="1" class="btn btn-success btn-sm">导出</button>

    				</span>

                </div>

            </div>

        </div>

  </form>


        <table class="table table-hover table-responsive">

            <thead class="navbar-inner">

                <tr>

                    <th style="width:25px;"><input type='checkbox' /></th>

                    <th style="width:60px;">排序</th>

                    <th style="width:200px;">讲座标题</th>

                    <th style="width:110px;">主讲人</th>

                    <th style="width:100px;">演讲时间</th>

                    <th style="width:80px;">当前状态</th>
					
                    <th style="width:100px;">查看</th>

                </tr>

            </thead>

            <tbody>

                <?php  if(is_array($list)) { foreach($list as $row) { ?>

                <tr rel="pop" data-title="ID: <?php  echo $row['id'];?> ">

                   	<td style="position: relative; ">

					   <input type='checkbox'   value="<?php  echo $row['id'];?>"/>

                    </td>

                    <td><?php  echo $row['id'];?></td>

                    <td>
                        <?php  echo $row['lecture_title'];?>
                    </td>


                    <td>
						<?php  if(!(empty($row['lecture_author']))) { ?><?php  echo $row['lecture_author']?>
						<?php  } else if(!(empty($row['realname']))) { ?><?php  echo $row['realname']?>
						<?php  } else if(!(empty($row['nickname']))) { ?><?php  echo $row['nickname']?>
						<?php  } ?>
					
					</td>

                    <td><?php  echo date("Y-m-d",$row['createtime'])?><br/><?php  echo date("H:i:s",$row['createtime'])?></td>

                    <td> 
						<label class='label <?php  if($row['status']==0) { ?><?php  } else if($row['status']==1) { ?>label-danger<?php  } else if($row['status']==2) { ?>label-success<?php  } ?>' >
						<?php  if($row['status']==0) { ?>待审核
						<?php  } else if($row['status']==1) { ?>不通过
						<?php  } else if($row['status']==2) { ?>已通过
						<?php  } else if($row['status']==3) { ?>已删除
						<?php  } ?>
						<?php  if($row['status']!=3) { ?>
                        </label><br/>
						
					    <label class='label <?php  if($row['isshow']==0) { ?>label-warning<?php  } else if($row['isshow']==1) { ?>label-primary<?php  } ?>' >
						<?php  if($row['isshow']==0) { ?>未发布<?php  } else if($row['isshow']==1) { ?>已发布<?php  } ?>
                        </label>
						<?php  } ?>
						
					</td>

                    <td  style="overflow:visible;">

                        <div class="btn-group btn-group-sm" >

                            <ul class="dropdown-menu-left" role="menu" style='z-index: 9999'>

                                <?php if(cv('lectures.detail')) { ?>

                                    <li><a href="<?php  echo webUrl('lectures/detail',array('id' => $row['id']));?>" title="讲座详情"><i class='fa fa-edit'></i> 讲座详情</a></li>

                                <?php  } ?>
								<?php  if($row['status']==0) { ?>
									<a class="btn btn-primary btn-xs" data-toggle="ajaxPost" href="<?php  echo webUrl('lectures/post/audit', array('id' => $row['id']))?>" data-confirm="确认此讲座申请审核通过吗？">审核通过</a><br/>
									<a  class="btn btn-danger btn-xs" data-toggle="ajaxModal" href="<?php  echo webUrl('lectures/post/checked', array('id' => $row['id'],'lecture_title' => $row['lecture_title']))?>" >审核不过</a>
								<?php  } ?>

                            </ul>

                        </div>

                    </td>

                </tr>

                <?php  } } ?>

            </tbody>

        </table>

        <?php  echo $pager;?>

		<script language="javascript">

			<?php  if($opencommission) { ?>

			    require(['bootstrap', 'jquery', 'jquery.ui'], function (bs, $, $) {

                    $("[rel=pop]").popover({

                        trigger:'manual',

                        placement : 'left', 

                        title : $(this).data('title'),

                        html: 'true', 

                        content : $(this).data('content'),

                        animation: false

                    }).on("mouseenter", function () {

                        var _this = this;

                        $(this).popover("show"); 

                        $(this).siblings(".popover").on("mouseleave", function () {

                            $(_this).popover('hide');

                        });

                    }).on("mouseleave", function () {

                        var _this = this;

                        setTimeout(function () {

                            if (!$(".popover:hover").length) {

                                $(_this).popover("hide")

                            }

                        }, 100);

                    });

            	});

        	<?php  } ?>   

        </script>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

