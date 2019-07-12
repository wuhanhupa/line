//px转换为rem
(function(doc, win) {
    var docEl = doc.documentElement,
        resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
        recalc = function() {
            var clientWidth = docEl.clientWidth;
            if(!clientWidth) return;
            docEl.style.fontSize = 100 * (clientWidth / 1242) + 'px';
        };

    if(!doc.addEventListener) return;
    win.addEventListener(resizeEvt, recalc, false);
    doc.addEventListener('DOMContentLoaded', recalc, false);
})(document, window);


function jsWebView() {
    
    if(typeof goToHome === "function") {
        goToHome('ios');
    } else {
        android.goToHome('gohome');
    }
}