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

class MkeyService
{

    const MINUTE = 60;
    const MINUTE_5 = 30;
    const MINUTE_30 = 1800;
    const HOUR = 3600;
    const HOUR_2 = 7200;
    const DAY = 86400;
    const DAY_7 = 604800;

    const CONFIGLIST = 'A000000001'; // 汽车参数配置项列表
    const CONFIGVALUESBYSTYLEID = 'A000000002'; // 汽车车型参数配置项值
    const ATTRIBUTEVALUESBYSTYLEID = 'A000000003'; // 汽车车型参数配置项值

    const GOODSCATEGORY = 'A000000004'; // 商品分类

    /**
     *
     * 获取缓存key
     * @param $key
     * @param string $append_key
     * @return string
     */
    public static function getMkey($key, $append_key = '')
    {
        $real_key = '';
        $real_key = config('cache.prefix') . '_' . $key;
        if (!empty($append_key)) {
            $real_key = $real_key . '_' . $append_key;
        }
        return $real_key;
    }

}