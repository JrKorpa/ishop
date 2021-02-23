<?php
/**
 * 仓库管理
 *
 *
 *
 * *  (c) 2015-2018 . (http://www.kela.cn)
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 * @since      珂兰技术中心提供技术支持
 */

defined('INTELLIGENT_SYS') or exit ('Access Invalid!');
class store_warehouseControl extends BaseSellerControl{

    protected $ts_warehouse = array('1','2','3','13');
    public function __construct() {
      parent::__construct() ;
        
    }

    /**
     * 单据查询
     *
     */
    public function indexOp() {
        $where = array();
        //$where['company_id'] = $_SESSION['store_company_id'];
        $where['store_id'] = $_SESSION['store_id'];
        if (trim($_GET['w_name']) != '') {
            $where['name'] = array('like', '%'.trim($_GET['w_name']).'%');
        }
        if (in_array($_GET['is_enabled'], array('0','1'))) {
            $where['is_enabled'] = $_GET['is_enabled'];
        }
        $warehouse = Model('erp_warehouse');
        $house_list = $warehouse->getWareHouseList($where, '*', 10);
        $management_api = data_gateway('imanagement');
        $type = $management_api->get_dictlist(array('name'=>'warehouse.type'));
        Tpl::output('show_page', $warehouse->showpage(2));
        Tpl::output('type', $type);
        Tpl::output('house_list', $house_list);
        Tpl::output('is_enabled', array(0=> '无效', 1 => '有效'));
        //门店
        //$chain_list = Model('chain')->getChainList(array('store_id' => $_SESSION['store_id']), '*', 1000);
        //$chain_list = empty($chain_list) ? [] : array_column($chain_list, 'chain_name','chain_id');
        //Tpl::output('chain_list', $chain_list);
        $this->profile_menu('index', 'index');
        Tpl::showpage('store_warehouse.index');
    }

    public function addOp() {
        if (chksubmit()) {
            // 验证表单
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                    array("input" => $_POST["w_name"], "require" => "true", "message" => '请填写仓库名称'),
                    //array("input" => $_POST["chain_id"], "require" => "true", "message" => '请选择店面'),
            );
            $error = $obj_validate->validate();
            if ($error != '') {
                showDialog(L('error') . $error, '');
            }
            //同名仓库名查询
            $sameHouse = Model('erp_warehouse')->getWareHouseInfo(array('name'=>trim($_POST['w_name'])));
            if(!empty($sameHouse)){
                showDialog("仓库名重复",'');
            }
            $insert = array();
            $insert['name']     = $_POST['w_name'];
            $insert['code']     = $_POST['code'];
            $insert['remark'] = $_POST['remark'];
            //$insert['chain_id']  = $_POST['chain_id'];
            $insert['store_id']  = $_SESSION['store_id'];
            $insert['company_id']  = $_SESSION['store_company_id'];
            $insert['company_name']  = $_SESSION['store_company_name'];
            $insert['store_name']  = $_SESSION['store_name'];
            //$insert['lock'] = 1;
            $insert['type'] = $_POST['type'];
            $insert['diamond_warehouse'] = 0;
            $insert['is_default'] = 1;
            $insert['create_time'] = date('Y-m-d H:i:s');
            $insert['create_user'] = $_SESSION['seller_name'];
            $insert['is_enabled'] = $_POST['is_enabled'];
            $insert['is_system'] = in_array($insert['type'],$this->ts_warehouse) ? 1 : 0 ;
            //$insert['level'] = $_POST['level'];
            $result = Model('erp_warehouse')->addWareHouse($insert);
            if ($result) {
                $box = array('box_name'=>'0-00-0-0', 'house_id'=>$result, 'is_enabled'=>1, 'note'=>'默认柜', 'is_lock'=>0);
                $res = Model('erp_box')->addBox($box);
                if($res){
                    showDialog(L('nc_common_op_succ'), urlShop('store_warehouse', 'index'),'succ');
                }else{
                    showDialog(L('nc_common_op_fail'), '');
                }
            } else {
                showDialog(L('nc_common_op_fail'), '');
            }
        }
        $management_api = data_gateway('imanagement');
        $type = $management_api->get_dictlist(array('name'=>'warehouse.type'));
        //门店
        //$chain_list = Model('chain')->getChainList(array('store_id' => $_SESSION['store_id']), '*', 1000);
        Tpl::output('type', $type);
        //Tpl::output('chain_list', $chain_list);
        $this->profile_menu('add', 'add');
        Tpl::showpage('store_warehouse.add');
    }

    public function editOp() {
        if (chksubmit()) {
            $house_id = intval($_POST['house_id']);
            if ($house_id <= 0) {
                showDialog(L('wrong_argument'),'');
            }
            // 验证表单
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                    array("input" => $_POST["w_name"], "require" => "true", "message" => '请填写仓库名称'),
                    //array("input" => $_POST["chain_id"], "require" => "true", "message" => '请选择店面'),
            );
            $error = $obj_validate->validate();
            if ($error != '') {
                showDialog(L('error') . $error,'');
            }
            //同名仓库名查询
            $sameHouse = Model('erp_warehouse')->getWareHouseInfo(array('name'=>trim($_POST['w_name']),'house_id'=>array('neq',$house_id)));
            if(!empty($sameHouse)){
                showDialog("仓库名重复",'');
            }

            //后库、柜面、待取、维修库不能更新成其他类型
            $house_info = Model('erp_warehouse')->getWareHouseInfo(array('house_id' => $house_id));
            if($house_info['is_system'] == 1 && $house_info['type'] != $_POST['type']){
                showDialog("后库、柜面、待取、维修库不能更改仓库类型",'');
            }

            $update = array();
            $update['name']     = $_POST['w_name'];
            $update['code']     = $_POST['code'];
            $update['remark'] = $_POST['remark'];
            //$update['chain_id']  = $_POST['chain_id'];
            $update['store_id']  = $_SESSION['store_id'];
            $update['company_id']  = $_SESSION['store_company_id'];
            $update['company_name']  = $_SESSION['store_company_name'];
            $update['store_name']  = $_SESSION['store_name'];
            //$update['lock'] = 1;
            $update['type'] = $_POST['type'];
            $update['diamond_warehouse'] = 0;
            $update['is_default'] = 1;
            $update['create_time'] = date('Y-m-d H:i:s');
            $update['create_user'] = $_SESSION['seller_name'];
            $update['is_enabled'] = $_POST['is_enabled'];
            $update['is_system'] = in_array($update['type'],$this->ts_warehouse) ? 1 : 0 ;

            $where = array();
            $where['house_id']  = $house_id;
            $where['store_id']  = $_SESSION['store_id'];
            $result = Model('erp_warehouse')->editWareHouse($update, $where);
            if ($result) {
                showDialog(L('nc_common_op_succ'), urlShop('store_warehouse', 'index'),'succ');
            } else {
                showDialog(L('nc_common_op_fail'),'');
            }
        }
        $house_id = intval($_GET['house_id']);
        if ($house_id <= 0) {
            showMessage(L('wrong_argument'), '', '', 'error');
        }
        $house_info = Model('erp_warehouse')->getWareHouseInfo(array('house_id' => $house_id));//, 'store_id' => $_SESSION['store_id']
        $management_api = data_gateway('imanagement');
        $type = $management_api->get_dictlist(array('name'=>'warehouse.type'));
        Tpl::output('type', $type);
        Tpl::output('house_info', $house_info);
        //门店
        $chain_list = Model('chain')->getChainList(array('store_id' => $_SESSION['store_id']), '*', 1000);
        Tpl::output('chain_list', $chain_list);
        $this->profile_menu('edit', 'edit');
        Tpl::showpage('store_warehouse.add');
    }

    /**
     * 删除仓库
     */
    public function dropOp() {
        $house_id = $_GET['house_id'];
        if (!preg_match('/^[\d,]+$/i', $house_id)) {
            showDialog(L('wrong_argument'), '', 'error');
        }
        $model_warehouse = new erp_warehouseModel();   
        $model_box = new erp_boxModel();     
        $houseid_array = explode(',', $house_id);
                
        $preDropHouse = $model_warehouse->preDropWarehouse($houseid_array);
        if(!$preDropHouse){
            showDialog("仓库有商品，不能删除", '', 'error');
        }

        $preDropHouse = $model_warehouse->preDropWarehouseIssystem($houseid_array);
        if(!$preDropHouse){
            showDialog("(后库、柜面、待取、维修库)属于系统内置仓库不能删除", '', 'error');
        } 
        try{
            $model_warehouse->beginTransaction();
            $res = $model_warehouse->delWareHouse(array('house_id' => array('in', $houseid_array), 'store_id' => $_SESSION['store_id']));
            if($res===false){
                throw new Exception("删除仓库失败");
            }
            $res = $model_box->delBox(array('house_id'=>array('in',$houseid_array)));
            if($res===false){
                throw new Exception("删除仓库内的柜位失败");
            }
            $model_warehouse->commit();
            showDialog(L('nc_common_del_succ'), 'reload', 'succ');
        }catch (Exception $e){
            $model_warehouse->rollback();
            showDialog($e->getMessage(), '', 'error');
        }
    }

    /**
     * 新增储位
     *
     */
    public function add_boxOp() {
        if (chksubmit()) {
            $house_id = intval($_POST['house_id']);
            if ($house_id <= 0) {
                //showMessage(L('wrong_argument'), '', '', 'error');
                echo json_encode(callback(false,L('wrong_argument')));
                exit();
            }
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                    array("input" => $_POST["box_name"], "require" => "true", "message" => '请填写储位名称'),
            );
            $error = $obj_validate->validate();
            if ($error != '') {
                //showMessage(L('error') . $error, '', '', 'error');
                echo json_encode(callback(false,L('error') . $error));
                exit();
            }
            
            //同名仓库名查询
            //$_GET['debug'] = 1;
            $sameBox = Model('erp_box')->getBoxInfo(array('box_name'=>trim($_POST['box_name']),'house_id'=>$house_id));
            if(!empty($sameBox)){
                echo json_encode(callback(false,"柜位名重复"));
                exit();
            }
            
            $insert = array();
            $insert['box_name']     = $_POST['box_name'];
            $insert['house_id'] = $house_id;
            $update['is_default']  = (int)$_POST['is_default'];
            $insert['is_enabled']  = (int)$_POST['is_enabled'];
            $insert['note']  = $_POST['note'];
            //$insert['is_lock'] = $_POST['is_lock'];
           
            $box_id = Model('erp_box')->insert($insert);
            if ($box_id) {
                $box_info = Model('erp_box')->where(array('box_id'=>$box_id))->find();
                $result = callback(true,"保存成功",$box_info);
                echo json_encode($result);
                exit();            
            } else {
                $result = callback(false,"保存失败");
                echo json_encode($result);
                exit();  
            }

        }
        $house_id = intval($_GET['house_id']);
        if ($house_id <= 0) {
            showMessage(L('wrong_argument'), '', '', 'error');
        }
        Tpl::showpage('erp_box.add', 'null_layout');
    }

     /**
     * 编辑储位
     *
     */
    public function edit_boxOp() {
        if (chksubmit()) {
            $box_id = intval($_POST['box_id']);
            $house_id = intval($_POST['house_id']);
            if ($box_id <= 0) {
                //showMessage(L('wrong_argument'), '', '', 'error');
                echo json_encode(callback(false,L('wrong_argument')));
                exit();
            }
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                    array("input" => $_POST["box_name"], "require" => "true", "message" => '请填写储位名称'),
            );
            $error = $obj_validate->validate();
            if ($error != '') {
                //showMessage(L('error') . $error, '', '', 'error');
                echo json_encode(callback(false,L('error') . $error));
                exit();
            }            
            //同名仓库名查询
            $sameBox = Model('erp_box')->getBoxInfo(array('box_name'=>trim($_POST['box_name']),'house_id'=>$house_id,'box_id'=>array('neq',$box_id)));
            if(!empty($sameBox)){
                echo json_encode(callback(false,"柜位名重复"));
                exit();
            }
            
            $update = array();
            $update['box_name']     = $_POST['box_name'];
            $update['is_default']  = (int)$_POST['is_default'];
            $update['is_enabled']  = (int)$_POST['is_enabled'];
            $update['note']  = $_POST['note'];
            //$update['is_lock'] = $_POST['is_lock'];
           
            $result = Model('erp_box')->where(array('box_id'=>$box_id))->update($update);
            if ($result) {
                $box_info = Model('erp_box')->where(array('box_id'=>$box_id))->find();
                $result = callback(true,"保存成功",$box_info);
                echo json_encode($result);
                exit();               
            } else {
                $result = callback(false,"保存失败");
                echo json_encode($result);
                exit();  
            }

        }
        $box_id = intval($_GET['box_id']);
        if ($box_id <= 0) {
            showMessage(L('wrong_argument'), '', '', 'error');
        }
        $box_info = Model('erp_box')->where(array('box_id'=>$box_id))->find();
        Tpl::output('box_info', $box_info);
        Tpl::showpage('erp_box.add', 'null_layout');
    }

    /**
     * 删除储位
     */
    public function del_boxOp() {
        $box_id = intval($_POST['box_id']);
        if ($box_id<=0) {
            //showDialog(L('wrong_argument'), '', 'error');
            $result = callback(false,L('wrong_argument'));
            exit(json_encode($result));
        }        
        $model_warehouse = new erp_warehouseModel();
        $preDropBox = $model_warehouse->preDropBox(array($box_id));
        if(!$preDropBox){
            //showDialog("柜位有商品，不能删除", '', 'error');
            $result = callback(false,"柜位有商品，不能删除");
            exit(json_encode($result));
        }
        $return = Model('erp_box')->table('erp_box')->delete($box_id);
        if ($return) {
            $result = callback(true,"删除成功");
            exit(json_encode($result));
        } else {
            $result = callback(false,"删除失败");
            exit(json_encode($result));
        }
    }
    
    public function get_house_list_ajaxOp(){
        $warehouse_model = new erp_warehouseModel();
        $where = array('is_enabled'=>1);
        if(!empty($_GET['company_id'])){
            $where['company_id'] = $_GET['company_id'];
        }
        if(!empty($_GET['house_type'])){
            $where['type'] = $_GET['house_type'];
            if($_GET['house_type'] ==11 && $_GET['company_id']==58){
                 $where['house_id'] = 606;//总公司维修库
            }
        }
        $house_list = $warehouse_model->getWareHouseList($where,"house_id,name,store_id",5000);
        if (empty($house_list)) {
            echo 'false';exit();
        }
        echo json_encode($house_list);
    }
    /**
     * 获取储位
     *
     */
    public function get_box_list_ajaxOp() {
        $house_id = intval($_GET['house_id']);
        if ($house_id <= 0) {
            echo 'false1';exit();
        }
        $model = new erp_warehouseModel();
        $box_list = $model->getBoxList($house_id);
        if (empty($box_list)) {
            echo 'false2';exit();
        }
        echo json_encode($box_list);
    }

    /**
     * 获取仓库
     *
     */
    public function get_warehouse_list_ajaxOp() {
        $house_id = intval($_GET['house_id']);
        if ($house_id <= 0) {
            echo 'false';exit();
        }
        $box_list = Model('erp_warehouse')->getWareHouseList(array('company_id'=>$house_id));
        if (empty($box_list)) {
            echo 'false';exit();
        }
        echo json_encode($box_list);
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
            case 'index':
                $menu_array = array(
                    array('menu_key' => 'index', 'menu_name' => '仓库列表', 'menu_url' => urlShop('store_warehouse', 'index')),
                );
                break;
            case 'add':
                $menu_array = array(
                    array('menu_key' => 'index', 'menu_name' => '仓库列表', 'menu_url' => urlShop('store_warehouse', 'index')),
                    array('menu_key' => 'add', 'menu_name' => '添加仓库', 'menu_url' => urlShop('store_warehouse', 'add'))
                );
                break;
            case 'edit':
                $menu_array = array(
                    array('menu_key' => 'index', 'menu_name' => '仓库列表', 'menu_url' => urlShop('store_warehouse', 'index')),
                    array('menu_key' => 'add', 'menu_name' => '添加仓库', 'menu_url' => urlShop('store_warehouse', 'add')),
                    array('menu_key' => 'edit', 'menu_name' => '编辑仓库', 'menu_url' => urlShop('store_warehouse', 'edit'))
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