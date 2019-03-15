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

    var _scrollT = 0;
    $(window).scroll(function(){
        _scrollT = $(window).scrollTop();
    });

    function isScrollTop(isScrollTop){
        if(__s){
            $('html,body').scrollTop(__scrollT);
        }
    }
    isScrollTop(__s);

    function screen(_dom, text, isUrl, callback) {
        var $c_dom = $(_dom);
        var _timer_arr = null;
        //筛选

        $c_dom.on('click', function() {
            if (!$(this).hasClass('disabled')) {
                if ($(this).find('.menu-pop').height() >= 290) {
                    $(this).find('.menu-pop').css({ 'height': '290px' }).show();
                } else {
                    $(this).find('.menu-pop').show();
                }
            }
        });

        $c_dom.hover(function() {
            clearTimeout(_timer_arr);
        }, function() {
            var that = this;
            _timer_arr = setTimeout(function() {
                $(that).find('.menu-pop').hide().css({ 'height': '' });
            }, 400);
        });

        $c_dom.on('click', '.menu-pop a', function(e) {
            var _text = $(this).attr('data-title');
            var _val = $(this).attr('data-val');
            if (_val != 0) {
                $c_dom.find('a.select-text').attr('data-cid', _val);
                $c_dom.find('span').text(_text);
            } else {
                $c_dom.find('a.select-text').removeAttr('data-cid');
                $c_dom.find('span').text(text);
            }
            $c_dom.find('.menu-pop').hide();
            $c_dom.find('label.error').hide();
            typeof callback == 'function' && callback($c_dom,_val);
            e.stopPropagation();

        });
    }
    // 4S店
    screen('.J_a_city', '请选择4S店', 0, function(dom,id){
        if(id == 0){
            dom.find('input[name="dealer"]').val(0);
        }else{
            dom.find('input[name="dealer"]').val(id);
        }
    });
    // 4S店
    screen('.J_c_city', '请选择4S店', 0, function(dom,id){
        if(id == 0){
            dom.find('input[name="dealer"]').val(0);
        }else{
            dom.find('input[name="dealer"]').val(id);
        }
    });
    // 车系
    screen('.J_c_style', '请选择车型', 0, function(dom,id){
        if(id == 0){
            dom.find('input[name="series_id"]').val(0);
        }else{
            dom.find('input[name="series_id"]').val(id);
        }
    });
    function formEnroll($formEnroll){
        $formEnroll.on('submit', function(){
            var _username = $formEnroll.find('input[name="customer_name"]').val();
            var _mobile = $formEnroll.find('input[name="mobile"]').val();
            var _dealer = $formEnroll.find('input[name="dealer"]').val();
            var series_id = $formEnroll.find('input[name="series_id"]').val();
            if(parseInt(series_id) == 0){

                $formEnroll.find('input[type="submit"]').next('p').text('请选择车型');
                return false;
            }
            if(parseInt(_dealer) == 0){
                $formEnroll.find('input[type="submit"]').next('p').text('请选择4S店');
                return false;
            }
            if(_username == ''){
                //layer.msg('姓名不能为空');
                $formEnroll.find('input[type="submit"]').next('p').text('姓名不能为空');
                return false;
            }

            if(_mobile == ''){
                $formEnroll.find('input[type="submit"]').next('p').text('手机号码不能为空');
                return false;
            }

            if(!/^(13[0-9]|15[0-9]|18[0-9])\d{8}$/.test(_mobile)){
                //layer.msg('手机号码不正确');
                $formEnroll.find('input[type="submit"]').next('p').text('手机号码不正确');
                return false;
            }

            var _btn_text = $formEnroll.find('input[type="submit"]').val();
            $formEnroll.find('input[type="submit"]').attr('disabled', true).attr('data-text', _btn_text).val('提交中...');
            $.ajax({
                url: $formEnroll.attr('action'),
                data: $formEnroll.serialize(),
                type: $formEnroll.attr('method'),
                cache: false,
                dataType: 'json',
                success:function(data){
                    if (data.code == 1) {
                        $formEnroll.find('input[name="customer_name"]').val('');
                        $formEnroll.find('input[name="mobile"]').val('');
                        layerMsg();
                    }else if(data.code == 0|| data.code == '-1'){
                        //layer.msg(data.msg);
                        $formEnroll.find('input[type="submit"]').next('p').text(data.msg);
                    } else {
                        //layer.msg('数据异常，请稍后!');
                        $formEnroll.find('input[type="submit"]').next('p').text('数据异常，请稍后!');
                    }
                }
            })
                .always(function() {
                    var _btn_text = $formEnroll.find('input[type="submit"]').attr('data-text');
                    $formEnroll.find('input[type="submit"]').removeAttr('disabled').val(_btn_text);
                });
            return false;
        })
    }
    //报名有礼
    formEnroll($('#J_formEnroll'));

    //悬浮报名
    $('#J_btn').on('click', function(e){
        var _username = $('.tmscreening').find('input[name="customer_name"]').val();
        var _mobile = $('.tmscreening').find('input[name="mobile"]').val();
        var _dealer = $('.tmscreening').find('input[name="dealer"]').val();
        var _series_id = $('.tmscreening').find('input[name="series_id"]').val();
        var _brand_id = $('.tmscreening').find('input[name="brand_id"]').val();

        if(parseInt(_dealer) == 0){
            layer.msg('请选择4S店');
            return;
        }

        if(parseInt(_series_id) == 0){
            layer.msg('请选择车型');
            return;
        }

        if(_username == ''){
            layer.msg('姓名不能为空');
            return;
        }

        if(_mobile == ''){
            layer.msg('手机号码不能为空');
            return;
        }

        if(!/^(13[0-9]|15[0-9]|18[0-9])\d{8}$/.test(_mobile)){
            layer.msg('手机号码不正确');
            return;
        }


        $.ajax({
            url: $('#J_formEnroll').attr('action'),
            data: {
                brand_id:_brand_id,
                series_id: _series_id,
                customer_name: _username,
                mobile: _mobile,
                dealer: _dealer
            },
            type: $('#J_formEnroll').attr('method'),
            cache: false,
            dataType: 'json',
            success:function(data){
                if (data.code == 1) {
                    $('.tmscreening').find('input[name="customer_name"]').val('');
                    $('.tmscreening').find('input[name="mobile"]').val('');
                    layerMsg();
                }else if(data.code == 0|| data.code == '-1'){
                    layer.msg(data.msg);
                } else {
                    layer.msg('数据异常，请稍后!');
                }
            },
            error:function(){

            }
        })
        e.stopPropagation();
    })

    var $fixedTab = $('.fixed-footer');
    var _bottom = 0;
    var _sT = 0;
    var $window = $(window);
    function _quick_nav() {
        var size = getWindowSize();
        _bottom = $window.scrollTop()+size.y-120;
        _sT = $window.scrollTop();
        var _top_arr = $('.mod-yhstyle').offset().top - 2;
        if (_sT > _top_arr) {
            if (!!window.ActiveXObject && !window.XMLHttpRequest) {
                $fixedTab.css({
                    'position': 'absolute',
                    'bottom': _bottom
                }).show();

            } else {
                $fixedTab.show();
            }
            if (!$fixedTab.hasClass('slideUp')) {
                $fixedTab.addClass('slideUp animated');
            }
            for (var i = 0; i < _top_arr.length; i++) {
                if (_sT < _top_arr[i]) {
                    $fixedTab.find('li').find('a').removeClass('selected');
                    $fixedTab.find('li').eq(i - 1).find('a').addClass('selected');
                    break;
                }
            }

        } else {
            $fixedTab.hide();
        }
    }

    function getWindowSize() {
        var client = {
            y:0
        };

        if(typeof document.compatMode != 'undefined' && document.compatMode == 'CSS1Compat') {
            client.y = document.documentElement.clientHeight;
        } else if(typeof document.body != 'undefined' && (document.body.scrollLeft || document.body.scrollTop)) {
            client.y = document.body.clientHeight;
        }

        return client;
    }
    _quick_nav();

    $window.on('resize scroll', _quick_nav);

    $('.J_anchors').on('click', function(){
        var _top_arr = $('.mod-yhstyle').offset().top - 2;
        $('html,body').scrollTop(_top_arr);
    })


    //提示
    function layerMsg(){
        layer.closeAll();
        layer.open({
            type: 1,
            title: '报名成功',
            area: '700px',
            shift: 0,
            skin: 'layermsg-layer',
            shade: 0.6,
            closeBtn: 1,
            shadeClose: true,
            content: $('#J_layermsg_tmpl').html(),
            success: function(layero, __index) {
                layero.find('.layui-layer-content').css({
                    'overflow': 'visible'
                });

                layero.find('.layui-layer-close').removeClass('layui-layer-ico').addClass('icon')
                layero.find('.mod-layermsg a').on('click', function(){
                    getLotteryNum(1)
                    layer.close(__index);
                });
            }
        });
    }
    function getLotteryNum(lotteryid) {
        var url = "/portal/channel/getDrawLotteryNumber?lotteryid="+ lotteryid;
        $.get(url,function(res){
            if(res.code == 1) {
                $('#f-note').text(res.data.num);
            }else{
                $('#f-note').text(0);
            }
        });
    }

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

            $result_U.hover(function() {
                clearTimeout(teltimer);
            }, function() {
                if (teltimer != undefined) {
                    clearTimeout(teltimer);
                }
                teltimer = setTimeout(infoScroll, time);
            });
        }
    }

    if($('#J_infoAnimateList dd').length > 5){
        infoAnimate('#J_infoAnimateList', 4000);
    }

    //弹出报名
    $('.mod-yhstyle li a').on('click', function(){
        var _text = $(this).attr('data-title');
        var _sid = $(this).attr('data-sid');
        var _bid = $(this).attr('data-bid');
        layer.open({
            type: 1,
            title: $('.J_title').html() +'&nbsp;--&nbsp;活动报名',
            area: '700px',
            shift: 0,
            skin: 'layerbm-layer',
            shade: 0.6,
            closeBtn: 1,
            shadeClose: true,
            content: $('#J_layerbm_tmpl').html(),
            success: function(layero, __index) {
                layero.find('.layui-layer-content').css({
                    'overflow': 'visible'
                });
                layero.find('.layui-layer-content li.li0 em').text(_text);
                layero.find('.layui-layer-content input[name="series_id"]').val(_sid);
                layero.find('.layui-layer-content input[name="brand_id"]').val(_bid);


                layero.find('.layui-layer-close').removeClass('layui-layer-ico').addClass('icon')
                layero.find('.mod-layermsg a').on('click', function(){
                    layer.close(__index);
                });
                // 4S店
                screen('.J_b_city', '请选择4S店', 0, function(dom,id){
                    if(id == 0){
                        dom.find('input[name="dealer"]').val(0);
                    }else{
                        dom.find('input[name="dealer"]').val(id);
                    }
                });

                formEnroll(layero.find('#J_formEnroll2'));
            }
        });
    });

    //点击车型
    $('.mod-style .style-box a').on('click', function(){
        var _sid = $(this).attr('data-sid');
        var _bid = $(this).attr('data-bid');
        $(this).addClass('on').siblings('a').removeClass('on');
        $('.mod-enroll input[name="series_id"]').val(_sid);
        $('.mod-enroll input[name="brand_id"]').val(_bid);
        $('.mod-style .style-bottom span').text($(this).text());

    });

    $('.coupon-btn').on('click',function () {
        var coupon_id = $(this).attr('data-coupon');
        var tpl = $('#J_layerCoupon_tmpl_'+coupon_id).html();
        layer.open({
            type: 1,
            title: '&nbsp;&nbsp;',
            area: '500px',
            shift: 0,
            skin: 'layerbm-layer',
            shade: 0.6,
            closeBtn: 1,
            shadeClose: true,
            content:tpl,
            success: function(layero, __index) {
                $(layero).addClass('coupon_layer_box');
                layero.find('.layui-layer-content').css({
                    'overflow': 'visible'
                });
                layero.find('.layui-layer-close').removeClass('layui-layer-ico').addClass('icon')
                layero.find('.mod-layermsg a').on('click', function(){
                    layer.close(__index);
                });
                formConponSubmit(layero.find('#J_formEnroll_'+coupon_id));
            }
        });

    })

    function formConponSubmit($formEnroll){
        $formEnroll.on('submit', function(){
            $formEnroll.find('#msg').text('');
            var _mobile = $formEnroll.find('input[name="mobile"]').val();
            if(_mobile == ''){
                $formEnroll.find('#msg').text('手机号码不能为空');
                return false;
            }
            if(!/^(13[0-9]|15[0-9]|18[0-9])\d{8}$/.test(_mobile)){
                //layer.msg('手机号码不正确');
                $formEnroll.find('#msg').text('手机号码不正确');
                return false;
            }
            var _btn_text = $formEnroll.find('input[type="submit"]').val();
            $formEnroll.find('input[type="submit"]').attr('disabled', true).attr('data-text', _btn_text).val('提交中...');
            $.ajax({
                url: $formEnroll.attr('action'),
                data: $formEnroll.serialize(),
                type: $formEnroll.attr('method'),
                cache: false,
                dataType: 'json',
                success:function(data){
                    if (data.code == 1) {
                        $formEnroll.find('input[name="mobile"]').val('');
                        layerCouponMsg();
                    }else if(data.code == 0|| data.code == '-1'){
                        $formEnroll.find('#msg').text(data.msg);
                    } else {
                        $formEnroll.find('#msg').text('数据异常，请稍后!');
                    }
                }
            }).always(function() {
                var _btn_text = $formEnroll.find('input[type="submit"]').attr('data-text');
                $formEnroll.find('input[type="submit"]').removeAttr('disabled').val(_btn_text);
            });
            return false;
        })
    }
    //提示
    function layerCouponMsg(){
        layer.closeAll();
        layer.open({
            type: 1,
            title: '领取成功',
            area: '500px',
            shift: 0,
            skin: 'layermsg-layer',
            shade: 0.6,
            closeBtn: 1,
            shadeClose: true,
            content: $('#J_layermsg2_tmpl').html(),
            success: function(layero, __index) {
                layero.find('.layui-layer-title').css({
                    'backgroundColor':'#c00000',
                    'borderColor':'#c00000'
                });
                layero.find('.layui-layer-content').css({
                    'overflow': 'visible',
                    'backgroundColor':'#c00000'
                });

                layero.find('.layui-layer-close').removeClass('layui-layer-ico').addClass('icon')
                layero.find('.mod-layermsg a').on('click', function(){
                    layer.close(__index);
                });
            }
        });
    }

});