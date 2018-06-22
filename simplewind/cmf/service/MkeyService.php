<?php
// +----------------------------------------------------------------------
// | ThinkCMF
// +----------------------------------------------------------------------
// | memcache缓存key定义函数
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------

namespace cmf\service;

class MkeyService{

const DAYEXPIRE = 3600;
const HOUREXPIRE = 3600;

const CONFIGLIST ='A000000001'; // 汽车参数配置项列表
const CONFIGVALUESBYSTYLEID ='A000000002'; // 汽车车型参数配置项值

    /**
     *
     * 获取缓存key
     * @param $key
     * @param string $append_key
     * @return string
     */
public static function  getMkey( $key, $append_key=''){
   $real_key = '';
    $real_key = config('cache.prefix').'_'.$key;
    if(!$append_key){
        $real_key =  $real_key.'_'.$append_key;
    }
    return $real_key;
}

}