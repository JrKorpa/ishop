<?php
/**
 * 店铺库存查询
 *
 *
 *
 * *  (c) 2015-2018 . (http://www.kela.cn)
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 * @since      珂兰技术中心提供技术支持
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class goods_itemsModel extends Model {
        
    public function __construct(){
        parent::__construct('goods_items');
    }
    /**
     * 库存商品状态列表
     * @return multitype:string
     */
    public static function getGoodsItemStatusList(){
        return array(
            12=>"作废", 11=>"退货中",10=>"销售中",9=>"已返厂",8=>"返厂中",7=>"已报损",6=>"损益中",5=>"调拨中",4=>"盘点中",3=>"已销售",2=>"库存",1=>"收货中"
        );
    }
    /**
     * 入库
     * @return multitype:string
     */
    public static function getGoodsPutTypeList(){
        return array(
            1=>"购买",
            4=>"借入",
            5=>"自采"
        );
    }

    /**
     * 入库方式（针对退货返厂单）
     * @return multitype:string
     */
    public static function getGoodsPutTypeListBybillB(){
        return array(
            1=>"购买",
            2=>"委托加工",
            3=>"代销",
            4=>"借入",
            5=>"自采"
        );
    }
    /**
     * 获取库存商品状态名称
     * @return multitype:string
     */
    public static function getGoodsItemStatusName($code){
        $goodsItemStatusList = self::getGoodsItemStatusList();
        return isset($goodsItemStatusList[$code])?$goodsItemStatusList[$code]:$code;
    }
    /**
     * 库存列表
     * @param array $condition
     * @param string $field
     * @param int $page
     * @return array
     */
    public function getGoodsItemsList($condition, $field = null, $pagesize = null,$order='goods_items.id desc') {
         $field = !empty($field)?$field:"base_style_info.`xilie`,goods_items.*,(SELECT MIN(thumb_img) FROM app_style_gallery WHERE style_sn=goods_items.goods_sn) AS goods_image";
         $table = "goods_items,base_style_info";
         $join = "left";
         $on = "goods_items.`goods_sn`=base_style_info.`style_sn`";
        return $this->table($table)->join($join)->on($on)->field($field)->where($condition)->page($pagesize)->order($order)->select();
    }



    /**
     * 批量更新上下架
     * @param array $update
     * @param array $condition
     * @return boolean
     */
    public function editGoodsItems($data, $condition) {
        return $this->table('goods_items')->where($condition)->update($data);
    }

    /**
     * 构造查询条件
     *
     * @param array $condition 条件数组
     * @return string
     */
    private function getCondition($condition){
        $conditionStr   = '';
        if($condition['status'] != ''){
            $conditionStr   .= " and goods_items.status='{$condition['status']}' ";
        }
        if($condition['name'] != ''){
            $conditionStr   .= " and goods.goods_name like '%{$condition['status']}%' ";
        }
        if($condition['item_id'] != ''){
            $conditionStr   .= " and goods_items.item_id in('".implode("','", $condition['item_id'])."') ";
        }
        $conditionStr .= " and goods_items.store_id = ".$_SESSION['store_id'];
        return $conditionStr;
    }

    /**
     * 款式分类
     * @param $condition
     * @return mixed
     */
    public function getCartTypeList($field="*",$condition=array()){
        return $this->table('app_cat_type')->field($field)->page(9999)->where($condition)->select();
    }

    /**
     * 系列
     * @param $condition
     * @return mixed
     */
    public function getXiLieList($field="*",$condition=array()){
        return $this->table('app_style_xilie')->field($field)->page(9999)->where($condition)->select();
    }
    /**
     * 产品线
     * @param $condition
     * @return mixed
     */
    public function getProductTypeList($field="*",$condition=array()){
        return $this->table('app_product_type')->field($field)->page(9999)->where($condition)->select();
    }
    /**
     * 公司
     * @param $condition
     * @return mixed
     */
    public function getCompanyList($field="*",$condition=array()){
        return $this->table('company')->field($field)->page(9999)->where($condition)->select();
    }
    /**
     * 供应商
     * @param $condition
     * @return mixed
     */
    public function getSupplierList($field="*",$condition=array()){
        return $this->table('store_supplier')->field($field)->page(9999)->where($condition)->select();
    }

    /**
     * 仓库
     * @param $condition
     * @return mixed
     */
    public function getWarehouseList($field="*",$condition=array()){
        return $this->table('erp_warehouse')->field($field)->page(9999)->where($condition)->select();
    }
    /**
     * 柜位
     * @param $condition
     * @return mixed
     */
    public function getBoxList($field="*",$condition=array()){
        $field = empty($field)?"erp_box.*,erp_warehouse.name as house_name":$field;
        return $this->table("erp_box,erp_warehouse")->join("inner")->on('erp_warehouse.house_id=erp_box.house_id')->field($field)->where($condition)->select();
    }
    /**
     * 批发客户
     * @param $condition
     * @return mixed
     */
    public function getWholesaleList($field="*",$condition=array()){
        if(!empty($condition['sd_company_id'])){
            $sql = "SELECT {$field} from jxc_wholesale ws INNER JOIN company c on ws.sign_company=c.id where c.sd_company_id={$condition['sd_company_id']}";
            $res = $this->query($sql);
        }else{
            $res = $this->table('jxc_wholesale')->field($field)->page(9999)->where($condition)->select();
        }
        return $res;
    }

    /**
     * 批量获取单条库存明细
     *
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getGoodsItemList($condition, $field = null) {
        $field = !empty($field)?$field:"goods_items.*,goods.goods_name,goods.goods_sn,goods.goods_image";
        return $this->table('goods_items,goods')->join("left")->on("goods.goods_id=goods_items.goods_id")->field($field)->where($condition)->select();
    }


    /**
     * 获取单条库存明细
     *
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getGoodsItemInfo($condition, $field = "*") {
        //$field = !empty($field)?$field:"goods_items.*,goods.goods_name,goods.goods_sn,goods.goods_image";
        //return $this->table('goods_items,goods')->join("left")->on("goods.goods_id=goods_items.goods_id")->field($field)->where($condition)->find();
        return $this->table('goods_items')->field($field)->where($condition)->find();
    }
    
    /**
     * 获取sku可用的商品列表，并按仓库优先顺序排, key: item_id, value => item
     * @param unknown $goods_id
     * @return unknown
     */
    public function getGoodsItemsForK($goods_id) {
    	$field = "goods_items.*,erp_warehouse.level,goods.goods_sn";
    	return $this->table('goods_items,erp_warehouse,goods')->join("inner,inner")->on("goods_items.house_id=erp_warehouse.house_id,goods_items.goods_id=goods.goods_id")->field($field)->where(array(
    			'goods_items.goods_id' => $goods_id,
    			'current_kc_num' => array('gt', 0),
    			'goods_items.status' => 1,
    			'erp_warehouse.is_enabled' => 1
    	))->page(10000)->select(array('key' => 'item_id'));
    }

    public function set_rfid($item_id, $tid, $epc = null) {
        $condition['goods_items.item_id'] = $item_id;
        $item = $this->table('goods_items')->field('goods_items.item_id,goods_items.rfid_tid,goods_items.rfid_epc,version')->where($condition)->find();
        if (empty($item)) {
            return callback(false, '无法找到该数据');
        }

        if (empty($item['rfid_tid']) && empty($tid)) {
            return callback(false, 'tid为空时，不能设置自定义数据');
        }

        $condition['goods_items.version'] = $item['version'];

        // 1. 新货需要设置tid; 2. 更换RFID标签时
        if (((empty($item['rfid_tid']) && empty($item['rfid_epc'])) || (!empty($item['rfid_tid']) && !empty($item['rfid_epc']))) && !empty($tid)) {
            $data['rfid_tid'] = $tid;
        }

        if (!empty($epc)) {
            $data['rfid_epc'] = $epc;
        }

        if (!isset($data)) {
            return callback(false, '数据异常'); 
        }
        
        $item_2 = $this->get_by_rfid($tid);
        if (!empty($item_2) && $item_2['item_id'] != $item_id) {
            return callback(false, "该标签已与货号 {$item_2['item_id']} 绑定"); 
        }
        return $this->editGoodsItems($data, $condition);
    }

    public function unset_rfid($tid) {
        $condition['goods_items.rfid_tid'] = $tid;
        
        $data['rfid_tid'] = null;
        $data['rfid_epc'] = null;

        return $this->editGoodsItems($data, $condition);
    }

    public function get_by_rfid($tid) {
        $condition['goods_items.rfid_tid'] = $tid;
        $item = $this->table('goods_items')->field('goods_items.item_id,goods_items.rfid_tid,goods_items.rfid_epc')->where($condition)->find();
        
        return $item;
    }   
}
