<?php
/**
 * 款式商品列表
 * @b1 (c) 2005-2016 kelan Inc.
 * @license    http://官网
 * @link       交流群号：216611541
 * @since      提供技术支持 授权请购买正版授权
 * 
 **/

defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class style_goodsControl extends mobileHomeControl{

	public function __construct() {
        parent::__construct();
    }   
    
    /**
     * 更多定制表单
     */
    public function get_style_goods_diy_indexOp(){
        
        if(empty($_POST['style_sn'])){
            output_error("款号不能为空");
        }        
        $style_sn = $_POST['style_sn'];
        $goods_sn = $_POST['goods_sn'];
        $cert_id  = $_POST['cert_id'];
        $store_id = $this->member_info['store_id'];
        
        $styleApi = data_gateway('istyle');    
        $keys = array("tuo_type","confirm","xiangqian","zhushi_num","cert","color","clarity","cut","facework","kezi");
            
        $result = $styleApi->get_style_goods_diy_index($keys,$style_sn);
        if($result['error']==1){
            output_error($result['error_msg']);
        }else{
            $attr_list = $result['return_msg'];
        }  
        $result = $styleApi->get_style_goods_attr(array('style_sn'=>$style_sn));
        if(!empty($result['return_msg'])){
            $attr_list = array_merge($attr_list,$result['return_msg']);
        } 
         
        $goods_info = array(
            'tuo_type'=>2,
            "is_dingzhi"=>1,
            "xiangqian"=>"需工厂镶嵌",          
        );
        $result = $styleApi->get_style_goods_info(array('goods_sn'=>$goods_sn,'channel_id'=>$store_id,'extends'=>array('goods_price')));
        if($result['error']==1){
            output_error($result['error_msg']);
        }else{
            $_goods_info = $result['return_msg'];
            $goods_info['xiangkou'] = $_goods_info['xiangkou'];
            $goods_info['goods_price'] = $_goods_info['goods_price'];
            if($goods_info['xiangkou']==0){
                $goods_info['tuo_type'] = 1;   
                $goods_info['xiangqian'] = "不需工厂镶嵌";
            }
        }
        if(!empty($cert_id)){
            $diamondApi = data_gateway('idiamond');
            $diaResult = $diamondApi->get_diamond_info(array('cert_id'=>$cert_id));
            if(isset($diaResult['error']) && $diaResult['error']==0){
                $diamond_info = $diaResult['return_msg'];
                $goods_info['carat'] = $diamond_info['carat'];
                $goods_info['cert'] = $diamond_info['cert']?$diamond_info['cert']:'无';
                $goods_info['cert_id'] = $diamond_info['cert_id'];
                $goods_info['color'] = $diamond_info['color'];
                $goods_info['clarity'] = $diamond_info['clarity'];
                $goods_info['cut'] = $diamond_info['cut'];
            }
        }
        if(!empty($attr_list['facework'])){
            if(count($attr_list['facework'])==1){
                $goods_info['facework'] = current($attr_list['facework']);
            }
        }
        if(!empty($attr_list['shoucun'])){
            $attr_list['shoucun'] = buildZhiquan($attr_list['shoucun']);
        }
        $datas['attr_list']  = $attr_list;
        $datas['goods_info'] = $goods_info;
        output_data($datas); 
    }
    /**
     * 跟起版号下单
     */
    public function get_qiban_info_indexOp(){
    
        $goods_id  = $_POST['goods_id'];
        $goods_type = 3;
    
        $styleApi = data_gateway('istyle');
        $keys = array("cert","color","clarity","cut",'kezi');
    
        $result = $styleApi->get_style_goods_diy_index($keys,null);
        if($result['error']==1){
            output_error($result['error_msg']);
        }
        $datas = array();
        $datas['attr_list'] = $result['return_msg'];
        //print_r($datas['attr_list']);
        $member_id = $this->member_info['member_id'];
        $store_id = $this->store_info['store_id'];
        $company_id = $this->store_info['store_company_id'];
    
        $buy_logic_1 = Logic("buy_1");
        $result = $buy_logic_1->apiGetGoodsInfo($goods_id,$goods_type,$store_id,$company_id,$member_id,false);
    
        if($result['state']==false){
            output_error($result['msg']);
        }else{
            $datas['goods_info'] = $result['data'];
        }
        output_data($datas);
    }
    /**
     * 根据现货货号下单
     */
    public function get_warehousegoods_info_indexOp(){
        if(empty($_POST['goods_id'])){
            output_error("请输入货号");
        }
        $goods_id   = $_POST['goods_id'];
        $is_dingzhi = (int)$_POST['is_dingzhi'];
        $goods_type = !empty($_POST['goods_type'])?(int)$_POST['goods_type']:4; //4门店现货 6总部现货
        $member_id  = $this->member_info['member_id'];
        $store_id   = $this->store_info['store_id'];
        $company_id = $this->store_info['store_company_id'];
        $buy_logic_1 = Logic("buy_1");
        $goods_info = array(
            "is_dingzhi"=>$is_dingzhi,
            "xiangqian"=>"不需工厂镶嵌",
            "goods_type"=>$goods_type,
        );
        $result = $buy_logic_1->apiGetGoodsInfo($goods_id,$goods_type,$store_id,$company_id,$member_id,false);
        if(!$result['state']){
            output_error($result['msg']);
        }else{
            $data = $result['data'];
        }    
        if(empty($data)){
            output_error("货号不存在");
        }else if($data['goods_price']==0){
            output_error("货号未设置销售政策");
        }else{
            $goods_info = array_merge($goods_info,$data);
            $style_sn = $goods_info['style_sn'];

            //获取系列图片
            $goods_info['xilie_img_url'] = $this->get_xilie_img_url($style_sn);


            $xiangkou = trim($goods_info['xiangkou']);
            $caizhi   = trim($goods_info['zhuchengse']);
            $shoucun  = $goods_info['shoucun']/1;
        }
        /*
        if($xiangkou<=0){
            $goods_info['xiangqian'] = '不需工厂镶嵌';            
        }*/
        $styleApi = data_gateway('istyle');
    
        $keys = array("tuo_type","confirm","xiangqian","zhushi_num","cert","color","clarity","cut","facework","kezi");
    
        $result = $styleApi->get_style_goods_diy_index($keys,$style_sn);
        if($result['error']==1){
            output_error($result['error_msg']);
        }else{
            $attr_list = $result['return_msg'];
            $result = $styleApi->get_style_goods_attr(array('style_sn'=>$style_sn));
            if(!empty($result['return_msg'])){
                $attr_list = array_merge($attr_list,$result['return_msg']);
            }else{
                if($is_dingzhi){
                    output_error("货品不支持定制");
                }
            }
            if($caizhi!=="" && (empty($attr_list['jinse']) || !in_array($caizhi,$attr_list['jinse']))){
                $attr_list['jinse'][] = $caizhi;
            }
            if($xiangkou!=="" && (empty($attr_list['xiangkou']) || !in_array($xiangkou,$attr_list['xiangkou']))){
                $attr_list['xiangkou'][] = $xiangkou;
            }
            if($shoucun!=0 && (empty($attr_list['shoucun']) || !in_array($shoucun,$attr_list['shoucun']))){
                $attr_list['shoucun'][] = $shoucun;
            }
            if(!empty($attr_list['facework'])){
                if(count($attr_list['facework'])==1){
                    $goods_info['facework'] = current($attr_list['facework']);
                }
            }
            if(!empty($attr_list['zhushi_num'])){
                if($goods_info['zhushi_num']<=0){
                    $goods_info['zhushi_num'] = $attr_list['zhushi_num'];
                }
            }
        }
        //print_r($attr_list);
        if($is_dingzhi==0){
            $result = $styleApi->get_style_attribute($style_sn);
            if($result['error']==0){
                $attr = $result['return_msg'];
                if(!empty($attr[31]) && $attr[31]['value'] != ""){
                    $str = $attr[31]['value'];
                    if(preg_match('/可增([0-9]+?)个手寸/is',$str,$arr)){
                        $shoucun_diff = $arr[1];
                        $attr_list['shoucun'] = array($shoucun);
                        for($i=1;$i<=$shoucun_diff;$i=$i+1){
                            $attr_list['shoucun'][] = $shoucun+$i;
                        }                    
                    }else if(preg_match('/可增减([0-9]+?)个手寸/is',$str,$arr)){
                        $shoucun_diff = $arr[1];
                        $attr_list['shoucun'] = array($shoucun);
                        for($i=1;$i<=$shoucun_diff;$i=$i+1){
                            $attr_list['shoucun'][] = $shoucun-$i;
                            $attr_list['shoucun'][] = $shoucun+$i;
                        }
                        //print_r($attr_list['shoucun']);
                    }else if(preg_match('/不可改圈/is',$str)){
                        $attr_list['shoucun'] = array($shoucun);
                    }
                    sort($attr_list['shoucun']);                    
                }
            }
        }
        if(!empty($attr_list['shoucun'])){
            $attr_list['shoucun'] = buildZhiquan($attr_list['shoucun']);
            if(count($attr_list['shoucun'])>=3){
                array_pop($attr_list['shoucun']);
                array_shift($attr_list['shoucun']);
            }
        }
        $datas = array();
        $datas['attr_list'] = $attr_list;
        $datas['goods_info'] = $goods_info;
        output_data($datas);
    }
    /**
     * 添加款号信息
     */
    public function get_style_info_indexOp(){
        if(empty($_POST['style_sn'])){
            output_error("请输入款号");
        }
        $style_sn = $_POST['style_sn'];
        $styleApi = data_gateway('istyle');
        $keys = array("style_info","tuo_type","confirm","xiangqian","zhushi_num","cert","color","clarity","cut","facework",'kezi');
    
        $result = $styleApi->get_style_goods_diy_index($keys,$style_sn);
        if($result['error']==1){
            output_error($result['error_msg']);
        }else{
            $attr_list = $result['return_msg'];
        }
        $goods_info = array(
            'tuo_type'=>2,
            "is_dingzhi"=>1,
        );    
        if($result['error']==1){
            output_error($result['error_msg']);
        }else{
            $attr_list = $result['return_msg'];
            if(empty($attr_list['style_info'])){
                output_error("款号不存在");
            }else{
                $goods_info = array_merge($goods_info,$attr_list['style_info']);
            }
            $result = $styleApi->get_style_goods_attr(array('style_sn'=>$style_sn));
            if(!empty($result['return_msg'])){
                $attr_list = array_merge($attr_list,$result['return_msg']);
            } else{
                output_error("款号不支持定制");
            }           
            if(!empty($attr_list['facework'])){
                if(count($attr_list['facework'])==1){
                    $goods_info['facework'] = current($attr_list['facework']);
                }
            }
            
        }
        if(!empty($attr_list['shoucun'])){
            $attr_list['shoucun'] = buildZhiquan($attr_list['shoucun']);
        }
        $datas['attr_list']  = $attr_list;
        $datas['goods_info'] = $goods_info;
        output_data($datas);
    }
    
    /**
     * 款式商品搜索表单
     */
    public function style_goods_indexOp(){
        /* if(empty($_POST['cert_id'])){
         output_error("参数错误:cert_id不能为空");
         } */
        $cert_id = $_POST['cert_id'];
        $diamondApi = data_gateway('idiamond');
        $styleApi = data_gateway('istyle');
        $goods_sn = $_POST['goods_sn'];
        if($cert_id){
            $result = $diamondApi->get_diamond_info(array('cert_id'=>$cert_id));
            if($result['error']==0){
                $datas['diamond'] = $result['return_msg'];
            }else{
                output_error("钻石不存在或者已下架，请重新挑选");
            }
        }else{
            $datas['diamond'] = array();
        }
        $goods_info = array("is_xianhuo"=>0,'cat_type'=>2);
        if($goods_sn && is_numeric($goods_sn)){
            $goods_info['is_xianhuo'] = 1; 
        }
        $datas['goods_info'] = $goods_info;
        $result = $styleApi->get_style_goods_index(array('xilie','caizhi','shape',"is_xianhuo",'jt_cat_type'));
        if(is_array($result['return_msg'])){
            $datas['attr_list'] = $result['return_msg'];
        }
        output_data($datas);
    }
    /**
     * 款式商品（空托）搜索列表
     */
    public function style_goods_listOp(){
        $curpage = !empty($_POST["curpage"])?$_POST["curpage"]:1;
        $pageSize = !empty($_POST["pagesize"])?$_POST["pagesize"]:12;
        $search_type = !empty($_POST["search_type"])?$_POST["search_type"]:"all";
        
        $store_id = $this->member_info['store_id'];
        $company_id = $this->member_info['store_company_id'];
        
        $_POST["order_by"] = $_POST["order_by"]?$_POST["order_by"]:"1|1";
/*
        if(!empty($_POST['shape'])){
            $_POST['shape'][] = 0;
        }*/        
        $styleApi = data_gateway('istyle');
        if($search_type == 'contrast'){
            $goods_ids = $_POST['contrast_list']; 
            //商品对比
            $where = array(
                'store_id' => $store_id,
                'goods_sn'=>$goods_ids?$goods_ids:'-1',
                'order_by'=>$_POST["order_by"],                        
            );
        }else{
            $is_tsyd = 0;
            if($_POST["cert_id"]){
                $cert_id = $_POST["cert_id"];
                $diamondApi = data_gateway('idiamond');
                $result = $diamondApi->get_tsyddia_by_cert_id($cert_id, $store_id, $company_id);
                if($result['error']==0){
                    $tsydDia = $result['return_msg'];
                    $tsyd_cert_id = $tsydDia['cert_id'];
                    $tsyd_carat = $tsydDia['carat'];                     
                    $carat = $_POST['carat_min'];                    
                    $is_tsyd = 1;
                }                
                
            }
            //商品列表
            $where = array(
                'store_id' => $store_id,
                'style_name'=>$_POST['goods_name'],
                'cat_type_id'=>$_POST['cat_type'],
                'shape'=>$_POST['shape'],
                'caizhi'=>$_POST['caizhi'],
                'yanse'=>$_POST['yanse'],
                'xilie'=>$_POST['xilie'],            
                'price_min'=>$_POST['price_min'],
                'price_max'=>$_POST['price_max'],
                'is_ok'=>1,
                'order_by'=>$_POST["order_by"],
                'group_by'=>'style_sn',                         
            );
            if(!empty($_POST['carat_min'])){
                if($_POST['carat_min']>0.25){
                   $where['xiangkou_min'] = $_POST['carat_min']-0.041;
                }else{
                    $where['xiangkou_min'] = $_POST['carat_min']-0.001;
                }
            }
            if(!empty($_POST['carat_max'])){
                if($_POST['carat_max']>0.25){
                   $where['xiangkou_max'] = $_POST['carat_max']+0.05;
                }else{
                    $where['xiangkou_max'] = $_POST['carat_max'];
                }
            }
            if($search_type == 'recommend'){
                $where['is_recommend'] = 1;
            }
            if(!empty($_POST['goods_name'])){
                $res = $styleApi->get_style_sn_list(array('keyword'=>$_POST['goods_name']));
                $exclude_style_sn = array('KLRW029477');//需要排除的款
                if($res['error']==0 && !in_array(trim($_POST['goods_name']), $exclude_style_sn)){
                    $where['style_sn'] = $res['return_msg'];
                }else{
                    $where['style_sn'] = "-1";
                }                
                unset($where['style_name']);
            }
            //if($is_tsyd ==1){
                //$where['is_tsyd'] = 1;
            //}
            if(($where['price_min'] || $where['price_max']) && $where['order_by'] == '1|1') {
                // 如果有价格选项，并且没有排序条件, 默认按价格递增
                $where['order_by'] = '5|1';
            }
        }
        
        if($is_tsyd){
            //unset($where['shape']);
            $pageSize = $pageSize*2;//对戒查询每页数据加倍
        }
        //print_r($where);
        $result = $styleApi->get_style_goods_list($where,$curpage, $pageSize);
        if($is_tsyd){
            //对戒戒托 过滤
            $result = $styleApi->build_tsyd_goods_list($result,$carat,$tsyd_carat);
        }
        //$result = $styleApi->build_style_goods_price_list($result,$store_id);//价格计算
        //print_r($result);
        $data = formatWapPageList($result['return_msg'],'style_goods_list');
        //print_r($data);
        exit(json_encode($data));
    }
    
    /**
     * 获取款式图库
     */
    public function get_style_galleryOp(){
        $style_sn = $_REQUEST['style_sn'];
        $api = data_gateway('istyle');
        $result = $api->get_style_gallery(array('style_sn'=>$style_sn,'image_place'=>array(1,2,3,4)));
        $style_goods_list = !empty($result['return_msg'])?$result['return_msg']:array();
        if(empty($style_goods_list)){
            $style_goods_list[] = array(
                'style_sn'=>$style_sn,
                'img_ori'=>'/data/upload/shop/common/default_goods_image_1280.gif',
                'middle_img'=>'/data/upload/shop/common/default_goods_image_240.gif'
            );           
        }
        $datas = array('style_goods_gallery'=>$style_goods_list);
        output_data($datas);             
    }
    /**
     * 查询商品详细信息
     */
    public function get_style_goods_detailOp(){
        
        $goods_sn = $_POST['goods_sn'];
        $cert_id  = $_POST['cert_id'];
        $store_id = $this->store_info['store_id'];
        $company_id = $this->store_info['store_company_id'];
        
        $diamond_api = data_gateway('idiamond');
        $styleApi   = data_gateway('istyle');       
        $result = $styleApi->get_style_goods_info(array('goods_sn'=>$goods_sn,'channel_id'=>$store_id,'extends'=>array('goods_price')));
        if($result['error']==1){
            output_error("商品不存在");
        }else{
            $goods_info = $result['return_msg'];            
        }
        if($goods_info['is_ok']!=1){
            output_error("商品已下架");
        }else if($goods_info['goods_price'] <=0 ){
            output_error("商品未设置销售政策");
        }
        $goods_info['cert_id'] = $cert_id;

        //配对款号
        $style_sn = $goods_info['style_sn'];

        //获取系列图片
        $goods_info['xilie_img_url'] = $this->get_xilie_img_url($style_sn);

        $tsyd_style_sn = "";
        $tsyd_cert_id  = "";
        $tsyd_goods_sn = "";
        /*对戒查询*/        
        $couplelist = $styleApi->get_couple_info(array('style_sn'=>$style_sn), " style_sn1,style_sn2 ");
        if(!empty($couplelist)){
            $coupleinfo = array_flip(array_values($couplelist));
            unset($coupleinfo[$style_sn]);
            $style_couple = array_flip($coupleinfo);
            $tsyd_style_sn = isset($style_couple[0])?$style_couple[0]:$style_couple[1];
        }

        $result = $styleApi->get_style_goods_attr(array('style_sn'=>$style_sn));    
        //print_r($result);exit;
        if(!empty($result['return_msg'])){
           $attr_list = $result['return_msg'];
        }else{
           output_error("商品不支持定制");
        }      
        $result = $styleApi->get_style_goods_diy_index(array("kezi"),$style_sn);
        if($result['error']==1){
            output_error($result['error_msg']);
        }else{
            $attr_list = array_merge($attr_list,$result['return_msg']);
        }      
        if($cert_id){
            //取出可用镶口数组，删除不可用镶口元素
            $res = $diamond_api->get_diamond_info(array('cert_id'=>$cert_id));
            if($res['error']==0){
                //$goods_info['carat'] = $res['return_msg']['carat'];
                $xiangkouArr = getXiangkouByStone($res['return_msg']['carat']);
                foreach ($attr_list['xiangkou'] as $k=>$xk){
                    if(!in_array($xk,$xiangkouArr)){
                        unset($attr_list['xiangkou'][$k]);
                    }
                }
            }            
        }
        if($tsyd_style_sn != ''){
            if($cert_id){                
                $result = $diamond_api->get_tsyddia_by_cert_id($cert_id, $store_id, $company_id);
                if($result['error']==0){
                    $tsydDia = $result['return_msg'];
                    $tsyd_cert_id = $tsydDia['cert_id'];
                    $tsyd_carat = $tsydDia['carat'];
                }else{
                    //output_error("所选钻石不是天生一对钻石".$tsyd_style_sn);
                }   
            }
            
            if($tsyd_cert_id && $tsyd_carat>0){
                $tsyd_xiangkou = getXiangkouByStone($tsyd_carat) ;
                $tsydWhere = array(
                    'store_id' => $store_id,
                    'style_sn'=>$tsyd_style_sn, 
                    'xiangkou_min'=>min($tsyd_xiangkou)-0.001, 
                    'xiangkou_max'=>max($tsyd_xiangkou),                   
                    'group_by'=>'style_sn'                    
                );                
                $result = $styleApi->get_style_goods_list($tsydWhere,1, 1);
                if(!empty($result['return_msg']['data'][0])){
                    $tsyd_goods_sn = $result['return_msg']['data'][0]['goods_sn'];
                }           
            }else{            
                $tsydWhere = array(
                    'store_id' => $store_id,
                    'style_sn'=>$tsyd_style_sn,
                    'group_by'=>'style_sn'                    
                );
                //print_r($tsydWhere);
                $result = $styleApi->get_style_goods_list($tsydWhere,1, 1);
                //print_r($result);
                if(!empty($result['return_msg']['data'][0])){
                    $tsyd_goods_sn = $result['return_msg']['data'][0]['goods_sn'];
                } 
            }
            if($tsyd_goods_sn==""){
                $tsyd_goods_sn = '-1';
                /*
               $tsydWhere = array('style_sn'=>$tsyd_style_sn);
               $result = $styleApi->get_style_goods_sn($tsydWhere);
               //print_r($tsydWhere);
               if($result['error']==0){
                   $tsyd_goods_sn = $result['return_msg'];
               }else{
                   output_error("所选天生一对钻石与对戒不搭配");
               }*/
            }
            
        }
        
        $goods_info['tsyd_style_sn'] = $tsyd_style_sn;
        $goods_info['tsyd_goods_sn'] = $tsyd_goods_sn;
        $goods_info['tsyd_cert_id'] = $tsyd_cert_id;       
        if(!empty($attr_list['shoucun'])){
            $attr_list['shoucun'] = buildZhiquan($attr_list['shoucun']);
        }
        $datas = array('goods_info'=>$goods_info,'attr_list'=>$attr_list);
        output_data($datas);
    }    
    /**
     * 计算查询商品价格
     */
    public function get_style_goods_priceOp(){
        $store_id = $this->member_info['store_id'];
        //$channel_id = $channel_id?$channel_id:178;
        
        $styleApi   = data_gateway('istyle');        
        $where = array(
            'style_sn'=>$_POST['style_sn'],
            'caizhi'=>$_POST['caizhi'],
            'yanse'=>$_POST['yanse'],
            'xiangkou'=>$_POST['xiangkou'],
            'shoucun'=>round($_POST['shoucun']),
            'channel_id'=>$store_id
        );
        $result = $styleApi->get_style_goods_price($where);
        //print_r($result);
        if(empty($result['return_msg'])){
            output_error("取价失败");  
        }
        $datas = array('goods_info'=>$result['return_msg']);
        output_data($datas);
    }    
    /**
     * 获取定制价格(空托定制和成品定制价格)
     */
    public function get_dingzhi_priceOp(){
        
        $store_id = $this->member_info['store_id'];
        $require_params = array(
            'tuo_type'=>'金托类型不能为空',
            'style_sn'=>'款号不能为空',
            'xiangqian'=>'镶嵌方式不能为空',
            'jinse'=>'材质不能为空',
            'shoucun'=>'指圈不能为空',
            'xiangkou'=>'镶口不能为空'
        );
        foreach ($require_params as $key=>$msg){
            if($_POST[$key]==""){
                output_error($msg);
            }
        }
        
        $is_dingzhi = (int)$_POST['is_dingzhi'];
        $style_sn = $_POST['style_sn'];
        $jinse = $_POST['jinse'];
        $tuo_type = $_POST['tuo_type'];
        $xiangkou = $_POST['xiangkou'];
        $shoucun  = round($_POST['shoucun']);   
        $carat  = $_POST['carat'];
        $color  = $_POST['color'];
        $cert   = $_POST['cert'];
        $cert_id   = $_POST['cert_id'];
        $clarity  = $_POST['clarity'];
        $cut  = $_POST['cut'];
        $xiangqian  = $_POST['xiangqian'];
        $policy_id = $_POST['policy_id'];
        $facework = $_POST['facework'];
        //分解材质和材质颜色 ID
             
        $jinse_arr = explode("|",$jinse);
        if(count($jinse_arr)==2){
            $caizhi = $jinse_arr[0];//caizhi 数字id
            $yanse  = $jinse_arr[1];//yanse 数字id
        } else{
            output_error("不支持定制：所选材质不支持定制");
        }


        //期货或现货转定制，且款号非DIA，【表面工艺】字段必填
        if($style_sn != 'DIA' && $facework ==''){
            $error = "表面工艺必填";
            output_error($error);
        }

        if(in_array($xiangqian,array("需工厂镶嵌","客户先看钻再返厂镶嵌","工厂配钻，工厂镶嵌","镶嵌4C裸钻"))){
            if($xiangkou>0 && ($carat <=0||empty($color) || empty($clarity))){
                output_error("{$xiangqian}时，主石颜色，净度，石重不能为空");
            }

            /*if($xiangkou>0 && $carat <=0){
                $error = "主石单颗重不能为空";
                output_error($error);
            }else if($xiangkou>0 && empty($color)){
                $error = "主石颜色不能为空";
                output_error($error);
            }else if($xiangkou>0 && empty($clarity)){
                $error = "主石净度不能为空";
                output_error($error);
            }*/



            if($_POST['zhushi_num']<=0){
                $error = "主石粒数不能为0";
                output_error($error);
            }
            if(!checkXiangkouStone($xiangkou,$carat)){
                $error = "镶口石重不匹配";
                output_error($error);
            }
            
        }

        if(in_array($xiangqian,array("需工厂镶嵌","客户先看钻再返厂镶嵌")) && $cert_id == ''){
            $error = "需工厂镶嵌、客户先看钻再返厂镶嵌时 证书号必填";
            output_error($error);
        }
        if(in_array($xiangqian,array("工厂配钻，工厂镶嵌")) && $cert_id != ''){
            $error = "工厂配钻，工厂镶嵌、镶嵌4C裸钻 证书号不能填写";
            output_error($error);
        }


        
        $styleApi  = data_gateway('istyle');
        
        if($tuo_type == 1 && $xiangkou>0 && $carat>0){
            if(empty($cert)){
                output_error("证书类型不能为空");
            }          
            if(empty($policy_id)){
                output_error("请点击【取价】选择成品定制价格");
            }

            //成品定制1.2:非成品定制码下单
            $where = array(
                'style_sn'=>$style_sn,
                'tuo_type'=>$tuo_type,
                'caizhi'=>$caizhi,
                'yanse'=>$yanse,
                'xiangkou'=>$xiangkou,
                'carat'=>$carat,
                'shoucun'=>$shoucun,
                'cert'=>$cert,                
                'color'=>$color,
                'clarity'=>$clarity,
                'channel_id'=>$store_id,
                'policy_id'=>$policy_id
            );
            $result = $styleApi->get_cpdz_price($where);
            if(isset($result['error']) && $result['error']==0){
                $goods_price_list = array();
                foreach ($result['return_msg']['sprice'] as $vo){
                    $goods_price_list[$vo['sale_price']] = $vo;
                }
                krsort($goods_price_list);
                $goods_info = current($goods_price_list);  
                $goods_info['goods_price'] = $goods_info['sale_price'];
                $datas = array('goods_info'=>$goods_info);
                output_data($datas);                
            }else{
                output_error("请点击【取价】选择成品定制价格");                
            }
            
        }else{

            if((!empty($clarity) || !empty($color) || $carat>0) && ($xiangkou==0)){
                $error = "亲，镶口为0时，不能镶嵌石头";
                output_error($error);
            }    
            //空托或成品下单
            $where = array( 
                'style_sn'=>$style_sn,
                'caizhi'=>$caizhi,
                'yanse'=>$yanse,
                'xiangkou'=>$xiangkou,
                'shoucun'=>$shoucun,
                'channel_id'=>$store_id
            );
            $result = $styleApi->get_style_goods_price($where);
            if(empty($result['return_msg'])){
                output_error($result['error_msg']);
            }
            $goods_info = $result['return_msg'];
            $goods_info['goods_id'] = $goods_info['goods_sn'];
            $datas = array('goods_info'=>$goods_info); 
            output_data($datas);

      }
    }
    /**
     * 成品定制价格列表
     */
    public function get_cpdz_price_listOp(){
        //echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        $store_id = $this->member_info['store_id'];
        $require_params = array(
            'style_sn'=>"款号不支持定制",
            'jinse'=>'所选材质不支持定制',
            'xiangkou'=>"镶口不能为空",
            'shoucun'=>"指圈不能为空",            
            'carat'=>"主石单颗重不能为空"            
        );
        foreach ($require_params as $key=>$msg){
            if(!isset($_REQUEST[$key]) || $_REQUEST[$key]==""){
                output_error($msg);
            }
        }        
        $style_sn = $_REQUEST['style_sn'];
        $jinse = $_REQUEST['jinse'];
        $xiangkou = $_REQUEST['xiangkou'];
        $shoucun = round($_REQUEST['shoucun']);        
        $carat  = $_REQUEST['carat'];  
        if(!checkXiangkouStone($xiangkou,$carat)){
            output_error("镶口石重不匹配");
        }
        //分解材质和材质颜色 ID            
        $jinse_arr = explode("|",$jinse);
        if(count($jinse_arr)==2){
            $caizhi = $jinse_arr[0];//caizhi 数字id
            $yanse  = $jinse_arr[1];//yanse 数字id
        } else{
            output_error("不支持定制：所选材质不支持定制");
        }
        $styleApi  = data_gateway('istyle');
        $where = array(
            'style_sn'=>$style_sn,
            'caizhi'=>$caizhi,
            'yanse'=>$yanse,
            'shoucun'=>$shoucun,
            'xiangkou'=>$xiangkou,
            'carat'=>$carat,
            'channel_id'=>$store_id
        );
        $res = $styleApi->get_cpdz_price_list($where);
        //print_r($res);
        if($res['error']==0){
            $cpdz_price_list = $styleApi->build_cpdz_price_list($res['return_msg'],'group');            
            $datas = array('cpdz_price_list'=>$cpdz_price_list);
            output_data($datas);
        }else{
            output_error($res['error_msg']);
        }               
    }

    /**
     * 获取分类推荐商品
     */
    public function get_recommend_styleOp(){
        $gc_parent_id=$_POST["gc_parent_id"];
        $rec_gc_id=$_POST["rec_gc_id"];
        $model_class = Model('goods_class');
        $gc_list = $model_class->getGoodsClassList(array('gc_parent_id'=>$gc_parent_id));
        $goods_list = array();
        if($rec_gc_id==0 && count($gc_list)>0) $rec_gc_id=$gc_list[0]["gc_id"];
        if ($rec_gc_id > 0) {
            $rec_list = Model('goods_recommend')->getGoodsRecommendList(array('rec_gc_id'=>$rec_gc_id),'','','*','','rec_goods_id');
            if (!empty($rec_list)) {
                $store_id = $this->store_info['store_id'];
                $condition = array(
                    'store_id'=>$store_id,
                    'style_sn'=>array_keys($rec_list)
                    );
                $styleApi = data_gateway('istyle');
                $result = $styleApi->get_style_list($condition,1,10);
                //$result = $styleApi->build_style_goods_price_list($result,$store_id);//价格计算
                $goods_list=$result['return_msg']['data'];
            }
        }
        $datas['child_type_list'] = $gc_list;
        $datas['style_goods_list'] = $goods_list;
        output_data($datas);
    }

    private function  get_xilie_img_url($style_sn){
        $base_style_info_model = new base_style_infoModel();
        //款式分类
        $style_info = $base_style_info_model->getStyleInfo(['style_sn'=>$style_sn]);
        //产品线
        $cat_type_name = $base_style_info_model->getStyleTypeName($style_info['style_type']);

        if(empty($style_info)) return '';

        //配置
        $order_detail_img_arr = C("order_detail_img");
        //款号:
        $style_sn_arr = $order_detail_img_arr['style_sn'];
        //系列及款式分类
        $xilie_and_cat_arr = $order_detail_img_arr['xilie_and_cat'];
        //单独系列或者单独款式分类
        $xilie_or_cat_arr = $order_detail_img_arr['xilie_or_cat'];
        //其他
        $other_arr = $order_detail_img_arr['other'];

        //款号:如果款号在配置里，则取对应的图片
        foreach ($style_sn_arr as $arr){
            if(in_array($style_sn,$arr['style_sn_s'])){
                return $arr['img_url'];exit;
            }
        }

        $xilie = array();
       if(!empty($style_info['xilie'])) {
          // 查询系列
          $where = array(
             'id' => array('in', explode(',', trim($style_info['xilie'], ',')))
          );
          $xilie = $base_style_info_model->getStyleXilieList($where);
          $xilie = array_values(array_column($xilie,'name'));

        }

        //系列及款式分类
        foreach ($xilie_and_cat_arr as $arr){
            if(!empty($xilie) && in_array($arr['xilie'],$xilie) && $cat_type_name == $arr['cat_type_name']){
                return $arr['img_url'];exit;
            }
        }

        

        //单独系列或者单独款式分类
        foreach ($xilie_or_cat_arr as $arr){
            if(!empty($xilie) && in_array($arr['name'],$xilie)){
                return $arr['img_url'];exit;
            }
            if($cat_type_name == $arr['name']){
                return $arr['img_url'];exit;
            }
        }

        //其他
        foreach ($other_arr as $arr){
           //系列
            if(!empty($xilie) && $arr['key'] == 'xilei' && in_array($arr['name'],$xilie)){
               return $arr['img_url'];
            }
            //款式分类
            if($arr['key'] == 'cat_type_name' && $arr['name'] == $cat_type_name){
                return $arr['img_url'];
            }

            if($arr['key'] == 'title' && strpos($style_info['style_name'],$arr['name'])){
                return $arr['img_url'];
            }
        }

        return '';


    }
}