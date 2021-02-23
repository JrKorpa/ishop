$(function(){
   var key = getCookie('key');
   if(!key){
        window.location.href = WapSiteUrl+'/login.html';
   }	   
	var cert_id = cert_id_org = getQueryString('cert_id');
	var goods_sn = getQueryString('goods_sn');
	var style_sn = getQueryString('style_sn');
	var is_cpdz = 0;
	var tuo_type = 2;
	var shape = 1;//圆形
	var is_dingzhi;
	var xiangqian;
	var xiangkou;
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
	$(document).delegate(".jt_kezi em","click",function(){
        var this_=$(this).attr("data-val");
        var ipt=$("#kezi_ipt").val();
        $("#kezi_ipt").val(ipt+this_);
    })
	initStyleGallery(style_sn);
	function initStyleGallery(style_sn){
		$.ajax({
			url:ApiUrl+"/index.php?act=marry_goods&op=get_style_gallery",
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
	initDiamondInfoById(cert_id);
	function initDiamondInfoById(cert_id){
		if(!cert_id){
			diamond_exists = false;
			return false;
		}
		$.ajax({
			url:ApiUrl+"/index.php?act=diamond&op=diamond_detail",
			type:'post',
			data:{cert_id:cert_id,key:key},
			dataType:'json',
			success:function(result){
				if(result.code ==200){
					diamond_exists = true;
					initDiamondInfo(result.datas);					
				}else{
					diamond_exists = false;
					alert(result.datas.error);
				}
			}
  	  });	
	}
	function initDiamondInfo(diamond_info){
		if(!diamond_info){
			diamond_exists = false;
		}
		var html = template.render('diamond-info-content',diamond_info);     
		$("#diamond_info").html(html);
	}
	initStyleGoodsInfo(goods_sn);
	function initStyleGoodsInfo(goods_sn){
		$.ajax({
			url:ApiUrl+"/index.php?act=marry_goods&op=get_style_goods_detail",
			type:'post',
			data:{goods_sn:goods_sn,key:key},
			dataType:'json',
			success:function(result){
				if(result.code ==200){
					var html = template.render('goods-attr-list',result.datas.attr_list); 
					$("#goods_attr").html(html);
					
					var data = result.datas.goods_info;
					style_goods = data;
					$("#goods_title").html(data.style_name);
					$("#goods_conent").html(data.goods_content);
					$("#delivery_tip").html(result.datas.delivery_tip);
					initStyleGoodsPrice(data.style_sn,data.caizhi,data.yanse,data.xiangkou,data.shoucun);
					
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
					
					var jinse = data.caizhi+'|'+data.yanse;					
					$("#jinseSelect .dropdown-menu a").each(function(){																 
							if($(this).attr("data-val")==jinse){
						        $(this).parents(".dropdown").find(".text").html($(this).html());
								$(this).parents(".dropdown").find(".text").attr('data-val',$(this).attr('data-val'));
						        $(this).parents(".dropdown").find(".text").attr('data-name',$(this).attr('data-name'));
							}
					});
					var shoucun  = data.shoucun;
					xiangkou = data.xiangkou;
					$("#shoucunSelect .dropdown-menu a").each(function(){
							if($(this).attr("data-val")==shoucun){
						        $(this).parents(".dropdown").find(".text").html($(this).html());
								$(this).parents(".dropdown").find(".text").attr('data-val',$(this).attr('data-val'));
						        $(this).parents(".dropdown").find(".text").attr('data-name',$(this).attr('data-name'));
							}										 
					});
					$("#jinseSelect .dropdown-menu a").click(function(){
						var jinseArr = $(this).attr('data-val').split("|");
						var caizhi = jinseArr[0];
						var yanse  = jinseArr[1];
						var shoucun = $("#shoucunSelect").find(".text").attr('data-val');
						initStyleGoodsPrice(style_sn,caizhi,yanse,xiangkou,shoucun);
					});
					$("#shoucunSelect .dropdown-menu a").click(function(){
							var jinseArr =$("#jinseSelect").find(".text").attr('data-val').split("|");
							
							var caizhi = jinseArr[0];
							var yanse  = jinseArr[1];
							shoucun = $(this).attr('data-val');							
							initStyleGoodsPrice(style_sn,caizhi,yanse,xiangkou,shoucun);
					});							
					
				}else{					
					alert(result.datas.error);
				}
			}
  	  });
		
	}
	
	function initStyleGoodsPrice(style_sn,caizhi,yanse,xiangkou,shoucun){
		$.ajax({
			url:ApiUrl+"/index.php?act=marry_goods&op=get_style_goods_price",
			type:'post',
			data:{style_sn:style_sn,caizhi:caizhi,yanse:yanse,xiangkou:xiangkou,shoucun:shoucun,key:key},
			dataType:'json',
			success:function(result){
				if(result.code ==200){
					style_goods_exists = true;
					var data = result.datas.goods_info;
					goods_sn = data.goods_sn;//货号
					$("#goods_price").html("￥"+data.goods_price).attr('data-val',data.goods_price).attr("data-sn",data.goods_sn);						
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
		 data['goods_id'] = goods_sn;
		 data['quantity'] = '1';
		 data['goods_type'] = '1';
		 return data;
		
	}
	
	//挑选，确认商品
	var dingzhiFormLoad = false;
	var dingzhiSaveBtn = "";
	function initDingzhiForm(){
		if(!checkFormData()){
			 return false;
		}
		//只加载一次						  
	    if(dingzhiFormLoad == true ){
			$("#kezi").val($("#kezi_ipt").val());
			return true;
	    }
		$.ajax({
				url:ApiUrl+"/index.php?act=style_goods&op=get_style_goods_diy_index",
				type:'post',
				data:{style_sn:style_sn,goods_sn:goods_sn},
				dataType:'json',
				async: true,
				success:function(result){
					if(result.code ==200){
						dingzhiFormLoad = true;
						var html = template.render('dingzhi-form',result.datas); 
					    $("#dingzhi_form").html(html);
						
						$("#is_dingzhi").change(function(){
							 if($(this).val()==1){							  
								$(".xianhuo_adds").hide();
							 }else{
								$(".xianhuo_adds").show();
							 }
						 });
						$("#kezi").val($("#kezi_ipt").val());
						$(".jt_kezi2 em").click(function(){
							var this_=$(this).attr("data-val");
							var ipt=$("#kezi").val();
							$("#kezi").val(ipt+this_);	
							$("#kezi_ipt").val(ipt+this_);	
						});
						$("#kezi").blur(function(){
							$("#kezi_ipt").val($(this).val());						
						});	
						
						$("#dingzhi")
						
					}else{	
						alert(result.datas.error);				   
					}
				}
		 });
    }
	function goods_save(){        
		tuo_type = $("#tuo_type").val();
		shape = 1;//圆形
		is_dingzhi = $("#is_dingzhi").val();
		xiangqian = $("#xiangqian").val();
		cert_id = $("#cert_id").val();
		carat  = $("#carat").val();
		zhushi_num = $("#zhushi_num").val();
		cert = $("#cert").val();
		cert_id = $("#cert_id").val();
		color = $("#color").val();
		clarity = $("#clarity").val();
		cut = $("#cut").val();
		facework = $("#facework").val();		
		kezi = $("#kezi_ipt").val();
		goods_price = $("#goods_price").attr("data-val");
		cpdz_code = $("#cpdz_code").val();
		goods_type = 1;//戒托
		is_cpdz = is_dingzhi==1 && tuo_type==1?1:0;
		$('input[name="xianhuo_adds"]:checked').each(function(){
		   xianhuo_adds = $(this).val()+',';
		});
        if(is_dingzhi==1){
		   xianhuo_adds = '';	
		}
		var postData = {
			key:key,style_sn:style_sn,goods_id:goods_sn,tuo_type:tuo_type,is_dingzhi:is_dingzhi,xiangqian:xiangqian,
			cert:cert,cert_id:cert_id,zhushi_num:zhushi_num,xiangkou:xiangkou,carat:carat,color:color,clarity:clarity,cut:cut,facework:facework,xianhuo_adds:xianhuo_adds,
			kezi:kezi,goods_price:goods_price,cpdz_code:cpdz_code,shape:shape
		};
		var flag = false;
	    $.ajax({
				url:ApiUrl+"/index.php?act=member_goods&op=style_goods_add",
				type:'post',
				data:postData,
				dataType:'json',
				async: false,
				success:function(result){
					if(result.code ==200){	
					    var data = result.datas;
						goods_price = data.goods_price;
						
						$("#goods_price").attr("data-val",goods_price).html("￥"+goods_price);
						
						flag = true;
					}else{	
						alert(result.datas.error);	
						flag = false;
					}
				}
		 });
		return flag;

	}
	
	$("#add_cart").click(function(){
		 var flag = true;									   
		 var postData = checkFormData();
		 if(postData !=false){		 
			  dingzhiSaveBtn = "add_cart";
			  flag = initDingzhiForm();
		 }else{
			  flag = false; 
		 }
		 return flag;
	});
	
	$("#buy_now").on("click",function(){
		 var flag = true;									   
		 var postData = checkFormData();
		 if(postData !=false){		 
			  dingzhiSaveBtn = "buy_now";
			  flag = initDingzhiForm();
		 }else{
			  flag = false; 
		 }
		 return flag;
	});
	$("#next_buy").on("click",function(){
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
	$("#dingzhi_submit").click(function(){
		var postData = checkFormData();
		 if(!postData){		
		     return false;
		 }
		 if(!goods_save()){
			  return false; 
		 }
		 if(dingzhiSaveBtn=="add_cart"){
			postData['key'] = key;
			 $.ajax({
				url:ApiUrl+"/index.php?act=member_cart&op=cart_add",
				type:'post',
				data:postData,
				dataType:'json',
				success:function(result){
					if(result.code ==200){
						alert("添加成功！");
					}else{	
						alert(result.datas.error);				   
					}
				}
		    }); 
		 }else if(dingzhiSaveBtn=="buy_now"){			 
			location.href = WapSiteUrl +'/tmpl/order/buy_step1.html?goods_id='+goods_sn+"@1"; 
		 }else{
			 alert("未知按钮事件"); 
		 }		 
		 $(".dialog-dingzhi-close").click();	
										
	});
	//对比添加
	$("#add_contrast").click(function(){
		//if(!checkFormData()){
			 //return false;
		//}	
		var marryContrastList = sessionStorageGet("marryContrastList");
		if(marryContrastList ==false){
			marryContrastList = new Array();
		}
		var existsIndex = $.inArray(goods_sn, marryContrastList);
		if(existsIndex==-1){			
			marryContrastList.push(goods_sn);
			sessionStorageAdd("marryContrastList", marryContrastList, true);
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