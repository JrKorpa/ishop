<form method="post" action="" id="check_bill_form">
<input type="hidden" name="bill_id" value="<?php echo $output['bill_info']['bill_id']?>"/>
<div class="ncsc-form-goods">
    <!--<dl>
      <dt>审核：</dt>
      <dd>
	  <input type="radio" name="check_status" checked="checked" value="1"/><label>通过</label>
	  <input type="radio" name="check_status" value="2"/><label>不通过</label>
	  </dd>	  
    </dl>
	<dl>
      <dt>备注：</dt>
      <dd><textarea class="w200" name="check_remark" rows="2">审核通过</textarea></dd>	  
    </dl>-->
    <dl>
        <dt>签入仓库：</dt>
        <dd>
            <select name="warehouse_id">
                <option value=""><?php echo $lang['nc_please_choose'];?></option>
                <?php if (is_array($output['warehouse_list'])) {?>
                    <?php foreach ($output['warehouse_list'] as $key=>$val) {?>
                        <option value="<?php echo $val['house_id'];?>"><?php echo $val['name'];?></option>
                    <?php }?>
                <?php }?>
            </select>
        </dd> 
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
	url: "index.php?act=erp_bill&op=check_sign_save",
	dataType:'json',	
	error:function ()
	{   
	    $('#check_bill_form .submit').attr('disabled',false);//解锁
		alert("请求失败");
	},
	beforeSubmit:function(frm,jq,op){
		$('#check_bill_form .submit').attr('disabled',true);//禁用
	},
	success: function(res) {
	    $('#check_bill_form .submit').attr('disabled',false);//解锁
		if(res.success == 1 ){
	      	showDialog('签收成功!','succ', '提示信息', function(){
		        window.location.href='index.php?act=erp_bill&op=show&bill_id='+<?php echo $_GET['bill_id']?>;
		    });
		}else{
		   showError(res.msg);
		}
	}
};
$("#check_bill_form #btn_submit").click(function(){
   $("#check_bill_form").ajaxSubmit(optionsSubmit);
});
//$("#check_bill_form input[name='check_status']").change(function(){
//  if($(this).val()==1){
//     $("#check_bill_form textarea[name='check_remark']").text("审核通过"); 
//  }else{
//     $("#check_bill_form textarea[name='check_remark']").text(""); 
//  }
//});

</script>
	