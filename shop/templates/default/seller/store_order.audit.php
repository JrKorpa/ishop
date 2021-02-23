<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<div class="eject_con">
  <div id="warning"></div>
  <form method="post" id="order_audit_form" >
    <input type="hidden" name="form_submit" value="ok" />
    <dl>
      <dt><?php echo $lang['store_order_order_sn'].$lang['nc_colon'];?></dt>
      <dd><span class="num"><?php echo trim($_GET['order_sn']); ?></span></dd>
    </dl>
      <dl>
          <dt>审核结果：</dt>
          <dd>
              <input type="radio" value="order_pass_audit"  name="state_type" id="state_type1" checked="checked" class="radio-box"><label for="state_type1">通过</label>
              <input type="radio" value="order_cancel_audit"  name="state_type" id="state_type2" class="radio-box"><label for="state_type2">不通过</label>
          </dd>
      </dl>
      <dl>
          <dt> 审核备注：</dt>
          <dd>
              <ul>
                  <li id="other_reason" style="height:48px;">
                      <textarea name="remark" rows="2"  id="remark" style="width:200px;"></textarea>
                  </li>
                  <li id="other_reason" style="height:48px;">
                  </li>
              </ul>
          </dd>
      </dl>
    <dl class="bottom">
      <dt>&nbsp;</dt>
      <dd>
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
});

$("#confirm_button").click( function(){
    var url='index.php?act=store_order&op=change_state&state_type=order_audit&order_id='+<?php echo $output['order_info']['order_id'] ?>;
    $("#order_audit_form").attr("action",url);
    $("#order_audit_form").submit();
});

</script> 
