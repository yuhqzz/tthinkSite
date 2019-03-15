<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2018-10-24
 * Time: 09:59
 */

namespace app\admin\model;


use think\Model;
use think\Cache;
use think\Db;
use cmf\service;


class ActivityUserModel extends Model
{

    protected $name ='activity_user';

    /**
     *
     * @param $user
     * @param array $codes
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getCodeList($where = [],$codes = []){
        $codes = array_filter($codes);
        if(!empty($codes)&&is_array($codes)){
            $where['ac.code'] = ['in',$codes] ;
        }
        $where['ac.is_filter'] = 0;
        $order = 'ac.create_time desc';
        $fields = 'u.id,u.user_name,u.phone,u.ip,u.mac,u.signup_name,u.pass,u.status,ac.code,ac.active,ac.is_share,ac.is_filter,ac.create_time,ac.status as cstatus';
        $list = $this->table(config('database.prefix').'activity_user')
            ->alias('u')
            ->join(config('database.prefix').'activity_code ac','ac.user_id = u.id','INNER')
            ->field($fields)
            ->where($where)
            ->order($order)
            ->cache(false)
            ->fetchSql(false)
            ->select();
        return $list;
    }
    /**
     *
     * @param $user
     * @param array $codes
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getUserCodeList($user,$codes = []){
        $codes = array_filter($codes);
        if(!empty($codes)&&is_array($codes)){
            $where['ac.code'] = ['in',$codes] ;
        }
        $where['u.phone'] = $user ;
        $order = 'ac.create_time desc';
        $fields = 'u.id,u.user_name,u.phone,u.ip,u.mac,u.signup_name,u.pass,u.status,ac.code,ac.active,ac.is_share,ac.is_filter,ac.create_time,ac.status as cstatus';
        $list = $this->table(config('database.prefix').'activity_user')
            ->alias('u')
            ->join(config('database.prefix').'activity_code ac','ac.user_id = u.id','INNER')
            ->field($fields)
            ->where($where)
            ->order($order)
            ->cache(false)
            ->fetchSql(false)
            ->select();
        return $list;
    }

    /**
     * 获取用户兑奖码
     * @param $uid
     * @param array $codes
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getUserCodeListByUid($uid,$codes = [],$limit = 20){
        $codes = array_filter($codes);
        if(!empty($codes)&&is_array($codes)){
            $where['ac.code'] = ['in',$codes] ;
        }
        $where['u.id'] = (int)$uid ;
        $order = 'ac.create_time desc';
        $fields = 'u.id,u.user_name,u.phone,u.ip,u.mac,u.signup_name,u.pass,u.status,ac.code,ac.active,ac.is_share,ac.is_filter,ac.create_time,ac.status as cstatus';
        $list = $this->table(config('database.prefix').'activity_user')
            ->alias('u')
            ->join(config('database.prefix').'activity_code ac','ac.user_id = u.id','INNER')
            ->field($fields)
            ->where($where)
            ->order($order)
            ->cache(false)
            ->fetchSql(false)
            ->paginate($limit);
        return $list;
    }

    /**
     *
     * @param $user
     * @param array $codes
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getActivityUserList($wh = [],$limit = 2){
        $where = [];
        if(!empty($wh)){
            $where = array_merge($where,$wh);
        }
        $where['delete_time'] = 0 ;
        $order = 'signup_name desc';
        $list = $this->where($where)
            ->order($order)
            ->paginate($limit);
        return $list;
    }



    /**
     * 参与活动并生成活动码
     *
     * @param $data
     * @param bool $is_share
     * @param string $source_token
     * @return array
     */
    public function createCode($data,$is_share = false,$source_token = ''){
        if(empty($data)) return [];
        $res = [];
        Db::startTrans(); //开启事务
        $add['username'] = $data['username'];
        $add['phone'] = $data['phone'];
        $add['act_id'] = $data['act_id'];
        $add['mac'] = '';
        $add['ip'] = $data['ip'];
        $add['signup_time'] = $data['signup_time'];
        $add['status'] = 1;
        $add['signup_name'] = $data['username'];
        $userMode = Db::name('activity_user');
        $codeMode = Db::name('activity_code');
        try {
            // 报名成功
            $uid = $userMode->insertGetId($add);
            if($uid){
                //  添加中奖码
                $code_add['user_id'] = $uid;
                $code_add['code'] = $data['code'];
                if($is_share){
                    $code_add['is_share'] = 1;
                }
                $code_add['create_time'] = $add['signup_time'] ;
                Db::name('activity_code')->insert($code_add);
                $user = $add;
                $user['id'] = $uid;
                $source_user = [];
                if(!empty($org_code) && $is_share){
                    // 获取分享源的uid
                    $org_user = Db::name('user_token')->where(['token'=>$source_token])->find();
                    if($org_user){
                        // 同时给源用户添加一个兑换码
                        $code_org_add['user_id'] = $org_user['user_id'];
                        $code_org_add['create_time'] =  $add['signup_time'];
                        $new_code_str = rand(100000,999999);
                        $code_org_add['code'] = $new_code_str;
                        $codeMode->insert($code_org_add);
                        $source_user = $this->where(['id'=>$org_user['user_id']])->find();
                    }
                }
                $wei_user_book = Db::name('activity_gqbt')
                    ->where('booking_phone','=',$data['phone'])
                    ->where('act_id','=',$data['act_id'])
                    ->find();
                if(!$wei_user_book){
                    // gb抽奖表
                    // 插入微信用户表
                    $insert['booking_phone'] = $data['phone'] ;
                    $insert['source_phone'] = $source_user?$source_user['phone']:'';
                    $insert['ctime'] = $data['signup_time'] ;
                    $insert['act_id'] = $data['act_id'];
                    $insert['code'] = $data['code'];
                    Db::name('activity_gqbt')->insert($insert);
                }else{
                    // 更新兑奖码
                    Db::name('activity_gqbt')->where('id','=',$wei_user_book['id'])->update(['code'=>$data['code']]);
                }
                // 提交事务
                Db::commit();
                $user = $add;
                $user['id'] = $uid;
                //报名成功自动登陆
                $token = cmf_generate_user_token($uid, 'mobile');
                session('act_user',$user);
                session('token', $token);
                cookie('isLogin',1);
                $res['uid'] = $uid;
                $res['token'] = $token;
            }else{
                Db::rollback();
                //报名成功自动登陆
                session('act_user',null);
                session('token', null);
                cookie('isLogin',null);
            }
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            //报名成功自动登陆
            session('act_user',null);
            session('token', null);
            cookie('isLogin',null);
        }
        return $res;
    }


    /**
     *
     */
    public function getLatestUser($limit = 4){
        $where['delete_time'] =0;
        $where['status'] = 1;
        $list = $this->where($where)->order('signup_name desc')->limit($limit)->select();
        $list = $list?$list->toArray():[];
        return $list;
    }

    public function getGbUser($limit = 4){
        $where['act_id'] = 4;
        $list = Db::name('activity_gqbt')->where($where)->order('ctime desc')->limit($limit)->select();
        $list = $list?$list->toArray():[];
        return $list;
    }




    /**
     *
     * 获取用户信息
     * @param $uid
     * @return array|false|\PDOStatement|string|Model
     */
    public function getActivityUserInfo($uid){
        $where['id'] = (int)$uid;
        $user = $this->where($where)->find();
        return $user;

    }

    /**
     *
     * @param $user
     *
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getUserCodeData($uid){

        $data = Db::name('activity_code')->where(['user_id'=>(int)$uid])->select();
        return $data?$data->toArray():[];
    }

    /**
     *
     * @param $user
     *
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getGBUserCodeData($uid){

        $data = Db::name('activity_gqbt')->where(['source_phone'=>(int)$uid])->select();
        return $data?$data->toArray():[];
    }

    /**
     *
     * 获取活动派送的兑奖码
     * @param $uid
     *
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getUserCodeCount($uid = 0){
        if(empty($uid)){
            $total = Db::name('activity_code')->count();
        }else{
            $total = Db::name('activity_code')->where(['user_id'=>(int)$uid])->count();
        }
        return (int)$total;
    }

    /**
     *
     * 获取活动报名用户
     * @param $wh=
     *
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getUserCount($wh=[]){
        if($wh){
            $total =$this->where($wh)->count();
        }else{
            $total =$this->count();
        }
        return (int)$total;
    }

    /**
     * 获取兑奖码详情信息
     */
    public function getActCodeInfo($code){
        $where['code'] = $code;
        $where['status'] = 1;
        $info = Db::name('activity_code')->where($where)->find();
        return $info;
    }

    /**
     * 是否已经中奖了。
     * @param $code
     * @return bool
     */
    public function isWining($code){
        $where['code'] = $code;
        $info = Db::name('activity_winning_code')->where($where)->find();
        if(!empty($info)){
            return true;
        }
        return false;
    }

    /**
     * 兑奖码对应手机好是否中过奖。
     * @param $user_id
     * @return bool
     */
    public function isWiningUser($user_id){
        $where['user_id'] = $user_id;
        $info = Db::name('activity_winning_code')->where($where)->find();
        if(!empty($info)){
            return true;
        }
        return false;
    }

    /**
     * 兑奖码对应手机好是否中过奖。
     * @param $user_id
     * @return bool
     */
    public function todayIsWining(){
        $cTime = strtotime(date("Y-m-d"))-1;
        $where['create_time'] = $cTime;
        $info = Db::name('activity_winning_code')->where($where)->find();
        if(!empty($info)){
            return true;
        }
        return false;
    }

    /**
     *  中奖记录保存
     * @param $data
     * @return array|bool
     */
    public function createWiningRecord($data){
        if(empty($data)) return [];
        $res = [];
        Db::startTrans(); //开启事务
        $transStatus = false;
        try {
            // 插入记录
            $add['code'] = $data['code'];
            $add['user_id'] = (int)$data['user_id'];
            $add['create_time'] = $data['create_time'];
            $info = Db::name('activity_winning_code')->insert($add);
            if($info){
                // 该用户的兑奖码全部失效 防止下次再中奖
                $rs = Db::name('activity_code')->where(['user_id'=>$data['user_id']])->update(['is_filter'=>1]);
                if(!$rs){
                    // 更新失败回滚
                    $transStatus = false;
                    Db::rollback();
                }
            }
            $ck = 'chuanqi_818_act';
            Cache::rm($ck,'');
            $transStatus = true;
            // 提交事务
            Db::commit();
        }catch(\Exception $e){
            Db::rollback();
            Cache::rm($ck,'');
        }
        return $transStatus;
    }

    /**
     *
     * 获取中奖记录
     */
    public function getWiningList(){
        $ck = 'honda_2019_act';
        $cache = Cache::get($ck);
        if(empty($cache)){
            $wining_list = Db::name('activity_winning_code')->order('create_time desc')->select();
            $wining_list = $wining_list?$wining_list->toArray():[];
            if($wining_list){
                Cache::set($ck,$wining_list,3600);
            }
            $list = $wining_list;

        }else{
            $list = $cache;
        }
        return $list;
    }


    /**
     *
     * 获取活动用户信息
     * @param $uid
     *
     * @return array|false|mixed|\PDOStatement|string|Model
     */
    public function getActUserInfo($uid,$act_id = 0){
        $uid = (int)$uid;
        $ck = 'act_user_'.$uid.'_'.$act_id;
        $cache = Cache::get($ck);
        if(empty($cache)){
            $user = Db::name('activity_user')->where(['id'=>$uid,'act_id'=>$act_id])->find();
            if($user){
                Cache::set($ck,$user,7200);
            }
            $list = $user;

        }else{
            $list = $cache;
        }
        return $list;
    }

    /**
     * 获取用户电话
     * @param $uid
     * @return int|mixed
     */
    public function getActUserPhone($uid,$act_id){
        $user_info = $this->getActUserInfo($uid,$act_id);
        return $user_info?$user_info['phone']:0;
    }

}