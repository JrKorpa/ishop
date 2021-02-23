<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/thickbox/thickbox.js?v=1.1" charset="utf-8"></script>
<link href="<?php echo RESOURCE_SITE_URL;?>/js/thickbox/thickbox.css" rel="stylesheet" />
<div class="tabmenu">
  <?php include template('layout/submenu');?>
  <a href="<?php echo urlShop('store_task', 'store_task_add');?>" class="ncbtn ncbtn-mint" title="添加">添加</a>
</div>
<!--<div class="alert mt15 mb5"><strong>操作提示：</strong>
  <ul>
    <li>1、关联版式可以把预设内容插入到商品描述的顶部或者底部，方便商家对商品描述批量添加或修改。</li>
  </ul>
</div>-->
<form method="get">
<input type="hidden" name="act" value="store_task">
<table class="search-form">
    <tr>
      <td>&nbsp;</td>
      <th>年份</th>
      <td class="w80">
        <select name="year">
          <option>请选择</option>
          <?php for ($i=date('Y',time());$i>=2019; $i--) {?>
          <option value="<?php echo $i;?>" <?php if (is_numeric($_GET['year']) && $i==$_GET['year']) {?>selected="selected"<?php }?>><?php echo $i;?></option>
          <?php }?>
        </select>
      </td>
      <th>月份</th>
      <td class="w80">
        <select name="month">
          <option>请选择</option>
          <?php for ($i=1;$i<=12;$i++) {?>
          <option value="<?php echo $i;?>" <?php if (is_numeric($_GET['month']) && $i==$_GET['month']) {?>selected="selected"<?php }?>><?php if($i <10){echo '0'.$i;}else{echo $i;}?></option>
          <?php }?>
        </select>
      </td>
      
      <td class="w70 tc"><label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" /></label></td>
    </tr>
</table>
</form>
<table class="ncsc-default-table">
  <thead>
    <tr>
      <th class="w30"></th>
      <th class="w120">年月</th>
      <th class="w280">任务目标</th>
      <th class="w80">添加时间</th>
    
      <th class="w200"><?php echo $lang['nc_handle'];?></th>
    </tr>
    
  </thead>
  <tbody>
    <?php if (!empty($output['storetasklist'])) { ?>
    <?php foreach($output['storetasklist'] as $val) { ?>
    <tr class="bd-line">
      <td class="tc"><input type="checkbox" class="checkitem tc" value="<?php echo $val['id']; ?>"/></td>
      <td><?php echo $val['year'].'年'.$val['month'].'月'; ?></td>
      <td><?php echo $val['task'];?></td>
      <td><?php echo date('Y-m-d',$val['add_time']);?></td>

      <td class="nscs-table-handle">
	    <span><a href="<?php echo urlShop('store_task', 'view_logs', array('id' => $val['id']));?>&&keepThis=true&TB_iframe=true&height=500&width=800" class="btn-bluejeans thickbox" title="查看日志"><i class="icon-search"></i><p>查看日志</p></a></span>
		
        <span><a href="<?php echo urlShop('store_task', 'store_task_edit', array('p_id' => $val['id']));?>" class="btn-bluejeans"><i class="icon-edit"></i><p><?php echo $lang['nc_edit'];?></p></a></span>

      </td>
    </tr>
    <?php } ?>
    <?php } else { ?>
    <tr>
      <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
    </tr>
    <?php } ?>
  </tbody>
  <tfoot>
    <?php if (!empty($output['storetasklist'])) { ?>
    
    <tr>
      <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
    </tr>
    <?php } ?>
  </tfoot>
</table>
