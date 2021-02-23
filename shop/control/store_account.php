<?php
/**
 * 卖家账号管理
 *
 *
 *
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */



defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class store_accountControl extends BaseSellerControl {
    public function __construct() {
        parent::__construct();
        Language::read('member_store_index');
    }

    public function account_listOp() {
        $model_seller = Model('seller');
        $condition = array(
            'seller_store.store_id' => $_SESSION['store_id'],
            'seller.is_hidden' => 0,
            'seller_store.seller_group_id' => array('gt', 0)
        );
        $seller_list = $model_seller->getSellerStoreList($condition);
        Tpl::output('seller_list', $seller_list);
        if (!empty($seller_list)) {
            $memberid_array = array();
            foreach ($seller_list as $val) {
                $memberid_array[] = $val['member_id'];
            }
            $member_name_array = Model('member')->getMemberList(array('member_id' => array('in', $memberid_array)), 'member_id,member_name');
            $member_name_array = array_under_reset($member_name_array, 'member_id');
            Tpl::output('member_name_array', $member_name_array);

            $model_seller_group = Model('seller_group');
            $seller_group_list = $model_seller_group->getSellerGroupList(array('store_id' => $_SESSION['store_id']));
            $seller_group_array = array_under_reset($seller_group_list, 'group_id');
            Tpl::output('seller_group_array', $seller_group_array);
        }

        $this->profile_menu('account_list');
        Tpl::showpage('store_account.list');
    }

    public function account_addOp() {
        $model_seller_group = Model('seller_group');
        $seller_group_list = $model_seller_group->getSellerGroupList(array('store_id' => $_SESSION['store_id']));
        if (empty($seller_group_list)) {
            showMessage('请先建立账号组', urlShop('store_account_group', 'group_add'), '', 'error');
        }
        Tpl::output('seller_group_list', $seller_group_list);
        $this->profile_menu('account_add');
        Tpl::showpage('store_account.add');
    }

    public function account_editOp() {
        $seller_id = intval($_GET['seller_id']);
        if ($seller_id <= 0) {
            showMessage('参数错误', '', '', 'error');
        }
        $model_seller = Model('seller');
        $seller_info = $model_seller->getSellerStoreInfo(array('seller.seller_id' => $seller_id, 'seller_store.store_id' => $_SESSION['store_id']));
        if (empty($seller_info) || intval($seller_info['store_id']) !== intval($_SESSION['store_id'])) {
            showMessage('账号不存在', '', '', 'error');
        }
        Tpl::output('seller_info', $seller_info);

        $model_seller_group = Model('seller_group');
        $seller_group_list = $model_seller_group->getSellerGroupList(array('store_id' => $_SESSION['store_id']));
        if (empty($seller_group_list)) {
            showMessage('请先建立账号组', urlShop('store_account_group', 'group_add'), '', 'error');
        }
        Tpl::output('seller_group_list', $seller_group_list);

        $this->profile_menu('account_edit');
        Tpl::showpage('store_account.edit');
    }

    public function account_pwOp() {
        $seller_id = intval($_GET['seller_id']);
        if ($seller_id <= 0) {
            showMessage('参数错误', '', '', 'error');
        }
        $model_seller = Model('seller');
        $seller_info = $model_seller->getSellerStoreInfo(array('seller.seller_id' => $seller_id, 'seller_store.store_id' => $_SESSION['store_id']));
        if (empty($seller_info) || intval($seller_info['store_id']) !== intval($_SESSION['store_id'])) {
            showMessage('账号不存在', '', '', 'error');
        }
        Tpl::output('seller_info', $seller_info);

        $model_seller_group = Model('seller_group');
        $seller_group_list = $model_seller_group->getSellerGroupList(array('store_id' => $_SESSION['store_id']));
        if (empty($seller_group_list)) {
            showMessage('请先建立账号组', urlShop('store_account_group', 'group_add'), '', 'error');
        }
        Tpl::output('seller_group_list', $seller_group_list);

        $this->profile_menu('account_pw');
        Tpl::showpage('store_account.pw');
    }

    public function account_saveOp() {

        $member_name = $_POST['member_name'];
        $password = $_POST['password'];
        $real_name = $_POST['real_name'];
        $group_id = intval($_POST['group_id']);
        $model_seller = Model('seller');
        if($this->_is_seller_name_exist($member_name)) {
            $sellerinfo = $model_seller->getSellerInfo(array('seller_name'=>$member_name));
            if(!empty($sellerinfo)){
                $seller_id = $sellerinfo['seller_id'];
                $store_id  = $sellerinfo['store_id'];
                $seller_group_id  = $sellerinfo['seller_group_id'];
                //$store_model = Model('store');
                //$toStore = $store_model->getOneStore(array('store_id'=>$store_id), " `store_company_id` ");
                //if($toStore['store_company_id'] != $_SESSION['store_company_id']){
                //    showDialog('账号已存在！', 'reload', 'error');
                //}
                $res = $this->_is_seller_store_exist($seller_id);
                if(!empty($res)){
                    showDialog('账号已存在', 'reload', 'error');
                }else{
                    //2.创建seller_store
                    $param = array(
                        'seller_id'=> $seller_id,
                        'store_id' => $_SESSION['store_id'],
                        'seller_group_id' => $group_id,
                        'is_admin' => 0
                        );
                    $model_seller_store = Model('seller_store');
                    $res = $model_seller_store->addSellerStore($param);
                    if($res){
                        $udata = array();
                        if(empty($store_id)){
                            $udata['store_id'] = $_SESSION['store_id'];
                        }
                        if(empty($seller_group_id)){
                            $udata['seller_group_id'] = $group_id;
                        }
                        if(!empty($udata)){
                            $model_seller->editSeller($udata, array('seller_id'=>$seller_id));
                        }
                        $this->recordSellerLog('添加账号成功!');
                        showDialog(Language::get('nc_common_op_succ'), urlShop('store_account', 'account_list'), 'succ');
                    }else{
                        $this->recordSellerLog('添加账号失败！');
                        showDialog(Language::get('nc_common_save_fail'), urlShop('store_account', 'account_list'), 'error');
                    }
                }
            }
        }
        
        $member_model = Model('member');   
        try {
            $member_model->beginTransaction();
            $condition = array('member_name'=>$member_name);
            $memberinfo = $member_model->getMemberInfo($condition);
            if(empty($memberinfo)){
                if(empty($password)){
                    showDialog('密码不能为空！', 'reload', 'error');
                }

                if(strlen($password)>30 || strlen($password)<6){
                    showDialog('密码长度不能小于6位数且不能大于30位数！', 'reload', 'error');
                }

                if(!preg_match("/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,10}$/", $password)){
                    showDialog('密码必须为字母和数字的组合！', 'reload', 'error');
                }
                //1.创建member
                $member_id = $member_model->addMember(array(
                    'member_name'       => $member_name,
                    'member_passwd'     => $password,
                    'member_truename'   => $real_name,
                    'member_email'      => ''
                ),false);

                if(!$member_id){
                    throw new Exception("添加member失败！");
                }
            }else{
                $member_id = $memberinfo['member_id'];
            }
            
            //2.创建seller
            $seller_info = array(
                'seller_id' =>$member_id,
                'seller_name' => $member_name,
                'member_id' => $member_id,
                'seller_group_id' => $group_id,
                'store_id' => $_SESSION['store_id'],
                'is_admin' => 0,
                'is_client' => 1, // 客户端登陆选项判断
            );
            //$model_seller = Model('seller');
            $result = $model_seller->addSeller($seller_info);
            if(!$result){
                throw new Exception("添加seller失败！");
            }

            $res = $this->_is_seller_store_exist($result);
            if(empty($res)){
                //2.创建seller_store
                $param = array(
                    'seller_id'=> $result,
                    'store_id' => $_SESSION['store_id'],
                    'seller_group_id' => $group_id,
                    'is_admin' => 0
                    );
                $model_seller_store = Model('seller_store');
                $res = $model_seller_store->addSellerStore($param);
                if(!$res){
                    throw new Exception("添加seller_store失败！");
                }
            }

            $member_model->commit();
            
            if(empty($memberinfo)){
                EventEmitter::dispatch('ishop', ['event' => 'member_upsert', 'member_name' => $member_name, 'member_passwd' =>xmd5(trim($password))]);
            }
            
            $this->recordSellerLog('添加账号成功，账号编号'.$result);
            showDialog(Language::get('nc_common_op_succ'), urlShop('store_account', 'account_list'), 'succ');
        } catch (Exception $e) {
            $member_model->rollback();
            
            $this->recordSellerLog('添加账号失败！'.$e->getMessage());
            showDialog(Language::get('nc_common_save_fail'), urlShop('store_account', 'account_list'), 'error');
        }
    }

    public function account_edit_saveOp() {
        // 客户端登陆选项判断
        $is_client = 0;
        if(intval($_POST['is_client']) > 0) {
            $is_client = 1;
        }

        $param = array(
            'seller_group_id' => intval($_POST['group_id']),
            'is_client' => $is_client,
        );

        $condition = array(
            'seller_id' => intval($_POST['seller_id']),
            'store_id' =>  $_SESSION['store_id']
        );
        $model_seller = Model('seller');
        $model_seller->beginTransaction();
        $result = $model_seller->editSeller($param, $condition);
        unset($param['is_client']);
        $model_seller_store = Model('seller_store');
        $res = $model_seller_store->editSellerStore($param, $condition);
        if($res) {
            $model_seller->commit();
            $this->recordSellerLog('编辑账号成功，账号编号：'.$_POST['seller_id']);
            showDialog(Language::get('nc_common_op_succ'), urlShop('store_account', 'account_list'), 'succ');
        } else {
            $member_model->rollback();
            $this->recordSellerLog('编辑账号失败，账号编号：'.$_POST['seller_id'], 0);
            showDialog(Language::get('nc_common_save_fail'), urlShop('store_account', 'account_list'), 'error');
        }
    }

    //重置密码
    public function account_pw_saveOp() {

        if(empty($_POST['new_password'])){
            showDialog('密码不能为空！', 'reload', 'error');
        }
        if(empty($_POST['check_password'])){
            showDialog('确认密码不能为空！', 'reload', 'error');
        }
        if($_POST['new_password'] != $_POST['check_password']){
            showDialog('两次输入密码不一致！', 'reload', 'error');
        }
        $model_seller = Model('seller');
        $sellerinfo = $model_seller->getSellerInfo(array('seller_id'=>$_POST['seller_id']));
        $member_id = $sellerinfo['member_id'];
        $param = array(
            'member_passwd' => xmd5(trim($_POST['new_password']))
        );
        $condition = array(
            'member_id' => intval($member_id)
        );
        $model_member = Model('member');
        $res = $model_member->editMember($condition, $param);
        if($res) {
            $this->recordSellerLog('重置密码成功!');
            showDialog("重置密码成功", urlShop('store_account', 'account_list'), 'succ');
        } else {
            $this->recordSellerLog('重置密码失败!', 0);
            showDialog("重置密码失败", urlShop('store_account', 'account_list'), 'error');
        }
    }

    public function account_delOp() {
        $seller_id = intval($_POST['seller_id']);
        if($seller_id > 0) {
            $condition = array();
            $condition['seller_id'] = $seller_id;
            $condition['store_id'] = $_SESSION['store_id'];
            $model_seller = Model('seller');
            $model_seller_store = Model('seller_store');
            $result = $model_seller_store->delSellerStore($condition);
            if($result) {
                $res = $model_seller_store->get_seller_stores($seller_id);
                if(empty($res)){
                    //$result = $model_seller->delSeller($condition);
                    $result = $model_seller->editSeller(array('store_id'=>0),array('seller_id'=>$seller_id));
                }else{
                    $result = $model_seller->editSeller(array('store_id'=>$res[0]['store_id']),array('seller_id'=>$seller_id));
                }
                $this->recordSellerLog('删除账号成功，账号编号'.$seller_id);
                showDialog(Language::get('nc_common_op_succ'),'reload','succ');
            } else {
                $this->recordSellerLog('删除账号失败，账号编号'.$seller_id);
                showDialog(Language::get('nc_common_save_fail'),'reload','error');
            }
        } else {
            showDialog(Language::get('wrong_argument'),'reload','error');
        }
    }

    public function check_seller_name_existOp() {
        $seller_name = $_GET['seller_name'];
        $result = $this->_is_seller_name_exist($seller_name);
        if($result) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    private function _is_seller_name_exist($seller_name) {
        $condition = $condition_member= array();
        $condition['seller_name'] = $seller_name;
        $condition_member['member_name'] = $seller_name;
        $model_seller = Model('seller');
        return $model_seller->isSellerExist($condition) || Model('member')->getMemberInfo($condition_member);//|| Model('store_joinin')->isExist($condition)
    }

    private function _is_seller_store_exist($seller_id) {
        $model_seller_store = Model('seller_store');
        $res = $model_seller_store->get_store($seller_id, $_SESSION['store_id']);
        return $res;
    }

    public function check_seller_memberOp() {
        $member_name = $_GET['member_name'];
        $password = $_GET['password'];
        $result = $this->_check_seller_member($member_name, $password);
        if($result) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    private function _check_seller_member($member_name, $password) {
        $member_info = $this->_check_member_password($member_name, $password);
        if($member_info && !$this->_is_seller_member_exist($member_info['member_id'])) {
            return $member_info;
        } else {
            return false;
        }
    }

    private function _check_member_password($member_name, $password) {
        $condition = array();
        $condition['member_name']   = $member_name;
        $condition['member_passwd'] = xmd5($password);
        $model_member = Model('member');
        $member_info = $model_member->getMemberInfo($condition);
        return $member_info;
    }

    private function _is_seller_member_exist($member_id) {
        $condition = array();
        $condition['member_id'] = $member_id;
        $model_seller = Model('seller');
        return $model_seller->isSellerExist($condition);
    }

    /**
     * 用户中心右边，小导航
     *
     * @param string    $menu_key   当前导航的menu_key
     * @return
     */
    private function profile_menu($menu_key = '') {
        $menu_array = array();
        $menu_array[] = array(
            'menu_key' => 'account_list',
            'menu_name' => '账号列表',
            'menu_url' => urlShop('store_account', 'account_list')
        );
        if($menu_key === 'account_add') {
            $menu_array[] = array(
                'menu_key'=>'account_add',
                'menu_name' => '添加账号',
                'menu_url' => urlShop('store_account', 'account_add')
            );
        }
        if($menu_key === 'account_edit') {
            $menu_array[] = array(
                'menu_key'=>'account_edit',
                'menu_name' => '编辑账号',
                'menu_url' => urlShop('store_account', 'account_edit')
            );
        }
        if($menu_key === 'account_pw') {
            $menu_array[] = array(
                'menu_key'=>'account_pw',
                'menu_name' => '修改密码',
                'menu_url' => urlShop('store_account', 'account_pw')
            );
        }

        Tpl::output('member_menu', $menu_array);
        Tpl::output('menu_key', $menu_key);
    }

}
