<?php defined('INTELLIGENT_SYS') or exit('Access Invalid!');?>
<!doctype html>
<html lang="zh">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>">
<title><?php echo $output['html_title'];?></title>
<meta name="keywords" content="<?php echo $output['seo_keywords']; ?>" />
<meta name="description" content="<?php echo $output['seo_description']; ?>" />
<meta name="author" content="kelan">
<meta name="copyright" content="kelan Inc. All Rights Reserved">
<?php echo html_entity_decode($output['setting_config']['qq_appcode'],ENT_QUOTES); ?><?php echo html_entity_decode($output['setting_config']['sina_appcode'],ENT_QUOTES); ?><?php echo html_entity_decode($output['setting_config']['share_qqzone_appcode'],ENT_QUOTES); ?><?php echo html_entity_decode($output['setting_config']['share_sinaweibo_appcode'],ENT_QUOTES); ?>
<style type="text/css">
body { _behavior: url(<?php echo LOGIN_TEMPLATES_URL;
?>/css/csshover.htc);
}
</style>
<link href="<?php echo LOGIN_TEMPLATES_URL;?>/css/base.css" rel="stylesheet" type="text/css">
<link href="<?php echo LOGIN_TEMPLATES_URL;?>/css/home_header.css" rel="stylesheet" type="text/css">
<link href="<?php echo LOGIN_TEMPLATES_URL;?>/css/home_login.css" rel="stylesheet" type="text/css">
<link href="<?php echo LOGIN_RESOURCE_SITE_URL;?>/font/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
<!--[if IE 7]>
  <link rel="stylesheet" href="<?php echo LOGIN_RESOURCE_SITE_URL;?>/font/font-awesome/css/font-awesome-ie7.min.css">
<![endif]-->
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
      <script src="<?php echo RESOURCE_SITE_URL_HTTPS;?>/js/html5shiv.js"></script>
      <script src="<?php echo RESOURCE_SITE_URL_HTTPS;?>/js/respond.min.js"></script>
<![endif]-->
<script>
var COOKIE_PRE = '<?php echo COOKIE_PRE;?>';var _CHARSET = '<?php echo strtolower(CHARSET);?>';var SITEURL = '<?php echo SHOP_SITE_URL;?>';var SHOP_SITE_URL = '<?php echo SHOP_SITE_URL;?>';var RESOURCE_SITE_URL = '<?php echo RESOURCE_SITE_URL;?>';var RESOURCE_SITE_URL = '<?php echo RESOURCE_SITE_URL;?>';var SHOP_TEMPLATES_URL = '<?php echo SHOP_TEMPLATES_URL;?>';
</script>
<script src="<?php echo RESOURCE_SITE_URL_HTTPS;?>/js/jquery.js"></script>
<script src="<?php echo RESOURCE_SITE_URL_HTTPS;?>/js/jquery-ui/jquery.ui.js"></script>
<script src="<?php echo RESOURCE_SITE_URL_HTTPS;?>/js/common.js"></script>
<script src="<?php echo RESOURCE_SITE_URL_HTTPS;?>/js/jquery.validation.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL_HTTPS;?>/js/dialog/dialog.js" id="dialog_js"></script>
<script src="<?php echo LOGIN_RESOURCE_SITE_URL?>/js/taglibs.js"></script>
<script src="<?php echo LOGIN_RESOURCE_SITE_URL?>/js/tabulous.js"></script>
</head>
<body>
<div class="header-wrap">
  <header class="public-head-layout wrapper">
    <h1 class="site-logo"><a href="<?php echo SHOP_SITE_URL;?>"><img src="<?php echo UPLOAD_SITE_URL_HTTPS.DS.ATTACH_COMMON.DS.$output['setting_config']['site_logo']; ?>" class="pngFix"></a></h1>
    <?php if ($output['hidden_login'] != '1') {?>
    <?php if ($_GET['op'] == 'index') {?>
    <div class="nc-regist-now">
    <span class="avatar"><img src="<?php echo getMemberAvatarHttps(cookie('member_avatar'));?>"></span>
    <span>您好，欢迎来到<?php echo $output['setting_config']['site_name']; ?><br/><?php echo $lang['login_index_regist_now_1'];?><a title="" href="<?php echo urlLogin('login', 'register', array('ref_url' => $_GET['ref_url']));?>" class="register"><?php echo $lang['login_index_regist_now_2'];?></a></span></div>
    <?php } else {?>
    <div class="nc-login-now"><?php echo $lang['login_register_login_now_1'];?><a href="<?php echo urlLogin('login', 'index', array('ref_url' => $_GET['ref_url']));?>" title="<?php echo $lang['login_register_login_now'];?>" class="register"><?php echo $lang['login_register_login_now_2'];?></a></span></div>
    <?php }?>
    <?php }?>
  </header>
</div>
<!-- PublicHeadLayout End -->
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<?php require_once($tpl_file);?>
<?php require_once template('layout/footer_https');?>
</body>
</html>
