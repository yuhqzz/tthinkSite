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
namespace app\goods\controller;

use app\goods\api\BrandApi;
use cmf\controller\HomeBaseController;
use think\Cache;

class TestController extends HomeBaseController
{
    public function getBrand()
    {
       $brandapi = new  BrandApi();
       return $brandapi->index($this->request);
    }
}
