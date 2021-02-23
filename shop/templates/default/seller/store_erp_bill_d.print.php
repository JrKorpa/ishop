
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>退货返厂单</title>
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
    <h1>退货返厂单</h1>现在时间：<?php echo $output['now_time'];?>
    <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="65">单号：</td>
            <td width="110"><?php echo $output['billlist']['bill_no'];?></td>
            <td width="65">日期：</td>
            <td width="110"><?php echo $output['billlist']['create_time'];?></td>
            <td width="65">店铺：</td>
            <td width="110"><?php echo $output['billlist']['from_company_name'];?></td>
            <td width="65">审核人：</td>
            <td width="110"><?php echo $output['billlist']['check_user'];?></td>
        </tr>
        <tr>
            <td>客户：</td>
            <td colspan = '3'>
                <?php echo $output['billlist']['wholesale_name'];?>
            </td>
            <td>件数：</td>
            <td><?php echo $output['billlist']['goods_num'];?></td>
            <td>参考编号：</td>
            <td></td>
        </tr>
        <tr>
            <td>备注：</td>
            <td colspan="3"><?php echo $output['billlist']['remark'];?></td>
            <td>销售价：</td>
            <td><?php echo $output['tongji']['sale_price'];?></td>
            <td></td>
            <td></td>
        </tr>
    </table>
    <table cellpadding="0" cellspacing="0" border="0" class="list-ch">
        <thead>
            <tr>
                <td>货号</td>
                <td>款号</td>
                <td>名称</td>
                <td>主石<br/>(ct/p)</td>
                <td>副石<br/>(ct/p)</td>
                <td>主成<br/>色重</td>
                <td>证书号</td>
                <td>数量</td>
                <td>销售价</td>
                <!--<td>展厅标签价</td>-->
            </tr>
        </thead>
        <tbody>
        <?php foreach($output['goodslist'] as $k =>$d){?>
            <tr>
                <td><?php echo $d['goods_itemid'];?></td>
                <td><?php echo $d['goods_sn'];?></td>
                <td><?php echo $d['goods_name'];?></td>
                <td><?php echo $d['zuanshidaxiao'];?></td>
                <td><?php echo $d['fushizhong'];?></td>
                <td class="tRight"><?php echo $d['jinzhong'];?></td>
                <td><?php echo $d['zhengshuhao'];?></td>
                <td class="tRight"><?php echo $d['goods_count'];?></td>
                <td class="tRight"><?php echo $d['sale_price'];?></td>
                <!--<td class="tRight"><%$d.label_price%></td>-->
            </tr>
        <?php }?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">合计</td>
                <td><?php echo $output['tongji']['zuanshidaxiao']; ?>/<?php echo $output['tongji']['zhushilishu']; ?></td>  <!-- 主石 -->
                <td><?php echo $output['tongji']['fushizhong']; ?>/<?php echo $output['tongji']['fushilishu']; ?></td> <!-- 副石 -->
                <td class="tRight"><?php echo $output['tongji']['jinzhong']; ?></td><!-- 金重 -->
                <td>&nbsp;</td> <!-- 证书号 -->
                <td class="tRight"><?php echo $output['tongji']['goods_count']; ?></td><!-- 数量 -->
                <td class="tRight"><?php echo $output['tongji']['sale_price']; ?></td>    <!-- 销售价 -->
                <!--<td class="tRight"><%$output['tongji'].label_price|string_format:'%.2f'%></td>-->
            </tr>
        </tfoot>
    </table>
</div>
    <!--endprint-->
</body>
</html>
