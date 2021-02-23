/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : ishop

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2021-02-23 11:21:43
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for activity
-- ----------------------------
DROP TABLE IF EXISTS `activity`;
CREATE TABLE `activity` (
  `activity_id` mediumint(9) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `activity_title` varchar(255) NOT NULL COMMENT '标题',
  `activity_type` enum('1','2') DEFAULT NULL COMMENT '活动类型 1:商品 2:抢购',
  `activity_banner` varchar(255) NOT NULL COMMENT '活动横幅大图片',
  `activity_style` varchar(255) NOT NULL COMMENT '活动页面模板样式标识码',
  `activity_desc` varchar(1000) DEFAULT NULL COMMENT '描述',
  `activity_start_date` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `activity_end_date` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `activity_sort` tinyint(1) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  `activity_state` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '活动状态 0为关闭 1为开启',
  PRIMARY KEY (`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='活动表';

-- ----------------------------
-- Records of activity
-- ----------------------------

-- ----------------------------
-- Table structure for activity_detail
-- ----------------------------
DROP TABLE IF EXISTS `activity_detail`;
CREATE TABLE `activity_detail` (
  `activity_detail_id` mediumint(9) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `activity_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '活动编号',
  `item_id` int(11) NOT NULL COMMENT '商品或抢购的编号',
  `item_name` varchar(255) NOT NULL COMMENT '商品或抢购名称',
  `store_id` int(11) NOT NULL COMMENT '店铺编号',
  `store_name` varchar(255) NOT NULL COMMENT '店铺名称',
  `activity_detail_state` enum('0','1','2','3') NOT NULL DEFAULT '0' COMMENT '审核状态 0:(默认)待审核 1:通过 2:未通过 3:再次申请',
  `activity_detail_sort` tinyint(1) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  PRIMARY KEY (`activity_detail_id`),
  KEY `activity_id` (`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='活动细节表';

-- ----------------------------
-- Records of activity_detail
-- ----------------------------

-- ----------------------------
-- Table structure for address
-- ----------------------------
DROP TABLE IF EXISTS `address`;
CREATE TABLE `address` (
  `address_id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '地址ID',
  `member_id` mediumint(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `true_name` varchar(50) NOT NULL COMMENT '会员姓名',
  `area_id` mediumint(10) unsigned NOT NULL DEFAULT '0' COMMENT '地区ID',
  `city_id` mediumint(9) DEFAULT NULL COMMENT '市级ID',
  `provice_id` mediumint(10) DEFAULT NULL COMMENT '省份ID',
  `area_info` varchar(255) NOT NULL DEFAULT '' COMMENT '地区内容',
  `address` varchar(255) NOT NULL COMMENT '地址',
  `tel_phone` varchar(20) DEFAULT NULL COMMENT '座机电话',
  `mob_phone` varchar(15) DEFAULT NULL COMMENT '手机电话',
  `is_default` enum('0','1') NOT NULL DEFAULT '0' COMMENT '1默认收货地址',
  `dlyp_id` int(11) DEFAULT '0' COMMENT '自提点ID',
  PRIMARY KEY (`address_id`),
  UNIQUE KEY `mob_phone` (`mob_phone`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='买家地址信息表';

-- ----------------------------
-- Records of address
-- ----------------------------

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `admin_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员ID',
  `admin_name` varchar(20) NOT NULL COMMENT '管理员名称',
  `admin_avatar` varchar(100) DEFAULT NULL COMMENT '管理员头像',
  `admin_password` varchar(32) NOT NULL DEFAULT '' COMMENT '管理员密码',
  `admin_login_time` int(10) NOT NULL DEFAULT '0' COMMENT '登录时间',
  `admin_login_num` int(11) NOT NULL DEFAULT '0' COMMENT '登录次数',
  `admin_is_super` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否超级管理员',
  `admin_gid` smallint(6) DEFAULT '0' COMMENT '权限组ID',
  `admin_quick_link` varchar(400) DEFAULT NULL COMMENT '管理员常用操作',
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员表';

-- ----------------------------
-- Records of admin
-- ----------------------------

-- ----------------------------
-- Table structure for admin_log
-- ----------------------------
DROP TABLE IF EXISTS `admin_log`;
CREATE TABLE `admin_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` varchar(50) NOT NULL COMMENT '操作内容',
  `createtime` int(10) unsigned DEFAULT NULL COMMENT '发生时间',
  `admin_name` char(20) NOT NULL COMMENT '管理员',
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `ip` char(15) NOT NULL COMMENT 'IP',
  `url` varchar(50) NOT NULL DEFAULT '' COMMENT '来源URL',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员操作日志';

-- ----------------------------
-- Records of admin_log
-- ----------------------------

-- ----------------------------
-- Table structure for adv
-- ----------------------------
DROP TABLE IF EXISTS `adv`;
CREATE TABLE `adv` (
  `adv_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '广告自增标识编号',
  `ap_id` mediumint(8) unsigned NOT NULL COMMENT '广告位id',
  `adv_title` varchar(255) NOT NULL COMMENT '广告内容描述',
  `adv_content` varchar(1000) NOT NULL COMMENT '广告内容',
  `adv_start_date` int(10) DEFAULT NULL COMMENT '广告开始时间',
  `adv_end_date` int(10) DEFAULT NULL COMMENT '广告结束时间',
  `slide_sort` int(10) unsigned NOT NULL COMMENT '幻灯片排序',
  `member_id` int(11) NOT NULL COMMENT '会员ID',
  `member_name` varchar(50) NOT NULL COMMENT '会员用户名',
  `click_num` int(10) unsigned NOT NULL COMMENT '广告点击率',
  `is_allow` smallint(1) unsigned NOT NULL COMMENT '会员购买的广告是否通过审核0未审核1审核已通过2审核未通过',
  `buy_style` varchar(10) NOT NULL COMMENT '购买方式',
  `goldpay` int(10) unsigned NOT NULL COMMENT '购买所支付的金币',
  PRIMARY KEY (`adv_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='广告表';

-- ----------------------------
-- Records of adv
-- ----------------------------

-- ----------------------------
-- Table structure for adv_position
-- ----------------------------
DROP TABLE IF EXISTS `adv_position`;
CREATE TABLE `adv_position` (
  `ap_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '广告位置id',
  `ap_name` varchar(100) NOT NULL COMMENT '广告位置名',
  `ap_intro` text,
  `ap_price` decimal(10,2) DEFAULT NULL,
  `ap_class` smallint(1) unsigned NOT NULL COMMENT '广告类别：0图片1文字2幻灯3Flash',
  `ap_display` smallint(1) unsigned NOT NULL COMMENT '广告展示方式：0幻灯片1多广告展示2单广告展示',
  `is_use` smallint(1) unsigned NOT NULL COMMENT '广告位是否启用：0不启用1启用',
  `ap_width` int(10) NOT NULL COMMENT '广告位宽度',
  `ap_height` int(10) NOT NULL COMMENT '广告位高度',
  `adv_num` int(10) unsigned NOT NULL COMMENT '拥有的广告数',
  `click_num` int(10) unsigned NOT NULL COMMENT '广告位点击率',
  `default_content` varchar(100) NOT NULL COMMENT '广告位默认内容',
  PRIMARY KEY (`ap_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='广告位表';

-- ----------------------------
-- Records of adv_position
-- ----------------------------

-- ----------------------------
-- Table structure for album_class
-- ----------------------------
DROP TABLE IF EXISTS `album_class`;
CREATE TABLE `album_class` (
  `aclass_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '相册id',
  `aclass_name` varchar(100) NOT NULL COMMENT '相册名称',
  `store_id` int(10) unsigned NOT NULL COMMENT '所属店铺id',
  `aclass_des` varchar(255) NOT NULL COMMENT '相册描述',
  `aclass_sort` tinyint(3) unsigned NOT NULL COMMENT '排序',
  `aclass_cover` varchar(255) NOT NULL COMMENT '相册封面',
  `upload_time` int(10) unsigned NOT NULL COMMENT '图片上传时间',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为默认相册,1代表默认',
  PRIMARY KEY (`aclass_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='相册表';

-- ----------------------------
-- Records of album_class
-- ----------------------------

-- ----------------------------
-- Table structure for album_pic
-- ----------------------------
DROP TABLE IF EXISTS `album_pic`;
CREATE TABLE `album_pic` (
  `apic_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '相册图片表id',
  `apic_name` varchar(100) NOT NULL COMMENT '图片名称',
  `apic_tag` varchar(255) DEFAULT '' COMMENT '图片标签',
  `aclass_id` int(10) unsigned NOT NULL COMMENT '相册id',
  `apic_cover` varchar(255) NOT NULL COMMENT '图片路径',
  `apic_size` int(10) unsigned NOT NULL COMMENT '图片大小',
  `apic_spec` varchar(100) NOT NULL COMMENT '图片规格',
  `store_id` int(10) unsigned NOT NULL COMMENT '所属店铺id',
  `upload_time` int(10) unsigned NOT NULL COMMENT '图片上传时间',
  PRIMARY KEY (`apic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='相册图片表';

-- ----------------------------
-- Records of album_pic
-- ----------------------------

-- ----------------------------
-- Table structure for apiseccode
-- ----------------------------
DROP TABLE IF EXISTS `apiseccode`;
CREATE TABLE `apiseccode` (
  `sec_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `sec_key` varchar(50) NOT NULL COMMENT '验证码标识',
  `sec_val` varchar(100) NOT NULL COMMENT '验证码值',
  `sec_addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`sec_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of apiseccode
-- ----------------------------

-- ----------------------------
-- Table structure for app_attribute
-- ----------------------------
DROP TABLE IF EXISTS `app_attribute`;
CREATE TABLE `app_attribute` (
  `attribute_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '属性id',
  `attribute_name` varchar(20) NOT NULL COMMENT '属性名称',
  `attribute_code` varchar(30) NOT NULL COMMENT '属性编码',
  `show_type` tinyint(1) NOT NULL COMMENT '展示方式：1文本框，2单选，3多选，4下拉',
  `attribute_status` tinyint(2) NOT NULL COMMENT '状态:1启用;0停用',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `create_user` varchar(10) NOT NULL COMMENT '创建人',
  `attribute_remark` varchar(50) DEFAULT NULL COMMENT '记录备注',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of app_attribute
-- ----------------------------

-- ----------------------------
-- Table structure for app_attribute_value
-- ----------------------------
DROP TABLE IF EXISTS `app_attribute_value`;
CREATE TABLE `app_attribute_value` (
  `att_value_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '属性值ID',
  `attribute_id` int(10) NOT NULL COMMENT '属性id',
  `att_value_name` varchar(50) NOT NULL COMMENT '属性值名称',
  `att_value_status` tinyint(2) NOT NULL COMMENT '状态:1启用;0停用',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `create_user` varchar(10) NOT NULL COMMENT '创建人',
  `att_value_remark` varchar(50) DEFAULT NULL COMMENT '记录备注',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`att_value_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of app_attribute_value
-- ----------------------------

-- ----------------------------
-- Table structure for app_cat_type
-- ----------------------------
DROP TABLE IF EXISTS `app_cat_type`;
CREATE TABLE `app_cat_type` (
  `cat_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '部门id',
  `cat_type_name` char(50) NOT NULL COMMENT '部门名称',
  `cat_type_code` char(50) DEFAULT NULL COMMENT '部门编码',
  `note` varchar(255) DEFAULT NULL COMMENT '描述',
  `parent_id` int(11) NOT NULL COMMENT '上级部门id',
  `tree_path` varchar(512) NOT NULL COMMENT '全路径',
  `pids` varchar(200) DEFAULT NULL COMMENT '祖先分类',
  `childrens` int(11) NOT NULL DEFAULT '0' COMMENT '下级分类数',
  `display_order` int(11) NOT NULL COMMENT '显示顺序',
  `cat_type_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '系统内置',
  `jiajialv` decimal(8,2) DEFAULT NULL,
  PRIMARY KEY (`cat_type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='款式分类';

-- ----------------------------
-- Records of app_cat_type
-- ----------------------------

-- ----------------------------
-- Table structure for app_gold_jiajialv
-- ----------------------------
DROP TABLE IF EXISTS `app_gold_jiajialv`;
CREATE TABLE `app_gold_jiajialv` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `gold_price` decimal(10,2) DEFAULT NULL COMMENT '黄金价格',
  `jiajialv` decimal(10,2) DEFAULT NULL COMMENT '加价率',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `create_user` varchar(20) DEFAULT NULL COMMENT '创建用户',
  `is_usable` tinyint(1) DEFAULT '1' COMMENT '是否可用（1 启用，0 禁用）',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='普通黄金价格加价率';

-- ----------------------------
-- Records of app_gold_jiajialv
-- ----------------------------

-- ----------------------------
-- Table structure for app_product_type
-- ----------------------------
DROP TABLE IF EXISTS `app_product_type`;
CREATE TABLE `app_product_type` (
  `product_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '产品线id',
  `product_type_name` char(50) NOT NULL COMMENT '产品线名称',
  `product_type_code` char(50) DEFAULT NULL COMMENT '产品线编码',
  `note` varchar(255) DEFAULT NULL COMMENT '描述',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级部门id',
  `tree_path` varchar(512) NOT NULL COMMENT '全路径',
  `pids` varchar(200) DEFAULT NULL COMMENT '祖先分类',
  `childrens` int(11) NOT NULL DEFAULT '0' COMMENT '下级分类数',
  `display_order` int(11) NOT NULL COMMENT '显示顺序',
  `product_type_status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否停用:1启用 0停用',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '系统内置',
  PRIMARY KEY (`product_type_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品线分类';

-- ----------------------------
-- Records of app_product_type
-- ----------------------------

-- ----------------------------
-- Table structure for app_salepolicy_channel
-- ----------------------------
DROP TABLE IF EXISTS `app_salepolicy_channel`;
CREATE TABLE `app_salepolicy_channel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `policy_id` int(10) DEFAULT NULL COMMENT '销售策略id',
  `channel` int(10) DEFAULT NULL COMMENT '渠道id',
  `channel_level` int(10) DEFAULT '1' COMMENT '等级',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `create_user` varchar(100) DEFAULT NULL COMMENT '创建人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `check_user` varchar(100) DEFAULT NULL COMMENT '审核',
  `status` int(1) DEFAULT NULL COMMENT '状态:1保存2申请3审核通过4未通过5取消',
  `is_delete` int(1) DEFAULT NULL COMMENT '取消 1未删除 2已删除',
  PRIMARY KEY (`id`),
  KEY `policy_id` (`policy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of app_salepolicy_channel
-- ----------------------------

-- ----------------------------
-- Table structure for app_salepolicy_goods
-- ----------------------------
DROP TABLE IF EXISTS `app_salepolicy_goods`;
CREATE TABLE `app_salepolicy_goods` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `policy_id` int(10) NOT NULL COMMENT '销售策略id',
  `goods_id` varchar(30) NOT NULL COMMENT '货号或款号',
  `chengben` decimal(8,2) unsigned NOT NULL COMMENT '成本价',
  `sale_price` decimal(12,2) unsigned DEFAULT NULL COMMENT '销售价',
  `jiajia` decimal(8,2) unsigned NOT NULL DEFAULT '1.00' COMMENT '加价率',
  `sta_value` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '固定值',
  `chengben_compare` decimal(8,2) DEFAULT NULL COMMENT '当可销售商品表中的成本发生变化 向本字段写入改变的成本价格',
  `isXianhuo` tinyint(1) NOT NULL DEFAULT '1' COMMENT '现货状态0是期货1是现货',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `create_user` varchar(100) DEFAULT NULL COMMENT '创建人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  `check_user` varchar(100) DEFAULT NULL COMMENT '审核',
  `status` int(1) DEFAULT '1' COMMENT '状态:1保存2申请3审核通过4未通过5取消',
  `is_delete` int(1) DEFAULT '1' COMMENT '删除 1未删除 2已删除',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`),
  KEY `policy_id` (`policy_id`,`goods_id`),
  KEY `status` (`status`),
  KEY `policy_id_2` (`policy_id`),
  KEY `is_delete` (`is_delete`),
  KEY `policy_id_3` (`policy_id`,`is_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='销售政策对应商品表';

-- ----------------------------
-- Records of app_salepolicy_goods
-- ----------------------------

-- ----------------------------
-- Table structure for app_style_baoxianfee
-- ----------------------------
DROP TABLE IF EXISTS `app_style_baoxianfee`;
CREATE TABLE `app_style_baoxianfee` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `min` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '最小值',
  `max` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '最大值',
  `price` decimal(10,0) NOT NULL COMMENT '价格',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态1启用，2停用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of app_style_baoxianfee
-- ----------------------------

-- ----------------------------
-- Table structure for app_style_gallery
-- ----------------------------
DROP TABLE IF EXISTS `app_style_gallery`;
CREATE TABLE `app_style_gallery` (
  `g_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `style_id` int(10) unsigned DEFAULT NULL COMMENT '款式id',
  `style_sn` varchar(30) DEFAULT NULL,
  `image_place` tinyint(3) unsigned NOT NULL COMMENT '图片位置，100=网络上架，6=表现工艺，5=证书图,1=正立45°图,2=正立图,3=爪头图,4=爪尾图,8=内臂图,7=质检专用图',
  `img_sort` int(10) unsigned NOT NULL COMMENT '图片排序',
  `img_ori` varchar(100) NOT NULL COMMENT '原图路径',
  `thumb_img` varchar(100) NOT NULL COMMENT '缩略图',
  `middle_img` varchar(100) NOT NULL COMMENT '中图',
  `big_img` varchar(100) NOT NULL COMMENT '大图',
  PRIMARY KEY (`g_id`),
  KEY `style_sn` (`style_sn`),
  KEY `img_ori` (`img_ori`)
) ENGINE=MyISAM AUTO_INCREMENT=124300 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of app_style_gallery
-- ----------------------------

-- ----------------------------
-- Table structure for app_style_jxs
-- ----------------------------
DROP TABLE IF EXISTS `app_style_jxs`;
CREATE TABLE `app_style_jxs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `style_name` varchar(100) DEFAULT NULL COMMENT '款式名称',
  `style_sn` varchar(60) DEFAULT NULL COMMENT '款号',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态',
  `add_user` varchar(20) DEFAULT NULL COMMENT '添加用户',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `ban_user` varchar(20) DEFAULT NULL COMMENT '禁用用户',
  `ban_time` datetime DEFAULT NULL COMMENT '禁用时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of app_style_jxs
-- ----------------------------

-- ----------------------------
-- Table structure for app_style_xilie
-- ----------------------------
DROP TABLE IF EXISTS `app_style_xilie`;
CREATE TABLE `app_style_xilie` (
  `id` int(20) NOT NULL AUTO_INCREMENT COMMENT '系列Id',
  `name` varchar(100) NOT NULL COMMENT '系列名称',
  `status` tinyint(1) unsigned NOT NULL COMMENT '是否启用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='款式系列表';

-- ----------------------------
-- Records of app_style_xilie
-- ----------------------------

-- ----------------------------
-- Table structure for app_yikoujia_goods
-- ----------------------------
DROP TABLE IF EXISTS `app_yikoujia_goods`;
CREATE TABLE `app_yikoujia_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` varchar(255) DEFAULT NULL,
  `goods_sn` varchar(255) DEFAULT NULL,
  `caizhi` varchar(255) DEFAULT NULL,
  `small` varchar(255) DEFAULT NULL,
  `tuo_type` tinyint(1) DEFAULT '0' COMMENT '金托类型',
  `color` varchar(20) DEFAULT NULL COMMENT '颜色',
  `clarity` varchar(20) DEFAULT NULL COMMENT '净度',
  `sbig` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `policy_id` varchar(255) DEFAULT NULL,
  `isXianhuo` varchar(255) DEFAULT NULL,
  `is_delete` tinyint(2) DEFAULT '0' COMMENT '是否删除0未删除 ，1已删除',
  `add_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  `add_user` varchar(120) DEFAULT NULL COMMENT '添加人',
  `cert` varchar(255) DEFAULT NULL COMMENT '裸钻证书类型',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of app_yikoujia_goods
-- ----------------------------

-- ----------------------------
-- Table structure for area
-- ----------------------------
DROP TABLE IF EXISTS `area`;
CREATE TABLE `area` (
  `area_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引ID',
  `area_name` varchar(50) NOT NULL COMMENT '地区名称',
  `area_parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '地区父ID',
  `area_sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `area_deep` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '地区深度，从1开始',
  `area_region` varchar(3) DEFAULT NULL COMMENT '大区名称',
  PRIMARY KEY (`area_id`),
  KEY `area_parent_id` (`area_parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='地区表';

-- ----------------------------
-- Records of area
-- ----------------------------

-- ----------------------------
-- Table structure for arrival_notice
-- ----------------------------
DROP TABLE IF EXISTS `arrival_notice`;
CREATE TABLE `arrival_notice` (
  `an_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '通知id',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `goods_name` varchar(50) NOT NULL COMMENT '商品名称',
  `member_id` int(10) unsigned NOT NULL COMMENT '会员id',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `an_addtime` int(10) unsigned NOT NULL COMMENT '添加时间',
  `an_email` varchar(100) NOT NULL COMMENT '邮箱',
  `an_mobile` varchar(11) NOT NULL COMMENT '手机号',
  `an_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态 1到货通知，2预售',
  PRIMARY KEY (`an_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品到货通知表';

-- ----------------------------
-- Records of arrival_notice
-- ----------------------------

-- ----------------------------
-- Table structure for article
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `article_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '索引id',
  `ac_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
  `article_url` varchar(100) DEFAULT NULL COMMENT '跳转链接',
  `article_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示，0为否，1为是，默认为1',
  `article_position` tinyint(4) NOT NULL DEFAULT '1' COMMENT '显示位置:1默认网站前台,2买家,3卖家,4全站',
  `article_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  `article_title` varchar(100) DEFAULT NULL COMMENT '标题',
  `article_content` text COMMENT '内容',
  `article_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布时间',
  PRIMARY KEY (`article_id`),
  KEY `ac_id` (`ac_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章表';

-- ----------------------------
-- Records of article
-- ----------------------------

-- ----------------------------
-- Table structure for article_class
-- ----------------------------
DROP TABLE IF EXISTS `article_class`;
CREATE TABLE `article_class` (
  `ac_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引ID',
  `ac_code` varchar(20) DEFAULT NULL COMMENT '分类标识码',
  `ac_name` varchar(100) NOT NULL COMMENT '分类名称',
  `ac_parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `ac_sort` tinyint(1) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  PRIMARY KEY (`ac_id`),
  KEY `ac_parent_id` (`ac_parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章分类表';

-- ----------------------------
-- Records of article_class
-- ----------------------------

-- ----------------------------
-- Table structure for attribute
-- ----------------------------
DROP TABLE IF EXISTS `attribute`;
CREATE TABLE `attribute` (
  `attr_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '属性id',
  `attr_name` varchar(100) NOT NULL COMMENT '属性名称',
  `type_id` int(10) unsigned NOT NULL COMMENT '所属类型id',
  `attr_value` text NOT NULL COMMENT '属性值列',
  `attr_show` tinyint(1) unsigned NOT NULL COMMENT '是否显示。0为不显示、1为显示',
  `attr_sort` tinyint(1) unsigned NOT NULL COMMENT '排序',
  `attr_type` tinyint(1) DEFAULT '1' COMMENT '1：单选；2：多选；3：文本框',
  PRIMARY KEY (`attr_id`),
  KEY `attr_id` (`attr_id`,`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品属性表';

-- ----------------------------
-- Records of attribute
-- ----------------------------

-- ----------------------------
-- Table structure for attribute_value
-- ----------------------------
DROP TABLE IF EXISTS `attribute_value`;
CREATE TABLE `attribute_value` (
  `attr_value_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '属性值id',
  `attr_value_name` varchar(100) DEFAULT '' COMMENT '属性值名称',
  `attr_id` int(10) unsigned NOT NULL COMMENT '所属属性id',
  `type_id` int(10) unsigned NOT NULL COMMENT '类型id',
  `attr_value_sort` tinyint(1) unsigned NOT NULL COMMENT '属性值排序',
  PRIMARY KEY (`attr_value_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品属性值表';

-- ----------------------------
-- Records of attribute_value
-- ----------------------------

-- ----------------------------
-- Table structure for base_lz_discount_config
-- ----------------------------
DROP TABLE IF EXISTS `base_lz_discount_config`;
CREATE TABLE `base_lz_discount_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `user_id` int(10) unsigned NOT NULL COMMENT '管理员ID[user表id]',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1.小于50分 ',
  `zhekou` decimal(5,2) NOT NULL DEFAULT '1.00' COMMENT '2.小于1克拉 ',
  `enabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '3.大于1克拉',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=25334 DEFAULT CHARSET=utf8 COMMENT='折扣管理表';

-- ----------------------------
-- Records of base_lz_discount_config
-- ----------------------------

-- ----------------------------
-- Table structure for base_salepolicy_info
-- ----------------------------
DROP TABLE IF EXISTS `base_salepolicy_info`;
CREATE TABLE `base_salepolicy_info` (
  `policy_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `policy_name` varchar(60) NOT NULL COMMENT '销售策略名称',
  `policy_start_time` date NOT NULL COMMENT '销售策略开始时间',
  `policy_end_time` date DEFAULT NULL COMMENT '销售策略结束时间',
  `create_time` datetime DEFAULT NULL COMMENT '记录创建时间',
  `create_user` varchar(20) DEFAULT NULL COMMENT '记录创建人',
  `create_remark` varchar(200) DEFAULT NULL COMMENT '记录创建备注',
  `check_user` varchar(20) DEFAULT NULL COMMENT '审核人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `zuofei_time` datetime DEFAULT NULL COMMENT '作废时间',
  `check_remark` varchar(200) DEFAULT NULL COMMENT '记录备注',
  `bsi_status` tinyint(1) DEFAULT NULL COMMENT '记录状态 1保存,2申请审核,3已审核,4取消',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '记录是否有效 0有效1无效',
  `is_together` tinyint(1) NOT NULL DEFAULT '1' COMMENT '策略类型：1，普通；2，打包',
  `jiajia` decimal(8,4) unsigned NOT NULL DEFAULT '1.0000' COMMENT '加价率',
  `sta_value` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '固定值',
  `is_default` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否为默认政策1为默认2位不是默认',
  `is_favourable` tinyint(11) NOT NULL DEFAULT '1',
  `product_type` varchar(50) NOT NULL DEFAULT '0' COMMENT '产品线',
  `tuo_type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '金托类型',
  `huopin_type` int(1) NOT NULL DEFAULT '2' COMMENT '货品类型',
  `cat_type` varchar(50) NOT NULL DEFAULT '0' COMMENT '款式分类',
  `range_begin` char(25) NOT NULL DEFAULT '0' COMMENT '开始范围',
  `range_end` char(25) NOT NULL DEFAULT '0' COMMENT '结束范围',
  `zhushi_begin` char(25) NOT NULL DEFAULT '0' COMMENT '开始范围',
  `zhushi_end` char(25) NOT NULL DEFAULT '0' COMMENT '结束范围',
  `is_kuanprice` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否按款定价 0不是1是',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `is_tsyd` tinyint(1) DEFAULT '0' COMMENT '是否天生一对',
  `product_type_id` int(10) DEFAULT '0' COMMENT '产品线id',
  `cat_type_id` int(10) DEFAULT '0' COMMENT '款式分类id',
  `xilie` text COMMENT '所属系列',
  `cert` text COMMENT '裸钻证书类型',
  `color` varchar(255) DEFAULT NULL,
  `clarity` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`policy_id`),
  KEY `policy_start_time` (`policy_start_time`,`policy_end_time`),
  KEY `policy_start_time_2` (`policy_start_time`,`policy_end_time`,`bsi_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='销售策略';

-- ----------------------------
-- Records of base_salepolicy_info
-- ----------------------------

-- ----------------------------
-- Table structure for base_style_info
-- ----------------------------
DROP TABLE IF EXISTS `base_style_info`;
CREATE TABLE `base_style_info` (
  `style_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '款式ID',
  `style_sn` varchar(20) NOT NULL COMMENT '款式编号',
  `style_name` varchar(60) NOT NULL COMMENT '款式名称',
  `product_type` tinyint(2) DEFAULT NULL COMMENT '产品线:app_product_type',
  `style_type` tinyint(2) NOT NULL COMMENT '款式分类:app_cat_type',
  `create_time` datetime NOT NULL COMMENT '添加时间',
  `modify_time` datetime NOT NULL COMMENT '更新时间',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `cancel_time` datetime DEFAULT NULL COMMENT '作废时间',
  `check_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '审核状态:1保存2提交申请3审核4未通过5作废',
  `is_sales` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否销售，0：否，1：是',
  `is_made` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否定制，0：否，1：是',
  `dismantle_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否拆货:1=正常 2=允许拆货 3=已拆货',
  `style_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '记录状态',
  `style_remark` text COMMENT '记录备注',
  `dapei_goods_sn` varchar(60) DEFAULT NULL COMMENT '搭配套系名称',
  `changbei_sn` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否常备款;1,是；2,否',
  `style_sex` tinyint(1) NOT NULL DEFAULT '1' COMMENT '款式性别;1:男；2：女；3：中性',
  `xilie` varchar(50) DEFAULT NULL COMMENT '系列',
  `market_xifen` varchar(50) DEFAULT NULL COMMENT '市场细分',
  `is_zp` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否是赠品；1否，2是',
  `is_new` tinyint(4) DEFAULT NULL COMMENT '导数据用',
  `ori_goods_sn` varchar(20) DEFAULT NULL COMMENT '老款号',
  `sell_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '畅销度',
  `bang_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '绑定1：需要绑定，2：不需要绑定',
  `sale_way` char(2) NOT NULL DEFAULT '1' COMMENT '可销售渠道. 1线上，2线下',
  `is_xz` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否销账,2.是.1否',
  `zp_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '赠品售价',
  `is_allow_favorable` tinyint(1) unsigned NOT NULL COMMENT '是否允许改价',
  `is_gold` tinyint(3) unsigned NOT NULL COMMENT '是否是黄金 0:非黄金，1:瑞金 2:3D  3:一口价',
  `is_support_style` tinyint(3) unsigned DEFAULT NULL COMMENT '是否支持按款销售',
  `company_type_id` varchar(30) DEFAULT NULL,
  `is_auto` tinyint(1) DEFAULT NULL COMMENT '是否自动生成款号1是',
  `jiajialv` decimal(8,2) DEFAULT NULL,
  `is_wukong` tinyint(2) DEFAULT '0' COMMENT '是否物控款式 1 物控款  2 正常款',
  `goods_content` text CHARACTER SET utf16le COMMENT '款式商品详情',
  `goods_salenum` int(11) DEFAULT NULL COMMENT '商品销量',
  `goods_click` int(10) unsigned DEFAULT NULL COMMENT '商品点击数',
  `is_recommend` tinyint(1) unsigned DEFAULT '0' COMMENT '是否推荐 默认0  1推荐 0不推荐',
  PRIMARY KEY (`style_id`),
  UNIQUE KEY `style_sn` (`style_sn`) USING BTREE,
  KEY `product_type` (`product_type`),
  KEY `create_time` (`create_time`),
  KEY `is_made` (`is_made`),
  KEY `style_type` (`style_type`),
  KEY `product_type_2` (`product_type`,`style_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of base_style_info
-- ----------------------------

-- ----------------------------
-- Table structure for bill_create
-- ----------------------------
DROP TABLE IF EXISTS `bill_create`;
CREATE TABLE `bill_create` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) DEFAULT '0',
  `os_month` int(11) DEFAULT '0',
  `os_type` int(11) DEFAULT '0' COMMENT '0实物1虚拟',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='结算生成辅助表';

-- ----------------------------
-- Records of bill_create
-- ----------------------------

-- ----------------------------
-- Table structure for boss_goods
-- ----------------------------
DROP TABLE IF EXISTS `boss_goods`;
CREATE TABLE `boss_goods` (
  `goods_id` bigint(30) NOT NULL DEFAULT '0' COMMENT '货号',
  `goods_sn` varchar(50) DEFAULT NULL COMMENT '款号',
  `product_type` varchar(50) DEFAULT NULL COMMENT '新产品线',
  `cat_type` varchar(50) DEFAULT NULL COMMENT '新款式分类',
  `is_on_sale` int(3) NOT NULL DEFAULT '1' COMMENT '见数据字典',
  `prc_id` int(4) NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `prc_name` varchar(100) DEFAULT NULL COMMENT '供货商名称',
  `put_in_type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '入库方式 见数据字典',
  `goods_name` varchar(100) DEFAULT NULL COMMENT '商品名称',
  `company` varchar(100) NOT NULL COMMENT '公司',
  `warehouse` varchar(30) NOT NULL COMMENT '仓库',
  `company_id` int(11) NOT NULL COMMENT '公司ID',
  `warehouse_id` int(11) NOT NULL COMMENT '仓库D',
  `caizhi` varchar(20) DEFAULT NULL COMMENT '材质',
  `jinzhong` decimal(8,3) DEFAULT NULL COMMENT '金重',
  `jinhao` varchar(50) DEFAULT '0' COMMENT '金耗',
  `zongzhong` varchar(50) DEFAULT NULL,
  `shoucun` varchar(50) DEFAULT NULL COMMENT '手寸',
  `order_sn` varchar(40) DEFAULT NULL COMMENT '订单号',
  `buchan_sn` varchar(20) DEFAULT NULL COMMENT '布产号',
  `order_detail_id` int(1) NOT NULL DEFAULT '0',
  `old_detail_id` varchar(10) DEFAULT NULL,
  `pinpai` varchar(100) DEFAULT NULL COMMENT '品牌',
  `changdu` varchar(100) DEFAULT NULL COMMENT '长度',
  `zhengshuhao` varchar(100) DEFAULT NULL COMMENT '证书号',
  `zhengshuhao2` varchar(30) DEFAULT NULL COMMENT '证书号2',
  `peijianshuliang` varchar(50) DEFAULT NULL COMMENT '配件数量',
  `guojizhengshu` varchar(50) DEFAULT NULL COMMENT '国际证书',
  `zhengshuleibie` varchar(50) DEFAULT NULL COMMENT '证书类别',
  `gemx_zhengshu` varchar(64) DEFAULT NULL,
  `num` int(6) NOT NULL DEFAULT '1',
  `addtime` datetime DEFAULT NULL,
  `yanse` varchar(50) DEFAULT NULL COMMENT '颜色',
  `jingdu` varchar(50) DEFAULT NULL COMMENT '净度',
  `qiegong` varchar(10) DEFAULT NULL COMMENT '切工',
  `paoguang` varchar(10) DEFAULT NULL COMMENT '抛光',
  `duichen` varchar(10) DEFAULT NULL COMMENT '对称',
  `yingguang` varchar(10) DEFAULT NULL COMMENT '荧光',
  `zuanshizhekou` varchar(11) DEFAULT NULL,
  `jinse` varchar(3) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `guojibaojia` varchar(20) DEFAULT NULL COMMENT '裸钻国际报价',
  `luozuanzhengshu` varchar(100) DEFAULT NULL COMMENT '裸钻证书类型',
  `tuo_type` tinyint(3) NOT NULL DEFAULT '1',
  `huopin_type` int(1) NOT NULL DEFAULT '2' COMMENT '货品类型；0为A类；1为B类；2为C类；',
  `dia_sn` varchar(4) DEFAULT NULL COMMENT '钻石代码（色阶+净度）',
  `zhushipipeichengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '修改主石匹配的成本-AB货',
  `biaoqianjia` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '标签价',
  `jietuoxiangkou` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '戒托镶口',
  `box_sn` varchar(30) DEFAULT '0-00-0-0' COMMENT '默认柜位',
  `jiejia` tinyint(1) DEFAULT NULL COMMENT '是否结价：1=是,0=否',
  `weixiu_status` tinyint(2) DEFAULT '0' COMMENT '货品维修状态 数字字典warehouse.weixiu_status',
  `change_time` datetime DEFAULT NULL COMMENT '最后一次转仓时间',
  `weixiu_company_id` int(10) NOT NULL DEFAULT '0' COMMENT '维修入库公司id',
  `weixiu_company_name` varchar(30) NOT NULL DEFAULT '' COMMENT '维修入库公司名称',
  `weixiu_warehouse_id` int(10) NOT NULL DEFAULT '0' COMMENT '维修入库仓库id',
  `weixiu_warehouse_name` varchar(30) NOT NULL DEFAULT '' COMMENT '维修入库仓库名称',
  `chuku_time` datetime DEFAULT NULL COMMENT '出库时间',
  `color_grade` varchar(50) DEFAULT NULL COMMENT '颜色等级',
  `is_hrds` tinyint(1) DEFAULT '0' COMMENT '星耀钻石 1:否；2:是',
  `peijianjinzhong` decimal(8,3) DEFAULT NULL COMMENT '配件金重',
  `zhushi` varchar(50) DEFAULT NULL COMMENT '主石',
  `zhushilishu` varchar(11) NOT NULL DEFAULT '0' COMMENT '主石粒数',
  `zuanshidaxiao` decimal(10,3) DEFAULT '0.000' COMMENT '主石大小',
  `zhushizhongjijia` varchar(50) DEFAULT NULL COMMENT '主石总计价',
  `zhushiyanse` varchar(50) DEFAULT NULL COMMENT '主石颜色',
  `zhushijingdu` varchar(50) DEFAULT NULL COMMENT '主石净度',
  `zhushiqiegong` varchar(50) DEFAULT NULL COMMENT '主石切工',
  `zhushixingzhuang` varchar(50) DEFAULT NULL COMMENT '主石形状',
  `zhushibaohao` varchar(60) DEFAULT NULL COMMENT '主石包号',
  `zhushiguige` varchar(60) DEFAULT NULL COMMENT '主石规格',
  `zhushitiaoma` varchar(200) DEFAULT NULL COMMENT '主石条码',
  `fushi` varchar(50) DEFAULT NULL COMMENT '副石',
  `fushilishu` varchar(50) DEFAULT NULL COMMENT '副石粒数',
  `fushizhong` decimal(8,3) NOT NULL DEFAULT '0.000',
  `fushizhongjijia` varchar(50) DEFAULT NULL,
  `fushibaohao` varchar(50) DEFAULT NULL,
  `fushiguige` varchar(50) DEFAULT NULL,
  `fushiyanse` varchar(50) DEFAULT NULL,
  `fushijingdu` varchar(50) DEFAULT NULL,
  `fushixingzhuang` varchar(50) DEFAULT NULL,
  `shi2` varchar(40) DEFAULT NULL COMMENT '副石2',
  `shi2lishu` varchar(40) DEFAULT NULL COMMENT '副石2粒数',
  `shi2zhong` decimal(8,3) NOT NULL DEFAULT '0.000' COMMENT '副石2重',
  `shi2zhongjijia` varchar(40) DEFAULT NULL COMMENT '副石2总计价',
  `shi2baohao` varchar(60) DEFAULT NULL COMMENT '石2包号',
  `shi3` varchar(40) DEFAULT NULL COMMENT '副石3',
  `shi3lishu` varchar(40) DEFAULT NULL COMMENT '副石3粒数',
  `shi3zhong` decimal(8,3) NOT NULL DEFAULT '0.000' COMMENT '副石3重',
  `shi3zhongjijia` varchar(40) DEFAULT NULL COMMENT '副石3总计价',
  `shi3baohao` varchar(60) DEFAULT NULL COMMENT '石3包号',
  `yuanshichengbenjia` decimal(11,3) DEFAULT NULL,
  `mingyichengben` decimal(11,3) DEFAULT NULL,
  `jijiachengben` varchar(50) DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `from_p_id` int(1) NOT NULL DEFAULT '0',
  `raw_is_on_sale` int(3) NOT NULL DEFAULT '1' COMMENT '见数据字典',
  `from_company_id` int(1) NOT NULL DEFAULT '0',
  `from_zt_pid` int(1) NOT NULL DEFAULT '0',
  `management_fee` decimal(10,3) DEFAULT '0.000' COMMENT '管理费'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of boss_goods
-- ----------------------------

-- ----------------------------
-- Table structure for boss_store
-- ----------------------------
DROP TABLE IF EXISTS `boss_store`;
CREATE TABLE `boss_store` (
  `store_id` int(10) unsigned DEFAULT '0' COMMENT '序号',
  `store_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '渠道名称',
  `grade_id` int(1) NOT NULL DEFAULT '0',
  `member_id` int(1) NOT NULL DEFAULT '0',
  `member_name` char(0) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `seller_name` char(0) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `sc_id` int(1) NOT NULL DEFAULT '0',
  `store_company_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '名称',
  `province_id` int(1) NOT NULL DEFAULT '0',
  `area_info` char(0) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `store_address` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `store_zip` char(0) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `store_state` int(1) NOT NULL DEFAULT '0',
  `store_sort` int(1) NOT NULL DEFAULT '0',
  `store_time` int(1) NOT NULL DEFAULT '0',
  `store_keywords` char(0) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `store_description` char(0) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `store_recommend` int(1) NOT NULL DEFAULT '0',
  `store_theme` varchar(7) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `store_credit` int(1) NOT NULL DEFAULT '0',
  `store_desccredit` int(1) NOT NULL DEFAULT '0',
  `store_servicecredit` int(1) NOT NULL DEFAULT '0',
  `store_deliverycredit` int(1) NOT NULL DEFAULT '0',
  `store_collect` int(1) NOT NULL DEFAULT '0',
  `store_sales` int(1) NOT NULL DEFAULT '0',
  `store_free_price` int(1) NOT NULL DEFAULT '0',
  `store_decoration_switch` int(1) NOT NULL DEFAULT '0',
  `store_decoration_only` int(1) NOT NULL DEFAULT '0',
  `store_decoration_image_count` int(1) NOT NULL DEFAULT '0',
  `is_own_shop` int(1) NOT NULL DEFAULT '0',
  `bind_all_gc` int(1) NOT NULL DEFAULT '0',
  `left_bar_type` int(1) NOT NULL DEFAULT '0',
  `is_person` int(1) NOT NULL DEFAULT '0',
  `store_company_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '主键'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of boss_store
-- ----------------------------

-- ----------------------------
-- Table structure for boss_user
-- ----------------------------
DROP TABLE IF EXISTS `boss_user`;
CREATE TABLE `boss_user` (
  `store_id` int(10) unsigned DEFAULT '0' COMMENT '序号',
  `store_company_id` int(10) unsigned DEFAULT '0' COMMENT '主键',
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '主键',
  `member` varchar(20) NOT NULL COMMENT '登录帐号',
  `member_truename` varchar(20) NOT NULL COMMENT '姓名',
  `member_passwd` varchar(50) NOT NULL COMMENT '登录密码',
  `member_email` varchar(60) NOT NULL DEFAULT '',
  `member_email_bind` int(1) NOT NULL DEFAULT '0',
  `member_mobile` char(11) NOT NULL DEFAULT '' COMMENT '手机',
  `member_mobile_bind` int(1) NOT NULL DEFAULT '0',
  `member_login_num` int(1) NOT NULL DEFAULT '0',
  `member_time` int(1) NOT NULL DEFAULT '0',
  `member_login_time` int(1) NOT NULL DEFAULT '0',
  `member_old_login_time` int(1) NOT NULL DEFAULT '0',
  `member_points` int(1) NOT NULL DEFAULT '0',
  `available_predeposit` int(1) NOT NULL DEFAULT '0',
  `freeze_predeposit` int(1) NOT NULL DEFAULT '0',
  `available_rc_balance` int(1) NOT NULL DEFAULT '0',
  `freeze_rc_balance` int(1) NOT NULL DEFAULT '0',
  `inform_allow` int(1) NOT NULL DEFAULT '0',
  `is_buy` int(1) NOT NULL DEFAULT '0',
  `is_allowtalk` int(1) NOT NULL DEFAULT '0',
  `member_state` int(1) NOT NULL DEFAULT '0',
  `member_snsvisitnum` int(1) NOT NULL DEFAULT '0',
  `member_exppoints` int(1) NOT NULL DEFAULT '0',
  `role_id` tinyint(10) DEFAULT '0' COMMENT '权限管理-角色管理',
  `user_type` tinyint(4) NOT NULL DEFAULT '3' COMMENT '用户类型'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of boss_user
-- ----------------------------

-- ----------------------------
-- Table structure for boss_warehouse
-- ----------------------------
DROP TABLE IF EXISTS `boss_warehouse`;
CREATE TABLE `boss_warehouse` (
  `id` int(4) unsigned NOT NULL DEFAULT '0',
  `name` varchar(20) NOT NULL COMMENT '仓库名称',
  `code` varchar(20) NOT NULL,
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` datetime DEFAULT NULL,
  `create_user` varchar(20) DEFAULT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否有效；1为有效，0为无效',
  `lock` tinyint(1) NOT NULL DEFAULT '0' COMMENT '锁定状态 0未锁定/1锁定',
  `type` int(2) NOT NULL DEFAULT '0' COMMENT '仓库类型',
  `diamond_warehouse` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否裸钻库 0否,1是',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '默认上架 0 否,1 是',
  `company_id` int(10) NOT NULL COMMENT '公司关联id',
  `company_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '名称',
  `store_id` int(1) NOT NULL DEFAULT '0',
  `store_name` varchar(41) CHARACTER SET utf8mb4 NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of boss_warehouse
-- ----------------------------

-- ----------------------------
-- Table structure for brand
-- ----------------------------
DROP TABLE IF EXISTS `brand`;
CREATE TABLE `brand` (
  `brand_id` mediumint(11) NOT NULL AUTO_INCREMENT COMMENT '索引ID',
  `brand_name` varchar(100) DEFAULT NULL COMMENT '品牌名称',
  `brand_initial` varchar(1) NOT NULL COMMENT '品牌首字母',
  `brand_class` varchar(50) DEFAULT NULL COMMENT '类别名称',
  `brand_pic` varchar(100) DEFAULT NULL COMMENT '图片',
  `brand_sort` tinyint(3) unsigned DEFAULT '0' COMMENT '排序',
  `brand_recommend` tinyint(1) DEFAULT '0' COMMENT '推荐，0为否，1为是，默认为0',
  `store_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '店铺ID',
  `brand_apply` tinyint(1) NOT NULL DEFAULT '1' COMMENT '品牌申请，0为申请中，1为通过，默认为1，申请功能是会员使用，系统后台默认为1',
  `class_id` int(10) unsigned DEFAULT '0' COMMENT '所属分类id',
  `show_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '品牌展示类型 0表示图片 1表示文字 ',
  `brand_bgpic` varchar(100) DEFAULT 'brand_default_max.jpg' COMMENT '品牌大图',
  `brand_xbgpic` varchar(100) DEFAULT 'brand_default_small.jpg' COMMENT '品牌小图',
  `brand_tjstore` varchar(100) DEFAULT '请于品牌管理里编辑我' COMMENT '品牌副标题',
  `brand_introduction` varchar(300) DEFAULT '珂兰技术提醒您：你当前的品牌介绍并没有填写！使用默认的这些会出现在你的眼前，请于后台进行修改' COMMENT '品牌介绍',
  `brand_view` int(10) NOT NULL COMMENT '品牌单页浏览量',
  PRIMARY KEY (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='品牌表';

-- ----------------------------
-- Records of brand
-- ----------------------------

-- ----------------------------
-- Table structure for cart
-- ----------------------------
DROP TABLE IF EXISTS `cart`;
CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '购物车id',
  `buyer_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '买家id',
  `store_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '店铺id',
  `store_name` varchar(100) DEFAULT NULL COMMENT '店铺名称',
  `goods_id` varchar(30) NOT NULL DEFAULT '0' COMMENT '商品id',
  `goods_name` varchar(100) NOT NULL COMMENT '商品名称',
  `goods_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品价格',
  `goods_num` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '购买商品数量',
  `goods_image` varchar(100) DEFAULT NULL COMMENT '商品图片',
  `bl_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '组合套装ID',
  `goods_type` tinyint(1) unsigned DEFAULT NULL COMMENT '--商品类型 1空托 2裸钻',
  `goods_tsyd` varchar(20) DEFAULT NULL,
  `goods_info` text COMMENT '--商品属性(序列化字段)',
  PRIMARY KEY (`cart_id`),
  KEY `member_id` (`buyer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='购物车数据表';

-- ----------------------------
-- Records of cart
-- ----------------------------

-- ----------------------------
-- Table structure for chain
-- ----------------------------
DROP TABLE IF EXISTS `chain`;
CREATE TABLE `chain` (
  `chain_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '门店id',
  `store_id` int(10) unsigned NOT NULL COMMENT '所属店铺id',
  `chain_user` varchar(50) NOT NULL COMMENT '登录名',
  `chain_pwd` char(32) NOT NULL COMMENT '登录密码',
  `chain_name` varchar(50) NOT NULL COMMENT '门店名称',
  `chain_img` varchar(50) NOT NULL COMMENT '门店图片',
  `area_id_1` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '一级地区id',
  `area_id_2` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '二级地区id',
  `area_id_3` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '三级地区id',
  `area_id_4` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '四级地区id',
  `area_id` int(10) unsigned NOT NULL COMMENT '地区id',
  `area_info` varchar(50) NOT NULL COMMENT '地区详情',
  `chain_address` varchar(50) NOT NULL COMMENT '详细地址',
  `chain_phone` varchar(100) NOT NULL COMMENT '联系方式',
  `chain_opening_hours` varchar(100) NOT NULL COMMENT '营业时间',
  `chain_traffic_line` varchar(100) NOT NULL COMMENT '交通线路',
  PRIMARY KEY (`chain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺门店表';

-- ----------------------------
-- Records of chain
-- ----------------------------

-- ----------------------------
-- Table structure for chain_stock
-- ----------------------------
DROP TABLE IF EXISTS `chain_stock`;
CREATE TABLE `chain_stock` (
  `chain_id` int(10) unsigned NOT NULL COMMENT '门店id',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `goods_commonid` int(10) unsigned NOT NULL COMMENT '商品SPU',
  `stock` int(10) NOT NULL COMMENT '库存',
  PRIMARY KEY (`chain_id`,`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='门店商品库存表';

-- ----------------------------
-- Records of chain_stock
-- ----------------------------

-- ----------------------------
-- Table structure for chat_log
-- ----------------------------
DROP TABLE IF EXISTS `chat_log`;
CREATE TABLE `chat_log` (
  `m_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `f_id` int(10) unsigned NOT NULL COMMENT '会员ID',
  `f_name` varchar(50) NOT NULL COMMENT '会员名',
  `f_ip` varchar(15) NOT NULL COMMENT '发自IP',
  `t_id` int(10) unsigned NOT NULL COMMENT '接收会员ID',
  `t_name` varchar(50) NOT NULL COMMENT '接收会员名',
  `t_msg` varchar(300) DEFAULT NULL COMMENT '消息内容',
  `add_time` int(10) unsigned DEFAULT '0' COMMENT '添加时间',
  `msg_type` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`m_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消息记录表';

-- ----------------------------
-- Records of chat_log
-- ----------------------------

-- ----------------------------
-- Table structure for chat_msg
-- ----------------------------
DROP TABLE IF EXISTS `chat_msg`;
CREATE TABLE `chat_msg` (
  `m_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `f_id` int(10) unsigned NOT NULL COMMENT '会员ID',
  `f_name` varchar(50) NOT NULL COMMENT '会员名',
  `f_ip` varchar(15) NOT NULL COMMENT '发自IP',
  `t_id` int(10) unsigned NOT NULL COMMENT '接收会员ID',
  `t_name` varchar(50) NOT NULL COMMENT '接收会员名',
  `t_msg` varchar(300) DEFAULT NULL COMMENT '消息内容',
  `r_state` tinyint(1) unsigned DEFAULT '2' COMMENT '状态:1为已读,2为未读,默认为2',
  `msg_type` varchar(32) DEFAULT NULL,
  `add_time` int(10) unsigned DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`m_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消息表';

-- ----------------------------
-- Records of chat_msg
-- ----------------------------

-- ----------------------------
-- Table structure for circle
-- ----------------------------
DROP TABLE IF EXISTS `circle`;
CREATE TABLE `circle` (
  `circle_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '圈子id',
  `circle_name` varchar(12) NOT NULL COMMENT '圈子名称',
  `circle_desc` varchar(255) DEFAULT NULL COMMENT '圈子描述',
  `circle_masterid` int(11) unsigned NOT NULL COMMENT '圈主id',
  `circle_mastername` varchar(50) NOT NULL COMMENT '圈主名称',
  `circle_img` varchar(50) DEFAULT NULL COMMENT '圈子图片',
  `class_id` int(11) unsigned NOT NULL COMMENT '圈子分类',
  `circle_mcount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '圈子成员数',
  `circle_thcount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '圈子主题数',
  `circle_gcount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '圈子商品数',
  `circle_pursuereason` varchar(255) DEFAULT NULL COMMENT '圈子申请理由',
  `circle_notice` varchar(255) DEFAULT NULL COMMENT '圈子公告',
  `circle_status` tinyint(3) unsigned NOT NULL COMMENT '圈子状态，0关闭，1开启，2审核中，3审核失败',
  `circle_statusinfo` varchar(255) DEFAULT NULL COMMENT '关闭或审核失败原因',
  `circle_joinaudit` tinyint(3) unsigned NOT NULL COMMENT '加入圈子时候需要审核，0不需要，1需要',
  `circle_addtime` varchar(10) NOT NULL COMMENT '圈子创建时间',
  `circle_noticetime` varchar(10) DEFAULT NULL COMMENT '圈子公告更新时间',
  `is_recommend` tinyint(3) unsigned NOT NULL COMMENT '是否推荐 0未推荐，1已推荐',
  `is_hot` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否为热门圈子 1是 0否',
  `circle_tag` varchar(60) DEFAULT NULL COMMENT '圈子标签',
  `new_verifycount` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '等待审核成员数',
  `new_informcount` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '等待处理举报数',
  `mapply_open` tinyint(4) NOT NULL DEFAULT '0' COMMENT '申请管理是否开启 0关闭，1开启',
  `mapply_ml` tinyint(4) NOT NULL DEFAULT '0' COMMENT '成员级别',
  `new_mapplycount` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '管理申请数量',
  PRIMARY KEY (`circle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='圈子表';

-- ----------------------------
-- Records of circle
-- ----------------------------

-- ----------------------------
-- Table structure for circle_affix
-- ----------------------------
DROP TABLE IF EXISTS `circle_affix`;
CREATE TABLE `circle_affix` (
  `affix_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '附件id',
  `affix_filename` varchar(100) NOT NULL COMMENT '文件名称',
  `affix_filethumb` varchar(100) NOT NULL COMMENT '缩略图名称',
  `affix_filesize` int(10) unsigned NOT NULL COMMENT '文件大小，单位字节',
  `affix_addtime` varchar(10) NOT NULL COMMENT '上传时间',
  `affix_type` tinyint(3) unsigned NOT NULL COMMENT '文件类型 0无 1主题 2评论',
  `member_id` int(11) unsigned NOT NULL COMMENT '会员id',
  `theme_id` int(11) unsigned NOT NULL COMMENT '主题id',
  `reply_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '评论id',
  `circle_id` int(11) unsigned NOT NULL COMMENT '圈子id',
  PRIMARY KEY (`affix_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='附件表';

-- ----------------------------
-- Records of circle_affix
-- ----------------------------

-- ----------------------------
-- Table structure for circle_class
-- ----------------------------
DROP TABLE IF EXISTS `circle_class`;
CREATE TABLE `circle_class` (
  `class_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '圈子分类id',
  `class_name` varchar(50) NOT NULL COMMENT '圈子分类名称',
  `class_addtime` varchar(10) NOT NULL COMMENT '圈子分类创建时间',
  `class_sort` tinyint(3) unsigned NOT NULL COMMENT '圈子分类排序',
  `class_status` tinyint(3) unsigned NOT NULL COMMENT '圈子分类状态 0不显示，1显示',
  `is_recommend` tinyint(3) unsigned NOT NULL COMMENT '是否推荐 0未推荐，1已推荐',
  PRIMARY KEY (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='圈子分类表';

-- ----------------------------
-- Records of circle_class
-- ----------------------------

-- ----------------------------
-- Table structure for circle_explog
-- ----------------------------
DROP TABLE IF EXISTS `circle_explog`;
CREATE TABLE `circle_explog` (
  `el_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '经验日志id',
  `circle_id` int(11) unsigned NOT NULL COMMENT '圈子id',
  `member_id` int(11) unsigned NOT NULL COMMENT '成员id',
  `member_name` varchar(50) NOT NULL COMMENT '成员名称',
  `el_exp` int(10) NOT NULL COMMENT '获得经验',
  `el_time` varchar(10) NOT NULL COMMENT '获得时间',
  `el_type` tinyint(3) unsigned NOT NULL COMMENT '类型 1管理员操作 2发表话题 3发表回复 4话题被回复 5话题被删除 6回复被删除',
  `el_itemid` varchar(100) NOT NULL COMMENT '信息id',
  `el_desc` varchar(255) NOT NULL COMMENT '描述',
  PRIMARY KEY (`el_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='经验日志表';

-- ----------------------------
-- Records of circle_explog
-- ----------------------------

-- ----------------------------
-- Table structure for circle_expmember
-- ----------------------------
DROP TABLE IF EXISTS `circle_expmember`;
CREATE TABLE `circle_expmember` (
  `member_id` int(11) NOT NULL COMMENT '成员id',
  `circle_id` int(11) unsigned NOT NULL COMMENT '圈子id',
  `em_exp` int(10) NOT NULL COMMENT '获得经验',
  `em_time` varchar(10) NOT NULL COMMENT '获得时间',
  PRIMARY KEY (`member_id`,`circle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='成员每天获得经验表';

-- ----------------------------
-- Records of circle_expmember
-- ----------------------------

-- ----------------------------
-- Table structure for circle_exptheme
-- ----------------------------
DROP TABLE IF EXISTS `circle_exptheme`;
CREATE TABLE `circle_exptheme` (
  `theme_id` int(11) unsigned NOT NULL COMMENT '主题id',
  `et_exp` int(10) NOT NULL COMMENT '获得经验',
  `et_time` varchar(10) NOT NULL COMMENT '获得时间',
  PRIMARY KEY (`theme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='主题每天获得经验表';

-- ----------------------------
-- Records of circle_exptheme
-- ----------------------------

-- ----------------------------
-- Table structure for circle_fs
-- ----------------------------
DROP TABLE IF EXISTS `circle_fs`;
CREATE TABLE `circle_fs` (
  `circle_id` int(11) unsigned NOT NULL COMMENT '圈子id',
  `friendship_id` int(11) unsigned NOT NULL COMMENT '友情圈子id',
  `friendship_name` varchar(11) NOT NULL COMMENT '友情圈子名称',
  `friendship_sort` tinyint(4) unsigned NOT NULL COMMENT '友情圈子排序',
  `friendship_status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '友情圈子名称 1显示 0隐藏',
  PRIMARY KEY (`circle_id`,`friendship_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='友情圈子表';

-- ----------------------------
-- Records of circle_fs
-- ----------------------------

-- ----------------------------
-- Table structure for circle_inform
-- ----------------------------
DROP TABLE IF EXISTS `circle_inform`;
CREATE TABLE `circle_inform` (
  `inform_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '举报id',
  `circle_id` int(11) unsigned NOT NULL COMMENT '圈子id',
  `circle_name` varchar(12) NOT NULL COMMENT '圈子名称',
  `theme_id` int(11) unsigned NOT NULL COMMENT '话题id',
  `theme_name` varchar(50) NOT NULL COMMENT '主题名称',
  `reply_id` int(11) unsigned NOT NULL COMMENT '回复id',
  `member_id` int(11) unsigned NOT NULL COMMENT '会员id',
  `member_name` varchar(50) NOT NULL COMMENT '会员名称',
  `inform_content` varchar(255) NOT NULL COMMENT '举报内容',
  `inform_time` varchar(10) NOT NULL COMMENT '举报时间',
  `inform_type` tinyint(4) NOT NULL COMMENT '类型 0话题、1回复',
  `inform_state` tinyint(4) NOT NULL COMMENT '状态 0未处理、1已处理',
  `inform_opid` int(11) unsigned DEFAULT '0' COMMENT '操作人id',
  `inform_opname` varchar(50) DEFAULT '' COMMENT '操作人名称',
  `inform_opexp` tinyint(4) DEFAULT '0' COMMENT '操作经验',
  `inform_opresult` varchar(255) DEFAULT '' COMMENT '处理结果',
  PRIMARY KEY (`inform_id`),
  KEY `circle_id` (`circle_id`,`theme_id`,`reply_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='圈子举报表';

-- ----------------------------
-- Records of circle_inform
-- ----------------------------

-- ----------------------------
-- Table structure for circle_like
-- ----------------------------
DROP TABLE IF EXISTS `circle_like`;
CREATE TABLE `circle_like` (
  `theme_id` int(11) unsigned NOT NULL COMMENT '主题id',
  `member_id` int(11) unsigned NOT NULL COMMENT '会员id',
  `circle_id` int(11) unsigned NOT NULL COMMENT '圈子id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='主题赞表';

-- ----------------------------
-- Records of circle_like
-- ----------------------------

-- ----------------------------
-- Table structure for circle_mapply
-- ----------------------------
DROP TABLE IF EXISTS `circle_mapply`;
CREATE TABLE `circle_mapply` (
  `mapply_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '申请id',
  `circle_id` int(11) unsigned NOT NULL COMMENT '圈子id',
  `member_id` int(11) unsigned NOT NULL COMMENT '成员id',
  `mapply_reason` varchar(255) NOT NULL COMMENT '申请理由',
  `mapply_time` varchar(10) NOT NULL COMMENT '申请时间',
  PRIMARY KEY (`mapply_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='申请管理表';

-- ----------------------------
-- Records of circle_mapply
-- ----------------------------

-- ----------------------------
-- Table structure for circle_member
-- ----------------------------
DROP TABLE IF EXISTS `circle_member`;
CREATE TABLE `circle_member` (
  `member_id` int(11) unsigned NOT NULL COMMENT '会员id',
  `circle_id` int(11) unsigned NOT NULL COMMENT '圈子id',
  `circle_name` varchar(12) DEFAULT NULL COMMENT '圈子名称',
  `member_name` varchar(50) NOT NULL COMMENT '会员名称',
  `cm_applycontent` varchar(255) DEFAULT '' COMMENT '申请内容',
  `cm_applytime` varchar(10) DEFAULT NULL COMMENT '申请时间',
  `cm_state` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0申请中 1通过 2未通过',
  `cm_intro` varchar(255) DEFAULT '' COMMENT '自我介绍',
  `cm_jointime` varchar(10) NOT NULL COMMENT '加入时间',
  `cm_level` int(11) NOT NULL DEFAULT '1' COMMENT '成员级别',
  `cm_levelname` varchar(10) NOT NULL DEFAULT '初级粉丝' COMMENT '成员头衔',
  `cm_exp` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '会员经验',
  `cm_nextexp` int(10) NOT NULL DEFAULT '5' COMMENT '下一级所需经验',
  `is_identity` tinyint(3) unsigned DEFAULT NULL COMMENT '1圈主 2管理 3成员',
  `is_allowspeak` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否允许发言 1允许 0禁止',
  `is_star` tinyint(4) NOT NULL DEFAULT '0' COMMENT '明星成员 1是 0否',
  `cm_thcount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '主题数',
  `cm_comcount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '回复数',
  `cm_lastspeaktime` varchar(10) DEFAULT '' COMMENT '最后发言时间',
  `is_recommend` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否推荐 1是 0否',
  PRIMARY KEY (`member_id`,`circle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='圈子会员表';

-- ----------------------------
-- Records of circle_member
-- ----------------------------

-- ----------------------------
-- Table structure for circle_ml
-- ----------------------------
DROP TABLE IF EXISTS `circle_ml`;
CREATE TABLE `circle_ml` (
  `circle_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '圈子id',
  `mlref_id` int(10) DEFAULT NULL COMMENT '参考头衔id 0为默认 null为自定义',
  `ml_1` varchar(10) NOT NULL COMMENT '1级头衔名称',
  `ml_2` varchar(10) NOT NULL COMMENT '2级头衔名称',
  `ml_3` varchar(10) NOT NULL COMMENT '3级头衔名称',
  `ml_4` varchar(10) NOT NULL COMMENT '4级头衔名称',
  `ml_5` varchar(10) NOT NULL COMMENT '5级头衔名称',
  `ml_6` varchar(10) NOT NULL COMMENT '6级头衔名称',
  `ml_7` varchar(10) NOT NULL COMMENT '7级头衔名称',
  `ml_8` varchar(10) NOT NULL COMMENT '8级头衔名称',
  `ml_9` varchar(10) NOT NULL COMMENT '9级头衔名称',
  `ml_10` varchar(10) NOT NULL COMMENT '10级头衔名称',
  `ml_11` varchar(10) NOT NULL COMMENT '11级头衔名称',
  `ml_12` varchar(10) NOT NULL COMMENT '12级头衔名称',
  `ml_13` varchar(10) NOT NULL COMMENT '13级头衔名称',
  `ml_14` varchar(10) NOT NULL COMMENT '14级头衔名称',
  `ml_15` varchar(10) NOT NULL COMMENT '15级头衔名称',
  `ml_16` varchar(10) NOT NULL COMMENT '16级头衔名称',
  PRIMARY KEY (`circle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员头衔表';

-- ----------------------------
-- Records of circle_ml
-- ----------------------------

-- ----------------------------
-- Table structure for circle_mldefault
-- ----------------------------
DROP TABLE IF EXISTS `circle_mldefault`;
CREATE TABLE `circle_mldefault` (
  `mld_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '头衔等级',
  `mld_name` varchar(10) NOT NULL COMMENT '头衔名称',
  `mld_exp` int(10) NOT NULL COMMENT '所需经验值',
  PRIMARY KEY (`mld_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='成员头衔默认设置表';

-- ----------------------------
-- Records of circle_mldefault
-- ----------------------------

-- ----------------------------
-- Table structure for circle_mlref
-- ----------------------------
DROP TABLE IF EXISTS `circle_mlref`;
CREATE TABLE `circle_mlref` (
  `mlref_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '参考头衔id',
  `mlref_name` varchar(10) NOT NULL COMMENT '参考头衔名称',
  `mlref_addtime` varchar(10) NOT NULL COMMENT '创建时间',
  `mlref_status` tinyint(3) unsigned NOT NULL COMMENT '状态',
  `mlref_1` varchar(10) NOT NULL COMMENT '1级头衔名称',
  `mlref_2` varchar(10) NOT NULL COMMENT '2级头衔名称',
  `mlref_3` varchar(10) NOT NULL COMMENT '3级头衔名称',
  `mlref_4` varchar(10) NOT NULL COMMENT '4级头衔名称',
  `mlref_5` varchar(10) NOT NULL COMMENT '5级头衔名称',
  `mlref_6` varchar(10) NOT NULL COMMENT '6级头衔名称',
  `mlref_7` varchar(10) NOT NULL COMMENT '7级头衔名称',
  `mlref_8` varchar(10) NOT NULL COMMENT '8级头衔名称',
  `mlref_9` varchar(10) NOT NULL COMMENT '9级头衔名称',
  `mlref_10` varchar(10) NOT NULL COMMENT '10级头衔名称',
  `mlref_11` varchar(10) NOT NULL COMMENT '11级头衔名称',
  `mlref_12` varchar(10) NOT NULL COMMENT '12级头衔名称',
  `mlref_13` varchar(10) NOT NULL COMMENT '13级头衔名称',
  `mlref_14` varchar(10) NOT NULL COMMENT '14级头衔名称',
  `mlref_15` varchar(10) NOT NULL COMMENT '15级头衔名称',
  `mlref_16` varchar(10) NOT NULL COMMENT '16级头衔名称',
  PRIMARY KEY (`mlref_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员参考头衔表';

-- ----------------------------
-- Records of circle_mlref
-- ----------------------------

-- ----------------------------
-- Table structure for circle_recycle
-- ----------------------------
DROP TABLE IF EXISTS `circle_recycle`;
CREATE TABLE `circle_recycle` (
  `recycle_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '回收站id',
  `member_id` int(11) NOT NULL COMMENT '会员id',
  `member_name` varchar(50) NOT NULL COMMENT '会员名称',
  `circle_id` int(11) unsigned NOT NULL COMMENT '圈子id',
  `circle_name` varchar(12) NOT NULL COMMENT '圈子名称',
  `theme_name` varchar(50) NOT NULL COMMENT '主题名称',
  `recycle_content` text NOT NULL COMMENT '内容',
  `recycle_opid` int(11) unsigned NOT NULL COMMENT '操作人id',
  `recycle_opname` varchar(50) NOT NULL COMMENT '操作人名称',
  `recycle_type` tinyint(3) unsigned NOT NULL COMMENT '类型 1话题，2回复',
  `recycle_time` varchar(10) NOT NULL COMMENT '操作时间',
  PRIMARY KEY (`recycle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='圈子回收站表';

-- ----------------------------
-- Records of circle_recycle
-- ----------------------------

-- ----------------------------
-- Table structure for circle_thclass
-- ----------------------------
DROP TABLE IF EXISTS `circle_thclass`;
CREATE TABLE `circle_thclass` (
  `thclass_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主题分类id',
  `thclass_name` varchar(20) NOT NULL COMMENT '主题名称',
  `thclass_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '主题状态 1开启，0关闭',
  `is_moderator` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '管理专属 1是，0否',
  `thclass_sort` tinyint(3) unsigned NOT NULL COMMENT '分类排序',
  `circle_id` int(11) unsigned NOT NULL COMMENT '所属圈子id',
  PRIMARY KEY (`thclass_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='圈子主题分类表';

-- ----------------------------
-- Records of circle_thclass
-- ----------------------------

-- ----------------------------
-- Table structure for circle_theme
-- ----------------------------
DROP TABLE IF EXISTS `circle_theme`;
CREATE TABLE `circle_theme` (
  `theme_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主题id',
  `theme_name` varchar(50) NOT NULL COMMENT '主题名称',
  `theme_content` text NOT NULL COMMENT '主题内容',
  `circle_id` int(11) unsigned NOT NULL COMMENT '圈子id',
  `circle_name` varchar(12) NOT NULL COMMENT '圈子名称',
  `thclass_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '主题分类id',
  `thclass_name` varchar(20) DEFAULT '' COMMENT '主题分类名称',
  `member_id` int(11) unsigned NOT NULL COMMENT '会员id',
  `member_name` varchar(50) NOT NULL COMMENT '会员名称',
  `is_identity` tinyint(3) unsigned NOT NULL COMMENT '1圈主 2管理 3成员',
  `theme_addtime` varchar(10) NOT NULL COMMENT '主题发表时间',
  `theme_editname` varchar(50) DEFAULT NULL COMMENT '编辑人名称',
  `theme_edittime` varchar(10) DEFAULT NULL COMMENT '主题编辑时间',
  `theme_likecount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '喜欢数量',
  `theme_commentcount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '评论数量',
  `theme_browsecount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '浏览数量',
  `theme_sharecount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分享数量',
  `is_stick` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否置顶 1是  0否',
  `is_digest` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否加精 1是 0否',
  `lastspeak_id` int(11) unsigned DEFAULT NULL COMMENT '最后发言人id',
  `lastspeak_name` varchar(50) DEFAULT NULL COMMENT '最后发言人名称',
  `lastspeak_time` varchar(10) DEFAULT NULL COMMENT '最后发言时间',
  `has_goods` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '商品标记 1是 0否',
  `has_affix` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '附件标记 1是 0 否',
  `is_closed` tinyint(4) NOT NULL DEFAULT '0' COMMENT '屏蔽 1是 0否',
  `is_recommend` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否推荐 1是 0否',
  `is_shut` tinyint(4) NOT NULL DEFAULT '0' COMMENT '主题是否关闭 1是 0否',
  `theme_exp` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '获得经验',
  `theme_readperm` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '阅读权限',
  `theme_special` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '特殊话题 0普通 1投票',
  PRIMARY KEY (`theme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='圈子主题表';

-- ----------------------------
-- Records of circle_theme
-- ----------------------------

-- ----------------------------
-- Table structure for circle_thg
-- ----------------------------
DROP TABLE IF EXISTS `circle_thg`;
CREATE TABLE `circle_thg` (
  `themegoods_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主题商品id',
  `theme_id` int(11) NOT NULL COMMENT '主题id',
  `reply_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '回复id',
  `circle_id` int(11) unsigned NOT NULL COMMENT '圈子id',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `goods_name` varchar(100) NOT NULL COMMENT '商品名称',
  `goods_price` decimal(10,2) NOT NULL COMMENT '商品价格',
  `goods_image` varchar(1000) NOT NULL COMMENT '商品图片',
  `store_id` int(11) NOT NULL COMMENT '店铺id',
  `thg_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '商品类型 0为本商城、1为淘宝 默认为0',
  `thg_url` varchar(1000) DEFAULT NULL COMMENT '商品链接',
  PRIMARY KEY (`themegoods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='主题商品表';

-- ----------------------------
-- Records of circle_thg
-- ----------------------------

-- ----------------------------
-- Table structure for circle_thpoll
-- ----------------------------
DROP TABLE IF EXISTS `circle_thpoll`;
CREATE TABLE `circle_thpoll` (
  `theme_id` int(11) unsigned NOT NULL COMMENT '话题id',
  `poll_multiple` tinyint(3) unsigned NOT NULL COMMENT '单/多选 0单选、1多选',
  `poll_startime` varchar(10) NOT NULL COMMENT '开始时间',
  `poll_endtime` varchar(10) NOT NULL COMMENT '结束时间',
  `poll_days` tinyint(3) unsigned NOT NULL COMMENT '投票天数',
  `poll_voters` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '投票参与人数',
  PRIMARY KEY (`theme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='投票表';

-- ----------------------------
-- Records of circle_thpoll
-- ----------------------------

-- ----------------------------
-- Table structure for circle_thpolloption
-- ----------------------------
DROP TABLE IF EXISTS `circle_thpolloption`;
CREATE TABLE `circle_thpolloption` (
  `pollop_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '投票选项id',
  `theme_id` int(11) unsigned NOT NULL COMMENT '话题id',
  `pollop_option` varchar(80) NOT NULL COMMENT '投票选项',
  `pollop_votes` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '得票数',
  `pollop_sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `pollop_votername` mediumtext COMMENT '投票者名称',
  PRIMARY KEY (`pollop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='投票选项表';

-- ----------------------------
-- Records of circle_thpolloption
-- ----------------------------

-- ----------------------------
-- Table structure for circle_thpollvoter
-- ----------------------------
DROP TABLE IF EXISTS `circle_thpollvoter`;
CREATE TABLE `circle_thpollvoter` (
  `theme_id` int(11) unsigned NOT NULL COMMENT '话题id',
  `member_id` int(11) unsigned NOT NULL COMMENT '成员id',
  `member_name` varchar(50) NOT NULL COMMENT '成员名称',
  `pollvo_options` mediumtext NOT NULL COMMENT '投票选项',
  `pollvo_time` varchar(10) NOT NULL COMMENT '投票选项',
  KEY `theme_id` (`theme_id`,`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='成员投票信息表';

-- ----------------------------
-- Records of circle_thpollvoter
-- ----------------------------

-- ----------------------------
-- Table structure for circle_threply
-- ----------------------------
DROP TABLE IF EXISTS `circle_threply`;
CREATE TABLE `circle_threply` (
  `theme_id` int(11) unsigned NOT NULL COMMENT '主题id',
  `reply_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '评论id',
  `circle_id` int(11) unsigned NOT NULL COMMENT '圈子id',
  `member_id` int(11) unsigned NOT NULL COMMENT '会员id',
  `member_name` varchar(50) NOT NULL COMMENT '会员名称',
  `reply_content` text NOT NULL COMMENT '评论内容',
  `reply_addtime` varchar(10) NOT NULL COMMENT '发表时间',
  `reply_replyid` int(11) unsigned DEFAULT NULL COMMENT '回复楼层id',
  `reply_replyname` varchar(50) DEFAULT NULL COMMENT '回复楼层会员名称',
  `is_closed` tinyint(4) NOT NULL DEFAULT '0' COMMENT '屏蔽 1是 0否',
  `reply_exp` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '获得经验',
  PRIMARY KEY (`theme_id`,`reply_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='主题评论表';

-- ----------------------------
-- Records of circle_threply
-- ----------------------------

-- ----------------------------
-- Table structure for cms_article
-- ----------------------------
DROP TABLE IF EXISTS `cms_article`;
CREATE TABLE `cms_article` (
  `article_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文章编号',
  `article_title` varchar(50) NOT NULL COMMENT '文章标题',
  `article_class_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文章分类编号',
  `article_origin` varchar(50) DEFAULT NULL COMMENT '文章来源',
  `article_origin_address` varchar(255) DEFAULT NULL COMMENT '文章来源链接',
  `article_author` varchar(50) NOT NULL COMMENT '文章作者',
  `article_abstract` varchar(140) DEFAULT NULL COMMENT '文章摘要',
  `article_content` text COMMENT '文章正文',
  `article_image` varchar(255) DEFAULT NULL COMMENT '文章图片',
  `article_keyword` varchar(255) DEFAULT NULL COMMENT '文章关键字',
  `article_link` varchar(255) DEFAULT NULL COMMENT '相关文章',
  `article_goods` text COMMENT '相关商品',
  `article_start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文章有效期开始时间',
  `article_end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文章有效期结束时间',
  `article_publish_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文章发布时间',
  `article_click` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文章点击量',
  `article_sort` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '文章排序0-255',
  `article_commend_flag` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '文章推荐标志0-未推荐，1-已推荐',
  `article_comment_flag` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '文章是否允许评论1-允许，0-不允许',
  `article_verify_admin` varchar(50) DEFAULT NULL COMMENT '文章审核管理员',
  `article_verify_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文章审核时间',
  `article_state` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1-草稿、2-待审核、3-已发布、4-回收站',
  `article_publisher_name` varchar(50) NOT NULL COMMENT '发布者用户名 ',
  `article_publisher_id` int(10) unsigned NOT NULL COMMENT '发布者编号',
  `article_type` tinyint(1) unsigned NOT NULL COMMENT '文章类型1-管理员发布，2-用户投稿',
  `article_attachment_path` varchar(50) NOT NULL COMMENT '文章附件路径',
  `article_image_all` text COMMENT '文章全部图片',
  `article_modify_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文章修改时间',
  `article_tag` varchar(255) DEFAULT NULL COMMENT '文章标签',
  `article_comment_count` int(10) unsigned DEFAULT '0' COMMENT '文章评论数',
  `article_attitude_1` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文章心情1',
  `article_attitude_2` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文章心情2',
  `article_attitude_3` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文章心情3',
  `article_attitude_4` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文章心情4',
  `article_attitude_5` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文章心情5',
  `article_attitude_6` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文章心情6',
  `article_title_short` varchar(50) NOT NULL DEFAULT '' COMMENT '文章短标题',
  `article_attitude_flag` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '文章态度开关1-允许，0-不允许',
  `article_commend_image_flag` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '文章推荐标志(图文)',
  `article_share_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文章分享数',
  `article_verify_reason` varchar(255) DEFAULT NULL COMMENT '审核失败原因',
  PRIMARY KEY (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMS文章表';

-- ----------------------------
-- Records of cms_article
-- ----------------------------

-- ----------------------------
-- Table structure for cms_article_attitude
-- ----------------------------
DROP TABLE IF EXISTS `cms_article_attitude`;
CREATE TABLE `cms_article_attitude` (
  `attitude_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '心情编号',
  `attitude_article_id` int(10) unsigned NOT NULL COMMENT '文章编号',
  `attitude_member_id` int(10) unsigned NOT NULL COMMENT '用户编号',
  `attitude_time` int(10) unsigned NOT NULL COMMENT '发布心情时间',
  PRIMARY KEY (`attitude_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMS文章心情表';

-- ----------------------------
-- Records of cms_article_attitude
-- ----------------------------

-- ----------------------------
-- Table structure for cms_article_class
-- ----------------------------
DROP TABLE IF EXISTS `cms_article_class`;
CREATE TABLE `cms_article_class` (
  `class_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类编号 ',
  `class_name` varchar(50) NOT NULL COMMENT '分类名称',
  `class_sort` tinyint(1) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  PRIMARY KEY (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='cms文章分类表';

-- ----------------------------
-- Records of cms_article_class
-- ----------------------------

-- ----------------------------
-- Table structure for cms_comment
-- ----------------------------
DROP TABLE IF EXISTS `cms_comment`;
CREATE TABLE `cms_comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '评论编号',
  `comment_type` tinyint(1) NOT NULL COMMENT '评论类型编号',
  `comment_object_id` int(10) unsigned NOT NULL COMMENT '推荐商品编号',
  `comment_message` varchar(2000) NOT NULL COMMENT '评论内容',
  `comment_member_id` int(10) unsigned NOT NULL COMMENT '评论人编号',
  `comment_time` int(10) unsigned NOT NULL COMMENT '评论时间',
  `comment_quote` varchar(255) DEFAULT NULL COMMENT '评论引用',
  `comment_up` int(10) unsigned DEFAULT '0' COMMENT '顶数量',
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMS评论表';

-- ----------------------------
-- Records of cms_comment
-- ----------------------------

-- ----------------------------
-- Table structure for cms_comment_up
-- ----------------------------
DROP TABLE IF EXISTS `cms_comment_up`;
CREATE TABLE `cms_comment_up` (
  `up_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '顶编号',
  `comment_id` int(10) unsigned NOT NULL COMMENT '评论编号',
  `up_member_id` int(10) unsigned NOT NULL COMMENT '用户编号',
  `up_time` int(10) unsigned NOT NULL COMMENT '评论时间',
  PRIMARY KEY (`up_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMS评论顶表';

-- ----------------------------
-- Records of cms_comment_up
-- ----------------------------

-- ----------------------------
-- Table structure for cms_index_module
-- ----------------------------
DROP TABLE IF EXISTS `cms_index_module`;
CREATE TABLE `cms_index_module` (
  `module_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '模块编号',
  `module_title` varchar(50) DEFAULT '' COMMENT '模块标题',
  `module_name` varchar(50) NOT NULL COMMENT '模板名称',
  `module_type` varchar(50) DEFAULT '' COMMENT '模块类型，index-固定内容、article1-文章模块1、article2-文章模块2、micro-微商城、adv-通栏广告',
  `module_sort` tinyint(1) unsigned DEFAULT '255' COMMENT '排序',
  `module_state` tinyint(1) unsigned DEFAULT '1' COMMENT '状态1-显示、0-不显示',
  `module_content` text COMMENT '模块内容',
  `module_style` varchar(50) NOT NULL DEFAULT 'style1' COMMENT '模块主题',
  `module_view` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '后台列表显示样式 1-展开 2-折叠',
  PRIMARY KEY (`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMS首页模块表';

-- ----------------------------
-- Records of cms_index_module
-- ----------------------------

-- ----------------------------
-- Table structure for cms_module
-- ----------------------------
DROP TABLE IF EXISTS `cms_module`;
CREATE TABLE `cms_module` (
  `module_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '模板模块编号',
  `module_title` varchar(50) NOT NULL DEFAULT '' COMMENT '模板模块标题',
  `module_name` varchar(50) NOT NULL DEFAULT '' COMMENT '模板名称',
  `module_type` varchar(50) NOT NULL DEFAULT '' COMMENT '模板模块类型，index-固定内容、article1-文章模块1、article2-文章模块2、micro-微商城、adv-通栏广告',
  `module_class` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '模板模块种类1-系统自带 2-用户自定义',
  PRIMARY KEY (`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMS模板模块表';

-- ----------------------------
-- Records of cms_module
-- ----------------------------

-- ----------------------------
-- Table structure for cms_module_assembly
-- ----------------------------
DROP TABLE IF EXISTS `cms_module_assembly`;
CREATE TABLE `cms_module_assembly` (
  `assembly_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '组件编号',
  `assembly_title` varchar(50) NOT NULL COMMENT '组件标题',
  `assembly_name` varchar(50) NOT NULL COMMENT '组件名称',
  `assembly_explain` varchar(255) NOT NULL COMMENT '组件说明',
  PRIMARY KEY (`assembly_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='cms模块组件表';

-- ----------------------------
-- Records of cms_module_assembly
-- ----------------------------

-- ----------------------------
-- Table structure for cms_module_frame
-- ----------------------------
DROP TABLE IF EXISTS `cms_module_frame`;
CREATE TABLE `cms_module_frame` (
  `frame_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '框架编号',
  `frame_title` varchar(50) NOT NULL COMMENT '框架标题',
  `frame_name` varchar(50) NOT NULL COMMENT '框架名称',
  `frame_explain` varchar(255) NOT NULL COMMENT '框架说明',
  `frame_structure` varchar(255) NOT NULL COMMENT '框架结构',
  PRIMARY KEY (`frame_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='cms模块框架表';

-- ----------------------------
-- Records of cms_module_frame
-- ----------------------------

-- ----------------------------
-- Table structure for cms_navigation
-- ----------------------------
DROP TABLE IF EXISTS `cms_navigation`;
CREATE TABLE `cms_navigation` (
  `navigation_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '导航编号',
  `navigation_title` varchar(50) NOT NULL COMMENT '导航标题',
  `navigation_link` varchar(255) NOT NULL COMMENT '导航链接',
  `navigation_sort` tinyint(1) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  `navigation_open_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '导航打开方式1-本页打开，2-新页打开',
  PRIMARY KEY (`navigation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMS导航表';

-- ----------------------------
-- Records of cms_navigation
-- ----------------------------

-- ----------------------------
-- Table structure for cms_picture
-- ----------------------------
DROP TABLE IF EXISTS `cms_picture`;
CREATE TABLE `cms_picture` (
  `picture_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '画报编号',
  `picture_title` varchar(50) NOT NULL COMMENT '画报标题',
  `picture_class_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '画报分类编号',
  `picture_author` varchar(50) NOT NULL COMMENT '画报作者',
  `picture_abstract` varchar(140) DEFAULT NULL COMMENT '画报摘要',
  `picture_image` varchar(255) DEFAULT NULL COMMENT '画报图片',
  `picture_keyword` varchar(255) DEFAULT NULL COMMENT '画报关键字',
  `picture_publish_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '画报发布时间',
  `picture_click` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '画报点击量',
  `picture_sort` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '画报排序0-255',
  `picture_commend_flag` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '画报推荐标志1-未推荐，2-已推荐',
  `picture_comment_flag` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '画报是否允许评论1-允许，2-不允许',
  `picture_verify_admin` varchar(50) DEFAULT NULL COMMENT '画报审核管理员',
  `picture_verify_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '画报审核时间',
  `picture_state` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1-草稿、2-待审核、3-已发布、4-回收站、5-已关闭',
  `picture_publisher_name` varchar(50) NOT NULL COMMENT '发布人用户名',
  `picture_publisher_id` int(10) unsigned NOT NULL COMMENT '发布人编号',
  `picture_type` tinyint(1) unsigned NOT NULL COMMENT '画报类型1-管理员发布，2-用户投稿',
  `picture_attachment_path` varchar(50) NOT NULL DEFAULT '',
  `picture_modify_time` int(10) unsigned NOT NULL COMMENT '画报修改时间',
  `picture_tag` varchar(255) DEFAULT NULL COMMENT '画报标签',
  `picture_comment_count` int(10) unsigned DEFAULT '0' COMMENT '画报评论数',
  `picture_title_short` varchar(50) DEFAULT '' COMMENT '画报短标题',
  `picture_image_count` tinyint(1) unsigned DEFAULT '0' COMMENT '画报图片总数',
  `picture_share_count` int(10) unsigned DEFAULT '0' COMMENT '画报分享数',
  `picture_verify_reason` varchar(255) DEFAULT NULL COMMENT '审核失败原因',
  PRIMARY KEY (`picture_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMS画报表';

-- ----------------------------
-- Records of cms_picture
-- ----------------------------

-- ----------------------------
-- Table structure for cms_picture_class
-- ----------------------------
DROP TABLE IF EXISTS `cms_picture_class`;
CREATE TABLE `cms_picture_class` (
  `class_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类编号 ',
  `class_name` varchar(50) NOT NULL COMMENT '分类名称',
  `class_sort` tinyint(1) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  PRIMARY KEY (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='cms画报分类表';

-- ----------------------------
-- Records of cms_picture_class
-- ----------------------------

-- ----------------------------
-- Table structure for cms_picture_image
-- ----------------------------
DROP TABLE IF EXISTS `cms_picture_image`;
CREATE TABLE `cms_picture_image` (
  `image_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '图片编号',
  `image_name` varchar(255) NOT NULL COMMENT '图片地址',
  `image_abstract` varchar(200) DEFAULT NULL COMMENT '图片摘要',
  `image_goods` text COMMENT '相关商品',
  `image_store` varchar(255) DEFAULT NULL COMMENT '相关店铺',
  `image_width` int(10) unsigned DEFAULT NULL COMMENT '图片宽度',
  `image_height` int(10) unsigned DEFAULT NULL COMMENT '图片高度',
  `image_picture_id` int(10) unsigned NOT NULL COMMENT '画报编号',
  `image_path` varchar(50) DEFAULT NULL COMMENT '图片路径',
  PRIMARY KEY (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMS画报图片表';

-- ----------------------------
-- Records of cms_picture_image
-- ----------------------------

-- ----------------------------
-- Table structure for cms_special
-- ----------------------------
DROP TABLE IF EXISTS `cms_special`;
CREATE TABLE `cms_special` (
  `special_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '专题编号',
  `special_title` varchar(50) NOT NULL COMMENT '专题标题',
  `special_stitle` varchar(200) NOT NULL COMMENT '专题副标题',
  `special_margin_top` int(10) DEFAULT '0' COMMENT '正文距顶部距离',
  `special_background` varchar(255) DEFAULT NULL COMMENT '专题背景',
  `special_image` varchar(255) DEFAULT NULL COMMENT '专题封面图',
  `special_image_all` text COMMENT '专题图片',
  `special_content` text COMMENT '专题内容',
  `special_modify_time` int(10) unsigned NOT NULL COMMENT '专题修改时间',
  `special_publish_id` int(10) unsigned NOT NULL COMMENT '专题发布者编号',
  `special_state` tinyint(1) unsigned NOT NULL COMMENT '专题状态1-草稿、2-已发布',
  `special_background_color` varchar(10) NOT NULL DEFAULT '#FFFFFF' COMMENT '专题背景色',
  `special_repeat` varchar(10) NOT NULL DEFAULT 'no-repeat' COMMENT '背景重复方式',
  `special_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '专题类型(1-cms专题 2-商城专题)',
  PRIMARY KEY (`special_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMS专题表';

-- ----------------------------
-- Records of cms_special
-- ----------------------------

-- ----------------------------
-- Table structure for cms_tag
-- ----------------------------
DROP TABLE IF EXISTS `cms_tag`;
CREATE TABLE `cms_tag` (
  `tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '标签编号',
  `tag_name` varchar(50) NOT NULL COMMENT '标签名称',
  `tag_sort` tinyint(1) unsigned NOT NULL COMMENT '标签排序',
  `tag_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '标签使用计数',
  PRIMARY KEY (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMS标签表';

-- ----------------------------
-- Records of cms_tag
-- ----------------------------

-- ----------------------------
-- Table structure for cms_tag_relation
-- ----------------------------
DROP TABLE IF EXISTS `cms_tag_relation`;
CREATE TABLE `cms_tag_relation` (
  `relation_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '关系编号',
  `relation_type` tinyint(1) unsigned NOT NULL COMMENT '关系类型1-文章，2-画报',
  `relation_tag_id` int(10) unsigned NOT NULL COMMENT '标签编号',
  `relation_object_id` int(10) unsigned NOT NULL COMMENT '对象编号',
  PRIMARY KEY (`relation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMS标签关系表';

-- ----------------------------
-- Records of cms_tag_relation
-- ----------------------------

-- ----------------------------
-- Table structure for company
-- ----------------------------
DROP TABLE IF EXISTS `company`;
CREATE TABLE `company` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `company_sn` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '编码',
  `company_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '名称',
  `parent_id` int(11) NOT NULL DEFAULT '1' COMMENT 'PID',
  `contact` varchar(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '联系人',
  `phone` varchar(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '联系电话',
  `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '公司地址',
  `bank_of_deposit` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '开户银行',
  `account` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '开户银行账户',
  `receipt` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否能力开发票 0=不开1=有',
  `is_sign` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否财务签字；0为否；1为是',
  `remark` text CHARACTER SET utf8 COLLATE utf8_bin COMMENT '备注',
  `create_user` int(11) DEFAULT NULL COMMENT '创建人',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `is_deleted` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否有效 0有效1无效',
  `is_system` tinyint(4) NOT NULL DEFAULT '0',
  `is_shengdai` tinyint(1) unsigned DEFAULT '0' COMMENT '是否省代',
  `sd_company_id` int(11) unsigned DEFAULT NULL COMMENT '所属省代公司ID',
  `company_type` tinyint(2) DEFAULT NULL COMMENT '公司类型 1直营店 2个体店 3经销商',
  `processor_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公司信息表';

-- ----------------------------
-- Records of company
-- ----------------------------

-- ----------------------------
-- Table structure for complain
-- ----------------------------
DROP TABLE IF EXISTS `complain`;
CREATE TABLE `complain` (
  `complain_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '投诉id',
  `order_id` int(11) NOT NULL COMMENT '订单id',
  `order_goods_id` int(10) unsigned DEFAULT '0' COMMENT '订单商品ID',
  `accuser_id` int(11) NOT NULL COMMENT '原告id',
  `accuser_name` varchar(50) NOT NULL COMMENT '原告名称',
  `accused_id` int(11) NOT NULL COMMENT '被告id',
  `accused_name` varchar(50) NOT NULL COMMENT '被告名称',
  `complain_subject_content` varchar(50) NOT NULL COMMENT '投诉主题',
  `complain_subject_id` int(11) NOT NULL COMMENT '投诉主题id',
  `complain_content` varchar(255) DEFAULT NULL COMMENT '投诉内容',
  `complain_pic1` varchar(100) DEFAULT NULL COMMENT '投诉图片1',
  `complain_pic2` varchar(100) DEFAULT NULL COMMENT '投诉图片2',
  `complain_pic3` varchar(100) DEFAULT NULL COMMENT '投诉图片3',
  `complain_datetime` int(11) NOT NULL COMMENT '投诉时间',
  `complain_handle_datetime` int(11) DEFAULT NULL COMMENT '投诉处理时间',
  `complain_handle_member_id` int(11) DEFAULT NULL COMMENT '投诉处理人id',
  `appeal_message` varchar(255) DEFAULT NULL COMMENT '申诉内容',
  `appeal_datetime` int(11) DEFAULT NULL COMMENT '申诉时间',
  `appeal_pic1` varchar(100) DEFAULT NULL COMMENT '申诉图片1',
  `appeal_pic2` varchar(100) DEFAULT NULL COMMENT '申诉图片2',
  `appeal_pic3` varchar(100) DEFAULT NULL COMMENT '申诉图片3',
  `final_handle_message` varchar(255) DEFAULT NULL COMMENT '最终处理意见',
  `final_handle_datetime` int(11) DEFAULT NULL COMMENT '最终处理时间',
  `final_handle_member_id` int(11) DEFAULT NULL COMMENT '最终处理人id',
  `complain_state` tinyint(4) NOT NULL COMMENT '投诉状态(10-新投诉/20-投诉通过转给被投诉人/30-被投诉人已申诉/40-提交仲裁/99-已关闭)',
  `complain_active` tinyint(4) NOT NULL DEFAULT '1' COMMENT '投诉是否通过平台审批(1未通过/2通过)',
  PRIMARY KEY (`complain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='投诉表';

-- ----------------------------
-- Records of complain
-- ----------------------------

-- ----------------------------
-- Table structure for complain_subject
-- ----------------------------
DROP TABLE IF EXISTS `complain_subject`;
CREATE TABLE `complain_subject` (
  `complain_subject_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '投诉主题id',
  `complain_subject_content` varchar(50) NOT NULL COMMENT '投诉主题',
  `complain_subject_desc` varchar(100) NOT NULL COMMENT '投诉主题描述',
  `complain_subject_state` tinyint(4) NOT NULL COMMENT '投诉主题状态(1-有效/2-失效)',
  PRIMARY KEY (`complain_subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='投诉主题表';

-- ----------------------------
-- Records of complain_subject
-- ----------------------------

-- ----------------------------
-- Table structure for complain_talk
-- ----------------------------
DROP TABLE IF EXISTS `complain_talk`;
CREATE TABLE `complain_talk` (
  `talk_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '投诉对话id',
  `complain_id` int(11) NOT NULL COMMENT '投诉id',
  `talk_member_id` int(11) NOT NULL COMMENT '发言人id',
  `talk_member_name` varchar(50) NOT NULL COMMENT '发言人名称',
  `talk_member_type` varchar(10) NOT NULL COMMENT '发言人类型(1-投诉人/2-被投诉人/3-平台)',
  `talk_content` varchar(255) NOT NULL COMMENT '发言内容',
  `talk_state` tinyint(4) NOT NULL COMMENT '发言状态(1-显示/2-不显示)',
  `talk_admin` int(11) NOT NULL DEFAULT '0' COMMENT '对话管理员，屏蔽对话人的id',
  `talk_datetime` int(11) NOT NULL COMMENT '对话发表时间',
  PRIMARY KEY (`talk_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='投诉对话表';

-- ----------------------------
-- Records of complain_talk
-- ----------------------------

-- ----------------------------
-- Table structure for consult
-- ----------------------------
DROP TABLE IF EXISTS `consult`;
CREATE TABLE `consult` (
  `consult_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '咨询编号',
  `goods_id` int(11) unsigned DEFAULT '0' COMMENT '商品编号',
  `goods_name` varchar(100) NOT NULL COMMENT '商品名称',
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '咨询发布者会员编号(0：游客)',
  `member_name` varchar(100) DEFAULT NULL COMMENT '会员名称',
  `store_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '店铺编号',
  `store_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `ct_id` int(10) unsigned NOT NULL COMMENT '咨询类型',
  `consult_content` varchar(255) DEFAULT NULL COMMENT '咨询内容',
  `consult_addtime` int(10) DEFAULT NULL COMMENT '咨询发布时间',
  `consult_reply` varchar(255) DEFAULT '' COMMENT '咨询回复内容',
  `consult_reply_time` int(10) DEFAULT NULL COMMENT '咨询回复时间',
  `isanonymous` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0表示不匿名 1表示匿名',
  PRIMARY KEY (`consult_id`),
  KEY `goods_id` (`goods_id`),
  KEY `seller_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品咨询表';

-- ----------------------------
-- Records of consult
-- ----------------------------

-- ----------------------------
-- Table structure for consult_type
-- ----------------------------
DROP TABLE IF EXISTS `consult_type`;
CREATE TABLE `consult_type` (
  `ct_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '咨询类型id',
  `ct_name` varchar(10) NOT NULL COMMENT '咨询类型名称',
  `ct_introduce` text NOT NULL COMMENT '咨询类型详细介绍',
  `ct_sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '咨询类型排序',
  PRIMARY KEY (`ct_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='咨询类型表';

-- ----------------------------
-- Records of consult_type
-- ----------------------------

-- ----------------------------
-- Table structure for consume
-- ----------------------------
DROP TABLE IF EXISTS `consume`;
CREATE TABLE `consume` (
  `consume_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '消费表',
  `member_id` int(10) unsigned NOT NULL COMMENT '会员id',
  `member_name` varchar(50) NOT NULL COMMENT '会员名称',
  `consume_amount` decimal(10,2) NOT NULL COMMENT '金额',
  `consume_time` int(10) unsigned NOT NULL COMMENT '时间',
  `consume_remark` varchar(200) NOT NULL COMMENT '备注',
  PRIMARY KEY (`consume_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消费记录';

-- ----------------------------
-- Records of consume
-- ----------------------------

-- ----------------------------
-- Table structure for contract
-- ----------------------------
DROP TABLE IF EXISTS `contract`;
CREATE TABLE `contract` (
  `ct_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `ct_storeid` int(11) NOT NULL COMMENT '店铺ID',
  `ct_storename` varchar(500) NOT NULL COMMENT '店铺名称',
  `ct_itemid` int(11) NOT NULL COMMENT '服务项目ID',
  `ct_auditstate` tinyint(1) NOT NULL DEFAULT '0' COMMENT '申请审核状态0未审核1审核通过2审核失败3已支付保证金4保证金审核通过5保证金审核失败',
  `ct_joinstate` tinyint(1) NOT NULL DEFAULT '0' COMMENT '加入状态 0未申请 1已申请 2已加入',
  `ct_cost` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '保证金余额',
  `ct_closestate` tinyint(1) NOT NULL DEFAULT '1' COMMENT '永久关闭 0永久关闭 1开启',
  `ct_quitstate` tinyint(1) NOT NULL DEFAULT '0' COMMENT '退出申请状态0未申请 1已申请 2申请失败',
  PRIMARY KEY (`ct_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺消费者保障服务加入情况表';

-- ----------------------------
-- Records of contract
-- ----------------------------

-- ----------------------------
-- Table structure for contract_apply
-- ----------------------------
DROP TABLE IF EXISTS `contract_apply`;
CREATE TABLE `contract_apply` (
  `cta_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '申请ID',
  `cta_itemid` int(11) NOT NULL COMMENT '保障项目ID',
  `cta_storeid` int(11) NOT NULL COMMENT '店铺ID',
  `cta_storename` varchar(500) NOT NULL COMMENT '店铺名称',
  `cta_addtime` int(11) NOT NULL COMMENT '申请时间',
  `cta_auditstate` tinyint(1) NOT NULL DEFAULT '0' COMMENT '审核状态 0未审核 1审核通过 2审核失败 3保证金待审核 4保证金审核通过 5保证金审核失败',
  `cta_cost` decimal(10,2) DEFAULT '0.00' COMMENT '保证金金额',
  `cta_costimg` varchar(500) DEFAULT NULL COMMENT '保证金付款凭证图片',
  PRIMARY KEY (`cta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺加入消费者保障服务申请表';

-- ----------------------------
-- Records of contract_apply
-- ----------------------------

-- ----------------------------
-- Table structure for contract_costlog
-- ----------------------------
DROP TABLE IF EXISTS `contract_costlog`;
CREATE TABLE `contract_costlog` (
  `clog_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `clog_itemid` int(11) NOT NULL COMMENT '保障项目ID',
  `clog_itemname` varchar(100) NOT NULL COMMENT '保障项目名称',
  `clog_storeid` int(11) NOT NULL COMMENT '店铺ID',
  `clog_storename` varchar(500) NOT NULL COMMENT '店铺名称',
  `clog_adminid` int(11) DEFAULT NULL COMMENT '操作管理员ID',
  `clog_adminname` varchar(200) DEFAULT NULL COMMENT '操作管理员名称',
  `clog_price` decimal(10,2) NOT NULL COMMENT '金额',
  `clog_addtime` int(11) NOT NULL COMMENT '添加时间',
  `clog_desc` varchar(2000) NOT NULL COMMENT '描述',
  PRIMARY KEY (`clog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺消费者保障服务保证金日志表';

-- ----------------------------
-- Records of contract_costlog
-- ----------------------------

-- ----------------------------
-- Table structure for contract_item
-- ----------------------------
DROP TABLE IF EXISTS `contract_item`;
CREATE TABLE `contract_item` (
  `cti_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `cti_name` varchar(100) NOT NULL COMMENT '保障项目名称',
  `cti_describe` varchar(2000) NOT NULL COMMENT '保障项目描述',
  `cti_cost` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '保证金',
  `cti_icon` varchar(500) NOT NULL COMMENT '图标',
  `cti_descurl` varchar(500) DEFAULT NULL COMMENT '内容介绍文章地址',
  `cti_state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 0关闭 1开启',
  `cti_sort` int(11) DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`cti_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消费者保障服务项目表';

-- ----------------------------
-- Records of contract_item
-- ----------------------------

-- ----------------------------
-- Table structure for contract_log
-- ----------------------------
DROP TABLE IF EXISTS `contract_log`;
CREATE TABLE `contract_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `log_storeid` int(11) NOT NULL COMMENT '店铺ID',
  `log_storename` varchar(500) NOT NULL COMMENT '店铺名称',
  `log_itemid` int(11) NOT NULL COMMENT '服务项目ID',
  `log_itemname` varchar(100) NOT NULL COMMENT '服务项目名称',
  `log_msg` varchar(1000) NOT NULL COMMENT '操作描述',
  `log_addtime` int(11) NOT NULL COMMENT '添加时间',
  `log_role` varchar(100) NOT NULL COMMENT '操作者角色 管理员为admin 商家为seller',
  `log_userid` int(11) NOT NULL COMMENT '操作者ID',
  `log_username` varchar(200) NOT NULL COMMENT '操作者名称',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺消费者保障服务日志表';

-- ----------------------------
-- Records of contract_log
-- ----------------------------

-- ----------------------------
-- Table structure for contract_quitapply
-- ----------------------------
DROP TABLE IF EXISTS `contract_quitapply`;
CREATE TABLE `contract_quitapply` (
  `ctq_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '退出申请ID',
  `ctq_itemid` int(11) NOT NULL COMMENT '项目ID',
  `ctq_itemname` varchar(200) NOT NULL COMMENT '项目名称',
  `ctq_storeid` int(11) NOT NULL COMMENT '店铺ID',
  `ctq_storename` varchar(500) NOT NULL COMMENT '店铺名称',
  `ctq_addtime` int(11) NOT NULL COMMENT '添加时间',
  `ctq_auditstate` tinyint(4) NOT NULL COMMENT '审核状态0未审核1审核通过2审核失败',
  PRIMARY KEY (`ctq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺消费者保障服务退出申请表';

-- ----------------------------
-- Records of contract_quitapply
-- ----------------------------

-- ----------------------------
-- Table structure for cron
-- ----------------------------
DROP TABLE IF EXISTS `cron`;
CREATE TABLE `cron` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned DEFAULT NULL COMMENT '任务类型 1商品上架 2根据商品id更新商品促销价格 3优惠套装过期 4推荐展位过期 5抢购开始更新商品促销价格 6抢购过期 7限时折扣过期 8加价购过期 9商品消费者保障服务开启状态更新 10手机专享过期',
  `exeid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联任务的ID[如商品ID,会员ID]',
  `exetime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '执行时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='任务队列表';

-- ----------------------------
-- Records of cron
-- ----------------------------

-- ----------------------------
-- Table structure for customer_sources
-- ----------------------------
DROP TABLE IF EXISTS `customer_sources`;
CREATE TABLE `customer_sources` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `source_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '来源名称',
  `source_code` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '来源编码',
  `source_class` tinyint(4) unsigned DEFAULT NULL COMMENT '1线上，2线下',
  `source_type` tinyint(4) unsigned DEFAULT NULL COMMENT '1部门，2体验店，3公司',
  `source_own_id` int(8) DEFAULT NULL COMMENT '所属ID',
  `source_own` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT 'sales_channels.id "销售渠道表"',
  `add_id` int(11) unsigned DEFAULT NULL COMMENT '创建人',
  `add_time` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_id` int(11) unsigned DEFAULT NULL COMMENT '更新人',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  `is_deleted` tinyint(1) unsigned DEFAULT '0' COMMENT '删除标识',
  `is_pay` int(2) NOT NULL DEFAULT '0' COMMENT '是否收款（财务应收专用字段）',
  `fenlei` tinyint(4) NOT NULL DEFAULT '0' COMMENT '所属分类 0:全部、-1:其他、1:异业联盟、2:社区、3:珂兰相关、4:团购、5:老顾客、6:数据、7:网络来源',
  `is_enabled` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否启用',
  PRIMARY KEY (`id`),
  KEY `source_code` (`source_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客户来源表';

-- ----------------------------
-- Records of customer_sources
-- ----------------------------

-- ----------------------------
-- Table structure for daddress
-- ----------------------------
DROP TABLE IF EXISTS `daddress`;
CREATE TABLE `daddress` (
  `address_id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '地址ID',
  `store_id` mediumint(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺ID',
  `seller_name` varchar(50) NOT NULL DEFAULT '' COMMENT '联系人',
  `area_id` mediumint(10) unsigned NOT NULL DEFAULT '0' COMMENT '地区ID',
  `city_id` mediumint(9) DEFAULT NULL COMMENT '市级ID',
  `area_info` varchar(100) DEFAULT NULL COMMENT '省市县',
  `address` varchar(100) NOT NULL COMMENT '地址',
  `telphone` varchar(40) DEFAULT NULL COMMENT '电话',
  `company` varchar(50) DEFAULT '' COMMENT '公司',
  `is_default` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否默认1是',
  PRIMARY KEY (`address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='卖家发货地址信息表';

-- ----------------------------
-- Records of daddress
-- ----------------------------

-- ----------------------------
-- Table structure for delivery_order
-- ----------------------------
DROP TABLE IF EXISTS `delivery_order`;
CREATE TABLE `delivery_order` (
  `order_id` int(11) NOT NULL COMMENT '订单ID',
  `addtime` int(11) DEFAULT '0' COMMENT '订单生成时间',
  `order_sn` bigint(20) DEFAULT NULL COMMENT '订单号',
  `dlyp_id` int(11) DEFAULT NULL COMMENT '自提点ID',
  `shipping_code` varchar(50) DEFAULT NULL COMMENT '物流单号',
  `express_code` varchar(30) DEFAULT NULL COMMENT '快递公司编码',
  `express_name` varchar(30) DEFAULT NULL COMMENT '快递公司名称',
  `reciver_name` varchar(20) DEFAULT NULL COMMENT '收货人',
  `reciver_telphone` varchar(20) DEFAULT NULL COMMENT '电话',
  `reciver_mobphone` varchar(11) DEFAULT NULL COMMENT '手机',
  `dlyo_state` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '订单状态 10(默认)未到站，20已到站，30已提取',
  `dlyo_pickup_code` varchar(6) DEFAULT NULL COMMENT '提货码',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单自提点表';

-- ----------------------------
-- Records of delivery_order
-- ----------------------------

-- ----------------------------
-- Table structure for delivery_point
-- ----------------------------
DROP TABLE IF EXISTS `delivery_point`;
CREATE TABLE `delivery_point` (
  `dlyp_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '提货站id',
  `dlyp_name` varchar(50) NOT NULL COMMENT '提货站登录名',
  `dlyp_passwd` varchar(32) NOT NULL COMMENT '提货站登录密码',
  `dlyp_truename` varchar(20) NOT NULL COMMENT '真实姓名',
  `dlyp_mobile` varchar(11) DEFAULT '' COMMENT '手机号码',
  `dlyp_telephony` varchar(20) DEFAULT '' COMMENT '座机号码',
  `dlyp_address_name` varchar(20) NOT NULL COMMENT '服务站名称',
  `dlyp_area_1` int(10) unsigned NOT NULL COMMENT '一级地区id',
  `dlyp_area_2` int(10) unsigned NOT NULL COMMENT '二级地区id',
  `dlyp_area_3` int(10) unsigned NOT NULL COMMENT '三级地区id',
  `dlyp_area_4` int(10) unsigned NOT NULL COMMENT '四级地区id',
  `dlyp_area` int(10) unsigned NOT NULL COMMENT '地区id',
  `dlyp_area_info` varchar(255) NOT NULL COMMENT '地区内容',
  `dlyp_address` varchar(255) NOT NULL COMMENT '详细地址',
  `dlyp_idcard` varchar(18) NOT NULL COMMENT '身份证号码',
  `dlyp_idcard_image` varchar(255) NOT NULL COMMENT '身份证照片',
  `dlyp_addtime` int(10) unsigned NOT NULL COMMENT '添加时间',
  `dlyp_state` tinyint(3) unsigned NOT NULL COMMENT '提货站状态 0关闭，1开启，10等待审核, 20审核失败',
  `dlyp_fail_reason` varchar(255) DEFAULT NULL COMMENT '失败原因',
  PRIMARY KEY (`dlyp_id`),
  UNIQUE KEY `dlyp_name` (`dlyp_name`),
  UNIQUE KEY `dlyp_idcard` (`dlyp_idcard`),
  UNIQUE KEY `dlyp_mobile` (`dlyp_mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='提货站表';

-- ----------------------------
-- Records of delivery_point
-- ----------------------------

-- ----------------------------
-- Table structure for diamond_jiajialv
-- ----------------------------
DROP TABLE IF EXISTS `diamond_jiajialv`;
CREATE TABLE `diamond_jiajialv` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `channel_id` int(10) unsigned NOT NULL COMMENT '销售渠道',
  `cert` varchar(10) DEFAULT NULL,
  `good_type` tinyint(1) unsigned NOT NULL COMMENT '货品类型1现货2期货',
  `carat_min` decimal(5,2) unsigned NOT NULL COMMENT '最小钻重',
  `carat_max` decimal(5,2) unsigned NOT NULL COMMENT '最大钻重',
  `jiajialv` decimal(4,3) DEFAULT '1.000' COMMENT '加价率',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态：1启用 0停用',
  PRIMARY KEY (`id`),
  KEY `channel` (`channel_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3380 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of diamond_jiajialv
-- ----------------------------

-- ----------------------------
-- Table structure for diamond_jiajialv_default
-- ----------------------------
DROP TABLE IF EXISTS `diamond_jiajialv_default`;
CREATE TABLE `diamond_jiajialv_default` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `channel_id` int(10) unsigned NOT NULL COMMENT '销售渠道',
  `cert` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `good_type` tinyint(1) unsigned NOT NULL COMMENT '货品类型1现货2期货',
  `carat_min` decimal(5,2) unsigned NOT NULL COMMENT '最小钻重',
  `carat_max` decimal(5,2) unsigned NOT NULL COMMENT '最大钻重',
  `jiajialv` decimal(4,3) DEFAULT '1.000' COMMENT '加价率',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态：1启用 0停用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of diamond_jiajialv_default
-- ----------------------------

-- ----------------------------
-- Table structure for diamond_jiajialv_log
-- ----------------------------
DROP TABLE IF EXISTS `diamond_jiajialv_log`;
CREATE TABLE `diamond_jiajialv_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `jiajialv_id` int(10) NOT NULL,
  `remark` varchar(500) DEFAULT NULL,
  `create_user` varchar(30) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='裸钻加价率日志';

-- ----------------------------
-- Records of diamond_jiajialv_log
-- ----------------------------

-- ----------------------------
-- Table structure for diamond_pay_log
-- ----------------------------
DROP TABLE IF EXISTS `diamond_pay_log`;
CREATE TABLE `diamond_pay_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` int(11) DEFAULT NULL COMMENT '购物车ID',
  `cert_id` varchar(30) DEFAULT NULL,
  `goods_price` decimal(10,3) DEFAULT NULL,
  `pifajia` decimal(10,3) DEFAULT NULL,
  `jiajialv` decimal(5,3) DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `store_id` int(11) DEFAULT NULL,
  `store_name` varchar(255) DEFAULT NULL,
  `type` tinyint(255) DEFAULT '1' COMMENT '1:添加购物车，2：下单',
  `order_no` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of diamond_pay_log
-- ----------------------------

-- ----------------------------
-- Table structure for dict
-- ----------------------------
DROP TABLE IF EXISTS `dict`;
CREATE TABLE `dict` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL COMMENT '属性',
  `label` varchar(20) NOT NULL COMMENT '标识',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否内置',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='数据字典';

-- ----------------------------
-- Records of dict
-- ----------------------------

-- ----------------------------
-- Table structure for dict_item
-- ----------------------------
DROP TABLE IF EXISTS `dict_item`;
CREATE TABLE `dict_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '明细主键',
  `dict_id` int(11) NOT NULL COMMENT '字典id',
  `name` tinyint(4) NOT NULL COMMENT '枚举key',
  `label` varchar(20) NOT NULL COMMENT '枚举显示标识',
  `note` varchar(200) DEFAULT NULL COMMENT '描述',
  `display_order` int(11) NOT NULL COMMENT '顺序号',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '系统内置',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
  PRIMARY KEY (`id`),
  KEY `dict_id` (`dict_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='字典明细表';

-- ----------------------------
-- Records of dict_item
-- ----------------------------

-- ----------------------------
-- Table structure for document
-- ----------------------------
DROP TABLE IF EXISTS `document`;
CREATE TABLE `document` (
  `doc_id` mediumint(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `doc_code` varchar(255) NOT NULL COMMENT '调用标识码',
  `doc_title` varchar(255) NOT NULL COMMENT '标题',
  `doc_content` text NOT NULL COMMENT '内容',
  `doc_time` int(10) unsigned NOT NULL COMMENT '添加时间/修改时间',
  PRIMARY KEY (`doc_id`),
  UNIQUE KEY `doc_code` (`doc_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统文章表';

-- ----------------------------
-- Records of document
-- ----------------------------

-- ----------------------------
-- Table structure for erp_bill
-- ----------------------------
DROP TABLE IF EXISTS `erp_bill`;
CREATE TABLE `erp_bill` (
  `bill_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bill_no` varchar(20) NOT NULL,
  `bill_type` varchar(2) NOT NULL COMMENT 'L进货单M调拨单D销售退货单B退货返厂单C其它出库单S销售出库单',
  `bill_status` smallint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1:待审核，2：已审核；3：已取消；4：已签收 5审核确认''',
  `item_id` varchar(255) DEFAULT NULL COMMENT '来源',
  `item_type` varchar(255) DEFAULT NULL COMMENT '来源类型,销售类型（LS 零售, PF批发, WX维修）,调拨类型(ZC转仓,WX维修)，退货类型(LS零售,PF批发,WX维修)',
  `from_company_id` int(11) unsigned DEFAULT NULL,
  `from_store_id` int(11) unsigned DEFAULT NULL COMMENT '所属门店',
  `from_house_id` int(11) unsigned DEFAULT NULL,
  `from_box_id` varchar(20) DEFAULT NULL,
  `to_company_id` int(11) DEFAULT NULL COMMENT 'to 公司',
  `to_store_id` int(11) DEFAULT NULL COMMENT 'to 门店',
  `to_house_id` int(11) DEFAULT NULL COMMENT 'to 仓库',
  `to_box_id` varchar(20) DEFAULT NULL COMMENT 'to 储位',
  `supplier_id` int(11) DEFAULT NULL COMMENT '供应商',
  `wholesale_id` int(11) DEFAULT NULL COMMENT '批发客户',
  `chengben_total` decimal(10,3) DEFAULT NULL COMMENT '总成本',
  `goods_total` decimal(10,3) DEFAULT NULL COMMENT '总金额',
  `remark` text COMMENT '备注',
  `goods_num` int(11) DEFAULT NULL COMMENT '货品总数',
  `create_user` varchar(30) DEFAULT NULL COMMENT '创建人',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `check_user` varchar(30) DEFAULT NULL COMMENT '审核人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `sign_time` datetime DEFAULT NULL COMMENT '签收时间',
  `sign_user` varchar(30) DEFAULT NULL COMMENT '签收人',
  `pifa_type` tinyint(1) DEFAULT NULL COMMENT '批发类型－类别',
  `express_id` int(6) DEFAULT NULL COMMENT '快递公司',
  `express_sn` varchar(64) DEFAULT NULL COMMENT '快递单号',
  `order_sn` varchar(50) DEFAULT NULL COMMENT '订单号',
  `is_settled` tinyint(1) DEFAULT '0' COMMENT '门店结算类型0未结算1已结算',
  `settle_time` datetime DEFAULT NULL COMMENT '结算时间',
  `settle_user` varchar(50) DEFAULT NULL COMMENT '结算人',
  `in_warehouse_type` tinyint(4) DEFAULT NULL COMMENT '入库类型',
  `out_warehouse_type` tinyint(4) DEFAULT NULL COMMENT '出库类型\\批发类型',
  `item_status` tinyint(4) DEFAULT NULL COMMENT '单据状态明细',
  PRIMARY KEY (`bill_id`),
  KEY `bill_no` (`bill_no`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of erp_bill
-- ----------------------------

-- ----------------------------
-- Table structure for erp_bill_goods
-- ----------------------------
DROP TABLE IF EXISTS `erp_bill_goods`;
CREATE TABLE `erp_bill_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_itemid` bigint(20) DEFAULT NULL COMMENT '货号',
  `goods_sn` varchar(25) NOT NULL COMMENT '款号',
  `goods_name` varchar(128) NOT NULL COMMENT '名称',
  `bill_id` int(11) NOT NULL,
  `bill_no` char(20) NOT NULL,
  `bill_type` char(2) NOT NULL,
  `goods_count` int(10) NOT NULL DEFAULT '1' COMMENT '数量',
  `yuanshichengben` decimal(10,3) DEFAULT NULL COMMENT '原始成本',
  `mingyichengben` decimal(10,3) DEFAULT NULL,
  `jijiachengben` decimal(10,3) DEFAULT NULL,
  `sale_price` decimal(10,3) DEFAULT NULL COMMENT '售价/D单退货价',
  `management_fee` decimal(10,3) DEFAULT NULL,
  `remark` text,
  `from_company_id` int(11) unsigned DEFAULT NULL,
  `from_store_id` int(11) unsigned DEFAULT NULL,
  `from_house_id` int(11) unsigned DEFAULT NULL,
  `from_box_id` varchar(20) DEFAULT NULL COMMENT '柜位编号',
  `to_company_id` int(11) DEFAULT NULL COMMENT 'to 公司',
  `to_store_id` int(11) DEFAULT NULL COMMENT 'to 门店',
  `to_house_id` int(11) DEFAULT NULL COMMENT 'to 仓库',
  `to_box_id` varchar(20) DEFAULT NULL COMMENT 'to 储位编号',
  `in_warehouse_type` tinyint(2) DEFAULT '0' COMMENT '入库方式 0、默认无。1.购买。2、委托加工。3、代销。4、借入',
  `is_settled` tinyint(2) DEFAULT '0' COMMENT '是否结算(0未结算1已结算2已退货)',
  `settle_user` varchar(16) DEFAULT NULL COMMENT '结算操作人',
  `settle_time` datetime DEFAULT NULL COMMENT '结算时间',
  `order_detail_id` int(8) unsigned DEFAULT NULL COMMENT '订单明细ID',
  `goods_data` text COMMENT '货品信息',
  `old_detail_id` int(10) DEFAULT NULL,
  `pandian_status` tinyint(1) DEFAULT NULL COMMENT '盘点状态 1盘亏（未盘点） 2盘盈 3正常 ',
  `pandian_adjust` tinyint(1) DEFAULT NULL COMMENT '盘点调整状态 0无  1在途 2已销售',
  `pandian_user` varchar(30) DEFAULT NULL COMMENT '盘点人',
  `pandian_time` datetime DEFAULT NULL COMMENT '盘点时间',
  PRIMARY KEY (`id`),
  KEY `goods_sn` (`goods_sn`) USING BTREE,
  KEY `goods_itemid` (`goods_itemid`) USING BTREE,
  KEY `old_detail_id` (`old_detail_id`) USING BTREE,
  KEY `bill_id` (`bill_id`) USING BTREE,
  KEY `bill_no` (`bill_no`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of erp_bill_goods
-- ----------------------------

-- ----------------------------
-- Table structure for erp_bill_log
-- ----------------------------
DROP TABLE IF EXISTS `erp_bill_log`;
CREATE TABLE `erp_bill_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `bill_id` int(11) DEFAULT NULL,
  `bill_status` tinyint(1) DEFAULT NULL,
  `remark` varchar(500) DEFAULT NULL,
  `create_user` varchar(30) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bill_id` (`bill_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of erp_bill_log
-- ----------------------------

-- ----------------------------
-- Table structure for erp_bill_sync
-- ----------------------------
DROP TABLE IF EXISTS `erp_bill_sync`;
CREATE TABLE `erp_bill_sync` (
  `sync_id` int(11) NOT NULL AUTO_INCREMENT,
  `bill_id` int(11) NOT NULL,
  `bill_no` varchar(20) NOT NULL,
  `latest_push_time` datetime DEFAULT NULL,
  `latest_pull_time` datetime DEFAULT NULL,
  PRIMARY KEY (`sync_id`),
  UNIQUE KEY `id_no` (`bill_id`,`bill_no`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of erp_bill_sync
-- ----------------------------

-- ----------------------------
-- Table structure for erp_bill_w
-- ----------------------------
DROP TABLE IF EXISTS `erp_bill_w`;
CREATE TABLE `erp_bill_w` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `bill_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '单据ID',
  `warehouse_id` int(10) unsigned DEFAULT NULL COMMENT '盘点仓库ID',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '盘点状态 : 0盘点中 1盘点完成',
  PRIMARY KEY (`id`),
  KEY `bill_id` (`bill_id`,`warehouse_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='盘点附属表';

-- ----------------------------
-- Records of erp_bill_w
-- ----------------------------

-- ----------------------------
-- Table structure for erp_box
-- ----------------------------
DROP TABLE IF EXISTS `erp_box`;
CREATE TABLE `erp_box` (
  `box_id` int(11) NOT NULL AUTO_INCREMENT,
  `box_name` varchar(255) NOT NULL,
  `house_id` int(11) NOT NULL,
  `is_enabled` smallint(1) DEFAULT '1' COMMENT '是否有效',
  `note` text,
  `is_lock` smallint(1) unsigned DEFAULT '0' COMMENT '是否锁定',
  PRIMARY KEY (`box_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of erp_box
-- ----------------------------

-- ----------------------------
-- Table structure for erp_ishop_order_map
-- ----------------------------
DROP TABLE IF EXISTS `erp_ishop_order_map`;
CREATE TABLE `erp_ishop_order_map` (
  `rec_id` int(11) NOT NULL DEFAULT '0' COMMENT '订单商品表索引id',
  `old_detail_id` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of erp_ishop_order_map
-- ----------------------------

-- ----------------------------
-- Table structure for erp_warehouse
-- ----------------------------
DROP TABLE IF EXISTS `erp_warehouse`;
CREATE TABLE `erp_warehouse` (
  `house_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '仓库名称',
  `code` varchar(20) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` datetime DEFAULT NULL,
  `create_user` varchar(20) DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否有效；1为有效，0为无效',
  `lock` tinyint(1) NOT NULL DEFAULT '0' COMMENT '锁定状态 0未锁定/1锁定',
  `type` int(2) NOT NULL DEFAULT '0' COMMENT '仓库类型',
  `diamond_warehouse` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否裸钻库 0否,1是',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '默认上架 0 否,1 是',
  `company_id` int(10) unsigned DEFAULT NULL COMMENT '公司ID',
  `company_name` varchar(100) DEFAULT NULL,
  `store_id` int(10) DEFAULT NULL COMMENT '门店ID',
  `store_name` varchar(100) DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否系统内置仓库',
  PRIMARY KEY (`house_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='仓库表';

-- ----------------------------
-- Records of erp_warehouse
-- ----------------------------

-- ----------------------------
-- Table structure for erp_warehouse_raw
-- ----------------------------
DROP TABLE IF EXISTS `erp_warehouse_raw`;
CREATE TABLE `erp_warehouse_raw` (
  `house_id` int(4) unsigned NOT NULL DEFAULT '0',
  `name` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT '仓库名称',
  `code` varchar(20) CHARACTER SET utf8 NOT NULL,
  `remark` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '备注',
  `create_time` datetime DEFAULT NULL,
  `create_user` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否有效；1为有效，0为无效',
  `lock` tinyint(1) NOT NULL DEFAULT '0' COMMENT '锁定状态 0未锁定/1锁定',
  `type` int(2) NOT NULL DEFAULT '0' COMMENT '仓库类型',
  `diamond_warehouse` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否裸钻库 0否,1是',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '默认上架 0 否,1 是',
  `company_id` int(10) unsigned DEFAULT NULL COMMENT '公司ID',
  `company_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `store_id` int(10) DEFAULT NULL COMMENT '门店ID',
  `store_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否系统内置仓库'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of erp_warehouse_raw
-- ----------------------------

-- ----------------------------
-- Table structure for evaluate_goods
-- ----------------------------
DROP TABLE IF EXISTS `evaluate_goods`;
CREATE TABLE `evaluate_goods` (
  `geval_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '评价ID',
  `geval_orderid` int(11) NOT NULL COMMENT '订单表自增ID',
  `geval_orderno` bigint(20) unsigned NOT NULL COMMENT '订单编号',
  `geval_ordergoodsid` int(11) NOT NULL COMMENT '订单商品表编号',
  `geval_goodsid` int(11) NOT NULL COMMENT '商品表编号',
  `geval_goodsname` varchar(100) NOT NULL COMMENT '商品名称',
  `geval_goodsprice` decimal(10,2) DEFAULT NULL COMMENT '商品价格',
  `geval_goodsimage` varchar(255) DEFAULT NULL COMMENT '商品图片',
  `geval_scores` tinyint(1) NOT NULL COMMENT '1-5分',
  `geval_content` varchar(255) DEFAULT NULL COMMENT '信誉评价内容',
  `geval_isanonymous` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0表示不是 1表示是匿名评价',
  `geval_addtime` int(11) NOT NULL COMMENT '评价时间',
  `geval_storeid` int(11) NOT NULL COMMENT '店铺编号',
  `geval_storename` varchar(100) NOT NULL COMMENT '店铺名称',
  `geval_frommemberid` int(11) NOT NULL COMMENT '评价人编号',
  `geval_frommembername` varchar(100) NOT NULL COMMENT '评价人名称',
  `geval_explain` varchar(255) DEFAULT NULL COMMENT '解释内容',
  `geval_image` varchar(255) DEFAULT NULL COMMENT '晒单图片',
  `geval_content_again` varchar(255) NOT NULL COMMENT '追加评价内容',
  `geval_addtime_again` int(10) unsigned NOT NULL COMMENT '追加评价时间',
  `geval_image_again` varchar(255) NOT NULL COMMENT '追加评价图片',
  `geval_explain_again` varchar(255) NOT NULL COMMENT '追加解释内容',
  PRIMARY KEY (`geval_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='信誉评价表';

-- ----------------------------
-- Records of evaluate_goods
-- ----------------------------

-- ----------------------------
-- Table structure for evaluate_store
-- ----------------------------
DROP TABLE IF EXISTS `evaluate_store`;
CREATE TABLE `evaluate_store` (
  `seval_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '评价ID',
  `seval_orderid` int(11) unsigned NOT NULL COMMENT '订单ID',
  `seval_orderno` varchar(100) NOT NULL COMMENT '订单编号',
  `seval_addtime` int(11) unsigned NOT NULL COMMENT '评价时间',
  `seval_storeid` int(11) unsigned NOT NULL COMMENT '店铺编号',
  `seval_storename` varchar(100) NOT NULL COMMENT '店铺名称',
  `seval_memberid` int(11) unsigned NOT NULL COMMENT '买家编号',
  `seval_membername` varchar(100) NOT NULL COMMENT '买家名称',
  `seval_desccredit` tinyint(1) unsigned NOT NULL DEFAULT '5' COMMENT '描述相符评分',
  `seval_servicecredit` tinyint(1) unsigned NOT NULL DEFAULT '5' COMMENT '服务态度评分',
  `seval_deliverycredit` tinyint(1) unsigned NOT NULL DEFAULT '5' COMMENT '发货速度评分',
  PRIMARY KEY (`seval_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺评分表';

-- ----------------------------
-- Records of evaluate_store
-- ----------------------------

-- ----------------------------
-- Table structure for exppoints_log
-- ----------------------------
DROP TABLE IF EXISTS `exppoints_log`;
CREATE TABLE `exppoints_log` (
  `exp_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '经验值日志编号',
  `exp_memberid` int(11) NOT NULL COMMENT '会员编号',
  `exp_membername` varchar(100) NOT NULL COMMENT '会员名称',
  `exp_points` int(11) NOT NULL DEFAULT '0' COMMENT '经验值负数表示扣除',
  `exp_addtime` int(11) NOT NULL COMMENT '添加时间',
  `exp_desc` varchar(100) NOT NULL COMMENT '操作描述',
  `exp_stage` varchar(50) NOT NULL COMMENT '操作阶段',
  PRIMARY KEY (`exp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='经验值日志表';

-- ----------------------------
-- Records of exppoints_log
-- ----------------------------

-- ----------------------------
-- Table structure for express
-- ----------------------------
DROP TABLE IF EXISTS `express`;
CREATE TABLE `express` (
  `id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引ID',
  `e_name` varchar(50) NOT NULL COMMENT '公司名称',
  `e_state` enum('0','1') NOT NULL DEFAULT '1' COMMENT '状态',
  `e_code` varchar(50) NOT NULL COMMENT '编号',
  `e_letter` char(1) NOT NULL COMMENT '首字母',
  `e_order` enum('1','2') NOT NULL DEFAULT '2' COMMENT '1常用2不常用',
  `e_url` varchar(100) NOT NULL COMMENT '公司网址',
  `e_zt_state` tinyint(4) DEFAULT '0' COMMENT '是否支持服务站配送0否1是',
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='快递公司';

-- ----------------------------
-- Records of express
-- ----------------------------

-- ----------------------------
-- Table structure for favorites
-- ----------------------------
DROP TABLE IF EXISTS `favorites`;
CREATE TABLE `favorites` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `member_id` int(10) unsigned NOT NULL COMMENT '会员ID',
  `member_name` varchar(50) NOT NULL COMMENT '会员名',
  `fav_id` int(10) unsigned NOT NULL COMMENT '商品或店铺ID',
  `fav_type` char(5) NOT NULL DEFAULT 'goods' COMMENT '类型:goods为商品,store为店铺,默认为商品',
  `fav_time` int(10) unsigned NOT NULL COMMENT '收藏时间',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺ID',
  `store_name` varchar(20) NOT NULL COMMENT '店铺名称',
  `sc_id` int(10) unsigned DEFAULT '0' COMMENT '店铺分类ID',
  `goods_name` varchar(50) DEFAULT NULL COMMENT '商品名称',
  `goods_image` varchar(100) DEFAULT NULL COMMENT '商品图片',
  `gc_id` int(10) unsigned DEFAULT '0' COMMENT '商品分类ID',
  `log_price` decimal(10,2) DEFAULT '0.00' COMMENT '商品收藏时价格',
  `log_msg` varchar(20) DEFAULT NULL COMMENT '收藏备注',
  PRIMARY KEY (`log_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收藏表';

-- ----------------------------
-- Records of favorites
-- ----------------------------

-- ----------------------------
-- Table structure for fff
-- ----------------------------
DROP TABLE IF EXISTS `fff`;
CREATE TABLE `fff` (
  `goods_id` bigint(30) NOT NULL DEFAULT '0' COMMENT '货号',
  `bill_id` int(11) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of fff
-- ----------------------------

-- ----------------------------
-- Table structure for flea
-- ----------------------------
DROP TABLE IF EXISTS `flea`;
CREATE TABLE `flea` (
  `goods_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '闲置索引id',
  `goods_name` varchar(100) NOT NULL COMMENT '闲置名称',
  `gc_id` int(11) NOT NULL COMMENT '闲置分类id',
  `gc_name` varchar(200) NOT NULL COMMENT '闲置分类名称',
  `member_id` int(11) NOT NULL COMMENT '店铺id',
  `member_name` varchar(110) NOT NULL COMMENT '会员名称',
  `goods_image` varchar(100) NOT NULL COMMENT '闲置默认封面图片',
  `goods_tag` varchar(100) NOT NULL COMMENT '闲置标签',
  `goods_price` decimal(10,2) NOT NULL COMMENT '闲置原价',
  `goods_store_price` decimal(10,2) NOT NULL COMMENT '闲置转让价格',
  `goods_show` tinyint(1) NOT NULL COMMENT '闲置上架',
  `goods_click` int(11) NOT NULL DEFAULT '0' COMMENT '闲置浏览数',
  `flea_collect_num` int(11) unsigned NOT NULL COMMENT '闲置物品总收藏次数',
  `goods_commend` tinyint(1) NOT NULL COMMENT '闲置推荐',
  `goods_add_time` varchar(10) NOT NULL COMMENT '闲置添加时间',
  `goods_keywords` varchar(255) NOT NULL COMMENT '闲置关键字',
  `goods_description` varchar(255) NOT NULL COMMENT '闲置描述',
  `goods_body` text NOT NULL COMMENT '商品详细内容',
  `commentnum` int(11) NOT NULL DEFAULT '0' COMMENT '评论次数',
  `salenum` int(11) NOT NULL DEFAULT '0' COMMENT '售出数量',
  `flea_quality` tinyint(4) NOT NULL DEFAULT '0' COMMENT '闲置物品成色，0未选择，9-5九五成新，3是低于五成新',
  `flea_pname` varchar(20) DEFAULT NULL COMMENT '闲置商品联系人',
  `flea_pphone` varchar(20) DEFAULT NULL COMMENT '闲置商品联系人电话',
  `flea_area_id` int(11) unsigned NOT NULL COMMENT '闲置物品地区id',
  `flea_area_name` varchar(50) NOT NULL COMMENT '闲置物品地区名称',
  PRIMARY KEY (`goods_id`),
  KEY `goods_name` (`goods_name`,`gc_id`,`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='闲置商品';

-- ----------------------------
-- Records of flea
-- ----------------------------

-- ----------------------------
-- Table structure for flea_area
-- ----------------------------
DROP TABLE IF EXISTS `flea_area`;
CREATE TABLE `flea_area` (
  `flea_area_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '闲置地区id',
  `flea_area_name` varchar(50) NOT NULL COMMENT '闲置地区名称',
  `flea_area_parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '闲置地区上级地区id',
  `flea_area_sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '闲置地区排序',
  `flea_area_deep` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '闲置地区层级',
  `flea_area_hot` int(11) NOT NULL DEFAULT '0' COMMENT '地区检索热度',
  PRIMARY KEY (`flea_area_id`)
) ENGINE=MyISAM AUTO_INCREMENT=45056 DEFAULT CHARSET=utf8 COMMENT='闲置地区';

-- ----------------------------
-- Records of flea_area
-- ----------------------------

-- ----------------------------
-- Table structure for flea_class
-- ----------------------------
DROP TABLE IF EXISTS `flea_class`;
CREATE TABLE `flea_class` (
  `gc_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引ID',
  `gc_name` varchar(100) NOT NULL COMMENT '分类名称',
  `gc_name_index` varchar(100) NOT NULL COMMENT '闲置首页显示的名称',
  `gc_parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `gc_sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `gc_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '前台显示，0为否，1为是，默认为1',
  `gc_index_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '首页显示 1：默认 显示 0：不显示',
  PRIMARY KEY (`gc_id`)
) ENGINE=MyISAM AUTO_INCREMENT=108 DEFAULT CHARSET=utf8 COMMENT='闲置分类';

-- ----------------------------
-- Records of flea_class
-- ----------------------------

-- ----------------------------
-- Table structure for flea_class_index
-- ----------------------------
DROP TABLE IF EXISTS `flea_class_index`;
CREATE TABLE `flea_class_index` (
  `fc_index_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `fc_index_class` varchar(50) NOT NULL COMMENT '类别名称',
  `fc_index_code` varchar(50) NOT NULL COMMENT '类别code',
  `fc_index_id1` varchar(50) NOT NULL DEFAULT '0' COMMENT '分类id1',
  `fc_index_name1` varchar(50) NOT NULL,
  `fc_index_id2` varchar(50) NOT NULL DEFAULT '0' COMMENT '分类id2',
  `fc_index_name2` varchar(50) NOT NULL,
  `fc_index_id3` varchar(50) NOT NULL DEFAULT '0' COMMENT '分类id3',
  `fc_index_name3` varchar(50) NOT NULL,
  `fc_index_id4` varchar(50) NOT NULL DEFAULT '0' COMMENT '分类id4',
  `fc_index_name4` varchar(50) NOT NULL,
  PRIMARY KEY (`fc_index_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='闲置首页分类';

-- ----------------------------
-- Records of flea_class_index
-- ----------------------------

-- ----------------------------
-- Table structure for flea_consult
-- ----------------------------
DROP TABLE IF EXISTS `flea_consult`;
CREATE TABLE `flea_consult` (
  `consult_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '咨询编号',
  `goods_id` int(11) DEFAULT '0' COMMENT '商品编号',
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '咨询发布者会员编号(0：游客)',
  `seller_id` int(11) NOT NULL COMMENT '信息发布者编号',
  `email` varchar(255) DEFAULT NULL COMMENT '咨询发布者邮箱',
  `consult_content` varchar(4000) DEFAULT NULL COMMENT '咨询内容',
  `consult_addtime` int(10) DEFAULT NULL COMMENT '咨询发布时间',
  `consult_reply` varchar(4000) DEFAULT NULL COMMENT '咨询回复内容',
  `consult_reply_time` int(10) DEFAULT NULL COMMENT '咨询回复时间',
  `type` varchar(20) NOT NULL DEFAULT 'flea' COMMENT '咨询类型',
  PRIMARY KEY (`consult_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='闲置咨询';

-- ----------------------------
-- Records of flea_consult
-- ----------------------------

-- ----------------------------
-- Table structure for flea_favorites
-- ----------------------------
DROP TABLE IF EXISTS `flea_favorites`;
CREATE TABLE `flea_favorites` (
  `member_id` int(10) unsigned NOT NULL COMMENT '会员ID',
  `fav_id` int(10) unsigned NOT NULL COMMENT '收藏ID',
  `fav_type` varchar(20) NOT NULL COMMENT '收藏类型',
  `fav_time` varchar(10) NOT NULL COMMENT '收藏时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='买家闲置收藏表';

-- ----------------------------
-- Records of flea_favorites
-- ----------------------------

-- ----------------------------
-- Table structure for flea_upload
-- ----------------------------
DROP TABLE IF EXISTS `flea_upload`;
CREATE TABLE `flea_upload` (
  `upload_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引ID',
  `file_name` varchar(100) DEFAULT NULL COMMENT '文件名',
  `file_thumb` varchar(100) DEFAULT NULL COMMENT '缩微图片',
  `file_wm` varchar(100) DEFAULT NULL COMMENT '水印图片',
  `file_size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `store_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `upload_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '默认为0，12为商品切换图片，13为商品内容图片',
  `upload_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `item_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '信息ID',
  PRIMARY KEY (`upload_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='闲置上传文件表';

-- ----------------------------
-- Records of flea_upload
-- ----------------------------

-- ----------------------------
-- Table structure for flowstat
-- ----------------------------
DROP TABLE IF EXISTS `flowstat`;
CREATE TABLE `flowstat` (
  `stattime` int(11) unsigned NOT NULL COMMENT '访问日期',
  `clicknum` int(11) unsigned NOT NULL COMMENT '访问量',
  `store_id` int(11) unsigned NOT NULL COMMENT '店铺ID',
  `type` varchar(10) NOT NULL COMMENT '类型',
  `goods_id` int(11) unsigned NOT NULL COMMENT '商品ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='访问量统计表';

-- ----------------------------
-- Records of flowstat
-- ----------------------------

-- ----------------------------
-- Table structure for flowstat_1
-- ----------------------------
DROP TABLE IF EXISTS `flowstat_1`;
CREATE TABLE `flowstat_1` (
  `stattime` int(11) unsigned NOT NULL COMMENT '访问日期',
  `clicknum` int(11) unsigned NOT NULL COMMENT '访问量',
  `store_id` int(11) unsigned NOT NULL COMMENT '店铺ID',
  `type` varchar(10) NOT NULL COMMENT '类型',
  `goods_id` int(11) unsigned NOT NULL COMMENT '商品ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='访问量统计表';

-- ----------------------------
-- Records of flowstat_1
-- ----------------------------

-- ----------------------------
-- Table structure for flowstat_2
-- ----------------------------
DROP TABLE IF EXISTS `flowstat_2`;
CREATE TABLE `flowstat_2` (
  `stattime` int(11) unsigned NOT NULL COMMENT '访问日期',
  `clicknum` int(11) unsigned NOT NULL COMMENT '访问量',
  `store_id` int(11) unsigned NOT NULL COMMENT '店铺ID',
  `type` varchar(10) NOT NULL COMMENT '类型',
  `goods_id` int(11) unsigned NOT NULL COMMENT '商品ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='访问量统计表';

-- ----------------------------
-- Records of flowstat_2
-- ----------------------------

-- ----------------------------
-- Table structure for fx_agent
-- ----------------------------
DROP TABLE IF EXISTS `fx_agent`;
CREATE TABLE `fx_agent` (
  `agent_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `agent_no` char(11) NOT NULL,
  `agent_name` varchar(20) DEFAULT NULL,
  `agent_password` varchar(64) DEFAULT NULL,
  `is_pass` tinyint(1) DEFAULT '0',
  `mobile` char(12) DEFAULT NULL,
  `level_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `consigner` varchar(50) DEFAULT NULL,
  `shop_address` text,
  `shop_img` varchar(255) DEFAULT NULL,
  `balance` decimal(10,2) DEFAULT '0.00',
  `total_sale_money` decimal(10,2) DEFAULT '0.00',
  `total_buy_goods` int(11) DEFAULT '0',
  PRIMARY KEY (`agent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fx_agent
-- ----------------------------

-- ----------------------------
-- Table structure for fx_agent_level
-- ----------------------------
DROP TABLE IF EXISTS `fx_agent_level`;
CREATE TABLE `fx_agent_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level_name` varchar(255) DEFAULT NULL,
  `join_fee` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fx_agent_level
-- ----------------------------

-- ----------------------------
-- Table structure for fx_bankaccount
-- ----------------------------
DROP TABLE IF EXISTS `fx_bankaccount`;
CREATE TABLE `fx_bankaccount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) DEFAULT NULL,
  `agent_name` varchar(15) DEFAULT NULL,
  `is_default` tinyint(2) DEFAULT '0',
  `bank_name` varchar(50) DEFAULT NULL,
  `bank_child` varchar(50) DEFAULT NULL,
  `account_num` varchar(50) DEFAULT NULL,
  `mobile` char(12) DEFAULT NULL,
  `realname` varchar(15) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fx_bankaccount
-- ----------------------------

-- ----------------------------
-- Table structure for fx_cart
-- ----------------------------
DROP TABLE IF EXISTS `fx_cart`;
CREATE TABLE `fx_cart` (
  `cart_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '购物车id',
  `agent_id` int(11) DEFAULT NULL,
  `buyer_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '买家id',
  `store_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '店铺id',
  `store_name` varchar(50) NOT NULL DEFAULT '' COMMENT '店铺名称',
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `goods_name` varchar(100) NOT NULL COMMENT '商品名称',
  `goods_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品价格',
  `goods_num` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '购买商品数量',
  `goods_image` varchar(100) NOT NULL COMMENT '商品图片',
  `bl_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '组合套装ID',
  PRIMARY KEY (`cart_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fx_cart
-- ----------------------------

-- ----------------------------
-- Table structure for fx_commission
-- ----------------------------
DROP TABLE IF EXISTS `fx_commission`;
CREATE TABLE `fx_commission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) DEFAULT NULL,
  `agent_name` varchar(50) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `order_sn` char(20) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL COMMENT 'today,yestoday,week,month,total',
  `type` int(2) DEFAULT '0' COMMENT '0:total,1:today,2:yestoday,3:week,4:month',
  `type_name` varchar(50) DEFAULT NULL,
  `money` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fx_commission
-- ----------------------------

-- ----------------------------
-- Table structure for fx_goods
-- ----------------------------
DROP TABLE IF EXISTS `fx_goods`;
CREATE TABLE `fx_goods` (
  `fx_goods_id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  `goods_name` varchar(200) DEFAULT NULL,
  `goods_storage` int(11) DEFAULT NULL,
  `goods_serial` varchar(20) DEFAULT NULL,
  `goods_price` decimal(10,2) DEFAULT NULL,
  `goods_cost_price` decimal(10,2) DEFAULT NULL,
  `items_storage` int(11) DEFAULT '0',
  PRIMARY KEY (`fx_goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fx_goods
-- ----------------------------

-- ----------------------------
-- Table structure for fx_orders
-- ----------------------------
DROP TABLE IF EXISTS `fx_orders`;
CREATE TABLE `fx_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_sn` char(20) DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  `buyer_id` int(11) DEFAULT NULL COMMENT 'member_id',
  `order_amount` decimal(10,2) DEFAULT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `agent_name` varchar(50) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `finished_time` int(11) DEFAULT NULL,
  `goods_amount` decimal(10,2) DEFAULT NULL,
  `pay_sn` varchar(50) DEFAULT NULL,
  `refund_amount` decimal(10,2) DEFAULT NULL,
  `commission` decimal(10,2) DEFAULT NULL,
  `trade_no` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fx_orders
-- ----------------------------

-- ----------------------------
-- Table structure for fx_order_goods
-- ----------------------------
DROP TABLE IF EXISTS `fx_order_goods`;
CREATE TABLE `fx_order_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  `goods_name` varchar(200) DEFAULT NULL,
  `goods_price` decimal(10,2) DEFAULT NULL,
  `goods_num` int(11) DEFAULT NULL,
  `goods_image` varchar(200) DEFAULT NULL,
  `goods_pay_price` decimal(10,2) DEFAULT NULL,
  `gc_id` int(11) DEFAULT NULL,
  `goods_spec` text,
  `solo_commission` decimal(10,2) DEFAULT NULL,
  `mmission` decimal(10,2) DEFAULT NULL,
  `goods_cost_price` decimal(10,2) DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fx_order_goods
-- ----------------------------

-- ----------------------------
-- Table structure for fx_purchase_orders
-- ----------------------------
DROP TABLE IF EXISTS `fx_purchase_orders`;
CREATE TABLE `fx_purchase_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) DEFAULT NULL,
  `goods_name` varchar(50) DEFAULT NULL,
  `goods_price` decimal(10,2) DEFAULT '0.00',
  `goods_cost_price` decimal(10,2) DEFAULT '0.00',
  `goods_num` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `deal_time` int(11) DEFAULT '0',
  `status_id` tinyint(2) DEFAULT '0',
  `agent_id` int(11) DEFAULT NULL,
  `agent_name` varchar(50) DEFAULT NULL,
  `order_sn` char(20) DEFAULT NULL,
  `order_money` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fx_purchase_orders
-- ----------------------------

-- ----------------------------
-- Table structure for fx_withdraw
-- ----------------------------
DROP TABLE IF EXISTS `fx_withdraw`;
CREATE TABLE `fx_withdraw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) DEFAULT NULL,
  `agent_name` varchar(50) DEFAULT NULL,
  `mobile` char(12) DEFAULT NULL,
  `realname` varchar(50) DEFAULT NULL,
  `money` decimal(10,2) DEFAULT NULL,
  `bank_name` varchar(50) DEFAULT NULL,
  `bank_child` varchar(50) DEFAULT NULL,
  `account_num` varchar(50) DEFAULT NULL,
  `status_id` tinyint(2) DEFAULT NULL,
  `status_name` varchar(15) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `deal_timein` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fx_withdraw
-- ----------------------------

-- ----------------------------
-- Table structure for gadmin
-- ----------------------------
DROP TABLE IF EXISTS `gadmin`;
CREATE TABLE `gadmin` (
  `gid` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `gname` varchar(50) DEFAULT NULL COMMENT '组名',
  `limits` text COMMENT '权限内容',
  PRIMARY KEY (`gid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限组';

-- ----------------------------
-- Records of gadmin
-- ----------------------------

-- ----------------------------
-- Table structure for gift_goods
-- ----------------------------
DROP TABLE IF EXISTS `gift_goods`;
CREATE TABLE `gift_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL COMMENT '名称',
  `num` int(11) NOT NULL COMMENT '数量',
  `min_num` int(10) NOT NULL DEFAULT '0' COMMENT '最低数量',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '均价',
  `sell_sprice` decimal(10,2) NOT NULL COMMENT '售价',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态-1删除1正常',
  `goods_number` varchar(30) DEFAULT NULL COMMENT '赠品货号',
  `sell_type` int(4) NOT NULL DEFAULT '1' COMMENT '店面销售 1=开启  2=关闭',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_randring` tinyint(3) NOT NULL DEFAULT '2' COMMENT '是否活圈 1是2否',
  `sale_way` char(2) NOT NULL DEFAULT '1' COMMENT '可销售渠道. 1线上，2线下',
  `is_xz` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否销账,2.是.1否',
  `is_zp` char(2) NOT NULL DEFAULT '1' COMMENT '是否显示在外部订单1.是.0.否',
  PRIMARY KEY (`id`),
  KEY `goods_number` (`goods_number`)
) ENGINE=MyISAM AUTO_INCREMENT=471 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gift_goods
-- ----------------------------

-- ----------------------------
-- Table structure for goods
-- ----------------------------
DROP TABLE IF EXISTS `goods`;
CREATE TABLE `goods` (
  `goods_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品id(SKU)',
  `goods_commonid` int(10) unsigned NOT NULL COMMENT '商品公共表id',
  `goods_name` varchar(50) NOT NULL COMMENT '商品名称（+规格名称）',
  `goods_jingle` varchar(150) DEFAULT '' COMMENT '商品广告词',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `store_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `gc_id` int(10) unsigned NOT NULL COMMENT '商品分类id',
  `gc_id_1` int(10) unsigned NOT NULL COMMENT '一级分类id',
  `gc_id_2` int(10) unsigned NOT NULL COMMENT '二级分类id',
  `gc_id_3` int(10) unsigned NOT NULL COMMENT '三级分类id',
  `brand_id` int(10) unsigned DEFAULT '0' COMMENT '品牌id',
  `goods_price` decimal(10,2) NOT NULL COMMENT '商品价格',
  `goods_promotion_price` decimal(10,2) NOT NULL COMMENT '商品促销价格',
  `goods_promotion_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '促销类型 0无促销，1抢购，2限时折扣',
  `goods_marketprice` decimal(10,2) NOT NULL COMMENT '市场价',
  `goods_serial` varchar(50) DEFAULT '' COMMENT '款号',
  `goods_storage_alarm` tinyint(3) unsigned NOT NULL COMMENT '库存报警值',
  `goods_barcode` varchar(20) DEFAULT '' COMMENT '商品条形码',
  `goods_click` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品点击数量',
  `goods_salenum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '销售数量',
  `goods_collect` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收藏数量',
  `spec_name` varchar(255) NOT NULL COMMENT '规格名称',
  `goods_spec` text NOT NULL COMMENT '商品规格序列化',
  `goods_storage` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品库存',
  `goods_image` varchar(100) NOT NULL DEFAULT '' COMMENT '商品主图',
  `goods_body` text NOT NULL COMMENT '商品描述',
  `mobile_body` text NOT NULL COMMENT '手机端商品描述',
  `goods_state` tinyint(3) unsigned NOT NULL COMMENT '商品状态 0下架，1正常，10违规（禁售）',
  `goods_verify` tinyint(3) unsigned NOT NULL COMMENT '商品审核 1通过，0未通过，10审核中',
  `goods_addtime` int(10) unsigned NOT NULL COMMENT '商品添加时间',
  `goods_edittime` int(10) unsigned NOT NULL COMMENT '商品编辑时间',
  `areaid_1` int(10) unsigned NOT NULL COMMENT '一级地区id',
  `areaid_2` int(10) unsigned NOT NULL COMMENT '二级地区id',
  `color_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '颜色规格id',
  `transport_id` mediumint(8) unsigned NOT NULL COMMENT '运费模板id',
  `goods_freight` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '运费 0为免运费',
  `goods_vat` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否开具增值税发票 1是，0否',
  `goods_commend` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '商品推荐 1是，0否 默认0',
  `goods_stcids` varchar(255) DEFAULT '' COMMENT '店铺分类id 首尾用,隔开',
  `evaluation_good_star` tinyint(3) unsigned NOT NULL DEFAULT '5' COMMENT '好评星级',
  `evaluation_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评价数',
  `is_virtual` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为虚拟商品 1是，0否',
  `virtual_indate` int(10) unsigned NOT NULL COMMENT '虚拟商品有效期',
  `virtual_limit` tinyint(3) unsigned NOT NULL COMMENT '虚拟商品购买上限',
  `virtual_invalid_refund` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否允许过期退款， 1是，0否',
  `is_fcode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否为F码商品 1是，0否',
  `is_presell` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否是预售商品 1是，0否',
  `presell_deliverdate` int(11) NOT NULL DEFAULT '0' COMMENT '预售商品发货时间',
  `is_book` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否为预定商品，1是，0否',
  `book_down_payment` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '定金金额',
  `book_final_payment` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '尾款金额',
  `book_down_time` int(11) NOT NULL DEFAULT '0' COMMENT '预定结束时间',
  `book_buyers` mediumint(9) DEFAULT '0' COMMENT '预定人数',
  `have_gift` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否拥有赠品',
  `is_own_shop` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为平台自营',
  `contract_1` tinyint(1) NOT NULL DEFAULT '0' COMMENT '消费者保障服务状态 0关闭 1开启',
  `contract_2` tinyint(1) NOT NULL DEFAULT '0' COMMENT '消费者保障服务状态 0关闭 1开启',
  `contract_3` tinyint(1) NOT NULL DEFAULT '0' COMMENT '消费者保障服务状态 0关闭 1开启',
  `contract_4` tinyint(1) NOT NULL DEFAULT '0' COMMENT '消费者保障服务状态 0关闭 1开启',
  `contract_5` tinyint(1) NOT NULL DEFAULT '0' COMMENT '消费者保障服务状态 0关闭 1开启',
  `contract_6` tinyint(1) NOT NULL DEFAULT '0' COMMENT '消费者保障服务状态 0关闭 1开启',
  `contract_7` tinyint(1) NOT NULL DEFAULT '0' COMMENT '消费者保障服务状态 0关闭 1开启',
  `contract_8` tinyint(1) NOT NULL DEFAULT '0' COMMENT '消费者保障服务状态 0关闭 1开启',
  `contract_9` tinyint(1) NOT NULL DEFAULT '0' COMMENT '消费者保障服务状态 0关闭 1开启',
  `contract_10` tinyint(1) NOT NULL DEFAULT '0' COMMENT '消费者保障服务状态 0关闭 1开启',
  `is_chain` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为门店商品 1是，0否',
  `invite_rate` decimal(10,2) DEFAULT '0.00' COMMENT '分销佣金',
  PRIMARY KEY (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品表';

-- ----------------------------
-- Records of goods
-- ----------------------------

-- ----------------------------
-- Table structure for goods_attr_index
-- ----------------------------
DROP TABLE IF EXISTS `goods_attr_index`;
CREATE TABLE `goods_attr_index` (
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `goods_commonid` int(10) unsigned NOT NULL COMMENT '商品公共表id',
  `gc_id` int(10) unsigned NOT NULL COMMENT '商品分类id',
  `type_id` int(10) unsigned NOT NULL COMMENT '类型id',
  `attr_id` int(10) unsigned NOT NULL COMMENT '属性id',
  `attr_value_id` int(10) unsigned NOT NULL COMMENT '属性值id',
  PRIMARY KEY (`goods_id`,`gc_id`,`attr_value_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品与属性对应表';

-- ----------------------------
-- Records of goods_attr_index
-- ----------------------------

-- ----------------------------
-- Table structure for goods_browse
-- ----------------------------
DROP TABLE IF EXISTS `goods_browse`;
CREATE TABLE `goods_browse` (
  `goods_id` int(11) NOT NULL COMMENT '商品ID',
  `member_id` int(11) NOT NULL COMMENT '会员ID',
  `browsetime` int(11) NOT NULL COMMENT '浏览时间',
  `gc_id` int(11) NOT NULL COMMENT '商品分类',
  `gc_id_1` int(11) NOT NULL COMMENT '商品一级分类',
  `gc_id_2` int(11) NOT NULL COMMENT '商品二级分类',
  `gc_id_3` int(11) NOT NULL COMMENT '商品三级分类',
  PRIMARY KEY (`goods_id`,`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品浏览历史表';

-- ----------------------------
-- Records of goods_browse
-- ----------------------------

-- ----------------------------
-- Table structure for goods_class
-- ----------------------------
DROP TABLE IF EXISTS `goods_class`;
CREATE TABLE `goods_class` (
  `gc_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引ID',
  `gc_name` varchar(100) NOT NULL COMMENT '分类名称',
  `type_id` int(10) unsigned DEFAULT '0' COMMENT '类型id',
  `type_name` varchar(100) DEFAULT '' COMMENT '类型名称',
  `gc_parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `commis_rate` float unsigned NOT NULL DEFAULT '0' COMMENT '佣金比例',
  `gc_sort` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `gc_virtual` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许发布虚拟商品，1是，0否',
  `gc_title` varchar(200) DEFAULT '' COMMENT '名称',
  `gc_keywords` varchar(255) DEFAULT '' COMMENT '关键词',
  `gc_description` varchar(255) DEFAULT '' COMMENT '描述',
  `show_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '商品展示方式，1按颜色，2按SPU',
  PRIMARY KEY (`gc_id`),
  KEY `store_id` (`gc_parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品分类表';

-- ----------------------------
-- Records of goods_class
-- ----------------------------

-- ----------------------------
-- Table structure for goods_class_nav
-- ----------------------------
DROP TABLE IF EXISTS `goods_class_nav`;
CREATE TABLE `goods_class_nav` (
  `cn_adv2_link` varchar(100) NOT NULL COMMENT '广告2链接',
  `gc_id` int(10) unsigned NOT NULL COMMENT '商品分类id',
  `cn_alias` varchar(100) DEFAULT '' COMMENT '商品分类别名',
  `cn_classids` varchar(100) DEFAULT '' COMMENT '推荐子级分类',
  `cn_brandids` varchar(100) DEFAULT '' COMMENT '推荐的品牌',
  `cn_pic` varchar(100) DEFAULT '' COMMENT '分类图片',
  `cn_adv1` varchar(100) DEFAULT '' COMMENT '广告图1',
  `cn_adv1_link` varchar(100) DEFAULT '' COMMENT '广告1链接',
  `cn_adv2` varchar(100) DEFAULT '' COMMENT '广告图2',
  PRIMARY KEY (`gc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分类导航表';

-- ----------------------------
-- Records of goods_class_nav
-- ----------------------------

-- ----------------------------
-- Table structure for goods_class_staple
-- ----------------------------
DROP TABLE IF EXISTS `goods_class_staple`;
CREATE TABLE `goods_class_staple` (
  `staple_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '常用分类id',
  `staple_name` varchar(255) NOT NULL COMMENT '常用分类名称',
  `gc_id_1` int(10) unsigned NOT NULL COMMENT '一级分类id',
  `gc_id_2` int(10) unsigned NOT NULL COMMENT '二级商品分类',
  `gc_id_3` int(10) unsigned NOT NULL COMMENT '三级商品分类',
  `type_id` int(10) unsigned NOT NULL COMMENT '类型id',
  `member_id` int(10) unsigned NOT NULL COMMENT '会员id',
  `counter` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '计数器',
  PRIMARY KEY (`staple_id`),
  KEY `store_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺常用分类表';

-- ----------------------------
-- Records of goods_class_staple
-- ----------------------------

-- ----------------------------
-- Table structure for goods_class_tag
-- ----------------------------
DROP TABLE IF EXISTS `goods_class_tag`;
CREATE TABLE `goods_class_tag` (
  `gc_tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'TAGid',
  `gc_id_1` int(10) unsigned NOT NULL COMMENT '一级分类id',
  `gc_id_2` int(10) unsigned NOT NULL COMMENT '二级分类id',
  `gc_id_3` int(10) unsigned NOT NULL COMMENT '三级分类id',
  `gc_tag_name` varchar(255) NOT NULL COMMENT '分类TAG名称',
  `gc_tag_value` text NOT NULL COMMENT '分类TAG值',
  `gc_id` int(10) unsigned NOT NULL COMMENT '商品分类id',
  `type_id` int(10) unsigned NOT NULL COMMENT '类型id',
  PRIMARY KEY (`gc_tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品分类TAG表';

-- ----------------------------
-- Records of goods_class_tag
-- ----------------------------

-- ----------------------------
-- Table structure for goods_common
-- ----------------------------
DROP TABLE IF EXISTS `goods_common`;
CREATE TABLE `goods_common` (
  `goods_commonid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品公共表id',
  `goods_name` varchar(50) NOT NULL COMMENT '商品名称',
  `goods_jingle` varchar(150) DEFAULT '' COMMENT '商品广告词',
  `gc_id` int(10) unsigned NOT NULL COMMENT '商品分类',
  `gc_id_1` int(10) unsigned NOT NULL COMMENT '一级分类id',
  `gc_id_2` int(10) unsigned NOT NULL COMMENT '二级分类id',
  `gc_id_3` int(10) unsigned NOT NULL COMMENT '三级分类id',
  `gc_name` varchar(200) NOT NULL COMMENT '商品分类',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `store_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `spec_name` varchar(255) NOT NULL COMMENT '规格名称',
  `spec_value` text NOT NULL COMMENT '规格值',
  `brand_id` int(10) unsigned DEFAULT '0' COMMENT '品牌id',
  `brand_name` varchar(100) DEFAULT '' COMMENT '品牌名称',
  `type_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '类型id',
  `goods_image` varchar(100) NOT NULL COMMENT '商品主图',
  `goods_attr` text NOT NULL COMMENT '商品属性',
  `goods_custom` text NOT NULL COMMENT '商品自定义属性',
  `goods_body` text NOT NULL COMMENT '商品内容',
  `mobile_body` text NOT NULL COMMENT '手机端商品描述',
  `goods_state` tinyint(3) unsigned NOT NULL COMMENT '商品状态 0下架，1正常，10违规（禁售）',
  `goods_stateremark` varchar(255) DEFAULT NULL COMMENT '违规原因',
  `goods_verify` tinyint(3) unsigned NOT NULL COMMENT '商品审核 1通过，0未通过，10审核中',
  `goods_verifyremark` varchar(255) DEFAULT NULL COMMENT '审核失败原因',
  `goods_lock` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '商品锁定 0未锁，1已锁',
  `goods_addtime` int(10) unsigned NOT NULL COMMENT '商品添加时间',
  `goods_selltime` int(10) unsigned NOT NULL COMMENT '上架时间',
  `goods_price` decimal(10,2) NOT NULL COMMENT '商品价格',
  `goods_marketprice` decimal(10,2) NOT NULL COMMENT '市场价',
  `goods_costprice` decimal(10,2) NOT NULL COMMENT '成本价',
  `goods_discount` float unsigned NOT NULL COMMENT '折扣',
  `goods_serial` varchar(50) DEFAULT '' COMMENT '商品货号',
  `goods_storage_alarm` tinyint(3) unsigned NOT NULL COMMENT '库存报警值',
  `goods_barcode` varchar(20) DEFAULT '' COMMENT '商品条形码',
  `transport_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '运费模板',
  `transport_title` varchar(60) DEFAULT '' COMMENT '运费模板名称',
  `goods_commend` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '商品推荐 1是，0否，默认为0',
  `goods_freight` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '运费 0为免运费',
  `goods_vat` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否开具增值税发票 1是，0否',
  `areaid_1` int(10) unsigned NOT NULL COMMENT '一级地区id',
  `areaid_2` int(10) unsigned NOT NULL COMMENT '二级地区id',
  `goods_stcids` varchar(255) DEFAULT '' COMMENT '店铺分类id 首尾用,隔开',
  `plateid_top` int(10) unsigned DEFAULT NULL COMMENT '顶部关联板式',
  `plateid_bottom` int(10) unsigned DEFAULT NULL COMMENT '底部关联板式',
  `is_virtual` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为虚拟商品 1是，0否',
  `virtual_indate` int(10) unsigned DEFAULT NULL COMMENT '虚拟商品有效期',
  `virtual_limit` tinyint(3) unsigned DEFAULT NULL COMMENT '虚拟商品购买上限',
  `virtual_invalid_refund` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否允许过期退款， 1是，0否',
  `sup_id` int(11) NOT NULL COMMENT '供应商id',
  `is_own_shop` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为平台自营',
  `contract_3` int(6) DEFAULT NULL,
  `contract_4` int(6) DEFAULT NULL,
  PRIMARY KEY (`goods_commonid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品公共内容表';

-- ----------------------------
-- Records of goods_common
-- ----------------------------

-- ----------------------------
-- Table structure for goods_fcode
-- ----------------------------
DROP TABLE IF EXISTS `goods_fcode`;
CREATE TABLE `goods_fcode` (
  `fc_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'F码id',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品sku',
  `fc_code` varchar(20) NOT NULL COMMENT 'F码',
  `fc_state` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0未使用，1已使用',
  PRIMARY KEY (`fc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品F码';

-- ----------------------------
-- Records of goods_fcode
-- ----------------------------

-- ----------------------------
-- Table structure for goods_gift
-- ----------------------------
DROP TABLE IF EXISTS `goods_gift`;
CREATE TABLE `goods_gift` (
  `gift_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '赠品id ',
  `goods_id` int(10) unsigned NOT NULL COMMENT '主商品id',
  `goods_commonid` int(10) unsigned NOT NULL COMMENT '主商品公共id',
  `gift_goodsid` int(10) unsigned NOT NULL COMMENT '赠品商品id ',
  `gift_goodsname` varchar(50) NOT NULL COMMENT '主商品名称',
  `gift_goodsimage` varchar(100) NOT NULL COMMENT '主商品图片',
  `gift_amount` tinyint(3) unsigned NOT NULL COMMENT '赠品数量',
  PRIMARY KEY (`gift_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品赠品表';

-- ----------------------------
-- Records of goods_gift
-- ----------------------------

-- ----------------------------
-- Table structure for goods_images
-- ----------------------------
DROP TABLE IF EXISTS `goods_images`;
CREATE TABLE `goods_images` (
  `goods_image_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品图片id',
  `goods_commonid` int(10) unsigned NOT NULL COMMENT '商品公共内容id',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `color_id` int(10) unsigned NOT NULL COMMENT '颜色规格值id',
  `goods_image` varchar(1000) NOT NULL COMMENT '商品图片',
  `goods_image_sort` tinyint(3) unsigned NOT NULL COMMENT '排序',
  `is_default` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '默认主题，1是，0否',
  PRIMARY KEY (`goods_image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品图片';

-- ----------------------------
-- Records of goods_images
-- ----------------------------

-- ----------------------------
-- Table structure for goods_items
-- ----------------------------
DROP TABLE IF EXISTS `goods_items`;
CREATE TABLE `goods_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` bigint(30) NOT NULL DEFAULT '0' COMMENT '货号',
  `goods_sn` varchar(50) DEFAULT NULL COMMENT '款号',
  `product_type` varchar(50) DEFAULT '0' COMMENT '产品线',
  `cat_type` varchar(50) DEFAULT '0' COMMENT '款式分类',
  `is_on_sale` int(3) NOT NULL DEFAULT '1' COMMENT '见数据字典',
  `prc_id` int(4) DEFAULT '0' COMMENT '供应商ID',
  `prc_name` varchar(100) DEFAULT NULL COMMENT '供货商名称',
  `put_in_type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '入库方式 见数据字典',
  `goods_name` varchar(100) DEFAULT NULL COMMENT '商品名称',
  `company` varchar(100) DEFAULT NULL COMMENT '公司',
  `warehouse` varchar(30) DEFAULT NULL COMMENT '仓库',
  `company_id` int(11) NOT NULL COMMENT '公司ID',
  `warehouse_id` int(11) NOT NULL COMMENT '仓库D',
  `caizhi` varchar(20) DEFAULT NULL COMMENT '材质',
  `jinzhong` decimal(8,3) DEFAULT NULL COMMENT '金重',
  `jinhao` varchar(50) DEFAULT '0' COMMENT '金耗',
  `zongzhong` varchar(50) DEFAULT NULL COMMENT '总重',
  `shoucun` varchar(50) DEFAULT NULL COMMENT '手寸',
  `order_sn` varchar(40) DEFAULT NULL COMMENT '订单号',
  `buchan_sn` varchar(20) DEFAULT NULL COMMENT '布产号',
  `order_detail_id` int(10) DEFAULT '0' COMMENT '订单商品detail_id',
  `pinpai` varchar(100) DEFAULT NULL COMMENT '品牌',
  `changdu` varchar(100) DEFAULT NULL COMMENT '长度',
  `zhengshuhao` varchar(100) DEFAULT NULL COMMENT '证书号',
  `zhengshuhao2` varchar(30) DEFAULT NULL COMMENT '证书号2',
  `peijianshuliang` varchar(50) DEFAULT NULL COMMENT '配件数量',
  `guojizhengshu` varchar(50) DEFAULT NULL COMMENT '国际证书',
  `zhengshuleibie` varchar(50) DEFAULT NULL COMMENT '证书类别',
  `gemx_zhengshu` varchar(64) DEFAULT NULL,
  `num` int(6) NOT NULL DEFAULT '1',
  `addtime` datetime DEFAULT NULL,
  `yanse` varchar(50) DEFAULT NULL COMMENT '颜色',
  `jingdu` varchar(50) DEFAULT NULL COMMENT '净度',
  `qiegong` varchar(10) DEFAULT NULL COMMENT '切工',
  `paoguang` varchar(10) DEFAULT NULL COMMENT '抛光',
  `duichen` varchar(10) DEFAULT NULL COMMENT '对称',
  `yingguang` varchar(10) DEFAULT NULL COMMENT '荧光',
  `zuanshizhekou` varchar(11) DEFAULT NULL,
  `jinse` varchar(10) DEFAULT NULL COMMENT '材质颜色/金色',
  `guojibaojia` varchar(20) DEFAULT NULL COMMENT '裸钻国际报价',
  `luozuanzhengshu` varchar(100) DEFAULT NULL COMMENT '裸钻证书类型',
  `tuo_type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '金托类型2空托1成品',
  `huopin_type` int(1) NOT NULL DEFAULT '2' COMMENT '货品类型；0为A类；1为B类；2为C类；',
  `dia_sn` varchar(4) DEFAULT NULL COMMENT '钻石代码（色阶+净度）',
  `biaoqianjia` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '标签价',
  `jietuoxiangkou` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '戒托镶口',
  `box_sn` varchar(30) DEFAULT '0-00-0-0' COMMENT '默认柜位',
  `jiejia` tinyint(1) DEFAULT NULL COMMENT '是否结价：1=是,0=否',
  `weixiu_status` tinyint(2) DEFAULT '0' COMMENT '货品维修状态 1维修取消 2.维修申请、3.维修受理、4.维修完成、5.待发货、6.转仓中',
  `weixiu_company_id` int(10) DEFAULT '0' COMMENT '维修入库公司id',
  `weixiu_company_name` varchar(30) DEFAULT '' COMMENT '维修入库公司名称',
  `weixiu_warehouse_id` int(10) DEFAULT '0' COMMENT '维修入库仓库id',
  `weixiu_warehouse_name` varchar(30) DEFAULT '' COMMENT '维修入库仓库名称',
  `chuku_time` datetime DEFAULT NULL COMMENT '出库时间',
  `color_grade` varchar(50) DEFAULT NULL COMMENT '颜色等级',
  `is_hrds` tinyint(1) DEFAULT '0' COMMENT '星耀钻石 1:否；2:是',
  `peijianjinzhong` decimal(8,3) DEFAULT NULL COMMENT '配件金重',
  `zhushi` varchar(50) DEFAULT NULL COMMENT '主石',
  `zhushilishu` varchar(11) NOT NULL DEFAULT '0' COMMENT '主石粒数',
  `zuanshidaxiao` decimal(10,3) DEFAULT '0.000' COMMENT '主石大小',
  `zhushizhongjijia` varchar(50) DEFAULT NULL COMMENT '主石总计价',
  `zhushiyanse` varchar(50) DEFAULT NULL COMMENT '主石颜色',
  `zhushijingdu` varchar(50) DEFAULT NULL COMMENT '主石净度',
  `zhushiqiegong` varchar(50) DEFAULT NULL COMMENT '主石切工',
  `zhushixingzhuang` varchar(50) DEFAULT NULL COMMENT '主石形状',
  `zhushibaohao` varchar(60) DEFAULT NULL COMMENT '主石包号',
  `zhushiguige` varchar(60) DEFAULT NULL COMMENT '主石规格',
  `zhushitiaoma` varchar(100) DEFAULT NULL COMMENT '主石条码',
  `fushi` varchar(50) DEFAULT NULL COMMENT '副石',
  `fushilishu` varchar(50) DEFAULT NULL COMMENT '副石粒数',
  `fushizhong` decimal(8,3) NOT NULL DEFAULT '0.000',
  `fushizhongjijia` varchar(50) DEFAULT NULL,
  `fushibaohao` varchar(50) DEFAULT NULL,
  `fushiguige` varchar(50) DEFAULT NULL,
  `fushiyanse` varchar(50) DEFAULT NULL,
  `fushijingdu` varchar(50) DEFAULT NULL,
  `fushixingzhuang` varchar(50) DEFAULT NULL,
  `shi2` varchar(40) DEFAULT NULL COMMENT '副石2',
  `shi2lishu` varchar(40) DEFAULT NULL COMMENT '副石2粒数',
  `shi2zhong` decimal(8,3) NOT NULL DEFAULT '0.000' COMMENT '副石2重',
  `shi2zhongjijia` varchar(40) DEFAULT NULL COMMENT '副石2总计价',
  `shi2baohao` varchar(60) DEFAULT NULL COMMENT '石2包号',
  `shi3` varchar(40) DEFAULT NULL COMMENT '副石3',
  `shi3lishu` varchar(40) DEFAULT NULL COMMENT '副石3粒数',
  `shi3zhong` decimal(8,3) NOT NULL DEFAULT '0.000' COMMENT '副石3重',
  `shi3zhongjijia` varchar(40) DEFAULT NULL COMMENT '副石3总计价',
  `shi3baohao` varchar(60) DEFAULT NULL COMMENT '石3包号',
  `yuanshichengbenjia` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '原始成本价',
  `mingyichengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '名义成本',
  `jijiachengben` varchar(50) DEFAULT NULL COMMENT '计价成本',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `store_id` int(11) DEFAULT NULL,
  `old_detail_id` int(10) unsigned DEFAULT '0' COMMENT '展厅历史订单明细ID',
  `goods_click` int(10) unsigned DEFAULT '1',
  `is_shopzc` tinyint(1) unsigned DEFAULT '0' COMMENT '商品原始入库方式，是否门店自采 1是 0否',
  `management_fee` decimal(10,2) DEFAULT '0.00' COMMENT '管理费',
  PRIMARY KEY (`id`),
  UNIQUE KEY `goods_id` (`goods_id`),
  KEY `is_on_sale` (`is_on_sale`),
  KEY `company` (`company`,`warehouse`),
  KEY `order_goods_id` (`order_detail_id`) USING BTREE,
  KEY `box_sn` (`box_sn`),
  KEY `goods_sn` (`goods_sn`) USING BTREE,
  KEY `xinyaozhanshi` (`is_hrds`),
  KEY `zhengshuhao` (`zhengshuhao`) USING BTREE,
  KEY `product_type` (`product_type`) USING BTREE,
  KEY `cat_type` (`cat_type`) USING BTREE,
  KEY `old_detail_id` (`old_detail_id`) USING BTREE,
  KEY `company_id` (`company_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='仓库货品表';

-- ----------------------------
-- Records of goods_items
-- ----------------------------

-- ----------------------------
-- Table structure for goods_items_log
-- ----------------------------
DROP TABLE IF EXISTS `goods_items_log`;
CREATE TABLE `goods_items_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '日志编号',
  `log_remark` varchar(1024) DEFAULT NULL,
  `log_user_id` int(10) unsigned DEFAULT NULL COMMENT '用户ID',
  `log_user_name` varchar(50) DEFAULT NULL COMMENT '用户账户',
  `log_user_ip` varchar(50) DEFAULT NULL COMMENT '用户IP',
  `log_store_id` int(10) unsigned DEFAULT NULL COMMENT '店铺编号',
  `goods_itemid` varchar(32) NOT NULL COMMENT '货号',
  `goods_warehouse` varchar(80) DEFAULT NULL COMMENT '商品当前仓库',
  `goods_box` varchar(20) DEFAULT NULL COMMENT '商品当前柜位',
  `goods_state` tinyint(255) DEFAULT NULL COMMENT '商品状态',
  `log_time` datetime DEFAULT NULL COMMENT '日志时间',
  `log_type` varchar(10) DEFAULT NULL,
  `order_sn` varchar(35) DEFAULT NULL COMMENT '订单号',
  `bill_no` varchar(35) DEFAULT NULL COMMENT '单据编号',
  PRIMARY KEY (`log_id`),
  KEY `goods_itemid` (`goods_itemid`,`log_store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='卖家库存操作日志表';

-- ----------------------------
-- Records of goods_items_log
-- ----------------------------

-- ----------------------------
-- Table structure for goods_recommend
-- ----------------------------
DROP TABLE IF EXISTS `goods_recommend`;
CREATE TABLE `goods_recommend` (
  `rec_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rec_gc_id` mediumint(9) DEFAULT NULL COMMENT '最底级商品分类ID',
  `rec_goods_id` varchar(50) DEFAULT NULL COMMENT '商品goods_id',
  `rec_gc_name` varchar(150) DEFAULT NULL COMMENT '商品分类名称',
  PRIMARY KEY (`rec_id`),
  KEY `rec_gc_id` (`rec_gc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品推荐表';

-- ----------------------------
-- Records of goods_recommend
-- ----------------------------

-- ----------------------------
-- Table structure for groupbuy
-- ----------------------------
DROP TABLE IF EXISTS `groupbuy`;
CREATE TABLE `groupbuy` (
  `groupbuy_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '抢购ID',
  `groupbuy_name` varchar(255) NOT NULL COMMENT '活动名称',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品ID',
  `goods_commonid` int(10) unsigned NOT NULL COMMENT '商品公共表ID',
  `goods_name` varchar(200) NOT NULL COMMENT '商品名称',
  `store_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺ID',
  `store_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `goods_price` decimal(10,2) NOT NULL COMMENT '商品原价',
  `groupbuy_price` decimal(10,2) NOT NULL COMMENT '抢购价格',
  `groupbuy_rebate` decimal(10,2) NOT NULL COMMENT '折扣',
  `virtual_quantity` int(10) unsigned NOT NULL COMMENT '虚拟购买数量',
  `upper_limit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '购买上限',
  `buyer_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已购买人数',
  `buy_quantity` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '购买数量',
  `groupbuy_intro` text COMMENT '本团介绍',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '抢购状态 10-审核中 20-正常 30-审核失败 31-管理员关闭 32-已结束',
  `recommended` tinyint(1) unsigned NOT NULL COMMENT '是否推荐 0.未推荐 1.已推荐',
  `views` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '查看次数',
  `class_id` int(10) unsigned NOT NULL COMMENT '抢购类别编号',
  `s_class_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '抢购2级分类id',
  `groupbuy_image` varchar(100) NOT NULL COMMENT '抢购图片',
  `groupbuy_image1` varchar(100) DEFAULT NULL COMMENT '抢购图片1',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `is_vr` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否虚拟抢购 1是0否',
  `vr_city_id` int(11) DEFAULT NULL COMMENT '虚拟抢购城市id',
  `vr_area_id` int(11) DEFAULT NULL COMMENT '虚拟抢购区域id',
  `vr_mall_id` int(11) DEFAULT NULL COMMENT '虚拟抢购商区id',
  `vr_class_id` int(11) DEFAULT NULL COMMENT '虚拟抢购大分类id',
  `vr_s_class_id` int(11) DEFAULT NULL COMMENT '虚拟抢购小分类id',
  PRIMARY KEY (`groupbuy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='抢购商品表';

-- ----------------------------
-- Records of groupbuy
-- ----------------------------

-- ----------------------------
-- Table structure for groupbuy_class
-- ----------------------------
DROP TABLE IF EXISTS `groupbuy_class`;
CREATE TABLE `groupbuy_class` (
  `class_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '类别编号',
  `class_name` varchar(20) NOT NULL COMMENT '类别名称',
  `class_parent_id` int(10) unsigned NOT NULL COMMENT '父类别编号',
  `sort` tinyint(1) unsigned NOT NULL COMMENT '排序',
  `deep` tinyint(1) unsigned DEFAULT '0' COMMENT '深度',
  PRIMARY KEY (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='抢购类别表';

-- ----------------------------
-- Records of groupbuy_class
-- ----------------------------

-- ----------------------------
-- Table structure for groupbuy_price_range
-- ----------------------------
DROP TABLE IF EXISTS `groupbuy_price_range`;
CREATE TABLE `groupbuy_price_range` (
  `range_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '价格区间编号',
  `range_name` varchar(20) NOT NULL COMMENT '区间名称',
  `range_start` int(10) unsigned NOT NULL COMMENT '区间下限',
  `range_end` int(10) unsigned NOT NULL COMMENT '区间上限',
  PRIMARY KEY (`range_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='抢购价格区间表';

-- ----------------------------
-- Records of groupbuy_price_range
-- ----------------------------

-- ----------------------------
-- Table structure for groupbuy_quota
-- ----------------------------
DROP TABLE IF EXISTS `groupbuy_quota`;
CREATE TABLE `groupbuy_quota` (
  `quota_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '抢购套餐编号',
  `member_id` int(10) unsigned NOT NULL COMMENT '用户编号',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺编号',
  `member_name` varchar(50) NOT NULL COMMENT '用户名',
  `store_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `start_time` int(10) unsigned NOT NULL COMMENT '套餐开始时间',
  `end_time` int(10) unsigned NOT NULL COMMENT '套餐结束时间',
  PRIMARY KEY (`quota_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='抢购套餐表';

-- ----------------------------
-- Records of groupbuy_quota
-- ----------------------------

-- ----------------------------
-- Table structure for help
-- ----------------------------
DROP TABLE IF EXISTS `help`;
CREATE TABLE `help` (
  `help_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '帮助ID',
  `help_sort` tinyint(1) unsigned DEFAULT '255' COMMENT '排序',
  `help_title` varchar(100) NOT NULL COMMENT '标题',
  `help_info` text COMMENT '帮助内容',
  `help_url` varchar(100) DEFAULT '' COMMENT '跳转链接',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  `type_id` int(10) unsigned NOT NULL COMMENT '帮助类型',
  `page_show` tinyint(1) unsigned DEFAULT '1' COMMENT '页面类型:1为店铺,2为会员,默认为1',
  PRIMARY KEY (`help_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='帮助内容表';

-- ----------------------------
-- Records of help
-- ----------------------------

-- ----------------------------
-- Table structure for help_type
-- ----------------------------
DROP TABLE IF EXISTS `help_type`;
CREATE TABLE `help_type` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '类型ID',
  `type_name` varchar(50) NOT NULL COMMENT '类型名称',
  `type_sort` tinyint(1) unsigned DEFAULT '255' COMMENT '排序',
  `help_code` varchar(10) DEFAULT 'auto' COMMENT '调用编号(auto的可删除)',
  `help_show` tinyint(1) unsigned DEFAULT '1' COMMENT '是否显示,0为否,1为是,默认为1',
  `page_show` tinyint(1) unsigned DEFAULT '1' COMMENT '页面类型:1为店铺,2为会员,默认为1',
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='帮助类型表';

-- ----------------------------
-- Records of help_type
-- ----------------------------

-- ----------------------------
-- Table structure for inform
-- ----------------------------
DROP TABLE IF EXISTS `inform`;
CREATE TABLE `inform` (
  `inform_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '举报id',
  `inform_member_id` int(11) NOT NULL COMMENT '举报人id',
  `inform_member_name` varchar(50) NOT NULL COMMENT '举报人会员名',
  `inform_goods_id` int(11) NOT NULL COMMENT '被举报的商品id',
  `inform_goods_name` varchar(100) NOT NULL COMMENT '被举报的商品名称',
  `inform_subject_id` int(11) NOT NULL COMMENT '举报主题id',
  `inform_subject_content` varchar(50) NOT NULL COMMENT '举报主题',
  `inform_content` varchar(100) NOT NULL COMMENT '举报信息',
  `inform_pic1` varchar(100) DEFAULT NULL COMMENT '图片1',
  `inform_pic2` varchar(100) DEFAULT NULL COMMENT '图片2',
  `inform_pic3` varchar(100) DEFAULT NULL COMMENT '图片3',
  `inform_datetime` int(11) NOT NULL COMMENT '举报时间',
  `inform_store_id` int(11) NOT NULL COMMENT '被举报商品的店铺id',
  `inform_state` tinyint(4) NOT NULL COMMENT '举报状态(1未处理/2已处理)',
  `inform_handle_type` tinyint(4) NOT NULL COMMENT '举报处理结果(1无效举报/2恶意举报/3有效举报)',
  `inform_handle_message` varchar(100) DEFAULT '' COMMENT '举报处理信息',
  `inform_handle_datetime` int(11) DEFAULT '0' COMMENT '举报处理时间',
  `inform_handle_member_id` int(11) DEFAULT '0' COMMENT '管理员id',
  `inform_goods_image` varchar(150) DEFAULT NULL COMMENT '商品图',
  `inform_store_name` varchar(100) DEFAULT NULL COMMENT '店铺名',
  PRIMARY KEY (`inform_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='举报表';

-- ----------------------------
-- Records of inform
-- ----------------------------

-- ----------------------------
-- Table structure for inform_subject
-- ----------------------------
DROP TABLE IF EXISTS `inform_subject`;
CREATE TABLE `inform_subject` (
  `inform_subject_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '举报主题id',
  `inform_subject_content` varchar(100) NOT NULL COMMENT '举报主题内容',
  `inform_subject_type_id` int(11) NOT NULL COMMENT '举报类型id',
  `inform_subject_type_name` varchar(50) NOT NULL COMMENT '举报类型名称 ',
  `inform_subject_state` tinyint(11) NOT NULL COMMENT '举报主题状态(1可用/2失效)',
  PRIMARY KEY (`inform_subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='举报主题表';

-- ----------------------------
-- Records of inform_subject
-- ----------------------------

-- ----------------------------
-- Table structure for inform_subject_type
-- ----------------------------
DROP TABLE IF EXISTS `inform_subject_type`;
CREATE TABLE `inform_subject_type` (
  `inform_type_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '举报类型id',
  `inform_type_name` varchar(50) NOT NULL COMMENT '举报类型名称 ',
  `inform_type_desc` varchar(100) NOT NULL COMMENT '举报类型描述',
  `inform_type_state` tinyint(4) NOT NULL COMMENT '举报类型状态(1有效/2失效)',
  PRIMARY KEY (`inform_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='举报类型表';

-- ----------------------------
-- Records of inform_subject_type
-- ----------------------------

-- ----------------------------
-- Table structure for invoice
-- ----------------------------
DROP TABLE IF EXISTS `invoice`;
CREATE TABLE `invoice` (
  `inv_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '索引id',
  `member_id` int(10) unsigned NOT NULL COMMENT '会员ID',
  `inv_state` enum('1','2') DEFAULT NULL COMMENT '1普通发票2增值税发票',
  `inv_title` varchar(50) DEFAULT '' COMMENT '发票抬头[普通发票]',
  `inv_content` varchar(10) DEFAULT '' COMMENT '发票内容[普通发票]',
  `inv_company` varchar(50) DEFAULT '' COMMENT '单位名称',
  `inv_code` varchar(50) DEFAULT '' COMMENT '纳税人识别号',
  `inv_reg_addr` varchar(50) DEFAULT '' COMMENT '注册地址',
  `inv_reg_phone` varchar(30) DEFAULT '' COMMENT '注册电话',
  `inv_reg_bname` varchar(30) DEFAULT '' COMMENT '开户银行',
  `inv_reg_baccount` varchar(30) DEFAULT '' COMMENT '银行账户',
  `inv_rec_name` varchar(20) DEFAULT '' COMMENT '收票人姓名',
  `inv_rec_mobphone` varchar(15) DEFAULT '' COMMENT '收票人手机号',
  `inv_rec_province` varchar(30) DEFAULT '' COMMENT '收票人省份',
  `inv_goto_addr` varchar(50) DEFAULT '' COMMENT '送票地址',
  PRIMARY KEY (`inv_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='买家发票信息表';

-- ----------------------------
-- Records of invoice
-- ----------------------------

-- ----------------------------
-- Table structure for jxc_wholesale
-- ----------------------------
DROP TABLE IF EXISTS `jxc_wholesale`;
CREATE TABLE `jxc_wholesale` (
  `wholesale_id` int(10) NOT NULL AUTO_INCREMENT,
  `wholesale_sn` varchar(10) DEFAULT NULL COMMENT '批发客户编号',
  `wholesale_name` varchar(30) DEFAULT NULL COMMENT '批发客户名称',
  `wholesale_credit` decimal(10,2) DEFAULT NULL COMMENT '授信额度',
  `wholesale_status` int(2) DEFAULT '1' COMMENT '开启状态  1=开启，0=关闭',
  `add_name` varchar(20) DEFAULT NULL COMMENT '添加人',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `sign_required` smallint(2) DEFAULT '0' COMMENT '是否需求签收',
  `sign_company` int(255) DEFAULT NULL COMMENT '签收公司',
  PRIMARY KEY (`wholesale_id`),
  UNIQUE KEY `sn` (`wholesale_sn`)
) ENGINE=MyISAM AUTO_INCREMENT=184 DEFAULT CHARSET=utf8 COMMENT='批发客户管理表';

-- ----------------------------
-- Records of jxc_wholesale
-- ----------------------------

-- ----------------------------
-- Table structure for link
-- ----------------------------
DROP TABLE IF EXISTS `link`;
CREATE TABLE `link` (
  `link_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引id',
  `link_title` varchar(100) DEFAULT NULL COMMENT '标题',
  `link_url` varchar(100) DEFAULT NULL COMMENT '链接',
  `link_pic` varchar(100) DEFAULT NULL COMMENT '图片',
  `link_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  PRIMARY KEY (`link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='合作伙伴表';

-- ----------------------------
-- Records of link
-- ----------------------------

-- ----------------------------
-- Table structure for list_style_goods
-- ----------------------------
DROP TABLE IF EXISTS `list_style_goods`;
CREATE TABLE `list_style_goods` (
  `goods_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_type_id` int(10) DEFAULT NULL COMMENT '产品线id',
  `cat_type_id` int(10) NOT NULL COMMENT '款式分类id',
  `style_id` int(10) unsigned DEFAULT NULL COMMENT '款式id',
  `style_sn` varchar(20) NOT NULL COMMENT '款式编号',
  `style_name` varchar(60) NOT NULL COMMENT '款式名称',
  `goods_sn` varchar(60) NOT NULL COMMENT '产品编号',
  `shoucun` int(2) NOT NULL COMMENT '手寸',
  `xiangkou` varchar(10) NOT NULL COMMENT '镶口',
  `caizhi` int(2) NOT NULL COMMENT '材质',
  `yanse` int(1) NOT NULL COMMENT '颜色',
  `zhushizhong` decimal(8,3) NOT NULL COMMENT '主石重',
  `zhushi_num` int(3) NOT NULL COMMENT '主石数',
  `fushizhong1` decimal(8,3) NOT NULL COMMENT '副石1重',
  `fushi_num1` int(3) NOT NULL COMMENT '副石1数',
  `fushizhong2` decimal(8,3) NOT NULL COMMENT '副石2重',
  `fushi_num2` int(3) NOT NULL COMMENT '副石2数',
  `fushizhong3` decimal(8,3) NOT NULL COMMENT '副石3重',
  `fushi_num3` int(3) NOT NULL COMMENT '副石3数',
  `fushi_chengbenjia_other` decimal(10,2) DEFAULT NULL COMMENT '其他副石副石成本价',
  `weight` decimal(8,3) NOT NULL COMMENT '材质金重',
  `jincha_shang` decimal(8,3) NOT NULL COMMENT '金重上公差',
  `jincha_xia` decimal(8,3) NOT NULL COMMENT '金重下公差',
  `dingzhichengben` decimal(10,2) NOT NULL COMMENT '定制成本',
  `is_ok` int(1) NOT NULL DEFAULT '1' COMMENT '是否上架;0为下架;1为上架',
  `last_update` datetime NOT NULL COMMENT '最后更新时间',
  `is_base_style` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否基本款(0：否，1：是)',
  `is_quick_diy` tinyint(1) unsigned DEFAULT '0' COMMENT '是否快速定制 1是 0否',
  `xiangkou_company_type` varchar(10) DEFAULT NULL COMMENT '可销售公司类型',
  PRIMARY KEY (`goods_id`),
  KEY `style_id` (`style_id`),
  KEY `goods_sn` (`goods_sn`),
  KEY `style_sn` (`style_sn`),
  KEY `style_id_2` (`style_id`,`caizhi`,`xiangkou`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of list_style_goods
-- ----------------------------

-- ----------------------------
-- Table structure for lock
-- ----------------------------
DROP TABLE IF EXISTS `lock`;
CREATE TABLE `lock` (
  `pid` bigint(20) unsigned NOT NULL COMMENT 'IP+TYPE',
  `pvalue` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '次数',
  `expiretime` int(11) NOT NULL DEFAULT '0' COMMENT '锁定截止时间',
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='防灌水表';

-- ----------------------------
-- Records of lock
-- ----------------------------

-- ----------------------------
-- Table structure for logi_bill
-- ----------------------------
DROP TABLE IF EXISTS `logi_bill`;
CREATE TABLE `logi_bill` (
  `id` int(11) NOT NULL,
  `bill_no` char(20) DEFAULT NULL COMMENT '订单编号',
  `status` tinyint(2) DEFAULT NULL,
  `supply` varchar(250) DEFAULT NULL COMMENT '供应商',
  `puchase` varchar(250) DEFAULT NULL COMMENT '采购商',
  `system_estimate_price` decimal(12,2) DEFAULT NULL COMMENT '系统初步估价',
  `real_estimate_price` decimal(12,2) DEFAULT NULL COMMENT '实际估价',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of logi_bill
-- ----------------------------

-- ----------------------------
-- Table structure for mail_cron
-- ----------------------------
DROP TABLE IF EXISTS `mail_cron`;
CREATE TABLE `mail_cron` (
  `mail_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '消息任务计划id',
  `mail` varchar(100) NOT NULL COMMENT '邮箱地址',
  `subject` varchar(255) NOT NULL COMMENT '邮件标题',
  `contnet` text NOT NULL COMMENT '邮件内容',
  PRIMARY KEY (`mail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邮件任务计划表';

-- ----------------------------
-- Records of mail_cron
-- ----------------------------

-- ----------------------------
-- Table structure for mail_msg_temlates
-- ----------------------------
DROP TABLE IF EXISTS `mail_msg_temlates`;
CREATE TABLE `mail_msg_temlates` (
  `name` varchar(100) NOT NULL COMMENT '模板名称',
  `title` varchar(100) DEFAULT NULL COMMENT '模板标题',
  `code` varchar(30) NOT NULL COMMENT '模板调用代码',
  `content` text NOT NULL COMMENT '模板内容',
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邮件模板表';

-- ----------------------------
-- Records of mail_msg_temlates
-- ----------------------------

-- ----------------------------
-- Table structure for mall_consult
-- ----------------------------
DROP TABLE IF EXISTS `mall_consult`;
CREATE TABLE `mall_consult` (
  `mc_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '平台客服咨询id',
  `mct_id` int(10) unsigned NOT NULL COMMENT '咨询类型id',
  `member_id` int(10) unsigned NOT NULL COMMENT '会员id',
  `member_name` varchar(50) NOT NULL COMMENT '会员名称',
  `mc_content` varchar(500) NOT NULL COMMENT '咨询内容',
  `mc_addtime` int(10) unsigned NOT NULL COMMENT '咨询时间',
  `is_reply` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否回复，1是，0否，默认0',
  `mc_reply` varchar(500) DEFAULT '' COMMENT '回复内容',
  `mc_reply_time` int(10) unsigned DEFAULT '0' COMMENT '回复时间',
  `admin_id` int(10) unsigned DEFAULT '0' COMMENT '管理员id',
  `admin_name` varchar(50) DEFAULT '' COMMENT '管理员名称',
  PRIMARY KEY (`mc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='平台客服咨询表';

-- ----------------------------
-- Records of mall_consult
-- ----------------------------

-- ----------------------------
-- Table structure for mall_consult_type
-- ----------------------------
DROP TABLE IF EXISTS `mall_consult_type`;
CREATE TABLE `mall_consult_type` (
  `mct_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '平台客服咨询类型id',
  `mct_name` varchar(50) NOT NULL COMMENT '咨询类型名称',
  `mct_introduce` text NOT NULL COMMENT '平台客服咨询类型备注',
  `mct_sort` tinyint(3) unsigned DEFAULT '255' COMMENT '咨询类型排序',
  PRIMARY KEY (`mct_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='平台客服咨询类型表';

-- ----------------------------
-- Records of mall_consult_type
-- ----------------------------

-- ----------------------------
-- Table structure for mb_category
-- ----------------------------
DROP TABLE IF EXISTS `mb_category`;
CREATE TABLE `mb_category` (
  `gc_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商城系统的分类ID',
  `gc_thumb` varchar(150) DEFAULT NULL COMMENT '缩略图',
  PRIMARY KEY (`gc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='一级分类缩略图[手机端]';

-- ----------------------------
-- Records of mb_category
-- ----------------------------

-- ----------------------------
-- Table structure for mb_feedback
-- ----------------------------
DROP TABLE IF EXISTS `mb_feedback`;
CREATE TABLE `mb_feedback` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` varchar(500) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL COMMENT '1来自手机端2来自PC端',
  `ftime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '反馈时间',
  `member_id` int(10) unsigned NOT NULL COMMENT '用户编号',
  `member_name` varchar(50) NOT NULL COMMENT '用户名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='意见反馈';

-- ----------------------------
-- Records of mb_feedback
-- ----------------------------

-- ----------------------------
-- Table structure for mb_payment
-- ----------------------------
DROP TABLE IF EXISTS `mb_payment`;
CREATE TABLE `mb_payment` (
  `payment_id` tinyint(1) unsigned NOT NULL COMMENT '支付索引id',
  `payment_code` char(20) NOT NULL COMMENT '支付代码名称',
  `payment_name` char(10) NOT NULL COMMENT '支付名称',
  `payment_config` varchar(255) DEFAULT NULL COMMENT '支付接口配置信息',
  `payment_state` enum('0','1') NOT NULL DEFAULT '0' COMMENT '接口状态0禁用1启用',
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='手机支付方式表';

-- ----------------------------
-- Records of mb_payment
-- ----------------------------

-- ----------------------------
-- Table structure for mb_seller_token
-- ----------------------------
DROP TABLE IF EXISTS `mb_seller_token`;
CREATE TABLE `mb_seller_token` (
  `token_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '令牌编号',
  `seller_id` int(10) unsigned NOT NULL COMMENT '用户编号',
  `seller_name` varchar(50) NOT NULL COMMENT '用户名',
  `token` varchar(50) NOT NULL COMMENT '登录令牌',
  `openid` varchar(50) DEFAULT NULL COMMENT '微信支付jsapi的openid缓存',
  `login_time` int(10) unsigned NOT NULL COMMENT '登录时间',
  `client_type` varchar(10) NOT NULL COMMENT '客户端类型 windows',
  PRIMARY KEY (`token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客户端商家登录令牌表';

-- ----------------------------
-- Records of mb_seller_token
-- ----------------------------

-- ----------------------------
-- Table structure for mb_special
-- ----------------------------
DROP TABLE IF EXISTS `mb_special`;
CREATE TABLE `mb_special` (
  `special_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '专题编号',
  `special_desc` varchar(20) NOT NULL COMMENT '专题描述',
  PRIMARY KEY (`special_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='手机专题表';

-- ----------------------------
-- Records of mb_special
-- ----------------------------

-- ----------------------------
-- Table structure for mb_special_item
-- ----------------------------
DROP TABLE IF EXISTS `mb_special_item`;
CREATE TABLE `mb_special_item` (
  `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '专题项目编号',
  `special_id` int(10) unsigned NOT NULL COMMENT '专题编号',
  `item_type` varchar(50) NOT NULL COMMENT '项目类型',
  `item_data` varchar(2000) NOT NULL COMMENT '项目内容',
  `item_usable` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '项目是否可用 0-不可用 1-可用',
  `item_sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '项目排序',
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='手机专题项目表';

-- ----------------------------
-- Records of mb_special_item
-- ----------------------------

-- ----------------------------
-- Table structure for mb_user_token
-- ----------------------------
DROP TABLE IF EXISTS `mb_user_token`;
CREATE TABLE `mb_user_token` (
  `token_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '令牌编号',
  `member_id` int(10) unsigned NOT NULL COMMENT '用户编号',
  `member_name` varchar(50) NOT NULL COMMENT '用户名',
  `token` varchar(50) NOT NULL COMMENT '登录令牌',
  `openid` varchar(50) DEFAULT NULL COMMENT '微信支付jsapi的openid缓存',
  `login_time` int(10) unsigned NOT NULL COMMENT '登录时间',
  `client_type` varchar(10) NOT NULL COMMENT '客户端类型 android wap',
  PRIMARY KEY (`token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='移动端登录令牌表';

-- ----------------------------
-- Records of mb_user_token
-- ----------------------------

-- ----------------------------
-- Table structure for member
-- ----------------------------
DROP TABLE IF EXISTS `member`;
CREATE TABLE `member` (
  `member_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '会员id',
  `member_name` varchar(50) NOT NULL COMMENT '会员名称',
  `member_truename` varchar(20) DEFAULT NULL COMMENT '真实姓名',
  `member_avatar` varchar(50) DEFAULT NULL COMMENT '会员头像',
  `member_sex` tinyint(1) DEFAULT NULL COMMENT '会员性别',
  `member_birthday` date DEFAULT NULL COMMENT '生日',
  `member_passwd` varchar(32) NOT NULL COMMENT '会员密码',
  `member_paypwd` char(32) DEFAULT NULL COMMENT '支付密码',
  `member_email` varchar(100) NOT NULL COMMENT '会员邮箱',
  `member_email_bind` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未绑定1已绑定',
  `member_mobile` varchar(11) DEFAULT NULL COMMENT '手机号',
  `member_mobile_bind` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未绑定1已绑定',
  `member_qq` varchar(100) DEFAULT NULL COMMENT 'qq',
  `member_ww` varchar(100) DEFAULT NULL COMMENT '阿里旺旺',
  `member_login_num` int(11) NOT NULL DEFAULT '1' COMMENT '登录次数',
  `member_time` varchar(10) NOT NULL COMMENT '会员注册时间',
  `member_login_time` varchar(10) NOT NULL COMMENT '当前登录时间',
  `member_old_login_time` varchar(10) NOT NULL COMMENT '上次登录时间',
  `member_login_ip` varchar(20) DEFAULT NULL COMMENT '当前登录ip',
  `member_old_login_ip` varchar(20) DEFAULT NULL COMMENT '上次登录ip',
  `member_qqopenid` varchar(100) DEFAULT NULL COMMENT 'qq互联id',
  `member_qqinfo` text COMMENT 'qq账号相关信息',
  `member_sinaopenid` varchar(100) DEFAULT NULL COMMENT '新浪微博登录id',
  `member_sinainfo` text COMMENT '新浪账号相关信息序列化值',
  `weixin_unionid` varchar(50) DEFAULT NULL COMMENT '微信用户统一标识',
  `weixin_info` varchar(255) DEFAULT NULL COMMENT '微信用户相关信息',
  `member_points` int(11) NOT NULL DEFAULT '0' COMMENT '会员积分',
  `available_predeposit` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '预存款可用金额',
  `freeze_predeposit` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '预存款冻结金额',
  `available_rc_balance` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '可用充值卡余额',
  `freeze_rc_balance` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '冻结充值卡余额',
  `inform_allow` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否允许举报(1可以/2不可以)',
  `is_buy` tinyint(1) NOT NULL DEFAULT '1' COMMENT '会员是否有购买权限 1为开启 0为关闭',
  `is_allowtalk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '会员是否有咨询和发送站内信的权限 1为开启 0为关闭',
  `member_state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '会员的开启状态 1为开启 0为关闭',
  `member_snsvisitnum` int(11) NOT NULL DEFAULT '0' COMMENT 'sns空间访问次数',
  `member_areaid` int(11) DEFAULT NULL COMMENT '地区ID',
  `member_cityid` int(11) DEFAULT NULL COMMENT '城市ID',
  `member_provinceid` int(11) DEFAULT NULL COMMENT '省份ID',
  `member_areainfo` varchar(255) DEFAULT NULL COMMENT '地区内容',
  `member_privacy` text COMMENT '隐私设定',
  `member_exppoints` int(11) NOT NULL DEFAULT '0' COMMENT '会员经验值',
  `invite_one` int(10) DEFAULT '0' COMMENT '一级会员',
  `invite_two` int(10) DEFAULT '0' COMMENT '二级会员',
  `invite_three` int(10) DEFAULT '0' COMMENT '三级会员',
  `inviter_id` int(11) DEFAULT NULL COMMENT '邀请人ID',
  `raw_usr_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`member_id`),
  UNIQUE KEY `member_name` (`member_name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员表';

-- ----------------------------
-- Records of member
-- ----------------------------

-- ----------------------------
-- Table structure for member_common
-- ----------------------------
DROP TABLE IF EXISTS `member_common`;
CREATE TABLE `member_common` (
  `member_id` int(11) NOT NULL COMMENT '会员ID',
  `auth_code` char(6) DEFAULT NULL COMMENT '短信/邮件验证码',
  `send_acode_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '短信/邮件验证码发送时间',
  `send_mb_time` int(11) DEFAULT NULL COMMENT '发送短信验证码时间',
  `send_email_time` int(11) DEFAULT NULL COMMENT '发送邮件验证码时间',
  `send_mb_times` tinyint(4) NOT NULL DEFAULT '0' COMMENT '发送手机验证码次数',
  `send_acode_times` tinyint(4) NOT NULL DEFAULT '0' COMMENT '发送验证码次数',
  `auth_code_check_times` tinyint(4) NOT NULL DEFAULT '0' COMMENT '验证码验证次数[目前wap使用]',
  `auth_modify_pwd_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改密码授权时间[目前wap使用]',
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员扩展表';

-- ----------------------------
-- Records of member_common
-- ----------------------------

-- ----------------------------
-- Table structure for member_goods
-- ----------------------------
DROP TABLE IF EXISTS `member_goods`;
CREATE TABLE `member_goods` (
  `goods_key` varchar(30) DEFAULT NULL COMMENT '自定义商品key标识（待定字段）',
  `goods_id` varchar(30) NOT NULL COMMENT '货号',
  `member_id` int(11) unsigned NOT NULL COMMENT '销售人员ID',
  `store_id` int(11) NOT NULL,
  `goods_type` tinyint(1) unsigned DEFAULT NULL COMMENT '商品类型 1戒托 2裸钻',
  `goods_info` text COMMENT '商品属性信息（序列化存储）',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`goods_id`,`member_id`,`store_id`),
  UNIQUE KEY `goods_id` (`goods_id`,`member_id`,`store_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of member_goods
-- ----------------------------

-- ----------------------------
-- Table structure for member_msg_setting
-- ----------------------------
DROP TABLE IF EXISTS `member_msg_setting`;
CREATE TABLE `member_msg_setting` (
  `mmt_code` varchar(50) NOT NULL COMMENT '用户消息模板编号',
  `member_id` int(10) unsigned NOT NULL COMMENT '会员id',
  `is_receive` tinyint(3) unsigned NOT NULL COMMENT '是否接收 1是，0否',
  PRIMARY KEY (`mmt_code`,`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户消息接收设置表';

-- ----------------------------
-- Records of member_msg_setting
-- ----------------------------

-- ----------------------------
-- Table structure for member_msg_tpl
-- ----------------------------
DROP TABLE IF EXISTS `member_msg_tpl`;
CREATE TABLE `member_msg_tpl` (
  `mmt_code` varchar(50) NOT NULL COMMENT '用户消息模板编号',
  `mmt_name` varchar(50) NOT NULL COMMENT '模板名称',
  `mmt_message_switch` tinyint(3) unsigned NOT NULL COMMENT '站内信接收开关',
  `mmt_message_content` varchar(255) NOT NULL COMMENT '站内信消息内容',
  `mmt_short_switch` tinyint(3) unsigned NOT NULL COMMENT '短信接收开关',
  `mmt_short_content` varchar(255) NOT NULL COMMENT '短信接收内容',
  `mmt_mail_switch` tinyint(3) unsigned NOT NULL COMMENT '邮件接收开关',
  `mmt_mail_subject` varchar(255) NOT NULL COMMENT '邮件标题',
  `mmt_mail_content` text NOT NULL COMMENT '邮件内容',
  PRIMARY KEY (`mmt_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户消息模板';

-- ----------------------------
-- Records of member_msg_tpl
-- ----------------------------

-- ----------------------------
-- Table structure for merchant
-- ----------------------------
DROP TABLE IF EXISTS `merchant`;
CREATE TABLE `merchant` (
  `merchant_id` int(11) NOT NULL,
  `merchat_name` varchar(150) DEFAULT NULL,
  `contactor` varchar(100) DEFAULT NULL COMMENT '负责人',
  `contact_phone` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `contact_email` varchar(50) DEFAULT NULL COMMENT '电子邮件',
  `contact_address` varchar(200) DEFAULT NULL COMMENT '联系地址可以收邮件的',
  `credit_cash` decimal(12,2) DEFAULT NULL COMMENT '信息额度',
  `business_type` tinyint(2) DEFAULT NULL COMMENT '业务类型',
  `created_time` datetime DEFAULT NULL,
  PRIMARY KEY (`merchant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of merchant
-- ----------------------------

-- ----------------------------
-- Table structure for message
-- ----------------------------
DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '短消息索引id',
  `message_parent_id` int(11) NOT NULL COMMENT '回复短消息message_id',
  `from_member_id` int(11) NOT NULL COMMENT '短消息发送人',
  `to_member_id` varchar(1000) NOT NULL COMMENT '短消息接收人',
  `message_title` varchar(50) DEFAULT NULL COMMENT '短消息标题',
  `message_body` varchar(255) NOT NULL COMMENT '短消息内容',
  `message_time` varchar(10) NOT NULL COMMENT '短消息发送时间',
  `message_update_time` varchar(10) DEFAULT NULL COMMENT '短消息回复更新时间',
  `message_open` tinyint(1) NOT NULL DEFAULT '0' COMMENT '短消息打开状态',
  `message_state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '短消息状态，0为正常状态，1为发送人删除状态，2为接收人删除状态',
  `message_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0为私信、1为系统消息、2为留言',
  `read_member_id` varchar(1000) DEFAULT NULL COMMENT '已经读过该消息的会员id',
  `del_member_id` varchar(1000) DEFAULT NULL COMMENT '已经删除该消息的会员id',
  `message_ismore` tinyint(1) NOT NULL DEFAULT '0' COMMENT '站内信是否为一条发给多个用户 0为否 1为多条 ',
  `from_member_name` varchar(100) DEFAULT NULL COMMENT '发信息人用户名',
  `to_member_name` varchar(100) DEFAULT NULL COMMENT '接收人用户名',
  PRIMARY KEY (`message_id`),
  KEY `from_member_id` (`from_member_id`),
  KEY `to_member_id` (`to_member_id`(255)),
  KEY `message_ismore` (`message_ismore`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='短消息';

-- ----------------------------
-- Records of message
-- ----------------------------

-- ----------------------------
-- Table structure for micro_adv
-- ----------------------------
DROP TABLE IF EXISTS `micro_adv`;
CREATE TABLE `micro_adv` (
  `adv_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '广告编号',
  `adv_type` varchar(50) DEFAULT '' COMMENT '广告类型',
  `adv_name` varchar(255) DEFAULT '' COMMENT '广告名称',
  `adv_image` varchar(255) NOT NULL DEFAULT '' COMMENT '广告图片',
  `adv_url` varchar(255) DEFAULT '' COMMENT '广告链接',
  `adv_sort` tinyint(1) unsigned NOT NULL DEFAULT '255' COMMENT '广告排序',
  PRIMARY KEY (`adv_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微商城广告表';

-- ----------------------------
-- Records of micro_adv
-- ----------------------------

-- ----------------------------
-- Table structure for micro_comment
-- ----------------------------
DROP TABLE IF EXISTS `micro_comment`;
CREATE TABLE `micro_comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '评论编号',
  `comment_type` tinyint(1) NOT NULL COMMENT '评论类型编号',
  `comment_object_id` int(10) unsigned NOT NULL COMMENT '推荐商品编号',
  `comment_message` varchar(255) NOT NULL COMMENT '评论内容',
  `comment_member_id` int(10) unsigned NOT NULL COMMENT '评论人编号',
  `comment_time` int(10) unsigned NOT NULL COMMENT '评论时间',
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微商城商品评论表';

-- ----------------------------
-- Records of micro_comment
-- ----------------------------

-- ----------------------------
-- Table structure for micro_goods
-- ----------------------------
DROP TABLE IF EXISTS `micro_goods`;
CREATE TABLE `micro_goods` (
  `commend_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '推荐编号',
  `commend_member_id` int(10) unsigned NOT NULL COMMENT '推荐人用户编号',
  `commend_goods_id` int(10) unsigned NOT NULL COMMENT '推荐商品编号',
  `commend_goods_commonid` int(10) unsigned NOT NULL COMMENT '商品公共表id',
  `commend_goods_store_id` int(10) unsigned NOT NULL COMMENT '推荐商品店铺编号',
  `commend_goods_name` varchar(100) NOT NULL COMMENT '推荐商品名称',
  `commend_goods_price` decimal(11,2) NOT NULL COMMENT '推荐商品价格',
  `commend_goods_image` varchar(100) NOT NULL COMMENT '推荐商品图片',
  `commend_message` varchar(1000) NOT NULL COMMENT '推荐信息',
  `commend_time` int(10) unsigned NOT NULL COMMENT '推荐时间',
  `class_id` int(10) unsigned NOT NULL,
  `like_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '喜欢数',
  `comment_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `click_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点击数',
  `microshop_commend` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '首页推荐 0-否 1-推荐',
  `microshop_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  PRIMARY KEY (`commend_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微商城推荐商品表随心看';

-- ----------------------------
-- Records of micro_goods
-- ----------------------------

-- ----------------------------
-- Table structure for micro_goods_class
-- ----------------------------
DROP TABLE IF EXISTS `micro_goods_class`;
CREATE TABLE `micro_goods_class` (
  `class_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类编号 ',
  `class_name` varchar(50) NOT NULL COMMENT '分类名称',
  `class_parent_id` int(11) unsigned DEFAULT '0' COMMENT '父级分类编号',
  `class_sort` tinyint(4) unsigned NOT NULL COMMENT '排序',
  `class_keyword` varchar(500) DEFAULT '' COMMENT '分类关键字',
  `class_image` varchar(100) DEFAULT '' COMMENT '分类图片',
  `class_commend` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '推荐标志0-不推荐 1-推荐到首页',
  `class_default` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '默认标志，0-非默认 1-默认',
  PRIMARY KEY (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微商城商品随心看分类表';

-- ----------------------------
-- Records of micro_goods_class
-- ----------------------------

-- ----------------------------
-- Table structure for micro_goods_relation
-- ----------------------------
DROP TABLE IF EXISTS `micro_goods_relation`;
CREATE TABLE `micro_goods_relation` (
  `relation_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '关系编号',
  `class_id` int(10) unsigned NOT NULL COMMENT '微商城商品分类编号',
  `shop_class_id` int(10) unsigned NOT NULL COMMENT '商城商品分类编号',
  PRIMARY KEY (`relation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微商城商品分类和商城商品分类对应关系';

-- ----------------------------
-- Records of micro_goods_relation
-- ----------------------------

-- ----------------------------
-- Table structure for micro_like
-- ----------------------------
DROP TABLE IF EXISTS `micro_like`;
CREATE TABLE `micro_like` (
  `like_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '喜欢编号',
  `like_type` tinyint(1) NOT NULL COMMENT '喜欢类型编号',
  `like_object_id` int(10) unsigned NOT NULL COMMENT '喜欢对象编号',
  `like_member_id` int(10) unsigned NOT NULL COMMENT '喜欢人编号',
  `like_time` int(10) unsigned NOT NULL COMMENT '喜欢时间',
  PRIMARY KEY (`like_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微商城喜欢表';

-- ----------------------------
-- Records of micro_like
-- ----------------------------

-- ----------------------------
-- Table structure for micro_member_info
-- ----------------------------
DROP TABLE IF EXISTS `micro_member_info`;
CREATE TABLE `micro_member_info` (
  `member_id` int(11) unsigned NOT NULL COMMENT '用户编号',
  `visit_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '个人中心访问计数',
  `personal_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '已发布个人秀数量',
  `goods_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '已发布随心看数量',
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微商城用户信息表';

-- ----------------------------
-- Records of micro_member_info
-- ----------------------------

-- ----------------------------
-- Table structure for micro_personal
-- ----------------------------
DROP TABLE IF EXISTS `micro_personal`;
CREATE TABLE `micro_personal` (
  `personal_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '推荐编号',
  `commend_member_id` int(10) unsigned NOT NULL COMMENT '推荐人用户编号',
  `commend_image` text NOT NULL COMMENT '推荐图片',
  `commend_buy` text NOT NULL COMMENT '购买信息',
  `commend_message` varchar(1000) NOT NULL COMMENT '推荐信息',
  `commend_time` int(10) unsigned NOT NULL COMMENT '推荐时间',
  `class_id` int(10) unsigned NOT NULL,
  `like_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '喜欢数',
  `comment_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `click_count` int(10) unsigned NOT NULL DEFAULT '0',
  `microshop_commend` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '首页推荐 0-否 1-推荐',
  `microshop_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  PRIMARY KEY (`personal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微商城个人秀表';

-- ----------------------------
-- Records of micro_personal
-- ----------------------------

-- ----------------------------
-- Table structure for micro_personal_class
-- ----------------------------
DROP TABLE IF EXISTS `micro_personal_class`;
CREATE TABLE `micro_personal_class` (
  `class_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类编号 ',
  `class_name` varchar(50) NOT NULL COMMENT '分类名称',
  `class_sort` tinyint(4) unsigned NOT NULL COMMENT '排序',
  `class_image` varchar(100) DEFAULT '' COMMENT '分类图片',
  PRIMARY KEY (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微商城个人秀分类表';

-- ----------------------------
-- Records of micro_personal_class
-- ----------------------------

-- ----------------------------
-- Table structure for micro_store
-- ----------------------------
DROP TABLE IF EXISTS `micro_store`;
CREATE TABLE `micro_store` (
  `microshop_store_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '店铺街店铺编号',
  `shop_store_id` int(11) unsigned NOT NULL COMMENT '商城店铺编号',
  `microshop_sort` tinyint(1) unsigned DEFAULT '255' COMMENT '排序',
  `microshop_commend` tinyint(1) unsigned DEFAULT '1' COMMENT '推荐首页标志 1-正常 2-推荐',
  `like_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '喜欢数',
  `comment_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `click_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`microshop_store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微商城店铺街表';

-- ----------------------------
-- Records of micro_store
-- ----------------------------

-- ----------------------------
-- Table structure for navigation
-- ----------------------------
DROP TABLE IF EXISTS `navigation`;
CREATE TABLE `navigation` (
  `nav_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '索引ID',
  `nav_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '类别，0自定义导航，1商品分类，2文章导航，3活动导航，默认为0',
  `nav_title` varchar(100) DEFAULT NULL COMMENT '导航标题',
  `nav_url` varchar(255) DEFAULT NULL COMMENT '导航链接',
  `nav_location` tinyint(1) NOT NULL DEFAULT '0' COMMENT '导航位置，0头部，1中部，2底部，默认为0',
  `nav_new_open` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否以新窗口打开，0为否，1为是，默认为0',
  `nav_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  `item_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '类别ID，对应着nav_type中的内容，默认为0',
  PRIMARY KEY (`nav_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='页面导航表';

-- ----------------------------
-- Records of navigation
-- ----------------------------

-- ----------------------------
-- Table structure for offpay_area
-- ----------------------------
DROP TABLE IF EXISTS `offpay_area`;
CREATE TABLE `offpay_area` (
  `store_id` int(8) unsigned NOT NULL COMMENT '商家ID',
  `area_id` text COMMENT '县ID组合',
  PRIMARY KEY (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='货到付款支持地区表';

-- ----------------------------
-- Records of offpay_area
-- ----------------------------

-- ----------------------------
-- Table structure for orders
-- ----------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '订单索引id',
  `order_sn` bigint(20) unsigned NOT NULL COMMENT '订单编号',
  `pay_sn` bigint(20) unsigned NOT NULL COMMENT '支付单号',
  `pay_sn1` bigint(20) unsigned DEFAULT NULL COMMENT '预定订单支付订金时的支付单号',
  `store_id` int(11) unsigned NOT NULL COMMENT '卖家店铺id',
  `store_name` varchar(50) DEFAULT NULL COMMENT '卖家店铺名称',
  `buyer_id` int(11) unsigned DEFAULT NULL COMMENT '买家id',
  `buyer_name` varchar(50) NOT NULL COMMENT '买家姓名',
  `buyer_email` varchar(80) DEFAULT NULL COMMENT '买家电子邮箱',
  `buyer_phone` varchar(20) NOT NULL DEFAULT '0' COMMENT '买家手机',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单生成时间',
  `payment_code` char(10) NOT NULL DEFAULT '' COMMENT '支付方式名称代码',
  `payment_time` int(10) unsigned DEFAULT '0' COMMENT '支付(付款)时间',
  `finnshed_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单完成时间',
  `goods_amount` decimal(12,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品总价格',
  `order_amount` decimal(12,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单总价格',
  `rcb_amount` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '充值卡支付金额',
  `refund_amount` decimal(10,2) DEFAULT '0.00' COMMENT '退款金额',
  `breach_amount` decimal(10,2) DEFAULT '0.00' COMMENT '违约金',
  `pd_amount` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '预存款支付金额(废弃)',
  `shipping_fee` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '运费',
  `evaluation_state` tinyint(4) DEFAULT '0' COMMENT '评价状态 0未评价，1已评价，2已过期未评价',
  `evaluation_again_state` tinyint(3) unsigned DEFAULT '0' COMMENT '追加评价状态 0未评价，1已评价，2已过期未评价',
  `order_state` tinyint(4) NOT NULL DEFAULT '5' COMMENT '订单状态：0(已取消)5(待确认)10(待支付)15(生产中);20:已付款;30:已发货;40:已收货;',
  `refund_state` tinyint(4) unsigned DEFAULT '0' COMMENT '退款状态:0是无退款,1是部分退款,2是全部退款',
  `lock_state` tinyint(4) unsigned DEFAULT '0' COMMENT '锁定状态:0是正常,大于0是锁定,默认是0',
  `delete_state` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除状态0未删除1放入回收站2彻底删除',
  `delay_time` int(10) unsigned DEFAULT '0' COMMENT '延迟时间,默认为0',
  `order_from` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1WEB2mobile',
  `shipping_code` varchar(50) DEFAULT '' COMMENT '物流单号',
  `order_type` tinyint(4) DEFAULT '1' COMMENT '订单类型1普通订单(默认),2预定订单,3门店自提订单',
  `api_pay_time` int(10) unsigned DEFAULT '0' COMMENT '在线支付动作时间,只要向第三方支付平台提交就会更新',
  `chain_id` int(10) unsigned DEFAULT '0' COMMENT '自提门店ID',
  `chain_code` mediumint(6) unsigned DEFAULT '0' COMMENT '门店提货码',
  `rpt_amount` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '红包值',
  `trade_no` varchar(50) DEFAULT NULL COMMENT '外部交易订单号',
  `weixiu_state` smallint(5) DEFAULT '0' COMMENT '-维修状态',
  `is_xianhuo` tinyint(1) DEFAULT NULL COMMENT '-是否现货订单',
  `remark` varchar(500) DEFAULT NULL COMMENT '-订单备注',
  `bespoke_id` int(10) DEFAULT '0' COMMENT '-预约单号',
  `seller_id` int(10) DEFAULT NULL COMMENT '-销售顾问ID',
  `seller_name` varchar(16) DEFAULT NULL COMMENT '-销售顾问名称',
  `audit_by` varchar(20) DEFAULT NULL COMMENT '-审核人',
  `audit_time` timestamp NULL DEFAULT NULL COMMENT '-审核时间',
  `customer_source_id` int(11) DEFAULT NULL COMMENT '顾客来源',
  `pay_status` tinyint(4) DEFAULT '1' COMMENT '支付状态 1：待付款 2：部分付款 3已付款',
  `is_zp` smallint(1) DEFAULT '0' COMMENT '是否赠品单',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`order_id`),
  KEY `order_sn` (`order_sn`),
  KEY `buyer_id` (`buyer_id`) USING BTREE,
  KEY `store_id` (`store_id`,`seller_id`) USING BTREE,
  KEY `order_state` (`order_state`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单表';

-- ----------------------------
-- Records of orders
-- ----------------------------

-- ----------------------------
-- Table structure for order_bill
-- ----------------------------
DROP TABLE IF EXISTS `order_bill`;
CREATE TABLE `order_bill` (
  `ob_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID作为新结算单编号',
  `ob_no` int(11) DEFAULT '0' COMMENT '结算单编号(年月店铺ID)',
  `ob_start_date` int(11) NOT NULL COMMENT '开始日期',
  `ob_end_date` int(11) NOT NULL COMMENT '结束日期',
  `ob_order_totals` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `ob_shipping_totals` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '运费',
  `ob_order_return_totals` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '退单金额',
  `ob_commis_totals` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '佣金金额',
  `ob_commis_return_totals` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '退还佣金',
  `ob_store_cost_totals` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '店铺促销活动费用',
  `ob_result_totals` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '应结金额',
  `ob_create_date` int(11) DEFAULT '0' COMMENT '生成结算单日期',
  `os_month` mediumint(6) unsigned DEFAULT NULL COMMENT '出账单应结时间,ob_end_date+1所在月(年月份)',
  `ob_state` enum('1','2','3','4') DEFAULT '1' COMMENT '1默认2店家已确认3平台已审核4结算完成',
  `ob_pay_date` int(11) DEFAULT '0' COMMENT '付款日期',
  `ob_pay_content` varchar(200) DEFAULT '' COMMENT '支付备注',
  `ob_store_id` int(11) NOT NULL COMMENT '店铺ID',
  `ob_store_name` varchar(50) DEFAULT NULL COMMENT '店铺名',
  `ob_order_book_totals` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '被关闭的预定订单的实收总金额',
  `ob_rpt_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '下单时使用的红包值',
  `ob_rf_rpt_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '全部退款时红包值',
  PRIMARY KEY (`ob_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='结算表';

-- ----------------------------
-- Records of order_bill
-- ----------------------------

-- ----------------------------
-- Table structure for order_book
-- ----------------------------
DROP TABLE IF EXISTS `order_book`;
CREATE TABLE `order_book` (
  `book_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `book_order_id` int(11) DEFAULT NULL COMMENT '订单ID',
  `book_step` tinyint(4) DEFAULT NULL COMMENT '预定时段,值为1 or 2,0为不分时段，全款支付',
  `book_amount` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '定金or尾款金额',
  `book_pd_amount` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '预存款支付金额',
  `book_rcb_amount` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '充值卡支付金额',
  `book_pay_name` varchar(10) DEFAULT NULL COMMENT '支付方式(文字)',
  `book_trade_no` varchar(40) DEFAULT NULL COMMENT '第三方平台交易号',
  `book_pay_time` int(11) DEFAULT '0' COMMENT '支付时间',
  `book_end_time` int(11) DEFAULT '0' COMMENT '时段1:订单自动取消时间,时段2:时段结束时间',
  `book_buyer_phone` bigint(20) DEFAULT NULL COMMENT '买家接收尾款交款通知的手机,只在第2时段有值即可',
  `book_deposit_amount` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '定金金额,只在全款支付时有值即可',
  `book_pay_notice` tinyint(4) DEFAULT '0' COMMENT '0未通知1已通知,该字段只对尾款时段有效',
  `book_real_pay` decimal(10,2) DEFAULT '0.00' COMMENT '订单被取消后最终支付金额（平台收款金额）',
  `book_cancel_time` int(11) DEFAULT '0' COMMENT '订单被取消时间,结算用,只有book_step是0或1时有值',
  `book_store_id` int(11) DEFAULT '0' COMMENT '商家 ID,只有book_step是0或1时有值即可',
  PRIMARY KEY (`book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='预定订单时段详细内容表';

-- ----------------------------
-- Records of order_book
-- ----------------------------

-- ----------------------------
-- Table structure for order_common
-- ----------------------------
DROP TABLE IF EXISTS `order_common`;
CREATE TABLE `order_common` (
  `order_id` int(11) NOT NULL COMMENT '订单索引id',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺ID',
  `shipping_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '配送时间',
  `shipping_express_id` tinyint(1) NOT NULL DEFAULT '0' COMMENT '配送公司ID',
  `evaluation_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评价时间',
  `evalseller_time` int(10) unsigned DEFAULT NULL COMMENT '卖家评价买家的时间',
  `order_message` varchar(300) DEFAULT NULL COMMENT '订单留言',
  `order_pointscount` int(11) NOT NULL DEFAULT '0' COMMENT '订单赠送积分',
  `voucher_price` int(11) DEFAULT NULL COMMENT '代金券面额',
  `voucher_code` varchar(32) DEFAULT NULL COMMENT '代金券编码',
  `deliver_explain` text COMMENT '发货备注',
  `daddress_id` mediumint(9) NOT NULL DEFAULT '0' COMMENT '发货地址ID',
  `reciver_name` varchar(50) NOT NULL COMMENT '收货人姓名',
  `reciver_info` varchar(500) NOT NULL COMMENT '收货人其它信息',
  `reciver_province_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '收货人省级ID',
  `reciver_city_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '收货人市级ID',
  `invoice_info` varchar(500) DEFAULT '' COMMENT '发票信息',
  `promotion_info` varchar(800) DEFAULT '' COMMENT '促销信息备注',
  `dlyo_pickup_code` varchar(6) DEFAULT NULL COMMENT '提货码',
  `promotion_total` decimal(10,2) DEFAULT '0.00' COMMENT '订单总优惠金额（代金券，满减，平台红包）',
  `discount` tinyint(4) DEFAULT '0' COMMENT '会员折扣x%',
  `distribution_type` smallint(2) DEFAULT NULL COMMENT '发货方式, 1: 物流发货, 2: 上门取货',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单信息扩展表';

-- ----------------------------
-- Records of order_common
-- ----------------------------

-- ----------------------------
-- Table structure for order_goods
-- ----------------------------
DROP TABLE IF EXISTS `order_goods`;
CREATE TABLE `order_goods` (
  `rec_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '订单商品表索引id',
  `order_id` int(11) NOT NULL COMMENT '订单id',
  `goods_id` varchar(30) DEFAULT NULL COMMENT '商品id /sku /起版号 /成品定制码',
  `goods_name` varchar(100) NOT NULL COMMENT '商品名称',
  `goods_price` decimal(10,2) NOT NULL COMMENT '商品价格',
  `goods_num` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '商品数量',
  `goods_image` varchar(200) DEFAULT NULL COMMENT '商品图片',
  `goods_pay_price` decimal(10,2) unsigned NOT NULL COMMENT '商品实际成交价',
  `discount_code` varchar(30) DEFAULT NULL COMMENT '折扣码',
  `store_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺ID',
  `buyer_id` int(10) unsigned DEFAULT '0' COMMENT '买家ID',
  `goods_type` char(1) NOT NULL DEFAULT '1' COMMENT '1默认2抢购商品3限时折扣商品4组合套装5赠品8加价购活动商品9加价购换购商品',
  `promotions_id` mediumint(8) unsigned DEFAULT '0' COMMENT '促销活动ID（抢购ID/限时折扣ID/优惠套装ID）与goods_type搭配使用',
  `commis_rate` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '佣金比例',
  `gc_id` mediumint(8) unsigned DEFAULT '0' COMMENT '商品最底级分类ID',
  `style_sn` varchar(16) DEFAULT NULL COMMENT '款号',
  `goods_itemid` varchar(25) DEFAULT NULL COMMENT '货品实物id',
  `carat` decimal(6,3) DEFAULT NULL COMMENT '石重',
  `clarity` varchar(10) DEFAULT NULL COMMENT '净度',
  `color` varchar(16) DEFAULT NULL COMMENT '颜色',
  `cut` varchar(16) DEFAULT NULL COMMENT '切工',
  `caizhi` varchar(12) DEFAULT NULL COMMENT '材质',
  `jinse` varchar(10) DEFAULT NULL COMMENT '材质颜色',
  `jinzhong` decimal(6,3) DEFAULT NULL COMMENT '金重',
  `zhiquan` decimal(6,1) DEFAULT NULL COMMENT '指圈',
  `xiangkou` decimal(6,3) DEFAULT NULL COMMENT '镶口',
  `face_work` varchar(32) DEFAULT NULL COMMENT '表面工艺,1:光面, 2:磨砂, 3:拉砂 ,4:光面&磨砂, 5:光面&拉砂, 6:其它, 7:特殊, 8:CNC工艺,9:勾丝',
  `kezi` varchar(32) DEFAULT NULL,
  `xiangqian` varchar(32) DEFAULT NULL COMMENT '镶嵌方式,1:工工厂配钻，工厂镶嵌, 2:不需工厂镶嵌, 3:需工厂镶嵌, 4:客户先看钻再返厂镶嵌, 5:镶嵌4C裸钻, 6:镶嵌4C裸钻，客户先看钻,7:成品, 8:半成品',
  `goods_spec` varchar(255) DEFAULT NULL COMMENT '商品规格',
  `goods_contractid` varchar(100) DEFAULT NULL COMMENT '商品开启的消费者保障服务id',
  `invite_rates` smallint(5) DEFAULT '0' COMMENT '分销佣金',
  `is_xianhuo` tinyint(1) DEFAULT '1' COMMENT '是否现货',
  `bc_status` smallint(5) DEFAULT '0' COMMENT '生产状态',
  `bc_id` varchar(12) DEFAULT NULL COMMENT '生产单号',
  `is_finance` tinyint(2) DEFAULT '1' COMMENT '是否需要销账, 1: 销账，0：不需要',
  `is_qiban` tinyint(2) DEFAULT '0' COMMENT '是否起版',
  `is_cpdz` tinyint(2) DEFAULT '0' COMMENT '是否成品定制',
  `cpdz_code` varchar(20) DEFAULT NULL COMMENT '成品定制码',
  `weixiu_status` tinyint(2) DEFAULT NULL COMMENT '维修状态',
  `is_return` tinyint(2) DEFAULT '0' COMMENT '是否退货',
  `refund_amount` decimal(10,2) DEFAULT '0.00' COMMENT '退款金额',
  `peishi_type` tinyint(2) DEFAULT '0' COMMENT '是否支持4C配钻，0不支持，1裸钻支持，2空托支持',
  `tuo_type` tinyint(2) DEFAULT NULL COMMENT '托类型， 1： 成品； 2：空托',
  `zhushi_num` int(1) DEFAULT NULL COMMENT '主石粒数',
  `cert_type` varchar(10) DEFAULT NULL COMMENT '证书类型',
  `cert_id` varchar(32) DEFAULT NULL COMMENT '证书号',
  `dia_is_xianhuo` tinyint(4) DEFAULT NULL COMMENT '裸石是现货＝1， 否则＝0',
  `xianhuo_adds` varchar(15) DEFAULT NULL COMMENT '现货增值项',
  `from_type` tinyint(1) DEFAULT NULL COMMENT '商品数据来源：1虚拟货号，2裸钻列表 3起版 4现货 5赠品 6总部现货',
  `breach_amount` decimal(8,2) DEFAULT NULL COMMENT '退款违约金',
  `old_detail_id` int(10) DEFAULT NULL,
  `is_exchange` tinyint(1) DEFAULT '0' COMMENT '1:换过货',
  PRIMARY KEY (`rec_id`),
  KEY `order_id` (`order_id`),
  KEY `old_detail_id` (`old_detail_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单商品表';

-- ----------------------------
-- Records of order_goods
-- ----------------------------

-- ----------------------------
-- Table structure for order_log
-- ----------------------------
DROP TABLE IF EXISTS `order_log`;
CREATE TABLE `order_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_id` int(11) NOT NULL COMMENT '订单id',
  `log_msg` varchar(1500) DEFAULT '' COMMENT '文字描述',
  `log_time` int(10) unsigned NOT NULL COMMENT '处理时间',
  `log_role` varchar(10) NOT NULL COMMENT '操作角色',
  `log_user` varchar(30) DEFAULT '' COMMENT '操作人',
  `log_orderstate` tinyint(4) DEFAULT NULL COMMENT '订单状态：0(已取消)5(待确认)10(待支付)15(生产中);20:已付款;30:已发货;40:已收货;',
  PRIMARY KEY (`log_id`),
  KEY `order_id` (`order_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单处理历史表';

-- ----------------------------
-- Records of order_log
-- ----------------------------

-- ----------------------------
-- Table structure for order_pay
-- ----------------------------
DROP TABLE IF EXISTS `order_pay`;
CREATE TABLE `order_pay` (
  `pay_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pay_sn` bigint(20) unsigned NOT NULL COMMENT '支付单号',
  `buyer_id` int(10) unsigned NOT NULL COMMENT '买家ID',
  `api_pay_state` enum('0','1') DEFAULT '0' COMMENT '0默认未支付1已支付(只有第三方支付接口通知到时才会更改此状态)',
  PRIMARY KEY (`pay_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单支付表';

-- ----------------------------
-- Records of order_pay
-- ----------------------------

-- ----------------------------
-- Table structure for order_pay_action
-- ----------------------------
DROP TABLE IF EXISTS `order_pay_action`;
CREATE TABLE `order_pay_action` (
  `pay_id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `order_id` mediumint(8) NOT NULL COMMENT '订单Id',
  `order_sn` varchar(20) NOT NULL COMMENT '订单号',
  `order_amount` decimal(10,2) NOT NULL COMMENT '订单总金额',
  `deposit` decimal(10,2) NOT NULL COMMENT '支付金额',
  `balance` decimal(10,2) NOT NULL COMMENT '剩余金额',
  `pay_date` datetime NOT NULL COMMENT '支付时间',
  `pay_code` varchar(20) DEFAULT NULL COMMENT '支付方式编码',
  `pay_type` varchar(100) DEFAULT NULL COMMENT '支付方式',
  `pay_account` varchar(100) DEFAULT NULL COMMENT '银行卡账户',
  `pay_sn` varchar(100) DEFAULT NULL COMMENT '支付流水号',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '1=未审核,2=有效,3=无效',
  `create_date` varchar(30) DEFAULT NULL COMMENT '创建时间',
  `remark` varchar(200) DEFAULT NULL COMMENT '备注',
  `created_name` varchar(50) DEFAULT NULL COMMENT '操作人',
  PRIMARY KEY (`pay_id`),
  KEY `order_sn` (`order_sn`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of order_pay_action
-- ----------------------------

-- ----------------------------
-- Table structure for order_snapshot
-- ----------------------------
DROP TABLE IF EXISTS `order_snapshot`;
CREATE TABLE `order_snapshot` (
  `rec_id` int(11) NOT NULL COMMENT '主键',
  `goods_id` int(11) NOT NULL COMMENT '商品ID',
  `create_time` int(11) NOT NULL COMMENT '生成时间',
  `goods_attr` text COMMENT '属性',
  `goods_body` text COMMENT '详情',
  `plate_top` text COMMENT '顶部关联版式',
  `plate_bottom` text COMMENT '底部关联版式',
  PRIMARY KEY (`rec_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单快照表';

-- ----------------------------
-- Records of order_snapshot
-- ----------------------------

-- ----------------------------
-- Table structure for order_statis
-- ----------------------------
DROP TABLE IF EXISTS `order_statis`;
CREATE TABLE `order_statis` (
  `os_month` mediumint(9) unsigned NOT NULL DEFAULT '0' COMMENT '统计编号(年月)',
  `os_year` smallint(6) DEFAULT '0' COMMENT '年',
  `os_start_date` int(11) NOT NULL COMMENT '开始日期',
  `os_end_date` int(11) NOT NULL COMMENT '结束日期',
  `os_order_totals` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `os_shipping_totals` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '运费',
  `os_order_return_totals` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '退单金额',
  `os_commis_totals` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '佣金金额',
  `os_commis_return_totals` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '退还佣金',
  `os_store_cost_totals` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '店铺促销活动费用',
  `os_result_totals` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '本期应结',
  `os_create_date` int(11) DEFAULT NULL COMMENT '创建记录日期',
  `os_order_book_totals` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '被关闭的预定订单的实收总金额',
  PRIMARY KEY (`os_month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='月销量统计表';

-- ----------------------------
-- Records of order_statis
-- ----------------------------

-- ----------------------------
-- Table structure for order_sync
-- ----------------------------
DROP TABLE IF EXISTS `order_sync`;
CREATE TABLE `order_sync` (
  `sync_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `init_sync_order` datetime DEFAULT NULL COMMENT '第一次同步订单到后端系统的时间',
  `latest_sync_order` datetime DEFAULT NULL COMMENT '最近一次推订单到后端系统的时间',
  `bc_date` datetime DEFAULT NULL COMMENT '订单布产时间',
  `latest_pull_order` datetime DEFAULT NULL COMMENT '最近一次拉取后端订单生产进度的时间',
  `sync_stop` smallint(1) DEFAULT '0' COMMENT '是否停止自动往后端系统同步',
  `pull_stop` smallint(1) DEFAULT '0' COMMENT '是否停止自动拉取信息',
  PRIMARY KEY (`sync_id`),
  UNIQUE KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of order_sync
-- ----------------------------

-- ----------------------------
-- Table structure for payment
-- ----------------------------
DROP TABLE IF EXISTS `payment`;
CREATE TABLE `payment` (
  `payment_id` int(1) unsigned NOT NULL COMMENT '支付索引id',
  `payment_code` char(11) NOT NULL COMMENT '支付代码名称',
  `payment_name` char(10) NOT NULL COMMENT '支付名称',
  `payment_config` text COMMENT '支付接口配置信息',
  `payment_state` enum('0','1') NOT NULL DEFAULT '0' COMMENT '接口状态0禁用1启用',
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付方式表';

-- ----------------------------
-- Records of payment
-- ----------------------------

-- ----------------------------
-- Table structure for pd_cash
-- ----------------------------
DROP TABLE IF EXISTS `pd_cash`;
CREATE TABLE `pd_cash` (
  `pdc_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增编号',
  `pdc_sn` bigint(20) NOT NULL COMMENT '记录唯一标示',
  `pdc_member_id` int(11) NOT NULL COMMENT '会员编号',
  `pdc_member_name` varchar(50) NOT NULL COMMENT '会员名称',
  `pdc_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `pdc_bank_name` varchar(40) NOT NULL COMMENT '收款银行',
  `pdc_bank_no` varchar(30) DEFAULT NULL COMMENT '收款账号',
  `pdc_bank_user` varchar(10) DEFAULT NULL COMMENT '开户人姓名',
  `pdc_add_time` int(11) NOT NULL COMMENT '添加时间',
  `pdc_payment_time` int(11) DEFAULT NULL COMMENT '付款时间',
  `pdc_payment_state` enum('0','1') NOT NULL DEFAULT '0' COMMENT '提现支付状态 0默认1支付完成',
  `pdc_payment_admin` varchar(30) DEFAULT NULL COMMENT '支付管理员',
  PRIMARY KEY (`pdc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='预存款提现记录表';

-- ----------------------------
-- Records of pd_cash
-- ----------------------------

-- ----------------------------
-- Table structure for pd_log
-- ----------------------------
DROP TABLE IF EXISTS `pd_log`;
CREATE TABLE `pd_log` (
  `lg_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增编号',
  `lg_member_id` int(11) NOT NULL COMMENT '会员编号',
  `lg_member_name` varchar(50) NOT NULL COMMENT '会员名称',
  `lg_admin_name` varchar(50) DEFAULT NULL COMMENT '管理员名称',
  `lg_type` varchar(15) NOT NULL DEFAULT '' COMMENT 'order_pay下单支付预存款,order_freeze下单冻结预存款,order_cancel取消订单解冻预存款,order_comb_pay下单支付被冻结的预存款,recharge充值,cash_apply申请提现冻结预存款,cash_pay提现成功,cash_del取消提现申请，解冻预存款,refund退款',
  `lg_av_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '可用金额变更0表示未变更',
  `lg_freeze_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '冻结金额变更0表示未变更',
  `lg_add_time` int(11) NOT NULL COMMENT '添加时间',
  `lg_desc` varchar(150) DEFAULT NULL COMMENT '描述',
  `lg_invite_member_id` int(11) DEFAULT '0' COMMENT '原始会员编号',
  PRIMARY KEY (`lg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='预存款变更日志表';

-- ----------------------------
-- Records of pd_log
-- ----------------------------

-- ----------------------------
-- Table structure for pd_recharge
-- ----------------------------
DROP TABLE IF EXISTS `pd_recharge`;
CREATE TABLE `pd_recharge` (
  `pdr_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增编号',
  `pdr_sn` bigint(20) unsigned NOT NULL COMMENT '记录唯一标示',
  `pdr_member_id` int(11) NOT NULL COMMENT '会员编号',
  `pdr_member_name` varchar(50) NOT NULL COMMENT '会员名称',
  `pdr_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '充值金额',
  `pdr_payment_code` varchar(20) DEFAULT '' COMMENT '支付方式',
  `pdr_payment_name` varchar(15) DEFAULT '' COMMENT '支付方式',
  `pdr_trade_sn` varchar(50) DEFAULT '' COMMENT '第三方支付接口交易号',
  `pdr_add_time` int(11) NOT NULL COMMENT '添加时间',
  `pdr_payment_state` enum('0','1') NOT NULL DEFAULT '0' COMMENT '支付状态 0未支付1支付',
  `pdr_payment_time` int(11) NOT NULL DEFAULT '0' COMMENT '支付时间',
  `pdr_admin` varchar(30) DEFAULT '' COMMENT '管理员名',
  PRIMARY KEY (`pdr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='预存款充值表';

-- ----------------------------
-- Records of pd_recharge
-- ----------------------------

-- ----------------------------
-- Table structure for points_cart
-- ----------------------------
DROP TABLE IF EXISTS `points_cart`;
CREATE TABLE `points_cart` (
  `pcart_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `pmember_id` int(11) NOT NULL COMMENT '会员编号',
  `pgoods_id` int(11) NOT NULL COMMENT '积分礼品序号',
  `pgoods_name` varchar(100) NOT NULL COMMENT '积分礼品名称',
  `pgoods_points` int(11) NOT NULL COMMENT '积分礼品兑换积分',
  `pgoods_choosenum` int(11) NOT NULL COMMENT '选择积分礼品数量',
  `pgoods_image` varchar(100) DEFAULT NULL COMMENT '积分礼品图片',
  PRIMARY KEY (`pcart_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='积分礼品兑换购物车';

-- ----------------------------
-- Records of points_cart
-- ----------------------------

-- ----------------------------
-- Table structure for points_goods
-- ----------------------------
DROP TABLE IF EXISTS `points_goods`;
CREATE TABLE `points_goods` (
  `pgoods_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '积分礼品索引id',
  `pgoods_name` varchar(100) NOT NULL COMMENT '积分礼品名称',
  `pgoods_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '积分礼品原价',
  `pgoods_points` int(11) NOT NULL COMMENT '积分礼品兑换所需积分',
  `pgoods_image` varchar(100) DEFAULT '' COMMENT '积分礼品默认封面图片',
  `pgoods_tag` varchar(100) DEFAULT '' COMMENT '积分礼品标签',
  `pgoods_serial` varchar(50) NOT NULL COMMENT '积分礼品货号',
  `pgoods_storage` int(11) NOT NULL DEFAULT '0' COMMENT '积分礼品库存数',
  `pgoods_show` tinyint(1) NOT NULL COMMENT '积分礼品上架 0表示下架 1表示上架',
  `pgoods_commend` tinyint(1) NOT NULL COMMENT '积分礼品推荐',
  `pgoods_add_time` int(11) NOT NULL COMMENT '积分礼品添加时间',
  `pgoods_keywords` varchar(100) DEFAULT NULL COMMENT '积分礼品关键字',
  `pgoods_description` varchar(200) DEFAULT NULL COMMENT '积分礼品描述',
  `pgoods_body` text NOT NULL COMMENT '积分礼品详细内容',
  `pgoods_state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '积分礼品状态，0开启，1禁售',
  `pgoods_close_reason` varchar(255) DEFAULT NULL COMMENT '积分礼品禁售原因',
  `pgoods_salenum` int(11) NOT NULL DEFAULT '0' COMMENT '积分礼品售出数量',
  `pgoods_view` int(11) NOT NULL DEFAULT '0' COMMENT '积分商品浏览次数',
  `pgoods_islimit` tinyint(1) NOT NULL COMMENT '是否限制每会员兑换数量',
  `pgoods_limitnum` int(11) DEFAULT NULL COMMENT '每会员限制兑换数量',
  `pgoods_islimittime` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否限制兑换时间 0为不限制 1为限制',
  `pgoods_limitmgrade` int(11) NOT NULL DEFAULT '0' COMMENT '限制参与兑换的会员级别',
  `pgoods_starttime` int(11) DEFAULT NULL COMMENT '兑换开始时间',
  `pgoods_endtime` int(11) DEFAULT NULL COMMENT '兑换结束时间',
  `pgoods_sort` int(11) NOT NULL DEFAULT '0' COMMENT '礼品排序',
  PRIMARY KEY (`pgoods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='积分礼品表';

-- ----------------------------
-- Records of points_goods
-- ----------------------------

-- ----------------------------
-- Table structure for points_log
-- ----------------------------
DROP TABLE IF EXISTS `points_log`;
CREATE TABLE `points_log` (
  `pl_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '积分日志编号',
  `pl_memberid` int(11) NOT NULL COMMENT '会员编号',
  `pl_membername` varchar(100) NOT NULL COMMENT '会员名称',
  `pl_adminid` int(11) DEFAULT NULL COMMENT '管理员编号',
  `pl_adminname` varchar(100) DEFAULT NULL COMMENT '管理员名称',
  `pl_points` int(11) NOT NULL DEFAULT '0' COMMENT '积分数负数表示扣除',
  `pl_addtime` int(11) NOT NULL COMMENT '添加时间',
  `pl_desc` varchar(100) NOT NULL COMMENT '操作描述',
  `pl_stage` varchar(50) NOT NULL COMMENT '操作阶段',
  PRIMARY KEY (`pl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员积分日志表';

-- ----------------------------
-- Records of points_log
-- ----------------------------

-- ----------------------------
-- Table structure for points_order
-- ----------------------------
DROP TABLE IF EXISTS `points_order`;
CREATE TABLE `points_order` (
  `point_orderid` int(11) NOT NULL AUTO_INCREMENT COMMENT '兑换订单编号',
  `point_ordersn` varchar(20) NOT NULL COMMENT '兑换订单编号',
  `point_buyerid` int(11) NOT NULL COMMENT '兑换会员id',
  `point_buyername` varchar(50) NOT NULL COMMENT '兑换会员姓名',
  `point_buyeremail` varchar(100) NOT NULL COMMENT '兑换会员email',
  `point_addtime` int(11) NOT NULL COMMENT '兑换订单生成时间',
  `point_shippingtime` int(11) DEFAULT NULL COMMENT '配送时间',
  `point_shippingcode` varchar(50) DEFAULT NULL COMMENT '物流单号',
  `point_shipping_ecode` varchar(30) DEFAULT NULL COMMENT '物流公司编码',
  `point_finnshedtime` int(11) DEFAULT NULL COMMENT '订单完成时间',
  `point_allpoint` int(11) NOT NULL DEFAULT '0' COMMENT '兑换总积分',
  `point_ordermessage` varchar(300) DEFAULT NULL COMMENT '订单留言',
  `point_orderstate` int(11) NOT NULL DEFAULT '20' COMMENT '订单状态：20(默认):已兑换并扣除积分;30:已发货;40:已收货;50已完成;2已取消',
  PRIMARY KEY (`point_orderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='兑换订单表';

-- ----------------------------
-- Records of points_order
-- ----------------------------

-- ----------------------------
-- Table structure for points_orderaddress
-- ----------------------------
DROP TABLE IF EXISTS `points_orderaddress`;
CREATE TABLE `points_orderaddress` (
  `point_oaid` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `point_orderid` int(11) NOT NULL COMMENT '订单id',
  `point_truename` varchar(50) NOT NULL COMMENT '收货人姓名',
  `point_areaid` int(11) NOT NULL COMMENT '地区id',
  `point_areainfo` varchar(100) NOT NULL COMMENT '地区内容',
  `point_address` varchar(200) NOT NULL COMMENT '详细地址',
  `point_telphone` varchar(20) DEFAULT '' COMMENT '电话号码',
  `point_mobphone` varchar(20) DEFAULT '' COMMENT '手机号码',
  PRIMARY KEY (`point_oaid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='兑换订单地址表';

-- ----------------------------
-- Records of points_orderaddress
-- ----------------------------

-- ----------------------------
-- Table structure for points_ordergoods
-- ----------------------------
DROP TABLE IF EXISTS `points_ordergoods`;
CREATE TABLE `points_ordergoods` (
  `point_recid` int(11) NOT NULL AUTO_INCREMENT COMMENT '订单礼品表索引',
  `point_orderid` int(11) NOT NULL COMMENT '订单id',
  `point_goodsid` int(11) NOT NULL COMMENT '礼品id',
  `point_goodsname` varchar(100) NOT NULL COMMENT '礼品名称',
  `point_goodspoints` int(11) NOT NULL COMMENT '礼品兑换积分',
  `point_goodsnum` int(11) NOT NULL COMMENT '礼品数量',
  `point_goodsimage` varchar(100) DEFAULT NULL COMMENT '礼品图片',
  PRIMARY KEY (`point_recid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='兑换订单商品表';

-- ----------------------------
-- Records of points_ordergoods
-- ----------------------------

-- ----------------------------
-- Table structure for p_bill_imp
-- ----------------------------
DROP TABLE IF EXISTS `p_bill_imp`;
CREATE TABLE `p_bill_imp` (
  `bill_no` char(17) NOT NULL COMMENT '单据编号',
  `bill_status` tinyint(4) DEFAULT '1' COMMENT '数据字典：仓储单据状态（warehouse_in_status）/ 盘点单状态（warehouse.pandian_plan）',
  `create_user` varchar(25) NOT NULL COMMENT '制单人',
  `create_time` datetime NOT NULL COMMENT '制单时间',
  `check_user` varchar(25) DEFAULT NULL COMMENT '审核人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `sign_user` varchar(255) DEFAULT NULL COMMENT '签收人',
  `sign_time` datetime DEFAULT NULL COMMENT '签收日期',
  `goods_id` bigint(30) NOT NULL DEFAULT '0' COMMENT '货号',
  `out_warehouse_type` tinyint(1) DEFAULT NULL COMMENT 'æ•°æ®å­—å…¸warehouse.out_warehouse_type',
  `p_type` varchar(100) DEFAULT NULL COMMENT 'p单类别',
  `to_customer_id` int(10) NOT NULL DEFAULT '0' COMMENT '配送公司id',
  `to_company_id` int(8) DEFAULT NULL COMMENT '入货公司ID',
  `from_company_id` int(10) DEFAULT NULL COMMENT '出货公司id',
  `chengbenjia` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pifajia` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实际价格',
  `management_fee` decimal(8,3) DEFAULT '0.000' COMMENT 'ç®¡ç†è´¹',
  `jiejia` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否结价',
  `fin_check_status` tinyint(4) DEFAULT '1' COMMENT '财务审核状态:见数据字典',
  `goods_data` varchar(155) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of p_bill_imp
-- ----------------------------

-- ----------------------------
-- Table structure for p_book_quota
-- ----------------------------
DROP TABLE IF EXISTS `p_book_quota`;
CREATE TABLE `p_book_quota` (
  `bkq_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '预定套餐id',
  `store_id` int(11) NOT NULL COMMENT '店铺id',
  `store_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `bkq_starttime` int(11) NOT NULL COMMENT '套餐开始时间',
  `bkq_endtime` int(11) NOT NULL COMMENT '套餐结束时间',
  PRIMARY KEY (`bkq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='预定商品套餐表';

-- ----------------------------
-- Records of p_book_quota
-- ----------------------------

-- ----------------------------
-- Table structure for p_booth_goods
-- ----------------------------
DROP TABLE IF EXISTS `p_booth_goods`;
CREATE TABLE `p_booth_goods` (
  `booth_goods_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '套餐商品id',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `gc_id` int(10) unsigned NOT NULL COMMENT '商品分类id',
  `booth_state` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '套餐状态 1开启 0关闭 默认1',
  PRIMARY KEY (`booth_goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='展位商品表';

-- ----------------------------
-- Records of p_booth_goods
-- ----------------------------

-- ----------------------------
-- Table structure for p_booth_quota
-- ----------------------------
DROP TABLE IF EXISTS `p_booth_quota`;
CREATE TABLE `p_booth_quota` (
  `booth_quota_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '套餐id',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `store_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `booth_quota_starttime` int(10) unsigned NOT NULL COMMENT '开始时间',
  `booth_quota_endtime` int(10) unsigned NOT NULL COMMENT '结束时间',
  `booth_state` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '套餐状态 1开启 0关闭 默认1',
  PRIMARY KEY (`booth_quota_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='展位套餐表';

-- ----------------------------
-- Records of p_booth_quota
-- ----------------------------

-- ----------------------------
-- Table structure for p_bundling
-- ----------------------------
DROP TABLE IF EXISTS `p_bundling`;
CREATE TABLE `p_bundling` (
  `bl_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '组合ID',
  `bl_name` varchar(50) NOT NULL COMMENT '组合名称',
  `store_id` int(11) NOT NULL COMMENT '店铺名称',
  `store_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `bl_discount_price` decimal(10,2) NOT NULL COMMENT '组合价格',
  `bl_freight_choose` tinyint(1) NOT NULL COMMENT '运费承担方式',
  `bl_freight` decimal(10,2) DEFAULT '0.00' COMMENT '运费',
  `bl_state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '组合状态 0-关闭/1-开启',
  PRIMARY KEY (`bl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='组合销售活动表';

-- ----------------------------
-- Records of p_bundling
-- ----------------------------

-- ----------------------------
-- Table structure for p_bundling_goods
-- ----------------------------
DROP TABLE IF EXISTS `p_bundling_goods`;
CREATE TABLE `p_bundling_goods` (
  `bl_goods_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '组合商品id',
  `bl_id` int(11) NOT NULL COMMENT '组合id',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `goods_name` varchar(50) NOT NULL COMMENT '商品名称',
  `goods_image` varchar(100) NOT NULL COMMENT '商品图片',
  `bl_goods_price` decimal(10,2) NOT NULL COMMENT '商品价格',
  `bl_appoint` tinyint(3) unsigned NOT NULL COMMENT '指定商品 1是，0否',
  PRIMARY KEY (`bl_goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='组合销售活动商品表';

-- ----------------------------
-- Records of p_bundling_goods
-- ----------------------------

-- ----------------------------
-- Table structure for p_bundling_quota
-- ----------------------------
DROP TABLE IF EXISTS `p_bundling_quota`;
CREATE TABLE `p_bundling_quota` (
  `bl_quota_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '套餐ID',
  `store_id` int(11) NOT NULL COMMENT '店铺id',
  `store_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `member_id` int(11) NOT NULL COMMENT '会员id',
  `member_name` varchar(50) NOT NULL COMMENT '会员名称',
  `bl_quota_month` tinyint(3) unsigned NOT NULL COMMENT '购买数量（单位月）',
  `bl_quota_starttime` varchar(10) NOT NULL COMMENT '套餐开始时间',
  `bl_quota_endtime` varchar(10) NOT NULL COMMENT '套餐结束时间',
  `bl_state` tinyint(1) unsigned NOT NULL COMMENT '套餐状态：0关闭，1开启。默认为 1',
  PRIMARY KEY (`bl_quota_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='组合销售套餐表';

-- ----------------------------
-- Records of p_bundling_quota
-- ----------------------------

-- ----------------------------
-- Table structure for p_combo_goods
-- ----------------------------
DROP TABLE IF EXISTS `p_combo_goods`;
CREATE TABLE `p_combo_goods` (
  `cg_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '推荐组合id ',
  `cg_class` varchar(10) NOT NULL COMMENT '推荐组合名称',
  `goods_id` int(10) unsigned NOT NULL COMMENT '主商品id',
  `goods_commonid` int(10) unsigned NOT NULL COMMENT '主商品公共id',
  `store_id` int(10) unsigned NOT NULL COMMENT '所属店铺id',
  `combo_goodsid` int(10) unsigned NOT NULL COMMENT '推荐组合商品id',
  PRIMARY KEY (`cg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品推荐组合表';

-- ----------------------------
-- Records of p_combo_goods
-- ----------------------------

-- ----------------------------
-- Table structure for p_combo_quota
-- ----------------------------
DROP TABLE IF EXISTS `p_combo_quota`;
CREATE TABLE `p_combo_quota` (
  `cq_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '推荐组合套餐id',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `store_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `cq_starttime` int(10) unsigned NOT NULL COMMENT '套餐开始时间',
  `cq_endtime` int(10) unsigned NOT NULL COMMENT '套餐结束时间',
  PRIMARY KEY (`cq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='推荐组合套餐表';

-- ----------------------------
-- Records of p_combo_quota
-- ----------------------------

-- ----------------------------
-- Table structure for p_cou
-- ----------------------------
DROP TABLE IF EXISTS `p_cou`;
CREATE TABLE `p_cou` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `store_id` int(11) NOT NULL COMMENT '店铺ID',
  `store_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `quota_id` int(11) NOT NULL COMMENT '套餐ID',
  `name` varchar(100) NOT NULL COMMENT '名称',
  `tstart` int(10) unsigned NOT NULL COMMENT '开始时间',
  `tend` int(10) unsigned NOT NULL COMMENT '结束时间',
  `state` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态 1正常2结束3平台关闭',
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  KEY `quota_id` (`quota_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='加价购';

-- ----------------------------
-- Records of p_cou
-- ----------------------------

-- ----------------------------
-- Table structure for p_cou_level
-- ----------------------------
DROP TABLE IF EXISTS `p_cou_level`;
CREATE TABLE `p_cou_level` (
  `cou_id` int(11) NOT NULL COMMENT '加价购ID',
  `xlevel` tinyint(3) unsigned NOT NULL COMMENT '等级',
  `mincost` decimal(10,2) NOT NULL COMMENT '最低消费金额',
  `maxcou` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最大可凑单数',
  PRIMARY KEY (`cou_id`,`xlevel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='加价购活动规则';

-- ----------------------------
-- Records of p_cou_level
-- ----------------------------

-- ----------------------------
-- Table structure for p_cou_level_sku
-- ----------------------------
DROP TABLE IF EXISTS `p_cou_level_sku`;
CREATE TABLE `p_cou_level_sku` (
  `cou_id` int(11) NOT NULL COMMENT '加价购ID',
  `xlevel` tinyint(3) unsigned NOT NULL COMMENT '等级',
  `sku_id` int(11) NOT NULL COMMENT '商品条目ID',
  `price` decimal(10,2) NOT NULL COMMENT '价格',
  PRIMARY KEY (`cou_id`,`xlevel`,`sku_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='加价购活动换购商品';

-- ----------------------------
-- Records of p_cou_level_sku
-- ----------------------------

-- ----------------------------
-- Table structure for p_cou_quota
-- ----------------------------
DROP TABLE IF EXISTS `p_cou_quota`;
CREATE TABLE `p_cou_quota` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `store_id` int(11) NOT NULL COMMENT '店铺ID',
  `store_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `tstart` int(10) unsigned NOT NULL COMMENT '开始时间',
  `tend` int(10) unsigned NOT NULL COMMENT '结束时间',
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='加价购套餐';

-- ----------------------------
-- Records of p_cou_quota
-- ----------------------------

-- ----------------------------
-- Table structure for p_cou_sku
-- ----------------------------
DROP TABLE IF EXISTS `p_cou_sku`;
CREATE TABLE `p_cou_sku` (
  `sku_id` int(11) NOT NULL COMMENT '商品条目ID',
  `cou_id` int(11) NOT NULL COMMENT '加价购ID',
  `tstart` int(10) unsigned NOT NULL COMMENT '开始时间',
  `tend` int(10) unsigned NOT NULL COMMENT '结束时间',
  PRIMARY KEY (`sku_id`,`cou_id`),
  KEY `cou_id` (`cou_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='加价购活动商品';

-- ----------------------------
-- Records of p_cou_sku
-- ----------------------------

-- ----------------------------
-- Table structure for p_fcode_quota
-- ----------------------------
DROP TABLE IF EXISTS `p_fcode_quota`;
CREATE TABLE `p_fcode_quota` (
  `fcq_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'F码套餐id',
  `store_id` int(11) NOT NULL COMMENT '店铺id',
  `store_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `fcq_starttime` int(11) NOT NULL COMMENT '套餐开始时间',
  `fcq_endtime` int(11) NOT NULL COMMENT '套餐结束时间',
  PRIMARY KEY (`fcq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='F码商品套餐表';

-- ----------------------------
-- Records of p_fcode_quota
-- ----------------------------

-- ----------------------------
-- Table structure for p_mansong
-- ----------------------------
DROP TABLE IF EXISTS `p_mansong`;
CREATE TABLE `p_mansong` (
  `mansong_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '满送活动编号',
  `mansong_name` varchar(50) NOT NULL COMMENT '活动名称',
  `quota_id` int(10) unsigned NOT NULL COMMENT '套餐编号',
  `start_time` int(10) unsigned NOT NULL COMMENT '活动开始时间',
  `end_time` int(10) unsigned NOT NULL COMMENT '活动结束时间',
  `member_id` int(10) unsigned NOT NULL COMMENT '用户编号',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺编号',
  `member_name` varchar(50) NOT NULL COMMENT '用户名',
  `store_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `state` tinyint(1) unsigned NOT NULL COMMENT '活动状态(1-未发布/2-正常/3-取消/4-失效/5-结束)',
  `remark` varchar(200) DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`mansong_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='满就送活动表';

-- ----------------------------
-- Records of p_mansong
-- ----------------------------

-- ----------------------------
-- Table structure for p_mansong_quota
-- ----------------------------
DROP TABLE IF EXISTS `p_mansong_quota`;
CREATE TABLE `p_mansong_quota` (
  `quota_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '满就送套餐编号',
  `member_id` int(10) unsigned NOT NULL COMMENT '用户编号',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺编号',
  `member_name` varchar(50) NOT NULL COMMENT '用户名',
  `store_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `start_time` int(10) unsigned NOT NULL COMMENT '开始时间',
  `end_time` int(10) unsigned NOT NULL COMMENT '结束时间',
  `state` tinyint(1) unsigned DEFAULT '0' COMMENT '配额状态(1-可用/2-取消/3-结束)',
  PRIMARY KEY (`quota_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='满就送套餐表';

-- ----------------------------
-- Records of p_mansong_quota
-- ----------------------------

-- ----------------------------
-- Table structure for p_mansong_rule
-- ----------------------------
DROP TABLE IF EXISTS `p_mansong_rule`;
CREATE TABLE `p_mansong_rule` (
  `rule_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则编号',
  `mansong_id` int(10) unsigned NOT NULL COMMENT '活动编号',
  `price` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '级别价格',
  `discount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '减现金优惠金额',
  `mansong_goods_name` varchar(50) DEFAULT '' COMMENT '礼品名称',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品编号',
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='满就送活动规则表';

-- ----------------------------
-- Records of p_mansong_rule
-- ----------------------------

-- ----------------------------
-- Table structure for p_sole_goods
-- ----------------------------
DROP TABLE IF EXISTS `p_sole_goods`;
CREATE TABLE `p_sole_goods` (
  `sole_goods_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '手机专享商品id',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `sole_price` decimal(10,2) NOT NULL COMMENT '专享价格',
  `gc_id` int(10) unsigned NOT NULL COMMENT '商品分类id',
  `sole_state` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '套餐状态 1开启 0关闭 默认1',
  PRIMARY KEY (`sole_goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='手机专享商品表';

-- ----------------------------
-- Records of p_sole_goods
-- ----------------------------

-- ----------------------------
-- Table structure for p_sole_quota
-- ----------------------------
DROP TABLE IF EXISTS `p_sole_quota`;
CREATE TABLE `p_sole_quota` (
  `sole_quota_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '套餐id',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `store_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `sole_quota_starttime` int(10) unsigned NOT NULL COMMENT '开始时间',
  `sole_quota_endtime` int(10) unsigned NOT NULL COMMENT '结束时间',
  `sole_state` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '套餐状态 1开启 0关闭 默认1',
  PRIMARY KEY (`sole_quota_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='手机专享套餐表';

-- ----------------------------
-- Records of p_sole_quota
-- ----------------------------

-- ----------------------------
-- Table structure for p_xianshi
-- ----------------------------
DROP TABLE IF EXISTS `p_xianshi`;
CREATE TABLE `p_xianshi` (
  `xianshi_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '限时编号',
  `xianshi_name` varchar(50) NOT NULL COMMENT '活动名称',
  `xianshi_title` varchar(10) DEFAULT NULL COMMENT '活动标题',
  `xianshi_explain` varchar(50) DEFAULT NULL COMMENT '活动说明',
  `quota_id` int(10) unsigned NOT NULL COMMENT '套餐编号',
  `start_time` int(10) unsigned NOT NULL COMMENT '活动开始时间',
  `end_time` int(10) unsigned NOT NULL COMMENT '活动结束时间',
  `member_id` int(10) unsigned NOT NULL COMMENT '用户编号',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺编号',
  `member_name` varchar(50) NOT NULL COMMENT '用户名',
  `store_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `lower_limit` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '购买下限，1为不限制',
  `state` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态，0-取消 1-正常',
  PRIMARY KEY (`xianshi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='限时折扣活动表';

-- ----------------------------
-- Records of p_xianshi
-- ----------------------------

-- ----------------------------
-- Table structure for p_xianshi_goods
-- ----------------------------
DROP TABLE IF EXISTS `p_xianshi_goods`;
CREATE TABLE `p_xianshi_goods` (
  `xianshi_goods_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '限时折扣商品表',
  `xianshi_id` int(10) unsigned NOT NULL COMMENT '限时活动编号',
  `xianshi_name` varchar(50) NOT NULL COMMENT '活动名称',
  `xianshi_title` varchar(10) DEFAULT NULL COMMENT '活动标题',
  `xianshi_explain` varchar(50) DEFAULT NULL COMMENT '活动说明',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品编号',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺编号',
  `goods_name` varchar(100) NOT NULL COMMENT '商品名称',
  `goods_price` decimal(10,2) NOT NULL COMMENT '店铺价格',
  `xianshi_price` decimal(10,2) NOT NULL COMMENT '限时折扣价格',
  `goods_image` varchar(100) NOT NULL COMMENT '商品图片',
  `start_time` int(10) unsigned NOT NULL COMMENT '开始时间',
  `end_time` int(10) unsigned NOT NULL COMMENT '结束时间',
  `lower_limit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '购买下限，0为不限制',
  `state` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态，0-取消 1-正常',
  `xianshi_recommend` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '推荐标志 0-未推荐 1-已推荐',
  `gc_id_1` mediumint(9) DEFAULT '0' COMMENT '商品分类一级ID',
  PRIMARY KEY (`xianshi_goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='限时折扣商品表';

-- ----------------------------
-- Records of p_xianshi_goods
-- ----------------------------

-- ----------------------------
-- Table structure for p_xianshi_quota
-- ----------------------------
DROP TABLE IF EXISTS `p_xianshi_quota`;
CREATE TABLE `p_xianshi_quota` (
  `quota_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '限时折扣套餐编号',
  `member_id` int(10) unsigned NOT NULL COMMENT '用户编号',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺编号',
  `member_name` varchar(50) NOT NULL COMMENT '用户名',
  `store_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `start_time` int(10) unsigned NOT NULL COMMENT '套餐开始时间',
  `end_time` int(10) unsigned NOT NULL COMMENT '套餐结束时间',
  PRIMARY KEY (`quota_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='限时折扣套餐表';

-- ----------------------------
-- Records of p_xianshi_quota
-- ----------------------------

-- ----------------------------
-- Table structure for rcb_log
-- ----------------------------
DROP TABLE IF EXISTS `rcb_log`;
CREATE TABLE `rcb_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增编号',
  `member_id` int(11) NOT NULL COMMENT '会员编号',
  `member_name` varchar(50) NOT NULL COMMENT '会员名称',
  `type` varchar(15) NOT NULL DEFAULT '' COMMENT 'order_pay下单使用 order_freeze下单冻结 order_cancel取消订单解冻 order_comb_pay下单扣除被冻结 recharge平台充值卡充值 refund确认退款 vr_refund虚拟兑码退款',
  `add_time` int(11) NOT NULL COMMENT '添加时间',
  `available_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '可用充值卡余额变更 0表示未变更',
  `freeze_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '冻结充值卡余额变更 0表示未变更',
  `description` varchar(150) DEFAULT NULL COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='充值卡余额变更日志表';

-- ----------------------------
-- Records of rcb_log
-- ----------------------------

-- ----------------------------
-- Table structure for rechargecard
-- ----------------------------
DROP TABLE IF EXISTS `rechargecard`;
CREATE TABLE `rechargecard` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `sn` varchar(50) NOT NULL COMMENT '卡号',
  `denomination` decimal(10,2) NOT NULL COMMENT '面额',
  `batchflag` varchar(20) DEFAULT '' COMMENT '批次标识',
  `admin_name` varchar(50) DEFAULT NULL COMMENT '创建者名称',
  `tscreated` int(10) unsigned NOT NULL COMMENT '创建时间',
  `tsused` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '使用时间',
  `state` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0可用 1已用 2已删',
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '使用者会员ID',
  `member_name` varchar(50) DEFAULT NULL COMMENT '使用者会员名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='平台充值卡';

-- ----------------------------
-- Records of rechargecard
-- ----------------------------

-- ----------------------------
-- Table structure for rec_position
-- ----------------------------
DROP TABLE IF EXISTS `rec_position`;
CREATE TABLE `rec_position` (
  `rec_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `pic_type` enum('1','2','0') NOT NULL DEFAULT '1' COMMENT '0文字1本地图片2远程',
  `title` varchar(200) DEFAULT '' COMMENT '标题',
  `content` text NOT NULL COMMENT '序列化推荐位内容',
  PRIMARY KEY (`rec_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='推荐位';

-- ----------------------------
-- Records of rec_position
-- ----------------------------

-- ----------------------------
-- Table structure for redpacket
-- ----------------------------
DROP TABLE IF EXISTS `redpacket`;
CREATE TABLE `redpacket` (
  `rpacket_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '红包编号',
  `rpacket_code` varchar(32) NOT NULL COMMENT '红包编码',
  `rpacket_t_id` int(11) NOT NULL COMMENT '红包模版编号',
  `rpacket_title` varchar(50) NOT NULL COMMENT '红包标题',
  `rpacket_desc` varchar(255) NOT NULL COMMENT '红包描述',
  `rpacket_start_date` int(11) NOT NULL COMMENT '红包有效期开始时间',
  `rpacket_end_date` int(11) NOT NULL COMMENT '红包有效期结束时间',
  `rpacket_price` int(11) NOT NULL COMMENT '红包面额',
  `rpacket_limit` decimal(10,2) NOT NULL COMMENT '红包使用时的订单限额',
  `rpacket_state` tinyint(4) NOT NULL COMMENT '红包状态(1-未用,2-已用,3-过期)',
  `rpacket_active_date` int(11) NOT NULL COMMENT '红包发放日期',
  `rpacket_owner_id` int(11) NOT NULL COMMENT '红包所有者id',
  `rpacket_owner_name` varchar(50) DEFAULT NULL COMMENT '红包所有者名称',
  `rpacket_order_id` bigint(20) DEFAULT NULL COMMENT '使用该红包的订单支付单号',
  `rpacket_pwd` varchar(100) DEFAULT NULL COMMENT '红包卡密',
  `rpacket_pwd2` varchar(100) DEFAULT NULL COMMENT '红包卡密2',
  `rpacket_customimg` varchar(1000) DEFAULT NULL COMMENT '红包自定义图片',
  PRIMARY KEY (`rpacket_id`),
  UNIQUE KEY `rpacket_pwd` (`rpacket_pwd`),
  UNIQUE KEY `rpacket_pwd2` (`rpacket_pwd2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='红包表';

-- ----------------------------
-- Records of redpacket
-- ----------------------------

-- ----------------------------
-- Table structure for redpacket_template
-- ----------------------------
DROP TABLE IF EXISTS `redpacket_template`;
CREATE TABLE `redpacket_template` (
  `rpacket_t_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '红包模版编号',
  `rpacket_t_title` varchar(50) NOT NULL COMMENT '红包模版名称',
  `rpacket_t_desc` varchar(255) NOT NULL COMMENT '红包模版描述',
  `rpacket_t_start_date` int(11) NOT NULL COMMENT '红包模版有效期开始时间',
  `rpacket_t_end_date` int(11) NOT NULL COMMENT '红包模版有效期结束时间',
  `rpacket_t_price` decimal(10,2) NOT NULL COMMENT '红包模版面额',
  `rpacket_t_limit` decimal(10,2) NOT NULL COMMENT '红包使用时的订单限额',
  `rpacket_t_adminid` int(11) NOT NULL COMMENT '修改管理员ID',
  `rpacket_t_state` tinyint(4) NOT NULL COMMENT '模版状态(1-有效,2-失效)',
  `rpacket_t_total` int(11) NOT NULL COMMENT '模版可发放的红包总数',
  `rpacket_t_giveout` int(11) NOT NULL COMMENT '模版已发放的红包数量',
  `rpacket_t_used` int(11) NOT NULL COMMENT '模版已经使用过的红包数量',
  `rpacket_t_updatetime` int(11) NOT NULL COMMENT '模版的创建时间',
  `rpacket_t_points` int(11) NOT NULL DEFAULT '0' COMMENT '兑换所需积分',
  `rpacket_t_eachlimit` int(11) NOT NULL DEFAULT '1' COMMENT '每人限领张数',
  `rpacket_t_customimg` varchar(200) DEFAULT NULL COMMENT '自定义模板图片',
  `rpacket_t_recommend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否推荐 0不推荐 1推荐',
  `rpacket_t_gettype` tinyint(1) NOT NULL DEFAULT '1' COMMENT '领取方式 1积分兑换 2卡密兑换 3免费领取',
  `rpacket_t_isbuild` tinyint(1) NOT NULL DEFAULT '0' COMMENT '领取方式为卡密兑换是否已经生成下属红包 0未生成 1已生成',
  `rpacket_t_mgradelimit` tinyint(2) NOT NULL DEFAULT '0' COMMENT '领取限制的会员等级',
  PRIMARY KEY (`rpacket_t_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='红包模版表';

-- ----------------------------
-- Records of redpacket_template
-- ----------------------------

-- ----------------------------
-- Table structure for refund_detail
-- ----------------------------
DROP TABLE IF EXISTS `refund_detail`;
CREATE TABLE `refund_detail` (
  `refund_id` int(10) unsigned NOT NULL COMMENT '记录ID',
  `order_id` int(10) unsigned NOT NULL COMMENT '订单ID',
  `batch_no` varchar(32) NOT NULL COMMENT '批次号',
  `refund_amount` decimal(10,2) DEFAULT '0.00' COMMENT '退款金额',
  `pay_amount` decimal(10,2) DEFAULT '0.00' COMMENT '在线退款金额',
  `pd_amount` decimal(10,2) DEFAULT '0.00' COMMENT '预存款金额',
  `rcb_amount` decimal(10,2) DEFAULT '0.00' COMMENT '充值卡金额',
  `refund_code` char(10) NOT NULL DEFAULT 'predeposit' COMMENT '退款支付代码',
  `refund_state` tinyint(1) unsigned DEFAULT '1' COMMENT '退款状态:1为处理中,2为已完成,默认为1',
  `add_time` int(10) unsigned NOT NULL COMMENT '添加时间',
  `pay_time` int(10) unsigned DEFAULT '0' COMMENT '在线退款完成时间,默认为0',
  PRIMARY KEY (`refund_id`),
  KEY `batch_no` (`batch_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='退款详细表';

-- ----------------------------
-- Records of refund_detail
-- ----------------------------

-- ----------------------------
-- Table structure for refund_reason
-- ----------------------------
DROP TABLE IF EXISTS `refund_reason`;
CREATE TABLE `refund_reason` (
  `reason_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '原因ID',
  `reason_info` varchar(50) NOT NULL COMMENT '原因内容',
  `sort` tinyint(1) unsigned DEFAULT '255' COMMENT '排序',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`reason_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='退款退货原因表';

-- ----------------------------
-- Records of refund_reason
-- ----------------------------

-- ----------------------------
-- Table structure for refund_return
-- ----------------------------
DROP TABLE IF EXISTS `refund_return`;
CREATE TABLE `refund_return` (
  `refund_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `order_id` int(10) unsigned NOT NULL COMMENT '订单ID',
  `order_sn` varchar(50) NOT NULL COMMENT '订单编号',
  `refund_sn` varchar(50) NOT NULL COMMENT '申请编号',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺ID',
  `store_name` varchar(20) NOT NULL COMMENT '店铺名称',
  `buyer_id` int(10) unsigned NOT NULL COMMENT '买家ID',
  `buyer_name` varchar(50) NOT NULL COMMENT '买家会员名',
  `goods_id` bigint(30) unsigned NOT NULL COMMENT '商品ID,全部退款是0',
  `order_goods_id` int(10) unsigned DEFAULT '0' COMMENT '订单商品ID,全部退款是0',
  `goods_name` varchar(50) NOT NULL COMMENT '商品名称',
  `goods_num` int(10) unsigned DEFAULT '1' COMMENT '商品数量',
  `refund_amount` decimal(10,2) DEFAULT '0.00' COMMENT '退款金额',
  `breach_amount` decimal(10,2) DEFAULT NULL COMMENT '违约金',
  `goods_image` varchar(100) DEFAULT NULL COMMENT '商品图片',
  `order_goods_type` tinyint(1) unsigned DEFAULT '1' COMMENT '订单商品类型:1默认2抢购商品3限时折扣商品4组合套装',
  `refund_type` tinyint(1) unsigned DEFAULT '1' COMMENT '申请类型:1为退款,2为退货,默认为1',
  `seller_state` tinyint(1) unsigned DEFAULT '1' COMMENT '卖家处理状态:1为待审核,2为同意,3为不同意,默认为1',
  `refund_state` tinyint(1) unsigned DEFAULT '1' COMMENT '申请状态:1为处理中,2为待管理员处理,3为已完成,默认为1',
  `return_type` tinyint(1) unsigned DEFAULT '1' COMMENT '退货类型:1为不用退货,2为需要退货,默认为1',
  `order_lock` tinyint(1) unsigned DEFAULT '1' COMMENT '订单锁定类型:1为不用锁定,2为需要锁定,默认为1',
  `goods_state` tinyint(1) unsigned DEFAULT '1' COMMENT '物流状态:1为待发货,2为待收货,3为未收到,4为已收货,默认为1',
  `add_time` int(10) unsigned NOT NULL COMMENT '添加时间',
  `seller_time` int(10) unsigned DEFAULT '0' COMMENT '卖家处理时间',
  `admin_time` int(10) unsigned DEFAULT '0' COMMENT '管理员处理时间,默认为0',
  `reason_id` int(10) unsigned DEFAULT '0' COMMENT '原因ID:0为其它',
  `reason_info` varchar(300) DEFAULT '' COMMENT '原因内容',
  `pic_info` varchar(300) DEFAULT '' COMMENT '图片',
  `buyer_message` varchar(300) DEFAULT NULL COMMENT '申请原因',
  `seller_message` varchar(300) DEFAULT NULL COMMENT '卖家备注',
  `admin_message` varchar(300) DEFAULT NULL COMMENT '管理员备注',
  `express_id` tinyint(1) unsigned DEFAULT '0' COMMENT '物流公司编号',
  `invoice_no` varchar(50) DEFAULT NULL COMMENT '物流单号',
  `ship_time` int(10) unsigned DEFAULT '0' COMMENT '发货时间,默认为0',
  `delay_time` int(10) unsigned DEFAULT '0' COMMENT '收货延迟时间,默认为0',
  `receive_time` int(10) unsigned DEFAULT '0' COMMENT '收货时间,默认为0',
  `receive_message` varchar(300) DEFAULT NULL COMMENT '收货备注',
  `commis_rate` smallint(6) DEFAULT '0' COMMENT '佣金比例',
  `rpt_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '退款红包值，默认0，只有全部退款时才把该订单使用的红包金额写到此处',
  `return_form` tinyint(1) DEFAULT NULL COMMENT '退款类型（1、现金，2、打卡）',
  `billha_info` varchar(300) DEFAULT NULL COMMENT '无订单退货（销售价、门店id、入库仓）',
  PRIMARY KEY (`refund_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='退款退货表';

-- ----------------------------
-- Records of refund_return
-- ----------------------------

-- ----------------------------
-- Table structure for rel_cat_attribute
-- ----------------------------
DROP TABLE IF EXISTS `rel_cat_attribute`;
CREATE TABLE `rel_cat_attribute` (
  `rel_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cat_type_id` int(10) NOT NULL COMMENT '分类名称id',
  `product_type_id` smallint(4) NOT NULL COMMENT '产品线id',
  `attribute_id` int(10) NOT NULL COMMENT '属性id',
  `is_show` tinyint(1) NOT NULL COMMENT '是否显示：1是0否',
  `is_default` tinyint(1) NOT NULL COMMENT '是否默认：1是0否',
  `is_require` tinyint(1) NOT NULL COMMENT '是否必填：1是0否',
  `status` tinyint(1) NOT NULL COMMENT '状态:1启用;0停用',
  `attr_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '属性类型 1基本属性2销售属性3商品属性',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `create_user` varchar(10) NOT NULL COMMENT '创建人',
  `info` varchar(100) DEFAULT NULL COMMENT '备注',
  `default_val` varchar(20) DEFAULT NULL COMMENT '默认值是什么',
  PRIMARY KEY (`rel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of rel_cat_attribute
-- ----------------------------

-- ----------------------------
-- Table structure for rel_style_attribute
-- ----------------------------
DROP TABLE IF EXISTS `rel_style_attribute`;
CREATE TABLE `rel_style_attribute` (
  `rel_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cat_type_id` int(10) NOT NULL DEFAULT '1' COMMENT '分类名称id',
  `product_type_id` smallint(4) NOT NULL DEFAULT '1' COMMENT '产品线id',
  `style_sn` varchar(20) NOT NULL COMMENT '款号',
  `attribute_id` int(10) NOT NULL COMMENT '属性id',
  `attribute_value` varchar(200) DEFAULT NULL COMMENT '属性值',
  `show_type` tinyint(1) NOT NULL COMMENT '1=>''文本框'',2=>''单选'',3=>''多选'',4=>''下拉列表''',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `create_user` varchar(10) NOT NULL COMMENT '创建人',
  `info` varchar(100) DEFAULT NULL COMMENT '备注',
  `style_id` int(10) NOT NULL COMMENT '款式ID',
  `is_price_conbined` tinyint(1) unsigned DEFAULT '0' COMMENT '是否参与价格计算 1是 0否',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`rel_id`),
  KEY `style_sn` (`style_sn`),
  KEY `style_id` (`style_id`),
  KEY `attribute_id` (`attribute_id`),
  KEY `cat_type_id` (`cat_type_id`),
  KEY `product_type_id` (`product_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of rel_style_attribute
-- ----------------------------

-- ----------------------------
-- Table structure for rel_style_lovers
-- ----------------------------
DROP TABLE IF EXISTS `rel_style_lovers`;
CREATE TABLE `rel_style_lovers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `style_id1` int(10) DEFAULT '0' COMMENT '款式id',
  `style_id2` int(10) DEFAULT '0' COMMENT '款式id',
  `style_sn1` varchar(30) DEFAULT NULL COMMENT '款式编号',
  `style_sn2` varchar(30) DEFAULT NULL COMMENT '款式编号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='情侣表';

-- ----------------------------
-- Records of rel_style_lovers
-- ----------------------------

-- ----------------------------
-- Table structure for rel_style_stone
-- ----------------------------
DROP TABLE IF EXISTS `rel_style_stone`;
CREATE TABLE `rel_style_stone` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `style_id` int(10) unsigned NOT NULL COMMENT '款式信息自增id',
  `stone_position` tinyint(3) NOT NULL COMMENT '石头位置类型 1主石2副石',
  `stone_cat` tinyint(3) NOT NULL COMMENT '石头类型',
  `stone_attr` text COMMENT '属性',
  `add_time` datetime DEFAULT NULL COMMENT '导入数据用',
  `shape` tinyint(1) unsigned DEFAULT '0',
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `style_id` (`style_id`),
  KEY `stone_position` (`stone_position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of rel_style_stone
-- ----------------------------

-- ----------------------------
-- Table structure for s1
-- ----------------------------
DROP TABLE IF EXISTS `s1`;
CREATE TABLE `s1` (
  `store_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `m_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_id` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of s1
-- ----------------------------

-- ----------------------------
-- Table structure for seller
-- ----------------------------
DROP TABLE IF EXISTS `seller`;
CREATE TABLE `seller` (
  `seller_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '卖家编号',
  `seller_name` varchar(50) NOT NULL COMMENT '卖家用户名',
  `member_id` int(10) unsigned NOT NULL COMMENT '用户编号',
  `seller_group_id` int(10) unsigned DEFAULT NULL COMMENT '卖家组编号',
  `store_id` int(10) unsigned DEFAULT NULL COMMENT '店铺编号',
  `is_admin` tinyint(3) unsigned DEFAULT NULL COMMENT '是否管理员(0-不是 1-是)',
  `seller_quicklink` varchar(255) DEFAULT NULL COMMENT '卖家快捷操作',
  `last_login_time` int(10) unsigned DEFAULT NULL COMMENT '最后登录时间',
  `is_client` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否客户端用户 0-否 1-是',
  `is_hidden` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`seller_id`),
  UNIQUE KEY `member_id` (`member_id`),
  UNIQUE KEY `seller_name` (`seller_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='卖家用户表';

-- ----------------------------
-- Records of seller
-- ----------------------------

-- ----------------------------
-- Table structure for seller_group
-- ----------------------------
DROP TABLE IF EXISTS `seller_group`;
CREATE TABLE `seller_group` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '卖家组编号',
  `group_name` varchar(50) NOT NULL COMMENT '组名',
  `limits` text NOT NULL COMMENT '权限',
  `smt_limits` text COMMENT '消息权限范围',
  `gc_limits` tinyint(3) unsigned DEFAULT '1' COMMENT '1拥有所有分类权限，0拥有部分分类权限',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺编号',
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='卖家用户组表';

-- ----------------------------
-- Records of seller_group
-- ----------------------------

-- ----------------------------
-- Table structure for seller_group_bclass
-- ----------------------------
DROP TABLE IF EXISTS `seller_group_bclass`;
CREATE TABLE `seller_group_bclass` (
  `bid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned DEFAULT '0' COMMENT '权限组ID',
  `class_1` mediumint(9) unsigned DEFAULT '0' COMMENT '一级分类',
  `class_2` mediumint(9) unsigned DEFAULT '0' COMMENT '二级分类',
  `class_3` mediumint(9) unsigned DEFAULT '0' COMMENT '三级分类',
  `gc_id` mediumint(9) unsigned DEFAULT '0' COMMENT '最底级分类',
  PRIMARY KEY (`bid`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商家内部组商品类目表';

-- ----------------------------
-- Records of seller_group_bclass
-- ----------------------------

-- ----------------------------
-- Table structure for seller_log
-- ----------------------------
DROP TABLE IF EXISTS `seller_log`;
CREATE TABLE `seller_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '日志编号',
  `log_content` varchar(500) NOT NULL COMMENT '日志内容',
  `log_time` int(10) unsigned NOT NULL COMMENT '日志时间',
  `log_seller_id` int(10) unsigned NOT NULL COMMENT '卖家编号',
  `log_seller_name` varchar(50) NOT NULL COMMENT '卖家账号',
  `log_store_id` int(10) unsigned NOT NULL COMMENT '店铺编号',
  `log_seller_ip` varchar(50) NOT NULL COMMENT '卖家ip',
  `log_url` varchar(50) NOT NULL COMMENT '日志url',
  `log_state` tinyint(3) unsigned NOT NULL COMMENT '日志状态(0-失败 1-成功)',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='卖家日志表';

-- ----------------------------
-- Records of seller_log
-- ----------------------------

-- ----------------------------
-- Table structure for seller_store
-- ----------------------------
DROP TABLE IF EXISTS `seller_store`;
CREATE TABLE `seller_store` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `seller_id` int(10) DEFAULT NULL,
  `store_id` int(10) DEFAULT NULL,
  `seller_group_id` int(10) DEFAULT NULL,
  `is_admin` tinyint(2) DEFAULT NULL,
  `seller_quicklink` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of seller_store
-- ----------------------------

-- ----------------------------
-- Table structure for seo
-- ----------------------------
DROP TABLE IF EXISTS `seo`;
CREATE TABLE `seo` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `keywords` varchar(255) NOT NULL COMMENT '关键词',
  `description` text NOT NULL COMMENT '描述',
  `type` varchar(20) NOT NULL COMMENT '类型',
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='SEO信息存放表';

-- ----------------------------
-- Records of seo
-- ----------------------------

-- ----------------------------
-- Table structure for setting
-- ----------------------------
DROP TABLE IF EXISTS `setting`;
CREATE TABLE `setting` (
  `name` varchar(50) NOT NULL COMMENT '名称',
  `value` text COMMENT '值',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统设置表';

-- ----------------------------
-- Records of setting
-- ----------------------------

-- ----------------------------
-- Table structure for signin
-- ----------------------------
DROP TABLE IF EXISTS `signin`;
CREATE TABLE `signin` (
  `sl_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `sl_memberid` int(11) NOT NULL COMMENT '会员ID',
  `sl_membername` varchar(100) NOT NULL COMMENT '会员名称',
  `sl_addtime` int(11) NOT NULL COMMENT '签到时间',
  `sl_points` int(11) NOT NULL COMMENT '获得积分数',
  PRIMARY KEY (`sl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of signin
-- ----------------------------

-- ----------------------------
-- Table structure for sms_log
-- ----------------------------
DROP TABLE IF EXISTS `sms_log`;
CREATE TABLE `sms_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `log_phone` char(11) NOT NULL COMMENT '手机号',
  `log_captcha` char(6) NOT NULL COMMENT '短信验证码',
  `log_ip` varchar(15) NOT NULL COMMENT '请求IP',
  `log_msg` varchar(300) NOT NULL COMMENT '短信内容',
  `log_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '短信类型:1为注册,2为登录,3为找回密码,默认为1',
  `add_time` int(10) unsigned NOT NULL COMMENT '添加时间',
  `member_id` int(10) unsigned DEFAULT '0' COMMENT '会员ID,注册为0',
  `member_name` varchar(50) DEFAULT '' COMMENT '会员名',
  PRIMARY KEY (`log_id`),
  KEY `log_phone` (`log_phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='手机短信记录表';

-- ----------------------------
-- Records of sms_log
-- ----------------------------

-- ----------------------------
-- Table structure for sns_albumclass
-- ----------------------------
DROP TABLE IF EXISTS `sns_albumclass`;
CREATE TABLE `sns_albumclass` (
  `ac_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '相册id',
  `ac_name` varchar(100) NOT NULL COMMENT '相册名称',
  `member_id` int(10) unsigned NOT NULL COMMENT '所属会员id',
  `ac_des` varchar(255) DEFAULT '' COMMENT '相册描述',
  `ac_sort` tinyint(3) unsigned NOT NULL COMMENT '排序',
  `ac_cover` varchar(255) DEFAULT NULL COMMENT '相册封面',
  `upload_time` int(10) unsigned NOT NULL COMMENT '图片上传时间',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为买家秀相册  1为是,0为否',
  PRIMARY KEY (`ac_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='相册表';

-- ----------------------------
-- Records of sns_albumclass
-- ----------------------------

-- ----------------------------
-- Table structure for sns_albumpic
-- ----------------------------
DROP TABLE IF EXISTS `sns_albumpic`;
CREATE TABLE `sns_albumpic` (
  `ap_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '相册图片表id',
  `ap_name` varchar(100) NOT NULL COMMENT '图片名称',
  `ac_id` int(10) unsigned NOT NULL COMMENT '相册id',
  `ap_cover` varchar(255) NOT NULL COMMENT '图片路径',
  `ap_size` int(10) unsigned NOT NULL COMMENT '图片大小',
  `ap_spec` varchar(100) NOT NULL COMMENT '图片规格',
  `member_id` int(10) unsigned NOT NULL COMMENT '所属店铺id',
  `upload_time` int(10) unsigned NOT NULL COMMENT '图片上传时间',
  `ap_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '图片类型，0为无、1为买家秀',
  `item_id` tinyint(4) NOT NULL DEFAULT '0' COMMENT '信息ID',
  PRIMARY KEY (`ap_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='相册图片表';

-- ----------------------------
-- Records of sns_albumpic
-- ----------------------------

-- ----------------------------
-- Table structure for sns_binding
-- ----------------------------
DROP TABLE IF EXISTS `sns_binding`;
CREATE TABLE `sns_binding` (
  `snsbind_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `snsbind_memberid` int(11) NOT NULL COMMENT '会员编号',
  `snsbind_membername` varchar(100) NOT NULL COMMENT '会员名称',
  `snsbind_appsign` varchar(50) NOT NULL COMMENT '应用标志',
  `snsbind_updatetime` int(11) NOT NULL COMMENT '绑定更新时间',
  `snsbind_openid` varchar(100) NOT NULL COMMENT '应用用户编号',
  `snsbind_openinfo` text COMMENT '应用用户信息',
  `snsbind_accesstoken` varchar(100) NOT NULL COMMENT '访问第三方资源的凭证',
  `snsbind_expiresin` int(11) NOT NULL COMMENT 'accesstoken过期时间，以返回的时间的准，单位为秒，注意过期时提醒用户重新授权',
  `snsbind_refreshtoken` varchar(100) DEFAULT NULL COMMENT '刷新token',
  PRIMARY KEY (`snsbind_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分享应用用户绑定记录表';

-- ----------------------------
-- Records of sns_binding
-- ----------------------------

-- ----------------------------
-- Table structure for sns_comment
-- ----------------------------
DROP TABLE IF EXISTS `sns_comment`;
CREATE TABLE `sns_comment` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `comment_memberid` int(11) NOT NULL COMMENT '会员ID',
  `comment_membername` varchar(100) NOT NULL COMMENT '会员名称',
  `comment_memberavatar` varchar(100) DEFAULT NULL COMMENT '会员头像',
  `comment_originalid` int(11) NOT NULL COMMENT '原帖ID',
  `comment_originaltype` tinyint(1) NOT NULL DEFAULT '0' COMMENT '原帖类型 0表示动态信息 1表示分享商品 默认为0',
  `comment_content` varchar(500) NOT NULL COMMENT '评论内容',
  `comment_addtime` int(11) NOT NULL COMMENT '添加时间',
  `comment_ip` varchar(50) NOT NULL COMMENT '来源IP',
  `comment_state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 0正常 1屏蔽',
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='评论表';

-- ----------------------------
-- Records of sns_comment
-- ----------------------------

-- ----------------------------
-- Table structure for sns_friend
-- ----------------------------
DROP TABLE IF EXISTS `sns_friend`;
CREATE TABLE `sns_friend` (
  `friend_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id值',
  `friend_frommid` int(11) NOT NULL COMMENT '会员id',
  `friend_frommname` varchar(100) DEFAULT NULL COMMENT '会员名称',
  `friend_frommavatar` varchar(100) DEFAULT NULL COMMENT '会员头像',
  `friend_tomid` int(11) NOT NULL COMMENT '朋友id',
  `friend_tomname` varchar(100) NOT NULL COMMENT '好友会员名称',
  `friend_tomavatar` varchar(100) DEFAULT NULL COMMENT '朋友头像',
  `friend_addtime` int(11) NOT NULL COMMENT '添加时间',
  `friend_followstate` tinyint(1) NOT NULL DEFAULT '1' COMMENT '关注状态 1为单方关注 2为双方关注',
  PRIMARY KEY (`friend_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='好友数据表';

-- ----------------------------
-- Records of sns_friend
-- ----------------------------

-- ----------------------------
-- Table structure for sns_goods
-- ----------------------------
DROP TABLE IF EXISTS `sns_goods`;
CREATE TABLE `sns_goods` (
  `snsgoods_goodsid` int(11) NOT NULL COMMENT '商品ID',
  `snsgoods_goodsname` varchar(100) NOT NULL COMMENT '商品名称',
  `snsgoods_goodsimage` varchar(100) DEFAULT NULL COMMENT '商品图片',
  `snsgoods_goodsprice` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品价格',
  `snsgoods_storeid` int(11) NOT NULL COMMENT '店铺ID',
  `snsgoods_storename` varchar(100) NOT NULL COMMENT '店铺名称',
  `snsgoods_addtime` int(11) NOT NULL COMMENT '添加时间',
  `snsgoods_likenum` int(11) NOT NULL DEFAULT '0' COMMENT '喜欢数',
  `snsgoods_likemember` text COMMENT '喜欢过的会员ID，用逗号分隔',
  `snsgoods_sharenum` int(11) NOT NULL DEFAULT '0' COMMENT '分享数',
  UNIQUE KEY `snsgoods_goodsid` (`snsgoods_goodsid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='SNS商品表';

-- ----------------------------
-- Records of sns_goods
-- ----------------------------

-- ----------------------------
-- Table structure for sns_membertag
-- ----------------------------
DROP TABLE IF EXISTS `sns_membertag`;
CREATE TABLE `sns_membertag` (
  `mtag_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '会员标签id',
  `mtag_name` varchar(20) NOT NULL COMMENT '会员标签名称',
  `mtag_sort` tinyint(4) NOT NULL DEFAULT '0' COMMENT '会员标签排序',
  `mtag_recommend` tinyint(4) NOT NULL DEFAULT '0' COMMENT '标签推荐 0未推荐（默认），1为已推荐',
  `mtag_desc` varchar(50) DEFAULT '' COMMENT '标签描述',
  `mtag_img` varchar(50) DEFAULT NULL COMMENT '标签图片',
  PRIMARY KEY (`mtag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员标签表';

-- ----------------------------
-- Records of sns_membertag
-- ----------------------------

-- ----------------------------
-- Table structure for sns_mtagmember
-- ----------------------------
DROP TABLE IF EXISTS `sns_mtagmember`;
CREATE TABLE `sns_mtagmember` (
  `mtag_id` int(11) NOT NULL COMMENT '会员标签表id',
  `member_id` int(11) NOT NULL COMMENT '会员id',
  `recommend` tinyint(4) NOT NULL DEFAULT '0' COMMENT '推荐，默认为0',
  PRIMARY KEY (`mtag_id`,`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员标签会员对照表';

-- ----------------------------
-- Records of sns_mtagmember
-- ----------------------------

-- ----------------------------
-- Table structure for sns_setting
-- ----------------------------
DROP TABLE IF EXISTS `sns_setting`;
CREATE TABLE `sns_setting` (
  `member_id` int(11) NOT NULL COMMENT '会员id',
  `setting_skin` varchar(50) DEFAULT NULL COMMENT '皮肤',
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='个人中心设置表';

-- ----------------------------
-- Records of sns_setting
-- ----------------------------

-- ----------------------------
-- Table structure for sns_sharegoods
-- ----------------------------
DROP TABLE IF EXISTS `sns_sharegoods`;
CREATE TABLE `sns_sharegoods` (
  `share_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `share_goodsid` int(11) NOT NULL COMMENT '商品ID',
  `share_memberid` int(11) NOT NULL COMMENT '所属会员ID',
  `share_membername` varchar(100) NOT NULL COMMENT '会员名称',
  `share_content` varchar(500) DEFAULT NULL COMMENT '描述内容',
  `share_addtime` int(11) NOT NULL DEFAULT '0' COMMENT '分享操作时间',
  `share_likeaddtime` int(11) NOT NULL DEFAULT '0' COMMENT '喜欢操作时间',
  `share_privacy` tinyint(1) NOT NULL DEFAULT '0' COMMENT '隐私可见度 0所有人可见 1好友可见 2仅自己可见',
  `share_commentcount` int(11) NOT NULL DEFAULT '0' COMMENT '评论数',
  `share_isshare` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否分享 0为未分享 1为分享',
  `share_islike` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否喜欢 0为未喜欢 1为喜欢',
  PRIMARY KEY (`share_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='共享商品表';

-- ----------------------------
-- Records of sns_sharegoods
-- ----------------------------

-- ----------------------------
-- Table structure for sns_sharestore
-- ----------------------------
DROP TABLE IF EXISTS `sns_sharestore`;
CREATE TABLE `sns_sharestore` (
  `share_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `share_storeid` int(11) NOT NULL COMMENT '店铺编号',
  `share_storename` varchar(100) NOT NULL COMMENT '店铺名称',
  `share_memberid` int(11) NOT NULL COMMENT '所属会员ID',
  `share_membername` varchar(100) NOT NULL COMMENT '所属会员名称',
  `share_content` varchar(500) DEFAULT NULL COMMENT '描述内容',
  `share_addtime` int(11) NOT NULL COMMENT '添加时间',
  `share_privacy` tinyint(1) NOT NULL DEFAULT '0' COMMENT '隐私可见度 0所有人可见 1好友可见 2仅自己可见',
  PRIMARY KEY (`share_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='共享店铺表';

-- ----------------------------
-- Records of sns_sharestore
-- ----------------------------

-- ----------------------------
-- Table structure for sns_tracelog
-- ----------------------------
DROP TABLE IF EXISTS `sns_tracelog`;
CREATE TABLE `sns_tracelog` (
  `trace_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `trace_originalid` int(11) NOT NULL DEFAULT '0' COMMENT '原动态ID 默认为0',
  `trace_originalmemberid` int(11) NOT NULL DEFAULT '0' COMMENT '原帖会员编号',
  `trace_originalstate` tinyint(1) NOT NULL DEFAULT '0' COMMENT '原帖的删除状态 0为正常 1为删除',
  `trace_memberid` int(11) NOT NULL COMMENT '会员ID',
  `trace_membername` varchar(100) NOT NULL COMMENT '会员名称',
  `trace_memberavatar` varchar(100) DEFAULT NULL COMMENT '会员头像',
  `trace_title` varchar(500) DEFAULT NULL COMMENT '动态标题',
  `trace_content` text NOT NULL COMMENT '动态内容',
  `trace_addtime` int(11) NOT NULL COMMENT '添加时间',
  `trace_state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态  0正常 1为禁止显示 默认为0',
  `trace_privacy` tinyint(1) NOT NULL DEFAULT '0' COMMENT '隐私可见度 0所有人可见 1好友可见 2仅自己可见',
  `trace_commentcount` int(11) NOT NULL DEFAULT '0' COMMENT '评论数',
  `trace_copycount` int(11) NOT NULL DEFAULT '0' COMMENT '转发数',
  `trace_orgcommentcount` int(11) NOT NULL DEFAULT '0' COMMENT '原帖评论次数',
  `trace_orgcopycount` int(11) NOT NULL DEFAULT '0' COMMENT '原帖转帖次数',
  `trace_from` tinyint(4) DEFAULT '1' COMMENT '来源 1=shop 2=storetracelog 3=microshop 4=cms 5=circle',
  PRIMARY KEY (`trace_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='动态信息表';

-- ----------------------------
-- Records of sns_tracelog
-- ----------------------------

-- ----------------------------
-- Table structure for sns_visitor
-- ----------------------------
DROP TABLE IF EXISTS `sns_visitor`;
CREATE TABLE `sns_visitor` (
  `v_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `v_mid` int(11) NOT NULL COMMENT '访客会员ID',
  `v_mname` varchar(100) NOT NULL COMMENT '访客会员名称',
  `v_mavatar` varchar(100) DEFAULT NULL COMMENT '访客会员头像',
  `v_ownermid` int(11) NOT NULL COMMENT '主人会员ID',
  `v_ownermname` varchar(100) NOT NULL COMMENT '主人会员名称',
  `v_ownermavatar` varchar(100) DEFAULT NULL COMMENT '主人会员头像',
  `v_addtime` int(11) NOT NULL COMMENT '访问时间',
  PRIMARY KEY (`v_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='sns访客表';

-- ----------------------------
-- Records of sns_visitor
-- ----------------------------

-- ----------------------------
-- Table structure for spec
-- ----------------------------
DROP TABLE IF EXISTS `spec`;
CREATE TABLE `spec` (
  `sp_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '规格id',
  `sp_name` varchar(100) NOT NULL COMMENT '规格名称',
  `sp_sort` tinyint(1) unsigned NOT NULL COMMENT '排序',
  `class_id` int(10) unsigned DEFAULT '0' COMMENT '所属分类id',
  `class_name` varchar(100) DEFAULT NULL COMMENT '所属分类名称',
  PRIMARY KEY (`sp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品规格表';

-- ----------------------------
-- Records of spec
-- ----------------------------

-- ----------------------------
-- Table structure for spec_value
-- ----------------------------
DROP TABLE IF EXISTS `spec_value`;
CREATE TABLE `spec_value` (
  `sp_value_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '规格值id',
  `sp_value_name` varchar(100) NOT NULL COMMENT '规格值名称',
  `sp_id` int(10) unsigned NOT NULL COMMENT '所属规格id',
  `gc_id` int(10) unsigned NOT NULL COMMENT '分类id',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `sp_value_color` varchar(10) DEFAULT NULL COMMENT '规格颜色',
  `sp_value_sort` tinyint(1) unsigned NOT NULL COMMENT '排序',
  PRIMARY KEY (`sp_value_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品规格值表';

-- ----------------------------
-- Records of spec_value
-- ----------------------------

-- ----------------------------
-- Table structure for stat_member
-- ----------------------------
DROP TABLE IF EXISTS `stat_member`;
CREATE TABLE `stat_member` (
  `statm_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `statm_memberid` int(11) NOT NULL COMMENT '会员ID',
  `statm_membername` varchar(100) NOT NULL COMMENT '会员名称',
  `statm_time` int(11) NOT NULL COMMENT '统计时间，当前按照最小时间单位为天',
  `statm_ordernum` int(11) NOT NULL DEFAULT '0' COMMENT '下单量',
  `statm_orderamount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '下单金额',
  `statm_goodsnum` int(11) NOT NULL DEFAULT '0' COMMENT '下单商品件数',
  `statm_predincrease` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '预存款增加额',
  `statm_predreduce` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '预存款减少额',
  `statm_pointsincrease` int(11) NOT NULL DEFAULT '0' COMMENT '积分增加额',
  `statm_pointsreduce` int(11) NOT NULL DEFAULT '0' COMMENT '积分减少额',
  `statm_updatetime` int(11) NOT NULL COMMENT '记录更新时间',
  PRIMARY KEY (`statm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员相关数据统计';

-- ----------------------------
-- Records of stat_member
-- ----------------------------

-- ----------------------------
-- Table structure for stat_order
-- ----------------------------
DROP TABLE IF EXISTS `stat_order`;
CREATE TABLE `stat_order` (
  `order_id` int(11) NOT NULL COMMENT '订单id',
  `order_sn` bigint(20) unsigned NOT NULL COMMENT '订单编号',
  `order_add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单生成时间(审核时间)',
  `payment_code` char(10) DEFAULT '' COMMENT '支付方式',
  `order_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单总价格',
  `shipping_fee` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '运费',
  `evaluation_state` enum('0','1') DEFAULT '0' COMMENT '评价状态 0未评价，1已评价',
  `order_state` tinyint(4) NOT NULL DEFAULT '10' COMMENT '订单状态：0(已取消)10(默认):未付款;20:已付款;30:已发货;40:已收货;',
  `refund_state` tinyint(1) unsigned DEFAULT '0' COMMENT '退款状态:0是无退款,1是部分退款,2是全部退款',
  `refund_amount` decimal(10,2) DEFAULT '0.00' COMMENT '退款金额',
  `order_from` enum('1','2') NOT NULL DEFAULT '1' COMMENT '1WEB2mobile',
  `order_isvalid` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为计入统计的有效订单，0为无效 1为有效',
  `reciver_province_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '收货人省级ID',
  `store_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺ID',
  `store_name` varchar(50) NOT NULL COMMENT '卖家店铺名称',
  `grade_id` int(11) DEFAULT '0' COMMENT '店铺等级',
  `sc_id` int(11) DEFAULT '0' COMMENT '店铺分类',
  `buyer_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '买家ID',
  `buyer_name` varchar(50) NOT NULL COMMENT '买家姓名',
  `is_zp` tinyint(1) DEFAULT NULL,
  `customer_source_id` int(11) DEFAULT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `seller_name` varchar(16) DEFAULT NULL,
  `rcb_amount` decimal(10,2) DEFAULT NULL,
  `breach_amount` decimal(10,2) DEFAULT NULL,
  `pay_status` tinyint(1) DEFAULT NULL,
  `buyer_phone` bigint(20) NOT NULL,
  `payment_time` int(10) DEFAULT NULL,
  `goods_amount` decimal(12,2) DEFAULT NULL,
  `delay_time` int(10) DEFAULT NULL,
  `finnshed_time` int(10) DEFAULT NULL,
  `remark` text,
  UNIQUE KEY `order_id` (`order_id`),
  KEY `order_add_time` (`order_add_time`),
  KEY `order_isvalid` (`order_isvalid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='统计功能订单缓存表';

-- ----------------------------
-- Records of stat_order
-- ----------------------------

-- ----------------------------
-- Table structure for stat_ordergoods
-- ----------------------------
DROP TABLE IF EXISTS `stat_ordergoods`;
CREATE TABLE `stat_ordergoods` (
  `rec_id` int(11) NOT NULL COMMENT '订单商品表索引id',
  `stat_updatetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '缓存生成时间',
  `order_id` int(11) NOT NULL COMMENT '订单id',
  `order_sn` bigint(20) unsigned NOT NULL COMMENT '订单编号',
  `order_add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单生成时间',
  `payment_code` char(10) DEFAULT '' COMMENT '支付方式',
  `order_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单总价格',
  `shipping_fee` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '运费',
  `evaluation_state` enum('0','1') DEFAULT '0' COMMENT '评价状态 0未评价，1已评价',
  `order_state` tinyint(4) NOT NULL DEFAULT '10' COMMENT '订单状态：0(已取消)10(默认):未付款;20:已付款;30:已发货;40:已收货;',
  `refund_state` tinyint(1) unsigned DEFAULT '0' COMMENT '退款状态:0是无退款,1是部分退款,2是全部退款',
  `refund_amount` decimal(10,2) DEFAULT '0.00' COMMENT '退款金额',
  `order_from` enum('1','2') NOT NULL DEFAULT '1' COMMENT '1WEB2mobile',
  `order_isvalid` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为计入统计的有效订单，0为无效 1为有效',
  `reciver_province_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '收货人省级ID',
  `store_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺ID',
  `store_name` varchar(50) NOT NULL COMMENT '卖家店铺名称',
  `grade_id` int(11) DEFAULT '0' COMMENT '店铺等级',
  `sc_id` int(11) DEFAULT '0' COMMENT '店铺分类',
  `buyer_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '买家ID',
  `buyer_name` varchar(50) NOT NULL COMMENT '买家姓名',
  `goods_id` varchar(16) NOT NULL COMMENT '商品id',
  `goods_name` varchar(50) NOT NULL COMMENT '商品名称(+规格)',
  `goods_commonid` int(10) unsigned NOT NULL COMMENT '商品公共表id',
  `goods_commonname` varchar(50) DEFAULT '' COMMENT '商品公共表中商品名称',
  `gc_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '商品最底级分类ID',
  `gc_parentid_1` int(11) DEFAULT '0' COMMENT '一级父类ID',
  `gc_parentid_2` int(11) DEFAULT '0' COMMENT '二级父类ID',
  `gc_parentid_3` int(11) DEFAULT '0' COMMENT '三级父类ID',
  `brand_id` int(10) unsigned DEFAULT '0' COMMENT '品牌id',
  `brand_name` varchar(100) DEFAULT '' COMMENT '品牌名称',
  `goods_serial` varchar(50) DEFAULT '' COMMENT '商家编号',
  `goods_price` decimal(10,2) NOT NULL COMMENT '商品价格',
  `goods_num` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '商品数量',
  `goods_image` varchar(100) DEFAULT NULL COMMENT '商品图片',
  `goods_pay_price` decimal(10,2) unsigned NOT NULL COMMENT '商品实际成交价',
  `goods_type` enum('1','2','3','4','5') NOT NULL DEFAULT '1' COMMENT '1默认2抢购商品3限时折扣商品4优惠套装5赠品',
  `promotions_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '促销活动ID（抢购ID/限时折扣ID/优惠套装ID）与goods_type搭配使用',
  `commis_rate` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '佣金比例',
  `cat_type` varchar(50) DEFAULT NULL,
  `from_type` tinyint(1) DEFAULT NULL,
  `is_xianhuo` tinyint(1) DEFAULT NULL,
  `style_sn` varchar(50) DEFAULT NULL,
  `tuo_type` tinyint(1) DEFAULT NULL,
  `cert_type` varchar(10) DEFAULT NULL,
  `carat` decimal(6,3) DEFAULT NULL,
  `caizhi` varchar(12) DEFAULT NULL,
  `jinzhong` decimal(6,3) DEFAULT NULL,
  `bc_status` smallint(5) DEFAULT NULL,
  `cert_id` varchar(32) DEFAULT NULL,
  `breach_amount` decimal(10,2) DEFAULT NULL,
  UNIQUE KEY `rec_id` (`rec_id`),
  KEY `order_id` (`order_id`),
  KEY `order_add_time` (`order_add_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='统计功能订单商品缓存表';

-- ----------------------------
-- Records of stat_ordergoods
-- ----------------------------

-- ----------------------------
-- Table structure for std
-- ----------------------------
DROP TABLE IF EXISTS `std`;
CREATE TABLE `std` (
  `store_id` int(11) NOT NULL DEFAULT '0' COMMENT '店铺索引id',
  `store_name` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT '店铺名称',
  `grade_id` int(11) NOT NULL COMMENT '店铺等级',
  `member_id` int(11) NOT NULL COMMENT '会员id',
  `member_name` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT '会员名称',
  `seller_name` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '店主卖家用户名',
  `sc_id` int(11) NOT NULL DEFAULT '0' COMMENT '店铺分类',
  `store_company_name` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '店铺公司名称',
  `province_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '店铺所在省份ID',
  `area_info` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '地区内容，冗余数据',
  `store_address` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '详细地区',
  `store_zip` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '邮政编码',
  `store_state` tinyint(1) NOT NULL DEFAULT '2' COMMENT '店铺状态，0关闭，1开启，2审核中',
  `store_close_info` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '店铺关闭原因',
  `store_sort` int(11) NOT NULL DEFAULT '0' COMMENT '店铺排序',
  `store_time` varchar(10) CHARACTER SET utf8 NOT NULL COMMENT '店铺时间',
  `store_end_time` varchar(10) CHARACTER SET utf8 DEFAULT NULL COMMENT '店铺关闭时间',
  `store_label` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '店铺logo',
  `store_banner` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '店铺横幅',
  `store_avatar` varchar(150) CHARACTER SET utf8 DEFAULT NULL COMMENT '店铺头像',
  `store_keywords` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '店铺seo关键字',
  `store_description` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '店铺seo描述',
  `store_qq` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT 'QQ',
  `store_ww` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '阿里旺旺',
  `store_phone` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '商家电话',
  `store_zy` text CHARACTER SET utf8 COMMENT '主营商品',
  `store_domain` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '店铺二级域名',
  `store_domain_times` tinyint(1) unsigned DEFAULT '0' COMMENT '二级域名修改次数',
  `store_recommend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '推荐，0为否，1为是，默认为0',
  `store_theme` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT 'default' COMMENT '店铺当前主题',
  `store_credit` int(10) NOT NULL DEFAULT '0' COMMENT '店铺信用',
  `store_desccredit` float NOT NULL DEFAULT '0' COMMENT '描述相符度分数',
  `store_servicecredit` float NOT NULL DEFAULT '0' COMMENT '服务态度分数',
  `store_deliverycredit` float NOT NULL DEFAULT '0' COMMENT '发货速度分数',
  `store_collect` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺收藏数量',
  `store_slide` text CHARACTER SET utf8 COMMENT '店铺幻灯片',
  `store_slide_url` text CHARACTER SET utf8 COMMENT '店铺幻灯片链接',
  `store_stamp` varchar(200) CHARACTER SET utf8 DEFAULT NULL COMMENT '店铺印章',
  `store_printdesc` varchar(500) CHARACTER SET utf8 DEFAULT NULL COMMENT '打印订单页面下方说明文字',
  `store_sales` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺销量',
  `store_presales` text CHARACTER SET utf8 COMMENT '售前客服',
  `store_aftersales` text CHARACTER SET utf8 COMMENT '售后客服',
  `store_workingtime` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '工作时间',
  `store_free_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '超出该金额免运费，大于0才表示该值有效',
  `store_decoration_switch` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺装修开关(0-关闭 装修编号-开启)',
  `store_decoration_only` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '开启店铺装修时，仅显示店铺装修(1-是 0-否',
  `store_decoration_image_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺装修相册图片数量',
  `is_own_shop` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否自营店铺 1是 0否',
  `bind_all_gc` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '自营店是否绑定全部分类 0否1是',
  `store_vrcode_prefix` char(3) CHARACTER SET utf8 DEFAULT NULL COMMENT '商家兑换码前缀',
  `mb_title_img` varchar(150) CHARACTER SET utf8 DEFAULT NULL COMMENT '手机店铺 页头背景图',
  `mb_sliders` text CHARACTER SET utf8 COMMENT '手机店铺 轮播图链接地址',
  `left_bar_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '店铺商品页面左侧显示类型 1默认 2商城相关分类品牌商品推荐',
  `deliver_region` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '店铺默认配送区域',
  `is_distribution` int(10) DEFAULT '0' COMMENT '是否分销店铺(0-否，1-是）',
  `is_person` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为个人 1是，0否',
  `store_company_id` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of std
-- ----------------------------

-- ----------------------------
-- Table structure for store
-- ----------------------------
DROP TABLE IF EXISTS `store`;
CREATE TABLE `store` (
  `store_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '店铺索引id',
  `store_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `grade_id` int(11) NOT NULL COMMENT '店铺等级',
  `member_id` int(11) NOT NULL COMMENT '会员id',
  `member_name` varchar(50) NOT NULL COMMENT '会员名称',
  `seller_name` varchar(50) DEFAULT NULL COMMENT '店主卖家用户名',
  `sc_id` int(11) NOT NULL DEFAULT '0' COMMENT '店铺分类',
  `store_company_name` varchar(50) DEFAULT NULL COMMENT '店铺公司名称',
  `province_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '店铺所在省份ID',
  `area_info` varchar(100) NOT NULL DEFAULT '' COMMENT '地区内容，冗余数据',
  `store_address` varchar(100) NOT NULL DEFAULT '' COMMENT '详细地区',
  `store_zip` varchar(10) NOT NULL DEFAULT '' COMMENT '邮政编码',
  `store_state` tinyint(1) NOT NULL DEFAULT '2' COMMENT '店铺状态，0关闭，1开启，2审核中',
  `store_close_info` varchar(255) DEFAULT NULL COMMENT '店铺关闭原因',
  `store_sort` int(11) NOT NULL DEFAULT '0' COMMENT '店铺排序',
  `store_time` varchar(10) NOT NULL COMMENT '店铺时间',
  `store_end_time` varchar(10) DEFAULT NULL COMMENT '店铺关闭时间',
  `store_label` varchar(255) DEFAULT NULL COMMENT '店铺logo',
  `store_banner` varchar(255) DEFAULT NULL COMMENT '店铺横幅',
  `store_avatar` varchar(150) DEFAULT NULL COMMENT '店铺头像',
  `store_keywords` varchar(255) NOT NULL DEFAULT '' COMMENT '店铺seo关键字',
  `store_description` varchar(255) NOT NULL DEFAULT '' COMMENT '店铺seo描述',
  `store_qq` varchar(50) DEFAULT NULL COMMENT 'QQ',
  `store_ww` varchar(50) DEFAULT NULL COMMENT '阿里旺旺',
  `store_phone` varchar(20) DEFAULT NULL COMMENT '商家电话',
  `store_zy` text COMMENT '主营商品',
  `store_domain` varchar(50) DEFAULT NULL COMMENT '店铺二级域名',
  `store_domain_times` tinyint(1) unsigned DEFAULT '0' COMMENT '二级域名修改次数',
  `store_recommend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '推荐，0为否，1为是，默认为0',
  `store_theme` varchar(50) NOT NULL DEFAULT 'default' COMMENT '店铺当前主题',
  `store_credit` int(10) NOT NULL DEFAULT '0' COMMENT '店铺信用',
  `store_desccredit` float NOT NULL DEFAULT '0' COMMENT '描述相符度分数',
  `store_servicecredit` float NOT NULL DEFAULT '0' COMMENT '服务态度分数',
  `store_deliverycredit` float NOT NULL DEFAULT '0' COMMENT '发货速度分数',
  `store_collect` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺收藏数量',
  `store_slide` text COMMENT '店铺幻灯片',
  `store_slide_url` text COMMENT '店铺幻灯片链接',
  `store_stamp` varchar(200) DEFAULT NULL COMMENT '店铺印章',
  `store_printdesc` varchar(500) DEFAULT NULL COMMENT '打印订单页面下方说明文字',
  `store_sales` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺销量',
  `store_presales` text COMMENT '售前客服',
  `store_aftersales` text COMMENT '售后客服',
  `store_workingtime` varchar(100) DEFAULT NULL COMMENT '工作时间',
  `store_free_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '超出该金额免运费，大于0才表示该值有效',
  `store_decoration_switch` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺装修开关(0-关闭 装修编号-开启)',
  `store_decoration_only` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '开启店铺装修时，仅显示店铺装修(1-是 0-否',
  `store_decoration_image_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺装修相册图片数量',
  `is_own_shop` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否自营店铺 1是 0否',
  `bind_all_gc` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '自营店是否绑定全部分类 0否1是',
  `store_vrcode_prefix` char(3) DEFAULT NULL COMMENT '商家兑换码前缀',
  `mb_title_img` varchar(150) DEFAULT NULL COMMENT '手机店铺 页头背景图',
  `mb_sliders` text COMMENT '手机店铺 轮播图链接地址',
  `left_bar_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '店铺商品页面左侧显示类型 1默认 2商城相关分类品牌商品推荐',
  `deliver_region` varchar(50) DEFAULT NULL COMMENT '店铺默认配送区域',
  `is_distribution` int(10) DEFAULT '0' COMMENT '是否分销店铺(0-否，1-是）',
  `is_person` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为个人 1是，0否',
  `store_company_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`store_id`),
  KEY `store_name` (`store_name`),
  KEY `sc_id` (`sc_id`),
  KEY `store_state` (`store_state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺数据表';

-- ----------------------------
-- Records of store
-- ----------------------------

-- ----------------------------
-- Table structure for store_bind_class
-- ----------------------------
DROP TABLE IF EXISTS `store_bind_class`;
CREATE TABLE `store_bind_class` (
  `bid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) unsigned DEFAULT '0' COMMENT '店铺ID',
  `commis_rate` tinyint(4) unsigned DEFAULT '0' COMMENT '佣金比例',
  `class_1` mediumint(9) unsigned DEFAULT '0' COMMENT '一级分类',
  `class_2` mediumint(9) unsigned DEFAULT '0' COMMENT '二级分类',
  `class_3` mediumint(9) unsigned DEFAULT '0' COMMENT '三级分类',
  `state` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态0审核中1已审核 2平台自营店铺',
  PRIMARY KEY (`bid`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺可发布商品类目表';

-- ----------------------------
-- Records of store_bind_class
-- ----------------------------

-- ----------------------------
-- Table structure for store_class
-- ----------------------------
DROP TABLE IF EXISTS `store_class`;
CREATE TABLE `store_class` (
  `sc_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引ID',
  `sc_name` varchar(50) NOT NULL COMMENT '分类名称',
  `sc_bail` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '保证金数额',
  `sc_sort` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`sc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺分类表';

-- ----------------------------
-- Records of store_class
-- ----------------------------

-- ----------------------------
-- Table structure for store_cost
-- ----------------------------
DROP TABLE IF EXISTS `store_cost`;
CREATE TABLE `store_cost` (
  `cost_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '费用编号',
  `cost_store_id` int(10) unsigned NOT NULL COMMENT '店铺编号',
  `cost_seller_id` int(10) unsigned NOT NULL COMMENT '卖家编号',
  `cost_price` int(10) unsigned NOT NULL COMMENT '价格',
  `cost_remark` varchar(255) NOT NULL COMMENT '费用备注',
  `cost_state` tinyint(3) unsigned NOT NULL COMMENT '费用状态(0-未结算 1-已结算)',
  `cost_time` int(10) unsigned NOT NULL COMMENT '费用发生时间',
  PRIMARY KEY (`cost_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺费用表';

-- ----------------------------
-- Records of store_cost
-- ----------------------------

-- ----------------------------
-- Table structure for store_cron_record
-- ----------------------------
DROP TABLE IF EXISTS `store_cron_record`;
CREATE TABLE `store_cron_record` (
  `store_id` int(255) DEFAULT NULL,
  `style_index_build_time` int(11) DEFAULT NULL,
  UNIQUE KEY `store_id` (`store_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of store_cron_record
-- ----------------------------

-- ----------------------------
-- Table structure for store_decoration
-- ----------------------------
DROP TABLE IF EXISTS `store_decoration`;
CREATE TABLE `store_decoration` (
  `decoration_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '装修编号',
  `decoration_name` varchar(50) NOT NULL COMMENT '装修名称',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺编号',
  `decoration_setting` varchar(500) DEFAULT NULL COMMENT '装修整体设置(背景、边距等)',
  `decoration_nav` varchar(5000) DEFAULT NULL COMMENT '装修导航',
  `decoration_banner` varchar(255) DEFAULT NULL COMMENT '装修头部banner',
  PRIMARY KEY (`decoration_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺装修表';

-- ----------------------------
-- Records of store_decoration
-- ----------------------------

-- ----------------------------
-- Table structure for store_decoration_album
-- ----------------------------
DROP TABLE IF EXISTS `store_decoration_album`;
CREATE TABLE `store_decoration_album` (
  `image_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '图片编号',
  `image_name` varchar(50) NOT NULL COMMENT '图片名称',
  `image_origin_name` varchar(50) NOT NULL COMMENT '图片原始名称',
  `image_width` int(10) unsigned NOT NULL COMMENT '图片宽度',
  `image_height` int(10) unsigned NOT NULL COMMENT '图片高度',
  `image_size` int(10) unsigned NOT NULL COMMENT '图片大小',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺编号',
  `upload_time` int(10) unsigned NOT NULL COMMENT '上传时间',
  PRIMARY KEY (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺装修相册表';

-- ----------------------------
-- Records of store_decoration_album
-- ----------------------------

-- ----------------------------
-- Table structure for store_decoration_block
-- ----------------------------
DROP TABLE IF EXISTS `store_decoration_block`;
CREATE TABLE `store_decoration_block` (
  `block_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '装修块编号',
  `decoration_id` int(10) unsigned NOT NULL COMMENT '装修编号',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺编号',
  `block_layout` varchar(50) NOT NULL COMMENT '块布局',
  `block_content` text COMMENT '块内容',
  `block_module_type` varchar(50) DEFAULT NULL COMMENT '装修块模块类型',
  `block_full_width` tinyint(3) unsigned DEFAULT NULL COMMENT '是否100%宽度(0-否 1-是)',
  `block_sort` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '块排序',
  PRIMARY KEY (`block_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺装修块表';

-- ----------------------------
-- Records of store_decoration_block
-- ----------------------------

-- ----------------------------
-- Table structure for store_distribution
-- ----------------------------
DROP TABLE IF EXISTS `store_distribution`;
CREATE TABLE `store_distribution` (
  `distri_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增编号',
  `distri_store_id` int(11) NOT NULL COMMENT '申请者店铺ID',
  `distri_store_name` varchar(255) NOT NULL COMMENT '申请者店铺名称',
  `distri_seller_name` varchar(255) NOT NULL COMMENT '店主名称',
  `distri_state` int(11) NOT NULL COMMENT '申请状态0未通过1通过',
  `distri_create_time` int(11) NOT NULL COMMENT '申请时间',
  PRIMARY KEY (`distri_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺可发布商品类目表';

-- ----------------------------
-- Records of store_distribution
-- ----------------------------

-- ----------------------------
-- Table structure for store_extend
-- ----------------------------
DROP TABLE IF EXISTS `store_extend`;
CREATE TABLE `store_extend` (
  `store_id` mediumint(8) unsigned NOT NULL COMMENT '店铺ID',
  `express` text COMMENT '快递公司ID的组合',
  `pricerange` text COMMENT '店铺统计设置的商品价格区间',
  `orderpricerange` text COMMENT '店铺统计设置的订单价格区间',
  `bill_cycle` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '结算周期，单位天，默认0表示未设置，还是按月结算',
  PRIMARY KEY (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺信息扩展表';

-- ----------------------------
-- Records of store_extend
-- ----------------------------

-- ----------------------------
-- Table structure for store_goods_class
-- ----------------------------
DROP TABLE IF EXISTS `store_goods_class`;
CREATE TABLE `store_goods_class` (
  `stc_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '索引ID',
  `stc_name` varchar(50) NOT NULL COMMENT '店铺商品分类名称',
  `stc_parent_id` int(11) NOT NULL COMMENT '父级id',
  `stc_state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '店铺商品分类状态',
  `store_id` int(11) NOT NULL DEFAULT '0' COMMENT '店铺id',
  `stc_sort` int(11) NOT NULL DEFAULT '0' COMMENT '商品分类排序',
  PRIMARY KEY (`stc_id`),
  KEY `stc_parent_id` (`stc_parent_id`,`stc_sort`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺商品分类表';

-- ----------------------------
-- Records of store_goods_class
-- ----------------------------

-- ----------------------------
-- Table structure for store_grade
-- ----------------------------
DROP TABLE IF EXISTS `store_grade`;
CREATE TABLE `store_grade` (
  `sg_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引ID',
  `sg_name` char(50) DEFAULT NULL COMMENT '等级名称',
  `sg_goods_limit` mediumint(10) unsigned NOT NULL DEFAULT '0' COMMENT '允许发布的商品数量',
  `sg_album_limit` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '允许上传图片数量',
  `sg_space_limit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传空间大小，单位MB',
  `sg_template_number` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '选择店铺模板套数',
  `sg_template` varchar(255) DEFAULT NULL COMMENT '模板内容',
  `sg_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '开店费用(元/年)',
  `sg_description` text COMMENT '申请说明',
  `sg_function` varchar(255) DEFAULT NULL COMMENT '附加功能',
  `sg_sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '级别，数目越大级别越高',
  PRIMARY KEY (`sg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺等级表';

-- ----------------------------
-- Records of store_grade
-- ----------------------------

-- ----------------------------
-- Table structure for store_joinin
-- ----------------------------
DROP TABLE IF EXISTS `store_joinin`;
CREATE TABLE `store_joinin` (
  `member_id` int(10) unsigned NOT NULL COMMENT '用户编号',
  `member_name` varchar(50) DEFAULT NULL COMMENT '店主用户名',
  `company_name` varchar(50) DEFAULT NULL COMMENT '公司名称',
  `company_province_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '所在地省ID',
  `company_address` varchar(50) DEFAULT NULL COMMENT '公司地址',
  `company_address_detail` varchar(50) DEFAULT NULL COMMENT '公司详细地址',
  `company_phone` varchar(20) DEFAULT NULL COMMENT '公司电话',
  `company_employee_count` int(10) unsigned DEFAULT NULL COMMENT '员工总数',
  `company_registered_capital` int(10) unsigned DEFAULT NULL COMMENT '注册资金',
  `contacts_name` varchar(50) DEFAULT NULL COMMENT '联系人姓名',
  `contacts_phone` varchar(20) DEFAULT NULL COMMENT '联系人电话',
  `contacts_email` varchar(50) DEFAULT NULL COMMENT '联系人邮箱',
  `business_licence_number` varchar(50) DEFAULT NULL COMMENT '营业执照号',
  `business_licence_address` varchar(50) DEFAULT NULL COMMENT '营业执所在地',
  `business_licence_start` date DEFAULT NULL COMMENT '营业执照有效期开始',
  `business_licence_end` date DEFAULT NULL COMMENT '营业执照有效期结束',
  `business_sphere` varchar(1000) DEFAULT NULL COMMENT '法定经营范围',
  `business_licence_number_elc` varchar(50) DEFAULT NULL COMMENT '营业执照电子版',
  `organization_code` varchar(50) DEFAULT NULL COMMENT '组织机构代码',
  `organization_code_electronic` varchar(50) DEFAULT NULL COMMENT '组织机构代码电子版',
  `general_taxpayer` varchar(50) DEFAULT NULL COMMENT '一般纳税人证明',
  `bank_account_name` varchar(50) DEFAULT NULL COMMENT '银行开户名',
  `bank_account_number` varchar(50) DEFAULT NULL COMMENT '公司银行账号',
  `bank_name` varchar(50) DEFAULT NULL COMMENT '开户银行支行名称',
  `bank_code` varchar(50) DEFAULT NULL COMMENT '支行联行号',
  `bank_address` varchar(50) DEFAULT NULL COMMENT '开户银行所在地',
  `bank_licence_electronic` varchar(50) DEFAULT NULL COMMENT '开户银行许可证电子版',
  `is_settlement_account` tinyint(1) DEFAULT NULL COMMENT '开户行账号是否为结算账号 1-开户行就是结算账号 2-独立的计算账号',
  `settlement_bank_account_name` varchar(50) DEFAULT NULL COMMENT '结算银行开户名',
  `settlement_bank_account_number` varchar(50) DEFAULT NULL COMMENT '结算公司银行账号',
  `settlement_bank_name` varchar(50) DEFAULT NULL COMMENT '结算开户银行支行名称',
  `settlement_bank_code` varchar(50) DEFAULT NULL COMMENT '结算支行联行号',
  `settlement_bank_address` varchar(50) DEFAULT NULL COMMENT '结算开户银行所在地',
  `tax_registration_certificate` varchar(50) DEFAULT NULL COMMENT '税务登记证号',
  `taxpayer_id` varchar(50) DEFAULT NULL COMMENT '纳税人识别号',
  `tax_registration_certif_elc` varchar(50) DEFAULT NULL COMMENT '税务登记证号电子版',
  `seller_name` varchar(50) DEFAULT NULL COMMENT '卖家账号',
  `store_name` varchar(50) DEFAULT NULL COMMENT '店铺名称',
  `store_class_ids` varchar(1000) DEFAULT NULL COMMENT '店铺分类编号集合',
  `store_class_names` varchar(1000) DEFAULT NULL COMMENT '店铺分类名称集合',
  `joinin_state` varchar(50) DEFAULT NULL COMMENT '申请状态 10-已提交申请 11-缴费完成  20-审核成功 30-审核失败 31-缴费审核失败 40-审核通过开店',
  `joinin_message` varchar(200) DEFAULT NULL COMMENT '管理员审核信息',
  `joinin_year` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '开店时长(年)',
  `sg_name` varchar(50) DEFAULT NULL COMMENT '店铺等级名称',
  `sg_id` int(10) unsigned DEFAULT NULL COMMENT '店铺等级编号',
  `sg_info` varchar(200) DEFAULT NULL COMMENT '店铺等级下的收费等信息',
  `sc_name` varchar(50) DEFAULT NULL COMMENT '店铺分类名称',
  `sc_id` int(10) unsigned DEFAULT NULL COMMENT '店铺分类编号',
  `sc_bail` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '店铺分类保证金',
  `store_class_commis_rates` varchar(200) DEFAULT NULL COMMENT '分类佣金比例',
  `paying_money_certificate` varchar(50) DEFAULT NULL COMMENT '付款凭证',
  `paying_money_certif_exp` varchar(200) DEFAULT NULL COMMENT '付款凭证说明',
  `paying_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '付款金额',
  `is_person` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为个人 1是，0否',
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺入住表';

-- ----------------------------
-- Records of store_joinin
-- ----------------------------

-- ----------------------------
-- Table structure for store_map
-- ----------------------------
DROP TABLE IF EXISTS `store_map`;
CREATE TABLE `store_map` (
  `map_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '地图ID',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺ID',
  `sc_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺分类ID',
  `store_name` varchar(20) NOT NULL COMMENT '店铺名称',
  `name_info` varchar(20) NOT NULL COMMENT '详细名称',
  `address_info` varchar(30) NOT NULL COMMENT '详细地址',
  `phone_info` varchar(50) DEFAULT '' COMMENT '电话信息',
  `bus_info` varchar(250) DEFAULT '' COMMENT '公交信息',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  `baidu_lng` float NOT NULL DEFAULT '0' COMMENT '百度经度',
  `baidu_lat` float NOT NULL DEFAULT '0' COMMENT '百度纬度',
  `baidu_province` varchar(15) NOT NULL COMMENT '百度省份',
  `baidu_city` varchar(15) NOT NULL COMMENT '百度城市',
  `baidu_district` varchar(15) NOT NULL COMMENT '百度区县',
  `baidu_street` varchar(15) DEFAULT '' COMMENT '百度街道',
  PRIMARY KEY (`map_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺百度地图表';

-- ----------------------------
-- Records of store_map
-- ----------------------------

-- ----------------------------
-- Table structure for store_msg
-- ----------------------------
DROP TABLE IF EXISTS `store_msg`;
CREATE TABLE `store_msg` (
  `sm_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '店铺消息id',
  `smt_code` varchar(100) NOT NULL COMMENT '模板编码',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `sm_content` varchar(255) NOT NULL COMMENT '消息内容',
  `sm_addtime` int(10) unsigned NOT NULL COMMENT '发送时间',
  `sm_readids` varchar(255) DEFAULT '' COMMENT '已读卖家id',
  PRIMARY KEY (`sm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺消息表';

-- ----------------------------
-- Records of store_msg
-- ----------------------------

-- ----------------------------
-- Table structure for store_msg_read
-- ----------------------------
DROP TABLE IF EXISTS `store_msg_read`;
CREATE TABLE `store_msg_read` (
  `sm_id` int(11) NOT NULL COMMENT '店铺消息id',
  `seller_id` int(11) NOT NULL COMMENT '卖家id',
  `read_time` int(11) NOT NULL COMMENT '阅读时间',
  PRIMARY KEY (`sm_id`,`seller_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺消息阅读表';

-- ----------------------------
-- Records of store_msg_read
-- ----------------------------

-- ----------------------------
-- Table structure for store_msg_setting
-- ----------------------------
DROP TABLE IF EXISTS `store_msg_setting`;
CREATE TABLE `store_msg_setting` (
  `smt_code` varchar(100) NOT NULL COMMENT '模板编码',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `sms_message_switch` tinyint(3) unsigned NOT NULL COMMENT '站内信接收开关，0关闭，1开启',
  `sms_short_switch` tinyint(3) unsigned NOT NULL COMMENT '短消息接收开关，0关闭，1开启',
  `sms_mail_switch` tinyint(3) unsigned NOT NULL COMMENT '邮件接收开关，0关闭，1开启',
  `sms_short_number` varchar(11) DEFAULT '' COMMENT '手机号码',
  `sms_mail_number` varchar(100) DEFAULT '' COMMENT '邮箱号码',
  PRIMARY KEY (`smt_code`,`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺消息接收设置';

-- ----------------------------
-- Records of store_msg_setting
-- ----------------------------

-- ----------------------------
-- Table structure for store_msg_tpl
-- ----------------------------
DROP TABLE IF EXISTS `store_msg_tpl`;
CREATE TABLE `store_msg_tpl` (
  `smt_code` varchar(100) NOT NULL COMMENT '模板编码',
  `smt_name` varchar(100) NOT NULL COMMENT '模板名称',
  `smt_message_switch` tinyint(3) unsigned NOT NULL COMMENT '站内信默认开关，0关，1开',
  `smt_message_content` varchar(255) NOT NULL COMMENT '站内信内容',
  `smt_message_forced` tinyint(3) unsigned NOT NULL COMMENT '站内信强制接收，0否，1是',
  `smt_short_switch` tinyint(3) unsigned NOT NULL COMMENT '短信默认开关，0关，1开',
  `smt_short_content` varchar(255) NOT NULL COMMENT '短信内容',
  `smt_short_forced` tinyint(3) unsigned NOT NULL COMMENT '短信强制接收，0否，1是',
  `smt_mail_switch` tinyint(3) unsigned NOT NULL COMMENT '邮件默认开，0关，1开',
  `smt_mail_subject` varchar(255) NOT NULL COMMENT '邮件标题',
  `smt_mail_content` text NOT NULL COMMENT '邮件内容',
  `smt_mail_forced` tinyint(3) unsigned NOT NULL COMMENT '邮件强制接收，0否，1是',
  PRIMARY KEY (`smt_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商家消息模板';

-- ----------------------------
-- Records of store_msg_tpl
-- ----------------------------

-- ----------------------------
-- Table structure for store_navigation
-- ----------------------------
DROP TABLE IF EXISTS `store_navigation`;
CREATE TABLE `store_navigation` (
  `sn_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '导航ID',
  `sn_title` varchar(50) NOT NULL COMMENT '导航名称',
  `sn_store_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '卖家店铺ID',
  `sn_content` text COMMENT '导航内容',
  `sn_sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '导航排序',
  `sn_if_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '导航是否显示',
  `sn_add_time` int(10) NOT NULL COMMENT '导航',
  `sn_url` varchar(255) DEFAULT NULL COMMENT '店铺导航的外链URL',
  `sn_new_open` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '店铺导航外链是否在新窗口打开：0不开新窗口1开新窗口，默认是0',
  PRIMARY KEY (`sn_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='卖家店铺导航信息表';

-- ----------------------------
-- Records of store_navigation
-- ----------------------------

-- ----------------------------
-- Table structure for store_plate
-- ----------------------------
DROP TABLE IF EXISTS `store_plate`;
CREATE TABLE `store_plate` (
  `plate_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '关联板式id',
  `plate_name` varchar(10) NOT NULL COMMENT '关联板式名称',
  `plate_position` tinyint(3) unsigned NOT NULL COMMENT '关联板式位置 1顶部，0底部',
  `plate_content` text NOT NULL COMMENT '关联板式内容',
  `store_id` int(10) unsigned NOT NULL COMMENT '所属店铺id',
  PRIMARY KEY (`plate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='关联板式表';

-- ----------------------------
-- Records of store_plate
-- ----------------------------

-- ----------------------------
-- Table structure for store_raw
-- ----------------------------
DROP TABLE IF EXISTS `store_raw`;
CREATE TABLE `store_raw` (
  `store_id` int(11) NOT NULL DEFAULT '0' COMMENT '店铺索引id',
  `store_name` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT '店铺名称',
  `grade_id` int(11) NOT NULL COMMENT '店铺等级',
  `member_id` int(11) NOT NULL COMMENT '会员id',
  `member_name` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT '会员名称',
  `seller_name` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '店主卖家用户名',
  `sc_id` int(11) NOT NULL DEFAULT '0' COMMENT '店铺分类',
  `store_company_name` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '店铺公司名称',
  `province_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '店铺所在省份ID',
  `area_info` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '地区内容，冗余数据',
  `store_address` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '详细地区',
  `store_zip` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '邮政编码',
  `store_state` tinyint(1) NOT NULL DEFAULT '2' COMMENT '店铺状态，0关闭，1开启，2审核中',
  `store_close_info` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '店铺关闭原因',
  `store_sort` int(11) NOT NULL DEFAULT '0' COMMENT '店铺排序',
  `store_time` varchar(10) CHARACTER SET utf8 NOT NULL COMMENT '店铺时间',
  `store_end_time` varchar(10) CHARACTER SET utf8 DEFAULT NULL COMMENT '店铺关闭时间',
  `store_label` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '店铺logo',
  `store_banner` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '店铺横幅',
  `store_avatar` varchar(150) CHARACTER SET utf8 DEFAULT NULL COMMENT '店铺头像',
  `store_keywords` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '店铺seo关键字',
  `store_description` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '店铺seo描述',
  `store_qq` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT 'QQ',
  `store_ww` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '阿里旺旺',
  `store_phone` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '商家电话',
  `store_zy` text CHARACTER SET utf8 COMMENT '主营商品',
  `store_domain` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '店铺二级域名',
  `store_domain_times` tinyint(1) unsigned DEFAULT '0' COMMENT '二级域名修改次数',
  `store_recommend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '推荐，0为否，1为是，默认为0',
  `store_theme` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT 'default' COMMENT '店铺当前主题',
  `store_credit` int(10) NOT NULL DEFAULT '0' COMMENT '店铺信用',
  `store_desccredit` float NOT NULL DEFAULT '0' COMMENT '描述相符度分数',
  `store_servicecredit` float NOT NULL DEFAULT '0' COMMENT '服务态度分数',
  `store_deliverycredit` float NOT NULL DEFAULT '0' COMMENT '发货速度分数',
  `store_collect` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺收藏数量',
  `store_slide` text CHARACTER SET utf8 COMMENT '店铺幻灯片',
  `store_slide_url` text CHARACTER SET utf8 COMMENT '店铺幻灯片链接',
  `store_stamp` varchar(200) CHARACTER SET utf8 DEFAULT NULL COMMENT '店铺印章',
  `store_printdesc` varchar(500) CHARACTER SET utf8 DEFAULT NULL COMMENT '打印订单页面下方说明文字',
  `store_sales` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺销量',
  `store_presales` text CHARACTER SET utf8 COMMENT '售前客服',
  `store_aftersales` text CHARACTER SET utf8 COMMENT '售后客服',
  `store_workingtime` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '工作时间',
  `store_free_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '超出该金额免运费，大于0才表示该值有效',
  `store_decoration_switch` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺装修开关(0-关闭 装修编号-开启)',
  `store_decoration_only` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '开启店铺装修时，仅显示店铺装修(1-是 0-否',
  `store_decoration_image_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺装修相册图片数量',
  `is_own_shop` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否自营店铺 1是 0否',
  `bind_all_gc` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '自营店是否绑定全部分类 0否1是',
  `store_vrcode_prefix` char(3) CHARACTER SET utf8 DEFAULT NULL COMMENT '商家兑换码前缀',
  `mb_title_img` varchar(150) CHARACTER SET utf8 DEFAULT NULL COMMENT '手机店铺 页头背景图',
  `mb_sliders` text CHARACTER SET utf8 COMMENT '手机店铺 轮播图链接地址',
  `left_bar_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '店铺商品页面左侧显示类型 1默认 2商城相关分类品牌商品推荐',
  `deliver_region` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '店铺默认配送区域',
  `is_distribution` int(10) DEFAULT '0' COMMENT '是否分销店铺(0-否，1-是）',
  `is_person` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为个人 1是，0否',
  `store_company_id` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of store_raw
-- ----------------------------

-- ----------------------------
-- Table structure for store_reopen
-- ----------------------------
DROP TABLE IF EXISTS `store_reopen`;
CREATE TABLE `store_reopen` (
  `re_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `re_grade_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '店铺等级ID',
  `re_grade_name` varchar(30) DEFAULT NULL COMMENT '等级名称',
  `re_grade_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '等级收费(元/年)',
  `re_year` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '续签时长(年)',
  `re_pay_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '应付总金额',
  `re_store_name` varchar(50) DEFAULT NULL COMMENT '店铺名字',
  `re_store_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺ID',
  `re_state` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态0默认，未上传凭证1审核中2审核通过',
  `re_start_time` int(10) unsigned DEFAULT NULL COMMENT '有效期开始时间',
  `re_end_time` int(10) unsigned DEFAULT NULL COMMENT '有效期结束时间',
  `re_create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '记录创建时间',
  `re_pay_cert` varchar(50) DEFAULT NULL COMMENT '付款凭证',
  `re_pay_cert_explain` varchar(200) DEFAULT NULL COMMENT '付款凭证说明',
  PRIMARY KEY (`re_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='续签内容表';

-- ----------------------------
-- Records of store_reopen
-- ----------------------------

-- ----------------------------
-- Table structure for store_sns_comment
-- ----------------------------
DROP TABLE IF EXISTS `store_sns_comment`;
CREATE TABLE `store_sns_comment` (
  `scomm_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '店铺动态评论id',
  `strace_id` int(11) NOT NULL COMMENT '店铺动态id',
  `scomm_content` varchar(150) DEFAULT NULL COMMENT '评论内容',
  `scomm_memberid` int(11) DEFAULT NULL COMMENT '会员id',
  `scomm_membername` varchar(45) DEFAULT NULL COMMENT '会员名称',
  `scomm_memberavatar` varchar(50) DEFAULT NULL COMMENT '会员头像',
  `scomm_time` varchar(11) DEFAULT NULL COMMENT '评论时间',
  `scomm_state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '评论状态 1正常，0屏蔽',
  PRIMARY KEY (`scomm_id`),
  UNIQUE KEY `scomm_id` (`scomm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺动态评论表';

-- ----------------------------
-- Records of store_sns_comment
-- ----------------------------

-- ----------------------------
-- Table structure for store_sns_setting
-- ----------------------------
DROP TABLE IF EXISTS `store_sns_setting`;
CREATE TABLE `store_sns_setting` (
  `sauto_storeid` int(11) NOT NULL COMMENT '店铺id',
  `sauto_new` tinyint(4) NOT NULL DEFAULT '1' COMMENT '新品,0为关闭/1为开启',
  `sauto_newtitle` varchar(150) DEFAULT '' COMMENT '新品内容',
  `sauto_coupon` tinyint(4) NOT NULL DEFAULT '1' COMMENT '优惠券,0为关闭/1为开启',
  `sauto_coupontitle` varchar(150) DEFAULT '' COMMENT '优惠券内容',
  `sauto_xianshi` tinyint(4) NOT NULL DEFAULT '1' COMMENT '限时折扣,0为关闭/1为开启',
  `sauto_xianshititle` varchar(150) DEFAULT '' COMMENT '限时折扣内容',
  `sauto_mansong` tinyint(4) NOT NULL DEFAULT '1' COMMENT '满即送,0为关闭/1为开启',
  `sauto_mansongtitle` varchar(150) DEFAULT '' COMMENT '满即送内容',
  `sauto_bundling` tinyint(4) NOT NULL DEFAULT '1' COMMENT '组合销售,0为关闭/1为开启',
  `sauto_bundlingtitle` varchar(150) DEFAULT '' COMMENT '组合销售内容',
  `sauto_groupbuy` tinyint(4) NOT NULL DEFAULT '1' COMMENT '抢购,0为关闭/1为开启',
  `sauto_groupbuytitle` varchar(150) DEFAULT '' COMMENT '抢购内容',
  PRIMARY KEY (`sauto_storeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺自动发布动态设置表';

-- ----------------------------
-- Records of store_sns_setting
-- ----------------------------

-- ----------------------------
-- Table structure for store_sns_tracelog
-- ----------------------------
DROP TABLE IF EXISTS `store_sns_tracelog`;
CREATE TABLE `store_sns_tracelog` (
  `strace_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '店铺动态id',
  `strace_storeid` int(11) DEFAULT NULL COMMENT '店铺id',
  `strace_storename` varchar(100) DEFAULT NULL COMMENT '店铺名称',
  `strace_storelogo` varchar(255) DEFAULT '' COMMENT '店标',
  `strace_title` varchar(150) DEFAULT NULL COMMENT '动态标题',
  `strace_content` text COMMENT '发表内容',
  `strace_time` varchar(11) DEFAULT NULL COMMENT '发表时间',
  `strace_cool` int(11) DEFAULT '0' COMMENT '赞数量',
  `strace_spread` int(11) DEFAULT '0' COMMENT '转播数量',
  `strace_comment` int(11) DEFAULT '0' COMMENT '评论数量',
  `strace_type` tinyint(4) DEFAULT '1' COMMENT '1=relay,2=normal,3=new,4=coupon,5=xianshi,6=mansong,7=bundling,8=groupbuy,9=recommend,10=hotsell',
  `strace_goodsdata` text COMMENT '商品信息',
  `strace_state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '动态状态 1正常，0屏蔽',
  PRIMARY KEY (`strace_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺动态表';

-- ----------------------------
-- Records of store_sns_tracelog
-- ----------------------------

-- ----------------------------
-- Table structure for store_supplier
-- ----------------------------
DROP TABLE IF EXISTS `store_supplier`;
CREATE TABLE `store_supplier` (
  `sup_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sup_store_id` int(11) DEFAULT NULL COMMENT '商家ID',
  `sup_store_name` varchar(50) DEFAULT NULL COMMENT '商家名称',
  `sup_name` varchar(50) DEFAULT NULL COMMENT '供货商名称',
  `sup_desc` varchar(200) DEFAULT NULL COMMENT '备注',
  `sup_man` varchar(30) DEFAULT NULL COMMENT '联系人',
  `sup_phone` varchar(30) DEFAULT NULL COMMENT '联系电话',
  `is_enabled` tinyint(1) unsigned DEFAULT '1' COMMENT '是否有效',
  PRIMARY KEY (`sup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='供货商表';

-- ----------------------------
-- Records of store_supplier
-- ----------------------------

-- ----------------------------
-- Table structure for store_watermark
-- ----------------------------
DROP TABLE IF EXISTS `store_watermark`;
CREATE TABLE `store_watermark` (
  `wm_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '水印id',
  `jpeg_quality` int(3) NOT NULL DEFAULT '90' COMMENT 'jpeg图片质量',
  `wm_image_name` varchar(255) DEFAULT NULL COMMENT '水印图片的路径以及文件名',
  `wm_image_pos` tinyint(1) NOT NULL DEFAULT '1' COMMENT '水印图片放置的位置',
  `wm_image_transition` int(3) NOT NULL DEFAULT '20' COMMENT '水印图片与原图片的融合度 ',
  `wm_text` text COMMENT '水印文字',
  `wm_text_size` int(3) NOT NULL DEFAULT '20' COMMENT '水印文字大小',
  `wm_text_angle` tinyint(1) NOT NULL DEFAULT '4' COMMENT '水印文字角度',
  `wm_text_pos` tinyint(1) NOT NULL DEFAULT '3' COMMENT '水印文字放置位置',
  `wm_text_font` varchar(50) DEFAULT NULL COMMENT '水印文字的字体',
  `wm_text_color` varchar(7) NOT NULL DEFAULT '#CCCCCC' COMMENT '水印字体的颜色值',
  `wm_is_open` tinyint(1) NOT NULL DEFAULT '0' COMMENT '水印是否开启 0关闭 1开启',
  `store_id` int(11) DEFAULT NULL COMMENT '店铺id',
  PRIMARY KEY (`wm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺水印图片表';

-- ----------------------------
-- Records of store_watermark
-- ----------------------------

-- ----------------------------
-- Table structure for store_waybill
-- ----------------------------
DROP TABLE IF EXISTS `store_waybill`;
CREATE TABLE `store_waybill` (
  `store_waybill_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '店铺运单模板编号',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺编号',
  `express_id` int(10) unsigned NOT NULL COMMENT '物流公司编号',
  `waybill_id` int(10) unsigned NOT NULL COMMENT '运单模板编号',
  `waybill_name` varchar(50) NOT NULL COMMENT '运单模板名称',
  `store_waybill_data` varchar(2000) DEFAULT NULL COMMENT '店铺自定义设置',
  `is_default` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否默认模板',
  `store_waybill_left` int(11) NOT NULL DEFAULT '0' COMMENT '店铺运单左偏移',
  `store_waybill_top` int(11) NOT NULL DEFAULT '0' COMMENT '店铺运单上偏移',
  PRIMARY KEY (`store_waybill_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺运单模板表';

-- ----------------------------
-- Records of store_waybill
-- ----------------------------

-- ----------------------------
-- Table structure for style_toindex
-- ----------------------------
DROP TABLE IF EXISTS `style_toindex`;
CREATE TABLE `style_toindex` (
  `style_sn` varchar(255) DEFAULT NULL,
  UNIQUE KEY `style_sn` (`style_sn`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of style_toindex
-- ----------------------------

-- ----------------------------
-- Table structure for transport
-- ----------------------------
DROP TABLE IF EXISTS `transport`;
CREATE TABLE `transport` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '运费模板ID',
  `title` varchar(30) NOT NULL COMMENT '运费模板名称',
  `send_tpl_id` mediumint(8) unsigned DEFAULT NULL COMMENT '发货地区模板ID',
  `store_id` mediumint(8) unsigned NOT NULL COMMENT '店铺ID',
  `update_time` int(10) unsigned DEFAULT '0' COMMENT '最后更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='运费模板';

-- ----------------------------
-- Records of transport
-- ----------------------------

-- ----------------------------
-- Table structure for transport_extend
-- ----------------------------
DROP TABLE IF EXISTS `transport_extend`;
CREATE TABLE `transport_extend` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '运费模板扩展ID',
  `area_id` text COMMENT '市级地区ID组成的串，以，隔开，两端也有，',
  `top_area_id` text COMMENT '省级地区ID组成的串，以，隔开，两端也有，',
  `area_name` text COMMENT '地区name组成的串，以，隔开',
  `sprice` decimal(10,2) DEFAULT '0.00' COMMENT '首件运费',
  `transport_id` mediumint(8) unsigned NOT NULL COMMENT '运费模板ID',
  `transport_title` varchar(60) DEFAULT NULL COMMENT '运费模板',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='运费模板扩展表';

-- ----------------------------
-- Records of transport_extend
-- ----------------------------

-- ----------------------------
-- Table structure for tttt
-- ----------------------------
DROP TABLE IF EXISTS `tttt`;
CREATE TABLE `tttt` (
  `store_id` int(1) NOT NULL DEFAULT '0',
  `store_company_id` int(1) NOT NULL DEFAULT '0',
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '主键',
  `member` varchar(20) NOT NULL COMMENT '登录帐号',
  `member_truename` varchar(20) NOT NULL COMMENT '姓名',
  `member_passwd` varchar(50) NOT NULL COMMENT '登录密码',
  `member_email` varchar(60) NOT NULL DEFAULT '',
  `member_email_bind` int(1) NOT NULL DEFAULT '0',
  `member_mobile` char(11) NOT NULL DEFAULT '' COMMENT '手机',
  `member_mobile_bind` int(1) NOT NULL DEFAULT '0',
  `member_login_num` int(1) NOT NULL DEFAULT '0',
  `member_time` int(1) NOT NULL DEFAULT '0',
  `member_login_time` int(1) NOT NULL DEFAULT '0',
  `member_old_login_time` int(1) NOT NULL DEFAULT '0',
  `member_points` int(1) NOT NULL DEFAULT '0',
  `available_predeposit` int(1) NOT NULL DEFAULT '0',
  `freeze_predeposit` int(1) NOT NULL DEFAULT '0',
  `available_rc_balance` int(1) NOT NULL DEFAULT '0',
  `freeze_rc_balance` int(1) NOT NULL DEFAULT '0',
  `inform_allow` int(1) NOT NULL DEFAULT '0',
  `is_buy` int(1) NOT NULL DEFAULT '0',
  `is_allowtalk` int(1) NOT NULL DEFAULT '0',
  `member_state` int(1) NOT NULL DEFAULT '0',
  `member_snsvisitnum` int(1) NOT NULL DEFAULT '0',
  `member_exppoints` int(1) NOT NULL DEFAULT '0',
  `role_id` tinyint(10) DEFAULT '0' COMMENT '权限管理-角色管理',
  `user_type` tinyint(4) NOT NULL DEFAULT '3' COMMENT '用户类型'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tttt
-- ----------------------------

-- ----------------------------
-- Table structure for type
-- ----------------------------
DROP TABLE IF EXISTS `type`;
CREATE TABLE `type` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '类型id',
  `type_name` varchar(100) NOT NULL COMMENT '类型名称',
  `type_sort` tinyint(1) unsigned NOT NULL COMMENT '排序',
  `class_id` int(10) unsigned DEFAULT '0' COMMENT '所属分类id',
  `class_name` varchar(100) DEFAULT '' COMMENT '所属分类名称',
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品类型表';

-- ----------------------------
-- Records of type
-- ----------------------------

-- ----------------------------
-- Table structure for type_brand
-- ----------------------------
DROP TABLE IF EXISTS `type_brand`;
CREATE TABLE `type_brand` (
  `type_id` int(10) unsigned NOT NULL COMMENT '类型id',
  `brand_id` int(10) unsigned NOT NULL COMMENT '品牌id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品类型与品牌对应表';

-- ----------------------------
-- Records of type_brand
-- ----------------------------

-- ----------------------------
-- Table structure for type_custom
-- ----------------------------
DROP TABLE IF EXISTS `type_custom`;
CREATE TABLE `type_custom` (
  `custom_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自定义属性id',
  `custom_name` varchar(50) NOT NULL COMMENT '自定义属性名称',
  `type_id` int(10) unsigned NOT NULL COMMENT '类型id',
  PRIMARY KEY (`custom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='自定义属性表';

-- ----------------------------
-- Records of type_custom
-- ----------------------------

-- ----------------------------
-- Table structure for type_spec
-- ----------------------------
DROP TABLE IF EXISTS `type_spec`;
CREATE TABLE `type_spec` (
  `type_id` int(10) unsigned NOT NULL COMMENT '类型id',
  `sp_id` int(10) unsigned NOT NULL COMMENT '规格id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品类型与规格对应表';

-- ----------------------------
-- Records of type_spec
-- ----------------------------

-- ----------------------------
-- Table structure for upload
-- ----------------------------
DROP TABLE IF EXISTS `upload`;
CREATE TABLE `upload` (
  `upload_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引ID',
  `file_name` varchar(100) DEFAULT NULL COMMENT '文件名',
  `file_thumb` varchar(100) DEFAULT NULL COMMENT '缩微图片',
  `file_size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `upload_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '文件类别，0为无，1为文章图片，默认为0，2为帮助内容图片，3为店铺幻灯片，4为系统文章图片，5为积分礼品切换图片，6为积分礼品内容图片',
  `upload_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `item_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '信息ID',
  PRIMARY KEY (`upload_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='上传文件表';

-- ----------------------------
-- Records of upload
-- ----------------------------

-- ----------------------------
-- Table structure for voucher
-- ----------------------------
DROP TABLE IF EXISTS `voucher`;
CREATE TABLE `voucher` (
  `voucher_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '代金券编号',
  `voucher_code` varchar(32) NOT NULL COMMENT '代金券编码',
  `voucher_t_id` int(11) NOT NULL COMMENT '代金券模版编号',
  `voucher_title` varchar(50) NOT NULL COMMENT '代金券标题',
  `voucher_desc` varchar(255) NOT NULL COMMENT '代金券描述',
  `voucher_start_date` int(11) NOT NULL COMMENT '代金券有效期开始时间',
  `voucher_end_date` int(11) NOT NULL COMMENT '代金券有效期结束时间',
  `voucher_price` int(11) NOT NULL COMMENT '代金券面额',
  `voucher_limit` decimal(10,2) NOT NULL COMMENT '代金券使用时的订单限额',
  `voucher_store_id` int(11) NOT NULL COMMENT '代金券的店铺id',
  `voucher_state` tinyint(4) NOT NULL COMMENT '代金券状态(1-未用,2-已用,3-过期,4-收回)',
  `voucher_active_date` int(11) NOT NULL COMMENT '代金券发放日期',
  `voucher_type` tinyint(4) DEFAULT '0' COMMENT '代金券类别1:折扣码 2：成品定制码',
  `voucher_owner_id` int(11) NOT NULL COMMENT '代金券所有者id',
  `voucher_owner_name` varchar(50) NOT NULL COMMENT '代金券所有者名称',
  `voucher_order_id` int(11) DEFAULT NULL COMMENT '使用该代金券的订单编号',
  `voucher_pwd` varchar(100) DEFAULT NULL COMMENT '代金券卡密不可逆',
  `voucher_pwd2` varchar(100) DEFAULT NULL COMMENT '代金券卡密2可逆',
  `voucher_goods_type` tinyint(2) DEFAULT NULL COMMENT '商品类型',
  PRIMARY KEY (`voucher_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='代金券表';

-- ----------------------------
-- Records of voucher
-- ----------------------------

-- ----------------------------
-- Table structure for voucher_price
-- ----------------------------
DROP TABLE IF EXISTS `voucher_price`;
CREATE TABLE `voucher_price` (
  `voucher_price_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '代金券面值编号',
  `voucher_price_describe` varchar(255) NOT NULL COMMENT '代金券描述',
  `voucher_price` int(11) NOT NULL COMMENT '代金券面值',
  `voucher_defaultpoints` int(11) NOT NULL DEFAULT '0' COMMENT '代金劵默认的兑换所需积分可以为0',
  PRIMARY KEY (`voucher_price_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='代金券面额表';

-- ----------------------------
-- Records of voucher_price
-- ----------------------------

-- ----------------------------
-- Table structure for voucher_quota
-- ----------------------------
DROP TABLE IF EXISTS `voucher_quota`;
CREATE TABLE `voucher_quota` (
  `quota_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '套餐编号',
  `quota_memberid` int(11) NOT NULL COMMENT '会员编号',
  `quota_membername` varchar(100) NOT NULL COMMENT '会员名称',
  `quota_storeid` int(11) NOT NULL COMMENT '店铺编号',
  `quota_storename` varchar(100) NOT NULL COMMENT '店铺名称',
  `quota_starttime` int(11) NOT NULL COMMENT '开始时间',
  `quota_endtime` int(11) NOT NULL COMMENT '结束时间',
  `quota_state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态(1-可用/2-取消/3-结束)',
  PRIMARY KEY (`quota_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='代金券套餐表';

-- ----------------------------
-- Records of voucher_quota
-- ----------------------------

-- ----------------------------
-- Table structure for voucher_template
-- ----------------------------
DROP TABLE IF EXISTS `voucher_template`;
CREATE TABLE `voucher_template` (
  `voucher_t_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '代金券模版编号',
  `voucher_t_title` varchar(50) NOT NULL COMMENT '代金券模版名称',
  `voucher_t_desc` varchar(255) NOT NULL COMMENT '代金券模版描述',
  `voucher_t_start_date` int(11) NOT NULL COMMENT '代金券模版有效期开始时间',
  `voucher_t_end_date` int(11) NOT NULL COMMENT '代金券模版有效期结束时间',
  `voucher_t_price` int(11) NOT NULL COMMENT '代金券模版面额/折扣比例（80为八折）',
  `voucher_t_limit` decimal(10,2) NOT NULL COMMENT '代金券使用时的订单限额',
  `voucher_t_store_id` int(11) NOT NULL COMMENT '代金券模版的店铺id',
  `voucher_t_storename` varchar(100) DEFAULT NULL COMMENT '店铺名称',
  `voucher_t_sc_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属店铺分类ID',
  `voucher_t_creator_id` int(11) NOT NULL COMMENT '代金券模版的创建者id',
  `voucher_t_state` tinyint(4) NOT NULL COMMENT '代金券模版状态(1-有效,2-失效)',
  `voucher_t_total` int(11) NOT NULL COMMENT '模版可发放的代金券总数',
  `voucher_t_giveout` int(11) NOT NULL COMMENT '模版已发放的代金券数量',
  `voucher_t_used` int(11) NOT NULL COMMENT '模版已经使用过的代金券',
  `voucher_t_add_date` int(11) NOT NULL COMMENT '模版的创建时间',
  `voucher_t_quotaid` int(11) NOT NULL COMMENT '套餐编号',
  `voucher_t_points` int(11) NOT NULL DEFAULT '0' COMMENT '兑换所需积分',
  `voucher_t_eachlimit` int(11) NOT NULL DEFAULT '1' COMMENT '每人限领张数',
  `voucher_t_styleimg` varchar(200) DEFAULT NULL COMMENT '样式模版图片',
  `voucher_t_customimg` varchar(200) DEFAULT NULL COMMENT '自定义代金券模板图片',
  `voucher_t_recommend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否推荐 0不推荐 1推荐',
  `voucher_t_gettype` tinyint(1) NOT NULL DEFAULT '1' COMMENT '领取方式 1积分兑换 2卡密兑换 3免费领取',
  `voucher_t_isbuild` tinyint(1) NOT NULL DEFAULT '0' COMMENT '领取方式为卡密兑换是否已经生成下属代金券 0未生成 1已生成',
  `voucher_t_mgradelimit` tinyint(2) NOT NULL DEFAULT '0' COMMENT '领取代金券限制的会员等级',
  `voucher_t_type` tinyint(1) DEFAULT '1' COMMENT '折扣券类别，1:折扣码 2：成品定制码',
  `voucher_t_goods_type` tinyint(2) DEFAULT NULL COMMENT '商品类型',
  PRIMARY KEY (`voucher_t_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='代金券模版表';

-- ----------------------------
-- Records of voucher_template
-- ----------------------------

-- ----------------------------
-- Table structure for vr_groupbuy_area
-- ----------------------------
DROP TABLE IF EXISTS `vr_groupbuy_area`;
CREATE TABLE `vr_groupbuy_area` (
  `area_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '区域id',
  `area_name` varchar(100) NOT NULL COMMENT '域区名称',
  `parent_area_id` int(11) NOT NULL COMMENT '域区id',
  `add_time` int(11) NOT NULL COMMENT '添加时间',
  `first_letter` char(1) NOT NULL COMMENT '首字母',
  `area_number` varchar(10) DEFAULT NULL COMMENT '区号',
  `post` varchar(10) DEFAULT NULL COMMENT '邮编',
  `hot_city` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0.否 1.是',
  `area_num` int(11) NOT NULL DEFAULT '0' COMMENT '数量',
  PRIMARY KEY (`area_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='虚拟抢购区域表';

-- ----------------------------
-- Records of vr_groupbuy_area
-- ----------------------------

-- ----------------------------
-- Table structure for vr_groupbuy_class
-- ----------------------------
DROP TABLE IF EXISTS `vr_groupbuy_class`;
CREATE TABLE `vr_groupbuy_class` (
  `class_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `class_name` varchar(100) NOT NULL COMMENT '分类名称',
  `parent_class_id` int(11) NOT NULL COMMENT '父类class_id',
  `class_sort` tinyint(3) unsigned DEFAULT NULL COMMENT '分类排序',
  PRIMARY KEY (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='虚拟抢购分类表';

-- ----------------------------
-- Records of vr_groupbuy_class
-- ----------------------------

-- ----------------------------
-- Table structure for vr_order
-- ----------------------------
DROP TABLE IF EXISTS `vr_order`;
CREATE TABLE `vr_order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '虚拟订单索引id',
  `order_sn` bigint(20) unsigned NOT NULL COMMENT '订单编号',
  `store_id` int(11) unsigned NOT NULL COMMENT '卖家店铺id',
  `store_name` varchar(50) NOT NULL COMMENT '卖家店铺名称',
  `buyer_id` int(11) unsigned NOT NULL COMMENT '买家id',
  `buyer_name` varchar(50) NOT NULL COMMENT '买家登录名',
  `buyer_phone` varchar(11) NOT NULL COMMENT '买家手机',
  `add_time` int(10) unsigned NOT NULL COMMENT '订单生成时间',
  `payment_code` char(10) DEFAULT '' COMMENT '支付方式名称代码',
  `payment_time` int(10) unsigned DEFAULT '0' COMMENT '支付(付款)时间',
  `trade_no` varchar(35) DEFAULT NULL COMMENT '第三方平台交易号',
  `close_time` int(10) unsigned DEFAULT '0' COMMENT '关闭时间',
  `close_reason` varchar(50) DEFAULT NULL COMMENT '关闭原因',
  `finnshed_time` int(11) DEFAULT NULL COMMENT '完成时间',
  `order_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单总价格(支付金额)',
  `refund_amount` decimal(10,2) DEFAULT '0.00' COMMENT '退款金额',
  `rcb_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '充值卡支付金额',
  `pd_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '预存款支付金额',
  `order_state` tinyint(4) NOT NULL DEFAULT '0' COMMENT '订单状态：0(已取消)10(默认):未付款;20:已付款;40:已完成;',
  `refund_state` tinyint(1) unsigned DEFAULT '0' COMMENT '退款状态:0是无退款,1是部分退款,2是全部退款',
  `buyer_msg` varchar(150) DEFAULT NULL COMMENT '买家留言',
  `delete_state` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除状态0未删除1放入回收站2彻底删除',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `goods_name` varchar(50) NOT NULL COMMENT '商品名称',
  `goods_price` decimal(10,2) NOT NULL COMMENT '商品价格',
  `goods_num` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '商品数量',
  `goods_image` varchar(100) DEFAULT NULL COMMENT '商品图片',
  `commis_rate` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '佣金比例',
  `gc_id` mediumint(9) DEFAULT '0' COMMENT '商品最底级分类ID',
  `vr_indate` int(11) DEFAULT NULL COMMENT '有效期',
  `vr_send_times` tinyint(4) NOT NULL DEFAULT '0' COMMENT '兑换码发送次数',
  `vr_invalid_refund` tinyint(4) NOT NULL DEFAULT '1' COMMENT '允许过期退款1是0否',
  `order_promotion_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '订单参加的促销类型 0无促销1抢购',
  `promotions_id` mediumint(9) DEFAULT '0' COMMENT '促销ID，与order_promotion_type配合使用',
  `order_from` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1WEB2mobile',
  `evaluation_state` tinyint(4) NOT NULL DEFAULT '0' COMMENT '评价状态0默认1已评价2禁止评价',
  `evaluation_time` int(11) NOT NULL DEFAULT '0' COMMENT '评价时间',
  `use_state` tinyint(4) DEFAULT '0' COMMENT '使用状态0默认，未使用1已使用，有一个被使用即为1',
  `api_pay_time` int(10) unsigned DEFAULT '0' COMMENT '在线支付动作时间,只有站内+在线组合支付时记录',
  `goods_contractid` varchar(100) DEFAULT NULL COMMENT '商品开启的消费者保障服务id',
  `goods_spec` varchar(200) DEFAULT NULL COMMENT '规格',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='虚拟订单表';

-- ----------------------------
-- Records of vr_order
-- ----------------------------

-- ----------------------------
-- Table structure for vr_order_bill
-- ----------------------------
DROP TABLE IF EXISTS `vr_order_bill`;
CREATE TABLE `vr_order_bill` (
  `ob_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键作为结算单号',
  `ob_no` int(11) DEFAULT '0' COMMENT '结算单编号(年月店铺ID)',
  `ob_start_date` int(11) NOT NULL COMMENT '开始日期',
  `ob_end_date` int(11) NOT NULL COMMENT '结束日期',
  `ob_order_totals` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `ob_commis_totals` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '佣金金额',
  `ob_result_totals` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '应结金额',
  `ob_create_date` int(11) DEFAULT '0' COMMENT '生成结算单日期',
  `os_month` mediumint(6) unsigned DEFAULT NULL COMMENT '出账单应结时间,ob_end_date+1所在月(年月份)',
  `ob_state` enum('1','2','3','4') DEFAULT '1' COMMENT '1默认2店家已确认3平台已审核4结算完成',
  `ob_pay_date` int(11) DEFAULT '0' COMMENT '付款日期',
  `ob_pay_content` varchar(200) DEFAULT '' COMMENT '支付备注',
  `ob_store_id` int(11) NOT NULL COMMENT '店铺ID',
  `ob_store_name` varchar(50) DEFAULT NULL COMMENT '店铺名',
  PRIMARY KEY (`ob_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='虚拟订单结算表';

-- ----------------------------
-- Records of vr_order_bill
-- ----------------------------

-- ----------------------------
-- Table structure for vr_order_code
-- ----------------------------
DROP TABLE IF EXISTS `vr_order_code`;
CREATE TABLE `vr_order_code` (
  `rec_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '兑换码表索引id',
  `order_id` int(11) NOT NULL COMMENT '虚拟订单id',
  `store_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺ID',
  `buyer_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '买家ID',
  `vr_code` varchar(18) NOT NULL COMMENT '兑换码',
  `vr_state` tinyint(4) NOT NULL DEFAULT '0' COMMENT '使用状态 0:(默认)未使用1:已使用2:已过期',
  `vr_usetime` int(11) DEFAULT NULL COMMENT '使用时间',
  `pay_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实际支付金额(结算)',
  `vr_indate` int(11) DEFAULT NULL COMMENT '过期时间',
  `commis_rate` smallint(6) NOT NULL DEFAULT '0' COMMENT '佣金比例',
  `refund_lock` tinyint(1) unsigned DEFAULT '0' COMMENT '退款锁定状态:0为正常,1为锁定,2为同意,默认为0',
  `vr_invalid_refund` tinyint(4) NOT NULL DEFAULT '1' COMMENT '允许过期退款1是0否',
  PRIMARY KEY (`rec_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='兑换码表';

-- ----------------------------
-- Records of vr_order_code
-- ----------------------------

-- ----------------------------
-- Table structure for vr_order_snapshot
-- ----------------------------
DROP TABLE IF EXISTS `vr_order_snapshot`;
CREATE TABLE `vr_order_snapshot` (
  `order_id` int(11) NOT NULL COMMENT '主键',
  `goods_id` int(11) NOT NULL COMMENT '商品ID',
  `create_time` int(11) NOT NULL COMMENT '生成时间',
  `goods_attr` text COMMENT '属性',
  `goods_body` text COMMENT '详情',
  `plate_top` text COMMENT '顶部关联版式',
  `plate_bottom` text COMMENT '底部关联版式',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='虚拟订单快照表';

-- ----------------------------
-- Records of vr_order_snapshot
-- ----------------------------

-- ----------------------------
-- Table structure for vr_order_statis
-- ----------------------------
DROP TABLE IF EXISTS `vr_order_statis`;
CREATE TABLE `vr_order_statis` (
  `os_month` mediumint(9) unsigned NOT NULL DEFAULT '0' COMMENT '统计编号(年月)',
  `os_year` smallint(6) DEFAULT '0' COMMENT '年',
  `os_start_date` int(11) NOT NULL COMMENT '开始日期',
  `os_end_date` int(11) NOT NULL COMMENT '结束日期',
  `os_order_totals` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `os_commis_totals` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '佣金金额',
  `os_result_totals` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '本期应结',
  `os_create_date` int(11) DEFAULT NULL COMMENT '创建记录日期',
  PRIMARY KEY (`os_month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='虚拟订单月销量统计表';

-- ----------------------------
-- Records of vr_order_statis
-- ----------------------------

-- ----------------------------
-- Table structure for vr_refund
-- ----------------------------
DROP TABLE IF EXISTS `vr_refund`;
CREATE TABLE `vr_refund` (
  `refund_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `order_id` int(10) unsigned NOT NULL COMMENT '虚拟订单ID',
  `order_sn` varchar(50) NOT NULL COMMENT '虚拟订单编号',
  `refund_sn` varchar(50) NOT NULL COMMENT '申请编号',
  `store_id` int(10) unsigned NOT NULL COMMENT '店铺ID',
  `store_name` varchar(20) NOT NULL COMMENT '店铺名称',
  `buyer_id` int(10) unsigned NOT NULL COMMENT '买家ID',
  `buyer_name` varchar(50) NOT NULL COMMENT '买家会员名',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品ID',
  `goods_num` int(10) unsigned DEFAULT '1' COMMENT '退款商品数量',
  `goods_name` varchar(50) NOT NULL COMMENT '商品名称',
  `goods_image` varchar(100) DEFAULT NULL COMMENT '商品图片',
  `code_sn` varchar(300) NOT NULL COMMENT '兑换码编号',
  `refund_amount` decimal(10,2) DEFAULT '0.00' COMMENT '退款金额',
  `admin_state` tinyint(1) unsigned DEFAULT '1' COMMENT '退款状态:1为待审核,2为同意,3为不同意,默认为1',
  `add_time` int(10) unsigned NOT NULL COMMENT '添加时间',
  `admin_time` int(10) unsigned DEFAULT '0' COMMENT '管理员处理时间,默认为0',
  `buyer_message` varchar(300) DEFAULT NULL COMMENT '申请原因',
  `admin_message` varchar(300) DEFAULT NULL COMMENT '管理员备注',
  `commis_rate` smallint(6) DEFAULT '0' COMMENT '佣金比例',
  PRIMARY KEY (`refund_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='虚拟兑码退款表';

-- ----------------------------
-- Records of vr_refund
-- ----------------------------

-- ----------------------------
-- Table structure for vr_refund_detail
-- ----------------------------
DROP TABLE IF EXISTS `vr_refund_detail`;
CREATE TABLE `vr_refund_detail` (
  `refund_id` int(10) unsigned NOT NULL COMMENT '记录ID',
  `order_id` int(10) unsigned NOT NULL COMMENT '订单ID',
  `batch_no` varchar(32) NOT NULL COMMENT '批次号',
  `refund_amount` decimal(10,2) DEFAULT '0.00' COMMENT '退款金额',
  `pay_amount` decimal(10,2) DEFAULT '0.00' COMMENT '在线退款金额',
  `pd_amount` decimal(10,2) DEFAULT '0.00' COMMENT '预存款金额',
  `rcb_amount` decimal(10,2) DEFAULT '0.00' COMMENT '充值卡金额',
  `refund_code` char(10) NOT NULL DEFAULT 'predeposit' COMMENT '退款支付代码',
  `refund_state` tinyint(1) unsigned DEFAULT '1' COMMENT '退款状态:1为处理中,2为已完成,默认为1',
  `add_time` int(10) unsigned NOT NULL COMMENT '添加时间',
  `pay_time` int(10) unsigned DEFAULT '0' COMMENT '在线退款完成时间,默认为0',
  PRIMARY KEY (`refund_id`),
  KEY `batch_no` (`batch_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='虚拟兑码退款详细表';

-- ----------------------------
-- Records of vr_refund_detail
-- ----------------------------

-- ----------------------------
-- Table structure for warehouse_box
-- ----------------------------
DROP TABLE IF EXISTS `warehouse_box`;
CREATE TABLE `warehouse_box` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` int(10) unsigned NOT NULL COMMENT '柜位所属仓库',
  `box_sn` varchar(30) NOT NULL COMMENT '柜位号',
  `create_name` varchar(35) NOT NULL COMMENT '新增人',
  `create_time` datetime NOT NULL COMMENT '新增时间',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=启用，0=禁用',
  `info` varchar(255) DEFAULT NULL COMMENT '备注',
  `last_pandian_time` datetime DEFAULT NULL COMMENT '最后周盘点时间',
  `last_pandian_error` int(10) DEFAULT '0' COMMENT '最后一次周盘点错误数 (盘盈+盘亏)',
  `is_pan` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已经盘点过（月盘点单使用字段） 1盘点过/0未盘点过',
  PRIMARY KEY (`id`),
  KEY `warehouse_id` (`warehouse_id`) USING BTREE,
  KEY `box_sn` (`box_sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='柜位表';

-- ----------------------------
-- Records of warehouse_box
-- ----------------------------

-- ----------------------------
-- Table structure for warehouse_goods_ishop_price
-- ----------------------------
DROP TABLE IF EXISTS `warehouse_goods_ishop_price`;
CREATE TABLE `warehouse_goods_ishop_price` (
  `id` bigint(30) NOT NULL AUTO_INCREMENT,
  `goods_id` bigint(30) NOT NULL,
  `channel_id` int(10) NOT NULL,
  `sale_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '智慧门店现货零售价',
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `goods_id_2` (`goods_id`,`channel_id`) USING BTREE,
  KEY `goods_id` (`goods_id`),
  KEY `channel_id` (`channel_id`),
  KEY `sale_price` (`sale_price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of warehouse_goods_ishop_price
-- ----------------------------

-- ----------------------------
-- Table structure for waybill
-- ----------------------------
DROP TABLE IF EXISTS `waybill`;
CREATE TABLE `waybill` (
  `waybill_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `waybill_name` varchar(50) NOT NULL,
  `waybill_image` varchar(50) NOT NULL,
  `waybill_width` int(10) unsigned NOT NULL COMMENT '宽度',
  `waybill_height` int(10) unsigned NOT NULL COMMENT '高度',
  `waybill_data` varchar(2000) DEFAULT NULL COMMENT '打印位置数据',
  `waybill_usable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否可用',
  `waybill_top` int(11) NOT NULL DEFAULT '0' COMMENT '上偏移量',
  `waybill_left` int(11) NOT NULL DEFAULT '0' COMMENT '左偏移量',
  `express_id` tinyint(1) unsigned NOT NULL COMMENT '快递公司编号',
  `express_name` varchar(50) NOT NULL COMMENT '快递公司名称',
  `store_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '店铺编号(0-代表系统模板)',
  PRIMARY KEY (`waybill_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='快递单打印模板表';

-- ----------------------------
-- Records of waybill
-- ----------------------------

-- ----------------------------
-- Table structure for web
-- ----------------------------
DROP TABLE IF EXISTS `web`;
CREATE TABLE `web` (
  `web_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '模块ID',
  `web_name` varchar(20) DEFAULT '' COMMENT '模块名称',
  `style_name` varchar(20) DEFAULT 'orange' COMMENT '风格名称',
  `web_page` varchar(10) DEFAULT 'index' COMMENT '所在页面(暂时只有index)',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  `web_sort` tinyint(1) unsigned DEFAULT '9' COMMENT '排序',
  `web_show` tinyint(1) unsigned DEFAULT '1' COMMENT '是否显示，0为否，1为是，默认为1',
  `web_html` text COMMENT '模块html代码',
  PRIMARY KEY (`web_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='页面模块表';

-- ----------------------------
-- Records of web
-- ----------------------------

-- ----------------------------
-- Table structure for web_channel
-- ----------------------------
DROP TABLE IF EXISTS `web_channel`;
CREATE TABLE `web_channel` (
  `channel_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '频道ID',
  `channel_name` varchar(50) DEFAULT '' COMMENT '频道名称',
  `channel_style` varchar(20) DEFAULT '' COMMENT '颜色风格',
  `gc_id` int(10) unsigned DEFAULT '0' COMMENT '绑定分类ID',
  `gc_name` varchar(50) DEFAULT '' COMMENT '分类名称',
  `keywords` varchar(255) DEFAULT '' COMMENT '关键词',
  `description` varchar(255) DEFAULT '' COMMENT '描述',
  `top_id` int(10) unsigned DEFAULT '0' COMMENT '顶部楼层编号',
  `floor_ids` varchar(100) DEFAULT '' COMMENT '中部楼层编号',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  `channel_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '启用状态，0为否，1为是，默认为1',
  PRIMARY KEY (`channel_id`),
  KEY `gc_id` (`gc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商城频道表';

-- ----------------------------
-- Records of web_channel
-- ----------------------------

-- ----------------------------
-- Table structure for web_code
-- ----------------------------
DROP TABLE IF EXISTS `web_code`;
CREATE TABLE `web_code` (
  `code_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '内容ID',
  `web_id` int(10) unsigned NOT NULL COMMENT '模块ID',
  `code_type` varchar(10) NOT NULL DEFAULT 'array' COMMENT '数据类型:array,html,json',
  `var_name` varchar(20) NOT NULL COMMENT '变量名称',
  `code_info` text COMMENT '内容数据',
  `show_name` varchar(20) DEFAULT '' COMMENT '页面名称',
  PRIMARY KEY (`code_id`),
  KEY `web_id` (`web_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='模块内容表';

-- ----------------------------
-- Records of web_code
-- ----------------------------
