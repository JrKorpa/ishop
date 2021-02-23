var order_id = getQueryString('order_id');
var order_sn="";
$(function(){
    var key = getCookie('key');
    if(!key){
        location.href = '../../login.html';
    }
    var cert_id = getQueryString('cert_id');
    var goods_sn = getQueryString('goods_sn');
    var style_sn = getQueryString('style_sn');
    var is_cpdz = 0;
    var tuo_type = 2;
    var shape = 1;//圆形
    var is_dingzhi;
    var xiangqian;
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
    var others = "";
    var style_goods_exists = false;
    var diamond_exists = false;
    $(document).delegate("#add_address","click",function(){
        var order_id = $(this).attr("order_id");
        var address_info = sessionStorageGet("order_adr");
        address_info['order_id'] = order_id; 
        console.log(address_info);
        var html = template.render('address-info',address_info); 
        var provice_id = 0;
        var city_id = 0;
        var area_id = 0;
        if(address_info){
           provice_id = address_info.provice_id;
           city_id = address_info.city_id;
           area_id = address_info.area_id;
        }
        $("#address_info").html(html);  
        initProvice(provice_id);
        initCity(provice_id,city_id);
        initArea(city_id,area_id);
        
        $("#provice_id").change(function(){
           var provice_id = $(this).val()?$(this).val():999999;
           initCity(provice_id,city_id);
        });
        $("#city_id").change(function(){
           var city_id = $(this).val()?$(this).val():999999;
           initArea(city_id,area_id);
        });
    });

    $(document).delegate(".jt_kezi2 em","click",function(){
        var this_=$(this).attr("data-val");
        var ipt=$("#kezi").val();
        $("#kezi").val(ipt+this_); 
        $("#kezi_ipt").val(ipt+this_);  
    });

    //var goodsDiyLoad = false;
    $(document).delegate("#goodsDiy","click",function(){
        //只加载一次                       
        //if(goodsDiyLoad == true ){
            //return true;
        //}
        var rec_id = $(this).attr("goods_list_id");
        if(!rec_id) alert("rec_id不能为空！");
        var style_sn = $(this).attr("style_sn");
        if(!style_sn) alert("style_sn不能为空！");
        var key = getCookie('key');
        $.ajax({
            url:ApiUrl+"/index.php?act=member_order&op=get_order_goods_list",
            type:'post',
            data:{key:key,rec_id:rec_id,style_sn:style_sn},
            dataType:'json',
            async: true,
            success:function(result){
                if(result){
                    var html = template.render('dingzhi-form',result.datas);
                    $("#dingzhi_form").html(html);
					initKeziEvent("dingzhi_form");
                    //goodsDiyLoad = true;                        
                }else{  
                    alert(result.datas.error);                 
                }
            }
         });
        
    });

    var gift_list_load = false; 
    $(document).delegate('#add_gift', 'click', function(event) {
       if(gift_list_load){
           $("#gift_form select[name='goods_id']").val('');
           $("#gift_info").html('');
           return true;
       }
       var key = getCookie('key');
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
    });
    
    $(document).delegate('#gift_form .save', 'click', function(event) {
       var formId = "gift_form";
       var goods_id = $("#"+formId+" select[name='goods_id']").val();
       if(!goods_id){
          alert("请选择赠品");
          return false;
       }
       var style_sn  = $("#"+formId+" input[name='style_sn']").val();
       var shoucun  = $("#"+formId+" input[name='shoucun']").val();
       var goods_name  = $("#"+formId+" input[name='goods_name']").val();
       var goods_price  = $("#"+formId+" input[name='goods_price']").val();
	   var is_xz  = $("#"+formId+" input[name='is_xz']").val();
	   var is_finance = (is_xz==2)?1:0;
       var flag = false;
       var goods_id = "";
       var key = getCookie('key');
       $.ajax({
            url:ApiUrl+"/index.php?act=member_order&op=gift_add",
            type:'post',
            data:{order_id:order_id,style_sn:style_sn,goods_name:goods_name,shoucun:shoucun,is_finance:is_finance,goods_price:goods_price,key:key},
            dataType:'json',
            async: false,
            success:function(result){
                if(result.code == 200){
                    window.location.reload();                                 
                }else{
                    alert(result.datas.error);                 
                }
            }
        });
    });

    $(document).delegate('#log_form .save', 'click', function(event) {
       var log_text = $("#log").val();
       if(!log_text){
          alert("请填写订单日志");
          return false;
       }
       var key = getCookie('key');
       $.ajax({
            url:ApiUrl+"/index.php?act=member_order&op=add_order_log",
            type:'post',
            data:{order_id:order_id,key:key,log_text:log_text},
            dataType:'json',
            async: false,
            success:function(result){
                if(result.code == 200){
                    window.location.reload();                                 
                }else{
                    alert(result.datas.error);
                }
            }
         });
    });

    $(document).delegate('#address_save', 'click', function(event) {
        var address_info = {};
        address_info['order_id'] = $("#order_id").val();
        address_info['chain_id'] = $("#chain_id").val();
        address_info['provice_id'] = $("#provice_id").val();
        address_info['city_id'] = $("#city_id").val();
        address_info['area_id'] = $("#area_id").val();
        
        address_info['provice'] = jQuery("#provice_id").find("option:selected").attr("name");
        address_info['city'] = jQuery("#city_id").find("option:selected").attr("name");
        address_info['area'] = jQuery("#area_id").find("option:selected").attr("name");
        
        
        address_info['true_name'] = $("#true_name").val();
        address_info['mob_phone'] = $("#mob_phone").val();
        address_info['tel_phone'] = $("#tel_phone").val();
        address_info['address'] = $("#address").val();  
        address_info['chain_id'] = $("#chain_id").val();
        var key = getCookie('key');
        address_info['key'] = key;
        if(address_info['true_name'] == ""){
            alert("收货人不能为空！");return;
        }
        if(address_info['mob_phone'] == ""){
            alert("手机号码不能为空！");return;
        }
        /*if(address_info['provice_id'] == ""){
            alert("省份不能为空！");return;
        }
        if(address_info['city_id'] == ""){
            alert("城市不能为空！");return;
        }
        if(address_info['area_id'] == ""){
            alert("地区不能为空！");return;
        }*/
        if(address_info['provice'] == undefined){
            address_info['provice'] = "";
        }
        if(address_info['city'] == undefined){
            address_info['city'] = "";
        }
        if(address_info['area'] == undefined){
            address_info['area'] = "";
        }
        if(address_info['address'] == undefined){
            address_info['address'] = "";
        }
        address_info['area_info'] = address_info['provice']+' '+address_info['city']+' '+address_info['area'];
        change_address(address_info);
        $(".dialog-address-close").click(); 
    });
    $(document).delegate('.edit_goods_price', 'click', function(event) {
		 var formId = "discount_form";	
		 var id =  $(this).attr('data-id');
		 var goods_price = $(this).attr('data-goods-price');	
		 var discount_code = $(this).attr('data-discount-code');
		 $("#"+formId+" input[name='rec_id']").val(id);
		 $("#"+formId+" input[name='goods_hand_price']").val(goods_price);
		 $("#"+formId+" input[name='discount_code']").val(discount_code);								
								
	});
    $(document).delegate('#confirmDiy', 'click', function(event) {
        //tuo_type = $("#tuo_type").val();
        is_dingzhi = $("#is_dingzhi").val();
        xiangqian = $("#xiangqian").val();
        cert_id = $("#cert_id").val();
        var rec_id = $("#rec_id").val();
        //carat  = $("#carat").val();
        //zhushi_num = $("#zhushi_num").val();
        //cert = $("#cert").val();
        //cert_id = $("#cert_id").val();
        //color = $("#color").val();
        //clarity = $("#clarity").val();
        //cut = $("#cut").val();
        facework = $("#facework").val();     
        if(!facework){
            alert("请选择表面工艺");return;
        }   
        kezi = $("#kezi").val()?$("#kezi").val():'';
        //goods_price = $("#goods_price").attr("data-val");
        //cpdz_code = $("#cpdz_code").val();
        goods_type = 1;//戒托
        //is_cpdz = is_dingzhi==1 && tuo_type==1?1:0;
        $('input[name="others"]:checked').each(function(){
           others += $(this).val()+',';
        });
        var key = getCookie('key');
        var postData = {
            key:key,is_dingzhi:is_dingzhi,xiangqian:xiangqian,cert_id:cert_id,facework:facework,
            kezi:kezi,goods_type:goods_type,rec_id:rec_id
        };
        //console.log(postData);
        //cert_id = cert_id;
        $.ajax({
                url:ApiUrl+"/index.php?act=member_order&op=goods_update",
                type:'post',
                data:postData,
                dataType:'json',
                async: true,
                success:function(result){
                    if(result.code ==200){  
                        window.location.reload();
                        //var data = result.datas;
                        //goods_price = data.goods_price;
                        //$("#goods_price").attr("data-val",goods_price).html("￥"+goods_price);
                        //initDiamondInfo(data);
                        $(".dialog-dingzhi-close").click();             
                    }else{  
                        $.sDialog({
                            skin:"red",
                            content:result.datas.error,
                            okBtn:false,
                            cancelBtn:false
                        });                 
                    }
                }
         });        

    });
   $("#discount_form .save").click(function(){
		var formId = "discount_form";									 
		var rec_id = $("#"+formId+" input[name='rec_id']").val();
		var goods_hand_price = $("#"+formId+" input[name='goods_hand_price']").val();
		var discount_code = $("#"+formId+" input[name='discount_code']").val();
		$.ajax({
			url:ApiUrl+"/index.php?act=member_order&op=edit_goods_discount",
			type:'post',
			data:{rec_id:rec_id,goods_hand_price:goods_hand_price,discount_code:discount_code,key:key},
			dataType:'json',
			async: true,
			success:function(result){
				if(result.code ==200){					
					window.location.reload(); 
					$(".dialog-dingzhi-close").click();
				}else{
				   alert(result.datas.error);
				}
			}
		 });		 
									  
	});
    template.helper('in_array', function(o,arr){
        //var boole = $.inArray(o, arr);
        for (var i = 0; i < arr.length; i++) {
            if(arr[i] == o){
                return true;
            }
        };
        return false;
    });
    
    var key = getCookie('key');
    $.getJSON(ApiUrl + '/index.php?act=member_order&op=order_info',{key:key,order_id:getQueryString('order_id')}, function(result) {
    	result.datas.order_info.WapSiteUrl = WapSiteUrl;
    	$('#order-info-container').html(template.render('order-info-tmpl',result.datas.order_info));
        var button_template = template.render('button-template',result.datas.order_info);
        $("#button-container").html(button_template);

        var log1_template = template.render('log1-template',{'order_log_list':result.datas.order_info.order_log_list});
		console.log(log1_template);
        $("#log1-container").html(log1_template);

        var log2_template = template.render('log2-template',{'product_log_list':result.datas.order_info.product_log_list});
        $("#log2-container").html(log2_template);

        order_sn=result.datas.order_info.order_sn;
        sessionStorageAdd("order_adr",result.datas.order_info.reciver_info, true);
        // 编辑商品
        //$('.goods-edit').click(goodsEdit);
        // 取消
        $(".cancel-order").click(cancelOrder);
        // 删除
        $(".goods-del").click(deleteGoods);
        // 全部退款
        $('.all_refund_order').click(allRefundOrder);
        // 部分退款
        $('.goods-refund').click(goodsRefund);
        // 退货
        $('.goods-return').click(goodsReturn);
        // 确认订单
        $('.check-order').click(checkOrder);
        //布产
        $('.order-bc').click(orderBc);
        //console.log(result.datas.order_info.order_log_page_count);
        //console.log(result.datas.order_info.product_log_page_count);
        pagenav(1, result.datas.order_info.order_log_page_count);
        pagenav2(1, result.datas.order_info.product_log_page_count);
        // 收货
        /*$(".sure-order").click(sureOrder);
        // 评价
        $(".evaluation-order").click(evaluationOrder);
        // 追评
        $('.evaluation-again-order').click(evaluationAgainOrder);
        // 查看物流
        $('.viewdelivery-order').click(viewOrderDelivery);*/
        /*$.ajax({
            type: 'post',
            url: ApiUrl + "/index.php?act=member_order&op=get_current_deliver",
            data:{key:key,order_id:getQueryString("order_id")},
            dataType:'json',
            success:function(result) {
                //检测是否登录了
                checkLogin(result.login);

                var data = result && result.datas;
                if (data.deliver_info) {
                    $("#delivery_content").html(data.deliver_info.context);
                    $("#delivery_time").html(data.deliver_info.time);               	
                }
            }
        });*/

        $("#gift_form select[name='goods_id']").change(function(){
               var goods_id = $(this).val();
               $("#gift_info").html("");
               if(!goods_id){
                   return false;
               }
               var key = getCookie('key');
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
                                $.sDialog({
                                    skin:"red",
                                    content:result.datas.error,
                                    okBtn:false,
                                    cancelBtn:false
                                });                 
                            }
                        }
                 });
           });
    });
});


//取消订单
function cancelOrder(){
    var order_id = $(this).attr("order_id");
    //cancelOrderId(order_id);
    $.sDialog({
        content: '确定取消订单？',
        okFn: function() { cancelOrderId(order_id); }
    });
}

function cancelOrderId(order_id) {
    var key = getCookie('key');
    $.ajax({
        type:"post",
        url:ApiUrl+"/index.php?act=member_order&op=order_cancel",
        data:{order_id:order_id,key:key},
        dataType:"json",
        success:function(result){
            if(result.datas && result.datas == 1){
                window.location.reload();
            } else {
                $.sDialog({
                    skin:"red",
                    content:result.datas.error,
                    okBtn:false,
                    cancelBtn:false
                });
                //alert(result.datas.error);
            }
        }
    });
}

//删除订单
function deleteGoods(){
    var order_id = $(this).attr("order_id");
    var rec_id = $(this).attr("goods_list_id");
    //deleteGoodsId(order_id, rec_id);
    $.sDialog({
        content: '确定删除？',
        okFn: function() { deleteGoodsId(order_id, rec_id); }
    });
}
function deleteGoodsId(order_id, rec_id) {
    var key = getCookie('key');
    $.ajax({
        type:"post",
        url:ApiUrl+"/index.php?act=member_order&op=goods_delete",
        data:{order_id:order_id,rec_id:rec_id,key:key},
        dataType:"json",
        success:function(result){
            if(result.datas && result.datas == 1){
                window.location.reload();
            } else {
                $.sDialog({
                    skin:"red",
                    content:result.datas.error,
                    okBtn:false,
                    cancelBtn:false
                });
                //alert(result.datas.error);
            }
        }
    });
}

//确认订单
function checkOrder(){
    var order_id = $(this).attr("order_id");
    //checkOrderId(order_id);
    $.sDialog({
        content: '确定审核订单通过？',
        okFn: function() { checkOrderId(order_id); }
    });
}


//确认订单
function orderBc(){
    var order_id = $(this).attr("order_id");
    $.sDialog({
        content: '确定布产当前订单吗？',
        okFn: function() { execOrderBc(order_id); }
    });
}


function execOrderBc(order_id) {
    var key = getCookie('key');
    $.ajax({
        type:"post",
        url:ApiUrl+"/index.php?act=member_order&op=order_bc",
        data:{order_id:order_id,key:key},
        dataType:"json",
        success:function(result){
            if(result.datas && result.datas == 1){
                window.location.reload();
            } else {
                $.sDialog({
                    skin:"red",
                    content:result.datas.error,
                    okBtn:false,
                    cancelBtn:false
                });
            }
        }
    });
}


function checkOrderId(order_id) {
    var key = getCookie('key');
    $.ajax({
        type:"post",
        url:ApiUrl+"/index.php?act=member_order&op=order_check",
        data:{order_id:order_id,key:key},
        dataType:"json",
        success:function(result){
            if(result.datas && result.datas == 1){
                window.location.reload();
            } else {
                $.sDialog({
                    skin:"red",
                    content:result.datas.error,
                    okBtn:false,
                    cancelBtn:false
                });
                //alert(result.datas.error);
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

// 编辑商品
function goodsEdit() {
    var orderId = $(this).attr('order_id');
    var orderGoodsId = $(this).attr('goods_list_id');
    location.href = WapSiteUrl + '/tmpl/member/refund.html?order_id=' + orderId +'&order_goods_id=' + orderGoodsId;
}

// 全部退款
function allRefundOrder() {
    var orderId = $(this).attr('order_id');
    location.href = WapSiteUrl + '/tmpl/member/refund_all.html?order_id=' + orderId;
}
// 退款
function goodsRefund() {
    var orderId = $(this).attr('order_id');
    var orderGoodsId = $(this).attr('goods_list_id');
    location.href = WapSiteUrl + '/tmpl/member/refund.html?order_id=' + orderId +'&order_goods_id=' + orderGoodsId;
}
// 退货
function goodsReturn() {
    var orderId = $(this).attr('order_id');
    var orderGoodsId = $(this).attr('goods_list_id');
    location.href = WapSiteUrl + '/tmpl/member/return.html?order_id=' + orderId +'&order_goods_id=' + orderGoodsId;
}
// 评价
/*function evaluationOrder() {
    var orderId = $(this).attr('order_id');
    location.href = WapSiteUrl + '/tmpl/member/member_evaluation.html?order_id=' + orderId;
    
}
// 追加评价
function evaluationAgainOrder() {
    var orderId = $(this).attr('order_id');
    location.href = WapSiteUrl + '/tmpl/member/member_evaluation_again.html?order_id=' + orderId;
}
// 全部退款
function allRefundOrder() {
    var orderId = $(this).attr('order_id');
    location.href = WapSiteUrl + '/tmpl/member/refund_all.html?order_id=' + orderId;
}
// 查看物流
function viewOrderDelivery() {
    var orderId = $(this).attr('order_id');
    location.href = WapSiteUrl + '/tmpl/member/order_delivery.html?order_id=' + orderId;
}
// 退款
function goodsRefund() {
    var orderId = $(this).attr('order_id');
    var orderGoodsId = $(this).attr('order_goods_id');
    location.href = WapSiteUrl + '/tmpl/member/refund.html?order_id=' + orderId +'&order_goods_id=' + orderGoodsId;
}
// 退货
function goodsReturn() {
    var orderId = $(this).attr('order_id');
    var orderGoodsId = $(this).attr('order_goods_id');
    location.href = WapSiteUrl + '/tmpl/member/return.html?order_id=' + orderId +'&order_goods_id=' + orderGoodsId;
}*/

function change_address(_address_info){
    address_info = _address_info;
    sessionStorageAdd("order_adr",address_info, true);
    initAddressList();
    //_init(address_info);
    //保存最新订单地址
    editAddress(address_info);
    
}

function initAddressList(){
   
   $("#add_address").click(function(){
        var address_info = sessionStorageGet("order_adr");
        var html = template.render('address-info',address_info); 
        var provice_id = 0;
        var city_id = 0;
        var area_id = 0;
        if(address_info){
           provice_id = address_info.provice_id;
           city_id = address_info.city_id;
           area_id = address_info.area_id;
        }
        $("#address_info").html(html);  
        initProvice(provice_id);
        initCity(provice_id,city_id);
        initArea(city_id,area_id);
        
        $("#provice_id").change(function(){
           var provice_id = $(this).val()?$(this).val():999999;
           initCity(provice_id,city_id);
        });
        $("#city_id").change(function(){
           var city_id = $(this).val()?$(this).val():999999;
           initArea(city_id,area_id);
        });
    });
}

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

//保存收获地址
function editAddress (info) {
    $.ajax({
        url:ApiUrl+"/index.php?act=member_order&op=editaddress",
        type:'post',
        data:info,
        dataType:'json',
        success:function(result){
            if(result.code ==200){
                $(".dialog-dingzhi-close").click();   
                window.location.reload();          
            }else{  
                alert(result.datas.error);                 
            }
       }
    });
}
//收货地址 end


//日志分页start
function search_log(click_page){
    var key = getCookie('key');
    $.ajax({
        type: 'post',
        url: ApiUrl + "/index.php?act=member_order&op=get_order_log_list",
        data:{key:key,order_id:order_id,page:click_page,page_size:8},
        dataType:'json',
        success:function(result) {
            var data = result && result.datas;
            if (data) {
                console.log(data);
                var log1_template = template.render('log1-template',data);
                $("#log1-container").html(log1_template);
            }
        }
    });
}

function search_product_log(click_page){
    var key = getCookie('key');
    $.ajax({
        type: 'post',
        url: ApiUrl + "/index.php?act=member_order&op=get_order_product_log_list",
        data:{key:key,order_sn:order_sn,page:click_page,page_size:8},
        dataType:'json',
        success:function(result) {
            var data = result && result.datas;
            if (data) {
                console.log(data);
                var log2_template = template.render('log2-template',data);
                $("#log2-container").html(log2_template);
            }
        }
    });
}

function pagenav(page, pageCount) {
    var pageNavObj = null;
    jQuery(document).ready(function($) {
        pageNavObj = new PageNavCreate("PageNavId1",{
            pageCount:pageCount,//总页数
            currentPage:page,//当前页
            perPageNum:5,//每页按钮数
        });
        pageNavObj.afterClick(pageNavCallBack);
    });
}

//翻页按钮点击后触发的回调函数
function pageNavCallBack(clickPage){
    //clickPage是被点击的目标页码
    //一般来说可以在这里通过clickPage,执行AJAX请求取数来重写页面
    search_log(clickPage);
    //最后别忘了更新一遍翻页导航栏
    //pageNavCreate("PageNav",pageCount,clickPage,pageNavCallBack);
    pageNavObj = new PageNavCreate("PageNavId1",{
        pageCount:getPageSet1().pageCount,//总页数
        currentPage:clickPage,//当前页
        perPageNum:getPageSet1().perPageNum,//每页按钮数
    });
    pageNavObj.afterClick(pageNavCallBack);
}

function getPageSet1(){
    var obj = {
        pageCount:null,//总页数
        currentPage:null,//当前页
        perPageNum:null,//每页按钮数
    }
    obj.pageCount = parseInt($("#PageNavId1 .page-input-box > input").attr("placeholder"));
    obj.currentPage = 1;
    obj.perPageNum = null;
    return obj;
}


function pagenav2(page, pageCount) {
    var pageNavObj = null;
    jQuery(document).ready(function($) {
        pageNavObj = new PageNavCreate("PageNavId2",{
            pageCount:pageCount,//总页数
            currentPage:page,//当前页
            perPageNum:5,//每页按钮数
        });
        pageNavObj.afterClick(pageNavCallBack2);
    });
}

//翻页按钮点击后触发的回调函数
function pageNavCallBack2(clickPage){
    //clickPage是被点击的目标页码
    //一般来说可以在这里通过clickPage,执行AJAX请求取数来重写页面
    search_product_log(clickPage);
    //最后别忘了更新一遍翻页导航栏
    //pageNavCreate("PageNav",pageCount,clickPage,pageNavCallBack);
    pageNavObj = new PageNavCreate("PageNavId2",{
        pageCount:getPageSet2().pageCount,//总页数
        currentPage:clickPage,//当前页
        perPageNum:getPageSet2().perPageNum,//每页按钮数
    });
    pageNavObj.afterClick(pageNavCallBack2);
}


function getPageSet2(){
    var obj = {
        pageCount:null,//总页数
        currentPage:null,//当前页
        perPageNum:null,//每页按钮数
    }
    obj.pageCount = parseInt($("#PageNavId2 .page-input-box > input").attr("placeholder"));
    obj.currentPage = 1;
    obj.perPageNum = null;
    return obj;
}

//日志分页 end