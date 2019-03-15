var mobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent);
var isIos = /iPhone|iPad/i.test(navigator.userAgent);
var touchstart = mobile ? "touchstart" : "mousedown";
var touchend = mobile ? "touchend" : "mouseup";
var touchmove = mobile ? "touchmove" : "mousemove";
var pubFunction = null;
var host = 'http://www.txjkc.com';
$(function() {
	loadTestdrive();
	var mySwiper = new Swiper('#meituList', {
		loop: true,
		nextButton: '.page6 .next',
		prevButton: '.page6 .prev'
	});
	var driveTop = $(".testDrive").offset().top;
	$(".btn_lj").on('click', function() {
		$("html, body").animate({
			scrollTop: driveTop
		});
		//trackEvent("go_driver");
	});
	$.get(host+"/portal/vv/wey_car_config.html?style_id=1", function(data){
		$("#configContent").html(data);
	});
	$('#carSelect').on("change", function(){
		$('#configContent').html('<div class="loading"></div>');
		var options = $(this).find('option:checked');
		$(this).siblings("h3").text(options.text());
		var getUrl = host+"/portal/vv/wey_car_config.html?style_id="+options.val();
		$.get(getUrl, function(data){
			$("#configContent").html(data);
		});
	});
	$("#configContent").delegate('thead', "click", function(){
		var tbodys = $(this).next();
		if(tbodys.is(':visible')){
			tbodys.hide();
			$(this).removeClass('hover');
		}else{
			$("#configContent thead").removeClass("hover")
			$("#configContent tbody").hide();
			tbodys.fadeIn();
			$(this).addClass('hover');
		}
	});
	$(".video_btn").on('click', function() {
		playVideo1();
		//trackEvent("btn_play");
	});
	$(".quit").on('click', function(){
		$(".page_about").fadeOut();
	});
	$("#dialog2 .weui_dialog_ft").on('click', function() {
		$("#dialog2").hide();
	});
	$(".success .closes").on('click', function() {
		$('.success').fadeOut();
	});
});

function playVideo1() {
	var vbox = $('#videoid1');
	//var data = {};
	//data.mp4 = _this.attr("data-video");
	//data.image = _this.attr("data-image");
	if(isIos) {
		var tmp = '<video poster="images/video_big.jpg" src=" ../video/vv5.mp4" type="video/mp4" playsinline="true" webkit-playsinline="true" x-webkit-airplay="true" x5-video-player-type="h5" loop="loop" controls="controls" autoplay="autoplay">Your browser does not support the video tag.</video>';
	} else {
		var tmp = '<video poster="images/video_big.jpg" src=" ../video/vv5.mp4" type="video/mp4" loop="loop" autoplay="autoplay">Your browser does not support the video tag.</video>';
	}
	vbox.html(tmp);
	//vbox.find("video").trigger("play");
	//trackEvent("btn_play");
}
function loadTestdrive() {
	$.get(host+"/portal/vv/testDrive_m.html?style_id=2", function(d) {
		$("#testDrive").html(d);
	});
}
var timer = null;
function showJsTip(s) {
	$(".weui_dialog_content").text(s);
	$("#dialog2").fadeIn();
	if(timer)clearTimeout(timer);
	timer = setTimeout(function() {
		$("#dialog2").hide();
	}, 2000)
}

function showSucess() {
	$('.success').fadeIn();
	setTimeout(function() {
		$('.success').fadeOut();
	}, 2000);
}

