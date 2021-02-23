<?php
/**
 * 单据单头模型
 *
 *
 *
 * *  (c) 2015-2018 . (http://www.kela.cn)
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 * @since      珂兰技术中心提供技术支持
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class goods_items_logModel extends Model {
    
    const LOG_TYPE_BILL_L ='B_L';//采购入库
    const LOG_TYPE_BILL_M ='B_M';//调拨
    const LOG_TYPE_BILL_S ='B_S';//销售出库
    const LOG_TYPE_BILL_C ='B_C';//销售出库
    const LOG_TYPE_BILL_B ='B_B';//退货返厂
    
    
    const LOG_TYPE_BILL_CG ='B_CG';//采购入库
    const LOG_TYPE_BILL_DB ='B_DB';//调拨
    const LOG_TYPE_BILL_ZJ ='B_ZJ';//质检入库
    const LOG_TYPE_BILL_CK ='B_CK';//配货出库
    const LOG_TYPE_BILL_FC ='B_FC';//退货返厂
    
    const LOG_TYPE_ORDER_XD ='O_XD';//下单
    const LOG_TYPE_ORDER_FH ='O_FH';//发货
    const LOG_TYPE_ORDER_PS ='O_PS';//物流配送
    const LOG_TYPE_ORDER_JS ='O_JS';//拒收
    const LOG_TYPE_ORDER_QS ='O_QS';//签收
    const LOG_TYPE_ORDER_SQS ='O_SQS';//卖家签收
    const LOG_TYPE_ORDER_BGH ='O_BGH';//客户归还
    const LOG_TYPE_ORDER_QH ='O_QH';//物流取货
    const LOG_TYPE_ORDER_TK ='O_TK';//退款退货
    const LOG_TYPE_ORDER_QT ='O_QT';//其它
    
    public function __construct(){
        parent::__construct('goods_items_log');
    }
    public static function getLogTypeList(){
        return array(
            'B_L'=>'采购入库',//采购入库完成,增加商品编号：b1401，库存货号：b140108
            'B_M'=>'调拨',//调拨完成，从【仓库1】调拨到【仓库2】
            'B_B'=>'退货返厂',//完成退货返厂
            'B_S'=>'销售出库',//完成养护处理
            'B_C'=>'其他出库',//完成防伪处理
            
            'O_XD'=>'下单',//客户下单，订单号：9000000000070701
            'O_FH'=>'发货',//确认发货，快递单号：623476234
            'O_PS'=>'物流配送',//完成物流发货配送，客户已签收，快递单号：623423487
            'O_JS'=>'拒收',//客户拒收，商品损坏，快递单号：623423487
            'O_QS'=>'签收',//客户签收，商品损坏，快递单号：623423487
            'O_QH'=>'物流取货',//完成物流取货，快递单号：623423489
            'O_TK'=>'退款退货',//退款退货
            'O_SQS'=>'卖家签收', //卖家签收
            'O_BGH'=>'客户归还', //客户归还
            'O_QH'=>'其它',//其它
        );
    }
    public function addGoodsItemLog($data){
        if(empty($data['goods_itemid']) || empty($data['log_type'])){
            exit('无效数据');
            return false;
        }
        $goods_itemid = $data['goods_itemid'];
        $log_type = $data['log_type'];
        if(!empty($goods_itemid)){
            $goods_info = $this->table("goods_items")->where(array('goods_id'=>$goods_itemid))->find();
        }
        $data['log_store_id']   = $goods_info['store_id'];//店铺ID
        $data['goods_warehouse'] = $goods_info['warehouse'];//所在仓库
        $data['goods_box'] = $goods_info['box_sn'];//所在柜位
        $data['goods_state'] = $goods_info['is_on_sale'];//商品状态
        $data['log_user_ip'] = getIp();//操作者IP
        $data['log_time'] = date("Y-m-d H:i:s");//操作时间
        return $this->table('goods_items_log')->insert($data);
    }
    public function getGoodsItemLogList($condition = array(), $fields = "*", $pagesize = null, $order = 'log_id desc', $limit = null){
        return $this->table('goods_items_log')->where($condition)->field($fields)->order($order)->limit($limit)->page($pagesize)->select();
    }
}    
?>