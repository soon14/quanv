<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style type='text/css'>
    .trhead td {  background:#efefef;text-align: center}
    .trbody td {  text-align: center; vertical-align:top;border-left:1px solid #f2f2f2;overflow: hidden; font-size:12px;}
    .trorder { background:#f8f8f8;border:1px solid #f2f2f2;text-align:left;}
    .ops { border-right:1px solid #f2f2f2; text-align: center;}
</style>

<div class="page-heading">

    <h2>全部预约管理</h2>
</div>

<form action="./index.php" method="get" class="form-horizontal table-search" role="form">

        <input type="hidden" name="c" value="site" />

        <input type="hidden" name="a" value="entry" />

        <input type="hidden" name="m" value="rr_vv3" />

        <input type="hidden" name="do" value="web" />

        <input type="hidden" name="r" value="consult.all" />

        <div class="page-toolbar row m-b-sm m-t-sm">

            <div class="col-sm-4">

                <div class="input-group-btn">

                  <button class="btn btn-default btn-sm"  type="button" data-toggle='refresh'><i class='fa fa-refresh'></i></button>

                  <?php if(cv('consult.all.delete')) { ?>

                    <!-- <button class="btn btn-default btn-sm" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('consult/all/delete')?>"><i class='fa fa-trash'></i> 批量删除</button> -->

                  <?php  } ?>

                </div>

            </div>

            <div class="col-sm-6 pull-right">

                <div class="input-group">

                  <input type="text" class="form-control input-sm"  name="keyword" value="<?php  echo $_GPC['keyword'];?>" placeholder="可搜索星期/费用/地址"/>

                  <span class="input-group-btn">

                    <button class="btn btn-sm btn-primary" type="submit"> 搜索</button>

                  </span>

                </div>

            </div>

        </div>

  </form>

   <table class="table table-hover table-responsive">

            <thead class="navbar-inner">

                <tr>

                    <th style="width:25px;"><input type='checkbox' /></th>

                    <th style="width:50px;">预约ID</th>

                    <th style="width:100px;">日期</th>

                    <th style="width:60px;">星期</th>

                    <th style="width:80px;">开始时间</th>

                    <th style="width:80px;">结束时间</th>

                    <th style="width:60px;">人数</th>

                    <th style="width:80px;">费用</th>

                    <th style="width:120px;">地址</th>

                    <th style="width:120px;">创建时间</th>
          
                    <th style="width:100px;">操作</th>

                </tr>

            </thead>

            <tbody>

                <?php  if(is_array($list)) { foreach($list as $row) { ?>

                <tr rel="pop" data-title="ID: <?php  echo $row['id'];?> ">

                    <td style="position: relative; ">

                        <input type='checkbox'   value="<?php  echo $row['id'];?>"/>

                    </td>

                    <td><?php  echo $row['id'];?></td>

                    <td><?php  echo $row['date'];?></td>

                    <td><?php  echo $row['week'];?></td>

                    <td><?php  echo $row['start_time'];?></td>

                    <td><?php  echo $row['end_time'];?></td>

                    <td><span style="color:blue;"><?php  echo $row['consult_nums'];?></span>/<?php  echo $row['people_nums'];?></td>

                    <td>￥<?php  echo $row['price'];?></td>

                    <td><?php  echo $row['address'];?></td>

                    <td><?php  echo $row['createtime'];?></td>

                    <td  style="overflow:visible;">

                        <div class="btn-group btn-group-sm" >

                            <ul class="dropdown-menu-left" role="menu" style='z-index: 9999'>

                                <?php if(cv('consult.detail')) { ?>

                                    <li><a href="<?php  echo webUrl('consult/detail',array('id' => $row['id']));?>" title="查看详情"><i class='fa fa-edit'></i> 查看详情</a></li>

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