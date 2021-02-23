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

class boss_styleControl extends BaseCronControl {
    /**
     * 执行频率常量 1小时
     * @var int
     */
    const EXE_TIMES = 3600;

    private $_doc;
    private $_xs;
    private $_index;
    private $_search;
    private $_contract_item;
    private $_store_id;
    private $_index_type;
    private $_styles;

    /**
     * php ./crontab/index.php boss_style build add 178
     */
    public function indexOp() {
        exit('nothing to do！');
    }


    /**
     * 初始化对象
     */
    private function _ini_xs(){
        
        $this->_store_id = $_GET['store_id'];
        $this->_index_type = $_GET['index_type'];
        
        if (empty($this->_store_id) || !in_array($this->_index_type, ['add','update'])) {
            exit('参数非法');
        }
        
        require_once(BASE_DATA_PATH.'/api/xs/lib/XS.class.php');
        $this->_doc = new XSDocument();
        $this->_xs = new XS(C('fullindexer.appname').'_'.$this->_store_id);
        $this->_index = $this->_xs->index;
        $this->_search = $this->_xs->search;
        $this->_search->setCharset(CHARSET);
    }

    /**
     * php ./crontab/index.php boss_style build add|update 178
     */
    public function buildOp() {
        if (empty($_SERVER['argv'][4])) {
            exit('store_id丢失');
        }
        
        if (empty($_SERVER['argv'][3])) {
            exit('add? update?');
        } 
        
        $_GET['index_type'] = $_SERVER['argv'][3];
        $_GET['store_id'] = $_SERVER['argv'][4];
        
        $this->build_index();
    }


    public function webOp(){
        $_GET['index_type'] = 'add';
        $_GET['store_id'] = $_GET['store_id'];
        $this->build_index();
    }
    /**
     * php ./crontab/index.php boss_style refresh add|update KLRM0001,KLRW0002
     */
    public function refreshOp() {
        if (empty($_SERVER['argv'][4])) {
            exit('请指定款号，以逗号分隔');
        }
        
        $this->_styles = "'".implode("','", explode(",", $_SERVER['argv'][4]))."'";
        $_GET['index_type'] = empty($_SERVER['argv'][3]) ? 'update' : $_SERVER['argv'][3];

        $files = glob(BASE_DATA_PATH.'/api/xs/app/'.C('fullindexer.appname').'_*.ini');
        foreach ($files as $fi) {
            
            $store_char = explode('_', $fi);
            $store_char = end($store_char);
            
            if (empty($store_char)) continue;
            
            $_GET['store_id'] = substr($store_char, 0, strlen($store_char) - 4);
            echo $_GET['store_id'].PHP_EOL;
            $this->build_index();
        }
    }

    public function autorebuildOp() {
        $files = glob(BASE_DATA_PATH.'/api/xs/app/'.C('fullindexer.appname').'_*.ini');
        foreach ($files as $fi) {
            
            $store_char = explode('_', $fi);
            $store_char = end($store_char);
            
            if (empty($store_char)) continue;
            
            $_GET['store_id'] = substr($store_char, 0, strlen($store_char) - 4);
            $store_id = $_GET['store_id'];
            // 查找当前渠道是否有销售政策的更新, 如果时间大于之前build的时间，则需要更新一下索引
            $model = Model();
            $store_index_built = $model->table('store_cron_record')->where(['store_id'=>$store_id])->find();
            if(empty($store_index_built)){
                $this->build_index();
            }else{
                $sql = "select max(i.update_time) as time1, max(c.create_time) as time2, max(g.add_time) as time3 from app_salepolicy_channel c inner join base_salepolicy_info i on i.policy_id = c.policy_id and i.bsi_status = 3 and i.is_delete = 0
left join app_yikoujia_goods g on g.policy_id = i.policy_id
where c.channel = ".$store_id;
                $max_time_arr = $model->query($sql);
                if(!empty($max_time_arr[0])){
                    $max_time_arr = array_values($max_time_arr[0]);
                    $max_time = strtotime(max($max_time_arr));//取3个时间中最大的那个时间戳
                    if($max_time > $store_index_built['style_index_build_time']){
                        $this->build_index();
                    }
                }
            }
        }
    }

    /**
     * 全量创建索引
     */
    private function build_index($is_update=false) {
        if (!C('fullindexer.open')) return;
        
        $store_id = $_GET['store_id'];

        $model = Model();
        $store_exists = $model->query("select 1 from store where store_id=".$store_id);
        if (empty($store_exists)) {
            echo $store_id. ' not in store db.'.PHP_EOL;
            return;
        }

        /* 查看当前渠道是否有销售政策存在 */
        $have_sale_policy = $model->table('app_salepolicy_channel,store,base_salepolicy_info')->join('inner,inner')->on('app_salepolicy_channel.channel=store.store_id,app_salepolicy_channel.policy_id=app_salepolicy_channel.policy_id')->field('1')
                                  ->where(['base_salepolicy_info.bsi_status' => 3, 'base_salepolicy_info.is_delete' =>0, 'store.store_id' => $store_id])->count();
        if (!$have_sale_policy) {
            echo $store_id.' sale policy not found.'.PHP_EOL;
            //$this->log($store_id. ': no sale policy.');
            return;
        }

        /** 记录index更新时间 */
        $is_full_rebuild = $_GET['op'] == 'build' || $_GET['op'] == 'autorebuild';
        if ($is_full_rebuild) {
            $build_time = time();
            $store_index_built = $model->table('store_cron_record')->where(['store_id'=>$store_id])->count();
            if($store_index_built){
                $sql = "update store_cron_record set style_index_build_time = {$build_time} where store_id = ".$store_id;
            }else{
                $sql = "insert into store_cron_record (`store_id`,`style_index_build_time`) values({$store_id},{$build_time}) ";
            }
            $model->query($sql);
        }

        $this->_ini_xs();

        if ($is_full_rebuild) {
            $this->_index->openBuffer(2);
            $this->_index->beginRebuild();
        }

        try {
            //每次批量更新商品数
            $step_num = 100;
            $condition = ["list_style_goods.is_ok" => 1 ];

            //天生一对款式信息
            $tsyd_style_list=$this->get_tsyd_style_list();
            if($is_update) {
                $date=date("Y-m-d H:i:s",strtotime("-1 day"));
                $condition["base_style_info.modify_time"]=array("exp","base_style_info.modify_time>='{$date}'");
            }
            if (empty($this->_styles)) {
                $this->_styles = 'select style_sn from style_toindex';
            }
            $condition["base_style_info.style_sn"]=array("exp","base_style_info.style_sn in (".$this->_styles.")");
            $count=$model->table('list_style_goods,base_style_info,rel_style_stone')->join('inner,left')->on('list_style_goods.style_sn =base_style_info.style_sn,rel_style_stone.style_id=base_style_info.style_id and rel_style_stone.stone_position=1')->field('1')->where($condition)->count();
            echo 'Total:'.$count."\n";
            if ($count != 0) {
                for ($i = 0; $i <= $count; $i =$i+$step_num){
                    $goods_list=$model->table('list_style_goods,base_style_info,rel_style_stone')->join('inner,left')->on('list_style_goods.style_sn =base_style_info.style_sn,rel_style_stone.style_id=base_style_info.style_id and rel_style_stone.stone_position = 1')
                        ->field('list_style_goods.*,0 AS goods_price,IFNULL(base_style_info.goods_click, 0) AS goods_click,IFNULL(base_style_info.goods_salenum,0) AS goods_salenum,base_style_info.xilie,rel_style_stone.stone_position,rel_style_stone.stone_cat,rel_style_stone.shape,base_style_info.is_recommend,base_style_info.style_sex')
                        ->where($condition)->limit("{$i},{$step_num}")->select();
                    $this->_build_goods($tsyd_style_list,$goods_list);
                    echo $i." ok\n";
                }
            }

            if ($is_full_rebuild) {
                $this->_index->closeBuffer();
                $this->_index->endRebuild();
            }
            
        } catch (Exception $e) {
            $this->log($e->getMessage());

            if ($is_full_rebuild) {
                $this->_index->closeBuffer();
                $this->_index->endRebuild();
            }
        }
        
        $this->_index->flushIndex();
        $this->_index->flushLogging();
    }

    /**
     * 天生一对配对款信息
     * @return array
     */
    private function get_tsyd_style_list(){
        $model = Model();
        $sql=" SELECT DISTINCT style_sn1,style_sn2 from rel_style_lovers";
        $tsyd_list=$model->query($sql);
        $tsyd_style_list=array();
        foreach ($tsyd_list as $item){
            $tsyd_style_list[$item["style_sn1"]]=$item["style_sn2"];
            $tsyd_style_list[$item["style_sn2"]]=$item["style_sn1"];
        }
        return $tsyd_style_list;
    }

    /**
     * 索引商品数据
     * @param array $goods_list
     */
    private function _build_goods($tsyd_style_list,$goods_list = array()) {
        //整理需要索引的数据
        $policyGoodsModel = new app_salepolicy_goodsModel();

        foreach ($goods_list as $k => $v) {
            
            $price = $policyGoodsModel->getQihuoPrice($v, $this->_store_id);
            if ($price) {
                $index_data = [];
                //$index_data['unique_id'] = utf8_encode("{$this->_store_id}{$v['goods_id']}{$v['shoucun']}{$v['caizhi']}{$v['yanse']}{$v['stone_position']}{$v['stone_cat']}{$v['shape']}");
                $index_data['unique_id'] = utf8_encode("{$v['goods_id']}{$v['shoucun']}{$v['caizhi']}{$v['yanse']}{$v['stone_position']}{$v['stone_cat']}{$v['shape']}");
                $index_data['store_id'] = utf8_encode($this->_store_id);
                $index_data['goods_price'] = utf8_encode($price);
                $index_data['goods_id'] = utf8_encode($v['goods_id']);
                $index_data['product_type_id'] = utf8_encode($v['product_type_id']);
                $index_data['cat_type_id'] = utf8_encode($v['cat_type_id']);
                $index_data['style_sn'] = utf8_encode($v['style_sn']);
                $index_data['style_name'] = utf8_encode($v['style_name']);
                $index_data['goods_sn'] = utf8_encode($v['goods_sn']);
                $index_data['shoucun'] = utf8_encode($v['shoucun']);
                $index_data['xiangkou'] = utf8_encode($v['xiangkou']);
                $index_data['caizhi'] = utf8_encode($v['caizhi']);
                $index_data['yanse'] = utf8_encode($v['yanse']);
                $index_data['zhushizhong'] = utf8_encode($v['zhushizhong']);
                $index_data['zhushi_num'] = utf8_encode($v['zhushi_num']);
                $index_data['fushizhong1'] = utf8_encode($v['fushizhong1']);
                $index_data['fushi_num1'] = utf8_encode($v['fushi_num1']);
                $index_data['fushizhong2'] = utf8_encode($v['fushizhong2']);
                $index_data['fushi_num2'] = utf8_encode($v['fushi_num2']);
                $index_data['fushizhong3'] = utf8_encode($v['fushizhong3']);
                $index_data['fushi_num3'] = utf8_encode($v['fushi_num3']);
                $index_data['fushi_chengbenjia_other'] = utf8_encode($v['fushi_chengbenjia_other']);
                $index_data['jinzhong'] = utf8_encode($v['weight']);
                $index_data['jincha_shang'] = utf8_encode($v['jincha_shang']);
                $index_data['jincha_xia'] = utf8_encode($v['jincha_xia']);
                $index_data['dingzhichengben'] = utf8_encode($v['dingzhichengben']);
                $index_data['jincha_xia'] = utf8_encode($v['dingzhichengben']);
                $index_data['goods_salenum'] = utf8_encode($v['goods_salenum']);
                $index_data['goods_click'] = !empty($v['goods_click'])?$v['goods_click']:0;
                $index_data['goods_salenum'] = utf8_encode($v['goods_salenum']);
                $index_data['xilie'] = utf8_encode($v['xilie']);
                $index_data['style_id'] = utf8_encode($v['style_id']);
                $index_data['stone_position'] = utf8_encode($v['stone_position']);
                $index_data['stone_cat'] = utf8_encode($v['stone_cat']);
                $index_data['shape'] = utf8_encode($v['shape']);
                $index_data['goods_image'] = '';//utf8_encode($v['goods_image']);
                $index_data['is_recommend'] =  utf8_encode($v['is_recommend']);
                $index_data['tsyd_style_sn'] =isset($tsyd_style_list[$v['style_sn']])?utf8_encode($tsyd_style_list[$v['style_sn']]):"";
                $index_data['is_tsyd'] =!empty($index_data['tsyd_style_sn'])?1:0;
                $index_data['default'] = 1;
                $index_data['style_sex'] = utf8_encode($v['style_sex']);
                //echo $index_data['unique_id'].": ".$index_data['goods_price'].PHP_EOL;
                //添加到索引库
                $this->_doc->setFields($index_data);
                $action = $this->_index_type;
                $this->_index->$action($this->_doc);
            } else {
                file_put_contents(__DIR__.'/'.date('Y-m-d').'_noprice.err', $v['goods_id'].": ".($this->_store_id).PHP_EOL, FILE_APPEND);
            }
        }
    }
    

    private function build_store_price($ginfo, $store_id) {
        static $_price_cache = array();
        $data = array(
            //'id'=>$ginfo['goods_id'],
            'goods_id'=>$ginfo['goods_sn'],
            'goods_sn'=>$ginfo['style_sn'],
            'goods_name'=>$ginfo['style_name'],
            'xiangkou'=>$ginfo['xiangkou'],
            'finger'=>$ginfo['shoucun'],
            'caizhi'=>$ginfo['caizhi'],
            'product_type'=>$ginfo['product_type_id'],
            'cat_type'=>$ginfo['cat_type_id'],
            'stone'=>$ginfo['zhushizhong'],
            'mingyichengben'=>$ginfo['dingzhichengben'],
            //'tuo_type'=>2,
            //'is_chengpin'=>0,
            //'is_xianhuo'=>0,
        );
        
        $hash = md5(serialize($data));
        if (array_key_exists($hash, $_price_cache)) {
            return $_price_cache[$hash];
        }
        
        $policyGoodsModel = new app_salepolicy_goodsModel();
        $price = $policyGoodsModel->getQihuoPrice($ginfo, $store_id);        
        $_price_cache[$hash] = $price;
        return $price;
    }
}
