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
use app\goods\service\CarConfigService;
use think\db;


class AdminCarConfigTemplateController extends AdminBaseController
{

    /*
     * 模板列表
     *
     */
    public function index()
    {

        /*$list = Db::name('goods_car_config_template')
            ->order('list_order asc ')
            ->paginate(20);
        $this->assign('list', $list);*/
        /*$data = CarConfigService::getConfigList();

        dump($data);*/

        return $this->fetch();
    }

    public function add()
    {
        $configList = CarConfigService::getConfigList();
        $this->assign('configList',$configList);
        return $this->fetch();
    }

    public function addPost()
    {

        $data      = $this->request->post();

        dump($data);exit;


       /* $cate_id = input('post.cate_id');
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

        $this->success('添加成功!', url('AdminCarConfigItems/add',['cid'=>$data['cate_id']]));*/

    }

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

    public function listOrder()
    {
        parent::listOrders(Db::name('goods_car_config_items'));
        $this->success("排序更新成功！", '');
    }

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
            $this->success('删除成功!');
        } else {
            $this->error('删除失败');
        }

    }




}