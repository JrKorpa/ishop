$(function(){
	var key = getCookie('key');
	if(!key){
		 window.location.href = WapSiteUrl+'/login.html';
	}	   
	 var cert_id = cert_id_org = getQueryString('cert_id');
	 var goods_sn = getQueryString('goods_sn');
	 var style_sn = "";
	 if(goods_sn){
		var arrg = new Array();
		arrg = goods_sn.split("-");
		style_sn = arrg[0];
	}
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
	 var zhushi_num ;
	 var cert ;
	 var color ;
	 var clarity ;
	 var cut ;
	 var facework ;		
	 var kezi ;
	 var goods_price ;
	 var cpdz_code ;
	 var goods_type = 1;//戒托
	 var xianhuo_adds = "";
	 var style_goods_exists = false;
	 var diamond_exists = false;
	 //$("#selectJietuo").attr("href","style_goods_list.html?cert_id="+cert_id);
	 $(".nav-sel-dia").click(function(){
	    window.location.href = WapSiteUrl+'/tmpl/diamond.html?goods_sn='+goods_sn;							 
	 });
	 initStyleGallery(style_sn);
	 function initStyleGallery(style_sn){
		 $.ajax({
			 url:ApiUrl+"/index.php?act=style_goods&op=get_style_gallery",
			 type:'post',
			 data:{style_sn:style_sn},
			 dataType:'json',
			 success:function(result){
				 if(result.code ==200){
					 var html = template.render('style-gallery-list',result.datas);     
					 $("#style_gallery").html(html);
                     $("#style_gallerythum").html(html);

                     var galleryTop = new Swiper('.gallery-top', {
                         spaceBetween: 10,
                         loop:true,
                         loopedSlides: 5, //looped slides should be the same
                     });
                     var galleryThumbs = new Swiper('.gallery-thumbs', {
                         spaceBetween: 10,
                         slidesPerView: 4,
                         touchRatio: 0.2,
                         loop: true,
                         loopedSlides: 5, //looped slides should be the same
                         slideToClickedSlide: true,
                     });
                     galleryTop.controller.control = galleryThumbs;
                     galleryThumbs.controller.control = galleryTop;
				 }
			 }
		 });
		 
	 }	
	 initStyleGoodsInfo(goods_sn);
	 function initStyleGoodsInfo(goods_sn){
		 $.ajax({
			 url:ApiUrl+"/index.php?act=style_goods&op=get_style_goods_detail",
			 type:'post',
			 data:{goods_sn:goods_sn,key:key},
			 dataType:'json',
			 success:function(result){
				 if(result.code ==200){
					 var html = template.render('goods-attr-list',result.datas); 
					 $("#goods_attr").html(html);
					 
					 var data = result.datas.goods_info;
					 style_goods = data;
					 $("#goods_title").html(data.style_name);
					 $("#goods_content").html(data.goods_content);
					 //$("#delivery_tip").html(result.datas.delivery_tip);
					 $("#goods_price").html("￥"+data.goods_price).attr({'data-val':data.goods_price,"data-sn":data.goods_sn});
					 if(data.goods_price >0){
						  style_goods_exists = true; 
					 }
					 $("#goods_form input[name='xiangkou']").val(data.xiangkou);
					 
					 $('.dropdown .text').click(function(){
						 if($(this).parents(".dropdown").find(".dropdown-menu").css("display")=="block"){
							 $(this).parents(".dropdown").find(".dropdown-menu").hide();
						 }else{
							 $(this).parents(".dropdown").find(".dropdown-menu").show();
						 }
					 });
					 $(".dropdown-menu a").click(function(){
						 var html=$(this).html();
						 $(this).parents(".dropdown").find(".text").html(html);
						 $(this).parents(".dropdown").find(".text").attr('data-val',$(this).attr('data-val'));
						 $(this).parents(".dropdown").find(".text").attr('data-name',$(this).attr('data-name'));
						 $(".dropdown-menu").hide();
					 });
					 
					 $(".jtDetail_top .nav-link").click(function(){
						 var type=$(this).attr("data-type");
						 $(".jtDetail_bottom").find("."+type).show().siblings().hide();
					 });
					 jinse = data.caizhi+'|'+data.yanse;					
					 $("#jinseSelect .dropdown-menu a").each(function(){																 
							 if($(this).attr("data-val")==jinse){
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
					 
					 $("#xiangkouSelect .dropdown-menu a").click(function(){
							 var jinseArr =$("#jinseSelect").find(".text").attr('data-val').split("|");							 
							 var caizhi = jinseArr[0];
							 var yanse  = jinseArr[1];
							 //shoucun = $("#xiangkouSelect").find(".text").attr('data-val');
							 xiangkou = $(this).attr('data-val');							 		
							 initStyleGoodsPrice(style_sn,caizhi,yanse,xiangkou,shoucun);
					 });
					 $("#jinseSelect .dropdown-menu a").click(function(){
						 jinse = $(this).attr('data-val');
						 var jinseArr = jinse.split("|");
						 var caizhi = jinseArr[0];
						 var yanse  = jinseArr[1];
						 //var shoucun = $("#shoucunSelect").find(".text").attr('data-val');						 
						 initStyleGoodsPrice(style_sn,caizhi,yanse,xiangkou,shoucun);
					 });
					 
					 $("#shoucunSelect .dropdown-menu a").click(function(){
							 var jinseArr =$("#jinseSelect").find(".text").attr('data-val').split("|");							 
							 var caizhi = jinseArr[0];
							 var yanse  = jinseArr[1];
							 shoucun = $(this).attr('data-val');							
							 initStyleGoodsPrice(style_sn,caizhi,yanse,xiangkou,shoucun);
					 });	
					 
					 $(".jt_kezi em").click(function(){
								 var this_=$(this).attr("data-val");
								 var ipt = $("#kezi_ipt").val();
								 var kezi = ipt+this_;
								 if(kezi.length >6 ){
									 kezi = kezi.substr(0,6);
									 alert("刻字内容最多支持6个字符");
								 }
								 $("#kezi").val(kezi);
								 $("#kezi_ipt").val(kezi);	
					 });
					 $("#kezi_ipt").blur(function(){
						 $("#kezi").val($(this).val());	
					 });
						 
					 setGoodsFormState();
				 }else{					
					 alert(result.datas.error);
				 }
			 }
		 });
		 
	 }
	 
	 function initStyleGoodsPrice(style_sn,caizhi,yanse,xiangkou,shoucun){
		 $.ajax({
			 url:ApiUrl+"/index.php?act=style_goods&op=get_style_goods_price",
			 type:'post',
			 data:{style_sn:style_sn,caizhi:caizhi,yanse:yanse,xiangkou:xiangkou,shoucun:shoucun,key:key},
			 dataType:'json',
			 success:function(result){
				 if(result.code ==200){
					 style_goods_exists = true;
					 var data = result.datas.goods_info;
					 goods_sn = data.goods_sn;//货号
					 $("#goods_price").html("￥"+data.goods_price).attr({'data-val':data.goods_price,"data-sn":data.goods_sn});				
					
				 }else{	
					 style_goods_exists = false;				   
				 }
			 }
		  });
	 }
	 function checkFormData(){
		  var data = {};
		  if(cert_id && diamond_exists == false){
			 alert("你所挑选的裸钻已下架"); 
			 return false;
		  }
		  
		  if(style_goods_exists==false){
			  alert("你所挑选的戒托未设置销售政策");
			  return false;
		  }
		  if(cert_id && cert_id ==cert_id_org){
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
	 
	 //挑选，确认商品
	 var dingzhiFormLoad = false;
	 var dingzhiSaveBtn = "";
	 function initDingzhiForm(){
		 if(!checkFormData()){
			  return false;
		 }
		 if(dingzhiSaveBtn == "next_buy" && (!cert_id || cert_id!=cert_id_org)){
			 window.location.href = WapSiteUrl + '/tmpl/diamond.html?goods_sn='+goods_sn;
			 return true;
		 }
		 //只加载一次						  
		 if(dingzhiFormLoad == true ){
			 $("#kezi").val($("#kezi_ipt").val());
			 return true;
		 }
		 $.ajax({
				 url:ApiUrl+"/index.php?act=style_goods&op=get_style_goods_diy_index",
				 type:'post',
				 data:{style_sn:style_sn,goods_sn:goods_sn,cert_id:cert_id},
				 dataType:'json',
				 async: true,
				 success:function(result){
					 if(result.code ==200){
						 dingzhiFormLoad = true;
						 var html = template.render('dingzhi-form',result.datas); 
						 $("#goods_form .dingzhi-body").html(html);
								 
						 $("#is_dingzhi").change(function(){
							 if($(this).val()==1){							  
								 $(".xianhuo_adds").hide();
							 }else{
								 $(".xianhuo_adds").show();
							 }
							 setGoodsFormState();
						 });						  
						 goods_price = $("#goods_price").attr('data-val');
						 $("#goods_form input[name='goods_price']").val(goods_price);
						 
						 $("#kezi").val($("#kezi_ipt").val());						 
						 $(".jt_kezi2 em").click(function(){
							 var this_=$(this).attr("data-val");
							 var ipt=$("#kezi").val();
							 var kezi = ipt+this_;
							 if(kezi.length >6 ){
								 kezi = kezi.substr(0,6);
								 alert("刻字内容最多支持6个字符");
							 }
							 $("#kezi").val(kezi);
							 $("#kezi_ipt").val(kezi);	
						 });					 
						 $("#kezi").blur(function(){
							 $("#kezi_ipt").val($(this).val());	
						 });
						 $("#kezi_ipt,#kezi,#goods_form input[name='carat']").blur(function(){
						   setGoodsFormState();				  
					     });
						 
						$("#goods_form select[name='xiangkou']").val(xiangkou);
						$("#goods_form select[name='tuo_type']").change(function(){
							 setGoodsFormState();
						});
						if(cert_id_org !=''){
							 $("#cert_id").attr("disabled",true);
							 $("#cert").attr("disabled",true);
							 $("#color").attr("disabled",true);
							 $("#clarity").attr("disabled",true);
							 $("#cut").attr("disabled",true);
							 $("#carat").attr("disabled",true);
						 }
						 $("#goods_form .create_price").click(function(){
							 var formData = getGoodsFormData();	
							 if(!formData){								 
								return false; 
							 }
							 formData['key'] = key;						 	 
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
										   $("#goods_price").attr("data-sn",goods_sn);
										   $("#goods_form input[name='goods_price']").val(data.goods_price);
										  
										   style_goods_exists = true;
										   alert("取价成功");
									  }else{
										   alert(result.datas.error);	
									  }
								 }
							 });
											   
					     });
						 setGoodsFormState();
						 
					 }else{	
						 alert(result.datas.error);				   
					 }
				 }
		  });
	 }
	// 
	function getGoodsFormData(){
		 var formId = "goods_form";
		 goods_id = $("#goods_price").attr("data-sn");
		 tuo_type = $("#"+formId+" select[name='tuo_type']").val();
		 is_dingzhi = $("#"+formId+" select[name='is_dingzhi']").val();
		 xiangqian = $("#"+formId+" select[name='xiangqian']").val();
		 xiangkou = $("#"+formId+" select[name='xiangkou']").val();
		 cert_id = $("#"+formId+" input[name='cert_id']").val();
		 carat  = $("#"+formId+" input[name='carat']").val();
		 zhushi_num = $("#"+formId+" input[name='zhushi_num']").val();
		 cert = $("#"+formId+" select[name='cert']").val();
		 cert_id = $("#"+formId+" input[name='cert_id']").val();
		 color = $("#"+formId+" select[name='color']").val();
		 clarity = $("#"+formId+" select[name='clarity']").val();
		 cut = $("#"+formId+" select[name='cut']").val();
		 facework = $("#"+formId+" select[name='facework']").val();		
		 kezi = $("#"+formId+" input[name='kezi']").val();
		 goods_price = $("#"+formId+" input[name='goods_price']").val();
		 cpdz_code = $("#"+formId+" input[name='cpdz_code']").val();
		 goods_type = 1;//戒托
		 $('input[name="xianhuo_adds"]:checked').each(function(){
			xianhuo_adds = $(this).val()+",";
		 }); 
		 if(is_dingzhi==1){
			xianhuo_adds = '';	
		 } 
		 if(xiangkou>0 && tuo_type==1 && is_dingzhi==1){
			 is_cpdz = 1; 
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
		data['cpdz_code'] = cpdz_code;
		data['is_cpdz'] = is_cpdz;
		data['kezi'] = kezi;
		data['xianhuo_adds'] = xianhuo_adds;
		return data;
		 
	}
		 
	function setGoodsFormState(){
		var formId = "goods_form";
		var is_dingzhi = $("#"+formId+" select[name='is_dingzhi']").val();	
		var goods_type = 1;
		var tuo_type   = $("#"+formId+" select[name='tuo_type']").val();
		var carat  = $("#"+formId+" input[name='carat']").val();
	    var kezi  = $("#"+formId+" input[name='kezi']").val();	
		var xiangqian  = $("#"+formId+" select[name='xiangqian']").val();
		if(is_dingzhi==1){
			 $("#"+formId+" .show_msg").html("").hide();
			 if(tuo_type ==1){
				  $("#"+formId+" select[name='xiangqian']").prop("disabled",true).val("工厂配钻，工厂镶嵌")
			 }else{
				  $("#"+formId+" select[name='xiangqian']").prop("disabled",false).val("需工厂镶嵌");
			 }

			 $("#"+formId+" select[name='tuo_type']").prop("disabled",false);	
			 $("#"+formId+" select[name='facework']").prop("disabled",false);
			 
			 $("#"+formId+" .create_price").show();  
			 $("#"+formId+" .xianhuo_adds").hide();
			 
			  if(xiangkou ==0){
				 $("#"+formId+" select[name='xiangqian']").prop("disabled",false).val("不需工厂镶嵌");	
				 $("#"+formId+" select[name='tuo_type']").prop("disabled",true).val(1);	
			 }
		}else{
			$("#"+formId+" .show_msg").html("现货，不需生产").show();
			$("#"+formId+" select[name='xiangqian']").prop("disabled",false).val("不需工厂镶嵌");
			$("#"+formId+" select[name='tuo_type']").prop("disabled",true);
			$("#"+formId+" select[name='facework']").prop("disabled",true);	
			
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
		}
		
		if(cert_id !='' && cert_id == cert_id_org){			 
			 $("#"+formId+" select[name='tuo_type']").prop("disabled",true);
			 $("#"+formId+" select[name='xiangkou']").prop("disabled",true);
		}else{
			 $("#"+formId+" select[name='xiangkou']").prop("disabled",false);
		}  
		$("#"+formId+" input[name='cert_id']").blur(function(){
		   var formId = "goods_form";												 
		   var cert_id = $(this).val();	
		   var flag = true;
		   $.ajax({
				url:ApiUrl+"/index.php?act=diamond&op=get_diammond_info",
				type:'get',
				data:{cert_id:cert_id,key:key},
				async: false,
				dataType:'json',
				success:function(result){
					if(result.code ==200){
					    var diamond_info = result.datas.diamond_info;
						 diamond_info.cert = diamond_info.cert?diamond_info.cert:"无";
						 $("#"+formId+" select[name='cert']").prop("disabled",true).val(diamond_info.cert);  
						 $("#"+formId+" select[name='color']").prop("disabled",true).val(diamond_info.color);
						 $("#"+formId+" select[name='clarity']").prop("disabled",true).val(diamond_info.clarity);
						 $("#"+formId+" select[name='cut']").prop("disabled",true).val(diamond_info.cut);
						 $("#"+formId+" input[name='carat']").prop("disabled",true).val(diamond_info.carat);
						 flag = true;
					}else{
						
						flag = false;
						//alert(result.datas.error);
					}
                    
			    }
		  });
		   
		   if(flag==false){
			    $("#"+formId+" select[name='cert']").prop("disabled",false).val('');  
				$("#"+formId+" select[name='color']").prop("disabled",false).val('');
				$("#"+formId+" select[name='clarity']").prop("disabled",false).val('');
				$("#"+formId+" select[name='cut']").prop("disabled",false).val('');
				$("#"+formId+" input[name='carat']").prop("disabled",false).val('');
		   }
	   });
	}
	function goods_save(){
		 var formData = getGoodsFormData();	
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
						   $("#goods_price").attr("data-sn",goods_sn);
						   $("#goods_form input[name='goods_price']").val(data.goods_price);
						   formData['goods_id'] = data.goods_id;
						   formData['goods_price'] = data.goods_price;
						   style_goods_exists = true;
						   flag = true;
					  }else{
						   $("#goods_form input[name='goods_price']").val(0);
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
				$.ajax({
						url:ApiUrl+"/index.php?act=member_cart&op=cart_add",
						type:'post',
						data:{goods_id:formData['goods_id'],goods_type:formData['goods_type'],quantity:1,key:key},
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

		// }		 
		 
		 return flag;
		 
	 }
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
			  if(cert_id && cert_id == cert_id_org){				 
				 window.location.href = WapSiteUrl +'/tmpl/order/buy_step1.html?goods_id='+goods_sn+"@1,"+cert_id+"@2";
			  }else{
				 window.location.href = WapSiteUrl + '/tmpl/order/buy_step1.html?goods_id='+goods_sn+"@1"; 
			  }
		  }else if(dingzhiSaveBtn=="next_buy"){
			  if(cert_id && cert_id == cert_id_org){				 
				 window.location.href = WapSiteUrl +'/tmpl/order/buy_step1.html?goods_id='+goods_sn+"@1,"+cert_id+"@2";
			  }else{
				 window.location.href = WapSiteUrl + '/tmpl/diamond.html?goods_sn='+goods_sn;
			  }
		  }else{
			  alert("未知按钮事件"); 
		  }
		 
										 
	 });
	 //对比添加
	 $("#add_contrast").click(function(){

		 var coupleContrastList = sessionStorageGet("coupleContrastList");
		 if(coupleContrastList ==false){
			 coupleContrastList = new Array();
		 }
		 var existsIndex = $.inArray(goods_sn, coupleContrastList);
		 if(existsIndex==-1){			
			 coupleContrastList.push(goods_sn);
			 sessionStorageAdd("coupleContrastList", coupleContrastList, true);
			 alert("添加成功！");
		 }else{
			 alert("对比已添加！");	
		 }
	 });
	 
	$(".nav >.nav-item >.nav-link").click(function(){
													
		 var nav_type = $(this).attr("data-type");
		 
		 $(".nav >.nav-item >.nav-link").removeClass("active");
		 $(this).addClass("active");
		 
		 $(".jtDetail_bottom > div").hide();	
		 $(".jtDetail_bottom >."+nav_type).show();							
	 });


 });
