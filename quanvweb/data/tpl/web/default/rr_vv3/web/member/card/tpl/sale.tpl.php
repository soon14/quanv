<?php defined('IN_IA') or exit('Access Denied');?><div class="form-group">
    <label class="col-sm-2 control-label must">赠送余额</label>
    <?php if( ce('goods' ,$item) ) { ?>
    <div class="col-sm-7"  style="padding-right:0;" >
        <input type="text" name="credit2"  class="form-control"  value="<?php  echo $item['credit2'];?>" data-rule-required="true" />
        <span class='help-block'>用户激活后赠送余额,如果为空或者0,则不赠送</span>
    </div>
    <?php  } else { ?>
        <div class='form-control-static'><?php  echo $item['credit1'];?></div>
    <?php  } ?>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label must">赠送积分</label>
    <?php if( ce('goods' ,$item) ) { ?>
    <div class="col-sm-7"  style="padding-right:0;" >
        <input type="text" name="credit1"  class="form-control"  value="<?php  echo $item['credit1'];?>" data-rule-required="true" />
        <span class='help-block'>用户激活后赠送积分,如果为空或者0,则不赠送</span>
    </div>
    <?php  } else { ?>
    <div class='form-control-static'><?php  echo $item['credit2'];?></div>
    <?php  } ?>
</div>

<div class="form-group">
    <?php if( ce('sale.coupon' ,$item) ) { ?>
    <label class="col-sm-2 control-label">选择优惠券</label>
    <div class="col-sm-9 col-xs-12">
        <?php  echo tpl_selector('couponid',array(
        'preview'=>true,
        'readonly'=>true,
        'multi'=>0,
        'value'=>$coupon['title'],
        'url'=>webUrl('sale/coupon/querycoupons'),
        'items'=>$coupon,
        'buttontext'=>'选择优惠券',
        'placeholder'=>'请选择优惠券')
        )
        ?>
    </div>
    <?php  } else { ?>
        <?php  if(!empty($coupon)) { ?>
            <a href="<?php  echo tomedia($coupon['thumb'])?>" target='_blank'>
                <img src="<?php  echo tomedia($coupon['thumb'])?>" style='width:100px;border:1px solid #ccc;padding:1px' />
            </a>
        <?php  } else { ?>
            不发送优惠券
        <?php  } ?>
    <?php  } ?>
</div>


<div class="form-group"  ></div>
<div class="form-group"  >
    <label class="col-sm-2 control-label" >会员等级</label>
    <div class="col-sm-8 col-lg-9 col-xs-12">
        <select  name="levelid" class="form-control"  >
            <option value="">不选择</option>
            <?php  if(is_array($levels)) { foreach($levels as $type) { ?>
            <option value="<?php  echo $type['id'];?>" <?php  if($type['id']==$item['levelid']) { ?>selected<?php  } ?>><?php  echo $type['levelname'];?></option>
            <?php  } } ?>
        </select>
        <span class='help-block'>用户激活后自动升级为当前会员等级,如果用户会员等级高于此等级则不改变.</span>
    </div>
</div>

<!--yifuyuanma-->