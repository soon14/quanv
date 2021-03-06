<?php defined('IN_IA') or exit('Access Denied');?><style type="text/css">
	.left_ul span{
		float:right;
		margin-right:20px;
	}
</style>

<?php if(cv('lectures|lectures.list.status0|lectures.list.status1|lectures.list.status2|lectures.list.status3')) { ?>
<div class='menu-header'>线下讲座</div>
<ul class="left_ul">
    <?php if(cv('lectures.list.status0')) { ?>
        <li <?php  if($_W['routes']=='lectures.list.status0') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('lectures/list/status0')?>">待审核<span class="status0 text-danger">--</span></a>
        </li>
    <?php  } ?>
    <?php if(cv('lectures.list.status1')) { ?>
        <li <?php  if($_W['routes']=='lectures.list.status1') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('lectures/list/status1')?>">未通过<span class="status1">--</span></a>
        </li>
    <?php  } ?>
    <?php if(cv('lectures.list.status2')) { ?>
        <li <?php  if($_W['routes']=='lectures.list.status2') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('lectures/list/status2')?>">已通过<span class="status2">--</span></a>
        </li>
    <?php  } ?>
	<?php if(cv('lectures.list.status2')) { ?>
        <li <?php  if($_W['routes']=='lectures.list.status3') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('lectures/list/status3')?>">已删除<span class="status3">--</span></a>
        </li>
    <?php  } ?>
    <?php if(cv('lectures.list.status3')) { ?>
        <li <?php  if($_W['routes']=='lectures.list.isshow0') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('lectures/list/isshow0')?>">未发布<span class="isshow0">--</span></a>
        </li>
    <?php  } ?>
	<?php if(cv('lectures.list.status3')) { ?>
        <li <?php  if($_W['routes']=='lectures.list.isshow1') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('lectures/list/isshow1')?>">已发布<span class="isshow1 text-primary">--</span></a>
        </li>
    <?php  } ?>
    <?php if(cv('lectures')) { ?>
        <li <?php  if($_W['routes']=='lectures') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('lectures')?>">全部讲座<span class="all">--</span></a>
        </li>
    <?php  } ?>
</ul>
<?php  } ?>

<script type="text/javascript">
$().ready(function(){
	$.ajax({
		url:'<?php  echo webUrl('lectures/list/ajaxGetTotals')?>',
		type:'get',
		dataType:'json',
		success:function(res){
			var res = res.result;
			$(".status0").text(res.status0);
			$(".status1").text(res.status1);
			$(".status2").text(res.status2);
			$(".status3").text(res.status3);
			$(".isshow0").text(res.isshow0);
			$(".isshow1").text(res.isshow1);
			$(".all").text(res.all);
			
		},
	});

});
</script>

