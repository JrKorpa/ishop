<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>

<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.ajaxContent.pack.js"></script>
<style type="text/css">
.ncsc-form-default dl dt { width: 16%;}
.ncsc-form-default dl dd { width: 82%;}
</style>

<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<div class="ncsc-form-default">
  <form method="post" action="<?php if (empty($output['house_info'])) { echo urlShop('store_warehouse', 'add');} else { echo urlShop('store_warehouse', 'edit');}?>" id="house_form">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="house_id" value="<?php echo $output['house_info']['house_id'];?>"/>
    
        <dl>
          <dt><i class="required">*</i>仓库名称<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w200" name="w_name" value="<?php echo $output['house_info']['name']?>" id="w_name" />
            <p class="hint">请输入20个字符内的名称，方便发布 / 编辑时选择使用。</p>
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>仓库编号<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w200" name="code" value="<?php echo $output['house_info']['code']?>" id="code" />
          </dd>
        </dl>
		<!--<input type="hidden" name="chain_id" value="1"/>
        <dl style="display:none">
          <dt><i class="required">*</i>门店<?php echo $lang['nc_colon'];?></dt>
          <dd>
          <span class="mr50">
            <select name="chain_id">
              <option value="">请选择</option>
              <?php if (!empty($output['chain_list'])) {?>
              <?php foreach ($output['chain_list'] as $val) {?>
              <option value="<?php echo $val['chain_id']?>" <?php if ($output['house_info']['chain_id'] == $val['chain_id']) {?>selected="selected"<?php }?>><?php echo $val['chain_name'];?></option>
              <?php }?>
              <?php }?>
            </select>
            </span> 
             </dd>
        </dl>-->
        <dl>
          <dt><i class="required">*</i>仓库类型<?php echo $lang['nc_colon'];?></dt>
          <dd>
          <span class="mr50">
            <select name="type">
              <option value="">请选择</option>
              <?php if (!empty($output['type'])) {?>
              <?php foreach ($output['type'] as $key => $val) {?>
              <option value="<?php echo $key?>" <?php if ($output['house_info']['type'] == $key) {?>selected="selected"<?php }?>><?php echo $val;?></option>
              <?php }?>
              <?php }?>
            </select>
            </span> 
             </dd>
        </dl>
        <!--<dl>
          <dt><i class="required">*</i>优先级<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w200" name="level" value="<?php if(empty($output['house_info'])){echo '1';}else{echo $output['house_info']['level'];}?>" id="level" />
            <p class="hint">请填写数字,数字越小优先级越高</p>
          </dd>
        </dl>-->
		<dl>
          <dt><i class="required">*</i>上架售卖<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <ul class="ncsc-form-radio-list">
              <li><label><input type="radio" name="is_default" value="1" class="radio" <?php if (empty($output['house_info']) || $output['house_info']['is_default'] == 1) {?>checked="checked"<?php }?> />是</label></li>
              <li><label><input type="radio" name="is_default" id="is_enabled" value="0" class="radio" <?php if (!empty($output['house_info']) && $output['house_info']['is_default'] == 0) {?>checked="checked"<?php }?>/>否</label></li>
            </ul>
            
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>状态<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <ul class="ncsc-form-radio-list">
              <li><label><input type="radio" name="is_enabled" id="is_enabled" value="1" class="radio" <?php if (empty($output['house_info']) || $output['house_info']['is_enabled'] == 1) {?>checked="checked"<?php }?> />有效</label></li>
              <li><label><input type="radio" name="is_enabled" id="is_enabled" value="0" class="radio" <?php if (!empty($output['house_info']) && $output['house_info']['is_enabled'] == 0) {?>checked="checked"<?php }?>/>无效</label></li>
            </ul>
            
          </dd>
        </dl>
        <dl>
          <dt>备注<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <textarea class="textarea h60 w400" name='remark'><?php echo $output['house_info']['remark'];?></textarea>
          </dd>
        </dl>
    <div class="bottom">
      <label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_submit'];?>"/></label>
    </div>
  </form>
</div>
<script>
$(function(){
    $('#house_form').validate({
        submitHandler:function(form){
            ajaxpost('house_form', '', '', 'onerror');
        },
        rules : {
            w_name : {
                required : true,
                maxlength: 20
            },
            //chain_id : {
            //    required : true
            //},
            level : {
                required : true,
                range:[1,99]
            }
        },
        messages : {
            w_name : {
                required : '<i class="icon-exclamation-sign"></i>请填写仓库名称',
                maxlength: '<i class="icon-exclamation-sign"></i>仓库名称不能超过20个字符'
            },
            //chain_id : {
                //required : '<i class="icon-exclamation-sign"></i>请选择门店',
            //},
            level : {
                required : '<i class="icon-exclamation-sign"></i>优先级必填',
                range : '<i class="icon-exclamation-sign"></i>输入值必须在1和99之间',
            }
        }
    });

   
});

</script> 
