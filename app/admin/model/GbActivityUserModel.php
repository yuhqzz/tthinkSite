<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2018-10-24
 * Time: 09:59
 */

namespace app\admin\model;


use think\Db;
use think\db\exception\DataNotFoundException;
use think\Model;


class GbActivityUserModel extends Model
{

    protected $name ='activity_gqbt';

    /**
     * 获取有资格的抽奖者
     * @param int $status
     * @param int $is_filter
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBookingUserList($status = 1,$is_filter = 0){
        $list = $this->where('status','=',intval($status))
            ->where('is_filter','=',intval($is_filter))
            ->where('is_wining','=',0)
            ->select();
        return $list?$list->toArray():[];
    }

    /**
     * 验证兑奖号码
     * @param $code
     * @param $act_id
     * @param int $level
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkCode($code,$act_id,$level = 0){
        if(empty($code)) return '兑奖号码不存在';
        // 验证兑奖码是否存在
        try{
            $code_user_info = $this->where('booking_phone','=',$code)->where('act_id','=',$act_id)->find()->toArray();
        }catch (DataNotFoundException $e){
            $code_user_info = [];
        }


        if( empty($code_user_info) || $code_user_info['status'] == 0){
           return '该兑奖码无效';
        }
        if( $this->isWining($code,$act_id) ){
            return '该奖码已经中过奖了';
        }
        if($level == 1){
            //1等奖 服务端过滤员工号码
           if($this->isEmployeePhone($code)){
               return '该奖码是员工号码';
           }
        }
        return true;
    }

    /**
     * 是否已经中奖了。
     * @param $code
     * @param $act_id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function isWining($code,$act_id){
        $where['code'] = $code;
        $where['act_id'] = (int)$act_id;
        $info = Db::name('activity_winning_code')->where($where)->find();
        if(!empty($info)){
            return true;
        }
        return false;
    }

    /**
     * 是否员工号码
     * @param $code
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function isEmployeePhone($code){
       if(empty($code)) return false;
       $user =  Db::name('dx_employee')->where('phone','=',$code)->find();
       if($user){
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
            $add['user_id'] = 0;
            $add['act_id'] = $data['act_id'];
            $add['level'] = $data['level'];
            $add['create_time'] = $data['create_time'];
            $info = Db::name('activity_winning_code')->insert($add);
            if($info){
                // 该用户的兑奖码全部失效 防止下次再中奖
                $rs = Db::name('activity_gqbt')->where(['booking_phone'=>$data['code'],'act_id'=>$data['act_id']])->update(['is_wining'=>1]);
                if(!$rs){
                    // 更新失败回滚
                    $transStatus = false;
                    Db::rollback();
                }
            }

            $transStatus = true;
            // 提交事务
            Db::commit();
        }catch(\Exception $e){
            Db::rollback();
        }
        return $transStatus;
    }

    /**
     *
     * @param $act_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getWiningList($act_id){
        $ck = 'honda_2019_act';
        $cache = Cache::get($ck);
        if(empty($cache)){
            $wining_list = Db::name('activity_winning_code')->where(['act_id','=',$act_id])->order('create_time desc')->select();
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
     * 兑奖码对应手机好是否中过奖。
     * @param $user_id
     * @return bool
     */
    public function todayIsWining($act_id){
        $cTime = strtotime(date("Y-m-d"))+22*3600;
        $where['create_time'] = $cTime;
        $where['act_id'] = $act_id;
        $count = Db::name('activity_winning_code')->where($where)->count();
        if($count && $count>8){
            return true;
        }
        return false;
    }

    /**
     * 当天已中奖数
     * @param $act_id
     * @return bool
     */
    public function todayWiningCount($act_id){
        $cTime = strtotime(date("Y-m-d"))+22*3600;
        $where['create_time'] = $cTime;
        $where['act_id'] = $act_id;
        $where['level'] = 0;
        $count = Db::name('activity_winning_code')->where($where)->count();
        return (int)$count;
    }
}