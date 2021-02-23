<div class="eject_con">
<div id="warning" class="alert alert-error"></div>
<?php if ($output['order_info']) {?>
  <form id="changeform" method="post">
    <input type="hidden" name="form_submit" value="ok" />
    <dl>
      <dt>订单应付金额</dt>
      <dd>
          <label><?php echo $output['order_info']['order_amount']; ?> </label>
      </dd>
    </dl>
      <dl>
          <dt>已支付金额</dt>
          <dd>
              <label><?php echo $output['order_info']['rcb_amount']; ?> </label>
          </dd>
      </dl>
	  <dl>
          <dt>退款金额</dt>
          <dd>
              <label><?php echo $output['order_info']['refund_amount']; ?> </label>
          </dd>
      </dl>
      <dl>
          <dt><i class="required">*</i>本次收款金额</dt>
          <dd>
              <input type="text" class="text" id="money" name="money"  />
        (剩余：<?php echo $output['order_info']['order_amount']-$output['order_info']['rcb_amount'] + $output['order_info']['refund_amount'] + $output['order_info']['breach_amount']; ?>)
          </dd>
      </dl>
      <dl>
          <dt>支付方式</dt>
          <dd>
              <select id="payment_code" name="payment_code" class="select w160" >
                  <?php
                      foreach ($output['payment_list'] as $payment){
                          echo '<option value="'.$payment["payment_code"].'">'.$payment["payment_name"].'</option>';
                      }
                  ?>
              </select>
          </dd>
      </dl>
	  <dl>
          <dt><i class="required">*</i>支付时间</dt>
          <dd>
             <input type="text" class="text w70" name="pay_date" id="pay_date" value="<?php echo $_GET['pay_date']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label>
          </dd>
      </dl>
      <dl>
          <dt>银行账号</dt>
          <dd>
              <input type="text" id="pay_account" name="pay_account">
          </dd>
      </dl>
      <dl>
          <dt>支付流水号</dt>
          <dd>
              <input type="text" id="pay_sn" name="pay_sn">
          </dd>
      </dl>
      <dl>
          <dt>收款备注</dt>
          <dd>
              <input type="text" id="remark" name="remark">
          </dd>
      </dl>
    <dl class="bottom">
      <dt>&nbsp;</dt>
      <dd>
        <input type="button" class="submit" id="confirm_button" value="<?php echo $lang['nc_ok'];?>" />
      </dd>
    </dl>
      <input type="hidden" name="order_id" id="order_id" value="<?php echo $output['order_id'];?>" >
      <input type="hidden" name="order_sn" id="order_sn" value="<?php echo $output['order_sn'];?>" >
      <input type="hidden" name="payment_name" id="payment_name" value="">
  </form>
<?php } else { ?>
<p style="line-height:80px;text-align:center">该订单并不存在，请检查参数是否正确!</p>
<?php } ?>
</div>
<script type="text/javascript">
$(function(){
	$('#pay_date').datepicker({dateFormat: 'yy-mm-dd',maxDate: 0,/*minDate: -5 */});   
    $('#changeform').validate({
    	errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
           var errors = validator.numberOfInvalids();
           if(errors){ $('#warning').show();}else{ $('#warning').hide(); }
        },
     	submitHandler:function(form){
    		ajaxpost('changeform', '', '', 'onerror'); 
    	},    
	    rules : {
            money : {
	            required : true,
	            number : true
	        },
			pay_date:{
			    required : true,
			}
	    },
	    messages : {
            money : {
	    		required : '收款金额不能为空且必须为大于0的数字',
            	number : '收款金额不能为空且必须为大于0的数字'
	        },
			pay_date: {
			    required : "支付时间必填",
			}
	    }
	});

    $("#confirm_button").click( function(){
        //var p_pay=<?php echo $output['order_info']['order_amount']-$output['order_info']['rcb_amount']; ?>;
        var p_pay=<?php echo $output['order_info']['order_amount']; ?>;
        var money=$("#money").val();
        if(money>p_pay){
            alert("收款金额不能大于未支付金额");
            return;
        }
        $("#payment_name").val($('#payment_code option:selected').text());
        var url='index.php?act=store_order&op=change_state&state_type=order_pay&order_id='+<?php echo $output['order_info']['order_id'] ?>;
        $("#changeform").attr("action",url);
        $("#changeform").submit();
    });
});
</script>