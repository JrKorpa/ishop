<?php
/**
 * 卖家佣金详情管理
 *
 *
 *
 ***/


defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class boss_goods_listControl extends BaseSellerControl {

    public function __construct() {
        parent::__construct();
    }

    public function indexOp() {
        $this->goods_listOp();
    }

    /**
     * 实物列表
     */
    public function goods_listOp() {
        // 裸钻
        $where = array();
        $curpage = isset($_GET['curpage'])?$_GET['curpage']:1;
        if (isset($_GET['goods_id']) && !empty($_GET['goods_id'])) {
            $where['goods_id'] = $_GET['goods_id'];
        }
        if (isset($_GET['goods_sn']) && !empty($_GET['goods_sn'])) {
            $where['goods_sn'] = $_GET['goods_sn'];
        }
        if (isset($_GET['goods_name']) && !empty($_GET['goods_name'])) {
            $where['goods_name'] = $_GET['goods_name'];
        }
        if (in_array($_GET['is_on_sale'], array('1','2','3'))) {
            $where['is_on_sale'] = $_GET['is_on_sale'];
        }
        if (isset($_GET['zhengshuhao']) && !empty($_GET['zhengshuhao'])) {
            $where['zhengshuhao'] = $_GET['zhengshuhao'];
        }
        if (in_array($_GET['tuo_type'], array('1','2','3'))) {
            $where['tuo_type'] = $_GET['tuo_type'];
        }
        $api = data_gateway('iwarehouse');
        $byApi = $api->get_warehouse_list(10, $curpage, $where);
        $goods_list = isset($byApi['return_msg']['data'])?$byApi['return_msg']['data']:array();
        $page = new Page();
        $page->setEachNum(10);
        $page->setNowPage($curpage);
        $page->setTotalNum($byApi['return_msg']['recordCount']);
        $page->setTotalPage($byApi['return_msg']['pageCount']);
        $page->setTotalPageByNum($byApi['return_msg']['pageCount']);
        $page->setStyle(2);
        Tpl::output('show_page',$page->show(2));
        Tpl::output('goods_list', $goods_list);
        $this->profile_menu('boss_goods_list', 'boss_goods_list');
        Tpl::showpage('boss_goods.list');
    }


    /**
     * 用户中心右边，小导航
     *
     * @param string    $menu_type  导航类型
     * @param string    $menu_key   当前导航的menu_key
     * @param array     $array      附加菜单
     * @return
     */
    private function profile_menu($menu_type,$menu_key='',$array=array()) {
        $menu_array = array();
        switch ($menu_type) {
            case 'diamond_info':
                $menu_array = array(
                    array('menu_key' => 'diamond_info', 'menu_name' => '裸钻列表', 'menu_url' => urlShop('diamond_info', 'diamond_info'))
                );
                break;
            case 'boss_goods_list':
                $menu_array = array(
                    array('menu_key' => 'boss_goods_list', 'menu_name' => '实物列表', 'menu_url' => urlShop('boss_goods_list', 'index'))
                );
                break;
            case 'plate_add':
                $menu_array = array(
                    array('menu_key' => 'diamond_info', 'menu_name' => '裸钻列表', 'menu_url' => urlShop('diamond_info', 'diamond_info')),
                    array('menu_key' => 'plate_add', 'menu_name' => '添加裸钻', 'menu_url' => urlShop('diamond_info', 'plate_add'))
                );
                break;
            case 'plate_edit':
                $menu_array = array(
                    array('menu_key' => 'diamond_info', 'menu_name' => '裸钻列表', 'menu_url' => urlShop('diamond_info', 'diamond_info')),
                    array('menu_key' => 'plate_add', 'menu_name' => '添加裸钻', 'menu_url' => urlShop('diamond_info', 'plate_add')),
                    array('menu_key' => 'plate_edit', 'menu_name' => '编辑裸钻', 'menu_url' => urlShop('diamond_info', 'plate_edit'))
                );
                break;
        }
        if(!empty($array)) {
            $menu_array[] = $array;
        }
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }
}
