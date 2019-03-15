<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2018-11-05
 * Time: 10:40
 */

namespace app\goods\controller;

use app\goods\api\BrandApi;
use app\goods\model\CouponModel;
use think\Controller;
use think\Request;
class AjaxController extends Controller
{

    public function brand(){
        $brandapi = new BrandApi();
        return $brandapi->index(\request());
    }

    public function coupon(){
        if($this->request->isAjax()){
            $id = $this->request->param('id');
            $coupon = CouponModel::get($id)->toArray();
            if( !$coupon || $coupon['delete_time']>0 || $coupon['status'] == 0){
                $this->result([],0,'优惠券不可用');
            }
            $type_txt = ['优惠券','抵用券','折扣券','特权券'];
            $data['id'] = $coupon['id'];
            $data['title'] = $coupon['title'];
            $data['type'] = $type_txt[$coupon['type']];
            $data['quantity'] = $coupon['quantity'];
            $data['img'] = cmf_get_image_url($coupon['img']);
            $data['coupon_price'] = $coupon['coupon_price'];
            $data['using_cars'] = $coupon['using_cars'];
            $data['description'] = $coupon['description'];
            $data['expire_time'] = $coupon['expire_time'];
            $data['create_time'] = $coupon['create_time'];
            $this->result($data,1);
        }
        $this->result([],0,'非法请求');
    }

}