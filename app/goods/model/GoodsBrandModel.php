<?php
// +----------------------------------------------------------------------
// | goods Brand model
// +----------------------------------------------------------------------
namespace app\Goods\model;

use think\Model;
use think\db;
class GoodsBrandModel extends Model
{

  /*  protected function base($query)
    {
        $query->where(['delete_time'=>0]);
    }*/

    public function getBrandList($where,$limit= 20){
        $wh['delete_time'] = 0;
        if($wh){
          $wh = array_merge($wh,$where);
        }
        $list  = $this->where($wh)->order('list_order asc first_char asc')->paginate($limit);
      return $list;
    }

    public function getShowBrandList(){
        $wh['delete_time'] = 0;
        $wh['is_show'] = 1;
        $list  = $this->where($wh)->order('first_char asc list_order asc ')->select();
        $list= $list?$list->toArray():[];
        return $list;
    }
}