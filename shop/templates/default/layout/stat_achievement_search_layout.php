<link href="<?php echo RESOURCE_SITE_URL;?>/js/chosen/css/chosen.css" rel="stylesheet" type="text/css">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/chosen/js/chosen.jquery.js"></script>

<form method="get" action="index.php" target="_self" onsubmit="return formValidator()">
  <table class="search-form">
    <input type="hidden" name="act" value="<?php echo $output['act']; ?>" />
    <input type="hidden" name="op" value="<?php echo $output['op']; ?>" />
    <tr>
    	<td class="tr">
		
		   
			
			

    		<div class="fr">
    			<label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_common_search'];?>" /></label>
    		</div>
    		
    		
			
			<div class="fr">&nbsp;
			   时间范围：
              	<input type="text" class="text w70" name="start_date" id="start_date" value="<?php if(isset($_GET['start_date'])){echo $_GET['start_date'];}else{echo date('Y-m',time())."-01";} ?>" /><label class="add-on"><i class="icon-calendar"></i></label>&nbsp;&#8211;&nbsp;<input id="end_date" class="text w70" type="text" name="end_date" value="<?php if(isset($_GET['end_date'])){echo $_GET['end_date'];}else{echo date('Y-m-d',time());} ?>"  /><label class="add-on"><i class="icon-calendar"></i></label>
    		</div>
			
			&nbsp;
			
    	</td>
    </tr>
  </table>
</form>

<script type="text/javascript">
$(function(){
	$('#start_date').datepicker({dateFormat: 'yy-mm-dd'});
    $('#end_date').datepicker({dateFormat: 'yy-mm-dd'});
	
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
	
	

	
	
	
	
	
});

function formValidator(){
	
	var start_date = $("#start_date").val();
	var mydate1 = new Date(start_date);
	var year1 = mydate1.getFullYear();//获取年
	var month1 = mydate1.getMonth();//获取年
	
	var end_date = $("#end_date").val();
	var mydate2 = new Date(end_date);
	var year2 = mydate2.getFullYear();//获取年
	var month2 = mydate2.getMonth();//获取年
	
	if(year1 != year2 || month1 != month2){
		alert("时间必须选择在同一月内");
		return false;
	}
	
	
}
</script>
