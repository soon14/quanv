<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>


<div class="page-heading"> <h2>医生账单</h2> </div>

    <form action="./index.php" method="get" class="form-horizontal table-search" role="form">

        <input type="hidden" name="c" value="site" />

        <input type="hidden" name="a" value="entry" />

        <input type="hidden" name="m" value="rr_vv3" />

        <input type="hidden" name="do" value="web" />

        <input type="hidden" name="r" value="finance.doctorsbill" />

        <div class="page-toolbar row m-b-sm m-t-sm">

            <div class="col-sm-4">

                <div class="input-group-btn">

			        <button class="btn btn-default btn-sm"  type="button" data-toggle='refresh'><i class='fa fa-refresh'></i></button>

				    <!-- <?php  echo tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d H:i', $starttime),'endtime'=>date('Y-m-d H:i', $endtime)),true);?> -->

			    </div>

            </div>

            <div class="col-sm-6 pull-right">


                <div class="input-group">

                    <input type="text" class="form-control input-sm"  name="realname" value="<?php  echo $_GPC['realname'];?>" placeholder="可搜索昵称/姓名"/>

    				<span class="input-group-btn">


                        <button class="btn btn-sm btn-primary" type="submit"> 搜索</button>

                        <button type="submit" name="export" value="1" class="btn btn-success btn-sm">导出</button>

    				</span>

                </div>

            </div>

        </div>

  </form>


        <table class="table table-hover table-responsive">

            <thead class="navbar-inner">

                <tr>

                    <th style="width:120px;">医生信息</th>

                    <th style="width:100px;">账单日期</th>

                    <th style="width:100px;">结算金额</th>

                    <th style="width:180px;">银行卡</th>

                    <th style="width:150px;">持卡人/联系方式</th>

                    <th style="width:80px;">状态</th>

                    <th style="width:100px;">操作</th>

                </tr>

            </thead>

            <tbody>

                <?php  if(is_array($result)) { foreach($result as $row) { ?>

                <tr rel="pop" data-title="ID: <?php  echo $row['openid'];?> ">

                    <td  >

			            <div  >

                        	<?php  if(!empty($row['avatar'])) { ?>

                            <img src='<?php  echo tomedia($row['avatar'])?>' style='width:30px;height:30px;padding1px;border:1px solid #ccc' />

                            <?php  } ?>

                            <?php  if(strexists($row['openid'],'sns_wa_')) { ?>

                                <i class="icon icon-wxapp" data-toggle="tooltip" data-placement="top" data-original-title="小程序注册" style="color: #7586db;"></i>

                            <?php  } ?>


                            <?php  if(empty($row['realname'])) { ?><?php  echo $row['nickname'];?><?php  } else { ?><?php  echo $row['realname'];?><?php  } ?>

                        </div>

                    </td>

                    <td>
                        <?php  echo $row['date'];?>
                    </td>

                    <td>￥<?php  echo $row['billprice'];?></td>

                    <td><?php  echo $row['bankname'];?><br/><?php  echo $row['bankcard'];?></td>

                    <td><?php  echo $row['cardname'];?><br/><?php  echo $row['mobile'];?></td>

                    <td> 

                        <label class='label <?php  if($row['isbill']==1) { ?>label-success<?php  } else { ?>label-default<?php  } ?>'>

                        <?php  if($row['isbill']==0) { ?>未结算<?php  } else if($row['isbill']==2) { ?>拒绝<?php  } else { ?>已结算<?php  } ?>

                        </label>

					</td>

                    <td  style="overflow:visible;">

                        <div class="btn-group btn-group-sm" >

                            <ul class="dropdown-menu-left" role="menu" style='z-index: 9999'>

                                <!-- <?php if(cv('member.doctors.detail')) { ?>

                                    <li><a href="<?php  echo webUrl('finance/doctorsbill/detail',array('openid' => $row['openid'], 'datetime' => $row['datetime']));?>" title="详情"><i class='fa fa-edit'></i> 详情</a></li>

                                <?php  } ?> -->

                                <?php if(cv('member.doctors.edit')) { ?>
                                    
                                    <?php  if($row['isbill']==0) { ?>
                                        <li>
                                            <a class="btn btn-primary btn-xs" data-toggle="ajaxPost" href="<?php  echo webUrl('finance/doctorsbill/addBill',array('openid' => $row['openid'], 'billprice' => $row['billprice'], 'tradedate' => $row['date'], 'status' => 1));?>" data-confirm="<?php  if(empty($row['bankcard'])) { ?>该医生还未绑定银行卡，确认结算吗？<?php  } else { ?>确认结算吗？<?php  } ?>">结算</a>
                                        </li>
                                        
                                        <!-- <li>
                                            <a class="btn btn-danger btn-xs" data-toggle="ajaxPost" href="<?php  echo webUrl('finance/doctorsbill/addBill',array('openid' => $row['openid'], 'billprice' => $row['billprice'], 'tradedate' => $row['date'], 'status' => 2));?>" data-confirm="确认拒绝结算吗？">拒绝</a>
                                        </li> -->

                                    <?php  } ?>

                                <?php  } ?>

                            </ul>

                        </div>

                    </td>

                </tr>

                <?php  } } ?>

            </tbody>

        </table>

        <?php  echo $pager;?>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

