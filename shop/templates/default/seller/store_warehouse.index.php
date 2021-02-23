<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
  <a href="<?php echo urlShop('store_warehouse', 'add');?>" class="ncbtn ncbtn-mint" title="添加仓库">添加仓库</a>
</div>

<form method="get">
<input type="hidden" name="act" value="store_warehouse">
<table class="search-form">
    <tr>
      <td>&nbsp;</td>
      
      <th>状态</th>
      <td class="w80">
        <select name="is_enabled">
          <option>请选择</option>
          <?php foreach ($output['is_enabled'] as $key => $val) {?>
          <option value="<?php echo $key;?>" <?php if (is_numeric($_GET['is_enabled']) && $key==$_GET['is_enabled']) {?>selected="selected"<?php }?>><?php echo $val;?></option>
          <?php }?>
        </select>
      </td><th>仓库名称</th>
      <td class="w160"><input type="text" class="text w150" name="w_name" value="<?php echo $_GET['w_name']; ?>"/></td>
      <td class="w70 tc"><label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" /></label></td>
    </tr>
</table>
</form>
<table class="ncsc-default-table">
  <thead>
    <tr>
      <th class="w30"></th>
      <th class="w160">公司名称</th>
      <th class="w200">仓库名称</th>
      <th class="w100">仓库类型</th>      
      <th class="w80">仓库编号</th>
	  <th class="w80">上架售卖</th>
      <th class="w80">状态</th>
      <th class="w150"><?php echo $lang['nc_handle'];?></th>
    </tr>
    <?php if (!empty($output['house_list'])) { ?>
    <tr>
      <td class="tc"><input type="checkbox" id="all" class="checkall"/></td>
      <td colspan="10"><label for="all" ><?php echo $lang['nc_select_all'];?></label>
        <a href="javascript:void(0);" nc_type="batchbutton" uri="<?php echo urlShop('store_warehouse', 'drop');?>" name="house_id" confirm="<?php echo $lang['nc_ensure_del'];?>" class="ncbtn-mini"><i class="icon-trash"></i><?php echo $lang['nc_del'];?></a>
      </td>
    </tr>
    <?php } ?>
  </thead>
  <tbody>
    <?php if (!empty($output['house_list'])) { ?>
    <?php foreach($output['house_list'] as $val) { ?>
    <tr class="bd-line">
      <td class="tc"><input type="checkbox" class="checkitem tc" value="<?php echo $val['house_id']; ?>"/></td>
      <td class="w300"><?php echo $val['company_name']; ?></td>
      <td class="w400"><?php echo $val['name']; ?></td>
      <td class="w80"><?php echo $output['type'][$val['type']]; ?></td>      
      <td><?php echo $val['code'];?></td>
	  <td><?php echo $val['is_enabled']==1?"是":"否";?></td>
      <td><?php echo $output['is_enabled'][$val['is_enabled']];?></td>
      <td class="nscs-table-handle w150">
        <!--<span> <a href="javascript:;" class="btn-mint"> <i class="icon-cog" nctype="ajaxBoxList" data-houseid="<?php echo $val['house_id'];?>" title="点击展开查看此仓库全部储位；储位过多时请横向拖动区域内的滚动条进行浏览。"></i>
        <p>储位</p>
        </a> </span>-->
        <span><a href="<?php echo urlShop('store_warehouse', 'edit', array('house_id' => $val['house_id']));?>" class="btn-bluejeans"><i class="icon-edit"></i><p><?php echo $lang['nc_edit'];?></p></a></span>
        <span><a href="javascript:void(0)" onclick="ajax_get_confirm('<?php echo $lang['nc_ensure_del'];?>', '<?php echo urlShop('store_warehouse', 'drop', array('house_id' => $val['house_id']));?>');" class="btn-grapefruit"><i class="icon-trash"></i><p><?php echo $lang['nc_del'];?></p></a></span>
      </td>
    </tr>
    <tr style="display:none;">
      <td colspan="20">
      <div class="ncsc-goods-sku ps-container"></div>
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
    <?php if (!empty($output['house_list'])) { ?>
    <tr>
      <th class="tc"><input type="checkbox" id="all" class="checkall"/></th>
      <th colspan="10"><label for="all" ><?php echo $lang['nc_select_all'];?></label>
        <a href="javascript:void(0);" nc_type="batchbutton" uri="<?php echo urlShop('store_warehouse', 'drop');?>" name="house_id" confirm="<?php echo $lang['nc_ensure_del'];?>" class="ncbtn-mini"><i class="icon-trash"></i><?php echo $lang['nc_del'];?></a>
       </th>
    </tr>
    <tr>
      <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
    </tr>
    <?php } ?>
  </tfoot>
</table>
<script type="text/javascript">
  $(function(){
      // ajax获取商品列表
      $('i[nctype="ajaxBoxList"]').toggle(
          function(){
              var _parenttr = $(this).parents('tr');
              var _houseid = $(this).attr('data-houseid');
              var _div = _parenttr.next().find('.ncsc-goods-sku');
              if (_div.html() == '') {
                  $.getJSON('index.php?act=store_warehouse&op=get_box_list_ajax' , {house_id : _houseid}, function(date){
                      if (date != 'false') {
                          var _ul = $('<ul class="ncsc-goods-sku-list"></ul>');
                          $.each(date, function(i, o){
                              var _li = $('<li></li>');
                              
                              $('<div class="goods-sku">名称：<em>' + o.box_name + '</em></div>').appendTo(_li);
                              $('<div class="goods-sku">说明：<em>' + o.note + '</em></div>').appendTo(_li);
                              $('<div class="goods-sku">是否有效：<em>' + (o.is_enabled>0 ? '有效' : '无效') + '</em></div>').appendTo(_li);
                              //$('<div class="goods-sku">是否锁定：<em>' + (o.is_lock>0 ? '锁定' : '正常')+ '</em></div>').appendTo(_li);
                              $('<div class="edit_box" data-param="{box_id:'+o.box_id+',house_id:'+o.house_id+'}"><a href="javascript:;" class="ncbtn-mini ncbtn-lavander mt5"><i class="icon-edit"></i>编辑储位</a></div>').appendTo(_li);
                              $('<div class="del_box" data-param="{box_id:'+o.box_id+'}"><a href="javascript:;" class="ncbtn-mini  mt5"><i class="icon-trash"></i>删除储位</a></div>').appendTo(_li);
                              _li.appendTo(_ul);
                          });
                          var _li = $('<li></li>');
                          $('<div class="add_box" data-param="{house_id:'+_houseid+'}"><a href="javascript:;" class="ncbtn-mini ncbtn-mint mt5"><i class="icon-plus-sign"></i>添加储位</a></div>').appendTo(_li);
                          _li.appendTo(_ul);
                          _ul.appendTo(_div);
                          _parenttr.next().show();
                          _div.perfectScrollbar();
                      }
                  });
              } else {
                _parenttr.next().show()
              }
          },
          function(){
              $(this).parents('tr').next().hide();
          }
      );
  });

  $(document).delegate('.add_box','click',function(){
        var data_str = '';
        var url = 'index.php?act=store_warehouse&op=add_box';
        eval('data_str = ' + $(this).attr('data-param'));
        ajax_form('ajax_add_box', '添加储位', url + '&house_id=' + data_str.house_id + '&inajax=1', '500');
       
    });
   $(document).delegate('.edit_box','click',function(){
        var data_str = '';
        var url = 'index.php?act=store_warehouse&op=edit_box';
        eval('data_str = ' + $(this).attr('data-param'));
        ajax_form('ajax_edit_box', '编辑储位', url + '&box_id=' + data_str.box_id + '&house_id='+data_str.house_id+'&inajax=1', '500');
       
    });
    $(document).delegate('.del_box','click',function(){
        var data_str = '';
        var url = 'index.php?act=store_warehouse&op=del_box';
        eval('data_str = ' + $(this).attr('data-param'));
        var _this = this;
        if(confirm('确定删除吗')){
            $.post(url,{box_id:data_str.box_id},function(res){
                if(res.state) {
                    $(_this).parent().remove();
                    showDialog('删除成功','succ');
                }else{
                    showDialog(res.msg);
                }
            },'json')
        }
       
    });
</script>