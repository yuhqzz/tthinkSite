<?php
// +----------------------------------------------------------------------
// | goods category model
// +----------------------------------------------------------------------
namespace app\goods\model;

use think\Cache;
use think\Model;
use cmf\service;


class GoodsCarConfigItemsModel extends Model
{
    /**
     * @param int $cid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getConfigList($cid = 0){
        $cid = (int)$cid;
        $c_k = service\MkeyService::getMkey(service\MkeyService::CONFIGLIST,$cid);
        $cache = Cache::get($c_k);
        if(!$cache){
            // 缓存不存在时
            $wh = [];
            if($cid){
                $wh['cate_id'] = intval($cid);
            }
            $list = $this->where($wh)->order('list_order')->select();
            $list = $list?$list->toArray():[];
            foreach ($list as &$val){
                if(trim($val['config_values'])){
                    $val['config_values'] = explode("\r\n",$val['config_values']);
                }
            }


            $cache = [];
            $cache['value'] = $list;
            Cache::set($c_k,$cache,service\MkeyService::DAY);
        }
        return $cache['value'];
    }

    /**
     * 添加配置项
     * @param $data
     * @return bool|int|string
     */
    public function addConfigItems( $data ){
        if(empty($data)) return false;
        $cate_id    = intval($data['cate_id']);
        $save['config_name'] = $data['config_name'];
        $save['cate_id'] = $cate_id;
        $save['config_input_type'] = intval($data['config_input_type']);
        $save['config_values'] = trim($data['config_values']);
        $save['description'] = htmlentities(trim($data['description']),ENT_QUOTES);
        $save['config_field'] = trim($data['config_field']);
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
    public function editConfigItems( $data ){
        if(empty($data)) return false;
        $save = [];
        $id          = intval($data['config_id']);
        $cate_id    = intval($data['cate_id']);
        $save['config_id'] = (int)$data['config_id'];
        $save['config_name'] = trim($data['config_name']);
        $save['cate_id'] = $cate_id;
        $save['config_input_type'] = intval($data['config_input_type']);
        $save['config_values'] = trim($data['config_values']);
        $save['description'] = htmlentities(trim($data['description']),ENT_QUOTES);
        $save['config_field'] = trim($data['config_filed']);
        $res = $this->isUpdate(true)->allowField(true)->save($save, ['id' => $id]);
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
                $c_k = service\MkeyService::getMkey(service\MkeyService::CONFIGLIST,$val['cate_id']);
                Cache::rm($c_k);
            }
        }
    }

}