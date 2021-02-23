$(function(){
	var key = getCookie('key');
    if(!key){
        window.location.href = WapSiteUrl+'/login.html';
    }	
	var goods_sn = getQueryString('goods_sn');
	var cert_id  = getQueryString('cert_id');
	var style_sn = getQueryString('style_sn');
	var carat = getQueryString('carat');
	var history = getQueryString('history');
	var is_xianhuo = false;
	if(!isNaN(parseInt(goods_sn)) && goods_sn>0){
		is_xianhuo = true;	
	}else if(goods_sn){
		var arr = goods_sn.split("-");
		style_sn = style_sn?style_sn:arr[0];
	}
    $(".nav-sel-dia").click(function(){
		if(cert_id){
			if(is_xianhuo){
				sessionStorageAdd("jietuo_cert_id",false,true); 
	            window.location.href = WapSiteUrl+'/tmpl/diamond_detail.html?cert_id='+cert_id+"&goods_sn="+goods_sn+"&style_sn="+style_sn;
			}else if(goods_sn){
				sessionStorageAdd("jietuo_cert_id",false,true); 
	            window.location.href = WapSiteUrl+'/tmpl/diamond_detail.html?cert_id='+cert_id+"&goods_sn="+goods_sn;	
			}else{
				window.location.href = WapSiteUrl+'/tmpl/diamond_detail.html?cert_id='+cert_id;
			}
		}else{
			if(is_xianhuo){
				sessionStorageAdd("jietuo_cert_id",false,true); 
	            window.location.href = WapSiteUrl+'/tmpl/diamond.html?cert_id='+cert_id+"&goods_sn="+goods_sn+"&style_sn="+style_sn+"&carat="+carat;
			}else if(goods_sn){
				 window.location.href = WapSiteUrl+'/tmpl/diamond.html?goods_sn='+goods_sn;
			}else{
				 window.location.href = WapSiteUrl+'/tmpl/diamond.html';
			}
		}
	});
	$(".nav-sel-jietuo").click(function(){
		if(is_xianhuo){								
	        window.location.href = WapSiteUrl+'/tmpl/xianhuo_detail.html?goods_sn='+goods_sn+"&style_sn="+style_sn+"&cert_id="+cert_id;	
		}else if(goods_sn){
			window.location.href = WapSiteUrl+'/tmpl/jietuo_detail.html?goods_sn='+goods_sn+"&cert_id="+cert_id;	
		}else{
			window.location.href = WapSiteUrl+'/tmpl/jietuo_list.html?cert_id='+cert_id;
		}
	});
   function restoreSearchVal(params)	{
	   for( var p in params ){							
			if(!params[p] || (cert_id && $.inArray(p,['shape','carat_min','carat_max']) !=-1)){
				continue;  
			}			
			var valStr = params[p].toString();							
			if($.inArray(p,['order_by'])!=-1){
				var valArr = valStr.split("|");
				$("#"+p+" > button").each(function(){
				   if($(this).attr('data-val')==valArr[0]){
					  $(this).addClass('active');
				   }
				   if(valArr[1]==1){
					   $(this).find('i').removeClass("icon-arrowdown").addClass('icon-arrowtop');
				   }else{
					   $(this).find('i').removeClass('icon-arrowtop').addClass('icon-arrowdown');
				   }
				});
			}else if($.inArray(p,['price_min','price_max','goods_name'])!=-1){
				$("#"+p).val(valStr);
			}else if($.inArray(p,['search_type'])!=-1){
				$("#"+p+" > .nav-item >.nav-link").each(function(){
				   if($(this).attr('data-val')==valStr){
					  $(this).addClass('active');
				   }else{
					  $(this).removeClass('active'); 
				   }
				});
			}else{
				var valArr = valStr.split(",");
				$("#"+p+" > button").each(function(){
					  var thisVal = $(this).attr('data-val');		
					  if(thisVal && $.inArray(thisVal,valArr)!=-1){
						  $(this).addClass('active');
					  }
				 });
			}
		}
   }
   function getSearchVal (argument) {
		var data = {};
		data['key'] = key;
		data['cert_id'] = cert_id;
		var xiangkou=[];//镶口
		$("#xiangkou button").each(function(){
			if($(this).hasClass('active') && $(this).attr('data-val')>0)
				xiangkou.push($(this).attr('data-val'));
		})
		data['xiangkou'] = xiangkou;
		
		var shape=[];//形状
		$("#shape button").each(function(){
			if($(this).hasClass('active'))
				shape.push($(this).attr('data-val'));
		})
		data['shape'] = shape;
	
		var caizhi=[];//材质
		$("#caizhi button").each(function(){
			if($(this).hasClass('active'))
				caizhi.push($(this).attr('data-val'));
		})
		data['caizhi'] = caizhi;
		
		var cat_type=[];//款式分类
		$("#cat_type button").each(function(){
			if($(this).hasClass('active'))
				cat_type.push($(this).attr('data-val'));
		})
		data['cat_type'] = cat_type;
		
	    var is_xianhuo ;//事都现货
		$("#is_xianhuo button").each(function(){
			if($(this).hasClass('active'))
				is_xianhuo = $(this).attr('data-val');
		})
		data['is_xianhuo'] = is_xianhuo;
		
		
		var xilie=[];//系列
		$("#xilie button").each(function(){
			if($(this).hasClass('active'))
				xilie.push($(this).attr('data-val'));
		})
		data['xilie'] = xilie;
		
		data['price_min'] = $("#price_min").val();
		data['price_max'] = $("#price_max").val();
		data['carat_min'] = $("#carat_min").val();
		data['carat_max'] = $("#carat_max").val();
		data['goods_name'] = $("#goods_name").val();

		$("#order_by button").each(function(){
			if($(this).hasClass('active')){
				data['order_by'] = $(this).attr('data-val')+'|'+$(this).find("i").attr('data-val');
			}
		});
		
		$("#search_type >.nav-item >.nav-link").each(function(obj,j){
			 var search_type = $(this).attr('data-val');
			 if($(this).hasClass("active")){					      
				 data['search_type'] = search_type;
				 if(search_type=="contrast"){
					 if(is_xianhuo==1){
						 data['contrast_list'] =  sessionStorageGet("xianhuoContrastList");
					 }else{
					     data['contrast_list'] =  sessionStorageGet("jietuoContrastList"); 
					 }
				 }
			 }
		});
		return data;
	}
    $.ajax({
        url:ApiUrl+"/index.php?act=style_goods&op=style_goods_index",
        type:'post',
        data:{cert_id:cert_id,goods_sn:goods_sn},
        dataType:'json',
        success:function(result){
			if(result.code ==200){
				var html = template.render('style-goods-search',result.datas);
				$("#search-content").html(html);
				$(".btn-group button.checkbox").click(function(){
					if( $(this).hasClass("active")){
						$(this).removeClass("active");
					}else{
						$(this).addClass("active");
					}
					doSearch(1);
				});	
				$(".btn-group button.radio").click(function(){
					$(".btn-group button.radio").removeClass("active");										
					$(this).addClass("active");
					doSearch(1);
				});	
				$("#order_by button").click(function(){
					$("#order_by button").removeClass("active");
					$(this).addClass("active");
					if($(this).find("i").hasClass("icon-arrowdown")){
						$(this).find("i").removeClass("icon-arrowdown");
						$(this).find("i").addClass("icon-arrowtop");
						$(this).find("i").attr("data-val",1);
					}else if($(this).find("i").hasClass("icon-arrowtop")){
						$(this).find("i").removeClass("icon-arrowtop");
						$(this).find("i").addClass("icon-arrowdown");
						$(this).find("i").attr("data-val",2);
					}
					doSearch(1);
				});	
				$("#price input,#carat input").blur(function(){
				   doSearch(1);	
				});
				$("#search_type >.nav-item >.nav-link").click(function(){
                    $("#search_type >.nav-item >.nav-link").removeClass("active");
                    $(this).addClass("active");
                    doSearch(1);
                });
				var params = sessionStorage.getItem('jt_list_params');	
				
				if (history && params) {
					params = JSON.parse(params);
					restoreSearchVal(params);
					doSearch(params['curpage'], params);				
					
				} else {
					doSearch(1);
				}
				$("#reset_btn").click(function(){
					 sessionStorage.removeItem('jt_list_params');						   
					 window.location.reload();							   
				});
			}else{
				alert(result.datas.error);
			}
		}
	});



	$("#searchButton").click(function(){
		 doSearch(1);							  
	});

	//页签切换
    function cutTabs (e) {
        var tab_val = 'all';
        $("#search_type a").each(function(){
            if($(this).hasClass('active'))
                tab_val = $(this).attr('data-val');
        })
        return tab_val;
    }

    function doSearch (curpage,params) {
		if (typeof params == 'undefined') {
			var params = getSearchVal();
			params['curpage'] = curpage;
		}
        var cuttab = cutTabs();
		var url = ApiUrl+"/index.php?act=style_goods&op=style_goods_list";
		if(params['is_xianhuo']==1){			
			url = ApiUrl+"/index.php?act=style_goods&op=warehouse_goods_list";
			if(cert_id){
				params['cert_id'] = cert_id;
			}
		}		
		sessionStorage.setItem('jt_list_params', JSON.stringify(params));
        $.ajax({
            url:url,
            type:'post',
            data:params,
            dataType:'json',
            success:function(result){
				if (!result || !result.datas) return;
				result.datas.cert_id = cert_id;
                var page = result.page;
				var pageCount = result.page_count;
				var recordCount = result.record_count;
				var contrastCount =  sessionStorageGet("jietuoContrastList").length;
				$("#search_type >.nav-item >.nav-link").each(function(obj,j){
                     var title = $(this).attr('data-title');
					 var val   = $(this).attr('data-val');
					 if($(this).hasClass("active") && recordCount>0){	
				        $(this).html(title+"("+recordCount+")");
						if(val=="contrast"){
							result.datas.del_show = true;//console.log(result.datas.del_show);
						}
				     }else if(val == "contrast" && contrastCount>0 && recordCount>0){
						$(this).html(title+"("+contrastCount+")"); 
					 }else{
						$(this).html(title);
					 }
				});
                if(params['is_xianhuo']==1){
                   var html = template.render('warehouse-goods-list',result.datas);   
				}else{
				   var html = template.render('style-goods-list',result.datas);				   
				}
                $("#style-goods-content").html(html);				
				
                /*
				if(cuttab == 'all') result.datas.allNum = result.record_count;
                if(cuttab == 'hot') result.datas.hotNum = result.record_count;
                if(cuttab == 'diff') {
                    result.datas.diffNum = result.record_count;
                    result.datas.del_show = false;
                }
                var html = template.render('nav-content', result.datas);
                $("#search_type").html(html);
                $("#search_type a").removeClass("active");
                $("#"+cuttab).addClass("active");               
				
                $("#search_type >.nav-item >.nav-link").click(function(){
                    $("#search_type >.nav-item >.nav-link").removeClass("active");
                    $(this).addClass("active");
                    doSearch(1);
                });*/
                 pagenav(page, pageCount);
            }
        });
    }
	function pagenav (page, pageCount) {
		var pageNavObj = null;
		jQuery(document).ready(function($) {
			//pageNavCreate("PageNav",200,1,pageNavCallBack);
			pageNavObj = new PageNavCreate("PageNavId",{
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
		doSearch(clickPage);
		//最后别忘了更新一遍翻页导航栏
		//pageNavCreate("PageNav",pageCount,clickPage,pageNavCallBack);
		pageNavObj = new PageNavCreate("PageNavId",{
			pageCount:getPageSet().pageCount,//总页数
			currentPage:clickPage,//当前页
			perPageNum:getPageSet().perPageNum,//每页按钮数
		});
		pageNavObj.afterClick(pageNavCallBack);
	}
	
	function getPageSet(){
		var obj = {
			pageCount:null,//总页数
			currentPage:null,//当前页
			perPageNum:null,//每页按钮数
		}
	
		if($("#testPageCount").val() && !isNaN(parseInt($("#testPageCount").val()))){
			obj.pageCount = parseInt($("#testPageCount").val());
		}else{
			obj.pageCount = parseInt($(".page-input-box > input").attr("placeholder"));
		}
	
		if($("#testCurrentPage").val() && !isNaN(parseInt($("#testCurrentPage").val()))){
			obj.currentPage = parseInt($("#testCurrentPage").val());
			obj.currentPage = (obj.currentPage<=obj.pageCount ? obj.currentPage : obj.pageCount);
		}else{
			obj.currentPage = 1;
		}
	
		if($("#testPerPageNum").val() && !isNaN(parseInt($("#testPerPageNum").val()))){
			obj.perPageNum = parseInt($("#testPerPageNum").val());
		}else{
			obj.perPageNum = null;
		}
	
		return obj;
	}
	
	
	
	window.create_div=function() 
    { 
      doSearch(1)
    } 
	
});


//删除对比
	function delConTrast(goods_sn){
		if(!confirm("确认删除吗")){
			return false;
		}
		//alert();return false;
		var jietuoContrastList = sessionStorageGet("jietuoContrastList");
		var existsIndex = $.inArray(goods_sn, jietuoContrastList);
		if(existsIndex==-1){
		  alert("已经删除！");return false;	
		}

		jietuoContrastList.splice(existsIndex,1);
		sessionStorageAdd("jietuoContrastList", jietuoContrastList, true);
		create_div();
		
		
	}
	
	
	//删除对比
	function delConTrast1(goods_sn){
		if(!confirm("确认删除吗")){
			return false;
		}
		//alert();return false;
		var xianhuoContrastList = sessionStorageGet("xianhuoContrastList");
		var existsIndex = $.inArray(goods_sn, xianhuoContrastList);
		if(existsIndex==-1){
		  alert("已经删除！");return false;	
		}

		xianhuoContrastList.splice(existsIndex,1);
		sessionStorageAdd("xianhuoContrastList", xianhuoContrastList, true);
		create_div();
		alert("删除成功")
		
	}
