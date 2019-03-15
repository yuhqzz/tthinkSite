<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2018-10-31
 * Time: 10:46
 */

namespace app\goods\controller;


use app\goods\model\DealerModel;
use app\goods\model\GoodsBrandModel;
use app\goods\service\DealerService;
use cmf\controller\AdminBaseController;

class DealerController extends AdminBaseController
{


    public function _initialize()
    {
        parent::_initialize();
        $this->model = new DealerModel();
    }

    /**
     * 4S店列表
     * @adminMenu(
     *     'name'   => '4S店列表',
     *     'parent' => 'goods/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => 'S店列表',
     *     'param'  => ''
     * )
     */
    public function index(){

        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            if($list){
                foreach ($list as $row){
                    $brandStr = '';
                   if($row['main_brand']){
                       $brands = explode(',',$row['main_brand']);
                       $brandStr = '';
                       foreach ($brands as $val){
                           $brandStr .= getBrandName($val.',');
                       }
                       $brandStr =   ltrim($brandStr,',');
                   }
                   $row['brand_name']  = $brandStr;
                    //$row['series_name']  = $row['series']['name'];
                }
            }

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);
            return json($result);

        }
        return $this->fetch();
    }

    /**
     * 添加4S店
     * @adminMenu(
     *     'name'   => '添加4S店',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加4S店',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $goodsBrandModel = new GoodsBrandModel();
        $brandList =  $goodsBrandModel->getShowBrandList();
        $this->assign('brandList',$brandList);
        return $this->fetch();
    }

    /**
     * 添加4S店提交
     * @adminMenu(
     *     'name'   => '添加4S店提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加4S店提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $data = $this->request->param();

        $result = $this->validate($data, 'Dealer.add');
        if ($result !== true) {
            $this->error($result);
        }
        $dealerModel = new DealerModel();
        $result = $dealerModel->isUpdate(false)->allowField(true)->save($data);
        if ($result === false) {
            $this->error('添加失败!');
        }
        $this->success('添加成功!', url('Dealer/index'));

    }

    /**
     * 编辑4S店信息
     * @adminMenu(
     *     'name'   => '编辑4S店信息',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑4S店信息',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id > 0) {
            $dealer = DealerModel::get($id);
            $dealer = $dealer?$dealer->toArray():[];
            if(empty($dealer)){
                $this->error('4S店不存在或已经删除!');
            }
            $this->assign($dealer);
            $goodsBrandModel = new GoodsBrandModel();
            $brandList =  $goodsBrandModel->getShowBrandList();
            $this->assign('brandList',$brandList);
            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }

    }

    /**
     * 编辑4S店信息提交
     * @adminMenu(
     *     'name'   => '编辑4S店信息提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑4S店信息提交',
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

        $result = $this->validate($data, 'Dealer.edit');

        if ($result !== true) {
            $this->error($result);
        }
        $dealerModel = new DealerModel();
        $result = $dealerModel->isUpdate(true)->allowField(true)->save($data);
        if ($result === false) {
            $this->error('保存失败!');
        }
        $this->success('保存成功!');
    }

    /**
     * 4S店详细信息
     * @adminMenu(
     *     'name'   => '4S店详细信息',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '4S店详细信息',
     *     'param'  => ''
     * )
     */
    public function detail(){
        $id = $this->request->param('id', 0, 'intval');
        if ($id > 0) {
            $dealer = DealerModel::get($id);
            $dealer = $dealer?$dealer->toArray():[];
            if( empty($dealer) ){
                $this->error('4S店不存在或已经删除!');
            }
            $this->assign($dealer);
            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }

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
        $dealerModel = new DealerModel();
        $ids                  = $this->request->param('ids');
       if(!empty($ids)){

           $where['id'] = ['in',array_filter(explode(',',$ids))];
           $dealerModel->where($where)->delete();

           $this->success('删除成功!');
       }
        $this->success('删除成功!');
    }

}