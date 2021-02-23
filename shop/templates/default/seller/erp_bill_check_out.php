<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<!-- S setp -->
<ul class="add-goods-step">
  <li class="<?php $output['step']=="1" ? print "current" : print "";?>"><i class="icon icon-list-alt"></i>
    <h6>STIP.1</h6>
    <h2>录入批发销售单号</h2>
    <i class="arrow icon-angle-right"></i> 
  </li>
  <li class="<?php $output['step']=="2" ? print "current" : print "";?>"><i class="icon icon-edit"></i>
    <h6>STIP.2</h6>
    <h2>录入货号</h2>
    <i class="arrow icon-angle-right"></i> 
  </li>
 <li class="<?php $output['step']=="3" ? print "current" : print "";?>"><i class="icon icon-ok-circle "></i>
    <h6>STIP.3</h6>
    <h2>结算完成</h2></li>
</ul>
<div class="alert mt15 mb5"><strong>珂兰技术中心操作提示：</strong>
  <ul>
    <li>1.录入货号必须在批发销售单中。<br>
</li>
  </ul>
</div>
<!--S 分类选择区域-->
<form method="post" action="index.php?act=erp_bill_checkout&op=index" id="checkout_form">
 <input type="hidden" name="form_submit" value="ok">
 <input type="hidden" name="process_type" id="process_type" value="JS">
  <div class="ncsc-form-goods">
      <dl>
          <dt><i class="required">*</i>批发销售单号：</dt>
          <dd>
              <input class="text w200" name="bill_no" rows="2" placeholder="输入批发销售单号"/>
          </dd>
      </dl>
     <dl>
         <dt><i class="required">*</i>货号</dt>
         <dd>
             <textarea class="w600" name="goods_itemid" style="height:120px" placeholder="扫码批量输入，用逗号(,)或回车隔开"></textarea>
         </dd>
     </dl>
    <dl>
      <dt>&nbsp;</dt>
      <dd>
        <input type="button" id="btn_reset"  class="submit" value="重置"  style="display:inherit;background-color: #f1f1f7; "/>
        <input type="button" id="btn_submit" class="submit" value="结算" style="display:inherit;"/>
        <input type="button" id="btn_return" class="submit" value="退货"  style="display:inherit;background-color: #c1c1ef"/>
      </dd>
    </dl>
    </ul>
  </div>
</form>
<!--step4--> 
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.form.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.ui-jqLoding.js"></script>
<script>
var formID = "checkout_form";
var optionsSubmit = {
	url: "index.php?act=erp_bill_checkout&op=index",
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
	      	showDialog('操作成功！','succ', '提示信息', function(){
                $("#"+formID).resetForm();
            });
		}else{
		   showError(res.msg);
		}
	}
};
$("#"+formID+" #btn_submit").click(function(){
    $("#process_type").val("JS");
   $("#"+formID).ajaxSubmit(optionsSubmit);
});
$("#"+formID+" #btn_return").click(function(){
    $("#process_type").val("TH");
    $("#"+formID).ajaxSubmit(optionsSubmit);
});
$("#"+formID+" #btn_reset").click(function(){
    $("#"+formID).resetForm();
});
</script>