//SAP OpenUI5
sap.ui.require([
    "sap/m/Shell",
    "sap/ui/core/ComponentContainer"
], function (Shell, ComponentContainer) {
    new Shell({
        appWidthLimited: false,
        app: new ComponentContainer({
            height: "100%",
            name: "com.mlauffer.gotmoneyappui5"
        })
    }).placeAt("content");
});


//GOOGLE
var Google = {auth2: null}; // The Sign-In object.
function onLoadGoogleClient() {
    gapi.load('auth2', function () {
        Google.auth2 = gapi.auth2.init();
    });
}

window.___gcfg = {
    lang: 'en-US',
    parsetags: 'onload'
};
(function () {
    var po = document.createElement('script');
    po.type = 'text/javascript';
    po.async = true;
    po.src = 'https://apis.google.com/js/client:plusone.js?onload=render';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(po, s);
})();
//GOOGLE ANALYTICS
(function (i, s, o, g, r, a, m) {
    i['GoogleAnalyticsObject'] = r;
    i[r] = i[r] || function () {
            (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
    a = s.createElement(o),
        m = s.getElementsByTagName(o)[0];
    a.async = 1;
    a.src = g;
    m.parentNode.insertBefore(a, m)
})(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
ga('create', 'UA-11465363-7', 'auto');
ga('send', 'pageview');


//Facebook SDK
window.fbAsyncInit = function () {
    FB.init({
        //appId      : '182002078627839', //PRD
        appId: '542787052549338', //DEV
        xfbml: true,
        version: 'v2.5'
    });
};

(function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {
        return;
    }
    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));