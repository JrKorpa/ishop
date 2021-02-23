<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>

<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.ajaxContent.pack.js"></script>

<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<div class="ncsc-form-default">
  <form method="post" action="<?php if (empty($output['plate_info'])) { echo urlShop('base_lz_discount_config', 'baselzdiscountconfig_add');} else { echo urlShop('base_lz_discount_config', 'baselzdiscountconfig_edit');}?>" id="plate_form">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="id" value="<?php echo $output['plate_info']['id'];?>"/>
        <dl>
          <dt><i class="required">*</i>销售顾问<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <select name="user_id"  <?php if (isset($output['plate_info']) && !empty($output['plate_info'])) {?>disabled="disabled"<?php }?>> 
                <option>请选择</option>
                <?php foreach ($output['seller'] as $key => $val) {?>
                    <option value="<?php echo $key;?>" <?php if (isset($output['user_id']) && $key==$output['user_id']) {?>selected="selected"<?php }?>><?php echo $val;?></option>
                <?php }?>
            </select>
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>普通<0.5克拉<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w100" name="zhekou_1" value="<?php if($output['plate_info'][1]){?><?php echo $output['plate_info'][1]?><?php }else{ ?>1<?php }?>" id="jiajialv" />
            <p class="hint">折扣范围<?php echo paramsHelper::echoOptionText("voucher_goods_jiajialv",1); ?>~1（0.8为8折）</p>
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>普通0.5（含）~1.0克拉<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w100" name="zhekou_2" value="<?php if($output['plate_info'][2]){?><?php echo $output['plate_info'][2]?><?php }else{ ?>1<?php }?>" id="jiajialv" />
            <p class="hint">折扣范围<?php echo paramsHelper::echoOptionText("voucher_goods_jiajialv",2); ?>~1（0.8为8折）</p>
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>普通1.0（含）~1.5克拉<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w100" name="zhekou_3" value="<?php if($output['plate_info'][3]){?><?php echo $output['plate_info'][3]?><?php }else{ ?>1<?php }?>" id="jiajialv" />
            <p class="hint">折扣范围<?php echo paramsHelper::echoOptionText("voucher_goods_jiajialv",3); ?>~1（0.8为8折）</p>
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>普通1.5（含）克拉以上<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w100" name="zhekou_4" value="<?php if($output['plate_info'][4]){?><?php echo $output['plate_info'][4]?><?php }else{ ?>1<?php }?>" id="jiajialv" />
            <p class="hint">折扣范围<?php echo paramsHelper::echoOptionText("voucher_goods_jiajialv",4); ?>~1（0.8为8折）</p>
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>星耀<0.5克拉<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w100" name="zhekou_5" value="<?php if($output['plate_info'][5]){?><?php echo $output['plate_info'][5]?><?php }else{ ?>1<?php }?>" id="jiajialv" />
            <p class="hint">折扣范围<?php echo paramsHelper::echoOptionText("voucher_goods_jiajialv",5); ?>~1（0.8为8折）</p>
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>星耀0.5（含）~1.0克拉<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w100" name="zhekou_6" value="<?php if($output['plate_info'][6]){?><?php echo $output['plate_info'][6]?><?php }else{ ?>1<?php }?>" id="jiajialv" />
            <p class="hint">折扣范围<?php echo paramsHelper::echoOptionText("voucher_goods_jiajialv",6); ?>~1（0.8为8折）</p>
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>星耀1.0（含）~1.5克拉<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w100" name="zhekou_7" value="<?php if($output['plate_info'][7]){?><?php echo $output['plate_info'][7]?><?php }else{ ?>1<?php }?>" id="jiajialv" />
            <p class="hint">折扣范围<?php echo paramsHelper::echoOptionText("voucher_goods_jiajialv",7); ?>~1（0.8为8折）</p>
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>星耀1.5（含）克拉以上<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w100" name="zhekou_8" value="<?php if($output['plate_info'][8]){?><?php echo $output['plate_info'][8]?><?php }else{ ?>1<?php }?>" id="jiajialv" />
            <p class="hint">折扣范围<?php echo paramsHelper::echoOptionText("voucher_goods_jiajialv",8); ?>~1（0.8为8折）</p>
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>天生一对裸石<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w100" name="zhekou_9" value="<?php if($output['plate_info'][9]){?><?php echo $output['plate_info'][9]?><?php }else{ ?>1<?php }?>" id="jiajialv" />
            <p class="hint">折扣范围<?php echo paramsHelper::echoOptionText("voucher_goods_jiajialv",9); ?>~1（0.8为8折）</p>
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>天生一对成品<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w100" name="zhekou_10" value="<?php if($output['plate_info'][10]){?><?php echo $output['plate_info'][10]?><?php }else{ ?>1<?php }?>" id="jiajialv" />
            <p class="hint">折扣范围<?php echo paramsHelper::echoOptionText("voucher_goods_jiajialv",10); ?>~1（0.8为8折）</p>
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>成品<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w100" name="zhekou_11" value="<?php if($output['plate_info'][11]){?><?php echo $output['plate_info'][11]?><?php }else{ ?>1<?php }?>" id="jiajialv" />
            <p class="hint">折扣范围<?php echo paramsHelper::echoOptionText("voucher_goods_jiajialv",11); ?>~1（0.8为8折）</p>
          </dd>
        </dl>	
		<dl>
          <dt><i class="required">*</i>香邂巴黎<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w100" name="zhekou_12" value="<?php if($output['plate_info'][12]){?><?php echo $output['plate_info'][12]?><?php }else{ ?>1<?php }?>" id="jiajialv" />
            <p class="hint">折扣范围<?php echo paramsHelper::echoOptionText("voucher_goods_jiajialv",12); ?>~1（0.8为8折）</p>
          </dd>
        </dl>	
        <dl>
          <dt><i class="required">*</i>皇室公主<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w100" name="zhekou_13" value="<?php if($output['plate_info'][13]){?><?php echo $output['plate_info'][13]?><?php }else{ ?>1<?php }?>" id="jiajialv" />
            <p class="hint">折扣范围<?php echo paramsHelper::echoOptionText("voucher_goods_jiajialv",13); ?>~1（0.8为8折）</p>
          </dd>
        </dl>		
    </dl>
    <div class="bottom">
      <label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_submit'];?>"/></label>
    </div>
  </form>
</div>
<script>
$(function(){
    /*$('#plate_form').validate({
        submitHandler:function(form){
            ajaxpost('plate_form', '', '', 'onerror');
        },
        rules : {
            p_name : {
                required : true,
                maxlength: 10
            },
            p_content : {
                required : true
            }
        },
        messages : {
            p_name : {
                required : '<i class="icon-exclamation-sign"></i>请填写版式名称',
                maxlength: '<i class="icon-exclamation-sign"></i>版式名称不能超过10个字符'
            },
            p_content : {
                required : '<i class="icon-exclamation-sign"></i>请填写版式内容'
            }
        }
    });

    // 版式内容使用
    $('a[nctype="show_desc"]').ajaxContent({
        event:'click', //mouseover
        loaderType:"img",
        loadingMsg:SHOP_TEMPLATES_URL+"/images/loading.gif",
        target:'#des_demo'
    }).click(function(){
        $(this).hide();
        $('a[nctype="del_desc"]').show();
    });
    $('a[nctype="del_desc"]').click(function(){
        $(this).hide();
        $('a[nctype="show_desc"]').show();
        $('#des_demo').html('');
    });*/
});
/* 插入编辑器 */
/*function insert_editor(file_path) {
    KE.appendHtml('p_content', '<img src="'+ file_path + '">');
}*/
</script> 
