/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50714
Source Host           : localhost:3306
Source Database       : swoole

Target Server Type    : MYSQL
Target Server Version : 50714
File Encoding         : 65001

Date: 2017-03-15 17:08:35
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for sw_config
-- ----------------------------
DROP TABLE IF EXISTS `sw_config`;
CREATE TABLE `sw_config` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL COMMENT '配置类型',
  `title` varchar(50) NOT NULL COMMENT '配置标题',
  `value` text NOT NULL COMMENT '配置数据',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ID` (`id`) USING BTREE,
  KEY `TYPE` (`type`) USING BTREE,
  KEY `TITLE` (`title`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sw_config
-- ----------------------------
INSERT INTO `sw_config` VALUES ('1', 'session', 'expiration_time', '1800');
INSERT INTO `sw_config` VALUES ('3', 'session', 'save_type', '2');

-- ----------------------------
-- Table structure for sw_info
-- ----------------------------
DROP TABLE IF EXISTS `sw_info`;
CREATE TABLE `sw_info` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `content` text,
  `status` tinyint(4) DEFAULT '1',
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sw_info
-- ----------------------------

-- ----------------------------
-- Table structure for sw_session_info
-- ----------------------------
DROP TABLE IF EXISTS `sw_session_info`;
CREATE TABLE `sw_session_info` (
  `session_id` varchar(255) NOT NULL,
  `uid` bigint(20) DEFAULT '0',
  `data` text,
  `expiration_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`session_id`),
  UNIQUE KEY `SESSION_ID` (`session_id`) USING BTREE,
  KEY `UID` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sw_session_info
-- ----------------------------
INSERT INTO `sw_session_info` VALUES ('6ee503283d5647740204d6df78', '3', 'uid|s:1:\"3\";uname|s:6:\"aaaaaa\";', '2017-03-15 16:53:37');
INSERT INTO `sw_session_info` VALUES ('985d511c99e359ee645c0752e0', '4', 'uid|s:1:\"4\";uname|s:6:\"bbbbbb\";', '2017-03-15 16:53:38');

-- ----------------------------
-- Table structure for sw_system_info
-- ----------------------------
DROP TABLE IF EXISTS `sw_system_info`;
CREATE TABLE `sw_system_info` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `content` text,
  `status` tinyint(4) DEFAULT '1',
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sw_system_info
-- ----------------------------
INSERT INTO `sw_system_info` VALUES ('1', '系统管理员', 'swoole聊天室', '欢迎您的访问', '1', '2017-02-13 11:50:41', '2017-02-13 11:30:32');

-- ----------------------------
-- Table structure for sw_tmp_record
-- ----------------------------
DROP TABLE IF EXISTS `sw_tmp_record`;
CREATE TABLE `sw_tmp_record` (
  `is_update` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`is_update`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sw_tmp_record
-- ----------------------------
INSERT INTO `sw_tmp_record` VALUES ('1');

-- ----------------------------
-- Table structure for sw_user
-- ----------------------------
DROP TABLE IF EXISTS `sw_user`;
CREATE TABLE `sw_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` text,
  `tel` varchar(50) DEFAULT NULL,
  `salt` varchar(20) DEFAULT NULL,
  `last_login_time` datetime DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sw_user
-- ----------------------------
INSERT INTO `sw_user` VALUES ('3', 'aaaaaa', '28d9669a157c49b3b64ec3154466795c0bc20aa2', null, '2zu8vItfYg', null, '2017-02-17 17:00:59');
INSERT INTO `sw_user` VALUES ('4', 'bbbbbb', '42e14f131409af5d24e69ce1d9591e56fa32735f', null, 'YO8EQIQwKs', null, '2017-02-17 17:07:36');
DROP TRIGGER IF EXISTS `tmp_record`;
DELIMITER ;;
CREATE TRIGGER `tmp_record` AFTER INSERT ON `sw_info` FOR EACH ROW UPDATE sw_tmp_record SET is_update = 1
;;
DELIMITER ;
DROP TRIGGER IF EXISTS `tmp_record_update`;
DELIMITER ;;
CREATE TRIGGER `tmp_record_update` AFTER UPDATE ON `sw_info` FOR EACH ROW UPDATE sw_tmp_record SET is_update = 1
;;
DELIMITER ;
SET FOREIGN_KEY_CHECKS=1;
