<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2018-06-14
 * Time: 13:59
 */

namespace app\goods\controller;


use app\goods\model\CouponModel;
use cmf\controller\AdminBaseController;
use think\db;
use think\exception\ErrorException;


/**
 * Class AdminDealersController
 * @package app\goods\controller
 */
class AdminCouponController extends AdminBaseController
{


    public function _initialize()
    {
        parent::_initialize();
        $this->model = new CouponModel();
    }

    /**
     * 优惠券列表
     * @adminMenu(
     *     'name'   => '优惠券管理',
     *     'parent' => 'goods/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '优惠券列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->where('delete_time','=',0)
                ->order('update_time','desc')
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->where($where)
                ->where('delete_time','=',0)
                ->order('update_time','desc')
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);
            return json($result);

        }
        return $this->fetch();
    }

    /**
     * 添加优惠券
     * @adminMenu(
     *     'name'   => '添加优惠券',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加优惠券',
     *     'param'  => ''
     * )
     */
    public function add()
    {

        return $this->fetch();
    }

    /**
     * 添加优惠券提交
     * @adminMenu(
     *     'name'   => '添加优惠券提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加优惠券提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        if($this->request->isPost()){
            $data = $this->request->param();
            $result = $this->validate($data, 'Coupon.add');
            if($data['expire_time'] === '不过期'){
                $data['expire_time'] = 'all';
            }

            if($data['using_cars'] === '全车型' ){
                $data['using_cars'] = 'all';
            }
            if ($result !== true) {
                $this->error($result);
            }
            $data['create_time'] = time();

            $couponModel = new CouponModel();
            $result = $couponModel->isUpdate(false)->allowField(true)->save($data);
            if ($result === false) {
                $this->error('添加失败!');
            }
            $this->success('添加成功!', url('AdminCoupon/index'));

            $this->error('添加失败!');
        }
    }

    /**
     * 编辑优惠券
     * @adminMenu(
     *     'name'   => '编辑优惠券',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑优惠券',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id > 0) {
           $coupon =  CouponModel::get($id);
           if(!$coupon || $coupon['delete_time'] > 0 ){
               $this->error('优惠券不存在或已删除!');

           }
           $this->assign('coupon',$coupon);
           $this->assign('couponimg',$coupon['img']?cmf_get_image_preview_url($coupon['img']):'');

           return $this->fetch();
        } else {
            $this->error('操作错误!');
        }

    }

    /**
     * 编辑优惠券提交
     * @adminMenu(
     *     'name'   => '编辑优惠券提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑优惠券提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        if($this->request->isPost()){
            $data = $this->request->post();
            $id = $this->request->param('id');
            if(empty($id)){
               $this->error('保存失败!');
            }
            $data['id'] = (int)$id;
            $result = $this->validate($data, 'Coupon.edit');
            if ($result !== true) {
                $this->error($result);
            } else {
                if(isset($data['expire_time']) && ($data['expire_time'] === '不过期')){
                    $data['expire_time'] = 'all';
                }
                if(isset($data['using_cars']) && ($data['using_cars'] === '全车型') ){
                    $data['using_cars'] = 'all';
                }
                $data['update_time'] = time();// 更新时间
                try{
                    $couponModel = new CouponModel();
                    $result = $couponModel->isUpdate(true)->allowField(true)->save($data);
                    if($result){
                        $this->success('保存成功!', url('AdminCoupon/index'));
                    }else{
                        $this->error('保存失败!');
                    }
                }catch (ErrorException $exception){
                    $this->error('保存失败!'.$exception->getMessage());
                }
            }
        }
    }

    /**
     * 优惠券删除
     * @adminMenu(
     *     'name'   => '优惠券删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '优惠券删除',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id = $this->request->param('ids');
        //获取删除的内容
        $findCoupon = CouponModel::get($id);
        if (empty($findCoupon) || $findCoupon['delete_time'] > 0) {
            $this->error('优惠券不存在!');
        }
        $data   = [
            'object_id'   => $findCoupon['id'],
            'create_time' => time(),
            'table_name'  => 'coupon',
            'name'        => $findCoupon['title'],
            'user_id' =>cmf_get_current_admin_id()
        ];
        $couponModel = new CouponModel();
        $result = $couponModel
            ->where('id', $id)
            ->update(['delete_time' => time(),'update_time'=> time()]);
        if ($result) {
            Db::name('recycleBin')->insert($data);
            $this->success('删除成功!');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 优惠券批量删除
     * @adminMenu(
     *     'name'   => '优惠券批量删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '优惠券批量删除',
     *     'param'  => ''
     * )
     */
    public function batchDelete()
    {
        $ids = $this->request->param('ids');
        $ids = explode(',',$ids);
        if(empty($ids)){
            $this->error('优惠券不存在!');
        }
        $success = $error = [];
        $couponModel = new CouponModel();
        $findCoupons =  $couponModel->where('id','in',$ids)->select()->toArray();
        foreach ($findCoupons as $findCoupon){
            $data   = [
                'object_id'   => $findCoupon['id'],
                'create_time' => time(),
                'table_name'  => 'coupon',
                'name'        => $findCoupon['title'],
                'user_id' =>cmf_get_current_admin_id()
            ];
            $result = $couponModel
                ->where('id', $findCoupon['id'])
                ->update(['delete_time' => time(),'update_time'=> time()]);
            if ($result) {
                Db::name('recycleBin')->insert($data);
                $success[] =  $findCoupon['id'];
            } else {
                $error[] = $findCoupon['id'];
            }
        }
        $info['success'] = $success;
        $info['error'] = $error;
        if($info['error']){
            $this->success(count($info['success']).'条记录删除成功!<br/>'.implode(',',$info['error']).'删除失败！');
        }else{
            $this->success(count($info['success']).' 条记录删除成功!');
        }

    }



}