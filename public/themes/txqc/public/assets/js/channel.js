
var page = page || {};
page.loadimg = function (swiper) {
    $img = $('#' + swiper.container.id + ' img')[swiper.activeIndex];
    $($img).attr('src', $($img).attr('data-src'));
}
page.video = function () {
    $(".section2 .play").on("click", function () {
        var videoData = {
            html5addr: $(this).attr('data-s'),
            flashaddr: $(this).attr('data-f')
        };


        var videoHeight = $(this).siblings().height() +18, videoWidth = $(this).siblings().width()+4;
       // var videoHeight = 470, videoWidth = 695;
        page.playVideo(videoData, videoHeight, videoWidth);
        $("#video_box").fadeIn();
        $('#video_player').click(function () { CKobject.getObjectById('video_player').playOrPause(); })
        //$(this).css("z-index", "1")
        $('.video_prev').click(function () {
            CKobject.getObjectById('video_player').videoPause();
            $("#video_box").fadeOut();
        })
        $('.video_next').click(function () {
            CKobject.getObjectById('video_player').videoPause();
            $("#video_box").fadeOut();
        })
    })
}

page.playVideo = function (videoData, videoHeight, videoWidth) {
    var playerSwf = "../ckplayer/ckplayer.swf";
    var containerId = "video_box";
    var flashvars = {
        f: videoData.flashaddr, //使用flash播放器时视频地址的
        c: 0, //风格配置参数
        p: 1, //1默认播放0暂停
        e: 1
    };
    var params = {
        bgcolor: '#FFF',
        allowFullScreen: true,
        allowScriptAccess: 'always',
        wmode: 'transparent',
        control: false
    };
    var html5video = [];
    html5video.push(videoData.html5addr);
    CKobject.embed(playerSwf, containerId, 'video_player', videoWidth, videoHeight, true, flashvars, html5video, params);
}
$(function(){
	page.video();
})
