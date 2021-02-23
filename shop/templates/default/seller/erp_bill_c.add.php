<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<!-- S setp -->
<ul class="add-goods-step">
  <li class="<?php $output['step']=="1" ? print "current" : print "";?>"><i class="icon icon-list-alt"></i>
    <h6>STIP.1</h6>
    <h2>创建其他出库单</h2>
    <i class="arrow icon-angle-right"></i> 
  </li>
  <li class="<?php $output['step']=="2" ? print "current" : print "";?>"><i class="icon icon-edit"></i>
    <h6>STIP.2</h6>
    <h2>扫描货号</h2>
    <i class="arrow icon-angle-right"></i> 
  </li>
 <li class="<?php $output['step']=="3" ? print "current" : print "";?>"><i class="icon icon-ok-circle "></i>
    <h6>STIP.3</h6>
    <h2>保存完成</h2></li>
</ul>
<div class="alert mt15 mb5"><strong>珂兰技术中心操作提示：</strong>
  <ul>
    <li>1.其他出库单多个货号用英文逗号隔开或回车换行。<br>
    <li>2.总记录行不能大于10000，超过限制请分多次扫描。<br>
</li>
  </ul>
</div>
<!--S 分类选择区域-->
<form method="post" action="index.php?act=erp_bill_c&op=insert" enctype="multipart/form-data" id="erp_bill_c_form">
  <div class="ncsc-form-goods">
     <dl>
         <dt><i class="required">*</i>货号</dt>
         <dd>
             <textarea class="w600" name="goods_itemid" id="goods_itemid" style="height:120px" placeholder="扫码批量输入，用逗号(,)或回车隔开"></textarea>
         </dd>
     </dl>
	<dl>
      <dt><i class="required">*</i>单据备注</dt>
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
                    <th>材质</th>
                    <th>材质颜色</th>
                    <th>指圈</th>
                    <th>主成色重</th>
                    <th>主石重</th>
                    <th>主石粒数</th>
                </tr>
            </thead>
            <tbody id="goods_list_body">
            </tbody>
        </table>
    </div>
</form>
<!--step4-->
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.form.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.ui-jqLoding.js"></script>
<script>
var formID = "erp_bill_c_form";
var optionsSubmit = {
	url: "index.php?act=erp_bill_c&op=insert",
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
	      	showDialog('添加成功! 单据编号：'+res.data.bill_no,'succ', '提示信息', function(){
		        window.location.href='index.php?act=erp_bill&op=show&bill_id='+res.data.bill_id;
		    });
		}else{
		   showError(res.msg);
		}
	}
};
$("#"+formID+" #btn_submit").click(function(){
   $("#"+formID).ajaxSubmit(optionsSubmit);
});

$("#goods_itemid").change(function(){
    $.ajax({
        type:'POST',
        url:'index.php?act=erp_bill_c&op=get_goods_info',
        cache:false,
        data:"goods_itemid="+$(this).val(),
        dataType:'json',
        error:function(){

        },
        success:function(result){
           if(result.state&&result.data.goods_list.length>0){
               var html="";
               var data=result.data.goods_list;
               console.log(data);
               for(var i=0;i<data.length;i++){
                   var goods_info=data[i];
                   html +='<tr><td>'+goods_info['goods_id']+'</td><td>'+goods_info['goods_sn']+'</td><td>'+goods_info['goods_name']+'</td><td>'+goods_info['num']+'</td><td>'+goods_info['mingyichengben']+'</td><td>'+goods_info['caizhi']+'</td><td>'+goods_info['jinse']+'</td><td>'+goods_info['shoucun']+'</td><td>'+goods_info['yanse']+'</td><td>'+goods_info['zuanshidaxiao']+'</td><td>'+goods_info['zhushilishu']+'</td></tr>';
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