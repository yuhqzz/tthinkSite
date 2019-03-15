<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2018-10-23
 * Time: 10:45
 */

namespace app\portal\controller;

use app\admin\model\ActivitySigninUserModel;
use cmf\controller\HomeBaseController;
use app\admin\model\ActivityModel;
use app\admin\model\ActivityUserModel;
use think\Db;
use think\model\Collection;


class SigninController extends HomeBaseController
{
    protected $act_id = 0;

    public function _initialize()
    {
        parent::_initialize();
        $this->act_id = $this->request->request('act_id','0','intval');
        $this->assign('act_id',$this->act_id);
        $this->assign('version','20181024');
    }


    public function singUp($act_id){
       // $act_id = (int)$act_id;
        //$act_id = 6;
        $act_id = 7;
        if(cookie('signed_in') == 1 && session('signin_user_'.$act_id)){
            $this->assign('signed_in',1);
            $signin_user = session('signin_user_'.$act_id);
            $total = db::name('activity_user_signin')->where('act_id','=',$act_id)->count();
            $signin_user['total'] = intval($total);
        }else{
            $this->assign('signed_in',0);
            $signin_user = [];
        }
        $this->assign('signin_user',$signin_user);
        $this->assign('act_id',$act_id);

        return  $this->fetch('sing_up');

    }

    /**
     * 领袖之夜-埃尔法
     */
    public function aiErFa(){
        $act_id = $this->act_id?$this->act_id:1;
        if(cookie('signed_in') == 1 && session('signin_user_'.$act_id)){
            $this->assign('signed_in',1);
            $signin_user = session('signin_user_'.$act_id);
        }else{
            $this->assign('signed_in',0);
            $signin_user = [];
        }
        $this->assign('signin_user',$signin_user);
        $this->assign('act_id',$act_id);

        return  $this->fetch('ai_er_fa');
    }
    /**
     * 领袖之夜-埃尔法-工作人员后台
     */
    public function aiErFaAdmin(){
        $act_id = 1;
        if(cookie('admin_signed_in') == 1 && session('admin_signin_user_'.$act_id)){
            $this->assign('admin_signed_in',1);
            $signin_user = session('admin_signin_user_'.$act_id);
        }else{
            $this->assign('admin_signed_in',0);
            $signin_user = [];
        }
        $this->assign('admin_signin_user_',$signin_user);
        $this->assign('act_id',$act_id);
        return  $this->fetch('ai_er_fa_admin');
    }

    /**
     *
     * 领袖之夜-埃尔法后台登陆
     *
     */
    public function aiErFaAdminDoLogin(){
        if($this->request->isPost()){
            // 登陆验证 密码
            $pass = trim(I('pass'));
            //密码 szdxqc  深圳大兴汽车

           if(empty($pass)){
               $this->result([],0,'密码为空');
           }

            if( md5($pass) !== md5('szdxqc')){
                $this->result([],0,'密码为错误');
            }
            //登陆成功
            $act_id = 1;
            $session_key = 'admin_signin_user_'.$act_id;
            $cookie_key = 'admin_signed_in';
            $user['username'] = 'activityAdmin';
            $user['phone'] = '13800000000';
            session($session_key,$user);
            cookie($cookie_key,1,86400);
            $this->result([],1);
        }
        $this->result([],0,'非法操作');

    }

    /**
     *
     * 登出操作
     */
    public function aiErFaAdminDoLogout(){
        if($this->request->isPost()){
            //登出成功
            $act_id = $this->request->get('act_id');
            $session_key = 'admin_signin_user_'.$act_id;
            $cookie_key = 'admin_signed_in';
            session($session_key,null);
            cookie($cookie_key,0);
            $this->result([],1);
        }
        $this->result([],0,'非法操作');
    }

    /**
     *
     * 报名
     *
     */
    public function aiErFaDoSignUp(){
        if($this->request->isPost()){
            $act_id = (int)I('act_id');
            $username = I('username');
            $phone = I('phone');
            if(empty( $act_id )){
                $this->result([],0,'操作错误！');
            }
            if(empty($username)){
                $this->result([],0,'姓名为空！');
            }
            if(empty($phone)){
                $this->result([],0,'电话号码为空！');
            }
            if(!isTelNumber($phone)){
                $this->result([],0,'输入正确的电话号码！');
            }

            $wh['act_id'] = $act_id;
            $wh['phone'] = $phone;
            $wh['delete_time'] = 0;
            $data = Db::name('activity_user')->where($wh)->find();
            if($data){
                $this->result([],-1,'该号码已经报名,请勿重复报名！');
            }
            $add['username'] = $username;
            $add['phone'] = $phone;
            $add['ip'] = get_client_ip();
            $add['signup_time'] = time();
            $add['mac'] = md5(get_client_ip());
            $add['act_id'] = $act_id;
            $add['signup_name'] = '活动现场报名';
            // 添加
            $res = Db::name('activity_user')->insert($add);
            if(is_numeric($res)){
                $this->result([],1,'报名成功');
            }else{
                $this->result([],0,'报名失败,请稍后在试一次！');
            }

        }
        $this->result([],0,'非法操作');
    }


    /**
     *
     * 活动用户签到用户
     *
     */
    public function getAiErFaSignInUser(){
        if($this->request->isAjax()){
            $user_model = new ActivitySigninUserModel();
            /*$search = $this->request->get("search", '');
            $filter = $this->request->get("filter", '');
            $op = $this->request->get("op", '', 'trim');*/
            $act_id = $this->request->get("act_id");
            $sort = $this->request->get("sort", "id");
            $order = $this->request->get("order", "DESC");
            $offset = $this->request->get("offset", 0);
            $limit = $this->request->get("limit", 0);
            /*$filter = (array)json_decode($filter, TRUE);
            $op = (array)json_decode($op, TRUE);
            $filter = $filter ? $filter : [];*/
            $search = $this->request->get("search", '');

            $where = [];
            if($search){
                $where['phone'] = $search;
            }
            $where['act_id'] = I('act_id');
            $total = $user_model
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $user_model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            //echo $user_model->getLastSql();die;
            $data = Collection::make($list)->toArray();
            $list = collection($data)->toArray();
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        $result = array("total" => 0, "rows" => []);
        return json($result);
    }

    /**
     *
     * 获取活动报名用户
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAiErFaSignUpUser(){
        if($this->request->isAjax()){
            $user_model = new ActivityUserModel();
            /*$search = $this->request->get("search", '');
            $filter = $this->request->get("filter", '');
            $op = $this->request->get("op", '', 'trim');*/
            $act_id = $this->request->param("act_id");
            $sort = $this->request->get("sort", "id");
            $order = $this->request->get("order", "DESC");
            $offset = $this->request->get("offset", 0);
            $limit = $this->request->get("limit", 0);
            /*$filter = (array)json_decode($filter, TRUE);
            $op = (array)json_decode($op, TRUE);
            $filter = $filter ? $filter : [];*/
            $search = $this->request->get("search", '');

            $where = [];
            if($search){
                $where['phone'] = $search;
            }
            $where['act_id'] = $act_id;
            $where['delete_time'] = 0;
            $total = $user_model
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $user_model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $data = Collection::make($list)->toArray();
            $list = collection($data)->toArray();
            if($list){
                //添加是否签到数据
                $signinUserModel = new ActivitySigninUserModel();
                foreach ($list as &$val){
                    $val['signed_in'] = $signinUserModel->isSigned($val['phone'],$val['act_id'])?1:0;
                }
            }
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        $result = array("total" => 0, "rows" => []);
        return json($result);
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
            $act_id = (int)I('act_id');

            if(empty($act_id)){
                $this->result([],0,'签到失败，参数异常，联系工程师查看！');
            }

            $activity = ActivityModel::get($act_id);
            if(empty($activity)){
                $this->result([],0,'签到失败，参数异常，联系工程师查看！');
            }

            $name = trim(I('name'));

            $add['username'] = $name?$name:$activity['title'].'用户';


            if( $activity['end_time'] < time() ){
                $this->result([],0,'签到失败，活动已经结束！');
            }

            if(empty($add['username'])){
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
                $signup_user_data = $userModel->where(['phone'=>$tel,'act_id'=>$act_id])->find();
                if( empty($signup_user_data) ){
                    $this->result([],0,'签到失败，你号码没有预报名，请联系工作人员进行预报名！');
                }
            }
            // 查询是否已签到
            $info = Db::name('activity_user_signin')->where(['phone'=>$tel,'act_id'=>$act_id])->find();

            $session_key = 'signin_user_'.$act_id;
            $cookie_key = 'signed_in';

            if($info){
                //增加备注
                $info['remark'] = isset($signup_user_data['remark'])?$signup_user_data['remark']:'';
                if($info['flag'] == 1){
                    $this->result([],-1,'已签到');
                }else{
                    //已签到 未领手环
                    session($session_key,$info);
                    cookie($cookie_key,1,86400);
                    $this->result([],1,'签到成功');
                }
            }
            if($act_id == 6){
                // 不需要发放门票标识
                $add['flag'] = 1;//是否发放门票标志0:签到成功未发放门票 1:已发放门票
            }else{
                // 未签到 进行签到
                $add['flag'] = 0;//是否发放门票标志0:签到成功未发放门票 1:已发放门票
            }
            $add['act_id'] = $act_id;
            if( $activity['need_sign_up'] == 1){
               // 预报名活动 替换预报名姓名
                $add['username'] = $signup_user_data['username'];
            }

            $rs = Db::name('activity_user_signin')->insert($add);
            if($rs){
                //增加备注
                $add['remark'] = isset($signup_user_data['remark'])?$signup_user_data['remark']:'';

                session($session_key,$add);

                cookie($cookie_key,1,86400);

                if($activity['need_sign_up'] == 0){

                    // 不需要预报名的活动 签到用户就是活动用户 保存签到用户
                    $wh['act_id'] = $act_id;
                    $wh['phone'] = $tel;
                    $wh['delete_time'] = 0;
                    $data = Db::name('activity_user')->where($wh)->find();
                    $save['username'] = $add['username'];
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

    }

    /**
     *
     * 领取激活手环
     *
     */
    public function shouhuan(){
        if($this->request->isPost()){
            $tel = trim(I('mobile'));
            $act_id = (int)I('act_id');

            $wh['act_id'] = $act_id;
            $wh['phone'] = $tel;
            $data = Db::name('activity_user_signin')->where($wh)->find();
            if(empty($data)){
                $this->result([],0,'请先扫码进行签到！');
            }
            if($data['flag'] == 1){
                $this->result([],-1,'已经领取手环，请勿重复领取！');
            }

            $rs = Db::name('activity_user_signin')->where(['id'=>$data['id']])->update(['flag'=>1]);

            $signin_user =  session('signin_user_'.$act_id);
            $signin_user['flag'] = 1;

            session('signin_user_'.$act_id,$signin_user);
            if($rs){
                $this->result([],1,'领取手环成功！');
            }
        }
        $this->result([],0,'参数异常');
    }

}