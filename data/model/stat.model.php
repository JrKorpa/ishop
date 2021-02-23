<?php
/**
 * 统计管理
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');

class statModel extends Model{
    /**
     * 查询新增会员统计
     *
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @param boolean $lock 是否锁定
     * @return array
     */
    public function statByMember($where, $field = '*', $page = 0, $order = '', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('member')->field($field)->where($where)->page($page[0],$page[1])->order($order)->group($group)->select();
            } else {
                return $this->table('member')->field($field)->where($where)->page($page[0])->order($order)->group($group)->select();
            }
        } else {
            return $this->table('member')->field($field)->where($where)->page($page)->order($order)->group($group)->select();
        }
    }
    /**
     * 查询单条会员统计
     *
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @return array
     */
    public function getoneByMember($where, $field = '*', $order = '', $group = '') {
        return $this->table('member')->field($field)->where($where)->order($order)->group($group)->find();
    }
    /**
     * 查询单条店铺统计
     *
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @return array
     */
    public function getoneByStore($where, $field = '*', $order = '', $group = '') {
        return $this->table('store')->field($field)->where($where)->order($order)->group($group)->find();
    }
    /**
     * 查询店铺统计
     */
    public function statByStore($where, $field = '*', $page = 0, $limit = 0, $order = '', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('store')->field($field)->where($where)->page($page[0],$page[1])->limit($limit)->group($group)->order($order)->select();
            } else {
                return $this->table('store')->field($field)->where($where)->page($page[0])->limit($limit)->group($group)->order($order)->select();
            }
        } else {
            return $this->table('store')->field($field)->where($where)->page($page)->limit($limit)->group($group)->order($order)->select();
        }
    }
    /**
     * 查询新增店铺统计
     */
    public function getNewStoreStatList($condition, $field = '*', $page = 0, $order = 'store_id desc', $limit = 0, $group = '') {
        return $this->table('store')->field($field)->where($condition)->page($page)->limit($limit)->group($group)->order($order)->select();
    }

    /**
     * 查询会员列表
     */
    public function getMemberList($where, $field = '*', $page = 0, $order = 'member_id desc', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('member')->field($field)->where($where)->page($page[0],$page[1])->group($group)->order($order)->select();
            } else {
                return $this->table('member')->field($field)->where($where)->page($page[0])->group($group)->order($order)->select();
            }
        } else {
            return $this->table('member')->field($field)->where($where)->page($page)->group($group)->order($order)->select();
        }
    }

    /**
     * 调取店铺等级信息
     */
    public function getStoreDegree(){
        $tmp = $this->table('store_grade')->field('sg_id,sg_name')->where(true)->select();
        $sd_list = array();
        if(!empty($tmp)){
            foreach ($tmp as $k=>$v){
                $sd_list[$v['sg_id']] = $v['sg_name'];
            }
        }
        return $sd_list;
    }

    /**
     * 查询会员统计数据记录
     *
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @return array
     */
    public function statByStatmember($where, $field = '*', $page = 0, $limit = 0,$order = '', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('stat_member')->field($field)->where($where)->page($page[0],$page[1])->limit($limit)->order($order)->group($group)->select();
            } else {
                return $this->table('stat_member')->field($field)->where($where)->page($page[0])->limit($limit)->order($order)->group($group)->select();
            }
        } else {
            return $this->table('stat_member')->field($field)->where($where)->page($page)->limit($limit)->order($order)->group($group)->select();
        }
    }

    /**
     * 查询商品数量
     */
    public function getGoodsNum($where){
        $rs = $this->field('count(*) as allnum')->table('goods_common')->where($where)->select();
        return $rs[0]['allnum'];
    }
    /**
     * 获取预存款数据
     */
    public function getPredepositInfo($condition, $field = '*', $page = 0, $order = 'lg_add_time desc', $limit = 0, $group = ''){
        return $this->table('pd_log')->field($field)->where($condition)->page($page)->limit($limit)->group($group)->order($order)->select();
    }
    /**
     * 获取结算数据
     */
    public function getBillList($where=array(), $field='*', $page = 0, $limit = 0, $order = 'ob_id desc', $group = ''){
        return $this->table('order_bill')->field($field)->where($where)->page($page)->limit($limit)->group($group)->order($order)->select();
    }
    /**
     * 查询订单及订单商品的统计
     *
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @return array
     */
    public function statByOrderGoods($where, $field = '*', $page = 0, $limit = 0,$order = '', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('order_goods,orders')->field($field)->join('left')->on('order_goods.order_id=orders.order_id')->where($where)->group($group)->page($page[0],$page[1])->limit($limit)->order($order)->select();
            } else {
                return $this->table('order_goods,orders')->field($field)->join('left')->on('order_goods.order_id=orders.order_id')->where($where)->group($group)->page($page[0])->limit($limit)->order($order)->select();
            }
        } else {
            return $this->table('order_goods,orders')->field($field)->join('left')->on('order_goods.order_id=orders.order_id')->where($where)->group($group)->page($page)->limit($limit)->order($order)->select();
        }
    }
    /**
     * 查询订单及订单商品的统计
     *
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @return array
     */
    public function statByOrderLog($where, $field = '*', $page = 0, $limit = 0,$order = '', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('order_log,orders')->field($field)->join('left')->on('order_log.order_id = orders.order_id')->where($where)->group($group)->page($page[0],$page[1])->limit($limit)->order($order)->select();
            } else {
                return $this->table('order_log,orders')->field($field)->join('left')->on('order_log.order_id = orders.order_id')->where($where)->group($group)->page($page[0])->limit($limit)->order($order)->select();
            }
        } else {
            return $this->table('order_log,orders')->field($field)->join('left')->on('order_log.order_id = orders.order_id')->where($where)->group($group)->page($page)->limit($limit)->order($order)->select();
        }
    }
    /**
     * 查询退款退货统计
     *
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @return array
     */
    public function statByRefundreturn($where, $field = '*', $page = 0, $limit = 0,$order = '', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('refund_return')->field($field)->where($where)->group($group)->page($page[0],$page[1])->limit($limit)->order($order)->select();
            } else {
                return $this->table('refund_return')->field($field)->where($where)->group($group)->page($page[0])->limit($limit)->order($order)->select();
            }
        } else {
            return $this->table('refund_return')->field($field)->where($where)->group($group)->page($page)->limit($limit)->order($order)->select();
        }
    }
    /**
     * 查询店铺动态评分统计
     *
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @return array
     */
    public function statByStoreAndEvaluatestore($where, $field = '*', $page = 0, $limit = 0,$order = '', $group = ''){
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('evaluate_store,store')->field($field)->join('left')->on('evaluate_store.seval_storeid=store.store_id')->where($where)->group($group)->page($page[0],$page[1])->limit($limit)->order($order)->select();
            } else {
                return $this->table('evaluate_store,store')->field($field)->join('left')->on('evaluate_store.seval_storeid=store.store_id')->where($where)->group($group)->page($page[0])->limit($limit)->order($order)->select();
            }
        } else {
            return $this->table('evaluate_store,store')->field($field)->join('left')->on('evaluate_store.seval_storeid=store.store_id')->where($where)->group($group)->page($page)->limit($limit)->order($order)->select();
        }
    }
    /**
     * 处理搜索时间
     */
    public function dealwithSearchTime($search_arr){
        //初始化时间
        //天
        if(!$search_arr['search_time']){
            $search_arr['search_time'] = date('Y-m-d', time()- 86400);
        }
        $search_arr['day']['search_time'] = strtotime($search_arr['search_time']);//搜索的时间

        //周
        if(!$search_arr['searchweek_year']){
            $search_arr['searchweek_year'] = date('Y', time());
        }
        if(!$search_arr['searchweek_month']){
            $search_arr['searchweek_month'] = date('m', time());
        }
        if(!$search_arr['searchweek_week']){
            $searchweek_weekarr = getWeek_SdateAndEdate(time());
            $search_arr['searchweek_week'] = implode('|', $searchweek_weekarr);
            $searchweek_week_edate_m = date('m', strtotime($searchweek_weekarr['edate']));
            if($searchweek_week_edate_m <> $search_arr['searchweek_month']){
                $search_arr['searchweek_month'] = $searchweek_week_edate_m;
            }
        }
        $weekcurrent_year = $search_arr['searchweek_year'];
        $weekcurrent_month = $search_arr['searchweek_month'];
        $weekcurrent_week = $search_arr['searchweek_week'];
        $search_arr['week']['current_year'] = $weekcurrent_year;
        $search_arr['week']['current_month'] = $weekcurrent_month;
        $search_arr['week']['current_week'] = $weekcurrent_week;

        //月
        if(!$search_arr['searchmonth_year']){
            $search_arr['searchmonth_year'] = date('Y', time());
        }
        if(!$search_arr['searchmonth_month']){
            $search_arr['searchmonth_month'] = date('m', time());
        }
        $monthcurrent_year = $search_arr['searchmonth_year'];
        $monthcurrent_month = $search_arr['searchmonth_month'];
        $search_arr['month']['current_year'] = $monthcurrent_year;
        $search_arr['month']['current_month'] = $monthcurrent_month;
        return $search_arr;
    }
    /**
     * 获得查询的开始和结束时间
     */
    public function getStarttimeAndEndtime($search_arr){
        //默认统计当前数据
        if(!$search_arr['search_type']){
            $search_arr['search_type'] = 'day';
        }
        if($search_arr['search_type'] == 'day'){
            $stime = $search_arr['day']['search_time'];//今天0点
            $etime = $search_arr['day']['search_time'] + 86400 - 1;//今天24点
        }
        if($search_arr['search_type'] == 'day3'){
            $stime = $search_arr['day']['search_time'] - 86400 * 2;//3天前0点
            $etime = $search_arr['day']['search_time'] + 86400 - 1;//今天24点
        }
        if($search_arr['search_type'] == 'day7'){
            $stime = $search_arr['day']['search_time'] - 86400 * 6;//7天前0点
            $etime = $search_arr['day']['search_time'] + 86400 - 1;//今天24点
        }
        if($search_arr['search_type'] == 'week'){
            $current_weekarr = explode('|', $search_arr['week']['current_week']);
            $stime = strtotime($current_weekarr[0]);
            $etime = strtotime($current_weekarr[1])+86400-1;
        }
        if($search_arr['search_type'] == 'month'){
            $stime = strtotime($search_arr['month']['current_year'].'-'.$search_arr['month']['current_month']."-01 0 month");
            $etime = getMonthLastDay($search_arr['month']['current_year'],$search_arr['month']['current_month'])+86400-1;
        }
        if($search_arr['search_type'] == 'year'){
            $stime = strtotime($search_arr['year']['current_year']."-01-01");
            $etime = strtotime($search_arr['year']['current_year']."-12-31")+86400-1;
        }
        return array($stime,$etime);
    }
    /**
     * 查询会员统计数据单条记录
     *
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @return array
     */
    public function getOneStatmember($where, $field = '*', $order = '', $group = ''){
        return $this->table('stat_member')->field($field)->where($where)->group($group)->order($order)->find();
    }
    /**
     * 更新会员统计数据单条记录
     *
     * @param array $condition 条件
     * @param array $update_arr 更新数组
     * @return array
     */
    public function updateStatmember($where,$update_arr){
        return $this->table('stat_member')->where($where)->update($update_arr);
    }
    /**
     * 查询订单的统计
     *
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @return array
     */
    public function statByOrder($where, $field = '*', $page = 0, $limit = 0,$order = '', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('orders')->field($field)->where($where)->group($group)->page($page[0],$page[1])->limit($limit)->order($order)->select();
            } else {
                return $this->table('orders')->field($field)->where($where)->group($group)->page($page[0])->limit($limit)->order($order)->select();
            }
        } else {
            return $this->table('orders')->field($field)->where($where)->group($group)->page($page)->limit($limit)->order($order)->select();
        }
    }
    /**
     * 查询积分的统计
     *
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @return array
     */
    public function statByPointslog($where, $field = '*', $page = 0, $limit = 0,$order = '', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('points_log')->field($field)->where($where)->group($group)->page($page[0],$page[1])->limit($limit)->order($order)->select();
            } else {
                return $this->table('points_log')->field($field)->where($where)->group($group)->page($page[0])->limit($limit)->order($order)->select();
            }
        } else {
            return $this->table('points_log')->field($field)->where($where)->group($group)->page($page)->limit($limit)->order($order)->select();
        }
    }
    /**
     * 删除会员统计数据记录
     *
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @return array
     */
    public function delByStatmember($where = array()) {
        $this->table('stat_member')->where($where)->delete();
    }
    /**
     * 查询订单商品缓存的统计
     *
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @return array
     */
    public function getoneByStatordergoods($where, $field = '*', $order = '', $group = '') {
        return $this->table('stat_ordergoods')->field($field)->where($where)->group($group)->order($order)->find();
    }


    /**
     * 查询订单商品缓存的统计
     *
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @return array
     */
    public function statByStatordergoods($where, $field = '*', $page = 0, $limit = 0,$order = '', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('stat_ordergoods')->field($field)->where($where)->group($group)->page($page[0],$page[1])->limit($limit)->order($order)->select();
            } else {
                return $this->table('stat_ordergoods')->field($field)->where($where)->group($group)->page($page[0])->limit($limit)->order($order)->select();
            }
        } else {
            return $this->table('stat_ordergoods')->field($field)->where($where)->group($group)->page($page)->limit($limit)->order($order)->select();
        }
    }
    /**
     * 查询订单缓存的统计
     *
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @return array
     */
    public function getoneByStatorder($where, $field = '*', $order = '', $group = '') {
        if(isset($where['fenlei']) || isset($where['source_name'])){
            return $this->table('stat_order,customer_sources')->join('inner')->on('stat_order.customer_source_id = customer_sources.id')->field($field)->where($where)->group($group)->order($order)->find();
        }else{
            return $this->table('stat_order')->field($field)->where($where)->group($group)->order($order)->find();
        }


    }
    /**
     * 查询订单缓存的统计
     *
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @return array
     */
    public function statByStatorder($where, $field = '*', $page = 0, $limit = 0,$order = '', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('stat_order')->field($field)->where($where)->group($group)->page($page[0],$page[1])->limit($limit)->order($order)->select();
            } else {
                return $this->table('stat_order')->field($field)->where($where)->group($group)->page($page[0])->limit($limit)->order($order)->select();
            }
        } else {
            return $this->table('stat_order')->field($field)->where($where)->group($group)->page($page)->limit($limit)->order($order)->select();
        }
    }
    /**
     * 查询订单缓存数量
     * 
     * @param array $where 条件
     * @param string $field 字段
     */
    public function getStatOrderCount($where, $field) {
       return $this->table('stat_order')->field($field)->where($where)->count();
    }


    /**
     * 查询订单及订单商品的统计
     *
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @return array
     */
    public function getStatOrderGoods($where, $field = '*', $page = 0, $limit = 0,$order = '', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('stat_ordergoods,stat_order')->field($field)->join('left')->on('stat_ordergoods.order_id=stat_order.order_id')->where($where)->group($group)->page($page[0],$page[1])->limit($limit)->order($order)->select();
            } else {
                return $this->table('stat_ordergoods,stat_order')->field($field)->join('left')->on('stat_ordergoods.order_id=stat_order.order_id')->where($where)->group($group)->page($page[0])->limit($limit)->order($order)->select();
            }
        } else {
            return $this->table('stat_ordergoods,stat_order')->field($field)->join('left')->on('stat_ordergoods.order_id=stat_order.order_id')->where($where)->group($group)->page($page)->limit($limit)->order($order)->select();
        }
    }


    /**
     * 订单、订单明细、库存、客户来源连表查询
     */
    public function getStatOrderGoodsItem($where, $field = '*',$page=0,$limit=0,$order='', $group = ''){
        return $this->table('stat_ordergoods,stat_order,customer_sources,base_style_info')->join('inner,left,left')->on('stat_order.order_id = stat_ordergoods.order_id,stat_order.customer_source_id = customer_sources.id,stat_ordergoods.style_sn=base_style_info.style_sn')->field($field)->where($where)->group($group)->page($page)->limit($limit)->order($order)->select();
    }






    /**
     * 订单、订单明细、库存、客户来源连表查询
     */
    public function getStatRefundOrderGoodsItem($where, $field = '*',$page=0,$limit=0,$order='', $group = ''){
        return $this->table('refund_return,stat_ordergoods,stat_order,customer_sources,base_style_info')->join('left,left,left,left')->on('refund_return.order_goods_id=stat_ordergoods.rec_id,stat_order.order_id = stat_ordergoods.order_id,stat_order.customer_source_id = customer_sources.id,stat_ordergoods.style_sn=base_style_info.style_sn')->field($field)->where($where)->group($group)->page($page)->limit($limit)->order($order)->select();
    }






    /**
     * 查询商品列表
     *
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @return array
     */
    public function statByGoods($where, $field = '*', $page = 0, $limit = 0,$order = '', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table('goods')->field($field)->where($where)->group($group)->page($page[0],$page[1])->limit($limit)->order($order)->select();
            } else {
                return $this->table('goods')->field($field)->where($where)->group($group)->page($page[0])->limit($limit)->order($order)->select();
            }
        } else {
            return $this->table('goods')->field($field)->where($where)->group($group)->page($page)->limit($limit)->order($order)->select();
        }
    }

    /**
     * 查询流量统计单条记录
     *
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @return array
     */
    public function getoneByFlowstat($tablename = 'flowstat', $where, $field = '*', $order = '', $group = '') {
        return $this->table($tablename)->field($field)->where($where)->group($group)->order($order)->find();
    }
    /**
     * 查询流量统计记录
     *
     * @param array $condition 条件
     * @param string $field 字段
     * @param string $group 分组
     * @param string $order 排序
     * @param int $limit 限制
     * @param int $page 分页
     * @return array
     */
    public function statByFlowstat($tablename = 'flowstat', $where, $field = '*', $page = 0, $limit = 0,$order = '', $group = '') {
        if (is_array($page)){
            if ($page[1] > 0){
                return $this->table($tablename)->field($field)->where($where)->group($group)->page($page[0],$page[1])->limit($limit)->order($order)->select();
            } else {
                return $this->table($tablename)->field($field)->where($where)->group($group)->page($page[0])->limit($limit)->order($order)->select();
            }
        } else {
            return $this->table($tablename)->field($field)->where($where)->group($group)->page($page)->limit($limit)->order($order)->select();
        }
    }

    /**
     * 库存统计查询
     */
    public function statGoodsItem($where, $field = '*',$page=0,$limit=0,$order='', $group = ''){
            return $this->table('goods_items')->field($field)->where($where)->group($group)->page($page)->limit($limit)->order($order)->select();
    }


    /**
     * 查询订单
     * @param $where
     * @param string $field
     * @param string $group
     * @return mixed
     */
    public function statOrder($where, $field = '*',$page=0,$limit=0,$order='', $group = ''){
        return $this->table('orders')->field($field)->where($where)->group($group)->page($page)->limit($limit)->order($order)->select();
    }

    /**
     * 库存订单连表查询
     */
    public function statOrderGoodsStock($where, $field = '*',$page=0,$limit=0,$order='', $group = ''){
        return $this->table('goods_items,order_goods,orders')->join('inner,inner')->on('goods_items.order_detail_id = order_goods.rec_id,order_goods.order_id = orders.order_id')->field($field)->where($where)->group($group)->page($page)->limit($limit)->order($order)->select();
    }

    /**
     * 订单、客户来源
     */
    public function statOrderCustomerSources($where, $field = '*',$page=0,$limit=0,$order='', $group = '',$count = 0){
        if($group != ''){
            $count = $this->table('stat_order,customer_sources')->join('inner')->on('stat_order.customer_source_id = customer_sources.id')->where($where)->group($group)->groupcount($field);
        }
        return $this->table('stat_order,customer_sources')->join('inner')->on('stat_order.customer_source_id = customer_sources.id')->field($field)->where($where)->group($group)->page($page,$count)->limit($limit)->order($order)->select();
    }



    /**
     * 订单、单据、单据明细、库存
     */
    public function sataLossGoodsItem($where, $field = '*',$page=0,$limit=0,$order='', $group = '',$count = 0){
        if($group != ''){
            $count = $this->table('erp_bill_goods,erp_bill,orders,customer_sources,goods_items')->join('inner,inner,inner,inner')->on('erp_bill_goods.bill_id = erp_bill.bill_id,erp_bill.order_sn = orders.order_sn,customer_sources.id = orders.customer_source_id,erp_bill_goods.goods_itemid=goods_items.goods_id')->where($where)->group($group)->groupcount($field);
        }
        return $this->table('erp_bill_goods,erp_bill,orders,customer_sources,goods_items')->join('inner,inner,inner,inner')->on('erp_bill_goods.bill_id = erp_bill.bill_id,erp_bill.order_sn = orders.order_sn,customer_sources.id = orders.customer_source_id,erp_bill_goods.goods_itemid=goods_items.goods_id')->field($field)->where($where)->group($group)->page($page,$count)->limit($limit)->order($order)->select();
    }

    //获取总库存入口
    public function get_stock_goods($field='*',$_group='',$page=0,$limit=0,$order=''){
        $where = array();
        $where['store_id'] = $_SESSION['store_id'];
        $where['is_on_sale'] = 2;
        return $this->statGoodsItem($where, $field,$page,$limit,$order, $_group);

    }



    //待取库存入口
    public function get_waiting_stock_goods($field='*',$_group='',$page=0,$limit=0,$order=''){
        $where = array();
        $where['goods_items.store_id'] = $_SESSION['store_id'];
        $where['goods_items.is_on_sale'] = 2;
        $where['orders.order_state'] = array('lt',ORDER_STATE_SEND);
        $where['orders.pay_status'] = array('gt',ORDER_PAY_TODO);
        $where['order_goods.is_return'] = 0;
        return $this->statOrderGoodsStock($where, $field,$page,$limit,$order, $_group);

    }



    /***
     * 获取订单金额
     * 实付金额 = 已付金额-退款金额
     * 应付尾款 = 订单金额 - （已付金额-退款金额）+ 违约金
     * @param $type
     * @return int
     */
    public function get_order_sum_price($type){
        $where = '';
        $where .= 'store_id = '. $_SESSION['store_id'];
        switch ($type){
            case 1://待取订单
                $where .= ' and pay_status > ' . ORDER_PAY_TODO .' and order_state >= '.ORDER_STATE_NEW .' and order_state <= '.ORDER_STATE_TOSEND;
                $field = 'SUM(order_amount) as sum_order_amount,SUM(rcb_amount-refund_amount) as sum_shi_price,SUM(order_amount - rcb_amount + refund_amount + IFNULL(breach_amount,0)) as sum_pay_price';
                break;
            case 2://已到货待取订单
                $where .= ' and pay_status > '. ORDER_PAY_TODO . ' and (order_state =' .ORDER_STATE_NEW . ' or order_state = '. ORDER_STATE_TOSEND .')';
                $field = 'SUM(order_amount) as sum_order_amount,SUM(rcb_amount-refund_amount) as sum_shi_price,SUM(order_amount - rcb_amount + refund_amount + IFNULL(breach_amount,0)) as sum_pay_price';
                break;
            case 3://未到货待取订单
                $where .= ' and pay_status > '. ORDER_PAY_TODO . ' and (order_state > ' .ORDER_STATE_NEW . ' and order_state < '. ORDER_STATE_TOSEND .')';
                $field = 'SUM(order_amount) as sum_order_amount,SUM(rcb_amount-refund_amount) as sum_shi_price,SUM(order_amount - rcb_amount + refund_amount + IFNULL(breach_amount,0)) as sum_pay_price';
                break;

        }
        $sale_price =  $this->statOrder($where, $field);
        return empty($sale_price[0]) ? 0: $sale_price[0];
    }


  //产品销售数量、金额
    public function getSaleByCatType($where){
        $db = new ModelDb();
        $where = $db->parseWhere($where);
        $sql = "SELECT IFNULL(IF (og.goods_type = 5,'赠品',IF (og.from_type = 2,'裸石',IF (og.is_xianhuo = 1,IF (g.product_type IN ('普通黄金','定价黄金','投资黄金','足金镶嵌'),'黄金',IF (g.cat_type = '0',NULL,g.cat_type)),IF (s.product_type IN ('普通黄金','定价黄金','投资黄金','足金镶嵌'),'黄金',t.cat_type_name)))),'未知') AS cat_type,count(og.rec_id) AS sum_sale_num,SUM(og.goods_pay_price) as sum_sale_price
                FROM stat_order AS o INNER JOIN stat_ordergoods AS og ON o.order_id = og.order_id 
                LEFT JOIN customer_sources AS c ON  c.id = o.customer_source_id
                LEFT JOIN goods_items AS g ON g.goods_id = og.goods_id 
                LEFT JOIN base_style_info AS s ON s.style_sn = og.style_sn
                LEFT JOIN app_cat_type AS t ON t.cat_type_id = s.style_type
                $where
                GROUP BY IFNULL(IF (og.goods_type = 5,'赠品',IF (og.from_type = 2,'裸石',IF (og.is_xianhuo = 1,IF (g.product_type IN ('普通黄金','定价黄金','投资黄金','足金镶嵌'),'黄金',IF (g.cat_type = '0',NULL,g.cat_type)),IF (s.product_type IN ('普通黄金','定价黄金','投资黄金','足金镶嵌'),'黄金',t.cat_type_name)))),'未知')";
         return $this->query($sql);
    }


    //根据客户来源分类分组查询销售金额
    public function getSaleByCSFL($where){
        $db = new ModelDb();
        $where = $db->parseWhere($where);
        $sql = "SELECT c.fenlei,CASE c.fenlei WHEN - 1 THEN '其他' WHEN 1 THEN '异业联盟' WHEN 2 THEN '社区' WHEN 3 THEN '珂兰相关' WHEN 4 THEN '团购' WHEN 5 THEN '老顾客' WHEN 6 THEN '数据' WHEN 7 THEN 	'网络来源' END AS 'fenlei_name',SUM(o.order_amount) as sum_sale_price
                FROM stat_order o INNER JOIN customer_sources c ON c.id = o.customer_source_id
                $where
                GROUP BY c.fenlei";
        return $this->query($sql);
    }

    //根据客户来源分组查询销售金额
    public  function getSaleByCS($where){
        $db = new ModelDb();
        $where = $db->parseWhere($where);
        $sql = "SELECT c.fenlei,CASE c.fenlei WHEN - 1 THEN '其他' WHEN 1 THEN '异业联盟' WHEN 2 THEN '社区' WHEN 3 THEN '珂兰相关' WHEN 4 THEN '团购' WHEN 5 THEN '老顾客' WHEN 6 THEN '数据' WHEN 7 THEN 	'网络来源' END AS 'fenlei_name',c.source_name,SUM(o.order_amount) as sum_order_amount ,SUM(o.rcb_amount) as sum_rcb_amount,SUM(o.order_amount-o.rcb_amount+o.refund_amount+o.breach_amount) as sum_pay_amount,SUM(o.refund_amount) as sum_refund_amount,count(distinct buyer_phone) as buyer_num
               FROM stat_order o INNER JOIN customer_sources c ON c.id = o.customer_source_id
               $where 
               GROUP BY o.customer_source_id ORDER BY c.fenlei asc";
        return $this->query($sql);

    }





    //销售渠道
    public function getCustomerSourcesByIds($where)
    {

        $sql = "select id, source_code,source_name,fenlei from customer_sources where is_deleted = 0 and is_enabled= 1";
        if (!empty($where['ids'])) {
            $ids = $where['ids'];
            if (is_array($ids)) {
                $ids = implode(",", $ids);
            } else {
                $ids = preg_split('/,/', $ids, -1, PREG_SPLIT_NO_EMPTY);
            }
            $ids = implode(",", $ids);
            $sql .= " AND id in ({$ids})";

        }
        if (!empty($where['source_codes'])) {
            $source_codes = $where['source_codes'];
            if (is_array($source_codes)) {
                $source_codes = implode(",", $source_codes);
            } else {
                $source_codes = preg_split('/,/', $source_codes, -1, PREG_SPLIT_NO_EMPTY);
            }
            $source_codes = implode("','", $source_codes);
            $sql .= " AND source_code in ('{$source_codes}')";

        }
        if (!empty($where['dept_ids'])) {
            $dept_ids = $where['dept_ids'];
            if (!is_array($dept_ids)) {
                $dept_ids = preg_split('/,/', $dept_ids, -1, PREG_SPLIT_NO_EMPTY);
            }
            $dept_ids = implode(",", $dept_ids);
            $sql .= " AND source_own_id in ({$dept_ids})";
        }

        if (!empty($where['company_ids'])) {
            $company_ids = $where['company_ids'];
            if (!is_array($company_ids)) {
                $company_ids = preg_split('/,/', $dept_ids, -1, PREG_SPLIT_NO_EMPTY);
            }
            $company_ids = implode(",", $company_ids);
            $sqlt = "select id from store where store_company_id in ({$company_ids})";
            $rest = $this->query($sqlt);
            $defarr = array(17);
            if ($rest) {
                $dept_id = array_column($rest, 'id');
                $defarr = array_merge($defarr, $dept_id);
            }
            $dept_ids = implode(",", $defarr);
            $sql .= " AND source_own_id in ({$dept_ids})";
        }

        if (!empty($where['fenlei'])) {
            $sql .= " AND fenlei = " . $where['fenlei'];
        }


        return $this->query($sql);
    }









}
