var _history = getQueryString('history');
$(function(){
	var key = getCookie('key');
    if(!key){
        window.location.href = WapSiteUrl+'/login.html';
    }    
    template.helper('objtoarr', function(o){
        var arr = []
        for (var i in o) {
            arr.push(o[i]);
        }
        return arr;
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

    template.helper('isSet', function(obj){
        // 本身为空直接返回true
        if (obj == null) return true;
        if (obj.length === 0)  return true;
        //最后通过属性长度判断。
        for (var key in obj) {
            if(obj[key] == null) return true;
        }

        return false;
    });

    $(document).delegate(".select-list dd","click",function(){
        $(this).addClass("selected").siblings("dd").removeClass("selected");
        doSearch();
    })

    $(document).delegate("#order_by button","click",function(){
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
        doSearch();
    }) 

    $(document).delegate("#proclass-list a",'click',function(){
        $("#proclass-list li").removeClass("active");
        $(this).parents().addClass("active");
        initPage();
        //doSearch();
    })
    tabSearch();

    $(document).delegate("#myTab a",'click',function(){
        $("#myTab a").removeClass("active");
        $(this).addClass("active");
        doSearch();
    })
    $(document).delegate("#minprice,#maxprice","change",function(){
		 var val = $(this).val();
		 if(val!=""){
			  $("#price > dd").removeClass("selected");
		 }
	});
	$(document).delegate("#price","click",function(){
		 $("#minprice,#maxprice").val('');
	});
    $(document).delegate("#mincart,#maxcart","change",function(){
		 var val = $(this).val();
		 if(val!=""){
			  $("#cart > dd").removeClass("selected");
		 }
	});
	$(document).delegate("#cart","click",function(){
		 $("#mincart,#maxcart").val('');
	});
    $("#testBtn").click(function(event) {
        pageNavObj = new PageNavCreate("PageNavId",{
            pageCount:getPageSet().pageCount,//总页数
            currentPage:getPageSet().currentPage,//当前页
            perPageNum:getPageSet().perPageNum,//每页按钮数
        });
        pageNavObj.afterClick(pageNavCallBack);
    });

    $(".test-box input").keydown(function(event) {
        if(event.which == 13){
            pageNavObj = new PageNavCreate("PageNavId",{
                pageCount:getPageSet().pageCount,//总页数
                currentPage:getPageSet().currentPage,//当前页
                perPageNum:getPageSet().perPageNum,//每页按钮数
            });
            pageNavObj.afterClick(pageNavCallBack);
        }
    });

    $("#pageNavStyle").change(function(event) {
        var selectVal = $(this).val();
        if(selectVal==0){
            $("#PageNavId").removeClass('dark');
            $("footer").removeClass('dark-footer');
        }else if(selectVal==1){
            $("#PageNavId").addClass('dark');
            $("footer").addClass('dark-footer');
        }
    });
});

function tabSearch () {
    var key = getCookie('key');
    $.ajax({
        url:ApiUrl+"/index.php?act=couple_goods&op=couple_goods_tab",
        type:'post',
        data:{key:key},
        dataType:'json',
        success:function(result){
            if(result.code ==200){
                var params = _history?sessionStorage.getItem('couple_list_params'):null;
                params = JSON.parse(params); 
                result.datas.ch_cat_type = 11;
                if(params){
                    if(params.cat_type != undefined){
                        result.datas.ch_cat_type = params.cat_type;
                    }
                }
                var cat_type = result.datas.ch_cat_type;
                var html = template.render('proclass-content',result.datas);
                $("#proclass-list").html(html);
                initPage(cat_type);
            }else{
                alert(result.datas.error);
            }
        }
    });
}

function initPage (cat_type) {
    //var cat_type=11;//分类
    $("#proclass-list a").each(function(){
        if($(this).parent().hasClass('active')){
            cat_type = $(this).attr('data-val');
        }
    })
    var key = getCookie('key');
	var xilie = getQueryString('xilie'); 
    $.ajax({
        url:ApiUrl+"/index.php?act=couple_goods&op=couple_goods_index",
        type:'post',
        data:{key:key,tab:cat_type},
        dataType:'json',
        success:function(result){
            if(result.code ==200){
                var params = _history?sessionStorage.getItem('couple_list_params'):null;
                if (params == null) {
                    params = getSearchVal();
                    params['search_type'] = 'all';
                    params['key'] = key;
                    params = JSON.stringify(params)
                }
                params = JSON.parse(params);
                if(params){
                    result.datas.params = params;
                    if(params.goods_name == undefined){
                        params.goods_name ="";
                    }
                    $("#goods_name").val(params.goods_name);
                }
                var html = template.render('prosearch-content',result.datas);
                $("#prosearch-list").html(html);	
				
				if(xilie){
					$("#pick_xilie dd").each(function(){
						if($(this).attr("data-val")==xilie){
							$(this).addClass("selected");
						}else{
							$(this).removeClass("selected");
						}
					});
				}
                //var params = sessionStorage.getItem('couple_list_params');
                if (params) {
                    //params = JSON.parse(params);
                    doSearch(params['curpage'], params);
                    //TODO: bind filters
                } else {
                  doSearch();
                }
            }else{
                alert(result.datas.error);
            }
        }
    });
}

function getSearchVal (e) {
    var data = {};

    data['goods_name'] = $("#goods_name").val();

    var price_min = $("#minprice").val();
    var price_max = $("#maxprice").val();

    var carat_min = $("#mincart").val();
    var carat_max = $("#maxcart").val();
    
    if(!isNaN(parseFloat(price_min))) data['price_min'] = parseFloat(price_min);
    if(!isNaN(parseFloat(price_max))) data['price_max'] = parseFloat(price_max);

    if(!isNaN(parseFloat(carat_min))) data['carat_min'] = parseFloat(carat_min);
    if(!isNaN(parseFloat(carat_max))) data['carat_max'] = parseFloat(carat_max);

    data['zhiquan'] = $("#zhiquan").val();

    var cat_type='';//分类
    $("#proclass-list a").each(function(){
        if($(this).parent().hasClass('active')){
            cat_type = $(this).attr('data-val');
        }
    })
    data['cat_type'] = cat_type == ''?11:cat_type;

    var pick_xilie='';//xilie
    $("#pick_xilie dd").each(function(){
        if($(this).hasClass('selected'))
            pick_xilie = $(this).attr('data-val');
    })
    data['pick_xilie'] = pick_xilie;

    var jud_type='';//类别
    $("#jud_type dd").each(function(){
        if($(this).hasClass('selected'))
            jud_type = $(this).attr('data-val');
    })
    data['jud_type'] = jud_type;

    var cart=[];//钻大小
    $("#cart dd").each(function(){
        if($(this).hasClass('selected')){
            cart.push($(this).attr('data-cartmin'));
            cart.push($(this).attr('data-cartmax'));
        }
            
    })
    data['cart'] = cart;

    var price=[];//price
    $("#price dd").each(function(){
        if($(this).hasClass('selected')){
            price.push($(this).attr('data-pricemin'));
            price.push($(this).attr('data-pricemax'));
        }
    })
    data['price'] = price;

    var caizhi='';//caizhi
    $("#caizhi dd").each(function(){
        if($(this).hasClass('selected'))
            caizhi = $(this).attr('data-val');
    })
    data['caizhi'] = caizhi;

    var tuo_type='';//tuo_type
    $("#tuo_type dd").each(function(){
        if($(this).hasClass('selected'))
            tuo_type = $(this).attr('data-val');
    });
    data['tuo_type'] = tuo_type;
    
	var is_xianhuo = 1 ;//默认现货
	$("#is_xianhuo dd").each(function(){
		if($(this).hasClass('selected'))
			is_xianhuo = $(this).attr('data-val');
	})
	data['is_xianhuo'] = is_xianhuo;
	
    $("#order_by button").each(function(){
        if($(this).hasClass('active')){
            data['order_by'] = $(this).attr('data-val')+'|'+$(this).find("i").attr('data-val');
        }
    });

    var warehouse ="总部";//默认门店
    $("#warehouse dd").each(function(){
        if($(this).hasClass('selected'))
            warehouse = $(this).attr('data-val');
    });
    data['warehouse']=warehouse;
    return data;
}

//页签切换
function cutTabs (e) {
    var tab_val = 'all';
    $("#myTab a").each(function(){
        if($(this).hasClass('active'))
            tab_val = $(this).attr('data-val');
    })
    return tab_val;
}

function doSearch (clickPage,params) {
    var key = getCookie('key');
    var cuttab = cutTabs();
    if (typeof params == 'undefined' || params.length == 0) {
        var params = getSearchVal(params);
        params['curpage'] = clickPage;
        params['key'] = key;
        params['gc_parent_id'] = 1062;
        params['search_type'] = cuttab;
        if(cuttab=="diff"){
            params['contrast_list'] =  sessionStorageGet("coupleContrastList"); 
        }
    }
	var url = ApiUrl+"/index.php?act=marry_goods&op=marry_goods_list";
	if(params['is_xianhuo']==1){			
		url = ApiUrl+"/index.php?act=marry_goods&op=xianhuo_list";
	}
    sessionStorage.setItem('couple_list_params', JSON.stringify(params));
    $.ajax({
        url:url,
        type:'post',
        data:params,
        dataType:'json',
        success:function(result){
            if(result.code ==200){
                var pageCount = result.page_count;
				var record_count = result.record_count;
                var page = result.page;
                if(sessionStorageGet("coupleContrastList")) result.datas.diffNum=sessionStorageGet("coupleContrastList").length;
                if(cuttab == 'all') result.datas.allNum = record_count;
                if(cuttab == 'hot') result.datas.hotNum = record_count;
				if(cuttab == 'diff') {
					result.datas.diffNum = record_count;
					result.datas.del_show = false;
				}
				if(params['is_xianhuo']==1){
                   var html = template.render('warehouse-goods-list',result.datas);   
				}else{
				   var html = template.render('data-content',result.datas); 
				}
                //var html = template.render('data-content',result.datas);
                $("#data-list").html(html);
                var html = template.render('page-content', result.datas);               
                $("#page").html(html);
                var html = template.render('nav-content', result.datas);               
                $("#nav-list").html(html);
                $("#myTab a").removeClass("active");
                $("#"+cuttab).addClass("active");
                pagenav(page, pageCount);
            }else{
                alert(result.datas.error);
            }
        }
    });
}

    //重置页面
    function doReset () {
        sessionStorageAdd("couple_list_params", [], true);
        window.location.reload();
    }

//对比
function conTrast (e) {
    var data = new Array();
    var coupleinfo = new Array();
    var diffinfo = sessionStorageGet("couple_info");
    if(diffinfo != false){
        for (var i = 0; i < diffinfo.length; i++) {
            coupleinfo[i] = JSON.parse(diffinfo[i]);
        };
    }
    data['style_goods_list'] = coupleinfo;
    data['WapSiteUrl'] = WapSiteUrl;
    data['pagetab_show'] = false;
    var html = template.render('data-content', data);
    $("#data-list").html(html);
    var html = template.render('page-content', data);               
    $("#page").html(html);
}

function delConTrast(goods_sn){
	//alert();return false;
	if(!confirm("确认删除吗")){
			return false;
	}
	var coupleContrastList = sessionStorageGet("coupleContrastList");
	var existsIndex = $.inArray(goods_sn, coupleContrastList);
	if(existsIndex==-1){
      alert("已经删除！");
	  return false;	
	}
	coupleContrastList.splice(existsIndex,1);
    sessionStorageAdd("coupleContrastList", coupleContrastList, true);
	doSearch(1);
	
	
}

//分页导航
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

//分页
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