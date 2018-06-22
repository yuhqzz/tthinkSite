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
