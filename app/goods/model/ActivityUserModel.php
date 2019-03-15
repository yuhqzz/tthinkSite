<?php
// +----------------------------------------------------------------------
// | goods ActivityUser model
// +----------------------------------------------------------------------
namespace app\goods\model;

use think\Cache;
use think\Db;
use think\Model;
use cmf\service;


class ActivityUserModel extends Model
{

    protected $table ='tx_activity_user';


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
        $fields = 'u.id,u.user_name,u.phone,u.ip,u.mac,u.reg_time,u.pass,u.status,ac.code,ac.active,ac.is_share,ac.is_filter,ac.create_time,ac.status as cstatus';
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
        $fields = 'u.id,u.user_name,u.phone,u.ip,u.mac,u.reg_time,u.pass,u.status,ac.code,ac.active,ac.is_share,ac.is_filter,ac.create_time,ac.status as cstatus';
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
        $fields = 'u.id,u.user_name,u.phone,u.ip,u.mac,u.reg_time,u.pass,u.status,ac.code,ac.active,ac.is_share,ac.is_filter,ac.create_time,ac.status as cstatus';
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
        $order = 'reg_time desc';
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
     * @param string $org_code
     * @return array
     */
    public function createCode($data,$is_share = false,$org_code = ''){
        if(empty($data)) return [];
        $res = [];
        Db::startTrans(); //开启事务
        $transStatus = false;
        $time = time();
        $add['user_name'] = 'tx-'.$data['phone'];
        $add['pass'] = md5($data['phone']);
        $add['phone'] = $data['phone'];
        $add['mac'] = md5($data['ip']);
        $add['ip'] = $data['ip'];
        $add['reg_time'] = $time;
        $add['status'] = 1;
        $codeMode = Db::name('activity_code');
        $userMode = Db::name('activity_user');
        try {
            $uid = $userMode->insertGetId($add);
            if($uid){
                $code_add['user_id'] = $uid;
                $code_str = $data['act_code'];
                $code_add['code'] = $code_str;
                if($is_share){
                    $code_add['is_share'] = 1;
                }
                $code_add['create_time'] = $time;
                $rs = Db::name('activity_code')->insert($code_add);
                $user = $add;
                $user['id'] = $uid;
                if(!empty($org_code) && $is_share){

                    // 获取分享源的uid
                    $org_user = Db::name('user_token')->where(['token'=>$org_code])->find();
                    if($org_user){
                        // 同时给源用户添加一个兑换码
                        $code_org_add['user_id'] = $org_user['user_id'];
                        $code_org_add['create_time'] = $time;
                        $new_code_str = rand(100000,999999);
                        $code_org_add['code'] = $new_code_str;
                        $codeMode->insert($code_org_add);
                    }
                }
                //报名成功自动登陆
                session('act_user',$user);
                $token = cmf_generate_user_token($uid, 'mobile');
                session('token', $token);
                cookie('isLogin',1);
            }
            $transStatus = true;
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            var_dump($e);
            // 回滚事务
            Db::rollback();
            //报名成功自动登陆
            session('act_user',null);
            session('token', null);
            cookie('isLogin',null);
        }
        if($transStatus){
            $res['uid'] = $uid;
            $res['code'] = $code_str;
            $res['token'] = $token;
        }

        return $res;
    }

    /**
     *
     */
    public function getLatestUser($limit = 4){
        $where['delete_time'] =0;
        $where['status'] = 1;
        $list = $this->where($where)->order('reg_time desc')->limit($limit)->select();
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
        $ck = 'chuanqi_818_act';
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
    public function getActUserInfo($uid){
        $uid = (int)$uid;
        $ck = 'act_user_'.$uid;
        $cache = Cache::get($ck);
        if(empty($cache)){
            $user = Db::name('activity_user')->where(['id'=>$uid])->find();
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
    public function getActUserPhone($uid){
        $user_info = $this->getActUserInfo($uid);
        return $user_info?$user_info['phone']:0;
    }

}