<?php
// +----------------------------------------------------------------------
// | goods Brand model
// +----------------------------------------------------------------------
namespace app\goods\model;

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
        $wh['parentid'] = 0;
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

    /***
     *
     * 获取热品牌
     * @param $type
     * @param int $limit
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public function getHotBrand($type = 1,$limit = 7){
        $wh['delete_time'] = 0;
        $wh['is_hot'] = 1;
        $wh['is_show'] = 1;
        $order = 'list_order desc first_char asc';
        $field = 'id,name,icon,first_char';
        $list  = $this->where($wh)->field($field)->order($order)->limit($limit)->select();
        $list = $list?$list->toArray():[];
        return $list;
    }

}