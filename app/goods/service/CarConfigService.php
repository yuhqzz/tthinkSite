<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\Goods\service;

use app\Goods\model\GoodsCarConfigItemsModel;
use think\Db;

class CarConfigService
{

    public static function getConfigList()
    {
        /*$fields = ['cc.id as cid','cc.name as cateName','ci.config_id as itemId','ci.config_name as itemName','ci.list_order as ord'];
        $list = Db::table('tx_goods_car_config_items')->alias('ci')
            ->join('tx_goods_car_config_category cc','ci.cate_id = cc.id','INNER')
            ->field($fields)
            ->order('ord desc')
            ->select();
    dump($list->toArray());

        echo Db::table('')->getLastSql();
        die;*/

        //获取所有配置分类
        $config_category = Db::name('goods_car_config_category')->order('list_order asc')->select();

        if($config_category){
            $config_category =  $config_category->toArray();
        }
        if(empty($config_category)) return [];

        //获取所有配置项
        $goodsCarConfigItems = new GoodsCarConfigItemsModel();
        $configItems  = $goodsCarConfigItems->getConfigList();
        if($configItems){
            $data = [];
            foreach ($configItems as $val){
                $data[$val['cate_id']][] = $val;
            }
        }
        foreach ($config_category as &$val){
            if(!empty($data)){
                $val['configItems'] = isset($data[$val['id']])?$data[$val['id']]:[];
            }
        }

        return $config_category;










    }


}