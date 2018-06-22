<?php
namespace app\Goods\validate;

use think\Db;
use think\Validate;

class GoodsCarConfigItemsValidate extends Validate
{
    protected $rule = [
        'cate_id'=>'checkCateId',
        'config_name'  => 'require|checkFieldUnique',
        'config_input_type'  => 'require|checkFieldValues'
    ];
    protected $message = [
        'config_name.require' => '配置项名称不能为空',
        'config_input_type.require' => '配置项取值方式不能为空',
    ];

    protected $scene = [
//        'add'  => ['user_login,user_pass,user_email'],
//        'edit' => ['user_login,user_email'],
    ];
    //$value, $rule, $data, $field, $title
    protected function checkFieldValues($value,$rule,$data){
        if($value>0){
            $config_values =  trim($data['config_values']);
            if(empty($config_values)){
                return '配置项值不允许为空';
            }
        }
        return true;
    }


    protected function checkFieldUnique($value,$rule,$data){
        if(isset($data['config_id'])){
            $wh['config_id'] = ['neq',$data['config_id']];
        }
        $wh['cate_id'] = $data['cate_id'];
        $wh['config_name'] = trim($value);
        $data = Db::name('goods_car_config_items')->where($wh)->find();
        if($data){
            return '该分类下已经配置了该参数项值';
        }
        return true;

   }
   protected function checkCateId($value){
       $wh['id'] = intval($value);
       $data = Db::name('goods_car_config_category')->where($wh)->find();
       if(!$data){
           return '该参数分类不存在，不允许添加';
       }
       return true;

   }

}