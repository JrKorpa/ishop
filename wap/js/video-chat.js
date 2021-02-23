/*
 *  Copyright (c) 2015 The WebRTC project authors. All Rights Reserved.
 *
 *  Use of this source code is governed by a BSD-style license
 *  that can be found in the LICENSE file in the root of the source
 *  tree.
 */

'use strict';

/**
 * const defination
 */

 var CHAT_TYPE_CALL = 1;
 var CHAT_TYPE_ANSWER = 2;
 var CHAT_STATUS_NOTHING = 0;
 var CHAT_STATUS_CONNECTING = 1;
 var CHAT_STATUS_CONNECTED = 2;
 var CHAT_STATUS_CLOSED = 3;

$('#open_video_chat').on("touchstart", function(e) {
    if(e.preventDefault) {
        e.preventDefault();
    }
    else {
        e.returnValue = false;
    }
    e.stopPropagation();

    startChat(CHAT_TYPE_CALL, function () {
        $('#videos').show();
        $("#normal-chat-screen").hide();
        $('#video-chat-screen').show();
        $("#normal-chat-bottom").hide();
        $('#icons').removeClass("hidden").addClass("active");
    });
});

$('#hungup-video').on('touchstart', stopChat);

var localVideo = document.getElementById('local-video');
var remoteVideo = document.getElementById('remote-video');
var miniVideo = document.getElementById('mini-video');

var peerConn = null;
var localStream = null;
var offerOptions = {
    offerToReceiveAudio: 1,
    offerToReceiveVideo: 1
};

//　１　主动发起　２　被动呼叫
var chatType = CHAT_TYPE_CALL;
//　０　未通话　１　请求中　３　通话中
var chatStatus = CHAT_STATUS_NOTHING;

var localSdp = null;
var remoteSdp = null;

var remoteCandidates = [];
var isAddedRemoteCandidate = false;

var initFailedCount = 0;
var isOnline = false;

window.AudioContext = window.AudioContext || window.webkitAudioContext || window.mozAudioContext || window.msAudioContext;

function initialize() {
    $("#hangup").click(function(e) {
        stopChat();
    });

    $("#mute-audio").click(function (e) {
        pauseAudio();
    });

    $("#mute-video").click(function (e) {
        pauseVideo();
    })
        
    initSocketEvents();
}

initialize();

function getUserMediaAdapter(success, failed) {
    /*var getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
    getUserMedia({video: true, audio: true}, success, failed);*/
    var mediaConfig = {video: true, audio: true};
    if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia(mediaConfig).then(success, failed);
    }
    else if(navigator.getUserMedia) { // Standard
        navigator.getUserMedia(mediaConfig, success, failed);
    } else if(navigator.webkitGetUserMedia) { // WebKit-prefixed
        navigator.webkitGetUserMedia(mediaConfig, success, failed);
    } else if(navigator.mozGetUserMedia) { // Mozilla-prefixed
        navigator.mozGetUserMedia(mediaConfig, success, failed);
    }
}

function initSocketEvents() {
    if(initFailedCount > 20) {
        alert("sorry, can't get the remote server.");
        stopChat();
        isOnline = false;
        return false;
    }
    //　check the socket connection status
    if (typeof current_socket == 'undefined' || current_socket == null) {
        console.info("IM server didn't connected yet!");
        initFailedCount ++;
        setTimeout(initSocketEvents, 1000);
        return false;
    }
    isOnline = true;
    // called by other peer
    current_socket.on("receive offer", function(data) {
        remoteSdp = data;
        console.info("socket: received remote offer");
        startChat(CHAT_TYPE_ANSWER, function () {
            $('#videos').show();
            $("#normal-chat-screen").hide();
            $('#video-chat-screen').show();
            $("#normal-chat-bottom").hide();
            $('#icons').removeClass("hidden").addClass("active");
        });
    });

    current_socket.on("receive answer", function(data) {
        console.info('socket: receive remote answer');
        remoteSdp = data;
        onReceivedAnswer(data);
    });

    current_socket.on("receive candidate", function(data) {
        console.info('socket: receive remote candidate');
        remoteCandidates.push(data);
        onReceivedCandidate();
    });

    current_socket.on('close video chat', function(data) {
        if(chatStatus == CHAT_STATUS_CONNECTED || chatStatus == CHAT_STATUS_CONNECTING) {
            stopChat();
        }
    });

    current_socket.on('offer failed', function() {
        alert("remote user not online, please try send text message!");
        stopChat();
    });
}

/**
 * 无论是主叫还是被叫，首先打开自己的视频，并绑定视频流到视频播放器。
 * 如果是主叫，创建offer并发送信令给对方。
 * 如果是被叫，接受远程信令并创建answer回给对方；绑定远程视频流到视频播放器。
 * @param chatType 通话方式
 */
function startChat(chat_type, onSuccess) {
    if(!isOnline) {
        alert("对方不在线，无法发起视频对话，您可以尝试发送文字或者图片留言");
        return false;
    }
    chatType = chat_type;
    console.log('start video: ' + chat_type);
    if(chatType == CHAT_TYPE_CALL) {
        calling();
    }
    else if(chatType == CHAT_TYPE_ANSWER) {
        onReceivedOffer();
    }

    if(typeof onSuccess == "function") {
        onSuccess();
    }
}

function startMediaCapture() {
    if(localStream != null) {
        onGotLocalMedia(localStream);
    }
    else {
        //get local video stream and set as source to the h5 video player
        /**
          * video: 是否接受视频流
          * audio：是否接受音频流
          * MinWidth: 视频流的最小宽度
          * MaxWidth：视频流的最大宽度
          * MinHeight：视频流的最小高度
          * MaxHiehgt：视频流的最大高度
          * MinAspectRatio：视频流的最小宽高比
          * MaxAspectRatio：视频流的最大宽高比
          * MinFramerate：视频流的最小帧速率
          * MaxFramerate：视频流的最大帧速率
          */
        /*navigator.mediaDevices.getUserMedia({
            audio: true,
            video: true
        })
        .then(onGotLocalMedia, onGetLocalMediaFailed)
        .catch(function (e) {
            console.info('无法获取您的视频设备！' + e.message);
        });*/
        try {
            getUserMediaAdapter(onGotLocalMedia, onGetLocalMediaFailed);
        }
        catch (e) {
            console.log(e);
        }
    }
}

function onGetLocalMediaFailed(e) {
    alert("对不起，您的设置不支持视频，对方将不能看到您的视频信息。");
    onGotLocalMedia(null);
}

function onGotLocalMedia(stream) {
    if(stream) {
        console.info('local stream');
        localStream = stream;
        console.info(localStream);
        filterAudio(localStream);
        localVideo.srcObject = localStream;
    }

    //自建stunserver
    var servers =　{'iceServers': [{'url': 'stun:www.haoduobu.com', 'urls': 'stun:www.haoduobu.com'}]};
    peerConn = new RTCPeerConnection(servers);
    console.info('Created local peer connection object localPeerConn');

    //绑定事件监控candidate发生变化的处理
    peerConn.onicecandidate = function (e) {
        checkPeerConnStatus("ice candidate created");
        onIceCandidate(e);
    };
    if(stream) {
        peerConn.addStream(stream);
    }

    peerConn.onaddstream = function(e) {
        checkPeerConnStatus("received remote stream and try to handler it");
        console.info(e);
        if (remoteVideo.srcObject !== e.stream) {
            filterAudio(e.stream);
            remoteVideo.srcObject = e.stream;
            $(remoteVideo).css('opacity', 1);
            $(localVideo).css('opacity', 0);
            localVideo.srcObj = null;
            miniVideo.srcObject = localStream;
            $(miniVideo).css("opacity", 1);
            $(miniVideo).css("z-index", 1);
            console.info('Received remote stream');
        }
    }

    peerConn.oniceconnectionstatechange = function() {
        checkPeerConnStatus("ice connection state changed");
    }
    
    peerConn.onnegotiationneeded = function() {
        checkPeerConnStatus("negotiation needed");
    }
    peerConn.onpeeridentity = function() {
        checkPeerConnStatus("peer identity");
    }
    peerConn.onsignalingstatechange = function() {
        checkPeerConnStatus("signaling state changed");
    }

    console.info('Listening add Stream to peer connection');
    console.info('Adding Local Stream to peer connection');
    // when received other peer video stream, we add show it to the player
    
    if(chatType == CHAT_TYPE_CALL) {
        checkPeerConnStatus("before create offer");
        peerConn.createOffer(
            offerOptions
        ).then(
            function(offer) {
                console.info("create offer sdp");
                console.info(offer);
                checkPeerConnStatus("success create offer, before set to local sdp");
                localSdp = offer;
                var sendOfferData = {t_id: "" + t_id, f_id: "" + memberInfo.member_id, sdp: offer}
                current_socket.emit("send offer", sendOfferData);
                console.info("send offer data to remote peer");
                console.info(sendOfferData);
                peerConn.setLocalDescription(offer).then(function() {
                    console.info('set localt sdp success');
                    checkPeerConnStatus("set offer as local sdp");
                }, function(e) {
                    console.info(e)
                });
            },
            onCreateSessionDescriptionError
        );
        
    }
    else if(chatType == CHAT_TYPE_ANSWER  && remoteSdp != null) {
        checkPeerConnStatus("received remote offer, before set as remote sdp;");
        var sdp = new RTCSessionDescription(remoteSdp);
        console.info(sdp);
        peerConn.setRemoteDescription(sdp).then(function() {
            checkPeerConnStatus("after set remote sdp, before create local answer");
            peerConn.createAnswer().then(
                function(answer) {
                    checkPeerConnStatus("before set local answer as local sdp");
                    peerConn.setLocalDescription(answer).then(function() {
                        checkPeerConnStatus("after set local answer as local sdp");
                        var sendAnswerData = {t_id: "" + t_id, f_id: "" + memberInfo.member_id, sdp: answer}
                        current_socket.emit("send answer", sendAnswerData);
                        console.info("socket send answer: ");
                        console.info(sendAnswerData);
                    }, function(e) {
                        console.info('failed: created answer and set as local sdp;');
                    });
                },
                onCreateSessionDescriptionError
            );
            chatStatus = CHAT_STATUS_CONNECTED;
            onReceivedCandidate();
        }, function(e) {
            console.info("failse: set received offer sdp as remote sdp for peer");
        });
    }
}

function calling() {
    startMediaCapture();
}

//accept an video chat call
function accept() {
    startMediaCapture();
}

function refuse() {
    console.info('Refused Call' + '\n\n');
    current_socket.emit("refused offer", {});
    stopChat();
}

function stopChat() {
    console.info('Ending Call' + '\n\n');
    $('#normal-chat-screen').show();
    $('#video-chat-screen').hide();
    $("#normal-chat-bottom").show();
    $('#video-chat-bottom').hide();
    if(localStream) {
        localStream.getTracks().forEach(function(track) {
            track.stop();
        });
    }
    localVideo.srcObject = null;
    remoteVideo.srcObject = null;
    miniVideo.srcObject = null;
    localStream = null;
    if(typeof current_socket != 'undefined' && current_socket != null) { 
        current_socket.emit("close video chat", {t_id: "" + t_id, f_id: "" + memberInfo.member_id});
    }
    chatStatus = CHAT_STATUS_CLOSED;
}

//禁止/恢复音频输出
function pauseAudio() {
    var audioTracks = localStream.getAudioTracks();
    if (audioTracks.length === 0) {
        console.info('No local audio available.');
        return;
    }

    console.info('Toggling audio mute state.');
    for (var i = 0; i < audioTracks.length; ++i) {
        audioTracks[i].enabled = !audioTracks[i].enabled;
    }
}

//禁止/恢复视频输出
function pauseVideo() {
    var videoTracks = localStream.getVideoTracks();
    if (videoTracks.length === 0) {
        console.info('No local audio available.');
        return;
    }

    console.info('Toggling audio mute state.');
    for (var i = 0; i < videoTracks.length; ++i) {
        videoTracks[i].enabled = !videoTracks[i].enabled;
    }
}



function filterAudio(stream) {
    var audioCtx = new AudioContext();
    var analyser = audioCtx.createAnalyser();
    var distortion = audioCtx.createWaveShaper();
    var gainNode = audioCtx.createGain();

    var filter = audioCtx.createBiquadFilter();
    
    var convolver = audioCtx.createConvolver();

    var source = audioCtx.createMediaStreamSource(stream);
    source.connect(analyser);
    analyser.connect(distortion);

    distortion.connect(filter);

    filter.connect(convolver);

    convolver.connect(gainNode);
    gainNode.connect(audioCtx.destination);


    filter.type = "bandpass";
    filter.frequency.value = 20;
    // filter.gain.value = 0;
    filter.Q.value = 100;
    filter.detune.value = 1;
}

function onReceivedCandidate() {
    if(peerConn == null) {
        setTimeout(onReceivedCandidate, 500);
        return false;
    }
    else {
        //caller has to wait the remoteDescription setted.
        if(chatType == CHAT_TYPE_CALL) {
            var rsdp = peerConn.remoteDescription;
            if(typeof rsdp == 'undefined' || rsdp == null) {
                checkPeerConnStatus("received a remote candidate but local peer didn't set a remote sdp.");
                setTimeout(onReceivedCandidate, 500);
                return;
            }
        }
        checkPeerConnStatus("received a remote candidate and try to set to local peer");
        var candidateData = remoteCandidates.pop();
        if(!candidateData) {
            return false;
        }
        var candidate = new RTCIceCandidate(candidateData);
        console.info(candidate);
        peerConn.addIceCandidate(candidate)
        .then(
             function() {
                checkPeerConnStatus("set remote candidate successful");
             },
             onAddIceCandidateError
        );
        if(remoteCandidates.length > 0) {
            setTimeout(onReceivedCandidate, 500);
            return;
        }
    }
}

function onReceivedOffer() {
    //@todo 
    // raise an accept button
    // popup a received tip button
    $('#icons').show();
    $('#hangup').show();

    //auto accept
    accept();
}

function onReceivedAnswer(desc) {
    chatStatus = CHAT_STATUS_CONNECTED;
    // Provisional answer, set a=inactive & set sdp type to pranswer.
    /*desc.sdp = desc.sdp.replace(/a=recvonly/g, 'a=inactive');
    desc.type = 'pranswer';*/
    checkPeerConnStatus("received remote answer, before set as remote sdp");
    var sdp = new RTCSessionDescription(desc);
    peerConn.setRemoteDescription(sdp).then(function() {
        console.info('set remote sdp success');
        checkPeerConnStatus("received remote answer data, and set it as remote sdp successful");
        onReceivedCandidate();
    }, function(e) {
        console.info("set remote sdp failed" + e.toString())
    });
}

function onCreateSessionDescriptionError(error) {
    console.info('Failed to create session description: ' + error.toString());
    stopChat();
}

function gotRemoteStream(e) {
    
}

function onIceCandidate(event) {
    console.log(event);
    if(!event.candidate) {
        return;
    }
    var sendCanditateData = {t_id: "" + t_id, f_id: "" + memberInfo.member_id, candidate: event.candidate}
    current_socket.emit("send candidate", sendCanditateData);
    console.info('local ICE candidate: \n' + (event.candidate ?
        event.candidate.candidate : '(null)'));
}

function onAddIceCandidateSuccess() {
    console.info('AddIceCandidate success.');
}

function onAddIceCandidateError(error) {
    console.info('Failed to add Ice Candidate: ' + error.toString());
}

function checkPeerConnStatus(tip) {
    if(peerConn) {
        if(!tip) {
            tip = "";
        }
        console.info(tip + " --||-- ICE status:" + peerConn.iceConnectionState + ", signal state:" + peerConn.signalingState, " ice gathering state:" + peerConn.iceGatheringState);
    }
}