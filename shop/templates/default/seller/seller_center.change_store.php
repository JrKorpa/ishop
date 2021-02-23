<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<!--下拉美化js、css-->
<link href="<?php echo RESOURCE_SITE_URL;?>/js/chosen/css/chosen.css" rel="stylesheet" type="text/css">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/chosen/js/chosen.jquery.js"></script>
<div class="eject_con">
  <div id="warning" class="alert alert-error"></div>
  <form method="post" action="index.php?act=seller_center&op=change_store" id="store_change_form" target="_parent">
    <input type="hidden" name="form_submit" value="ok" />
    <dl>
      <dt><i class="required">*</i>店铺：</dt>
      <dd>
          <select name="store_id" class="w200 chose">
              <?php echo paramsHelper::echoArrayOption($output['store_list'],"store_id","store_name",$_SESSION["store_id"]); ?>
          </select>
      </dd>
    </dl>
    <div class="bottom">
      <label class="submit-border"><input type="submit" nctype="store_change_submit" class="submit" value="确定" /></label>
    </div>
  </form>
</div>
<script>
var SITEURL = "<?php echo SHOP_SITE_URL; ?>";
$(document).ready(function(){
	$('.chose').chosen();//下拉美化（可搜索）
	
	$("#region").nc_region();
	$('input[nctype="store_change_submit"]').click(function(){
		if ($('#store_change_form').valid()) {
            $.post("<?php echo urlShop('seller_center', 'change_store');?>",$('#store_change_form').serialize(), function(data) {

            }, "json");
		}
	});
    $('#store_change_form').validate({
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
                required : '<i class="icon-exclamation-sign"></i>请选择店铺'
            },
        }
    });
});
</script> 
