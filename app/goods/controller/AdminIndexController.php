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
namespace app\goods\controller;

use cmf\controller\AdminBaseController;
use Dompdf\Exception;
use think\db;
/**
 * Class AdminIndexController
 * @package app\goods\controller
 * @adminMenuRoot(
 *     'name'   =>'商品管理',
 *     'action' =>'default',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 30,
 *     'icon'   =>'shopping-cart',
 *     'remark' =>'商品管理'
 * )
 */
class AdminIndexController extends AdminBaseController
{
    /**
     * ajax 修改指定表数据字段  一般修改状态 比如 是否推荐 是否开启 等 图标切换的 table,id_name,id_value,field,value
     * @adminMenu(
     *     'name'   => 'ajax 修改指定表数据字段 ',
     *     'parent' => 'goods/AdminIndex/default',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => 'ajax 修改指定表数据字段 ',
     *     'param'  => ''
     * )
     */
    public function changeTableVal(){
        $table = I('table'); // 表名
        $id_name = I('id_name'); // 表主键id名
        $id_value = I('id_value'); // 表主键id值
        $field  = I('field'); // 修改哪个字段
        $value  = I('value'); // 修改字段值
        $save = [];
        $save[$field] = $value;
        if( $table === 'goods' && $field === 'is_on_sale'){
            $save['on_time'] = time();
        }
        $rs = db::name($table)->where("$id_name = $id_value")->update(array($field=>$value)); // 根据条件保存修改的数据
        if($rs){
            echo  1;
            return ;
        }
        echo  0;
    }

    /**
     * ajax删除商品图片
     * @adminMenu(
     *     'name'   => 'ajax删除商品图片',
     *     'parent' => 'goods/AdminIndex/default',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => 'ajax删除商品图片',
     *     'param'  => ''
     * )
     *
     * @return bool|int|void
     *
     * @throws \think\Exception
     *
     */
    public function removeGoodsImages(){
        $img_id = I('id'); // 表主键id名
        $goods_id = I('goods_id'); // 表主键id名
        if(empty($img_id)||empty($goods_id)) {
            echo 0;
            return false;
        }
        $wh['img_id'] = $img_id;
        $wh['goods_id'] = $goods_id;
        try{
            $rs = db::name('goods_images')->where($wh)->delete();
            if(is_numeric($rs)){
                echo 1;
                return ;
            }
        }catch (Exception $e){
            return 0;
        }
    }
}
