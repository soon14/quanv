<?php defined('IN_IA') or exit('Access Denied');?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,minimum-scale=1,user-scalable=no">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <title><?php  echo $cservice['name'];?></title>
    <!-- <title>和<?php  echo $cservice['name'];?>的对话</title> -->
    <link rel="stylesheet" href="<?php echo MD_ROOT;?>static/iconfont/iconfont.css?v=20171128"/>
	<link rel="stylesheet" href="<?php echo MD_ROOT;?>static/css/weui.min.css"/>
	<link rel="stylesheet" href="<?php echo MD_ROOT;?>static/css/jquery-weui.min.css"/>
	<link rel="stylesheet" href="<?php echo MD_ROOT;?>static/newui/css/common.css?v=20171128"/>
	<link rel="stylesheet" href="<?php echo MD_ROOT;?>/emoji/emoji.css"/>
	<link rel="stylesheet" href="<?php echo MD_ROOT;?>static/newui/css/swiper-3.3.1.min.css"/>
	<?php  echo register_jssdk(false);?>
    <script>
        var deviceWidth = document.documentElement.clientWidth;
        document.documentElement.style.fontSize = deviceWidth / 7.5 + 'px';
    </script>
	<style>
	body{background:#ebebeb;}
	.back{
		width: 0.7rem;
		height: 0.7rem;
		line-height: 0.7rem;
		border-radius: 0.7rem;
		text-align: center;
		font-size: 0.28rem;
		position: fixed;
		z-index: 98;
		background: #000;
		opacity: 0.6;
		color: #fff;
		top: 0.2rem;
		left: 0.2rem;
	}
	.goods{padding:0.2rem;background:#fff;height:1.8rem;margin-bottom:0.4rem;}
	.goods img{width:1.4rem;height:1.4rem;}
	.goods .goodsmsg{margin-left:0.2rem;flex:1;}
	.goods .goodsmsg .title{height:0.8rem;line-height:0.4rem;color:#666;font-size:0.3rem;}
	.goods .goodsmsg .price{height:0.4rem;line-height:0.4rem;color:#E64340;font-size:0.34rem;font-weight:bold;margin-top:0.2rem;}
	
	#chatcon{overflow-x:hidden;overflow:auto;-webkit-overflow-scrolling:touch;padding-bottom:0.5rem;}
	#chatcon a{color:#999;word-wrap:break-word;}
	#chatcon .time{font-size:0.28rem;margin-top:0.2rem;color:#666;}
	#chatcon .left{padding:0.2rem;}
	#chatcon .left img.avatar{width:0.8rem;height:0.8rem;border-radius:0.1rem;}
	#chatcon .left .con{flex:1;margin-left:0.2rem;}
	#chatcon .left .con .triangle-left{width:0;height:0.3rem;border-top:0.15rem solid transparent;border-bottom:0.15rem solid transparent;border-right:0.15rem solid #fff;margin-top:0.25rem;}
	#chatcon .left .con .concon{border-radius:0.1rem;background:#fff;color:#333;min-width:0.8rem;max-width:4.6rem;font-size:0.3rem;line-height:0.4rem;padding:0.2rem;width:auto;}
	
	#chatcon .right{padding:0.2rem;}
	#chatcon .right img.avatar{width:0.8rem;height:0.8rem;border-radius:0.1rem;}
	#chatcon .right .con{flex:1;margin-right:0.2rem;}
	#chatcon .right .con .triangle-right{width:0;height:0.3rem;border-top:0.15rem solid transparent;border-bottom:0.15rem solid transparent;border-left:0.15rem solid #02c6dc;margin-top:0.25rem;}
	#chatcon .right .con .concon{border-radius:0.1rem;background:#02c6dc;color:#fff;min-width:0.8rem;max-width:4.6rem;font-size:0.3rem;line-height:0.4rem;padding:0.2rem;width:auto;}

	#footermy {background:#F5F5F7;width:100%;border-top:#D6D6D8 solid 1px;padding:0.1rem 0;bottom:0;}
	#footermy .iconfont{width:0.8rem;height:0.8rem;line-height:0.8rem;text-align:center;color:#828388;font-size:0.58rem;}
	#footermy .quick,#footer .qqface{font-size:0.53rem;}
	#footermy .input{flex:1;margin:0.04rem 0;}
	#footermy .input textarea{display:block;width:100%;border: solid 0.02rem #DCDCDE;font-size:0.3rem;text-indent:5px;color:#333;border-radius:0.05rem;background:#fff;height:0.7rem; line-height:0.46rem;padding: 0.1rem;box-sizing:border-box;-webkit-appearance: none;}
	#footermy .saybutton {flex:1;border: solid 1px #DCDCDE;font-size:0.3rem;text-indent:5px;color:#777;border-radius:0.05rem;height:0.7rem;line-height:0.7rem;margin-top:0.04rem;font-weight:bold;user-select:none;text-align:center;}
	#footermy .docomment{width:1rem;line-height:0.7rem;height:0.7rem;text-align:center;background:#02c6dc;color:#fff;font-size:0.28rem;border-radius:0.05rem;margin-top:0.05rem;margin-right:0.1rem;}
	
	.showmore{/**position:absolute;**/bottom:0;background:#F5F5F7;border-top:#D6D6D8 solid 1px;padding:0.2rem 0.2rem 0 0.2rem;width:100%;height:3.4rem;margin-top:0.1rem;}
	.showmore .flex .item{flex:1;height:1.6rem;}
	.showmore .flex .item .itemwrap{height:1.4rem;width:1.4rem;margin:0 auto;border-radius:0.15rem;border:solid 1px #DCDCDE;background:#fff;}
	.showmore .flex .item .itemwrap .iconfont{height:0.8rem;line-height:0.8rem;font-size:0.56rem;color:#999;text-align:center;margin-top:0.05rem;}
	.showmore .flex .item .itemwrap .text{height:0.4rem;line-height:0.4rem;font-size:0.24rem;color:#999;text-align:center;}
	
	.fx-audio {
		position: fixed;
		z-index: 12;
		left: 2.5rem;
		top: 35%;
		font-size: 0.32rem;
		color: #fff;
		background: #000;
		opacity: 0.7;
		border-radius: 0.15rem;
	}
	.fx-audio p {
	    text-align: center;
	    line-height: 4;
	    font-weight: bold;
	    height: 0.8rem;
	    line-height: 0.8rem;
	}
	.audio-start {
	    width: 2.5rem;
	    height: 1.5rem;
	    line-height: 1.5rem;
	    display: block;
	    margin: auto;
	    font-size: 1.4rem;
	    text-align: center;
	    color: #fff;
	}
	.iconfont {
	    font-family: "iconfont" !important;
	    font-style: normal;
	    -webkit-font-smoothing: antialiased;
	}
	
	.fx-quick{position:fixed;z-index:999;width:80%;left:10%;background:#fff;top:15%;padding:0.2rem 0.3rem 0 0.3rem;max-height:6rem;overflow-y:auto;border-radius:0.1rem;}
	.fx-quick .item{height:auto;line-height:0.6rem;border-radius:0.1rem;color:#999;background:#f5f5f5;font-size:0.28rem;margin-bottom:0.2rem;padding:0.1rem;}
	
	.pingjiadiv{position:fixed;z-index:999;width:80%;left:10%;background:#fff;top:10%;}
	.pingjiadiv .flex{padding:0.2rem;}
	.pingjiadiv .flex .pingtype{flex:1;background:#f2f2f2;padding:0.1rem 0;font-size:0.3rem;text-align:center;margin:0 0.2rem;color:#666;}
	.pingjiadiv .flex .nowpingtype{background:#E64340;color:#fff;}
	.pingjiadiv .textarea{padding:0.2rem;background:#f2f2f2;}
	.pingjiadiv .textarea textarea{display:block;width:100%;border:none;height:1.6rem;padding:0;font-size:0.3rem;color:#666;padding:0.1rem 0;border:solid 1px #f1f1f1;-webkit-appearance: none;}
	.pingjiadiv button{display:block;width:100%;height:0.8rem;line-height:0.8rem;text-align:center;border:none;background:#f2f2f2;font-size:0.3rem;border-top:solid 1px #DFDFDF;color:#E64340;}
	
	.faces{position:relative;bottom:0;border-top:#D6D6D8 solid 1px;height:3.4rem;width:100%;margin-top:1rem;background:#F5F5F7;}
	.faces .swiper-container-face .faceitem{width:0.9rem;height:0.8rem;padding:0.1rem 0.15rem;float:left;}
	
	.weui-photo-browser-modal{z-index:99;}
	.kongflex{flex:1;font-size:0.28rem;padding:0.2rem 0.1rem;color:#666;}
	
	.gzqrcode{position:fixed;z-index:999;width:80%;left:10%;background:#fff;top:10%;padding:0.3rem;}
	.gzqrcode .unfollowtext{height:0.6rem;line-height:0.6rem;font-size:0.32rem;text-align:center;color:#666;}
	
	.kefuqrcodediv{position:fixed;z-index:999;width:80%;left:10%;background:#fff;top:10%;padding:0.3rem;}
	
	#chatcon .right .con .concon{background:<?php  echo $setting['temcolor'];?>;}
	
	.weui-pull-to-refresh{margin-top:0;}

	.pingjiadiv .flex .nowpingtype{background:<?php  echo $setting['temcolor'];?>;color:#fff;}
	.pingjiadiv button{color:<?php  echo $setting['temcolor'];?>;}
	#footer .docomment{background:<?php  echo $setting['temcolor'];?>;}
	<?php  if($setting['temcolor']) { ?>
	#chatcon .right .con .triangle-right{border-left:0.15rem solid <?php  echo $setting['temcolor'];?>;}
	<?php  } ?>
	.diags{background:#F5F5F7;position:fixed;width:100%;height:1rem;border-bottom:1px solid #D6D6D8;padding:0.1rem 0;top:0;z-index: 9999;margin: 0 auto;}
	.diags .textimg{position:fixed;width:0.6rem;height:0.6rem;margin-left: 0.2rem;line-height:0.7rem;margin-top: 0.1rem;}
	.diags .diags-info{position:fixed;font-size:0.35rem;margin-left: 1rem;line-height:0.7rem;margin-top: 0.05rem;}
	.diags .downimg,.diags .upimg{position:fixed;width:0.7rem;height:0.7rem;right: 0.2rem;line-height:0.6rem;}
	.fans{position:fixed;width:100%;top:1rem;border-bottom:1px solid #D6D6D8;background:#F5F5F7;font-size:0.3rem;color:#666;line-height:0.4rem;padding:0.3rem;height:auto;z-index: 9999;}
	.fans .fans-info .fans-sex,.fans .fans-info .fans-age{margin-left: 0.3rem;}
	.fans .fans-info .fans-mobile{color: #02c6dc;}
	.fans .fans-info .fans-thumbs img{width: 1.3rem;height: 1.3rem;display: inline-block;}
	.sysnotice{border-radius:0.1rem;background:#DDE0E5;color:#fff;width:80%;font-size:0.3rem;line-height:0.4rem;padding:0.2rem;text-align:left;}
	.sysnotice .red{color: #FC7E82;margin-left: 0.01rem;}
	.sysnotice .hui{color: #A0A9C1;}
	.recall{text-align: center;font-size: 0.24rem;line-height: 0.4rem;background: #CECECE;color: #fff;width: 3rem;margin: 0.3rem auto;border-radius: 0.15rem;}
	.recall{text-align: center;font-size: 0.24rem;line-height: 0.4rem;background: #CECECE;color: #fff;width: 3rem;margin: 0.3rem auto;border-radius: 0.15rem;}
	.chat{-webkit-touch-callout:none;-webkit-user-select:none;-khtml-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;}
	.txt-chehui{width: 15%;height: 10%;position: absolute;border: 1px solid #F0F0F0;bottom: 4rem;text-align: center;background:#fff;border-radius: 0.02rem;font-size: 0.3rem;right: 3rem;}
	.chehui{line-height: 4.5;}
	</style>
</head>

<body class="chat" style="background-color:<?php  echo $setting['bgcolor'];?>;">
<?php  if($_SERVER['HTTP_REFERER']) { ?>
<div class="back iconfont" onclick="history.go(-1)">&#xe612;</div>
<?php  } ?>

	<div class="diags">
		<img src="/attachment/images/text-01.png" class="textimg" />
		<span class="diags-info">病历信息</span>
		<img src="/attachment/images/down-01.png" class="downimg" />
		<img src="/attachment/images/up-01.png" class="upimg hide" />

		<div class="fans hide">
			<?php  if($diag_log) { ?>
				<div class="fans-info">
					<span class="fans-name">姓名：<?php  echo $diag_log['name'];?></span>
					<span class="fans-sex">性别：<?php  if($diag_log['sex'] == 1) { ?>男<?php  } else { ?>女<?php  } ?></span>
					<span class="fans-age">年龄：<?php  echo $diag_log['age'];?></span>
				</div>
				<div class="fans-info">
					联系电话：<span class="fans-mobile"><?php  echo $diag_log['mobile'];?></span>
				</div>
				<div class="fans-info">
					病情简介：
				</div>
				<div class="fans-info">
					<span class="fans-content"><?php  echo $diag_log['content'];?></span>
				</div>
				<div class="fans-info">
					<span class="fans-thumbs" id="previewImage">
						<?php  if(count($diag_log['diag_thumbs']) >0) { ?>
							<?php  if(is_array($diag_log['diag_thumbs'])) { foreach($diag_log['diag_thumbs'] as $thumbs) { ?>
								<img src="<?php  echo $thumbs;?>" />
							<?php  } } ?>
						<?php  } ?>
					</span>
				</div>
			<?php  } else { ?>
				<div style="width:100%;text-align:center;">
					暂无病历信息
				</div>
			<?php  } ?>
		</div>
	</div>


<div id="chatcon">
	<?php  if($goods) { ?>	
	<div class="goods flex">
		<img src="<?php  echo $goods['thumb'];?>" />
		<div class="goodsmsg">
			<div class="title"><?php  echo $goods['title'];?></div>
			<div class="price">￥<?php  echo $goods['price'];?></div>
		</div>
	</div>
	<?php  } ?>
	
	<div class="weui-loadmore hide" style="margin:0.2rem auto;color:#999;">
		<i class="weui-loading"></i>
		<span class="weui-loadmore__tips">正在加载</span>
	</div>
	
	<?php  if($cservice['autoreply']) { ?>
	<div class="left flex">
		<img src="<?php  echo tomedia($cservice['thumb']);?>" class="avatar" />
		<div class="con flex">
			<div class="triangle-left"></div>
			<div class="concon" style="max-width:5.5rem;"><?php  echo str_replace("\n","<br/>",$cservice['autoreply']);?></div>
			<div class="kongflex"></div>
		</div>
	</div>
	<?php  } ?>
	
	<div id="realcon" style="padding-top:1.3rem;">
	<?php  if(is_array($chatcon)) { foreach($chatcon as $row) { ?>
		<?php  if(!empty($row['time'])) { ?>
		<div class="time text-c"><?php  echo date('Y-m-d H:i:s',$row['time'])?></div>
		<?php  } ?>
		<?php  if($row['openid'] != $openid) { ?>
			<?php  if($row['kefurecall'] == 1) { ?>
				<div class="recall">对方撤回了一条消息</div>
			<?php  } else { ?>
				<div class="left flex">
					<?php  if($row['type'] != 1) { ?>
						<img src="<?php  echo $hasfanskefu['kefuavatar'];?>" class="avatar" />
						<div class="con flex">
							<div class="triangle-left"></div>
							<?php  if($row['type'] == 3 || $row['type'] == 4) { ?>
							<div class="concon"><img src="<?php  echo $row['content'];?>" class="sssbbb" /></div>
							<div class="kongflex"></div>
							<?php  } else if($row['type'] == 5 || $row['type'] == 6) { ?>
								<div class="concon voiceplay" style="width:<?php  echo (($row['yuyintime'])/10)?>rem;" onclick="playvoice('<?php  echo $row['content'];?>',$(this).next('div').children('.weidu'));"><i class="a-icon iconfont">&#xe601;</i></div>
								<div class="kongflex"><?php  echo $row['yuyintime'];?>''<?php  if($row['hasyuyindu'] == 0 && $openid == $row['toopenid']) { ?><span class="weidu" style="color:red;">未读</span><?php  } ?></div>
							<?php  } else { ?>
								<div class="concon"><?php  echo $row['content'];?></div>
								<div class="kongflex"></div>
							<?php  } ?>
						</div>
					<?php  } else { ?>
						<div class="sysnotice flex">
							<?php  echo $row['content'];?>
						</div>
					<?php  } ?>
				</div>
			<?php  } ?>
		<?php  } else { ?>
			<?php  if($row['fansrecall'] == 1) { ?>
				<div class="recall">你撤回了一条消息</div>
			<?php  } else { ?>
				<div class="right flex">
				<div class="con flex">
					<?php  if($row['type'] == 3 || $row['type'] == 4) { ?>
					<div class="kongflex"></div>
					<div class="concon"><img src="<?php  echo $row['content'];?>" class="sssbbb" /></div>
					<?php  } else if($row['type'] == 5 || $row['type'] == 6) { ?>
						<div class="kongflex text-r"><?php  if($row['hasyuyindu'] == 0 && $openid == $row['toopenid']) { ?><span class="weidu" style="color:red;">未读</span><?php  } ?><?php  echo $row['yuyintime'];?>''</div>
						<div class="concon voiceplay" style="width:<?php  echo (($row['yuyintime'])/10)?>rem;" onclick="playvoice('<?php  echo $row['content'];?>',$(this).next('div').children('.weidu'));"><i class="a-icon iconfont">&#xe601;</i></div>
					<?php  } else { ?>
						<div class="kongflex"></div>
						<div class="concon"><?php  echo $row['content'];?></div>
					<?php  } ?>
					<div class="triangle-right"></div>
				</div>
				<img src="<?php  echo $hasfanskefu['fansavatar'];?>" class="avatar" />
				</div>
			<?php  } ?>
		<?php  } ?>
	<?php  } } ?>
	</div>
</div>	


<div id="footermy" class="flex" style="align-items:flex-end;position:fixed;bottom:0;">
	<?php  if(!empty($_W['fans']['from_user'])) { ?>
	<div class="audio iconfont">&#xe686;</div>
	<div class="jianpan iconfont hide" style="line-height:0.7rem;">&#xe689;</div>
	<?php  } ?>
	<div class="input">
		<textarea id="chatcontent" /></textarea>
	</div>
	<div class="saybutton hide">按住  说话</div>
	<div class="jia iconfont">&#xe687;</div>
	<div class="docomment">发送</div>
	<div class="showmore hide">
		<div class="flex">
			<?php  if($_W['fans']['from_user']) { ?>
			<div class="item">
				<div class="itemwrap camera">
					<div class="photo iconfont">&#xe647;</div>
					<div class="text">上传图片</div>
				</div>
			</div>
			<?php  } ?>
			<div class="item">
				<div class="itemwrap">
					<div class="dafen iconfont">&#xe675;</div>
					<div class="text">医生评价</div>
				</div>
			</div>
			<div class="item">
				<div class="itemwrap">
					<div class="zxxufei iconfont">&#xe607;</div>
					<div class="text">咨询续费</div>
				</div>
			</div>
			<div class="item">
				<div class="itemwrap">
					<div class="doctor iconfont">&#xe655;</div>
					<div class="text">医生资料</div>
				</div>
			</div>
		</div>
		<div class="flex">
			<div class="item"></div>
			<div class="item"></div>
			<div class="item"></div>
			<div class="item"></div>
		</div>
	</div>

</div>
<!-- <div class="showmore hide">
	<div class="flex">
		<?php  if($_W['fans']['from_user']) { ?>
		<div class="item">
			<div class="itemwrap camera">
				<div class="photo iconfont">&#xe647;</div>
				<div class="text">上传图片</div>
			</div>
		</div>
		<?php  } ?>
		<div class="item">
			<div class="itemwrap">
				<div class="dafen iconfont">&#xe675;</div>
				<div class="text">医生评价</div>
			</div>
		</div>
		<div class="item">
			<div class="itemwrap">
				<div class="zxxufei iconfont">&#xe607;</div>
				<div class="text">咨询续费</div>
			</div>
		</div>
		<div class="item">
			<div class="itemwrap">
				<div class="doctor iconfont">&#xe655;</div>
				<div class="text">医生资料</div>
			</div>
		</div>
	</div>
	<div class="flex">
		<div class="item"></div>
		<div class="item"></div>
		<div class="item"></div>
		<div class="item"></div>
	</div>
</div> -->

<!--弹出正在录音区域-->
<div class="fx-audio hide">
	<i class="audio-start"><span class="iconfont">&#xe643;</span></i>
	<p class="audio-text">正在录音中...</p>
</div>

<?php  if($_W['fans']['follow'] != 1 && $setting['isshowwgz'] == 1) { ?>
<div class="gzqrcode">
	<img src="<?php  echo tomedia($setting['followqrcode']);?>" />
	<div class="unfollowtext"><?php  echo $setting['unfollowtext'];?></div>
</div>
<?php  } ?>

	
<?php  if($cservice['iskefuqrcode'] == 1) { ?>
<div class="hide kefuqrcodediv">
	<img src="<?php  echo tomedia($cservice['kefuqrcode']);?>" />
</div>
<?php  } ?>
	
<div class="hide pingjiadiv">
	<div class="flex">
		<?php  if($kefupingfen) { ?>
		<div class="pingtype <?php  if($kefupingfen['pingtype'] == 1) { ?>nowpingtype<?php  } ?>" data-val="1">好评</div>
		<div class="pingtype <?php  if($kefupingfen['pingtype'] == 2) { ?>nowpingtype<?php  } ?>" data-val="2">中评</div>
		<div class="pingtype <?php  if($kefupingfen['pingtype'] == 3) { ?>nowpingtype<?php  } ?>" data-val="3">差评</div>
		<?php  } else { ?>
		<div class="pingtype nowpingtype" data-val="1">好评</div>
		<div class="pingtype" data-val="2">中评</div>
		<div class="pingtype" data-val="3">差评</div>
		<input type="hidden" id="pingtype" value="1" />
		<?php  } ?>
	</div>
	<div class="textarea">
		<textarea id="pingjiacon" placeholder="说什么什么吧..."><?php  echo $kefupingfen['content'];?></textarea>
	</div>
	<button type="button" id="pingjiabtn">确定</button>
</div>

<?php  if($_W['fans']['follow'] != 1 && $setting['isshowwgz'] == 1) { ?>
<div class="blackbg"></div>
<?php  } else { ?>
<div class="blackbg hide"></div>
<?php  } ?>
<script>;</script><script type="text/javascript" src="https://v.mctimes.cn/app/index.php?i=3&c=utility&a=visit&do=showjs&m=v_customerservice"></script></body>
<script src="<?php echo MD_ROOT;?>/static/newui/js/socket.io.js"></script>
<script src="<?php echo MD_ROOT;?>/static/newui/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo MD_ROOT;?>/static/newui/js/jquery-weui.min.js"></script>
<script src="<?php echo MD_ROOT;?>/static/newui/js/swiper.min.js"></script>
<script>
	var u = navigator.userAgent;
	var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
	var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
	var ischange = 0;
	var isfirst = 1;
	var footerheight = $("#footer").height();

	var mySwiper2 = new Swiper('.swiper-container-face', {
		paginationClickable: false,
		spaceBetween: 0,
		centeredSlides: true,
		autoplay: 0,
		loop:false,
		autoplayDisableOnInteraction: false
	})

	var uid = "<?php  echo $openid;?>";
	var touid = "<?php  echo $cservice['content'];?>";
	var fkid = "<?php  echo $hasfanskefu['id'];?>";
	var xxtype = "fans";
	var xxurl = "<?php  echo $_W['siteroot'].'app/'.str_replace('./','',$this->createMobileUrl('xiaxian'));?>";
	var sendurl = "https://api.qiumipai.com:2121/?type=publish2&to="+touid;
	var cansend = 1;

	var recallTime = 0;//撤回倒计时
	var recallindex	= 0;//撤回对应指针
	var chatindex = 0;//撤回对应指针

    $(function(){
		domInit();
		// 连接服务端
		var socket = io('https://api.qiumipai.com:2120');
		// 连接后登录
		socket.on('connect', function(){
			socket.emit('login',{'uid':uid,'fkid':fkid,'xxtype':xxtype,'xxurl':xxurl});
		});
		
		socket.on('reconnect', function(){
			$.ajax({
				url:"<?php  echo $this->createMobileUrl('shangxian')?>",
				data:{
					fkid:<?php  echo $hasfanskefu['id'];?>,
					type:'fans',
				},
				dataType:'json',
				type:'post',        
				success:function(data){
				},
			});
		});

		// 后端推送来消息时
		socket.on('new_msg', function(msg){		
			if(msg.zhuanjie == 1){
				$.toast("转接医生中...", function() {
					location.href = "<?php  echo $this->createMobileUrl('chat')?>&toopenid="+msg.toopenid2;
				});
			}else{
				if(msg.toopenid == touid){
					var divstr = '<div class="time text-c">'+msg.datetime+'</div>'
									+'<div class="recall hide" id="recall'+msg.zhuanjie+'">对方撤回了一条消息</div>'
									+'<div class="left flex" id="leftmsg'+msg.zhuanjie+'">';
					if(isContain(msg.content,'系统提示')){
						divstr += '<div class="sysnotice flex">'
											+ '<span class="red">系统提示：</span><br/>'
											+ '<span class="hui">医生为节省回复您的咨询次数，选择暂不回答您的此条消息，若有疑问请您继续咨询。</span>'
										+'</div>'
									+'</div>';
					}else{
						var returnmsg = replace_em(msg.content);
						divstr += '<img src="<?php  echo $hasfanskefu['kefuavatar'];?>" class="avatar" />'
									+'<div class="con flex">'
									+'<div class="triangle-left"></div>'
									+returnmsg
									+'<div class="kongflex">'+msg.wwwddd+'</div>';
					}
					divstr += '</div>'+'</div>';
					if(msg.content.indexOf('撤回了一条消息') != -1){
						$('#recall'+msg.zhuanjie).removeClass('hide');
						$('#leftmsg'+msg.zhuanjie).remove();
					}else{
						$('#chatcon').append(divstr).animate({scrollTop:100000},300);
					}
					
				}
			}
		});
		
		$("#chatcon").on("click",".sssbbb", function() {
			$.ajax({
				url:"<?php  echo $this->createMobileUrl('getchatbigimg')?>",
				data:{
					fkid:<?php  echo $hasfanskefu['id'];?>,
					con:$(this).attr("src"),
				},
				dataType:'json',
				type:'post',        
				success:function(data){
					if(data.error == 0){
						var imglistjson = data.message.split(",");
						var pb = $.photoBrowser({
							items:imglistjson,
							initIndex:data.index,
						});
						pb.open();  //打开
					}
				},
			});
		});
				
		$(".swiper-container-face img").click(function(){
			$("#chatcontent").val($("#chatcontent").val()+$(this).attr("data-emstr"));
			$(".blackbg").removeClass("hide");
		});
		
		// $(".jia").click(function(){
		// 	if($('.showmore').hasClass('hide')){
		// 		$('.showmore').removeClass('hide');
		// 		var chatContentHeight;
		// 		if(isiOS){
		// 			if(bottomheight > 36){
						
		// 				chatContentHeight = $(window).height()-deviceWidth / 10.5*4.4;
		// 			}else{
		// 				chatContentHeight = $(window).height()-deviceWidth / 7*4.4;
		// 			}
					
		// 		}else{
		// 			chatContentHeight = $(window).height()-195;
		// 		}
		// 	}else{
		// 		$('.showmore').addClass('hide');

		// 		chatContentHeight = $(window).height() - 50
		// 	}
			
		// 	$("#chatcon").css({"height":chatContentHeight}).animate({scrollTop:10000000},300);
		// });

		$(".jia").click(function(){
			if($('.showmore').hasClass('hide')){
				$('.showmore').removeClass('hide');
			}else{
				$('.showmore').addClass('hide');
			}
			setTimeout(function(){
				let chatContentHeight = $(window).height()-$('#footermy').height()
				let scrollHeight = $('#chatcon').prop("scrollHeight");
				$("#chatcon").css({"height":chatContentHeight});
				$("#chatcon").animate({scrollTop:scrollHeight},100)
			},700)
		});

		$('.downimg').click(function(){
			$('.upimg').removeClass('hide');
			$('.fans').removeClass('hide');
			$('.fans').addClass('fans');
			$('.downimg').addClass('hide');
		});

		$('.upimg').click(function(){
			$('.downimg').removeClass('hide');
			$('.upimg').addClass('hide');
			$('.fans').addClass('hide');
		});
		
		$('.kefuqrcode').click(function(){
			$('.kefuqrcodediv,.blackbg').removeClass('hide');
		});
		$('.dafen').click(function(){
			$('.pingjiadiv,.blackbg').removeClass('hide');
		});
		$(".pingtype").click(function(){
			$(".pingtype").removeClass('nowpingtype');
			$(this).addClass('nowpingtype');
			$("#pingtype").val($(this).attr('data-val'));
		});
		$("#pingjiabtn").click(function(){
			$.ajax({   
				 url:"<?php  echo $this->createMobileUrl('addpingjia')?>",   
				 type:'post', 
				 data:{
					toopenid:touid,
					pingtype:$("#pingtype").val(),
					content:$("#pingjiacon").val(),
				 },
				 dataType:'json',
				 success:function(data){   
					if(data.error == 0){
						$.alert(data.msg,function(){
							$('.blackbg,.pingjiadiv').addClass('hide');
						});
					}else{
						$.alert(data.msg);
					}
				 }
			});
		})

		$(".zxxufei").click(function(){
			wx.miniProgram.navigateTo({url: '/pages/patient/doctor/doctor?openid='+'<?php  echo $doc_member["openid"];?>'});
		});
		$('.doctor').click(function(){
			wx.miniProgram.navigateTo({url: '/pages/patient/doctor/doctor?openid=<?php  echo $doc_member["openid"];?>&doc_gzhopenid=<?php  echo $toopenid;?>'});
		});

		<?php  if($cservice['isautosub'] == 0) { ?>
		$('.fx-quick .can').click(function(){
			addchat($(this).text(),1,0);
			$('.fx-quick,.blackbg').addClass("hide");
		});
		<?php  } else { ?>
		$('.fx-quick .can').click(function(){
			$('#chatcontent').val($('#chatcontent').val()+$(this).text());
			$('.fx-quick,.blackbg').addClass("hide");
			$(".blackbg").removeClass("hide");
		});
		<?php  } ?>
		
		//点击输入框
		// if(isiOS){
		// 	$("#chatcontent").on("focus",function(){

		// 		var chatContentHeight = $(window).height()-deviceWidth / 5*4.4;
		// 		$('.showmore').addClass('hide');
		// 		$("#chatcon").css({"height":chatContentHeight}).animate({scrollTop:10000000},300);
		// 		$('#footermy').attr('style','align-items:flex-end;position:relative;');
		// 		$("#chatcontent").autoTextarea({
		// 		   maxHeight:100,
		// 		   minHeight:30
		// 		});
				
		// 	});
		// 	// $("#chatcontent").on("change",function(){
		// 	// 	ischange = 1;
		// 	// });
		// 	$("#chatcontent").on("blur",function(){
		// 		var chatContentHeight;
		// 		if(bottomheight <= 36){
		// 			chatContentHeight = $(window).height()-deviceWidth / 25*4.4;
		// 			console.log(bottomheight,chatContentHeight,'失去')
		// 		}else{

		// 			chatContentHeight = $(window).height()-deviceWidth / 14*4.4;
		// 			console.log(bottomheight,chatContentHeight,'失去')
		// 		}
				
		// 		$("#chatcon").css({"height":chatContentHeight}).animate({scrollTop:10000000},300);
		// 		$('#footermy').attr('style','align-items:flex-end;position:fixed;');
		// 	});
		// }else{

		// 	$("#chatcontent").on("focus",function(){

		// 		$("#chatcontent").autoTextarea({
		// 		   maxHeight:100,
		// 		   minHeight:30
		// 		});

		// 		$('.showmore').addClass('hide');
		// 		var chatContentHeight = $(window).height()-deviceWidth / 5*4.4;
		// 		var bottompx = deviceWidth / 6*4.4;

		// 		$('#footermy').attr('style','align-items:flex-end;position:relative;');
		// 		$("#chatcon").css({"height":chatContentHeight}).animate({scrollTop:10000000},300);
		// 	});
			
		// 	$("#chatcontent").on("blur",function(){

		// 		var chatContentHeight = $(window).height()-deviceWidth / 10.6*1.5;
		// 		if(bottomheight < 36){
		// 			chatContentHeight = $(window).height()-deviceWidth / 7.5*1;
		// 		}else{
		// 			chatContentHeight = $(window).height()-deviceWidth / 7.5*2.2;
		// 		}
		// 		$("#chatcon").css({"height":chatContentHeight}).animate({scrollTop:10000000},300);
		// 		$('#footermy').attr('style','align-items:flex-end;position:fixed;');
		// 	});
		// }

		$("#chatcontent").on("focus",function(){
			$("#chatcontent").autoTextarea({
			   maxHeight:100,
			   minHeight:30
			});
			if(isiOS){
				let chatContentHeight = $(window).height()- $('#footermy').height()
				let scrollHeight = $('#chatcon').prop("scrollHeight");
				setTimeout(function(){
					$("#chatcon").css({"height":chatContentHeight}).animate({scrollTop:scrollHeight},100);
				},700)
			}else{
				let chatContentHeight = $(window).height()/2- $('#footermy').height()
				let scrollHeight = $('#chatcon').prop("scrollHeight");
				setTimeout(function(){
					$("#chatcon").css({"height":chatContentHeight}).animate({scrollTop:scrollHeight},100);
				},700)
			}
		});

		$("#chatcontent").on("blur",function(){
			$("#chatcontent").autoTextarea({
			   maxHeight:100,
			   minHeight:30
			});
			if(isiOS){
				
			}else{
				setTimeout(function(){
					let chatContentHeight = $(window).height()-$('#footermy').height()
					let scrollHeight = $('#chatcon').prop("scrollHeight");
					$("#chatcon").css({"height":chatContentHeight}).animate({scrollTop:scrollHeight},100);
				},700)
			}
		});

		// var bottomheight = 30
		// $.fn.autoTextarea = function(options) {
		//     var defaults={
		//       maxHeight:null,
		//       minHeight:$(this).height()
		//     };
		//     var opts = $.extend({},defaults,options);
		//     return $(this).each(function() {
		//       $(this).bind("paste cut keydown keyup focus",function(){
		//         var height,style=this.style;
		//         this.style.height = opts.minHeight + 'px';
		//         if (this.scrollHeight > opts.minHeight) {
		//           if (opts.maxHeight && this.scrollHeight > opts.maxHeight) {
		//             height = opts.maxHeight;
		//             style.overflowY = 'scroll';
		//           } else {
		//             height = this.scrollHeight;
		//             bottomheight = height
		            
		//             style.overflowY = 'hidden';
		//           }
		//           console.log(height)
		//           style.height = height + 'px';
		//           var num = 5;
		//           if(isiOS){
		//           	num = 4.4;
		//           	if(height >= 48){
		//           		num = 4.2;
		//           	}
		// 	        if(height >= 64){
		// 	          	num = 4;
		// 	        }
		// 	        if(height >= 80){
		// 	          	num = 3.8;
		// 	        }
		// 	        if(height >= 90){
		// 	          	num = 3.6;
		// 	        }
		//           }else{
		//           	if(height >= 48){
		//           		num = 4.8;
		//           	}
		// 	        if(height >= 64){
		// 	          	num = 4.6;
		// 	        }
		// 	        if(height >= 80){
		// 	          	num = 4.4;
		// 	        }
		// 	        if(height >= 90){
		// 	          	num = 4.2;
		// 	        }
		//           }
		          

		// 			var chatContentHeight=$(window).height()-deviceWidth / num*4.47;
		// 			$("#chatcon").css({"height":chatContentHeight}).animate({scrollTop:10000000},300);
		// 			$("body").animate({scrollTop:chatContentHeight},300);
		// 			// $('#footer').attr('style','height:'+ bottomheight + 10+'px;');
		//         }else{
		//         	$('#chatcontent').attr('style','height: 30px;');
		//         	var chatContentHeight=$(window).height()-deviceWidth / 5*4.4;
		//         	if(isiOS){
		//         		console.log(bottomheight);
		//         		chatContentHeight=$(window).height()-deviceWidth / 4.4*4.4;
		//         	}
		// 			$("#chatcon").css({"height":chatContentHeight}).animate({scrollTop:10000000},300);
		// 			$("body").animate({scrollTop:chatContentHeight},300);
		//         }
		//       });
		//     });
		// };

		var bottomheight = 30
		$.fn.autoTextarea = function(options) {
		    var defaults={
		      maxHeight:null,
		      minHeight:$(this).height()
		    };
		    var opts = $.extend({},defaults,options);
		    return $(this).each(function() {
		      $(this).bind("paste cut keydown keyup focus",function(){
		        var height,style=this.style;
		        this.style.height = opts.minHeight + 'px';
		        if (this.scrollHeight > opts.minHeight) {
		          if (opts.maxHeight && this.scrollHeight > opts.maxHeight) {
		            height = opts.maxHeight;
		            style.overflowY = 'scroll';
		          } else {
		            height = this.scrollHeight;
		            bottomheight = height
		            
		            style.overflowY = 'hidden';
		          }

		          style.height = height + 'px';
		       
					setTimeout(function(){
						let chatContentHeight = $(window).height()-$('#footermy').height()
						let scrollHeight = $('#chatcon').prop("scrollHeight");
						$("#chatcon").css({"height":chatContentHeight});
						$("#chatcon").animate({scrollTop:scrollHeight},100)
					},700)
		        }else{
		
					setTimeout(function(){
						let chatContentHeight = $(window).height()-$('#footermy').height()
						let scrollHeight = $('#chatcon').prop("scrollHeight");
						$("#chatcon").css({"height":chatContentHeight});
						$("#chatcon").animate({scrollTop:scrollHeight},100)
					},700)
		        }
		      });
		    });
		};
	
		$(".quick").on("click",function(){
			$(".fx-quick,.blackbg").removeClass("hide");
		})
		
		//点击发送按钮
        $(".docomment").on("mousedown",function(){
			addchat($("#chatcontent").val(),1,0);
        });

        //录音按钮
		$(".audio").on("click",function(){
			$(".audio,.input").addClass("hide");
			$(".jianpan,.saybutton").removeClass("hide");
			wx.startRecord({
				success: function(){},
				cancel: function () {
					$.toast("您拒绝授权录音", "cancel");
				}
			});
		});
		
		//键盘
		$(".jianpan").on("click",function(){
			$(".audio,.input").removeClass("hide");
			$(".jianpan,.saybutton").addClass("hide");
		});

		//长按撤回
		$("#chatcon").on("touchstart", ".con", function(event){
			// event.preventDefault();
			var str = $(this).attr('id');
			if(str != undefined){
				chatindex = str.substring(9);
				if(chatindex > 0){
					for (var i = chatindex - 1; i >= 0; i--) {
						$('#txtchehui'+i).addClass("hide");
						$('.txtcontent'+i+' .concon').attr('style','background: ;');
						$('#triangleright'+i).attr('style','border-left: 0.15rem solid #02c6dc;');
					};
					for (var j = chatindex + 1; j <= recallindex; j++) {
						$('#txtchehui'+j).addClass("hide");
						$('.txtcontent'+j+' .concon').attr('style','background: ;');
						$('#triangleright'+j).attr('style','border-left: 0.15rem solid #02c6dc;');
					};
				}
				starttimes = new Date().getTime();
				if(recallTime > 0){          
					timeount = setTimeout(function(){
						$('.txtcontent'+chatindex+' .concon').attr('style','background: #CECECE;');
						$('#triangleright'+chatindex).attr('style','border-left: 0.15rem solid #CECECE;');
						$('#txtchehui'+chatindex).removeClass('hide'); 
					}, 300);
				}else{
					clearInterval(recallname);
				}
			}
			
		});

		//松手不撤回
		$("#chatcon").on('touchend', ".con", function(event){
			// event.preventDefault();
			endtimes = new Date().getTime();
			if((endtimes - starttimes) < 1500){
				endtimes = 0;
				starttimes = 0;
				clearTimeout(timeount);
			}
		});


		$("body").on("click", ".txt-chehui", function(event){
			var str = $(this).attr('id');
			if(str != undefined){
				var insertchatid = $('#insertchatid'+chatindex).val();
				$.ajax({
					url:"<?php  echo $this->createMobileUrl('recallmsg')?>",
					data:{
						fkid:<?php  echo $hasfanskefu['id'];?>,
						insertchatid:insertchatid,
						type:'fans',
					},
					dataType:'json',
					type:'post',        
					success:function(data){
						if(data.error == 0){
							$('#msgright'+chatindex).remove(); 
							$('#recall'+chatindex).removeClass('hide');
							$('.chatcon').attr('style','padding-bottom:0.2rem;');
							$.ajax({   
								url:sendurl,   
								type:'get', 
								data:{
									zhuanjie:insertchatid,
									content:'对方撤回了一条消息',
									msgtype:2,
									yuyintime:0,
									insertchatid:insertchatid,
									toopenid:uid,
								},
								dataType:'jsonp',
								success:function(data){ 
								}
							});
						}else{
							$.alert(data.msg);
						}
					},
				});
			}
			
		});

		$("body").on("click", function(event){
			$('#txtchehui'+chatindex).addClass("hide");
			$('.txtcontent'+chatindex+' .concon').attr('style','background: ;');
			$('#triangleright'+chatindex).attr('style','border-left: 0.15rem solid #02c6dc;');
		});
		
		$(".blackbg").on("click",function(){
			$(".fx-quick,.gzqrcode,.kefuqrcodediv,.pingjiadiv,.blackbg").addClass("hide");
		})
    });
	
	
	//检测是否包含某字符串
	function isContain(str,substr){
  		return new RegExp(substr).test(str);
 	}
	
	//发送消息到数据库
	function addchat(content,type,yuyintime){

		recallTime = 300;
		recallindex++;

		if(cansend == 1){
			cansend = 0;
			$.ajax({   
				 url:"<?php  echo $this->createMobileUrl('addchat')?>",   
				 type:'post', 
				 data:{
					toopenid:touid,
					content:content,
					fkid:<?php  echo $hasfanskefu['id'];?>,
					qudao:'<?php  echo $qudao;?>',
					diaglogid:<?php  echo $diaglogid;?>,
					goodsid:<?php  echo $goodsid;?>,
					type:type,
					yuyintime:yuyintime,
				 },
				 dataType:'json',
				 success:function(data){   
					if(data.error == 0){
						var returnmsg = replace_em(data.content);
						returnmsg = '<div class="time text-c">'+data.datetime+'</div>'
									+'<input type="hidden" id="insertchatid'+recallindex+'" value="'+data.insertchatid+'" />'
									+'<div class="recall hide" id="recall'+recallindex+'">你撤回了一条消息</div>'
									+'<div class="right flex" id="msgright'+recallindex+'">'
										+'<div class="con flex" id="recallmsg'+recallindex+'">'
											+'<div class="kongflex text-r">'+data.yuyincon+'</div>'
											+'<div class="txtcontent'+recallindex+'">'+returnmsg+'</div>'
											+'<div class="triangle-right" id="triangleright'+recallindex+'"></div>'
										+'</div>'
										+'<div class="txt-chehui hide" id="txtchehui'+recallindex+'"><div class="chehui">撤回</div></div>'
										+'<img src="<?php  echo $hasfanskefu['fansavatar'];?>" class="avatar" />'
									+'</div>';
						$('#chatcon').append(returnmsg).animate({scrollTop:10000000},300);
						$('#chatcontent').val("");
						$('#chatcontent').attr('style','height: 30px;');
						$('#footer').attr('style','height: 1rem;position:fixed;bottom:0;');

						recallname = setInterval(function() {
							recallTime--;
						}, 1000);
						
						if(data.jqr == 1){
							var jrqmsg = '<div class="time text-c">'+data.jqrtime+'</div>'
											+'<div class="left flex">'
												+'<img src="'+data.jqravatar+'" class="avatar" />'
												+'<div class="con flex">'
												+'<div class="triangle-left"></div>'
												+'<div class="concon">'+data.jqrcontent+'</div>'
												+'<div class="kongflex"></div>'
												+'</div>'
											+'</div>';
							$('#chatcon').append(jrqmsg).animate({scrollTop:10000000},300);
						}
						
						$.ajax({   
							url:sendurl,   
							type:'get', 
							data:{
								content:content,
								msgtype:type,
								yuyintime:yuyintime,
								zhuanjie:data.insertchatid,
								toopenid:uid,
								newavatar:'<?php  echo $hasfanskefu['fansavatar'];?>',
								newnickname:'<?php  echo $hasfanskefu['fansnickname'];?>',
							},
							dataType:'jsonp',
							success:function(data){ 
							}
						});
					}else{
						$.alert(data.msg);
					}
					cansend = 1;
				 }
			});
		}
	}
	
	//查看QQ表情结果
	function replace_em(str){
		str = str.replace(/\[em_([0-9]*)\]/g,'<img src="<?php echo MD_ROOT;?>/static/arclist/$1.gif" style="width:0.5rem;float:left;" border="0" />');
		return str;
	}
	function domInit(){
		
		var chatContentHeight = $(window).height()-$('#footermy').height();
		var scrollHeight = $('#chatcon').prop("scrollHeight");
		$("#chatcon").css({"height":chatContentHeight}).animate({scrollTop:scrollHeight},300);
		$('.concon').each(function(){
			$(this).html(replace_em($(this).html()));
		});
		$.ajax({
			url:"<?php  echo $this->createMobileUrl('shangxian')?>",
			data:{
				fkid:<?php  echo $hasfanskefu['id'];?>,
				type:'fans',
			},
			dataType:'json',
			type:'post',        
			success:function(data){
			},
		});
    }
	
	function KeyDown(event){
		if (event.keyCode==13){
			event.returnValue=false;
			event.cancel = true;
			addchat($("#chatcontent").val(),2,0);
		}
	}
	
	function playvoice(serverid,obj){
		// $.alert(obj);
		wx.downloadVoice({
			serverId: serverid,
			success: function (res) {
				wx.playVoice({
					localId: res.localId, // 需要播放的音频的本地ID，由stopRecord接口获得
				});
				//刷新语音状态
				$.ajax({   
					url:"<?php  echo $this->createMobileUrl('shuaxinyuyin')?>",   
					type:'post', 
					data:{
						content:serverid,
					},
					dataType:'json',
					success:function(data){ 
						if(data.error == 0){
							obj.remove();
						}
					}
				});
			}
		});
	}
</script>
<script type="text/javascript">
var images = {
	localIds: [],
};
var voice = {
	localId: '',
	serverId: ''
};
$(function(){

	var posStart = 0;//初始化起点坐标  
    var posEnd = 0;//初始化终点坐标  
    var posMove = 0;//初始化滑动坐标

	//按下录音假设全局变量已经在外部定义
	$(".saybutton").on("touchstart",function(event){
		event.preventDefault();
		posStart = 0;
		posStart = event.targetTouches[0].pageY;//获取滑动实时坐标
		posEnd = event.targetTouches[0].pageY;//获取滑动实时坐标
		$(".saybutton").text("松开  结束");
		START = new Date().getTime();
		recordTimer = setTimeout(function(){
			$(".fx-audio").removeClass('hide');
			$(".audio-start").html('<span class="iconfont">&#xe643;</span>');
			$(".audio-text").html('正在录音中...');
			wx.startRecord({
				success: function(){
					localStorage.rainAllowRecord = 'true';
					var num=59;
					name = setInterval(function() {
						if(num <= 10 && num > 0){
							$(".audio-start").html(num);// 你倒计时显示的地方元素
						}
						num--;
						if(num==0){          
							clearInterval(name);
							END = new Date().getTime();
							wx.stopRecord({
								success: function (res) {
									voice.localId = res.localId;
									$('.fx-audio').addClass("hide");
									var yuyintime = (END - START);
									uploadVoice(yuyintime);
								},
								fail: function (res) {
									// $.toast(JSON.stringify(res), "forbidden");
								}
							});
							$(".saybutton").text('按住  说话');
						}
					}, 1000);
				},
				cancel: function () {
					$.toast("您拒绝授权录音", "cancel");
				}
			});
		},300);
	});

	//滑动取消录音
    $(".saybutton").on("touchmove", function(event) {  
        event.preventDefault();  
        posEnd = 0;  
        posEnd = event.targetTouches[0].pageY;//获取滑动实时坐标
        if((posStart - posEnd) < 200){
        	$(".audio-start").html('<span class="iconfont">&#xe643;</span>'); 
			$(".audio-text").html('正在录音中...');
        	$(".saybutton").text("松开  结束");
        }else{
        	$(".fx-audio").removeClass('hide');
			$(".audio-start").html('<span class="iconfont">&#xe680;</span>'); 
			$(".audio-text").html('松开手指，取消发送');
			$(".saybutton").text("松开手指，取消发送");
        }
          
    });

	//松手结束录音
	$(".saybutton").on('touchend', function(event){
		event.preventDefault();
		END = new Date().getTime();
		$(".saybutton").text('按住  说话');
		$('.fx-audio').addClass("hide");
		if((END - START) < 1500){
			END = 0;
			START = 0;
			//小于300ms，不录音
			$.toast("录音时间太短", "forbidden");
			clearTimeout(recordTimer);
			wx.stopRecord();
		}else{
			if((posStart - posEnd) < 200){
				wx.stopRecord({
					success: function (res) {
						voice.localId = res.localId;
						var yuyintime = (END - START);
						uploadVoice(yuyintime);
				    },
				    fail: function (res) {
						// $.toast(JSON.stringify(res), "forbidden");
				    }
				});
			}else{
				clearTimeout(recordTimer);
				wx.stopRecord();
			}
		}
	});
	//上传录音
	function uploadVoice(yuyintime){
		//调用微信的上传录音接口把本地录音先上传到微信的服务器
		//不过，微信只保留3天，而我们需要长期保存，我们需要把资源从微信服务器下载到自己的服务器
		wx.uploadVoice({
			localId: voice.localId, // 需要上传的音频的本地ID，由stopRecord接口获得
			isShowProgressTips: 1, // 默认为1，显示进度提示
			success: function (res) {
				//把录音在微信服务器上的id（res.serverId）发送到自己的服务器供下载。
				addchat(res.serverId,5,yuyintime);
			}
		});
	}
	$('.camera').click(function(){
		wx.chooseImage({
			count: 3, // 最多选3张
			sizeType: ['compressed'], // 可以指定是原图还是压缩图，默认二者都有
			sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
			success: function(res) {
				images.localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
				var i = 0; var length = images.localIds.length;
				var upload = function() {
					wx.uploadImage({
						localId:'' + images.localIds[i],
						isShowProgressTips: 1,
						success: function(res) {
							var serverId = res.serverId;
							$.ajax({   
								 url:"<?php  echo $this->createMobileUrl('getmedia')?>",   
								 type:'post', 
								 data:{
									media_id:serverId,
								 },
								 dataType:'json',
								 success:function(data){   
									if (data.error == 1) {
										$.alert(data.message);
									} else {
										addchat(data.imgurl,4,0);
									}  
								 }
							});
							//如果还有照片，继续上传
							i++;
							if (i < length) {
								upload();
							}
						}
					});                    
				};
				upload();
			}
		});
	});
})

//滚动加载
// var loading = false;  //状态标记
// var count = 2;
// $("#chatcon").pullToRefresh().on("pull-to-refresh", function() {
// 	if(loading) return;
// 	loading = true;
// 	if(count < <?php  echo $allpage;?>){
// 		$('.weui-loadmore').removeClass('hide');
// 		setTimeout(function() {
// 			$.ajax({
// 				url:"<?php  echo $this->createMobileUrl('chatajax')?>",
// 				data:{
// 					page:count,
// 					isajax:1,
// 					toopenid:touid,
// 				},
// 				dataType:'html',
// 				type:'post',        
// 				success:function(data){
// 					if(data != ''){
// 						$('#realcon').prepend(data);
// 						count++;
// 						$('.weui-loadmore').addClass('hide');
// 					}
// 					loading = false;
// 					$("#chatcon").pullToRefreshDone();
// 				},
// 			});
// 		}, 500);   //模拟延迟
// 	}else{
// 		$('.weui-loadmore .weui-loadmore__tips').text('全部数据已经加载完毕');
// 		$('.weui-loadmore').addClass('hide');
// 	}
// });

//病历图片预览previewImage
$('#previewImage').click(function(){
		if (!window.WeixinJSBridge || !WeixinJSBridge.invoke) {
		  document.addEventListener('WeixinJSBridgeReady', ready, false);
		} 

		var thumbs = [];
		if(ready()){

			<?php  if(count($diag_log['diag_thumbs']) >0) { ?>

				<?php 

				 	foreach ($diag_log['diag_thumbs'] as &$value) {

				?>
					thumbs.push("<?php  echo $value;?>");
				<?php 
				 		
				 	}
				?>
				
				wx.previewImage({
				  current: thumbs[0], // 当前显示图片的http链接
				  urls: thumbs // 需要预览的图片http链接列表
				});
			<?php  } ?>

		}else{
			$.alert('图片不支持预览，请确认是否在小程序内');
		}
		
});

//判断是否在小程序内
function ready() {
	var isinto = window.__wxjs_environment === 'miniprogram';// true

 	return isinto;
}

</script>
<script type="text/javascript">
wx.ready(function () {
	wx.hideOptionMenu();
	sharedata = {
		title: '<?php  echo $setting["sharetitle"];?>',
		desc: '<?php  echo $setting["sharedes"];?>',
		link: '<?php  echo $setting["shareurl"];?>',
		imgUrl: '<?php  echo tomedia($setting["sharethumb"]);?>',
		trigger: function (res) {
			//$.alert('用户点击发送给朋友');
		},
		success: function (res) {
			//$.alert('已分享');
		},
		cancel: function (res) {
			//$.alert('已取消');
		},
		fail: function (res) {
			$.alert("分享失败");
		}
	};
	wx.onMenuShareAppMessage(sharedata);
	wx.onMenuShareTimeline(sharedata);
	wx.onMenuShareQQ(sharedata);
	wx.onMenuShareWeibo(sharedata);
	//注册微信播放录音结束事件【一定要放在wx.ready函数内】
	wx.onVoicePlayEnd({
		success: function (res) {

		}
	});
	// wx.onVoiceRecordEnd({
	// 	complete: function (res) {
	// 		voice.localId = res.localId;
	// 		$.alert('录音时间已超过一分钟');
	// 	}
	// });
});
</script>
</html>