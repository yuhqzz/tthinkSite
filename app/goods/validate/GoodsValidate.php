<?php
namespace app\goods\validate;

use think\Validate;
use think\db;

class GoodsValidate extends Validate
{
    protected $rule = [
        'name'  => 'require',
        'category_id'  => 'require',
        'brand_id'=>'require',
        'series_id'=>'require',
        'style_id'=>'require',
        'grade_id'=>'require',
        'market_price'=>'require',
        'shop_price'=>'require',
        'factory_price'=>'require',
        'goods_remark'=>'require',
        'original_img'=>'require',
    ];
    protected $message = [
       'name.require' => '名称不能为空',
       'category_id.require' => '分类不能为空',
       'brand_id.require' => '品牌不能为空',
       'series_id.require' => '车系不能为空',
       'grade_id.require' => '等级不能为空',
       'style_id.require' => '车型不能为空',
       'market_price.require' => '市场价不能为空',
       'shop_price.require' => '本店售价不能为空',
       'factory_price.require' => '厂家指导价不能为空',
       'goods_remark.require' => '简介不能为空',
       'original_img.require' => '缩略图不能为空',
    ];

    protected $scene = [
      'add'  => ['name,category_id,brand_id,series_id,style_id,grade_id,market_price,shop_price,factory_price,goods_remark,original_img'],
      'edit'  => ['name,category_id,brand_id,series_id,style_id,grade_id,market_price,shop_price,factory_price,goods_remark,original_img'],
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