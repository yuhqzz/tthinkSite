<?php
namespace app\goods\validate;

use think\Validate;

class GoodsCarStyleValidate extends Validate
{
    protected $rule = [
       'name'       => 'require',
       'brand_id'   => 'require',
       'series_id'  => 'require',
       'grade_id'   => 'require',
       'example_img'=>'require',
       'style_id'=>'require',
       'conf_items'=>'require'
    ];
    protected $message = [
        'name.require' => '车型名称不能为空',
        'brand_id.require' => '品牌不能为空',
        'series_id.require' => '车型不能为空',
        'grade_id.require' => '车等级不能为空',
        'example_img.require' => '车样例图不能为空',
        'style_id.require' => '车型id不能为空',
        'conf_items.require' => '配置项不能为空',
    ];

    protected $scene = [
          'add'  => ['name,brand_id,series_id,grade_id,example_img'],
          'edit' => ['name,example_img'],
          'config'=>['style_id,conf_items'],
    ];
}