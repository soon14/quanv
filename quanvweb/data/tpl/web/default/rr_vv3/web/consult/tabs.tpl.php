<?php defined('IN_IA') or exit('Access Denied');?><style type='text/css'>

    .nav-list a {

        position: relative;

    }

    .nav-list span  {

     

    float:right;margin-right:20px;

    }

</style>
<?php if(cv('consult.news|consult.waits|consult.finishs|consult.historys|consult.all')) { ?>
<div class='menu-header'>当面咨询</div>
<ul class='nav-list'>
    <?php if(cv('consult.news')) { ?>
        <li <?php  if($_W['routes']=='consult.news') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('consult/news')?>">新的预约 <span class='text-danger news'>--</span></a>
        </li>
    <?php  } ?>
    <?php if(cv('consult.waits')) { ?>
        <li <?php  if($_W['routes']=='consult.waits') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('consult/waits')?>">待完成 <span class='text-danger waits'>--</span></a>
        </li>
    <?php  } ?>
    <?php if(cv('consult.finishs')) { ?>
        <li <?php  if($_W['routes']=='consult.finishs') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('consult/finishs')?>">已完成 <span class='text-primary finishs'>--</span></a>
        </li>
    <?php  } ?>
    <?php if(cv('consult.historys')) { ?>
        <li <?php  if($_W['routes']=='consult.historys') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('consult/historys')?>">历史记录 <span class='historys'>--</span></a>
        </li>
    <?php  } ?>
    <?php if(cv('consult.all')) { ?>
        <li <?php  if($_W['routes']=='consult.all') { ?> class="active" <?php  } ?>>
            <a href="<?php  echo webUrl('consult/all')?>">全部预约 <span class='all'>--</span></a>
        </li>
    <?php  } ?>
</ul>
<?php  } ?>
<script>
    $(function () {
        $.ajax({type: "GET",url: "<?php  echo webUrl('consult/ajaxgettotals')?>",dataType:"json",success: function(data){
                var res = data.result;
                $("span.news").text(res.news);
                $("span.waits").text(res.waits);
                $("span.finishs").text(res.finishs);
                $("span.historys").text(res.historys);
                $("span.all").text(res.all);
            }
        });
    });
</script>

