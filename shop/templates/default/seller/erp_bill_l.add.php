<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>

<!--下拉美化js、css-->
<link href="<?php echo RESOURCE_SITE_URL;?>/js/chosen/css/chosen.css" rel="stylesheet" type="text/css">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/chosen/js/chosen.jquery.js"></script>
<!-- S setp -->
<ul class="add-goods-step">
  <li class="<?php $output['step']=="1" ? print "current" : print "";?>"><i class="icon icon-list-alt"></i>
    <h6>STIP.1</h6>
    <h2>创建进货单</h2>
    <i class="arrow icon-angle-right"></i> 
  </li>
  <li class="<?php $output['step']=="2" ? print "current" : print "";?>"><i class="icon icon-edit"></i>
    <h6>STIP.2</h6>
    <h2>导入货品</h2>
    <i class="arrow icon-angle-right"></i> 
  </li>
 <li class="<?php $output['step']=="3" ? print "current" : print "";?>"><i class="icon icon-ok-circle "></i>
    <h6>STIP.3</h6>
    <h2>保存完成</h2></li>
</ul>
<div class="alert mt15 mb5"><strong>珂兰技术中心操作提示：</strong>
  <ul>
    <li>1.下载进货单模板，打开Excel找到工号栏位，更改成自己的工号（您的工号为：<?php echo $_SESSION['seller_id']?>）<br>
	<li>2.在Excel录入货品信息，点击【完成提交数据】按钮 完成货品数据提交。<br>
    <li>3.返回进货单页面，点击 【导入库存】 按钮载入库存数据（Excel每重新提交一次数据，都需要重复第3项操作）<br>
	<li>4.填写其他信息，点击保存按钮，完成入库。
</li>
  </ul>
</div>
<!--S 分类选择区域-->
<form method="post" action="index.php?act=erp_bill_l&op=insert" enctype="multipart/form-data" id="erp_bill_l_form" onsubmit="return pre_submit();">
  <div class="ncsc-form-goods"  <?php if($output['step'] != '1'){?> style="display:none"<?php }?>>
    <dl>
      <dt><i class="required">*</i>供应商/入库方式</dt>
      <dd>
        <select name="supplier_id">
          <option value=""><?php echo $lang['nc_please_choose'];?></option>
		  <?php 
		  if (is_array($output['company_list'])) {
		      $count = count($output['company_list']);
		  ?>
        <?php foreach ($output['company_list'] as $key=>$val) {?>
        <option value="<?php echo $val['id'];?>"<?php echo $key+1==$count?' selected':''?>><?php echo $val['company_name'];?></option>
        <?php }?>
        <?php }?>
        </select>	
		<select name="put_in_type">
          <option value=""><?php echo $lang['nc_please_choose'];?></option>
		  <?php if (is_array($output['put_type_list'])) {?>
        <?php foreach ($output['put_type_list'] as $key=>$val) {?>
		<?php if($key!=5){ continue;}?>
        <option value="<?php echo $key;?>" <?= $key==5?"selected":''?>><?php echo $val;?></option>
        <?php }?>
        <?php }?>
        </select>
      </dd>
    </dl>	
	<dl>
      <dt><i class="required">*</i>入库仓库</dt>
      <dd>
        <select name="to_house_id" class="chose">
          <option value=""><?php echo $lang['nc_please_choose'];?></option>
		  <?php if (is_array($output['warehouse_list'])) {?>
        <?php foreach ($output['warehouse_list'] as $key=>$val) {?>
        <option value="<?php echo $val['store_id'];?>|<?php echo $val['house_id'];?>"><?php echo $val['name'];?></option>
        <?php }?>
        <?php }?>
        </select>
		<!--<select name="to_box_id" >
          <option value=""><?php echo $lang['nc_please_choose'];?></option>		  
        </select>-->
      </dd>
    </dl>
	<dl>
      <dt><i class="required">*</i>货品数据</dt>
      <dd>
	      <span id="import_data" style="display:none"></span>
          <span id="import_state" style="float:left;min-width:100px"><font color="red">未导入</font></span>
		  <input type="button" id="btn_import" class="submit" style="float:left" value="导入库存" />
		  <!--<a href="/data/upload/shop/excel/shouhuo.xls" class="blue ml5" style="float:left">进货单模板下载</a> -->
		  <a href="index.php?act=erp_bill_l&op=down_excel" class="blue ml5" style="float:left">进货单模板下载</a>
      </dd>
    </dl>
	<dl>
      <dt>单据备注</dt>
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
</form>
<!--step4--> 
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.form.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.ui-jqLoding.js"></script>
<script>
//下拉美化（可搜索）    
$(function(){
	$('.chose').chosen();
});


var formID = "erp_bill_l_form";
var optionsSubmit = {
	url: "index.php?act=erp_bill_l&op=insert",
	dataType:'json',	
	error:function ()
	{   //$(this).jqLoading("destroy");
	    $('#'+formID+' .submit').attr('disabled',false);//解锁
		alert("请求失败");
	},
	beforeSubmit:function(frm,jq,op){
	    //$(this).jqLoading({text:"正在处理中..."});
		$('#'+formID+' .submit').attr('disabled',true);//禁用
	},
	success: function(res) {
	    //$(this).jqLoading("destroy");
	    $('#'+formID+' .submit').attr('disabled',false);//解锁
		if(res.state){
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
/*
$("#"+formID+" select[name='to_house_id']").change(function(){
  var house_id = $(this).val();
  var boxSelectObj = $("#"+formID+" select[name='to_box_id']");
  var str_options='<option value="">请选择</option>';  
  if(house_id==""){
     boxSelectObj.html(str_options);
     return;
  }  
  house_id = house_id.split('|')[1];  
  $.getJSON('index.php?act=store_warehouse&op=get_box_list_ajax&house_id=' +house_id , function(data){  
        if (data) {
            for(var i=0;i<data.length;i++){
			    if(data.length == 1){				
			        str_options +='<option value="'+data[i]['box_name']+'" selected>'+data[i]['box_name']+'</option>';	
				}else{
				    str_options +='<option value="'+data[i]['box_name']+'">'+data[i]['box_name']+'</option>';	
				}		
			} 
			boxSelectObj.html(str_options);
        }
		boxSelectObj.html(str_options);
    });
});
*/
$("#btn_import").click(function(){
   $("#import_state").html("<font color='red'>导入中</font>");
   $.ajax({
		url:"index.php?act=erp_bill_l&op=import_js",
		type:"get",
		dataType:"html",
		data:{},
		success:function (data){
		   if(data.length>100){
			   $("#import_data").html(data);
			   var goods_num = $("#"+formID+" input[name='create_goods_num']").val();
			   var msg = "<font color='green'>导入成功（共计"+goods_num+"件货品）</font>";
			   $("#import_state").html(msg);
		   }else{
		        showDialog("导入失败：未提交货品信息");
		        $("#import_state").html("<font color='red'>导入失败</font>");
		   }
		}
   });
   
});
</script>