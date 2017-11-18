/*
 Navicat Premium Data Transfer

 Source Server         : locahost
 Source Server Type    : MySQL
 Source Server Version : 100128
 Source Host           : localhost:3306
 Source Schema         : car

 Target Server Type    : MySQL
 Target Server Version : 100128
 File Encoding         : 65001

 Date: 18/11/2017 23:37:42
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for website_admin
-- ----------------------------
DROP TABLE IF EXISTS `website_admin`;
CREATE TABLE `website_admin`  (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `password` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `salt` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `group` tinyint(4) NULL DEFAULT 0 COMMENT '权限组 超级管理员还是普通管理员',
  `tel` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `last_login_time` datetime(0) NULL DEFAULT NULL,
  `deleted` tinyint(4) NULL DEFAULT 1 COMMENT '是否删除 1否 2是',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`admin_id`) USING BTREE,
  UNIQUE INDEX `ADMIN_ID`(`admin_id`) USING BTREE,
  INDEX `DELETED`(`deleted`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_admin
-- ----------------------------
INSERT INTO `website_admin` VALUES (1, 'superadmin', 'db1c91a1dec1865c9c3e519c1de442afaa4fa778', 'vQ5N4kMrlS', 1, '13656480135', '403520515@qq.com', '2017-11-18 15:13:36', 1, '2017-04-28 16:01:32');
INSERT INTO `website_admin` VALUES (2, 'admin', 'c5937201355066b46610a18838425036e4f2ef52', 'gGSBezIQAI', 2, '18866830116', 'long83116@qq.com', '2017-05-24 16:18:08', 1, '2017-04-28 16:04:36');

-- ----------------------------
-- Table structure for website_article_advertising
-- ----------------------------
DROP TABLE IF EXISTS `website_article_advertising`;
CREATE TABLE `website_article_advertising`  (
  `advertising_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标题',
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '是否启用,对应配置表',
  `image_path` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图片地址',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '内容',
  `deleted` tinyint(4) NULL DEFAULT 1 COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`advertising_id`) USING BTREE,
  UNIQUE INDEX `ADVERTISING_ID`(`advertising_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_article_advertising
-- ----------------------------
INSERT INTO `website_article_advertising` VALUES (12, '蒙氏教育的', 80, 'catalog/tuzi1.jpg', '&lt;p&gt;蒙氏教育蒙氏教育蒙氏教育蒙氏教育蒙氏教育&lt;/p&gt;', 1, '2017-06-08 13:39:24');

-- ----------------------------
-- Table structure for website_article_advertising_category
-- ----------------------------
DROP TABLE IF EXISTS `website_article_advertising_category`;
CREATE TABLE `website_article_advertising_category`  (
  `association_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL DEFAULT 0 COMMENT '栏目ID',
  `article_id` int(11) NOT NULL COMMENT '文章ID',
  PRIMARY KEY (`association_id`) USING BTREE,
  UNIQUE INDEX `ASSOCIATION_id`(`association_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 20 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '栏目和文章关联表，多对多关系' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_article_advertising_category
-- ----------------------------
INSERT INTO `website_article_advertising_category` VALUES (18, 12, 12);
INSERT INTO `website_article_advertising_category` VALUES (19, 11, 12);

-- ----------------------------
-- Table structure for website_article_advertising_images
-- ----------------------------
DROP TABLE IF EXISTS `website_article_advertising_images`;
CREATE TABLE `website_article_advertising_images`  (
  `advertising_image_id` int(11) NOT NULL AUTO_INCREMENT,
  `advertising_id` int(11) NOT NULL DEFAULT 0 COMMENT '内容ID',
  `image_path` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图片地址',
  `sort` int(11) NULL DEFAULT 0 COMMENT '排序',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`advertising_image_id`) USING BTREE,
  UNIQUE INDEX `ADVERTISING_IMAGE_ID`(`advertising_image_id`) USING BTREE,
  INDEX `ADVERTISING_ID`(`advertising_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 64 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_article_advertising_images
-- ----------------------------
INSERT INTO `website_article_advertising_images` VALUES (61, 12, 'catalog/180.jpg', 3, '2017-06-08 13:39:24');
INSERT INTO `website_article_advertising_images` VALUES (62, 12, 'catalog/3333.jpg', 2, '2017-06-08 13:39:24');
INSERT INTO `website_article_advertising_images` VALUES (63, 12, 'catalog/121212.jpg', 4, '2017-06-08 13:55:03');

-- ----------------------------
-- Table structure for website_article_announcement
-- ----------------------------
DROP TABLE IF EXISTS `website_article_announcement`;
CREATE TABLE `website_article_announcement`  (
  `announcement_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标题',
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '是否启用,对应配置表',
  `image_path` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图片地址',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '内容',
  `deleted` tinyint(4) NULL DEFAULT 1 COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`announcement_id`) USING BTREE,
  UNIQUE INDEX `ANNOUNCEMENT_ID`(`announcement_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_article_announcement
-- ----------------------------
INSERT INTO `website_article_announcement` VALUES (15, '网站基本框架建成1', 80, 'catalog/tuzi2.jpg', '&lt;p&gt;网站基本框架建成网站基本框架建成网站基本框架建成网站基本框架建成&lt;/p&gt;', 1, '2017-06-08 14:26:57');

-- ----------------------------
-- Table structure for website_article_announcement_category
-- ----------------------------
DROP TABLE IF EXISTS `website_article_announcement_category`;
CREATE TABLE `website_article_announcement_category`  (
  `association_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL DEFAULT 0 COMMENT '栏目ID',
  `article_id` int(11) NOT NULL COMMENT '文章ID',
  PRIMARY KEY (`association_id`) USING BTREE,
  UNIQUE INDEX `ASSOCIATION_id`(`association_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 27 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '栏目和文章关联表，多对多关系' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_article_announcement_category
-- ----------------------------
INSERT INTO `website_article_announcement_category` VALUES (24, 5, 15);
INSERT INTO `website_article_announcement_category` VALUES (25, 16, 15);
INSERT INTO `website_article_announcement_category` VALUES (26, 14, 15);

-- ----------------------------
-- Table structure for website_article_announcement_images
-- ----------------------------
DROP TABLE IF EXISTS `website_article_announcement_images`;
CREATE TABLE `website_article_announcement_images`  (
  `announcement_image_id` int(11) NOT NULL AUTO_INCREMENT,
  `announcement_id` int(11) NOT NULL DEFAULT 0 COMMENT '内容ID',
  `image_path` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图片地址',
  `sort` int(11) NULL DEFAULT 0 COMMENT '排序',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`announcement_image_id`) USING BTREE,
  UNIQUE INDEX `ANNOUNCEMENT_IMAGE_ID`(`announcement_image_id`) USING BTREE,
  INDEX `ANNOUNCEMENT_ID`(`announcement_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 67 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_article_announcement_images
-- ----------------------------
INSERT INTO `website_article_announcement_images` VALUES (64, 15, 'catalog/121212.jpg', 3, '2017-06-08 14:26:58');
INSERT INTO `website_article_announcement_images` VALUES (65, 15, 'catalog/180.jpg', 4, '2017-06-08 14:26:58');
INSERT INTO `website_article_announcement_images` VALUES (66, 15, 'catalog/3333.jpg', 1, '2017-06-08 14:35:33');

-- ----------------------------
-- Table structure for website_article_category
-- ----------------------------
DROP TABLE IF EXISTS `website_article_category`;
CREATE TABLE `website_article_category`  (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标题',
  `desc` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '描述',
  `article_type` int(11) NULL DEFAULT 0 COMMENT '文章类型ID,对应配置表',
  `status` int(11) NULL DEFAULT 0 COMMENT '是否启用,对应配置表',
  `deleted` tinyint(4) NULL DEFAULT 1 COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`category_id`) USING BTREE,
  UNIQUE INDEX `CATEGORY_ID`(`category_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_article_category
-- ----------------------------
INSERT INTO `website_article_category` VALUES (1, '经验心得', '', 82, 78, 1, '2017-05-31 16:47:29');
INSERT INTO `website_article_category` VALUES (2, '早间新闻', '', 83, 78, 1, '2017-06-05 11:27:45');
INSERT INTO `website_article_category` VALUES (4, '顶部广告位', '', 84, 78, 1, '2017-06-05 14:43:51');
INSERT INTO `website_article_category` VALUES (5, '公告展示', '', 85, 78, 1, '2017-06-05 14:44:12');
INSERT INTO `website_article_category` VALUES (6, '情绪智力', '', 82, 78, 1, '2017-06-05 14:48:48');
INSERT INTO `website_article_category` VALUES (7, '所见所闻', '', 82, 78, 1, '2017-06-05 17:01:08');
INSERT INTO `website_article_category` VALUES (8, '热门新闻', '', 83, 78, 1, '2017-06-07 15:46:39');
INSERT INTO `website_article_category` VALUES (9, '最新新闻', '', 83, 78, 1, '2017-06-07 15:46:50');
INSERT INTO `website_article_category` VALUES (10, '综合新闻', '', 83, 78, 1, '2017-06-07 15:47:01');
INSERT INTO `website_article_category` VALUES (11, '右边广告位', '', 84, 78, 1, '2017-06-08 11:55:25');
INSERT INTO `website_article_category` VALUES (12, '热门广告位', '', 84, 78, 1, '2017-06-08 11:55:35');
INSERT INTO `website_article_category` VALUES (13, '特殊广告位', '', 84, 78, 1, '2017-06-08 11:55:49');
INSERT INTO `website_article_category` VALUES (14, '最新公告', '', 85, 78, 1, '2017-06-08 14:22:53');
INSERT INTO `website_article_category` VALUES (15, '往期公告', '', 85, 78, 1, '2017-06-08 14:23:08');
INSERT INTO `website_article_category` VALUES (16, '热门公告', '', 85, 78, 1, '2017-06-08 14:24:57');

-- ----------------------------
-- Table structure for website_article_content
-- ----------------------------
DROP TABLE IF EXISTS `website_article_content`;
CREATE TABLE `website_article_content`  (
  `content_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标题',
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '是否启用,对应配置表',
  `image_path` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图片地址',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '内容',
  `deleted` tinyint(4) NULL DEFAULT 1 COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`content_id`) USING BTREE,
  UNIQUE INDEX `CONTENT_ID`(`content_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_article_content
-- ----------------------------
INSERT INTO `website_article_content` VALUES (9, '笔记', 80, 'catalog/tuzi2.jpg', '&lt;p&gt;心得体会&lt;/p&gt;', 1, '2017-06-06 14:17:48');

-- ----------------------------
-- Table structure for website_article_content_category
-- ----------------------------
DROP TABLE IF EXISTS `website_article_content_category`;
CREATE TABLE `website_article_content_category`  (
  `association_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL DEFAULT 0 COMMENT '栏目ID',
  `article_id` int(11) NOT NULL COMMENT '文章ID',
  PRIMARY KEY (`association_id`) USING BTREE,
  UNIQUE INDEX `ACCOCIATION_id`(`association_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '栏目和文章关联表，多对多关系' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_article_content_category
-- ----------------------------
INSERT INTO `website_article_content_category` VALUES (8, 7, 9);
INSERT INTO `website_article_content_category` VALUES (9, 1, 9);

-- ----------------------------
-- Table structure for website_article_content_images
-- ----------------------------
DROP TABLE IF EXISTS `website_article_content_images`;
CREATE TABLE `website_article_content_images`  (
  `content_image_id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL DEFAULT 0 COMMENT '内容ID',
  `image_path` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图片地址',
  `sort` int(11) NULL DEFAULT 0 COMMENT '排序',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`content_image_id`) USING BTREE,
  UNIQUE INDEX `CONTENT_IMAGE_ID`(`content_image_id`) USING BTREE,
  INDEX `CONTENT_ID`(`content_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 55 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_article_content_images
-- ----------------------------
INSERT INTO `website_article_content_images` VALUES (52, 9, 'catalog/180.jpg', 2, '2017-06-06 14:17:48');
INSERT INTO `website_article_content_images` VALUES (54, 9, 'catalog/3333.jpg', 3, '2017-06-06 15:43:25');

-- ----------------------------
-- Table structure for website_article_news
-- ----------------------------
DROP TABLE IF EXISTS `website_article_news`;
CREATE TABLE `website_article_news`  (
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标题',
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '是否启用,对应配置表',
  `image_path` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图片地址',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '内容',
  `deleted` tinyint(4) NULL DEFAULT 1 COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`news_id`) USING BTREE,
  UNIQUE INDEX `NEWS_ID`(`news_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_article_news
-- ----------------------------
INSERT INTO `website_article_news` VALUES (10, '测试新闻的', 80, 'catalog/121212.jpg', '&lt;p&gt;测试新闻测试新闻测试新闻测试新闻测试新闻&lt;/p&gt;', 1, '2017-06-07 15:48:04');
INSERT INTO `website_article_news` VALUES (11, '所有新闻类型', 80, 'catalog/180.jpg', '&amp;lt;p&amp;gt;所有新闻类型所有新闻类型所有新闻类型所有新闻类型&amp;lt;/p&amp;gt;', 1, '2017-06-07 15:50:29');

-- ----------------------------
-- Table structure for website_article_news_category
-- ----------------------------
DROP TABLE IF EXISTS `website_article_news_category`;
CREATE TABLE `website_article_news_category`  (
  `association_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL DEFAULT 0 COMMENT '栏目ID',
  `article_id` int(11) NOT NULL COMMENT '文章ID',
  PRIMARY KEY (`association_id`) USING BTREE,
  UNIQUE INDEX `ASSOCIATION_id`(`association_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '栏目和文章关联表，多对多关系' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_article_news_category
-- ----------------------------
INSERT INTO `website_article_news_category` VALUES (11, 8, 10);
INSERT INTO `website_article_news_category` VALUES (12, 2, 11);
INSERT INTO `website_article_news_category` VALUES (13, 8, 11);
INSERT INTO `website_article_news_category` VALUES (14, 9, 11);
INSERT INTO `website_article_news_category` VALUES (15, 10, 11);
INSERT INTO `website_article_news_category` VALUES (16, 9, 10);

-- ----------------------------
-- Table structure for website_article_news_images
-- ----------------------------
DROP TABLE IF EXISTS `website_article_news_images`;
CREATE TABLE `website_article_news_images`  (
  `news_image_id` int(11) NOT NULL AUTO_INCREMENT,
  `news_id` int(11) NOT NULL DEFAULT 0 COMMENT '内容ID',
  `image_path` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图片地址',
  `sort` int(11) NULL DEFAULT 0 COMMENT '排序',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`news_image_id`) USING BTREE,
  UNIQUE INDEX `NEWS_IMAGE_ID`(`news_image_id`) USING BTREE,
  INDEX `NEWS_ID`(`news_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 61 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_article_news_images
-- ----------------------------
INSERT INTO `website_article_news_images` VALUES (55, 10, 'catalog/3333.jpg', 2, '2017-06-07 15:48:04');
INSERT INTO `website_article_news_images` VALUES (56, 10, 'catalog/tuzi.jpg', 3, '2017-06-07 15:48:04');
INSERT INTO `website_article_news_images` VALUES (57, 11, 'catalog/tuzi2.jpg', 3, '2017-06-07 15:50:29');
INSERT INTO `website_article_news_images` VALUES (58, 11, 'catalog/3333.jpg', 1, '2017-06-07 15:50:29');
INSERT INTO `website_article_news_images` VALUES (59, 11, 'catalog/121212.jpg', 2, '2017-06-07 15:50:29');
INSERT INTO `website_article_news_images` VALUES (60, 10, 'catalog/180.jpg', 0, '2017-06-07 16:05:18');

-- ----------------------------
-- Table structure for website_company
-- ----------------------------
DROP TABLE IF EXISTS `website_company`;
CREATE TABLE `website_company`  (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NULL DEFAULT 0 COMMENT '父类商户ID，为0是代表是主商户',
  `tel` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '手机号',
  `numbering` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '企业会员编号',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '名称',
  `type` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '类型',
  `address` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '住所',
  `legal_person` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '法人',
  `registered_capital` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '注册资本',
  `date_time` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '成立日期',
  `operating_period` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '营业期限',
  `image_path` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '封面图地址',
  `score` int(11) NULL DEFAULT 0 COMMENT '评分',
  `group` tinyint(4) NULL DEFAULT 0,
  `password` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `salt` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `last_login_time` datetime(0) NULL DEFAULT NULL,
  `deleted` tinyint(4) NULL DEFAULT 1 COMMENT '是否删除 1否 2是',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`company_id`) USING BTREE,
  UNIQUE INDEX `COMPANY_ID`(`company_id`) USING BTREE,
  INDEX `DELETED`(`deleted`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_company
-- ----------------------------
INSERT INTO `website_company` VALUES (1, 0, '13656480135', 'CPY0000001', 'supercompany', '2', '3', '4', '5', '6', '7', NULL, 0, 0, 'cc554b52696c1cab6dde8fd8c034ea226e4e0a01', 'eY0ue2D5vV', '2017-11-18 14:34:18', 1, '2017-04-28 16:01:32');
INSERT INTO `website_company` VALUES (2, 1, '13333333332', 'CPY0000002', 'company', '2', '3', '4', '5', '6', '7', NULL, 0, 0, 'cc554b52696c1cab6dde8fd8c034ea226e4e0a01', 'eY0ue2D5vV', '2017-11-18 11:10:32', 1, '2017-04-28 16:04:36');
INSERT INTO `website_company` VALUES (3, 0, '13333333333', 'CPY0000003', 'test', '2', '3', '4', '5', '6', '7', NULL, 0, 0, 'cc554b52696c1cab6dde8fd8c034ea226e4e0a01', 'eY0ue2D5vV', '2017-11-18 11:11:46', 1, '2017-04-28 16:04:36');
INSERT INTO `website_company` VALUES (4, 1, '13333333334', 'CPY0000004', 't1', '1', '1', '1', '1', '1', '1', NULL, 0, 0, 'cc554b52696c1cab6dde8fd8c034ea226e4e0a01', 'eY0ue2D5vV', '2017-11-18 11:10:49', 1, '2017-11-18 17:34:59');
INSERT INTO `website_company` VALUES (5, 3, '13333333335', 'CPY0000005', '22', '33', '44', '55', '66', '77', '88', NULL, 0, 0, 'cc554b52696c1cab6dde8fd8c034ea226e4e0a01', 'eY0ue2D5vV', '2017-11-18 11:11:31', 1, '2017-11-18 17:47:56');
INSERT INTO `website_company` VALUES (9, 0, '13666666666', 'CPY0000006', '1', '1', '1', '1', '1', '1', '1', NULL, 0, 0, '27bee50dc789a0389277ee95fa868a0ed36f2ab8', 'WqW44osPcs', '2017-11-18 14:29:06', 1, '2017-11-18 21:29:01');
INSERT INTO `website_company` VALUES (11, 9, '13666666667', 'CPY0000007', '3', '1', '1', '1', '1', '1', '1', NULL, 0, 0, '36e603bb502f59bf09a4f2d18017b64b09a34eee', 'mvvzoZa2Xt', '2017-11-18 14:34:07', 1, '2017-11-18 21:30:42');

-- ----------------------------
-- Table structure for website_company_score
-- ----------------------------
DROP TABLE IF EXISTS `website_company_score`;
CREATE TABLE `website_company_score`  (
  `score_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0 COMMENT '打分用户ID',
  `score` int(11) NULL DEFAULT 0 COMMENT '分数',
  `description` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '描述',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`score_id`) USING BTREE,
  UNIQUE INDEX `SCORE_ID`(`score_id`) USING BTREE,
  INDEX `COMPANY_ID`(`company_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for website_config
-- ----------------------------
DROP TABLE IF EXISTS `website_config`;
CREATE TABLE `website_config`  (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `column` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '所属栏目',
  `module` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '所属模块',
  `title` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标题',
  `key` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标识',
  `value` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '内容',
  `sort` int(11) NULL DEFAULT 0 COMMENT '排序',
  PRIMARY KEY (`config_id`) USING BTREE,
  UNIQUE INDEX `CONFIG_CATEGORY_ID`(`config_id`) USING BTREE,
  INDEX `MODULE`(`module`) USING BTREE,
  INDEX `category`(`column`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_config
-- ----------------------------
INSERT INTO `website_config` VALUES (5, '其他', 'page', '网站后台每页显示数量', 'paging_limit', '20', 0);
INSERT INTO `website_config` VALUES (6, '其他', 'page', '分页规则', 'paging_rules', '显示 %d 到 %d / %d (总 %d 页)', 0);

-- ----------------------------
-- Table structure for website_record
-- ----------------------------
DROP TABLE IF EXISTS `website_record`;
CREATE TABLE `website_record`  (
  `record_id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_id` tinyint(4) NULL DEFAULT NULL COMMENT '信息记录配置ID',
  `money` float(10, 2) NULL DEFAULT 0.00 COMMENT '金额',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '详情',
  `deleted` tinyint(4) NULL DEFAULT 1 COMMENT '是否删除 1否 2是',
  `update_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0),
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`record_id`) USING BTREE,
  INDEX `RECORD_ID`(`record_id`) USING BTREE,
  INDEX `SETTING_ID`(`setting_id`) USING BTREE,
  INDEX `DELETED`(`deleted`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_record
-- ----------------------------
INSERT INTO `website_record` VALUES (1, 73, 100.00, 'aaaaa', 1, '2017-04-25 09:57:55', '2017-04-25 09:42:57');
INSERT INTO `website_record` VALUES (2, 74, 200.00, 'bbbbbb', 1, '2017-04-25 09:57:55', '2017-04-25 09:43:21');
INSERT INTO `website_record` VALUES (3, 73, 1000.22, '测试的测试的测试的测试的测试测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的测试的的测试的测试的测试的测试的测试的测试的测试的测试的', 1, '2017-04-25 09:57:57', '2017-04-25 09:44:52');
INSERT INTO `website_record` VALUES (4, 73, 324234.00, '', 1, '2017-04-25 10:30:35', '2017-04-25 10:30:35');
INSERT INTO `website_record` VALUES (5, 73, 0.00, '', 1, '2017-04-25 10:30:42', '2017-04-25 10:30:42');
INSERT INTO `website_record` VALUES (6, 73, 1.00, '', 1, '2017-04-25 10:31:28', '2017-04-25 10:31:28');

-- ----------------------------
-- Table structure for website_session_info
-- ----------------------------
DROP TABLE IF EXISTS `website_session_info`;
CREATE TABLE `website_session_info`  (
  `session_id` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `uid` bigint(20) NULL DEFAULT 0 COMMENT '用户ID,包括前后台',
  `data` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `user_type` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '访问网站的用户类型',
  `expiration_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`session_id`) USING BTREE,
  UNIQUE INDEX `SESSION_ID`(`session_id`) USING BTREE,
  INDEX `UID`(`uid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_session_info
-- ----------------------------
INSERT INTO `website_session_info` VALUES ('39fec53508996c8a8444d3bbde', 1, 'admin_id|s:1:\"1\";admin_name|s:10:\"superadmin\";group|s:1:\"1\";', 'admin', '2017-11-14 17:32:50');

-- ----------------------------
-- Table structure for website_setting
-- ----------------------------
DROP TABLE IF EXISTS `website_setting`;
CREATE TABLE `website_setting`  (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_category_id` int(11) NOT NULL DEFAULT 0 COMMENT '配置类别',
  `key` int(11) NOT NULL DEFAULT 0 COMMENT '唯一标示，某些地方会用到',
  `value` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '值',
  `months` int(11) NULL DEFAULT 0 COMMENT '此字段存的是会员卡种有效期月数',
  `times` int(11) NULL DEFAULT 0 COMMENT '此字段存的是会员卡种可用次数,如果是敏感期则是敏感期的课时',
  `money` float(10, 2) NULL DEFAULT 0.00 COMMENT '会员卡种的费用金额',
  `parent_setting_id` int(11) NULL DEFAULT NULL COMMENT '父类设置ID',
  `description` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '描述',
  `sort` int(11) NULL DEFAULT 0 COMMENT '同类选项排序',
  `deleted` tinyint(4) NULL DEFAULT 1 COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`) USING BTREE,
  UNIQUE INDEX `SETTING_ID`(`setting_id`) USING BTREE,
  INDEX `SETTING_CATEGORY_ID`(`setting_category_id`) USING BTREE,
  INDEX `DELETED`(`deleted`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 86 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_setting
-- ----------------------------
INSERT INTO `website_setting` VALUES (1, 1, 1, '普通', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (2, 1, 2, 'VIP', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (3, 2, 1, '启用', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (4, 2, 2, '停用', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (5, 3, 1, '启用', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (6, 3, 2, '停用', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (7, 4, 1, '男', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (8, 4, 2, '女', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (14, 11, 1, '男', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (15, 11, 2, '女', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (16, 12, 1, '鼠', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (17, 12, 2, '牛', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (18, 12, 3, '虎', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (19, 12, 4, '兔', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (20, 12, 5, '龙', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (21, 12, 6, '蛇', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (22, 12, 7, '马', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (23, 12, 8, '羊', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (24, 12, 9, '猴', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (25, 12, 10, '鸡', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (26, 12, 11, '狗', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (27, 12, 12, '猪', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (28, 13, 1, '白羊座', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (29, 13, 2, '金牛座', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (30, 13, 3, '双子座', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (31, 13, 4, '巨蟹座', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (32, 13, 5, '狮子座', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (33, 13, 6, '处女座', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (34, 13, 7, '天秤座', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (35, 13, 8, '天蝎座', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (36, 13, 9, '射手座', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (37, 13, 10, '摩羯座', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (38, 13, 11, '水瓶座', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (39, 13, 12, '双鱼座', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (40, 14, 1, 'A', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (41, 14, 2, 'B', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (42, 14, 3, 'AB', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (43, 14, 4, 'O', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (44, 14, 5, '其他', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (45, 15, 1, '老人带孩子', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (46, 15, 2, '父母带孩子', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (47, 15, 3, '其他', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (48, 16, 1, 'E', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (49, 16, 2, 'T', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (50, 16, 3, 'D', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (51, 16, 4, 'A', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-11 10:17:01');
INSERT INTO `website_setting` VALUES (52, 17, 1, '半年', 6, 0, 0.00, NULL, '', 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (53, 17, 2, '一年', 12, 0, 0.00, NULL, '', 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (54, 17, 3, '二年', 24, 0, 0.00, NULL, '', 0, 1, '2017-04-11 09:58:09');
INSERT INTO `website_setting` VALUES (56, 18, 1, '2999元48次半年内有效', 0, 48, 2999.00, 52, NULL, 0, 1, '2017-04-11 10:58:40');
INSERT INTO `website_setting` VALUES (57, 19, 1, '语言敏感期', 0, 1, 0.00, NULL, NULL, 0, 1, '2017-04-12 11:34:59');
INSERT INTO `website_setting` VALUES (58, 19, 2, '秩序敏感期', 0, 1, 0.00, NULL, NULL, 0, 1, '2017-04-12 11:35:06');
INSERT INTO `website_setting` VALUES (59, 19, 3, '感冒敏感期', 0, 1, 0.00, NULL, NULL, 0, 1, '2017-04-12 11:35:19');
INSERT INTO `website_setting` VALUES (60, 19, 4, '对细微事物感兴趣的敏感期', 0, 1, 0.00, NULL, NULL, 0, 1, '2017-04-12 11:35:41');
INSERT INTO `website_setting` VALUES (61, 19, 5, '动作敏感期', 0, 1, 0.00, NULL, NULL, 0, 1, '2017-04-12 11:35:52');
INSERT INTO `website_setting` VALUES (62, 19, 6, '社会规范敏感期', 0, 1, 0.00, NULL, NULL, 0, 1, '2017-04-12 11:36:03');
INSERT INTO `website_setting` VALUES (63, 19, 7, '书写敏感期', 0, 1, 0.00, NULL, NULL, 0, 1, '2017-04-12 11:36:18');
INSERT INTO `website_setting` VALUES (64, 19, 8, '阅读敏感期', 0, 1, 0.00, NULL, NULL, 0, 1, '2017-04-12 11:36:26');
INSERT INTO `website_setting` VALUES (65, 19, 9, '文化敏感期', 0, 3, 0.00, NULL, NULL, 0, 1, '2017-04-12 11:36:41');
INSERT INTO `website_setting` VALUES (66, 18, 2, '4999元100次一年内有效', 0, 100, 4999.00, 53, NULL, 0, 1, '2017-04-13 15:48:41');
INSERT INTO `website_setting` VALUES (67, 18, 3, '9999元200次二年内有效', 0, 200, 9999.00, 54, NULL, 0, 1, '2017-04-13 15:49:06');
INSERT INTO `website_setting` VALUES (68, 20, 1, '教具1', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-19 15:13:00');
INSERT INTO `website_setting` VALUES (69, 20, 2, '教具2', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-19 15:13:08');
INSERT INTO `website_setting` VALUES (70, 20, 3, '教具3', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-19 15:13:13');
INSERT INTO `website_setting` VALUES (71, 22, 1, '启用', 0, 0, 0.00, NULL, '请不要删除此选项或者随意改Key值，此选项的Key影响这网站的信息选择，如果删除，网站信息将出现错误。', 0, 1, '2017-04-24 15:37:05');
INSERT INTO `website_setting` VALUES (72, 22, 2, '停用', 0, 0, 0.00, NULL, '请不要删除此选项或者随意改Key值，此选项的Key影响这网站的信息选择，如果删除，网站信息将出现错误。', 0, 1, '2017-04-24 15:37:11');
INSERT INTO `website_setting` VALUES (73, 23, 1, '收入', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-24 17:39:07');
INSERT INTO `website_setting` VALUES (74, 23, 2, '支出', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-04-24 17:39:12');
INSERT INTO `website_setting` VALUES (75, 17, 4, '三年', 36, 0, 0.00, NULL, '', 0, 1, '2017-04-26 14:47:19');
INSERT INTO `website_setting` VALUES (76, 18, 4, '14999元300次三年内有效', 0, 300, 14999.00, 75, NULL, 0, 1, '2017-04-26 15:37:05');
INSERT INTO `website_setting` VALUES (77, 18, 5, '三年XXX卡种多少次', 0, 200, 9998.00, 75, NULL, 0, 1, '2017-04-26 16:28:40');
INSERT INTO `website_setting` VALUES (78, 24, 1, '是', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-05-31 16:16:43');
INSERT INTO `website_setting` VALUES (79, 24, 2, '否', 0, 0, 0.00, NULL, NULL, 1, 1, '2017-05-31 16:16:48');
INSERT INTO `website_setting` VALUES (80, 25, 1, '是', 0, 0, 0.00, NULL, NULL, 0, 1, '2017-06-01 11:39:43');
INSERT INTO `website_setting` VALUES (81, 25, 2, '否', 0, 0, 0.00, NULL, NULL, 1, 1, '2017-06-01 11:39:48');
INSERT INTO `website_setting` VALUES (82, 28, 1, '内容', 0, 0, 0.00, NULL, 'key不要随意改，影响到内容显示，文章管理内的各个模块和前台的文章模块都会用到此key来判断归属', 0, 1, '2017-06-02 16:57:48');
INSERT INTO `website_setting` VALUES (83, 28, 2, '新闻', 0, 0, 0.00, NULL, 'key不要随意改，影响到内容显示，文章管理内的各个模块和前台的文章模块都会用到此key来判断归属', 0, 1, '2017-06-02 16:58:10');
INSERT INTO `website_setting` VALUES (84, 28, 3, '广告', 0, 0, 0.00, NULL, 'key不要随意改，影响到内容显示，文章管理内的各个模块和前台的文章模块都会用到此key来判断归属', 0, 1, '2017-06-05 10:46:54');
INSERT INTO `website_setting` VALUES (85, 28, 4, '临时公告', 0, 0, 0.00, NULL, 'key不要随意改，影响到内容显示，文章管理内的各个模块和前台的文章模块都会用到此key来判断归属', 0, 1, '2017-06-05 10:47:10');

-- ----------------------------
-- Table structure for website_setting_category
-- ----------------------------
DROP TABLE IF EXISTS `website_setting_category`;
CREATE TABLE `website_setting_category`  (
  `setting_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '所属模块',
  `key` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标识',
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标题',
  `description` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '描述',
  `deleted` tinyint(4) NULL DEFAULT 1 COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_category_id`) USING BTREE,
  UNIQUE INDEX `SETTING_CATEGORY_ID`(`setting_category_id`) USING BTREE,
  INDEX `DELETED`(`deleted`) USING BTREE,
  INDEX `MODULE`(`module`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 29 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_setting_category
-- ----------------------------
INSERT INTO `website_setting_category` VALUES (1, 'user', 'setting_user_group', '会员等级', NULL, 1, '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES (2, 'user', 'setting_user_newsletter', '会员订阅邮件', NULL, 1, '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES (3, 'user', 'setting_user_status', '会员状态', NULL, 1, '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES (4, 'user', 'setting_user_sex', '会员性别', NULL, 1, '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES (11, 'baby', 'setting_userbaby_sex', '性别', NULL, 1, '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES (12, 'baby', 'setting_userbaby_zodiac', '属相', NULL, 1, '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES (13, 'baby', 'setting_userbaby_constellation', '星座', NULL, 1, '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES (14, 'baby', 'setting_userbaby_blood', '血型', NULL, 1, '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES (15, 'baby', 'setting_userbaby_family_situation', '家庭情况', NULL, 1, '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES (16, 'baby', 'setting_userbaby_baby_character', '性格', NULL, 1, '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES (17, 'card_type', 'setting_user_card_type_valid_period', '有效期', NULL, 1, '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES (18, 'card_type', 'setting_user_card_type', '卡种选择', NULL, 1, '2017-04-11 10:04:14');
INSERT INTO `website_setting_category` VALUES (19, 'baby_sensitive_period', 'setting_current_sensitive_period', '敏感期', NULL, 1, '2017-04-12 11:33:54');
INSERT INTO `website_setting_category` VALUES (20, 'teaching_aids', 'setting_teaching_aids', '教具', NULL, 1, '2017-04-19 15:10:03');
INSERT INTO `website_setting_category` VALUES (21, 'teaching_times', 'setting_teaching_times', '上课时间设置', NULL, 1, '2017-04-19 16:58:22');
INSERT INTO `website_setting_category` VALUES (22, 'website', 'setting_website_status', '状态', NULL, 1, '2017-04-24 15:36:45');
INSERT INTO `website_setting_category` VALUES (23, 'record', 'setting_income_and_expenditure', '记录类型', NULL, 1, '2017-04-24 17:38:40');
INSERT INTO `website_setting_category` VALUES (24, 'article', 'setting_article_category_status', '是否启用', '文章栏目是否启用', 1, '2017-05-31 16:15:50');
INSERT INTO `website_setting_category` VALUES (25, 'article', 'setting_article_status', '是否启用', '各类文章是否启用', 1, '2017-06-01 11:38:03');
INSERT INTO `website_setting_category` VALUES (28, 'article', 'setting_article_type', '文章模块', '所有栏目可选的文章模块', 1, '2017-06-02 16:15:21');

-- ----------------------------
-- Table structure for website_system_info
-- ----------------------------
DROP TABLE IF EXISTS `website_system_info`;
CREATE TABLE `website_system_info`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `status` tinyint(4) NULL DEFAULT 1,
  `update_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0),
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_system_info
-- ----------------------------
INSERT INTO `website_system_info` VALUES (1, '系统管理员', 'swoole聊天室', '欢迎您的访问', 1, '2017-02-13 11:50:41', '2017-02-13 11:30:32');

-- ----------------------------
-- Table structure for website_user
-- ----------------------------
DROP TABLE IF EXISTS `website_user`;
CREATE TABLE `website_user`  (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `tel` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '手机号码',
  `numbering` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '用户会员编号',
  `password` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `salt` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `deleted` tinyint(4) NULL DEFAULT 1 COMMENT '是否删除 1未删除 2已删除',
  `last_login_time` datetime(0) NULL DEFAULT NULL,
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`user_id`) USING BTREE,
  UNIQUE INDEX `USER_ID`(`user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for website_user_card
-- ----------------------------
DROP TABLE IF EXISTS `website_user_card`;
CREATE TABLE `website_user_card`  (
  `user_card_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `baby_id` int(11) NOT NULL,
  `card_number` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '卡号',
  `setting_id` int(11) NOT NULL DEFAULT 0 COMMENT '会员卡种配置ID',
  `money` float(10, 2) NULL DEFAULT 0.00 COMMENT '此次办卡费用',
  `remaining_times` int(11) NULL DEFAULT 0 COMMENT '剩余使用次数',
  `times` int(11) NULL DEFAULT 0 COMMENT '续卡次数',
  `card_start_time` date NULL DEFAULT NULL COMMENT '办卡事件',
  `card_end_time` date NULL DEFAULT NULL COMMENT '到期时间',
  `deleted` tinyint(4) NULL DEFAULT 1 COMMENT '是否删除 1未删除 2已删除',
  `update_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0),
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_card_id`) USING BTREE,
  UNIQUE INDEX `USER_CARD_ID`(`user_card_id`) USING BTREE,
  UNIQUE INDEX `CARD_NUMBER`(`card_number`) USING BTREE,
  INDEX `USER_ID`(`user_id`) USING BTREE,
  INDEX `CARD_START_TIME`(`card_start_time`) USING BTREE,
  INDEX `CARD_END_TIME`(`card_end_time`) USING BTREE,
  INDEX `BABY_ID`(`baby_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_user_card
-- ----------------------------
INSERT INTO `website_user_card` VALUES (8, 13, 1, '201705080001', 56, 2999.00, 192, 2, '2017-05-08', '2019-05-08', 1, '2017-11-14 17:07:27', '2017-05-08 16:42:55');
INSERT INTO `website_user_card` VALUES (9, 14, 2, '201705080002', 67, 9999.00, 197, 0, '2017-05-08', '2019-05-08', 1, '2017-05-09 16:08:45', '2017-05-08 16:43:06');

-- ----------------------------
-- Table structure for website_user_cars
-- ----------------------------
DROP TABLE IF EXISTS `website_user_cars`;
CREATE TABLE `website_user_cars`  (
  `car_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户标识',
  `plate_number` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '号牌号码',
  `car_type` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '车辆类型',
  `owner` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '所有人',
  `address` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '住址',
  `use_type` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '使用性质',
  `brand_type` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '品牌型号',
  `identification_number` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '车辆识别代号',
  `engine_number` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '发动机号码',
  `registration_date` datetime(0) NULL DEFAULT NULL COMMENT '注册日期',
  `accepted_date` datetime(0) NULL DEFAULT NULL COMMENT '受理日期',
  `file_number` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '档案编号',
  `people_number` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '核定人数',
  `total_mass` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '总质量',
  `dimension` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '外观尺寸',
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `deleted` tinyint(4) NULL DEFAULT 1 COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`car_id`) USING BTREE,
  UNIQUE INDEX `CAR_ID`(`car_id`) USING BTREE,
  INDEX `USER_ID`(`user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for website_user_continued_card
-- ----------------------------
DROP TABLE IF EXISTS `website_user_continued_card`;
CREATE TABLE `website_user_continued_card`  (
  `user_continued_card_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_card_id` int(11) NOT NULL DEFAULT 0 COMMENT '会员卡种ID,为0时,代表是续卡前的卡本身数据',
  `user_id` int(11) NOT NULL,
  `baby_id` int(11) NOT NULL,
  `card_number` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '卡号',
  `setting_id` int(11) NOT NULL DEFAULT 0 COMMENT '会员卡种配置ID',
  `money` float(10, 2) NULL DEFAULT 0.00 COMMENT '续卡费用',
  `remaining_times` int(11) NULL DEFAULT 0 COMMENT '剩余使用次数',
  `card_start_time` date NULL DEFAULT NULL COMMENT '办卡事件',
  `card_end_time` date NULL DEFAULT NULL COMMENT '到期时间',
  `times` int(11) NULL DEFAULT 0 COMMENT '第几次续卡,如果为0,则是续卡前的卡本身数据',
  `deleted` tinyint(4) NULL DEFAULT 1 COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_continued_card_id`) USING BTREE,
  UNIQUE INDEX `USER_CONTINUED_CARD_ID`(`user_continued_card_id`) USING BTREE,
  INDEX `CARD_START_TIME`(`card_start_time`) USING BTREE,
  INDEX `CARD_END_TIME`(`card_end_time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 37 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_user_continued_card
-- ----------------------------
INSERT INTO `website_user_continued_card` VALUES (34, 8, 13, 1, '201705080001', 56, 0.00, 48, '2017-05-08', '2017-11-08', 0, 1, '2017-05-08 16:43:18');
INSERT INTO `website_user_continued_card` VALUES (35, 8, 13, 1, '201705080001', 56, 2999.00, 96, '2017-05-08', '2018-05-08', 1, 1, '2017-05-08 16:43:18');
INSERT INTO `website_user_continued_card` VALUES (36, 8, 13, 1, '201705080001', 56, 4999.00, 196, '2017-05-08', '2019-05-08', 2, 1, '2017-05-08 16:44:51');

-- ----------------------------
-- Table structure for website_user_continued_card_log
-- ----------------------------
DROP TABLE IF EXISTS `website_user_continued_card_log`;
CREATE TABLE `website_user_continued_card_log`  (
  `user_continued_card_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_continued_card_id` int(11) NOT NULL,
  `user_card_id` int(11) NOT NULL,
  `setting_id` int(11) NOT NULL DEFAULT 0 COMMENT '会员卡种有效期配置ID',
  `add_remaining_times` int(11) NULL DEFAULT 0 COMMENT '剩余使用次数',
  `money` float(10, 2) NULL DEFAULT 0.00,
  `continued_card_start_time` date NULL DEFAULT NULL COMMENT '开始续卡的时间',
  `continued_card_end_time` date NULL DEFAULT NULL COMMENT '续卡后的时间',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_continued_card_log_id`) USING BTREE,
  UNIQUE INDEX `USER_CONTINUED_CARD_LOG_ID`(`user_continued_card_log_id`) USING BTREE,
  INDEX `USER_CONTINUED_CARD_ID`(`user_continued_card_id`) USING BTREE,
  INDEX `CONTINUED_CARD_START_TIME`(`continued_card_start_time`) USING BTREE,
  INDEX `CONTINUED_CARD_END_TIME`(`continued_card_end_time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_user_continued_card_log
-- ----------------------------
INSERT INTO `website_user_continued_card_log` VALUES (10, 35, 8, 52, 48, 2999.00, '2017-11-08', '2018-05-08', '2017-05-08 16:43:18');
INSERT INTO `website_user_continued_card_log` VALUES (11, 36, 8, 53, 100, 4999.00, '2018-05-08', '2019-05-08', '2017-05-08 16:44:51');

-- ----------------------------
-- Table structure for website_vender
-- ----------------------------
DROP TABLE IF EXISTS `website_vender`;
CREATE TABLE `website_vender`  (
  `vender_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `password` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `salt` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `group` tinyint(4) NULL DEFAULT 0 COMMENT '权限组 超级管理员还是普通管理员',
  `tel` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `last_login_time` datetime(0) NULL DEFAULT NULL,
  `deleted` tinyint(4) NULL DEFAULT 1 COMMENT '是否删除 1否 2是',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`vender_id`) USING BTREE,
  UNIQUE INDEX `VENDER_ID`(`vender_id`) USING BTREE,
  INDEX `DELETED`(`deleted`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_vender
-- ----------------------------
INSERT INTO `website_vender` VALUES (1, 'supervender', '0dd914c6d0cb8d41695c954a944c5df010a2243b', 'tj0LsNcQSW', 1, '13656480135', '403520515@qq.com', '2017-11-18 09:19:20', 1, '2017-04-28 16:01:32');
INSERT INTO `website_vender` VALUES (2, 'vender', '00c85fbfe266402064b3bbb62ada204caacf299e', 'e9mBslcs2w', 2, '18866830116', 'long83116@qq.com', '2017-05-24 16:18:08', 1, '2017-04-28 16:04:36');

-- ----------------------------
-- Table structure for website_website
-- ----------------------------
DROP TABLE IF EXISTS `website_website`;
CREATE TABLE `website_website`  (
  `website_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `url` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `meta_title` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `meta_description` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `meta_keyword` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `website_logo` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `website_icon` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `status` tinyint(4) NULL DEFAULT 0 COMMENT '是否启用设置信息,此选项在配置页面配置',
  `deleted` tinyint(4) NULL DEFAULT 1 COMMENT '是否删除 1否 2是',
  `update_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0),
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`website_id`) USING BTREE,
  UNIQUE INDEX `WEBSITE_ID`(`website_id`) USING BTREE,
  INDEX `DELETED`(`deleted`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_website
-- ----------------------------
INSERT INTO `website_website` VALUES (2, '文章社区', 'local.bb.com', '文章社区', '文章社区', '文章社区', 'catalog/tuzi.jpg', '', 1, 1, '2017-06-06 14:58:11', '2017-04-24 16:11:49');

SET FOREIGN_KEY_CHECKS = 1;
