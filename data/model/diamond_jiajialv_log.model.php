<?php
/**
 * 店铺模型管理
 *
 *
 *
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class diamond_jiajialv_logModel extends Model {
    public function __construct(){
        parent::__construct('diamond_jiajialv_log');
    }

    /**
     * 裸钻加价率日志列表
     * @param array $condition
     * @param string $field
     * @param int $page
     * @return array
     */
    public function getLogList($condition, $field = '*', $page = 0, $order = '', $limit = ''){
        return $this->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }


    /**
     * 添加裸钻加价率日志
     * @param unknown $insert
     * @return boolean
     */
    public function addLog($insert) {
        return $this->insert($insert);
    }
    
    public function getDiffRemark($newdo,$olddo){
        $key_map = array(
            'cert'=>'证书类型',
            'carat_min'=>'最小钻重',
            'carat_max'=>'最大钻重',
            'goods_type'=>'货品类型',
            'jiajialv'=>'加价率',
            'status'=>'状态',
        );
        $str = '修改操作：';
        foreach ($key_map as $k=>$kname){
             if(isset($newdo[$k]) && isset($olddo[$k])){                 
                  if($newdo[$k]!=$olddo[$k]){  
                      $newVal = $newdo[$k];
                      $oldVal = $olddo[$k];
                      if($k=="goods_type")  {
                          $newVal = $newVal==1?"现货":"期货";
                          $oldVal = $oldVal==1?"现货":"期货";
                      }else if($k=="status"){
                          $newVal = $newVal==1?"启用":"停用";
                          $oldVal = $oldVal==1?"启用":"停用";
                      }
                      $str .= "{$kname}：{$newVal}=>{$oldVal}；";
                  }                 
             }
        }
        return $str;        
    }

}
