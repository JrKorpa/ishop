<?php
/**
 * 进货单
 *
 * @珂兰技术中心提供技术支持
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class erp_bill_lControl extends BaseSellerControl {
    public function __construct() {
        if(!in_array($_GET['op'],array("write_js"))){
            parent::__construct();
            Language::read('store_bill,store_goods_index');
        }
    }

    /**
     * 新增进货单
     *
     */
    public function addOp() {
        $store_id = $_SESSION['store_id'];
        $company_id = $_SESSION['store_company_id'];
        $company_model = Model('company');
        $warehouse_model = Model('erp_warehouse');

        $company_list = $company_model->getCompanyList(array('id'=>57));
        $warehouse_list = $warehouse_model->getWareHouseList(array('company_id'=>$company_id,'is_enabled'=>1));
        $put_type_list = goods_itemsModel::getGoodsPutTypeList();
        
        Tpl::output('step',1);
        Tpl::output('put_type_list',$put_type_list);
        Tpl::output('company_list',$company_list);
        Tpl::output('warehouse_list',$warehouse_list);
        Tpl::showpage('erp_bill_l.add');
    }
    /**
     * 新增进货单 保存
     *
     */
    public  function insertOp(){
        set_time_limit(0);
        $result = array("success"=>0,'msg'=>'');
        if(!$_POST){
            $result = callback(false,"非法提交");
            exit(json_encode($result));
        }  
        $bill_type = "L";
        /**
         * 供应商判定
         */
        if(empty($_POST['supplier_id'])){
            $result = callback(false,"供应商不能为空");
            exit(json_encode($result));
        }else{
            $supplier_id = trim($_POST['supplier_id']);
        }
        if(empty($_POST['put_in_type'])){
            $result = callback(false,"入库方式不能为空");
            exit(json_encode($result));
        }else{
            $put_in_type = trim($_POST['put_in_type']);
        }
        /**
         * 入库仓库不能为空
         */
        if(empty($_POST['to_house_id'])){
            $result = callback(false,"入库仓库不能为空");
            exit(json_encode($result));
        }else{
            $house_arr = explode("|",$_POST['to_house_id']);
            if(count($house_arr)==2){
                $to_store_id = (int)$house_arr[0];
                $to_house_id = (int)$house_arr[1];
            }else{
                $result = callback(false,"入库仓库参数错误！");
                exit(json_encode($result));
            }
        } 
        //入库柜位
        /*if(empty($_POST['to_box_id'])){
            $result = callback(false,"入库柜位不能为空");
            exit(json_encode($result));
        }else{
            $to_box_id = $_POST['to_box_id'];
        }*/
        $to_box_id = '0-00-0-0';
        /**
         * 是否导入货品信息判断
        */
        if(empty($_POST['create_goods_grid'])){
            $result = callback(false,"请导入货品信息！");
            exit(json_encode($result));
        }else{
            $goods_list = unserialize(base64_decode($_POST['create_goods_grid']));
            if(count($goods_list)==0){
                $result = callback(false,"导入货品数据为空！");
                exit(json_encode($result));
            }
        }
        $remark = !empty($_POST['remark'])?trim($_POST['remark']):'';         
        $to_company_id = $_SESSION['store_company_id'];        
        $from_company_id = $supplier_id;
        $from_store_id  = 0;
        
        $erp_bill_model	= new erp_billModel();
        $goods_model = new goodsModel();
        $warehouse_model = new erp_warehouseModel();
        
        $warehouse = $warehouse_model->getWareHouseInfo(array('house_id'=>$to_house_id));
        if(isset($warehouse) && $warehouse['lock']>0){
            $result = callback(false,"入库仓库正在盘点中！");
            exit(json_encode($result));
        }
        
        foreach ($goods_list as $line=>$goods){
            $line ++;
            $tip = "第{$line}行：";          
            if($goods['goods_sn']==""){
                $result = callback(false,$tip."导入款号不能为空！");
                exit(json_encode($result));
            }       
            $bill_goods_list[] = array(
                'goods_itemid'=>null,
                'in_warehouse_type'=>$put_in_type,
                'goods_sn'=>$goods['goods_sn'],
                'goods_name'=>$goods['goods_name'],
                'goods_count'=>1,
                'yuanshichengben'=>$goods['chengbenjia'],
                'mingyichengben'=>$goods['chengbenjia'],
                'jijiachengben'=>$goods['jijiachengben'],
                'sale_price'=>$goods['jijiachengben'],
                'to_store_id'=>$to_store_id,
                'to_company_id'=>$to_company_id,
                'to_house_id'=>$to_house_id,
                'to_box_id'=>$to_box_id,
                'in_warehouse_type'=>$put_in_type,
                'goods_data'=>json_encode($goods,JSON_UNESCAPED_UNICODE),
            );
        }
        
        $bill_info = array(
            'bill_no'=>uniqid(),
            'bill_type'=>$bill_type,
            'bill_status'=>1,
            'in_warehouse_type'=>$put_in_type,
            'from_company_id'=>$from_company_id,
            'from_store_id'=>$from_store_id,
            'create_user'=>$_SESSION['seller_name'],
            'create_time'=>date("Y-m-d H:i:s",TIMESTAMP),
            'remark'=>$remark,
            'to_store_id'=>$to_store_id,
            'to_company_id'=>$to_company_id,
            'to_house_id'=>$to_house_id,
            'to_box_id'=>$to_box_id,
            'supplier_id'=>$supplier_id
        );
        //print_r($bill_goods_list);
        //print_r($bill_info);exit;
        $res = $erp_bill_model->createBillL($bill_info,$bill_goods_list);
        if($res['success'] == 1){
            $file = 'data/upload/shop/excel/tmp/' .$_SESSION['seller_id'] . '.html';
            if (file_exists($file)) {
                @unlink($file);                
            }
            $result = callback(true,"保存成功",$res['data']);
            exit(json_encode($result));
        }else{
            $result = callback(false,$res['error']);
            exit(json_encode($result));
        }        
        
    }
    
    /**
     * Excel 推过来的数据 进行创建写入 文件
     */
    public function write_jsOp(){ 
          
        $content = $_POST['content'];
        $contents = explode('|', $content);
        $sn = $_POST['sn'];
    
        $l_arr = array();
        $j_error = "";
    
        $chengbenjia = $cnt = 0;
        $contents_cnt = count($contents);
        for ($i = 0  ; $i < $contents_cnt  ; $i ++ ){
            if ($contents[$i] != null){
                $d = explode(';', $contents[$i]);
    
                if ($d[1] != ''){
                    if(trim($d[4])=="裸石" || trim($d[4])=="彩钻"){
                        //$xiaoshouchengben = ($d[72] == '' || $d[72] == 0) ? round(trim($d[46]),2) : round(trim($d[72]),2);
                        $xiaoshouchengben = $d[72];
                        $certificate_fee = $d[85] === ''?0:$d['85'];
                        //$xiaoshouchengben = ($d[72] == '' || $d[72] == 0) ? round(trim($d[46]),2) : round(trim($d[72]),2);
                    }else{
                        //$xiaoshouchengben = trim($d[46]) !='' ? round(trim($d[46]),2) : '0';
                        $xiaoshouchengben = $d[72];
                        $certificate_fee = $d[85];
                    }
                    if ('空托' == trim($d[77])){
                        $tuo_type = '2';
                    }else{
                        $tuo_type = '1';
                    }
                    $caizhiArray = $this->getGoldAndColor(trim($d[5]));
                    $d[1] = strtoupper($d[1]);
                    $goods[] = array(
                        'og_id'=>'',
                        'goods_id'=>trim($d[0]),
                        'goods_sn'=>trim($d[1]),
                        'mo_sn'=>trim($d[2]),
                        'product_type'=>trim($d[3]),
                        'cat_type'=>trim($d[4]),
                        'caizhi'=>$caizhiArray['gold'],
                        'jinse'=>$caizhiArray['color'],
                        'jinzhong'=>round(trim($d[6]),3),
                        'jinhao'=>trim($d[7]),
                        'zhuchengsezhongjijia'=>trim($d[8]),
                        'zhuchengsemairudanjia'=>round(trim($d[9]),2),
                        'zhuchengsemairuchengben'=>round(trim($d[10]),2),
                        'zhuchengsejijiadanjia'=>round(trim($d[11]),2),
                        'zhushi'=>trim($d[12]),
                        'zhushilishu'=>trim($d[13]),
                        'zuanshidaxiao'=>round(trim($d[14]),3),
                        'zhushizhongjijia'=>trim($d[15]),
                        'zhushiyanse'=>trim($d[16]),
                        'zhushijingdu'=>trim($d[17]),
                        'zhushimairudanjia'=>round(trim($d[18]),2),
                        'zhushimairuchengben'=>round(trim($d[19]),2),
                        'zhushijijiadanjia'=>round(trim($d[20]),2),
                        'zhushiqiegong'=>trim($d[21]),
                        'zhushixingzhuang'=>trim($d[22]),
                        'zhushibaohao'=>trim($d[23]),
                        'zhushiguige'=>trim($d[24]),
                        'fushi'=>trim($d[25]),
                        'fushilishu'=>trim($d[26]),
                        'fushizhong'=>round(trim($d[27]),3),
                        'fushizhongjijia'=>trim($d[28]),
                        'fushiyanse'=>trim($d[29]),
                        'fushijingdu'=>trim($d[30]),
                        'fushimairudanjia'=>round(trim($d[31]),2),
                        'fushimairuchengben'=>round(trim($d[32]),2),
                        'fushijijiadanjia'=>round(trim($d[33]),2),
                        'fushixingzhuang'=>trim($d[34]),
                        'fushibaohao'=>trim($d[35]),
                        'fushiguige'=>trim($d[36]),
                        'zongzhong'=>round(trim($d[37]),3),
                        'mairugongfeidanjia'=>round(trim($d[38]),2),
                        'mairugongfei'=>round(trim($d[39]),2),
                        'jijiagongfei'=>round(trim($d[40]),2),
                        'shoucun'=>trim($d[41]),
                        'ziyin'=>trim($d[42]),
                        'danjianchengben'=>round(trim($d[43]),2),
                        'peijianchengben'=>round(trim($d[44]),2),
                        'qitachengben'=>round(trim($d[45]),2),
                        'chengbenjia'=>round(trim($d[46]),2),
                        'jijiachengben'=>round(trim($d[47]),2),
                        'jiajialv'=>round(trim($d[48]),2),
                        'zuixinlingshoujia'=>round(trim($d[49]),2),
                        'xianzaixiaoshou'=>round(trim($d[49]),2),//根据老系统来讲，现在销售价格和最新零售价格是一样的--JUAN
                        'pinpai'=>trim($d[50]),//品牌
                        'changdu'=>trim($d[51]),
                        'zhengshuhao'=>trim($d[52]),
                        'yanse'=>trim($d[53]),
                        'jingdu'=>trim($d[54]),
                        'peijianshuliang'=>trim($d[55]),
                        'guojizhengshu'=>trim($d[56]),
                        'zhengshuleibie'=>trim($d[57]),
                        'goods_name'=>trim($d[58]),
                        'kela_order_sn'=>trim($d[59]),
                        'shi2'=>trim($d[60]),
                        'shi2lishu'=>trim($d[61]),
                        'shi2zhong'=>round(trim($d[62]),3),
                        'shi2zhongjijia'=>trim($d[63]),
                        'shi2mairudanjia'=>round(trim($d[64]),2),
                        'shi2mairuchengben'=>round(trim($d[65]),2),
                        'shi2jijiadanjia'=>round(trim($d[66]),2),
                        'qiegong'=>trim($d[67]),
                        'paoguang'=>trim($d[68]),
                        'duichen'=>trim($d[69]),
                        'yingguang'=>trim($d[70]),
                        'buchanhao'=>trim($d[71]),
                        //'gene_sn'=>trim($d[72]),
                        'xiaoshouchengben'=>$xiaoshouchengben,
                        'zuanshizhekou'=>trim($d[73]),
                        'zhengshuhao2'=>trim($d[74]),
                        'guojibaojia'=>trim($d[75]),
                        'gongchangchengben'=>trim($d[76]),
                        'tuo_type'=>$tuo_type,
                        'gemx_zhengshu'=>trim($d[78]),
                        'jietuoxiangkou'=>trim($d[79]),
                        'zhushitiaoma'=>trim($d[80]),//主石条码
                        'color_grade'=>trim($d[81]),
                        'supplier_code' =>trim($d[82]),
                        'luozuanzhengshu' =>trim($d[83]),
                        'with_fee' =>trim($d[84]),
                        'certificate_fee' =>$certificate_fee,
                        'operations_fee' =>trim($d[86]),
                        'peijianjinchong' =>empty(trim($d[87]))?0:trim($d[87]),
                        'shi2baohao'=>trim($d[88]),
                        'shi3'=>trim($d[89]),
                        'shi3lishu'=>trim($d[90]),
                        'shi3zhong'=>round(trim($d[91]),3),
                        'shi3zhongjijia'=>trim($d[92]),
                        'shi3mairudanjia'=>round(trim($d[93]),2),
                        'shi3mairuchengben'=>round(trim($d[94]),2),
                        'shi3jijiadanjia'=>round(trim($d[95]),2),
                        'shi3baohao'=>trim($d[96])
                    );
                    $chengbenjia += round(trim($d[46]),2);
                    $cnt++;
                }
            }
        }
    
        if (!empty($j_error)) {
            echo $j_error;
            exit;
        }
    
        $str = base64_encode(serialize($goods));
    
        header('Content-Type: text/html; charset=utf-8');
        $file = "data/upload/shop/excel/tmp/" . $sn . ".html";
        if (is_file($file)) {
            unlink($file);
        }
    
        $fp = fopen($file, "a+");
        fwrite($fp, "<input type='hidden' name=create_goods_grid  value= '" . $str .
        "'/>\n");
        fwrite($fp, "<input type='hidden' name=create_goods_num  value= '" . $cnt . "'/>\n");
        fwrite($fp, "<input type='hidden' name=create_goods_cost  value= '" . $chengbenjia .
        "'/>\n");
        fclose($fp);
        echo "ok";
    }
    public function import_jsOp()
    {  
        $seller_id = $_SESSION['seller_id'];
        $file = 'data/upload/shop/excel/tmp/' . $seller_id . '.html';
        if (file_exists($file)) {
            echo file_get_contents($file);
        } else {
            echo 0;
        }     
    
    }
    public static function getGoldAndColor($caizhi)
    {
        $returndata = array('gold'=>'','color'=>'');
        if(empty($caizhi) || $caizhi=='无')
        {
            return $returndata;
        }
        //转换为大写
        $checkinfo = strtoupper($caizhi);
        if($checkinfo == 'PT950')
        {
            $returndata['gold'] = 'PT950';
            $returndata['color']='白';
            return $returndata;
        }
        if($checkinfo == '千足金' || $checkinfo=='足金')
        {
            $returndata['gold'] = '足金';
            $returndata['color']= '无';
            return $returndata;
        }
        if(preg_match('/[0-9a-z]+/i',$checkinfo,$arr)){
            $returndata['gold'] = strtoupper($arr[0]);
            $returndata['color']  = substr($checkinfo,strlen($arr[0]));
        }else{
            $returndata['gold'] = $checkinfo;
            $returndata['color']  = '无';
        }
        return $returndata;
        /*
        //定义两种情况
        $goldkind = array('PT900','K');
        //默认为K
        $goldtxt = 'K';
        foreach($goldkind as $v)
        {
            if(strpos($checkinfo,$v) !== false)
            {
                //金料的值
                $goldtxt = $v;
                continue;
            }
        }
        if(preg_match('/[0-9a-z]+/i',$checkinfo,$arr)){
            $returndata['gold'] = strtoupper($arr[0]);
            $returndata['color']  = substr($checkinfo,strlen($arr[0]));
        }else{
            $returndata['gold'] = $checkinfo;
            //$returndata['color']  = '';
        }
        //将材质用获取到的goldtxt打散
        $caizhi_arr = explode($goldtxt,$checkinfo);        
        $returndata['gold'] = $caizhi_arr[0].$goldtxt;
        $returndata['color'] = $caizhi_arr[1];
        if($returndata['color'] == '黄金')
        {
            $returndata['color']='黄';
        }
        if($returndata['color'] == '白金')
        {
            $returndata['color']='白';
        }
        return $returndata;*/
    }
    /**
     * 用户中心右边，小导航
     *
     * @param string $menu_type 导航类型
     * @param string $menu_key 当前导航的menu_key
     * @param boolean $allow_promotion
     * @return
     */
    private function profile_menu($menu_type,$menu_key, $allow_promotion = array()) {
        $menu_array = array();
        switch ($menu_type) {
            case 'show':
                $menu_array = array(
                array('menu_key' =>'bill','menu_name'=>'单据信息','menu_url' =>urlShop('erp_bill_l', 'show',array('bill_id'=>$_GET['bill_id']))),
                array('menu_key' =>'bill_goods', 'menu_name' =>'单据商品列表', 'menu_url' => urlShop('erp_bill_l','show_bill_goods',array('bill_id'=>$_GET['bill_id'],'menu_key'=>'bill_goods')))
                );
                break;
            case 'edit':
                $menu_array = array(
                    array('menu_key' =>'bill','menu_name'=>'编辑单据','menu_url' =>urlShop('erp_bill_l', 'edit')),
                    array('menu_key' =>'bill_goods', 'menu_name' =>'编辑单据商品', 'menu_url' => urlShop('erp_bill_l', 'edit',array('bill_id'=>$_GET['bill_id'],'menu_key'=>'bill_goods')))
                );
                break;
        }
        Tpl::output ( 'member_menu', $menu_array );
        Tpl::output ( 'menu_key', $menu_key );
    }


    public function down_excelOp(){
        $temexcel_file = BASE_ROOT_PATH.'/data/upload/shop/excel/shouhuo.xls';
        $user_file = 'ishop_' . $_SESSION['seller_id'] . "_ishop.xls";
        $file = fopen($temexcel_file, 'r');

        header('Content-type: application/octet-stream');
        header("Accept-Ranges:bytes");
        header("Accept-length:" . filesize($temexcel_file));
        header('Content-Disposition: attachment;filename=' . $user_file);
        ob_clean();
        $a = fread($file, filesize($temexcel_file));
        fclose($file);
        echo $a;
    }
}
