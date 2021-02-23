<?php
/**
 * 店铺卖家注销
 *
 *
 *
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */



defined('INTELLIGENT_SYS') or exit('Access Invalid!');

class seller_logoutControl extends BaseSellerControl {

    public function __construct() {
        parent::__construct();
    }

    public function indexOp() {
        $this->logoutOp();
    }

    public function logoutOp() {
        $this->recordSellerLog('注销成功');
        // 清除店铺消息数量缓存
        setNcCookie('storemsgnewnum'.$_SESSION['seller_id'],0,-3600);
        session_destroy();
        
        $sso_config = Util::get_defined_array_var('SSO');
        if($sso_config){
            setNcCookie($sso_config['token_ck'],'',-3600);
            $logout_url = $sso_config['logout'];
            header("Location:".$logout_url);
            exit;
        }

        redirect('index.php?act=seller_login');
    }

}
