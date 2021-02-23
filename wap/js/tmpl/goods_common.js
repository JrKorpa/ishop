// JavaScript Document
var key = getCookie('key');
function initGoodsDiamondInfo(formId,cert_id){								 
	   var flag = false;
	   var old_cert_id = $("#"+formId+" input[name='cert_id']").attr('data-cert_id');
	   if(cert_id!=''){
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
						 $("#"+formId+" input[name='cert_id']").attr('data-cert_id',cert_id).val(cert_id);
						 $("#"+formId+" select[name='cert']").prop("disabled",true).val(diamond_info.cert);  
						 $("#"+formId+" select[name='color']").prop("disabled",true).val(diamond_info.color);
						 $("#"+formId+" select[name='clarity']").prop("disabled",true).val(diamond_info.clarity);
						 $("#"+formId+" select[name='cut']").prop("disabled",true).val(diamond_info.cut);
						 $("#"+formId+" input[name='carat']").prop("disabled",true).val(diamond_info.carat);
						 flag = true;
						 initGoodsDiamondFlag = true;
					}else{					
						flag = false;
						//alert(result.datas.error);
					}
					
				}
		  });
	  }
	  if(old_cert_id!='' && flag==false){
		    $("#"+formId+" input[name='cert_id']").attr('data-cert_id',""); 
			$("#"+formId+" select[name='cert']").prop("disabled",false).val('');  
			$("#"+formId+" select[name='color']").prop("disabled",false).val('');
			$("#"+formId+" select[name='clarity']").prop("disabled",false).val('');
			$("#"+formId+" select[name='cut']").prop("disabled",false).val('');
			$("#"+formId+" input[name='carat']").prop("disabled",false).val('');
	   }
		
}

//初始 取价按钮 点击事件
function initDingzhiPriceEvent(formId,wrapId){
	wrapId = wrapId?wrapId:'wrap_id1';
	$("#"+formId+" .create_price").click(function(){
		 var formData = formId=="style_form"?getStyleFormData(wrapId):getGoodsFormData();
		 if(formData['is_cpdz']==1){
			 $("#"+formId+" .cpdz_price_search").click();
			 return false; 
		 }
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
					   $("#"+formId+" input[name='goods_price']").val(data.goods_price);
					   $("#"+formId+" input[name='goods_id']").attr("data-val",data.goods_id);
					   //$("#goods_form input[name='goods_key']").val(data.goods_key);
					   alert("取价成功");
				  }else{
					   alert(result.datas.error);	
				  }
			 }
		 });							   
	 });
}
//初始化 成品定制 价格列表事件
function initCpdzPriceSearchEvent(formId,wrapId){
	   
	 $("#"+formId+" .cpdz_price_search").click(function(){	
		 $("#cpdz_price_form .dingzhi-body").html('<div class="msg">努力加载中....</div>');												
		 var formData = formId=="style_form"?getStyleFormData(wrapId):getGoodsFormData();//console.log(formData);	 
		 formData['key'] = key;
		 $.ajax({
			url:ApiUrl+"/index.php?act=style_goods&op=get_cpdz_price_list",
			type:'post',
			data:formData,
			dataType:'json',
			async: true,
			success:function(result){
				if(result.code == 200){
					var html = template.render('cpdz-price-list',result.datas); 
					$("#cpdz_price_form .dingzhi-body").html(html);
					$("#cpdz_price_form select[name='cpdz_price_select']").change(function(){
					      var checkBtnObj = $(this).parent().parent().parent().find(".checkBtn");	
						  var val = $(this).val().split("|");
						  checkBtnObj.attr('data-policy_id',val[0]);
						  checkBtnObj.attr('data-goods_price',val[1]);
						  //checkBtnObj.attr('data-clarity',val[3]);
						  //checkBtnObj.attr('data-cert',val[4]);						  
					});
					$(".checkBtn").click(function(){
						 var color = $(this).attr('data-color');
						 var clarity = $(this).attr('data-clarity');
						 var cert = $(this).attr('data-cert');
						 var policy_id = $(this).attr('data-policy_id');
						 var goods_price = $(this).attr('data-goods_price');
						 $("#"+formId+" select[name='cert']").val(cert);
						 $("#"+formId+" select[name='color']").val(color);
						 $("#"+formId+" select[name='clarity']").val(clarity);
						 $("#"+formId+" input[name='goods_price']").val(goods_price);
						 $("#"+formId+" input[name='policy_id']").val(policy_id);
						 $("#cpdz_price_form .dialog-dingzhi-close").click();
					});
				}else{	
					//$("#cpdz_price_form .dialog-dingzhi-close").click();
					$("#cpdz_price_form .msg").html(result.datas.error).attr("style","color:red");
					//alert(result.datas.error);				   
				}
			}
	   });		 
    });
	//点击 证书类型，主石颜色，净度 挑选成品定制裸石和价格
	
	//$("#"+formId+" .cert_box,#"+formId+" .color_box,#"+formId+" .clarity_box,#"+formId+" .hideBox").click(function(e){
	$("#"+formId+" .hideBox").click(function(e){																											   
		var tuo_type = $("#"+formId+" select[name='tuo_type']").val();
		 var is_dingzhi = $("#"+formId+" select[name='is_dingzhi']").val();	
		 var xiangkou = $("#"+formId+" select[name='xiangkou']").val();
		 var carat = $("#"+formId+" input[name='carat']").val();
		 var caizhi = $("#"+formId+" select[name='jinse']").val();
		 var shoucun = $("#"+formId+" select[name='shoucun']").val();
		 var zhushi_num = $("#"+formId+" input[name='zhushi_num']").val();
		 if(tuo_type == 1 && zhushi_num >0 && ((is_dingzhi==1 && formId=="goods_form") || formId=="style_form")){
			 if(caizhi==""){
				 alert("请先选择材质");
				 return false;
			 }else if(shoucun==""){
				 alert("请先选择手寸");
				 return false;
			 }else if(xiangkou==""){
				 alert("请先选择镶口");
				 return false;
			 }else if(carat<=0){
				 alert("请先填写主石单颗重");
				 return false;
			 }
			 if(xiangkou >0 && carat>0){
				$("#"+formId+" .cpdz_price_search").click();	
			 }
		 }
   }); 
}
//初始化商品图库
function initStyleGallery(style_sn,wrapId){
	 wrapId = wrapId?wrapId:'wrap_id1';
	 $.ajax({
		 url:ApiUrl+"/index.php?act=style_goods&op=get_style_gallery",
		 type:'post',
		 data:{style_sn:style_sn},
		 dataType:'json',
		 success:function(result){
			 if(result.code ==200){
				 var html = template.render('style-gallery-list',result.datas);     
				 $("#"+wrapId+" #style_gallery").html(html);
				 $("#"+wrapId+" #style_gallerythum").html(html);

          var  num1 = $('#wrap_id1 .gallery-top').find('.swiper-slide').length;
          var  num2 = $('#wrap_id2 .gallery-top').find('.swiper-slide').length;
          if(wrapId =="wrap_id2"){
            if(num2 > 3){
              var galleryTop2 = new Swiper('#wrap_id2 .gallery-top', {
                spaceBetween: 10,
                loop:true,
                loopedSlides: num2, //looped slides should be the same
              });
              var galleryThumbs2 = new Swiper('#wrap_id2 .gallery-thumbs', {
                spaceBetween: 10,
                slidesPerView: 4,
                touchRatio: 0.2,
                loop: true,
                loopedSlides: num2, //looped slides should be the same
                slideToClickedSlide: true,
              });
              galleryTop2.controller.control = galleryThumbs2;
              galleryThumbs2.controller.control = galleryTop2;
						}else{
              var galleryTop2 = new Swiper('#wrap_id2 .gallery-top', {
                spaceBetween: 10,
                on: {
                  slideChangeTransitionEnd: function(){
                    $("#wrap_id2 .gallery-thumbs .swiper-slide").eq(this.activeIndex).addClass("active swiper-slide-active").siblings().removeClass("active swiper-slide-active")
                  },
                },
              });
              var galleryThumbs2 = new Swiper('#wrap_id2 .gallery-thumbs', {
                spaceBetween: 10,
                slidesPerView: 4,
                touchRatio: 0.2,
                on:{
                  click: function(){
                    var i=galleryThumbs2.clickedIndex;
                    galleryTop2.slideTo(i);
                  },
                },
              });
						}
					}else{
						if(num1 > 3){
              var galleryTop1 = new Swiper('#wrap_id1 .gallery-top', {
                spaceBetween: 10,
                loop:true,
                loopedSlides: num1,
              });
              var galleryThumbs1 = new Swiper('#wrap_id1 .gallery-thumbs', {
                spaceBetween: 10,
                slidesPerView: 4,
                touchRatio: 0.2,
                loop: true,
                loopedSlides: num1,
                slideToClickedSlide: true,
              });
              galleryTop1.controller.control = galleryThumbs1;
              galleryThumbs1.controller.control = galleryTop1;
						}else{
              var galleryTop1 = new Swiper('#wrap_id1 .gallery-top', {
                spaceBetween: 10,
                on: {
                  slideChangeTransitionEnd: function(){
                    $("#wrap_id1 .gallery-thumbs .swiper-slide").eq(this.activeIndex).addClass("active swiper-slide-active").siblings().removeClass("active swiper-slide-active")
                  },
                },
              });
              var galleryThumbs1 = new Swiper('#wrap_id1 .gallery-thumbs', {
                spaceBetween: 10,
                slidesPerView: 4,
                touchRatio: 0.2,
                on:{
                  click: function(){
                    var i=galleryThumbs1.clickedIndex;
                    galleryTop1.slideTo(i);
                  },
                },
              });
						}
					}
			 }
		 }
	 });
	 
 }
function initKeziEvent(formId){
     $("#"+formId+" .jt_kezi em").click(function(){
		 var this_=$(this).attr("data-val");
		 var ipt = $("#"+formId+" input[name='kezi']").val();
		 var kezi = ipt+this_;
		 var res = checkKezi(kezi);						 
		 if(res.error){
			  alert(res.error);
			  return false;
		 }
		 var keziHtml = res.data;
		 $(".showKZ").html(keziHtml);
		 $("#"+formId+" input[name='kezi']").val(kezi);
	 });
	 $("#"+formId+" input[name='kezi']").blur(function(){
		 var kezi = $(this).val();											   
	     var res = checkKezi(kezi);	
		 var keziHtml = res.data;
		 $(".showKZ").html(keziHtml);
		 if(res.error){
			  alert(res.error);
			  return false;
		 }											   
	 });
}
 function checkKezi(kezi){
	 var result = {};
	 var temp = kezi;
	 $(".jt_kezi em").each(function(){
			var code = $(this).attr('data-val');
			var img = $(this).html();			
			for(var i=0;i<6;i++){
				if(kezi.indexOf(code)>=0){
				  kezi = kezi.replace(code,img);				 
			    }
				temp = temp.replace(code,'#');
			}
	 }); 
	 if(temp.length >6 ){
		 result.error = "刻字内容最多支持6个字符";
	 }
	 console.log(temp);
	 result.data = kezi;
	 return result;					 
}