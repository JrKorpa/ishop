<?php
/**
 * 商家注销
 *
 *
 * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */



defined('INTELLIGENT_SYS') or exit('Access Invalid!');

class seller_taobao_apiControl extends mobileSellerControl {

    public function __construct(){
        parent::__construct();
    }

    public function get_taobao_app_keyOp() {
        $taobao_app_key = "";
        if(C('taobao_api_isuse')) {
            $taobao_app_key = C('taobao_app_key');
        }
        output_data(array('taobao_app_key' => $taobao_app_key));
    }

    public function get_taobao_signOp() {
        $taobao_sign = "";
        $taobao_secret_key = C('taobao_secret_key');
        if(C('taobao_api_isuse')) {
            $taobao_sign = md5($taobao_secret_key . $_POST['sign_string'] . $taobao_secret_key);
        }
        output_data(array('taobao_sign' => $taobao_sign));
    }

    public function get_taobao_session_keyOp() {
        $taobao_session_key = "";

        if(C('taobao_api_isuse')) {
            $param = array();
            $param['client_id'] = C('taobao_app_key');
            $param['client_secret'] = C('taobao_secret_key');
            $param['grant_type'] = 'authorization_code';
            $param['code'] = trim($_POST['auth_code']);
            $param['redirect_uri'] = "urn:ietf:wg:oauth:2.0:oob";

            $result = http_post('https://oauth.taobao.com/token', $param);
            if($result) {
                $result = json_decode($result);
                if(!empty($result->access_token)) {
                    $taobao_session_key = $result->access_token;
                }
            }
        }

        output_data(array('taobao_session_key' => $taobao_session_key));
    }
}
