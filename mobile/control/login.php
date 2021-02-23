<?php
/**
 * 前台登录 退出操作
 *
 *
 * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */



defined('INTELLIGENT_SYS') or exit('Access Invalid!');

class loginControl extends mobileHomeControl {

    public function __construct(){
        parent::__construct();
    }

    /**
     * 登录
     */
    public function indexOp(){
        if(empty($_POST['username']) || empty($_POST['password']) || !in_array($_POST['client'], $this->client_type_array)) {
            output_error('登录失败');
        }

        $model_member = Model('member');

        $login_info = array();
        $login_info['user_name'] = $_POST['username'];
        $login_info['password'] = $_POST['password'];
        $member_info = $model_member->login($login_info);
        if(isset($member_info['error'])) {
            output_error($member_info['error']);
        } else {
            $token = $this->_get_token($member_info['member_id'], $member_info['member_name'], $_POST['client']);
            $seller_info = Model('seller')->getSellerInfo(array('member_id' => $member_info['member_id']));
            $store_list = model('seller_store')->get_seller_stores($seller_info['seller_id']);
            $new_store_list=array();
            foreach($store_list as $index=>$store_info){
                $new_store_list[$index]["store_id"]=$store_info["store_id"];
                $new_store_list[$index]["store_name"]= Model('store')->getOneStore(array('store_id' => $store_info["store_id"]))["store_name"];
            }
            if($token) {
                output_data(array('username' => $member_info['member_name'], 'userid' => $member_info['member_id'], 'key' => $token,
                    'store_list'=>$new_store_list,
                    'store_count'=>count($new_store_list),
                    'current_store_id'=>$new_store_list[0]["store_id"],
                    'current_store_name'=>$new_store_list[0]["store_name"],
                ));
            } else {
                output_error('登录失败，用户或密码不正确');
            }
        }
    }

    /**
     * 登录生成token
     */
    private function _get_token($member_id, $member_name, $client) {
        $model_mb_user_token = Model('mb_user_token');
        //重新登录后以前的令牌失效
        //暂时停用
        //$condition = array();
        //$condition['member_id'] = $member_id;
        //$condition['client_type'] = $client;
        //$model_mb_user_token->delMbUserToken($condition);
        //生成新的token
        $mb_user_token_info = array();
        $token = md5($member_name . strval(TIMESTAMP) . strval(rand(0,999999)));
        $mb_user_token_info['member_id'] = $member_id;
        $mb_user_token_info['member_name'] = $member_name;
        $mb_user_token_info['token'] = $token;
        $mb_user_token_info['login_time'] = TIMESTAMP;
        $mb_user_token_info['client_type'] = $client;

        $result = $model_mb_user_token->addMbUserToken($mb_user_token_info);

        if($result) {
            return $token;
        } else {
            return null;
        }

    }

    /**
     * 注册
     */
    public function registerOp(){
        $model_member   = Model('member');

        $register_info = array();
        $register_info['username'] = $_POST['username'];
        $register_info['password'] = $_POST['password'];
        $register_info['password_confirm'] = $_POST['password_confirm'];
        $register_info['email'] = $_POST['email'];
        $member_info = $model_member->register($register_info);
        if(!isset($member_info['error'])) {
            $token = $this->_get_token($member_info['member_id'], $member_info['member_name'], $_POST['client']);
            if($token) {
                output_data(array('username' => $member_info['member_name'], 'userid' => $member_info['member_id'], 'key' => $token));
            } else {
                output_error('注册失败');
            }
        } else {
            output_error($member_info['error']);
        }

    }

    public function post_loginOp(){
        
        if(!(defined('SSO_WAP'))){
            output_error('系统配置错误');
        }

        $token = $_POST['token'];
        if(empty($token)){
            output_error('参数错误');
        }

        //token验证并返回用户名
        $sso_config = Util::get_defined_array_var('SSO_WAP');
        $check_url = $sso_config['checkout'];
        $check_url .= $token;
        $resp = Util::httpCurl($check_url);
        $resp = json_decode($resp, true);
        if(!isset($resp['status']) || $resp['status'] <> 1){
            output_error('登录验证失败');
        }

        $lite_user = $resp['data']['user'];
        $model_member = Model('member');
        $member_info = $model_member->get_member_by_name($lite_user['account']);

        if(isset($member_info['error'])) {
            output_error('当前系统找不到您的账号信息');
        } else {
            $key_token = $this->_get_token($member_info['member_id'], $member_info['member_name'], 'wap');
            $seller_info = Model('seller')->getSellerInfo(array('member_id' => $member_info['member_id']));
            $store_list = model('seller_store')->get_seller_stores($seller_info['seller_id']);
            $new_store_list=array();
            foreach($store_list as $index=>$store_info){
                $store_arr = Model('store')->getOneStore(array('store_id' => $store_info["store_id"],'store_state'=>1));
                if(empty($store_arr)) continue;
                $new_store_list[$index]["store_id"]=$store_info["store_id"];
                $new_store_list[$index]["store_name"]= $store_arr["store_name"];
            }
            if($key_token) {
                
                $store_num = count($new_store_list);
                if (empty($store_num)) {
                    output_error('您当前没有任何商家信息，请与经销商部确认');
                }
                
                output_data(array('username' => $member_info['member_name'], 'userid' => $member_info['member_id'], 'key' => $key_token,
                    'store_list'=>$new_store_list,
                    'store_count'=>$store_num,
                    'current_store_id'=>$new_store_list[0]["store_id"],
                    'current_store_name'=>$new_store_list[0]["store_name"],
                ));

            } else {

                output_error('当前系统登录失败');
            }
        }

    }

    public function check_tokenOp() {
        $sso_config = Util::get_defined_array_var('SSO_WAP');
        if ($sso_config) {
            
            $token = $_POST['token'];
            if (empty($token)) {
                output_error('token is null');
            }
            
            $checked = Sso::check_token($token, $sso_config);
            if ($checked === false) {
                output_error('token is invalid');
            } else {
                output_data($checked);
            }
        }
    }
}
