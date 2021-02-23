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
				<dd class="active"><a href="index.php?act=statistics_achievement&op=achievement">统计表</a></dd>	
				<dd ><a href="index.php?act=statistics_achievement&op=achievement_yiye">异业客户</a></dd>	
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
					<i title="任务(元)" class="tip icon-question-sign"></i>
					任务(元)：<strong><?php echo $output['statcount_arr']['task'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="销售(元)" class="tip icon-question-sign"></i>
					本月销售(元)：<strong><?php echo $output['statcount_arr']['month_amount'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="达成比" class="tip icon-question-sign"></i>
					达成比：<strong><?php echo $output['statcount_arr']['achieve_ratio'];?> %</strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="销售(元)" class="tip icon-question-sign"></i>
					销售(元)：<strong><?php echo $output['statcount_arr']['amount'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="进店量" class="tip icon-question-sign"></i>
					进店量：<strong><?php echo $output['statcount_arr']['shop_num'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="成交单数" class="tip icon-question-sign"></i>
					成交单数：<strong><?php echo $output['statcount_arr']['num'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="转化率" class="tip icon-question-sign"></i>
					转化率：<strong><?php echo $output['statcount_arr']['cconversion_rate'];?> %</strong>
				</span>
				
				<span class="w210 fl h30" style="display:block;">
					<i title="异业进店量" class="tip icon-question-sign"></i>
					异业进店量：<strong><?php echo $output['statcount_arr']['yiye_shop_num'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="异业成交单数" class="tip icon-question-sign"></i>
					异业成交单数：<strong><?php echo $output['statcount_arr']['yiye_num'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="异业转化率" class="tip icon-question-sign"></i>
					异业转化率：<strong><?php echo $output['statcount_arr']['yiye_cconversion_rate'];?> %</strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="自然进店" class="tip icon-question-sign"></i>
					自然进店：<strong><?php echo $output['statcount_arr']['ziren_shop_num'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="自然成交单数" class="tip icon-question-sign"></i>
					自然成交单数：<strong><?php echo $output['statcount_arr']['ziren_num'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="自然转化率" class="tip icon-question-sign"></i>
					自然转化率：<strong><?php echo $output['statcount_arr']['ziren_cconversion_rate'];?> %</strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="老顾客及转介绍成单数" class="tip icon-question-sign"></i>
					老顾客及转介绍成单数：<strong><?php echo $output['statcount_arr']['laoguke_num'];?></strong>
				</span>
				<span class="w210 fl h30" style="display:block;">
					<i title="老顾客及转介绍成交占比" class="tip icon-question-sign"></i>
					老顾客及转介绍成交占比：<strong><?php echo $output['statcount_arr']['laoguke_num_rate'];?> %</strong>
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

