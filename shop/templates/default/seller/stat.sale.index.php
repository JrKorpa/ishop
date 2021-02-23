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
				<dd class="active"><a href="index.php?act=statistics_sale&op=sale">销售趋势</a></dd>	
				<dd><a href="index.php?act=statistics_sale&op=statistics">销售统计</a></dd>	
				<dd><a href="index.php?act=statistics_sale&op=channels_statistics">各渠道销售统计</a></dd>	
				<dd><a href="index.php?act=statistics_sale&op=buying">购买分析</a></dd>	
			</dl>
			
		</div>
		<div class="alert mt10" style="clear:both;">
			<ul class="mt5">
				<li>1、<?php echo $lang['stat_validorder_explain'];?></li>
				<li>2、统计图展示了符合搜索条件的有效订单中的下单总金额和下单数量在时间段内的走势情况及与前一个时间段的趋势对比</li>
				<li>3、新增订单明细按第一次付款时间统计，发货订单明细按发货时间统计，退货订单明细按退款/退货记录通过时间统计，可以点击“导出Excel”将订单记录导出为Excel文件</li>
			</ul>
		</div>
		<div class="alert alert-info mt10" style="clear:both;">
			<ul class="mt5">
			<li>
				<span class="w210 fl h30" style="display:block;">
					<i title="店铺符合搜索条件的订单总金额" class="tip icon-question-sign"></i>
					总下单金额：<strong><?php echo $output['statcount_arr']['orderamount'].$lang['currency_zh'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="店铺符合搜索条件的订单数量" class="tip icon-question-sign"></i>
					总下单量：<strong><?php echo $output['statcount_arr']['ordernum'];?></strong>
				</span>
			</li>
			</ul>
			<div style="clear:both;"></div>
		</div>

		<div id="stat_tabs" class="ui-tabs" style="min-height:500px;padding-top:10px;">
			<div class="tabmenu">
				<ul class="tab pngFix">
					<li><a href="#orderamount_div" nc_type="showdata" data-param='{"type":"orderamount"}'>下单金额</a></li>
					<li><a href="#ordernum_div" nc_type="showdata" data-param='{"type":"ordernum"}'>下单量</a></li>
				</ul>
			</div>
			<!-- 下单金额 -->
			<div id="orderamount_div" style="width:910px;"></div>
			<!-- 下单量 -->
			<div id="ordernum_div" style="width:910px;"></div>
		</div>
		
		<div style="overflow-x:scroll;">
		  <div id="statlist1" class="" style="width:3000px;"></div>
		</div>
		
		<div style="overflow-x:scroll;">
		  <div id="statlist2" class="" style="width:3000px;"></div>
		</div>
		
		<div style="overflow-x:scroll;">
		  <div id="statlist3" class="" style="width:3000px;"></div>
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
	$('#ordernum_div').highcharts(<?php echo $output['stat_json']['ordernum'];?>);
	$('#orderamount_div').highcharts(<?php echo $output['stat_json']['orderamount'];?>);
	$('#statlist1').load('index.php?act=statistics_sale&op=salelist&t=<?php echo $output['searchtime'];?>&order_state=<?php echo $_REQUEST['order_state'];?>&is_zp=<?php echo $_REQUEST['is_zp'];?>&source_id=<?php echo $_REQUEST['source_id'];?>&fenlei=<?php echo $_REQUEST['fenlei'];?>&seller_id=<?php echo $_REQUEST['seller_id'];?>');
	$('#statlist2').load('index.php?act=statistics_sale&op=send_salelist&t=<?php echo $output['searchtime'];?>&order_state=<?php echo $_REQUEST['order_state'];?>&is_zp=<?php echo $_REQUEST['is_zp'];?>&source_id=<?php echo $_REQUEST['source_id'];?>&fenlei=<?php echo $_REQUEST['fenlei'];?>&seller_id=<?php echo $_REQUEST['seller_id'];?>');
	$('#statlist3').load('index.php?act=statistics_sale&op=refund_salelist&t=<?php echo $output['searchtime'];?>&order_state=<?php echo $_REQUEST['order_state'];?>&is_zp=<?php echo $_REQUEST['is_zp'];?>&source_id=<?php echo $_REQUEST['source_id'];?>&fenlei=<?php echo $_REQUEST['fenlei'];?>&seller_id=<?php echo $_REQUEST['seller_id'];?>');
});
</script>