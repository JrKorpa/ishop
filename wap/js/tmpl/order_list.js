var page = pagesize;
var curpage = 1;
var hasMore = true;
var footer = false;
var reset = true;
var orderKey = '';
	
$(function(){
	var key = getCookie('key');
	if(!key){
		window.location.href = WapSiteUrl+'/login.html';
	}

	if (getQueryString('data-state') != '') {
	    $('#filtrate_ul').find('li').has('a[data-state="' + getQueryString('data-state')  + '"]').addClass('selected').siblings().removeClass("selected");
	}

    $('#p3 > a').click(function(){
    //$(document).delegate("#p3 > a",'click',function(){
        $("#p3 > a").removeClass("active");
        $(this).addClass("active");                         
    });

    $('#p4 > a').click(function(){
    //$(document).delegate("",'click',function(){
        $("#p4 > a").removeClass("active");
        $(this).addClass("active");                         
    });

    $('#search_btn').click(function(){
        reset = true;
    	initPage();
    });

    $('.success').click(function(){
        reset = true;
        initPage();
        $j(".shaixuan_mask").hide();
        $j(".shaixuan").animate({right:"-550px"});
    });

    //清空搜索条件
    $('.clearq').click(function(){
        $('#search_val').val("");
        $('#goods_sn').val("");
        var is_zp = "";
        $("#p3 > a").each(function(){
            $(this).removeClass("active");
        })
        $("#p4 > a").each(function(){
            $(this).removeClass("active");
        })
        $('#settle_create_time1').val("");
        $('#settle_create_time2').val("");
        reset = true;
        initPage();
    });

    $('#fixed_nav').waypoint(function() {
        $('#fixed_nav').toggleClass('fixed');
    }, {
        offset: '50'
    });

	function initPage(){
	    if (reset) {
	        curpage = 1;
	        hasMore = true;
	    }
        $('.loading').remove();
        if (!hasMore) {
            return false;
        }
        hasMore = false;
        var params = getSearchVal();
	    var state_type = $('#filtrate_ul').find('.selected').find('a').attr('data-state');
	    var orderKey = $('#order_key').val();
        params['key'] = key;
        params['state_type'] = state_type;
        params['order_key'] = orderKey;
		$.ajax({
			type:'post',
			url:ApiUrl+"/index.php?act=member_order&op=order_list&page="+page+"&curpage="+curpage,
			data:params,
			dataType:'json',
			success:function(result){
				checkLogin(result.login);//检测是否登录了
				curpage++;
                hasMore = result.hasmore;
                if (!hasMore) {
                    get_footer();
                }
                if (result.datas.order_group_list.length <= 0) {
                    $('#footer').addClass('posa');
                } else {
                    $('#footer').removeClass('posa');
                }
				var data = result;
                var order_num = result.datas.order_num;
				data.WapSiteUrl = WapSiteUrl;//页面地址
				data.ApiUrl = ApiUrl;
				data.key = getCookie('key');
				template.helper('$getLocalTime', function (nS) {
                    var d = new Date(parseInt(nS) * 1000);
                    var s = '';
                    s += d.getFullYear() + '年';
                    s += (d.getMonth() + 1) + '月';
                    s += d.getDate() + '日 ';
                    s += d.getHours() + ':';
                    s += d.getMinutes();
                    return s;
				});
                template.helper('p2f', function(s) {
                    return (parseFloat(s) || 0).toFixed(2);
                });
                template.helper('parseInt', function(s) {
                    return parseInt(s);
                });
				var html = template.render('order-list-tmpl', data);
				if (reset) {
				    reset = false;
				    $("#order-list").html(html);
				} else {
                    $("#order-list").append(html);
                }
                $("#folat-left").html(order_num+"个订单");
			}
		});

	}
	

    // 取消
    $('#order-list').on('click','.cancel-order', cancelOrder);
    // 删除
    $('#order-list').on('click','.delete-order',deleteOrder);

    $('#order-list').on('click','.viewdelivery-order',viewOrderDelivery);

    $('#order-list').on('click', '.check-order', checkOrder);

    $('#order-list').on('click', '.order-bc', orderBc);

    $('#order-list').on('click','.check-payment',function() {
        var pay_sn = $(this).attr('data-paySn');
        toPay(pay_sn,'member_buy','pay');
        return false;
    });

    //取消订单
    function cancelOrder(){
        var order_id = $(this).attr("order_id");

        $.sDialog({
            content: '确定取消订单？',
            okFn: function() { cancelOrderId(order_id); }
        });
    }

    function cancelOrderId(order_id) {
        $.ajax({
            type:"post",
            url:ApiUrl+"/index.php?act=member_order&op=order_cancel",
            data:{order_id:order_id,key:key},
            dataType:"json",
            success:function(result){
                if(result.datas && result.datas == 1){
                    reset = true;
                    initPage();
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

    //删除订单
    function deleteOrder(){
        var order_id = $(this).attr("order_id");

        $.sDialog({
            content: '是否移除订单？<h6>电脑端订单回收站可找回订单！</h6>',
            okFn: function() { deleteOrderId(order_id); }
        });
    }

    //订单布产
    function orderBc(){
        alert(1);
        var order_id = $(this).attr("order_id");
        $.sDialog({
            content: '确定布产当前订单吗？',
            okFn: function() { execOrderBc(order_id); }
        });
    }

    function deleteOrderId(order_id) {
        $.ajax({
            type:"post",
            url:ApiUrl+"/index.php?act=member_order&op=order_delete",
            data:{order_id:order_id,key:key},
            dataType:"json",
            success:function(result){
                if(result.datas && result.datas == 1){
                    reset = true;
                    initPage();
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

    function getSearchVal () {
        var data = {};
        var search_val = $('#search_val').val();
        var goods_sn = $('#goods_sn').val();
        data['search_val'] = search_val;
        data['goods_sn'] = goods_sn;
        var is_zp = "";
        $("#p3 > a").each(function(){
            if($(this).hasClass('active'))
               is_zp = $(this).attr('data-val');
        })
        data['is_zp'] = is_zp;
        var is_xianhuo = "";
        $("#p4 > a").each(function(){
            if($(this).hasClass('active'))
               is_xianhuo = $(this).attr('data-val');
        })
        data['is_xianhuo'] = is_xianhuo;
        var starttime = $('#settle_create_time1').val();
        var endtime = $('#settle_create_time2').val();
        data['starttime'] = starttime;
        data['endtime']     = endtime;
        return data;
    }

    //确认订单
    function checkOrder(){
        var order_id = $(this).attr("order_id");

        $.sDialog({
            content: '确定审核订单通过?',
            okFn: function() { checkOrderId(order_id); }
        });
    }

    function checkOrderId(order_id) {
        $.ajax({
            type:"post",
            url:ApiUrl+"/index.php?act=member_order&op=order_check",
            data:{order_id:order_id,key:key},
            dataType:"json",
            success:function(result){
                if(result.datas && result.datas == 1){
                    reset = true;
                    initPage();
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
  
    function viewOrderDelivery() {
        var orderId = $(this).attr('order_id');
        location.href = WapSiteUrl + '/tmpl/member/order_delivery.html?order_id=' + orderId;
    }
    
    $('#filtrate_ul').find('a').click(function(){
        $('#filtrate_ul').find('li').removeClass('selected');
        $(this).parent().addClass('selected').siblings().removeClass("selected");
        reset = true;
        window.scrollTo(0,0);
        initPage();
    });

    //初始化页面
    initPage();
    $(window).scroll(function(){
        if(($(window).scrollTop() + $(window).height() > $(document).height()-1)){
            initPage();
        }
    });
});
function get_footer() {
    if (!footer) {
        footer = true;
        $.ajax({
            url: WapSiteUrl+'/js/tmpl/footer.js',
            dataType: "script"
          });
    }
}