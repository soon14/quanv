<?php defined('IN_IA') or exit('Access Denied');?>
<div class="form-group">

    <label class="col-sm-2 control-label">姓名</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('member.patients.edit')) { ?>

        <input type="text" name="name" class="form-control" value="<?php  echo $list['name'];?>" />

        <?php  } else { ?>

        <div class='form-control-static'><?php  echo $list['name'];?></div>

        <?php  } ?>

    </div>

</div>

<div class="form-group">

    <label class="col-sm-2 control-label">年龄</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('member.patients.edit')) { ?>

        <input type="text" name="age" class="form-control" value="<?php  echo $list['age'];?>" />

        <?php  } else { ?>

        <div class='form-control-static'><?php  echo $list['age'];?></div>

        <?php  } ?>

    </div>

</div>


<div class="form-group">

    <label class="col-sm-2 control-label">就诊科室</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('member.patients.edit')) { ?>

        <input type="text" name="diag_department" class="form-control" value="<?php  echo $list['diag_department'];?>" />

        <?php  } else { ?>

        <div class='form-control-static'><?php  echo $list['diag_department'];?></div>

        <?php  } ?>
        <!-- <span class='help-block text-danger'>提示，例如：外科-骨科</span> -->

    </div>

</div>

<div class="form-group">

    <label class="col-sm-2 control-label">电话</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('member.patients.edit')) { ?>

        <input type="text" name="tel" class="form-control" value="<?php  echo $list['tel'];?>" />

        <?php  } else { ?>

        <div class='form-control-static'><?php  echo $list['tel'];?></div>

        <?php  } ?>
      

    </div>

</div>

<div class="form-group">

    <label class="col-sm-2 control-label">是否看过医生</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('member.patients.edit')) { ?>

        <label class="radio-inline">
		<input type="radio" name="diag_doctor" id="inlineRadio1" value="0" <?php  if($list['diag_doctor']==0) { ?>checked<?php  } ?>> 否
		</label>
		<label class="radio-inline">
		<input type="radio" name="diag_doctor" id="inlineRadio2" value="1" <?php  if($list['diag_doctor']==1) { ?>checked<?php  } ?>> 是
		</label>

        <?php  } else { ?>

        <div class='form-control-static'><?php  echo $list['diag_doctor'];?></div>

        <?php  } ?>

    </div>

</div>


<div class="form-group">

    <label class="col-sm-2 control-label">发病时间</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('member.patients.edit')) { ?>

		<div class="input-group">
		<input type="text" name="diag_day" class="form-control" placeholder="" aria-describedby="basic-addon2" value="<?php  echo $list['diag_day'];?>">
			<span class="input-group-addon" id="basic-addon2">天</span>
		</div>
        <?php  } else { ?>

        <div class='form-control-static'></div>

        <?php  } ?>
       
    </div>
    

</div>



<div class="form-group">

    <label class="col-sm-2 control-label">病情描述</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('member.patients.edit')) { ?>

        <textarea name="content" class='form-control'><?php  echo $list['content'];?></textarea>

        <?php  } else { ?>

        <div class='form-control-static'><?php  echo $list['content'];?></div>

        <?php  } ?>

    </div>

</div>

<div class="form-group">

    <label class="col-sm-2 control-label must">病情(图片)</label>

    <div class="col-sm-9 col-xs-12 gimgs">

        <?php if( ce('patients' ,$list_updiag) ) { ?>

        <?php  echo tpl_form_field_multi_image('diag_thumbs',$list_updiag['diag_thumbs'])?>

        <?php  } ?>

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

