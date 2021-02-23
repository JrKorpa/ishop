
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>维修调拨单</title>
<style type="text/css">

*{margin:0;padding:0;}
body{font:12px/25px "宋体";}
.tRight{text-align:right;}
.wrap{width:700px;margin:50px auto;}
h1{font-size:14px;text-align:center;margin-bottom:10px;}
table.list-ch{border-collapse:collapse;border:none;width:100%;margin-top:10px;}
table.list-ch td{border:1px #333 solid;padding:0 2px;}
table.list-ch thead td{height:35px; line-height:14px; text-align:center; font-weight:bold;}

</style>
<style media="print">

    .Noprint{display:none;}

</style>
<script type="text/javascript">
    function close_bill(){
        window.close();
    }

    function previewprice() {
        bdhtml = window.document.body.innerHTML;
        sprnstr = "<!--startprint-->";
        eprnstr = "<!--endprint-->";
        prnhtml = bdhtml.substr(bdhtml.indexOf(sprnstr) + 17);
        prnhtml = prnhtml.substring(0, prnhtml.indexOf(eprnstr));
        window.document.body.innerHTML = prnhtml;
        window.print();
    }
</script>
</head>
<body>
    <div style="text-align:center;">
        <input type="button" class="Noprint" value="打&nbsp;印&nbsp;单&nbsp;据" onclick="previewprice()" >
        <button id="print_close" onclick="close_bill();">关闭</button>
    </div>
<!--startprint-->
<div class="wrap" id="price">
    <h1>维修调拨单</h1>现在时间：<?php echo $output['now_time'];?>
    <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="65">单号：</td>
            <td width="110"><?php echo $output['billlist']['bill_no'];?><br><img src="http://bardcode.kela.cn/index.php?code_sn=<?php echo $output['billlist']['bill_no'];?>" style="width:360px;height:60px"></td>
            <td width="65">日期：</td>
            <td width="110"><?php echo $output['billlist']['create_time'];?></td>
            <td width="65">状态：</td>
            <td width="110"><?php echo $output['bill_status'][$output['billlist']['bill_status']];?></td>
        </tr>
        <tr>
            <td>收货方：</td>
            <td><?php echo $output['billlist']['to_company_name'];?></td>
            <td>发货地：</td>
            <td colspan = '3'>
                <?php echo $output['billlist']['from_company_name'];?>
            </td>
        </tr>
        <tr>
            <td>数量总计：</td>
            <td><?php echo $output['billlist']['goods_num'];?></td>
            <td>制单人：</td>
            <td><?php echo $output['billlist']['create_user'];?></td>
            <td>审核人：</td>
            <td><?php echo $output['billlist']['check_user'];?></td>
        </tr>
        <tr>
            <td>订单号：</td>
            <td><?php echo $output['billlist']['order_sn'];?></td>
            <td>顾客姓名：</td>
            <td><?php echo $output['billlist']['buyer_name'];?></td>
            <td>配送公司：</td>
            <td><?php echo $output['billlist']['express_name'];?></td>
        </tr>
        <tr>
            <td>配送单号：</td>
            <td><?php echo $output['billlist']['express_sn'];?></td>
            <td>备注：</td>
            <td colspan = '5'><?php echo $output['billlist']['remark'];?></td>
        </tr>
    </table>
    <table cellpadding="0" cellspacing="0" border="0" class="list-ch">
        <thead>
            <tr>
                <td>货号</td>
                <td>款号</td>
                <td>主成<br/>色重</td>
                <td>主石<br/>粒数</td>
                <td>主石重</td>
                <td>数量</td>
                <td>成本价</td>
                <td>成本价小计</td>
                <td>检验数量</td>
                <td>副石粒数</td>
                <td>副石重</td>
                <td>颜色</td>
                <td>净度</td>
                <td>证书号</td>
                <td>名称</td>
                <td>单据编号</td>
            </tr>
        </thead>
        <tbody>
        <?php foreach($output['goodslist'] as $k =>$d){?>
            <tr>
                <td><?php echo $d['goods_itemid'];?></td>
                <td><?php echo $d['goods_sn'];?></td>
                <td class="tRight"><?php echo $d['jinzhong'];?></td>
                <td><?php echo $d['zhushilishu'];?></td>
                <td><?php echo $d['zuanshidaxiao'];?></td>
                <td class="tRight"><?php echo $d['goods_count'];?></td>
                <td><?php echo $d['yuanshichengben'];?></td>
                <td><?php echo $d['yuanshichengben'];?></td>
                <td class="tRight"><?php echo $d['goods_count'];?></td>
                <td><?php echo $d['fushilishu'];?></td>
                <td><?php echo $d['fushizhong'];?></td>
                <td><?php echo $d['yanse'];?></td>
                <td><?php echo $d['jingdu'];?></td>
                <td><?php echo $d['zhengshuhao'];?></td>
                <td><?php echo $d['goods_name'];?></td>
                <td><?php echo $d['bill_no'];?></td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
    <!--endprint-->
</body>
</html>
