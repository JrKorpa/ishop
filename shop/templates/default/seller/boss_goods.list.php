<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<style>
    .ncsc-default-table thead th{
        min-width:70px;
        text-align: left;
    }
</style>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<!--<div class="alert mt15 mb5"><strong>操作提示：</strong>
  <ul>
    <li>1、关联版式可以把预设内容插入到商品描述的顶部或者底部，方便商家对商品描述批量添加或修改。</li>
  </ul>
</div>-->
<form method="get">
<input type="hidden" name="act" value="boss_goods_list">
<table class="search-form">
    <tr>
      <th>货号</th>
      <td class="w160"><input type="text" class="text w150" name="goods_id" value="<?php echo $_GET['goods_id']; ?>"/></td>
        <th>款号</th>
        <td class="w160"><input type="text" class="text w150" name="goods_sn" value="<?php echo $_GET['goods_sn']; ?>"/></td>
        <th>商品名称</th>
        <td class="w160"><input type="text" class="text w150" name="goods_name" value="<?php echo $_GET['goods_name']; ?>"  /></td>
        <th>状态</th>
        <td class="w80">
            <select name="is_on_sale" class="select w160">
                <option>请选择</option>
                <option value="1" <?php if (is_numeric($_GET['status']) && 1==$_GET['status']) {?>selected="selected"<?php }?>>未上架</option>
                <option value="2" <?php if (is_numeric($_GET['status']) && 2==$_GET['status']) {?>selected="selected"<?php }?>>可销售</option>
                <option value="3" <?php if (is_numeric($_GET['status']) && 3==$_GET['status']) {?>selected="selected"<?php }?>>已出售</option>
            </select>
        </td>
    </tr>
    <tr>
        <th>证书号</th>
        <td class="w160"><input type="text" class="text w150" name="zhengshuhao" value="<?php echo $_GET['zhengshuhao']; ?>"/></td>
        <th>金托类型</th>
        <td class="w160">
            <select name="tuo_type" class="select w160">
                <option>请选择</option>
                <option value="1"  <?php if (is_numeric($_GET['tuo_type']) && 1==$_GET['status']) {?>selected="selected"<?php }?>>成品</option>
                <option value="2"  <?php if (is_numeric($_GET['tuo_type']) && 2==$_GET['status']) {?>selected="selected"<?php }?>>空托</option>
            </select>
        </td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td class="w70 tc"><label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" /></label></td>
    </tr>

</table>
</form>

<div style="width: 100%;overflow-x: scroll;">
    <table class="ncsc-default-table" width="959">
      <thead>
        <tr>
          <th style="width: 150px;">货号</th>
          <th>款号</th>
          <th>商品名称</th>
          <th>产品线</th>
          <th>款式分类</th>
          <th>入库方式</th>
          <th>状态</th>
          <th>所在仓库</th>
          <th>数量</th>
          <th>价格</th>
          <th>材质</th>
          <th>材质颜色</th>
          <th>指圈</th>
          <th>金托类型</th>
          <th>主石</th>
          <th>主石单颗重</th>
          <th>主石粒数</th>
          <th>证书号</th>
          <th>证书类型</th>
          <th>品牌</th>
          <th>主石形状</th>
          <th>主石颜色</th>
          <th>主石净度</th>
          <th>主石切工</th>
          <th>副石1</th>
          <th>副石1粒数</th>
          <th>副石2</th>
          <th>副石2粒数</th>
          <th>副石2重</th>
          <th>副石3</th>
          <th>副石3粒数</th>
          <th>副石3重</th>
          <th>经销商批发价</th>
          <th>管理费</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($output['goods_list'])) { ?>
        <?php foreach($output['goods_list'] as $val) { ?>
        <tr class="bd-line">
            <th><?php echo $val["goods_id"] ?></th>
            <th><?php echo $val["goods_sn"] ?></th>
            <th>  <?php echo $val["goods_name"] ?></th>
            <th> <?php echo $val["product_type1"] ?></th>
            <th> <?php echo  $val["cat_type"] ?></th>
            <th><?php echo  str_replace(array(1,2,3,4,5),array('购买','委托加工','代销','借入','自采'),$val["put_in_type"]);  ?></th>
            <th><?php echo goodsStatusName($val["is_on_sale"]); ?></th>
            <th>   <?php echo $val["warehouse"] ?> </th>
            <th>  <?php echo $val["num"] ?></th>
            <th>  <?php echo $val["zuixinlingshoujia"] ?></th>
            <th>   <?php echo $val["caizhi"] ?></th>
            <th>    <?php echo $val["yanse"] ?></th>
            <th>    <?php echo $val["shoucun"] ?></th>
            <th>    <?php echo str_replace(array(1,2),array('成品','空托'),$val["tuo_type"]); ?></th>
            <th>     <?php echo $val["zhushi"] ?></th>
            <th>    <?php echo $val["zuanshidaxiao"] ?></th>
            <th> <?php echo $val["zhushilishu"] ?></th>
            <th>  <?php echo $val["zhengshuhao"] ?></th>
            <th> <?php echo $val["zhengshuleibie"] ?></th>
            <th> <?php echo $val["pinpai"] ?></th>
            <th><?php echo $val["zhushixingzhuang"] ?></th>
            <th>  <?php echo $val["zhushiyanse"] ?></th>
            <th>   <?php echo $val["zhushijingdu"] ?></th>
            <th>   <?php echo $val["zhushiqiegong"] ?></th>
            <th>   <?php echo $val["fushi"] ?></th>
            <th>    <?php echo $val["fushilishu"] ?></th>
            <th>     <?php echo $val["shi2"] ?></th>
            <th>   <?php echo $val["shi2lishu"] ?></th>
            <th> <?php echo $val["shi2zhong"] ?></th>
            <th><?php echo $val["shi3"] ?></th>
            <th><?php echo $val["shi3lishu"] ?></th>
            <th><?php echo $val["shi3zhong"] ?></th>
            <th> <?php echo $val["jingxiaoshangchengbenjia"] ?></th>
            <th><?php echo $val["management_fee"] ?></th>
        </tr>
        <?php } ?>
        <?php } else { ?>
        <tr>
          <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
        </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <?php if (!empty($output['goods_list'])) { ?>
        <tr>
          <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
        </tr>
        <?php } ?>
      </tfoot>
    </table>
</div>
