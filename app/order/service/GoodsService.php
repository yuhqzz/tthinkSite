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
namespace app\goods\service;


use app\goods\model\GoodsModel;
use think\Db;
use app\goods\model\GoodsCarSeriesModel;
use app\goods\model\GoodsCarStyleModel;
use app\goods\model\GoodsAttributeModel;

class GoodsService
{


    public function getAttrDataByGoodsId($goods_id){
        $goodsModel = new GoodsModel();
        $data = $goodsModel->getAttrDataByGoodsId($goods_id);
        return $data;
    }

    public function getGoodsImagesByGoodsId($goods_id){
        if(empty($goods_id)) return [];
       $data =  Db::name('goods_images')->cache(false)->where(['goods_id'=>$goods_id])->select();
       $data = $data?$data->toArray():[];
       return $data;
    }
    /**
     *
     * 根据品牌id 获取车系数据 包含车系下的车型
     * @param $brand_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCarSeriesDataByBrandId($brand_id ,$includeCarStyle = false){
        $brand_id = intval($brand_id);
        if(empty($brand_id)) return [];
        $carSeriesModel = new GoodsCarSeriesModel();
        $carSeriesData = $carSeriesModel->getCarSeriesByBrandId($brand_id);
        if(!$includeCarStyle){
            return $carSeriesData;
        }
        if(empty($carSeriesData)) return [];
        $carStyleModel = new GoodsCarStyleModel();
        $carStyleData = $carStyleModel->getCarStyleDataByBrandId($brand_id);
        $carStyleData_new = [];
        if(!empty($carStyleData)){
            $carStyleData_new = [];
            foreach ($carStyleData as $val){
                $carStyleData_new[$val['series_id']][] = $val;
            }
        }
        foreach ($carSeriesData as &$val){
            $val['series'] = isset($carStyleData_new[$val['id']])?$carStyleData_new[$val['id']]:[];
        }
        return $carSeriesData;
    }

   public function getCarStyleDataBySeriesId($series_id){
        if(empty($series_id)) return [];
       $goodsCarStyleModel = new GoodsCarStyleModel();
       $styleData = $goodsCarStyleModel->getCarStyleDataBySeriesId($series_id);
       return $styleData;
   }


    public function getGoodsAttrDataByStyleId($style_id){
        if(empty($style_id)) return [];
        $goodsAttrModel = new GoodsAttributeModel();
        $attrData = $goodsAttrModel->getAttributeList($style_id);
        return $attrData;
    }























}