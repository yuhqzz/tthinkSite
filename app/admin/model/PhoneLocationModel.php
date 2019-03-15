<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2018-10-24
 * Time: 09:59
 */

namespace app\admin\model;


use think\Cache;
use think\Model;


class PhoneLocationModel extends Model
{

    protected $name ='phone_location';

    public function getLocationData($phone,$update = false){
        if(empty($phone)) return [];
        $ck = 'tx-location-'.$phone;
        $cache = Cache::get($ck);
        if($cache === false || $update){
            $data = $this->where(['phone'=>$phone])->find();
            $data = $data?$data->toArray():[];
            if($data){
                Cache::set($ck,$data);
            }else{
                $req_url = 'http://mobsec-dianhua.baidu.com/dianhua_api/open/location';
                $req_url .= '?tel='.$phone;
                $result = httpRequest($req_url);
                if($result){
                    $result = \Qiniu\json_decode($result,true);
                    if($result['responseHeader']['status'] == 200){
                        if(isset($result['response'][$phone])){
                            $add['phone'] = $phone;
                            $add['location'] = $result['response'][$phone]['location'];
                            $add['province'] = $result['response'][$phone]['detail']['province'] ;
                            $add['city'] = $result['response'][$phone]['detail']['area'][0]['city'] ;
                            $add['operator'] = $result['response'][$phone]['detail']['operator'] ;
                            $add['content'] = json_encode($result);
                                 $id = $this->allowField(true)->save($add);
                                 if($id){
                                     $data = $add;
                                 }
                            }

                        }
                    }
            }
            $cache = $data;
        }
        return $cache;
    }

    /**
     * 获取号码归属地城市
     * @param $phone
     * @param bool $update
     * @return string
     */
    public function getLocationCity($phone,$update = false){
        $data = $this->getLocationData($phone,$update);
        if($data){
            return $data['city'];
        }

        return '';
    }

    /**
     * 获取号码归属地省份
     * @param $phone
     * @param bool $update
     * @return string
     */
    public function getLocationProvince($phone,$update = false){
        $data = $this->getLocationData($phone,$update);
        if($data){
            return $data['province'];
        }

        return '';
    }

    /**
     * 获取号码归属地
     * @param $phone
     * @param bool $update
     * @return string
     */
    public function getLocation($phone,$update = false){
        $data = $this->getLocationData($phone,$update);
        if($data){
            return $data['location'];
        }

        return '';
    }

}