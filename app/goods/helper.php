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

if (!function_exists('getBrandName')) {
    /**
     * @param $id
     * @return mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function getBrandName($id)
    {
        $data = Db::name('goods_brand')->where(['id'=>$id])->find();
        if($data){
            return $data['name'];
        }
        return '';

    }
}
if (!function_exists('getSeriesName')) {
    /**
     * @param $id
     * @return mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function getSeriesName($id)
    {
        $data = Db::name('goods_car_series')->where(['id'=>$id])->find();
        if($data){
            return $data['name'];
        }
        return '';

    }
}
if (!function_exists('getStyleName')) {
    /**
     * @param $id
     * @return mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function getStyleName($id)
    {
        $data = Db::name('goods_car_style')->where(['id'=>$id])->find();
        if($data){
            return $data['name'];
        }
        return '';

    }
}


if (!function_exists('getGradeName')) {
    /**
     * @param $id
     * @return mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function getGradeName($id)
    {
        $data = config('car_grade');
         return $data[$id];

    }
}

if (!function_exists('getAttrItemType')) {
    /**
     * @param $cid
     * @return mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function getAttrItemType($id)
    {
        if(!in_array(intval($id),[0,1,2])){
            $id = 0;
        }
        $input =['唯一属性','单选属性','多选属性'];

        return $input[$id];

    }
}

if (!function_exists('getAttrInputType')) {
    /**
     * @param $cid
     * @return mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function getAttrInputType($id)
    {
        if(!in_array(intval($id),[0,1])){
            $id = 0;
        }
        $input =['手工录入','从列表中选择'];

        return $input[$id];

    }
}
if (!function_exists('getGaugeType')) {
    /**
     * @param $id
     * @return mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function getGaugeType($id)
    {
        $data = array_flip(config('car_gauge'));
        return $data[$id];
    }
}
if (!function_exists('getActUserPhone')) {
    function getActUserPhone($uid){
        $user = Db::name('activity_user')->where(['id'=>$uid])->find();
        if($user){
            return $user['phone'];
        }
        return 'xxx';
    }
}