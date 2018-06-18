<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2018-06-14
 * Time: 13:59
 * 品牌管理
 */

namespace app\goods\controller;


use app\Goods\model\GoodsCarSeriesModel;
use cmf\controller\AdminBaseController;
use think\db;
use app\Goods\model\GoodsBrandModel;


class AdminCarSeriesController extends AdminBaseController
{
    /**
     * 添加商品品牌列表
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
                case 'is_hot':
                    $where['is_hot'] = intval($q);
                    break;
            }
        }
        $goodsCarSeriesModel = new GoodsCarSeriesModel();
        $list = $goodsCarSeriesModel->getCarSeriesList($where,1);
        $this->assign('list',$list);
        $this->assign('q',$q);
        $this->assign('field',$field);
        return $this->fetch();
    }

    /**
     * 添加商品车系
     */
    public function add()
    {
        $goodsBrandModel = new GoodsBrandModel();
        $brandList =  $goodsBrandModel->getShowBrandList();
        $this->assign('brandList',$brandList);
        return $this->fetch();
    }

    /**
     * 添加商品品牌提交
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
     * 编辑商品品牌
     * @adminMenu(
     *     'name'   => '编辑商品分类',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑商品分类',
     *     'param'  => ''
     * )
     */
    public function edit()
    {


    }

    /**
     * 编辑商品品牌提交
     * @adminMenu(
     *     'name'   => '编辑商品分类提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑商品分类提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {


        $this->success('保存成功!');
    }


    /**
     * 商品品牌排序
     * @adminMenu(
     *     'name'   => '商品分类排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品分类排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        parent::listOrders(Db::name('goods_category'));
        $this->success("排序更新成功！", '');
    }

    /**
     * 删除商品品牌
     * @adminMenu(
     *     'name'   => '删除商品分类',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除商品分类',
     *     'param'  => ''
     * )
     */
    public function delete()
    {

    }




}