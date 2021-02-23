$(function (){
    if (getQueryString('key') != '') {
        var key = getQueryString('key');
        var username = getQueryString('username');
        addCookie('key', key);
        addCookie('username', username);
    } else {
        var key = getCookie('key');
		var username = getCookie('username');
    }
    
	username = username?username:'您';

    if($("#header-container")){		
        var navHtml = '<div class="logo"><a href="'+WapSiteUrl+'/index.html"><img src="'+WapSiteUrl+'/images/logo.png"/></a></div><div class="head-nav">'
        navHtml+='<a href="'+WapSiteUrl+'/tmpl/zhuanti/tab4c.html"><i class="ic ic-read"></i>珠宝课堂</a>';
        if(key){
			navHtml +='<a  href="javascript:void(0)" key="'+key+'" id="logout_top"><i class="ic ic-loginout"></i>'+username+'</a>';		
        } else {
           navHtml +='<a href="'+WapSiteUrl+'/login.html"><i class="ic ic-login"></i>登录</a>';
        }
        navHtml+= '<a href="'+WapSiteUrl+'/tmpl/cart_list.html"><i class="ic ic-cart"></i>购物车</a>';
        $("#header-container").html(navHtml);       


	}
    if($("#menuBtn")) {
	 //左侧菜单
		var menuHtml = ''+
		'<link rel="stylesheet" href="'+WapSiteUrl+'/css/left_menu.css?1">'+
		'<link rel="stylesheet" href="'+WapSiteUrl+'/js/chosen/css/chosen.css">'+				
		'<script type="text/javascript" src="'+WapSiteUrl+'/js/chosen/js/chosen.jquery.js"><\/script>'+
		'<div class="menu">'+
			'<div class="menuWap">'+
				'<a href="javascript:;" class="mclose"><span><i class="icon icon-close3"></i></span></a>'+
				'<ul class="menuList">'+
					'<li class="disabled home"><a href="'+WapSiteUrl+'/index.html"><i class="ic ic-home"></i>首页</a></li>'+
					'<li class="disabled"><a href="'+WapSiteUrl+'/customized.html"><i class="ic ic-dzzj"></i>定制钻戒</a></li>'+
					'<li class=""><a href="'+WapSiteUrl+'/tmpl/diamond.html"><i class="i">&middot;</i>挑选钻石</a></li>'+
					'<li class=""><a href="'+WapSiteUrl+'/tmpl/jietuo_list.html"><i class="i">&middot;</i>挑选戒托</a></li>'+
					'<li class=""><a href="'+WapSiteUrl+'/tmpl/zhuanti/xy.html"><i class="i">&middot;</i>星耀钻石</a></li>'+
					'<li class="disabled"><a href="'+WapSiteUrl+'/tmpl/marry_index.html"><i class="ic ic-jhzj"></i>结婚钻戒</a></li>'+
					'<li class=""><a href="'+WapSiteUrl+'/tmpl/marry_list.html"><i class="i">&middot;</i>搜索</a></li>'+
					'<li class=""><a href="'+WapSiteUrl+'/tmpl/zhuanti/xxbl.html"><i class="i">&middot;</i>香邂巴黎</a></li>'+
					'<li class=""><a href="'+WapSiteUrl+'/tmpl/zhuanti/xzw.html"><i class="i">&middot;</i>心之吻</a></li>'+
					'<li class="disabled"><a href="'+WapSiteUrl+'/tmpl/couple_index.html"><i class="ic ic-dj"></i>对戒</a></li>'+
					'<li class=""><a href="'+WapSiteUrl+'/tmpl/couple_list.html"><i class="i">&middot;</i>搜索</a></li>'+
					'<li class=""><a href="'+WapSiteUrl+'/tmpl/zhuanti/tsyd.html"><i class="i">&middot;</i>天生一对</a></li>'+
					'<li class=""><a href="'+WapSiteUrl+'/tmpl/zhuanti/tszy.html"><i class="i">&middot;</i>天使之翼</a></li>'+
					'<li class="disabled"><a href="'+WapSiteUrl+'/tmpl/zhuanti/ppgs.html"><i class="ic ic-ppgs"></i>品牌故事</a></li>'+
					'<li class=""><a href="'+WapSiteUrl+'/tmpl/zhuanti/ppgs.html"><i class="i">&middot;</i>品牌文化</a></li>'+
					'<li class=""><a href="'+WapSiteUrl+'/tmpl/zhuanti/ppgs.html"><i class="i">&middot;</i>品牌动态</a></li>'+
					'<li class="disabled"><a href="javascript:;"><i class="ic ic-zbkt"></i>珠宝课堂</a></li>'+
					'<li class=""><a href="'+WapSiteUrl+'/tmpl/zhuanti/tab4c.html"><i class="i">&middot;</i>挑选钻石</a></li>'+
					'<li class=""><a href="'+WapSiteUrl+'/tmpl/zhuanti/tabKs.html"><i class="i">&middot;</i>挑选款式</a></li>'+
					'<li class=""><a href="'+WapSiteUrl+'/tmpl/zhuanti/tabCz.html"><i class="i">&middot;</i>确认材质</a></li>'+
					'<li class=""><a href="#"><i class="i">&middot;</i>快速导购</a></li>'+
					'<li class="disabled"><a href="javascript:;"><i class="ic ic-cwhy"></i>成为会员</a></li>'+
					'<li class=""><a href="javascript:void(0);" id="show_toggle" data-toggle="modal" data-target=".bd-example-modal-vip"><i class="i">&middot;</i>会员有礼</a></li>'+
					'<li class=""><a href="#"><i class="i">&middot;</i>终身质保</a></li>'+
					'<li class="disabled"><a href="javascript:;"><i class="ic ic-more"></i>更多</a></li>'+
					'<li class=""><a id="scan_code" href="javascript:;"><i class="i">&middot;</i>扫一扫</a></li>'+
					'<!--<li class=""><a id="do_makeorder" href="javascript:;">下单</a></li>-->'+
					'<li class=""><a href="'+WapSiteUrl+'/tmpl/cart_list.html"><i class="i">&middot;</i>购物车</a></li>'+
					'<li class=""><a href="'+WapSiteUrl+'/tmpl/member/order_list.html"><i class="i">&middot;</i>订单查询</a></li>';
				if (!window.platformId) {
					menuHtml += '<li class=""><a href="/index.php?act=seller_center"><i class="i">&middot;</i>商家中心</a></li>';
				}
				menuHtml += '<li class=""><a href="#" id="logout_left"><i class="i">&middot;</i>退出</a></li>'+
				'</ul>'+
		   ' </div>'+
		'</div>';

		menuHtml += '<div class="modal fade bd-example-modal-vip " tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="true" >'+
			'<div class="modal-dialog modal-lg dingzhi-modal" id="vip_form">'+
				'<div class="modal-content vip-content" >'+
					'<div class="modal-header">'+
					'<h5 class="modal-title">成为会员</h5>'+
					'<button type="button" class="close dialog-dingzhi-close" data-dismiss="modal" aria-label="Close">'+
						'<span aria-hidden="true">&times;</span>'+
					'</button>'+
				'</div>'+
				'<div class="modal-body vip-body">努力加载中....'+
					
				'</div>'+
				'<div class="modal-footer">'+
					'<button type="button" class="btn quxiao" data-dismiss="modal" >取消</button>'+
					'<button type="button" class="btn save">成为会员</button>'+
				'</div>'+
				'</div>'+
		   ' </div>'+
		'</div>';
		
		menuHtml += '<script type="text/html" id="vip-list">'+   
			'<div class="p1 row">'+
				'<input type="tel" class="vip_phone" name="vip_phone" placeholder="手机号"/>'+
				'<input type="text" class="vip_name" name="vip_name" placeholder="客户姓名"/>'+
				'<select class="khly chosen" name="vip_source_id">'+
					'<option value="">请选择</option>'+
					'<% for (var id in sourcelist) { var name = sourcelist[id]; %>'+
						'<option  value="<%= id%>"><%=name%></option>'+
					'<% } %> '+
				'</select>'+
			'</div>'+
			'<div class="p2 row">'+
				'<p class="title">客户需求</p>'+
				'<div id="vip_xuqiu">'+
					'<a href="javascript:;" class="active"; data-val="3";>婚戒</a>'+
					'<a href="javascript:;" data-val="1";>订婚钻戒</a>'+
					'<a href="javascript:;" data-val="2";>女戒</a>'+
					'<a href="javascript:;" data-val="4";>情侣戒</a>'+
				'</div>'+
			'</div>'+
			'<div class="p3 row">'+
				'<p class="title">客户预算</p>'+
				'<input type="text" id="vip_carat_min" name="vip_carat_min" value="" placeholder="￥"><em>&nbsp;&nbsp;-&nbsp;&nbsp;</em>'+
				'<input type="text" id="vip_carat_max" name="vip_carat_max" value="" placeholder="￥"><em></em>'+
			'</div>'+
		'</script>';      
		$("#menuBtn").parent().parent().parent().append(menuHtml);
	}		
	//////////////
	var vipFormId = "vip_form";
	$(document).delegate("#vip_xuqiu > a",'click',function(){
		$("#vip_xuqiu > a").removeClass("active");
		$(this).addClass("active");                         
	});
	$("#menuBtn").click(function(){					 
		$(".menu").animate({"left":"0"});
	});
	$(".menu .mclose").click(function(){
		$(".menu").animate({"left":"-15.5rem"});
	});
	$('#scan_code').click(function(){
		if (window.cordovaext) {
			window.cordovaext.scan_barcode(function(code){
				//alert(JSON.stringify(code));
				window.location.href = WapSiteUrl + "/tmpl/cart_list.html?goods_id="+code.text;
			});
		}else{
			alert("当前设备不支持扫描");	
		}
	});

	$(document).delegate("#"+vipFormId+" input[name='vip_phone']",'change',function(){
		var khphone   = $("#"+vipFormId+" input[name='vip_phone']").val();
		if(khphone != ''){
			$.ajax({
				url:ApiUrl+"/index.php?act=member_order&op=get_vip_list_by_mob",
				type:'get',
				data:{key:key,khphone:khphone},
				jsonp:'callback',
				dataType:'jsonp',
				success:function(result){
					if(result.code == 200){
						var data = result.datas;
						$("#"+vipFormId+" input[name='vip_name']").val(data.khname);
						$("#"+vipFormId+" select[name='vip_source_id']").val(data.source_id);
						$(".chosen").trigger("chosen:updated");						
					}
				}
			}); 
		}
	});
    
	$(document).delegate("#show_toggle",'click',function(){
		if(!key){
			 window.location.href = WapSiteUrl+'/login.html';			 
		}      
		$.ajax({
			url:ApiUrl+"/index.php?act=member_order&op=get_sources_list",
			type:'get',
			data:{key:key},
			jsonp:'callback',
			dataType:'jsonp',
			success:function(result){
				var data = result.datas;
				var html = template.render('vip-list', data);
				$("#"+vipFormId+" .vip-body").html(html);
				$(".chosen").chosen();
				$('.chosen-container').css('width','33%');
			}
		});
	})

	$(document).delegate(".bd-example-modal-vip .save",'click',function(){
		var key = getCookie('key');
		var khphone   = $("#"+vipFormId+" input[name='vip_phone']").val();
		if(!khphone){
			alert("请输入手机号");
			return false;
		}
		var khname    = $("#"+vipFormId+" input[name='vip_name']").val();
		if(!khname){
			alert("请输入客户姓名");
			return false;
		}
		var source_id = $("#"+vipFormId+" select[name='vip_source_id']").val();
		//if(!source_id){
		//    alert("请选择客户来源");
		//    return false;
		//}
		var carat_min = $("#"+vipFormId+" input[name='vip_carat_min']").val();
		var carat_max = $("#"+vipFormId+" input[name='vip_carat_max']").val();
		var xuqiu = "";
		$("#vip_xuqiu > a").each(function(){
			if($(this).hasClass('active'))
			   xuqiu = $(this).attr('data-val');
		})
		$.ajax({
			url:ApiUrl+"/index.php?act=member_order&op=save_vip_info",
			type:'get',
			data:{key:key, khphone:khphone, khname:khname, source_id:source_id, xuqiu:xuqiu, carat_min:carat_min, carat_max:carat_max},
			jsonp:'callback',
			dataType:'jsonp',
			success:function(result){
				var data = result.datas;
				if(result.code == 200){
					sessionStorageAdd("vip_info", data, true);
					alert("添加成功！");
					$(".dialog-dingzhi-close").click(); 
				}else{
					alert(data.error);
					return false;
				}
			}
		});
	});	
	
	$(document).delegate("#logout_top",'click',function(){
		var key = getCookie('key');
		if (key) {
			if(window.confirm('您确认要退出登录吗？')){
				logout();
			}
		}										 
	});
	$(document).delegate("#logout_left",'click',function(){
		 var key = getCookie('key');
		 if (key) {
			logout();
		}else{
			window.location.href = loginCfg.login;
		}								 
	});
	//////////
	 
	 
 });

$(document).ready(function(e) {
    var refer = document.referrer; 
	var docName = getHtmlDocName();
	if (window.history && window.history.pushState) {
		$(window).on('popstate', function () {
			if(refer){
				  window.history.pushState('forward', null, '');
			      window.history.forward(1);  
			      var url = refer; 
			      if(RegExp(/\.html$/).test(refer)){
				      url = refer+'?history='+docName;
				  }else if(RegExp(/history=.*?$/i).test(refer)){
					  url = refer.replace(/history=.*$/i,'history='+docName);
				  }else{
					  url = refer+"&history="+docName;
				  }					  
				  location.href = url;			
			}	
		
		});
	}
	if(refer){
	  window.history.pushState('forward', null, ''); //在IE中必须得有这两行
	  window.history.forward(1);
	}
});