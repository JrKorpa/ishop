<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>

<?php include template('layout/stat_achievement_search_layout');?>

<div class="stat_fie_box">
   <div class="stat_inn_box">
		<div class="stat_inn_tab">
			<dl>
			    <dt>业绩统计</dt>
				<dd class="active"><a href="index.php?act=statistics_achievement_all&op=achievement">统计表(总)</a></dd>	
				<dd><a href="index.php?act=statistics_achievement_all&op=achievement_yiye">异业客户(总)</a></dd>	
				<dd ><a href="index.php?act=statistics_achievement_all&op=achievement_ziren">自然进店(总)</a></dd>	
					
			</dl>
			
		</div>
		
		<div style="overflow-x:scroll;">
		  <div id="statlist1" class="" style="width:1800px;"></div>
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

	$('#statlist1').load('index.php?act=statistics_achievement&op=achievement_list&start_date=<?php echo $output['search_arr']['start_date'];?>&end_date=<?php echo $output['search_arr']['end_date'];?>');
	
});
</script>

