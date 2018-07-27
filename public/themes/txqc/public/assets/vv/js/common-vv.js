$(function(){
	Web.init();
})
var Web = {
	init: function(){
		this.checkIe();
		this.livePlay();
		this.tableScroll();
		this.testDirve();
		this.cluoInit();
		//this.newsScroll();

		//this.carslist();
		//this.goDriver();
	},
	checkIe: function(){
		var b_v = navigator.appVersion;
		var IE6 = b_v.search(/MSIE 6/i) != -1;
		var IE7 = b_v.search(/MSIE 7/i) != -1;
		if (IE6)
		{
			alert("您的浏览器版本过低，在本系统中不能达到良好的视觉效果，建议你升级到ie8以上！");
		}
		else if (IE7)
		{
			$(".onl_ie7").show();
			$(".close_btn").on("click",function(){
				$(".onl_ie7").hide()
			})
		}
	},
	livePlay: function(){
//		var liveHtml = '<iframe src="http://www.iqiyi.com/kszt/VV5s.html" frameborder="0" allowfullscreen="true" width="100%" height="114%"></iframe>';
//		var dataM = (new Date()).getMonth()+1, dataD = (new Date()).getDate(), dataH = (new Date()).getHours();
//		if(dataM == 8 && dataD == 31) {
//			if(dataH >= 19) {
//				$("#mainLive").html(liveHtml);
//			}
//		}
		$("#mainLive img").on("click",function(){
			$(this).hide();
			$("#mainLive").find(".jwPlayer").show();
			playVideo("jwPlayer1",753,418);
		})
	},
	advLunbo: function(){
		$('#lifeLunbo').DB_rotateRollingBanner({
			moveSpeed: 200,
			autoRollingTime: 5000
		});
	},
	tableScroll: function(){
        $.get('/portal/vv/vv5Config', function(data) {
            $("#tableBox2").html(data);
            setTimeout(function(){
                $("#tableBox2").mCustomScrollbar({
                    theme: "dark",
                    autoDraggerLength: true,
                    mouseWheel:true
                });
                $('#vv5Scroll').hide();
            }, 0);

        });
        $.get('/portal/vv/vv7Config', function(data) {
        	$("#tableBox").html(data);
        	setTimeout(function(){
                $("#tableBox").mCustomScrollbar({
                    theme: "dark",
                    autoDraggerLength: true,
                    mouseWheel:true
                });

			}, 0);

        });
        $.get('/portal/vv/p8Config', function(data) {
            $("#tableBox3").html(data);
            setTimeout(function(){
                $("#tableBox3").mCustomScrollbar({
                    theme: "dark",
                    autoDraggerLength: true,
                    mouseWheel:true
                });
                $('#p8Scroll').hide();
            }, 0);

        });
        $(".table_nav span").on('click', function(){
        	var index = $(this).index();
        	$(this).addClass('active').siblings().removeClass('active');
        	$(".mainTable").hide().eq(index).show();
		});
	},
	testDirve: function(){
		$.get("/portal/vv/testDrive", function(data) {
			$("#testDrive").html(data);
			$('select').click(function() {
				$('.sel span').each(function(i) {
					$(this).html($(this).next().find("option:checked").text())
				})
			});
			//提交表单
			$(".kuang").on("click", function() {
				var _this = $(".kuang_J");
				if(_this.hasClass("on")) {
					_this.removeClass("on")
				} else {
					_this.addClass("on")
				}
			});
		});
	},
	//精美车图
	cluoInit:function(){
		var tmp = '';
		for( var i=1; i < 23; i++){
			tmp += '<div class="item">' +
                '<img src="/themes/txqc/public/assets/vv/images/hot_img/'+i+'.png" alt="">' +
                '</div>';
		}
		$('.pic1_box').html(tmp);
        $(".pic1_box").owlCarousel({
            items: 1,
            navigation: true,
            slideSpeed: 300,
            paginationSpeed: 400,
            singleItem: true,
            autoPlay: false,
            addClassActive:true,
            afterMove: function(){
            }
        });

	},
	carslist: function(){
		$("#owlLunbo").owlCarousel({
			items: 1,
			navigation: true,
			slideSpeed: 300,
			paginationSpeed: 400,
			singleItem: true,
			autoPlay: false,
			addClassActive:true,
			afterMove: function(){
				var obj = $("#owlLunbo");
				var item = obj.find(".owl-item");
				var len = item.length;
				if(item.eq(0).hasClass("active")){
					obj.find(".owl-prev").hide();
				}else if(item.eq(len-1).hasClass("active")){
					obj.find(".owl-next").hide();
				}else{
					obj.find(".owl-next,.owl-prev").show();
				}
			}
		});
		$(".mainCars li").on("click",function(){
			var i = $(this).index();
			$(".layer").show();
			var owl = $("#owlLunbo").data('owlCarousel');
			owl.jumpTo(i);
			$(".layer .lClose").on("click",function(){
				$(this).parents(".layer").hide();
			});
			trackEvent("cars_img_"+(i+1));
		});
	},
	goDriver: function(){
		var scrollTop = $(".pageDrive").offset().top - 110;
		$("#goDriver").on("click",function(){
			$('body,html').animate({scrollTop: scrollTop},600);
			trackEvent("go_test_drive");
		});
	}
};
//加载视频
function playVideo(pId,w,h) {
	var isVideo = checkVideo();
	var data = {};
	data.mp4 = $("#" + pId).attr("data-video");
	data.image = $("#" + pId).attr("data-image");
	if(!isVideo) {
		jwplayer(pId).setup({
			autostart: true,
			width: w,
			height: h,
			skin: "js/jwplayer/skin.zip",
			controlbar: {},
			flashplayer: "js/jwplayer/player.swf",
			file: data.mp4,
			image: data.image
		});
	} else {
		var tmp = '<video preload="' + data.image + '" src="' + data.mp4 + '" type="video/mp4" width="'+w+'" height="'+h+'" controls="controls" autoplay="autoplay" loop="loop">Your browser does not support the video tag.</video>';
		$("#" + pId).html(tmp);
	}
}
//验证是否支持video
function checkVideo() {
	if(!!document.createElement('video').canPlayType) {
		var vidTest = document.createElement("video");
		oggTest = vidTest.canPlayType('video/ogg; codecs="theora, vorbis"');
		if(!oggTest) {
			h264Test = vidTest.canPlayType('video/mp4; codecs="avc1.42E01E, mp4a.40.2"');
			if(!h264Test) {
				return false;
			} else {
				if(h264Test == "probably") {
					return true;
				} else {
					return false;
				}
			}
		} else {
			if(oggTest == "probably") {
				return true;
			} else {
				return false;
			}
		}
	} else {
		return false;
	}
}
function loadTestdrive(){
	Web.testDirve();
}
//表单提交弹层
function showJsTip(str) {
	$(".mask").show();
	$(".mask .aleart").html(str);
	setTimeout(function() {
		$(".mask").hide();
	}, 1500);
}
//表单提交成功弹层
function showSucess(){
	$(".layerSucce").show();
	$(".layerSucce").find(".sCont i").on("click",function(){
		$(".layerSucce").hide();
	});
	setTimeout(function() {
		$(".layerSucce").hide();
	}, 3000);
}