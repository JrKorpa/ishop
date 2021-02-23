<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>珂兰钻石</title>
<style type="text/css">
    body,td { font-size:12px; line-height:18px;}
    .table{width: 800px;margin: 0 auto}
    .fontsize12 {font-size:12px;}
    .return_goods * {text-decoration:line-through;}
    .dwqr{position: absolute; bottom:0; right:0;width:140px; height:122px;text-align: center;}
    .dwqr img{padding: 0;margin: 0}
    .dwqr p{padding:0;margin: 0;line-height: 22px;font-size: 12px}
    .print{width:750px;margin: 0 auto; padding: 30px 25px; overflow:hidden;}
    .print button{float: right;height: 28px;padding:4px 20px; margin:0 10px}
</style>
<!--<script type="text/javascript" src="public/js/jquery-1.10.2.min.js"></script>-->
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.js" charset="utf-8"></script>
</head>
<body>
<div class="print">
    <button id="print_close">关闭</button>
    <button id="print_btn">打&nbsp;印</button>
</div>
<table width="100%" cellspacing="0" cellpadding="1" border="0">
    <tbody>
    <tr>
        <td>
            <table style="margin-bottom: 15px; border-bottom: #000 2px solid" cellspacing="0" width="100%" border="0">
                <tbody>
                <tr>
                    <td width="14%"><img alt="" border="0" src="<?php echo SHOP_TEMPLATES_URL;?>/images/logo3.jpg" /></td>
                    <td valign="middle" width="86%">
                        <h1 style="padding-left: 15px; padding-top: 20px"><?php echo $output['order_info']['title'];?><!--珂兰货品销售单--></h1></td>
                    <td align="right">
                        <img src="http://bardcode.kela.cn/index.php?code_sn=<?php echo $output['order_info']['order_sn'];?>" style="float:right;" width="200">
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table style="line-height: 18px" cellspacing="0" cellpadding="1" width="100%" border="0">
                <tbody>
                <tr>
                    <td>收货人: <?php echo $output['order_info']['buyer_name'];?>&nbsp;<!-- 收货人姓名 --></td>
                    <td>手机号码：<?php echo $output['order_info']['buyer_phone'];?><!-- 手机号码 --></td>
                    <td align="right" >订单编号：</td>
                    <td><?php echo $output['order_info']['order_sn'];?><!-- 订单号 --></td>
                    <!--<td align="right">支付方式：</td>
                    <td></td>-->
                </tr>
                <tr>
                    <td colspan="3">收货地址：<?php echo @$output['order_info']['extend_order_common']['reciver_info']['address'];?></td>
                    <td align="center"><!--{if $chknum}会员等级:{$chknum}{/if}--></td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table cellspacing="0" cellpadding="1" width="100%" border="0">
                <tbody>
                <tr>
                    <td>
                        <table style="border-right: #000 1px solid; border-top: #000 1px solid; margin-top: 5px; border-left: #000 1px solid; border-bottom: #000 1px solid; border-collapse: collapse" width="100%" border="1">
                            <tbody>
                            <tr align="center">
                                <td width="30%">商品名称<!-- 商品名称--></td>
                                <td width="10%">款号<!--款号--></td>
                                <td width="6%">手圈<!--手寸--></td>
                                <td width="6%">石重<!--石重--></td>
                                <td width="6%">金料<!--金料--></td>
                                <td width="8%">金料颜色<!--金料颜色--></td>
                                <td width="8%">刻字<!--刻字--></td>
                                <td width="13%">小计<!--小计--></td>
                            </tr>
                            <?php foreach ($output['goods_list'] as $item_k =>$item_v){?>
                            <?php foreach ($item_v as $k=>$v){?>
                            <!--<%foreach from=$detailsinfo item=goods key=key%>-->
                            <tr <?php if($v['is_return'] == 1){?>class="return_goods"<?php }?>>
                            <td><?php echo $v['goods_name'];?><!--商品名称--></td>
                            <td><?php echo $v['style_sn'];?><!--款号--></td>
                            <td><?php echo $v['zhiquan'];?><!--手寸--></td>
                            <td><?php echo $v['carat'];?><!--石重--></td>
                            <td><?php echo $v['caizhi'];?><!--金料--></td>
                            <td><?php echo $v['jinse'];?><!--金料颜色--></td>
                            <td><?php echo $v['kezi'];?><!--刻字--></td>
                            <td><?php echo $lang['currency'].$v['goods_all_price'];?>&nbsp;<!--小计 --></td>
                            </tr>
                            <?php }?>
                            <?php }?>
                            <!--<%/foreach%>-->
                            <tr>
                                <!-- 发票抬头和发票内容 -->
                                <td colspan="6">发票抬头: <br/>发票邮寄地址：</td>
                                <!-- 商品总金额 -->
                                <td colspan="1"></td>
                                <td colspan="1"></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <!--<td style="padding-right: 15px"align="right">商品总金额：:￥<%$order_account.money_paid+$order_account.money_unpaid%> - 已支付金额：￥<%$order_account.money_paid%> = 应付余额：￥<%$order_account.money_unpaid%></td>-->
					<td style="padding-right: 15px"align="right">订单总金额：<?php echo $lang['currency'].$output['goods_total_price'];?>元 - 已付款金额：<?php echo $lang['currency'].$output['order_info']['rcb_amount'];?>元 + 实退金额 <?php echo $lang['currency'].$output['order_info']['refund_amount'];?>元 = 应收尾款：<?php echo $lang['currency'].$output['order_info']['money_unpaid'];?>元</td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table style="margin-top: 5px" cellspacing="0" cellpadding="1" width="100%" border="0">
                <tbody>
                <tr>
                    <td><strong><!--用户给商家留言-->订单备注:  </strong><?php echo $output['order_info']['remark'];?></td>
                </tr>
                <tr>
                    <td><strong><!--用户给商家留言-->赠品信息： </strong><?php echo $output['order_info']['giftstr'];?></td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table style="margin-top: 5px" cellspacing="0" cellpadding="1" width="100%" border="0">
                <tbody>
                <tr>
                    <td style="margin-top: 5px"><strong>订购须知：</strong></td>
                </tr>
                <tr>
                    <td height="92" > 
1.购买现货产品的，应在本单签订之日向珂兰支付全款；购买订制产品的，应在本单签订之日支付珂兰不低于产品总额50%的预付款。<br />
2.您向珂兰购买的产品均附有相应的国际证书或国检证书。<br />
3.购买订制产品的，<b><u><i>如由于原材料紧缺、产品成本上涨等不可抗力原因，造成珂兰无法按时交货，珂兰将在本订单签订之日5个工作日向您说明，您可以选择其他同类产品生成新订单并享受总价1%的折扣优惠或者您可以选择取消本单，珂兰退还您已支付的预付款并赠送歉意礼品。</i></u></b><br />
4.购买现货产品的，珂兰将于您付清货款当日交付产品；购买订制产品的，珂兰自本单签订之日起35个工作日内向您交付产品<b><u><i>（若在本单生产过程中您修改订制内容影响生产周期，交付期限将从确认订制内容之日重新计算）。</i></u></b><br />
5.<b><u><i>购买订制产品的，自珂兰通知您取货之日起，您应在30天内到店支付办理验货及结算取货手续，如您未在30天内完成验货及结算取货手续，您应按照订单总价的1%/天向珂兰支付该商品的保管费。</i></u></b><br />
6.<b><u><i>对于您购买黄金、裸钻、珍珠、特价及订制产品，除因产品质量问题，珂兰不接受退、换货。特别说明：购买现货产品提出特殊需求，如刻字、改圈等影响二次销售的订单产品，珂兰也将不接受退、换货。</i></u></b><br />
7.<b><u><i>由于国内外钻石分级标准不同，如您选择的钻石国检证书复检后颜色和净度低于国际证书2级或以上，您可选择退货（赠送歉意礼品）或换货，换货将享受新订单总额1%的现金优惠。</i></u></b><br />
8.<b><u><i>珂兰销售的成品裸钻，均以附带的证书为准。如客户自行送检，出现证书级别与国际证书级别不一致的情况（除第7条外），不得以此向珂兰要求退货或者赔偿。</i></u></b><br />
9.本单一式两份，各执一份，在您签名确认和加盖珂兰销售专用章后生效。<br />
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table style="margin-top: 5px" cellspacing="0" cellpadding="1" width="100%" border="0">
                <tbody>
                <tr>
                    <td>
                        <table cellspacing="0" cellpadding="1" width="100%" border="0">
                            <tbody>
                            <tr>
                                <td><strong>收货检验小提示:</strong></td>
                            </tr>
                            <tr>
                                <td>1.由于监督检验中心的信息录入会滞后产品交付时间，一般在收到产品10天内可查询相关信息。<br />
                                    2.国家黄金钻石制品质量监督检验中心（NGDTC）证书查询，请访问 http://www.ngdtc.cn/Index.asp。<br />
                                    3.GIA裸钻查询地址：http://www.gia.edu/reportcheck/。<br />
                                    4.如果其他类别的质监证书，请咨询珂兰客服人员。珂兰官网（http://www.kela.cn），客服电话：400-8980-188。</td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table cellspacing="0" cellpadding="1" width="100%" border="0">
                            <tbody>
                            <tr>
                                <td><b><i>我已阅读并同意上述约定，客户签名：____________</i></b></td><td>珂兰销售专用章(盖章)<br/></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <!--<tr>
                    <td>
                        <table cellspacing="0" cellpadding="1" width="100%" border="0">
                            <tbody>
                            <tr>
                                <td><strong>国际证书裸钻销售说明:</strong></td>
                            </tr>
                            <tr>
                                <td>珂兰销售的国际证书裸钻，均附有国内权威检测机构出具的复检证书。<br />
                                    珂兰销售的国际证书裸钻，均以该裸钻附带的国际证书为标准。如客户自行送检，出现证书级别与国际证书级别不一致的情况，不得作为向珂兰要求退货或者赔偿的理由。<br/>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>-->
                <tr>
                    <td>
                        <table cellspacing="0" cellpadding="1" width="100%" border="0">
                            <tbody>
                            <tr>
                                <td style="padding-top: 10px">珂兰钻石网（http://www.kela.cn） 地址：&nbsp;&nbsp; <!--kela.cn 珂兰钻石网（ http://www.kela.cn/）<br />地址：北京市宣武区菜市口大街平原里21号亚泰中心1011室  电话：400-722-8000,010-63559332--></td>
                            </tr>
                            <tr>
                                <td style="padding-top: 10px">体验店电话：客服中心电话：400-8980-188<!--kela.cn 珂兰钻石网（ http://www.kela.cn/）<br /地址：北京市宣武区菜市口大街平原里21号亚泰中心1011室  电话：400-722-8000,010-63559332--></td>
                            </tr>
                            <tr>
                                <td style="padding-top: 10px" align="center"><!--订单操作员以及订单打印的日期 -->打印时间：<?php echo $output['order_info']['daying_time'];?>&nbsp;&nbsp;&nbsp;销售顾问:<?php echo $output['order_info']['seller_name'];?></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>

    </tbody>
</table>

<div class="dwqr">
    <img src="<?php echo SHOP_TEMPLATES_URL;?>/images/QR.jpg" width="100" height="100" alt="" />
    <p>关注微信,查询订单更方便</p>
</div>
<script type="text/javascript">
    $('#print_close').click(function(){
        window.close();
    });
    $('#print_btn').click(function(){
        $(".print").hide();
        window.print();
    });
</script>
</body>
</html>