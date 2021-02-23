<?php
/**
 * 对戒搜索
 *
 *
 *
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */
//use Log;


class search_couple{

    //是否开启分面搜索
    private $_open_face = false;

    //全文搜索对象
    private $_xs_search;

    //全文搜索对象
    private $_xs_index;

    //全文搜索到的商品数组
    private $_goods_list = array();

    //全文搜索到的品牌数组
    private $_indexer_brands = array();

    //全文检索到的商品分类数组
    private $_indexer_cates = array();

    //全文搜索结果总数
    private $_indexer_count;

    //搜索结果中品牌分面信息
    private $_face_brand = array();

    //搜索结果中品牌分面信息
    private $_face_attr = array();

    private $_app_name="goods";

    private $_file_name="";

    private $_collapse="style_sn";

    public function __construct(){
        $this->_file_name=str_replace('\\','/',dirname(__FILE__)).'/lib/XS.class.php';
    }



    /**
     * 从全文索引库搜索关键词
     * @param unknown $condition 条件
     * @param unknown $order 排序
     * @param number $pagesize 每页显示商品数
     * @return
     */
    public function getIndexerList($condition = array(), $order = array(),$pagesize = 20) {
        try {
            //全文搜索初始化
            $this->_createXS($pagesize);
            //设置搜索内容
            $this->_setQueryXS($condition,$order);
            //执行搜索
            $this->_searchXS($pagesize);
            return array($this->_goods_list, $this->_indexer_count);
        } catch (XSException $e) {
            if (C('debug')) {
                showMessage('全文搜索出现异常','','html','error');
            } else {
                Log::record('search\index'.$e->getMessage()."\r\n",Log::ERR);
                return false;
            }
        }
    }

    /**
     * 设置全文检索查询条件
     * @param unknown $condition
     * @param array $order
     */
    private function _setQueryXS($condition,$order) {
        //$condition['style_name'] = preg_replace("/([a-zA-Z0-9]{2,})/",' ${1} ',$condition['style_name']);
        //$this->_xs_search->setQuery(is_null($condition['style_name']) ? '':$condition['style_name']);
        if (isset($condition['style_name'])&&!empty($condition['style_name'])) {
            $this->_xs_search->addQueryString('style_name'.':"'.$condition['style_name'].'"',XS_CMD_QUERY_OP_OR);
            $this->_xs_search->addQueryString('style_sn'.':"'.$condition['style_name'].'"',XS_CMD_QUERY_OP_OR);
        }else{
            if (isset($condition['goods_sn'])&&!empty($condition['goods_sn'])) {
                if(is_array($condition['goods_sn'])){
                    $this->addMultipleQueryString($condition,"goods_sn");
                }else{
                    $this->_xs_search->addQueryString('goods_sn'.':'.$condition['goods_sn']);
                }
            }
            if (isset($condition['style_sn'])&&!empty($condition['style_sn'])) {
                if(is_array($condition['style_sn'])){
                    $this->addMultipleQueryString($condition,"style_sn");
                }else{
                    $this->_xs_search->addQueryString('style_sn'.':'.$condition['style_sn']);
                }
            }
            if (isset($condition['cat_type_id'])&&!empty($condition['cat_type_id'])) {
                if(is_array($condition['cat_type_id'])){
                    $this->addMultipleQueryString($condition,"cat_type_id");
                }else{
                    $this->_xs_search->addQueryString('cat_type_id'.':'.$condition['cat_type_id']);
                }
            }
            if (isset($condition['product_type_id'])&&!empty($condition['product_type_id'])) {
                if(is_array($condition['product_type_id'])){
                    $this->addMultipleQueryString($condition,"product_type_id");
                }else{
                    $this->_xs_search->addQueryString('product_type_id'.':'.$condition['product_type_id']);
                }
            }

            if (isset($condition['xilie'])&&!empty($condition['xilie'])) {
                if(is_array($condition['xilie'])){
                    $this->addMultipleQueryString($condition,"xilie");
                }else{
                    $this->_xs_search->addQueryString('xilie'.':'.$condition['xilie']);
                }
            }
            if (isset($condition['caizhi'])&&!empty($condition['caizhi'])) {
                if(is_array($condition['caizhi'])){
                    $this->addMultipleQueryString($condition,"caizhi");
                }else{
                    $this->_xs_search->addQueryString('caizhi'.':'.$condition['caizhi']);
                }
            }
            if (isset($condition['yanse'])&&!empty($condition['yanse'])) {
                if(is_array($condition['yanse'])){
                    $this->addMultipleQueryString($condition,"yanse");
                }else{
                    $this->_xs_search->addQueryString('yanse'.':'.$condition['yanse']);
                }
            }

            if (isset($condition['shape'])&&!empty($condition['shape'])) {
                if(is_array($condition['shape'])){
                    $this->addMultipleQueryString($condition,"shape");
                }else{
                    $this->_xs_search->addQueryString('shape'.':'.$condition['shape']);
                }
            }
            if (isset($condition['xiangkou'])&&!empty($condition['xiangkou'])) {
                if(is_array($condition['xiangkou'])){
                    $this->addMultipleQueryString($condition,"xiangkou");
                }else{
                    $this->_xs_search->addQueryString('xiangkou'.':'.$condition['xiangkou']);
                }
            }
            if (isset($condition['shoucun_min'])&&!empty($condition['shoucun_min'])) {
                $this->_xs_search->addRange('shoucun',$condition['shoucun_min'],null);
            }
            if ( (isset($condition['price_min'])&&!empty($condition['price_min'])) || (isset($condition['price_max'])&&!empty($condition['price_max'])) ) {
                $this->_xs_search->addRange('dingzhichengben',$condition['price_min'],$condition['price_max']);
            }
            if((isset($condition['carat_min'])&&!empty($condition['carat_min']))&&(isset($condition['carat_max'])&&!empty($condition['carat_max']))) {
                $this->_xs_search->addRange('zhushizhong',sprintf('%.3f',$condition['carat_min']),sprintf('%.3f',$condition['carat_max']));
            }else if (isset($condition['carat_min'])&&!is_null($condition['carat_min'])&&$condition['carat_min']>0) {
                $this->_xs_search->addRange('zhushizhong',sprintf('%.3f',$condition['carat_min']),null);
            }else if(isset($condition['carat_max'])&&($condition['carat_max']=="0"||$condition['carat_max']>0)){
                $this->_xs_search->addRange('zhushizhong',null,sprintf('%.3f',$condition['carat_max']));
            }
        }
        if (isset($condition['group_by'])&&!empty($condition['group_by'])) {
            $this->_collapse=$condition['group_by'];
        }
       if (count($order) > 1) {
          $this->_xs_search->setMultiSort($order);
       } else {
          $this->_xs_search->setSort($order);
       }
       //print_r( $this->_xs_search);
       //print_r( $this->_xs_search->getQuery());
    }

    private function addMultipleQueryString($condition,$key){
        $query="";
        foreach ($condition[$key] as $value){
            if($key=='xiangkou') $value=strval(sprintf('%.2f',$value));
            $query.="{$key}:{$value} OR ";
        }
        $this->_xs_search->addQueryString(rtrim($query,"OR "));
    }

    /**
     * 创建全文搜索对象，并初始化基本参数
     * @param number $pagesize 每页显示商品数
     * @param string $appname 全文搜索INI配置文件名
     */
    private function _createXS($pagesize) {
        if (is_numeric($_REQUEST['curpage']) && $_REQUEST['curpage'] > 0) {
            $curpage = intval($_REQUEST['curpage']);
            $start =  ($curpage-1) * $pagesize;
        } else {
            $start = null;
        }
        require_once($this->_file_name);
        $obj_doc = new XSDocument();
        $obj_xs = new XS($this->_app_name);
        $this->_xs_search = $obj_xs->search;
        $this->_xs_index = $obj_xs->index;
        $this->_xs_search->setCharset(CHARSET);
        $this->_xs_search->setLimit($pagesize,$start);
        //设置分面
       /* if ($this->_open_face) {
            $this->_xs_search->setFacets(array('brand_id','attr_id'));
        }*/
    }

    /**
     * 执行全文搜索
     */
    private function _searchXS($page_size){
        $start = null;
        if (is_numeric($_REQUEST['curpage']) && $_REQUEST['curpage'] > 0) {
            $curpage = intval($_REQUEST['curpage']);
            $start =  ($curpage-1) * $page_size;
        }
        $this->_xs_search->setCollapse($this->_collapse,1)->search();
        $count = $this->_xs_search->setCollapse($this->_collapse,1)->getLastCount();
        //解决总条数(getLastCount()/count())统计不准确问题
        $total_page = ceil($count/$page_size);
        $begin = ($total_page-1)*$page_size;
        $this->_xs_search->setCollapse($this->_collapse,1)->setLimit($page_size,$begin);
        $this->_xs_search->setCollapse($this->_collapse,1)->search();
        $count = $this->_xs_search->setCollapse($this->_collapse,1)->getLastCount();
        $this->_indexer_count = $count;
        //查询
        $this->_xs_search->setCollapse($this->_collapse,1)->setLimit($page_size,$start);
        $docs=$this->_xs_search->setCollapse($this->_collapse,1)->search();
        //print_r($this->_xs_search->getQuery());
        //print_r($count);
        $goods_list = array();
        foreach ($docs as $index=>$doc) {
            foreach (array('unique_id','goods_id','goods_image','product_type_id','cat_type_id','style_sn','style_name','goods_sn','shoucun',
                    'xiangkou','caizhi','yanse','zhushizhong','zhushi_num','fushizhong1','fushi_num1',
                    'fushizhong2','fushi_num2','fushizhong3','fushi_num3','fushi_chengbenjia_other','jinzhong','jincha_shang',
                    'jincha_xia','dingzhichengben','goods_price','goods_salenum','goods_click','goods_price','xilie',
                    'style_id','stone_position','stone_cat','shape') as $v) {
                $goods_list[$index][$v] =utf8_decode($doc->$v);
            }
        }
        $this->_goods_list = $goods_list;
        //读取分面结果
       /* if ($this->_open_face) {
            $this->_face_brand = $this->_xs_search->getFacets('brand_id');
            $this->_face_attr = $this->_xs_search->getFacets('attr_id');
        }*/
        return true;
    }


    public function __get($key) {
        return $this->$key;
    }

    /**
     * 全文搜索
     * @return array 商品列表，搜索结果总数
     */
    public function indexerSearch($condition = array(), $page_size) {
        //if (!C('fullindexer.open')) return array(null,null);
        //拼接排序(销量,浏览量,价格)
        $order = array('dingzhichengben' => true);
        if(isset($condition["order_by"])&&!empty($condition["order_by"])){
            list($sort_key,$sort_order)=explode("|",$condition["order_by"]);
            if (in_array($sort_key,array('1','2','3','4','5'))) {
                $order = array(str_replace(array('1','2','3','4','5'), array('goods_salenum','goods_click','style_id','goods_salenum','dingzhichengben'),$sort_key)
                => $sort_order == '1' ? true : false);
            }
        }
        //取得商品主键等信息
        $result = $this->getIndexerList($condition,$order,$page_size);
        if ($result !== false) {
            list($goods_list,$indexer_count) = $result;
            //如果全文搜索发生错误，后面会再执行数据库搜索
        } else {
            $goods_list = null;
            $indexer_count = null;
        }
        return array($goods_list,$indexer_count);
    }

    public function autoComplete($get) {
        if ($get['term'] == '' && cookie('his_sh') != '') {
            $corrected = explode('~', cookie('his_sh'));
            if ($corrected != '' && count($corrected) !== 0) {
                $data = array();
                foreach ($corrected as $word)
                {
                    $row['id'] = $word;
                    $row['label'] = $word;
                    $row['value'] = $word;
                    $data[] = $row;
                }
                return $data;
            }
            return array();
        }
        //if (!C('fullindexer.open')) return array();
        try {
            require($this->_file_name);
            $obj_doc = new XSDocument();
            $obj_xs = new XS($this->_app_name);
            $obj_index = $obj_xs->index;
            $obj_search = $obj_xs->search;
            $obj_search->setCharset(CHARSET);
            $corrected = $obj_search->getExpandedQuery($get['term']);
            if (count($corrected) !== 0) {
                $data = array();
                foreach ($corrected as $word)
                {
                    $row['id'] = $word;
                    $row['label'] = $word;
                    $row['value'] = $word;
                    $data[] = $row;
                }
                return $data;
            }
        } catch (XSException $e) {
            if (is_object($obj_index)) {
                $obj_index->flushIndex();
            }
        }
        return array();
    }
}
