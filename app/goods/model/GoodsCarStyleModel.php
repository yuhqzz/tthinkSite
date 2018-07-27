<?php
// +----------------------------------------------------------------------
// | goods CarStyle model
// +----------------------------------------------------------------------
namespace app\goods\model;

use think\Model;

class GoodsCarStyleModel extends Model
{

    public function getCarStyleList($where,$limit= 20){
        $wh['delete_time'] = 0;
        if($wh){
            $wh = array_merge($wh,$where);
        }
        $list  = $this->where($wh)->order('brand_id asc,series_id asc,list_order asc ')->paginate($limit);
        return $list;
    }

    /**
     * 保存车型参数配置
     * @param $style_id
     * @param $data
     * @return bool|int|string
     */
    public function saveCarConfig($style_id,$data){
        $style_id = intval($style_id);
        if(empty($data)) return false;
        if(empty($style_id)) return false;
        $items = [];

        foreach ($data as $val){
            $item = [];
            if(!empty($val)){
                foreach ($val as  $k=>$v){
                    $item['car_style_id'] = $style_id;
                    $item['config_id'] = intval($k);
                    $item['config_value'] = strval($v);
                }
                $items[] = $item;
            }
        }
        $rs = Db::name('goods_car_config_values')->isUpdate(false)->allowField(true)->insertAll($items);

        return $rs;
    }

    /**
     *
     */
    public function getCarStyleDataBySeriesId( $seriesId ){
        if(empty($seriesId)) return [];
        $wh  = [];
        $wh['gct.delete_time'] = 0;
        if(is_array($seriesId)){
            $seriesId = array_filter($seriesId);
            if(empty($seriesId)) return [];
            $wh['gct.series_id'] = ['in',$seriesId];
        }else{
            $wh['gct.series_id'] = intval($seriesId);
        }
        $order = 'is_hot desc';
        $fields = 'gct.id,gct.name,gct.brand_id,gct.series_id,gct.is_hot,gct.year,gct.gauge_id,gct.factory_price,gcs.name as series_name';
        $data = $this->table(config('database.prefix').'goods_car_style')
            ->alias('gct')
            ->join(config('database.prefix').'goods_car_series gcs','gct.series_id = gcs.id','INNER')
            ->field($fields)
            ->where($wh)
            ->order($order)
            ->cache(false)
            ->fetchSql(false)
            ->select();
        $list = $data?$data->toArray():[];
        return $list;
    }

    /**
     *
     * 根据品牌获取所有车型
     * @param $brandId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCarStyleDataByBrandId( $brandId ){
        if(empty($brandId)) return [];
        $wh  = [];
        $wh['delete_time'] = 0;
        $wh['brand_id'] = intval($brandId);
        $data = $this->where($wh)->field('id,name,brand_id,series_id,is_hot')->order('is_hot desc')->select();
        $list = $data?$data->toArray():[];
        return $list;
    }

    /**
     * @param $brandId
     */
    public function getCarStyleListByBrandId($brandId){

        $wh  = [];
        $wh['s.delete_time'] = 0;
        $wh['s.brand_id'] = intval($brandId);



    }

}