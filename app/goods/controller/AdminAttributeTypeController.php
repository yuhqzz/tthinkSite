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

/**
 * Class AdminAttributeTypeController
 * @package app\goods\controller
 */
class AdminAttributeTypeController extends AdminBaseController
{

    /**
     * 商品属性模型列表
     * @adminMenu(
     *     'name'   => '属性模型',
     *     'parent' => 'goods/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '属性模型管理',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $data = Db::name('goods_type')->select();
        $data = $data?$data->toArray():[];
        $this->assign('data',$data);
        return $this->fetch();
    }
    /**
     * 添加属性模型
     * @adminMenu(
     *     'name'   => '添加属性模型',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加属性模型',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        return $this->fetch();
    }

    /**
     * 添加属性模型提交
     * @adminMenu(
     *     'name'   => '添加属性模型提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加属性模型提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
           // dump($data);die;
            $result = $this->validate($data, 'GoodsAttributeType');
            if ($result !== true) {
                $this->error($result);
            } else {

                $result = Db::name('goods_type')->insert($data);
                if ($result) {
                    $this->success("添加成功", url("AdminAttributeType/index"));
                } else {
                    $this->error("添加失败");
                }

            }
        }

    }

    /**
     * 编辑属性模型
     * @adminMenu(
     *     'name'   => '编辑属性模型',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑属性模型',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id > 0) {
            $data = Db::name('goods_type')->where(["id" => $id])->find();
            if (!$data) {
                $this->error("该类型不存在！");
            }
            $this->assign($data);
            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }

    }

    /**
     * 编辑属性模型提交
     * @adminMenu(
     *     'name'   => '编辑属性模型提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑属性模型提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $result = $this->validate($data, 'GoodsAttributeType');
            if ($result !== true) {
                // 验证失败 输出错误信息
                $this->error($result);

            } else {
                if (Db::name('goods_type')->update($data) !== false) {
                    $this->success("保存成功！", url('AdminAttribute/index'));
                } else {
                    $this->error("保存失败！");
                }
            }
        }
    }

    /**
     * 删除属性模型提交
     * @adminMenu(
     *     'name'   => '删除属性模型提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除属性模型提交',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id  = $this->request->param('id');

        $result = Db::name('goods_type')
            ->where('id', $id)
            ->delete();
        // 有车源引用不允许被删除
        $rs = Db::name('goods')->where(['model_id'=>$id,'delete_time'=>0])->find();
        if($rs){
            $this->error('该模型已经被车源引用,不允许被删除');
        }
        if ($result) {
            // 删除该模型下所有属性
            Db::name('goods_attribute')->where(['type_id'=>$id])->delete();
            $this->success('删除成功!');
        } else {
            $this->error('删除失败');
        }
    }




}