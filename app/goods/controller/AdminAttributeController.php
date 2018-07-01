<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2018-06-14
 * Time: 13:59
 */

namespace app\goods\controller;


use app\goods\model\GoodsAttributeModel;
use cmf\controller\AdminBaseController;
use think\db;


class AdminAttributeController extends AdminBaseController
{

    public function index()
    {
        $cid =  $this->request->param('type_id');
        if (empty($cid)) {
            $this->error("请指定类型id!");
        }

        $goodsAttributeMod = new GoodsAttributeModel();
        $list = $goodsAttributeMod
            ->where(['type_id'=>$cid])
            ->order('list_order asc,attr_id desc')
            ->paginate(20);
        $this->assign('list', $list);
        $this->assign('cid', $cid);
        return $this->fetch();
    }

    public function add()
    {
        $cid =  $this->request->param('type_id');
        $cid = intval($cid);
        if (empty($cid)) {
            $this->error("请指定属性类型!");
        }
        $data = Db::name('goods_type')->where(['id'=>$cid])->find();
        if(empty($data)){
            $this->error("请指定属性类型!");
        }
        $this->assign($data);
        return $this->fetch();
    }

    public function addPost()
    {
        if($this->request->isPost()){
            $data      = $this->request->post();
            $cate_id = input('post.type_id');
            if(empty($cate_id)){
                $this->error('添加失败!');
            }
            $data['type_id'] = intval($data['type_id']);

            $result = $this->validate($data, 'GoodsAttribute');

            if ($result !== true) {
                $this->error($result);
            }

            if ($result === false) {
                $this->error('添加失败!');
            }
            $goodsAttributeMod = new GoodsAttributeModel();
            $goodsAttributeMod->addAttribute($data);
            $this->success('添加成功!', url('AdminAttribute/add',['type_id'=>$data['type_id']]));
        }
    }

    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id > 0) {
            $goodsAttributeMod = new GoodsAttributeModel();
            //获取删除的内容
            $data = $goodsAttributeMod::get($id);
            $data = $data?$data->toArray():false;
            if(empty($data)){
                $this->error('数据不存在,操作错误!');
            }
            $type_id = intval($data['type_id']);
            $type_data = Db::name('goods_type')->where(['id'=>$type_id])->find();
            if(empty($type_data)){
                $this->error("请指定属性类型!");
            }
            $this->assign($type_data);

            $this->assign($data);
            $this->assign('id',$data['type_id']);
            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }

    }

    public function editPost()
    {

        if($this->request->isPost()){
            $data = $this->request->param();
            $cate_id = intval($data['type_id']);
            $config_id = input('post.attr_id');
            if(empty($cate_id)||empty($config_id)){
                $this->error('保存失败!');
            }
            $data['type_id'] = $cate_id;
            $data['attr_id'] = $config_id;
            $result = $this->validate($data, 'GoodsAttribute');
            if ($result !== true) {
                $this->error($result);
            }
            $goodsAttributeMod = new GoodsAttributeModel();
            $result = $goodsAttributeMod->editAttribute($data);

            if ($result === false) {
                $this->error('保存失败!');
            }
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
        // 有车源引用不允许删除
        $rs = Db::name('goods_attr')->where(['attr_id'=>$id])->find();
        if($rs){
            $this->error('该属性已经被车源引用，无法删除');
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




}