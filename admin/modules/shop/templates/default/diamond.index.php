<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3><?php echo $lang['nc_diamond_manage'];?></h3>
        <h5></h5>
      </div>
    </div>
  </div>
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span>
    </div>
    <ul>
      <li><?php echo $lang['spec_index_prompts_one'];?></li>
      <li><?php echo $lang['spec_index_prompts_two'];?></li>
    </ul>
  </div>
  <div id="flexigrid"></div>
</div>
<script type="text/javascript">
$(function(){
    $("#flexigrid").flexigrid({
        url: 'index.php?act=diamond&op=get_xml',
        colModel : [
            {display: '操作', name : 'operation', width : 150, sortable : false, align: 'center', className: 'handle'},
            {display: '形状', name : 'shape', width : 70, sortable : true, align: 'left'},
            {display: '石重', name : 'carat', width : 60, sortable : true, align: 'left'},
            {display: '颜色', name : 'color', width : 60, sortable : true, align: 'center'},
            {display: '净度', name : 'clarity', width : 60, sortable : true, align: 'left'},
            {display: '切工', name : 'cut', width : 60, sortable : true, align: 'left'},
            {display: '对称', name : 'symmetry', width : 60, sortable : true, align: 'left'},
            {display: '抛光', name : 'polish', width : 60, sortable : true, align: 'left'},
            {display: '荧光', name : 'fluorescence', width : 60, sortable : true, align: 'left'},
            {display: '珂兰价', name : 'shop_price', width : 90, sortable : true, align: 'left'},
            {display: '证书类型', name : 'cert', width : 80, sortable : true, align: 'left'},
            {display: '证书号', name : 'cert_id', width : 110, sortable : true, align: 'left'},
            {display: '货品类型', name : 'good_type', width : 60, sortable : true, align: 'left'},
            {display: '上架状态', name : 'status', width : 60, sortable : true, align: 'left'}
            ],
        buttons : [
            //{display: '<i class="fa fa-plus"></i>新增数据', name : 'add', bclass : 'add', title : '新增数据', onpress : fg_operation }
        ],
        searchitems : [
            {display: '形状', name : 'shape'},
            {display: '石重', name : 'carat'},
            {display: '颜色', name : 'color'},
            {display: '净度', name : 'clarity'},
            {display: '切工', name : 'cut'},
            {display: '对称', name : 'symmetry'},
            {display: '抛光', name : 'polish'},
            {display: '荧光', name : 'fluorescence'},
            {display: '珂兰价', name : 'shop_price'},
            {display: '证书类型', name : 'cert'},
            {display: '证书号', name : 'cert_id'},
            {display: '货品类型', name : 'good_type'},
            {display: '上架状态', name : 'status'}
            ],
        sortname: "shop_price",
        sortorder: "asc",
        title: '裸钻列表'
    });
});

function fg_operation(name, bDiv) {
    if (name == 'add') {
        window.location.href = 'index.php?act=spec&op=spec_add';
    }
}
function fg_del(id) {
    if(confirm('删除后将不能恢复，确认删除这项吗？')){
        $.getJSON('index.php?act=spec&op=spec_del', {id:id}, function(data){
            if (data.state) {
                $("#flexigrid").flexReload();
            } else {
                showError(data.msg)
            }
        });
    }
}
</script>