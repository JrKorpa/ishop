<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<div class="search">
<form method="get" id="searchForm">
<input type="hidden" name="act" value="erp_bill" />
<input type="hidden" name="op" value="show_bill_goods" />
<input type="hidden" name="bill_id" value="<?php echo $_GET['bill_id']?>" />
<input type="hidden" name="menu_key" value="<?php echo $_GET['menu_key']?>" />
<table class="search-form" style="color:blue">
    <tr>
	  <th style="text-align:left;padding-left:10px">
		  <label><input type="radio" name="pandian_status" <?php echo $_GET['pandian_status']==""?'checked':''?> value="" />盘亏/盘盈/调整<label>&nbsp;
		  <label><input type="radio" name="pandian_status" <?php echo $_GET['pandian_status']=="1"?'checked':''?> value="1" />盘亏<label>&nbsp;
		  <label><input type="radio" name="pandian_status" <?php echo $_GET['pandian_status']=="2"?'checked':''?> value="2" />盘盈<label>&nbsp;
		  <label><input type="radio" name="pandian_status" <?php echo $_GET['pandian_status']=="3"?'checked':''?> value="3" />调整<label>
	  </th>
	  <?php if(checkSellerLimit("limit_show_goods_chengben")){?>
	  <th style="width:25%; text-align:left">
			 <div style="">应盘：<?php echo $output['bill_info']['goods_num']?>（件）/ <?php echo $output['bill_info']['chengben_total']?>（元）</div>
			 <div style="">盘盈：<?php echo $output['bill_info']['goods_num2']?>（件）/ <?php echo $output['bill_info']['chengben_total2']?>（元）</div>
			 <div style="">盘亏：<?php echo $output['bill_info']['goods_num1']?>（件）/ <?php echo $output['bill_info']['chengben_total1']?>（元）</div>
			  <div style="">正常：<?php echo $output['bill_info']['goods_num3']?>（件）/ <?php echo $output['bill_info']['chengben_total3']?>（元）</div>
      </th>
	  <?php }else{?>
	  <th style="width:15%; text-align:left">
			 <div style="">应盘：<?php echo $output['bill_info']['goods_num']?>（件）</div>
			 <div style="">盘盈：<?php echo $output['bill_info']['goods_num2']?>（件）</div>
			 <div style="">盘亏：<?php echo $output['bill_info']['goods_num1']?>（件）</div>
			 <div style="">正常：<?php echo $output['bill_info']['goods_num3']?>（件）</div>
      </th>
	  <?php }?>
    </tr> 
</table>
</form>
</div>
<div class="datalist" style="width:100%;height: auto;overflow-x: auto;">
<table class="ncsc-default-table order" style="width:1500px;">
   <thead>   	
    <tr nc_type="table_header">
	  <th>序号</th>
	  <th>货号</th>
      <th>款号</th>
	  <th>商品名称</th>
	   
	   <th>数量</th>
	   <th>盘点状态</th>
	   <th>调整</th>
	   <th>盘点仓库</th>
	   <th>所属仓库</th>
	   
	  <th>材质</th>
	  <th>颜色</th>
	  <th>证书类型</th>
	  <th>证书号</th>
	  <th>钻石大小</th> 
	  <th>主石粒数</th>
	  <th>指圈</th>
	  <th>金重</th> 
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($output['bill_goods_list'])) { ?>
    <?php foreach ($output['bill_goods_list'] as $i=>$val) { ?>	
      <tr>
	      <td><?php echo $i+1; ?></td>
		  <td><?php echo $val['goods_itemid']; ?></td>
		  <td><?php echo $val['goods_sn']; ?></td>	  
		  <td><?php echo $val['goods_name']; ?></td>

		   <td><?php echo $val['goods_count']; ?></td>
		   <td><?php echo $val['pandian_status_name']; ?></td>
		   <td><?php echo $val['pandian_adjust_name']; ?></td>
		   <td><?php echo $val['from_house_name']; ?></td>
		   <td><?php echo $val['warehouse']; ?></td>

		  <td><?php echo $val['caizhi']; ?></td>
		  <td><?php echo $val['jinse']; ?></td>	
		  <td><?php echo $val['zhengshuleibie']; ?></td>	
		  <td><?php echo $val['zhengshuhao']; ?></td>
		  <td><?php echo $val['zuanshidaxiao']; ?></td>
		  <td><?php echo $val['zhushilishu']; ?></td>
		  <td><?php echo $val['shoucun']; ?></td>
		  <td><?php echo $val['jinzhong']; ?></td>        
	  <?php }?>   
    </tr>

    <?php } else { ?>
    <tr>
      <td colspan="10" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
    </tr>
    <?php } ?>
  </tbody>
</table>
</div>
<div class="pagination" style="float:left;text-align:left;width:30%">
 <?php if ($output['confirm_check']){?>	   	  
	<a href="javascript:void(0);" class="ncbtn ncbtn-mint" data-id="ajax_check_bill" data-title="审核单据" 
	  nctype="ajaxBox" data-url="<?php echo urlShop('erp_bill', 'check_bill', array('bill_id' => $_GET['bill_id']));?>" title="审核">审 核</a>
	<?php }?>
  <?php if($output['bill_info']['bill_status']==1){?>		
  <a href="javascript:void(0);" class="ncbtn ncbtn-gray" nctype="batch" data-param="{sign:'pandian_adjust','url':'<?php echo urlShop('erp_bill', 'pandian_adjust', array('bill_id' => $_GET['bill_id']));?>'}" title="刷 新">刷 新</a>  
  <?php }?>  
	<?php if (checkSellerLimit('limit_export_bill')){?>
	<a href="<?php echo urlShop('erp_bill', 'down_bill_goods', array('bill_id' => $_GET['bill_id']));?>"  class="ncbtn ncbtn-gray" title="导出明细">导出明细</a>
	<?php }?>
</div> 
<div class="pagination" class="float:left; width:70%"> <?php echo $output['show_page']; ?> </div>		
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.form.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.ui-jqLoding.js"></script>
<script>
$(function(){
    //弹窗提示 操作
    $('a[nctype="ajaxBox"]').click(function(){
       ajax_form($(this).attr('data-id'),$(this).attr('data-title'),$(this).attr('data-url')+'&inajax=1', '480');        
    });
	//ajax 操作
	$('a[nctype="batch"]').click(function(){	
		var data_str = '';
		eval('data_str = ' + $(this).attr('data-param'));	
		if(data_str.sign=="pandian_adjust"){
		    if(!confirm("确定刷新盘点单，矫正商品盘点状态？")){			
			   return false;
			}		
		}  
		$(this).jqLoading({text:"正在处理中..."});
		$.post(data_str.url,{form_submit:'ok'},function(res){
			//var res = $.parseJSON(res);
			if(res.msg == '操作成功') {
				showDialog(res.msg,'succ','',function(){window.location.reload()},'','','','','','',2);
			}else{
				showError(res.msg);
			}
			 $(this).jqLoading("destroy");
		},'json');

   });
   
   $("#searchForm input[name='pandian_status']").click(function(){
       $("#searchForm").submit();
   });
});
</script>
