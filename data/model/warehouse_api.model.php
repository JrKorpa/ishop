<?php
/**
 * warehouse API 
 *
 *
 *
 * *  (c) 2015-2018 . (http://www.kela.cn)
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 * @since      珂兰技术中心提供技术支持
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class warehouse_apiModel extends Model {
    
    /**
     * 现货列表
     * @param unknown $filter
     * @param string $field
     * @param number $pageSize
     */
    public function getWarehouseGoodsList($filter){
        $result = array('error'=>0,'error_msg'=>'','return_msg'=>array());
        //$_GET['debug'] = 1;
        if(!empty($filter['goods_id'])){
            if(is_array($filter['goods_id'])){
                $where["goods_items.goods_id"] = array('in',$filter['goods_id']);
            }else{
                $where["goods_items.goods_id"] = $filter['goods_id'];
            }
        }
        if(!empty($filter['company_id'])) {
            $where["goods_items.company_id"] = $filter['company_id'];
        }
        if(!empty($filter['goods_name'])) {
            $where["goods_items.goods_name"] = array('exp',"goods_items.goods_id='{$filter['goods_name']}' or goods_items.goods_sn='{$filter['goods_name']}' or goods_items.goods_name like '%{$filter['goods_name']}%' or goods_items.zhengshuhao='{$filter['goods_name']}'");
        }
        if(!empty($filter['goods_sn'])){
            if(is_array($filter['goods_sn'])){
                $where["goods_items.goods_sn"] = array('in',$filter['goods_sn']);
            }else{
                $where["goods_items.goods_sn"] = $filter['goods_sn'];
            }
        }
        if(!empty($filter['goods_sn_not'])){
            if(is_array($filter['goods_sn_not'])){
                $where["goods_items.goods_sn"] = array('not in',$filter['goods_sn_not']);
            }else{
                $where["goods_items.goods_sn"] = array('ueq',$filter['goods_sn_not']);
            }
        }
        if(!empty($filter['tuo_type'])){
            if(is_array($filter['tuo_type'])){
                $where["goods_items.tuo_type"] = array('in',$filter['tuo_type']);
            }else{
                $where["goods_items.tuo_type"] = $filter['tuo_type'];
            }
        }        
        if(!empty($filter['is_on_sale'])){
            if(is_array($filter['is_on_sale'])){
                $where["goods_items.is_on_sale"] = array('in',$filter['is_on_sale']);
            }else{
                $where["goods_items.is_on_sale"] = $filter['is_on_sale'];
            }
            //查询库存商品条件
            if($filter['is_on_sale']==2){
                $where["goods_items.order_detail_id"] = 0;
                $where["warehouse_goods_ishop_price.sale_price"] = array('gt',0);
                $where["erp_warehouse.is_enabled"] = 1;
                $where["erp_warehouse.is_default"] = 1;
            }
        }
        if(isset($filter['carat_min']) && $filter['carat_min']!==""){
            $where["zuanshidaxiao_min"] = array('exp',"goods_items.zuanshidaxiao >={$filter['carat_min']}");
        } 
        if(isset($filter['carat_max']) && $filter['carat_max']!==""){
            if($filter['carat_max']==0){
                $where["zuanshidaxiao_min"] = array('exp',"goods_items.zuanshidaxiao=0 or goods_items.zuanshidaxiao is null");
            }else{
                $where["zuanshidaxiao_max"] = array('exp',"goods_items.zuanshidaxiao <={$filter['carat_max']}");
            }            
        } 
        //$_GET['debug'] = 1;
        if(isset($filter['price_min']) && $filter['price_min']!==""){
            //$filter['price_min'] = round($filter['price_min']/1.8);
            $where["price_min"] = array('exp',"warehouse_goods_ishop_price.sale_price >={$filter['price_min']}");
        }
        if(isset($filter['price_max']) && $filter['price_max']!==""){
            //$filter['price_max'] = round($filter['price_max']/1.8);
            $where["price_max"] = array('exp',"warehouse_goods_ishop_price.sale_price <={$filter['price_max']}");
        }              
        $goodsAttrModel = new goods_attributeModel();
        if(!empty($filter['caizhi'])){            
            $caizhiArr = $goodsAttrModel->getCaizhiList();
            if(is_array($filter['caizhi'])){
                foreach ($filter['caizhi'] as $k=>$v){
                    if(is_numeric($v)){
                        $filter['caizhi'][$k] = $caizhiArr[$v];
                    }
                }
                $where["goods_items.caizhi"] = array('in',$filter['caizhi']);
            }else{
                if(is_numeric($filter['caizhi'])){
                    $filter['caizhi'] = $caizhiArr[$filter['caizhi']];
                }
                $where["goods_items.caizhi"] = $filter['caizhi'];
            }
        } 
        if(!empty($filter['product_type'])) {
            if(empty($productTypeArr)){
                $productTypeArr = $goodsAttrModel->getProductTypeList();
            }
            if(is_array($filter['product_type'])){
                foreach ($filter['product_type'] as $k=>$v){
                    if(is_numeric($v)){
                        $filter['product_type'][$k] = $productTypeArr[$v];
                    }
                }
                $where["goods_items.product_type"] = array('in',$filter['product_type']);
            }else{
                if(is_numeric($filter['product_type'])){
                    $filter['product_type'] = $productTypeArr[$filter['product_type']];
                }
                $where["goods_items.product_type"] = $filter['product_type'];
            }    
        }
        if(!empty($filter['product_type_not'])) {
            if(is_array($filter['product_type_not'])){
                $where["goods_items.product_type"] = array('not in',$filter['product_type_not']);
            }else{
                $where["goods_items.product_type"] = array('ueq',$filter['product_type_not']);
            }
        }
        //print_r($where);
        if(!empty($filter['cat_type'])) {
            if(empty($catTypeArr)){
               $catTypeArr = $goodsAttrModel->getCatTypeList();
            }
            if(is_array($filter['cat_type'])){
                foreach ($filter['cat_type'] as $k=>$v){
                    if(is_numeric($v)){
                        $filter['cat_type'][$k] = $catTypeArr[$v];
                    }
                }
                $where["goods_items.cat_type"] = array('in',$filter['caizhi']);
            }else{
                if(is_numeric($filter['cat_type'])){
                    $filter['cat_type'] = $catTypeArr[$filter['cat_type']];
                }
                $where["goods_items.cat_type"] = $filter['cat_type'];
            }            
        }
        if(!empty($filter['cat_type_not'])) {
            if(is_array($filter['cat_type_not'])){
                $where["goods_items.cat_type"] = array('not in',$filter['cat_type_not']);
            }else{
                $where["goods_items.cat_type"] = array('ueq',$filter['cat_type_not']);
            }
        }
        //镶口
        if(!empty($filter['xiangkou'])) {
            if(is_array($filter['xiangkou'])){
                $where["goods_items.jietuoxiangkou"] = array('in',$filter['xiangkou']);
            }else{
                $where["goods_items.jietuoxiangkou"] = $filter['xiangkou'];
            }
        }  
        if(isset($filter['xiangkou_min']) && $filter['xiangkou_min'] !== '') {
            $where["xiangkou_min"] = array('exp',"goods_items.jietuoxiangkou>={$filter['xiangkou_min']}");
        }
        if(isset($filter['xiangkou_max']) && $filter['xiangkou_max'] !== '') {
            $where["xiangkou_max"] = array('exp',"goods_items.jietuoxiangkou<={$filter['xiangkou_max']}");
        }      
        //手寸
        if(!empty($filter['shoucun'])) {
            if(is_array($filter['shoucun'])){
                $where["goods_items.shoucun"] = array('in',$filter['shoucun']);
            }else{
                $where["goods_items.shoucun"] = $filter['shoucun'];
            }
        }  
        if(!empty($filter['shoucun_min'])) {
            $where["shoucun_min"] = array('exp',"goods_items.shoucun>={$filter['shoucun_min']}");
        }
        if(!empty($filter['shoucun_max'])) {
            $where["shoucun_max"] = array('exp',"goods_items.shoucun<={$filter['shoucun_max']}");
        }   
        if(isset($filter['is_recommend']) && !empty($filter['is_recommend'])) {
            $where["base_style_info.is_recommend"] = $filter['is_recommend'];
        }      
        //系列
        if(!empty($filter['xilie'])){
            $where["xilie"] = array('exp',"goods_items.goods_sn in(select style_sn from base_style_info where check_status=3 and xilie like '%,{$filter['xilie']},%')");
        }
        if(!empty($filter['channel_id'])){
            $where["channel_id"] = $filter['channel_id'];
        }else{
            $result['error'] = 1;
            $result['error_msg'] = "门店ID不能为空";
            return $result;
        }
        $orderby_list = array(
            '1|1'=>"",
            '1|2'=>"",
            '2|1'=>"base_style_info.goods_click asc",
            '2|2'=>"base_style_info.goods_click desc",
            '3|1'=>"goods_items.goods_id asc",
            '3|2'=>"goods_items.goods_id desc",
            '4|1'=>"base_style_info.goods_salenum asc",
            '4|2'=>"base_style_info.goods_salenum desc",
            '5|1'=>"goods_items.jijiachengben asc",
            '5|2'=>"goods_items.jijiachengben desc",
        );
        $order_by = "";
        if(!empty($filter['order_by']) && !empty($orderby_list[$filter['order_by']])){
            $order_by = $orderby_list[$filter['order_by']];
        }
        $page = !empty($filter['page'])?$filter['page']:1;
        $pageSize = !empty($filter['page_size'])?$filter['page_size']:12;
        $field = !empty($filter['field'])?$filter['field']:"goods_items.*,warehouse_goods_ishop_price.sale_price";
        $goods_list = $this->table("goods_items,warehouse_goods_ishop_price,erp_warehouse,base_style_info")->join("inner,inner,left")->on("goods_items.goods_id=warehouse_goods_ishop_price.goods_id,goods_items.warehouse_id=erp_warehouse.house_id,goods_items.goods_sn=base_style_info.style_sn")->field($field)->where($where)->page($pageSize)->order($order_by)->select();
        $this->fill_image_and_price($goods_list,$filter);

        $data = array();
        $data['page'] = $page;
        $data['pageSize'] = $pageSize;
        $data['recordCount'] = $this->gettotalnum();
        $data['pageCount'] = $this->gettotalpage();
        $data['data'] = $goods_list;
    
        $result['error'] = 0;
        $result['return_msg'] = $data;
        return $result;
    }

    public function fill_image_and_price(&$goods_list,$filter,$calc_price = false){
        if(!empty($filter['extends'])){
            //$policyGoodsModel = new app_salepolicy_goodsModel();
            //$diamond_api = data_gateway('idiamond');
            if(!is_array($goods_list)) return;
            foreach ($goods_list as $key=>$goods_info){
                $style_sn = trim($goods_info['goods_sn']);
                if(in_array("goods_image",$filter['extends'])){
                    $styleApi = new style_apiModel();
                    $res = $styleApi->getStyleGalleryInfo(array('style_sn'=>$style_sn,'image_place'=>array(1,2,3,4)));
                    if(!empty($res['return_msg'])){
                        $goods_info['goods_image'] = $res['return_msg']['middle_img'];
                    }
                }
                //商品价格计算
                if(in_array("goods_price",$filter['extends'])){
                    if(empty($filter['channel_id'])){
                        $result['error'] = 1;
                        $result['error_msg'] = "参数错误，channel_id不能为空";
                        return $result;
                    }else{
                        $channel_id = $filter['channel_id'];
                    }
                    if(empty($filter['company_id'])){
                        $result['error'] = 1;
                        $result['error_msg'] = "参数错误，company_id不能为空";
                        return $result;
                    }else{
                        $company_id = $filter['company_id'];
                    }
                    //如果是总部商品，取出批发价 作为 门店的成本价
                    if($goods_info['company_id'] == 58){
                        $goods_info['jijiachengben'] = $goods_info['pifajia'];
                        if($goods_info['pifajia']<=0){
                            //print_r($goods_info);
                        }
                    } 
                    if($calc_price===true){
                       $policyGoodsModel = new app_salepolicy_goodsModel();
                       $goods_info['goods_price'] = $policyGoodsModel->getXianhuoPrice($goods_info, $channel_id, $company_id);
                    } else{
                       $goods_info['goods_price'] = $goods_info['sale_price'];                        
                    }               
                }
                $goods_list[$key] = $goods_info;
            }
        }
    }
    /**
     * 现货计算价格
     * @param unknown $goods_info 
     * 重要字段 product_type cat_type zuanshidaxiao zhengshuleibie jijiachengben 必填
     * @param unknown $store_id 
     * @param unknown $company_id
     * @return Ambigous <number, string, mixed>
     */
    /*public function getGoodsPrice($goods_info,$store_id,$company_id){
        $policyGoodsModel = new app_salepolicy_goodsModel();
        $diamond_api = data_gateway('idiamond');
        if($goods_info['product_type'] == '钻石' && $goods_info['cat_type'] =='裸石'){
            //裸石价格计算
            $diaInfo = array(
                'carat'=>$goods_info['zuanshidaxiao'],
                'goods_type'=>1,
                'cert'=>$goods_info['zhengshuleibie'],
                'pifajia'=>$goods_info['jijiachengben'],
            );
            $diamond_api->multiply_jiajialv($diaInfo, $store_id, $company_id);
            $goods_price = sprintf("%.2f",$diaInfo['shop_price']);
        }else{
            $goods_info['is_xianhuo'] = 1;
            $sdata = $policyGoodsModel->getPolicyGoods([$goods_info],$store_id);
            if(empty($sdata[0]['sprice'])){
                $goods_price = 0;
            }else{
                $goods_price_list = array();
                foreach ($sdata[0]['sprice'] as $vo){
                    $goods_price_list[$vo['id']] = $vo;
                }
                krsort($goods_price_list);
                $goods_price_info = current($goods_price_list);
                $goods_price = $goods_price_info['sale_price'];
            }
        }
        return $goods_price;
    }*/
    /**
     * 查询现货详情（替换boss api）
     * @param unknown $condition
     */
    public function getWarehouseGoodsInfo($filter){
        $result = array('error'=>0,'error_msg'=>'','return_msg'=>[]);
        $where = array();
        if (empty($filter['goods_id'])) {
            $result['error'] = 1;
            $result['error_msg'] = "参数错误，goods_id不能为空";
            return $result;
        }else{
            $where['goods_id'] = $filter['goods_id'];
        }
        if (!empty($filter['company_id'])) {
            $where['company_id'] = $filter['company_id'];
        }
        $field = !empty($filter['field'])?$filter['field']:"*";
        $goodsInfo = $this->table('goods_items')->where($where)->field($field)->find();
        if (empty($goodsInfo)) {
            $result['error'] = 1;
            $result['error_msg'] = "未查询到此商品";
            return $result;
        }else if($goodsInfo['is_on_sale'] != 2){
            $result['error'] = 1;
            $result['error_msg'] = '货号目前不是库存状态';
            return $result;
        }else if($goodsInfo['order_detail_id'] >0){
            $result['error'] = 1;
            $result['error_msg'] = '货号已绑定订单';
            return $result;
        }elseif($goodsInfo['product_type'] == '彩钻' && $goodsInfo['cat_type'] =='裸石'){        
            $result['error'] = 1;
            $result['error_msg'] = '货品的产品线是彩钻, 不走销售政策, 请选择彩钻下单, 或找产品部核对商品信息';
            return $result;
        }elseif($goodsInfo['product_type'] == '钻石' && $goodsInfo['cat_type'] =='裸石'){
            //$result['error'] = 0;
            //$result['return_msg'] = $goodsInfo;
            //return $result;
        }elseif($goodsInfo['product_type'] == '钻石' && $goodsInfo['cat_type'] =='彩钻'){
            $result['error'] = 1;
            $result['error_msg'] = '货品的产品线是钻石, 款式分类为彩钻, 这类货品不走销售政策, 请找产品部核对该货品信息';
            return $result;
        }elseif($goodsInfo['product_type'] == '彩钻' && $goodsInfo['cat_type'] =='钻石'){
            $result['error'] = 1;
            $result['error_msg'] = '货品的产品线是彩钻, 款式分类为钻石, 这类货品不走销售政策, 请找产品部核对该货品信息';
            return $result;
        }else if($goodsInfo['put_in_type'] != 5 && $goodsInfo['jijiachengben'] <= 0){
            $result['error'] = 1;
            $result['error_msg'] = '非自采的商品, 成本价为0, 请找产品部核对该货品信息';
            return $result;
        }
        $houseInfo = DB::getRow2("select * from erp_warehouse where house_id={$goodsInfo['warehouse_id']}");
        if($houseInfo['is_default'] != 1){
            $result['error'] = 1;
            $result['error_msg'] = '货品所在的仓库不是默认上架仓库';
            return $result;
        }
        $goods_list=[$goodsInfo];
        $this->fill_image_and_price($goods_list,$filter,true);
        $result['error'] = 0;
        $result['return_msg'] = $goods_list[0];
        return $result;
    }

}