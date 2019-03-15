;
(function ($, window, document, undefined) {

    $.fn.selectpick = function (options) {
        // selectpick的配置
        var selectpick_config = {
            container: "body",//模拟select生成的DIV存放的父容器
            disabled: false, // 是否禁用,默认false
            family: ['province','city','dealer'], // 多级联动关联关系 //add by zz at 2017-6-20
            onSelect: "" // 点击后选中事件
        }
        var settings = $.extend({}, selectpick_config, options);
        if($(this[0]).hasClass('select_hide') && $(this[0]).next('.selectpick_div_box').length>0) return false; //add by zz at 2017-5-26, edit by zz at 2017-11-7
        // 每个下拉框组件的操作
        return this.each(function (elem_id) {
            var obj = this;
            // 生成的div的样式
            var _selectBody = "<div class='selectpick_div_box' onselectstart='return false;'><div class='selectpick_div'><span class='selectpick_chara'></span><span class='selectpick_icon'></span></div><div class='selectpick_options'></div></div>";
            $(_selectBody).appendTo($(obj).parent());
            $(this).addClass("select_hide");

            //设置默认显示在div上的值为当前select的选中值
            $(this).siblings('.selectpick_div_box').find('span:eq(0)').text(
                $(this).find("option:selected").text() || $(this).find('option[value="'+ $(this).val() +'"]').text()
            );

            // 是否禁用下拉框
            if (settings.disabled) {
                $(this).siblings('.selectpick_div_box').find(".selectpick_div").addClass("selectpick_no_select");
                return;
            }
            // 点击div显示列表
            $(this).siblings('.selectpick_div_box').bind("click", function (event) {

                $('.selectpick_div_box').css('zIndex', '9').parents('dd').removeClass('cur').prev().removeClass('cur');
                $(this).css('zIndex', '99').parents('dd').addClass('cur').prev().addClass('cur');

                var selected_text = $(this).find('.selectpick_chara').html(); // 当前div中的值
                event.stopPropagation(); //  阻止事件冒泡

                if ($(this).find('li').length > 0) {
                    // 隐藏和显示div
                    $(this).find(".selectpick_options").empty().hide();
                    $('.selectpick_div_box').css('zIndex', '9');
                    $(this).parents('dd').removeClass('cur').prev().removeClass('cur');
                    return;
                } else {
                    $(".selectpick_options").hide();
                    $(this).find(".selectpick_options").show();
                    $(".selectpick_options ul li").remove();
                    // 添加列表项
                    var ul = "<ul>";
                    $(obj).children("option").each(function () {
                        if ($(this).text() == selected_text) {
                            ul += "<li class='selectpick_options_selected'><label>" + $(this).text() + "</label></li>";
                        } else {
                            ul += "<li><label>" + $(this).text() + "</label></li>";
                        }
                    });
                    ul += "</ul>";
                    $(this).find(".selectpick_options").append(ul).show();
                    $(this).find('span:eq(0)').text(
                        $(obj).find("option:selected").text() || $(obj).find('option[value="'+ $(obj).val() +'"]').text()
                    );

                    //设置下拉框是否有滚动条以及展示方向
                    var lH = ($(this).find("li").height() + 1) * 7;
                    var lH2 = $(this).find(".selectpick_options").height() + 1;
                    var cH = $(this).offset().top + $(this).height();
                    var wH = $(window).height(),sT = $(window).scrollTop();


                    if ($(this).find("li").length > 7) {
                        $(this).find(".selectpick_options").css('height', lH + 'px');
                        if ((wH+sT) - cH < lH) {
                            $(this).find(".selectpick_options").css({
                                top: -lH + 'px'
                            })
                        }else{
                            $(this).find(".selectpick_options").css({
                                top: '-'
                            })
                        }
                    }
                    else {
                        if ((wH+sT) - cH < lH2) {
                            $(this).find(".selectpick_options").css({
                                top: -lH2 + 'px'
                            })
                        }else{
                            $(this).find(".selectpick_options").css({
                                top: '-'
                            })
                        }
                    }

                    // 每个li点击事件
                    $(this).find('li').bind("click", function (event) {
                        $(obj).siblings('.selectpick_div_box').find('span:eq(0)').text($(this).children("label:eq(0)").text());

                        $(this).parents('.selectpick_options').css('height','auto').empty().hide();
                        //$(obj).val(name);//设置下拉框的值
                        $(obj).children("option").removeAttr('selected');
                        $(obj).children("option:nth-child(" + ($(this).index() + 1) + ")").prop('selected', 'selected');
                        $(obj).change()

                        //清除联动默认值
                        var cName = $(obj).attr('id'), family_length = settings.family.length;
                        for(var i=0; i<family_length-1; i++){
                            if(cName == settings.family[i]){
                                for(var o = i+1 ; o<family_length; o++){
                                    var sf = settings.family[o];
                                    $('#'+ sf +' ~ .selectpick_div_box').find('span:eq(0)').html($('#'+ sf +' option[value="'+ $('#'+ sf).val() +'"]').text())
                                }
                                break;
                            }
                        }

                        $(obj).parents('dd').removeClass('cur').prev().removeClass('cur');

                        // 回调函数
                        settings.onSelect && settings.onSelect();
                        return false;
                    });

                }

            });

            // 点击div外面关闭列表
            $(document).bind("click", function (event) {
                var e = event || window.event;
                var elem = e.srcElement || e.target;
                //console.log(elem)
                if (elem.className == "selectpick_div" || elem.className == "selectpick_icon" || elem.className == "selectpick_chara") {
                    return;
                } else {
                    //alert(0)
                    $(obj).siblings('.selectpick_div_box').css('zIndex', '9').find(".selectpick_options").css('height','auto').empty().hide();
                    $(obj).parents('dd').removeClass('cur').prev().removeClass('cur');
                }
            });

        });
    }

    $.fn.resetSelect = function(){
        $('.selectpick_div').each(function(){
            var text = $(this).parent().siblings('select').children("option:nth-child(1)").text();
            $(this).find('.selectpick_chara').text(text);
        })
    }
})(jQuery, window, document);