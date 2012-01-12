<?php

require_once('CFileCache.php');

/**
 *	実体SQL用ファイル キャッシュ。
 */
class CFileSQLEntity
	extends CFileCache
{

	//* fields ────────────────────────────────*

	/**	クラス オブジェクト。 */
	private static $instance = null;

	//* constructor & destructor ───────────────────────*

	/**
	 *	コンストラクタ。
	 */
	protected function __construct()
	{
		parent::__construct(IB01_ROOT . '/sql/entity');
	}

	//* class methods ────────────────────────────-*

	/**
	 *	クラス オブジェクトを取得します。
	 *
	 *	@return CFileCache クラス オブジェクト。
	 */
	public static function getInstance()
	{
		if(self::$instance == null)
		{
			self::$instance = new CFileSQLEntity();
		}
		return self::$instance;
	}

	//* instance methods ───────────────────────────*

	/**
	 *	不明なプロパティが呼ばれた際に呼び出されます。
	 *
	 *	ここでは、プロパティ名をファイルと見なし呼び出します。
	 *
	 *	@param ファイル名。
	 *	@return ファイル内容文字列。
	 */
	public function __get($name)
	{
		return $this->load($name . '.sql');
	}
}

?>