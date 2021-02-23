
<div class="eject_con">
  <div id="warning"></div>
  <form method="post" id="order_audit_form" >
    <input type="hidden" name="form_submit" value="ok" />
    <dl>
      <dt></dt>
      <dd>订单<span class="num"><?php echo trim($_GET['order_sn']); ?></span>是否允许布产</dd>
    </dl>
     
      
    <dl class="bottom">
      <dt>&nbsp;</dt>
      <dd>
	     <input type="hidden" name="state_type" value="1"/>
        <input type="button" class="submit" id="confirm_button" value="<?php echo $lang['nc_ok'];?>" />
      </dd>
    </dl>
      <input type="hidden" name="order_id"  id="order_id" value="<?php echo trim($_GET['order_id']); ?>" >
  </form>
</div>
<script type="text/javascript">
$(function(){
    $('#cancel_button').click(function(){
        DialogManager.close('seller_order_cancel_order');
     });
	 
	  $('#order_audit_form').validate({
    	
        invalidHandler: function(form, validator) {
           var errors = validator.numberOfInvalids();
        
        },
     	submitHandler:function(form){
    		ajaxpost('order_audit_form', '', '', 'onerror'); 
    	},    
	    rules : {
            
	    },
	    messages : {
          
	    }
	});
	 
	 $("#confirm_button").click( function(){
		var url='index.php?act=store_order&op=change_state&state_type=order_bc&order_id='+<?php echo $output['order_info']['order_id'] ?>;
		$("#order_audit_form").attr("action",url);
		$("#order_audit_form").submit();
	});
});



</script> 
