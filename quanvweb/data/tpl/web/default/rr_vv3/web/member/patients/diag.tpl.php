<?php defined('IN_IA') or exit('Access Denied');?>
<table class="table table-hover table-responsive">

    <thead class="navbar-inner">

        <tr>

            <th style="width:100px;">姓名</th>
			
			<th style="width:120px;">类别</th>

            <th style="width:80px;">年龄</th>

            <th style="width:120px;">电话</th>

            <th style="width:120px;">是否看过医生</th>

            <th style="width:100px;">创建病情时间</th>

            <th style="width:100px;">操作</th>

        </tr>

    </thead>

    <tbody>

        <?php  if(is_array($list)) { foreach($list as $row) { ?>

        <tr rel="pop" data-title="ID: <?php  echo $row['id'];?> ">


            <td>

                <?php  echo $member['realname'];?>
                
            </td>

            <td>

                <?php  echo $row['diag_department'];?>

            </td>

            <td>

                <?php  echo $row['age'];?>

            </td>

            <td>

                <?php  echo $member['mobile'];?>

            </td>

            <td>

                <?php  if($row['diag_doctor']==0) { ?>否<?php  } else if($row['diag_doctor']==1) { ?>是<?php  } ?>

            </td>

            <td><?php  echo date("Y-m-d",$row['createtime'])?><br/><?php  echo date("H:i:s",$row['createtime'])?></td>

            <td  style="overflow:visible;">

                <div class="btn-group btn-group-sm" >

                    <ul class="dropdown-menu-left" role="menu" style='z-index: 9999'>

                        <?php if(cv('member.patients.detail')) { ?>

                            <li><a href="<?php  echo webUrl('member/patients/detail',array('patientsid' => $info['id'],'diagid' => $row['id'],'type' => '1'));?>" title="查看病情详情"><i class='fa fa-edit'></i> 查看病情详情</a></li>

                        <?php  } ?>

                        <?php if(cv('member.patients.delete')) { ?>

                            <li><a  data-toggle='ajaxRemove'  href="<?php  echo webUrl('member/patients/delete',array('id' => $row['id']));?>" title='删除病情信息' data-confirm="确定要删除该病情信息吗？"><i class='fa fa-remove'></i> 删除病情信息</a></li>

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

</script>


