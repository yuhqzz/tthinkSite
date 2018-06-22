<?php
// +----------------------------------------------------------------------
// | goods CarConfigValues model
// +----------------------------------------------------------------------
namespace app\Goods\model;

use think\Model;
use think\Cache;
use cmf\service;

class GoodsCarConfigValuesModel extends Model
{
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
                    $items[] = $item;
                }
            }
        }
        $rs = $this->isUpdate(false)->allowField(true)->insertAll($items,true);
        if($rs){
            // 清缓存
            $c_k = service\MkeyService::getMkey(service\MkeyService::CONFIGVALUESBYSTYLEID,$style_id);
             Cache::rm($c_k);
        }
        return $rs;
    }

    /**
     *
     * 获取车型参数配置值
     * @param $style_id
     */
    public function getConfigListByStyleId($style_id){

        $style_id = (int)$style_id;
        $c_k = service\MkeyService::getMkey(service\MkeyService::CONFIGVALUESBYSTYLEID,$style_id);
        $cache = Cache::get($c_k);
        if(!$cache||1){
            // 缓存不存在时
            $wh = [];
            $wh['car_style_id'] = $style_id;
            $fields = ['gci.*,gcv.config_value as config_real_value,gcv.car_style_id,gcc.name as cate_name'];
            $list = $this->table(config('database.prefix').'goods_car_config_values')
                ->alias('gcv')
                ->join(config('database.prefix').'goods_car_config_items gci','gcv.config_id = gci.config_id','INNER')
                ->join(config('database.prefix').'goods_car_config_category gcc','gci.cate_id = gcc.id','INNER')
                ->where($wh)
                ->field($fields)
                ->select();
            $list = $list?$list->toArray():[];
            if($list){
                $new_list = [];
                foreach ($list as $val){
                    $new_list[$val['config_id']] = $val;
                }
            }else{
                $new_list = $list;
            }
            $cache = [];
            $cache['value'] = $new_list;
            Cache::set($c_k,$cache,service\MkeyService::DAYEXPIRE);
        }
        return $cache['value'];



    }






}