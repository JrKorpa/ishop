var key = getCookie('key');
// buy_stop2使用变量
var ifcart = getQueryString('ifcart');
if(ifcart==1){
    var cart_id = getQueryString('cart_id');
}else{
   // var cart_id = getQueryString("goods_id")+'|'+getQueryString("buynum");
   var cart_id = getQueryString("goods_id");
}
var order_id;
var pay_name = 'offline';
var invoice_id = 0;
var address_info,vat_hash,offpay_hash,offpay_hash_batch,voucher,pd_pay,password,fcode='',rcb_pay,rpt,payment_code,store_id;
var message = {};
// change_address 使用变量
var freight_hash,city_id,area_id
// 其他变量
var area_info;
var goods_id;
$(function() {
 
    template.helper('isEmpty', function(o) {
        var b = true;
        $.each(o, function(k, v) {
            b = false;
            return false;
        });
        return b;
    });
    
    template.helper('pf', function(o) {
        return parseFloat(o) || 0;
    });

    template.helper('p2f', function(o) {
        return (parseFloat(o) || 0).toFixed(2);
    });

    $(document).delegate("#khphone",'change',function(){
        var khphone   = $("#khphone").val();
        if(khphone == ''){alert('请填写您的手机号！');return false;}
        if(!isMobile(khphone)){alert("手机号码格式不正确！");return false;}
        if(khphone != ''){
            $.ajax({
                url:ApiUrl+"/index.php?act=member_order&op=get_vip_list_by_mob",
                type:'get',
                data:{key:key,khphone:khphone},
                jsonp:'callback',
                dataType:'jsonp',
                success:function(result){
                    var data = result.datas;
					console.log(data);
                    if(result.code == 200){
                        $("#khname").val(data.khname);
                        $("#source_id").val(data.source_id);
                        $("#mob_phone").val(khphone);
                        $("#true_name").val(data.khname);
                    }else{
                        $("#khname").val("");
                        $("#source_id").val("");
                        $("#mob_phone").val(khphone);
                        $("#true_name").val("");
                    }					
					$(".chosen").trigger("chosen:updated");
                }
            }); 
        }
    });

    $(document).delegate("#chain_id",'click',function(){
         if($(this).val()==1){
             $("#address_form .address-info").hide(); 
         }else{
             $("#address_form .address-info").show(); 
         }
         $("#mob_phone").blur();                 
    });

    $(document).delegate("#khname",'blur',function(){
        var khname   = $("#khname").val();
        if(khname != ''){
            $("#true_name").val(khname);
        }
    });

    var _init = function (address_info) {		
        var totals = 0;
		address_info = address_info?address_info:sessionStorageGet("order_address_info");
        // 购买第一步 提交
        $.ajax({//提交订单信息
            type:'post',
            url:ApiUrl+'/index.php?act=member_buy&op=buy_step1',
            dataType:'json',
            data:{key:key,cart_id:cart_id,ifcart:ifcart,address_info:address_info},
            success:function(result){
                if (result.code!=200) {
					alert(result.datas.error);
                    return false;
                }
				if(result.datas.error){
				    $("#ToBuyStep2").hide();	
				}
                // 商品数据
                result.datas.WapSiteUrl = WapSiteUrl;
                var html = template.render('goods-list', result.datas);
                $("#deposit").html(html);
               

                for (var k in result.datas.store_final_total_list) {
					store_id = k;
                    // 总价
                    $('#storeTotal' + k).html(result.datas.store_final_total_list[k]);
                    totals += parseFloat(result.datas.store_final_total_list[k]);               
                }
				$(".goods_discount_btn").click(function(){
					var formId = "discount_form";									
					var goods_id = $(this).attr("goods_id");
					var goods_type = $(this).attr("goods_type");
					var goods_hand_price = $(this).attr("goods_hand_price");
					var discount_code = $(this).attr("discount_code");
					$("#"+formId+" input[name='goods_id']").val(goods_id);
					$("#"+formId+" input[name='goods_type']").val(goods_type);
					$("#"+formId+" input[name='goods_hand_price']").val(goods_hand_price);
					$("#"+formId+" input[name='discount_code']").val(discount_code);					
	     															  
				});
				
				$(".goods-del").on("click",function(){
				   if(confirm("确定删除赠品?")){
					  var curr_goods_id = $(this).attr("goods_id");
					  var curr_cart_id = $(this).attr("cart_id");
					  var cart_id_array = cart_id.split(",");
					  if(ifcart){
						  $.ajax({
							url:ApiUrl+"/index.php?act=member_cart&op=cart_del",
							type:"post",
							data:{key:key,cart_id:curr_cart_id},
							dataType:"json",
							success:function (res){								
								if(!res.datas.error && res.datas == "1"){ 
									 cart_id_array.splice(cart_id_array.indexOf(curr_cart_id+"@5"), 1);
									 refreshUrl(cart_id_array.join(","));
								}else{
									alert(res.datas.error);
								}
							}
						});
					  }else{
						  cart_id_array.splice(cart_id_array.indexOf(curr_goods_id+"@5"), 1);
						  refreshUrl(cart_id_array.join(","));
					  }
					 
				   }
			   });
                // 计算总价
                var total_price = totals - 0;
                if (total_price <= 0) {
                    total_price = 0;
                }
                $('#totalPrice,#onlineTotal').html(total_price.toFixed(2));
            }
        });
		
		
    }
    // 初始化
    _init();
    // 支付
    $('#ToBuyStep2').click(function(){
		$('#ToBuyStep2').addClass("none-event").html('提交中...');							
		if(order_id){
			window.location.href = WapSiteUrl + '/tmpl/member/order_detail.html?order_id='+order_id;
			return true;
		}
        var pay_message = store_id+"|"+$("#storeMessage").val();       
		address_info = sessionStorageGet("order_address_info");
		pay_name = 'chain';
        $.ajax({
            type:'post',
            url:ApiUrl+'/index.php?act=member_buy&op=buy_step2',
            data:{
                key:key,
                ifcart:ifcart,
                cart_id:cart_id,
    			address_info:address_info,	
                vat_hash:vat_hash,
                offpay_hash:offpay_hash,
                offpay_hash_batch:offpay_hash_batch,
                pay_name:pay_name,
                invoice_id:0,
                voucher:'',
                pd_pay:0,
                password:'',
                fcode:fcode,
                rcb_pay:0,
                rpt:'',
                pay_message:pay_message,//订单备注
                },
            dataType:'json',
            success: function(result){
                checkLogin(result.login);
                if (result.code !=200) {   					
					$('#ToBuyStep2').removeClass("none-event").html('下一步');
					alert(result.datas.error);
                    return false;
                }else if(result.datas.order_id<=0){
					$('#ToBuyStep2').removeClass("none-event").html('下一步');
					alert("订单生成失败");
					return false;
				}else{
					sessionStorageAdd("order_address_info",null, true);
                    sessionStorageAdd("vip_info",null, true);
					order_id = result.datas.order_id;
					window.location.href = WapSiteUrl + '/tmpl/member/order_detail.html?order_id='+order_id;				
				}
                
            }
        });
    });
	
	
	//收货地址begin
	function initProvice(provice_id){     
		 $.ajax({
				url:ApiUrl+"/index.php?act=area&op=area_list&area_id=0",
				type:'get',
				data:{},
				dataType:'json',
				success:function(result){
					var html = "<option value=''>请选择所在省</option>";
					if(result.code ==200){				    
					   for(var i=0;i < result.datas.area_list.length;i++){
						  var area = result.datas.area_list[i];
						  var selected = area.area_id==provice_id?" selected":"";
						  html += "<option value="+area.area_id+" name="+area.area_name+""+selected+">"+area.area_name+"</option>";
					   }				   
					}
					$("#provice_id").html(html);
			   }
		});
	}
	function initCity(provice_id,city_id){
	   $.ajax({
			url:ApiUrl+"/index.php?act=area&op=area_list&area_id="+provice_id,
			type:'get',
			data:{},
			dataType:'json',
			success:function(result){
				var html = "<option value=''>请选择城市</option>"; 
				if(result.code ==200){				   
				   for(var i=0;i < result.datas.area_list.length;i++){
					  var area = result.datas.area_list[i];
					  var selected = area.area_id==city_id?" selected":"";
					  html += "<option value="+area.area_id+" name="+area.area_name+""+selected+">"+area.area_name+"</option>";
				   }				   
				}
				$("#city_id").html(html);
		   }
		});
	}
	function initArea(city_id,area_id){
	   $.ajax({
			url:ApiUrl+"/index.php?act=area&op=area_list&area_id="+city_id,
			type:'get',
			data:{},
			dataType:'json',
			success:function(result){
				var html = "<option value=''>请选择地区</option>";
				if(result.code ==200){				   
				   for(var i=0;i < result.datas.area_list.length;i++){
					  var area = result.datas.area_list[i];
					  var selected = area.area_id==area_id?" selected":"";
					  html += "<option value="+area.area_id+" name="+area.area_name+""+selected+">"+area.area_name+"</option>";
				   }				   
				}
				$("#area_id").html(html);
		   }
		});
	}
    function isMobile(strMobile){
		var patternMobile = /^1\d{10}$/;   
		if (patternMobile.test(strMobile)){ 
		    return true;
		}else{
			return false;
		}
	}
	//sessionStorageAdd("order_address_info",[], true);
	initAddressList();
	function initAddressList(){
	   var address_info = sessionStorageGet("order_address_info");
	   var html = template.render('address-list',address_info);
	   $("#address_list").html(html);
	   
	   $("#add_address").click(function(){
            var address_info = sessionStorageGet("order_address_info");
            $.ajax({
                url:ApiUrl+"/index.php?act=member_order&op=get_sources_list",
                type:'get',
                data:{key:key},
                jsonp:'callback',
                dataType:'jsonp',
                success:function(result){
                    var Animals = new Object(); 
                    var data = result.datas;
                    if(address_info == false){
                        var vipinfo = sessionStorageGet("vip_info");
                        if(vipinfo == false){

                        }else{
                            Animals = vipinfo;
                        }
                    }else{
                        Animals = address_info;
                    }
                    Animals.sourcelist = data.sourcelist;
                    var html = template.render('address-info',Animals); 
                    $("#address_form .modal-body").html(html);
					$(".chosen").chosen();
					$(".chosen").trigger("chosen:updated");
					$('.chosen-container').css('width','33%');
                }
            });
			var provice_id = 0;
			var city_id = 0;
			var area_id = 0;
			if(address_info){
			   provice_id = address_info.provice_id;
			   city_id = address_info.city_id;
			   area_id = address_info.area_id;
			}
			initProvice(provice_id);
			if(provice_id>0){
				initCity(provice_id,city_id);
			}
			if(city_id>0){
				initArea(city_id,area_id);
			}
            $(document).delegate("#provice_id",'change',function(){
			//$("#provice_id").change(function(){
			   var provice_id = $(this).val()?$(this).val():999999;
			   initCity(provice_id,city_id);
			});
            $(document).delegate("#city_id",'change',function(){
			//$("#city_id").change(function(){
			   var city_id = $(this).val()?$(this).val():999999;
			   initArea(city_id,area_id);
			});
			
            $(document).delegate("#mob_phone",'change',function(){
			//$("#mob_phone").blur(function(){		 						 
				 var mob_phone = $(this).val();
				 if((address_info && address_info.mob_phone == $("#mob_phone").val()) || mob_phone==""){
					 return false; 
				 }
				 var chain_id = $("#chain_id").val();
				 $.ajax({
						url:ApiUrl+"/index.php?act=member_address&op=address_info",
						type:'post',
						data:{mob_phone:mob_phone,key:key},
						dataType:'json',
						async: false,
						success:function(result){
							if(result.code == 200){
								var address_info = result.datas.address_info;
								var provice_id = address_info.provice_id; 
								var city_id = address_info.city_id; 
								var area_id = address_info.area_id; 
								
								$("#true_name").val(address_info.true_name);
								if(chain_id==2){
									initProvice(address_info.provice_id);
									if(provice_id>0){
										initCity(provice_id,city_id);
									}
									if(city_id>0){
										initArea(city_id,area_id);
									}									
									$("#address").val(address_info.address)
								}
							}
						}
				});						 
			});//$("#mob_phone").blur end
			
			
		});
	   
	   $("#del_address").click(function(){
			if(confirm("确定删除当前收货地址?")){
				change_address(null);
			}		
	   });
	   
	}
	
	$("#address_save").on("click",function(){
		var address_info = {};
		address_info['chain_id'] = $("#chain_id").val()==1?1:0;
		address_info['provice_id'] = $("#provice_id").val();
		address_info['city_id'] = $("#city_id").val();
		address_info['area_id'] = $("#area_id").val();
        address_info['source_id'] = $("#source_id").val();
		
		if(address_info['provice_id']){
		    address_info['provice'] = $("#provice_id").find("option").not(function(){return !this.selected}).attr("name");
		}else{
			address_info['provice'] = '';
		}
		if(address_info['city_id']){
		    address_info['city'] = $("#city_id").find("option").not(function(){return !this.selected}).attr("name");
		}else{
			address_info['city'] = '';
		}
		if(address_info['area_id']){
		    address_info['area'] = $("#area_id").find("option").not(function(){return !this.selected}).attr("name");
		}else{
			address_info['area'] = '';
		}	
        if(address_info['source_id']){
            address_info['source_name'] = $("#source_id").find("option").not(function(){return !this.selected}).attr("source_name");
        }else{
            address_info['source_name'] = '';
        }   	
		address_info['area_info'] = address_info['provice']+' '+address_info['city']+' '+address_info['area'];
		
		address_info['true_name'] = $("#true_name").val();
		address_info['mob_phone'] = $("#mob_phone").val();
		address_info['tel_phone'] = $("#tel_phone").val();
		address_info['source_id'] = $("#source_id").val();
		address_info['address'] = $("#address").val();	
		if(address_info['true_name']==""){
		    alert("请填写收货人");
			return false;
		}
		if(address_info['mob_phone']==""){
		    alert("请填写收货人手机");
			return false;
		}
        if(address_info['source_id']==""){
            alert("请选择客户来源");
            return false;
        }
		if($("#source_id_chosen").find("span").html()==""||$("#source_id_chosen").find("span").html()=="请选择"){
		    alert("请选择客户来源");
			return false;
		}
		if(address_info['chain_id']==0){
			if(address_info['area_id']<=0){
				 alert("请选择地区");
				return false;
			}
			if(address_info['address']==''){
				 alert("请填写收货地址");
				return false;
			}
			
		}else{
		    if(address_info['area_info'].trim()==""){
			    address_info['area_info'] = "客户到店提货";
			}	
		}
	    change_address(address_info);
		$(".dialog-address-close").click();	
	});
	function change_address(_address_info){
		address_info = _address_info;
		sessionStorageAdd("order_address_info",address_info, true);
		initAddressList();
		_init(address_info);
	}
	//收货地址 end 
    
	//折扣优惠保存
	$("#discount_form .save").click(function(){
		var formId = "discount_form";									 
		var goods_id = $("#"+formId+" input[name='goods_id']").val();
		var goods_type = $("#"+formId+" input[name='goods_type']").val();
		var goods_hand_price = $("#"+formId+" input[name='goods_hand_price']").val();
		var discount_code = $("#"+formId+" input[name='discount_code']").val();
		$.ajax({
			url:ApiUrl+"/index.php?act=member_goods&op=goods_edit_discount",
			type:'post',
			data:{goods_id:goods_id,goods_type:goods_type,goods_hand_price:goods_hand_price,discount_code:discount_code,key:key},
			dataType:'json',
			async: true,
			success:function(result){
				if(result.code ==200){
				   _init();
				   $(".dialog-discount-close").click();
				}else{
				   alert(result.datas.error);
				}
			}
		 });		 
									  
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
       
   });
   $("#gift_form select[name='goods_id']").change(function(){
       var goods_id = $(this).val();
	   $("#gift_info").html("");
	   if(!goods_id){
		   return false;
	   }
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
	   var goods_id = "";
	   var cart_id_array = cart_id.split(',');
       $.ajax({
				url:ApiUrl+"/index.php?act=member_goods&op=gift_add",
				type:'post',
				data:{style_sn:style_sn,goods_name:goods_name,shoucun:shoucun,goods_price:goods_price,goods_pay_price:goods_pay_price,key:key},
				dataType:'json',
				async: false,
				success:function(result){
					if(result.code == 200){	
					    goods_id = result.datas.goods_id;	  				    
						if(!ifcart){
							cart_id_array.push(goods_id+'@5');
							refreshUrl(cart_id_array.join(","));
						}	
						flag = true;
					}else{	
					    flag = false;
						alert(result.datas.error);				   
					}
				}
		 });
		 if(ifcart && flag == true){
            $.ajax({
				url:ApiUrl+"/index.php?act=member_cart&op=cart_add",
				type:'post',
				data:{goods_id:goods_id,quantity:1,goods_type:5,key:key},
				dataType:'json',
				async: false,
				success:function(result){
					if(result.code == 200){
						var cart_info = result.datas.cart_list[0];
						cart_id_array.push(cart_info.cart_id+"@5");
						refreshUrl(cart_id_array.join(","));
					}else{	
						alert(result.datas.error);				   
					}
				}
		     });
		 }
   });
   function refreshUrl(goods_id){
       if(!goods_id){
	       return false;
	   }	    
	   if(!ifcart){
		  window.location.href =  WapSiteUrl +"/tmpl/order/buy_step1.html?goods_id="+goods_id;
	   }else{
	      window.location.href =  WapSiteUrl +"/tmpl/order/buy_step1.html?ifcart=1&cart_id="+goods_id;
	   }
   }
   
   
   
});


