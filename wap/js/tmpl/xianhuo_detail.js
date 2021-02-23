 var key = getCookie('key');
 if(!key){
	window.location.href = WapSiteUrl+'/login.html';		
 }   
 
 var goods_sn = getQueryString('goods_sn');
 var style_sn = getQueryString('style_sn');
 var cert_id = _cert_id = getQueryString('cert_id');
 var from_type = getQueryString('from_type');//
 var is_shop = getQueryString('is_shop'); //1门店 2 总部
 
 var is_cpdz = 0;
 var tuo_type = 2;
 var shape = 1;//圆形
 var is_dingzhi;
 var xiangqian;
 var xiangkou;
 var caizhi;
 var yanse_id;
 var cert_id ;
 var carat ;
 var zhushi_num;
 var cert ;
 var color ;
 var clarity ;
 var cut ;
 var facework ;		
 var kezi ;
 var goods_price ;
 var cpdz_code ;
 var goods_type;//
 var xianhuo_adds = "";
 var goods_exists = false;
 var diamond_exists = false; 
 //挑选，确认商品
 var dingzhiSaveBtn  = false;
$(function(){

	 $(".nav-sel-jietuo").click(function(){
		if(xiangkou>0){			
			window.location.href = WapSiteUrl+'/tmpl/jietuo_list.html?goods_sn='+goods_sn+"&style_sn="+style_sn+"&carat="+xiangkou+"&cert_id="+_cert_id;		
		}else{
			window.location.href = WapSiteUrl+'/tmpl/jietuo_list.html?goods_sn='+goods_sn+"&style_sn="+style_sn+"&cert_id="+_cert_id;	

		}
	 });
	 $(".nav-sel-dia").click(function(){
		if(xiangkou>0 && tuo_type!=1){
	       window.location.href = WapSiteUrl+'/tmpl/diamond.html?goods_sn='+goods_sn+"&style_sn="+style_sn+"&carat="+xiangkou+"&cert_id="+_cert_id;
		}
	 });
	 $(".nav >.nav-item >.nav-link").click(function(){												
		 var nav_type = $(this).attr("data-type");		 
		 $(".nav >.nav-item >.nav-link").removeClass("active");
		 $(this).addClass("active");		 
		 $(".jtDetail_bottom > div").hide();	
		 $(".jtDetail_bottom >."+nav_type).show();							
	 });
	 $("#add_cart").click(function(){
		  var flag = true;									   
		  var postData = checkFormData();
		  if(postData != false){		 
			   dingzhiSaveBtn = "add_cart";
			   flag = initDingzhiForm();
		  }else{
			   flag = false; 
		  }
		  return flag;
	 });
	 
	 $("#buy_now").click(function(){
		  var flag = true;									   
		  var postData = checkFormData();
		  if(postData != false){		 
			   dingzhiSaveBtn = "buy_now";
			   flag = initDingzhiForm();
		  }else{
			   flag = false; 
		  }
		  return flag;
	 });
	 
	 $("#next_buy").click(function(){
		  var flag = true;									   
		  var postData = checkFormData();
		  if(postData !=false){		 
			   dingzhiSaveBtn = "next_buy";
			   flag = initDingzhiForm();
		  }else{
			   flag = false; 
		  }
		  return flag;
	 });
	
	 $("#goods_form .save").click(function(){
		  var postData = checkFormData();
		  var goods_type = is_shop==2?6:4;
		  if(!postData){		
			  return false;
		  }
		  if(!goods_save()){
			   return false; 
		  }
		  if(dingzhiSaveBtn=="add_cart"){			  
			  alert("添加成功！");
			  $(".dialog-dingzhi-close").click();					 
		  }else if(dingzhiSaveBtn=="buy_now"){			 
			  if(cert_id && cert_id == _cert_id){				 
				 window.location.href = WapSiteUrl +'/tmpl/order/buy_step1.html?goods_id='+goods_sn+"@"+goods_type+","+cert_id+"@2";
			  }else{
				 window.location.href = WapSiteUrl + '/tmpl/order/buy_step1.html?goods_id='+goods_sn+"@"+goods_type; 
			  }
		  }else if(dingzhiSaveBtn=="next_buy"){
			  if(cert_id && cert_id == _cert_id){				 
				 window.location.href = WapSiteUrl +'/tmpl/order/buy_step1.html?goods_id='+goods_sn+"@"+goods_type+","+cert_id+"@2";
			  }else{
				 window.location.href = WapSiteUrl + '/tmpl/diamond.html?goods_sn='+goods_sn;
			  }
		  }else{
			  alert("未知按钮事件"); 
		  }
		 
										 
	 });
	 //对比添加
	 $("#add_contrast").click(function(){
		  var contrastKey = '';					   
		  if(from_type=="marry_list"){
			  contrastKey = "marryContrastList";
		  }else{
			  contrastKey = "coupleContrastList";			  
		  }
		  var contrastList = sessionStorageGet(contrastKey);
		  if(contrastList ==false){
			 contrastList = new Array();
		  }
		 var existsIndex = $.inArray(goods_sn, contrastList);
		 if(existsIndex==-1){			
			 contrastList.push(goods_sn);
			 sessionStorageAdd(contrastKey, contrastList, true);
			 alert("添加成功！");
		 }else{
			 alert("对比已添加！");	
		 }
	 });
	 initStyleGallery(style_sn);
	 initWarehouseGoodsInfo(goods_sn);
	  
});
 
 var initDiamondInfoByIdFlag = false;
 function initDiamondInfoById(cert_id){
	 if(initDiamondInfoByIdFlag){
		 return ; 
	 }
	 if(!cert_id){
		 diamond_exists = false;
		 return false;
	 }else{
		 $("#next_buy").hide();
	 }
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
				  datas['goods_info'] = {goods_sn:goods_sn,style_sn:style_sn,xiangkou:xiangkou};
				  var html = template.render('diamond-info-content',datas);     
				  $("#diamond_info").html(html);
				  initDiamondInfoByIdFlag = true;
			 }else{
				 diamond_exists = false;
				 alert(result.datas.error);
			 }
		 }
	 });	
 }
 

 function initWarehouseGoodsInfo(goods_sn){
	 var formId= 'goods_form';
	 var is_dingzhi =  $("#"+formId+" select[name='is_dingzhi']").val();
	 var is_shop= getQueryString("is_shop"); //是否为门店货品
	 var goods_type = (is_shop==2)?6:4;
	 $.ajax({
		 url:ApiUrl+"/index.php?act=style_goods&op=get_warehousegoods_info_index",
		 type:'post',
		 data:{goods_id:goods_sn,is_dingzhi:is_dingzhi,key:key,goods_type:goods_type},
		 dataType:'json',
		 success:function(result){
			 if(result.code ==200){

			   
				 var html = template.render('goods-attr-list',result.datas); 
				 $("#goods_attr").html(html);
				 
				 var data = result.datas.goods_info;
				
				 
				 if(data.xilie_img_url !=''){
					$("#xilie_url").attr('src',data.xilie_img_url); 
				 }
				 
				 
			 
				 
				 if(data.goods_price>0){
					goods_exists = true; 
					$("#goods_price").attr({'data-val':data.goods_price,"data-sn":data.goods_id}).html("￥"+data.goods_price);
				 }
				 var goods_name = data.goods_name?data.goods_name+"(现货)":"";
				 $("#goods_title").html(goods_name);
				 //$("#goods_content").html(data.goods_content);
				 //$("#delivery_tip").html(result.datas.delivery_tip);
				 //initStyleGoodsPrice(data.style_sn,data.caizhi,data.yanse,data.xiangkou,data.shoucun);
				 tuo_type = data.tuo_type;
				 if(data.tuo_type==1){
					 $("#next_buy").hide(); 
				 }
				 $("#"+formId+" input[name='xiangkou']").val(data.xiangkou);
				 
				 $('#shoucunSelect.dropdown .text').click(function(){
					 if($(this).parents(".dropdown").find(".dropdown-menu").css("display")=="block"){
						 $(this).parents(".dropdown").find(".dropdown-menu").hide();
					 }else{
						 $(this).parents(".dropdown").find(".dropdown-menu").show();
					 }
				 });
				 
				 $("#shoucunSelect .dropdown-menu a").click(function(){
					 var html=$(this).html();
					 $(this).parents(".dropdown").find(".text").html(html);
					 $(this).parents(".dropdown").find(".text").attr('data-val',$(this).attr('data-val'));
					 $(this).parents(".dropdown").find(".text").attr('data-name',$(this).attr('data-name'));
					 $(".dropdown-menu").hide();
				 });
				 /*
				 $(".jtDetail_top .nav-link").click(function(){
					 var type=$(this).attr("data-type");
					 $(".jtDetail_bottom").find("."+type).show().siblings().hide();
				 });*/
				 jinse = data.zhuchengse;
				 $("#jinseSelect .dropdown-menu a").each(function(){																 
						 if($(this).attr("data-name")==jinse){
							 $(this).parents(".dropdown").find(".text").html($(this).html());
							 $(this).parents(".dropdown").find(".text").attr('data-val',$(this).attr('data-val'));
							 $(this).parents(".dropdown").find(".text").attr('data-name',$(this).attr('data-name'));
						 }
				 });
				 shoucun  = data.shoucun;					 
				 xiangkou = data.xiangkou;
				 $("#shoucunSelect .dropdown-menu a").each(function(){											
						 if($(this).attr("data-val")/1==shoucun/1){
							 $(this).parents(".dropdown").find(".text").html($(this).html());
							 $(this).parents(".dropdown").find(".text").attr('data-val',$(this).attr('data-val'));
							 $(this).parents(".dropdown").find(".text").attr('data-name',$(this).attr('data-name'));
						 }										 
				 });	
				 $("#xiangkouSelect .dropdown-menu a").each(function(){
						 if($(this).attr("data-val")/1==xiangkou/1){
							 $(this).parents(".dropdown").find(".text").html($(this).html());
							 $(this).parents(".dropdown").find(".text").attr('data-val',$(this).attr('data-val'));
							 $(this).parents(".dropdown").find(".text").attr('data-name',$(this).attr('data-name'));
						 }										 
				 });
				 /*
				 $("#jinseSelect .dropdown-menu a").click(function(){
					 jinse = $(this).attr('data-val');
					 var jinseArr = jinse.split("|");
					 var caizhi = jinseArr[0];
					 var yanse  = jinseArr[1];
					 var shoucun = $("#shoucunSelect").find(".text").attr('data-val');						 
					 initStyleGoodsPrice(style_sn,caizhi,yanse,xiangkou,shoucun);
				 });
				 */
				 $("#shoucunSelect .dropdown-menu a").click(function(){												 
					  shoucun = $(this).attr('data-val');
				 });	
				 
				 //dingzhiFormLoad = true;
				 
				 var html = template.render('dingzhi-form',result.datas); 
				 $("#"+formId+" .dingzhi-body").html(html);
				 //initDiamondInfoById(cert_id);
				  //var diamond_info = {};
				  //datas['diamond_info'] = result.datas.diamond_info;
				  //datas['goods_info'] = {goods_sn:goods_sn,style_sn:style_sn,xiangkou:xiangkou};
				  //alert(data['carat']);
				  if(data['carat']>0){
					 var html = template.render('diamond-info-content',result.datas);    
					 $("#diamond_info").html(html);
				  }
				  //initDiamondInfoByIdFlag = true;
				  
				 $("#kezi").val($("#kezi_ipt").val());
				 $(".jt_kezi em,.jt_kezi2 em").click(function(){
						 var this_=$(this).attr("data-val");
						 var ipt = $("#kezi_ipt").val();
						 var kezi = ipt+this_;
						 var res = checkKezi(kezi);						 
						 if(res.error){
							  alert(res.error);
							  return false;
						 }
						 var keziHtml = res.data;
						 $(".showKZ").html(keziHtml);
						 $("#kezi").val(kezi);
						 $("#kezi_ipt").val(kezi);	
				 });				 				 
				 $("#kezi,#kezi_ipt").blur(function(){
					 var kezi = $(this).val();					  
					 var res = checkKezi(kezi);	
					 var keziHtml = res.data;
					 $(".showKZ").html(keziHtml);
					 if(res.error){
						  alert(res.error);
						  return false;
					 }							  
					 $("#kezi_ipt").val(kezi);
					 $("#kezi").val(kezi);
				 });
				 $("#kezi_ipt,#kezi,#goods_form input[name='carat']").blur(function(){
					   setGoodsFormState();				  
				 });	
				 $("#"+formId+" select[name='is_dingzhi']").change(function(){		
					  initWarehouseGoodsInfo(goods_sn);
				 });
				 $("#"+formId+" select[name='shoucun']").change(function(){
					   setGoodsFormState();				  
				 });
				 $("#"+formId+" input[name='kezi'],#goods_form input[name='carat']").blur(function(){
					   setGoodsFormState();				  
				 });
				 $("#"+formId+" select[name='tuo_type']").change(function(){						 
					  setGoodsFormState();
				 });
				 
				  //加载证书号失去焦点事件
				 //加载证书号失去焦点事件
				$("#"+formId+" input[name='cert_id']").blur(function(){
					 initGoodsDiamondFlag = false;												  
					 initGoodsDiamondInfo(formId,$(this).val());	 
					 setGoodsFormState();
				});//
				 //加载成品定制价格方案事件
				 initCpdzPriceSearchEvent(formId);
				 //加载取价按钮点击事件
				 initDingzhiPriceEvent(formId);					 
				 setGoodsFormState();
			 }else{					
				 alert(result.datas.error);
				 $("#goods_title").html("商品不可销售");
				 $("#goods_form select[name='is_dingzhi']").val(0);
				 //$("#goods_form .show_msg").html(result.datas.error).show();
			 }
		 }
	 });
	 
 }
 
 function checkFormData(){
	  var data = {};
	  if(cert_id && diamond_exists == false){
		 alert("商品不可销售"); 
		 return false;
	  }
	  
	  if(goods_exists==false){
		  alert("商品不可销售");
		  return false;
	  }
	  if(cert_id && cert_id ==_cert_id){
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

 function initDingzhiForm(){
	 if(!checkFormData()){
		  return false;
	 }
	 if(dingzhiSaveBtn == "next_buy" && (!cert_id || cert_id!=_cert_id)){
		 window.location.href = WapSiteUrl + '/tmpl/diamond.html?goods_sn='+goods_sn+"&style_sn="+style_sn;
		 return true;
	 }
	 $("#kezi").val($("#kezi_ipt").val());
	 $("#dingzhi_form select[name='shoucun']").val(shoucun);
	 setGoodsFormState();
	 /*$(".jt_kezi2 em").click(function(){
		 var this_=$(this).attr("data-val");
		 var ipt = $("#kezi").val();
		 var kezi = ipt+this_;
		 var res = checkKezi(kezi);						 
		 if(res.error){
			  alert(res.error);
			  return false;
		 }
		 var keziHtml = res.data;
		 $(".showKZ").html(keziHtml);
		 $("#kezi").val(kezi);
		 $("#kezi_ipt").val(kezi);	
	 });*/
							   
}
// 
function getGoodsFormData(){		
	var formId = "goods_form";
	var goods_id = $("#"+formId+" input[name='goods_id']").val();
	var goods_sn = $("#"+formId+" input[name='goods_id']").attr("data-val");
	if(goods_sn){
		 goods_id = goods_sn;
	}
	var goods_type = $("#"+formId+" input[name='goods_type']").val();	
	var style_sn  = $("#"+formId+" input[name='style_sn']").val();
	var is_dingzhi = $("#"+formId+" select[name='is_dingzhi']").val();
	var tuo_type   = $("#"+formId+" select[name='tuo_type']").val();
	var xiangqian  = $("#"+formId+" select[name='xiangqian']").val();	
	var cert  = $("#"+formId+" select[name='cert']").val();
	var cert_id  = $("#"+formId+" input[name='cert_id']").val();
	var jinse  = $("#"+formId+" select[name='jinse']").val();
	var xiangkou  = $("#"+formId+" select[name='xiangkou']").val();
	var shoucun = $("#"+formId+" select[name='shoucun']").val();
	var carat  = $("#"+formId+" input[name='carat']").val();
	var zhushi_num  = $("#"+formId+" input[name='zhushi_num']").val();
	var color  = $("#"+formId+" select[name='color']").val();
	var clarity  = $("#"+formId+" select[name='clarity']").val();
	var cut  = $("#"+formId+" select[name='cut']").val();
	var facework  = $("#"+formId+" select[name='facework']").val();
	var cpdz_code  = $("#"+formId+" input[name='cpdz_code']").val();
	var goods_price  = $("#"+formId+" input[name='goods_price']").val();
	var kezi  = $("#"+formId+" input[name='kezi']").val();
	var policy_id = $("#"+formId+" input[name='policy_id']").val();
	var xianhuo_adds ='';	
	$("#"+formId+" input[name='xianhuo_adds']:checked").each(function(){											  
		 xianhuo_adds += $(this).val()+",";															  
	});
	
	var is_cpdz = 0;
	if(tuo_type==1 && is_dingzhi==1 && xiangkou>0 && carat>0){
	   is_cpdz = 1;
	}
	
	if(is_dingzhi==1){
		goods_type = 1;
		xianhuo_adds = '';
	}else{
		goods_type = is_shop==2?6:goods_type;	
	}
	if(goods_type==2){
	   goods_id = cert_id;
	}	
	var data = {};
	data['goods_id'] = goods_id;
	data['style_sn'] = style_sn;
	data['goods_type'] = goods_type;
	data['is_dingzhi'] = is_dingzhi;
	data['tuo_type'] = tuo_type;
	data['xiangqian'] = xiangqian;
	data['cert'] = cert;
	data['cert_id'] = cert_id;
	data['jinse'] = jinse;
	data['xiangkou'] = xiangkou;
	data['shoucun'] = shoucun;
	data['carat'] = carat;
	data['zhushi_num'] = zhushi_num;
	data['color'] = color;
	data['clarity'] = clarity;
	data['cut'] = cut;
	data['facework'] = facework;
	data['goods_price'] = goods_price;
	data['cpdz_code'] = cpdz_code;
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
	 
function setGoodsFormState(){
	var formId = "goods_form";
	var is_dingzhi = $("#"+formId+" select[name='is_dingzhi']").val();	
	var goods_type = $("#"+formId+" input[name='goods_type']").val();
	var tuo_type   = $("#"+formId+" select[name='tuo_type']").val();
	var shoucun   = $("#"+formId+" select[name='shoucun']").val();
	var _shoucun   = $("#"+formId+" select[name='shoucun']").find("option").not(function(){return !this.selected}).attr("org");;
	var carat  = $("#"+formId+" input[name='carat']").val();
	var kezi  = $("#"+formId+" input[name='kezi']").val();
	var xiangqian  = $("#"+formId+" select[name='xiangqian']").val();
	var zhushi_num  = $("#"+formId+" input[name='zhushi_num']").val();
	if(goods_type==2){
		//裸钻
		$("#"+formId+" select").prop("disabled",true);
		$("#"+formId+" input").prop("disabled",true);
		$("#"+formId+" input[name='goods_id']").prop("disabled",false);
		$("#"+formId+" select[name='xiangqian']").prop("disabled",false);
		return ;
	}	
	if(is_dingzhi==1){
		 $("#"+formId+" .show_msg").html("定制，需重新生产").show();
		 if(tuo_type ==1){
			  $("#"+formId+" select[name='xiangqian']").prop("disabled",true).val("工厂配钻，工厂镶嵌");
			  if(carat > 0){
				  $("#"+formId+" input[name='cert_id']").prop("disabled",true).val("");
				  $("#"+formId+" select[name='cut']").prop("disabled",true);
			  }else{
				  $("#"+formId+" input[name='cert_id']").prop("disabled",false).val("");
				  $("#"+formId+" select[name='cut']").prop("disabled",false);
			  }
			  
			  //成品定制  禁用 裸石 select控件
			  $("#"+formId+" .hideBox").show();
			  $("#"+formId+" select[name='cert']").prop("disabled",true).addClass('none-event');
			  $("#"+formId+" select[name='color']").prop("disabled",true).addClass('none-event');
			  $("#"+formId+" select[name='clarity']").prop("disabled",true).addClass('none-event');
		 }else{			 
			  $("#"+formId+" select[name='xiangqian']").prop("disabled",false).val("需工厂镶嵌");
			  $("#"+formId+" input[name='cert_id']").prop("disabled",false).val("");
			  $("#"+formId+" select[name='cut']").prop("disabled",false);
			  
			  //非成品定制 解除禁用  裸石 select控件
			  $("#"+formId+" .hideBox").hide();
			  $("#"+formId+" select[name='cert']").prop("disabled",false).removeClass('none-event');
			  $("#"+formId+" select[name='color']").prop("disabled",false).removeClass('none-event');
			  $("#"+formId+" select[name='clarity']").prop("disabled",false).removeClass('none-event');
		 }
		 $("#"+formId+" select[name='tuo_type']").prop("disabled",false);	
		 $("#"+formId+" select[name='facework']").prop("disabled",false);
		 $("#"+formId+" select[name='jinse']").prop("disabled",false);
		 $("#"+formId+" select[name='xiangkou']").prop("disabled",false);
		 
		 $("#"+formId+" .create_price").show();  
		 //$("#"+formId+" .xianhuo_adds").hide();
	}else{
		$("#"+formId+" .show_msg").html("").hide();
		$("#"+formId+" input[name='cert_id']").prop("disabled",true);			
		//$("#"+formId+" select[name='xiangqian']").prop("disabled",false).val("不需工厂镶嵌");
		if(cert_id && cert_id ==_cert_id){
			 $("#"+formId+" select[name='xiangqian']").prop("disabled",true).val("需工厂镶嵌");
		}else if(tuo_type !=1 ){
			$("#"+formId+" input[name='cert_id']").prop("disabled",false);	
		}
		$("#"+formId+" select[name='tuo_type']").prop("disabled",true);
		$("#"+formId+" select[name='facework']").prop("disabled",true);
		$("#"+formId+" select[name='jinse']").prop("disabled",true);
		$("#"+formId+" select[name='xiangkou']").prop("disabled",true);
		$("#"+formId+" input[name='xianhuo_adds']").prop("disabled",true);
		if(kezi!=''){
			$("#"+formId+" input[name='xianhuo_adds'][value='刻字']").prop("checked",true);
		}else{
			$("#"+formId+" input[name='xianhuo_adds'][value='刻字']").prop("checked",false);
		}
		xiangqian  = $("#"+formId+" select[name='xiangqian']").val();
		var xqArray = ["需工厂镶嵌","客户先看钻再返厂镶嵌","工厂配钻，工厂镶嵌"];
		if(carat > 0 && tuo_type >1 && $.inArray(xiangqian,xqArray)>=0 ){
			$("#"+formId+" input[name='xianhuo_adds'][value='镶石']").prop("checked",true);
		}else{
			$("#"+formId+" input[name='xianhuo_adds'][value='镶石']").prop("checked",false);
		}
		if(shoucun!="" && shoucun != _shoucun){
			$("#"+formId+" input[name='xianhuo_adds'][value='改圈']").prop("checked",true);
		}else{
			$("#"+formId+" input[name='xianhuo_adds'][value='改圈']").prop("checked",false);
		}
	}
	
	if(tuo_type!=1 && cert_id && cert_id==_cert_id){
		$("#"+formId+" input[name='cert_id']").prop("disabled",true).val(cert_id);
		initGoodsDiamondFlag = false;
		initGoodsDiamondInfo(formId,cert_id);
	}
	
	if(zhushi_num<1){
		$("#"+formId+" select[name='xiangqian']").prop("disabled",true).val("成品");
	}
}	

function goods_save(){	
	 var formData = getGoodsFormData();	
	 if(!formData){								 
		return false; 
	 }
	 formData['key'] = key;
	 var flag = false;
	 
	 if(formData['is_dingzhi']==1){
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
					   $("#goods_form input[name='goods_price']").val(data.goods_price);
					   $("#goods_form input[name='goods_id']").attr("data-val",data.goods_id);
					   formData['goods_id '] = data.goods_id;
					   formData['goods_price '] = data.goods_price;
					   flag = true;
				  }else{
					   //$("#goods_form input[name='goods_price']").val(0);
					   //$("#goods_form input[name='goods_id']").attr("data-val",'');
					   //$("#goods_form input[name='goods_key']").val('');
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
							flag = false;
							alert(result.datas.error);									
						}
					}
			 });
		 }
		 if(dingzhiSaveBtn=="add_cart" && flag == true){	
			var cartData = {goods_id:formData['goods_id'],goods_type:formData['goods_type'],quantity:1,key:key};
			if(formData['cert_id'] && formData['cert_id'] ==_cert_id){
				  cartData['goods_id'] = formData['goods_id']+'@'+formData['cert_id'];
				  cartData['quantity'] = '1@1';
				  cartData['goods_type'] = formData['goods_type']+"@2";
			}
			$.ajax({
					url:ApiUrl+"/index.php?act=member_cart&op=cart_add",
					type:'post',
					data:cartData,
					dataType:'json',
					async: false,
					success:function(result){
						if(result.code == 200){
							flag = true;
							$(".dialog-dingzhi-close").click();
						}else{	
							flag = false;
							alert(result.datas.error);				   
						}
					}
			 });
		 }
	 }else{
		 //现货
		 if(formData['goods_type']==2){
			 var url = ApiUrl+"/index.php?act=member_goods&op=diamond_add";					 
		 }else{
			 var url = ApiUrl+"/index.php?act=member_goods&op=warehouse_goods_add";
		 }
		 $.ajax({
				url:url,
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

		 if(dingzhiSaveBtn=="add_cart" && flag == true){
			var cartData = {goods_id:formData['goods_id'],goods_type:formData['goods_type'],quantity:1,key:key};
			if(formData['cert_id'] && formData['cert_id'] ==_cert_id){
				  cartData['goods_id'] = formData['goods_id']+'@'+formData['cert_id'];
				  cartData['quantity'] = '1@1';
				  cartData['goods_type'] = formData['goods_type']+"@2";
			} 
			$.ajax({
					url:ApiUrl+"/index.php?act=member_cart&op=cart_add",
					type:'post',
					data:cartData,
					dataType:'json',
					async: false,
					success:function(result){
						if(result.code == 200){
							flag = true;
							$(".dialog-dingzhi-close").click();
						}else{	
							flag = false;
							alert(result.datas.error);				   
						}
					}
			 });
		 }	 
		 
	 }	 
	 return flag;
	 
 }
