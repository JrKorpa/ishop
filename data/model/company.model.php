<?php
/**
 * 公司
 *
 * @提供技术支持 授权请购买正版授权
 * @license    http://官网
 * @link       交流群号：官网群
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class companyModel extends Model{

    public function __construct(){
        parent::__construct('company');
    }

    /**
     * 读取列表
     * @param array $condition
     *
     */
    public function getCompanyList($condition,$page=null,$order='',$field='*',$limit=''){
        $result = $this->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
        return $result;
    }

    /**
     * 读取单条记录
     * @param array $condition
     *
     */
    public function getCompanyInfo($condition,$field='*'){
        $result = $this->where($condition)->field($field)->find();
        return $result;
    }

    /*
     *  判断是否存在
     *  @param array $condition
     *
     */
    public function isExist($condition) {
        $result = $this->getOne($condition);
        if(empty($result)) {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }

    /*
     * 增加
     * @param array $param
     * @return bool
     */
    public function save($param){
        return $this->insert($param);
    }

    /*
     * 更新
     * @param array $update
     * @param array $condition
     * @return bool
     */
    public function modify($update, $condition){
        return $this->where($condition)->update($update);
    }

    /*
     * 删除
     * @param array $condition
     * @return bool
     */
    public function drop($condition){
        return $this->where($condition)->delete();
    }



   //获取入库公司
    public function get_put_company()
    {
        $wholesale_model = Model('jxc_wholesale');
        $where = array();
        $store_company_id = $_SESSION['store_company_id'];
        $where['sign_company'] = $store_company_id;
        $where['wholesale_status'] = 1;
        //根据所拥有公司查询出批发客户
        //$JxcWholesale=$JxcWholesaleModel->select2(' * ' , "wholesale_status=1 {$str}" , $type = 'all');
        $JxcWholesale = $wholesale_model->getJxcWholesaleList($where, '*', 1000);
        //2、    增加入库公司
        $put_company = array();
        $companyInfo = $this->getCompanyInfo(array('is_deleted'=>0, 'id'=>$store_company_id),  ' `id`,`company_name`,`is_shengdai`,`sd_company_id`, `company_type` ');
        if(!empty($companyInfo)) {
            $put_company_sd = array();//省代公司做单
            $put_company_jx = array();//上级省代公司

            $sd_company_id = $companyInfo['sd_company_id'];
            if ($sd_company_id) {
                //$company_name = $company_model->select2(' `company_name` ' , " id = '{$sd_company_id}' " , $type = '3');
                $company_name = $this->getCompanyInfo(array('id' => $sd_company_id));
                $put_company_jx[$sd_company_id] = $company_name['company_name'];
            }

            if ($companyInfo['is_shengdai'] == 1) {
                $put_company_sd[$companyInfo['id']] = $companyInfo['company_name'];
                $put_company = $put_company_sd;


            } else {
                if (!empty($put_company_jx)) {
                    $put_company = $put_company_jx;
                }
            }

            $put_company['58'] = '总公司';
        }
        $put_company['57'] = '经销商自采供应商';
        //3. 增加外协工厂
        $company_list = $this->getCompanyList(array('is_deleted'=>0, 'company_type'=>4), 1000, '', '`id`,`company_name`');
        foreach($company_list as $com) {
            $put_company[$com['id']] = $com['company_name'];
        }
        return array('wholesale_list'=>$JxcWholesale, 'company_list'=>$put_company);
    }

    //获取入库公司
    public function get_put_company1()
    {
        //var_dump($_SESSION['store_list']);die;
        $store_model = Model("store");
        $store_list = $_SESSION['store_list'];
        $storeinfo = $store_model->getStoreList(array('store_id'=>array('in', $store_list)), 1000, '', ' `store_company_id` ', '');
        $storeinfo = array_column($storeinfo, 'store_company_id');
        //$JxcWholesaleModel =  new JxcWholesaleModel(22);
        $wholesale_model = Model('jxc_wholesale');
        //$companyId = $this->getUserCompanyId();
        $companyId = array_unique($storeinfo);
        //1、登陆人员所属公司为门店（非总公司）的人，【退货客户】只能显示自己的所属公司
        //$is_company = Auth::user_is_from_base_company();
        //$str = '';
        $where = array();
        //if(!empty($companyId) && !$is_company){
        //$str = " and sign_company in(".implode(",", $companyId).") ";
        $where['sign_company'] = array('in', $companyId);
        //}
        $where['wholesale_status'] = 1;
        //根据所拥有公司查询出批发客户
        //$JxcWholesale=$JxcWholesaleModel->select2(' * ' , "wholesale_status=1 {$str}" , $type = 'all');
        $JxcWholesale = $wholesale_model->getJxcWholesaleList($where, '*', 1000);
        //2、    增加入库公司
        $put_company = array();
        //$company_model = new CompanyModel(1);
        $company_model = Model('company');
        //$companyInfo = $company_model->select2(' `id`,`company_name`,`is_shengdai`,`sd_company_id`, `company_type`' , " is_deleted = 0 and id in(".implode(",", $companyId).") " , $type = '1');
        $companyInfo = $company_model->getCompanyList(array('is_deleted'=>0, 'id'=>array('in', $companyId)), 1000, '', ' `id`,`company_name`,`is_shengdai`,`sd_company_id`, `company_type` ', '');
        if(!empty($companyInfo)){
            $mark_shengdai = false;//是否省代做单
            $put_company_sd = array();//省代公司做单
            $put_company_jx = array();//上级省代公司
            $company_type=0;
            $wai_xie_company=array();
            foreach ($companyInfo as $value) {
                $sd_company_id = $value['sd_company_id'];
                if($value['is_shengdai'] == 1){
                    $mark_shengdai = true;
                    $put_company_sd[$value['id']] = $value['company_name'];
                }
                if($sd_company_id){
                    //$company_name = $company_model->select2(' `company_name` ' , " id = '{$sd_company_id}' " , $type = '3');
                    $company_name = $company_model->getCompanyInfo(array('id'=>$sd_company_id));
                    $put_company_jx[$sd_company_id] = $company_name['company_name'];
                }
                if($value['company_type']==4){
                    $company_type=4;
                    $wai_xie_company=$value;
                }
            }
            if($mark_shengdai == true){
                //（3）如果是省代做单，入库公司就是省代自己
                $put_company = $put_company_sd;
                $put_company['58'] = '总公司';
                //如果创建单据的人是省代的，那么【退货客户】只能是省代下面的经销商
                if(!empty($put_company)){
                    $jxsInfo = array();
                    foreach ($put_company as $id => $name) {
                        //$companyinfo= $company_model->select2(' `id` ' , " sd_company_id = '{$id}' " , $type = '1');
                        $companyinfo = $company_model->getCompanyList(array('sd_company_id'=>$id), 1000, '', ' `id` ', '');
                        if(!empty($companyinfo)){
                            $companyinfo = array_column($companyinfo, 'id');
                            //$jxsInfo[$id]=$JxcWholesaleModel->select2(' * ' , "wholesale_status=1 and sign_company in(".implode(",", $companyinfo).") " , $type = 'all');
                            $jxsInfo[$id]=$wholesale_model->getJxcWholesaleList(array('wholesale_status'=>1, 'sign_company' =>array('in',$companyinfo)), '*', 1000);
                        }
                    }
                    if(!empty($jxsInfo)){
                        foreach ($jxsInfo as $val) {
                            foreach ($val as $r) {
                                $JxcWholesale[] = $r;
                            }
                        }
                    }
                }
            }else{
                //（1） 如果是省代下面的经销商，入库公司显示上级省代公司和总公司
                if(!empty($put_company_jx)){
                    $put_company = $put_company_jx;
                }
                //if(!empty($wai_xie_company)){
                //    $put_company[$wai_xie_company['id']]=$wai_xie_company['company_name'];
                //    //$JxcWholesale=$JxcWholesaleModel->getTocustByCompanyID($wai_xie_company['id']);
                //    $JxcWholesale=$wholesale_model->getTocustByCompanyID($wai_xie_company['id']);
                //}
                //（2） 如果是普通经销商，入库公司只显示总公司
                $put_company['58'] = '总公司';
            }
        }
        $put_company['57'] = '经销商自采供应商';
        return array('wholesale_list'=>$JxcWholesale, 'company_list'=>$put_company);
    }


}
