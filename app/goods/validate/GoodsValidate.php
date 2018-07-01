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
        'factory_price'=>'require',
        'goods_remark'=>'require',
        'original_img'=>'require',
        'goods_id'=>'require|checkIdValue'
    ];
    protected $message = [
       'name.require' => '名称不能为空',
       'category_id.require' => '分类不能为空',
       'brand_id.require' => '品牌不能为空',
       'series_id.require' => '车系不能为空',
       'grade_id.require' => '等级不能为空',
       'style_id.require' => '车型不能为空',
       'market_price.require' => '市场价不能为空',
       'factory_price.require' => '厂家指导价不能为空',
       'goods_remark.require' => '简介不能为空',
       'original_img.require' => '缩略图不能为空',
    ];

    protected $scene = [
      'add'  => ['name,category_id,brand_id,series_id,style_id,grade_id,market_price,factory_price,goods_remark,original_img'],
      'edit'  => ['name,category_id,brand_id,series_id,style_id,grade_id,market_price,factory_price,goods_remark,original_img,goods_id'],
    ];


    protected function checkIdValue($value,$rule,$data){
        if(isset($data['goods_id'])){
            //编辑
            $wh['goods_id'] = $data['goods_id'];
        }
        $wh['goods_id'] = (int)$data['goods_id'];
        $wh['delete_time'] = 0;
        $res =  Db::name('goods')->where($wh)->find();
        if(empty($res)){
            return '该商品不存在';
        }
        return true;
    }



}