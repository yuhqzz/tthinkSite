var lazyImg = {
        loadArr:null,
        init:function(){
            var lazyImgObj = $("img[_src]");
            this.calculate(lazyImgObj);
            var arrScrollTop = this.loadArr;

            if(arrScrollTop == null){
                return false;
            }
            var _this = this;
            _this.isLoad($(window).scrollTop(), arrScrollTop);
            $(window).scroll(function () {
                _this.isLoad($(this).scrollTop(), arrScrollTop);
            });
        },
        calculate:function(lazyImgObj){
            var windowHeight = $(window).height();
            var arrReturn = {};
            var _scrollTop;
            if(lazyImgObj.length == 0){
                return false;   
            }
            var lazyImgObjLength = lazyImgObj.length;
            for(var i=0 ; i<lazyImgObjLength ;i++ ){
                _scrollTop = parseInt(lazyImgObj.eq(i).offset().top - windowHeight);
                if (!arrReturn.hasOwnProperty(_scrollTop)) {
                    arrReturn[_scrollTop] = new Array();
                }
                arrReturn[_scrollTop].push(lazyImgObj.eq(i));   
            }
            this.loadArr = arrReturn;
        },
        isLoad:function(scrolltop,objsTop){
            if(objsTop != null && objsTop != {}){
                for (var i in objsTop) {
                    if (parseInt(i) <= scrolltop && objsTop.hasOwnProperty(i)) {
                        for (var j=0;j<objsTop[i].length;j++) {
                            objsTop[i][j].attr("src",objsTop[i][j].attr("_src"));
                            objsTop[i][j].removeAttr("_src");
                        }
                        delete this.loadArr[i];
                    }
                }
            }
        }
    }
    
$(function(){
    lazyImg.init();    
});



$(function () {
	var $w = $(window);
    var _H = $w.height();

    //触摸滑动
    function alloyTouch(obj, dom, options, $variable) {
        var setting = {
            alloyChange: function(value) {},
            alloyAnimationEnd: function(value) {},
            alloyPressMove: function(evt, value) {},
            alloyTouchStart: function(evt, value) {},
            alloyTouchMove: function(evt, value) {},
            alloyTouchEnd: function(evt, value) {},
            alloyTap: function(evt, value) {},
            alloyTo: function($variable){},
            translate: "translateY",
            vertical: true,
            preventDefault: true
        };

        var target = document.querySelector(obj);
        var Minimum;

        if (options) {
            $.extend(setting, options);
        }

        if($variable == undefined){
            var alloyTouch;
            $variable = alloyTouch;
        }

        if (setting.translate == "translateY") {
            Minimum = $(dom).height() - $(obj).height();
            if ($(obj).height() < $(dom).height()) {
                return false;
            }
        } else {
            Minimum = $(dom).width() - $(obj).width();
            if ($(obj).width() < $(dom).width()) {
                return false;
            }
        }
        //给element注入transform属性
        Transform(target, true);
        $variable = new AlloyTouch({
            touch: dom, //反馈触摸的dom
            vertical: setting.vertical, //不必需，默认是true代表监听竖直方向touch
            target: target, //运动的对象
            property: setting.translate, //被滚动的属性
            sensitivity: 1, //不必需,触摸区域的灵敏度，默认值为1，可以为负数
            factor: 1, //不必需,默认值是1代表touch区域的1px的对应target.y的1
            min: Minimum, //不必需,滚动属性的最小值
            max: 0, //不必需,滚动属性的最大值
            step: 45,
            preventDefault: setting.preventDefault,
            bindSelf: true,
            initialValue: 0,
            change: function(value) {
                if (setting.alloyChange && typeof setting.alloyChange) {
                    setting.alloyChange(value);
                }
            },
            touchStart: function(evt, value) {
                if (setting.alloyTouchStart && typeof setting.alloyTouchStart) {
                    setting.alloyTouchStart(evt, value);
                }
            },
            touchMove: function(evt, value) {
                if (setting.alloyTouchMove && typeof setting.alloyTouchMove) {
                    setting.alloyTouchMove(evt, value);
                }
            },
            touchEnd: function(evt, value) {
                if (setting.alloyTouchEnd && typeof setting.alloyTouchEnd) {
                    setting.alloyTouchEnd(evt, value);
                }
            },
            tap: function(evt, value) {
                if (setting.alloyTap && typeof setting.alloyTap) {
                    setting.alloyTap(evt, value);
                }
            },
            animationEnd: function(value) {
                if (setting.alloyAnimationEnd && typeof setting.alloyAnimationEnd) {
                    setting.alloyAnimationEnd(value);
                }
            },
            pressMove: function(evt, value) {
                if (setting.alloyPressMove && typeof setting.alloyPressMove) {
                    setting.alloyPressMove(evt, value);
                }
                //console.log(evt.deltaX + "_" + evt.deltaY + "__" + value);
            },
        });

        if (setting.alloyTo && typeof setting.alloyTo) {
            setting.alloyTo($variable);
        }
        /*document.body.addEventListener("touchmove", function (evt) {
            evt.preventDefault();
        }, false);*/
    }

    function _auto_scroll_height(options) {
        var setting = {
            defaultCallback: function(){
                $.H = $w.height();
                $(document.body).css({"overflow":"hidden"});
            },
            endCallback: function(){}
        };

        if (options) {
            $.extend(setting, options);
        }
        typeof setting.defaultCallback == 'function' && setting.defaultCallback();
        typeof setting.endCallback == 'function' && setting.endCallback();
    }
    //滑动关闭
    function screenTouchmove(options) {
        var that = this;
        var setting = {
            object: '#J_carsFilterShade',
            callbackRightend: function() {},
            callbackClick: function() {}
        };
        if (options) {
            options = $.extend(setting, options);
        };

        var startX, startY, moveEndX, moveEndY, X, Y;

        $(options.object).bind("touchstart", function(e) {
            //e.preventDefault();
            var orig = event;
            startX = orig.changedTouches[0].pageX,
                startY = orig.changedTouches[0].pageY;
        });

        $(options.object).bind("touchmove", function(event) {
            //e.preventDefault();
            var orig = event;
            moveEndX = orig.changedTouches[0].pageX,
                moveEndY = orig.changedTouches[0].pageY,
                X = moveEndX - startX,
                Y = moveEndY - startY;

            if (Math.abs(X) > Math.abs(Y) && X > 0) {
                typeof options.callbackRightend == 'function' && options.callbackRightend();
            } else if (Math.abs(X) > Math.abs(Y) && X < 0) {
                //alert("right 2 left");
            } else if (Math.abs(Y) > Math.abs(X) && Y > 0) {
                //alert("right 2 top");
            } else if (Math.abs(Y) > Math.abs(X) && Y < 0) {
                //alert("right 2 bootom");
            } else{
                typeof options.callbackClick == 'function' && options.callbackClick();
            }
            return false;
        });
    }

    var _scrollT = 0;
    $(window).scroll(function(){
        _scrollT = $(window).scrollTop();
    });
    
    function isScrollTop(isScrollTop){
        if(__s){
            $('html,body').scrollTop(__scrollT);
        }
    }
    isScrollTop(__s)

    function searchStyle(){
        var that = this;
        var _scrollTopH;
        var _type = "city";

        $('#J_selectStyle').on('click', function(e){
        	var $this = $(this);
        	var _id = $this.attr('data-id') || 0;
            var __html = $('#J_screen'+_type+'_tmpl').html();
            if ($('#J_carsFilterShade').length <= 0) {
                $('body').append('<div id="J_carsFilterShade" class="mod-shade" style="z-index: 8;"></div>');
            }

            if ($('.mod-screen'+_type).length <= 0) {
                $('body').append('<div class="mod-screen'+_type+'">'+__html+'</div>');
            } else{
                $('.mod-screen'+_type).html(__html);
            }

            _auto_scroll_height();
            setTimeout(function() {
                $('.mod-screen'+_type).addClass('show').height($.H);
            }, 200);

            $('.screen'+_type+'-box dd a').each(function(i){
                if($(this).attr('data-id') == _id){
                    $(this).addClass('hover').parent().siblings('dd').find('a').removeClass('hover');
                };
            });

            /*$(document).on('touchmove', function (event) {
                event.preventDefault();
            }, false);*/

            $('.screen'+_type+'-box dd a').off('click').on('click', function(e) {
                var _id = $(this).attr('data-id') || 0;
                var _title = $(this).attr('data-title') || 0;
                $this.find('span').text(_title);
                $this.find('input[name="city"]').val(_id);
                $this.attr('data-id', _id);
                close();
                //window.location.href = "/real_20180608m/?cid="+ _id +"&sid="+ $('input[name="styleId"]').val() +"&pi="+ $('input[name="sourceValue"]').val() + '&s=1&t='+ _scrollT;
            });

            alloyTouch('.screen'+_type+'-box dl', '.screen'+_type+'-box');
            screenTouchmove({
                object: '#J_carsFilterShade',
                callbackRightend: function(){
                    close();
                }
            });

            $('#J_carsFilterShade').off('click').on('click', function() {
                close();
            });

            $('.screen'+_type+'-box .close').off('click').on('click', function() {
            	close();
            });
            function close(){
                $('#J_carsFilterShade').remove();
                $('.mod-screen'+_type).removeClass('show');
                $('.mod-container').removeClass('hidden').css('height', '');
                $('html,body').scrollTop(_scrollTopH);
                $(document.body).css({"overflow":""});
                document.body.removeEventListener('touchmove', function(evt){
                    evt.preventDefault();
                }, false);
            }
        });
    }

    searchStyle();

    var $fixedTab = $('#J_fastbar');
    var _bottom = 0;
    var _sT = 0;
    var $window = $(window);
    function _quick_nav() {
        _sT = $window.scrollTop();
        var _top_arr = $('.mod-yhstyle').offset().top - 2;
        if (_sT > _top_arr) {
        	$fixedTab.removeClass('g-hide');
        } else {
            $fixedTab.addClass('g-hide');
        }
    }
    _quick_nav();

    $window.on('resize scroll', _quick_nav);

    $('.J_anchors').on('click', function(){
        //var _top_arr = $('.mod-yhstyle').offset().top - 2;
        debugger;

        var bid = $(this).attr('data-bid');
        $('html,body').scrollTop($("#miaoto-"+bid).offset().top);
    })
    //提示
    function layerMsg(){
    	layer.closeAll();
        layer.open({
            title: '',
            className: 'bm-layer default-layer-btn3',
            content: '<i class="icon"></i><h3>恭喜您，报名成功！</h3><p>深圳大兴销售顾问将会在24小时内与您联系，请注意接听！</p>',
            btn: ['确定'],
            success: function(elem) {
            },
            yes: function(index) {
                if ($('#layermbox' + index).find('.layermbtn').find('span[type="1"]').find('em').length <= 0) {
                    layer.close(index);
                }
            },
            no: function() {}
        });
    }

	function formEnroll($formEnroll){
	   	$formEnroll.on('submit', function(){
	    	var _username = $formEnroll.find('input[name="customer_name"]').val();
	    	var _mobile = $formEnroll.find('input[name="mobile"]').val();
	    	var series_id = $formEnroll.find('input[name="series_id"]').val();
	    	var brand_id = $formEnroll.find('input[name="brand_id"]').val();
	    	var dealer_id = $formEnroll.find('input[name="dealer"]').val();


	    	if(series_id == ''){
				layer.open({
					content: '请选择车型',
					skin: 'msg',
					time: 2
				});
	    		return false;
	    	}

            if(brand_id == ''){
                layer.open({
                    content: '请选择品牌',
                    skin: 'msg',
                    time: 2
                });
                return false;
            }

	    	if(_username == ''){
				layer.open({
					content: '姓名不能为空',
					skin: 'msg',
					time: 2
				});
	    		return false;
	    	}

	    	if(dealer_id == ''){
				layer.open({
					content: '4S店不能为空',
					skin: 'msg',
					time: 2
				});
	    		return false;
	    	}
            if(_mobile == ''){
                layer.open({
                    content: '手机号码不能为空',
                    skin: 'msg',
                    time: 2
                });
                return false;
            }
            if(!/^(13[0-9]|15[0-9]|18[0-9])\d{8}$/.test(_mobile)){
                layer.open({
                    content: '手机号码不正确',
                    skin: 'msg',
                    time: 2
                });
                return false;
            }

	        var _btn_text = $formEnroll.find('input[type="submit"]').val();
	        $formEnroll.find('input[type="submit"]').attr('disabled', true).attr('data-text', _btn_text).val('提交中...');
	        $.ajax({
                type: $formEnroll.attr('method'),
                url: $formEnroll.attr('action'),
                data: $formEnroll.serialize(),
                dataType: 'json',
                cache: true,
	            success: function(_json) {
	                if (_json.code == 1) {
	                    	$formEnroll.find('input[name="customer_name"]').val('');
	                    	$formEnroll.find('input[name="mobile"]').val('');
	                    	$formEnroll.find('input[name="dealer"]').val('');
	                        layerMsg();
                    } else if(_json.code == 0|| _json.code == '-1') {
							layer.open({
								content: _json.msg,
								skin: 'msg',
								time: 2
							});
	                } else {
						layer.open({
							content: '数据异常，请稍后!',
							skin: 'msg',
							time: 2
						});
	                }
	           	},
                error: function(xhr, errorType, error) {
					layer.open({
						content: '数据异常，请稍后!',
						skin: 'msg',
						time: 2
					});
                }
	        });
            var _btn_text = $formEnroll.find('input[type="submit"]').attr('data-text');
            $formEnroll.find('input[type="submit"]').removeAttr('disabled').val(_btn_text);
	        return false;
	   });
    }
    //报名有礼
    formEnroll($('#J_formEnroll'));

    //滚动
    function infoAnimate(id, time) {
        var teltimer = null,
            $result = $(id),
            $result_U = $result.find('dl'),
            $result_H = $result.find('dd:first-child').height();

        $result.css({
            'position': 'relative'
        });
        $result_U.css({
            'position': 'absolute',
            'left': '0',
            'top': '0',
            'width': '100%'
        });

        if (teltimer == null) {
            teltimer = setTimeout(infoScroll, time);
            return false;
        }

        function infoScroll() {
            $result_U.animate({
                'top': $result_H
            }, function() {
                $result.find('dd:last').clone().hide().prependTo($result_U).fadeIn();
                $result.find('dd:last').remove();
                $result_U.css('top', '0');
                $result.toggleClass('change-bg');
                if (teltimer != undefined) {
                    clearTimeout(teltimer);
                }
                teltimer = setTimeout(infoScroll, time);

            });
        }
    }

    function getSeriesData(brand_id){
        if(!brand_id) return false;
        $.ajax({
            url: '/portal/channel/ajax_get_series.html',
            data: {'brand_id': brand_id},
            type: "get",
            cache: false,
            dataType: 'json',
            success: function (data) {
                if (data.code == 1) {
                    var html = '';
                    for (var i = 0; i < data.data.length; i++) {
                        if (i == 0) {
                            html += '<a href="javascript:;" data-sid="' + data.data[i].id + '" class="on" data-bid="' + brand_id + '"><span>' + data.data[i].name + '</span></a>';
                        } else {
                            html += '<a href="javascript:;" data-sid="' + data.data[i].id + '" class="" data-bid="' + brand_id + '"><span>' + data.data[i].name + '</span></a>';
                        }
                    }
                    $("#style-box").html(html);
                    $("#selected_style").html(data.data[0].name);

                    //点击车型
                    $('.mod-style .style-box a').on('click', function(){
                        var _ta = this;
                        var _sid = $(this).attr('data-sid');
                        var _bid = $(this).attr('data-bid');
                        $(this).addClass('on').siblings('a').removeClass('on');
                        $('.mod-enroll input[name="series_id"]').val(_sid);
                        $('.mod-enroll select[name="brand_id"]').val(_bid);
                        $('.mod-style .style-bottom span').text($(_ta).text());
                    });

                    //初始化
                    $('#dealer-select').empty();
                    $("#dealer-select-operate").val('选择预约4S店');
                    $('#J_formEnroll input[name="dealer"]').val(0);

                    // 4s店
                    if(dealer == undefined) return false;
                    var dealer_data = dealer[brand_id];
                    var li = '';
                    if(dealer_data !== undefined){
                        for(var d in dealer_data){
                            li +=' <li class="dealer-op" data-id="'+dealer_data[d].id+'" data-name="'+dealer_data[d].name+'" >'+dealer_data[d].name+'</li>';
                        }
                    }
                    if(li){
                        $('#dealer-select').empty().append(li).hide();
                        $('#dealer-select').find('li').bind('click',function () {
                            $("#dealer-select-operate").val($(this).attr('data-name'));
                            $('#J_formEnroll input[name="dealer"]').val($(this).attr('data-id'));
                            $(this).parent('ol').hide();
                        })
                    }
                }
            }
        })

    }

    $('#J_formEnroll select[name="brand_id"]').change(function(){
        var brand_id = $(this).val()||5;
            getSeriesData(brand_id);
        });
    getSeriesData(5);
    $("#dealer-select-operate").on('click',function () {
        $('#dealer-select').toggle();
    })


   	if($('#J_infoAnimateList dd').length > 6){
   		infoAnimate('#J_infoAnimateList', 4000);
   	}
	//点击车型
	$('.mod-style .style-box a').on('click', function(){
		var _sid = $(this).attr('data-sid');
        var _bid = $(this).attr('data-bid');
		$(this).addClass('on').siblings('a').removeClass('on');
		$('.mod-enroll input[name="series_id"]').val(_sid);
        $('.mod-enroll input[name="brand_id"]').val(_bid);
		$('.mod-style .style-bottom span').text($(this).text());

	});

	//经销商切换
	$('.mod-dealer .dealer-nav a').on('click', function(){
		var _index = $(this).index();
		$(this).addClass('on').siblings('a').removeClass('on');
		$('.mod-dealer .dealer-list').eq(_index).show().siblings('.dealer-list').hide();
	});

	$('#J_fastbar a.btn').on('click', function(){
		var _top_arr = $('.mod-style').offset().top;
		$('html,body').scrollTop(_top_arr);
	});

	$('.mod-yhstyle li a').on('click', function(){
		var _sid = $(this).attr('data-sid');
        var _bid = $(this).attr('data-bid');
		/*var _title = $(this).attr('data-title');*/
		var _top_arr = $('.mod-style').offset().top;
		$('html,body').scrollTop(_top_arr);

        $('.mod-enroll select[name="brand_id"]').val(_bid);
        getSeriesData(_bid);
        $('#J_formEnroll select[name="brand_id"]').change();
        $('.mod-enroll input[name="series_id"]').val(_sid);
        /*$('.mod-style .style-bottom span').text(_title);*/
        console.log($('.mod-style .style-box a[data-sid="'+_sid+'"]'));
        $('.mod-style .style-box a[data-sid="'+_sid+'"]').find('span').trigger('click');
	});


     //获取终端
     function getPage() {
         var _end = '';
         if ((navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i))) {
             _end = 2;
         } else {
             _end = 1;
         }
         return _end;
     }
     getPage();
     $('#terminal').val(getPage());
     //获取分辨率
     function getResolution() {
         var c = "-";
         window.self.screen && (c = window.screen.width + "x" + window.screen.height);
         return c
     }
     getResolution();
     $('#resolution').val(getResolution());

});