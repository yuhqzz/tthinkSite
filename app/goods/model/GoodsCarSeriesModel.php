<?php
// +----------------------------------------------------------------------
// | goods CarSeries model
// +----------------------------------------------------------------------
namespace app\goods\model;

use think\Model;


class GoodsCarSeriesModel extends Model
{


    public function getCarSeriesList($where,$limit= 20,$return_page = true){
        $wh['delete_time'] = 0;
        if($wh){
            $wh = array_merge($wh,$where);
        }
        if($return_page){
            $list  = $this->where($wh)->order('list_order asc ')->paginate($limit);
        }else{
            $list = $this->where($wh)->order('list_order asc ')->limit($limit)->select();
        }

        return $list;
    }


    /**
     *
     * 根据品牌id 获取车系
     * @param $brand_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     *
     */
    public function getCarSeriesByBrandId($brand_id){
        $list = [];
        if(empty($brand_id)) return $list;
        $wh['delete_time'] = 0;
        $wh['brand_id'] = intval($brand_id);
        $data =  $this->where($wh)->field('id,name,is_hot')->order('list_order asc')->select();
        if($data){
           $list = $data->toArray();
        }
       return $list;
    }

}