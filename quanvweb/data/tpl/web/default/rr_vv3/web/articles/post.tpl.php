<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-heading"> <h2>文章编辑</h2> </div>



<form <?php  if('articles.edit') { ?>action="" method='post'<?php  } ?> class='form-horizontal form-validate'>

 <input type="hidden" name="referer" value="<?php  echo referer()?>" />

	<div class="tabs-container">



		<div class="tabs">

			<ul class="nav nav-tabs">

				<li class="active"><a data-toggle="tab" href="#tab-basic" aria-expanded="true"> 文章信息</a></li>

			</ul>

			<div class="tab-content ">

				<div id="tab-basic" class="tab-pane active">

					<div class="form-group">

					    <label class="col-sm-2 control-label">文章作者</label>

					    <div class="col-sm-9 col-xs-12">

					        <?php if(cv('articles.edit')) { ?>

					        <select name='member' class='form-control'>
            
					            <option value=''>请选择作者</option>
					            <?php  if(is_array($members)) { foreach($members as $member) { ?>

					                <option value='<?php  echo $member['openid'];?>'><?php  if(empty($member['realname'])) { ?><?php  echo $member['nickname'];?><?php  } else { ?><?php  echo $member['realname'];?><?php  } ?></option>

					            <?php  } } ?>

					        </select>

					        <?php  } ?>

					    </div>

					</div>

					<div class="form-group">

					    <label class="col-sm-2 control-label">文章标题</label>

					    <div class="col-sm-9 col-xs-12">

					        <?php if(cv('articles.edit')) { ?>

					        <input type="text" name="title" class="form-control" value="" />

					        <?php  } else { ?>

					        <div class='form-control-static'></div>

					        <?php  } ?>

					    </div>

					</div>

					<div class="form-group">

					        <label class="col-sm-2 control-label">文章封面</label>
							
					        <div class="col-sm-9 col-xs-12">    
					            <?php  echo tpl_form_field_image('cover_url','')?>
					        </div>
					</div>


					<div class="form-group">

					    <label class="col-sm-2 control-label">文章简介</label>

					    <div class="col-sm-9 col-xs-12">

					        <?php if(cv('articles.edit')) { ?>

					        <textarea name="article_introduction" style="height:100px;" class='form-control'></textarea>

					        <?php  } ?>

					    </div>

					</div>

					<div class="form-group">

					    <label class="col-sm-2 control-label">文章价格</label>

					    <div class="col-sm-5 col-xs-12">

							<div class="input-group">
							  <div class="input-group-addon">￥</div>
							  <input type="text" name="money" class="form-control"  placeholder="价格" value="">
							  <div class="input-group-addon">元</div>
							</div>

					    </div>

					</div>

					<div class="form-group">

					    <label class="col-sm-2 control-label">文章内容</label>
							
					    <div class="col-sm-9 col-xs-12">

					        <?php  echo tpl_ueditor('content','')?>

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

			<?php if(cv('articles.edit')) { ?>

			<input type="submit"  value="提交" class="btn btn-primary" />

			<?php  } ?>

			<input type="button" class="btn btn-primary" name="submit" onclick="history.go(-1)" value="返回列表" <?php if(cv('articles.edit')) { ?>style='margin-left:10px;'<?php  } ?> />

		</div>

	</div>



</form>



<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

