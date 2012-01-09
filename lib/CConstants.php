<?php

//////////////////////////////////////////////////////////////////////
// このファイルは原則改変しないでください。
// プログラムが壊れる可能性があります。
//////////////////////////////////////////////////////////////////////

require_once(IB01_ROOT . '/conf/CConfigure.php');

/**
 *	定数クラス。
 */
class CConstants
{

	/** バージョン情報。 */
	const VERSION = '0.0.2';

	/** MySQL用のID。 */
	const DBMS_MYSQL = 'mysql';

	/** 単発メッセージ表示時用XSLファイル。 */
	const FILE_XSL_MESSAGE = 'message.xsl';

	/** トップ ページ表示時用XSLファイル。 */
	const FILE_XSL_TOP = 'top.xsl';

	/** 新規作成時用XSLファイル。 */
	const FILE_XSL_NEW = 'new.xsl';

}

?>