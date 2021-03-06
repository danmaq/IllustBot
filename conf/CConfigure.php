<?php

//////////////////////////////////////////////////////////////////////
// 設定ファイル。
// 設定可能な項目はもう少し下にあります。
//////////////////////////////////////////////////////////////////////

require_once(IB01_LIB_ROOT . '/CConstants.php');

/**
 *	設定クラス。
 */
class CConfigure
{

	//////////////////////////////////////////////////////////////////
	// >>>> ここから設定可能エリア
	//////////////////////////////////////////////////////////////////

	/**
	 *	レスポンスを圧縮するかどうかを設定します。
	 */
	const COMPRESS = true;

	/**
	 *	デフォルトの1ページあたりの記事数です。
	 */
	const DEFAULT_TOPIC_PER_PAGE = 100;

	/**
	 *	使用するスキンを設定します。
	 */
	const SKINSET = 'default';

	/**
	 *	XSLT処理をサーバで行わずにクライアントに任せる場合、trueに設定します。
	 *	XSLT処理をクライアントに丸投げするため、PHPの負荷が軽くなります。
	 *
	 *	注意：携帯電話(特にガラケー)の場合、フルブラウザでしか閲覧できなくなります。
	 *	PCブラウザでも、古いバージョンで正しく表示されない場合があります。
	 *
	 *	クライアントサイドXSLTを搭載しているブラウザ一覧
	 *	Internet Explorer Version 6以降
	 *	Mozilla Firefox Version 3以降
	 *	Opera Version 9以降
	 *	Apple Safari Version 3以降
	 */
	const USE_CLIENT_XSLT = false;

	/**
	 *	使用するDBMSを選択します。
	 *
	 *	現在のバージョンではMySQLのみ選択可能です。
	 *	将来的にはSQLiteも対応予定です。
	 */
	const DB_TYPE = 'mysql';

	/**
	 *	データベースのあるホスト名を設定します。
	 */
	const DB_HOST = 'localhost';

	/**
	 *	データベースへ接続するポート名を設定します。
	 */
	const DB_PORT = 3306;

	/**
	 *	データベースにログインするユーザIDを設定します。
	 */
	const DB_USER = 'kagaminerin00001';

	/**
	 *	データベースにログインするパスワードを設定します。
	 */
	const DB_PASSWORD = 'zddscc';

	/**
	 *	使用するデータベース名を設定します。
	 */
	const DB_NAME = 'kagaminerin00001';

	//////////////////////////////////////////////////////////////////
	// <<<< ここまで設定可能エリア
	//////////////////////////////////////////////////////////////////

}

?>