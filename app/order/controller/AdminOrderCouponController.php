<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\order\controller;

use app\order\model\OrderBookModel;
use app\order\model\OrderCouponModel;
use cmf\controller\AdminBaseController;
use think\Db;


class AdminOrderCouponController extends AdminBaseController
{
    /**
     * 优惠券订单管理列表
     * @adminMenu(
     *     'name'   => '优惠券订单管理',
     *     'parent' => 'order/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '优惠券订单管理列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $this->model = new OrderCouponModel();
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->with(['coupon'])
                ->where($where)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->with(['coupon'])
                ->where($where)
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
     * 优惠券订单删除
     * @adminMenu(
     *     'name'   => '优惠券订单删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '优惠券订单删除',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $params   = $this->request->param();
        $this->model = new OrderCouponModel();
        if(isset($params['id'])){
            // 单个删除
            $data['ids'] = $this->request->param('id');
        }elseif(isset($params['ids'])){
            // 批量删除
            $data['ids'] = $this->request->param('ids');
            $data['ids'] = array_filter(explode(',',$data['ids'] ));
        }

        if(is_array($data['ids'])){
            // 批量删除
            $where['id'] = ['in',$data['ids']];

            $result = $this->model->where($where)->delete();
            if ($result) {
                $this->success('删除成功!');
            } else {
                $this->error('删除失败');
            }


        }else{
            // 单个删除
            $where['id'] = (int)$data['ids'];
            $result = $this->model->where($where)->delete();
            if ($result) {

                $this->success('删除成功!');
            } else {
                $this->error('删除失败');
            }
        }
    }



    /**
     * 获取报名状态
     */
    public function getBookStatus(){
       // if($this->request->isAjax()){

            $mobile = $this->request->param('tel');
            if(!isTelNumber($mobile)){
                $this->result([],0,'电话号码非法');
            }
            $orderBookModel  = new  OrderBookModel();
            $start_time = strtotime('-7 day');
            $result = $orderBookModel->where('book_telephone','eq',$mobile)->where('book_time','>=',$start_time)->count();
            if( $result > 0 ){
                $this->result([],1);
            }else{
                $this->result([],0);
            }
        //}
       // $this->result([],0,'非法请求');
    }

}
