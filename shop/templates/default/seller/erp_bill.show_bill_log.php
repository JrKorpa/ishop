<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<div class="datalist">
<table class="ncsc-default-table order" style="width:100%">
  <thead>
   	<tr><th colspan="5" style="text-align:left"></tr> 
    <tr nc_type="table_header">
      <th class="w160">时间</th>
	  <th  style="text-align:left">操作描述</th>	  
	  <th class="w80">操作人</th>
	  <th class="w150">单据编号</th>	 
      <th class="w80">单据状态</th>	 
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($output['bill_log_list'])) { $i=1;?>
    <?php foreach ($output['bill_log_list'] as $val) { ?>	
    <tr>	 
	  <td><?php echo $val['create_time']; ?></td>
	  <td style="text-align:left"><?php echo $val['remark']; ?></td>
	  
	  <td><?php echo $val['create_user']; ?></td>	  
	  <td><?php echo $output['bill_info']['bill_no']; ?></td>
	  <td><?php echo $val['bill_status_name']; ?></td>		  		    
    </tr>
    <?php } ?>
    <?php } else { ?>
    <tr>
      <td colspan="11" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
    </tr>
    <?php } ?>
  </tbody>
</table>
</div>
<div class="pagination"> <?php echo $output['show_page']; ?> </div>