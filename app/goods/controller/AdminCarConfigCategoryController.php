<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2018-06-14
 * Time: 13:59
 */

namespace app\goods\controller;


use cmf\controller\AdminBaseController;
use think\db;


class AdminCarConfigCategoryController extends AdminBaseController
{

    /**
     * 汽车参数配置分类列表
     * @adminMenu(
     *     'name'   => '参数配置分类管理',
     *     'parent' => 'goods/AdminCarConfig/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '商品分类列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $category_data = Db::name('goodsCarConfigCategory')
            ->order('list_order asc')
            ->select()
            ->toArray();
        $this->assign('categoryData', $category_data);

        return $this->fetch();
    }

    /**
     * 添加商品分类
     * @adminMenu(
     *     'name'   => '添加商品分类',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加商品分类',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        return $this->fetch();
    }

    /**
     * 添加商品分类提交
     * @adminMenu(
     *     'name'   => '添加商品分类提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加商品分类提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {

        $data = $this->request->param();

        $result = $this->validate($data, 'GoodsCarConfigCategory');

        if ($result !== true) {
            $this->error($result);
        }


        if ($result === false) {
            $this->error('添加失败!');
        }
        Db::name('goodsCarConfigCategory')->insert($data);


        $this->success('添加成功!', url('AdminCarConfigCategory/index'));

    }

    /**
     * 编辑商品分类
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
            $data = Db::name('goodsCarConfigCategory')->where('id', $id)->find();
            $this->assign($data);

            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }

    }

    /**
     * 编辑商品分类提交
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

        $result = $this->validate($data, 'GoodsCarConfigCategory');

        if ($result !== true) {
            $this->error($result);
        }

        $result = Db::name('goodsCarConfigCategory')->update($data);

        if ($result === false) {
            $this->error('保存失败!');
        }

        $this->success('保存成功!');
    }

    /**
     * 商品分类排序
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
        parent::listOrders(Db::name('goods_car_config_category'));
        $this->success("排序更新成功！", '');
    }

    /**
     * 删除商品分类
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

        $id = $this->request->param('id');
        //获取删除的内容
        $findCategory = Db::name('goodsCarConfigCategory')->where('id', $id)->find();

        if (empty($findCategory)) {
            $this->error('分类不存在!');
        }
//判断此分类有无子分类（不算被删除的子分类）
        $goodsCarConfigItemsCount =  Db::name('goodsCarConfigItems')->where(['cate_id' => $id])->count();

        if ($goodsCarConfigItemsCount > 0) {
            $this->error('此分类有配置项无法删除!');
        }


        $result = Db::name('goodsCarConfigCategory')
            ->where('id', $id)
            ->delete();
        if ($result) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败');
        }


    }




}