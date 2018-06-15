<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//------------------------
// ThinkPHP 助手函数
//-------------------------

use think\Db;

if (!function_exists('getCarConfigCateName')) {
    /**
     * @param $cid
     * @return mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function getCarConfigCateName($cid)
    {
        $data = Db::name('goods_car_config_category')->where(['id'=>$cid])->find();

        if($data){
            return $data['name'];
        }
        return '';

    }
}


if (!function_exists('getConfigInputType')) {
    /**
     * @param $cid
     * @return mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function getConfigInputType($id)
    {
        if(!in_array(intval($id),[0,1,2])){
            $id = 0;
        }
        $input =['手工录入','从列表中选择','多行文本框'];

        return $input[$id];

    }
}


