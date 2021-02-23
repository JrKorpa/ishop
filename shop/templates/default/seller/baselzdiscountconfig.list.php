<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
  <a href="<?php echo urlShop('base_lz_discount_config', 'baselzdiscountconfig_add');?>" class="ncbtn ncbtn-mint" title="添加">添加</a>
</div>
<!--<div class="alert mt15 mb5"><strong>操作提示：</strong>
  <ul>
    <li>1、关联版式可以把预设内容插入到商品描述的顶部或者底部，方便商家对商品描述批量添加或修改。</li>
  </ul>
</div>-->
<form method="get">
<input type="hidden" name="act" value="base_lz_discount_config">
<table class="search-form">
    <tr>
      <td>&nbsp;</td>
      <th>销售顾问</th>
      <td class="w120">
        <select name="user_id">
          <option value="">请选择</option>
          <?php foreach ($output['seller'] as $key => $val) {?>
          <option value="<?php echo $key;?>" <?php if (is_numeric($_GET['seller']) && $key==$_GET['seller']) {?>selected="selected"<?php }?>><?php echo $val;?></option>
          <?php }?>
        </select>
      </td>
      <th>商品类型</th>
      <td class="w120">
        <select name="type">
          <option value="">请选择</option>
          <?php foreach ($output['type'] as $key => $val) {?>
          <option value="<?php echo $key;?>" <?php if (is_numeric($_GET['type']) && $key==$_GET['type']) {?>selected="selected"<?php }?>><?php echo $val;?></option>
          <?php }?>
        </select>
      </td>
      <th>状态</th>
      <td class="w120">
        <select name="enabled">
          <option value="">请选择</option>
          <?php foreach ($output['enabled'] as $key => $val) {?>
          <option value="<?php echo $key;?>" <?php if (is_numeric($_GET['enabled']) && $key==$_GET['enabled']) {?>selected="selected"<?php }?>><?php echo $val;?></option>
          <?php }?>
        </select>
      </td>
      <!--<th>体验店</th>
      <td class="w160"><input type="text" class="text w150" name="store_name" value="<?php echo $_GET['store_name']; ?>"/></td>-->
      <td class="w70 tc"><label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" /></label></td>
    </tr>
</table>
</form>
<table class="ncsc-default-table">
  <thead>
    <tr>
      <th class="w30"></th>
      <th class="w100">销售顾问</th>
      <th class="w200">商品类型</th>
      <th class="w100">折扣</th>
      <th class="w100">状态</th>
      <th class="w110"><?php echo $lang['nc_handle'];?></th>
    </tr>
    <?php if (!empty($output['baselzdiscountconfiglist'])) { ?>
    <tr>
      <td class="tc"><input type="checkbox" id="all" class="checkall"/></td>
      <td colspan="10"><label for="all" ><?php echo $lang['nc_select_all'];?></label>
        <a href="javascript:void(0);" nc_type="batchbutton" uri="<?php echo urlShop('base_lz_discount_config', 'drop_base_lz_discount_config');?>" name="id" confirm="<?php echo $lang['nc_ensure_del'];?>" class="ncbtn-mini"><i class="icon-trash"></i><?php echo $lang['nc_del'];?></a>
      </td>
    </tr>
    <?php } ?>
  </thead>
  <tbody>
    <?php if (!empty($output['baselzdiscountconfiglist'])) { ?>
    <?php foreach($output['baselzdiscountconfiglist'] as $val) { ?>
    <tr class="bd-line">
      <td class="tc"><input type="checkbox" class="checkitem tc" value="<?php echo $val['id']; ?>"/></td>
      <td><?php echo $output['seller'][$val['user_id']]; ?></td>
      <td><?php echo $output['type'][$val['type']];?></td>
      <td><?php echo $val['zhekou'];?></td>
      <td><?php echo $output['enabled'][$val['enabled']];?></td>
      <td class="nscs-table-handle">
        <span><a href="<?php echo urlShop('base_lz_discount_config', 'baselzdiscountconfig_edit', array('id' => $val['id']));?>" class="btn-bluejeans"><i class="icon-edit"></i><p><?php echo $lang['nc_edit'];?></p></a></span>
        <span><a href="javascript:void(0)" onclick="ajax_get_confirm('<?php echo $lang['nc_ensure_del'];?>', '<?php echo urlShop('base_lz_discount_config', 'drop_base_lz_discount_config', array('id' => $val['id']));?>');" class="btn-grapefruit"><i class="icon-trash"></i><p><?php echo $lang['nc_del'];?></p></a></span>
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
    <?php if (!empty($output['baselzdiscountconfiglist'])) { ?>
    <tr>
      <th class="tc"><input type="checkbox" id="all" class="checkall"/></th>
      <th colspan="10"><label for="all" ><?php echo $lang['nc_select_all'];?></label>
        <a href="javascript:void(0);" nc_type="batchbutton" uri="<?php echo urlShop('baselzdiscountconfig', 'drop_base_lz_discount_config');?>" name="id" confirm="<?php echo $lang['nc_ensure_del'];?>" class="ncbtn-mini"><i class="icon-trash"></i><?php echo $lang['nc_del'];?></a>
       </th>
    </tr>
    <tr>
      <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
    </tr>
    <?php } ?>
  </tfoot>
</table>
