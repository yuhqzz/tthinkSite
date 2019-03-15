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
	var init_brand_id = parseInt($('.J_anchors').eq(0).data('bid'));
    // 初始化
    getSeriesData(init_brand_id);
    $('.mod-enroll select[name="brand_id"]').val(init_brand_id);


    var $fixedTab = $('#J_fastbar');
    var _sT = 0;
    var $window = $(window);
    _quick_nav();
    $window.on('resize scroll', _quick_nav);
    //底部浮动
    function _quick_nav() {
        _sT = $window.scrollTop();
        var _top_arr = $('.mod-enrollbox .btn').offset().top +46;
        if (_sT > _top_arr) {
            $fixedTab.removeClass('g-hide');
        } else {
            $fixedTab.addClass('g-hide');
        }
    }
    // 品牌点击事件
    $('.J_anchors').on('click', function(){
        var bid = $(this).attr('data-bid');
        $("html, body").scrollTop(parseInt($(".miaoto-"+bid).offset().top)-10);
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
	    	var brand_id = $formEnroll.find('select[name="brand_id"]').val();
	    	var dealer_id = $formEnroll.find('input[name="dealer"]').val();

            /*console.log(brand_id,series_id,_username,dealer_id,_mobile);
            debugger;*/
	    	if(parseInt(series_id) == 0){
				layer.open({
					content: '请选择车型',
					skin: 'msg',
					time: 2
				});
	    		return false;
	    	}

            if(parseInt(brand_id) == 0){
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

	    	if(parseInt(dealer_id) == 0){
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
            var index = -1;
	        $.ajax({
                type: $formEnroll.attr('method'),
                url: $formEnroll.attr('action'),
                data: $formEnroll.serialize(),
                dataType: 'json',
                cache: true,
                beforeSend:function(xhr, settings){
                   index = layer.open({
                        shadeClose: false,
                        type: 2,
                        content: '提交中'
                    });
                },
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
                    setTimeout(layer.close(index),3000);;
	           	},
                error: function(xhr, errorType, error) {
					layer.open({
						content: '数据异常，请稍后!',
						skin: 'msg',
						time: 2
					});
                  //  setTimeout(layer.closeAll('loading'),30000);
                    setTimeout(layer.close(index),3000);
                }
	        });
            var _btn_text = $formEnroll.find('input[type="submit"]').attr('data-text');
            $formEnroll.find('input[type="submit"]').removeAttr('disabled').val(_btn_text);
	        return false;
	   });
    }
    //报名有礼
    formEnroll($('#J_formEnroll'));

    function getSeriesData(brand_id,calback){
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
                    var selectname = '';
                    var selectsid = 0;
                    for (var i = 0; i < data.data.length; i++) {
                        if (  i == 0  ) {
                            html += '<a href="javascript:;" data-sid="' + data.data[i].id + '" class="on" data-bid="' + brand_id + '"><span>' + data.data[i].name + '</span></a>';
                            selectname = data.data[i].name;
                            selectsid = data.data[i].id;
                        } else {
                            html += '<a href="javascript:;" data-sid="' + data.data[i].id + '" class="" data-bid="' + brand_id + '"><span>' + data.data[i].name + '</span></a>';
                        }
                    }
                    $("#style-box").html(html);
                    $("#selected_style").html(selectname);
                    $('.mod-enroll input[name="series_id"]').val(selectsid);

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
                    if($.isFunction(calback)){
                        calback();
                    }

                }
            }
        })

    }

    $('#J_formEnroll select[name="brand_id"]').change(function(){
        var brand_id = $(this).val()||5;
            getSeriesData(brand_id);
        });

    $("#dealer-select-operate").on('click',function () {
        $('#dealer-select').toggle();
    })
	//点击车型
	$('.mod-style .style-box a').on('click', function(){
		var _sid = $(this).attr('data-sid');
        var _bid = $(this).attr('data-bid');
		$(this).addClass('on').siblings('a').removeClass('on');
		$('.mod-enroll input[name="series_id"]').val(_sid);
        $('.mod-enroll select[name="brand_id"]').val(_bid);
		$('.mod-style .style-bottom span').text($(this).text());
	});

	//经销商切换
	$('.mod-dealer .dealer-nav a').on('click', function(){
		var _index = $(this).index();
		$(this).addClass('on').siblings('a').removeClass('on');
		$('.mod-dealer .dealer-list').eq(_index).show().siblings('.dealer-list').hide();
	});

	$('#J_fastbar a').on('click', function(){
		var _top_arr = $('.mod-style').offset().top-130;
        $('#J_fastbar').addClass('g-hide');
        $('html,body').scrollTop(_top_arr);
	});

    //点击车系
	$('.mod-yhstyle li a').on('click', function(){
		var _sid = $(this).attr('data-sid');
        var _bid = $(this).attr('data-bid');
        $('.mod-enroll select[name="brand_id"]').val(_bid);
        $('.mod-enroll input[name="series_id"]').val(_sid);
        getSeriesData(_bid,function(){
            $('.mod-style .style-box a[data-sid="'+_sid+'"]').trigger('click');
        });
        var _top_arr = $('.mod-style').offset().top-130;
        $('html,body').scrollTop(_top_arr);
	});
});