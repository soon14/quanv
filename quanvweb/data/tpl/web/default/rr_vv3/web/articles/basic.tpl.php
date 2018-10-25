<?php defined('IN_IA') or exit('Access Denied');?><div class="form-group">

    <label class="col-sm-2 control-label">文章作者</label>

    <div class="col-sm-9 col-xs-12">

        <img src='<?php  echo $member['avatar'];?>' style='width:50px;height:50px;padding:1px;border:1px solid #ccc' />

        <?php  if(strexists($member['openid'],'sns_wa_')) { ?>

            <i class="icon icon-wxapp" data-toggle="tooltip" data-placement="top" data-original-title="小程序注册" style="color: #7586db;"></i>

        <?php  } ?>

        <?php  if(strexists($member['openid'],'wap_user_')||strexists($member['openid'],'sns_qq_')||strexists($member['openid'],'sns_wx_')) { ?>

            <i class="icon icon-mobile2" data-toggle="tooltip" data-placement="top" data-original-title="<?php  if(strexists($member['openid'],'wap_user_')) { ?>手机号注册<?php  } else { ?>APP注册<?php  } ?>" style="color: #44abf7;"></i>

        <?php  } ?>

        <?php  if(empty($member['realname'])) { ?><?php  echo $member['nickname'];?><?php  } else { ?><?php  echo $member['realname'];?><?php  } ?>

    </div>

</div>



<div class="form-group">

    <label class="col-sm-2 control-label">文章标题</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('articles.edit')) { ?>

        <input type="text" name="article_title" class="form-control" value="<?php  echo $info['article_title'];?>" />

        <?php  } else { ?>

        <div class='form-control-static'><?php  echo $info['article_title'];?></div>

        <?php  } ?>

    </div>

</div>

<div class="form-group">

        <label class="col-sm-2 control-label">文章封面</label>
		
        <div class="col-sm-9 col-xs-12">    
            <?php  echo tpl_form_field_image('cover_url',$info['cover_url'])?>
        </div>
</div>


<div class="form-group">

    <label class="col-sm-2 control-label">文章简介</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('articles.edit')) { ?>

        <textarea name="article_introduction" style="height:100px;" class='form-control'><?php  echo $info['article_introduction'];?></textarea>

        <?php  } else { ?>

        <div class='form-control-static'><?php  echo $info['article_introduction'];?></div>

        <?php  } ?>

    </div>

</div>



<div class="form-group">

        <label class="col-sm-2 control-label">文章内容</label>
		
        <div class="col-sm-9 col-xs-12">    
            <?php  echo tpl_ueditor('article_content',$info['article_content'])?>
        </div>
</div>


<div class="form-group">

    <label class="col-sm-2 control-label">文章浏览数</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('articles.edit')) { ?>

        <input type="text" name="click_nums" class="form-control" value="<?php  echo $info['click_nums'];?>" />

        <?php  } else { ?>

        <div class='form-control-static'><?php  echo $info['click_nums'];?></div>

        <?php  } ?>

    </div>

</div>

<div class="form-group">

    <label class="col-sm-2 control-label">文章转发数</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('articles.edit')) { ?>

        <input type="text" name="turn_nums" class="form-control" value="<?php  echo $info['turn_nums'];?>" />

        <?php  } else { ?>

        <div class='form-control-static'><?php  echo $info['turn_nums'];?></div>

        <?php  } ?>

    </div>

</div>

<div class="form-group">

    <label class="col-sm-2 control-label">文章价格</label>

    <div class="col-sm-5 col-xs-12">

   
		<div class="input-group">
		  <div class="input-group-addon">￥</div>
		  <input type="text" name="money" class="form-control"  placeholder="价格" value="<?php  if($info['money']>0) { ?><?php  echo sprintf("%.2f",$info['money'])?><?php  } ?>">
		  <div class="input-group-addon">元</div>
		</div>

    </div>

</div>


<div class="form-group">

    <label class="col-sm-2 control-label">文章标签</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('articles.edit')) { ?>

        <input type="text" name="tag" class="form-control" value="<?php  echo $info['tag'];?>" />

        <?php  } else { ?>

        <div class='form-control-static'><?php  echo $info['tag'];?></div>

        <?php  } ?>

    </div>

</div>



<div class="form-group">

    <label class="col-sm-2 control-label">审核状态</label>

    <div class="col-sm-9 col-xs-12">

		<div class='form-control-static'>
			<label class='label <?php  if($info['status'] == 0) { ?><?php  } else if($info['status'] == 1) { ?>label-danger<?php  } else if($info['status'] == 3) { ?><?php  } else if($info['status'] == 2) { ?>label-success<?php  } ?>'>
			<?php  if($info['status'] == 0) { ?>待审核
			<?php  } else if($info['status'] == 1) { ?>未通过
			<?php  } else if($info['status'] == 2) { ?>已通过
			<?php  } else if($info['status'] == 3) { ?>已删除
			<?php  } ?>
			</label>
		</div>

	</div>

</div>

<div class="form-group">

    <label class="col-sm-2 control-label">发布状态</label>

    <div class="col-sm-9 col-xs-12">

        <div class='form-control-static'>
		<label class='label <?php  if($info['isshow'] == 0) { ?>label-warning<?php  } else if($info['isshow'] == 1) { ?>label-primary<?php  } ?>'>
		<?php  if($info['isshow'] == 0) { ?>未发布
		<?php  } else if($info['isshow'] == 1) { ?>已发布
		<?php  } ?>
		</label>
		</div>

    </div>

</div>




<div class="form-group">

    <label class="col-sm-2 control-label">提交时间</label>

    <div class="col-sm-9 col-xs-12">

        <div class='form-control-static'><?php  echo date("Y-m-d H:i:s",$info['createtime'])?></div>

    </div>

</div>

<div class="form-group">

    <label class="col-sm-2 control-label">发布时间</label>

    <div class="col-sm-9 col-xs-12">

        <div class='form-control-static'><?php  if(!(empty($info['releasetime']))) { ?><?php  echo date("Y-m-d H:i:s",$info['releasetime'])?><?php  } else { ?>该文章暂未发布<?php  } ?></div>

    </div>

</div>



<script type="text/javascript">
    
    $(function () {

        $(".btn-maxcredit").unbind('click').click(function () {

            var val = $(this).val();

            if(val==1){

                $(".maxcreditinput").css({'display':'inline-block'});

            }else{

                $(".maxcreditinput").css({'display':'none'});

            }

        });
        

    })


</script>

