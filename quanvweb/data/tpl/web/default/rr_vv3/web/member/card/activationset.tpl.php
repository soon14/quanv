<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-heading">
    <h2>会员卡激活设置 <small>修改</small></h2>
</div>
<!--<div class="alert alert-info">警告：当模板中已经添加数据后改变模板结构有可能导致无法使用！
    <br/>使用情况：
    <br/>会员资料 (<?php  if($use_flag1) { ?>正在使用<?php  } else { ?>未使用<?php  } ?>)
    <br/>分销商申请资料 (<?php  if($use_flag2) { ?>正在使用<?php  } else { ?>未使用<?php  } ?>)
    <br/>商城商品(<?php  if($datacount3>0) { ?><?php  echo $datacount3?>种商品正在使用<?php  } else { ?>未使用<?php  } ?>)</div>-->

<form action="" method="post" class="form-horizontal form-validate" enctype="multipart/form-data">
    <ul class="nav nav-arrow-next nav-tabs" id="myTab">
        <li <?php  if($_GPC['tab']=='basic' || empty($_GPC['tab'])) { ?>class="active"<?php  } ?> ><a href="#tab_basic">基本</a></li>
        <li <?php  if($_GPC['tab']=='sale') { ?>class="active"<?php  } ?> ><a href="#tab_sale">激活赠送</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane  <?php  if($_GPC['tab']=='basic' || empty($_GPC['tab'])) { ?>active<?php  } ?>" id="tab_basic"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('member/card/tpl/basic', TEMPLATE_INCLUDEPATH)) : (include template('member/card/tpl/basic', TEMPLATE_INCLUDEPATH));?></div>
        <div class="tab-pane  <?php  if($_GPC['tab']=='sale') { ?>active<?php  } ?>" id="tab_sale"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('member/card/tpl/sale', TEMPLATE_INCLUDEPATH)) : (include template('member/card/tpl/sale', TEMPLATE_INCLUDEPATH));?></div>
    </div>

    <div class="form-group"></div>

    <div class="form-group">
        <div class="col-sm-9 col-xs-12">
            <?php if( ce('member.card' ,$item) ) { ?>
            <input type="submit" value="提交" class="btn btn-primary"  />
            <?php  } ?>
            <a class="btn btn-default  btn-sm" href="<?php  echo webUrl('member/card')?>">返回</a>
        </div>
    </div>
</form>

<script language='javascript'>
    require(['bootstrap'],function(){
        $('#myTab a').click(function (e) {
            e.preventDefault();
            $('#tab').val( $(this).attr('href'));
            $(this).tab('show');
        })
    });
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

<!--yifuyuanma-->