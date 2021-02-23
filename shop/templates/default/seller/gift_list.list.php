<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
  <!--<a href="<?php echo urlShop('diamond_jiajialv', 'plate_add');?>" class="ncbtn ncbtn-mint" title="添加">添加</a>-->
</div>
<!--<div class="alert mt15 mb5"><strong>操作提示：</strong>
  <ul>
    <li>1、关联版式可以把预设内容插入到商品描述的顶部或者底部，方便商家对商品描述批量添加或修改。</li>
  </ul>
</div>-->
<form method="get">
<input type="hidden" name="act" value="gift_list">
<table class="search-form">
    <tr>
      <td>&nbsp;</td>     
      <th>名称</th>
      <td class="w160"><input type="text" class="text w150" name="gift_name" value="<?php echo $_GET['gift_name']; ?>"/></td>
      <th>款号</th>
      <td class="w160"><input type="text" class="text w150" name="goods_number" value="<?php echo $_GET['goods_number']; ?>"/></td>
      <td class="w70 tc"><label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" /></label></td>
    </tr>
</table>
</form>
<table class="ncsc-default-table">
  <thead>
    <tr>
      <th class="w30"></th>
      <th class="w300">赠品名称</th>
      <th class="w100">赠品款号</th>
      <th class="w100">是否活圈</th>
      <th class="w100">销售价格</th>
      <th class="w100">是否启用</th>
      <th class="w100">添加时间</th>
      <th class="w100">修改时间</th>
      <th class="w100">是否销账</th>
      <th class="w100">可销售渠道</th>
      <!--<th class="w110"><?php echo $lang['nc_handle'];?></th>-->
    </tr>
    <?php if (!empty($output['giftlist'])) { ?>
    <!--<tr>
      <td class="tc"><input type="checkbox" id="all" class="checkall"/></td>
      <td colspan="10"><label for="all" ><?php echo $lang['nc_select_all'];?></label>
        <a href="javascript:void(0);" nc_type="batchbutton" uri="<?php echo urlShop('diamond_jiajialv', 'drop_diamond_jiajialv');?>" name="p_id" confirm="<?php echo $lang['nc_ensure_del'];?>" class="ncbtn-mini"><i class="icon-trash"></i><?php echo $lang['nc_del'];?></a>
      </td>
    </tr>-->
    <?php } ?>
  </thead>
  <tbody>
    <?php if (!empty($output['giftlist'])) { ?>
    <?php foreach($output['giftlist'] as $val) { ?>
    <tr class="bd-line">
      <td class="tc"><input type="checkbox" class="checkitem tc" value="<?php echo $val['id']; ?>"/></td>
      <td><?php echo $val['goods_name']; ?></td>
      <td><?php echo $val['goods_id'];?></td>
      <td><?php echo $val['is_randring'] == 1?'是':'否';?></td>
      <td><?php echo $val['goods_price'];?></td>
      <td><?php echo $val['status'] == 1?'启用':'禁用';?></td>
      <td><?php echo date('Y-m-d H:i:s', $val['add_time']);?></td>
      <td><?php echo date('Y-m-d H:i:s', $val['update_time']);?></td>
      <td><?php echo $val['is_xz'] == 2?'是':'否';?></td>
      <td><?php echo $output['sale_way'][$val['sale_way']];?></td>
      <!--<td class="nscs-table-handle">
        <span><a href="<?php echo urlShop('diamond_jiajialv', 'plate_edit', array('p_id' => $val['id']));?>" class="btn-bluejeans"><i class="icon-edit"></i><p><?php echo $lang['nc_edit'];?></p></a></span>
        <span><a href="javascript:void(0)" onclick="ajax_get_confirm('<?php echo $lang['nc_ensure_del'];?>', '<?php echo urlShop('diamond_jiajialv', 'drop_diamond_jiajialv', array('p_id' => $val['id']));?>');" class="btn-grapefruit"><i class="icon-trash"></i><p><?php echo $lang['nc_del'];?></p></a></span>
      </td>-->
    </tr>
    <?php } ?>
    <?php } else { ?>
    <tr>
      <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
    </tr>
    <?php } ?>
  </tbody>
  <tfoot>
    <?php if (!empty($output['giftlist'])) { ?>
    <!--<tr>
      <th class="tc"><input type="checkbox" id="all" class="checkall"/></th>
      <th colspan="10"><label for="all" ><?php echo $lang['nc_select_all'];?></label>
        <a href="javascript:void(0);" nc_type="batchbutton" uri="<?php echo urlShop('diamond_jiajialv', 'drop_diamond_jiajialv');?>" name="p_id" confirm="<?php echo $lang['nc_ensure_del'];?>" class="ncbtn-mini"><i class="icon-trash"></i><?php echo $lang['nc_del'];?></a>
       </th>
    </tr>
    <tr>
        <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
    </tr>-->
    <?php } ?>
  </tfoot>
</table>
