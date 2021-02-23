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
class store_taskControl extends BaseSellerControl {

    public function __construct() {
        parent::__construct();
    }

    public function indexOp() {
        $this->storetasklistOp();
    }




    /**
     * 裸钻加价率列表
     */
    public function storetasklistOp() {
        // 裸钻加价率
        $where = array();
        $where['store_id'] = $this->store_info['store_id'];
        if ($_GET['year'] != '') {
            $where['year'] = $_GET['year'];
        }
        if ($_GET['month'] != '') {
            $where['month'] = $_GET['month'];
        }



        $store_task = new store_taskModel();
        $storetasklist = $store_task->getStoreTaskList($where, ' * ', 10, 'year desc,month desc');
        Tpl::output('show_page', $store_task->showpage(2));
        Tpl::output('storetasklist', $storetasklist);
        //Tpl::output('position', array(0=> '底部', 1 => '顶部'));
        $this->profile_menu('storetasklist', 'storetasklist');
        Tpl::showpage('store_task.list');
    }
    /**
     * 查看裸钻加价率日志
     */
    public function view_logsOp(){
        $id = $_GET['id'];//加价率ID
        $logModel = new store_task_logModel();
        $log_list = $logModel->getLogList(array('task_id'=>$id),"*",10,'id desc');
        Tpl::output('log_list',$log_list);
        Tpl::output('show_page',$logModel->showpage());
        Tpl::showpage('store_task.view_logs','simple_layout');
    }
    /**
     * 添加
     */
    public function store_task_addOp() {
        if (chksubmit()) {
            // 验证表单
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                    array("input" => $_POST["year"], "require" => "true", "message" => '请选择年份'),
                    array("input" => $_POST["month"], "require" => "true", "message" => '请选择月份'),
                    array("input" => $_POST["task"], "require" => "true", "message" => '请输入任务')
            );
            $error = $obj_validate->validate();
            if ($error != '') {
                showDialog(L('error') . $error, urlShop('store_task', 'store_task_add'));
            }



            $insert = array();

            $insert['year']     = $_POST['year'];
            $insert['month']  = $_POST['month'];
            $insert['task']  = $_POST['task'];
            $insert['add_time']  = time();
            $insert['store_id']       = $_SESSION['store_id'];

            //判断是否已经存在
            $condition = $insert;
            unset($condition['add_time']);
            unset($condition['task']);
            $store_task = new store_taskModel();
            $res = $store_task->getStoreTaskInfo($condition);
            if(!empty($res)){
                showDialog( "不能重复添加", urlShop('store_task', 'store_task_add'));
            }

            $task_id = $store_task->addStoreTask($insert);
            if ($task_id) {
                //添加日志
                $logData = array('task_id'=>$task_id,'remark'=>'添加任务:'.$insert['task'],'create_user'=>$_SESSION['seller_name'],'create_time'=>date("Y-m-d H:i:s"));
                Model("store_task_log")->addLog($logData);
                
                showDialog(L('nc_common_op_succ'), urlShop('store_task', 'index'),'succ');
            } else {
                showDialog(L('nc_common_op_fail'), urlShop('store_task', 'index'));
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

        Tpl::output('laiyuan', 'add');

        $this->profile_menu('store_task_add', 'store_task_add');
        Tpl::showpage('store_task.add');
    }

    /**
     * 关联版式编辑
     */
    public function store_task_editOp() {
        $store_task = new store_taskModel();
        if (chksubmit()) {
            $plate_id = intval($_POST['p_id']);
            if ($plate_id <= 0) {
                showMessage(L('wrong_argument'), '', '', 'error');
            }

            // 验证表单
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input" => $_POST["year"], "require" => "true", "message" => '请选择年份'),
                array("input" => $_POST["month"], "require" => "true", "message" => '请选择月份'),
                array("input" => $_POST["task"], "require" => "true", "message" => '请输入任务')
            );
            $error = $obj_validate->validate();
            if ($error != '') {
                showDialog(L('error') . $error, urlShop('store_task', 'index'));
            }


            $update = array();
            $update['id'] = $plate_id;
            $update['year']  = $_POST['year'];
            $update['month']  = $_POST['month'];
            $update['task']  = $_POST['task'];
            $where = array();
            $where['id']  = $plate_id;

            //判断是否已经存在
            $condition = $update;
            unset($condition['id']);
            unset($condition['task']);
            $condition['id'] = array('neq',$plate_id);
            $res = $store_task->getStoreTaskInfo($condition);
            if(!empty($res)){
                showDialog( "不能重复添加", urlShop('store_task', 'index'));
            }
            //print_r($update);exit;
            //$where['store_id']  = $_SESSION['store_id'];
            $result = $store_task->editStoreTask($update, $where);
            
            if ($result) {
                $logModel = new store_task_logModel();
                $logData = array('task_id'=>$plate_id,'remark'=>'编辑任务:'.$update['task'],'create_user'=>$_SESSION['seller_name'],'create_time'=>date("Y-m-d H:i:s"));
                $logModel->addLog($logData);                
                
                showDialog(L('nc_common_op_succ'), urlShop('store_task', 'index'),'succ');
            } else {
                showDialog(L('nc_common_op_fail'), urlShop('store_task', 'index'));
            }
        }
        $plate_id = intval($_GET['p_id']);
        if ($plate_id <= 0) {
            showMessage(L('wrong_argument'), '', '', 'error');
        }

        $store_task_info = $store_task->getStoreTaskInfo(array('id' => $plate_id));
        Tpl::output('store_task_info', $store_task_info);

        Tpl::output('laiyuan', 'edit');
        $this->profile_menu('store_task_edit', 'store_task_edit');
        Tpl::showpage('store_task.add');
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
            $logData = array('jiajialv_id'=>$plate_id,'remark'=>"删除加价率",'create_user'=>$_SESSION['seller_name'],'create_time'=>date("Y-m-d H:i:s"));
            Model('diamond_jiajialv_log')->addLog($logData);
            
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
            case 'storetasklist':
                $menu_array = array(
                    array('menu_key' => 'storetasklist', 'menu_name' => '任务列表', 'menu_url' => urlShop('store_task', 'storetasklist'))
                );
                break;
            case 'store_task_add':
                $menu_array = array(
                    array('menu_key' => 'storetasklist', 'menu_name' => '任务列表', 'menu_url' => urlShop('store_task', 'storetasklist')),
                    array('menu_key' => 'store_task_add', 'menu_name' => '添加任务', 'menu_url' => urlShop('store_task', 'store_task_add'))
                );
                break;
            case 'store_task_edit':
                $menu_array = array(
                    array('menu_key' => 'storetasklist', 'menu_name' => '任务列表', 'menu_url' => urlShop('store_task', 'storetasklist')),
                    array('menu_key' => 'store_task_add', 'menu_name' => '添加任务', 'menu_url' => urlShop('store_task', 'store_task_add')),
                    array('menu_key' => 'store_task_edit', 'menu_name' => '编辑任务', 'menu_url' => urlShop('store_task', 'store_task_edit'))
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
