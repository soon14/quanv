<?php defined('IN_IA') or exit('Access Denied');?><style type='text/css'>

    .nav-list a {

        position: relative;

    }

    .nav-list span  {

     

    float:right;margin-right:20px;

    }

</style>
<?php if(cv('videos|videos.list.status0|videos.list.status1|videos.list.status2|videos.list.status3|videos.list.isshow0|videos.list.isshow1')) { ?>
<div class='menu-header'>视频管理</div>
<ul class='nav-list'>
    <?php if(cv('videos.list.status0')) { ?>
        <li <?php  if($_W['routes']=='videos.list.status0') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('videos/list/status0')?>">待审核 <span class='text-danger status0'>--</span></a>
        </li>
    <?php  } ?>
	<?php if(cv('videos.list.status2')) { ?>
        <li <?php  if($_W['routes']=='videos.list.status2') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('videos/list/status2')?>">未通过 <span class="status2">--</span></a>
        </li>
    <?php  } ?>
    <?php if(cv('videos.list.status1')) { ?>
        <li <?php  if($_W['routes']=='videos.list.status1') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('videos/list/status1')?>">已通过 <span class='text-primary status1'>--</span></a>
        </li>
    <?php  } ?>
    
    <?php if(cv('videos.list.status3')) { ?>
        <li <?php  if($_W['routes']=='videos.list.status3') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('videos/list/status3')?>">已删除 <span class="status3">--</span></a>
        </li>
    <?php  } ?>
    <?php if(cv('videos.list.isshow0')) { ?>
        <li <?php  if($_W['routes']=='videos.list.isshow0') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('videos/list/isshow0')?>">未发布 <span class="isshow0">--</span></a>
        </li>
    <?php  } ?>
    <?php if(cv('videos.list.isshow1')) { ?>
        <li <?php  if($_W['routes']=='videos.list.isshow1') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('videos/list/isshow1')?>">已发布 <span class='text-primary isshow1'>--</span></a>
        </li>
    <?php  } ?>
    
    <?php if(cv('videos')) { ?>
        <li <?php  if($_W['routes']=='videos') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('videos')?>">全部视频 <span class="all">--</span></a>
        </li>
    <?php  } ?>
</ul>
<?php  } ?>
<script>
    $(function () {
        $.ajax({type: "GET",url: "<?php  echo webUrl('videos/list/ajaxgettotals')?>",dataType:"json",success: function(data){
                var res = data.result;
                $("span.status0").text(res.status0);
                $("span.status1").text(res.status1);
                $("span.status2").text(res.status2);
                $("span.status3").text(res.status3);
                $("span.isshow0").text(res.isshow0);
                $("span.isshow1").text(res.isshow1);
                $("span.all").text(res.all);
            }
        });
    });
</script>
<!--yifuyuanma-->
