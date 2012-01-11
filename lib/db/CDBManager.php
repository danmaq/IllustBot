<?php

require_once(IB01_CONSTANTS);
require_once('IDB.php');

/**
 *	データベース オブジェクト管理クラス。
 */
class CDBManager
	implements IDB
{

	//* fields ────────────────────────────────*

	/**	クラス オブジェクト。 */
	private static $instance = null;

	/**	データベース オブジェクト。 */
	private $db;

	//* constructor & destructor ───────────────────────*

	/**
	 *	コンストラクタ。
	 */
	private function __construct()
	{
		$db = null;
		require_once(CConfigure::DB_TYPE . '.php');
		$this->db = $db;
	}

	//* class methods ────────────────────────────-*

	/**
	 *	データベース オブジェクトを取得します。
	 *
	 *	@return IDB データベース オブジェクト。
	 */
	public static function getInstance()
	{
		if(self::$instance == null)
		{
			self::$instance = new CDBManager();
		}
		return self::$instance;
	}

	//* instance methods ───────────────────────────*

	/**
	 *	接続を確立します。
	 *
	 *	@return boolean 接続できた場合、true。
	 */
	public function connect()
	{
		return $this->db->connect();
	}

	/**
	 *	PDOオブジェクトを取得します。
	 *
	 *	@return mixed PDOオブジェクト。
	 */
	public function getPDO()
	{
		return $this->db->getPDO();
	}

	/**
	 *	エラー発生時の例外オブジェクトを取得します。
	 *
	 *	@return Exception 例外オブジェクト。
	 */
	public function getException()
	{
		return $this->db->getException();
	}

	/**
	 *	接続を閉じます。
	 */
	public function close()
	{
		return $this->db->close();
	}

	/**
	 *	データベースにSQLを実行させます。
	 *
	 *	@param string $sql データベースに投入するクエリ。
	 *	@param string $args 引数一覧。
	 *	@return boolean 成功した場合、true。
	 */
	public function execute($sql, $args = array())
	{
		return $this->db->execute($sql, $args);
	}

	/**
	 *	データベースから値を取得します。
	 *
	 *	@param string $sql データベースに投入するクエリ。
	 *	@param string $args 引数一覧。
	 *	@return array 値一覧。
	 */
	public function execAndFetch($sql, $args = array())
	{
		return $this->db->execAndFetch($sql, $args);
	}

	/**
	 *	データベースにSQLを実行させ、単一の値を取得します。
	 *
	 *	@param string $sql データベースに投入するクエリ。
	 *	@param string $column 対象の列。
	 *	@param string $args 引数一覧。
	 *	@return mixed 値。
	 */
	public function singleFetch($sql, $column, $args = array())
	{
		return $this->db->singleFetch($sql, $column, $args);
	}
}

?>
