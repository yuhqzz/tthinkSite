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


use app\goods\model\GoodsBrandModel;
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
       $list = [];
       if($styleData){
           foreach($styleData as $val){
               $val['showName'] = $val['series_name'].' '.$val['year'].'款 '.$val['name'];
               $list[] = $val;
           }
       }
       return $list;
   }


    public function getGoodsAttrDataByStyleId($style_id){
        if(empty($style_id)) return [];
        $goodsAttrModel = new GoodsAttributeModel();
        $attrData = $goodsAttrModel->getAttributeList($style_id);
        return $attrData;
    }

    /**
     *
     * 获取最新推荐车源
     * @param int $limit
     */
    public function getRecommendCar($limit = 4){
        $goodsModel  = new  GoodsModel();
        $brand_id = 0;
        $data = $goodsModel->getLatestRecommend($brand_id,$limit);
        return $data;
    }

    /**
     *
     *  首页获取推荐车系
     * @param int $type
     * @param int $show_num
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public function getRecommendSeries($type = 1,$show_num = 3){
        $brandModel = new GoodsBrandModel();
        $hotBrandData = $brandModel->getHotBrand($type);
        if($hotBrandData){
            foreach($hotBrandData as &$val){
                $series_list = $this->getRecommendSeriesByBrandId($val['id'],$type);
                $val['series_list'] = $series_list;
                if(count($series_list)>$show_num){
                    $val['has_more'] = 1;
                }else{
                    $val['has_more'] = 0;
                }

            }
        }
        return $hotBrandData;
    }
    /**
     * 根据品牌id 获取热门车系
     * @param $brand_id
     * @param int $type 平行进口 中规车
     * @return array
     */
    public function getRecommendSeriesByBrandId($brand_id, $type=1 ){
        $seriesModel = new GoodsCarSeriesModel();
        $where['brand_id'] =  $brand_id;
        $where['is_hot'] =  1;
        $list = $seriesModel->getCarSeriesList($where,3,false);
        return $list?$list->toArray():$list;
    }





















}