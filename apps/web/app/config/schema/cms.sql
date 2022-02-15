CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `name` varchar(40) NOT NULL DEFAULT '',
  `username` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `admins` (`id`, `created`, `modified`, `name`, `username`, `password`, role) VALUES
(1, NOW(), NOW(),  '管理者', 'caters_admin', '$2y$10$7X.icRPhUBnFrsoBR784y.VMC9IrXxbbinEff3WMGa0N.WG3D8kH6',0);

INSERT INTO `admins` (`id`, `created`, `modified`, `name`, `username`, `password`, role) VALUES
(2, NOW(), NOW(),  '管理者', 'demo', '$2y$10$zGVtQmpuYTmnhnQHc50ElOV1nlrl8rdXesJUvBsUDOvaHDrDVat8G',0);


CREATE TABLE mst_literal (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  ltrl_sys_kb decimal(1,0) NOT NULL DEFAULT 0 COMMENT '0:通常、1:管理用',
  ltrl_kb char(3) NOT NULL,
  position decimal(3,0) NOT NULL COMMENT '表示順',
  status enum('publish', 'draft') NOT NULL DEFAULT 'publish',
  ltrl_cd char(10) NOT NULL,
  ltrl_nm varchar(60) DEFAULT NULL,
  ltrl_val varchar(60) DEFAULT NULL,
  ltrl_sub_val text NULL,
  created datetime NOT NULL,
  modified datetime NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniqe_sys_kb_kb_cd (ltrl_sys_kb,ltrl_kb,ltrl_cd)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE kvs (
  id int unsigned NOT NULL AUTO_INCREMENT,
  created datetime NOT NULL,
  modified datetime NOT NULL,
  name varchar(50) NOT NULL DEFAULT '',
  key_name varchar(40) NOT NULL DEFAULT '',
  val text NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

/* Create Tables */

-- コンテンツ
CREATE TABLE contents
(
  id int unsigned NOT NULL AUTO_INCREMENT,
  created datetime NOT NULL,
  modified datetime NOT NULL,
  site_config_id int unsigned DEFAULT 0 NOT NULL,
  position int unsigned DEFAULT 0 NOT NULL,
  status enum('publish','draft') DEFAULT 'publish' NOT NULL,
  name varchar(40) DEFAULT '' NOT NULL,
  serial_no int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- コンテンツ素材
CREATE TABLE content_materials
(
  id int unsigned NOT NULL AUTO_INCREMENT,
  created datetime NOT NULL,
  modified datetime NOT NULL,
  content_id int unsigned DEFAULT 0 NOT NULL,
  material_id int unsigned DEFAULT 0 NOT NULL,
  position int unsigned NOT NULL DEFAULT 0,
  view_second int unsigned DEFAULT 0 NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- 機械箱
CREATE TABLE machine_boxes
(
  id int unsigned NOT NULL AUTO_INCREMENT,
  created datetime NOT NULL,
  modified datetime NOT NULL,
  site_config_id int unsigned DEFAULT 0 NOT NULL,
  content_id int unsigned DEFAULT 0,
  name varchar(40) DEFAULT '' NOT NULL,
  position int unsigned NOT NULL DEFAULT 0,
  url varchar(100) DEFAULT '' NOT NULL,
  resolution decimal(2) unsigned DEFAULT 0 NOT NULL,
  width int DEFAULT 0 NOT NULL,
  height int DEFAULT 0 NOT NULL,
  memo text NOT NULL,
  reload_flag decimal(1) NOT NULL DEFAULT 0,
  is_vertical decimal(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- 機械箱コンテンツ
CREATE TABLE machine_contents
(
  id int unsigned NOT NULL AUTO_INCREMENT,
  created datetime NOT NULL,
  modified datetime NOT NULL,
  site_config_id int unsigned DEFAULT 0 NOT NULL,
  position int unsigned DEFAULT 0 NOT NULL,
  status enum('publish','draft') DEFAULT 'publish' NOT NULL,
  name varchar(40) DEFAULT '' NOT NULL,
  serial_no int unsigned DEFAULT 0 NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- 機械箱コンテンツ素材
CREATE TABLE machine_materials
(
  id int unsigned NOT NULL AUTO_INCREMENT,
  created datetime NOT NULL,
  modified datetime NOT NULL,
  position int unsigned DEFAULT 0 NOT NULL,
  status enum('publish','draft') DEFAULT 'publish' NOT NULL,
  role int unsigned DEFAULT 0 NOT NULL,
  name varchar(40) DEFAULT '' NOT NULL,
  type decimal(2) unsigned NOT NULL,
  image varchar(100) DEFAULT '' NOT NULL,
  movie_tag text NOT NULL,
  url varchar(255) DEFAULT '' NOT NULL,
  content text NOT NULL,
  file varchar(100) DEFAULT '' NOT NULL,
  file_name varchar(100) DEFAULT '' NOT NULL,
  file_size int unsigned DEFAULT 0 NOT NULL,
  file_extension varchar(10) DEFAULT '' NOT NULL,
  view_second int DEFAULT 0 NOT NULL,
  machine_content_id int unsigned DEFAULT 0 NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- 素材
CREATE TABLE materials
(
  id int unsigned NOT NULL AUTO_INCREMENT,
  created datetime NOT NULL,
  modified datetime NOT NULL,
  position int unsigned DEFAULT 0 NOT NULL,
  status enum('publish','draft') DEFAULT 'publish' NOT NULL,
  role int unsigned DEFAULT 0 NOT NULL,
  name varchar(40) DEFAULT '' NOT NULL,
  type decimal(2) unsigned NOT NULL,
  image varchar(100) DEFAULT '' NOT NULL,
  movie_tag text NOT NULL,
  url varchar(255) DEFAULT '' NOT NULL,
  content text NOT NULL,
  file varchar(100) NOT NULL DEFAULT '',
  file_name varchar(100) NOT NULL DEFAULT '',
  file_size int NOT NULL DEFAULT 0,
  file_extension varchar(10) NOT NULL DEFAULT '',
  view_second int NOT NULL DEFAULT 0,
  site_config_id int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- サイト設定
CREATE TABLE site_configs
(
  id int unsigned NOT NULL AUTO_INCREMENT,
  created datetime NOT NULL,
  modified datetime NOT NULL,
  position int unsigned DEFAULT 0 NOT NULL,
  status enum('draft','publish') DEFAULT 'draft' NOT NULL,
  site_name varchar(100) DEFAULT '',
  slug varchar(40) DEFAULT '' NOT NULL,
  is_root decimal(1) DEFAULT 0 NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- ユーザー
CREATE TABLE users
(
  id int unsigned NOT NULL AUTO_INCREMENT,
  updated datetime NOT NULL,
  modified datetime NOT NULL,
  email varchar(200) DEFAULT '' NOT NULL,
  username varchar(30) DEFAULT '' NOT NULL,
  password varchar(200) DEFAULT '' NOT NULL,
  temp_password varchar(40) DEFAULT '' NOT NULL,
  temp_pass_expired datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  temp_key varchar(200) DEFAULT '' NOT NULL,
  name varchar(60) DEFAULT '' NOT NULL,
  status enum('publish','draft') DEFAULT 'publish' NOT NULL,
  role int DEFAULT 1 NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- ユーザーサイト
CREATE TABLE user_sites
(
  id int unsigned NOT NULL AUTO_INCREMENT,
  created datetime NOT NULL,
  modified datetime NOT NULL,
  user_id int unsigned DEFAULT 0 NOT NULL,
  site_config_id int unsigned DEFAULT 0 NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- 2022/01/25
-- カテゴリ
CREATE TABLE material_categories
(
  id int unsigned NOT NULL AUTO_INCREMENT,
  created datetime NOT NULL,
  modified datetime NOT NULL,
  parent_category_id int unsigned DEFAULT 0 NOT NULL,
  position int unsigned DEFAULT 0 NOT NULL,
  status enum('draft','publish') DEFAULT 'publish' NOT NULL,
  name varchar(40) DEFAULT '' NOT NULL,
  identifier varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE materials ADD COLUMN category_id int unsigned NOT NULL DEFAULT 0;
-- 2022/01/25 end

-- 2022/02/03
ALTER TABLE machine_boxes ADD COLUMN reload_flag_device decimal(1) NOT NULL DEFAULT 0;

-- 2022/02/10
ALTER TABLE materials ADD COLUMN status_mp4 enum('converting', 'converted') DEFAULT 'converted' NOT NULL;
ALTER TABLE content_materials ADD COLUMN rolling_caption text NOT NULL;
ALTER TABLE machine_materials ADD COLUMN rolling_caption text NOT NULL;
-- 追加漏れのカラム
ALTER TABLE machine_boxes ADD COLUMN rolling_caption text NOT NULL;

-- 2022/02/15
ALTER TABLE machine_boxes ADD COLUMN caption_flg enum('machine', 'content') DEFAULT 'machine' NOT NULL;
