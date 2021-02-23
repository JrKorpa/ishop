<?php
/**
 * 款式库管理
 *
 *
 *
 *
 * @提供技术支持 授权请购买正版授权
 * @license    http://官网
 * @link       交流群号：官网群
 */



defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class styleControl extends SystemControl{

    public function __construct(){
        parent::__construct();
    }
    /**
     * 默认列表
     */
    public function indexOp(){    	
		Tpl::setDirquna('shop');
		Tpl::output('list_type',"index");
        Tpl::showpage('style.index');
    }
    /**
     * 推荐列表
     */
    public function recommend_listOp(){
        Tpl::setDirquna('shop');
        Tpl::output('list_url',"index.php?act=style&op=get_xml&is_recommend=1");
        Tpl::output('list_type',"recommend");
        Tpl::showpage('style.index');
    }
    public function get_xmlOp(){

        $style_model = new base_style_infoModel();//Model('base_style_info');
        $condition  = array();
        $sort_fields = array('style_id');        
        if ($_POST['sortorder'] != '' && in_array($_POST['sortname'],$sort_fields)) {
            $order = $_POST['sortname'].' '.$_POST['sortorder'];
        }
        if($_GET['is_recommend']==1){
            $condition['is_recommend'] = 1;
        }
        if ($_POST['query'] != '' && in_array($_POST['qtype'],array('style_sn','style_name'))) {
            $condition[$_POST['qtype']] = array('like',"%{$_POST['query']}%");
        }
        
        $page = $_POST['rp'];
        $total_num = $style_model->getStyleCount($condition);
        $style_list = $style_model->getStyleList($condition,"*",$page,$order);
        $data = array();
        $data['now_page'] = $style_model->shownowpage();
        $data['total_num'] = $total_num;
        foreach ($style_list as $v) {
            $list = array();
            $operation = '';
            if ($v['is_recommend']) {
                $operation .= "<a class='btn red' href=\"javascript:void(0);\" onclick=\"fg_unrecommend({$v['style_id']})\"><i class='fa'></i>取消推荐</a>";
            }else{
                $operation .= "<a class='btn red' href=\"javascript:void(0);\" onclick=\"fg_recommend({$v['style_id']})\"><i class='fa'></i>推荐</a>";
            }
            $list['operation'] = $operation;
            $list['style_sn'] = $v['style_sn'];//款号
            $list['style_name'] = $v['style_name'];//款式名称
            $list['product_type'] = $style_model->getProductTypeName($v['product_type']);//产品线
            $list['style_type'] = $style_model->getStyleTypeName($v['style_type']);//款式分类
            $list['style_sex'] = paramsHelper::echoOptionText("style_sex",$v['style_sex']);//款式性别
            $list['is_recommend'] = paramsHelper::echoOptionText("confirm",$v['is_recommend']);//是否推荐
            $list['is_made'] = paramsHelper::echoOptionText("confirm",$v['is_made']);//是否定制
            $list['goods_salenum'] = $v['goods_salenum'];//畅销量
            $list['goods_click'] = $v['goods_click'];//人气
            $list['check_status'] = paramsHelper::echoOptionText("style_check",$v['check_status']);//审核状态
            $list['create_time'] = $v['create_time'];//添加时间
            $list['modify_time'] = $v['modify_time'];//更新时间
            $data['list'][$v['style_id']] = $list;
        }
        exit(Tpl::flexigridXML($data));
    }  
    /**
     * 款式推荐
     */
    function recommendOp(){
         $id = $_REQUEST['id']; 
         if(is_array($id)){
             $where = array('style_id'=>array('in',$id));
         }else if($id>0){
             $where = array('style_id'=>$id);
         }else{
             $result = callback(false,"参数错误");
         }
         $style_model = new base_style_infoModel();         
         $res = $style_model->editStyle(array('is_recommend'=>1),$where);
        
         if($res !== false){
             $result = callback(true);             
         }else{
             $result = callback(false,"操作失败");
         }
         $style_api = data_gateway('istyle');
         $res = $style_api->update_style_info(array('is_recommend'=>1),array('style_id'=>$id));
         echo json_encode($result);
    }
    /**
     * 取消款式推荐
     */
    function canncel_recommendOp(){
        
        $id = $_REQUEST['id'];
        
        if(is_array($id)){
            $where = array('style_id'=>array('in',$id));
        }else if($id>0){
            $where = array('style_id'=>$id);
        }else{
             $result = callback(false,"参数错误");
        }
        
        $style_model = new base_style_infoModel();
        $res = $style_model->editStyle(array('is_recommend'=>0),$where);
        if($res !==false){
            $result = callback(true);
        }else{
            $result = callback(false,"操作失败");
        }
        $style_api = data_gateway('istyle');
        $style_api->update_style_info(array('is_recommend'=>0),array('style_id'=>$id));
        echo json_encode($result);
    
    }

}
