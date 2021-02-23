<?php
/**
 * 对戒商品列表
 * @b1 (c) 2005-2016 kelan Inc.
 * @license    http://官网
 * @link       交流群号：216611541
 * @since      提供技术支持 授权请购买正版授权
 * 
 **/

defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class couple_goodsControl extends mobileHomeControl{

	public function __construct() {
        parent::__construct();
    }

    public function couple_goods_tabOp(){
        $styleApi = data_gateway('istyle');
        $result = $styleApi->get_style_goods_index(array('cat_type'));
        $datas = array();
        if(is_array($result['return_msg'])){
            $datas = $result['return_msg'];
            $datas['cat_type_val'] = array_flip($datas['cat_type']);
        } 
        output_data($datas);        
    }
    
    /**
     * 对戒搜索条件
     */
    public function couple_goods_indexOp(){
        $styleApi = data_gateway('istyle');
        $tab = !empty($_REQUEST["tab"])?$_REQUEST["tab"]:11;
        if(in_array($tab, array(2, 11))){
            $search = array('cart', 'price', 'caizhi', 'pick_xilie','is_xianhuo','tuo_type','zhiquan');
        }elseif(in_array($tab, array(10))){
            $search = array('cart', 'price', 'caizhi','is_xianhuo','tuo_type','zhiquan');
        }elseif(in_array($tab, array(3, 4, 5,7))){
            $search = array('cart', 'price', 'caizhi', 'jud_type','is_xianhuo','tuo_type');
        }elseif(in_array($tab, array(15))){
            $search = array('caizhi','is_xianhuo','tuo_type');
        }
        $result = $styleApi->get_style_goods_index($search);
        $datas = array();
        if(is_array($result['return_msg'])){
            $datas = $result['return_msg'];
            if(!empty($datas['cart'])) $datas['cart_key'] = array_keys($datas['cart']);
            if(!empty($datas['cart'])) $datas['cart_val'] = array_values($datas['cart']);
            if(!empty($datas['price'])) $datas['price_key'] = array_keys($datas['price']);
            if(!empty($datas['price'])) $datas['price_val'] = array_values($datas['price']);
            if(!empty($datas['pick_xilie'])) $datas['pick_xilie_key'] = array_keys($datas['pick_xilie']);
            if(!empty($datas['pick_xilie'])) $datas['pick_xilie_val'] = array_values($datas['pick_xilie']);
        }
        $datas['warehouse']=array("总部", "门店");
        output_data($datas);
    }


    /**
     * 对戒列表
     */
    /*
    public function couple_goods_listOp(){
        
        $curpage = !empty($_REQUEST["curpage"])?$_REQUEST["curpage"]:1;
        $pageSize = !empty($_REQUEST["pagesize"])?$_REQUEST["pagesize"]:12;
        $search_type = !empty($_REQUEST["search_type"])?$_REQUEST["search_type"]:"all";
        $cat_type = !empty($_POST['cat_type'])?$_POST['cat_type']:11;
        $product_id = array();
        if($cat_type==15){
            $product_id = array(7, 13, 14, 21);//黄金产品线
            $cat_type = "";
        }
        $_POST["order_by"] = $_POST["order_by"]?$_POST["order_by"]:"1|1";
        if($search_type == 'diff'){
            $goods_ids = $_POST['contrast_list'];
            //商品对比
            $where = array(
                'goods_sn'=>$goods_ids?$goods_ids:'-1',
                'order_by'=>$_POST["order_by"],                        
            );
        }else{
            //商品列表
            $where = array(
                'style_name'=>$_POST['goods_name'],
                'cart_min'=>$_POST['carat_min'],//石重范围小
                'cart_max'=>$_POST['carat_max'],//石重范围大
                'minprice'=>$_POST['minprice'],
                'maxprice'=>$_POST['maxprice'],
                'cat_type_id'=>$cat_type,
                'product_type_id'=>$product_id,
                'xilie'=>$_POST['pick_xilie'],
                'jud_type'=>$_POST['jud_type'],
                //'carat_min'=>isset($_POST['cart'][0])?$_POST['cart'][0]:'',
                //'carat_max'=>isset($_POST['cart'][1])?$_POST['cart'][1]:'',
                'price_min'=>isset($_POST['price'][0])?$_POST['price'][0]:'',
                'price_max'=>isset($_POST['price'][1])?$_POST['price'][1]:'',
                'caizhi'=>$_POST['caizhi'],
                'order_by'=>$_POST["order_by"],
                'group_by'=>'style_sn',
                'tuo_type'=>$_POST['tuo_type'],//金驼类型
            );
            
            if(!empty($_POST['cart'][0])){
                $carat_min = $_POST['cart'][0];
            }else if($_POST['carat_min']){
                $carat_min = $_POST['carat_min'];
            }
            if(!empty($_POST['cart'][1])){
                $carat_max = $_POST['cart'][1];
            }else if($_POST['carat_max']){
                $carat_max = $_POST['carat_max'];
            }
            if(!empty($carat_min)){
                if($carat_min>0.25){
                    $where['xiangkou_min'] = $carat_min-0.051;
                }else{
                    $where['xiangkou_min'] = $carat_min-0.001;
                }
            }
            if(!empty($carat_max)){
                if($carat_max>0.25){
                    $where['xiangkou_max'] = $carat_max+0.04;
                }else{
                    $where['xiangkou_max'] = $carat_max;
                }
            }
        }
        //var_dump($where);die;
        $api = data_gateway('istyle');
        $result = $api->get_couple_marry_goods_list($where, $curpage, $pageSize);
        //print_r($result);
        //价格计算
        $store_id = $this->store_info['store_id'];
        $result   = $api->build_style_goods_price_list($result,$store_id);

        $data = formatWapPageList($result['return_msg'],'style_goods_list');
        //print_r($data);
        exit(json_encode($data));
    }*/
    
}