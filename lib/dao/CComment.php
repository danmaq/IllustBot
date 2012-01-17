<?php

require_once('CBot.php');
require_once(IB01_LIB_ROOT . '/file/CFileSQLComment.php');

/**
 *	コメントDAOクラス。
 */
class CComment
	extends CDataIndex
{

	//* fields ────────────────────────────────*

	/**	実体のメンバとデフォルト値一覧。 */
	private static $format = array(
		'message'	=> '',
	);

	/**	初期化済みかどうか。 */
	private static $initialized = false;

	/**	親ぼっと。 */
	private $owner = null;

	//* constructor & destructor ───────────────────────*

	/**
	 *	コンストラクタ。
	 *
	 *	@param string $id コメントID 兼 実体ID。
	 *	@param string $entity_id 実体ID(GUID)。
	 */
	public function __construct($id = null, $owner = null)
	{
		parent::__construct(self::$format, $id);
	}

	//* class methods ────────────────────────────-*

	/**
	 *	テーブルの初期化をします。
	 */
	public static function initialize()
	{
		if(!self::$initialized)
		{
			CDataEntity::initializeTable();
			CDBManager::getInstance()->execute(CFileSQLComment::getInstance()->ddl);
			self::$initialized = true;
		}
	}

	/**
	 *	指定のぼっとにつけられたコメント数を取得します。
	 *
	 *	@param CBot $owner 親ぼっと。
	 *	@return int コメント数。
	 */
	public static function getCountFromOwner(CBot $owner)
	{
		self::initialize();
		return CDBManager::getInstance()->singleFetch(
			CFileSQLComment::getInstance()->selectCount, 'COUNT',
			self::createDBParamsFromOwner($owner));
	}

	/**
	 *	指定のぼっとにつけられたコメント一覧を取得します。
	 *
	 *	@param CBot $owner 親ぼっと。
	 *	@return array[CComment] コメント一覧。
	 */
	public static function getListFromOwner(CBot $owner)
	{
		self::initialize();
		$info = CDBManager::getInstance()->execAndFetch(
			CFileSQLComment::getInstance()->selectFromOwner,
			self::createDBParamsFromOwner($owner));
		$result = array();
		for($i = count($info); --$i >= 0; )
		{
			$item = new CComment($info[$i]['ENTITY_ID'], $owner->getID());
			if($item->rollback())
			{
				array_unshift($result, $item);
			}
		}
		return $result;
	}

	/**
	 *	DB受渡し用のパラメータを生成します。
	 *
	 *	@param CBot $owner 親ぼっと。
	 *	@return array DB受渡し用のパラメータ。
	 */
	private static function createDBParamsFromOwner(CBot $owner)
	{
		return array('owner' => array($owner->getID(), PDO::PARAM_STR));
	}

	//* instance methods ───────────────────────────*

	/**
	 *	コメントIDを取得します。
	 *
	 *	@return string コメントID。実体IDと兼用(GUID)。
	 */
	public function getID()
	{
		return $this->getEntity()->getID();
	}

	/**
	 *	更新日時を取得します。
	 *
	 *	@return mixed 更新日時。
	 */
	public function getUpdated()
	{
		return $this->getEntity()->getUpdated();
	}

	/**
	 *	親ぼっとを取得します。
	 *
	 *	@param boolean $autoCreate 自動的にオブジェクトを生成するかどうか。
	 *	@return CBot 親ぼっと。
	 */
	public function getOwner($autoCreate = true)
	{
		$result = $this->owner;
		if($autoCreate && is_string($result))
		{
			$result = new CBot($result);
			$result->rollback();
		}
		return $result;
	}

	/**
	 *	親ぼっとを設定します。
	 *
	 *	@param CBot or string $value 親ぼっと。
	 */
	public function setOwner($value)
	{
		$this->owner = $value;
	}

	/**
	 *	本文を取得します。
	 *
	 *	@return string 本文。
	 */
	public function getMessage()
	{
		$body =& $this->storage();
		return $body['message'];
	}

	/**
	 *	本文を設定します。
	 *
	 *	@param string $value 本文。
	 */
	public function setMessage($value)
	{
		$body =& $this->storage();
		$body['message'] = $value;
	}

	/**
	 *	データベースに保存されているかどうかを取得します。
	 *
	 *	注意: この関数は、コミットされているかどうかを保証するものではありません。
	 *
	 *	@return boolean 保存されている場合、true。
	 */
	public function isExists()
	{
		self::initialize();
		return CDBManager::getInstance()->singleFetch(
			CFileSQLComment::getInstance()->selectExists,
			'EXIST', $this->createDBParamsOnlyEID());
	}

	/**
	 *	削除します。
	 *
	 *	@return boolean 削除に成功した場合、true。
	 */
	public function delete()
	{
		$db = CDBManager::getInstance();
		$pdo = $db->getPDO();
		try
		{
			self::initialize();
			$pdo->beginTransaction();
			$result = $db->execute(CFileSQLComment::getInstance()->delete,
				$this->createDBParamsOnlyEID()) && parent::delete();
			if(!$result)
			{
				throw new Exception(_('DB書き込みに失敗'));
			}
			$pdo->commit();
		}
		catch(Exception $e)
		{
			error_log($e->toString());
			$pdo->rollback();
		}
		return $result;
	}

	/**
	 *	コミットします。
	 *
	 *	@return boolean 成功した場合、true。
	 */
	public function commit()
	{
		self::initialize();
		$db = CDBManager::getInstance();
		$pdo = $db->getPDO();
		$result = false;
		try
		{
			$pdo->beginTransaction();
			$result = $this->isExists();
			if(!$result)
			{
				$result = $db->execute(CFileSQLComment::getInstance()->insert,
					$this->createDBParamsOnlyEID() + $this->createDBParams());
			}
			$result = $result && $this->getEntity()->commit();
			if(!$result)
			{
				throw new Exception(_('DB書き込みに失敗'));
			}
			$pdo->commit();
		}
		catch(Exception $e)
		{
			error_log($e);
			$pdo->rollback();
		}
		return $result;
	}

	/**
	 *	ロールバックします。
	 *
	 *	@return boolean 成功した場合、true。
	 */
	public function rollback()
	{
		$body = CDBManager::getInstance()->execAndFetch(
			CFileSQLComment::getInstance()->select, $this->createDBParamsOnlyEID());
		$result = count($body) > 0;
		if($result)
		{
			$this->createEntity($body[0]['ENTITY_ID']);
			$this->setOwner($body[0]['OWNER']);
		}
		return $result;
	}

	/**
	 *	DB受渡し用のパラメータを生成します。
	 *
	 *	@return array DB受渡し用のパラメータ。
	 */
	private function createDBParams()
	{
		return array('owner' => array($this->getOwner()->getID(), PDO::PARAM_STR));
	}
}

?>