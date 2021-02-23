<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
  <?php
  $bill_info = $output['bill_info'];
  unset($output['bill_info']);
  $bill_type = $bill_info['bill_type'];
  $item_type = $bill_info['item_type'];
  $bill_type_name = billType($bill_info);
 ?>
</div>
<style>
.pandian_msg{ font-size:16px}
</style>
<!--单据基本信息 Begin -->
  <div class="ncsc-form-goods">
    <dl>
      <dt>请输入条码盘点：</dt>
      <dd>
	  <input type="text" id="goods_id" placeholder="请输货号" style="border:#0000FF 1px solid"/>
	   <p class="pandian_msg"></p>
	  </dd>	  
    </dl>
    <dl>
      <dt>当前盘点人(<?php echo $_SESSION['seller_name']?>)：</dt>
      <dd id="my_pandian_total"><?php echo $bill_info['my_pandian_total']?></dd>	  
    </dl>
	<dl>
      <dt>已盘数量：</dt>
      <dd id="all_pandian_total"><?php echo $bill_info['all_pandian_total']?></dd>	  
    </dl>
	<dl>	
      <dt>应盘数量</dt>
      <dd>
        <?php echo $bill_info['goods_num']?>
      </dd>
    </dl>
	<dl>	
      <dt>应盘总成本：</dt>
      <dd>
        <?php echo billChengbenTotal($bill_info); ?>
      </dd>
    </dl>
    <dl>
      <dt>单据编号：</dt>
      <dd><?php echo $bill_info['bill_no']?></dd>	  
    </dl>
	<dl>
	<dt>单据类型：</dt>
      <dd><?php echo $bill_type_name;?></dd>
	</dl>
	<dl>
	<dt>单据状态：</dt>
      <dd><?php echo billStatus($bill_info);?>
		  </dd>
	</dl>
    <dl>	
	<dl>	
      <dt>盘点仓库：</dt>
      <dd>
        <?php echo $bill_info['from_house_name']?>
      </dd>
    </dl>
	<dl>	
      <dt>制单人：</dt>
      <dd>
        <?php echo $bill_info['create_user']?>
      </dd>
    </dl>	
	<dl>	
      <dt>制单时间：</dt>
      <dd>
        <?php echo $bill_info['create_time']?>
      </dd>
    </dl>	
	<dl>
      <dt>单据备注：</dt>
      <dd> 
		  <?php echo $bill_info['remark']?>
      </dd>
    </dl>	 
	<dl>
      <dt>&nbsp;</dt>
      <dd>
        <input type="button" id="btn_finished" class="submit" value="盘点结束" />
      </dd>
    </dl>   
    </ul>
  </div>
<!--单据基本信息 END-->  
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.form.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.ui-jqLoding.js"></script>
<script>
$(function(){
    //Ajax提示
    $('a[nctype="ajaxBox"]').click(function(){
       ajax_form($(this).attr('data-id'),$(this).attr('data-title'),$(this).attr('data-url')+'&inajax=1', '480');        
    });

	$("#btn_finished").click(function(){
	   if(!confirm("盘点结束后，不能继续盘点，确认是否继续？")){	   
	       return false;
	   }
	   $(this).jqLoading({text:"正在处理中..."});	   
	   $.ajax({
			url: "index.php?act=erp_bill&op=pandian_finished&bill_id="+<?php echo $bill_info['bill_id']?>,
			dataType:'json',	
			success: function(res) {
				if(res.state){
					showDialog('操作成功！','succ', '提示信息', function(){
						window.location.reload();
					});
				}else{
				   showDialog(res.msg);
				}
				$(this).jqLoading("destroy");
			}
	  });
	});
	$("#goods_id").keydown(function(event){
	    if(event.keyCode != "13") {	  
		    return true;
		}	       
	    var goods_id = $.trim($(this).val());
		if(goods_id==""){
		    return false;
		}			
		$.ajax({
			url: "index.php?act=erp_bill&op=pandian_goods",
			type:'post',
			dataType:'json',
			data:{bill_id:<?php echo $bill_info['bill_id']?>,'goods_id':goods_id},
			success: function(res) {
				if(res.state){				    
					$(".pandian_msg").html("<font color='green'>"+res.msg+"</font>");
					$("#my_pandian_total").html(res.data.my_pandian_total);
					$("#all_pandian_total").html(res.data.all_pandian_total);
				}else{
				    $(".pandian_msg").html("<font color='red'>"+res.msg+"</font>");
				}				
			}			
	    });
		
		$(this).val('');

	});

});
</script>
