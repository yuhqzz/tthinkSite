<?php

// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
+------------------------------------------------------------------------------
 * DirectoryIterator实现类 PHP5以上内置了DirectoryIterator类
+------------------------------------------------------------------------------
 * @category   ORG
 * @package  ORG
 * @subpackage  Io
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
+------------------------------------------------------------------------------
 */
namespace spider;
class SpiderImg {//类定义开始

    protected $save_root_dir = 'images/';
    protected $real_save_path;

    /**
    +----------------------------------------------------------
     * 架构函数
    +----------------------------------------------------------
     */
    function __construct($save_path = 'download/images/') {
        $this->save_root_dir = './upload/'.$save_path;

    }


    public function downloadImage($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $file = curl_exec($ch);
        curl_close($ch);
        $this->_saveAsImage($url, $file);
    }

    public function getRealSavePath(){
        return $this->real_save_path;
    }

    private function _saveAsImage($url, $file)
    {
        $filename = pathinfo($url, PATHINFO_BASENAME);
        $file_ext_arr = explode('.',$filename);
        $file_ext = end($file_ext_arr);
        if(stripos($file_ext,'!') !== false ){
            $file_ext = explode('!',$file_ext);
            $strFileExtension = $file_ext[0];
        }else{
            $strFileExtension = $file_ext;
        }
        $strDate    = date('Ymd');
        $fileSaveName    =   $strDate . '/' . md5(uniqid()) . "." . $strFileExtension;
        $strSaveFilePath = ltrim($this->save_root_dir,'/').'/'. $fileSaveName;
        $strSaveFileDir  = dirname($strSaveFilePath);
        if (!file_exists($strSaveFileDir)) {
            mkdir($strSaveFileDir, 0777, true);
        }
        $resource = fopen($strSaveFilePath, 'a');
        fwrite($resource, $file);
        fclose($resource);
        $this->real_save_path = $strSaveFilePath;
        return ;
    }
}
