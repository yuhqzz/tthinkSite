<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2018-06-14
 * Time: 13:59
 * 品牌管理
 */

namespace app\goods\controller;


use app\goods\model\GoodsCarStyleModel;
use cmf\controller\AdminBaseController;
use think\db;
use app\goods\model\GoodsBrandModel;
use app\goods\model\GoodsCarSeriesModel;
use app\goods\model\GoodsCarStyleModeld;
use app\goods\service\CarConfigService;
use app\goods\model\GoodsCarConfigValuesModel;

/**
 * Class AdminCarStyleController
 * @package app\goods\controller
 */
class AdminCarStyleController extends AdminBaseController
{
    /**
     * 汽车车型列表
     * @adminMenu(
     *     'name'   => '车型管理',
     *     'parent' => 'goods/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '汽车车型列表',
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
                        $brand_id = $brand['id'];
                    }
                    $where['brand_id'] = $brand_id;
                    break;
                case 'series_name':
                    $series_id = 0;
                    $goodsCarSeriesModel = new GoodsCarSeriesModel();
                    $series = $goodsCarSeriesModel->where(['name'=>$q,'delete_time'=>0])->find();
                    if($series){
                        $series_id = $series['id'];
                    }
                    $where['series_id'] = $series_id;
                    break;
                case 'grade_name':
                    $car_grade = config('car_grade');
                    $car_grade = array_flip($car_grade);
                    $where['grade_id'] = (int)$car_grade[$q];
                    break;
                case 'is_hot':
                    $where['is_hot'] = intval($q);
                    break;
                case 'is_recommend':
                    $where['is_recommend'] = intval($q);
                    break;
            }
        }
        $goodsCarStyleModel = new GoodsCarStyleModel();
        $list = $goodsCarStyleModel->getCarStyleList($where,20);
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
        $this->assign('carGaugeList',config('car_gauge'));
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
    public function addPost()
    {
        $data = $this->request->param();
        $data['name'] = trim($data['name']);
        $data['brand_id'] = intval($data['brand_id']);
        $data['series_id'] = intval($data['series_id']);
        $data['gauge_id'] = intval($data['gauge_id']);
        $data['is_hot'] = intval($data['is_hot']);
        $data['is_recommend'] = intval($data['is_recommend']);
        $data['year'] = intval($data['year']);
        $data['factory_price'] = trim($data['factory_price']);
        $data['description'] = htmlspecialchars(trim($data['description']),ENT_QUOTES);
        $data['more'] = trim($data['more']);

       // dump($data);exit;
        $result = $this->validate($data, 'GoodsCarStyle.add');
        if ($result !== true) {
            $this->error($result);
        }
        $goodsCarSeriesModel = new GoodsCarStyleModel();
        $result = $goodsCarSeriesModel->isUpdate(false)->allowField(true)->save($data);
        if ($result === false) {
            $this->error('添加失败!');
        }
        $this->success('添加成功!', url('AdminCarStyle/index'));

    }

    /**
     * 编辑车型
     * @adminMenu(
     *     'name'   => '编辑车型',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑车型',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');
       if ($id > 0) {
            $carStyle = GoodsCarStyleModel::get($id);
            $carStyle = $carStyle?$carStyle->toArray():[];
            if(empty($carStyle)||$carStyle['delete_time']>0){
                $this->error('车型不存在或已经删除!');
            }
            $goodsBrandModel = new GoodsBrandModel();
            $brandList =  $goodsBrandModel->getShowBrandList();
            $this->assign('brandList',$brandList);
            $this->assign('carGaugeList',config('car_gauge'));
            $this->assign($carStyle);
            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }
        return $this->fetch();
    }

    /**
     * 编辑车型提交
     * @adminMenu(
     *     'name'   => '编辑车型提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑车型提交',
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
        $data['is_hot'] = intval($data['is_hot']);
        $data['is_recommend'] = intval($data['is_recommend']);
        $data['factory_price'] = trim($data['factory_price']);
        $data['gauge_id'] = intval($data['gauge_id']);
        $data['year'] = trim($data['year']);
        $data['description'] = htmlspecialchars(trim($data['description']),ENT_QUOTES);
        $data['more'] = trim($data['more']);
        $result = $this->validate($data, 'GoodsCarStyle.edit');

        if ($result !== true) {
            $this->error($result);
        }
        $goodsCarStyleModel = new GoodsCarStyleModel();
        $result = $goodsCarStyleModel->isUpdate(true)->allowField(true)->save($data);
        if ($result === false) {
            $this->error('保存失败!');
        }
        $this->success('保存成功!');
    }


    /**
     * 车型排序
     * @adminMenu(
     *     'name'   => '车型排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '车型排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        parent::listOrders(Db::name('goods_car_style'));
        $this->success("排序更新成功！", '');
    }

    /**
     * 删除车型
     * @adminMenu(
     *     'name'   => '删除车型',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除车型',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $goodsCarStyleModel = new GoodsCarStyleModel();
        $id                  = $this->request->param('id');
        $findCarSeries = GoodsCarStyleModel::get($id);

        if (empty($findCarSeries)||$findCarSeries['delete_time']>0) {
            $this->error('车型不存在!');
        }
        // 存在商品不允许被删除
        $rs = Db::name('goods')->where(['style_id'=>$id,'delete_time'=>0])->find();
        if($rs){
            $this->error('请先转移该车型下的车源');
        }
        $data   = [
            'object_id'   => $findCarSeries['id'],
            'create_time' => time(),
            'table_name'  => 'goods_car_style',
            'name'        => $findCarSeries['name'],
            'user_id' =>cmf_get_current_admin_id()
        ];
        $result = $goodsCarStyleModel
            ->where('id', $id)
            ->update(['delete_time' => time()]);
        if ($result) {
            Db::name('recycleBin')->insert($data);
            // 删除参数配置表信息
            Db::name('goods_car_config_values')->where(['car_style_id'=>$id])->delete();
            $this->success('删除成功!');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 车型参数配置
     * @adminMenu(
     *     'name'   => '车型参数配置',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '车型参数配置',
     *     'param'  => ''
     * )
     */
    public function configCar(){
        $data = $this->request->param();
        $style_id = intval($data['id']);
        if(!$style_id){
            $this->error('车型id为空');
        }
        $configList = CarConfigService::getConfigList();
        $this->assign('configList',$configList);
        $this->assign('style_id',$style_id);
        // 原有值
         $goodsCarConfigValuesModel = new GoodsCarConfigValuesModel();
         $configValues = $goodsCarConfigValuesModel->getConfigListByStyleId($style_id);
         $act_type = 'add';
         if($configValues){
             $act_type = 'edit';
             $this->assign('configValues',$configValues);
             $template = 'car_edit_config';
         }else{
             $template = 'car_config';
         }
        $this->assign('act_type',$act_type);
        return $this->fetch($template);
    }

    /**
     * 车型参数配置提交
     * @adminMenu(
     *     'name'   => '车型参数配置提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '车型参数配置提交',
     *     'param'  => ''
     * )
     */
    public function configPost(){

        $data = $this->request->param();
        $style_id = (int)$data['style_id'];
        if($data['act_type'] == 'add'){
            $act_msg = '保存';
        }else{
            $act_msg = '更新';
        }

        if(!$style_id){
            $this->error('车型id为空');
        }
        $result = $this->validate($data, 'GoodsCarStyle.config');
        if ($result !== true) {
            $this->error($result);
        }
        $goodsCarConfigValuesModel = new GoodsCarConfigValuesModel();
        $result = $goodsCarConfigValuesModel->saveCarConfig($style_id,$data['conf_items']);

        if ($result === false) {
            $this->error($act_msg.'失败!');
        }
        $this->success($act_msg.'成功!');

    }

}