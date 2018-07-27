(function($) {

	$.platforms = {};
	$.platforms.is = {
		init: function(navigator) {
			var platforms = this.platforms,
				ln = platforms.length,
				i, platform;

			navigator = navigator || window.navigator;

			for (i = 0; i < ln; i++) {
				platform = platforms[i];
				this[platform.identity] = platform.regex.test(navigator[platform.property]);
			}


			this.Desktop = this.Mac || this.Windows || (this.Linux && !this.Android);

			this.Tablet = this.iPad;

			this.Phone = !this.Desktop && !this.Tablet;

			this.iOS = this.iPhone || this.iPad || this.iPod;


			this.Standalone = !!window.navigator.standalone;
		},


		platforms: [{
				property: 'platform',
				regex: /iPhone/i,
				identity: 'iPhone'
			},


			{
				property: 'platform',
				regex: /iPod/i,
				identity: 'iPod'
			},


			{
				property: 'userAgent',
				regex: /iPad/i,
				identity: 'iPad'
			},


			{
				property: 'userAgent',
				regex: /Blackberry/i,
				identity: 'Blackberry'
			},


			{
				property: 'userAgent',
				regex: /Android/i,
				identity: 'Android'
			},


			{
				property: 'platform',
				regex: /Mac/i,
				identity: 'Mac'
			},


			{
				property: 'platform',
				regex: /Win/i,
				identity: 'Windows'
			},


			{
				property: 'platform',
				regex: /Linux/i,
				identity: 'Linux'
			}
		]
	};

	$.platforms.is.init();

	var userAgent = navigator.userAgent.toLowerCase();


	var check = function(regex) {
			return regex.test(userAgent);
		},
		isStrict = document.compatMode == "CSS1Compat",
		version = function(is, regex) {
			var m;
			return (is && (m = regex.exec(userAgent))) ? parseFloat(m[1]) : 0;
		},
		docMode = document.documentMode,
		isOpera = check(/opera/),
		isOpera10_5 = isOpera && check(/version\/10\.5/),
		isChrome = check(/\bchrome\b/),
		isWebKit = check(/webkit/),
		isSafari = !isChrome && check(/safari/),
		isSafari2 = isSafari && check(/applewebkit\/4/),
		isSafari3 = isSafari && check(/version\/3/),
		isSafari4 = isSafari && check(/version\/4/),
		isSafari5_0 = isSafari && check(/version\/5\.0/),
		isSafari5 = isSafari && check(/version\/5/),
		isIE = !isOpera && check(/msie/),
		isIE7 = isIE && ((check(/msie 7/) && docMode != 8 && docMode != 9) || docMode == 7),
		isIE8 = isIE && ((check(/msie 8/) && docMode != 7 && docMode != 9) || docMode == 8),
		isIE9 = isIE && ((check(/msie 9/) && docMode != 7 && docMode != 8) || docMode == 9),
		isIE6 = isIE && check(/msie 6/),
		isGecko = !isWebKit && check(/gecko/),
		isGecko3 = isGecko && check(/rv:1\.9/),
		isGecko4 = isGecko && check(/rv:2\.0/),
		isGecko5 = isGecko && check(/rv:5\./),
		isGecko10 = isGecko && check(/rv:10\./),
		isFF3_0 = isGecko3 && check(/rv:1\.9\.0/),
		isFF3_5 = isGecko3 && check(/rv:1\.9\.1/),
		isFF3_6 = isGecko3 && check(/rv:1\.9\.2/),
		isWindows = check(/windows|win32/),
		isMac = check(/macintosh|mac os x/),
		isLinux = check(/linux/),
		scrollbarSize = null,
		chromeVersion = version(true, /\bchrome\/(\d+\.\d+)/),
		firefoxVersion = version(true, /\bfirefox\/(\d+\.\d+)/),
		ieVersion = version(isIE, /msie (\d+\.\d+)/),
		operaVersion = version(isOpera, /version\/(\d+\.\d+)/),
		safariVersion = version(isSafari, /version\/(\d+\.\d+)/),
		webKitVersion = version(isWebKit, /webkit\/(\d+\.\d+)/),
		isSecure = /^https/i.test(window.location.protocol),
		nullLog;

	try {
		document.execCommand("BackgroundImageCache", false, true);
	} catch (e) {}


	$.extend($.platforms, {

		isStrict: isStrict,

		isIEQuirks: isIE && !isStrict,


		isOpera: isOpera,


		isOpera10_5: isOpera10_5,


		isWebKit: isWebKit,


		isChrome: isChrome,


		isSafari: isSafari,


		isSafari3: isSafari3,


		isSafari4: isSafari4,


		isSafari5: isSafari5,


		isSafari5_0: isSafari5_0,



		isSafari2: isSafari2,


		isIE: isIE,


		isIE6: isIE6,


		isIE7: isIE7,


		isIE7m: isIE6 || isIE7,


		isIE7p: isIE && !isIE6,


		isIE8: isIE8,


		isIE8m: isIE6 || isIE7 || isIE8,


		isIE8p: isIE && !(isIE6 || isIE7),


		isIE9: isIE9,


		isIE9m: isIE6 || isIE7 || isIE8 || isIE9,


		isIE9p: isIE && !(isIE6 || isIE7 || isIE8),


		isGecko: isGecko,


		isGecko3: isGecko3,


		isGecko4: isGecko4,


		isGecko5: isGecko5,


		isGecko10: isGecko10,


		isFF3_0: isFF3_0,


		isFF3_5: isFF3_5,


		isFF3_6: isFF3_6,


		isFF4: 4 <= firefoxVersion && firefoxVersion < 5,


		isFF5: 5 <= firefoxVersion && firefoxVersion < 6,


		isFF10: 10 <= firefoxVersion && firefoxVersion < 11,


		isLinux: isLinux,


		isWindows: isWindows,


		isMac: isMac,


		chromeVersion: chromeVersion,


		firefoxVersion: firefoxVersion,


		ieVersion: ieVersion,


		operaVersion: operaVersion,


		safariVersion: safariVersion,


		webKitVersion: webKitVersion,


		isSecure: isSecure

	});

	function _getChromiumType() {
        if (isIe || win.scrollMaxX !== undefined) return '';

        var isOriginalChrome = _mime('type', 'application/vnd.chromium.remoting-viewer');

        // 原始 chrome
        if (isOriginalChrome) {
            return 'chrome';
        }
        // 谷歌、火狐、ie的某些版本也有 window.chrome 属性
        // 需先排除
        else if ( win.chrome) {
            var _track = 'track' in doc.createElement('track'),
                _style = 'scoped' in doc.createElement('style'),
                _v8locale = 'v8Locale' in win,
                external = win.external;

            // 搜狗浏览器
            if ( external && 'SEVersion' in external) return 'sougou';

            // 猎豹浏览器
            if ( external && 'LiebaoGetVersion' in external) return 'liebao';

            // 360极速浏览器
            if (_track && !_style && !_v8locale && /Gecko\)\s+Chrome/.test(appVersion)) return '360ee';

            // 360安全浏览器
            if (_track && _style && _v8locale) return '360se';

            return 'other chrome';
        }
        return '';
    }


})(jQuery);