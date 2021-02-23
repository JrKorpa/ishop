var app = require('express')();
var server = require('http').createServer(app);
var webRTC = require('webrtc.io').listen(server);

var port = process.env.PORT || 3001;
server.listen(port);



app.get('/webrtc', function(req, res) {
    res.sendFile(__dirname + '/webrtc.html');
});

app.get('/webrtc/index.html', function(req, res) {
    res.sendFile(__dirname + '/webrtc.html');
});

app.get('/rtc-style.css', function(req, res) {
    res.sendFile(__dirname + '/public/rtc-style.css');
});

app.get('/fullscrean.png', function(req, res) {
    res.sendFile(__dirname + '/public/fullscrean.png');
});

app.get('/script.js', function(req, res) {
    res.sendFile(__dirname + '/public/script.js');
});

app.get('/webrtc.io.js', function(req, res) {
    res.sendFile(__dirname + '/public/webrtc.io.js');
});

webRTC.rtc.on('chat_msg', function(data, socket) {
    var roomList = webRTC.rtc.rooms[data.room] || [];

    for (var i = 0; i < roomList.length; i++) {
        var socketId = roomList[i];

        if (socketId !== socket.id) {
            var soc = webRTC.rtc.getSocket(socketId);

            if (soc) {
                soc.send(JSON.stringify({
                    "eventName": "receive_chat_msg",
                    "data": {
                        "messages": data.messages,
                        "color": data.color
                    }
                }), function(error) {
                    if (error) {
                        console.log(error);
                    }
                });
            }
        }
    }
});