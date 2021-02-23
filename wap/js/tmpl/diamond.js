var pageCount=0;
var goods_sn = getQueryString('goods_sn');
var cert_id  = getQueryString('cert_id');
var cart     = getQueryString('carat');;
var style_sn = getQueryString('style_sn');
var _history = getQueryString('history');
var is_xianhuo = false;

if(!isNaN(parseInt(goods_sn)) && goods_sn>0){
	is_xianhuo = true;	
}else if(goods_sn){
	var arr = goods_sn.split("-");
	cart = cart?cart:arr[3]/100;
    style_sn = arr[0];
}
$(function(){
    var key = getCookie('key');
    if(!key){
        window.location.href = WapSiteUrl+'/login.html';
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
		        window.location.href = WapSiteUrl+'/tmpl/diamond_detail.html?cert_id='+cert_id+'&goods_sn='+goods_sn;
			 }else{
				window.location.href = WapSiteUrl+'/tmpl/diamond_detail.html?cert_id='+cert_id;
			 }
		}else if(goods_sn){
		    window.location.href = WapSiteUrl+'/tmpl/diamond.html?goods_sn='+goods_sn;
		}else{
			window.location.href = WapSiteUrl+'/tmpl/diamond.html';
		}
	});
	var ac_id = getQueryString('ac_id');
    var hasOwnProperty = Object.prototype.hasOwnProperty;

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

    template.helper('objtoarr', function(o){
        var arr = []
        for (var i in o) {
            arr.push(o[i]);
        }
        return arr;
    });

    template.helper('toFixed', function(val){
        var price = new Number(val).toFixed(2);
        return price;
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

    template.helper('log', function(o){
        str = console.log(o);
        return str;
    });
    initPage(style_sn);//初始化
   
    $(document).delegate(".btn-group button",'click',function(){
         if( $(this).hasClass("active")){
            $(this).removeClass("active");
        }else{
            $(this).addClass("active");
        }
        doSearch();
    })

    $(document).delegate("#myTab a",'click',function(){
        if(!style_sn){
           $("#myTab a").removeClass("active");
            $(this).addClass("active");
            doSearch(); 
        }
    })

    $(document).delegate("#sortcheck",'click',function(){
        var val = $(this).attr('data-val');
        if(val == '' || val == 'undefined' || val == 'desc'){
            $(this).attr('data-val', 'asc');
            $(this).attr('src', '../images/img/down.png');
        }else{
            $(this).attr('data-val', 'desc');
            $(this).attr('src', '../images/img/up.png');
        }
        doSearch();
    })

    $(document).delegate("#del",'click',function(){
        if(!confirm("确认删除吗")){
            return false;
        }
        $(this).parent().parent().remove();
        var val = $(this).attr('data-val');
        delStorage(val);
    })

    //doSearch();

    /*if(goods_sn){
        setTimeout(function(){
            doSearch();
            //$("#btnSearch").click();
        },800); 
    }*/

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

function initPage (style_sn) {
    var key = getCookie('key');
    $.ajax({
        url:ApiUrl+"/index.php?act=diamond&op=diamond_index",
        type:'get',
        data:{key:key,style_sn:style_sn},
        jsonp:'callback',
        dataType:'jsonp',
        success:function(result){
            var data = result.datas;
            data.WapSiteUrl = WapSiteUrl;
            data.cart = cart;
            data.is_shape_disabled = false;
            data.is_xilie_disabled = false;
            var params = _history?sessionStorage.getItem('dia_list_params'):null;
            if (params == null) {
                params = getSearchVal();
                params = JSON.stringify(params)
            }
            if (params) {
                params = JSON.parse(params);
                data.params = params;
                if(data.shape_sed.length>0){
                    data.params.shape = data.shape_sed;
                    data.is_shape_disabled = true;
                }
                if(data.cert_sed.length>0){
                    data.params.certificate = data.cert_sed;
                    data.is_cert_disabled = data.is_disabled;
                }
                //console.log(data.params);
            }
            var html = template.render('diamond-search', data);               
            $("#search-content").html(html);
            if (params) {
                var carat_min = $('#carat_min').val();
                var carat_max = $('#carat_max').val();
                if(!isNaN(parseFloat(carat_min))) params.carat_min = parseFloat(carat_min);
                if(!isNaN(parseFloat(carat_max))) params.carat_max = parseFloat(carat_max);
                params.style_sn = style_sn;
                //console.log(params);
                doSearch(params['clickPage'], params);
                //TODO: bind filters
            } else {
                doSearch();
            }
        }
    });
}

function getSearchVal (e) {
    var data = {};

    var carat_min = $('#carat_min').val();
    var carat_max = $('#carat_max').val();
    var price_min = $('#price_min').val();
    var price_max = $('#price_max').val();
    var cert_id = $('#cert_id').val();

    data['carat_min'] = "";
    data['carat_max'] = "";
    data['price_min'] = "";
    data['price_max'] = "";
    if(cart){
        //data['carat_min'] = parseFloat(cart);
        //data['carat_max'] = parseFloat(cart);
        if(!isNaN(parseFloat(cart))) data['carat_min'] = parseFloat(cart);
        if(!isNaN(parseFloat(cart))) data['carat_max'] = parseFloat(cart);
    }else{
        if(!isNaN(parseFloat(carat_min))) data['carat_min'] = parseFloat(carat_min);
        if(!isNaN(parseFloat(carat_max))) data['carat_max'] = parseFloat(carat_max);
    }
    if(!isNaN(parseFloat(price_min))) data['price_min'] = parseFloat(price_min);
    if(!isNaN(parseFloat(price_max))) data['price_max'] = parseFloat(price_max);
    data['style_sn'] = style_sn;
    data['cert_id'] = cert_id;
    data['pricesort'] = $("#sortcheck").attr('data-val');

    var shape=[];//形状
    $(".shape button").each(function(){
        if($(this).hasClass('active'))
            shape.push($(this).attr('data-val'));
    })
    data['shape'] = shape;

    var color=[];//颜色
    $(".color button").each(function(){
        if($(this).hasClass('active'))
            color.push($(this).attr('data-val'));
    })
    data['color'] = color;

    var cut=[];//切工
    $(".cut button").each(function(){
        if($(this).hasClass('active'))
            cut.push($(this).attr('data-val'));
    })
    data['cut'] = cut;

    var clarity=[];//净度
    $(".clarity button").each(function(){
        if($(this).hasClass('active'))
            clarity.push($(this).attr('data-val'));
    })
    data['clarity'] = clarity;

    var fluorescence=[];//荧光
    $(".fluorescence button").each(function(){
        if($(this).hasClass('active'))
            fluorescence.push($(this).attr('data-val'));
    })
    data['fluorescence'] = fluorescence;

    var symmetry=[];//对称
    $(".symmetry button").each(function(){
        if($(this).hasClass('active'))
            symmetry.push($(this).attr('data-val'));
    })
    data['symmetry'] = symmetry;

    var polishing=[];//抛光
    $(".polishing button").each(function(){
        if($(this).hasClass('active'))
            polishing.push($(this).attr('data-val'));
    })
    data['polishing'] = polishing;

    var certificate=[];//证书
    $(".certificate button").each(function(){
        if($(this).hasClass('active'))
            certificate.push($(this).attr('data-val'));
    })
    data['certificate'] = certificate;

    /*var xilie=[];//系列
    $(".series button").each(function(){
        if($(this).hasClass('active'))
            xilie.push($(this).attr('data-val'));
    })
    data['xilie'] = xilie;*/

    var good_type=[];//商品类型
    $(".good_type button").each(function(){
        if($(this).hasClass('active'))
            good_type.push($(this).attr('data-val'));
    })
    data['good_type'] = good_type;

    var is_3dimage=[];//3D图
    $(".is_3dimage button").each(function(){
        if($(this).hasClass('active'))
            is_3dimage.push($(this).attr('data-val'));
    })
    data['is_3dimage'] = is_3dimage;
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

//裸钻搜索
function doSearch (clickPage,params) {
    var key = getCookie('key');
    var cuttab = cutTabs();
    if (typeof params == 'undefined') {
        var params = getSearchVal();
        params['clickPage'] = clickPage;
    }
    params['key'] = key;
    //console.log(params);
    if(cuttab == 'dff'){
        conTrast(params);
    }else{
        params['is_hot'] = cuttab == 'hot'?1:'';
        var is_hot = params['is_hot'];
        sessionStorage.setItem('dia_list_params', JSON.stringify(params));
        $.ajax({
            url:ApiUrl+"/index.php?act=diamond&op=diamond_list",
            type:'get',
            data:params,
            jsonp:'callback',
            dataType:'jsonp',
            success:function(result){
                var data = result.datas;
                pageCount = data.curlist.pageCount;
                var recordCount = data.curlist.recordCount;
                var page = data.curlist.page;
                var _recordCount = data._recordCount;
                data.WapSiteUrl = WapSiteUrl;
                data.pagetab_show = true;
                if(pageCount == undefined || pageCount == '') pageCount = 1;
                //if(is_hot) data.pagetab_show = true;
                data.allNum = is_hot?_recordCount:recordCount;
                data.hotNum = is_hot?recordCount:_recordCount;
                data.goods_sn = goods_sn;
                data.style_sn = style_sn;
				data.is_xianhuo = is_xianhuo;
                data.is_hot =is_hot;
                var diffinfo = sessionStorageGet("diamond_info");
                data.dffNum = diffinfo == false?0:diffinfo.length;
                data.price_sort = $("#sortcheck").attr('data-val');
                data.src_val = $("#sortcheck").attr('src');
                if(!data.src_val){
                    data.src_val = '../images/img/order.png';
                }
                console.log(data.src_val);
                var html = template.render('diamond-top', data);               
                $("#diamtop-content").html(html);
                var html = template.render('diamond-list', data);               
                $("#diamond-content").html(html);
                var html = template.render('page-content', data);               
                $("#page").html(html);
                $("#myTab a").removeClass("active");
                $("#"+cuttab).addClass("active");
                //if(!is_hot){
                    pagenav(page, pageCount);
                //}
            }
        });
    }
}

//对比
function conTrast (e) {
    var data = new Array();
    var diamondlist = new Array();
    var diffinfo = sessionStorageGet("diamond_info");
    if(diffinfo != false){
        for (var i = 0; i < diffinfo.length; i++) {
             var aa = JSON.parse(diffinfo[i]);
			 diamondlist[i] = aa[0];
        };
    }
    data['diamond_list'] = diamondlist;
    data['WapSiteUrl'] = WapSiteUrl;
    data['pagetab_show'] = true;
    data['del'] = false;
    data['goods_sn'] = goods_sn;
    var html = template.render('diamond-list', data);
    $("#diamond-content").html(html);
    var html = template.render('page-content', data);               
    $("#page").html(html);
    pagenav(1, 1);
}

//删除对比裸钻
function delStorage (val) {
    var data = new Array();
    var diffinfo = sessionStorageGet("diamond_info");
    if(diffinfo != false){
        sessionStorageAdd("diamond_info", [], true);
        for (var i = 0; i < diffinfo.length; i++) {
             var arr = JSON.parse(diffinfo[i]);
             if(arr[0].cert_id == val) continue;
             sessionStorageAdd("diamond_info", arr, false);
        };
    }
	var diffinfo = sessionStorageGet("diamond_info");
	var length = diffinfo.length;
	$("#dff").html("比较("+length+")");
	
	
    
}

//重置页面
function doReset () {
    sessionStorageAdd("dia_list_params", [], true);
    window.location.reload();
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