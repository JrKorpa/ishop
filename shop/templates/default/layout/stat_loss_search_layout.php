<link href="<?php echo RESOURCE_SITE_URL;?>/js/chosen/css/chosen.css" rel="stylesheet" type="text/css">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/chosen/js/chosen.jquery.js"></script>

<form method="get" action="index.php" target="_self">
  <table class="search-form">
    <input type="hidden" name="act" value="statistics_loss" />
    <input type="hidden" name="op" value="<?php echo $output['search_url']; ?>" />
    <tr>
    	<td class="tr">
		
		   
			
			

    		<div class="fr">
    			<label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_common_search'];?>" /></label>
    		</div>
    		
    		<div class="fr">
        		<div class="fl" style="margin-right:3px;">
            		<select name="search_type" id="search_type" class="querySelect">
            			<option value="day" <?php echo $output['search_arr']['search_type']=='day'?'selected':''; ?>>按照天统计</option>
            			<option value="week" <?php echo $output['search_arr']['search_type']=='week'?'selected':''; ?>>按照周统计</option>
            			<option value="month" <?php echo $output['search_arr']['search_type']=='month'?'selected':''; ?>>按照月统计</option>
            		</select>
        		</div>
        		<div id="searchtype_day" style="display:none;" class="fl">
        			<input type="text" class="text w70" name="search_time" id="search_time" value="<?php echo @date('Y-m-d',$output['search_arr']['day']['search_time']);?>" /><label class="add-on"><i class="icon-calendar"></i></label>
                </div>
                <div id="searchtype_week" style="display:none;" class="fl">
                  	<select name="searchweek_year" class="querySelect">
                  		<?php foreach ($output['year_arr'] as $k=>$v){?>
                  		<option value="<?php echo $k;?>" <?php echo $output['search_arr']['week']['current_year'] == $k?'selected':'';?>><?php echo $v; ?></option>
                  		<?php } ?>
                    </select>
                    <select name="searchweek_month" class="querySelect">
                    	<?php foreach ($output['month_arr'] as $k=>$v){?>
                  		<option value="<?php echo $k;?>" <?php echo $output['search_arr']['week']['current_month'] == $k?'selected':'';?>><?php echo $v; ?></option>
                  		<?php } ?>
                    </select>
                    <select name="searchweek_week" class="querySelect">
                    	<?php foreach ($output['week_arr'] as $k=>$v){?>
                  		<option value="<?php echo $v['key'];?>" <?php echo $output['search_arr']['week']['current_week'] == $v['key']?'selected':'';?>><?php echo $v['val']; ?></option>
                  		<?php } ?>
                    </select>
              </div>
              <div id="searchtype_month" style="display:none;" class="fl">
                  	<select name="searchmonth_year" class="querySelect">
                  		<?php foreach ($output['year_arr'] as $k=>$v){?>
                  		<option value="<?php echo $k;?>" <?php echo $output['search_arr']['month']['current_year'] == $k?'selected':'';?>><?php echo $v; ?></option>
                  		<?php } ?>
                    </select>
                    <select name="searchmonth_month" class="querySelect">
                    	<?php foreach ($output['month_arr'] as $k=>$v){?>
                  		<option value="<?php echo $k;?>" <?php echo $output['search_arr']['month']['current_month'] == $k?'selected':'';?>><?php echo $v; ?></option>
                  		<?php } ?>
                    </select>
              </div>
    		</div>
			
			<div class="fr">&nbsp;
			   客户来源：
              	<select name="source_id" id="source_id" class="querySelect" >
                  <option value="" <?php echo $_REQUEST['source_id']==''?'selected':''; ?>>请选择</option>
				  
                </select>
    		</div>
			<div class="fr">&nbsp;
			   来源分类：
              	<select name="fenlei" id="fenlei" class="querySelect">
                  <option value="" <?php echo $_REQUEST['fenlei']=='0'?'selected':''; ?>>全部</option> 
				  <?php
                   foreach($output['cs_fenlei'] as $k=>$v){
				  ?>
				  <option value="<?php echo $k; ?>" <?php echo $_REQUEST['fenlei']== $k ? 'selected':''; ?>><?php echo $v; ?></option>  
				   <?php } ?>
				 
				
                </select>
    		</div>
			&nbsp;
			
    	</td>
    </tr>
  </table>
</form>

<script type="text/javascript">
//展示搜索时间框
function show_searchtime(){
	s_type = $("#search_type").val();
	$("[id^='searchtype_']").hide();
	$("#searchtype_"+s_type).show();
}

$(function(){
	//Ajax提示
    $('.tip').poshytip({
        className: 'tip-yellowsimple',
        showTimeout: 1,
        alignTo: 'target',
        alignX: 'center',
        alignY: 'top',
        offsetY: 5,
        allowTipHover: false
    });
    
	//切换登录卡
	$('#stat_tabs').tabs();
	
	//统计数据类型
	var s_type = $("#search_type").val();
	$('#search_time').datepicker({dateFormat: 'yy-mm-dd'});

	show_searchtime();
	$("#search_type").change(function(){
		show_searchtime();
	});
	
	//更新周数组
	$("[name='searchweek_month']").change(function(){
		var year = $("[name='searchweek_year']").val();
		var month = $("[name='searchweek_month']").val();
		$("[name='searchweek_week']").html('');
		$.getJSON('index.php?act=index&op=getweekofmonth',{y:year,m:month},function(data){
	        if(data != null){
	        	for(var i = 0; i < data.length; i++) {
	        		$("[name='searchweek_week']").append('<option value="'+data[i].key+'">'+data[i].val+'</option>');
			    }
	        }
	    });
	});

	
	var fenlei = "<?php echo isset($_REQUEST['fenlei']) ? $_REQUEST['fenlei'] : "";?>";
	get_sources_list(fenlei);
	function get_sources_list(fenlei){
		$("[name='source_id']").html('<option value="">请选择</option>').trigger("chosen:updated"); ;	
		$.getJSON('index.php?act=statistics_sale&op=get_sources_list',{fenlei:fenlei},function(data){
	        if(data != null){
	        	for(var i = 0; i < data.length; i++) {
					if(data[i].id == "<?php echo $_REQUEST['source_id'];?>"){
						$("[name='source_id']").append('<option selected value="'+data[i].id+'">'+data[i].source_name+'</option>');
					}else{
						$("[name='source_id']").append('<option value="'+data[i].id+'">'+data[i].source_name+'</option>');
					}
	        		
			    }
	        }
			//下拉美化（可搜索）
			$("[name='source_id']").chosen().trigger("chosen:updated");
	    });
	}
	
	//更新客户来源数组
	$("[name='fenlei']").change(function(){
		var fenlei = $(this).val();
		get_sources_list(fenlei);	
		
	});
	
});
</script>
