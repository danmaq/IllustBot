CREATE TABLE IF NOT EXISTS IB01_IMAGE_STORE
(
	HASH		INTEGER UNSIGNED	NOT NULL	COMMENT '画像のハッシュ',
	BODY		MEDIUMBLOB			NOT NULL	COMMENT '画像本体',
	PRIMARY KEY (HASH)
)
ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='画像';