<?php
/**
 * 结算管理
 *
 *
 *
 *
 * @提供技术支持 授权请购买正版授权
 * @license    http://官网
 * @link       交流群号：官网群
 */



defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class goods_recommendControl extends SystemControl{

    public function __construct(){
        parent::__construct();
    }

    public function indexOp(){
        $model_rec = Model('goods_recommend');
		    	
		Tpl::setDirquna('shop');
        Tpl::showpage('goods_recommend.index');
    }

    /**
     * 新增
     */
    public function addOp(){
        $model_class = Model('goods_class');
        $gc_list = $model_class->getTreeClassList(1);
        Tpl::output('gc_list', $gc_list);

        $rec_gc_id = intval($_GET['rec_gc_id']);
        $goods_list = array();
        if ($rec_gc_id > 0) {
            $rec_list = Model('goods_recommend')->getGoodsRecommendList(array('rec_gc_id'=>$rec_gc_id),'','','*','','rec_goods_id');
            if (!empty($rec_list)) {
                $condition["style_sn"]=array_keys($rec_list);
                $styleApi = data_gateway('istyle');
                $result = $styleApi->get_style_list($condition,1,10);
                $goods_list=$result['return_msg']['data'];
                if (!empty($goods_list)) {
                    foreach ($goods_list as $k => $v) {
                        $goods_list[$k]['goods_id'] = $goods_list[$k]['style_sn'];
                        $goods_list[$k]['goods_name'] = $goods_list[$k]['style_name'];
                    }
                }
            }
        }
        Tpl::output('goods_list_json',json_encode($goods_list));
        Tpl::output('goods_list', $goods_list);
        Tpl::output('rec_info', is_array($rec_list) ? current($rec_list) : array());
		    	
		Tpl::setDirquna('shop');
        Tpl::showpage('goods_recommend.add');
    }

    /**
     * 保存
     */
    public function saveOp(){
        $gc_id = intval($_POST['gc_id']);
        if (!chksubmit() || $gc_id <= 0) {
            showMessage('非法提交');
        }
        $model_rec = Model('goods_recommend');
        $del = $model_rec->delGoodsRecommend(array('rec_gc_id' => $gc_id));
        if (!$del) {
            showMessage('保存失败');
        }

        $data = array();
        if (is_array($_POST['goods_id_list'])) {
            foreach ($_POST['goods_id_list'] as $k => $goods_id) {
                $data[$k]['rec_gc_id'] = $_POST['gc_id'];
                $data[$k]['rec_gc_name'] = rtrim($_POST['gc_name'],' >');
                $data[$k]['rec_goods_id'] = $goods_id;
            }
        }
        $insert = $model_rec->addGoodsRecommend($data);
        if ($insert) {
            showMessage('保存成功','index.php?act=goods_recommend&op=index');
        }
    }

    public function get_xmlOp(){
        $model_rec = Model('goods_recommend');
        $condition  = array();
        $sort_fields = array('rec_id');
        if ($_POST['sortorder'] != '' && in_array($_POST['sortname'],$sort_fields)) {
            $order = $_POST['sortname'].' '.$_POST['sortorder'];
        }
        if ($_POST['query'] != '' && in_array($_POST['qtype'],array('rec_gc_name'))) {
            $condition[$_POST['qtype']] = array('like',"%{$_POST['query']}%");
        }
        $total_num = $model_rec->getGoodsRecommendCount($condition,'distinct rec_gc_id');
        $rec_list = $model_rec->getGoodsRecommendList($condition,$_POST['rp'],$order,'count(*) as rec_count,rec_gc_id,min(rec_gc_name) as rec_gc_name,min(rec_id) as rec_id','rec_gc_id','',$total_num);
        $data = array();
        $data['now_page'] = $model_rec->shownowpage();
        $data['total_num'] = $total_num;
        foreach ($rec_list as $v) {
            $list = array();
            $list['operation'] = "<a class='btn red' onclick=\"fg_delete({$v['rec_gc_id']})\"><i class='fa fa-trash-o'></i>删除</a><a class='btn blue' href='index.php?act=goods_recommend&op=add&rec_gc_id={$v['rec_gc_id']}'><i class='fa fa-pencil-square-o'></i>编辑</a>";
            $list['rec_gc_name'] = $v['rec_gc_name'];
            $list['rec_count'] = $v['rec_count'];
            $data['list'][$v['rec_gc_id']] = $list;
        }
        exit(Tpl::flexigridXML($data));
    }

    /**
     * 删除
     */
    public function deleteOp() {
        $model_rec = Model('goods_recommend');
        $condition = array();
        if (preg_match('/^[\d,]+$/', $_GET['del_id'])) {
            $_GET['del_id'] = explode(',',trim($_GET['del_id'],','));
            $condition['rec_gc_id'] = array('in',$_GET['del_id']);
        }
        $del = $model_rec->delGoodsRecommend($condition);
        if (!$del){
            $this->log('删除分类推荐商品失败',0);
            exit(json_encode(array('state'=>false,'msg'=>'删除失败')));
        }else{
            $this->log('成功删除分类推荐商品',1);
            exit(json_encode(array('state'=>true,'msg'=>'删除成功')));
        }
    }

    public function org_get_goods_listOp(){
        $model_goods = Model('goods');
        $condition = array();
        $condition['gc_id'] = intval($_GET['gc_id']);
        if (!empty($_GET['goods_name'])) {
            $condition['goods_name'] = array('like',"%{$_GET['goods_name']}%");
        }
        $goods_list = $model_goods->getGoodsOnlineList($condition,'*',8);
        print_r($goods_list);
        $html = "<ul class=\"dialog-goodslist-s2\">";
        foreach($goods_list as $v) {
            $url = urlShop('goods', 'index', array('goods_id' => $v['goods_id']));
            $img = thumb($v,240);
            $html .= <<<EOB
            <li>
            <div class="goods-pic" onclick="select_recommend_goods({$v['goods_id']});">
            <span class="ac-ico"></span>
            <span class="thumb size-72x72">
            <i></i>
            <img width="72" src="{$img}" goods_name="{$v['goods_name']}" goods_id="{$v['goods_id']}" title="{$v['goods_name']}">
            </span>
            </div>
            <div class="goods-name">
            <a target="_blank" href="{$url}">{$v['goods_name']}</a>
            </div>
            </li>
EOB;
        }
        $admin_tpl_url = ADMIN_TEMPLATES_URL;
        $html .= '<div class="clear"></div></ul><div id="pagination" class="pagination">'.$model_goods->showpage(1).'</div><div class="clear"></div>';
        $html .= <<<EOB
        <script>
        $('#pagination').find('.demo').ajaxContent({
                event:'click',
                loaderType:"img",
                loadingMsg:"{$admin_tpl_url}/images/transparent.gif",
                target:'#show_recommend_goods_list'
            });
        </script>
EOB;
        echo $html;
    }


    public function get_goods_listOp(){
        $model_goods = Model('goods');
        $condition = array();
        if (empty($_GET['goods_name'])) return;
        $condition['style_sn'] =$_GET['goods_name'];
        $styleApi = data_gateway('istyle');
        $result = $styleApi->get_style_list($condition,1,8);
        $goods_list=$result['return_msg']['data'];
        if(!is_array($goods_list)||count($goods_list)==0)return;
        $html = "<ul class=\"dialog-goodslist-s2\">";
        $base_style_url=WAP_SITE_URL."/tmpl/couple_detail.html?goods_sn=";
        foreach($goods_list as $v) {
            $img = $v['goods_image'];
            $html .= <<<EOB
            <li>
            <div class="goods-pic" onclick="select_recommend_goods('{$v['style_sn']}');">
            <span class="ac-ico"></span>
            <span class="thumb size-72x72">
            <i></i>
            <img width="72" src="{$img}" goods_name="{$v['style_name']}" goods_id="{$v['style_sn']}" title="{$v['style_sn']}">
            </span>
            </div>
            <div class="goods-name">
            <a target="_blank" href="{$base_style_url}{$v['goods_sn']}">{$v['style_sn']}-{$v['style_name']}</a>
            </div>
            </li>
EOB;
        }
        $admin_tpl_url = ADMIN_TEMPLATES_URL;
        $html .= '<div class="clear"></div></ul><div id="pagination" class="pagination">'.$model_goods->showpage(1).'</div><div class="clear"></div>';
        $html .= <<<EOB
        <script>
        $('#pagination').find('.demo').ajaxContent({
                event:'click',
                loaderType:"img",
                loadingMsg:"{$admin_tpl_url}/images/transparent.gif",
                target:'#show_recommend_goods_list'
            });
        </script>
EOB;
        echo $html;
    }

}
