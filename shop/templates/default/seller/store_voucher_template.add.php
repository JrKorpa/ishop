<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_SITE_URL."/js/jquery-ui/themes/ui-lightness/jquery.ui.css";?>"/>

  <div class="tabmenu">
    <?php include template('layout/submenu');?>
  </div>
	<div class="ncsc-form-default">
	  <form id="add_form" method="post" enctype="multipart/form-data" action="index.php?act=store_voucher&op=<?php echo $output['type']=='add'?'templateadd':'templateedit'; ?>">
	  	<input type="hidden" id="act" name="act" value="store_voucher"/>
	  	<?php if ($output['type'] == 'add'){?>
	  	<input type="hidden" id="op" name="op" value="templateadd"/>
	  	<?php }else {?>
	  	<input type="hidden" id="op" name="op" value="templateedit"/>
	  	<input type="hidden" id="tid" name="tid" value="<?php echo $output['t_info']['voucher_t_id'];?>"/>
	  	<?php }?>
	  	<input type="hidden" id="form_submit" name="form_submit" value="ok"/>
          <dl>
              <dt><i class="required">*</i>折扣码类型：</dt>
              <dd>
                  <select name="voucher_t_type" id="voucher_t_type">
                      <option value="1">折扣码</option>
                      <!--<option value="2">成品定制码</option>-->
                  </select>
                  <p class="hint">折扣码：设定商品的优惠折扣，成品定制码：指定商品的销售价</p>
              </dd>
          </dl>
		  
		  <dl id="voucher_t_goods_type">
              <dt><i class="required">*</i>商品类型：</dt>
              <dd>
                  <select name="voucher_t_goods_type" >
				      <option value="">-请选择</option>
                      <?php echo paramsHelper::echoOption("voucher_goods_type") ?>
                  </select>
                  <p class="hint"></p>
              </dd>
          </dl>
		  
          <dl>
              <dt id="txt_value">折扣：</dt>
              <dd>
                  <input type="text" class="w70 text" name="select_template_price" id="select_template_price" value="<?php echo $output['t_info']['voucher_t_price']; ?>">
                  <em class="add-on" id="txt_symbol">%</em>
                  <p id="txt_desc" class="hint">设定商品的优惠折扣，八折请输入80</p>
              </dd>
          </dl>
	    <dl>
	      <dt><em class="pngFix"></em><?php echo $lang['voucher_template_enddate'].$lang['nc_colon']; ?></dt>
	      <dd>
	      	<input type="text" class="text w70" id="txt_template_enddate" name="txt_template_enddate" value="" readonly><em class="add-on"><i class="icon-calendar"></i></em>
	        <span></span><p class="hint">留空则默认30天之后到期</p>
	      </dd>
	    </dl>
	    <dl>
	      <dt><i class="required">*</i><?php echo $lang['voucher_template_total'].$lang['nc_colon']; ?></dt>
	      <dd>
	        <input type="text" class="w70 text" name="txt_template_total" id="txt_template_total" value="1">
	        <span></span>
	        <p class="hint">发放总数应为1~50之间的整数</p>
	      </dd>
	    </dl>
	    <div class="bottom">
	      <label class="submit-border">
	      <a id='btn_add' class="submit" href="javascript:void(0);"><?php echo $lang['nc_submit'];?></a>
	      </label>
	      </div>
	  </form>
	</div>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js"></script>
<script>
//判断是否显示预览模块
<?php if (!empty($output['t_info']['voucher_t_customimg'])){?>
$('#customimg_preview').show();
<?php }?>
var year = <?php echo date('Y',$output['quotainfo']['quota_endtime']);?>;
var month = <?php echo intval(date('m',$output['quotainfo']['quota_endtime']));?>;
var day = <?php echo intval(date('d',$output['quotainfo']['quota_endtime']));?>;
function showcontent(choose_gettype){
	if(choose_gettype == 'pwd'){
		$("#eachlimit_dl").hide();
		$("#mgrade_dl").hide();
	}else{
		$("#eachlimit_dl").show();
		$("#mgrade_dl").show();
	}
}

function show_voucher_attr(voucher_type){
    if(voucher_type == '1'){
        $("#txt_value").html("折扣：");
        $("#txt_symbol").html("%");
        $("#txt_desc").html("设定商品的优惠折扣，八折请输入80");
		$("#voucher_t_goods_type").show();
    }else{
        $("#txt_value").html("面额：");
        $("#txt_symbol").html("<i class='icon-renminbi'></i>");
        $("#txt_desc").html("用于设定商品的销售价");
		$("#voucher_t_goods_type").hide();
		
    }
}

$(document).ready(function(){
	showcontent('<?php echo $output['t_info']['voucher_t_gettype_key']; ?>');
    $("#voucher_t_type").change(function(){
        var voucher_t_type = $("#voucher_t_type").val();
        show_voucher_attr(voucher_t_type);
    });

	$("#gettype_sel").change(function(){
		var choose_gettype = $("#gettype_sel").val();
		showcontent(choose_gettype);
	});
    //日期控件
    $('#txt_template_enddate').datepicker();
    
    var currDate = new Date();
    var date = currDate.getDate();
    date = date + 1;
    currDate.setDate(date);
    
    $('#txt_template_enddate').datepicker( "option", "minDate", currDate);
<?php if (!$output['isOwnShop']) { ?>
    $('#txt_template_enddate').datepicker( "option", "maxDate", new Date(year,month-1,day));
<?php } ?>


    $('#txt_template_enddate').val("<?php echo $output['t_info']['voucher_t_end_date']?@date('Y-m-d',$output['t_info']['voucher_t_end_date']):'';?>");
    $('#customimg').change(function(){
		var src = getFullPath($(this)[0]);
		if(navigator.userAgent.indexOf("Firefox")>0){
			$('#customimg_preview').show();
			$('#customimg_preview').children('p').html('<img src="'+src+'">');
		}
	});

    $("#btn_add").click(function(){
        if($("#add_form").valid()){
        	var choose_gettype = $("#gettype_sel").val();
        	if(choose_gettype == 'pwd'){
            	var template_total = parseInt($("#txt_template_total").val());
            	if(template_total > 1000){
            		$("#txt_template_total").addClass('error');
            		$("#txt_template_total").parent('dd').children('span').append('<label for="txt_template_total" class="error"><i class="icon-exclamation-sign"></i>领取方式为卡密兑换的代金券，发放总数不能超过1000张</label>');
            		return false;
                }
            }
        	ajaxpost('add_form', '', '', 'onerror');
    	}
	});
	
    //表单验证
    $('#add_form').validate({
        errorPlacement: function(error, element){
	    	var error_td = element.parent('dd').children('span');
			error_td.append(error);
	    },
        rules : {
            txt_template_total: {
                required : true,
                digits : true,
                min: 1
            },
        },
        messages : {
            txt_template_total: {
                required : '<i class="icon-exclamation-sign"></i><?php echo $lang['voucher_template_total_error'];?>',
                digits : '<i class="icon-exclamation-sign"></i><?php echo $lang['voucher_template_total_error'];?>',
                min: '<i class="icon-exclamation-sign"></i><?php echo $lang['voucher_template_total_error'];?>'
            }
        }
    });
});
</script>