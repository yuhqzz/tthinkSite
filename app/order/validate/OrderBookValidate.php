<?php
namespace app\order\validate;

use think\Validate;

class OrderBookValidate extends Validate
{
    protected $rule = [
       'name'       => 'require',
       'dealers_id'   => 'require',
       'car_style_id'  => 'require',
       'area_id'   => 'require',
       'book_telephone'=>'require',
       'book_to_time'=>'require'
    ];
    protected $message = [
        'name.require' => '姓名不能为空',
        'dealers_id.require' => '经销商不能为空',
        'car_style_id.require' => '车型不能为空',
        'area_id.require' => '地点不允许为空',
        'book_to_time.require' => '计划到店时间不允许为空',
        'book_telephone.require' => '手机(电话)不允许为空',
    ];

    protected $scene = [
          'add'  => ['name,dealers_id,car_style_id,area_id,book_to_time,book_telephone'],
          'edit' => ['name,dealers_id,car_style_id,area_id,book_to_time,book_telephone'],
          'channel'=>['name,book_telephone,car_typeid'],
    ];
}