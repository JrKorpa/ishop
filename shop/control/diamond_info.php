<?php
/**
 * 裸钻列表
 *
 *
 *
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */



defined('INTELLIGENT_SYS') or exit ('Access Invalid!');
class diamond_infoControl extends BaseSellerControl {

    public $status = array(1 => '上架', 0=> '下架');
    public $good_type = array(1 => '现货',2=> '期货');

    public function __construct() {
        parent::__construct();
    }

    public function indexOp() {
        $this->diamondinfolistOp();
    }

    /**
     * 获取体验店列表
     */
    public function get_storelist()
    {
        $model_store = Model('store');
        $storelist = $model_store->getStoreOnlineList(array());
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
     * 裸钻列表
     */
    public function diamondinfolistOp() {
        // 裸钻
        $where = array(
            'status'=>1,
            'not_from_ad' => array('11'), //直营店期货(kgk除外) enjoy 17
            'warehouse' => array('HPLZK', 'COM'), //所有总部期货(COM排除kgk+enjoy) + 自己门店现货 +浩鹏公司现货（仓库HPLZK）
        );
        $curpage = isset($_GET['curpage'])?$_GET['curpage']:1;
        //$where['store_id'] = $_SESSION['store_id'];
        if (trim($_GET['store_name']) != '') {
            $where['store_name'] = array('like', '%'.trim($_GET['store_name']).'%');
        }

        if (isset($_GET['shape']) && !empty($_GET['shape'])) {
            $where['shape'] = array(intval($_GET['shape']));
        }
        if (isset($_GET['color']) && !empty($_GET['color'])) {
            $where['color'] = array($_GET['color']);
        }
        if (isset($_GET['cut']) && !empty($_GET['cut'])) {
            $where['cut'] = array($_GET['cut']);
        }
        if (isset($_GET['clarity']) && !empty($_GET['clarity'])) {
            $where['clarity'] = array($_GET['clarity']);
        }
        if (isset($_GET['symmetry']) && !empty($_GET['symmetry'])) {
            $where['symmetry'] = array($_GET['symmetry']);
        }
        if (isset($_GET['polish']) && !empty($_GET['polish'])) {
            $where['polish'] = array($_GET['polish']);
        }

        if (isset($_GET['carat_min']) && !empty($_GET['carat_min'])) {
            $where['carat_min'] = $_GET['carat_min'];
        }
        if (isset($_GET['carat_max']) && !empty($_GET['carat_max'])) {
            $where['carat_max'] = $_GET['carat_max'];
        }



        if (isset($_GET['cert_id']) && !empty($_GET['cert_id'])) {
            $where['cert_id'] = $_GET['cert_id'];
        }
        if (in_array($_GET['good_type'], array('1','2'))) {
            $where['good_type'] = $_GET['good_type'];
        }
        if (in_array($_GET['status'], array('0','1'))) {
            $where['status'] = $_GET['status'];
        }
        $diamondattr = $this->get_diamond_attr();
        $cert = $diamondattr['cert'];
        if (!empty($_GET['cert'])) {
            $where['cert'][] = $_GET['cert'];
        }
        $diamond_api = data_gateway('idiamond');
        $byApi = $diamond_api->get_diamond_list(10, $curpage, $where);
        $diamond_jiajialv = Model("diamond_jiajialv");
        $diamondlist = isset($byApi['return_msg']['data'])?$byApi['return_msg']['data']:array();
        $shape_all = $diamond_api->get_diamond_index(array('shape_all'));
        $shape_all = isset($shape_all['return_msg'])?$shape_all['return_msg']:array();
        //var_dump($shape_all['shape_all']);die;
        //$this->calc_dia_channel_price($diamondlist, 300);
        //var_dump($diamondlist);die;
        if(!empty($diamondlist)){
            $diamond_api = data_gateway('idiamond');
            $diamond_api->multiply_jiajialv($diamondlist,$_SESSION['store_id'],$_SESSION['store_company_id']);
        }
        $page = new Page();
        $page->setEachNum(10);
        $page->setNowPage($curpage);
        $page->setTotalNum($byApi['return_msg']['recordCount']);
        $page->setTotalPage($byApi['return_msg']['pageCount']);
        $page->setTotalPageByNum($byApi['return_msg']['pageCount']);
        $page->setStyle(2);
        
        $show_chengben = $this->check_seller_limit('limit_show_goods_chengben');
        Tpl::output('show_chengben', $show_chengben);

        Tpl::output('show_page',$page->show(2));
        Tpl::output('good_type', $this->good_type);
        Tpl::output('status', $this->status);
        Tpl::output('cert', $cert);
        Tpl::output('shape_all',$shape_all['shape_all']);
        Tpl::output('diamondlist', $diamondlist);
        $this->profile_menu('diamond_info', 'diamond_info');
        Tpl::showpage('diamond_info.list');
    }

    /**
    * 计算裸钻加价率
    * @diamond_list 裸钻信息
    * @company_id 用户所属公司
    * @where array 检索条件
    **/
    public function calc_dia_channel_price(&$diamond_list, $company_id) {
        if (empty($diamond_list)) return;
        if ($company_id == '666' || $company_id == '488') 
        {
            $calc_func = function(&$d) 
            {
                if ($d['cert'] == 'HRD-S') 
                {
                    $d['shop_price'] = round($d['shop_price'] * 1.1);
                }
            };
            if (count($diamond_list) == count($diamond_list, 1)) 
            {
                $calc_func($diamond_list);
            } 
            else 
            {
                foreach ($diamond_list as &$d) 
                {
                    $calc_func($d);
                }
            }
            return;
        } 
        /*$sql = "select channel_id, s.channel_name from cuteframe.user_channel uc
        inner join cuteframe.sales_channels s on s.id = uc.channel_id
        inner join cuteframe.company c on s.company_id = c.id
        where user_id = '{$company_id}' and c.id = '{$company_id}'";*/
        $sql = "select b.id, b.channel_name from company a 
            inner join sales_channels b on b.company_id = a.id
            where a.id = '{$company_id}'";
        $channel_list = $this->db->getAll($sql);
        if (empty($channel_list))
        {
            if (count($diamond_list) == count($diamond_list, 1)) 
            {
                $diamond_list['shop_price_recalc'] = 0;
            } 
            else 
            {
                foreach ($diamond_list as &$d) 
                {
                    $d['shop_price_recalc'] = 0;
                }
            }
            return;
        }
        // TODO: 默认一个公司的所有渠道都是相同加价率
        $channel_id = isset($channel_list[0]['channel_id'])?$channel_list[0]['channel_id']:0;
        $sql = "select * from front.diamond_channel_jiajialv where channel_id={$channel_id} and status = 1";
        $channel_price_configs = $this->db->getAll($sql);
        $calc_func = function(&$d) use($channel_price_configs) 
        {
            if ($d['pifajia'] == 0) 
            {
                $d['shop_price_recalc'] = 0;
                return;
            }
            foreach ($channel_price_configs as $cfg)
            {
                if ($cfg['cert'] == $d['cert'] && 
                    $d['good_type'] == $cfg['good_type'] && 
                    $cfg['carat_min'] <= $d['carat'] && 
                    $d['carat'] < $cfg['carat_max']) 
                {
                    $d['shop_price'] = round($d['pifajia'] * $cfg['jiajialv']);
                    $d['shop_price_recalc'] = 1;
                    break;
                }
            }
            
            if (!isset($d['shop_price_recalc'])) 
            {

                //获取对应证书类型的默认加价率数组
                $store_lz_jijialv_arr = paramsHelper::echoOptionText('store_lz_moren_jijialv',$d['cert']);
                if(empty($store_lz_jijialv_arr)){
                    //获取默认加价率
                    $lv = paramsHelper::echoOptionText('store_lz_moren_jijialv','default');
                }else{
                    //获取定义石重数组的健名
                    $carat_arr = paramsHelper::getParams("carat");
                    $carat_key_arr = array_keys($carat_arr);
                    //组成新的默认加价率关联数组
                    $carat_min_arr = array_combine($carat_key_arr,$store_lz_jijialv_arr);

                    $carat = "0";
                    //获取对应的钻重范围
                    foreach ($carat_key_arr as $v){
                        $carat_arr = explode('~',$v);
                        if(isset($carat_arr[1])){
                            if($d['carat'] >= $carat_arr[0] && $d['carat'] < $carat_arr[1]){
                                $carat = $v;
                                break;
                            }
                        }else{
                            if($d['carat'] >= $carat_arr[0]){
                                $carat = $v;
                                break;
                            }
                        }
                    }
                    //获取对应钻重的默认加价率
                    if(isset($carat_min_arr[$carat])){
                        $lv = $carat_min_arr[$carat];
                    }else{
                        $lv = paramsHelper::echoOptionText('store_lz_moren_jijialv','default');
                    }
                }
                //$lv =  $d['good_type'] == 1 ? 1.95 : 1.95;
                $d['shop_price'] = round($d['pifajia'] * $lv); //避免将成本价显示出来
                $d['shop_price_recalc'] = 0;
            }
        };
        
        if (count($diamond_list) == count($diamond_list, 1)) {

            $calc_func($diamond_list);
        } else {

            foreach ($diamond_list as &$d) {

                $calc_func($d);
            }
        }
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
                    array("input" => $_POST["jiajialv"], "require" => "true", "message" => '请输入')
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
                    array("input" => $_POST["jiajialv"], "require" => "true", "message" => '请输入')
            );
            $error = $obj_validate->validate();
            if ($error != '') {
                showDialog(L('error') . $error, urlShop('diamond_jiajialv', 'index'));
            }
            $update = array();
            $update['channel_id']     = $_POST['store_id'];
            $update['carat_min'] = $_POST['carat_min'];
            $update['carat_max']  = $_POST['carat_max'];
            $update['cert']  = $_POST['cert'];
            $update['good_type']  = $_POST['good_type'];
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
    public function drop_plateOp() {
        $plate_id = $_GET['p_id'];
        if (!preg_match('/^[\d,]+$/i', $plate_id)) {
            showDialog(L('wrong_argument'), '', 'error');
        }
        $plateid_array = explode(',', $plate_id);
        $return = Model('diamond_jiajialv')->delStorePlate(array('plate_id' => array('in', $plateid_array), 'store_id' => $_SESSION['store_id']));
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
            case 'diamond_info':
                $menu_array = array(
                    array('menu_key' => 'diamond_info', 'menu_name' => '裸钻列表', 'menu_url' => urlShop('diamond_info', 'diamond_info'))
                );
                break;
            case 'plate_add':
                $menu_array = array(
                    array('menu_key' => 'diamond_info', 'menu_name' => '裸钻列表', 'menu_url' => urlShop('diamond_info', 'diamond_info')),
                    array('menu_key' => 'plate_add', 'menu_name' => '添加裸钻', 'menu_url' => urlShop('diamond_info', 'plate_add'))
                );
                break;
            case 'plate_edit':
                $menu_array = array(
                    array('menu_key' => 'diamond_info', 'menu_name' => '裸钻列表', 'menu_url' => urlShop('diamond_info', 'diamond_info')),
                    array('menu_key' => 'plate_add', 'menu_name' => '添加裸钻', 'menu_url' => urlShop('diamond_info', 'plate_add')),
                    array('menu_key' => 'plate_edit', 'menu_name' => '编辑裸钻', 'menu_url' => urlShop('diamond_info', 'plate_edit'))
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
