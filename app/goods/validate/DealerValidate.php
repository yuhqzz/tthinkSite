<?php
namespace app\goods\validate;

use think\Validate;
use think\db;

class DealerValidate extends Validate
{
    protected $rule = [
        'name'  => 'require|checkNameUniqueValue',
        'type'  => 'require',
        'main_brand'  => 'require',
        'address'  => 'require',
        'contact_name'  => 'require',
        'telephone'  => 'require',
        'email'  => 'require',
    ];
    protected $message = [
        'name.require' => '名称不能为空',
        'type.require' => '类型不能为空',
        'main_brand.require' => '主营品牌不能为空',
        'address.require' => '地址不能为空',
        'contact_name.require' => '联系人不能为空',
        'telephone.require' => '服务热线不能为空',
        'email.require' => '服务邮箱不能为空',
    ];

    protected $scene = [
         'add'  => ['name,type,main_brand','address','contact_name','telephone','email'],
         'edit'  => ['name,type,main_brand','address','contact_name','telephone','email']
    ];

    protected function checkNameUniqueValue($value,$rule,$data){
        if(isset($data['id'])){
            //编辑
            $wh['id'] = ['neq',$data['id']];
        }
        $wh['name'] = trim($value);
        $res =  Db::name('dealer')->where($wh)->find();
        if($res){
            return '该店名已经存在，不要重复添加';
        }
        return true;
    }


}