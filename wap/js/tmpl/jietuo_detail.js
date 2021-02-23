var key = getCookie('key');
if(!key){
	 window.location.href = WapSiteUrl+'/login.html';
}	   
// _开头的变量表示 GET 参数 
var _goods_sn = getQueryString('goods_sn');
var _style_sn = getQueryString('style_sn');
var _cert_id = getQueryString('cert_id');//
if(_goods_sn){
	var arr = _goods_sn.split("-");
	_style_sn = _style_sn?_style_sn:arr[0];
}
var wrapId = 'wrap_id1';//当前操作wapID
var goodsData = new Array();
//挑选，确认商品
var dingzhiSaveBtn = false;
$(function(){
	   
	 $(".nav-sel-jietuo").click(function(){							 
		window.location.href = WapSiteUrl+'/tmpl/jietuo_list.html?goods_sn='+_goods_sn+"&cert_id="+_cert_id;						 
	 });
	 $(".nav-sel-dia").click(function(){		
		if(_cert_id){
			sessionStorageAdd("jietuo_cert_id",false,true); 
			window.location.href = WapSiteUrl+'/tmpl/diamond_detail.html?goods_sn='+_goods_sn+"&cert_id="+_cert_id;		
		}else{
			window.location.href = WapSiteUrl+'/tmpl/diamond.html?goods_sn='+_goods_sn+"&cert_id="+_cert_id;
		}
	 });
	 //is_first_jietuo 是否 先戒托下单 
	 if(_cert_id){
		  sessionStorageAdd("jietuo_cert_id",_cert_id,true); 
	 }	 
	 initStyleGoodsInfo(_goods_sn,_cert_id,"wrap_id1","wrap_id2");

	 $("#wrap_id1 #add_cart,#wrap_id2 #add_cart").click(function(){
		  wrapId = $(this).attr('wrap-id');				   
		  var flag = true;									   
		  var postData = checkFormData(wrapId);
		  if(postData != false){		 
			   dingzhiSaveBtn = "add_cart";
			   flag = initDingzhiForm(wrapId);
		  }else{
			   flag = false; 
		  }
		  return flag;
	 });	 
	 $("#wrap_id1 #next_buy,#wrap_id2 #next_buy").click(function(){
		  wrapId = $(this).attr('wrap-id');
		  var flag = true;									   
		  var postData = checkFormData(wrapId);
		  if(postData !=false){		 
			   dingzhiSaveBtn = "next_buy";
			   flag = initDingzhiForm(wrapId);
		  }else{
			   flag = false; 
		  }
		  return flag;
	 });
	 $("#style_form .save").click(function(){						   
		  var postData = checkFormData(wrapId);
		  if(!postData){		
			  return false;
		  }
		  if(!styleGoodsSave(wrapId)){
			   return false; 
		  }
		  if(dingzhiSaveBtn=="add_cart"){			  
			  alert("添加成功！");
			  $(".dialog-dingzhi-close").click();					 
		  }else{
			  alert("未知按钮事件"); 
		  }
										 
	 });
	 //对比添加
	 $("#wrap_id1 #add_contrast,#wrap_id2 #add_contrast").click(function(){
		 var wrapId = $(this).attr('wrap-id');
	     var goods_sn = goodsData[wrapId].goods_sn;
		 var jietuoContrastList = sessionStorageGet("jietuoContrastList");
		 if(jietuoContrastList ==false){
			 jietuoContrastList = new Array();
		 }
		 var existsIndex = $.inArray(goods_sn, jietuoContrastList);
		 if(existsIndex==-1){			
			 jietuoContrastList.push(goods_sn);
			 sessionStorageAdd("jietuoContrastList", jietuoContrastList, true);
			 alert("添加成功！");
		 }else{
			 alert("对比已添加！");	
		 }
	 });
	 
});

 //initDiamondInfoById(cert_id);
 function initDiamondInfoById(cert_id,wrapId){
	 wrapId = wrapId?wrapId:'wrap_id1';
	 if(!cert_id){
		 diamond_exists = false;
		 return false;
	 }else{
		 $("#"+wrapId+" #next_buy").hide();
	 }
	 var style_sn = goodsData[wrapId].style_sn;
	 var goods_sn = goodsData[wrapId].goods_sn;
	 $.ajax({
		 url:ApiUrl+"/index.php?act=diamond&op=get_diammond_info",
		 type:'post',
		 data:{cert_id:cert_id,key:key},
		 dataType:'json',
		 success:function(result){
			 if(result.code ==200){
				  diamond_exists = true;
				  var datas = {};
				  datas['diamond_info'] = result.datas.diamond_info;
				  datas['goods_info'] = {goods_sn:goods_sn,style_sn:style_sn};
				  var html = template.render('diamond-info-content',datas);     
				  $("#"+wrapId+" #diamond_info").html(html);					 
			 }else{
				 diamond_exists = false;						 
				 alert(result.datas.error);
			 }
		 }
	 });	
 }	 
 
 function initStyleGoodsInfo(goods_sn,cert_id,wrapId,loadWrapId){
	 wrapId = wrapId?wrapId:'wrap_id1';
	 $.ajax({
		 url:ApiUrl+"/index.php?act=style_goods&op=get_style_goods_detail",
		 type:'post',
		 data:{goods_sn:goods_sn,cert_id:cert_id,key:key},
		 dataType:'json',
		 success:function(result){
			 if(result.code ==200){
				 var html = template.render('goods-attr-list',result.datas); 
				 $("#"+wrapId+" #goods_attr").html(html);
				 
				 var data = result.datas.goods_info;
				 
				 if(data.xilie_img_url !=''){
					$("#xilie_url").attr('src',data.xilie_img_url); 
				 }
				 
				 goodsData[wrapId] = data;
				 //天生一对 款式信息
				 var style_sn  = data.style_sn;
				 var goods_sn  = data.goods_sn;
				 var tsyd_style_sn = data.tsyd_style_sn;
				 var tsyd_cert_id  = data.tsyd_cert_id;
				 var tsyd_goods_sn = data.tsyd_goods_sn;
				
				 
				 $("#"+wrapId+" #goods_title").html(data.style_name);
				 //$("#"+wrapId+" #goods_content").html(data.goods_content);
				 //$("#delivery_tip").html(result.datas.delivery_tip);	
				 
				 $("#"+wrapId+" #goods_price").html("￥"+data.goods_price);	
				 if(tsyd_goods_sn && data.style_sex){	
					 if(data.style_sex==1){
						 $("#"+wrapId+" #style_sex").html("男款");	
					 }else if(data.style_sex==2){
						 $("#"+wrapId+" #style_sex").html("女款");
					 }
				 }
				 

				 initDiamondInfoById(cert_id,wrapId);

				 //$("#style_form input[name='xiangkou']").val(data.xiangkou);
				/* $("#"+wrapId+" .dropdown .text").click(function(){
					 if($(this).parents(".dropdown").find(".dropdown-menu").css("display")=="block"){
						 $(this).parents(".dropdown").find(".dropdown-menu").hide();
					 }else{
						 $(this).parents(".dropdown").find(".dropdown-menu").show();
					 }
				 });*/
				 $("#"+wrapId+" .dropdown-menu a").click(function(){
					 var html=$(this).html();
					 $(this).parents(".dropdown").find(".text").html(html);
					 $(this).parents(".dropdown").find(".text").attr('data-val',$(this).attr('data-val'));
					 $(this).parents(".dropdown").find(".text").attr('data-name',$(this).attr('data-name'));
					// $(".dropdown-menu").hide();
				 });
				 
				 $(".jtDetail_top .nav-link").click(function(){
					 var type=$(this).attr("data-type");
					 $(".jtDetail_bottom").find("."+type).show().siblings().hide();
				 });
				 var jinse = data.caizhi+'|'+data.yanse;
				 goodsData[wrapId].jinse = jinse;
				 $("#"+wrapId+" #jinseSelect .dropdown-menu a").each(function(){																                         $(this).attr('wrap-id',wrapId);
						 if($(this).attr("data-val")==jinse){
							 $(this).parents(".dropdown").find(".text").html($(this).html());
							 $(this).parents(".dropdown").find(".text").attr('data-val',$(this).attr('data-val'));
							 $(this).parents(".dropdown").find(".text").attr('data-name',$(this).attr('data-name'));
						 }
				 });
				 var shoucun  = data.shoucun;
				 var xiangkou = data.xiangkou;
				 
				 $("#"+wrapId+" #shoucunSelect .dropdown-menu a").each(function(){	
						 $(this).attr('wrap-id',wrapId);														
						 if($(this).attr("data-val")/1==shoucun/1){
							 $(this).parents(".dropdown").find(".text").html($(this).html());
							 $(this).parents(".dropdown").find(".text").attr('data-val',$(this).attr('data-val'));
							 $(this).parents(".dropdown").find(".text").attr('data-name',$(this).attr('data-name'));
						 }										 
				 });
				 $("#"+wrapId+" #xiangkouSelect .dropdown-menu a").each(function(){
						 $(this).attr('wrap-id',wrapId);														 
						 if($(this).attr("data-val")/1==xiangkou/1){
							 $(this).parents(".dropdown").find(".text").html($(this).html());
							 $(this).parents(".dropdown").find(".text").attr('data-val',$(this).attr('data-val'));
							 $(this).parents(".dropdown").find(".text").attr('data-name',$(this).attr('data-name'));
						 }										 
				 });
				 //镶口为0，视为成品
				 if(xiangkou==0 || _cert_id!=''){
					  $("#"+wrapId+" #next_buy").hide();						  
				 }
				 $("#"+wrapId+" #xiangkouSelect .dropdown-menu a").click(function(){
						 var wrapId = $(this).attr('wrap-id');  													  
						 var jinseArr = $("#"+wrapId+" #jinseSelect").find(".text").attr('data-val').split("|");						 
						 var caizhi = jinseArr[0];
						 var yanse  = jinseArr[1];
						 //shoucun = $("#xiangkouSelect").find(".text").attr('data-val');
						 xiangkou = $(this).attr('data-val');	
						 initStyleGoodsPrice(style_sn,caizhi,yanse,xiangkou,shoucun,wrapId);
				 });
				 $("#"+wrapId+" #jinseSelect .dropdown-menu a").click(function(){														   
					 var wrapId = $(this).attr('wrap-id');													   
					 var jinse = $(this).attr('data-val');
					 var jinseArr = jinse.split("|");
					 var caizhi = jinseArr[0];
					 var yanse  = jinseArr[1];
					 //var shoucun = $("#shoucunSelect").find(".text").attr('data-val');	
					 goodsData[wrapId].jinse = jinse;
					 initStyleGoodsPrice(style_sn,caizhi,yanse,xiangkou,shoucun,wrapId);
				 });
				 $("#"+wrapId+" #shoucunSelect .dropdown-menu a").click(function(){
						 var wrapId = $(this).attr('wrap-id');															 
						 var jinseArr = $("#"+wrapId+" #jinseSelect").find(".text").attr('data-val').split("|");
						 var caizhi = jinseArr[0];
						 var yanse  = jinseArr[1];
						 shoucun = $(this).attr('data-val');	
						 initStyleGoodsPrice(style_sn,caizhi,yanse,xiangkou,shoucun,wrapId);
				 });
				 //保存刻字
				 $("#"+wrapId+" #kezi_ipt").change(function(){					
					 sessionStorageAdd(wrapId+"_jietuo_kezi",$(this).val(),true);
				 });
				 var kezi = sessionStorageGet(wrapId+"_jietuo_kezi");
				 //当在 钻和托 组合页面，自动填充刻字内容
				 if(kezi && _cert_id){
					$("#"+wrapId+" #kezi_ipt").val(kezi);
				 }
				 
				 $("#"+wrapId+" .jt_kezi em").click(function(){
						 var this_=$(this).attr("data-val");
						 var ipt = $("#"+wrapId+" #kezi_ipt").val();
						 var kezi = ipt+this_;
						 var res = checkKezi(kezi);						 
						 if(res.error){
							  alert(res.error);
							  return false;
						 }
						 var keziHtml = res.data;
						 $("#"+wrapId+" .showKZ").html(keziHtml);
						 
						 $("#kezi").val(kezi);
						 $("#"+wrapId+" #kezi_ipt").val(kezi);	
				 });
				 $("#"+wrapId+" #kezi_ipt").blur(function(){
					 var kezi = $(this).val();					  
					 var res = checkKezi(kezi);	
					 var keziHtml = res.data;
					 $("#"+wrapId+" .showKZ").html(keziHtml);
					 if(res.error){
						  alert(res.error);
						  return false;
					 }					  
					 $("#kezi").val(kezi);
				 });

				 initStyleGallery(style_sn,wrapId);
				 if(tsyd_goods_sn && loadWrapId){
					  $("#"+loadWrapId).show();
					  initStyleGoodsInfo(tsyd_goods_sn,tsyd_cert_id,loadWrapId,false);
					  initStyleGallery(tsyd_style_sn,"wrap_id2");
				 }                 
			 }else{		
			     $("#"+wrapId+" #goods_title").html("商品不可销售");
				 initStyleGallery(_style_sn,wrapId);
			 }
		 }
	 });
	 
 }
 
 function initStyleGoodsPrice(style_sn,caizhi,yanse,xiangkou,shoucun,wrapId){
	 wrapId = wrapId?wrapId:'wrap_id1';
	 if(xiangkou==0){
		 $("#"+wrapId+" #next_buy").hide();						  
	 }else{
		 if(_cert_id ==''){
			 $("#"+wrapId+" #next_buy").show();
		 }else{
			 $("#"+wrapId+" #next_buy").hide();
		 }
	 }
	 $.ajax({
		 url:ApiUrl+"/index.php?act=style_goods&op=get_style_goods_price",
		 type:'post',
		 data:{style_sn:style_sn,caizhi:caizhi,yanse:yanse,xiangkou:xiangkou,shoucun:shoucun,key:key},
		 dataType:'json',
		 success:function(result){
			 if(result.code ==200){
				 style_goods_exists = true;
				 var data = result.datas.goods_info;
				 goodsData[wrapId].goods_sn = data.goods_sn;
				 goodsData[wrapId].xiangkou = data.xiangkou;
				 goodsData[wrapId].shoucun = shoucun;
				 goodsData[wrapId].goods_price = data.goods_price;
				 $("#"+wrapId+" #goods_price").html("￥"+data.goods_price);				 
				 $("#"+wrapId+" #reSearchDia").attr('href','../tmpl/diamond.html?goods_sn='+data.goods_sn);	
			 }else{	
				 style_goods_exists = false;	
				 alert(result.datas.error);
			 }
		 }
	  });
 }
 function checkFormData(wrapId){
	  wrapId = wrapId?wrapId:'wrap_id1';	  
	  var style_goods_exists = goodsData[wrapId]?true:false;
	  if(style_goods_exists==false){
		  alert("商品不可销售");
		  return false;
	  }	  
	  
	  var diamond_exists = goodsData[wrapId].cert_id?true:false;	 
	  if(_cert_id && diamond_exists == false){
		 alert("商品不可销售"); 
		 return false;
	  }	  
	  
	  var cert_id = goodsData[wrapId].cert_id;
	  var goods_sn = goodsData[wrapId].goods_sn;
	  var data = {};
	  if(cert_id && _cert_id){
		  data['goods_id'] = goods_sn+'@'+cert_id;
		  data['quantity'] = '1@1';
		  data['goods_type'] = '1@2';
	  }else{
		  data['goods_id'] = goods_sn;
		  data['quantity'] = '1';
		  data['goods_type'] = '1';
	  }
	  return data;
	 
 }

 function initDingzhiForm(wrapId){
	 wrapId = wrapId?wrapId:'wrap_id1';
	 var formId = 'style_form';
	 if(!checkFormData()){
		  return false;
	 }
	 var style_sn = goodsData[wrapId].style_sn;
	 var goods_sn = goodsData[wrapId].goods_sn;
	 var cert_id  = goodsData[wrapId].cert_id;
	 if(dingzhiSaveBtn == "next_buy" && (!cert_id || !_cert_id)){
		 window.location.href = WapSiteUrl + '/tmpl/diamond.html?goods_sn='+goods_sn;
		 return true;
	 }
	 $("#kezi").val($("#"+wrapId+" #kezi_ipt").val());	 
	 $.ajax({
			 url:ApiUrl+"/index.php?act=style_goods&op=get_style_goods_diy_index",
			 type:'post',
			 data:{style_sn:style_sn,goods_sn:goods_sn,key:key},
			 dataType:'json',
			 async: true,
			 success:function(result){
				 if(result.code ==200){
					 dingzhiFormLoad = true;
					 var html = template.render('dingzhi-form',result.datas); 
					 $("#"+formId+" .dingzhi-body").html(html);
					 
					 $("#"+formId+" select[name='is_dingzhi']").change(function(){
						 if($(this).val()==1){							  
							 //$(".xianhuo_adds").hide();
						 }else{
							 //$(".xianhuo_adds").show();
						 }
						 setStyleFormState();
					 });
					 $("#"+formId+" select[name='xiangkou']").change(function(){						
						  setStyleFormState();
					 });	
					 //goods_price = $("#"+wrapId+" #goods_price").attr('data-val');
					 //$("#"+formId+" input[name='goods_price']").val(goods_price);
					 
					 $("#kezi").val($("#"+wrapId+" #kezi_ipt").val());						
					 $(".jt_kezi2 em").click(function(){													  
						 var this_=$(this).attr("data-val");
						 var ipt=$("#kezi").val();
						 var kezi = ipt+this_;
						 var res = checkKezi(kezi);						 
						 if(res.error){
							  alert(res.error);
							  return false;
						 }
						 var keziHtml = res.data;
						 $("#"+formId+" .showKZ").html(keziHtml);
						 $("#kezi").val(kezi);
						 $("#"+wrapId+" #kezi_ipt").val(kezi);	
					 });					 
					 $("#kezi").blur(function(){
						 var kezi = $(this).val();					  
						 var res = checkKezi(kezi);	
						 var keziHtml = res.data;
						 $("#"+formId+" .showKZ").html(keziHtml);
						 if(res.error){
							  alert(res.error);
							  return false;
						 }					  
						 $("#"+wrapId+" #kezi_ipt").val(kezi);	
					 });
					 $("#"+wrapId+" #kezi_ipt,#kezi,#"+formId+" input[name='carat']").blur(function(){
						setStyleFormState();				  
					 });
					//$("#"+formId+" select[name='xiangkou']").val(xiangkou);
					$("#"+formId+" select[name='tuo_type']").change(function(){						 
						  if($(this).val()==1){
							   var xiangkou = $("#"+formId+" select[name='xiangkou']").val();
							   if(xiangkou>0){
								   $("#"+formId+" input[name='carat']").val(xiangkou);
							   }
						  }
						  setStyleFormState();
				     });
					 $("#"+formId+" select[name='xiangkou']").change(function(){
						  var xiangkou = $(this).val();												  
						  if(xiangkou >0){
							   var tuo_type = $("#"+formId+" select[name='tuo_type']").val();
							   if(tuo_type == 1){
								   $("#"+formId+" input[name='carat']").val(xiangkou);
							   }
						  }
				     });
					 if(_cert_id !=''){							
						initGoodsDiamondFlag = false;												  
						initGoodsDiamondInfo(formId,cert_id);
						$("#"+formId+" select[name='tuo_type']").attr('disabled',true);
						$("#"+formId+" select[name='xiangkou']").attr('disabled',true);
						$("#"+formId+" input[name='cert_id']").attr('disabled',true).val(cert_id);
						
					}
					 //加载证书号失去焦点事件
					$("#"+formId+" input[name='cert_id']").blur(function(){
						 initGoodsDiamondFlag = false;												  
						 initGoodsDiamondInfo(formId,$(this).val());	 
						 setStyleFormState();
					});//
					 //加载成品定制价格方案事件
					 initCpdzPriceSearchEvent(formId,wrapId);
					 //加载取价按钮点击事件
					 initDingzhiPriceEvent(formId,wrapId);
	                 
					 setStyleFormState();

				 }else{	
					 alert(result.datas.error);				   
				 }
			 }
	  });
 }
// 
function getStyleFormData(wrapId){
	 wrapId = wrapId?wrapId:'wrap_id1';
	 var formId = "style_form";
	 var goods_id = goodsData[wrapId].goods_sn;
	 var style_sn = goodsData[wrapId].style_sn;	 
	 var jinse  = goodsData[wrapId].jinse;
	 var xiangkou = goodsData[wrapId].xiangkou;
	 var shoucun = goodsData[wrapId].shoucun;
	 var tuo_type = $("#"+formId+" select[name='tuo_type']").val();
	 var is_dingzhi = $("#"+formId+" select[name='is_dingzhi']").val();
	 var xiangqian = $("#"+formId+" select[name='xiangqian']").val();
	 var xiangkou = $("#"+formId+" select[name='xiangkou']").val();
	 var cert_id = $("#"+formId+" input[name='cert_id']").val();
	 var carat  = $("#"+formId+" input[name='carat']").val();
	 var zhushi_num = $("#"+formId+" input[name='zhushi_num']").val();
	 var cert = $("#"+formId+" select[name='cert']").val();
	 var cert_id = $("#"+formId+" input[name='cert_id']").val();
	 var color = $("#"+formId+" select[name='color']").val();
	 var clarity = $("#"+formId+" select[name='clarity']").val();
	 var cut = $("#"+formId+" select[name='cut']").val();
	 var facework = $("#"+formId+" select[name='facework']").val();		
	 var kezi = $("#"+formId+" input[name='kezi']").val();
	 var goods_price = $("#"+formId+" input[name='goods_price']").val();
	 var cpdz_code = $("#"+formId+" input[name='cpdz_code']").val();
	 var policy_id = $("#"+formId+" input[name='policy_id']").val();
	 var goods_type = 1;//戒托
	 var xianhuo_adds = '';
	 $("#"+formId+' input[name="xianhuo_adds"]:checked').each(function(){
		xianhuo_adds = $(this).val()+",";
	 }); 
	 if(is_dingzhi==1){
		xianhuo_adds = '';	
	 } 	 
	 if(xiangkou>0 && carat >0 && tuo_type==1){
		 is_cpdz = 1; 
	 }else{
		 is_cpdz = 0; 
	 }
	var data = {};
	data['goods_id'] = goods_id;
	data['style_sn'] = style_sn;
	data['jinse'] = jinse;
	data['goods_type'] = goods_type;
	data['is_dingzhi'] = is_dingzhi;
	data['tuo_type'] = tuo_type;
	data['xiangqian'] = xiangqian;
	data['cert'] = cert;
	data['cert_id'] = cert_id;
	data['xiangkou'] = xiangkou;
	data['shoucun'] = shoucun;
	data['carat'] = carat;
	data['zhushi_num'] = zhushi_num;
	data['color'] = color;
	data['clarity'] = clarity;
	data['cut'] = cut;
	data['facework'] = facework;
	data['goods_price'] = goods_price;
	data['is_cpdz'] = is_cpdz;
	data['kezi'] = kezi;
	data['xianhuo_adds'] = xianhuo_adds;
	data['policy_id'] = policy_id;
	var res = checkKezi(kezi);
	if(res.error){
		alert(res.error);
	    return false;	
	}
	return data;
	 
}
	 
function setStyleFormState(){
	var formId = "style_form";
	var zhushi_num  = $("#"+formId+" input[name='zhushi_num']").val();
	if(zhushi_num==0){
		$("#"+formId+" select[name='xiangkou']").prop("disabled",true).val("0.00");
		$("#"+formId+" select[name='tuo_type']").prop("disabled",true).val(1);
		$("#"+formId+" input[name='carat']").prop("disabled",true).val('0');
	}
	var is_dingzhi = $("#"+formId+" select[name='is_dingzhi']").val();	
	var goods_type = 1;
	var tuo_type   = $("#"+formId+" select[name='tuo_type']").val();
	var carat  = $("#"+formId+" input[name='carat']").val();
	var xiangkou  = $("#"+formId+" select[name='xiangkou']").val();
	var kezi  = $("#"+formId+" input[name='kezi']").val();	
	var xiangqian  = $("#"+formId+" select[name='xiangqian']").val();
	if(is_dingzhi==1){
		 $("#"+formId+" .show_msg").html("").hide();
		 if(tuo_type ==1){				  
			  $("#"+formId+" select[name='xiangqian']").prop("disabled",true).val("工厂配钻，工厂镶嵌");
			  $("#"+formId+" input[name='cert_id']").prop("disabled",false).val("");
			  $("#"+formId+" select[name='cut']").prop("disabled",false);			  
		 }else{
			  $("#"+formId+" select[name='xiangqian']").prop("disabled",false).val("需工厂镶嵌");
			  $("#"+formId+" input[name='cert_id']").prop("disabled",false);
			  $("#"+formId+" select[name='cut']").prop("disabled",false);
		 }
		 $("#"+formId+" select[name='tuo_type']").prop("disabled",false);	
		 $("#"+formId+" select[name='facework']").prop("disabled",false);
		 
		 $("#"+formId+" .create_price").show();  
		 //$("#"+formId+" .xianhuo_adds").hide();		 
		 if(xiangkou ==0){
			 $("#"+formId+" select[name='xiangqian']").prop("disabled",false).val("不需工厂镶嵌");	
			 $("#"+formId+" select[name='tuo_type']").prop("disabled",true).val(1);	
		 }
		 
	}else{		
			  
		$("#"+formId+" .show_msg").html("现货，不需生产").show();
		$("#"+formId+" select[name='xiangqian']").prop("disabled",false).val("不需工厂镶嵌");
		//$("#"+formId+" select[name='tuo_type']").prop("disabled",true);
		//$("#"+formId+" select[name='facework']").prop("disabled",true);	
		
		$("#"+formId+" input[name='xianhuo_adds']").prop("disabled",true);
		if(kezi!=''){			
			$("#"+formId+" input[name='xianhuo_adds'][value='刻字']").prop("checked",true);
		}else{
			$("#"+formId+" input[name='xianhuo_adds'][value='刻字']").prop("checked",false);
		}
		xiangqian  = $("#"+formId+" select[name='xiangqian']").val();
		var xqArray = ["需工厂镶嵌","客户先看钻再返厂镶嵌","工厂配钻，工厂镶嵌"];
		if(carat > 0 && tuo_type >1 && $.inArray(xiangqian,xqArray)>=0){
			$("#"+formId+" input[name='xianhuo_adds'][value='镶石']").prop("checked",true);
		}else{
			$("#"+formId+" input[name='xianhuo_adds'][value='镶石']").prop("checked",false);
		}
	}
	
	if(cert_id !='' && cert_id == _cert_id){			 
		 $("#"+formId+" select[name='tuo_type']").prop("disabled",true);
		 $("#"+formId+" select[name='xiangkou']").prop("disabled",true);
	}else{
		 $("#"+formId+" select[name='xiangkou']").prop("disabled",false);
	}
	if(tuo_type == 1 && xiangkou>0){
		$("#"+formId+" input[name='cert_id']").prop("disabled",true).val("");
		$("#"+formId+" select[name='cut']").prop("disabled",true);
		
		//成品定制  禁用 裸石 select控件
		$("#"+formId+" .hideBox").show();
		$("#"+formId+" select[name='cert']").prop("disabled",true).addClass('none-event');
		$("#"+formId+" select[name='color']").prop("disabled",true).addClass('none-event');
		$("#"+formId+" select[name='clarity']").prop("disabled",true).addClass('none-event');
	}else if(xiangkou>0){
		 //非成品定制 解除禁用 裸石select控件
		 $("#"+formId+" .hideBox").hide();
		 $("#"+formId+" select[name='cert']").prop("disabled",false).removeClass('none-event');
		 $("#"+formId+" select[name='color']").prop("disabled",false).removeClass('none-event');
		 $("#"+formId+" select[name='clarity']").prop("disabled",false).removeClass('none-event');		
	}
	
	if(zhushi_num<1){
		$("#"+formId+" select[name='xiangkou']").prop("disabled",true).val("0.00");
		$("#"+formId+" input[name='cert_id']").prop("disabled",true).val("");
		$("#"+formId+" input[name='carat']").prop("disabled",true).val();
		$("#"+formId+" select[name='cert']").prop("disabled",true).val("");
		$("#"+formId+" select[name='color']").prop("disabled",true).val("");
		$("#"+formId+" select[name='clarity']").prop("disabled",true).val("");
		$("#"+formId+" select[name='xiangqian']").prop("disabled",true).val("成品");
	}
}	
function styleGoodsSave(wrapId){
	 var formData = getStyleFormData(wrapId);	
	 if(!formData){								 
		return false; 
	 }
	 formData['key'] = key;
	 var flag = false;		 
	 //if(formData['is_dingzhi']==1){
		//期货 
		$.ajax({
			 url:ApiUrl+"/index.php?act=style_goods&op=get_dingzhi_price",
			 type:'post',
			 data:formData,
			 dataType:'json',
			 async: false,
			 success:function(result){
				  if(result.code ==200){
					   var data = result.datas.goods_info;
					   goods_id = data.goods_id;
					   goods_sn = data.goods_id;
					   var jsonData = {'data-goods_price':data.goods_price,"data-goods_sn":data.goods_sn,"data-style_sn":data.goods_sn}
				       $("#"+wrapId+" #goods_price").html("￥"+data.goods_price).attr(jsonData);
					   $("#style_form input[name='goods_price']").val(data.goods_price);
					   formData['goods_id'] = data.goods_id;
					   formData['goods_price'] = data.goods_price;
					   style_goods_exists = true;
					   flag = true;
				  }else{
					   //$("#style_form input[name='goods_price']").val(0);
					   flag = false;
					   alert(result.datas.error);	
				  }
			 }
		 });
		 if(flag == true){
			 $.ajax({
					url:ApiUrl+"/index.php?act=member_goods&op=style_goods_add",
					type:'post',
					data:formData,
					dataType:'json',
					async: false,
					success:function(result){
						if(result.code == 200){
							flag = true;
						}else{	
							alert(result.datas.error);	
							flag = false;
						}
					}
			 });
		 }
		 
		 if(flag == true && dingzhiSaveBtn=="add_cart"){
			var cartData = {goods_id:formData['goods_id'],goods_type:formData['goods_type'],quantity:1,key:key};
			if(formData['cert_id'] && formData['cert_id'] ==_cert_id){
				  cartData['goods_id'] = formData['goods_id']+'@'+formData['cert_id'];
				  cartData['quantity'] = '1@1';
				  cartData['goods_type'] = '1@2';
			}
			$.ajax({
					url:ApiUrl+"/index.php?act=member_cart&op=cart_add",
					type:'post',
					data:cartData,
					dataType:'json',
					async: false,
					success:function(result){
						if(result.code == 200){
							$(".dialog-dingzhi-close").click();
						}else{	
							alert(result.datas.error);				   
						}
					}
			 });
		 }

	 //}		 
	 
	 return flag;
	 
 }