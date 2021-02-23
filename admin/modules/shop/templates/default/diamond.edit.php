<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="index.php?act=diamond&op=diamond" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
      <div class="subject">
        <h3>裸钻管理 - <?php echo $lang['nc_edit'];?></h3>
        <h5></h5>
        <h5></h5>
      </div>
    </div>
  </div>
  <form id="spec_form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="cert_id" value="<?php echo $output['diamond_detail']['cert_id']?>" />
    <div class="ncap-form-default">
      <dl class="row">
        <dt class="tit">
          <label>是否上架</label>
        </dt>
        <dd class="opt">
          <div class="onoff">
          <label for="enabled_subdomain1" class="cb-enable <?php if($output['diamond_detail']['status'] == '1'){ ?>selected<?php } ?>" title="上架">上架</label>
          <label for="enabled_subdomain0" class="cb-disable <?php if($output['diamond_detail']['status'] == '2'){ ?>selected<?php } ?>" title="下架">下架</label>
          <input type="radio" id="enabled_subdomain1" <?php if($output['diamond_detail']['status'] == '1'){ ?>checked="checked"<?php } ?> value="1" name="status">
          <input type="radio" id="enabled_subdomain0" <?php if($output['diamond_detail']['status'] == '2'){ ?>checked="checked"<?php } ?> value="2" name="status">
          <span class="err"></span>
          <p class="notic"><?php echo $lang['open_domain_document'];?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label>是否热销</label>
        </dt>
        <dd class="opt">
          <div class="onoff">
            <label for="subdomain_edit1" class="cb-enable <?php if($output['diamond_detail']['is_hot'] == '1'){ ?>selected<?php } ?>" title="<?php echo $lang['nc_yes'];?>"><?php echo $lang['nc_yes'];?></label>
            <label for="subdomain_edit0" class="cb-disable <?php if($output['diamond_detail']['is_hot'] == '0'){ ?>selected<?php } ?>" title="<?php echo $lang['nc_no'];?>"><?php echo $lang['nc_no'];?></label>
            <input type="radio" id="subdomain_edit1" <?php if($output['diamond_detail']['is_hot'] == '1'){ ?>checked="checked"<?php } ?> value="1" name="is_hot">
            <input type="radio" id="subdomain_edit0" <?php if($output['diamond_detail']['is_hot'] == '0'){ ?>checked="checked"<?php } ?> value="0" name="is_hot">
          </div>
          <p class="notic"><?php echo  $lang['domain_edit_tips'];?></p>
        </dd>
      </dl>
      <div class="bot"> <a id="submitBtn" class="ncap-btn-big ncap-btn-green" href="JavaScript:void(0);"><?php echo $lang['nc_submit'];?></a> </div>
    </div>
  </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common_select.js" charset="utf-8"></script> 
<script type="text/javascript">
$(function(){
    // 编辑分类时清除分类信息
    $('.edit_gcategory').click(function(){
        $('input[name="class_id"]').val('');
        $('input[name="class_name"]').val('');
    });
	//表单验证
    $('#spec_form').validate({
        errorPlacement: function(error, element){
			var error_td = element.parent('dd').children('span.err');
            error_td.append(error);
        },

        rules : {
        	s_name: {
        		required : true,
                maxlength: 10,
                minlength: 1
            },
            s_sort: {
				required : true,
				digits	 : true
            }
        },
        messages : {
        	s_name : {
            	required : '<i class="fa fa-exclamation-circle"></i><?php echo $lang['spec_add_name_no_null'];?>',
                maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['spec_add_name_max'];?>',
                minlength: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['spec_add_name_max'];?>'
            },
            s_sort: {
				required : '<i class="fa fa-exclamation-circle"></i><?php echo $lang['spec_add_sort_no_null'];?>',
				digits   : '<i class="fa fa-exclamation-circle"></i><?php echo $lang['spec_add_sort_no_digits'];?>'
            }
        }
    });

    //按钮先执行验证再提交表单
    $("#submitBtn").click(function(){
        $("#spec_form").submit();
    });
});
gcategoryInit('gcategory');
</script> 