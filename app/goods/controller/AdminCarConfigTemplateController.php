<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2018-06-14
 * Time: 13:59
 */

namespace app\goods\controller;


use cmf\controller\AdminBaseController;
use app\goods\model\GoodsCarConfigItemsModel;
use app\goods\service\CarConfigService;
use think\db;

/**
 * Class AdminCarConfigTemplateController
 * @package app\goods\controller
 */
class AdminCarConfigTemplateController extends AdminBaseController
{

    /*
     * 模板列表
     *
     */
    public function index()
    {
        return $this->fetch();
    }



}