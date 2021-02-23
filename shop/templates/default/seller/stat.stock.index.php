<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>

<form method="get" action="index.php" target="_self">
  <table class="search-form">
    <input type="hidden" name="act" value="statistics_sale" />
    <input type="hidden" name="op" value="sale" />
    <tr>
    	<td class="tr">
    		<div class="fr">
    			<label class="submit-border"></label>
    		</div>
    		
    	
    	</td>
    </tr>
  </table>
</form>

<div class="stat_fie_box">
   <div class="stat_inn_box">
		<div class="stat_inn_tab">
			<dl>
			    <dt>库存分析</dt>
				<dd class="active"><a href="index.php?act=statistics_stock&op=stock">总库存统计</a></dd>	
				<dd><a href="index.php?act=statistics_stock&op=waiting_stock">待取库存统计</a></dd>	
				<dd><a href="index.php?act=statistics_stock&op=sale_stock">可销售库存统计</a></dd>	
			</dl>
			
		</div>
		<div class="alert mt10" style="clear:both;">
			<ul class="mt5">
				<li>1、统计商品总库存及状态的占比情况</li>
				<li>2、待取：货品绑定付过款的订单且未发货</li>
			</ul>
		</div>
		<div class="alert alert-info mt10" style="clear:both;">
			<ul class="mt5">
			<li>
				<span class="w270 fl h30" style="display:block;">
					<i title="库存总数量" class="tip icon-question-sign"></i>
					库存数量：<strong><?php echo $output['statcount_arr']['sum_num'];?>件</strong>
				</span>
				<span class="w270 fl h30" style="display:block;">
					<i title="库存总成本" class="tip icon-question-sign"></i>
					库存金额：<strong><?php echo $output['statcount_arr']['sum_price'].$lang['currency_zh'];?></strong>
				</span>
				<span class="w270 fl h30" style="display:block;">
					<i title="销售数量" class="tip icon-question-sign"></i>
					可销售数量：<strong><?php echo $output['statcount_arr']['sale_sum_num'];?>件</strong>
				</span>
				<span class="w270 fl h30" style="display:block;">
					<i title="销售成本" class="tip icon-question-sign"></i>
					可销售成本：<strong><?php echo $output['statcount_arr']['sale_sum_price'].$lang['currency_zh'];?></strong>
				</span>
				<span class="w270 fl h30" style="display:block;">
					<i title="待取货品数量" class="tip icon-question-sign"></i>
					待取货品数量：<strong><?php echo $output['statcount_arr']['waiting_sum_num'];?>件</strong>
				</span>
				<span class="w270 fl h30" style="display:block;">
					<i title="待取货品的成本" class="tip icon-question-sign"></i>
					待取成本：<strong><?php echo $output['statcount_arr']['waiting_sum_price'].$lang['currency_zh'];?></strong>
				</span>
				
				<span class="w270 fl h30" style="display:block;">
					<i title="已到货的待取订单应收金额" class="tip icon-question-sign"></i>
					待取订单尾款：<strong><?php echo $output['statcount_arr']['order_sale_sum_price'].$lang['currency_zh'];?></strong>
				</span>

			</li>
			</ul>
			<div style="clear:both;"></div>
		</div>
		<div class="highcharts">
			<?php if ($output['stat_json1']){ ?><div class="container" id="container1"></div><?php } ?>
			<?php if ($output['stat_json2']){ ?><div class="container" id="container2"></div><?php } ?>
			<?php if ($output['stat_json3']){ ?><div class="container" id="container3"></div><?php } ?>
			<div style="clear:both;"></div>
		</div>
		
		<div class="statlist">
		   <div id="statlist" class=""></div>
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
	<?php if ($output['stat_json3']){ ?>
	  $('#container3').highcharts(<?php echo $output['stat_json3']; ?>);
	<?php } ?>


	
	$('#statlist').load('index.php?act=statistics_stock&op=stocklist');
});
</script>