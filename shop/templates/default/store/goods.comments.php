<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<?php if(!empty($output['goodsevallist']) && is_array($output['goodsevallist'])){?>
<?php foreach($output['goodsevallist'] as $k=>$v){?>

<div id="t" class="ncs-commend-floor">
  <div class="user-avatar"> <a <?php if($v['geval_isanonymous'] != 1){?>href="index.php?act=member_snshome&mid=<?php echo $v['geval_frommemberid'];?>" target="_blank" data-param="{'id':<?php echo $v['geval_frommemberid'];?>}" nctype="mcard"<?php }?>> <img src="<?php echo getMemberAvatarForID($v['geval_frommemberid']);?>"> </a> </div>
  <dl class="detail">
    <dt> <span class="user-name">
      <?php if($v['geval_isanonymous'] == 1){?>
      <?php echo str_cut($v['geval_frommembername'],2).'***';?>
      <?php }else{?>
      <a href="index.php?act=member_snshome&mid=<?php echo $v['geval_frommemberid'];?>" target="_blank" data-param="{'id':<?php echo $v['geval_frommemberid'];?>}" nctype="mcard"><?php echo $v['geval_frommembername'];?></a>
      <?php }?>
      </span>
      <div class="goods-raty">商品评分：<em class="raty" data-score="<?php echo $v['geval_scores'];?>"></em></div>
    </dt>
    <dd><?php echo $v['geval_content'];?></dd>
    <?php if(!empty($v['geval_image'])) {?>
    <dd>
      <ul class="photos-thumb">
        <?php $image_array = explode(',', $v['geval_image']);?>
        <?php foreach ($image_array as $value) { ?>
        <li><a nctype="nyroModal"  href="<?php echo snsThumb($value, 1024);?>"> <img src="<?php echo snsThumb($value);?>"> </a></li>
        <?php } ?>
      </ul>
    </dd>
    <?php } ?>
    <dd class="pubdate" pubdate="pubdate"><?php echo @date('Y-m-d H:i:s',$v['geval_addtime']);?></dd>
    <?php if (!empty($v['geval_explain'])){?>
    <dd class="explain"><?php echo $lang['nc_credit_explain'];?>：<?php echo $v['geval_explain'];?></dd>
    <?php } ?>
    <?php if ($v['geval_content_again'] != '') {?>
    <dd>[追加评价]&nbsp;<?php echo $v['geval_content_again'];?></dd>
    <?php if(!empty($v['geval_image_again'])) {?>
    <dd>
      <ul class="photos-thumb">
        <?php $image_array = explode(',', $v['geval_image_again']);?>
        <?php foreach ($image_array as $value) { ?>
        <li><a nctype="nyroModal"  href="<?php echo snsThumb($value, 1024);?>"> <img src="<?php echo snsThumb($value);?>"> </a></li>
        <?php } ?>
      </ul>
    </dd>
    <?php } ?>
    <dd class="pubdate">确认收货并评价后 <?php echo ($d = floor($v['geval_addtime_again']/ 60 / 60 / 24) - floor($v['geval_addtime']/ 60 / 60 / 24)) == 0? '当' : $d;?> 天再次追加评价</dd>
    <?php if (!empty($v['geval_explain_again'])){?>
    <dd class="explain"><?php echo $lang['nc_credit_explain'];?>：<?php echo $v['geval_explain_again'];?></dd>
    <?php } ?>
    <?php }?>
    <hr/>
  </dl>
</div>
<?php }?>
<div class="tr pr5 pb5 pr"> <a href="<?php echo urlShop('goods', 'comments_list', array('goods_id' => $_GET['goods_id']));?>" target="_blank" class="more-commend">查看全部评价>></a>
  <div class="pagination"> <?php echo $output['show_page'];?></div>
</div>
<?php }else{?>
<div class="ncs-norecord"><?php echo $lang['no_record'];?></div>
<?php }?>
<script type="text/javascript">
$(document).ready(function(){
   $('.raty').raty({
        path: "<?php echo RESOURCE_SITE_URL;?>/js/jquery.raty/img",
        readOnly: true,
        score: function() {
          return $(this).attr('data-score');
        }
    });

   $('a[nctype="nyroModal"]').nyroModal();

    $('#goodseval').find('.demo').ajaxContent({
        event:'click', //mouseover
        loaderType:"img",
        loadingMsg:"<?php echo SHOP_TEMPLATES_URL;?>/images/transparent.gif",
        target:'#goodseval'
    });
});
</script> 
