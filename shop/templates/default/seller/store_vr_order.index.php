<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>

<div class="tabmenu">
  <?php include template('layout/submenu');?>
  <a href="<?php echo urlShop('store_vr_order', 'exchange');?>" class="ncbtn ncbtn-bittersweet"><i class="icon-edit"></i>兑换兑换码</a> </div>
<form method="get" action="index.php" target="_self">
  <table class="search-form">
    <input type="hidden" name="act" value="store_vr_order" />
    <input type="hidden" name="op" value="index" />
    <?php if ($_GET['state_type']) { ?>
    <input type="hidden" name="state_type" value="<?php echo $_GET['state_type']; ?>" />
    <?php } ?>
    <tr>
      <td>&nbsp;</td>
      <?php if ($_GET['state_type'] == 'store_order') { ?>
      <td><input type="checkbox" id="skip_off" value="1" <?php echo $_GET['skip_off'] == 1 ? 'checked="checked"' : null;?>  name="skip_off">
        <label for="skip_off">不显示已关闭的订单</label></td>
      <?php } ?>
      <th><?php echo $lang['store_order_add_time'];?></th>
      <td class="w240"><input type="text" class="text w70" name="query_start_date" id="query_start_date" value="<?php echo $_GET['query_start_date']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label>&nbsp;&#8211;&nbsp;<input id="query_end_date" class="text w70" type="text" name="query_end_date" value="<?php echo $_GET['query_end_date']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label></td>
      <th><?php echo $lang['store_order_buyer'];?></th>
      <td class="w100"><input type="text" class="text w80" name="buyer_name" value="<?php echo $_GET['buyer_name']; ?>" /></td>
      <th><?php echo $lang['store_order_order_sn'];?></th>
      <td class="w160"><input type="text" class="text w150" name="order_sn" value="<?php echo $_GET['order_sn']; ?>" /></td>
      <td class="w70 tc"><label class="submit-border">
          <input type="submit" class="submit" value="<?php echo $lang['store_order_search'];?>" />
        </label></td>
    </tr>
  </table>
</form>
<table class="ncsc-default-table order">
  <thead>
    <tr>
      <th class="w10"></th>
      <th colspan="2"><?php echo $lang['store_order_goods_detail'];?></th>
      <th class="w100"><?php echo $lang['store_order_goods_single_price'];?></th>
      <th class="w40"><?php echo $lang['store_show_order_amount'];?></th>
      <th class="w110"><?php echo $lang['store_order_buyer'];?></th>
      <th class="w120"><?php echo $lang['store_order_sum'];?></th>
      <th class="w100">交易状态</th>
      <th class="w150">交易操作</th>
    </tr>
  </thead>
  <?php if (is_array($output['order_list']) and !empty($output['order_list'])) { ?>
  <?php foreach($output['order_list'] as $order_id => $order) { ?>
  <tbody>
    <tr>
      <td colspan="20" class="sep-row"></td>
    </tr>
    <tr>
      <th colspan="20"><span class="ml10"><?php echo $lang['store_order_order_sn'].$lang['nc_colon'];?><em><?php echo $order['order_sn']; ?></em>
        <?php if ($order['order_from'] == 2){?>
        <i class="icon-mobile-phone"></i>
        <?php }?>
        </span> <span><?php echo $lang['store_order_add_time'].$lang['nc_colon'];?><em class="goods-time"><?php echo date("Y-m-d H:i:s",$order['add_time']); ?></em></span> </th>
    </tr>
    <tr>
      <td class="bdl"></td>
      <td class="w70"><div class="ncsc-goods-thumb"><a href="<?php echo urlShop('goods','index',array('goods_id'=>$order['goods_id']));?>" target="_blank"><img src="<?php echo thumb($order,60);?>" onMouseOver="toolTip('<img src=<?php echo thumb($order,240);?>>')" onMouseOut="toolTip()"/></a></div></td>
      <td class="tl"><dl class="goods-name">
          <dt><a target="_blank" href="<?php echo urlShop('goods','index',array('goods_id'=>$order['goods_id']));?>"><?php echo $order['goods_name']; ?></a><a target="_blank" class="blue" href="<?php echo urlShop('vr_snapshot', 'index', array('order_id' => $order['order_id']));?>">[交易快照]</a></dt>
          <?php if ($order['goods_spec']) { ?>
          <?php echo $order['goods_spec'];?>
          <?php } ?>
          <!-- S消费者保障服务 -->
          
          <?php if($order["contractlist"]){?>
          <dd class="goods-cti mt5">
            <?php foreach($order["contractlist"] as $gcitem_v){?>
            <span <?php if($gcitem_v['cti_descurl']){ ?>onclick="window.open('<?php echo $gcitem_v['cti_descurl'];?>');" style="cursor: pointer;"<?php }?> title="<?php echo $gcitem_v['cti_name']; ?>"> <img src="<?php echo $gcitem_v['cti_icon_url_60'];?>"/> </span>
            <?php }?>
          </dd>
          <?php }?>
          
          <!-- E消费者保障服务 -->
        </dl></td>
      <td><p><?php echo ncPriceFormat($order['goods_price']); ?></p>
        <?php if ($order['order_promotion_type'] == 1) { ?>
        <span class="sale-type">抢购活动</span>
        <?php } ?></td>
      <td><?php echo $order['goods_num']; ?></td>
      <td class="bdl"><div class="buyer"><?php echo $order['buyer_name'];?>
          <p member_id="<?php echo $order['buyer_id'];?>">
            <?php if(!empty($order['extend_member']['member_qq'])){?>
            <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $order['extend_member']['member_qq'];?>&site=qq&menu=yes" title="QQ: <?php echo $order['extend_member']['member_qq'];?>"><img border="0" src="http://wpa.qq.com/pa?p=2:<?php echo $order['extend_member']['member_qq'];?>:52" style=" vertical-align: middle;"/></a>
            <?php }?>
            <?php if(!empty($order['extend_member']['member_ww'])){?>
            <a target="_blank" href="http://amos.im.alisoft.com/msg.aw?v=2&uid=<?php echo $order['extend_member']['member_ww'];?>&site=cntaobao&s=2&charset=<?php echo CHARSET;?>" ><img border="0" src="http://amos.im.alisoft.com/online.aw?v=2&uid=<?php echo $order['extend_member']['member_ww'];?>&site=cntaobao&s=2&charset=<?php echo CHARSET;?>" alt="Wang Wang" style=" vertical-align: middle;" /></a>
            <?php }?>
          </p>
          <div class="buyer-info"> <em></em>
            <div class="con">
              <h3><i></i><span><?php echo $lang['store_order_buyer_info'];?></span></h3>
              <dl>
                <dt><?php echo $lang['store_order_receiver'].$lang['nc_colon'];?></dt>
                <dd><?php echo $order['buyer_name'];?></dd>
              </dl>
              <dl>
                <dt><?php echo $lang['store_order_phone'].$lang['nc_colon'];?></dt>
                <dd><?php echo $order['buyer_phone'];?></dd>
              </dl>
            </div>
          </div>
        </div></td>
      <td class="bdl"><p class="ncsc-order-amount"><?php echo $order['order_amount']; ?></p>
        <p class="goods-pay" title="<?php echo $lang['store_order_pay_method'].$lang['nc_colon'];?><?php echo $order['payment_name']; ?>"><?php echo $order['payment_name']; ?></p></td>
      <td class="bdl bdr"><p><?php echo $order['state_desc']; ?> </p>
        
        <!-- 订单查看 -->
        
        <p><a href="index.php?act=store_vr_order&op=show_order&order_id=<?php echo $order['order_id'];?>" target="_blank"><?php echo $lang['store_order_view_order'];?></a></p></td>
      <td class="bdl bdr"><!-- 取消订单 -->
        
        <?php if($order['if_cancel']) { ?>
        <p><a href="javascript:void(0)" class="ncbtn ncbtn-grapefruit mt5" nc_type="dialog" uri="index.php?act=store_vr_order&op=change_state&state_type=order_cancel&order_sn=<?php echo $order['order_sn']; ?>&order_id=<?php echo $order['order_id']; ?>" dialog_title="<?php echo $lang['store_order_cancel_order'];?>" dialog_id="seller_order_cancel_order" dialog_width="400" id="order<?php echo $order['order_id']; ?>_action_cancel" /><i class="icon-remove-circle"></i><?php echo $lang['store_order_cancel_order'];?></a></p>
        <?php } ?>
    </tr>
    <?php } } else { ?>
    <tr>
      <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
    </tr>
    <?php } ?>
  </tbody>
  <tfoot>
    <?php if (is_array($output['order_list']) and !empty($output['order_list'])) { ?>
    <tr>
      <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
    </tr>
    <?php } ?>
  </tfoot>
</table>
<script charset="utf-8" type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<script type="text/javascript">
$(function(){
    $('#query_start_date').datepicker({dateFormat: 'yy-mm-dd'});
    $('#query_end_date').datepicker({dateFormat: 'yy-mm-dd'});
    $('.checkall_s').click(function(){
        var if_check = $(this).attr('checked');
        $('.checkitem').each(function(){
            if(!this.disabled)
            {
                $(this).attr('checked', if_check);
            }
        });
        $('.checkall_s').attr('checked', if_check);
    });
    $('#skip_off').click(function(){
        url = location.href.replace(/&skip_off=\d*/g,'');
        window.location.href = url + '&skip_off=' + ($('#skip_off').attr('checked') ? '1' : '0');
    });
});
</script> 
