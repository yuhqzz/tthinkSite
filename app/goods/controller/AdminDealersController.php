<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2018-06-14
 * Time: 13:59
 */

namespace app\goods\controller;


use app\goods\model\GoodsCarSeriesModel;
use app\goods\model\GoodsCarStyleModel;
use app\goods\model\GoodsModel;
use app\goods\model\GoodsCategoryModel;
use app\goods\model\GoodsBrandModel;
use app\goods\model\GoodsAttributeModel;
use cmf\controller\AdminBaseController;
use think\db;
use app\goods\service\GoodsService;

/**
 * Class AdminDealersController
 * @package app\goods\controller
 */
class AdminDealersController extends AdminBaseController
{

    /**
     * 经销商列表
     * @adminMenu(
     *     'name'   => '经销商管理',
     *     'parent' => 'goods/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '经销商列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {

        return $this->fetch();
    }

    /**
     * 添加经销商
     * @adminMenu(
     *     'name'   => '添加经销商',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加经销商',
     *     'param'  => ''
     * )
     */
    public function add()
    {

        return $this->fetch();
    }

    /**
     * 添加经销商提交
     * @adminMenu(
     *     'name'   => '添加经销商提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加经销商提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        if($this->request->isPost()){
            $data = $this->request->post();

//            $result = $this->validate($data['post'], 'Goods.add');
//            if ($result !== true) {
//                $this->error($result);
//            } else {
//                $goodsModel = new GoodsModel();
//                $id = $goodsModel->addGoodsData($data);
//                if(is_numeric($id)){
//                    $this->success('添加成功!', url('AdminGoods/index'));
//                }else{
//                    $this->error('添加失败!');
//                }
//            }
            $this->error('添加失败!');
        }
    }

    /**
     * 编辑车源
     * @adminMenu(
     *     'name'   => '编辑车源',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑车源',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id > 0) {
            $goodsModel = new GoodsModel();
            $goods_data = $goodsModel->where(['id'=>$id])->find();
            $goods_data = $goods_data?$goods_data->toArray():[];
            if(empty($goods_data) || $goods_data['delete_time']>0){
                $this->error('该车源已删除或不存在!');
            }
            $this->assign('goods_data',$goods_data);
            //dump($goods_data);exit;
            // 获取 商品分类
            $goodsCategoryModel = new GoodsCategoryModel();
            $categoriesTree      = $goodsCategoryModel->adminCategoryTree($goods_data['category_id']);
            $this->assign('categories_tree',$categoriesTree);
            // 获取汽车等级
            $this->assign('carGradeList',config('car_grade'));
            // 获取汽车品牌
            $goodsBrandModel = new GoodsBrandModel();
            $brandList =  $goodsBrandModel->getShowBrandList();
            $this->assign('brandList',$brandList);
            // 商品模型（属性类型）
            $goodsTypeList = Db::name('goods_type')->select();
            $goodsTypeList = $goodsTypeList?$goodsTypeList->toArray():[];
            $this->assign('modelList',$goodsTypeList);

            // 获取汽车车系
            $goodsSeriesModel = new GoodsCarSeriesModel();
            $seriesList =  $goodsSeriesModel->getCarSeriesByBrandId($goods_data['brand_id']);
            $this->assign('seriesList',$seriesList);

            // 获取汽车车型
            $goodsCarStyleModel = new GoodsCarStyleModel();
            $carStyleList =  $goodsCarStyleModel->getCarStyleDataBySeriesId($goods_data['series_id']);
            $this->assign('carStyleList',$carStyleList);

            $attributeHtml = $this->_getAttributeHtml($goods_data['model_id'],$goods_data['id']);
            $this->assign('attributeHtml',$attributeHtml->getData());
            $goodsImagesHtml =  $this->_getGoodsImagesHtml($goods_data['id']);
            $this->assign('goodsImagesHtml',$goodsImagesHtml->getData());



            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }

    }

    /**
     * 编辑车源提交
     * @adminMenu(
     *     'name'   => '编辑车源提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑车源提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        if($this->request->isPost()){
            $data = $this->request->post();
            $goods_id =  (int)$data['goods_id'];
            if(empty($goods_id)){
               $this->error('保存失败!');
            }
            $result = $this->validate($data['post'], 'Goods.edit');
            if ($result !== true) {
                $this->error($result);
            } else {
                $goodsModel = new GoodsModel();
                $id = $goodsModel->saveGoodsData($goods_id,$data);
                if(is_numeric($id)){
                    $this->success('保存成功!', url('AdminGoods/index'));
                }else{
                    $this->error('保存失败!');
                }
            }
        }
    }
    /**
     * 车源排序
     * @adminMenu(
     *     'name'   => '车源排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '车源排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        parent::listOrders(Db::name('goods'));
        $this->success("排序更新成功！", '');
    }

    /**
     * 车源删除
     * @adminMenu(
     *     'name'   => '车源删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '车源删除',
     *     'param'  => ''
     * )
     */
    public function delete()
    {

        $params   = $this->request->param();
       if(isset($params['id'])){
           // 单个删除
           $data['id'] = $this->request->param('id');
       }elseif(isset($params['ids'])){
           // 批量删除
           $data['ids'] = $this->request->param('ids/a');
       }
        $goodsModel = new GoodsModel();
        $result = $goodsModel->deleteGoods($data);
        if ($result) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败');
        }
    }
    /**
     * 获取品牌下所有车系
     * @adminMenu(
     *     'name'   => '获取品牌下所有车系',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '获取品牌下所有车系',
     *     'param'  => ''
     * )
     */
    public function ajaxGetCarSeriesByBrandId(){
        $brand_id = $this->request->param('brand_id');
        $brand_id = intval($brand_id);
        $seriesData = [];
        if(!empty($brand_id)){
            $goodsService = new GoodsService();
            $seriesData = $goodsService->getCarSeriesDataByBrandId($brand_id,true);
        }
        $this->result($seriesData,1);
    }
    /**
     * 获取车系下所有车型
     * @adminMenu(
     *     'name'   => '获取车系下所有车型',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '获取车系下所有车型',
     *     'param'  => ''
     * )
     */
    public function ajaxGetCarStyleBySeriesId(){
        $series_id = $this->request->param('series_id');
        $series_id = intval($series_id);
        $styleData = [];
        if(!empty($series_id)){
            $goodsService = new GoodsService();
            $styleData = $goodsService->getCarStyleDataBySeriesId($series_id);
        }
        $this->result($styleData,1);
    }
    /**
     * 获取车型属性
     * @adminMenu(
     *     'name'   => '获取车型属性',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '获取车型属性',
     *     'param'  => ''
     * )
     */
    public function ajaxGetGoodsAttributes(){
        $type_id = $this->request->param('model_id');
        $type_id = intval($type_id);
        // $attrData = $goodsService->getGoodsAttrDataByStyleId($type_id);
        $html = $this->_getAttributeHtml($type_id);
        $attrData = $html->getData();
        $this->result($attrData,1);
    }

    /**
     * 车源上架
     * @adminMenu(
     *     'name'   => '车源上架',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '车源上架',
     *     'param'  => ''
     * )
     */
    public function toOnSale(){
        $param           = $this->request->param();
        $goodsModel = new GoodsModel();

        if (isset($param['ids']) && isset($param["yes"])) {
            $ids = $this->request->param('ids/a');

            $goodsModel->where(['id' => ['in', $ids]])->update(['is_on_sale' => 1, 'on_time' => time()]);

            $this->success("上架成功！", '');
        }

        if (isset($param['ids']) && isset($param["no"])) {
            $ids = $this->request->param('ids/a');

            $goodsModel->where(['id' => ['in', $ids]])->update(['is_on_sale' => 0]);

            $this->success("下架成功！", '');
        }
    }
    /**
     * 车源热门
     * @adminMenu(
     *     'name'   => '车源热门',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '车源热门',
     *     'param'  => ''
     * )
     */
    public function toHot(){
        $param           = $this->request->param();
        $goodsModel = new GoodsModel();

        if (isset($param['ids']) && isset($param["yes"])) {
            $ids = $this->request->param('ids/a');

            $goodsModel->where(['id' => ['in', $ids]])->update(['is_hot' => 1, 'last_update' => time()]);

            $this->success("热卖成功！", '');
        }

        if (isset($param['ids']) && isset($param["no"])) {
            $ids = $this->request->param('ids/a');

            $goodsModel->where(['id' => ['in', $ids]])->update(['is_hot' => 0,'last_update' => time()]);

            $this->success("取消热卖成功！", '');
        }
    }
    /**
     * 车源推荐
     * @adminMenu(
     *     'name'   => '车源推荐',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '车源推荐',
     *     'param'  => ''
     * )
     */
    public function recommend(){
        $param           = $this->request->param();
        $goodsModel = new GoodsModel();

        if (isset($param['ids']) && isset($param["yes"])) {
            $ids = $this->request->param('ids/a');

            $goodsModel->where(['id' => ['in', $ids]])->update(['is_recommend' => 1, 'last_update' => time()]);

            $this->success("推荐成功！", '');
        }

        if (isset($param['ids']) && isset($param["no"])) {
            $ids = $this->request->param('ids/a');

            $goodsModel->where(['id' => ['in', $ids]])->update(['is_recommend' => 0,'last_update' => time()]);

            $this->success("取消推荐成功！", '');
        }
    }
    /**
     * 置新品
     * @adminMenu(
     *     'name'   => '置新品',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '置新品',
     *     'param'  => ''
     * )
     */
    public function toIsNew(){
        $param           = $this->request->param();
        $goodsModel = new GoodsModel();

        if (isset($param['ids']) && isset($param["yes"])) {
            $ids = $this->request->param('ids/a');

            $goodsModel->where(['id' => ['in', $ids]])->update(['is_new' => 1, 'last_update' => time()]);

            $this->success("置为新品成功！", '');
        }

        if (isset($param['ids']) && isset($param["no"])) {
            $ids = $this->request->param('ids/a');

            $goodsModel->where(['id' => ['in', $ids]])->update(['is_new' => 0,'last_update' => time()]);

            $this->success("取消新品成功！", '');
        }
    }
    private function _getAttributeHtml($model_id,$goods_id = 0){
        $goodsService = new GoodsService();
        if($goods_id>0){
            $goodsAttributeData = $goodsService->getAttrDataByGoodsId($goods_id);
            $this->assign('goodsAttributeData',$goodsAttributeData);
        }
        $attributeData = [];
        if($model_id){
            $attributeData = $goodsService->getGoodsAttrDataByStyleId($model_id);
        }
        $this->assign('attributeData',$attributeData);
        return $this->fetch('goods_attribute');
    }

    private function _getGoodsImagesHtml($goods_id){
        $goodsService = new GoodsService();
        $goodsImagesData = $goodsService->getGoodsImagesByGoodsId($goods_id);
        $this->assign('goodsImages',$goodsImagesData);
        return $this->fetch('goods_images');
    }

}