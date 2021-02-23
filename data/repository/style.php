<?php

class style extends base_repository implements istyle  {
    /**
     * 款式商品列表
     * @see istyle::get_goods_list()
     */
    public function get_style_list($where,$page,$page_size) {
        $where['page']=$page;
        $where['pageSize']=$page_size;
        $where['group_by']=1;
        $result= $this->invoke("getStyleGoodsList",$where);
        return $result;
    }

    /**
     * 商品搜索表单
     * @see istyle::get_style_goods_index()
     */
    public function get_style_goods_index($keys){
        //$where['keys'] = $keys;
        $data = array();
        if(!empty($keys)){
            foreach ($keys as $key){
                if($key == 'xilie'){

                    $data['xilie'] = array(1 => '天鹅湖', 2 => '天使之吻', /*3 => '怦然心动',*/5=>'天使之翼',8=>'天生一对',24=>'香邂巴黎',20=>'心之吻','27'=>'皇室公主');
                                    
                }else if($key == "caizhi"){

                    $data['caizhi'] = array(1 => '18K', 2 => 'PT950');
                    
                }else if($key =="shape"){

                    $data['shape'] = array(1 => '圆形', 2 => '公主方形', 3 => '祖母绿形', 4 => '橄榄形', 5 => '椭圆形',/*6 => '水滴形',*/ 7 => '心形', 8 => '坐垫形',15=>'梨形',/* 17 => '马眼形',18 => '长方形'*/);
                    
                }else if($key =="cat_type"){

                    $data['cat_type'] = array(2 => '钻戒', 11 => '对戒', 10 => '男戒', 3 => '吊坠', 4 => '项链', 5 => '耳饰', 7 => '手链', 15 => '黄金');
                }else if($key =="jt_cat_type"){
                    $data['jt_cat_type'] = array(2 => '钻戒', 11 => '对戒', 10 => '男戒', 3 => '吊坠', 4 => '项链', 5 => '耳饰', 7 => '手链');
                
                }else if($key =="cart"){

                    $data['cart'] = array('无钻'=>array(0, 0), '30分以下'=>array(0, 0.29), '30-50分'=>array(0.3,0.5), '50-70分'=>array(0.5,0.7), '50-70分'=>array(0.7,1), '1克拉以上'=>array(1.01, 10));
                }else if($key =="price"){

                    $data['price'] = array('3000以下'=>array(0,2999), '3000-5000' =>array(3000,5000), '5000-10000'=>array(5000, 10000), '10000-15000'=>array(10000, 15000), '15000-30000'=>array(15000, 30000), '30000以上'=>array(30000,10000000));

                }else if($key == "caizhi"){

                    $data['caizhi'] = array('18K', 'PT950');
                }else if($key == "tuo_type"){

                    $data['tuo_type'] = array(2=>'空托', 1=>'成品');
                }else if($key == "zhiquan"){

                    $data['zhiquan'] = array('9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26');
                }else if($key == "jud_type"){

                    //$data['jud_type'] = array('吊坠/项链', '手链/手镯', '耳饰');
                }else if($key == "pick_xilie"){

                    $data['pick_xilie'] = array(24=>'香邂巴黎', 6=>'星耀美钻', 20=>'心之吻', 8=>'天生一对', 5=>'天使之翼');
                }else if($key == "is_xianhuo"){
                    $data['is_xianhuo'] = array(0=>"期货",1=>"现货");
                }
            }
        }else{
            return array('error'=>1, 'return_msg'=>$data);
        }
        return array('error'=>0, 'return_msg'=>$data);
        //return $this->invoke("getStyleGoodsIndex",$where);
    }
    /**
     * 款式商品列表
     * @see istyle::get_goods_list()
     */
    public function get_style_goods_list($where,$page,$page_size) {
        $where['page']=$page;
        $where['pageSize']=$page_size;
        $result = $this->invoke("getStyleGoodsList",$where);        
        return $result;
    }
    /**
     * 图片重新查询
     * @param unknown $result
     * @return unknown
     */
    public function build_style_goods_image_list($result){
        if(!empty($result['return_msg']['data'])){
            $styleApi = new style_apiModel();
            foreach ($result['return_msg']['data'] as $key=>&$vo){
                $res = $styleApi->getStyleGalleryInfo(array('style_sn'=>$vo['style_sn'],'image_place'=>array(1,2,3,4)));
                //print_r($res);
                if(!empty($res['return_msg'])){
                    $vo['goods_image'] = $res['return_msg']['big_img'];
                }                
            }
            
        }        
        return $result;
    }
    /**
     * 商品价格计算（对分页列表商品进行价格计算）
     * @see istyle::build_style_goods_price_list()
     */
    public function build_style_goods_price_list($result,$store_id){
        if(!empty($result['return_msg']['data'])){
            $policyGoodsModel = new app_salepolicy_goodsModel();
            foreach ($result['return_msg']['data'] as $key=>&$ginfo){                
                $ginfo['goods_price'] = $policyGoodsModel->getQihuoPrice($goodsInfo,$store_id);
            }
        }
        //重置索引
        if(!empty($result['return_msg']['data'])){
            $result['return_msg']['data'] = array_merge($result['return_msg']['data']);
        }
        return $result;
    }

    /**
     * 款式图库列表
     * @see istyle::get_style_gallery()
     */
    public function get_style_gallery($where) {
        //return $this->invoke("getStyleGalleryList",$where);
        $styleApiModel = new style_apiModel();
        return $styleApiModel->getStyleGalleryList($where);
    }
    /**
     * 根据款式商品   聚合可用 【材质】，【材质颜色】，【指圈】，【镶口】属性列表
     * @see istyle::get_style_goods_attr()
     */
    public function get_style_goods_attr($where){
        //return $this->invoke("getStyleGoodsAttrList",$where);
        $styleApiModel = new style_apiModel();
        return $styleApiModel->getStyleGoodsAttr($where);
    }
    /**
     * 获取款式商品价格
     * @param unknown $where
     * @return mixed
     */
    public function get_style_goods_price($where){
        //return $this->invoke("getStyleGoodsPrice",$where);
        $styleApiModel = new style_apiModel();
        return $styleApiModel->getStyleGoodsPrice($where);
    }
    /**
     * 查询款式商品
     * @see istyle::get_style_goods_info()
     */
    public function get_style_goods_info($where){        
        //return $this->invoke("getStyleGoodsInfo",$where);
        $styleApiModel = new style_apiModel();
        return $styleApiModel->getStyleGoodsInfo($where);
    }
    
    public function get_style_goods_diy_index($keys,$style_sn=''){        
        $where['keys'] = $keys;
        $where['style_sn'] = $style_sn;
        return $this->invoke("getStyleGoodsDiyIndex",$where);
    }

    /**
     * 查询款式商品
     * @see istyle::get_style_goods_info()
     */
    public function get_style_info($where){
        return $this->invoke("GetStyleInfo",$where);
    }

    /**
     * 查询款式属性
     * @see istyle::get_param_by_sn()
     */
    public function get_param_by_sn($where)
    {
        $styleApiModel = new style_apiModel();
        return $styleApiModel->getStyleParamsBySn($where);
        //return $this->invoke("getStyleParamsBySn",$where);
    }

    /**
     * 对戒婚戒商品列表
     * @see istyle::get_goods_list()
     */
    public function get_couple_marry_goods_list($where,$page,$page_size) {
        $where['page']=$page;
        $where['pageSize']=$page_size;
        $result= $this->invoke("getStyleGoodsList",$where);
        return $result;
    }
    
    public function get_cpdz_price($where){
        $result= $this->invoke("getCpdzPrice",$where);
        return $result;
    }
    public function get_cpdz_price_list($where){
        $result= $this->invoke("getCpdzPriceList",$where);
        return $result;
    }
    /**
     * 格式化成品定制价格列表
     * @see istyle::format_cpdz_price_list()
     */
    public function build_cpdz_price_list($cpdz_price_list,$type="group"){
        $datalist = array();
        foreach ($cpdz_price_list as $key=>$vo){
            $data = array(
                'goods_id'=>$vo['goods_id'],
                'goods_key'=>$vo['goods_key'],
                'style_sn'=>$vo['goods_sn'],
                'cert'=>$vo['cert'],
                'color'=>$vo['color'],
                'clarity'=>$vo['clarity'],
                'shape'=>$vo['shape'],
                'policy_id'=>$vo['id'],
                'policy_name'=>$vo['policy_name'],
                'goods_price'=>$vo['sale_price'],
            );            
            $cpdz_price_list[$key] = $data;
        }
        
        if($type == "group"){
            usort($cpdz_price_list, function($x, $y) {
                return $x['goods_price'] < $y['goods_price']?1:-1;
            });
            foreach ($cpdz_price_list as $key=>$vo){
                $key = $vo['cert'].'-'.$vo['color'].'-'.$vo['clarity'];
                if(!isset($datalist[$key])){
                    $datalist[$key] = $vo;                    
                }
                $datalist[$key]['items'][$vo['goods_price']] = $vo;
            } 
            
            $cert_sort = array_flip(array_unique(array_column($datalist,"cert")));
            foreach ($datalist as $key=>$vo){
                $vo['cert_sort'] = $cert_sort[$vo['cert']];
                if(!empty($vo['items'])){
                    //重置索引
                    $vo['items'] = array_merge($vo['items']);
                }
                $datalist[$key] = $vo;
            }
            usort($datalist, function($x, $y) {
                return $x['cert_sort'] >= $y['cert_sort']?1:-1;
            });
        }
        return $datalist;
        
    }
    public function get_style_goods_sn($where){
        //$result= $this->invoke("getStyleGoodsSn",$where);
        //return $result;
        $styleApiModel = new style_apiModel();
        return $styleApiModel->getStyleGoodsSn($where);
    }
    
    public function get_style_attribute($style_sn){
        $where  = array('style_sn'=>$style_sn);
        //$data = $this->invoke("GetStyleAttribute",$where);
        //print_r($data);exit;
        $styleApiModel = new style_apiModel();
        return $styleApiModel->getStyleAttribute($where);              
    }
    /**
     * 修改款式表
     * @param unknown $data
     * @param unknown $where
     * @return mixed
     */
    public function update_style_info($data,$where){
        $where['data'] = $data;
        return $this->invoke("updateStyleInfoById",$where);
    }

    /**
     * 获取情侣戒信息
     * @param unknown $where
     * @param unknown $field
     */
    public function get_couple_info($where, $field)
    {
        $styleApiModel = new style_apiModel();
        return $styleApiModel->getCoupleInfo($where, $field);  
    }

    /**
     * 根据一个对戒款号获取另一个对戒款
     * @param unknown $where
     * @param unknown $field
     */
    public function reason_couple_style_other($style_sn)
    {
        $result = array('error'=>0,'error_msg'=>'','return_msg'=>"");
        //$styleApiModel = new style_apiModel();
        //return $styleApiModel->getCoupleInfo($where, $field);
        $style_other = "";
        $couplelist = $this->get_couple_info(array('style_sn'=>$style_sn), " style_sn1,style_sn2 ");
        if(!empty($couplelist)){
            $coupleinfo = array_flip(array_values($couplelist));
            unset($coupleinfo[$style_sn]);
            $style_couple = array_flip($coupleinfo);
            $style_other = isset($style_couple[0])?$style_couple[0]:$style_couple[1];
        }
        if(!empty($style_other)){
            $result['return_msg'] = $style_other;
        }else{
            $result['error'] = 1;
        }
        return $result;
    }

    /**
     * 根据款号获取可做镶口
     * @param unknown $where
     * @param unknown $field
     */
    public function reason_style_by_xiangkou($style_sn)
    {
        $result = array('error'=>0,'error_msg'=>'','return_msg'=>array());
        //$styleApiModel = new style_apiModel();
        //return $styleApiModel->reasonStyleByXiangkou($style_sn);
        $xiangkou_arr = array();  
        $data = $this->get_style_attribute($style_sn);
        if($data['error'] == 0){
            $xiangkou_value = isset($data['return_msg'][1]['value'])?$data['return_msg'][1]['value']:"";
            if(!empty($xiangkou_value)){
                $xiangkou_arr = explode(",", $xiangkou_value);
                $xiangkou_arr = array_filter($xiangkou_arr);
            }
        }
        if(!empty($xiangkou_arr)){
            $result['return_msg'] =$xiangkou_arr;
        }else{
            $result['error'] = 1;
        }
        return $result;
    }

    /**
    *镶口推石重规则=》
    *镶口25分及以上，按上4下5分匹配石头，
    *镶口25分以下，直接根据镶口大小匹配石头大小
     */
    public function get_stone_by_xiangkou($xiangkou)
    {
        $xiangkou = bcmul($xiangkou, 1000, 3);
        $xiangkou = intval($xiangkou);
        if (bccomp($xiangkou, 0, 3) != -1 && bccomp($xiangkou, 250, 3) == -1)
        {
            
        }elseif(bccomp($xiangkou, 250, 3) != -1){
            $stone_min = bccomp(bcsub($xiangkou, 50, 3), 0, 3) ==1?bcsub($xiangkou, 50, 3):0;
            $stone_max = bcadd($xiangkou, 40, 3);
            return array($stone_min/1000, $stone_max/1000);
        }
        return array($xiangkou/1000, $xiangkou/1000);
    }

    //石重范围计算出最大区间
    public function section_minmax_by_stone($data)
    {
        $cart_min = $cart_max = 0;
        if(!empty($data)){
            foreach ($data as $val) {
                $arr_min[] = $val[0];
                $arr_max[] = $val[1];
            }
            $cart_min = min($arr_min);
            $cart_max = min($arr_max);
        }
        return array($cart_min, $cart_max);
    }

    /**
     * 根据镶口匹配石重范围
     * @param unknown $xiangkou_arr 可做镶口数组
     */
    public function stone_scope_by_xiangkou($xiangkou_arr)
    {
        $result = array('error'=>0,'error_msg'=>'','return_msg'=>array());
        $stone_arr = array();
        if(!empty($xiangkou_arr)){
            foreach ($xiangkou_arr as $xiangkou) {
                //根据镶口匹配可做石重范围
                list($_carat_min,$_carat_max) = $this->get_stone_by_xiangkou($xiangkou);
                $stone_arr[] = array($_carat_min,$_carat_max);
            }
        }
        if(!empty($stone_arr)){
            $result['return_msg'] =$stone_arr;
        }else{
            $result['error'] = 1;
        }
        return $result;
    }

    /**
     * 托最适配钻
     * @param unknown $cart     当前天生一对托镶口
     * @param unknown $carat    当前天生一对钻证书号=》镶口
     * @param unknown $style_sn 当前托款号
     */
    public function get_tuo_adaptive_by_dia($cart, $carat, $style_sn)
    {
        $rest = array('error'=>0,'error_msg'=>'','return_msg'=>"");
        
        $do_cart_id = "";
        if(!empty($cart) && !empty($carat) && !empty($style_sn)){

            //根据天生一对款获取可做镶口
            $result = $this->reason_style_by_xiangkou($style_sn);
            $can_xiangkou1 = $tsyd_stone1_arr = array();
            if($result['error'] == 0){
                $can_xiangkou1 = isset($result['return_msg'])?$result['return_msg']:array();
                if(!empty($can_xiangkou1)){
                    $result = $this->stone_scope_by_xiangkou($can_xiangkou1);
                    if($result['error'] == 0){
                        $tsyd_stone1_arr = isset($result['return_msg'])?$result['return_msg']:array();
                        list($_carat_min,$_carat_max) = $this->section_minmax_by_stone($tsyd_stone1_arr);
                        $tsyd_stone1_arr = array($_carat_min, $_carat_max);
                    }
                }
            }

            //根据天生一对款获取另一个款
            $result = $this->reason_couple_style_other($style_sn);
            $can_xiangkou2 = $tsyd_stone2_arr = array();
            if($result['error'] == 0){
                $style_other = isset($result['return_msg'])?$result['return_msg']:"";
                if(!empty($style_other)){
                    $result = $this->reason_style_by_xiangkou($style_sn);
                    $can_xiangkou2 = isset($result['return_msg'])?$result['return_msg']:array();
                    if(!empty($can_xiangkou2)){
                        $result = $this->stone_scope_by_xiangkou($can_xiangkou2);
                        if($result['error'] == 0){
                            $tsyd_stone2_arr = isset($result['return_msg'])?$result['return_msg']:array();
                            list($_carat_min,$_carat_max) = $this->section_minmax_by_stone($tsyd_stone2_arr);
                            $tsyd_stone2_arr = array($_carat_min, $_carat_max);
                        }
                    }
                }
            }
            /*计算另一个镶口值*/
            $other_func = function($cert, $arr) {
                $arr = array_flip($arr);
                unset($arr[$cert]);
                if(!empty($arr)){
                    $cert_other = array_keys($arr);
                    $cert = isset($cert_other[0])?$cert_other[0]:$cert;
                }
                return $cert;
            };
            $cartdata = array_values($carat);
            $cartlist = array_flip($carat);
                asort($cartdata);
            $count=count($cartdata);
            for ($i=0; $i <$count ; $i++) {
                $arr2[]=abs($cart-$cartdata[$i]);
            }

            $min= min($arr2);
            for ($i=0; $i <$count ; $i++) {
                if ($min==$arr2[$i]) {

                    $cartt = $cartdata[$i];
                    $other_cert = $other_func($cartt, $cartdata);
                    $do_cart_id = $cartlist[$cartt];

                    //if(bccomp($cartt, $cart, 3) != -1){
                    if(bccomp($cartt, (float) $tsyd_stone1_arr[0], 3) == -1 
                        && bccomp($cartt, (float) $tsyd_stone1_arr[1], 3) == 1){
                        $do_cart_id = $cartlist[$other_cert];
                    }
                    if(bccomp($cartt, (float) $tsyd_stone2_arr[0], 3) == -1 
                        && bccomp($cartt, (float) $tsyd_stone2_arr[1], 3) == 1){
                        $do_cart_id = $cartlist[$other_cert];
                    }
                }
            }
        }

        if(!empty($do_cart_id)){
            $rest['return_msg'] =$do_cart_id;
        }else{
            $rest['error'] = 1;
        }
        return $rest;
    }
    /**
     * 对戒虚拟货号列表
     * @see istyle::build_tsyd_goods_list()
     */
    public function build_tsyd_goods_list($result,$carat,$tsyd_carat){
        if(!empty($result['return_msg']['data'])){
            $xiangkou = getXiangkouByStone($carat);
            $tsyd_xiangkou = getXiangkouByStone($tsyd_carat);
            $style_sn_list = array();
            $data = $result['return_msg']['data'];
            foreach ($data as $key=>$ginfo){
                if(empty($ginfo['tsyd_style_sn'])){
                    $ginfo['tsyd_style_sn'] = DB::getOne("select if(style_sn1='{$ginfo['style_sn']}',style_sn2,style_sn1) from rel_style_lovers where style_sn1='{$ginfo['style_sn']}' or style_sn2='{$ginfo['style_sn']}'");
                }
                if($ginfo['tsyd_style_sn']){
                    //print_r($ginfo);
                    if(in_array($ginfo['xiangkou'],$xiangkou) && !in_array($ginfo['style_sn'],$style_sn_list)){
                        $xk_min = min($tsyd_xiangkou);
                        $xk_max = min($tsyd_xiangkou);
                        $tsydWhere = array('style_sn'=>$ginfo['tsyd_style_sn'],'xiangkou_min'=>$xk_min,'xiangkou_max'=>$xk_max);                    
                        $res = $this->get_style_goods_sn($tsydWhere);
                        if(empty($res['return_msg'])){
                            unset($data[$key]);
                        }
                        $style_sn_list[] = $ginfo['tsyd_style_sn'];
                    }else{
                        unset($data[$key]);
                    }
                }
            }
            //重置索引
            if(!empty($data)){
                $data = array_merge($data);
            }
            $result['return_msg']['data'] = $data;
        }
        //print_r($result);
        return $result;
    }

    /*普通单镶口取可镶石头*/
    public function get_stone_common_by_xiangkou($xiangkou)
    {
        $result = array('error'=>0,'error_msg'=>'','return_msg'=>"");
        $data = array();
        $result = $this->stone_scope_by_xiangkou(array($xiangkou));
        if($result['error'] == 0){
            $stone_arr = isset($result['return_msg'])?$result['return_msg']:[];
            if(!empty($stone_arr)){
                list($_carat_min,$_carat_max) = $this->section_minmax_by_stone($stone_arr);
                $data = array($_carat_min, $_carat_max);
            }
        }
        if(!empty($data)){
            $result['return_msg'] =$data;
        }else{
            $result['error'] = 1;
        }
        return $result;
    }
    /**
     * 查询款号列表
     * @param unknown $where
     */
    public function get_style_sn_list($where){
        $styleApiModel = new style_apiModel();
        return $styleApiModel->getStyleSnList($where);
    }
}

