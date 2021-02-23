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
				<dd ><a href="index.php?act=statistics_achievement&op=achievement">统计表</a></dd>	
				<dd class="active"><a href="index.php?act=statistics_achievement&op=achievement_yiye">异业客户</a></dd>	
				<dd><a href="index.php?act=statistics_achievement&op=achievement_ziren">自然进店</a></dd>	
					
			</dl>
			
		</div>
		<div class="alert mt10" style="clear:both;">
			<ul class="mt5">
				<li></li>
				
			</ul>
		</div>
		<div class="alert alert-info mt10" style="clear:both;">
			<ul class="mt5">
			<li>
				<span class="w210 fl h30" style="display:block;">
					<i title="合计" class="tip icon-question-sign"></i>
					合计：<strong><?php echo $output['statcount_arr']['shop_num'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="A类客户" class="tip icon-question-sign"></i>
					A类客户：<strong><?php echo $output['statcount_arr']['A_shop_num'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="B类客户" class="tip icon-question-sign"></i>
					B类客户：<strong><?php echo $output['statcount_arr']['B_shop_num'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="C类客户" class="tip icon-question-sign"></i>
					C类客户：<strong><?php echo $output['statcount_arr']['C_shop_num'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="D类客户" class="tip icon-question-sign"></i>
					D类客户：<strong><?php echo $output['statcount_arr']['D_shop_num'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="E类客户" class="tip icon-question-sign"></i>
					E类客户：<strong><?php echo $output['statcount_arr']['E_shop_num'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="成交" class="tip icon-question-sign"></i>
					成交：<strong><?php echo $output['statcount_arr']['num'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="转化率" class="tip icon-question-sign"></i>
					转化率：<strong><?php echo $output['statcount_arr']['cconversion_rate'];?> %</strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="A类客户占比" class="tip icon-question-sign"></i>
					A类客户占比：<strong><?php echo $output['statcount_arr']['A_cconversion_rate'];?> %</strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="E类客户占比" class="tip icon-question-sign"></i>
					E类客户占比：<strong><?php echo $output['statcount_arr']['E_cconversion_rate'];?> %</strong>
				</span>
			

			</li>
			</ul>
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

