<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<style>.search-form td{ padding:0}</style>
<div class="search">
<table class="search-form" style="width:500px; margin:10px auto; font-size:14px; border:none">
    <tr><td>货号：<?php echo $output['goods_items_info']['goods_id'];?></td><td>款号：<?php echo $output['goods_items_info']['goods_sn'];?></td><td>货品状态：<?php echo $output['goods_state_list'][$output['goods_items_info']['is_on_sale']];?></td></tr>      
    <!--<tr><td colspan="3">商品名称：<?php echo $output['goods_items_info']['goods_name'];?></td></tr> -->        
</table>
</div>
<div class="datalist">
<table class="ncsc-default-table">
  <thead>
    <tr>
      <th class="w30">序号</th>
      <th class="w60">操作类型</th>
	  <th class="w300">系统日志</th>
	  <th class="w100">仓库</th>
      <th class="w50">货品状态</th>
	  <th class="w50">操作人</th>
	  <th class="w50">操作时间</th>      
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($output['goods_log_list'])) { $i=1;?>
    <?php foreach($output['goods_log_list'] as $val) { ?>
    <tr class="bd-line">
      <td><?php echo $i++; ?></td>
      <td><?php echo $output['log_type_list'][$val['log_type']]?></td>
	  <td style="text-align:left; padding:0px 10px">
	  <?php echo $val['log_remark']?>
	  </td>
      <td><?php echo $val['goods_warehouse'];?></td>
	  <td><?php echo $output['goods_state_list'][$val['goods_state']];?></td>
	  <td><?php echo $val['log_user_name'];?></td>
	  <td><?php echo $val['log_time'];?></td>    
    </tr>
    <?php } ?>
    <?php } else { ?>
    <tr>
      <td colspan="10" class="norecord">
	  <div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div>
	  </td>
    </tr>
    <?php } ?>
  </tbody>
</table>
</div>
<div class="pagination"> <?php echo $output['show_page']; ?> </div>