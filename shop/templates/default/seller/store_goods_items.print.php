<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
 <head>
  <title>货品标签打印</title>
  <meta name="Generator" content="EditPlus">
  <meta name="Author" content="">
  <meta name="Keywords" content="">
  <meta name="Description" content="">
<STYLE>
@media print {
.noprint{display:none}
.PageNext{page-break-after: always;}
}
table{
font-family:'黑体';font-size:10px; fonborder-collapse:collapse; 
padding:0px;height:40mm; width:50mm;margin:1.5mm 0.5mm;
}
table th { font-weight:normal;}
table th,table td{ border:0px;padding:0mm 2mm; text-align:left}
table td img{ width:50mm}
</STYLE>
 </head>
 <body style="padding:0px; margin:0px"><input type="button" value="打印" id="printBtn" class="noprint"><br class="noprint"/>
<?php foreach($output['goodslist'] as $goods){?>
<table class="print">
  <tr>
  <td colspan="2" style="text-align:center;">
  <img src="/barcode/barcode128.php?code_sn=<?php echo $goods['goods_id']?>&hideTxt=1"></td>
  </tr>
  <tr>
     <th>商品货号：</th>
     <td><?php echo $goods['goods_id']?></td>  
  </tr>  
  <tr>
     <th>商品款号：</th>
     <td><?php echo $goods['goods_sn']?></td>  
  </tr>
  <tr>
     <th>商品名称：</th>
     <td><?php echo $goods['goods_name']?></td>  
  </tr>
  <tr>
   <th>规&nbsp;&nbsp;&nbsp;&nbsp;格：</th>
   <td><?php echo $goods['caizhi'].$goods['jinse']?>/<?php echo $goods['shoucun']/1?>#/<?php echo $goods['jietuoxiangkou']/1?></td>  
  <tr>
  <tr>
   <th>仓&nbsp;&nbsp;&nbsp;&nbsp;库：</th>
   <td><?php echo $goods['warehouse']?></td>  
  <tr>
</table>
<p class="PageNext"></p>
<?php }?>
</tr>
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
