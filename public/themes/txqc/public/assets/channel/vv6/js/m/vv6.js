$(function(){
    var is_submit = false;
    var is_ajax_request = 0;
    $("#btnSubmit").on('click',function(event){
        var c_that = this;
        event.preventDefault();
        if (is_submit || is_ajax_request) {
            error('请不要重复提交哦');
            return;
        }
        $(c_that).attr('is_ajax_request',1);
        var formObj = $('form[name="vv6TestDriveForm"]');
        var url = formObj.attr('action');
        var seriesVal = formObj.find('select[name="series_id"]').val();
        var customerNameVal = formObj.find('input[name="customer_name"]').val();
        var mobileVal = formObj.find('input[name="mobile"]').val();
        if (seriesVal == undefined || seriesVal == 0) {
            errorMsg('请选择车型');
            $(c_that).attr('is_ajax_request',0);
            return;
        }
        if (customerNameVal == undefined || customerNameVal == 0) {
            errorMsg('请填写您的姓名');
            $(c_that).attr('is_ajax_request',0);
            return;
        }
        if (mobileVal == undefined || mobileVal == 0) {
            errorMsg('请填写您的手机号码');
            $(c_that).attr('is_ajax_request',0);
            return;
        } else if (mobileVal.length !== 11 && !/^(13[0-9]|15[0-9]|18[0-9])\d{8}$/.test(mobileVal)) {
            errorMsg('请填写正确手机号码');
            $(c_that).attr('is_ajax_request',0);
            return;
        }
        //var datas = {
        //    'series_id': seriesVal,
        //    'customer_name': customerNameVal,
        //    'mobile': mobileVal
        //};
        var datas = formObj.serialize();
        //console.log(datas);
        is_submit = true;
        layer.open({
            type: 2,
            content: '数据提交中...',
            time: 2
        });
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            data: datas,
            complete:function(XMLHttpRequest, textStatus){
                is_submit = false;
                $(c_that).attr('is_ajax_request',0);
            },
            success: function (mydata) {
            console.log(mydata);
                if( mydata.code == 1){
                    successMsg('您已成功提交！感谢您的参与，我们将会在24小时内安排客服人员与您联系');
                    $('.absreset').val('');
                    is_submit = false;
                    $(c_that).attr('is_ajax_request',0);
                }else{
                    $(c_that).attr('is_ajax_request',0);
                    errorMsg(mydata.msg);
                }
            }
        })



    })
    function errorMsg(msg) {
        layer.open({
            content: msg,
            skin: 'msg',
            style: 'font-size:24px;padding:10px;color:red;',
            time: 2
        })

    };
    function successMsg(info){
        layer.open({
            content:info,
            skin: 'msg',
            style: 'padding:10px;color:green;',
            time: 2
        })
    }

});



