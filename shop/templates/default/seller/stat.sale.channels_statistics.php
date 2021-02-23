<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>

<?php include template('layout/stat_sale_search_layout');?>

<div class="stat_fie_box">
   <div class="stat_inn_box">
		<div class="stat_inn_tab">
			<dl>
			    <dt>销售分析</dt>
				<dd ><a href="index.php?act=statistics_sale&op=sale">销售趋势</a></dd>	
				<dd ><a href="index.php?act=statistics_sale&op=statistics">销售统计</a></dd>	
				<dd class="active"><a href="index.php?act=statistics_sale&op=channels_statistics">各渠道销售统计</a></dd>	
				<dd><a href="index.php?act=statistics_sale&op=buying">购买分析</a></dd>	
			</dl>
			
		</div>
		<div class="alert mt10" style="clear:both;">
			<ul class="mt5">
				<li>1、符合以下条件的订单即为有效订单：1）订单已支付，包含支付定金，按照第一次支付时间统计</li>
				<li>2、统计图展示了符合搜索条件的有效订单成品、裸钻、赠品的销售金额和数量占比</li>
				<li>3、统计图展示了符合搜索条件的有效订单各渠道来源的销售金额占比</li>
			</ul>
		</div>
		<div class="alert alert-info mt10" style="clear:both;">
			<ul class="mt5">
			<li>
				<span class="w210 fl h30" style="display:block;">
					<i title="有效订单的总金额(元)" class="tip icon-question-sign"></i>
					下单金额：<strong><?php echo $output['statcount_arr']['sum_order_amount'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="新增预约会员总数" class="tip icon-question-sign"></i>
					新增预约会员：<strong><?php echo $output['statcount_arr']['sum_buyer_reservation_num'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="进店会员总数" class="tip icon-question-sign"></i>
					进店会员数：<strong><?php echo $output['statcount_arr']['sum_buyer_toshop_num'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="有效订单的下单会员总数" class="tip icon-question-sign"></i>
					成交会员数：<strong><?php echo $output['statcount_arr']['sum_buyer_deal_num'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="有效订单的总数量" class="tip icon-question-sign"></i>
					下单量：<strong><?php echo $output['statcount_arr']['sum_order_num'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="下单商品总数量" class="tip icon-question-sign"></i>
					下单商品数：<strong><?php echo $output['statcount_arr']['sum_goods_num'];?></strong>
				</span>
				
				<span class="w210 fl h30" style="display:block;">
					<i title="包含商品的平均单价（元）" class="tip icon-question-sign"></i>
					平均价格：<strong><?php echo $output['statcount_arr']['average_price'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="会员的平均单价（元）" class="tip icon-question-sign"></i>
					平均客单价：<strong><?php echo $output['statcount_arr']['average_buyer_price'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="收款总金额(元)" class="tip icon-question-sign"></i>
					收款金额：<strong><?php echo $output['statcount_arr']['sum_rcb_amount'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="退款总金额(元)" class="tip icon-question-sign"></i>
					退款金额：<strong><?php echo $output['statcount_arr']['sum_refund_amount'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="实收总金额(元)" class="tip icon-question-sign"></i>
					实收金额：<strong><?php echo $output['statcount_arr']['sum_real_amount'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="应收总金额（元）" class="tip icon-question-sign"></i>
					应收尾款：<strong><?php echo $output['statcount_arr']['sum_pay_amount'];?></strong>
				</span>

			</li>
			</ul>
			<div style="clear:both;"></div>
		</div>
		<div class="highcharts">
		<?php 
		  for($i = 0;$i < $output['stocklist_num']; $i++ ){
		?>
			<?php if ($output['stat_cs_json'.$i]){ ?><div class="container" id="stat_cs_json<?php echo $i; ?>"></div><?php } ?>
			<?php if ($output['stat_ct_json'.$i]){ ?><div class="container" id="stat_ct_json<?php echo $i; ?>"></div><?php } ?>
		 <?php } ?>
			<div style="clear:both;"></div>
		</div>
		
		
		
   </div>
</div>







<script charset="utf-8" type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/highcharts/highcharts.js"></script>
<script type="text/javascript" src="<?php echo SHOP_RESOURCE_SITE_URL; ?>/js/ui.core.js"></script>
<script type="text/javascript" src="<?php echo SHOP_RESOURCE_SITE_URL; ?>/js/ui.tabs.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.ajaxContent.pack.js"></script>
<script type="text/javascript" src="<?php echo SHOP_RESOURCE_SITE_URL;?>/js/statistics.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js"></script>

<script type="text/javascript">

$(function(){
	<?php 
	  for($i = 0;$i < $output['stocklist_num']; $i++ ){
	?>
		<?php if ($output['stat_cs_json'.$i]){ ?>
		 $('#stat_cs_json<?php echo $i; ?>').highcharts(<?php echo $output['stat_cs_json'.$i]; ?>);
		<?php } ?>
		<?php if ($output['stat_ct_json'.$i]){ ?>
		 $('#stat_ct_json<?php echo $i; ?>').highcharts(<?php echo $output['stat_ct_json'.$i]; ?>);
		<?php } ?>
	  <?php } ?>
	


});
</script>