<?php defined('IN_IA') or exit('Access Denied');?><style type='text/css'>

    .member-list a {

        position: relative;

    }

    .member-list span  {

     

    float:right;margin-right:20px;

    }

</style>
<ul class="menu-head-top">
    <li <?php  if($_W['action']=='member') { ?> class="active" <?php  } ?>><a href="<?php  echo webUrl('member')?>">会员概述 <i class="fa fa-caret-right"></i></a></li>
</ul>

<?php if(cv('member.list|member.level|member.group|member.doctors|member.patients|member.department')) { ?>
<div class='menu-header'>会员</div>
<ul class='member-list'>
	<?php if(cv('member.list')) { ?>
    	<li <?php  if($_W['action']=='member.list') { ?> class="active" <?php  } ?>><a href="<?php  echo webUrl('member/list')?>">会员管理</a></li>
    <?php  } ?>
    <?php if(cv('member.level')) { ?>
    	<li <?php  if($_W['action']=='member.level') { ?> class="active" <?php  } ?>><a href="<?php  echo webUrl('member/level')?>">会员等级</a></li>
    <?php  } ?>
    <?php if(cv('member.group')) { ?>
    	<li <?php  if($_W['action']=='member.group') { ?> class="active" <?php  } ?>><a href="<?php  echo webUrl('member/group')?>">会员分组</a></li>
    <?php  } ?>

    <?php if(cv('member.list')) { ?>

        <li <?php  if($_W['action']=='member.auditing') { ?> class="active" <?php  } ?>><a href="<?php  echo webUrl('member/auditing')?>">待审核 <span class='text-danger isaudit'>--</span></a></li>

    <?php  } ?>
    <!-- 修改人：caifang -->
    <?php if(cv('member.doctors')) { ?>
        <li <?php  if($_W['action']=='member.doctors') { ?> class="active" <?php  } ?>><a href="<?php  echo webUrl('member/doctors')?>">医生管理</a></li>
    <?php  } ?>
    
    <?php if(cv('member.patients')) { ?>
        <li <?php  if($_W['action']=='member.patients') { ?> class="active" <?php  } ?>><a href="<?php  echo webUrl('member/patients')?>">患者管理</a></li>
    <?php  } ?>
    <?php if(cv('member.department')) { ?>
        <li <?php  if($_W['action']=='member.department') { ?> class="active" <?php  } ?>><a href="<?php  echo webUrl('member/department')?>">科室分类</a></li>
    <?php  } ?>
    <?php if(cv('member.card')) { ?>
        <?php  if(com('wxcard')) { ?>
            <li <?php  if($_W['action']=='member.group') { ?> class="active" <?php  } ?>><a href="<?php  echo webUrl('member/card')?>">微信会员卡设置</a></li>
        <?php  } ?>
    <?php  } ?>
</ul>
<?php  } ?>

<!-- <?php if(cv('member.rank')) { ?>
<div class='menu-header'>设置</div>
<ul>
    <li <?php  if($_W['action']=='member.rank') { ?> class="active" <?php  } ?>><a href="<?php  echo webUrl('member/rank')?>">排行榜</a></li>
</ul>
<?php  } ?> -->
<script>

    $(function () {

        $.ajax({type: "GET",url: "<?php  echo webUrl('member/auditing/ajaxgettotals')?>",dataType:"json",success: function(data){

                var res = data.result;

                $("span.isaudit").text(res.isaudit);

            }

        });

    });

</script>
<!--yifuyuanma-->