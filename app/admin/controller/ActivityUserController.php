<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2018-10-25
 * Time: 16:53
 */

namespace app\admin\controller;


use app\admin\model\ActivityModel;
use app\admin\model\ActivitySigninUserModel;
use app\admin\model\ActivityUserModel;
use app\admin\model\GbActivityUserModel;
use app\admin\model\PhoneLocationModel;
use cmf\controller\AdminBaseController;
use think\Collection;
use think\Db;
use think\db\exception\ModelNotFoundException;
use think\exception\ErrorException;

/**
 * Class ActivityUserController
 * @package app\admin\controller
 * @adminMenuRoot(
 *     'name'   => '活动用户',
 *     'action' => 'default',
 *     'parent' => 'admin/ActivityAdmin/index',
 *     'display'=> false,
 *     'order'  => 10000,
 *     'icon'   => '',
 *     'remark' => '活动用户'
 * )
 */
class ActivityUserController extends AdminBaseController
{

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     *
     * 活动用户列表
     * @return mixed|\think\response\Json
     * @throws \think\exception\DbException
     */
    public function index(){
        $act_id = $this->request->param('id');
        if(empty($act_id)){
            $this->error('活动不存在');
        }
        $activity = ActivityModel::get($act_id);
        if(empty($activity) || $activity['is_del'] == 1){
            $this->error('活动已经删除');
        }
        $this->assign('activity',$activity);
        if($act_id == 4){
            // 广本开业线上活动报名用户
            if($this->request->isAjax()){
                $this->model = new  GbActivityUserModel();
                list($where, $sort, $order, $offset, $limit) = $this->buildparams();
                $total = $this->model
                    ->alias('gba')
                    ->join('tx_weixin_user weiu','weiu.phone = gba.booking_phone','INNER')
                    ->where($where)
                    ->where('gba.act_id','=',$act_id)
                    ->order($sort, $order)
                    ->count();

                $list = $this->model
                    ->alias('gba')
                    ->field('gba.id book_id,gba.booking_phone,gba.source_phone,gba.ctime booking_ctime,weiu.*')
                    ->join('tx_weixin_user weiu','weiu.phone = gba.booking_phone','INNER')
                    ->where($where)
                    ->where('gba.act_id','=',$act_id)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

                $data = Collection::make($list)->toArray();
                $list = collection($data)->toArray();

                $result = array("total" => $total, "rows" => $list);
                return json($result);
            }
            return $this->fetch('gb_index');
        }else{
            if($this->request->isAjax()){
                $this->model = new ActivityUserModel();
                //如果发送的来源是Selectpage，则转发到Selectpage
                if ($this->request->request('keyField'))
                {
                    return $this->selectpage();
                }
                list($where, $sort, $order, $offset, $limit) = $this->buildparams();
                $total = $this->model
                    ->where($where)
                    ->where('act_id','=',$act_id)
                    ->order($sort, $order)
                    ->count();

                $list = $this->model
                    ->where($where)
                    ->where('act_id','=',$act_id)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();


                $data = Collection::make($list)->toArray();
                $list = collection($data)->toArray();

                $result = array("total" => $total, "rows" => $list);
                return json($result);
            }
            return $this->fetch();
        }

    }

    /**
     *
     * 删除活动签到会员
     * @return \think\response\Json
     */
    public function delete(){
        if ($this->request->isAjax()) {
            $params = $this->request->param();
            $ids = array_filter(explode(',',$params['ids']));

            if((int)$params['act_id'] === 4){
                // update
                 $save['is_filter'] = 1;
                try{
                    $rs = Db::name('activity_gqbt')->where(['id'=>['in',$ids]])->update($save);
                    $this->result([],1,$rs.'删除成功');

                }catch (ErrorException $e){
                    $this->result([],0,'删除失败');
                }
            }else{
                if($ids){
                    try{
                        Db::name('activity_user')->where(['id'=>['in',$ids]])->delete();
                        $this->result([],1,'删除成功');
                    }catch (ErrorException $e){
                        $this->result([],0,'删除失败');
                    }
                }
            }
        }
        $this->result([],0,'非法操作');
    }
    /**
     *
     * 删除活动签到会员
     * @return \think\response\Json
     */
    public function signinDelete(){
        if ($this->request->isAjax()) {
            $params = $this->request->param();
            $ids = array_filter(explode(',',$params['ids']));
            if($ids){
                try{
                    Db::name('activity_user_signin')->where(['id'=>['in',$ids]])->delete();
                    $this->result([],1,'删除成功');
                }catch (ErrorException $e){
                    $this->result([],0,'删除失败');
                }
            }
        }
        $this->result([],0,'非法操作');
    }

    /**
     *
     * 导入报名用户
     */
    public function loadData()
    {

        if ($this->request->isAjax()) {
            $params = $this->request->param();
            $act_id = isset($params['id'])?(int)$params['id']:'0';
            if (empty($act_id)) {
                $this->result([], 0, '上传数据异常');
            }
            if (!isset($params['file']) || empty($params['file'])) {
                $this->result([], 0, '上传数据为空');
            }
            //print_r($params);die;
            if (!isset($params['file']['filepath']) || empty($params['file']['preview_url'])) {
                $this->result([], 0, '上传文件出现异常');
            }

            $inputFileName = './upload/'.ltrim($params['file']['filepath'],'/');
            if (!file_exists($inputFileName)) {
                $this->result([], 0, '上传文件出现异常');
            }

            try {
                $inputFileType = \PHPExcel_IOFactory::identify($inputFileName);
                $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);

            } catch (\PHPExcel_Reader_Exception $e) {
                $msg = "加载文件发生错误:".pathinfo($inputFileName,PATHINFO_BASENAME)." : ".$e->getMessage();
                $this->result([], 0, $msg);
            }
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();

           // $highestColumn = $sheet->getHighestColumn();

            $allColumn = $sheet->getHighestDataColumn(); //取得最大的列号

            $maxColumnNumber = \PHPExcel_Cell::columnIndexFromString($allColumn);

            if( $highestRow>500 ){
                $this->result([], 0, '一次最多导入500,请拆分导入！');
            }

            $items = [];
            for ($row = 1; $row <= $highestRow; $row++){
                if($row == 1 ){
                    // 第一行为表头不需要导入
                    continue;
                }
                $values = [];
                for ($currentColumn = 0; $currentColumn < $maxColumnNumber; $currentColumn++) {
                    $val = $sheet->getCellByColumnAndRow($currentColumn, $row)->getValue();
                    $values[] = is_null($val) ? '' : trim($val);
                }
                $items[] = $values;
            }
            $insert_time = time();
            $inserteds = [];
            // 数据入库
            if( $items ){
                foreach ($items as $val){
                    if( trim($val[1]) == ''){
                        continue;
                    }
                    if(isset($inserteds[$val[1]])){
                        // 已经插入过不进行插入
                        continue;
                    }
                    $save['username'] = (string)$val[0];// 姓名
                    $save['phone'] = $val[1];  // 电话
                    $save['act_id'] = $act_id;
                    $save['ip'] = get_client_ip();
                    $save['mac'] = '';
                    $save['signup_time'] = $insert_time;
                    $save['signup_name'] = $val[2]?$val[2]:$val[0];
                    $save['remark'] = $val[3]?$val[3]:'';
                    $activity_user = new  ActivityUserModel();
                    $user_data = $activity_user->where(['phone'=>$save['phone'],'act_id'=>$save['act_id'],'delete_time'=>0])->find();
                    if( !$user_data ){
                        // 不存在插入
                        try{
                            $rs = $activity_user->allowField(false)->isUpdate(false)->save($save);
                            if($rs){
                                $inserteds[$save['phone']] = 1;
                            }
                        }catch (ErrorException $e){

                        }

                    }
                }
            }
            $this->result([],1,'导入完成');
        }
    }
    /**
     *
     * 导入报名用户
     */
    public function loadData2()
    {

        if ($this->request->isAjax()) {
            $params = $this->request->param();
            $act_id = isset($params['id'])?(int)$params['id']:'0';
            if (empty($act_id)) {
                $this->result([], 0, '上传数据异常');
            }
            if (!isset($params['file']) || empty($params['file'])) {
                $this->result([], 0, '上传数据为空');
            }
            //print_r($params);die;
            if (!isset($params['file']['filepath']) || empty($params['file']['preview_url'])) {
                $this->result([], 0, '上传文件出现异常');
            }

            $inputFileName = './upload/'.ltrim($params['file']['filepath'],'/');
            if (!file_exists($inputFileName)) {
                $this->result([], 0, '上传文件出现异常');
            }

            try {
                $inputFileType = \PHPExcel_IOFactory::identify($inputFileName);
                $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);

            } catch (\PHPExcel_Reader_Exception $e) {
                $msg = "加载文件发生错误:".pathinfo($inputFileName,PATHINFO_BASENAME)." : ".$e->getMessage();
                $this->result([], 0, $msg);
            }
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();

            // $highestColumn = $sheet->getHighestColumn();

            $allColumn = $sheet->getHighestDataColumn(); //取得最大的列号

            $maxColumnNumber = \PHPExcel_Cell::columnIndexFromString($allColumn);

            if( $highestRow>1000 ){
                $this->result([], 0, '一次最多导入1000,请拆分导入！');
            }

            $items = [];
            for ($row = 1; $row <= $highestRow; $row++){
                if($row == 1 ){
                    // 第一行为表头不需要导入
                    continue;
                }
                $values = [];
                for ($currentColumn = 0; $currentColumn < $maxColumnNumber; $currentColumn++) {
                    $val = $sheet->getCellByColumnAndRow($currentColumn, $row)->getValue();
                    $values[] = is_null($val) ? '' : trim($val);
                }
                $items[] = $values;
            }
            // 数据入库
            if( $items ){
                $save_phones = $phones = [];
                foreach ($items as $val){
                    if(isTelNumber($val[0])){
                        $phones[] = trim($val[0]);
                    }
                    if(isTelNumber($val[1])){
                        $phones[] = trim($val[1]);
                    }
                }
               foreach (array_unique($phones) as $val){
                   if(empty($val)) continue;
                   $save_phones[] = ['phone'=>$val];
               }
                if($save_phones){
                    $rs = Db::name('dx_employee')->insertAll($save_phones);
                }
            }
            $this->result([],1,'导入完成');
        }
    }

    /**
     * 活动签到用户
     *
     */
    public function signInUser(){
        $act_id = $this->request->param('act_id','0','intval');
        if(empty($act_id)){
            $this->error('活动不存在');
        }
        $activity = ActivityModel::get($act_id);
        if(empty($activity) || $activity['is_del'] == 1){
            $this->error('活动已经删除');
        }
        $this->assign('activity',$activity);

        if($this->request->isAjax()){
            $signinUsermodel = new ActivitySigninUserModel();
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $total = $signinUsermodel
                ->alias('aug')
                ->join(config('database.prefix').'activity_user au','au.phone = aug.phone and au.act_id = aug.act_id','INNER')
                ->where($where)
                ->where('aug.act_id','=',$act_id)
                ->order($sort, $order)
                ->count();

            $fields = 'aug.*,au.username real_name,au.signup_time,au.signup_name,au.remark';
            $list = $signinUsermodel
                ->alias('aug')
                ->join(config('database.prefix').'activity_user au','au.phone = aug.phone and au.act_id = aug.act_id','INNER')
                ->where($where)
                ->where('aug.act_id','=',$act_id)
                ->field($fields)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $data = Collection::make($list)->toArray();
            $list = collection($data)->toArray();

            $result = array("total" => $total, "rows" => $list);
            return json($result);

        }

        return $this->fetch();
    }

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function chouJiang()
    {
        $act_id = $this->request->param('act_id','0','intval');
        $type = $this->request->param('type','0','intval');
        if(empty($act_id)){
            $this->error('活动不存在');
        }
        $activity = ActivityModel::get($act_id);
        if(empty($activity) || $activity['is_del'] == 1){
            $this->error('活动已经删除');
        }
        $this->assign('activity',$activity);
        $mci = rand(1,3);
        $this->assign('mci',$mci);
        if($type == 1){
            return $this->fetch('chou_jiang_jl');
        }

        return $this->fetch();

    }

    public function getMobilePosition(){
        if($this->request->isAjax()){
            $mobile = $this->request->param('tel');
            $type = $this->request->param('type');
            $phoneLocation = new PhoneLocationModel();
            $city = $phoneLocation->getLocationCity($mobile);
            if($type == 2){
                if( $city === '深圳'){
                    // 更新 status = 1 //有效的
                    Db::name('activity_gqbt')->where('booking_phone','=',$mobile)->update(['status'=>1]);
                }
            }
            $location = $phoneLocation->getLocation($mobile);
            if($location){
                $this->result(['location'=>$location],1);
            }else{
                $this->result([],0,'未知电话号码');
            }
        }
        $this->result([],0,'非法请求');
    }


    public function ajaxGetActBookList(){
        $activity_user = new GbActivityUserModel();
        $list  = $activity_user->getBookingUserList();
        if(empty($list)){
            $this->result('暂无可以抽奖的兑奖码',0);
        }
        $this->result($list,1);
    }


    /**
     * 保存中奖的名单
     *
     */
    public function saveWinningUser(){
        if(!$this->request->isAjax()){
            $this->result('',0,'非法请求');
        }
        $act_id = $this->request->param('act_id');
        $level = $this->request->param('level',0,'intval');
        $gbActivity_user = new GbActivityUserModel();
        if($gbActivity_user->todayIsWining($act_id)){
            $this->result('',0,'今天已经没有抽奖次数了，请明天再抽!');
        }
        $act_codes = $this->request->param('act_code/a');
        if(empty($act_codes)){
            $this->result('',0,'兑奖码为空');
        }
        $save_msg = [];
        foreach ($act_codes as $act_code){
            $check_res = $gbActivity_user->checkCode($act_code,$act_id,0);
            if($check_res === true){
                // 验证通过 保存
                //获取今天0:0:1
                $today_end = strtotime(date("Y-m-d"))+22*3600;
                if($gbActivity_user->todayWiningCount($act_id) > 8){
                    $save_msg[$act_code] = '今天已经抽取了8个中奖号码！' ;
                    break;
                }
                $data['code'] = $act_code;
                $data['user_id'] = 0;
                $data['act_id'] = $act_id;
                $data['level'] = 0;
                $data['create_time'] = $today_end;
                $rs = $gbActivity_user->createWiningRecord($data);
                if($rs){
                    $save_msg[$act_code] = '['.$act_code.']'.'保存成功' ;
                }else{
                    $save_msg[$act_code] = '['.$act_code.']'.'保存失败' ;
                }
            }else{
                $save_msg[$act_code] = '['.$act_code.']'.$check_res ;
            }
        }
        $this->result('',1,implode('<br/>',$save_msg));
    }

    /**
     * 保存中奖的名单
     *
     */
    public function saveWinningUser2(){
        if(!$this->request->isAjax()){
            $this->result('',0,'非法请求');
        }
        $act_id = $this->request->param('act_id');
        $level = $this->request->param('level',0,'intval');
        $gbActivity_user = new GbActivityUserModel();

        $act_codes = $this->request->param('act_code/a');
        if(empty($act_codes)){
            $this->result('',0,'兑奖码为空');
        }
        $save_msg = [];
        $this->result('',1,implode('<br/>',$save_msg));
    }
}