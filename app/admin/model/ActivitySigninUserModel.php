<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2018-10-24
 * Time: 09:59
 */

namespace app\admin\model;


use think\Model;


class ActivitySigninUserModel extends Model
{

    protected $table ='tx_activity_user_signin';

    /**
     *
     * 是否签到
     * @param $phone
     * @param $act_id
     * @return bool
     */
    public function isSigned( $phone, $act_id){
        if(empty($phone)||empty($act_id)) return false;
        $wh['act_id'] = (int)$act_id;
        $wh['phone'] = $phone;
        $count = $this->where($wh)->count();
        return $count?true:false;
    }
}