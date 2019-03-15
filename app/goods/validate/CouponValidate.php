<?php
namespace app\goods\validate;

use think\Validate;

class CouponValidate extends Validate
{
    protected $rule = [
        'title'  => 'require',
        'type'  => 'require',
        'img'  => 'require',
        'using_cars'  => 'require',
        'coupon_price'  => 'checkPriceValue',
    ];
    protected $message = [
        'title.require' => '标题不能为空',
        'type.require' => '种类不能为空',
    ];

    protected $scene = [
         'add'  => ['title,type,img','using_cars'],
         'edit'  => ['title,type,img','using_cars']
    ];
    protected function checkPriceValue($value,$rule,$data){
       if( isset($data['type']) && ($data['type'] == 1 || $data['type'] == 2) ){
           // 抵用券  折扣券 必填
           if(empty($value)){
               return '抵用券和折扣券价格或者折扣必填项！';
           }
       }
        return true;
    }



}