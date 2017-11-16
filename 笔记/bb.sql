/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : bb

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-11-16 09:18:18
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for website_admin
-- ----------------------------
DROP TABLE IF EXISTS `website_admin`;
CREATE TABLE `website_admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) DEFAULT NULL,
  `password` varchar(150) DEFAULT NULL,
  `salt` varchar(20) DEFAULT NULL,
  `group` tinyint(4) DEFAULT '0' COMMENT '权限组 超级管理员还是普通管理员',
  `tel` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `last_login_time` datetime DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT '1' COMMENT '是否删除 1否 2是',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `ADMIN_ID` (`admin_id`) USING BTREE,
  KEY `DELETED` (`deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_admin
-- ----------------------------
INSERT INTO `website_admin` VALUES ('1', 'superadmin', 'db1c91a1dec1865c9c3e519c1de442afaa4fa778', 'vQ5N4kMrlS', '1', '13656480135', '403520515@qq.com', '2017-11-14 13:40:55', '1', '2017-04-28 16:01:32');
INSERT INTO `website_admin` VALUES ('2', 'admin', 'c5937201355066b46610a18838425036e4f2ef52', 'gGSBezIQAI', '2', '18866830116', 'long83116@qq.com', '2017-05-24 16:18:08', '1', '2017-04-28 16:04:36');

-- ----------------------------
-- Table structure for website_article_advertising
-- ----------------------------
DROP TABLE IF EXISTS `website_article_advertising`;
CREATE TABLE `website_article_advertising` (
  `advertising_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL COMMENT '标题',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '是否启用,对应配置表',
  `image_path` varchar(100) DEFAULT NULL COMMENT '图片地址',
  `content` text COMMENT '内容',
  `deleted` tinyint(4) DEFAULT '1' COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`advertising_id`),
  UNIQUE KEY `ADVERTISING_ID` (`advertising_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_article_advertising
-- ----------------------------
INSERT INTO `website_article_advertising` VALUES ('12', '蒙氏教育的', '80', 'catalog/tuzi1.jpg', '&lt;p&gt;蒙氏教育蒙氏教育蒙氏教育蒙氏教育蒙氏教育&lt;/p&gt;', '1', '2017-06-08 13:39:24');

-- ----------------------------
-- Table structure for website_article_advertising_category
-- ----------------------------
DROP TABLE IF EXISTS `website_article_advertising_category`;
CREATE TABLE `website_article_advertising_category` (
  `association_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `article_id` int(11) NOT NULL COMMENT '文章ID',
  PRIMARY KEY (`association_id`),
  UNIQUE KEY `ASSOCIATION_id` (`association_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='栏目和文章关联表，多对多关系';

-- ----------------------------
-- Records of website_article_advertising_category
-- ----------------------------
INSERT INTO `website_article_advertising_category` VALUES ('18', '12', '12');
INSERT INTO `website_article_advertising_category` VALUES ('19', '11', '12');

-- ----------------------------
-- Table structure for website_article_advertising_images
-- ----------------------------
DROP TABLE IF EXISTS `website_article_advertising_images`;
CREATE TABLE `website_article_advertising_images` (
  `advertising_image_id` int(11) NOT NULL AUTO_INCREMENT,
  `advertising_id` int(11) NOT NULL DEFAULT '0' COMMENT '内容ID',
  `image_path` varchar(100) DEFAULT NULL COMMENT '图片地址',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`advertising_image_id`),
  UNIQUE KEY `ADVERTISING_IMAGE_ID` (`advertising_image_id`) USING BTREE,
  KEY `ADVERTISING_ID` (`advertising_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_article_advertising_images
-- ----------------------------
INSERT INTO `website_article_advertising_images` VALUES ('61', '12', 'catalog/180.jpg', '3', '2017-06-08 13:39:24');
INSERT INTO `website_article_advertising_images` VALUES ('62', '12', 'catalog/3333.jpg', '2', '2017-06-08 13:39:24');
INSERT INTO `website_article_advertising_images` VALUES ('63', '12', 'catalog/121212.jpg', '4', '2017-06-08 13:55:03');

-- ----------------------------
-- Table structure for website_article_announcement
-- ----------------------------
DROP TABLE IF EXISTS `website_article_announcement`;
CREATE TABLE `website_article_announcement` (
  `announcement_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL COMMENT '标题',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '是否启用,对应配置表',
  `image_path` varchar(100) DEFAULT NULL COMMENT '图片地址',
  `content` text COMMENT '内容',
  `deleted` tinyint(4) DEFAULT '1' COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`announcement_id`),
  UNIQUE KEY `ANNOUNCEMENT_ID` (`announcement_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_article_announcement
-- ----------------------------
INSERT INTO `website_article_announcement` VALUES ('15', '网站基本框架建成1', '80', 'catalog/tuzi2.jpg', '&lt;p&gt;网站基本框架建成网站基本框架建成网站基本框架建成网站基本框架建成&lt;/p&gt;', '1', '2017-06-08 14:26:57');

-- ----------------------------
-- Table structure for website_article_announcement_category
-- ----------------------------
DROP TABLE IF EXISTS `website_article_announcement_category`;
CREATE TABLE `website_article_announcement_category` (
  `association_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `article_id` int(11) NOT NULL COMMENT '文章ID',
  PRIMARY KEY (`association_id`),
  UNIQUE KEY `ASSOCIATION_id` (`association_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COMMENT='栏目和文章关联表，多对多关系';

-- ----------------------------
-- Records of website_article_announcement_category
-- ----------------------------
INSERT INTO `website_article_announcement_category` VALUES ('24', '5', '15');
INSERT INTO `website_article_announcement_category` VALUES ('25', '16', '15');
INSERT INTO `website_article_announcement_category` VALUES ('26', '14', '15');

-- ----------------------------
-- Table structure for website_article_announcement_images
-- ----------------------------
DROP TABLE IF EXISTS `website_article_announcement_images`;
CREATE TABLE `website_article_announcement_images` (
  `announcement_image_id` int(11) NOT NULL AUTO_INCREMENT,
  `announcement_id` int(11) NOT NULL DEFAULT '0' COMMENT '内容ID',
  `image_path` varchar(100) DEFAULT NULL COMMENT '图片地址',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`announcement_image_id`),
  UNIQUE KEY `ANNOUNCEMENT_IMAGE_ID` (`announcement_image_id`) USING BTREE,
  KEY `ANNOUNCEMENT_ID` (`announcement_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_article_announcement_images
-- ----------------------------
INSERT INTO `website_article_announcement_images` VALUES ('64', '15', 'catalog/121212.jpg', '3', '2017-06-08 14:26:58');
INSERT INTO `website_article_announcement_images` VALUES ('65', '15', 'catalog/180.jpg', '4', '2017-06-08 14:26:58');
INSERT INTO `website_article_announcement_images` VALUES ('66', '15', 'catalog/3333.jpg', '1', '2017-06-08 14:35:33');

-- ----------------------------
-- Table structure for website_article_category
-- ----------------------------
DROP TABLE IF EXISTS `website_article_category`;
CREATE TABLE `website_article_category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL COMMENT '标题',
  `desc` varchar(100) DEFAULT NULL COMMENT '描述',
  `article_type` int(11) DEFAULT '0' COMMENT '文章类型ID,对应配置表',
  `status` int(11) DEFAULT '0' COMMENT '是否启用,对应配置表',
  `deleted` tinyint(4) DEFAULT '1' COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `CATEGORY_ID` (`category_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_article_category
-- ----------------------------
INSERT INTO `website_article_category` VALUES ('1', '经验心得', '', '82', '78', '1', '2017-05-31 16:47:29');
INSERT INTO `website_article_category` VALUES ('2', '早间新闻', '', '83', '78', '1', '2017-06-05 11:27:45');
INSERT INTO `website_article_category` VALUES ('4', '顶部广告位', '', '84', '78', '1', '2017-06-05 14:43:51');
INSERT INTO `website_article_category` VALUES ('5', '公告展示', '', '85', '78', '1', '2017-06-05 14:44:12');
INSERT INTO `website_article_category` VALUES ('6', '情绪智力', '', '82', '78', '1', '2017-06-05 14:48:48');
INSERT INTO `website_article_category` VALUES ('7', '所见所闻', '', '82', '78', '1', '2017-06-05 17:01:08');
INSERT INTO `website_article_category` VALUES ('8', '热门新闻', '', '83', '78', '1', '2017-06-07 15:46:39');
INSERT INTO `website_article_category` VALUES ('9', '最新新闻', '', '83', '78', '1', '2017-06-07 15:46:50');
INSERT INTO `website_article_category` VALUES ('10', '综合新闻', '', '83', '78', '1', '2017-06-07 15:47:01');
INSERT INTO `website_article_category` VALUES ('11', '右边广告位', '', '84', '78', '1', '2017-06-08 11:55:25');
INSERT INTO `website_article_category` VALUES ('12', '热门广告位', '', '84', '78', '1', '2017-06-08 11:55:35');
INSERT INTO `website_article_category` VALUES ('13', '特殊广告位', '', '84', '78', '1', '2017-06-08 11:55:49');
INSERT INTO `website_article_category` VALUES ('14', '最新公告', '', '85', '78', '1', '2017-06-08 14:22:53');
INSERT INTO `website_article_category` VALUES ('15', '往期公告', '', '85', '78', '1', '2017-06-08 14:23:08');
INSERT INTO `website_article_category` VALUES ('16', '热门公告', '', '85', '78', '1', '2017-06-08 14:24:57');

-- ----------------------------
-- Table structure for website_article_content
-- ----------------------------
DROP TABLE IF EXISTS `website_article_content`;
CREATE TABLE `website_article_content` (
  `content_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL COMMENT '标题',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '是否启用,对应配置表',
  `image_path` varchar(100) DEFAULT NULL COMMENT '图片地址',
  `content` text COMMENT '内容',
  `deleted` tinyint(4) DEFAULT '1' COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`content_id`),
  UNIQUE KEY `CONTENT_ID` (`content_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_article_content
-- ----------------------------
INSERT INTO `website_article_content` VALUES ('9', '笔记', '80', 'catalog/tuzi2.jpg', '&lt;p&gt;心得体会&lt;/p&gt;', '1', '2017-06-06 14:17:48');

-- ----------------------------
-- Table structure for website_article_content_category
-- ----------------------------
DROP TABLE IF EXISTS `website_article_content_category`;
CREATE TABLE `website_article_content_category` (
  `association_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `article_id` int(11) NOT NULL COMMENT '文章ID',
  PRIMARY KEY (`association_id`),
  UNIQUE KEY `ACCOCIATION_id` (`association_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='栏目和文章关联表，多对多关系';

-- ----------------------------
-- Records of website_article_content_category
-- ----------------------------
INSERT INTO `website_article_content_category` VALUES ('8', '7', '9');
INSERT INTO `website_article_content_category` VALUES ('9', '1', '9');

-- ----------------------------
-- Table structure for website_article_content_images
-- ----------------------------
DROP TABLE IF EXISTS `website_article_content_images`;
CREATE TABLE `website_article_content_images` (
  `content_image_id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL DEFAULT '0' COMMENT '内容ID',
  `image_path` varchar(100) DEFAULT NULL COMMENT '图片地址',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`content_image_id`),
  UNIQUE KEY `CONTENT_IMAGE_ID` (`content_image_id`) USING BTREE,
  KEY `CONTENT_ID` (`content_id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_article_content_images
-- ----------------------------
INSERT INTO `website_article_content_images` VALUES ('52', '9', 'catalog/180.jpg', '2', '2017-06-06 14:17:48');
INSERT INTO `website_article_content_images` VALUES ('54', '9', 'catalog/3333.jpg', '3', '2017-06-06 15:43:25');

-- ----------------------------
-- Table structure for website_article_news
-- ----------------------------
DROP TABLE IF EXISTS `website_article_news`;
CREATE TABLE `website_article_news` (
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL COMMENT '标题',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '是否启用,对应配置表',
  `image_path` varchar(100) DEFAULT NULL COMMENT '图片地址',
  `content` text COMMENT '内容',
  `deleted` tinyint(4) DEFAULT '1' COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`news_id`),
  UNIQUE KEY `NEWS_ID` (`news_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_article_news
-- ----------------------------
INSERT INTO `website_article_news` VALUES ('10', '测试新闻的', '80', 'catalog/121212.jpg', '&lt;p&gt;测试新闻测试新闻测试新闻测试新闻测试新闻&lt;/p&gt;', '1', '2017-06-07 15:48:04');
INSERT INTO `website_article_news` VALUES ('11', '所有新闻类型', '80', 'catalog/180.jpg', '&amp;lt;p&amp;gt;所有新闻类型所有新闻类型所有新闻类型所有新闻类型&amp;lt;/p&amp;gt;', '1', '2017-06-07 15:50:29');

-- ----------------------------
-- Table structure for website_article_news_category
-- ----------------------------
DROP TABLE IF EXISTS `website_article_news_category`;
CREATE TABLE `website_article_news_category` (
  `association_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `article_id` int(11) NOT NULL COMMENT '文章ID',
  PRIMARY KEY (`association_id`),
  UNIQUE KEY `ASSOCIATION_id` (`association_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='栏目和文章关联表，多对多关系';

-- ----------------------------
-- Records of website_article_news_category
-- ----------------------------
INSERT INTO `website_article_news_category` VALUES ('11', '8', '10');
INSERT INTO `website_article_news_category` VALUES ('12', '2', '11');
INSERT INTO `website_article_news_category` VALUES ('13', '8', '11');
INSERT INTO `website_article_news_category` VALUES ('14', '9', '11');
INSERT INTO `website_article_news_category` VALUES ('15', '10', '11');
INSERT INTO `website_article_news_category` VALUES ('16', '9', '10');

-- ----------------------------
-- Table structure for website_article_news_images
-- ----------------------------
DROP TABLE IF EXISTS `website_article_news_images`;
CREATE TABLE `website_article_news_images` (
  `news_image_id` int(11) NOT NULL AUTO_INCREMENT,
  `news_id` int(11) NOT NULL DEFAULT '0' COMMENT '内容ID',
  `image_path` varchar(100) DEFAULT NULL COMMENT '图片地址',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`news_image_id`),
  UNIQUE KEY `NEWS_IMAGE_ID` (`news_image_id`) USING BTREE,
  KEY `NEWS_ID` (`news_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_article_news_images
-- ----------------------------
INSERT INTO `website_article_news_images` VALUES ('55', '10', 'catalog/3333.jpg', '2', '2017-06-07 15:48:04');
INSERT INTO `website_article_news_images` VALUES ('56', '10', 'catalog/tuzi.jpg', '3', '2017-06-07 15:48:04');
INSERT INTO `website_article_news_images` VALUES ('57', '11', 'catalog/tuzi2.jpg', '3', '2017-06-07 15:50:29');
INSERT INTO `website_article_news_images` VALUES ('58', '11', 'catalog/3333.jpg', '1', '2017-06-07 15:50:29');
INSERT INTO `website_article_news_images` VALUES ('59', '11', 'catalog/121212.jpg', '2', '2017-06-07 15:50:29');
INSERT INTO `website_article_news_images` VALUES ('60', '10', 'catalog/180.jpg', '0', '2017-06-07 16:05:18');

-- ----------------------------
-- Table structure for website_config
-- ----------------------------
DROP TABLE IF EXISTS `website_config`;
CREATE TABLE `website_config` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `column` varchar(20) DEFAULT NULL COMMENT '所属栏目',
  `module` varchar(20) DEFAULT NULL COMMENT '所属模块',
  `title` varchar(20) DEFAULT NULL COMMENT '标题',
  `key` varchar(50) DEFAULT NULL COMMENT '标识',
  `value` varchar(50) DEFAULT NULL COMMENT '内容',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`config_id`),
  UNIQUE KEY `CONFIG_CATEGORY_ID` (`config_id`) USING BTREE,
  KEY `MODULE` (`module`) USING BTREE,
  KEY `category` (`column`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_config
-- ----------------------------
INSERT INTO `website_config` VALUES ('5', '其他', 'page', '网站后台每页显示数量', 'paging_limit', '20', '0');
INSERT INTO `website_config` VALUES ('6', '其他', 'page', '分页规则', 'paging_rules', '显示 %d 到 %d / %d (总 %d 页)', '0');

-- ----------------------------
-- Table structure for website_record
-- ----------------------------
DROP TABLE IF EXISTS `website_record`;
CREATE TABLE `website_record` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_id` tinyint(4) DEFAULT NULL COMMENT '信息记录配置ID',
  `money` float(10,2) DEFAULT '0.00' COMMENT '金额',
  `content` text COMMENT '详情',
  `deleted` tinyint(4) DEFAULT '1' COMMENT '是否删除 1否 2是',
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`record_id`),
  KEY `RECORD_ID` (`record_id`),
  KEY `SETTING_ID` (`setting_id`),
  KEY `DELETED` (`deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_record
-- ----------------------------
INSERT INTO `website_record` VALUES ('1', '73', '100.00', 'aaaaa', '1', '2017-04-25 09:57:55', '2017-04-25 09:42:57');
INSERT INTO `website_record` VALUES ('2', '74', '200.00', 'bbbbbb', '1', '2017-04-25 09:57:55', '2017-04-25 09:43:21');
INSERT INTO `website_record` VALUES ('3', '73', '1000.22', '测试的测试的测试的测试的测试测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的的测试的测试的测试的测试的测试的测试的测试的测试的', '1', '2017-04-25 09:57:57', '2017-04-25 09:44:52');
INSERT INTO `website_record` VALUES ('4', '73', '324234.00', '', '1', '2017-04-25 10:30:35', '2017-04-25 10:30:35');
INSERT INTO `website_record` VALUES ('5', '73', '0.00', '', '1', '2017-04-25 10:30:42', '2017-04-25 10:30:42');
INSERT INTO `website_record` VALUES ('6', '73', '1.00', '', '1', '2017-04-25 10:31:28', '2017-04-25 10:31:28');

-- ----------------------------
-- Table structure for website_session_info
-- ----------------------------
DROP TABLE IF EXISTS `website_session_info`;
CREATE TABLE `website_session_info` (
  `session_id` varchar(200) NOT NULL DEFAULT '0',
  `uid` bigint(20) DEFAULT '0' COMMENT '用户ID,包括前后台',
  `data` text,
  `user_type` varchar(20) DEFAULT NULL COMMENT '访问网站的用户类型',
  `expiration_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`session_id`),
  UNIQUE KEY `SESSION_ID` (`session_id`) USING BTREE,
  KEY `UID` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_session_info
-- ----------------------------
INSERT INTO `website_session_info` VALUES ('39fec53508996c8a8444d3bbde', '1', 'admin_id|s:1:\"1\";admin_name|s:10:\"superadmin\";group|s:1:\"1\";', 'admin', '2017-11-14 17:32:50');

-- ----------------------------
-- Table structure for website_setting
-- ----------------------------
DROP TABLE IF EXISTS `website_setting`;
CREATE TABLE `website_setting` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_category_id` int(11) NOT NULL DEFAULT '0' COMMENT '配置类别',
  `key` int(11) NOT NULL DEFAULT '0' COMMENT '唯一标示，某些地方会用到',
  `value` varchar(50) NOT NULL COMMENT '值',
  `months` int(11) DEFAULT '0' COMMENT '此字段存的是会员卡种有效期月数',
  `times` int(11) DEFAULT '0' COMMENT '此字段存的是会员卡种可用次数,如果是敏感期则是敏感期的课时',
  `money` float(10,2) DEFAULT '0.00' COMMENT '会员卡种的费用金额',
  `parent_setting_id` int(11) DEFAULT NULL COMMENT '父类设置ID',
  `description` varchar(100) DEFAULT NULL COMMENT '描述',
  `sort` int(11) DEFAULT '0' COMMENT '同类选项排序',
  `deleted` tinyint(4) DEFAULT '1' COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `SETTING_ID` (`setting_id`) USING BTREE,
  KEY `SETTING_CATEGORY_ID` (`setting_category_id`) USING BTREE,
  KEY `DELETED` (`deleted`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_setting
-- ----------------------------
INSERT INTO `website_setting` VALUES ('1', '1', '1', '普通', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('2', '1', '2', 'VIP', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('3', '2', '1', '启用', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('4', '2', '2', '停用', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('5', '3', '1', '启用', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('6', '3', '2', '停用', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('7', '4', '1', '男', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('8', '4', '2', '女', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('14', '11', '1', '男', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('15', '11', '2', '女', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('16', '12', '1', '鼠', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('17', '12', '2', '牛', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('18', '12', '3', '虎', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('19', '12', '4', '兔', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('20', '12', '5', '龙', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('21', '12', '6', '蛇', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('22', '12', '7', '马', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('23', '12', '8', '羊', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('24', '12', '9', '猴', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('25', '12', '10', '鸡', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('26', '12', '11', '狗', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('27', '12', '12', '猪', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('28', '13', '1', '白羊座', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('29', '13', '2', '金牛座', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('30', '13', '3', '双子座', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('31', '13', '4', '巨蟹座', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('32', '13', '5', '狮子座', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('33', '13', '6', '处女座', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('34', '13', '7', '天秤座', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('35', '13', '8', '天蝎座', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('36', '13', '9', '射手座', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('37', '13', '10', '摩羯座', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('38', '13', '11', '水瓶座', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('39', '13', '12', '双鱼座', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('40', '14', '1', 'A', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('41', '14', '2', 'B', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('42', '14', '3', 'AB', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('43', '14', '4', 'O', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('44', '14', '5', '其他', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('45', '15', '1', '老人带孩子', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('46', '15', '2', '父母带孩子', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('47', '15', '3', '其他', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('48', '16', '1', 'E', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('49', '16', '2', 'T', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('50', '16', '3', 'D', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('51', '16', '4', 'A', '0', '0', '0.00', null, null, '0', '1', '2017-04-11 10:17:01');
INSERT INTO `website_setting` VALUES ('52', '17', '1', '半年', '6', '0', '0.00', null, '', '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('53', '17', '2', '一年', '12', '0', '0.00', null, '', '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('54', '17', '3', '二年', '24', '0', '0.00', null, '', '0', '1', '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES ('56', '18', '1', '2999元48次半年内有效', '0', '48', '2999.00', '52', null, '0', '1', '2017-04-11 10:58:40');
INSERT INTO `website_setting` VALUES ('57', '19', '1', '语言敏感期', '0', '1', '0.00', null, null, '0', '1', '2017-04-12 11:34:59');
INSERT INTO `website_setting` VALUES ('58', '19', '2', '秩序敏感期', '0', '1', '0.00', null, null, '0', '1', '2017-04-12 11:35:06');
INSERT INTO `website_setting` VALUES ('59', '19', '3', '感冒敏感期', '0', '1', '0.00', null, null, '0', '1', '2017-04-12 11:35:19');
INSERT INTO `website_setting` VALUES ('60', '19', '4', '对细微事物感兴趣的敏感期', '0', '1', '0.00', null, null, '0', '1', '2017-04-12 11:35:41');
INSERT INTO `website_setting` VALUES ('61', '19', '5', '动作敏感期', '0', '1', '0.00', null, null, '0', '1', '2017-04-12 11:35:52');
INSERT INTO `website_setting` VALUES ('62', '19', '6', '社会规范敏感期', '0', '1', '0.00', null, null, '0', '1', '2017-04-12 11:36:03');
INSERT INTO `website_setting` VALUES ('63', '19', '7', '书写敏感期', '0', '1', '0.00', null, null, '0', '1', '2017-04-12 11:36:18');
INSERT INTO `website_setting` VALUES ('64', '19', '8', '阅读敏感期', '0', '1', '0.00', null, null, '0', '1', '2017-04-12 11:36:26');
INSERT INTO `website_setting` VALUES ('65', '19', '9', '文化敏感期', '0', '3', '0.00', null, null, '0', '1', '2017-04-12 11:36:41');
INSERT INTO `website_setting` VALUES ('66', '18', '2', '4999元100次一年内有效', '0', '100', '4999.00', '53', null, '0', '1', '2017-04-13 15:48:41');
INSERT INTO `website_setting` VALUES ('67', '18', '3', '9999元200次二年内有效', '0', '200', '9999.00', '54', null, '0', '1', '2017-04-13 15:49:06');
INSERT INTO `website_setting` VALUES ('68', '20', '1', '教具1', '0', '0', '0.00', null, null, '0', '1', '2017-04-19 15:13:00');
INSERT INTO `website_setting` VALUES ('69', '20', '2', '教具2', '0', '0', '0.00', null, null, '0', '1', '2017-04-19 15:13:08');
INSERT INTO `website_setting` VALUES ('70', '20', '3', '教具3', '0', '0', '0.00', null, null, '0', '1', '2017-04-19 15:13:13');
INSERT INTO `website_setting` VALUES ('71', '22', '1', '启用', '0', '0', '0.00', null, '请不要删除此选项或者随意改Key值，此选项的Key影响这网站的信息选择，如果删除，网站信息将出现错误。', '0', '1', '2017-04-24 15:37:05');
INSERT INTO `website_setting` VALUES ('72', '22', '2', '停用', '0', '0', '0.00', null, '请不要删除此选项或者随意改Key值，此选项的Key影响这网站的信息选择，如果删除，网站信息将出现错误。', '0', '1', '2017-04-24 15:37:11');
INSERT INTO `website_setting` VALUES ('73', '23', '1', '收入', '0', '0', '0.00', null, null, '0', '1', '2017-04-24 17:39:07');
INSERT INTO `website_setting` VALUES ('74', '23', '2', '支出', '0', '0', '0.00', null, null, '0', '1', '2017-04-24 17:39:12');
INSERT INTO `website_setting` VALUES ('75', '17', '4', '三年', '36', '0', '0.00', null, '', '0', '1', '2017-04-26 14:47:19');
INSERT INTO `website_setting` VALUES ('76', '18', '4', '14999元300次三年内有效', '0', '300', '14999.00', '75', null, '0', '1', '2017-04-26 15:37:05');
INSERT INTO `website_setting` VALUES ('77', '18', '5', '三年XXX卡种多少次', '0', '200', '9998.00', '75', null, '0', '1', '2017-04-26 16:28:40');
INSERT INTO `website_setting` VALUES ('78', '24', '1', '是', '0', '0', '0.00', null, null, '0', '1', '2017-05-31 16:16:43');
INSERT INTO `website_setting` VALUES ('79', '24', '2', '否', '0', '0', '0.00', null, null, '1', '1', '2017-05-31 16:16:48');
INSERT INTO `website_setting` VALUES ('80', '25', '1', '是', '0', '0', '0.00', null, null, '0', '1', '2017-06-01 11:39:43');
INSERT INTO `website_setting` VALUES ('81', '25', '2', '否', '0', '0', '0.00', null, null, '1', '1', '2017-06-01 11:39:48');
INSERT INTO `website_setting` VALUES ('82', '28', '1', '内容', '0', '0', '0.00', null, 'key不要随意改，影响到内容显示，文章管理内的各个模块和前台的文章模块都会用到此key来判断归属', '0', '1', '2017-06-02 16:57:48');
INSERT INTO `website_setting` VALUES ('83', '28', '2', '新闻', '0', '0', '0.00', null, 'key不要随意改，影响到内容显示，文章管理内的各个模块和前台的文章模块都会用到此key来判断归属', '0', '1', '2017-06-02 16:58:10');
INSERT INTO `website_setting` VALUES ('84', '28', '3', '广告', '0', '0', '0.00', null, 'key不要随意改，影响到内容显示，文章管理内的各个模块和前台的文章模块都会用到此key来判断归属', '0', '1', '2017-06-05 10:46:54');
INSERT INTO `website_setting` VALUES ('85', '28', '4', '临时公告', '0', '0', '0.00', null, 'key不要随意改，影响到内容显示，文章管理内的各个模块和前台的文章模块都会用到此key来判断归属', '0', '1', '2017-06-05 10:47:10');

-- ----------------------------
-- Table structure for website_setting_category
-- ----------------------------
DROP TABLE IF EXISTS `website_setting_category`;
CREATE TABLE `website_setting_category` (
  `setting_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(50) DEFAULT NULL COMMENT '所属模块',
  `key` varchar(50) DEFAULT NULL COMMENT '标识',
  `title` varchar(50) DEFAULT NULL COMMENT '标题',
  `description` varchar(100) DEFAULT NULL COMMENT '描述',
  `deleted` tinyint(4) DEFAULT '1' COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_category_id`),
  UNIQUE KEY `SETTING_CATEGORY_ID` (`setting_category_id`) USING BTREE,
  KEY `DELETED` (`deleted`) USING BTREE,
  KEY `MODULE` (`module`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_setting_category
-- ----------------------------
INSERT INTO `website_setting_category` VALUES ('1', 'user', 'setting_user_group', '会员等级', null, '1', '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES ('2', 'user', 'setting_user_newsletter', '会员订阅邮件', null, '1', '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES ('3', 'user', 'setting_user_status', '会员状态', null, '1', '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES ('4', 'user', 'setting_user_sex', '会员性别', null, '1', '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES ('11', 'baby', 'setting_userbaby_sex', '性别', null, '1', '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES ('12', 'baby', 'setting_userbaby_zodiac', '属相', null, '1', '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES ('13', 'baby', 'setting_userbaby_constellation', '星座', null, '1', '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES ('14', 'baby', 'setting_userbaby_blood', '血型', null, '1', '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES ('15', 'baby', 'setting_userbaby_family_situation', '家庭情况', null, '1', '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES ('16', 'baby', 'setting_userbaby_baby_character', '性格', null, '1', '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES ('17', 'card_type', 'setting_user_card_type_valid_period', '有效期', null, '1', '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES ('18', 'card_type', 'setting_user_card_type', '卡种选择', null, '1', '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES ('19', 'baby_sensitive_period', 'setting_current_sensitive_period', '敏感期', null, '1', '2017-04-12 11:33:54');
INSERT INTO `website_setting_category` VALUES ('20', 'teaching_aids', 'setting_teaching_aids', '教具', null, '1', '2017-04-19 15:10:03');
INSERT INTO `website_setting_category` VALUES ('21', 'teaching_times', 'setting_teaching_times', '上课时间设置', null, '1', '2017-04-19 16:58:22');
INSERT INTO `website_setting_category` VALUES ('22', 'website', 'setting_website_status', '状态', null, '1', '2017-04-24 15:36:45');
INSERT INTO `website_setting_category` VALUES ('23', 'record', 'setting_income_and_expenditure', '记录类型', null, '1', '2017-04-24 17:38:40');
INSERT INTO `website_setting_category` VALUES ('24', 'article', 'setting_article_category_status', '是否启用', '文章栏目是否启用', '1', '2017-05-31 16:15:50');
INSERT INTO `website_setting_category` VALUES ('25', 'article', 'setting_article_status', '是否启用', '各类文章是否启用', '1', '2017-06-01 11:38:03');
INSERT INTO `website_setting_category` VALUES ('28', 'article', 'setting_article_type', '文章模块', '所有栏目可选的文章模块', '1', '2017-06-02 16:15:21');

-- ----------------------------
-- Table structure for website_system_info
-- ----------------------------
DROP TABLE IF EXISTS `website_system_info`;
CREATE TABLE `website_system_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `content` text,
  `status` tinyint(4) DEFAULT '1',
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_system_info
-- ----------------------------
INSERT INTO `website_system_info` VALUES ('1', '系统管理员', 'swoole聊天室', '欢迎您的访问', '1', '2017-02-13 11:50:41', '2017-02-13 11:30:32');

-- ----------------------------
-- Table structure for website_teaching
-- ----------------------------
DROP TABLE IF EXISTS `website_teaching`;
CREATE TABLE `website_teaching` (
  `teaching_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `baby_id` int(11) NOT NULL COMMENT '会员宝宝ID',
  `user_card_id` int(11) NOT NULL COMMENT '会员卡种ID',
  `title` varchar(50) DEFAULT NULL COMMENT '课程名称',
  `setting_id` int(11) DEFAULT NULL COMMENT '敏感期课程配置ID',
  `teaching_date` date DEFAULT NULL COMMENT '上课日期',
  `teaching_start_time` varchar(20) DEFAULT NULL COMMENT '上课开始时间',
  `teaching_end_time` varchar(20) DEFAULT NULL COMMENT '上课结束时间',
  `deleted` tinyint(4) DEFAULT '1' COMMENT '是否删除 1未删除 2已删除',
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`teaching_id`),
  UNIQUE KEY `TEACHING_ID` (`teaching_id`),
  KEY `USER_ID` (`user_id`),
  KEY `BABY_ID` (`baby_id`),
  KEY `TITLE` (`title`),
  KEY `CREATE_TIME` (`create_time`),
  KEY `USER_CARD_ID` (`user_card_id`) USING BTREE,
  KEY `SETTING_ID` (`setting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_teaching
-- ----------------------------
INSERT INTO `website_teaching` VALUES ('1', '13', '1', '8', '兔子课', '57', '2017-11-14', '13:00', '15:00', '1', '2017-11-14 14:44:47', '2017-05-09 16:04:24');
INSERT INTO `website_teaching` VALUES ('2', '14', '2', '9', '测试的课程', '65', '2017-11-14', '15:00', '17:00', '1', '2017-11-14 14:44:41', '2017-05-09 16:08:45');
INSERT INTO `website_teaching` VALUES ('3', '13', '1', '8', '测试课程2', '58', '2017-11-15', '09:00', '11:00', '1', '2017-11-14 14:44:32', '2017-11-14 14:43:29');
INSERT INTO `website_teaching` VALUES ('4', '13', '6', '8', '测试课程3', '60', '2017-11-18', '10:00', '12:00', '1', '2017-11-14 14:45:13', '2017-11-14 14:44:24');
INSERT INTO `website_teaching` VALUES ('5', '13', '1', '8', '', '62', '2017-12-13', '10:00', '12:00', '1', '2017-11-14 17:07:26', '2017-11-14 17:07:26');

-- ----------------------------
-- Table structure for website_teaching_tools
-- ----------------------------
DROP TABLE IF EXISTS `website_teaching_tools`;
CREATE TABLE `website_teaching_tools` (
  `teaching_tool_id` int(11) NOT NULL AUTO_INCREMENT,
  `teaching_id` int(11) NOT NULL,
  `setting_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`teaching_tool_id`),
  UNIQUE KEY `TEACHING_TOOL_ID` (`teaching_tool_id`),
  KEY `TEACHING_ID` (`teaching_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_teaching_tools
-- ----------------------------
INSERT INTO `website_teaching_tools` VALUES ('1', '1', '68');
INSERT INTO `website_teaching_tools` VALUES ('2', '1', '69');
INSERT INTO `website_teaching_tools` VALUES ('3', '1', '70');
INSERT INTO `website_teaching_tools` VALUES ('4', '2', '68');
INSERT INTO `website_teaching_tools` VALUES ('5', '3', '69');
INSERT INTO `website_teaching_tools` VALUES ('6', '4', '70');
INSERT INTO `website_teaching_tools` VALUES ('7', '5', '68');

-- ----------------------------
-- Table structure for website_user
-- ----------------------------
DROP TABLE IF EXISTS `website_user`;
CREATE TABLE `website_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_name` varchar(20) DEFAULT NULL COMMENT '母亲姓名',
  `tel` varchar(20) DEFAULT NULL COMMENT '母亲电话',
  `job` varchar(50) DEFAULT NULL COMMENT '母亲职业',
  `email` varchar(50) DEFAULT NULL COMMENT '邮箱',
  `user_group_id` tinyint(4) unsigned DEFAULT '1' COMMENT '会员等级',
  `newsletter` tinyint(4) DEFAULT '1' COMMENT '订阅邮件',
  `deleted` tinyint(4) DEFAULT '1' COMMENT '是否删除 1未删除 2已删除',
  `last_login_time` datetime DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `USER_ID` (`user_id`) USING BTREE,
  KEY `DELETED` (`deleted`),
  KEY `TEL` (`tel`) USING BTREE,
  KEY `PARENT_NAME` (`parent_name`) USING BTREE,
  KEY `JOB` (`job`) USING BTREE,
  KEY `CREATE_TIME` (`create_time`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_user
-- ----------------------------
INSERT INTO `website_user` VALUES ('13', '牙签哥', '13444444444', '教育', 'abcde@qq.com', '1', '3', '1', null, '2017-04-05 17:12:24');
INSERT INTO `website_user` VALUES ('14', '测试', '13333333333', 'aaa', 'aaa@qq.com', '1', '3', '1', null, '2017-04-12 10:37:46');

-- ----------------------------
-- Table structure for website_user_address
-- ----------------------------
DROP TABLE IF EXISTS `website_user_address`;
CREATE TABLE `website_user_address` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '会员ID',
  `realname` varchar(20) DEFAULT NULL COMMENT '真实姓名',
  `tel` varchar(20) DEFAULT NULL COMMENT '电话',
  `province` int(11) DEFAULT '0' COMMENT '省份',
  `city` int(11) DEFAULT '0' COMMENT '城市',
  `zone` int(11) DEFAULT '0' COMMENT '地区',
  `address` text COMMENT '详细地址',
  `postcode` varchar(50) DEFAULT NULL COMMENT '邮政编码',
  `default` tinyint(4) DEFAULT '2' COMMENT '默认地址',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`address_id`),
  UNIQUE KEY `ADDRESS_ID` (`address_id`) USING BTREE,
  KEY `PROVINCE` (`province`) USING BTREE,
  KEY `USER_ID` (`user_id`) USING BTREE,
  KEY `CITY` (`city`) USING BTREE,
  KEY `ZONE` (`zone`) USING BTREE,
  KEY `DEFAULT` (`default`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_user_address
-- ----------------------------

-- ----------------------------
-- Table structure for website_user_baby
-- ----------------------------
DROP TABLE IF EXISTS `website_user_baby`;
CREATE TABLE `website_user_baby` (
  `baby_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `name` varchar(20) DEFAULT NULL COMMENT '宝宝姓名',
  `age` int(11) DEFAULT '0' COMMENT '宝宝年龄',
  `body_height` float(10,2) DEFAULT '0.00' COMMENT '宝宝身高',
  `body_weight` float(10,2) DEFAULT '0.00' COMMENT '宝宝体重',
  `sex` tinyint(4) DEFAULT '0' COMMENT '宝宝性别',
  `birthday` date DEFAULT NULL COMMENT '出生日期',
  `zodiac` tinyint(4) DEFAULT '0' COMMENT '宝宝属相',
  `constellation` tinyint(4) DEFAULT '0' COMMENT '宝宝星座',
  `blood_type` tinyint(4) DEFAULT '0' COMMENT '宝宝血型',
  `family_situation` tinyint(4) DEFAULT '0' COMMENT '家庭情况',
  `breakfast` varchar(100) DEFAULT NULL COMMENT '早饭',
  `lunch` varchar(100) DEFAULT NULL COMMENT '午饭',
  `dinner` varchar(100) DEFAULT NULL COMMENT '晚饭',
  `sleeping_time` varchar(100) DEFAULT NULL COMMENT '睡眠时间',
  `deleted` tinyint(4) DEFAULT '1' COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`baby_id`),
  UNIQUE KEY `BABY_ID` (`baby_id`) USING BTREE,
  KEY `USER_ID` (`user_id`) USING BTREE,
  KEY `BIRTHDAY` (`birthday`) USING BTREE,
  KEY `NAME` (`name`) USING BTREE,
  KEY `DELETED` (`deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_user_baby
-- ----------------------------
INSERT INTO `website_user_baby` VALUES ('1', '13', '兔子', '5', '60.00', '50.00', '15', '2011-05-03', '19', '29', '43', '46', '早饭时间', '午饭时间', '晚饭时间', '睡眠时间', '1', '2017-04-11 15:36:58');
INSERT INTO `website_user_baby` VALUES ('2', '14', 'ceshi', '1', '11.00', '12.00', '15', '2017-04-12', '19', '29', '43', '46', null, null, null, null, '1', '2017-04-12 15:01:28');
INSERT INTO `website_user_baby` VALUES ('6', '13', '兔子2号', '6', '0.00', '0.00', '15', '2017-05-09', '0', '0', '0', '0', null, null, null, null, '1', '2017-05-09 17:09:17');

-- ----------------------------
-- Table structure for website_user_baby_character
-- ----------------------------
DROP TABLE IF EXISTS `website_user_baby_character`;
CREATE TABLE `website_user_baby_character` (
  `character_id` int(11) NOT NULL AUTO_INCREMENT,
  `baby_id` int(11) NOT NULL,
  `setting_id` int(11) DEFAULT '0' COMMENT '敏感期选项key',
  PRIMARY KEY (`character_id`),
  UNIQUE KEY `CHARACTER_ID` (`character_id`) USING BTREE,
  KEY `BABY_ID` (`baby_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_user_baby_character
-- ----------------------------
INSERT INTO `website_user_baby_character` VALUES ('61', '6', '48');
INSERT INTO `website_user_baby_character` VALUES ('63', '6', '50');
INSERT INTO `website_user_baby_character` VALUES ('64', '6', '51');

-- ----------------------------
-- Table structure for website_user_baby_sensitive_period
-- ----------------------------
DROP TABLE IF EXISTS `website_user_baby_sensitive_period`;
CREATE TABLE `website_user_baby_sensitive_period` (
  `sensitive_period_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `baby_id` int(11) NOT NULL DEFAULT '0',
  `advantage` text COMMENT '优点',
  `characteristic` text COMMENT ' 特点',
  `habit` text COMMENT '习惯',
  `deleted` tinyint(4) DEFAULT '1' COMMENT '是否删除 1未删除 2已删除',
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sensitive_period_id`),
  UNIQUE KEY `SENSITIVE_PERIOD_ID` (`sensitive_period_id`) USING BTREE,
  KEY `USER_ID` (`user_id`),
  KEY `BABY_ID` (`baby_id`),
  KEY `CREATE_TIME` (`create_time`),
  KEY `DELETED` (`deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_user_baby_sensitive_period
-- ----------------------------
INSERT INTO `website_user_baby_sensitive_period` VALUES ('11', '13', '1', 'aaa1', 'bbb2', 'ccc3', '1', '2017-04-17 15:46:02', '2017-04-17 15:44:57');
INSERT INTO `website_user_baby_sensitive_period` VALUES ('12', '14', '2', 'a1', 'b1', 'c1', '1', '2017-04-17 15:47:18', '2017-04-17 15:47:18');
INSERT INTO `website_user_baby_sensitive_period` VALUES ('13', '14', '2', '123', '456', '789', '1', '2017-04-17 15:50:54', '2017-04-17 15:50:54');
INSERT INTO `website_user_baby_sensitive_period` VALUES ('14', '13', '1', '', '', '', '1', '2017-04-26 14:42:57', '2017-04-26 14:42:57');

-- ----------------------------
-- Table structure for website_user_baby_sensitive_period_option
-- ----------------------------
DROP TABLE IF EXISTS `website_user_baby_sensitive_period_option`;
CREATE TABLE `website_user_baby_sensitive_period_option` (
  `sensitive_period_option_id` int(11) NOT NULL AUTO_INCREMENT,
  `sensitive_period_id` int(11) NOT NULL,
  `setting_id` int(11) DEFAULT '0' COMMENT '敏感期选项key',
  PRIMARY KEY (`sensitive_period_option_id`),
  UNIQUE KEY `SENSITIVE_PERIOD_OPTION_ID` (`sensitive_period_option_id`) USING BTREE,
  KEY `SENSITIVE_PERIOD_ID` (`sensitive_period_id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_user_baby_sensitive_period_option
-- ----------------------------
INSERT INTO `website_user_baby_sensitive_period_option` VALUES ('29', '11', '57');
INSERT INTO `website_user_baby_sensitive_period_option` VALUES ('30', '11', '58');
INSERT INTO `website_user_baby_sensitive_period_option` VALUES ('33', '11', '61');
INSERT INTO `website_user_baby_sensitive_period_option` VALUES ('35', '11', '63');
INSERT INTO `website_user_baby_sensitive_period_option` VALUES ('36', '11', '65');
INSERT INTO `website_user_baby_sensitive_period_option` VALUES ('37', '11', '64');
INSERT INTO `website_user_baby_sensitive_period_option` VALUES ('38', '12', '57');
INSERT INTO `website_user_baby_sensitive_period_option` VALUES ('39', '12', '58');
INSERT INTO `website_user_baby_sensitive_period_option` VALUES ('40', '12', '59');
INSERT INTO `website_user_baby_sensitive_period_option` VALUES ('41', '12', '60');
INSERT INTO `website_user_baby_sensitive_period_option` VALUES ('42', '12', '65');
INSERT INTO `website_user_baby_sensitive_period_option` VALUES ('43', '13', '63');
INSERT INTO `website_user_baby_sensitive_period_option` VALUES ('44', '13', '60');
INSERT INTO `website_user_baby_sensitive_period_option` VALUES ('47', '14', '61');
INSERT INTO `website_user_baby_sensitive_period_option` VALUES ('48', '14', '62');

-- ----------------------------
-- Table structure for website_user_card
-- ----------------------------
DROP TABLE IF EXISTS `website_user_card`;
CREATE TABLE `website_user_card` (
  `user_card_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `baby_id` int(11) NOT NULL,
  `card_number` varchar(20) NOT NULL COMMENT '卡号',
  `setting_id` int(11) NOT NULL DEFAULT '0' COMMENT '会员卡种配置ID',
  `money` float(10,2) DEFAULT '0.00' COMMENT '此次办卡费用',
  `remaining_times` int(11) DEFAULT '0' COMMENT '剩余使用次数',
  `times` int(11) DEFAULT '0' COMMENT '续卡次数',
  `card_start_time` date DEFAULT NULL COMMENT '办卡事件',
  `card_end_time` date DEFAULT NULL COMMENT '到期时间',
  `deleted` tinyint(4) DEFAULT '1' COMMENT '是否删除 1未删除 2已删除',
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_card_id`),
  UNIQUE KEY `USER_CARD_ID` (`user_card_id`) USING BTREE,
  UNIQUE KEY `CARD_NUMBER` (`card_number`) USING BTREE,
  KEY `USER_ID` (`user_id`),
  KEY `CARD_START_TIME` (`card_start_time`),
  KEY `CARD_END_TIME` (`card_end_time`),
  KEY `BABY_ID` (`baby_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_user_card
-- ----------------------------
INSERT INTO `website_user_card` VALUES ('8', '13', '1', '201705080001', '56', '2999.00', '192', '2', '2017-05-08', '2019-05-08', '1', '2017-11-14 17:07:27', '2017-05-08 16:42:55');
INSERT INTO `website_user_card` VALUES ('9', '14', '2', '201705080002', '67', '9999.00', '197', '0', '2017-05-08', '2019-05-08', '1', '2017-05-09 16:08:45', '2017-05-08 16:43:06');

-- ----------------------------
-- Table structure for website_user_continued_card
-- ----------------------------
DROP TABLE IF EXISTS `website_user_continued_card`;
CREATE TABLE `website_user_continued_card` (
  `user_continued_card_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_card_id` int(11) NOT NULL DEFAULT '0' COMMENT '会员卡种ID,为0时,代表是续卡前的卡本身数据',
  `user_id` int(11) NOT NULL,
  `baby_id` int(11) NOT NULL,
  `card_number` varchar(20) NOT NULL COMMENT '卡号',
  `setting_id` int(11) NOT NULL DEFAULT '0' COMMENT '会员卡种配置ID',
  `money` float(10,2) DEFAULT '0.00' COMMENT '续卡费用',
  `remaining_times` int(11) DEFAULT '0' COMMENT '剩余使用次数',
  `card_start_time` date DEFAULT NULL COMMENT '办卡事件',
  `card_end_time` date DEFAULT NULL COMMENT '到期时间',
  `times` int(11) DEFAULT '0' COMMENT '第几次续卡,如果为0,则是续卡前的卡本身数据',
  `deleted` tinyint(4) DEFAULT '1' COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_continued_card_id`),
  UNIQUE KEY `USER_CONTINUED_CARD_ID` (`user_continued_card_id`) USING BTREE,
  KEY `CARD_START_TIME` (`card_start_time`),
  KEY `CARD_END_TIME` (`card_end_time`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_user_continued_card
-- ----------------------------
INSERT INTO `website_user_continued_card` VALUES ('34', '8', '13', '1', '201705080001', '56', '0.00', '48', '2017-05-08', '2017-11-08', '0', '1', '2017-05-08 16:43:18');
INSERT INTO `website_user_continued_card` VALUES ('35', '8', '13', '1', '201705080001', '56', '2999.00', '96', '2017-05-08', '2018-05-08', '1', '1', '2017-05-08 16:43:18');
INSERT INTO `website_user_continued_card` VALUES ('36', '8', '13', '1', '201705080001', '56', '4999.00', '196', '2017-05-08', '2019-05-08', '2', '1', '2017-05-08 16:44:51');

-- ----------------------------
-- Table structure for website_user_continued_card_log
-- ----------------------------
DROP TABLE IF EXISTS `website_user_continued_card_log`;
CREATE TABLE `website_user_continued_card_log` (
  `user_continued_card_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_continued_card_id` int(11) NOT NULL,
  `user_card_id` int(11) NOT NULL,
  `setting_id` int(11) NOT NULL DEFAULT '0' COMMENT '会员卡种有效期配置ID',
  `add_remaining_times` int(11) DEFAULT '0' COMMENT '剩余使用次数',
  `money` float(10,2) DEFAULT '0.00',
  `continued_card_start_time` date DEFAULT NULL COMMENT '开始续卡的时间',
  `continued_card_end_time` date DEFAULT NULL COMMENT '续卡后的时间',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_continued_card_log_id`),
  UNIQUE KEY `USER_CONTINUED_CARD_LOG_ID` (`user_continued_card_log_id`) USING BTREE,
  KEY `USER_CONTINUED_CARD_ID` (`user_continued_card_id`),
  KEY `CONTINUED_CARD_START_TIME` (`continued_card_start_time`) USING BTREE,
  KEY `CONTINUED_CARD_END_TIME` (`continued_card_end_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_user_continued_card_log
-- ----------------------------
INSERT INTO `website_user_continued_card_log` VALUES ('10', '35', '8', '52', '48', '2999.00', '2017-11-08', '2018-05-08', '2017-05-08 16:43:18');
INSERT INTO `website_user_continued_card_log` VALUES ('11', '36', '8', '53', '100', '4999.00', '2018-05-08', '2019-05-08', '2017-05-08 16:44:51');

-- ----------------------------
-- Table structure for website_website
-- ----------------------------
DROP TABLE IF EXISTS `website_website`;
CREATE TABLE `website_website` (
  `website_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) DEFAULT NULL,
  `url` varchar(50) DEFAULT NULL,
  `meta_title` varchar(20) DEFAULT NULL,
  `meta_description` text,
  `meta_keyword` text,
  `website_logo` varchar(100) DEFAULT NULL,
  `website_icon` varchar(100) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0' COMMENT '是否启用设置信息,此选项在配置页面配置',
  `deleted` tinyint(4) DEFAULT '1' COMMENT '是否删除 1否 2是',
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`website_id`),
  UNIQUE KEY `WEBSITE_ID` (`website_id`),
  KEY `DELETED` (`deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of website_website
-- ----------------------------
INSERT INTO `website_website` VALUES ('2', '文章社区', 'local.bb.com', '文章社区', '文章社区', '文章社区', 'catalog/tuzi.jpg', '', '1', '1', '2017-06-06 14:58:11', '2017-04-24 16:11:49');
