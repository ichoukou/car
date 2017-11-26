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

 Date: 26/11/2017 22:29:13
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
INSERT INTO `website_admin` VALUES (1, 'superadmin', 'db1c91a1dec1865c9c3e519c1de442afaa4fa778', 'vQ5N4kMrlS', 1, '13656480135', '403520515@qq.com', '2017-11-19 15:38:55', 1, '2017-04-28 16:01:32');
INSERT INTO `website_admin` VALUES (2, 'admin', 'c5937201355066b46610a18838425036e4f2ef52', 'gGSBezIQAI', 2, '18866830116', 'long83116@qq.com', '2017-05-24 16:18:08', 1, '2017-04-28 16:04:36');

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
  `date_time` date NULL DEFAULT NULL COMMENT '成立日期',
  `operating_period` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '营业期限',
  `image_path` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '封面图地址',
  `score` int(11) NULL DEFAULT 0 COMMENT '评分',
  `score_count` int(11) NULL DEFAULT 0 COMMENT '评分次数',
  `views` int(11) NULL DEFAULT 0 COMMENT '浏览量',
  `group` tinyint(4) NULL DEFAULT 0,
  `status` tinyint(4) NULL DEFAULT 1 COMMENT '1审核通过 2待审核 3审核不通过',
  `password` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `salt` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `last_login_time` datetime(0) NULL DEFAULT NULL,
  `deleted` tinyint(4) NULL DEFAULT 1 COMMENT '是否删除 1否 2是',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`company_id`) USING BTREE,
  UNIQUE INDEX `COMPANY_ID`(`company_id`) USING BTREE,
  INDEX `DELETED`(`deleted`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_company
-- ----------------------------
INSERT INTO `website_company` VALUES (10, 0, '13656480135', 'CPY0000001', '青岛测试公司', '私企', '青岛市', '张三', '100万', '2017-09-25', '无限', NULL, 4, 1, 40, 0, 1, '48df5e738a4994df4191f2bd07c813517dfa49ea', 'A0UXS93kWh', '2017-11-26 20:41:39', 1, '2017-11-25 18:26:12');
INSERT INTO `website_company` VALUES (11, 0, '13333333333', 'CPY0000002', '车辆维修中心', '民企', '济南', '李斯', '500万', '2017-05-25', '无限', NULL, 0, 0, 6, 0, 1, 'e2f7c6ff922c63c24e25e79355442f7b6ee0dec4', 'OUrUgg9fOF', NULL, 1, '2017-11-25 18:32:52');
INSERT INTO `website_company` VALUES (12, 0, '13361076388', 'CPY0000003', '女童', '及逻辑', '裤头那具体', '看看', '666', '2017-11-25', '长期', NULL, 0, 0, 5, 0, 1, '5e3784e8bac30813d4638edc3e3e1b42946ddb4f', 'TnMC5kIlGc', '2017-11-25 21:26:51', 1, '2017-11-25 20:19:44');

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
INSERT INTO `website_config` VALUES (5, '其他', 'page', '网站后台每页显示数量', 'paging_limit', '100000', 0);
INSERT INTO `website_config` VALUES (6, '其他', 'page', '分页规则', 'paging_rules', '显示 %d 到 %d / %d (总 %d 页)', 0);

-- ----------------------------
-- Table structure for website_evaluation
-- ----------------------------
DROP TABLE IF EXISTS `website_evaluation`;
CREATE TABLE `website_evaluation`  (
  `evaluation_id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_id` int(11) NOT NULL COMMENT '订单标识',
  `company_id` int(11) NOT NULL COMMENT '被评价商户',
  `user_id` int(11) NOT NULL COMMENT '评价账户',
  `car_id` int(11) NOT NULL,
  `bill` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `score` int(11) NULL DEFAULT 0,
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`evaluation_id`) USING BTREE,
  UNIQUE INDEX `EAVLUATION_ID`(`evaluation_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_evaluation
-- ----------------------------
INSERT INTO `website_evaluation` VALUES (8, 17, 10, 9, 7, '20171125191057GB250825786293039', 4, '2017-11-25 19:16:00');

-- ----------------------------
-- Table structure for website_maintenance_costs
-- ----------------------------
DROP TABLE IF EXISTS `website_maintenance_costs`;
CREATE TABLE `website_maintenance_costs`  (
  `costs_id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_id` int(11) NOT NULL,
  `image_path` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '结算单据图片地址',
  `total_revenue` float(10, 2) NULL DEFAULT 0.00 COMMENT '实收合计',
  `deleted` tinyint(4) NULL DEFAULT 1 COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`costs_id`) USING BTREE,
  UNIQUE INDEX `COSTS_ID`(`costs_id`) USING BTREE,
  INDEX `RESERVATION_ID`(`reservation_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_maintenance_costs
-- ----------------------------
INSERT INTO `website_maintenance_costs` VALUES (7, 17, NULL, 0.10, 1, '2017-11-25 19:15:20');
INSERT INTO `website_maintenance_costs` VALUES (8, 18, NULL, 22.00, 1, '2017-11-25 19:17:09');
INSERT INTO `website_maintenance_costs` VALUES (9, 20, NULL, 5000.00, 1, '2017-11-25 20:22:49');
INSERT INTO `website_maintenance_costs` VALUES (10, 21, NULL, 11.00, 1, '2017-11-25 22:24:46');
INSERT INTO `website_maintenance_costs` VALUES (12, 22, 'Image\\upload\\vender\\other\\20171126173843GB26891231535516814171.jpg', 0.10, 1, '2017-11-26 17:38:43');
INSERT INTO `website_maintenance_costs` VALUES (13, 24, 'Image\\upload\\vender\\other\\20171126205806GB26010868569954705972.jpg', 0.10, 1, '2017-11-26 20:58:06');

-- ----------------------------
-- Table structure for website_maintenance_costs_-back
-- ----------------------------
DROP TABLE IF EXISTS `website_maintenance_costs_-back`;
CREATE TABLE `website_maintenance_costs_-back`  (
  `costs_id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_id` int(11) NOT NULL,
  `material_costs` float(10, 2) NULL DEFAULT 0.00 COMMENT '材料费合计',
  `working_hours_costs` float(10, 2) NULL DEFAULT 0.00 COMMENT '工时费合计',
  `outside_costs` float(10, 2) NULL DEFAULT 0.00 COMMENT '外加工费',
  `other_costs` float(10, 2) NULL DEFAULT 0.00 COMMENT '其他费用',
  `total_receivables` float(10, 2) NULL DEFAULT 0.00 COMMENT '应收合计',
  `discount_costs` float(10, 2) NULL DEFAULT 0.00 COMMENT '优惠金额',
  `total_revenue` float(10, 2) NULL DEFAULT 0.00 COMMENT '实收合计',
  `deleted` tinyint(4) NULL DEFAULT 1 COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`costs_id`) USING BTREE,
  UNIQUE INDEX `COSTS_ID`(`costs_id`) USING BTREE,
  INDEX `RESERVATION_ID`(`reservation_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_maintenance_costs_-back
-- ----------------------------
INSERT INTO `website_maintenance_costs_-back` VALUES (7, 17, 0.10, 0.00, 0.00, 0.00, 0.10, 0.00, 0.10, 1, '2017-11-25 19:15:20');
INSERT INTO `website_maintenance_costs_-back` VALUES (8, 18, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2017-11-25 19:17:09');
INSERT INTO `website_maintenance_costs_-back` VALUES (9, 20, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 5000.00, 1, '2017-11-25 20:22:49');
INSERT INTO `website_maintenance_costs_-back` VALUES (10, 21, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, '2017-11-25 22:24:46');

-- ----------------------------
-- Table structure for website_pay_log
-- ----------------------------
DROP TABLE IF EXISTS `website_pay_log`;
CREATE TABLE `website_pay_log`  (
  `pay_id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_id` int(11) NOT NULL,
  `bill` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '订单号',
  `total_amount` float(10, 2) NULL DEFAULT 0.00 COMMENT '本次交易支付的订单金额，单位为人民币（元）',
  `notify_type` tinyint(4) NULL DEFAULT 0 COMMENT '通知类型 1、同步通知  2、异步通知',
  `notify_id` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '通知校验ID',
  `notify_message` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '返回消息',
  `notify_time` datetime(0) NULL DEFAULT NULL COMMENT '通知时间',
  `app_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '支付宝分配给开发者的应用Id',
  `receipt_amount` float(10, 2) NULL DEFAULT 0.00 COMMENT '商家在交易中实际收到的款项，单位为元',
  `trade_no` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '支付宝交易凭证号',
  `out_biz_no` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '商户业务ID，主要是退款通知中返回退款申请的流水号',
  `buyer_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '买家支付宝账号对应的支付宝唯一用户号。以2088开头的纯16位数字',
  `buyer_logon_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '买家支付宝账号',
  `seller_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '收款方支付宝用户号',
  `seller_email` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '收款方支付宝账号',
  `trade_status` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '交易目前所处的状态',
  `gmt_create` datetime(0) NULL DEFAULT NULL COMMENT '该笔交易创建的时间。格式为yyyy-MM-dd HH:mm:ss',
  `gmt_payment` datetime(0) NULL DEFAULT NULL COMMENT '该笔交易的买家付款时间。格式为yyyy-MM-dd HH:mm:ss',
  `gmt_refund` datetime(0) NULL DEFAULT NULL COMMENT '该笔交易的退款时间。格式为yyyy-MM-dd HH:mm:ss.S',
  `gmt_close` datetime(0) NULL DEFAULT NULL COMMENT '该笔交易结束时间。格式为yyyy-MM-dd HH:mm:ss',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pay_id`) USING BTREE,
  UNIQUE INDEX `PAY_ID`(`pay_id`) USING BTREE,
  INDEX `RESERVATION_ID`(`reservation_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_pay_log
-- ----------------------------
INSERT INTO `website_pay_log` VALUES (9, 17, '20171125191057GB250825786293039', 0.10, 1, NULL, '交易成功', '2017-11-25 19:15:44', '2017111800028220', 0.00, '2088902349621211', NULL, NULL, NULL, '2017112521001004930592403960', NULL, NULL, NULL, NULL, NULL, NULL, '2017-11-25 19:15:44');

-- ----------------------------
-- Table structure for website_reservation
-- ----------------------------
DROP TABLE IF EXISTS `website_reservation`;
CREATE TABLE `website_reservation`  (
  `reservation_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL COMMENT '被预约企业标识',
  `user_id` int(11) NOT NULL DEFAULT 0 COMMENT '预约人标识',
  `car_id` int(11) NOT NULL COMMENT '预约人车辆标识',
  `bill` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '订单号',
  `image_path` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '接车问诊单图片地址',
  `reservation_time` datetime(0) NULL DEFAULT NULL COMMENT '预约时间',
  `description` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '描述',
  `status` tinyint(4) NULL DEFAULT 1 COMMENT '1用户预约 2企业接待 3企业结算 4用户支付 5用户评价 6企业或用户取消当前预约',
  `deleted` tinyint(4) NULL DEFAULT 1 COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`reservation_id`) USING BTREE,
  UNIQUE INDEX `RESERVATION_ID`(`reservation_id`) USING BTREE,
  INDEX `COMPANY_ID`(`company_id`) USING BTREE,
  INDEX `USER_ID`(`user_id`) USING BTREE,
  INDEX `CAR_ID`(`car_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 26 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_reservation
-- ----------------------------
INSERT INTO `website_reservation` VALUES (17, 10, 9, 7, '20171125191057GB250825786293039', 'http://www.baidu.com/', '2017-12-24 18:10:00', NULL, 5, 1, '2017-11-25 19:10:57');
INSERT INTO `website_reservation` VALUES (18, 10, 9, 7, '20171125191637GB250859769184409', 'http://www.baidu.com/', '2017-11-27 19:16:00', NULL, 3, 1, '2017-11-25 19:16:37');
INSERT INTO `website_reservation` VALUES (19, 11, 11, 9, '20171125194234GB2510154666388911', NULL, '2017-11-25 19:42:00', NULL, 1, 1, '2017-11-25 19:42:34');
INSERT INTO `website_reservation` VALUES (20, 12, 11, 9, '20171125202041GB2512441450764911', 'http://www.baidu.com/', '2017-11-25 20:20:00', NULL, 3, 1, '2017-11-25 20:20:41');
INSERT INTO `website_reservation` VALUES (21, 12, 11, 9, '20171125214058GB2517258489191811', 'http://www.baidu.com/', '2017-11-26 21:40:00', NULL, 3, 1, '2017-11-25 21:40:58');
INSERT INTO `website_reservation` VALUES (22, 10, 9, 7, '20171125232109GB252326964287589', 'http://www.baidu.com/', '2017-11-25 23:21:00', '1111222', 3, 1, '2017-11-25 23:21:09');
INSERT INTO `website_reservation` VALUES (23, 10, 9, 7, '20171125232947GB252378707190129', NULL, '2017-11-25 23:29:00', '啊啊啊啊啊啊啊啊啊啊啊', 2, 1, '2017-11-25 23:29:47');
INSERT INTO `website_reservation` VALUES (24, 10, 9, 7, '20171126150055GB267965577917609', 'Image\\upload\\vender\\other\\20171126174406GB26894468406060795280.jpg', '2017-11-26 15:00:00', 'aaaa', 3, 1, '2017-11-26 15:00:55');
INSERT INTO `website_reservation` VALUES (25, 10, 9, 7, '20171126174650GB268961035722499', NULL, '2017-11-26 17:46:00', 'rrr', 1, 1, '2017-11-26 17:46:50');

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
-- Table structure for website_sms
-- ----------------------------
DROP TABLE IF EXISTS `website_sms`;
CREATE TABLE `website_sms`  (
  `sms_id` int(11) NOT NULL AUTO_INCREMENT,
  `tel` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0',
  `rand_number` int(11) NULL DEFAULT 0 COMMENT '验证码',
  `sms_type` tinyint(4) NULL DEFAULT 0 COMMENT '1：注册  2：找回密码',
  `obj_type` tinyint(4) NULL DEFAULT 0 COMMENT '1：企业 2：用户',
  `send_time` int(11) NULL DEFAULT 0,
  `return_time` int(11) NULL DEFAULT 0,
  `return_type` tinyint(4) NULL DEFAULT 0 COMMENT '1：成功  2：失败',
  `create_time` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sms_id`) USING BTREE,
  UNIQUE INDEX `SMS_ID`(`sms_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 33 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_sms
-- ----------------------------
INSERT INTO `website_sms` VALUES (17, '18866830116', 515796, 1, 2, 1511606350, 1511606350, 1, '2017-11-25 18:39:10');
INSERT INTO `website_sms` VALUES (19, '18866830116', 153172, 1, 2, 1511607034, 1511607034, 1, '2017-11-25 18:50:34');
INSERT INTO `website_sms` VALUES (21, '13333333332', 626481, 1, 2, 1511607542, 1511607542, 1, '2017-11-25 18:59:02');
INSERT INTO `website_sms` VALUES (22, '18866830116', 534227, 1, 2, 1511607932, 1511607932, 1, '2017-11-25 19:05:32');
INSERT INTO `website_sms` VALUES (23, '18866830116', 585080, 1, 1, 1511608915, 1511608915, 1, '2017-11-25 19:21:55');
INSERT INTO `website_sms` VALUES (24, '18866830116', 821017, 1, 2, 1511608979, 1511608979, 1, '2017-11-25 19:22:59');
INSERT INTO `website_sms` VALUES (26, '13444444444', 325767, 1, 1, 1511610434, 1511610434, 1, '2017-11-25 19:47:14');
INSERT INTO `website_sms` VALUES (27, '18615596228', 358489, 1, 2, 1511611108, 1511611108, 1, '2017-11-25 19:58:28');
INSERT INTO `website_sms` VALUES (28, '18615596228', 461742, 1, 2, 1511611399, 1511611399, 1, '2017-11-25 20:03:19');
INSERT INTO `website_sms` VALUES (30, '18866830116', 127416, 1, 1, 1511619763, 1511619763, 1, '2017-11-25 22:22:43');
INSERT INTO `website_sms` VALUES (31, '13566666666', 908594, 1, 2, 1511690304, 1511690304, 1, '2017-11-26 17:58:24');
INSERT INTO `website_sms` VALUES (32, '13655555554', 189657, 1, 1, 1511690640, 1511690640, 1, '2017-11-26 18:04:00');

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
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_user
-- ----------------------------
INSERT INTO `website_user` VALUES (9, '13656480135', 'USER0000001', '652db5916678036fab1b22baf3ea0e6241b1dea9', 'xYl2MEb3Su', 1, '2017-11-26 21:09:05', '2017-11-25 18:49:51');
INSERT INTO `website_user` VALUES (11, '13361076388', 'USER0000002', '52dd19f468de055682e8dabd1ededad726b7f749', 'Ed2SyhVNfo', 1, '2017-11-25 21:33:21', '2017-11-25 19:41:31');

-- ----------------------------
-- Table structure for website_user_car
-- ----------------------------
DROP TABLE IF EXISTS `website_user_car`;
CREATE TABLE `website_user_car`  (
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
  `registration_date` date NULL DEFAULT NULL COMMENT '注册日期',
  `accepted_date` date NULL DEFAULT NULL COMMENT '受理日期',
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
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of website_user_car
-- ----------------------------
INSERT INTO `website_user_car` VALUES (7, 9, 'BMW001', 'BMW', '啊黑', '山东省', '民用', 'BMW', 'xxxxxxxx', 'xxxxxxxx', '2017-11-25', '2017-12-25', '001', '5', '1吨', '600x500x500', '啊啊啊啊啊啊', 1, '2017-11-25 18:49:51');
INSERT INTO `website_user_car` VALUES (9, 11, 'vvvh', 'yh', 'ffg', 'dzchh', '人', 'ffh', '看看', '6896525544', '2017-11-25', '2017-11-25', '6886', '66', '55563', '5555', '他现在', 1, '2017-11-25 19:41:31');

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
