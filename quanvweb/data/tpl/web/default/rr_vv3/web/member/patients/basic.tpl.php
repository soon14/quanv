<?php defined('IN_IA') or exit('Access Denied');?><div class="form-group">

    <label class="col-sm-2 control-label">患者</label>

    <div class="col-sm-9 col-xs-12">

        <img src='<?php  echo $member['avatar'];?>' style='width:50px;height:50px;padding:1px;border:1px solid #ccc' />

        <?php  if(strexists($member['openid'],'sns_wa_')) { ?>

            <i class="icon icon-wxapp" data-toggle="tooltip" data-placement="top" data-original-title="小程序注册" style="color: #7586db;"></i>

        <?php  } ?>

        <?php  if(strexists($member['openid'],'wap_user_')||strexists($member['openid'],'sns_qq_')||strexists($member['openid'],'sns_wx_')) { ?>

            <i class="icon icon-mobile2" data-toggle="tooltip" data-placement="top" data-original-title="<?php  if(strexists($member['openid'],'wap_user_')) { ?>手机号注册<?php  } else { ?>APP注册<?php  } ?>" style="color: #44abf7;"></i>

        <?php  } ?>

        <?php  echo $member['nickname'];?>

    </div>

</div>


<div class="form-group">

    <label class="col-sm-2 control-label">OPENID</label>

    <div class="col-sm-9 col-xs-12">

        <div class="form-control-static js-clip" data-url='<?php  echo $member['openid'];?>'><?php  echo $member['openid'];?></div>

    </div>

</div>


<div class="form-group">

    <label class="col-sm-2 control-label">性别</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('member.patients.edit')) { ?>

        <label class="radio-inline"><input type="radio" name="sex" value="1" <?php  if($info['sex']==1) { ?>checked<?php  } ?>>男</label>

        <label class="radio-inline" ><input type="radio" name="sex" value="0" <?php  if($info['sex']==0) { ?>checked<?php  } ?>>女</label>

        <?php  } else { ?>

        <div class='form-control-static'><?php  if($info['sex']==1) { ?>男<?php  } else { ?>女<?php  } ?></div>

        <?php  } ?>

    </div>

</div>


<div class="form-group">

    <label class="col-sm-2 control-label">婚姻</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('member.patients.edit')) { ?>

        <label class="radio-inline"><input type="radio" name="marital_status" value="0" <?php  if($info['marital_status']==0) { ?>checked<?php  } ?>>未婚</label>

        <label class="radio-inline" ><input type="radio" name="marital_status" value="1" <?php  if($info['marital_status']==1) { ?>checked<?php  } ?>>已婚</label>

        <?php  } else { ?>

        <div class='form-control-static'><?php  if($info['marital_status']==0) { ?>未婚<?php  } else { ?>已婚<?php  } ?></div>

        <?php  } ?>

    </div>

</div>


<div class="form-group">

    <label class="col-sm-2 control-label">是否吸烟</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('member.patients.edit')) { ?>

        <label class="radio-inline"><input type="radio" name="issmoke" value="0" <?php  if($info['issmoke']==0) { ?>checked<?php  } ?>>无</label>

        <label class="radio-inline" ><input type="radio" name="issmoke" value="1" <?php  if($info['issmoke']==1) { ?>checked<?php  } ?>>已戒烟</label>

        <label class="radio-inline" ><input type="radio" name="issmoke" value="2" <?php  if($info['issmoke']==2) { ?>checked<?php  } ?>>偶尔吸烟</label>

        <label class="radio-inline" ><input type="radio" name="issmoke" value="3" <?php  if($info['issmoke']==3) { ?>checked<?php  } ?>>经常吸烟</label>

        <?php  } else { ?>

        <div class='form-control-static'>
            <?php  if($info['issmoke']==0) { ?>无<?php  } ?>
            <?php  if($info['issmoke']==1) { ?>已戒烟<?php  } ?>
            <?php  if($info['issmoke']==2) { ?>偶尔吸烟<?php  } ?>
            <?php  if($info['issmoke']==3) { ?>经常吸烟<?php  } ?>
        </div>

        <?php  } ?>

    </div>

</div>



<div class="form-group">

    <label class="col-sm-2 control-label">是否饮酒</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('member.patients.edit')) { ?>

        <label class="radio-inline"><input type="radio" name="isliquor" value="0" <?php  if($info['isliquor']==0) { ?>checked<?php  } ?>>无</label>

        <label class="radio-inline" ><input type="radio" name="isliquor" value="1" <?php  if($info['isliquor']==1) { ?>checked<?php  } ?>>已戒酒</label>

        <label class="radio-inline" ><input type="radio" name="isliquor" value="2" <?php  if($info['isliquor']==2) { ?>checked<?php  } ?>>偶尔饮酒</label>

        <label class="radio-inline" ><input type="radio" name="isliquor" value="3" <?php  if($info['isliquor']==3) { ?>checked<?php  } ?>>经常饮酒</label>

        <?php  } else { ?>

        <div class='form-control-static'>
            <?php  if($info['isliquor']==0) { ?>无<?php  } ?>
            <?php  if($info['isliquor']==1) { ?>已戒酒<?php  } ?>
            <?php  if($info['isliquor']==2) { ?>偶尔饮酒<?php  } ?>
            <?php  if($info['isliquor']==3) { ?>经常饮酒<?php  } ?>
        </div>

        <?php  } ?>

    </div>

</div>



<div class="form-group">

    <label class="col-sm-2 control-label">身高（cm）</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('member.patients.edit')) { ?>

        <input type="text" name="height" class="form-control" value="<?php  echo $info['height'];?>" />

        <?php  } else { ?>

        <div class='form-control-static'><?php  echo $info['height'];?></div>

        <?php  } ?>

    </div>

</div>

<div class="form-group">

    <label class="col-sm-2 control-label">体重（kg）</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('member.patients.edit')) { ?>

        <input type="text" name="weight" class="form-control" value="<?php  echo $info['weight'];?>" />

        <?php  } else { ?>

        <div class='form-control-static'><?php  echo $info['weight'];?></div>

        <?php  } ?>

    </div>

</div>


<div class="form-group">

    <label class="col-sm-2 control-label">地址</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('member.patients.edit')) { ?>

        <input type="text" name="address" class="form-control" value="<?php  echo $info['address'];?>" />

        <?php  } else { ?>

        <div class='form-control-static'><?php  echo $info['address'];?></div>

        <?php  } ?>

    </div>

</div>



<div class="form-group">

    <label class="col-sm-2 control-label">过敏药品</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('member.patients.edit')) { ?>

        <textarea name="allergy_goods" class='form-control'><?php  echo $info['allergy_goods'];?></textarea>

        <?php  } else { ?>

        <div class='form-control-static'><?php  echo $info['allergy_goods'];?></div>

        <?php  } ?>

    </div>

</div>


<div class="form-group">

    <label class="col-sm-2 control-label">个人病史</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('member.patients.edit')) { ?>

        <textarea name="medical_history" class='form-control'><?php  echo $info['medical_history'];?></textarea>

        <?php  } else { ?>

        <div class='form-control-static'><?php  echo $info['medical_history'];?></div>

        <?php  } ?>

    </div>

</div>


<div class="form-group">

    <label class="col-sm-2 control-label">家族病史</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('member.patients.edit')) { ?>

        <textarea name="home_medicalhistory" class='form-control'><?php  echo $info['home_medicalhistory'];?></textarea>

        <?php  } else { ?>

        <div class='form-control-static'><?php  echo $info['home_medicalhistory'];?></div>

        <?php  } ?>

    </div>

</div>


<div class="form-group">

    <label class="col-sm-2 control-label">遗传病史</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('member.patients.edit')) { ?>

        <textarea name="hereditary_medical" class='form-control'><?php  echo $info['hereditary_medical'];?></textarea>

        <?php  } else { ?>

        <div class='form-control-static'><?php  echo $info['hereditary_medical'];?></div>

        <?php  } ?>

    </div>

</div>

<div class="form-group">

    <label class="col-sm-2 control-label">已关注的医生</label>

    <div class="col-sm-9 col-xs-12">
	<ul class="list-inline">
		<?php  if(is_array($doctors)) { foreach($doctors as $doc) { ?>
        
		<li><img src='<?php  echo $doc['avatar'];?>' style='width:50px;height:50px;padding:1px;border:1px solid #ccc' /></li>
		<?php  if(strexists($doc['openid'],'sns_wa_')) { ?>

            <i class="icon icon-wxapp" data-toggle="tooltip" data-placement="top" data-original-title="小程序注册" style="color: #7586db;"></i>
			<?php  echo $doc['nickname'];?>
        <?php  } ?>
		
		
		
		<?php  } } ?>
	</ul>
    </div>

</div>


<div class="form-group">

    <label class="col-sm-2 control-label">注册时间</label>

    <div class="col-sm-9 col-xs-12">

        <div class='form-control-static'><?php  echo date("Y-m-d H:i:s",$info['createtime'])?></div>

    </div>

</div>


<div class="form-group">

    <label class="col-sm-2 control-label">是否在线</label>

    <div class="col-sm-9 col-xs-12">

        <?php if(cv('member.patients.edit')) { ?>

        <label class="radio-inline"><input type="radio" name="status" value="1" <?php  if($info['status']==1) { ?>checked<?php  } ?>>是</label>

        <label class="radio-inline" ><input type="radio" name="status" value="0" <?php  if($info['status']==0) { ?>checked<?php  } ?>>否</label>

        <?php  } else { ?>

        <div class='form-control-static'><?php  if($info['status']==1) { ?>是<?php  } else { ?>否<?php  } ?></div>

        <?php  } ?>



    </div>

</div>

<div class="form-group"></div>	

          <div class="form-group">

		<label class="col-sm-2 control-label"></label>

		<div class="col-sm-9 col-xs-12">

			<?php if(cv('member.doctors.edit')) { ?>

			<input type="submit"  value="提交" class="btn btn-primary" />

			<?php  } ?>

			<input type="button" class="btn btn-default" name="submit" onclick="history.go(-1)" value="返回列表" <?php if(cv('member.doctors.edit')) { ?>style='margin-left:10px;'<?php  } ?> />

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

