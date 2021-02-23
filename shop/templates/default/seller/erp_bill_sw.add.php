<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>

<!--下拉美化js、css-->
<link href="<?php echo RESOURCE_SITE_URL;?>/js/chosen/css/chosen.css" rel="stylesheet" type="text/css">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/chosen/js/chosen.jquery.js"></script>

<!-- S setp -->
<ul class="add-goods-step">
  <li class="<?php $output['step']=="1" ? print "current" : print "";?>"><i class="icon icon-list-alt"></i>
    <h6>STIP.1</h6>
    <h2>创建维修出库单</h2>
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
    <li>1.批量输入多个货号用英文逗号隔开或回车换行。<br></li>
  </ul>
</div>
<!--S 分类选择区域-->
<form method="post" action="index.php?act=erp_bill_s&op=insert" id="erp_bill_s_form" onsubmit="return pre_submit();">
  <div class="ncsc-form-goods"  <?php if($output['step'] != '1'){?> style="display:none"<?php }?>>
	<input type="hidden" name="sales_type" value="WX"/>    
    <dl>
      <dt><i class="required">*</i>货号</dt>
      <dd>
            <textarea id="goods_ids" class="w600" name="goods_itemid" style="height:120px" placeholder="扫码批量输入，用逗号(,)或回车隔开"></textarea>  <!-- <input type="button" id="get_data" class="submit" value="取数" /> -->
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
<!--<script src="<?php echo RESOURCE_SITE_URL;?>/js/bootstrap.min.js"></script>-->

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
                    if(sales_type == 'PF'){
                        html +='<tr><td>'+arr[i]['goods_id']+'</td><td>'+arr[i]['goods_sn']+'</td><td>'+arr[i]['goods_name']+'</td><td>'+arr[i]['num']+'</td><td>'+arr[i]['yuanshichengbenjia']+'</td><td>'+arr[i]['mingyichengben']+'</td><td><input type="text" name="pifajia[]" placeholder="请输入批发价"></td><td><input type="text" name="guanlifei[]" placeholder="请输入管理费"></td><td>'+arr[i]['order_sn']+'</td><td>'+arr[i]['caizhi']+'</td><td>'+arr[i]['shoucun']+'</td><td>'+arr[i]['yanse']+'</td><td>'+arr[i]['jinzhong']+'</td><td>'+arr[i]['zuanshidaxiao']+'</td><td>'+arr[i]['zhushilishu']+'</td></tr>';
                    }else if(sales_type == 'WX'){
                        html +='<tr><td>'+arr[i]['goods_id']+'</td><td>'+arr[i]['goods_sn']+'</td><td>'+arr[i]['goods_name']+'</td><td>'+arr[i]['num']+'</td><td>'+arr[i]['yuanshichengbenjia']+'</td><td>'+arr[i]['mingyichengben']+'</td><td>'+arr[i]['order_sn']+'</td><td>'+arr[i]['caizhi']+'</td><td>'+arr[i]['shoucun']+'</td><td>'+arr[i]['yanse']+'</td><td>'+arr[i]['jinzhong']+'</td><td>'+arr[i]['zuanshidaxiao']+'</td><td>'+arr[i]['zhushilishu']+'</td></tr>';
                    }else{
                        showDialog("请选择销售类型");return;
                    }
                }
            }else{
                $("#html_title").html("");
                $("#html").html("");
                showDialog(data.msg);return;
            }
            $("#html_title").html(html_title);
            $("#html").html(html);
        }
    });
});

</script>