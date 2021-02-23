<?php
/**
 * 裸钻加价率
 *
 *
 *
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */



defined('INTELLIGENT_SYS') or exit ('Access Invalid!');
class base_lz_discount_configControl extends BaseSellerControl {

    public $enabled = array(1 => '启用', 0=> '停用');
    //public $type = array(1=>'普通<0.5克拉',2=>'普通0.5（含）~1.0克拉',3=>'普通1.0（含）~1.5克拉',4=>'普通1.5（含）克拉以上',5=>'星耀<0.5克拉',6=>'星耀0.5（含）~1.0克拉',7=>'星耀1.0（含）~1.5克拉',8=>'星耀1.5（含）克拉以上',9=>'天生一对裸石',10=>'天生一对成品',11=>'成品',12=>'香榭巴黎');

    public function __construct() {
        parent::__construct();
    }

    public function indexOp() {
        $this->baselzdiscountconfiglistOp();
    }

    /**
     * 获取用户列表
     */
    public function get_sellerlist()
    {
        $model_seller = Model('seller');
        $condition = array(
            'seller_store.store_id' => $_SESSION['store_id'],
            'seller_store.seller_group_id' => array('gt', 0),
            'seller.is_hidden'=>0,
        );
        $sellerlist = $model_seller->getSellerStoreList($condition,'','',"seller.seller_id,seller.member_id,seller.seller_name");
        $temp_key = array_column($sellerlist,'member_id');  //键
        $temp_val = array_column($sellerlist,'seller_name');  //值
        $sellerlist = array_combine($temp_key,$temp_val);
        return $sellerlist;
    }

    /**
     * 获取裸钻属性值
     */
    public function get_diamond_attr()
    {
        $diamond_api = data_gateway('idiamond');
        $byApi = $diamond_api->get_diamond_index(array('cert'));
        $cert = isset($byApi['return_msg']['cert'])?$byApi['return_msg']['cert']:array();
        return array('cert'=> $cert);
    }

    /**
     * 裸钻加价率列表
     */
    public function baselzdiscountconfiglistOp() {
        // 裸钻zhekou
        $where = array();
        //if (trim($_GET['store_name']) != '') {
            //$where['store_name'] = array('like', '%'.trim($_GET['store_name']).'%');
        //}
        //var_dump($_GET);die;
        if (isset($_GET['type']) && !empty($_GET['type'])) {
            $where['type'] = $_GET['type'];
        }
        if (in_array($_GET['enabled'], array(0, 1)) && $_GET['enabled'] !== ''&& $_GET['enabled'] != null) {
            $where['enabled'] = $_GET['enabled'];
        }
        $sellerlist = $this->get_sellerlist();
        if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
            $where['user_id'] = $_GET['user_id'];
        }else{
            if(!empty($sellerlist)) $where['user_id'] = array("in", array_keys($sellerlist));
        }

        $type = paramsHelper::getParams("voucher_goods_type");
        $base_lz_discount_config = Model('base_lz_discount_config');
        $baselzdiscountconfiglist = $base_lz_discount_config->getBaseLzDiscountConfigList($where, '*', 'id desc', '',10);
        Tpl::output('show_page', $base_lz_discount_config->showpage(2));
        Tpl::output('baselzdiscountconfiglist', $baselzdiscountconfiglist);
        Tpl::output('type', $type);
        Tpl::output('enabled', $this->enabled);
        Tpl::output('seller', $sellerlist);
        $this->profile_menu('baselzdiscountconfiglist', 'baselzdiscountconfiglist');
        Tpl::showpage('baselzdiscountconfig.list');
    }

    /**
     * 关联版式添加
     */
    public function baselzdiscountconfig_addOp() {
        if (chksubmit()) {
            // 验证表单
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                    array("input" => $_POST["user_id"], "require" => "true", "message" => '请选择销售顾问'),
                    array("input" => $_POST["zhekou_1"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_2"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_3"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_4"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_5"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_6"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_7"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_8"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_9"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_10"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_11"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_12"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_13"], "require" => "true", "message" => '折扣不能为空')
            );
            $error = $obj_validate->validate();
            if ($error != '') {
                showDialog(L('error') . $error, urlShop('base_lz_discount_config', 'index'));
            }
            $insert = array();
            $user_id     = $_POST['user_id'];
            $zhekou_1     = $_POST['zhekou_1'];
            $zhekou_2     = $_POST['zhekou_2'];
            $zhekou_3     = $_POST['zhekou_3'];
            $zhekou_4     = $_POST['zhekou_4'];
            $zhekou_5     = $_POST['zhekou_5'];
            $zhekou_6     = $_POST['zhekou_6'];
            $zhekou_7     = $_POST['zhekou_7'];
            $zhekou_8     = $_POST['zhekou_8'];
            $zhekou_9     = $_POST['zhekou_9'];
            $zhekou_10     = $_POST['zhekou_10'];
            $zhekou_11     = $_POST['zhekou_11'];
            $zhekou_12     = $_POST['zhekou_12'];
            $zhekou_13     = $_POST['zhekou_13'];

            for($i=1;$i<14;$i++){
                $tmp_str = "zhekou_".$i;
                $nn = $$tmp_str;
                list($min,$type) = $this->get_voucher_goods($i);
                if($nn<$min || $nn >1){
                    showDialog(L('error') .$type. "折扣范围在{$min}到1之间", urlShop('base_lz_discount_config', 'index'));
                }
            }
            $model = Model('base_lz_discount_config');
            for($i=1;$i<14;$i++){
                $tmp_str = "zhekou_".$i;
                $nn = $$tmp_str;
                $where = array('user_id'=>$user_id,'type'=>$i);
                $discount_info = $model->getBaseLzDiscountConfigInfo($where);
                if(!empty($discount_info)){
                    $where = array();
                    $where['id']  = $discount_info['id'];
                    $update = array('zhekou'=>$nn);
                    $result = $model->editBaseLzDiscountConfig($update, $where);
                }else{
                    $insert = array('user_id'=>$user_id,'type'=>$i,'zhekou'=>$nn, 'enabled'=>1);
                    $result = $model->addBaseLzDiscountConfig($insert);
                }
            }  
            
            if ($result) {
                showDialog(L('nc_common_op_succ'), urlShop('base_lz_discount_config', 'index'),'succ');
            } else {
                showDialog(L('nc_common_op_fail'), urlShop('base_lz_discount_config', 'index'));
            }
        }
        // 是否能使用编辑器
        /*if(checkPlatformStore()){ // 平台店铺可以使用编辑器
            $editor_multimedia = true;
        } else {    // 三方店铺需要
            $editor_multimedia = false;
            if ($this->store_grade['sg_function'] == 'editor_multimedia') {
                $editor_multimedia = true;
            }
        }
        Tpl::output('editor_multimedia', $editor_multimedia);*/
        $diamondattr = $this->get_diamond_attr();
        Tpl::output('seller', $this->get_sellerlist());
        Tpl::output('cert', $diamondattr['cert']);
        Tpl::output('good_type', $this->good_type);
        $this->profile_menu('baselzdiscountconfig_add', 'baselzdiscountconfig_add');
        Tpl::showpage('baselzdiscountconfig.add');
    }

    /**
     * 关联版式编辑
     */
    public function baselzdiscountconfig_editOp() {
        if (chksubmit()) {
            $id = intval($_POST['id']);
            if ($id <= 0) {
                showMessage(L('wrong_argument'), '', '', 'error');
            }
            // 验证表单
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                    //array("input" => $_POST["user_id"], "require" => "true", "message" => '请选择销售顾问'),
                    array("input" => $_POST["zhekou_1"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_2"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_3"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_4"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_5"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_6"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_7"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_8"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_9"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_10"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_11"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_12"], "require" => "true", "message" => '折扣不能为空'),
                    array("input" => $_POST["zhekou_13"], "require" => "true", "message" => '折扣不能为空')
            );
            $error = $obj_validate->validate();
            if ($error != '') {
                showDialog(L('error') . $error, urlShop('base_lz_discount_config', 'index'));
            }
            $model = Model('base_lz_discount_config');
            $plate_info = $model->getBaseLzDiscountConfigInfo(array('id' => $id));
            $user_id = $plate_info['user_id'];

            $update = array();
            //$user_id     = $_POST['user_id'];
            $zhekou_1     = $_POST['zhekou_1'];
            $zhekou_2     = $_POST['zhekou_2'];
            $zhekou_3     = $_POST['zhekou_3'];
            $zhekou_4     = $_POST['zhekou_4'];
            $zhekou_5     = $_POST['zhekou_5'];
            $zhekou_6     = $_POST['zhekou_6'];
            $zhekou_7     = $_POST['zhekou_7'];
            $zhekou_8     = $_POST['zhekou_8'];
            $zhekou_9     = $_POST['zhekou_9'];
            $zhekou_10     = $_POST['zhekou_10'];
            $zhekou_11     = $_POST['zhekou_11'];
            $zhekou_12     = $_POST['zhekou_12'];
            $zhekou_13     = $_POST['zhekou_13'];

            for($i=1;$i<14;$i++){
                $tmp_str = "zhekou_".$i;
                $nn = $$tmp_str;
                list($min,$type) = $this->get_voucher_goods($i);
                if($nn<$min || $nn >1){
                    showDialog(L('error') .$type. "折扣范围在{$min}到1之间", urlShop('base_lz_discount_config', 'baselzdiscountconfig_edit',array('id'=>$id)));
                }
            }
            //$model = Model('base_lz_discount_config');
            for($i=1;$i<14;$i++){
                $tmp_str = "zhekou_".$i;
                $nn = $$tmp_str;
                $where = array('user_id'=>$user_id,'type'=>$i);
                $discount_info = $model->getBaseLzDiscountConfigInfo($where);
                if($discount_info){
                    $where = array();
                    $where['id']  = $discount_info['id'];
                    $update = array('user_id'=>$user_id,'type'=>$i,'zhekou'=>$nn, 'enabled'=>1);
                    $result = $model->editBaseLzDiscountConfig($update, $where);
                }else{
                    $insert = array('user_id'=>$user_id,'type'=>$i,'zhekou'=>$nn, 'enabled'=>1);
                    $result = $model->addBaseLzDiscountConfig($insert);
                }
            }
            if ($result) {
                showDialog(L('nc_common_op_succ'), urlShop('base_lz_discount_config', 'index'),'succ');
            } else {
                showDialog(L('nc_common_op_fail'), urlShop('base_lz_discount_config', 'index'));
            }

        }
        $id = intval($_GET['id']);
        if ($id <= 0) {
            showMessage(L('wrong_argument'), '', '', 'error');
        }
        $model = Model('base_lz_discount_config');
        $plate_info = $model->getBaseLzDiscountConfigInfo(array('id' => $id));
        $user_id = $plate_info['user_id'];
        $discount_data = $model->getBaseLzDiscountConfigList(array('user_id'=>$user_id), '*',"type desc", '',12);
        //var_dump($discount_data);die;
        $new_discount_data = array();
        $sellerlist = $this->get_sellerlist();
        foreach($discount_data as $val){
            $new_discount_data[$val['type']] = $val['zhekou'];
        }
        $new_discount_data['id'] = $id;
        Tpl::output('plate_info', $new_discount_data);
        $diamondattr = $this->get_diamond_attr();
        Tpl::output('seller', $sellerlist);
        Tpl::output('user_id', $user_id);
        Tpl::output('cert', $diamondattr['cert']);
        Tpl::output('type', paramsHelper::getParams("voucher_goods_type"));
        $this->profile_menu('baselzdiscountconfig_edit', 'baselzdiscountconfig_edit');
        Tpl::showpage('baselzdiscountconfig.add');
    }

    /**
     * 删除关联版式
     */
    public function drop_base_lz_discount_configOp() {
        $id = $_GET['id'];
        if (!preg_match('/^[\d,]+$/i', $id)) {
            showDialog(L('wrong_argument'), '', 'error');
        }
        $plateid_array = explode(',', $id);
        $return = Model('base_lz_discount_config')->delStorePlate(array('id' => array('in', $plateid_array)));
        if ($return) {
            showDialog(L('nc_common_del_succ'), 'reload', 'succ');
        } else {
            showDialog(L('nc_common_del_fail'), '', 'error');
        }
    }


   public function get_voucher_goods($key){
       $voucher_goods_type = paramsHelper::getParams('voucher_goods_type');
       $voucher_goods_jiajialv = paramsHelper::getParams('voucher_goods_jiajialv');
       $jiajialv = isset($voucher_goods_jiajialv[$key]) ? $voucher_goods_jiajialv[$key] : 0.01;
       $type = isset($voucher_goods_type[$key]) ? $voucher_goods_type[$key] : '';
       return array($jiajialv,$type);
   }



    /**
     * 用户中心右边，小导航
     *
     * @param string    $menu_type  导航类型
     * @param string    $menu_key   当前导航的menu_key
     * @param array     $array      附加菜单
     * @return
     */
    private function profile_menu($menu_type,$menu_key='',$array=array()) {
        $menu_array = array();
        switch ($menu_type) {
            case 'baselzdiscountconfiglist':
                $menu_array = array(
                    array('menu_key' => 'baselzdiscountconfiglist', 'menu_name' => '裸钻折扣列表', 'menu_url' => urlShop('base_lz_discount_config', 'baselzdiscountconfiglist'))
                );
                break;
            case 'baselzdiscountconfig_add':
                $menu_array = array(
                    array('menu_key' => 'baselzdiscountconfiglist', 'menu_name' => '裸钻折扣列表', 'menu_url' => urlShop('base_lz_discount_config', 'baselzdiscountconfiglist')),
                    array('menu_key' => 'baselzdiscountconfig_add', 'menu_name' => '添加裸钻折扣', 'menu_url' => urlShop('base_lz_discount_config', 'baselzdiscountconfig_add'))
                );
                break;
            case 'baselzdiscountconfig_edit':
                $menu_array = array(
                    array('menu_key' => 'baselzdiscountconfiglist', 'menu_name' => '裸钻折扣列表', 'menu_url' => urlShop('base_lz_discount_config', 'baselzdiscountconfiglist')),
                    array('menu_key' => 'baselzdiscountconfig_add', 'menu_name' => '添加裸钻折扣', 'menu_url' => urlShop('base_lz_discount_config', 'baselzdiscountconfig_add')),
                    array('menu_key' => 'baselzdiscountconfig_edit', 'menu_name' => '編輯裸钻折扣', 'menu_url' => urlShop('base_lz_discount_config', 'baselzdiscountconfig_edit'))
                );
                break;
        }
        if(!empty($array)) {
            $menu_array[] = $array;
        }
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }
}
