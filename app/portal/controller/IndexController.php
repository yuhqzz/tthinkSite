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
namespace app\portal\controller;

use cmf\controller\HomeBaseController;

class IndexController extends HomeBaseController
{
    public function index()
    {
        $options = array(
            'token'             =>  '', // 填写你设定的key
            'appid'             =>  'wxcc77f23a1659ea15', // 填写高级调用功能的app id, 请在微信开发模式后台查询
            'appsecret'         =>  '', // 填写高级调用功能的密钥
            'encodingaeskey'    =>  '', // 填写加密用的EncodingAESKey（可选，接口传输选择加密时必需）
            'mch_id'            =>  '', // 微信支付，商户ID（可选）
            'partnerkey'        =>  '', // 微信支付，密钥（可选）
            'ssl_cer'           =>  '', // 微信支付，证书cert的路径（可选，操作退款或打款时必需）
            'ssl_key'           =>  '', // 微信支付，证书key的路径（可选，操作退款或打款时必需）
            'cachepath'         =>  '', // 设置SDK缓存目录（可选，默认位置在./src/Cache下，请保证写权限）
        );
        \Wechat\Loader::config($options);
// 实例SDK相关的操作对象
        $menu = & \Wechat\Loader::get('Menu'); // 这行可以在任何地方New，IDE会带提示功能哦

        var_dump($menu);

        return $this->fetch(':index');
    }

    public function about(){
        return $this->fetch(':about');
    }

    public function buyCar(){
        return $this->fetch(':maiche_list');
    }

    public function wymc(){
        return $this->fetch(':wymc');
    }

    public function srdz(){
        return $this->fetch(':srdz');
    }

    public function userIndex(){

        return $this->fetch(':user_index');

    }

}
