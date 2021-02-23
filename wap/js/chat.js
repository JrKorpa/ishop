
var nodeSiteUrl = '';
/**
 * current login user
 */
var memberInfo = {};
/**
 * customer server
 */
var to_memberInfo = {};
var resourceSiteUrl = '';
var smilies_array = []
smilies_array[1] = [['1', ':smile:', 'smile.gif', '28', '28', '28','微笑'], ['2', ':sad:', 'sad.gif', '28', '28', '28','难过'], ['3', ':biggrin:', 'biggrin.gif', '28', '28', '28','呲牙'], ['4', ':cry:', 'cry.gif', '28', '28', '28','大哭'], ['5', ':huffy:', 'huffy.gif', '28', '28', '28','发怒'], ['6', ':shocked:', 'shocked.gif', '28', '28', '28','惊讶'], ['7', ':tongue:', 'tongue.gif', '28', '28', '28','调皮'], ['8', ':shy:', 'shy.gif', '28', '28', '28','害羞'], ['9', ':titter:', 'titter.gif', '28', '28', '28','偷笑'], ['10', ':sweat:', 'sweat.gif', '28', '28', '28','流汗'], ['11', ':mad:', 'mad.gif', '28', '28', '28','抓狂'], ['12', ':lol:', 'lol.gif', '28', '28', '28','阴险'], ['13', ':loveliness:', 'loveliness.gif', '28', '28', '28','可爱'], ['14', ':funk:', 'funk.gif', '28', '28', '28','惊恐'], ['15', ':curse:', 'curse.gif', '28', '28', '28','咒骂'], ['16', ':dizzy:', 'dizzy.gif', '28', '28', '28','晕'], ['17', ':shutup:', 'shutup.gif', '28', '28', '28','闭嘴'], ['18', ':sleepy:', 'sleepy.gif', '28', '28', '28','睡'], ['19', ':hug:', 'hug.gif', '28', '28', '28','拥抱'], ['20', ':victory:', 'victory.gif', '28', '28', '28','胜利'], ['21', ':sun:', 'sun.gif', '28', '28', '28','太阳'],['22', ':moon:', 'moon.gif', '28', '28', '28','月亮'], ['23', ':kiss:', 'kiss.gif', '28', '28', '28','示爱'], ['24', ':handshake:', 'handshake.gif', '28', '28', '28','握手']];

//敏感参数改为sessionStorage传递。
// var t_id = getQueryString('t_id');//店铺店长的member_id
// var chat_goods_id = getQueryString('goods_id');
// var store_id = getQueryString("store_id");
// var req_type = getQueryString('req_type'); //请求类型：presale ->　售前，aftersale　->　售后，　'' ->　普通对话

var chat_info = getChatInfo(function (e) {
    window.location.href = "/wap/tmpl/chat_list.html";
});
var t_id = chat_info.t_id;
var req_type = chat_info.req_type;
var is_seller = chat_info.is_seller;
var chat_goods_id = chat_info.goods_id;
var key = getCookie('key');

/**
 * chat socket object
 */
var current_socket = null;
var page = 10;
var curpage = 1;
var hasMore = 'true';
var myScroll,pullDownEl, pullDownOffset,pullUpEl, pullUpOffset,wrapper;
function runloading(){
    $('.container').each(function(index,val){
        var bar = $(this).find('.bar');
        var width = $(this).width();
        var twidth = bar.data('percent')*width;
        if(bar.width()<twidth){
            //bar.css('width',parseInt(bar.css('width'))+1+'%');
            bar.css('width',parseInt(bar.data('percent'))+'%');
        }
        if(bar.css('width') == "100%"){
            window.clearTimeout(timeout);
            $(this).remove();
            return;
        }
        var timeout=window.setTimeout("runloading()",100);
    })
}
// 表情
function update_chat_msg(msg, type){
    switch(type) {
        case "image/jpeg": {
            msg = '<img src="' + msg + '"/>';
            break;
        }
        case "audio/wav": {
            msg = '<div class="audio"><div ontouchstart="play_sound(this);" class="Play"><span class="icon icon-play"></span></div><div class="Process"> <div class="ProcessAll"></div><div class="ProcessNow"></div><div class="SongTime">00:00&nbsp;|&nbsp;00:00</div> </div><audio controls="controls" src="'+msg +'">wav</audio></div>';
            break;
        }
        case "video/mp4": {
            msg = '<video controls="controls"><source src="'+msg +'" type="video/mp4"></video>';
            break;
        }
        default: {
            if (typeof smilies_array !== "undefined") {
                msg = ''+msg;
                for(var i in smilies_array[1]) {
                    var s = smilies_array[1][i];
                    var re = new RegExp(""+s[1],"g");
                    var smilieimg = '<img title="'+s[6]+'" alt="'+s[6]+'" src="'+resourceSiteUrl+'/js/smilies/images/'+s[2]+'">';
                    msg = msg.replace(re,smilieimg);
                }
            }
        }
    }

    return msg;
}
function append_html(msgData) {console.log(msgData);
    msgData.t_msg = update_chat_msg(msgData.t_msg, msgData.msg_type);
    var html = '<dl class="msg-time">'+msgData.time+'</dl><dl class="'+msgData.class+'"><dt><img src="' + msgData.avatar + '" alt=""/><i></i></dt><dd>'+msgData.t_msg+'</dd></dl>';
    $("#chat_msg_html").prepend(html);
    if (!$.isEmptyObject(msgData.goods_info)) {
        var goods = msgData.goods_info;
        var html = '<div class="nctouch-chat-product"> <a href="' + WapSiteUrl + '/tmpl/product_detail.html?goods_id=' + goods.goods_id + '" target="_blank"><div class="goods-pic"><img src="' + goods.pic36 + '" alt=""/></div><div class="goods-info"><div class="goods-name">' + goods.goods_name + '</div><div class="goods-price">￥' + goods.goods_promotion_price + '</div></div></a> </div>';
        $("#chat_msg_html").prepend(html);
    }
    $("#anchor-bottom")[0].scrollIntoView();
}
function initPage(page,curpage){
    $.ajax({
        type:'post',
        url:ApiUrl+'/index.php?act=member_chat&op=get_chat_log&page='+page+"&curpage="+curpage,
        data:{key:key,t_id:t_id,t:30},
        dataType:'json',
        success: function(result){
            if(result.code == 200){
                if (result.datas.list.length == 0) {
                    $.sDialog({
                        skin:"block",
                        content:'暂无聊天记录',
                        okBtn:false,
                        cancelBtn:false
                    });
                    return false;
                }
                result.datas.list.reverse();
                $("#wrapper").attr('has_more',result.datas.has_more);
                if(result.datas.list.length == 0 && result.datas.has_more=='false'){
                    $.sDialog({
                        skin:"red",
                        content:'没有了',
                        okBtn:false,
                        cancelBtn:false
                    });
                    return false;
                }
                for (var i=0; i<result.datas.list.length; i++) {
                    var _list = result.datas.list[i];
                    if (_list.f_id != t_id) {
                        var data = {};
                        data.class = 'msg-me';
                        data.avatar = memberInfo.member_avatar;
                        data.t_msg = _list.t_msg;
                        data.msg_type = _list.msg_type;
                        data.time = _list.time;
                        append_html(data);
                    } else {
                        var data = {};
                        data.class = 'msg-other';
                        data.avatar = to_memberInfo.store_avatar == '' ? to_memberInfo.member_avatar : to_memberInfo.store_avatar;
                        data.t_msg = _list.t_msg;
                        data.msg_type = _list.msg_type;
                        data.time = _list.time;
                        append_html(data);
                    }
                }
                myScroll.refresh();
            } else {
                $.sDialog({
                    skin:"red",
                    content:result.datas.error,
                    okBtn:false,
                    cancelBtn:false
                });
                return false;
            }
        }
    });
}

function pullDownAction(){
    var hasMore = $("#wrapper").attr("has_more");
    if(hasMore == "true"){
        curpage = curpage+1;
        initPage(page,curpage);
    }else{
        $.sDialog({
            skin:"red",
            content:'没有了',
            okBtn:false,
            cancelBtn:false
        });
        return false;
    }
}


function loaded() {
    wrapper = document.getElementById('wrapper');
    pullDownEl = document.getElementById('pullDown');
    pullDownOffset = pullDownEl.offsetHeight;
    myScroll = new iScroll('wrapper', {
        useTransition: true,
        checkDOMChanges: true,
        hideScrollbar: true,
        vScrollbar: true,
        hScrollbar: false,
        fadeScrollbar: true,
        momentum: true,
        topOffset: pullDownOffset,
        onRefresh: function () {
            if (pullDownEl.className.match('loading')) {
                pullDownEl.className = '';
                pullDownEl.querySelector('.pullDownLabel').innerHTML = '下拉刷新...';
            }
        },
        onScrollMove: function () {
            if (this.y > 5 && !pullDownEl.className.match('flip')) {
                pullDownEl.className = 'flip';
                pullDownEl.querySelector('.pullDownLabel').innerHTML = '松手开始更新...';
                this.minScrollY = 0;
            } else if (this.y < 5 && pullDownEl.className.match('flip')) {
                pullDownEl.className = '';
                pullDownEl.querySelector('.pullDownLabel').innerHTML = '下拉刷新...';
                this.minScrollY = -pullDownOffset;
            }
        },
        onScrollEnd: function () {
            if (pullDownEl.className.match('flip')) {
                pullDownEl.className = 'loading';
                pullDownEl.querySelector('.pullDownLabel').innerHTML = '加载中...';
                pullDownAction(); // Execute custom function (ajax call?)
            }
        }
    });
    setTimeout(function () { document.getElementById('wrapper').style.left = '0'; }, 800);
}

function setContentHeight(){
    var height=document.documentElement.clientHeight;
    var bottom_height=$(".nctouch-chat-bottom").height();
    $("#wrapper").height(height-bottom_height-77);
    //$("#chat_msg_html").height(height-bottom_height-76);
}

function showLoading(){
    var index = layer.load(2, {
        time: 30*1000,
        shade: [0.1,'#fff'], //0.1透明度的白色背景
        area: ['50px', '50px']
    });
}

function closeLoading(){
    layer.closeAll();
}

$("#open_image_picker").on('click',function(){
    $("#img-file").click();
});

$(function(){
    setContentHeight();
    //如果为微信环境，只允许发文字和表情
    if(isWeiXin()){
        $(".sendmessage").show();
        $(".chat-input-layout .input-box").css("left","0.4rem");
        $("#open_video_capture,#open_video_chat").hide();
    }else{
        $(".icon-maikefeng").show();
        $(".icon-jiahao").show();

        if (window.platformId == 'ios') {
            $("#open_video_chat").hide();
        }
    }
    $('#msg').bind('focus',filter_time);
    var str = '';
    var now = ''
    function filter_time() {
        var time = setInterval(filter_staff_from_exist, 100);
        $(this).bind('blur',function(){
            clearInterval(time);
        });
    };
    function filter_staff_from_exist() {
        now = $.trim($('#msg').val());
        if (now != '') {
            $('#open_more').addClass('hide');
            $('#submit').removeClass('hide');
        }  else {
            $('#submit').addClass('hide');
            $('#open_more').removeClass('hide');
        }
        str = now;
    }

    if(is_seller) {
        $.getJSON(ApiUrl + '/index.php?act=member_chat&op=get_seller_node_info', {
            key: key,
            u_id: t_id,
            chat_goods_id: chat_goods_id
        }, function (result) {
            checkLogin(result.login, true);
            connectNode(result.datas);
            /*if (!$.isEmptyObject(result.datas.chat_goods)) {
                var goods = result.datas.chat_goods;
                var html = '<div class="nctouch-chat-product"> <div class="goods-pic"><img src="' + goods.pic + '" alt=""/></div><div class="goods-info"><div class="goods-name"><a href="' + WapSiteUrl + "/tmpl/product_detail.html?goods_id=" + goods.goods_id + '" target="_blank">' + goods.goods_name + '</div></a><div class="goods-price">￥' + goods.goods_promotion_price + "</div><p><a href='javascript:;' class='send_goods_url'>发送链接</a></p></div> </div>";
              // $("#chat_msg_html").append('<dl class="msg-me" id="div_"><dt><img src="/data/upload/shop/common/default_user_portrait.gif" alt=""/><i></i></dt><dd><img src="/data/111.jpg" alt=""/><div class="container"><div class="bar" style="width:0%;" data-percent="1"></div></div></dd></dl>');
               // $("#chat_msg_html").append('<dl class="msg-me" id="div_"><dt><img src="/data/upload/shop/common/default_user_portrait.gif" alt=""/><i></i></dt><dd><video controls="controls" id="video_"><source src="/data/movie.ogg" type="audio/ogg"></video><div class="container"><div class="bar" style="width:0%;" data-percent="1"></div></div></dd></dl>');
                //$("#chat_msg_html").append('<dl class="msg-me" id="div_"><dt><img src="/data/upload/shop/common/default_user_portrait.gif" alt=""/><i></i></dt><dd><div class="audio"><div onclick="play_sound(this);" class="Play"><span class="icon icon-play"></span></div><div class="Process"> <div class="ProcessAll"></div><div class="ProcessNow"></div><div class="SongTime">00:00&nbsp;|&nbsp;00:00</div> </div><audio controls="controls" id="audio_" src="/data/song.mp3" >wav</audio></div><div class="container"><div class="bar" style="width:0%;" data-percent="1"></div></div></dd></dl>');
                $("#chat_msg_html").append(html);
            }*/
        });
    }
    else {
        if(req_type == "presale" || req_type == "aftersale") {
            store_id = t_id;
            u_id = null;
        }
        else {
            u_id = t_id;
            store_id = null;
        }
        $.getJSON( ApiUrl+'/index.php?act=member_chat&op=get_node_info',{key:key, u_id:u_id, chat_goods_id:chat_goods_id, store_id: store_id, req_type: req_type}, function(result){
            checkLogin(result.login, false);
            connectNode(result.datas);
            if (!$.isEmptyObject(result.datas.chat_goods)) {
                var goods = result.datas.chat_goods;
                var html = '<div class="nctouch-chat-product"> <div class="goods-pic"><img src="' + goods.pic + '" alt=""/></div><div class="goods-info"><div class="goods-name"><a href="' + WapSiteUrl + "/tmpl/product_detail.html?goods_id=" + goods.goods_id + '" target="_blank">' + goods.goods_name + '</div></a><div class="goods-price">￥' + goods.goods_promotion_price + "</div><p><a href='javascript:;' class='send_goods_url'>发送链接</a></p></div> </div>";
                //$("#chat_msg_html").append('<dl class="msg-me" id="div_"><dt><img src="/data/upload/shop/common/default_user_portrait.gif" alt=""/><i></i></dt><dd><img src="/data/111.jpg" alt=""/><div class="container"><div class="bar" style="width:0%;" data-percent="1"></div></div></dd></dl>');
                // $("#chat_msg_html").append('<dl class="msg-me" id="div_"><dt><img src="/data/upload/shop/common/default_user_portrait.gif" alt=""/><i></i></dt><dd><img src="/data/111.jpg" alt=""/><div class="container"><div class="bar" style="width:0%;" data-percent="1"></div></div></dd></dl>');

                // $("#chat_msg_html").append('<dl class="msg-me" id="div_"><dt><img src="/data/upload/shop/common/default_user_portrait.gif" alt=""/><i></i></dt><dd><video controls="controls" id="video_"><source src="/data/movie.ogg" type="audio/ogg"></video><div class="container"><div class="bar" style="width:0%;" data-percent="1"></div></div></dd></dl>');
                //$("#chat_msg_html").append('<dl class="msg-me" id="div_"><dt><img src="/data/upload/shop/common/default_user_portrait.gif" alt=""/><i></i></dt><dd><div class="audio"><div onclick="play_sound(this);" class="Play"><span class="icon icon-play"></span></div><div class="Process"> <div class="ProcessAll"></div><div class="ProcessNow"></div><div class="SongTime">00:00&nbsp;|&nbsp;00:00</div> </div><audio controls="controls" id="audio_" src="/data/71129.wav" >wav</audio></div><div class="container"><div class="bar" style="width:0%;" data-percent="1"></div></div></dd></dl>');
                // $("#chat_msg_html").append('<dl class="msg-me" id="div_"><dt><img src="/data/upload/shop/common/default_user_portrait.gif" alt=""/><i></i></dt><dd><div class="audio"><div onclick="play_sound(this);" class="Play"><span class="icon icon-play"></span></div><div class="Process"> <div class="ProcessAll"></div><div class="ProcessNow"></div><div class="SongTime">00:00&nbsp;|&nbsp;00:00</div> </div><audio controls="controls" id="audio_" src="/data/71129.wav" >wav</audio></div><div class="container"><div class="bar" style="width:0%;" data-percent="1"></div></div></dd></dl>');
                // runloading();
                $("#chat_msg_html").append(html);
            }
            //初始化聊天记录
            /*$('#chat_msg_html').html('');
            $('#wrapper').attr('has_more','true');
            $('#pullDown').removeClass('hide');
            initPage(page,curpage);
            loaded();*/
        });
    }
    var connectNode = function(data){
        nodeSiteUrl = data.node_site_url;
        memberInfo = data.member_info;
        //店长的信息
        to_memberInfo = data.user_info;
        if(typeof to_memberInfo != 'undefined') {
            $('h1').html(to_memberInfo.store_name != '' ? to_memberInfo.member_name : to_memberInfo.store_name);
            t_id = to_memberInfo.member_id;
        }
        resourceSiteUrl = data.resource_site_url;
        if (!data.node_chat) {
            $.sDialog({
                skin:"red",
                content:'在线聊天系统暂时未启用',
                okBtn:false,
                cancelBtn:false
            });
            return false;
        }
        //改为在chat_info.html中手动加载
        /*var script = document.createElement("script");
        script.type = "text/javascript";
        script.src = nodeSiteUrl+'/socket.io/socket.io.js';
        document.body.appendChild(script);*/
        checkIO();
        function checkIO() {
            setTimeout(function(){
                if ( typeof io === "function" ) {
                    connect_node();
                } else {
                    checkIO();
                }
            },500);
        }
        function connect_node() {
            var connect_url = nodeSiteUrl;
            var connect = 0;//连接状态
            var member = {};

            member['u_id'] = memberInfo.member_id;
            member['u_name'] = memberInfo.member_name;
            member['avatar'] = memberInfo.member_avatar;
            member['s_id'] = memberInfo.store_id;
            member['s_name'] = memberInfo.store_name;
            member['s_avatar'] = memberInfo.store_avatar;
            member['t_id'] = to_memberInfo.member_id;
            member['is_seller'] = "0";

            current_socket = io(connect_url, { 'path': '/socket.io', 'reconnection': false, "transports":['websocket', 'polling'] });
            current_socket.on('connect', function () {
                connect = 1;
                current_socket.emit('update_user', member);
                // 在线状态
                /*socket.on('get_state', function (data) {
                    get_state(data);
                });*/
                //接收系统分配的新客服，如果没有分配继续跟当前的店长留言
                console.log("get customer server ...");
               /* current_socket.emit("get_customer_server", {
                    store_id: to_memberInfo.store_id,
                    u_id: memberInfo.member_id
                });
                current_socket.on('set_customer_server', function(data) {
                    t_id = data.member_id;
                    $('h1').html(data.store_name != '' ? data.store_name : data.member_name);
                    to_memberInfo = data;
                });*/

                current_socket.on('get_msg', function (data) {
                    get_msg(data);
                });

                /**
                 * 收到对方发起视频通话
                 */
                current_socket.on('receive_video_chat', function (data) {

                });

                current_socket.on('multiple connected', function(data) {
                    //当前连接退出
                    connect = 0;
                    $.sDialog({
                        skin:"red",
                        content:'您的账号已在其他设备登录，请检查账号安全',
                        okBtn:false,
                        cancelBtn:false
                    });
                    //如果有视频对话，断开视频通话。
                    current_socket.emit("close video chat", {t_id: "" + t_id, f_id: "" + memberInfo.member_id});
                    stopChat();
                    delete current_socket;
                });

                /*socket.on('del_msg', function (data) {
                    del_msg(data);
                });*/
                current_socket.on('disconnect', function () {
                    connect = 0;
                    $.sDialog({
                        skin:"red",
                        content:'您已经与服务器断开连接，请返回重连。',
                        okBtn:false,
                        cancelBtn:false
                    });
                    // 重连
                });
            });

            function node_get_state(data){
               if(connect === 1) {
                   var myArray=new Array();
                   myArray['5'] = 0
                   current_socket.emit('get_state', myArray);
               }
            }
            function node_send_msg(data){
                if(connect === 1) {
                    $.ajax({
                        type:'post',
                        url:ApiUrl+'/index.php?act=member_chat&op=send_msg',
                        data:data,
                        dataType:'json',
                        success: function(result){
                            if (result.code == 200) {
                                var msgData = result.datas.msg;
                                current_socket.emit('send_msg', msgData);
                                msgData.avatar = memberInfo.member_avatar;
                                msgData.class='msg-me';
                                insert_html(msgData);
                            } else {
                                $.sDialog({
                                    skin:"red",
                                    content:result.datas.error,
                                    okBtn:false,
                                    cancelBtn:false
                                });
                                return false;
                            }
                        }
                    });
                }
            }
            // connect to server
            function node_del_msg(max_id, t_id){
                if(connect === 1) {
                    current_socket.emit('del_msg', {'max_id':max_id, 't_id':t_id, 'f_id': "" + memberInfo.member_id});
                }
            }

            // 获取状态
            function get_state(data) {
                node_send_msg('');
            }
            // 接收消息
            function get_msg(data) {
                var max_id;
                for (var k in data){
                    var msgData = data[k];
                    if (data[k].f_id != t_id) {
                        continue;
                    }
                    max_id = k;
                    msgData.avatar = (!$.isEmptyObject(to_memberInfo.store_id)? to_memberInfo.store_avatar : to_memberInfo.member_avatar);
                    msgData.class='msg-other';
                    insert_html(msgData);
                }
                if (typeof(max_id) != 'undefined') {
                    node_del_msg(max_id, t_id);
                }
            }
            // 删除消息
            function del_msg(data) {
            }

            //发送照片 add by matt 20171010
            document.getElementById('img-file').addEventListener('change', function (){
                showLoading();
                var reader = new FileReader();
                var fileSize = Math.round(this.files[0].size/1024/1024) ; //以M为单位
                reader.onload = function (event) {
                    compress(reader.result,fileSize,function(base64_data){
                        $.ajax({
                            type:'post',
                            url:ApiUrl+'/index.php?act=sns_album&op=base64_upload',
                            data:{key:key,imgBase64:base64_data},
                            dataType:'json',
                            success: function(result){
                                node_send_msg({
                                    key: key,
                                    t_id: t_id,
                                    t_name: to_memberInfo.member_name,
                                    t_msg: result.datas.file_url,
                                    chat_goods_id: chat_goods_id,
                                    msg_type: "image/jpeg"
                                });
                                closeLoading();
                            }
                        });
                    });
                };
                reader.readAsDataURL(this.files[0]);
            },true);


            $('#submit').on('touchstart',function(){
                var t_msg = $('#msg').val();
                $('#msg').val('');
                if (t_msg == '') {
                    $.sDialog({
                        skin:"red",
                        content:'请填写内容',
                        okBtn:false,
                        cancelBtn:false
                    });
                    return false;
                }
                node_send_msg({
                    key: key,
                    t_id: t_id,
                    t_name: to_memberInfo.member_name,
                    t_msg: t_msg,
                    chat_goods_id: chat_goods_id,
                    msg_type: "text"
                });
                $('#chat_smile').addClass('hide');
                $('.nctouch-chat-con').css('bottom', '2rem');
            });

            //开始
            $(".send_goods_url").on('touchstart', function(){
                var goods_url = $(".goods-name a").attr("href");
                var goods_name = $(".goods-name a").html();
                var goods_price = $(".goods-price").html();
                var last_msg = $("#msg").val() + goods_url +"&nbsp;"+ goods_name +"&nbsp;"+ goods_price;
                console.log(last_msg);
                $("#msg").val(last_msg).trigger("touchstart");
				$("#submit").trigger("touchstart");

            });
            //结束
        }

        for(var i in smilies_array[1]) {
            var s = smilies_array[1][i];
            var smilieimg = '<img title="'+s[6]+'" alt="'+s[6]+'" data-sign="'+s[1]+'" src="'+resourceSiteUrl+'/js/smilies/images/'+s[2]+'">';
            $('#chat_smile > ul').append('<li>'+smilieimg+'</li>');
        }
        $('.open-audio').on('touchstart', function(){
            if (!($('#more_plus').hasClass('hide'))) {
                $('#more_plus').addClass('hide');
            }
            if (!($('#chat_smile').hasClass('hide'))) {
                $('#chat_smile').addClass('hide');
                $('#open_smile').removeClass('icon-jianpan').addClass('icon-xiaolian');
            }
            var d = $(this).data('flag');
            if(parseInt(d)){
                $('#record').hide();
                $('#msg').show();
                $(this).data('flag',0);
                $(this).removeClass('icon-jianpan').addClass('icon-maikefeng');
            }else{
                $('#msg').hide();
                $('#record').show();
                $(this).data('flag',1);
                $(this).removeClass('icon-maikefeng').addClass('icon-jianpan');
            }

        })
        $('#open_smile').on('touchstart',function(){
            if (!($('#more_plus').hasClass('hide'))) {
                $('#more_plus').addClass('hide');
            }
            $('#record').hide();
            $('#msg').show();
            $('.open-audio').removeClass('icon-jianpan').addClass('icon-maikefeng');
            if ($('#chat_smile').hasClass('hide')) {
                $('#chat_smile').removeClass('hide');
                $('.nctouch-chat-con').css('bottom', '7rem');
                $(this).removeClass('icon-xiaolian').addClass('icon-jianpan');
            } else {
                $('#chat_smile').addClass('hide');
                $('.nctouch-chat-con').css('bottom', '2rem');
                $(this).removeClass('icon-jianpan').addClass('icon-xiaolian');
            }

        });
        $('#open_more').on('touchstart', function(){
            if (!($('#chat_smile').hasClass('hide'))) {
                $('#chat_smile').addClass('hide');
            }
            $('#record').hide();
                $('#msg').show();
            $('.open-audio').removeClass('icon-jianpan').addClass('icon-maikefeng');
            if ($('#more_plus').hasClass('hide')) {
                $('#more_plus').removeClass('hide');
                $('.nctouch-chat-con').css('bottom', '7rem');
            } else {
                $('#more_plus').addClass('hide');
                $('.nctouch-chat-con').css('bottom', '2rem');
            }

        });
        $('#chat_smile').on('touchstart', 'img', function(){
            var _sign = $(this).attr('data-sign');
            var dthis = $('#msg')[0];
            var start = dthis.selectionStart;
            var end = dthis.selectionEnd;
            var top = dthis.scrollTop;
            dthis.value = dthis.value.substring(0, start) + _sign + dthis.value.substring(end, dthis.value.length);
            dthis.setSelectionRange(start + _sign.length, end + _sign.length);
        });

        // 查看更多聊天记录
        $('#chat_msg_log').on('touchstart',function(){
            $('#chat_msg_html').html('');
            $('#wrapper').attr('has_more','true');
            $('#pullDown').removeClass('hide');
            initPage(page,curpage);
            loaded();

        });


        function insert_html(msgData) {
            msgData.t_msg = update_chat_msg(msgData.t_msg, msgData.msg_type);
            var time=new Date().Format("yyyy-MM-dd HH:mm:ss");
            var html = '<dl class="msg-time">'+time+'</dl><dl class="'+msgData.class+'"><dt><img src="' + msgData.avatar + '" alt=""/><i></i></dt><dd>'+msgData.t_msg+'</dd></dl>';
            $("#chat_msg_html").append(html);
            if (!$.isEmptyObject(msgData.goods_info)) {
                var goods = msgData.goods_info;
                var html = '<div class="nctouch-chat-product"> <a href="' + WapSiteUrl + '/tmpl/product_detail.html?goods_id=' + goods.goods_id + '" target="_blank"><div class="goods-pic"><img src="' + goods.pic36 + '" alt=""/></div><div class="goods-info"><div class="goods-name">' + goods.goods_name + '</div><div class="goods-price">￥' + goods.goods_promotion_price + '</div></div></a> </div>';
                $("#chat_msg_html").append(html);
            }
            $("#anchor-bottom")[0].scrollIntoView();
        }


        function run(){
          $('.container').each(function(index,val){
              var bar = $(this).find('.bar');
              var width = $(this).width();
              var twidth = bar.data('percent')*width;
              if(bar.width()<twidth){
                  bar.css('width',parseInt(bar.css('width'))+1+'%');
              }
              if(bar.css('width') == "100%"){
                window.clearTimeout(timeout);
                $(this).remove();
                return;
              }
              var timeout=window.setTimeout("run()",100);
          })
        }
        run();
    }
});
function AutoResizeImage(maxWidth,maxHeight,objImg){
    var img = new Image();
    img.src = objImg.src;
    var hRatio;
    var wRatio;
    var Ratio = 1;
    var w = img.width;
    var h = img.height;
    wRatio = maxWidth / w;
    hRatio = maxHeight / h;
    if (maxWidth ==0 && maxHeight==0){
        Ratio = 1;
    }else if (maxWidth==0){//
        if (hRatio<1) Ratio = hRatio;
    }else if (maxHeight==0){
        if (wRatio<1) Ratio = wRatio;
    }else if (wRatio<1 || hRatio<1){
        Ratio = (wRatio<=hRatio?wRatio:hRatio);
    }
    if (Ratio<1){
        w = w * Ratio;
        h = h * Ratio;
    }
    objImg.height = h;
    objImg.width = w;
}
$('#chat_msg_html').on('touchstart', 'dl dd img', function(){
    if (window.platformId == 'ios' && window.WebViewJavascriptBridge) {
        window.call_native('scalimg',{'img':$(this).attr('src')});
    } else {
        var img = new Image();
        img.src = $(this).attr('src');
        if(isWeiXin()){
            WeixinJSBridge.invoke('imagePreview', {
                'current': $(this).attr('src'),
                'urls': [$(this).attr('src')]
            });
        }else{
            AutoResizeImage(window.screen.width,window.screen.height,img);
            $(img).css('margin-left','-'+img.width/2+'px').css('margin-top','-'+img.height/2+'px');
            $(".propo_masker").append($(img));
            $(".propo_masker").removeClass('hide');
        }
    }
});
$(".propo_masker").on('touchstart',function(){
    $(this).addClass('hide').html('');
})

function play_sound(obj){
    var audio = $(obj).parent().find('audio')[0];
    if(audio.paused){
        if($(obj).children().hasClass('icon-play') ) {
            $(obj).children('span').removeClass("icon-play").addClass("icon-pause");
            Play(audio,obj);
        }
    }
    else{
        $(obj).children('span').removeClass("icon-pause").addClass("icon-play");
        Pause(audio);
    }
}

function Play(audio,obj) {
    audio.play();
    TimeSpan(audio,obj);
} //Play()

function Pause(audio) {
    audio.pause();
} //Pause()

function TimeSpan(audio,obj) {
    var ProcessNow = 0;
    var clock = setInterval(function () {
        var ProcessNow = (audio.currentTime / audio.duration) * 140;
        $(obj).next().find(".ProcessNow").css("width", ProcessNow);
        var currentTime = timeFormat(audio.currentTime);
        var timeAll = timeFormat(TimeAll(audio));
        $(obj).next().find(".SongTime").html(currentTime + " | " + timeAll);
        if(currentTime==timeAll){
            clearInterval(clock);
            $(obj).children('span').removeClass("icon-pause").addClass("icon-play");
            $(obj).next().find(".ProcessNow").css("width", '0px');
        }
    }, 1000);
}  //TimeSpan()

function timeFormat(number) {
    var minute = parseInt(number / 60);
    var second = parseInt(number % 60);
    minute = minute >= 10 ? minute : "0" + minute;
    second = second >= 10 ? second : "0" + second;
    return minute + ":" + second;
} //timeFormat()

function TimeAll(audio) {
    return audio.duration;
} //TimeAll()
