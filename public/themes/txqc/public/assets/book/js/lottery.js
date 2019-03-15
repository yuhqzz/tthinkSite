$(function () {
    $('.coupon-btn').on('click',function () {
        var coupon_id = $(this).attr('data-coupon');
        var tpl = $('#J_layerCoupon_tmpl_'+coupon_id).html();


    })
})

