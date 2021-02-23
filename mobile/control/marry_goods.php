<?php
/**
 * 婚戒商品列表
 * @b1 (c) 2005-2016 kelan Inc.
 * @license    http://官网
 * @link       交流群号：216611541
 * @since      提供技术支持 授权请购买正版授权
 * 
 **/

defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class marry_goodsControl extends mobileHomeControl{

	public function __construct() {
        parent::__construct();
    }

    public function marry_goods_tabOp(){
        $styleApi = data_gateway('istyle');
        $result = $styleApi->get_style_goods_index(array('cat_type'));
        $datas = array();
        if(is_array($result['return_msg'])){
            $datas = $result['return_msg'];
            $datas['cat_type_val'] = array_flip($datas['cat_type']);
        } 
        //排除不需要的分类
        //unset($datas['cat_type']['11'],$datas['cat_type_val']['对戒']);
        output_data($datas);        
    }
    
    /**
     * 婚戒搜索条件
     */
    public function marry_goods_indexOp(){
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
        $datas['warehouse']=array("总部","门店");
        output_data($datas);
    }


    /**
     * 婚戒列表
     */
    public function marry_goods_listOp(){
        
        $curpage = !empty($_REQUEST["curpage"])?$_REQUEST["curpage"]:1;
        $pageSize = !empty($_REQUEST["pagesize"])?$_REQUEST["pagesize"]:12;
        $search_type = !empty($_REQUEST["search_type"])?$_REQUEST["search_type"]:"all";
        $cat_type = !empty($_POST['cat_type'])?$_POST['cat_type']:2;
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
                'minprice'=>$_POST['minprice'],
                'maxprice'=>$_POST['maxprice'],
                'cat_type_id'=>$cat_type,
                'product_type_id'=>$product_id,
                'xilie'=>$_POST['pick_xilie'],
                'jud_type'=>$_POST['jud_type'],
                'price_min'=>isset($_POST['price'][0])?$_POST['price'][0]:'',
                'price_max'=>isset($_POST['price'][1])?$_POST['price'][1]:'',
                'caizhi'=>$_POST['caizhi'],
                'order_by'=>$_POST["order_by"],
                'group_by'=>'style_sn',
            );
            if($search_type == 'hot'){
                $where['is_recommend'] = 1;
            }
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
            if($_POST['tuo_type']){
                if($_POST['tuo_type']==1){
                    $where['tuo_type'] = 1;
                }else{
                    $where['tuo_type'] = array(2,3);
                }
            }
        }
        $api = data_gateway('istyle');
        $result = $api->get_couple_marry_goods_list($where, $curpage, $pageSize);
        //价格计算
        $store_id = $this->store_info['store_id'];
        $result   = $api->build_style_goods_price_list($result,$store_id);
        $data = formatWapPageList($result['return_msg'],'style_goods_list');
        exit(json_encode($data));
    }
    
    /**
     * 现货搜索列表
     */
    public function xianhuo_listOp(){
        $curpage = !empty($_REQUEST["curpage"])?$_REQUEST["curpage"]:1;
        $pageSize = !empty($_REQUEST["pagesize"])?$_REQUEST["pagesize"]:12;
        $search_type = !empty($_REQUEST["search_type"])?$_REQUEST["search_type"]:"all";
        /*if(!empty($_POST['xiangkou'])){
            foreach ($_POST['xiangkou'] as $k=>$xk){
                if($xk > 0.1){
                    $_POST['xiangkou'][$k] = $this->get_xiangkou($xk);
                }
            }
        }*/
        $cat_type = !empty($_POST['cat_type'])?$_POST['cat_type']:2;
        $product_type = array();
        if($cat_type == 15){
            $product_type = array(7, 13, 14, 21);//黄金产品线
            $cat_type = "";
        }
        $store_id = $this->store_info['store_id'];
        $company_id = $this->store_info['store_company_id'];
        if($search_type == 'diff'){
            $goods_ids = $_POST['contrast_list'];
            //商品对比
            $where = array(
                'goods_id'=>$goods_ids?$goods_ids:'-1',
                'order_by'=>$_POST["order_by"],
                'channel_id'=>$store_id,
                'company_id'=>$company_id,
                'extends'=>array('goods_image','goods_price')
            );
        }else{
            $where = array(
                'goods_name'=>$_POST['goods_name'],
                'shape'=>$_POST['shape'],
                'caizhi'=>$_POST['caizhi'],
                'xilie'=>$_POST['pick_xilie'],
                'xiangkou'=>$_POST['xiangkou'],                
                'is_on_sale'=>2,
                'price_min'=>isset($_POST['price'][0])?$_POST['price'][0]:'',
                'price_max'=>isset($_POST['price'][1])?$_POST['price'][1]:'',
                'cat_type'=>$cat_type,
                'product_type'=>$product_type,
                'goods_sn_not'=>array('仅售现货'),
                'channel_id'=>$store_id,
                'company_id'=>$company_id,
                'is_shop'=>$_POST["warehouse"]=="门店"?1:2,
                'order_by'=>$_POST["order_by"],
                'tuo_type'=>$_POST['tuo_type'],//金驼类型
                'extends'=>array('goods_image','goods_price')
            );
            if($search_type == 'hot'){
                /*$model_class = Model('goods_class');
                $gc_list = $model_class->getGoodsClassList(array('gc_parent_id'=>$_POST['gc_parent_id']));
                $rec_gc_id=$gc_list[0]["gc_id"];
                $style_sn = array();
                if ($rec_gc_id > 0) {
                    $rec_list = Model('goods_recommend')->getGoodsRecommendList(array('rec_gc_id'=>$rec_gc_id),'','','*','','rec_goods_id');
                    $style_sn=array_keys($rec_list);
                }
                if(!empty($style_sn)){
                    $where['goods_sn'] = $style_sn;
                }*/
                $where['is_recommend'] = 1;
            }
            if(is_numeric($_POST['price_min']) || is_numeric($_POST['price_max'])){
                $where['price_min'] = $_POST['price_min'];
                $where['price_max'] = $_POST['price_max'];
            }  
            if(is_numeric($_POST['zhiquan'])){
                $where['shoucun'] = $_POST['zhiquan'];
            }

            if(!empty($_POST['cart'][0]) || $_POST['cart'][0] === '0'){

                $carat_min = $_POST['cart'][0];
            }else if($_POST['carat_min']){
                $carat_min = $_POST['carat_min'];
            }

            if(!empty($_POST['cart'][1]) || $_POST['cart'][1] === '0'){
                $carat_max = $_POST['cart'][1];
            }else if($_POST['carat_max']){
                $carat_max = $_POST['carat_max'];
            }
            if($carat_min !== ''){
                if($carat_min>0.25){
                    $where['xiangkou_min'] = $carat_min-0.05;
                }else{
                    $where['xiangkou_min'] = $carat_min;
                }
            }
            if($carat_max !== ''){
                if($carat_max>0.25){
                    $where['xiangkou_max'] = $carat_max+0.04;
                }else{
                    $where['xiangkou_max'] = $carat_max;
                }
            } 

            if($_POST['tuo_type']){
                if($_POST['tuo_type']==1){
                    $where['tuo_type'] = 1;
                }else{
                    $where['tuo_type'] = array(2,3);
                }
            }

        }
        $api = data_gateway('iwarehouse');
        $result = $api->get_warehousegoods_list($where,$curpage, $pageSize);

        $goods_list=array();
        if(!empty($result['return_msg']['data'])){
            foreach ($result['return_msg']['data'] as $key=>$vo){
                $goods = array(
                    'goods_id'=>$vo['goods_id'],
                    'style_sn'=>$vo['goods_sn'],
                    'goods_name'=>$vo['goods_name'],
                    'goods_image'=>$vo['goods_image'],
                    'goods_price'=>$vo['goods_price'],
                );
                $goods_list[] = $goods;
            }
        }
        $result['return_msg']['data']=$goods_list;
        $data = formatWapPageList($result['return_msg'],'warehouse_goods_list');
        $data['datas']['is_shop']=$_POST["warehouse"]=="门店"?1:2;
        exit(json_encode($data));
    }
    
}