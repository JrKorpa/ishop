<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>

<div class="tabmenu">
    <?php include template('layout/submenu');?>

<?php if ($output['isOwnShop']) { ?>
<!--    <a class="ncbtn ncbtn-mint" style="right:100px" href="<?php /*echo urlShop('pointvoucher', 'index', array('store_id' => $_SESSION['store_id']));*/?>" target="_blank"><i class="icon-plus-sign"></i>查看活动列表</a>-->
    <a class="ncbtn ncbtn-mint" href="<?php echo urlShop('store_voucher', 'templateadd');?>"><i class="icon-plus-sign"></i>新增折扣码</a>
<?php } else { ?>
    <?php if(!empty($output['current_quota'])) { ?>
<!--    <a class="ncbtn ncbtn-mint" style="right:200px" href="<?php /*echo urlShop('pointvoucher', 'index', array('store_id' => $_SESSION['store_id']));*/?>" target="_blank"><i class="icon-plus-sign"></i>查看活动列表</a>-->
    <a class="ncbtn ncbtn-mint" style="right:100px" href="<?php echo urlShop('store_voucher', 'templateadd');?>"><i class="icon-plus-sign"></i>新增折扣码</a>
	<a class="ncbtn ncbtn-aqua" href="<?php echo urlShop('store_voucher', 'quotaadd');?>" title=""><i class="icon-money"></i>套餐续费</a>
    <?php } else { ?>
    <a class="ncbtn ncbtn-aqua" href="<?php echo urlShop('store_voucher', 'quotaadd');?>" title=""><i class="icon-money"></i>购买套餐</a>
    <?php } ?>

<?php } ?>
</div>
<div class="alert alert-block mt10 mb10">
	<ul>
        <li>1、手工设置折扣码失效后,用户将不能使用折扣码</li>
        <li>2、折扣码模版和已发放的折扣码过期后自动失效</li>
	</ul>
</div>
<form method="get">
	<table class="search-form">
		<input type="hidden" id='act' name='act' value='store_voucher' />
		<input type="hidden" id='op' name='op' value='templatelist' />
		<tr>
			<td>&nbsp;</td>
			<th><?php echo $lang['voucher_template_enddate'];?></th>
			<td class="w240"><input type="text" class="text w70"
				readonly="readonly" value="<?php echo $_GET['txt_startdate'];?>"
				id="txt_startdate" name="txt_startdate" /><label class="add-on"> <i
					class="icon-calendar"></i>
			</label> &#8211; <input type="text" class="text w70"
				readonly="readonly" value="<?php echo $_GET['txt_enddate'];?>"
				id="txt_enddate" name="txt_enddate" /><label class="add-on"> <i
					class="icon-calendar"></i>
			</label></td>
			<th><?php echo $lang['nc_status'];?></th>
			<td class="w120"><select class="w80" name="select_state">
                    <option value="0"
                        <?php if (!$_GET['select_state'] == '0'){ echo 'selected=true';}?>><?php echo $lang['nc_please_choose'];?></option>
                    <?php if (!empty($output['templatestate_arr'])){?>
                        <?php foreach ($output['templatestate_arr'] as $k=>$v){?>
                            <option value="<?php echo $v[0]; ?>"
                                <?php if ($_GET['select_state'] == $v[0]){echo 'selected=true';}?>><?php echo $v[1];?></option>
                        <?php }?>
                    <?php }?>
                </select></td>
		 		
           <!--   <th  class="w60">折扣码类型</th>
            <td class="w80">
                <select class="w80" name="voucher_t_type">
                    <option value="" >-请选择-</option>
                    <option value="1" <?php //if($_GET['voucher_t_type'] ==1){echo 'selected';}?> >折扣码</option>
                   <option value="2"  <?php //if($_GET['voucher_t_type'] ==2){echo 'selected';}?>>成品定制码</option>
                </select>
            </td>-->
			<th  class="w60">商品类型</th>
            <td class="w80">
                <select class="w80" name="voucher_t_goods_type">
                    <option value="">-请选择</option>
                      <?php echo paramsHelper::echoOption("voucher_goods_type",$_GET['voucher_t_goods_type']) ?>
                </select>
            </td>
			<!--
			<th class="w80">&nbsp;折扣码名称</th>
			<td class="w160"><input type="text" class="text w120" value=""
				id="txt_keyword" name="txt_keyword" /></td>
			-->	
			<td class="tc w70"><label class="submit-border"><input type="submit"
					class="submit" value="<?php echo $lang['nc_search'];?>" /></label></td>
			
	
	</table>
</form>
<table class="ncsc-default-table">
	<thead>
		<tr>
			<th class="w50"></th>
			<th class="tl">折扣码名称</th>
		<!--	<th class="w100"><?php /*echo $lang['voucher_template_orderpricelimit'];*/?></th>-->
		<!--	<th class="w100">会员级别</th>-->
			<th class="w60">折扣值</th>
			<th class="w60">商品类型</th>
			<th class="w200"><?php echo $lang['voucher_template_enddate'];?></th>
	<!--		<th class="w60">领取方式</th>-->
			<th class="w60"><?php echo $lang['nc_status'];?></th>
            <th class="w80">创建时间</th>
			<th class="w150"><?php echo $lang['nc_handle'];?></th>
		</tr>
	</thead>
	<tbody>
      <?php if (count($output['list'])>0) { ?>
      <?php foreach($output['list'] as $val) { ?>
      <tr class="bd-line">
		<!--	<td><div class="pic-thumb">
					<img src="<?php /*echo $val['voucher_t_customimg'];*/?>" />
				</div></td>-->
            <th></th>
			<td class="w50" style="text-align: left;"><?php echo $val['voucher_t_title'];?></td>
		<!--	<td>￥<?php /*echo $val['voucher_t_limit'];*/?></td>-->
	<!--		<td><?php /*echo $val['voucher_t_mgradelimittext'];*/?></td>-->
			<td class="goods-price"><?php echo $val['voucher_t_price'];?><?php echo str_replace(array(1,2),array("%","元"),$val['voucher_t_type']); ?></td>
			<td class="w50" style="text-align: left;"><?php echo paramsHelper::echoOptionText('voucher_goods_type',$val['voucher_t_goods_type']);?></td>
			<td class="goods-time"><?php echo date("Y-m-d",$val['voucher_t_start_date']).'~'.date("Y-m-d",$val['voucher_t_end_date']);?></td>
	<!--		<td><?php /*echo $val['voucher_t_gettype_text'];*/?></td>-->
			<td><?php echo $val['voucher_t_state_text']; ?></td>
          <td><?php echo date('Y-m-d H:i:s',$val['voucher_t_add_date']); ?></td>
			<td class="nscs-table-handle">
        	<?php if($val['voucher_t_state']==$output['templatestate_arr']['usable'][0] && (!$val['voucher_t_giveout']) && (!$val['voucher_t_isbuild'])) {//折扣码模板有效并且没有领取时可以编辑?>
        		<span> <a class="btn-bluejeans"
					href="index.php?act=store_voucher&op=templateedit&tid=<?php echo $val['voucher_t_id'];?>">
						<i class="icon-edit"></i>
						<p>编辑</p>
				</a>
			</span>
        	<?php } ?>
        	<!-- 折扣码详细 -->
        	<span>
        	   <a class="btn-bluejeans" href="index.php?act=store_voucher&op=templateinfo&tid=<?php echo $val['voucher_t_id'];?>"><i class="icon-th-list"></i><p>详细</p></a>
			</span>
        	<?php if ((!$val['voucher_t_giveout']) && (!$val['voucher_t_isbuild'])){//该模板没有发放过折扣码时可以删除?>
        	<span> <a class="btn-grapefruit" href="javascript:void(0)" onclick="ajax_get_confirm('<?php echo $lang['nc_ensure_del'];?>','index.php?act=store_voucher&op=templatedel&tid=<?php echo $val['voucher_t_id'];?>');"><i class="icon-trash"></i><p>删除</p></a></span>
        	<?php }?>
        </td>
		</tr>
      <?php }?>
      <?php } else { ?>
      <tr>
			<td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
		</tr>
      <?php } ?>
    </tbody>
	<tfoot>
      <?php  if (count($output['list'])>0) { ?>
      <tr>
			<td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
		</tr>
      <?php } ?>
    </tfoot>
</table>
<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_SITE_URL."/js/jquery-ui/themes/ui-lightness/jquery.ui.css";?>" />
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" charset="utf-8"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#txt_startdate').datepicker();
	$('#txt_enddate').datepicker();
});
</script>
