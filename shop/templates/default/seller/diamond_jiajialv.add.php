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
  <form method="post" action="<?php if (empty($output['plate_info'])) { echo urlShop('diamond_jiajialv', 'plate_add');} else { echo urlShop('diamond_jiajialv', 'plate_edit');}?>" id="plate_form">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="p_id" value="<?php echo $output['plate_info']['id'];?>"/>
        <dl>
          <dt><i class="required">*</i>体验店<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <select name="store_id" <?php if (isset($output['plate_info']) && !empty($output['plate_info'])) {?>disabled="disabled"<?php }?>>
                <!--<option>请选择</option>-->
                <?php foreach ($output['storelist'] as $key => $val) {?>
                    <option value="<?php echo $key;?>" <?php if (isset($output['plate_info']['channel_id']) && $key==$output['plate_info']['channel_id']) {?>selected="selected"<?php }?>><?php echo $val;?></option>
                <?php }?>
            </select>
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>钻重范围(CT)<?php echo $lang['nc_colon'];?></dt>
          <dd>
		    <select name="carat" >
			    <option value="">请选择</option>
				 <?php foreach ($output['carat_arr'] as $key => $val) {?>
				<option value="<?php echo $key;?>" <?php if (isset($output['plate_info']['carat']) && $key==$output['plate_info']['carat']) {?>selected="selected"<?php }?>><?php echo $val;?></option>
				<?php }?>
			</select>
            <!--<input type="text" class="text w50" name="carat_min" value="<?php //echo $output['plate_info']['carat_min']?>" /> ~ <input type="text" class="text w50" name="carat_max" value="<?php //echo $output['plate_info']['carat_max']?>"/>-->
            <!--<p class="hint">请输入10个字符内的名称，方便商品发布 / 编辑时选择使用。</p>-->
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>证书类型<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <select name="cert">
                <option value="">请选择</option>
                <?php foreach ($output['cert'] as $key => $val) {?>
                    <option value="<?php echo $val;?>" <?php if (isset($output['plate_info']['cert']) && $val==$output['plate_info']['cert']) {?>selected="selected"<?php }?>><?php echo $val;?></option>
                <?php }?>
            </select>
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>货品类型<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <select name="good_type">
                <option value="">请选择</option>
                <?php foreach ($output['good_type'] as $key => $val) {?>
                    <option value="<?php echo $key;?>" <?php if (isset($output['plate_info']['good_type']) && $key==$output['plate_info']['good_type']) {?>selected="selected"<?php }?>><?php echo $val;?></option>
                <?php }?>
            </select>
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>加价率<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w100" name="jiajialv" value="<?php echo $output['plate_info']['jiajialv']?>" id="jiajialv" />
            <!--<p class="hint">请输入10个字符内的名称，方便商品发布 / 编辑时选择使用。</p>-->
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>状态<?php echo $lang['nc_colon'];?></dt>
          <dd id="gcategory">
            <ul class="ncsc-form-radio-list">
              <li><label><input type="radio" name="status" id="status" value="1" class="radio" <?php if (empty($output['plate_info']) || $output['plate_info']['status'] == 1) {?>checked="checked"<?php }?> />启用</label></li>
              <li><label><input type="radio" name="status" id="status" value="0" class="radio" <?php if (!empty($output['plate_info']) && $output['plate_info']['status'] == 0) {?>checked="checked"<?php }?>/>停用</label></li>
            </ul>
            <!--<p class="hint">选择关联版式插入到页面中的位置，选择“顶部”为商品详情上方内容，“底部”为商品详情下方内容。</p>-->
          </dd>
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
