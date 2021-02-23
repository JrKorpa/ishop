<?php
/**
 * 任务计划 - 小时执行的任务
 *
 *
 * @提供技术支持 授权请购买正版授权
 * @license    http://官网
 * @link       交流群号：官网群
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');

class boss_diamondControl extends BaseCronControl {
    /**
     * 执行频率常量 1小时
     * @var int
     */
    const EXE_TIMES = 3600;

    private $_doc;
    private $_xs;
    private $_index;
    private $_search;

    /**
     * 默认方法
     */
    public function indexOp() {
        //更新全文搜索内容
        $this->_xs_update();
    }


    /**
     * 初始化对象
     */
    private function _ini_xs(){
        require(BASE_DATA_PATH.'/api/xs/lib/XS.class.php');
        $this->_doc = new XSDocument();
        $this->_xs = new XS(C('fullindexer.appname'));
        $this->_index = $this->_xs->index;
        $this->_search = $this->_xs->search;
        $this->_search->setCharset(CHARSET);
    }

    /**
     * 全量创建索引
     */
    public function xs_createOp() {
        if (!C('fullindexer.open')) return;
        $this->_ini_xs();
        $model = Model();
        try {
            //每次批量更新商品数
            $step_num = 100;
            $condition=array();
            $count=$model->table('diamond_info')->field('goods_id,
  goods_sn,goods_name,goods_number,from_ad,good_type,market_price,shop_price,member_price,chengben_jia, 
   carat,clarity,cut,color,shape,depth_lv,table_lv,symmetry,polish,fluorescence,warehouse,
  guojibaojia,cts,us_price_source,source_discount,cert,cert_id,gemx_zhengshu,status,add_time,is_active,
  kuan_sn,mo_sn,pifajia,pifajia_mode,img,is_hot,  
   CASE WHEN (gemx_zhengshu!="" OR cert ="HRD-S") THEN "gemx"
  WHEN (cert ="GIA") THEN "gia" WHEN (kuan_sn!="" AND cert="HRD-D") THEN "tsyd" ELSE "" END AS xilie')->where($condition)->count();
            echo 'Total:'.$count."\n";
            if ($count != 0) {
                for ($i = 0; $i <= $count; $i =$i+$step_num){
                    $diamond_list=$model->table('diamond_info')->field('goods_id,
  goods_sn,goods_name,goods_number,from_ad,good_type,market_price,shop_price,member_price,chengben_jia, 
   carat,clarity,cut,color,shape,depth_lv,table_lv,symmetry,polish,fluorescence,warehouse,
  guojibaojia,cts,us_price_source,source_discount,cert,cert_id,gemx_zhengshu,status,add_time,is_active,
  kuan_sn,mo_sn,pifajia,pifajia_mode,img,1 as is_hot,  
   CASE WHEN (gemx_zhengshu!="" OR cert ="HRD-S") THEN "gemx"
  WHEN (cert ="GIA") THEN "gia" WHEN (kuan_sn!="" AND cert="HRD-D") THEN "tsyd" ELSE "" END AS xilie')->limit("{$i},{$step_num}")->select();
                    $this->_build_goods($diamond_list);
                    echo $i." ok\n";
                    flush();
                    ob_flush();
                }
            }
            if ($count > 0) {
                sleep(2);
                $this->_index->flushIndex();
                sleep(2);
                $this->_index->flushLogging();
            }
        } catch (XSException $e) {
            $this->log($e->getMessage());
        }
    }

    /**
     * 更新增量索引
     */
    public function _xs_update() {
        if (!C('fullindexer.open')) return;
        $this->_ini_xs();
        $model = Model();
        try {
            //更新多长时间内的新增(编辑)商品信息，该时间一般与定时任务触发间隔时间一致,单位是秒,默认3600
            $step_time = self::EXE_TIMES + 60;
            //每次批量更新商品数
            $step_num = 100;

            $model_goods = Model('goods');
            $condition = array();
            $condition['goods_edittime'] = array('egt',TIMESTAMP-$step_time);
            if (C('dbdriver') == 'mysql') {
                $_field = "CONCAT(goods_commonid,',',color_id)";
                $_distinct = 'nc_distinct';
            } elseif (C('dbdriver') == 'oracle') {
                $_field = $_distinct = "goods_commonid||','||color_id";
            }
            $count = $model_goods->getGoodsOnlineCount($condition,"distinct ".$_field);
            echo 'Total:'.$count."\n";
            for ($i = 0; $i <= $count; $i = $i + $step_num){
                if (C('dbdriver') == 'mysql') {
                    $goods_list = $model_goods->getGoodsOnlineList($condition, '*,'.$_field.' nc_distinct', 0, '', "{$i},{$step_num}", $_distinct);
                } elseif (C('dbdriver') == 'oracle') {
                    //先查出所有goods_id,再使用in查询
                    $condition['goods_state']   = 1;
                    $condition['goods_verify']  = 1;
                    $goods_id_list =  $model->table('goods')->where($condition)->field('min(goods_id) as goods_id,'.$_field)->group($_field)->key('goods_id')->limit("{$i},{$step_num}")->select();
                    if ($goods_id_list) {
                        $condition1 = array('goods_id' => array('in',array_keys($goods_id_list)));
                        $goods_list = $model_goods->getGoodsOnlineList($condition1, '*', 0, '', '', false);
                    }
                }
                //通过commonid得到所有goods_id，然后删除全文索引中的goods_id内容
                $goods_commonid_array = array();
                foreach ($goods_list as $_v) {
                    $goods_commonid_array[] = $_v['goods_commonid'];
                }
                if ($goods_commonid_array) {
                    $condition1 = array('goods_commonid' => array('in',$goods_commonid_array));
                    $goods_list1 = $model_goods->getGoodsOnlineList($condition1, 'goods_id', 0, '', '', false);
                    if ($goods_list1) {
                        $goods_id_array = array();
                        foreach ($goods_list1 as $_v) {
                            $goods_id_array[] = $_v['goods_id'];
                        }
                        $this->_index->del($goods_id_array);
                    }
                }
                $this->_build_goods($goods_list);
                echo $i." ok\n";
                flush();
                ob_flush();
            }
            if ($count > 0) {
                sleep(2);
                $this->_index->flushIndex();
                sleep(2);
                $this->_index->flushLogging();
            }
        } catch (XSException $e) {
            $this->log($e->getMessage());
        }
    }

    /**
     * 索引商品数据
     * @param array $diamond_list
     */
    private function _build_goods($diamond_list = array()) {
        //整理需要索引的数据
        foreach ($diamond_list as $k => $v) {
            $index_data['goods_id'] = utf8_encode($v['goods_id']);
            $index_data['goods_sn'] = utf8_encode($v['goods_sn']);
			$index_data['goods_name'] =utf8_encode($v['goods_name']);
            $index_data['goods_number'] = utf8_encode($v['goods_number']);
            $index_data['from_ad'] = utf8_encode($v['from_ad']);
            $index_data['good_type'] = utf8_encode($v['good_type']);
            $index_data['market_price'] =!empty($v['market_price'])?utf8_encode($v['market_price']):0;
            $index_data['shop_price'] =!empty($v['shop_price'])?utf8_encode($v['shop_price']):0;
            $index_data['chengben_jia'] =!empty($v['chengben_jia'])?utf8_encode($v['chengben_jia']):0;
            $index_data['carat'] = utf8_encode($v['carat']);
            $index_data['clarity'] = utf8_encode($v['clarity']);
            $index_data['cut'] = utf8_encode($v['cut']);
            $index_data['color'] = utf8_encode($v['color']);
            $index_data['shape'] = utf8_encode($v['shape']);
            $index_data['depth_lv'] = utf8_encode($v['depth_lv']);
            $index_data['table_lv'] = utf8_encode($v['table_lv']);
            $index_data['symmetry'] = utf8_encode($v['symmetry']);
            $index_data['polish'] = utf8_encode($v['polish']);
            $index_data['fluorescence'] = utf8_encode($v['fluorescence']);
            $index_data['warehouse'] = utf8_encode($v['warehouse']);
            $index_data['guojibaojia'] =!empty($v['guojibaojia'])?utf8_encode($v['guojibaojia']):0;
            $index_data['cts'] = utf8_encode($v['cts']);
            $index_data['us_price_source'] =!empty($v['us_price_source'])?utf8_encode($v['us_price_source']):0;
            $index_data['source_discount'] = utf8_encode($v['source_discount']);
            $index_data['cert'] = utf8_encode($v['cert']);
            $index_data['cert_id'] = utf8_encode($v['cert_id']);
            $index_data['gemx_zhengshu'] = utf8_encode($v['gemx_zhengshu']);
            $index_data['status'] = utf8_encode($v['status']);
            $index_data['add_time'] = utf8_encode($v['add_time']);
            $index_data['is_active'] = utf8_encode($v['is_active']);
            $index_data['kuan_sn'] =utf8_encode($v['kuan_sn']);
            $index_data['mo_sn'] = utf8_encode($v['mo_sn']);
            $index_data['pifajia'] =!empty($v['pifajia'])?utf8_encode($v['pifajia']):0;
            $index_data['pifajia_mode'] = utf8_encode($v['pifajia_mode']);
            $index_data['img'] = utf8_encode($v['img']);
            $index_data['is_hot'] = utf8_encode($v['is_hot']);
            $index_data['xilie'] = utf8_encode($v['xilie']);
            //添加到索引库
             $this->_doc->setFields($index_data);
             $this->_index->update($this->_doc);

        }
    }

    public function xs_clearOp(){
        if (!C('fullindexer.open')) return;
        $this->_ini_xs();

        try {
            $this->_index->clean();
        } catch (XSException $e) {
            $this->log($e->getMessage());
        }
    }

    public function xs_flushLoggingOp(){
        if (!C('fullindexer.open')) return;
        $this->_ini_xs();
        try {
            $this->_index->flushLogging();
        } catch (XSException $e) {
            $this->log($e->getMessage());
        }
    }

    public function xs_flushIndexOp(){
        if (!C('fullindexer.open')) return;
        $this->_ini_xs();

        try {
            $this->_index->flushIndex();
        } catch (XSException $e) {
            $this->log($e->getMessage());
        }
    }
}
