<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2018-06-14
 * Time: 13:59
 */

namespace app\goods\controller;


use cmf\controller\AdminBaseController;
use app\goods\model\GoodsCarConfigItemsModel;
use think\db;

/**
 * Class AdminCarConfigItemsController
 * @package app\goods\controller
 */
class AdminCarConfigItemsController extends AdminBaseController
{

    /**
     * 汽车参数配置列表
     * @adminMenu(
     *     'name'   => '汽车参数配置列表',
     *     'parent' => 'goods/AdminCarConfigCategory/index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '汽车参数配置列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $cid =  $this->request->param('cid');
        if (empty($cid)) {
            $this->error("请指定配置参数分类!");
        }

        $goodsCarConfigItemsMod = new GoodsCarConfigItemsModel();
        $list = $goodsCarConfigItemsMod
            ->where(['cate_id'=>$cid])
            ->order('list_order asc config_id desc')
            ->paginate(20);
        $this->assign('list', $list);
        $this->assign('cid', $cid);
        return $this->fetch();
    }
    /**
     * 添加汽车参数配置
     * @adminMenu(
     *     'name'   => '添加汽车参数配置',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加汽车参数配置',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $cid =  $this->request->param('cid');
        $cid = intval($cid);
        if (empty($cid)) {
            $this->error("请指定配置参数分类!");
        }
        $data = Db::name('goodsCarConfigCategory')->where(['id'=>$cid])->find();
        if(empty($data)){
            $this->error("请先配置参数分类!");
        }
        $this->assign($data);
        return $this->fetch();
    }
    /**
     * 添加汽车参数配置提交
     * @adminMenu(
     *     'name'   => '添加汽车参数配置提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加汽车参数配置提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {

        $data      = $this->request->post();
        $cate_id = input('post.cate_id');
        if(empty($cate_id)){
            $this->error('添加失败!');
        }
        $data['cate_id'] = $data['cate_id'];

        $result = $this->validate($data, 'GoodsCarConfigItems');

        if ($result !== true) {
            $this->error($result);
        }

        if ($result === false) {
            $this->error('添加失败!');
        }
        $goodsCarConfigItemsMod = new GoodsCarConfigItemsModel();


        $goodsCarConfigItemsMod->addConfigItems($data);

        //$goodsCarConfigItemsMod->allowField(true)->isUpdate(false)->save($data);

        $this->success('添加成功!', url('AdminCarConfigItems/add',['cid'=>$data['cate_id']]));

    }
    /**
     * 编辑汽车参数配置
     * @adminMenu(
     *     'name'   => '编辑汽车参数配置',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑汽车参数配置',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id > 0) {
            $goodsCarConfigItemsMod = new GoodsCarConfigItemsModel();
            //获取删除的内容
            $data = $goodsCarConfigItemsMod::get($id);
            $data = $data?$data->toArray():false;
            if(empty($data)){
                $this->error('数据不存在,操作错误!');
            }
            $this->assign($data);
            $this->assign('id',(int)$data['cate_id']);
            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }
    }
    /**
     * 编辑汽车参数配置提交
     * @adminMenu(
     *     'name'   => '编辑汽车参数配置提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑汽车参数配置提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {

        $data = $this->request->param();
        $cate_id = intval($data['cate_id']);
        $config_id = input('post.config_id');
        if(empty($cate_id)||empty($config_id)){
            $this->error('保存失败!');
        }
        $data['cate_id'] = $cate_id;
        $data['config_id'] = $config_id;
        $result = $this->validate($data, 'GoodsCarConfigItems');
        if ($result !== true) {
            $this->error($result);
        }
        $goodsCarConfigItemsMod = new GoodsCarConfigItemsModel();
        $result = $goodsCarConfigItemsMod->editConfigItems($data);

        if ($result === false) {
            $this->error('保存失败!');
        }
        $this->success('保存成功!');
    }
    /**
     * 汽车参数配置排序
     * @adminMenu(
     *     'name'   => '汽车参数配置排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '汽车参数配置排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        parent::listOrders(Db::name('goods_car_config_items'));
        // 清缓存
        $goodsCarConfigItemsMod = new GoodsCarConfigItemsModel();
        $goodsCarConfigItemsMod->clearCache(0,true);
        $this->success("排序更新成功！", '');
    }
    /**
     * 汽车参数配置删除
     * @adminMenu(
     *     'name'   => '汽车参数配置删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '汽车参数配置删除',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id = $this->request->param('id');
        if(!$id){
            $this->error('删除失败');
        }
        $goodsCarConfigItemsMod = new GoodsCarConfigItemsModel();
        $findCategory = $goodsCarConfigItemsMod::get($id);

        if (empty($findCategory)) {
            $this->error('配置项不存在!');
        }
        $goodsCarConfigItemsMod = new GoodsCarConfigItemsModel();
        $result = $goodsCarConfigItemsMod->where('config_id', $id)->delete();
        if ($result) {
            $goodsCarConfigItemsMod->clearCache($findCategory['cate_id']);
            $this->success('删除成功!');
        } else {
            $this->error('删除失败');
        }

    }




}