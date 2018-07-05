<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2018-06-14
 * Time: 13:59
 * 品牌管理
 */

namespace app\goods\controller;


use app\goods\model\GoodsBrandModel;
use cmf\controller\AdminBaseController;
use think\db;

/**
 * Class AdminBrandController
 * @package app\goods\controller
 */
class AdminBrandController extends AdminBaseController
{
    /**
     * 商品品牌列表
     * @adminMenu(
     *     'name'   => '品牌管理',
     *     'parent' => 'goods/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品品牌列表',
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
                case 'first_char':
                    $where['first_char'] = $q;
                    break;
                case 'is_hot':
                    $where['is_hot'] = intval($q);
                    break;
                case 'is_show':
                    $where['is_show'] = intval($q);
                    break;
                case 'is_del':
                    if($q==1){
                        $where['delete_time'] = 0;
                    }else{
                        $where['delete_time'] = ['gt',0];
                    }
                    break;
            }
        }
        $goodsBrandModel = new GoodsBrandModel();
        $list = $goodsBrandModel->getBrandList($where,20);
        $this->assign('list',$list);
        $this->assign('q',$q);
        $this->assign('field',$field);
        return $this->fetch();
    }

    /**
     * 添加商品品牌
     * @adminMenu(
     *     'name'   => '添加商品品牌',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加商品品牌',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        return $this->fetch();
    }

    /**
     * 添加商品品牌提交
     * @adminMenu(
     *     'name'   => '添加品牌',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加品牌',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $data = $this->request->param();
        $data['name'] = trim($data['name']);
        $data['is_hot'] = intval($data['is_hot']);
        $data['is_show'] = intval($data['is_show']);
        $data['description'] = htmlspecialchars(trim($data['description']),ENT_QUOTES);
        $result = $this->validate($data, 'GoodsBrand');
        if ($result !== true) {
            $this->error($result);
        }
        $goodsBrandModel = new GoodsBrandModel();
        $result = $goodsBrandModel->isUpdate(false)->allowField(true)->save($data);
        if ($result === false) {
            $this->error('添加失败!');
        }
        $this->success('添加成功!', url('AdminBrand/index'));

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
        $id = $this->request->param('id', 0, 'intval');
        if ($id > 0) {
            $brand = GoodsBrandModel::get($id);
            $brand = $brand?$brand->toArray():[];
            if(empty($brand)){
                $this->error('品牌不存在或已经删除!');
            }
            $this->assign($brand);
            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }

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
        $data = $this->request->param();
        $data['id'] = intval($data['id']);
        if(empty($data['id'])){
            $this->error('保存失败!');
        }
        $data['name'] = trim($data['name']);
        $data['is_hot'] = intval($data['is_hot']);
        $data['is_show'] = intval($data['is_show']);
        $data['description'] = htmlspecialchars(trim($data['description']),ENT_QUOTES);
        $result = $this->validate($data, 'GoodsBrand');

        if ($result !== true) {
            $this->error($result);
        }
        $goodsBrandModel = new GoodsBrandModel();
        $result = $goodsBrandModel->isUpdate(true)->allowField(true)->save($data);
        if ($result === false) {
            $this->error('保存失败!');
        }
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
        parent::listOrders(Db::name('goods_brand'));
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
        $goodsBrandModel = new GoodsBrandModel();
        $id                  = $this->request->param('id');
        //获取删除的内容
        $findBrandgory = GoodsBrandModel::get($id);

        if (empty($findBrandgory)) {
            $this->error('品牌不存在!');
        }
        // 存在车系 不允许被删除
        $rs = Db::name('goods_car_series')->where(['brand_id'=>$id,'delete_time'=>0])->find();
        if($rs){
            $this->error('请先转移该品牌下的车系');
        }
        // 存在车型不允许被删除
        $rs = Db::name('goods_car_style')->where(['brand_id'=>$id,'delete_time'=>0])->find();
        if($rs){
            $this->error('请先转移该品牌下的车型');
        }
        // 存在商品不允许被删除
        $rs = Db::name('goods')->where(['brand_id'=>$id,'delete_time'=>0])->find();
        if($rs){
            $this->error('请先转移该品牌下的车源');
        }
        $data   = [
            'object_id'   => $findBrandgory['id'],
            'create_time' => time(),
            'table_name'  => 'goods_brand',
            'name'        => $findBrandgory['name'],
            'user_id' =>cmf_get_current_admin_id()
        ];
        $result = $goodsBrandModel
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
     * 商品品牌显示/隐藏
     * @adminMenu(
     *     'name'   => '商品品牌显示/隐藏',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品品牌显示',
     *     'param'  => ''
     * )
     */
    public function updateShowStatus(){
        $id = intval(input('get.id'));
        $is_show = intval(input('get.is_show'));

        if(empty($id)){
            $this->error('更新失败');
        }
       // $goodsBrandModel = new GoodsBrandModel();
        $result = Db::name('goods_brand')->where(['id'=>$id])->update(['is_show'=>$is_show]);
        if($result  === false){
            $this->error('更新失败');
        }
        $this->success('更新成功');


    }




}