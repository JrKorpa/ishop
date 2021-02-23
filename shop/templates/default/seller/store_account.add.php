<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>

<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<div class="ncsc-form-default">
  <form id="add_form" action="<?php echo urlShop('store_account', 'account_save');?>" method="post">
    <dl>
      <dt><i class="required">*</i>用户名<?php echo $lang['nc_colon'];?></dt>
      <dd><input class="w120 text" name="member_name" type="text" id="member_name" value="" />
          <span></span>
        <p class="hint"></p>
      </dd>
    </dl>
    <dl id="passwords">
      <dt><i class="required">*</i>用户密码<?php echo $lang['nc_colon'];?></dt>
      <dd><input class="w120 text" name="password" type="password" id="password" value="" />
          <span></span>
        <p class="hint"></p>
      </dd>
    </dl>
    <dl>
      <dt><i class="required">*</i>真实姓名<?php echo $lang['nc_colon'];?></dt>
      <dd><input class="w120 text" name="real_name" type="text" id="real_name" value="" />
          <span></span>
        <p class="hint"></p>
      </dd>
    </dl>
    <dl>
      <dt><i class="required">*</i>账号组<?php echo $lang['nc_colon'];?></dt>
      <dd><select name="group_id">
            <?php foreach($output['seller_group_list'] as $value) { ?>
            <option value="<?php echo $value['group_id'];?>"><?php echo $value['group_name'];?></option>
            <?php } ?>
          </select>
          <span></span>
        <p class="hint"></p>
      </dd>
    </dl>
    <!--
    <dl>
      <dt><i class="required"></i>客户端登录<?php echo $lang['nc_colon'];?></dt>
      <dd>
          <input type="radio" id="is_client_1" name="is_client" value="1">
          <label for="is_client_1">允许</label>
          <input type="radio" id="is_client_0" name="is_client" value="0" checked>
          <label for="is_client_0">不允许</label>
          <span></span>
        <p class="hint">设置为允许后，该用户可以使用客户端登录，并使用客户端软件进行相关操作</p>
      </dd>
    </dl>
    -->
    <div class="bottom">
      <label class="submit-border">
        <input type="submit" class="submit" value="<?php echo $lang['nc_submit'];?>">
      </label>
    </div>
  </form>
</div>
<script>
$(document).ready(function(){
    jQuery.validator.addMethod("seller_name_exist", function(value, element, params) { 
        var result = true;
        $.ajax({  
            type:"GET",  
            url:'<?php echo urlShop('store_account', 'check_seller_name_exist');?>',  
            async:false,  
            data:{seller_name: $('#member_name').val()},  
            success: function(data){  
                if(data == 'true') {
                    //$.validator.messages.seller_name_exist = "账号已存在";
                    //result = false;
                    $("#passwords").hide();
                    $("#add_form input[name='password']").val("");
                }else{
                    $("#passwords").show();
                }
            }  
        });  
        return result;
    }, '');

    $('#add_form').validate({
        onkeyup: false,
        errorPlacement: function(error, element){
            element.nextAll('span').first().after(error);
        },
    	submitHandler:function(form){
    		ajaxpost('add_form', '', '', 'onerror');
    	},
        rules: {
            member_name: {
                required: true,
                minlength: 2,
                maxlength: 10, 
                seller_name_exist: true
            },
            /*password: {
                required: true,
                minlength: 6
            },*/
            real_name: {
                required: true,
                minlength: 2,
                maxlength: 10, 
            },
            group_id: {
                required: true
            }
        },
        messages: {
            member_name: {
                required: '<i class="icon-exclamation-sign"></i>用户名不能为空',
                minlength: '<i class="icon-exclamation-sign"></i>用户名最少2个字',               
                maxlength: '<i class="icon-exclamation-sign"></i>用户名最多10个字'
            },
            /*password: {
                required: '<i class="icon-exclamation-sign"></i>用户密码不能为空',
                minlength: '<i class="icon-exclamation-sign"></i>密码最少6个位数'
            },*/
            real_name: {
                required: '<i class="icon-exclamation-sign"></i>真实姓名不能为空',
                minlength: '<i class="icon-exclamation-sign"></i>用户名最少2个字', 
                maxlength: '<i class="icon-exclamation-sign"></i>真实姓名最多10个字'
            },
            group_id: {
                required: '<i class="icon-exclamation-sign"></i>请选择账号组'
            }
        }
    });
});
</script> 
