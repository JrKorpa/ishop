var SiteUrl = "http://"+window.location.host+"/shop";//"/shop";
var ApiUrl = "http://"+window.location.host+"/mobile";//"/mobile";
var pagesize = 10;
var WapSiteUrl = "http://"+window.location.host+"/wap";//"/wap";
var IOSSiteUrl = "http://"+window.location.host+"/app.ipa";//"/app.ipa";
var AndroidSiteUrl = "http://"+window.location.host+"/app.apk";//"/app.apk";

var loginCfg = {
    sso: 0,
    token_qs: '__klc_001',
    token_ck: '492D_wapsso', // 根据后端加密后的cookie名称
    login: "http://my.kela.cn/member/sso/login?client=ishop_wap",
    logout: "http://my.kela.cn/member/sso/logout"
};

// auto url detection
(function() {
    var m = /^(https?:\/\/.+)\/wap/i.exec(location.href);
    if (m && m.length > 1) {
        SiteUrl = m[1] + '/shop';
        ApiUrl = m[1] + '/mobile';
        WapSiteUrl = m[1] + '/wap';
    }
})();
