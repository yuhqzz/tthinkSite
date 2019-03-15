var pcd = [];
pcd[215] = ['广东省', '深圳市', '深圳市大兴宝锦汽车销售服务有限公司', '440361'];

var al1 = AL([{ id: 'province', text: "请选择省份"}, { id: 'city', text: "请选择城市" }, { id: 'dealer', text: "请选择经销商"}], pcd,function(){

});

AF.forms[0].onpass = function (data) {
    for (var i = 0, len = pcd.length; i < len; i++) {
        if(pcd[i]!=null) {
            if (pcd[i][1] == data.city && pcd[i][2] == data.dealer) {
                data.dealerid = pcd[i][3];
                break;
            }
        }
    }
    data.intentcar = AF.$dtext("intentbrandid");
    return true;
};
$("#province").val('广东');
$("#city").val('深圳');
$(".top3 select").selectpick({});
AF.forms[0].oncall = function (re, data) {
    if (re.status == 1) {
        AF.alert(re.msg);
        al1.bind();
        this.form.reset();
        $(".top3 select").resetSelect();
    } else {
        AF.alert(re.msg);
    }
}

