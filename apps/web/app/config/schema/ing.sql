/* Create Tables */

CREATE TABLE artists
(
    id int unsigned NOT NULL AUTO_INCREMENT,
    created datetime NOT NULL,
    modified datetime NOT NULL,
    position int unsigned DEFAULT 0 NOT NULL,
    name varchar(60) DEFAULT '' NOT NULL,
    kana varchar(60) DEFAULT '' NOT NULL,
    predicted_char text NOT NULL,
    status enum('publish','draft') DEFAULT 'publish' NOT NULL,
    member_count int unsigned NOT NULL DEFAULT 0,
    asset_no varchar(20) NOT NULL DEFAULT '',
    PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE artist_rates
(
    id int unsigned NOT NULL AUTO_INCREMENT,
    created datetime NOT NULL,
    modified datetime NOT NULL,
    artist_id int unsigned DEFAULT 0 NOT NULL,
    position int unsigned NOT NULL DEFAULT 0,
    name varchar(20) DEFAULT '' NOT NULL,
    -- パーセント。１００倍して保存
    rate int DEFAULT 0 NOT NULL COMMENT 'パーセント。１００倍して保存',
    PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE companies
(
    id int unsigned NOT NULL AUTO_INCREMENT,
    created datetime NOT NULL,
    modified datetime NOT NULL,
    position int unsigned DEFAULT 0 NOT NULL,
    status enum('publish','draft') DEFAULT 'publish' NOT NULL,
    name varchar(60) DEFAULT '' NOT NULL,
    full_name varchar(60) DEFAULT '' NOT NULL,
    honorific varchar(10) DEFAULT '' NOT NULL,
    kana varchar(60) DEFAULT '' NOT NULL,
    predicted_char text NOT NULL,
    rate int NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE imports
(
    id int unsigned NOT NULL AUTO_INCREMENT,
    created datetime NOT NULL,
    modified datetime NOT NULL,
    date date DEFAULT '0000-00-00' NOT NULL,
    media_id int unsigned DEFAULT 0 NOT NULL,
    is_delete decimal(1) unsigned DEFAULT 0 NOT NULL,
    file_name varchar(100) NOT NULL DEFAULT '',
    PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE import_configs
(
    id int unsigned NOT NULL AUTO_INCREMENT,
    created datetime NOT NULL,
    modified datetime NOT NULL,
    media_id int unsigned DEFAULT 0 NOT NULL,
    key_name varchar(40) DEFAULT '' NOT NULL,
    -- CSVなら列番号、EXCELなら番地
    location varchar(10) DEFAULT '' NOT NULL COMMENT 'CSVなら列番号、EXCELなら番地',
    position int unsigned DEFAULT 0 NOT NULL,
    is_fixed decimal(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE import_details
(
    id int unsigned NOT NULL AUTO_INCREMENT,
    created datetime NOT NULL,
    modified datetime NOT NULL,
    import_id int unsigned DEFAULT 0 NOT NULL,
    position int unsigned DEFAULT 0 NOT NULL,
    artist_id int unsigned DEFAULT 0 NOT NULL,
    artist_name varchar(60) DEFAULT '' NOT NULL,
    music_id int unsigned DEFAULT 0 NOT NULL,
    music_name varchar(60) DEFAULT '' NOT NULL,
    isrc varchar(20) DEFAULT '' NOT NULL,
    fixed decimal(1) DEFAULT 0 NOT NULL,
    is_delete decimal(1) DEFAULT 0 NOT NULL,
    row_data text NOT NULL,
    price float(8,4) DEFAULT 0.0 NOT NULL,
    dl_count int DEFAULT 0 NOT NULL,
    amount float(10,3) DEFAULT 0 NOT NULL,
    ym decimal(6) unsigned DEFAULT 0 NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE media
(
    id int unsigned NOT NULL AUTO_INCREMENT,
    created datetime NOT NULL,
    modified datetime NOT NULL,
    position int unsigned DEFAULT 0 NOT NULL,
    name varchar(40) DEFAULT '' NOT NULL,
    full_name varchar(40) DEFAULT '' NOT NULL,
    -- 1=CSV、2=エクセル
    import_type decimal(2) unsigned DEFAULT 0 NOT NULL COMMENT '1=CSV、2=エクセル',
    status enum('publish','draft') DEFAULT 'publish' NOT NULL,
    import_start_row int NOT NULL DEFAULT 0,
    input_encoding varchar(10) DEFAULT '' NOT NULL,
    enclosure varchar(10) DEFAULT '' NOT NULL,
    parent_id int unsigned NOT NULL DEFAULT 0,
    attent_memo text,
    rate int NOT NULL DEFAULT 0,
    is_plan decimal(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE media_plans
(
    id int unsigned NOT NULL AUTO_INCREMENT,
    created datetime NOT NULL,
    modified datetime NOT NULL,
    media_id int unsigned DEFAULT 0 NOT NULL,
    position int unsigned DEFAULT 0 NOT NULL,
    name varchar(40) DEFAULT '' NOT NULL,
    price varchar(12) DEFAULT '0' NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE musics
(
    id int unsigned NOT NULL AUTO_INCREMENT,
    created datetime NOT NULL,
    modified datetime NOT NULL,
    position int unsigned DEFAULT 0 NOT NULL,
    status enum('publish','draft') DEFAULT 'publish' NOT NULL,
    artist_id int unsigned DEFAULT 0 NOT NULL,
    name varchar(60) DEFAULT '' NOT NULL,
    kana varchar(60) DEFAULT '' NOT NULL,
    predicted_char text NOT NULL,
    company_id int unsigned DEFAULT 0 NOT NULL,
    isrc varchar(20) NOT NULL DEFAULT '',
    rate int NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

