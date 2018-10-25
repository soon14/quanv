<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>


<div class="page-heading"> <h2>文章管理</h2> </div>

    <form action="./index.php" method="get" class="form-horizontal table-search" role="form">

        <input type="hidden" name="c" value="site" />

        <input type="hidden" name="a" value="entry" />

        <input type="hidden" name="m" value="rr_vv3" />

        <input type="hidden" name="do" value="web" />

        <input type="hidden" name="r" value="articles" />

        <div class="page-toolbar row m-b-sm m-t-sm">

            <div class="col-sm-4">

                <div class="input-group-btn">

			        <button class="btn btn-default btn-sm"  type="button" data-toggle='refresh'><i class='fa fa-refresh'></i></button>

				    <!-- <?php if(cv('articles.doctors.edit')) { ?> -->

			            <!-- <button class="btn btn-default btn-sm" type="button" data-toggle='batch' data-href="<?php  echo webUrl('articles/setblack',array('isblack'=>1))?>"><i class='fa fa-user'></i> 设置黑名单</button> -->

				        <!-- <button class="btn btn-default btn-sm" type="button" data-toggle='batch'  data-href="<?php  echo webUrl('articles/setblack',array('isblack'=>0))?>"><i class='fa fa-user-o'></i> 取消黑名单</button> -->

				    <!-- <?php  } ?> -->


                    <?php if(cv('articles.delete')) { ?>

                        <button class="btn btn-default btn-sm" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('articles/delete')?>"><i class='fa fa-trash'></i> 批量删除</button>
						
                    <?php  } ?>

			    </div>

            </div>

            <div class="col-sm-6 pull-right">


                <div class="input-group">

                    <input type="text" class="form-control input-sm"  name="keyword" value="<?php  echo $_GPC['keyword'];?>" placeholder="可搜索文章标题/内容/作者"/>
					<!-- <?php  if($_GPC['status']!=null) { ?> -->
					<!-- <input type="hidden" name="status" value="<?php  echo $_GPC['status'];?>" /> -->
					<!-- <?php  } ?> -->
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

                    <th style="width:200px;">文章标题</th>

                    <th style="width:110px;">作者</th>

                    <th style="width:100px;">发布时间</th>

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
                        <?php  echo mb_substr($row['article_title'],0,10,'utf-8')?>......
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


                            <?php  if(empty($row['nickname'])) { ?>未更新<?php  } else { ?><?php  echo $row['nickname'];?><?php  } ?>

                        </div>
					
					</td>

                    <td><?php  echo date("Y-m-d",$row['createtime'])?><br/><?php  echo date("H:i:s",$row['createtime'])?></td>

                    <td> 

					    <label class='label <?php  if($row['status']==1) { ?>label-warning'<?php  } else if($row['status']==2) { ?>label-danger'<?php  } else if($row['status']==3) { ?>label-info'<?php  } else if($row['status']==4) { ?>label-primary<?php  } ?>' >

						<?php  if($row['status']==0) { ?>待审核<?php  } else if($row['status']==1) { ?>已通过<?php  } else if($row['status']==2) { ?>未通过<?php  } else if($row['isshow']==0) { ?>未发布<?php  } else if($row['isshow']==1) { ?>已发布<?php  } else if($row['status']==3) { ?>已删除<?php  } ?>

                        </label>
						<!-- <?php  if($row['status']==0) { ?><br> -->
						<!-- <label class=''><a class="btn btn-info btn-xs" data-toggle="ajaxPost" href="<?php  echo webUrl('articles/post/audit',array('id' => $row['id']))?>" data-confirm="确认审核此文章吗？">审核通过</a> -->
						<!-- </label><br> -->
						<!-- <label class=''><a class="btn btn-danger btn-xs" data-toggle="ajaxPost" href="<?php  echo webUrl('articles/post/noaudit',array('id' => $row['id']))?>" data-confirm="确认审核此文章吗？">审核不通过</a> -->
						<!-- </label> -->
						<!-- <?php  } else if($row['status']==1) { ?> -->
						<!-- <br> -->
						<!-- <label class=''><a class="btn btn-info btn-xs" data-toggle="ajaxPost" href="<?php  echo webUrl('articles/post/audit',array('id' => $row['id']))?>" data-confirm="确认审核此文章吗？">审核通过</a> -->
						<!-- </label> -->
						<!-- <?php  } else if($row['status']==2) { ?> -->
						<!-- <br> -->
						<!-- <label class=''><a class="btn btn-danger btn-xs" data-toggle="ajaxPost" href="<?php  echo webUrl('articles/post/noaudit',array('id' => $row['id']))?>" data-confirm="确认审核此文章吗？">审核不通过</a> -->
						<!-- </label> -->
						<!-- <?php  } else if($row['status']==3) { ?> -->
						<!-- <br> -->
						<!-- <label class=''><a class="btn btn-default btn-xs" data-toggle="ajaxPost" href="<?php  echo webUrl('articles/post/delete',array('id' => $row['id']))?>" data-confirm="确认删除此文章吗？">删除文章</a> -->
						<!-- </label> -->
						<!-- <?php  } else if($row['isshow']==0) { ?> -->
						<!-- <br> -->
						<!-- <label class=''><a class="btn btn-danger btn-xs" data-toggle="ajaxPost" href="<?php  echo webUrl('articles/post/release',array('id' => $row['id']))?>" data-confirm="确认重新发布此文章吗？发布后为已发布状态">重新发布</a> -->
						<!-- </label> -->
						<!-- <?php  } ?> -->
					</td>

                    <td  style="overflow:visible;">

                        <div class="btn-group btn-group-sm" >

                            <ul class="dropdown-menu-left" role="menu" style='z-index: 9999'>

                                <?php if(cv('articles.detail')) { ?>

                                    <li><a href="<?php  echo webUrl('articles/detail',array('id' => $row['id']));?>" title="文章详情"><i class='fa fa-edit'></i> 文章详情</a></li>

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

