<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
  <!--<a href="<?php echo urlShop('diamond_info', 'plate_add');?>" class="ncbtn ncbtn-mint" title="添加">添加</a>-->
</div>
<!--<div class="alert mt15 mb5"><strong>操作提示：</strong>
  <ul>
    <li>1、关联版式可以把预设内容插入到商品描述的顶部或者底部，方便商家对商品描述批量添加或修改。</li>
  </ul>
</div>-->
<form method="get">
<input type="hidden" name="act" value="diamond_info">
<table class="search-form">
    <tr>
      
	  <th>形状</th>
      <td class="w90">
        <select name="shape" class="w80">
          <option value="">请选择</option>
          <?php echo paramsHelper::echoOption('shape',$_GET['shape']); ?>
        </select>
      </td>
	  
	  <th>颜色</th>
      <td class="w80">
        <select name="color">
          <option value="">请选择</option>
          <?php echo paramsHelper::echoOption('yanse',$_GET['color']); ?>
        </select>
      </td>
	  
	  
	  <th>切工</th>
      <td class="w80">
        <select name="cut">
          <option value="">请选择</option>
          <?php echo paramsHelper::echoOption('cut',$_GET['cut']); ?>
        </select>
      </td>
	  
	  
	  
	  <th>净度</th>
      <td class="w80">
        <select name="clarity">
          <option value="">请选择</option>
          <?php echo paramsHelper::echoOption('jingdu',$_GET['clarity']); ?>
        </select>
      </td>
	  
	  
	  
	  <th>石重</th>
	  <td><input type="text" name="carat_min" value="<?php echo $_GET['carat_min'] ?>" class="text w50"/> ct -- <input type="text" name="carat_max" class="text w50" value="<?php echo $_GET['carat_max'] ?>"></td>
	  <td></td>
	  </tr>
	  
	  
	  <tr>
	  <th>对称</th>
      <td class="w90">
        <select name="symmetry" class="w80">
          <option value="">请选择</option>
          <?php echo paramsHelper::echoOption('symmetry',$_GET['symmetry']); ?>
        </select>
      </td>
	  
	  
	  
	  
	  <th>抛光</th>
      <td class="w80">
        <select name="polish">
          <option value="">请选择</option>
          <?php echo paramsHelper::echoOption('polish',$_GET['polish']); ?>
        </select>
      </td>
	  
	  
      <th>证书类型</th>
      <td class="w80">
        <select name="cert">
          <option value="">请选择</option>
          <?php foreach ($output['cert'] as $key => $val) {?>
          <option value="<?php echo $val;?>" <?php if ($val==$_GET['cert']) {?>selected="selected"<?php }?>><?php echo $val;?></option>
          <?php }?>
        </select>
      </td>
      <th>现货期货</th>
      <td class="w80">
        <select name="good_type">
          <option value="">请选择</option>
          <?php foreach ($output['good_type'] as $key => $val) {?>
          <option value="<?php echo $key;?>" <?php if (is_numeric($_GET['good_type']) && $key==$_GET['good_type']) {?>selected="selected"<?php }?>><?php echo $val;?></option>
          <?php }?>
        </select>
      </td>
	  
	  
	  
      <th>证书号</th>
      <td class="w200"><input type="text" class="text w140" name="cert_id" value="<?php echo $_GET['cert_id']; ?>"/></td>
      <td class="w70 tc"><label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" /></label></td>
    </tr>
</table>
</form>
<table class="ncsc-default-table">
  <thead>
    <tr>
      <th class="w30"></th>
      <th class="w100">形状</th>
      <th class="w100">重量</th>
      <th class="w100">颜色</th>
      <th class="w100">净度</th>
      <th class="w100">切工</th>
      <th class="w100">抛光</th>
      <th class="w100">荧光</th>
      <th class="w100">对称</th>
      <th class="w100">证书类型</th>
      <th class="w100">证书号</th>      
	  <th class="w100">批发价</th>
	  <th class="w100">零售价</th>
      <th class="w100">货品类型</th>
      <!--<th class="w160"><?php echo $lang['nc_handle'];?></th>-->
    </tr>
    <?php if (!empty($output['diamondlist'])) { ?>
    <!--<tr>
      <td class="tc"><input type="checkbox" id="all" class="checkall"/></td>
      <td colspan="10"><label for="all" ><?php echo $lang['nc_select_all'];?></label>
        <a href="javascript:void(0);" nc_type="batchbutton" uri="<?php echo urlShop('diamond_info', 'drop_plate');?>" name="p_id" confirm="<?php echo $lang['nc_ensure_del'];?>" class="ncbtn-mini"><i class="icon-trash"></i><?php echo $lang['nc_del'];?></a>
      </td>
    </tr>-->
    <?php } ?>
  </thead>
  <tbody>
    <?php if (!empty($output['diamondlist'])) { ?>
    <?php foreach($output['diamondlist'] as $val) { ?>
    <tr class="bd-line">
      <td class="tc"><input type="checkbox" class="checkitem tc" value="<?php echo $val['id']; ?>"/></td>
      <td><?php echo $output['shape_all'][$val['shape']]; ?></td>
      <td><?php echo $val['carat'];?></td>
      <td><?php echo $val['color'];?></td>
      <td><?php echo $val['clarity'];?></td>
      <td><?php echo $val['cut'];?></td>
      <td><?php echo $val['polish']; ?></td>
      <td><?php echo $val['fluorescence'];?></td>
      <td><?php echo $val['symmetry'];?></td>
      <td><?php echo $val['cert'];?></td>
      <td><?php echo $val['cert_id'];?></td>      
      <td>
        <?php if($output['show_chengben']){?>
          <?php echo $val['pifajia'];?>
        <?php } ?>  
      </td>
	  <td><?php echo $val['shop_price'];?></td>
      <td><?php echo $output['good_type'][$val['good_type']];?></td>
      <!--<td class="nscs-table-handle">
        <span><a href="<?php echo urlShop('diamond_info', 'plate_edit', array('p_id' => $val['id']));?>" class="btn-bluejeans"><i class="icon-edit"></i><p><?php echo $lang['nc_edit'];?></p></a></span>
        <span><a href="javascript:void(0)" onclick="ajax_get_confirm('<?php echo $lang['nc_ensure_del'];?>', '<?php echo urlShop('diamond_info', 'drop_plate', array('p_id' => $val['id']));?>');" class="btn-grapefruit"><i class="icon-trash"></i><p><?php echo $lang['nc_del'];?></p></a></span>
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
    <?php if (!empty($output['diamondlist'])) { ?>
    <!--<tr>
      <th class="tc"><input type="checkbox" id="all" class="checkall"/></th>
      <th colspan="10"><label for="all" ><?php echo $lang['nc_select_all'];?></label>
        <a href="javascript:void(0);" nc_type="batchbutton" uri="<?php echo urlShop('diamond_info', 'drop_plate');?>" name="p_id" confirm="<?php echo $lang['nc_ensure_del'];?>" class="ncbtn-mini"><i class="icon-trash"></i><?php echo $lang['nc_del'];?></a>
       </th>
    </tr>-->
    <tr>
      <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
    </tr>
    <?php } ?>
  </tfoot>
</table>
