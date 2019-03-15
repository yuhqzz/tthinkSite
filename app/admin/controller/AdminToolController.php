<?php
// +----------------------------------------------------------------------
// |管理员工具
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\goods\model\GoodsBrandModel;
use app\goods\model\GoodsCarConfigItemsModel;
use app\goods\model\GoodsCarSeriesModel;
use app\goods\model\GoodsCarStyleModel;
use app\goods\validate\GoodsCarConfigCategoryValidate;
use cmf\controller\AdminBaseController;
use cmf\service\MkeyService;
use think\Cache;
use think\Db;
use think\Exception;
use think\exception\DbException;

class AdminToolController extends AdminBaseController
{
    public function _initialize()
    {
    }


    /**
     * 管理员工具列表
     * @adminMenu(
     *     'name'   => '幻灯片管理',
     *     'parent' => 'admin/Setting/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 40,
     *     'icon'   => '',
     *     'remark' => '幻灯片管理',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $str =<<<EOF
<tr><td rowspan="2"><div class="w2">奔驰</div></td>
<td rowspan="1"><div class="w4">奔驰S级</div></td>
<td><div class="w7 Sys_models-table-con"><input class="checkbox" type="checkbox" name="checkbox" id="5497132"><div class="img"><div class="icon icon_zd"></div><img src="http://pic1.cheoo.com/201803/903049dd656912cdf6c16a0b0b6a59e0.jpg@!120x80" width="80" height="60" alt=""></div>
													<div class="info">
											<a target="_blank" href="http://www.maiche168.com/d721168/chexing/5497132.html">奔驰S450 18款 短轴 四驱</a><p class="color">加版现车</p><p class="color">黑/黑</p></div></div></td><td class="tv-t"><div class="w2">127万</div></td><td><div class="w2">2018-07-26 08:56</div></td>

													<td><div class="wm"><a href="/index.php?c=info&amp;m=adminInfoEdit&amp;id=5497132" class="mr10"><i class="icon icon_edit"></i>编辑</a></div></td>
											</tr>
											<tr><td rowspan="2"><div class="w2">奔驰</div></td>
<td rowspan="1"><div class="w4">奔驰S级</div></td>
<td><div class="w7 Sys_models-table-con"><input class="checkbox" type="checkbox" name="checkbox" id="5497132"><div class="img"><div class="icon icon_zd"></div><img src="http://pic1.cheoo.com/201803/903049dd656912cdf6c16a0b0b6a59e0.jpg@!120x80" width="80" height="60" alt=""></div>
													<div class="info">
											<a target="_blank" href="http://www.maiche168.com/d721168/chexing/5497132.html">奔驰S450 18款 短轴 四驱</a><p class="color">加版现车</p><p class="color">黑/黑</p></div></div></td><td class="tv-t"><div class="w2">127万</div></td><td><div class="w2">2018-07-26 08:56</div></td>

													<td><div class="wm"><a href="/index.php?c=info&amp;m=adminInfoEdit&amp;id=5497132" class="mr10"><i class="icon icon_edit"></i>编辑</a></div></td>
											</tr>
											<tr><td rowspan="2"><div class="w2">奔驰</div></td>
<td rowspan="1"><div class="w4">奔驰S级</div></td>
<td><div class="w7 Sys_models-table-con"><input class="checkbox" type="checkbox" name="checkbox" id="5497132"><div class="img"><div class="icon icon_zd"></div><img src="http://pic1.cheoo.com/201803/903049dd656912cdf6c16a0b0b6a59e0.jpg@!120x80" width="80" height="60" alt=""></div>
													<div class="info">
											<a target="_blank" href="http://www.maiche168.com/d721168/chexing/5497132.html">奔驰S450 18款 短轴 四驱</a><p class="color">加版现车</p><p class="color">黑/黑</p></div></div></td><td class="tv-t"><div class="w2">127万</div></td><td><div class="w2">2018-07-26 08:56</div></td>

													<td><div class="wm"><a href="/index.php?c=info&amp;m=adminInfoEdit&amp;id=5497132" class="mr10"><i class="icon icon_edit"></i>编辑</a></div></td>
											</tr>
EOF;

        //$pattern ="/<[^>]+>(.*)<\/[^>]+>/Ui";
        $pattern ="/<tr.*?>(.*?)<\/tr>/ism";

        preg_match_all($pattern,$str,$arr);
        echo "<pre>";
        print_r($arr);
        $pattern2 = "/<td.*?>(.*?)<\/td>/ism";
        preg_match_all($pattern2,$arr[1][0],$arr2);
        print_r($arr2);

        $pattern3 = "/<[^>]+>(.*)<\/[^>]+?>/U";
        preg_match_all($pattern3,$arr2[1][2],$arr3);
        print_r($arr3);
        die;





         /*$spider = new  \spider\SpiderImg('download/images/test');
         $ulr ='http://cartype-image.mucang.cn/cartype-logo/2017/02/22/10/86e36835dc144336a4da8d7d60c56c6b_315X210.png!315x210';
         $spider->downloadImage($ulr);
         $img = $spider->getRealSavePath();
         bb(dirname($img));
         bb(pathinfo($img));
         bb(basename($img));

        basename($img);*/

        // return $this->fetch();
    }

    /**
     * 导入品牌
     *
     */
    public function loadCarBrand(){
        $url = 'http://parallel-home-web.maiche.com/api/web/brand-web/get-grouped-brand-list.htm';
        $data =  httpRequest($url);
        $data = \Qiniu\json_decode($data,true);die;
       /* bb($data['data']['itemList']);die;*/
        $goodsBrandModel = new GoodsBrandModel();
        $spider = new  \spider\SpiderImg();
        $index = 0;
        foreach($data['data']['itemList'] as $val){
            foreach($val['brandList'] as $v){
                $add['name'] = $v['name'];
                $add['first_char'] = strtoupper($v['firstLetter']);
                $more['orgBrandId'] = $v['id'];
                $add['more'] = json_encode($more);
                $rs = $goodsBrandModel->allowField(true)->insert($add);
                if($rs){
                    $index++;
                }
                $spider->downloadImage($v['logoUrl']);
            }
        }
        echo $index;



        //$spider = new  \spider\SpiderImg();

       // $spider->downloadImage('http://cartype.image.mucang.cn/cartype-logo/2018/0720/17/a1c60218fa06434cba29fc457eb1b652.jpg!210x140');

    }

    /**
     * 导入品牌
     *
     */
    public function loadCarBrand2(){
        $cache = Cache::get('ck_a_li_brand_data');
        if(empty($cache)){
            $url = 'https://jisucxdq.market.alicloudapi.com/car/brand';
            $rs = $this->_requestAli($url);
            if(!empty($rs)){
                Cache::set('ck_a_li_brand_data',$rs,86400);
            }
            $cache = $rs;
        }
        $data = \Qiniu\json_decode($cache,true);
        $goodsBrandModel = new GoodsBrandModel();
        $spider = new  \spider\SpiderImg('goods/');
        $index = 0;
        foreach($data['result'] as $val){
                $add['name'] = $val['name'];
                $add['first_char'] = strtoupper($val['initial']);
                $filenameStr = '';
                if(stripos($val['logo'],'.png') !== false ){
                    $spider->downloadImage($val['logo']);
                    $img = $spider->getRealSavePath();
                    $filename = basename($img);
                    $filenameStr = 'goods/'.date('Ymd').'/'.$filename;
                }
                $add['icon'] = $filenameStr;
                $more['orgBrandId'] = $val['id'];
                $add['more'] = json_encode($more);
                $rs = $goodsBrandModel->allowField(true)->insert($add);
                if($rs){
                    $index++;
                }
        }
        echo $index;
    }

    /**
     * 导入品牌
     *
     */
    public function loadCarSeries2(){
        set_time_limit(0);
        $res = [];
        $prev_brand_id = intval(I('b_id'));
        $goodsBrandModel = new GoodsBrandModel();
        $brands =  $goodsBrandModel->where(['delete_time'=>0,'parentid'=>0,'id'=>['gt',$prev_brand_id]])->order('id asc')->limit(1)->select();
        $brands =  $brands->toArray();
        $goodsSeriesModel = new GoodsCarSeriesModel();
        $spider = new  \spider\SpiderImg('goods/');
        $error_brand = [];
        $brand_id = 0;
        if( empty($brands)){
            echo  '数据已全部刷完';
            die;
        }
       // var_dump($brands[0]['id']);die;
       /* $brand_id = $brands[0]['id'];
        $url = "http://www.txqc.com/admin/admin_tool/loadCarSeries2?b_id={$brand_id}";
        echo "<script type='text/javascript'>";
        echo "window.location.href='$url'";
        echo "</script>";die;*/
        foreach($brands as $item){
            $brand_id = (int)$item['id'];
            $more = \Qiniu\json_decode($item['more'],true);
            $org_brand_id  = (int)$more['orgBrandId'];
            if(empty($org_brand_id)) {
                $error_brand[] =$brand_id;
                continue;
            }
            $cache = Cache::get('ck_a_li_brand_series_data_'.$org_brand_id);
            if(empty($cache)){
                $url = 'https://jisucxdq.market.alicloudapi.com/car/carlist?parentid='.$org_brand_id;
                $rs = $this->_requestAli($url);
                if(!empty($rs)){
                    Cache::set('ck_a_li_brand_series_data_'.$org_brand_id,$rs,86400*30);
                }
                $cache = $rs;
            }
            $data = \Qiniu\json_decode($cache,true);
            $index = 0;
            foreach($data['result'] as $val){
                //1.保存子公司 厂家
                $add['name'] = $val['name'];
                $add['first_char'] = strtoupper($val['initial']);
                $add['icon'] = $item['icon'];
                $add['parentid'] = $brand_id;
                $more['orgComId'] = $val['id'];
                $more['orgParentId'] = $val['parentid'];
                $more['fullname'] = $val['fullname'];
                $more['price'] = $val['price'];
                $add['more'] = json_encode($more);
                $insert_id = $goodsBrandModel->allowField(true)->insertGetId($add);
                if(  $insert_id && !empty($val['carlist']) ){
                    // 2. 保存车系 数据
                    foreach($val['carlist'] as $v){
                        $add1['name'] = $v['name'];
                        $add1['brand_id'] = $brand_id; //品牌id
                        $add1['brand_name'] = $item['name']; //品牌名称
                        $add1['brand_sub_id'] = $insert_id; //子品牌公司 id
                        $add1['brand_sub_name'] = $val['name']; //子品牌公司 名称
                        $add1['description'] = '';
                        $add1['keyword'] = $item['name'].','.$val['name'].' '.$v['name'];
                        $add1['level'] = 0;
                        $add1['max_price'] = 0;
                        $add1['min_price'] = 0;
                        $all_car_style_price = [];
                        if(isset($v['list']) && $v['list']){
                            // 保存车型
                            foreach($v['list'] as $vv){
                                $add2['series_id'] = (int)$vv['parentid'];
                                $add2['style_id'] = (int)$vv['id'];
                                $add2['name'] = $vv['name'];
                                $add2['logo'] = $vv['logo'];
                                $add2['price'] = $vv['price'];
                                $add2['yeartype'] = $vv['yeartype'];
                                $add2['productionstate'] = $vv['productionstate'];
                                $add2['salestate'] = $vv['salestate'];
                                $add2['sizetype'] = $vv['sizetype'];
                                $rs = Db::name('goods_car_style_tmp')->insert($add2);
                                /*$sql = Db::name()->getLastSql();
                                echo $sql;die;*/
                                $all_car_style_price[] = floatval($vv['price']);
                            }
                            sort($all_car_style_price,SORT_NUMERIC);
                        }

                        $add1['max_price'] = end($all_car_style_price);
                        reset($all_car_style_price);
                        $add1['min_price'] = current($all_car_style_price);
                        $more['orgSeriesId'] = $v['id'];
                        $more['orgBrandSubId'] = $v['parentid'];
                        $more['fullname'] = $v['fullname'];
                        if(!empty($v['logo'])){
                            $spider->downloadImage($v['logo']);
                            $img = $spider->getRealSavePath();
                            $filename = basename($img);
                            $filenameStr = 'goods/'.date('Ymd').'/'.$filename;
                            $add1['example_img'] = $filenameStr;
                        }else{
                            $add1['example_img'] = '';
                        }
                        $add1['more'] = json_encode($more);
                        $rs = $goodsSeriesModel->allowField(true)->insert($add1);
                        if($rs){
                            $index++;
                        }
                    }
                }
            }
            $res[$item['name']] = $index;
            bb($res);
            bb($error_brand);
            sleep(2);
        }

        $url = "http://www.txqc.com/admin/admin_tool/loadCarSeries2?b_id={$brand_id}";
        echo "<script type='text/javascript'>";
        echo "window.location.href='$url'";
        echo "</script>";

    }



    /**
     * 导入品牌
     *
     */
    public function loadCarSeries(){
        set_time_limit(0);
        $res = [];
        $goodsBrandModel = new GoodsBrandModel();
        $brands =  $goodsBrandModel->where(['delete_time'=>0])->select();
        $brands =  $brands->toArray();
        $goodsSeriesModel = new GoodsCarSeriesModel();
        $spider = new  \spider\SpiderImg('download/images/series');
        foreach($brands as $item){
            $brand_id = (int)$item['id'];
            $more = \Qiniu\json_decode($item['more'],true);
            $org_brand_id  = (int)$more['orgBrandId'];
            $url = 'http://parallel-home-web.maiche.com/api/web/series-web/get-series-list-by-brand.htm?brandId='.$org_brand_id.'&pageSize=100';
            $data =  httpRequest($url);
            $data = \Qiniu\json_decode($data,true);
            $index = 0;
            foreach($data['data'] as $val){
                $add['name'] = $val['name'];
                $add['max_price'] = $val['maxPrice'];
                $add['min_price'] = $val['minPrice'];
                $add['brand_id'] = $brand_id;
                $add['description'] = htmlspecialchars($val['comment'],ENT_QUOTES);
                $add['keyword'] = $val['seriesKeyword'];
                $add['level'] = $val['type'];
                $more['orgSeriesId'] = $val['id'];
                $more['orgBrandId'] = $val['brandId'];
                $add['more'] = json_encode($more);

                $spider->downloadImage($val['logoUrl']);
                $img = $spider->getRealSavePath();
                $filename = basename($img);
                $filenameStr = 'goods/'.date('Ymd').'/'.$filename;
                $add['example_img'] = $filenameStr;
                $rs = $goodsSeriesModel->allowField(true)->insert($add);
                if($rs){
                    $index++;
                }
            }
            $res[$item['name']] = $index;
        }
        bb($res);
    }
    /**
     * 导入车型
     *
     */
    public function loadCarStyle(){
        set_time_limit(0);
        $res = [];
        /*$goodsBrandModel = new GoodsBrandModel();
        $brands =  $goodsBrandModel->where(['delete_time'=>0])->select();
        $brands =  $brands->toArray();*/
        $gauge_id_config = ['国产'=>0,'中规'=>1,'美规'=>2,'中东'=>3,'欧规'=>4,'加规'=>5,'墨规'=>6];

        $goodsSeriesModel = new GoodsCarSeriesModel();
        $series = $goodsSeriesModel->where(['delete_time'=>0])->select();
        $series_list =  $series->toArray();
        $styleModel = new GoodsCarStyleModel();
        foreach($series_list as $item){
            $series_id = (int)$item['id'];
            $more = \Qiniu\json_decode($item['more'],true);
            $org_series_id  = (int)$more['orgSeriesId'];
            $url = 'http://parallel-home-web.maiche.com/api/web/model-web/get-model-list-by-condition.htm?seriesId='.$org_series_id;
            $data =  httpRequest($url);
            $data = \Qiniu\json_decode($data,true);
            $index = 0;
            if(empty($data['data']['itemList'])) continue;
            foreach($data['data']['itemList'] as $val){
                $add['series_id'] = $series_id;
                $add['name'] = $this->_str_re($val['name']);
                $add['brand_id'] = $item['brand_id'];
                $add['factory_price'] = $val['maxPrice'];
                $add['description'] = $val['showName'];
                $add['year'] = $val['year'];
                $add['gauge_id'] = $val['spec']?(int)$gauge_id_config[$val['spec']]:1; //车规
                $more['orgSeriesId'] = $val['seriesId'];
                $add['more'] = json_encode($more);
                $rs = $styleModel->allowField(true)->insert($add);
                if($rs){
                    $index++;
                }
            }
            $res[$val['seriesName']] = $index;
        }
        bb($res);
    }


    /**
     *
     * 导入车型配置参数
     */
    public function loadCarStyleConfig(){
        die;
        header('Content-type:text/html;charset=utf-8');
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        error_reporting(E_ALL);
        $prev_style_id = 0;
        ob_end_clean();
        while(true){
           $goodsCarStyleModel = new GoodsCarStyleModel();
           $data =  $goodsCarStyleModel->where(['delete_time'=>0,'is_update'=>0,'salestate'=>1,'id'=>['gt',$prev_style_id]])->order('id asc')->limit(1)->find();
           if(empty($data)) {
                $this->_flush('数据刷取完成');
               break;
           }
           $style_id = (int)$data['id'];
           $org_car_style_id = (int)$data['org_style_id'];
          /* $ck_key = 'ck_a_li_brand_style_config_data_'.$org_car_style_id;
           $cache = Cache::get($ck_key);
           if(empty($cache)){*/
               $url = 'https://jisucxdq.market.alicloudapi.com/car/detail?carid='.$org_car_style_id;
               $rs = $this->_requestAli($url);
              /* if(!empty($rs)){
                   Cache::set($ck_key,$rs,86400*30);
               }*/
               $cache = $rs;
           //}
           $config_data = \Qiniu\json_decode($cache,true);
           if(!empty($config_data)){
               $this->_flush($org_car_style_id.'数据获取成功');
               $save['style_id']= $org_car_style_id;
               $save['new_style_id']= (int)$style_id;
               $save['data_json']= json_encode($config_data,JSON_UNESCAPED_UNICODE);
               $rs = Db::name('goods_car_style_config_temp')->insert($save);
               if(is_numeric($rs)){
                   $goodsCarStyleModel->where(['id'=>$style_id])->update(['is_update'=>1]);
                   $this->_flush($org_car_style_id.'数据保存成功');
                   unset($save,$config_data);
               }else{
                   $this->_flush($org_car_style_id.'数据保存失败');
               }
           }
            //sleep(1);
           $prev_style_id = $style_id;
       }
    }

    public function loadCarConfigCategory(){
        header('Content-type:text/html;charset=utf-8');
        set_time_limit(0);
        ob_end_clean();
        $json =<<<EOF
{
	"result": {
		"id": "42902",
		"basic-基本信息": {
			"price": "厂家指导价",
			"saleprice": "商家报价",
			"sizetype":"车型级别",
			"yeartype":"上市时间",
			"warrantypolicy": "保修政策",
			"vechiletax": "车船税减免",
			"comfuelconsumption": "综合工况油耗(L/100km)",
			"userfuelconsumption": "网友油耗(L/100km)",
			"officialaccelerationtime100": "官方0-100公里加速时间(s)",
			"testaccelerationtime100": "实测0-100公里加速时间(s)",
			"maxspeed": "最高车速(km/h)",
			"seatnum": "乘车人数(区间)(个)",
			"accelerationtime100":"加速时间(0-100km/h)(s)",
			"brakingdistance":"制动距离(100-0km/h)(m)"
		},
		"body-车身尺寸": {
			"color": "车身颜色",
			"len": "车长(mm)",
			"width": "车宽(mm)",
			"height": "车高(mm)",
			"wheelbase": "轴距(mm)",
			"fronttrack": "前轮距(mm)",
			"reartrack": "后轮距(mm)",
			"weight": "整车重量(kg)",
			"fullweight": "满载重量(kg)",
			"mingroundclearance": "最小离地间隙(mm)",
			"approachangle": "接地角(°)",
			"departureangle": "离去角(°)",
			"luggagevolume": "行李箱容积(L)",
			"luggagemode": "行李箱盖开合方式",
			"luggageopenmode": "行李箱打开方式",
			"inductionluggage": "感应行李箱",
			"doornum": "车门个数",
			"tooftype": "车顶形式",
			"hoodtype": "车篷款式",
			"roofluggagerack": "车顶行李箱架",
			"sportpackage": "运动外观套件"
		},
		"engine-动力系统": {
			"position": "发动机位置",
			"model": "发动机型号",
			"displacement": "排量(L)",
			"displacementml": "排量(mL)",
			"intakeform": "进气形式",
			"cylinderarrangetype": "气缸排列型式",
			"cylindernum": "气缸数(个)",
			"valvetrain": "每缸气门个数(个)",
			"valvestructure": "气门结构",
			"compressionratio": "压缩比",
			"bore": "缸径(mm)",
			"stroke": "行程(mm)",
			"maxhorsepower": "最大马力(Ps)",
			"maxpower": "最大功率(kW)",
			"maxpowerspeed": "最大功率转速(rpm)",
			"maxtorque": "最大扭矩(Nm)",
			"maxtorquespeed": "最大扭矩转速(rpm)",
			"fueltype": "燃料类型",
			"fuelgrade": "燃油标号",
			"fuelmethod": "供油方式",
			"fueltankcapacity": "燃油箱容积(L)",
			"cylinderheadmaterial": "缸盖材料",
			"cylinderbodymaterial": "缸体材料",
			"environmentalstandards": "环保标准",
			"startstopsystem": "启停系统",
			"gearbox": "变数箱",
			"shiftpaddles": "换挡拨片"
		},
		"chassisbrake-底盘制动": {
			"bodystructure": "车体结构",
			"powersteering": "转向助力",
			"frontbraketype": "前制动类型",
			"rearbraketype": "后制动类型",
			"parkingbraketype": "驻车制动类型",
			"drivemode": "驱动方式",
			"airsuspension": "空气悬挂",
			"adjustablesuspension": "可调悬挂",
			"frontsuspensiontype": "前悬挂类型",
			"rearsuspensiontype": "后悬挂类型",
			"centerdifferentiallock": "中央差速器锁"
		},
		"safe-安全配置": {
			"airbagdrivingposition": "驾驶位安全气囊",
			"airbagfrontpassenger": "副驾驶位安全气囊",
			"airbagfrontside": "前排侧安全气囊",
			"airbagfronthead": "前排头部安全气囊(气帘)",
			"airbagknee": "膝部气囊",
			"airbagrearside": "后排侧安全气囊",
			"airbagrearhead": "后排头部安全气囊(气帘)",
			"safetybeltprompt": "安全带未系提示",
			"safetybeltlimiting": "安全带限力功能",
			"safetybeltpretightening": "安全带预收紧功能",
			"frontsafetybeltadjustment": "前安全带调节",
			"rearsafetybelt": "后安全带",
			"tirepressuremonitoring": "胎压监测装置",
			"zeropressurecontinued": "零压续航",
			"centrallocking": "中控门锁",
			"childlock": "儿童锁",
			"remotekey": "遥控钥匙",
			"keylessentry": "无钥匙进入系统",
			"keylessstart": "无钥匙启动系统",
			"engineantitheft": "发动机电子防盗"
		},
		"wheel-车轮信息": {
			"fronttiresize": "前轮胎规格",
			"reartiresize": "后轮胎规格",
			"sparetiretype": "备胎类型",
			"hubmaterial": "轮毂材料"
		},
		"drivingauxiliary-行车辅助": {
			"abs": "刹车防抱死(ABS)",
			"ebd": "电子制动分配系统(EBD)",
			"brakeassist": "刹车辅助",
			"tractioncontrol": "牵引力控制",
			"esp": "动态稳定控制系统(ESP)",
			"eps": "随速助力转向调节(EPS)",
			"automaticparking": "自动驻车",
			"hillstartassist": "上坡辅助",
			"hilldescent": "陡坡缓降",
			"frontparkingradar": "泊车雷达(车前)",
			"reversingradar": "倒车雷达(车后)",
			"reverseimage": "倒车影像",
			"panoramiccamera": "全景摄像头",
			"cruisecontrol": "定速巡航",
			"adaptivecruise": "自适应巡航",
			"gps": "GPS导航系统",
			"automaticparkingintoplace": "自动泊车入位",
			"ldws": "车道偏离预警系统",
			"activebraking": "主动刹车/主动安全系统",
			"integralactivesteering": "整体主动转向系统",
			"nightvisionsystem": "夜视系统",
			"blindspotdetection": "盲点监测"
		},
		"doormirror-门窗/后视镜": {
			"openstyle": "开门方式",
			"electricwindow": "电动车窗",
			"uvinterceptingglass": "防紫外线/隔热玻璃",
			"privacyglass": "隐私玻璃",
			"antipinchwindow": "电动窗防夹功能",
			"skylightopeningmode": "天窗开合方式",
			"skylightstype": "天窗形式",
			"rearwindowsunshade": "后窗遮阳帘",
			"rearsidesunshade": "后排侧遮阳帘",
			"rearwiper": "后雨刷器",
			"sensingwiper": "感应雨刷",
			"electricpulldoor": "电动吸合门",
			"rearmirrorwithturnlamp": "后视镜带侧转向灯",
			"externalmirrormemory": "外后视镜记忆功能",
			"externalmirrorheating": "外后视镜加热功能",
			"externalmirrorfolding": "外后视镜电动折叠功能",
			"externalmirroradjustment": "外后视镜电动调节",
			"rearviewmirrorantiglare": "内后视镜防眩目功能",
			"sunvisormirror": "遮阳板化妆镜"
		},
		"light-灯光配置": {
			"headlighttype": "前大灯类型",
			"optionalheadlighttype": "选配前大灯类型",
			"headlightautomaticopen": "前大灯自动开闭",
			"headlightautomaticclean": "前大灯自动清洗功能",
			"headlightdelayoff": "前大灯延时关闭",
			"headlightdynamicsteering": "前大灯随动转向",
			"headlightilluminationadjustment": "前大灯照射范围调整",
			"headlightdimming": "会车前灯防炫目功能",
			"frontfoglight": "前雾灯",
			"readinglight": "阅读灯",
			"interiorairlight": "车内氛围灯",
			"daytimerunninglight": "日间行车灯",
			"ledtaillight": "LED尾灯",
			"lightsteeringassist": "转向辅助灯"
		},
		"internalconfig-内部配置": {
			"steeringwheelbeforeadjustment": "方向盘前后调节",
			"steeringwheelupadjustment": "方向盘上下调节",
			"steeringwheeladjustmentmode": "方向盘调节方式",
			"steeringwheelmemory": "方向盘记忆设置",
			"steeringwheelmaterial": "方向盘表面材料",
			"steeringwheelmultifunction": "多功能方向盘",
			"steeringwheelheating": "方向盘加热",
			"computerscreen": "行车电脑显示屏",
			"huddisplay": "HUD抬头数字显示",
			"interiorcolor": "内饰颜色",
			"rearcupholder": "后排杯架",
			"supplyvoltage": "车内车源电压",
			"airconditioningcontrolmode": "空调控制方式",
			"tempzonecontrol": "温度分区控制",
			"rearairconditioning": "后排独立空调",
			"reardischargeoutlet": "后排出风口",
			"airconditioning": "空气调节/花粉过滤",
			"airpurifyingdevice": "车内空气净化装置",
			"carrefrigerator": "车载冰箱"
		},
		"seat-座椅配置": {
			"sportseat": "运动座椅",
			"seatmaterial": "座椅材料",
			"seatheightadjustment": "座椅高度调节",
			"driverseatadjustmentmode": "驾驶座座椅调节方式",
			"auxiliaryseatadjustmentmode": "副驾驶座座椅调节方式",
			"driverseatlumbarsupportadjustment": "驾驶座腰部支撑调节",
			"driverseatshouldersupportadjustment": "驾驶座肩部支撑调节",
			"frontseatheadrestadjustment": "前座椅头枕调节",
			"rearseatadjustmentmode": "后排座椅调节方式",
			"rearseatreclineproportion": "后排座位放到比例",
			"rearseatangleadjustment": "后排座椅角度调节",
			"frontseatcenterarmrest": "前座中央扶手",
			"rearseatcenterarmrest": "后座中央扶手",
			"seatventilation": "座椅通风",
			"seatheating": "座椅加热",
			"seatmassage": "座椅按摩功能",
			"electricseatmemory": "电动座椅记忆",
			"childseatfixdevice": "儿童安全座椅固定装置",
			"thirdrowseat": "第三排座椅"
		},
		"entcom-信息娱乐": {
			"locationservice": "定位互动服务",
			"bluetooth": "蓝牙系统",
			"externalaudiointerface": "外接音源接口",
			"builtinharddisk": "内置硬盘",
			"cartv": "车载电视",
			"speakernum": "扬声器数量",
			"audiobrand": "音响品牌",
			"dvd": "DVD",
			"cd": "CD",
			"consolelcdscreen": "中控台液晶",
			"rearlcdscreen": "后排液晶屏"
		}
	}
}
EOF;
      //  echo '<pre>';
       // print_r(\Qiniu\json_decode($json,true));

        $config_data = \Qiniu\json_decode($json,true);
        foreach($config_data['result'] as $key=>$val){
            if( $key == 'id'){
                continue;
            }
            $config_arr =  explode('-',$key);
            $add['name'] = $config_arr[1];
            $add['field_name'] = $config_arr[0];
            $cate_id = Db::name('goods_car_config_category')->insertGetId($add);
            if($cate_id){
                // 添加配置项
                foreach($val as $k=>$v){
                    $save['cate_id'] = $cate_id;
                    $save['config_name'] = trim($v);
                    $save['config_input_type'] = 0;
                    $save['config_values'] = '';
                    $save['description'] = $save['config_name'];
                    $save['config_field'] = trim($k);
                    $configItems = new GoodsCarConfigItemsModel();
                    $rs = $configItems->addConfigItems($save);
                    if($rs){
                       $this->_flush($config_arr[1].'-'.$v.' 添加成功！');
                    }else{
                        $this->_flush($config_arr[1].'-'.$v.' 添加失败！');
                    }
                }
            }
        }
    }

    /**
     *
     * 创建车型参数配置数据表
     */
    public function createCarStyleConfigTable(){
        /*Cache::rm(MkeyService::getMkey(MkeyService::CONFIGLIST,0));
        die;*/
        $sql = "DROP TABLE IF EXISTS `tx_goods_car_config_vals`;";
        Db::execute($sql);
        $configItems = new GoodsCarConfigItemsModel();
        $items = $configItems->getConfigList();
        if($items){
            $sql = "CREATE TABLE `tx_goods_car_config_vals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `styleid` int(11) NOT NULL DEFAULT  0 COMMENT '车型id',";
            foreach($items as $val){
                $sql.= "`field_{$val['config_id']}` varchar(64) DEFAULT NULL COMMENT '{$val['config_name']}',";

            }
            $sql .= "PRIMARY KEY (`id`),
  UNIQUE KEY `styleid` (`styleid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;";
            try{
                Db::execute($sql);
                $this->_flush('创建成功');
            }catch(\Exception $e){
                $this->_flush($e);
            }
        }
    }


    /**
     *
     * 刷每个车型的配置
     *
     * @return bool
     * @throws Exception
     */
    public function loadEveryCarStyleConfigs(){
        header('Content-type:text/html;charset=utf-8');
        set_time_limit(0);
        ob_end_clean();
        $configItems = new GoodsCarConfigItemsModel();
        $items = $configItems->getConfigList();
        $error = [];
        if(empty($items)){
            return false;
        }
        $new_items = [];
        foreach($items as $item){
            $new_items[$item['config_field']] = $item['config_id'];
        }
        $base = ['basic','body','engine','gearbox','chassisbrake','safe','wheel','drivingauxiliary','doormirror','light','internalconfig','seat','entcom','aircondrefrigerator','actualtest'];
        $start = 0;
        while(true){
            $data = Db::name('goods_car_style_config_temp')->where(['id'=>['gt',$start],'is_update'=>0])->order( 'id asc ')->limit(1)->select();
            if(empty($data)) break;
            if($data){
                $config_detail = $data->toArray();
                if(empty($config_detail)) break;
                if($config_detail){
                    $adds = [];
                    foreach($config_detail as $config_item){
                        $config = \Qiniu\json_decode($config_item['data_json'],true);
                        if(empty($config['result'])){
                            $error[] = $config_item['new_style_id'];
                            continue;
                        }
                        $add = [];
                        $add['styleid'] = $config_item['new_style_id'];
                        foreach($config['result'] as $key=>$val){
                            if(is_array($val)){
                                if(in_array($key,$base)){
                                    foreach($val as $k=>$v){
                                        if(isset($new_items[$k]) && !empty($new_items[$k])){
                                            $field = 'field_'.intval($new_items[$k]);
                                            $add[$field] = empty($v)?'-':$v;
                                        }
                                    }
                                }
                            }else{
                                if(isset($new_items[$key]) && !empty($new_items[$key])){
                                    $field = 'field_'.intval($new_items[$key]);
                                    $add[$field] =  empty($val)?'-':$val;
                                }
                            }
                        }
                        $adds[] = $add;
                    }
                    $rs = Db::name('goods_car_config_vals')->insertAll($adds);

                    if($rs){
                        $this->_flush(count($adds).'插入成功');
                    Db::name('goods_car_style_config_temp')->where(['id'=>['in',get_arr_column($config_detail,'id')]])->update(['is_update'=>1]);
                        $end_arr = end($config_detail);
                        $start = $end_arr['id'];
                    }else{
                        $this->_flush(import(',',get_arr_column($config_detail,'id')).' 更新失败');
                        break;
                    }
                }

            }
        }

        $this->_flush('程序结束');
        if(!empty($error)){
            echo '<hr>';
            echo '<pre>';
            print_r($error);
        }

    }






    private function _flush($str){
        //ob_end_clean();
        echo str_repeat(" ", 1024 * 2);//人为将缓冲数据扩充到2k
        echo $str . '<br />';
        flush();
       // ob_flush();
        //sleep(1);

    }
    private function _str_re($name){

        return str_replace(['美规','中东','欧规','加规','墨规'],'',$name);

    }

    public function yt_thumb_pro_url($img,$width=350,$height=350)
    {
        $article=$img;
        if (strpos($article, "http") === 0) {
            return redirect($article);
        } else if (strpos($article, "/") === 0) {
            return redirect(cmf_get_domain() . $article);
        }
        $avatarPath = "./upload/" . $article;
        $fileArr = pathinfo($avatarPath);
        $filename=$fileArr['dirname'].$fileArr['filename']."-".$width."-".$height.".jpg";
        if(file_exists($filename))
        {
            $url=$filename;
        }else{
            $avatarImg = Image::open($avatarPath);
            $avatarImg->thumb($width, $height)->save($filename);
            $url=$filename;
        }
        $request = request();
        $url=$request->domain().$url;
        return redirect($url);
    }


    private function _requestAli($url,$method='GET'){

        //$host = "https://jisucxdq.market.alicloudapi.com";
        //$path = "/car/brand";
       // $method = "GET";
        $appcode = "e9293d7dc887403d84c6a4cbb799b0ff";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
       // $querys = "";
        //$bodys = "";
       // $url = $host . $path;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        // if (1 == strpos("$".$host, "https://"))
        // {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        // }
        $content= curl_exec($curl);

        return $content;
    }


    public function demo(){

        $host = "https://jisucxdq.market.alicloudapi.com";
        $path = "/car/brand";
        $method = "GET";
        $appcode = "e9293d7dc887403d84c6a4cbb799b0ff";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        $querys = "";
        $bodys = "";
        $url = $host . $path;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
       // if (1 == strpos("$".$host, "https://"))
       // {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
       // }
        $content= curl_exec($curl);
        print_r(\Qiniu\json_decode($content,true));


    }

}