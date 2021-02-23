<?php
/**
 *  赠品管理
 * @b1 (c) 2005-2016 kelan Inc.
 * @license    http://官网
 * @link       交流群号：216611541
 * @since      提供技术支持 授权请购买正版授权
 * 
 **/

defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class gift_goodsControl extends mobileHomeControl{

	public function __construct() {
        parent::__construct();
    }
    
    public function get_gift_listOp(){
        $salesApi = data_gateway('isales');       
        $result = $salesApi->get_gift_list(array());
        if(isset($result['error']) && $result['error']==0){
            $gift_list = $result['return_msg'];
        }else{
            output_error($result['error']);
        }
        $datas = array('gift_list'=>$gift_list);
        output_data($datas);
    }
    
    public function get_gift_infoOp(){
        if(empty($_POST['goods_id'])){
            output_error("请选择赠品");
        }
        $goods_id = $_POST['goods_id']; 
        $salesApi = data_gateway('isales');
        $result = $salesApi->get_gift_list(array('goods_number'=>$goods_id,'status'=>1,'extends'=>array('goods_image')));
        if(isset($result['error']) && $result['error']==0){
            $gift_info = $result['return_msg'][0];
        }else{
            output_error($result['error']);
        }
        $datas = array('gift_info'=>$gift_info);
        output_data($datas);
    }
    
}