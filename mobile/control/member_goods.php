<?php
/**
 * 用户商品
 *
 *
 * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */



defined('INTELLIGENT_SYS') or exit('Access Invalid!');

class member_goodsControl extends mobileMemberControl {

    //public $type = array('1'=>'普通<0.5克拉','2'=>'普通0.5（含）~1.0克拉','3'=>'普通1.0（含）~1.5克拉','4'=>'普通1.5（含）克拉以上','5'=>'星耀<0.5克拉','6'=>'星耀0.5（含）~1.0克拉','7'=>'星耀1.0（含）~1.5克拉','8'=>'星耀1.5（含）克拉以上','9'=>'天生一对裸石','10'=>'天生一对成品','11'=>'成品');

    public function __construct() {
        parent::__construct();
    }    
    /**
     * 起版商品添加
     * gaopeng
     */
    public function qiban_addOp(){
        if(empty($_POST['goods_id'])){
            output_error("请填写起版号");
        }
        $goods_id = $_POST['goods_id'];        
        $goods_type = 3;
        //起版信息
        $data = array(
            'cert'=>$_POST['cert'],
            'cert_id'=>$_POST['cert_id'],            
            'color' => $_POST['color'],
            'cut' =>$_POST['cut'],
            'clarity' => $_POST['clarity'],
            'kezi' => $_POST['kezi'],
       );        
       $this->goods_save($goods_id, $goods_type, $data);
    }
    /**
     * 添加裸钻属性
     */
    public function diamond_addOp(){
        
        $store_id = $this->store_info['store_id'];
        $company_id = $this->store_info['store_company_id'];
        
        if(empty($_POST['goods_id'])){
            output_error("货号不能为空");
        }
        if($_POST['xiangqian']==""){
            output_error("请选择镶嵌方式");
        }
        $goods_id = $_POST['goods_id'];
        $goods_type = 2;//裸钻     
        $xiangqian = $_POST['xiangqian'];
        $goods_ids = array($goods_id);
        
        //天生一对裸钻处理
        $diamond_api = data_gateway('idiamond');
        $result = $diamond_api->get_tsyddia_by_cert_id($goods_id, $store_id, $company_id);
        if($result['error']==0){
            $tsydDia = $result['return_msg'];
            $goods_ids[] = $tsydDia['cert_id'];
        }
        //print_r($goods_ids);
        foreach ($goods_ids as $goods_id){
            $data = array(            
                'xiangqian'=>$xiangqian,            
            );        
            $res = $this->goods_save($goods_id, $goods_type, $data,true);
            if(!$res){
                break;
            }
        }
        if($res){
            output_data($res);
        }else{
            output_error("保存失败");
        }
    }
    /**
     * 款式商品添加
     * gaopeng
     */
    public function style_goods_addOp() {
        
        if(empty($_POST['goods_id'])){
            output_error("参数错误：goods_id 不能为空");
        }
        if(empty($_POST['tuo_type'])){
            output_error("请选择成品/空托");
        }
        if($_POST['is_dingzhi']==""){
            output_error("请选择是否定制");
        }
        if($_POST['xiangqian']==""){
            output_error("请选择镶嵌方式");
        }       
        $goods_id = $_POST['goods_id'];
        $goods_type = 1;//款式商品
        $goods_price = $_POST['goods_price'];        
        $tuo_type = $_POST['tuo_type'];
        $is_dingzhi = $_POST['is_dingzhi'];
        $is_xianhuo = $is_dingzhi==1?0:1;
        $xiangqian = $_POST['xiangqian'];
        $store_id = $this->member_info['store_id'];
        //镶嵌方式验证
        $salesApi = data_gateway('isales');
        
        if(!empty($_POST['cpdz_code'])){
            $voucher_info = Model('voucher')->getVoucherInfo(array('voucher_code'=>$_POST['cpdz_code'],'voucher_type'=>2));
            if(empty($voucher_info)){
              output_error("成品定制码不存在！");
            }else if($voucher_info['voucher_state']!=1){
              output_error("成品定制码已被使用！");
            }
            $goods_price = $voucher_info['voucher_price'];
        }
        $is_cpdz = (int)$_POST['is_cpdz'];        
        if($is_cpdz){
            if($_POST['goods_price']<=0){
                output_error("请先取价");
            }
            $goods_price = $_POST['goods_price'];
        }

        //戒托
        $data = array(
            'tuo_type'=>$tuo_type,
            'is_dingzhi'=>$is_dingzhi,
            'is_xianhuo'=>$is_xianhuo,
            'xiangqian'=>$_POST['xiangqian'],
            'shoucun' => $_POST['shoucun'],
            'zhushi_num'=>$_POST['zhushi_num'],
            'cert'=>$_POST['cert'],
            'cert_id'=>$_POST['cert_id'],
            'carat' => $_POST['carat'],
            'color' => $_POST['color'],
            'cut' => $_POST['cut'],                
            'clarity' => $_POST['clarity'],
            'facework' => $_POST['facework'],
            'kezi' => htmlspecialchars_decode($_POST['kezi']),
            'is_cpdz'=>$is_cpdz,
            'cpdz_code' => $_POST['cpdz_code'],
            'goods_price' => $goods_price,
            'goods_hand_price' => $goods_price,
            'goods_pay_price' => $goods_price,
            'xianhuo_adds' => trim($_POST['xianhuo_adds'],","),
        );
        $this->goods_save($goods_id, $goods_type, $data);

    }
    /**
     * 赠品添加
     * gaopeng
     */
    public function gift_addOp(){
        if(empty($_POST['style_sn'])){
            output_error("请选择赠品");
        }
        $goods_id = $_POST['style_sn']."-".(int)$_POST['shoucun'];
        $goods_type = 5;//赠品
        //起版信息
        $data = array(
            'goods_name'=>$_POST['goods_name'],
            'goods_id' =>$goods_id,
            'style_sn'=>$_POST['style_sn'],            
            'shoucun' =>$_POST['shoucun'],
            'goods_price' => $_POST['goods_price'],
            'goods_pay_price' => $_POST['goods_pay_price'],
        );
        $this->goods_save($goods_id, $goods_type, $data);
        
    }
    /**
     * 现货
     */
    public function warehouse_goods_addOp(){
        if(empty($_POST['goods_id'])){
            output_error("请填写货号");
        }
        if(empty($_POST['tuo_type'])){
            output_error("请选择成品/空托");
        }
        if($_POST['xiangqian']==""){
            output_error("请选择镶嵌方式");
        }
        
        $goods_id = $_POST['goods_id'];
        $style_sn = $_POST['style_sn'];
        $goods_type = !empty($_POST['goods_type'])?$_POST['goods_type']:4;//4门店现货 6总部现货
        $goods_price = $_POST['goods_price'];
        $tuo_type = $_POST['tuo_type'];
        $xiangqian = $_POST['xiangqian'];
        $xiangkou = $_POST['xiangkou'];
        $cert_id = $_POST['cert_id'];
        $store_id = $this->member_info['store_id'];
        
        if(in_array($xiangqian,array("需工厂镶嵌","客户先看钻再返厂镶嵌","工厂配钻，工厂镶嵌","镶嵌4C裸钻"))){
            if($xiangkou>0 && $_POST['carat'] <=0){
                $error = "主石单颗重不能为空";
                output_error($error);
            }else if($xiangkou>0 && empty($_POST['clarity'])){
                $error = "主石颜色不能为空";
                output_error($error);
            }else if($xiangkou>0 && empty($_POST['color'])){
                $error = "主石净度不能为空";
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

        //镶嵌方式验证        
        if(!empty($_POST['cpdz_code'])){
            $voucher_info = Model('voucher')->getVoucherInfo(array('voucher_code'=>$_POST['cpdz_code'],'voucher_type'=>2));
            if(empty($voucher_info)){
                output_error("成品定制码不存在！");
            }else if($voucher_info['voucher_state']!=1){
                output_error("成品定制码已被使用！");
            }
            $goods_price = $voucher_info['voucher_price'];
        }
        //戒托
        $data = array(
            'xiangqian'=>$_POST['xiangqian'],
            'zhushi_num'=>$_POST['zhushi_num'],
            'shoucun'=>$_POST['shoucun'],
            'cert'=>$_POST['cert'],
            'cert_id'=>$_POST['cert_id'],
            'carat' => $_POST['carat'],
            'color' => $_POST['color'],
            'cut' => $_POST['cut'],
            'clarity' => $_POST['clarity'],
            'facework' => $_POST['facework'],
            'kezi' => htmlspecialchars_decode($_POST['kezi']),
            'cpdz_code' => $_POST['cpdz_code'],
            'goods_price' => $goods_price,
            'goods_hand_price' => $goods_price,
            'goods_pay_price' => $goods_price,
            'xianhuo_adds' => trim($_POST['xianhuo_adds'],","),
        );
        $this->goods_save($goods_id, $goods_type, $data);
        
    }
    /**
     * 用户商品编辑
     * by gaopeng 2018-3-30
     */
    public function goods_updateOp() {
        if($_POST['is_dingzhi']==""){
            output_error("请选择是否定制");
        }
        $salesApi = data_gateway('isales');
        $goods_id = $_POST['goods_id'];
        $goods_type = $_POST['goods_type'];
        $channel_id = $this->member_info['store_id'];
        $is_dingzhi = $_POST['is_dingzhi'];
        $is_xianhuo = $is_dingzhi==1?0:1;
        if($goods_type==1){
            //戒托
            $data = array(
                //'tuo_type'=>$_POST['tuo_type'],
                'is_dingzhi'=>$is_dingzhi,
                'is_xianhuo'=>$is_xianhuo,
                'xiangqian'=>$_POST['xiangqian'],
                //'zhushi_num'=>$_POST['zhushi_num'],
                //'cert'=>$_POST['cert'],
                'cert_id'=>$_POST['cert_id'],
                //'carat' => $_POST['carat'],
                //'color' => $_POST['color'],
                //'cut' => $_POST['cut'],                
                //'clarity' => $_POST['clarity'],
                'facework' => $_POST['facework'],
                'kezi' => $_POST['kezi'],
                //'is_cpdz'=>$is_cpdz,
                //'cpdz_code' => $_POST['cpdz_code'],
                //'goods_price' => $goods_price,
                //'goods_pay_price' => $goods_price,
                'others' => $_POST['others'],
            );
        }else{
            output_error("goods_type 参数错误");
        }
        $this->goods_edit($goods_id, $goods_type, $data);

    }
    
    
    /**
     * 修改刻字
     * gaopeng
     */
    public function goods_edit_keziOp(){
        $goods_id = $_POST['goods_id'];
        $goods_type = $_POST['goods_type'];
        $kezi = $_POST['kezi'];
        $data = array(
            'kezi'=>$kezi,
        );
        $this->goods_save($goods_id, $goods_type, $data);
    }
    
    /**
     * 修改折扣金额
     * gaopeng
     */
    public function goods_edit_discountOp(){
        if(empty($_POST['goods_id'])){
            output_error("参数错误：goods_id 不能为空");
        }
        if(empty($_POST['goods_type'])){
            output_error("参数错误：goods_type 不能为空");
        }
        $goods_id = $_POST['goods_id'];
        $goods_type = $_POST['goods_type'];
        //$goods_hand_price实际输入金额
        //$goods_hand_price_min 最小可输入金额
        $goods_hand_price = $goods_hand_price_min = floatval($_POST['goods_hand_price']);
        $discount_code = $_POST['discount_code'];
        if($goods_hand_price < 0 || ($goods_hand_price == 0 && $goods_type != 5) ){
            output_error("商品金额不合法");
        }
        $logic_buy_1 = Logic("buy_1");
        $member_id = $this->member_info['member_id'];
        $store_id = $this->store_info['store_id'];
        $company_id = $this->store_info['store_company_id'];
        //查询商品详情
        $res = $logic_buy_1->apiGetGoodsInfo($goods_id,$goods_type,$store_id,$company_id,$member_id);
        if($res['state']==true){
            $goods_info = $res['data'];
        }else{
            output_error($res['msg']);
        }
        $goods_price = $goods_info['goods_price'];
        $model_voucher = Model('voucher');
        //取出最低优惠金额；
        if($discount_code == ''){
            $discountinfo = $this->getDiscount($member_id, $goods_info);
            if($discountinfo['error'] == 1){
                $error = $discountinfo['error_msg'];
                output_error($error);
            }
            $favorable_money = isset($discountinfo['data']['favorable_money'])?$discountinfo['data']['favorable_money']:0;
            if($favorable_money <= 0 && $goods_type != 5){
                output_error("商品最低优惠金额异常！");
            }
            if(bccomp($goods_hand_price, $favorable_money) == -1 && $goods_type != 5){
                output_error("超出最低折扣权限，请输入商品折扣码打折！");   
            }         
        }else{
            //获取商品属于那种类型
            $type = $model_voucher->get_diamond_type($member_id, $goods_info);
            $voucher_info = $model_voucher->getVoucherInfo(array('voucher_code'=>$discount_code,'voucher_type'=>1));
            if(empty($voucher_info)){
                output_error("折扣码不存在！");
            }else if($voucher_info['voucher_start_date']>time() || $voucher_info['voucher_end_date']<time()){
                output_error("折扣码不在有效期内！");
            }else if($voucher_info['voucher_store_id']!=$store_id){
                output_error("折扣码不属于当前门店！");
            }else if($voucher_info['voucher_state']!=1){
                output_error("折扣码已被使用！");
            }else if($voucher_info['voucher_goods_type'] != $type){
                output_error("折扣码类型与商品类型不一致！");
            }
            if($goods_hand_price == $goods_price){
                output_error("亲，使用折扣券后，需要手动修改商品金额，请确认！");
            }
            $favorable_money   = round($goods_price*(1-$voucher_info['voucher_price']/100));
            //最低手调商品价格
            $goods_hand_price_min = $goods_price - $favorable_money;
        }
        
        if($goods_hand_price < $goods_hand_price_min) { 
            output_error("超出折扣码折扣范围,请确认并修改商品价格");
        }else{
            $goods_pay_price = $goods_hand_price;
            $discount_price = $goods_price - $goods_pay_price;
        }
        $data = array(
            'goods_pay_price'=>$goods_pay_price,
            'goods_hand_price'=>$goods_hand_price,
            'discount_code'=>$discount_code, 
            'discount_price'=>$discount_price,
        );
        $this->goods_save($goods_id, $goods_type, $data);        
    }
    /**
     * 修改商品
     * @param unknown $goods_id
     * @param unknown $goods_type
     * @param unknown $data
     * gaopeng
     */
    private function goods_save($goods_id,$goods_type,$data,$return=false){
        $model_goods = Model("member_goods");
        $member_id = $this->member_info['member_id'];
        $store_id = $this->store_info['store_id'];
        $store_name = $this->store_info['store_name'];
        if(empty($goods_id)){
            output_error("goods_id不能为空");
        }
        if(empty($goods_type)){
            output_error("goods_type不能为空");
        }
        $data['store_id'] = $store_id;
        $data['store_name'] = $store_name;
        
        $where = array("goods_id"=>$goods_id,'member_id'=>$member_id,'store_id'=>$store_id);
        $olddo = $model_goods->getGoodsInfo($where,"*",false);    
        if(!empty($olddo)){
            $goods_info = unserialize($olddo['goods_info']);
            if(!empty($goods_info)){
                $goods_info = array_merge($goods_info,$data);
            }else{
                $goods_info = $data;
            }
            $olddo['goods_info'] = serialize($goods_info);
            $olddo['update_time'] = date("Y-m-d H:i:s");
            $res = $model_goods->editGoods($olddo,$where);
        }else{
            $goods_info = $data;
            $newdo = array();
            $goods_key = ($member_id%10).(time()%100000).rand(10000, 99999);
            $newdo['goods_key'] = $goods_key;
            $newdo['goods_id'] = $goods_id;
            $newdo['member_id'] = $member_id;
            $newdo['store_id'] = $store_id;
            $newdo['goods_type'] = $goods_type;
            $newdo['goods_info'] = serialize($goods_info);
            $newdo['update_time'] = date("Y-m-d H:i:s");
            $res = $model_goods->addGoods($newdo);
        }
        if($return){
            return $res?$goods_info:false;
        }else{
            if($res){            
                output_data($goods_info);
            }else{
                output_error("保存失败");
            }
        }
    }

    /**
     * 编辑商品信息
     */
    public function edit_goods_infoOp(){
         if(empty($_POST['style_sn'])){
            output_error("参数错误:style_sn不能为空");
        }
        $model_goods = Model("member_goods");
        $member_id = $this->member_info['member_id'];
        $store_id = $this->store_info['store_id'];
        $style_sn = $_POST['style_sn'];
        $goods_id = $_POST['goods_sn'];
        $cert_id  = $_POST['cert_id'];
        $styleApi = data_gateway('istyle');    
        $keys = array("tuo_type","confirm","xiangqian","zhushi_num","cert","color","clarity","cut","facework");
            
        $result = $styleApi->get_style_goods_diy_index($keys,$style_sn);
        if($result['error']==1){
            output_error($result['error_msg']);
        }
        $datas['attr_list'] = $result['return_msg'];
        //var_dump($goods_id,$member_id,$store_id);die;
        $where = array("goods_id"=>$goods_id,'member_id'=>$member_id,'store_id'=>$store_id);
        //print_r($where);
        $olddo = $model_goods->getGoodsInfo($where,"*",false);
        $goods_info = array();
        if(!empty($olddo)){
            $goods_info = $olddo['goods_info'];
        }
        $datas['goods_info'] = $goods_info;
        output_data($datas); 
    }

    /**
     * 订单明细修改商品
     * @param unknown $goods_id
     * @param unknown $goods_type
     * @param unknown $data
     */
    private function goods_edit($goods_id,$goods_type,$data){
        $model_goods = Model("member_goods");
        $member_id = $this->member_info['member_id'];
        $store_id = $this->store_info['store_id'];
        if(empty($goods_id)){
            output_error("goods_id不能为空");
        }
        if(empty($goods_type)){
            output_error("goods_type不能为空");
        }
        $where = array("goods_id"=>$goods_id,'member_id'=>$member_id,'store_id'=>$store_id);
        $olddo = $model_goods->getGoodsInfo($where,"*",false);
        $res = false;
        if(!empty($olddo)){
            $goods_info = unserialize($olddo['goods_info']);
            if(!empty($goods_info)){
                $goods_info = array_merge($goods_info,$data);
            }else{
                $goods_info = $data;
            }
            $olddo['goods_info'] = serialize($goods_info);
            $olddo['update_time'] = date("Y-m-d H:i:s");
            $res = $model_goods->editGoods($olddo,$where);
        }

        if($res){
            output_data($goods_info);
        }else{
            output_error("保存失败");
        }
    }

    /**
    * 折扣权限优惠金额\
    * user_id 用户ID
    * goods_info 商品信息
    */
    public function getDiscount($user_id, $goods_info)
    {

        $model_voucher = Model('voucher');
        $type = $model_voucher->get_diamond_type($user_id, $goods_info);
        $favorable_money = 0;
        $goods_price = isset($goods_info['goods_price']) ? $goods_info['goods_price']:'0';
        $result = array('error'=>1, 'error_msg'=>'', 'data'=>array());
        
        $discountmodel = model("base_lz_discount_config");
        $where = array(
            'user_id' => $user_id,
            'type' => $type,
            'enabled' => 1
            );
        $discountlist = $discountmodel->getBaseLzDiscountConfigInfo($where);
        if(empty($discountlist)){
            $result['error_msg'] = "您没有“".paramsHelper::echoOptionText('voucher_goods_type',$type)."”商品的折扣权限，请申请设置并开启！";
            return $result;
        }
        $zhekou = isset($discountlist['zhekou'])?$discountlist['zhekou']:0;
        if($zhekou <=1 && $zhekou>0){
            $favorable_money =bcmul($goods_price,$zhekou,3);
        }
        if($favorable_money){
            $result['error'] = 0;
            $result['data'] = array('favorable_money'=> $favorable_money, 'zhekou'=>$zhekou);;
            return $result;
        }
        $result['error_msg'] = "优惠金额错误！";
        return $result;
    }




}
