<?php
require_once BASE_ROOT_PATH.'/data/repository/diamond.php';

class xs_diamond extends diamond  {

    /**
     * 查询裸钻列表
     * @page_size 每页显示条数
     * @page_index 第几页
     * @where array 检索条件
     **/
    public function get_diamond_list($page_size, $page_index, $where=[])
    {
        if(isset($where["certificate"])&&!empty($where["certificate"])) $where["cert"]=$where["certificate"];
        if(isset($where["polishing"])&&!empty($where["polishing"])) $where["polish"]=$where["polishing"];
        require_once BASE_ROOT_PATH .'/data/api/xs/search_diamond.php';
        $search_diamond_class=new search_diamond();
        $_REQUEST['curpage']=$page_index;
        list($diamond_list, $total_count)=$search_diamond_class->indexerSearch($where,$page_size);
        $page_count=($total_count%$page_size)==0?($total_count/$page_size):ceil($total_count/$page_size);
        $result=array('return_msg'=>array('data'=>$diamond_list,'recordCount'=>$total_count,'page_total'=>$page_count,'page'=>$page_index,'pageCount'=>$page_count,'isFirst'=>$page_index==1?1:0,'isLast'=>$page_index==$page_count?1:0,'pageSize'=>$page_size));
        return $result;
    }
}

