<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>提现管理</h3>
        <h5>提现管理，提现审核及提现账号列表</h5>
      </div>
      <ul class="tab-base nc-row">
        <li><a href="index.php?act=withdraw&op=withdraw">提现列表</a></li>
        <li><a href="JavaScript:void(0);" class="current">待审核列表</a></li>
        <li><a href="index.php?act=withdraw&op=withdraw_zh">提现账户列表</a></li>
      </ul>
    </div>
  </div>
  <div id="flexigrid"></div>
</div>
<script type="text/javascript">
$(function(){
    $("#flexigrid").flexigrid({
        url: 'index.php?act=withdraw&op=get_xml&type=0',
        colModel : [
            {display: '操作', name : 'operation', width : 120, sortable : true, align: 'left'},
            {display: '代理商', name : 'agent_name', width : 60, sortable : true, align: 'center'},
            {display: '金额', name : 'money', width : 60, sortable : true, align: 'center'},
            {display: '状态', name : 'status_name', width : 60, sortable : true, align: 'center'},
            {display: '银行名称', name : 'account_num', width : 150, sortable : true, align: 'center'},
            {display: '银行账户', name : 'bank_name', width : 120, sortable : false, align: 'left'},
            {display: '真实姓名', name : 'realname', width : 80, sortable : false, align: 'left'},
            {display: '手机号', name : 'mobile', width: 100, sortable : true, align : 'center'},
            {display: '申请时间', name : 'create_time', width: 120, sortable : true, align : 'center'},
            {display: '处理时间', name : 'deal_timein', width : 120, sortable : true, align: 'center'}
            ],
        searchitems : [
            {display: '代理商名称', name : 'agent_name', isdefault: true},
            ],
        sortname: "id",
        sortorder: "desc",
        title: '提现列表'
    });
});
function tongguo(id){
	$.ajax({
        type:"POST",
        url:'index.php?act=withdraw&op=tongguo',  
        async:false,  
        data:{
            'id': id,
            },
//         dataType: 'html',
        success: function(data){
            if(!data){
            	alert("审核失败");
    		}else{
        		alert("已通过");
            	document.location.reload();
    		}
        }  
    });  
}
function butongguo(id){
	$.ajax({
        type:"POST",
        url:'index.php?act=withdraw&op=butongguo',  
        async:false,  
        data:{
            'id': id,
            },
//         dataType: 'html',
        success: function(data){
            if(!data){
            	alert("审核失败");
    		}else{
        		alert("已拒绝");
            	document.location.reload();
    		}
        }  
    }); 
}
</script>
