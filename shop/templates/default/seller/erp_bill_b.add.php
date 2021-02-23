<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>

<!--下拉美化js、css-->
<link href="<?php echo RESOURCE_SITE_URL;?>/js/chosen/css/chosen.css" rel="stylesheet" type="text/css">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/chosen/js/chosen.jquery.js"></script>

<!-- S setp -->
<ul class="add-goods-step">
  <li class="<?php $output['step']=="1" ? print "current" : print "";?>"><i class="icon icon-list-alt"></i>
    <h6>STIP.1</h6>
    <h2>创建退货返厂单</h2>
    <i class="arrow icon-angle-right"></i> 
  </li>
 <li class="<?php $output['step']=="3" ? print "current" : print "";?>"><i class="icon icon-ok-circle "></i>
    <h6>STIP.2</h6>
    <h2>保存完成</h2></li>
</ul>
<div class="alert mt15 mb5"><strong>珂兰技术中心操作提示：</strong>
  <ul>
    <li>1.退货返厂单多个货号用英文逗号隔开 或 回车换行。</li>
    <li>2.自采商品或向上级公司采购的商品需要退货，做退货返厂单，供应商收到货后审核单据，库存减少。</li>
  </ul>
</div>
<!--S 分类选择区域-->
<form method="post" action="index.php?act=erp_bill_b&op=insert" id="erp_bill_b_form">
  <div class="ncsc-form-goods"  <?php if($output['step'] != '1'){?> style="display:none"<?php }?>>
     <dl>
      <dt><i class="required">*</i>入库方式</dt>
      <dd>
		<select name="put_out_type">
          <option value=""><?php echo $lang['nc_please_choose'];?></option>
		  <?php if (is_array($output['put_type_list'])) {?>
        <?php foreach ($output['put_type_list'] as $key=>$val) {?>
        <option value="<?php echo $key;?>" <?= $key==5?"selected":''?>><?php echo $val;?></option>
        <?php }?>
        <?php }?>
        </select>
      </dd>
    </dl>	
    <dl>
      <dt><i class="required">*</i>供应商</dt>
      <dd>
        <select name="supplier_id" class="chose2">
    	<?php if (is_array($output['company_list'])) {?>
        <?php foreach ($output['company_list'] as $key=>$val) {?>
		<?php if($key!=57){ continue;}?>
        <option value="<?php echo $key;?>"><?php echo $val;?></option>
        <?php }?>
        <?php }?>
        </select>		
      </dd>
    </dl>
	 <dl>
          <dt>物流公司</dt>
          <dd>
            <select name="express_id" class="chose" style="width:150px;">
              <option value=""><?php echo $lang['nc_please_choose'];?></option>
              <?php if (is_array($output['express_list'])) {?>
            <?php foreach ($output['express_list'] as $key=>$val) {?>
            <option value="<?php echo $val['id'];?>"><?php echo $val['e_name'];?></option>
            <?php }?>
            <?php }?>
            </select>
             <input type="text" class="text" placeholder="请输入物流单号" name="express_sn" style="margin-top: 2px;">
          </dd>
        </dl>	
	<dl>
      <dt><i class="required">*</i>货号</dt>
      <dd><textarea class="w600" name="goods_itemid" style="height:120px" placeholder="扫码批量输入，用逗号(,)或回车隔开"></textarea></dd>
    </dl>
	<dl>
      <dt><i class="required">*</i>备注</dt>
      <dd> 
		  <textarea class="w600" name="remark" rows="2"></textarea>
      </dd>
    </dl>
    <dl>
      <dt>&nbsp;</dt>
      <dd>
        <input type="button" id="btn_submit" class="submit" value="保存" />
      </dd>
    </dl>
    </ul>
  </div>
</form>
<div class="table-responsive" style="font-size: 12px;">
        <div style="height:35px;margin-top: 5px;font-size: 14px;font-weight: bolder;"> 总数量：<span id="total_count" style="margin-right: 20px;">0</span>总成本：<span id="total_price">0</span></div>
        <table class="ncsc-default-table" >
            <thead>
                <tr>
                    <th>货号</th>
                    <th>款号</th>
                    <th>名称</th>
                    <th>数量</th>
					<th>采购成本</th>
					<th>退货价</th>
                    <th>材质</th>
                    <th>材质颜色</th>
                    <th>指圈</th>					
                </tr>
            </thead>
            <tbody id="goods_list_body">
            </tbody>
        </table>
 </div>
<!--step4--> 
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.form.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.ui-jqLoding.js"></script>
<script>
//下拉美化（可搜索）
$(function(){
	$('.chose').chosen();
});

var curr_company_id = "<?php echo $output['company_id']?>";
var formID = "erp_bill_b_form";
var optionsSubmit = {
	url: "index.php?act=erp_bill_b&op=insert",
	dataType:'json',	
	error:function ()
	{   //$(this).jqLoading("destroy");
	    $('#'+formID+' .submit').attr('disabled',false);//解锁
		alert("请求失败");
	},
	beforeSubmit:function(frm,jq,op){
	    //$(this).jqLoading({text:"正在处理中..."});
		$('#'+formID+' .submit').attr('disabled',true);//禁用
	},
	success: function(res) {
	    //$(this).jqLoading("destroy");
	    $('#'+formID+' .submit').attr('disabled',false);//解锁
		if(res.state){
	      	showDialog('单据创建成功','succ', '提示信息', function(){
		        window.location.href='index.php?act=erp_bill&op=show&bill_id='+res.data.bill_id;
		    });
		}else{
		   showDialog(res.msg);
		}
	}
};
$("#"+formID+" select[name='put_out_type']").change(function(){
    if($(this).val()!=5){
	    location.href='index.php?act=erp_bill_b&op=add&put_out_type='+$(this).val();
	}
});
$("#"+formID+" #btn_submit").click(function(){
   $("#"+formID).ajaxSubmit(optionsSubmit);
});

$("#"+formID+" textarea[name='goods_itemid']").change(function(){
    $.ajax({
        type:'POST',
        url:'index.php?act=erp_bill_b&op=get_goods_list',
        cache:false,
        data:"goods_itemid="+$(this).val(),
        dataType:'json',
        error:function(){

        },
        success:function(result){
           if(result.state&&result.data.goods_list.length>0){
               var html="";
               var data=result.data.goods_list;
               for(var i=0;i<data.length;i++){
                   var goods_info = data[i];
                   html +='<tr><td>'+goods_info['goods_id']+'</td><td>'+goods_info['goods_sn']+'</td><td>'+goods_info['goods_name']+'</td><td>'+goods_info['num']+'</td><td>'+goods_info['mingyichengben']+'</td><td>'+goods_info['mingyichengben']+'</td><td>'+goods_info['caizhi']+'</td><td>'+goods_info['jinse']+'</td><td>'+goods_info['shoucun']+'</td></tr>';
               }
               $("#goods_list_body").html(html);
               $("#total_count").html(result.data.total_count);
               $("#total_price").html(result.data.total_chengben);
           }else{
               $("#goods_list_body").html('');
           }
        }
    });
});
</script>