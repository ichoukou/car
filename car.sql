-- Adminer 4.2.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

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
  PRIMARY KEY (`admin_id`) USING BTREE,
  UNIQUE KEY `ADMIN_ID` (`admin_id`) USING BTREE,
  KEY `DELETED` (`deleted`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

INSERT INTO `website_admin` (`admin_id`, `username`, `password`, `salt`, `group`, `tel`, `email`, `last_login_time`, `deleted`, `create_time`) VALUES
(1,	'superadmin',	'db1c91a1dec1865c9c3e519c1de442afaa4fa778',	'vQ5N4kMrlS',	1,	'13656480135',	'403520515@qq.com',	'2017-11-19 15:38:55',	1,	'2017-04-28 08:01:32'),
(2,	'admin',	'c5937201355066b46610a18838425036e4f2ef52',	'gGSBezIQAI',	2,	'18866830116',	'long83116@qq.com',	'2017-05-24 16:18:08',	1,	'2017-04-28 08:04:36');

DROP TABLE IF EXISTS `website_company`;
CREATE TABLE `website_company` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT '0' COMMENT '父类商户ID，为0是代表是主商户',
  `tel` varchar(20) DEFAULT NULL COMMENT '手机号',
  `numbering` varchar(30) DEFAULT NULL COMMENT '企业会员编号',
  `name` varchar(50) DEFAULT NULL COMMENT '名称',
  `type` varchar(50) DEFAULT NULL COMMENT '类型',
  `address` varchar(50) DEFAULT NULL COMMENT '住所',
  `legal_person` varchar(10) DEFAULT NULL COMMENT '法人',
  `registered_capital` varchar(20) DEFAULT NULL COMMENT '注册资本',
  `date_time` date DEFAULT NULL COMMENT '成立日期',
  `operating_period` varchar(10) DEFAULT NULL COMMENT '营业期限',
  `image_path` varchar(150) DEFAULT NULL COMMENT '封面图地址',
  `score` int(11) DEFAULT '0' COMMENT '评分',
  `score_count` int(11) DEFAULT '0' COMMENT '评分次数',
  `views` int(11) DEFAULT '0' COMMENT '浏览量',
  `group` tinyint(4) DEFAULT '0',
  `status` tinyint(4) DEFAULT '1' COMMENT '1审核通过 2待审核 3审核不通过',
  `password` varchar(150) DEFAULT NULL,
  `salt` varchar(20) DEFAULT NULL,
  `last_login_time` datetime DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT '1' COMMENT '是否删除 1否 2是',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`company_id`) USING BTREE,
  UNIQUE KEY `COMPANY_ID` (`company_id`) USING BTREE,
  KEY `DELETED` (`deleted`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

INSERT INTO `website_company` (`company_id`, `pid`, `tel`, `numbering`, `name`, `type`, `address`, `legal_person`, `registered_capital`, `date_time`, `operating_period`, `image_path`, `score`, `score_count`, `views`, `group`, `status`, `password`, `salt`, `last_login_time`, `deleted`, `create_time`) VALUES
(10,	0,	'13656480135',	'CPY0000001',	'青岛测试公司',	'私企',	'青岛市',	'张三',	'100万',	'2017-09-25',	'无限',	NULL,	7,	2,	13,	0,	1,	'44d98cf3d486009c070cba02564d00a6c74355f7',	'aUTPcYpTZs',	'2017-11-30 23:12:15',	1,	'2017-11-25 10:26:12'),
(11,	0,	'13333333333',	'CPY0000002',	'车辆维修中心',	'民企',	'济南',	'李斯',	'500万',	'2017-05-25',	'无限',	NULL,	0,	0,	6,	0,	1,	'e2f7c6ff922c63c24e25e79355442f7b6ee0dec4',	'OUrUgg9fOF',	NULL,	1,	'2017-11-25 10:32:52'),
(12,	0,	'13361076388',	'CPY0000003',	'老王汽修',	'三类',	'山东省济南市天桥区北园大街888号',	'王林',	'666',	'2017-11-25',	'长期',	NULL,	0,	0,	29,	0,	1,	'd8eae8690d5fbe94fd5412ed960e343e1034ac9d',	'GxIlTv4Nks',	'2017-12-01 23:29:25',	1,	'2017-11-25 12:19:44'),
(14,	0,	'18866830116',	'CPY0000004',	'山东派乐特网络科技有限公司',	'自然人',	'山东省济南市槐荫区经七路69号新世界附光花',	'无',	'1000',	'2015-09-28',	'无',	NULL,	5,	1,	24,	0,	1,	'9bbc5d5ea833188c71c408d85a3ac8f3deff08fb',	'6FhTW6DPjv',	'2017-11-30 14:55:15',	1,	'2017-11-27 06:45:20');

DROP TABLE IF EXISTS `website_company_score`;
CREATE TABLE `website_company_score` (
  `score_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '打分用户ID',
  `score` int(11) DEFAULT '0' COMMENT '分数',
  `description` varchar(100) DEFAULT NULL COMMENT '描述',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`score_id`) USING BTREE,
  UNIQUE KEY `SCORE_ID` (`score_id`) USING BTREE,
  KEY `COMPANY_ID` (`company_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;


DROP TABLE IF EXISTS `website_config`;
CREATE TABLE `website_config` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `column` varchar(20) DEFAULT NULL COMMENT '所属栏目',
  `module` varchar(20) DEFAULT NULL COMMENT '所属模块',
  `title` varchar(20) DEFAULT NULL COMMENT '标题',
  `key` varchar(50) DEFAULT NULL COMMENT '标识',
  `value` varchar(50) DEFAULT NULL COMMENT '内容',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`config_id`) USING BTREE,
  UNIQUE KEY `CONFIG_CATEGORY_ID` (`config_id`) USING BTREE,
  KEY `MODULE` (`module`) USING BTREE,
  KEY `category` (`column`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

INSERT INTO `website_config` (`config_id`, `column`, `module`, `title`, `key`, `value`, `sort`) VALUES
(5,	'其他',	'page',	'网站后台每页显示数量',	'paging_limit',	'100000',	0),
(6,	'其他',	'page',	'分页规则',	'paging_rules',	'显示 %d 到 %d / %d (总 %d 页)',	0);

DROP TABLE IF EXISTS `website_evaluation`;
CREATE TABLE `website_evaluation` (
  `evaluation_id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_id` int(11) NOT NULL COMMENT '订单标识',
  `company_id` int(11) NOT NULL COMMENT '被评价商户',
  `user_id` int(11) NOT NULL COMMENT '评价账户',
  `car_id` int(11) NOT NULL,
  `bill` varchar(100) NOT NULL,
  `score` int(11) DEFAULT '0',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`evaluation_id`) USING BTREE,
  UNIQUE KEY `EAVLUATION_ID` (`evaluation_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

INSERT INTO `website_evaluation` (`evaluation_id`, `reservation_id`, `company_id`, `user_id`, `car_id`, `bill`, `score`, `create_time`) VALUES
(8,	17,	10,	9,	7,	'20171125191057GB250825786293039',	4,	'2017-11-25 11:16:00'),
(9,	22,	13,	12,	10,	'20171126001048GB2626248852140012',	5,	'2017-11-25 16:33:37'),
(10,	28,	14,	14,	12,	'20171127145358GB2765638634260714',	5,	'2017-11-27 07:03:27'),
(11,	26,	10,	9,	7,	'20171127140323GB2762603176989164',	3,	'2017-12-01 15:49:33');

DROP TABLE IF EXISTS `website_maintenance_costs`;
CREATE TABLE `website_maintenance_costs` (
  `costs_id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_id` int(11) NOT NULL,
  `image_path` varchar(150) NOT NULL COMMENT '结算单据图片地址',
  `total_revenue` float(10,2) DEFAULT '0.00' COMMENT '实收合计',
  `deleted` tinyint(4) DEFAULT '1' COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`costs_id`) USING BTREE,
  UNIQUE KEY `COSTS_ID` (`costs_id`) USING BTREE,
  KEY `RESERVATION_ID` (`reservation_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

INSERT INTO `website_maintenance_costs` (`costs_id`, `reservation_id`, `image_path`, `total_revenue`, `deleted`, `create_time`) VALUES
(17,	26,	'Image/upload/vender/other/20171128153824GB28547049066910995727.jpg',	0.01,	1,	'2017-11-28 07:38:24'),
(18,	35,	'Image/upload/vender/other/20171130072615GB30979758443024973827.jpg',	0.01,	1,	'2017-11-29 23:26:15');

DROP TABLE IF EXISTS `website_pay_log`;
CREATE TABLE `website_pay_log` (
  `pay_id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_id` int(11) NOT NULL,
  `pay_type` varchar(10) DEFAULT NULL COMMENT '支付方式',
  `bill` varchar(100) DEFAULT NULL COMMENT '订单号',
  `total_amount` float(10,2) DEFAULT '0.00' COMMENT '本次交易支付的订单金额，单位为人民币（元）',
  `trade_status` varchar(50) DEFAULT NULL COMMENT '交易目前所处的状态',
  `message` varchar(50) DEFAULT NULL COMMENT '人为判断消息',
  `notify_type` tinyint(4) DEFAULT '0' COMMENT '通知类型 1、同步通知  2、异步通知',
  `notify_id` varchar(128) DEFAULT NULL COMMENT '通知校验ID',
  `notify_message` varchar(50) DEFAULT NULL COMMENT '返回消息',
  `notify_time` datetime DEFAULT NULL COMMENT '通知时间',
  `app_id` varchar(50) DEFAULT NULL COMMENT '支付宝分配给开发者的应用Id',
  `receipt_amount` float(10,2) DEFAULT '0.00' COMMENT '商家在交易中实际收到的款项，单位为元',
  `trade_no` varchar(30) DEFAULT NULL COMMENT '支付宝交易凭证号',
  `buyer_id` varchar(50) DEFAULT NULL COMMENT '买家支付宝账号对应的支付宝唯一用户号。以2088开头的纯16位数字',
  `buyer_logon_id` varchar(50) DEFAULT NULL COMMENT '买家支付宝账号',
  `seller_id` varchar(50) DEFAULT NULL COMMENT '收款方支付宝用户号',
  `seller_email` varchar(50) DEFAULT NULL COMMENT '收款方支付宝账号',
  `gmt_create` datetime DEFAULT NULL COMMENT '该笔交易创建的时间。格式为yyyy-MM-dd HH:mm:ss',
  `gmt_payment` datetime DEFAULT NULL COMMENT '该笔交易的买家付款时间。格式为yyyy-MM-dd HH:mm:ss',
  `gmt_refund` datetime DEFAULT NULL COMMENT '该笔交易的退款时间。格式为yyyy-MM-dd HH:mm:ss.S',
  `gmt_close` datetime DEFAULT NULL COMMENT '该笔交易结束时间。格式为yyyy-MM-dd HH:mm:ss',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pay_id`) USING BTREE,
  UNIQUE KEY `PAY_ID` (`pay_id`) USING BTREE,
  KEY `RESERVATION_ID` (`reservation_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;


DROP TABLE IF EXISTS `website_reservation`;
CREATE TABLE `website_reservation` (
  `reservation_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL COMMENT '被预约企业标识',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '预约人标识',
  `car_id` int(11) NOT NULL COMMENT '预约人车辆标识',
  `pay_type` varchar(10) DEFAULT '0' COMMENT '支付类型',
  `bill` varchar(100) DEFAULT '0' COMMENT '订单号',
  `image_path` varchar(150) DEFAULT NULL COMMENT '接车问诊单图片地址',
  `reservation_time` datetime DEFAULT NULL COMMENT '预约时间',
  `description` varchar(200) DEFAULT NULL COMMENT '描述',
  `status` tinyint(4) DEFAULT '1' COMMENT '1用户预约 2企业接待 3企业结算 4用户支付 5用户评价 6企业或用户取消当前预约',
  `deleted` tinyint(4) DEFAULT '1' COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`reservation_id`) USING BTREE,
  UNIQUE KEY `RESERVATION_ID` (`reservation_id`) USING BTREE,
  KEY `COMPANY_ID` (`company_id`) USING BTREE,
  KEY `USER_ID` (`user_id`) USING BTREE,
  KEY `CAR_ID` (`car_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

INSERT INTO `website_reservation` (`reservation_id`, `company_id`, `user_id`, `car_id`, `pay_type`, `bill`, `image_path`, `reservation_time`, `description`, `status`, `deleted`, `create_time`) VALUES
(17,	10,	9,	7,	'0',	'20171125191057GB250825786293039',	'',	'2017-12-24 18:10:00',	NULL,	1,	1,	'2017-11-25 11:10:57'),
(18,	10,	9,	7,	'0',	'20171125191637GB250859769184410',	'',	'2017-11-27 19:16:00',	NULL,	1,	1,	'2017-11-25 11:16:37'),
(26,	10,	9,	7,	'',	'20171127140323GB2762603176989165',	'Image/upload/vender/other/20171128153803GB28546837317879127079.jpg',	'2017-11-27 14:03:00',	'的多',	3,	1,	'2017-11-27 06:03:23'),
(33,	12,	11,	9,	'0',	'20171129064846GB2909326289130911',	'Image/upload/vender/other/20171129065027GB29094273975368658742.jpg',	'2017-11-29 11:48:00',	'换机油',	2,	1,	'2017-11-28 22:48:46'),
(34,	14,	11,	9,	'0',	'20171129152239GB2940159359028411',	NULL,	'2017-11-29 15:24:00',	'保弟保养',	1,	1,	'2017-11-29 07:22:39'),
(35,	12,	11,	9,	'0',	'20171129173230GB2947950566764011',	'Image/upload/vender/other/20171129173354GB29480342516316728324.jpg',	'2017-11-29 02:00:00',	'充气',	3,	1,	'2017-11-29 09:32:30'),
(36,	14,	11,	9,	'0',	'20171201002632GC0159192706974911',	NULL,	'2017-12-01 00:27:00',	'换机油',	1,	1,	'2017-11-30 16:26:32'),
(37,	12,	11,	9,	'0',	'20171201002852GC0159332297615211',	NULL,	'2017-12-01 00:29:00',	'保养',	1,	1,	'2017-11-30 16:28:52');

DROP TABLE IF EXISTS `website_session_info`;
CREATE TABLE `website_session_info` (
  `session_id` varchar(200) NOT NULL DEFAULT '0',
  `uid` bigint(20) DEFAULT '0' COMMENT '用户ID,包括前后台',
  `data` text,
  `user_type` varchar(20) DEFAULT NULL COMMENT '访问网站的用户类型',
  `expiration_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`session_id`) USING BTREE,
  UNIQUE KEY `SESSION_ID` (`session_id`) USING BTREE,
  KEY `UID` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

INSERT INTO `website_session_info` (`session_id`, `uid`, `data`, `user_type`, `expiration_time`) VALUES
('39fec53508996c8a8444d3bbde',	1,	'admin_id|s:1:\"1\";admin_name|s:10:\"superadmin\";group|s:1:\"1\";',	'admin',	'2017-11-14 09:32:50');

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
  PRIMARY KEY (`setting_id`) USING BTREE,
  UNIQUE KEY `SETTING_ID` (`setting_id`) USING BTREE,
  KEY `SETTING_CATEGORY_ID` (`setting_category_id`) USING BTREE,
  KEY `DELETED` (`deleted`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

INSERT INTO `website_setting` (`setting_id`, `setting_category_id`, `key`, `value`, `months`, `times`, `money`, `parent_setting_id`, `description`, `sort`, `deleted`, `create_time`) VALUES
(1,	1,	1,	'普通',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(2,	1,	2,	'VIP',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(3,	2,	1,	'启用',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(4,	2,	2,	'停用',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(5,	3,	1,	'启用',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(6,	3,	2,	'停用',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(7,	4,	1,	'男',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(8,	4,	2,	'女',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(14,	11,	1,	'男',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(15,	11,	2,	'女',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(16,	12,	1,	'鼠',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(17,	12,	2,	'牛',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(18,	12,	3,	'虎',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(19,	12,	4,	'兔',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(20,	12,	5,	'龙',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(21,	12,	6,	'蛇',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(22,	12,	7,	'马',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(23,	12,	8,	'羊',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(24,	12,	9,	'猴',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(25,	12,	10,	'鸡',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(26,	12,	11,	'狗',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(27,	12,	12,	'猪',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(28,	13,	1,	'白羊座',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(29,	13,	2,	'金牛座',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(30,	13,	3,	'双子座',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(31,	13,	4,	'巨蟹座',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(32,	13,	5,	'狮子座',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(33,	13,	6,	'处女座',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(34,	13,	7,	'天秤座',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(35,	13,	8,	'天蝎座',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(36,	13,	9,	'射手座',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(37,	13,	10,	'摩羯座',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(38,	13,	11,	'水瓶座',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(39,	13,	12,	'双鱼座',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(40,	14,	1,	'A',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(41,	14,	2,	'B',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(42,	14,	3,	'AB',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(43,	14,	4,	'O',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(44,	14,	5,	'其他',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(45,	15,	1,	'老人带孩子',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(46,	15,	2,	'父母带孩子',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(47,	15,	3,	'其他',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(48,	16,	1,	'E',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(49,	16,	2,	'T',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(50,	16,	3,	'D',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 01:58:09'),
(51,	16,	4,	'A',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-11 02:17:01'),
(52,	17,	1,	'半年',	6,	0,	0.00,	NULL,	'',	0,	1,	'2017-04-11 01:58:09'),
(53,	17,	2,	'一年',	12,	0,	0.00,	NULL,	'',	0,	1,	'2017-04-11 01:58:09'),
(54,	17,	3,	'二年',	24,	0,	0.00,	NULL,	'',	0,	1,	'2017-04-11 01:58:09'),
(56,	18,	1,	'2999元48次半年内有效',	0,	48,	2999.00,	52,	NULL,	0,	1,	'2017-04-11 02:58:40'),
(57,	19,	1,	'语言敏感期',	0,	1,	0.00,	NULL,	NULL,	0,	1,	'2017-04-12 03:34:59'),
(58,	19,	2,	'秩序敏感期',	0,	1,	0.00,	NULL,	NULL,	0,	1,	'2017-04-12 03:35:06'),
(59,	19,	3,	'感冒敏感期',	0,	1,	0.00,	NULL,	NULL,	0,	1,	'2017-04-12 03:35:19'),
(60,	19,	4,	'对细微事物感兴趣的敏感期',	0,	1,	0.00,	NULL,	NULL,	0,	1,	'2017-04-12 03:35:41'),
(61,	19,	5,	'动作敏感期',	0,	1,	0.00,	NULL,	NULL,	0,	1,	'2017-04-12 03:35:52'),
(62,	19,	6,	'社会规范敏感期',	0,	1,	0.00,	NULL,	NULL,	0,	1,	'2017-04-12 03:36:03'),
(63,	19,	7,	'书写敏感期',	0,	1,	0.00,	NULL,	NULL,	0,	1,	'2017-04-12 03:36:18'),
(64,	19,	8,	'阅读敏感期',	0,	1,	0.00,	NULL,	NULL,	0,	1,	'2017-04-12 03:36:26'),
(65,	19,	9,	'文化敏感期',	0,	3,	0.00,	NULL,	NULL,	0,	1,	'2017-04-12 03:36:41'),
(66,	18,	2,	'4999元100次一年内有效',	0,	100,	4999.00,	53,	NULL,	0,	1,	'2017-04-13 07:48:41'),
(67,	18,	3,	'9999元200次二年内有效',	0,	200,	9999.00,	54,	NULL,	0,	1,	'2017-04-13 07:49:06'),
(68,	20,	1,	'教具1',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-19 07:13:00'),
(69,	20,	2,	'教具2',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-19 07:13:08'),
(70,	20,	3,	'教具3',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-19 07:13:13'),
(71,	22,	1,	'启用',	0,	0,	0.00,	NULL,	'请不要删除此选项或者随意改Key值，此选项的Key影响这网站的信息选择，如果删除，网站信息将出现错误。',	0,	1,	'2017-04-24 07:37:05'),
(72,	22,	2,	'停用',	0,	0,	0.00,	NULL,	'请不要删除此选项或者随意改Key值，此选项的Key影响这网站的信息选择，如果删除，网站信息将出现错误。',	0,	1,	'2017-04-24 07:37:11'),
(73,	23,	1,	'收入',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-24 09:39:07'),
(74,	23,	2,	'支出',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-04-24 09:39:12'),
(75,	17,	4,	'三年',	36,	0,	0.00,	NULL,	'',	0,	1,	'2017-04-26 06:47:19'),
(76,	18,	4,	'14999元300次三年内有效',	0,	300,	14999.00,	75,	NULL,	0,	1,	'2017-04-26 07:37:05'),
(77,	18,	5,	'三年XXX卡种多少次',	0,	200,	9998.00,	75,	NULL,	0,	1,	'2017-04-26 08:28:40'),
(78,	24,	1,	'是',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-05-31 08:16:43'),
(79,	24,	2,	'否',	0,	0,	0.00,	NULL,	NULL,	1,	1,	'2017-05-31 08:16:48'),
(80,	25,	1,	'是',	0,	0,	0.00,	NULL,	NULL,	0,	1,	'2017-06-01 03:39:43'),
(81,	25,	2,	'否',	0,	0,	0.00,	NULL,	NULL,	1,	1,	'2017-06-01 03:39:48'),
(82,	28,	1,	'内容',	0,	0,	0.00,	NULL,	'key不要随意改，影响到内容显示，文章管理内的各个模块和前台的文章模块都会用到此key来判断归属',	0,	1,	'2017-06-02 08:57:48'),
(83,	28,	2,	'新闻',	0,	0,	0.00,	NULL,	'key不要随意改，影响到内容显示，文章管理内的各个模块和前台的文章模块都会用到此key来判断归属',	0,	1,	'2017-06-02 08:58:10'),
(84,	28,	3,	'广告',	0,	0,	0.00,	NULL,	'key不要随意改，影响到内容显示，文章管理内的各个模块和前台的文章模块都会用到此key来判断归属',	0,	1,	'2017-06-05 02:46:54'),
(85,	28,	4,	'临时公告',	0,	0,	0.00,	NULL,	'key不要随意改，影响到内容显示，文章管理内的各个模块和前台的文章模块都会用到此key来判断归属',	0,	1,	'2017-06-05 02:47:10');

DROP TABLE IF EXISTS `website_setting_category`;
CREATE TABLE `website_setting_category` (
  `setting_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(50) DEFAULT NULL COMMENT '所属模块',
  `key` varchar(50) DEFAULT NULL COMMENT '标识',
  `title` varchar(50) DEFAULT NULL COMMENT '标题',
  `description` varchar(100) DEFAULT NULL COMMENT '描述',
  `deleted` tinyint(4) DEFAULT '1' COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_category_id`) USING BTREE,
  UNIQUE KEY `SETTING_CATEGORY_ID` (`setting_category_id`) USING BTREE,
  KEY `DELETED` (`deleted`) USING BTREE,
  KEY `MODULE` (`module`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

INSERT INTO `website_setting_category` (`setting_category_id`, `module`, `key`, `title`, `description`, `deleted`, `create_time`) VALUES
(1,	'user',	'setting_user_group',	'会员等级',	NULL,	1,	'2017-04-11 02:04:14'),
(2,	'user',	'setting_user_newsletter',	'会员订阅邮件',	NULL,	1,	'2017-04-11 02:04:14'),
(3,	'user',	'setting_user_status',	'会员状态',	NULL,	1,	'2017-04-11 02:04:14'),
(4,	'user',	'setting_user_sex',	'会员性别',	NULL,	1,	'2017-04-11 02:04:14'),
(11,	'baby',	'setting_userbaby_sex',	'性别',	NULL,	1,	'2017-04-11 02:04:14'),
(12,	'baby',	'setting_userbaby_zodiac',	'属相',	NULL,	1,	'2017-04-11 02:04:14'),
(13,	'baby',	'setting_userbaby_constellation',	'星座',	NULL,	1,	'2017-04-11 02:04:14'),
(14,	'baby',	'setting_userbaby_blood',	'血型',	NULL,	1,	'2017-04-11 02:04:14'),
(15,	'baby',	'setting_userbaby_family_situation',	'家庭情况',	NULL,	1,	'2017-04-11 02:04:14'),
(16,	'baby',	'setting_userbaby_baby_character',	'性格',	NULL,	1,	'2017-04-11 02:04:14'),
(17,	'card_type',	'setting_user_card_type_valid_period',	'有效期',	NULL,	1,	'2017-04-11 02:04:14'),
(18,	'card_type',	'setting_user_card_type',	'卡种选择',	NULL,	1,	'2017-04-11 02:04:14'),
(19,	'baby_sensitive_period',	'setting_current_sensitive_period',	'敏感期',	NULL,	1,	'2017-04-12 03:33:54'),
(20,	'teaching_aids',	'setting_teaching_aids',	'教具',	NULL,	1,	'2017-04-19 07:10:03'),
(21,	'teaching_times',	'setting_teaching_times',	'上课时间设置',	NULL,	1,	'2017-04-19 08:58:22'),
(22,	'website',	'setting_website_status',	'状态',	NULL,	1,	'2017-04-24 07:36:45'),
(23,	'record',	'setting_income_and_expenditure',	'记录类型',	NULL,	1,	'2017-04-24 09:38:40'),
(24,	'article',	'setting_article_category_status',	'是否启用',	'文章栏目是否启用',	1,	'2017-05-31 08:15:50'),
(25,	'article',	'setting_article_status',	'是否启用',	'各类文章是否启用',	1,	'2017-06-01 03:38:03'),
(28,	'article',	'setting_article_type',	'文章模块',	'所有栏目可选的文章模块',	1,	'2017-06-02 08:15:21');

DROP TABLE IF EXISTS `website_sms`;
CREATE TABLE `website_sms` (
  `sms_id` int(11) NOT NULL AUTO_INCREMENT,
  `tel` varchar(20) DEFAULT '0',
  `rand_number` int(11) DEFAULT '0' COMMENT '验证码',
  `sms_type` tinyint(4) DEFAULT '0' COMMENT '1：注册  2：找回密码',
  `obj_type` tinyint(4) DEFAULT '0' COMMENT '1：企业 2：用户',
  `send_time` int(11) DEFAULT '0',
  `return_time` int(11) DEFAULT '0',
  `return_type` tinyint(4) DEFAULT '0' COMMENT '1：成功  2：失败',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sms_id`) USING BTREE,
  UNIQUE KEY `SMS_ID` (`sms_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

INSERT INTO `website_sms` (`sms_id`, `tel`, `rand_number`, `sms_type`, `obj_type`, `send_time`, `return_time`, `return_type`, `create_time`) VALUES
(38,	'13655552222',	184136,	1,	1,	1511691099,	1511691099,	1,	'2017-11-26 10:11:39'),
(39,	'13322222222',	294128,	1,	2,	1512891234,	1511691234,	1,	'2017-11-26 10:13:54'),
(42,	'13333333338',	783189,	1,	1,	1511797954,	1511797954,	1,	'2017-11-27 15:52:34'),
(43,	'17600103602',	144932,	1,	2,	1512003850,	1512003850,	1,	'2017-11-30 01:04:10');

DROP TABLE IF EXISTS `website_user`;
CREATE TABLE `website_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `tel` varchar(20) DEFAULT NULL COMMENT '手机号码',
  `numbering` varchar(30) DEFAULT NULL COMMENT '用户会员编号',
  `password` varchar(150) DEFAULT NULL,
  `salt` varchar(20) DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT '1' COMMENT '是否删除 1未删除 2已删除',
  `last_login_time` datetime DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`user_id`) USING BTREE,
  UNIQUE KEY `USER_ID` (`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

INSERT INTO `website_user` (`user_id`, `tel`, `numbering`, `password`, `salt`, `deleted`, `last_login_time`, `create_time`) VALUES
(9,	'13656480135',	'USER0000001',	'7bbc02ffe287faa492ddd4d14147a1a152e81ff1',	'tRCLMgLrU2',	1,	'2017-12-01 23:11:27',	'2017-11-25 10:49:51'),
(11,	'13361076388',	'USER0000002',	'52dd19f468de055682e8dabd1ededad726b7f749',	'Ed2SyhVNfo',	1,	'2017-12-01 23:26:00',	'2017-11-25 11:41:31'),
(13,	'18953138487',	'USER0000004',	'793d3d94f9fc5c516e47b7b8d3aa277a73173db2',	'Wg9cdlsBwg',	1,	'2017-11-27 14:25:04',	'2017-11-26 08:41:26'),
(15,	'18866830116',	'USER0000005',	'9d1332b6ab4845c4e1de0975850f5635158f4000',	'maIIWSM84a',	1,	'2017-11-27 23:51:34',	'2017-11-27 15:27:36');

DROP TABLE IF EXISTS `website_user_car`;
CREATE TABLE `website_user_car` (
  `car_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户标识',
  `plate_number` varchar(50) DEFAULT NULL COMMENT '号牌号码',
  `car_type` varchar(50) DEFAULT NULL COMMENT '车辆类型',
  `owner` varchar(50) DEFAULT NULL COMMENT '所有人',
  `address` varchar(50) DEFAULT NULL COMMENT '住址',
  `use_type` varchar(50) DEFAULT NULL COMMENT '使用性质',
  `brand_type` varchar(50) DEFAULT NULL COMMENT '品牌型号',
  `identification_number` varchar(50) DEFAULT NULL COMMENT '车辆识别代号',
  `engine_number` varchar(50) DEFAULT NULL COMMENT '发动机号码',
  `registration_date` date DEFAULT NULL COMMENT '注册日期',
  `accepted_date` date DEFAULT NULL COMMENT '受理日期',
  `file_number` varchar(50) DEFAULT NULL COMMENT '档案编号',
  `people_number` varchar(20) DEFAULT NULL COMMENT '核定人数',
  `total_mass` varchar(50) DEFAULT NULL COMMENT '总质量',
  `dimension` varchar(50) DEFAULT NULL COMMENT '外观尺寸',
  `description` text COMMENT '备注',
  `deleted` tinyint(4) DEFAULT '1' COMMENT '是否删除 1未删除 2已删除',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`car_id`) USING BTREE,
  UNIQUE KEY `CAR_ID` (`car_id`) USING BTREE,
  KEY `USER_ID` (`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

INSERT INTO `website_user_car` (`car_id`, `user_id`, `plate_number`, `car_type`, `owner`, `address`, `use_type`, `brand_type`, `identification_number`, `engine_number`, `registration_date`, `accepted_date`, `file_number`, `people_number`, `total_mass`, `dimension`, `description`, `deleted`, `create_time`) VALUES
(7,	9,	'BMW001',	'BMW',	'啊黑',	'山东省',	'民用',	'BMW',	'xxxxxxxx',	'xxxxxxxx',	'2017-11-25',	'2017-12-25',	'001',	'5',	'1吨',	'600x500x500',	'啊啊啊啊啊啊',	1,	'2017-11-25 10:49:51'),
(9,	11,	'鲁A66666',	'越野车',	'高先生',	'山东省济南市经四路888号',	'个人',	'北京现代',	'看看',	'6896525544',	'2017-11-25',	'2017-11-25',	'6886',	'66',	'55563',	'5555',	'他现在',	1,	'2017-11-25 11:41:31'),
(10,	12,	'苏B1585W',	'小型轿车',	'周寅峰',	'江苏省无锡市南长区荣机新村46号503室',	'非营运',	'别克牌SGM7165MTB',	'LSGJA52U6CH063211',	'C1260774',	'2017-11-26',	'2017-11-29',	'213123123123',	'12',	'5',	'854564',	'5456',	1,	'2017-11-25 16:07:08'),
(11,	13,	'豫AH507',	'中型旅居半挂车',	'郑州名门汽车销售有限公司',	'州印金水区三全路东段段一米阳光商务公寓一楼',	'非营运',	'杰克1U1BRJ0AJ',	'1U1B10A15D17E0099',	'无',	'2015-06-20',	'1920-11-09',	'558786',	'22',	'9966',	'5555',	'9868',	1,	'2017-11-26 08:41:26'),
(12,	14,	'苏A61J51',	'小型普通客车',	'钱磊',	'南京市白下区富丽山庄2幢502室',	'非营运',	'奥德赛牌HG6481BAA',	'LHGRB3861B8019596',	'1019724',	'2011-09-06',	'2011-09-06',	'hfhf',	'17',	'dnjdn',	'dnndnd',	'hdjnd',	1,	'2017-11-27 06:44:55'),
(13,	15,	'晋A2008P',	'小型普通客车',	'孙战萍',	'山西省太原市杏花岭区新建巷2号318',	'非营运',	'比亚迪牌BY',	'LC0CG4CG6F1022067',	'A15014079',	'2015-06-20',	'2017-11-28',	'',	'',	'',	'',	'',	1,	'2017-11-27 15:27:36');

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
  PRIMARY KEY (`website_id`) USING BTREE,
  UNIQUE KEY `WEBSITE_ID` (`website_id`) USING BTREE,
  KEY `DELETED` (`deleted`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

INSERT INTO `website_website` (`website_id`, `title`, `url`, `meta_title`, `meta_description`, `meta_keyword`, `website_logo`, `website_icon`, `status`, `deleted`, `update_time`, `create_time`) VALUES
(2,	'文章社区',	'local.bb.com',	'文章社区',	'文章社区',	'文章社区',	'catalog/tuzi.jpg',	'',	1,	1,	'2017-06-06 06:58:11',	'2017-04-24 08:11:49');

-- 2017-12-01 15:53:20
