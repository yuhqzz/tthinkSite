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
namespace app\order\model;

use think\Model;
use think\Db;

class OrderBookModel extends Model
{
    /**
     * @param $data
     * @return bool|int|string
     */
    public function addOrderBookData($data){
        if(empty($data)) return false;
        $add['name'] = trim($data['name']); // 姓名
        $add['area_id'] = $data['area_id']; // 区级id
        $add['sex'] = intval($data['sex']); // 性别id
        $add['car_style_id'] = intval($data['car_style_id']); // 车型id
        $add['book_time'] = time(); // 发布时间
        $add['book_to_time'] = strtotime($data['book_to_time']); // 预约到店时间
        $add['book_telephone'] = $data['book_telephone']; // 电话
        $add['series_id'] = isset($data['series_id'])?$data['series_id']:0; // 车系
        $add['dealers_id'] = isset($data['dealers_id'])?$data['dealers_id']:0; // 供应商
        $id = $this->isUpdate(false)->allowField(true)->insertGetId($add);
        return $id;
    }

    public function getList($where,$order='',$limit=20){
        $wh['b.delete_time'] = 0;
        if(empty($order)) {
            $order = 'b.book_time desc';
        }
        if($wh){
            $wh = array_merge($wh,$where);
        }
        $fields = 'b.id,b.name,b.sex,b.dealers_id,b.car_style_id,b.series_id,b.area_id,b.book_telephone,b.book_to_time,b.book_time,b.status,b.remark,gcs.name as style_name,gcse.name as series_name';
        $list = $this->table(config('database.prefix').'order_book')
            ->alias('b')
            ->join(config('database.prefix').'goods_car_style gcs','b.car_style_id = gcs.id','LEFT')
            ->join(config('database.prefix').'goods_car_series gcse','b.series_id = gcse.id','LEFT')
            ->field($fields)
            ->where($wh)
            ->order($order)
            ->cache(false)
            ->paginate($limit);

        return $list;
    }


}
