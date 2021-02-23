<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>

<link href="<?php echo RESOURCE_SITE_URL;?>/js/chosen/css/chosen.css" rel="stylesheet" type="text/css">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/chosen/js/chosen.jquery.js"></script>
<!-- S setp -->
<ul class="add-goods-step">
  <li class="<?php $output['step']=="1" ? print "current" : print "";?>"><i class="icon icon-list-alt"></i>
    <h6>STIP.1</h6>
    <h2>录入货号</h2>
    <i class="arrow icon-angle-right"></i> 
  </li>
  <li class="<?php $output['step']=="2" ? print "current" : print "";?>"><i class="icon icon-edit"></i>
    <h6>STIP.2</h6>
    <h2>录入价格</h2>
    <i class="arrow icon-angle-right"></i> 
  </li>
 <li class="<?php $output['step']=="3" ? print "current" : print "";?>"><i class="icon icon-ok-circle "></i>
    <h6>STIP.3</h6>
    <h2>到退货列表审核</h2></li>
</ul>
<div class="alert mt15 mb5"><strong>珂兰技术中心操作提示：</strong>
  <ul>
    <li>1.录入货号必须在商品列表中存在。<br>
</li>
  </ul>
</div>
<!--S 分类选择区域-->
<form method="post" action="index.php?act=erp_bill_ha&op=index" id="ha_form">
 <input type="hidden" name="form_submit" value="ok">
  <div class="ncsc-form-goods">
      <dl>
          <dt><i class="required">*</i>货号：</dt>
          <dd>
              <input class="text w200" name="goods_itemid" rows="2" placeholder="输入货号"/>
          </dd>
      </dl>
      <dl>
          <dt><i class="required">*</i>退货价：</dt>
          <dd>
              <input class="text w200" name="return_amount" rows="2" placeholder="输入退货价"/>
          </dd>
      </dl>
      <dl>
          <dt><i class="required">*</i>销售价：</dt>
          <dd>
              <input class="text w200" name="sales_price" rows="2" placeholder="输入销售价"/>
          </dd>
      </dl>
        <!--<dl>
            <dt><i class="required">*</i>入库仓库：</dt>
            <dd>
                <select name="to_house_id" class="chose">
                    <option value=""><?php echo $lang['nc_please_choose'];?></option>
                    <?php if (is_array($output['warehouse_list'])) {?>
                        <?php foreach ($output['warehouse_list'] as $key=>$val) {?>
                        <option value="<?php echo $val['store_id'];?>|<?php echo $val['house_id'];?>"><?php echo $val['name'];?></option>
                        <?php }?>
                    <?php }?>
                </select>
            </dd>
        </dl>-->
        <dl>
             <dt><i class="required">*</i>备注：</dt>
             <dd>
                 <textarea class="w600" name="buyer_message" style="height:120px" placeholder="请输入备注信息"></textarea>
             </dd>
         </dl>
    <dl>
      <dt>&nbsp;</dt>
      <dd>
        <input type="button" id="btn_reset"  class="submit" value="重置"  style="display:inherit;background-color: #f1f1f7; "/>
        <input type="button" id="btn_submit" class="submit" value="保存" style="display:inherit;"/>
      </dd>
    </dl>
    </ul>
  </div>
</form>
<!--step4--> 
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.form.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.ui-jqLoding.js"></script>
<script>
//下拉美化（可搜索）    
$(function(){
    $('.chose').chosen();
});

var formID = "ha_form";
var optionsSubmit = {
	url: "index.php?act=erp_bill_ha&op=index",
	dataType:'json',	
	error:function ()
	{   $(this).jqLoading("destroy");
	    $('#'+formID+' .submit').attr('disabled',false);//解锁
		alert("请求失败");
	},
	beforeSubmit:function(frm,jq,op){
	    $(this).jqLoading({text:"正在处理中..."});
		$('#'+formID+' .submit').attr('disabled',true);//禁用
	},
	success: function(res) {
	    $(this).jqLoading("destroy");
	    $('#'+formID+' .submit').attr('disabled',false);//解锁
		if(res.success == 1 ){
	      	showDialog('操作成功！','succ', '提示信息', function(){
                $("#"+formID).resetForm();
                window.location.href="index.php?act=store_return&op=edit&return_id="+res.data;
            });
		}else{
		   showError(res.msg);
		}
	}
};
$("#"+formID+" #btn_submit").click(function(){
   $("#"+formID).ajaxSubmit(optionsSubmit);
});
$("#"+formID+" #btn_reset").click(function(){
    $("#"+formID).resetForm();
});
</script>