<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>

<!--下拉美化js、css-->
<link href="<?php echo RESOURCE_SITE_URL;?>/js/chosen/css/chosen.css" rel="stylesheet" type="text/css">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/chosen/js/chosen.jquery.js"></script>
<!-- S setp -->
<ul class="add-goods-step">
  <li class="<?php $output['step']=="1" ? print "current" : print "";?>"><i class="icon icon-list-alt"></i>
    <h6>STIP.1</h6>
    <h2>创建盘点单</h2>
    <i class="arrow icon-angle-right"></i> 
  </li>
  <li class="<?php $output['step']=="2" ? print "current" : print "";?>"><i class="icon icon-ok-circle "></i>
    <h6>STIP.2</h6>
    <h2>保存完成</h2>
  </li>
</ul>
<div class="alert mt15 mb5"><strong>珂兰技术中心操作提示：</strong>
  <ul>
    <li>1.盘点仓库，可选择全部或一个仓库进行盘点<br>
	<li>2.在途货品不计入应盘数量<br>
    <li>3.盘点中的仓库不允许签收货品，其中的货品不允许做出库，只允许下现货订单<br>
  </ul>
</div>
<!--S 分类选择区域-->
<form method="post" action="index.php?act=erp_bill_w&op=insert" enctype="multipart/form-data" id="erp_bill_w_form" onsubmit="return pre_submit();">
  <div class="ncsc-form-goods"  <?php if($output['step'] != '1'){?> style="display:none"<?php }?>>
    
	<dl>
      <dt><i class="required">*</i>盘点仓库</dt>
      <dd>
        <select name="warehouse_id" class="chose" style="min-width:220px">
		  <option value="">请选择仓库</option>
          <option value="0">全部仓库</option>
		  <?php if (is_array($output['warehouse_list'])) {?>
        <?php foreach ($output['warehouse_list'] as $key=>$val) {?>
        <option value="<?php echo $val['house_id'];?>"><?php echo $val['name'];?></option>
        <?php }?>
        <?php }?>
        </select>
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


var formID = "erp_bill_w_form";
var optionsSubmit = {
	url: "index.php?act=erp_bill_w&op=insert",
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
		if(res.state){
	      	showDialog('添加成功! 单据编号：'+res.data.bill_no,'succ', '提示信息', function(){
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
</script>