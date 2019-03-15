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

use app\admin\model\ActivityModel;
use app\admin\model\ActivityUserModel;
use app\portal\service\ActivityService;
use cmf\controller\HomeBaseController;
use jssdk\Jssdk;
use think\Db;

class ActivityController extends HomeBaseController
{




    public function index()
    {
        $debug = I('debug');
       if (!is_weixin() || !isMobile() ) {
                  // 非微信浏览器禁止浏览
            echo "HTTP/1.1 401 Unauthorized;请用手机微信内置浏览器打开!";
            return ;
       }
        $this->assign('isLogin',0);
        $act_user_mod = new ActivityUserModel();
        $user = session('act_user');
        // 获取最新的4条兑奖码
        $latest_user = $act_user_mod->getGbUser(4);
        $this->assign('latest_user',$latest_user);
        // 获取中奖名单
        $wining_list = $act_user_mod->getWiningList();
        $wining_code = [];
        $user_code = [];
        $current_wining_code ='';
        if($wining_list){
            foreach($wining_list as &$list){
                $wining_code[] = $list['code'];
                $list['user_phone'] = $act_user_mod->getActUserPhone($list['user_id']);
            }
        }
        if(!empty($user)){
            // 登陆获取 当前用户的信息
            $this->assign('isLogin',1);
            $this->assign('user',$user);
            $user_token = cmf_generate_user_token($user["id"], 'mobile');
            $this->assign('user_token',$user_token);
            // 获取当前用户的兑奖码
            $codeList = $act_user_mod->getUserCodeData($user['id']);
            $total = $act_user_mod->getUserCodeCount($user['id']);
            $this->assign('codeList',$codeList);
            $this->assign('codeCount',$total);
            foreach($codeList as $item){
                $user_code[] = $item['code'];
            }
            $current_wining_code_arr = array_intersect($user_code,$wining_code);
            $current_wining_code = $current_wining_code_arr?$current_wining_code_arr[0]:0;
        }
        $this->assign('current_wining_code',$current_wining_code);
        $this->assign('wining_list',[]);
	    $this->assign('version',time());
        return $this->fetch('index');
    }

    /***
     *
     * 活动用户签到
     *
     */
    public function userSignIn(){
        if($this->request->isPost()){
            $tel = trim(I('mobile'));
            $add['ip'] = get_client_ip();
            $add['phone'] = $tel;
            $add['signin_time'] = time();

            if(empty($act_id)){
                $this->result([],0,'签到失败，参数异常，联系工程师查看！');
            }
            $activity = ActivityModel::get($act_id);

            if(empty($activity)){
                $this->result([],0,'签到失败，参数异常，联系工程师查看！');
            }

            $name = trim(I('name'));
            $add['name'] = $name?$name:$activity['title'].'用户';
            $act_id = (int)I('act_id');

            if( $activity['end_time'] < time() ){
                $this->result([],0,'签到失败，活动已经结束！');
            }

            if(empty($add['name'])){
                $this->result([],0,'签到失败，姓名不允许为空！');
            }
            if(empty($tel)){
                $this->result([],0,'签到失败，手机号不允许为空！');
            }
            if(!isTelNumber($tel)){
                $this->result([],0,'签到失败，手机号不正确！');
            }
            if( $activity['need_sign_up'] == 1){
                // 需要报名 验证手机号码是否报名，报名才可以进行签到
                $userModel = new  ActivityUserModel();
                $data = $userModel->where(['phone'=>$tel,'act_id'=>$act_id])->find();
                if(empty($data)){
                    $this->result([],0,'签到失败，你号码没要预报名，请联系工作人员进行预报名！');
                }
            }
            $info = Db::name('activity_user_signin')->where(['phone'=>$tel])->find();
            if($info){
                //已签到
                session('signin_user',$info);
                cookie('signed_in',1,86400);
                $this->result([],-1,'已签到');
            }
            $add['flag'] = 0;//是否发放门票标志0:签到成功未发放门票 1:已发放门票
            $rs = Db::name('activity_user_signin')->insert($add);
            if($rs){
                session('signin_user',$add);
                cookie('signed_in',1,86400);

                if($activity['need_sign_up'] == 0){
                    // 不需要预报名的活动 签到用户就是活动用户 保存签到用户
                    $wh['act_id'] = $act_id;
                    $wh['phone'] = $tel;
                    $wh['delete_time'] = 0;
                    $data = Db::name('activity_user')->where($wh)->find();
                    $save['username'] = $add['name'];
                    $save['phone'] = $tel;
                    $save['ip'] = get_client_ip();
                    $save['signup_time'] = time();
                    $save['mac'] = md5(get_client_ip());
                    $save['act_id'] = $act_id;
                    if($data){
                        // 更新
                       Db::name('activity_user')->where(['id'=>$data['id']])->update($save);
                    }else{
                        // 添加
                         Db::name('activity_user')->insert($save);
                    }
                }


                $this->result([],1,'签到成功');

            }else{
                $this->result([],0,'签到失败');
            }
        }

        if(cookie('signed_in') == 1 && session('signin_user')){
            $this->assign('signed_in',1);
            $signin_user = session('signin_user');
        }else{
            $this->assign('signed_in',0);
            $signin_user = [];
        }
        $this->assign('signin_user',$signin_user);
        //$count = Db::name('activity_user_signin')->count();
       // $this->assign('singInCount',$count);
      return $this->fetch('user_sign_in_2');
    }
    //抽奖
    public function draw(){
        if(!$this->request->isAjax()){
            $this->result('',0,'非法请求');
        }
        $phone = trim(I('phone'));
        $org_sign = intval(I('org_sign'));
        $act_code = trim(I('act_code'));
        if(empty($phone)){
            $this->result('',0,'请输入电话号码');
        }
        if ( !preg_match('/^1([3-9])\d{9}$/',$phone)){
            $this->result('',0,'请正确的电话号码');
        }
        if(empty($act_code)){
            $this->result('',0,'兑奖码失效');
        }
        //$userIp = get_client_ip(0,1);
        //$user =  Db::name('activity_user')->where(['phone'=>$phone,'delete_time'=>0,'act_id'=>4])->whereOr(['ip'=>$userIp,'delete_time'=>0,'act_id'=>4])->find();
        $user =  Db::name('activity_gqbt')->where(['booking_phone'=>$phone,'act_id'=>4])->find();

        if(!empty($user)){
            $this->result('',0,'该号码已经报名，不需要重复报名！');
        }else{
            $data['act_id'] = 4;
            $data['phone'] = $phone;
            $data['ip'] = get_client_ip(0,1);
            $data['act_code'] = $act_code;
            $actModel = new ActivityUserModel();
            if( $org_sign ){
                $is_share = true;
                $rs = $actModel->createCode($data,$is_share, $org_sign);
            }else{
                $rs = $actModel->createCode($data,false,'');
            }
            if(!empty($rs)){
                $this->result($rs,1,'报名成功');
            }else{
                $this->result('',0,'报名失败');
            }
        }
    }

    /**
     *
     * 活动用户登陆
     */
    public function userLogin(){
        if(!$this->request->isAjax()){
            $this->result('',0,'非法请求');
        }
        $phone = trim(I('phone'));
        if(empty($phone)){
            $this->result('',0,'请输入电话号码');
        }
        if(!isTelNumber($phone)){
            $this->result('',0,'请正确的电话号码');
        }

        $user =  Db::name('activity_user')->where(['act_id'=>4,'phone'=>$phone,'delete_time'=>0])->find();
        if(!empty($user)){
            session('act_user',$user);
            $token = cmf_generate_user_token($user["id"], 'mobile');
            if (!empty($token)) {
                session('token', $token);
                cookie('isLogin',1);
            }
            $this->result('',1,'登陆成功');
        }else{
            $this->result('',0,'该号码不存在');
        }
    }

    /**
     *
     * 活动用户登陆
     */
    public function userLogout(){
        session('act_user',null);
        session('token', null);
        cookie('isLogin',null);
        $this->result('',1,'退出成功');
        if(!$this->request->isAjax()){
            $this->result('',0,'非法请求');
        }
        $user_id = intval(I('user_id'));
        $user =  Db::name('activity_user')->where(['id'=>$user_id,'delete_time'=>0])->find();
        if(!empty($user)){
            session('act_user',null);
            session('token', null);
            cookie('isLogin',null);
            $this->result('',1,'退出成功');
        }else{
            $this->result('',0,'该号码不存在');
        }
    }

    public function demo(){

        return $this->fetch('my_activity');
    }
    // jssdk
    public function jssdkAuth(){
        $callback =  I('callback');
        $url = I('url');
        //$url = str_replace('&amp;','&',$url);
        $url = urldecode($url);
        //var_dump($url);die;
        $appid = 'wxa46f2369b21ce7a5';
        $appsecret = '50576bb589947ba559b8d97ca32e7ad7';
        $jssdk = new Jssdk($appid,$appsecret,$url);
        $signData = $jssdk->getSignPackage();
        if(!empty($callback)){
            echo $callback."(".json_encode($signData).")";
        }else{
            echo json_encode($signData);
        }
    }

    /***
     *
     * 游戏活动报名
     *
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function activityBooking(){
        if($this->request->isPost()){
            $aid = $this->request->param('aid',0,'intval');
            $openid = $this->request->post('openid');
            $name = $this->request->post('name');
            $phone = $this->request->post('phone');

            if(empty($aid)){
                $this->result([],0,'活动id不存在');
            }
            $activity =  ActivityModel::get($aid);
            if(empty($activity)||$activity['is_del'] == 1){
                $this->result([],0,'活动不存在或已经结束');
            }
            if($activity['signup_stime']>time()){
                $this->result([],0,'活动报名未开始,报名时间是'.date('yy-m-d H:i:s'));
            }
            if($activity['signup_end']<time()){
                $this->result([],0,'活动报名未已结束,无法继续报名,结束时间是'.date('yy-m-d H:i:s'));
            }
            if (empty($phone)) {
                $this->result([],0,'电话号码为空');
            }
            // 验证手机号码正确性
            if (!preg_match('/^1([3-5]|8)\d{9}$/', $phone)) {
                $this->result([],0,'电话不正确');
            }
            $user = DB::name('weixin_user')->where(['openid'=>$openid])->find();
            if(empty($user)){
                // 不存在
                $this->result([],0,'用户不存在');
            }
            //更新用户号码信息
            $user_update_data['phone'] = $phone;
            $user_update_data['mtime'] = time();
            DB::name('weixin_user')->where(['openid'=>$openid])->update($user_update_data);

            $activityUser = new  ActivityUserModel();
            $booking_user = $activityUser->where('phone','=',$phone)
                        ->where('act_id','=',$aid)
                        ->find();
            if($booking_user){
                // 已经报名 请不要重复报名
                $this->result([],-1,'用户已经报名不需要重复报名');
            }

            $addUser['phone'] = $phone;
            $addUser['name'] = $name;
            //$activitySer  = new ActivityService();
            //$res = $activitySer->createCodeByBooking($addUser);
            $res = [];
            if($res){
                $this->result($res,1,'success');
            }
            $this->result([],0,'fail');
        }

         $this->result([],0,'非法请求');
    }

}
