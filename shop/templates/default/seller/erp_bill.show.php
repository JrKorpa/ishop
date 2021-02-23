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
<!--单据基本信息 Begin -->
  <div class="ncsc-form-goods">
    <dl>
      <dt>单据编号：</dt>
      <dd><?php echo $bill_info['bill_no']?></dd>	  
    </dl>
	<dl>
	<dt>单据类型：</dt>
      <dd><?php echo $bill_type_name;?></dd>
	</dl>	
    <?php if($bill_type=="S" && $bill_info['item_type']=="PF"){?>
	   <?php if(strpos($bill_type_name,'出')!==false){?>
	    <dl>
			<dt>批发客户：</dt>
			<dd> <?php echo $bill_info['to_company_name']?></dd>
		</dl>
		<dl>
			<dt>出库方式：</dt>
			<dd><?php echo paramsHelper::echoOptionText("out_warehouse_type",$bill_info['out_warehouse_type']);?></dd>
		</dl>
	<?php }else{?>
	    <dl>
			<dt>供应商：</dt>
			<dd> <?php echo $bill_info['supplier_name']?></dd>
		</dl>
		<dl>
			<dt>入库方式：</dt>
			<dd><?php echo paramsHelper::echoOptionText("in_warehouse_type",$bill_info['in_warehouse_type']);?></dd>
		</dl>
	<?php }?>
	  <!--
		<dl>
		<dt>类别：</dt>
			<dd><?php //echo paramsHelper::echoOptionText("pifa_type",$bill_info['pifa_type']);?></dd>
		</dl>-->
    <?php }?>
	<?php if($bill_type=="L"){?>
	    <dl>
			<dt>供应商：</dt>
			<dd> <?php echo $bill_info['supplier_name']?></dd>
		</dl>
	    <dl>
			<dt>入库方式：</dt>
			<dd><?php echo paramsHelper::echoOptionText("in_warehouse_type",$bill_info['in_warehouse_type']);?></dd>
		</dl>
	<?php }?>
	<?php if(($bill_type=="S" && in_array($item_type,array("LS")))/* || $bill_type=="C" || $bill_type=="D"*/){?>
	<dl>
	<dt>订单号：</dt>
      <dd><?php echo $bill_info['order_sn']?></dd>
	</dl>
	<?php }?>
	<dl>
	<dt>单据状态：</dt>
      <dd><?php echo billStatus($bill_info);?>&nbsp&nbsp
		  <?php if ($output['confirm_check'] && $bill_type!='W'){?>	   	  
		  <a href="javascript:void(0);" class="ncbtn ncbtn-mint" data-id="ajax_check_bill" data-title="审核单据" 
		  nctype="ajaxBox" data-url="?act=erp_bill&op=check_bill&bill_id=<?php echo $bill_info['bill_id']?>">审核</a>
		  <?php }else if ($output['sign_check']){?>
          <a href="javascript:void(0);" class="ncbtn ncbtn-mint" data-id="ajax_check_bill" data-title="签收单据" 
          nctype="ajaxBox" data-url="?act=erp_bill&op=sign_for_bill&bill_id=<?php echo $bill_info['bill_id']?>">签收</a>
          <?php }else if($output['settle_check']){?>
		  <a href="javascript:void(0);" class="ncbtn ncbtn-mint" data-id="ajax_check_bill" data-title="结算单据" 
          nctype="ajaxBox" data-url="?act=erp_bill&op=settle_bill&bill_id=<?php echo $bill_info['bill_id']?>">结算</a>
		  <?php }?>
	  </dd>
	</dl>
    <dl>
	<dt>结算状态：</dt>
      <dd><?php echo paramsHelper::echoOptionText("settle_type",$bill_info['is_settled']);?></dd>
	</dl>
	
	<?php if(in_array($bill_type,array('L','T','M','D')) && $bill_info['to_company_id']==$_SESSION['store_company_id']){?>	   
	<dl>	
      <dt>入库仓库：</dt>
      <dd>
        <?php echo $bill_info['to_house_name']?>
      </dd>
    </dl>
	<!--<dl>	
      <dt>入库柜位：</dt>
      <dd>
        <?php echo $bill_info['to_box_name']?>
      </dd>
    </dl>-->
	<?php }else if($bill_type=="W"){?>
	<dl>	
      <dt>盘点仓库：</dt>
      <dd>
        <?php echo $bill_info['from_house_name']?>
      </dd>
    </dl>
	<dl>	
      <dt>已盘数量：</dt>
      <dd>
        <?php echo $bill_info['all_pandian_total']?>
      </dd>
    </dl>
	<?php }?>	
	<?php if(in_array($bill_type,array('L','T','M'))){?>
	  <dl>	
      <dt>入库数量：<dd>	  
        <?php echo $bill_info['goods_num']?>
      </dd>
	  </dl>
	<?php }else if(in_array($bill_type,array('C','S'))){?>
	  <dl>	
      <dt>出库数量：<dd>	  
        <?php echo $bill_info['goods_num']?>
      </dd>
	  </dl>
	<?php }else if($bill_type=='W'){?>
	   <?php if(checkSellerLimit('limit_show_pandian_goods')){?>
	  <dl>	
      <dt>应盘数量：<dd>	  
         <?php echo $bill_info['goods_num']?>
      </dd>
	  </dl>
	  <dl>	
      <dt>应盘总成本：<dd>	  
        <?php echo billChengbenTotal($bill_info); ?>
      </dd>
	  </dl>
	  <?php }?>
	<?php }else{?>
	 <dl>	
      <dt>数量：<dd>	  
         <?php echo $bill_info['goods_num']?>
      </dd>
	  </dl>
	<?php }?>
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
      <dt>审核人：</dt>
      <dd>
        <?php echo $bill_info['check_user']?>
      </dd>
    </dl>	
	<dl>	
      <dt>审核时间：</dt>
      <dd>
        <?php echo $bill_info['check_time']?>
      </dd>
    </dl>
    <!--<dl>    
      <dt>结算类型：</dt>
      <dd>
        <?php echo $output['settle'][$bill_info['is_settled']]?>
      </dd>
    </dl>
    <dl>    
      <dt>结算人：</dt>
      <dd>
        <?php echo $bill_info['settle_user']?>
      </dd>
    </dl>   
    <dl>    
      <dt>结算时间：</dt>
      <dd>
        <?php echo $bill_info['settle_time']?>
      </dd>
    </dl>-->
    <?php if($bill_info['sign_time'] || in_array($bill_info['bill_type'],array("S","D")) && $bill_info['item_type']=='PF'){?>
        <dl>
          <dt>签收人：</dt>
          <dd>
            <?php echo $bill_info['sign_user']?>
          </dd>
        </dl>   
        <dl>
          <dt>签收时间：</dt>
          <dd>
            <?php echo $bill_info['sign_time']?>
          </dd>
        </dl>
    <?php }?>
	<!--<dl>	
      <dt>打印状态：</dt>
      <dd>
        <?php echo $bill_info['is_print']==1?'<font color="green">已打印</font>':'未打印'?>
      </dd>
    </dl>	
	<dl>	
      <dt>打印人：</dt>
      <dd>
        <?php echo $bill_info['print_user']?>
      </dd>
    </dl>	
	<dl>	
      <dt>打印时间：</dt>
      <dd>
        <?php echo $bill_info['print_time']?>
      </dd>
    </dl>-->
	<dl>
      <dt>单据备注：</dt>
      <dd> 
		  <?php echo $bill_info['remark']?>
      </dd>
    </dl>    
    </ul>
  </div>
<!--单据基本信息 END-->  
<script>
$(function(){
    //Ajax提示
    $('a[nctype="ajaxBox"]').click(function(){
       ajax_form($(this).attr('data-id'),$(this).attr('data-title'),$(this).attr('data-url')+'&inajax=1', '480');        
    });
});
</script>
