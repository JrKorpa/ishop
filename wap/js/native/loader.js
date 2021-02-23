
switch(window.platformId) {
    case 'android':
    load_android();
        break;
    case 'ios':
        load_ios();
        break;
    case 'osx':
		load_osx();
    default:
        break;
}

function load_android() {
	if (document.getElementById('cordova_channel')) return;
    document.write('<script id="cordova_channel" type="text/javascript" src="'+ WapSiteUrl  +'/js/native/android/cordova.js"><\/script>');
    document.write('<script type="text/javascript" src="'+ WapSiteUrl  +'/js/native/cordova.init.js"><\/script>');
}

function load_ios() {
	//if (document.getElementById('ios_channel')) return;
    //document.write('<script id="ios_channel" type="text/javascript" src="'+ WapSiteUrl  +'/js/native/iOSJsBridge.js"><\/script>');
	if (document.getElementById('cordova_channel')) return;
    document.write('<script id="cordova_channel" type="text/javascript" src="'+ WapSiteUrl  +'/js/native/ios/cordova.js"><\/script>');
    document.write('<script type="text/javascript" src="'+ WapSiteUrl  +'/js/native/cordova.init.js"><\/script>');
}

function load_osx() {
	//if (document.getElementById('ios_channel')) return;
    //document.write('<script id="ios_channel" type="text/javascript" src="'+ WapSiteUrl  +'/js/native/iOSJsBridge.js"><\/script>');
	if (document.getElementById('cordova_channel')) return;
    document.write('<script id="cordova_channel" type="text/javascript" src="'+ WapSiteUrl  +'/js/native/osx/cordova.js"><\/script>');
    document.write('<script type="text/javascript" src="'+ WapSiteUrl  +'/js/native/cordova.init.js"><\/script>');
}
