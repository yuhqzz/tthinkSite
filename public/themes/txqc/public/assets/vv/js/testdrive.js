$(function(){
	var formObj = $('#v-testdrive');
	$('input[name="da[media]"]').val( getParam('media') );
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
		}else if ( !(/^(?:13\d|15\d|17\d|18\d|145|147)-?\d{5}(\d{3}|\*{3})$/.test(mobile)) ) {
			showJsTip('请填写正确的手机号');return false;
		}
		if(!agree){
			showJsTip('请同意协议');return false;
		}
		var _t= $(this);
		if(_t.attr('data-ajax') == 1) {
			showJsTip('嗨，网络连接不给力，程序君正在努力提交...');
			setTimeout(function(){
				_t.attr('data-ajax') == 0;
			},1500);
			return false;
		}
		_t.attr('data-ajax', 1);	
		//var ajaxUrl = MClient == "pc" ? 'app/index.php/Index/api' : '../app/index.php/Index/api';
		//var ajaxUrl = $('#v-testdrive').attr('action');
		var ajaxUrl = '/portal/vv/ajaxSubmitTestDrive';
		$.post(ajaxUrl,formObj.serialize(), function(d){
			_t.attr('data-ajax',0);
			if(d.code==1){
				setTimeout(function(){
					showSucess();
					loadTestdrive();
				},600);
			}else{
				showJsTip(d.msg);
			}
		},'json');
    });
});
