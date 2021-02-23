<?php
/**
 * style API 
 *
 *
 *
 * *  (c) 2015-2018 . (http://www.kela.cn)
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 * @since      珂兰技术中心提供技术支持
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class style_apiModel extends Model {
    
    /**
     * 虚拟商品 单行 查询
     */
    public function getStyleGoodsInfo($filter){
        
        $result = array('error'=>0,'error_msg'=>'','return_msg'=>array());
        
        if(empty($filter['goods_sn'])){
            $result['error'] = 1;
            $result['error_msg'] = "参数错误，goods_sn不能为空";
            return $result;
        }else{
            $goods_sn = $filter['goods_sn'];
        }
        /*
        $sql = "select sg.*,si.goods_content,IF((pt.parent_id=3 and sg.xiangkou=0) or pt.parent_id<>3,1,2) as tuo_type 
from list_style_goods sg inner join front.app_product_type pt on sg.product_type_id = pt.product_type_id 
INNER JOIN base_style_info si on si.style_sn= sg.style_sn 
	        where sg.goods_sn='{$filter['goods_sn']}'";*/
        $sql = "select g.*,s.style_sex from list_style_goods g inner join base_style_info s on g.style_sn=s.style_sn where g.goods_sn='{$goods_sn}'";
        if(!empty($filter['is_ok'])){
            $sql .=" AND si.is_ok={$filter['is_ok']}";
        }
        $goodsInfo = DB::getRow2($sql);
        if(empty($goodsInfo)){
            $result['error'] = 1;
            $result['error_msg'] = "商品不存在";
            $result['return_sql'] = $sql;
            return $result;
        }
        $attrModel = new goods_attributeModel(11);
        
        $caizhiArr = $attrModel->getCaizhiList();
        $goodsInfo['caizhi_name'] = isset($caizhiArr[$goodsInfo['caizhi']])?$caizhiArr[$goodsInfo['caizhi']]:$goodsInfo['caizhi'];
        
        //$dd = new DictView(new DictModel(1));
        //$goodsInfo['yanse_name'] = $dd->getEnum("style.color",$goodsInfo['yanse']);
        $jinseArr = $attrModel->getJinseList();
        $goodsInfo['yanse_name'] = isset($jinseArr[$goodsInfo['yanse']])?$jinseArr[$goodsInfo['yanse']]:$goodsInfo['yanse'];

        
        if(!empty($filter['extends'])){
            $style_sn = $goodsInfo['style_sn'];
            $style_id = $goodsInfo['style_id'];
            if(in_array("goods_image",$filter['extends'])){
                $res = $this->getStyleGalleryInfo(array('style_sn'=>$style_sn));
                if(!empty($res['return_msg'])){
                    $goodsInfo['goods_image'] = $res['return_msg']['middle_img'];
                }else{
                    $goodsInfo['goods_image'] = '';
                }
                
            }    
            if(in_array("goods_shape",$filter['extends'])){
                
                $shapeArr = $attrModel->getShapeList();
                 
                $shape_sql = "select distinct shape from rel_style_stone where style_id='{$style_id}' and stone_position=1 and shape<>0";
                $shape_list = DB::getAll($shape_sql);
                foreach ($shape_list as $vo){
                    $shape[] = $vo['shape'];
                    $shape_name[] = isset($shapeArr[$vo['shape']])?$shapeArr[$vo['shape']]:$vo['shape'];
                }
                $goodsInfo['shape'] = !empty($shape)?implode("|", $shape):'';
                $goodsInfo['shape_name'] = !empty($shape_name)?implode("|", $shape_name):'';
            }
            //商品价格计算
            if(in_array("goods_price",$filter['extends'])){               
                if(empty($filter['channel_id'])){
                    $result['error'] = 1;
                    $result['error_msg'] = "参数错误，channel_id不能为空";
                    return $result;
                }
                $policyGoodsModel = new app_salepolicy_goodsModel();
                $goodsInfo['goods_price'] = $policyGoodsModel->getQihuoPrice($goodsInfo,$filter['channel_id']);
             }
         }
    
        $result['error'] = 0;
        $result['return_msg'] = $goodsInfo;
        return $result;
    
    }
    /**
     * 根据【虚拟货号】 或 【虚拟货号5个属性】查询商品价格
     * @param unknown $filter
     * @return multitype:number string multitype: |multitype:number string multitype: multitype:unknown Ambigous <>
     */
    public function getStyleGoodsPrice($filter){   
        
        $result = array('error'=>0,'error_msg'=>'','return_msg'=>array());
        
        if(empty($filter['channel_id'])){
            $result['error'] = 1;
            $result['error_msg'] = "参数错误，channel_id参数不能为空";
            return $result;
        }
        if(empty($filter['goods_sn'])){
            if(empty($filter['style_sn'])){
                $result['error'] = 1;
                $result['error_msg'] = "参数错误，style_sn参数不能为空";
                return $result;
            }else if(!isset($filter['shoucun'])){
                $result['error'] = 1;
                $result['error_msg'] = "参数错误，shoucun参数不能为空";
                return $result;
            }else if(!isset($filter['xiangkou'])){
                $result['error'] = 1;
                $result['error_msg'] = "参数错误，xiangkou参数不能为空";
                return $result;
            }else if(empty($filter['caizhi'])){
                $result['error'] = 1;
                $result['error_msg'] = "参数错误，caizhi参数不能为空";
                return $result;
            }else if(empty($filter['yanse'])){
                $result['error'] = 1;
                $result['error_msg'] = "参数错误，yanse参数不能为空";
                return $result;
            }
            $style_sn = $filter['style_sn'];
            $shoucun  = floatval($filter['shoucun']);
            $xiangkou = floatval($filter['xiangkou']);
            $caizhi   = $filter['caizhi'];
            $yanse    = $filter['yanse'];
            $sql = "select * from list_style_goods where style_sn='{$style_sn}' and shoucun={$shoucun} and xiangkou={$xiangkou} and caizhi={$caizhi} and yanse={$yanse} and is_ok=1";
            $goodsInfo = DB::getRow2($sql);
        }else{
            $goods_sn = $filter['goods_sn'];
            $sql = "select * from list_style_goods where goods_sn='{$goods_sn}' and is_ok=1";
            $goodsInfo = DB::getRow2($sql);
        }
        //print_r($goodsInfo);
        if(empty($goodsInfo)){
            $result['error'] = 1;
            $result['error_msg'] = "不支持定制：未匹配到虚拟商品";
            return $result;
        }
        $policyGoodsModel = new app_salepolicy_goodsModel();
        $goods_price = $policyGoodsModel->getQihuoPrice($goodsInfo,$filter['channel_id']);
        if($goods_price==0){
            $result['error'] = 1;
            $result['error_msg'] = "商品未设置销售政策";
            return $result;
        }
        $data = array(
            'goods_sn'=>$goodsInfo['goods_sn'],
            'style_sn'=>$goodsInfo['style_sn'],
            'xiangkou'=>$goodsInfo['xiangkou'],
            'shoucun'=>$goodsInfo['shoucun'],
            'caizhi'=>$goodsInfo['caizhi'],
            'yanse'=>$goodsInfo['yanse'],
            'goods_price'=>$goods_price,
       );
       $result['error'] = 0;
       $result['return_msg'] = $data;
       return $result;
    }
    /**
     * 根据款号 获取款号图库列表
     * @param unknown $filter
     * @return multitype:number string multitype: unknown
     */
    public function getStyleGalleryList($filter) {
        
        $result = array('error'=>0,'error_msg'=>'','return_msg'=>array());
        $style_cat = 0;
        $sql = "SELECT * FROM `app_style_gallery` WHERE 1 ";
        if(empty($filter['image_place'])){
            $filter['image_place'] = array(1,2,3,4);
        }
		if (!empty($filter['style_id'])) {
			$sql .= " AND style_id = {$filter['style_id']}";
		}
		if (!empty($filter['style_sn'])) {
			$sql .= " AND style_sn = '{$filter['style_sn']}'";
		}		
		if(!empty($filter['style_sn'])){
		    $sql2 = "select count(*) from rel_style_lovers where style_sn1='{$filter['style_sn']}' or style_sn2='{$filter['style_sn']}'";		    
		    $is_tsyd = DB::getOne($sql2);
		    if($is_tsyd){
		        $filter['image_place'][] = 100;
		    }
		}		
		if (!empty($filter['image_place'])) {
		    if(is_array($filter['image_place'])){
		        $sql .= " AND image_place in(".implode(",",$filter['image_place']).")";
		    }else{
		        $sql .= " AND image_place = {$filter['image_place']}";
		    }		    
		}	
		$sql .=" order by if(image_place=100,0,image_place+1) asc";
		if(!empty($filter['limit'])){
		    $sql .= " limit {$filter['limit']}";
		}
        $data = DB::getAll($sql);
        $result['error'] = 0; 
        $result['return_msg'] = $data;
        return $result;
	}
	/**
	 * 根据款号 获取图片
	 * @param unknown $filter
	 * @return multitype:number string multitype: unknown
	 */
	public function getStyleGalleryInfo($filter) {	
	    $filter['limit'] = 1;
	    $result = $this->getStyleGalleryList($filter);
	    if(!empty($result['return_msg'])){
	        $result['return_msg'] = $result['return_msg'][0];
        }   

	    return $result;
	}
	/**
	 * 根据款式商品   聚合可用 【材质】，【材质颜色】，【指圈】，【镶口】属性列表
	 */
	public function getStyleGoodsAttr($filter){	    
	    $result = array('error'=>0,'error_msg'=>'','return_msg'=>array());
	    if(empty($filter['style_sn'])){
	        $result['error'] = 1;
	        $result['error_msg'] = "参数错误，style_sn不能为空";
	        return $result;
	    }
	
	    $sql = "select caizhi,yanse,shoucun,xiangkou from list_style_goods where style_sn='{$filter['style_sn']}' and is_ok=1";
	    if(isset($filter['xiangkou']) && $filter['xiangkou']!=''){
	        $sql .= " AND xiangkou=".floatval($filter['xiangkou']);
	    }
	    $data = DB::getAll($sql);
	    if(empty($data)){
	        $result['error'] = 1;
	        $result['error_msg'] = "商品不支持定制";
	        return $result;
	    }

        $attModel = new goods_attributeModel();
        $caizhiArr = $attModel->getCaizhiList();
        $yanseArr = $attModel->getJinseList();
        $datalist = array();
        foreach ($data as $key=>$vo){
            $caizhi = !empty($caizhiArr[$vo['caizhi']])?$caizhiArr[$vo['caizhi']]:$vo['caizhi'];
            $yanse  = !empty($yanseArr[$vo['yanse']])?$yanseArr[$vo['yanse']]:$vo['yanse'];
            $vo['jinse_key']   = $vo['caizhi'].'|'.$vo['yanse'];
            $vo['jinse_name']  = $caizhi.$yanse;
            $data[$key] = $vo;
        }
        $jinseArr = array_unique(array_column($data,'jinse_name','jinse_key'));
        $shoucunArr = array_unique(array_column($data,'shoucun'));
        $xiangkouArr = array_unique(array_column($data,'xiangkou'));
        sort($shoucunArr);
        sort($xiangkouArr);
        $data = array(
            'jinse'=>$jinseArr,
            'shoucun'=>$shoucunArr,
            'xiangkou'=>$xiangkouArr,
        );
        $result['error'] = 0;
        $result['return_msg'] = $data;        
        return $result;
	}
	
    /**
     *  获取赠品列表
     * @param unknown $filter
     * @return multitype:number string multitype:
     */
	public function getGiftGoodsList($filter)
	{
	    $result = array('error'=>0,'error_msg'=>'','return_msg'=>array());

	    $where = " WHERE 1 ";
	    if(isset($filter['sell_type']) && $filter['sell_type'] != ''){
	        $where .= " and `sell_type` = {$filter['sell_type']}";
	    }
	    if(!empty($filter['sale_way'])){
	        if(is_array($filter['sale_way'])){
	            $where .= " and `sale_way` in ('".implode("','",$filter['sale_way'])."')";
	        }else{
	            $where .= " and `sale_way` = '{$filter['sale_way']}'";
	        }
	    }
	     
	    if(isset($filter['status']) && $filter['status'] != ''){
	        $where .= " and `status` = {$filter['status']}";
	    }
	
	    if(!empty($filter['goods_number'])){
	        if(is_array($filter['goods_number'])){
	            $where .= " and `goods_number` in ('".implode("','",$filter['goods_number'])."')";
	        }else{
	            $style_sn = $filter['goods_number'];
	            $where .= " and `goods_number` = '{$filter['goods_number']}'";
	        }
	    }
	    if(!empty($filter['gift_name'])){
	        $where .= " and `name` like '%{$filter['gift_name']}%'";
	    }
	    $sql = "SELECT `name` as goods_name,`goods_number` as goods_id,`sell_sprice` as goods_price,is_randring,status,add_time,update_time,is_xz,sale_way FROM gift_goods {$where}  order by name desc;";
	
	    //file_put_contents("gift.txt",$sql);
	
	    $data = DB::getAll($sql);
	    if(!empty($filter['extends'])){
	        if(in_array('goods_image',$filter['extends'])){
	            foreach ($data as $key=>$vo){
	                $style_sn = $vo['goods_id'];
    	            $image_sql = "select middle_img from app_style_gallery where style_sn='{$style_sn}'";
    	            $goods_image = DB::getOne($image_sql);
    	            $data[$key]['goods_image'] = $goods_image.'';
	            }
	        }
	    }
	    //返回信息
	    if(!empty($data)){
	        $result['error']  = 0;
	        $result['return_msg']  = $data;
	        return $result;
	    }else{
	        $result['error']  = 1;
	        $result['error_msg']  = "未查询到数据";
	        return $result;
	    }
	}
	
	/**
	 * 根据 款号 材质，材质颜色，指圈，镶口 获取有效虚拟货号
	 */
	public function getStyleGoodsSn($filter){
	    $result = array('error'=>0,'error_msg'=>'','return_msg'=>array());
	    
	    $where = 'where is_ok=1';
	    if(empty($filter['style_sn'])){
	        $result['error'] = 1;
	        $result['error_msg'] = "参数错误，style_sn参数不能为空";
	        return $result;
	    }else{
	        $where .=" AND style_sn='{$filter['style_sn']}'";
	    }
	    if(isset($filter['shoucun']) && is_numeric($filter['shoucun'])){
	        $where .=" AND shoucun={$filter['shoucun']}";
	    }
	    if(isset($filter['xiangkou']) && is_numeric($filter['xiangkou'])){
	        $where .=" AND xiangkou={$filter['xiangkou']}";
	    }
	    if(isset($filter['caizhi']) && is_numeric($filter['caizhi'])){
	        $where .=" AND caizhi={$filter['caizhi']}";
	    }
	    if(isset($filter['yanse']) && is_numeric($filter['yanse'])){
	        $where .=" AND yanse={$filter['yanse']}";
	    }
	    if(isset($filter['xiangkou_min'])){	        
	        $where .=" AND xiangkou>={$filter['xiangkou_min']}";
	    }
	    if(isset($filter['xiangkou_max'])){
	        $where .=" AND xiangkou <={$filter['xiangkou_max']}";
	    }	    
	
	    $sql = "select goods_sn from list_style_goods {$where}";
	    $goods_sn = DB::getOne($sql);
	    if(empty($goods_sn)){
	        $result['error'] = 1;
	        $result['error_msg'] = "未查询到数据";
	    }else{
    	    $result['error'] = 0;
    	    $result['return_msg'] = $goods_sn;
	    }
	    //$result['return_sql'] = $sql;
	    return $result;
	}
	/**
	 * 款号属性配置信息
	 * @param unknown $filter
	 * @return multitype:number string multitype: |multitype:number string multitype: Ambigous <multitype:, unknown>
	 */
	public function getStyleAttribute($filter){
	    
	    $result = array('error'=>0,'error_msg'=>'','return_msg'=>array());
	    
	    if(empty($filter['style_sn'])){
	        $result['error'] = 1;
	        $result['error_msg'] = "款号不能为空";
	        return $result;
	    }
	    $style_sn = trim($filter['style_sn']);
        //判断此款是否已经审核
        $sql = "select a.`check_status`,a.`product_type` as `product_type_id`,a.`style_type` as `cat_type_id` from `base_style_info` as a where a.`style_sn` = '".$style_sn."'";
        $styleInfo = DB::getRow2($sql);
        if(empty($styleInfo)){
            $result['error'] = 1;
	        $result['error_msg'] = "款号{$style_sn}不存在";
	        return $result;
        }else if($styleInfo['check_status']!=3){
            $result['error'] = 1;
            $result['error_msg'] = "款号{$style_sn}不是审核状态";
            return $result;
	    }	
        //获取此款的产品线和分类，来确定一下属性的是否必填
        $product_type_id = $styleInfo['product_type_id'];
        $cat_type_id = $styleInfo['cat_type_id'];
	
	    $sql = " SELECT `is_require`,`attribute_id`,`attr_type` FROM `rel_cat_attribute` WHERE `product_type_id`=$product_type_id and `cat_type_id`=$cat_type_id ";
	    $cat_attribute_arr = DB::getAll($sql);
        if(empty($cat_attribute_arr)){
            $result['error'] = 1;
            $result['error_msg'] = "款号{$style_sn}对应的产品线或款式分类没有设置属性！";
            return $result;
        }
        //获取属性的是否必填和销售属性
        $new_cat_attribute = array();
        $new_cat_attribute_attr_type = array();
        foreach ($cat_attribute_arr as $val){
            $is_require = $val['is_require'];
            $attr_id = $val['attribute_id'];
            $attr_type = $val['attr_type'];
            $new_cat_attribute[$attr_id] = $is_require;
            $new_cat_attribute_attr_type[$attr_id] = $attr_type;
        }
	    
        //此款的所有属性和属性值
        $sql = "SELECT a.product_type_id,a.cat_type_id,a.`attribute_id`,a.`attribute_value`,b.`attribute_code`,b.`show_type`,b.`attribute_name`,c.`att_value_id`,c.`att_value_name` FROM `rel_style_attribute` as a inner join `app_attribute` as b on a.`attribute_id` = b.`attribute_id` left join `app_attribute_value` as c on b.`attribute_id` = c.`attribute_id` where a.style_sn='{$style_sn}'";
        $styleAttrData = DB::getAll($sql);
        if(empty($styleAttrData)){
            $result['error'] = 1;
            $result['error_msg'] = "款号{$style_sn}未设置属性！";
            return $result;
        }        
	    //过滤一下
        $style_attr_arr = array();
        foreach ($styleAttrData as $val){
            $a_id = $val['attribute_id'] ;
            $show_type = $val['show_type'];
            $style_value = $val['attribute_value'];
            if($show_type == 3){//多选
                if(empty($style_value)) {
                    continue;
                }
                $tmp_value = rtrim($style_value,",");
                $style_attr_arr[$a_id] = explode(",", $tmp_value);
            }else{
                $style_attr_arr[$a_id] =$style_value;
            }
        }
	 
	
	    $sql = " SELECT * FROM `rel_cat_attribute` WHERE `product_type_id`={$product_type_id} and `cat_type_id`={$cat_type_id}";
        $cat_attribute_arr = DB::getAll($sql);
        $new_cat_attribute = array();
        $new_cat_attribute_attr_type = array();
        foreach ($cat_attribute_arr as $val){
            $is_require = $val['is_require'];
            $attr_id = $val['attribute_id'];
            $attr_type = $val['attr_type'];
            $new_cat_attribute[$attr_id] = $is_require;
            $new_cat_attribute_attr_type[$attr_id] = $attr_type;
        }
        
        //获取指圈的属性id,因为指圈需要由6-8切分6，7，8
        $sql = "SELECT `attribute_id` FROM `app_attribute` WHERE `attribute_code` ='zhiquan'";
        $zhiquan_attr_id = (int) DB::getOne($sql);
        
        $new_attribute_data = array();
        foreach ($styleAttrData as $val) {
            //匹配一下属性值存在
            $value_id = $val['att_value_id'];
            $attribute_id = $val['attribute_id'];
            if(isset($style_attr_arr[$attribute_id]) && is_array($style_attr_arr[$attribute_id])){
                if(!in_array($value_id, $style_attr_arr[$attribute_id])){
                    continue;
                }
            }	            

            $attribute_value = $val['attribute_value'];
            $show_type = $val['show_type'];

            $att_value_name = $val['att_value_name'];
            $new_attribute_data[$attribute_id]['attribute_id'] = $val['attribute_id'];
            $new_attribute_data[$attribute_id]['attribute_name'] = $val['attribute_name'];
            $new_attribute_data[$attribute_id]['attribute_code'] = $val['attribute_code'];
            $new_attribute_data[$attribute_id]['show_type'] = $val['show_type'];
            $new_attribute_data[$attribute_id]['is_require'] = $new_cat_attribute[$attribute_id];
            $new_attribute_data[$attribute_id]['attr_type'] = $new_cat_attribute_attr_type[$attribute_id];

            switch ($show_type){
                case 1:
                    //文本框
                    $new_attribute_data[$attribute_id]['value'] = $attribute_value;
                    break;
                case 2:
                    //2单选
                    if($attribute_value == $value_id){
                        $new_attribute_data[$attribute_id]['value'] = $att_value_name;
                    }
                    break;
                case 3:
                    //3多选
                    if(!isset($new_attribute_data[$attribute_id]['value'])){
                        $new_attribute_data[$attribute_id]['value'] = '';

                    }
                    //指圈需要切割6-8变成6,7,8
                    if($attribute_id == $zhiquan_attr_id){
                        $zhiquan_arr = $this->cutFingerInfo(array($att_value_name));
                        $zhiquan_str = implode(",", $zhiquan_arr[0]);
                        $new_attribute_data[$attribute_id]['value'] .= $zhiquan_str.',';
                        $new_attribute_data[$attribute_id]['valstr'] .= $att_value_name.',';
                    }else{
                        $new_attribute_data[$attribute_id]['value'] .= $att_value_name.',';
                    }
                    break;
                case 4:
                    //4下拉列表
                    if($attribute_value == $value_id){
                        $new_attribute_data[$attribute_id]['value'] = $att_value_name;
                    }

                    break;
            }
        }
        
        $result['error'] = 0;
        $result['return_msg'] = $new_attribute_data;
	    return $result;
	    
	}
	/**
	 * 指圈拆分
	 * @param unknown $data
	 * @return multitype:number unknown
	 */
	private function cutFingerInfo($data){
	    foreach ($data as $key=>$val){
	        if(empty($val)){
	            continue;
	        }	
	        $new_arr = array();
	        if(strpos('-',$val)){
	            $tmp = explode('-', $val);
	            $min = intval($tmp[0]);
	            $max = intval($tmp[1]);	            
                for($i=$min;$i<=$max;$i++){
                    $new_arr[] = $i;
                    $new_arr[] = $i+0.5;
                }
	        }else{
	            $new_arr[] = $val;
	        }
	        $data[$key]=$new_arr;
	    }
	
	    return $data;
	}
	
    /**
     * 款号查询适配形状
     * @param unknown $filter
     * @return multitype:number unknown
     */
    public function getStyleParamsBySn($filter)
    {
        $shapearr = array();
        $sql = "select style_id from base_style_info where style_sn ='".$filter['style_sn']."'";
        $style_id = (int) DB::getOne($sql);
        $tempSql = "";
        if(!empty($style_id)){
            $tempSql = "select shape from rel_style_stone where stone_position=1 and stone_cat in(1,2) and shape<>0 AND style_id =".$style_id;
            $shapearr = DB::getAll($tempSql);
        }
        return array('return_sql'=>$tempSql, 'return_msg'=>$shapearr, 'error'=>0, 'error_msg'=>"");
    }

    /**
     * 查询情侣戒信息
     * @param unknown $filter
     * @return multitype:number unknown
     */
    public function getCoupleInfo($filter, $field="*")
    {
        $sql = "SELECT {$field} FROM rel_style_lovers WHERE (style_sn1 = '".$filter['style_sn']."' OR style_sn2 = '".$filter['style_sn']."')";
        $data = DB::getRow2($sql);
        return $data;
    }
    /**
     * 查询款号列表
     * @param unknown $filter
     * @return multitype:string unknown number multitype:
     */
    public function getStyleSnList($filter){
        $result = array('return_sql'=>'', 'return_msg'=>$shapearr, 'error'=>0, 'error_msg'=>"");
        
        $sql = "SELECT style_sn FROM base_style_info WHERE 1";
        if(!empty($filter['keyword'])){
            $sql .= " AND (style_sn='{$filter['keyword']}' or style_name like '%{$filter['keyword']}%')";
        }
        if(!empty($filter['style_name'])){
            $sql .= " AND style_name like '%{$filter['style_name']}%'";
        }
        $data = DB::getAll($sql);
        
        if(!empty($data)){
           $data = array_column($data,"style_sn");
           $result['error'] = 0;
           $result['error_msg'] = "查询成功";
           $result['return_msg'] = $data;
           $result['return_sql'] = $sql;
        }else{
            $result['error'] = 1;
            $result['error_msg'] = "查询失败";
            $result['return_msg'] = array();
            $result['return_sql'] = $sql;
        }
        return $result;
    }
}