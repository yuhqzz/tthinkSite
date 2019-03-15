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
namespace app\admin\controller;

use app\admin\model\ActivityModel;
use app\admin\model\PhoneLocationModel;
use app\goods\model\ActivityUserModel;
use cmf\controller\AdminBaseController;
use think\Cache;
use think\Collection;
use think\Db;
use think\exception\ErrorException;
use think\Image;

/**
 * Class UserController
 * @package app\admin\controller
 * @adminMenuRoot(
 *     'name'   => '活动管理',
 *     'action' => 'default',
 *     'parent' => 'user/AdminIndex/default',
 *     'display'=> true,
 *     'order'  => 10000,
 *     'icon'   => '',
 *     'remark' => '管理组'
 * )
 */
class ActivityAdminController extends AdminBaseController
{

    /**
     * 活动列表
     * @adminMenu(
     *     'name'   => '活动列表',
     *     'parent' => 'default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '活动列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
       if($this->request->isAjax()){
           $this->model = new ActivityModel();
           //如果发送的来源是Selectpage，则转发到Selectpage
           if ($this->request->request('keyField'))
           {
               return $this->selectpage();
           }
           list($where, $sort, $order, $offset, $limit) = $this->buildparams();
           $total = $this->model
               ->where($where)
               ->order($sort, $order)
               ->count();

           $list = $this->model
               ->where($where)
               ->order($sort, $order)
               ->limit($offset, $limit)
               ->select();

            $data = Collection::make($list)->toArray();
            $list = collection($data)->toArray();
            if($list){
                foreach ( $list as &$item ) {
                    $item['create_uname'] = getUserName([$item['create_uid']]);
                }
            }

            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->fetch();
    }

    /**
     * 活动发布
     * @adminMenu(
     *     'name'   => '活动发布',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '活动发布',
     *     'param'  => ''
     * )
     */
    public function  add(){
        return $this->fetch();
    }

    /**
     *
     * @adminMenu(
     *     'name'   => '活动发布提交',
     *     'parent' => 'add',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '活动发布提交',
     *     'param'  => ''
     *
     */
    public function addPost(){
        if ($this->request->isPost()) {

            $params = $this->request->param();
           // var_dump($params);die;
            //活动标题
            $data['title'] = trim($params['title']);
            //地址
            $data['address'] = htmlspecialchars(trim($params['address']),ENT_QUOTES);
            //活动介绍
            $data['description'] = htmlspecialchars(trim($params['description']),ENT_QUOTES);
            //活动海报
            $data['poster'] = $params['poster'];

            //是否需要预报名
            if(isset($params['need_sign_up'])){
                $data['need_sign_up'] = intval($params['need_sign_up']);
            }else{
                $data['need_sign_up'] = 0;
            }

            //开始报名时间
            $data['signup_stime'] = strtotime($params['signup_stime']);
            //结束报名时间
            $data['signup_etime'] = strtotime($params['signup_etime']);

            // 活动开始时间
            $data['start_time'] = strtotime($params['start_time']);
            // 活动结束时间
            $data['end_time'] = strtotime($params['end_time']);

            //置顶
            if(isset($params['istop'])){
                $data['istop'] = intval($params['istop']);
            }else{
                $data['istop'] = 0;
            }

            $result = $this->validate($data, 'ActivityAdmin.add');
            if ($result !== true) {
                $this->error($result);
            }

            //发布者
            $data['create_uid'] = cmf_get_current_admin_id();
            //发布时间
            $data['createtime'] = time();

            //开启报名
            if(isset($params['status'])){
                $data['status'] = intval($params['status']);
            }else{
                $data['status']  = 0;
            }

            $result = Db::name('activity')->insert($data);
            if ($result === false) {
                $this->error('添加失败!');
            }
            $this->success('添加成功!', url('ActivityAdmin/index'));
        }

    }

    /**
     * 编辑
     * @adminMenu(
     *     'name'   => '编辑',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑',
     *     'param'  => ''
     *
     */
    public function edit(){
        $id = $this->request->param('id', 0, 'intval');
        if ($id > 0) {
            $activity = ActivityModel::get($id);
            $activity = $activity?$activity->toArray():[];
            if( empty($activity) ){
                $this->error('活动不存在!');
            }
            if( $activity['is_del'] == 1 ){
                $this->error('活动已删除!');
            }
            $this->assign($activity);
            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }

    }

    /**
     *
     * 编辑提交
     * @adminMenu(
     *     'name'   => '编辑提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑提交',
     *     'param'  => ''
     *
     */
    public function editPost(){
        if($this->request->isPost()){
            $params = $this->request->param();
            $params['id'] = intval($params['id']);
            if(empty($params['id'])){
                $this->error('保存失败!');
            }
            $params = $this->request->param();
            //活动标题
            $data['title'] = trim($params['title']);
            //地址
            $data['address'] = htmlspecialchars(trim($params['address']),ENT_QUOTES);
            //活动介绍
            $data['description'] = htmlspecialchars(trim($params['description']),ENT_QUOTES);
            //活动海报
            $data['poster'] = $params['poster'];

            //是否需要预报名
            if(isset($params['need_sign_up'])){
                $data['need_sign_up'] = intval($params['need_sign_up']);
            }else{
                $data['need_sign_up'] = 0;
            }

            //开始报名时间
            $data['signup_stime'] = strtotime($params['signup_stime']);
            //结束报名时间
            $data['signup_etime'] = strtotime($params['signup_etime']);

            // 活动开始时间
            $data['start_time'] = strtotime($params['start_time']);
            // 活动结束时间
            $data['end_time'] = strtotime($params['end_time']);

            //置顶
            if(isset($params['istop'])){
                $data['istop'] = intval($params['istop']);
            }else{
                $data['istop'] = 0;
            }

            $result = $this->validate($data, 'ActivityAdmin.edit');
            if ($result !== true) {
                $this->error($result);
            }

            //发布者

            //编辑时间
            $data['updatetime'] = time();
            //开启报名
            if(isset($params['status'])){
                $data['status'] = intval($params['status']);
            }else{
                $data['status']  = 0;
            }

            $result = Db::name('activity')->where(['id'=>(int)$params['id']])->update($data);
            if ($result === false) {
                $this->error('添加失败!');
            }
            $this->success('保存成功!');
        }
        $this->error('非法操作!');
    }

    /**
     *
     * @adminMenu(
     *     'name'   => '活动详情',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '活动详情',
     *     'param'  => ''
     */
    public function detail(){
        $id = $this->request->param('id', 0, 'intval');
        if ($id > 0) {
            $activity = ActivityModel::get($id);
            $activity = $activity?$activity->toArray():[];
            if( empty($activity) ){
                $this->error('活动不存在!');
            }
            if( $activity['is_del'] == 1 ){
                $this->error('活动已删除!');
            }
            $this->assign($activity);
            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }
    }

    /**
     *
     * 活动报名
     * @adminMenu(
     *     'name'   => '活动报名',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '活动报名',
     *     'param'  => ''
     */
    public function signUp(){
        $id = $this->request->param('id', 0, 'intval');
        if ($id > 0) {
            $activity = ActivityModel::get($id);
            $activity = $activity?$activity->toArray():[];
            if( empty($activity) ){
                $this->error('活动不存在!');
            }
            if( $activity['is_del'] == 1 ){
                $this->error('活动已删除!');
            }
            $this->assign($activity);
            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }
    }

    /**
     *
     * 活动报名提交
     * @adminMenu(
     *     'name'   => '活动报名提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '活动报名提交',
     *     'param'  => ''
     *
     */
    public function signUpPost(){
         if($this->request->isPost()){
             $params = $this->request->param();
             $act_id = (int)$params['act_id'];
             if(empty( $act_id )){
                $this->error('操作错误!');
             }
             if(empty( $params['row'] )){
                 $this->error('添加的用户为空!');
             }
             $error = [];
             $success = [];
             foreach ( $params['row']['tel'] as $key=>$val ){

                 if( empty($val) ) continue;

                 if(!isTelNumber($val)){
                     $msg = $val.'不是正确的电话号码';
                     $error[] = $msg;
                     continue;
                 }

                 $wh['act_id'] = $act_id;
                 $wh['phone'] = $val;
                 $wh['delete_time'] = 0;

                 $data = Db::name('activity_user')->where($wh)->find();

                 $add['username'] = isset($params['row']['username'][$key])?trim($params['row']['username'][$key]):$params['title'];
                 $add['phone'] = $val;
                 $add['ip'] = get_client_ip();
                 $add['signup_time'] = time();
                 $add['mac'] = md5(get_client_ip());
                 $add['act_id'] = $act_id;

                 if($data){
                     // 更新
                     $res = Db::name('activity_user')->where(['id'=>$data['id']])->update($add);

                 }else{
                     // 添加
                     $res = Db::name('activity_user')->insert($add);
                 }
                 if(is_numeric($res)){
                     $success[] = $val.'报名成功!';
                 }

             }

             if( empty($success) && empty($error)){
                 $this->error('报名用户为空!');
             }

             if( empty($success) && !empty($error)){
                 $this->error(implode('<br />',$error));
             }

             $info = implode('<br/>',$success)."<hr />".implode('<br />',$error);
             $this->success('报名完成!<br />'.$info);
         }
         $this->error('非法操作');
    }

    /**
     *
     * 活动删除
     * @adminMenu(
     *     'name'   => '活动删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '活动删除',
     *     'param'  => ''
     *
     */
    public function delete(){

        $ids                  = $this->request->param();

        $this->success('删除成功');

    }


    /**
     *
     *
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function batchSignUp(){

        if($this->request->isPost()){
            // 提交报名
            $params = $this->request->param();
            $act_id = $params['act_id'];
            $users = $params['sign_up_users'];
            if(empty($users)){
                $this->error('请添加报名用户!');
            }
            $activityUserModel = new  ActivityUserModel();
            $users = explode(',',$users);
            $msg = [];
            try{
                foreach ($users as $user){
                    $add['user_name'] = 'tx-'.$user;
                    $add['pass'] = 'tx-'.$user;
                    $add['phone'] = $user;
                    $add['ip'] = get_client_ip();
                    $add['reg_time'] = time();
                    $add['act_id'] = $act_id;
                    $rs = db::name('activity_user')->insert($add);
                    if(!$rs){
                        $msg[] = $user;
                    }
                }
            }catch (ErrorException $e){
                echo  $e;
            }
            if($msg){
                $this->error(implode(',',$msg).' 报名失败');
            }else{
                $this->success(implode(',',$users).'报名成功',url('ActivityAdmin/index'));
            }
        }else{
            $act_id = $this->request->param('id');
            $data = ActivityModel::get($act_id);
            if(empty($data)){
                $this->error('活动已不存在');
            }
            if($data['end_time']<time()){
                $this->error('活动已经结束');
            }
            if( $data['status'] == 0){
                // 活动未开始报名
                $this->error('活动未开始报名!');
            }elseif ($data['status'] == 2){
                $this->error('活动已开始，报名已经结束!');
            }elseif ($data['status'] == 3){
                $this->error('活动已经结束!');
            }
            $this->assign('data',$data);
            return $this->fetch('sign_up');
        }
    }

    /**
     *
     * @return mixed
     *
     */
    public function activityDetail(){
        // 获取 所有的活动参与者
        $activity_user = new ActivityUserModel();
        $user_list = $activity_user->getActivityUserList([],50);
        $this->assign('userList',$user_list);
        $code_count = $activity_user->getUserCodeCount();
        $user_count = $activity_user->getUserCount();
        $this->assign('user_count',$user_count);
        $this->assign('code_count',$code_count);
        // 获取中奖记录
        $wining_list =  $activity_user->getWiningList();
        $this->assign('wining_list',$wining_list);
        return $this->fetch();
    }



    /**
     * 获取用户
     * @return mixed
     */
    public function userCode(){
        $user_id = I('user_id');
        if(empty($user_id)){
            $this->error('用户id为空');
        }
        $activity_user = new ActivityUserModel();
        $userInfo = $activity_user->getActivityUserInfo($user_id);
        if(empty($userInfo)){
            $this->error('无效用户');
        }
        // 用户兑奖码
        $user_code_list = $activity_user->getUserCodeListByUid($user_id,[]);
        $count = $user_code_list?$user_code_list->count():0;
        $this->assign('userCodeList',$user_code_list);
        $this->assign('userInfo',$userInfo);
        $this->assign('userCodeCount',$count);
        return $this->fetch('user_act_code');
    }

    /**
     *
     * 抽奖页面
     */
    public function chouJiang(){
     /*   $today = strtotime(date("Y-m-d"));
        echo $today.'<br/>';
       echo date('y-m-d H:i:s',strtotime(date("Y-m-d"))+86400);die;*/
        /*$time = time();

        if($time<strtotime(date("Y-m-d"))+86400){
            $this->error('今天已经抽过奖了,请明天再抽！',url('admin/ActivityAdmin/activityDetail',['act_id'=>1]));
        }*/


       /* $activity_user = new ActivityUserModel();
        if($activity_user->todayIsWining()){
            $this->error('今天已经抽过奖了,请明天再抽！',url('admin/ActivityAdmin/activityDetail',['act_id'=>1]));
        }*/
        $mci = rand(1,3);
        $this->assign('mci',$mci);
        return $this->fetch();
    }

    /**
     *
     * 获取要抽奖的手机号码
     */
    public function ajaxGetActCodeList(){
        $activity_user = new ActivityUserModel();
        $list  = $activity_user->getCodeList();
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
        //18826246252 777036
        $this->result('抽奖已经结束',0);
        $activity_user = new ActivityUserModel();
        if($activity_user->todayIsWining()){
            $this->result('',0,'今天已经抽个奖了，请明天再抽！');
        }
        $act_code = I('act_code');
        if(empty($act_code)){
            $this->result('',0,'兑奖码为空');
        }
        // 验证兑奖码是否存在
        $code_info = $activity_user->getActCodeInfo($act_code);
        if(empty($code_info) || $code_info['is_filter'] == 1){
            $this->result('',0,'该兑奖码无效');
        }
        if($activity_user->isWining($act_code)){
            $this->result('',0,'该奖码已经中过奖了');
        }
        if($activity_user->isWiningUser($code_info['user_id'])){
            $this->result('',0,'该奖码已经中过奖了');
        }
        //获取今天0:0:1
        //$today = strtotime(date("Y-m-d"));
        $today_end = strtotime(date("Y-m-d"))-1;
        $data['code'] = $act_code;
        $data['user_id'] = $code_info['user_id'];
        $data['create_time'] = $today_end;
        $rs = $activity_user->createWiningRecord($data);
        if($rs){
            $this->result('',1,'保存成功');
        }
        $this->result('',0,'保存失败');
    }

    /**
     *
     * 获取手机号码归属地
     *
     */
    public function ajaxPhoneAddress(){
        $tel = I('tel');
        if(empty($tel)){
            $this->result('手机号码为空',0);
        }
        $phoneLocation = new PhoneLocationModel();
        $location = $phoneLocation->getLocation($tel);
        if($location){
            $this->result(['location'=>$location],1);
        }else{
            $this->result([],0,'未知电话号码');
        }
    }

    /**
     *
     * 导出活动用户名单
     *
     */
    public function exportActivityUser(){
        $objPHPExcel = new \PHPExcel();
        $count = Db::name('activity_user_signin')->count();
        if($count){
            $user_data = Db::name('activity_user_signin')->order('singin_time desc')->select();
        }else{
            $user_data = [];
        }
        $objPHPExcel->getProperties()->setCreator("txqc")->setLastModifiedBy("传祺818签到嘉宾")->setTitle("传祺818签到嘉宾")->setSubject("传祺818签到嘉宾")->setDescription("传祺818签到嘉宾")->setKeywords("传祺818签到嘉宾")->setCategory("传祺818签到嘉宾");

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '序号')->setCellValue('B1', '姓名')->setCellValue('C1', '签到时间')->setCellValue('D1', '电话');
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('传祺818签到嘉宾-' . date('Y-m-d'));

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);
        $objPHPExcel->getActiveSheet()->freezePane('A2');
        $i = 2;
        foreach($user_data as $data){
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $data['id'])->getStyle('A'.$i)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $data['name'],\PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, date('Y-m-d H:i:s',$data['singin_time']),\PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $data['phone'],\PHPExcel_Cell_DataType::TYPE_STRING);
            $i++ ;
        }
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->getColumnDimension('A')->setWidth(18.5);
        $objActSheet->getColumnDimension('B')->setWidth(23.5);
        $objActSheet->getColumnDimension('C')->setWidth(18.5);
        $objActSheet->getColumnDimension('D')->setWidth(18.5);


        $filename = date('Ymd',time()).'_act_user_'.time();
        ob_end_clean();//清除缓冲区,避免乱码
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('php://output');
    }

    public function selectpage(){

        return [];
    }

    /**
     *  活动图集
     * @return array|mixed|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function imgs(){
        $act_id = $this->request->param('act_id');
        if(empty($act_id)){
            $this->error('参数错误');
        }
        $this->assign('act_id',$act_id);
        if($this->request->isAjax()){
            $this->model = Db::name('activity_img');
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

            $list = Collection::make($list)->toArray();
            if($list){
                foreach ($list as &$value){
                    $value['img_url'] = $value['img_path']?cmf_get_image_url($value['img_path']):'';
                }
            }

            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->fetch();
    }

    /**
     *  活动图集上传
     * @return array|mixed|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addImgs(){

        $act_id = $this->request->param('act_id');
        if(empty($act_id)) {
            $this->error('参数错误!');
        }
        $this->assign('act_id',$act_id);
        return $this->fetch();
    }

    /**
     *  活动图集上传
     * @return array|mixed|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function doAddImgs(){
        if ($this->request->isPost()) {
            $params = $this->request->param();
            $act_id = (int)$params['act_id'];
            if(empty($act_id)){
                $this->error('参数出错');
            }
            $data = [];
            $createtime = time();
            $start_id = Db::name('activity_img')->max('id');
            foreach ($params['images']['photo_urls'] as $key=>$imgs ){
                $add = [];
                if(empty($imgs)) continue;
                $add['img_path']  = $imgs;
                $add['act_id']  = $params['act_id'];
                $add['createtime']  = $createtime;
                $add['list_order']  = $start_id++;
                $data[] = $add;
            }
            if(!empty($data)){
                $result = Db::name('activity_img')->insertAll($data);
                if ($result === false) {
                    $this->error('保存失败!');
                }
            }
            $this->success('保存成功!', url('ActivityAdmin/imgs',array('act_id'=>$act_id)));
        }

    }

    /**
     * 删除
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function deleteImgs(){
        if ($this->request->isAjax()) {
            $params = $this->request->param();
            $ids = array_filter(explode(',',$params['ids']));
            if($ids){
                try{
                    Db::name('activity_img')->where(['id'=>['in',$ids]])->delete();
                    $this->result([],1,'删除成功');
                }catch (ErrorException $e){
                    $this->result([],0,'删除失败');
                }
            }
        }
        $this->result([],0,'非法操作');
    }
}