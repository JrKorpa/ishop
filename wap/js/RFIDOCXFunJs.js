var rfid_connected = false;
$(document).ready(function () {
	//connect_rfid();
});

function connect_rfid() {
	$.ajax({
		url: "http://127.0.0.1:18086/Connect",
		dataType: "jsonp",
		timeout: 1000,
		success: function (data) {
			if (data != 0) {
				// 提示错误
				connect_rfid();
			} else {
				rfid_connected = true;
			}
		},
		error: function(ex) {
			connect_rfid();
		}
	});
}

function disconnect_rfid(){
	$.ajax({
		url: "http://127.0.0.1:18086/Disconnect",
		dataType: "jsonp",
		timeout: 1000,
		success: function (data) {
			if(data==0) rfid_connected=false;
		}
	});
}

function write(epc, callback) {
	if (!rfid_connected) {
		connect_rfid();
	}

	$.ajax({
		url: "http://127.0.0.1:18086/Write?id=&nid=" + epc,
		dataType: "jsonp",
		timeout: 1000,
		success: function (data) {
			if (data == 0) {
				if (callback) callback(true);
			} else {
				showError('RFID写入失败');
				if (callback) callback(false);
			}
		}
	});
}

function read(callback) {
	if (!rfid_connected) {
		connect_rfid();
	}

	$.ajax({
		url: 'http://127.0.0.1:18086/Read',
		type: "get",
		dataType: "jsonp",
		success: function (data) {
			data = data +'';
			console.log(data);
			var resp = data.match(/^EPC:\s*(\w*)\s*TID:\s*(\w+)/);
			if (resp == null|| resp[2] == '000000000000000000000000') {
				console.log('RFID标签数据异常');
				disconnect_rfid();
			} else {
				console.log(resp);
				if (callback) callback(resp[2], resp[1]);
			}
		},
		error: function () {
			//showError('RFID标签读取失败');
		}
	});
}

function showError(error) {
	alert(error);
}