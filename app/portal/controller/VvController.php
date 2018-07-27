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
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use app\order\model\OrderBookModel;
use app\goods\model\GoodsCarStyleModel;
use app\goods\model\GoodsCarSeriesModel;
use think\exception\ErrorException;

class VvController extends HomeBaseController
{
    private $book_car_manger_email = '7124046@qq.com';//'2915295429@qq.com';

    public function index()
    {
        return $this->fetch('index');
    }

    /**
     * p8 车型配置
     * @return mixed
     */
    public function p8Config(){
        return $this->fetch('p8');
    }
    public function vv7Config(){
        return $this->fetch('vv7');
    }
    public function vv5Config(){
        return $this->fetch('vv5');
    }

    /**
     * 试驾表单
     * @return mixed
     */
    public function testDrive(){
        return $this->fetch('testDrive');
    }

    /**
     *  试驾ajax表单提交
     */
    public function ajaxSubmitTestDrive(){
        //dump($this->request->param());
        $series_id = I('da.car');
        $customer_name = I('da.name');
        $mobile = I('da.mobile');
        if(empty($series_id)){
            $this->result('',0,'请选择车型');
        }
        if(empty($customer_name)){
            $this->result('',0,'请填写姓名');
        }
        if(empty($mobile)){
            $this->result('',0,'请填写电话号码');
        }
        if(strlen($mobile) !== 11|| !preg_match('/^(13[0-9]|15[0-9]|18[0-9])\d{8}$/',$mobile)){
            $this->result('',0,'请输入正确的手机号码格式');
        }

        $wh = [];
        //$wh['car_style_id'] = $style_id;
        $wh['series_id'] = $series_id;
        $wh['book_telephone'] = $mobile;
        $wh['dealers_id'] = 0;
        $wh['delete_time'] = 0;
        $orderBookModel = new OrderBookModel();
        $count = $orderBookModel->where($wh)->count();
        if( $count>0 ){
            $this->result('',-1,'您已经预约');
        }
        $data['name'] = $customer_name;
        $data['area_id'] = 4403; // 区级id 深圳
        $data['sex'] = I('da.gender'); // 性别id
        $data['car_style_id'] = 0; // 车型id
        $data['book_time'] = time(); // 发布时间
        $data['book_to_time'] = 0; // 预约到店时间
        $data['book_telephone'] = $mobile; // 电话
        $data['series_id'] = $series_id; // 车系
        try{
            $id = $orderBookModel->addOrderBookData($data);
            if($id){
                // 给运维人员发邮件通知
                $email_config['to'] = $this->book_car_manger_email;
                $email_config['subject'] = '预约试驾';
                $email_tpl = '<table border="0" cellspacing="0" cellpadding="0" width="40%%" align="center">
            <tr>
                <td width="17%%" align="left" height="36" style="font-size: 14px;border-top: 1px solid  #cdd1dc;
    border-left: 1px solid  #cdd1dc; padding-left: 5px;">客&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;户:</td>
                <td width="83%%" align="left" height="36" style="font-size: 14px;color: #40485B;border-top: 1px solid  #cdd1dc;
    border-right: 1px solid  #cdd1dc;">%s</td>
            </tr>
            <tr>
                <td colspan="2" align="left" height="36" style="padding-left:30px;font-size: 14px;border-right: 1px solid #cdd1dc;border-left: 1px solid  #cdd1dc;color:#40485B;">于 %s 预约试驾！</td>
            </tr>
            <tr>
                <td width="17%%" style="font-size: 14px;
    border-left: 1px solid  #cdd1dc; padding-left: 5px;">预约车型:</td>
                <td width="83%%" align="left" height="36" style="font-size: 14px; color: #40485B;
    border-right: 1px solid  #cdd1dc;" >%s</td>
            </tr>
            <tr>
                <td width="17%%" style="font-size: 14px;border-bottom: 1px solid  #cdd1dc;
    border-left: 1px solid  #cdd1dc; padding-left: 5px;">联系电话:</td>
                <td width="83%%" align="left" height="36" style="font-size: 14px;color: #40485B;border-bottom: 1px solid  #cdd1dc;
    border-right: 1px solid  #cdd1dc;" >%s</td>
            </tr>
        </table>';
                $book_date = date('Y-m-d H:i:s',$data['book_time']);
                $book_car_style = '广汽传祺 '.getSeriesName($series_id);
                $body = sprintf($email_tpl,$customer_name,$book_date,$book_car_style,$mobile);
                cmf_send_email($email_config['to'], $email_config['subject'], $body);

                $this->result(['order_book_id'=>$id],1);
            }
        }catch(ErrorException $e){
            $this->result('',0,'服务出错,请稍后再试');
        }
    }

    public function weyvv5(){
        return $this->fetch('index');
    }

    public function weyvv7(){
        return $this->fetch('index');
    }
}
