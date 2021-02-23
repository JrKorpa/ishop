<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>

<div class="eject_con">
  <div class="adds">
    <div id="warning"></div>
    <form method="post" action="index.php?act=store_order&op=edit_invoice&order_id=<?php echo $_GET['order_id'];?>" id="address_form" target="_parent">
      <input type="hidden" name="form_submit" value="ok" />
      <dl>
        <dt class="required">发票类型：</dt>
        <dd>
            个人<input type="radio" value="个人"  name="invoice_type"  class="radio-box" <?php echo $output['common_info']['invoice_info']['类型']=="个人"?"checked":"";?> >
            单位<input type="radio" value="单位"  name="invoice_type"  class="radio-box" <?php echo $output['common_info']['invoice_info']['类型']=="单位"?"checked":"";?>>
        </dd>
      </dl>
      <dl>
        <dt class="required">抬头：</dt>
        <dd>
          <input type="text" class="text" name="header" id="header" value="<?php echo $output['common_info']['invoice_info']['抬头'];?>"/>
        </dd>
      </dl>
      <dl>
        <dt class="required">发票内容：</dt>
        <dd>
            <select name="content" id="content" class="w160">
                <option value="明细">明细</option>
                <option value="酒">酒</option>
                <option value="食品">食品</option>
                <option value="饮料">饮料</option>
                <option value="玩具">玩具</option>
                <option value="日用品">日用品</option>
                <option value="装修材料">装修材料</option>
                <option value="化妆品">化妆品</option>
                <option value="办公用品">办公用品</option>
                <option value="学生用品">学生用品</option>
                <option value="家居用品">家居用品</option>
                <option value="饰品">饰品</option>
                <option value="服装">服装</option>
                <option value="箱包">箱包</option>
                <option value="精品">精品</option>
                <option value="家电">家电</option>
                <option value="劳防用品">劳防用品</option>
                <option value="耗材">耗材</option>
                <option value="电脑配件">电脑配件</option>
            </select>
        </dd>
      </dl>
      <div class="bottom"><label class="submit-border"><a href="javascript:void(0);" id="submit" class="submit"><?php echo $lang['nc_common_button_save'];?></a></label></div>
    </form>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
    window.setTimeout(function(){
        $('#content').val('<?php echo  $output['common_info']['invoice_info']['内容'];?>');
        $('#invoice_type').val('<?php echo $output['common_info']['invoice_info']['类型'];?>');
        $('#header').val('<?php echo $output['common_info']['invoice_info']['抬头'];?>');
    },300)

    $('#address_form').validate({
        rules : {
            header : {
                required : true
            },
            invoice_type : {
                required : true
            },
            content: {
                required : true
            }
        },
        messages : {
            header: {
                required : '<i class="icon-exclamation-sign"></i>抬头不能为空'
            },
            invoice_type: {
                required : '<i class="icon-exclamation-sign"></i>类型不能为空'
            },
            content: {
                required : '<i class="icon-exclamation-sign"></i>内容不能为空'
            }
        }
    });
	$('#submit').on('click',function(){
		if ($('#address_form').valid()) {
            var content = $('#content').val();
            var invoice_type = $("input[name='invoice_type']:checked").val();
            var header = $('#header').val();
            $.ajax({
                url:  "<?php echo urlShop('store_order', 'save_invoice');?>",
                type: 'post',
                data: {order_id: <?php echo $output['common_info']["order_id"];?>, content: content, invoice_type: invoice_type, header: header},
            }).done(function(res) {
                console.log(res);
                var invoice_content = invoice_type + '&nbsp;' + header + '&nbsp;' + content;
                $('#invoice_span').html(invoice_content);
                DialogManager.close('edit_invoice');
            }).fail(function(res) {
                console.log(JSON.parse(res));
            });
		}
	});

});
</script>
