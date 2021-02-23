<?php

defined('INTELLIGENT_SYS') or exit ('Access Invalid!');
class store_goods_itemsControl extends BaseSellerControl{
	
public function __construct() {
   parent::__construct() ;
        Language::read('store_bill,store_goods_index');
    }
    //库存查询
    public function indexOp() {
        $condition=$this->convertParams($_GET);
        $goods_items_model = Model('goods_items');
        $condition["company_id"]=$_SESSION['store_company_id'];
        $item_list = $goods_items_model->getGoodsItemsList($condition,null, 10);
        Tpl::output('show_page', $goods_items_model->showpage());
        Tpl::output('item_list', $item_list);
        /*$show_price = false;
        if(in_array($_SESSION['seller_group_name'], array('管理员', '管理组'))){
            $show_price = true;
        }
        Tpl::output('show_price', $show_price);
        */
        $erp_warehouse_model =Model('erp_warehouse');
        //$erp_box_model =Model('erp_box');
        $house_list = $erp_warehouse_model->getWareHouseList(array('store_id' =>$_SESSION['store_id']), 'house_id,name', 5000);
        //$box_list = $erp_box_model->getBoxList(array('erp_warehouse.store_id'=>$_SESSION['store_id']));
       // $box_list = empty($box_list)?[]: array_column($box_list,"box_name",'box_id');
        $company_list = $goods_items_model->getCompanyList("id,company_name");
       // $prc_list=$goods_items_model->getSupplierList("sup_id,sup_name");
        $product_type_list = $goods_items_model->getProductTypeList("product_type_id,product_type_name",array("product_type_status"=>1));
        $cat_type_list = $goods_items_model->getCartTypeList("cat_type_id,cat_type_name",array("cat_type_status"=>1));
        $xilie_list = $goods_items_model->getXiLieList("id,name");
        $default_img_url = BASE_SITE_URL."/data/upload/shop/common/default_goods_image_240.gif";
        $show_chengben = $this->check_seller_limit('limit_show_goods_chengben');
        Tpl::output('show_chengben', $show_chengben);
        Tpl::output('default_img_url', $default_img_url);
        Tpl::output('house_list', $house_list);
        //Tpl::output('box_list', $box_list);
        Tpl::output('company_list', $company_list);
        //Tpl::output('prc_list', $prc_list);
        Tpl::output('product_type_list', $product_type_list);
        Tpl::output('cat_type_list', $cat_type_list);
        Tpl::output('xilie_list', $xilie_list);
        $menu_key = empty($_GET['status'])?'index':$_GET['status'];
        $this->profile_menu('index', $menu_key);
        Tpl::showpage('store_goods_items.index');
    }

    private function convertParams($params){
        unset($params["act"]);
        unset($params["op"]);
        $condition = array();
        foreach ($params as $key=>$item){
            if($key=="goods_id"&&trim($params['goods_id'])!=''){
                //去除多余空格换行符
                $params['goods_id'] = preg_replace("/\s+/is",' ',trim($params['goods_id']));
                $params['goods_id'] = str_replace(" ",',',trim($params['goods_id']));
                $item_id_arr = explode(',',$params['goods_id']);
                if(count($item_id_arr)==1){
                    $condition['goods_items.goods_id'] = $item_id_arr[0];
                }else{
                    $condition['goods_items.goods_id'] = array('in',$item_id_arr);
                }
            }else if($key=="goods_sn"&&trim($params['goods_sn'])!=''){
                $params['goods_sn'] = preg_replace("/\s+/is",' ',trim($params['goods_sn']));
                $params['goods_sn'] = str_replace(" ",',',trim($params['goods_sn']));
                $goods_sn_arr = explode(',',$params['goods_sn']);
                if(count($goods_sn_arr)==1){
                    $condition['goods_items.goods_sn'] = $goods_sn_arr[0];
                }else{
                    $condition['goods_items.goods_sn'] = array('in',$goods_sn_arr);
                }
            }else if($key=="goods_name"&&!empty($params['goods_name'])){
                $condition['goods_items.goods_name'] = array('like',"%{$params['goods_name']}%");
            }else if($key=="prc_name"&&!empty($params['prc_name'])){
                $condition['goods_items.prc_name'] = array('like',"%{$params['prc_name']}%");
            }else if($key=="is_bind_order"&&!empty($params['is_bind_order'])){
                if($params["is_bind_order"]==1){
                    $condition['goods_items.order_detail_id'] = array('gt',0);
                }else{
                    $condition['goods_items.order_detail_id'] = 0;
                }
            }else if($key=="curpage"&&!empty($params['curpage'])){
                //$condition[$key] = $params[$key];
            }else if($key=="status"&&!empty($params['status'])){
                $condition['goods_items.is_on_sale'] = $params[$key];
            }else if($key=="xilie"&&!empty($params['xilie'])){
                $condition['base_style_info.xilie'] = array('like',",%{$params['xilie']}%,");
            }else if(($key=="cart_min"&&!empty($params['cart_min']))||($key=="cart_max"&&!empty($params['cart_max']))){
                $condition['goods_items.zuanshidaxiao'] = array('between', array($params['cart_min'],$params['cart_max']));
            }else if(($key=="zhiquan_min"&&!empty($params['zhiquan_min']))||($key=="zhiquan_max"&&!empty($params['zhiquan_max']))){
                $condition['goods_items.shoucun'] = array('between', array($params['zhiquan_min'],$params['zhiquan_max']));
            }else if(($key=="jinzhong_min"&&!empty($params['jinzhong_min']))||($key=="jinzhong_max"&&!empty($params['jinzhong_max']))){
                $condition['goods_items.jinzhong'] = array('between', array($params['jinzhong_min'],$params['jinzhong_max']));
            }else{
                if(trim($params[$key])!='') $condition['goods_items.'.$key] = $params[$key];
            }
        }
        return $condition;
    }

    /**
     * 查看货品日志
     */
    public function view_logsOp(){
        if(empty($_GET['goods_id'])){
            exit('empty goods_id');
        }
        $goods_itemid = $_GET['goods_id'];
        $goods_items_model = new goods_itemsModel();//Model('goods_items');
        $goods_log_model = new goods_items_logModel();
        $goods_items_info = $goods_items_model->getGoodsItemInfo(array('goods_id'=>$goods_itemid));
        $goods_log_list = $goods_log_model->getGoodsItemLogList(array('goods_itemid'=>$goods_itemid),"*",5);
        Tpl::output('show_page', $goods_log_model->showpage());


        $goods_state_list = $goods_items_model->getGoodsItemStatusList();
        
        $log_type_list = $goods_log_model->getLogTypeList();
        Tpl::output('goods_state_list',$goods_state_list);

        
        Tpl::output('log_type_list', $log_type_list);
        
        Tpl::output('goods_items_info', $goods_items_info);
        Tpl::output('goods_log_list', $goods_log_list);
        Tpl::showpage('store_goods_items.view_logs','simple_layout');
    }
    //批量上下架
    public function editOp() {
        if (chksubmit()) {
            $items = $_POST['items'];
            if ($items =='') {
                showMessage(L('wrong_argument'), '', '', 'error');
            }
            $update = array();
            $update['status'] = $_POST['status'];
            $where = array();
            $item_ids = explode(',', $items);
            $where['item_id']  =array('in',$item_ids) ;
            $where['store_id']  = $_SESSION['store_id'];
            $result = Model('goods_items')->editGoodsItems($update, $where);
            if ($result) {
                //记录日志
                $log_info = array();
                $log_info['log_time'] = TIMESTAMP;
                $log_info['log_seller_id'] = $_SESSION['seller_id'];
                $log_info['log_seller_name'] = $_SESSION['seller_name'];
                $log_info['log_store_id'] = $_SESSION['store_id'];
                $log_info['log_seller_ip'] = getIp();
                $log_info['log_url'] = $_GET['act'].'&'.$_GET['op'];
                $log_info['log_state'] =1;
                foreach ($item_ids as  $item_id) {
                     $log_info['item_id'] = $item_id;
                     Model()->table('goods_items_log')->insert($log_info);
                }
                showMessage(L('nc_common_op_succ'), '', 'json', 'succ');
            } else {
                showMessage(L('nc_common_op_fail'), '', 'json', 'error');
            }
        }
        
    }
    /**
     * 打印货品标签
     */
    public function print_goodsOp(){        
        $goods_itemids = $_GET['_ids'];
        $goods_itemids = explode(",",$goods_itemids);

        $condition['goods_items.goods_id'] = array('in',$goods_itemids);        
        $goods_model = new goods_itemsModel();
        $goodslist = $goods_model->getGoodsItemsList($condition,null,9999);        
        Tpl::output('goodslist',$goodslist);
        Tpl::showpage('store_goods_items.print','null_layout');
    }

    /**
     * 解绑
     */
    public function unbindOp(){
        $goods_itemids = $_POST['_ids'];
        $goods_itemids = explode(",",$goods_itemids);
        $condition['goods_items.goods_id'] = array('in',$goods_itemids);
        $goods_model = new goods_itemsModel();
        foreach ($goods_itemids as $goods_id){
            $goods_info = $goods_model->getGoodsItemInfo(['goods_id'=>$goods_id],'is_on_sale');
            if($goods_info['is_on_sale'] != '2'){
                //$res = callback(false, '货号为 '.$goods_id."不是库存状态，不能解绑");
                exit(json_encode(0));
            }
        }

        $result = $goods_model->editGoodsItems(array("order_sn"=>"","order_detail_id"=>0),$condition);
        exit(json_encode($result));
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
                    array('menu_key' => 'index', 'menu_name' => '货品查询', 'menu_url' => urlShop('store_goods_items', 'index')),
                    array('menu_key' => '1', 'menu_name' => '收货中', 'menu_url' => urlShop('store_goods_items', 'index',array('status'=>1))),
                    array('menu_key' => '2', 'menu_name' => '库存', 'menu_url' => urlShop('store_goods_items', 'index',array('status'=>2))),
                    array('menu_key' => '3', 'menu_name' => '已销售', 'menu_url' => urlShop('store_goods_items', 'index',array('status'=>3))),
                    array('menu_key' => '4', 'menu_name' => '盘点中', 'menu_url' => urlShop('store_goods_items', 'index',array('status'=>4))),
                    array('menu_key' => '5', 'menu_name' => '调拨中', 'menu_url' => urlShop('store_goods_items', 'index',array('status'=>5))),
                    //array('menu_key' => '6', 'menu_name' => '损益中', 'menu_url' => urlShop('store_goods_items', 'index',array('status'=>6))),
                    array('menu_key' => '7', 'menu_name' => '已报损', 'menu_url' => urlShop('store_goods_items', 'index',array('status'=>7))),
                    //array('menu_key' => '8', 'menu_name' => '返厂中', 'menu_url' => urlShop('store_goods_items', 'index',array('status'=>8))),
                    array('menu_key' => '9', 'menu_name' => '已返厂', 'menu_url' => urlShop('store_goods_items', 'index',array('status'=>9))),
                    array('menu_key' => '10', 'menu_name' => '销售中', 'menu_url' => urlShop('store_goods_items', 'index',array('status'=>10))),
                    array('menu_key' => '11', 'menu_name' => '退货中', 'menu_url' => urlShop('store_goods_items', 'index',array('status'=>11))),
                    //array('menu_key' => '12', 'menu_name' => '作废', 'menu_url' => urlShop('store_goods_items', 'index',array('status'=>12))),
                );
                break;
            case 'edit':
                $menu_array = array(
                    array('menu_key' => 'index', 'menu_name' => '库存列表', 'menu_url' => urlShop('store_goods_items', 'index')),
                    array('menu_key' => 'edit', 'menu_name' => '批量上下架', 'menu_url' => urlShop('store_goods_items', 'edit'))
                );
                break;
        }
        if(!empty($array)) {
            $menu_array[] = $array;
        }
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }

    public function rfid_bindOp() {
       if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

            $tid = isset($_POST['tid']) ? $_POST['tid'] : '';
            $item_id = isset($_POST['item_id']) ? $_POST['item_id'] : '';
            $epc = isset($_POST['epc']) ? $_POST['epc'] : strtoupper(md5(strrev(md5($item_id))));

            $goods_model = new goods_itemsModel();
            $resp = $goods_model->set_rfid($item_id, $tid, $epc);
           
            if ($resp === false) {
                $resp = callback($resp, '设置失败');
            } else if ($resp === true) {
                $resp = callback($resp, '设置成功', array('tid' => $tid, 'epc' => $epc));
            }

            exit(json_encode($resp));
       } else {
            Tpl::showpage('store_goods_items.rfid');
       }
    }

    public function rfid_unbindOp() {
        $tid = isset($_POST['tid']) ? $_POST['tid'] : '';
        
        if (empty($tid)) {
            $resp = callback(false,'tid不能为空');
        } else {
            $goods_model = new goods_itemsModel();
            $resp = $goods_model->unset_rfid($tid); 
            if ($resp === false) {
                $resp = callback(false, '更新失败');
            } else {
                $resp = callback(true, '更新成功');
            }

            exit(json_encode($resp));
        }
    }

    public function rfid_recogOp() {
        $tid = isset($_POST['tid']) ? $_POST['tid'] : '';

        if (empty($tid)) {
            $resp = callback(false,'tid不能为空');
        } else {
            $goods_model = new goods_itemsModel();
            $item = $goods_model->get_by_rfid($tid);
            if ($item) {
                $resp = callback(true, '找到该货品，货号为 '.$item['item_id']);
            } else {
                $resp = callback(false, '无法找到关联的货品');
            }
        }

        exit(json_encode($resp));
    }




    public function downloadOp() {
        $condition=$this->convertParams($_GET);
        $goods_items_model = new goods_itemsModel();
        $condition["company_id"]=$_SESSION['store_company_id'];
        $goods_items_model->getGoodsItemsList($condition,null,1);
        $countNum = $goods_items_model->gettotalnum();//获取总数量
        if($countNum>=200000){
            $data=null;
            $this->downExcel($data);
            exit();
        }
        $data = $goods_items_model->getGoodsItemsList($condition,null,$countNum);
        $this->downExcel($data);
    }

    function downExcel($data)
    {
        if (empty($data)) {
            $xls_content = '没有数据！或者数据太多请联系技术导出';
        } else {
            $jijiachengben = "";
            $show_chengben = $this->check_seller_limit('limit_show_goods_chengben');
            if ($show_chengben) {
                $jijiachengben = "计价成本,";
            }
            $xls_content = "货号,款号,商品名称,产品线,款式分类,入库方式,状态,绑定订单,所在仓库,供应商,数量,{$jijiachengben}材质及颜色,金重,指圈,金托类型	,主石大小,主石粒数,证书号,证书类型,品牌,主石形状,主石颜色,主石净度,主石切工,副石1,副石1粒数,副石2,副石2粒数,副石2重,副石3,副石3粒数,副石3重,维修状态,入库时间\r\n";
            foreach ($data as $val) {
                $xls_content .= $val['goods_id'] . ",";
                $xls_content .= $val['goods_sn'] . ",";
                $xls_content .= $val['goods_name'] . ",";
                $xls_content .= $val['product_type'] . ",";
                $xls_content .= $val['cat_type'] . ",";
                $xls_content .= paramsHelper::echoOptionText("put_type", $val["put_in_type"]) . ",";
                $xls_content .= paramsHelper::echoOptionText("is_on_sale", $val["is_on_sale"]) . ",";
                $xls_content .= "'".$val['order_sn'] . ",";
                $xls_content .= $val['warehouse'] . ",";
                $xls_content .= $val['prc_name'] . ",";
                $xls_content .= $val['num'] . ",";
                if ($jijiachengben != '') {
                    $xls_content .= $val['jijiachengben'] . ",";
                }
                $xls_content .= $val['caizhi'] . '/' . $val["jinse"] . ",";
                $xls_content .= $val['jinzhong'] . ",";
                $xls_content .= $val['shoucun'] . ",";
                $xls_content .= paramsHelper::echoOptionText("tuo_type", $val["tuo_type"]) . ",";
                $xls_content .= $val['zuanshidaxiao'] . ",";
                $xls_content .= $val['zhushilishu'] . ",";
                $xls_content .= $val['zhengshuhao'] . ",";
                $xls_content .= $val['zhengshuleibie'] . ",";
                $xls_content .= $val['pinpai'] . ",";
                $xls_content .= $val['zhushixingzhuang'] . ",";
                $xls_content .= $val['zhushiyanse'] . ",";
                $xls_content .= $val['zhushijingdu'] . ",";
                $xls_content .= $val['zhushiqiegong'] . ",";
                $xls_content .= $val['fushi'] . ",";
                $xls_content .= $val['fushilishu'] . ",";
                $xls_content .= $val['shi2'] . ",";
                $xls_content .= $val['shi2lishu'] . ",";
                $xls_content .= $val['shi2zhong'] . ",";
                $xls_content .= $val['shi3'] . ",";
                $xls_content .= $val['shi3lishu'] . ",";
                $xls_content .= $val['shi3zhong'] . ",";
                $xls_content .= paramsHelper::echoOptionText("weixiu_status", $val["weixiu_status"]) . ",";
                $xls_content .= $val['update_time'] . "\n";
            }
        }
        exportcsv($xls_content,'导出库存');
    }






}