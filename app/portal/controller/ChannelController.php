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

use app\admin\model\LotteryModel;
use app\admin\model\LotteryRecordModel;
use app\admin\model\LotteryUserModel;
use app\goods\model\CouponModel;
use app\goods\model\DealerModel;
use app\goods\service\DealerService;
use app\order\model\OrderCouponModel;
use cmf\controller\HomeBaseController;
use app\order\model\OrderBookModel;
use app\goods\model\GoodsCarStyleModel;
use app\goods\model\GoodsCarSeriesModel;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\exception\ErrorException;
use think\File;


class ChannelController extends HomeBaseController
{
    private $book_car_manger_email = [
                       3=>['7124046@qq.com'],
                       5=>['642406378@qq.com'],
                       6=>['642406378@qq.com'],
                       7=>['642406378@qq.com']
    ];

    private $test_drive_form = ['cq'=>0,'wey'=>1,'bf'=>2];
    public function index()
    {

    }

    /**
     * 品牌预约试驾落地页（统一固定模板）
     * @return bool|mixed|void
     */
    public function book(){
        $brand_name = I('b-n');
        if(empty($brand_name)) return $this->error('参数出错','/');
        $page_data = $this->_bookPageData($brand_name);
        if(empty($page_data)) return $this->error('参数出错','/');



        $dealer_info = $page_data['dealer'];
        //bb($page_data);die;
        $brand_id = $page_data['brand_id'];
        $series_data = $page_data['series'];
        $this->assign('seriesData',$series_data);
        $this->assign('brand_id',$brand_id);
        $this->assign('brand_name',$page_data['brand_name']);
        $this->assign('page',$page_data);
        $this->assign('dealer_info',json_encode($dealer_info));
        $this->assign('source_id',$brand_id);

        // 获取4S店信息
        $dealerSer = new DealerService();
        $dealer_data = $dealerSer->getDealer();
        $dealer_item = $dealer_data[$brand_id];

        $this->assign('dealers',$dealer_item);
        $this->assign('bn',$brand_name);
        if(cmf_is_mobile()){
            return $this->fetch('book_mobile');
        }else{
            return $this->fetch('book');
        }
    }
    public function multi_brand(){
        $setting_domain = ['http://1212.szdxhonda.cn', 'http://1212.szdxcq.cn', 'http://1212.szdxwey.com'];
        $current_domain =  cmf_get_domain();
        //指定域名页面
        if(in_array($current_domain,$setting_domain)){
            $brand_data = $this->_getCommonTplData('all');
            $new_brand_data = [];
            $source_id = '';
            $domain_code = '';
            switch ($current_domain){
                case 'http://1212.szdxhonda.cn':
                case 'https://1212.szdxhonda.cn':
                    $brands = [
                        16 =>"广汽本田",
                        3  =>"长城WEY",
                        5  =>"广汽传祺"
                    ];
                    $new_brand_data['GUANGBEN'] =  $brand_data['GUANGBEN'];
                    $new_brand_data['WEY'] =  $brand_data['WEY'];
                    $new_brand_data['CHUANQI'] =  $brand_data['CHUANQI'];
                    $source_id = 92;
                    $domain_code = 'szdxhonda';
                    break;
                case 'http://1212.szdxcq.cn':
                case 'https://1212.szdxcq.cn':
                    $brands = [
                        5  =>"广汽传祺",
                        3  =>"长城WEY",
                        16 =>"广汽本田"
                    ];

                    $new_brand_data['CHUANQI'] =  $brand_data['CHUANQI'];
                    $new_brand_data['WEY'] =  $brand_data['WEY'];
                    $new_brand_data['GUANGBEN'] =  $brand_data['GUANGBEN'];
                    $source_id = 91;
                    $domain_code = 'szdxcq';
                    break;
                case 'http://1212.szdxwey.com':
                case 'https://1212.szdxwey.com':
                    $brands = [
                        3  =>"长城WEY",
                        5  =>"广汽传祺",
                        16 =>"广汽本田"
                    ];
                    $new_brand_data['WEY'] =  $brand_data['WEY'];
                    $new_brand_data['CHUANQI'] =  $brand_data['CHUANQI'];
                    $new_brand_data['GUANGBEN'] =  $brand_data['GUANGBEN'];
                    $source_id = 90;
                    $domain_code = 'szdxwey';
                    break;
                default:
                    $brands = [
                        3  =>"长城WEY",
                        5  =>"广汽传祺",
                        16 =>"广汽本田"
                    ];
                    $new_brand_data['WEY'] =  $brand_data['WEY'];
                    $new_brand_data['CHUANQI'] =  $brand_data['CHUANQI'];
                    $new_brand_data['GUANGBEN'] =  $brand_data['GUANGBEN'];
                    $source_id = 90;
                    $domain_code = 'szdxwey';
            }

            $dealerSer = new DealerService();
            $dealer = $dealerSer->getDealer();
            $this->assign('dealers',json_encode($dealer));
            $this->assign('brandData',$new_brand_data);
            $this->assign('brands',$brands);
            $this->assign('source_id',$source_id);
            $this->assign('domain_code',$domain_code);

            if(cmf_is_mobile()){
                return $this->fetch('multi_brand_two_mobile');
            }else{
                return $this->fetch('multi_brand_two');
            }

        }else{

            $brand_data = $this->_getCommonTplData('all');
            $dealerSer = new DealerService();
            $dealer = $dealerSer->getDealer();
            $this->assign('dealers',json_encode($dealer));
            $this->assign('brandData',$brand_data);
            $this->assign('source_id',99);
            if(cmf_is_mobile()){
                return $this->fetch('multi_brand_mobile');
            }else{
                return $this->fetch('multi_brand');
            }
        }
    }

    public function multi_brand_back(){
        $brand_data = $this->_getCommonTplData('all');
        $data1 = [];
        $data2 = [];
        $data3 = [];
        $data4 = [];
        $data99 = [];
        foreach($brand_data as $item){
            foreach( $item['series'] as $key => $val ){
                $val['brand_id'] = $item['brand_id'];
                $val['brand_name'] = $item['brand_name'];
                $val['brand_alias'] = $item['brand_alias'];
                switch($key){
                    case 0:
                        $data1[] = $val;
                        break;
                    case 1:
                        $data2[] = $val;
                        break;
                    case 2:
                        $data3[] = $val;
                        break;
                    case 3:
                        $data4[] = $val;
                        break;
                    default:
                        $data99[] = $val;
                }
            }
        }
        $series_data =  array_merge($data1,$data2,$data3,$data4,$data99);
        $this->assign('brandData',$brand_data);
        $this->assign('seriesData',$series_data);
        $this->assign('dealers_id',99);
        if(cmf_is_mobile()){
            return $this->fetch('multi_brand_mobile');
        }else{
            return $this->fetch('multi_brand');
        }
    }
    // chuanqi
    public function chuanqi(){

        //$series = new GoodsCarSeriesModel();
        $brand_id = 5;
        $brand_name = '广汽传祺';

        $chuanqi = [
            [
                'id'=>7,
                'name'=>'传祺GS4',
                'price'=>'8.98',
                'img'=>'gs4.jpg',
            ],
            [
                'id'=>8,
                'name'=>'传祺GS8',
                'price'=>'16.38',
                'img'=>'gs8.jpg',
            ],
            [
                'id'=>9,
                'name'=>'传祺GS3',
                'price'=>'7.38',
                'img'=>'gs3.jpg',
            ],
            [
                'id'=>6,
                'name'=>'传祺GS7',
                'price'=>'14.98',
                'img'=>'gs7.jpg',
            ],
            [
                'id'=>10,
                'name'=>'传祺GA6',
                'price'=>'11.68',
                'img'=>'ga6.jpg',
            ],
            [
                'id'=>11,
                'name'=>'传祺GA8',
                'price'=>'14.98',
                'img'=>'ga8.jpg',
            ],
            [
                'id'=>4,
                'name'=>'传祺GA4',
                'price'=>'7.38',
                'img'=>'ga4.jpg',
            ],
            [
                'id'=>5,
                'name'=>'传祺GM8',
                'price'=>'17.68',
                'img'=>'gm8.jpg',
            ],
            [
                'id'=>26,
                'name'=>'传祺GM6',
                'price'=>'11.50',
                'img'=>'gm8.jpg',
            ],
        ];
        $this->assign('chuanqi',$chuanqi);
        $this->assign('brand_id',$brand_id);
        $this->assign('brand_name',$brand_name);
        if(cmf_is_mobile()){
            return $this->fetch('chuanqi_mobile');
        }else{
            return $this->fetch('chuanqi');
        }
    }

    // wey p8
    public function weyP8(){
        if(cmf_is_mobile()){
            return $this->fetch('mobile_wey');
        }else{
            $brand_id = 3;//wey
            $goodsCarSeriesModel = new  GoodsCarSeriesModel();
            $weyp8_styles =  $goodsCarSeriesModel->getCarSeriesByBrandId($brand_id);
            $this->assign('weyp8Styles',$weyp8_styles);
            $car_configs = $this->p8ConfigData();
            $this->assign('carConfigs',$car_configs);
            return $this->fetch('wey_p8');
        }
    }

    public function weyVV6(){
        if(isMobile()){
            return $this->fetch('wey_vv6_mobile');
        }
        return $this->fetch('wey_vv6');
    }

    public function vv6_config(){
        return $this->fetch('vv6_config');
    }

    public function ajaxWeyTestDrive(){
        return $this->fetch('test_drive');
    }
    /**
     * 获取车型表单
     * @return mixed
     */
    public function ajaxWeyForm(){
        $series_id = I('series_id');
        $goodsCarStyleModel = new  GoodsCarStyleModel();
        $wey_styles = $goodsCarStyleModel->getCarStyleDataBySeriesId($series_id);
        $this->assign('weyCarStyles',$wey_styles);
        $this->assign('series_id',$series_id);
        return $this->fetch('ajax_wey_form');
    }
    /**
     * 预约试驾表单提交
     *
     */
    public function ajaxSubmitBookCar(){
        $series_id = I('series_id');
        //$style_id = I('style_id');
        $customer_name = I('customer_name');
        $mobile = I('mobile');
        if(empty($series_id)){
            $this->result('',0,'请选择车型');
        }
        /*if(empty($style_id)){
            $this->result('',0,'请选择车型');
        }*/
        if(empty($customer_name)){
            $this->result('',0,'请填写姓名');
        }
        if(empty($mobile)){
            $this->result('',0,'请填写电话号码');
        }
        if(strlen($mobile) !== 11|| !preg_match('/^(13[0-9]|15[0-9]|18[0-9])\d{8}$/',$mobile)){
            $this->result('',0,'请输入正确的手机号码格式');
        }
        // 获取df 来源字段
        // cq ,wey, bh
        $dealers_id = I('dealer');
        $dealers_id = (int)$dealers_id;
        if(empty($dealers_id)){
            $this->result('',0,'请选择要预约的4S店');
        }
        // 来源页面
        $source_id = intval(I('source_id'));

        $source_id = $source_id?$source_id:0;
        $wh = [];
        //$wh['car_style_id'] = $style_id;
        $wh['series_id'] = $series_id;
        $wh['book_telephone'] = $mobile;
        $wh['dealers_id'] = $dealers_id;
        $wh['delete_time'] = 0;
        $orderBookModel = new OrderBookModel();
        $count = $orderBookModel->where($wh)->count();
        if($count>0){
            $this->result('',-1,'您已经预约');
        }
        $data['name'] = $customer_name;
        $data['area_id'] = 4403; // 区级id 深圳
        $data['sex'] = 0; // 性别id
        $data['car_style_id'] = 0; // 车型id
        $data['book_time'] = time(); // 发布时间
        $data['book_to_time'] = 0; // 预约到店时间
        $data['book_telephone'] = $mobile; // 电话
        $data['series_id'] = $series_id; // 车系
        $data['dealers_id'] = $dealers_id; // 店面id
        $data['source'] = $source_id; // 线索来源页面

        try{
            $id = $orderBookModel->addOrderBookData($data);
            if($id){
                // 如果有转盘抽奖
                $lottery_id = I('lottery_id');
                $lottery_id = 1;
                if( $lottery_id ){
                    // 报名参加 抽奖
                    $lotteryUserModel = new LotteryUserModel();
                    if( !$lotteryUserModel->isBooked($lottery_id,$mobile) ){
                        $lotteryUserModel->booking($lottery_id,$mobile,3,$source_id);
                    }
                    // 设置抽奖的手机号码cookie 1天内有效
                    cookie('lottery_phone',$mobile,86400);
                }

                // 给运维人员发邮件通知
                //  $email_config['to'] = $this->book_car_manger_email;
                $email_config['subject'] = '预约试驾';
                $email_tpl = '<table border="0" cellspacing="0" cellpadding="0" width="60%%" align="center" >
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
            </tr>';
                if($dealers_id){
                    $email_tpl .='<tr>
                <td width="17%%" style="font-size: 14px;
    border-left: 1px solid  #cdd1dc; padding-left: 5px;">4S店:</td>
                <td width="83%%" align="left" height="36" style="font-size: 14px; color: #40485B;
    border-right: 1px solid  #cdd1dc;" >%s</td></tr>';
                }
                $email_tpl .= '
            <tr>
                <td width="17%%" style="font-size: 14px;
    border-left: 1px solid  #cdd1dc; padding-left: 5px;">来源页面:</td>
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
                $seriesModel = new GoodsCarSeriesModel();
                $brand_data = $seriesModel->getBrandBySeriesId($series_id);
                $book_date = date('Y-m-d H:i:s',$data['book_time']);
                // $email_config['to'] = $this->book_car_manger_email[$brand_data['brand_id']][0];
                $book_car_style = $brand_data['brand_name'].' '.$brand_data['series_name'];
                if(in_array($source_id,[90,91,92,93,94,99])){
                    $source = '多品牌页面';
                }else{
                    $source = $brand_data['brand_name'].'页面';
                }

                //配置固定邮箱
                if(isset($this->book_car_manger_email[$brand_data['brand_id']]) && empty($dealers_id)){
                    $body = sprintf($email_tpl,$customer_name,$book_date,$book_car_style,$source,$mobile);
                    foreach($this->book_car_manger_email[$brand_data['brand_id']] as $email_user){
                        //cmf_send_email($email_user, $email_config['subject'], $body);
                    }
                }
                //给4S店运维发邮件通知
                if($dealers_id){
                    try{
                        $dealer = DealerModel::get($dealers_id);
                        if($dealer['email']){
                            $body = sprintf($email_tpl,$customer_name,$book_date,$book_car_style,$dealer['name'],$source,$mobile);
                            $dealer_emails = explode(',',$dealer['email']);
                            if($dealer_emails){
                                foreach ( $dealer_emails as $email){
                                    if(empty($email)){
                                        continue;
                                    }
                                    //cmf_send_email($email, $email_config['subject'], $body);
                                }
                            }
                        }
                    }catch (DataNotFoundException $foundException){

                    }
                }

                $this->result(['order_book_id'=>$id],1);
            }
        }catch(ErrorException $e){
            $this->result('',0,'服务出错,请稍后再试');
        }

    }

    public function ajaxCoupon(){
        $mobile = I('mobile');
        if(empty($mobile)){
            $this->result('',0,'请填写电话号码');
        }
        if(strlen($mobile) !== 11|| !preg_match('/^(13[0-9]|15[0-9]|18[0-9])\d{8}$/',$mobile)){
            $this->result('',0,'请输入正确的手机号码格式');
        }
        $coupon_id = I('coupon_id');
        if(empty($coupon_id)){
            $this->result('',0,'优惠券已经派送完成！');
        }
        $coupon = CouponModel::get($coupon_id)->toArray();
        if( empty($coupon) || $coupon['delete_time']>0 || $coupon['status'] == 0){
            $this->result('',0,'优惠券已经派送完成！');
        }

        $wh = [];
        $wh['telephone'] = $mobile;
        $wh['coupon_id'] = $coupon_id;
        $couponOrderModel = new OrderCouponModel();
        $count = $couponOrderModel->where($wh)->count();
        if($count > 0){
            $this->result('',-1,'您已经领取过该优惠券!');
        }

        $hx_code = cmf_random_string(10);
        $create_time = time();
        $data['coupon_id'] = $coupon_id;
        $data['telephone'] = $mobile;
        $data['code'] = $hx_code;
        $data['create_time'] = $create_time;
        $data['remark'] = 0;


        try{
            $id = $couponOrderModel->addOrderCouponData($data);
            if($id){
                // 给运维人员发邮件通知
                $email_config['subject'] = '领取优惠券';
                $email_tpl = '
        <table border="0" cellspacing="0" cellpadding="0" width="80%%" align="center" bgcolor="#f9f0f0" >
            <tr>
                <td colspan="2" align="left" height="36" style="padding-left:10px;font-size: 16px;border-right: 1px solid #cdd1dc;border-left: 1px solid  #cdd1dc;color:#40485B;">客户 <span style="font-weight: bold;">%s</span> 领取了%s。</td>
            </tr>
            <tr>
                <td width="27%%" style="font-size: 14px;
    border-left: 1px solid  #cdd1dc; padding-left: 5px;">领取时间:</td>
                <td width="53%%" align="left" height="36" style="font-size: 14px; color: #40485B;
    border-right: 1px solid  #dc99dc;" >%s</td>
            </tr>
            <tr>
                <td width="27%%" style="font-size: 14px;
    border-left: 1px solid  #cdd1dc; padding-left: 5px;">优惠券:</td>
                <td width="53%%" align="left" height="36" style="font-size: 14px; color: #40485B;
    border-right: 1px solid  #cdd1dc;" >%s</td>
            </tr>
            <tr>
                <td width="27%%" style="font-size: 14px;
    border-left: 1px solid  #cdd1dc; padding-left: 5px;">适用车型:</td>
                <td width="53%%" align="left" height="36" style="font-size: 14px; color: #40485B;
    border-right: 1px solid  #cdd1dc;" >%s</td>
            </tr>
            <tr>
                <td width="27%%" style="font-size: 14px;
    border-left: 1px solid  #cdd1dc; padding-left: 5px;">有效期:</td>
                <td width="53%%" align="left" height="36" style="font-size: 14px; color: #40485B;
    border-right: 1px solid  #cdd1dc;" >%s</td>
            </tr>
            <tr>
                <td width="27%%" style="font-size: 14px;
    border-left: 1px solid  #cdd1dc; padding-left: 5px;">核销码:</td>
                <td width="53%%" align="left" height="36" style="font-size: 14px; color: #40485B;
    border-right: 1px solid  #cdd1dc;" >%s</td>
            </tr>
            <tr>
                <td width="27%%" style="font-size: 14px;
    border-left: 1px solid  #cdd1dc; padding-left: 5px;">来源页面:</td>
                <td width="53%%" align="left" height="36" style="font-size: 14px; color: #40485B;
    border-right: 1px solid  #cdd1dc;" >%s</td>
            </tr>
        </table>';
            //给4S店运维发邮件通知
            if(cmf_is_mobile()){
                $source ='移动端页面';
            }else{
                $source = 'pc页面';
            }
            try{

                switch ($coupon['type']){
                    case 0://优惠券
                        $coupon_type = '优惠券';
                        $coupon_title = $coupon['coupon_price']? $coupon['coupon_price'].$coupon['title']:$coupon['title'];
                        if( $coupon['using_cars'] === 'all' ){
                            $coupon_desc = 'WEY 全系车有效';
                        }else{
                            $coupon_desc = "仅WEY ".$coupon['using_cars']."有效";
                        }
                        if( $coupon['expire_time'] === 'all' ){
                            $coupon_expire = '长期有效';
                        }else{
                            $coupon_expire = $coupon['expire_time']." 23:59:59 截止";
                        }
                        $email_config['subject'] = '领取优惠券';
                        break;
                    case 1://抵用券
                        $coupon_type = '抵用券';
                        $coupon_title = $coupon['coupon_price']? $coupon['coupon_price'].$coupon['title']:$coupon['title'];
                        if( $coupon['using_cars'] === 'all' ){
                            $coupon_desc = 'WEY 全系车有效';
                        }else{
                            $coupon_desc = "仅WEY ".$coupon['using_cars']."有效";
                        }
                        if( $coupon['expire_time'] === 'all' ){
                            $coupon_expire = '长期有效';
                        }else{
                            $coupon_expire = $coupon['expire_time']." 23:59:59 截止";
                        }
                        $email_config['subject'] = '领取抵用券';
                        break;
                    case 2://折扣券
                        $coupon_type = '折扣券';
                        $coupon_title = $coupon['coupon_price']? $coupon['coupon_price'].'折'.$coupon['title']:$coupon['title'];
                        if( $coupon['using_cars'] === 'all' ){
                            $coupon_desc = 'WEY 全系车有效';
                        }else{
                            $coupon_desc = "仅WEY ".$coupon['using_cars']."有效";
                        }
                        if( $coupon['expire_time'] === 'all' ){
                            $coupon_expire = '长期有效';
                        }else{
                            $coupon_expire = $coupon['expire_time']." 23:59:59 截止";
                        }
                        $email_config['subject'] = '领取折扣券';
                        break;
                    case 3://特权券
                        $coupon_type = '特权券';
                        $coupon_title = $coupon['title'];
                        if( $coupon['using_cars'] === 'all' ){
                            $coupon_desc = 'WEY 全系车有效';
                        }else{
                            $coupon_desc = "仅WEY ".$coupon['using_cars']."有效";
                        }
                        if( $coupon['expire_time'] === 'all' ){
                            $coupon_expire = '长期有效';
                        }else{
                            $coupon_expire = $coupon['expire_time']." 23:59:59 截止";
                        }
                        $email_config['subject'] = '领取特权券';
                        break;
                    default:
                        $coupon_type = '';
                        $coupon_title = '';
                        $coupon_desc = '';
                        $coupon_expire = '';
                }
                $dealer = DealerModel::get(1)->toArray();
                $create_date = date('Y-m-d H:i:s',$create_time);
                $body = sprintf( $email_tpl,$mobile,$coupon_type,$create_date,$coupon_title,$coupon_desc,$coupon_expire,$hx_code,$source);
                $dealer_emails = explode(',',$dealer['email']);
                if($dealer_emails){
                    foreach ( $dealer_emails as $email){
                        if(empty($email)){
                            continue;
                        }
                        cmf_send_email($email, $email_config['subject'], $body);
                    }
                }
            }catch (DataNotFoundException $foundException){

            }

            $this->result(['order_book_id'=>$id],1);
            }
        }catch(ErrorException $e){
            $this->result('',0,'服务出错,请稍后再试'.$e->getMessage());
        }
    }

    /**
     *
     * 转盘抽奖
     *
     */
    public function drawLottery(){
        if($this->request->isPost()){
            //$mobile = $this->request->post('mobile','','trim');
            $mobile =  cookie('lottery_phone');
            $lottery_id = $this->request->post('lotteryid',1,'intval');
            if( empty($mobile) ){
                $this->result([],-1,'要先报名才能参与抽奖哦');
            }
            $lotteryUser = new LotteryUserModel();
            $lottery_user_info = $lotteryUser->where('lottery_id','=',$lottery_id)
                                ->where('mobile','=',$mobile)
                                ->find();

           // echo $lotteryUser->getLastSql();
           // die;
            if( !$lottery_user_info ){
                $this->result([],-1,'要先报名才能参与抽奖哦');
            }
            $lottery_user_info = $lottery_user_info->toArray();

            if( $lottery_user_info['draw_count'] == 0 ){
                $this->result([],-2,'谢谢参与，你已经无可用抽奖次数了！');
            }
            $lotteryModel = new LotteryModel();
            $lottery_opt_id  = $lotteryModel->lotterying();
            // 抽奖次数-1
            if( $lottery_user_info['draw_count'] >=1 ){
                // 抽奖次数-1
                $lotteryUser->where(['mobile'=>$mobile,'lottery_id'=>$lottery_id])->setDec('draw_count',1);
            }else{
                $lotteryUser->where(['mobile'=>$mobile,'lottery_id'=>$lottery_id])->save(['draw_count'=>0]);
            }
            if( $lottery_opt_id > 0){
                // TODO 插入中奖纪录
                $lotteryRecordModel =  new LotteryRecordModel();
                $add['lottery_id'] = $lottery_id;
                $add['lottery_user_id'] = $lottery_id;
                $add['lottery_opt_id'] = $lottery_id;
                $add['createtime'] = time();
                $rs = $lotteryRecordModel->create($add);
                if( !$rs ){
                    // todo 写日志
                }
            }
            $num =  $this->_parseLotteryOption($lottery_opt_id);
            $this->result(['num'=>$num ],1,'success');
        }else{
            $this->result([],0,'非法请求');
        }
    }

    public function getDrawLotteryNumber(){
        $mobile =  cookie('lottery_phone');
        $num = 0;
        $lottery_id = $this->request->post('lotteryid',1,'intval');
        if( empty($mobile) ){
            $this->result(['num'=>$num],1,'success');
        }
        $lotteryUser = new LotteryUserModel();
        $lottery_user_info = $lotteryUser->where('lottery_id','=',$lottery_id)
            ->where('mobile','=',$mobile)
            ->find();
        if( $lottery_user_info && $lottery_user_info->draw_count){
            $lottery_user_info = $lottery_user_info->toArray();
            $num = $lottery_user_info['draw_count']>0?(int)$lottery_user_info['draw_count']:0;
        }
        $this->result(['num'=>$num],1,'success');
    }

    /**
     *
     * 咨询车型底价
     *
     */
    public function ajaxSubmitAskCarPrice(){
        $series_id = I('series_id');
        $style_id = I('style_id');
        $customer_name = I('customer_name');
        $mobile = I('mobile');
        if(empty($series_id)){
            $this->result('',0,'请选择车系');
        }
        if(empty($style_id)){
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
        $wh['car_style_id'] = $style_id;
        $wh['series_id'] = $series_id;
        $wh['book_telephone'] = $mobile;
        $wh['dealers_id'] = 0;
        $wh['delete_time'] = 0;
        $orderBookModel = new OrderBookModel();
        $count = $orderBookModel->where($wh)->count();
        if($count>0){
            $this->result('',-1,'您已经咨询');
        }
        $data['name'] = $customer_name;
        $data['area_id'] = 4403; // 区级id 深圳
        $data['sex'] = 0; // 性别id
        $data['car_style_id'] = 0; // 车型id
        $data['book_time'] = time(); // 发布时间
        $data['book_to_time'] = 0; // 预约到店时间
        $data['book_telephone'] = $mobile; // 电话
        $data['series_id'] = $series_id; // 车系
        $data['remark'] = '咨询底价'; // 车系

        $id = $orderBookModel->addOrderBookData($data);
        if($id){
            // 给运维人员发邮件通知
            //$email_config['to'] = 'qingcaozhi@163.com';
            //$email_config['to'] = $this->book_car_manger_email;
            $email_config['subject'] = '咨询车型底价';
            $email_tpl = '<table border="0" cellspacing="0" cellpadding="0" width="60%%" align="center">
        <tr>
            <td width="17%%" align="left" height="36" style="font-size: 14px;border-top: 1px solid  #cdd1dc;
border-left: 1px solid  #cdd1dc; padding-left: 5px;">客&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;户:</td>
            <td width="83%%" align="left" height="36" style="font-size: 14px;color: #40485B;border-top: 1px solid  #cdd1dc;
border-right: 1px solid  #cdd1dc;">%s</td>
        </tr>
        <tr>
            <td colspan="2" align="left" height="36" style="padding-left:30px;font-size: 14px;border-right: 1px solid #cdd1dc;border-left: 1px solid  #cdd1dc;color:#40485B;">于 %s 咨询车型底价！</td>
        </tr>
        <tr>
            <td width="17%%" style="font-size: 14px;
border-left: 1px solid  #cdd1dc; padding-left: 5px;">咨询车型:</td>
            <td width="83%%" align="left" height="36" style="font-size: 14px; color: #40485B;
border-right: 1px solid  #cdd1dc;" >%s</td>
        </tr>
        <tr>
                <td width="17%%" style="font-size: 14px;
    border-left: 1px solid  #cdd1dc; padding-left: 5px;">来源页面:</td>
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
            $seriesModel = new GoodsCarSeriesModel();
            $brand_data = $seriesModel->getBrandBySeriesId($series_id);
            $book_date = date('Y-m-d H:i:s',$data['book_time']);
           // $email_config['to'] = $this->book_car_manger_email[$brand_data['brand_id']][0];
            $book_car_style = $brand_data['brand_name'].' '.$brand_data['series_name'];
            $body = sprintf($email_tpl,$customer_name,$book_date,$book_car_style,$mobile);
            if(isset($this->book_car_manger_email[$brand_data['brand_id']])){
                foreach($this->book_car_manger_email[$brand_data['brand_id']] as $email_user){
                    $result = cmf_send_email($email_user, $email_config['subject'], $body);
                }
            }
            //$result = cmf_send_email($email_config['to'], $email_config['subject'], $body);
            $this->result(['order_book_id'=>$id],1);
            /*if ($result && empty($result['error'])) {
                $this->success('发送成功！');
            } else {
                $this->error('发送失败：' . $result['message']);
            }*/
        }else{
            $this->result('',0,'服务出错,请稍后再试');
        }
    }
    /**
     * 预约试驾表单提交
     *
     */
    public function ajaxSubmitBookCar2(){
        $result = [];
        $series_id = I('series_id');
        $customer_name = I('name');
        $dealers_id = I('dealers_id');
        $mobile = I('mobile');
        if(empty($series_id)){
            $result['status'] = 0;
            $result['msg'] = '请选择车型';
            echo  json_encode($result);
            exit;
        }
        if(empty($customer_name)){
            $result['status'] = 0;
            $result['msg'] = '请填写姓名';
            echo  json_encode($result);
            exit;
        }
        if(empty($mobile)){
            $result['status'] = 0;
            $result['msg'] = '请填写电话号码';
            echo  json_encode($result);
            exit;
        }
        if(strlen($mobile) !== 11|| !preg_match('/^(13[0-9]|15[0-9]|18[0-9])\d{8}$/',$mobile)){
            $result['status'] = 0;
            $result['msg'] = '请输入正确的手机号码格式';
            echo  json_encode($result);
            exit;
        }

        $wh = [];
        //$wh['car_style_id'] = $style_id;
        $wh['series_id'] = $series_id;
        $wh['book_telephone'] = $mobile;
        $wh['dealers_id'] = (int)$dealers_id;
        $wh['delete_time'] = 0;
        $orderBookModel = new OrderBookModel();
        $count = $orderBookModel->where($wh)->count();
        if($count>0){
            $result['status'] = 0;
            $result['msg'] = '您已经预约';
            echo  json_encode($result);
            exit;
        }
        $data['name'] = $customer_name;
        $data['area_id'] = 4403; // 区级id 深圳
        $data['sex'] = 0; // 性别id
        $data['car_style_id'] = 0; // 车型id
        $data['book_time'] = time(); // 发布时间
        $data['book_to_time'] = 0; // 预约到店时间
        $data['book_telephone'] = $mobile; // 电话
        $data['series_id'] = $series_id; // 车系
        $data['dealers_id'] = $dealers_id; // 来源

        try{
            $id = $orderBookModel->addOrderBookData($data);
            if($id){
                // 给运维人员发邮件通知
                //  $email_config['to'] = $this->book_car_manger_email;
                $email_config['subject'] = '预约试驾';
                $email_tpl = '<table border="0" cellspacing="0" cellpadding="0" width="60%%" align="center" >
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
                $seriesModel = new GoodsCarSeriesModel();
                $brand_data = $seriesModel->getBrandBySeriesId($series_id);
                $book_date = date('Y-m-d H:i:s',$data['book_time']);
                // $email_config['to'] = $this->book_car_manger_email[$brand_data['brand_id']][0];
                if($dealers_id == 99){
                    $source = '多品牌页面';
                }else{
                    $source = $brand_data['brand_name'].'页面';
                }

                $book_car_style = $brand_data['brand_name'].' '.$brand_data['series_name'];
                $body = sprintf($email_tpl,$customer_name,$book_date,$book_car_style,$source,$mobile);
                if(isset($this->book_car_manger_email[$brand_data['brand_id']])){
                    foreach($this->book_car_manger_email[$brand_data['brand_id']] as $email_user){
                        $result = cmf_send_email($email_user, $email_config['subject'], $body);
                    }
                }
              //  $this->result(['order_book_id'=>$id],1);
                /*if ($result && empty($result['error'])) {
                    $this->success('发送成功！');
                } else {
                    $this->error('发送失败：' . $result['message']);
                }*/
                $result['status'] = 1;
                $result['msg'] = '预约成功';
                echo  json_encode($result);
                exit;

            }
        }catch(ErrorException $e){
            $result['status'] = 0;
            $result['msg'] = '服务出错,请稍后再试';
            echo  json_encode($result);
            exit;
        }
    }

    public function demo(){
        return $this->fetch();
    }

    public function test(){
        $seriesModel = new GoodsCarSeriesModel();
        $brand_data = $seriesModel->getBrandBySeriesId(1);
        $result =   cmf_send_email($this->book_car_manger_email[$brand_data['brand_id']][0],'测试邮件', '这是一份测试邮件');

        dump($result);

        return $this->fetch();

    }
    public function p8ConfigData(){
        $wey_style =[
            [
                'name'=>'P8&nbsp;尊贵版',
                'item1'=>[
                    'key'=>'价格',
                    'val'=>[
                        [
                            'k'=>'官方指导价',
                            'v'=>'292,800RMB',
                            's'=>'1'
                        ],
                        [
                            'k'=>'补贴后全国统一价',
                            'v'=>' 259,800RMB',
                            's'=>'1'
                        ]
                    ]
                ],
                'item2'=>[
                    'key'=>'基本参数',
                    'val'=>[
                        [
                            'k'=>'长×宽×高（mm）',
                            'v'=>'4760×1931×1655',
                            's'=>'1'
                        ],
                        [
                            'k'=>'轴距（mm）',
                            'v'=>'2950',
                            's'=>'1'
                        ],
                        [
                            'k'=>'最小离地间隙（mm）',
                            'v'=>'180',
                            's'=>'1'
                        ],
                        [
                            'k'=>'接近角(度)',
                            'v'=>'20',
                            's'=>'1'
                        ],
                        [
                            'k'=>'离去角(度)',
                            'v'=>'25',
                            's'=>'1'
                        ],
                        [
                            'k'=>'油箱容积(L)',
                            'v'=>'45',
                            's'=>'1'
                        ]

                    ]
                ],
                'item3'=>[
                    'key'=>'发动机',
                    'val'=>[
                        [
                            'k'=>'发动机类型',
                            'v'=>'汽油',
                            's'=>'1'
                        ],
                        [
                            'k'=>'发动机型号',
                            'v'=>'4C20A',
                            's'=>'1'
                        ],
                        [
                            'k'=>'发动机峰值功率/转速(kW/rpm)',
                            'v'=>'172/5500',
                            's'=>'1'
                        ],
                        [
                            'k'=>'发动机峰值扭矩/转速(N.m/rpm)',
                            'v'=>'360/2200-4000',
                            's'=>'1'
                        ],
                    ]
                ],
                'item4'=>[
                    'key'=>'电动机及电池',
                    'val'=>[
                        [
                            'k'=>'驱动电机类型',
                            'v'=>'永磁同步电机',
                            's'=>'1'
                        ],
                        [
                            'k'=>'BSG电机最大功率(kW)',
                            'v'=>' 15',
                            's'=>'1'
                        ],
                        [
                            'k'=>'BSG电机最大扭矩(N·m)',
                            'v'=>'50',
                            's'=>'1'
                        ],
                        [
                            'k'=>'驱动电机最大功率(kW)',
                            'v'=>'85',
                            's'=>'1'
                        ],
                        [
                            'k'=>'驱动电机最大扭矩(N·m)',
                            'v'=>' 360/2200-4000',
                            's'=>'1'
                        ],
                        [
                            'k'=>'动力电池类型',
                            'v'=>'三元锂离子电池',
                            's'=>'1'
                        ],
                        [
                            'k'=>'动力电池容量(kWh)',
                            'v'=>'12.96',
                            's'=>'1'
                        ],
                        [
                            'k'=>'0-100%充电时间(h)',
                            'v'=>'4',
                            's'=>'1'
                        ],
                    ]
                ],
                'item5'=>[
                    'key'=>'综合性能',
                    'val'=>[
                        [
                            'k'=>'整车综合最大功率(kW)',
                            'v'=>'250',
                            's'=>'1'
                        ],
                        [
                            'k'=>'整车综合最大扭矩(N·m)',
                            'v'=>'524',
                            's'=>'1'
                        ],
                        [
                            'k'=>'0-100km/h加速时间(s)',
                            'v'=>'6.5',
                            's'=>'1'
                        ],
                        [
                            'k'=>'综合工况油耗(L/100km)',
                            'v'=>'2.3',
                            's'=>'1'
                        ],
                        [
                            'k'=>'最高车速(km/h)',
                            'v'=>'230',
                            's'=>'1'
                        ],
                    ]
                ],
                'item6'=>[
                    'key'=>'底盘操控',
                    'val'=>[
                        [
                            'k'=>'变速器',
                            'v'=>'6速湿式双离合变速器',
                            's'=>'1'
                        ],
                        [
                            'k'=>'悬挂系统',
                            'v'=>'麦弗逊式独立前悬挂/多连杆式独立后悬挂',
                            's'=>'1'
                        ],
                        [
                            'k'=>'驱动型式',
                            'v'=>'智能四驱',
                            's'=>'1'
                        ],
                        [
                            'k'=>'制动型式',
                            'v'=>'四轮通风盘式制动',
                            's'=>'1'
                        ],
                        [
                            'k'=>'转向系统型式',
                            'v'=>'电动助力转向',
                            's'=>'1'
                        ],
                        [
                            'k'=>'SPORT模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'EV模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'AUTO模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'SAVE模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'AWD模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'雪地模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'沙地模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'泥地模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'235/55 R20(米其林)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'255/50 R20(马牌)',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'255/45 R21(佳通)',
                            'v'=>'0',
                            's'=>'2'
                        ],
                    ]
                ],
                'item7'=>[
                    'key'=>'安全装备',
                    'val'=>[
                        [
                            'k'=>'ACC自适应巡航(0-150km/h区间开启)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'FCW前碰撞预警',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'AEB自动刹车系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'LKA车道保持',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'LDW车道偏离预警',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'LCA并线辅助',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'BSD盲点监测系统',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'ESP车身稳定控制系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'TCS牵引力控制系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'ABS制动防抱死系统',
                            'v'=>'2',
                            's'=>'2'
                        ],[
                            'k'=>'BA智能刹车辅助系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'EBD电子制动力分配系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'BOS制动优先系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'ESS紧急制动警示系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'HHC上坡辅助系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'HDC陡坡缓降系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'驾驶员疲劳提醒',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'DOW开门预警',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'CTA倒车侧向警告',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'半自动泊车',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'360度全景监视',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'EPB电子驻车系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'AUTO HOLD自动驻车系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'前/后测距雷达',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'RMI防侧翻',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'TPMS智能胎压监测系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'前排双安全气囊',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'前后一体式侧气帘',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'前排侧气囊',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'纯电动行驶声音提示',
                            'v'=>'2',
                            's'=>'2'
                        ],[
                            'k'=>'前/后排预紧式限力安全带',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'儿童安全锁',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'儿童座椅固定装置(LATCH)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                    ]
                ],
                'item8'=>[
                    'key'=>'外观配置',
                    'val'=>[
                        [
                            'k'=>'智能全景天窗',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'矩阵式LED大灯(带ALS+跟随回家)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'LED前雾灯(带转向自动照明)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'全LED后组合灯',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'四门把手照明灯',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'自动大灯+自动雨刷',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'前排静音玻璃',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'后排隐私玻璃',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'20英寸双色刃型铝合金轮毂',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'21英寸多辐银色铝合金轮毂',
                            'v'=>'2',
                            's'=>'2'
                        ],[
                            'k'=>'21英寸多辐黑色铝合金轮毂',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'哑光银外观套件',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'高亮黑外观套件',
                            'v'=>'2',
                            's'=>'2'
                        ],
                    ]
                ],
                'item9'=>[
                    'key'=>'内饰配置',
                    'val'=>[
                        [
                            'k'=>'黑色内饰',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'黑棕内饰',
                            'v'=>'1',
                            's'=>'2'
                        ],
                        [
                            'k'=>'黑红内饰',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'高亮黑内饰板',
                            'v'=>'1',
                            's'=>'2'
                        ],
                        [
                            'k'=>'科技纹内饰板',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'皮质座椅',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'真皮座椅',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'前排座椅加热',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'前排座椅通风+按摩功能',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'主驾驶座椅8向电动调节(带座椅记忆及迎宾)',
                            'v'=>'0',
                            's'=>'2'
                        ],[
                            'k'=>'主驾驶座椅6向电动调节',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'副驾驶座椅4向电动调节',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'运动真皮多功能方向盘(带换挡拨片)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'PM2.5空调滤芯',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'空气净化系统',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'LED动感氛围灯(三色可调)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'LED顶灯/阅读灯',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'车外温度显示',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'冷光迎宾门槛',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'后排高度可调节头枕',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'豪华脚垫',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'金属装饰踏板及脚歇',
                            'v'=>'0',
                            's'=>'2'
                        ],
                    ]
                ],
                'item10'=>[
                    'key'=>'科技便利配置',
                    'val'=>[
                        [
                            'k'=>'WEY智享互联',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'高清流媒体广角内后视镜',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'12.3英寸全彩高清数字虚拟组合仪表',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'9英寸TFT彩色液晶显示屏(带行车锁屏功能)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'GPS智能导航系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'智能语音识别',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'高级音响系统(9扬声器，带独立功放)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'随速音量调节',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'感应式电动后背门(带位置记忆及防夹)',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'电动后背门(带位置记忆及防夹)',
                            'v'=>'0',
                            's'=>'2'
                        ],[
                            'k'=>'一键启动+无钥匙进入',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'双温区自动空调系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'AQS空气质量自动控制系统',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'四门车窗一键升降(带防夹)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'电动折叠外后视镜(带除霜)',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'外后视镜倒车记忆辅助',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'智能车门防误锁',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'车载充电机',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'充电桩',
                            'v'=>'2',
                            's'=>'2'
                        ],
                    ]
                ],
            ],
            [
                'name'=>'P8&nbsp;尊享型',
                'item1'=>[
                    'key'=>'价格',
                    'val'=>[
                        [
                            'k'=>'官方指导价',
                            'v'=>'302,800RMB',
                            's'=>'1'
                        ],
                        [
                            'k'=>'补贴后全国统一价',
                            'v'=>' 269,800RMB',
                            's'=>'1'
                        ]
                    ]
                ],
                'item2'=>[
                    'key'=>'基本参数',
                    'val'=>[
                        [
                            'k'=>'长×宽×高（mm）',
                            'v'=>'4760×1931×1655',
                            's'=>'1'
                        ],
                        [
                            'k'=>'轴距（mm）',
                            'v'=>'2950',
                            's'=>'1'
                        ],
                        [
                            'k'=>'最小离地间隙（mm）',
                            'v'=>'180',
                            's'=>'1'
                        ],
                        [
                            'k'=>'接近角(度)',
                            'v'=>'20',
                            's'=>'1'
                        ],
                        [
                            'k'=>'离去角(度)',
                            'v'=>'25',
                            's'=>'1'
                        ],
                        [
                            'k'=>'油箱容积(L)',
                            'v'=>'45',
                            's'=>'1'
                        ]

                    ]
                ],
                'item3'=>[
                    'key'=>'发动机',
                    'val'=>[
                        [
                            'k'=>'发动机类型',
                            'v'=>'汽油',
                            's'=>'1'
                        ],
                        [
                            'k'=>'发动机型号',
                            'v'=>'4C20A',
                            's'=>'1'
                        ],
                        [
                            'k'=>'发动机峰值功率/转速(kW/rpm)',
                            'v'=>'172/5500',
                            's'=>'1'
                        ],
                        [
                            'k'=>'发动机峰值扭矩/转速(N.m/rpm)',
                            'v'=>'360/2200-4000',
                            's'=>'1'
                        ],
                    ]
                ],
                'item4'=>[
                    'key'=>'电动机及电池',
                    'val'=>[
                        [
                            'k'=>'驱动电机类型',
                            'v'=>'永磁同步电机',
                            's'=>'1'
                        ],
                        [
                            'k'=>'BSG电机最大功率(kW)',
                            'v'=>' 15',
                            's'=>'1'
                        ],
                        [
                            'k'=>'BSG电机最大扭矩(N·m)',
                            'v'=>'50',
                            's'=>'1'
                        ],
                        [
                            'k'=>'驱动电机最大功率(kW)',
                            'v'=>'85',
                            's'=>'1'
                        ],
                        [
                            'k'=>'驱动电机最大扭矩(N·m)',
                            'v'=>' 360/2200-4000',
                            's'=>'1'
                        ],
                        [
                            'k'=>'动力电池类型',
                            'v'=>'三元锂离子电池',
                            's'=>'1'
                        ],
                        [
                            'k'=>'动力电池容量(kWh)',
                            'v'=>'12.96',
                            's'=>'1'
                        ],
                        [
                            'k'=>'0-100%充电时间(h)',
                            'v'=>'4',
                            's'=>'1'
                        ],
                    ]
                ],
                'item5'=>[
                    'key'=>'综合性能',
                    'val'=>[
                        [
                            'k'=>'整车综合最大功率(kW)',
                            'v'=>'250',
                            's'=>'1'
                        ],
                        [
                            'k'=>'整车综合最大扭矩(N·m)',
                            'v'=>'524',
                            's'=>'1'
                        ],
                        [
                            'k'=>'0-100km/h加速时间(s)',
                            'v'=>'6.5',
                            's'=>'1'
                        ],
                        [
                            'k'=>'综合工况油耗(L/100km)',
                            'v'=>'2.3',
                            's'=>'1'
                        ],
                        [
                            'k'=>'最高车速(km/h)',
                            'v'=>'230',
                            's'=>'1'
                        ],
                    ]
                ],
                'item6'=>[
                    'key'=>'底盘操控',
                    'val'=>[
                        [
                            'k'=>'变速器',
                            'v'=>'6速湿式双离合变速器',
                            's'=>'1'
                        ],
                        [
                            'k'=>'悬挂系统',
                            'v'=>'麦弗逊式独立前悬挂/多连杆式独立后悬挂',
                            's'=>'1'
                        ],
                        [
                            'k'=>'驱动型式',
                            'v'=>'智能四驱',
                            's'=>'1'
                        ],
                        [
                            'k'=>'制动型式',
                            'v'=>'四轮通风盘式制动',
                            's'=>'1'
                        ],
                        [
                            'k'=>'转向系统型式',
                            'v'=>'电动助力转向',
                            's'=>'1'
                        ],
                        [
                            'k'=>'SPORT模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'EV模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'AUTO模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'SAVE模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'AWD模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'雪地模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'沙地模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'泥地模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'235/55 R20(米其林)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'255/50 R20(马牌)',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'255/45 R21(佳通)',
                            'v'=>'0',
                            's'=>'2'
                        ],
                    ]
                ],
                'item7'=>[
                    'key'=>'安全装备',
                    'val'=>[
                        [
                            'k'=>'ACC自适应巡航(0-150km/h区间开启)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'FCW前碰撞预警',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'AEB自动刹车系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'LKA车道保持',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'LDW车道偏离预警',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'LCA并线辅助',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'BSD盲点监测系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'ESP车身稳定控制系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'TCS牵引力控制系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'ABS制动防抱死系统',
                            'v'=>'2',
                            's'=>'2'
                        ],[
                            'k'=>'BA智能刹车辅助系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'EBD电子制动力分配系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'BOS制动优先系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'ESS紧急制动警示系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'HHC上坡辅助系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'HDC陡坡缓降系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'驾驶员疲劳提醒',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'DOW开门预警',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'CTA倒车侧向警告',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'半自动泊车',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'360度全景监视',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'EPB电子驻车系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'AUTO HOLD自动驻车系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'前/后测距雷达',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'RMI防侧翻',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'TPMS智能胎压监测系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'前排双安全气囊',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'前后一体式侧气帘',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'前排侧气囊',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'纯电动行驶声音提示',
                            'v'=>'2',
                            's'=>'2'
                        ],[
                            'k'=>'前/后排预紧式限力安全带',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'儿童安全锁',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'儿童座椅固定装置(LATCH)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                    ]
                ],
                'item8'=>[
                    'key'=>'外观配置',
                    'val'=>[
                        [
                            'k'=>'智能全景天窗',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'矩阵式LED大灯(带ALS+跟随回家)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'LED前雾灯(带转向自动照明)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'全LED后组合灯',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'四门把手照明灯',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'自动大灯+自动雨刷',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'前排静音玻璃',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'后排隐私玻璃',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'20英寸双色刃型铝合金轮毂',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'21英寸多辐银色铝合金轮毂',
                            'v'=>'0',
                            's'=>'2'
                        ],[
                            'k'=>'21英寸多辐黑色铝合金轮毂',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'哑光银外观套件',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'高亮黑外观套件',
                            'v'=>'1',
                            's'=>'2'
                        ],
                    ]
                ],
                'item9'=>[
                    'key'=>'内饰配置',
                    'val'=>[
                        [
                            'k'=>'黑色内饰',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'黑棕内饰',
                            'v'=>'1',
                            's'=>'2'
                        ],
                        [
                            'k'=>'黑红内饰',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'高亮黑内饰板',
                            'v'=>'1',
                            's'=>'2'
                        ],
                        [
                            'k'=>'科技纹内饰板',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'皮质座椅',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'真皮座椅',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'前排座椅加热',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'前排座椅通风+按摩功能',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'主驾驶座椅8向电动调节(带座椅记忆及迎宾)',
                            'v'=>'0',
                            's'=>'2'
                        ],[
                            'k'=>'主驾驶座椅6向电动调节',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'副驾驶座椅4向电动调节',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'运动真皮多功能方向盘(带换挡拨片)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'PM2.5空调滤芯',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'空气净化系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'LED动感氛围灯(三色可调)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'LED顶灯/阅读灯',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'车外温度显示',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'冷光迎宾门槛',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'后排高度可调节头枕',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'豪华脚垫',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'金属装饰踏板及脚歇',
                            'v'=>'2',
                            's'=>'2'
                        ],
                    ]
                ],
                'item10'=>[
                    'key'=>'科技便利配置',
                    'val'=>[
                        [
                            'k'=>'WEY智享互联',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'高清流媒体广角内后视镜',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'12.3英寸全彩高清数字虚拟组合仪表',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'9英寸TFT彩色液晶显示屏(带行车锁屏功能)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'GPS智能导航系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'智能语音识别',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'高级音响系统(9扬声器，带独立功放)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'随速音量调节',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'感应式电动后背门(带位置记忆及防夹)',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'电动后背门(带位置记忆及防夹)',
                            'v'=>'2',
                            's'=>'2'
                        ],[
                            'k'=>'一键启动+无钥匙进入',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'双温区自动空调系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'AQS空气质量自动控制系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'四门车窗一键升降(带防夹)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'电动折叠外后视镜(带除霜)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'外后视镜倒车记忆辅助',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'智能车门防误锁',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'车载充电机',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'充电桩',
                            'v'=>'2',
                            's'=>'2'
                        ],
                    ]
                ],
            ],
            [
                'name'=>'P8&nbsp;旗舰型',
                'item1'=>[
                    'key'=>'价格',
                    'val'=>[
                        [
                            'k'=>'官方指导价',
                            'v'=>'312,800RMB',
                            's'=>'1'
                        ],
                        [
                            'k'=>'补贴后全国统一价',
                            'v'=>' 279,800RMB',
                            's'=>'1'
                        ]
                    ]
                ],
                'item2'=>[
                    'key'=>'基本参数',
                    'val'=>[
                        [
                            'k'=>'长×宽×高（mm）',
                            'v'=>'4760×1931×1655',
                            's'=>'1'
                        ],
                        [
                            'k'=>'轴距（mm）',
                            'v'=>'2950',
                            's'=>'1'
                        ],
                        [
                            'k'=>'最小离地间隙（mm）',
                            'v'=>'180',
                            's'=>'1'
                        ],
                        [
                            'k'=>'接近角(度)',
                            'v'=>'20',
                            's'=>'1'
                        ],
                        [
                            'k'=>'离去角(度)',
                            'v'=>'25',
                            's'=>'1'
                        ],
                        [
                            'k'=>'油箱容积(L)',
                            'v'=>'45',
                            's'=>'1'
                        ]

                    ]
                ],
                'item3'=>[
                    'key'=>'发动机',
                    'val'=>[
                        [
                            'k'=>'发动机类型',
                            'v'=>'汽油',
                            's'=>'1'
                        ],
                        [
                            'k'=>'发动机型号',
                            'v'=>'4C20A',
                            's'=>'1'
                        ],
                        [
                            'k'=>'发动机峰值功率/转速(kW/rpm)',
                            'v'=>'172/5500',
                            's'=>'1'
                        ],
                        [
                            'k'=>'发动机峰值扭矩/转速(N.m/rpm)',
                            'v'=>'360/2200-4000',
                            's'=>'1'
                        ],
                    ]
                ],
                'item4'=>[
                    'key'=>'电动机及电池',
                    'val'=>[
                        [
                            'k'=>'驱动电机类型',
                            'v'=>'永磁同步电机',
                            's'=>'1'
                        ],
                        [
                            'k'=>'BSG电机最大功率(kW)',
                            'v'=>' 15',
                            's'=>'1'
                        ],
                        [
                            'k'=>'BSG电机最大扭矩(N·m)',
                            'v'=>'50',
                            's'=>'1'
                        ],
                        [
                            'k'=>'驱动电机最大功率(kW)',
                            'v'=>'85',
                            's'=>'1'
                        ],
                        [
                            'k'=>'驱动电机最大扭矩(N·m)',
                            'v'=>' 360/2200-4000',
                            's'=>'1'
                        ],
                        [
                            'k'=>'动力电池类型',
                            'v'=>'三元锂离子电池',
                            's'=>'1'
                        ],
                        [
                            'k'=>'动力电池容量(kWh)',
                            'v'=>'12.96',
                            's'=>'1'
                        ],
                        [
                            'k'=>'0-100%充电时间(h)',
                            'v'=>'4',
                            's'=>'1'
                        ],
                    ]
                ],
                'item5'=>[
                    'key'=>'综合性能',
                    'val'=>[
                        [
                            'k'=>'整车综合最大功率(kW)',
                            'v'=>'250',
                            's'=>'1'
                        ],
                        [
                            'k'=>'整车综合最大扭矩(N·m)',
                            'v'=>'524',
                            's'=>'1'
                        ],
                        [
                            'k'=>'0-100km/h加速时间(s)',
                            'v'=>'6.5',
                            's'=>'1'
                        ],
                        [
                            'k'=>'综合工况油耗(L/100km)',
                            'v'=>'2.3',
                            's'=>'1'
                        ],
                        [
                            'k'=>'最高车速(km/h)',
                            'v'=>'230',
                            's'=>'1'
                        ],
                    ]
                ],
                'item6'=>[
                    'key'=>'底盘操控',
                    'val'=>[
                        [
                            'k'=>'变速器',
                            'v'=>'6速湿式双离合变速器',
                            's'=>'1'
                        ],
                        [
                            'k'=>'悬挂系统',
                            'v'=>'麦弗逊式独立前悬挂/多连杆式独立后悬挂',
                            's'=>'1'
                        ],
                        [
                            'k'=>'驱动型式',
                            'v'=>'智能四驱',
                            's'=>'1'
                        ],
                        [
                            'k'=>'制动型式',
                            'v'=>'四轮通风盘式制动',
                            's'=>'1'
                        ],
                        [
                            'k'=>'转向系统型式',
                            'v'=>'电动助力转向',
                            's'=>'1'
                        ],
                        [
                            'k'=>'SPORT模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'EV模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'AUTO模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'SAVE模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'AWD模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'雪地模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'沙地模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'泥地模式',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'235/55 R20(米其林)',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'255/50 R20(马牌)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'255/45 R21(佳通)',
                            'v'=>'1',
                            's'=>'2'
                        ],
                    ]
                ],
                'item7'=>[
                    'key'=>'安全装备',
                    'val'=>[
                        [
                            'k'=>'ACC自适应巡航(0-150km/h区间开启)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'FCW前碰撞预警',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'AEB自动刹车系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'LKA车道保持',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'LDW车道偏离预警',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'LCA并线辅助',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'BSD盲点监测系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'ESP车身稳定控制系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'TCS牵引力控制系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'ABS制动防抱死系统',
                            'v'=>'2',
                            's'=>'2'
                        ],[
                            'k'=>'BA智能刹车辅助系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'EBD电子制动力分配系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'BOS制动优先系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'ESS紧急制动警示系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'HHC上坡辅助系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'HDC陡坡缓降系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'驾驶员疲劳提醒',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'DOW开门预警',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'CTA倒车侧向警告',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'半自动泊车',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'360度全景监视',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'EPB电子驻车系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'AUTO HOLD自动驻车系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'前/后测距雷达',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'RMI防侧翻',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'TPMS智能胎压监测系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'前排双安全气囊',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'前后一体式侧气帘',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'前排侧气囊',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'纯电动行驶声音提示',
                            'v'=>'2',
                            's'=>'2'
                        ],[
                            'k'=>'前/后排预紧式限力安全带',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'儿童安全锁',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'儿童座椅固定装置(LATCH)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                    ]
                ],
                'item8'=>[
                    'key'=>'外观配置',
                    'val'=>[
                        [
                            'k'=>'智能全景天窗',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'矩阵式LED大灯(带ALS+跟随回家)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'LED前雾灯(带转向自动照明)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'全LED后组合灯',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'四门把手照明灯',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'自动大灯+自动雨刷',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'前排静音玻璃',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'后排隐私玻璃',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'20英寸双色刃型铝合金轮毂',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'21英寸多辐银色铝合金轮毂',
                            'v'=>'1',
                            's'=>'2'
                        ],[
                            'k'=>'21英寸多辐黑色铝合金轮毂',
                            'v'=>'1',
                            's'=>'2'
                        ],
                        [
                            'k'=>'哑光银外观套件',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'高亮黑外观套件',
                            'v'=>'1',
                            's'=>'2'
                        ],
                    ]
                ],
                'item9'=>[
                    'key'=>'内饰配置',
                    'val'=>[
                        [
                            'k'=>'黑色内饰',
                            'v'=>'1',
                            's'=>'2'
                        ],
                        [
                            'k'=>'黑棕内饰',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'黑红内饰',
                            'v'=>'1',
                            's'=>'2'
                        ],
                        [
                            'k'=>'高亮黑内饰板',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'科技纹内饰板',
                            'v'=>'1',
                            's'=>'2'
                        ],
                        [
                            'k'=>'皮质座椅',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'真皮座椅',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'前排座椅加热',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'前排座椅通风+按摩功能',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'主驾驶座椅8向电动调节(带座椅记忆及迎宾)',
                            'v'=>'2',
                            's'=>'2'
                        ],[
                            'k'=>'主驾驶座椅6向电动调节',
                            'v'=>'0',
                            's'=>'2'
                        ],
                        [
                            'k'=>'副驾驶座椅4向电动调节',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'运动真皮多功能方向盘(带换挡拨片)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'PM2.5空调滤芯',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'空气净化系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'LED动感氛围灯(三色可调)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'LED顶灯/阅读灯',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'车外温度显示',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'冷光迎宾门槛',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'后排高度可调节头枕',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'豪华脚垫',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'金属装饰踏板及脚歇',
                            'v'=>'0',
                            's'=>'2'
                        ],
                    ]
                ],
                'item10'=>[
                    'key'=>'科技便利配置',
                    'val'=>[
                        [
                            'k'=>'WEY智享互联',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'高清流媒体广角内后视镜',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'12.3英寸全彩高清数字虚拟组合仪表',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'9英寸TFT彩色液晶显示屏(带行车锁屏功能)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'GPS智能导航系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'智能语音识别',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'高级音响系统(9扬声器，带独立功放)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'随速音量调节',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'感应式电动后背门(带位置记忆及防夹)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'电动后背门(带位置记忆及防夹)',
                            'v'=>'0',
                            's'=>'2'
                        ],[
                            'k'=>'一键启动+无钥匙进入',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'双温区自动空调系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'AQS空气质量自动控制系统',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'四门车窗一键升降(带防夹)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'电动折叠外后视镜(带除霜)',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'外后视镜倒车记忆辅助',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'智能车门防误锁',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'车载充电机',
                            'v'=>'2',
                            's'=>'2'
                        ],
                        [
                            'k'=>'充电桩',
                            'v'=>'2',
                            's'=>'2'
                        ],
                    ]
                ],
            ],
        ];
        return $wey_style;
    }



    public function ajax_get_series(){
        $brand_id = I('brand_id');
        $brand =  [0=>'all',3=>'wey',5=>'chuanqi',6=>'guangfeng',7=>'richan',16=>'guangben'];
        $data = $this->_getCommonTplData($brand[$brand_id]);

        $this->result($data['series'],1);
    }


    /**
     *  获取多品牌数据
     * @param $brand_name
     * @return array|mixed
     */
    private function _getCommonTplData($brand_name){
        $data =  file_get_contents(APP_PATH.'../data/conf/brand_data.json');
        $page_datas  = \Qiniu\json_decode($data,true);
        if($brand_name ==='all'){
            return $page_datas;
        }
        $brand_name = strtoupper($brand_name);
        return isset($page_datas[$brand_name])?$page_datas[$brand_name]:[];
    }

    /**
     *  获取多品牌数据 -two 20181204
     * @param $brand_name
     * @return array|mixed
     */
    private function _getCommonTplDataTwo($brand_name){
        $data =  file_get_contents(APP_PATH.'../data/conf/brand_data_two.json');
        $page_datas  = \Qiniu\json_decode($data,true);
        if($brand_name ==='all'){
            return $page_datas;
        }

        $brand_name = strtoupper($brand_name);
        return isset($page_datas[$brand_name])?$page_datas[$brand_name]:[];
    }


    /**
     * 获取落地页配置数据
     * @param $brand_name
     * @return array
     */
    private function _bookPageData($brand_name){
       $data =  file_get_contents(APP_PATH.'../data/conf/book.json');
       $page_datas  = \Qiniu\json_decode($data,true);
       $brand_name = strtoupper($brand_name);
       return isset($page_datas[$brand_name])?$page_datas[$brand_name]:[];
    }

    /**
     * 解析对应奖项
     * @param $lottery_opt_id
     * @return int
     */
    private function _parseLotteryOption( $lottery_opt_id ){

        return (int)$lottery_opt_id;
    }

}
