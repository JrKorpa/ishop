<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>

<!--下拉美化js、css-->
<link href="<?php echo RESOURCE_SITE_URL;?>/js/chosen/css/chosen.css" rel="stylesheet" type="text/css">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/chosen/js/chosen.jquery.js"></script>

<!-- S setp -->
<ul class="add-goods-step">
  <li class="<?php $output['step']=="1" ? print "current" : print "";?>"><i class="icon icon-list-alt"></i>
    <h6>STIP.1</h6>
    <h2>创建销售出库单</h2>
    <i class="arrow icon-angle-right"></i> 
  </li>
  <!--<li class="<?php $output['step']=="2" ? print "current" : print "";?>"><i class="icon icon-edit"></i>
    <h6>STIP.2</h6>
    <h2>导入CSV</h2>
    <i class="arrow icon-angle-right"></i> 
  </li>-->
 <li class="<?php $output['step']=="2" ? print "current" : print "";?>"><i class="icon icon-ok-circle "></i>
    <h6>STIP.2</h6>
    <h2>保存完成</h2></li>
</ul>
<div class="alert mt15 mb5"><strong>珂兰技术中心操作提示：</strong>
  <ul>
    <li>1.批量输入多个货号用英文逗号隔开或回车换行。</li>
	<li>2.客单发货通过订单发货操作自动产生零售类型的销售出库单。</li>
  </ul>
</div>
<!--S 分类选择区域-->
<form method="post" action="index.php?act=erp_bill_s&op=insert" id="erp_bill_s_form" onsubmit="return pre_submit();">
  <div class="ncsc-form-goods"  <?php if($output['step'] != '1'){?> style="display:none"<?php }?>>
	<input type="hidden" name="sales_type" value="PF"/>
    <div id="show_div">
	    <dl>
          <dt><i class="required">*</i>销售类型</dt>
          <dd>
            批发
          </dd>
        </dl> 
        <dl>
          <dt><i class="required">*</i>批发客户</dt>
          <dd>
            <select name="jxc_wholesale" class="chose" style="width:240px;">
                <option value=""><?php echo $lang['nc_please_choose'];?></option>
                <?php if (is_array($output['jxc_wholesale'])) {?>
                <?php foreach ($output['jxc_wholesale'] as $key=>$val) {?>
                    <option value="<?php echo $val['wholesale_id'];?>"><?php echo $val['wholesale_name'];?></option>
                <?php }?>
                <?php }?>
            </select>
          </dd>
        </dl>        
        <dl>
          <dt><i class="required">*</i>出库方式</dt>
          <dd>
            <select name="out_warehouse_type">
                <option value=""><?php echo $lang['nc_please_choose'];?></option>
                <?php if (is_array($output['out_warehouse_type'])) {?>
                    <?php foreach ($output['out_warehouse_type'] as $key=>$val) {?>
                        <option value="<?php echo $key;?>"><?php echo $val;?></option>
                    <?php }?>
                <?php }?>
            </select>
          </dd>
        </dl>
		<!--
        <dl>
          <dt><i class="required">*</i>类别</dt>
          <dd>
            <select name="pifa_type" class="chose" style="width:150px;">
              <option value=""><?php echo $lang['nc_please_choose'];?></option>
              <?php if (is_array($output['pifa_type'])) {?>
            <?php foreach ($output['pifa_type'] as $key=>$val) {?>
            <option value="<?php echo $key;?>"><?php echo $val;?></option>
            <?php }?>
            <?php }?>
            </select>
          </dd>
        </dl>-->
		<dl>
          <dt>物流公司</dt>
          <dd>
            <select name="express_id" class="chose" style="width:150px;">
              <option value=""><?php echo $lang['nc_please_choose'];?></option>
              <?php if (is_array($output['expresslist'])) {?>
            <?php foreach ($output['expresslist'] as $key=>$val) {?>
            <option value="<?php echo $val['id'];?>"><?php echo $val['e_name'];?></option>
            <?php }?>
            <?php }?>
            </select>
            <input type="text" class="text" placeholder="请输入物流单号" name="express_sn" id="express_sn" style="margin-top: 2px;">
          </dd>
        </dl>        
    </div>
    <dl>
      <dt><i class="required">*</i>货号</dt>
      <dd>
            <textarea id="goods_ids" class="w600" name="goods_itemid" style="height:120px" placeholder="扫码批量输入，用逗号(,)或回车隔开"></textarea>
      </dd>
    </dl>

	<dl>
      <dt>备注</dt>
      <dd> 
		  <textarea class="w600" name="remark" rows="2"></textarea>
      </dd>
    </dl>
    <dl>
      <dt>&nbsp;</dt>
      <dd>
        <input type="button" id="btn_submit" class="submit" value="保存" />
      </dd>
    </dl>
    </ul>
  </div>
  <div class="table-responsive">
    <table class="ncsc-default-table">
        <thead id="html_title">
        </thead>
        <tbody id="html">
        </tbody>
    </table>
    </div>
</form>
<!--step4--> 

<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.form.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.ui-jqLoding.js"></script>
<script>
//下拉美化（可搜索）
$(function(){
	$('.chose').chosen();
});

//$(".chosen-select").chosen({max_selected_options: 15,width: "95%"});
var formID = "erp_bill_s_form";
var optionsSubmit = {
	url: "index.php?act=erp_bill_s&op=insert",
	dataType:'json',	
	error:function ()
	{   $(this).jqLoading("destroy");
	    $('#'+formID+' .submit').attr('disabled',false);//解锁
		alert("请求失败");
	},
	beforeSubmit:function(frm,jq,op){
	    $(this).jqLoading({text:"正在处理中..."});
		$('#'+formID+' .submit').attr('disabled',true);//禁用
	},
	success: function(res) {
	    $(this).jqLoading("destroy");
	    $('#'+formID+' .submit').attr('disabled',false);//解锁
		if(res.success == 1 ){
	      	showDialog('添加导入成功! 单据编号：'+res.data.bill_no,'succ', '提示信息', function(){
		        window.location.href='index.php?act=erp_bill&op=show&bill_id='+res.data.bill_id;
		    });
		}else{
		   showDialog(res.msg);
		}
	}
};

$("#"+formID+" #btn_submit").click(function(){   
   $("#"+formID).ajaxSubmit(optionsSubmit);
});

$("#"+formID+" select[name='to_company_id']").change(function(){
  var house_id = $(this).val();
  var boxSelectObj = $("#"+formID+" select[name='to_house_id']");
  var str_options='<option value="">请选择</option>';  
  if(house_id==""){
     boxSelectObj.html(str_options);
     return;
  }
  //house_id = house_id.split('|')[1];  
  $.getJSON('index.php?act=store_warehouse&op=get_warehouse_list_ajax&house_id=' +house_id , function(data){  
        if (data) {
            for(var i=0;i<data.length;i++){
                str_options +='<option value="'+data[i]['house_id']+'">'+data[i]['name']+'</option>';           
            }
            boxSelectObj.html(str_options);
        }
        boxSelectObj.html(str_options);
    });
});

$("#"+formID+" select[name='jxc_wholesale']").change(function(){
    var jxc_wholesale = $(this).val();
    var url = "index.php?act=erp_bill_s&op=getWholesale";
    $.ajax({
        type: "POST", 
        url: url,
        data: {jxc_wholesale:jxc_wholesale}, //可选参数
        dataType: "json",
        success: function(data){
            if (data.success == 1) {
                $("#"+formID+" select[name='to_company_id']").val("");
                $("#"+formID+" select[name='to_house_id']").val("");
                $("#jxc_div").hide();
            }else{
                $("#jxc_div").show();
            }
        }
    });
});

$("#"+formID+" textarea[name='goods_itemid']").change(function(){
    var sales_type = $("#"+formID+" input[name='sales_type']").val();
    if(!sales_type){
        showDialog("请选择销售类型");return;
    }
    var goods_ids = $("#goods_ids").val();
    var url = "index.php?act=erp_bill_s&op=get_warehouse_goods_ajax";
    $.ajax({
        type: "POST", 
        url: url,
        data: {sales_type:sales_type, goods_ids:goods_ids}, //可选参数
        dataType: "json",
        success: function(data){
            var html = '';
            var html_title = '';
            if (data.success == 1) {
                var arr = data.data;
                if(sales_type == 'PF'){
                   html_title = "<tr><th>货号</th><th>款号</th><th>名称</th><th>数量</th><th>采购成本</th><th>销售价</th><th>批发价</th><th>管理费</th><th>订单号</th><th>材质</th><th>材质颜色</th><th>指圈</th><th>主成色重</th><th>主石重</th><th>主石粒数</th></tr>"; 
                }else if(sales_type == 'WX'){
                    html_title = "<tr><th>货号</th><th>款号</th><th>名称</th><th>数量</th><th>采购成本</th><th>销售价</th><th>订单号</th><th>材质</th><th>材质颜色</th><th>指圈</th><th>主成色重</th><th>主石重</th><th>主石粒数</th></tr>"; 
                }else{
                    showDialog("请选择销售类型");return;
                }

                for(var i=0;i<arr.length;i++){
					if(arr[i]['order_sn'] == null){
						arr[i]['order_sn'] = '';
					}	
                    if(sales_type == 'PF'){
                        html +='<tr><td>'+arr[i]['goods_id']+'</td><td>'+arr[i]['goods_sn']+'</td><td>'+arr[i]['goods_name']+'</td><td>'+arr[i]['num']+'</td><td>'+arr[i]['yuanshichengbenjia']+'</td><td>'+arr[i]['mingyichengben']+'</td><td><input type="text" class="pifajia" name="pifajia[]" placeholder="请输入批发价"></td><td><input type="text" class="guanlifei" name="guanlifei[]" placeholder="请输入管理费"></td><td>'+arr[i]['order_sn']+'</td><td>'+arr[i]['caizhi']+'</td><td>'+arr[i]['shoucun']+'</td><td>'+arr[i]['yanse']+'</td><td>'+arr[i]['jinzhong']+'</td><td>'+arr[i]['zuanshidaxiao']+'</td><td>'+arr[i]['zhushilishu']+'</td></tr>';
                    }else if(sales_type == 'WX'){
                        html +='<tr><td>'+arr[i]['goods_id']+'</td><td>'+arr[i]['goods_sn']+'</td><td>'+arr[i]['goods_name']+'</td><td>'+arr[i]['num']+'</td><td>'+arr[i]['yuanshichengbenjia']+'</td><td>'+arr[i]['mingyichengben']+'</td><td>'+arr[i]['order_sn']+'</td><td>'+arr[i]['caizhi']+'</td><td>'+arr[i]['shoucun']+'</td><td>'+arr[i]['yanse']+'</td><td>'+arr[i]['jinzhong']+'</td><td>'+arr[i]['zuanshidaxiao']+'</td><td>'+arr[i]['zhushilishu']+'</td></tr>';
                    }else{
                        showDialog("请选择销售类型");return;
                    }
                }				
				html += '<tr><td colspan="20"><span style="font-weight:bold;"> 批发价：<font id="fa_total" color="red">0.00</font></span> <span style="font-weight:bold;margin-left:20px;">管理费：<font color="red" id="guanlifei">0.00</font></span></td></tr>'
           
				
		   }else{
                $("#html_title").html("");
                $("#html").html("");
                showDialog(data.msg);return;
            }
            $("#html_title").html(html_title);
            $("#html").html(html);
			
			
			$(".pifajia").change(function(){		
					var fa_total = getEachPrice(".pifajia");
					$("#fa_total").html(fa_total);
			})
			
			$(".guanlifei").change(function(){		
					var guanlifei_total = getEachPrice(".guanlifei");
					$("#guanlifei").html(guanlifei_total);
			})
				
        }
    });
});

function getEachPrice(id){
	var total_price = 0.00;
	 $(id).each(function(){
		var price = parseFloat($(this).val());
		if(isNaN(price)) {
			price = 0.00;
		}
		total_price +=  price;
	});
	return total_price;

}

</script>