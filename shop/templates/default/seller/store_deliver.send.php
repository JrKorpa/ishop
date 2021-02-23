<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<style type="text/css">
.sticky .tabmenu { padding: 0;  position: relative; }
</style>
        <span class="fr mr5"> <a href="<?php echo urlShop('store_deliver', 'waybill_print', array('order_id' => $output['order_info']['order_id']));?>" class="ncbtn-mini" target="_blank" title="打印运单"/><i class="icon-print"></i>打印运单</a></span>
<div class="wrap">
  <div class="step-title"><em><?php echo $lang['store_deliver_first_step'];?></em><?php echo $lang['store_deliver_confirm_trade'];?></div>
  <form name="deliver_form" method="POST" id="deliver_form" action="index.php?act=store_deliver&op=send&order_id=<?php echo $_GET['order_id'];?>" onsubmit="ajaxpost('deliver_form', '', '', 'onerror');return false;">
    <input type="hidden" value="<?php echo getReferer();?>" name="ref_url">
    <input type="hidden" value="ok" name="form_submit">
    <input type="hidden" id="shipping_express_id" value="<?php echo $output['order_info']['shipping_express_id'];?>" name="shipping_express_id">
    <input type="hidden" value="<?php echo $output['order_info']['extend_order_common']['reciver_name'];?>" name="reciver_name" id="reciver_name">
    <input type="hidden" value="<?php echo $output['order_info']['extend_order_common']['reciver_info']['area'];?>" name="reciver_area" id="reciver_area">
    <input type="hidden" value="<?php echo $output['order_info']['extend_order_common']['reciver_info']['street'];?>" name="reciver_street" id="reciver_street">
    <input type="hidden" value="<?php echo $output['order_info']['extend_order_common']['reciver_info']['mob_phone'];?>" name="reciver_mob_phone" id="reciver_mob_phone">
    <input type="hidden" value="<?php echo $output['order_info']['extend_order_common']['reciver_info']['tel_phone'];?>" name="reciver_tel_phone" id="reciver_tel_phone">
    <input type="hidden" value="<?php echo $output['order_info']['extend_order_common']['reciver_info']['dlyp'];?>" name="reciver_dlyp" id="reciver_dlyp">
    <table class="ncsc-default-table order deliver">
      <tbody>
        <?php if (is_array($output['order_info']) and !empty($output['order_info'])) { ?>
        <tr>
          <td colspan="20" class="sep-row"></td>
        </tr>
        <tr>
          <th colspan="20"><a href="index.php?act=store_order&op=order_print&order_id=<?php echo $output['order_info']['order_id'];?>" target="_blank" class="fr" title="<?php echo $lang['store_show_order_printorder'];?>"/><i class="print-order"></i></a><span class="fr mr30"></span><span class="ml10"><?php echo $lang['store_order_order_sn'].$lang['nc_colon'];?><?php echo $output['order_info']['order_sn']; ?></span><span class="ml20"><?php echo $lang['store_order_add_time'].$lang['nc_colon'];?><em class="goods-time"><?php echo date("Y-m-d H:i:s",$output['order_info']['add_time']); ?></em></span>
        </tr>
        <tr>
		   <td colspan="20">
		       <table class="ncsc-default-table order">
					<thead>
					   <tr>
						<th>图片</th>
						<th>货品款号</th>
						<th>真实货号</th>
						<th>货品名称</th>
						<th>石重</th>
						<th>切工</th>
						<th>净度</th>
						<th>颜色</th>
						<th>证书号</th>
						<th>材质</th>
						<th>金色</th>
						<th>金重</th>
						<th>指圈</th>
						<th>数量</th>
						<th>赠品</th>
						<th>销账</th>
						<th>仓储货号</th>
					  </tr>	
					</thead>
					<tbody>
					<?php foreach($output['order_info']['extend_order_goods'] as $k => $g) { ?>
					   <tr>
					    <td><div class="pic-thumb"><img src="<?php echo $g['goods_image']; ?>" /></div></td>
						<td><?php echo $g['style_sn'];?></td>
						<td><?php echo $g['goods_itemid'];?><?php if($g['is_cpdz']==1){?><br/>(成品定制)<?php }?></td>
						<td><?php echo $g['goods_name'];?></td>
						<td><?php echo $g['carat'];?></td>
						<td><?php echo $g['cut'];?></td>
						<td><?php echo $g['clarity'];?></td>
						<td><?php echo $g['color'];?></td>
						<td><?php echo $g['cert_id'];?></td>
						<td><?php echo $g['caizhi'];?></td>
						<td><?php echo $g['jinse'];?></td>
						<td><?php echo $g['jinzhong'];?></td>
						<td><?php echo $g['zhiquan'];?></td>
						<td><?php echo $g['goods_num'];?></td>
						<td><?php echo $g['goods_type']==5?"是":"否";?></td>
						<td><?php echo $g['is_finance']?"是":"否";?></td>
						<td>
						<?php if($g['is_finance']==0){?>
						   无需销账
						<?php }else{?>
						<input type="input" class="text" placeholder="请输入发货货号" name="goods_item_id[<?php echo $g['rec_id'];?>]" value="<?php echo $g['goods_itemid'];?>"><?php }?>
						</td>	
						</tr>
					   <?php if(!empty($g['stone_list'])) {?>
					      <?php foreach($g['stone_list'] as $k2=>$g2){?>
						     <?php if(is_array($g2)){?> 
							<tr>
							<td><div class="pic-thumb"><img src="<?php echo $g2['goods_image']; ?>" /></div></td>
							<td><?php echo $g2['goods_sn'];?></td>
							<td><?php echo $g2['goods_id'];?></td>
							<td><?php echo $g2['goods_name'];?></td>
							<td><?php echo $g2['zuanshidaxiao'];?></td>
							<td><?php echo $g2['qiegong'];?></td>
							<td><?php echo $g2['jingdu'];?></td>
							<td><?php echo $g2['yanse'];?></td>
							<td><?php echo $g2['zhengshuhao'];?></td>
							<td><?php echo $g2['caizhi'];?></td>
							<td><?php echo $g2['jinse'];?></td>
							<td><?php echo $g2['jinzhong'];?></td>
							<td><?php echo $g2['shoucun'];?></td>
							<td>1</td>
							<td>否</td>
							<td>是</td>
							<td><input type="input" class="text" name="stone_goods_id[<?php echo $g['rec_id'];?>][]" value="<?php echo $g2['goods_id'];?>"></td>
							</tr>
							<?php }else{?>
							<tr><td>&nbsp;</td><td colspan="15"><font color="red"><?php echo $g2?></font></td></tr>
							<?php }?>						 
						<?php }?>
					   				   
					   <?php }?>					 						
										 
					<?php }?>  
				  </tbody>	
			   </table>
		   
		   </td>
		</tr>
        <tr>
          <td class="bdl bdr order-info w500" rowspan="20"><dl>
              <dt><?php echo $lang['store_deliver_shipping_amount'].$lang['nc_colon'];?></dt>
              <dd>
                <?php if (!empty($output['order_info']['shipping_fee']) && $output['order_info']['shipping_fee'] != '0.00'){?>
                <?php echo $output['order_info']['shipping_fee'];?>
                <?php }else{?>
                <?php echo $lang['nc_common_shipping_free'];?>
                <?php }?>
              </dd>
            </dl>
            <dl>
              <dt><?php echo $lang['store_deliver_forget'].$lang['nc_colon'];?></dt>
              <dd>
                <textarea name="deliver_explain" cols="100" rows="2" class="w400 tip-t" title="<?php echo $lang['store_deliver_forget_tips'];?>"><?php echo $output['order_info']['extend_order_common']['deliver_explain'];?></textarea>
              </dd>
            </dl></td>
        </tr>

        <tr>
          <td colspan="20" class="tl bdl bdr" style="padding:8px" id="address"><strong class="fl"><?php echo $lang['store_deliver_buyer_adress'].$lang['nc_colon'];?></strong><span id="buyer_address_span"><?php echo $output['order_info']['extend_order_common']['reciver_name'];?>&nbsp;<?php echo $output['order_info']['extend_order_common']['reciver_info']['phone'];?>&nbsp;<?php echo $output['order_info']['extend_order_common']['reciver_info']['address'];?></span><?php echo $output['order_info']['extend_order_common']['reciver_info']['dlyp'] ? '[自提服务站]' : '';?>
          <a href="javascript:void(0)" nc_type="dialog" dialog_title="<?php echo $lang['store_deliver_buyer_adress'];?>" dialog_id="edit_buyer_address" uri="index.php?act=store_deliver&op=buyer_address_edit&order_id=<?php echo $output['order_info']['order_id'];?>" dialog_width="550" class="ncbtn-mini fr"><i class="icon-edit"></i><?php echo $lang['nc_edit'];?></a></td>
        </tr>
        <?php } else { ?>
        <tr>
          <td colspan="20" class="norecord"><i>&nbsp;</i><span><?php echo $lang['no_record'];?></span></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
    <div class="step-title mt30"><em><?php echo $lang['store_deliver_second_step'];?></em><?php echo $lang['store_deliver_confirm_daddress'];?></div>
    <div class="deliver-sell-info"><strong class="fl"><?php echo $lang['store_deliver_my_daddress'].$lang['nc_colon'];?></strong>
      <a href="javascript:void(0);" onclick="ajax_form('modfiy_daddress', '<?php echo $lang['store_deliver_select_daddress'];?>', 'index.php?act=store_deliver&op=send_address_select&order_id=<?php echo $output['order_info']['order_id'];?>', 640,0);" class="ncbtn-mini fr"><i class="icon-edit"></i><?php echo $lang['nc_edit'];?></a> <span id="seller_address_span">
      <?php if (empty($output['daddress_info'])) {?>
      <?php echo $lang['store_deliver_none_set'];?>
      <?php } else { ?>
      <?php echo $output['daddress_info']['seller_name'];?>&nbsp;<?php echo $output['daddress_info']['telphone'];?>&nbsp;<?php echo $output['daddress_info']['area_info'];?>&nbsp;<?php echo $output['daddress_info']['address'];?>
      <?php } ?>
      </span>
    </div>
    <input type="hidden" name="daddress_id" id="daddress_id" value="<?php echo $output['daddress_info']['address_id'];?>">
    <div class="step-title mt30"><em><?php echo $lang['store_deliver_third_step'];?></em><?php echo $lang['store_deliver_express_select'];?></div>
    <div class="alert alert-success"><?php echo $lang['store_deliver_express_note'];?></div>
    <div class="tabmenu">
      <ul class="tab pngFix">
	  <?php if ($output['order_info']['extend_order_common']['reciver_info']['dlyp'] == '') {?>
        <li id="eli2" class="active"><a href="javascript:void(0);" onclick="etab(2)"><?php echo $lang['store_deliver_express_wx'];?></a></li>
        <?php } ?>
        <li id="eli1" class="normal"><a href="javascript:void(0);" onclick="etab(1)"><?php echo $lang['store_deliver_express_zx'];?></a></li>
        
      </ul>
    </div>
    <table class="ncsc-default-table order" id="texpress1" style="display:none">
      <tbody>
        <tr>
          <td class="bdl w150"><?php echo $lang['store_deliver_company_name'];?></td>
          <td class="w250"><?php echo $lang['store_deliver_shipping_code'];?></td>
          <td class="tc"><?php echo $lang['store_deliver_bforget'];?></td>
          <td class="bdr w90 tc"><?php echo $lang['nc_common_button_operate'];?></td>
        </tr>
        <?php if (is_array($output['my_express_list']) && !empty($output['my_express_list'])){?>
        <?php foreach ($output['my_express_list'] as $k=>$v){?>
        <?php if (!isset($output['express_list'][$v])) continue;?>
        <tr>
          <td class="bdl"><?php echo $output['express_list'][$v]['e_name'];?></td>
          <td class="bdl"><input name="shipping_code" type="text" class="text w200 tip-r" title="<?php echo $lang['store_deliver_shipping_code_tips'];?>" maxlength="20" nc_type='eb' nc_value="<?php echo $v;?>" /></td>
          <td class="bdl gray" nc_value="<?php echo $v;?>"></td>
          <td class="bdl bdr tc"><a nc_type='eb' nc_value="<?php echo $v;?>" href="javascript:void(0);" class="ncbtn"><?php echo $lang['nc_common_button_confirm'];?></a></td>
        </tr>
        <?php }?>
        <?php }?>
      </tbody>
    </table>
    <table class="ncsc-default-table order" id="texpress2" >
      <tbody>
        <tr>
          <td colspan="2"></td>
        </tr>
        <tr>
          <td class="bdl tr"><?php echo $lang['store_deliver_no_deliver_tips'];?></td>
          <td class="bdr tl w400">&emsp;<a nc_type='eb' nc_value="e1000" href="javascript:void(0);" class="ncbtn ncbtn-mint"><?php echo $lang['nc_common_button_confirm'];?></a></td>
        </tr>
        <tr>
          <td colspan="2"></td>
        </tr>
      </tbody>
    </table>
  </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js"></script>
<script charset="utf-8" type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<script type="text/javascript">
function etab(t){
	if (t==1){
		$('#eli1').removeClass('normal').addClass('active');
		$('#eli2').removeClass('active').addClass('normal');
		$('#texpress1').css('display','');
		$('#texpress2').css('display','none');
	}else{
		$('#eli1').removeClass('active').addClass('normal');
		$('#eli2').removeClass('normal').addClass('active');
		$('#texpress1').css('display','none');
		$('#texpress2').css('display','');
	}
}
$(function(){
	//表单提示
	$('.tip-t').poshytip({
		className: 'tip-yellowsimple',
		showOn: 'focus',
		alignTo: 'target',
		alignX: 'center',
		alignY: 'top',
		offsetX: 0,
		offsetY: 2,
		allowTipHover: false
	});
	$('.tip-r').poshytip({
		className: 'tip-yellowsimple',
		showOn: 'focus',
		alignTo: 'target',
		alignX: 'right',
		alignY: 'center',
		offsetX: -50,
		offsetY: 0,
		allowTipHover: false
	});
	$('a[nc_type="eb"]').on('click',function(){
		if ($('input[nc_value="'+$(this).attr('nc_value')+'"]').val() == ''){
			showDialog('<?php echo $lang['store_deliver_shipping_code_pl'];?>', 'error','','','','','','','','',2);return false;
		}
		$('input[nc_type="eb"]').attr('disabled',true);
		$('input[nc_value="'+$(this).attr('nc_value')+'"]').attr('disabled',false);
		$('#shipping_express_id').val($(this).attr('nc_value'));
		$('#deliver_form').submit();
	});

    $('#add_time_from').datepicker({dateFormat: 'yy-mm-dd'});
    $('#add_time_to').datepicker({dateFormat: 'yy-mm-dd'});
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
    <?php if ($output['order_info']['shipping_code'] != ''){?>
    	$('input[nc_value="<?php echo $output['order_info']['extend_order_common']['shipping_express_id'];?>"]').val('<?php echo $output['order_info']['shipping_code'];?>');
    	$('td[nc_value="<?php echo $output['order_info']['extend_order_common']['shipping_express_id'];?>"]').html('<?php echo $output['order_info']['extend_order_common']['deliver_explain'];?>');
    <?php } ?>

    $('#my_address_add').click(function(){
		ajax_form('my_address_add', '<?php echo $lang['store_deliver_add_daddress'];?>' , 'index.php?act=store_deliver&op=send_address_edit', 550,0);
    });
});
</script>
