<?php
/**
 * 接收微信请求，接收productid和用户的openid等参数，执行（【统一下单API】返回prepay_id交易会话标识
 *
 * 
 * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */
error_reporting(7);
$_GET['act']	= 'payment';
$_GET['op']		= 'wxpay_return';
require_once(dirname(__FILE__).'/../../../index.php');
?>