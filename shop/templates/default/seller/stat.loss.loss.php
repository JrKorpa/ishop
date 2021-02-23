<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>

<?php include template('layout/stat_loss_search_layout');?>

<div class="stat_fie_box">
   <div class="stat_inn_box">
		<div class="stat_inn_tab">
			<dl>
			    <dt>损益分析</dt>
				<dd class="active"><a href="index.php?act=statistics_loss&op=loss">损益表报</a></dd>	
			</dl>
			
		</div>
		<div class="alert mt10" style="clear:both;">
			<ul class="mt5">
				<li>1、统计符合以下条件的订单：1）订单已发货，包含退货退款</li>
				<li>2、按月、周、天统计店面损益汇总</li>
			</ul>
		</div>
		<div class="alert alert-info mt10" style="clear:both;">
			<ul class="mt5">
			<li>
				<span class="w210 fl h30" style="display:block;">
					<i title="销售总额" class="tip icon-question-sign"></i>
					销售金额：<strong><?php echo ncPriceFormat($output['statcount_arr']['sum_sale_amount']);?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="退货总额" class="tip icon-question-sign"></i>
					退货金额：<strong><?php echo ncPriceFormat($output['statcount_arr']['sum_refund_amount']);?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="销售金额-退货金额-采购成本" class="tip icon-question-sign"></i>
					毛利额：<strong><?php echo ncPriceFormat($output['statcount_arr']['sum_mao_amount']);?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="毛利额/（销售金额-退货金额）" class="tip icon-question-sign"></i>
					毛利率：<strong><?php echo $output['statcount_arr']['sum_mao_lv'];?> %</strong>
				</span>
				
			

			</li>
			</ul>
			<div style="clear:both;"></div>
		</div>
		<div class="highcharts">
			<?php if ($output['stat_json1']){ ?><div class="container" id="container1"></div><?php } ?>
			<?php if ($output['stat_json2']){ ?><div class="container" id="container2"></div><?php } ?>
			
			<div style="clear:both;"></div>
		</div>
		
		<div class="statlist">
		   <div id="statlist1" class=""></div>
		</div>
		<div class="statlist">
		   <div id="statlist2" class=""></div>
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
    <?php if ($output['stat_json1']){ ?>
	 $('#container1').highcharts(<?php echo $output['stat_json1']; ?>);
	<?php } ?>
	<?php if ($output['stat_json2']){ ?>
	 $('#container2').highcharts(<?php echo $output['stat_json2']; ?>);
	<?php } ?>
	
	
	$('#statlist1').load('index.php?act=statistics_loss&op=losscslist&t=<?php echo $output['searchtime'];?>&source_id=<?php echo $_REQUEST['source_id'];?>&fenlei=<?php echo $_REQUEST['fenlei'];?>');
	$('#statlist2').load('index.php?act=statistics_loss&op=lossctlist&t=<?php echo $output['searchtime'];?>&source_id=<?php echo $_REQUEST['source_id'];?>&fenlei=<?php echo $_REQUEST['fenlei'];?>');
	
});
</script>