<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
 <head>
  <title>货品价格标签打印</title>
  <meta name="Generator" content="EditPlus">
  <meta name="Author" content="">
  <meta name="Keywords" content="">
  <meta name="Description" content="">
<STYLE>
@media print {
.NoPrint{display:none}
.PageNext{page-break-after: always;}
}
table{
   font-family:'黑体';font-size:10px;padding:0px; font-weight:bold;
}
table.print{
  height:72mm;/* width:28mm;*/margin:0;padding:0px 1mm; /*border:1px solid red*/
}
table.print th { font-weight:normal;}
table.print th,table.print td{ border:0px;text-align:left;}
table.print td img{ width:100%}
</STYLE>
 </head>
 <body style="padding:0px; margin:0px">
 <input type="button" value="打印" id="printBtn" class="NoPrint"> 
 <br class="NoPrint"/>
 <p class="NoPrint">打印纸张设置：宽 2.8cm 高 7.4cm 上下左右边距 分别为0.1cm 0.1cm 0.1cm 0.1cm</p>
<?php foreach($output['goodslist'] as $goods){?>
<table class="print" cellpadding="0" cellspacing="0">
  <tr>
     <td colspan="2" style="height:4mm;">&nbsp;</th>
  </tr>
  <tr>
     <td style="text-align:center;height:4mm; padding-right:1mm;"><?php echo $goods['zhengshuhao']?></th>
     <td style="text-align:center;height:4mm"><?php echo $goods['goods_sn']?></td>  
  </tr>
  <tr>
  <td colspan="2" style="text-align:center;height:8mm;">
	  <img src="/barcode/barcode128.php?code_sn=<?php echo $goods['goods_id']?>&hideTxt=1"><br/>
	  <?php echo $goods['goods_id']?>
  </td>
  </tr>
  <tr>
     <td colspan="2" style="height:10mm">&nbsp;</td>  
  </tr>
  <?php if($goods['product_type']=="钻石" && $goods['cat_type']=="裸石"){?>
  <tr>
     <td colspan="2" style="text-align:center;height:4mm">裸石</td>  
  </tr>
  <tr>
     <td colspan="2" style="text-align:center;height:3mm">￥<?php echo $goods['goods_price']?></td>  
  </tr>
  <tr>
   <td style="height:3mm">石重:<?php echo $goods['zuanshidaxiao']/1?></td>  
   <td style="height:3mm">颜色:<?php echo $goods['zhushiyanse']?></td>  
  </tr> 
  <tr>
   <td style="height:3mm">净度:<?php echo $goods['zhushijingdu']?></td>  
   <td style="height:3mm">切工:<?php echo $goods['zhushiqiegong']?></td>  
  </tr>
  <?php }else{?>
  <tr>
     <td colspan="2" style="text-align:center;height:4mm"><?php echo $goods['goods_name']?></td>  
  </tr>
  <tr>
     <td colspan="2" style="text-align:center;height:3mm">￥<?php echo $goods['goods_price']?></td>  
  </tr>
  <tr>
   <td colspan="2" style="height:3mm">
     <table style="width:100%;" cellpadding="0" cellspacing="0">
	    <tr>
		 <td style="width:40%;padding:0;"><?php echo $goods['zuanshidaxiao']/1?>ct/<?php echo $goods['zhushilishu']/1?>p</td>
		 <td style="width:40%;padding:0;text-align:center;"><?php echo $goods['zhushiyanse']?>/<?php echo $goods['zhushijingdu']?></td>
		 <td style="width:20%;padding:0;text-align:right;"><?php echo $goods['shoucun']/1?>#</td>
		 </tr>		 
	 </table>   
   </td>  
  </tr>  
  <tr>
     <td style="text-align:left;height:3mm"><?php echo $goods['fushizhong']/1?>ct/<?php echo $goods['fushilishu']/1?>p</td>
     <td style="text-align:right;"><?php echo $goods['jinzhong']/1?>g</td>  
  </tr>
  
  <?php }?>
  <tr>
  <td colspan="2" style="height:18.5mm">&nbsp;</td>
  </tr>
  <tr>
  <td colspan="2" style="text-align:center; height:5.5mm">
	  <img src="/barcode/barcode128.php?code_sn=<?php echo $goods['goods_id']?>&hideTxt=1"><br/>
	  <?php echo $goods['goods_id']?>
  </td>
  </tr>
  <tr>
  <td colspan="2" style="text-align:center; height:18mm">&nbsp;
	  
  </td>
  </tr>
</table>
<div class="PageNext">
   <p class="NoPrint" style="border:2px solid #666666"></p>
</div>
<?php }?>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.js"></script>
<script>
   $("#printBtn").click(function(){
       window.print();
	   <?php if(isset($output['print_type']) && !empty($output['goodslist'])){?>
       $.post('index.php?act=erp_bill&op=print_bill_log',{bill_ids:'<?php echo $_GET['_ids']?>',print_type:'<?php echo $output['print_type']?>'},
	   function(res){
	      if(!res.state){
		      alert(res.msg);
		  }
	   },'json');
	   <?php }?>
   });
</script>
 </body>
</html>
