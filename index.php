<?php
/**
 * 入口文件
 * @author 珂兰
 */
if(empty($_SERVER['QUERY_STRING'])){
    header('location:/wap/');
}
$site_url = strtolower('http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/index.php')).'/shop/index.php');
// @header('Location: '.$site_url);
include('shop/index.php');

