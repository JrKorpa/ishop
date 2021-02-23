<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>订单详情</title>
<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/dialog/dialog.js" id="dialog_js" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<link rel="stylesheet" type="text/css" href="<?php echo BASE_SITE_URL;?>/shop/templates/default/css/seller_center.css"  />
<link href="<?php echo BASE_SITE_URL;?>/shop/templates/default/css/base.css" rel="stylesheet" type="text/css">
<link href="<?php echo ADMIN_SITE_URL;?>/templates/default/css/index.css?1" rel="stylesheet" type="text/css">
<link href="<?php echo BASE_SITE_URL;?>/data/resource/js/dialog/dialog.css" rel="stylesheet" type="text/css">
<link href="<?php echo BASE_SITE_URL;?>/shop/resource/font/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo ADMIN_SITE_URL;?>/resource/font/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<style>
    .ncm-goods-gift {
        text-align: left;
    }
    .ncm-goods-gift ul {
        display: inline-block;
        font-size: 0;
        vertical-align: middle;
    }
    .ncm-goods-gift li {
        display: inline-block;
        letter-spacing: normal;
        margin-right: 4px;
        vertical-align: top;
        word-spacing: normal;
    }
    .ncm-goods-gift li a {
        background-color: #fff;
        display: table-cell;
        height: 30px;
        line-height: 0;
        overflow: hidden;
        text-align: center;
        vertical-align: middle;
        width: 30px;
    }
    .ncm-goods-gift li a img {
        max-height: 30px;
        max-width: 30px;
    }
    th{
        text-align: center;
        border: solid 1px #D7D7D7;
        min-width: 50px;
    }
    .ncap-order-details {
        margin-bottom: 50px;
    }

    #log_list table th{
        min-height: 32px;
    }
</style>
<body style="overflow-y: scroll;">
<div class="page">
    <div class="ncap-order-style">
        <div class="titile">
            <h3></h3>
        </div>
        <div class="ncap-order-flow">
                <ol class="num5">
                    <li class="current">
                        <h5>生成订单</h5>
                        <i class="fa fa-arrow-circle-right"></i>
                        <time><?php echo date('Y-m-d H:i:s',$output['order_info']['add_time']);?></time>
                    </li>
                    <?php if ($output['order_info']['order_state'] == ORDER_STATE_CANCEL) { ?>
                        <li class="current">
                            <h5>取消订单</h5>
                            <time><?php echo date('Y-m-d H:i:s',$output['order_info']['close_info']['log_time']);?></time>
                        </li>
                    <?php } else { ?>
                        <li class="<?php if(intval($output['order_info']['payment_time']) && $output['order_info']['order_pay_state'] !== false) echo 'current'; ?>">
                            <h5>完成付款</h5>
                            <i class="fa fa-arrow-circle-right"></i>
                            <time><?php echo intval(date('His',$output['order_info']['payment_time'])) ? date('Y-m-d H:i:s',$output['order_info']['payment_time']) : date('Y-m-d',$output['order_info']['payment_time']);?></time>
                        </li>
                        <?php if ($output['order_info']['is_xianhuo'] != 1) { ?>
                            <li>
                                <h5>生产完成</h5>
                                <time><?php echo date('Y-m-d H:i:s',$output['order_info']['close_info']['log_time']);?></time>
                            </li>
                        <?php } ?>
                        <li class="<?php if($output['order_info']['extend_order_common']['shipping_time']) echo 'current'; ?>">
                            <h5>商家发货</h5>
                            <i class="fa fa-arrow-circle-right"></i>
                            <time><?php echo $output['order_info']['extend_order_common']['shipping_time'] ? date('Y-m-d H:i:s',$output['order_info']['extend_order_common']['shipping_time']) : null; ?></time>
                        </li>
                        <li class="<?php if(!empty($output['order_info']['finnshed_time'])) { ?>current<?php } ?>">
                            <h5>订单完成</h5>
                            <time><?php echo $output['order_info']['finnshed_time'] ? date("Y-m-d H:i:s",$output['order_info']['finnshed_time']) : null; ?></time>
                        </li>
                    <?php } ?>
                </ol>
        </div>
        <div class="ncap-order-details">
            <ul class="tabs-nav">
                <li class="current"><a href="javascript:void(0);">订单详情</a></li>
            </ul>
            <div class="tabs-panels">
                <div class="misc-info">
                    <dl>
                        <dt>
                        <h4 style="float: left;">下单/支付</h4>
                        </dt>
                        <?php if ($output['order.editable']) { ?><dd><a href="javascript:void(0);" onClick="ajax_form('edit_order', '编辑订单', 'index.php?act=store_order&op=edit_order&order_id=<?php echo $output['order_info']['order_id'];?>', 640,0);" class="ncbtn-mini fr"><i class="icon-edit"></i>编辑</a></dd><?php } ?>
                    </dl>
                    <dl>
                        <dt>订单号：</dt>
                        <dd><?php echo $output['order_info']['order_sn'];?><?php if ($output['order_info']['order_type'] == 2) echo '[预定]';?><?php if ($output['order_info']['order_type'] == 3) echo '[门店自提]';?></dd>
                        <dt>销售渠道：</dt>
                        <dd><?php echo  $output['order_info']['store_name'];?></dd>
                        <dt>下单时间：</dt>
                        <dd><?php echo date('Y-m-d H:i:s',$output['order_info']['add_time']);?></dd>
                    </dl>
                    <dl>
                        <dt>客户来源：</dt>
                        <dd id="customer_source_name"><?php echo $output['order_info']['source_name'];?></dd>
                        <dt>支付方式：</dt>
                        <dd><?php echo orderPaymentName($output['order_info']['payment_code']);?></dd>
                        <dt>支付时间：</dt>
                        <dd><?php echo empty($output['order_info']['payment_time'])?"":date('Y-m-d H:i:s',$output['order_info']['payment_time']);?></dd>
                    </dl>
                    <dl>
                        <dt>制单人：</dt>
                        <dd id ="buyer_name1"><?php echo $output['order_info']['seller_name'];?></dd>
                        <dt>审核人：</dt>
                        <dd><?php echo $output['order_info']['audit_by'];?></dd>
                        <dt>审核时间：</dt>
                        <dd><?php echo $output['order_info']['audit_time'];?></dd>
                    </dl>
                    <dl>
                        <dt>客户名称：</dt>
                        <dd id="seller_name1"><?php echo $output['order_info']['buyer_name'];?></dd>
                        <dt>手机号码：</dt>
                        <dd id="buyer_phone1"><?php echo orderPaymentName($output['order_info']['buyer_phone']);?></dd>
                        <dt>订单类型：</dt>
                        <dd><?php echo $output['order_info']['is_xianhuo']==1?"现货订单":"定制订单";?></dd>
                    </dl>
                    <dl>
                        <dt>订单状态：</dt>
                        <dd><?php echo orderState($output['order_info']);?></dd>
                        <dt>支付状态：</dt>
                        <dd><?php echo orderPayStatusName($output['order_info']['pay_status']);?></dd>
                        <dt>生产状态：</dt>
                            <dd><?php echo orderProductName($output['order_info']);?></dd>
                    </dl>
                    <dl>
                        <dt>退款状态：</dt>
                        <dd><?php echo orderRefundState($output['order_info']);?></dd>
                        <dt>订单备注：</dt>
                        <dd><?php echo $output['order_info']['remark'];?></dd>
                    </dl>
                    <?php if ($output['order_info']['order_state'] == ORDER_STATE_CANCEL) { ?>
                        <dl>
                            <dt>订单取消原因：</dt>
                            <dd><?php echo $output['order_info']['close_info']['log_role'];?>(<?php echo $output['order_info']['close_info']['log_user'];?>) <?php echo $output['order_info']['close_info']['log_msg'];?></dd>
                        </dl>
                    <?php }?>
                </div>
                <div class="addr-note">
                    <dl>
                        <dt>
                             <h4 style="float: left;">购买/收货方信息</h4>
                        </dt>
                        <dd></dd>
                    </dl>
                    <dl>
                        <dt>买家：</dt>
                        <dd><?php echo $output['order_info']['buyer_name'];?></dd>
                        <dt>联系方式<?php echo $lang['nc_colon'];?></dt>
                        <dd><?php echo @$output['order_info']['extend_order_common']['reciver_info']['phone'];?></dd>
                    </dl>
                    <dl>
                        <dt>收货地址<?php echo $lang['nc_colon'];?></dt>
                        <dd> <span id="buyer_address_span"><?php echo $output['order_info']['extend_order_common']['reciver_name'];?>&nbsp;<?php echo $output['order_info']['extend_order_common']['reciver_info']['phone'];?>&nbsp;<?php echo $output['order_info']['extend_order_common']['reciver_info']['address'];?></span>
                             &nbsp;&nbsp;<a href="javascript:void(0);" onClick="ajax_form('edit_buyer_address', '编辑收货地址', 'index.php?act=store_deliver&op=buyer_address_edit&order_id=<?php echo $output['order_info']['order_id'];?>', 640,0);" class="ncbtn-mini fr"><i class="icon-edit"></i>编辑</a>
                        </dd>
                    </dl>
                    <dl>
                        <dt>发票信息<?php echo $lang['nc_colon'];?></dt>
                        <dd>
                            <span id="invoice_span">
                            <?php if (!empty($output['order_info']['extend_order_common']['invoice_info'])) {?>
                                <ul>
                                    <?php foreach ((array)$output['order_info']['extend_order_common']['invoice_info'] as $key => $value){?>
                                        <li><strong><?php echo $key.$lang['nc_colon'];?></strong><?php echo $value;?></li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                            </span>
                            <a href="javascript:void(0);" onClick="ajax_form('edit_invoice', '编辑发票', 'index.php?act=store_order&op=edit_invoice&order_id=<?php echo $output['order_info']['order_id'];?>', 640,0);" class="ncbtn-mini fr"><i class="icon-edit"></i>编辑</a>
                        </dd>
                    </dl>
                    <dl>
                        <dt>买家留言<?php echo $lang['nc_colon'];?></dt>
                        <dd><?php echo $output['order_info']['extend_order_common']['order_message']; ?></dd>
                    </dl>
                </div>

                <div class="contact-info">
                    <dl>
                        <dt>
                        <h4 style="float: left;">销售/发货方信息</h4>
                        </dt>
                        <dd>
                        </dd>
                    </dl>
                    <dl>
                        <dt>发货方式：</dt>
                        <dd><?php echo $output['order_info']['store_name'];?></dd><dt>店主名称<?php echo $lang['nc_colon'];?></dt>
                        <dd><?php echo $output['store_info']['seller_name'];?></dd>
                        <dt>联系电话<?php echo $lang['nc_colon'];?></dt>
                        <dd><?php echo $output['store_info']['store_phone'];?></dd>
                    </dl>
                    <dl>
                        <dt>发货地址<?php echo $lang['nc_colon'];?></dt>
                        <?php if (!empty($output['daddress_info'])) {?>
                            <dd>
                                <a href="javascript:void(0);" onClick="ajax_form('modfiy_daddress', '编辑发货地址', 'index.php?act=store_deliver&op=send_address_select&order_id=<?php echo $output['order_info']['order_id'];?>', 640,0);" class="ncbtn-mini fr"><i class="icon-edit"></i>编辑</a> <span id="seller_address_span"> <?php echo $output['daddress_info']['seller_name']; ?>&nbsp;,&nbsp;<?php echo $output['daddress_info']['telphone'];?>&nbsp;,&nbsp;<?php echo $output['daddress_info']['area_info'];?>&nbsp;<?php echo $output['daddress_info']['address'];?>&nbsp;,&nbsp;<?php echo $output['daddress_info']['company'];?></span></dd>
                            </dd>
                        <?php }else{?>
                            <dd> <a href="javascript:void(0);" onClick="ajax_form('modfiy_daddress', '编辑发货地址', 'index.php?act=store_deliver&op=send_address_select&order_id=<?php echo $output['order_info']['order_id'];?>', 640,0);" class="ncbtn-mini fr"><i class="icon-edit"></i>编辑</a> <span id="seller_address_span"></span></dd>
                        <?php } ?>

                    </dl>
                    <dl>
                        <dt>发货时间<?php echo $lang['nc_colon'];?></dt>
                        <dd><?php echo $output['order_info']['extend_order_common']['shipping_time'] ? date('Y-m-d H:i:s',$output['order_info']['extend_order_common']['shipping_time']) : null; ?></dd>
                        <dt>快递公司<?php echo $lang['nc_colon'];?></dt>
                        <dd><?php echo $output['order_info']['express_info']['e_name'];?></dd>
                        <dt>物流单号<?php echo $lang['nc_colon'];?></dt>
                        <dd>
                            <?php if($output['order_info']['shipping_code'] != ''){?>
                                <?php echo $output['order_info']['shipping_code'];?>
                            <?php }?>
                        </dd>
                    </dl>
                </div>

                <div class="goods-info" style="overflow-x: scroll;">
                    <h4>商品信息</h4>
                    <table  style="width: 700px;font-size: 13px;">
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>图片</th>
                            <th>货号</th>
                            <th>真实货号</th>
                            <th>款号</th>
                            <th>商品名称</th>
                            <th>商品价格</th>
                            <th>优惠金额</th>
                            <th>成交金额</th>
                            <th>退款金额</th>
                            <th>数量</th>
                            <th>主石单颗重</th>
                            <th>主石粒数</th>
                            <th>颜色</th>
                            <th>净度</th>
                            <th>切工</th>
                            <th>证书类型</th>
                            <th>证书号</th>
                            <th>金料</th>
                            <th>金色</th>
                            <th>表面工艺</th>
                            <th>镶口</th>
                            <th>指圈</th>
                            <th>刻字</th>
                            <th>镶嵌要求</th>
                            <th>现货/期货</th>
                            <th>商品类型</th>
                            <th>布产状态</th>
                            <th>布产id</th>
                            <th>维修状态</th>
                            <th>是否赠品</th>
                            <th>是否销账</th>
                            <th>是否4C配钻</th>
                            <th>是否成品定制</th>
                            <th>起版类型</th>
                        </tr>
                        </thead>
                        <tbody id="check_del">
                        <?php $i = 0;?>
                        <?php foreach($output['order_info']['goods_list'] as $goods){ ?>
                            <?php $i++;?>
                            <tr del="<?php echo $goods['is_return'];?>">
                                <th><?php echo $i;?></th>
                                <th><img  src="<?php echo $goods['goods_image'];?>" width="100" height="100" /></th>
                                <th><?php echo $goods['goods_id'];?></th>
                                <th><?php echo $goods['goods_itemid'];?></th>
                                <th><?php echo $goods['style_sn'];?></th>
                                <th><?php echo $goods['goods_name'];?></th>
                                <th><?php echo $goods['goods_price'];?></th>
                                <th><?php echo $goods['goods_price']-$goods['goods_pay_price'];?></th>
                                <th><?php echo $goods['goods_pay_price'];?></th>
                                <th><?php echo $goods['refund_amount'];?></th>
                                <th><?php echo $goods['goods_num'];?></th>
                                <th><?php echo $goods['carat'];?></th>
                                <th><?php echo $goods['zhushi_num'];?></th>
                                <th><?php echo $goods['color'];?></th>
                                <th><?php echo $goods['clarity'];?></th>
                                <th><?php echo $goods['cut'];?></th>
                                <th><?php echo $goods['cert_type'];?></th>
                                <th><?php echo $goods['cert_id'];?></th>
                                <th><?php echo $goods['caizhi'];?></th>
                                <th><?php echo $goods['jinse'];?></th>
                                <th><?php echo $goods['face_work'];?></th>
                                <th><?php echo $goods['xiangkou'];?></th>
                                <th><?php echo $goods['zhiquan'];?></th>
                                <th><?php echo $goods['kezi'];?></th>
                                <th><?php echo str_replace(array(1,2,3,4,5,6,7,8),array('工工厂配钻，工厂镶嵌','不需工厂镶嵌','需工厂镶嵌,客户先看钻再返厂镶嵌','镶嵌4C裸钻','镶嵌4C裸钻','客户先看钻','成品','半成品'),$goods['xiangqian']);?></th>
                                <th><?php echo str_replace(array(0,1),array('期货','现货'),(int)$goods['is_xianhuo']);?></th>
                                <th><?php echo str_replace(array(1,2),array('成品','空托'),(int)$goods['tuo_type']);?></th>
								<?php 
								  $bc_arr =array(1=>'初始化',2=>'待分配',3=>'已分配',4=>'生产中',5=>'质检中',6=>'质检完成',7=>'部分出厂',8=>'作废',9=>'已出厂',10=>'已取消',11=>'不需布产',12=>'其他'); 
								?>
                                <th><?php echo $bc_arr[$goods['bc_status']];?></th>
                                <th><?php echo $goods['bc_id'];?></th>
								<th><?php echo str_replace(array(0,1),array('未维修','有维修'),(int)$goods['weixiu_status']);?></th>
                                <th><?php echo $goods['goods_type']==5?"是":"否";?></th>                                
                                <th><?php echo str_replace(array(0,1),array('不需要','销账'),$goods['is_finance']);?></th>
                                <th><?php echo str_replace(array(0,1,2),array('不支持','裸钻支持','成品支持'),$goods['peishi_type']);?></th>
                                <th><?php echo str_replace(array(0,1),array('否','是'),$goods['is_cpdz']);?></th>
                                <th>
								<?php if($goods['is_qiban'] && $goods['style_sn']=="QIBAN"){?>
								无款起版
								<?php }else if($goods['is_qiban'] && $goods['style_sn']!="QIBAN"){?>
								有款起版
								<?php }else{?>
								非起版
								<?php }?>
								</th>
                               </tr>
                        <?php } ?>
                        </tbody>
                        <!-- S 促销信息 -->
                        <?php $pinfo = $output['order_info']['extend_order_common']['promotion_info'];?>
                        <?php if(!empty($pinfo)){ ?>
                            <?php $pinfo = unserialize($pinfo);?>
                            <tfoot>
                            <tr>
                                <th colspan="10">其它信息</th>
                            </tr>
                            <tr>
                                <td colspan="10">
                                    <?php if($pinfo == false){ ?>
                                        <?php echo $output['order_info']['extend_order_common']['promotion_info'];?>
                                    <?php }elseif (is_array($pinfo)){ ?>
                                        <?php foreach ($pinfo as $v) {?>
                                            <dl class="nc-store-sales"><dt><?php echo $v[0];?></dt><dd><?php echo $v[1];?></dd></dl>
                                        <?php }?>
                                    <?php }?>
                                </td>
                            </tr>
                            </tfoot>
                        <?php } ?>
                        <!-- E 促销信息 -->
                    </table>
                </div>
                <div class="total-amount">
                    <h3>订单总额：<strong class="red_common"><?php echo $lang['currency'].ncPriceFormat($output['order_info']['order_amount']);?></strong></h3>
                    <h4>(运费：<?php echo $lang['currency'].ncPriceFormat($output['order_info']['shipping_fee']);?>)</h4>
                    <?php if($output['order_info']['refund_amount'] > 0) { ?>
                        (退款总额：<?php echo $lang['currency'].ncPriceFormat($output['order_info']['refund_amount']);?>)
                    <?php } ?>
                </div>
                <style>
                    .log_title{ cursor:pointer}
                    .log_title h4{display: inline-block;padding: 5px 10px;  cursor: :pointer;}
                    .log_title h4.selected{color:#2cbca3;border-bottom: 1px solid #2cbca3;}
                </style>
                <div class="goods-info" style="margin-bottom: 35px;" id="log_list">
                    <div class="log_title"><h4 class="selected">订单日志</h4><h4>生产日志</h4></div>
                    <div class="log_inner log_inner1">
                        <table  style="width: 100%;font-size: 13px;">
                            <thead>
                            <tr>
                                <th>序号</th>
                                <th>系统日志</th>
                                <th>操作人</th>
                                <th>操作时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 0;?>
                            <?php foreach($output['order_log_list'] as $log){ ?>
                                <?php $i++;?>
                                <tr style="height: 32px;">
                                    <th><?php echo $i;?></th>
                                    <th style="text-align: left;padding-left:5px"><?php echo $log['log_msg'];?></th>
                                    <th><?php echo $log['log_user'];?></th>
                                    <th><?php echo $log['log_time'];?></th>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="log_inner log_inner2" style="display:none;">
                        <table  style="width: 100%;font-size: 13px;">
                            <thead>
                            <tr>
							    <th>序号</th>
                                <th>系统日志</th>
                                <th>操作人</th>
                                <th>操作时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 0;?>
                            <?php foreach($output['product_log_list'] as $log){ ?>
                                <?php $i++;?>
                                <tr style="height: 32px;">
								    <th><?php echo $i;?></th>
                                    <th style="text-align: left;padding-left:5px"><?php echo $log['remark'];?></th>
                                    <th><?php echo $log['create_user'];?></th>
                                    <th><?php echo $log['create_time'];?></th>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script type="text/javascript">

    $('#check_del tr').each(function(){
        if ($(this).attr('del')==1)
        {
            $(this).children().each(function(){
                $(this).attr('style',"position:relative;");
                $(this).append('<div style="width:100%;position:absolute;top:14px;left:-1px;border-bottom:solid 1px red;"></div><div style="width:100%;position:absolute;top:19px;left:-1px;border-bottom:solid 1px red;"></div>');
            });
        }
    });

    $(".log_title h4").click(function(){
        $(this).addClass("selected").siblings().removeClass("selected");
        var this_=$(this).index()+1;
        $(".log_inner"+this_).show().siblings(".log_inner").hide();
    })
</script>
</html>
