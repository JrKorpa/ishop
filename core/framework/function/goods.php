<?php
/**
 * 商品图片统一调用函数
 *
 *
 * @提供技术支持 授权请购买正版授权
 * @license    http://官网
 * @link       交流群号：官网群

 */

defined('INTELLIGENT_SYS') or exit('Access Invalid!');

/**
 * 取得商品缩略图的完整URL路径，接收商品信息数组，返回所需的商品缩略图的完整URL
 *
 * @param array $goods 商品信息数组
 * @param string $type 缩略图类型  值为60,240,360,1280
 * @return string
 */
function thumb($goods = array(), $type = ''){
    $type_array = explode(',_', ltrim(GOODS_IMAGES_EXT, '_'));
    if (!in_array($type, $type_array)) {
        $type = '240';
    }
    if (empty($goods)){
        return UPLOAD_SITE_URL.'/'.defaultGoodsImage($type);
    }
    if (array_key_exists('apic_cover', $goods)) {
        $goods['goods_image'] = $goods['apic_cover'];
    }
    if (empty($goods['goods_image'])) {
        return UPLOAD_SITE_URL.'/'.defaultGoodsImage($type);
    }
    $search_array = explode(',', GOODS_IMAGES_EXT);
    $file = str_ireplace($search_array,'',$goods['goods_image']);
    $fname = basename($file);
    //取店铺ID
    if (preg_match('/^(\d+_)/',$fname)){
        $store_id = substr($fname,0,strpos($fname,'_'));
    }else{
        $store_id = $goods['store_id'];
    }

    if (!C('oss.open')) {
        $file = $type == '' ? $file : str_ireplace('.', '_' . $type . '.', $file);
        if (!file_exists(BASE_UPLOAD_PATH.'/'.ATTACH_GOODS.'/'.$store_id.'/'.$file)){
            return UPLOAD_SITE_URL.'/'.defaultGoodsImage($type);
        }
        $thumb_host = UPLOAD_SITE_URL.'/'.ATTACH_GOODS;
        return $thumb_host.'/'.$store_id.'/'.$file;        
    } else {
        return C('oss.img_url'). '/' . ATTACH_GOODS . '/' . $store_id . '/' . $file . '@!product-' . $type;
    }


}
/**
 * 取得商品缩略图的完整URL路径，接收图片名称与店铺ID
 *
 * @param string $file 图片名称
 * @param string $type 缩略图尺寸类型，值为60,240,360,1280
 * @param mixed $store_id 店铺ID 如果传入，则返回图片完整URL,如果为假，返回系统默认图
 * @return string
 */
function cthumb($file, $type = '', $store_id = false) {
    $type_array = explode(',_', ltrim(GOODS_IMAGES_EXT, '_'));
    if (!in_array($type, $type_array)) {
        $type = '240';
    }
    if (empty($file)) {
        return UPLOAD_SITE_URL . '/' . defaultGoodsImage ( $type );
    }
    $search_array = explode(',', GOODS_IMAGES_EXT);
    $file = str_ireplace($search_array,'',$file);
    $fname = basename($file);
    // 取店铺ID
    if ($store_id === false || !is_numeric($store_id)) {
        $store_id = substr ( $fname, 0, strpos ( $fname, '_' ) );
    }
    if (!C('oss.open')) {
        // 本地存储时，增加判断文件是否存在，用默认图代替
        if ( !file_exists(BASE_UPLOAD_PATH . '/' . ATTACH_GOODS . '/' . $store_id . '/' . ($type == '' ? $file : str_ireplace('.', '_' . $type . '.', $file)) )) {
            return UPLOAD_SITE_URL.'/'.defaultGoodsImage($type);
        }
        $thumb_host = UPLOAD_SITE_URL . '/' . ATTACH_GOODS;
        return $thumb_host . '/' . $store_id . '/' . ($type == '' ? $file : str_ireplace('.', '_' . $type . '.', $file));        
    } else {
        return C('oss.img_url'). '/' . ATTACH_GOODS . '/' . $store_id . '/' . $file . '@!product-' . $type;
    }

}
/**
 * 商品二维码
 * @param array $goods_info
 * @return string
 */
function goodsQRCode($goods_info) {
    if (!file_exists(BASE_UPLOAD_PATH. '/' . ATTACH_STORE . '/' . $goods_info['store_id'] . '/' . $goods_info['goods_id'] . '.png' )) {
        return UPLOAD_SITE_URL.DS.ATTACH_STORE.DS.'default_qrcode.png';
    }
    return UPLOAD_SITE_URL.DS.ATTACH_STORE.DS.$goods_info['store_id'].DS.$goods_info['goods_id'].'.png';
}

/**
 * 取得抢购缩略图的完整URL路径
 *
 * @param string $imgurl 商品名称
 * @param string $type 缩略图类型  值为small,mid,max
 * @return string
 */
function gthumb($image_name = '', $type = ''){
	if (!in_array($type, array('small','mid','max'))) $type = 'small';
	if (empty($image_name)){
		return UPLOAD_SITE_URL.'/'.defaultGoodsImage('240');
	}
    list($base_name, $ext) = explode('.', $image_name);
    list($store_id) = explode('_', $base_name);
    $file_path = ATTACH_GROUPBUY.DS.$store_id.DS.$base_name.'_'.$type.'.'.$ext;
    if(!file_exists(BASE_UPLOAD_PATH.DS.$file_path)) {
		return UPLOAD_SITE_URL.'/'.defaultGoodsImage('240');
	}
	return UPLOAD_SITE_URL.DS.$file_path;
}
/**
 * 取得买家缩略图的完整URL路径
 *
 * @param string $imgurl 商品名称
 * @param string $type 缩略图类型  值为240,1024
 * @return string
 */
function snsThumb($image_name = '', $type = ''){
	if (!in_array($type, array('240','1024'))) $type = '240';
	if (empty($image_name)){
		return UPLOAD_SITE_URL.'/'.defaultGoodsImage('240');
    }

    if(strpos($image_name, '/')) {
        $image = explode('/', $image_name);
        $image = end($image);
    } else {
        $image = $image_name;
    }

    list($member_id) = explode('_', $image);
    $file_path = ATTACH_MALBUM.DS.$member_id.DS.str_ireplace('.', '_'.$type.'.', $image_name);
    if(!file_exists(BASE_UPLOAD_PATH.DS.$file_path)) {
		return UPLOAD_SITE_URL.'/'.defaultGoodsImage('240');
	}
	return UPLOAD_SITE_URL.DS.$file_path;
}



/**
 * 取得积分商品缩略图的完整URL路径
 *
 * @param string $imgurl 商品名称
 * @param string $type 缩略图类型  值为small
 * @return string
 */
function pointprodThumb($image_name = '', $type = ''){
	if (!in_array($type, array('small','mid'))) $type = '';
	if (empty($image_name)){
		return UPLOAD_SITE_URL.'/'.defaultGoodsImage('240');
    }

    if($type) {
        $file_path = ATTACH_POINTPROD.DS.str_ireplace('.', '_'.$type.'.', $image_name);
    } else {
        $file_path = ATTACH_POINTPROD.DS.$image_name;
    }
    if(!file_exists(BASE_UPLOAD_PATH.DS.$file_path)) {
		return UPLOAD_SITE_URL.'/'.defaultGoodsImage('240');
	}
	return UPLOAD_SITE_URL.DS.$file_path;
}
/**
 * 取得品牌图片
 *
 * @param string $image_name
 * @return string
 */
function brandImage($image_name = '') {
    if ($image_name != '') {
        return UPLOAD_SITE_URL.'/'.ATTACH_BRAND.'/'.$image_name;
    }
    return UPLOAD_SITE_URL.'/'.ATTACH_COMMON.'/default_brand_image.gif';
}

/**
* 取得订单状态文字输出形式
*
* @param array $order_info 订单数组
* @return string $order_state 描述输出
*/
function orderState($order_info) {
    switch ($order_info['order_state']) {
        case ORDER_STATE_CANCEL:
            $order_state = '已取消';
        break;
        case ORDER_STATE_TO_CONFIRM:
            $order_state='待审核';
            break;
        case ORDER_STATE_NEW:
            //if ($order_info['chain_code']) {
                //$order_state = '门店付款自提';
            //} else {
                $order_state = '待付款';
            //}
        break;
        case ORDER_STATE_TO_BC:
            $order_state='待布产';
            break;
        case ORDER_STATE_MAKING:
            $order_state='生产中';
            break;
        case ORDER_STATE_TO_SIGN:
            $order_state='待签收';
            break;
        case ORDER_STATE_TOSEND:
            $order_state='待发货';
            break;
        case ORDER_STATE_PAY:
            if ($order_info['chain_code']) {
                $order_state = '待自提';
            } else {
                $order_state = '待发货';
            }
        break;
        case ORDER_STATE_SEND:
            $order_state = '待收货';
        break;
        case ORDER_STATE_SUCCESS:
            $order_state = '交易完成';
        break;
    }
    return $order_state;
}

/**
* 取得订单退款状态文字输出形式
*
* @param array $order_info 订单数组
* @return string $order_state 描述输出
*/
function orderRefundState($order_info) {
    $refund_state = "无退款";
    switch ($order_info['refund_state']) {
        case REFUND_STATW_BU:
            $refund_state = '部分退款';
        break;
        case REFUND_STATW_ALL:
            $refund_state='全部退款';
        break;
    }
    return $refund_state;
}
/**
 * 取得订单支付类型文字输出形式
 *
 * @param array $payment_code
 * @return string
 */
function orderPaymentName($payment_code) {
    return str_replace(
            array('offline','online','ali_native','alipay','tenpay','chinabank','predeposit','wxpay','wx_jsapi','wx_saoma','chain','zhanting','mall_pay','cash','bank_pay','old_to_new','channe_pay'),
            array('货到付款','在线付款','支付宝移动支付','支付宝','财付通','网银在线','站内余额支付','微信支付[客户端]','微信支付[jsapi]','微信支付[扫码]','门店支付','展厅订购','商场代收款','现金','银行转账','以旧换新','跨渠道协作收款'),
            $payment_code);
}


/**
 * 取得订单支付状态文字输出形式
 *
 * @param array $pay_status
 * @return string
 */
function orderPayStatusName($pay_status) {
    return str_replace(
        array(0,ORDER_PAY_TODO,ORDER_PAY_PART,ORDER_PAY_FULL),
        array('待付款','待付款','已付定金','已付款'),
        $pay_status);
}

/**
 * 取得批发销售单销售类型文字输出形式
 *
 * @param array $pay_status
 * @return string
 */
function billPFStatusName($item_type) {
    return str_replace(
        array(BILL_ITEM_TYPE_LS,BILL_ITEM_TYPE_PF,BILL_ITEM_TYPE_WX,BILL_ITEM_TYPE_ZC),
        array('零售','批发','维修','转仓'),
        $item_type);
}

/**
 * 订单生产状态
 * @param $pay_status
 * @return mixed
 */
function orderProductName($order_info) {
    if($order_info["is_xianhuo"]==1){
        return "无需生产";
    }else{
        if($order_info['order_state']==ORDER_STATE_MAKING)
            return "生产中";
        else if($order_info['order_state']>ORDER_STATE_MAKING)
            return "生产完成";
        else
            return "";
    }
}


/**
 * 商品状态文字输出形式
 *
 * @param array $pay_status
 * @return string
 */
function goodsStatusName($pay_status) {
    return str_replace(
        array(1,2,3,4,5,6,7,8,9,10,11,12),
        array('收货中','库存','已销售','盘点中','调拨中','损益中','已报损','返厂中','已返厂','销售中','退货中','作废'),
        $pay_status);
}

/**
 * 取得订单商品销售类型文字输出形式
 *
 * @param array $goods_type
 * @return string 描述输出
 */
function orderGoodsType($goods_type) {
    return str_replace(array(
        '1','2','3','4','5',
        '8', '9',
    ), array(
        '','抢购','限时折扣','优惠套装','赠品',
        '', '换购',
    ), $goods_type);
}
/**
 * 取得结算文字输出形式
 *
 * @param array $bill_state
 * @return string 描述输出
 */
function billState($bill_state) {
    return str_replace(
            array('1','2','3','4'),
            array('已出账','商家已确认','平台已审核','结算完成'),
            $bill_state);
}
/**
 * 代理商商品二维码
 * @param array $goods_info
 * @return string
 */
function goodsFxQRCode($goods_info) {
    if (!file_exists(BASE_UPLOAD_PATH.DS.'agent/goods'.DS.$goods_info['agent_id'].DS.$goods_info['fx_goods_id'].'.png')) {
//         return UPLOAD_SITE_URL.DS.ATTACH_STORE.DS.'default_qrcode.png';
        return '';
    }
//     return UPLOAD_SITE_URL.DS.ATTACH_STORE.DS.$goods_info['store_id'].DS.$goods_info['goods_id'].'.png';
    return UPLOAD_SITE_URL.DS.'agent/goods'.DS.$goods_info['agent_id'].DS.$goods_info['fx_goods_id'].'.png';
}
/**
 * 单据类型
 * @param unknown $bill_info
 */
function billType($bill_info){
    $store_id = $_SESSION['store_id'];
    $company_id = $_SESSION['store_company_id'];
    $bill_type = $bill_info['bill_type'];
    $item_type = $bill_info['item_type'];
    $to_company_id = $bill_info['to_company_id'];
    switch ($bill_type){
        case 'S':
            $bill_type_name = "销售出库单";
            if($item_type == "PF"){
                if($company_id == $to_company_id){
                    $bill_type_name = "进货单";
                }
            }else if($item_type == "WX"){
                $bill_type_name = "维修出库单";
            }
            break;
        case 'D':
            $bill_type_name = "销售退货单";
            if($item_type == "WX"){
                $bill_type_name = "维修入库单";
            }else if($item_type == "PF"){
                if($to_company_id == $company_id ){
                    $bill_type_name = "销售退货单";
                }else{
                    $bill_type_name = "退货返厂单";
                }
                
            }
            break;
        case 'M':
            $bill_type_name = "调拨单";
            if($item_type == "WX"){
                $bill_type_name = "维修调拨单";
            }
            break;
        case 'L':
            $bill_type_name = "进货单";            
            break;
        case 'B':
            $bill_type_name = "退货返厂单";
            break;
        case 'C':
            $bill_type_name = "其他出库单";
            break;
        case 'W':
            $bill_type_name = "盘点单";
            break;
        default:
            $bill_type_name = $bill_type;
            break;
            
    }
    
    return $bill_type_name;
}
/**
 * 单据单头总成本显示
 */
function billChengbenTotal($bill_info, $show_chengben = false){
    if($show_chengben===false){
        $show_chengben = checkSellerLimit("limit_show_goods_chengben");
    }
    if(!$show_chengben) return '/';
    $bill_type = $bill_info['bill_type'];
    $item_type = $bill_info['item_type'];
    $to_company_id = $bill_info['to_company_id'];
    $chengbenTotal = $bill_info['chengben_total'];
    switch ($bill_type){
        case 'S':
            if($item_type == "PF"){
                if($_SESSION['store_company_id'] == $to_company_id){
                     $chengbenTotal = $bill_info['goods_total'];
                }
            }
            break;
        case 'M':
            //$chengbenTotal = '/';//业务说屏蔽掉
            break;  
        case 'D':
            if($item_type == "LS"){
               //$chengbenTotal = "/";
            }
            break;
        default:    
            break;
    }
    return $chengbenTotal;
}

//单据明细成本价管控
function billgoodsChengbenShow($bill_info, $bill_goods, $show_chengben=false){
    if($show_chengben===false){
        $show_chengben = checkSellerLimit("limit_show_goods_chengben");
    }
    if(!$show_chengben) return '/';
    $bill_type = $bill_info['bill_type'];
    $item_type = $bill_info['item_type'];
    $to_company_id = $bill_info['to_company_id'];
    $yuanshichengben = $bill_goods['yuanshichengben'];
    switch ($bill_type){
        case 'S':
            if($item_type == "PF"){
                if($_SESSION['store_company_id'] == $to_company_id){
                     $yuanshichengben = $bill_info['goods_total'];
                }
            }
            break;
        case 'M':
            //$yuanshichengben = '/';//业务说屏蔽掉
            break;  
        case 'D':
            if($item_type == "LS"){
               //$yuanshichengben = "/";
            }
            break;
        default:    
            break;
    }
    return $yuanshichengben;
}

/**
 * 单据主类型 状态
 * @param unknown $bill_info
 */
function billStatus($bill_info){
    //$store_id = $_SESSION['store_id'];
    //$company_id = $_SESSION['store_company_id'];
    //$bill_type = $bill_info['bill_type'];
    $bill_status = $bill_info['bill_status'];
    //$item_type = $bill_info['item_type'];
    //$item_status = $bill_info['item_status'];
    //$from_company_id = $bill_info['from_company_id'];
    //$to_company_id = $bill_info['to_company_id']; 
    
    $status_name = '';   
    switch ($bill_status){
        case 1:
            $status_name = "待审核";                        
            break;
        case 2:
            $status_name = "已审核";            
            break;
        case 3:
            $status_name = "已取消";
            break;
        case 4:
            $status_name = "已签收";
            break;
        default:
            $status_name = $bill_status;
            break;

    }

    return $status_name;
}
/**
 * 镶口 石重匹配验证
 * @param unknown $xiangkou
 * @param unknown $stone
 * @return boolean
 * 选托选钻（成品定制还是按照单独的配置表匹配）镶口25分及以上，按上4下5分匹配石头，镶口25分以下，直接根据镶口大小匹配石头大小
 */
function checkXiangkouStone($xiangkou,$stone) {
    $stone = $stone * 1000;
    $xiangkou = $xiangkou * 1000;
    $stone = intval($stone);
    if($xiangkou <= 250){
        if($xiangkou == $stone){
            return true;
        }
    }else{
        //echo "{$stone} >= ({$xiangkou}-50) &&  {$stone}<=({$xiangkou}+40)";
        if($stone >= ($xiangkou-50) &&  $stone<=($xiangkou+40)){
            return true;
        }
    }
    return false;
}
/**
 * 根据石重匹配镶口
 * @param unknown $cart
 * @return string
 */
function getXiangkouByStone($cart){
    $cart = $cart * 1000;
    $cart = intval($cart);
    if ($cart > 0 && $cart <= 250){
        return array($cart/1000);
    }
    if ($cart<=0 || $cart > 10000){
        return 0;
    }    
    $arr = array();
    for($i = 250;$i <= 10000; $i = $i+50) {
        $arr []= ($i);
    }
    $count = count($arr);
    $xiangkouArr = array();
    for($i = 0; $i <$count; $i ++)
    {   
        $xiangkou = $arr[$i]; 
        if ( $cart >= ($xiangkou-50) && $cart <= $xiangkou+40){
            $xiangkou = sprintf("%.3f",$xiangkou/1000);
            $xiangkouArr[] = $xiangkou;
        }
    }
	return $xiangkouArr;
}
/**
 * 格式化刻字内容
 * @param unknown $kezi
 * @return mixed
 */
function formatKezi($kezi){
    $keziList = array(
        "[&符号]"=>"1.png",
        "[间隔号]"=>"2.png",
        "[空心]"=>"3.png",
        "[实心]"=>"4.png",
        "[小数点]"=>"5.png",
        "[心心相印]"=>"6.png",
        "[一箭穿心]"=>"7.png",
        "[红宝石]"=>"8.png",
    );
    foreach ($keziList as $k=>$v){
        if(strpos($kezi,$k) !==false){
            $title = ltrim(rtrim($k,']'),'[');
            $kezi = str_replace($k,"<img src='/wap/images/face/{$v}' title='{$title}' style='width:18px'/>", $kezi);
        }
    }
    return $kezi;
}
/**
 * 格式化刻字内容
 * @param unknown $kezi
 * @return mixed
 */
function formatGoodsListKezi($goods_list){
    foreach ($goods_list as $k=>$goods){
        $goods['kezi_raw'] = $goods['kezi'];
        $goods['kezi'] = formatKezi($goods['kezi']);
        $goods_list[$k] = $goods;
    }
    return $goods_list;
}

/**
 * 裸钻列表批发价显示
 */
function diamond_pifajia($diamondinfo,$show_chengben=false){
    if($show_chengben===false){
        $show_chengben = checkSellerLimit("limit_show_goods_chengben");
    }
    $pifajia_price = "/";
    if(!$show_chengben) return $pifajia_price;
    //var_dump($diamondinfo);die;
    if(!empty($diamondinfo)){
        $good_type = $diamondinfo['good_type'];
        $pifajia = $diamondinfo['pifajia'];
        $shop_price = $diamondinfo['shop_price'];
        
        if($good_type == 1){
            $pifajia_price = $shop_price;
        }elseif($good_type == 2){
            $pifajia_price = $pifajia;
        }else{

        }
    }
    return $pifajia_price;
}

/**
 * 盘点状态
 * @param unknown $status
 * @return Ambigous <string, unknown>
 */
function pandianStatus($status){
    $status_name = '';
    switch ($status){
        case 1:
            $status_name = "盘亏";
            break;
        case 2:
            $status_name = "盘盈";
            break;
        case 3:
            $status_name = "正常";
            break;
        default:
            $status_name = $bill_status;
            break;    
    }
    
    return $status_name;
}
/**
 * 盘点调整状态
 * @param unknown $status
 * @return string
 */
function pandianAjdust($status){
    $status_name = '';
    switch ($status){
        case 1:
            $status_name = "在途";
            break;
        case 2:
            $status_name = "已销售";
            break;
        default:
            $status_name = "";
            break;
    }
    return $status_name;
}
/**
 * 权限校验
 * @param unknown $perm_key
 * @return boolean
 */
function checkSellerLimit($perm_key){
   if ($_SESSION['seller_is_admin'] !== 1 && $perm_key !== 'seller_center' && $perm_key !== 'seller_logout') {
       return in_array($perm_key, $_SESSION['seller_limits']); 
   }        
   return true;
}
/**
 * 指圈0.5拆分
 * @param unknown $list
 * @return unknown|multitype:
 */
function buildZhiquan($list){
    if(empty($list) || !is_array($list)){
        return $list;
    }
    $newlist = array();
    foreach ($list as $v){
        $newlist[]= $v;
        if(round($v) == $v){
            $newlist[]= $v-0.5;
            $newlist[]= $v+0.5;
        }
    }
    $newlist = array_unique($newlist);//去重  
    asort($newlist);//排序
    $newlist = array_merge($newlist);//重置键值
    return $newlist;
}