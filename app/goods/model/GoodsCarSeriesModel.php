<?php
// +----------------------------------------------------------------------
// | goods CarSeries model
// +----------------------------------------------------------------------
namespace app\Goods\model;

use think\Model;

class GoodsCarSeriesModel extends Model
{


    public function getCarSeriesList($where,$limit= 20){
        $wh['delete_time'] = 0;
        if($wh){
            $wh = array_merge($wh,$where);
        }
        $list  = $this->where($wh)->order('list_order asc ')->paginate($limit);
        return $list;
    }

}