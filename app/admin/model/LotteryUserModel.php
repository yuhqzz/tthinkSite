<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2019-03-14
 * Time: 13:46
 */

namespace app\admin\model;


use think\Model;

class LotteryUserModel extends Model
{
  protected  $name = "lottery_user";


    /**
     * @param $lottery_id
     * @param $mobile
     * @param int $draw_count
     * @param string $source
     * @return LotteryUserModel
     */
  public function booking($lottery_id,$mobile,$draw_count =3,$source =''){
      $data['lottery_id'] = intval($lottery_id);
      $data['mobile'] = $mobile;
      $data['draw_count'] = intval($draw_count);
      $data['source'] = $source;
      $data['createtime'] = time();
      return $this->create($data);
  }

    /**
     *
     * 是否报名
     * @param $lottery_id
     * @param $mobile
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
  public function isBooked($lottery_id,$mobile){
      $rs = $this->where('lottery_id','=',intval($lottery_id))->where('mobile','=',$mobile)->find();
    return $rs?true:false;
  }

}