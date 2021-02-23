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
				<dd><a href="index.php?act=statistics_sale&op=channels_statistics">各渠道销售统计</a></dd>	
				<dd class="active"><a href="index.php?act=statistics_sale&op=buying">购买分析</a></dd>	
			</dl>
			
		</div>
		<div class="alert mt10" style="clear:both;">
			<ul class="mt5">
				<li>1、符合以下条件的订单即为有效订单：1）订单已支付，包含支付定金，按照下单时间时间统计</li>
				<li>2、点击“设置价格区间”进入设置价格区间页面，客单价分布图将根据您设置的价格区间进行分布统计</li>
				<li>3、“购买时段分布”统计图展示符合搜索条件的有效订单在各个时间段的分布情况，为工作时间的合理安排提供依据</li>
			</ul>
		</div>
		

		<div class="alert alert-info"><strong>客单价分布</strong>（<a href="index.php?act=statistics_general&op=orderprange" target="_blank">设置价格区间</a>）</div>
		<table class="ncsc-default-table">
		  <tbody>
			<tr id="row_0">
				<td class="tl">
					<?php if ($output['guestprice_statjson']){ ?>
					<div id="container_guestprice"></div>
					<?php } else { ?>
					<div class="tc h50 mt10">查看分布情况前，请先设置价格区间。<a href="index.php?act=statistics_general&op=orderprange" target="_blank">马上设置</a></div>
					<?php }?>
				</td>
			</tr>
		  </tbody>
		</table>

		<div class="alert alert-info"><strong>购买时段分布</strong></div>
		<table class="ncsc-default-table">
		  <tbody>
			<tr id="row_0">
				<td class="tl">
					<div id="container_hour"></div>
				</td>
			</tr>
		  </tbody>
		</table>
  </div>
</div>

<script charset="utf-8" type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/highcharts/highcharts.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js"></script>

<script type="text/javascript">

	<?php if ($output['guestprice_statjson']){ ?>
	$('#container_guestprice').highcharts(<?php echo $output['guestprice_statjson'];?>);
	<?php } ?>
	$('#container_hour').highcharts(<?php echo $output['hour_statjson'];?>);

</script>