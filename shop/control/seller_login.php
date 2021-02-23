<?php
/**
 * 店铺卖家登录
 *
 *
 *
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */



defined('INTELLIGENT_SYS') or exit('Access Invalid!');

class seller_loginControl extends BaseSellerControl {

    public function __construct() {
        parent::__construct();
        /*if (!empty($_SESSION['seller_id'])) {
            @header('location: index.php?act=seller_center');die;
        }*/
    }

    public function indexOp() {
        $this->show_loginOp();
    }

    //后台登录
    public function show_loginOp() {
        if (defined('SSO')) {
            $login_url = Util::get_defined_array_var('SSO', 'login');
            Util::jump($login_url);
            exit;
        }
        Tpl::output('nchash', getNchash());
        Tpl::setLayout('null_layout');
        Tpl::showpage('login');
    }


    public function loginOp() {
        if(define('SSO')){
            showDialog('禁止进入','','error');
        }
        $result = chksubmit(true,true,'num');
        if ($result){
            if ($result === -11){
                showDialog('用户名或密码错误','','error');
            } elseif ($result === -12){
                showDialog('验证码错误','','error');
            }
        } else {
            showDialog('非法提交','','error');
        }

        $model_seller = Model('seller');
        $seller_info = $model_seller->getSellerInfo(array('seller_name' => $_POST['seller_name']));    
        if($seller_info) {
            $model_member = Model('member');
            $member_info = $model_member->getMemberInfo(
                array(
                    'member_id' => $seller_info['member_id'],
                    'member_passwd' => xmd5($_POST['password'])
                )
            );
            if($member_info) {
                parent::execLogin($member_info,$seller_info);
                $this->recordSellerLog('登录成功');
                redirect('index.php?act=seller_center');
            } else {
                showMessage('用户名密码错误', '', '', 'error');
            }
        } else {
            showMessage('用户名密码错误', '', '', 'error');
        }
    }



    public function post_loginOp(){
        //$virgin = Util::getString('virgin');
        $token = Util::getString('__klc_001');
        if(empty($token)){
            showMessage('地址错误', '', '', 'error');
            exit;
        }
        
        $sso_config = Util::get_defined_array_var('SSO');
        if (!$sso_config) {
            showMessage('地址错误', '', '', 'error');
            exit;
        }
        
        //token验证并返回用户名
        $sso_config = Util::get_defined_array_var('SSO');
        $check_url = $sso_config['checkout'];
        $check_url .= $token;
        $resp = Util::httpCurl($check_url);
        $resp = json_decode($resp, true);
        if(!isset($resp['status']) || $resp['status'] <> 1){
            showMessage('验证不通过，登录失败', $sso_config['logout'], '', 'error');
        }
        $lite_user = $resp['data']['user'];
        $model_seller = Model('seller');
        $seller_info = $model_seller->getSellerInfo(array('seller_name' => $lite_user['account']));
        if($seller_info) {
            $model_member = Model('member');
            $member_info = $model_member->getMemberInfo(
                array(
                    'member_id' => $seller_info['member_id']
                )
            );
            if($member_info) {
                parent::execLogin($member_info,$seller_info);
                setNcCookie($sso_config['token_ck'], $token, 86400);
                $this->recordSellerLog('登录成功');
                
                redirect('index.php?act=seller_center');
            } else {
                //退出登录点
                showMessage('没有此用户', $sso_config['logout'], '', 'error');
            }
        } else {
            //退出登录点
            showMessage('没有此卖家',$sso_config['logout'], '', 'error');
        }
    }







}
