<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>

<!--<div class="alert mt15 mb5"><strong>操作提示：</strong>
  <ul>
    <li>1、关联版式可以把预设内容插入到商品描述的顶部或者底部，方便商家对商品描述批量添加或修改。</li>
  </ul>
</div>-->
<form method="get" action="index.php" >
<input type="hidden" name="act" value="store_pay_action">
<input type="hidden" name="op" value="index" />
<table class="search-form">
    <tr>
      
      <th>订单号</th>
      <td class="w200"><input type="text" class="text w140" name="order_sn" value="<?php echo $_GET['order_sn']; ?>"/></td>
	  
	  <th>收款时间</th>
	  <td><input type="text" class="text w70 pay_date"   name="min_pay_date"  value="<?php echo $_GET['min_pay_date']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label>  至 <input type="text" class="text w70 pay_date"  name="max_pay_date"  value="<?php echo $_GET['max_pay_date']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label></td>
	  <td></td>
	  
	 </tr>
	 <tr>
      <th>收款方式</th>
      <td class="w80">
        <select id="pay_code" name="pay_code" class="select w160" >
		       <option value="">请选择</option>
			  <?php
				  foreach ($output['payment_list'] as $payment){
					  if($payment['payment_code'] == $_GET['pay_code']){
						  echo '<option selected value="'.$payment["payment_code"].'">'.$payment["payment_name"].'</option>';
					  }else{
						  echo '<option  value="'.$payment["payment_code"].'">'.$payment["payment_name"].'</option>';
					  }
					  
				  }
			  ?>
		 </select>
      </td>
	  
	  
	  
      <th>支付流水号</th>
      <td class="w200"><input type="text" class="text w140" name="pay_sn" value="<?php echo $_GET['pay_sn']; ?>"/></td>
      <td class="w70 tc"><label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" /></label></td>
	  <td class="w70 tc">
	  <input type="hidden" id="export_type" name="export_type" data-param='{"url":"<?php echo $output['actionurl'];?>&exporttype=excel"}' value="excel"/>
	  <label class="submit-border"><input type="button" id="export_btn" class="submit" value="导出Excel" /></label>
	  </td>
    </tr>
</table>
</form>
<table class="ncsc-default-table">
  <thead>
    <tr>
      <th class="w30"></th>
      <th class="w100">订单号</th>
      <th class="w100">收款金额</th>
      <th class="w100">收款方式</th>
      <th class="w100">收款时间</th>
      <th class="w100">收款人</th>
      <th class="w100">支付流水号</th>
      <th class="w100">客户姓名</th>
      <th class="w100">操作时间</th>
      <th class="w100">批注</th>
    
    </tr>
    <?php if (!empty($output['diamondlist'])) { ?>
    <!--<tr>
      <td class="tc"><input type="checkbox" id="all" class="checkall"/></td>
      <td colspan="10"><label for="all" ><?php echo $lang['nc_select_all'];?></label>
        <a href="javascript:void(0);" nc_type="batchbutton" uri="<?php echo urlShop('diamond_info', 'drop_plate');?>" name="p_id" confirm="<?php echo $lang['nc_ensure_del'];?>" class="ncbtn-mini"><i class="icon-trash"></i><?php echo $lang['nc_del'];?></a>
      </td>
    </tr>-->
    <?php } ?>
  </thead>
  <tbody id="datatable">
    <?php if (!empty($output['order_pay_action_list'])) { ?>
    <?php foreach($output['order_pay_action_list'] as $val) { ?>
    <tr class="bd-line">
      <td></td>
      <td><?php echo $val['o_order_sn']; ?></td>
      <td><?php echo $val['deposit'];?></td>
      <td><?php echo $val['pay_type'];?></td>
      <td><?php echo $val['pay_date'];?></td>
      <td><?php echo $val['created_name'];?></td>
      <td><?php echo $val['pay_sn']; ?></td>
      <td><?php echo $val['buyer_name'];?></td>
      <td><?php echo $val['create_date'];?></td>
      <td><?php echo $val['remark'];?></td>
    </tr>
    <?php } ?>
	
	<tr ><td></td><td style="color:red;">总记</td><td style="color:red;"><?php  echo $output['sum_pay_price']; ?></td></tr>
    <?php } else { ?>
    <tr>
      <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
    </tr>
    <?php } ?>
  </tbody>
  <tfoot>
    <?php if (!empty($output['order_pay_action_list'])) { ?>
    <!--<tr>
      <th class="tc"><input type="checkbox" id="all" class="checkall"/></th>
      <th colspan="10"><label for="all" ><?php echo $lang['nc_select_all'];?></label>
        <a href="javascript:void(0);" nc_type="batchbutton" uri="<?php echo urlShop('diamond_info', 'drop_plate');?>" name="p_id" confirm="<?php echo $lang['nc_ensure_del'];?>" class="ncbtn-mini"><i class="icon-trash"></i><?php echo $lang['nc_del'];?></a>
       </th>
    </tr>-->
    <tr>
      <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
    </tr>
    <?php } ?>
  </tfoot>
</table>
<script charset="utf-8" type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<script type="text/javascript">
$(function(){
	$('.pay_date').datepicker({dateFormat: 'yy-mm-dd',maxDate: 0,/*minDate: -5 */}); 
	//导出图表
    $("#export_btn").click(function(){
        var item = $("#export_type");
        var type = $(item).val();
        if(type == 'excel'){
        	download_excel(item);
        }
    });
});
//导出Excel
function download_excel(obj){
	var tablehtml = $("#datatable").html();
	if(!tablehtml){
		showDialog('暂时没有数据');
		return false;
	}
	var data = $(obj).attr('data-param');
	if(data == undefined  || data.length<=0){
		showDialog('参数错误');
		return false;
	}
	eval("data = "+data);
	go(data.url);
}
</script>