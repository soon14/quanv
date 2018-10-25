<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-heading"> <h2>医生详情</h2> </div>



<form <?php  if('member.doctors.edit') { ?>action="" method='post'<?php  } ?> class='form-horizontal form-validate'>

 <input type="hidden" name="referer" value="<?php  echo referer()?>" />

	<div class="tabs-container">



		<div class="tabs">

			<ul class="nav nav-tabs">

				<li class="active"><a data-toggle="tab" href="#tab-basic" aria-expanded="true"> 基本信息</a></li>

			</ul>

			<div class="tab-content ">

				<div id="tab-basic" class="tab-pane active">
	
					<div class="form-group">

					    <label class="col-sm-2 control-label">医生</label>

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

					    <label class="col-sm-2 control-label">OPENID</label>

					    <div class="col-sm-9 col-xs-12">

					        <div class="form-control-static js-clip" data-url='<?php  echo $member['openid'];?>'><?php  echo $member['openid'];?></div>

					</div>

					</div>


					<div class="form-group">

					    <label class="col-sm-2 control-label">所属医院</label>

					    <div class="col-sm-9 col-xs-12">

					        <?php if(cv('member.doctors.edit')) { ?>

					        <input type="text" name="hospital" class="form-control" value="<?php  echo $info['hospital'];?>" />

					        <?php  } else { ?>

					        <div class='form-control-static'><?php  echo $info['hospital'];?></div>

					        <?php  } ?>

					    </div>

					</div>


					<div class="form-group">

					    <label class="col-sm-2 control-label">职务</label>

					    <div class="col-sm-9 col-xs-12">

					        <?php if(cv('member.doctors.edit')) { ?>

					        <input type="text" name="job" class="form-control" value="<?php  echo $info['job'];?>" />

					        <?php  } else { ?>

					        <div class='form-control-static'><?php  echo $info['job'];?></div>

					        <?php  } ?>

					    </div>

					</div>


					<div class="form-group">

					    <label class="col-sm-2 control-label">科室</label>

					    <div class="col-sm-9 col-xs-12">

					        <?php if(cv('member.doctors.edit')) { ?>

					        <select name='parent' class='form-control'>

								<option value=''>请选择科室</option>
					            <?php  if(is_array($parents)) { foreach($parents as $parent) { ?>

						            <?php  if($info['departmentid']==$parent['id']) { ?>
					                    <option value='<?php  echo $parent['id'];?>' selected><?php  echo $parent['name'];?></option>
					                <?php  } else if($info['parentid']==$parent['id']) { ?>
					                    <option value='<?php  echo $parent['id'];?>' selected><?php  echo $parent['name'];?></option>
					                <?php  } else { ?>
					                    <option value='<?php  echo $parent['id'];?>'><?php  echo $parent['name'];?></option>
					                <?php  } ?>

					            <?php  } } ?>

					        </select>

					        <?php  } ?>

					    </div>

					</div>


					<!-- <div class="form-group">

					    <label class="col-sm-2 control-label">推荐指数</label>

					    <div class="col-sm-9 col-xs-12">

					        <?php if(cv('member.doctors.edit')) { ?>

					        <input type="text" name="recommend_index" class="form-control" value="<?php  echo $info['recommend_index'];?>" />

					        <?php  } else { ?>

					        <div class='form-control-static'><?php  echo $info['recommend_index'];?></div>
							
					        <?php  } ?>
							<div><span id="helpBlock" class="help-block" style="color:red;">最高5颗星星，1代表1颗星星</span></div>
					    </div>
						
					</div> -->

					<!-- <div class="form-group">

					    <label class="col-sm-2 control-label">擅长领域</label>

					    <div class="col-sm-9 col-xs-12">
						
							<?php if(cv('member.doctors.edit')) { ?>
							
							<?php  if(is_array($specialty)) { foreach($specialty as $spe) { ?>
					        <label class="checkbox-inline">
							
								<input type="checkbox" id="inlineCheckbox<?php  echo $spe['id'];?>" name="specialty[]" value="<?php  echo $spe['id'];?>" <?php  if(in_array($spe['id'],$info['specialty'])) { ?>checked<?php  } ?> /> <?php  echo $spe['title'];?>
							
							</label>
							<?php  } } ?>
							
					        <?php  } ?>
							<div><span id="helpBlock" class="help-block" style="color:red;">擅长领域标签为多选</span></div>
					    </div>
						
					</div> -->




					<div class="form-group">

					    <label class="col-sm-2 control-label">医生简介</label>

					    <div class="col-sm-9 col-xs-12">

					        <?php if(cv('member.doctors.edit')) { ?>

					        <textarea name="introduce" style="height:200px;" class='form-control'><?php  echo $info['introduce'];?></textarea>

					        <?php  } else { ?>

					        <div class='form-control-static'><?php  echo $info['introduce'];?></div>

					        <?php  } ?>

					    </div>

					</div>




					<div class="form-group">

					    <label class="col-sm-2 control-label">身份证信息</label>

					    <div class="col-sm-9 col-xs-12">

					        <?php if(cv('member.doctors.edit')) { ?>

					        <!-- <input type="text" name="id_card" class="form-control" value="<?php  echo $info['id_card'];?>" /> -->
							
							<?php  echo tpl_form_field_image('id_card[]', $info['id_card']['front'])?>
					         <span class='help-block'>身份证正面图片</span>
							<?php  echo tpl_form_field_image('id_card[]', $info['id_card']['verso'])?>
					         <span class='help-block'>身份证反面图片</span>
						
					        <?php  } else { ?>

					        <!-- <div class='form-control-static'><?php  echo $info['id_card'];?></div> -->

					        <?php  } ?>

					    </div>

					</div>


					<div class="form-group">
					    <label class="col-sm-2 control-label must">执业医师证书</label>
					    <div class="col-sm-9 col-xs-12 gimgs">
					        <?php if(cv('member.doctors.edit')) { ?>
					        <?php  echo tpl_form_field_multi_image('practice_doctors_certificate',$info['practice_doctors_certificate'])?>
					        <span class="help-block image-block">第一张为缩略图，建议为正方型图片，其他为详情页面图片，尺寸建议宽度为640，并保持图片大小一致</span>
					        <span class="help-block">您可以拖动图片改变其显示顺序 </span>
					        <?php  } else { ?>
					        <?php  if(is_array($info['practice_doctors_certificate'])) { foreach($info['practice_doctors_certificate'] as $p) { ?>
					        <a href='<?php  echo $p;?>' target='_blank'>
					            <img src="<?php  echo $p;?>" style='height:100px;border:1px solid #ccc;padding:1px;float:left;margin-right:5px;' />
					        </a>
					        <?php  } } ?>
					        <?php  } ?>
					    </div>
					</div>

					<div class="form-group">
					    <label class="col-sm-2 control-label must">医师资格证</label>
					    <div class="col-sm-9 col-xs-12 gimgs">
							<?php if(cv('member.doctors.edit')) { ?>
					        <?php  echo tpl_form_field_multi_image('doctor_certificate',$info['doctor_certificate'])?>
					        <span class="help-block image-block">第一张为缩略图，建议为正方型图片，其他为详情页面图片，尺寸建议宽度为640，并保持图片大小一致</span>
					        <span class="help-block">您可以拖动图片改变其显示顺序 </span>
					        <?php  } else { ?>
					        <?php  if(is_array($info['doctor_certificate'])) { foreach($info['doctor_certificate'] as $p) { ?>
					        <a href='<?php  echo $p;?>' target='_blank'>
					            <img src="<?php  echo $p;?>" style='height:100px;border:1px solid #ccc;padding:1px;float:left;margin-right:5px;' />
					        </a>
					        <?php  } } ?>
					        <?php  } ?>
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

					        <?php if(cv('member.doctors.edit')) { ?>

					        <label class="radio-inline"><input type="radio" name="status" value="1" <?php  if($info['status']==1) { ?>checked<?php  } ?>>是</label>

					        <label class="radio-inline" ><input type="radio" name="status" value="0" <?php  if($info['status']==0) { ?>checked<?php  } ?>>否</label>

					        <?php  } else { ?>

					        <div class='form-control-static'><?php  if($info['status']==1) { ?>是<?php  } else { ?>否<?php  } ?></div>

					        <?php  } ?>



					    </div>

					</div>				

				</div>

			</div>

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



</form>
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


<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

