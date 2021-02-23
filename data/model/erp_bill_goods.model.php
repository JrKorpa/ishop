<?php
/**
 * 单据单身模型
 *
 *
 *
 * *  (c) 2015-2018 . (http://www.kela.cn)
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 * @since      珂兰技术中心提供技术支持
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class erp_bill_goodsModel extends Model {
    public function __construct(){
        parent::__construct('erp_bill_goods');
    }
    /**
     * 添加单据明细
     * @param unknown $insert
     * @return unknown
     */
    public function addBillGoods($insert){
        $result = $this->table('erp_bill_goods')->insert($insert);
        return $result;
    }

    public function getErpBillGoodsInfo($condition,$field="*"){
        return $this->table('erp_bill_goods')->field($field)->where($condition)->find();
    }

    /**
     * 编辑单据明细信息
     * @param array $condition
     * @param array $update
     * @return boolean
     */
    public function editErpBillGoods($condition, $update) {
        return $this->where($condition)->update($update);
    }

    /**
     * erp_bill_goods 表列表
     * @param unknown $condition
     * @param string $fields
     * @param string $pagesize
     * @param string $order
     * @param string $limit
     */
    public function getErpBillGoodsList($condition = array(), $field = null, $pagesize = null, $order = '', $limit = null){
       return $this->table('erp_bill_goods')->where($condition)->field($field)->order($order)->limit($limit)->page($pagesize)->select();
    }
    public function searchBillGoodsList($where, $pagesize = null, $order = '', $limit = null){
        $field = array(
            "erp_bill_goods.*",
            "goods_items.goods_id",
            "goods_items.goods_name",
            "goods_items.caizhi",
            "goods_items.jinse",
            "goods_items.jietuoxiangkou",
            "goods_items.zhengshuleibie",
            "goods_items.zhengshuhao",
            "goods_items.zuanshidaxiao",
            "goods_items.zhushilishu",
            "goods_items.shoucun",
            "goods_items.jinzhong",
            "goods_items.product_type",
            "goods_items.cat_type",
            "goods_items.tuo_type",
            "goods_items.zhushi",
            "goods_items.zhushiyanse",
            "goods_items.zhushijingdu",
            "goods_items.zhushiqiegong",
            "goods_items.fushizhong",
            "goods_items.fushilishu",
        );        
        return $this->table('erp_bill_goods,goods_items')->join("left")->on("erp_bill_goods.goods_itemid=goods_items.goods_id")->where($where)->field($field)->order($order)->limit($limit)->page($pagesize)->select();
    }


    public function searchErpBillGoodsList($condition, $pagesize = null, $order = '', $limit = null){
        $field = array(
            "erp_bill.bill_no",
            "erp_bill.item_type",
            "erp_bill.goods_total",
            "erp_bill.from_company_id as b_from_company_id",
            "erp_bill.to_company_id as b_to_company_id",
            "erp_bill.from_store_id as b_from_store_id",
            "erp_bill.to_store_id as b_to_store_id",
            "erp_bill_goods.*",
            "goods_items.goods_id",
            "goods_items.goods_name",
            "goods_items.caizhi",
            "goods_items.jinse",
            "goods_items.zhengshuleibie",
            "goods_items.zhengshuhao",
            "goods_items.zuanshidaxiao",
            "goods_items.zhushilishu",
            "goods_items.shoucun",
            "goods_items.jinzhong",
            "goods_items.product_type",
            "goods_items.cat_type",
            "goods_items.tuo_type",
            "goods_items.zhushi",
            "goods_items.zhushiyanse",
            "goods_items.zhushijingdu",
            "goods_items.zhushiqiegong",
            "goods_items.fushizhong",
            "goods_items.fushilishu",
        );

        return $this->table('erp_bill,erp_bill_goods,goods_items')->join("inner,left")->on("erp_bill.bill_id=erp_bill_goods.bill_id,erp_bill_goods.goods_itemid=goods_items.goods_id")->where($condition)->field($field)->order($order)->limit($limit)->page($pagesize)->select();
    }

    /**
     * 格式化 单据详情查询列表
     * @param unknown $data  数据
     */
    public function formatErpBillGoodsList($data,$type="list"){
        if(empty($data)){
            return $data;
        }

        /*保存的储位就是名称，没必要再查询
        $box_model = new erp_boxModel();
        $box_list = $box_model->getBoxList(array('erp_warehouse.is_enabled'=>1),'',10000);
        */
        $warehouse_model = new erp_warehouseModel();
        $goods_items_model = new goods_itemsModel();
        $store_model = new storeModel();
        $warehouse_list = $warehouse_model->getWareHouseAll(array('is_enabled'=>1),'house_id,name',5000);
        $warehouse_list = array_column($warehouse_list,'name','house_id');
        $warehouse_list = array_unique($warehouse_list);
        $company_list = $goods_items_model->getCompanyList("id,company_name");
        $company_list = array_column($company_list,'company_name','id');
        $store_list = $store_model->getStoreOnlineList(array(),0,'',"store_id,store_name");
        $store_list = array_column($store_list,'store_name','store_id');
        
        $data = $type=="list"?$data:array($data);
        foreach($data as &$vo){
            $vo['from_house_name'] = '';
            $vo['from_box_name'] = '';
            $vo['to_house_name'] = '';
            $vo['to_box_name'] = '';
            //出库仓
            if(!empty($vo['from_house_id']) && isset($warehouse_list[$vo['from_house_id']])){
                $vo['from_house_name'] = $warehouse_list[$vo['from_house_id']];
            }
            //出库储位
            /*保存的储位就是名称，没必要再查询
            if(!empty($vo['from_box_id']) && isset($box_list[$vo['from_box_id']])){
                $vo['from_box_name'] = $box_list[$vo['from_box_id']];
            }*/
            //入库仓
            if(!empty($vo['to_house_id']) && isset($warehouse_list[$vo['to_house_id']])){
                $vo['to_house_name'] = $warehouse_list[$vo['to_house_id']];
            }

            //出库公司
            if(!empty($vo['from_company_id']) && isset($company_list[$vo['from_company_id']])){
                $vo['from_company_name'] = $company_list[$vo['from_company_id']];
            }

            //出库门店
            if(!empty($vo['from_store_id']) && isset($store_list[$vo['from_store_id']])){
                $vo['from_store_name'] = $store_list[$vo['from_store_id']];
            }
            //入库公司
            if(!empty($vo['to_company_id']) && isset($company_list[$vo['to_company_id']])){
                $vo['to_company_name'] = $company_list[$vo['to_company_id']];
            }

            //入库门店
            if(!empty($vo['to_store_id']) && isset($store_list[$vo['to_store_id']])){
                $vo['to_store_name'] = $store_list[$vo['to_store_id']];
            }

            //入库 储位
            /*
            if(!empty($vo['to_box_id']) && isset($box_list[$vo['to_box_id']])){
                $vo['to_box_name'] = $box_list[$vo['to_box_id']];
            }*/
            if(!empty($vo['goods_data'])){
                $goods_data = @unserialize($vo['goods_data']);
                if(empty($goods_data)){
                    $goods_data = json_decode($vo['goods_data'],true);
                }
                //$goods_data = json_decode($vo['goods_data'],true);
                unset($goods_data['yuanshichengbenjia']);
                unset($goods_data['mingyichengben']);
                unset($goods_data['jijiachengben']);
                unset($goods_data['management_fee']);
                //unset($goods_data['goods_name']);
                $vo = array_merge($vo,$goods_data);
            }
            
            if($vo['bill_type']=="W"){                
                $vo['pandian_status_name'] = pandianStatus($vo['pandian_status']);
                $vo['pandian_adjust_name'] = pandianAjdust($vo['pandian_adjust']);
            }
            
        }
        return $type=="list"?$data:$data[0];
	}

    public function calc_l_bill_items($bill_id) {
    	$erp_bill_goods_table = DBPRE.'erp_bill_goods';
    	$erp_bill_table = DBPRE.'erp_bill';
    	$result = $this->query("SELECT goods_id, sum(current_kc_num) as kc_num from {$erp_bill_goods_table} g inner join {$erp_bill_table} b on b.bill_id = g.bill_id where b.bill_status = 2 and b.bill_id = {$bill_id} and b.bill_type ='L' group by goods_id ");
    	return $result;
    }
    
}    
?>