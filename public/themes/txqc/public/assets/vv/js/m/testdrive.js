$(function(){
	var formObj = $('#v-testdrive');
	$('input[name="da[media]"]').val('mobile' );
    formObj.find('.btn_submit').click(function(){
	    var car = formObj.find('select[name="da[car]"]').val(),
			name = formObj.find('input[name="da[name]"]').val(),
			mobile = formObj.find('input[name="da[mobile]"]').val(),
			province = formObj.find('input[name="da[province]"]').val(),
			city = formObj.find('input[name="da[city]"]').val(),
			dealer = formObj.find('input[name="da[dealer]"]').val(),
			agree = formObj.find('input[name="da[agree]"]').is(':checked');
	    if(car==''){
			showJsTip('请选择车型');return false;
		}
		if(name==''){
			showJsTip('姓名不能为空');return false;
		}else if( !(/^[A-Za-z \u4e00-\u9fa5]+$/.test( name )) ){
			showJsTip('请正确填写姓名');return false;
		}
		if(mobile==''){
			showJsTip('手机号不能为空');return false;
		}else if (!(/^(?:13\d|15\d|17\d|18\d|145|147)-?\d{5}(\d{3}|\*{3})$/.test(mobile)) ) {
			showJsTip('请填写正确的手机号');return false;
		}
		if(!agree){
			showJsTip('请同意协议');return false;
		}
		var ajaxing = false;
		var _t=$(this);
		if(ajaxing) {
			showJsTip('嗨，网络连接不给力，程序君正在努力提交...');
			return false;
		}
		ajaxing = true;
		formMobile = mobile;
		var ajaxUrl = '/portal/vv/ajaxSubmitTestDrive';
		$.ajax({
			url:ajaxUrl,
			data:formObj.serialize(),
			type:"post",
			dataType:"json",
			success: function(d){
				ajaxing = false;
				if(d.code == 1){
					showSucess();
					setTimeout(function(){
						loadTestdrive();
					},600);
				}else{
					showJsTip(d.msg);
				}
				isTestDrive = true;
			},
			error:function(){
				ajaxing = false;
			}
		})
	});
	$('.show_suc_cont .exit').on('click', function(){
		$('.showSuccess').fadeOut();
	});
});

function showSucess(){
	$(".showSuccess").show();
	$('.show_suc_cont dt').text('提交成功');
	$('.show_suc_cont dd').html('销售顾问将在24小时内联系您，将此页面分享至朋友圈，<br />还可免费获得3000元购车礼包呦～');
}