<?php
// +----------------------------------------------------------------------
// | goods category model
// +----------------------------------------------------------------------
namespace app\goods\model;

use think\Model;
use tree\Tree;
use cmf\service;
use think\Cache;

class GoodsCategoryModel extends Model
{

    protected $type = [
        'more' => 'array',
    ];
    protected $allow_level = 3;

    /**
     * 生成分类 select树形结构
     * @param int $selectId 需要选中的分类 id
     * @param int $currentCid 需要隐藏的分类 id
     * @return string
     */
    public function adminCategoryTree($selectId = 0, $currentCid = 0)
    {
        $where = ['delete_time' => 0];
        if (!empty($currentCid)) {
            $where['id'] = ['neq', $currentCid];
        }
        $categories = $this->order("list_order ASC")->where($where)->select()->toArray();

        $tree       = new Tree();
        $tree->icon = ['&nbsp;&nbsp;│', '&nbsp;&nbsp;├─', '&nbsp;&nbsp;└─'];
        $tree->nbsp = '&nbsp;&nbsp;';

        $newCategories = [];
        foreach ($categories as $item) {
            $item['selected'] = $selectId == $item['id'] ? "selected" : "";

            array_push($newCategories, $item);
        }
        $tree->init($newCategories);
        $str     = '<option value=\"{$id}\" {$selected}>{$spacer}{$name}</option>';
        $treeStr = $tree->getTree(0, $str);
        return $treeStr;
    }
    public function getCategoryData( $update = false){
        $cache_key = service\MkeyService::getMkey(service\MkeyService::GOODSCATEGORY);
        $list = Cache::get($cache_key);
        if($list && !$update){
            return $list;
        }
        $where = ['delete_time' => 0];
        $list = $this->order("list_order ASC")->where($where)->cache($cache_key,service\MkeyService::DAY,'goodsCategory')->select()->toArray();
        return $list;
    }

    public function getSubCategory( $cid = 0 ,$include_self = false){
        $tree = new  Tree();
        $data = $this->getCategoryData();
        $tree->init($data);
        $childrenIds = $tree->getChildrenId($cid);
        if(empty($childrenIds)){
            return $include_self?intval($cid):[];
        }
        if( $include_self && $childrenIds){
            array_push($childrenIds,intval($cid));
        }
       return $childrenIds;
    }

    /**
     * @param int|array $currentIds
     * @param string $tpl
     * @return string
     */
    public function adminCategoryTableTree($currentIds = 0, $tpl = '')
    {
        $where = ['delete_time' => 0];
        $categories = $this->order("list_order ASC")->where($where)->select()->toArray();

        $tree       = new Tree();
        $tree->icon = ['&nbsp;&nbsp;│', '&nbsp;&nbsp;├─', '&nbsp;&nbsp;└─'];
        $tree->nbsp = '&nbsp;&nbsp;';

        if (!is_array($currentIds)) {
            $currentIds = [$currentIds];
        }

        $newCategories = [];
        foreach ($categories as $item) {
            $item['checked'] = in_array($item['id'], $currentIds) ? "checked" : "";
            /*$item['url']     = cmf_url('portal/List/index', ['id' => $item['id']]);*/
            $item['url'] = 'javascript:void(0)';

            if(count(explode('-',$item['path'])) > $this->allow_level){
                $item['str_action'] = '<a href="' . url("AdminCategory/edit", ["id" => $item['id']]) . '">' . lang('EDIT') . '</a>  <a class="js-ajax-delete" href="' . url("AdminCategory/delete", ["id" => $item['id']]) . '">' . lang('DELETE') . '</a> ';
            }else{
                $item['str_action'] = '<a href="' . url("AdminCategory/add", ["parent" => $item['id']]) . '">添加子分类</a>  <a href="' . url("AdminCategory/edit", ["id" => $item['id']]) . '">' . lang('EDIT') . '</a>  <a class="js-ajax-delete" href="' . url("AdminCategory/delete", ["id" => $item['id']]) . '">' . lang('DELETE') . '</a> ';
            }
            array_push($newCategories, $item);
        }

        $tree->init($newCategories);

        if (empty($tpl)) {
            $tpl = "<tr>
                        <td><input name='list_orders[\$id]' type='text' size='3' value='\$list_order' class='input-order'></td>
                        <td>\$id</td>
                        <td>\$spacer <a href='\$url' target='_blank'>\$name</a></td>
                        <td>\$description</td>
                        <td>\$str_action</td>
                    </tr>";
        }
        $treeStr = $tree->getTree(0, $tpl);

        return $treeStr;
    }

    /**
     * 添加商品分类
     * @param $data
     * @return bool
     */
    public function addCategory($data)
    {
        $result = true;
        self::startTrans();
        try {
            if (!empty($data['more']['thumbnail'])) {
                $data['more']['thumbnail'] = cmf_asset_relative_url($data['more']['thumbnail']);
            }
            $this->allowField(true)->save($data);
            $id = $this->id;
            if (empty($data['parent_id'])) {

                $this->where(['id' => $id])->update(['path' => '0-' . $id]);
            } else {
                $parentPath = $this->where('id', intval($data['parent_id']))->value('path');
                $this->where(['id' => $id])->update(['path' => "$parentPath-$id"]);

            }
            self::commit();
        } catch (\Exception $e) {
            self::rollback();
            $result = false;
        }
        $this->clearCache();
        return $result;
    }

    public function editCategory($data)
    {
        $result = true;

        $id          = intval($data['id']);
        $parentId    = intval($data['parent_id']);
        $oldCategory = $this->where('id', $id)->find();

        if (empty($parentId)) {
            $newPath = '0-' . $id;
        } else {
            $parentPath = $this->where('id', intval($data['parent_id']))->value('path');
            if ($parentPath === false) {
                $newPath = false;
            } else {
                $newPath = "$parentPath-$id";
            }
        }

        if (empty($oldCategory) || empty($newPath)) {
            $result = false;
        } else {

            $data['path'] = $newPath;
            if (!empty($data['more']['thumbnail'])) {
                $data['more']['thumbnail'] = cmf_asset_relative_url($data['more']['thumbnail']);
            }
            $this->isUpdate(true)->allowField(true)->save($data, ['id' => $id]);

            $children = $this->field('id,path')->where('path', 'like', $oldCategory['path'] . "-%")->select();
            if (!$children->isEmpty()) {
                foreach ($children as $child) {
                    $childPath = str_replace($oldCategory['path'] . '-', $newPath . '-', $child['path']);
                    $this->where('id', $child['id'])->update(['path' => $childPath], ['id' => $child['id']]);
                }
            }
        }
        $this->clearCache();
        return $result;
    }


    public function clearCache(){
        $cache_key = service\MkeyService::getMkey(service\MkeyService::GOODSCATEGORY);
        Cache::rm($cache_key);
    }

}