<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/thickbox/thickbox.js?v=1.1" charset="utf-8"></script>
<link href="<?php echo RESOURCE_SITE_URL;?>/js/thickbox/thickbox.css" rel="stylesheet" />
<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<form method="get" action="index.php" target="_self">
  <table class="search-form">
    <input type="hidden" name="act" value="store_order" />
    <input type="hidden" name="op" value="index" />
    <?php if ($_GET['state_type']) { ?>
    <input type="hidden" name="state_type" value="<?php echo $_GET['state_type']; ?>" />
    <?php } ?>
    <tr>
      <th>制单时间</th>
      <td class="w240"><input type="text" class="text w70" name="query_start_date" id="query_start_date" value="<?php echo $_GET['query_start_date']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label>&nbsp;&#8211;&nbsp;<input id="query_end_date" class="text w70" type="text" name="query_end_date" value="<?php echo $_GET['query_end_date']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label></td>
     
	  <th>客户姓名</th>
      <td class="w160"><input type="text" class="text w150" name="buyer_name" value="<?php echo $_GET['buyer_name']; ?>" /></td>
      <th>手机号</th>
      <td class="w160"><input type="text" class="text w150" name="buyer_phone" value="<?php echo $_GET['buyer_phone']; ?>" /></td>
      <!--<th>销售渠道</th>
      <td class="w160">
          <select  class="select w160">
              <option>肄业</option>
              <option>官网</option>
          </select>
      </td>-->
    </tr>
      <tr>
	   <th>点款时间</th>
       <td class="w240"><input type="text" class="text w70" name="pay_start_date" id="pay_start_date" value="<?php echo $_GET['pay_start_date']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label>&nbsp;&#8211;&nbsp;<input id="pay_end_date" class="text w70" type="text" name="pay_end_date" value="<?php echo $_GET['pay_end_date']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label></td>
       <th>订单号</th>
       <td class="w160"><input type="text" class="text w160" name="order_sn" value="<?php echo $_GET['order_sn']; ?>" /></td>  
          <!--<th>生产状态</th>
          <td class="w100">
              <select class="select w160">
                  <option>待生产</option>
                  <option>生产中</option>
                  <option>已完成</option>
              </select>
          </td> -->
          
          <th>制单人</th>
          <td class="w160"><input type="text" class="text w150" name="seller_name" value="<?php echo $_GET['seller_name']; ?>" /></td>
        
	 </tr>
      <tr>
	    <th>发货时间</th>
       <td class="w240"><input type="text" class="text w70" name="finnshed_start_date" id="finnshed_start_date" value="<?php echo $_GET['finnshed_start_date']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label>&nbsp;&#8211;&nbsp;<input id="finnshed_end_date" class="text w70" type="text" name="finnshed_end_date" value="<?php echo $_GET['finnshed_end_date']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label></td>     
	  
	     <th>订单类型</th>
          <td class="w160">
              <select class="select w160" name="is_xianhuo">
                  <option value="">--请选择--</option>
                  <option value="0" <?php echo isset($_GET['is_xianhuo'])&&$_GET['is_xianhuo']=='0'?"selected":""; ?>>定制单</option>
                  <option value="1" <?php echo $_GET['is_xianhuo']=='1'?"selected":""; ?>>现货单</option>
              </select>
          </td>
          <th>支付状态</th>
          <td class="w160">
              <select  class="select w160" name="pay_status">
                  <option value="">--请选择--</option>
                  <option value="1"  <?php echo $_GET['pay_status']==1?"selected":""; ?>>待支付</option>
                  <option value="2"  <?php echo $_GET['pay_status']==2?"selected":""; ?>>支付定金</option>
                  <option value="3"  <?php echo $_GET['pay_status']==3?"selected":""; ?>>已全款</option>
              </select>
          </td>
         
          <!--<th>客户来源</th>
          <td class="w160">
              <select class="select w160">
                  <option>线上</option>
                  <option>线下</option>
              </select>
          </td>
          -->
          
      </tr>
	  <tr>
	 <th>货号/款号</th>
     <td class="w100"><input type="text" class="text w150" name="goods_id" value="<?php echo $_GET['goods_id']; ?>" /></td>
     <th>是否赠品</th>
          <td class="w160">
              <select class="select w160" name="is_zp">
                  <option value="">--请选择--</option>                 
                  <option value="1" <?php echo $_GET['is_zp']=='1'?"selected":""; ?>>是</option>
				  <option value="0" <?php echo isset($_GET['is_zp'])&&$_GET['is_zp']=='0'?"selected":""; ?>>否</option>
              </select>
          </td>
	  <td class="w70 tc" colspan="2">
            <label class="submit-border">
                  <input type="submit" class="submit" value="<?php echo $lang['store_order_search'];?>" />
            </label>
      </td>
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
      <th class="w80">销售渠道</th>
      <th class="w50"><?php echo $lang['store_order_buyer'];?></th>
      <th class="w50">手机号</th>
      <th class="w100"><?php echo $lang['store_order_sum'];?></th>
      <th class="w60">订单类型</th>
      <th class="w60">支付状态</th>
      <th class="w60">订单状态</th>
      <th class="w100">交易操作</th>
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
        </span> <span><?php echo $lang['store_order_add_time'].$lang['nc_colon'];?><em class="goods-time"><?php echo date("Y-m-d H:i:s",$order['add_time']); ?></em></span>
          <span>制单人：<em class="goods-time"><?php echo $order['seller_name']; ?></em></span>
        <?php if ($order['chain_id']) { ?>
        <span>取货方式：门店自提</span>
        <?php } ?>
        <span class="fr mr5"> <a href="index.php?act=store_order&op=print_order&print_type=1&order_id=<?php echo $order_id;?>" class="ncbtn-mini" target="_blank" title="打印销售单"/><i class="icon-print"></i>打印销售单</a>&nbsp;&nbsp;<a href="index.php?act=store_order&op=print_order&print_type=2&order_id=<?php echo $order_id;?>" class="ncbtn-mini" target="_blank" title="打印定制单"/><i class="icon-print"></i>打印定制单</a></span>
        <!--<span class="fr mr5"> <a href="index.php?act=store_order&op=order_print&order_id=<?php echo $order_id;?>" class="ncbtn-mini" target="_blank" title="打印发货单"/><i class="icon-print"></i>打印发货单</a></span>--> </th>
    </tr>
    <?php $i = 0;?>
    <?php 
	    if(is_array($order['goods_list'])){
        foreach($order['goods_list'] as $k => $goods) { ?>
    <?php $i++;?>
    <tr>
      <td class="bdl"></td>
      <td class="w70"><div class="ncsc-goods-thumb"><a href="<?php echo $goods['goods_url'];?>" target="_blank"><img src="<?php echo $goods['goods_image'];?>" onMouseOver="toolTip('<img src=<?php echo $goods['goods_image'];?>>')" onMouseOut="toolTip()"/></a></div></td>
      <td class="tl"><dl class="goods-name">
          <dt <?php if($goods['is_return'] == 1){ ?>style="text-decoration:line-through;"<?php }?>><a target="_blank" href="<?php echo $goods['goods_url'];?>"><?php echo $goods['goods_name']; ?></a></dt>
          <?php if ($goods['goods_spec']){ ?>
          <dd><?php echo $goods['goods_spec'];?></dd>
          <?php } ?>
          <!-- S消费者保障服务 -->
          
          <?php if($goods["contractlist"]){?>
          <dd class="goods-cti mt5">
            <?php foreach($goods["contractlist"] as $gcitem_v){?>
            <span <?php if($gcitem_v['cti_descurl']){ ?>onclick="window.open('<?php echo $gcitem_v['cti_descurl'];?>');" style="cursor: pointer;"<?php }?> title="<?php echo $gcitem_v['cti_name']; ?>"> <img src="<?php echo $gcitem_v['cti_icon_url_60'];?>"/> </span>
            <?php }?>
          </dd>
          <?php }?>
          
          <!-- E消费者保障服务 -->
        </dl></td>
      <td><p><?php echo ncPriceFormat($goods['goods_price']); ?></p>
        <?php if (!empty($goods['goods_type_cn'])){ ?>
        <span class="sale-type"><?php echo $goods['goods_type_cn'];?></span>
        <?php } ?></td>
      <td><?php echo $goods['goods_num']; ?></td>
      
      <!-- S 合并TD -->
      <?php if (($order['goods_count'] > 1 && $k ==0) || ($order['goods_count']) == 1){ ?>
      <td class="bdl" rowspan="<?php echo $order['goods_count'];?>">
            <?php echo $order['store_name']; ?>
      </td>
          <!--买家信息-->
      <td class="bdl" rowspan="<?php echo $order['goods_count'];?>"><div class="buyer"><?php echo $order['buyer_name'];?>
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
                <dd><?php echo $order['extend_order_common']['reciver_name'];?></dd>
              </dl>
              <dl>
                <dt><?php echo $lang['store_order_phone'].$lang['nc_colon'];?></dt>
                <dd><?php echo $order['extend_order_common']['reciver_info']['phone'];?></dd>
              </dl>
              <dl>
                <dt>地址<?php echo $lang['nc_colon'];?></dt>
                <dd><?php echo $order['extend_order_common']['reciver_info']['address'];?></dd>
              </dl>
            </div>
          </div>
        </div></td>
          <!--手机号-->
      <td class="bdl" rowspan="<?php echo $order['goods_count'];?>">
          <?php echo $order['buyer_phone'];?>
      </td>
          <!--订单金额-->
      <td class="bdl" rowspan="<?php echo $order['goods_count'];?>"><p class="ncsc-order-amount"><?php echo $order['order_amount']; ?></p>
        <p class="goods-freight">
          <?php if ($order['shipping_fee'] > 0){?>
          (<?php echo $lang['store_show_order_shipping_han']?>运费<?php echo $order['shipping_fee'];?>)
          <?php }else{?>
          <?php echo $lang['nc_common_shipping_free'];?>
          <?php }?>
        </p>
        <p class="goods-pay" title="<?php echo $lang['store_order_pay_method'].$lang['nc_colon'];?><?php echo $order['payment_name']; ?>"><?php echo $order['payment_name']; ?></p></td>
       <!--订单类型-->
       <td class="bdl" rowspan="<?php echo $order['goods_count'];?>">
          <?php echo $order['is_xianhuo_name'];?>
      </td>
     <!--支付状态-->
      <td class="bdl" rowspan="<?php echo $order['goods_count'];?>">
          <?php echo $order['pay_status_name'];?>
      </td>
      <td class="bdl bdr" rowspan="<?php echo $order['goods_count'];?>"><p><?php echo $order['state_desc']; ?>
          <?php if($order['evaluation_time']) { ?>
          <br/>
          <?php echo $lang['store_order_evaluated'];?>
          <?php } ?>
        </p>
        
        <!-- 订单查看 -->
        
        <p><a href="index.php?act=store_order&op=show_order&order_id=<?php echo $order_id;?>" target="_blank"><?php echo $lang['store_order_view_order'];?></a></p>
        
        <!-- 物流跟踪 -->
        
        <p>
          <?php if ($order['if_deliver']) { ?>
          <a href='index.php?act=store_deliver&op=search_deliver&order_sn=<?php echo $order['order_sn']; ?>'><?php echo $lang['store_order_show_deliver'];?></a>
          <?php } ?>
        </p></td>
      
      <!-- 取消订单 -->
      <td class="bdl bdr" rowspan="<?php echo $order['goods_count'];?>"><?php if($order['if_store_cancel']) { ?>
        <p><a href="javascript:void(0)" class="ncbtn ncbtn-grapefruit mt5" nc_type="dialog" uri="index.php?act=store_order&op=change_state&state_type=order_cancel&order_sn=<?php echo $order['order_sn']; ?>&order_id=<?php echo $order['order_id']; ?>" dialog_title="<?php echo $lang['store_order_cancel_order'];?>" dialog_id="seller_order_cancel_order" dialog_width="400" id="order<?php echo $order['order_id']; ?>_action_cancel" /><i class="icon-remove-circle"></i><?php echo $lang['store_order_cancel_order'];?></a></p>
        <?php } ?>
        
        <!-- 修改运费价格 -->
        
        <?php if ($order['if_modify_price']) { ?>
        <p><a href="javascript:void(0)" class="ncbtn-mini ncbtn-bittersweet mt10" uri="index.php?act=store_order&op=change_state&state_type=modify_price&order_sn=<?php echo $order['order_sn']; ?>&order_id=<?php echo $order['order_id']; ?>" dialog_width="480" dialog_title="<?php echo $lang['store_order_modify_price'];?>" nc_type="dialog"  dialog_id="seller_order_adjust_fee" id="order<?php echo $order['order_id']; ?>_action_adjust_fee" /><i class="icon-pencil"></i>修改运费</a></p>
        <?php }?>
        <!--修改订单价格-->
        <!--<?php if ($order['if_spay_price']) { ?>
        <p><a href="javascript:void(0)" class="ncbtn-mini ncbtn-bluejeansjeans mt10" uri="index.php?act=store_order&op=change_state&state_type=spay_price&order_sn=<?php echo $order['order_sn']; ?>&order_id=<?php echo $order['order_id']; ?>" dialog_width="480" dialog_title="<?php echo $lang['store_order_modify_price'];?>" nc_type="dialog"  dialog_id="seller_order_adjust_fee" id="order<?php echo $order['order_id']; ?>_action_adjust_fee" /><i class="icon-pencil"></i>修改价格</a></p>
		<?php }?>-->
      <!--后台新增订单操作按钮-->
      <?php  if($order["order_state"] > ORDER_STATE_TO_CONFIRM && $order["pay_status"]< ORDER_PAY_FULL): ?>
          <!--付款-->
          <p><a href="javascript:void(0)" class="ncbtn-mini ncbtn-bluejeansjeans mt10"
                uri="index.php?act=store_order&op=change_state&state_type=order_pay&order_sn=<?php echo $order['order_sn']; ?>&order_id=<?php echo $order['order_id']; ?>"
                dialog_width="480" dialog_title="订单收款" nc_type="dialog"
                dialog_id="seller_order_pay" id="order<?php echo $order['order_id']; ?>_action_order_pay" />
              <i class="icon-money"></i>订单收款</a>
          </p>
      <?php endif; ?>
      <?php  if($order["if_check"]): ?>
          <!--审核-->
          <p><a href="javascript:void(0)" class="ncbtn-mini ncbtn-bluejeansjeans mt10"
                uri="index.php?act=store_order&op=change_state&state_type=order_audit&order_sn=<?php echo $order['order_sn']; ?>&order_id=<?php echo $order['order_id']; ?>"
                dialog_width="400" dialog_title="订单审核" nc_type="dialog"
                dialog_id="seller_order_order_aduit" id="order<?php echo $order['order_id']; ?>_action_order_pass_aduit"/>
              <i class="icon-ok-circle"></i>订单审核</a>
          </p>
      <?php endif; ?>
	  
	  <?php  if($order["if_buchan"]): ?>
          <!--允许布产-->
          <p><a href="javascript:void(0)" class="ncbtn-mini ncbtn-bluejeansjeans mt10"
                uri="index.php?act=store_order&op=change_state&state_type=order_bc&order_sn=<?php echo $order['order_sn']; ?>&order_id=<?php echo $order['order_id']; ?>"
                dialog_width="400" dialog_title="允许布产" nc_type="dialog"
                dialog_id="seller_order_order_bc" id="order<?php echo $order['order_id']; ?>_action_order_pass_bc"/>
              <i class="icon-ok-circle"></i>允许布产</a>
          </p>
      <?php endif; ?>
	  
      <!--查看日志-->
      <p><a href="index.php?act=store_order&op=change_state&state_type=view_log&order_sn=<?php echo $order['order_sn']; ?>&order_id=<?php echo $order['order_id']; ?>&keepThis=true&TB_iframe=true&height=500&width=800" class="ncbtn-mini ncbtn-bluejeansjeans mt10 thickbox" title="查看日志"/>
          <i class="icon-search"></i>查看日志</a>
      </p>
        <!-- 发货 -->
        
        <?php if ($order['if_store_send']) { ?>
        <p><a class="ncbtn ncbtn-mint mt10" href="index.php?act=store_deliver&op=send&order_id=<?php echo $order['order_id']; ?>"/><i class="icon-truck"></i><?php echo $lang['store_order_send'];?></a></p>
        <?php } ?>
		<?php if($order['if_exchange']) { ?>
		<p><a class="ncbtn ncbtn-mint mt10" href="index.php?act=store_goods_exchange&op=index&order_sn=<?php echo $order['order_sn']; ?>"><i class="icon-truck"></i>订单换货</a></p>
		<?php } ?>
        
        <!-- 锁定 -->
        
        <?php if ($order['if_lock']) {?>
        <p><?php echo '退款退货中';?></p>
        <?php }?></td>
      <?php } ?>
      <!-- E 合并TD --> 
    </tr>
    
    <!-- S 赠品列表 -->
    <?php if (!empty($order['zengpin_list']) && $i == count($order['goods_list'])) { ?>
    <tr>
      <td class="bdl"></td>
      <td colspan="4" class="tl"><div class="ncsc-goods-gift">赠品：
          <ul>
            <?php foreach ($order['zengpin_list'] as $zengpin_info) { ?>
            <li> <a title="赠品：<?php echo $zengpin_info['goods_name'];?> * <?php echo $zengpin_info['goods_num'];?>" href="<?php echo $zengpin_info['goods_url'];?>" target="_blank"><img src="<?php echo $zengpin_info['image_60_url'];?>" onMouseOver="toolTip('<img src=<?php echo $zengpin_info['image_240_url'];?>>')" onMouseOut="toolTip()"/></a></li>
          </ul>
          <?php } ?>
        </div></td>
    </tr>
    <?php } ?>
    <!-- E 赠品列表 --> 
    
    <!-- S 预定时段 -->
    <?php if ($order['order_type'] == 2 && $i == count($order['goods_list'])) { ?>
    <?php if (is_array($order['book_list'])) { ?>
    <?php foreach($order['book_list'] as $book_info) {?>
    <tr>
      <td class="bdl"></td>
      <td colspan="2"><?php echo $book_info['book_step'];?></td>
      <td colspan="2"><?php echo $book_info['book_amount'].$book_info['book_amount_ext'];?></td>
      <td colspan="2"><?php echo $book_info['book_state'];?></td>
      <td class="bdr" colspan="2"></td>
    </tr>
    <?php } ?>
    <?php } ?>
    <?php } ?>
    <!-- E 预定时段 -->
    
    <?php } } ?>
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
	$('#pay_start_date').datepicker({dateFormat: 'yy-mm-dd'});
    $('#pay_end_date').datepicker({dateFormat: 'yy-mm-dd'});
	$('#finnshed_start_date').datepicker({dateFormat: 'yy-mm-dd'});
    $('#finnshed_end_date').datepicker({dateFormat: 'yy-mm-dd'});
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
