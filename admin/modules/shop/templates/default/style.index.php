<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>款式管理</h3>
        <h5>可批量对款号进行推荐管理</h5>
      </div>
      <ul class="tab-base nc-row">
        <li><a href="index.php?act=style&op=index" <?php if($output['list_type']=="index"){?>class="current"<?php }?>>款式列表</a></li>
        <li><a href="index.php?act=style&op=recommend_list" <?php if($output['list_type']=="recommend"){?>class="current"<?php }?>>推荐列表</a></li>
      </ul>
    </div>
  </div>
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
      <li><?php echo $lang['brand_index_help1'];?></li>
      <li><?php echo $lang['brand_index_help2'];?></li>
      <li><?php echo $lang['brand_index_help3'];?></li>
    </ul>
  </div>
  <div id="flexigrid"></div>
</div>
<script type="text/javascript">
$(function(){
    $("#flexigrid").flexigrid({
        url: '<?php echo !empty($output['list_url'])?$output['list_url']:'index.php?act=style&op=get_xml'?>',
        colModel : [
            {display: '操作', name : 'operation', width : 150, sortable : false, align: 'center', className: 'handle'},
            {display: '款号', name : 'style_sn', width : 100, sortable : true, align: 'center'},			
            {display: '款式名称', name : 'style_name', width : 150, sortable : false, align: 'left'},			
            {display: '产品线', name : 'prodcut_type', width : 120, sortable : true, align: 'center'},
            {display: '款式分类', name : 'cat_type', width : 120, sortable : false, align: 'left'},
			{display: '性别', name : 'style_sex', width : 40, sortable : false, align: 'left'},
			{display: '是否推荐', name : 'is_recommend', width: 60, sortable : true, align : 'center'},
            {display: '是否定制', name : 'is_made', width: 60, sortable : true, align : 'center'},			
			{display: '畅销量', name : 'goods_salenum', width: 60, sortable : true, align : 'center'},
			{display: '人气量', name : 'goods_click', width: 60, sortable : true, align : 'center'},			
			{display: '状态', name : 'style_status', width: 60, sortable : true, align : 'center'},
            {display: '添加时间', name : 'create_time', width : 120, sortable : true, align: 'center'},
			{display: '更新时间', name : 'modify_time', width : 120, sortable : true, align: 'center'}
            ],
        buttons : [
		    <?php if($output['list_type']!="recommend"){?>
			{display: '<i class="fa fa-cog"></i>批量推荐', name : 'recommend', bclass : 'recommend', title : '将选定行数据推荐', onpress : fg_operation },
            <?php }?>
			{display: '<i class="fa fa-trash"></i>取消推荐', name : 'unrecommend', bclass : 'recommend', title : '将选定行数据推荐', onpress : fg_operation },
			//{display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'csv', bclass : 'csv', title : '将选定行数据导出CVS文件', onpress : fg_operation }						
        ],
        searchitems : [
            {display: '款号', name : 'style_sn', isdefault: true},
            {display: '款式名称', name : 'style_name'},
            ],
        sortname: "style_id",
        sortorder: "desc",
        title: '款式列表'
    });
});

function fg_operation(name, bDiv) {
    //批量操作 parts1 
    if (name == 'csv') {
        if ($('.trSelected', bDiv).length == 0) {
            if (!confirm('您确定要下载全部数据吗？')) {
                return false;
            }
        }
        var itemids = new Array();
        $('.trSelected', bDiv).each(function(i){
            itemids[i] = $(this).attr('data-id');
        });
        fg_csv(itemids);
		return ;
    }
	
	//批量操作 parts2 	
	var itemids = new Array();
	$('.trSelected', bDiv).each(function(i){
		itemids[i] = $(this).attr('data-id');
	});	
	if(itemids.length ==0){
		 alert("请选择要操作的对象!");
		 return false;
	}
	if(name == 'recommend'){	    
		fg_recommend(itemids);
	}else if(name=='unrecommend'){
	    fg_unrecommend(itemids);
	}
	
}

function fg_csv(ids) {
    id = ids.join(',');
    window.location.href = $("#flexigrid").flexSimpleSearchQueryString()+'&op=export_csv&id=' + id;
}
//推荐,支持批量
function fg_recommend(id){
     $.getJSON('index.php?act=style&op=recommend', {id:id}, function(data){
        if (data.state) {
		    showSucc("操作成功");
            $("#flexigrid").flexReload();
        } else {
            showError(data.msg);
        }
    });
}
//取消推荐，支持批量
function fg_unrecommend(id){
	$.getJSON('index.php?act=style&op=canncel_recommend', {id:id}, function(data){
        if (data.state) {
		    showSucc("操作成功");
            $("#flexigrid").flexReload();
        } else {
            showError(data.msg);
        }
    });
}
</script>
