<?php
// +----------------------------------------------------------------------
// | goods category model
// +----------------------------------------------------------------------
namespace app\goods\model;

use think\Cache;
use think\Model;
use cmf\service;


class GoodsAttributeModel extends Model
{
    /**
     * @param int $cid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAttributeList($cid = 0){
        $cid = (int)$cid;
        $c_k = service\MkeyService::getMkey(service\MkeyService::ATTRIBUTEVALUESBYSTYLEID,$cid);
        $cache = Cache::get($c_k);
        if(!$cache){
            // 缓存不存在时
            $wh = [];
            if($cid){
                $wh['type_id'] = intval($cid);
            }
            $list = $this->where($wh)->order('list_order')->select();
            $list = $list?$list->toArray():[];
            foreach ($list as &$val){
                if(trim($val['attr_values'])){
                    $val['attr_values'] = explode("\r\n",$val['attr_values']);
                }
            }


            $cache = [];
            $cache['value'] = $list;
            Cache::set($c_k,$cache,service\MkeyService::DAYEXPIRE);
        }
        return $cache['value'];
    }

    /**
     * 添加配置项
     * @param $data
     * @return bool|int|string
     */
    public function addAttribute( $data ){
        if(empty($data)) return false;
        $cate_id    = intval($data['type_id']);
        $save['attr_name'] = $data['attr_name'];
        $save['cate_id'] = $cate_id;
        $save['attr_type'] = intval($data['attr_type']);
        $save['attr_input_type'] = intval($data['attr_input_type']);
        $save['attr_values'] = trim($data['config_values']);
        $id =  $this->allowField(true)->insertGetId($save);
        if($id){
            $this->clearCache($cate_id);
        }
        return $id;
    }

    /**
     * 编辑配置项
     * @param $data
     * @return bool|false|int
     */
    public function editAttribute( $data ){
        if(empty($data)) return false;
        $save = [];
        $id          = intval($data['attr_id']);
        $cate_id    = intval($data['type_id']);
        $save['attr_id'] = (int)$data['attr_id'];
        $save['attr_name'] = trim($data['attr_name']);
        $save['attr_type'] = intval($data['attr_type']);
        $save['attr_input_type'] = intval($data['attr_input_type']);
        $save['type_id'] = $cate_id;
        $save['attr_values'] = trim($data['attr_values']);  $res = $this->isUpdate(true)->allowField(true)->save($save, ['id' => $id]);
        if($res){
            $this->clearCache($cate_id);
        }
        return $res;
    }


    public function clearCache($cate_id,$clear_all = false){
        $cate_id = intval($cate_id);
        $c_k = service\MkeyService::getMkey(service\MkeyService::CONFIGLIST,$cate_id);
        $c_k_all = service\MkeyService::getMkey(service\MkeyService::CONFIGLIST,0);
        Cache::rm($c_k);
        Cache::rm($c_k_all);
        if($clear_all){
            $data = self::all();
            foreach ($data as $val){
                $c_k = service\MkeyService::getMkey(service\MkeyService::CONFIGLIST,$val['type_id']);
                Cache::rm($c_k);
            }
        }
    }

}