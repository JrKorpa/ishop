// For an introduction to the Blank template, see the following documentation:
// http://go.microsoft.com/fwlink/?LinkID=397704
// To debug code on page load in cordova-simulate or on Android devices/emulators: launch your app, set breakpoints, 
// and then run "window.location.reload()" in the JavaScript Console.
(function () {
    "use strict";

    document.addEventListener( 'deviceready', onDeviceReady.bind( this ), false );

    function onDeviceReady() {
        // Handle the Cordova pause and resume events
        document.addEventListener('pause', onPause.bind(this), false);
        document.addEventListener('resume', onResume.bind(this), false);

        window.cordovaext = (function() {
            var device_call_failed = function(error) {
                if (error && error.code && error.code != CaptureError.CAPTURE_NO_MEDIA_FILES) {
                    navigator.notification.alert('出错了，错误代码：'+error.code, null, '提示', '知道了');
                }
            };

            var upload_completed_hanlder = function(resp, mine_type, callback) {
                var upload_resp = typeof resp == 'object' ? resp : JSON.parse(resp);
                if (upload_resp.state == 0) {
                    navigator.notification.alert(upload_resp.msg, null, '提示', '知道了');
                    return false;
                } else {
                    if (callback) callback(upload_resp.data);
                }
            };
			
			var asyncReadFileAsBase64 = function(path, callback, output_stream) {
			    window.resolveLocalFileSystemURL(path, gotFile, fail);
			            
			    function fail(err) {            
					navigator.notification.alert('找不到文件', null, '提示', '知道了');
			    }
			
			    function gotFile(fileEntry) {
					if (output_stream) {
		                fileEntry.file(function(file) {
                            var reader = new FileReader();
		                    reader.onloadend = function(e) {
		                        callback(this.result, fileEntry);
		                    };

		                    reader.readAsDataURL(file);
		                });
						return;
					}

					callback(null, fileEntry);
			    }
            };

            return {
                getImage : function(from_camera, success_callback) {
                    
                    var srcType = from_camera === true ? Camera.PictureSourceType.CAMERA : Camera.PictureSourceType.PHOTOLIBRARY;
                    var options = {
                        quality: 20,
                        destinationType: cordova.platformId == 'ios' ? Camera.DestinationType.NATIVE_URI : Camera.DestinationType.FILE_URI,
                        sourceType: srcType,
                        encodingType: Camera.EncodingType.JPEG,
                        mediaType: Camera.MediaType.PICTURE,
                        allowEdit: false,
                        correctOrientation: true
                    };
                    
                    navigator.camera.getPicture(
                        function(img) {
                            asyncReadFileAsBase64(img, function(base64_data, fs){
                                success_callback(base64_data, fs);
                            }, false);
            
                            if (from_camera && cordova.platformId == 'ios') navigator.camera.cleanup(function(){}, function(err){});
                        }, device_call_failed, options
                    );
                },	

                asyncUpload : function (file_path, mine_type, success_callback, progress_callback, serv_url,error_callback) {
                    try{
                        var options = new FileUploadOptions();
                        options.fileKey = "file";
                        options.fileName = file_path.substr(file_path.lastIndexOf('/')+1);
                        options.mimeType = mine_type;
                        options.chunkedMode = false;

                        var ft = new FileTransfer();
                        if (progress_callback) {
                            ft.onprogress = function(progressEvent) {
                                if (progressEvent.lengthComputable) {
                                    progress_callback(progressEvent.loaded / progressEvent.total);
                                } else {
                                    progress_callback(1);
                                }
                            };
                        }

                        var default_success_callback = function(r) {
                            upload_completed_hanlder(r.response, mine_type, success_callback);
                        };

                        var fail = function(error) {
                            var human_tips = '';
                            switch(error.code) {
                                case FileTransferError.FILE_NOT_FOUND_ERR :
                                    human_tips = '找不到该文件';
                                    break;
                                case FileTransferError.INVALID_URL_ERR:
                                    human_tips = '非法的文件路径';
                                    break;
                                case FileTransferError.CONNECTION_ERR:
                                    human_tips = '连接服务器错误';
                                    break;
                                default:
                                    human_tips = '未知的错误';
                                    break;
                            }
                            navigator.notification.alert(human_tips, null, '提示', '知道了');
                        };

                        var uri = encodeURI(ApiUrl + "/index.php?act=index&op=appupload");
                        if (serv_url) uri = encodeURI(serv_url);
                        if (cordova.platformId == 'android') {
                            var qm_idx = file_path.indexOf('?');
                            if (qm_idx > 0) {
                                file_path = file_path.substr(0, qm_idx);
                            }
                        } else if (cordova.platformId == 'ios') {
                            if (options.mimeType != 'audio/wav') {
                                options.fileName = options.fileName.substr(0, options.fileName.lastIndexOf('?'));
                            }
                        }
                        ft.upload(file_path, uri, default_success_callback, fail, options);
                    }catch(error){
                        if(typeof(error_callback)=='function') error_callback(error);
                    }
                },
                xhr_asyncUpload : function(base64data_or_fe, mine_type, success_callback, progress_callback, serv_url) {
                    var uri = ApiUrl + "/index.php?act=index&op=appupload&manual=1&cc="+cordova.platformId+"ft="+encodeURIComponent(mine_type);
                    if (serv_url) uri = serv_url;

                    if (base64data_or_fe instanceof FileEntry) {
                        
                        base64data_or_fe.file(function(file) {
                            var reader = new FileReader();
                            reader.onloadend = function() {
                                var rawdata = reader.result;
                     
                                var xhr = new window.XMLHttpRequest();
                                xhr.open('POST', uri, true);
                                xhr.timeout = 60000;
                                xhr.withCredentials = true;                            
                                xhr.upload.onprogress = function(evt) {
                                    if (evt.lengthComputable) {
                                        var percent = evt.loaded / evt.total;
                                        if (progress_callback) progress_callback(percent);
                                    }
                                };
                                xhr.onload = function(evt) {
                                    if(xhr.status == 200){
                                        alert(xhr.response);
                                        upload_completed_hanlder(xhr.response, mine_type, success_callback);
                                    } else {
                                        navigator.notification.alert('上传失败', null, '提示', '知道了');
                                    }
                                };
                                
                                try {
                                    xhr.send(rawdata);
                                } catch(e) {
                                    navigator.notification.alert('上传失败+', null, '提示', '知道了');
                                }
                            };

                            reader.readAsArrayBuffer(file);
                        });
                    } else {
                        $.ajax({
                            xhr: function() {
                                var xhr = new window.XMLHttpRequest();
                                xhr.upload.addEventListener("progress", function(evt) {
                                    if (evt.lengthComputable) {
                                        var percent = evt.loaded / evt.total;
                                        if (progress_callback) progress_callback(percent);
                                    }
                                }, false);
                                    
                                return xhr;
                            },
                            type: 'POST',
                            timeout: 60000,
                            url: uri,
                            data: base64data_or_fe,
                            success: function(resp){
                                upload_completed_hanlder(resp, mine_type, success_callback);
                            }
                        });  
                    }
                },

                show_tips : function() {
                    var len = arguments.length;
                    if (len == 4){
                        navigator.notification.alert(arguments[0], arguments[1], arguments[2], arguments[3]);
                    } else if (len == 3) {
                        if (typeof arguments[1] === 'function') {
                            navigator.notification.alert(arguments[0], arguments[1], arguments[2]);
                        } else {
                            navigator.notification.alert(arguments[0], null, arguments[2], arguments[3]);
                        }
                    } else if (len == 2) {
                        if (typeof arguments[1] === 'function') {
                            navigator.notification.alert(arguments[0], arguments[1]);
                        } else {
                            navigator.notification.alert(arguments[0], null, arguments[1]);
                        }
                    } else if (len == 1) {
                        navigator.notification.alert(arguments[0], null);
                    }
                },

                scan_barcode : function(success_callback){
                    var _this = this;
                    cordova.plugins.barcodeScanner.scan(
                        function (result) { 
                            //_this.show_tips(JSON.stringify(result));
                            if (!result.cancelled) {
                                success_callback(result);  
                            }    
                        },
                        function (error) {
                            _this.show_tips(error);
                        },
                        {
                            preferFrontCamera : false, // iOS and Android
                            showFlipCameraButton : false, // iOS and Android
                            showTorchButton : true, // iOS and Android
                            torchOn: false, // Android, launch with the torch switched on (if available)
                            saveHistory: false, // Android, save scan history (default false)
                            prompt : "请将二维码/条码放入框内，即可自动扫描", // Android
                            resultDisplayDuration: 0, // Android, display scanned text for X ms. 0 suppresses it entirely, default 1500
                            //formats : "QR_CODE,PDF_417", // default: all but PDF_417 and RSS_EXPANDED
                            //orientation : "landscape", // Android only (portrait|landscape), default unset so it rotates with the device
                            disableAnimations : true, // iOS
                            disableSuccessBeep: true // iOS and Android
                        }
                     );
                },

                network_state : function() { 
                    return navigator.connection.type;
                },

                notification : function() {
                    return navigator.notification
                },
                
                is_ios : function() {
                    return cordova.platformId === 'ios'
                },
                
                is_android : function() {
                    return cordova.platformId === 'android'
                }
            };
        })();
    };

    function onPause() {
        // TODO: This application has been suspended. Save application state here.
    };

    function onResume() {
        // TODO: This application has been reactivated. Restore application state here.
    };   

} )();