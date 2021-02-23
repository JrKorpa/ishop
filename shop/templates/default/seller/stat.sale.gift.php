<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />

<!-- <div class="alert mt10" style="clear:both;">
	 <ul class="mt5">

      </ul>
</div>-->
<form method="get" action="index.php" target="_self" id="formSearch">
  <table class="search-form">
    <input type="hidden" name="act" value="statistics_sale_gift" />
    <input type="hidden" name="op" value="index" />
   
    <tr>
    	<td class="tr">
		     
    		
			<div class="fr">
			
			&nbsp;&nbsp;订单审核时间
			    <input type="text" class="text w70" name="start_time" id="pay_date1" value="<?php echo isset($_GET['start_time'])?$_GET['start_time']:$output['start_time']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label>
				至 
				<input type="text" class="text w70" name="end_time" id="pay_date2" value="<?php echo isset($_GET['end_time'])?$_GET['end_time']:$output['end_time'];; ?>" /><label class="add-on"><i class="icon-calendar"></i></label>
			     <label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_common_search'];?>" /></label>
			</div>
			<div class="fr">&nbsp;&nbsp;赠品款号
    			<input type="text" class="text w150" name="style_sn" value="<?php echo $_GET['style_sn']; ?>" />
    			
    		</div>
			
		
    		
    	</td>
    </tr>
  </table>
</form>
<table class="ncsc-default-table">
  <thead>
    <tr class="sortbar-array">
  
      <th>名称</th>
      <th>款号</th>
      <th>手寸</th>
	  <th>需求量</th>

    </tr>
  </thead>
  <tbody>
    <?php if (!empty($output['goodslist']) && is_array($output['goodslist'])) { ?>
    <?php foreach($output['goodslist'] as $v) { ?>
    <tr class="bd-line">
     
      <td class="tl"><span class="over_hidden w400 h20"><?php echo $v['goods_name'];?></span></td>
      <td><?php echo $v['style_sn'];?></td>
      <td><?php echo $v['zhiquan'];?></td>
      <td><?php echo $v['xuqiu'];?></td>
      
    </tr>
    <?php }?>
    <?php } else { ?>
    <tr>
      <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
    </tr>
    <?php } ?>
  </tbody>
  <?php if (!empty($output['goodslist']) && is_array($output['goodslist'])) { ?>
  <tfoot>
    <tr>
      <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
    </tr>
  </tfoot>
  <?php } ?>
</table>
<table class="ncsc-default-table">
	<tbody>
    	<tr>
    		<div id="goodsinfo_div" class="close_float" style="text-align:center;"></div>
    	</tr>
	</tbody>
</table>
<script charset="utf-8" type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" ></script>
<script charset="utf-8" type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common_select.js" ></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.ajaxContent.pack.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/highcharts/highcharts.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js"></script>

<script type="text/javascript">
$(function(){
	
   $('#pay_date1').datepicker({dateFormat: 'yy-mm-dd',maxDate: 0,/*minDate: -5 */});  
   $('#pay_date2').datepicker({dateFormat: 'yy-mm-dd',maxDate: 0,/*minDate: -5 */});
 
  
   
});

</script>