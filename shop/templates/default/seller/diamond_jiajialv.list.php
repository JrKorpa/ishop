<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/thickbox/thickbox.js?v=1.1" charset="utf-8"></script>
<link href="<?php echo RESOURCE_SITE_URL;?>/js/thickbox/thickbox.css" rel="stylesheet" />
<div class="tabmenu">
  <?php include template('layout/submenu');?>
  <a href="<?php echo urlShop('diamond_jiajialv', 'plate_add');?>" class="ncbtn ncbtn-mint" title="添加">添加</a>
</div>
<!--<div class="alert mt15 mb5"><strong>操作提示：</strong>
  <ul>
    <li>1、关联版式可以把预设内容插入到商品描述的顶部或者底部，方便商家对商品描述批量添加或修改。</li>
  </ul>
</div>-->
<form method="get">
<input type="hidden" name="act" value="diamond_jiajialv">
<table class="search-form">
    <tr>
      <td>&nbsp;</td>
      <th>证书类型</th>
      <td class="w80">
        <select name="cert">
          <option>请选择</option>
          <?php foreach ($output['cert'] as $key => $val) {?>
          <option value="<?php echo $val;?>" <?php if (is_numeric($_GET['cert']) && $key==$_GET['cert']) {?>selected="selected"<?php }?>><?php echo $val;?></option>
          <?php }?>
        </select>
      </td>
      <th>现货期货</th>
      <td class="w80">
        <select name="good_type">
          <option>请选择</option>
          <?php foreach ($output['good_type'] as $key => $val) {?>
          <option value="<?php echo $key;?>" <?php if (is_numeric($_GET['good_type']) && $key==$_GET['good_type']) {?>selected="selected"<?php }?>><?php echo $val;?></option>
          <?php }?>
        </select>
      </td>
      <th>状态</th>
      <td class="w80">
        <select name="status">
          <option>请选择</option>
          <?php foreach ($output['status'] as $key => $val) {?>
          <option value="<?php echo $key;?>" <?php if (is_numeric($_GET['status']) && $key==$_GET['status']) {?>selected="selected"<?php }?>><?php echo $val;?></option>
          <?php }?>
        </select>
      </td>
      <th>体验店</th>
      <td class="w160"><input type="text" class="text w150" name="store_name" value="<?php echo $_GET['store_name']; ?>"/></td>
      <td class="w70 tc"><label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" /></label></td>
    </tr>
</table>
</form>
<table class="ncsc-default-table">
  <thead>
    <tr>
      <th class="w30"></th>
      <th class="w280">体验店</th>
      <th class="w80">证书类型</th>
      <th class="w100">最小钻重<span style="color: green;">[含]</span></th>
      <th class="w140">最大钻重<span style="color: green;">[不含]</span></th>
      <th class="w80">加价率</th>
      <th class="w80">货品类型</th>
      <th class="w80">状态</th>
      <th class="w200"><?php echo $lang['nc_handle'];?></th>
    </tr>
    <?php if (!empty($output['diamondjiajialvlist'])) { ?>
    <tr>
      <td class="tc"><input type="checkbox" id="all" class="checkall"/></td>
      <td colspan="10"><label for="all" ><?php echo $lang['nc_select_all'];?></label>
        <a href="javascript:void(0);" nc_type="batchbutton" uri="<?php echo urlShop('diamond_jiajialv', 'drop_diamond_jiajialv');?>" name="p_id" confirm="<?php echo $lang['nc_ensure_del'];?>" class="ncbtn-mini"><i class="icon-trash"></i><?php echo $lang['nc_del'];?></a>
      </td>
    </tr>
    <?php } ?>
  </thead>
  <tbody>
    <?php if (!empty($output['diamondjiajialvlist'])) { ?>
    <?php foreach($output['diamondjiajialvlist'] as $val) { ?>
    <tr class="bd-line">
      <td class="tc"><input type="checkbox" class="checkitem tc" value="<?php echo $val['id']; ?>"/></td>
      <td><?php echo $val['store_name']; ?></td>
      <td><?php echo $val['cert'];?></td>
      <td><?php echo $val['carat_min'];?></td>
      <td><?php echo $val['carat_max'];?></td>
      <td><?php echo $val['jiajialv'];?></td>
      <td><?php echo $output['good_type'][$val['good_type']];?></td>
      <td><?php echo $output['status'][$val['status']];?></td>
      <td class="nscs-table-handle">
	    <span><a href="<?php echo urlShop('diamond_jiajialv', 'view_logs', array('id' => $val['id']));?>&&keepThis=true&TB_iframe=true&height=500&width=800" class="btn-bluejeans thickbox" title="查看日志"><i class="icon-search"></i><p>查看日志</p></a></span>
		
        <span><a href="<?php echo urlShop('diamond_jiajialv', 'plate_edit', array('p_id' => $val['id']));?>" class="btn-bluejeans"><i class="icon-edit"></i><p><?php echo $lang['nc_edit'];?></p></a></span>
        <span><a href="javascript:void(0)" onclick="ajax_get_confirm('<?php echo $lang['nc_ensure_del'];?>', '<?php echo urlShop('diamond_jiajialv', 'drop_diamond_jiajialv', array('p_id' => $val['id']));?>');" class="btn-grapefruit"><i class="icon-trash"></i><p><?php echo $lang['nc_del'];?></p></a></span>

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
    <?php if (!empty($output['diamondjiajialvlist'])) { ?>
    <tr>
      <th class="tc"><input type="checkbox" id="all" class="checkall"/></th>
      <th colspan="10"><label for="all" ><?php echo $lang['nc_select_all'];?></label>
        <a href="javascript:void(0);" nc_type="batchbutton" uri="<?php echo urlShop('diamond_jiajialv', 'drop_diamond_jiajialv');?>" name="p_id" confirm="<?php echo $lang['nc_ensure_del'];?>" class="ncbtn-mini"><i class="icon-trash"></i><?php echo $lang['nc_del'];?></a>
       </th>
    </tr>
    <tr>
      <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
    </tr>
    <?php } ?>
  </tfoot>
</table>
