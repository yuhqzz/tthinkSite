$(function() {
    var url = location.href.split('#')[0]; // 传给后台PHP
    $.ajax({
        url: "http://www.sztxqc.cn/portal/activity/jssdkAuth.html",
        type:'post',
        dataType: "jsonp",
        jsonp: "callback",
        data: {url:encodeURIComponent(url) },
        success: function(data) {
           // console.log(data);
            wx.config({
                debug: false,
                appId: data.appId,
                timestamp:data.timestamp ,
                nonceStr: data.nonceStr,
                signature: data.signature,
                jsApiList: [     //把需要的接口加入至列表
                    "onMenuShareTimeline", //分享给好友
                    "onMenuShareAppMessage", //分享到朋友圈
                    "onMenuShareQQ", //分享到QQ
                    "onMenuShareWeibo", //分享到微博
                    "onMenuShareQZone",// 分享到QQ空间
                ]
            });
            wx.ready(function() {
                wxShare();
            });
        }
    })
});

function wxShare() {
    var shareUrl = location.href;
    var shareData = {
        title: 'WEY VV5 一周岁了，送上超大豪礼!',  //标题
        desc: 'WEY VV5 一周岁了，送上超大豪礼!',
        link: shareUrl,
        imgUrl: "http://www.txjkc.com/themes/txqc/public/assets/vv/images/share_logo.jpg"
    };
    wx.onMenuShareAppMessage(shareData);
    wx.onMenuShareQQ(shareData);
    wx.onMenuShareWeibo(shareData);
    wx.onMenuShareTimeline(shareData);
    wx.onMenuShareQZone(shareData);
}