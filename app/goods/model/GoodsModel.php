<?php
// +----------------------------------------------------------------------
// | goods category model
// +----------------------------------------------------------------------
namespace app\goods\model;

use think\Cache;
use think\Db;
use think\Model;
use cmf\service;


class GoodsModel extends Model
{
    /**
     *
     * 保存商品数据
     */
   public function addGoodsData($data){
       if(empty($data['post'])) return false;
       Db::startTrans();
        // 保存主表数据
       $data['post']['create_time'] = time(); // 发布时间
       $goods_id = $this->isUpdate(false)->allowField(true)->insertGetId($data['post']);
       if($goods_id){
           // 保存属性信息
            if(isset($data['attr'])&&!empty($data['attr'])){
                //属性数据
                $save_attr_data = [];
                foreach ($data['attr'] as $key=>$val){
                    if(is_array($val)){
                        foreach ($val as $k=>$v){
                            $item =[];
                            $item['goods_id'] = $goods_id;
                            $item['attr_id'] = $key;
                            $item['attr_value'] = (string)$v;
                            $save_attr_data[] = $item;
                        }
                    }else{
                        $item =[];
                        $item['goods_id'] = $goods_id;
                        $item['attr_id'] = $key;
                        $item['attr_value'] = (string)$val;
                        $save_attr_data[] = $item;
                    }
                }
                try{
                    Db::name('goods_attr')->insertAll($save_attr_data);
                }catch (\Exception $exception){
                    Db::rollback();
                }

            }
           // 保存图片信息
            if(isset($data['images']['photo_urls'])&&!empty($data['images']['photo_urls'])){
                $save_images_data = [];
                foreach ($data['images']['photo_urls'] as $key=>$val){
                    $item =[];
                    $item['goods_id'] = (int)$goods_id;
                    $item['name'] = isset($data['images']['photo_names'][$key])?$data['images']['photo_names'][$key]:'';
                    $item['image_url'] = (string)$val;
                    $save_images_data[] = $item;

                }
                try{
                    Db::name('goods_images')->insertAll($save_images_data);
                }catch (\Exception $exception){
                    Db::rollback();
                }
            }
       }
       Db::commit();
       return $goods_id;
   }

    /**
     *
     * 保存商品数据
     */
    public function saveGoodsData($goods_id,$data){
        $goods_id = (int)$goods_id;
        if(empty($goods_id)) return false;
        if(empty($data['post'])) return false;
        Db::startTrans();
        // 保存主表数据
        $data['post']['last_update'] = time(); // 更新时间
        $where['id'] = (int)$goods_id;
        $this->isUpdate(true)->allowField(true)->where($where)->update($data['post']);
        if($goods_id){
            // 先删除该商品的属性
            try{
                Db::name('goods_attr')->where(['goods_id'=>$goods_id])->delete();
            }catch (\Exception $exception){
                Db::rollback();
            }
            // 保存属性信息
            if(isset($data['attr']) && !empty($data['attr'])){
                //属性数据
                $save_attr_data = [];
                foreach ($data['attr'] as $key=>$val){
                    if(is_array($val)){
                        foreach ($val as $k=>$v){
                            $item = [];
                            $item['goods_id'] = $goods_id;
                            $item['attr_id'] = $key;
                            $item['attr_value'] = (string)$v;
                            $save_attr_data[] = $item;
                        }
                    }else{
                        $item =[];
                        $item['goods_id'] = $goods_id;
                        $item['attr_id'] = $key;
                        $item['attr_value'] = (string)$val;
                        $save_attr_data[] = $item;
                    }
                }
                try{
                    Db::name('goods_attr')->insertAll($save_attr_data);
                }catch (\Exception $exception){
                    Db::rollback();
                }
            }
            // 先删除该商品的图片
            try{
                Db::name('goods_images')->where(['goods_id'=>$goods_id])->delete();
            }catch (\Exception $exception){
                Db::rollback();
            }
            // 保存图片信息
            if(isset($data['images']['photo_urls']) && !empty($data['images']['photo_urls'])){
                $save_images_data = [];
                foreach ($data['images']['photo_urls'] as $key=>$val){
                    $item =[];
                    $item['goods_id'] = $goods_id;
                    $item['name'] = isset($data['images']['photo_names'][$key])?$data['images']['photo_names'][$key]:'';
                    $item['image_url'] = (string)$val;
                    $save_images_data[] = $item;
                }
                try{
                    Db::name('goods_images')->insertAll($save_images_data);
                }catch (\Exception $exception){
                    Db::rollback();
                }
            }
        }
        self::commit();
        return $goods_id;
    }

    public function deleteGoods($data)
    {

        if (isset($data['id'])) {
            $id = $data['id']; //获取删除id

            $res = $this->where(['id' => $id])->find();

            if ($res) {
                $res = json_decode(json_encode($res), true); //转换为数组
                $recycleData = [
                    'object_id'   => $res['id'],
                    'create_time' => time(),
                    'table_name'  => 'goods',
                    'name'        => $res['name'],
                    'user_id' =>cmf_get_current_admin_id()

                ];

                Db::startTrans(); //开启事务
                $transStatus = false;
                try {
                    Db::name('goods')->where(['id' => $id])->update([
                        'delete_time' => time()
                    ]);
                    Db::name('recycle_bin')->insert($recycleData);

                    $transStatus = true;
                    // 提交事务
                    Db::commit();
                } catch (\Exception $e) {

                    // 回滚事务
                    Db::rollback();
                }
                return $transStatus;

            } else {
                return false;
            }
        } elseif (isset($data['ids'])) {
            $ids = $data['ids'];
            $res = $this->where(['id' => ['in', $ids]])
                ->select();
            if ($res) {
                $res = json_decode(json_encode($res), true);
                foreach ($res as $key => $value) {
                    $recycleData[$key]['object_id']   = $value['id'];
                    $recycleData[$key]['create_time'] = time();
                    $recycleData[$key]['table_name']  = 'goods';
                    $recycleData[$key]['name']        = $value['name'];
                    $recycleData[$key]['user_id']        = cmf_get_current_admin_id();
                }

                Db::startTrans(); //开启事务
                $transStatus = false;
                try {
                    Db::name('goods')->where(['id' => ['in', $ids]])
                        ->update([
                            'delete_time' => time()
                        ]);
                    Db::name('recycle_bin')->insertAll($recycleData);
                    $transStatus = true;
                    // 提交事务
                    Db::commit();
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                }
                return $transStatus;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function getGoodsList($where,$order='',$limit=1){
       $wh['g.delete_time'] = 0;
       if(empty($order)){
           $order = 'g.last_update desc,g.create_time desc';
       }
       if($wh){
           $wh = array_merge($wh,$where);
       }

       /*'id' => int 2
          'name' => string '2016款 2.6L风尚版  前置前驱CVT无级变速' (length=50)
          'category_id' => int 1
          'brand_id' => int 2
          'series_id' => int 3
          'style_id' => int 5
          'grade_id' => int 4
          'click_count' => int 0
          'comment_count' => int 0
          'market_price' => string '24.98' (length=5)
          'shop_price' => string '24.98' (length=5)
          'factory_price' => string '24.98' (length=5)
          'price_ladder' => null
          'keywords' => string '风尚；本田；艾力绅' (length=27)
          'goods_remark' => string '2016款 2.6L风尚版  前置前驱CVT无级变速' (length=50)
          'description' => string '&lt;p&gt;2016款 2.6L风尚版&amp;nbsp; 前置前驱CVT无级变速2016款 2.6L风尚版&amp;nbsp; 前置前驱CVT无级变速2016款 2.6L风尚版&amp;nbsp; 前置前驱CVT无级变速2016款 2.6L风尚版&amp;nbsp; 前置前驱CVT无级变速2016款 2.6L风尚版&amp;nbsp; 前置前驱CVT无级变速2016款 2.6L风尚版&amp;nbsp; 前置前驱CVT无级变速2016款 2.6L风尚版&amp;nbsp; 前置前驱CVT无级变速2016款 2.6L风尚版&amp;nbsp; 前置前驱CVT无级变速2016款 2.6L风尚版&amp;nbsp;'... (length=1082)
          'original_img' => string 'goods/20180629/2c813c80a53735c5e6b03a819408db6d.jpg' (length=51)
          'is_on_sale' => int 1
          'on_time' => int 0
          'list_order' => int 50
          'is_recommend' => int 0
          'is_new' => int 0
          'is_hot' => int 0
          'create_time' => int 1530260532
          'last_update' => int 0
          'sales_sum' => int 0
          'delete_time' => int 0
          'seriesName*/

       $fields = 'g.*,gcs.name as series_name';
       $list = $this->table(config('database.prefix').'goods')
           ->alias('g')
           ->join(config('database.prefix').'goods_car_series gcs','g.series_id =gcs.id','INNER')
           ->field($fields)
           ->where($wh)
           ->order($order)
           ->cache(false)
           ->fetchSql(false)
           ->paginate($limit);
       /*$sql= $this->table(config('database.prefix').'goods')
           ->alias('g')
           ->join(config('database.prefix').'goods_car_series gcs','g.series_id =gcs.id','INNER')
           ->field($fields)
           ->where($wh)
           ->order($order)
           ->cache(true)
           ->fetchSql(true)
           ->limit($limit)
           ->select();
      dump($sql);die;*/
       return $list;
   }
    /**
     * @param int $cid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAttributeList($cid = 0){
        $cid = (int)$cid;
        $c_k = service\MkeyService::getMkey(service\MkeyService::ATTRIBUTEVALUESBYSTYLEID,$cid);
        $cache = Cache::get($c_k);
        if(!$cache){
            // 缓存不存在时
            $wh = [];
            if($cid){
                $wh['type_id'] = intval($cid);
            }
            $list = $this->where($wh)->order('list_order')->select();
            $list = $list?$list->toArray():[];
            foreach ($list as &$val){
                if(trim($val['attr_values'])){
                    $val['attr_values'] = explode("\r\n",$val['attr_values']);
                }
            }


            $cache = [];
            $cache['value'] = $list;
            Cache::set($c_k,$cache,service\MkeyService::DAYEXPIRE);
        }
        return $cache['value'];
    }

    /**
     * 添加配置项
     * @param $data
     * @return bool|int|string
     */
    public function addAttribute( $data ){
        if(empty($data)) return false;
        $cate_id    = intval($data['type_id']);
        $save['attr_name'] = $data['attr_name'];
        $save['type_id'] = $cate_id;
        $save['attr_type'] = intval($data['attr_type']);
        $save['attr_input_type'] = intval($data['attr_input_type']);
        $save['attr_values'] = trim($data['attr_values']);
        $id =  $this->allowField(true)->insertGetId($save);
        if($id){
            $this->clearCache($cate_id);
        }
        return $id;
    }

    /**
     * 编辑配置项
     * @param $data
     * @return bool|false|int
     */
    public function editAttribute( $data ){
        if(empty($data)) return false;
        $save = [];
        $id          = intval($data['attr_id']);
        $cate_id    = intval($data['type_id']);
        $save['attr_id'] = (int)$data['attr_id'];
        $save['attr_name'] = trim($data['attr_name']);
        $save['attr_type'] = intval($data['attr_type']);
        $save['attr_input_type'] = intval($data['attr_input_type']);
        $save['type_id'] = $cate_id;
        $save['attr_values'] = trim($data['attr_values']);  $res = $this->isUpdate(true)->allowField(true)->save($save, ['id' => $id]);
        if($res){
            $this->clearCache($cate_id);
        }
        return $res;
    }


    public function clearCache($cate_id,$clear_all = false){
        $cate_id = intval($cate_id);
        $c_k = service\MkeyService::getMkey(service\MkeyService::ATTRIBUTEVALUESBYSTYLEID,$cate_id);
        $c_k_all = service\MkeyService::getMkey(service\MkeyService::ATTRIBUTEVALUESBYSTYLEID,0);
        Cache::rm($c_k);
        Cache::rm($c_k_all);
        if($clear_all){
            $data = self::all();
            foreach ($data as $val){
                $c_k = service\MkeyService::getMkey(service\MkeyService::ATTRIBUTEVALUESBYSTYLEID,$val['type_id']);
                Cache::rm($c_k);
            }
        }
    }

    /**
     *
     *  获取商品的属性
     * @param $goods_id
     */
    public function getAttrDataByGoodsId($goods_id){
        if(empty($goods_id)) return [];
        $wh['goods_id'] = (int)$goods_id;
        $list = Db::name('goods_attr')->where($wh)->select();
        $data = $list?$list->toArray():[];
        if($data){
            $list = [];
            foreach ($data as $val){
                $list[$val['attr_id']][] = $val['attr_value'];
            }
            $data = $list;
        }
        return $data;
    }

    public function getLatestRecommend($brand_id = 0,$limit = 4){
        $wh['g.delete_time'] = 0;
        if(empty($order)){
            $order = 'g.last_update desc,g.create_time desc';
        }
        $wh['g.is_on_sale'] = 1;
        $wh['g.is_recommend'] = 1;
        $wh['g.is_hot'] = 1;
        if($brand_id>0){
            $wh['g.brand_id'] = $brand_id;
        }
            //int_color  out_color
        $fields = 'g.*,gcs.name as series_name,gcs.example_img,gcs.min_price,gcs.max_price,gb.name as brand_name';
        $list = $this->table(config('database.prefix').'goods')
            ->alias('g')
            ->join(config('database.prefix').'goods_car_series gcs','g.series_id = gcs.id','INNER')
            ->join(config('database.prefix').'goods_brand gb','g.brand_id = gb.id','INNER')
            ->field($fields)
            ->where($wh)
            ->order($order)
            ->limit($limit)
            ->cache(false)
            ->fetchSql(false)
            ->select();
        $list = $list?$list->toArray():[];
        return $list;
    }

    /**
     * 获取新品
     * @param int $limit
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public function getLatestCar($limit = 4){
        $wh['g.delete_time'] = 0;
        if(empty($order)){
            $order = 'g.last_update desc,g.create_time desc';
        }
        $wh['g.is_on_sale'] = 1;
        $wh['g.is_new'] = 1;
        $fields = 'g.*,gcs.name as series_name,gcs.example_img,gcs.min_price,gcs.max_price,gb.name as brand_name';
        $list = $this->table(config('database.prefix').'goods')
            ->alias('g')
            ->join(config('database.prefix').'goods_car_series gcs','g.series_id = gcs.id','INNER')
            ->join(config('database.prefix').'goods_brand gb','g.brand_id = gb.id','INNER')
            ->field($fields)
            ->where($wh)
            ->order($order)
            ->limit($limit)
            ->cache(false)
            ->fetchSql(false)
            ->select();
        $list = $list?$list->toArray():[];
        return $list;
    }

    /**
     *
     * 置顶的车型
     * @param int $limit
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public function getTopCar($limit = 4){
        $wh['g.delete_time'] = 0;
        if(empty($order)){
            $order = 'g.last_update desc,g.create_time desc';
        }
        $wh['g.is_on_sale'] = 1;
        $wh['g.is_top'] = 1;
        $fields = 'g.*,gcs.name as series_name,gcs.example_img,gcs.min_price,gcs.max_price,gb.name as brand_name';
        $list = $this->table(config('database.prefix').'goods')
            ->alias('g')
            ->join(config('database.prefix').'goods_car_series gcs','g.series_id = gcs.id','INNER')
            ->join(config('database.prefix').'goods_brand gb','g.brand_id = gb.id','INNER')
            ->field($fields)
            ->where($wh)
            ->order($order)
            ->limit($limit)
            ->cache(false)
            ->fetchSql(false)
            ->select();
        $list = $list?$list->toArray():[];
        return $list;
    }

}