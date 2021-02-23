<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<link href="<?php echo RESOURCE_SITE_URL;?>/js/chosen/css/chosen.css" rel="stylesheet" type="text/css">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/chosen/js/chosen.jquery.js"></script>
<!--下拉搜索样式-->
<style type="text/css">
.eject_con dl{
	overflow: visible;
}
.chosen-container{
	position: fixed;
}
</style>
<div class="eject_con">
  <div class="adds">
    <div id="warning"></div>
    <form method="post" action="index.php?act=store_order&op=edit_invoice&order_id=<?php echo $_GET['order_id'];?>" id="address_form" target="_parent">
      <input type="hidden" name="form_submit" value="ok" />
      <!--<dl>
        <dt class="required">发票类型：</dt>
        <dd>
            个人<input type="radio" value="个人"  name="invoice_type"  class="radio-box" <?php echo $output['common_info']['invoice_info']['类型']=="个人"?"checked":"";?> >
            单位<input type="radio" value="单位"  name="invoice_type"  class="radio-box" <?php echo $output['common_info']['invoice_info']['类型']=="单位"?"checked":"";?>>
        </dd>
      </dl>-->
      <dl>
        <dt class="required">客户姓名：</dt>
        <dd>
          <input type="text" class="text" name="buyer_name" id="buyer_name" value="<?php echo $output['order_info']['buyer_name'];?>"/>
        </dd>
      </dl>
      <dl>
        <dt class="required">手机号：</dt>
        <dd>
          <input type="text" class="text" name="buyer_phone" id="buyer_phone" value="<?php echo $output['order_info']['buyer_phone'];?>"/>
        </dd>
      </dl>
      <dl>
        <dt class="required">制单人：</dt>
        <dd>
          <input type="text" class="text" name="seller_name" id="seller_name" value="<?php echo $output['order_info']['seller_name'];?>"/>
        </dd>
      </dl>
      <dl>
        <dt class="required">客户来源：</dt>
        <dd>
            <select name="customer_source_id" id="customer_source_id" class="w160 chose">
                <option value="">请选择</option>
                <?php echo $output['order_info']['sourcelist'];?>
            </select>
        </dd>
      </dl>
      <div class="bottom"><label class="submit-border"><a href="javascript:void(0);" id="submit" class="submit"><?php echo $lang['nc_common_button_save'];?></a></label></div>
    </form>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	//下拉美化（可搜索）
	$('.chose').chosen();
	
    window.setTimeout(function(){
        $('#buyer_name').val('<?php echo  $output['order_info']['buyer_name'];?>');
        $('#buyer_phone').val('<?php echo $output['order_info']['buyer_phone'];?>');
        $('#seller_name').val('<?php echo $output['order_info']['seller_name'];?>');
        $('#customer_source_id').val('<?php echo $output['order_info']['customer_source_id'];?>');
    },300)

    $('#address_form').validate({
        rules : {
            buyer_name : {
                required : true
            },
            buyer_phone : {
                required : true
            },
            seller_name: {
                required : true
            },
            customer_source_id: {
                required : true
            }
        },
        messages : {
            buyer_name: {
                required : '<i class="icon-exclamation-sign"></i>客户姓名不能为空'
            },
            buyer_phone: {
                required : '<i class="icon-exclamation-sign"></i>客户电话不能为空'
            },
            seller_name: {
                required : '<i class="icon-exclamation-sign"></i>制单人不能为空'
            },
            customer_source_id: {
                required : '<i class="icon-exclamation-sign"></i>客户来源不能为空'
            }
        }
    });
	$('#submit').on('click',function(){
		if ($('#address_form').valid()) {
            var buyer_name = $('#buyer_name').val();
            var buyer_phone = $('#buyer_phone').val();
            var seller_name = $('#seller_name').val();
            var customer_source_id = $('#customer_source_id').val();
            var customer_source_name = $("#customer_source_id").find("option:selected").text();
            $.ajax({
                url:  "<?php echo urlShop('store_order', 'save_order');?>",
                type: 'post',
                data: {order_id: <?php echo $output['order_info']["order_id"];?>, buyer_name: buyer_name, buyer_phone: buyer_phone, seller_name: seller_name, customer_source_id:customer_source_id, customer_source_name:customer_source_name},
            }).done(function(res) {
                //var buyer_name = buyer_name;
                //$('#buyer_name1').html(buyer_name);
                //var buyer_phone = buyer_phone;
                //$('#buyer_phone1').html(buyer_phone);
                //var seller_name = seller_name;
                //$('#seller_name1').html(seller_name);
                //var customer_source_name = customer_source_name;
                //$('#customer_source_name').html(customer_source_name);
                DialogManager.close('edit_order');
                window.location.reload();
            }).fail(function(res) {
                console.log(JSON.parse(res));
            });
		}
	});

});
</script>
