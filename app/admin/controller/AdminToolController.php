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
use app\goods\model\GoodsCarSeriesModel;
use app\goods\model\GoodsCarStyleModel;
use cmf\controller\AdminBaseController;
use think\Db;
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


    /**
     * 添加幻灯片
     * @adminMenu(
     *     'name'   => '添加幻灯片',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加幻灯片',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        return $this->fetch();
    }

    /**
     * 添加幻灯片提交
     * @adminMenu(
     *     'name'   => '添加幻灯片提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加幻灯片提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $data           = $this->request->param();
        $slidePostModel = new SlideModel();
        $result         = $slidePostModel->validate(true)->save($data);
        if ($result === false) {
            $this->error($slidePostModel->getError());
        }
        $this->success("添加成功！", url("slide/index"));
    }




    /**
     * 编辑幻灯片
     * @adminMenu(
     *     'name'   => '编辑幻灯片',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑幻灯片',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id             = $this->request->param('id');
        $slidePostModel = new SlideModel();
        $result         = $slidePostModel->where('id', $id)->find();
        $this->assign('result', $result);
        return $this->fetch();
    }

    /**
     * 编辑幻灯片提交
     * @adminMenu(
     *     'name'   => '编辑幻灯片提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑幻灯片提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        $data           = $this->request->param();
        $slidePostModel = new SlideModel();
        $result         = $slidePostModel->validate(true)->save($data, ['id' => $data['id']]);
        if ($result === false) {
            $this->error($slidePostModel->getError());
        }
        $this->success("保存成功！", url("slide/index"));
    }

    /**
     * 删除幻灯片
     * @adminMenu(
     *     'name'   => '删除幻灯片',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除幻灯片',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id             = $this->request->param('id', 0, 'intval');
        $slidePostModel = new SlideModel();
        $result       = $slidePostModel->where(['id' => $id])->find();
        if (empty($result)){
            $this->error('幻灯片不存在!');
        }

        //如果存在页面。则不能删除。
        $slidePostCount = Db::name('slide_item')->where('slide_id', $id)->count();
        if ($slidePostCount > 0) {
            $this->error('此幻灯片有页面无法删除!');
        }

        $data         = [
            'object_id'   => $id,
            'create_time' => time(),
            'table_name'  => 'slide',
            'name'        => $result['name']
        ];

        $resultSlide = $slidePostModel->save(['delete_time' => time()], ['id' => $id]);
        if ($resultSlide) {
            Db::name('recycleBin')->insert($data);
        }
        $this->success("删除成功！", url("slide/index"));
    }

}