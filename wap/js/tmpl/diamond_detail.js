//v3-b11
var key = getCookie('key');
var goods_sn = getQueryString('goods_sn');
var cert_id  = getQueryString('cert_id');
var style_sn = getQueryString('style_sn');
var carat = getQueryString('carat');
var is_xianhuo = false;
var cart = "";

if(!isNaN(parseInt(goods_sn)) && goods_sn>0){
	is_xianhuo = true;	
}else if(goods_sn){
	var arr = goods_sn.split("-");
    cart = cart?cart:arr[3]/100;
	style_sn = style_sn?style_sn:arr[0];
}

var diamond_exists = false;
$(function(){
    if(!key){
       window.location.href = WapSiteUrl+'/login.html';
    }
	var jietuo_cert_id = sessionStorageGet("jietuo_cert_id");//是否先从戒托下单
	if(jietuo_cert_id && jietuo_cert_id == cert_id && goods_sn){
		if(is_xianhuo){
			window.location.href = WapSiteUrl+'/tmpl/xianhuo_detail.html?goods_sn='+goods_sn+"&style_sn="+style_sn+"&cert_id="+cert_id;
		}else{
		    window.location.href = WapSiteUrl+'/tmpl/jietuo_detail.html?goods_sn='+goods_sn+"&cert_id="+cert_id;
		}
	}
    $(".nav-sel-jietuo").click(function(){	
		if(is_xianhuo){
			window.location.href = WapSiteUrl+'/tmpl/xianhuo_detail.html?goods_sn='+goods_sn+"&style_sn="+style_sn+"&cert_id="+cert_id;
		}else if(goods_sn){
			window.location.href = WapSiteUrl+'/tmpl/jietuo_detail.html?goods_sn='+goods_sn+"&cert_id="+cert_id;
		}else if(cert_id){
	        window.location.href = WapSiteUrl+'/tmpl/jietuo_list.html?cert_id='+cert_id;		
		}else{
			window.location.href = WapSiteUrl+'/tmpl/jietuo_list.html';		
		}
	});
	$(".nav-sel-dia").click(function(){	
		if(cert_id){	
		     if(goods_sn){
		        window.location.href = WapSiteUrl+'/tmpl/diamond.html?cert_id='+cert_id+'&goods_sn='+goods_sn;
			 }else{
				window.location.href = WapSiteUrl+'/tmpl/diamond.html?cert_id='+cert_id;
			 }
		}else if(goods_sn){
		    window.location.href = WapSiteUrl+'/tmpl/diamond.html?goods_sn='+goods_sn;
		}else{
			window.location.href = WapSiteUrl+'/tmpl/diamond.html';
		}
	});	
    template.helper('isEmpty', function(obj){
        // 本身为空直接返回true
        if (obj == null) return true;

        // 然后可以根据长度判断，在低版本的ie浏览器中无法这样判断。
        if (obj.length > 0)    return false;
        if (obj.length === 0)  return true;

        //最后通过属性长度判断。
        for (var key in obj) {
            if (hasOwnProperty.call(obj, key)) return false;
        }

        return true;
    });
	$.ajax({
		url:ApiUrl+"/index.php?act=diamond&op=diamond_detail",
		type:'get',
		data:{cert_id:cert_id,key:key,cart:cart,style_sn:style_sn},
		jsonp:'callback',
		dataType:'jsonp',
		success:function(result){
            if(result.code == 200){
                var data = result.datas;
                //console.log(data);
                data.WapSiteUrl = WapSiteUrl;
                data.goods_sn = goods_sn;
                //var html = template.render('detail-image', data);             
                //$("#diamond-image").html(html);
                var leng =data.diamond_detail.length > 1?true:false;
                data.leng = leng;
                var html = template.render('detail-param', data);               
                $("#detailCent-box").html(html);
                swip();
                if(leng == true){
                    swip2();
                }
                showEx();
                
                //钻托组合情况下，隐藏添加到购物车
                if(cert_id && goods_sn){
                    $("#add_cart").hide();
                    $(".fixbtn a").css("width","50%");
                }else{
                    $(".fixbtn a").css("width","33.3%");
                }
            }else{
                alert(result.error_msg);
            }
		}
	});
	function checkFormData(){
		 var data = {};
		 data['goods_id'] = cert_id;
		 data['quantity'] = '1';
		 data['goods_type'] = '2';
		 return data;		
	}
     //挑选，确认商品
	 var dingzhiFormLoad = false;
	 var dingzhiSaveBtn = false;
	 function initDingzhiForm(){
		 if(!checkFormData()){
			  return false;
		 }
		 if(dingzhiSaveBtn == "next_buy"){
			 window.location.href = WapSiteUrl + '/tmpl/jietuo_detail.html?cert_id='+cert_id;
			 return true;
		 }
		 //只加载一次						  
		 if(dingzhiFormLoad == true ){
			 //return true;
		 }
		 $.ajax({
				 url:ApiUrl+"/index.php?act=diamond&op=get_diamond_info_index",
				 type:'post',
				 data:{cert_id:cert_id,key:key},
				 dataType:'json',
				 async: true,
				 success:function(result){
					 if(result.code ==200){
						 dingzhiFormLoad = true;
						 var html = template.render('dingzhi-form',result.datas); 
						 $("#dingzhi_form .dingzhi-body").html(html);						 
					 }else{	
						 alert(result.datas.error);				   
					 }
				 }
		  });
	 }
	 function goods_save(){ 
	     var xiangqian = $("#xiangqian").val();	
		 var postData = {
			 xiangqian:xiangqian,goods_id:cert_id,key:key			 
		 };
		 var flag = false;
		 $.ajax({
				 url:ApiUrl+"/index.php?act=member_goods&op=diamond_add",
				 type:'post',
				 data:postData,
				 dataType:'json',
				 async: false,
				 success:function(result){
					 if(result.code ==200){							 		
						 flag = true;
					 }else{	
						 alert(result.datas.error);	
						 flag = false;
					 }
				 }
		  });
		  if(dingzhiSaveBtn=="add_cart" && flag == true){
			  postData = {
				 goods_id:cert_id,quantity:1,goods_type:2,key:key			 
			  };
			  $.ajax({
				 url:ApiUrl+"/index.php?act=member_cart&op=cart_add",
				 type:'post',
				 data:postData,
				 dataType:'json',
				 async: false,
				 success:function(result){
					 if(result.code ==200){
						 $(".dialog-dingzhi-close").click();
						 flag = true;						 
					 }else{	
						 alert(result.datas.error);	
						 flag = false;
					 }
				 }
			   });
			  
		  }
		 return flag;
 
	 }
	 $(document).delegate("#search_jietuo","click",function(){
            var do_cert_id = $(this).attr('data-val') ? $(this).attr('data-val'):cert_id;
			if(goods_sn){
				if(is_xianhuo){
					window.location.href = WapSiteUrl+'/tmpl/xianhuo_detail.html?goods_sn='+goods_sn+'&style_sn='+style_sn+'&cert_id='+do_cert_id;
				}else{
					window.location.href = WapSiteUrl+'/tmpl/jietuo_detail.html?goods_sn='+goods_sn+'&cert_id='+do_cert_id;			
				}
			}else{
				window.location.href = WapSiteUrl+'/tmpl/jietuo_list.html?cert_id='+do_cert_id;
			}		   
	 });
	 $(document).delegate("#add_cart","click",function(){
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

	 $(document).delegate("#buy_now","click",function(){
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

	 $(document).delegate("#next_buy","click",function(){
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
	 $(document).delegate("#dingzhi_form .save","click",function(){
	 
		  var postData = checkFormData();
		  if(!postData){		
			  return false;
		  }		  
		  if(!goods_save()){
			   return false; 
		  }
		  if(dingzhiSaveBtn=="add_cart"){
			  alert("添加成功!");
			  $(".dialog-dingzhi-close").click();
		  }else if(dingzhiSaveBtn=="buy_now"){			 
			  window.location.href='order/buy_step1.html?goods_id='+cert_id+"@2"; 
		  }else if(dingzhiSaveBtn=="next_buy"){
			  window.location.href = WapSiteUrl + '/tmpl/jietuo_detail.html?cert_id='+cert_id;
		  }else{
			  alert("未知按钮事件"); 
		  }
					 
	 });

});

function swip(){
    var galleryTop = new Swiper('.gallery-top0', {
        spaceBetween: 10,
        loop:false,
    });
    var galleryThumbs = new Swiper('.gallery-thumbs0', {
        spaceBetween: 10,
        slidesPerView: 4,
        touchRatio: 0.2,
        loop: false,
        slideToClickedSlide: true,
    });
    galleryTop.controller.control = galleryThumbs;
    galleryThumbs.controller.control = galleryTop;
}

function swip2(){
    var galleryTop1 = new Swiper('.gallery-top1', {
        spaceBetween: 10,
        loop:false,
    });
    var galleryThumbs1 = new Swiper('.gallery-thumbs1', {
        spaceBetween: 10,
        slidesPerView: 4,
        touchRatio: 0.2,
        loop: false,
        slideToClickedSlide: true,
    });
    galleryTop1.controller.control = galleryThumbs1;
    galleryThumbs1.controller.control = galleryTop1;
}


//加入對比
function addContrast (cert_id) {
    var key = getCookie('key');
    $.ajax({
        url:ApiUrl+"/index.php?act=diamond&op=diamond_detail",
        type:'get',
        data:{cert_id:cert_id,key:key},
        jsonp:'callback',
        dataType:'jsonp',
        success:function(result){
            var data = result.datas.diamond_detail;
            if(!$.isEmptyObject(data)){
                var diffinfo = sessionStorageGet("diamond_info");
                if(!$.isEmptyObject(diffinfo)){
                    for (var i = 0; i < diffinfo.length; i++) {						
                         var arr = JSON.parse(diffinfo[i]);
						 console.log(data[0].cert_id);
                         if(arr[0].cert_id == data[0].cert_id){
                            alert("已加入对比");return;
                         }
                    };
                }
            }
			
            sessionStorageAdd("diamond_info", data, false);
            alert("加入对比成功");return;
        }
    });
}

function showEx(){
    var cut={"EX":"极好。代表只有3%的一流高质量钻石才能达到的标准。这种切工使钻石几乎反射了所有进入钻石的光线。一种高雅且杰出的切工。",
        "VG":"很好。 代表大约15%的钻石切工。可以使钻石反射出和标准等级切工的光芒。",
        "G":"好。代表大约25%的钻石切工。是钻石反射了大部分进入钻使内部的光。"};

    var color={"D":"完全无色。 最高色级，极其稀有!",
        "E":"无色。 仅仅只有宝石鉴定专家能够检测到微量颜色。是非常稀有的钻石",
        "F":"无色。 少量的颜色只有珠宝专家可以检测到，但是仍然被认为是无色级。属于高品质钻石。",
        "G-H":"接近无色。当和较高色级钻石比较时，有轻微的颜色。但是这种色级的钻石仍然拥有很高的价值。",
        "I-J":"接近无色。 可检测到轻微的颜色。价值较高。",
        "K-L-M-N":"颜色较深。 火彩差，建议客户不使用。"};

    var clarity={"FL":"完美无暇。 专业人士在10倍放大条件下观察，内外无任何瑕疵，在所有净度等级中这种是最稀有和最珍贵的。",
        "IF":"内无瑕。 专业人士在10倍放大条件下观察，内部无瑕疵，但是在钻石表面有很微小的瑕疵。",
        "VVS1":"极微瑕1级。 专业人士在10倍放大条件下观察到只有单个极微小的瑕疵。",
        "VVS2":"极微瑕2级。 专业人士在10倍放大条件下观察到个别极微小瑕疵。",
        "VS1":"微瑕1级。 10倍放大条件下可见单个微小瑕疵。",
        "VS2":"微瑕2级。 10倍放大条件下可见数个微小瑕疵。",
        "SI1":"小瑕疵1级。 10倍放大条件下容易观察到明显瑕疵。",
        "SI2":"小瑕疵2级。 10倍放大条件下容易观察到数个明显瑕疵。"};

    $(".jieshao ul").each(function(i){
        var cut_=$(".jieshao ul").eq(0).attr("data-act");
        var color_=$(".jieshao ul").eq(1).attr("data-act");
        var clarity_=$(".jieshao ul").eq(2).attr("data-act");

        if(color_ =="G" || color_=="H"){

            $(".color-tip").text(color["G-H"]);

        }else if(color_ =="I" || color_=="J"){

            $(".color-tip").text(color["I-J"]);

        }else if(color_ =="K" || color_=="L" || color_=="M" || color_=="N"){

            $(".color-tip").text(color["K-L-M-N"]);

        }else{
            $(".color-tip").text(color[color_]);
        }
        $(".cut-tip").text(cut[cut_]);
        $(".clarity-tip").text(clarity[clarity_]);
    });
    $(".jieshao-top a").click(function(){
        var c=$(this).attr("data-type");
        $(this).addClass("active").siblings().removeClass("active");
        $(".jieshao-btm div[data-type="+c+"]").show().siblings("div").hide();
    });
}