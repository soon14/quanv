<?php defined('IN_IA') or exit('Access Denied');?><style type="text/css">
	.left_ul span{
		float:right;
		margin-right:20px;
	}
</style>
<?php if(cv('articles|articles.list.status0|articles.list.status1|articles.list.status2|articles.list.status3|articles.list.isshow1|articles.list.isshow0')) { ?>
<div class='menu-header'>文章管理</div>
<ul class="left_ul">
    <?php if(cv('articles.list.status0')) { ?>
        <li <?php  if($_W['routes']=='articles.list.status0') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('articles/list/status0',array('status' => '0'))?>">待审核<span class="status0 text-danger">--</span></a>
        </li>
    <?php  } ?>
	<?php if(cv('articles.list.status1')) { ?>
        <li <?php  if($_W['routes']=='articles.list.status1') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('articles/list/status1',array('status' => '1'))?>">未通过<span class="status1">--</span></a>
        </li>
    <?php  } ?>
    <?php if(cv('articles.list.status2')) { ?>
        <li <?php  if($_W['routes']=='articles.list.status2') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('articles/list/status2',array('status' => '2'))?>">已通过<span class="status2">--</span></a>
        </li>
    <?php  } ?>
	<?php if(cv('articles.list.status3')) { ?>
        <li <?php  if($_W['routes']=='articles.list.status3') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('articles/list/status3',array('status' => '3'))?>">已删除<span class="status3">--</span></a>
        </li>
    <?php  } ?>

	<?php if(cv('articles.list.isshow0')) { ?>
        <li <?php  if($_W['routes']=='articles.list.isshow0') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('articles/list/isshow0',array('isshow' => '0'))?>">未发布<span class="isshow0">--</span></a>
        </li>
    <?php  } ?>
    <?php if(cv('articles.list.isshow1')) { ?>
        <li <?php  if($_W['routes']=='articles.list.isshow1') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('articles/list/isshow1',array('isshow' => '1'))?>">已发布<span class="isshow1">--</span></a>
        </li>
    <?php  } ?>
    
   
    <?php if(cv('articles')) { ?>
        <li <?php  if($_W['routes']=='articles') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('articles')?>">全部文章<span class="all">--</span></a>
        </li>
    <?php  } ?>
</ul>
<?php  } ?>

<script type="text/javascript">
$().ready(function(){
	$.ajax({
		url:'<?php  echo webUrl('articles/list/ajaxGetTotals')?>',
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