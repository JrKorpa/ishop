<?php

class diamond extends base_repository implements idiamond  {

    /**
    * 裸钻检索参数
    **/
    public function get_diamond_index($keys=[], $where=[])
    {   
        $where['keys'] = $keys;
        return $this->invoke("getdiamondindex", $where);
    }

    /**
    * 查询裸钻列表
    * @page_size 每页显示条数
    * @page_index 第几页
    * @where array 检索条件
    **/
    public function get_diamond_list($page_size, $page_index, $where=[])
    {   
        $where['page'] = $page_index;
        $where['pageSize'] = $page_size;
        return $this->invoke("getwitdiamondlist",$where);
    }

    /**
    * 查询裸钻推荐列表
    * @where array 检索条件
    **/
    public function get_tuijian_list($where=[])
    {
        return $this->invoke("getTuiJianList",$where);
    }
    
    /**
     * 查询裸钻详情，参数 cert_id
     * @see idiamond::get_diamond_info()
     */
    public function get_diamond_info($where=[]){
        if(empty($where)) return false;
        return $this->invoke("getDiamondInfo",$where);
    }

    /**
     * 查询天生一对，参数 kuan_sn
     * @see idiamond::get_diamond_by_kuan_sn()
     */
    public function get_diamond_by_kuan_sn($kuan_sn)
    {   
        $where = array();
        $where['kuan_sn'] = $kuan_sn;
        return $this->invoke("GetDiamondByKuan_sn",$where);
    }
    
    /**
    * 根据证书号批量更新裸钻信息
    * @data array(array('cert_id'=>'2321112332', status'=>1),array('cert_id'=>'2321112332', status'=>1))
    **/
    public function update_diamond_info($data=[])
    {
        if(!empty($data)){
            $setdata = $res = array();
            foreach ($data as $v) {
                $cert_id = isset($v['cert_id'])?$v['cert_id']:'';
                unset($v['cert_id']);
                if(!empty($cert_id) && !empty($v)){
                    $res[$cert_id] = $v;
                }else{
                    return false;
                }
            }
            $setdata['data'] = $res;
            return $this->invoke("editDiamondInfoMulti", $setdata);
        }
        return false;
    }

    /**
     * 裸钻加价率
     * @diamondinfo 裸钻信息 array()
     * @store_id    店铺ID
     * @company_id  公司ID
     */
    public function multiply_jiajialv(&$diamondinfo, $store_id='', $company_id='')
    {
        if(empty($diamondinfo) || empty($store_id)) return false;
        if ($company_id == '666' 
            || $company_id == '488' 
            || $company_id == '623') {
            $calc_func = function(&$d, $company_id='') {
                if ($d['cert'] == 'HRD-S') {
                    $x = 1.1;
                    if ($company_id == '623') {
                        if ($d['carat'] >= 0.5) {
                            $x = 1.15;
                        } else {
                            $x = 1.35;
                        }
                    }
                    
                    $d['shop_price'] = round($d['shop_price'] * $x);
                    $d['jiajialv'] = $x;
                }
            };
            
            if (count($diamondinfo) == count($diamondinfo, 1)) {
                $calc_func($diamondinfo, $company_id);
            } else {
                foreach ($diamondinfo as &$d) {
                    $calc_func($d, $company_id);
                }
            }
            
            return;
        }
        $jiajialv_model = new diamond_jiajialvModel();// Model('diamond_jiajialv');
        $where = array('channel_id'=>$store_id, 'status'=>1);
        $jiajialv = $jiajialv_model->getDiamondJiajialvList($where);
        //print_r($jiajialv);
        $calc_func = function(&$d) use($jiajialv) {
            if ($d['pifajia'] == 0) {
                $d['shop_price_recalc'] = 0;
                return;
            }
            //echo $d['cert'],'--',$d['carat'],'--',$d['good_type'];
            foreach ($jiajialv as $cfg) {
                if ($cfg['cert'] == $d['cert'] 
                    && $d['good_type'] == $cfg['good_type'] 
                    && $cfg['carat_min'] <= $d['carat'] 
                    && $d['carat'] < $cfg['carat_max']) {
                    $d['shop_price'] = round($d['pifajia'] * $cfg['jiajialv']);
                    $d['jiajialv'] = $cfg['jiajialv'];
                    $d['shop_price_recalc'] = 1;
                    break;
                }
            }
            if (!isset($d['shop_price_recalc'])) {

                //$lv =  $d['good_type'] == 1 ? 1.95 : 1.95;

                /**
                 * 针对星耀： 如果没有设置加价率，按以下逻辑
                 * 30-49分最低2.1；50-59分最低1.643；60-99分最低1.546；100-149分最低1.457；150分以上最低1.2
                 */
                /*
                if ($d['cert'] == 'HRD-S') {
                    if ($d['carat'] >= 1.5) {
                        $lv = 1.2;
                    } else if ($d['carat'] >= 1) {
                        $lv = 1.457;
                    } else if ($d['carat'] >= 0.6) {
                        $lv = 1.546;
                    } else if ($d['carat'] >= 0.5) {
                        $lv = 1.643;
                    } else if ($d['carat'] >= 0.3) {
                        $lv = 2.1;
                    }

                }
                */

                //获取对应证书类型的默认加价率数组
                if($d['good_type'] == 1){//货品类型：1现货2期货
                    $default_jiajialv = "store_lz_moren_jijialv";
                }else{
                    $default_jiajialv = "store_lz_qihuo_moren_jijialv";
                }
                $store_lz_jijialv_arr = paramsHelper::echoOptionText($default_jiajialv,$d['cert']);
                if(empty($store_lz_jijialv_arr)){
                    //获取默认加价率
                    $lv = paramsHelper::echoOptionText($default_jiajialv,'default');
                }else{
                    //获取定义石重数组的健名
                    $carat_arr = paramsHelper::getParams("carat");
                    $carat_key_arr = array_keys($carat_arr);
                    //组成新的默认加价率关联数组
                    $carat_min_arr = array_combine($carat_key_arr,$store_lz_jijialv_arr);

                    $carat = "0";
                    //获取对应的钻重范围
                    foreach ($carat_key_arr as $v){
                        $carat_arr = explode('~',$v);
                        if(isset($carat_arr[1])){
                            if($d['carat'] >= $carat_arr[0] && $d['carat'] < $carat_arr[1]){
                                $carat = $v;
                                break;
                            }
                        }else{
                            if($d['carat'] >= $carat_arr[0]){
                                $carat = $v;
                                break;
                            }
                        }
                    }
                    //获取对应钻重的默认加价率
                    if(isset($carat_min_arr[$carat])){
                        $lv = $carat_min_arr[$carat];
                    }else{
                        $lv = paramsHelper::echoOptionText($default_jiajialv,'default');
                    }
                }


                $d['shop_price'] = round($d['pifajia'] * $lv);
                $d['shop_price_recalc'] = 0;
                $d['jiajialv'] = $lv;
            }
        };
        if (count($diamondinfo) == count($diamondinfo, 1)) {
            $calc_func($diamondinfo);
        } else {
            foreach ($diamondinfo as &$d) {
                $calc_func($d);
            }
        }
    }

    /*
    * 打折优惠取裸钻详细 diamond_info diamond_info_all
    * $cert_id 证书号
     */
    public function GetDiamondByCert_id($cert_id, $where=[])
    {
        $where['cert_id'] = $cert_id;
        return $this->invoke("GetDiamondByCert_id", $where);
    }

    /*
    * 证书号获取天生一对钻 get_tsyddia_by_cert_id()
    * $cert_id 证书号
    * $store_id 商铺ID
    * $company_id 公司ID
     */
    public function get_tsyddia_by_cert_id($cert_id, $store_id="", $company_id="")
    {
        $result = array('error'=>0,'error_msg'=>'','return_msg'=>'');
        $diaKuan = array();
        $data = $this->get_diamond_info(array('cert_id'=>$cert_id));
        if($data['error'] == 0){
            $kuan_sn = $data['return_msg']['kuan_sn'];
            if(!empty($kuan_sn)){
                $dotsydinfo = $this->get_diamond_by_kuan_sn($kuan_sn);
                if($dotsydinfo['error'] == 0){
                    $dia_kuan = $dotsydinfo['return_msg'];
                    if(!empty($dia_kuan)){
                        $this->multiply_jiajialv($dia_kuan, $store_id, $company_id);
                        foreach($dia_kuan as $k => $v){
                            if($v['cert_id']!=$cert_id){
                                $diaKuan = $v;
                                break;
                            }
                        }
                    }
                }
            }
        }
        if(empty($diaKuan)){
            $result['error'] = 1;
            return $result;
        }else{
            $result['return_msg'] = $diaKuan;
            return $result;
        }
    }

    /*
    * 天生一对列表 get_tsyd_list_by_dia()
    * $cert_id 证书号
    * $store_id 商铺ID
    * $company_id 公司ID
     */
    public function get_tsyd_list_by_dia($diamond_list, $store_id, $company_id)
    {
        $result = array('error'=>0,'error_msg'=>'','return_msg'=>'');
        $_goods_list=array();
        foreach($diamond_list as $key => $val){
            if($val['cert']=='HRD-D' && $val['kuan_sn']!=''){
                $diainfo = $dia_kuan =array();
                $diainfo=$this->get_diamond_by_kuan_sn($val['kuan_sn']);
                $dia_kuan = isset($diainfo['return_msg']) && !empty($diainfo['return_msg'])?$diainfo['return_msg']:array();
                if(!empty($dia_kuan)){
                    $this->multiply_jiajialv($dia_kuan, $store_id, $company_id);
                    foreach($dia_kuan as $k => $v){
                        if($v['goods_sn']!=$val['goods_sn']){
                            $val['add']=$v;
                            break;
                        }
                    }
                }
            }
            $_goods_list[]=$val;
        }
        $diamond_list=$_goods_list;
        $kuan_sn=array();
        foreach($diamond_list as $key=>$val)
        {
            /*if(isset($val['img']) && !empty($val['img'])){
               if(substr($val['img'],0,63)=='http://diamonds.kirangems.com/GemKOnline/DiaSearch/appVideo.jsp'
                    || substr($val['img'],0,48)=='https://diamanti.s3.amazonaws.com/images/diamond'){
                    $diamond_list[$key]['is_3d'] = "是";
                }else{
                    $diamond_list[$key]['is_3d'] = "否";
                } 
            }*/
            if($val['kuan_sn']!=''){
                if(!in_array($val['kuan_sn'],$kuan_sn)){
                    $kuan_sn[]=$val['kuan_sn'];
                }else{
                    unset($diamond_list[$key]);
                }
            }
        }
        if(!empty($diamond_list)){
            $result['return_msg'] = $diamond_list;
        }else{
            $result['error'] = 1;
        }
        return $result;
    }
}

