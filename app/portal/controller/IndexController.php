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

use app\goods\model\GoodsBrandModel;
use app\goods\model\GoodsModel;
use app\goods\service\GoodsService;
use cmf\controller\HomeBaseController;

class IndexController extends HomeBaseController
{
    public function index()
    {
        // 获取最新发布的车源
        // 获取热门推荐品牌下几款车型
       $brandModel = new GoodsBrandModel();
        $hotBrandData = $brandModel->getHotBrand();
        //bb($hotBrandData);die;
        $this->assign('hotBrandList',$hotBrandData);

        // 获取推荐置顶的4款车源
        $goodsModel = new GoodsModel();
        $goodsSer = new GoodsService();

        $recommendData = $goodsSer->getRecommendCar(6);
        $recommendSeriesData = $goodsSer->getRecommendSeries(1);

        $this->assign('recommendData',$recommendData);
        $this->assign('recommendSeriesData',$recommendSeriesData);
        //今日新品
        $latestCarList = $goodsModel->getLatestCar(4);
        $this->assign('latestCarList',$latestCarList);
        $topCarList = $goodsModel->getTopCar(3);
        $this->assign('topCarList',$topCarList);
        return $this->fetch(':index');
    }

    public function detail(){
        return $this->fetch(':maiche_show');

    }
    public function about(){
        return $this->fetch(':about');
    }

    public function contact(){
        return $this->fetch(':contact');
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

    public function wey(){
        header('location:http://www.szdxwey.com/portal/channel/book.html?b-n=wey');
        exit;
    }
    public function honda(){
        header('location:http://www.szdxhonda.cn/vehicle/channel/channel.html');
        exit;
    }

    public function gf(){
        header('location:http://chr.66820000.com/gf.html');
        exit;
    }
}
