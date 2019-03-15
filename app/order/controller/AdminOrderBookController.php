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
namespace app\order\controller;

use app\admin\model\PhoneLocationModel;
use app\goods\model\DealerModel;
use cmf\controller\AdminBaseController;
use app\goods\model\GoodsBrandModel;
use app\order\model\OrderBookModel;
use think\db;
/**
 * Class AdminOrderBookController
 * @package app\order\controller
 * @adminMenuRoot(
 *     'name'   =>'预约试驾管理',
 *     'action' =>'default',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 30,
 *     'icon'   =>'shopping-cart',
 *     'remark' =>'预约试驾管理'
 * )
 */
class AdminOrderBookController extends AdminBaseController
{
    /**
     * 预约订单试驾列表
     * @adminMenu(
     *     'name'   => '预约订单管理',
     *     'parent' => 'order/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '预约订单列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $this->model = new OrderBookModel();
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $total = $this->model
                ->with(['series'])
                ->where($where)
               	->where('order_book_model.delete_time','=',0)
                ->order($sort, $order)
                ->count();
           // echo $this->model->getLastSql();die;
            $list = $this->model
                ->with(['series'])
                ->where($where)
                ->where('order_book_model.delete_time','=',0)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            if($list){
                $dealerModel = new DealerModel();
                foreach ($list as $row){
                    $row['brand_name']  = getBrandName($row['series']['brand_id']);
                    $row['series_name']  = $row['series']['name'];
                    $dealer =  $dealerModel::get($row['dealers_id']);
                    $row['dealerName']  = $dealer?$dealer['name']:'';
                    //unset($row['series']);
                }
            }

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);
            return json($result);

        }
        return $this->fetch();
    }

    public function ajaxList(){
        $where = [];
        $order = '';
        if($this->request->post()){
            $query_params = $this->request->post();
            dump($query_params);die;
            $filter_a  = [];
            if(isset($query_params['brand_id']) && $query_params['brand_id']>0){
                array_push($filter_a,intval($query_params['brand_id']));
            }
            if(isset($query_params['car_series_id']) && $query_params['car_series_id'] > 0){
                array_push($filter_a,intval($query_params['car_series_id']));
            }
            if(isset($query_params['car_style_id']) && $query_params['car_style_id'] > 0){
                array_push($filter_a,intval($query_params['car_style_id']));
            }
            $count_a =  count($filter_a);
            if($count_a ==1){
                //模糊
            }elseif ($count_a == 2){
                // 车系模糊
            }elseif ($count_a == 3){
                // 车型精准搜索

            }



            $car_style_id =  I('car_style_id');
            if($car_style_id){
                $where['g.car_typeid'] = (int)$car_style_id;
            }
            $select_time =  I('select_time');
            if($select_time){
                if($select_time == 1){
                    $timeField = 'b.book_time';
                }elseif ($select_time == 2){
                    $timeField = 'b.book_to_time';
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
            $status =  I('status');
            $where['b.status'] = $status;
            $keyword =  I('keyword');
            if($keyword){
                $where['b.name'] = ['like',"{$keyword}"];// 名称模糊
            }

            /*$allow_order_field = ['factory_price','click_count','comment_count','is_on_sale','on_time','is_recommend','is_new','is_hot','create_time','sales_sum'];
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
            }*/
        }
        $orderBookModel = new OrderBookModel();
        $list =  $orderBookModel->getList($where,$order,40);

        //dump($list->toArray());exit;
        $this->assign('orderList',$list);
        return $this->fetch('admin_order_book/list');
    }


    /**
     * 发布试驾
     * @adminMenu(
     *     'name'   => '发布试驾',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '发布试驾',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        // 获取汽车品牌
        $goodsBrandModel = new GoodsBrandModel();
        $brandList =  $goodsBrandModel->getShowBrandList();
        $this->assign('brandList',$brandList);
        $area_data = Db::name('base_area')->where(['parentid'=>0])->order('vieworder asc')->select();
        $area_data = $area_data?$area_data->toArray():[];
        $this->assign('area_data',$area_data);
        return $this->fetch();
    }

    /**
     * 发布试驾提交
     * @adminMenu(
     *     'name'   => '发布试驾提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '发布试驾提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        //echo 11;
      //  die;
        if($this->request->isPost()){
            $data = $this->request->post();
             $area_ids = [];
            //dump($data);
             if(isset($data['district']) && $data['district']>0){
                 array_push($area_ids,intval($data['district']));
             }
            if(isset($data['city'])&&$data['city']>0){
                array_push($area_ids,intval($data['city']));
            }
            if(isset($data['province'])&&$data['province']>0){
                array_push($area_ids,intval($data['province']));
            }
            sort($area_ids);
            $data['area_id'] = end($area_ids);
            $data['car_style_id'] = isset($data['car_style_id'])?$data['car_style_id']:0;
            $data['series_id'] = isset($data['series_id'])?$data['series_id']:0;
            $result = $this->validate($data, 'OrderBook.add');
            if ($result !== true) {
                $this->error($result);
            } else {
                $orderBookModel = new OrderBookModel();
                $id = $orderBookModel->addOrderBookData($data);
                if(is_numeric($id)){
                    $this->success('发布成功!', url('AdminOrderBook/index'));
                }else{
                    $this->error('发布失败!');
                }
            }
            $this->error('发布失败!');
        }
    }

    /**
     * 编辑试驾
     * @adminMenu(
     *     'name'   => '编辑试驾',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑试驾',
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
            $data['ids'] = $this->request->param('id');
        }elseif(isset($params['ids'])){
            // 批量删除
            $data['ids'] = $this->request->param('ids/a');
        }

        if(is_array($data['ids'])){
              // 批量删除
            $where['id'] = ['in',$data['ids']];
            $items = [];
            foreach($data['ids'] as $id){
                $item   = [
                    'object_id'   =>$id,
                    'create_time' => time(),
                    'table_name'  => 'order_book',
                    'name'        => $id,
                    'user_id' =>cmf_get_current_admin_id()
                ];
                $items[] = $item;
            }
            $result =  Db::name('order_book')
                ->where($where)
                ->update(['delete_time' => time()]);

            if ($result) {
                Db::name('recycleBin')->insertAll($items);
                $this->success('删除成功!');
            } else {
                $this->error('删除失败');
            }

        }else{
            // 单个删除
            $where['id'] = (int)$data['ids'];
            $result =  Db::name('order_book')
                ->where($where)
                ->update(['delete_time' => time()]);
            $item   = [
                'object_id'   =>$where['id'],
                'create_time' => time(),
                'table_name'  => 'order_book',
                'name'        => $where['id'],
                'user_id' =>cmf_get_current_admin_id()
            ];
            if ($result) {
                Db::name('recycleBin')->insert($item);
                $this->success('删除成功!');
            } else {
                $this->error('删除失败');
            }
        }
    }

    /**
     *
     * 获取电话号码归属地
     */
    public function getMobilePosition(){
        if($this->request->isAjax()){
            $mobile = $this->request->param('tel');
            if(!isTelNumber($mobile)){
                $this->result([],0,'电话号码非法');
            }
            $phoneLocation = new PhoneLocationModel();
            $location = $phoneLocation->getLocationCity($mobile);
            if($location){
                $this->result(['location'=>$location],1);
            }else{
                $this->result([],0,'未知电话号码');
            }
        }
        $this->result([],0,'非法请求');
    }



}
