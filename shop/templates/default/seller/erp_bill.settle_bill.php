<form method="post" action="" id="settle_bill_form">
<input type="hidden" name="bill_id" value="<?php echo $output['bill_info']['bill_id']?>"/>
<div class="ncsc-form-goods">
    <dl>
      <dt>结算：</dt>
      <dd>
	  <input type="radio" checked="checked" value="1"/><label>通过</label>
	  </dd>	  
    </dl>
	<dl>
      <dt>备注：</dt>
      <dd><textarea class="w200" name="remark" rows="2">结算通过</textarea></dd>	  
    </dl>
	<dl>
      <dt></dt>
      <dd><input type="button" id="btn_submit" class="submit" value="保存" /></dd>	  
    </dl>
</div>	
</form>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.form.js"></script>
<script>
var optionsSubmit = {
	url: "index.php?act=erp_bill&op=settle_bill_save",
	dataType:'json',	
	error:function ()
	{   
	    $('#settle_bill_form .submit').attr('disabled',false);//解锁
		alert("请求失败");
	},
	beforeSubmit:function(frm,jq,op){
		$('#settle_bill_form .submit').attr('disabled',true);//禁用
	},
	success: function(res) {
	    $('#settle_bill_form .submit').attr('disabled',false);//解锁
		if(res.success == 1 ){
	      	showDialog('结算成功!','succ', '提示信息', function(){
		        window.location.href='index.php?act=erp_bill&op=show&bill_id='+<?php echo $_GET['bill_id']?>;
		    });
		}else{
		   showError(res.msg);
		}
	}
};
$("#settle_bill_form #btn_submit").click(function(){
   $("#settle_bill_form").ajaxSubmit(optionsSubmit);
});

</script>
	