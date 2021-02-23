<?php
/**
 * 赠品管理
 *
 *
 *
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */



defined('INTELLIGENT_SYS') or exit ('Access Invalid!');
class gift_listControl extends BaseSellerControl {

    public $status = array(1 => '启用', 0=> '停用');
    public $good_type = array(1 => '现货',2=> '期货');
    public $sale_way = array(1 => '线上',2=> '线下',12=> '线上&线下');

    public function __construct() {
        parent::__construct();
    }

    public function indexOp() {
        $this->gift_listOp();
    }

    /**
     * 获取体验店列表
     */
    public function get_storelist()
    {
        $model_store = Model('store');
        $store_id = $this->store_info['store_id'];
        $storelist = $model_store->getStoreOnlineList(array('store_id'=>$store_id));
        $temp_key = array_column($storelist,'store_id');  //键
        $temp_val = array_column($storelist,'store_name');  //值
        $storelist = array_combine($temp_key,$temp_val);
        return $storelist;
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
     * 赠品列表
     */
    public function gift_listOp() {
        // 裸钻加价率
        $where = array();
        $where['channel_id'] = $this->store_info['store_id'];
        if (trim($_GET['gift_name']) != '') {
            //$where['gift_name'] = array('like', '%'.trim($_GET['gift_name']).'%');
            $where['gift_name'] = $_GET['gift_name'];
        }
        if (trim($_GET['goods_number']) != '') {
            //$where['gift_name'] = array('like', '%'.trim($_GET['gift_name']).'%');
            $where['goods_number'] = $_GET['goods_number'];
        }
        /*if (in_array($_GET['good_type'], array('1','2'))) {
            $where['good_type'] = $_GET['good_type'];
        }
        if (in_array($_GET['status'], array('0','1'))) {
            $where['status'] = $_GET['status'];
        }
        $diamondattr = $this->get_diamond_attr();
        $cert = $diamondattr['cert'];
        //var_dump($_GET['cert'], $cert);die;
        if (in_array($_GET['cert'], $cert)) {
            $where['cert'] = $_GET['cert'];
        }*/
        //var_dump($where);die;
        $sales_api = data_gateway('isales');
        $byApi = $sales_api->get_gift_list($where);
        $giftlist = isset($byApi['return_msg']) && !empty($byApi['return_msg']) ? $byApi['return_msg']:array();
        //var_dump($giftlist);die;
        //$diamond_jiajialv = Model('diamond_jiajialv');
        //$diamondjiajialvlist = $diamond_jiajialv->getDiamondJiajialvList($where, ' * ', 10, 'channel_id asc,cert asc');
        //Tpl::output('show_page', $diamond_jiajialv->showpage(2));
        Tpl::output('giftlist', $giftlist);
        //Tpl::output('position', array(0=> '底部', 1 => '顶部'));
        Tpl::output('good_type', $this->good_type);
        Tpl::output('sale_way', $this->sale_way);
        Tpl::output('status', $this->status);
        Tpl::output('cert', $cert);
        $this->profile_menu('diamondjiajialvlist', 'diamondjiajialvlist');
        Tpl::showpage('gift_list.list');
    }

    /**
     * 关联版式添加
     */
    public function plate_addOp() {
        if (chksubmit()) {
            // 验证表单
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                    array("input" => $_POST["store_id"], "require" => "true", "message" => '请选择体验店'),
                    array("input" => $_POST["carat_min"], "require" => "true", "message" => '请石重最小范围'),
                    array("input" => $_POST["carat_max"], "require" => "true", "message" => '请石重最大范围'),
                    array("input" => $_POST["cert"], "require" => "true", "message" => '请选择证书类型'),
                    array("input" => $_POST["good_type"], "require" => "true", "message" => '请选择货品类型'),
                    array("input" => $_POST["jiajialv"], "require" => "true", "message" => '请输入加价率')
            );
            $error = $obj_validate->validate();
            if ($error != '') {
                showDialog(L('error') . $error, urlShop('diamond_jiajialv', 'index'));
            }
            $insert = array();
            $insert['channel_id']     = $_POST['store_id'];
            $insert['carat_min'] = $_POST['carat_min'];
            $insert['carat_max']  = $_POST['carat_max'];
            $insert['cert']  = $_POST['cert'];
            $insert['good_type']  = $_POST['good_type'];
            $insert['jiajialv']  = $_POST['jiajialv'];
            $insert['status']  = $_POST['status'];
            //$insert['store_id']       = $_SESSION['store_id'];
            $result = Model('diamond_jiajialv')->addDiamondJiajialv($insert);
            if ($result) {
                showDialog(L('nc_common_op_succ'), urlShop('diamond_jiajialv', 'index'),'succ');
            } else {
                showDialog(L('nc_common_op_fail'), urlShop('diamond_jiajialv', 'index'));
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
        Tpl::output('storelist', $this->get_storelist());
        Tpl::output('cert', $diamondattr['cert']);
        Tpl::output('good_type', $this->good_type);
        $this->profile_menu('plate_add', 'plate_add');
        Tpl::showpage('diamond_jiajialv.add');
    }

    /**
     * 关联版式编辑
     */
    public function plate_editOp() {
        if (chksubmit()) {
            $plate_id = intval($_POST['p_id']);
            if ($plate_id <= 0) {
                showMessage(L('wrong_argument'), '', '', 'error');
            }
            // 验证表单
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                    array("input" => $_POST["store_id"], "require" => "true", "message" => '请选择体验店'),
                    array("input" => $_POST["carat_min"], "require" => "true", "message" => '请石重最小范围'),
                    array("input" => $_POST["carat_max"], "require" => "true", "message" => '请石重最大范围'),
                    array("input" => $_POST["cert"], "require" => "true", "message" => '请选择证书类型'),
                    array("input" => $_POST["good_type"], "require" => "true", "message" => '请选择货品类型'),
                    array("input" => $_POST["jiajialv"], "require" => "true", "message" => '请输入加价率')
            );
            $error = $obj_validate->validate();
            if ($error != '') {
                showDialog(L('error') . $error, urlShop('diamond_jiajialv', 'index'));
            }
            $update = array();
            //$update['id'] = $plate_id;
            $update['channel_id']     = $_POST['store_id'];
            $update['cert']  = $_POST['cert'];
            $update['good_type']  = $_POST['good_type'];
            $update['carat_min'] = $_POST['carat_min'];
            $update['carat_max']  = $_POST['carat_max'];
            $update['jiajialv']  = $_POST['jiajialv'];
            $update['status']  = $_POST['status'];
            $where = array();
            $where['id']  = $plate_id;
            //$where['store_id']  = $_SESSION['store_id'];
            $result = Model('diamond_jiajialv')->editDiamondJiajialv($update, $where);
            if ($result) {
                showDialog(L('nc_common_op_succ'), urlShop('diamond_jiajialv', 'index'),'succ');
            } else {
                showDialog(L('nc_common_op_fail'), urlShop('diamond_jiajialv', 'index'));
            }
        }
        $plate_id = intval($_GET['p_id']);
        if ($plate_id <= 0) {
            showMessage(L('wrong_argument'), '', '', 'error');
        }
        $plate_info = Model('diamond_jiajialv')->getDiamondJiajialvInfo(array('id' => $plate_id));
        Tpl::output('plate_info', $plate_info);
        $diamondattr = $this->get_diamond_attr();
        Tpl::output('storelist', $this->get_storelist());
        Tpl::output('cert', $diamondattr['cert']);
        Tpl::output('good_type', $this->good_type);
        $this->profile_menu('plate_edit', 'plate_edit');
        Tpl::showpage('diamond_jiajialv.add');
    }

    /**
     * 删除关联版式
     */
    public function drop_diamond_jiajialvOp() {
        $plate_id = $_GET['p_id'];
        if (!preg_match('/^[\d,]+$/i', $plate_id)) {
            showDialog(L('wrong_argument'), '', 'error');
        }
        $plateid_array = explode(',', $plate_id);
        $return = Model('diamond_jiajialv')->delDiamondJiajialv(array('id' => array('in', $plateid_array)));
        if ($return) {
            showDialog(L('nc_common_del_succ'), 'reload', 'succ');
        } else {
            showDialog(L('nc_common_del_fail'), '', 'error');
        }
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
            case 'diamondjiajialvlist':
                $menu_array = array(
                    array('menu_key' => 'diamondjiajialvlist', 'menu_name' => '赠品列表', 'menu_url' => urlShop('gift_list', 'diamondjiajialvlist'))
                );
                break;
            case 'plate_add':
                $menu_array = array(
                    array('menu_key' => 'diamondjiajialvlist', 'menu_name' => '赠品列表', 'menu_url' => urlShop('gift_list', 'diamondjiajialvlist')),
                    array('menu_key' => 'plate_add', 'menu_name' => '添加裸钻加价率', 'menu_url' => urlShop('gift_list', 'plate_add'))
                );
                break;
            case 'plate_edit':
                $menu_array = array(
                    array('menu_key' => 'diamondjiajialvlist', 'menu_name' => '赠品列表', 'menu_url' => urlShop('gift_list', 'diamondjiajialvlist')),
                    array('menu_key' => 'plate_add', 'menu_name' => '添加裸钻加价率', 'menu_url' => urlShop('gift_list', 'plate_add')),
                    array('menu_key' => 'plate_edit', 'menu_name' => '编辑裸钻加价率', 'menu_url' => urlShop('gift_list', 'plate_edit'))
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
