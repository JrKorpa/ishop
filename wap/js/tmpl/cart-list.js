var key = getCookie('key');
var check_tsyd = false;
$(function (){
    template.helper('isEmpty', function(o) {
        for (var i in o) {
            return false;
        }
        return true;
    });
    template.helper('decodeURIComponent', function(o){
        return decodeURIComponent(o);
    });    
    if(!key){
         window.location.href = WapSiteUrl+'/login.html';         
		 return false;
    }
	//初始化页面数据
	function initCartList(isFirstInit){
		 $.ajax({
			url:ApiUrl+"/index.php?act=member_cart&op=cart_list",
			type:"post",
			dataType:"json",
			data:{key:key},
			success:function (result){
				if(result.datas.error){
					alert(result.datas.error);
					return false;
				}				
				if (result.datas.cart_list.length == 0) {
					addCookie('cart_count',0);
				}
				var rData = result.datas;
				
				rData.WapSiteUrl = WapSiteUrl;
				rData.check_out = true;
				var html = template.render('cart-list', rData);
				if (rData.cart_list.length == 0) {
					get_footer();
				}
				$("#cart-list-wp").html(html);
				//删除购物车
				$(".goods-del").click(function(){
					var  cart_id = $(this).attr("cart_id"); 
					$.sDialog({
						content: '确认删除吗？',
						okFn: function() { delCartList(cart_id); }
					});                               
				});
				
			   if(isFirstInit && getQueryString('goods_id')){					   
				   $("#add_goods").click();
				   $("#goods_form input[name='goods_id']").val(getQueryString('goods_id'));
				   $("#goods_form .search").click();
				   //setTimeout(function(){$("#goods_form .search").click();},100);
			   }
			   calculateTotalPrice();
		   }			
			
	     });//end $.ajax
	}
	initCartList(true);

	//删除购物车
	function delCartList(cart_id){
		$.ajax({
			url:ApiUrl+"/index.php?act=member_cart&op=cart_del",
			type:"post",
			data:{key:key,cart_id:cart_id},
			dataType:"json",
			success:function (res){
				if(checkLogin(res.login)){
					if(!res.datas.error && res.datas == "1"){
						initCartList();
						delCookie('cart_count');
						// 更新购物车中商品数量
						getCartCount();
					}else{
						alert(res.datas.error);
					}
				}
			}
		});
	}
   

	//去结算
	$('#cart-list-wp').on('click', ".check-out > a", function(){
		if (!$(this).parent().hasClass('ok')) {
			return false;
		}
		//购物车ID
		var cartIdArr = [];
		$('.cart-litemw-cnt').each(function(){
			if ($(this).find('input[name="cart_id"]').prop('checked')) {
				var cartId = $(this).find('input[name="cart_id"]').val();
				var goodsType = $(this).find('input[name="cart_id"]').attr("goods-type");
				var cartIdNum = cartId+"@"+goodsType;
				cartIdArr.push(cartIdNum);
			}
		});
		var cart_id = cartIdArr.join(',');
		window.location.href = WapSiteUrl + "/tmpl/order/buy_step1.html?ifcart=1&cart_id="+cart_id;
	});

     
    // 店铺全选
   /* $('#cart-list-title').on('click', '.store_checkbox', function(){
        $(this).parents('.nctouch-cart-container').find('input[name="cart_id"]').prop('checked', $(this).prop('checked'));
		$('#cart-list-wp').find('input[type="checkbox"]').prop('checked', $(this).prop('checked'));
        calculateTotalPrice();
    });*/
    // 所有全选
    $('#cart-list-title').on('click', '.all_checkbox', function(){
        $('#cart-list-wp').find('input[type="checkbox"]').prop('checked', $(this).prop('checked'));
        check_tsyd = check_tsyd?false:true;
        calculateTotalPrice();
    })
    
    $('#cart-list-wp').on('click', 'input[name="cart_id"]', function(){
        var cart_id = $(this).val();
        $.ajax({
            url:ApiUrl+"/index.php?act=member_cart&op=check_tsyd",
            type:'post',
            data:{key:key,cart_id:cart_id},
            dataType:'json',
            async: false,
            success:function(result){
                if(result.code == 200){
                    var fx_cart_id = result.datas.fx_cart_id;
                    if(fx_cart_id){
                        $('#cart-list-wp').find('input[type="checkbox"]').each(function(i, n){
                            if($(this).val() == fx_cart_id){
                                $(this).prop("checked", check_tsyd);
                            }
                            if($(this).val() == cart_id){
                                $(this).prop("checked", check_tsyd);
                            }
                        });
                        check_tsyd = check_tsyd?false:true;
                    }else{
                        $(this).prop("checked", check_tsyd);
                    }
                    
                }
            }
        });
        calculateTotalPrice();
    });
	var gift_list_load = false; 
	$("#add_gift").click(function(){
	   if(gift_list_load){
		   $("#gift_form select[name='goods_id']").val('');
		   $("#gift_info").html('');
		   return true;
	   }
	   setTimeout(function(){
		  $.ajax({
			url:ApiUrl+"/index.php?act=gift_goods&op=get_gift_list",
			type:'post',
			data:{key:key},
			dataType:'json',
			async: false,
			success:function(result){
				if(result.code == 200){
					gift_list_load = true;
					var html = "<option value=''>请选择赠品</option>";
					for(var i=0;i< result.datas.gift_list.length;i++){
						var gift_info = result.datas.gift_list[i];
						html += "<option value='"+gift_info.goods_id+"'>"+gift_info.goods_id+" | "+gift_info.goods_name+"</option>"
					}
					$("#gift_form select[name='goods_id']").html(html);						
					//$("#gift_form select[name='goods_id']").change();
				}else{	
					alert(result.datas.error);				   
				}
			}
	     });		   
						   
	  },1000);
	  
   });//end $("#add_gift")

   $("#add_goods").click(function(){
		 $("#goods_form input[name='goods_id']").val('');
		 $("#goods_info").html('');						 
   });
   $("#goods_form .scan").click(function(){
		if (window.cordovaext) {
            window.cordovaext.scan_barcode(function(code){
                //window.location.href = WapSiteUrl + "/tmpl/cart_list.html?goods_id="+code.text;
				$("#goods_form input[name='goods_id']").val(code.text);
				$("#goods_form .search").click();
            });
        }else{
		    alert("设备不支持扫描！");	
		}
   });
   $("#add_style").click(function(){
		 $("#style_form input[name='style_sn']").val('');
		 $("#style_info").html('');						 
   });
   $("#style_form .scan").click(function(){
		if (window.cordovaext) {
            window.cordovaext.scan_barcode(function(code){
                //window.location.href = WapSiteUrl + "/tmpl/cart_list.html?goods_id="+code.text;
				$("#style_form input[name='style_sn']").val(code.text);
				$("#style_form .search").click();
            });
        }else{
		    alert("设备不支持扫描！");	
		}
   });
   $("#add_qiban").click(function(){
		$("#goods_id").val("");								   
		$("#qiban_info").html("");						
		return true;									
  }); 
   $("#qiban_form .scan").click(function(){
		if (window.cordovaext) {
            window.cordovaext.scan_barcode(function(code){
                //window.location.href = WapSiteUrl + "/tmpl/cart_list.html?goods_id="+code.text;
				$("#qiban_form input[name='goods_id']").val(code.text);
				$("#qiban_form .search").click();
            });
        }else{
		    alert("设备不支持扫描！");	
		}
   });
   $("#qiban_form .search").click(function(){
		var formId = 'qiban_form';									
		var goods_id = $("#"+formId+" input[name='goods_id']").val();
		$("#qiban_info").html("");
		if(goods_id==""){
		   alert("请填写起版号");	
		   return false;	
		}
		$("#qiban_info").html("<div style='height:120px;text-align:center'>努力加载中...</div>");
		$.ajax({
				url:ApiUrl+"/index.php?act=style_goods&op=get_qiban_info_index",
				type:'post',
				data:{goods_id:goods_id,key:key},
				dataType:'json',
				async: false,
				success:function(result){
					if(result.code == 200){
						var html = template.render('qiban-info',result.datas); 
						$("#qiban_info").html(html);
						initKeziEvent(formId);
					}else{	
						alert(result.datas.error);				   
					}
				}
		 });
	});
	
	$("#qiban_form .save").click(function(){
		var formId = 'qiban_form';									  
		var goods_id = $("#"+formId+" input[name='goods_id']").val();
		var cert = $("#"+formId+" select[name='cert']").val();
		var cert_id = $("#"+formId+" input[name='cert_id']").val();
		var color = $("#"+formId+" select[name='color']").val();
		var clarity = $("#"+formId+" select[name='clarity']").val();
		var cut = $("#"+formId+" select[name='cut']").val();
		var kezi = $("#kezi").val();
		var goods_type = 3;//起版
		var flag = false;	
		$.ajax({
				url:ApiUrl+"/index.php?act=member_goods&op=qiban_add",
				type:'post',
				data:{goods_id:goods_id,cert:cert,cert_id:cert_id,color:color,clarity:clarity,cut:cut,kezi:kezi,key:key},
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
		 if(flag == true){
			$.ajax({
					url:ApiUrl+"/index.php?act=member_cart&op=cart_add",
					type:'post',
					data:{goods_id:goods_id,goods_type:3,quantity:1,key:key},
					dataType:'json',
					async: false,
					success:function(result){
						if(result.code == 200){
							initCartList();	
							$(".dialog-dingzhi-close").click();
						}else{	
							alert(result.datas.error);				   
						}
					}
			 });
		 }
	});
	
  
   $("#gift_form select[name='goods_id']").change(function(){
       var goods_id = $(this).val();
	   $("#gift_info").html("");
	   if(!goods_id){
		   return false;
	   }
	   $("#gift_info").html("<div style='height:120px;text-align:center'>努力加载中...</div>");
       $.ajax({
				url:ApiUrl+"/index.php?act=gift_goods&op=get_gift_info",
				type:'post',
				data:{goods_id:goods_id,key:key},
				dataType:'json',
				async: false,
				success:function(result){
					if(result.code == 200){
						var html = template.render('gift-info',result.datas); 
						$("#gift_info").html(html);						
					}else{	
						alert(result.datas.error);				   
					}
				}
		 });
   });
   $("#gift_form .save").click(function(){
       var formId = "gift_form";
	   var goods_id = $("#"+formId+" select[name='goods_id']").val();
	   if(!goods_id){
		  alert("请选择赠品");
		  return false;
	   }
	   var style_sn  = $("#"+formId+" input[name='style_sn']").val();
	   var shoucun  = $("#"+formId+" input[name='shoucun']").val();
	   var goods_name  = $("#"+formId+" input[name='goods_name']").val();
	   var goods_pay_price  = $("#"+formId+" input[name='goods_pay_price']").val();
	   var goods_price  = $("#"+formId+" input[name='goods_price']").val();
	   var flag = false;
       $.ajax({
				url:ApiUrl+"/index.php?act=member_goods&op=gift_add",
				type:'post',
				data:{style_sn:style_sn,goods_name:goods_name,shoucun:shoucun,goods_price:goods_price,goods_pay_price:goods_pay_price,key:key},
				dataType:'json',
				async: false,
				success:function(result){
					if(result.code == 200){
						goods_id = result.datas.goods_id;
						flag = true;
					}else{
						flag = false;
						alert(result.datas.error);				   
					}
				}
		 });
		 if(flag == true){
            $.ajax({
				url:ApiUrl+"/index.php?act=member_cart&op=cart_add",
				type:'post',
				data:{goods_id:goods_id,quantity:1,goods_type:5,key:key},
				dataType:'json',
				async: false,
				success:function(result){
					if(result.code == 200){
						initCartList();	
						$(".dialog-dingzhi-close").click();
					}else{	
						alert(result.datas.error);				   
					}
				}
		    });  		 
		 
		 }
   });
   
   $("#goods_form .search").click(function(){
		var formId = 'goods_form';								   
		var goods_id = $("#"+formId+" input[name='goods_id']").val();
		var is_dingzhi = $("#"+formId+" select[name='is_dingzhi']").val();
		$("#goods_info").html('');
		if(!goods_id){
		    alert("请输入货号");
			return false;
		}
		$("#goods_info").html("<div style='height:120px;text-align:center'>努力加载中...</div>");
	    $.ajax({
			 url:ApiUrl+"/index.php?act=style_goods&op=get_warehousegoods_info_index",
			 type:'post',
			 data:{goods_id:goods_id,is_dingzhi:is_dingzhi,key:key},
			 dataType:'json',
			 async: true,
			 success:function(result){
				 if(result.code ==200){
					 var html = template.render('goods-info',result.datas); 
					 $("#goods_info").html(html);
					 setGoodsFormState();
					 initKeziEvent(formId);
					 
					 $("#"+formId+" select[name='is_dingzhi']").change(function(){
						  if($(this).val()==1){
							  $("#goods_form .search").click();
						  }else{
							  $("#goods_form .search").click();
						  }
						  setGoodsFormState();
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
					 $("#"+formId+" select[name='xiangqian']").change(function(){						 
						  setGoodsFormState();
				     });
					 //加载证书号失去焦点事件
					 $("#"+formId+" input[name='cert_id']").blur(function(){
						 initGoodsDiamondFlag = false;												  
						 initGoodsDiamondInfo(formId,$(this).val());	 
					 });//
					 //加载成品定制价格方案事件
					 initCpdzPriceSearchEvent(formId);
					 //加载取价按钮点击事件
					 initDingzhiPriceEvent(formId);						 
					 
				 }else{	
				     $("#goods_info").html('');
					 alert(result.datas.error);				   
				 }
			 }
	  });									   
   });
   $("#goods_form .save").click(function(){
		var formId = 'goods_form';								 
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
						   $("#"+formId+" input[name='goods_price']").val(data.goods_price);
						   $("#"+formId+" input[name='goods_id']").attr("data-val",data.goods_id);
						   formData['goods_id '] = data.goods_id;
						   formData['goods_price '] = data.goods_price;
						   flag = true;
					  }else{
						   //$("#"+formId+" input[name='goods_price']").val(0);
						   //$("#"+formId+" input[name='goods_id']").attr("data-val",'');
						  // $("#"+formId+" input[name='goods_key']").val('');
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
			 if(flag == true){				  
				$.ajax({
						url:ApiUrl+"/index.php?act=member_cart&op=cart_add",
						type:'post',
						data:{goods_id:formData['goods_id'],goods_type:formData['goods_type'],quantity:1,key:key},
						dataType:'json',
						async: false,
						success:function(result){
							if(result.code == 200){
								initCartList();	
								$(".dialog-dingzhi-close").click();
							}else{	
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

			 if(flag == true){
				$.ajax({
						url:ApiUrl+"/index.php?act=member_cart&op=cart_add",
						type:'post',
						data:{goods_id:formData['goods_id'],goods_type:formData['goods_type'],quantity:1,key:key},
						dataType:'json',
						async: false,
						success:function(result){
							if(result.code == 200){
								initCartList();	
								$(".dialog-dingzhi-close").click();
							}else{	
								alert(result.datas.error);				   
							}
						}
				 });
			 }		 
			 
		 }
   }); 
   
   $("#style_form .search").click(function(){
		var formId= "style_form";								   
		var style_sn = $("#style_form input[name='style_sn']").val();
		$("#style_info").html("");
		if(!style_sn){
		    alert("请输入款号");
			return false;
		}
		$("#style_info").html("<div style='height:120px;text-align:center'>努力加载中...</div>");
	    $.ajax({
			 url:ApiUrl+"/index.php?act=style_goods&op=get_style_info_index",
			 type:'post',
			 data:{style_sn:style_sn,key:key},
			 dataType:'json',
			 async: true,
			 success:function(result){
				 if(result.code ==200){
					 var html = template.render('style-info',result.datas); 
					 $("#style_info").html(html);
					 
					 setStyleFormState();
					 initKeziEvent(formId);
					 
					 $("#style_info input[name='kezi'],#style_info input[name='carat']").blur(function(){																									   						   setStyleFormState();				  
					 });
					 $("#"+formId+" select[name='xiangkou']").change(function(){
					      setStyleFormState();
					 });
	
					 $("#"+formId+" select[name='is_dingzhi']").change(function(){
						  if($(this).val()==1){
							  //$("#style_form .create_price").show();  
							  $("#"+formId+" .show_msg").html("").hide();
							  //$("#"+formId+" .xianhuo_adds").hide();
						  }else{
							  $("#"+formId+" .show_msg").html("现货，不需生产").show();
							  //$("#"+formId+" .xianhuo_adds").show();
							  
							  //$("#style_form .search").click();
						  }
						  setStyleFormState();
				     });
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
					 //加载证书号失去焦点事件
					 $("#"+formId+" input[name='cert_id']").blur(function(){
						 initGoodsDiamondFlag = false;												  
						 initGoodsDiamondInfo(formId,$(this).val());	 
					 });//
					 //加载成品定制价格方案事件
					 initCpdzPriceSearchEvent(formId);
					 //加载取价按钮点击事件
					 initDingzhiPriceEvent(formId);					 
				 }else{					     
					 alert(result.datas.error);	
					 $("#style_info").html("");
				 }
			 }
	  });									   
   });
   $("#style_form .save").click(function(){
		 var flag = false;								 
         var formData = getStyleFormData();	
		 if(!formData){								 
			return false; 
		 }
		 formData['key'] = key;
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
					   $("#style_form input[name='goods_price']").val(data.goods_price);
					   $("#style_form input[name='goods_id']").val(data.goods_id);
					   formData['goods_id'] = data.goods_id;
					   formData['goods_price'] = data.goods_price;
					   flag = true;
				  }else{
					   //$("#style_form input[name='goods_price']").val(0);
					   //$("#style_form input[name='goods_id']").val('');
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
		 if(flag == true){
			$.ajax({
					url:ApiUrl+"/index.php?act=member_cart&op=cart_add",
					type:'post',
					data:{goods_id:formData['goods_id'],goods_type:1,quantity:1,key:key},
					dataType:'json',
					async: false,
					success:function(result){
						if(result.code == 200){
							initCartList();	
							$(".dialog-dingzhi-close").click();
						}else{	
							alert(result.datas.error);				   
						}
					}
			 });
		 }
	 
   }); 
   
});
//获取现货商品formData
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
    if(tuo_type==1 && xiangkou>0 && carat>0){
	   var is_cpdz = 1;
	}
	if(is_dingzhi==1){
		goods_type = 1;
		xianhuo_adds = '';
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
//现货下单 form表单
function setGoodsFormState(){
	var formId = "goods_form";
	var is_dingzhi = $("#"+formId+" select[name='is_dingzhi']").val();	
	var goods_type = $("#"+formId+" input[name='goods_type']").val();
	var tuo_type   = $("#"+formId+" select[name='tuo_type']").val();
	var shoucun   = $("#"+formId+" select[name='shoucun']").val();
	var shoucun_org   = $("#"+formId+" select[name='shoucun']").find("option").not(function(){return !this.selected}).attr("org");;
	var carat  = $("#"+formId+" input[name='carat']").val();
	var kezi  = $("#"+formId+" input[name='kezi']").val();
	var xiangqian  = $("#"+formId+" select[name='xiangqian']").val();
	var xiangkou  = $("#"+formId+" select[name='xiangkou']").val();
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
			  if(carat>0){
				  $("#"+formId+" input[name='cert_id']").prop("disabled",true).val("");
				  $("#"+formId+" select[name='cut']").prop("disabled",true);
			  }else{
				  $("#"+formId+" input[name='cert_id']").prop("disabled",false).val("");
				  $("#"+formId+" select[name='cut']").prop("disabled",false);				  
			  }
			  //$("#"+formId+" input[name='carat']").val(xiangkou);
			  
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
		 //$("#"+formId+" input[name='cert_id']").val("");
		 $("#"+formId+" select[name='xiangkou']").prop("disabled",false);
		 
		 $("#"+formId+" .create_price").show();  
		 //$("#"+formId+" .xianhuo_adds").hide();
	}else{
		$("#"+formId+" .show_msg").html("").hide();
		if(goods_type==2 || tuo_type==1){
		    $("#"+formId+" input[name='cert_id']").prop("disabled",true);
		}else{
			$("#"+formId+" input[name='cert_id']").prop("disabled",false);
		}
		//$("#"+formId+" select[name='xiangqian']").prop("disabled",false).val("不需工厂镶嵌");
		$("#"+formId+" select[name='xiangqian']").prop("disabled",false);
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
		var xqArray = ["需工厂镶嵌","客户先看钻再返厂镶嵌","工厂配钻，工厂镶嵌"];
		if(carat > 0 && $.inArray(xiangqian,xqArray)>=0){
			$("#"+formId+" input[name='xianhuo_adds'][value='镶石']").prop("checked",true);
		}else{
			$("#"+formId+" input[name='xianhuo_adds'][value='镶石']").prop("checked",false);
		}
		if(shoucun!="" && shoucun != shoucun_org){
			$("#"+formId+" input[name='xianhuo_adds'][value='改圈']").prop("checked",true);
		}else{
			$("#"+formId+" input[name='xianhuo_adds'][value='改圈']").prop("checked",false);
		}
	}	
	
	if(zhushi_num<1){
		$("#"+formId+" select[name='xiangqian']").prop("disabled",true).val("成品");
	}
}
//获取款号商品 formData
function getStyleFormData(){
   	var formId = "style_form";
	var goods_id = $("#"+formId+" input[name='goods_id']").val();
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
	if(is_dingzhi==1){
	   xianhuo_adds = '';	
	}
	
	var is_cpdz = 0;
    if(xiangkou>0 && tuo_type==1 && is_dingzhi==1){
	   var is_cpdz = 1;	
	}
	
	var data = {};
	data['goods_id'] = goods_id;
	data['style_sn'] = style_sn;	
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
	var kezi  = $("#"+formId+" input[name='kezi']").val();
	var xiangkou = $("#"+formId+" select[name='xiangkou']").val();
	var xiangqian  = $("#"+formId+" select[name='xiangqian']").val();
	
	if(is_dingzhi==1){
		 if(tuo_type ==1){
			 $("#"+formId+" select[name='xiangqian']").prop("disabled",true).val("工厂配钻，工厂镶嵌");
		 }else{
			  // $("#"+formId+" input[name='policy_id']").val(0);
			  $("#"+formId+" select[name='xiangqian']").prop("disabled",false).val("需工厂镶嵌");
			  $("#"+formId+" input[name='cert_id']").prop("disabled",false).val("");
		 }
		 //$("#"+formId+" select[name='tuo_type']").prop("disabled",false);	
		 $("#"+formId+" select[name='facework']").prop("disabled",false);
		 $("#"+formId+" select[name='jinse']").prop("disabled",false);
		 
		 $("#"+formId+" .create_price").show();  
		 //$("#"+formId+" .xianhuo_adds").hide();
	}else{
		$("#"+formId+" select[name='xiangqian']").prop("disabled",false).val("不需工厂镶嵌");
		//$("#"+formId+" select[name='tuo_type']").prop("disabled",true);
		//$("#"+formId+" select[name='facework']").prop("disabled",true);	
		//$("#"+formId+" .xianhuo_adds").show();
		$("#"+formId+" input[name='xianhuo_adds']").prop("disabled",true);
		if(kezi!=''){			
		    $("#"+formId+" input[name='xianhuo_adds'][value='刻字']").prop("checked",true);
		}else{
			$("#"+formId+" input[name='xianhuo_adds'][value='刻字']").prop("checked",false);
		}
		var xqArray = ["需工厂镶嵌","客户先看钻再返厂镶嵌","工厂配钻，工厂镶嵌"];
		if(carat > 0 && tuo_type >1 && $.inArray(xiangqian,xqArray)>=0 ){
			$("#"+formId+" input[name='xianhuo_adds'][value='镶石']").prop("checked",true);
		}else{
			$("#"+formId+" input[name='xianhuo_adds'][value='镶石']").prop("checked",false);
		}
	}
	//选择【镶嵌4C裸钻、工厂配钻工厂镶嵌】，证书号字段不能编辑
	var xq1Array = ["工厂配钻，工厂镶嵌"];
	if($.inArray(xiangqian,xq1Array)>=0 ){
		$("#"+formId+" input[name='cert_id']").prop("disabled",false).val("");
	}else{
		$("#"+formId+" input[name='cert_id']").prop("disabled",true);
	}
	
	if(tuo_type ==1 && xiangkou>0){
		$("#"+formId+" select[name='xiangqian']").prop("disabled",true).val("工厂配钻，工厂镶嵌");
		 
		$("#"+formId+" input[name='cert_id']").prop("disabled",true).val("");
		$("#"+formId+" select[name='cut']").prop("disabled",true);
		$("#"+formId+" input[name='carat']").val(xiangkou);
		
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
		//不需要镶嵌，禁用主石信息
		$("#"+formId+" select[name='xiangkou']").prop("disabled",true).val("0.00");
		$("#"+formId+" input[name='cert_id']").prop("disabled",true).val("");
		$("#"+formId+" select[name='cert']").prop("disabled",true).val("");
		$("#"+formId+" select[name='color']").prop("disabled",true).val("");
		$("#"+formId+" select[name='clarity']").prop("disabled",true).val("");
		$("#"+formId+" select[name='xiangqian']").prop("disabled",true).val("成品");
	}

}

function calculateTotalPrice() {
    var totalPrice = parseFloat("0.00");
    $('.cart-litemw-cnt').each(function(){
        if ($(this).find('input[name="cart_id"]').prop('checked')) {
            totalPrice += parseFloat($(this).find('.goods-price').attr("goods_pay_price")) * parseInt($(this).find('.value-box').find('input').val());
        }
    });
    $(".total-money").find('em').html(totalPrice.toFixed(2));
    check_button();
    return true;
}

function get_footer() {
        footer = true;
        $.ajax({
            url: WapSiteUrl+'/js/tmpl/footer.js',
            dataType: "script"
          });
}

function check_button() {
    var _has = false
    $('input[name="cart_id"]').each(function(){
        if ($(this).prop('checked')) {
            _has = true;
        }
    });
    if (_has) {
        $('.check-out').addClass('ok');
    } else {
        $('.check-out').removeClass('ok');
    }
}
