<?php
namespace app\goods\validate;

use think\Validate;

class GoodsAttributeTypeValidate extends Validate
{
    protected $rule = [
        'name'  => 'require',
    ];
    protected $message = [
        'name.require' => '类型名称不能为空',
    ];

    protected $scene = [
//        'add'  => ['user_login,user_pass,user_email'],
//        'edit' => ['user_login,user_email'],
    ];
}