<?php
/**
 * 店铺
 *
 * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */



defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class voucherControl extends mobileHomeControl{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * 代金券列表
     */
    public function voucher_tpl_listOp(){
        $param = $_REQUEST;
        $model_voucher = Model('voucher');
        $templatestate_arr = $model_voucher->getTemplateState();
        $voucher_gettype_array = $model_voucher->getVoucherGettypeArray();

        $where = array();
        $where['voucher_t_state'] = $templatestate_arr['usable'][0];
        $store_id = intval($param['store_id']);
        if ($store_id > 0){
            $where['voucher_t_store_id'] = $store_id;
        }
        $where['voucher_t_gettype'] = array('in',array($voucher_gettype_array['points']['sign'],$voucher_gettype_array['free']['sign']));
        if ($param['gettype'] && in_array($param['gettype'], array('points','free'))) {
            $where['voucher_t_gettype'] = $voucher_gettype_array[$param['gettype']]['sign'];
        }
        $order = 'voucher_t_id asc';
        $voucher_list = $model_voucher->getVoucherTemplateList($where, '*', 20, 0, $order);
        if ($voucher_list) {
            foreach($voucher_list as $k=>$v){
                $v['voucher_t_end_date_text'] = $v['voucher_t_end_date']?@date('Y年m月d日',$v['voucher_t_end_date']):'';
                $voucher_list[$k] = $v;
            }
        }
        output_data(array('voucher_list' => $voucher_list));
    }
}
