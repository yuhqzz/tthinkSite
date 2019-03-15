<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2019-03-14
 * Time: 13:46
 */

namespace app\admin\model;


use think\Model;

class LotteryModel extends Model
{
  protected  $name = "lottery";


    /**
     *
     * 产生中奖项
     *
     * @param int $lottery_id
     */
  public function lotterying( $lottery_id = 0){


      return 0;
  }
}