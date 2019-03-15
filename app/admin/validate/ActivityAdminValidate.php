<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\validate;

use think\Validate;

class ActivityAdminValidate extends Validate
{
    protected $rule = [
        'title' => 'require',
        'address'  => 'require',
        'description'  => 'require',
        'poster' => 'require',
        'need_sign_up'=>'checkSignUp',
        'signup_stime'=>'checkSignUp',
        'signup_etime'=>'checkSignUp',
        'start_time' => 'require|checkStartTime',
        'end_time' => 'require|checkEndTime',
    ];
    protected $message = [
        'title.require' => '标题不能为空',
        'address.require' => '地点不能为空',
        'description.require' => '介绍不能为空',
        'poster.require' => '海报封面不能为空',
        'start_time.require'  => '开始时间不能为空',
        'end_time.require' => '结束不能为空',
        'start_time.checkStartTime'   => '开始时间不正确',
        'end_time.checkEndTime'  => '结束时间不正确',
    ];

    protected $scene = [
        'add'  => ['title', 'address','description', 'poster','need_sign_up','signup_stime','signup_etime','start_time','end_time'],
        'edit'  => ['title', 'address','description','poster'],
    ];

    public function checkStartTime( $value, $rule, $data){
        $now_time = time();
        if($now_time>$value){
            return "开始时间必须大于当前时间";
        }
        if(isset($data['end_time'])){
            if( $data['end_time'] < $value ){
                return "结束时间必须大于开始时间";
            }
        }
        return true;

    }

    public function checkEndTime($value, $rule, $data){
        $now_time = time();

        if($now_time>$value){
            return "结束时间必须大于当前时间";
        }
        if(isset($data['start_time'])){
            if( $data['start_time'] > $value ){
                return "开始时间必须小于结束时间";
            }
        }
        return true;
    }

    /**
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    public function checkSignUp($value, $rule, $data){
        if(isset($data['need_sign_up']) && $data['need_sign_up'] == 1){
            //验证预报名时间
            $now_time = time();
            //开始时间
            if(isset($data['signup_stime']) && $now_time > $data['signup_stime']){
                return "预报名开始时间必须大于当前时间";
            }
            //结束时间
            if(isset($data['signup_etime']) && $now_time > $data['signup_etime']){
                return "预报名结束时间必须大于当前时间";
            }
            //
            if(isset($data['signup_stime']) && isset($data['signup_etime']) && isset($data['start_time'])){
                if( $data['signup_etime'] < $data['signup_stime'] ){
                    return "预报名开始时间必须预报名小于结束时间";
                }
                //预报名结束时间必须小于等于活动开始时间
                /*if($data['signup_etime'] > $data['start_time']){
                    return "预报名结束时间必须小于等于活动开始时间";
                }*/

            }

        }
        return true;

    }


}