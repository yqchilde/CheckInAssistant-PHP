/*
 Navicat Premium Data Transfer

 Source Server         : ketangdianming
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : 47.102.135.172:3306
 Source Schema         : ketangdianming

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 22/09/2019 00:18:03
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for t_app
-- ----------------------------
DROP TABLE IF EXISTS `t_app`;
CREATE TABLE `t_app`  (
  `v_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '版本编号',
  `version` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '版本号',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '下载链接地址',
  `notes` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '更新说明',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '版本上线时间',
  `shareDown` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '分享好友的下载信息',
  `status` tinyint(1) NOT NULL COMMENT '状态码(0未上线，1上线)',
  PRIMARY KEY (`v_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of t_app
-- ----------------------------
INSERT INTO `t_app` VALUES (1, '1.0.0', 'http://www.yqqy.top', '修复了若干bug', '2019-09-02 12:47:53', NULL, 0);
INSERT INTO `t_app` VALUES (3, '1.0.1', 'http://qiniu.yqqy.top/__UNI__256407E_0904003730.apk', '改为https协议', '2019-09-03 23:30:57', NULL, 0);
INSERT INTO `t_app` VALUES (4, '1.0.2', 'http://qiniu.yqqy.top/1.0.2.wgt', '改为https并修复了若干bug', '2019-09-04 11:04:13', NULL, 0);
INSERT INTO `t_app` VALUES (5, '1.0.3', 'http://qiniu.yqqy.top/1.0.3.wgt', '改为https并修复了若干bug', '2019-09-04 15:41:52', '欢迎使用签到助手，下载地址为', 0);
INSERT INTO `t_app` VALUES (6, '1.0.5', 'http://qiniu.yqqy.top/1.0.5.wgt', '1.完善部分功能', '2019-09-18 14:47:32', '欢迎使用签到助手，下载地址为：http://qiniu.yqqy.top/1.0.5.apk', 1);

-- ----------------------------
-- Table structure for t_class
-- ----------------------------
DROP TABLE IF EXISTS `t_class`;
CREATE TABLE `t_class`  (
  `classid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '班级ID',
  `classname` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '班级名称',
  `peonum` int(3) UNSIGNED NOT NULL COMMENT '班级容纳人数',
  `userid` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '用户ID',
  `classInfo` json NULL COMMENT '加入用户信息',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态码(0表示正常，1表示删除)',
  PRIMARY KEY (`classid`) USING BTREE,
  INDEX `userid`(`userid`) USING BTREE,
  CONSTRAINT `userid` FOREIGN KEY (`userid`) REFERENCES `t_user` (`userid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of t_class
-- ----------------------------
INSERT INTO `t_class` VALUES (3, '计算机科学一班', 12, 4, '{\"userList\": [{\"name\": \"于波test\", \"userId\": \"7\", \"joinTime\": \"07月31日\"}, {\"name\": \"测试\", \"userId\": \"8\", \"joinTime\": \"2019/08/19\"}, {\"name\": \"测试\", \"userId\": \"5\", \"joinTime\": \"2019/08/21\"}, {\"name\": \"略略\", \"userId\": \"6\", \"joinTime\": \"2019/09/02\"}, {\"name\": \"杨金月\", \"userId\": \"12\", \"joinTime\": \"2019/09/04\"}], \"className\": \"计算机科学一班\"}', 0);
INSERT INTO `t_class` VALUES (5, '计算机科学二班', 505, 4, '{\"userList\": [{\"name\": \"于波test\", \"userId\": \"7\", \"joinTime\": \"07月31日\"}, {\"name\": \"测试\", \"userId\": \"5\", \"joinTime\": \"2019/08/22\"}], \"className\": \"计算机科学二班\"}', 0);
INSERT INTO `t_class` VALUES (6, '计算机科学三班', 40, 4, '{\"userList\": [{\"name\": \"测试\", \"userId\": \"5\", \"joinTime\": \"2019/08/22\"}], \"className\": \"计算机科学三班\"}', 0);
INSERT INTO `t_class` VALUES (7, '计算机科学四班', 38, 4, '{\"userList\": [{\"name\": \"测试\", \"userId\": \"5\", \"joinTime\": \"2019/08/22\"}], \"className\": \"计算机科学四班\"}', 0);
INSERT INTO `t_class` VALUES (8, '计算机科学五班', 55, 4, '{\"userList\": [{\"name\": \"测试\", \"userId\": \"5\", \"joinTime\": \"2019/08/19\"}], \"className\": \"计算机科学五班\"}', 0);
INSERT INTO `t_class` VALUES (10, '测试班级1', 10, 5, '{\"userList\": [{\"name\": \"于波fdfsf\", \"userId\": \"6\", \"joinTime\": \"07月31日\"}, {\"name\": \"于波\", \"userId\": \"4\", \"joinTime\": \"2019/08/23\"}], \"className\": \"测试班级1\"}', 0);
INSERT INTO `t_class` VALUES (11, '测试班级2', 22, 5, '{\"userList\": [{\"name\": \"于波\", \"userId\": \"4\", \"joinTime\": \"2019/08/22\"}], \"className\": \"测试班级2\"}', 0);
INSERT INTO `t_class` VALUES (12, '测试班级3', 33, 5, '{\"className\": \"测试班级3\"}', 0);
INSERT INTO `t_class` VALUES (13, '501', 50, 12, '{\"userList\": [{\"name\": \"高丹丹\", \"userId\": \"13\", \"joinTime\": \"2019/09/04\"}, {\"name\": \"刘君君\", \"userId\": \"14\", \"joinTime\": \"2019/09/04\"}], \"className\": \"501\"}', 0);
INSERT INTO `t_class` VALUES (14, '计算机科学一班', 50, 15, '{\"userList\": [{\"name\": \"测试名字\", \"userId\": \"16\", \"joinTime\": \"2019/09/10\"}], \"className\": \"计算机科学一班\"}', 0);

-- ----------------------------
-- Table structure for t_classroom
-- ----------------------------
DROP TABLE IF EXISTS `t_classroom`;
CREATE TABLE `t_classroom`  (
  `roomid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '课堂ID',
  `roomname` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '课堂名称',
  `classes` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '课堂班级(对应的classid)',
  `userid` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '课堂创建人',
  `roomInfo` json NULL COMMENT '加入班级',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态码(0表示正常，1表示删除)',
  PRIMARY KEY (`roomid`) USING BTREE,
  INDEX `uid`(`userid`) USING BTREE,
  CONSTRAINT `uid` FOREIGN KEY (`userid`) REFERENCES `t_user` (`userid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of t_classroom
-- ----------------------------
INSERT INTO `t_classroom` VALUES (4, '测试课堂2', '11', 5, '{\"roomId\": \"4\", \"joinTime\": 1566438389, \"roomName\": \"测试课堂2\", \"userList\": [{\"name\": \"于波\", \"userId\": \"4\", \"joinTime\": \"2019/08/22\"}], \"joinClass\": [{\"cid\": [\"11\"]}]}', 0);
INSERT INTO `t_classroom` VALUES (5, '临时课堂', '3,5', 4, '{\"roomId\": \"5\", \"joinTime\": 1566439201, \"roomName\": \"临时课堂\", \"userList\": [{\"name\": \"于波test\", \"userId\": \"7\", \"joinTime\": \"07月31日\"}, {\"name\": \"测试\", \"userId\": \"8\", \"joinTime\": \"2019/08/19\"}, {\"name\": \"测试\", \"userId\": \"5\", \"joinTime\": \"2019/08/21\"}, {\"name\": \"略略\", \"userId\": \"6\", \"joinTime\": \"2019/09/02\"}, {\"name\": \"测试还\", \"userId\": \"9\", \"joinTime\": \"2019/09/04\"}, {\"name\": \"杨金月\", \"userId\": \"12\", \"joinTime\": \"2019/09/04\"}, {\"name\": \"测试\", \"userId\": \"5\", \"joinTime\": \"2019/08/22\"}], \"joinClass\": [{\"cid\": [\"3\", \"5\"]}]}', 0);
INSERT INTO `t_classroom` VALUES (6, '临时课堂2', '7', 4, '{\"roomId\": \"6\", \"joinTime\": 1566439528, \"roomName\": \"临时课堂2\", \"userList\": [{\"name\": \"测试\", \"userId\": \"5\", \"joinTime\": \"2019/08/22\"}], \"joinClass\": [{\"cid\": [\"7\"]}]}', 0);
INSERT INTO `t_classroom` VALUES (7, '临时课堂3', '5', 4, '{\"roomId\": \"7\", \"joinTime\": 1566441650, \"roomName\": \"临时课堂3\", \"userList\": [{\"name\": \"于波test\", \"userId\": \"7\", \"joinTime\": \"07月31日\"}, {\"name\": \"测试\", \"userId\": \"5\", \"joinTime\": \"2019/08/22\"}], \"joinClass\": [{\"cid\": [\"5\"]}]}', 0);
INSERT INTO `t_classroom` VALUES (8, '三班集合', '6', 4, '{\"roomId\": \"8\", \"joinTime\": 1566446323, \"roomName\": \"三班集合\", \"userList\": [{\"name\": \"测试\", \"userId\": \"5\", \"joinTime\": \"2019/08/22\"}], \"joinClass\": [{\"cid\": [\"6\"]}]}', 0);
INSERT INTO `t_classroom` VALUES (9, '计算机', '13', 12, '{\"roomId\": \"9\", \"joinTime\": 1567589418, \"roomName\": \"计算机\", \"userList\": [{\"name\": \"高丹丹\", \"userId\": \"13\", \"joinTime\": \"2019/09/04\"}], \"joinClass\": [{\"cid\": [\"13\"]}]}', 0);
INSERT INTO `t_classroom` VALUES (10, '一班的专属课堂', '3', 4, '{\"roomId\": \"10\", \"joinTime\": 1567590042, \"roomName\": \"一班的专属课堂\", \"userList\": [{\"name\": \"于波test\", \"userId\": \"7\", \"joinTime\": \"07月31日\"}, {\"name\": \"测试\", \"userId\": \"8\", \"joinTime\": \"2019/08/19\"}, {\"name\": \"测试\", \"userId\": \"5\", \"joinTime\": \"2019/08/21\"}, {\"name\": \"略略\", \"userId\": \"6\", \"joinTime\": \"2019/09/02\"}, {\"name\": \"杨金月\", \"userId\": \"12\", \"joinTime\": \"2019/09/04\"}], \"joinClass\": [{\"cid\": [\"3\"]}]}', 0);
INSERT INTO `t_classroom` VALUES (11, '第一节课', '14', 15, '{\"roomId\": \"11\", \"joinTime\": 1568106446, \"roomName\": \"第一节课\", \"userList\": [{\"name\": \"测试名字\", \"userId\": \"16\", \"joinTime\": \"2019/09/10\"}], \"joinClass\": [{\"cid\": [\"14\"]}]}', 0);

-- ----------------------------
-- Table structure for t_sign
-- ----------------------------
DROP TABLE IF EXISTS `t_sign`;
CREATE TABLE `t_sign`  (
  `signid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '签到ID',
  `userid` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '发起签到人的id',
  `roomid` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '课堂ID',
  `begintime` int(10) NULL DEFAULT NULL COMMENT '签到开始时间',
  `signtime` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '签到时长',
  `signinway` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '发起签到方式',
  `signInfo` json NULL COMMENT '签到信息',
  `signList` json NULL COMMENT '签到人员列表',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态码(0表示正常，1表示删除)',
  PRIMARY KEY (`signid`) USING BTREE,
  INDEX `classroomid`(`roomid`) USING BTREE,
  INDEX `uuid`(`userid`) USING BTREE,
  CONSTRAINT `roomid` FOREIGN KEY (`roomid`) REFERENCES `t_classroom` (`roomid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `uuid` FOREIGN KEY (`userid`) REFERENCES `t_user` (`userid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 40 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of t_sign
-- ----------------------------
INSERT INTO `t_sign` VALUES (12, 4, 7, 1566381281, '5min', 'code', '{\"roomId\": \"1\", \"roomName\": \"第一节课\", \"signCode\": \"3618\", \"signTime\": \"1566381581\", \"beginTime\": 1566381281, \"headCount\": 2, \"signInWay\": \"code\"}', '[{\"userId\": \"5\", \"signTime\": 1566381549, \"userName\": \"测试\"}, {\"userId\": \"6\", \"signTime\": 1566381549, \"userName\": \"测试\"}]', 0);
INSERT INTO `t_sign` VALUES (13, 5, 4, 1566438038, '1min', 'code', '{\"roomId\": \"3\", \"roomName\": \"测试课堂1\", \"signCode\": \"5942\", \"signTime\": \"1566438098\", \"beginTime\": 1566438038, \"signInWay\": \"code\"}', NULL, 0);
INSERT INTO `t_sign` VALUES (14, 5, 4, 1566438771, '5min', 'code', '{\"roomId\": \"3\", \"roomName\": \"测试课堂1\", \"signCode\": \"0228\", \"signTime\": \"1566439071\", \"beginTime\": 1566438771, \"signInWay\": \"code\"}', NULL, 0);
INSERT INTO `t_sign` VALUES (15, 5, 4, 1566446072, '1min', 'code', '{\"roomId\": \"4\", \"roomName\": \"测试课堂2\", \"signCode\": \"4105\", \"signTime\": \"1566446132\", \"beginTime\": 1566446072, \"signInWay\": \"code\"}', NULL, 0);
INSERT INTO `t_sign` VALUES (16, 4, 8, 1566446350, '5min', 'code', '{\"roomId\": \"8\", \"roomName\": \"三班集合\", \"signCode\": \"1410\", \"signTime\": \"1566446650\", \"beginTime\": 1566446350, \"signInWay\": \"code\"}', '[{\"userId\": \"5\", \"signTime\": 1566446361, \"userName\": \"测试\"}]', 0);
INSERT INTO `t_sign` VALUES (17, 5, 4, 1566793895, '1min', 'code', '{\"roomId\": \"4\", \"roomName\": \"测试课堂2\", \"signCode\": \"5624\", \"signTime\": \"1566793955\", \"beginTime\": 1566793895, \"signInWay\": \"code\"}', '[{\"userId\": \"4\", \"signTime\": 1566793905, \"userName\": \"于波\"}]', 0);
INSERT INTO `t_sign` VALUES (20, 4, 5, 1566996619, '5min', 'gps', '{\"roomId\": \"5\", \"latitude\": \"36.68773\", \"roomName\": \"临时课堂\", \"signTime\": \"1566996919\", \"beginTime\": 1566996619, \"longitude\": \"117.324402\", \"signInWay\": \"gps\"}', NULL, 0);
INSERT INTO `t_sign` VALUES (21, 4, 8, 1567037956, '5min', 'gps', '{\"roomId\": \"8\", \"latitude\": \"36.689301\", \"roomName\": \"三班集合\", \"signTime\": \"1567038256\", \"beginTime\": 1567037956, \"longitude\": \"117.325095\", \"signInWay\": \"gps\"}', NULL, 0);
INSERT INTO `t_sign` VALUES (22, 4, 5, 1567038269, '5min', 'gps', '{\"roomId\": \"5\", \"latitude\": \"36.689225\", \"roomName\": \"临时课堂\", \"signTime\": \"1567038569\", \"beginTime\": 1567038269, \"longitude\": \"117.325168\", \"signInWay\": \"gps\"}', '[{\"userId\": \"4\", \"signTime\": 1567038442, \"userName\": \"于波\"}]', 0);
INSERT INTO `t_sign` VALUES (23, 4, 5, 1567039048, '5min', 'gps', '{\"roomId\": \"5\", \"latitude\": \"36.689291\", \"roomName\": \"临时课堂\", \"signTime\": \"1567039348\", \"beginTime\": 1567039048, \"longitude\": \"117.325189\", \"signInWay\": \"gps\"}', '[{\"userId\": \"5\", \"signTime\": 1567039142, \"userName\": \"测试\"}]', 0);
INSERT INTO `t_sign` VALUES (24, 4, 7, 1567403231, '5min', 'gps', '{\"roomId\": \"7\", \"latitude\": \"36.688383\", \"roomName\": \"临时课堂3\", \"signTime\": \"1567403531\", \"beginTime\": 1567403231, \"longitude\": \"117.324241\", \"signInWay\": \"gps\"}', NULL, 0);
INSERT INTO `t_sign` VALUES (25, 4, 8, 1567412475, '1min', 'gps', '{\"roomId\": \"8\", \"latitude\": \"36.687863\", \"roomName\": \"三班集合\", \"signTime\": \"1567412535\", \"beginTime\": 1567412475, \"longitude\": \"117.324034\", \"signInWay\": \"gps\"}', '[{\"userId\": \"5\", \"signTime\": 1567412508, \"userName\": \"测试\"}]', 0);
INSERT INTO `t_sign` VALUES (26, 4, 5, 1567425167, '5min', 'gps', '{\"roomId\": \"5\", \"latitude\": \"36.687142\", \"roomName\": \"临时课堂\", \"signTime\": \"1567425467\", \"beginTime\": 1567425167, \"longitude\": \"117.324972\", \"signInWay\": \"gps\"}', '[{\"userId\": \"6\", \"signTime\": 1567425227, \"userName\": \"略略\"}]', 0);
INSERT INTO `t_sign` VALUES (27, 4, 5, 1567425373, '5min', 'code', '{\"roomId\": \"5\", \"roomName\": \"临时课堂\", \"signCode\": \"0049\", \"signTime\": \"1567425673\", \"beginTime\": 1567425373, \"signInWay\": \"code\"}', '[{\"userId\": \"6\", \"signTime\": 1567425386, \"userName\": \"略略\"}]', 0);
INSERT INTO `t_sign` VALUES (28, 4, 5, 1567425409, '5min', 'code', '{\"roomId\": \"5\", \"roomName\": \"临时课堂\", \"signCode\": \"0427\", \"signTime\": \"1567425709\", \"beginTime\": 1567425409, \"signInWay\": \"code\"}', NULL, 0);
INSERT INTO `t_sign` VALUES (29, 4, 10, 1567590079, '5min', 'code', '{\"roomId\": \"10\", \"roomName\": \"一班的专属课堂\", \"signCode\": \"7855\", \"signTime\": \"1567590379\", \"beginTime\": 1567590079, \"signInWay\": \"code\"}', '[{\"userId\": \"12\", \"signTime\": 1567590116, \"userName\": \"杨金月\"}]', 0);
INSERT INTO `t_sign` VALUES (30, 4, 10, 1567590151, '5min', 'gps', '{\"roomId\": \"10\", \"latitude\": \"36.688944\", \"roomName\": \"一班的专属课堂\", \"signTime\": \"1567590451\", \"beginTime\": 1567590151, \"longitude\": \"117.325091\", \"signInWay\": \"gps\"}', '[{\"userId\": \"12\", \"signTime\": 1567590282, \"userName\": \"杨金月\"}]', 0);
INSERT INTO `t_sign` VALUES (31, 12, 9, 1567604694, '1min', 'code', '{\"roomId\": \"9\", \"roomName\": \"计算机\", \"signCode\": \"2759\", \"signTime\": \"1567604754\", \"beginTime\": 1567604694, \"signInWay\": \"code\"}', NULL, 0);
INSERT INTO `t_sign` VALUES (32, 12, 9, 1567604782, '1min', 'code', '{\"roomId\": \"9\", \"roomName\": \"计算机\", \"signCode\": \"3168\", \"signTime\": \"1567604842\", \"beginTime\": 1567604782, \"signInWay\": \"code\"}', '[{\"userId\": \"13\", \"signTime\": 1567604824, \"userName\": \"高丹丹\"}]', 0);
INSERT INTO `t_sign` VALUES (33, 5, 4, 1567756882, '5min', 'gps', '{\"roomId\": \"4\", \"latitude\": \"36.688945\", \"roomName\": \"测试课堂2\", \"signTime\": \"1567757182\", \"beginTime\": 1567756882, \"longitude\": \"117.32509\", \"signInWay\": \"gps\"}', NULL, 0);
INSERT INTO `t_sign` VALUES (34, 12, 9, 1567950109, '1min', 'code', '{\"roomId\": \"9\", \"roomName\": \"计算机\", \"signCode\": \"1376\", \"signTime\": \"1567950169\", \"beginTime\": 1567950109, \"signInWay\": \"code\"}', NULL, 0);
INSERT INTO `t_sign` VALUES (35, 12, 9, 1567950138, '1min', 'gps', '{\"roomId\": \"9\", \"latitude\": \"36.689094\", \"roomName\": \"计算机\", \"signTime\": \"1567950198\", \"beginTime\": 1567950138, \"longitude\": \"117.319342\", \"signInWay\": \"gps\"}', NULL, 0);
INSERT INTO `t_sign` VALUES (36, 4, 10, 1568034670, '5min', 'code', '{\"roomId\": \"10\", \"roomName\": \"一班的专属课堂\", \"signCode\": \"9474\", \"signTime\": \"1568034970\", \"beginTime\": 1568034670, \"signInWay\": \"code\"}', NULL, 0);
INSERT INTO `t_sign` VALUES (37, 4, 10, 1568034688, '5min', 'gps', '{\"roomId\": \"10\", \"latitude\": \"36.687847\", \"roomName\": \"一班的专属课堂\", \"signTime\": \"1568034988\", \"beginTime\": 1568034688, \"longitude\": \"117.324497\", \"signInWay\": \"gps\"}', NULL, 0);
INSERT INTO `t_sign` VALUES (38, 15, 11, 1568106485, '5min', 'code', '{\"roomId\": \"11\", \"roomName\": \"第一节课\", \"signCode\": \"4087\", \"signTime\": \"1568106785\", \"beginTime\": 1568106485, \"signInWay\": \"code\"}', '[{\"userId\": \"16\", \"signTime\": 1568106551, \"userName\": \"测试名字\"}]', 0);
INSERT INTO `t_sign` VALUES (39, 15, 11, 1568106514, '5min', 'gps', '{\"roomId\": \"11\", \"latitude\": \"36.686949\", \"roomName\": \"第一节课\", \"signTime\": \"1568106814\", \"beginTime\": 1568106514, \"longitude\": \"117.324161\", \"signInWay\": \"gps\"}', '[{\"userId\": \"16\", \"signTime\": 1568106580, \"userName\": \"测试名字\"}]', 0);

-- ----------------------------
-- Table structure for t_user
-- ----------------------------
DROP TABLE IF EXISTS `t_user`;
CREATE TABLE `t_user`  (
  `userid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `phoneNumber` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户名',
  `password` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '密码',
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '邮箱',
  `class` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '所属班级',
  `realname` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '真实姓名',
  `regtime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '注册时间',
  `userInfo` json NULL COMMENT '用户个人信息',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态码(0表示正常，1表示删除)',
  PRIMARY KEY (`userid`) USING BTREE,
  UNIQUE INDEX `phoneNumber`(`phoneNumber`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of t_user
-- ----------------------------
INSERT INTO `t_user` VALUES (4, '15555055952', 'f0b16b6c304633d83b707f51ed05876c', '1455723643@qq.com', '计科一班', '于波', 1562937522, '{\"joinClass\": [{\"cid\": \"11\"}, {\"cid\": \"10\"}, {\"cid\": null}]}', 0);
INSERT INTO `t_user` VALUES (5, '15555055123', '35c4022f539bf8bcde24c5e955b3f028', NULL, NULL, '测试', 1566048523, '{\"joinClass\": [{\"cid\": \"3\"}, {\"cid\": \"8\"}, {\"cid\": \"7\"}, {\"cid\": \"5\"}, {\"cid\": \"6\"}]}', 0);
INSERT INTO `t_user` VALUES (6, '15106972337', '6ef7897ba0592251a542f729ef741ecd', NULL, NULL, '略略', 1566517558, '{\"joinClass\": [{\"cid\": \"3\"}]}', 0);
INSERT INTO `t_user` VALUES (7, '15555055000', '4441dc594172c2bd412df3add9bfdb00', NULL, NULL, NULL, 1566788634, NULL, 0);
INSERT INTO `t_user` VALUES (12, '17860544838', '3f1ca14c45c74b7d5a0acce8c13dfb4b', NULL, NULL, '杨金月', 1567583815, '{\"joinClass\": [{\"cid\": \"3\"}]}', 0);
INSERT INTO `t_user` VALUES (13, '18853793014', '688082ec393bf970dd06c96e348a75fc', NULL, NULL, '高丹丹', 1567604450, '{\"joinClass\": [{\"cid\": \"13\"}]}', 0);
INSERT INTO `t_user` VALUES (14, '17862915726', 'a8096e765f4bd62723889e734bc44a28', NULL, NULL, '刘君君', 1567604808, '{\"joinClass\": [{\"cid\": \"13\"}]}', 0);
INSERT INTO `t_user` VALUES (15, '15555555555', '6c7400f152aa54f1dc058d89de28fb05', '1455723643@qq.com', NULL, '于波', 1568106116, NULL, 0);
INSERT INTO `t_user` VALUES (16, '16666666666', 'e262b32f6a42c16a12f136c2c8238105', NULL, NULL, '测试名字', 1568106169, '{\"joinClass\": [{\"cid\": \"14\"}]}', 0);

SET FOREIGN_KEY_CHECKS = 1;
