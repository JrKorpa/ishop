<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/thickbox/thickbox.js?v=1.1" charset="utf-8"></script>
<link href="<?php echo RESOURCE_SITE_URL;?>/js/thickbox/thickbox.css" rel="stylesheet" />
<!--下拉美化js、css-->
<link href="<?php echo RESOURCE_SITE_URL;?>/js/chosen/css/chosen.css" rel="stylesheet" type="text/css">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/chosen/js/chosen.jquery.js"></script>
<style>
    .ncsc-default-table thead th{
        min-width:70px;
        text-align: center;
    }
    .ncsc-default-table td{
        min-width:70px;
    }
</style>
<div class="search">
<form method="get" id="searchForm">
<input type="hidden" name="act" value="store_goods_items">
<table class="search-form">
    <tr>      
          <th>货  号：</th>
          <td class="w160">
            <input type="text" class="text w120" name="goods_id" placeholder="可批量输入,空格隔开" value="<?php echo $_GET['goods_id']; ?>" />
          </td>
        <!-- <th>公 司：</th>
         <td class="w120">
            <select name="company_id" class="w130">
                <option value=""><?php /*echo $lang['nc_please_choose'];*/?></option>
                <?php /*echo paramsHelper::echoArrayOption($output['company_list'],"id","company_name",$_GET['company_id']); */?>
            </select>
        </td>-->
        <th>仓 库：</th>
        <td class="w120">
            <select name="warehouse_id"  class="w130 chose">
                <option value=""><?php echo $lang['nc_please_choose'];?></option>
                <?php echo paramsHelper::echoArrayOption($output['house_list'],"house_id","name",$_GET['warehouse_id']); ?>
            </select>
        </td>
        <th>入库方式：</th>
        <td class="w120">
            <select name="put_in_type"  class="w130">
                <option value=""><?php echo $lang['nc_please_choose'];?></option>
                <?php echo paramsHelper::echoOption("put_type",$_GET['put_in_type']); ?>
            </select>
        </td>
        <th>维修状态：</th>
        <td class="w120">
            <select name="weixiu_status" class="select w130">
                <option value="">请选择</option>
                <?php echo paramsHelper::echoOption("weixiu_status",$_GET['weixiu_status']); ?>
            </select>
        </td>
    </tr>
	 <tr>
         <th>款号：</th>
         <td class="w120"><input type="text" class="text w120" name="goods_sn" placeholder="输入款号" value="<?php echo $_GET['goods_sn']; ?>"/></td>
         <!--<th>供应商：</th>
         <td class="w120">
             <select name="prc_id"  class="w130">
                 <option value=""><?php /*echo $lang['nc_please_choose'];*/?></option>
                 <?php /*echo paramsHelper::echoArrayOption($output['prc_list'],"sup_id","sup_name",$_GET['prc_id']); */?>
             </select>
         </td>
         <th>供应商名：</th>
         <td class="w120">
             <input type="text" class="text w120" name="prc_name" value="<?php /*echo $_GET['prc_name']; */?>"/>
         </td>-->
         <th>款式系列：</th>
         <td class="w130">
             <select name="xilie" class="w130 chose">
                 <option value=""><?php echo $lang['nc_please_choose'];?></option>
                 <?php echo paramsHelper::echoArrayOption($output['xilie_list'],"id","name",$_GET['xilie']); ?>
             </select>
         </td>
         <th>货品状态：</th>
         <td class="w120">
             <select name="status" class="select w130">
                 <option value="">请选择</option>
                 <?php echo paramsHelper::echoOption("is_on_sale",$_GET['status']); ?>
             </select>
         </td>
         <th>产品线：</th>
         <td class="w120">
             <select name="product_type"  class="w130 chose">
                 <option value=""><?php echo $lang['nc_please_choose'];?></option>
                 <?php echo paramsHelper::echoArrayOption($output['product_type_list'],"product_type_name","product_type_name",$_GET['product_type']); ?>
             </select>
         </td>
     </tr>
    <tr>
        <th>商品名称：</th>
        <td class="w160"><input type="text" class="text w120" name="goods_name"  placeholder="输入商品名称" value="<?php echo $_GET['goods_name']; ?>"/></td>
        <th>款式分类：</th>
        <td class="w120">
            <select name="cat_type"  class="w130 chose">
                <option value=""><?php echo $lang['nc_please_choose'];?></option>
                <?php echo paramsHelper::echoArrayOption($output['cat_type_list'],"cat_type_name","cat_type_name",$_GET['cat_type']); ?>
            </select>
        </td>
        <th>订单号：</th>
        <td class="w120">
            <input type="text" class="text w120" name="order_sn" placeholder="输入订单号" value="<?php echo $_GET['order_sn']; ?>"/>
        </td>
        <th>绑定订单：</th>
        <td class="w120">
            <select name="is_bind_order"  class="w130">
                <option value=""><?php echo $lang['nc_please_choose'];?></option>
                <option value="1"  <?php if (1==$_GET['is_bind_order']) {?>selected="selected"<?php }?>>已绑定</option>
                <option value="2"  <?php if (2==$_GET['is_bind_order']) {?>selected="selected"<?php }?>>未绑定</option>
            </select>
        </td>
    </tr>
    <tr>
        <th>主石大小：</th>
        <td class="w120">
            <input type="text" class="text w60" name="cart_min" placeholder="最小值" value="<?php echo $_GET['cart_min']; ?>"/>-<input type="text" class="text w60" name="cart_max" placeholder="最大值" value="<?php echo $_GET['cart_max']; ?>"/>
        </td>
        <th>证书号：</th>
        <td class="w120">
            <input type="text" class="text w120" name="zhengshuhao" placeholder="输入证书号" value="<?php echo $_GET['zhengshuhao']; ?>"/>
        </td>
        <th>证书类型：</th>
        <td class="w120">
            <select name="zhengshuleibie"  class="w130 chose">
                <option value=""><?php echo $lang['nc_please_choose'];?></option>
                <?php echo paramsHelper::echoOption("zhengshu_type",$_GET['zhengshuleibie']); ?>
            </select>
        </td>
        <th>金托类型：</th>
        <td class="w120">
            <select name="tuo_type" class="select w130">
                <option value="">请选择</option>
                <?php echo paramsHelper::echoOption("tuo_type",$_GET['tuo_type']); ?>
            </select>
        </td>
    </tr>
    <tr>
        <th>指圈范围：</th>
        <td class="w120">
            <input type="text" class="text w60" name="zhiquan_min" placeholder="最小值" value="<?php echo $_GET['zhiquan_min']; ?>"/>-<input type="text" class="text w60" name="zhiquan_max" placeholder="最大值" value="<?php echo $_GET['zhiquan_max']; ?>"/>
        </td>
        <th>颜色：</th>
        <td class="w120">
            <select name="yanse"  class="w130 chose">
                <option value=""><?php echo $lang['nc_please_choose'];?></option>
                <?php echo paramsHelper::echoOption("yanse",$_GET['yanse']); ?>
            </select>
        </td>
        <th>净度：</th>
        <td class="w120">
            <select name="jingdu"  class="w130 chose">
                <option value=""><?php echo $lang['nc_please_choose'];?></option>
                <?php echo paramsHelper::echoOption("jingdu",$_GET['jingdu']); ?>
            </select>
        </td>
        <th>材质：</th>
        <td class="w120">
            <select name="caizhi"  class="w130 chose">
                <option value=""><?php echo $lang['nc_please_choose'];?></option>
                <?php echo paramsHelper::echoOption("caizhi",$_GET['caizhi']); ?>
            </select>
        </td>
    </tr>
    <tr>
        <th>金重范围：</th>
        <td class="w120">
            <input type="text" class="text w60" name="jinzhong_min" placeholder="最小值" value="<?php echo $_GET['jinzhong_min']; ?>"/>-<input type="text" class="text w60" name="jinzhong_max" placeholder="最大值" value="<?php echo $_GET['jinzhong_max']; ?>"/>
        </td>
        <th>材质颜色：</th>
        <td class="w120">
            <select name="jinse"  class="w130">
                <option value=""><?php echo $lang['nc_please_choose'];?></option>
                <?php echo paramsHelper::echoOption("jinse",$_GET['jinse']); ?>
            </select>
        </td>
        <td class="tc" colspan="4" style="text-align: right;">
            <label class="submit-border"><input type="button" class="submit" onclick="choseReset()" value="重置" /></label>
			<label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" /></label>
			<label class="submit-border"><input type="button" class="submit" id="download" value="导出" /></label>
        </td>
    </tr>
</table>
</form>
</div>
<div class="datalist" style="width:100%;height: auto;overflow-x: auto;">
<table class="ncsc-default-table" style="width:1500px">
  <thead>
    <tr>
        <td colspan="21">
           <!-- <a href="javascript:void(0);" nctype="batch" data-param="{sign:'print','url':'<?php echo urlShop('store_goods_items', 'print_goods');?>'}" class="ncbtn ncbtn-mint"><i class="icon-print"></i>打印货品标签</a>-->
            <?php
            $status=empty($_GET["status"])?"-1":"";
            if($status=="-1"||$_GET["status"]==2) {?>
                <a href="javascript:void(0);" nctype="batch" data-param="{sign:'unbind','url':'<?php echo urlShop('store_goods_items', 'unbind');?>'}" class="ncbtn ncbtn-mint"><i></i>货号解绑</a>
            <?php } ?>

        </td>
    </tr>

    <tr>
      <th class="w30"><label for="all" ><?php echo $lang['nc_select_all'];?></label><input type="checkbox" id="all" class="checkall"/></th>
      <!--<th class="w30">序号</th>-->
	  <th class="w30">图片</th>
      <th>货号</th>
      <th>款号</th>
      <th>商品名称</th>
      <th>产品线</th>
      <th>款式分类</th>
      <th>入库方式</th>
      <th>状态</th>
      <th>绑定订单</th>
      <th>所在仓库</th>
      <th>供应商</th>
      <th>数量</th>
      <?php if($output['show_chengben']){?>
      <th>计价成本</th>
      <?php }?>
      <th>材质及颜色</th>
      <th>金重</th>
      <th>指圈</th>
      <th>金托类型</th>
      <th>主石大小</th>
      <th>主石粒数</th>
      <th>证书号</th>
      <th>证书类型</th>
      <th>品牌</th>
      <th>主石形状</th>
      <th>主石颜色</th>
      <th>主石净度</th>
      <th>主石切工</th>
      <th>副石1</th>
      <th>副石1粒数</th>
      <th>副石2</th>
      <th>副石2粒数</th>
      <th>副石2重</th>
      <th>副石3</th>
      <th>副石3粒数</th>
      <th>副石3重</th>
      <th>维修状态</th>
	  <th>维修仓库</th>
	  <th>入库时间</th>
    </tr>   
  </thead>
  <tbody>
    <?php if (!empty($output['item_list'])) { $i=1;?>
    <?php foreach($output['item_list'] as $val) { ?>
    <tr class="bd-line">
      <td class="tc"><input type="checkbox" class="checkitem tc" value="<?php echo $val['goods_id']; ?>"/></td>
     <!-- <td><?php echo $i++; ?></td>-->
	  <td><img src="<?php echo empty($val["goods_image"])?$output["default_img_url"]:$val["goods_image"];?>" width="60" height="60" onMouseOver="toolTip('<img src=<?php echo empty($val["goods_image"])?$output["default_img_url"]:$val["goods_image"];?>>')" onMouseOut="toolTip()"/></td>
      <td><div style="width:150px"><?php echo $val["goods_id"] ?></div></td>
      <td><div style="width:100px"><?php echo $val["goods_sn"] ?></div></td>
      <td>  <?php echo $val["goods_name"] ?></td>
      <td> <?php echo $val["product_type"] ?></td>
      <td> <?php echo  $val["cat_type"] ?></td>
      <td><?php echo paramsHelper::echoOptionText("put_type",$val["put_in_type"]);  ?></td>
      <td><?php echo paramsHelper::echoOptionText("is_on_sale",$val["is_on_sale"]); ?></td>
      <td> <?php echo $val["order_sn"] ?> </td>
      <td><div style="width:150px"><?php echo $val["warehouse"] ?></div> </td>
     <td> <?php echo $val["prc_name"];?> </td>
      <td> <?php echo $val["num"] ?></td>
      <?php if($output['show_chengben']){?>
      <td> <?php echo $val["jijiachengben"] ?></td>
      <?php }?>
      <td> <?php echo $val["caizhi"].''.$val["jinse"] ?></td>
      <td> <?php echo $val["jinzhong"] ?></td>
      <td> <?php echo $val["shoucun"] ?></td>
      <td> <?php echo paramsHelper::echoOptionText("tuo_type",$val["tuo_type"]); ?></td>
      <td> <?php echo $val["zuanshidaxiao"] ?></td>
      <td> <?php echo $val["zhushilishu"] ?></td>
      <td> <?php echo $val["zhengshuhao"] ?></td>
      <td> <?php echo $val["zhengshuleibie"] ?></td>
      <td> <?php echo $val["pinpai"] ?></td>
      <td> <?php echo $val["zhushixingzhuang"] ?></td>
      <td> <?php echo $val["zhushiyanse"] ?></td>
      <td> <?php echo $val["zhushijingdu"] ?></td>
      <td> <?php echo $val["zhushiqiegong"] ?></td>
      <td> <?php echo $val["fushi"] ?></td>
      <td> <?php echo $val["fushilishu"] ?></td>
      <td> <?php echo $val["shi2"] ?></td>
      <td> <?php echo $val["shi2lishu"] ?></td>
      <td> <?php echo $val["shi2zhong"] ?></td>
      <td><?php echo $val["shi3"] ?></td>
      <td><?php echo $val["shi3lishu"] ?></td>
      <td><?php echo $val["shi3zhong"] ?></td>
      <td><?php echo paramsHelper::echoOptionText("weixiu_status",$val["weixiu_status"]);?></td>
	  <td><?php echo $val['weixiu_warehouse_name'];?></td>
	  <td><?php echo $val['update_time'];?></td>
      <!--<td>
         <a href="index.php?act=store_goods_items&op=view_logs&goods_id=<?php echo $val['goods_id'];?>&keepThis=true&TB_iframe=true&height=500&width=920" title="查看货品日志" class="ncbtn-mini thickbox"><i></i>货品日志</a>
	  </td>-->
    </tr>
    <?php } ?>
    <?php } else { ?>
    <tr>
      <td colspan="10" class="norecord">
	  <div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div>
	  </td>
    </tr>
    <?php } ?>
  </tbody>
</table>
</div>
<div class="pagination"> <?php echo $output['show_page']; ?> </div>
<script>
    //重置
    function choseReset(){
		//document.getElementById("searchForm").reset()
		<?php if(isset($_GET['status'])){ ?>
		 window.location.href = "index.php?act=store_goods_items&op=index&status=<?php echo $_GET['status'] ?>";
		<?php }else{ ?>
		window.location.href = "index.php?act=store_goods_items&op=index";
		<?php } ?>
		$(".chose").val('').trigger("chosen:updated"); 
	}
    $(function(){
		//下拉美化（可搜索）
		$('.chose').chosen();
		
		
        $('a[nctype="batch"]').click(function(){
            if($('.checkitem:checked').length == 0){    //没有选择
                showDialog('请选择需要操作的记录');
                return false;
            }
            var _items = '';
            $('.checkitem:checked').each(function(){
                _items += $(this).val() + ',';
            });
            _items = _items.substr(0, (_items.length - 1));

            var data_str = '';
            eval('data_str = ' + $(this).attr('data-param'));
            if(data_str.sign == "print"){
                window.open(data_str.url + '&_ids=' + _items);
            }else if(data_str.sign == "unbind"){
                $.post(data_str.url,{_ids:_items,status:data_str.status,form_submit:'ok'},function(res){
                    var res = $.parseJSON(res);
                    if(res) {
                        showDialog("操作成功！",'succ','',function(){window.location.reload()},'','','','','','',2);
                    }else{
                        showError("解绑失败！");
                    }
                })
            }else{
                $.post(data_str.url,{items:_items,status:data_str.status,form_submit:'ok'},function(res){
                    var res = $.parseJSON(res);
                    if(res.msg == '操作成功') {
                        showDialog(res.msg,'succ','',function(){window.location.reload()},'','','','','','',2);
                    }else{
                        showError(res.msg);
                    }
                });
            }
        });

        var formID = 'searchForm';
        $("#"+formID+" select[name='house_id']").change(function(){
            init_box_select($(this).val());
        });
        function init_box_select(house_id,box_id=""){
            var boxSelectObj = $("#"+formID+" select[name='box_id']");
            var str_options='<option value="">请选择...</option>';
            if(house_id==""){
                boxSelectObj.html(str_options);
                return;
            }
            $.getJSON('index.php?act=store_warehouse&op=get_box_list_ajax&house_id=' +house_id , function(data){
                if (data) {
                    for(var i=0;i<data.length;i++){
                        var selected = data[i]['box_id']==box_id?' selected':'';
                        str_options +='<option value="'+data[i]['box_id']+'"'+selected+'>'+data[i]['box_name']+'</option>';
                    }
                    boxSelectObj.html(str_options);
                }
                boxSelectObj.html(str_options);
            });
        }
        <?php if(!empty($_GET['house_id'])){?>
        init_box_select('<?php echo $_GET['house_id']?>','<?php echo $_GET['box_id']?>');
        <?php }?>
		
		
		$("#download").click(function(){
			var goods_id = $("#searchForm [name='goods_id']").val();
			var warehouse_id = $("#searchForm [name='warehouse_id']").val();
			var put_in_type = $("#searchForm [name='put_in_type']").val();
			var weixiu_status = $("#searchForm [name='weixiu_status']").val();
			var goods_sn = $("#searchForm [name='goods_sn']").val();
			var xilie = $("#searchForm [name='xilie']").val();
			var status = $("#searchForm [name='status']").val();
			var product_type = $("#searchForm [name='product_type']").val();
			var goods_name = $("#searchForm [name='goods_name']").val();
			var cat_type = $("#searchForm [name='cat_type']").val();
			var order_sn = $("#searchForm [name='order_sn']").val();
			var is_bind_order = $("#searchForm [name='is_bind_order']").val();
			var cart_min = $("#searchForm [name='cart_min']").val();
			var cart_max = $("#searchForm [name='cart_max']").val();
			var zhengshuhao = $("#searchForm [name='zhengshuhao']").val();
			var zhengshuleibie = $("#searchForm [name='zhengshuleibie']").val();
			var tuo_type = $("#searchForm [name='tuo_type']").val();
			var zhiquan_min = $("#searchForm [name='zhiquan_min']").val();
			var zhiquan_max = $("#searchForm [name='zhiquan_max']").val();
			var yanse = $("#searchForm [name='yanse']").val();
			var jingdu = $("#searchForm [name='jingdu']").val();
			var caizhi = $("#searchForm [name='caizhi']").val();
			var jinzhong_min = $("#searchForm [name='jinzhong_min']").val();
			var jinzhong_max = $("#searchForm [name='jinzhong_max']").val();
			var jinse = $("#searchForm [name='jinse']").val();
			
			if(!goods_id && !warehouse_id && !put_in_type && !weixiu_status && !goods_sn && !xilie && !status && !product_type && !goods_name && !cat_type && !order_sn && !is_bind_order && !cart_min && !cart_max && !zhengshuhao && !zhengshuleibie && !tuo_type && !zhiquan_min && !zhiquan_max && !yanse && !jingdu && !caizhi && !jinzhong_min && !jinzhong_max && !jinse){
				if(!confirm('没有导出限制可能会消耗较长的时间，点击‘确定’继续！')){
					return false;
				}	
			}
			var args = "";
			args += "&goods_id="+goods_id;
			args += "&warehouse_id="+warehouse_id;
			args += "&put_in_type="+put_in_type;
			args += "&weixiu_status="+weixiu_status;
			args += "&goods_sn="+goods_sn;
			args += "&xilie="+xilie;
			args += "&status="+status;
			args += "&product_type="+product_type;
			args += "&goods_name="+goods_name;
			args += "&cat_type="+cat_type;
			args += "&order_sn="+order_sn;
			args += "&is_bind_order="+is_bind_order;
			args += "&cart_min="+cart_min;
			args += "&cart_max="+cart_max;
			args += "&zhengshuhao="+zhengshuhao;
			args += "&zhengshuleibie="+zhengshuleibie;
			args += "&tuo_type="+tuo_type;
			args += "&zhiquan_min="+zhiquan_min;
			args += "&zhiquan_max="+zhiquan_max;
			args += "&yanse="+yanse;
			args += "&jingdu="+jingdu;
			args += "&caizhi="+caizhi;
			args += "&jinzhong_min="+jinzhong_min;
			args += "&jinzhong_max="+jinzhong_max;
			args += "&jinse="+jinse;
			console.log(args);
			location.href = "index.php?act=store_goods_items&op=download" + args;
			
		})
		
    });
</script>