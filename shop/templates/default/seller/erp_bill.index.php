<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<!--下拉美化js、css-->
<link href="<?php echo RESOURCE_SITE_URL;?>/js/chosen/css/chosen.css" rel="stylesheet" type="text/css">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/chosen/js/chosen.jquery.js"></script>

<style>
    .ncsc-default-table thead th{
        min-width:70px;
        text-align: center;
    }
.tabmenu .tab a {    
    padding: 7px 8px 8px 8px;
}
</style>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>

<form method="get" action="index.php" id="searchForm">
  <table class="search-form">
    <input type="hidden" name="act" value="erp_bill" />
    <input type="hidden" name="op" value="index" />
    <tr> 
	  <th class="w65">制单时间：</th>
	  <td class="w240">
	  <input type="text" class="text w60" name="create_time_begin" id="create_time_begin" placeholder="" value="<?php echo $_GET['create_time_begin']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label>&nbsp;&#8211;&nbsp;
	  <input id="create_time_end" class="text w60" type="text" name="create_time_end" placeholder="" value="<?php echo $_GET['create_time_end']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label>
	  </td>
	  <th>单据类型：</th>
      <td class="w90">
	   <select name="bill_type" class="w130">
        <option value=""><?php echo $lang['nc_please_choose'];?></option>
        <?php if (is_array($output['bill_type_list'])) {?>
        <?php foreach ($output['bill_type_list'] as $key=>$val) {?>
        <option value="<?php echo $key;?>" <?php if ($_GET['bill_type'] == $key) {?>selected<?php }?>><?php echo $key;?> | <?php echo $val;?></option>
        <?php }?>
        <?php }?>
      </select>
	  </td>
      <th>单据状态：</th>
      <td class="w100">
	  <select name="bill_status" class="w130">
        <option value=""><?php echo $lang['nc_please_choose'];?></option>
        <?php if (is_array($output['bill_status_list'])) {?>
        <?php foreach ($output['bill_status_list'] as $key=>$val) {?>
        <option value="<?php echo $key;?>" <?php if ($_GET['bill_status'] == $key) {?>selected<?php }?>><?php echo $key;?> | <?php echo $val;?></option>
        <?php }?>
        <?php }?>
      </select>
	  </td>	  
	  <th>单据编号：</th>
      <td class="w160">
	    <input type="text" class="text w120" name="bill_no" placeholder="输入单据号" value="<?php echo $_GET['bill_no']; ?>"/>
	  </td>
    </tr>
  <tr>
	  <th>审核时间：</th>
	  <td class="w240">
	  <input type="text" class="text w60" name="check_time_begin" id="check_time_begin" value="<?php echo $_GET['check_time_begin']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label>&nbsp;&#8211;&nbsp;
	  <input type="text" class="text w60" name="check_time_end" id="check_time_end" value="<?php echo $_GET['check_time_end']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label>
	  </td>
     <th>入库公司：</th>
     <td class="w120">
         <select name="to_company_id" class="w130 chose">
             <option value=""><?php echo $lang['nc_please_choose'];?></option>
             <?php echo paramsHelper::echoArrayOption($output['company_list'],"id","company_name",$_GET['to_company_id']); ?>
         </select>
     </td>
     <th>入库仓库：</th>
     <td class="w120">
         <select name="to_house_id"  class="w130 chose">
             <option value=""><?php echo $lang['nc_please_choose'];?></option>
             <?php echo paramsHelper::echoArrayOption($output['house_list'],"house_id","name",$_GET['to_house_id']); ?>
         </select>
     </td>
     <th>订单号：</th>
     <td class="w160">
         <input type="text" class="text w120" name="order_sn" placeholder="输入订单号" value="<?php echo $_GET['order_sn']; ?>"/>
     </td>
    </tr>
	 <tr>
         <th>货号：</th>
         <td class="w100">
             <input type="text" class="text w210" name="goods_id" placeholder="输入货号" value="<?php echo $_GET['goods_id']; ?>"/>
         </td>
         <th>批发客户：</th>
         <td class="w160">
             <select name="wholesale_id" class="w130 chose">
                 <option value=""><?php echo $lang['nc_please_choose'];?></option>
                 <?php echo paramsHelper::echoArrayOption($output['wholesale_list'],"wholesale_id","wholesale_name",$_GET['wholesale_id']); ?>
             </select>
         </td>
        <!-- <th>供应商：</th>
         <td class="w160">
             <select name="supplier_id"  class="w130 chose">
                 <option value=""><?php echo $lang['nc_please_choose'];?></option>
                 <?php //echo paramsHelper::echoArrayOption($output['prc_list'],"sup_id","sup_name",$_GET['supplier_id']); ?>
             </select>
         </td>-->
		 <th>销售类型：</th>
          <td class="w120">
              <select name="sales_type" class="w130">
                  <option value=""><?php echo $lang['nc_please_choose'];?></option>
                  <?php echo paramsHelper::echoOption("sales_type",$_GET['sales_type']); ?>
              </select>
          </td>
         <th>款号：</th>
         <td class="w160"><input type="text" class="text w120" name="goods_sn" placeholder="输入款号" value="<?php echo $_GET['goods_sn']; ?>"/></td>
    </tr>
      <tr>
          <th>结算时间：</th>
          <td class="w240">
              <input type="text" class="text w60" name="settle_time_begin" id="settle_time_begin" placeholder="" value="<?php echo $_GET['settle_time_begin']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label>&nbsp;&#8211;&nbsp;
              <input type="text" class="text w60" name="settle_time_end" id="settle_time_end" placeholder="" value="<?php echo $_GET['settle_time_end']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label>
          </td>
          <th>批发类型：</th>
          <td class="w120">
              <select name="out_warehouse_type" class="w130">
                  <option value=""><?php echo $lang['nc_please_choose'];?></option>
                  <?php echo paramsHelper::echoOption("out_warehouse_type",$_GET['out_warehouse_type']); ?>
              </select>
          </td>
    	 <!-- <th>调拨类型：</th>
          <td class="w120">
              <select name="allot_type" class="w130">
                  <option value=""><?php echo $lang['nc_please_choose'];?></option>
                  <?php echo paramsHelper::echoOption("allot_type",$_GET['allot_type']); ?>
              </select>
          </td>-->
          <th>门店结算类型：</th>
          <td class="w160">
              <select name="is_settle"  class="w130">
                  <option value=""><?php echo $lang['nc_please_choose'];?></option>
				  <?php 
				     if(isset($_GET['is_settle']) && in_array($_GET['is_settle'], ['0', '1'])){
						 echo paramsHelper::echoOption("settle_type",$_GET['is_settle']);
					 }else{
						 echo paramsHelper::echoOption("settle_type",'-1');
					 }	 
					  ?>
                 
              </select>
          </td>
      </tr>
      <tr>
          <th>备注：</th>
          <td class="w240">
              <input type="text" class="text w210" name="remark" placeholder="输入单据备注" value="<?php echo $_GET['remark']; ?>"/>
          </td>
          <th>制单人：</th>
          <td class="w160">
              <input type="text" class="text w120" name="create_user" placeholder="输入制单人" value="<?php echo $_GET['create_user']; ?>"/>
          </td>
      </tr>
      <tr>
          <th>审核人</th>
          <td class="w100">
              <input type="text" class="text w210" name="check_user" placeholder="输入审核人" value="<?php echo $_GET['check_user']; ?>"/>
          </td>
          <td class="tc w60" colspan="4">
		     <label class="submit-border"><input type="button" class="submit" onclick="choseReset()" value="重置" /></label>
              <label class="submit-border">	     
                  <input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" />
              </label>
          </td>
      </tr>
  </table>
</form>
<div class="datalist" style="width:100%;height: auto;overflow-x: auto;">
<table class="ncsc-default-table" style="width:1000px">
  <thead>
   	<tr>
      <th colspan="11" style="text-align:left">
	    <?php if((empty($_GET['bill_type']) || $_GET['bill_type']=="S") && in_array('limit_print_goods_price',$_SESSION['seller_limits'])){?>
      	    <a href="javascript:void(0)" class="ncbtn ncbtn-mint" nctype="batch" dialog_title="打印价格标签" dialog_id="print_bill_goods" data-param="{url:'index.php?act=erp_bill&op=print_bill_goods_form',sign:'ajax_form'}" dialog_width="400" alt="打印价格标签" title="打印价格标签"><i class="icon-print"></i>打印价格标签</a>
		<?php }?>
        <?php if(in_array($_GET['bill_type'], array("S", "B", "MW"))){?><!--empty($_GET['bill_type']) || -->
            <a href="javascript:void(0)" data-param="{url:'index.php?act=erp_bill&op=print_bill_details',sign:'print'}" class="ncbtn ncbtn-mint" target="_blank" title="打印单据明细" nctype="batch"><i class="icon-print"></i>打印单据</a>
        <?php }?>
		<?php if (checkSellerLimit('limit_export_bill')){?>
		<a href="javascript:void(0)"  class="ncbtn ncbtn-mint" target="_blank" id="bill_download" title="导出单据" ><i class="icon-print"></i>导出单据</a>
		<a href="javascript:void(0)"  class="ncbtn ncbtn-mint" target="_blank" id="bill_detail_download" title="导出单据明细"><i class="icon-print"></i>导出单据明细</a>
		<?php }?>
      </th>
    </tr> 
    <tr nc_type="table_header">
	  <th class="tc"><input type="checkbox" id="all" class="checkall"/><label for="all"> 全选</label></th>
      <th>单据编号</th>
      <th>单据类型</th>
      <th>单据状态</th>
	  <th>成本价</th>
	  <?php if($output['is_shengdai']){ ?>
	  <th>批发价</th>
	  <?php } ?>
      <th>数量</th>
	  <th>订单号</th>
	  <th>制单时间</th>
	  <th>审核时间</th>
      <th>出库公司</th>
	  <th>出库仓</th>
      <th>入库公司</th>
      <th>入库仓</th>
      <th>供应商</th>
      <th>批发客户</th>
      <th>备注</th>
	  <th>操作</th>  
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($output['bill_list'])) { ?>
    <?php foreach ($output['bill_list'] as $val) { ?>	
    <tr class="bd-line">
	  <td><input type="checkbox" class="checkitem tc" value="<?php echo $val['bill_id']; ?>"/></td>
      <td><a href="<?php echo urlShop('erp_bill', 'show', array('bill_id' => $val['bill_id'],'bill_type'=>$_GET['bill_type']));?>">
	  <?php echo $val['bill_no']; ?></a></td>
      <td><?php echo billType($val); ?></td>	 
	  <td><?php echo billStatus($val); ?></td>
	  <td><?php echo billChengbenTotal($val, $output['show_chengben']); ?></td>
	  <?php if($output['is_shengdai']){ ?>
		  <?php if($val['bill_type'] =='S' && $val['item_type']=='PF'){ ?>
		    <td><?php echo $val['goods_total'] ?></td>	
		  <?php }else{ ?>	  
		    <td></td>
		  <?php } ?>
	  <?php } ?>
	 	
	  <td><?php echo $val['goods_num']/1; ?></td>
	  <td><?php echo $val['order_sn']; ?></td>    
	  <td><?php echo $val['create_time'];?><br/><?php echo $val['create_user'];?></td>
	  <td><?php echo $val['check_time'];?><br/><?php echo $val['check_user'];?></td>
      <td><?php echo $val['from_company_name']; ?></td>
	  <td><?php echo $val['from_house_name']; ?></td>
      <td><?php echo $val['to_company_name']; ?></td>
      <td><?php echo $val['to_house_name']; ?></td>
      <td><?php echo $val['supplier_name']; ?></td>
      <td><?php echo $val['wholesale_name']; ?></td>
      <td><?php echo $val['remark']; ?></td>
      <td class="nscs-table-handle">
	  <span> <a href="<?php echo urlShop('erp_bill', 'show', array('bill_id' => $val['bill_id'],'bill_type'=>$_GET['bill_type']));?>" class="btn-mint"> <i class="icon-cog"></i><p>管理</p></a></span>
  	  </td> 
    </tr>
    <tr style="display:none;">
      <td colspan="11"><div class="ncsc-goods-sku ps-container"></div></td>
    </tr>
    <?php } ?>
    <?php } else { ?>
    <tr>
      <td colspan="11" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
    </tr>
    <?php } ?>
  </tbody>
</table>
</div>
<div class="pagination"> <?php echo $output['show_page']; ?> </div>
<script charset="utf-8" type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js"></script> 
<script>
 //重置
    function choseReset(){
		//document.getElementById("searchForm").reset()
		<?php if(isset($_GET['bill_type'])){ ?>
		window.location.href = "index.php?act=erp_bill&op=index&bill_type=<?php echo $_GET['bill_type'] ?>";
		<?php }else{ ?>
		window.location.href = "index.php?act=erp_bill&op=index";
		<?php } ?>
		$(".chose").val('').trigger("chosen:updated"); 
	}
$(function(){
	//下拉美化（可搜索）
    $('.chose').chosen();

	
    $('#create_time_begin').datepicker({dateFormat: 'yy-mm-dd'}).attr("readonly",false);
    $('#create_time_end').datepicker({dateFormat: 'yy-mm-dd'}).attr("readonly",false);
	$('#check_time_begin').datepicker({dateFormat: 'yy-mm-dd'}).attr("readonly",false);
    $('#check_time_end').datepicker({dateFormat: 'yy-mm-dd'}).attr("readonly",false);
    $('#settle_time_begin').datepicker({dateFormat: 'yy-mm-dd'}).attr("readonly",false);
    $('#settle_time_end').datepicker({dateFormat: 'yy-mm-dd'}).attr("readonly",false);
    //Ajax提示
    $('.tip').poshytip({
        className: 'tip-yellowsimple',
        showTimeout: 1,
        alignTo: 'target',
        alignX: 'center',
        alignY: 'top',
        offsetY: 5,
        allowTipHover: false
    });
	
	$('a[nctype="batch"]').click(function(){
        if($('.checkitem:checked').length == 0){    //没有选择
        	showDialog('请选择需要操作的记录');
            return false;
        }
        var _items = '';
        $('.checkitem:checked').each(function(){
            _items += $(this).val() + ',';
        });
        _items = _items.substr(0, (_items.length - 1));

        var data_str = '';
        eval('data_str = ' + $(this).attr('data-param'));
		if(data_str.sign == "print"){
		    window.open(data_str.url + '&_ids=' + _items);
		}else if(data_str.sign == "ajax_form"){
		    var id = $(this).attr('dialog_id');
			var title = $(this).attr('dialog_title') ? $(this).attr('dialog_title') : '';
			var url = data_str.url+"&_ids="+_items;
			var width = $(this).attr('dialog_width');
			CUR_DIALOG = ajax_form(id, title, url, width,0);
			return false;
		}else{
			$.post(data_str.url,{items:_items,status:data_str.status,form_submit:'ok'},function(res){
				var res = $.parseJSON(res);
				if(res.msg == '操作成功') {
					showDialog(res.msg,'succ','',function(){window.location.reload()},'','','','','','',2);
				}else{
					showError(res.msg);
				}
	
			})
		}
        
    });
	
	
	  
	
	    $("#bill_download").click(function(){
			var args = get_args();	
            if(args == false) return false;			
			console.log(args);
			location.href = "index.php?act=erp_bill&op=download" + args;
			
		})
		
		$("#bill_detail_download").click(function(){
			var args = get_args();
            if(args == false) return false;				
			console.log(args);
			location.href = "index.php?act=erp_bill&op=detail_download" + args;
			
		})
		
		
		function get_args(){

            var _items = '';
            $('.checkitem:checked').each(function(){
                _items += $(this).val() + ',';
            });
            _items = _items.substr(0, (_items.length - 1));
            //var b_ids = _items;
			var create_time_begin = $("#searchForm [name='create_time_begin']").val();
			var create_time_end = $("#searchForm [name='create_time_end']").val();
			var bill_type = $("#searchForm [name='bill_type']").val();
			var bill_status = $("#searchForm [name='bill_status']").val();
			var bill_no = $("#searchForm [name='bill_no']").val();
			var check_time_begin = $("#searchForm [name='check_time_begin']").val();
			var check_time_end = $("#searchForm [name='check_time_end']").val();
			var to_company_id = $("#searchForm [name='to_company_id']").val();
			var to_house_id = $("#searchForm [name='to_house_id']").val();
			var order_sn = $("#searchForm [name='order_sn']").val();
			var goods_id = $("#searchForm [name='goods_id']").val();
			var wholesale_id = $("#searchForm [name='wholesale_id']").val();
			var sales_type = $("#searchForm [name='sales_type']").val();
			var goods_sn = $("#searchForm [name='goods_sn']").val();
			var settle_time_begin = $("#searchForm [name='settle_time_begin']").val();
			var settle_time_end = $("#searchForm [name='settle_time_end']").val();
			var out_warehouse_type = $("#searchForm [name='out_warehouse_type']").val();
			var is_settle = $("#searchForm [name='is_settle']").val();
			var remark = $("#searchForm [name='remark']").val();
			var create_user = $("#searchForm [name='create_user']").val();
			var check_user = $("#searchForm [name='check_user']").val();

            if(!_items){
                if(!create_time_begin || !create_time_end){
                    if(!confirm('请设置制单时间范围为1年内，点击‘确定’继续！')){
                        return false;
                    }   
                    return false;
                }
            }
			
			//if(!create_time_begin && !create_time_end && !bill_type && !bill_status && !bill_no && !check_time_begin && !check_time_end && !to_company_id && !to_house_id && !order_sn && !goods_id && !wholesale_id && !sales_type && !goods_sn && !settle_time_begin && !settle_time_end && !out_warehouse_type && !is_settle && !remark && !create_user && !check_user ){
				//if(!confirm('没有导出限制可能会消耗较长的时间，点击‘确定’继续！')){
					//return false;
				//}	
			//}
			var args = "";
            args += "&ids="+_items;
			args += "&create_time_begin="+create_time_begin;
			args += "&create_time_end="+create_time_end;
			args += "&bill_type="+bill_type;
			args += "&bill_status="+bill_status;
			args += "&bill_no="+bill_no;
			args += "&check_time_begin="+check_time_begin;
			args += "&check_time_end="+check_time_end;
			args += "&to_company_id="+to_company_id;
			args += "&to_house_id="+to_house_id;
			args += "&order_sn="+order_sn;
			args += "&goods_id="+goods_id;
			args += "&wholesale_id="+wholesale_id;
			args += "&sales_type="+sales_type;
			args += "&goods_sn="+goods_sn;
			args += "&settle_time_begin="+settle_time_begin;
			args += "&settle_time_end="+settle_time_end;
			args += "&out_warehouse_type="+out_warehouse_type;
			args += "&is_settle="+is_settle;
			args += "&remark="+remark;
			args += "&create_user="+create_user;
			args += "&check_user="+check_user;
            return args; 			
		}
		
		
});
</script>