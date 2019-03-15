<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2018-10-31
 * Time: 14:20
 */

namespace app\goods\service;


use app\goods\model\DealerModel;
use think\Collection;

class DealerService
{


    public function getDealerList( $type ='all',$brand = 0){
        $dealerModel = new DealerModel();
        $where = [];
        if( isset($type) && $type !== 'all' ){
            $where[] = ['type','=',intval($type)];
        }
        if($brand){
            $where[] = "FIND_IN_SET({$brand},main_brand)";
        }
        if($where){
            $where = function ($query) use ($where) {
                foreach ($where as $k => $v) {
                    if (is_array($v)) {
                        call_user_func_array([$query, 'where'], $v);
                    } else {
                        $query->where($v);
                    }
                }
            };
        }
        $data = $dealerModel->where($where)->select();
        $list = Collection::make($data)->toArray();
        return $list;
    }

    /**
     * 获取4S店数据
     * @param bool $returnExtendInfo 是否返回更多附加信息 默认值返回店名
     * @return array
     */
    public function getDealer($returnExtendInfo = false){
        $new_list = [];
        $list = $this->getDealerList(1);

        if($list){
            foreach ($list as $val){
                $main_brands = explode(',',$val['main_brand']);
                foreach ($main_brands as $v){
                    if(empty($v)) continue;
                    $item['id'] = $val['id'];
                    $item['name'] = $val['name'];
                    $item['brand_id'] = (int)$v;
                    $item['brand_name'] = getBrandName($val['main_brand']);
                    if($returnExtendInfo){
                        $item['extend_info'] = $val;
                    }

                    $new_list[$v][$item['id']] = $item;
                }
            }
        }
        return $new_list;
    }





}