<?php
/**
 * 裸钻管理
 *
 *
 *
 *
 * @提供技术支持 授权请购买正版授权
 * @license    http://官网
 * @link       交流群号：官网群
 */



defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class diamondControl extends SystemControl {
    const EXPORT_SIZE = 5000;
    public $goods_type = array(1=>'现货', 2=>'期货');
    public $status = array(1=>'上架',2=>'下架');

    public function __construct(){
        parent::__construct();
        Language::read('diamond');
    }

    public function indexOp() {
        $this->diamondOp();
    }

    /**
     * 裸钻管理
     */
    public function diamondOp(){
		Tpl::setDirquna('shop');
        Tpl::showpage('diamond.index');
    }

    /**
     * 输出XML数据
     */
    public function get_xmlOp() {
        // 设置页码参数名称
        $condition = array();
        //'not_from_ad' => array('11','17'), //直营店期货(kgk+enjoy除外)
        //'warehouse' => array('HPLZK', 'COM'), 
        $condition['not_from_ad'] = array('11','17');
        $condition['warehouse'] = array('HPLZK', 'COM');
        if ($_POST['query'] != '') {
            if($_POST['qtype'] == 'status'){
                $status_arr = array_flip($this->status);
                $_POST['query'] = $status_arr[$_POST['query']];
            }
            if(in_array($_POST['qtype'],array('status'))){
                $condition[$_POST['qtype']] = $_POST['query'];
            }else{
                $condition[$_POST['qtype']] = array($_POST['query']);
            }
        }
        $order = '';
        $param = array('shop_price');
        if (in_array($_POST['sortname'], $param) && in_array($_POST['sortorder'], array('asc', 'desc'))) {
            //$condition['order'] = $_POST['sortname'] . ' ' . $_POST['sortorder'];
            $condition['pricesort'] = $_POST['sortorder'];
        }
        $rp = isset($_POST['rp'])?$_POST['rp']:15;
        $curpage = isset($_POST['curpage'])?$_POST['curpage']:1;
        $diamond_api = data_gateway('idiamond');
        $shape_all = $diamond_api->get_diamond_index(array('shape_all'));
        $shape_all = isset($shape_all['return_msg'])?$shape_all['return_msg']:array();
        $byApi = $diamond_api->get_diamond_list($rp, $curpage, $condition);
        $diamond_list = isset($byApi['return_msg']['data'])?$byApi['return_msg']['data']:array();
        $store_id   = $this->member_info['store_id'];
        $company_id = $this->store_info['store_company_id'];
        $diamond_api->multiply_jiajialv($diamond_list, $store_id, $company_id);
        $curlist = isset($byApi['return_msg'])?$byApi['return_msg']:array();
        $page   = new Page();
        $page->setEachNum($rp);
        $page->setStyle('admin');
        //店铺列表
        //$spec_list = Model('spec')->specList($condition, $page);

        $data = array();
        $data['now_page'] = $curlist['page'];
        $data['total_num'] = $curlist['recordCount'];
        foreach ((array)$diamond_list as $value) {
            $param = array();
            $operation = '';
            //$operation .= "<a class='btn red' href='javascript:void(0);' onclick='fg_del(". $value['cert_id'] .")'><i class='fa fa-trash-o'></i>删除</a>";
            $operation .= "<a class='btn blue' href='index.php?act=diamond&op=diamond_edit&cert_id=".$value['cert_id']."'><i class='fa fa-pencil-square-o'></i>编辑</a>";
            $param['operation'] = $operation;
            $param['shape'] = $shape_all['shape_all'][$value['shape']];
            $param['carat'] = $value['carat'];
            $param['color'] = $value['color'];
            $param['clarity'] = $value['clarity'];
            $param['cut'] = $value['cut'];
            $param['symmetry'] = $value['symmetry'];
            $param['polish'] = $value['polish'];
            $param['fluorescence'] = $value['fluorescence'];
            $param['shop_price'] = $value['shop_price'];
            $param['cert'] = $value['cert'];
            $param['cert_id'] = $value['cert_id'];
            $param['good_type'] = $this->goods_type[$value['good_type']];
            $param['status'] = $this->status[$value['status']];
            $data['list'][$value['cert_id']] = $param;
        }
        echo Tpl::flexigridXML($data);exit();
    }

    /**
     * 添加规格
     */
    public function spec_addOp(){
        $lang   = Language::getLangContent();
        $model_spec = Model('spec');
        if (chksubmit()){
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST["s_name"], "require"=>"true", "message"=>$lang['spec_add_name_no_null'])
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                showMessage($error);
            }else {
                $spec = array();
                $spec['sp_name']        = $_POST['s_name'];
                $spec['sp_sort']        = intval($_POST['s_sort']);
                $spec['class_id']       = $_POST['class_id'];
                $spec['class_name']     = $_POST['class_name'];

                $return = $model_spec->addSpec($spec);
                if($return) {
                    $url = array(
                        array(
                            'url'=>'index.php?act=spec&op=spec_add',
                            'msg'=>$lang['spec_index_continue_to_dd']
                        ),
                        array(
                            'url'=>'index.php?act=spec&op=spec',
                            'msg'=>$lang['spec_index_return_type_list']
                        )
                    );
                    $this->log(L('nc_add,spec_index_spec_name').'['.$_POST['s_name'].']',1);
                    showMessage($lang['nc_common_save_succ'], $url);
                }else {
                    $this->log(L('nc_add,spec_index_spec_name').'['.$_POST['s_name'].']',0);
                    showMessage($lang['nc_common_save_fail']);
                }
            }
        }
        // 一级商品分类
        $gc_list = Model('goods_class')->getGoodsClassListByParentId(0);
        Tpl::output('gc_list', $gc_list);
		Tpl::setDirquna('shop');

        Tpl::showpage('spec.add');
    }

    /**
     * 编辑裸钻
     */
    public function diamond_editOp() {
        $lang   = Language::getLangContent();
        if(empty($_GET['cert_id'])) {
            showMessage($lang['param_error']);
        }
        /**
         * 裸钻模型
         */
        //$model_spec = Model('spec');
        $diamond_api = data_gateway('idiamond');

        /**
         * 编辑保存
         */
        if (chksubmit()) {
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST["cert_id"], "require"=>"true", "message"=>"cert_id错误")
            );
            $error = $obj_validate->validate();
            if ($error != '') {
                showMessage($error);
            } else {

                //更新裸钻信息
                $param      = array();
                $param['cert_id'] = $_POST['cert_id'];
                $param['status']      = $_POST['status'];
                $param['is_hot']    = $_POST['is_hot'];
                $return = $diamond_api->update_diamond_info(array($param));
                //$return = $model_spec->specUpdate($param, array('cert_id'=>intval($_POST['cert_id'])), 'spec');
                if ($return) {
                    /*$url = array(
                        array(
                            'url'=>'index.php?act=diamond&op=diamond_edit&cert_id='.$_POST['cert_id'],
                            'msg'=>$lang['spec_index_return_type_list']
                        )
                    );*/
                    $this->log(L('nc_edit,spec_index_spec_name').'['.$_POST['cert_id'].']',1);
                    showMessage($lang['nc_common_save_succ']);//, $url;
                } else {
                    $this->log(L('nc_edit,spec_index_spec_name').'['.$_POST['cert_id'].']',0);
                    showMessage($lang['nc_common_save_fail']);
                }
            }
        }

        //规格列表
        //$spec_list  = $model_spec->getSpecInfo(intval($_GET['cert_id']));
        $diamond_api = data_gateway('idiamond');
        $where = array('cert_id' => $_GET['cert_id']);
        $byApi = $diamond_api->get_diamond_info($where);
        $diamond_detail = isset($byApi['return_msg'])?$byApi['return_msg']:array();
        if(!$diamond_detail){
            showMessage($lang['param_error']);
        }

        Tpl::output('diamond_detail',$diamond_detail);
		Tpl::setDirquna('shop');
        Tpl::showpage('diamond.edit');
    }

    /**
     * 删除规格
     */
    public function spec_delOp(){
        $lang   = Language::getLangContent();
        if(empty($_GET['id'])) {
            exit(json_encode(array('state'=>false,'msg'=>L('param_error'))));
        }
        //规格模型
        $model_spec = Model('spec');

        if(is_array($_GET['id'])){
            $id = "'".implode("','", $_GET['id'])."'";
        }else{
            $id = intval($_GET['id']);
        }
        //规格列表
        $spec_list  = $model_spec->specList(array('in_sp_id'=>$id));

        if(is_array($spec_list) && !empty($spec_list)){
            // 删除类型与规格关联表
            $return = $model_spec->delSpec('type_spec', array('in_sp_id'=>$id));
            if($return === false){
                exit(json_encode(array('state'=>false,'msg'=>L('nc_common_save_fail'))));
            }

            //删除规格值表
            $return = $model_spec->delSpec('spec_value',array('in_sp_id'=>$id));
            if($return === false){
                exit(json_encode(array('state'=>false,'msg'=>L('nc_common_save_fail'))));
            }

            //删除规格表
            $return = $model_spec->delSpec('spec',array('in_sp_id'=>$id));
            if($return === false){
                exit(json_encode(array('state'=>false,'msg'=>L('nc_common_save_fail'))));
            }

            $this->log(L('nc_delete,spec_index_spec_name').'[ID:'.$id.']',1);
            exit(json_encode(array('state'=>true,'msg'=>L('nc_common_del_succ'))));
        }else{
            $this->log(L('nc_delete,spec_index_spec_name').'[ID:'.$id.']',0);
            exit(json_encode(array('state'=>false,'msg'=>L('param_error'))));
        }
    }
}
