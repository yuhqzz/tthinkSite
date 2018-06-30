<?php
namespace app\goods\validate;

use think\Validate;
use think\db;

class GoodsAttributeValidate extends Validate
{
    protected $rule = [
        'attr_name'  => 'require|checkNameUniqueValue',
        'type_id'  => 'require',
        'attr_input_type'=>'checkInputValue',
    ];
    protected $message = [
       'attr_name.require' => '属性名称不能为空',
    ];

    protected $scene = [
      'add'  => ['attr_name,type_id'],
      'edit' => ['attr_name,type_id'],
    ];


    protected function checkNameUniqueValue($value,$rule,$data){
        if(isset($data['attr_id'])){
            //编辑
            $wh['attr_id'] = ['neq',$data['attr_id']];
        }
        $wh['attr_name'] = trim($value);
        $wh['type_id'] = intval($data['type_id']);
        $res =  Db::name('goods_attribute')->where($wh)->find();
        if($res){
            return '该属性已经存在，不要重复添加';
        }
        return true;
    }


    protected function checkInputValue($value,$rule,$data){
        if($value == 1){
            if(!isset($data['attr_values'])||empty($data['attr_values'])){
                return '取值方式为从列表中选择时，属性项值不允许为空';
            }
        }
        return true;
    }


}