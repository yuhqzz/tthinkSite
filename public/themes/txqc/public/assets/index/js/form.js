$(function(){
  testDrivForm._formSubmit('v-testdrive');
  $("#v-testdrive-2").hide();
  $(window).bind('scroll',function(e){
    var sTop=$(window).scrollTop();
    var showH = 1081;
    if(window.innerWidth == 1920){
      showH = 1081;
    }else if(window.innerWidth == 1707){
      showH = 1038;
    }else if(window.innerWidth == 1280){
      showH = 815;
    }
    if(sTop>=showH){
      $("#v-testdrive-2").fadeIn();
    }else{
      $("#v-testdrive-2").fadeOut();
    }
  });


  testDrivForm._formSubmit('v-testdrive-2');
});
var testDrivForm = {};
var showJsTip = function(msg){
  Wind.css('layer');
  Wind.use("layer", function(){
    layer.alert(msg,{icon:2,skin: 'layui-layer-lan',anim: 1});
  });

};
var  showSuccess = function(msg){
  Wind.css('layer');
  Wind.use("layer", function(){
    layer.alert(msg,{icon:1,skin: 'layui-layer-lan',anim: 1});
  });
}
testDrivForm._formSubmit = function(formName){
  var formObj = $("form[name='"+formName+"']");
  formObj.find('.btn_submit').click(function(){
    var series_id = formObj.find('select[name="series_id"]').val(),
        name = formObj.find('input[name="customer_name"]').val(),
        mobile = formObj.find('input[name="mobile"]').val();
    if(series_id==''){
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
    var _t = $(this);
    if(_t.attr('data-ajax') == 1) {
      showJsTip('嗨，网络连接不给力，程序君正在努力提交...');
      setTimeout(function(){
        _t.attr('data-ajax') == 0;
      },1500);
      return false;
    }
    _t.attr('data-ajax', 1);
    var ajaxUrl = formObj.attr('action');
    $.post(ajaxUrl,formObj.serialize(), function(res){
      _t.attr('data-ajax',0);
      if(res.code==1){
        showSuccess('您已成功报名！感谢您的参与，我们将会在24小时内安排客服人员与您联系。');
      }else{
        showJsTip(res.msg);
      }
    },'json');
  });
}