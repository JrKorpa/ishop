<?php
/**
 * 裸钻列表
 * @b1 (c) 2005-2016 kelan Inc.
 * @license    http://官网
 * @link       交流群号：216611541
 * @since      提供技术支持 授权请购买正版授权
 * 
 **/

defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class diamondControl extends mobileHomeControl{

	public function __construct() {
        parent::__construct();

        //if(empty($this->member_info)) exit('error:502');
    }

    /**
     * 裸钻搜索条件
     */
    public function diamond_indexOp() {
        $diamond_api = data_gateway('idiamond');
        $style_api = data_gateway('istyle');
        $style_sn = isset($_GET['style_sn']) && !empty($_GET['style_sn']) ?$_GET['style_sn']:"";
        $shape = $cert = array();
        $is_disabled = false;
        if(!empty($style_sn)){
            $search_check = $style_api->get_param_by_sn(array('style_sn'=>$style_sn));
            if(isset($search_check['return_msg']) && !empty($search_check['return_msg'])){
                $shape = array_column($search_check['return_msg'], 'shape');
            }
            $styleinfo = $style_api->get_style_info(array('style_sn'=>$style_sn));
            if(isset($styleinfo['return_msg']) && !empty($styleinfo['return_msg'])){
                $xiliestr = $styleinfo['return_msg']['xilie'];
                if($styleinfo['return_msg']['style_type'] == 11){
                    $cert = array('HRD-D');//是否天生一对款
                    if(strpos($xiliestr, ',8,') !== false){
                        $is_disabled = true;
                    }
                }
            }
        }
        $byApi = $diamond_api->get_diamond_index();
        $diamond_search = isset($byApi['return_msg'])?$byApi['return_msg']:array();
        output_data(array('diamond_search' => $diamond_search, 'shape_sed'=>$shape, 'cert_sed'=>$cert, 'is_disabled'=>$is_disabled));
    }
    /**
     * 根据石重，匹配石重范围
     * @param unknown $cart
     * @return multitype:number |string
     */
    private function get_cart_min_max($cart){
        $cart = $cart * 1000;
        $cart = intval($cart);
        if ($cart >= 0 && $cart < 100)
        {
            return array(0.01,0.1);
        }
        if ($cart >= 100 && $cart <= 150)
        {
            return array(0.01,0.15);
        }
        if ($cart > 10000)
        {//当钻石大小大于10克拉的也为空
            return array(10,100);
        }
        $arr = array();
        $j = 0;
        for($i = 150;$i <= 10000; $i = $i+100) {
            $arr [$j]= ($i);
            $arr2[$j] = ($i+100);
            $j ++;
        }
        $count = count($arr);
        for($i = 0; $i <$count; $i ++)
        {
            if ( $cart > $arr[$i] && $cart <= $arr2[$i]){
                return array($arr[$i]/1000,$arr2[$i]/1000);
            }
        }
   }


    /**
     * 裸钻列表
     */
    public function diamond_listOp() {
        
        $curpage = isset($_REQUEST['clickPage'])?$_REQUEST['clickPage']:1;
        $shape = isset($_REQUEST['shape'])?$_REQUEST['shape']:array();
        $color = isset($_REQUEST['color'])?$_REQUEST['color']:array();
        $cut = isset($_REQUEST['cut'])?$_REQUEST['cut']:array();
        $clarity = isset($_REQUEST['clarity'])?$_REQUEST['clarity']:array();
        $symmetry = isset($_REQUEST['symmetry'])?$_REQUEST['symmetry']:array();
        $fluorescence = isset($_REQUEST['fluorescence'])?$_REQUEST['fluorescence']:array();
        $certificate = isset($_REQUEST['certificate'])?$_REQUEST['certificate']:array();
        $xilie = isset($_REQUEST['xilie'])?$_REQUEST['xilie']:array();
        $polishing = isset($_REQUEST['polishing'])?$_REQUEST['polishing']:array();
        $carat_min = isset($_REQUEST['carat_min'])?$_REQUEST['carat_min']:"";
        $carat_max = isset($_REQUEST['carat_max'])?$_REQUEST['carat_max']:"";

        $price_min = isset($_REQUEST['price_min'])?$_REQUEST['price_min']:"";
        $price_max = isset($_REQUEST['price_max'])?$_REQUEST['price_max']:"";
        $cert_id = isset($_REQUEST['cert_id'])?$_REQUEST['cert_id']:"";
        $pricesort = isset($_REQUEST['pricesort'])?$_REQUEST['pricesort']:"";
        $is_hot = isset($_REQUEST['is_hot'])?$_REQUEST['is_hot']:"";
        $good_type = isset($_REQUEST['good_type'])?$_REQUEST['good_type']:"";
        $is_3dimage = isset($_REQUEST['is_3dimage'])?$_REQUEST['is_3dimage']:"";
        $style_sn = isset($_REQUEST['style_sn'])?$_REQUEST['style_sn']:"";

        //排除已加入购物车中的裸钻商品
        $cartModel = new cartModel();
        $dialist = $cartModel->getCartList(array('goods_type'=>2)," goods_id ");
        $not_dia = array();
        if(!empty($dialist)){
            $not_dia = array_column($dialist, 'goods_id');
        }

        $style_api = data_gateway('istyle');
        if($carat_min>0 && $carat_min == $carat_max && !empty($style_sn)){
            $result = $style_api->get_stone_common_by_xiangkou($carat_min);
            if($result['error'] == 0){
                $stone_arr = isset($result['return_msg'])?$result['return_msg']:[];
                $carat_min = isset($stone_arr[0])?$stone_arr[0]:$carat_min;
                $carat_max = isset($stone_arr[1])?$stone_arr[1]:$carat_max;
            }
            //list($_carat_min,$_carat_max) = $this->get_cart_min_max($carat_min);
            //$carat_max = $_carat_max;
        }

        $tsyd_stone_arr = array();
        $is_tsyd = "";
        //是否对款
        if(!empty($style_sn)){
            $is_tsyd = false;
            $coupleinfo = $style_api->reason_couple_style_other($style_sn);
            if($coupleinfo['error'] == 0){
                $style_other = isset($coupleinfo['return_msg'])?$coupleinfo['return_msg']:"";
                //是否天生一对款
                if(!empty($style_other) && in_array('HRD-D', $certificate)){
                    //根据款号查询可做镶口
                    $result = $style_api->reason_style_by_xiangkou($style_other);
                    if($result['error'] == 0){
                        $xiangkou_arr = isset($result['return_msg'])?$result['return_msg']:array();
                        if(!empty($xiangkou_arr)){
                           $result = $style_api->stone_scope_by_xiangkou($xiangkou_arr);
                           if($result['error'] == 0){
                                $tsyd_stone_arr = isset($result['return_msg'])?$result['return_msg']:array();
                           }
                       }
                    }
                }
                $styleinfo = $style_api->get_style_info(array('style_sn'=>$style_sn));
                if(isset($styleinfo['return_msg']) && !empty($styleinfo['return_msg'])){
                    $xiliestr = $styleinfo['return_msg']['xilie'];
                    if($styleinfo['return_msg']['style_type'] == 11 && strpos($xiliestr, ',8,') !== false){
                        $is_tsyd = true;
                    }
                }
            }else{
                //非对戒（排除天生一对钻）
                //$certificate = array('GIA','HRD-S');
            }
        }
        $where = array(
                'shape' => $shape,
                'color' => $color,
                'cut' => $cut,
                'clarity' => $clarity,
                'symmetry' => $symmetry,
                'fluorescence' => $fluorescence,
                'cert' => $certificate,
                'carat_min' => is_numeric($carat_min) === true ? $carat_min : 0.001,
                'carat_max' => is_numeric($carat_max) === true ? $carat_max : 10,
                'pf_price_min' => bcdiv($price_min,2,2),
                'pf_price_max' => bcdiv($price_max,2,2),
                //'price_min' => bcdiv($price_min,2,2),
                //'price_max' => bcdiv($price_max,2,2),
                'cert_id' => $cert_id,
                'polish' => $polishing,
                'xilie'=>$xilie,
                'pricesort'=>$pricesort,
                'status' => 1,//上架
                'is_hot' => $is_hot,
                'good_type' => $good_type,
                'not_from_ad' => array('11'), //直营店期货(kgk除外)  enjoy 17
                'warehouse' => array('HPLZK', 'COM'), //所有总部期货(COM排除kgk+enjoy) + 自己门店现货 +浩鹏公司现货（仓库HPLZK）
                'no_goods_id' => $not_dia,
                'no_cert_id' => $not_dia,
                'is_3dimage' => $is_3dimage,
                'carats_tsyd2'=> $tsyd_stone_arr,
                'is_tsyd' =>$is_tsyd
            );
        /*推荐查询*/
        $_where = $where;
        $params = paramsHelper::getParams("diamond_recommend");
        //$_where['cert'] = $params['certificate'];
        $_where['cert'] = tovalInArray($where['cert'], $params['certificate']);
        $_where['carat_min'] = $params['carat_min'];
        $_where['carat_max'] = $params['carat_max'];
        if($_where['carat_min']>0 && $_where['carat_min'] == $_where['carat_max']){
            list($_carat_min,$_carat_max) = $this->get_cart_min_max($_where['carat_min']);
            $_where['carat_max'] = $_carat_max;
        }
        $_where['color'] = $params['color'];
        $_where['clarity'] = $params['clarity'];
        $_where['cut'] = $params['cut'];
        $_where['pricesort'] = 'asc';
        $_where['carats_tsyd2']='';
        if(empty($_where['cert'])){
            $_where['carat_min'] = $_where['carat_max'] = 100;//若条件不符阻止查询推荐数据
        }

        $diamond_api = data_gateway('idiamond');
        if($is_hot){
            $byApi = $diamond_api->get_diamond_list(10, 1, $_where);
            $diamond_list = isset($byApi['return_msg'])?$byApi['return_msg']:array();
        }else{
            $byApi = $diamond_api->get_diamond_list(10, $curpage, $where);
            $diamond_list = isset($byApi['return_msg']['data'])?$byApi['return_msg']['data']:array();
        }
        $shape_arr = $diamond_api->get_diamond_index(array('shape_val'));
        $shape_val = isset($shape_arr['return_msg'])?$shape_arr['return_msg']:array();
        $store_id   = $this->member_info['store_id'];
        $company_id = $this->store_info['store_company_id'];
        if(!empty($diamond_list)){
            $diamond_api->multiply_jiajialv($diamond_list, $store_id, $company_id);
        }

        //获取天生一对列表
        $result = $diamond_api->get_tsyd_list_by_dia($diamond_list, $store_id, $company_id);
        if($result['error'] == 0){
            $diamond_list = isset($result['return_msg'])?$result['return_msg']:$diamond_list;
        }
        $curlist = array();
        if(!$is_hot){
            $_where['is_hot'] = $is_hot?'':1;
            $by_where = $_where;
            $curlist = isset($byApi['return_msg'])?$byApi['return_msg']:array();
        }else{
            $where['is_hot'] = $is_hot?'':1;
            $by_where = $where;
            $curlist['recordCount'] = count($diamond_list);
            $curlist['pageCount'] = 1;
        }
        if(!empty($diamond_list)){
            $diamond_list = array_values($diamond_list);
        }
        $byApi = $diamond_api->get_diamond_list(1, 1, $by_where);
        $bylist = isset($byApi['return_msg'])?$byApi['return_msg']:array();
        $_recordCount = 0;
        if($is_hot){
            $_recordCount = isset($bylist['recordCount'])?$bylist['recordCount']:0;
        }else{
            $_recordCount = count($bylist);
        }
		output_data(array('diamond_list' => $diamond_list, 'curlist'=>$curlist, 'shape_val'=>$shape_val, '_recordCount'=>$_recordCount));
    }
    
    /**
     * 裸钻详情
     */
    public function diamond_detailOp() {
        $cert_id = isset($_REQUEST['cert_id'])?$_REQUEST['cert_id']:'';
        $cart = isset($_REQUEST['cart'])?$_REQUEST['cart']:'';
        $style_sn = isset($_REQUEST['style_sn'])?$_REQUEST['style_sn']:'';
        if(!$cert_id) return false;
        $diamond_api = data_gateway('idiamond');
        $where = array('cert_id' => $cert_id, 'status' => 1);
        $byApi = $diamond_api->get_diamond_info($where);
        $diamond_detail = isset($byApi['return_msg'])?$byApi['return_msg']:array();
        if(empty($diamond_detail)){
            output_data(array(),array('error_msg'=>"裸钻不存在或已下架"), true);
        }
        if(!empty($diamond_detail['kuan_sn'])){
            $diainfo=$diamond_api->get_diamond_by_kuan_sn($diamond_detail['kuan_sn']);//天生一对钻HRD-D
            $diamond_detail = isset($diainfo['return_msg']) && !empty($diainfo['return_msg'])?$diainfo['return_msg']:array();
        }else{
            $diamond_detail = array($diamond_detail);
        }
        $store_id   = $this->member_info['store_id'];
        $company_id = $this->store_info['store_company_id'];
        $diamond_api->multiply_jiajialv($diamond_detail, $store_id, $company_id);
        $carat = array();
        foreach ($diamond_detail as $k => &$detail) {
            if(isset($detail['img']) && !empty($detail['img'])){
                if(substr($detail['img'],0,63)=='http://diamonds.kirangems.com/GemKOnline/DiaSearch/appVideo.jsp'){
                    $detail['is_jpg'] = false;
                }elseif(substr($detail['img'],0,48)=='https://diamanti.s3.amazonaws.com/images/diamond'){
                    $detail['is_jpg'] = true;
                }else{
                    $detail['img'] = "";
                }
            }
            $carat[$detail['cert_id']] = $detail['carat'];
        }

        $style_api = data_gateway('istyle');
        /*托最适配钻*/
        $result = $style_api->get_tuo_adaptive_by_dia($cart, $carat, $style_sn);
        $do_cart_id = "";
        if($result['error'] == 0){
           $do_cart_id = isset($result['return_msg'])?$result['return_msg']:""; 
        }

        /*天生一对DIA*/
        /*$diamond_detail[0]['cert_id2'] = "";
        if($diamond_detail[0]['cert']=='HRD-D' && $diamond_detail[0]['kuan_sn']!=''){
            $diainfo = $dia_kuan =array();
            $diainfo=$diamond_api->get_diamond_by_kuan_sn($diamond_detail[0]['kuan_sn']);
            $dia_kuan = isset($diainfo['return_msg']) && !empty($diainfo['return_msg'])?$diainfo['return_msg']:array();
            foreach($dia_kuan as $k => $v){
                if($v['goods_sn']!=$diamond_detail[0]['goods_sn']){
                    $diamond_detail[1]=$v;
                    break;
                }
            }
        }*/

        $curlist = $diamond_api->get_diamond_index(array('shape', 'cut', 'color', 'clarity'));
        $curlist = isset($curlist['return_msg'])?$curlist['return_msg']:array();
        $send_date = date("Y-m-d", strtotime("+1 months", strtotime(date('Y-m-d'))));
        output_data(array('diamond_detail'=>$diamond_detail, 'curlist'=>$curlist, 'send_date'=>$send_date, 'do_cart_id'=>$do_cart_id));
    }

    
    public function get_diammond_infoOp(){
        $cert_id = isset($_REQUEST['cert_id'])?$_REQUEST['cert_id']:'';
        if(!$cert_id) {
            output_error("证书号不能为空");
        }
        $diamond_api = data_gateway('idiamond');
        $result = $diamond_api->get_diamond_info(array('cert_id' => $cert_id));
        $diamond_info = array();
        if(isset($result['error']) && $result['error']==0){
            $diamond_info = $result['return_msg'];
        }
        if(empty($diamond_info)){
             $goods_items_model = new goods_itemsModel();
             $goods_info = $goods_items_model->getGoodsItemInfo(array('zhengshuhao'=>$cert_id,'product_type'=>'钻石','cat_type'=>'裸石','company_id'=>$this->store_info['store_company_id']));
             if(!empty($goods_info)){
                 $diamond_info = array(
                    'cert'=>$goods_info['zhengshuleibie']?$goods_info['zhengshuleibie']:'无',                
                    'cert_id'=>$goods_info['zhengshuhao'],
                    'carat'=>$goods_info['zuanshidaxiao'],
                    'color'=>$goods_info['yanse'],
                    'clarity'=>$goods_info['jingdu'],
                    'cut'=>$goods_info['qiegong'],
                    'goods_type'=>1,
                    'pifajia'=>$goods_info['jijiachengben']
                 );
             }
        }
        if(empty($diamond_info)){
            output_error("证书号不存在");
        }
        $store_id   = $this->member_info['store_id'];
        $company_id = $this->store_info['store_company_id'];
        $diamond_api->multiply_jiajialv($diamond_info, $store_id, $company_id);
        output_data(array("diamond_info"=>$diamond_info));
    }
    /**
     * 更多定制表单
     */
    public function get_diamond_info_indexOp(){
        $cert_id  = $_POST['cert_id'];        
        $styleApi = data_gateway('istyle');
        $keys = array("xiangqian");    
        $result = $styleApi->get_style_goods_diy_index($keys,'');
        if($result['error']==1){
            output_error($result['error_msg']);
        }else{
            $attr_list = $result['return_msg'];
        }
        $goods_info = array(
            "xiangqian" => "不需工厂镶嵌",
        );
        $diamond_api = data_gateway('idiamond');
        $result = $diamond_api->get_diamond_info(array('cert_id' => $cert_id));
        if($result['error']==0){
            $diamond_info = $result['return_msg'];
            if($diamond_info['kuan_sn']) {
                $goods_info['xiangqian'] = "需工厂镶嵌";
            }
        } 
        $datas['attr_list']  = $attr_list;
        $datas['goods_info'] = $goods_info;
        output_data($datas);
    }
}
