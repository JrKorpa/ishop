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
  <form method="post" action="<?php if (empty($output['store_task_info'])) { echo urlShop('store_task', 'store_task_add');} else { echo urlShop('store_task', 'store_task_edit');}?>" id="store_task_form">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="p_id" value="<?php echo $output['store_task_info']['id'];?>"/>
        <dl>
          <dt><i class="required">*</i>年份<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <select name="year">
			  <?php for ($i=date("Y",time());$i<=date("Y",time());$i++) {?>
				<option value="<?php echo $i;?>" <?php if (isset($output['store_task_info']['year']) && $i==$output['store_task_info']['year']) {?>selected="selected"<?php }?>><?php echo $i;?></option>
				<?php }?>
            </select>
          </dd>
        </dl>
        <dl>
          <dt><i class="required">*</i>月份<?php echo $lang['nc_colon'];?></dt>
          <dd>
		    <select name="month" >
			    <option value="">请选择</option>
				 <?php for ($i=1;$i<=12;$i++) {?>
				<option value="<?php echo $i;?>" <?php if (isset($output['store_task_info']['month']) && $i==$output['store_task_info']['month']) {?>selected="selected"<?php }?>><?php if($i <10){echo '0'.$i;}else{echo $i;}?></option>
				<?php }?>
			</select>
            <!--<input type="text" class="text w50" name="carat_min" value="<?php //echo $output['store_task_info']['carat_min']?>" /> ~ <input type="text" class="text w50" name="carat_max" value="<?php //echo $output['store_task_info']['carat_max']?>"/>-->
            <!--<p class="hint">请输入10个字符内的名称，方便商品发布 / 编辑时选择使用。</p>-->
          </dd>
        </dl>
       
       
        <dl>
          <dt><i class="required">*</i>任务<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <input type="text" class="text w100" name="task" value="<?php echo $output['store_task_info']['task']?>" id="task" />
            <!--<p class="hint">请输入10个字符内的名称，方便商品发布 / 编辑时选择使用。</p>-->
          </dd>
        </dl>
        
    <div class="bottom">
      <label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_submit'];?>"/></label>
    </div>
  </form>
</div>
<script>
$(function(){
    /*$('#store_task_form').validate({
        submitHandler:function(form){
            ajaxpost('store_task_form', '', '', 'onerror');
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
        loadingMsg:SHOP_TEMstore_taskS_URL+"/images/loading.gif",
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
