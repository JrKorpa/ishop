<?php
/**
 * 单据日志模型
 *
 *
 *
 * *  (c) 2015-2018 . (http://www.kela.cn)
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 * @since      珂兰技术中心提供技术支持
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class erp_bill_logModel extends Model {

    public function __construct(){
        parent::__construct('erp_bill_log');
    }
    
    /**
     * erp_bill_goods 表列表
     * @param unknown $condition
     * @param string $fields
     * @param string $pagesize
     * @param string $order
     * @param string $limit
     */
    public function getErpBillLogList($condition = array(), $field = "*", $pagesize = null, $order = 'id desc', $limit = null){
        return $this->table('erp_bill_log')->where($condition)->field($field)->order($order)->limit($limit)->page($pagesize)->select();
    }
    /**
     * 格式化 单据日志列表
     * @param unknown $data
     * @param string $type
     * @return unknown|Ambigous <unknown, multitype:unknown >
     */
    public function formatErpBillLogList($data,$type="list"){
        if(empty($data)){
            return $data;
        }        
        $data = $type=="list"?$data:array($data);
        foreach($data as &$vo){
            $vo['bill_status_name'] = erp_billModel::getBillStatus($vo['bill_status']);
        }
        return $type=="list"?$data:$data[0];
    }
    
    public function createBillLog($bill_id,$remark){
        $bill_info = $this->table("erp_bill")->field("bill_status")->where(array('bill_id'=>$bill_id))->find();
        if(!empty($bill_info)){
            $bill_status = $bill_info['bill_status'];
            $bill_log = array(
                'bill_id'=>$bill_id,
                'bill_status'=>$bill_info['bill_status'],                
                'create_time'=> date("Y-m-d H:i:s",TIMESTAMP),
                'create_user'=>$_SESSION['seller_name'],
                'remark'=>$remark               
            );
            return $this->table('erp_bill_log')->insert($bill_log);
        }
        return false;
    }
    
}