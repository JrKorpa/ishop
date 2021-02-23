<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<style type="text/css">
.sticky .tabmenu { padding: 0;  position: relative; }

.order_search input{
	display:inline;
}


td.exc_goods_info{text-align:left;}
.goods_table{
	width: 100%;
}
.goods_table td{
	text-align:center;border:solid 1px #DDD;
}
</style>
       
<div class="wrap">
   <div class="order_search">    
     <input type="text" size="30" name="order_sn" placeholder="请输入订单号" value="<?php echo $output['order_sn'] ?>"/>&nbsp;<input type="button" class="submit" id="order_search" value="查询">
	
   </div>
   <?php if($output['order_sn'] !=''){ ?>
   <table class="ncsc-default-table order deliver"  id="goods_exchange">
      <tbody>
        <?php if (is_array($output['order_info']) and !empty($output['order_info'])) { ?>
        <tr>
          <td colspan="20" class="sep-row"></td>
        </tr>
        <tr>
          <th colspan="20"><span class="fr mr30"></span><span class="ml10"><?php echo $lang['store_order_order_sn'].$lang['nc_colon'];?><a href="index.php?act=store_order&op=show_order&order_id=<?php echo $output['order_info']['order_id'];?>" target="_blank"><?php echo $output['order_info']['order_sn']; ?></a></span><span class="ml20"><?php echo $lang['store_order_add_time'].$lang['nc_colon'];?><em class="goods_info-time"><?php echo date("Y-m-d H:i:s",$output['order_info']['add_time']); ?></em></span>
        </tr>
		<tr>
		 <td class="bdl exc_goods_info" style="text-align:left;padding-left:10px;border-bottom:none;" colspan="20">更换货号: <input type="text" name="goods_sn" placeholder="请输入新货号"/></td>
		</tr>
        
        <tr>
		   <td  colspan="20" style="padding:0px;">
         
                    <table class="goods_table" style="">
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>图片</th>
                            <th>货号</th>
                            <th>真实货号</th>
                            <th>款号</th>
                            <th>商品名称</th>
                            <th>商品价格</th>
                            <th>颜色</th>
                            <th>净度</th>
                            
                           
                            <th>现货/期货</th>
                            <th>是否退货</th>
                            <th>布产状态</th>
                            <th>布产id</th>
                            <th>维修状态</th>
                          
                           
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($output['order_info']['extend_order_goods'] as $k => $goods_info) { ?>
                           <?php if ($goods_info['is_return']==1 || ($goods_info['goods_type']==5 && $goods_info['is_finance']==0 )) continue; ?>
                            <tr>
                                <td><input type="radio" name="goods_id" <?php if($goods_info['is_return']==1) echo 'disabled="disabled"; ' ?> value="<?php echo $goods_info['rec_id'];?>"/></td>
                                <td><img  src="<?php echo $goods_info['goods_image'];?>" widtd="50" height="50" /></td>
                                <td><?php echo $goods_info['goods_id'];?></td>
                                <td><?php echo $goods_info['goods_itemid'];?></td>
                                <td><?php echo $goods_info['style_sn'];?></td>
                                <td><?php echo $goods_info['goods_name'];?></td>
                                <td><?php echo $goods_info['goods_price'];?></td>
                              
                                <td><?php echo $goods_info['color'];?></td>
                                <td><?php echo $goods_info['clarity'];?></td>
                               
                                <td><?php echo str_replace(array(0,1),array('期货','现货'),(int)$goods_info['is_xianhuo']);?></td>
                                <td><?php echo $goods_info['is_return']==1?"是":"否";?></td>
								<?php 
								  $bc_arr =array(1=>'初始化',2=>'待分配',3=>'已分配',4=>'生产中',5=>'质检中',6=>'质检完成',7=>'部分出厂',8=>'作废',9=>'已出厂',10=>'已取消',11=>'不需布产',12=>'其他'); 
								?>
                                <th><?php echo $bc_arr[$goods_info['bc_status']];?></th>    
                                <td><?php echo $goods_info['bc_id'];?></td>
								<td><?php echo str_replace(array(0,1),array('未维修','有维修'),(int)$goods_info['weixiu_status']);?></td>
                              
                               
                               </tr>
                        <?php } ?>
                        </tbody>
                        <!-- S 促销信息 -->
                        <tfoot>
                           
                            <tr>
                                <td colspan="20" style="padding-left:10px;">
								   <input type="hidden" value="<?php echo $output['order_info']['order_id'];?>" name="order_id"/>
                                   <input type="button" class="submit" id="confirm_button" value="换货">
                                </td>
                            </tr>
                            </tfoot>
                        
                        <!-- E 促销信息 -->
                    </table>
           
           </td>
        </tr>
      
       
        <?php } else { ?>
        <tr>
          <td colspan="20" class="norecord"><i>&nbsp;</i><span><?php echo $lang['no_record'];?></span></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
   <?php } ?>
    
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js"></script>
<script charset="utf-8" type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<script type="text/javascript">

$(function(){
	$("#order_search").click(function(){
		var order_sn = $(".order_search").find("input[name='order_sn']").val();
		if(order_sn == ''){
			alert("请输入订单号");return false;
		}
		window.location.href="index.php?act=store_goods_exchange&op=index&order_sn="+order_sn;
		
		
	});
	
	
	$("#confirm_button").click(function(){
		var order_id = $("#goods_exchange").find("input[name='order_id']").val();
		var goods_id = $("#goods_exchange").find("input[name='goods_id']:checked").val();
		var goods_sn = $("#goods_exchange").find("input[name='goods_sn']").val();
		if(order_id == ''){
			alert("地址错误");return false;
		}
		if(goods_sn == ''){
			alert("请填写要更换的货号");return false;
		}
		if(typeof(goods_id) == "undefined"){
			alert("请选择货品");return false;
		}
		$.ajax({
			url:'index.php?act=store_goods_exchange&op=exchange',
			type:'POST', //GET
			async:true,    //或false,是否异步
			data:{
				order_id:order_id,
				goods_id:goods_id,
				goods_sn:goods_sn
			},
			timeout:5000,    //超时时间
			dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
			beforeSend:function(xhr){
				//console.log(xhr)
				console.log('发送前')
			},
			success:function(data,textStatus,jqXHR){
				console.log(data)
				if(data.code == 0){
					alert(data.msg);return false;
				}else{
					window.location.reload();
				}
				//console.log(textStatus)
				//console.log(jqXHR)
			},
			error:function(xhr,textStatus){
				//console.log('错误')
				console.log(xhr)
				//console.log(textStatus)
			},
			complete:function(){
				console.log('结束')
			}
		})
		
		
	})
});
</script>
