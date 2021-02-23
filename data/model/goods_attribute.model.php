<?php
/**
 * 商品属性MODEL
 *  -------------------------------------------------
 *   @file		: GoodsAttributeModel.php
 *   @link		: 珂兰钻石 www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 11:11:09
 *   @update	:
 *  -------------------------------------------------
 */
class goods_attributeModel extends Model
{
	function __construct ()
	{
		parent::__construct('app_attribute');
	}
    /**
     * 获取商品属性列表，根据属性类型CODE
     * @param array $attribute_code
     */
    public function getAttributeValues($attribute_code)
    {
        if(empty($attribute_code)){
            return array();
        }
        $sql = "SELECT a.`attribute_name`,a.`attribute_code`,b.`att_value_name` as attribute_value FROM front.`app_attribute` AS a,front.`app_attribute_value` AS b WHERE 
a.`attribute_id`=b.`attribute_id` AND b.att_value_status=1 AND a.attribute_code='{$attribute_code}'";
        return DB::getAll($sql);
    }
    /**
     * 根据属性ID获取属性值列表
     * @param unknown $attr_id
     */
    public function getAttrValsByAttrId($attr_id){
        $sql = "SELECT a.`attribute_id`,a.show_type,b.`att_value_name`,b.`att_value_name`,b.`att_value_id` FROM front.`app_attribute` AS a,front.`app_attribute_value` AS b WHERE 
a.`attribute_id`=b.`attribute_id` AND b.att_value_status=1 AND a.attribute_id={$attr_id}";
        return DB::getAll($sql);
    }
    /**
     * 获取表面工艺
     * @param string $all
     */
    public function getFaceworkList($all = true) {
        $data = $this->getAttributeValues("biaomiangongyi");
        $attr_list = array_column($data,"attribute_value","attribute_value");
        return $attr_list;
    }
    /**
     * 获取 镶嵌方式
     * @param string $all
     */
    public function getXiangqianList($numeral_key = false) {
        return $numeral_key ?
        array('1'=>'工厂配钻，工厂镶嵌','2'=>'不需工厂镶嵌','7'=>"镶嵌4C裸钻",'8'=>'镶嵌4C裸钻，客户先看钻','3'=>'需工厂镶嵌','4'=>'客户先看钻再返厂镶嵌','5'=>'成品','6'=>'半成品') :
        array('工厂配钻，工厂镶嵌'=>'工厂配钻，工厂镶嵌','不需工厂镶嵌'=>'不需工厂镶嵌','镶嵌4C裸钻'=>'镶嵌4C裸钻','镶嵌4C裸钻，客户先看钻'=>'镶嵌4C裸钻，客户先看钻','需工厂镶嵌'=>'需工厂镶嵌','客户先看钻再返厂镶嵌'=>'客户先看钻再返厂镶嵌','成品'=>'成品','半成品'=>'半成品');
    }
    /**
     * 获取 镶嵌方式 非4C配钻
     * @param string $all
     */
    public function getXiangqianListNew($all = true) {        
        return array('工厂配钻，工厂镶嵌'=>'工厂配钻，工厂镶嵌','不需工厂镶嵌'=>'不需工厂镶嵌','需工厂镶嵌'=>'需工厂镶嵌','客户先看钻再返厂镶嵌'=>'客户先看钻再返厂镶嵌','成品'=>'成品','半成品'=>'半成品');
    }
    /**
     * 获取 布产类型列表
     * @param string $all
     */
    public function getBuchanTypeList(){
        return array('0'=>'普通件','1'=>'加急件','2'=>'特急件');
    }
    /**
     * 获取钻石颜色
     * @param string $all
     */
    public function getColorList($all = true){
        //$data= array("不分级","N","M","L","K-L","K","J-K","J","I-J","I","H-I","H","H+","G-H","G","F-G","F","E-F","E","D-E","D","黄","蓝","粉","橙","绿","红","香槟","格雷恩","紫","混色","蓝紫色","黑","变色","其他","白色","金色");
        $data = array(
            'D'=>'D','D-E'=>'D-E',
            'E'=>'E','E-F'=>'E-F',
            'F'=>'F','F-G'=>'F-G',
            'G'=>'G','G-H'=>'G-H',
            'H'=>'H','H+'=>'H+','H-I'=>'H-I',
            'I'=>'I','I-J'=>'I-J',
            'J'=>'J','J-K'=>'J-K',
            'K'=>'K','K-L'=>'K-L',
            'L'=>'L','M'=>'M',
            '白'=>'白','黑色'=>'黑色','金色'=>'金色','无'=>'无');
        /* foreach($data as $key=>$vo){
            $data[$vo] = $vo;
            unset($data[$key]);
        } */
        if($all !== true){
            $res = $this->getAttributeValues("yanse");
            $data = array_column($res,"attribute_value");
        }
        return $data;
    }
    /**
     * 获取钻石净度
     * @param string $all
     * @return Ambigous <multitype:string , multitype:>
     */
    public function getClarityList($all = true){
        $data = array(
            '不分级'=>'不分级',
            'FL'=>'FL',
            'IF'=>'IF',
            'VVS'=>'VVS','VVS1'=>'VVS1','VVS2'=>'VVS2',
            'VS'=>'VS','VS1'=>'VS1','VS2'=>'VS2',
            'SI'=>'SI','SI1'=>'SI1','SI2'=>'SI2',
            'I1'=>'I1','I2'=>'I2','P'=>'P',
            'P1'=>'P1','无'=>'无');
        //$data= array("不分级","P","P1","I","I1","I2","SI","SI1","SI2","VS","VS1","VS2","VVS","VVS1","VVS2","IF","FL");
        /* foreach($data as $key=>$vo){
            $data[$vo] = $vo;
            unset($data[$key]);
        } */
        if($all !== true){
            $res = $this->getAttributeValues("zuanshijingdu");
            $data = array_column($res,"attribute_value");
        }
        return $data;
    }    
    /**
     * 获取钻石切工
     * @return multitype:string
     */
    public function getCutList(){
        $data= array('EX'=>'EX', 'VG'=>'VG', 'G'=>'G', 'Fair'=>'Fair');
        return $data;
    }
    /**
     * 获取钻石抛光
     * @return multitype:string
     */
    public function getPolishList(){
        $data= array('EX'=>'EX', 'VG'=>'VG', 'G'=>'G', 'Fair'=>'Fair');
        return $data;
    }
    /**
     * 获取钻石对称
     * @return multitype:string
     */
    public function getSymmetryList(){
        $data= array('EX'=>'EX', 'VG'=>'VG', 'G'=>'G', 'Fair'=>'Fair');
        return $data;
    }
    /**
     * 获取钻石荧光
     * @return multitype:string
     */
    public function getFluorescenceList(){
        $data= array('N'=>'N', 'F'=>'F', 'M'=>'M', 'S'=>'S');
        return $data;
    }
    /**
     * 获取钻石形状
     * @return multitype:string
     */
    public function getShapeList(){
        $data= array(1 => '圆形', 2 => '公主方形', 3 => '祖母绿形', 4 => '橄榄形', 5 => '椭圆形', 6 => '水滴形', 7 => '心形', 8 => '坐垫形', 9 => '辐射形', 10 => '方形辐射形', 11 => '方形祖母绿', 12 => '三角形',13=>'戒指托',14=>'异形',15=>'梨形',16=>'阿斯切',17 => '马眼形', 18 => '长方形', 19 => '雷迪恩形');
        return $data;
    }
    /**
     * 获取钻石证书类型
     * @return multitype:string
     */
    public function getCertList($all = true){
        $data= array('HRD-D'=>'HRD-D','GIA'=>'GIA','HRD'=>'HRD','IGI'=>'IGI','DIA'=>'DIA','EGL'=>'EGL','HRD-S'=>'HRD-S','NGTC'=>'NGTC','NGGC'=>'NGGC','NGSTC'=>'NGSTC','NTGS'=>'NTGS','JZJ'=>'JZJ','无'=>'无');
        if($all !== true){
            $res = $this->getAttributeValues("zhengshu");
            $data = array_column($res,"attribute_value");
        }
        return $data;
    }
    /**
     * 获取所有材质列表
     */
    public function getCaizhiList($all = true)
    {
        $data= array(
            '10'=>'9K',
            '13'=>'10K',
            '9'=>'14K',
            '1'=>'18K',
            '11'=>'PT900',
            '2'=>'PT950',
            '17'=>'PT990',
            '12'=>'PT999',            
            '3'=>'18K&PT950',
            '4'=>'S990',
            '6'=>'S925',
            '8'=>'足金',
            '5'=>'千足银',
            '7'=>'千足金',
            '14'=>'千足金银',
            '15'=>'裸石',
            '16'=>'无',
            '0'=>'其它',  
            '18'=>'S999'      
        );  
        if($all !== true){
             $res = $this->getAttributeValues("caizhi");
             $data = array_column($res,"attribute_value");
             asort($data);
        }
        return $data;      
    }
    /**
     * 获取所有金色列表
     */
    public function getJinseList($all = true)
    {
        $data= array(
            '0'=>'无',
            '1'=>'白',
            '2'=>'黄',           
            '3'=>'玫瑰金',
            '4'=>'分色',
            '5'=>'彩金',
            '6'=>'玫瑰黄',
            '7'=>'玫瑰白',
            '8'=>'黄白',
            '9'=>'白黄（黄为电分色）',
            '10'=>'按图做'
        );
        if($all !== true){
            $res = $this->getAttributeValues("caizhiyanse");
            $data = array_column($res,"attribute_value");
        }
        return $data;
    }
    public function getZhuchengseList(){
        return array(
            '10K白'=>'10K白',
            '10K彩金'=>'10K彩金',
            '10K玫瑰金'=>'10K玫瑰金',
            '10K黄'=>'10K黄',
            '10K玫瑰白'=>'10K玫瑰白',
            '10K黄白'=>'10K黄白',
            '10K玫瑰黄'=>'10K玫瑰黄',
            '14K白'=>'14K白',
            '14K黄'=>'14K黄',
            '14K彩金'=>'14K彩金',
            '14K玫瑰金'=>'14K玫瑰金',
            '14K黄白'=>'14K黄白',
            '14K玫瑰白'=>'14K玫瑰白',
            '14K玫瑰黄'=>'14K玫瑰黄',
            '18K白'=>'18K白',
            '18K黄白'=>'18K黄白',
            '18K彩金'	=>'18K彩金'	,
            '18K黄'=>'18K黄',
            '18K玫瑰金'=>'18K玫瑰金',
            '18K玫瑰白'=>'18K玫瑰白',
            '18K玫瑰黄'=>'18K玫瑰黄',
            '9K玫瑰白'=>'9K玫瑰白',
            '9K黄白'=>'9K黄白',
            '9K白'=>'9K白',
            '9K彩金'=>'9K彩金',
            '9K黄'=>'9K黄',
            '9K玫瑰金'=>'9K玫瑰金',
            '9K玫瑰黄'=>'9K玫瑰黄',
            'PT900'=>'PT900',
            'PT990'=>'PT990',
            'PT950'=>'PT950',
            'S925'=>'S925',
        	'S925白'=>'S925白',
        	'S925玫瑰金'=>'S925玫瑰金',
        	'S925黄'=>'S925黄',
            'S990'=>'S990',
            'S999'=>'S999',
            '裸石	'=>'裸石	',
            '足金'=>'足金',
            '其它'=>'其它',
            '无'=>'无'
        );
    }
    /**
     * 根据主成色，获取数字类型的材质，金色
     * @param $zhuchengse
     * @return array
     */
    public function explodeZhuchengseToInt($zhuchengse){
        $caizhi_arr = $this->getCaizhiList();
        $jinse_arr  = $this->getJinseList();
        $caizhi_keys_arr = array_flip($caizhi_arr);
        $jinse_keys_arr = array_flip($jinse_arr);
        
        $caizhi_jinse = $this->explodeZhuchengseToStr($zhuchengse);
        $caizhi = $caizhi_jinse['caizhi'];
        $jinse  = $caizhi_jinse['jinse'];
        if(in_array($caizhi,$caizhi_arr)){
            $caizhi = $caizhi_keys_arr[$caizhi];   
        }else{
            $caizhi = 0;
        }
        if(in_array($jinse,$jinse_arr)){
            $jinse = $jinse_keys_arr['jinse'];
        }else{
            $jisne = 0;
        }
        $data = array(
            'caizhi'=>$caizhi,
            'jinse' =>$jinse,
        );
        return $data;
    }
    /**
     * 根据主成色拆分,材质，金色
     * @param unknown $zhuchengse
     * @return multitype:string Ambigous <string, unknown>
     */
    public static function explodeZhuchengseToStr($zhuchengse){
        $zhuchengse = strtoupper($zhuchengse);
        if(preg_match('/[0-9a-z]+/i',$zhuchengse,$caizhi_jinse)){
            $caizhi = strtoupper($caizhi_jinse[0]);
            $jinse  = substr($zhuchengse,strlen($caizhi_jinse[0]));
        }else{
            $caizhi = $zhuchengse;
            $jinse  = '';
        }
        if(empty($zhuchengse)){
            $caizhi = empty($caizhi)?"":$caizhi;
            $jinse = empty($jinse)?"无":$jinse;
        }        
        $data = array(
            'caizhi'=>$caizhi,
            'jinse' =>$jinse,
        );        
        return $data;
        
    } 
    /**
     * 获取所有镶口列表
     */
    public function getXiangkouList($all = true)
    {
        $data= array(
            
        );
        if($all !== true){
            $res = $this->getAttributeValues("xiangkou");
            $data = array_column($res,"attribute_value");
            asort($data);
        }
        return $data;
    }
    public function getAttrListByStyleSn($style_sn,$attribute_code_arr=array()){
        $sql = "SELECT	a.attribute_id,a.style_sn,a.attribute_value,a.product_type_id,a.cat_type_id,a.show_type,b.attribute_name,b.attribute_code
        FROM rel_style_attribute a LEFT JOIN app_attribute b ON a.attribute_id = b.attribute_id  where style_sn='{$style_sn}' and b.attribute_status=1";

        if(!empty($attribute_code_arr)){
            $attribute_code_in = "'".implode("','",$attribute_code_arr)."'";
            $sql .=" AND b.attribute_code in($attribute_code_in)";
        }
        //@file_put_contents("11.txt",$sql);        
        return DB::getAll($sql);
    }
     
    public function getAttrValByIds($ids){
        $sql = "select att_value_id,att_value_name from app_attribute_value where att_value_status=1 AND att_value_id in({$ids})";
        return DB::getAll($sql);
    } 
    public function getAttrValListBySN($style_sn,$attribute_code_arr){
        $attrList = $this->getAttrListByStyleSn($style_sn,$attribute_code_arr);
        foreach ($attribute_code_arr as $vo){
            $attr_list[$vo] = array();
        }
        foreach($attrList as $key1=>$vo1){
            $attrValIds = trim($vo1['attribute_value'],',');
            if($vo1['show_type'] ==1){
                $_attrVal = explode(',',$attrValIds);
                $attrVal = array();
                foreach($_attrVal as $key2=>$vo2){
                    $attrVal[] = $vo2['att_value_name'];
                }
            }else{
                if(empty($attrValIds)){
                    continue;
                }
                $attrVal = $this->getAttrValByIds($attrValIds);
                if(empty($attrVal)){
                    continue;
                }
                $attrVal = array_column($attrVal,'att_value_name','att_value_id');
            }
            $attribute_code = $vo1['attribute_code'];
            $attr_list[$attribute_code] = $attrVal;
        }         
        return $attr_list;
    }
    public function getXilieList(){        
        $sql = "SELECT id,name FROM app_style_xilie";
        $data = DB::getAll($sql);     
        $data = array_column($data,'name','id');
        return $data;
    }
    /**
     * 根据主成色，拆分 材质和材质颜色 
     * (与 explodeZhuchengseToStr类似，但功能不一样)
     * @param unknown $zhuchengse
     * @return multitype:string |multitype:string Ambigous <>
     */
    public static function getGoldAndColor($caizhi)
	{
		$returndata = array('gold'=>'无','color'=>'无');
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
		if($checkinfo == 'S925')
		{
			$returndata['gold'] = 'S925';
			return $returndata;
		}
		if($checkinfo == '千足金' || $checkinfo=='足金')
		{
			//$returndata['gold'] = '24K';
			//$returndata['color']='黄';
			$returndata['gold'] = '足金';
			$returndata['color']='';
			return $returndata;
		}
		
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
		return $returndata;
	}
	
	public function getCatTypeList(){
	    $sql = "SELECT cat_type_id,cat_type_name from app_cat_type";
	    $data = DB::getAll($sql);
	    $data = array_column($data,"cat_type_name",'cat_type_id');
	    return $data;
	}
	
	public function getProductTypeList(){
	    $sql = "SELECT product_type_id,product_type_name from app_product_type";
	    $data = DB::getAll($sql);
	    $data = array_column($data,"product_type_name",'product_type_id');
	    return $data;
	}

}

?>