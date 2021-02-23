<?php
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
/**
 * 店铺统计
 */
//$lang['stat_validorder_explain']	= "符合以下任何一种条件的订单即为有效订单：1）采用在线支付方式支付并且已付款；2）采用货到付款方式支付并且交易已完成";
$lang['stat_validorder_explain']    = "符合以下条件的订单即为有效订单：1）订单已支付，包含支付定金，按照第一次支付时间统计";