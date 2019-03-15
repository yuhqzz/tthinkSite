<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\order\model;

use app\goods\model\CouponModel;
use think\Model;

class OrderCouponModel extends Model
{

    protected $name = 'order_coupon';
    /**
     * @param $data
     * @return bool|int|string
     */
    public function addOrderCouponData($data){
        if(empty($data)) return false;
        $add['coupon_id'] = (int)$data['coupon_id'];
        $add['telephone'] = $data['telephone'];
        $add['code'] = $data['code'];
        $add['create_time'] = isset($data['create_time'])?$data['create_time']:time();
        $add['status'] = isset($data['status'])?$data['status']:1;
        $add['ip'] = get_client_ip(0,true); // ip
        $add['remark'] = isset($data['remark'])?$data['remark']:'';
        $add['req_domain'] = cmf_get_domain();
        $id = $this->isUpdate(false)->allowField(true)->insertGetId($add);
        return $id;
    }

    public function coupon(){
        return $this->hasOne('app\goods\model\CouponModel','id','coupon_id','', 'INNER')->setEagerlyType(0);
    }
}

