<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style type='text/css'>
    .trhead td {  background:#efefef;text-align: center}
    .trbody td {  text-align: center; vertical-align:top;border-left:1px solid #f2f2f2;overflow: hidden; font-size:12px;}
    .trorder { background:#f8f8f8;border:1px solid #f2f2f2;text-align:left;}
    .ops { border-right:1px solid #f2f2f2; text-align: center;}
</style>

<div class="page-heading">

    <h2>讲座管理</h2>
</div>

<form action="./index.php" method="get" class="form-horizontal table-search" role="form">

        <input type="hidden" name="c" value="site" />

        <input type="hidden" name="a" value="entry" />

        <input type="hidden" name="m" value="rr_vv3" />

        <input type="hidden" name="do" value="web" />

        <input type="hidden" name="r" value="lectures.list.<?php  echo $st;?>" />

        <div class="page-toolbar row m-b-sm m-t-sm">

            <div class="col-sm-4">

                <div class="input-group-btn">

			        <button class="btn btn-default btn-sm"  type="button" data-toggle='refresh'><i class='fa fa-refresh'></i></button>


                    <?php if(cv('lectures.delete')) { ?>

                        <?php  if($st!='status3') { ?>
						<button class="btn btn-default btn-sm" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('lectures/delete')?>"><i class='fa fa-trash'></i> 批量删除</button>
						<?php  } ?>
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

                    <th style="width:110px;">演讲人</th>

                    <th style="width:80px;">提交时间</th>

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
                        <?php  echo mb_substr($row['lecture_title'],0,20,'utf-8')?>
                    </td>


                    <td>
						<div>

                        	<?php  if(!empty($row['avatar'])) { ?>

                            <img src='<?php  echo tomedia($row['avatar'])?>' style='width:30px;height:30px;padding1px;border:1px solid #ccc' />

                            <?php  } ?>

                            <?php  if(strexists($row['openid'],'sns_wa_')) { ?>

                                <i class="icon icon-wxapp" data-toggle="tooltip" data-placement="top" data-original-title="小程序注册" style="color: #7586db;"></i>

                            <?php  } ?>

                            <?php  if(strexists($row['openid'],'wap_user_')||strexists($row['openid'],'sns_qq_')||strexists($row['openid'],'sns_wx_')) { ?>

                                <i class="icon icon-mobile2" data-toggle="tooltip" data-placement="top" data-original-title="<?php  if(strexists($row['openid'],'wap_user_')) { ?>手机号注册<?php  } else { ?>APP注册<?php  } ?>" style="color: #44abf7;"></i>

                            <?php  } ?>


                            <?php  if(empty($row['realname'])) { ?><?php  echo $row['nickname'];?><?php  } else { ?><?php  echo $row['realname'];?><?php  } ?>

                        </div>
					
					</td>

                    <td><?php  echo date('Y-m-d',$row['createtime'])?><br/><?php  echo date('H:i:s',$row['createtime'])?></td>

                    <td> 

						<label class='label <?php  if($st=='status0') { ?><?php  } else if($st=='status1') { ?>label-danger<?php  } else if($st=='status2') { ?>label-success<?php  } else if($st=='isshow0') { ?>label-warning<?php  } else if($st=='isshow1') { ?>label-primary<?php  } ?>'>
						<?php  if($st=='status0') { ?>待审核
						<?php  } else if($st=='status1') { ?>未通过
						<?php  } else if($st=='status2') { ?>已通过
						<?php  } else if($st=='status3') { ?>已删除
						<?php  } else if($st=='isshow0') { ?>未发布
						<?php  } else if($st=='isshow1') { ?>已发布
						<?php  } ?>
						</label>	
					
					</td>
					
					
                    <td  style="overflow:visible;">

                        <div class="btn-group btn-group-sm" >

                            <ul class="dropdown-menu-left" role="menu" style='z-index: 9999'>

                                <?php if(cv('lectures.detail')) { ?>
                                    <li><a href="<?php  echo webUrl('lectures/detail',array('id' => $row['id']));?>" title="讲座详情"><i class='fa fa-edit'></i> 讲座详情</a></li>
                                <?php  } ?>
								<?php  if($st=='status0') { ?>
									<a class="btn btn-primary btn-xs" data-toggle="ajaxPost" href="<?php  echo webUrl('lectures/post/audit', array('id' => $row['id']))?>" data-confirm="确认此讲座申请审核通过吗？">审核通过</a><br/>
									<a  class="btn btn-danger btn-xs" data-toggle="ajaxModal" href="<?php  echo webUrl('lectures/post/checked', array('id' => $row['id'],'lecture_title' => $row['lecture_title']))?>" >审核不过</a>
								<?php  } ?>
								<?php  if($st=='isshow1') { ?>
									<a  class="btn btn-danger btn-xs" data-toggle="ajaxModal" href="<?php  echo webUrl('lectures/post/goback', array('id' => $row['id'],'lecture_title' => $row['lecture_title']))?>" >退回前台重新审核</a>
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

<!--yifuyuanma-->