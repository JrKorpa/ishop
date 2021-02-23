<?php
/**
 * 公司
 *
 * @提供技术支持 授权请购买正版授权
 * @license    http://官网
 * @link       交流群号：官网群
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class app_attributeModel extends Model{

    public function __construct(){
        parent::__construct('app_attribute');
    }

    /**
     * 读取列表
     * @param array $condition
     *
     */
    public function getAttributeList($condition,$page=null,$order='',$field='*',$limit=''){
        $result = $this->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
        return $result;
    }

    /**
     * 读取单条记录
     * @param array $condition
     *
     */
    public function getAttributeInfo($condition,$order=''){
        $result = $this->where($condition)->order($order)->find();
        return $result;
    }



}
