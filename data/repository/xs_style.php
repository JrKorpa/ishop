<?php
require_once BASE_ROOT_PATH.'/data/repository/style.php';

class xs_style extends style  {
    /**
     * 搜索款式信息
     *
     */
    public function get_style_list($where,$page,$page_size) {
        require_once BASE_ROOT_PATH .'/data/api/xs/search_style.php';
        $search_style_class=new search_style();
        list($goods_list, $total_count)=$search_style_class->indexerSearch($where,$page_size);
        $page_count=($total_count%$page_size)==0?($total_count/$page_size):ceil($total_count/$page_size);
        $result=array('return_msg'=>array('data'=>$goods_list,'recordCount'=>$total_count,'page_total'=>$page_count,'page'=>$page,'pageCount'=>$page_count,'isFirst'=>$page==1?1:0,'isLast'=>$page==$page_count?1:0,'pageSize'=>$page_size));
        $result = $this->build_style_goods_image_list($result);
        return $result;
    }

    /**
     * 款式商品列表
     * @see istyle::get_goods_list()
     */
    public function get_style_goods_list($where,$page,$page_size) {
        require_once BASE_ROOT_PATH .'/data/api/xs/search_style.php';
        $search_style_class=new search_style();
        $where["default"]=1; //查询全部必须有查询条件，索引库中all全部为1
        //$where["stone_cat"]=array(1,2);
        list($goods_list, $total_count)=$search_style_class->indexerSearch($where,$page_size);
        $page_count=($total_count%$page_size)==0?($total_count/$page_size):ceil($total_count/$page_size);
        $result = array('return_msg'=>array('data'=>$goods_list,'recordCount'=>$total_count,'page_total'=>$page_count,'page'=>$page,'pageCount'=>$page_count,'isFirst'=>$page==1?1:0,'isLast'=>$page==$page_count?1:0,'pageSize'=>$page_size));
        $result = $this->build_style_goods_image_list($result);
        return $result;
    }

    /**
     * 对戒婚戒商品列表
     * @see istyle::get_goods_list()
     */
     public function get_couple_marry_goods_list($where,$page,$page_size) {
        require_once BASE_ROOT_PATH .'/data/api/xs/search_couple.php';
        $search_style_class=new search_couple();
        $this->convertParams($where);
        $_GET["curpage"]=$page;
        list($goods_list, $total_count)=$search_style_class->indexerSearch($where,$page_size);
        $page_count=($total_count%$page_size)==0?($total_count/$page_size):ceil($total_count/$page_size);
        $result=array('return_msg'=>array('data'=>$goods_list,'recordCount'=>$total_count,'page_total'=>$page_count,'page'=>$page,'pageCount'=>$page_count,'isFirst'=>$page==1?1:0,'isLast'=>$page==$page_count?1:0,'pageSize'=>$page_size));
        $result = $this->build_style_goods_image_list($result);
        return $result;
     }

    private function convertParams(&$where){
        $where["is_made"]=1;
        if(isset($where["cat_type"])&&!empty($where["cat_type"])) $where["cat_type_id"]=$where["cat_type"];
        if(isset($where["pick_xilie"])&&!empty($where["pick_xilie"])) $where["xilie"]=$where["pick_xilie"];
        if(isset($where["cart"])&&!empty($where["cart"])){
            $where["cart_min"]=$where["cart"][0];
            $where["cart_max"]=$where["cart"][1];
        }
        if(isset($where["price"])&&!empty($where["price"])&&(empty($where["minprice"])||empty($where["maxprice"]))){
            $where["price_min"]=$where["price"][0];
            $where["price_max"]=$where["price"][1];
        }
        if(isset($where["minprice"])&&!empty($where["minprice"])){
            $where["price_min"]=$where["minprice"];
        }
        if(isset($where["maxprice"])&&!empty($where["maxprice"])){
            $where["price_max"]=$where["maxprice"];
        }
        if(isset($where["caizhi"])&&!empty($where["caizhi"])) $where["caizhi"]=str_replace(array("18K","PT950"),array(1,2),$where["caizhi"]);
        unset($where["cart"]);
        unset($where["price"]);
        unset($where["pick_xilie"]);
        unset($where["cat_type"]);
    }
}

