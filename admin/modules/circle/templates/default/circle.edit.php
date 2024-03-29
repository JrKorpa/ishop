<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="index.php?act=circle_manage&op=circle_list" title="返回圈子列表"><i class="fa fa-arrow-circle-o-left"></i></a>
      <div class="subject">
        <h3><?php echo $lang['nc_circle_manage'];?> - <?php echo $output['circle_info']['circle_status'] == 2 ? '审核' : $lang['nc_edit'];?>圈子“<?php echo $output['circle_info']['circle_name'];?>”</h3>
        <h5><?php echo $lang['nc_circle_manage_subhead'];?></h5>
      </div>
    </div>
  </div>
  <form id="circle_form" method="post">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="c_id" value="<?php echo $output['circle_info']['circle_id'];?>" />
    <input type="hidden" name="c_oldimg" value="<?php echo $output['circle_info']['circle_img'];?>" />
    <div class="ncap-form-default">
      <dl class="row">
        <dt class="tit">
          <label for="c_name"><em>*</em><?php echo $lang['circle_name'];?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="c_name" id="c_name" class="input-txt" value="<?php echo $output['circle_info']['circle_name'];?>" />
          <span class="err"></span>
          <p class="notic"><?php echo $lang['circle_name_tips'];?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="mastername"><em>*</em><?php echo $lang['circle_member_identity_master'];?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="mastername" id="mastername" class="input-txt" readonly value="<?php echo $output['circle_info']['circle_mastername'];?>" />
          <input type="hidden" name="masterid" id="masterid" value="<?php echo $output['circle_info']['circle_masterid'];?>" />
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label class="" for="classid"><?php echo $lang['circle_class'];?></label>
        </dt>
        <dd class="opt">
          <select name="classid">
            <option value="0"><?php echo $lang['nc_please_choose'];?></option>
            <?php if(!empty($output['class_list'])){?>
            <?php foreach ($output['class_list'] as $val){?>
            <option value="<?php echo $val['class_id'];?>" <?php if($output['circle_info']['class_id'] == $val['class_id']){echo 'selected="selected"';}?>><?php echo $val['class_name'];?></option>
            <?php }?>
            <?php }?>
          </select>
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="c_desc"><?php echo $lang['circle_desc'];?></label>
        </dt>
        <dd class="opt">
          <textarea class="tarea" rows="6" name="c_desc" id="c_desc"><?php echo $output['circle_info']['circle_desc'];?></textarea>
          <span class="err"></span>
          <p class="notic"><?php echo $lang['circle_desc_tips'];?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="c_tag"><?php echo $lang['circle_tag'];?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="c_tag" class="input-txt" value="<?php echo $output['circle_info']['circle_tag'];?>" />
          <span class="err"></span>
          <p class="notic"><?php echo $lang['circle_tag_tips'];?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="c_notice"><?php echo $lang['circle_notice'];?></label>
        </dt>
        <dd class="opt">
          <textarea class="tarea" rows="6" name="c_notice" id="c_notice"><?php echo $output['circle_info']['circle_notice'];?></textarea>
          <span class="err"></span>
          <p class="notic"><?php echo $lang['circle_notice_tips'];?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo $lang['circle_image'];?></label>
        </dt>
        <dd class="opt">
          <div class="input-file-show"><span class="show"><a class="nyroModal" rel="gal" href="<?php echo circleLogo($output['circle_info']['circle_id']);?>?<?php echo microtime();?>"><i class="fa fa-picture-o" onMouseOver="toolTip('<img src=<?php echo circleLogo($output['circle_info']['circle_id']);?>?<?php echo microtime();?>>')" onMouseOut="toolTip()"></i></a></span><span class="type-file-box">
            <input class="type-file-file" id="_pic" name="_pic" type="file" size="30" hidefocus="true" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
            <input type="text" name="c_img" id="c_img" class="type-file-text" />
            <input type="button" name="button" id="button" value="选择上传..." class="type-file-button" />
            </span></div>
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="c_status"><?php echo $lang['circle_ststus']?></label>
        </dt>
        <dd class="opt">
          <label>
            <input type="radio" name="c_status" <?php if($output['circle_info']['circle_status'] == 0){ echo 'checked="checked"';}?> value="0" />
            <?php echo $lang['nc_close'];?></label>
          <label>
            <input type="radio" name="c_status" <?php if($output['circle_info']['circle_status'] == 1){ echo 'checked="checked"';}?> value="1" />
            <?php echo $lang['nc_open'];?></label>
          <label>
            <input type="radio" name="c_status" <?php if($output['circle_info']['circle_status'] == 2){ echo 'checked="checked"';}?> value="2" />
            <?php echo $lang['circle_verifying'];?></label>
          <label>
            <input type="radio" name="c_status" <?php if($output['circle_info']['circle_status'] == 3){ echo 'checked="checked"';}?> value="3" />
            <?php echo $lang['circle_verify_fail'];?></label>
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row" id="circle_statusinfo" <?php if($output['circle_info']['circle_status'] == 1 || $output['circle_info']['circle_status'] == 2){ echo 'style="display:none;"';}?>>
        <dt class="tit">
          <label for="c_statusinfo"><?php echo $lang['circle_verify_fail_reason'];?></label>
        </dt>
        <dd class="opt">
          <textarea class="tarea" rows="6" name="c_statusinfo" id="c_statusinfo"><?php echo $output['circle_info']['circle_statusinfo'];?></textarea>
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="c_recommend"><?php echo $lang['circle_is_recommend'];?></label>
        </dt>
        <dd class="opt">
          <div class="onoff">
            <label for="c_recommend1" class="cb-enable <?php if($output['circle_info']['is_recommend'] == 1) echo 'selected';?>" ><?php echo $lang['nc_yes'];?></label>
            <label for="c_recommend0" class="cb-disable <?php if($output['circle_info']['is_recommend'] == 0) echo 'selected';?>" ><?php echo $lang['nc_no'];?></label>
            <input id="c_recommend1" name="c_recommend" <?php if($output['circle_info']['is_recommend'] == 1) echo 'checked="checked"';?> value="1" type="radio">
            <input id="c_recommend0" name="c_recommend" <?php if($output['circle_info']['is_recommend'] == 0) echo 'checked="checked"';?> value="0" type="radio">
          </div>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="is_hot">是否热门</label>
        </dt>
        <dd class="opt">
          <div class="onoff">
            <label for="is_hot1" class="cb-enable <?php if($output['circle_info']['is_hot'] == 1) echo 'selected';?>" ><?php echo $lang['nc_yes'];?></label>
            <label for="is_hot0" class="cb-disable <?php if($output['circle_info']['is_hot'] == 0) echo 'selected';?>" ><?php echo $lang['nc_no'];?></label>
            <input id="is_hot1" name="is_hot" <?php if($output['circle_info']['is_hot'] == 1) echo 'checked="checked"';?> value="1" type="radio">
            <input id="is_hot0" name="is_hot" <?php if($output['circle_info']['is_hot'] == 0) echo 'checked="checked"';?> value="0" type="radio">
          </div>
          <p class="notic"></p>
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn"><?php echo $lang['nc_submit'];?></a></div>
    </div>
  </form>
</div>
<script type="text/javascript" src="<?php echo ADMIN_RESOURCE_URL;?>/js/jquery.edit.js" charset="utf-8"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/ajaxfileupload/ajaxfileupload.js"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.Jcrop/jquery.Jcrop.js"></script>
<link href="<?php echo RESOURCE_SITE_URL;?>/js/jquery.Jcrop/jquery.Jcrop.min.css" rel="stylesheet" type="text/css"/>
<script>
//裁剪图片后返回接收函数
function call_back(picname){
	$('#c_img').val(picname);
	$('#view_img').attr('src','<?php echo UPLOAD_SITE_URL.'/'.ATTACH_CIRCLE;?>/group/'+picname+'?'+Math.random());
}
// 选择圈主
function chooseReturn(data) {
    $('#mastername').val(data.name);$('#masterid').val(data.id);
}
//按钮先执行验证再提交表单
$(function(){
	$('input[class="type-file-file"]').change(uploadChange);
	function uploadChange(){
		var filepatd=$(this).val();
		var extStart=filepatd.lastIndexOf(".");
		var ext=filepatd.substring(extStart,filepatd.lengtd).toUpperCase();		
		if(ext!=".PNG"&&ext!=".GIF"&&ext!=".JPG"&&ext!=".JPEG"){
			alert("file type error");
			$(this).attr('value','');
			return false;
		}
		if ($(this).val() == '') return false;
		ajaxFileUpload();
	}
	function ajaxFileUpload()
	{
		$.ajaxFileUpload
		(
			{
				url:'<?php echo ADMIN_SITE_URL?>/index.php?act=common&op=pic_upload&form_submit=ok&uploadpath=<?php echo ATTACH_CIRCLE;?>/group',
				secureuri:false,
				fileElementId:'_pic',
				dataType: 'json',
				success: function (data, status)
				{
					if (data.status == 1){
						ajax_form('cutpic','<?php echo $lang['nc_cut'];?>','<?php echo ADMIN_SITE_URL?>/index.php?act=common&op=pic_cut&type=circle&x=120&y=120&resize=1&ratio=1&filename=<?php echo UPLOAD_SITE_URL.'/'.ATTACH_CIRCLE;?>/group/<?php echo $_GET['c_id'];?>.jpg&url='+data.url,690);
					}else{
						alert(data.msg);
					}
					$('input[class="type-file-file"]').bind('change',uploadChange);
				},
				error: function (data, status, e)
				{
					alert('上传失败');$('input[class="type-file-file"]').bind('change',uploadChange);
				}
			}
		)
	}	
	// 关闭或审核失败原因控制
	$('input[name="c_status"]').click(function(){
		statusinfo($(this).val());
	});
	// 选择圈主弹出框
	$('#mastername').focus(function(){
		ajax_form('choose_master', '<?php echo $lang['circle_choose_master'];?>', 'index.php?act=circle_manage&op=choose_master&c_id=<?php echo $output['circle_info']['circle_id'];?>', 640);
	});
	$("#submitBtn").click(function(){
		if($("#circle_form").valid()){
			$("#circle_form").submit();
		}
	});
	$('#circle_form').validate({
         errorPlacement: function(error, element){
			var error_td = element.parent('dd').children('span.err');
            error_td.append(error);
        },
        rules : {
        	c_name : {
        		required : true,
        		minlength : 4,
        		maxlength : 12,
            	remote : {
            		url : 'index.php?act=circle_manage&op=check_circle_name&id=<?php echo $output['circle_info']['circle_id'];?>',
                    type: 'get',
                    data:{
                    	name : function(){
                            return $('#c_name').val();
                        }
                    }
            	}
            },
            mastername : {
            	required : true,
                remote   : {
                    url :'index.php?act=circle_manage&op=check_member&c_id=<?php echo $output['circle_info']['circle_id'];?>&branch=ok',
                    type:'get',
                    data:{
                        name : function(){
                            return $('#mastername').val();
                        },
                        id : function(){
                            return $('#masterid').val();
                        }
                    }
                }
            },
            c_desc : {
            	maxlength : 240
            },
            c_tag : {
                maxlength : 50
            },
            c_notice : {
                maxlength : 240
            },
            c_sort : {
            	digits : true,
            	max : 255
            },
            c_statusinfo : {
				maxlength : 240
            }
        },
        messages : {
            c_name : {
                required : '<i class="fa fa-exclamation-circle"></i><?php echo $lang['circle_name_not_null'];?>',
        		minlength: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['circle_name_length_4_12'];?>',
                maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['circle_name_length_4_12'];?>',
            	remote   : '<i class="fa fa-exclamation-circle"></i><?php echo $lang['circle_change_name_please'];?>'
            },
            mastername : {
            	required : '<i class="fa fa-exclamation-circle"></i><?php echo $lang['circle_choose_master_please'];?>',
            	remote   : '<i class="fa fa-exclamation-circle"></i><?php echo $lang['circle_master_choose_error'];?>'
            },
            c_desc : {
            	maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['circle_desc_maxlength'];?>'
            },
            c_tag : {
                maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['circle_tag_maxlength'];?>'
            },
            c_notice : {
                maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['circle_notice_maxlength'];?>'
            },
            c_sort : {
            	digits : '<i class="fa fa-exclamation-circle"></i><?php echo $lang['circle_sort_digits'];?>',
            	max : '<i class="fa fa-exclamation-circle"></i><?php echo $lang['circle_sort_max'];?>'
            },
            c_statusinfo : {
				maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo $lang['circle_verify_fail_reason_maxlength'];?>'
            }
        }
    });
});
function statusinfo(val){
	if(val == '0' || val == '3'){
		$('#circle_statusinfo').show();
	}else if(val == '1' || val == '2'){
		$('#circle_statusinfo').hide();
	}
}
</script> 
