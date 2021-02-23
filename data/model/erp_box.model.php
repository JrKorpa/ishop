<?php
/**
 * 店铺柜位管理
 *
 *
 *
 * *  (c) 2015-2018 . (http://www.kela.cn)
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 * @since      珂兰技术中心提供技术支持
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class erp_boxModel extends Model {
    public function __construct(){
        parent::__construct('erp_box');
    }

    /**
     * 获取储位
     * @param int $house_id
     * @return boolean
     */
    public function getBoxList($condition,$field='',$limit=1000){
        $field = empty($field)?"erp_box.*,erp_warehouse.name as house_name":$field;
        return $this->table("erp_box,erp_warehouse")->join("inner")->on('erp_warehouse.house_id=erp_box.house_id')->field($field)->where($condition)->limit($limit)->select();
    }
    
    public function getBoxInfo($condition,$field="*"){
        return $this->table("erp_box")->field($field)->where($condition)->find();
    }
    public function delBox($condition){
        return $this->table('erp_box')->where($condition)->delete();
    }

    /**
     * 添加柜位
     * @param unknown $insert
     * @return boolean
     */
    public function addBox($insert) {
        return $this->table('erp_box')->insert($insert);
    }
}