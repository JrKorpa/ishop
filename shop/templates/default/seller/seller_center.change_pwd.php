<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>

<div class="eject_con">
  <div id="warning" class="alert alert-error"></div>
  <form method="post" action="index.php?act=seller_center&op=change_pwd" id="store_change_pwd_form" target="_parent">
    <input type="hidden" name="form_submit" value="ok" />
	<dl>
      <dt><i class="required">*</i>原始密码：</dt>
      <dd>
          <input type="password" name="old_password"/>
      </dd>
    </dl>
	<dl>
      <dt><i class="required">*</i>新密码：</dt>
      <dd>
          <input type="password" name="password"/>
      </dd>
    </dl>
	<dl>
      <dt><i class="required">*</i>重复密码：</dt>
      <dd>
          <input type="password" name="confirm_password"/>
      </dd>
    </dl>
    <div class="bottom">
      <label class="submit-border"><input type="button" nctype="store_change_pwd_submit" class="submit" value="确定" /></label>
    </div>
  </form>
</div>
<script>
var SITEURL = "<?php echo SHOP_SITE_URL; ?>";
$(document).ready(function(){
	$("#region").nc_region();
	$('input[nctype="store_change_pwd_submit"]').click(function(){
		if ($('#store_change_pwd_form').valid()) {
            $.post("<?php echo urlShop('seller_center', 'change_pwd',array('inajax'=>1));?>",$('#store_change_pwd_form').serialize(), function(res) {
               if(res.state){
			       alert("修改成功");
				   CUR_DIALOG.close();
			   }else{
			       alert(res.msg);
			   }
            }, "json");
		}
	});
    $('#store_change_pwd_form').validate({
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
            old_password : {
                required : true
            },
			password : {
                required : true
            },
			confirm_password : {
                required : true
            },
        },
        messages : {
            old_password : {
                required : '<i class="icon-exclamation-sign"></i>请输入原始密码'
            },
			password : {
                required : '<i class="icon-exclamation-sign"></i>请输入新密码'
            },
			confirm_password : {
                required : '<i class="icon-exclamation-sign"></i>请再次输入新密码'
            },
        }
    });
});
</script> 
