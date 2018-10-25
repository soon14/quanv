<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>



<style type='text/css' xmlns="http://www.w3.org/1999/html">

    .dd-handle { height: 40px; line-height: 30px}

    .dd-list { width:860px;}

</style>





<div class="page-heading"> 

    <span class="pull-right">

        <button type="button" id='btnExpand' class="btn btn-default" data-action='expand'><i class='fa fa-angle-up'></i> 折叠所有</button>  

        <?php if(cv('member.department.add')) { ?>

        <a href="<?php  echo webUrl('member/department/add')?>" class="btn btn-primary"><i class="fa fa-plus"></i> 添加新科室</a>

        <?php  } ?>



    </span>

    <h2>科室分类</h2>

</div>

<form action="" method="post" class="form-validate">



    <div class="dd" id="div_nestable">

        <ol class="dd-list">



            <?php  if(is_array($department)) { foreach($department as $row) { ?>

            <?php  if(empty($row['parentid'])) { ?>

            <li class="dd-item full" data-id="<?php  echo $row['id'];?>">



                <div class="dd-handle" >

                    [ID: <?php  echo $row['id'];?>] <?php  echo $row['name'];?>

                    <span class="pull-right">


                        <div class='label <?php  if($row['isshow']==1) { ?>label-success<?php  } else { ?>label-default<?php  } ?>'>

                             <?php  if($row['isshow']==1) { ?>显示<?php  } else { ?>隐藏<?php  } ?></div>



                               <!-- <?php  if(intval($row['level'])>1 ) { ?><?php if(cv('member.department.add')) { ?><a class='btn btn-default btn-sm' href="<?php  echo webUrl('member/department/add', array('parentid' => $row['id']))?>" title='添加子分类' ><i class="fa fa-plus"></i></a><?php  } ?><?php  } ?> -->
                               <?php if(cv('member.department.add')) { ?><a class='btn btn-default btn-sm' href="<?php  echo webUrl('member/department/add', array('parentid' => $row['id']))?>" title='添加子分类' ><i class="fa fa-plus"></i></a><?php  } ?>
		
		
                               <?php if(cv('member.department.edit|member.department.view')) { ?>

                               <a class='btn btn-default btn-sm' href="<?php  echo webUrl('member/department/edit', array('id' => $row['id']))?>" title="<?php if(cv('member.department.edit')) { ?>修改<?php  } else { ?>查看<?php  } ?>" ><i class="fa fa-edit"></i></a>

                               <?php  } ?>

                               <?php if(cv('member.department.delete')) { ?><a class='btn btn-default btn-sm' data-toggle='ajaxPost' href="<?php  echo webUrl('member/department/delete', array('id' => $row['id']))?>" data-confirm='确认删除此分类吗？'><i class="fa fa-remove"></i></a><?php  } ?>

                        </span>

                    </div>

                    <?php  if(count($children[$row['id']])>0) { ?>



                    <ol class="dd-list">

                        <?php  if(is_array($children[$row['id']])) { foreach($children[$row['id']] as $child) { ?>

                        <li class="dd-item full" data-id="<?php  echo $child['id'];?>">

                            <div class="dd-handle" style="width:100%;">

                                <img src="<?php  echo tomedia($child['thumb']);?>" width='30' height="30" onerror="$(this).remove()" style='padding:1px;border: 1px solid #ccc;float:left;' /> &nbsp;

                                [ID: <?php  echo $child['id'];?>] <?php  echo $child['name'];?>

                                <span class="pull-right">

                                    <div class='label <?php  if($child['isshow']==1) { ?>label-success<?php  } else { ?>label-default<?php  } ?>'>

                                         <?php  if($child['isshow']==1) { ?>显示<?php  } else { ?>隐藏<?php  } ?></div>


                                           <?php if(cv('member.department.edit|member.department.view')) { ?><a class='btn btn-default btn-sm' href="<?php  echo webUrl('member/department/edit', array('id' => $child['id']))?>" title="<?php if(cv('member.department.edit')) { ?>修改<?php  } else { ?>查看<?php  } ?>" ><i class="fa fa-edit"></i></a><?php  } ?>

                                           <?php if(cv('member.department.delete')) { ?> <a class='btn btn-default btn-sm'  data-toggle='ajaxPost'  href="<?php  echo webUrl('member/department/delete', array('id' => $child['id']))?>" data-confirm="确认删除此分类吗？"><i class="fa fa-remove"></i></a><?php  } ?>

                                    </span>

                                </div>

                                <?php  if(count($children[$child['id']])>0 && intval($_W['shopset']['department']['level'])==3) { ?>



                                <ol class="dd-list"  style='width:100%;'>

                                    <?php  if(is_array($children[$child['id']])) { foreach($children[$child['id']] as $third) { ?>

                                    <li class="dd-item" data-id="<?php  echo $third['id'];?>">

                                        <div class="dd-handle">

                                            <img src="<?php  echo tomedia($third['thumb']);?>" width='30' height="30" onerror="$(this).remove()" style='padding:1px;border: 1px solid #ccc;float:left;' /> &nbsp;

                                            [ID: <?php  echo $third['id'];?>] <?php  echo $third['name'];?>

                                            <span class="pull-right">

						 <div class='label <?php  if($third['isshow']==1) { ?>label-success<?php  } else { ?>label-default<?php  } ?>'>

											<?php  if($third['isshow']==1) { ?>显示<?php  } else { ?>隐藏<?php  } ?></div>

												

                                                <?php if(cv('member.department.edit|member.department.view')) { ?><a class='btn btn-default btn-sm' href="<?php  echo webUrl('member/department/edit', array('id' => $third['id']))?>" title="<?php if(cv('member.department.edit')) { ?>修改<?php  } else { ?>查看<?php  } ?>" ><i class="fa fa-edit"></i></a><?php  } ?>

                                                <?php if(cv('member.department.delete')) { ?><a class='btn btn-default btn-sm'  data-toggle='ajaxPost'  href="<?php  echo webUrl('member/department/delete', array('id' => $third['id']))?>" data-confirm="确认删除此分类吗？"><i class="fa fa-remove"></i></a><?php  } ?>

                                            </span>

                                        </div>

                                    </li>

                                    <?php  } } ?>

                                </ol>

                                <?php  } ?>

                            </li>

                            <?php  } } ?>

                        </ol>

                        <?php  } ?>



                    </li>

                    <?php  } ?>

                    <?php  } } ?>



        </ol>


    </div>





</form>



        <script language='javascript'>

            myrequire(['jquery.nestable'], function () {



                $('#btnExpand').click(function () {

                    var action = $(this).data('action');

                    if (action === 'expand') {

                        $('#div_nestable').nestable('collapseAll');

                        $(this).data('action', 'collapse').html('<i class="fa fa-angle-down"></i> 展开所有');



                    } else {

                        $('#div_nestable').nestable('expandAll');

                        $(this).data('action', 'expand').html('<i class="fa fa-angle-up"></i> 折叠所有');

                    }

                })

                // var depth = <?php  echo intval($_W['shopset']['category']['level'])?>;

                // if (depth <= 0) {

                //     depth = 2;

                // }

                // $('#div_nestable').nestable({maxDepth: depth});



                $('.dd-item').addClass('full');



                $(".dd-handle a,.dd-handle div").mousedown(function (e) {



                    e.stopPropagation();

                });

                var $expand = false;

                $('#nestableMenu').on('click', function (e)

                {

                    if ($expand) {

                        $expand = false;

                        $('.dd').nestable('expandAll');

                    } else {

                        $expand = true;

                        $('.dd').nestable('collapseAll');

                    }

                });



                $('form').submit(function(){

                    var json = window.JSON.stringify($('#div_nestable').nestable("serialize"));

                    $(':input[name=datas]').val(json);

                });



            })

        </script>



        <?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>





