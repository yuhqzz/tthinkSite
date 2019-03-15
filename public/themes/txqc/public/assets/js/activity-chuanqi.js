$(function () {

    var $w = $(window);
    var _H = $w.height();
    var sign = $('input[name="user_token"]').val();
    var isLogin = getCookie('isLogin');
    if(sign && isLogin == 1){
        if(getUrlParam('sign') == ''){
            redirect(goUrlParam(sign));
        }else{
            var current_sign = getUrlParam('sign');
            if( current_sign !== sign ){
                redirect(goUrlParam(sign));
            }
        }
    }

    $('.nav-toolbar li').bind('click',function(){
        var index = $(this).index();
        $(this).addClass('active').siblings('li').removeClass('active');
        $('.act-detail .act').eq(index).show().addClass('active').siblings('.act').hide().removeClass('active');
        //$('.act').css('transform','none');
        if( index == 2 ){
            $('.act-operate').hide();
        }else{
            $('.act-operate').show();
        }
        setCookie('tabindex',index);
    })
    var tabindex = getCookie('tabindex')||0;
    $('.nav-toolbar li').eq(tabindex).click();


    $('.act-btn').bind('click',function(){
        var that = this;
        var html = $("#act-submit-form-tpl").html();
        if($(that).attr('disabled')){
            layer.open({
                content: '每人只能抽奖一次,分享好友助力也能获得兑奖码哦，赶快分享好友助力吧！',
                skin: 'msg',
                time: 6
            })
            return false;
        }
        var btn = {};
        var isajax = 0;
        layer.open({
            content: html,
            btn: '立即报名',
            shadeClose: true,
            yes: function(){
                var formObj = $("form[name='act-submit-form']");
               // console.log(btn.find('span').attr('isajax'));
                var yes_btn_v = btn.find('span').attr('isajax')|0;
                var info_dom = formObj.find('.down_info');
                if( yes_btn_v == 1){
                    info_dom.show().css('color','red').html('数据在提交中，请稍后！');
                    setTimeout(function(){
                        info_dom.html('');
                    },1000);
                    return false;
                }else if(yes_btn_v == 2){
                    info_dom.show().css('color','red').html('已报名请勿重复报名！分享好友助力可以获取兑奖码哦。');
                    setTimeout(function(){
                        info_dom.html('');
                    },1000);
                    return false;
                }

                var url = formObj.attr('action');
                var method = formObj.attr('method');
                var mobile = formObj.find("input[name='phone']").val();
                if( mobile == '' ){
                    info_dom.css('color','red').html('输入您的手机号码');
                    return false;
                }
                if(mobile.length !== 11){
                    info_dom.css('color','red').html('输入正确的手机号码');
                    return false
                }
                if(!istel(mobile)){
                    info_dom.css('color','red').html('输入正确的手机号码');
                    return false
                }
                var org_sign = getUrlParam('sign');
                if(org_sign){
                    formObj.find("input[name='org_sign']").val(org_sign);
                }
                var data = formObj.serialize();
                btn.find('span').attr('isajax',1);
                $(that).attr('disabled', true).text('报名中');
                btn.find('span').text('报名中');
                info_dom.css('color','#24825c').html('报名中...');
                $.ajax({
                    type: method,
                    url: url,
                    data: data,
                    dataType: 'json',
                    cache: false,
                    async:true,
                    success: function(_json) {
                        if(_json.code == 1 ){
                            info_dom.css('color','#12e012').html('您的兑奖码 '+_json.data.code+' 进行分享可以获取更多兑奖码。');
                            $(that).attr('disabled', true).text('待开奖');
                            btn.find('span').attr('isajax',2).text('报名成功');
                            setTimeout(function(){
                                redirect(goUrlParam(_json.data.token));

                            },3000);
                        }else if( _json.code == 0){
                            info_dom.css('color','red').html(_json.msg);
                            $(that).attr('disabled', false).text('我要抽奖');
                            btn.find('span').attr('isajax',0).text('立即报名');
                        }else{
                            info_dom.css('color','red').html(_json.msg);
                            $(that).attr('disabled', false).text('我要抽奖');
                            btn.find('span').attr('isajax',0).text('立即报名');
                        }
                    },
                    error: function(xhr, errorType, error) {
                        info_dom.css('color','red').html('系统出错了,程序正在努力修复中！');
                        $(that).attr('disabled', false).text('我要抽奖');
                        btn.find('span').attr('isajax',0).text('立即报名');
                    }
                });
            },
            success: function(elem){
                btn = $(elem).find('.layui-m-layerbtn');
                $(elem).find('.layui-m-layercont').css('paddingRight','10px').css('paddingLeft','10px');
                //
                var form = $(elem).find('form[name="act-submit-form"]');
                var str_code = createRandomId(6);
                var info_str = '恭喜您!获得一个兑奖码！';
                info_str +='<span class="c_txt">'+str_code+'</span>';
                form.find('.info').html(info_str).css('color','#12e012');
                form.find('input[name="act_code"]').val(str_code);
            }
        });

    });
    $('#login-btn').bind('click',function(){
        var that = this;
        var html = $("#login-form-tpl").html();
        var btn = {};
        var isajax = 0;
        layer.open({
            content: html,
            btn: ['登录','取消'],
            shadeClose: false,
            yes: function(){
                var formObj = $("form[name='act-login-form']");
                // console.log(btn.find('span').attr('isajax'));
                var yes_btn = btn.find('span[type="1"]');

                var yes_btn_v = btn.find('span[type="1"]').attr('isajax')|0;
                var info_dom = formObj.find('.info');
                if( yes_btn_v == 1){
                    info_dom.show().css('color','#12e012').html('登录中，请稍后！');
                    setTimeout(function(){
                        info_dom.html('');
                    },1000);
                    return false;
                }
                var url = formObj.attr('action');
                var method = formObj.attr('method');
                var mobile = formObj.find("input[name='phone']").val();
                if( mobile == '' ){
                    info_dom.css('color','red').html('输入您的手机号码');
                    return false;
                }
                if(mobile.length !== 11){
                    info_dom.css('color','red').html('输入正确的手机号码');
                    return false
                }
                if(!istel(mobile)){
                    info_dom.css('color','red').html('输入正确的手机号码');
                    return false
                }
                var data = formObj.serialize();
                yes_btn.attr('isajax',1);
                yes_btn.text('登陆中');
                info_dom.css('color','#12e012').html('登录中...');
                $.ajax({
                    type: method,
                    url: url,
                    data: data,
                    dataType: 'json',
                    cache: true,
                    async:true,
                    success: function(_json) {
                        if(_json.code == 1){
                            layer.open({
                                content: '登录成功!',
                                skin: 'msg',
                                time: 2
                            });
                            setCookie('tabindex',2);
                            setTimeout(function(){
                                reloadPage(window);
                            },2000);
                        }else if(_json.code == 0){
                            info_dom.css('color','red').html(_json.msg);
                            yes_btn.text('登录');
                            yes_btn.attr('isajax',0);
                        }else {
                            info_dom.css('color','red').html('系统出错了,程序正在努力修复中！');
                            yes_btn.text('登录');
                            yes_btn.attr('isajax',0);
                        }
                    },
                    error: function(xhr, errorType, error) {
                        info_dom.css('color','red').html('系统出错了,程序正在努力修复中！');
                        yes_btn.attr('isajax',0);
                    }
                });
            },
            success: function(elem){
                btn = $(elem).find('.layui-m-layerbtn');
            }
        });

    });
    $('#logout-btn').bind('click',function(){
        var _this = this;
        setCookie('isLogin',null);
        setCookie('tabindex',0);
        var uid =  $(_this).attr('data-uid');
        var  act_url = $(_this).attr('data-act');
        $.ajax({
            type: 'post',
            url: act_url,
            data: {'user_id':uid},
            dataType: 'json',
            cache: false,
            async:true,
            success: function(_json) {
                if(_json.code == 1 ){
                    layer.open({
                        content: '退出成功！',
                        skin: 'msg',
                        time: 2
                    });
                    setTimeout(function(){
                        reloadPage(window);
                    },1000);
                }else{
                    layer.open({
                        content: '退出失败！',
                        skin: 'msg',
                        time: 2
                    });
                }
            }
        });

    })

    function istel(tel) {
        var rtn = false;
        //联通号段
        var regtel = /^1([3-9])\d{9}$/;
        if (regtel.test(tel)) {
            rtn = true;
        }
        return rtn;
    }


    //重新刷新页面，使用location.reload()有可能导致重新提交
    function reloadPage(win) {
        var location  = win.location;
        location.href = location.pathname + location.search;
    }


    //页面跳转
    function redirect(url) {
        location.href = url;
    }
    //设置url中参数值
    function setParam(param, value) {
        var query = location.search.substring(1);
        var p = new RegExp("(^|)" + param + "=([^&]*)(|$)");
        if (p.test(query)) {
            var firstParam = query.split(param)[0];
            var secondParam = query.split(param)[1];
            if (secondParam.indexOf("&") > -1) {
                var lastPraam = secondParam.substring(secondParam.indexOf('&')+1);
                return '?' + firstParam + param + '=' + value + '&' + lastPraam;
            } else {
                if (firstParam) {
                    return '?' + firstParam + param + '=' + value;
                } else {
                    return '?' + param + '=' + value;
                }
            }
        } else {
            if (query == '') {
                return '?' + param + '=' + value;
            } else {
                return '?' + query + '&' + param + '=' + value;
            }
        }
    }
    function goUrlParam(sign) {
        var url = window.location.href; //获取当前url
        if (url.indexOf("?")>0) {
            url = url.split("?")[0];
        }
        return url + setParam("sign", sign);
    }

    /**
     * 读取cookie
     * @param name
     * @returns
     */
    function getCookie(name) {
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }

    /**
     * 设置cookie
     */
    function setCookie(name, value, options) {
        options = options || {};
        if (value === null) {
            value           = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        var path        = options.path ? '; path=' + options.path : '';
        var domain      = options.domain ? '; domain=' + options.domain : '';
        var secure      = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    }

    /**
     * 生成兑奖码
     * @param num
     * @returns {number}
     */
    function createRandomId(num) {
            var random = Math.floor((Math.random()+Math.floor(Math.random()*9+1))*Math.pow(10,num-1));
         return random;
    }

    /*
     ** randomWord 产生任意长度随机字母数字组合
     ** randomFlag-是否任意长度 min-任意长度最小位[固定位数] max-任意长度最大位
     **
     */

    function randomWord(randomFlag, min, max){
        var str = "",
            range = min,
            arr = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

        // 随机产生
        if(randomFlag){
            range = Math.round(Math.random() * (max-min)) + min;
        }
        for(var i=0; i<range; i++){
            pos = Math.round(Math.random() * (arr.length-1));
            str += arr[pos];
        }
        return str;
    }

    function getUrlParam(name)
    {
        // 如果链接没有参数，或者链接中不存在我们要获取的参数，直接返回空
        if (location.href.indexOf("?") == -1 || location.href.indexOf(name + '=') == -1) {
            return '';
        }
        // 获取链接中参数部分
        var queryString = location.href.substring(location.href.indexOf("?") + 1);
        queryString = decodeURI(queryString);
        //decodeURI 解码
        //encodeURI 转码


        // 分离参数对 ?key=value&key2=value2
        var parameters = queryString.split("&");


        var pos, paraName, paraValue;
        for (var i = 0; i < parameters.length; i++) {
            // 获取等号位置
            pos = parameters[i].indexOf('=');
            if (pos == -1) { continue; }


            // 获取name 和 value
            paraName = parameters[i].substring(0, pos);
            paraValue = parameters[i].substring(pos + 1);


            // 如果查询的name等于当前name，就返回当前值，同时，将链接中的+号还原成空格
            if (paraName == name) {
                return unescape(paraValue.replace(/\+/g, " "));
            }
        }
        return '';
    }





});