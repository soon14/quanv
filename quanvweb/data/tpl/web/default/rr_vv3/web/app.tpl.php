<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-heading">
    <h2><?php  echo m('plugin')->getName('app')?></h2>
</div>

<div class="row" style="padding: 0;">
    <div class="col-sm-12">
        <div style="border: 1px solid #e7eaec;" class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?php  echo m('plugin')->getName('app')?> 应用说明</h5>
            </div>
            <div class="ibox-content">
                <p></p>
                <p>“<?php  echo m('plugin')->getName('app')?>”应用是微信小程序的管理后台，可在此设置<!-- <a href="<?php  echo webUrl('app/shop/sort')?>">个性化首页排版</a>、 --><a href="<?php  echo webUrl('app/setting')?>">参数配置</a>及<a href="<?php  echo webUrl('app/pay')?>">微信支付</a>；</p>
                <p></p>
                <p></p>
            </div>
        </div>
    </div>
</div>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>