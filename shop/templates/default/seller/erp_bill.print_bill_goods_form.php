<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<!--下拉美化js、css-->
<link href="<?php echo RESOURCE_SITE_URL;?>/js/chosen/css/chosen.css" rel="stylesheet" type="text/css">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/chosen/js/chosen.jquery.js"></script>
<div class="eject_con">
  <?php if($output['error']){?>
   <div id="warning" class="alert alert-error" style="display:inherit"><?php echo $output['error']?></div>
   <div class="bottom">
      <label class="submit-border"><input type="button" onClick="$('.dialog_close_button').click();" class="submit" value="确定" /></label>
   </div>
  <?php }else{?>
  <div id="warning" class="alert alert-error"><?php echo $output['error']?></div>
  <form method="get" action="index.php?act=erp_bill&op=print_bill_goods" id="print_form" target="_blank">
    <input type="hidden" name="act" value="erp_bill"/>
	<input type="hidden" name="op" value="print_bill_goods"/>
	<input type="hidden" name="_ids" value="<?php echo $_GET['_ids']?>"/>
    <dl>
      <dt><i class="required">*</i>销售渠道：</dt>
      <dd>
          <select name="store_id" class="w200 chose">
		      <option value="">请选择...</option>
              <?php echo paramsHelper::echoArrayOption($output['store_list'],"store_id","store_name",0); ?>
          </select>
      </dd>
    </dl>
    <div class="bottom">
      <label class="submit-border"><input type="submit" nctype="print_submit" class="submit" value="确定" /></label>
    </div>
  </form>
  <?php }?>
</div>
<script>
var SITEURL = "<?php echo SHOP_SITE_URL; ?>";
$(document).ready(function(){
	$('.chose').chosen();//下拉美化（可搜索）
	$('input[nctype="print_submit"]').click(function(){
		if ($('#print_form').valid()) {
            return true;
		}
	});
    $('#print_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
           var errors = validator.numberOfInvalids();
           if(errors)
           {
               $('#warning').show();
           }
           else
           {
               $('#warning').hide();
           }
        },
        rules : {
            store_id : {
                required : true
            },
        },
        messages : {
            store_id : {
                required : '<i class="icon-exclamation-sign"></i>请选择销售渠道'
            },
        }
    });
});
</script> 
