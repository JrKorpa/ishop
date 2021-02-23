var user_list = new Array();//会员的信息

var msg_list = new Array();//所有未读消息的二维数组['u_id']['m_id']
var userListPrefix = "chat_user_list_";
var msgListPrefix = "chat_msg_list_";

var c = console.log;

var _redis_ = null;

exports.set_user = set_user;
exports.set_user_info = set_user_info;
exports.get_user = get_user;

exports.set_msg = set_msg;
exports.get_msg = get_msg;
exports.del_msg = del_msg;

function set_user(user) {//更新会员的连接信息
    var u_id = user['u_id'];
    if (typeof u_id != "undefined") {
        var cacheMsgList = get_msg(u_id);
        if (!cacheMsgList || typeof cacheMsgList === "undefined") set_msg(u_id, {});
    } 
    user_list[u_id]    = user;
}

function set_user_info(u_id, k, v) {//设置会员信息
    var cachedUser = get_user(u_id);
    if (cachedUser && typeof cachedUser === "object") {
        cachedUser[k] = v;
    }
    else {
        cachedUser['u_id'] = u_id;
        cachedUser[k] = v;
    }
    set_user(cachedUser);
}


function get_user(u_id) {//会员信息
    if(typeof user_list[u_id] != "undefined") {
        return user_list[u_id];
    }
    return {};
}

function set_socket(u_id, socket) {//设置会话
    socket.join('user_' + u_id);
    socket.set('u_id', u_id);
}

function set_msg(u_id, msg) {//添加消息
    var m_id = msg['m_id'];
    var cachedMsgList = get_msg(u_id);
    if (!cachedMsgList || typeof cachedMsgList === "undefined") cachedMsgList = {};
    cachedMsgList[m_id] = msg;
    msg_list[u_id] = cachedMsgList;
}

function get_msg(u_id) {//会员的消息
    if(typeof msg_list[u_id] != "undefined") {
        return msg_list[u_id];
    }
    return {};
}

function del_msg(u_id, m_id) {//删除消息
    var cachedMsgList = get_msg(u_id);
        if(cachedMsgList && typeof cachedMsgList[m_id] != "undefined") {
            delete cachedMsgList[m_id];
        }
        else if(!cachedMsgList) {
            cachedMsgList = {};
        }
        msg_list[u_id] = cachedMsgList;
}