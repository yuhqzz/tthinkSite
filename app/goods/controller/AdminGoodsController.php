<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2018-06-14
 * Time: 13:59
 */

namespace app\goods\controller;


use app\goods\model\GoodsModel;
use app\goods\model\GoodsCategoryModel;
use app\goods\model\GoodsBrandModel;
use app\goods\model\GoodsAttributeModel;
use cmf\controller\AdminBaseController;
use think\db;
use app\goods\service\GoodsService;


class AdminGoodsController extends AdminBaseController
{

    public function index()
    {
        $goodsModel = new GoodsModel();
        $category_id =  intval(I('category_id'));
        // 获取 商品分类
        $goodsCategoryModel = new GoodsCategoryModel();
        $categoriesTree      = $goodsCategoryModel->adminCategoryTree($category_id);
        $this->assign('categories_tree',$categoriesTree);
        // 获取汽车等级
        $this->assign('carGradeList',config('car_grade'));
        // 获取汽车品牌
        $goodsBrandModel = new GoodsBrandModel();
        $brandList =  $goodsBrandModel->getShowBrandList();
        $this->assign('brandList',$brandList);
        $where = [];
        $order = '';
        if($this->request->post()){
            $query_params = $this->request->post();
            // 带搜索条件
           $category_id =  intval(I('category_id'));
           if($category_id){
               $category_ids = $goodsCategoryModel->getSubCategory( $category_id,true);
               $where['g.category_id'] =['in',$category_ids];
           }
           $grade_id =  intval(I('grade_id'));
            if($grade_id){
                $where['g.grade_id'] = $grade_id;
            }
           $brand_id =  I('brand_id','','intval');
            if($brand_id){
                $where['g.brand_id'] = $brand_id;
            }
           $series_id =  I('series_id');
            if($series_id){
                $where['g.series_id'] = (int)$series_id;
            }
            $select_time =  I('select_time','','intval');
            if($select_time){
                if($select_time == 1){
                    $timeField = 'g.create_time';
                }elseif ($select_time == 2){
                    $timeField = 'g.on_time';
                }elseif ($select_time == 3){
                    $timeField = 'g.last_update';
                }
                $start_time = I('start_time');
                $start_time = strtotime($start_time);
                if($start_time>0){
                    $where[$timeField] = ['egt',$start_time];
                }
                $end_time = I('end_time');
                $end_time = strtotime($end_time);
                if($end_time>0){
                    $where[$timeField] = ['elt',$end_time];
                }
            }
            $is_on_sale =  I('is_on_sale',0,'intval');
            if($is_on_sale == 1){
                $where['g.is_on_sale'] = 1;// 在售
            }elseif ($is_on_sale == 2){
                $where['g.is_on_sale'] = 0;// 待售
            }

            $is_recommend =  I('is_recommend',0,'intval');
            if($is_recommend == 1){
                $where['g.is_recommend'] = 1;// 已推荐
            }elseif ($is_recommend == 2){
                $where['g.is_recommend'] = 0;// 未推荐
            }

            $is_new =  I('is_new',0,'intval');
            if($is_new == 1){
                $where['g.is_new'] = 1;// 新品
            }elseif ($is_new == 2){
                $where['g.is_new'] = 0;// 非新品
            }

            $is_hot =  I('is_hot',0,'intval');
            if($is_hot == 1){
                $where['g.is_hot'] = 1;// 热门
            }elseif ($is_hot == 2){
                $where['g.is_hot'] = 0;// 非热门
            }
            $keyword =  I('keyword','','trim');
            if($keyword){
                $where['g.name'] = ['like',"{$keyword}"];// 名称模糊
            }

            $allow_order_field = ['factory_price','click_count','comment_count','is_on_sale','on_time','is_recommend','is_new','is_hot','create_time','sales_sum'];
            $order_field = I('order_field');
            $order_field = trim($order_field);
            if(in_array($order_field,$allow_order_field)){
                // 必须要在允许的排序字段才能排序
                $order_type = I('order_field_value');
                if(!in_array(strtolower($order_type),['desc','asc'])){
                    $order = '';
                }else{
                    $order = 'g.'.$order_field.' '.strtoupper($order_type);
                }
            }
           // dump($query_params);exit;
            $this->assign('query_params',$query_params);
           // dump($query_params);exit;
        }
        $list =  $goodsModel->getGoodsList($where,$order,10);

       // dump($list->toArray());exit;
        $this->assign('goods',$list);
        return $this->fetch();
    }

    public function add()
    {
        // 获取 商品分类
        $goodsCategoryModel = new GoodsCategoryModel();
        $categoriesTree      = $goodsCategoryModel->adminCategoryTree(0);
        $this->assign('categories_tree',$categoriesTree);
        // 获取汽车等级
        $this->assign('carGradeList',config('car_grade'));
        // 获取汽车品牌
        $goodsBrandModel = new GoodsBrandModel();
        $brandList =  $goodsBrandModel->getShowBrandList();
        $this->assign('brandList',$brandList);
        // 商品模型（属性类型）
        $goodsTypeList = Db::name('goods_type')->select();
        $goodsTypeList =$goodsTypeList?$goodsTypeList->toArray():[];
        $this->assign('modelList',$goodsTypeList);
        return $this->fetch();
    }

    public function addPost()
    {
        if($this->request->isPost()){
            $data = $this->request->post();
            //dump($data);exit;
            $result = $this->validate($data['post'], 'Goods.add');
            if ($result !== true) {
                $this->error($result);
            } else {
                $goodsModel = new GoodsModel();
                $id = $goodsModel->addGoodsData($data);
                if(is_numeric($id)){
                    $this->success('添加成功!', url('AdminGoods/index'));
                }else{
                    $this->error('添加失败!');
                }
            }
            $this->error('添加失败!');
        }
    }

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

            $attributeHtml = $this->_getAttributeHtml($goods_data['model_id'],$goods_data['id']);
            $this->assign('attributeHtml',$attributeHtml->getData());
            $goodsImagesHtml =  $this->_getGoodsImagesHtml($goods_data['id']);
            $this->assign('goodsImagesHtml',$goodsImagesHtml->getData());

            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }

    }

    public function editPost()
    {
        if($this->request->isPost()){

            $this->success('保存成功!');
        }
    }

    public function listOrder()
    {
        parent::listOrders(Db::name('goods_attribute'));
        // 清缓存
        $goodsAttributeMod = new GoodsAttributeModel();
        $goodsAttributeMod->clearCache(0,true);
        $this->success("排序更新成功！", '');
    }

    public function delete()
    {
        $id = $this->request->param('id');
        if(!$id){
            $this->error('删除失败');
        }
        $goodsAttributeMod = new GoodsAttributeModel();
        $findCategory = $goodsAttributeMod::get($id);

        if (empty($findCategory)) {
            $this->error('属性项不存在!');
        }
        $goodsAttributeMod = new GoodsAttributeModel();
        $result = $goodsAttributeMod->where('attr_id', $id)->delete();
        if ($result) {
            $goodsAttributeMod->clearCache($findCategory['type_id']);
            $this->success('删除成功!');
        } else {
            $this->error('删除失败');
        }
    }
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

    public function ajaxGetGoodsAttributes(){
        $type_id = $this->request->param('model_id');
        $type_id = intval($type_id);
        // $attrData = $goodsService->getGoodsAttrDataByStyleId($type_id);
        $html = $this->_getAttributeHtml($type_id);
        $attrData = $html->getData();
        $this->result($attrData,1);
    }


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