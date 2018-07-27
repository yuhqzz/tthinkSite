(function() {
	var winSize = getWidthHeight();
	var resizeTimer;
	$(window).resize(function() {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(function() {
			winSize = getWidthHeight();
			if (winSize.wWidth >= 992) {
				$('body,#main').removeAttr('style');
			} else {
				if ($('button.navbar-toggle').hasClass('active')) {
					$('body').attr('style', 'overflow: hidden;position: fixed;');
				}
				$('.nav-big-menu,.nav-big-car').removeClass('show');
			}
		}, 250);
	});

	var $header = $('header');
	$(window).scroll(function(event) {
		if ($(window).scrollTop() >= 111) {
			$header.addClass('small');
			$('.nav_enicon').hide();
			$('.newIcon').addClass('scroll');
		} else {
			$header.removeClass('small');
			$('.nav_enicon').show();
			$('.newIcon').removeClass('scroll');
		}
	});

	var sTop = 0;
	$('button.navbar-toggle').on('click', function(event) {
		event.preventDefault();
		if ($('#collapse').hasClass('collapsing')) return;
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			$('body,#main').removeAttr('style');
			$('body').scrollTop(sTop)
		} else {
			sTop = $('body').scrollTop();
			$(this).addClass('active');
			$('#collapse').css('max-height', window.innerHeight - 64)
			$('body').attr('style', 'overflow: hidden;position: fixed;');
			$('#main').css('margin-top', -sTop);
		}
	});
    // wp-aleen
	$(".common-right-nav").mouseover(function(){
	    $(this).addClass("new-hover");
	});
	$(".common-right-nav").mouseout(function(){
	    $(this).removeClass("new-hover");
	});
	$(".share-icon2").click(function(){
	    $(".qr-code-popup").toggleClass('on-open');
	});
	$('.nav-big-menu .left-box a').mouseenter(function(event) {
		event.preventDefault();
		if ($(this).hasClass('active')) return;
		$(this).addClass('active').siblings('a').removeClass('active');
		$(this).parents('.left-box').next('.right-box').find('.content').eq($(this).index()).addClass('show').siblings('.content').removeClass('show');
	});

	//二级菜单事件,解决click冲突
	function navEvent(){
		if (winSize.wWidth < 992) return;
		var isClick = false;
		var time ;
		$('.navbar-nav li.more').mouseover(function(event) {
			event.preventDefault();
			$(this).addClass('hover').find('a.more').next('ul').show();
			$(this).find('a.more').next('div').show().children('ul').show();
		});
		$('.navbar-nav li.more').on('mousedown', function(event) {
			event.preventDefault();
			clearTimeout(time);
			isClick=true;
		});
		$('.navbar-nav li.more').on('mouseup', function(event) {
			event.preventDefault();
			time = setTimeout(function(){
				isClick=false;console.log(isClick)
			},10)
			
		});
		$('.navbar-nav li.more').mouseout(function(event) {
			event.preventDefault();
			if(isClick) return;
			
			$(this).removeClass('hover').find('a.more').next('ul').hide();
			$(this).find('a.more').next('div').hide().children('ul').hide();
		});
	}

	navEvent();
	
	$('.navbar-nav a.more').on('click', function(event) {
		event.preventDefault();
		if (winSize.wWidth >= 992) return;
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			$(this).next('ul').removeClass('show');
			$(this).next('div').removeClass('show');
			$(this).next('div').children('ul').removeClass('show');
		} else {
			$(this).addClass('active');
			$(this).next('ul').addClass('show');
			$(this).next('div').addClass('show');
			$(this).next('div').children('ul').addClass('show');
			$(this).parent('li').siblings('li').find('a.more').removeClass('active').next('ul').removeClass('show');
			$(this).parent('li').siblings('li').find('a.more').removeClass('active').next('div').removeClass('show');
			$(this).parents('ul.nav').siblings('ul.nav').find('a.more').removeClass('active').next('ul').removeClass('show');
		}
	});

	$('header .nav a.more').mouseenter(function() {
		if (winSize.wWidth < 992) return;
		$(this).nextAll('.nav-big-menu, .nav-big-car').addClass('show');

	});
	$('header .nav a.more').mouseleave(function() {
		if (winSize.wWidth < 992) return;
		var _this = $(this);
		setTimeout(function() {
			if (_this.nextAll('.nav-big-menu, .nav-big-car').hasClass('hover')) return;
			_this.nextAll('.nav-big-menu, .nav-big-car').removeClass('show');
		}, 10)

	});

	$('header .nav-big-menu, header .nav-big-car').mouseenter(function() {
		if (winSize.wWidth < 992) return;
		$(this).addClass('hover');
	})
	$('header .nav-big-menu, header .nav-big-car').mouseleave(function() {
		if (winSize.wWidth < 992) return;
		$(this).removeClass('show hover');
	});

	$('footer .col-md-1 h4').on('click', function(event) {
		event.preventDefault();
		if (winSize.wWidth >= 1200) return;
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			$(this).next('.content').removeClass('show');
		} else {
			$(this).addClass('active');
			$(this).next('.content').addClass('show');
			$(this).parents('div.col-md-1').siblings('div.col-md-1').find('h4.active').removeClass('active').next('.content').removeClass('show');
		}
	});

	$('a.btn-go-top').on('click', function(event) {
		event.preventDefault();
		$(window).scrollTop(0)
	});

})();

/*用于兼容ie8实现不同宽度窗口的响应式布局*/
function getWidthHeight() {
	var wWidth, wHeight;
	//获取窗口宽度
	if (window.innerWidth) {
		wWidth = window.innerWidth;

	} else if (document.body && document.body.clientWidth) {
		wWidth = document.body.clientWidth;
	}
	//获取窗口高度
	if (window.innerHeight) {
		wHeight = window.innerHeight;
	} else if (document.body && document.body.clientHeight) {
		wHeight = document.body.clientHeight;
	}
	//通过深入Document内部对body进行检测，获取窗口大小
	if (window.documentElement && window.documentElement.clientWidth && window.documentElement.clientHeight) {
		wWidth = window.documentElement.clientWidth;
		wHeight = window.documentElement.clientHeight;
	}

	return {
		"wWidth": wWidth,
		"wHeight": wHeight
	};
}

/*
 * jQuery placeholder, fix for IE6,7,8,9
 * @author JENA
 * @since 20131115.1504
 * @website ishere.cn
 */
var JPlaceHolder = {
    //检测
    _check : function(){
        return 'placeholder' in document.createElement('input');
    },
    //初始化
    init : function(){
        if(!this._check()){
            this.fix();
        }
    },
    //修复
    fix : function(){
        jQuery(':input[placeholder]').each(function(index, element) {
            var self = $(this), txt = self.attr('placeholder');
            self.wrap($('<div></div>').css({position:'relative', zoom:'1', border:'none', background:'none', padding:'none', margin:'none'}));
            var pos = self.position(), h = self.outerHeight(true), paddingleft = self.css('padding-left');
            var holder = $('<span></span>').text(txt).css({position:'absolute', left:pos.left, top:pos.top, height:h, lineHeight:h+'px', paddingLeft:paddingleft, color:'#aaa'}).appendTo(self.parent());
            self.focusin(function(e) {
                holder.hide();
            }).focusout(function(e) {
                if(!self.val()){
                    holder.show();
                }
            });
            holder.click(function(e) {
                holder.hide();
                self.focus();
            });
        });
    }
};
//执行
jQuery(function(){
    JPlaceHolder.init();    
});


/**
 * Created by lzhao on 2017/4/21. ie8及以下显示浮层
 */
var theUA = window.navigator.userAgent.toLowerCase();
if ((theUA.match(/msie\s\d+/) && theUA.match(/msie\s\d+/)[0]) || (theUA.match(/trident\s?\d+/) && theUA.match(/trident\s?\d+/)[0])) {
	var ieVersion = theUA.match(/msie\s\d+/)[0].match(/\d+/)[0] || theUA.match(/trident\s?\d+/)[0];
	if (ieVersion < 9) {
		$('#ifIe_mask').show();
		$('#ifIe').show();
		
		$('body').css('overflow-y','hidden');
	}
} 
 
$('#ifIe .closeBtn').click(function(event){
	event.preventDefault();
	
	$('#ifIe_mask').hide();
	$('#ifIe').hide();
	$('body').css('overflow-y','scroll');
});

/**
 * 车品导航轮播
 */
var navstep = 174;
var navflag = true;
$('.nav-top-arrow').click(function(event){
	event.preventDefault();
	
	if(navflag){
		navflag = false;
		
		var top = $('.nav-car-list').css('top');
		top = parseInt(top);
		if(top == 0){
			navflag = true;
			return false;
		}
		top += navstep;
		$('.nav-car-list').animate({'top':top},400,function(){
			navflag = true;
		});	
		if(top == 0){
			$('.nav-top-arrow').addClass('disabled');	
		}
		$('.nav-bottom-arrow').removeClass('disabled');
	}
});
$('.nav-bottom-arrow').click(function(event){
	event.preventDefault();
	
	if(navflag){
		navflag = false;
		
		var navHeight = $('.nav-car-list').height();
		var top = $('.nav-car-list').css('top');
		top = parseInt(top);
		if(top == -(navHeight-navstep*3)){
			navflag = true;
			return false;
		}
		
		top -= navstep;
		$('.nav-car-list').animate({'top':top},400,function(){
			navflag = true;	
		});
		if(top == -(navHeight-navstep*3)){
			$('.nav-bottom-arrow').addClass('disabled');	
		}
		$('.nav-top-arrow').removeClass('disabled');
	}
});

/*
$(".nav_more").click(function(){
	window.location.href = "/interconnection.html";
})
*/

if($(window).width()>992){
	$('.nav-car-list li:nth-child(3) .price').css('margin-left','100px');
 }