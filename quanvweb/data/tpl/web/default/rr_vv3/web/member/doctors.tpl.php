<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>


<div class="page-heading"> <h2>医生管理</h2> </div>

    <form action="./index.php" method="get" class="form-horizontal table-search" role="form">

        <input type="hidden" name="c" value="site" />

        <input type="hidden" name="a" value="entry" />

        <input type="hidden" name="m" value="rr_vv3" />

        <input type="hidden" name="do" value="web" />

        <input type="hidden" name="r" value="member.doctors" />

        <div class="page-toolbar row m-b-sm m-t-sm">

            <div class="col-sm-4">

                <div class="input-group-btn">

			        <button class="btn btn-default btn-sm"  type="button" data-toggle='refresh'><i class='fa fa-refresh'></i></button>

				    <!-- <?php if(cv('member.doctors.edit')) { ?>

			            <button class="btn btn-default btn-sm" type="button" data-toggle='batch' data-href="<?php  echo webUrl('member/doctors/setblack',array('isblack'=>1))?>"><i class='fa fa-user'></i> 设置黑名单</button>

				        <button class="btn btn-default btn-sm" type="button" data-toggle='batch'  data-href="<?php  echo webUrl('member/doctors/setblack',array('isblack'=>0))?>"><i class='fa fa-user-o'></i> 取消黑名单</button>

				    <?php  } ?>


                    <?php if(cv('member.doctors.delete')) { ?>

                        <button class="btn btn-default btn-sm" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('member/doctors/delete')?>"><i class='fa fa-trash'></i> 删除</button>

                    <?php  } ?> -->

			    </div>

            </div>

            <div class="col-sm-6 pull-right">


                <div class="input-group">

                    <input type="text" class="form-control input-sm"  name="realname" value="<?php  echo $_GPC['realname'];?>" placeholder="可搜索昵称/姓名/手机号"/>

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

                    <!-- <th style="width:25px;"><input type='checkbox' /></th> -->
                    <th style="width:60px;text-align:center;">排序</th>
                    <th style="width:150px;">医生信息</th>

                    <th style="width:180px;">所属医院</th>

                    <th style="width:100px;">职称</th>

                    <th style="width:100px;">科室</th>

                    <th style="width:100px;">注册时间</th>

                    <th style="width:80px;">状态</th>

                    <th style="width:80px;">是否名医</th>

                    <th style="width:100px;">操作</th>

                </tr>

            </thead>

            <tbody>

                <?php  if(is_array($list)) { foreach($list as $row) { ?>
    
                <tr rel="pop" data-title="ID: <?php  echo $row['id'];?> ">

                   	<!-- <td style="position: relative; ">

					   <input type='checkbox'   value="<?php  echo $row['id'];?>"/>

                    </td> -->
                    <td style='text-align:center;'>
                        
                        <a href='javascript:;' data-toggle='ajaxEdit' data-href="<?php  echo webUrl('member/doctors/change',array('type'=>'displayorder','id'=>$row['id']))?>" ><?php  echo $row['displayorder'];?></a>
                        
                    </td>
                    <td  >

			            <div  >

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

                    <td>
                        <?php  echo $row['hospital'];?>
                    </td>

                    <td><?php  echo $row['job'];?></td>

                    <td><?php  echo $row['parentname'];?><br/><?php  echo $row['departname'];?></td>

                    <td><?php  echo date("Y-m-d",$row['createtime'])?><br/><?php  echo date("H:i:s",$row['createtime'])?></td>

                    <td> 

                        <label class='label <?php  if($row['isaudit']==1) { ?>label-success<?php  } else { ?>label-default<?php  } ?>'>

                        <?php  if($row['isaudit']==0) { ?>未审核<?php  } else if($row['isaudit']==2) { ?>审核不过<?php  } else { ?>已审核<?php  } ?>

                        </label>
                        <br/>
					    <label class='label <?php  if($row['status']==0) { ?>label-error<?php  } else { ?>label-primary<?php  } ?>' >

						<?php  if($row['status']==1) { ?>正常<?php  } else { ?>关闭<?php  } ?>

                        </label>

					</td>
                    <td>
                        <label class='label <?php  if($row['isfamous']==0) { ?>label-error<?php  } else { ?>label-primary<?php  } ?>' >
                    <?php  if($row['isfamous'] ==1) { ?>是<?php  } else { ?>否<?php  } ?>
                        </label>
                    </td>
                    <td  style="overflow:visible;">

                        <div class="btn-group btn-group-sm" >

                            <ul class="dropdown-menu-left" role="menu" style='z-index: 9999'>

                                <?php if(cv('member.doctors.detail')) { ?>

                                    <li><a href="<?php  echo webUrl('member/doctors/detail',array('id' => $row['id']));?>" title="医生详情"><i class='fa fa-edit'></i> 医生详情</a></li>
                                    <?php  if($row['isfamous'] == 0) { ?>
                                    <a class="btn btn-primary btn-xs" data-toggle="ajaxPost" href="<?php  echo webUrl('member/doctors/setFamous', array('id' => $row['id']))?>" data-confirm="确认设置该医生为名医推荐吗？">设为名医推荐</a>
                                    <?php  } else { ?>
                                    <a class="btn btn-warning btn-xs" data-toggle="ajaxPost" href="<?php  echo webUrl('member/doctors/setNoFamous', array('id' => $row['id']))?>" data-confirm="确认设置该医生为名医推荐吗？">取消名医推荐</a>
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
