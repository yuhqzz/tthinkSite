
(function($) {
    $.countdown = function(el, options) {
        var getDateData,
            _this = this;
        this.el = el;
        this.$el = $(el);
        this.$el.attr("data-countdown", this);
        this.init = function() {
            _this.options = $.extend({}, $.countdown.defaultOptions, options);
            if (typeof $(this.el).attr('data-date') != 'undefined') {
                _this.options.date = $(this.el).attr('data-date');

            }
            if (_this.options.refresh) {
                _this.interval = setInterval(function() {
                    return _this.render();
                }, _this.options.refresh);
            }
            _this.render();
            return _this;
        };
        getDateData = function(endDate) {
            var dateData, diff;
            endDate = Date.parse($.isPlainObject(_this.options.date) ? _this.options.date : new Date((_this.options.date).replace(/-/g, "/")));
            diff = (endDate - Date.parse(new Date)) / 1000;
            if (diff <= 0) {
                diff = 0;
                if (_this.interval) {
                    _this.stop();
                }
                _this.options.onEnd.apply(_this);
            } else if(_this.options.show == ''){
                $(this.el).removeClass('g-hide').show();
            } else {
                $(_this.options.show).removeClass('g-hide').show();
            }
            dateData = {
                years: 0,
                days: 0,
                hours: 0,
                min: 0,
                sec: 0,
                millisec: 0
            };
            //                    if (diff >= (365.25 * 86400)) {
            //                        dateData.years = Math.floor(diff / (365.25 * 86400));
            //                        diff -= dateData.years * 365.25 * 86400;
            //                    }
            if (diff >= 86400) {
                dateData.days = Math.floor(diff / 86400);
                diff -= dateData.days * 86400;
            }
            if (diff >= 3600) {
                dateData.hours = Math.floor(diff / 3600);
                diff -= dateData.hours * 3600;
            }
            if (diff >= 60) {
                dateData.min = Math.floor(diff / 60);
                diff -= dateData.min * 60;
            }
            dateData.sec = diff;
            return dateData;
        };
        this.leadingZeros = function(num, length) {
            if (length == null) {
                length = 2;
            }
            num = String(num);
            while (num.length < length) {
                num = "0" + num;
            }
            return num;
        };
        this.update = function(newDate) {
            _this.options.date = newDate;
            return _this;
        };
        this.render = function() {
            _this.options.render.apply(_this, [getDateData(_this.options.date)]);
            return _this;
        };
        this.stop = function() {
            if (_this.interval) {
                clearInterval(_this.interval);
            }
            _this.interval = null;
            return _this;
        };
        this.start = function(refresh) {
            if (refresh == null) {
                refresh = _this.options.refresh || $.countdown.defaultOptions.refresh;
            }
            if (_this.interval) {
                clearInterval(_this.interval);
            }
            _this.render();
            _this.options.refresh = refresh;
            _this.interval = setInterval(function() {
                return _this.render();
            }, _this.options.refresh);
            return _this;
        };
        return this.init();
    };
    $.countdown.defaultOptions = {
        date: "June 7, 2087 15:03:25",
        refresh: 1000,
        onEnd: function(){},
        autoShrink: true,
        tpl: '{d天}{h时}{m分}{s秒}',
        show: '',
        render: function(date) {
            var _html = (this.options.tpl).replace(/\{s秒\}/ig, '<s class="scd-digit-s">' + this.leadingZeros(date.sec) + '</s><s class="scd-unit-s">秒</s>').
            replace(/\{m分\}/ig, '<s class="scd-digit-m">' + this.leadingZeros(date.min) + '</s><s class="scd-unit-m">分</s>').
            replace(/\{h时\}/ig, '<s class="scd-digit-h">' + this.leadingZeros(date.hours) + '</s><s class="scd-unit-h">时</s>').
            replace(/\{d天\}/ig, '<s class="scd-digit-d">' + date.days + '</s><s class="scd-unit-d">天</s>');
            if (this.options.autoShrink) {
                if (date.days == 0 && date.hours == 0 && date.min == 0) {
                    _html = _html.replace('<s class="scd-digit-d">0</s>', '<s class="scd-digit-d">00</s>').replace('<s class="scd-digit-h">00</s>', '<s class="scd-digit-h">00</s>').replace('<s class="scd-digit-m">00</s>', '<s class="scd-digit-m">00</s>').replace('<s class="scd-unit-d">天</s>', '<s class="scd-unit-d">天</s>').replace('<s class="scd-unit-h">时</s>', '<s class="scd-unit-h">时</s>').replace('<s class="scd-unit-m">分</s>', '<s class="scd-unit-m">分</s>');
                } else if (date.days == 0 && date.hours == 0) {
                    _html = _html.replace('<s class="scd-digit-d">0</s>', '<s class="scd-digit-d">00</s>').replace('<s class="scd-digit-h">00</s>', '<s class="scd-digit-h">00</s>').replace('<s class="scd-unit-d">天</s>', '<s class="scd-unit-d">天</s>').replace('<s class="scd-unit-h">时</s>', '<s class="scd-unit-h">时</s>');
                } else if (date.days == 0) {
                    _html = _html.replace('<s class="scd-digit-d">0</s>', '<s class="scd-digit-d">00</s>').replace('<s class="scd-unit-d">天</s>', '<s class="scd-unit-d">天</s>');
                }
            }
            return $(this.el).html('' + _html);
        }
    };
    $.fn.countdown = function(options) {
        return $.each(this, function(i, el) {
            var $el;
            $el = $(el);
            if (!$el.attr('data-countdown')) {
                return $el.attr('data-countdown', new $.countdown(el, options));
            }
        });
    };
    return void 0;
})($);
