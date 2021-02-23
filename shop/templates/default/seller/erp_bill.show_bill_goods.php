<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
  <?php 
  if(in_array($bill_info['bill_type'],array('M','B','C','D'))){
      $tableWidth = "1100px";
  }else{
      $tableWidth = "1500px";
  }
  $bill_info = $output['bill_info'];
  $bill_type_name = billType($bill_info); 
  ?>
</div>
<div class="datalist" style="width:100%;height: auto;overflow-x: auto;">
<table class="ncsc-default-table order" style="width:<?php echo $tableWidth?>;">
  <thead>
   	<tr><th colspan="10" style="text-align:left"></tr> 
    <tr nc_type="table_header">
	  <th>序号</th>
	  <th>货号</th>
      <th>款号</th>
	  <th>商品名称</th>
	  <?php if($bill_info['bill_type']=="W"){?>
	   <th>数量</th>
	   <th>盘点状态</th>
	   <th>调整</th>
	   <th>盘点仓库</th>
	   <th>所属仓库</th>
	  <?php }?>
	  <th>材质</th>
	  <th>颜色</th>
	  <th>证书类型</th>
	  <th>证书号</th>
	  <th>钻石大小</th> 
	  <th>主石粒数</th>
	  <th>指圈</th>
	  <th>金重</th>
	  <th>数量</th>
	  <?php if(in_array($bill_info['bill_type'],array('L'))){?>	  
      <th>成本价</th>
	  <th>入库方式</th>
	  <th>入库仓库</th>	  	  
	  <?php }else if(in_array($bill_info['bill_type'],array('M'))){?>
	  <th>出库仓库</th> 
	  <th>入库仓库</th>
	  <?php }else if(in_array($bill_info['bill_type'],array('B','C'))){?>
	  <th>成本价</th>
	  <?php }else if(in_array($bill_info['bill_type'],array('D'))){?>	 
      <th>成本价</th> 
	  <?php if(in_array($bill_info['item_type'],array('PF','LS'))){?>
	  <th>退货价</th>
	  <?php } ?>
	  
	  <?php }else if(in_array($bill_info['bill_type'],array('S'))){?> 
	  <?php if(in_array($bill_info['item_type'],array('PF','LS'))){?>
	  <th>成本价</th>
	    <?php if($_SESSION['store_company_id'] == $bill_info['from_company_id']){?>
	    <th>销售价</th>
	    <?php }?>
	  <?php }?>
	  <?php if ($bill_info['item_type']=='PF'){?>	
      
	  <th>管理费</th>
  
	  <th><?php echo strpos($bill_type_name,"出")!==false?"出库方式":'入库方式'?></th>
	  <th>结算状态</th>
	  <th>结算人</th>
	  <th>结算时间</th>
	  <?php }?>
	  
	  <?php }?>	 
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($output['bill_goods_list'])) { ?>
    <?php foreach ($output['bill_goods_list'] as $i=>$val) { ?>	
      <tr>
	      <td><?php echo $i+1; ?></td>
		  <td><?php echo $val['goods_itemid']; ?></td>
		  <td><?php echo $val['goods_sn']; ?></td>	  
		  <td><?php echo $val['goods_name']; ?></td>
		  <?php if($bill_info['bill_type']=="W"){?>
		   <td><?php echo $val['goods_count']; ?></td>
		   <td><?php echo $val['pandian_status_name']; ?></td>
		   <td><?php echo $val['pandian_adjust_name']; ?></td>
		   <td><?php echo $val['from_house_name']; ?></td>
		   <td><?php echo $val['warehouse']; ?></td>
		  <?php }?>
		  <td><?php echo $val['caizhi']; ?></td>
		  <td><?php echo $val['jinse']; ?></td>	
		  <td><?php echo $val['zhengshuleibie']; ?></td>	
		  <td><?php echo $val['zhengshuhao']; ?></td>
		  <td><?php echo $val['zuanshidaxiao']; ?></td>
		  <td><?php echo $val['zhushilishu']; ?></td>
		  <td><?php echo $val['shoucun']; ?></td>
		  <td><?php echo $val['jinzhong']; ?></td>
		  <td><?php echo $val['goods_count']; ?></td>
	 <?php if(in_array($bill_info['bill_type'],array('L'))){?>		  
		  <td><?php echo billgoodsChengbenShow($bill_info, $val, $output['show_chengben']); ?></td>
		  <td><?php echo paramsHelper::echoOptionText("in_warehouse_type",$val['in_warehouse_type']);?></td>
		  <td><?php echo $val['to_house_name']; ?></td>	          
	 <?php }else if(in_array($bill_info['bill_type'],array('M'))){?>
		  <td><?php if($bill_info['from_company_id'] == 58){ echo '总公司维修库'; }else{ echo $val['from_house_name'];} ?></td>
		  <td><?php echo $val['to_house_name']; ?></td>
	 <?php }else if(in_array($bill_info['bill_type'],array('B','C'))){?>
		  <td><?php echo billgoodsChengbenShow($bill_info, $val, $output['show_chengben']); ?></td>
	<?php }else if(in_array($bill_info['bill_type'],array('D'))){?>		
          <td><?php echo billgoodsChengbenShow($bill_info, $val, $output['show_chengben']); ?></td>
          <?php if(in_array($bill_info['item_type'],array('PF','LS'))){?>		  
		     <td><?php echo $val['sale_price']; ?></td>
	      <?php } ?>  
		 
   <?php }else if(in_array($bill_info['bill_type'],array('S'))){?> 
    <?php if(in_array($bill_info['item_type'],array('PF','LS'))){?>
          <?php if($_SESSION['store_company_id'] == $bill_info['from_company_id']){?>
		  <td><?php echo billgoodsChengbenShow($bill_info, $val, $output['show_chengben']); ?></td>
          <?php }else if($_SESSION['store_company_id'] == $bill_info['to_company_id'] && $output['show_chengben']){ ?>
		    <td><?php echo $val['sale_price']; ?></td>
          <?php }else{?>
		    <td></td>
		  <?php } ?>
		  <?php if($_SESSION['store_company_id'] == $bill_info['from_company_id']){?>
		  <td><?php echo $val['sale_price']; ?></td> 
		  <?php }?>
	<?php }?>	  
		  <?php if ($bill_info['item_type']=='PF'){?>	              
           <td><?php echo $val['management_fee']; ?></td> 
         
		  <td><?php echo paramsHelper::echoOptionText("in_warehouse_type",$val['in_warehouse_type']);?></td>
          <td><?php echo $output['settle'][$val['is_settled']]; ?></td>
          <td><?php echo $val['settle_user']; ?></td>
          <td><?php echo $val['settle_time']; ?></td> 
		  <?php }?>          
	  <?php }?>   
    </tr>
    <?php } ?>
    <?php } else { ?>
    <tr>
      <td colspan="10" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
    </tr>
    <?php } ?>
  </tbody>
</table>
</div>
<div class="pagination"> <?php echo $output['show_page']; ?> </div>
