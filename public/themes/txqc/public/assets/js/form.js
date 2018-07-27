(function ($) {
    var URL_SUBMIT = "";
    $.fn.serializeObject = function () {
        var me = $(this);
        if (me.is("form")) {
            var result = {};
            var data = me.serializeArray();
            for (var index = 0, len = data.length; index < len; index++) {
                var d = data[index];
                result[d.name] = d.value;
            };
            return result;
        };
        return null;
    };

    $.form = {};
    $.form.ajax = function (url, data, valueField, nameField) {
        var result = [];
        $.ajax({
            url: url,
            data: data,
            type: "GET",
            dataType: "json",
            async: false,
            success: function (data) {
                for (var index = 0, len = data.length; index < len; index++) {
                    result.push({
                        value: data[index][valueField],
                        name: data[index][nameField]
                    });
                }
            },
            error: function (e) {
    
            }
        });
        return result;
    };

    $.form.check = function (callback) {
        var form = $("div#sign-up-form form");
            URL_SUBMIT = form.attr('action');
        var data = form.serializeArray();
        var result = [];
        for (var index = 0, len = data.length; index < len; index++) {
            var d = data[index];
            if (d.value == "") {
                result.push(d.name);
            };
            if (d.name == "MOBILE") {
                var r = /^[\d]{11}$/;
                if (!r.test(d.value))
                    result.push(d.name);
            }
        };

        if (result.length == 0) {
            form.trigger("ev_check", [true]);
            return true;
        } else {
            form.trigger("ev_invalid", result);
            form.trigger("ev_check", [false]);
            return false;
        };
    };
    //提交
    $.form.submit = function (callbackSuccess, callbackFailed) {
        var _this = this;
        if ($.form.check(function () {
				var form = $(this);
				for (var index = 1, len = arguments.length; index < len; index++) {
					var name = arguments[index];
					var t = form.find("[name=" + name + "]");
					if (t.is("input")) {
						t.addClass("invalid");
                        } else if (t.is("select")) {
                						t.closest("div.combo").addClass("invalid");
                        };
                };
        }) !== true) {
            callbackFailed.call(_this);
            return false;
        };
        var form = $("div#sign-up-form form");
        var data = form.serializeObject();
        var result = false;
        var inputValue = JSON.stringify(data);

        $.getJSON(URL_SUBMIT, inputValue, function (data) {
            if (data.Success == "1") {
                result = true;
                callbackSuccess.call(_this);
            } else {
                callbackFailed.call(_this);
            };
            return result;
        });
    }

  
    $.form.pop = function (string) {
        $("div#form-pop-content").html(string);
        $("div#form-pop").stop().fadeIn(200);
        setTimeout(function () {
            $("div#form-pop").stop().fadeOut(200);
        }, 5 * 1000);
    };

    $.form.init = function () {

        $("select#slt-dealer").on("change", function (e, v) {
            $.form.check();
        });

        $("a#form-submit img").on("click", function () {
            if ($("input#chk-agree").prop("checked")) {
                var btn = $(this);
                //btn.addClass("disabled").prop("disabled", true);
                $.form.submit(function (data) {
                    $.form.pop("您已成功提交！感谢您的参与 <br/>我们将会在24小时内安排客服人员与您联系。");
                    //$("#btnReset").trigger("click");
                    $("#txt-name").val('');
                    $("#txt-mobile").val('');
                    //btn.removeClass("disabled").prop("disabled", false);
                }, function (e) {
                    $.form.pop("提交失败，请重试！");
                    $.form.check();
                });
            };

        }); //.addClass("disabled").prop("disabled", true);

        $("input#txt-name, input#txt-mobile").on("blur", function (e) {
            $.form.check();
        })
        $("input#chk-agree").prop("checked", true);
        $("label#label-agree").on("click", function () {
            $.form.check();
        });

        $.form.check();
    };

})(jQuery);