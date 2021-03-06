<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2018-06-14
 * Time: 13:59
 * 品牌管理
 */

namespace app\goods\controller;


use app\goods\model\GoodsCarSeriesModel;
use cmf\controller\AdminBaseController;
use think\db;
use app\goods\model\GoodsBrandModel;

/**
 * Class AdminCarSeriesController
 * @package app\goods\controller
 */
class AdminCarSeriesController extends AdminBaseController
{
    /**
     * 汽车车系列表
     * @adminMenu(
     *     'name'   => '车系管理',
     *     'parent' => 'goods/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '汽车车系列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $q  = input('post.q');
        $field = input('post.field');
        $where = [];
        if($field){
            switch($field){
                case 'name':
                    $where['name'] = ['like',$q];
                    break;
                case 'brand_name':
                    $brand_id = 0;
                    $goodsBrandModel = new GoodsBrandModel();
                    $brand = $goodsBrandModel->where(['name'=>$q,'delete_time'=>0])->find();
                    if($brand){
                        $brand = $brand->toArray();
                        $brand_id = $brand['id'];
                    }
                    $where['brand_id'] = $brand_id;
                    break;
                case 'is_hot':
                    $where['is_hot'] = intval($q);
                    break;
            }
        }
        $goodsCarSeriesModel = new GoodsCarSeriesModel();
        $list = $goodsCarSeriesModel->getCarSeriesList($where,20);
        $this->assign('list',$list);
        $this->assign('q',$q);
        $this->assign('field',$field);
        return $this->fetch();
    }

    /**
     * 添加车系
     * @adminMenu(
     *     'name'   => '添加车系',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加车系',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $goodsBrandModel = new GoodsBrandModel();
        $brandList =  $goodsBrandModel->getShowBrandList();
        $this->assign('brandList',$brandList);
        // 获取汽车等级
        $this->assign('carGradeList',config('car_grade'));
        return $this->fetch();
    }

    /**
     * 添加车系提交
     * @adminMenu(
     *     'name'   => '添加车系提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加车系提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $data = $this->request->param();
        $data['name'] = trim($data['name']);
        $data['brand_id'] = intval($data['brand_id']);
        $data['is_hot'] = intval($data['is_hot']);
        $data['description'] = htmlspecialchars(trim($data['description']),ENT_QUOTES);
        $data['more'] = trim($data['more']);
        $result = $this->validate($data, 'GoodsCarSeries');
        if ($result !== true) {
            $this->error($result);
        }
        $goodsCarSeriesModel = new GoodsCarSeriesModel();
        $result = $goodsCarSeriesModel->isUpdate(false)->allowField(true)->save($data);
        if ($result === false) {
            $this->error('添加失败!');
        }
        $this->success('添加成功!', url('AdminCarSeries/index'));

    }

    /**
     * 编辑车系
     * @adminMenu(
     *     'name'   => '编辑车系',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑车系',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id = $this->request->param('id');
        if ($id > 0) {
            $carSeries = GoodsCarSeriesModel::get($id);
            var_dump($carSeries);die;
            $carSeries = $carSeries?$carSeries->toArray():[];
            if(empty($carSeries)){
                $this->error('车系不存在或已经删除!');
            }
            $goodsBrandModel = new GoodsBrandModel();
            $brandList =  $goodsBrandModel->getShowBrandList();
            $this->assign('brandList',$brandList);
            // 获取汽车等级
            $this->assign('carGradeList',config('car_grade'));
            $this->assign($carSeries);
            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }
        return $this->fetch();
    }

    /**
     * 编辑车系提交
     * @adminMenu(
     *     'name'   => '编辑车系提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑车系提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        $data = $this->request->param();
        $data['id'] = intval($data['id']);
        if(empty($data['id'])){
            $this->error('保存失败!');
        }
        $data['name'] = trim($data['name']);
        if(isset($data['is_hot'])){
            $data['is_hot'] = intval($data['is_hot']);
        }
        $data['brand_id'] = intval($data['brand_id']);
        $data['description'] = htmlspecialchars(trim($data['description']),ENT_QUOTES);
        $result = $this->validate($data, 'GoodsCarSeries');

        if ($result !== true) {
            $this->error($result);
        }
        $goodsCarSeriesModel = new GoodsCarSeriesModel();
        $result = $goodsCarSeriesModel->where('id','=',$data['id'])->isUpdate(true)->allowField(true)->save($data);
        if ($result === false) {
            $this->error('保存失败!');
        }

        $this->success('保存成功!');
    }

    /**
     * 删除车系
     * @adminMenu(
     *     'name'   => '删除车系',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除车系',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $goodsCarSeriesModel = new GoodsCarSeriesModel();
        $id                  = $this->request->param('id');
        $findCarSeries = GoodsCarSeriesModel::get($id);

        if (empty($findCarSeries)) {
            $this->error('车系不存在!');
        }
        // 存在车型不允许被删除
        $rs = Db::name('goods_car_style')->where(['series_id'=>$id,'delete_time'=>0])->find();
        if($rs){
            $this->error('请先转移该车系下的车型');
        }
        // 存在商品不允许被删除
        $rs = Db::name('goods')->where(['series_id'=>$id,'delete_time'=>0])->find();
        if($rs){
            $this->error('请先转移该车系下的车源');
        }
        $data   = [
            'object_id'   => $findCarSeries['id'],
            'create_time' => time(),
            'table_name'  => 'goods_brand',
            'name'        => $findCarSeries['name'],
            'user_id' =>cmf_get_current_admin_id()
        ];
        $result = $goodsCarSeriesModel
            ->where('id', $id)
            ->update(['delete_time' => time()]);
        if ($result) {
            Db::name('recycleBin')->insert($data);
            $this->success('删除成功!');
        } else {
            $this->error('删除失败');
        }
    }
    /**
     * 获取品牌下的车系
     * @adminMenu(
     *     'name'   => '获取品牌下的车系',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '获取品牌下的车系',
     *     'param'  => ''
     * )
     */
    public function getSeriesByBrandId(){
        $brand_id = $this->request->param('brand_id');
        $brand_id = intval($brand_id);
        $seriesData = [];
        if(!empty($brand_id)){
            $goodsCarSeriesModel = new GoodsCarSeriesModel();
            $seriesData = $goodsCarSeriesModel->getCarSeriesByBrandId($brand_id);

        }
        $this->result($seriesData,1);
    }


}