<?php
/**
 * 订单管理
 *
 *
 *
 *
 * @提供技术支持 授权请购买正版授权
 * @license    http://官网
 * @link       交流群号：官网群
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class orderModel extends Model {

    /**
     * 取单条订单信息
     *
     * @param unknown_type $condition
     * @param array $extend 追加返回那些表的信息,如array('order_common','order_goods','store')
     * @return unknown
     */
    public function getOrderInfo($condition = array(), $extend = array(), $fields = '*', $order = '',$group = '') {
        
        $order_info = $this->table('orders')->field($fields)->where($condition)->group($group)->order($order)->find();
        if (empty($order_info)) {
            return array();
        }
        if (isset($order_info['order_state'])) {
            $order_info['state_desc'] = orderState($order_info);
        }
        if (isset($order_info['payment_code'])) {
            $order_info['payment_name'] = orderPaymentName($order_info['payment_code']);
        }

        //追加返回订单扩展表信息
        if (in_array('order_common',$extend)) {
            $order_info['extend_order_common'] = $this->getOrderCommonInfo(array('order_id'=>$order_info['order_id']));
            $reciver_info = @unserialize($order_info['extend_order_common']['reciver_info']);
            if(empty($reciver_info)){
                $reciver_info = @json_decode($value['reciver_info'],true);
            }
            $invoice_info = @unserialize($order_info['extend_order_common']['invoice_info']);
            if(empty($invoice_info)){
                $invoice_info = @json_decode($value['invoice_info'],true);
            }
            $order_info['extend_order_common']['reciver_info'] = $reciver_info;
            $order_info['extend_order_common']['invoice_info'] = $invoice_info;
        }
        //追加返回店铺信息
        if (in_array('store',$extend)) {
            $order_info['extend_store'] = Model('store')->getStoreInfo(array('store_id'=>$order_info['store_id']));
        }
        //追加支付日志
        if (in_array('order_pay_action',$extend)) {
            $order_info['order_pay_action'] = Model('order_pay_action')->getOrderPayActionList(array('order_id'=>$order_info['order_id']));
        }
        //返回买家信息(废弃)
        if (in_array('member',$extend)) {
            $order_info['extend_member'] = Model('member')->getMemberInfoByID($order_info['buyer_id']);
        }
        //追加返回商品信息
        if (in_array('order_goods',$extend)) {
            //取商品列表
            $order_goods_list = $this->getOrderGoodsList(array('order_id'=>$order_info['order_id']));
            foreach ($order_goods_list as $k=>$order_goods){
                $if_edit_goods = empty($order_goods['bc_id']) && $order_info['order_state'] < ORDER_STATE_MAKING && $order_info['order_state']>0;
                $order_goods['if_edit_goods'] = $if_edit_goods; 
                $order_goods['kezi_raw'] = $order_goods['kezi'];
                $order_goods['kezi'] = formatKezi($order_goods['kezi']);
                $order_goods_list[$k] = $order_goods;
            }
            $order_info['extend_order_goods'] = $order_goods_list;
        }
        if (in_array('order_log',$extend)) {            
            //取日志列表
            $order_log_list = $this->table("order_log")->where(array('order_id'=>$order_info['order_id']))->order('log_id desc')->select();
            foreach ($order_log_list as $k=>$v){
                $order_tmp = array('order_state'=>$v['log_orderstate'],'chain_code'=>$order_info['chain_code']);
                $v['log_time'] = date("Y-m-d H:i:s",$v['log_time']);
                $v['log_orderstate'] = orderState($order_tmp);
                $order_log_list[$k] = $v;
            }
            $order_info['extend_order_log'] = $order_log_list;
        }
        return $order_info;
    }

    public function getOrderCommonInfo($condition = array(), $field = '*') {
        return $this->table('order_common')->where($condition)->find();
    }

    public function getOrderPayInfo($condition = array(), $master = false,$lock = false) {
        return $this->table('order_pay')->where($condition)->master($master)->lock($lock)->find();
    }

    /**
     * 取得支付单列表
     *
     * @param unknown_type $condition
     * @param unknown_type $pagesize
     * @param unknown_type $filed
     * @param unknown_type $order
     * @param string $key 以哪个字段作为下标,这里一般指pay_id
     * @return unknown
     */
    public function getOrderPayList($condition, $pagesize = '', $filed = '*', $order = '', $key = '') {
        return $this->table('order_pay')->field($filed)->where($condition)->order($order)->page($pagesize)->key($key)->select();
    }

    /**
     * 取得店铺订单列表
     *
     * @param int $store_id 店铺编号
     * @param string $order_sn 订单sn
     * @param string $buyer_name 买家名称
     * @param string $state_type 订单状态
     * @param string $query_start_date 搜索订单起始时间
     * @param string $query_end_date 搜索订单结束时间
     * @param string $skip_off 跳过已关闭订单
     * @return array $order_list
     */
    public function getStoreOrderList($store_id, $order_sn, $buyer_name, $state_type, $query_start_date, $query_end_date, $skip_off, $fields = '*', $extend = array(),$params = array()) {
        $condition = array();
        $condition['store_id'] = $store_id;
        
        //$condition['store_id'] = $store_id;
        if(!in_array("limit_store_order_all",$_SESSION['seller_limits'])){
            $condition['seller_id'] = $_SESSION['seller_id'];
        }
        if(!empty($order_sn) ||
         !empty($buyer_name) || 
         !empty($params["buyer_phone"])){
            unset($condition['store_id'], $condition['seller_id']);
        }
        if (preg_match('/^\d{10,20}$/',$order_sn)) {
            $condition['order_sn'] = $order_sn;
        }
        if ($buyer_name != '') {
            $condition['buyer_name'] = $buyer_name;
        }
        if(isset($params["is_xianhuo"])&&$params["is_xianhuo"]!=''){
            $condition['is_xianhuo'] = $params["is_xianhuo"];
        }
        if(isset($params["is_zp"])&&$params["is_zp"]!=''){
            $condition['is_zp'] = $params["is_zp"];
        }
        if(isset($params["seller_name"])&&!empty($params["seller_name"])){
            $condition['seller_name'] = $params["seller_name"];
        }
        if(isset($params["pay_status"])&&!empty($params["pay_status"])){
            $condition['pay_status'] = $params["pay_status"];
        }
        if(isset($params["buyer_phone"])&&!empty($params["buyer_phone"])){
            $condition['buyer_phone'] = $params["buyer_phone"];
        }
        if(isset($params["goods_id"])&&!empty($params["goods_id"])){
            $list_order_id=$this->getOrderGoodsList(array("goods_itemid|style_sn"=>$params["goods_id"]),"order_id");
            if(count($list_order_id)>0){
                $order_ids=array_column($list_order_id,"order_id");
                $condition['order_id']=array("in",$order_ids);
            }else{
                $condition['order_id'] = 0;//货品、款号不正确时，让它搜索不到；
            }
        }

        //点款时间条件--开始
        $start_payment_time = null;
        $end_payment_time = null;
        if(isset($params["pay_start_date"])&&!empty($params["pay_start_date"])){
            $pay_start_date = $params["pay_start_date"];
            $if_start_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$pay_start_date);
            $start_payment_time = $if_start_date ? strtotime($pay_start_date) : null;
        }
        if(isset($params["pay_end_date"])&&!empty($params["pay_end_date"])){
            $pay_end_date = $params["pay_end_date"];
            $if_end_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$pay_end_date);
            $end_payment_time = $if_end_date ? strtotime($pay_end_date) : null;
        }
        if ($start_payment_time || $end_payment_time) {
            $condition['payment_time'] = array('time',array($start_payment_time,$end_payment_time));
        }
        //点款时间条件--结束

        //发货时间条件--开始
        $start_finnshed_time = null;
        $end_finnshed_time = null;
        if(isset($params["finnshed_start_date"])&&!empty($params["finnshed_start_date"])){
            $finnshed_start_date = $params["finnshed_start_date"];
            $if_start_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$finnshed_start_date);
            $start_finnshed_time = $if_start_date ? strtotime($finnshed_start_date) : null;
        }
        if(isset($params["finnshed_end_date"])&&!empty($params["finnshed_end_date"])){
            $finnshed_end_date = $params["finnshed_end_date"];
            $if_end_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$finnshed_end_date);
            $end_finnshed_time = $if_end_date ? strtotime($finnshed_end_date) : null;
        }
        if ($start_finnshed_time || $end_finnshed_time) {
            $condition['delay_time'] = array('time',array($start_finnshed_time,$end_finnshed_time));
        }
        //发货时间条件--结束



        if (isset($chain_id)) {
            $condition['chain_id'] = intval($chain_id);
        }
        $allow_state_array = array('state_toconfirm', 'state_topay','state_making','state_tosign', 'state_tosend', 'state_success','state_cancel','state_tobc');
        if (in_array($state_type, $allow_state_array)) {
            $condition['order_state'] = str_replace($allow_state_array,
                array(ORDER_STATE_TO_CONFIRM, ORDER_STATE_NEW, ORDER_STATE_MAKING, ORDER_STATE_TO_SIGN, ORDER_STATE_TOSEND,ORDER_STATE_SUCCESS,ORDER_STATE_CANCEL,ORDER_STATE_TO_BC), $state_type);
        } else {
            if ($state_type != 'state_notakes') {
                $state_type = 'store_order';
            }
        }
        $if_start_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$query_start_date);
        $if_end_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$query_end_date);
        $start_unixtime = $if_start_date ? strtotime($query_start_date) : null;
        $end_unixtime = $if_end_date ? strtotime($query_end_date): null;
        if ($start_unixtime || $end_unixtime) {
            $condition['add_time'] = array('time',array($start_unixtime,$end_unixtime));
        }

        if ($skip_off == 1) {
            $condition['order_state'] = array('neq',ORDER_STATE_CANCEL);
        }

        if ($state_type == 'state_new') {
            $condition['chain_code'] = 0;
        }
        if ($state_type == 'state_pay') {
            $condition['chain_code'] = 0;
        }
        if ($state_type == 'state_notakes') {
            $condition['order_state'] = array('in',array(ORDER_STATE_NEW,ORDER_STATE_PAY));
            $condition['chain_code'] = array('gt',0);
        }
        if($state_type == 'store_order'){
            $condition['order_state'] = array('neq',ORDER_STATE_CANCEL);
        }
        $order_list = $this->getOrderList($condition, 20, $fields, 'order_id desc','', $extend);

        //页面中显示那些操作
        foreach ($order_list as $key => $order_info) {

            //显示取消订单
            $order_info['if_store_cancel'] = $this->getOrderOperateState('store_cancel',$order_info, $store_id);
            //显示调整费用
            $order_info['if_modify_price'] = $this->getOrderOperateState('modify_price',$order_info, $store_id);
			//显示调整订单费用
        	$order_info['if_spay_price'] = $this->getOrderOperateState('spay_price',$order_info, $store_id);
            //显示发货
            $order_info['if_store_send'] = $this->getOrderOperateState('store_send',$order_info, $store_id);
            //显示锁定中
            $order_info['if_lock'] = $this->getOrderOperateState('lock',$order_info, $store_id);
            //显示物流跟踪
            $order_info['if_deliver'] = $this->getOrderOperateState('deliver',$order_info, $store_id);
            //门店自提订单完成状态
            $order_info['if_chain_receive'] = $this->getOrderOperateState('chain_receive',$order_info, $store_id);

           //可以换货
            $order_info['if_exchange'] = $this->getOrderOperateState('exchange',$order_info, $store_id);

            //显示布产按钮
            $order_info['if_buchan'] = $this->getOrderOperateState('buchan_bar',$order_info, $store_id);

            //显示审核按钮
            $order_info['if_check'] = $this->getOrderOperateState('check_order',$order_info, $store_id);


            //查询消费者保障服务
            if (C('contract_allow') == 1) {
                $contract_item = Model('contract')->getContractItemByCache();
            }
            foreach ($order_info['extend_order_goods'] as $value) {
                $value['image_60_url'] = cthumb($value['goods_image'], 60, $value['store_id']);
                $value['image_240_url'] = cthumb($value['goods_image'], 240, $value['store_id']);
                $value['goods_type_cn'] = orderGoodsType($value['goods_type']);
                $value['goods_url'] = '#';//urlShop('goods','index',array('goods_id'=>$value['goods_id']));
                //处理消费者保障服务
                if (trim($value['goods_contractid']) && $contract_item) {
                    $goods_contractid_arr = explode(',',$value['goods_contractid']);
                    foreach ((array)$goods_contractid_arr as $gcti_v) {
                        $value['contractlist'][] = $contract_item[$gcti_v];
                    }
                }
                $order_info['goods_list'][] = $value;
                /*
                if ($value['goods_type'] == 5) {
                    $order_info['zengpin_list'][] = $value;
                } else {
                    $order_info['goods_list'][] = $value;
                }
                */
            }

            if (empty($order_info['zengpin_list'])) {
                $order_info['goods_count'] = count($order_info['goods_list']);
            } else {
                $order_info['goods_count'] = count($order_info['goods_list']) + 1;
            }
            //取得其它订单类型的信息
            $this->getOrderExtendInfo($order_info);
            $order_list[$key] = $order_info;
        }
        return $order_list;
    }


    /**
     * 取得订单列表(未被删除)
     * @param unknown $condition
     * @param string $pagesize
     * @param string $field
     * @param string $order
     * @param string $limit
     * @param unknown $extend 追加返回那些表的信息,如array('order_common','order_goods','store')
     * @return Ambigous <multitype:boolean Ambigous <string, mixed> , unknown>
     */
    public function getNormalOrderList($condition, $pagesize = '', $field = '*', $order = 'order_id desc', $limit = '', $extend = array()){
        $condition['delete_state'] = 0;
        return $this->getOrderList($condition, $pagesize, $field, $order, $limit, $extend);
    }

    /**
     * 取得订单列表(所有)
     * @param unknown $condition
     * @param string $pagesize
     * @param string $field
     * @param string $order
     * @param string $limit
     * @param unknown $extend 追加返回那些表的信息,如array('order_common','order_goods','store')
     * @return Ambigous <multitype:boolean Ambigous <string, mixed> , unknown>
     */
    public function getOrderList($condition, $pagesize = '', $field = '*', $order = 'order_id desc', $limit = '', $extend = array(), $master = false){
           
        $list = $this->table('orders')->field($field)->where($condition)->page($pagesize)->order($order)->limit($limit)->master($master)->select();
        if (empty($list)) return array();
        $order_list = array();
        foreach ($list as $order) {
            if (isset($order['order_state'])) {
                $order['state_desc'] = orderState($order);
            }
            if (isset($order['payment_code'])) {
                $order['payment_name'] = orderPaymentName($order['payment_code']);
            }
            if (isset($order['pay_status'])) {
                $order['pay_status_name'] = orderPayStatusName($order['pay_status']);
            }
            if(isset($order['is_xianhuo'])&&$order['is_xianhuo']==1)
                $order['is_xianhuo_name'] = "现货订单";
            else
                $order['is_xianhuo_name'] = "定制订单";

            if (!empty($extend)) $order_list[$order['order_id']] = $order;
        }
        if (empty($order_list)) $order_list = $list;

        //追加返回订单扩展表信息
        if (in_array('order_common',$extend)) {
            $order_common_list = $this->getOrderCommonList(array('order_id'=>array('in',array_keys($order_list))));
            foreach ($order_common_list as $value) {
                $order_list[$value['order_id']]['extend_order_common'] = $value;
                $reciver_info = @unserialize($value['reciver_info']);
                if(empty($reciver_info)){
                    $reciver_info = @json_decode($value['reciver_info'],true);
                }
                $invoice_info = @unserialize($value['invoice_info']);
                if(empty($invoice_info)){
                    $invoice_info = @json_decode($value['invoice_info'],true);
                }
                $order_list[$value['order_id']]['extend_order_common']['reciver_info'] = $reciver_info;
                $order_list[$value['order_id']]['extend_order_common']['invoice_info'] = $invoice_info;
            }
        }
        //追加返回店铺信息
        if (in_array('store',$extend)) {
            $store_id_array = array();
            foreach ($order_list as $value) {
                if (!in_array($value['store_id'],$store_id_array)) $store_id_array[] = $value['store_id'];
            }
            $store_list = Model('store')->getStoreList(array('store_id'=>array('in',$store_id_array)));
            $store_new_list = array();
            foreach ($store_list as $store) {
                $store_new_list[$store['store_id']] = $store;
            }
            foreach ($order_list as $order_id => $order) {
                $order_list[$order_id]['extend_store'] = $store_new_list[$order['store_id']];
            }
        }

        //追加返回买家信息
        if (in_array('member',$extend)) {
            foreach ($order_list as $order_id => $order) {
                $order_list[$order_id]['extend_member'] = Model('member')->getMemberInfoByID($order['buyer_id']);
            }
        }

        //追加返回商品信息
        if (in_array('order_goods',$extend)) {
            //取商品列表
            $order_goods_list = $this->getOrderGoodsList(array('order_id'=>array('in',array_keys($order_list))));
            if (!empty($order_goods_list)) {
                foreach ($order_goods_list as $value) {
                    $order_list[$value['order_id']]['extend_order_goods'][] = $value;
                }
            } else {
                $order_list[$value['order_id']]['extend_order_goods'] = array();
            }
        }

        return $order_list;
    }

    /**
     * 取得(买/卖家)订单某个数量缓存
     * @param string $type 买/卖家标志，允许传入 buyer、store
     * @param int $id   买家ID、店铺ID
     * @param string $key 允许传入  NewCount、PayCount、SendCount、EvalCount、TakesCount，分别取相应数量缓存，只许传入一个
     * @return array
     */
    public function getOrderCountCache($type, $id, $key) {
        if (!C('cache_open')) return array();
        $type = 'ordercount'.$type;
        $ins = Cache::getInstance('redis');
        $order_info = $ins->hget($id,$type,$key);
        return !is_array($order_info) ? array($key => $order_info) : $order_info;
    }


    /**
     * 设置(买/卖家)订单某个数量缓存
     * @param string $type 买/卖家标志，允许传入 buyer、store
     * @param int $id 买家ID、店铺ID
     * @param array $data
     */
    public function editOrderCountCache($type, $id, $data) {
        if (!C('cache_open') || empty($type) || !intval($id) || !is_array($data)) return ;
        $ins = Cache::getInstance('redis');
        $type = 'ordercount'.$type;
        $ins->hset($id,$type,$data);
    }

    /**
     * 取得买卖家订单数量某个缓存
     * @param string $type $type 买/卖家标志，允许传入 buyer、store
     * @param int $id 买家ID、店铺ID
     * @param string $key 允许传入  NewCount、PayCount、SendCount、EvalCount、TakesCount，分别取相应数量缓存，只许传入一个
     * @return int
     */
    public function getOrderCountByID($type, $id, $key) {
        $cache_info = $this->getOrderCountCache($type, $id, $key);

        if (is_string($cache_info[$key])) {
            //从缓存中取得
            $count = $cache_info[$key];
        } else {
            //从数据库中取得
            $field = $type == 'buyer' ? 'buyer_id' : 'store_id';
            $condition = array($field => $id);
            $func = 'getOrderState'.$key;
            $count = $this->$func($condition);
            $this->editOrderCountCache($type,$id,array($key => $count));
        }
        return $count;
    }

    /**
     * 删除(买/卖家)订单全部数量缓存
     * @param string $type 买/卖家标志，允许传入 buyer、store
     * @param int $id   买家ID、店铺ID
     * @return bool
     */
    public function delOrderCountCache($type, $id) {
        if (!C('cache_open')) return true;
        $ins = Cache::getInstance('redis');
        $type = 'ordercount'.$type;
        return $ins->hdel($id,$type);
    }

    /**
     * 待付款订单数量
     * @param unknown $condition
     */
    public function getOrderStateNewCount($condition = array()) {
        $condition['order_state'] = ORDER_STATE_NEW;
        $condition['chain_code'] = 0;
        return $this->getOrderCount($condition);
    }

    /**
     * 待发货订单数量
     * @param unknown $condition
     */
    public function getOrderStatePayCount($condition = array()) {
        $condition['order_state'] = ORDER_STATE_TOSEND;
        return $this->getOrderCount($condition);
    }

    /**
     * 待收货订单数量
     * @param unknown $condition
     */
    public function getOrderStateSendCount($condition = array()) {
        $condition['order_state'] = ORDER_STATE_SEND;
        return $this->getOrderCount($condition);
    }

    /**
     * 待评价订单数量
     * @param unknown $condition
     */
    public function getOrderStateEvalCount($condition = array()) {
        $condition['order_state'] = ORDER_STATE_SUCCESS;
        $condition['delete_state'] = 0;
        $condition['evaluation_state'] = 0;
        return $this->getOrderCount($condition);
    }

    /**
     * 待自提订单数量
     * @param unknown $condition
     */
    public function getOrderStateTakesCount($condition = array()) {
        $condition['order_state'] = array('in',array(ORDER_STATE_NEW,ORDER_STATE_PAY));
        $condition['chain_code'] = array('gt',0);
        return $this->getOrderCount($condition);
    }

    /**
     * 交易中的订单数量
     * @param unknown $condition
     */
    public function getOrderStateTradeCount($condition = array()) {
        $condition['order_state'] = array(array('neq',ORDER_STATE_CANCEL),array('neq',ORDER_STATE_SUCCESS),'and');
        return $this->getOrderCount($condition);
    }

    /**
     * 取得订单数量
     * @param unknown $condition
     */
    public function getOrderCount($condition) {
        return $this->table('orders')->where($condition)->count();
    }

    /**
     * 取得订单商品表详细信息
     * @param unknown $condition
     * @param string $fields
     * @param string $order
     */
    public function getOrderGoodsInfo($condition = array(), $fields = '*', $order = '') {
        return $this->table('order_goods')->where($condition)->field($fields)->order($order)->find();
    }

    /**
     * 刪除訂單商品
     * @param unknown $condition
     * @param string $fields
     * @param string $order
     */
    public function deleteOrderGoodsInfo($condition = array()) {
        if(empty($condition)) return false;
        return $this->table('order_goods')->where($condition)->delete();
    }

    /**
     * 取得订单商品表列表
     * @param unknown $condition
     * @param string $fields
     * @param string $limit
     * @param string $page
     * @param string $order
     * @param string $group
     * @param string $key
     */
    public function getOrderGoodsList($condition = array(), $fields = '*', $limit = null, $page = null, $order = 'rec_id desc', $group = null, $key = null) {
        return $this->table('order_goods')->field($fields)->where($condition)->limit($limit)->order($order)->group($group)->key($key)->page($page)->select();
    }

    /**
     * 取得订单扩展表列表
     * @param unknown $condition
     * @param string $fields
     * @param string $limit
     */
    public function getOrderCommonList($condition = array(), $fields = '*', $order = '', $limit = null) {
        return $this->table('order_common')->field($fields)->where($condition)->order($order)->limit($limit)->select();
    }

    /**
     * 插入订单支付表信息
     * @param array $data
     * @return int 返回 insert_id
     */
    public function addOrderPay($data) {
        return $this->table('order_pay')->insert($data);
    }

    /**
     * 插入订单表信息
     * @param array $data
     * @return int 返回 insert_id
     */
    public function addOrder($data) {
        $insert = $this->table('orders')->insert($data);
        if ($insert) {
            //更新缓存
            if (C('cache_open')) {
                QueueClient::push('delOrderCountCache',array('buyer_id'=>$data['buyer_id'],'store_id'=>$data['store_id']));
            }
        }
        return $insert;
    }

    /**
     * 插入订单扩展表信息
     * @param array $data
     * @return int 返回 insert_id
     */
    public function addOrderCommon($data) {
        return $this->table('order_common')->insert($data);
    }

    /**
     * 插入订单扩展表信息
     * @param array $data
     * @return int 返回 insert_id
     */
    public function addOrderGoods($data) {
        return $this->table('order_goods')->insertAll($data);
    }
	
	public function addOrderGood($data) {
        return $this->table('order_goods')->insert($data);
    }

    /**
     * 添加订单日志
     */
    public function addOrderLog($data) {
        $data['log_role'] = str_replace(array('buyer','seller','system','admin'),array('买家','商家','系统','管理员'), $data['log_role']);
        $data['log_time'] = time();
        return $this->table('order_log')->insert($data);
    }

    /**
     * 获取一条订单信息
     */
    public function getoneByorder($where, $field = '*', $order = '', $group = '') {
        return $this->table('orders')->field($field)->where($where)->group($group)->order($order)->find();
    }

    /**
     * 更改订单信息
     *
     * @param unknown_type $data
     * @param unknown_type $condition
     */
    public function editOrder($data,$condition,$limit = '') {
        $update = $this->table('orders')->where($condition)->limit($limit)->update($data);
        if ($update) {
            //更新缓存
            if (C('cache_open')) {
                QueueClient::push('delOrderCountCache',$condition);
            }
        }
        return $update;
    }

    /**
     * 更改订单信息
     *
     * @param unknown_type $data
     * @param unknown_type $condition
     */
    public function editOrderCommon($data,$condition) {
        return $this->table('order_common')->where($condition)->update($data);
    }

    /**
     * 更改订单信息
     *
     * @param unknown_type $data
     * @param unknown_type $condition
     */
    public function editOrderGoods($data,$condition) {
        return $this->table('order_goods')->where($condition)->update($data);
    }

    /**
     * 更改订单支付信息
     *
     * @param unknown_type $data
     * @param unknown_type $condition
     */
    public function editOrderPay($data,$condition) {
        return $this->table('order_pay')->where($condition)->update($data);
    }

    /**
     * 订单操作历史列表
     * @param unknown $order_id
     * @return Ambigous <multitype:, unknown>
     */
    public function getOrderLogList($condition, $order = 'log_id desc',$fields = '*', $limit = null, $page = null, $group = null, $key = null) {
        $order_log_list=$this->table('order_log')->field($fields)->where($condition)->limit($limit)->order($order)->group($group)->key($key)->page($page)->select();
         foreach ($order_log_list as $k=>$v){
            $order_tmp = array('order_state'=>$v['log_orderstate'],'chain_code'=>'');
            $v['log_time'] = date("Y-m-d H:i:s",$v['log_time']);
            $v['log_orderstate'] = orderState($order_tmp);
            $order_log_list[$k] = $v;

            if(preg_match("/收货单(.*?)审核后货号(.*?)自动绑定订单/is", $order_log_list[$k]['log_msg'])){
                unset($order_log_list[$k]);
            }
        }
        return $order_log_list;
    }


    /**
     * 订单操作历史列表
     * @param unknown $order_id
     * @return Ambigous <multitype:, unknown>
     */
    public function getOrderLogPageList($order_id,$page,$page_size) {
        $from_num=($page-1)*$page_size;
        $to_num=$page*$page_size;
        $sql="select * from order_log where order_id={$order_id} order by log_id desc limit {$from_num},{$to_num}";
        $order_log_list=$this->query($sql);
        foreach ($order_log_list as $k=>$v){
            $order_tmp = array('order_state'=>$v['log_orderstate'],'chain_code'=>'');
            $v['log_time'] = date("Y-m-d H:i:s",$v['log_time']);
            $v['log_orderstate'] = orderState($order_tmp);
            $order_log_list[$k] = $v;
        }
        return $order_log_list;
    }

    /**
     * 取得单条订单操作记录
     * @param unknown $condition
     * @param string $order
     */
    public function getOrderLogInfo($condition = array(), $order = '') {
        return $this->table('order_log')->where($condition)->order($order)->find();
    }

    /**
     * 返回是否允许某些操作
     * @param unknown $operate
     * @param unknown $order_info
     */
    public function getOrderOperateState($operate,$order_info,$store_id=''){

        if (!is_array($order_info) ||
         empty($order_info) ||
          ($order_info['store_id'] != $store_id)) {
            return false;
        }
        
        if (isset($order_info['if_'.$operate])) {
            return $order_info['if_'.$operate];
        }
        switch ($operate) {

            //审核按钮
            case 'check_order':
                $state = $order_info['order_state'] == ORDER_STATE_TO_CONFIRM ;
                //|| ($order_info['pay_status'] == ORDER_PAY_TODO && $order_info['order_state'] == ORDER_STATE_NEW);
               break;

            //布产按钮
            case 'buchan_bar':
                $state = $order_info['order_state'] == ORDER_STATE_TO_BC ;
                //|| ($order_info['pay_status'] == ORDER_PAY_TODO && $order_info['order_state'] == ORDER_STATE_NEW);
               break;

            //买家取消订单: 订单未确认，或者 未付款
            case 'buyer_cancel':
                $state = $order_info['order_state'] == ORDER_STATE_TO_CONFIRM 
                || ($order_info['pay_status'] == ORDER_PAY_TODO && $order_info['order_state'] == ORDER_STATE_NEW);
               break;

            //添加赠品
            case 'add_zp':
                $state = $order_info['order_state'] == ORDER_STATE_TO_CONFIRM ;
                //|| ($order_info['pay_status'] == ORDER_PAY_TODO && $order_info['order_state'] == ORDER_STATE_NEW);
               break;

            //编辑发货地址按钮
            case 'edit_addr':
                $state = $order_info['order_state'] < ORDER_STATE_SEND ;
                //|| ($order_info['pay_status'] == ORDER_PAY_TODO && $order_info['order_state'] == ORDER_STATE_NEW);
               break;

            //编辑货品
            case 'edit_goods':                
                $state = $order_info['pay_status'] <= ORDER_PAY_TODO && $order_info['order_state']>0;
                //|| ($order_info['pay_status'] == ORDER_PAY_TODO && $order_info['order_state'] == ORDER_STATE_NEW);
               break;

            //退款
            case 'return_price':
                $state = $order_info['pay_status'] > ORDER_PAY_TODO && in_array($order_info['if_return_all'],array(0,3));
                //|| ($order_info['pay_status'] == ORDER_PAY_TODO && $order_info['order_state'] == ORDER_STATE_NEW);
               break;

            //退货
            case 'return_goods':
                $state = $order_info['pay_status'] == ORDER_PAY_FULL && $order_info['order_state'] > ORDER_STATE_TOSEND;
                //|| ($order_info['pay_status'] == ORDER_PAY_TODO && $order_info['order_state'] == ORDER_STATE_NEW);
               break;

           //申请退款:
           case 'refund_cancel':
               //$state = $order_info['refund'] == 1 && !intval($order_info['lock_state']);
               $state = $order_info['pay_status'] > ORDER_PAY_TODO && $order_info['order_state'] > ORDER_STATE_TO_CONFIRM && $order_info['refund_state'] < REFUND_STATW_ALL && in_array($order_info['if_return_all'],array(0,3));
               break;

           //商家取消订单: 订单未确认，或者 未付款
           case 'store_cancel':
               $state = $order_info['order_state'] == ORDER_STATE_TO_CONFIRM
               || ($order_info['pay_status'] == ORDER_PAY_TODO && $order_info['order_state'] == ORDER_STATE_NEW);
               break;

           //平台取消订单
           case 'system_cancel':
               $state = $order_info['order_state'] == ORDER_STATE_TO_CONFIRM
               || ($order_info['pay_status'] == ORDER_PAY_TODO && $order_info['order_state'] == ORDER_STATE_NEW);
               break;

           //平台收款
           case 'system_receive_pay':
               $state = $order_info['order_state'] == ORDER_STATE_NEW;
               //$state = $state && $order_info['payment_code'] == 'online' && $order_info['api_pay_time'];
               break;

           //买家投诉
           case 'complain':
               $state = in_array($order_info['order_state'],array(ORDER_STATE_PAY,ORDER_STATE_SEND)) ||
                   intval($order_info['finnshed_time']) > (TIMESTAMP - C('complain_time_limit'));
               break;

           case 'payment':
               //$state = $order_info['order_state'] == ORDER_STATE_NEW && $order_info['payment_code'] == 'online';
               $state = $order_info['order_state'] == ORDER_STATE_NEW;
               break;

            //调整运费
            case 'modify_price':
                $state = $order_info['order_state'] == ORDER_STATE_TO_CONFIRM
                || ($order_info['pay_status'] == ORDER_PAY_TODO && $order_info['order_state'] == ORDER_STATE_NEW);
                $state = floatval($order_info['shipping_fee']) > 0 && $state;
               break;
			   
			//调整商品费用
        	case 'spay_price':
        	    $state = $order_info['order_state'] == ORDER_STATE_TO_CONFIRM
        	    || ($order_info['pay_status'] == ORDER_PAY_TODO && $order_info['order_state'] == ORDER_STATE_NEW);
				   $state = floatval($order_info['goods_amount']) > 0 && $state;
        	   break;

            //发货
            case 'store_send':
                $state = !$order_info['lock_state'] && $order_info['order_state'] == ORDER_STATE_TOSEND;
                break;

            //收货
            case 'receive':
                $state = !$order_info['lock_state'] && $order_info['order_state'] == ORDER_STATE_SEND;
                break;


            //换货
            case 'exchange':
                $state = !$order_info['lock_state'] && $order_info['order_state'] >= ORDER_STATE_NEW  && $order_info['order_state'] < ORDER_STATE_SEND ;
                break;

            //门店自提完成
            case 'chain_receive':
                $state = !$order_info['lock_state'] && $order_info['order_state'] == ORDER_STATE_TOSEND && $order_info['chain_code'];
                break;

            //评价
            case 'evaluation':
                $state = !$order_info['lock_state'] && !$order_info['evaluation_state'] && $order_info['order_state'] == ORDER_STATE_SUCCESS;
                break;

            case 'evaluation_again':
                $state = !$order_info['lock_state'] && $order_info['evaluation_state'] && !$order_info['evaluation_again_state'] && $order_info['order_state'] == ORDER_STATE_SUCCESS;
                break;

            //锁定
            case 'lock':
                $state = intval($order_info['lock_state']) ? true : false;
                break;

            //快递跟踪
            case 'deliver':
                $state = false; //!empty($order_info['shipping_code']) && in_array($order_info['order_state'],array(ORDER_STATE_SEND,ORDER_STATE_SUCCESS));
                break;

            //放入回收站
            case 'delete':
                $state = false; //in_array($order_info['order_state'], array(ORDER_STATE_CANCEL,ORDER_STATE_SUCCESS)) && $order_info['delete_state'] == 0;
                break;

            //永久删除、从回收站还原
            case 'drop':
            case 'restore':
                $state = false; // in_array($order_info['order_state'], array(ORDER_STATE_CANCEL,ORDER_STATE_SUCCESS)) && $order_info['delete_state'] == 1;
                break;

            //分享
            case 'share':
                $state = false;
                break;
                
            // 确认订单    
            case 'confirm':
                $state = $order_info['order_state'] == ORDER_STATE_TO_CONFIRM;
                break;

        }
        return $state;

    }

    /**
     * 联查订单表订单商品表
     *
     * @param array $condition
     * @param string $field
     * @param number $page
     * @param string $order
     * @return array
     */
    public function getOrderAndOrderGoodsList($condition, $field = '*', $page = 0, $order = 'rec_id desc') {
        return $this->table('order_goods,orders')->join('inner')->on('order_goods.order_id=orders.order_id')->where($condition)->field($field)->page($page)->order($order)->select();
    }

    /**
     * 订单销售记录 订单状态为20、30、40时
     * @param unknown $condition
     * @param string $field
     * @param number $page
     * @param string $order
     */
    public function getOrderAndOrderGoodsSalesRecordList($condition, $field="*", $page = 0, $order = 'rec_id desc') {
        $condition['order_state'] = array('in', array(ORDER_STATE_PAY, ORDER_STATE_SEND, ORDER_STATE_SUCCESS));
        return $this->getOrderAndOrderGoodsList($condition, $field, $page, $order);
    }

    /**
     * 取得其它订单类型的信息
     * @param unknown $order_info
     */
    public function getOrderExtendInfo(& $order_info) {
        //取得预定订单数据
        if ($order_info['order_type'] == 2) {
            $result = Logic('order_book')->getOrderBookInfo($order_info);
            //如果是未支付尾款
            if ($result['data']['if_buyer_repay']) {
                $result['data']['order_pay_state'] = false;
            }
            $order_info = $result['data'];
        }
    }
    
    /**
     * 更新订单布产信息
     * @param unknown $bcinfos
     */
    public function updateGoodsBCinfo($order_detail_id, $bc) {           
        
        $data = array('bc_id' => $bc['bc_id'], 'bc_status' => $bc['buchan_status']);
        if (!empty($bc['goods_id']) && is_numeric($bc['goods_id'])) {
            if ($bc['goods_type'] == 'qiban' || $bc['goods_type'] == 'lz') {
                $order_good = $this->getOrderGoodsInfo(['rec_id' => $order_detail_id], 'goods_id');
                if (!empty($order_good) && $order_good['goods_id'] != $bc['goods_id']) {
                    $data['goods_itemid'] = $bc['goods_id'];
                }
            } else {
                $data['goods_itemid'] = $bc['goods_id'];
            }
        }
        
        // TODO: 更新订单商品
        $data['carat'] = $bc['cart'];
        $data['zhushi_num'] = $bc['zhushi_num'];
        $data['cut'] = $bc['cut'];
        $data['clarity'] = $bc['clarity'];
        $data['color'] = $bc['color'];
        $data['cert_type'] = $bc['cert'];
        $data['cert_id'] = $bc['zhengshuhao'];
        $data['caizhi'] = $bc['caizhi'];
        $data['jinse'] = $bc['jinse'];
        $data['jinzhong'] = $bc['jinzhong'];
        $data['zhiquan'] = $bc['zhiquan'];
        $data['kezi'] = $this->resolve_kezi(isset($bc['kezi_raw']) ? $bc['kezi_raw'] : $bc['kezi']);
        $data['face_work'] = $bc['face_work'];
        $data['xiangqian'] = $bc['xiangqian'];
        $data['xiangkou'] = floatval($bc['xiangkou']);
        $data['peishi_type'] = $bc['is_peishi'];
        
        $this->editOrderGoods($data, array('rec_id' => $order_detail_id));
        return $bc['buchan_status'];
    }
   
    public function getOrderSync($order_id) {
        return $this->table('orders,order_sync')->join('left')->on('orders.order_id = order_sync.order_id')->field('*')->where(['orders.order_id' => $order_id])->find();
    }
    
    public function createOrderSync($data) {
        return $this->table('order_sync')->insert($data);
    }
    
    public function editOrderSync($condition, $data) {
        return $this->table('order_sync')->where($condition)->update($data);
    }

    /**
     * 获取布产中的订单信息
     * @param number $limit
     * @return array
     */
    public function getOrderSyncList($limit = 100) {
        // 针对已布产的期货订单 and 已同步到后端 and 尚未到待发货状态 的付款订单  大于1514736000 表示 2018年的单子
        return $this->table('orders,order_sync')
                    ->join('inner')->on('orders.order_id = order_sync.order_id')->field('orders.order_sn,orders.order_id')
                    ->where('orders.is_xianhuo = 0 AND orders.order_state = '. ORDER_STATE_MAKING.' AND orders.pay_status > '.ORDER_PAY_TODO.' AND IFNULL(pull_stop, 0) = 0 AND (order_sync.latest_pull_order IS NULL or TIMESTAMPDIFF(HOUR, order_sync.latest_pull_order, NOW()) >= '.PULL_BC_FREQ_IN_HOUR.') AND orders.add_time > 1514736000')
                    ->order("case when order_sync.latest_pull_order is null then '1900-01-01 00:00:00' else order_sync.latest_pull_order end ASC, orders.order_state DESC")->limit($limit)
                    ->select();   
    }
    
    /**
     * 获取待同步到后端的订单列表
     * @param number $limit
     * @return array
     */
    public function getOrdersToSync($limit = 100) {
        // 针对 已审核 and （没有同步过 或者 在上一次同步后订单有发生信息变更） and 允许同步 的订单
        return $this->table('orders,order_sync')
                    ->join('left')->on('orders.order_id = order_sync.order_id')->field('orders.order_sn,orders.order_id')
                    ->where('((orders.order_state >=' .ORDER_STATE_TO_CONFIRM. ' AND IFNULL(sync_stop, 0) = 0) OR (orders.order_state = '.ORDER_STATE_CANCEL.' and order_sync.sync_stop = 0)) AND (order_sync.latest_sync_order is null or orders.update_time > order_sync.latest_sync_order ) AND orders.add_time > 1514736000')
                    ->order("CASE WHEN order_sync.latest_sync_order IS NULL THEN '1900-01-01 00:00:00' ELSE order_sync.latest_sync_order END ASC, orders.order_id DESC")->limit($limit)
                    ->select();
    }
    /**
     * 查询订单现货商品是否被其它订单绑定
     * @param unknown $order_id
     * @return array|bool/null/array
     */
    public function preCheckOrder($order_id) {
        
        return $this->query("SELECT m.order_sn, g.goods_itemid from order_goods g inner join goods_items m on m.goods_id = g.goods_itemid and m.is_on_sale = 2 and m.order_detail_id > 0 and m.order_detail_id != g.rec_id where g.order_id =".$order_id);
    }

    /***
     * 获取订单商品布产信息(只关注未退款退货商品)
     * @param unknown $order_id
     * @return array|bool/null/array
     */
    public function getOrderGoodsBcStatus($order_id) {
        return $this->getOrderGoodsFillStats($order_id, ['order_goods.is_xianhuo' => 0, 'order_goods.is_return' => 0]);
    }
    
    public function getOrderGoodsFillStats($order_id, $conditions = ['order_goods.is_return' => 0]) {   
        $conditions['order_goods.order_id'] = $order_id;     
        $conditions['order_goods.is_finance'] = 1; //排除不要销账的货品   
        return $this->table('order_goods,goods_items')->join('left')->on('order_goods.goods_itemid=goods_items.goods_id')->where($conditions)->field("order_goods.*,ifnull(goods_items.is_on_sale, 0) as is_on_sale")->select();
    }
    
    /**
     * 成品定制商品拆分
     * @param unknown $goods_list
     */
    public function buildCpdzGoodsList($goods_list){
        if(empty($goods_list) || !is_array($goods_list)){
            return array();
        }
        foreach($goods_list as $k => $v){
            $goods_list[$k]['stone_list'] = array();
            $order_detail_id = $v['rec_id'];
            $is_cpdz = $v['is_cpdz'];                        
            if($is_cpdz !=1 ){  
                continue; //非成品定制
            }

            $goods_info = DB::getRow2("select goods_id,pinpai,zhushitiaoma,company_id from goods_items where order_detail_id={$order_detail_id}");
            if(!empty($goods_info)){
                $goods_id  = $goods_info['goods_id'];
                $company_id = $goods_info['company_id'];
                $pinpai_arr = explode('/',trim($goods_info['pinpai']));
                $zhushitiaoma_arr = explode('/',trim($goods_info['zhushitiaoma']));
            }else{
                $goods_list[$k]['stone_list'][] = "成品定制货号{$v['goods_itemid']}绑定订单状态不对，请重新绑定货号";
                continue;
            }
            if(!empty($goods_info['pinpai'])){
                foreach ($pinpai_arr as $p){
                    $error ="亲，成品定制货号{$goods_info['goods_id']}品牌字段填写的证书号有问题请联系入库人员核实! ";
                    $stone_info = DB::getRow2("select * from goods_items where zhengshuhao='{$p}' and is_on_sale=2 and company_id={$company_id} and cat_type='裸石'");
                    if(empty($stone_info) ){
                        $error .= "证书号<span style='color:red;'>{$p}</span>找不到符合销账条件的货号！";
                        $goods_list[$k]['stone_list'][] = $error;
                    }else{
                        $stone_info['order_goods_id'] = $order_detail_id;
                        $goods_list[$k]['stone_list'][] = $stone_info;
                    }
                }
            }           
            /*
            if(empty($goods_list[$k]['stone_list'])){
                $error ="亲，成品定制货号{$goods_info['goods_id']}品牌字段没有填写证书号，请联系入库人员核实! ";
                $goods_list[$k]['stone_list'][] = $error;
            }*/
            
        }//end foreach
        return $goods_list;
     }//endbuildCpdzGoodsList
     
     public function checkGoodsReturn($order_detail_id){         
         $sql = "select g.goods_id,g.weixiu_status from order_goods og inner join goods_items g on og.goods_itemid=g.goods_id where og.rec_id={$order_detail_id}";
         $row = DB::getRow2($sql);
         if(empty($row['goods_id'])){
             return callback(false,"商品暂时不能退货");
         }else if(!in_array($row['weixiu_status']/1,array(0,1,4))){
             return callback(false,"商品正在维修中，暂时不能退货");
         }         
         return callback(true);
	 }
    
     private function resolve_kezi($kezi) {
         /*if (strpos($kezi, '[&符号]') !== false) {
             return str_replace('[&符号]', '&', $kezi);
         } else if (strpos($kezi, '[间隔号]') !== false) {
             return str_replace('[间隔号]','•', $kezi);
         } else if (strpos($kezi, '[空心]') !== false) {
             return str_replace('[空心]','♡', $kezi);
         } else if (strpos($kezi, '[实心]') !== false) {
             return str_replace('[实心]', '♥', $kezi);
         }
         */
         return $kezi;

     }

    /**
     * 获取订单赠品列表
     */
     public  function get_gift_order_goods($where, $page = 0,$field='*',$group='', $order = 'order_goods.style_sn desc'){

         $condition['order_goods.goods_type'] = 5;
         $condition['orders.order_state'] = array('gt',10);
         if (!empty($where['start_time'])) {
             $condition['orders.audit_time'][] = array('gt',$where['start_time']);
         }
         if(!empty($where['end_time'])){
             $condition['orders.audit_time'][] = array('lt',$where['end_time'].' 23:59:59');
         }
         if(empty($where['start_time']) && empty($where['end_time'])){
             $condition['orders.audit_time'] = array('gt',date("Y").'-'.date("m").'-01');
         }
         if(isset($where['style_sn']) && !empty($where['style_sn'])){
             $condition['order_goods.style_sn'] = $where['style_sn'];
         }         

         if(isset($where['store_id']) && !empty($where['store_id'])){
             $condition['orders.store_id'] = $where['store_id'];
         }
         $lists =  $this->table('order_goods,orders')->join('inner')->on('order_goods.order_id=orders.order_id')->where($condition)->field('order_goods.rec_id')->order($order)->group($group)->select();
         $count = count($lists);


         return $this->table('order_goods,orders')->join('inner')->on('order_goods.order_id=orders.order_id')->where($condition)->field($field)->page($page,$count)->order($order)->group($group)->select();






     }
}
