<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2018-10-31
 * Time: 14:20
 */

namespace app\goods\service;


use app\goods\model\CouponModel;
use think\Collection;

class CouponService
{


    public function getCoupon($coupon_ids){
        $coupon_ids = array_filter(explode(',',$coupon_ids));
        $couponModel = new CouponModel();
        $coupons = $couponModel->where('id','in',$coupon_ids)->order('update_time desc,create_time desc')->select();
        $data =   $coupons->toArray();
        if( $data ){
            $type_txt = ['优惠券','抵用券','折扣券','特权券'];
            foreach ($data as &$item){
                $item['img_pre'] = $item['img']?cmf_get_image_url($item['img']):'';
                $item['type_name'] = $type_txt[$item['type']];
            }
        }
     return $data;
    }
    public function getDealerList( $type ='all',$brand = 0){
        $dealerModel = new DealerModel();
        $where = [];
        if( isset($type) && $type !== 'all' ){
            $where[] = ['type','=',intval($type)];
        }
        if($brand){
            $where[] = "FIND_IN_SET({$brand},main_brand)";
        }
        if($where){
            $where = function ($query) use ($where) {
                foreach ($where as $k => $v) {
                    if (is_array($v)) {
                        call_user_func_array([$query, 'where'], $v);
                    } else {
                        $query->where($v);
                    }
                }
            };
        }
        $data = $dealerModel->where($where)->select();
        $list = Collection::make($data)->toArray();
        return $list;
    }





}