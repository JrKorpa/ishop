$(function(){
    var key = getCookie('key');
    if (key) {
        window.location.href = WapSiteUrl+'/index.html';
        return;
    }

    $.animationLeft({
        valve : '#loginbtn2',
        wrapper : '#list-store-wrapper',
        scroll : '#list-store-scroll'
    });

    if (loginCfg.sso) {
        var token = getQueryString(loginCfg.token_qs);
        if (token) {
            login(token);
            return;
        }

        token = getCookie(loginCfg.token_ck);
        if (token) {
            login(token);
            return;
        }

        window.location.href = loginCfg.login;
    } else {
        var referurl = document.referrer;//上级网址
        $.sValid.init({
            rules:{
                username:"required",
                userpwd:"required"
            },
            messages:{
                username:"用户名必须填写！",
                userpwd:"密码必填!"
            },
            callback:function (eId,eMsg,eRules){
                if(eId.length >0){
                    var errorHtml = "";
                    $.map(eMsg,function (idx,item){
                        errorHtml += "<p>"+idx+"</p>";
                    });
                    errorTipsShow(errorHtml);
                }else{
                    errorTipsHide();
                }
            }
        });

        var allow_submit = true;
        $('#loginbtn').click(function(){//会员登陆
            if (allow_submit) {
                allow_submit = false;
            } else {
                return false;
            }
            var username = $('#username').val();
            var pwd = $('#userpwd').val();
            if(lrtrim(username)==""||lrtrim(pwd)==""){
            alert("请输入用户名和密码");
                allow_submit = true;
                return;
            }
            var client = 'wap';
            $.ajax({
                type: 'post',
                url: ApiUrl + "/index.php?act=login",
                data: {username: username, password: pwd, client: client},
                dataType: 'json',
                success: function (result) {
                    console.log(result.datas.key);
                    allow_submit = true;
                    postlogin(result);
                }
            });
        });
    }

    /**
     * 选择店铺
     */
	function select_store(){
        key = getCookie('key');
        $("#ul-store a").click(function(){
           var store_id=$(this).attr("data-id");
           var store_name=$(this).attr("data-name");
            //var referurl = document.referrer;
            var expireHours = 7200;
            delCookie('current_store_id');
            delCookie('current_store_name');
            addCookie('current_store_id', store_id, expireHours);
            addCookie('current_store_name', store_name, expireHours);
            $.ajax({
                type: 'post',
                url: ApiUrl + "/index.php?act=member_account&op=change_store",
                data: {store_id: store_id, key: key},
                dataType: 'json',
                success: function (result) {
                    console.log(result);
                    if (!result.datas.error) {
                        //location.href = referurl;
                        location.href = WapSiteUrl+'/index.html';;
                    } else {
                        alert(result.datas.error);
                    }
                }
            });
        });
    }

    function login(token) {
        
        $.ajax({
            type: 'post',
            url: ApiUrl + "/index.php?act=login&op=post_login",
            data: {token: token},
            dataType: 'json',
            success: function (result) {
                //console.log(result);
                if (!result.datas.error) {
                    addCookie(loginCfg.token_ck, token, 24);
                    postlogin(result);
                } else {
                    document.write('<h1>'+ result.datas.error + '</h1>');
                    setTimeout("logout()", 3000);
                }
            }
        });
    }

    function postlogin(result) {
        if (!result.datas.error) {
            if (typeof(result.datas.key) == 'undefined') {
                return false;
            } else {
                var expireHours = 24;
                if ($('#checkbox').prop('checked')) {
                    expireHours = 188;
                }
                // 更新cookie购物车
                //updateCookieCart(result.datas.key);
                addCookie('username', result.datas.username, expireHours);
                addCookie('key', result.datas.key, expireHours);
                addCookie('store_list', result.datas.store_list, expireHours);
                addCookie('current_store_id', result.datas.current_store_id, expireHours);
                addCookie('current_store_name', result.datas.current_store_name, expireHours);
               if (result.datas.store_count > 1) {
                   $("#ul-store").empty();
                   var store_str="";
                   $.each(result.datas.store_list, function(){
                       store_str+='<a href="javascript:void(0);" data-id="'+this.store_id+'" data-name="'+this.store_name+'"><h4>'+this.store_name+'</h4></a>';
                   });
                   $("#ul-store").html(store_str);
                   select_store();
                   $("#loginbtn2").click();
                } else {
                    //location.href = referurl;
                    location.href =  WapSiteUrl+'/index.html';
                }
            }
            errorTipsHide();
        } else {
            alert(result.datas.error);
        }
    }

});