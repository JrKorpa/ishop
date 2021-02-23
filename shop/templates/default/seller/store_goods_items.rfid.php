<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<div class="alert mt15 mb5"><strong>操作提示： 1. 设置防伪码时，为确保货品安全，请用扫码器输入货号;&nbsp;&nbsp;&nbsp;&nbsp;2. 防伪识别时， 不需要输入货号；
</strong>
  <ul>
    <li><?php echo $lang['store_goods_import_csv_desc'];?></li>
  </ul>
</div>
<div class="search">
<table class="search-form">
    <tr>      
      <th>货  号：</th>
      <td class="w120">
        <input type="text" class="text w150" id="item_id" placeholder="请扫码输入" />
      </td>
    </tr>
		<tr>      
      <th>RFID 防伪码：</th>
      <td class="w120">
        <input type="text" class="text w150" id="rfid_tid" placeholder="自动获取" value="" readonly />
      </td>
    </tr>
		<tr>     
			<th></th> 
      <td class="w120" style="text-align:left">
				<label class="submit-border"><input type="button" class="submit" value="设置"  onclick="setRfid();"></label>
				<label class="submit-border"><input type="button" class="submit" value="防伪识别"  onclick="rfid_recognition();"></label>
      </td>
    </tr>
</table>
</div>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/RFIDOCXFunJs.js"></script>
<script>

	function setRfid() {
		var item_id = $('#item_id').val();
		if (!item_id) {
			showError('请扫码填入货号');
			return;
		}

		read(function(tid, epc) {
					$.post('index.php?act=store_goods_items&op=rfid_bind',{ tid: tid, item_id: item_id },function(res){
						var res = $.parseJSON(res);
						if(res.state) {
							write(res.msg, function(is_ok){
								$('#rfid_tid').val(res.data['tid']);
							});
						}else{
							showError(res.msg);
						}
					});
			}); 
	}

	function rfid_recognition() {
		read(function(tid, epc){
			$.post('index.php?act=store_goods_items&op=rfid_recog',{ tid: tid },function(res){
						var res = $.parseJSON(res);
						if(res.state) {
							showSucc(res.msg);
						}else{
							showError(res.msg);
						}
					});
		});
	}

</script>