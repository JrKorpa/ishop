// Setup basic express server
var express = require('express');
var app = express();
var fs = require("fs");
var _config = require('./config');
var _test_local_conf = false;
//fs.exists(__dirname + "/config-local.js", function() {
//    var _localConfig = require("./config-local");
//    _config = jsonMerge(_config, _localConfig);
//    console.log(_config);
//    _test_local_conf = true;
//});

try {
    _config = require("./config-local");
}
catch(e) {
    _config = require("./config");
}
console.info(_config);

//改为Apache代理ssl
/*var privateKey  = fs.readFileSync(_config.config.ssl_key_file, 'utf8');
var certificate = fs.readFileSync(_config.config.ssl_crt_file, 'utf8');
var credentials = {key: privateKey, cert: certificate};
var httpsServer = require("https").createServer(credentials, app);
var ios = require('./lib/index')(httpsServer);*/

var server = require('http').createServer(app);
var io = require('./lib/index')(server);

var db = require('./lib/db');
var lib_user = require('./lib/users');

var port = process.env.PORT || _config.port;
var cache = require("./lib/cache");
var url = require("url");
app.post("/join/video-chat", function(req,res){
    res.send("hello world");
});

server.listen(port, function () {
    console.log('Server listening on port: %d', port);
});
/*httpsServer.listen(_config.sslport, function() {
    console.log('HTTPS Server is running on port: %s', _config.sslport);
});*/

// Routing
app.use(express.static(__dirname + '/public'));

/*app.get('/.well-known/pki-validation/fileauth.txt', function(req, res) {
    res.send('201709180000005kkmmbbwlx1mqurao7oj4vww6nx9ri4bven39k4xsslkj3y0zt');
});*/

var hostname = _config.hostname;
var clientsUserOnlineStatus = {};
var socketsUserMap = {};
var userSocketMap = {};
function getIPAddress(){
    var interfaces = require('os').networkInterfaces();
    for(var devName in interfaces){
        var iface = interfaces[devName];
        for(var i=0;i<iface.length;i++){
            var alias = iface[i];
            if(alias.family === 'IPv4' && alias.address !== '127.0.0.1' && !alias.internal){
                return alias.address;
            }
        }
    }
}

function jsonMerge(target) {
    var sources = [].slice.call(arguments, 1);
    sources.forEach(function (source) {
        for (var prop in source) {
            target[prop] = source[prop];
        }
    });
    return target;
}

io.set('log level', 0);
io.set('origins', '*:*');
io.set('resource', '/resource');
io.set('transports', ['websocket', 'xhr-polling', 'jsonp-polling', 'htmlfile', 'flashsocket', 'polling']);
io.set('authorization', authorizationHandler);
io.on('connect', socketRequestHandler);

/*ios.set('log level', 0);
ios.set('origins', '*:*');
ios.set('resource', '/resource');
ios.set('transports', ['websocket', 'xhr-polling', 'jsonp-polling', 'htmlfile', 'flashsocket', 'polling']);
ios.set('authorization', authorizationHandler);
ios.on('connect', socketRequestHandler);*/

function authorizationHandler(handshakeData, cb) {
    // console.log(handshakeData);
    var domain = hostname;
    var origin = handshakeData.headers.origin || handshakeData.headers.referer;
    var parts = url.parse(''+origin);
    var re = new RegExp(domain+"$","g");
    var arr = re.exec(parts.hostname);
    if ( domain === "" ) {
        cb(null, true);
    } else if ( arr !== null ) {
        cb(null, true);
    } else {
        if(parts.hostname == '127.0.0.1' || parts.hostname == getIPAddress()) {
            cb(null, true);
        }
        var dt = new Date();
        console.log(dateToString(dt)+' '+parts.hostname+' handshake unauthorized');
        cb(null, true);
    }
}

function socketRequestHandler(socket, data) {
    socket.online = true;
    console.log('Server received a connect request');
    console.log('connect data:');
    console.log(data);
    socket.on('update_user', function (user) {// 更新用户信息
        var u_id = user['u_id'];
        var s_id = user['s_id'];
        console.log("update user for user: %s", u_id);
        // socket.set('u_id', u_id);
        socketsUserMap[socket.id] = u_id;

        //如果用户有旧socket,需要通知对应的登录设备，如果是已经主动断开，则不用理会；销毁旧socket
        oldSocket = userSocketMap[u_id];
        if(oldSocket && oldSocket.online) {
            oldSocket.emit("multiple connected", null);
            oldSocket.online = false;
            oldSocket.emit("disconnect", null);
            delete oldSocket;
        }
        
        //缓存当前登录人员的socket对象
        userSocketMap[u_id] = socket;
        socket.join('user_' + u_id);
        //　共享用户在线状态
        userOnline(u_id);
        update_user(user, function () {
            lib_user.set_user_info(u_id, 'online', 1);
            lib_user.set_user_info(u_id, 's_id', user['s_id']);
            lib_user.set_user_info(u_id, 's_name', user['s_name']);
            lib_user.set_user_info(u_id, 'avatar', user['avatar']);
        });
    });

    // when the client emits 'new message', this listens and executes
    socket.on('send_msg', function (msg) {
        console.log('Server received message');
        console.log(msg);
        var t_id = msg['t_id'];
        var u_id = msg['f_id'];
        // sort member id asc
        var msg_key = t_id > u_id ? 'room_' + u_id + '-' + t_id : 'room_' + t_id + '-' + u_id;

        if(userIsOnline(t_id)) {
            var user_info  = lib_user.get_user(t_id);
            if (user_info) {//如果找到该客户，则给其发送消息
                var m_id = msg['m_id'];
                var msg_list = {};
                msg['online'] = 1;
                msg_list[m_id] = msg;
                socket.in('user_' + t_id).emit('get_msg', msg_list);
            }
        }
        lib_user.set_msg(msg_key, msg);
    });

    //seller login in pc will check for all talked use connecting status
    socket.on('get_state', function (u_state) {//获取连接状态
        console.log("reveive a  get_state request");
        console.log(u_state);
        var list = {};
        var user_list = {};
        for (var k in u_state) {
            var user_info  = lib_user.get_user(k);
            console.info(k);
            console.info(user_info);
            user_info['online'] = 0;
            if(typeof user_info != 'undefined') {
                if(userIsOnline(user_info['u_id'])) {
                    u_state[k] = 1;
                    user_info['online'] = 1;
                }
                if (user_info['u_id'] > 0) user_list[k] = user_info;
            }
        }
        list['u_state'] = u_state;
        list['user'] = user_list;
        console.log(list);
        socket.emit('get_state', list);
    });

    socket.on('del_msg', function (msg) {//删除消息
        var max_id = msg['max_id'];//获取最大的消息ＩＤ
        var f_id = msg['f_id'];//获取要删除的消息ID
        var u_id = msg['m_id'];
        if(userIsOnline(f_id)) {
            // sort member id asc
            var msg_key = f_id > u_id ? 'room_' + u_id + '-' + f_id : 'room_' + f_id + '-' + u_id;
            var list = lib_user.get_msg(msg_key);
            if(list && list[max_id]) {
                if (typeof list[max_id] === "object") {//判断消息是否为复合消息，如果是，需要迭代删除
                    for (var k in list) {
                        var m_id = list[k]['m_id'];
                        var f = list[k]['f_id'];
                        if (max_id > m_id && f_id == f) {
                            lib_user.del_msg(msg_key, m_id);
                        }
                    }
                    if (userIsOnline(u_id)) {//如果已经删除成功，需要通知客户端同步删除
                        socket.in('user_' + u_id).emit('del_msg', msg);
                    }
                }
                lib_user.del_msg(msg_key, max_id);//删除主消息体
            }
            db.del_msg(' t_id = ' + u_id + ' AND f_id = ' + f_id + ' AND m_id < ' + max_id);
            var v = {};
            v['r_state'] = 1;//设置状态
            db.update_msg(' t_id = ' + u_id + ' AND f_id = ' + f_id + ' AND m_id = ' + max_id, v);
        }
    });

    // when the client emits 'typing', we broadcast it to others
    socket.on('typing', function () {
        socket.in('user_' + t_id).emit('get_msg', msg_list);
        socket.emit('typing', {
            username: socket.username
        });
    });

    // when the client emits 'stop typing', we broadcast it to others
    socket.on('stop typing', function () {
        socket.in('user_' + t_id).emit('stop typing', {
            username: socket.username
        });
    });

    // when the user disconnects.. perform this
    socket.on('disconnect', function () {
        console.log('user disconnect to im server.');
        var u_id = socketsUserMap[socket.id];
        console.log("get u_id from socket:" + u_id);
        var user_info  = lib_user.get_user(u_id);
        if(user_info) {
            var dt = new Date();
            var update_time = dt.getTime();
            lib_user.set_user_info(u_id, 'disconnect_time', update_time);
            lib_user.set_user_info(u_id, 'online', 0);
            var s_id = user_info['s_id'];
            userOffline(u_id);
            var t_id = socket.get("video_chat_cp");
            socket.in('user_' + t_id).emit('close video chat', {});
        };
        socket.online = false;
        socket.leave("user_" + u_id);
    });

    // video chat handler
    socket.on('send offer', function(data) {
        console.log("send offer");
        console.log(data);
        var t_id = data['t_id'];
        var u_id = data['f_id'];
        var attached = false;
        if(userIsOnline(t_id)) {
            var user_info  = lib_user.get_user(t_id);
            if (user_info) {//如果找到该客户，则给其发送消息
                attached = true;
                socket.in('user_' + t_id).emit('receive offer', data.sdp);
                socket.set('video_chat_cp', t_id);
            }
        }
        if(!attached) {
            socket.in('user_' + u_id).emit('offer failed', {});
        }
    });

    socket.on("refused offer", function(data) {
        console.log("refused offer");
        console.log(data);
        var t_id = data['t_id'];
        var u_id = data['f_id'];
        var attached = false;
        if(userIsOnline(t_id)) {
            var user_info  = lib_user.get_user(t_id);
            if (user_info) {//如果找到该客户，则给其发送消息
                attached = true;
                socket.in('user_' + t_id).emit('refused offer', {});
            }
        }
        if(!attached) {
            //nothing to do
        }
    });

    socket.on("send answer", function(data) {
        console.log("send answer");
        console.log(data);
        var t_id = data['t_id'];
        var u_id = data['f_id'];
        var attached = false;
        if(userIsOnline(t_id)) {
            var user_info  = lib_user.get_user(t_id);
            if (user_info) {//如果找到该客户，则给其发送消息
                attached = true;
                socket.in('user_' + t_id).emit('receive answer', data.sdp);
                cache.str_set("video_chat_cp", t_id);
            }
        }
        if(!attached) {
            socket.in('user_' + u_id).emit('answer failed', {});
        }
    });

    socket.on("send candidate", function(data) {
        console.log("send candidate");
        console.log(data);
        if(data == null) {
            return false;
        }
        var t_id = data['t_id'];
        var u_id = data['f_id'];
        var attached = false;
        if(typeof data.candidate != 'undefined' && data.candidate != null && userIsOnline(t_id)) {
            var user_info  = lib_user.get_user(t_id);
            if (user_info) {//如果找到该客户，则给其发送消息
                attached = true;
                socket.in('user_' + t_id).emit('receive candidate', data.candidate);
            }
        }
        if(!attached) {
            socket.in('user_' + u_id).emit('candidate transfer failed', {});
        }
    });

    socket.on("close video chat", function(data) {
        var t_id = data['t_id'];
        var u_id = data['f_id'];
        var attached = false;
        if(userIsOnline(t_id)) {
            var user_info  = lib_user.get_user(t_id);
            if (user_info) {//如果找到该客户，则给其发送消息
                attached = true;
                socket.in('user_' + t_id).emit('close video chat', {});
            }
        }
        if(!attached) {
            //nothing to do
        }
    });
}



function userOnline(u_id) {
    if(u_id == null) {
        return false;
    }
    console.log("set user" + u_id + " online status as true")
    clientsUserOnlineStatus[u_id] = true;
    console.log("user online status list:");
    console.log(clientsUserOnlineStatus);
    cache.str_set("online_status_" + u_id, "1");
}

function userOffline(u_id) {
    console.log("set user " + u_id + " online status as false")
    clientsUserOnlineStatus[u_id] = false;
    console.log("user online status list:");
    console.log(clientsUserOnlineStatus);
    cache.str_set("online_status_" + u_id, "0");
}

function userIsOnline(u_id) {
    console.log("user online status list:");
    console.log(clientsUserOnlineStatus);
    return clientsUserOnlineStatus[u_id];
}


/**
 * 更新登录用户信息
 * @param user
 * @param callback
 */
function update_user(user, callback) {
    var u_id = user['u_id'];
    var user_info = {};
    var dt = new Date();
    var update_time = dt.getTime();
    user_info = lib_user.get_user(u_id);
    if (typeof user_info === "undefined" || typeof user_info['u_id'] === "undefined") {
        lib_user.set_user(user);
        lib_user.set_user_info(u_id, 'update_time', update_time);// 设置最后更新时间
        var db_query = ' t_id = ' + u_id + ' AND r_state = 2';
        db.get_msg_list(db_query, function (list) {// 获取消息历史
            for (var k in list) {
                list[k]['add_time'] = dateToString(list[k]['add_time'] * 1000);
                lib_user.set_msg(u_id, list[k]);
            }
            callback();
        });
    } else {
        lib_user.set_user_info(u_id, 'update_time', update_time);// 设置最后更新时间
        callback();
    }
}


function inArray(val, arr) {
    for(var k in arr) {
        if(val == arr[k]) {
            return true;
        }
    }
    return false;
}

/**
 * 日期转为字符串
 * @param date
 * @returns {string}
 */
function dateToString(date) {
    var dt = new Date(date);
    var year = dt.getFullYear();
    var month = zeroPad(dt.getMonth() + 1);
    var day = zeroPad(dt.getDate());
    var hour = zeroPad(dt.getHours());
    var minute = zeroPad(dt.getMinutes());
    var second = zeroPad(dt.getSeconds());

    return year + '-' + month + '-' + day + ' ' + hour + ':' + minute + ':' + second;
}

/**
 * 数字位数补零
 * @param number
 * @returns {string}
 */
function zeroPad(number) {
    return (number < 10) ? '0' + number : number;
}
