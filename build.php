<?php
/**
 * Created by PhpStorm.
 * User: yuhq
 * Date: 2018-06-15
 * Time: 13:53
 */

return [
    '__file__'  => ['hello.php','test.php'],
    // 定义index模块的自动生成
    'index'   => [
        '__file__'   => ['tags.php', 'user.php', 'hello.php'],
        '__dir__'    => ['behavior', 'controller', 'model', 'view'],
        'controller' => ['Index', 'Test', 'UserType'],
        'model'      => [],
        'view'       => ['index/index'],
    ],
    // 定义test模块的自动生成
    'test'=>[
        '__dir__'   =>  ['behavior','controller','model','widget'],
        'controller'=>  ['Index','Test','UserType'],
        'model'     =>   ['User','UserType'],
        'view'      =>  ['index/index','index/test'],
    ],
];