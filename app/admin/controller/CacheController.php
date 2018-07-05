<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Cache;
use think\Db;

/**
 * Class UserController
 * @package app\admin\controller
 */
class CacheController extends AdminBaseController
{

    /**
     * 缓存管理
     * @adminMenu(
     *     'name'   => '缓存管理',
     *     'parent' => 'admin/Setting/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '管理员管理',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $act_type = I('act_type');
        $cache_key = I('cache_key');
        $data = [];
        if($act_type == 'get'){
            //Cache::set($cache_key,'1122',60);

            $data = Cache::get($cache_key);
        }elseif($act_type == 'rm'){
            Cache::set($cache_key);
        }elseif ($act_type == 'set'){
            $cache_value = I('cache_value');
            Cache::set($cache_key,$cache_value,3600);
        }elseif ($act_type == 'rm-all'){

        }
        $this->assign('data',$data);
        return $this->fetch();
    }


}