<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<!--下拉美化js、css-->
<link href="<?php echo RESOURCE_SITE_URL;?>/js/chosen/css/chosen.css" rel="stylesheet" type="text/css">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/chosen/js/chosen.jquery.js"></script>

<!-- S setp -->
<ul class="add-goods-step">
  <li class="<?php $output['step']=="1" ? print "current" : print "";?>"><i class="icon icon-list-alt"></i>
    <h6>STIP.1</h6>
    <h2>创建退货返厂单</h2>
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
	<li>2.客单退货通过订单退货操作自动产生零售类型的销售退货单。</li>
  </ul>
</div>
<!--S 分类选择区域-->
<form method="post" action="index.php?act=erp_bill_d&op=insert" id="erp_bill_d_form" onsubmit="return pre_submit();">
  <div class="ncsc-form-goods"  <?php if($output['step'] != '1'){?> style="display:none"<?php }?>>
	<input type="hidden" name="sales_type" value="PF"/>
		 <dl>
		  <dt><i class="required">*</i>入库方式</dt>
		  <dd>
			<select name="put_out_type">
			  <option value=""><?php echo $lang['nc_please_choose'];?></option>
			  <?php if (is_array($output['put_type_list'])) {?>
			<?php foreach ($output['put_type_list'] as $key=>$val) {?>
			<option value="<?php echo $key;?>" <?= $key==$_GET['put_out_type']?"selected":''?>><?php echo $val;?></option>
			<?php }?>
			<?php }?>
			</select>
		  </dd>
		</dl>	 
        <!--<dl>
          <dt><i class="required">*</i>退货类型</dt>
          <dd>
            批发
          </dd>
        </dl>-->
        <dl>
          <dt><i class="required">*</i>退货客户</dt>
          <dd>
            <select name="jxc_wholesale" class="chose" style="width:200px;">
                <option value=""><?php echo $lang['nc_please_choose'];?></option>
                <?php if (is_array($output['wholesale_list'])) {?>
                <?php foreach ($output['wholesale_list'] as $key=>$val) {?>
                    <option value="<?php echo $val['wholesale_id'];?>"><?php echo $val['wholesale_name'];?></option>
                <?php }?>
                <?php }?>
            </select>
          </dd>
        </dl>
		<dl>
        <dt><i class="required">*</i>供应商</dt>
          <dd>
            <select name="to_company_id" class="chose" style="width:200px;">
              <option value=""><?php echo $lang['nc_please_choose'];?></option>
              <?php if (is_array($output['company_list'])) {?>
            <?php foreach ($output['company_list'] as $key=>$val) {?>
			<?php if($key==57){ continue;}?>
            <option value="<?php echo $key;?>"><?php echo $val;?></option>
            <?php }?>
            <?php }?>
            </select>
            <!--<select name="to_house_id" class="chose1" style="width:250px;">
              <option value=""><?php echo $lang['nc_please_choose'];?></option>       
            </select>-->
          </dd>
        </dl>
        <dl>
          <dt>物流公司</dt>
          <dd>
            <select name="express_id" class="chose" style="width:150px;">
              <option value=""><?php echo $lang['nc_please_choose'];?></option>
              <?php if (is_array($output['express_list'])) {?>
            <?php foreach ($output['express_list'] as $key=>$val) {?>
            <option value="<?php echo $val['id'];?>"><?php echo $val['e_name'];?></option>
            <?php }?>
            <?php }?>
            </select>
             <input type="text" class="text" placeholder="请输入物流单号" name="express_sn" style="margin-top: 2px;">
          </dd>
        </dl>
        
    <dl>
      <dt><i class="required">*</i>货号</dt>
      <dd>
            <textarea id="goods_ids" class="w600" name="goods_itemid" style="height:120px" placeholder="扫码批量输入，用逗号(,)或回车隔开"></textarea>   <!--<input type="button" id="get_data" class="submit" value="取数" /> -->
      </dd>
    </dl>	
	<dl>
      <dt><i class="required">*</i>备注</dt>
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
<script src="<?php echo RESOURCE_SITE_URL;?>/js/bootstrap.min.js"></script>
<script>
//下拉美化（可搜索）
$(function(){
	$('.chose').chosen();
});

var formID = "erp_bill_d_form";
var optionsSubmit = {
	url: "index.php?act=erp_bill_d&op=insert",
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
$("#"+formID+" select[name='put_out_type']").change(function(){
    if($(this).val()==5){
	    location.href='index.php?act=erp_bill_b&op=add&put_out_type='+$(this).val();
	}
});
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
		$('.chose1').chosen();
		$('.chose1').val('').trigger("chosen:updated"); 
    });
	
});

$("#"+formID+" textarea[name='goods_itemid']").change(function(){
    var sales_type = $("#"+formID+" input[name='sales_type']").val();
	var put_out_type = $("#"+formID+" select[name='put_out_type']").val();
	var to_company_id = $("#"+formID+" select[name='to_company_id']").val();
	var jxc_wholesale = $("#"+formID+" select[name='jxc_wholesale']").val();
    if(!sales_type){
	    $(this).val('');
        showDialog("请选择退货类型");return;
    }
	if(!put_out_type){
	    $(this).val('');  
        showDialog("请先选择出库方式");return;
    }
	if(!to_company_id){
	    $(this).val('');
        showDialog("请先选择供应商");return;
    }
    var goods_ids = $("#goods_ids").val();
    var url = "index.php?act=erp_bill_d&op=get_warehouse_goods_ajax";
      $.ajax({
            type: "POST", 
            url: url,
            data: {sales_type:sales_type, goods_ids:goods_ids,put_out_type:put_out_type,to_company_id:to_company_id,jxc_wholesale:jxc_wholesale}, //可选参数
            dataType: "json",
            success: function(data){
                var html = '';
                var html_title = '';
                if(sales_type == 'PF'){
                    html_title = "<tr><th>货号</th><th>款号</th><th>名称</th><th>数量</th><th>采购成本</th><th>销售价</th><th>退货价</th><th>订单号</th><th>材质</th><th>材质颜色</th><th>指圈</th><th>主成色重</th><th>主石重</th><th>主石粒数</th><th>出库类型</th></tr>"; 
                }else if(sales_type == 'WX'){
                    html_title = "<tr><th>货号</th><th>款号</th><th>名称</th><th>数量</th><th>采购成本</th><th>销售价</th><th>订单号</th><th>材质</th><th>材质颜色</th><th>指圈</th><th>主成色重</th><th>主石重</th><th>主石粒数</th><th>出库类型</th></tr>"; 
                }else{
                    showDialog("请选择退货类型!");return;
                }
                if (data.success == 1) {
                    var arr = data.data;
                    for(var i=0;i<arr.length;i++){//<td><input type="text" name="pifajia[]" ></td><td><input type="text" name="guanlifei[]" ></td>
					    if(arr[i]['order_sn'] == null){
							arr[i]['order_sn'] = '';
						}
                        if(sales_type == 'PF'){
                            html +='<tr><td>'+arr[i]['goods_id']+'</td><td>'+arr[i]['goods_sn']+'</td><td>'+arr[i]['goods_name']+'</td><td>'+arr[i]['num']+'</td><td>'+arr[i]['jijiachengben']+'</td><td>'+arr[i]['jijiachengben']+'</td><td><input type="text" name="pifajia[]" placeholder="请输入退货价" value='+arr[i]['yuanshichengbenjia']+'></td><td>'+arr[i]['order_sn']+'</td><td>'+arr[i]['caizhi']+'</td><td>'+arr[i]['jinse']+'</td><td>'+arr[i]['shoucun']+'</td><td>'+arr[i]['jinzhong']+'</td><td>'+arr[i]['zuanshidaxiao']+'</td><td>'+arr[i]['zhushilishu']+'</td><td>'+arr[i]['out_warehouse_type']+'</td></tr>';
                        }else if(sales_type == 'WX'){
                            html +='<tr><td>'+arr[i]['goods_id']+'</td><td>'+arr[i]['goods_sn']+'</td><td>'+arr[i]['goods_name']+'</td><td>'+arr[i]['num']+'</td><td>'+arr[i]['yuanshichengbenjia']+'</td><td>'+arr[i]['mingyichengben']+'</td><td>'+arr[i]['order_sn']+'</td><td>'+arr[i]['caizhi']+'</td><td>'+arr[i]['jinse']+'</td><td>'+arr[i]['shoucun']+'</td><td>'+arr[i]['jinzhong']+'</td><td>'+arr[i]['zuanshidaxiao']+'</td><td>'+arr[i]['zhushilishu']+'</td><td>'+arr[i]['out_warehouse_type']+'</td></tr>';
                        }else{
                            showDialog("请选择退货类型");return;
                        }
                    }
                    //boxSelectObj.html(str_options);
                }else{
                    $("#html_title").html("");
                    $("#html").html("");
                    showDialog(data.msg);return;
                    //alert(data.msg);return;
                }
                $("#html_title").html(html_title);
                $("#html").html(html);
                //boxSelectObj.html(str_options);
            } //可选参数
        });

});

</script>