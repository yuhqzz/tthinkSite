<?php
namespace app\Goods\validate;

use think\Validate;

class GoodsCarConfigItemsValidate extends Validate
{
    protected $rule = [
        'config_name'  => 'require',
    ];
    protected $message = [
        'config_name.require' => '配置项名称不能为空',
    ];

    protected $scene = [
//        'add'  => ['user_login,user_pass,user_email'],
//        'edit' => ['user_login,user_email'],
    ];

   protected function checkField($value,$data){
        if($value == 1){
            $config_values =  trim($data['config_values']);
            if(empty($config_values)){
                return false;
            }
        }
        return true;
    }

}