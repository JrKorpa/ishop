<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<!-- S setp -->
<ul class="add-goods-step">
  <li class="<?php $output['step']=="1" ? print "current" : print "";?>"><i class="icon icon-list-alt"></i>
    <h6>STIP.1</h6>
    <h2>创建其他入库单</h2>
    <i class="arrow icon-angle-right"></i> 
  </li>
  <li class="<?php $output['step']=="2" ? print "current" : print "";?>"><i class="icon icon-edit"></i>
    <h6>STIP.2</h6>
    <h2>导入CSV</h2>
    <i class="arrow icon-angle-right"></i> 
  </li>
 <li class="<?php $output['step']=="3" ? print "current" : print "";?>"><i class="icon icon-ok-circle "></i>
    <h6>STIP.3</h6>
    <h2>保存完成</h2></li>
</ul>
<div class="alert mt15 mb5"><strong>珂兰技术中心操作提示：</strong>
  <ul>
    <li>1.如果修改CSV文件请务必使用微软excel软件，且必须保证第一行表头名称含有如下项目: 
SKU码、成本价、建议零售价、数量<br>
<li>2.文件总记录行不能大于10000; 超过限制，请分成多个文件<br>
</li>
  </ul>
</div>
<!--S 分类选择区域-->
<form method="post" action="index.php?act=erp_bill_t&op=insert" enctype="multipart/form-data" id="erp_bill_t_form" onsubmit="return pre_submit();">
  <div class="ncsc-form-goods"  <?php if($output['step'] != '1'){?> style="display:none"<?php }?>>
    <dl>
      <dt>供应商</dt>
      <dd>
        <select name="supplier_id">
          <option value=""><?php echo $lang['nc_please_choose'];?></option>
		  <?php if (is_array($output['supplier_list'])) {?>
        <?php foreach ($output['supplier_list'] as $key=>$val) {?>
        <option value="<?php echo $val['sup_id'];?>"><?php echo $val['sup_name'];?></option>
        <?php }?>
        <?php }?>
        </select>
      </dd>
    </dl>
	<dl>
      <dt><i class="required">*</i>入库仓库</dt>
      <dd>
        <select name="to_house_id">
          <option value=""><?php echo $lang['nc_please_choose'];?></option>
		  <?php if (is_array($output['warehouse_list'])) {?>
        <?php foreach ($output['warehouse_list'] as $key=>$val) {?>
        <option value="<?php echo $val['chain_id'];?>|<?php echo $val['house_id'];?>"><?php echo $val['name'];?></option>
        <?php }?>
        <?php }?>
        </select>
		<select name="to_box_id">
          <option value=""><?php echo $lang['nc_please_choose'];?></option>		  
        </select>
      </dd>
    </dl>
	<dl>
      <dt><i class="required">*</i>CSV文件</dt>
      <dd>
          <input type="file" name="file" />
		  <a href="<?php echo urlShop('erp_bill_t', 'down_template');?>" class="blue ml5">其他入库单模板下载</a>		 
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
var formID = "erp_bill_t_form";
var optionsSubmit = {
	url: "index.php?act=erp_bill_t&op=insert",
	dataType:'json',	
	error:function ()
	{    $(this).jqLoading("destroy");
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
		   showError(res.msg);
		}
	}
};
$("#"+formID+" #btn_submit").click(function(){
   $("#"+formID).ajaxSubmit(optionsSubmit);
});
$("#"+formID+" select[name='to_house_id']").change(function(){
  var house_id = $(this).val();
  var boxSelectObj = $("#"+formID+" select[name='to_box_id']");
  var str_options='<option value="">请选择...</option>';  
  if(house_id==""){
     boxSelectObj.html(str_options);
     return;
  }  
  house_id = house_id.split('|')[1];  
  $.getJSON('index.php?act=store_warehouse&op=get_box_list_ajax&house_id=' +house_id , function(data){  
        if (data) {		    
            for(var i=0;i<data.length;i++){
			    str_options +='<option value="'+data[i]['box_id']+'">'+data[i]['box_name']+'</option>';			
			} 
			boxSelectObj.html(str_options);
        }
		boxSelectObj.html(str_options);
    });
});
</script>