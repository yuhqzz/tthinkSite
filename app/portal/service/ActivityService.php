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
namespace app\portal\service;


use app\admin\model\ActivityUserModel;

class ActivityService
{
    // 报名增加兑奖码
    public function createCodeByBooking($data){
        $activityUserModel =  new  ActivityUserModel();
        $res = $activityUserModel->createCode($data);
        return $res;
    }

    /**
     * 分享增加活动兑奖码
     * @param $data
     * @param $org
     * @return array
     */
    public function createCodeByShare($data,$org){

        $activityUserModel =  new  ActivityUserModel();
        $res = $activityUserModel->createCode($data,true,$org);
        return $res;
    }

}