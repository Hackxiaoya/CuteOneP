/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50722
 Source Host           : localhost:3306
 Source Schema         : cuteone_free

 Target Server Type    : MySQL
 Target Server Version : 50722
 File Encoding         : 65001

 Date: 18/06/2019 02:26:27
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cuteone_author_rules
-- ----------------------------
DROP TABLE IF EXISTS `cuteone_author_rules`;
CREATE TABLE `cuteone_author_rules`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '标题',
  `disk_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '网盘ID',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '路径',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '密码',
  `login_hide` int(1) NULL DEFAULT 0 COMMENT '登陆显示',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '是否有效(0:无效,1:有效)',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新日期',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建日期',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '权限规则表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cuteone_configs
-- ----------------------------
DROP TABLE IF EXISTS `cuteone_configs`;
CREATE TABLE `cuteone_configs`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置名称',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置说明',
  `value` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '配置值',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '配置表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cuteone_configs
-- ----------------------------
INSERT INTO `cuteone_configs` VALUES (1, 'username', '后台管理员用户名', 'admin', '2019-06-14 19:37:05', '2019-06-14 19:37:05');
INSERT INTO `cuteone_configs` VALUES (2, 'password', '后台管理员密码', '$2y$10$DpScRKVZIM1NmJCHHBtKPe/nsU2YIVEv.gEIC/jn6TqOtlL.oH4yq', '2019-06-14 19:37:05', '2019-06-14 19:37:05');
INSERT INTO `cuteone_configs` VALUES (3, 'toggle_web_site', '站点开关', '1', '2019-06-14 19:37:05', '2019-06-14 19:37:05');
INSERT INTO `cuteone_configs` VALUES (4, 'web_site', '域名地址', NULL, '2019-06-14 19:37:05', '2019-06-14 19:37:05');
INSERT INTO `cuteone_configs` VALUES (5, 'web_site_title', '网站标题', 'CuteOne 网盘系统', '2019-06-14 19:37:05', '2019-06-14 19:37:05');
INSERT INTO `cuteone_configs` VALUES (6, 'web_site_logo', '网站LOGO', '/images/logo.png', '2019-06-14 19:37:05', '2019-06-14 19:37:05');
INSERT INTO `cuteone_configs` VALUES (7, 'web_site_description', 'SEO描述', 'SEO的描述', '2019-06-14 19:37:05', '2019-06-14 19:37:05');
INSERT INTO `cuteone_configs` VALUES (8, 'web_site_keyword', 'SEO关键字', 'SEO的关键字', '2019-06-14 19:37:05', '2019-06-14 19:37:05');
INSERT INTO `cuteone_configs` VALUES (9, 'web_site_copyright', '版权信息', 'Copyright © CuteOne All rights reserved.', '2019-06-14 19:37:05', '2019-06-14 19:37:05');
INSERT INTO `cuteone_configs` VALUES (10, 'web_site_icp', '网站备案号', '<a href=\"https://github.com/Hackxiaoya/CuteOne\">CuteOne</a>', '2019-06-14 19:37:05', '2019-06-14 19:37:05');
INSERT INTO `cuteone_configs` VALUES (11, 'web_site_statistics', '站点统计', NULL, '2019-06-14 19:37:05', '2019-06-14 19:37:05');
INSERT INTO `cuteone_configs` VALUES (12, 'page_number', '列表条数', '30', '2019-06-14 19:37:05', '2019-06-14 19:37:05');
INSERT INTO `cuteone_configs` VALUES (13, 'search_type', '搜索类型', '0', '2019-06-14 19:37:05', '2019-06-14 19:37:05');
INSERT INTO `cuteone_configs` VALUES (14, 'web_site_background', '背景图', '/images/bg.jpg', '2019-06-14 19:37:05', '2019-06-14 19:37:05');
INSERT INTO `cuteone_configs` VALUES (15, 'is_music', '音乐播放器', '0', '2019-06-14 19:37:05', '2019-06-14 19:37:05');

-- ----------------------------
-- Table structure for cuteone_disks
-- ----------------------------
DROP TABLE IF EXISTS `cuteone_disks`;
CREATE TABLE `cuteone_disks`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `other` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `types` int(11) NOT NULL DEFAULT 1,
  `client_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_secret` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cuteone_menus
-- ----------------------------
DROP TABLE IF EXISTS `cuteone_menus`;
CREATE TABLE `cuteone_menus`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NULL DEFAULT NULL COMMENT '父级ID',
  `position` int(1) NOT NULL DEFAULT 0 COMMENT '位置，0是前端 1是后台',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '标题',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'url',
  `icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图标',
  `top_nav` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '顶级导航',
  `activity_nav` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '二级导航',
  `type` int(1) NOT NULL DEFAULT 0 COMMENT '类型0是自定义，1是网盘驱动，2是模型，3是插件',
  `type_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '模型或者插件的name',
  `activate` int(1) NOT NULL DEFAULT 0 COMMENT '是否默认首页显示驱动，1是 0不是',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '密码',
  `sort` int(10) NULL DEFAULT 0 COMMENT '排序，越大越靠前',
  `target` int(1) NOT NULL DEFAULT 0 COMMENT '打开方式，0当前窗口打开 1新窗口打开',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '状态',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新日期',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建日期',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '菜单表' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
