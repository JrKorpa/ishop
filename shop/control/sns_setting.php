<?php
/**
 * 图片空间操作
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */



defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class sns_settingControl extends BaseSNSControl {
    public function __construct() {
        parent::__construct();
        /**
         * 读取语言包
         */
        Language::read('sns_setting');
    }
    public function change_skinOp(){
        Tpl::showpage('sns_changeskin', 'null_layout');
    }
    public function skin_saveOp(){
        $insert = array();
        $insert['member_id']    = $_SESSION['member_id'];
        $insert['setting_skin'] = $_GET['skin'];

        Model()->table('sns_setting')->pk(array('member_id'))->insert($insert,true);
    }
}
