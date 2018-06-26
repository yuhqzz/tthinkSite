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


class AdminAttributeTypeController extends AdminBaseController
{
    public function index()
    {
        $data = Db::name('goods_type')->select();
        $data = $data?$data->toArray():[];
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function add()
    {
        return $this->fetch();
    }
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
    public function delete()
    {
        $id                  = $this->request->param('id');

        $result = Db::name('goods_type')
            ->where('id', $id)
            ->delete();
        if ($result) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败');
        }
    }




}